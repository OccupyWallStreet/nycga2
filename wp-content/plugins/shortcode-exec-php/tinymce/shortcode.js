(function() {
	tinymce.create('tinymce.plugins.ShortcodeExecPHP', {
		init: function(ed, url) {
			// Register command
			ed.addCommand('mceShortcodeExecPHP', function() {
				ed.windowManager.open({
					file: ajaxurl + '?action=scep_ajax&scep_action=tinymce',
					width: 320 + ed.getLang('ShortcodeExecPHP.delta_width', 0),
					height: 240 + ed.getLang('ShortcodeExecPHP.delta_height', 0),
					inline: 1
				}, {
					plugin_url: url // Plugin absolute URL
				});
			});

			// Register button
			ed.addButton('ShortcodeExecPHP', {
				title: 'Shortcode',
				cmd: 'mceShortcodeExecPHP',
				image: url + '/shortcode.gif'
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('ShortcodeExecPHP', n.nodeName == 'IMG');
			});
		},

		createControl: function(n, cm) {
			return null;
		},

		getInfo: function() {
			return {
				longname : 'Shortcode Exec PHP plugin',
				author : 'Marcel Bokhorst',
				authorurl : 'http://blog.bokhorst.biz/about/',
				infourl : 'http://blog.bokhorst.biz/3626/computers-en-internet/wordpress-plugin-shortcode-exec-php/',
				version : '1.0'
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('ShortcodeExecPHP', tinymce.plugins.ShortcodeExecPHP);
})();
