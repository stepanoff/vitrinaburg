<?php
class SwitchEditWidget extends SwitchWidget
{
	public $controller;
	
	public $id;
	
	public $delimetr = ' ';
	
	public function run()
	{
		$this->list = array(
			array('name' => 'Правка', 'url' => array('edit', 'id' => $this->id)),
			array('name' => 'Удалить', 'url' => array('delete', 'id' => $this->id), 'ajax' => true),
		);		
		
		return parent::run();
	}
}