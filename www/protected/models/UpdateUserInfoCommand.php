<?php
require_once(LIB_PATH . DS . 'redisServer' . DS . 'RedisServer.php');

class UpdateUserInfoCommand extends CronCommand {

    public function runProcess()
    {
        if (Yii::app()->params['dbDriver'] != 'redis')
            return false;
        $driver = new RedisGporAuthDbDriver(Yii::app()->params['redis_host'], Yii::app()->params['redis_port']);
        $flag = true;
        $len = $driver->getServer()->send_command('llen', 'user_update_queue');

        $len = $driver->getServer()->send_command('llen', 'user_update_queue');

        if(!$len)
            $flag = false;

        while ($flag)
        {
            $lastId = $driver->getServer()->send_command('lindex', 'user_update_queue', 0);
            if (!$lastId)
            {
                $flag = false;
                break;
            }
            $user = $driver->findByPk($lastId);
            if ($user)
            {
                $service = $user['service'];
                $serviceClass = Yii::app()->eauth->getServiceClassName($service);
                if ($serviceClass)
                {
                    if (method_exists($serviceClass, 'refreshInfo'))
                    {
                        $class = Yii::app()->eauth->getIdentity($service);
                        if ($class->refreshInfo($user))
                        {
                            $data = $class->attributes;
                            foreach($user as $k=>$v)
                            {
                                if (isset($data[$k]))
                                    $user[$k] = $data[$k];
                            }
                            $driver->updateByPk($lastId, $user);

                        }
                    }
                }
            }
            $driver->getServer()->send_command('lrem', 'user_update_queue', '0',  $lastId);

        }


        return true;
    }

}