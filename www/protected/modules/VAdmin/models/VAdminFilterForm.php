<?php
/**
 * Модель регитсрации магазина
 * @author stepanoff stenlex@gmail.com
 * @version 1.0
 *
 */
class RegisterShopForm extends VFormModel
{
	public $shopName;
	public $contactName;
	public $phone;
	public $email;
	public $captcha;

    public function rules()
    {
        return array(
        	array('shopName', 'required', 'message' => 'Укажите нахвание магазина' ),
            array('contactName', 'required', 'message' => 'Укажите имя контактного лица' ),
            array('phone', 'required', 'message' => 'Укажите контактный телефон' ),
            array('phone', 'VPhoneMaskValidator' ),
            array('email', 'required', 'message' => 'Укажите контактный адрес электронной почты' ),
            array('email', 'email', 'message' => 'Адрес электронной почты указан неверно. Пример: mymail@gmail.com' ),
		);
    }

    public function attributeLabels()
    {
        return array(
        	'shopName' => 'Название магазина',
        	'contactName' => 'Имя контактного лица',
			'phone' => 'Контактный телефон',
        	'email' => 'E-mail (для уведомлений)',
        	'captcha'	=> 'Код на картинке',
        );
    }

    public function getFormElements () {
        return array(
            'shopName' => array(
                'type' => 'text',
            ),
            'contactName' => array(
                'type' => 'text',
            ),
            'phone' => array(
                'type' => 'ext.VExtension.widgets.htmlextended.VHtmlPhoneWidget',
            ),
            'email' => array(
                'type' => 'text',
            ),
        );
    }

    public function getButtons () {
        return array(
            'submit' => array(
                'type' => 'submit',
                'label'=> 'Отправить',
            ),
        );
    }

}
?>