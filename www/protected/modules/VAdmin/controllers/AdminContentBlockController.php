<?php
class AdminContentBlockController extends AdminController
{
	public $model = 'ContentBlock';
	
	public function templates()
	{
		return array(
			'list' => 'admin.views.contentblock.list',
		);
	}
	
	public function appendLayoutFilters($model)
	{
		//$model->orderDefault();
		return $model;
	}

	
	protected function editFormElements ($model)
	{
		return array(
			'enctype'=>'multipart/form-data',
		
		    'elements'=>array(
				'name'=>array(
		        	'type'=>'text',
				),
				
				'description'=>array(
		        	'type'=>'textarea',
				),
		
				'text'=>array(
					'type'=>'application.extensions.htmlext.widgets.CkEditorWidget',
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