<?php
class VAdminFilterBuilderWidget extends CWidget
{
	public $form;

    public $defaultOptions = array(
		'action'=>'',
		'method'=>'get',
        'htmlOptions' => array('class' => ''),
	);
	public $renderSafeAttributes = false;

	public function run()
	{
        $this->registerAssets();

		$form = $this->form;
        if (!$form)
            return;

        echo CHtml::openTag('div', array('class' => 'navbar'));
        echo CHtml::openTag('div', array('class' => 'navbar-inner', 'style' => 'padding-top: 10px;'));

        $form->activeForm = array_merge($this->defaultOptions, $form->activeForm );
        $form->formInputLayout = '<div class="span3">{label}{input}{hint}<div class="form-row__error">{error}</div></div>';
        $form->buttonsLayout = '<div class="span2">{buttons}</div></div>';
        $form->buttonLayout = '<div class="span6">{button}</div>';
        $form->formInputsLayout = '<div class="row-fluid"><div class="span10">{elements}</div>';
        $form->formErrorLayout = '{error}';

        echo $form->render();

        echo CHtml::closeTag('div');
        echo CHtml::closeTag('div');
	}

    public function registerAssets () {

        /*
        $cs = Yii::app()->clientScript;
        $url = Yii::app()->VExtension->getAssetsUrl();
        $cs->registerCssFile($url.'/css/vform.css');
        $cs->registerScriptFile($url.'/js/jquery.form.js', CClientScript::POS_HEAD);
        */
    }
}
?>