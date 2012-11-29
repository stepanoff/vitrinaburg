CKEDITOR.plugins.add('linkpopup', {

  requires : ['dialog'], lang : ['en'],
  init : function(editor) {

    editor.addCommand('linkpopup', new CKEDITOR.dialogCommand('linkpopup'));


	editor.ui.addButton('Linkpopup', {
		label: 'Ссылка на попап',
		command: 'linkpopup',
		icon: this.path + 'images/anchor.gif'
	});

    CKEDITOR.dialog.add('linkpopup', this.path + 'dialogs/link.js');
  }
});