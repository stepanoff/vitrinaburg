<?php
/**
 * Модель формы для формопостроителя
 * @author stepanoff stenlex@gmail.com
 * @version 1.0
 *
 */
class VFormModel extends CFormModel
{
    public function rules()
    {
    }

    public function attributeLabels()
    {
    }

    public function getFormRenderData() {
        $elements = array(
            'elements' => array(),
            'enctype' => 'multipart/form-data',
            'elements' => $this->getFormElements(),
            'buttons' => $this->getButtons(),
        );
        return $elements;
    }

    public function getFormElements () {
        return array();
    }

    public function getButtons () {
        return array();
    }

}
?>