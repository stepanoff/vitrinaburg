<?php
/**
 * Phone validator
 * User: stepanoff
 * Date: 10.01.12
 * Time: 14:35
 */
class VPhoneMaskValidator extends CValidator
{
	public $mask = '\+\d\s?\(\d{3}\)\s?\d{3}\-\d{2}\-\d{2}';
    public $example = '+7 (555) 555-55-55';

	public function validateAttribute($object, $attribute) {
        $value = $object->$attribute;
        if (!preg_match('#^('.$this->mask.')$#', trim($value)))
            $this->addError($object, $attribute, 'Телефон указан некорректно. Пример правильного телефона: '.$this->example);
	}
}

