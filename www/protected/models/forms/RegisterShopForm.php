<?php
/**
 * Модель для формы поиска автокредитов
 * @author stepanoff
 * @version 1.0
 *
 */
class BanksAutocreditSearchForm extends BanksProgrammSearchForm
{
	public $type;
	public $subtype;
	public $income_confirm;
	public $registration;
	public $sum_start;
	public $age;
	
    public function rules()
    {
		//return parent::rules();
        return array_merge(parent::rules(), array(
        	array('income_confirm, registration', 'numerical', 'integerOnly' => true ),
        	array('income_confirm', 'in', 'range' => array_keys(BanksProgramm::incomeConfirmTypes()) ),
			array('registration', 'in', 'range' => array_keys(BanksProgramm::registrationTypes()) ),
			array('type, subtype', 'safe' ),
			array('sum_start', 'checkStartSum'),
		));
    }

    public function afterValidate()
    {
    	return parent::afterValidate();
    }
    
    public function beforeValidate()
    {
		$this->sum_start = preg_replace('/[\s\.\,]/', '', $this->sum_start);
    	
    	return parent::beforeValidate();
    }
    
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), array(
        	'type'				=> 'Вид транспорта',
        	'subtype'			=> 'Тип транспорта',
			'sum'				=> 'Стоимость автомобиля',
        	'sum_start'			=> 'Ваш первоначальный взнос',
        	'income_confirm'	=> 'Подтверждение дохода',
        	'age'				=> 'Ваш возраст',
			'registration'		=> 'Прописка',
        ));
    }

}
?>