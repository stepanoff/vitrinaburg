<?php
class AdminInputWidget extends CWidget
{
	public $id =0;
	public $action = ''; // урл отправки данных
	public $options = array(); // htmlOptions для селекта
	public $params = null; // массив ключ=>значение дополнительных данных для передачи скрипту
	public $value = false; // текущий выбор
	public $ajax = false; // посылать данные постом или ajax'ом
	public $key = 'value'; // имя инпута
	public $elId = '';
	public $data = array(); // для дропдаунов
	
	public function run()
	{
		$this->elId = md5(rand(1, time()));
		$this->options['id'] = $this->elId;
		$this->render('admin.views.widgets.input', array(
				'id' => $this->id,
				'value' => $this->value,
				'params' => $this->params,
				'data' => $this->data,
				'key' => $this->key,
				'elId' => $this->elId,
				'inputElement' => $this->inputElement(),
				'action'=>$this->action,
				'ajax'=>$this->ajax,
			)
		);
	}
	
	public function inputElement ()
	{
		return  CHtml::textField($this->elId, $this->value, $this->options);
	}
}