<?php
class VContentBlockWidget extends CWidget {

    public $name = ''; // content block name


	/**
	 * Executes the widget.
	 * This method is called by {@link CBaseController::endWidget}.
	 */
    public function run() {
		parent::run();
		$this->registerAssets();

        $cb = VContentBlock::model()->byName($this->name)->find();
        if (!$cb) {
            $cb = new VContentBlock();
            $cb->name = $this->name;
            if (!$cb->save())
                return '';
        }

		$this->render(Yii::app()->VExtension->getViewsAlias() . '.cb', array(
			'isAdmin' => $this->isAdmin(),
			'cd' => $cb,
		));
    }

    public function getJsOptions()
    {
        return array (
        );
    }

    public function isAdmin ()
    {
        return true;
    }

	/**
	 * Register CSS and JS files.
	 */
	protected function registerAssets() {
        $cs = Yii::app()->clientScript;
        $url = Yii::app()->VExtension->getAssetsUrl();

        if ($this->isAdmin()) {
            $cs->registerCssFile($url.'/css/cb.css');
            $cs->registerScriptFile($url.'/js/vapp.js', CClientScript::POS_HEAD);
            $cs->registerScriptFile($url.'/js/cb.js', CClientScript::POS_HEAD);
            $js = '';
            //$js .= 'Vauth.init('.json_encode($this->getJsOptions()).');'."\n";
            //$cs->registerScript('auth-services', $js, CClientScript::POS_READY);
        }
	}
}
