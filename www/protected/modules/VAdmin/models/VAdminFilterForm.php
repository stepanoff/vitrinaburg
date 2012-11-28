<?php
class VAdminFilterForm extends VFormModel
{
    protected $attributes = array();
    protected $_elements = array();

    public function __set($name,$value)
    {
        if (isset($this->attributes[$name])) {
            $this->attributes[$name] = $value;
        }
        else return parent::__set($name,$value);
    }

    public function __get($name)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
        else return parent::__get($name);
    }

    public function __isset($name)
    {
        if (isset($this->attributes[$name])) {
            return true;
        }
        else return parent::__isset($name);
    }

    public function setElements ($elements) {
        $this->_elements = $elements;
        foreach ($this->_elements as $k => $data) {
            $this->attributes[$k] = isset($data['value']) ? $data['value'] : false;
        }
    }

    public function rules()
    {
        $res = array();
        $safe = array();
        foreach ($this->_elements as $k => $data) {
            $safe[] = $k;
            if (isset($data['rules'])) {
                $res = array_merge($res, $data['rules']);
            }
        }
        $res[] = array(implode(', ', $safe), 'safe');
        return $res;
    }

    public function attributeLabels()
    {
        $res = array();
        foreach ($this->_elements as $k => $data) {
            if (isset($data['label'])) {
                $res[$k] = $data['label'];
            }
        }
        return $res;
    }

    public function getFormElements () {
        $res = array();
        foreach ($this->_elements as $k => $data) {
            if (isset($data['label'])) {
                unset($data['label']);
            }
            if (isset($data['value'])) {
                unset($data['value']);
            }
            if (isset($data['rules'])) {
                unset($data['rules']);
            }
            $res[$k] = $data;
        }
        return $res;
    }

    public function getButtons () {
        return array(
            'reset' => array(
                'type' => 'submit',
                'label'=> 'Сбросить',
                'class' => 'btn btn-block'
            ),
            'submit' => array(
                'type' => 'submit',
                'label'=> 'Показать',
                'class' => 'btn btn-block btn-primary'
            ),
        );
    }

}
?>