if ((typeof GporAuth == "undefined") || !GporAuth) {
    var GporAuth = {
    	'loaded': false,
        'providers_set': null,
        'redirectUrl': null,
        'returnUrl': null,
        'service_host': null,
        'ajax': true,
        'mobile': false,
        'callback': null,
        'hash': '',
        'width': '600',
        'height': '400',
        'url' : ''
    };
}
GporAuth.show = function () {
    /*
	// мобильная версия
	GporAuth.mobile = GporAuth.getQueryStringValue(this, 'mobile');
	// определение устройства
	if (GporAuth.mobile == 'auto') {
		var nav = window.navigator.userAgent;
		var mobua = ['iPhone', 'Android', 'iPad', 'Opera Mobi', 'Kindle/3.0'];
		GporAuth.mobile = false;
		for (var i=0; i<mobua.length; i++){
			if (nav.indexOf(mobua[i]) >= 0) {
				GporAuth.mobile = true;
				break;
			}
		}
	} else if (GporAuth.mobile) {
		GporAuth.mobile = true;
	} else {
		GporAuth.mobile = false;
	}
	*/
    GporAuth.mobile = false;

	if (!GporAuth.mobile && !GporAuth.loaded) {
		var cldDiv = document.createElement("div");
		cldDiv.id = 'gpor_auth_form';
		cldDiv.style.overflow = 'visible';
		cldDiv.style.backgroundColor = 'transparent';
		cldDiv.style.zIndex = '10000';
		cldDiv.style.position = 'fixed';
		cldDiv.style.display = 'block';
		cldDiv.style.top = '0px';
		cldDiv.style.left = '0px';
		cldDiv.style.textAlign = 'center';
		cldDiv.style.height = '878px';
		cldDiv.style.width = '1247px';
		cldDiv.style.paddingTop = '125px';
		//cldDiv.style.backgroundImage = 'url('+GporAuth.service_host+'/img/widget/overlay.png)';
		
		var cntDiv = document.createElement("div");
		cntDiv.style.position = 'relative';
		cntDiv.style.display = 'inline';
		cntDiv.style.overflow = 'visible';
		
		var img = document.createElement("img");
		img.onclick = GporAuth.close;
		img.style.position = 'relative';
		img.style.left = '348px';
		img.style.top = '-332px';
		img.style.cursor = 'hand';
		img.style.width = '7px';
		img.style.height = '7px';
		img.style.border = '0';
		img.alt = 'X';
		img.title = 'Close';
		img.src = GporAuth.service_host+'/img/widget/close.gif';
		
		var iframe = document.createElement("iframe");
		iframe.id = 'gpor_auth_main_ifr';
        iframe.name = 'gpor_auth_main_ifr';
		iframe.width = GporAuth.width;
		iframe.height = GporAuth.height;

		iframe.scrolling = 'no';
		iframe.frameBorder = '0';
        iframe.src = 'javascript:"<html><body style=background-color:transparent><h1>Loading...</h1></body></html>"';

		// appends
		cntDiv.appendChild(img);
		cldDiv.appendChild(cntDiv);
		cldDiv.appendChild(iframe);

		try {
			cldDiv.style.paddingTop = (window.innerHeight-350)/2 + 'px';
		} catch (e) {
			cldDiv.style.paddingTop = '100px';
		}
		cldDiv.style.paddingLeft = 0;
		cldDiv.style.height = '2000px';
		cldDiv.style.width = document.body.clientWidth + 50 + 'px';
		// создание контейнера для формы
		document.body.appendChild(cldDiv);
		// форма загружена
		GporAuth.loaded = true;
	}

    var gpor_auth_url = GporAuth.service_host+'/?width='
    +GporAuth.width+'&height='+GporAuth.height+
    '&providers_set='+encodeURIComponent(GporAuth.providers_set)+
    '&redirectUrl='+encodeURIComponent(GporAuth.redirectUrl)+
    '&returnUrl='+encodeURIComponent(GporAuth.returnUrl)+
    '&iframe=true'+
    '&ajax='+(GporAuth.ajax ? 'true' : 'false')+
    (GporAuth.mobile ? '&mobile=true' : '');

		if (GporAuth.mobile) {
			document.location = gpor_auth_url;
		} else {
			document.getElementById('gpor_auth_main_ifr').setAttribute('src', gpor_auth_url);
		}

	if (!GporAuth.mobile) {
		// показать форму
		document.getElementById('gpor_auth_form').style.display = '';
	}
	return false;
}
GporAuth.close = function () {
	document.getElementById('gpor_auth_form').style.display = 'none';
}

GporAuth.resize = function () {
	var frm = document.getElementById('gpor_auth_form');
	if (frm) {
		frm.style.width = document.body.clientWidth + 50 + 'px';
		try {
			frm.style.paddingTop = (window.innerHeight-350)/2 + 'px';
		} catch (e) {
			frm.style.paddingTop = '100px';
		}
	}
}
GporAuth.getQueryStringValue = function (link, key) {
	var url_str = link.href;
    var match = null;
    var query_str = url_str.match(/^[^?]*(?:\?([^#]*))?(?:$|#.*$)/)[1]
    var _query_regex = new RegExp("([^=]+)=([^&]*)&?", "g");
    while ((match = _query_regex.exec(query_str)) != null)
    {
        if (decodeURIComponent(match[1]) == key) {
            return decodeURIComponent(match[2]);
        }
    }
    return '';
}
GporAuth.findClass = function (str, node) {
	if(document.getElementsByClassName) return (node || document).getElementsByClassName(str);
	else {
		var node = node || document, list = node.getElementsByTagName('*'), length = list.length, Class = str.split(/\s+/), classes = Class.length, array = [], i, j, key;
		for(i = 0; i < length; i++) {
			key = true;
			for(j = 0; j < classes; j++) if(list[i].className.search('\\b' + Class[j] + '\\b') == -1) key = false;
			if(key) array.push(list[i]);
		}
		return array;
	}
}
GporAuth.addEvent = function (obj, type, fn){
	if (obj.addEventListener){
	      obj.addEventListener( type, fn, false);
	} else if(obj.attachEvent) {
	      obj.attachEvent( "on"+type, fn );
	} else {
	      obj["on"+type] = fn;
	}
}
GporAuth.run = function (callBack) {
    return false;
}

GporAuth.init = function (options) {

    options = options ? options : false;
    if (options)
    {
        for (i in options)
            this[i] = options[i];
    }

	// обработчик на открытие формы
	if (document.getElementById('gpor_auth') && document.getElementById('gpor_auth').href != undefined) {
		document.getElementById('gpor_auth').onclick = GporAuth.show;
	}
    /*
	var i, list = GporAuth.findClass('gpor_auth'), length = list.length;
	for(i = 0; i < length; i++) {
		if (list[i].href != undefined) {
			list[i].onclick = GporAuth.show;
		}
	}
	*/
	// прочие обработчики
	GporAuth.addEvent(window, 'resize', GporAuth.resize);
	GporAuth.addEvent(document, 'keydown', function(e) {
		e = e || window.event;
		if (e.keyCode == 27) {
			GporAuth.close();
		}
		return true;
	});
}
GporAuth.widget = function () {
	var iframeNode = document.getElementById('gpor_auth_main_ifr');
	if (iframeNode.contentDocument)
    {
        return iframeNode.contentDocument;
    }
	if (iframeNode.contentWindow)
    {
        return iframeNode.contentWindow.document;
    }
	return iframeNode.document;
}