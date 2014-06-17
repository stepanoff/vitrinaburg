<?php
class VitrinaFavoriteWidget extends CWidget {

    public $type = false;
    public $typeId = false;
    public $link = false;
    public $jsCallback = false;

    public function run() {
    	$userId = Yii::app()->user->id;

		$this->render('favorite', array(
			'userId' => $userId,
			'isFavorite' => Yii::app()->favorites->isFavorite ($this->type, $this->typeId),
			'type' => $this->type,
			'typeId' => $this->typeId,
			'link' => $this->link,
			'jsCallback' => $this->jsCallback,
		));
    }

}
