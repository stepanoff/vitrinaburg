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
		$.fn.vfavorites_refresh = function(opts){
            var id = $(this).attr('id')
            if ($.fn.vfavorites.instances[id]) {
                $.fn.vfavorites.instances[id].update(opts);
            }
		};

		// default options
		$.fn.vfavorites.defaults = {
            'isFavorite' : false,
			'isAuthorized' : false,
            'link' : false,
			'isFavoriteClass' : 'in',
			'isFavoriteLabel' : 'В избранном',
			'toFavoriteLabel' : 'В избранное',
            'onAuthorize' : false,
            'onToggle' : false
		};

		var VFavorites = function(obj, o, instance_id){

			var obj = $(obj);
            var isFavorite = false;
            var isAuthorized = false;
            var inProgress = false;

            this.update = function (opts) {
                o = $.extend(o, opts);
                isFavorite = o.isFavorite;
                isAuthorized = o.isAuthorized ? true : false;
                inProgress = false;
                if (o.link !== false) {
                    obj.attr('href', o.link);
                }
                setState(isFavorite);
            }


            var showAuthMessage = function () {
                if (o.onAuthorize)
                    o.onAuthorize();
                else
                    alert ("Чтобы добавлять в избранное вы должны войти на сайт");
                return false;
            }

            var setState = function (state) {
                if (state) {
                    isFavorite = true;
                    obj.addClass(o.isFavoriteClass);
                    obj.html(o.isFavoriteLabel);
                } else {
                    isFavorite = false;
                    obj.removeClass(o.isFavoriteClass);
                    obj.html(o.toFavoriteLabel);
                }
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
                        if (result.success) {
                            var state = result.result == 'on' ? true : false;
                            setState (state);
                            inProgress = false;
                            if (o.onToggle) {
                                o.onToggle(state);
                            }
                        }
                        else {
                            if (result['error']) {
                            	alert(result['error']);
                            }
                            else {
                                alert ("Ошибка. Попробуйте позже");
                            }
                            inProgress = false;
                        }
                    }
                });
                return false;
            };

            obj.click(function(){
                toggleFavorite();
                return false;
            });
            this.update();

		};

	})(jQuery);


var VAuthLaunch = function() {
	Vauth.launch(); return false;
}
$(document).ready(function() {
	$('#tofav2').vfavorites({
		'isAuthorized' : <?php echo $userId ? 'true' : 'false'; ?>,
        'onToggle' : <?php echo $jsCallback ? $jsCallback : 'false'; ?>,
		'onAuthorize' : VAuthLaunch
	});
});
</script>


