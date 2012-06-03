<?php
/**
 * $Id: $
 *
 * @author kivi
 * @since  31.03.2010
 */
class UserAdmin extends CActiveRecord
{
	public $_role;
	
	public $_photo;
	public $_photo_delete;
	
	private $__isNewRecord;
	
	public function init()
	{
		if ($this->isNewRecord)
    	{
    		$this->user = new User();
    	}
	}
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'user_admin';
	}

	public function relations()
	{
        return array(
			'user'	=>	array(self::BELONGS_TO, 'User', 'user_id'),
		);
	}
	
    public function rules()
    {
        return array(
        	array('_role', 'in', 'range' => array_keys( Yii::app()->authManager->getRoles() ), 'on' => 'admin'),
        	array('name', 'required', 'on' => 'admin'),
			array('name, post, phone,mobile, email, _photo, icq', 'safe', 'on' => 'admin'),
		);
    }
	
    public function attributeLabels()
    {
        return array(
        	'name' => 'Имя',
        	'post' => 'Должность',
        	'phone' => 'Телефон',
        	'mobile' => 'Сотовый',
        	'icq' => 'ICQ',
        	'email' => 'E-mail',
        	'_photo' => 'Фото',
        	'_photo_delete' => 'Удалить фото',
        	'_role'   => 'Роль',
		);
    }	
    
	protected function beforeSave()
	{
		$this->user->save();
		
		if ($this->isNewRecord)
		{
			$this->user_id = $this->user->id;
			$this->__isNewRecord = true;
		}
		
		if ($this->_photo_delete)
		{
			$userFilesManager = Yii::app()->getComponent('userFilesManager');
			$userFilesManager->deleteFileByUid($this->photo);
			$this->photo = '';
		}

		if ($this->_photo || $this->_photo = CUploadedFile::getInstance($this, '_photo'))
		{
			$userFilesManager = Yii::app()->getComponent('userFilesManager');
			$this->photo = $userFilesManager->publishFile($this->_photo->getTempName(), $this->_photo->getExtensionName())->getUID();
		}
		
		return parent::beforeSave();
    }

    protected function afterSave()
    {
		// Присваиваем роль
		if ($this->getScenario() == 'admin')
			$this->setRole($this->_role);

		return parent::afterSave();
    }    
    
    protected function afterFind()
    {
    	// Получаем роли пользователя
    	$this->_role = Yii::app()->authManager->getRoles($this->user_id);
    	$this->_role = array_pop(array_keys($this->_role));

    	return parent::afterFind();
    }
    
    protected function beforeDelete()
    {
    	$this->user->delete();
    	
		if ($this->photo)
		{
			$userFilesManager = Yii::app()->getComponent('userFilesManager');
			$userFilesManager->deleteFileByUid($this->photo);
			$this->photo = '';
		}
    	    	
    	return parent::beforeDelete();
    }    
    
    // Присвоить роль
    public function setRole($role)
    {
		foreach (Yii::app()->authManager->getRoles($this->user_id) as $_k => $_v)
			Yii::app()->authManager->revoke($_k, $this->user_id);

		Yii::app()->authManager->assign($role, $this->user_id);
    }
}