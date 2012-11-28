<?php
class ListWidget extends CWidget
{
	public $id;
	
	public $value;
	
	public $list = array();
	
	public $action;
	
	public $callback;
	
	public function run()
	{
		$options = array();
		
		$options['onChange'] = 'SendRequest("'.CHtml::normalizeUrl(array($this->action, 'id' => $this->id)).'", {value: this.value}'.($this->callback?', '.$this->callback:'').'); return false;';
		$options['onKeydown'] = $options['onChange'];

		$item = CHtml::dropDownList($this->action.$this->id, $this->value, $this->list, $options);
		
		$this->render('admin.views.widgets.list', array('item' => $item));
	}
}