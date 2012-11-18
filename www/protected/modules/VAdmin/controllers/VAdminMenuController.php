<?php
class VAdminMenuController extends VAdminController
{
	public $model = 'MenuItem';
	
	public function templates()
	{
		return array(
			'list' => 'VAdmin.views.adminmenu.list',
		);
	}
	
	public function appendLayoutFilters($model)
	{
		$model->byDefault();
		return $model;
	}

	public function actionSortUp()
	{
		if (isset($_GET['id']) && ($model = CActiveRecord::model($this->model)->findByPk($_GET['id'])) )
		{
			$model->sortUp();
		}
		$this->actionList();
	}
	
	public function actionSortDown()
	{
		if (isset($_GET['id']) && ($model = CActiveRecord::model($this->model)->findByPk($_GET['id'])) )
		{
			$model->sortDown();
		}
		$this->actionList();
	}
	
	public function actionAddOnSite()
	{
		if (isset($_GET['id']) && ($model = CActiveRecord::model($this->model)->findByPk($_GET['id'])) )
			$model->setOnSite(1);
		$this->actionList();
	}
	
	public function actionRemoveFromSite()
	{
		if (isset($_GET['id']) && ($model = CActiveRecord::model($this->model)->findByPk($_GET['id'])) )
			$model->setOnSite(0);
		$this->actionList();
	}
	
	public function actionAddInMenu()
	{
		if (isset($_GET['id']) && ($model = CActiveRecord::model($this->model)->findByPk($_GET['id'])) )
			$model->setInMenu(1);
		$this->actionList();
	}
	
	public function actionRemoveFromMenu()
	{
		if (isset($_GET['id']) && ($model = CActiveRecord::model($this->model)->findByPk($_GET['id'])) )
			$model->setInMenu(0);
		$this->actionList();
	}
	
	
	
	protected function editFormElements ($model)
	{
		$items = MenuItem::model()->byDefault()->findAll();
		$menuItems = array(0=>'Выбрать');
		foreach ($items as $item)
			$menuItems[$item->id] = $item->name;
		
		return array(
			'enctype'=>'multipart/form-data',
		
		    'elements'=>array(
				'name'=>array(
		        	'type'=>'text',
				),
				
				'link'=>array(
		        	'type'=>'text',
				),
		
				'reg'=>array(
		        	'type'=>'text',
				),
		
				'visible'=>array(
					'type'=>'checkbox',
				),
						
				'inMenu'=>array(
					'type'=>'checkbox',
				),
		
				'parentId'=>array(
		        	'type'=>'dropdownlist',
					'items' => $menuItems,
				),	
		    ),
		 
		    'buttons'=>array(
		        'save'=>array(
		            'type'=>'submit',
		            'label'=>'Сохранить',
		        ),
		    ),
		);

	}
}