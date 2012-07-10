/*!
 *	WP UI version 0.8.1
 *	
 *	Copyright (c) 2011, Kavin Amuthan ( http://kav.in )
 *	@license - Dual licensed under the MIT and GPL licenses.
 *	
 *	Below components Copyright and License as per the respective authors. 
 *	Thanks for their hard work.
 *	
 *	Includes jQuery cookie plugin by Klaus Hartl.
 *	Includes hashchange event plugin by Ben Alman.
 *	Includes Mousewheel event plugin by Brandon Aaron.
 *	
 *	
 *	Requires : jQuery v1.4.2, jQuery UI v1.8 or later.
 */


// document.write blank page fix. Thanks to altCognito's code on stackoverflow. ( http://stackoverflow.com/q/761190 ) from which this is adapted.
if ( typeof wpUIOpts == 'object' && wpUIOpts.docWriteFix == 'on' ) {
var docWriteTxt = "";

jQuery(function() {
  document.write = function( dWT ) {
    docWriteTxt += dWT;
  }
  // document.write("");
  jQuery( docWriteTxt ).appendTo( 'body' );

});
} // END doc write fix.


/*
 *	Component - Tabs
 */
tabSet = 0;
function getNextSet() {
	return ++tabSet;
}

var tabNames = [], accNames = [];

jQuery.fn.wptabs = function( options ) {

	var linkAttrs;
	var o = jQuery.extend({} , jQuery.fn.wptabs.defaults, options);
	
	// Assign $this.
	this.each(function() {
		uid = getNextSet();
		base = this;
		var $this = jQuery(this);
		base.jqui = false;
		
		if ( $this.hasClass( 'jqui-styles' ) ) {
			base.jqui = true;
		}

		// Add an empty UL element.
		$this.prepend('<ul class="ui-tabs-nav" />');	
		
		var wrapper = $this.children(o.h3Class).wrap('<div class="ui-tabs-panel"></div>');
		
		$this.find('div.ui-tabs-panel:last-child').after('<p id="jqtemp">');
		
		if ( o.wpuiautop ) {
							
			$this
				.find('p, br')
				.not('div.wp-tab-content br, div.wp-tab-content p ')
				.filter(function() {
				return jQuery.trim(jQuery(this).html()) === ''
			}).remove();
		}
		
		
		wrapper.each(function() {
			jQuery(this).parent().append( jQuery(this).parent().nextUntil("div.ui-tabs-panel") );
		});	
		
		// liStr = '';
		
		// Add the respective IDs to the ui-tabs-panel(content) and remove the h3s.
		$this.find('.ui-tabs-panel').children(o.h3Class).each(function( index ) {
			dup = getNextSet();
			
			parID = jQuery( this ).text();
			otherClass = jQuery( this ).hasClass( 'wpui-hidden-tab' ) ? 'wpui-hidden-tab' : '';
			
		
			// Non english characters.
			if ( parID.match( /[^\x00-\x80]+/ ) ) {
				base.nonEng = true;
				parID = 'tab-' + dup;
			}
			
			if ( jQuery( this ).has( o.linkAjaxClass ).length != 0 ) 
				base.linkAJAX = true;
			
			// Process pID.
			parID = parID.replace(/\s{1,}/gm, '_')
					.replace( /[^\-A-Za-z0-9\s_]/mg, '')
					.toLowerCase();
			
			var linkS = '';
			
			if ( jQuery.inArray( parID, tabNames ) != '-1' )
					parID = parID + '-' + dup;
				
			
			if ( base.linkAJAX ) {
			aLink = jQuery( this ).find( o.linkAjaxClass );
			linkS = '<a href="' + aLink.attr( "href" ) + '">' + aLink.text() + '</a>';
			} else if( jQuery( this ).find( 'img' ).length == 1 ) { 

				var parIMG = jQuery( this ).find( 'img' );
				while ( parIMG.parent().is( 'h3' ) != 1 ) parIMG.unwrap();
				parIMG.removeAttr( 'style' ).css( 'vertical-align', 'middle' );
				if ( parIMG.attr( 'title' ) != 'undefined' && parIMG.attr( 'title') != '' && jQuery( this ).text() == '' )
				parID = parIMG.attr( 'title' ).replace(/\s{1,}/gm, '_').replace( /[^\-A-Za-z0-9\s_]/mg, '');
				var imgLink = '<img src="' + parIMG.attr( 'src' ) + '" title="' + parIMG.attr( 'title' ) + '" />'; 
				
			linkS = '<a href="#' + parID + '">' + imgLink + jQuery( this ).text() + '</a>';
				
			} else {
			linkS = '<a href="#' + parID + '">' + jQuery( this ).text() + '</a>'; 
			}
		
			
			liStr = '<li class="' + otherClass + '">' + linkS + '</li>';
			$this.find( 'ul.ui-tabs-nav' ).append( liStr );
			
			if ( ! base.linkAJAX )
			jQuery( this ).parent().attr( 'id', parID );
			else
			jQuery( this ).parent().remove();
			
			tabNames = tabNames.concat( parID );


		}).hide();
		
		// Wrap everything inside div.ui-tabs
		if ( $this.find('div.ui-tabs').length == 0) {
			$this.find('ul.ui-tabs-nav').before("<div class='ui-tabs'>");
			$this.find('.ui-tabs').each(function() {
				jQuery(this).append( jQuery(this).nextUntil('p#jqtemp'));
			});
		}
		
		tabsobj = {};
	
		if ( o.effect == 'slideDown' ) {
		 	tabsobj.fx = { height: 'toggle', speed: o.effectSpeed};
		} else if ( o.effect == 'fadeIn' ) {
			tabsobj.fx = {opacity: 'toggle', speed: o.effectSpeed};
		}
		
		if ( o.cookies ) {
			tabsobj.cookie = { expires : 30 };
		}	
	
		if ( o.tabsEvent ) {
			tabsobj.event = o.tabsEvent;
		}
		
		if ( o.collapsibleTabs ) {
			tabsobj.collapsible = true;
		}
	
		////////////////////////////////////////////////
		///////////// Initialize the tabs /////////////
		//////////////////////////////////////////////
		var $tabs = $this.children('.ui-tabs').tabs(tabsobj);
		
		jQuery('ul.ui-tabs-nav').each(function() {
			jQuery('li:first', this).addClass('first-li');
			jQuery('li:last', this).addClass('last-li');
		});
		
		if ( o.alwaysRotate != 'disable' ) {
			jQuery( this + '[class*=tab-rotate]').each(function() {
				rotateSpeed = jQuery(this).attr('class').match(/tab-rotate-(.*)/, "$1");
				if (rotateSpeed != null ) {
					if (rotateSpeed[1].match(/(\d){1,2}s/, "$1")) rotateSpeed[1] = rotateSpeed[1].replace(/s$/, '')*1000 ;
					rotateSpeed = rotateSpeed[1];
					alwaysRotate = ( o.alwaysRotate == 'always' ) ? true : false;
			}
				jQuery(this).find('.ui-tabs')
					.tabs( 'rotate', rotateSpeed, alwaysRotate );
			});
			 
 		}
		if (o.followNav == true || $this.hasClass('tab-nav-follows')) {
			o.topNav = o.bottomNav = false;
			$tabs.append('<a href="#" class="ui-button tab-nav-follows prev-follow"><span></span>Previous</a>  <a href="#" class="ui-button tab-nav-follows next-follow"><span></span>Next</a>');
			
			jQuery('.tab-nav-follows').css({
				position: 'absolute'
			});
			
			wptabsHgt = $this.height() / 2;
			wptabsNavWdt = $tabs.children('.tab-nav-follows').outerWidth();
			$tabs.parent().css({
				position: 'relative'
			});
			
			maxPH = 0;
			$tabs.children('.ui-tabs-panel').each(function() {
				if (jQuery(this).height() > maxPH) {
					maxPH = jQuery(this).height();
				}
			});
			
			$tabs.children('div.ui-tabs-panel').innerHeight(maxPH + 50);
			jQuery('.next-follow').css({
				right: wptabsNavWdt * -1 + "px",
				top: "150px"
			}).click(function() {
				wpuiTabsMover('forward');
				return false;
			});
			jQuery('.prev-follow').css({
				left: wptabsNavWdt * -1 + "px",
				top: "150px"
			}).click(function() {
				wpuiTabsMover('backward');
				return false;
			});
			$fNavs = $this.find('a.tab-nav-follows');
			$fNavs.wpuiScroller({
				container: $tabs.get(0)
			});
		}
		
	
		if ( o.topNav || o.bottomNav ) {
		// Add previous/next navigation.
		$this.find('div.ui-tabs-panel').each(function(i) {
			// base.navClass = '';
			// base.navNextSpan = '';
			// base.navPrevSpan = '';

			// if ( base.jqui ) {
				base.navClass = ' ui-button ui-widget ui-state-default ui-corner-all';
				base.navPrevSpan = '<span class="ui-icon ui-icon-circle-triangle-w"></span>';
				base.navNextSpan = '<span class="ui-icon ui-icon-circle-triangle-e"></span>';
			// } 
			
			! o.topNav || jQuery(this).prepend('<div class="tab-top-nav" />');
			! o.bottomNav || jQuery(this).append('<div style="clear: both;"></div><div class="tab-bottom-nav" />');
			
			var totalLength = jQuery(this).parent().children('.ui-tabs-panel').length -1;
		
			if ( i != 0 ) {
				! o.topNav || jQuery(this).children('.tab-top-nav').prepend('<a href="#" class="backward prev-tab ' + base.navClass + '">' + base.navPrevSpan + o.tabPrevText + '</a>');
				! o.bottomNav || jQuery(this).children('.tab-bottom-nav').append('<a href="#" class="backward prev-tab ' + base.navClass + '">' + base.navPrevSpan  + o.tabPrevText + '</a>');		
			}
			
			if ( i != totalLength ) {
				! o.topNav || jQuery(this).children('.tab-top-nav').append('<a href="#" class="forward next-tab ' + base.navClass + '">' + o.tabNextText + base.navNextSpan + '</a>');
				! o.bottomNav || jQuery(this).children('.tab-bottom-nav').append('<a href="#" class="forward next-tab ' + base.navClass + '">' + o.tabNextText +  base.navNextSpan + '</a>');			
			}
			
			
		}); //END div.ui-tabs-panel each.
	
		jQuery('a.forward, a.backward').hover(function() {
			if ( base.jqui )
			jQuery(this).addClass('ui-state-hover');
		}, function() {
			if ( base.jqui )
			jQuery( this ).removeClass('ui-state-hover');
		}).focus(function() {
			if ( base.jqui )
			jQuery(this).addClass('ui-state-focus ui-state-active');
		}).blur(function() {
			if ( base.jqui )
			jQuery(this).removeClass('ui-state-focus ui-state-active');
			
		});
	} // END if o.navigation

	 	// $tabs.tabs('option', 'disabled', false);
		
	if ( o.position == 'bottom' || jQuery(this).hasClass('tabs-bottom') ) {
		jQuery('ul.ui-tabs-nav', this).each(function() { 					
				jQuery(this)
				.appendTo(jQuery(this).parent())
				.addClass('ul-bottom');
		});
		
		$this.children('.ui-tabs')
			.addClass('bottom-tabs')
			.children('.ui-tabs-panel')
			.each(function() { 
			jQuery(this).addClass('bottom-tabs');
		});
	} // END BottomTabs check.


	// Vertical tabsets.
	if ( $this.hasClass( 'wpui-tabs-vertical' ) ) {
		
		$tabs.addClass( 'ui-tabs-vertical ui-helper-clearfix' );
		$tabs.find('li').removeClass('ui-corner-top').addClass( 'ui-corner-left' );
		
		$tabs.find('ul.ui-tabs-nav')
			.css({ position : 'absolute' })
			.removeClass( 'ui-corner-all' )
			.addClass( 'ui-corner-left' )
			.children()
			.css({ 'float' : 'left', clear: 'left', position : 'relative' });	
		
		
		getListWidth = jQuery(this).attr('class').match(/listwidth-(\d{2,4})/, "$1");

		if ( getListWidth != null ) {
			ulWidth = getListWidth[ 1 ];
		} else {
			ulWidth = $tabs.find( 'ul.ui-tabs-nav' ).outerWidth();
		}
		// console.log( ulWidth ); 
			
		
		ulHeight = $tabs.find( 'ul.ui-tabs-nav' ).outerHeight();
		$tabs.find( 'ul.ui-tabs-nav' ).width( ulWidth );
		$tabs.find( 'div.ui-tabs-panel' ).css({ 'float' : 'right' });
		
		parWidth = $tabs.width() -
					(
					parseInt( $tabs.children( 'div.ui-tabs-panel' ).css('paddingLeft') )  +
					parseInt( $tabs.children( 'div.ui-tabs-panel' ).css('paddingRight') )  +
					parseInt( $tabs.children( 'div.ui-tabs-panel' ).css('borderRightWidth') )  +
					parseInt( $tabs.children( 'div.ui-tabs-panel' ).css('borderLeftWidth') )  
					);
		
		PaneWidth = parWidth - ulWidth;

		maxPane = 0;
		paneCount = $tabs.find( '.ui-tabs-panel' ).length;
		
		$tabs.find('.ui-tabs-panel').width( PaneWidth );
		
		$tabs.find( '.ui-tabs-panel' ).each(function() {
			if ( jQuery( this ).outerHeight() > maxPane ) {
				maxPane = jQuery( this ).outerHeight();
			}
			
		});
		
		// $tabs.find( '.wpui-tabs-arrow' ).each(function() {
		// 	jQuery( this ).height( 
		// 		jQuery( this ).parent().height() // +
		// 		 // 				parseInt( jQuery( this ).css( 'borderTopWidth' ) ) +
		// 		 // 				parseInt( jQuery( this ).css( 'borderBottomWidth' ) )
		// 		
		// 		);
		// });
		
		// if ( jQuery.browser.mozilla == true ) {
		// 	jQuery( 'body' ).append( '<style type="text/css">.tabs-arrow-svg {  clip-path : url( #c1 ); }</style><svg height="0">  <clipPath id="c1" clipPathUnits="objectBoundingBox">  <polygon style="fill:#FFFFFF;" points="0,0 0,1 0.4,1 0.8,0.5 0.4,0"/> </clipPath> </svg> ' );
		// 	jQuery( '.wpui-tabs-arrow' ).each(function() {
		// 		jQuery( this ).addClass( 'tabs-arrow-svg' );
		// 	});
		// } 
		
		
		if ( o.effect == 'slideDown' )
			$this.find('.ui-tabs').tabs({ fx : null });
		
		if ( maxPane != 0 ) {
			( maxPane > ulHeight ) ?
				$tabs.children().innerHeight( maxPane + 40 ) :
				$tabs.children().innerHeight( ulHeight + 40 );
			}
		}


	if (typeof WPUIOpts != 'undefined')
	$this.append('<a class="thickbox cap-icon-link" title="" href="http://kav.in"><img src="' + wpUIOpts.pluginUrl  + '/images/cquest.png" alt="Cap" /></a>');


	if ( jQuery.event.special.mousewheel !== "undefined" && o.mouseWheel != 'false' ) {

		if ( o.mouseWheel && o.mouseWheel == "panels" ) {
			scrollEl = 'div.ui-tabs-panel';		
		} else {
			scrollEl = 'ul.ui-tabs-nav';
		}
		
	$this.panelength = $tabs.find( '.ui-tabs-panel' ).length;

		$tabs.find( scrollEl ).mousewheel(function( event, delta) {
			if ( delta < 0 )
				dir = "forward";
			else if ( delta > 0 )
				dir = "backward";	
			typeof ( dir ) == 'undefined' || wpuiTabsMover( dir );
			return false;
		});
	}
	
	$this.find( 'a.next-tab, a.prev-tab' ).hover(function() {
		jQuery( this ).addClass( 'ui-state-hover' );
	}, function() {
		jQuery( this ).removeClass( 'ui-state-hover' );
	});
	
	$this.find( 'a.next-tab, a.prev-tab' ).click(function() {
		if ( jQuery( this ).is('a.next-tab') )
			wpuiTabsMover( "forward" );
		else	
			wpuiTabsMover( "backward" );
		return false;
	});

	// Change the corners on no-tabs-background
	if ( $this.hasClass( 'wpui-no-background' ) ) {
		$this.find( 'ul.ui-tabs-nav > li' )
			.removeClass( 'ui-corner-top' )
			.addClass( 'ui-corner-all' );
	}

	// if ( jQuery.fn.wpuiSwipe !== "undefined" && o.mouseWheel != 'false' ) {
	// 
	// 	$tabs.find( 'div.ui-tabs-panel' ).wpuiSwipe({
	// 		swipeLeft : function() { wpuiTabsMover( "backward" ); },
	// 		swipeRight : function() { wpuiTabsMover( "forward" ); }
	// 	});
	// }

		var wpuiTabsMover = function( dir ) {
			dir = dir || 'forward';
			mrel = $this.find('.ui-tabs').tabs('option', 'selected');
			mrel = ( dir == 'backward' ) ? mrel - 1 : mrel + 1;
			if ( dir == "forward" && mrel == $this.panelength ) mrel = 0;
			if ( dir == "backward" && mrel < 0 ) mrel = $this.panelength - 1;
			$tabs.tabs( "select", mrel );			
		};		
	}); // END return $this.each.	
	
	if ( o.hashChange && typeof jQuery.event.special.hashchange != "undefined" ) {
		
		jQuery( window ).hashchange(function() {
			tabHash = window.location.hash;	
			if ( ( jQuery( tabHash ).length != 1 ) || 
			   ( jQuery.inArray( tabHash.replace( /^#/, '' ) , tabNames ) == -1 )
			)
				return false;

			hashed = jQuery(window.location.hash).prevAll().length - 1;
			// console.log( window.location.hash );
			// console.log( tabNames );  
			jQuery( window.location.hash )
					.parent()
					.tabs({ selected : hashed });
			return false;
		});

		jQuery( window ).hashchange();

	} // END check availability for hashchange event.
	return this;
	
}; // END function jQuery.fn.wptabs.


jQuery.fn.wptabs.defaults = {
	h3Class			:		'h3.wp-tab-title',
	linkAjaxClass	:		'a.wp-tab-load',
	topNav			: 		(typeof wpUIOpts != "undefined"  && wpUIOpts.topNav == 'on' ) ? true : false,
	bottomNav		: 		(typeof wpUIOpts != "undefined"  && wpUIOpts.bottomNav == 'on' ) ? true : false,
	position		: 		'top',
	navStyle		: 		(typeof wpUIOpts != "undefined") ? wpUIOpts.tabsLinkClass : '',
	effect			: 		(typeof wpUIOpts != "undefined") ? wpUIOpts.tabsEffect : '', 
	effectSpeed		: 		(typeof wpUIOpts != "undefined") ? wpUIOpts.effectSpeed : '',
	alwaysRotate	: 		(typeof wpUIOpts != "undefined") ? wpUIOpts.alwaysRotate : '', // True - will rotate inspite of clicks. False - will stop.
	tabsEvent		: 		(typeof wpUIOpts != "undefined") ? wpUIOpts.tabsEvent : '',
	collapsibleTabs	: 		(typeof wpUIOpts != "undefined"  && wpUIOpts.collapsibleTabs == 'on' ) ? true : false,
	
	tabPrevText		: 		(typeof wpUIOpts != "undefined" && wpUIOpts.tabPrevText != '' ) ? wpUIOpts.tabPrevText : '&laquo; Previous',		
	tabNextText		: 		(typeof wpUIOpts != "undefined" && wpUIOpts.tabNextText != '' ) ? wpUIOpts.tabNextText : 'Next &raquo;',
	cookies			: 		(typeof wpUIOpts != "undefined"  && wpUIOpts.cookies == 'on' ) ? true : false,
	hashChange		: 		(typeof wpUIOpts != "undefined"  && wpUIOpts.hashChange == 'on' ) ? true : false,
	hashChange		: 		(typeof wpUIOpts != "undefined"  && wpUIOpts.hashChange == 'on' ) ? true : false,
	mouseWheel		: 		(typeof wpUIOpts != "undefined" ) ? wpUIOpts.mouseWheelTabs : '',
	wpuiautop		: 		true,
	followNav: false
};


jQuery.fn.wpuiScroller = function(options) {
	var base = this;
	base.$el = jQuery(this);
	base.opts = jQuery.extend({},
	jQuery.fn.wpuiScroller.defaults, options);
	base.startTop = parseInt(base.$el.css('top'));
	if (base.opts.limiter) {
		base.limiter = jQuery(base.opts.limiter);
	} else {
		base.limiter = base.$el.parent().parent();
	}
	base.startAt = parseInt(base.limiter.offset().top);
	jQuery(window).scroll(function() {
		base.endAt = parseInt(base.limiter.height() + jQuery(window).height() / 2);
		base.moveTo = base.startTop;
		if (jQuery(document).scrollTop() >= base.startAt) {
			base.moveTo = base.startTop + (jQuery(window).scrollTop() - base.startAt);
			if ((jQuery(window).scrollTop() + jQuery(window).height() / 2) >= (base.limiter.height() + base.limiter.offset().top - base.startTop)) {
				base.moveTo = base.limiter.height() - base.startTop;
			}
		}
		base.$el.css('top', base.moveTo);
	});
	return this;
};
jQuery.fn.wpuiScroller.defaults = {
	limiter: false,
	adJust: 50
};




/*
 *	WP UI Pager
 */
jQuery.fn.wpuiPager = function( options ) {
	var base = this;
	base.$el = jQuery( base );
	o = jQuery.extend( {} , jQuery.fn.wpuiPager.defaults, options );

	base.$el.each(function() {
		base.pages = jQuery( this ).children( o.pageClass );
		

		// base.pages.addClass( 'wpui-page-hidden' );
		// base.pages.eq( 0 ).removeClass( 'wpui-page-hidden' );
				
		base.pages.hide();
		base.pages.eq( 0 ).show();
				
		// Pager
		base.pageNum = jQuery( this ).children( o.pageClass ).length;
		jQuery( this ).append( '<div class="wpui-pager">' + base.pageNum + ' Pages  </div>' );
		base.pager = jQuery( this ).find( '.wpui-pager' );

		base.wpuiHeight = 0;
		
		base.pages.each(function() {
			tisHgt = parseInt( jQuery( this ).css('height') );
			
			if ( tisHgt > base.wpuiHeight )
				base.wpuiHeight = tisHgt;
		});
		base.wpuiHeight <= 0 || base.pages.height( base.wpuiHeight );
		
		pageStr = '';
		for( i = 0; i < base.pageNum; i++ ) {
			pageNum = i+1;
			pageStr += '<a class="wpui-page-number" href="#" rel="' + i + '">' + pageNum + '</a>';
		}
		
		base.pager.append( pageStr );
		base.pager.append( '<a class="wpui-next-page" href="#">Next &raquo;</a>' );
		base.pager.each(function() {
			jQuery( this ).find( 'a' ).eq( 0 ).addClass( 'wpui-page-active' );
		});
		// END base.pager
		
		
		// // Slide animation
		// base.pagesTWidth = Math.round( ( base.pages.length + 1 ) * base.pages.width() );
		// base.pageHeight = base.pages.height();
		// base.pageWid = base.pages.eq( 0 ).width();
		// base.pages.width( base.pages.parent().innerWidth() - 40 );
		// 
		// base.pages
		// 	.parent()
		// 	.wrapInner( '<div class="wpui-pages-wrapper" />' );
		// 
		// base.pages.width( base.pageWid );	
		// 
		// base.pages
		// 	.parent()
		// 	.css({ position : 'absolute', width : base.pagesTWidth })
		// 	.parent()
		// 	.css({ position : 'relative', overflow : 'hidden' })
		// 	.height( base.pageHeight );



		base.browsePages = function( pageN, el ) {
			// console.log( this );
			bPage = jQuery( el ).parent().parent().find( o.pageClass );
			
			if ( o.effect == 'fade' ) {
				bPage.eq( pageN )
					.fadeIn( o.speed )
					.siblings( '.wpui-page' )
					.hide();
			} else if ( o.effect == 'slide' ) {
				bPage.eq( pageN )
					.slideDown( o.speed )
					.siblings( '.wpui-page' )
					.hide();				
			} else {
				bPage.eq( pageN ).show().siblings( '.wpui-page' ).hide();
			}
			
			
			
			jQuery( el ).siblings().removeClass( 'wpui-page-active' );
			// console.log( this );
			
			jQuery( el ).addClass( 'wpui-page-active' );
			
		};


		base.pager.children( 'a' ).click( function() {
			// console.log( base.pages );
			pagess = jQuery( this ).parent().parent().find( o.pageClass );
			// console.log( pagess );
			pagessCount = jQuery( this ).siblings().length;
			if ( jQuery( this ).hasClass( 'wpui-next-page' ) ) {
				currEl = jQuery( this ).siblings( '.wpui-page-active' );
				if ( currEl.attr("rel") == ( pagessCount - 1 ) )
					nextEl = jQuery( this ).siblings().eq( 0 );
				else 
					nextEl = currEl.next();
				relEL = nextEl.attr( 'rel' );
				activeEl = nextEl.get( 0 );
			} else {
				relEL = jQuery( this ).attr( 'rel' );
				activeEl = this;
			}
			
			// pagess.addClass( 'wpui-page-hidden' );		
			// pagess.eq( relEL ).removeClass( 'wpui-page-hidden' );			
			// // base.$el.find( o.pageClass )
			// 
			// jQuery( this ).siblings().removeClass( 'wpui-page-active' );
			// jQuery( this ).addClass( 'wpui-page-active' );

			base.browsePages( relEL, activeEl );		
			
			return false;
		});

		
		
	});
		
	return this;
	
};

jQuery.fn.wpuiPager.defaults = {
	position : 'bottom',
	pageClass : '.wpui-page',
	speed : 600,
	effect : 'fade'
};




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



/*
 *	Component - Spoilers/Collapsibles
 */
jQuery.fn.wpspoiler = function( options ) {
	var o, defaults, holder, hideText, showText, currText, hideSpan;

	o = jQuery.extend({}, jQuery.fn.wpspoiler.defaults, options );

	this.each(function() {
		var base = this,
		$this = jQuery( base );
		
		if ( typeof convertEntities == 'function' ) {
			hideText = convertEntities( o.hideText );
			showText = convertEntities( o.showText );
		} else {
			hideText = o.hideText; showText = o.showText;
		}

		$this.addClass( 'ui-widget ui-collapsible' );
		
		$header = $this.children( o.headerClass );

		$header
		.addClass( 'ui-collapsible-header' )
		.each(function() {
			jQuery( this ).prepend( '<span class="ui-icon"></span>' );
			jQuery( this )
				.addClass( 'ui-state-default ui-corner-all ui-helper-reset' )
				.find( 'span.ui-icon', this )
				.addClass( o.openIconClass );
		
			jQuery( this )
				.append( '<span class="' +  o.spanClass.replace(/\./, '') + '" style="float:right"></span>' )
				.find( o.spanClass )
				.html( showText );
				
			base.aniOpts = {};
			if ( o.fade ) base.aniOpts[ 'opacity' ] = 'toggle';
			if ( o.slide ) base.aniOpts[ 'height' ] = 'toggle';
			
			if ( o.slide || o.fade ) {
				if ( jQuery(this + '[class*=speed-]').length ) {
					animSpeed = jQuery(this)
									.attr('class')
									.match(/speed-(.*)\s|\"/, "$1");
					if ( animSpeed ) {
						speed = animSpeed[1];
					} else {
						speed = o.speed;
					}
				}				
				
			}
	
		
				
		}).next( 'div' )
		.addClass( 'ui-collapsible-content ui-widget-content ui-corner-bottom' )
		.wrapInner( '<div class="ui-collapsible-wrapper" />' )
		.find( '.close-spoiler')
		.addClass('ui-state-default ui-widget ui-corner-all ui-button-text-only' )
		.end()
		.hide(); // end headerClass each.	

		$header.hover( function() {
			jQuery( this ).addClass( 'ui-state-hover' ).css({ cursor : 'pointer' });
		}, function() {
			jQuery( this ).removeClass( 'ui-state-hover' );
		});
		
		
		$header.click(function() {
			base.headerToggle( this );
		});

		$this.find( 'a.close-spoiler' ).click(function( e ) {
			e.stopPropagation();
			e.preventDefault();
			heads = jQuery( this ).parent().parent().siblings( o.headerClass ).get(0);

			base.headerToggle( heads );
			return false;						
		});
		
		base.headerToggle = function( hel ) {
			spanText = jQuery( hel ).find( o.spanClass ).html();

			// Toggle the header and icon classes.
			jQuery( hel )
				.toggleClass( 'ui-state-active ui-corner-all ui-corner-top' )
				.children( 'span.ui-icon' )
				.toggleClass( o.closeIconClass )
				.siblings( o.spanClass )
				.html( ( spanText == hideText) ? showText : hideText )
				.parent()
				.next( 'div.ui-collapsible-content' )
				.animate( base.aniOpts , o.speed, o.easing )
				.addClass( 'ui-widget-content' );

			
		}; // END headerToggle function.
	
		if ( $this.find( o.headerClass).hasClass( 'open-true' ) ) {
			h3 = $this.children( o.headerClass ).get(0);
			base.headerToggle( h3 );		
		} // end check for open-true
		
		
	}); // this.each function.
	
	return this;
	
};

jQuery.fn.wpspoiler.defaults = {
	// hideText : 'Click to hide',
	// showText : 'Click to show',
	hideText : (typeof wpUIOpts != "undefined") ? wpUIOpts.spoilerHideText : '',
	showText : (typeof wpUIOpts != "undefined") ? wpUIOpts.spoilerShowText : '',
	easing : 'easeInOutQuart',
	fade	 : true,
	slide	 : true,
	speed	 : 600,
	spanClass: '.toggle_text',
	headerClass : 'h3.wp-spoiler-title',
	openIconClass : 'ui-icon-triangle-1-e',
	closeIconClass : 'ui-icon-triangle-1-s'
};


/*
 *	Component - Dialogs
 */
jQuery.fn.wpDialog = function( options ) {
	
	var o = jQuery.extend( {} , jQuery.fn.wpDialog.defaults, options );
		
	// var wpfill = function( el, index ) {
	// 	kel = el.replace( /wpui-(.*)-arg/mg, '$1' )
	// 			.replace(/(.*)-(.*)/, '$1 : $2');
	// 	return kel;
	// };
	
	return this.each(function() {
		var base = this;
		$base = jQuery( base );
		// dtitle = $base.find('h4.wp-dialog-title').text();

		dialogArgs = $base.find('h4.wp-dialog-title')
						.toggleClass('wp-dialog-title')
						.attr('class').split(' ');
		
		$base.find('h4:first').remove();
		
		kel = {};	
		
		// console.log( dialogArgs ); 
		for( i = 0; i < dialogArgs.length; i++ ) {
			dialogArgs[i] = dialogArgs[i].replace( /wpui-(.*)-arg/mg, '$1' );
			key = dialogArgs[i].replace(/([\w\d\S]*):([\w\d\S]*)/mg, '$1');
			value = dialogArgs[i].replace(/(.*):(.*)/mg, '$2').replace( /%/mg , ' ');
			if ( value == "true" ) value = true;
			if ( value == "false" ) value = false;
			kel[key] = value;			
		}

		
		dialogCloseFn = function() {
			$(this).dialog("close");
		};
		
		
		if ( kel.position == 'bottomleft' ) {
			kel.position = [ 'left' , 'bottom' ];
		} else if ( kel.position == 'bottomright' ) {
			kel.position = [ 'right', 'bottom' ];
		} else if ( kel.position == 'topleft' ) {
			kel.position = [ 'left', 'top' ];
		} else if ( kel.position == 'topright' ) {
			kel.position = [ 'right', 'top' ];
		}
		
		kel.width = parseInt( kel.width ) + "px";
		
		if ( kel.button ) {
			buttonLabel = kel.button;
			delete kel.button;
			kel.buttons = {};
			kel.buttons[ buttonLabel ] = dialogCloseFn
		}
		
		if ( kel.dialogClass && kel.dialogClass != '' ) {
			kel[ 'dialogClass' ] = kel.dialogClass.replace(/_/gm, ' ');
		}

		// console.log( kel ); 
		
		$base.dialog( kel );

		jQuery( '[class*=dialog-opener]' ).button({
			icons : {
				primary : 'ui-icon-newwin'
			}
		});
		
		jQuery( '[class*=dialog-opener]' ).click(function() {
			openerClass = jQuery( this ).attr( 'class' ).match(/dialog\-opener\-(\d{1,2})/);
			dNum = openerClass[ 1 ];
			jQuery( '.wp-dialog-' + dNum ).dialog( 'open' );
			return false;
		});
		
			
	}); // return this.each.

};

jQuery.fn.wpDialog.defaults = {
	title	: 'Information'
};




/**
 *	The includes below. A lot of thanks to the respective authors.
 */

/**
 * jQuery Cookie plugin
 *
 * Copyright (c) 2010 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

// TODO JsDoc

/**
 * Create a cookie with the given key and value and other optional parameters.
 *
 * @example $.cookie('the_cookie', 'the_value');
 * @desc Set the value of a cookie.
 * @example $.cookie('the_cookie', 'the_value', { expires: 7, path: '/', domain: 'jquery.com', secure: true });
 * @desc Create a cookie with all available options.
 * @example $.cookie('the_cookie', 'the_value');
 * @desc Create a session cookie.
 * @example $.cookie('the_cookie', null);
 * @desc Delete a cookie by passing null as value. Keep in mind that you have to use the same path and domain
 *       used when the cookie was set.
 *
 * @param String key The key of the cookie.
 * @param String value The value of the cookie.
 * @param Object options An object literal containing key/value pairs to provide optional cookie attributes.
 * @option Number|Date expires Either an integer specifying the expiration date from now on in days or a Date object.
 *                             If a negative value is specified (e.g. a date in the past), the cookie will be deleted.
 *                             If set to null or omitted, the cookie will be a session cookie and will not be retained
 *                             when the the browser exits.
 * @option String path The value of the path atribute of the cookie (default: path of page that created the cookie).
 * @option String domain The value of the domain attribute of the cookie (default: domain of page that created the cookie).
 * @option Boolean secure If true, the secure attribute of the cookie will be set and the cookie transmission will
 *                        require a secure protocol (like HTTPS).
 * @type undefined
 *
 * @name $.cookie
 * @cat Plugins/Cookie
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */

/**
 * Get the value of a cookie with the given key.
 *
 * @example $.cookie('the_cookie');
 * @desc Get the value of a cookie.
 *
 * @param String key The key of the cookie.
 * @return The value of the cookie.
 * @type String
 *
 * @name $.cookie
 * @cat Plugins/Cookie
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */
jQuery.cookie = function (key, value, options) {

    // key and value given, set cookie...
    if (arguments.length > 1 && (value === null || typeof value !== "object")) {
        options = jQuery.extend({}, options);

        if (value === null) {
            options.expires = -1;
        }

        if (typeof options.expires === 'number') {
            var days = options.expires, t = options.expires = new Date();
            t.setDate(t.getDate() + days);
        }

        return (document.cookie = [
            encodeURIComponent(key), '=',
            options.raw ? String(value) : encodeURIComponent(String(value)),
            options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
            options.path ? '; path=' + options.path : '',
            options.domain ? '; domain=' + options.domain : '',
            options.secure ? '; secure' : ''
        ].join(''));
    }

    // key and possibly options given, get cookie...
    options = value || {};
    var result, decode = options.raw ? function (s) { return s; } : decodeURIComponent;
    return (result = new RegExp('(?:^|; )' + encodeURIComponent(key) + '=([^;]*)').exec(document.cookie)) ? decode(result[1]) : null;
};

/*
 * jQuery hashchange event - v1.3 - 7/21/2010
 * http://benalman.com/projects/jquery-hashchange-plugin/
 * 
 * Copyright (c) 2010 "Cowboy" Ben Alman
 * Dual licensed under the MIT and GPL licenses.
 * http://benalman.com/about/license/
 */
(function($,e,b){var c="hashchange",h=document,f,g=$.event.special,i=h.documentMode,d="on"+c in e&&(i===b||i>7);function a(j){j=j||location.href;return"#"+j.replace(/^[^#]*#?(.*)$/,"$1")}$.fn[c]=function(j){return j?this.bind(c,j):this.trigger(c)};$.fn[c].delay=50;g[c]=$.extend(g[c],{setup:function(){if(d){return false}$(f.start)},teardown:function(){if(d){return false}$(f.stop)}});f=(function(){var j={},p,m=a(),k=function(q){return q},l=k,o=k;j.start=function(){p||n()};j.stop=function(){p&&clearTimeout(p);p=b};function n(){var r=a(),q=o(m);if(r!==m){l(m=r,q);$(e).trigger(c)}else{if(q!==m){location.href=location.href.replace(/#.*/,"")+q}}p=setTimeout(n,$.fn[c].delay)}$.browser.msie&&!d&&(function(){var q,r;j.start=function(){if(!q){r=$.fn[c].src;r=r&&r+a();q=$('<iframe tabindex="-1" title="empty"/>').hide().one("load",function(){r||l(a());n()}).attr("src",r||"javascript:0").insertAfter("body")[0].contentWindow;h.onpropertychange=function(){try{if(event.propertyName==="title"){q.document.title=h.title}}catch(s){}}}};j.stop=k;o=function(){return a(q.location.href)};l=function(v,s){var u=q.document,t=$.fn[c].domain;if(v!==s){u.title=h.title;u.open();t&&u.write('<script>document.domain="'+t+'"<\/script>');u.close();q.location.hash=v}}})();return j})()})(jQuery,this);


/*
 *	JSON Library
 *	https://github.com/douglascrockford/JSON-js/blob/master/json2.js
 */
var JSON;if(!JSON){JSON={};}
(function(){"use strict";function f(n){return n<10?'0'+n:n;}
if(typeof Date.prototype.toJSON!=='function'){Date.prototype.toJSON=function(key){return isFinite(this.valueOf())?this.getUTCFullYear()+'-'+
f(this.getUTCMonth()+1)+'-'+
f(this.getUTCDate())+'T'+
f(this.getUTCHours())+':'+
f(this.getUTCMinutes())+':'+
f(this.getUTCSeconds())+'Z':null;};String.prototype.toJSON=Number.prototype.toJSON=Boolean.prototype.toJSON=function(key){return this.valueOf();};}
var cx=/[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,escapable=/[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,gap,indent,meta={'\b':'\\b','\t':'\\t','\n':'\\n','\f':'\\f','\r':'\\r','"':'\\"','\\':'\\\\'},rep;function quote(string){escapable.lastIndex=0;return escapable.test(string)?'"'+string.replace(escapable,function(a){var c=meta[a];return typeof c==='string'?c:'\\u'+('0000'+a.charCodeAt(0).toString(16)).slice(-4);})+'"':'"'+string+'"';}
function str(key,holder){var i,k,v,length,mind=gap,partial,value=holder[key];if(value&&typeof value==='object'&&typeof value.toJSON==='function'){value=value.toJSON(key);}
if(typeof rep==='function'){value=rep.call(holder,key,value);}
switch(typeof value){case'string':return quote(value);case'number':return isFinite(value)?String(value):'null';case'boolean':case'null':return String(value);case'object':if(!value){return'null';}
gap+=indent;partial=[];if(Object.prototype.toString.apply(value)==='[object Array]'){length=value.length;for(i=0;i<length;i+=1){partial[i]=str(i,value)||'null';}
v=partial.length===0?'[]':gap?'[\n'+gap+partial.join(',\n'+gap)+'\n'+mind+']':'['+partial.join(',')+']';gap=mind;return v;}
if(rep&&typeof rep==='object'){length=rep.length;for(i=0;i<length;i+=1){if(typeof rep[i]==='string'){k=rep[i];v=str(k,value);if(v){partial.push(quote(k)+(gap?': ':':')+v);}}}}else{for(k in value){if(Object.prototype.hasOwnProperty.call(value,k)){v=str(k,value);if(v){partial.push(quote(k)+(gap?': ':':')+v);}}}}
v=partial.length===0?'{}':gap?'{\n'+gap+partial.join(',\n'+gap)+'\n'+mind+'}':'{'+partial.join(',')+'}';gap=mind;return v;}}
if(typeof JSON.stringify!=='function'){JSON.stringify=function(value,replacer,space){var i;gap='';indent='';if(typeof space==='number'){for(i=0;i<space;i+=1){indent+=' ';}}else if(typeof space==='string'){indent=space;}
rep=replacer;if(replacer&&typeof replacer!=='function'&&(typeof replacer!=='object'||typeof replacer.length!=='number')){throw new Error('JSON.stringify');}
return str('',{'':value});};}
if(typeof JSON.parse!=='function'){JSON.parse=function(text,reviver){var j;function walk(holder,key){var k,v,value=holder[key];if(value&&typeof value==='object'){for(k in value){if(Object.prototype.hasOwnProperty.call(value,k)){v=walk(value,k);if(v!==undefined){value[k]=v;}else{delete value[k];}}}}
return reviver.call(holder,key,value);}
text=String(text);cx.lastIndex=0;if(cx.test(text)){text=text.replace(cx,function(a){return'\\u'+
('0000'+a.charCodeAt(0).toString(16)).slice(-4);});}
if(/^[\],:{}\s]*$/.test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g,'@').replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,']').replace(/(?:^|:|,)(?:\s*\[)+/g,''))){j=eval('('+text+')');return typeof reviver==='function'?walk({'':j},''):j;}
throw new SyntaxError('JSON.parse');};}}());
/*
 * jQuery hashchange event - v1.3 - 7/21/2010
 * http://benalman.com/projects/jquery-hashchange-plugin/
 * 
 * Copyright (c) 2010 "Cowboy" Ben Alman
 * Dual licensed under the MIT and GPL licenses.
 * http://benalman.com/about/license/
 */
(function($,e,b){var c="hashchange",h=document,f,g=$.event.special,i=h.documentMode,d="on"+c in e&&(i===b||i>7);function a(j){j=j||location.href;return"#"+j.replace(/^[^#]*#?(.*)$/,"$1")}$.fn[c]=function(j){return j?this.bind(c,j):this.trigger(c)};$.fn[c].delay=50;g[c]=$.extend(g[c],{setup:function(){if(d){return false}$(f.start)},teardown:function(){if(d){return false}$(f.stop)}});f=(function(){var j={},p,m=a(),k=function(q){return q},l=k,o=k;j.start=function(){p||n()};j.stop=function(){p&&clearTimeout(p);p=b};function n(){var r=a(),q=o(m);if(r!==m){l(m=r,q);$(e).trigger(c)}else{if(q!==m){location.href=location.href.replace(/#.*/,"")+q}}p=setTimeout(n,$.fn[c].delay)}$.browser.msie&&!d&&(function(){var q,r;j.start=function(){if(!q){r=$.fn[c].src;r=r&&r+a();q=$('<iframe tabindex="-1" title="empty"/>').hide().one("load",function(){r||l(a());n()}).attr("src",r||"javascript:0").insertAfter("body")[0].contentWindow;h.onpropertychange=function(){try{if(event.propertyName==="title"){q.document.title=h.title}}catch(s){}}}};j.stop=k;o=function(){return a(q.location.href)};l=function(v,s){var u=q.document,t=$.fn[c].domain;if(v!==s){u.title=h.title;u.open();t&&u.write('<script>document.domain="'+t+'"<\/script>');u.close();q.location.hash=v}}})();return j})()})(jQuery,this);




/* Copyright (c) 2009 Brandon Aaron (http://brandonaaron.net)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 * Thanks to: http://adomas.org/javascript-mouse-wheel/ for some pointers.
 * Thanks to: Mathias Bank(http://www.mathias-bank.de) for a scope bug fix.
 *
 * Version: 3.0.2
 * 
 * Requires: 1.2.2+
 */
(function(c){var a=["DOMMouseScroll","mousewheel"];c.event.special.mousewheel={setup:function(){if(this.addEventListener){for(var d=a.length;d;){this.addEventListener(a[--d],b,false)}}else{this.onmousewheel=b}},teardown:function(){if(this.removeEventListener){for(var d=a.length;d;){this.removeEventListener(a[--d],b,false)}}else{this.onmousewheel=null}}};c.fn.extend({mousewheel:function(d){return d?this.bind("mousewheel",d):this.trigger("mousewheel")},unmousewheel:function(d){return this.unbind("mousewheel",d)}});function b(f){var d=[].slice.call(arguments,1),g=0,e=true;f=c.event.fix(f||window.event);f.type="mousewheel";if(f.wheelDelta){g=f.wheelDelta/120}if(f.detail){g=-f.detail/3}d.unshift(f,g);return c.event.handle.apply(this,d)}})(jQuery);


/**
 *	Init the scripts.
 */
jQuery(document).ready(function( $ ) {
	
	if ( typeof wpUIOpts == 'object' ) {

	if ( wpUIOpts.enablePagination == 'on' )
		jQuery( 'div.wpui-pages-holder' ).wpuiPager();
	
	if ( wpUIOpts.enableTabs == 'on')
		jQuery('div.wp-tabs').wptabs();
	
	if ( wpUIOpts.enableSpoilers == 'on' )
		jQuery('.wp-spoiler').wpspoiler();
	
	if ( wpUIOpts.enableAccordion == 'on')
		jQuery('.wp-accordion').wpaccord();
	
	// if ( wpUIOpts.enableDialogs == 'on' )
		// jQuery('.wp-dialog').wpDialog();
	} 
	
	
	jQuery( "ul.wpui-related-posts" ).each(function() {
		allWidth = jQuery( this ).children( 'li' ).outerWidth() - jQuery( this ).children('li').width();

		if ( jQuery( this ).hasClass( 'wpui-per_3' ) ) {
			liWidth = ( jQuery( this ).innerWidth() / 3 ) - allWidth;
		} else if ( jQuery( this ).hasClass( 'wpui-per_4' ) ) {
			liWidth = ( jQuery( this ).innerWidth() / 4 ) - allWidth;
			
		} else if ( jQuery( this ).hasClass( 'wpui-per_2' ) ) {
			liWidth = ( jQuery( this ).innerWidth() / 2 ) - allWidth;
		}

		if ( typeof( liWidth ) != 'undefined' )
		jQuery( this ).children( 'li' ).width( liWidth - 1 );
		
		if ( jQuery( this ).hasClass( 'wpui-per_2' ) ) {
			jQuery( this ).children( 'li' ).find( '.wpui-rel-post-meta' ).width( liWidth  - 120 );
		}
		
		// var soHgt = 0;
		// jQuery( this ).children( 'li' ).each(function() {
		// 	soHgt = Math.max( soHgt, jQuery( this ).height());
		// }).height( soHgt );

	});	
		
});

jQuery.fn.tabsThemeSwitcher = function(classArr) {
	
	return this.each(function() {
		var $this = jQuery(this);

		$this.prepend('<div class="selector_tab_style">Switch Theme : <select id="tabs_theme_select" /></div>');
	
	for( i=0; i< classArr.length; i++) {
		jQuery('#tabs_theme_select', this).append('<option value="' + classArr[i] + '">' + classArr[i] + '</option');
		
	} // END for loop.
	

	if ( jQuery.cookie && jQuery.cookie('tab_demo_style') != null ) {
		currentVal = jQuery.cookie('tab_demo_style');
		$this.find('select#tabs_theme_select option').each(function() {
			if ( currentVal == jQuery(this).attr("value") ) {
			 	jQuery(this).attr( 'selected', 'selected' );
			}
		});
	} else {
		currentVal = classArr[0];
	} // END cookie value check.

	
	$this.children('.wp-tabs').attr('class', 'wp-tabs wpui-styles').addClass(currentVal, 500);
	$this.children('.wp-accordion').attr('class', 'wp-accordion wpui-styles').addClass(currentVal, 500);
	$this.children('.wp-spoiler').attr('class', 'wp-spoiler wpui-styles').addClass(currentVal, 500);

	
	jQuery('#tabs_theme_select').change(function(e) {
		newVal = jQuery(this).val();
		
		$this.children('.wp-tabs, .wp-accordion, .wp-spoiler').switchClass(currentVal, newVal, 1500);
		
		currentVal = newVal;
		
		if ( jQuery.cookie ) jQuery.cookie('tab_demo_style', newVal, { expires : 2 });
	}); // END on select box change.
	

	}); // END each function.	
	
};
// 
// var tb_remove = function() {
// 	// console.log( "Thickbox close fix" );
//  	jQuery("#TB_imageOff").unbind("click");
// 	jQuery("#TB_closeWindowButton").unbind("click");
// 	jQuery("#TB_window")
// 		.fadeOut("fast",function(){
// 				jQuery('#TB_window,#TB_overlay,#TB_HideSelect')
// 					.unload("#TB_ajaxContent")
// 					.unbind()
// 					.remove();
// 		});
// 	jQuery("#TB_load").remove();
// 	if (typeof document.body.style.maxHeight == "undefined") {//if IE 6
// 		jQuery("body","html").css({height: "auto", width: "auto"});
// 		jQuery("html").css("overflow","");
// 	}
// 	jQuery(document).unbind('.thickbox');
// 	return false;
// } // END function tb_remove()
