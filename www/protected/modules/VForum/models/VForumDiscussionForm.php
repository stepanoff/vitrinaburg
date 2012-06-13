<?php
class VForumDiscussionForm extends CFormModel
{
    public $text;
    public $title;
    public $forum_category_id;

    public function rules()
    {
        return array(
            array('title', 'required', 'message'=>'Укажите название темы' ),
            array('text', 'required', 'message'=>'Напишите что-нибудь' ),
            array('text, title, forum_category_id', 'safe'),
		);
    }

    public function attributeLabels()
    {
        return array(
                        'title' => 'Тема',
                        'text' => 'Комментарий',
                        'forum_category_id' => 'Категория',
                    );
    }

}
?>