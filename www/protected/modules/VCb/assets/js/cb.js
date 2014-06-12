/*
Контент-блоки
*/
VCb = function(opts){

    var o = $.extend({
    objSelector : '',
    editLinkClass : ''
    }, opts);

    var obj = $(o.objSelector);

    var init = function()
    {
    }

    obj.delegate('.'+o.editLinkClass, 'click', function () {
        window.open(this.href, 'cb_window', 'width=1000,height=768');
        return false;
    });
   //app.addListener ('pageReloaded', obtainAjaxData);


	this.init = function (opts) {
		init(opts);
	};

    this.initUi = function () {

    }

}
