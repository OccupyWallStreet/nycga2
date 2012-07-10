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