<?php
class VMysqlAuthDbDriver implements IVAuthDbDriver
{
    public $userModel = 'VUser';
    public $tokenModel = 'VToken';

    public function findByService($service, $serviceId)
    {
        $user = CActiveRecord::model($this->userModel)->byService($service, $serviceId)->find();
        if ($user)
               return $user->attributes;
        return false;
    }

	public function findByToken($token)
    {
        $token = CActiveRecord::model($this->tokenModel)->byToken($token)->find();
        if ($token)
        {
            $user = CActiveRecord::model($this->userModel)->findByPk($token->userId);
            if ($user)
            {
                $res = $user->attributes;
                $res['duration'] = $token->expire;
                return $res;
            }
        }
        return false;
    }

    public function findByPk($id)
    {
        $user = CActiveRecord::model($this->userModel)->findByPk($id);
        if ($user)
        {
            $res = $user->attributes;
            return $res;
        }
    }

	public function addUser($data)
    {
        $className = $this->userModel;
        $user = new $className;
        unset($data['id']);
        $user->setAttributes($data);
        $user->updated = date ('Y-m-d G:i:s');
        if ($user->save())
            return $user->id;
        return false;
    }

    public function updateByPk($id, $data)
    {
        $user = CActiveRecord::model($this->userModel)->findByPk($id);
        if (!$user)
            return false;
        unset($data['id']);
        $user->setAttributes($data);
        $user->updated = date ('Y-m-d G:i:s');
        if ($user->save())
            return true;
        return false;
    }

	public function addToken($userId, $tokenValue, $duration = 0)
    {
        $className = $this->tokenModel;
        $token = new $className;
        $token->userId = $userId;
        $token->token = $tokenValue;
        $token->expire = time() + $duration;
        if ($token->save())
            return true;
        return false;
    }

    public function removeToken($token)
    {
        $token = CActiveRecord::model($this->tokenModel)->byToken($token)->find();
        if ($token)
        {
            if ($token->delete())
                   return true;
        }
        return false;
    }

    public function findUidByToken ($token)
    {
        $token = CActiveRecord::model($this->tokenModel)->byToken($token)->find();

        if ($token)
            return $token->userId;
        return false;
    }

}
?>