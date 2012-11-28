vGrid = function(opts){

    var obj = false;
    var filters = false;
    var pager = false;
    var filtersForm = false;
    var filtersSubmit = false;

    var o = $.extend({
    filtersSelector : '',
    filtersSubmitSelector : 'btn-primary',
    pagerSelector : '',
    activePageClass : '',
    tableSelector : '',
    actionItemsSelectors : [],
    actionTypeAttr : ''
    }, opts);

    obj = $(o.tableSelector);
    var filters = $(o.filtersSelector);
    if (!filters.length)
        filters = false;
    else {
        filtersForm = filters.find("form");
        filtersSubmit = filters.find(o.filtersSubmitSelector);
    }

    var init = function()
    {
        var i = false;
        for (i in o.actionItemsSelectors) {
            obj.delegate(o.actionItemsSelectors[i], 'click', function(){
                return makeButtonAction($(this));
            });
        }
    }

    var refresh=function()
    {

    };

    var makeButtonAction = function(el)
    {
        var act = el.attr(o.actionTypeAttr);
        if (!act)
            return true;

        if (act == 'ajaxPage') {
            var url = el.attr("href");
            if (filtersForm) {
                var params = filtersForm.serialize();
                url = url + (url.match(/\?/) ? '&' : '?' ) + params;
                url += '&'+filtersSubmit.attr("name")+"=1";
                // todo: add page num
            }
            app.ajax.ajaxPage(url, false);
        }
        return false;
    };

	this.init = function (opts) {
		init(opts);
	};

    this.initUi = function () {

    }

    var obtainAjaxData = function (evt) {

        var result = evt['result'] ? evt['result'] : false;
        var contextData = result;
        if (contextData['list']) {
            var html = $(contextData['list']).html();
            obj.html(html);
        }
    };

    if (filtersForm) {
        filtersForm.submit(function(){
            var url = filtersForm.attr("action");
            var params = filtersForm.serialize();
            url = url + (url.match(/\?/) ? '&' : '?' ) + params;
            url += '&'+filtersSubmit.attr("name")+"=1";
            // todo: add page num
            app.ajax.ajaxPage(url, false);
            return false;
        })
    }

    app.addListener ('pageReloaded', obtainAjaxData);

}
