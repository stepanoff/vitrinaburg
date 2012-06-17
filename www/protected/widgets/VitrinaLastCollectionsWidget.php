<?php
class VitrinaLastCollectionsWidget extends CWidget {

    public $max = 4; // maximum items in block
    public $alwaysMax = true;

    public function run() {
        $items = array();
        $photos = VitrinaShopCollectionPhoto::model()->onSite()->orderCreated()->byLimit(50)->findAll();
        shuffle($photos);
        for ($i = 0; $i < $this->max; $i++)
            $items[] = $photos[$i];
		$this->render('lastCollections', array(
			'items' => $items,
		));
    }

}
