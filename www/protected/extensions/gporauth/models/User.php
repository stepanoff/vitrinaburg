<?php
class User extends CActiveRecord {

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'users';
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

}
