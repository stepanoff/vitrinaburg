<?php 
class VMessagesWidget extends CWidget
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
			'readUrl' => "false",
			);
	}
		
	/**
	 */
	public function init()
	{
		$this->types = VInformer::$types;
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
            $cs = Yii::app()->clientScript;
            $url = Yii::app()->VExtension->getAssetsUrl();
//            $cs->registerCssFile($url.'/css/ui-popup.css');
            $cs->registerCoreScript('jquery');
//            $cs->registerCoreScript('jquery.ui');
            $cs->registerScriptFile($url.'/js/vapp.js', CClientScript::POS_HEAD);
            $cs->registerScriptFile($url.'/js/jquery.vmessqueue.js', CClientScript::POS_END);
			$opts = array();
			foreach ($this->jsOptions() as $k=>$v)
			{
				$opts[] = $k.' : '.$v;
			}
			$script = "
app.module.register( 'messages-widget', VMessQueue, {
".implode(', ',$opts)."
});";
			$cs->registerScript('widget-oc1', $script, CClientScript::POS_END);
	}

	protected function renderMessage($item)
	{
		$options = $this->jsOptions();

        $containerClass = str_replace("'",'',$options['messClass']);
		echo CHtml::openTag('div', array('class' => $containerClass ));

        $containerClass = 'modal hide fade';
		echo CHtml::openTag('div', array('class' => $containerClass ));

		if ($item->id)
		{
			echo CHtml::tag('div', array('class'=>str_replace("'",'',$options['listContainer']) ), '');
			if ($item->author && $item->author!=Yii::app()->user->id)
			{
				// todo: вставка фото отправителя
			}
		}
        $titleClass = 'modal-header '.str_replace("'",'',$options['titleClass']);
		echo CHtml::tag('div', array('class'=> $titleClass ), '<h3>'.$item->title.'</h3>');

        $contentClass = 'modal-body '.str_replace("'",'',$options['contentClass']);
		echo CHtml::openTag('div', array('class'=>$contentClass));
		echo $item->text;
		echo CHtml::closeTag('div');
		
		
		echo CHtml::openTag('div', array('class'=>'buttons modal-footer'));

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
        echo CHtml::closeTag('div');
	}

	protected function renderButton($item, $obj)
	{
		$options = $this->jsOptions();
		$action = isset($item['action'])?$item['action']:'';
        $actionType = '';
		$class = 'btn '.(isset($item['primary']) ? 'btn-primary ' : '');
		switch ($action)
		{
			case VInformer::ACTION_CLOSE:
				$class .= str_replace("'",'',$options['nextActionClass']);
				$href = '#';
				break;
			case VInformer::ACTION_READ:
				$class .= str_replace("'",'',$options['readActionClass']);
				$href = '#';
				break;
			default:
				if (!empty($action))
					$class .= str_replace("'",'',$options['actionBtnClass']);
                    $actionType = $action;
                    $href = '#';
					if (isset($item['url']))
					{
                        $href = $item['url'];
                        if (is_array($item['url'])) {
                            $route = isset($item['url']) && isset($item['url'][0]) ? $item['url'][0] : '';
                            $params = isset($item['url']) && isset($item['url'][1]) ? array_splice($item['url'],1) : array();
                            $href = Yii::app()->urlManager->createUrl ($route, $params);
                        }
					}
				break;
		}
        $options = array('class' => $class, 'action' => $actionType);
		
		$type = isset($item['type'])?$item['type']:'';
		$text = isset($item['text'])?$item['text']:'';
		switch ($type)
		{
			case VInformer::BTN_OK:
				$value = 'Ok';
				break;
			case VInformer::BTN_YES:
				$value = 'Да';
				break;
			case VInformer::BTN_NO:
				$value = 'Нет';
				break;
			default:
				$value = $text;
				break;
		}
        echo CHtml::link($value, $href, $options);
	}

}
?>