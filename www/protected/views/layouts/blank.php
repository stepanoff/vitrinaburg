<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
	<title><?php echo $this->pageTitle; ?></title>

    <script type="text/javascript" src="<?php echo Yii::app()->request->staticUrl; ?>/js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->staticUrl; ?>/js/jquery-ui-1.8.1.custom.min.js"></script>
    <link type="text/css" rel="stylesheet" href="<?php echo Yii::app()->request->staticUrl; ?>/css/jquery_ui/jquery-ui-1.8.1.custom.css" />

</head>

<body>
	<div id="content">
				<?php echo $content; ?>
	</div>
</body>
</html>