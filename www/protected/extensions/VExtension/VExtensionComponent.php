<?php
class VExtensionComponent extends CComponent {

    public $extensionAlias = 'ext.VExtension';
    public $modules = array ();
    public $components;

    protected $assetsPath = '';
    protected $assetsUrl = '';

    public function init () {
        Yii::import($this->extensionAlias.'.*');
        Yii::import($this->extensionAlias.'.models.*');
        Yii::import($this->extensionAlias.'.widgets.*');
        Yii::import($this->extensionAlias.'.helpers.*');

        if ($this->components) {
            foreach ($this->components as $componentName => $comp) {
                Yii::app()->setComponents(array(
                    $comp['name']=>$comp['options']
                ));
            }
        }

        $this->assetsPath = dirname(__FILE__).DIRECTORY_SEPARATOR.'assets';
        $this->assetsUrl = Yii::app()->params['staticUrl'].Yii::app()->assetManager->publish($this->assetsPath, false, -1, YII_DEBUG);
    }

    public function getAssetsPath () {
        return $this->assetsPath;
    }

    public function getAssetsUrl () {
        return $this->assetsUrl;
    }

}
?>
