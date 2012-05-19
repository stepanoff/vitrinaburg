<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
    protected $data = array();
    public $pageDescription = '';
    public $seoText = '';
    public $mainPage = false;

    public function getData($key)
    {
        if (isset($this->data[$key]))
            return $this->data[$key];
        return false;
    }

	public function setData($key, $value)
    {
        $this->data[$key] = $value;
    }

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