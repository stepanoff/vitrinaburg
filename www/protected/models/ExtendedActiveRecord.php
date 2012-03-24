<?php
class VitrinaShop extends ExtendedActiveRecord
{
	const STATUS_HIDDEN = 0;
	const STATUS_SIMPLE = 10;
	const STATUS_ON_MAIN = 20;
	const STATUS_ON_LIST = 30;
	const STATUS_ON_MAIN_ON_LIST = 40;
	
    protected $_statusBefore;

	protected $__collections = null; // коллекции магазина
	protected $__visibleCollections = null; // отображаемые на сайте коллекции
	
	public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public static function statusTypes ()
    {
    	return array (
			self::STATUS_HIDDEN => 'скрыта',
			self::STATUS_SIMPLE => 'показывать',
			self::STATUS_ON_MAIN => 'показывать на главной',
			self::STATUS_ON_LIST => 'показывать над списком',
			self::STATUS_ON_MAIN_ON_LIST => 'показывать на главной и над списком',
		);
    }

    public function tableName()
    {
        return 'object_shop';
    }
    
	public function scopes()
	{
		return array(
			'onSite' => array(
				'condition'=>'t.status > '.self::STATUS_HIDDEN,
			),
			'orderByName' => array (
				'order' => 'name ASC',
			),
			'orderDefault' => array (
				'order' => 'name ASC',
			),
		);
	}

	public function isOnSite ()
	{
		return $this->status > self::STATUS_HIDDEN;
	}

	public function byStatus($status)
	{
		$status = is_array($status) ? $status : array($status);
		$this->getDbCriteria()->mergeWith(array(
			'condition'=>'t.status IN ('.implode(', ', $status).')',
		));
		return $this;
	}
	
	public function byNotInStatus($status)
	{
		$status = is_array($status) ? $status : array($status);
		$this->getDbCriteria()->mergeWith(array(
			'condition'=>'t.status NOT IN ('.implode(', ', $status).')',
		));
		return $this;
	}
	
    public function relations()
    {
        return array(
			'photos' => array(self::HAS_MANY, 'VitrinaShopPhoto', 'shop', 'order' => 'p.order', 'alias' => 'p', 'index'=>'id'),
        	'collections' => array(self::HAS_MANY, 'VitrinaShopCollection', 'shop'),
        	'addresses' => array(self::HAS_MANY, 'VitrinaShopCollection', 'objectId'),
        );
    }

    public function rules()
    {
        return array(
        	array('name', 'required', 'message' => 'Укажите название новостройки'),
        	array('developerId', 'required', 'message' => 'Укажите застройщика'),
			array('_prices', 'PricesValidator', 'message' => 'Одна из указанных цен не является правильным числом'),
        	array('name, completed, site, materials, _materials, developerId, yandexmap_latitude, yandexmap_longitude, yandexmap_zoom, cityId, districtId, text, collectionId, status, prices', 'safe', 'on' => 'admin'),
		);
    }

    public function attributeLabels()
    {
			'shopphoto'=>
				array(
					'name'=>'shopphoto',
					'descr'=>'Фото',
					'type'=>PROP_TYPE_OBJ,
					'cur_val'=>'shopphoto',
					'relation'=>PROP_REL_ONE2MANY,
					),
			'shopaddress'=>
				array(
					'name'=>'shopaddress',
					'descr'=>'Адреса',
					'type'=>PROP_TYPE_OBJ,
					'cur_val'=>'shopaddress',
					'relation'=>PROP_REL_ONE2MANY,
					),
			'shopcollect'=>
				array(
					'name'=>'shopcollect',
					'descr'=>'Коллекции',
					'type'=>PROP_TYPE_OBJ,
					'cur_val'=>'shopcollect',
					'relation'=>PROP_REL_ONE2MANY,
					),
        return array(
        	'name' => 'Название',
        	'logo' => 'Логотип',
        	'site' => 'Сайт',
        	'brand' => 'Бренды магазина',
        	'text' => 'Описание',
        	'developerId' => 'Застройщик',
        	'yandexmap_latitude' => 'Точка на карте',
     		'cityId'   => 'Город',
			'districtId'   => 'Район',
        	'text' => 'Описание',
        	'status' => 'Статус',
        	'prices' => 'Цены',
        	'_prices' => 'Цены',
        	'collectionId' => 'Коллекция файлов',
        );
    }
    
    public function get_materials()
    {
    	if ($this->__materials === null)
    	{
    		$this->__materials = array();
    		if ($this->materials)
    			$this->__materials = unserialize($this->materials);
    	}
    	return $this->__materials;
    }

    public function set_materials($value)
    {
    	$this->__materials = $value;
    }
    
    public function get_prices()
    {
    	if ($this->__prices === null)
    	{
    		$this->__prices = array();
    		if ($this->prices)
    			$this->__prices = unserialize($this->prices);
    	}
    	return $this->__prices;
    }

    public function set_prices($value)
    {
    	$this->__prices = $value;
    }
    
	public function setStatus ($status)
	{
		$statuses = self::statusTypes();
		if (!isset($statuses[$status]))
			return false;
        //Мне надо чтобы отработал эвент afterSave поэтому так
		$this->status = $status;
	    $this->save();
		return true;
	}
	
	/*
	 * Возвращает общее кол-во предложений о продаже по этому объекту. Опубликованных, допущенных
	 */
	public function getAnnouncesTotal ()
	{
		if ($this->__announcesTotal === null)
		{
			$this->__announcesTotal = 0;
			if ($this->stages)
			{
				foreach ($this->stages as $stage)
				{
					if (!$stage->isOnSite())
						continue;
					
					$this->__announcesTotal += $stage->getAnnouncesTotal();
				}
			}
		}
		return $this->__announcesTotal;
	}
	
    protected function beforeValidate()
    {
    	
        return parent::beforeValidate();
    }

	protected function beforeSave()
	{
		if ($this->_materials)
			$this->materials = serialize($this->_materials);
		else
			$this->materials = '';
		
		if ($this->_prices)
			$this->prices = serialize($this->_prices);
		else
			$this->prices = '';
		
		return parent::beforeSave();
    }

    protected function afterFind()
    {
        $this->_statusBefore = $this->status;
        return parent::afterFind();
    }
    
    protected function afterSave()
    {
        Yii::import('activity.models.*');

        if(($this->_statusBefore == self::STATUS_HIDDEN || $this->isNewRecord) && $this->isOnSite())
        {
        	if ($this->activityModel)
        	{
        		$modelName = $this->activityModel;
	            $activity = new $modelName;
	            $activity->objectId = $this->id;
	            $activity->activityType = $this->activityTypePrefix.'Add';
	            $activity->generateText ();
	            $activity->save();
        	}
        }
    	
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
		if ($this->stages)
		{
			foreach ($this->stages as $stage)
				$stage->delete();
		}
		
        if ($this->collectionId)
        {
        	$gallery = new RealtyCollectionExtension;
        	$gallery->delete($this->collectionId);
        }
		
		parent::afterDelete();
	}

	/*
	 * возвращает отображаемые на сайте очереди
	 */
	public function getVisibleStages ()
	{
		if ($this->_visibleStages === null)
		{
			$this->_visibleStages = array();
			$modelName = $this->stageModel;
			$this->_visibleStages = $modelName::model()->onSite()->byObjectId($this->id)->orderDefault()->findAll();
		}
		return $this->_visibleStages;
	}
	
	/*
	 * возвращает отображаемые на сайте объекты продаж
	 */
	public function getVisibleItems ()
	{
		$res = array();
		if ($this->getVisibleStages ())
		{
			foreach ($this->getVisibleStages () as $stage)
			{
				if ($items = $stage->getVisibleItems ())
					$res = array_merge($res, $items);
			}
		}
		return $res;
	}

}