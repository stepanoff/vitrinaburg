<?php
if ($structure)
{
    echo '<ul class="cat-menu">';
    foreach ($structure as $mainNode)
    {
        $total = isset($counters[$mainNode['id']]) ? $counters[$mainNode['id']] : 0;
        if ($showNotEmpty && !$total)
                continue;

        $opened = in_array($mainNode['id'], $selected) ? true : false;
        $class = $opened ? 'cm-l1 opened-' : 'cm-l1';
        echo '<li class="'.$class.'">';
        echo CHtml::link($mainNode['name'], ($route+array( $routeIndex => $mainNode['id'] )), array('class'=>'cm-l1'));
        if ($total)
            echo  ' <small class="cm-l1">('.$total.')</small>';
        // дети
        if ($opened && $mainNode['children'])
        {
            echo '<ul class="cm-submenu">';
            foreach ($mainNode['children'] as $childNode)
            {
                $total = isset($counters[$childNode['id']]) ? $counters[$childNode['id']] : 0;
                if ($showNotEmpty && !$total)
                        continue;
                $opened = in_array($childNode['id'], $selected) ? true : false;
                $class = $opened ? ($selectedSection == $childNode['id'] ? 'cm-l2 opened- active- gradient1' : 'cm-l2 opened- active- ') : 'cm-l2';
                echo '<li class="'.$class.'">';
                echo CHtml::link($childNode['name'], ($route+array( $routeIndex => $childNode['id'] )), array('class'=>'cm-l2'));
                if ($total)
                    echo  ' <small class="cm-l1">('.$total.')</small>';

                // дети второго уровня
                if ($opened && $childNode['children'])
                {
                    foreach ($childNode['children'] as $subChildNode)
                    {
                        $total = isset($counters[$subChildNode['id']]) ? $counters[$subChildNode['id']] : 0;
                        if ($showNotEmpty && !$total)
                                continue;
                        $opened = in_array($subChildNode['id'], $selected) ? true : false;
                        $class = $opened ? 'cm-l3 active- gradient1' : 'cm-l3';
                        echo '<li class="'.$class.'">';
                        echo CHtml::link($subChildNode['name'], ($route+array( $routeIndex => $subChildNode['id'] )), array('class'=>''));
                        if ($total)
                            echo  ' <small class="cm-l1">('.$total.')</small>';
                        echo '</li>';
                    }

                }
                echo '</li>';
            }
            echo '</ul>';
        }
        echo '</li>';

    }
    echo '</ul>';
}
?>
