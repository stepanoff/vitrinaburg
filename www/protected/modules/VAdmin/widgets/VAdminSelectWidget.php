<?php
class VAdminSelectWidget extends CGridColumn {

    public $htmlOptions=array('class'=>'button-column');
    /**
     * @var array the HTML options for the header cell tag.
     */
    public $headerHtmlOptions=array('class'=>'button-column', 'width' => '1%');
    /**
     * @var array the HTML options for the footer cell tag.
     */
    public $footerHtmlOptions=array('class'=>'button-column');

    public $url = 'Yii::app()->controller->createUrl("setValue",array("id"=>$data->primaryKey))';
    public $value; // выражение для определения текущего значения в выборе
    public $label = false;
    public $inputName = 'value';
    public $data = array();

    /**
     * Initializes the column.
     * This method registers necessary client script for the button column.
     */
    public function init()
    {
        // todo: присобачить js
        //$this->registerClientScript();
    }

    /**
     * Registers the client scripts for the button column.
     */
    protected function registerClientScript()
    {
//            Yii::app()->getClientScript()->registerScript(__CLASS__.'#'.$this->id, implode("\n",$js));
    }

    /**
     * Renders the data cell content.
     * This method renders the view, update and delete buttons in the data cell.
     * @param integer $row the row number (zero-based)
     * @param mixed $data the data associated with the row
     */
    protected function renderDataCellContent($row,$data)
    {
        ob_start();

        echo CHtml::openTag ('div', array('class' => 'btn-group'));

        $optGroup = $this->data;
        $currentValue = null;
        if ($this->value !== null) {
            $currentValue = $this->evaluateExpression($this->value,array('data'=>$data,'row'=>$row));
        }

        if (isset($optGroup[$currentValue])) {
            echo CHtml::tag('button', array('class' => 'btn dropdown-toggle', 'data-toggle' => 'dropdown'), $optGroup[$currentValue].' <span class="caret"></span>');
            unset($optGroup[$currentValue]);
        }
        else {
            echo CHtml::tag('button', array('class' => 'btn dropdown-toggle', 'data-toggle' => 'dropdown'), ' <span class="caret"></span>');
        }

        echo CHtml::openTag ('ul', array('class' => 'dropdown-menu'));
        $url = $this->evaluateExpression($this->url,array('data'=>$data,'row'=>$row));
        foreach($optGroup as $id=>$optionLabel)
        {
            $optionUrl = $url.(strpos($url, '?') ? '&' : '?' ).CHtml::encode($this->inputName).'='.CHtml::encode($id);
            echo CHtml::openTag ('li', array('class' => ''));
            echo CHtml::link($optionLabel, $optionUrl, array('class' => 'js-link', 'actionType' => 'ajaxPage'));
            echo CHtml::closeTag ('li');
        }
        echo CHtml::closeTag ('ul');

        echo CHtml::closeTag ('div');

        $c = ob_get_contents();
        ob_end_clean();
        echo $c;
    }

}
