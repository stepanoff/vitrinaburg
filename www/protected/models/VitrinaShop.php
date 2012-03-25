<?php
class VitrinaShop extends ExtendedActiveRecord
{
	protected $__visibleCollections = null; // отображаемые на сайте коллекции

    protected $collectionModel = 'VitrinaShopCollection';
    protected $addressModel = 'VitrinaShopAddress';
    protected $photoModel = 'VitrinaShopPhoto';
    protected $brandModel = 'VitrinaShopPhoto';
    protected $brandTable = 'obj_shop_brand';

	public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return 'obj_shop';
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
            'condition'=>$alias.'.visible = '.self::VISIBLE_ON.' AND '.$alias.'.status > '.self::STATUS_NEW,
        ));
        return $this;
    }

    public function shopOnSite()
    {
        return $this->onSite('shop');
    }

    public function manyToManyRelations ()
    {
        $res = parent::manyToManyRelations();
        return array_merge($res, array(
            'brands' => array($this->brandModel, $this->brandTable, 'obj_id', 'prop_id'),
        ));
    }

    public function relations()
    {
        $res = parent::relations();
        return array_merge($res, array(
			'photos' => array(self::HAS_MANY, $this->photoModel, 'shop', 'order' => 'p.order', 'alias' => 'p', 'index'=>'id'),
        	'collections' => array(self::HAS_MANY, $this->collectionModel, 'shop'),
        	'addresses' => array(self::HAS_MANY, $this->addressModel, 'shop'),
        ));
    }

    public function rules()
    {
        $res = parent::rules();
        return array_merge($res, array(
        	array('name', 'required', 'message' => 'Укажите название магазина'),
        	array('text', 'required', 'message' => 'Укажите наличие скидок, опишите качество товара и другие приемущества вашего магазина перед другими. Все то, что должно привлекать покупателя'),
            array('logo', 'ImageValidator'),
        	array('name, logo, site, text', 'safe', 'on' => 'admin'),
		));
    }

    public function ImageValidator($attribute, $params) {
    }

    public function attributeLabels()
    {
        $res = parent::attributeLabels();
        return array_merge($res, array(
        	'name' => 'Название',
        	'logo' => 'Логотип',
        	'site' => 'Сайт',
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
		if ($this->collections)
		{
			foreach ($this->collections as $item)
				$item->delete();
		}
		
        if ($this->photos)
        {
            foreach ($this->photos as $item)
                $item->delete();
        }

        if ($this->addresses)
        {
            foreach ($this->addresses as $item)
                $item->delete();
        }

		parent::afterDelete();
	}

	/*
	 * возвращает отображаемые на сайте очереди
	 */
	public function getVisibleCollections ()
	{
		if ($this->__visibleCollections === null)
		{
			$this->__visibleCollections = array();
			$modelName = $this->collectionModel;
			$this->__visibleCollections = VitrinaShopCollection::model()->onSite()->byObjectId($this->id)->orderDefault()->findAll();
		}
		return $this->__visibleCollections;
	}

}