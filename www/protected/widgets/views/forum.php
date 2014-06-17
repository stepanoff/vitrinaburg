<?php
if ($discussions)
{
	?>
	<div id="forum">
	<?php
	echo '<div class="item">';
	echo '<a href="'.CHtml::normalizeUrl(array('/VForum/VForum/index')).'">Форум</a>';
	// echo ' <span>800 тем</span>';
	echo '</div>';
	foreach ($discussions as $discussion) {
		echo '<div class="item">';
		echo CHtml::link($discussion->title, array('/VForum/VForum/discussion', 'id'=>$discussion->id));
		echo '&nbsp;<span>' . $discussion->commentsTotal . '</span>';
		echo '</div>';
	}
	?>
	</div>
	<?php
}
