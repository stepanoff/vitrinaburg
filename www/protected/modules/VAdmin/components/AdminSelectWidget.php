<?php
class AdminSelectWidget extends AdminInputWidget
{
	public $data = array(); // htmlOptions для селекта
	
	public function inputElement ()
	{
		if (!isset($this->data[0]))
			$this->data = array('0'=>'Выбрать')+$this->data;
		return  CHtml::dropDownList($this->key, $this->value, $this->data, $this->options);
	}
}