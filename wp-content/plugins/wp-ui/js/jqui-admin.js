if (typeof console == "undefined" || typeof console.log == "undefined") var console = { log: function() {} };

(function($){
    if(!$.wpui){
        $.wpui = new Object();
    };
    
    $.wpui.jquiThemeManage = function(el, options){
        var base = this;
        base.$el = $(el);
        base.el = el;
		// base.fileListBak = JSON.parse( base.$el.val());
		base.fileList = {};
		base.valid = true;
		base.validMsg = {};
		base.ajaxError = false;
		
		base.fileList = ( base.$el.val() != '' ) ? JSON.parse( base.$el.val()) : {};

		base.ble = false;
		if ( typeof( wpUIOpts ) == 'object' && wpUIOpts.bleeding == 'on' ) base.ble = true;

        base.$el.data("wpui.jquiThemeManage", base);

        base.init = function(){
            base.options = $.extend({},$.wpui.jquiThemeManage.defaultOptions, options);
			base.$el.hide();
            
			if( jQuery( '#jqui_theme_list' ).length != 1 ) {
				base.$el.after('<div id="jqui_theme_list" />');
			}
			base.tableC = jQuery( '#jqui_theme_list' );
		
			base.createStructures();
			base.formBinders();
			
	
			jQuery( base.fileList ).bind('change', function() {
				base.$el.val( JSON.stringify( base.fileList ) );
			});

        };


		/*
		 *	Create the required structures.
		 */
		base.createStructures = function() {
			
			
			// Add the table, and its header.
			base.tableC
				.append( '<table class="widefat"><thead /><tbody /></table>' )
				.find( 'thead' )
				.append( '<tr><th class="column-name">Name</th><th class="column-link">Stylesheet URL</th><th class="column-manage">Manage</th></tr>');
		base.table = base.tableC.find( 'table.widefat' );
		
		
			// if the fileList is empty,insert the placeholder.
			if ( jQuery.isEmptyObject( base.fileList ) ) {
			
				base.table
					.find( 'tbody' )
					.append( '<tr class="placeholder" />')
					.find( 'tr.placeholder' )
					.append( '<td colspan="3">Custom themes list is empty. Click "Add themes" to add one. If you have uploaded the files to wp-content/uploads, click "Scan Uploads" to add them.</td>');
			
			} else {
				// So there are keys in there! Insert em.
					for ( keyz in base.fileList ) {
						value = base.fileList[ keyz ];
						base.table
							.append( '<tr />' )
							.find( '<tr:last' )
							.append( '<td>' + keyz + '</td><td><a target="_blank" href="' + value + '">' + value + '</a></td><td><a class="jqui_theme_delete" title="Remove this theme" href="#">Delete</a></td>');
					}
			
			}

			// Add a hidden dialog form to the body, later used for add or delete.
			base.themeForm = jQuery( '<div style="display : none; " title="" id="jqui_theme_form" />')
				.append( '<form><fieldset /></form>' )
				.find( 'form fieldset' )
				.append( '<div class="theme_add_notes" />')
				.append( '<label for="jqui_theme_name">Name (CSS scope) <span> that will be used with the style argument</span></label>' )
				.append( '<input id="jqui_theme_name" type="text" name="theme_name" class="ui-widget-content ui-corner-all" />' )
				.append( '<label for="jqui_theme_url">Link <span>Absolute URL of the stylesheet</label>' )
				.append( '<input id="jqui_theme_url" type="text" name="theme_url"  class="ui-widget-content ui-corner-all" />' )
				// .append( '<input id="jqui_theme_multiple" type="checkbox" name="theme_multiple"  class="ui-widget-content ui-corner-all" /><label for="jqui_theme_multiple">Keep on adding</label>' )
				.end()
				.appendTo( 'body' );
				
				base.infoD = jQuery( '<div class="wpui_info_box" />' )
								.appendTo( 'body' )
								.dialog({
									autoOpen : false
								});
								
		};


		/**
		 *	Binding functions for - Add/Edit forms, delete link and uploads.
		 */
		base.formBinders = function( ) {
			
			// Adding a theme 
			jQuery( '#jqui_add_theme' ).click(function() {
				
				base.themeForm
					.attr( 'title' , 'Add a jQuery UI custom theme' )
					.dialog({
						width : 400,
						modal : true,
						buttons : {
							"Cancel" : function( ) {
								jQuery( this ).dialog('destroy');
								jQuery( this ).attr( 'title' , '' );
							},
							"Add" : function() {
								base.validate();
								if ( base.valid )
									base.addDetails();
								
								base.table.trigger( 'adjust' );
							}
						},
						open : function() {
							jQuery( 'div.theme_add_notes' ).find('ol').remove();
							jQuery( '.ui-button:first' , '.ui-dialog-buttonset' )
								.addClass('cancel-button');
							jQuery( '.ui-button:last' , '.ui-dialog-buttonset' )
								.addClass('save-button');
							jQuery( '#jqui_theme_form' ).find( 'input' ).val('');							
						}
					
						
					});
				
				return false;
			});
		
			base.table.find('tr:not(.placeholder)').live('dblclick.wpui', function( e ) {
				e.stopPropagation();
				tableTr = jQuery(this);
				editName = jQuery( this ).find('td').eq( 0 ).text();
				editUrl = jQuery( this ).find( 'td' ).eq( 1 ).text();
				
				base.themeForm
					.attr( 'title' , 'Edit the theme - ' + editName )
					.dialog({
						width : 400,
						modal : true,
						buttons : {
							"Cancel" : function() {
								jQuery( this ).dialog('destroy');
							},
							"Save"	: function( ) {
								newName = jQuery( '#jqui_theme_name' ).val();
								newUrl = jQuery( '#jqui_theme_url' ).val();
								base.validate();
								if( base.valid ) {
									base.editDetails(
										tableTr,
										[ newName , newUrl , editName , editUrl ]
									);
								}
								
							}
						},
						open : function() {
							jQuery( 'ol' , 'div.theme_add_notes' ).remove();
							// Fillup the text fields.
							jQuery( 'input#jqui_theme_name' )
								.val( editName );
							jQuery( 'input#jqui_theme_url' )
								.val( editUrl );							
							
							// Add class to the buttons
							jQuery( '.ui-button:first' , '.ui-dialog-buttonset' )
								.addClass('cancel-button');
							jQuery( '.ui-button:last' , '.ui-dialog-buttonset' )
								.addClass('save-button');

						},
						close : function() {
							jQuery( this ).attr( 'title', '' );
						}						
					});
					
				
				return false;
			});
		
			jQuery( 'a.jqui_theme_delete' ).live('click', function() {
				
				// Unclick the counterparts.
				jQuery( this )
					.parent()
					.parent()
					.siblings()
					.find('a.action-delete-cancel')
					.trigger('click');
					
				// Add the class.
				jQuery( this )
					.removeClass( 'jqui_theme_delete' )
					.addClass( 'action-delete-confirm' )
					.after( '<br /><a title="Cancel the action" class="action-delete-cancel" href="#">Cancel</a>');
				
				return false;
				
			});
		
		
			jQuery( '.action-delete-confirm' ).live( "click", function() {
				
				keyName = jQuery( this ).parent().parent().children().eq(0).text();
				
				delete base.fileList[ keyName ];
				
				if ( typeof jQuery.ui !== "undefined" ) {
					jQuery( this )
						.parent()
						.parent()
						.hide('pulsate', {times : '3'} , 'fast', function() {
							jQuery( this ).remove();
						});
				}
				jQuery( base.fileList ).trigger("change");	
				
				base.table.trigger( 'adjust' );	
											
				return false;
			});
			
			jQuery( '.action-delete-cancel' ).live('click', function() {
				jQuery( this )
					.parent()
					.children()
					.eq( 0 )
					.removeClass('action-delete-confirm')
					.addClass( 'jqui_theme_delete' );
					
				jQuery( this ).prev().remove().end().remove();	
				
				base.table.trigger( 'adjust' );	
											
				return false;
			});
		
			
			jQuery( '#jqui_scan_uploads' ).click( function() {
				base.themeUploads( function() {
					base.table.trigger( 'adjust' );
				});
				return false;
			});
			
			base.table.bind( 'adjust', function() {
				if ( ! base.ble ) return false;
				jQuery( '.ktabs' ).ktabs( 'fixheight' );
			});
			
			
			
		};

		
		base.showInfo = function( titl, htm ) {
			base.infoD
					.html( htm )
					.dialog({
						close : function() {
							base.infoD.html( "" );
							base.infoD.removeClass( 'scan-error' );
						},
						open : function() {
						},
						width : ( titl == 'Error' ) ? '500' : '300',
						dialogClass : ( titl == 'Error' ) ? 'scan-error' : '',
						title : titl,
						modal : true,
						buttons : [
						{
							'text' : 'Ok',
							'click' : function() { $( this ).dialog( "close" ); },
							'class' : 'save-button'
						}
						]
					})
					.dialog( 'open' );
		};
		

		/**
		 *	Query the directory wp-uploads/wp-ui for themes.
		 */	
		base.themeUploads = function( callback ) {
			callback = callback || function() {};
			jQuery( 'div.jqui_ajax_info').remove();
			
			jQuery( '#jqui_scan_uploads' )
				.before( '<div class="jqui_ajax_info"></div>');
				
			$msg = jQuery( '.jqui_ajax_info' );
				
			$msg
				.append( '<span class="ajax_success" />' )
				.find( 'span.ajax_success')
				.html( '<img width="30px" height="30px" src="' + initOpts.pluginUrl + 'images/scanning-white.gif" />Scanning the uploads folder for themes..</span>' );
						
			var data = {
				action : 'jqui_css',
				upNonce : jqui_admin.upNonce
			}, response;			
			
				jQuery.post( ajaxurl, data, function( response ) {
					
					var resp = JSON.parse( response );
 					if ( typeof( resp ) != 'object' ) return false;

					$msg.find( 'span' ).remove();
					if ( resp.status == 'error' ) {
						// $msg.hide()
						// 	.append( '<span class="ajax_error" />')
						// 	.find( 'span.ajax_error' )
						// 	.html( resp.description + "<br /><code>" + resp.link + '</code>' )
						// 	.end()
						// 	.slideDown( 500 );
							
						msg = '<span class="ajax_error">';
						msg += resp.description + "<br /><code>" + resp.link + '</code>';
						msg += '</span>';
						
						base.showInfo( "Error", msg );
						
							
					// }
					// 
					// 				
					// // No directory!
					// if ( /NO_DIR.*/i.test(response) ) {
					// 	base.ajaxError = "The directory wp-ui does not exist.";
					// 	base.ajaxPath = response.replace( /NO_DIR\s:::::\s/gm, '')
					// 	
					// 	$msg.hide()
					// 		.append( '<span class="ajax_error" />')
					// 		.find( 'span.ajax_error' )
					// 		.html( base.ajaxError + " Create the following folder through FTP or hosting's file manager.<br /><code>" + base.ajaxPath + '</code>' )
					// 		.end()
					// 		.slideDown( 500 );
					// 		
					// // Directory found, but empty.
					// } else if ( /EMPTY_DIR.*/i.test( response ) ) {
					// 	// console.log( response ); 
					// 	base.ajaxError = "The wp-ui folder is probably empty. Upload the theme folders to this location.";
					// 	base.ajaxPath = response.replace( /EMPTY_DIR\s:::::\s/gm, '')
					// 	
					// 	$msg.hide()
					// 		.append( '<span class="ajax_error" />')
					// 		.find( 'span.ajax_error' )
					// 		.html( base.ajaxError + '<br /><code>' + base.ajaxPath + '</code>' )
					// 		.end()
					// 		.slideDown( 500 );
				
					// Ha! we found some themes atlast!
					} else {
						
					upList = resp[ 'links' ];
					// Remove the placeholder
					base.table.find('tr.placeholder').remove();
					for ( keyss in upList ) {
						// Check if the file already exists
						if( typeof base.fileList[ keyss ] == "undefined" ) {
							vales = upList[ keyss ];
							base.fileList[ keyss ] = vales;
							base.table
								.append('<tr />')
								.find( 'tr:last' )
								.append( '<td>' + keyss + '</td><td><a target="_blank" href="' + vales + '">' + vales + '</a></td><td><a class="jqui_theme_delete" title="Remove this theme" href="#">Delete</a></td>');
						} else {
							// gather the refused files!
							if ( typeof base.rej == "undefined" ) base.rej = {};
							// console.log( "The value already exists!" ); 
							delete upList[ keyss ];
							value = upList[ keyss ];
							base.rej[ keyss ] = value;

						}
						
						}
						
						jQuery( 'div.jqui_ajax_info span.ajax_info' ).html('');
						
						msg = '';
						
						// Display the files added
						if ( ! jQuery.isEmptyObject( upList ) ) {
						// $msg
							// .hide()
							msg += '<span class="ajax_success">The following styles found were added successfully to the list. Click "Save Changes" to save them.';
							msg += '<ol>';
							for( keysz in upList ) {
								msg += '<li>' + keysz + '</li>';
							}
							msg += '</ol>';
							msg += '</span>';
						}
						
						// display the rejected files
						if ( typeof base.rej != "undefined" ) {
							// console.log( $msg ); 
							// $msg
							// 	.append('<span class="ajax_info" />')
							// 	.find( 'span.ajax_info' )
								
								msg += '<span class="ajax_info">The following styles are already on the list, hence were not added.';
								msg += '<ol>';
								for ( rejKeys in base.rej ) {
									msg += '<li>' + rejKeys + '</li>';
								}								
								msg += '</ol></span>';							
							
						}
						
						base.showInfo( "Success", msg );
												
						// $msg.slideDown( 300 );
						jQuery( base.fileList ).trigger( "change" );
						
						setTimeout( function() {
							// jQuery( 'div.jqui_ajax_info' ).slideUp(300, function() {
								
							base.infoD.dialog( 'close' );	
							// jQuery( 'div.jqui_ajax_info').remove();
							callback();
								
							// });
							delete base.rej;
							delete upList;
							delete base.ajaxError;
						}, 10000 );		
						
						// console.log( upList ); 
						
						callback();
						
						// // console.log( base.rej ); 
					}
					return false;
				});
		};


		base.validate = function( ) {
			base.validMsg = {};
			base.valid = true;
			
			theme_name = jQuery( '#jqui_theme_name' ).val();
			theme_url = jQuery( '#jqui_theme_url' ).val();
			theme_notes = jQuery( 'div.theme_add_notes' );
			theme_notes.find('ol').remove();
			
			if ( theme_name == '' ) {
				base.valid = false;
				base.validMsg.name = "Name(CSS scope) must not be empty.";
			} else if ( !( /^[\w\-_]*$/im ).test( theme_name ) ) {
				base.valid = false;
				base.validMsg.name = "Name shall contain only alphabets, digits, hyphens and underscore. It is the CSS scope you selected while downloading the theme.";
			}
		
			if ( theme_url == '' ) {
				base.valid = false;
				base.validMsg.url = "Link to the stylesheet is needed.";
			} else if ( ! (/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[\-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i).test(theme_url) ) {
				base.valid = false;
				base.validMsg.url = "Please verify the link.";
			}
		
			// if ( typeof base.fileList[ theme_name ] != "undefined" ) {
			// 	base.valid = false;
			// 	base.validMsg.name = "Name conflict."
			// }
			
			if ( ! base.valid ) {
				if ( typeof jQuery.ui != "undefined" )
					jQuery( '.ui-dialog' ).effect('shake', { times : 5 }, 60 );
				theme_notes
					.append('<ol style="display : none "/>');
				tempOl = theme_notes.find('ol');
				for ( zeus in base.validMsg ) {
					tempOl
						.append( '<li>' + base.validMsg[zeus] + '</li>' );
				}
				tempOl.animate({
					height : 'toggle',
					opacity : 'toggle'
				}, 300);
				
			}

		};



		base.addDetails = function() {
			nameExists = false;
			
			if ( typeof base.fileList[ theme_name ] == "undefined" ) {
			base.fileList[ theme_name ] = theme_url;
			
			jQuery(base.fileList).trigger('change');
			base.table.find('tbody tr.placeholder').remove();
			
			base.table
				.find('tbody')
				.append( '<tr />')
				.find( 'tr:last' )
				.hide()
				.append( '<td>' + theme_name + '</td><td><a href="' + theme_url + '" target="_blank">' + theme_url + '</a></td><td><a href="#" class="jqui_theme_delete" >Delete</a></td>' )
				.fadeIn( 600 );
			// console.log( base.fileList ); 
			}
			base.themeForm.attr( 'title' , '' );
			base.themeForm.dialog( 'destroy' );		

		};

		base.editDetails = function( element, arr ) {
			if ( arr[0] ===  arr[2] && arr[1] === arr[3] ) {
				base.themeForm.attr( 'title' , '' );
				base.themeForm.dialog('destroy');	
				return false;			
			}
			// base.validate();
			
			// if ( base.valid ) {
				nameExists = false;

				for( key in base.fileList ) {
					if ( arr[ 2 ] == key ) 
					nameExists = true;
				}

				if ( nameExists ) {
					delete base.fileList[ arr[ 2 ] ];
				}

				element
					.find( 'td' ).eq( 0 )
					.text( arr[ 0 ] );
				tableTr
					.find( 'td' ).eq( 1 )
					.html( '<a target="_blank" href="' + arr[ 1 ] + '">' + arr[ 1 ] + '</a>'	);

				base.fileList[ arr[ 0 ] ] = arr[ 1 ];
				jQuery( base.fileList ).trigger( 'change' ); 
			
			
			// }
			
			base.themeForm.attr( 'title' , '' );
			base.themeForm.dialog('destroy');
			
		};



		base.init();
    };
    
    $.wpui.jquiThemeManage.defaultOptions = {
			
    };
    
    $.fn.wpui_jquiThemeManage = function(options){
        return this.each(function(){
            (new $.wpui.jquiThemeManage(this, options));
        });
    };
    
    
	$.wpui.selectStyles = function( el, options ) {
        var base = this;
        base.$el = $(el);
        base.el = el;

        base.$el.data("wpui.selectStyles", base);
		
		base.init = function( ) {
			base.o = $.extend( {}, $.wpui.selectStyles.defaults , options );
			
			base.addForm();
			base.formBinders();
			
			base.stylesList = [];
			
			
			
			
		};

		base.addForm = function() {
			var data = {
				action : 'selectstyles_list'
			},
			response;
			
			// Add a hidden dialog form to the body, later used for add or delete.
			base.selectForm = jQuery( '<div style="display : none; " title="" id="multiple_styles_form" />')
				.append( '<form><fieldset /></form>' )
				.find( 'form fieldset' )
				.append( '<div class="theme_add_notes" />')
				.append( '<div class="check_styles_lists">Uncheck the styles you don\'t want to load. Drag to reorder the styles. These are loaded only if "Load Multiple Styles" is checked.<ul id="wpui-sortable"></ul></div>')
				.end()
				.appendTo( 'body' );
			
			
			jQuery.post( ajaxurl, data, function( response ) {
				if ( response == '404' ) return false;	

				try {
					base.stylesList = JSON.parse(response);
				} catch ( err ) {
					base.stylesList = response || ["wpui-gene", "wpui-light", "wpui-blue", "wpui-red", "wpui-green", "wpui-dark", "wpui-quark", "wpui-cyaat9", "wpui-android", "wpui-safle", "wpui-alma", "wpui-macish", "wpui-achu", "wpui-redmond", "wpui-sevin"];					
				}

				
				
				
				
				base.storedList = [];
				if ( jQuery( '#selected_styles' ).val() )
					base.storedList = JSON.parse( jQuery( '#selected_styles' ).val() );
				liDStr = '';
				
				// console.log( base.stylesList );
				for( i =0; i < base.stylesList.length; i++ ) {
					addScore = base.stylesList[ i ].replace( /\-/im, '_' );
					isDis = (jQuery.inArray( base.stylesList[ i ], base.storedList ) == '-1');
					if (  isDis ) {
						liClass = 'checkbox-holder ui-state-disabled';
						checKed = '';
					} else {
						liClass = 'checkbox-holder';
						checKed = 'checked="checked"';
					}
					liStr = '<li class="' + liClass + '" id="' + base.stylesList[ i ] + '"><input type="checkbox" name="' + addScore + '" id="' + addScore + '" value="on" ' + checKed + ' /><label for="'+ addScore + '">' + base.stylesList[ i ] + '</label></li>';
					if ( isDis ) {
						liDStr += liStr;
					} else {					
					jQuery( '.check_styles_lists ul' )
						.append( liStr );
					}
				}
				
				jQuery( '.check_styles_lists ul' )
					.append( liDStr );
				
				
			});
			
			
			base.formBinders = function() {
				jQuery( '#wpui-combine-css3-files' ).click(function() {

					base.selectForm
						.attr( 'title' , 'Select multiple styles' )
						.dialog({
							width : 400,
							modal : true,
							buttons : {
								"Cancel" : function( ) {
									jQuery( this ).dialog('destroy');
									jQuery( this ).attr( 'title' , '' );
								},
								"Select" : function() {
									jQuery( '#wpui-sortable' ).trigger( 'sortchange' );
									jQuery( this ).dialog( 'destroy' );
								}
							},
							open : function() {
								jQuery( 'div.theme_add_notes' ).find('ol').remove();
								jQuery( '.ui-button:first' , '.ui-dialog-buttonset' )
									.addClass('cancel-button');
								jQuery( '.ui-button:last' , '.ui-dialog-buttonset' )
									.addClass('save-button');
							}


						});

					return false;
				});				
			};



			jQuery( '#wpui-sortable' ).sortable({
				items : "li:not(.ui-state-disabled)",
				cancel : ".ui-state-disabled",
				containment : 'parent'				
			});

			jQuery( '#wpui-sortable' ).bind( 'sortchange', function() {
				SLarr = jQuery( this ).sortable( 'toArray' );

				jQuery( 'textarea#selected_styles' )
					.val( JSON.stringify( SLarr ) );				
			});


			jQuery( '#wpui-sortable li input' ).live( 'change', function() {

				
				if ( jQuery( this ).is(':checked') ) {
					jQuery( this ).parent().removeClass( 'ui-state-disabled' );
				} else {
					jQuery( this ).parent().addClass( 'ui-state-disabled' );
					jQuery( this ).parent()
						.fadeOut( 300, function() {
							jQuery( this )
							.appendTo( jQuery( this ).parent() )
							.fadeIn( 300 );
						});
				}
				jQuery( '#wpui-sortable' ).sortable( 'refresh' );			
				jQuery( '#wpui-sortable' ).trigger( 'sortchange' );			
				
					
			});
			
			
			
			
			
		};
		

		
		
		base.init();
	};
	
	$.wpui.selectStyles.defaults = {
		
	};
	
	$.fn.wpui_selectStyles = function( options ) {
	 	return this.each(function() {
			(new $.wpui.selectStyles( this, options ) );
		});
	};
	
	
})( jQuery );

jQuery( document ).ready(function() {
	// jQuery( '#optionsform input[type=checkbox]' ).wpuiToggleSwitch();

	jQuery( 'textarea#selected_styles' ).parent().parent().hide();
	

	
	jQuery( '#jqui_custom_themes' ).wpui_jquiThemeManage();
	jQuery( '#wpui-combine-css3-files' ).wpui_selectStyles();
	


	
});


(function( $ ) {
	$.widget( 'wpui.wpuiToggleSwitch', $.ui.mouse, {
		_state : 'off',
		options: {
			animation : 'simpletoggle',
			speed : 300,
			easing: 'swing',
			event : 'drag',
			threshold : 10,
			classes	: false
		},
		_create	 : function() {
			var el = this.element, elID = el.attr( 'id' ), self = this;
			this.thr = this.options.threshold;
						
			// this._setState( this.element.val() == 'on' ? 'on' : 'off' );

			this.switche = $( '<div id="' + elID + '-switch" class="wpui-toggle-switch ui-switch-off" />' ).insertAfter( el );
			this.switchWrap = $( '<div id="' + elID + '-wrapper" class="wpui-toggle-switch-wrapper" />' ).appendTo( this.switche );
			this.switchHandle = $( '<span id="' + elID + '-handle" class="wpui-toggle-switch-handle" />' ).appendTo( this.switchWrap );
			
			this.swWidth = this.switchHandle.outerWidth();
			
			this.switche.css({
				position : 'relative',
				overflow : 'hidden',
				width : ( ( this.swWidth * 2 ) + this.thr ) 
			});

			this.switchWrap
			.css({
				position : 'relative',
				display : 'inline-block',
				width : ( this.swWidth * 3 ) + ( this.thr * 2 ),
				left : (this.swWidth + this.thr)
			});
			
			this.switchHandle.css({
				position : 'absolute',
				display : 'block',
				'float' : 'left',
				left : this.swWidth + this.thr
			});
			
			// this.element.hide();
			
			this.switchWrap.prepend( '<span class="ui-statuses ui-status-on">On</span>' );
			this.switchWrap.append( '<span class="ui-statuses ui-status-off">Off</span> ');
			
			this.switchHandle
			.siblings()
			.css({
				width : ( this.switchHandle.width() + this.thr ),
				position : 'static'
			}).disableSelection();		
		
			this.element.hide();
		
		},
		_init	: function() {
			var self = this, cssObj = {};
			
			this._restore();
			this.element.bind( 'change', function() {
				self._toggle();
			});	
			
			if ( this.options.classes ) this.switche.addClass( this.options.classes );
			
			if ( this.options.event == 'drag' ) {
				this._mouseInit();
			} else {			
				this.switche.bind( 'click', function() {
					self._toggle();
				});					
			}

			
		},
		_toggle : function( init ) {
			init = init || false;
			var self = this, state = self._getState();

			// state is gonna be on.
			lEfT = ( state == 'off' ) ? ( ( self.swWidth * -1 ) - self.thr ) : 0;
			
			// ( state == 'off' ) ? self.switchHandle.next().show() : self.switchHandle.prev().show();
			
			self.switchWrap.stop().animate({
				left : lEfT
			}, self.options.speed, self.options.easing, function() {
				// ( state == 'off' ) ? self.switchHandle.prev().hide() : self.switchHandle.next().hide();
				if ( ! init )
				self._setState( ( state == 'off' ) ? 'on' : 'off' );
			});			

		},
		
		_restore : function() {
			if ( this.element.is( ':checked' ) ) this._setState( 'on' );
			else this._setState( 'off' );				
			this._toggle( false );

		},
				
		_mouseInit : function() {
			var mouse_Down = false, self = this, cos= {}, 
			finFunc = function() {
				$( document ).trigger( 'mouseup' );
				mouse_Down = false;
				return false;
			};
			
			this.switchHandle.bind( 'mousedown', function( e ) {
				mouse_Down = true;
				
				cos.x1 = e.pageX;
		

			});
			
			$( document ).bind( 'mouseup', function( e ) {
				mouse_Down = false;
			});
		
		
		
			this.switchHandle.bind( 'mousemove', function( e ) {
				if ( mouse_Down == false ) return;
				cos.x2 = e.pageX;
				
				cosmin = cos.x2 - cos.x1;
				
				prevPos = self.switchWrap.position().left;
				
				newX = prevPos - cosmin;				

				if ( cosmin > 0 && self._getState() != 'off' ) {
					// console.log( "right" ); 
					self._toggle();
				} else if ( cosmin < 0 && self._getState() != 'on' ) {
					// console.log( "left" ); 
					self._toggle();					
				} else {
					finFunc();
				}
				
				return false;
				

				
				// if ( ( self._getState() == 'on' && timse > 0 ) ||
				// 	( self._getState() == 'off' && timse < 0 && Math.abs( timse ) > ( self.switchWrap.width() - self.thr ) ) ) return false;
				// 
				// // if ( ( timse > 0 && self._state == 'off' ) || ( timse > self.switchWrap.width() / 2 ) && self._state == 'on' ) {
				// // 	$( document ).trigger( 'mousemove' );
				// // 	return false;
				// // }
				// 		
				// if ( Math.abs( timse ) > ( self.switchWrap.width() / 2 ) || timse < 0 ) {
				// 	self._toggle();
				// 	return false;
				// } else {				
				// 	jQuery( this ).parent().stop().animate({
				// 		left : timse
				// 	}, self.options.speed, self.options.easing );
				// }
			});

			this.switchWrap.children().bind( 'dragstart selectstart', function( e ) {
				e.preventDefault();
				return false;
			});

			this.switche.find( 'span').bind( 'dragstart selectstart', function( e ) {
				e.preventDefault();
				return false;
			});


			// this.switchWrap.draggable({
			// 	axis : 'x',
			// 	containment : 'parent',
			// 	// revert : true,
			// 	handle : 'span.wpui-toggle-switch-handle',
			// 	start : function() {
			// 		
			// 	}
			// });
			
		
		},
		_setState : function( stat ) {
			if ( stat == 'on' ) {
				this.element.removeAttr( "checked" );
			} else {
				this.element.attr( 'checked', 'checked' );
			}
						
			return this._state = stat;
		},
		_getState : function( ) {
			return this._state;
		},
		_destroy : function() {
			this.element.next( 'div.' + elID + '-switch' ).remove();
						
			
			
		},

		_setOption : function( key, value ) {
			this.options[ key ] = value;
			
			switch( key ) {
				case 'state':
					if ( ! value ) return this._getState();
					else return this.toggle();
					break;
			}
			
		}

		
		
	});	

	// $.widget( "wpui.wpuiToggleSwitch", $.ui.mouse, $.wpui.toggleSwitch );
	
})( jQuery );
(function( $ ) {
	
	$.widget( "wpui.selectBox", {
		_create: function() {
			var self = this,
				select = this.element.hide(),
				selected = select.children( ":selected" ),
				value = selected.val() ? selected.text() : "",
				selectDiv = $( '<div class="ui-select-div" />' ).insertAfter( select );
				self.maxLength = 1;
			var input = this.input = $( "<input>" )
				.appendTo( selectDiv )
				.val( value )
				.autocomplete({
					delay: 0,
					minLength: 0,
					// appendTo : select.parent(),
					source: function( request, response ) {
						var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
						response( select.find( "option" ).map(function() {
							var text = $( this ).text();
							self.maxLength = text.length + 10;
							if ( this.value && ( !request.term || matcher.test(text) ) )
								return {
									label: text.replace(
										new RegExp(
											"(?![^&;]+;)(?!<[^<>]*)(" +
											$.ui.autocomplete.escapeRegex(request.term) +
											")(?![^<>]*>)(?![^&;]+;)", "gi"
										), "<strong>$1</strong>" ),
									value: text,
									category : ($(this).parent().is('optgroup')? $( this ).parent().attr('label') : 0 ),
									option: this
								};
						}) );
	
					},
					position : {
						my : 'left top',
						at : 'left bottom',
						collision : 'none',
						offset : "-5 0"
						
					},
					create : function() {
						self.maxLength = 1;
						select.find( 'option' ).each(function() {
							var text = jQuery( this ).text();
							if ( text.length > self.maxLength )
								self.maxLength = text.length;
							if ( self.maxLength < 5 )
								self.maxLength = 5;
						});

						selectDiv
							.width( self.maxLength * 10 )
							.find( 'input' )
							.width( ( self.maxLength * 10 ) - 25 );
						
							
						// input.width( self.maxWidth );						
					},
					select: function( event, ui ) {
						ui.item.option.selected = true;
						self._trigger( "selected", event, {
							item: ui.item.option
						});
					},
					change: function( event, ui ) {
						if ( !ui.item ) {
							var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
								valid = false;
							select.children( "option" ).each(function() {
								if ( $( this ).text().match( matcher ) ) {
									this.selected = valid = true;
									return false;
								}
							});
							if ( !valid ) {
								// remove invalid value, as it didn't match anything
								$( this ).val( "" );
								select.val( "" );
								input.data( "autocomplete" ).term = "";
								return false;
							}
						}
					}
				
				})
				.addClass( "ui-widget ui-widget-content ui-corner-left" );

			input.data( "autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li></li>" )
					.data( "item.autocomplete", item )
					.append( "<a>" + item.label + "</a>" )
					.appendTo( ul );
			};
			
            input.data( "autocomplete" )._renderMenu = function( ul, items ) {
                    var self = this,
                        currentCategory = "";
                    $.each( items, function( index, item ) {
                        if (item.category != 0) {
                            if ( item.category != currentCategory ) {
                                ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
                                currentCategory = item.category;
                            }
                        }
                        self._renderItem( ul, item );
						if ( ul.height() > 300 ) ul.css({
							height : '300px',
							overflowY : 'scroll'
						});
                    });
             };

			this.button = $( "<button type='button'>&nbsp;</button>" )
				.attr( "tabIndex", -1 )
				.attr( "title", "Show All Items" )
				.insertAfter( input )
				.button({
					icons: {
						primary: "ui-icon-triangle-1-s"
					},
					text: false
				})
				.removeClass( "ui-corner-all" )
				.addClass( "ui-corner-right ui-button-icon" )
				.click(function() {
					// close if already visible
					if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
						input.autocomplete( "close" );
						return;
					}

					// work around a bug (likely same cause as #5265)
					$( this ).blur();

					// pass empty string as value to search for, displaying all results
					input.autocomplete( "search", "" );
					input.focus();
				});
			
			$( '.ui-menu' ).live( 'mousescroll selectstart dragstart', function( event ) {
				event.preventDefault();
				return false;
			}).children( 'li' ).live( 'hover', function() {
				$( this ).toggleClass( 'ui-state-hover' );
			});
			input.val( select.val() );

		},
		destroy: function() {
			this.input.remove();
			this.button.remove();
			this.element.show();
			$.Widget.prototype.destroy.call( this );
		}
	});
})( jQuery );