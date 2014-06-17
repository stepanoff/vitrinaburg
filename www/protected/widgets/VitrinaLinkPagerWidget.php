<?php
class VitrinaLinkPagerWidget extends CLinkPager {

    /**
     * @var array HTML attributes for the pages container tag.
     */
    public $htmlPagesOptions;

    /**
     * @var string Кастомный шаблон
     */
    public $customTemplate = false;

    /**
     * @var mixed JS file used for the widget.
     */
    public $jsFile;

    public function init()
    {
        $this->prevPageLabel = 'Предыдущая';
        $this->nextPageLabel = 'Следующая';
        $this->firstPageLabel = 'Первая';
        $this->lastPageLabel = 'Последняя';

        $this->header = 'Страницы';
        $this->cssFile = false;
        $this->jsFile = false;

        $this->htmlPagesOptions = array(
            "class" => ""
        );
        $this->htmlOptions['class'] = '';

        parent::init();
    }

    /**
     * Executes the widget.
     * This overrides the parent implementation by displaying the generated page buttons.
     */
    public function run()
    {
        $pages = $this->createInternalPages();
        $buttons = $this->createPageButtons();
        if (empty($buttons))
            return;
        $this->registerClientScript();

        echo '<div class="pn">';
        foreach ($buttons as $button) {
        	echo $button;
        }
        echo '</div>';

        echo '<div class="num">';
        foreach ($pages as $page) {
        	echo $page;
        }
        echo '</div>';

        /*
        echo '<span class="yiiSitePagerHeader">' . $this->header . '</span>';
        echo CHtml::tag('ul', $this->htmlOptions, implode("\n", $buttons));
        echo CHtml::tag('ul', $this->htmlPagesOptions, implode("\n", $pages));
        echo $this->footer;
        */

    }

    /**
     * Registers the needed client scripts.
     */
    public function registerClientScript()
    {
        //parent::registerClientScript();
        //Yii::app()->getClientScript()->registerScriptFile($this->jsFile, CClientScript::POS_END);
    }

    /**
     * Creates the page buttons.
     * @return array a list of page buttons (in HTML code).
     */
    protected function createPageButtons()
    {
        $pageCount = $this->getPageCount();
        if ($pageCount <= 1)
            return array();

        $currentPage = $this->getCurrentPage(false); // currentPage is calculated in getPageRange()
        $lastPage = $this->getPageCount();

        $buttons = array();

//      // first page
//      $buttons[]=$this->createPageButton($this->firstPageLabel,0,self::CSS_FIRST_PAGE,$currentPage<=0,false);

        // prev page
        $addClass = ' end';
        if ($currentPage != 0) {
        	$addClass = '';
        }
		$page = $currentPage - 1;
		if ($page < 0)
			$page = 0;

		$buttons[] = CHtml::link('← Ctrl <span>'. $this->prevPageLabel .'</span>', $this->createPageUrl($page), array('class' => 'prev' . $addClass) );

        // next page
        $page = $currentPage + 1;
        if ($page >= $pageCount - 1)
            $page = $pageCount - 1;

        $addClass = ' end';
        if ($lastPage - 1 != $currentPage) {
        	$addClass = '';
        }
        $buttons[] = CHtml::link('<span>'. $this->nextPageLabel .'</span> Ctrl →', $this->createPageUrl($page), array('class' => 'next' . $addClass) );

//      // last page
//      $buttons[]=$this->createPageButton($this->lastPageLabel,$pageCount-1,self::CSS_LAST_PAGE,$currentPage>=$pageCount-1,false);

        return $buttons;
    }

    /**
     * Create internal pages.
     * @return array a list of page buttons (in HTML code).
     */
    protected function createInternalPages()
    {
        $buttons = array();

        list($beginPage, $endPage) = $this->getPageRange();
        $currentPage = $this->getCurrentPage(false); // currentPage is calculated in getPageRange()
        $lastPage = $this->getPageCount();

        for ($i = $beginPage, $j = 0; $i <= $endPage; ++$i, $j++) {
            $label = $i + 1;
            if ($this->maxButtonCount == $j + 1 && $lastPage != ($i + 1)) {
                $buttons[] = '<span>…</span>';
	            $buttons[] = CHtml::link($lastPage, $this->createPageUrl($lastPage-1), array('class' => $addClass) );
            }
            else if ($j == 0 && $i != 0) {
	            $buttons[] = CHtml::link('1', $this->createPageUrl(0), array('class' => $addClass) );
                $buttons[] = '<span>…</span>';
            }

            else {
	            $addClass = '';
	            if ($i == $currentPage) {
	            	$addClass = ' cur';
	            }
	            $buttons[] = CHtml::link($label, $this->createPageUrl($i), array('class' => $addClass) );
            }
        }

        return $buttons;
    }


}
