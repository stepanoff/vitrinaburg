<?php
/**
 * CustomOldGporService class file.
 *
 * @author Stepanoff <stenlex@gmail.com>
 */

/**
 * OldGpor provider class.
 */
class CustomOldGporService extends EAuthServiceBase implements IAuthService {
	
	protected $name = 'oldgpor';
	protected $title = 'Старый городской портал';
	protected $type = 'OldGpor';
	protected $jsArguments = array('popup' => false, 'actionType' => 'sendForm' );

	protected $client_id = '';
	protected $client_secret = '';
	protected $providerOptions = array(
		'authorize' => '',
		'refresh_info' => '',
	);

    public $formModelClass = 'GporAuthForm';
    protected $_form;
	
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
/*
 		if ($this->hasState('auth_token') && $this->getState('expires', 0) > time()) {
			$this->access_token = $this->getState('auth_token');
			$this->authenticated = true;
			return true;
		}
		else {
			$this->access_token = null;
			$this->authenticated = false;
			return false;
		}
		if ($this->hasState('uid')) {
			$this->uid = $this->getState('uid');
			return true;
		}
		else {
			$this->uid = null;
			return false;
		}
 */
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

            $info = (array)$this->makeRequest($this->providerOptions['authorize'], array(
                'query' => array(
                    'client_id' => $this->client_id,
                    'client_secret' => $this->client_secret,
                ),
                'data' => $form->attributes,
            ));

            if ($this->obtainResponse($info))
            {
                $this->authenticated = true;
            }
            else
            {
                $error = array (
                    'message' => 'Ошибка обработки данных. Попробуйте позже.',
                    'code' => 500,
                );
                throw new EAuthException($error['message'], $error['code']);
            }
        }
        else if (isset($_GET['code']) && isset($_GET['uid'])) {

            $signature = md5($this->client_id.$this->client_secret.$_GET['code']);

            $info = (array)$this->makeRequest($this->providerOptions['authorize'], array(
                'query' => array(
                    'client_id' => $this->client_id,
                    'signature' => $signature,
                    'uid' => (int) $_GET['uid'],
                ),
            ));

            if ($this->obtainResponse($info))
            {
                $this->authenticated = true;
            }
            else
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

    public function refreshInfo ($userData)
    {
        $info = (array)$this->makeRequest($this->providerOptions['refresh_info'], array(
            'query' => array(
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                //'uid' => $this->getId(),
                'uid' => $userData['serviceId'],
            ),
        ));

        if ($this->obtainResponse($info))
        {
            return true;
        }
        return false;
    }

    protected function obtainResponse ($info)
    {
        $info = $info['response'];
        $this->attributes['id'] = $info->uid;
        $this->attributes['name'] = $info->name . ($info->surname ? ' '.$info->surname : '');
        $this->attributes['url'] = $info->url;
        $this->attributes['username'] = $info->username;
        $this->attributes['gender'] = $info->sex == 1 ? 'M' : ($info->gender == 2 ? 'F' : '');
        $this->attributes['avatar'] = $info->photo;
        $this->attributes['photo'] = $info->photo_big;
        $this->attributes['email'] = $info->email;
        if (Yii::app()->getComponent('user'))
        {
            $this->setState('expires', $info->expires - 60);
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
        return 'ext.gporauth.views.services.oldGpor';
    }

    public function cancel($url = null) {
        $this->getComponent()->cancel(isset($url) ? $url : $this->getCancelUrl(), false);
    }

}