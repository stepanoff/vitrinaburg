<?php
class TemporaryToken extends CActiveRecord {

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'temporarytokens';
    }

	protected function afterSave()
	{
		return parent::afterSave();
	}

    protected function beforeSave()
    {
        if ($this->isNewRecord)
            $this->created = date('Y-m-d G:i:s');
        return parent::beforeSave();
    }

    public function rules()
    {
        return array(
			array('id, token, userId, created', 'safe'),
		);
    }

    public function byToken($token)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition'=>'token=:token',
            'params'=>array(':token'=>$token),
        ));
        return $this;
    }

}
