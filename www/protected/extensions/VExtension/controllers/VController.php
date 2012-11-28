<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends VController
{
    public $pageDescription = '';
    public $seoText = '';
    public $mainPage = false;
    public $crumbs = array();

    public function beforeRender($view)
    {
        $seoPage = VitrinaSeoPage::model()->byUrl(Yii::app()->request->requestUri)->find();
        if ($seoPage)
        {
            $this->pageTitle = $seoPage->title;
            $this->pageDescription = $seoPage->description;
            $this->seoText = $seoPage->text;
        }
        return parent::beforeRender($view);
    }
}