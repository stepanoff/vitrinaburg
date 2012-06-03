<?php
class VAuthRedirectWidget extends EAuthRedirectWidget {

    public $changeHash = null; // менять только хеш на странице или полностью ее перезагружать
    public $component = 'vauth';

    public function run () {
        $changeHash  = $this->changeHash !== null ? $this->changeHash : (strpos($this->url, '#') !== false && strpos($this->url, '#') == 0 ? true : false);
        $assets_path = dirname(__FILE__).DIRECTORY_SEPARATOR.'assets';
        $component = Yii::app()->{$this->component};
		$this->render($component->getBaseTemplatePath () . 'redirect', array(
            'id' => $this->getId(),
            'url' => $this->url,
            'redirect' => $this->redirect,
            'assets_path' => $assets_path,
            'changeHash' => $changeHash,
        ));
        Yii::app()->end();
    }


}
