<?php
/**
 * MultiSelect widget class file.
 *
 * @copyright Copyright &copy; stepanoff
 */

/**
 * Красивый мультиселект
 */
class VHtmlMultiSelectWidget extends CWidget
{
	public $model;
	public $attribute;
	public $htmlOptions = array();
	public $data = array();
	
	/**
	 * 
	 */
	public function run()
	{
        $cs = Yii::app()->clientScript;
        $url = Yii::app()->VExtension->getAssetsUrl();

        $cs->registerCssFile($url . '/css/multiselect.css');
        $cs->registerCoreScript('jquery');
        $cs->registerCoreScript('jquery.ui');
        $cs->registerScriptFile($url . '/js/jquery.multiselect.js', CClientScript::POS_HEAD);

        $inputId = CHtml::activeId($this->model, $this->attribute);
        $script = '$(document).ready(function() { $("#'.$inputId.'").dropdownchecklist({ maxDropHeight: 300, width: 300 }); });';
		$cs->registerScript($inputId.'multiselect', $script, CClientScript::POS_END);

		echo CHtml::activeDropDownList($this->model, $this->attribute, $this->data, array_merge(array('multiple' => 'multiple'), $this->htmlOptions));
	}
}