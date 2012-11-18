<?php
class VAdminMenuWidget extends CWidget {

    public $uri = false;

    public function run() {
		parent::run();

        $items = array();

		$this->render('menu', array(
            'items' => $items,
		));
    }

}
