    <div id="inner-page">
      <div class="base-width clearfix">
        <div class="main-col">
        <div id="collsPhotosContainer">
            <?php
                $this->widget('application.widgets.VitrinaTopLogosWidget', array(
                    'max' => 6,
                    'alwaysMax' => true,
                ));
            ?>
          <?php
          if ($items)
          {
              echo '<ul class="cat-list js-collsPhotos">';
              foreach ($items as $item)
              {
                  echo '<li>';
                  $imageSrc = VHtml::thumbSrc($item->src, array(150, 200), VHtml::SCALE_SMALLER_SIDE);
                  //$imageTag = VHtml::thumb($item->src, array(120, 200), VHtml::SCALE_SMALLER_SIDE, array('title'=>$item->name, 'alt'=>$item->name));
                  echo CHtml::link('', array('/vitrinaCollection/show/', 'collectionId'=>$item->collection->id,'photoId'=>$item->id), array(
                    'class'=>'cat-list-photo',
                    'style'=>'background-image: url(\''.$imageSrc.'\');',
                  ));
                  $price = '&nbsp;';
                  if ($item->cost)
                      $price = VHtml::sum($item->cost, true);
                  echo '<b>'.$price.'</b>';
                  echo CHtml::link($item->collection->name, array('/vitrinaCollection/show/', 'collectionId'=>$item->collection->id,'photoId'=>$item->id), array());
                  echo '</li>';
              }
              echo '</ul>';
          }
          ?>
          <div class="pagination">
          <?php
            $this->widget('CLinkPager', array('pages'=>$pages));
          ?>
          </div>
          <?php
            $this->widget('application.widgets.VitrinaInfiniteScrollWidget', array(
                'navSelector' => "#collsPhotosContainer .pagination",
                'nextSelector' => "#collsPhotosContainer .pagination li.next a",
                'contentSelector' => "#collsPhotosContainer ul.js-collsPhotos",
                'itemSelector' => "#collsPhotosContainer ul.js-collsPhotos li",
                'finishedMsg' => 'Изображения загружены',
                'msgText' => 'Загрузка изображений...',
            ));
          ?>
        </div>
        </div>
        <div class="left-col">
        <?php
            $this->widget('application.widgets.VitrinaSectionsTreeWidget', array(
                'counters' => $counters,
                'selectedSection' => $sectionId,
            ));
        ?>
          <div class="left-banner"><img src="images/must_be_deleted/right_banner.jpg" width="240" height="400" alt=""></div>
          <div class="vk"><img src="images/must_be_deleted/vk2.jpg" width="240" height="229" alt=""></div>
        </div>
      </div>
    </div>
