<?php
class VFormBuilderWidget extends CWidget
{
	public $form;
	public $model;
    public $elements;
	public $formInputLayout = '<div class="form-row">{label}{input}{hint}<div class="form-row__error">{error}</div></div>';
	public $formErrorInputLayout = '<div class="form-row error">{label}{input}{hint}<div class="form-row__error">{error}</div></div>';
    public $renderButtons = true;
    public $startPageIndex = false;
    public $defaultClasses = array(
//		'text'=>'grid-span-7',
//		'password'=>'grid-span-7',
//		'textarea'=>'grid-span-9',
//		'file'=>'grid-span-7',
//		'listbox'=>'grid-span-7',
//		'dropdownlist'=>'grid-span-7',
	);
    public $defaultOptions = array(
		'action'=>'',
		'method'=>'post',
        'htmlOptions' => array('class' => 'v-form'),
	);
	public $renderSafeAttributes = false;

	public function run()
	{
        $this->registerAssets();

		$form = $this->form;
        if ($form === null) {
            $form = $this->_buildForm($this->model, $this->elements);
        }
        if (!$form)
            return;

        $form->activeForm = array_merge($this->defaultOptions, $form->activeForm );
        $this->form->startPageIndex = $this->startPageIndex;

        echo $form->render();
	}

    protected function _buildForm ($model, $elements)
    {
        if (!is_array($elements))
            return false;

        if ($this->renderButtons)
        {
            if (!isset($elements['buttons']))
                $elements['buttons'] = array();
        }
        else
        {
            unset($elements['buttons']);
        }

        if (!isset($elements['enctype']))
            $elements['enctype'] = 'multipart/form-data';

        $form = new VFormRender ($elements);
        $form->model = $model;

        return $form;
    }

    public function registerAssets () {

        $cs = Yii::app()->clientScript;
        $url = Yii::app()->VExtension->getAssetsUrl();
        $cs->registerCssFile($url.'/css/vform.css');
        $cs->registerScriptFile($url.'/js/jquery.form.js', CClientScript::POS_HEAD);
    }
}
?>