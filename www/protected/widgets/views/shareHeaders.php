<meta property="og:site_name" content="<?php echo Yii::app()->params['siteName']; ?>"/>
<?php 
if (!empty($options['linkText'])) {
	?>
<meta property="og:title" content="<?php echo $options['linkText']; ?>"/>
	<?php
}

if (!empty($options['type'])) {
	?>
<meta property="og:type" content="<?php echo $options['type']; ?>"/>
	<?php
}

if (!empty($options['imageUrl'])) {
	?>
<meta property="og:image" content="<?php echo $options['imageUrl']; ?>"/>
<link rel="image_src" href="<?php echo $options['imageUrl']; ?>"/>
	<?php
}
