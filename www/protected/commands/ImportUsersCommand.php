<?php
/*
 * Синхронизация баз старых и новых юзеров
 */
class ImportUsersCommand extends CConsoleCommand
{
    const MAX = 50;
    protected $_log;

	public function run($args)
	{
        // всем старым юзерам, которые авторизовывались по новой схеме ставим id из новой таблицы
        $lastId = 0;

        $criteria = new CDbCriteria();
        $criteria->order = '`id`';
        $criteria->limit = self::MAX;
        $criteria->addCondition('`id` > :id AND `last_name` = "new service"');
        $criteria->params = array(
            ':id' => $lastId
        );
        $users = Yii::app()->db->commandBuilder->createFindCommand('users', $criteria)->queryAll();

        while ($users) {
            foreach ($users as $user) {
                $lastId = $user['id'];
                $criteria = new CDbCriteria();
                $criteria->addCondition('`name` = :username');
                $criteria->params = array(
                    ':username' => $user['username']
                );
                $newUser = Yii::app()->db->commandBuilder->createFindCommand('vusers', $criteria)->queryRow();
                if ($newUser && $newUser['id'] != $user['id']) {
                    echo $user['id'].' => '.$newUser['id']."\n";

                    $criteria = new CDbCriteria();
                    $criteria->addCondition('`id` = :id');
                    $criteria->params = array(
                        ':id' => $user['id']
                    );
                    Yii::app()->db->commandBuilder->createUpdateCommand('users', array('id'=>$newUser['id']),$criteria)->execute();

                    $criteria = new CDbCriteria();
                    $criteria->addCondition('`user_id` = :id');
                    $criteria->params = array(
                        ':id' => $user['id']
                    );
                    Yii::app()->db->commandBuilder->createUpdateCommand('users_roles', array('user_id'=>$newUser['id']),$criteria)->execute();

                    $criteria = new CDbCriteria();
                    $criteria->addCondition('`user_id` = :id');
                    $criteria->params = array(
                        ':id' => $user['id']
                    );
                    Yii::app()->db->commandBuilder->createUpdateCommand('user_profiles', array('user_id'=>$newUser['id']),$criteria)->execute();
                }
            }

            $criteria = new CDbCriteria();
            $criteria->order = '`id`';
            $criteria->limit = self::MAX;
            $criteria->addCondition('`id` > :id AND `last_name` = "new service"');
            $criteria->params = array(
                ':id' => $lastId
            );
            $users = Yii::app()->db->commandBuilder->createFindCommand('users', $criteria)->queryAll();
        }

        
        // всех старых юзеров, которых нет среди новых переносим в новых
        $lastId = 0;

        $criteria = new CDbCriteria();
        $criteria->order = '`id`';
        $criteria->limit = self::MAX;
        $criteria->addCondition('`id` > :id AND `last_name` != "new service"');
        $criteria->params = array(
            ':id' => $lastId
        );
        $users = Yii::app()->db->commandBuilder->createFindCommand('users', $criteria)->queryAll();

        while ($users) {
            foreach ($users as $user) {
                $lastId = $user['id'];
                if (!$user['username'] || !$user['open_pass'])
                    continue;

                $criteria = new CDbCriteria();
                $criteria->addCondition('`login` = :username AND `service` = "inner"');
                $criteria->params = array(
                    ':username' => $user['username']
                );
                $newUser = Yii::app()->db->commandBuilder->createFindCommand('vusers', $criteria)->queryRow();
                if (!$newUser) {
                    echo $user['id']."\n";
                    $values = array(
                        'id' => $user['id'],
                        'service' => 'inner',
                        'serviceId' => $user['id'],
                        'email' => $user['email'],
                        'username' => $user['username'],
                        'updated' => date('Y-m-d G:i:s'),
                        'login' => $user['username'],
                        'password' => $user['open_pass'],
                    );

                    Yii::app()->db->commandBuilder->createInsertCommand('vusers', $values)->execute();
                }
            }

            $criteria = new CDbCriteria();
            $criteria->order = '`id`';
            $criteria->limit = self::MAX;
            $criteria->addCondition('`id` > :id AND `last_name` != "new service"');
            $criteria->params = array(
                ':id' => $lastId
            );
            $users = Yii::app()->db->commandBuilder->createFindCommand('users', $criteria)->queryAll();
        }


	}


    protected function oldUserMigration () {
        $c = Yii::app()->user;

        $criteria = new CDbCriteria(array(
            'order' => '`id` ASC',
        ));
        $users = Yii::app()->db->commandBuilder->createFindCommand('users', $criteria)->queryAll();
        foreach ($users as $user)
        {
            $userExists = VUser::model()->findByPk($user['id']);
            if ($userExists)
                continue;

            $password = false;
            if (!empty($user['open_pass']))
                $password = $user['open_pass'];
            else
            {
                $criteria = new CDbCriteria(array(
                    'condition' => '`user` = "'.$user['id'].'"',
                ));
                $client = Yii::app()->db->commandBuilder->createFindCommand('obj_client', $criteria)->queryRow();
                if ($client)
                {
                    $password = $client['pass'];
                }
            }

            if (!$password)
                continue;

            $username = $user['username'];
            $name = $user['name'].(!empty($user['last_name']) ? ' '.$user['last_name'] : '');
            $gender = '';
            $avatar = '';
            $photo = '';
            $birthday = '';

            $criteria = new CDbCriteria(array(
                'condition' => '`user_id` = "'.$user['id'].'"',
            ));
            $profile = Yii::app()->db->commandBuilder->createFindCommand('user_profiles', $criteria)->queryRow();
            if ($profile)
            {
                $username = $profile['name'];
                $name = $profile['fullname'];
                $gender = $gender == 1 ? 'm' : ($gender == 2 ? 'f' : '');
                $avatar = $user['avatar'];
                $photo = $user['photo'];
                $birthday = ($user['day_birth'] && $user['month_birth']) ? $user['day_birth'].'.'.$user['month_birth'] . ($user['year_birth'] ? '.'.$user['year_birth'] : '') : '';
            }

            /*
            $roleId = 1;
            $criteria = new CDbCriteria(array(
                'condition' => '`user_id` = "'.$user['id'].'"',
            ));
            $role = Yii::app()->db->commandBuilder->createFindCommand('user_roles', $criteria)->queryRow();
            if ($role)
            {
                $roleId = $role['role_id'];
            }
            */

            $newUser = new VUser;
            $newUser->id = $user['id'];
            $newUser->name = $name;
            $newUser->service = '';
            $newUser->serviceId = $user['id'];
            $newUser->avatar = $avatar;
            $newUser->email = $user['email'];
            $newUser->username = $username;
            $newUser->gender = $gender;
            $newUser->url = '';
            $newUser->photo = $photo;
            $newUser->updated = date ('Y-m-d 00:00:00');
            $newUser->login = $user['username'];
            $newUser->password = $password;
            $newUser->birthday = $birthday;

            $newUser->save();
        }

        echo 'migration completed';
        die();

    }

}