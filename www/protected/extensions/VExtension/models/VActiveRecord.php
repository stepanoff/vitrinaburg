<?php
class VActiveRecord extends CActiveRecord
{
    const VISIBLE_OFF = 0;
    const VISIBLE_ON = 1;

    protected $visibleColumn = 'visible';
    protected $statusColumn = 'status';

    protected $_statusBefore;
    protected $_tmpStorage = array(); // массив для хранения временных данных вместо кеша

    protected $manyToManyIds = array();

	public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public static function statusTypes ()
    {
    	return array (
		);
    }

    public function __get ($attr) {
        if (strstr($attr, 'Ids')) {
            $rels = $this->manyToManyRelations();
            $attrName = str_replace('Ids', '', $attr);
            if (isset($rels[$attrName])) {
                if (!isset($this->manyToManyIds[$attrName])) {
                    $this->manyToManyIds[$attrName] = $this->getRelatedIds($attrName);
                }
                return $this->manyToManyIds[$attrName];
            }
        }
        return parent::__get($attr);
    }

    public function __set ($attr, $value) {
        if (strstr($attr, 'Ids')) {
            $rels = $this->manyToManyRelations();
            $attrName = str_replace('Ids', '', $attr);
            if (isset($rels[$attrName])) {
                $vals = is_array($value) ? $value : array($value);
                $this->manyToManyIds[$attrName] = $vals;
                return true;
            }
        }
        return parent::__set($attr, $value);
    }

    public function __isset ($attr) {
        if (strstr($attr, 'Ids')) {
            $rels = $this->manyToManyRelations();
            $attrName = str_replace('Ids', '', $attr);
            if (isset($rels[$attrName])) {
                return true;
            }
        }
        return parent::__isset($attr);
    }

	public function byStatus($status)
	{
		$status = is_array($status) ? $status : array($status);
		$this->getDbCriteria()->mergeWith(array(
			'condition'=>'t.status IN ('.implode(', ', $status).')',
		));
		return $this;
	}
	
    public function byObjectId($attr, $attrId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition'=>'t.'.$attr.' = :'.$attr.'Id',
            'params'=>array(':'.$attr.'Id' => $attrId),
        ));
        return $this;
    }

	public function byNotInStatus($status)
	{
		$status = is_array($status) ? $status : array($status);
		$this->getDbCriteria()->mergeWith(array(
			'condition'=>'t.status NOT IN ('.implode(', ', $status).')',
		));
		return $this;
	}

    /*
     * скоп для поиска по связям MANY_MANY
     */
    public function byRelationIds($relation, $ids, $alias='t')
    {
        $ids = is_array($ids) ? $ids : array($ids);
        $relations = $this->manyToManyRelations();
        $relation = isset($relations[$relation]) ? $relations[$relation] : false;
        if (!$relation)
            return $this;
        $this->getDbCriteria()->mergeWith(array(
            'join' => 'INNER JOIN `'.$relation[1].'` `sections` ON `'.$alias.'`.`id` = `sections`.`'.$relation[2].'` AND `sections`.`'.$relation[3].'` IN ("'.implode('", "', $ids).'")',
        ));
        return $this;
    }

    public function byLimit($limit)
    {
        $this->getDbCriteria()->mergeWith(array(
            'limit' => $limit,
        ));
        return $this;
    }

    public function byOffset($offset)
    {
        $this->getDbCriteria()->mergeWith(array(
            'offset' => $offset,
        ));
        return $this;
    }

    public function manyToManyRelations ()
    {
        return array(

        );
    }

    public function relations ()
    {
        return array(

        );
    }

    public function rules()
    {
        return array(
		);
    }

    public function attributeLabels()
    {
        return array(
        );
    }
    
    public function changeStatus($status) {
        $className = get_class($this);
        $statuses = $className::statusTypes();
        if (isset($statuses[$status])) {
            $column = $this->statusColumn;
            $this->$column = $status;
            $this->saveAttributes(array($column));
            return $this->afterSetStatus();
        }
        return false;
    }

    protected function afterSetStatus () {
        return true;
    }

    public function visibleOn () {
        return $this->visibleOff(self::VISIBLE_ON);
    }

    public function visibleOff ($type = false) {
        $type = $type ? $type : self::VISIBLE_OFF;
        $column = $this->visibleColumn;
        $error = true;
        if ($type == self::VISIBLE_OFF) {
            $this->$column = self::VISIBLE_OFF;
            $error = false;
        }
        else if ($type == self::VISIBLE_ON) {
            $this->$column = self::VISIBLE_ON;
            $error = false;
        }
        if (!$error) {
            $this->saveAttributes(array($column));
            return $this->afterSetVisible($type);
        }
        return false;
    }
	
    protected function afterSetVisible ($type) {
        return true;
    }

    protected function beforeValidate()
    {
    	
        return parent::beforeValidate();
    }

	protected function beforeSave()
	{
		return parent::beforeSave();
    }

    protected function afterFind()
    {
        if (isset($this->status))
            $this->_statusBefore = $this->status;
        return parent::afterFind();
    }
    
    protected function afterSave()
    {
        if (count($this->manyToManyIds)) {
            $rels = $this->manyToManyRelations();
            foreach ($this->manyToManyIds as $attr => $ids) {
                $rel = $rels[$attr];

                $rel_table = $rel[1];
                $rel_key = $rel[2];
                $rel_foreign_key = $rel[3];

                $app = Yii::app();
                $criteria = new CDbCriteria();
                $criteria->addCondition($rel_key . ' = ' . $this->id);
                $app->db->getCommandBuilder()->createDeleteCommand($rel_table, $criteria)->execute();
                foreach($ids as $id)
                {
                    $data = array(
                               $rel_key => $this->id,
                               $rel_foreign_key => $id
                           );
                    $app->db->getCommandBuilder()->createInsertCommand($rel_table, $data)->execute();
                }

            }
        }

    	return parent::afterSave();
    }
    
	protected function afterDelete()
	{
		parent::afterDelete();
	}

    /*
     * список id из реляционной таблицы, связанных с этой моделью
     */
    public function getRelatedIds ($relationName)
    {
        $res = array();
        $relations = $this->manyToManyRelations();
        $relation = isset($relations[$relationName]) ? $relations[$relationName] : false;
        if (!$relation)
            return $res;
        $criteria = new CDbCriteria();
        $criteria->select = $relation[3].' as `id`';
        $criteria->addInCondition($relation[2], array($this->id));
        $relationRows = Yii::app()->db->commandBuilder->createFindCommand($relation[1], $criteria)->queryAll();
        foreach ($relationRows as $row)
        {
            $res[] = $row['id'];
        }
        return $res;
    }


    /*
     * список абсолютно всех id из реляционной таблицы
     */
    public function relationIds ($relationName)
    {
        $res = array();
        $relations = $this->manyToManyRelations();
        $relation = isset($relations[$relationName]) ? $relations[$relationName] : false;
        if (!$relation)
            return $res;
        $class = $relation[0];
        $model = new $class;
        $criteria = new CDbCriteria();
        $criteria->order = '`id` ASC';
        $relationRows = Yii::app()->db->commandBuilder->createFindCommand($model->tableName(), $criteria)->queryAll();
        foreach ($relationRows as $row)
        {
            $res[] = $row['id'];
        }
        return $res;
    }


    /*
     * возвращает кол-во моделей для каждой вариации scope и элемента из списка ids
     * каждый элемент из списка поочередно передается в указанный $scope
     */
    public function relationCountersByScope ($scope, $ids, $addScopes = false, $useCache = false)
    {
        return $this->relationCounters (false, $addScopes, $scope, $ids, $useCache);
    }

    /*
     * возвращает кол-во моделей для каждого элемента отношения MANY_MANY (см. manyToManyRelations)
     */
    public function relationCounters ($relationName, $scopes, $relationScope = false, $ids = false, $useCache = false)
    {
        $res = array();
        $relations = $this->manyToManyRelations();
        $relation = isset($relations[$relationName]) ? $relations[$relationName] : false;
        if (!$relation && (!$relationScope || !$ids) )
            return $res;

        $key = get_class($this).'_relationCounters_'.$relationScope.serialize($ids);
        if ($scopes)
        {
            foreach ($scopes as $k => $scope)
            {
                $key .= '_'.is_array($scope) ? $k.serialize($scope) : $scope;
            }
        }
        $key = md5($key);

        // todo: прикрутить кеш
        if (!$useCache)
        {
            if (!$ids && $relation)
                $ids = $this->relationIds($relationName);
            elseif  (!$ids)
                $ids = array();

            foreach ($ids as $id)
            {
                $modelName = get_class($this);
                $model = new $modelName;

                if ($scopes)
                {
                    foreach ($scopes as $k => $scope)
                    {
                        $scopeName = is_array($scope) ? $k : $scope;
                        $args = is_array($scope) ? $scope : array();

                        $model = call_user_func_array(array($model, $scopeName), $args);
                    }
                }
                if ($relationScope === false)
                    $model->byRelationIds($relationName, $id);
                else
                    $model = call_user_func_array(array($model, $relationScope), array($id));
                $res[$id] = $model->count();
            }

        }
        return $res;

    }

}