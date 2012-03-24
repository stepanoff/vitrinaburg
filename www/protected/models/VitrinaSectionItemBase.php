<?php
abstract class VitrinaSectionItemBase extends ExtendedActiveRecord
{
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

}