define(
		[
		 "jquery",
		 "domReady",
		 "scripts/calendar/load_views",
		 "scripts/calendar/pop_up",
		 "scripts/calendar/print",
		 "scripts/calendar/agenda_view",
		 "scripts/calendar/posterboard_view",
		 "libs/modernizr",
		 "ai1ec_calendar"
		 ]
		, function( $, domReady, load_views, pop_up, print, agenda_view, posterboard_view, Modernizr, ai1ec_calendar ) {
	var css_selector_replacement = function() {
		// =====================================
		// = Calendar CSS selector replacement =
		// =====================================

		if( ai1ec_calendar.selector !== undefined && ai1ec_calendar.selector !== '' &&
		    $( ai1ec_calendar.selector ).length === 1 ) {
			// Try to find an <h#> element containing the title
			var $title = $( ":header:contains(" + ai1ec_calendar.title + "):first" );
			// If none found, create one
			if( ! $title.length ) {
				$title = $( '<h1 class="page-title"></h1>' );
				$title.text( ai1ec_calendar.title ); // Do it this way to automatically generate HTML entities
			}
			var $calendar = $( '#ai1ec-container' )
				.detach()
				.before( $title );

			$( ai1ec_calendar.selector )
				.empty()
				.append( $calendar )
				.hide()
				.css( 'visibility', 'visible' )
				.fadeIn( 'fast' );
		}
	};
	var add_classes_to_body = function() {
		// Check whether appropriate classes have been added to <body> (some themes
		// don't respect the WP body_class() function). If not, add them, or our app
		// won't function properly.
		var classes = $('body').attr( 'class' );
		if( classes == undefined ) classes = '';
		if( classes.match( /\s?\bai1ec-[\w-]+\b/ ) == null ) {
			// Add action body class(es)
			classes += ' ' + ai1ec_calendar.body_class;
			$('body').attr( 'class', classes );
		}
	};
	var init = function() {
		// Do the replacement of the calendar and create title if not present
		css_selector_replacement();
		// Add the classes to the body if needed
		add_classes_to_body();
	};
	var attach_event_handlers = function() {
		// Register navigation click handlers
		$( window ).on( 'click', 'a.ai1ec-load-view', load_views.handle_click_on_link_to_load_view );
		// Hide opened pop-ups when the window loses focus
		$( window ).blur( pop_up.hide_popup_on_windows_blur_event );
		// Register popup click (for touch devices) or hover (for non-touch devices)
		// handlers for month/week views
		$('.ai1ec-oneday-view .ai1ec-event, .ai1ec-week-view .ai1ec-event')
			.live( Modernizr.touch ? 'click' : 'mouseenter', pop_up.show_popup );
		// Only hide popups on mouseleave for mouse-based devices (doesn't work
		// properly in touch-based devices for some reason).
		if( ! Modernizr.touch ) {
			$('.ai1ec-event-popup, .ai1ec-oneday-view .ai1ec-event-popup, .ai1ec-week-view .ai1ec-event-popup')
				.live( 'mouseleave', pop_up.hide_popup )
				// Track whether popup contains mouse cursor
				.live( 'mousemove', pop_up.set_data_on_active_popup );
		}
		// ======================================
		// = Week / Day view hover-raise effect =
		// ======================================
		$( '.ai1ec-oneday-view .ai1ec-oneday a.ai1ec-event-container, .ai1ec-week-view .ai1ec-week a.ai1ec-event-container' )
			.live( 'mouseenter',
				function() {
					$(this).delay( 500 ).queue( function() {
						$(this).css( 'z-index', 5 );
						} );
				} )
			.live( 'mouseleave',
				function() {
					$(this).clearQueue().css( 'z-index', 'auto' );
				} );
		// Register click handlers for Agenda View event title
		$( window ).on( 'click', '.ai1ec-agenda-view .ai1ec-event-click', agenda_view.toggle_event );

		// Register click handlers for expand/collapse all buttons
		$( window ).on( 'click', '.ai1ec-action-agenda #ai1ec-expand-all', agenda_view.expand_all );
		$( window ).on( 'click', '.ai1ec-action-agenda #ai1ec-collapse-all', agenda_view.collapse_all );

		// ===========================================================
		// = Posterboard view - fix webfont loading bug with masonry =
		// ===========================================================
		$( window ).on( 'load', posterboard_view.reload_masonry );

		// *** All views ***

		// ========================
		// = Category/tag filters =
		// ========================
		$( '.ai1ec-category-filter-selector li, .ai1ec-tag-filter-selector li' ).click( load_views.handle_click_on_tag_category );

		$('.ai1ec-filter-selector li').click( load_views.update_filter_selectors );
		// Handle clearing filters
		$('.ai1ec-clear-filters').click( load_views.clear_filters );
		// Handle the click on the print.
		$( '#ai1ec-print-button' ).click( print.handle_click_on_print_button );
	};
	var start = function() {
		domReady( function() {
			init();
			attach_event_handlers();
			load_views.pre_select_tag_categories_if_set( '.ai1ec-tag-filter-selector li', 'ai1ec-tags', '#ai1ec-selected-tags' );
			load_views.pre_select_tag_categories_if_set( '.ai1ec-category-filter-selector li', 'ai1ec-categories', '#ai1ec-selected-categories' );
			// Initialize the filters
			load_views.initialize_filters();
			// Load the first view
			load_views.initialize_view();
		} );
	};
	return {
		start : start
	};
} );
