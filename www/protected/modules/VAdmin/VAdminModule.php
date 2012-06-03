<?php
class AdminModule extends CWebModule
{
	public $layout = 'main';

	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(	
			'admin.models.*',
			'admin.models.forms.*',
			'admin.components.*',
			'admin.controllers.*',
			'admin.views.*',
		));
	}
}