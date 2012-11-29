CKEDITOR.dialog.add('linkpopup', function (a)
{

    var b = CKEDITOR.plugins.link,
    c = function ()
    {
        var C = this.getDialog(),
            D = C.getContentElement('target', 'textLink'),
            E = C.getContentElement('target', 'urlLink'),
            F = this.getValue();

    }


    return {

        title : "ссылка на попап",
        minWidth : 300,
        minHeight : 120,
        contents : [
        {
            id : 'info',
            label : "label",
            title : "title",
            elements : [

            {
                type : 'vbox',
                id : 'textLink',
                children : [
                {
                    type : 'hbox',
                    widths : ['25%', '75%'],
                    children : [
                        {
                            type : 'text',
                            id : 'textLink',
                            label : "текст ссылки",
                            required : true,
                                setup : function (C)
                                {

                                },
                                commit : function (C)
                                {

                                }
                        }
                    ],
                    setup : function (C)
                    {

                    }
                }]
            },

            {
                type : 'vbox',
                id : 'textField',
                children : [

                    {
                        type : 'text',
                        id : 'urlLink',
                        label : "url ссылки",
                        required : true,
                        setup : function (C)
                        {

                        },
                        commit : function (C)
                        {

                        }
                    }],
                    setup : function (C)
                    {

                    }
            }]
        }],
        onShow : function ()
        {

        },
        onOk : function ()
        {
            var f = this;
            var c = f.getValueOf('info', 'textLink');
            var d = f.getValueOf('info', 'urlLink');
            this._.editor.insertHtml('<a class="js-wysiwyg-link-container inline-block" href="'+ d +'"><ins> </ins>' + c + '</a> ');
        },
        onLoad : function ()
        {

        },
        onFocus : function ()
        {

        }
    };
});