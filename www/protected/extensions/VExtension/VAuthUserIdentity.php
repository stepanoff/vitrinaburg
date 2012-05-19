<?php
class VAuthUserIdentity extends CBaseUserIdentity {
	const ERROR_NOT_AUTHENTICATED = 3;

	/**
	 * @var EAuthServiceBase the authorization service instance.
	 */
	protected $service;

    /**
     * @var EAuthServiceBase the authorization service instance.
     */
    protected $_temporaryToken;

    /**
     * @var EAuthServiceBase the authorization service instance.
     */
    protected $_token;


	/**
	 * @var string the unique identifier for the identity.
	 */
	protected $id;
	
	/**
	 * @var string the display name for the identity.
	 */
	protected $name;

    /**
     * @var integer authorization lifetime in seconds.
     */
    protected $_duration = 0;

	/**
	 * Constructor.
	 * @param EAuthServiceBase $service the authorization service instance.
	 */
	public function __construct($service = false) {
		$this->service = $service;
	}
	
	/**
	 * Authenticates a user based on {@link service}.
	 * This method is required by {@link IUserIdentity}.
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate() {
        // авторизация по сервису
        if ($this->service)
        {
            if ($this->service->isAuthenticated) {
                $this->id = $this->service->id;
                $this->name = $this->service->getAttribute('name');
                if (method_exists($this->service, 'getDuration'))
                    $this->_duration = $this->service->getDuration();

                $this->setState('id', $this->id);
                $this->setState('name', $this->name);
                $this->setState('service', $this->service->serviceName);
                $this->errorCode = self::ERROR_NONE;
            }
            else {
                $this->errorCode = self::ERROR_NOT_AUTHENTICATED;
            }
        }
		return !$this->errorCode;
	}

	/**
	 * Returns the unique identifier for the identity.
	 * This method is required by {@link IUserIdentity}.
	 * @return string the unique identifier for the identity.
	 */
	public function getId() {
		return $this->id;
	}

    public function getServiceAttributes()
    {
        return $this->service->getAttributes();
    }

	/**
	 * Returns the display name for the identity.
	 * This method is required by {@link IUserIdentity}.
	 * @return string the display name for the identity.
	 */
	public function getName() {
		return $this->name;
	}

    public function getDuration ()
    {
        return $this->_duration;
    }

	/**
	 * @return string a prefix for the name of the session variables storing eauth session data.
	 */
	protected function getStateKeyPrefix() {
		return '__gpor_auth_'.$this->getName().'__';
	}

	/**
	 * Stores a variable in eauth session.
	 * @param string $key variable name.
	 * @param mixed $value variable value.
	 * @param mixed $defaultValue default value. If $value===$defaultValue, the variable will be
	 * removed from the session.
	 * @see getState
	 */
	public function setState($key, $value, $defaultValue = null) {
		$session = Yii::app()->session;
		$key = $this->getStateKeyPrefix().$key;
		if($value === $defaultValue)
			unset($session[$key]);
		else
			$session[$key] = $value;
	}

	/**
	 * Returns a value indicating whether there is a state of the specified name.
	 * @param string $key state name.
	 * @return boolean whether there is a state of the specified name.
	 */
	public function hasState($key) {
		$session = Yii::app()->session;
		$key = $this->getStateKeyPrefix().$key;
		return isset($session[$key]);
	}

	/**
	 * Returns the value of a variable that is stored in eauth session.
	 * @param string $key variable name.
	 * @param mixed $defaultValue default value.
	 * @return mixed the value of the variable. If it doesn't exist in the session,
	 * the provided default value will be returned.
	 * @see setState
	 */
	public function getState($key, $defaultValue = null) {
		$session = Yii::app()->session;
		$key = $this->getStateKeyPrefix().$key;
		return isset($session[$key]) ? $session[$key] : $defaultValue;
	}

}