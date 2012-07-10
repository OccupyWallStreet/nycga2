(function( $ ) {
	if ( ! $.wpui ) $.wpui = new Object();
	if ( ! $.wpui.lastState ) $.wpui.lastState = {};
	
	$.wpui.tour = {
		instance : 0,
		lastEl : false,
		currEl : false,
		init : function( items ) {
			self = this;
			self.items = items;
			if ( self.instance == 0 ) {
				self.next( 0 );
			}

		},
		
		next : function( instance ) {
			itLe = self.items.length;

			if ( self.instance >= itLe ) {
			if ( typeof( self.lastBefore ) == 'function' ) {
				self.lastBefore();
			} 
			it = {
			id : '#wp-admin-bar-my-account',
			content :'<h3>Congrats</h3><p>You\'ve completed your tour! We hope the tour was useful.</p><p>Please press "close" to end this tour.</p>' + self.closeContent,
			position : 'top',
			button : ''
			};
			} else {		
			
			it = self.items[ self.instance ];

			}
			
			self.pointer( it );
			
			
		},
		
		prev : function() {
			
		},
		
		close : function() {
			
		},
		pointer : function( it ) {
			it['pcall'] = it['pcall'] || false;
			self.lastEl = self.currEl;

			if ( self.lastEl ) {
				jQuery( self.lastEl ).pointer( 'destroy' );
			}

			self.currEl = it['id'];
			button_func = function( event, t ) {
				cbutton = jQuery('<a id="close-pointer" class="button-secondary">Close</a>');
				cbutton.bind( 'click.pointer', function() {
					t.element.pointer('close');
					if ( typeof(self.dismiss) == 'function' ) {
						self.dismiss();
					}
				});
				return cbutton;
			};
			jQuery( it['id'] ).pointer({
				content	: it['content'],
				position: it['position'],
				arrow	: it['arrow'],
				buttons: button_func,
				close: function() {

		        }
				
			}).pointer( 'open' );
			
			if ( it['button'] != '' ) {
			jQuery( '#close-pointer' ).after( '<a class="button-primary" style="margin-right: 10px;" id="button-next">Next</a>' );			
			jQuery( '#button-next' ).bind( 'click', function() {
				eval( it['callback'] ); self.instance++; self.next( self.instance );		
			});

			jQuery( '#dismiss-tour' ).click(function() {
				
			});
			
			
			}
			
			if ( ! self.lastEl ) self.lastEl = it['id'];
			eval( it['addl'] );
			
			// if ( typeof( pcall ) == 'function' ) {
			// }
			
		},
		dismiss : false,
		lastBefore : false,
		closeContent : ''
	};	
	
	
	jQuery.fn.wpuiHilite = function( options ) {
		opts = jQuery.extend({}, jQuery.fn.wpuiHilite.options, options );
		
		this.each(function() {
			var self = this; self.$el = jQuery( this );
			self.atarget = self.$el.attr( 'rel' );
			self.$el.hover(function() {
				jQuery( self.atarget ).toggleClass( 'highlighted-item', 1000 );
				return false;
			}, function() {
				jQuery( self.atarget).toggleClass( 'highlighted-item' , 1000 );
			});
			
		});		
		
		return this;
	}; // end wpuiHilite
	
	jQuery.fn.wpuiHilite.options = {
		color	: 'lime'
	};
	
})( jQuery );
