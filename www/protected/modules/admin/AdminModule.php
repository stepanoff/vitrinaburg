<?php
class VAdminModule extends CWebModule
{
    public $viewsAlias = 'application.modules.VAdmin.views'; // путь до шаблонов форума (для кастомизации шаблонов)
    //public $adminRole = 'moderator'; // имя роли пользователя, который может удалять темы и комментарии к ним
    public $defaultLayout = 'application.modules.VAdmin.views.layouts.base';

    public $staticUrl = '/';
    protected $assetsPath = '';
    protected $assetsUrl = '';

	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(	
			'VAdmin.models.*',
			'VAdmin.models.forms.*',
			'VAdmin.components.*',
			'VAdmin.controllers.*',
            'VAdmin.widgets.*',
			'VAdmin.views.*',

            'zii.widgets.grid.*',
		));

        $this->assetsPath = LIB_PATH . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'assets';
        $this->assetsUrl = $this->staticUrl.Yii::app()->assetManager->publish($this->assetsPath, false, -1, YII_DEBUG);
	}

    public function getAssetsPath () {
        return $this->assetsPath;
    }

    public function getAssetsUrl () {
        return $this->assetsUrl;
    }

    public function getViewsAlias($viewName)
    {
        return $this->viewsAlias.'.'.$viewName;
    }

    public function getLayout ()
    {
        return $this->defaultLayout;
    }
}