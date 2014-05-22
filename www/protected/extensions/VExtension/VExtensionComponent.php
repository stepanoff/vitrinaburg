<?php
class VExtensionComponent extends CComponent {

    public $extensionAlias = 'ext.VExtension';
    public $modules = array ();
    public $components;
    public $staticUrl = '/';

    protected $assetsPath = '';
    protected $assetsUrl = '';

    public function init () {
        Yii::import($this->extensionAlias.'.*');
        Yii::import($this->extensionAlias.'.models.*');
        Yii::import($this->extensionAlias.'.widgets.*');
        Yii::import($this->extensionAlias.'.validators.*');
        Yii::import($this->extensionAlias.'.controllers.*');
        Yii::import($this->extensionAlias.'.widgets.htmlextended.*');
        Yii::import($this->extensionAlias.'.helpers.*');

        if ($this->components) {
            foreach ($this->components as $componentName => $comp) {
                Yii::app()->setComponents(array(
                    $comp['name']=>$comp['options']
                ));
            }
        }

        $this->assetsPath = dirname(__FILE__).DIRECTORY_SEPARATOR.'assets';
        $this->assetsUrl = $this->staticUrl.Yii::app()->assetManager->publish($this->assetsPath, false, -1, YII_DEBUG);
    }

    public function getAssetsPath () {
        return $this->assetsPath;
    }

    public function getAssetsUrl () {
        return $this->assetsUrl;
    }

    public function getViewsAlias () {
        return $this->extensionAlias . '.views';
    }

}
?>
