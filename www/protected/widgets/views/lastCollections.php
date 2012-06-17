<?php
if ($items) {
    ?>
<div class="left-newColl clearfix">
  <h2>Новые коллекции</h2>
    <ul class="clearfix">
        <?php
        foreach ($items as $item)
        {
            echo '<li>';
            $imageTag = VHtml::thumb($item->src, array(110, 150), VHtml::SCALE_SMALLER_SIDE, array('title'=>$item->name, 'alt'=>$item->name));
            if ($item->cost)
                $imageTag .= VHtml::sum($item->cost, true, array('prefix' => '<span class="price">', 'postfix' => '</span>'));
            echo CHtml::link($imageTag, array('/vitrinaCollection/show/', 'collectionId'=>$item->collection->id,'photoId'=>$item->id), array());
            echo '</li>';
        }
        ?>
    </ul>
  </div>
    <?php
}
?>