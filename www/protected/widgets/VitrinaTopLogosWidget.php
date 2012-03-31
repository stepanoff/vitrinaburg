<?php
class VitrinaTopLogosWidget extends CWidget {

    public $max = 6; // maximum items in block
    public $alwaysMax = true;

    public function run() {
        $model = new VitrinaShop;
        $items = $model->onSite()->onTop()->orderRand()->byLimit($this->max)->findAll();

        $items2 = array();
        if (count($items) < $this->max && $this->alwaysMax)
        {
            $items2 = $model->onSite()->orderRand()->byLimit(($this->max - count($items)))->findAll();
        }

        $items = $items + $items2;

		$this->render('topLogos', array(
			'items' => $items,
		));
    }

}
