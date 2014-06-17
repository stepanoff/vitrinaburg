        <div class="main-col">
            <?php
            $title = $collection->name;
            /*
            $titlePost = $collection->name;
            if ($photo && !empty($photo->name))
            {
                $title = $photo->name . ' &mdash; ' . $titlePost;
            }
            */
            ?>
          <h1 id="pageHeader"><?php if ($photo) { /*echo '<span class="more-link-"><a href="#">Cмотреть все фото</a> ('.count($collection->photos).')</span> '; */} ?><?php echo $title; ?></h1>
            <?php

        if ($collection->photos)
        {
            ?>
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->staticUrl; ?>/js/ad-gallery/jquery.ad-gallery.css">
  <script type="text/javascript" src="<?php echo Yii::app()->request->staticUrl; ?>/js/jquery.docwrite.js"></script>
  <script type="text/javascript" src="<?php echo Yii::app()->request->staticUrl; ?>/js/ad-gallery/jquery.ad-gallery.js"></script>
  <script type="text/javascript">
  $(function() {
    var ad_pushStateSupport = !!(window.history && history.pushState);
    var baseUrl = '<?php echo CHtml::normalizeUrl(array('/vitrinaCollection/show/', 'collectionId'=>$collection->id)); ?>';
    var saveInHistory = false;
    if(ad_pushStateSupport){
          $(window).bind('popstate', function() {
              var l = location.pathname.replace(baseUrl+"", '');
              l = l.replace('/', '');
              l = parseInt(l);
              var images = galleries[0].images;
              var index = 0;
              for (i in images)
              {
                  _id = $(images[i]['thumb']).attr('ad-id');
                  if (l == _id)
                  {
                      index = i;
                      break;
                  }
              }

              {
                galleries[0].showImage(index);
                saveInHistory = false;
              }
          });
    }

    var galleries = $('.ad-gallery').adGallery({
        'description_wrapper' : $('#ad-pop-text'),
        'thumb_opacity' : 1.0,
        'update_window_hash' : false,
        'start_at_index' : <?php echo $index; ?>,
        'slideshow' : {'enable' : false},
        callbacks: {
            afterImageVisible: function () {
                var thumb = this.images[this.current_index]['thumb'];
                var _id = $(thumb).attr("ad-id");
                $('#tofav2').attr('href', baseUrl+_id+'/toggleFavorite/');
                if (saveInHistory)
                {
                    if (ad_pushStateSupport)
                    {
                        history.pushState(null, document.title, baseUrl+_id+'/');
                        // todo: вызывать событие vapp
                        reloadCounters();
                    }
                    else
                        window.location = '<?php echo 'http://'.Yii::app()->params['domain']; ?>'+baseUrl+_id+'/';
                }
                else
                    saveInHistory = true;
            }
        }
    });
  });
  </script>

  <div id="gallery" class="ad-gallery">
            <?php

            echo '
      <div class="ad-controls">
      </div>
      <div class="ad-nav">
        <div class="ad-thumbs">
          <ul class="ad-thumb-list">';
            $i = 1;
            foreach ($collection->photos as $_photo)
            {
                $bigPicSrc = VHtml::thumbSrc($_photo->src, array(500, 500), VHtml::SCALE_EXACT);
                echo '<li>';
                $imageTag = VHtml::thumb($_photo->src, array(72, 100), VHtml::SCALE_SMALLER_SIDE, array('alt'=>$_photo->name, 'title'=>VHtml::formattedSum($_photo->cost, true), 'ad-id'=>$_photo->id));
                echo CHtml::link($imageTag, array('/vitrinaCollection/show/', 'collectionId'=>$collection->id,'photoId'=>$_photo->id), array('ad-link'=>$bigPicSrc));
                echo '</li>';
                $i++;
            }
            echo '</ul>
        </div>
      </div>
      <div class="ad-po-left">
          <div class="ad-image-wrapper">';
          if ($photo)
          {
              $firstPhoto = $collection->photos ? array_slice($collection->photos, 0, 1) : false;
              $photo = $photo ? $photo : ($collection->photos ? $firstPhoto[0] : false);
              ?>
              <div class="po-pic">
                <div class="pop-pos">
                  <div class="pop-cell">
              <?php
              $imageTag = VHtml::thumb($photo->src, array(500, 500), VHtml::SCALE_EXACT, array('title'=>$photo->name, 'alt'=>$photo->name));
              echo $imageTag;
              ?>
                  </div>
                </div>
                <div class="pop-text">
                  <?php if ($photo->cost) { echo '<div class="pop-price">'.VHtml::sum($photo->cost, true).'</div>'; } ?>
                  <?php echo $photo->name; ?>
                </div>
              </div>

              <?
          }
          echo '</div>
          <div class="ad-pop-text" id="ad-pop-text"></div>
    </div>';
        }
            ?>

              <div class="ad-po-right po-right">
              <?php
                  $this->widget('application.widgets.VitrinaFavoriteWidget', array(
                      'link' => CHtml::normalizeUrl(array('/vitrinaCollection/toggleFavorite/', 'collectionId'=>$collection->id, 'photoId'=>$photo->id)),
                      'type' => VitrinaFavorite::TYPE_COLITEM,
                      'typeId' => $photo->id,
                  ));
              ?>
                <h3>Где купить</h3>
                <p>Магазин: <?php echo CHtml::link($collection->shopObj->name, array('/vitrinaShop/show/', 'id'=>$collection->shopObj->id), array()); ?>
                  <?php
                  $tmp = array();
                  $addresses = $collection->shopObj->addresses;
                  if ($addresses)
                  {
                      echo '<br/>';
                      foreach ($collection->shopObj->addresses as $address) {
                            $tmp[] = VitrinaHtmlHelper::formatAddress($address);
                      }
                      echo implode('<br/>', $tmp);
                  }
                  ?>
                  <?php
                  $addresses = $collection->shopObj->addresses;
                  if ($addresses)
                  {
                      $phones = false;
                      $tmp = array();
                      foreach ($addresses as $address) {
                            if (!$address->phone)
                                continue;
                            $tmp[] = $address->phone;
                      }
                      $phones = implode(', ', $tmp);

                      if ($phones)
                      {
                      ?>
                      <div class="po-phone-box">
                        Уточните наличие по телефону:
                        <div><b><a href="#" class="showPhone">показать телефон</a></b></div>
                        <div style="display: none;" class="shopPhone"><b><?php echo nl2br($phones); ?></b></div>
                      </div>
                      <?
                      }
                  }
                  ?>
                <div class="po-shop">
                    <?php
                    if ($collection->shopObj->logo)
                    {
                        echo '<div class="pos-logo">';
                        $imageTag = VHtml::thumb($collection->shopObj->logo, array(90, 90), VHtml::SCALE_EXACT, array('title'=>$collection->shopObj->name, 'alt'=>$collection->shopObj->name));
                        echo CHtml::link($imageTag, array('/vitrinaShop/show/', 'id'=>$collection->shopObj->id), array());
                        echo '</div>';
                    }
                    ?>
                  <div class="pos-more"><?php echo CHtml::link('Смотреть все коллекции магазина', array('/vitrinaShop/show/', 'id'=>$collection->shopObj->id), array()); ?></div>
                  <p><?php echo nl2br($collection->text); ?></p>
                </div>
              </div>

            <?php
            if (0)
            {
                ?>
                <h2>С этим товаром смотрят</h2>
                <ul class="gallery">
                  <li><a href="#"><img src="images/must_be_deleted/gallery01.jpg" width="72" height="100" alt=""></a></li>
                  <li><a href="#"><img src="images/must_be_deleted/gallery02.jpg" width="72" height="100" alt=""></a></li>
                  <li><a href="#"><img src="images/must_be_deleted/gallery03.jpg" width="72" height="100" alt=""></a></li>
                  <li><a href="#"><img src="images/must_be_deleted/gallery04.jpg" width="72" height="100" alt=""></a></li>
                  <li><a href="#"><img src="images/must_be_deleted/gallery05.jpg" width="72" height="100" alt=""></a></li>
                  <li><a href="#"><img src="images/must_be_deleted/gallery06.jpg" width="72" height="100" alt=""></a></li>
                  <li><a href="#"><img src="images/must_be_deleted/gallery07.jpg" width="72" height="100" alt=""></a></li>
                  <li><a href="#"><img src="images/must_be_deleted/gallery08.jpg" width="72" height="100" alt=""></a></li>
                  <li><a href="#"><img src="images/must_be_deleted/gallery09.jpg" width="72" height="100" alt=""></a></li>
                  <li><a href="#"><img src="images/must_be_deleted/gallery03.jpg" width="72" height="100" alt=""></a></li>
                  <li><a href="#"><img src="images/must_be_deleted/gallery04.jpg" width="72" height="100" alt=""></a></li>
                  <li><a href="#"><img src="images/must_be_deleted/gallery05.jpg" width="72" height="100" alt=""></a></li>
                  <li><a href="#"><img src="images/must_be_deleted/gallery06.jpg" width="72" height="100" alt=""></a></li>
                  <li><a href="#"><img src="images/must_be_deleted/gallery07.jpg" width="72" height="100" alt=""></a></li>
                  <li><a href="#"><img src="images/must_be_deleted/gallery08.jpg" width="72" height="100" alt=""></a></li>
                  <li><a href="#"><img src="images/must_be_deleted/gallery09.jpg" width="72" height="100" alt=""></a></li>
                </ul>
                <?php
            }
            ?>
        
        </div>
            <div id="pageDescriptionFooter"></div>
        </div>
        <div class="left-col">
            <?php
                $this->widget('application.widgets.VitrinaSectionsTreeWidget', array(
                    'counters' => $counters,
                    'selectedSections' => $selectedSections,
                ));
            ?>
          <div class="left-banner">
          </div>
          <div class="vk">
              <?php $this->renderPartial('application.views.blocks.social', array()); ?>
          </div>
        </div>

<script type="text/javascript">
    $(document).ready(function(){
        $(".po-phone-box a").click(function(){
            container = $(this).closest("div");
            phoneContainer = $(this).closest(".po-phone-box").find(".shopPhone");
            container.hide();
            phoneContainer.show();
            return false;
        });

        $("a.showMap").click(function(){
            return false;
        });

    });

</script>