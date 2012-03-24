<?php
class SiteController extends Controller
{
    public $layout='column1';

    public function actionIndex()
    {
        $options = array(
            'iframe' => false,
            'action' => 'site/login',
            'popup' => isset($_GET['iframe']) ? $_GET['iframe'] : false,
            'width' => 600,
            'height' => 400,
            'providers_set' => false,
            'returnUrl' => '',
            'redirectUrl' => '',
        );

        foreach ($options as $k => $v)
        {
            if (isset($_GET[$k]))
                $options[$k] = $_GET[$k];
        }

        $userData = false;
        if (!Yii::app()->user->isGuest)
        {
            $userData = Yii::app()->user->getInfo();
        }

        $this->render('widget', array('userData'=>$userData, 'options' => $options));
    }

	public function actionLogout() {
        $redirectUrl = Yii::app()->user->returnUrl;
        Yii::app()->user->logout();
        if (isset($_GET['returnUrl']) && !empty($_GET['returnUrl']))
             $redirectUrl = $_GET['returnUrl'];
        Yii::app()->request->redirect($redirectUrl);
        Yii::app()->end();
	}

	public function actionLogin() {

        // todo: без js авторизует, но не редиректит назад
        $ajax = Yii::app()->request->isAjaxRequest;

		$service = Yii::app()->request->getQuery('service');
		if (isset($service)) {
            try {
                $authIdentity = Yii::app()->eauth->getIdentity($service);
                if ($authIdentity->authenticate()) {

                    if (isset($_GET['nopopup']))
                    {
                        $rUrl = isset($_GET['redirect_uri']) ? $_GET['redirect_uri'] : Yii::app()->user->returnUrl;
                        $rUrl .= (strstr('?', $rUrl) ? '&' : '?').'error='.urlencode($service);
                        $authIdentity->cancelUrl = $rUrl;
                    }
                    else
                        $authIdentity->cancelUrl = '#error:'.$service;

                    $identity = new GporAuthUserIdentity($authIdentity);

                    // успешная авторизация
                    $rememberMe = true;
                    if ($identity->authenticate()) {
                        Yii::app()->user->login($identity, $rememberMe);
                        if (isset($_GET['nopopup']))
                        {
                            $returnUrl = isset($_GET['returnUrl']) ? $_GET['returnUrl'] : '';
                            $rUrl = isset($_GET['redirectUrl']) ? $_GET['redirectUrl'] : Yii::app()->user->returnUrl;
                            $rUrl .= (strstr('?', $rUrl) ? '&' : '?').'auth_token='.urlencode(Yii::app()->user->generateTemporaryToken());
                            $rUrl .= '&returnUrl='.urlencode($returnUrl);
                            $authIdentity->redirectUrl = $rUrl;
                        }
                        else
                            $authIdentity->redirectUrl = '#token:'.Yii::app()->user->generateTemporaryToken();

                        if ($ajax)
                        {
                            $data = array ('success' => true, 'redirect_url' => $authIdentity->getRedirectUrl());
                            echo CJSON::encode($data);
                            die();
                            Yii::app()->end();
                        }
                        else
                        {
                            // специальное перенаправления для корректного закрытия всплывающего окна
                            $authIdentity->redirect();
                        }
                    }
                    else {
                        // закрытие всплывающего окна и перенаправление на cancelUrl
                        $authIdentity->cancel();
                    }
                }
                die();
                // авторизация не удалась, перенаправляем на страницу входа
                if (!$ajax)
                    $this->redirect(array('site/login'));
                else
                    Yii::app()->eauth->cancel(false);
            }
            catch (EAuthException $e)
            {
                $error = $e->getMessage();
                $code = $e->getCode();
                if ($ajax)
                {
                    $data = array(
                        'error' => array (
                            'code' => $code,
                            'message' => $error,
                        )
                    );
                    echo CJSON::encode($data);
                    die();
                    Yii::app()->end();
                }
                else
                {
                    $data = array(
                        'error' => array (
                            'code' => $code,
                            'message' => $error,
                        )
                    );
                    if (isset($_GET['redirectUrl']))
                    {
                        $url = $_GET['redirectUrl'];
                        $url .= (strstr('?', $url) ? '&' : '?').'error='.urlencode($data['error']['message']);
                        $this->redirect($url);
                        die();
                        Yii::app()->end();
                    }
                }
            }
		}

        $data = array(
            'error' => array (
                'code' => 500,
                'message' => 'Ошибка аутентификации. Сервис не найден',
            )
        );
        echo CJSON::encode($data);
        die();
        Yii::app()->end();
	}

    protected function checkClientAuth ($client, $secret)
    {
        $component = Yii::app()->eauth;
        foreach (Yii::app()->params['clients'] as $_client)
        {
            if ($_client['login'] == $client && $_client['password'] == $secret)
            {
                return true;
            }
        }

        throw new EAuthException(Yii::t('eauth', 'Client authorization failed.', array(), 'en'), 500);
        return false;

    }


    /*
     * добавить пользователя на обновление профиля
     */
    public function actionAddUserToUpdateQueue ()
    {
        $client = isset($_GET['client_id']) ? $_GET['client_id'] : false;
        $secret = isset($_GET['client_secret']) ? $_GET['client_secret'] : false;
        $uid = isset($_GET['uid']) ? $_GET['uid'] : false;
        try {
            if (!$uid)
                throw new EAuthException(Yii::t('eauth', 'user not found.', array(), 'en'), 500);

            if ($this->checkClientAuth($client, $secret))
            {
                $driverClass = Yii::app()->user->dbDriver;
                $driver = new $driverClass;

                $res = $driver->addUserToQueue($uid);
                if (!$res)
                {
                    throw new EAuthException(Yii::t('eauth', 'user not found.', array(), 'en'), 500);
                }

                $res = array(
                    'success' => true,
                    'id' => $uid,
                );
                echo CJSON::encode($res);
                die();
                Yii::app()->end();
            }
        }
        catch (EAuthException $e)
        {
            $error = $e->getMessage();
            $code = $e->getCode();
            $data = array(
                    'error' => array (
                        'code' => $code,
                        'message' => $error,
                    )
            );
            echo CJSON::encode($data);
            die();
            Yii::app()->end();
        }
    }

   /*
     * получение информации о пользователе по id и имени сервиса
     */
    public function actionGetUserInfo ()
    {
        $client = isset($_GET['client_id']) ? $_GET['client_id'] : false;
        $secret = isset($_GET['client_secret']) ? $_GET['client_secret'] : false;
        $service = isset($_GET['service']) ? $_GET['service'] : false;
        $serviceId = isset($_GET['serviceId']) ? $_GET['serviceId'] : false;
        try {
            if ($this->checkClientAuth($client, $secret))
            {
                $driverClass = Yii::app()->user->dbDriver;
                $driver = new $driverClass;

                $user = $driver->findByService($service, $serviceId);
                if (!$user)
                {
                    throw new EAuthException(Yii::t('eauth', 'user not found.', array(), 'en'), 500);
                }

                $res = array(
                    'success' => true,
                    'userData' => Yii::app()->user->getInfoById($user['id']),
                    'uid' => $user['id'],
                );
                echo CJSON::encode($res);
                die();
                Yii::app()->end();
            }
        }
        catch (EAuthException $e)
        {
            $error = $e->getMessage();
            $code = $e->getCode();
            $data = array(
                    'error' => array (
                        'code' => $code,
                        'message' => $error,
                    )
            );
            echo CJSON::encode($data);
            die();
            Yii::app()->end();
        }
    }

   /*
     * добавление пользователя в базу (использовался для импорта базы с 66-ого)
     */
    public function actionAddUser ()
    {
        $client = isset($_GET['client_id']) ? $_GET['client_id'] : false;
        $secret = isset($_GET['client_secret']) ? $_GET['client_secret'] : false;
        $service = isset($_GET['service']) ? $_GET['service'] : false;
        try {
            if ($this->checkClientAuth($client, $secret))
            {
                $userData = $_POST;
                if (!$userData)
                    throw new EAuthException(Yii::t('eauth', 'User data empty.', array(), 'en'), 500);

                $driverClass = Yii::app()->user->dbDriver;
                $driver = new $driverClass;

                $id = false;
                if (!$driver->findByService($service, $userData['serviceId']))
                {
                    $id = $driver->addUser($userData);
                    if (!$id)
                        throw new EAuthException(Yii::t('eauth', 'save user operation failed.', array(), 'en'), 500);
                }
                else
                    throw new EAuthException(Yii::t('eauth', 'User already exists.', array(), 'en'), 500);

                $res = array(
                    'success' => true,
                    'id' => $id,
                );
                echo CJSON::encode($res);
                die();
                Yii::app()->end();
            }
        }
        catch (EAuthException $e)
        {
            $error = $e->getMessage();
            $code = $e->getCode();
            $data = array(
                    'error' => array (
                        'code' => $code,
                        'message' => $error,
                    )
            );
            echo CJSON::encode($data);
            die();
            Yii::app()->end();
        }
    }

}