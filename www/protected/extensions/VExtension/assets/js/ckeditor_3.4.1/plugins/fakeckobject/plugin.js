(function()
{
	CKEDITOR.plugins.add('fakeckobject', {
		init : function(editor) {
			editor.addCommand( 'fakeckobject',
			{
				exec: function( editor )
				{

				},
				canUndo: false
			});
		},

		afterInit : function( editor )
		{
			var dataProcessor = editor.dataProcessor;
			var dataFilter = dataProcessor && dataProcessor.dataFilter;

			dataFilter.addRules({
				elements: {
					'fake_object': function(element) {
						var fakeElement = editor.createFakeParserElement(element, 'fake_object', 'img', true);
						fakeElement.attributes.src = fakeCkObject.getImageById(element.attributes.object_id);
						if (element.attributes.width)
							fakeElement.attributes.width = element.attributes.width;
						if (element.attributes.height)
							fakeElement.attributes.height = element.attributes.height;
						return fakeElement;
					},
					'fake_photoreportage': function(element) {
						var fakeElement = editor.createFakeParserElement(element, 'fake_photoreportage', 'img', true);
						fakeElement.attributes.src = fakeCkObject.getPhotoreportageImage();
						return fakeElement;
					},
					'fake_poll': function(element) {
						var fakeElement = editor.createFakeParserElement(element, 'fake_poll', 'img', true);
						fakeElement.attributes.src = fakeCkObject.getPollImage();
						return fakeElement;
					},
					'fake_positiveicon': function(element) {
						var fakeElement = editor.createFakeParserElement(element, 'fake_positiveicon', 'img', true);
						fakeElement.attributes.src = fakeCkObject.getPositiveImage();
						return fakeElement;
					},
					'fake_negativeicon': function(element) {
						var fakeElement = editor.createFakeParserElement(element, 'fake_negativeicon', 'img', true);
						fakeElement.attributes.src = fakeCkObject.getNegativeImage();
						return fakeElement;
					}
				}
			});
		}
	});
})();