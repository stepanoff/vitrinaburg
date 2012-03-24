<?php
class VitrinaUserSet extends ExtendedActiveRecord
{
    protected $userModel = 'User';

	public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return 'obj_widgetset';
    }
    
	public function scopes()
	{
        $res = parent::scopes();
        return array_merge($res, array(
		));
	}

    public function manyToManyRelations ()
    {
        $res = parent::manyToManyRelations();
        return array_merge($res, array(
        ));
    }

    public function relations()
    {
        $res = parent::relations();
        return array_merge($res, array(
            'user' => array(self::BELONGS_TO, $this->userModel, 'user_id'),
        ));
    }

    public function rules()
    {
        $res = parent::rules();
        return array_merge($res, array(
        	array('name', 'required', 'message' => 'Укажите название образа'),
            array('author', 'checkAuthor'),
        	array('name, announce, author, html, image, data, user_id', 'safe'),
		));
    }

    public function checkAuthor($attribute, $params) {
    }

    public function attributeLabels()
    {
        $res = parent::attributeLabels();
        return array_merge($res, array(
        	'name' => 'Название',
            'announce' => 'Подпись',
        	'author' => 'Автор',
        	'html' => 'html-код',
        	'image' => 'Изображение',
            'data' => 'Данные сета',
            'user_id' => 'Пользователь',
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
		$this->convertImages();
    	return parent::afterSave();
    }
    
	/**
	 * Конвертирует необходимые изображения
	 * @return void
	 */
	private function convertImages()
	{

	}
    
	protected function afterDelete()
	{
		parent::afterDelete();
	}

}