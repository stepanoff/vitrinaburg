<?php
class SwitchModerWidget extends SwitchWidget
{
	public $true;
	
	public $false;
	
	public $id;
	
	public $delimetr = ' / ';
	
	public $ajax = true;
	
	public $trueText = 'Да'; 
	
	public $falseText = 'Нет';
	
	public $trueAction = 'check';
	
	public $falseAction = 'uncheck';
	
	public function run()
	{
		$this->list = array(
			array('name' => $this->trueText, 'url' => array($this->trueAction, 'id' => $this->id), 'select' => $this->true),
			array('name' => $this->falseText, 'url' => array($this->falseAction, 'id' => $this->id), 'select' => $this->false),
		);		
		
		return parent::run();
	}
}