<?php
    echo '<h2>Регистрация</h2>';
?>

<form class="" method="post" enctype="multipart/form-data" action="<?php echo $link; ?>">
<?php
    echo '<div>';
    echo CHtml::activeLabel($form, 'login');
    echo CHtml::activeTextField($form, 'login');
    echo '</div>';

    echo '<div>';
    echo CHtml::activeLabel($form, 'username');
    echo CHtml::activeTextField($form, 'username');
    echo '</div>';


    echo '<div>';
    echo CHtml::activeLabel($form, 'password');
    echo CHtml::activePasswordField($form, 'password');
    echo '</div>';

    echo '<div>';
    echo CHtml::activeLabel($form, 'passwordAgain');
    echo CHtml::activePasswordField($form, 'passwordAgain');
    echo '</div>';

    echo '<div>';
    echo '<input type="submit" class="auth-service-submit" value="Зарегистрироваться" />';
    echo '</div>';
    ?>
</form>
</div>
