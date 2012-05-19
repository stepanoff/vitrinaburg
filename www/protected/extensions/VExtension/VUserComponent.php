<?php
class VUserComponent extends CApplicationComponent implements IWebUser
{
	const STATES_VAR='__states';
	const AUTH_TIMEOUT_VAR='__timeout';
    const COOKIE_DAYS = 1; // сколько дней еще будет жить кука на клиенте после даты протухания, полученной от сервиса (чтобы авторизация внезапно не отвалилась). Реальная дата протухания хранится в базе.

    private $_isGuest = null;

	/**
	 * @var boolean whether to enable cookie-based login. Defaults to false.
	 */
	public $allowAutoLogin=false;

    /**
     * @var string class name which stores user data and tokens.
     */
    public $dbDriver='VMysqlAuthDbDriver';

    /**
     * @var db class which stores user data and tokens.
     */
    private $_dbDriver=null;

	/**
	 * @var string the name for a guest user. Defaults to 'Guest'.
	 * This is used by {@link getName} when the current user is a guest (not authenticated).
	 */
	public $guestName='Guest';
	/**
	 * @var string|array the URL for login. If using array, the first element should be
	 * the route to the login action, and the rest name-value pairs are GET parameters
	 * to construct the login URL (e.g. array('/site/login')). If this property is null,
	 * a 403 HTTP exception will be raised instead.
	 * @see CController::createUrl
	 */
	public $loginUrl=array('/site/login');
	/**
	 * @var array the property values (in name-value pairs) used to initialize the identity cookie.
	 * Any property of {@link CHttpCookie} may be initialized.
	 * This property is effective only when {@link allowAutoLogin} is true.
	 * @since 1.0.5
	 */
	public $identityCookie;
	/**
	 * @var integer timeout in seconds after which user is logged out if inactive.
	 * If this property is not set, the user will be logged out after the current session expires
	 * (c.f. {@link CHttpSession::timeout}).
	 * @since 1.1.7
	 */
	public $authTimeout;
	/**
	 * @var boolean whether to automatically renew the identity cookie each time a page is requested.
	 * Defaults to false. This property is effective only when {@link allowAutoLogin} is true.
	 * When this is false, the identity cookie will expire after the specified duration since the user
	 * is initially logged in. When this is true, the identity cookie will expire after the specified duration
	 * since the user visits the site the last time.
	 * @see allowAutoLogin
	 * @since 1.1.0
	 */
	public $autoRenewCookie=false;
	/**
	 * @var boolean whether to automatically update the validity of flash messages.
	 * Defaults to true, meaning flash messages will be valid only in the current and the next requests.
	 * If this is set false, you will be responsible for ensuring a flash message is deleted after usage.
	 * (This can be achieved by calling {@link getFlash} with the 3rd parameter being true).
	 * @since 1.1.7
	 */

	private $_keyPrefix;

    private $__token = null;
    private $__userData = null;

	/**
	 * PHP magic method.
	 * This method is overriden so that persistent states can be accessed like properties.
	 * @param string $name property name
	 * @return mixed property value
	 * @since 1.0.3
	 */
	public function __get($name)
	{
		if($this->hasState($name))
			return $this->getState($name);
		else
			return parent::__get($name);
	}

	/**
	 * PHP magic method.
	 * This method is overriden so that persistent states can be set like properties.
	 * @param string $name property name
	 * @param mixed $value property value
	 * @since 1.0.3
	 */
	public function __set($name,$value)
	{
		if($this->hasState($name))
			$this->setState($name,$value);
		else
			parent::__set($name,$value);
	}

	/**
	 * PHP magic method.
	 * This method is overriden so that persistent states can also be checked for null value.
	 * @param string $name property name
	 * @return boolean
	 * @since 1.0.3
	 */
	public function __isset($name)
	{
		if($this->hasState($name))
			return $this->getState($name)!==null;
		else
			return parent::__isset($name);
	}

	/**
	 * PHP magic method.
	 * This method is overriden so that persistent states can also be unset.
	 * @param string $name property name
	 * @throws CException if the property is read only.
	 * @since 1.0.3
	 */
	public function __unset($name)
	{
		if($this->hasState($name))
			$this->setState($name,null);
		else
			parent::__unset($name);
	}

	/**
	 * Initializes the application component.
	 * This method overrides the parent implementation by starting session,
	 * performing cookie-based authentication if enabled
	 */
	public function init()
	{
        if (!$this->dbDriver)
            return false;

		parent::init();

        $className = $this->dbDriver;
        $this->_dbDriver = new $className;

		Yii::app()->getSession()->open();
		if($this->getIsGuest() && $this->allowAutoLogin)
			$this->restoreFromCookie();
		else if($this->autoRenewCookie && $this->allowAutoLogin)
			$this->renewCookie();

		$this->updateAuthStatus();
	}

	/**
	 * Logs in a user.
	 *
	 * The user identity information will be saved in storage that is
	 * persistent during the user session. By default, the storage is simply
	 * the session storage. If the duration parameter is greater than 0,
	 * a cookie will be sent to prepare for cookie-based login in future.
	 *
	 * Note, you have to set {@link allowAutoLogin} to true
	 * if you want to allow user to be authenticated based on the cookie information.
	 *
	 * @param IUserIdentity $identity the user identity (which should already be authenticated)
	 * @param integer $duration number of seconds that the user can remain in logged-in status. Defaults to 0, meaning login till the user closes the browser.
	 * If greater than 0, cookie-based login will be used. In this case, {@link allowAutoLogin}
	 * must be set true, otherwise an exception will be thrown.
	 */
	public function login($identity,$rememberMe=false)
	{
		$states=array();
        $serviceId = $identity->getId();
        $serviceName = $identity->getState('service');
        $data = $this->_dbDriver->findByService($serviceName, $serviceId);

        $duration = $rememberMe ? $identity->getDuration() : 0;

        $info = $identity->getServiceAttributes();
        $info['service'] = $serviceName;
        $info['serviceId'] = $serviceId;

        $this->setInfo($info);

        if (!$data)
        {
            $id = $this->_dbDriver->addUser($this->getInfo());
        }
        else
        {
            $id = $data['id'];
            if ($this->infoChanged($data,$this->getInfo() ))
                $this->_dbDriver->updateByPk($id, $this->getInfo());
        }

        if (!$id)
            return false;

		if($this->beforeLogin($id,$states,false))
		{
            $token = $this->generateToken();
            $states['__token'] = $token;

			$this->changeIdentity($id,$identity->getName(),$states);
            $this->_dbDriver->addToken($id, $token, $duration);

			if($this->allowAutoLogin)
				$this->saveToCookie(($duration + 60*60*24*self::COOKIE_DAYS));
			elseif ($duration>0)
				throw new CException(Yii::t('yii','{class}.allowAutoLogin must be set true in order to use cookie-based authentication.',
					array('{class}'=>get_class($this))));

			$this->afterLogin(false);
		}
	}

	/**
	 * Logs out the current user.
	 * This will remove authentication-related session data.
	 * If the parameter is true, the whole session will be destroyed as well.
	 * @param boolean $destroySession whether to destroy the whole session. Defaults to true. If false,
	 * then {@link clearStates} will be called, which removes only the data stored via {@link setState}.
	 * This parameter has been available since version 1.0.7. Before 1.0.7, the behavior
	 * is to destroy the whole session.
	 */
	public function logout($destroySession=true)
	{
		if($this->beforeLogout())
		{
			if($this->allowAutoLogin)
			{
				Yii::app()->getRequest()->getCookies()->remove($this->getStateKeyPrefix());
				if($this->identityCookie!==null)
				{
					$cookie=$this->createIdentityCookie($this->getStateKeyPrefix());
					$cookie->value=null;
					$cookie->expire=0;
					Yii::app()->getRequest()->getCookies()->add($cookie->name,$cookie);
				}
			}
            if ($this->getState('__token')!==null)
            {
                $this->_dbDriver->removeToken($this->getState('__token'));
            }

			if($destroySession)
				Yii::app()->getSession()->destroy();
			else
				$this->clearStates();
			$this->afterLogout();
		}
	}

	/**
	 * @return boolean whether the current application user is a guest.
	 */
	public function getIsGuest()
	{
        if ($this->_isGuest === null)
        {
            $this->_isGuest = true;
            if ($this->allowAutoLogin)
            {
                $this->restoreFromCookie();
            }
            $this->_isGuest = $this->getState('__id')===null || $this->getState('__token')===null;
        }
        return $this->_isGuest;
	}

	/**
	 * @return mixed the unique identifier for the user. If null, it means the user is a guest.
	 */
	public function getId()
	{
		return $this->getState('__id');
	}

	/**
	 * @param mixed $value the unique identifier for the user. If null, it means the user is a guest.
	 */
	public function setId($value)
	{
		$this->setState('__id',$value);
	}

    public function getToken()
    {
        return $this->getState('__token');
    }

    public function setToken($value)
    {
        $this->setState('__token',$value);
    }

	/**
	 * Returns the unique identifier for the user (e.g. username).
	 * This is the unique identifier that is mainly used for display purpose.
	 * @return string the user name. If the user is not logged in, this will be {@link guestName}.
	 */
	public function getName()
	{
		if(($name=$this->getState('__name'))!==null)
			return $name;
		else
			return $this->guestName;
	}

	/**
	 * Sets the unique identifier for the user (e.g. username).
	 * @param string $value the user name.
	 * @see getName
	 */
	public function setName($value)
	{
		$this->setState('__name',$value);
	}

    public function getInfoFields ()
    {
        return array('id', 'name', 'username', 'url', 'gender', 'avatar', 'photo', 'email', 'service', 'serviceId', 'updated');
    }

    protected function setInfo($mixed)
    {
        $fields = $this->getInfoFields();
        foreach ($mixed as $k=>$v)
        {
            if (in_array($k, $fields))
            {
                $this->__userData[$k] = $v;
            }
        }
    }

    public function getInfo()
    {
        if ($this->__userData == null)
        {
            $this->__userData = array();
            $id = $this->getId();
            if ($id)
                $data = $this->_dbDriver->findByPk($id);
            if ($data)
            {
                foreach ($this->getInfoFields() as $key)
                {
                    $val = '';
                    if (isset($data[$key]))
                        $val = $data[$key];
                    $this->__userData[$key] = $val;
                }
            }
        }
        return $this->__userData;
    }

    public function getInfoById($id)
    {
            $res = array();
            if ($id)
                $data = $this->_dbDriver->findByPk($id);
            if ($data)
            {
                foreach ($this->getInfoFields() as $key)
                {
                    $val = '';
                    if (isset($data[$key]))
                        $val = $data[$key];
                    $res[$key] = $val;
                }
            }
        return $res;
    }

	/**
	 * Returns the URL that the user should be redirected to after successful login.
	 * This property is usually used by the login action. If the login is successful,
	 * the action should read this property and use it to redirect the user browser.
	 * @param string $defaultUrl the default return URL in case it was not set previously. If this is null,
	 * the application entry URL will be considered as the default return URL.
	 * @return string the URL that the user should be redirected to after login.
	 */
	public function getReturnUrl($defaultUrl=null)
	{
		return $this->getState('__returnUrl', $defaultUrl===null ? Yii::app()->getRequest()->getScriptUrl() : CHtml::normalizeUrl($defaultUrl));
	}

	/**
	 * @param string $value the URL that the user should be redirected to after login.
	 */
	public function setReturnUrl($value)
	{
		$this->setState('__returnUrl',$value);
	}

	/**
	 * This method is called before logging in a user.
	 * You may override this method to provide additional security check.
	 * For example, when the login is cookie-based, you may want to verify
	 * that the user ID together with a random token in the states can be found
	 * in the database. This will prevent hackers from faking arbitrary
	 * identity cookies even if they crack down the server private key.
	 * @param mixed $id the user ID. This is the same as returned by {@link getId()}.
	 * @param array $states a set of name-value pairs that are provided by the user identity.
	 * @param boolean $fromCookie whether the login is based on cookie
	 * @return boolean whether the user should be logged in
	 * @since 1.1.3
	 */
	protected function beforeLogin($id,$states,$fromCookie)
	{
		return true;
	}

	/**
	 * This method is called after the user is successfully logged in.
	 * You may override this method to do some postprocessing (e.g. log the user
	 * login IP and time; load the user permission information).
	 * @param boolean $fromCookie whether the login is based on cookie.
	 * @since 1.1.3
	 */
	protected function afterLogin($fromCookie)
	{
        return true;
	}

	/**
	 * This method is invoked when calling {@link logout} to log out a user.
	 * If this method return false, the logout action will be cancelled.
	 * You may override this method to provide additional check before
	 * logging out a user.
	 * @return boolean whether to log out the user
	 * @since 1.1.3
	 */
	protected function beforeLogout()
	{
		return true;
	}

	/**
	 * This method is invoked right after a user is logged out.
	 * You may override this method to do some extra cleanup work for the user.
	 * @since 1.1.3
	 */
	protected function afterLogout()
	{
	}

	/**
	 * Populates the current user object with the information obtained from cookie.
	 * This method is used when automatic login ({@link allowAutoLogin}) is enabled.
	 * The user identity information is recovered from cookie.
	 * Sufficient security measures are used to prevent cookie data from being tampered.
	 * @see saveToCookie
	 */
	protected function restoreFromCookie()
	{
		$app=Yii::app();
		$cookie=$app->getRequest()->getCookies()->itemAt($this->getStateKeyPrefix());

        if($cookie && !empty($cookie->value))
        {
            $data = $this->_dbDriver->findByToken($cookie->value);

			if(is_array($data) && isset($data['id'],$data['name'],$data['duration']))
			{
                $states = array('__token'=>$cookie->value);
                $duration = $data['duration'];
                $id = $data['id'];
                $name = $data['name'];
                // если кука ставилась на время и протухла, не авторизуем
                if (!$duration || ($duration && $duration > time()))
                {
                    if($this->beforeLogin($id,$states,true))
                    {
                        $this->changeIdentity($id,$name,$states);
                        if($this->autoRenewCookie)
                        {
                            $cookie->expire=time()+$duration;
                            $app->getRequest()->getCookies()->add($cookie->name,$cookie);
                        }
                        return $this->afterLogin(true);
                    }
                }
			}
		}
        $this->changeIdentity(null,null,array());
        return false;
	}

	/**
	 * Renews the identity cookie.
	 * This method will set the expiration time of the identity cookie to be the current time
	 * plus the originally specified cookie duration.
	 * @since 1.1.3
	 */
	protected function renewCookie()
	{
		$cookies=Yii::app()->getRequest()->getCookies();
		$cookie=$cookies->itemAt($this->getStateKeyPrefix());

        if($cookie && !empty($cookie->value))
        {
            $data = $this->_dbDriver->findByToken($cookie->value);
			if(is_array($data) && isset($data['id'],$data['name'],$data['duration']))
			{
				$cookie->expire=time()+$data['duration'];
				$cookies->add($cookie->name,$cookie);
			}
		}
	}

	/**
	 * Saves necessary user data into a cookie.
	 * This method is used when automatic login ({@link allowAutoLogin}) is enabled.
	 * This method saves user ID, username, other identity states and a validation key to cookie.
	 * These information are used to do authentication next time when user visits the application.
	 * @param integer $duration number of seconds that the user can remain in logged-in status. Defaults to 0, meaning login till the user closes the browser.
	 * @see restoreFromCookie
	 */
	protected function  saveToCookie($duration)
	{
		$app=Yii::app();
		$cookie=$this->createIdentityCookie($this->getStateKeyPrefix());
        if ($duration > 0)
    		$cookie->expire=time()+$duration;
		$cookie->value=$this->generateToken();
		$app->getRequest()->getCookies()->add($cookie->name,$cookie);
	}

    protected function generateToken ()
    {
        if ($this->__token === null)
        {
            $data=array(
                $this->getId(),
                $this->getName(),
            );
            $token=md5(serialize($data).mktime().rand(0, 1000000));
            $this->__token = $token;
        }
        return $this->__token;
    }

    public function findUidByToken ($token)
    {
        return $this->_dbDriver->findUidByToken($token);
    }

    protected function infoChanged ($newInfo, $oldInfo)
    {
        $md5_1 = '';
        $md5_2 = '';

        $tmp = array();
        foreach ($this->getInfoFields() as $k)
        {
            if ($k == 'updated')
                continue;
            $tmp[] = $k.'_'.(isset($newInfo[$k]) ? $newInfo[$k] : '');
        }
        $md5_1 = md5(implode('_', $tmp));

        $tmp = array();
        foreach ($this->getInfoFields() as $k)
        {
            if ($k == 'updated')
                continue;
            $tmp[] = $k.'_'.(isset($oldInfo[$k]) ? $oldInfo[$k] : '');
        }
        $md5_2 = md5(implode('_', $tmp));

        if ($md5_1 != $md5_2)
            return true;
        return false;
    }


	/**
	 * Creates a cookie to store identity information.
	 * @param string $name the cookie name
	 * @return CHttpCookie the cookie used to store identity information
	 * @since 1.0.5
	 */
	protected function createIdentityCookie($name)
	{
		$cookie=new CHttpCookie($name,'');
		if(is_array($this->identityCookie))
		{
			foreach($this->identityCookie as $name=>$value)
				$cookie->$name=$value;
		}
		return $cookie;
	}

	/**
	 * @return string a prefix for the name of the session variables storing user session data.
	 */
	public function getStateKeyPrefix()
	{
		if($this->_keyPrefix!==null)
			return $this->_keyPrefix;
		else
			return $this->_keyPrefix=Yii::app()->params['sessionPrefix'];
	}

	/**
	 * @param string $value a prefix for the name of the session variables storing user session data.
	 * @since 1.0.9
	 */
	public function setStateKeyPrefix($value)
	{
		$this->_keyPrefix=$value;
	}

	/**
	 * Returns the value of a variable that is stored in user session.
	 *
	 * This function is designed to be used by CWebUser descendant classes
	 * who want to store additional user information in user session.
	 * A variable, if stored in user session using {@link setState} can be
	 * retrieved back using this function.
	 *
	 * @param string $key variable name
	 * @param mixed $defaultValue default value
	 * @return mixed the value of the variable. If it doesn't exist in the session,
	 * the provided default value will be returned
	 * @see setState
	 */
	public function getState($key,$defaultValue=null)
	{
		$key=$this->getStateKeyPrefix().$key;
		return isset($_SESSION[$key]) ? $_SESSION[$key] : $defaultValue;
	}

	/**
	 * Stores a variable in user session.
	 *
	 * This function is designed to be used by CWebUser descendant classes
	 * who want to store additional user information in user session.
	 * By storing a variable using this function, the variable may be retrieved
	 * back later using {@link getState}. The variable will be persistent
	 * across page requests during a user session.
	 *
	 * @param string $key variable name
	 * @param mixed $value variable value
	 * @param mixed $defaultValue default value. If $value===$defaultValue, the variable will be
	 * removed from the session
	 * @see getState
	 */
	public function setState($key,$value,$defaultValue=null)
	{
		$key=$this->getStateKeyPrefix().$key;
		if($value===$defaultValue)
			unset($_SESSION[$key]);
		else
			$_SESSION[$key]=$value;
	}

	/**
	 * Returns a value indicating whether there is a state of the specified name.
	 * @param string $key state name
	 * @return boolean whether there is a state of the specified name.
	 * @since 1.0.3
	 */
	public function hasState($key)
	{
		$key=$this->getStateKeyPrefix().$key;
		return isset($_SESSION[$key]);
	}

	/**
	 * Clears all user identity information from persistent storage.
	 * This will remove the data stored via {@link setState}.
	 */
	public function clearStates()
	{
		$keys=array_keys($_SESSION);
		$prefix=$this->getStateKeyPrefix();
		$n=strlen($prefix);
		foreach($keys as $key)
		{
			if(!strncmp($key,$prefix,$n))
				unset($_SESSION[$key]);
		}
	}

	/**
	 * Changes the current user with the specified identity information.
	 * This method is called by {@link login} and {@link restoreFromCookie}
	 * when the current user needs to be populated with the corresponding
	 * identity information. Derived classes may override this method
	 * by retrieving additional user-related information. Make sure the
	 * parent implementation is called first.
	 * @param mixed $id a unique identifier for the user
	 * @param string $name the display name for the user
	 * @param array $states identity states
	 */
	protected function changeIdentity($id,$name,$states)
	{
		Yii::app()->getSession()->regenerateID();
		$this->setId($id);
		$this->setName($name);
		$this->loadIdentityStates($states);
	}

	/**
	 * Retrieves identity states from persistent storage and saves them as an array.
	 * @return array the identity states
	 */
	protected function saveIdentityStates()
	{
		$states=array();
		foreach($this->getState(self::STATES_VAR,array()) as $name=>$dummy)
			$states[$name]=$this->getState($name);
		return $states;
	}

	/**
	 * Loads identity states from an array and saves them to persistent storage.
	 * @param array $states the identity states
	 */
	protected function loadIdentityStates($states)
	{
		$names=array();
		if(is_array($states))
		{
			foreach($states as $name=>$value)
			{
				$this->setState($name,$value);
				$names[$name]=true;
			}
		}
		$this->setState(self::STATES_VAR,$names);
	}

	/**
	 * Updates the authentication status according to {@link authTimeout}.
	 * If the user has been inactive for {@link authTimeout} seconds,
	 * he will be automatically logged out.
	 * @since 1.1.7
	 */
	protected function updateAuthStatus()
	{
		if($this->authTimeout!==null && !$this->getIsGuest())
		{
			$expires=$this->getState(self::AUTH_TIMEOUT_VAR);
			if ($expires!==null && $expires < time())
				$this->logout(false);
			else
				$this->setState(self::AUTH_TIMEOUT_VAR,time()+$this->authTimeout);
		}
	}

    public function checkAccess($operation,$params=array())
    {
        return true;
    }

}