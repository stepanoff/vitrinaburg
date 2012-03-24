<?php
class VitrinaShop extends ExtendedActiveRecord
{
	protected $__visibleCollections = null; // отображаемые на сайте коллекции
    protected $__brandIds = null;

    protected $collectionModel = 'VitrinaShopCollection';
    protected $addressModel = 'VitrinaShopAddress';
    protected $photoModel = 'VitrinaShopPhoto';
    protected $brandTable = 'obj_shop_brand';

	public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return 'object_shop';
    }
    
	public function scopes()
	{
        $res = parent::scopes();
        return array_merge($res, array(
		));
	}

    public function get_brandIds()
    {
    	return $this->__brandIds;
    }

    public function set_brandIds($value)
    {
    	$this->__brandIds = $value;
    }

    public function relations()
    {
        $res = parent::relations();
        return array_merge($res, array(
			'photos' => array(self::HAS_MANY, $this->photoModel, 'shop', 'order' => 'p.order', 'alias' => 'p', 'index'=>'id'),
        	'collections' => array(self::HAS_MANY, $this->collectionModel, 'shop'),
        	'addresses' => array(self::HAS_MANY, $this->addressModel, 'objectId'),
        ));
    }

    public function rules()
    {
        $res = parent::rules();
        return array_merge($res, array(
        	array('name', 'required', 'message' => 'Укажите название магазина'),
        	array('developerId', 'required', 'message' => 'Укажите застройщика'),
			array('_prices', 'PricesValidator', 'message' => 'Одна из указанных цен не является правильным числом'),
        	array('name, completed, site, materials, _materials, developerId, yandexmap_latitude, yandexmap_longitude, yandexmap_zoom, cityId, districtId, text, collectionId, status, prices', 'safe', 'on' => 'admin'),
		));
    }

    public function attributeLabels()
    {
        $res = parent::rules();
        return array_merge($res, array(
        	'name' => 'Название',
        	'logo' => 'Логотип',
        	'site' => 'Сайт',
        	'brand' => 'Бренды магазина',
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
			$this->__visibleCollections = $modelName::model()->onSite()->byObjectId($this->id)->orderDefault()->findAll();
		}
		return $this->__visibleCollections;
	}

}