<?php
class VitrinaCollectionShopPhoto extends ExtendedActiveRecord
{
    protected $collectionModel = 'VitrinaShopCollection';

	public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return 'obj_shopcollectphoto';
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
			'collection' => array(self::BELONGS_TO, $this->collectionModel, 'shopcollect'),
        ));
    }

    public function rules()
    {
        $res = parent::rules();
        return array_merge($res, array(
            array('shopcollect', 'required', 'message' => 'Укажите коллекцию'),
        	array('src', 'ImageValidator'),
        	array('name, src, shopcollect, announce, cost, cost_old, useInWidget, order', 'safe', 'on' => 'admin'),
		));
    }

    public function ImageValidator($attribute, $params) {
    }

    public function attributeLabels()
    {
        $res = parent::attributeLabels();
        return array_merge($res, array(
        	'name' => 'Название товара',
            'announce' => 'Краткое описание',
        	'src' => 'Изображение',
            'cost' => 'Стоимость, руб.',
            'cost_old' => 'Старая стоимость, руб. (в случае скидки)',
            'useInWidget' => 'Использовать в виджете',
        	'shopcollect' => 'Коллекция',
        	'order' => 'Порядковый номер',
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
		return parent::afterDelete();
	}

}