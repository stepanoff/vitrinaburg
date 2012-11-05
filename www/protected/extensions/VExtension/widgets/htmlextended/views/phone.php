{{ [model, attribute]|static('CHtml', 'activeTextField')|raw }}
<script type="text/javascript">
jQuery(function($) {
    $.mask.definitions['~']='[+-]';
    $('#{{ inputId }}').mask('+7 (999) 999-99-99');
});
</script>