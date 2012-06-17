<?php
class VUser extends CActiveRecord {

    const GENDER_MALE = 'M';
    const GENDER_FEMALE = 'F';

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
