<?php
class CustomGoogleService extends GoogleOpenIDService {
	
	//protected $jsArguments = array('popup' => array('width' => 450, 'height' => 450));
	
	protected $requiredAttributes = array(
		'name' => array('firstname', 'namePerson/first'),
		'lastname' => array('lastname', 'namePerson/last'),
		'email' => array('email', 'contact/email'),
		'language' => array('language', 'pref/language'),
	);
	
	protected function fetchAttributes() {
		$this->attributes['fullname'] = $this->attributes['name'].' '.$this->attributes['lastname'];
	}
}