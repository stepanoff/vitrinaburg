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
                        'text' => 'Оставить комментарий',
                        'parentId' => 'Родительский комментарий',
                    );
    }

    public function getFormElements ()
    {
        $widgetAlias = 'ext.VExtension.widgets.VTextEditorWidget';
        return array (
            'elements' => array (
                'parentId' => array (
                    'type' => 'hidden',
                )
            ),
            'elements' => array (
                'text' => array (
                    'type' => $widgetAlias,
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
}
?>