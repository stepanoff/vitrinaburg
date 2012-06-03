<?php
    $form = $service->getForm();
    $link = CHtml::normalizeUrl(array($action, 'service' => $name));
    echo '<div class="custom-auth-service auth-service-'.$service->getServiceName().'">';
?>
<form class="" method="post" enctype="multipart/form-data" action="<?php echo $link; ?>">
<?php
    echo '<div class="auth-form-row">';
    echo CHtml::activeLabel($form, 'login');
    echo '<div class="auth-form-control">';
    echo CHtml::activeTextField($form, 'login');
    echo '</div>';
    echo '</div>';

    echo '<div class="auth-form-row">';
    echo CHtml::activeLabel($form, 'password');
    echo '<div class="auth-form-control">';
    echo CHtml::activePasswordField($form, 'password');
    echo '</div>';
    echo '</div>';

    echo '<div class="auth-form-row">';
    echo '<div class="auth-form-control">';
    echo CHtml::activeCheckbox($form, 'rememberMe');
    echo CHtml::activeLabel($form, 'rememberMe');
    echo '</div>';
    echo '</div>';

    echo '<div class="auth-form-row">';
    echo '<div class="auth-form-control">';
    echo '<input type="submit" class="auth-service-submit" value="Войти" />';
    echo '</div>';
    echo '</div>';
    ?>
</form>
</div>
<?php
echo '<div class="auth-service-process auth-service-process-'.$service->getServiceName().'">';
$html = '<div>Идет авторизация...</div>';
$html .= '<span class="auth-title">'.$service->getServiceTitle().'</span>';
echo '</div>';
echo '<p style="text-align: center; margin-top: 40px;">Войти с помощью:</p>';
?>