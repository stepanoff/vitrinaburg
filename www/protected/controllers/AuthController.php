<?php

class AuthController extends Controller
{
    public $layout='column1';

    /*
     * если пользователь авторизован на сайте, отправляем сессионный токен
     */
    public function actionCheckIsAuth()
    {
        if (Yii::app()->user->isGuest)
        {
            $res = array(
                'result' => false,
            );
        }
        else
        {
            $res = array(
                'result' => true,
                'token' => Yii::app()->user->generateTemporaryToken(),
            );
        }
?>
var checkIsAuth = {
    'result' : <?php echo $res['result'] ? 'true' : 'false'; ?>,
    't' : '<?php echo $res['token'] ? $res['token'] : ''; ?>'
}
checkIsAuth.run = function (callbackFunc) {
         if (checkIsAuth.result) {
            callbackFunc(checkIsAuth.t);
         }
         return false;
}
<?php
        //echo CJSON::encode($res);
        die();
        Yii::app()->end();
    }

    /*
     * обращение для получения данных о пользователе по сессионному токену
     */
    public function actionCheckToken()
    {
        $client = isset($_GET['client_id']) ? $_GET['client_id'] : false;
        $secret = isset($_GET['client_secret']) ? $_GET['client_secret'] : false;
        $token = isset($_GET['token']) ? $_GET['token'] : false;
        try {
            if ($this->checkClientAuth($client, $secret))
            {
                $uid = Yii::app()->user->findUidByToken($token);

                if (!$token)
                    throw new EAuthException(Yii::t('eauth', 'Token not found. Authentication failed.', array(), 'en'), 500);
                $res = array(
                    'success' => true,
                    'userData' => Yii::app()->user->getInfoById($uid),
                    'uid' => $uid,
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
     * обращение для получения сессионного токена по временному токену
     */
    public function actionCheckAuthToken ()
    {
        $client = isset($_GET['client_id']) ? $_GET['client_id'] : false;
        $secret = isset($_GET['client_secret']) ? $_GET['client_secret'] : false;
        $ttoken = isset($_GET['auth_token']) ? $_GET['auth_token'] : false;
        try {
            if ($this->checkClientAuth($client, $secret))
            {
                $token = Yii::app()->user->findTokenByTtoken($ttoken);
                Yii::app()->user->removeTemporaryToken($ttoken);
                if (!$token)
                    throw new EAuthException(Yii::t('eauth', 'Temporary token not found. Authentication failed.', array(), 'en'), 500);
                $res = array(
                    'success' => true,
                    'token' => $token,
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