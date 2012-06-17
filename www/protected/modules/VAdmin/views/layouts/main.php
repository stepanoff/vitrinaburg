<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
<?php 
$cs=Yii::app()->clientScript;
$cs->registerScriptFile(Yii::app()->request->staticUrl.'js/jquery-lib.min.js', CClientScript::POS_HEAD);
$cs->registerScriptFile(Yii::app()->request->staticUrl.'js/jquery_ui.js', CClientScript::POS_HEAD);
$cs->registerScriptFile(Yii::app()->request->staticUrl.'js/common.js', CClientScript::POS_HEAD);
$cs->registerScriptFile(Yii::app()->request->staticUrl.'js/multiselect.js', CClientScript::POS_HEAD);
$cs->registerScriptFile(Yii::app()->request->staticUrl.'js/datepicker.js', CClientScript::POS_HEAD);
$cs->registerScriptFile(Yii::app()->request->staticUrl.'js/admin/common.js', CClientScript::POS_HEAD);

$cs->registerCssFile(Yii::app()->request->staticUrl.'css/admin.css');
?>
	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->staticUrl; ?>css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->staticUrl; ?>css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->staticUrl; ?>css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->staticUrl; ?>css/jquery-ui.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->staticUrl; ?>css/form.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->staticUrl; ?>css/multiselect.css" />

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<div class="container" id="admin_page">

	<div id="admin_header">
		<div id="admin_logo"><a href="/"><img src="/images/logo.png" width="180" height="29" alt="<?php echo CHtml::encode(Yii::app()->name); ?>" title="<?php echo CHtml::encode(Yii::app()->name); ?>"/></a></div>
	</div>

	<div id="admin_menu">
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'Меню сайта', 'url'=>array('/admin/adminMenu/')),
				array('label'=>'Страницы', 'url'=>array('/admin/contentpage/')),
				array('label'=>'Текстовые блоки', 'url'=>array('/admin/adminContentBlock/')),
				//array('label'=>'Обратная связь', 'url'=>array('/admin/requests')),
				array('label'=>'Админы', 'url'=>array('/admin/adminUsers')),
				array('label'=>'Выход', 'url'=>array('/site/logout')),
				),
		)); ?>
	</div>

	<?php
//	$this->widget('zii.widgets.CBreadcrumbs', array(
//	'links'=>$this->breadcrumbs,
//	));
	?>

<div class="container" style="width: 100%">
	<div id="content" class="admin_content">
		<?php echo $content; ?>
	</div>
</div>

	<div id="admin_footer">
		Copyright &copy; <?php echo date('Y'); ?> <?php echo Yii::app()->name; ?><br/>
		All Rights Reserved.<br/>
	</div>

</div>
		<?php 
		$cs->registerScript('amdin_page_ini', 'AdminPageInit();', CClientScript::POS_END);
		$items = Yii::app()->informer->getAlerts();
		$items2 = Yii::app()->informer->getUnread();
		if (is_array($items2) && sizeof($items2))
			$items = array_merge($items, $items2);
		$this->widget('application.widgets.InformerWidget',array(
			'items'=>$items,
		)); ?>
</body>
</html>