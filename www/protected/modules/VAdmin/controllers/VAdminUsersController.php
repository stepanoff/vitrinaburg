<?php
class VAdminUsersController extends VAdminController
{
	public $model = 'UserAdmin';
	
	public function templates()
	{
		return array(
			'list' => 'admin.views.adminusers.list',
		);
	}
	
	
	public function accessRules()
	{
		return array(
			array('allow',
				'roles'=>array('admin_master'),
			),

			array('deny',
				'users' => array('*'),
			),
		);
	}

	public function actionEdit()
	{
		if (isset($_GET['id']))
		{
			if (!$model = CActiveRecord::model($this->model)->findByPk($_GET['id']))
				$this->redirect(array('list'));
		}
		else
			$model = new $this->model;
				
		$model->setScenario('admin');
		$model->user->setScenario('admin');
		
	    $form = new CForm($this->editFormElements($model), $model);
	    $form['user']->model = $model->user;

	    if($form->submitted('save') && $form->validate())
	    {
	    	$model->save();
	    	$this->redirect(array('list'));
	    }
	    
		$this->render($this->__templates['edit'], array('form' => $form));
	}	
	
	public function actionRole()
	{
		if (isset($_GET['id']) && ($model = CActiveRecord::model($this->model)->findByPk($_GET['id'])) && isset($_REQUEST['value']))
		{
			$model->setScenario('admin');
			$model->setAttribute('_role', $_REQUEST['value']);
			if ($model->validate())
				$model->save();
		}
	}
	

	protected function editFormElements ($model)
	{
		return array(
			'enctype'=>'multipart/form-data',
		
		    'elements'=>array(
				'name'=>array(
		        	'type'=>'text',
				),
				
				'post'=>array(
		        	'type'=>'text',
				),
		
				'phone'=>array(
		        	'type'=>'text',
				),
		
				'mobile'=>array(
		        	'type'=>'text',
				),
		
				'icq'=>array(
		        	'type'=>'text',
				),
		
				'email'=>array(
		        	'type'=>'text',
				),
				
				'_role'=>array(
		        	'type'=>'dropdownlist',
					'items' => self::getAdminRoles (),
				),	

                /*
				'photo'=>array(
					'type'=>'application.extensions.htmlext.widgets.PhotoWidget',
				),
                */
		
		
				'user'=>array(
		            'type'=>'form',
		            'elements'=>array(
		                'username'=>array(
		                    'type'=>'text',
		                ),
		                'password'=>array(
		                    'type'=>'text',
		                ),
		            ),
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