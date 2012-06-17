if ((typeof Vauth == "undefined") || !Vauth) {
	// инициализация объекта Vauth
    var Vauth = {
        'ajax': true,
        'hash': '',
        'iframe': false,
        'popup': false,
        'redirectUrl' : false,
        'loaded' : false,
        'pageUrl' : false,
        'containerClass' : 'auth',
        'shadowClass' : 'auth-shadow',
        'serviceClass' : 'auth-service-'
    };
}

Vauth.hashParser = function () {
	var func, param;
	try {
		var hash = window.location.hash.substr(1);
		var commands = hash.split(';');
		// набор якорь, функция для обработки нажатий по ссылкам
		var callbacks = [
            ['reload:', 'reloadPage'],
            ['cancel:', 'authCanceled']
		];
		// если хеш новый
		if (hash != Vauth.hash) {
			for (var k=0; k<commands.length; k++) {
				// вызов нужного callback в зависимости от переданного якоря
				for (var i=0; i<callbacks.length; i++) {
					func = callbacks[i][1];
					param = commands[k].substr(callbacks[i][0].length);

					if (commands[k].indexOf(callbacks[i][0])===0) {
						Vauth[func](param);
					}
				}
			}
			Vauth.hash = hash;
		}
	} catch (e) {}
}

Vauth.reloadPage = function (result) {
    if (!result)
        return;
    if (!this.iframe)
        window.location.href = this.pageUrl;
    else
        parent.location.href = this.pageUrl;
}

Vauth.authCanceled = function (service) {
    if (service)
        alert('service '+service+' canceled');
    return false;
}

Vauth.init = function (options) {
    for (i in options)
        this[i] = options[i];
    this.loaded = false;
    if (this.pageUrl == false)
        this.pageUrl = window.location.href;
    if (this.iframe && !this.redirectUrl)
    {
        alert ('redirectUrl not found. Initalization failed');
    }

    Vauth.addEvent(document, 'keydown', function(e) {
        e = e || window.event;
        if (e.keyCode == 27) {
            Vauth.close();
        }
        return true;
    });

    this.loaded = true;
    return true;
}

Vauth.launch = function (service) {
    service = service ? service : false;
    $("."+this.containerClass).show();
    $("."+this.shadowClass).show();
    if (service) {
        $(".auth-service-"+service).find('a').trigger('click');
    }
    return false;
}

Vauth.close = function () {
    $("."+this.containerClass).hide();
    $("."+this.shadowClass).hide();
    return false;
}

Vauth.addEvent = function (obj, type, fn){
	if (obj.addEventListener){
	      obj.addEventListener( type, fn, false);
	} else if(obj.attachEvent) {
	      obj.attachEvent( "on"+type, fn );
	} else {
	      obj["on"+type] = fn;
	}
}

hashChangeTimer = setInterval(Vauth.hashParser, 500);

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
                url += (url.indexOf('?') >= 0 ? '&' : '?') + 'js=1';
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