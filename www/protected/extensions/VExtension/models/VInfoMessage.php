<?php
/**
 * Файл с классом модели InfoMessage
 *
 * @author stepanoff
 * @since 17.12.2009
 */

/**
 * Модель сообщения мессенджера
 *
 * @author stepanoff
 * @version 1.0
 */
class VInfoMessage extends CActiveRecord
{
	public $to = null;
	public $read = null;
	public $show_once = null;
	
    /**
	 * Returns the static model of the specified AR class.
	 * The model returned is a static instance of the AR class.
	 * It is provided for invoking class-level methods (something similar to static class methods.)
     * @param string active record class name.
	 * @return CActiveRecord active record model instance.
	 */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Имя таблицы, к которой привязан данный класс
     * @return string Имя таблицы в базе данных
     */
    public function tableName()
    {
        return 'infomessages';
    }

    public function tableMessage2User()
    {
        return 'infomessage2user';
    }
    
	public function getUnread ($userId)
	{
		$connection = Yii::app()->db;
		$sql="SELECT `message_id` FROM `".$this->tableMessage2User()."` WHERE 
				`user_id`='".$userId."' AND 
				`read`='0' 
				";
		$command=$connection->createCommand($sql);
		$result  = $command->queryAll();
		$res = array();
		if ($result)
		{
			foreach ($result as $row)
				$res[] = $row['message_id'];
		}
		return $res;
	}    

	public function relations()
    {
        return array(
		);
    }

    protected function beforeSave()
    {
    	return parent::beforeSave();
    }
    
	public function rules()
	{
		return array(
			array('title, text, info_type, author, date, show_once, read, to, buttons, params', 'safe'),
		);
	}

	/**
	 * Записать запись о прочтении сообщения пользователем
	 */
	public function readBy($userId)
	{
		if (!$this->id)
			return false;
		$connection = Yii::app()->db;
		$sql="UPDATE `".$this->tableMessage2User()."` SET  
				`read`='1' WHERE 
				`user_id`='".$userId."' AND 
				`message_id`='".$this->id."' 
				";
		$command=$connection->createCommand($sql);
		$command->execute();
		return true;
	}

	/**
	 * Метод, вызываемый после сохранения записи
	 */
	protected function afterSave()
	{
		if ($this->isNewRecord)
		{
			$to = is_array($this->to)?$this->to:array($this->to);
			foreach ($to as $uid)
			{
				$connection = Yii::app()->db;
				$sql="INSERT INTO `".$this->tableMessage2User()."` SET  
				`message_id`='".$this->id."',
				`read`='".$this->read."',
				`show_once`='".$this->show_once."',
				`user_id`='".$uid."';";
				$command=$connection->createCommand($sql);
				$command->execute();
			}
		}
		return parent::afterSave();
    }

    /**
     * Метод, вызываемый после удаления записи
     * Удаляем рейтинги и комментарии.
     */
	protected function afterDelete()
	{
		// пользователей, которые должны получить сообщение
		
		parent::afterDelete();
	}
	
}
?>
