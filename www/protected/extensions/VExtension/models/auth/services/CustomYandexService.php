<?php
class CustomYandexService extends YandexOpenIDService {

	protected $jsArguments = array('popup' => array('width' => 900, 'height' => 620));
	
	protected $requiredAttributes = array(
		'name' => array('fullname', 'namePerson'),
		'username' => array('nickname', 'namePerson/friendly'),
		'email' => array('email', 'contact/email'),
		'gender' => array('gender', 'person/gender'),
		'birthDate' => array('dob', 'birthDate'),
	);
	
	protected function fetchAttributes() {
//		if (isset($this->attributes['username']) && !empty($this->attributes['username']))
//			$this->attributes['url'] = 'http://openid.yandex.ru/'.$this->attributes['username'];
			
//		if (isset($this->attributes['birthDate']) && !empty($this->attributes['birthDate']))
//			$this->attributes['birthDate'] = strtotime($this->attributes['birthDate']);
	}

}