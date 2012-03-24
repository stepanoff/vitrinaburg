<?php
/**
 * GporAuthRedirectWidget class file.
 *
 * @author Stepanoff <stenlex@gmail.com>
 */

/**
 * The EAuthRedirectWidget widget displays the redirect page after returning from provider.
 * @package application.extensions.eauth
 */
class GporAuthRedirectWidget extends EAuthRedirectWidget {

    public $changeHash = null; // менять только хеш на странице или полностью ее перезагружать

    public function run () {
        $changeHash  = $this->changeHash !== null ? $this->changeHash : (strpos($this->url, '#') !== false && strpos($this->url, '#') == 0 ? true : false);
        $assets_path = dirname(__FILE__).DIRECTORY_SEPARATOR.'assets';
        $this->render('redirect', array(
            'id' => $this->getId(),
            'url' => $this->url,
            'redirect' => $this->redirect,
            'assets_path' => $assets_path,
            'changeHash' => $changeHash,
        ));
        Yii::app()->end();
    }


}
