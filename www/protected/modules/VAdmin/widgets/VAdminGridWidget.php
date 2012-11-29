<?php
class VAdminGridWidget extends CGridView {

    public $cssFile = false;
    public $rowCssClass = false;
    public $rowCssClassExpression = null;

    public $pager = array('class' => 'VAdminLinkPager');
    public $enableFilters = true;
    public $filters = array();
    public $template="{filters}\n{summary}\n{items}\n{pager}";

    public $route = '';

    public $filtersCssClass = 'filters';
    public $summaryCssClass = 'summary';
    public $itemsCssClass = 'table table-striped table-hover';
    public $pagerCssClass = 'pager';
    public $activePageCssClass = 'selected';

    protected $itemsCssSelector = false;
    protected $filtersCssSelector = false;
    protected $pagerCssSelector = false;
    protected $summaryCssSelector = false;

    protected $filterForm = '';
    private $_id;

    public function init() {
        if (!isset($this->pager['route'])) {
            $this->pager['route'] =  $this->route;
        }
        $this->pagerCssSelector = 'pager_'.$this->getId();
        $this->itemsCssSelector = 'items_'.$this->getId();
        $this->filtersCssSelector = 'filters_'.$this->getId();
        $this->summaryCssSelector = 'summary_'.$this->getId();

        $this->pagerCssClass .= ' '.$this->pagerCssSelector;
        $this->itemsCssClass .= ' '.$this->itemsCssSelector;
        $this->filtersCssClass .= ' '.$this->filtersCssSelector;
        $this->summaryCssClass .= ' '.$this->summaryCssSelector;

        parent::init();
    }

    public function getId () {
        if ($this->_id === null) {
            $this->_id = 'grid_'.rand(0, 1000000);
        }
        return $this->_id;
    }

    /**
     * Renders filters.
     */
    public function renderFilters()
    {
        if(!$this->enableFilters)
            return;

        $filters=array();
        $class='VAdminFilterBuilderWidget';
        if(is_string($this->filters))
            $class=$this->filters;
        else if(is_array($this->filters))
        {
            $filters=$this->filters;
            if(isset($filters['class']))
            {
                $class=$filters['class'];
                unset($filters['class']);
            }
        }

        if($filters['form'])
        {
            echo '<div class="'.$this->filtersCssClass.'">';
            $this->widget($class,$filters);
            echo '</div>';
        }
        else
            $this->widget($class,$filters);
    }

    public function renderParts () {
        ob_start();
        preg_match_all("/{(\w+)}/", $this->template, $matches);
        $res = array();
        if (isset($matches[1]))
        foreach ($matches[1] as $k) {
            $res[$k] = $this->renderSection(array(0, $k));
        }
        ob_end_flush();
        return $res;
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
            'filtersSelector' => '.'.$this->filtersCssSelector,
            'pagerSelector' => '.'.$this->pagerCssSelector,
            'summarySelector' => '.'.$this->summaryCssSelector,
            'tableSelector' => '.'.$this->itemsCssSelector,
            'filtersSubmitSelector' => '.btn-primary',
            'actionItemsSelectors' =>  array(".btn", ".js-link"),
            'activePageClass' => $this->activePageCssClass,
            'pageVar' => $this->dataProvider->getPagination()->pageVar,
            'actionTypeAttr' => 'actionType'
        );
        $options=CJavaScript::encode($options);

        $script = "
app.module.register( '".__CLASS__.'_'.$id."', vGrid, ".$options.");";
        $cs->registerScript(__CLASS__.'#'.$id, $script, CClientScript::POS_END);
    }

}
