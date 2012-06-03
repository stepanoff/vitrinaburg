<?php
class VForumDiscussionComment extends VActiveRecord
{
    protected $discussionModel = 'VForumDiscussion';
    protected $userModel = 'VUser';

	public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'forum_comments';
    }

	public function scopes()
	{
        $res = parent::scopes();
        return array_merge($res, array(
		));
	}

    public function onSite($alias = 't')
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition'=>$alias.'.visible = '.self::VISIBLE_ON,
        ));
        return $this;
    }

    public function orderDefault($alias = 't')
    {
        $this->getDbCriteria()->mergeWith(array(
            'order'=>$alias.'.date ASC',
        ));
        return $this;
    }

    public function orderLast($alias = 't')
    {
        $this->getDbCriteria()->mergeWith(array(
            'order'=>$alias.'.date DESC',
        ));
        return $this;
    }

    public function getContent ()
    {
        return '<p>'.$this->content.'</p>';
    }

    public function relations()
    {
        $res = parent::relations();
        return array_merge($res, array(
            'discussionObj' => array(self::BELONGS_TO, $this->discussionModel, 'forum_discussion_id', 'joinType'=>'INNER JOIN'),
            'user' => array(self::BELONGS_TO, $this->userModel, 'user_id', 'joinType'=>'INNER JOIN'),
        ));
    }

    public function rules()
    {
        $res = parent::rules();
        return array_merge($res, array(
            array('content', 'required', 'message' => 'Напишите что-нибудь'),
        	array('date, user_id, content, forum_discussion_id', 'safe', 'on' => 'admin, user'),
		));
    }

    public function ImageValidator($attribute, $params) {
    }

    public function manyToManyRelations ()
    {
        $res = parent::manyToManyRelations();
        return array_merge($res, array(
        ));
    }

    public function attributeLabels()
    {
        $res = parent::rules();
        return array_merge($res, array(
        	'content' => 'Комментарий',
            'date' => 'Дата создания',
            'user_id' => 'Автор',
            'forum_discussion_id' => 'Тема',
        ));
    }



    protected function beforeValidate()
    {
        return parent::beforeValidate();
    }

	protected function beforeSave()
	{
        if ($this->isNewRecord)
            $this->date = time();

		return parent::beforeSave();
    }

    protected function afterFind()
    {
        return parent::afterFind();
    }

    protected function afterSave()
    {
    	return parent::afterSave();
    }
    
	protected function afterDelete()
	{
		parent::afterDelete();
	}

}