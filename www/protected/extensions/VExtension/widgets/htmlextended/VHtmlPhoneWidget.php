<?php
/**
 * ввод телефона по маске
 */
Yii::import('ext.htmlextended.components.HtmlSingleFileWidget');
class HtmlPhoneWidget extends ExtendedWidget
{
    public $model;
    public $attribute;

    protected $_template = 'phone';

    public function run()
    {
        $this->render($this->_template, array(
            'inputId' => CHtml::activeId($this->model, $this->attribute),
            'model' => $this->model,
            'attribute' => $this->attribute,
        ));
    }

}