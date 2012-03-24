/**
 * GporAuth widget internal
 */
if ((typeof GporAuth == "undefined") || !GporAuth) {
	// инициализация объекта GporAuth
    var GporAuth = {
        'ajax': true,
        'hash': '',
        'iframe': false,
        'popup': false,
        'redirectUrl' : false,
        'returnUrl' : false,
        'loaded' : false
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
    this.hash = token;
    this.redirectUrl += (this.redirectUrl.indexOf('?') >= 0 ? '&' : '?') + 'auth_token=' + encodeURIComponent(token) + '&returnUrl=' + encodeURIComponent(this.returnUrl);
    if (!this.iframe)
        window.location.href = this.redirectUrl;
    else
        parent.location.href = this.redirectUrl;
}

GporAuth.authCanceled = function (service) {
    if (service)
        alert('service '+service+' canceled');
    return false;
}

GporAuth.init = function (options) {
    for (i in options)
        this[i] = options[i];
    this.loaded = false;
    if (this.iframe && !this.redirectUrl)
    {
        alert ('redirectUrl not found. Initalization failed');
    }
    this.loaded = true;
    return true;
}

hashChangeTimer = setInterval(GporAuth.hashParser, 500);

jQuery(function($) {
    var popup;
    var serviceContainer = $('.services');
    var processContainer = $('.auth-processs');
    var errorContainer = $('.auth-error');
    var processContent = $('.auth-processs').find('.auth-processs-content');
    var in_process = false;

	$.fn.auth_service = function(options) {
		options = $.extend({
			id: '',
			popup: {
				width: 450,
				height: 380
			},
            actionType: 'popup'
		}, options);

        var openPopup = function (el, url)
        {
            if (popup !== undefined)
                popup.close();

            if (!url)
            {
                var redirect_uri, url = redirect_uri = $(el).attr('href');
                //url += (url.indexOf('?') >= 0 ? '&' : '?') + 'redirect_uri=' + encodeURIComponent(redirect_uri);
                url += (url.indexOf('?') >= 0 ? '&' : '?') + '&js';
            }

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
                    stopProcess();
                }
            }
            popupWindowTimer = setInterval(WatchPopupWindowClosed,500);

            return false;
        }

        var sendForm = function (el)
        {
            var popupUrl = false;
            formObj = $(el).closest('form');
            formData = $(formObj).serialize();
            in_process = true;
            $.ajax({
                    url: $(formObj).attr('action'),
                    data: formData,
                    type: 'post',
                    async: false,
                    dataType: 'json',
                    success: function(result) {
                       in_process = false;
                       if (result.success)
                       {
                           if (result.redirect_url)
                           {
                               window.location.href = result.redirect_url;
                               return false;
                           }
                           if (result.popupUrl)
                           {
                               popupUrl = result.popupUrl;
                               //openPopup(false, result.popupUrl);
                           }
                       }
                       else
                       {
                           showError (result);
                       }
                    }
            });
            if (popupUrl)
                openPopup(false, popupUrl);
            return false;
        }

        var stopProcess = function ()
        {
            //errorContainer.hide();
            processContainer.hide();
            processContent.html('');
            serviceContainer.show();
            if (popup !== undefined)
                popup.close();
            in_process = false;
            return false;
        }

        var startProcess = function ()
        {
            if (in_process)
                return false;

            errorContainer.hide();
            el = $(serviceContainer).find('.auth-service-process-'+options.id);
            if (el.eq(0))
            {
                htmlContent = $(el).html();
                processContent.html(htmlContent);
                processContainer.show();
                serviceContainer.hide();
            }
            in_process = true;
            return true;
        }

        var showError = function (data)
        {
            var message = 'Ошибка авторизации. Попробуйте еще раз позже';
            if (data.error)
            {
                message = data.error.message;
            }
            errorContainer.html(message);
            errorContainer.show();
        }

        $(processContainer).find('.auth-process-stop').click(function(){
            stopProcess();
        })

        return this.each(function() {
            if (options.actionType == 'popup')
            {
                var el = $(this).find('a');
                el.click(function() {
                    if (startProcess(el))
                    {
                        openPopup ($(this));
                        return false;
                    }
                });
            }
            if (options.actionType == 'processForm')
            {
                var el = $(this).find('a');
                el.click(function() {
                    if (startProcess(el))
                    {
                        formSubmit = processContainer.find('.auth-service-submit');
                        formSubmit.click(function() {
                            startProcess(el);
                            sendForm($(this));
                            return false;
                        });
                    }
                    return false;
                });
            }
            if (options.actionType == 'sendForm')
            {
                formSubmit = $('.auth-service-'+options.id).find('.auth-service-submit');
                formSubmit.click(function() {
                    sendForm($(this));
                    return false;
                });
            }

        });

	};
});