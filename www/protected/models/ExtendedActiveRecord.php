<?php
class ExtendedActiveRecord extends CActiveRecord
{
    const VISIBLE_OFF = 0;
    const VISIBLE_ON = 1;

	const STATUS_DELETED = 0;
    const STATUS_BLOCKED = 10;
	const STATUS_MODER = 15;
    const STATUS_NEW = 20;
	const STATUS_UPDATED = 30;
	const STATUS_ACCEPT = 40;

    protected $_statusBefore;
    protected $_tmpStorage = array(); // массив для хранения временных данных вместо кеша

	public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public static function statusTypes ()
    {
    	return array (
            self::STATUS_DELETED => 'удален',
            self::STATUS_BLOCKED => 'отклонен',
			self::STATUS_NEW => 'новый',
			self::STATUS_MODER => 'на проверку модератору',
			self::STATUS_UPDATED => 'обновлен',
			self::STATUS_ACCEPT => 'допущен',
		);
    }

	public function byStatus($status)
	{
		$status = is_array($status) ? $status : array($status);
		$this->getDbCriteria()->mergeWith(array(
			'condition'=>'t.status IN ('.implode(', ', $status).')',
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
    
	public function setStatus ($status)
	{
		$statuses = self::statusTypes();
		if (!isset($statuses[$status]))
			return false;
        //Мне надо чтобы отработал эвент afterSave поэтому так
		$this->status = $status;
        $this->saveAttributes(array('status'));
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
        $this->_statusBefore = $this->status;
        return parent::afterFind();
    }
    
    protected function afterSave()
    {
    	return parent::afterSave();
    }
    
	protected function afterDelete()
	{
		parent::afterDelete();
	}

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
     * возвращает кол-во моделей для каждой вариации scope и элемента из списка id
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