<?php
/**
 * Модель формы напоминания пароля
 * @author stepanoff stenlex@gmail.com
 * @version 1.0
 *
 */
class VitrinaForgetPasswordForm extends VFormModel
{
	public $email;

    public function rules()
    {
        return array(
            array('email', 'required', 'message' => 'Укажите адрес электронной почты' ),
            array('email', 'email', 'message' => 'Адрес электронной почты указан неверно. Пример: mymail@gmail.com' ),
            array('email', 'checkMail'),
		);
    }

    public function checkMail ($attribute, $params) {
        $user = VUser::model()->byEmail($this->$attribute)->find();
        if (!$user) {
            $this->addError($attribute, 'Указанный адрес не найден в базе');
        }
    }

    public function attributeLabels()
    {
        return array(
        	'email' => 'E-mail (указанный при регистрации)',
        );
    }

    public function getFormElements () {
        return array(
            'email' => array(
                'type' => 'text',
            ),
        );
    }

    public function getButtons () {
        return array(
            'submit' => array(
                'type' => 'submit',
                'label'=> 'Выслать пароль',
            ),
        );
    }

}
?>