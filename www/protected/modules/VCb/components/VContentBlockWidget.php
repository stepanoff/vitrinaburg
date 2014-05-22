<?php
class VContentBlockWidget extends CWidget {

    public $name = ''; // content block name
    public $namespace = ''; // content block namespace
    public $description = ''; // content block description

    private $_module = null;


	/**
	 * Executes the widget.
	 * This method is called by {@link CBaseController::endWidget}.
	 */
    public function run() {
		parent::run();


		$this->registerAssets();

        $name = $this->name ? $this->name : VContentBlock::NAME_DEFAULT;

        $cb = VContentBlock::model()->byNamespace($this->namespace)->byName($name)->find();
        if (!$cb) {
            $cb = new VContentBlock();
            $cb->name = $name;
            $cb->namespace = $this->namespace;
            $cb->description = $this->description;
            if (!$cb->save())
                return '';

            if ($name != VContentBlock::NAME_DEFAULT) {
                $cbDefault = $cb->getDefaultCb();
                if ($cbDefault === null)
                {
                    $cbDefault = new VContentBlock();
                    $cbDefault->name = VContentBlock::NAME_DEFAULT;
                    $cbDefault->namespace = $this->namespace;
                    $cbDefault->description = '';
                    if (!$cbDefault->save()) {

                    }
                }

            }
        }

        if (empty($cb->content) && $cb->name != VContentBlock::NAME_DEFAULT) {
            $cbDefault = $cb->getDefaultCb();
            if ($cbDefault)
                $cb->content = $cbDefault->content;
        }

		$this->render('cb', array(
			'isAdmin' => $this->isAdmin(),
			'cb' => $cb,
            'route' => $this->getModule()->getBaseRoute(),
		));
    }

    public function getJsOptions()
    {
        return array (
        );
    }

    public function isAdmin ()
    {
        $currentWebUser = Yii::app()->user;
        return $currentWebUser->checkAccess(VCbModule::ROLE_CB_EDITOR);
    }

    public function getModule ()
    {
        if ($this->_module === null) {
            // todo: имя модуля должно браться из переменной
            $this->_module = Yii::app()->getModule('VCb');
        }
        return $this->_module;
    }

	/**
	 * Register CSS and JS files.
	 */
	protected function registerAssets() {
        $cs = Yii::app()->clientScript;
        $url = $this->getModule()->getAssetsUrl();
        $extUrl = Yii::app()->VExtension->getAssetsUrl();

        if ($this->isAdmin()) {
            $cs->registerScriptFile($extUrl.'/js/jquery.url.js', CClientScript::POS_HEAD);
            $cs->registerScriptFile($extUrl.'/js/vapp.js', CClientScript::POS_HEAD);

            $cs->registerCssFile($url.'/css/cb.css');
            $cs->registerScriptFile($url.'/js/cb.js', CClientScript::POS_HEAD);
            $js = '
vapp.module.register( "VCb", VCb, {
    objSelector : ".b-cb",
    editLinkClass : "b-cb__btn-edit"
});
            ';
            $cs->registerScript('VCb', $js, CClientScript::POS_END);
        }
	}
}
