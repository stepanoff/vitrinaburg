<?php
if ($userData)
{
    ?>
    <p>
        <h2>Вы авторизованы</h2>
        <ul>
            <li>id: <?php echo $userData['id']; ?></li>
            <li>name: <?php echo $userData['name']; ?></li>
            <li>service: <?php echo $userData['service']; ?></li>
            <li>serviceId: <?php echo $userData['serviceId']; ?></li>
        </ul>
    </p>
    <?php
}

$this->widget('ext.gporauth.GporAuthWidget', $data);
?>