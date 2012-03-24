<?php
require_once(LIB_PATH . DS . 'redisServer' . DS . 'RedisServer.php');

class RemoveStorageTokensCommand extends CronCommand {

    const DAYS = 1; // на сколько дней должна протухнуть кука прежде чем ее удляем

    public function runProcess()
    {
        if (Yii::app()->params['dbDriver'] != 'redis')
            return false;

        $driver = new RedisGporAuthDbDriver(Yii::app()->params['redis_host'], Yii::app()->params['redis_port']);

        $length = $driver->getServer()->send_command('llen', 'token_storage');

        if ($length)
        {
            $n = 0;
            $maxTime = time() - (60*60*24*self::DAYS);
            for ($i = 0; $i < $length; $i++)
            {
                $token =  $driver->getServer()->send_command('lindex', 'token_storage', $n);
                if (!$token)
                {
                    $res =  $driver->getServer()->send_command('lpop', 'token_storage');
                    continue;
                }

                $tokenData = $driver->findToken($token);

                // если протухла - удаляем
                $removed = false;
                if ($tokenData)
                {
                    if ($tokenData['expire'] < $maxTime)
                    {
                        $driver->removeToken($token);
                        $removed = true;
                    }
                }
                else
                    $removed = true;

                // если не найдена сама кука или кука удалена -удаляем из списка
                if ($removed)
                {
                    $token =  $driver->getServer()->send_command('lrem', 'token_storage', '1',  $token);
                }
                else
                    $n++;
            }
        }

        return true;
    }

}