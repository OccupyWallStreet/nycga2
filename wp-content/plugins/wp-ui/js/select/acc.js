/*
 *	Component - Accordion, uses same shortcode as tabs.
 */
if ( typeof( accNames ) == 'undefined' ) accNames = [];
if ( typeof( getNextSet ) == 'undefined' ) {
	tabSet = 0;
	function getNextSet() {
		return ++tabSet;
	}
}

/*
 *	Component - Accordion
 */
jQuery.fn.wpaccord = function( options ) {
	
	var wrapper,
	loadLinks,
	getAjaxUrl, 
	o = jQuery.extend({} , jQuery.fn.wpaccord.defaults, options );
	
	
	this.each(function() {
		var $this = jQuery(this);
		
		$this.append('<p id="jqtemp" />');
		
		if ( o.wpuiautop ) {
							
			$this
				.find('p, br')
				.not('div.wp-tab-content br, div.wp-tab-content p ')
				.filter(function() {
				return jQuery.trim(jQuery(this).html()) === ''
			}).remove();

		}
		
		// var wrapcontent = $this.find('h3').next().wrap('<div class="accordion-pre">');
		wrapper = $this.find('h3:first').wrap('<div class="accordion">');
		
		// $this.find('p, br').filter(function() {
		// 	return jQuery.trim(jQuery(this).text()) === ''
		// }).remove();
		
		if ( o.wpuiautop ) {
							
			$this
				.find('p, br')
				.not('div.wp-tab-content br, div.wp-tab-content p ')
				.filter(function() {
				return jQuery.trim(jQuery(this).html()) === ''
			}).remove();

		}		
		
		wrapper.each(function() {
			jQuery(this).parent().append( jQuery(this).parent().nextUntil( 'p#jqtemp' ));
		});
		
	
		
		$this.find(o.h3Class).each(function() {
			loadLinks = jQuery(this).children(o.linkAjaxClass);
				dup = getNextSet();
				
				aparID = jQuery(this).text().replace(/\s{1,}/gm, '_');
				aparID = aparID.replace( /[^\-A-Za-z0-9\s_]/mg, '');
				
				if ( aparID.match( /[^\x00-\x80]+/ ) ) {
					aparID = 'acc-' + dup;
				}
				
				
				if ( jQuery.inArray( aparID, accNames ) != '-1' ) {
					
					aparID = aparID + '_' + dup;
					
				}
				
				jQuery(this)
					.next()
					.attr('id', aparID);			

		if ( loadLinks.length != 0) {
				getAjaxUrl = loadLinks.attr("href");
			
				loadLinks.parent().after('<div></div>');
			
				jQuery(this).next().load(wpUIOpts.wpUrl + "/" + getAjaxUrl);
			
				
				jQuery(this).text(jQuery(this).children().text());
				
			} // END check loadLinks.length

			accNames = accNames.concat( aparID );

		}); // END $this h3class.

	
		accordOpts = {};

		if ( o.autoHeight ) {
			accordOpts.autoHeight = true;
		} else {
			accordOpts.autoHeight = false;
		}
		


		// console.log( accClass.match(/acc-active-(\d){1}/im) ); 
		
		if ( o.collapse ) {
			accordOpts.collapsible = true;
			accordOpts.active = false;
		}
		
		accordOpts.animated = o.easing;
		
		accordOpts.event = o.accordEvent;

		$wpAccord = jQuery( '.accordion' ).accordion(accordOpts);

		accClass = $this.attr( 'class' );

		if ( activePanel = accClass.match(/acc-active-(\d){1}/im) ) {
			$wpAccord.accordion( 'activate', parseInt( activePanel[ 1 ] ) );
		}
				
		jQuery('.accordion h3.ui-accordion-header:last').addClass('last-child');


		// if ( o.hashChange && typeof jQuery.event.special.hashchange != "undefined" ) {

			jQuery( window ).hashchange(function() {
				aHash = window.location.hash;	
				if ( ( jQuery( aHash ).length != 1 ) || 
				   ( jQuery.inArray( aHash.replace( /^#/, '' ) , accNames ) == -1 )
				)
					return false;
				
				hashed = jQuery(window.location.hash).prevAll( o.h3Class ).length - 1;
				jQuery( window.location.hash ).parent().accordion( 'activate', hashed );
				
				return false;
			});

			jQuery( window ).hashchange();

		// }
		// $this.find('p#jqtemp').remove();
	

	});	
	
	
	
}; // END Function wpaccord. 

jQuery.fn.wpaccord.defaults = {
	h3Class			: 	'h3.wp-tab-title',
	linkAjaxClass	: 	'a.wp-tab-load',
	effect			: 	(typeof wpUIOpts != "undefined") ? wpUIOpts.accordEffect : '',
	autoHeight		: 	(typeof wpUIOpts != "undefined"  && wpUIOpts.accordAutoHeight == 'on' ) ? true : false,
	collapse		: 	(typeof wpUIOpts != "undefined"  && wpUIOpts.accordCollapsible == 'on' ) ? true : false,
	easing			: 	(typeof wpUIOpts != "undefined" ) ? wpUIOpts.accordEasing : '',
	accordEvent		:   ( typeof wpUIOpts != "undefined" ) ? wpUIOpts.accordEvent : '',
	wpuiautop		: 	true,
	hashChange 		: 	true
}; // END wpaccord defaults.
