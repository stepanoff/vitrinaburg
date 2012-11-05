<?php
class VUser extends CActiveRecord {

    const GENDER_MALE = 'M';
    const GENDER_FEMALE = 'F';

    const ROLE_USER = 1;
    const ROLE_CLIENT = 2;
    const ROLE_MODER = 3;
    const ROLE_ADMIN = 4;

    protected $_roles = null;

    protected $roleModel = 'VUserRole';

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'vusers';
    }

	protected function afterSave()
	{
		parent::afterSave();
	}

    public function rules()
    {
        return array(
			array('id, name, username, avatar, photo, gender, url, service, serviceId, created, email, updated', 'safe'),
		);
    }

    public function getRoles()
    {
        if ($this->_roles === null) {
            $this->_roles = array();
            $sql = '
    SELECT DISTINCT user_id, role_id
    FROM users_roles
    WHERE user_id = '.$this->id.'
';
            $roles = Yii::app()->db->commandBuilder->createSqlCommand($sql)->queryAll();
            if ($roles) {
                foreach ($roles as $role) {
                    $this->_roles[] = $role['role_id'];
                }
            }
        }
        return $this->_roles;
    }


    public function byService($service, $serviceId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition'=>'service=:service AND serviceId=:serviceId',
            'params'=>array(':service'=>$service, ':serviceId'=>$serviceId),
        ));
        return $this;
    }

    public function byLogin($login)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition'=>'login=:login',
            'params'=>array(':login'=>$login),
        ));
        return $this;
    }

    public function byEmail($email)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition'=>'email=:email',
            'params'=>array(':email'=>$email),
        ));
        return $this;
    }

    public function getAvatar($size = 'medium')
    {
        if ($this->avatar)
            return $this->avatar;
        return Yii::app()->user->getDefaultAvatar($size, $this->gender);
    }

    public function getLink ()
    {
        $originService = Yii::app()->vauth->originService;
        if ($this->service == $originService)
        {
            return CHtml::normalizeUrl (array(Yii::app()->vauth->getUserRoute(), 'id' => $this->id));
        }
        else
            return $this->url;
    }
}
