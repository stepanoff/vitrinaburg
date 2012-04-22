    <div id="inner-page">
      <div class="base-width clearfix">
        <div class="main-col">

    <div class="shop">
            <div class="shop-head clearfix">
            <?php
            if ($item->logo)
            {
                echo '<div class="sh-logo">';
                echo VHtml::thumb($item->logo, array(200, 200), VHtml::SCALE_EXACT, array('title'=>$item->name, 'alt'=>$item->name));
                echo '</div>';
            }
            ?>
              <h1><?php echo $item->name; ?></h1>
            <?php
            if ($item->site)
            {
                ?>
                <p><noindex><a href="/go/?url=<?php echo urlencode('http://'.(str_replace('http://','',$item->site)) ); ?>" target="_blank"><?php echo $item->site; ?></a></noindex></p>
                <?php
            }
            ?>
            </div>

            <?php
            if ($item->addresses)
            {
                echo '<div class="shop-address clearfix">';
                echo '<h2>Адреса магазинов</h2>';
                echo '<ul class="sa-addresses">';
                $address = array();
                $i = 0;
                foreach ($item->addresses as $r)
                {
                    $str = '';
                    if (!empty($r->worktime))
                        $str .= '<div class="saa-time">Время работы: '.$r->worktime.'</div>';
                    $str .= '<div class="saa-street">';
                    if (!empty($r->address))
                        $str .= VitrinaHtmlHelper::formatAddress($r);
                    if (!empty($r->phone))
                        $str .= '<br>Тел: '.$r->phone;
                    $str.= '</div>';
                    if (!empty($str))
                        $address[] = '<li class="'.(!$i ? 'active-' : '').'">'.$str.'</li>';
                    $i++;
                }
                echo implode('', $address);
                echo '</ul></div>';
            }
            ?>

            <?php
            if ($item->getActualActions())
            {
                echo '<div class="shop-events">';
                echo '<h2>Акции и скидки</h2>';
                echo '<dl>';
                foreach ($item->getVisibleActions() as $action)
                {
                    echo '<dt>'.DateUtils::_date($action->date).'</dt>';
                    echo '<dd>';
                    echo CHtml::link($action->title, array('/vitrinaAction/action', 'id'=>$action->id));
                    echo '</dd>';
                }
                echo '</dl></div>';
            }
            ?>
    </div>

    <?php
	if ($item->getVisibleCollections())
	{
        echo '<div class="collections">';
        echo '<h2>Коллекции:</h2>';

		foreach ($item->getVisibleCollections() as $row)
		{
			if (!$row->photoTotal)
				continue;
            echo '<h3>'.CHtml::link($row->name, array('/vitrinaCollection/show/', 'collectionId'=>$row->id), array('class'=>'name red')).'&nbsp;<span>'.CHtml::link('смотреть все', array('/vitrinaCollection/show/', 'collectionId'=>$row->id), array('class'=>'')).'&nbsp;<b>обновлено '.DateUtils::_date($row->updated, 'dm').'</b></span></h3>';
            if ($row->photos)
            {
                    $i = 0;
                    echo '<ul class="gallery">';
                    foreach ($row->photos as $_photo)
                    {
                        echo '<li>';
                        $imageTag = VHtml::thumb($_photo->src, array(72, 100), VHtml::SCALE_EXACT, array('title'=>$_photo->name, 'alt'=>$_photo->name));
                        echo CHtml::link($imageTag, array('/vitrinaCollection/show/', 'collectionId'=>$row->id,'photoId'=>$_photo->id), array());
                        echo '</li>';
                        $i++;
                        if ($i>=9)
                            break;
                    }
                    echo '</ul>';
            }
	    }
        echo '</div>';
    }
    ?>

        </div>
          <div class="left-col">
              <?php
                  $this->widget('application.widgets.VitrinaSectionsTreeWidget', array(
                      'structure' => $mallsStructure,
                      'selectedSections' => $selectedMalls,
                      'route' => array('/vitrinaShop/index'),
                      'routeIndex' => 'mallId',
                      'counters' => $counters,
                  ));
              ?>
            <div class="vk">
                <?php $this->renderPartial('application.views.blocks.social', array()); ?>
            </div>
          </div>
      </div>
    </div>

<script type="text/javascript">
    $(document).ready(function(){

        $("a.showMap").click(function(){
            return false;
        });


    });
</script>