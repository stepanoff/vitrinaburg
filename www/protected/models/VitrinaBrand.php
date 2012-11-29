<?php
class VitrinaBrand extends ExtendedActiveRecord
{
	public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return 'obj_brand';
    }
    
	public function scopes()
	{
        $res = parent::scopes();
        return array_merge($res, array(
		));
	}

    public function onSite($alias = 't')
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition'=>$alias.'.visible = '.self::VISIBLE_ON,
        ));
        return $this;
    }

    public function orderDefault($alias = 't')
    {
        $this->getDbCriteria()->mergeWith(array(
            'order'=>$alias.'.name ASC',
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
        	array('title', 'required', 'message' => 'Укажите заголовок'),
        	array('text', 'required', 'message' => 'Напишите что-нибудь'),
            array('img', 'ImageValidator'),
        	array('title, date, img, announce, source, source_link, text', 'safe', 'on' => 'admin'),
		));
    }

    public function ImageValidator($attribute, $params) {
    }

    public function attributeLabels()
    {
        $res = parent::rules();
        return array_merge($res, array(
        	'title' => 'Заголовок',
            'date' => 'Дата новости',
            'img' => 'Изображение',
            'announce' => 'Анонс',
            'source' => 'Источник',
            'source_link' => 'Ссылка на источник',
            'text' => 'Текст',
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