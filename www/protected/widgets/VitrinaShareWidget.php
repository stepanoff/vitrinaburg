<?php
class VitrinaShareWidget extends CWidget {

    public $services = array('yaru', 'twitter', 'facebook', 'vkontakte');
    public $template = 'shareVertical';

    public function run() {
    	$controller = Yii::app()->controller;
    	if (!$controller) {
    		return '';
    	}

    	$defaults = array(
    		'link' => '',
    		'linkText' => '',
    		'annotation' => '',
    		'imageUrl' => '',
    	);
    	$shareOptions = $controller->getData('shareOptions');

    	$options = array();
    	foreach ($defaults as $key => $value) {
    		$options[$key] = isset($shareOptions[$key]) && !empty($shareOptions[$key]) ? Chtml::encode($shareOptions[$key]) : Chtml::encode($value);
    	}

		$this->render($this->template, array(
			'options' => $options,
			'services' => $this->services,
		));
    }

}
