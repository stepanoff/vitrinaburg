<?php
class VForumDiscussionComment extends VActiveRecord
{
    const TAG_QUOTE = 'quote';
    const TAG_BOLD = 'b';
    const TAG_ITALIC = 'i';

    protected $discussionModel = 'VForumDiscussion';
    protected $userModel = 'VUser';

	public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function getTags ()
    {
        return array (
            self::TAG_QUOTE => 'blockquote',
            self::TAG_BOLD => 'b',
            self::TAG_ITALIC => 'i',
        );
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

    public function byIds ($ids, $alias = 't')
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition'=>$alias.'.id IN ('.implode(',',$ids).')',
        ));
        return $this;
    }

    public function getContent ()
    {
        $content = nl2br($this->content);
        foreach ($this->getTags() as $code => $tag)
        {
            $content = $this->replaceTag ($content, $code, $tag);
        }
        return '<p>'.$content.'</p>';
    }

    protected function replaceTag ($content, $code, $tag)
    {
        return str_replace(array('['.$code.']', '[/'.$code.']'), array('<'.$tag.'>', '</'.$tag.'>'), $content);
    }

    public function getQuoteText ()
    {
        $content = preg_replace('#\['.self::TAG_QUOTE.'\][^\@]*\[\/'.self::TAG_QUOTE.'\]\s*#', "\n...\n", $this->content);
        $authorText = '['.self::TAG_BOLD.']'.$this->user->username.' написал'.($this->user->gender == VUser::GENDER_FEMALE ? 'а' : '').':[/'.self::TAG_BOLD.'] ';
        return '['.self::TAG_QUOTE.']'.$authorText.strip_tags($content).'[/'.self::TAG_QUOTE.']'."\n";
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