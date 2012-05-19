<?php
class VitrinaInfiniteScrollWidget extends CWidget {

    public $navSelector = "#collsPhotosContainer .pagination";
    public $nextSelector = "#collsPhotosContainer .pagination li.next a";
    public $contentSelector = "#collsPhotosContainer ul.js-collsPhotos";
    public $itemSelector = "#collsPhotosContainer ul.js-collsPhotos li";
    public $finishedMsg = 'Изображения загружены';
    public $msgText = 'Загрузка изображений...';

    public function run() {
        $jsSource = Yii::app()->request->staticUrl.'/js/jquery.infinitescroll.js';

        $script = '
$(document).ready(function(){
  $("#collsPhotosContainer").infinitescroll({
    navSelector  : "'.$this->navSelector.'",
    nextSelector : "'.$this->nextSelector.'",
    contentSelector : "'.$this->contentSelector.'",
    itemSelector : "'.$this->itemSelector.'",
    loading: {
			finishedMsg: \'<div class="more-"><a href="#" class="gradient1">'.$this->finishedMsg.'</a></div>\',
			img: "/img/loading.gif",
			img_width: "220",
			img_height: "19",
			msgText: \'<div class="more-"><a href="#" class="gradient1">'.$this->msgText.'</a></div>\',
		},
  }

);

});
';
        $cs = Yii::app()->clientScript;
        $cs->registerScriptFile($jsSource, CClientScript::POS_END);
        $cs->registerScript('infiniteScroll', $script, CClientScript::POS_READY);
    }

}
