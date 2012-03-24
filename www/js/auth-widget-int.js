/**
 * GporAuth widget internal
 */
if ((typeof GporAuth == "undefined") || !GporAuth) {
	// инициализация объекта GporAuth
    var GporAuth = {
    	'loaded': false,
        'ajax': true,
        'mobile': false,
        'callback': null,
        'service': null,
        'hash': ''
    };
}

GporAuth.hashParser = function () {
	var func, param;
	try {
		var hash = window.location.hash.substr(1);
		var commands = hash.split(';');
		// набор якорь, функция для обработки нажатий по ссылкам
		var callbacks = [
		    ['token:', 'getToken'],
            ['cancel:', 'authCanceled']
		];
		// если хеш новый
		if (hash != GporAuth.hash) {
			for (var k=0; k<commands.length; k++) {
				// вызов нужного callback в зависимости от переданного якоря
				for (var i=0; i<callbacks.length; i++) {
					func = callbacks[i][1];
					param = commands[k].substr(callbacks[i][0].length);

					if (commands[k].indexOf(callbacks[i][0])===0) {2
						GporAuth[func](param);
					}
				}
			}
			GporAuth.hash = hash;
		}
	} catch (e) {}
}

GporAuth.getToken = function (token) {
    alert(token);
//	GporAuth.close();
//	GporAuth.callback(token);
}

GporAuth.authCanceled = function (service) {
    if (service)
        alert('service '+service+' canceled');
    return false;
//	GporAuth.close();
//	GporAuth.callback(token);
}

hashChangeTimer = setInterval(GporAuth.hashParser, 500);

/*
 * Yii EAuth extension.
 * @author Maxim Zemskov
 * @link http://code.google.com/p/yii-eauth/
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
jQuery(function($) {
    var popup;

	$.fn.eauth = function(options) {
		options = $.extend({
			id: '',
			popup: {
				width: 450,
				height: 380
			}
		}, options);

		return this.each(function() {
		    var el = $(this);
		    el.click(function() {
	            if (popup !== undefined)
	                popup.close();

	            var redirect_uri, url = redirect_uri = this.href;
				url += (url.indexOf('?') >= 0 ? '&' : '?') + 'redirect_uri=' + encodeURIComponent(redirect_uri);
				url += '&js';

	            /*var remember = $(this).parents('.auth-services').parent().find('.auth-services-rememberme');
	            if (remember.size() > 0 && remember.find('input').is(':checked'))
					url += (url.indexOf('?') >= 0 ? '&' : '?') + 'remember';*/

	            var centerWidth = ($(window).width() - options.popup.width) / 2;
	            var centerHeight = ($(window).height() - options.popup.height) / 2;

	            popup = window.open(url, "yii_eauth_popup", "width=" + options.popup.width + ",height=" + options.popup.height + ",left=" + centerWidth + ",top=" + centerHeight + ",resizable=yes,scrollbars=no,toolbar=no,menubar=no,location=no,directories=no,status=yes");
	            popup.focus();

                WatchPopupWindowClosed = function () {
                    if (!popup || popup.closed)
                    {
                        clearInterval(popupWindowTimer); //stop the timer
                        alert ('service canceled');
                    }
                }
                popupWindowTimer = setInterval(WatchPopupWindowClosed,500);

	            return false;
	        });
		});
	};
});