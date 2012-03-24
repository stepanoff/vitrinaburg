<?php
class VitrinaArticle extends ExtendedActiveRecord
{
	protected $__visibleCollections = null; // отображаемые на сайте коллекции
    protected $__brandIds = null;

    protected $sectionArticleModel = 'VitrinaArticleSection';
    protected $sectionModel = 'VitrinaArticleSection';
    protected $tagModel = 'VitrinaTag';
    protected $brandTable = 'obj_shop_brand';

	public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return 'obj_article';
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
        	array('title', 'required', 'message' => 'Укажите заголовок'),
        	array('text', 'required', 'message' => 'Напишите что-нибудь'),
        	array('title, date, img, announce, source, source_link, text', 'safe', 'on' => 'admin'),
		));
    }

    public function manyToManyRelations ()
    {
        $res = parent::manyToManyRelations();
        return array_merge($res, array(
            'articleSections' => array($this->sectionArticleModel, 'obj_article_rubric', 'obj_id', 'prop_id'),
            'sections' => array($this->sectionModel, 'obj_article_tag1', 'obj_id', 'prop_id'),
            'tags' => array($this->tagModel, 'obj_article_tag2', 'obj_id', 'prop_id'),
        ));
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