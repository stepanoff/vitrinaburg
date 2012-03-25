<?php
class ExtendedActiveRecord extends CActiveRecord
{
    const VISIBLE_OFF = 0;
    const VISIBLE_ON = 1;

	const STATUS_DELETED = 0;
    const STATUS_BLOCKED = 10;
	const STATUS_MODER = 15;
    const STATUS_NEW = 20;
	const STATUS_UPDATED = 30;
	const STATUS_ACCEPT = 40;

    protected $_statusBefore;
    protected $_tmpStorage = array(); // массив для хранения временных данных вместо кеша

	public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public static function statusTypes ()
    {
    	return array (
            self::STATUS_DELETED => 'удален',
            self::STATUS_BLOCKED => 'отклонен',
			self::STATUS_NEW => 'новый',
			self::STATUS_MODER => 'на проверку модератору',
			self::STATUS_UPDATED => 'обновлен',
			self::STATUS_ACCEPT => 'допущен',
		);
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

    public function byLimit($limit)
    {
        $this->getDbCriteria()->mergeWith(array(
            'limit' => $limit,
        ));
        return $this;
    }

    public function manyToManyRelations ()
    {
        return array(

        );
    }

    public function relations ()
    {
        return array(

        );
    }

    public function rules()
    {
        return array(
		);
    }

    public function attributeLabels()
    {
        return array(
        );
    }
    
	public function setStatus ($status)
	{
		$statuses = self::statusTypes();
		if (!isset($statuses[$status]))
			return false;
        //Мне надо чтобы отработал эвент afterSave поэтому так
		$this->status = $status;
        $this->saveAttributes(array('status'));
		return true;
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
        $this->_statusBefore = $this->status;
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