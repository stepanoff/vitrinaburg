<?php
class CustomInnerService extends EAuthServiceBase implements IAuthService {

    const EXPIRES_DAYS = 30;

	protected $name = 'inner';
	protected $title = 'Внутренняя авторизация';
	protected $type = 'inner';
	protected $jsArguments = array('popup' => false, 'actionType' => 'sendForm' );

    public $formModelClass = 'VAuthForm';
    protected $_form;
    protected $_rememberMe = false;
	
	protected $uid = null;

	protected function fetchAttributes() {
	}

	/**
	 * Save access token to the session.
	 * @param stdClass $token access token object.
	 */
	protected function saveAccess($token) {
		$this->setState('uid', $this->attributes['id']);
		$this->setState('expires', time() + $token->expires_in - 60);
		$this->uid = $this->attributes['id'];
	}
	
	/**
	 * Restore access token from the session.
	 * @return boolean whether the access token was successfuly restored.
	 */
	protected function restoreAccess() {
        return false;
	}

	/**
	 * Returns the error info from json.
	 * @param stdClass $json the json response.
	 * @return array the error array with 2 keys: code and message. Should be null if no errors.
	 */
	protected function fetchJsonError($json) {
		if (isset($json->error)) {
			return array(
				'code' => $json->error->code,
				'message' => $json->error->message,
			);
		}
		else
			return null;
	}


	/**
	 * Authenticate the user.
	 * @return boolean whether user was successfuly authenticated.
	 */
	public function authenticate() {
		if (isset($_POST[$this->formModelClass])) {
            $form = $this->getForm();
            $form->setAttributes($_POST[$this->formModelClass]);

            if (!$form->validate())
            {
                $error = array (
                    'message' => 'Укажите логин и пароль',
                    'code' => 500,
                );
                throw new EAuthException($error['message'], $error['code']);
            }

            $user = VUser::model()->byLogin($form->login)->find();
            $info = false;
            if ($user)
            {
                if ($user->password && $user->password == $form->password)
                {
                    $this->_rememberMe = $form->rememberMe;
                    $info = $this->obtainUser($user);
                    $this->authenticated = true;
                }
                else
                {
                    $error = array (
                        'message' => 'Логин или пароль указан неверно',
                        'code' => 500,
                    );
                    throw new EAuthException($error['message'], $error['code']);
                }
            }
            else
            {
                $error = array (
                    'message' => 'Логин или пароль указан неверно',
                    'code' => 500,
                );
                throw new EAuthException($error['message'], $error['code']);
            }

            if (!$info)
            {
                $error = array (
                    'message' => 'Ошибка обработки данных. Попробуйте позже.',
                    'code' => 500,
                );
                throw new EAuthException($error['message'], $error['code']);
            }
        }
        else if (isset($_GET['code']) && isset($_GET['uid'])) {

            // todo: авторизация по коду

            /*
            $signature = md5($this->client_id.$this->client_secret.$_GET['code']);


            if ($this->obtainResponse($info))
            {
                $this->authenticated = true;
            }
            else
            */
            {
                $error = array (
                    'message' => 'Ошибка обработки данных. Попробуйте позже.',
                    'code' => 500,
                );
                throw new EAuthException($error['message'], $error['code']);
            }
        }
		// Redirect to the authorization page
		else if (!$this->restoreAccess()) {
			// Use the URL of the current page as the callback URL.
			if (isset($_GET['redirect_uri'])) {
				$redirect_uri = $_GET['redirect_uri'];
			}
			else {
				$server = Yii::app()->request->getHostInfo();
				$path = Yii::app()->request->getUrl();
				$redirect_uri = $server.$path;
			}
            $url = $redirect_uri;
			Yii::app()->request->redirect($url);
		}

		return $this->getIsAuthenticated();
	}

    public function getDuration ()
    {
        $expires = $this->getState('expires');
        if ($expires)
        {
            $duration = $expires -time();
            if ($duration && $duration>0)
                return $duration;
        }
        return 0;
    }

    protected function obtainUser ($user)
    {
        $this->attributes['id'] = $user->id;
        $this->attributes['name'] = $user->name;
        $this->attributes['url'] = CHtml::normalizeUrl(array($this->getComponent()->getUserRoute(), 'id' => $user->id));
        $this->attributes['username'] = $user->username;
        $this->attributes['gender'] = $user->gender;
        $this->attributes['avatar'] = '';
        $this->attributes['photo'] = '';
        $this->attributes['email'] = $user->email;
        if (Yii::app()->getComponent('user'))
        {
            if ($this->_rememberMe)
                $this->setState('expires', time()+self::EXPIRES_DAYS*60*60*24);
        }
        return true;
    }

    public function getForm ()
    {
        if ($this->_form === null)
        {
            $className = $this->formModelClass;
            $this->_form = new $className;
        }
        return $this->_form;
    }

    public function getCustomTemplate()
    {
        return $this->getComponent()->getBaseTemplatePath ().'inner';
    }

    public function cancel($url = null) {
        $this->getComponent()->cancel(isset($url) ? $url : $this->getCancelUrl(), false);
    }

}