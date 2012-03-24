<?php
require_once(LIB_PATH . DS . 'redisServer' . DS . 'RedisServer.php');

class RemoveSessionTokensCommand extends CronCommand {

    const DAYS = 7;

    public function runProcess()
    {
        if (Yii::app()->params['dbDriver'] != 'redis')
            return false;

        $driver = new RedisGporAuthDbDriver(Yii::app()->params['redis_host'], Yii::app()->params['redis_port']);
        $flag = true;

        $maxTime = time() - (60*60*24*self::DAYS);
        while ($flag)
        {
            $res = $driver->removeLastQueueToken($maxTime);

            if (!$res)
            {
                $flag = false;
                break;
            }
        }


        return true;
    }

}