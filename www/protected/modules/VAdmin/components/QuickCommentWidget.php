<?php
class QuickCommentWidget extends CWidget
{
	public $url = array (''); // урл, на который будет отправлен камент
	
	public $value; // текущий текст камента
	
	public $attribute = 'text'; // имя атрибута для отправки камента
	
	public $editText = 'изменить';
	
	public $saveText = 'сохранить';
	
	public $cancelText = 'отмена';
	
	public function run()
	{
		$url = CHtml::normalizeUrl($this->url);
		$id = 'quickComment'.$this->id;
		$idDiv = 'quickCommentEditContainer'.$this->id;
		$idText = 'quickCommentValueContainer'.$this->id;
		$idText2 = 'quickCommentTextContainer'.$this->id;
		
		echo CHtml::openTag ('div', array('id'=>$idText2), '');

		echo CHtml::openTag ('div', array('id'=>$idText), '');
		echo nl2br($this->value);
		echo CHtml::closeTag ('div');
		
		echo CHtml::link($this->editText, $url, array('onclick' => '$("#'.$idDiv.'").show(); $("#'.$idText2.'").hide();return false;', 'style' => 'font-size: 7pt;'));
		echo CHtml::closeTag ('div');
		
		echo CHtml::openTag ('div', array('style'=>'display:none;', 'id'=>$idDiv), '');
		echo CHtml::textArea($this->attribute, $this->value, array('id' => $id));
		echo '<br/>';
		echo CHtml::button($this->saveText, array('onclick' => 'SendRequest("'.$url.'", { target: "'.$idText.'", '.$this->attribute.': $("#'.$id.'").val()}); $("#'.$idText2.'").show(); $("#'.$idDiv.'").hide();return false;'));
		echo CHtml::link($this->cancelText, $url, array('onclick' => '$("#'.$idDiv.'").hide(); $("#'.$idText2.'").show();return false;', 'style' => 'font-size: 7pt;'));
		echo CHtml::closeTag ('div');
	}
}