<?php
class VAdminShowHideWidget extends CGridColumn {

    const BUTTON_SHOW = 'show';
    const BUTTON_HIDE = 'hide';

    /**
     * @var array the HTML options for the data cell tags.
     */
    public $htmlOptions=array('class'=>'button-column');
    /**
     * @var array the HTML options for the header cell tag.
     */
    public $headerHtmlOptions=array('class'=>'button-column', 'width' => '1%');
    /**
     * @var array the HTML options for the footer cell tag.
     */
    public $footerHtmlOptions=array('class'=>'button-column');

    public $visibleCondition = false;
    public $itemVisible = null;

    /**
     * @var string a PHP expression that is evaluated for every view button and whose result is used
     * as the URL for the view button. In this expression, the variable
     * <code>$row</code> the row number (zero-based); <code>$data</code> the data model for the row;
     * and <code>$this</code> the column object.
     */
    public $showUrl='Yii::app()->controller->createUrl("show",array("id"=>$data->primaryKey))';
    public $hideUrl='Yii::app()->controller->createUrl("hide",array("id"=>$data->primaryKey))';

    public function defaultButtons () {
        return array(
            self::BUTTON_SHOW => array(
                'icon' => 'icon-eye-open icon-white',
                'label' => 'Видимый',
                'options' => array('class'=>'btn btn-success', 'actionType' => 'ajaxPage'),
            ),
            self::BUTTON_HIDE => array(
                'icon' => 'icon-eye-close icon-white',
                'label' => 'Скрытый',
                'options' => array('class'=>'btn btn-inverse', 'actionType' => 'ajaxPage'),
            ),
        );
    }

    /**
     * Initializes the column.
     * This method registers necessary client script for the button column.
     */
    public function init()
    {
    }

    /**
     * Renders the data cell content.
     * This method renders the view, update and delete buttons in the data cell.
     * @param integer $row the row number (zero-based)
     * @param mixed $data the data associated with the row
     */
    protected function renderDataCellContent($row,$data)
    {
        $tr=array();
        ob_start();
        if ($this->visibleCondition && !$this->evaluateExpression($this->visibleCondition,array('row'=>$row,'data'=>$data)))
              return;

        $defaults = $this->defaultButtons();
        $itemVisible = $this->evaluateExpression($this->itemVisible,array('row'=>$row,'data'=>$data));
        if ($itemVisible) {
            $id = self::BUTTON_SHOW;
            $button = $defaults[self::BUTTON_SHOW];
            $button['url'] = $this->{self::BUTTON_HIDE.'Url'};
        }
        else {
            $id = self::BUTTON_HIDE;
            $button = $defaults[self::BUTTON_HIDE];
            $button['url'] = $this->{self::BUTTON_SHOW.'Url'};
        }

        $this->renderButton($id,$button,$row,$data);

        $content=ob_get_contents();
        ob_clean();

        ob_end_clean();
        echo $content;
    }

    /**
     * Renders a link button.
     * @param string $id the ID of the button
     * @param array $button the button configuration which may contain 'label', 'url', 'imageUrl' and 'options' elements.
     * See {@link buttons} for more details.
     * @param integer $row the row number (zero-based)
     * @param mixed $data the data object associated with the row
     */
    protected function renderButton($id,$button,$row,$data)
    {
        $label=isset($button['label']) ? $button['label'] : $id;
        $url=isset($button['url']) ? $this->evaluateExpression($button['url'],array('data'=>$data,'row'=>$row)) : '#';
        $options=isset($button['options']) ? $button['options'] : array();

        if(isset($button['icon']) && is_string($button['icon'])) {
            $span = CHtml::tag('i', array('class' => $button['icon']), '');
            $options = array_merge($options, array('title' => $label));
            echo CHtml::link($span,$url,$options);
        }
        else
            echo CHtml::link($label,$url,$options);
    }

}
