<?php
class VForumDiscussion extends VActiveRecord
{
    protected $categoryModel = 'VForumCategory';
    protected $commentModel = 'VForumDiscussionComment';
    protected $userModel = 'VUser';

	public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return 'forum_discussions';
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

    public function byIds ($ids, $alias = 't')
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition'=>$alias.'.id IN ('.implode(',',$ids).')',
        ));
        return $this;
    }

    public function orderDefault($alias = 't')
    {
        $this->getDbCriteria()->mergeWith(array(
            'order'=>$alias.'.date_created DESC',
        ));
        return $this;
    }

    public function relations()
    {
        $res = parent::relations();
        return array_merge($res, array(
            'comments' => array(self::HAS_MANY, $this->commentModel, 'forum_discussion_id', 'order' => 'c.date ASC', 'alias' => 'c', 'index'=>'id'),
            'commentsTotal' => array(self::STAT, $this->commentModel, 'forum_discussion_id'),
            'user' => array(self::BELONGS_TO, $this->userModel, 'user_id', 'joinType'=>'INNER JOIN'),
        ));
    }

    public function rules()
    {
        $res = parent::rules();
        return array_merge($res, array(
        	array('title', 'required', 'message' => 'Укажите название темы'),
            array('content', 'required', 'message' => 'Напишите что-нибудь'),
            array('cost', 'required', 'message' => 'Укажите стоимость', 'on' => VForumCategory::TYPE_ANNOUNCE),
            array('photo', 'ImageValidator', 'on' => VForumCategory::TYPE_ANNOUNCE),
        	array('title, date_created, user_id, cost, photo, forum_category_id, content', 'safe', 'on' => 'admin'),
            array('title, date_created, user_id, forum_category_id, content', 'on' => VForumCategory::TYPE_ANNOUNCE.', '.VForumCategory::TYPE_DEFAULT),
            array('cost, photo', 'on' => VForumCategory::TYPE_ANNOUNCE),
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
        	'title' => 'Тема',
            'content' => 'Содержание темы',
            'date_created' => 'Дата создания',
            'user_id' => 'Автор темы',
            'forum_category_id' => 'Рубрика форума',
            'cost' => 'Стоимость',
            'photo' => 'Изображение',
        ));
    }


    
    protected function beforeValidate()
    {
        return parent::beforeValidate();
    }

	protected function beforeSave()
	{
        if ($this->isNewRecord)
            $this->date_created = time();

		return parent::beforeSave();
    }

    protected function afterFind()
    {
        return parent::afterFind();
    }

    protected function afterSave()
    {
		$this->convertImages();
    	return parent::afterSave();
    }
    
	/**
	 * Конвертирует необходимые изображения
	 * @return void
	 */
	private function convertImages()
	{

	}
    
	protected function afterDelete()
	{
		parent::afterDelete();
	}

}