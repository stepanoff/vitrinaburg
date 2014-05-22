<?php
class VCbModule extends CWebModule
{
    public $viewsAlias = 'application.modules.VCb.views'; // путь до шаблонов форума (для кастомизации шаблонов)
    public $staticUrl = '/';
    public $baseRoute = '/VCb';
    public $defaultLayout = 'application.views.layouts.blank';

    protected $assetsPath = '';
    protected $assetsUrl = '';

    const ROLE_CB_EDITOR = 'cb_editor';

	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(	
			'VCb.models.*',
			'VCb.components.*',
			'VCb.controllers.*',
            'VCb.helpers.*',
		));

        $this->assetsPath = dirname(__FILE__).DIRECTORY_SEPARATOR.'assets';
        $this->assetsUrl = $this->staticUrl.Yii::app()->assetManager->publish($this->assetsPath, false, -1, YII_DEBUG);
	}

    public function getBaseRoute () {
        return $this->baseRoute;
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

    // todo: надо уметь цеплять bootstrap в любом котнроллере
    public function registerBootstrapAssets ()
    {
        $assetsPath = LIB_PATH . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'assets';
        $assetsUrl = $this->staticUrl.Yii::app()->assetManager->publish($assetsPath, false, -1, YII_DEBUG);
        $cs = Yii::app()->clientScript;
		$cs->registerCssFile($assetsUrl.'/css/bootstrap.css');
        $cs->registerCssFile($assetsUrl.'/css/bootstrap-responsive.css');
        $cs->registerCssFile($assetsUrl.'/css/docs.css');
        $cs->registerScriptFile($assetsUrl.'/js/bootstrap.min.js', CClientScript::POS_HEAD);
    }

}