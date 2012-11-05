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
        $offset = 0;

        $criteria = new CDbCriteria();
        $criteria->order = '`id`';
        $criteria->limit = self::MAX;
        $criteria->addCondition('`id` > :id AND `last_name` = "new service"');
        $criteria->params = array(
            ':id' => $lastId
        );
        $users = Yii::app()->db->commandBuilder->createFindCommand('users', $criteria)->queryAll();

        foreach ($users as $user) {
            $criteria = new CDbCriteria();
            $criteria->addCondition('`name` = :username');
            $criteria->params = array(
                ':username' => $user['username']
            );
            $newUser = Yii::app()->db->commandBuilder->createFindCommand('users', $criteria)->queryRow();
            if ($newUser) {
                echo $user['id'].' => '.$newUser['id']."\n";
                /*
                $criteria = new CDbCriteria();
                $criteria->addCondition('`id` = :id');
                $criteria->params = array(
                    ':id' => $user['id']
                );
                Yii::app()->db->commandBuilder->createUpdateCommand('users', array('id'=>$newUser['id']),$criteria)->execute();
                */
            }
        }

	}

}