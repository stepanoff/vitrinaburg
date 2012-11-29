<?php
/**
 * MultiSelect widget class file.
 *
 * @copyright Copyright &copy; 2008-2010 Mediasite :)
 */

/**
 * Отображает всплывающие сообщения
 */
class HtmlMultiSelectWidget extends ExtendedWidget
{
	public $model;
	public $attribute;
	public $htmlOptions = array();
	public $data;
	
	/**
	 * 
	 */
	public function run()
	{
		$cs=Yii::app()->clientScript;
		$script = '$(document).ready(function() { $("#'.$this->attribute.'").dropdownchecklist({ maxDropHeight: 300, width: 300 }); });';
		$cs->registerScript($this->attribute.'multiselect', $script, CClientScript::POS_END);

		echo CHtml::activeDropDownList($this->model, $this->attribute, $this->data, array_merge(array('id'=>$this->attribute, 'multiple' => 'multiple'), $this->htmlOptions));
	}
}