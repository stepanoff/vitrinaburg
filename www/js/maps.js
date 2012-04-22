(function($){

	$.fn.easy_map = function(opts){
		opts = $.extend({}, $.fn.easy_map.defaults, opts);
		$.fn.easy_map.instance = new EasyMap(this, opts );
		return this.each(function(){
			$(this).bind('click', function(){
				$.fn.easy_map.instance.showYandexAddress(this);
			});
		});
	};
	
	$.fn.easy_map.instance = new Object();
	
	// default options
	$.fn.easy_map.defaults = {
		CLASS_EASY_MAP : 'easymap',
		title : "Екатеринбург"
	};

	var EasyMap = function(obj, o){
		var map, geoResult, placeMark, placeMark_e, s;
		var geoResult = false;
		var placeMark_e = false;
		
		mapContainer = $('<div>').addClass("").appendTo(document.body).hide();
		$(mapContainer).attr("id", "yandexMap");
		$(mapContainer).css({
            width: 600,
            height: 600,
        });
		
		map = new YMaps.Map(mapContainer);
        map.addControl(new YMaps.TypeControl());
        
        s = new YMaps.Style();
        s.iconStyle = new YMaps.IconStyle();
        s.iconStyle.offset = new YMaps.Point(-10, -10);
        s.iconStyle.href = "http://www.rabota66.ru/tpl/i/icon_error.gif";
        s.iconStyle.size = new YMaps.Point(20, 20);

        YMaps.Styles.add("example#customPoint", s);
        
		$(mapContainer).dialog({ 
			autoOpen: false, 
			modal: true, 
			resizable: false, 
			draggable: false, 
			width: 700, 
			height:600, 
			title:  o.title,
		});
		
		this.showYandexAddress = function (el)
		{
			addr = $(el).attr("addr");
			if (!addr)
				return;
			if (geoResult)
				map.removeOverlay(geoResult);
	        var geocoder = new YMaps.Geocoder('Россия, Екатеринбург, '+addr, {results: 1, boundedBy: map.getBounds()});

	        YMaps.Events.observe(geocoder, geocoder.Events.Load, function () {
	        	if (this.length()) {
	            	geoResult = this.get(0);
	                map.setBounds(geoResult.getBounds());
	                
	                mapContainer.dialog({title: addr});
	                mapContainer.dialog('open');

	                var geoPoint = this.get(0).getGeoPoint();	                
	                map.setCenter(geoPoint, 15);
	                map.setZoom(15);
	                map.addControl(new YMaps.TypeControl());
	                map.addControl(new YMaps.Zoom());
	                map.enableScrollZoom({smooth: true});

					if (geoPoint == '60.657769,56.83908')
					{
		                if (!placeMark_e)
		                	placeMark_e = new YMaps.Placemark(geoPoint, { draggable: false, hideIcon: true, style: "example#customPoint" });
		                else
		                	placeMark_e.setCoordPoint(geoPoint);

		                placeMark_e.name = 'Адрес не найден';
		                placeMark_e.update();
		                
		                map.addOverlay(placeMark_e);

		                if (placeMark)
		                	map.removeOverlay(placeMark);
					}
					else
					{
		                if (!placeMark)
		                	placeMark = new YMaps.Placemark(geoPoint, { draggable: false, hideIcon: true });
		                else
		                	placeMark.setCoordPoint(geoPoint);
	                	
		                placeMark.name = addr;
		                placeMark.update();
		                
		                map.addOverlay(placeMark);

		                if (placeMark_e)
		                	map.removeOverlay(placeMark_e);
					}
	                
	                map.redraw(true);
				}
	        	else 
	        	{
	            	alert('Адрес не найден на карте');
	        	}
	        });
		};

	};

})(jQuery);
