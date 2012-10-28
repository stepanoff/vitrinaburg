<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
    <title>message</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
</head>
<body>
<p>Здравствуйте, </p>

<p>
    Посетитель сайта <?php echo Yii::app()->params['siteName']; ?> оставил заявку на вступление.
</p>

<p>
    Контакты:
    <?php
    foreach ($item->messageFields() as $k=>$v)
    {
        echo '<br><b>'.$v.':</b> '.$item->$k;
    }
    ?>
</p>

</body>
</html>

