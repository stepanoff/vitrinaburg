<?php
            echo '<div class="auth-service auth-service-'.$service->getServiceName().'">';
            $html = '<span class="auth-icon '.$service->getServiceName().'"><i></i></span>';
            $html .= '<span class="auth-title">'.$service->getServiceTitle().'</span>';
            $html = CHtml::link($html, array($action, 'service' => $name), array(
                'class' => 'auth-link '.$service->getServiceName(),
            ));
            echo $html;
            echo '</div>';
 ?>
<div class="auth-service-process auth-service-process-<?php echo $service->getServiceName(); ?>">
    <?php
        $form = $service->getForm();
        $link = CHtml::normalizeUrl(array($action, 'service' => $name));
        $html = '';
        $html .= '<span class="auth-title">'.$service->getServiceTitle().'</span>';
        echo $html;
    ?>
    <form class="" method="post" enctype="multipart/form-data" action="<?php echo $link; ?>">
    <?php
        echo '<div>';
        echo CHtml::activeLabel($form, 'login');
        echo CHtml::activeTextField($form, 'login');
        echo '</div>';

        echo '<div>';
        echo '<input type="submit" class="auth-service-submit" value="Войти" />';
        echo '</div>';
        ?>
    </form>
</div>