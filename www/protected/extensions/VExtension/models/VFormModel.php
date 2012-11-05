<?php
/**
 * Модель регитсрации маазина
 * @author stepanoff stenlex@gmail.com
 * @version 1.0
 *
 */
class RegisterShopForm extends CFormModel
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
            array('email', 'required', 'message' => 'Укажите контактный адрес электронной почты' ),
            array('email', 'email', 'message' => 'Адрес электронной почты указан неверно. Пример: mymail@gmail.com' ),
		);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), array(
        	'shopName' => 'Название магазина',
        	'contactName' => 'Имя контактного лица',
			'phone' => 'Контактный телефон',
        	'email' => 'E-mail (для уведомлений)',
        	'captcha'	=> 'Код на картинке',
        ));
    }

}
?>