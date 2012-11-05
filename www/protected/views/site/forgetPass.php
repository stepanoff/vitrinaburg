<?php
    echo '<h1>Вспомнить пароль</h1>';

    if ($form) {
        ?>
    <p>Укажите почтовый адрес, который вы указывали при регистрации на сайте.</p>
    <div class="comment-form">
        <?php $this->widget('application.extensions.VExtension.widgets.VFormBuilderWidget', array('form'=>$form)); ?>
    </div>
        <?php
    }
    echo $text;
?>
