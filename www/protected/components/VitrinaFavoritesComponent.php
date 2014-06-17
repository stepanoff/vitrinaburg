<?php
/**
 * Informer class file
 *
 * @author stepanoff
 * TO_DO:
 */

/**
 * VitrinaFavoritesComponent - компонент для избранного
 *
 */
class VitrinaFavoritesComponent extends CApplicationComponent
{
	public $userComponent = null;
	public $_userComponent = 'user';

	public $favoriteClass = 'VitrinaFavorite';
	
	protected $_favorites = null; // массив избранного пользователя

	/**
	 * Initializes the application component.
	 */
	public function init()
	{
		parent::init();
		$this->userComponent = Yii::app()->getComponent($this->_userComponent);
	}

	/**
	 * Является ли объект в избранном у пользователя
	 */
	public function isFavorite ($type, $typeId) {
		$favorites = $this->getFavorites();
		return isset($favorites[$type][$typeId]);
	}

	/**
	 * Переключить элемент в избранном
	 */
	public function toggleFavorite ($type, $typeId) {
		$res = null;
		$model = false;
		$userId = $this->userComponent->id;
		if ($userId) {
			$favorites = $this->getFavorites();
			if (isset($favorites[$type][$typeId])) {
				$modelName = $this->favoriteClass;
				$model = new $modelName;
				$model = $model->byType($type)->byTypeId($typeId)->find();
				if ($model && $model->userId == $userId) {
					if ($model->delete()) {
						unset($this->_favorites[$type][$typeId]);
						$res = 0;
					}
				}
			}
			if ($res === null) {
				$modelName = $this->favoriteClass;
				$model = new $modelName;
				$model->type = $type;
				$model->typeId = $typeId;
				$model->date = date('Y-m-d G:i', time());
				$model->userId = $userId;
				if ($model->save()) {
					$this->_favorites[$type][$typeId] = $model->attributes;
					$res = 1;
				}
			}
		}
		return $res;
	}

	/**
	 * Избранное пользователя в виде массива
	 */
	public function getFavorites () {
		$userId = $this->userComponent->id;
    	$res = array();

    	if ($this->_favorites !== null) {
    	}
    	else if ($userId) {
			$modelName = $this->favoriteClass;
			$model = new $modelName;
    		$this->_favorites = array();
	    	// TODO: плохо вот это: LIMIT 5000
	        $sql = 'SELECT * FROM `'.$model->tableName().'` where userId = "'.$userId.'" ORDER BY `date` DESC LIMIT 5000'; 
	        $items = Yii::app()->db->commandBuilder->createSqlCommand($sql)->queryAll();

	        foreach ($items as $row) {
	        	$this->_favorites[$row['type']][$row['typeId']] = $row;
	        }
    	}
    	else {
    		$this->_favorites = array();
    	}

        return $this->_favorites;
	}


}
?>