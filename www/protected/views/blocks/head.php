<?php
$user = false;
if (Yii::app()->user->id)
    $user = Yii::app()->user->getUser();
?>
<!DOCTYPE HTML>
<html lang="en-US">
<head>
  	<meta charset="utf8" />
  	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="description" content="" />
  	<meta name="author" content="" />
  	<meta content="width = 1150" name="viewport">

	<?php
		$this->widget('application.widgets.VitrinaShareWidget', array(
			'template' => 'shareHeaders',
		));
	?>

  	<link rel="icon" href="/favicon.ico" type="image/x-icon">
	<script src="<?php echo Yii::app()->request->staticUrl; ?>/js/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->staticUrl; ?>/js/jquery-ui-1.8.1.custom.min.js"></script>
  	<title><?php echo $this->pageTitle; ?></title>
	<link rel="stylesheet" href="<?php echo Yii::app()->request->staticUrl; ?>/layout.css" media="all">
	<link rel="stylesheet" href="<?php echo Yii::app()->request->staticUrl; ?>/css/old.css" media="all">
    <link type="text/css" rel="stylesheet" href="<?php echo Yii::app()->request->staticUrl; ?>/css/jquery_ui/jquery-ui-1.8.1.custom.css" />
	<!--[if lt IE 9]>
	<script src="<?php echo Yii::app()->request->staticUrl; ?>js/html5.js"></script>
		<script src="<?php echo Yii::app()->request->staticUrl; ?>/js/ie.js"></script>
		<link rel="stylesheet" href="<?php echo Yii::app()->request->staticUrl; ?>/ie.css" media="all" />
	<![endif]-->
 	<script src="<?php echo Yii::app()->request->staticUrl; ?>/js/faq.js"></script>
 	<script src="http://api-maps.yandex.ru/1.1/index.xml?key=<?php echo Yii::app()->params['yandexMapsKey']; ?>" type="text/javascript"></script>
 	<script src="<?php echo Yii::app()->request->staticUrl; ?>/js/jquery.jcarousel.min.js"></script>
 	<script src="<?php echo Yii::app()->request->staticUrl; ?>/js/maps.js"></script>
  <script src="<?php echo Yii::app()->request->staticUrl; ?>/js/funcs.js"></script>
</head>
<body>
	<header>
		<div class="inner">
			<a class="logo" href="/">Екатеринбург</a>
	        <?php
	        $this->widget('VitrinaMenuWidget', array('uri' => $this->getData('rootSectionUri') ));
	        ?>
			<div class="log">
		        <?php
		        if ($user)
		        {
		        	echo VHtml::userLink($user);
		        	?>
				<!--a href="#">Александр Петрович</a-->
				<ul>
					<li><a href="<?php echo CHtml::normalizeUrl(array('/user/favorite', 'returnUrl'=>Yii::app()->request->requestUri)); ?>"><b>Избранное</b> <span>(321)</span></a></li>
					<li><a href="<?php echo CHtml::normalizeUrl(array('/user/settings', 'returnUrl'=>Yii::app()->request->requestUri)); ?>">Настройки</a></li>
					<li><a href="<?php echo CHtml::normalizeUrl(array('/site/logout', 'returnUrl'=>Yii::app()->request->requestUri)); ?>">Выход</a></li>
				</ul>
		        	<?php
		        }
		        else
		        {
		            ?>
		            <!--div class="social">
		              <a class="auth-vk" href="#" onclick="Vauth.launch('vkontakte');" title="вконтакте">вконтакте</a>
		              <a class="auth-fb" href="#" onclick="Vauth.launch('facebook');" title="facebook">facebook</a>
		              <a class="auth-tw" href="#" onclick="Vauth.launch('twitter');" title="twitter">twitter</a>
		            </div-->
		            <a href="#" onclick="Vauth.launch();">войти на сайт</a>
		            <!--a href="#">зарегистрироваться</a> -->
		            <?php
		        }
		        ?>
			</div>
			
			
			<div class="clr"></div>
		</div>
	</header>
