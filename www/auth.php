<?php
/*
 * redis authetication script
 */
date_default_timezone_set('Asia/Yekaterinburg');

$localConfig = @include(dirname(__FILE__) . '/localConfig/params.php');
$yiiDebug = (!empty($localConfig) && isset($localConfig['yiiDebug'])) ? $localConfig['yiiDebug'] : false;

$config=dirname(__FILE__).'/protected/config/main.php';

define('ROOT_PATH', dirname(__FILE__));
define('BASE_PATH', dirname(__FILE__). DS . '..');
define('FILES_PATH', dirname(__FILE__). DS . 'files');
define('LIB_PATH', dirname(__FILE__). DS . '..' . DS . 'lib');

defined('YII_DEBUG') or define('YII_DEBUG', $yiiDebug);
defined('YII_DEBUG_LOG') or define('YII_DEBUG_LOG', $yiiDebug);


$f = isset($_GET['f']) ? $_GET['f'] : false;

require_once(ROOT_PATH . DS . 'protected' . DS . 'extensions' . DS . 'gporauth' . DS . 'RedisGporAuthDbDriver.php');
$driver = new RedisGporAuthDbDriver ($localConfig['redis_host'], $localConfig['redis_port']);

switch ($f) {
    case 'checkIsAuth':
        $token = authCheckIsAuth($localConfig, $driver);
        if (!$token)
        {
            $res = array(
                'result' => false,
                'token' => false,
            );
        }
        else
        {
            $res = array(
                'result' => true,
                'token' => authGenerateTemporaryToken($localConfig, $token, $driver),
            );
        }

        if ($res['token'])
        {
?>
var GporAuth = {
    'result' : <?php echo $res['result'] ? 'true' : 'false'; ?>,
    't' : '<?php echo $res['token'] ? $res['token'] : ''; ?>',
    'onAuthCallback' : false
}
GporAuth.run = function (callbackFunc) {
         if (callbackFunc)
             this.onAuthCallback = callbackFunc;
         if (GporAuth.result) {
            this.onAuthCallback(GporAuth.t);
         }
         return false;
}
GporAuth.show = function (callbackFunc) {
    GporAuth.run();
    return false;
}
<?php
        }
        else
        {
            $params = array (
                'width' => 600,
                'height' => 400,
                'returnUrl' => '',
                'redirectUrl' => '',
                'providers_set' => '',
            );
            foreach ($params as $k => $v)
            {
                if (isset($_GET[$k]))
                    $params[$k] = $_GET[$k];
            }
            $params['service_host'] = 'http://'.$localConfig['domain'].'/';
            include(ROOT_PATH.'/js/auth-widget.js');
            ?>

GporAuth.addEvent(window, 'load', GporAuth.init(<?php echo json_encode($params); ?>));
            <?
        }
            //echo authJsonEncode($res);
        die();

        break;

    case 'checkAuthToken':
        $client = isset($_GET['client_id']) ? $_GET['client_id'] : false;
        $secret = isset($_GET['client_secret']) ? $_GET['client_secret'] : false;
        $ttoken = isset($_GET['auth_token']) ? $_GET['auth_token'] : false;
        try {
            if (checkClientAuth($client, $secret, $localConfig))
            {
                $token = $driver->findTokenByTtoken($ttoken);
                //$driver->removeTemporaryToken($ttoken);
                if (!$token)
                    throw new AuthException('Temporary token not found. Authentication failed.', 500);
                $res = array(
                    'success' => true,
                    'token' => $token,
                );
                echo json_encode($res);
                die();
            }
        }
        catch (AuthException $e)
        {
            $error = $e->getMessage();
            $code = $e->getCode();
            $data = array(
                    'error' => array (
                        'code' => $code,
                        'message' => $error,
                    )
            );
            echo json_encode($data);
            die();
        }

        break;

    case 'checkToken':
        $client = isset($_GET['client_id']) ? $_GET['client_id'] : false;
        $secret = isset($_GET['client_secret']) ? $_GET['client_secret'] : false;
        $token = isset($_GET['token']) ? $_GET['token'] : false;
        try {
            if (checkClientAuth($client, $secret, $localConfig))
            {
                $data = $driver->findByToken($token);

                $uid = $data['id'];

                if (!$uid)
                    throw new AuthException('Token not found. Authentication failed.', 500);
                elseif ($data['duration'] && $data['duration'] < time())
                {
                    throw new AuthException('Token expired. Authentication failed.', 500);
                }

                $res = array(
                    'success' => true,
                    'userData' => $driver->findByPk($uid),
                    'uid' => $uid,
                );
                echo json_encode($res);
                die();
            }
        }
        catch (AuthException $e)
        {
            $error = $e->getMessage();
            $code = $e->getCode();
            $data = array(
                    'error' => array (
                        'code' => $code,
                        'message' => $error,
                    )
            );
            echo json_encode($data);
            die();
        }
        break;

    default:

        // todo: 404
        break;
}

/*
 * functions
 */
function checkClientAuth ($client, $secret, $config)
{
    foreach ($config['clients'] as $_client)
    {
        if ($_client['login'] == $client && $_client['password'] == $secret)
        {
            return true;
        }
    }

    throw new AuthException('Client authorization failed.', 500);
    return false;

}

function authCheckIsAuth($localConfig, $driver) {

		$cookie=isset($_COOKIE[$localConfig['sessionPrefix']]) ? $_COOKIE[$localConfig['sessionPrefix']] : false;

        if($cookie && !empty($cookie))
        {
            $data = $driver->findByToken($cookie);

			if(is_array($data) && isset($data['id']) && isset($data['username']))
			{
                if (!$data['duration'] || ($data['duration'] && $data['duration'] > time()) )
                    return $cookie;
                elseif ($data['duration'] && $data['duration'] < time())
                {
                    setcookie($localConfig['sessionPrefix'], $cookie, 0, '/', '.'.$localConfig['domain']);
                    $driver->addTokenToQueue($cookie);
                    return $cookie;
                }
			}
            else
            {
                $driver->removeToken($cookie);
            }
		}
        // $this->changeIdentity(null,null,array());
        return false;
}

function authGenerateTemporaryToken ($localConfig, $token, $driver)
{
    $ttoken = md5($localConfig['token_secret'].mktime().rand(0, 1000000));
    if ($driver->addTemporaryToken($token, $ttoken))
        return $ttoken;
    return false;

}

class AuthException extends Exception
{
}
?>