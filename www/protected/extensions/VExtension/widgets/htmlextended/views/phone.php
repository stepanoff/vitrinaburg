<?php
echo CHtml::activeTextField($model, $attribute);
?>
<script type="text/javascript">
jQuery(function($) {
    $.mask.definitions['~']='[+-]';
    $('#<?php echo $inputId ?>').mask('+7 (999) 999-99-99');
});
</script>