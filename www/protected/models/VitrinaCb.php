<?php
class VitrinaCb extends CActiveRecord
{
	public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return 'obj_cb';
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

    public function byName($name)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition'=>'t.name = :name',
            'params' => array(':name' => $name),
        ));
        return $this;
    }

    public function rules()
    {
        $res = parent::rules();
        return array_merge($res, array(
        	array('path, title, description, text', 'safe', 'on' => 'admin'),
		));
    }

    public function ImageValidator($attribute, $params) {
    }

    public function attributeLabels()
    {
        $res = parent::attributeLabels();
        return array_merge($res, array(
        	'path' => 'Урл',
        	'title' => 'Мкта-тег title',
        	'description' => 'Мета-тег description',
        	'text' => 'Сео-текст',
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

}