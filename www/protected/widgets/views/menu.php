<?php 
// старая верстка
if (0) {
	?>
<ul>
<?
foreach ($items as $item)
{
    $opts = array();
    $liClass = $item['active'] ? 'active- gradient1-revert' : '';
    echo '<li class="'.$liClass.'">';
    echo CHtml::link($item['caption'], $item['link'], $opts);
    echo '</li>';
}
?>
</ul>
	<?php
}
?>
<nav>
	<ul>
<?
foreach ($items as $item)
{
    $opts = array();
    $liClass = $item['active'] ? 'active' : '';
    echo '<li class="'.$liClass.'">';
    echo CHtml::link($item['caption'], $item['link'], $opts);
    echo '</li>';
    // пример выпадающего меню
    /*
    echo '
    					<ul>
							<li><a href="#">Одежда <span>(321)</span></a></li>
							<li><a href="#">Верхняя одежда <span>(321)</span></a></li>
							<li><a href="#">Белье, купальники <span>(321)</span></a></li>
							<li><a href="#">Акссесуары <span>(321)</span></a></li>
							<li><a href="#">Одежда больших размеров <span>(321)</span></a></li>
						</ul>';
	*/
}
?>
	</ul>
</nav>