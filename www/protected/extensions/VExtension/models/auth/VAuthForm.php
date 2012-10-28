<?php
class VAuthForm extends CFormModel
{
    public $login;
    
    public $password;
    
    public $rememberMe;
    
    public function rules()
    {
        return array(
        				array('rememberMe', 'numerical', 'integerOnly' => true ),
        				array('login', 'required', 'message'=>'Укажите логин' ),
        				array('password', 'required', 'message'=>'Укажите пароль' ),
        				array('login, password, rememberMe', 'safe'),
		);
    }

    public function attributeLabels()
    {
        return array(
                        'login' => 'Логин',
                        'password'  => 'Пароль',
        				'rememberMe'   => 'Запомнить меня',
                    );
    }
}
?>