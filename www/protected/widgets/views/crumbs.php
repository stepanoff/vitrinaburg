<?php
    if ($items)
    {
        echo '<div class="page-path">';
        foreach ($items as $item)
            echo CHtml::link($item['label'], $item['link']);
        echo '</div>';
    }
?>