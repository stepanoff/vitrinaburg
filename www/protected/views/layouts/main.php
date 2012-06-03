<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <link rel="shortcut icon" href="/favicon.gif" />
  <title><?php echo $this->pageTitle; ?></title>
  <meta name="description" content="<?php echo CHtml::encode($this->pageDescription); ?>">
  <meta name="author" content="">
  <link rel="stylesheet" href="<?php echo Yii::app()->request->staticUrl; ?>/css/base.css">
  <link rel="stylesheet" href="<?php echo Yii::app()->request->staticUrl; ?>/css/base_1.css">
    <link rel="stylesheet" href="<?php echo Yii::app()->request->staticUrl; ?>/css/old.css">
    <link type="text/css" rel="stylesheet" href="<?php echo Yii::app()->request->staticUrl; ?>/css/jquery_ui/jquery-ui-1.8.1.custom.css" />
  <!--[if lt IE 10]>
  <link href="<?php echo Yii::app()->request->staticUrl; ?>/css/ie9fix.css" rel="stylesheet" />
  <![endif]-->
  <!--[if lt IE 9]>
    <link href="<?php echo Yii::app()->request->staticUrl; ?>/css/normalize.ie.css" rel="stylesheet" />
    <link href="<?php echo Yii::app()->request->staticUrl; ?>/css/iefix.css" rel="stylesheet" />
  <![endif]-->
  <script src="http://api-maps.yandex.ru/1.1/index.xml?key=<?php echo Yii::app()->params['yandexMapsKey']; ?>" type="text/javascript"></script>
  <script type="text/javascript" src="<?php echo Yii::app()->request->staticUrl; ?>/js/jquery-1.7.1.min.js"></script>
  <script type="text/javascript" src="<?php echo Yii::app()->request->staticUrl; ?>/js/jquery-ui-1.8.1.custom.min.js"></script>
  <script src="<?php echo Yii::app()->request->staticUrl; ?>/js/jquery.jcarousel.min.js"></script>
    <script src="<?php echo Yii::app()->request->staticUrl; ?>/js/maps.js"></script>
  <script src="<?php echo Yii::app()->request->staticUrl; ?>/js/funcs.js"></script>
</head>
<body>
  <div id="wrapper">
    <div id="header">
      <div class="base-width">
        <div class="top-banner">
            <?php $this->renderPartial('application.views.blocks.banner_top', array()); ?>
        </div>
        <div class="auth-box">
            <div class="social"></div>
            <?php
            //$this->widget('ext.VExtension.widgets.auth.VAuthWidget', array('action'=>'/site/login'));
            ?>
            <!-- войти через:
          <div class="social">
            <a class="auth-vk" href="#" title="вконтакте">вконтакте</a>
            <a class="auth-fb" href="#" title="facebook">facebook</a>
            <a class="auth-tw" href="#" title="twitter">twitter</a>
            <a class="auth-gg" href="#" title="google+">google+</a>
          </div>
          <a href="#">войти на сайт</a><br>
          <a href="#">зарегистрироваться</a> -->
        </div>
        <a href="/"><img class="logo" src="<?php echo Yii::app()->request->staticUrl; ?>/images/logo.png" width="275" height="47" alt="<?php echo Yii::app()->params['title']; ?>"  title="<?php echo Yii::app()->params['title']; ?>"></a>
      </div>
    </div>
    <div id="main-menu" class="gradient1">
      <div class="base-width">
        <!-- form class="search-form" action="/">
          <fieldset>
            <table>
              <tr>
                <td class="label"><label for="keywords">Поиск</label></td>
                <td class="inputs">
                  <div>
                    <input type="text" name="keywords" id="keywords">
                    <input class="gradient2" type="submit" value="Искать">
                  </div>
                </td>
              </tr>
            </table>
          </fieldset>
        </form -->
        <ul>
        <?php
        $this->widget('VitrinaMenuWidget', array('uri' => $this->getData('rootSectionUri')));
        ?>
        </ul>
      </div>
    </div>

    <!-- content -->
      <div id="<?php echo $this->mainPage ? 'main-page' : 'inner-page'; ?>">
          <div class="base-width clearfix">
              <div id="pageDescription">
              <?
              if ($this->seoText)
              {
                  echo $this->seoText;
              }
              ?>
              </div>
              <script type="text/javascript">
              $("#pageDescription").hide();
              </script>
              <script type="text/javascript">
              $(document).ready(function() {
                  $('#pageDescription').appendTo("#pageDescriptionFooter").show();
              });
              </script>

            <?php echo $content; ?>
            </div>
      </div>
    <!-- // content -->
  </div>
  <script type="text/javascript">
      <?php
      $counters = $this->renderPartial('application.views.blocks.counters', array(), true);
      ?>
       var counters = <?php echo CJSON::encode($counters); ?>;
       function reloadCounters ()
       {
           $('#counters').html('');
           $(document).bind('beforedocwrite', function(event, data) {
               data.target = $('#counters');
           });
           document.write(counters);
       }
  </script>
  <div id="footer" class="gradient1">
    <div class="base-width">
      <div class="counters" id="counters">
           <?php echo $counters; ?>
      </div>
      <div class="nav">
        <!--a href="#" class="people-link">72 пользователя он-лайн</a>
        <a href="#" class="people-link">250 гостей</a-->
        <?
        $links = array (
            array ('label' => 'О проекте', 'route' => array ('/staticPage/show', 'staticPage' => 'about')),
            array ('label' => 'Размещение рекламы', 'route' => array ('/staticPage/show', 'staticPage' => 'adv')),
            array ('label' => 'Добавить магазин', 'route' => array ('/staticPage/show', 'staticPage' => 'register')),
        );
        echo '<ul>';
        foreach ($links as $link)
        {
            echo '<li>'.CHtml::link($link['label'], $link['route']).'</li>';
        }
        echo '</ul>';
        ?>
      </div>
      <div class="about">
        Витринабург - сайт об одежде в магазинах Екатеринбурга<br>
        Размещение рекламы и сотрудничество: (343) 345-93-27, <a href="mailto:adv@vitrinaburg.ru">adv@vitrinaburg.ru</a>
      </div>
    </div>
  </div>
</body>
<script type="text/javascript">
$(document).ready(function() {
	$(".showMap").easy_map();
});
</script>
</html>