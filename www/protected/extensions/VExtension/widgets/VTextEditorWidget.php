<?php
class VTextEditorWidget extends CWidget
{
	public $model;
    public $attribute;
    public $type = 'bbcode';

	public function run()
	{
        parent::run();
        $this->registerAssets();

        echo CHtml::activeTextArea($this->model, $this->attribute);
	}

	protected function registerAssets() {
        $inputId = CHtml::activeId($this->model, $this->attribute);

        $cs = Yii::app()->clientScript;
        $url = Yii::app()->VExtension->getAssetsUrl();
		$cs->registerCssFile($url.'/css/markitup.css');
        $cs->registerScriptFile($url.'/js/jquery.markitup.js', CClientScript::POS_HEAD);
        $cs->registerScriptFile($url.'/js/markitup.bbcode_min.js', CClientScript::POS_HEAD);

        $js = "$('#".$inputId."').markItUp(myBbcodeSettings);\n";
		$cs->registerScript($inputId.'-bbcode-editor', $js, CClientScript::POS_READY);
	}

}
?>