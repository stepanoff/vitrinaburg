<?php
class VForumCategory extends VActiveRecord
{
	protected $__visibleCollections = null; // отображаемые на сайте коллекции

    const TYPE_DEFAULT = 0;
    const TYPE_ANNOUNCE = 1;

    protected $discussionModel = 'VForumDiscussion';

	public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return 'vforum_categories';
    }

    public function getTypeList()
    {
        return array (
            self::TYPE_DEFAULT => 'простая тема',
            self::TYPE_ANNOUNCE => 'объвление о продаже',
        );
    }
    
	public function scopes()
	{
        $res = parent::scopes();
        return array_merge($res, array(
		));
	}

    public function orderDefault($alias = 't')
    {
        $this->getDbCriteria()->mergeWith(array(
            'order'=>$alias.'.order ASC',
        ));
        return $this;
    }

    public function relations()
    {
        $res = parent::relations();
        return array_merge($res, array(
            'discussions' => array(self::HAS_MANY, $this->discussionModel, 'forum_category_id', 'order' => 'd.date_created DESC', 'alias' => 'd', 'index'=>'id'),
        ));
    }

    public function rules()
    {
        $res = parent::rules();
        return array_merge($res, array(
        	array('name', 'required', 'message' => 'Укажите название категории'),
        	array('name, order, description, type', 'safe', 'on' => 'admin'),
		));
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
        	'name' => 'Название',
            'order' => 'Порядковый номер',
            'description' => 'Описение',
            'type' => 'Тип дискуссий',
        ));
    }


    
    protected function beforeValidate()
    {
        return parent::beforeValidate();
    }

	protected function beforeSave()
	{
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