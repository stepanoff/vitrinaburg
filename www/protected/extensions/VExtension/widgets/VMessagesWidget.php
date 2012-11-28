<?php 
class MessagesWidget extends ExtendedWidget
{
	const TYPE_ALERT = 1;
	const TYPE_INFORMER = 2;
	
	public $items = null;
	public $type = null;
	public $result_array = false;
	public $isAjax = false;
	
	private $types = array();
	
	public function jsOptions ()
	{
		return array (
			'messDiv' => "'informerMessages'",
			'messContainer' => "'messageContainer'",
			'messContainerClass' => "'messageContainer'",
			'listContainer'=>"'listContainer'",
			'messClass' => "'informerMessage'",
			'titleClass' => "'title'",
			'contentClass' => "'messageContent'",
			'actionBtnClass' => "'action'",
			'closeActionClass' => "'close'",
			'updateActionClass' => "'update'",
			'nextActionClass' => "'next'",
			'readActionClass' => "'read'",
			'listContainer' => "'listContainer'",
			'btnClass' => "'button'",
			'skipWord'=> "'след'",
			'prevWord'=> "'пред'",
			'sendFunction'=> "$.ajaxCallback",
			'showOnStart'=> "true",
			'readUrl' => "'/informer/read/'",
			);
	}
		
	/**
	 */
	public function init()
	{
		$this->types = Informer::$types;
		$this->type = self::TYPE_ALERT;
		parent::init();
	}

	/**
	 * Calls {@link renderMenu} to render the menu.
	 */
	public function run()
	{
		if (!Yii::app()->getComponent('informer'))
			return false;
		
		if ($this->items === null)
		{
			$this->items = array();
			$items = Yii::app()->informer->getAlerts();
			$items2 = Yii::app()->informer->getUnread();
			if (is_array($items2) && sizeof($items2))
				$items = array_merge($items, $items2);
			$this->items = $items;
		}
		
		return $this->renderMessages($this->items);
	}
	
	/**
	 * Calls {@link renderMenu} to render the menu.
	 */
	public function runInternal()
	{
		$this->result_array = true;
		ob_start();
		ob_implicit_flush(false);
		$this->run();
		return ob_get_clean();
	}


	/**
	 */
	protected function renderMessages($items)
	{
		$options = $this->jsOptions();
		if (!$this->result_array)
		{
			echo CHtml::openTag('div', array('id'=>str_replace("'",'',$options['messDiv']) ));
		}
//		else
//		{
//			$items = array();
//		}
		if(count($items))
		{
			foreach($items as $item)
			{
//				if ($this->result_array)
//					ob_start();
				$this->renderMessage($item);
				if ($this->result_array)
				{
//					$mess = ob_get_clean();
//					$items[] = $mess;
				}
			}
		}
		if (!$this->result_array)
		{
			echo CHtml::closeTag('div');
			$this->renderScript ();
		}
		else
			return $items;
	}
	
	protected function renderScript ()
	{
			$o = $this->jsOptions();
			$divId = str_replace("'",'',$o['messDiv']);
			$cs=Yii::app()->clientScript;
//			$cs->registerScriptFile(Yii::app()->params['interfaceResourcesUrl2'].'/js/admin.messqueue.js', CClientScript::POS_END);
			$opts = array();
			foreach ($this->jsOptions() as $k=>$v)
			{
				$opts[] = $k.' : '.$v;
			}
			$script = "
$('#".$divId."').hide();
$(document).ready(function(){
Messager = new messQueue({
".implode(', ',$opts)."
					});
});";
			$cs->registerScript('widget-oc1', $script, CClientScript::POS_END);
	}

	protected function renderMessage($item)
	{
		$options = $this->jsOptions();
		$icon_classes = array();
		$classes = array(str_replace("'",'',$options['contentClass']));
		$type_class = isset($this->types[$item->info_type])?$this->types[$item->info_type]['class']:'';
		if (!empty($type_class))
		{
			$classes[] = 'ui-state-'.$this->types[$item->info_type]['stateClass'].'-text';
			$icon_classes[] = 'icon';
			$icon_classes[] = 'ui-icon';
			$icon_classes[] = 'ui-icon-'.$type_class;
		}
			
		echo CHtml::openTag('div', array('class'=>str_replace("'",'',$options['messClass']) ));
		if ($item->id)
		{
			echo CHtml::tag('div', array('class'=>str_replace("'",'',$options['listContainer']) ), '');
			if ($item->author && $item->author!=Yii::app()->user->id)
			{
				/*
				// вставка фото отправителя
				if ($tmp = Yii::app()->Dir->getbyAttr(Dir::DIR_ADMINS, 'user_id', $item->author))
				{
					$author = array_shift($tmp);
					echo CHtml::openTag('div', array('class'=>'headers' ));
					if (!empty($author['photo']))
						echo CHtml::tag('span', array('class'=>'photo', 'style'=>'background-image:url('.FileUtils::thumb($author['photo'], 60, 60).');' ), '');
					echo CHtml::tag('span', array('class'=>'date' ), DateUtils::_date($item->date));
					echo CHtml::tag('span', array('class'=>'from' ), 'От: <span>'.$author['name'].'</span>');
					//echo CHtml::link('ответить', '/feedback?manager='.$author['id'], array('target'=>'_blank', 'class'=>'feedback close'));
					echo CHtml::tag('div', array('class'=>'clear' ),'');
					echo CHtml::closeTag('div');
				}
				*/
			}
		}
		echo CHtml::tag('div', array('class'=>str_replace("'",'',$options['titleClass']) ), $item->title);
		echo CHtml::openTag('div', array('class'=>implode(' ',$classes)));
		if (!empty($type_class))
			echo CHtml::tag('span', array('class'=>implode(' ',$icon_classes)), '');
		echo $item->text;
		echo CHtml::closeTag('div');
		
		
		echo CHtml::openTag('div', array('class'=>'buttons ui-dialog-buttonpane ui-widget-content ui-helper-clearfix'));
		// TO_DO: показываем кнопки
		if (!empty($item->buttons))
		{
			if (!is_array($item->buttons))
				$buttons = unserialize ($item->buttons);
			else
				$buttons = $item->buttons;
			foreach ($buttons as $btn)
			{
				$this->renderButton($btn, $item);
			}
		}
		echo CHtml::closeTag('div');
		echo CHtml::closeTag('div');
	}

	protected function renderButton($item, $obj)
	{
		$options = $this->jsOptions();
		$action = isset($item['action'])?$item['action']:'';
		$class = '';
		switch ($action)
		{
			case Informer::ACTION_CLOSE:
				$class = str_replace("'",'',$options['nextActionClass']);
				$action = '';
				break;
			case Informer::ACTION_READ:
				$class = str_replace("'",'',$options['readActionClass']);
				$action = '';
				break;
			default:
				if (!empty($action))
					$class = str_replace("'",'',$options['actionBtnClass']);
					if (is_array($action))
					{
						$route = isset($action[0]) ? $action[0] : '';
						$params = isset($action[1]) ? array_splice($action,1) : array();
						$action = Yii::app()->urlManager->createUrl ($route, $params);
					}
				break;
		}
		
		echo CHtml::form($action);
		if (isset($item['params']) && sizeof($item['params']))
		{
			foreach ($item['params'] as $k=>$v)
			{
				echo CHtml::hiddenField($k,$v);
			}
		}
		
		$type = isset($item['type'])?$item['type']:'';
		$text = isset($item['text'])?$item['text']:'';
		switch ($type)
		{
			case Informer::BTN_OK:
				$value = 'Ok';
				//echo CHtml::button('ok', array('class'=>"button ui-dialog-buttonpane ui-widget-content ui-helper-clearfix ".$class, 'type'=>'button', 'name'=>$obj->id ));
				break;
			case Informer::BTN_YES:
				$value = 'Да';
				break;
			case Informer::BTN_NO:
				$value = 'Нет';
				break;
			default:
				$value = $text;
				break;
		}
		echo '<button class="button ui-state-default ui-corner-all '.$class.'" type="button" name="'.$obj->id.'">'.$value.'</button>';
		//echo CHtml::submitButton('yes', array('class'=>"button ".$class, 'name'=>$obj->id ));
		echo CHtml::endForm();
	}

}
?>