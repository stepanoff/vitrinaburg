CKEDITOR.addTemplates('default',
{
	imagesPath : CKEDITOR.getUrl( CKEDITOR.plugins.getPath( 'templates' ) + 'templates/images/' ),

	templates :
		[
			{
				title: 'Цитата с картинкой',
				image: '1.png',
				description: '',
				html:
					'<div class="pic-with-cite"><h3 class="pic-with-cite-header">Заголовок цитаты:</h3><p class="fo-width-120"><img src="' + $(document).data("portal.resources") + '/img/ckeditor/pic_comments.jpg" alt="" /></p><blockquote><p>Текст цитаты</p></blockquote></div><p></p>'
			},
			{
				title: 'Цитата без картинки',
				image: '2.png',
				description: '',
				html:
					'<h3 class="pic-without-cite-header">Заголовок цитаты:</h3><ins>Текст цитаты</ins><p></p>'
			},
			{
				title: 'Картинка с нейтральной подписью',
				image: '3.png',
				description: '',
				html:
					'<table border="0" cellpadding="0" cellspacing="0" class="simple-picture"><thead><tr><th><img alt="" src="' + $(document).data("portal.resources") + '/img/ckeditor/pic_img.jpg" title=""></th></tr></thead><tbody><tr><td><p><em>Текст</em></p></td></tr></tbody></table><p></p>'
			},
			{
				title: 'Картинка с позитивной подписью',
				image: '4.png',
				description: '',
				html: 
					'<table border="0" cellpadding="0" cellspacing="0" class="positive_picture"><thead><tr><th colspan="2"><img alt="" src="' + $(document).data("portal.resources") + '/img/ckeditor/pic_img.jpg" title=""></th></tr></thead><tbody><tr><td class="table-picture-ico"><fake_positiveicon></fake_positiveicon></td><td>Текст</td></tr></tbody></table><p></p>'
				},
			{
				title: 'Картинка с негативной подписью',
				image: '5.png',
				description: '',
				html:
					'<table border="0" cellpadding="0" cellspacing="0" class="negative_picture"><thead><tr><th colspan="2"><img alt="" src="' + $(document).data("portal.resources") + '/img/ckeditor/pic_img.jpg" title=""></th></tr></thead><tbody><tr><td class="table-picture-ico"><fake_negativeicon></fake_negativeicon></td><td>Текст</td></tr></tbody></table><p></p>'
			},
			{
				title: 'Таблица 3x3',
				image: '6.png',
				description: '',
				html:
					'<p><em>Заголовок таблицы</em></p><table width="100%"><tbody><tr><th width="33%">Заголовок</th><th width="33%">Заголовок</th><th width="33%">Заголовок</th></tr><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr></tbody></table>'
			}
		]
});
