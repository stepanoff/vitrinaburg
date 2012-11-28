/*
Очередь всплывающих окон
для нормального ф-рования нужны jQuery
*/
messQueue = function(opts){

    var queueObj = new Object();
    var messageObj = new Object();
    var listObj = new Object();
    var indexSpan = new Object();
    var totalSpan = new Object();
    var skipLink = new Object();
    var prevLink = new Object();
    var index = 0;
    var messages = new Array ();

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
    readUrl : '/informer/read/'
    }, opts);

    var init = function()
    {
        queueObj = $("#"+o.messDiv);
        $(queueObj).hide();
        messObj = $('<div>').attr('id', o.messContainer).addClass(o.messContainerClass).insertAfter("#"+o.messDiv);
        $(messObj).dialog({ autoOpen: false, modal: true, resizable: false, draggable: false });

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
        messages = $(queueObj).children("."+o.messClass);
        if(messages.length)
        {
            $(messages).each(function(index){
                id = o.messClass+index;
                $(this).attr('id', id);
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
        id = messages[index];
        html = $("#"+id).html();
        $(messObj).html(html).show();
        //$(messObj).dialog( "option", "buttons", { "Ok": function() { $(this).dialog("close"); } } );
        refresh_content();
        $(messObj).dialog('open');
    };

    var refresh_content=function()
    {
        $(messObj).find("."+o.closeActionClass).click(function(){close();return false;});
        $(messObj).find("."+o.nextActionClass).click(function(){next();return false;});
        $(messObj).find("."+o.updateActionClass).click(function(){update();return false;});
        $(messObj).find("."+o.readActionClass).click(function(){read($(this));return false;});
        $(messObj).find("."+o.actionBtnClass).click(function(){send($(this));return false;});

        messTitle = '';
        if ($(messObj).find("."+_o.titleClass).get()!=0)
        {
            messTitleObj = $(messObj).find("."+_o.titleClass);
            messTitle = $(messTitleObj).html();
            $(messTitleObj).remove();
            $(messObj).dialog('option', {"title": messTitle});
        }

        if ($(messObj).find("."+_o.listContainer).get()!=0)
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
            $(listObj).appendTo($(messObj).find("."+_o.listContainer));
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
        $(messObj).dialog('close');
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

    var send=function(obj)
    {
        send_url = $(obj).parent("form").attr("action");
        params = new Object();
        $(obj).parent("form").children("input").each(function(){
            params[$(this).attr("name")] = $(this).attr("value");
        });
        read (obj, send_url, params);
    };

    var read=function(obj, url, params)
    {
        id = $(obj).attr("name");
        url = url?url:false;
        params = params?params:{};
        jQuery.ajax({
            type: "POST",
            url: readUrl+"?Id="+id,
            success: function(data){
                next();
                if (url && o.sendFunction)
                    callback(o.sendFunction, {}, [url, params]);
            }
        });
    };

    var destructor=function()
    {
        delete o;
        delete messages;
    };

	this.init = function (opts) {
		init(opts);
	};

    this.initUi = function () {

    }

}
