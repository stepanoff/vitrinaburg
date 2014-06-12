<?php
if ($isAdmin) {
    ?>
<div class="b-cb">
    <?php
    echo CHtml::link('e', array($route.'/VCb/edit', 'id' => $cb->id), array('class' => 'b-cb__btn-edit'));
}

echo $cb->content;

if ($isAdmin) {
    ?>
</div>
    <?
}
?>