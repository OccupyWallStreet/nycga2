(function( $ ) {
	if ( ! $.wpui ) $.wpui = {};
	
	$.wpui._spoilerHashSet = false;
	
	$.widget( 'wpui.wpspoiler', {
		options : {
			hideText : ( typeof wpUIOpts != "undefined" ) ? wpUIOpts.spoilerHideText : '',
			showText : ( typeof wpUIOpts != "undefined" ) ? wpUIOpts.spoilerShowText : '',
			fade	 : true,
			slide	 : true,
			speed	 : 600,
			spanClass: '.toggle_text',
			headerClass : 'h3.ui-collapsible-header',
			openIconClass : 'ui-icon-triangle-1-e',
			closeIconClass : 'ui-icon-triangle-1-s',
			autoOpen : false
		},
				
		_create	: 	function() {
			var base = this;
			this._isOpen = false;
			this._trigger( 'create' );
			this._spoil(); 
			this._hashSet = false;

			// $.wpui.wpspoiler.instances.push( this.element );
			$.wpui.wpspoiler.instances[ this.element.attr('id') ] = this.element;
		
			
		},
		
		_spoil : function( init ) {
			var self = this;
			self.o = this.options;
			this._trigger( 'init' );
			self.element.addClass( 'ui-widget ui-collapsible ui-helper-reset' );
			
			this.header = this.element.children( 'h3' ).first();
			this.content = this.header.next( 'div' );
			
			this.header.prepend( '<span class="ui-icon ' + self.o.openIconClass + '" />' )
					.append( '<span class="' + this._stripPre( self.o.spanClass )   + '" />');	
								
			this.header.addClass( 'ui-collapsible-header ui-state-default ui-widget-header ui-helper-reset ui-corner-top' )
					.children( self.o.spanClass )
					.html( self.o.showText );

			this.content
				.addClass( 'ui-helper-reset ui-state-default ui-widget-content ui-collapsible-content ui-collapsible-hide' )
				.wrapInner( '<div class="ui-collapsible-wrapper" />' );
			
			this.animOpts = {};
			if ( self.o.fade ) this.animOpts.opacity = 'toggle';
			if ( self.o.slide ) this.animOpts.height = 'toggle';
					

		},
		
		_init : function() {
			var self = this;
			this._isOpen = false;
			
			// this.hashGo();		

			if ( this.options.autoOpen || this.header.hasClass( 'open-true' ) ) this.toggle();
			
			self.header.bind( 'click.wpspoiler', function() {
				self.toggle();
			})
			.hover( function() { $( this ).toggleClass( 'ui-state-hover' ) });

			this.content.find( '.close-spoiler' )
			.wrapInner( '<span class="ui-button-text" />')
			.addClass('ui-state-default ui-widget ui-corner-all ui-button-text-only' )
			.click(function() {
				self.toggle(); return false;
			});			
			
		},
		_stripPre : function( str ) {
			return str.replace( /^(\.|#)/, '' );
		},		
		toggle : function() {
			var TxT = ( this.isOpen() ) ? this.options.showText : this.options.hideText;
			this.header
				.toggleClass( 'ui-corner-top ui-corner-all ui-state-active' )
				.children( '.ui-icon' )
				// .removeClass( this.options.closeIconClass )
				// .addClass( this.options.openIconClass )
				.toggleClass( this.options.openIconClass + ' ' + this.options.closeIconClass )
				.siblings('span')
				.html( TxT );
				
			this.animate();
			
			if ( this.isOpen() ) {
				this._trigger( 'close' );
				this._isOpen = false;
			} else {
				this._trigger( 'open' );
				this._isOpen = true;
			}
		},
		open : function() {
			if ( this.isOpen() ) this.toggle();
			
		},
		close : function() {
			if ( ! this.isOpen() ) this.toggle();
		},
		animate : function() {
			this.content.animate( this.animOpts, this.options.speed, this.options.easing, function() {
			});			
		},	
		isOpen : function() {
			return this._isOpen;
		},
		
		destroy : function() {
			
			this.header
				.removeClass( 'ui-collapsible-header ui-state-default ui-corner-all ui-helper-reset' )
				.find( 'span' )
				.remove();
				
			this.header.unbind( 'click.wpspoiler' );
			
			this.content
				.children()
				.unwrap()
				.end()
				.removeClass( 'ui-collapsible-content ui-corner-bottom ui-helper-reset');
			
			this.removeClass( 'ui-collapsible ui-widget' );
			
			$.Widget.prototype.destroy.call( this );
		},
		_getOtherInstances : function( dall ) {
			var element = this.element,
			all = dall || false;
			
			if ( ! all  ) {			
				return $.grep( $.wpui.wpspoiler.instances, function( el ) {
					return el != element;
				});
			} else {
				return $.wpui.wpspoiler.instances;
			}			
		},		
		_setOption : function( key, value ) {
			this.options[ key ] = value;
			
			switch( key ) {
				case 'open':
				case 'close':
				case 'toggle':
					this.toggle();
					break;
				case 'destroy':
					this.destroy();
					break;
				case 'status':
					return (this._isOpen() ? 'Open' : 'closed' );
					break;
				case 'goto':
					return this.hashGo( value );
					break;
			}
		}
	});
	
	$.extend( $.wpui.wpspoiler, {
		instances : {}
	});


	$.fn.wpspoilerHash = function() {
		if ( $.wpui._spoilerHashSet ) return this;
	
		$( window ).bind( 'hashchange', function() {
			var some = $.bbq.getState(),
			spoils = $.wpui.wpspoiler.instances;

			if ( typeof( some ) == 'object' && typeof( spoils ) == 'object' ) {

				for ( so in some ) {
					if ( some[ so ] instanceof Array ) {
						var i = some[so].length;
						while( i-- ) {
							if ( spoils[ some[ so ] ] )
							spoils[ some[ so ][ i ] ].wpspoiler( so ); 
						}
					} else {
						if ( spoils[ some[ so ] ] )
						spoils[ some[so] ].wpspoiler( so );
					}							
				}
				return false;
			}
		});			
		
		$( window ).trigger( 'hashchange' );
		
		$.wpui._spoilerHashSet = true;
		
		
		return this;
	};
	

	$.widget( 'wpui.wpuiClickReveal', {
		options : {
			spanClass : 'span.wpui-click-reveal',
			showText : '<b>spoiler!</b>',
			hideText : '<b>Hide</b>',
			autoShow : false
		},
		_state : 'off',
		instances : {},
		_create : function() {
			var self = this, el = this.element;
			this.handle = $( '<span class="wpui-click-handle">' + this.options.showText + '</span>' )
							.insertBefore( el );
			
		},
		_init : function() {
			var self = this;
			if ( ! this.options.autoShow ) {
				this.element.toggle();
				this._getState( 'off' );
			} else {
				this._getState('on');
			}
			this.handle.click( function() {
				self.element.toggle( 'fast', function() {
					self._getState( this._state == 'on' ? 'off' : 'on' );
				});
			});
		},
		_getState : function( stat ) {
			if ( ! stat ) {
				return this._state;
			} else {
				return this._state = stat;
			}
		},
		_destroy : function() {
			
		}
	});	

	
	
	
	
})( jQuery );
jQuery( document ).ready(function() {
	// jQuery( '.wp-spoiler' ).bind( 'wpspoilercreate', function() {
		jQuery( '.wp-spoiler' ).wpspoilerHash();
		jQuery( '.wpui-click-reveal' ).wpuiClickReveal();
	// });


});
