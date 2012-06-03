<?php
class FilterWidget extends CWidget
{
	public $controller;
	
	public function run()
	{
		$filters = array();
		
		foreach ($this->controller->layoutsFilter() as $_k => $_v)
		{
			switch 	($_v['type'])
			{
				case 'text':
					$_v['field'] = CHtml::textField('filter['.$_k.']', isset($this->controller->_layoutFilters[$_k])?$this->controller->_layoutFilters[$_k]:null);
					break;
					
				case 'list':
					$_v['field'] = CHtml::dropDownList('filter['.$_k.']', isset($this->controller->_layoutFilters[$_k])?$this->controller->_layoutFilters[$_k]:null, $_v['data'], array('empty'=>'[выбрать]'));
					break;
					
				case 'bool':
					$_v['field'] = CHtml::checkBox('filter['.$_k.']', isset($this->controller->_layoutFilters[$_k])?$this->controller->_layoutFilters[$_k]:null);
					break;
					
				case 'date':
					$_v['field'] = CHtml::textField('filter['.$_k.']', isset($this->controller->_layoutFilters[$_k])?$this->controller->_layoutFilters[$_k]:null);
					break;					
			}
			
			$filters[] = $_v;
		}		
		
		$this->render('admin.views.widgets.filter', array('filters' => $filters));
	}
}