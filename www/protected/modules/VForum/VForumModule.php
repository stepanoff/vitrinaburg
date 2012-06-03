<?php
class VForumModule extends CWebModule
{
    public $viewsAlias = 'application.modules.VForum.views'; // путь до шаблонов форума (для кастомизации шаблонов)
    public $adminRole = 'moderator'; // имя роли пользователя, который может удалять темы и комментарии к ним
    public $defaultLayout = 'application.views.layouts.main';

	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(	
			'VForum.models.*',
			'VForum.models.forms.*',
			'VForum.components.*',
			'VForum.controllers.*',
            'VForum.helpers.*',
			'VForum.views.*',
		));
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