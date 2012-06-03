<?php
class VLjAuthForm extends CFormModel
{
    public $login;
    
    public function rules()
    {
        return array(
			array('login', 'required', 'message'=>'Укажите логин' ),
		);
    }

    public function attributeLabels()
    {
        return array(
            'login' => 'Логин',
        );
    }
}
?>