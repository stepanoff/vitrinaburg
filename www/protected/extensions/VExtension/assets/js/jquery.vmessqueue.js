/*
Очередь всплывающих окон
для нормального ф-рования нужны jQuery
*/
VMessQueue = function(opts){

    var o = $.extend({
    messDiv : 'informerMessages',
    messContainer : 'messageContainer',
    messContainerClass : 'messageContainer',
    listContainer : 'listContainer',
    messClass : 'informerMessage',
    controlsClass : 'controls',
    contentClass : 'messageContent',
    titleClass : 'title',
    actionBtnClass : 'action',
    closeActionClass : 'close',
    updateActionClass : 'update',
    nextActionClass : 'next',
    readActionClass : 'read',
    btnClass : 'button',
    skipWord : 'след.',
    prevWord : 'пред.',
    showOnStart : true,
    sendFunction : false,
    btnActionAttr : 'action',
    readUrl : '/informer/read/'
    }, opts);

    var queueObj = $("#"+o.messDiv);
    var messObj = $('<div>').attr('id', o.messContainer).addClass(o.messContainerClass).insertAfter("#"+o.messDiv);
    var listObj = new Object();

    var indexSpan = new Object();
    var totalSpan = new Object();
    var skipLink = new Object();
    var prevLink = new Object();
    var index = 0;
    var messages = new Array ();

    var init = function()
    {
        $(queueObj).hide();
        //$(messObj).dialog({ autoOpen: false, modal: true, resizable: false, draggable: false });
        //$(messObj).modal({});

        listObj = $('<div>');
        $(listObj).addClass(o.controlsClass);
        $(listObj).append('<span class="index"></span> из <span class="total"></span>');
        $(listObj).append('<a class="prev" href="#" id="'+o.messContainer+'Prev">'+o.prevWord+'</a>');
        $(listObj).append('<a class="skip" href="#" id="'+o.messContainer+'Skip">'+o.skipWord+'</a>');
        totalSpan = $(listObj).find(".total");
        indexSpan = $(listObj).find(".index");
        skipLink = $(listObj).find(".skip");
        prevLink = $(listObj).find(".prev");

        refresh();
    }

    var refresh=function()
    {
        var messes = $(queueObj).children("."+o.messClass);
        if(messes.length)
        {
            var i = 1;
            $(messes).each(function(){
                var id = o.messClass+i;
                $(this).attr('id', id);
                i++;
                addMessage(id);
            });
            if (o.showOnStart)
            {
                show();
            }
        }
    };

    var addMessage = function(id)
    {
        messages[(messages.length)] = id;
    };

    var addMessages = function(data)
    {
        if (data=="")
            return;
        $(queueObj).prepend(data);
        refresh();
    };

    var show=function()
    {
        var html = $("#"+messages[index]).html();
        $(messObj).html(html).show();
        //$(messObj).dialog( "option", "buttons", { "Ok": function() { $(this).dialog("close"); } } );
        refresh_content();
        $(messObj).children().filter(":first").modal('show');
        //$(messObj).dialog('open');
    };

    var refresh_content=function()
    {
        $(messObj).find("."+o.closeActionClass).click(function(){close();return false;});
        $(messObj).find("."+o.nextActionClass).click(function(){next();return false;});
        $(messObj).find("."+o.updateActionClass).click(function(){update();return false;});
        $(messObj).find("."+o.readActionClass).click(function(){read($(this));return false;});
        $(messObj).find("."+o.actionBtnClass).click(function(){send($(this));return false;});

        if ($(messObj).find("."+o.listContainer).get()!=0)
        {
            $(indexSpan).html((index+1));
            $(totalSpan).html((messages.length));
            if (index == 0)
                $(prevLink).hide();
            else
            {
                $(prevLink).click(function(){prev();return false;});
                $(prevLink).show();
            }
            if (index >= messages.length-1)
                $(skipLink).hide();
            else
            {
                $(skipLink).click(function(){skip();return false;});
                $(skipLink).show();
            }
            $(listObj).appendTo($(messObj).find("."+o.listContainer));
        }
    };

    var next=function()
    {
        id = messages.splice(index,1);
        if (messages.length)
        {
            if (messages[index])
            {}
            else
                index = 0;
            show();
        }
        else
            close();
    };

    var close=function()
    {
        $(messObj).children().filter(":first").modal('hide');
        //$(messObj).dialog('close');
        $(messObj).hide();
        $(queueObj).html('');
        messages = new Array();
    };

    var skip=function()
    {
        index++;
        if (messages[index])
        {
            show();
        }
        else
            index--;
    };

    var prev=function()
    {
        index--;
        if (index>=0 && messages[index])
        {
            show();
        }
        else
            index++;
    };

    var send=function(el)
    {
        var act = el.attr(o.btnActionAttr);
        var url = false;
        if (act) {
            if (act == 'ajaxPage') {
                url = el.attr("href");
                app.ajax.ajaxPage(url, false);
            }
        }
        read (el, url, {});
    };

    var read=function(el, url, params)
    {
        if (o.readUrl) {
            var id = $(el).attr("name");
            url = url?url:false;
            params = params?params:{};
            jQuery.ajax({
                type: "POST",
                url: o.readUrl+"?Id="+id,
                success: function(data){
                    next();
                    if (url && o.sendFunction)
                        callback(o.sendFunction, {}, [url, params]);
                }
            });
        }
        else
            next();
    };

    var destructor=function()
    {
        delete o;
        delete messages;
    };

    var obtainAjaxData = function (evt) {

        var result = evt['result'] ? evt['result'] : false;
        var contextData = result;
        if (contextData['messages']) {
            addMessages(contextData['messages']);
        }
    };

    app.addListener ('pageReloaded', obtainAjaxData);


	this.init = function (opts) {
		init(opts);
	};

    this.initUi = function () {

    }

}
