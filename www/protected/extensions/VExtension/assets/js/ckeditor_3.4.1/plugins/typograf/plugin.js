(function()
{
	CKEDITOR.plugins.add('typograf', {
		init: function(editor) {
			var command = editor.addCommand(
				'typograf',
				{
					exec: function()
					{
						var ckId = 'cke_' + editor.name;
						var ckEl = $('#' + ckId);
						ckEl.block({
							message: 'Загрузка...',
							css: {
								border: 'none',
								padding: '15px',
								backgroundColor: '#000',
								'border-radius': '10px',
								'-webkit-border-radius': '10px',
								'-moz-border-radius': '10px',
								opacity: .5,
								color: '#fff'
							}
						});
						$.ajax({
							type: 'POST',
							url: '/news/typograf/',
							data: {
								text: editor.getData()
							},
							success: function(data){
								if (data && data['text']) {
									editor.setData(data['text']);
								}
								else {
									alert('Ошибка во время работы типографа');
								}
								ckEl.unblock();
							},
							error: function() {
								alert('Ошибка во время работы типографа');
								ckEl.unblock();
							},
							dataType: 'json'
						});
					},
					canUndo: true
				}
			);

			editor.ui.addButton('Typograf', {
				label: 'Типографировать текст',
				command: 'typograf',
				icon: this.path + 'icon.png'
			});
		}
	});
})();