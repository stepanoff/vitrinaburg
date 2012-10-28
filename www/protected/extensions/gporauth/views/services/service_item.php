<?php
            echo '<div class="auth-service auth-service-'.$service->getServiceName().'">';
            $html = '<span class="auth-icon '.$service->getServiceName().'"><i></i></span>';
            $html .= '<span class="auth-title">'.$service->getServiceTitle().'</span>';
            $html = CHtml::link($html, array($action, 'service' => $name), array(
                'class' => 'auth-link '.$service->getServiceName(),
            ));
            echo $html;
            echo '</div>';

            echo '<div class="auth-service-process auth-service-process-'.$service->getServiceName().'">';
            $html = '<div>Идет авторизация...</div>';
            $html .= '<span class="auth-title">'.$service->getServiceTitle().'</span>';
            echo $html;
            echo '</div>';
?>