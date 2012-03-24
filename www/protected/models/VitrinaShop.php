<?php
/**
 * Общий класс обънктов недвижимости
 *
 * @property string $name
 * @property boolean $completed
 * @property string $site
 * @property string $materials
 * @property integer $developerId
 * @property string $street
 * @property string $house
 * @property integer $cityId
 * @property integer $districtId
 * @property string $text
 * @property string $yandexmap_latitude
 * @property string $yandexmap_longitude
 * @property string $prices
 * @property integer $collectionId
 * @property integer $status
 *
 * @property City $city
 *
 * @property array _prices
 */
class RealtyObjectCommon extends CActiveRecord
{
	const STATUS_HIDDEN = 0;
	const STATUS_SIMPLE = 10;
	const STATUS_ON_MAIN = 20;
	const STATUS_ON_LIST = 30;
	const STATUS_ON_MAIN_ON_LIST = 40;
	
	const GROUP_ITEMS_ROOM = 10;
	const GROUP_ITEMS_FLOOR = 20;
	const GROUP_ITEMS_FLAT = 30;

    protected $_statusBefore;

	protected $__materials = null;
	protected $__files = null;
	protected $__items = null; // экземпляры объектов продажи
	protected $__announcesTotal = null; // кол-во реальных предложений о продаже по этому объекту
	protected $__prices = null;
	
	protected $_visibleStages = null; // отображаемые на сайте очереди
	
	public $itemModel = '';
	public $stageModel = '';
	public $activityModel = '';
	public $activityTypePrefix = '';
	
	public $controllerName = ''; // Название контроллера, который отвечает за отображение карточки объекта

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
        return '';
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

	public function byVisibleDeveloper()
	{
		//$this->with('developer');
		$developer = new RealtyDeveloper;
		$developer->onSite();
		$criteria = $developer->getDbCriteria()->toArray();
		$condition = str_replace('t.', 'developer.', $criteria['condition']);
		
		$this->getDbCriteria()->mergeWith(array(
			'condition'=> $condition,
			'with'=>array('developer'),
		));
		return $this;
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
	
	public function byDeveloperId($id)
	{
		$this->getDbCriteria()->mergeWith(array(
			'condition'=>'developerId=:id',
			'params'=>array(':id'=>$id),
		));
		return $this;
	}
	
    public function relations()
    {
        return array(
			'stages' => array(self::HAS_MANY, $this->stageModel, 'objectId', 'order' => 's.orderNum', 'alias' => 's', 'index'=>'id'),
			'stageCount' => array(self::STAT, $this->stageModel, 'objectId'),
        	'developer' => array(self::BELONGS_TO, 'RealtyDeveloper', 'developerId'),
        	'city' => array(self::BELONGS_TO, 'City', 'cityId'),
        	'activities' => array(self::HAS_MANY, $this->activityModel, 'objectId'),
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

	public function PricesValidator($attribute, $params) {
		if (!empty($this->_prices) && is_array($this->_prices)) {
			foreach($this->_prices as $row)
				if (empty($row['price']) || !is_numeric($row['price'])) {
					$this->addError($attribute, $params['message']);
					break;
				}
		}
	}
    
	public function getAddress() {
		$result = '';
		if ($this->city)
			$result.= $this->city->name. ', ';
		$result.= $this->street;
		if ($this->house)
			$result.= ', д. '.$this->house;
		return $result;
	}

    public function attributeLabels()
    {
        return array(
        	'name' => 'Название',
        	'completed' => 'Дом сдан',
        	'site' => 'Сайт объекта',
        	'materials' => 'Материал',
        	'_materials' => 'Материал',
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
	
	/*
	 * Возвращает файлы коллеции у объекта
	 * $hasAttribute - фильтр по атрибуту элемента коллекции. Например, $this->getFiles('inDocumentation') вернет все элементы документации.
	 */
	public function getFiles ($hasAttribute = false)
	{
		if ($this->__files === null)
		{
			$this->__files = array ();
			if ($this->collectionId)
			{
				$this->__files = RealtyCollectionItem::model()->byCollection($this->collectionId)->orderDefault()->findAll();
			}
		}
		
		if ($hasAttribute !== false)
		{
			$tmp = array();
			foreach ($this->__files as $file)
			{
				if ($file->$hasAttribute)
					$tmp[] = $file;
			}
			return $tmp;
		}
		return $this->__files;
	}
	
	public function getMainPhotoUrl($width = false, $height = false, $type = false)
	{
		$files = $this->getFiles('inGallery');
		if ($files)
		{
			$file = array_shift($files);
			return $file->getUrl($width, $height, $type);
		}
		return false;
	}
	
	/*
	 * записывает найденные объекты продаж и группирует их по необходимости
	 */
	public function setFoundedItemsById ($itemIds, $limit = 5, $group = false, $order_cost = false)
	{
		if ($this->__items === null)
		{
			$this->__items = array ();
			if (!$itemIds)
				return $this->__items;
			$needToFind = true;
			foreach ($itemIds as $item)
			{
				if (is_object($item))
				{
					$needToFind = false;
					break;
				}
			}
			
			if ($needToFind)
			{
				$modelName = $this->itemModel;
				$model = new $modelName;
				$model->getDbCriteria()->addInCondition('id', $itemIds);
				$this->__items = $model->findAll();
			}
			else
				$this->__items = $itemIds;
		}
		
		if ($group !== false)
		{
			$res = array ();
			$allItems = array();
			switch ($group)
			{
				case self::GROUP_ITEMS_ROOM:
					$tmp = array();
					$rooms = array();
					$limit = false;
					foreach ($this->__items as $item)
					{
						$bestAnnounce = $item->getBestAnnounce();
						$allItems[$item->rooms][] = $item;
						$rooms[$item->rooms] = $item->rooms;
						
						// нет предложения о продаже
						if (!$bestAnnounce)
							continue;
						
						// есть предложение о продаже
						if (!isset($tmp[$item->rooms]))
							$tmp[$item->rooms] = $item;
						else
						{
							if ($bestAnnounce->priceFull < $tmp[$item->rooms]->getBestAnnounce()->priceFull)
								$tmp[$item->rooms] = $item;
						}
					}
					
					$this->__items = array();
					ksort($rooms);
					foreach ($rooms as $room)
					{
						if (isset($tmp[$room]))
							$item = $tmp[$room];
						else
							$item = $allItems[$room][0];
							
						$this->__items[] = $item;
					}
					break;
					
				case self::GROUP_ITEMS_FLOOR:
					$tmp = array();
					$limit = false;
					$floors = array();
					foreach ($this->__items as $item)
					{
						$bestAnnounce = $item->getBestAnnounce();
						$allItems[$item->floors][] = $item;
						$floors[$item->floors] = $item->floors;
						
						if (!$bestAnnounce)
							continue;
						if (!isset($tmp[$item->floors]))
							$tmp[$item->floors] = $item;
						else
						{
							if ($bestAnnounce->priceFull < $tmp[$item->floors]->getBestAnnounce()->priceFull)
								$tmp[$item->floors] = $item;
						}
							
					}
					
					$this->__items = array();
					ksort($floors);
					foreach ($floors as $floor)
					{
						if (isset($tmp[$floor]))
							$item = $tmp[$floor];
						else
							$item = $allItems[$floor][0];
					
						//$item->square = RealtyHelper::getMinAttribute($allItems[$floor], 'square');
						$this->__items[] = $item;
					}
					
					break;
					
			}
		}
		
		// сортируем по стоимости
		if ($order_cost)
		{
			$tmp = array ();
			$i = 0;
			foreach ($this->__items as $item)
			{
				$bestAnnounce = $item->getBestAnnounce();
				if ($bestAnnounce)
				{
					$bestPrice = $item->getBestAnnounce()->priceFull;
					$tmp['a'.$bestPrice] = $item;
				}
				else
				{
					$tmp['b'.$i] = $item;
					$i++;
				}
			}
			$this->__items = array();
			ksort ($tmp);
			
			$i = 0;
			if ($tmp)
			{
				foreach ($tmp as $item)
				{
					$this->__items[] = $item;
					$i++;
					if ($i >= $limit && $limit)
						break;
				}
			}
		}
		return true;
	}
	
	public function getFoundedItems ()
	{
		return $this->__items;
	}
	
	public function resetFoundedItems ()
	{
		$this->__items = null;
		return true;
	}
	
	public function hasBestPriceInFoundedItems ()
	{
		if ($this->getFoundedItems() )
		{
			foreach ($this->getFoundedItems() as $item)
			{
				if ($item->getBestAnnounce())
					return true;
			}
		}
		return false;
	}
	
	
	/*
	 * TO_DO: доделать
	 */
	public function getLocation()
	{
		$res = '';
		if ($this->cityId)
			$res = $this->city->name;
		return $res;
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
		$userFilesManager = Yii::app()->getComponent('userFilesManager');

		if (isset($this->collectionId) && $this->collectionId)
		{
			// gallery images
			$items = $this->getFiles('inGallery');
			if ($items)
			{
				foreach ($items as $item)
				{
					if (!$item->fileUid)
						continue;
					$image = $userFilesManager->getFileByUid($item->fileUid, RealtyCollectionExtension::DEFAULT_STORAGE_CUSTOM_PATH);
					if ($image)
					{
						if (get_class($image) != 'ImageFile')
							continue;

		                $params = array(
		                    'priority' => ImageFile::RESIZE_HIGH_PRIORITY
		                );
						$image->getUrlResizedScaledBySide(ImageFile::RESIZE_HEIGHT_CODE, 40,  $params); // photo on developer list
						$image->getUrlResizedScaledBySide(ImageFile::RESIZE_WIDTH_CODE,  130, $params); // photo on developer card
						$image->getUrlResizedScaledBySide(ImageFile::RESIZE_HEIGHT_CODE, 80,  $params); // photo on top
						$image->getUrlResizedScaledBySide(ImageFile::RESIZE_HEIGHT_CODE, 55,  $params); // small in gallery
						$image->getUrlResizedScaledBySide(ImageFile::RESIZE_WIDTH_CODE,  135, $params); // photo in list
						$image->getUrlResized(75, 75, $params); // thumb in popup
						$image->getUrlResizedScaledBySide(ImageFile::RESIZE_WIDTH_CODE,  516, $params); // first in gallery
						$image->getUrlResizedScaledBySide(ImageFile::RESIZE_HEIGHT_CODE, 450, $params); // big in gallery
						$image->getUrlResizedScaledBySide(ImageFile::RESIZE_HEIGHT_CODE, 100, $params); // thumb on main
					}
				}
			}
			
			// progress images
			$items = $this->getFiles('inProgress');
			if ($items)
			{
				foreach ($items as $item)
				{
					if (!$item->fileUid)
						continue;
					$image = $userFilesManager->getFileByUid($item->fileUid, RealtyCollectionExtension::DEFAULT_STORAGE_CUSTOM_PATH);
					if ($image)
					{
						if (get_class($image) != 'ImageFile')
							continue;
		                $params = array(
		                    'priority' => ImageFile::RESIZE_HIGH_PRIORITY
		                );
						$image->getUrlResized(75, 75, $params); // thumb in popup
						$image->getUrlResizedScaledBySide(ImageFile::RESIZE_HEIGHT_CODE, 165, $params); // thumb in list
						$image->getUrlResizedScaledBySide(ImageFile::RESIZE_HEIGHT_CODE, 450, $params); // big in gallery
					}
				}
			}
		
		}
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
	
	/*
	 * Возвращает объект продажи с самым низким предложением о продаже
	 */
	public function getBestItem ()
	{
		$bestPrice = 0;
		$bestItem = false;
		if ($items = $this->getVisibleItems ())
		{
			foreach ($items as $item)
			{
				if (!$item->getBestAnnounce())
					continue;
				if ($bestItem === false)
				{
					$bestItem = $item;
					$bestPrice = $item->getBestAnnounce()->priceFull;
				}
				elseif ($bestPrice = $item->getBestAnnounce()->priceFull < $bestPrice)
					$bestItem = $item;
			}
		}
		return $bestItem;
	}
	
	/*
	 * лучшее предложение от застройщика
	 */
	public function getBestDeveloperPrice ()
	{
		$bestPrice = false;
		if ($this->_prices)
		{
			foreach ($this->_prices as $price)
			{
				if ($bestPrice === false)
					$bestPrice = $price;
				elseif ($price['price'] < $bestPrice)
					$bestPrice = $price;
			}
		}
		return $bestPrice;
	}
	
	/**
	 * Формирование массива этажностей всех очередей строительства объекта.
	 * Все значения массива уникальны и отсортированы в порядке возрастания этажности.
	 * @param bool $all Порядок учета флага "Показывать на сайте".<br>
	 *  Возможные значения:<br><ul><li>false - учет флага "Показывать на сайте";
	 *  <li>true - игнорирование флага "Показывать на сайте".</ul>
	 * @return array Массив этажностей всех очередй строительства, 
	 *	отсортированный по возрастанию и содержащий уникальные значения.
	 */
	public function get_floorsStages($all=false){
		//
		$floors = array();
		foreach($this->stages as $idx => $stage){
			if($all===false) if($stage->isOnSite()===false) continue;
			$floors = array_merge($floors, $stage->_floors);
		}
		asort($floors,SORT_NUMERIC);
		$floors = array_unique($floors);

		return $floors;
	}//get_floorsStages
	
	
	/*
	 * Подготавливает фото для галлереи "Ход строительства"
	 */
	public function prepareProgressImages($sizes = array('small'=>array(false, 165), 'big'=>array(false, 450), 'thumb'=>array(75,75)) )
	{
		$res = array();
		if ($files = $this->getFiles ('inProgress'))
		{
			foreach ($files as $file)
			{
				$stamp = strtotime($file->date);
				$m = date('m', $stamp);
				$y = date('Y', $stamp);
				$key = $y.'-'.$m;
				if (!isset($res[$key]))
					$res[$key] = array ('photos'=>array(), 'date'=>DateUtils::formatMonthYear($file->date));
				
				$tmp = array();
				foreach ($sizes as $k=>$v)
				{
					$tmp[$k] = $file->getUrl($v[0], $v[1]);
				}
				$res[$key]['photos'][] = $tmp;
			}
			krsort($res);	
		}
		return $res;
	}
	
	
	/*
	 * Подготавливает фото для галлереи
	 */
	public function prepareGalleryImages($sizes = array('small'=>array(false, 55), 'big'=>array(false, 450), 'first'=>array(516, false), 'thumb'=>array(75,75)) )
	{
		$res = array();
		if ($files = $this->getFiles ('inGallery'))
		{
			$i = 0;
			foreach ($files as $file)
			{
				$tmp = array();
				foreach ($sizes as $k=>$v)
				{
					if ($i!=0 && $k=='first')
						continue;
					$tmp[$k] = $file->getUrl($v[0], $v[1]);
				}
				$res[] = $tmp;
				$i++;
			}	
		}
		return $res;
	}

	/**
	 * Получает activity по данному объекту
	 * @param int $limit
	 * @return array
	 */
	public function getNews($limit = 10)
	{
		Yii::import('activity.models.*');

		$c = new CDbCriteria();
		$c->limit = $limit;
		$c->mergeWith (array(
			'condition' => 'objectId = '.$this->id
		));
		return CActiveRecord::model($this->activityModel)->onSite()->findAll($c);
	}
	
	
	/**
	 * Получает редакторские новости по данному объекту
	 * @param int $limit
	 * @return array
	 */
	public function getObjectNews($limit = 10, $offset = 0)
	{
		return array();
	}

	public function getFullTitleLink($absolute=false) {
		return RealtyHelper::objectLink($this, $this->name, null, $absolute);
	}

	public function getActivitiesCount() {
		$c = new CDbCriteria();
		$c->mergeWith (array(
			'condition' => 'objectId = '.$this->id
		));
		return CActiveRecord::model($this->activityModel)->onSite()->count($c);
	}
}