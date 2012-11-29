vGrid = function(opts){

    var obj = false;
    var filters = false;
    var pager = false;
    var summary = false;
    var filtersForm = false;
    var filtersSubmit = false;

    var o = $.extend({
    summarySelector : '',
    filtersSelector : '',
    filtersSubmitSelector : 'btn-primary',
    pagerSelector : '',
    activePageClass : '',
    tableSelector : '',
    actionItemsSelectors : [],
    pageVar : '',
    activePageClass : '',
    actionTypeAttr : ''
    }, opts);

    obj = $(o.tableSelector);
    var filters = $(o.filtersSelector);
    if (!filters.length)
        filters = false;
    else {
        filtersForm = filters.find("form");
    }

    summary = $(o.summarySelector);
    if (!summary.length) {
        summary = false;
    }

    pager = $(o.pagerSelector);
    if (!pager.length) {
        pager = false;
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

    var getFormParams = function () {
        if (filtersForm) {
            var res = '';
            var res = filtersForm.serialize();
            res += '&'+filtersForm.find(o.filtersSubmitSelector).attr("name")+"=1";
        }
        return res;
    }

    var getPagerParams = function () {
        if (pager) {
            var res = '';
            var currentPage = pager.find("."+o.activePageClass+" a").html();
            res = o.pageVar + "=" + currentPage;
        }
        return res;
    }

    var makeButtonAction = function(el)
    {
        var act = el.attr(o.actionTypeAttr);
        if (!act)
            return true;

        if (act == 'ajaxPage') {
            var url = el.attr("href");
            if (filtersForm) {
                url = url + (url.match(/\?/) ? '&' : '?' ) + getFormParams();
            }
            if (pager) {
                url = url + (url.match(/\?/) ? '&' : '?' ) + getPagerParams();
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
        if (contextData['items']) {
            var html = $(contextData['items']).html();
            obj.html(html);
        }

        if (contextData['filters'] && filtersForm) {
            var html = $(contextData['filters']).find("form").html();
            filtersForm.html(html);
        }

        if (contextData['summary'] && summary) {
            var html = $(contextData['summary']).html();
            summary.html(html);
        }

        if (contextData['pager'] && pager) {
            var html = $(contextData['pager']).html();
            pager.html(html);
        }

    };

    if (pager) {
        pager.delegate("a", "click", function(){
            var url = $(this).attr("href");
            app.ajax.ajaxPage(url, false);
            return false;
        });
    }

    if (filtersForm) {
        filtersForm.submit(function(){
            var url = filtersForm.attr("action");

            url = url + (url.match(/\?/) ? '&' : '?' ) + getFormParams();
            // todo: add page num
            app.ajax.ajaxPage(url, false);
            return false;
        })
    }

    app.addListener ('pageReloaded', obtainAjaxData);

}
