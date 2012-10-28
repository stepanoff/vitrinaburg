<?php
/**
 * RedisGporAuthDbDriver interface
 *
 * @author stepanoff <stenlex@gmail.com>
 * @since 1.0
 */
require_once (LIB_PATH. DS . 'redisServer' . DS . 'RedisServer.php');
require_once ('IGporAuthDbDriver.php');

class RedisGporAuthDbDriver implements IGporAuthDbDriver
{
    private $_server = null;

    public static function getUserFileds()
    {
        return array('id', 'username', 'name', 'avatar', 'photo', 'service', 'serviceId', 'email', 'gender', 'url', 'updated');
    }

    public function __construct ($host = false, $port = false)
    {
        $host = $host ? $host : Yii::app()->params['redis_host'];
        $port = $port ? $port : Yii::app()->params['redis_port'];
        $this->_server = new Jamm\Memory\RedisServer($host, $port);
    }

    public function getServer()
    {
        return $this->_server;
    }

    public function findByService($service, $serviceId)
    {
        $uid = $this->_server->send_command('get', 'service:'.$service.':'.md5($serviceId));
        if ($uid)
        {
            return $this->findByPk($uid);
        }
        return false;
    }

	public function findByToken($token)
    {
        $res = $this->_server->send_command('hmget', 'token:'.$token, 'id', 'expire');
        list($uid, $expire) = $res;
        if ($uid)
        {
            $userInfo = $this->findByPk($uid);
            if ($userInfo)
            {
                $userInfo['duration'] = $expire;
                return $userInfo;
            }
        }
        return false;
    }

    public function findByPk($id)
    {
        //$res = $this->_server->send_command('hmget', 'user:'.$id, 'uid', 'username', 'name', 'avatar', 'photo', 'service', 'serviceId', 'email', 'gender', 'url', 'updated');
        $args = array_merge(array('hmget', 'user:'.$id), $this->getUserFileds());
        $res = call_user_func_array(array( $this->_server, "send_command"), $args);
        $userInfo = array();
        $i = 0;
        foreach ($this->getUserFileds() as $key)
        {
            $userInfo[$key] = $res[$i];
            $i++;
        }

        if ($id)
        {
            return $userInfo;
        }
        return false;
    }

	public function addUser($data)
    {
        $id = $this->_server->send_command('incr', 'global:nextUserId');
        $fields = array();

        foreach ($this->getUserFileds() as $key)
        {
            $fields[] = $key;
            if ($key == 'id')
                $fields[] = $id;
            elseif ($key == 'updated')
                $fields[] = date ('Y-m-d G:i:s');
            else
                $fields[] = isset($data[$key]) ? $data[$key] : '';
        }

        $res = $this->_server->send_command('set', 'service:'.($data['service'].':'.md5($data['serviceId']) ), $id);
        if ($res)
        {
            $args = array_merge(array('hmset', 'user:'.$id), $fields);
            $res = call_user_func_array(array( $this->_server, "send_command"), $args);
        }
        if ($res)
            return $id;
        return false;
    }

    public function updateByPk($id, $data)
    {
        $fields = array();

        foreach ($this->getUserFileds() as $key)
        {
            $fields[] = $key;
            if ($key == 'id')
                $fields[] = $id;
            elseif ($key == 'updated')
                $fields[] = date ('Y-m-d G:i:s');
            else
                $fields[] = isset($data[$key]) ? $data[$key] : '';
        }
        $args = array_merge(array('hmset', 'user:'.$id), $fields);
        $res = call_user_func_array(array( $this->_server, "send_command"), $args);
        if ($res)
            return true;
        return false;
    }

	public function addToken($userId, $tokenValue, $duration = 0)
    {
        $expire = $duration ? (time() + $duration) : 0;
        $res = $this->_server->send_command('hmset', 'token:'.$tokenValue, 'id', $userId, 'expire', $expire, 'created', time() );
        // очередь токенов на удаление
        if (!$duration)
            $res = $this->_server->send_command('rpush', 'token_queue', $tokenValue);
        else
            $res = $this->_server->send_command('rpush', 'token_storage', $tokenValue);
        if ($res)
            return true;
        return false;
    }

    public function addTemporaryToken($realToken, $tokenValue)
    {
        $res = $this->_server->send_command('set', 'ttoken:'.$tokenValue, $realToken );
        if ($res)
            return true;
        return false;
    }

    public function removeTemporaryToken($token)
    {
        $res = $this->_server->send_command('del', 'ttoken:'.$token );
        if ($res)
            return true;
        return false;
    }

    public function removeToken($token)
    {
        $res = $this->_server->send_command('del', 'token:'.$token );
        if ($res)
            return true;
        return false;
    }

    public function findTokenByTtoken ($ttoken)
    {
        $res = $this->_server->send_command('get', 'ttoken:'.$ttoken );
        if ($res)
            return $res;
        return false;
    }

    public function findUidByToken ($token)
    {
        $res = $this->_server->send_command('hmget', 'token:'.$token, 'id', 'expire' );
        if ($res && $res[0])
            return $res[0];
        return false;
    }

    /*
     * Redis methods
     */
    public function findToken ($token)
    {
        $res = $this->_server->send_command('hmget', 'token:'.$token, 'id', 'expire', 'created' );
        if ($res && $res[0])
            return array(
                'id' => $res[0],
                'expire' => $res[1],
                'created' => isset($res[2]) ? $res[2] : 0,
            );
        return false;
    }

    public function addTokenToQueue($token)
    {
        $res = false;
        if ($tokenData = $this->findToken($token))
        {
            $res = $this->_server->send_command('hmset', 'token:'.$token, 'id', $tokenData['id'], 'expire', '0', 'created', $tokenData['created'] );
            $res = $this->_server->send_command('rpush', 'token_queue', $token );
        }
        return $res ? true : false;
    }

    /*
     * Добавление пользователей в очередь на обновление
     */
    public function addUserToQueue($id)
    {
        if ($this->findByPk($id))
        {
            $res = $this->_server->send_command('rpush', 'user_update_queue', $id );
        }
        return $res ? true : false;
    }

    /*
     * Удаляем самый старый сессионный токен если он старше указанного времени
     */
    public function removeLastQueueToken ($maxTime)
    {
        $length = $this->_server->send_command('llen', 'token_queue');
        if ($length)
        {
            $lastToken = $this->_server->send_command('lindex', 'token_queue', 0);
            if ($lastToken)
            {
                $token = $this->findToken($lastToken);
                if ($token)
                {
                    if ($token['created'] < $maxTime)
                    {
                        $this->removeToken($lastToken);
                        $this->_server->send_command('lpop', 'token_queue');
                        return true;
                    }
                }
                else
                {
                    $this->_server->send_command('lpop', 'token_queue');
                    return true;
                }

            }
        }
        return false;
    }

}
?>