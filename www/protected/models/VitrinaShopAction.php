<?php
class VitrinaShopAction extends ExtendedActiveRecord
{
	protected $__visibleCollections = null; // отображаемые на сайте коллекции

    protected $shopModel = 'VitrinaShop';
    protected $sectionModel = 'VitrinaActionSection';

	public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return 'obj_action';
    }
    
	public function scopes()
	{
        $res = parent::scopes();
        return array_merge($res, array(
            'orderDefault' => array(
                'order' => 't.created DESC',
            ),
		));
	}

    public function onSite()
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition'=>'t.visible = '.self::VISIBLE_ON.' AND t.status > '.self::STATUS_NEW,
            'with' => array(
                'shop'=>array(
                    'scopes'=>array('shopOnSite'),
                    'alias' => 'shop',
                )
            )
        ));
        return $this;
    }

    public function byActual()
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition'=>'t.visible = '.self::VISIBLE_ON.' AND t.status > '.self::STATUS_NEW.' AND `date_end` >= "'.date('Y-m-d G:i:s').'"',
        ));
        return $this;
    }

    public function relations()
    {
        $res = parent::relations();
        return array_merge($res, array(
            'shop' => array(self::BELONGS_TO, $this->shopModel, 'shop', 'joinType'=>'INNER JOIN'),
        ));
    }

    public function rules()
    {
        $res = parent::rules();
        return array_merge($res, array(
        	array('title', 'required', 'message' => 'Укажите заголовок'),
        	array('text', 'required', 'message' => 'Напишите что-нибудь'),
            array('img', 'ImageValidator'),
        	array('title, date, date_end, img, announce, shop, text', 'safe', 'on' => 'admin'),
		));
    }

    public function ImageValidator($attribute, $params) {
    }

    public function manyToManyRelations ()
    {
        $res = parent::manyToManyRelations();
        return array_merge($res, array(
            'sections' => array($this->sectionModel, 'obj_action_action_rubrics', 'obj_id', 'prop_id'),
        ));
    }

    public function attributeLabels()
    {
        $res = parent::attributeLabels();
        return array_merge($res, array(
        	'title' => 'Заголовок',
            'date' => 'Дата начала мероприятия',
            'date_end' => 'Дата окончания мероприятия',
            'img' => 'Изображение',
            'announce' => 'Анонс',
            'shop' => 'Магазин',
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