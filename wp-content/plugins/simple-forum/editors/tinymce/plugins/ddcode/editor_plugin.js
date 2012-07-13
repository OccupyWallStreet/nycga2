(function() {
	var ddUrl;
	tinymce.PluginManager.requireLangPack('ddcode');
	tinymce.create('tinymce.plugins.ddcodePlugin', {
		init : function(ed, url) {
			ed.onInit.add(function() {
				ed.dom.loadCSS(url + "/css/content.css");
			});
			ddUrl = url;

			ed.onNodeChange.add(function(ed, cm, n, co) {
				if(co) {
					selText = '';
				} else {
					selText = ed.selection.getContent();
				}
				cm.setDisabled('ddcode', (selText==''));
			});
		},

		createControl : function(n, cm) {
			if(n == 'ddcode') {
				var c = cm.createSplitButton('ddcode', {
					title : 'ddcode.desc',
					image : ddUrl + '/img/ddcode.gif',
					onclick : function() {
						c.showMenu();
					}
				});

				c.onRenderMenu.add(function(c, m) {
					m.add({title : 'ddcode.select', 'class' : 'mceMenuItemTitle'}).setDisabled(1);

					var list = tinyMCE.activeEditor.getParam("brushes");
					var langs = list.split(',');

					var x = langs.length;
					for (i=0; i<x; i++) {
						switch(langs[i]) {
							case 'apache':
								m.add({title : 'apache', onclick : function() { processCodeLang('apache'); }}); break;
							case 'applescript':
								m.add({title : 'applescript', onclick : function() { processCodeLang('applescript'); }}); break;
							case 'asm':
								m.add({title : 'asm', onclick : function() { processCodeLang('asm'); }}); break;
							case 'bash-script':
								m.add({title : 'bash-script', onclick : function() { processCodeLang('bash-script'); }}); break;
							case 'bash':
								m.add({title : 'bash', onclick : function() { processCodeLang('bash'); }}); break;
							case 'basic':
								m.add({title : 'basic', onclick : function() { processCodeLang('basic'); }}); break;
							case 'clang':
								m.add({title : 'clang', onclick : function() { processCodeLang('clang'); }}); break;
							case 'css':
								m.add({title : 'css', onclick : function() { processCodeLang('css'); }}); break;
							case 'diff':
								m.add({title : 'diff', onclick : function() { processCodeLang('diff'); }}); break;
							case 'html':
								m.add({title : 'html', onclick : function() { processCodeLang('html'); }}); break;
							case 'java':
								m.add({title : 'java', onclick : function() { processCodeLang('java'); }}); break;
							case 'javascript':
								m.add({title : 'javascript', onclick : function() { processCodeLang('javascript'); }}); break;
							case 'lisp':
								m.add({title : 'lisp', onclick : function() { processCodeLang('lisp'); }}); break;
							case 'ooc':
								m.add({title : 'ooc', onclick : function() { processCodeLang('ooc'); }}); break;
							case 'php':
								m.add({title : 'php', onclick : function() { processCodeLang('php'); }}); break;
							case 'python':
								m.add({title : 'python', onclick : function() { processCodeLang('python'); }}); break;
							case 'ruby':
								m.add({title : 'ruby', onclick : function() { processCodeLang('ruby'); }}); break;
							case 'sql':
								m.add({title : 'sql', onclick : function() { processCodeLang('sql'); }}); break;
							case 'sql':
								m.add({title : 'yaml', onclick : function() { processCodeLang('yaml'); }}); break;
						}
					}
				});
				return c;
			}

			function processCodeLang(codeLang) {
				selText = tinyMCE.activeEditor.selection.getContent();
				html = '<div class="sfcode"><pre class="brush-'+codeLang+' syntax">'+selText+'</pre></div>';
				tinyMCE.activeEditor.execCommand("mceInsertContent", false, html);
				tinyMCE.activeEditor.execCommand('mceRepaint');
				return;
			}
		},

		getInfo : function() {
			return {
				longname : 'ddcode plugin - Re-Write',
				author : 'Andy Staines',
				authorurl : 'http://simple-press.com',
				version : "1.0"
			};
		}
	});

	tinymce.PluginManager.add('ddcode', tinymce.plugins.ddcodePlugin);
})();
