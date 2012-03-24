<?php
/**
 * LjOpenIDService class file.
 *
 * @author Maxim Zemskov <nodge@yandex.ru>
 * @link http://code.google.com/p/yii-eauth/
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * Livejournal provider class.
 * @package application.extensions.eauth.services
 */
class CustomLjService extends EOpenIDService {
	
	protected $name = 'lj';
	protected $title = 'LiveJournal';
	protected $type = 'OpenID';
	protected $jsArguments = array('popup' => array('width' => 900, 'height' => 550), 'actionType' => 'processForm');

    protected $_form;
    public $formModelClass = 'LjAuthForm';

	protected $url = '';
	protected $requiredAttributes = array(
		//'name' => array('fullname', 'namePerson'),
		//'username' => array('nickname', 'namePerson/friendly'),
		//'email' => array('email', 'contact/email'),
		//'gender' => array('gender', 'person/gender'),
		//'birthDate' => array('dob', 'birthDate'),
	);

    public function init($component, $options = array()) {
        if (isset($_POST[$this->formModelClass]))
        {
            $form = $this->getForm();
            $form->setAttributes($_POST[$this->formModelClass]);
            if (!$form->validate())
            {
                $error = array (
                    'message' => 'Укажите ваш логин на livejournal',
                    'code' => 500,
                );
                throw new EAuthException($error['message'], $error['code']);
            }
            else
            {
                $options['url'] = $this->getUrlByLogin($form->login);
                $this->setState('login', $form->login);
            }
        }
        parent::init($component, $options);
    }

	protected function fetchAttributes() {
        if ($this->getState('login'))
            $this->attributes['username'] = $this->getState('login');
        else
            $this->attributes['username'] = $this->attributes['id'];
        $this->attributes['name'] = $this->attributes['username'];
        $this->attributes['url'] = $this->attributes['id'];
	}

    public function getForm ()
    {
        if ($this->_form === null)
        {
            $className = $this->formModelClass;
            $this->_form = new $className;
        }
        return $this->_form;
    }

    protected function getUrlByLogin($login)
    {
        return 'http://'.$login.'.livejournal.com';
    }

    public function getCustomTemplate()
    {
        return 'ext.gporauth.views.services.lj';
    }
}