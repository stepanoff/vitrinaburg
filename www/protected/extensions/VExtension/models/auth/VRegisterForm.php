<?php
class VRegisterForm extends CFormModel
{
    public $login;

    public $username;

    public $password;
    
    public $passwordAgain;

    public $rememberMe;
    
    public function rules()
    {
        return array(
        				array('rememberMe', 'numerical', 'integerOnly' => true ),
        				array('login', 'required', 'message'=>'Укажите вашу почту' ),
                        array('login', 'email', 'message'=>'Почта указана неверно' ),
                        array('login', 'checkUnique' ),
        				array('password', 'required', 'message'=>'Укажите пароль' ),
                        array('passwordAgain', 'required', 'message'=>'Укажите пароль еще раз' ),
                        array('passwordAgain', 'checkPassword'),
        				array('login, password, passwordAgain, username, rememberMe', 'safe'),
		);
    }

    public function checkUnique($attribute, $params) {
        if (!$this->$attribute)
            return;
        $user = VUser::model()->byLogin($this->$attribute)->find();
        if ($user)
        {
            $this->addError($attribute, 'Пользовательс такой почтой уже зарегистрирован');
        }
    }

    public function checkPassword($attribute, $params) {
        if ($this->password && $this->passwordAgain && $this->password != $this->passwordAgain)
        {
            $this->addError($attribute, 'Пароли не совпадают');
        }
    }


    public function attributeLabels()
    {
        return array(
                        'login' => 'Почта',
                        'password'  => 'Пароль',
                        'passwordAgain'  => 'Пароль еще раз',
                        'username'  => 'Отображаемое имя',
        				'rememberMe'   => 'Запомнить меня',
                    );
    }
}
?>