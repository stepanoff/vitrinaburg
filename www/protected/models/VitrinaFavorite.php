<?php
class VitrinaFavorite extends ExtendedActiveRecord
{
	const TYPE_COLITEM = 10;


	public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return 'obj_favorite';
    }
    
	public static function type2Class()
    {
        return array(
        	self::TYPE_COLITEM => 'VitrinaShopCollectionPhoto'
        );
    }

	public function scopes()
	{
        $res = parent::scopes();
        return array_merge($res, array(
		));
	}

    public function orderDefault($alias = 't')
    {
        $this->getDbCriteria()->mergeWith(array(
            'order'=>$alias.'.date DESC',
        ));
        return $this;
    }

    public function byType($type)
    {
    	$alias = $this->getTableAlias();
        $this->getDbCriteria()->mergeWith(array(
            'condition'=>$alias.'.type = "'.$type.'"',
        ));
        return $this;
    }

    public function byTypeId($typeId)
    {
    	$alias = $this->getTableAlias();
        $this->getDbCriteria()->mergeWith(array(
            'condition'=>$alias.'.typeId = "'.$typeId.'"',
        ));
        return $this;
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
        	array('date', 'required', 'message' => 'Отсутствует дата добавления'),
        	array('type', 'required', 'message' => 'Отсутствует тип'),
        	array('typeId', 'required', 'message' => 'Отсутствует id типа'),
        	array('userId', 'required', 'message' => 'Отсутствует id пользователя'),
        	array('date, type, typeId, userId', 'safe'),
		));
    }

    public function attributeLabels()
    {
        $res = parent::rules();
        return array_merge($res, array(
        	'date' => 'Дата добавления',
            'type' => 'Тип избранного',
            'id' => 'id избранного',
            'userId' => 'id пользователя',
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
		parent::afterDelete();
	}

}