<?php
if ($items)
{
    echo '<ul class="cat-brends clearfix">';
    foreach ($items as $item)
    {
        echo '<li>';
        $imageTag = VHtml::thumb($item->logo, array(100, 74), VHtml::SCALE_EXACT, array('title'=>$item->name, 'alt'=>$item->name));
        echo CHtml::link($imageTag, array('/vitrinaShop/show/', 'id'=>$item->id) );
        echo '</li>';
    }
    echo '</ul>';
}
?>