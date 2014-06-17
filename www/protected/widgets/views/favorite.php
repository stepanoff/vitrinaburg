<?php 
if (!$userId) {
	echo CHtml::link('В избранное', $link, array('id' => 'tofav2', 'class' => 'notAuth'));
}
else if ($isFavorite) {
    echo CHtml::link('В избранном', $link, array('id' => 'tofav2', 'class' => 'in'));
}
else {
    echo CHtml::link('В избранное', $link, array('id' => 'tofav2', 'class' => ''));
}
?>

<script type="text/javascript">
	(function($){
		$.fn.vfavorites = function(opts){
		opts = $.extend({}, $.fn.vfavorites.defaults, opts);
			return this.each(function(){
				$.fn.vfavorites.instances[$(this).attr('id')] = new VFavorites(this, opts, $(this).attr('id') );
				return $.fn.vfavorites.instances[$(this).attr('id')];
			});
		};

		$.fn.vfavorites.instances = new Object();
		$.fn.vfavorites_refresh = function(){
		};

		// default options
		$.fn.vfavorites.defaults = {
			'isAuthorized' : false,
			'isFavoriteClass' : 'in',
			'isFavoriteLabel' : 'В избранном',
			'toFavoriteLabel' : 'В избранное',
            'onAuthorize' : false
		};

		var VFavorites = function(obj, o, instance_id){

			var obj = $(obj);
			var isFavorite = false;
            var isAuthorized = o.isAuthorized ? true : false;
			if (obj.hasClass(o.isFvoriteClass))
				isFavorite = true;
            var inProgress = false;

            var showAuthMessage = function () {
                if (o.onAuthorize)
                    o.onAuthorize();
                else
                    alert ("Чтобы добавлять в избранное вы должны войти на сайт");
                return false;
            }

            var toggleFavorite = function () {
                if (inProgress) {
                    return false;
                }
                if (!isAuthorized) {
                    showAuthMessage();
                    return false;
                }
                var url = obj.attr("href");

                inProgress = true;
                $.ajax({
                    url: url,
                    data: {},
                    type: 'post',
                    dataType: 'json',
                    success: function(result) {
                        if (result.success)
                        {
                        	if (result.result == 'on') {
                        		isFavorite = true;
                        		obj.addClass(o.isFavoriteClass);
                        		obj.html(o.isFavoriteLabel);
                        	} else {
                        		isFavorite = false;
                        		obj.removeClass(o.isFavoriteClass);
                        		obj.html(o.toFavoriteLabel);
                        	}
                            inProgress = false;
                        }
                        else
                            if (result['error'])
                            {
                            	alert(result['error']);
                            }
                            else
                            {
                                alert ("Ошибка. Попробуйте позже");
                            }
                            inProgress = false;
                        }
                    });
                    return false;
            };

            obj.click(function(){
                toggleFavorite();
                return false;
            });

		};

	})(jQuery);


var VAuthLaunch = function() {
	Vauth.launch(); return false;
}
$(document).ready(function() {
	$('#tofav2').vfavorites({
		'isAuthorized' : <?php echo $userId ? 'true' : 'false'; ?>,
		'onAuthorize' : VAuthLaunch
	});
});
</script>


