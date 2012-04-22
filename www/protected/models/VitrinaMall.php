<?php
class VitrinaMall extends ExtendedActiveRecord
{
	public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return 'obj_mall';
    }
    
	public function scopes()
	{
        $res = parent::scopes();
        return array_merge($res, array(
            'orderDefault' => array(
                'order' => 't.name ASC',
            ),
		));
	}

    public function onSite()
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition'=>'t.visible = '.self::VISIBLE_ON,
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
            array('name', 'required', 'message' => 'Укажите название'),
        	array('address', 'required', 'message' => 'Укажите адрес'),
        	array('name, logo, address, text, worktime', 'safe', 'on' => 'admin'),
		));
    }

    public function attributeLabels()
    {
        $res = parent::attributeLabels();
        return array_merge($res, array(
        	'address' => 'Адрес',
            'name' => 'Название',
            'logo' => 'Изображение',
            'worktime' => 'Время работы',
            'text' => 'Описание',
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