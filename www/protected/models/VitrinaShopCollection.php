<?php
class VitrinaShopCollection extends ExtendedActiveRecord
{
	protected $__visiblePhotos = null; // отображаемые на сайте коллекции

    protected $shopModel = 'VitrinaShop';
    protected $photoModel = 'VitrinaShopCollectionPhoto';
    protected $sectionModel = 'VitrinaSection';
    protected $sectionsTable = 'obj_shopcollect_rubric';

	public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return 'obj_shopcollect';
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
            'condition'=>$alias.'.visible = '.self::VISIBLE_ON.' AND '.$alias.'.status > '.self::STATUS_NEW.' AND '.$alias.'.actual > "'.DateUtils::toMysql(time()).'"',
            'with' => array(
                'shop'=>array(
                    'scopes'=>array('shopOnSite'),
                    'alias' => 'shop',
                ),
            ),
        ));
        return $this;
    }

    public function bySections($sectionIds, $alias = 't')
    {
        return $this->byRelationIds('sections', $sectionIds, $alias);
    }

    public function collectionOnSite()
    {
        return $this->onSite('collection');
    }

    public function relations()
    {
        $res = parent::relations();
        return array_merge($res, array(
			'photos' => array(self::HAS_MANY, $this->photoModel, 'shopcollect', 'order' => 'p.order', 'alias' => 'p', 'index'=>'id'),
            'shop' => array(self::BELONGS_TO, $this->shopModel, 'shop', 'joinType'=>'INNER JOIN'),
        ));
    }

    public function manyToManyRelations ()
    {
        $res = parent::manyToManyRelations();
        return array_merge($res, array(
            'sections' => array($this->sectionModel, $this->sectionsTable, 'obj_id', 'prop_id'),
        ));
    }

    public function rules()
    {
        $res = parent::rules();
        return array_merge($res, array(
        	array('name', 'required', 'message' => 'Укажите название коллекции'),
            array('shop', 'required', 'message' => 'Укажите магазин'),
        	array('name, actual, cost_from, cost_to, text', 'safe', 'on' => 'admin'),
		));
    }

    public function attributeLabels()
    {
        $res = parent::attributeLabels();
        return array_merge($res, array(
        	'name' => 'Название',
            'shop' => 'Магазин',
        	'actual' => 'Актуальность',
        	'cost_from' => 'Стоимость от, руб.',
        	'cost_to' => 'Стоимость до, руб.',
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
        // todo: сохранить связки с рубриками
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
        if ($this->photos)
        {
            foreach ($this->photos as $item)
                $item->delete();
        }

        // todo: удалить связки с рубриками

		parent::afterDelete();
	}

	/*
	 * возвращает отображаемые на сайте очереди
	 */
	public function getVisiblePhotos ()
	{
		if ($this->__visiblePhotos === null)
		{
			$this->__visiblePhotos = array();
			$this->__visiblePhotos = VitrinaShopCollectionPhoto::model()->onSite()->byObjectId($this->id)->orderDefault()->findAll();
		}
		return $this->__visiblePhotos;
	}

}