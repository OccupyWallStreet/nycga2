// document.write blank page fix. Thanks to altCognito on stackoverflow. ( http://stackoverflow.com/q/761190 ) for the code from which this is adapted.
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