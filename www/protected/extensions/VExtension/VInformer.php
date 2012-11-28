<?php
/**
 * Informer class file
 *
 * @author stepanoff
 * TO_DO:
 */

/**
 * VInformer - сообщения в всплывающих окнах
 *
 */
class VInformer extends CApplicationComponent
{
	public $userComponent = null;
	public $_userComponent = 'user';

	public $authManager = null;
	public $_authManager = 'authManager';
	
	//public $messageClass = 'InfoMessage';
	public $messageClass = 'VInfoMessage';
	public $userClass = 'VUser';
	
	protected $toCurrentUser = false;
	
	public $mailTemplate = 'informerDefault';
	
	const TYPE_NONE = 0;
	const TYPE_INFO = 1;
	const TYPE_ERROR = 2;
	const TYPE_WARNING = 3;
	
	const BTN_CUSTOM = 0;
	const BTN_OK = 1;
	const BTN_YES = 2;
	const BTN_NO = 3;
	
	const ACTION_NONE = 0;
	const ACTION_CLOSE = 1;
	const ACTION_READ = 2;
	const ACTION_CUSTOM = 16;
	
	public $_session_key = 'informer';
	protected $_unread = null; // массив непрочитанных сообщений
	protected $_hide_unread = false; // прочитаю потом

	// вские данные для виджета, украшения
	public static $types = array (
		self::TYPE_NONE => array ('class'=>false, 'stateClass'=>false, 'name'=>'нет'),
		self::TYPE_INFO => array ('class'=>'info', 'stateClass'=>'highlight', 'name'=>'инфо'),
		self::TYPE_ERROR => array ('class'=>'alert', 'stateClass'=>'error', 'name'=>'ошибка'),
		self::TYPE_WARNING => array ('class'=>'alert', 'stateClass'=>'error', 'name'=>'предупреждение'),
	);
	
	/**
	 * Initializes the application component.
	 */
	public function init()
	{
		parent::init();
		
		$this->userComponent = Yii::app()->getComponent($this->_userComponent);
		$this->authManager = Yii::app()->getComponent($this->_authManager);
		
		if (!isset($_SESSION[$this->_session_key]))
			$_SESSION[$this->_session_key] = array();
		$uid = isset($_SESSION[$this->_session_key]['uid'])?$_SESSION[$this->_session_key]['uid']:false;
		if ($uid!=$this->userComponent->id)
			$_SESSION[$this->_session_key] = array();
		
		$_SESSION[$this->_session_key]['uid'] = $this->userComponent->id;
		
		if (isset($_SESSION[$this->_session_key]['hideUnread']))
			$this->_hide_unread = $_SESSION[$this->_session_key]['hideUnread'];

	}

	/**
	 * добавить сообщение.
	 * @options - array
	 * - title - заголовок сообщения
	 * - text - текст сообщения
	 * - info_type - тип информации (TYPE_INFO, TYPE_ERROR, TYPE_WARNING)
	 * - author - автор, вызвавший сообщение (по умолчанию, пользователь)
	 * - date - время сообщения, по умолчанию текущее
	 * - show - bool показать принудительно (даже если пользователь нажал "не показывать новые в течение сессии")
	 * - show_once - удалить сообщение после прочтения
	 * - store - bool хранить сообщение в истории
	 * - read - признак "прочтено" (для хранимых в базе), по умолчанию false
	 * - to - id пользователя-адресата или массив id. Можно передать строку списка ролей через запятую. Сообщение уйдет всем пользователям с данной ролью. Например: array (10, 153, 'role'=>'manager, admin')
	 * - send - продублировать сообщение письмом. По умолчанию false. Можно передать сообщение с подготовленным текстом (?).
	 * 
	 * @actions - array список кнопок. Параметры кнопки задаются массивом:
	 * - type - тип BTN_CUSTOM, BTN_OK, BTN_YES, BTN_NO.
	 * - text - титл и текст на кнопке
	 * - action - действие при нажатии на кнопку ACTION_CLOSE, ACTION_READ или урл действия
	 * @params - список поле=>значение, которые будут отправлены постом при подтверждении сообщения
	 */
	public function message ($options, $actions = false, $params = false)
	{
		$def_actions = array(
				array ('type'=>self::BTN_OK, 'action'=>self::ACTION_READ, 'text'=>'Ok'),
			);

		// определяем адресатов
		$to = $this->getReceiverUids($options);
		if (isset($options['send']))
		{
			if (is_array($options['send']) && isset($options['send']['emails']))
				$emails = $options['send']['emails'];
			else
				$emails = $this->getEmailsByUids ($to, $options);
		}

		$options = array(
				'title' => isset($options['title'])?$options['title']:'',
				'text' => isset($options['text'])?$options['text']:'',
				'info_type' => isset($options['info_type'])?$options['info_type']:self::TYPE_INFO,
				'author' => isset($options['author'])?$options['author']:$this->userComponent->id,
				'date' => isset($options['date'])?$options['date']:date('Y-m-d G:i:00'),
				'show' => isset($options['show'])?$options['show']:0,
				'show_once' => isset($options['show_once'])?$options['show_once']:0,
				'read' => isset($options['read'])?$options['read']:0,
				'store' => isset($options['store'])?$options['store']:1,
				'send' => isset($options['send'])?$options['send']:0,
				'buttons' => $actions!==false?serialize($actions):serialize($def_actions),
				'params' =>  $params!==false?serialize($params):'',
				'to' => $to,
		);

		if ($options['show'])
		{
			$modelName = $this->messageClass;
			$message = new $modelName;
			$message->attributes = $options;
			// записываем сообщение в сессию
			if (!isset($_SESSION[$this->_session_key]['messages']))
				$_SESSION[$this->_session_key]['messages'] = array();
			$_SESSION[$this->_session_key]['messages'][] = $message;
		}
		
		if ($options['store'])
		{
			$add_unread = true;
			if ($options['show'] && $this->toCurrentUser)
			{
				$options['read'] = 1;
				$options['show'] = 0;
				$add_unread = false;
			}
			// сохраняем соощение в базу
			$modelName = $this->messageClass;
			$message = new $modelName;
			$message->attributes = array_intersect_key($options, array_flip($message->getSafeAttributeNames()));
			$message->save();
			// добавляем id в непрочитанные, если сообщение для текущего пользователя
			if ($add_unread && $this->toCurrentUser )
			{
				$this->_addUnread($message->id);
			}
		}
		
		if ($options['send'])
		{
            // todo: отправка не работает
            /*
			$mailer = Yii::app()->getComponent('mailQueue');
			
			$subject = is_array($options['send'])&&isset($options['send']['title'])?$options['send']['title']:$options['title'];
			$fields = is_array($options['send'])&&isset($options['send']['fields'])?$options['send']['fields']:array('text'=>$options['text']);
			$template = is_array($options['send'])&&isset($options['send']['template'])?$options['send']['template']:$this->mailTemplate;
			
			$fromName = is_array($options['send'])&&isset($options['send']['fromName']) ? $options['send']['fromName'] : $this->getSenderName();
			$fromEmail = is_array($options['send'])&&isset($options['send']['fromEmail']) ? $options['send']['fromEmail'] : $this->getSenderEmail();
			
			if ($emails)
			{
				foreach ($emails as $email=>$name)
				{
					$mailer->enqueueItem(array(
							'template' => $template,
							'fields' => $fields,
							'user_id' => 1,
							'subject' => $subject,
							'from_username' => $fromName,
							'from_email' => $fromEmail,
							'to_email' => $email,
							'to_address' => $email,
						));
				}
			}
            */
		}
	}
	
	public function getSenderEmail ()
	{
		return Yii::app()->params['senderEmail'];
	}
	
	public function getSenderName ()
	{
		return Yii::app()->params['siteName'];
	}
	
	public function getReceiverUids ($options)
	{
		$to = array();
		$this->toCurrentUser = false;
		if (isset($options['to']) && $options['to'])
		{
			if (isset($options['to']['role']))
			{
				$this->toCurrentUser = false;
				$to = array();
				$roles = explode (',', $options['to']['role']);
				foreach ($roles as $role)
				{
					// взять пользователей с данной ролью
					$modelName = $this->userClass;
					$users = new $modelName;
					$sql="SELECT * FROM ".$this->authManager." WHERE itemname=:itemname";
					$command=Yii::app()->db->createCommand($sql);
					$command->bindValue(':itemname',trim($role));
					if(($rows=$command->queryAll($sql))!==false)
					{
						foreach ($rows as $row)
							$to[] = (int)$row['userid'];
					}
				}
				unset($options['to']['role']);
			}
			$to = array_merge ($to, $options['to']);
			$to = array_unique($to);
		}
		else
		{
			$this->toCurrentUser = true;
			$to = $this->userComponent->id;
		}
		return $to;		
	}
	

	public function getEmailsByOptions ($uids, $options = false)
	{
		$emails = array ();
		$clientsIds = is_array($uids)?$uids:array($uids);
		
		$modelName = $this->userClass;
		$clients = CActiveRecord::model($modelName);
		$clients->getDbCriteria()->addInCondition('id', $clientsIds);
		$clients = $clients->findAll();
		foreach ($clients as $client)
		{
			$emails[$client->email] = $client->email;
		}
		return $emails;
	}

	/**
	 * всплывающее окошко с сообщением.
	 * Показывается один раз, в базу не записывается (ошибки, запросы подтверждения действия).
	 * Записываются в сессию, в случае отображения, удаляются из сессии.
	 * @options - см. message()
	 */
	public function alert ($options, $buttons = false)
	{
		if ($buttons===false)
			$buttons = array(
				array ('type'=>self::BTN_OK, 'action'=>self::ACTION_CLOSE, 'text'=>'Закрыть'),
			);
		$this->message (
			array(
				'title' => isset($options['title'])?$options['title']:'',
				'text' => isset($options['text'])?$options['text']:'',
				'info_type' => isset($options['info_type'])?$options['info_type']:self::TYPE_INFO,
				'author' => isset($options['author'])?$options['author']:$this->userComponent->id,
				'date' => isset($options['date'])?$options['date']:date('Y-m-d G:i:00'),
				'show' => isset($options['show'])?$options['show']:1,
				'show_once' => isset($options['show_once'])?$options['show_once']:0,
				'store' => isset($options['store'])?$options['store']:0,
			),
			$buttons
		);
	}
	
	public function alert_and_store ($options)
	{
		$options['store'] = true;
		$buttons = array(
			array ('type'=>self::BTN_OK, 'action'=>self::ACTION_READ, 'text'=>'Ok'),
		);
		$this->message ($options, $buttons);
	}
	
	/**
	 * всплывающее окошко с ошибкой. Аналогично alert, только в красной рамке с ошибкой
	 */
	public function error ($options)
	{
		$options['info_type'] = self::TYPE_ERROR;
		$this->alert ($options);
	}
	
	/**
	 * всплывающее окошко с предупреждением. Аналогично alert, только в рамке с предупреждением
	 */
	public function warning ($options)
	{
		$options['info_type'] = self::TYPE_WARNING;
		$this->alert ($options);
	}
		
	/**
	 * окошко с подтверждением
	 * @action - куда отправлять подтверждение
	 * @params - список поле=>значение, которые будут отправлены постом при подтверждении сообщения
	 */
	public function confirm ($options, $action=false, $params=false)
	{
		$action = $action!==false?($action.(strstr($action,'?')?'&':'?').'confirm=1'):self::ACTION_CLOSE;
		
		$this->message (
			array(
				'title' => isset($options['title'])?$options['title']:'',
				'text' => isset($options['text'])?$options['text']:'',
				'info_type' => isset($options['info_type'])?$options['info_type']:self::TYPE_INFO,
				'author' => isset($options['author'])?$options['author']:$this->userComponent->id,
				'date' => isset($options['author'])?$options['author']:date('Y-m-d G:i:00'),
				'show' => 1,
				'store' => 0,
			),
			array(
				array ('type'=>self::BTN_NO, 'action'=>self::ACTION_CLOSE, 'text'=>'Нет'),
                array ('type'=>self::BTN_YES, 'action'=>'ajaxPage', 'url' => $action, 'text'=>'Да', 'primary' => true),
			),
			$params
		);
	}
	
	public function read($messageId)
	{
		$message = CActiveRecord::model($this->messageClass)->findByPk($messageId);
		if ($message)
		{
			if ($message->readBy($this->userComponent->id))
				$this->_removeUnread($messageId);
		}
	}


	/**
	 * текущие сообщения для отображения в браузере
	 * @param - очистить кеш
	 */
	public function getAlerts ($clear=true)
	{
		if (isset($_SESSION[$this->_session_key]['messages']) && is_array($_SESSION[$this->_session_key]['messages']) && sizeof($_SESSION[$this->_session_key]['messages']))
		{
			$items = $_SESSION[$this->_session_key]['messages'];
			unset ($_SESSION[$this->_session_key]['messages']);
			return $items;
		}
		return array();
	}
	
	/**
	 * текущие непрочитанные
	 * @param - не обращать внимание на "прочитаю потом"
	 */
	public function getUnread ($ignore_hide=false)
	{
		if ($this->_hide_unread && !$ignore_hide)
			return array();

		$res = array();
		$ids = $this->_getUnread ();
		if (sizeof($ids))
		{
			$ids = implode(',',$ids);
			$res = CActiveRecord::model($this->messageClass)->findAll('`id` IN ('.$ids.')',array());
		}
		return $res;
	}
	
	/**
	 * добавить непрочитанное
	 */
	protected function _addUnread ($id)
	{
		$tmp = $this->_getUnread();
		array_push($tmp, $id);
		$this->_unread = $tmp;
		$_SESSION[$this->_session_key]['unread'] = $this->_unread;
	}
	
	/**
	 * удалить из не прочитанных
	 */
	protected function _removeUnread ($id)
	{
		$tmp = $this->_getUnread();
		if (in_array($id, $tmp))
		{
			$i = 0;
			foreach ($tmp as $_id)
			{
				if ($id==$_id)
				{
					array_splice($tmp, $i, 1);
					break;
				}
				$i++;
			} 
		}
		$this->_unread = $tmp;
		$_SESSION[$this->_session_key]['unread'] = $this->_unread;
	}
	
	/**
	 * массив id непрочитанных
	 */
	protected function _getUnread ($refresh = false)
	{
		if ($this->_unread === null)
		{
			if (isset($_SESSION[$this->_session_key]['unread']) && is_array($_SESSION[$this->_session_key]['unread']) && !$refresh)
				$this->_unread = $_SESSION[$this->_session_key]['unread'];
			else
			{
				// берем из базы непрочитанные
				$uid = false;
				if (!$uid)
					$uid = $this->userComponent->id;
				
				$this->_unread = CActiveRecord::model($this->messageClass)->getUnread($uid);
				$_SESSION[$this->_session_key]['unread'] = $this->_unread;
			}
		}
		return $this->_unread;
	}
	
	/**
	 * действие "прочитаю потом". Не показывать всплывающее окно с непрочитанным сообщением
	 */
	public function HideUnread ($hide = 1)
	{
		$hide = $hide===false?$hide:1;
		$_SESSION[$this->_session_key]['hideUnread'] = $hide;
		$this->_hide_unread = $hide;
	}
			
}
?>