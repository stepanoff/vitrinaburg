<?php
abstract class VitrinaSectionItemBase extends ExtendedActiveRecord
{
    protected $_structure;

	public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return '';
    }
    
	public function scopes()
	{
        $res = parent::scopes();
        return array_merge($res, array(
		));
	}

    public function relations()
    {
        $res = parent::relations();
        return array_merge($res, array(
        ));
    }

    public function rules()
    {
        $res = parent::rules();
        return array_merge($res, array(
        	array('src', 'ImageValidator'),
        	array('parent_id, name, position, left, right, level', 'safe', 'on' => 'admin'),
		));
    }

    public function ImageValidator($attribute, $params) {
    }

    public function attributeLabels()
    {
        $res = parent::attributeLabels();
        return array_merge($res, array(
        	'parent_id' => 'Родитель',
        	'name' => 'Название',
        	'position' => 'Порядковый номер',
            'left' => 'id элемента слева',
            'right' => 'id элемента справа',
            'level' => 'Уровень глубины',
        ));
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
        return parent::afterFind();
    }

    protected function afterSave()
    {
    	return parent::afterSave();
    }
    
	protected function afterDelete()
	{
		return parent::afterDelete();
	}


    public function getStructure ($startLevel=false)
    {
        //if ($this->_structure === null)
        {
            $nodes = array();

            $criteria = new CDbCriteria(array(
                                    'order' => '`level` DESC, `parent_id`, `position`',
                            ));
            if ($startLevel)
            {
                $criteria->addCondition('level >= :level');
                $criteria->params = array(
                    ':level' => $startLevel
                );
            }

            $rows = Yii::app()->db->commandBuilder->createFindCommand($this->tableName(), $criteria)->queryAll();
            if (is_array($rows) && sizeof($rows)>0)
            {
                foreach ($rows as $row)
                {
                    $nodes[(int)$row['id']] = $row;
                    $nodes[(int)$row['id']]['children'] = array();
                }
                unset($rows);
                foreach ($nodes as $id=>$row)
                {
                    if (isset($nodes[(int)$row['parent_id']]))
                    {
                        $nodes[(int)$row['parent_id']]['children'][$id] = $nodes[$id];
                        unset($nodes[$id]);
                    }
                }
            }
            $this->_structure = $nodes;
        }
        return $this->_structure;
    }

}