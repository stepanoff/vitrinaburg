<?php
/**
 * визивиг
 */
class VHtmlCkEditorWidget extends CWidget
{
    public $id = null;
    public $model;
    public $attribute;
    public $description;
    public $htmlOptions = array();
    public $disabled = null;
    public $mode = 'default';
    public $options = array();
    public $css = null;

    private $_defaultOptions = array(
        'width'                    => 790,
        'height'                   => 300,
        'skin'                     => 'v2',
        'toolbar'                  => array(
            array(
                'Bold', 'Italic', 'Strike', 'Format', '-',
                'Undo', 'Redo', '-',
                'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'BulletedList', 'NumberedList', '-',
                'Blockquote', 'Link', 'Unlink', 'Linkpopup', 'Templates', 'Maximize', 'Typograf', 'Source',
            )
        ),
        'extraPlugins'             => 'format,fakeckobject,webkitfix,typograf,linkpopup',
        'templates_replaceContent' => false,
        'forcePasteAsPlainText'    => true,
        'lang'                     => 'ru',
        'format_tags'              => 'h2;h3;h4;h5;h6',
    );

    public function init () {
        $this->id = $this->id !== null ? $this->id : (isset($this->htmlOptions['id']) ? $this->htmlOptions['id'] : get_class($this->model) . '_' . $this->attribute);
    }

    public function run()
    {
        $this->registerAssets();

        if ($this->disabled !== null)
            $this->htmlOptions['disabled'] = $this->disabled;
        $this->htmlOptions['id'] = $this->id;

        echo CHtml::activeTextArea($this->model, $this->attribute, $this->htmlOptions);
    }

    public function registerAssets () {
        $cs = Yii::app()->clientScript;
        $url = Yii::app()->VExtension->getAssetsUrl();

        $cs->registerScriptFile($url . '/js/ckeditor_3.4.1/ckeditor.js', CClientScript::POS_HEAD);

        $config = CJSON::encode(array_merge($this->_defaultOptions, $this->options));
        $script = '';
        if (!empty($this->css)) {
            $script .= '
                CKEDITOR.config.contentsCss =
                    [
                        "' . $url . '/js/ckeditor_3.4.1/' . $this->css . '"
                    ];';
        }
        $script .= '
            var editor' . $this->id . ' = CKEDITOR.replace("' . $this->id . '",' . $config . ');
        ';
        $cs->registerScript('ckeditor_' . $this->id, $script, CClientScript::POS_END);
    }

}