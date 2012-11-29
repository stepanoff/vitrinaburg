(function()
{
	CKEDITOR.plugins.add('webkitfix', {
		init : function(editor) {
			editor.addCommand('webkitfix',
			{
				exec: function(editor) { },
				canUndo: false
			});

			if ($.browser.webkit) {
				if (!Array.prototype.filter) {
					Array.prototype.filter = function(fun /*, thisp*/) {
						var len = this.length;
						if (typeof fun != "function") throw new TypeError();
						var res = new Array();
						var thisp = arguments[1];
						for (var i = 0; i < len; i++) {
							if (i in this) {
								var val = this[i]; if (fun.call(thisp, val, i, this))res.push(val);
							}
						}
						return res;
					};
				}
				editor.on('insertHtml', function(e){
					window.setTimeout(function(){
						var myf=document.getElementsByTagName("iframe");
						var mysp=myf[0].contentWindow.document.getElementsByTagName('span');
						var spans = Array.prototype.filter.call(
							mysp,
							function(item){
								return (item.className == 'Apple-style-span');
							}
						);
						for ( var i = spans.length - 1 ; i >= 0 ; i-- ) {
							spans[i].parentNode.replaceChild(spans[i].firstChild,spans[i]);
						}
					}, 100);
				});
			}
		}
/*
		afterInit : function( editor )
		{
			var dataProcessor = editor.dataProcessor;
			console.log(dataProcessor);
			var dataFilter = dataProcessor && dataProcessor.dataFilter;
			var htmlFilter = dataProcessor && dataProcessor.htmlFilter;

			dataFilter.addRules({
				elements: {
					'span': function(element) {
						console.log(element);
						if (element.attributes.class == 'Apple-style-span') {
							element.name = '';
							return element;
						}
					}
				}
			});

			htmlFilter.addRules(
			{
				elements :
				{
					'span': function( element )
					{
						console.log(element);
						if (element.attributes.class == 'Apple-style-span') {
							element.name = '';
							return element;
						}
					}
				}
			}, 100);
		}
*/
	});
})();