<?php
class VitrinaCrumbsWidget extends CWidget {

    public $items;

    public function run() {
		parent::run();

        $items = $this->items !== null ? $this->items : Yii::app()->controller->crumbs;

		$this->render('crumbs', array(
            'items' => $items,
		));
    }

}
