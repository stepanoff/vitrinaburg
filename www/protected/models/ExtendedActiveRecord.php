<?php
class ExtendedActiveRecord extends VActiveRecord
{
	const STATUS_DELETED = 0;
    const STATUS_BLOCKED = 10;
	const STATUS_MODER = 15;
    const STATUS_NEW = 20;
	const STATUS_UPDATED = 30;
	const STATUS_ACCEPT = 40;

	public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function rules()
    {
        $res = parent::rules();
        return array_merge($res, array(
        	array('status, visible', 'safe', 'on' => 'admin'),
		));
    }

    public function isVisible () {
        return $this->visible == self::VISIBLE_ON;
    }

    /*
     * Работа со статусами
     */
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

}