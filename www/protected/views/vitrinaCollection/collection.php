    <div id="inner-page">
      <div class="base-width clearfix">
        <div class="main-col">
          <h2><?php if ($photo) { echo '<span class="more-link-"><a href="#">Cмотреть все фото</a> ('.count($collection->photos).')</span> ';} ?><?php echo $collection->name; ?></h2>
            <?php
            if ($collection->photos)
            {
                echo '<ul class="gallery">';
                foreach ($collection->photos as $_photo)
                {
                    echo '<li>';
                    $imageTag = VHtml::thumb($_photo->src, array(72, 100), VHtml::SCALE_EXACT, array('title'=>$_photo->name, 'alt'=>$_photo->name));
                    echo CHtml::link($imageTag, array('/vitrinaCollection/show/', 'collectionId'=>$collection->id,'photoId'=>$_photo->id), array());
                    echo '</li>';
                }
                echo '</ul>';
            }
            ?>
            <?php
            $firstPhoto = $collection->photos ? array_slice($collection->photos, 0, 1) : false;
            $photo = $photo ? $photo : ($collection->photos ? $firstPhoto[0] : false);
            if ($photo)
            {
                ?>
            <div class="product-one clearfix">
              <div class="po-left">
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
                <div class="po-social"><img src="images/must_be_deleted/social.png" width="457" height="21" alt=""></div>
              </div>
              <div class="po-right">
                <div class="fav-link">
                  <!-- a class="gradient1" href="#">отложить в избранное</a -->
                </div>
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
            </div>
                <?php
            }
            else
            {
            ?>
            <div class="product-one clearfix">
              <div class="po-left">
                <div class="po-pic">
                  <div class="pop-pos">
                    <div class="pop-cell">
                    </div>
                  </div>
                  <div class="pop-text">
                  </div>
                </div>
                <div class="po-social"></div>
              </div>
              <div class="po-right">
                <div class="fav-link">
                </div>
              </div>
            </div>
            <?php
            }

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
        <div class="left-col">
            <?php
                $this->widget('application.widgets.VitrinaSectionsTreeWidget', array(
                    'counters' => $counters,
                    'selectedSections' => $selectedSections,
                ));
            ?>
          <div class="left-banner"><img src="images/must_be_deleted/right_banner.jpg" width="240" height="400" alt=""></div>
          <div class="vk"><img src="images/must_be_deleted/vk2.jpg" width="240" height="229" alt=""></div>
        </div>
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