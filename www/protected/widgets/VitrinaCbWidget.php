<?php
class VitrinaCbWidget extends CWidget {

    public $name;

    public function run() {
		parent::run();

        if (!$this->name)
            return;

        $cb = VitrinaCb::model()->byName($this->name)->find();
        if (!$cb)
            return;

		$this->render('cb', array(
            'item' => $cb,
		));
    }

}
