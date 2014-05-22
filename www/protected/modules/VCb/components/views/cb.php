<?php
if ($isAdmin) {
    ?>
<div class="b-cb">
     <a class="b-cb__btn-edit" href="#"></a>
    <?
}

echo $cb->content;

if ($isAdmin) {
    ?>
</div>
    <?
}
?>
