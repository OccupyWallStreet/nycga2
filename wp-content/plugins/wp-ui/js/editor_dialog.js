jQuery( document ).ready(function() {

	jQuery( '#wpui-editor-dialog' ).wpuiEditor({
		store	: '#_wpui-editor-dialog'
	});
	
	jQuery( '#wpui-editor-dialog .wpui-reveal' ).live('click', function() {  
		jQuery( this ).next('div').slideToggle( 'fast' );
		jQuery( this ).toggleClass( 'toggle-arrow-active' );
		jQuery( this )
			.siblings('.wpui-reveal' )
			.next('div')
			.hide();	
	});
	
	
});


(function($){
    $.wpui = $.wpui || {};
    var $store;
    $.wpui.editor = function(el, options){
        var base = this, inited, process;
        base.$el = jQuery(el);
        base.el = el;
        base.$el.data("wpui.editor", base);
        
        base.init = function(){
			process = liveVal = {};
            base.o = $.extend({},$.wpui.editor.defaultOptions, options);
			base.process = $.extend( {}, $.wpui.editor.process, process );
			base.liveVal = $.extend( {}, $.wpui.editor.liveVal, liveVal );
			base.bindings = {};
			base.throb = '<div style="padding: 10px; margin: 10px; text-align: center;" class="wpui-waiting">Please wait  <img src="' + pluginVars.pluginUrl + 'images/wpspin_light.gif" /></div>';
			
            inited = base.$el.data( 'dialog' );
			if ( ! $store ) $store = jQuery( base.o.store );
			base.create();
			
			if ( base.o.mode != ''  && typeof(base.o.mode) != 'undefined' )
			 	base.open( base.o.mode );	
       	};
		
		base.create = function() {
			if ( ! base.$el.data( 'dialog' ) ) {
				base.$el.dialog({
					dialogClass : 'wp-dialog',
					width : 450,
					modal : true,
					autoOpen : false,
					buttons : [
					{
						text : 'close',
						'class' : 'button-secondary',
						click : function() {
							jQuery( this ).dialog( 'close' );
						}
					}, {
						text	: 'Insert',
						'class'	: 'button-primary',
						click	: function() { 
							base.insert();
						}
					}],
					open : function() { base.open(); },
					close : function() { base.unbinders(); }
				});	
				
			}
		};	
		
        base.open = function(){
		
			mode = base.o.mode;
	
			if ( mode === '' ) return false;
			
			base.$el.html( base.throb );			
		
			var selecTxt = base.getSelection(),			
			wwrap = '<div class="wpui-dialog-wrapper" />', dtitle;
			mode = mode || 'addtab';

			jQuery.post( ajaxurl, {
				action : 'wpui_get_editor_dialog',
				nonce : jQuery( '#wpui-editor-main-nonce' ).val(),
				panel : mode
			}, function( data ) {
				base.$el.html( data ).wrapInner( wwrap );
				dTitle = base.$el.find('.wpui-dialog-title').html();
				base.$el.find('.wpui-dialog-title').remove();

				base.$el.dialog({ title : dTitle });
				
				if ( mode == 'wraptab' ) base.loadTax();
				else if( mode !== 'addfeed' ) base.loadPosts();
			
				base.$el.find( 'textarea' ).val( selecTxt );
				// 
				// if ( $store.find( '#wpui-editor-mode' ).length == 0 )
				// $store.append( '<input type="hidden" id="wpui-editor-mode" />' );

				base.$el.dialog( 'open' );
				if ( typeof( base.liveVal[ mode ] ) == 'function' ) {
					base.liveVal[ mode ]( base );
				}
				
			});
        };
		
		base.close = function() {
			// clear = clear || false;
			// if ( clear ) base.$el.html( '' );
			// base.$el.html( '' );
			base.$el.dialog( 'close' );
		};

		base.insert = function() {
        	// mode = mode || 'addtab';
        	// var mode = $store.find( 'input#wpui-editor-mode' ).val();

			mode = base.o.mode;
			
			valError = false;
			ins = '';
			args = '';

			base.process[ mode ]( base );
			
			// // MODE is add new tab.
			// if ( mode == 'addtab' ) {
			// 	base.process.addtab( base );
			// } else if ( mode == 'wraptab' ) {
			// 	base.process.wraptab( base );
			// }
		
		};
		
		base.binder = function( element, state ) {
			base.bindings = base.bindings || {};
			
			if ( typeof(base.bindings[state]) == undefined ) {
				var eleL = base.bindings[state].length;
				base.bindings[state][ eleL ] = element;
			} else {
				base.bindings[ state ] = [];
				base.bindings[ state ][0] = element; 
			}

		};		
		
		base.unbinder = function( element, state ) {
			if ( typeof( base.bindings[ state ] ) !== 'undefined' ) {
				for ( st in base.bindings[ state ] ) {
					if ( base.bindings[ state ][ st ] == element ) {
						base.bindings[ state ][ st ];
					}
				}
			}
		};
		
		base.commit = function( content ) {
			if ( base.isMCE() ) 
				tinyMCE.activeEditor.selection.setContent( content );
			else 
				edInsertContent( edCanvas, content );
		};
		
		base.loadPosts = function( ) {

				// base.$el
				// 	.find( '.wpui-dialog-wrapper' )
				// 	.append( $store.find( '.wpui-search-posts' ).clone() );
			if ( base.$el.find( '.wpui-search-posts' ).length == 1 ) {
				
				base.$el
					.find( '.wpui-search-posts' )
					.find( 'p.wpui-reveal' )
					.insertBefore( base.$el.find( '.wpui-search-posts' ) );
				
			}  
			
			base.$el
				.find( '#wpui-search-posts-form' )
				.live('submit', function() {
			searchStr = jQuery( '#wpui-post-search-field' ).val();
			
			sOff = 0;			
			
			wpuiQuery = {
				action : 'wpui_query_posts',
				search : searchStr,
				number : 10,
				page : 0,
				'_ajax_post_nonce' : jQuery( '#wpui-editor-post-nonce' ).val()
			
			};			
			jQuery.post( ajaxurl, wpuiQuery, function( data ) {
				base.$el.find( '.wpui-search-results ul' ).html( data );
			});
				return false;
			}).trigger( 'submit' );
			
			base.$el.find( 'div.wpui-search-results' )
				.bind( 'scroll' , function() {
				if ( searchStr != '' ) return;
				diHeight = jQuery( this ).height() + jQuery( this ).scrollTop();
				if ( diHeight > jQuery( this ).find('ul').height() ) {
					wpuiQuery.page++;
					jQuery( this ).find( '.wpui-waiting' ).show();
					jQuery.post( ajaxurl, wpuiQuery, function( data ) {
						base.$el.find( '.wpui-search-results ul' ).append( data );
						jQuery( this ).find('.wpui-waiting' ).hide();
					});
				}					
			});
			
			
			
			base.binders();
		};
			
		base.loadTax = function() {
			base.$el
				.find( '#wpui-tax-search-form' )
				.live('submit', function() {
			searchStr = base.$el.find( '#wpui-tax-search-field' ).val();
			searchType = base.$el.find( '#wpui-tax-search-type' ).val();
			
			wpuiQuery = { action : 'wpui_query_meta', search : searchStr, type : searchType, number : 10, '_ajax_tax_nonce' : jQuery( '#wpui-editor-tax-nonce' ).val()	};		
			jQuery.post( ajaxurl, wpuiQuery, function( data ) {
				base.$el.find( '.wpui-search-results ul' ).html( data );
			});
				return false;
			}).trigger( 'submit' );		
			
			base.$el.find( '#wpui-tax-search-type' ).live( 'change', function() {
				jQuery( '.wpui-selected' ).val('');
				jQuery( this ).parent().trigger( 'submit' );
			});
			
			base.binders();			
		};
		
		base.binders = function() {
			base.$el.find( '.wpui-search-results ul li' )
				.die( 'click' )
				.live('click', function() {
				
				if ( jQuery( this ).hasClass( 'no-select' ) ) return false;	
					
				thisVal = jQuery( this ).find( 'a' ).attr( 'rel' ).replace( /(post|cat|tag)\-/, '');
				if ( base.o.selection == 'single' ) {
				jQuery( this )
					.siblings()
					.removeClass( 'selected' );
				}
				
				jQuery( this ).toggleClass( 'selected' );
				if ( base.o.selection != 'single' ) {
					thisVal += ',';
					alSel = base.$el.find( 'input.wpui-selected' ).val();
					if ( alSel.match( thisVal ) )
						alSel = alSel.replace( thisVal, '' );
					else
						alSel += thisVal;
				} else {
					alSel = thisVal;
				}
				
				base.$el.find( 'input.wpui-selected' ).val( alSel );
							
				return false;
			});
			
		};


		
		base._setMode = function( options ) {
			if ( typeof( options ) == 'object' ) {
				base.o = $.extend( base.o, options );
				base.open();
			}
		};
		
		base.unbinders = function( ) {
			base.$el.find( '.wpui-search-results ul li' )
				.die( 'click' );
			base.$el.find( '#wpui-search-posts-form' ).die( 'submit' );
			base.$el.find( '#wpui-tax-search-form' ).die( 'submit' );
			base.$el.find( '#wpui-tax-search-type' ).die( 'change' );
			
		};
		
		base.isMCE = function() {
			return typeof(tinyMCE) != 'undefined' &&( ed = tinyMCE.activeEditor ) && ! ed.isHidden();
		};

		base.isQT = function() {
			return ( typeof edCanvas != 'undefined' && edCanvas.value != '' );
		};

		base.getSelection = function() {
			rtina = '';
			if ( base.isMCE() )
				rtina = tinyMCE.activeEditor.selection.getContent();
			else if ( base.isQT() )
				rtina = edCanvas.value.slice( edCanvas.selectionStart, edCanvas.selectionEnd );
			return rtina;
		};


        // Run initializer
        base.init();
    };
    
    $.wpui.editor.defaultOptions = {
		store 	: '',
		mode : '',
		selection : 'single'
    };
    
    $.fn.wpuiEditor = function(options){
	     return this.each(function(){
			instance = jQuery( this ).data( 'wpui.editor' );
			if ( instance ) {
				instance._setMode( options );				
			} else {		
            	(new $.wpui.editor(this, options));
			}
        });
    };
    
    // This function breaks the chain, but returns
    // the wpui.editor if it has been attached to the object.
    $.fn.getwpuiEditor = function(){
        this.data("wpui.editor");
    };
    
})(jQuery);

jQuery.wpui.editor.process = {};
jQuery.wpui.editor.liveVal = {};

jQuery.wpui.editor.process.addtab = function( base ) {
	
	tabName = base.$el.find( 'input#wpui-tab-name' ).val();
	tabCont = base.$el.find( 'textarea#wpui-tab-contents' ).val();
	
	if ( tabName == '' || tabName == 'undefined' ) {
		base.$el.find( 'span.error-message' )
			.eq(0).addClass( 'active' )
			.text( 'Tab name is required!' );
		valError = true;
	}
	postVal = base.$el.find( '#wpui-selected-post' ).val();
	
	if ( postVal == '' ) {
		if ( tabCont == '' || tabCont == 'undefined' ) {
			base.$el.find( 'span.error-message' )
				.eq(0).addClass( 'active' )
				.text( 'Tab Content is required, or select a post!' );
			valError = true;
		} else {
			ins += '[wptabtitle] ' + tabName + '[/wptabtitle] ';
			ins += '[wptabcontent]' + tabCont + '[/wptabcontent]';
		}
	} else {
		args = ' post="' + postVal + '"';
		ins += '[wptabtitle' + args + ']' + tabName + '[/wptabtitle] ';
	}
	if ( ! valError ) {
		base.commit( ins );

		base.$el.dialog('close');				
	}
	
};


jQuery.wpui.editor.process.wraptab = function( base ) {
	
	selT = '';
	selT += base.getSelection();
	
	wrArgs = '';
	wrStr = '';
	
	if ( base.$el.find( '#tabs-type' ).val() !== 'undefined' )
		wrArgs += base.$el.find( '#tabs-type').val() == 'accordion' ? ' type="accordion"' : '';	
	
	if ( base.$el.find( '#tabs-style' ).val() !== 'undefined' &&
		base.$el.find( '#tabs-style' ).val() !== 'default' ) {
		wrArgs += ' style="' + base.$el.find( '#tabs-style' ).val() + '"';
	}	
	
	if ( base.$el.find( '#tabs-effect' ).val() !== 'undefined' &&
		base.$el.find( '#tabs-effect' ).val() !== 'disable' )
		wrArgs += ' effect="' + base.$el.find( '#tabs-effect' ).val() + '"';	

	if ( base.$el.find( '#tabs-mode' ).val() !== 'undefined' )
		wrArgs += ' mode="' + base.$el.find( '#tabs-mode' ).val() + '"';		

	if ( base.$el.find( '#tabs-rotate' ).val() !== 'undefined' &&
	 	base.$el.find( '#tabs-rotate' ).val() !== '' &&
	 	base.$el.find( '#tabs-rotate' ).val().match( /\d{1,5}s?/ ) != ''
		)
		wrArgs += ' rotate="' + base.$el.find( '#tabs-rotate' ).val() + '"';

	if ( base.$el.find( '#tabs-no-bg' ).val() !== 'undefined' &&
		 base.$el.find( '#tabs-no-bg' ).is( ':checked' )
		) wrArgs += ' background="false"';
	
	selTax = base.$el.find('#wpui-selected-tax').val();
	
	if ( selTax == '' ) { 
		wrStr += '[wptabs' + wrArgs + '] ' + selT + ' [/wptabs]';
	} else {
		taxType = base.$el.find( '#wpui-tax-search-type' ).val();
		wrArgs += ' number="' + base.$el.find( '#wpui-tax-number' ).val() + '"';
		if ( taxType == 'cat' || taxType == 'tag' ) {
		wrArgs += ' ' + taxType + '="' + selTax.replace( /,$/, '' ) + '"';
		} else {
		wrArgs += ' get="' + taxType + '"'; 	
		}				
		wrStr = '[wptabposts' + wrArgs + ']';		
	}	

	base.commit( wrStr );
	base.$el.dialog('close');			
		
};

jQuery.wpui.editor.process.spoiler = function( base ) {
	valError = false;
	spls = '';
	selP = '';
	
	if ( base.$el.find( '#wpui-selected-post' ) )
		selP += base.$el.find( '#wpui-selected-post' ).val();
	
	spoilT = base.$el.find( '#spoil-title' ).val();
	spoilC = base.$el.find( '#spoil-content' ).val();
	
	if (( spoilT == '' || spoilT == 'undefined' ) && selP == '' ){
		base.$el.find( 'span.error-message' ).eq(0)
			.addClass( 'active' )
			.text( 'Title of the spoiler is required.' );
		valError = true;
	}

	if ( ( spoilC == '' || spoilC == 'undefined' ) && selP == '' ) {
		base.$el.find( 'span.error-message' ).eq( 1 )
			.addClass( 'active' )
			.text( 'Spoiler needs content!' );
		valError = true;
	}
	
	spoilArgs = '';

	if ( base.$el.find( '#spoils-open').val() != 'undefined' && base.$el.find( '#spoils-open' ).is( ':checked' ) )
		spoilArgs += ' open="true"';
	
	if ( base.$el.find( '#spoils-style' ).val() != 'undefined' && 
		base.$el.find( '#spoils-style' ).val() !== 'default' )
		spoilArgs += ' style="' + base.$el.find( '#spoils-style' ).val() + '"';
		
	if ( base.$el.find( '#spoils-closebtn' ) != 'undefined' && base.$el.find( '#spoils-closebtn').val() !== '' ) {
		spoilArgs += ' closebtn="' + base.$el.find( '#spoils-closebtn' ).val() + '"'; 
	}
	
	if ( selP == '' ) {
		edStr = ' [wpspoiler name="' + spoilT + '" '+ spoilArgs + ']' + spoilC + '[/wpspoiler] ';
	} else {
		spoilArgs += ' post="' + selP + '"';
		edStr = ' [wpspoiler' + spoilArgs + ']';
	}
	
	
	if ( ! valError ) {
	base.commit( edStr );

	base.$el.dialog( 'close' );
	
	}
	
	
};


jQuery.wpui.editor.process.addfeed = function( base ) {
	valError = valError || false;

	if ( typeof( base.$el.find( '#wpui-feed-url' ) ) == 'undefined' ) 
	valError = true;

	jQuery.post( ajaxurl, {
		action : 'wpui_validate_feed',
		feed_url : base.$el.find( "#wpui-feed-url" ).val()
	}, function( data ) {
		data =  JSON.parse(data );
		if ( typeof( data ) != 'object' ) return false;
		if ( data.status == 'error' ) {
			valError = false;
			base.$el.find( '#wpui-feed-url' ).next('span.error-message' ).text( data.description );
			jQuery( '#wpui-feed-url' ).bind( 'keydown', function() {
				jQuery( this ).next('span.error-message').text('');
			});
		} else {
			jQuery( '#wpui-feed-url' ).unbind( 'keydown' );
			edStr = '[wpuifeeds ';
			edStr += 'url="' + base.$el.find('#wpui-feed-url').val() + '" ';
			edStr += 'number="' + base.$el.find( '#wpui-feed-number' ).val() + '"';
			edStr += ']';
			base.commit( edStr );
			base.$el.dialog( 'close' );				
		}				
	
	});
};


// jQuery.wpui.editor.liveVal.addfeed = function( base ) {
// 	
// 	var keyMap = base.$el.find( '#wpui-feed-url' ).bind( 'keyup', function() {
// 		jQuery.post( ajaxurl, {
// 			action : 'wpui_validate_feed',
// 			feed_url : base.$el.find( "#wpui-feed-url" ).val()
// 		}, function( data ) {
// 			console.log( data ); 
// 			base.$el.find( '#wpui-feed-url' ).next('.error-message').text(data);
// 			if ( data !== '' ) 
// 				valError = false;
// 		});		
// 	});
// 	
// 	base.binder( keyMap, 'keyup' );
// 		
// };


jQuery.wpui.editor.process.dialog = function( base ) {
	valError = false;
	
	dialT = base.$el.find( '#dialog-title' ).val();
	dialC = base.$el.find( '#dialog-contents' ).val();

	if ( dialT == '' || dialT == 'undefined' ) {
		base.$el
			.find( 'span.error-message' ).eq ( 0 )
			.addClass( 'active' )
			.text( 'Title is required.' );
		valError = true;
	}
	
	selP = '';
	if ( base.$el.find( '#wpui-selected-post' ) )
		selP += base.$el.find( '#wpui-selected-post' ).val();	

	if ( ( dialC == '' || dialC == 'undefined' ) && selP == '' ) {
		base.$el.find( 'span.error-message' ).eq( 1 )
			.addClass( 'active' )
			.text( 'Content is required.' );
		valError = true;
	}

	dialogArgs = '';

	if ( base.$el.find( '#dialog-style' ).val() != 'undefined' &&
	 	base.$el.find( '#dialog-style' ).val() != 'default' ) {
		dialogArgs += ' style="' + base.$el.find( '#dialog-style' ).val() + '"'; 
	}

	if( base.$el.find( '#dialog-openlabel' ).val() != '' )
		dialogArgs += ' auto_open="false" openlabel="' + base.$el.find( '#dialog-openlabel' ).val() + '"';

	if (  base.$el.find( '#dialog-width' ).val() != '' )
		dialogArgs += ' width="' + base.$el.find( '#dialog-width' ).val() + '"';

	if (  base.$el.find( '#dialog-height' ).val() != '' )
		dialogArgs += ' height="' + base.$el.find( '#dialog-height' ).val() + '"';

	if ( selP == '' ) {
		edStr = ' [wpdialog title="' + dialT + '"' + dialogArgs + '] ' + dialC + ' [/wpdialog] ';
	} else {
		dialogArgs += ' post="' + selP + '"';
		edStr = ' [wpdialog' + dialogArgs + ']';
	}


	if ( !valError ) {
		base.commit( edStr );
			
		base.$el.dialog( 'close' );
			
	}
	
};




