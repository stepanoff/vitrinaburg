<?php
class ContentpageController extends AdminController
{
	public $model = 'ContentPage';
	
	public function templates()
	{
		return array(
			'list' => 'admin.views.contentpage.list',
		);
	}
	
	public function layoutsFilter()
	{
		return array(
    		'text' => array('type' => 'text', 'label' => 'Поиск'),
		);
	}	
	
	public function appendLayoutFilters($model)
	{
		if (isset($this->_layoutFilters['text']) && $this->_layoutFilters['text'])
		{
			$model->getDbCriteria()->addSearchCondition('path', $this->_layoutFilters['text'], true, 'OR');
			$model->getDbCriteria()->addSearchCondition('header', $this->_layoutFilters['text'], true, 'OR');
		}
			
		return $model;
	}		
	
	protected function editFormElements ($model)
	{
		return array (
			'elements' => array (
				'link'=>array(
					'type'=>'text',
				),
				'header'=>array(
					'type'=>'text',
				),
				'title'=>array(
					'type'=>'text',
				),
				'text'=>array(
					'type'=>'application.extensions.htmlext.widgets.CkEditorWidget',
				),
				'visible'=>array(
					'type'=>'checkbox',
				),
			),
			
			'buttons' => array (
				'submit' => array(
		            'type'=>'submit',
		            'label'=>'сохранить',
		 			'class'=>'submit',
	        	)
			)
		);
	}	
}