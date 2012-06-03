<?php
class SwitchWidget extends CWidget
{
	public $list = array();
	
	public $ajax = false;
	
	public $delimetr = ' | ';
	
	private $def_item = array (
		'name' => 'ссылка', //
		'options' => array(), // массив html-атрибутов элемента
		'url' => array(), // урл ссылки array(путь, массив_параметров)
		'ajax' => false, // обрабатывать ссылку ajax'ом
		'select' => false, // тип элемента селект
		'callback' => 'false', // имя ф-ции, которую надо 
	);

	public $params = array(); // дополнительные параметры для передачи POST'ом, пока работает только для ajax'a
	
	public function run()
	{
		$list = array();
		
		foreach ($this->list as $item)
		{
			$item = array_replace_recursive ($this->def_item, $item);
			
			$ajax = ($this->ajax || (isset($item['ajax']) && $item['ajax']));
			
			if (!is_array($item['url']))
				$item['url'] = array($item['url']);
			
			$url_data = count($this->params)?$item['url']+$this->params:$item['url'];
			$url = CHtml::normalizeUrl($url_data);
			if ($ajax)
				$item['options']['onclick'] = 'SendRequest("'.$url.'", false'.($item['callback']?', '.$item['callback']:'').'); return false;';

			if (isset($item['select']) && $item['select'])
				$item['options']['style'] = 'font-weight: bold; color: #000000';

			$list[] = CHtml::link($item['name'], $url, $item['options']);
		}
		
		$this->render('admin.views.widgets.switch', array('list' => $list, 'delimetr' => $this->delimetr));
	}
}