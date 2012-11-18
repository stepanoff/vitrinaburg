<?php
class VAdminContentBlockController extends VAdminController
{
	public $model = 'VitrinaCb';
	
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