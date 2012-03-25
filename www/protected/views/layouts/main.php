<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?php echo $this->pageTitle; ?></title>
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/base.css">
  <link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/base_1.css">
  <!--[if lt IE 10]>
  <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie9fix.css" rel="stylesheet" />
  <![endif]-->
  <!--[if lt IE 9]>
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/normalize.ie.css" rel="stylesheet" />
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/iefix.css" rel="stylesheet" />
  <![endif]-->
  <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-1.7.1.min.js"></script>
  <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.jcarousel.min.js"></script>
  <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/funcs.js"></script>
</head>
<body>
  <div id="wrapper">
    <div id="header">
      <div class="base-width">
        <div class="top-banner"><a href="#"><img src="/images/must_be_deleted/top_banner.jpg" width="728" height="90" alt=""></a></div>
        <div class="auth-box">
          <div class="social">
            войти через:
            <a class="auth-vk" href="#" title="вконтакте">вконтакте</a>
            <a class="auth-fb" href="#" title="facebook">facebook</a>
            <a class="auth-tw" href="#" title="twitter">twitter</a>
            <a class="auth-gg" href="#" title="google+">google+</a>
          </div>
          <a href="#">войти на сайт</a><br>
          <a href="#">зарегистрироваться</a>
        </div>
        <a href="/"><img class="logo" src="/images/logo.png" width="275" height="47" alt="<?php echo Yii::app()->params['title']; ?>"  title="<?php echo Yii::app()->params['title']; ?>"></a>
      </div>
    </div>
    <div id="main-menu" class="gradient1">
      <div class="base-width">
        <form class="search-form" action="/">
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
        </form>
        <ul>
        <?
        $items = array (
            array ('link' => array('/vitrinaShop/index'), 'caption' => 'Магазины', 'regexp' => false),
            array ('link' => array('/vitrinaCollection/section', 'sectionId'=>311), 'caption' => 'Для женщин', 'regexp' => false),
            array ('link' => array('/vitrinaCollection/section', 'sectionId'=>313), 'caption' => 'Для мужчин', 'regexp' => false),
            array ('link' => array('/vitrinaCollection/section', 'sectionId'=>314), 'caption' => 'Для детей', 'regexp' => false),
            array ('link' => array('/vitrinaCollection/section', 'sectionId'=>315), 'caption' => 'Обувь', 'regexp' => false),
            array ('link' => array('/vitrinaAction/index'), 'caption' => 'Акции', 'regexp' => false),
            array ('link' => array('/vitrinaWidget/create'), 'caption' => 'Создать стиль', 'regexp' => false),
        );
        foreach ($items as $item)
        {
            echo '<li>';
            echo CHtml::link($item['caption'], $item['link'], array());
            echo '</li>';
        }
        ?>
        </ul>
      </div>
    </div>
    <!-- content -->
    <?php echo $content; ?>
    <!-- // content -->
  </div>
  <div id="footer" class="gradient1">
    <div class="base-width">
      <div class="counters">
        <a href="#"><img src="/images/must_be_deleted/counter.png" width="31" height="31" alt=""></a>
      </div>
      <div class="nav">
        <a href="#" class="people-link">72 пользователя он-лайн</a>
        <a href="#" class="people-link">250 гостей</a>
        <ul>
          <li><a href="#">О проекте</a></li>
          <li><a href="#">Размещение рекламы</a></li>
          <li><a href="#">Добавить магазин</a></li>
        </ul>
      </div>
      <div class="about">
        Витринабург - сайт об одежде в магазинах Екатеринбурга<br>
        Размещение рекламы и сотрудничество: (343) 345-93-27, <a href="mailto:adv@vitrinaburg.ru">adv@vitrinaburg.ru</a>
      </div>
    </div>
  </div>
</body>
</html>