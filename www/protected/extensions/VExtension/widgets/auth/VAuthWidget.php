<?php
class VAuthWidget extends CWidget {

    public $redirectUrl = ''; // redirect url after success authentication
    public $returnUrl = ''; // return url after success authentication
    public $providers_set = ''; // services used for authentication
    public $width = false; // width of widget in pixels, default 600
    public $height = false; // height of widget in pixels, default 400

	/**
	 * @var string EAuth component name.
	 */
	public $component = 'vauth';
	
	/**
	 * @var array the services.
	 * @see EAuth::getServices() 
	 */
	public $services = null;
	
	/**
	 * @var boolean whether to use popup window for authorization dialog. Javascript required.
	 */
	public $popup = true;

    public $iframe = null;

	/**
	 * @var string the action to use for dialog destination. Default: the current route.
	 */
	public $action = null;

	/**
	 * Initializes the widget.
	 * This method is called by {@link CBaseController::createWidget}
	 * and {@link CBaseController::beginWidget} after the widget's
	 * properties have been initialized.
	 */
	public function init() {
		parent::init();
		
		// EAuth component
		$component = Yii::app()->{$this->component};

		// Some default properties from component configuration
		if (!isset($this->services))
        {
            $services = $component->getFullServices();
            if ($this->providers_set)
            {
                $tmp = explode(',', $this->providers_set);
                $tmp_services = array();
                {
                    foreach($tmp as $provider)
                    {
                        if (isset($services[trim($provider)]))
                            $tmp_services[$provider] = $services[trim($provider)];
                    }
                }
                $this->services = $tmp_services;
            }
            else
                $this->services = $services;
        }
		if (!isset($this->popup))
			$this->popup = $component->popup;
		
		// Set the current route, if it is not set.
		if (!isset($this->action))
			$this->action = Yii::app()->urlManager->parseUrl(Yii::app()->request);
        $this->width = $this->width ? $this->width : 400;
        $this->height = $this->height ? $this->height : 320;
	}
	
	/**
	 * Executes the widget.
	 * This method is called by {@link CBaseController::endWidget}.
	 */
    public function run() {
		parent::run();
		$this->registerAssets();

        $component = Yii::app()->{$this->component};

        $services = array();
        $serviceTemplates = array();

        foreach ($this->services as $k=>$service)
        {
            $serviceTemplates[$k] = $component->getServiceTemplate($k);
        }

        $component = Yii::app()->{$this->component};
		$this->render($component->getBaseTemplatePath () . 'auth', array(
			'id' => $this->getId(),
			'services' => $this->services,
            'serviceTemplates' => $serviceTemplates,
			'action' => $this->action,
            'width' => $this->width,
            'height' => $this->height,
		));
    }

    public function getJsOptions()
    {
        return array (
            'redirectUrl' => $this->redirectUrl,
            'returnUrl' => $this->returnUrl,
            'popup' => $this->popup,
            'iframe' => $this->iframe,
        );
    }

	/**
	 * Register CSS and JS files.
	 */
	protected function registerAssets() {
		//$cs->registerCoreScript('jquery');

        $cs = Yii::app()->clientScript;
        $url = Yii::app()->VExtension->getAssetsUrl();
		$cs->registerCssFile($url.'/css/auth.css');
        $cs->registerScriptFile($url.'/js/auth.js', CClientScript::POS_HEAD);

		// Open the authorization dilalog in popup window.
		if ($this->popup) {
			$js = '';
			foreach ($this->services as $name => $service) {
				$args = $service->getJsArguments();
				$args['id'] = $service->getServiceName();
				$js .= '$(".auth-service-'.$service->getServiceName().'").auth_service('.json_encode($args).');'."\n";
			}
            $js .= 'Vauth.init('.json_encode($this->getJsOptions()).');'."\n";
			$cs->registerScript('auth-services', $js, CClientScript::POS_READY);
		}
	}
}
