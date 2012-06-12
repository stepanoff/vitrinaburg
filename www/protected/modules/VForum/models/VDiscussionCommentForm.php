<?php
class VDiscussionCommentForm extends CFormModel
{
    public $text;
    public $parentId;

    public function rules()
    {
        return array(
            array('text', 'required', 'message'=>'Напишите что-нибудь' ),
            array('text, parentId', 'safe'),
		);
    }

    public function attributeLabels()
    {
        return array(
                        'text' => 'Комментарий',
                        'parentId' => 'Родительский комментарий',
                    );
    }

    /*
    public function getElements ()
    {
        return array (
            'elements' => array (
                'text' => array (
                    'type' => 'textarea',
                )
            ),
            'buttons' => array (
                'send'		 => array(
                    'type' => 'submit',
                    'label'=> 'Отправить',
                ),
            ),
        );
    }
    */
}
?>