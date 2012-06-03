<?php

/**
 * $Id$
 *
 * @author stepanoff
 * @since  18.08.2009
 * 
 */
class AdminInformerForm extends CFormModel
{
	public $send_type;
	public $type;
	public $region;
	public $company_id;
	public $company_status;
	public $tariff;
	public $subject;
	public $title;
	public $text;
	public $email;
	public $priority;
	public $author;
	public $employee;
	public $employee_id;
		
	public function rules()
	{
		return array(
			array('send_type, type, region, company_id, company_status, tariff, subject, title, text, email, priority, author, employee, employee_id', 'safe'),
			array('email', 'email', 'message'=>'E-mail указан неверно'),
			array('title', 'required', 'message'=>'Укажите заголовок'),
			array('company_id', 'numerical', 'message'=>'Id компании должно быть цифрой'),
			array('text', 'required', 'message'=>'Напишите что-нибудь'),
		);
	}
	
	public function attributeLabels()
	{
		return array(
			'send_type' => 'Тип отправки',
			'type' => 'Тип сообщения',
			'region' => 'Компании по региону',
			'company_id' => 'Отдельная компания (Id)',
			'company_status' => 'Компании по статусу модерации',
			'tariff' => 'Компании по тарифу',
			'subject' => 'Тема письма (только для письма)',
			'title' => 'Заголовок',
			'text' => 'Текст',
			'email' => 'Произвольный e-mail (сообщение будет отправлено только на него)',
			'priority' => 'Срочность письма',
			'author' => 'Автор',
			'employee' => 'Сотрудник компании',
			'employee_id' => 'Каким-сотрудникам отправлять',
		);
	}

}