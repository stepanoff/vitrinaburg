<?php
class VDiscussionCommentForm extends CFormModel
{
    public $text;
    public $title;
    public $parentId;

    public function rules()
    {
        return array(
            array('title', 'required', 'message'=>'Укажите название темы' ),
            array('text', 'required', 'message'=>'Напишите что-нибудь' ),
            array('text, parentId, title', 'safe'),
		);
    }

    public function attributeLabels()
    {
        return array(
                        'title' => 'Тема',
                        'text' => 'Комментарий',
                        'parentId' => 'Родительский комментарий',
                    );
    }

}
?>