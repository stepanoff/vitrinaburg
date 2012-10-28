<div class="auth" style="width: <?php echo $width;?>px; height: <?php echo $height;?>px;">
    <div class="auth-error"></div>
    <div class="auth-processs">
        <div class="auth-processs-content">
        </div>
        <div>
            <a class="auth-process-stop" href="">Отмена</a>
        </div>
    </div>
    <div class="services">
        <div class="auth-services clear">
        <?php
            foreach ($services as $name => $service) {
                $this->renderInternal(Yii::getPathOfAlias($serviceTemplates[$name]).'.php', array(
                    'service' => $service,
                    'action' => $action,
                    'name' => $name,
                ));
            }
        ?>
        </div>
    </div>
</div>