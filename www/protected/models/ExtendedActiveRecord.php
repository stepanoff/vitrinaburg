<?php
class ExtendedActiveRecord extends CActiveRecord
{
	const STATUS_HIDDEN = 0;
	const STATUS_SIMPLE = 10;
	const STATUS_ON_MAIN = 20;
	const STATUS_ON_LIST = 30;
	const STATUS_ON_MAIN_ON_LIST = 40;
	
    protected $_statusBefore;

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

	public function scopes()
	{
		return array(
			'onSite' => array(
				'condition'=>'t.status > '.self::STATUS_HIDDEN,
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