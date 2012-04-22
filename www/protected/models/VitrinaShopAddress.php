<?php
class VitrinaShopAddress extends ExtendedActiveRecord
{
    protected $shopModel = 'VitrinaShop';
    protected $mallModel = 'VitrinaMall';

	public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return 'obj_shopaddress';
    }
    
	public function scopes()
	{
        $res = parent::scopes();
        return array_merge($res, array(
            'orderDefault' => array(
                'order' => 't.address ASC',
            ),
		));
	}

    public function onSite($alias = 't')
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition'=>$alias.'.visible = '.self::VISIBLE_ON,
        ));
        return $this;
    }

    public function addressOnSite()
    {
        return $this->onSite('address');
    }

    public function byMall($mallId, $alias='t')
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition'=>$alias.'.`mall` = :mallId',
            'params'=>array(':mallId' => $mallId),
        ));
        return $this;
    }


    public function relations()
    {
        $res = parent::relations();
        return array_merge($res, array(
            'shopObj' => array(self::BELONGS_TO, $this->shopModel, 'shop', 'joinType'=>'INNER JOIN'),
            'mallObj' => array(self::BELONGS_TO, $this->mallModel, 'mall', 'joinType'=>'INNER JOIN'),
        ));
    }

    public function rules()
    {
        $res = parent::rules();
        return array_merge($res, array(
        	array('address', 'required', 'message' => 'Укажите адрес'),
        	array('address, mall, shop, worktime, phone', 'safe', 'on' => 'admin'),
		));
    }

    public function attributeLabels()
    {
        $res = parent::attributeLabels();
        return array_merge($res, array(
        	'address' => 'Адрес',
            'mall' => 'Торговый центр',
            'shop' => 'Магазин',
            'worktime' => 'Время работы',
            'phone' => 'Телефон',
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
    	return parent::afterSave();
    }
    
	protected function afterDelete()
	{
		parent::afterDelete();
	}

}