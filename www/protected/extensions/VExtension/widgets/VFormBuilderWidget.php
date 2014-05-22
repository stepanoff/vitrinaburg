<?php
class VFormBuilderWidget extends CWidget
{
	public $form;
	public $model;
    public $elements;
    public $startPageIndex = false;
    public $renderButtons = true;

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
        $cs->registerScriptFile($url.'/js/jquery.form.js', CClientScript::POS_HEAD);
        $cs->registerCssFile($url.'/css/vform.css');
    }
}
?>