<?php
class VAdminGridWidget extends CGridView {

    public $cssFile = false;
    public $itemsCssClass = 'table table-striped table-hover';
    public $rowCssClass = false;
    public $rowCssClassExpression = null;

    public $pager = array('class' => 'VAdminLinkPager');

    public function init() {
        if (!isset($this->pager['route'])) {
            $controller = Yii::app()->controller;
            if ($controller && method_exists($controller, 'getRoute'))
                $this->pager['route'] =  $controller->getRoute();
        }
        parent::init();
    }


    public function registerClientScript()
    {
        $id=$this->getId();

        $cs = Yii::app()->clientScript;
        $url = Yii::app()->VExtension->getAssetsUrl();
        $cs->registerCoreScript('jquery');
        $cs->registerScriptFile($url.'/js/vapp.js', CClientScript::POS_HEAD);
        $cs->registerScriptFile($url.'/js/jquery.vgrid.js', CClientScript::POS_END);

        $options=array(
            'filtersSelector' => '.filters',
            'filtersSubmitSelector' => '.btn-primary',
            'tableSelector' => '.grid-view',
            'actionItemsSelectors' =>  array(".btn", ".js-link"),
            'actionTypeAttr' => 'actionType'
        );
        $options=CJavaScript::encode($options);

        $script = "
app.module.register( '".__CLASS__.'_'.$id."', vGrid, ".$options.");";
        $cs->registerScript(__CLASS__.'#'.$id, $script, CClientScript::POS_END);
    }

}
