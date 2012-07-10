(function() {
	tinymce.create( 'tinymce.plugins.WPUIMCE', {
		
		createControl : function( n , cm ) {
			
			switch( n ) {
				
			case 'wpuimce' :
			var csm = cm.createSplitButton( 'wpuimce', {
				title	: "WP UI widgets",
				image	: pluginVars.pluginUrl + "/images/icons/wpui_editor.png",
				 onclick: function() {
					// tinyMCE.activeEditor.windowManager.alert( 'Button was clicked!' );					
				}				
			});
			
			csm.onRenderMenu.add( function(c, m) {
				m.add({
					title	: 	'WP-UI',
					'class'	: 	'mceMenuItemTitle'
				}).setDisabled(1);
				
				m.add({
					title : 'Add Tab set',
					onclick : function() {
						// jQuery( '#wpui-dialog-mode' ).val( 'addtab' );
						
						jQuery('#wpui-editor-dialog').wpuiEditor({
							mode : 'addtab'
						});
						
						
						// jQuery( '#wpui-editor-dialog' )
						// .html( jQuery( '#_wpui-editor-dialog #wpui-new-tabset' ).clone() )
						// .attr( 'title', 'Add a tab set')
						// .dialog( 'open' );
					}
				});
				
			
				m.add({
					title : 'Wrap tab set',
					onclick : function() {
						// jQuery( '#wpui-dialog-mode' ).val( 'wrapset' );
						// 
						// jQuery( '#wpui-editor-dialog' )
						// .attr( 'title', 'Wrap the tabs' )
						// .html( jQuery( '#_wpui-editor-dialog #wpui-wrap-tabs' ).clone() )
						// .dialog( 'open' );
						jQuery('#wpui-editor-dialog').wpuiEditor({
							mode : 'wraptab',
							selection : 'multiple'
						});
				
				
					}
				});
				
				
				m.add({
					title : 'Spoiler',
					onclick : function() {
						jQuery('#wpui-editor-dialog').wpuiEditor({
							mode : 'spoiler'				
						});					
					}
				});
				
				
				m.add({
					title : 'Dialog',
					onclick : function() {
						jQuery('#wpui-editor-dialog').wpuiEditor({
							mode : 'dialog'				
						});
					}
				});					
							
				
				m.add({
					title : 'Feeds',
					onclick : function() {
						jQuery('#wpui-editor-dialog').wpuiEditor({
							mode : 'addfeed'				
						});
					}
				});					
			
							
				
				// 
				// m.add({
				// 	title	: 	'Tab Name',
				// 	onclick	: 	function() {
				// 		
				// 		ti = tinyMCE.activeEditor.selection.getContent();
				// 		
				// 		if ( typeof ti == "undefined" || ti == '') { 
				// 			tinyMCE.activeEditor.windowManager.open({
				// 				url: pluginVars.pluginUrl + "js/wpuimce/wptabtitle.htm",
				// 				width: 500,
				// 				height: 280,
				// 				inline: 1,
				// 				popup_css: false
				// 			}); // END open window manager.
				// 		
				// 		}
				// 		else {
				// 			tinyMCE.activeEditor.selection.setContent(' [wptabtitle]' + ti + '[/wptabtitle] ');
				// 
				// 		}
				// 	} // END function onclick.
				// });
				// 
				// m.add({
				// 	title	: 	'Tab contents',
				// 	onclick	: 	function() {
				// 	var uj = tinyMCE.activeEditor.selection.getContent();
				// 	
				// 	tinyMCE.activeEditor.selection.setContent(' [wptabcontent]' + uj + '[/wptabcontent] ');
				// 
				// 	} // end onclick tabscontents.
				// 	
				// });
				// 
				// m.add({
				// 	title	: "Wrap Tabs",
				// 	onclick	: function() {
				// 
				// 		tinyMCE.activeEditor.windowManager.open({
				// 			url: pluginVars.pluginUrl + "js/wpuimce/wptabs_options.htm",
				// 			width: 500,
				// 			height: 400,
				// 			inline: 1,
				// 			popup_css: false
				// 		}); // END open window manager.
				// 		
				// 		// Process the inserted content.
				// 
				// 	} 
				// });
				
				
				m.add({
					title	: 	'Documentation',
					onclick	: 	function() {
						editorHelp = pluginVars.wpUrl + '/wp-admin/admin-ajax.php?action=editorButtonsHelp&TB_iframe=true';
					
						tb_show('WP UI documentation', editorHelp);				
					}
					
				});
				
				
			}); // END c.onRenderMenu.
			return csm;	
		}
		
		return null;
			
		}, // END create control.
		
		getInfo : function() {
			return {
				longname : 'WP-UI Menu',
				author : 'Kavin',
				authorurl : 'http://kav.in',
				infourl : 'http://kav.in',
				version : '0.1'
			}
		}
		
		
	}); // END tinymce.create  tinymce.....

	tinymce.PluginManager.add( 'wpuimce', tinymce.plugins.WPUIMCE);

})(); // END auto closure.

