
(function() {
	tinymce.create('tinymce.plugins.SimpleImagePlugin', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('mceSimpleImage', function() {
				// Internal image object like a flash placeholder
				if (ed.dom.getAttrib(ed.selection.getNode(), 'class', '').indexOf('mceItem') != -1)
					return;

				ed.windowManager.open({
					file : url + '/image.htm',
					width : 460,
					height : 140,
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('image', {
				title : 'simpleimage.image_desc',
				cmd : 'mceSimpleImage'
			});
		}
	});

	// Register plugin
	tinymce.PluginManager.add('simpleimage', tinymce.plugins.SimpleImagePlugin);
})();