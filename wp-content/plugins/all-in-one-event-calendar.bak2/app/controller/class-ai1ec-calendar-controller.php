<?php
//
//  class-ai1ec-calendar-controller.php
//  all-in-one-event-calendar
//
//  Created by The Seed Studio on 2011-07-13.
//

/**
 * Ai1ec_Calendar_Controller class
 *
 * @package Controllers
 * @author time.ly
 **/
class Ai1ec_Calendar_Controller {
	/**
	 * _instance class variable
	 *
	 * Class instance
	 *
	 * @var null | object
	 **/
	static $_instance = NULL;

	/**
	 * request class variable
	 *
	 * Stores a custom $_REQUEST array for all calendar requests
	 *
	 * @var array
	 **/
	private $request = array();

	/**
	 * __construct function
	 *
	 * Default constructor - calendar initialization
	 **/
	private function __construct() {
		// ===========
		// = ACTIONS =
		// ===========
		// Handle AJAX requests
		// Strange! Now regular WordPress requests will respond to the below AJAX
		// hooks! Thus we need to check to make sure we are being called by the
		// AJAX script before returning AJAX responses.
		if( basename( $_SERVER['SCRIPT_NAME'] ) == 'admin-ajax.php' )
		{
			add_action( 'wp_ajax_ai1ec_posterboard', array( &$this, 'ajax_posterboard' ) );
			add_action( 'wp_ajax_ai1ec_month', array( &$this, 'ajax_month' ) );
			add_action( 'wp_ajax_ai1ec_oneday', array( &$this, 'ajax_oneday' ) );
			add_action( 'wp_ajax_ai1ec_week', array( &$this, 'ajax_week' ) );
			add_action( 'wp_ajax_ai1ec_agenda', array( &$this, 'ajax_agenda' ) );
			add_action( 'wp_ajax_ai1ec_term_filter', array( &$this, 'ajax_term_filter' ) );

			add_action( 'wp_ajax_nopriv_ai1ec_posterboard', array( &$this, 'ajax_posterboard' ) );
			add_action( 'wp_ajax_nopriv_ai1ec_month', array( &$this, 'ajax_month' ) );
			add_action( 'wp_ajax_nopriv_ai1ec_oneday', array( &$this, 'ajax_oneday' ) );
			add_action( 'wp_ajax_nopriv_ai1ec_week', array( &$this, 'ajax_week' ) );
			add_action( 'wp_ajax_nopriv_ai1ec_agenda', array( &$this, 'ajax_agenda' ) );
			add_action( 'wp_ajax_nopriv_ai1ec_term_filter', array( &$this, 'ajax_term_filter' ) );
		}
	}
	/**
	 * process_request function
	 *
	 * Initialize/validate custom request array, based on contents of $_REQUEST,
	 * to keep track of this component's request variables.
	 *
	 * @return void
	 **/
	function process_request()
	{
		global $ai1ec_settings;

		// Find out which view of the calendar page was requested, then validate
		// request parameters accordingly and save them to our custom request
		// object
		$this->request['action'] = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';
		if( ! in_array( $this->request['action'],
			      array( 'ai1ec_posterboard', 'ai1ec_month', 'ai1ec_oneday', 'ai1ec_week', 'ai1ec_agenda', 'ai1ec_term_filter' ) ) )
			$this->request['action'] = 'ai1ec_' . $ai1ec_settings->default_calendar_view;

		switch( $this->request['action'] )
		{

			case 'ai1ec_posterboard':
				$this->request['ai1ec_page_offset'] =
					isset( $_REQUEST['ai1ec_page_offset'] ) ? intval( $_REQUEST['ai1ec_page_offset'] ) : 0;
				// Parse active event parameter as an integer ID
				$this->request['ai1ec_active_event'] = isset( $_REQUEST['ai1ec_active_event'] ) ? intval( $_REQUEST['ai1ec_active_event'] ) : null;
				// Category/tag filter parameters
				$this->request['ai1ec_cat_ids'] = isset( $_REQUEST['ai1ec_cat_ids'] ) ? $_REQUEST['ai1ec_cat_ids'] : null;
				$this->request['ai1ec_tag_ids'] = isset( $_REQUEST['ai1ec_tag_ids'] ) ? $_REQUEST['ai1ec_tag_ids'] : null;
				$this->request['ai1ec_post_ids'] = isset( $_REQUEST['ai1ec_post_ids'] ) ? $_REQUEST['ai1ec_post_ids'] : null;
				break;

			case 'ai1ec_month':
				$this->request['ai1ec_month_offset'] =
					isset( $_REQUEST['ai1ec_month_offset'] ) ? intval( $_REQUEST['ai1ec_month_offset'] ) : 0;
				// Parse active event parameter as an integer ID
				$this->request['ai1ec_active_event'] = isset( $_REQUEST['ai1ec_active_event'] ) ? intval( $_REQUEST['ai1ec_active_event'] ) : null;
				// Category/tag filter parameters
				$this->request['ai1ec_cat_ids'] = isset( $_REQUEST['ai1ec_cat_ids'] ) ? $_REQUEST['ai1ec_cat_ids'] : null;
				$this->request['ai1ec_tag_ids'] = isset( $_REQUEST['ai1ec_tag_ids'] ) ? $_REQUEST['ai1ec_tag_ids'] : null;
				$this->request['ai1ec_post_ids'] = isset( $_REQUEST['ai1ec_post_ids'] ) ? $_REQUEST['ai1ec_post_ids'] : null;
				break;

			case 'ai1ec_oneday':
				$this->request['ai1ec_oneday_offset'] =
					isset( $_REQUEST['ai1ec_oneday_offset'] ) ? intval( $_REQUEST['ai1ec_oneday_offset'] ) : 0;
				// Parse active event parameter as an integer ID
				$this->request['ai1ec_active_event'] = isset( $_REQUEST['ai1ec_active_event'] ) ? intval( $_REQUEST['ai1ec_active_event'] ) : null;
				// Category/tag filter parameters
				$this->request['ai1ec_cat_ids'] = isset( $_REQUEST['ai1ec_cat_ids'] ) ? $_REQUEST['ai1ec_cat_ids'] : null;
				$this->request['ai1ec_tag_ids'] = isset( $_REQUEST['ai1ec_tag_ids'] ) ? $_REQUEST['ai1ec_tag_ids'] : null;
				$this->request['ai1ec_post_ids'] = isset( $_REQUEST['ai1ec_post_ids'] ) ? $_REQUEST['ai1ec_post_ids'] : null;
				break;

			case 'ai1ec_week':
				$this->request['ai1ec_week_offset'] =
					isset( $_REQUEST['ai1ec_week_offset'] ) ? intval( $_REQUEST['ai1ec_week_offset'] ) : 0;
				// Parse active event parameter as an integer ID
				$this->request['ai1ec_active_event'] = isset( $_REQUEST['ai1ec_active_event'] ) ? intval( $_REQUEST['ai1ec_active_event'] ) : null;
				// Category/tag filter parameters
				$this->request['ai1ec_cat_ids'] = isset( $_REQUEST['ai1ec_cat_ids'] ) ? $_REQUEST['ai1ec_cat_ids'] : null;
				$this->request['ai1ec_tag_ids'] = isset( $_REQUEST['ai1ec_tag_ids'] ) ? $_REQUEST['ai1ec_tag_ids'] : null;
				$this->request['ai1ec_post_ids'] = isset( $_REQUEST['ai1ec_post_ids'] ) ? $_REQUEST['ai1ec_post_ids'] : null;
				break;

			case 'ai1ec_agenda':
				$this->request['ai1ec_page_offset'] =
					isset( $_REQUEST['ai1ec_page_offset'] ) ? intval( $_REQUEST['ai1ec_page_offset'] ) : 0;
				// Parse active event parameter as an integer ID
				$this->request['ai1ec_active_event'] = isset( $_REQUEST['ai1ec_active_event'] ) ? intval( $_REQUEST['ai1ec_active_event'] ) : null;
				// Category/tag filter parameters
				$this->request['ai1ec_cat_ids'] = isset( $_REQUEST['ai1ec_cat_ids'] ) ? $_REQUEST['ai1ec_cat_ids'] : null;
				$this->request['ai1ec_tag_ids'] = isset( $_REQUEST['ai1ec_tag_ids'] ) ? $_REQUEST['ai1ec_tag_ids'] : null;
				$this->request['ai1ec_post_ids'] = isset( $_REQUEST['ai1ec_post_ids'] ) ? $_REQUEST['ai1ec_post_ids'] : null;
				break;

			case 'ai1ec_term_filter':
				$this->request['ai1ec_post_ids'] = isset( $_REQUEST['ai1ec_post_ids'] ) ? $_REQUEST['ai1ec_post_ids'] : null;
				$this->request['ai1ec_term_ids'] = isset( $_REQUEST['ai1ec_term_ids'] ) ? $_REQUEST['ai1ec_term_ids'] : null;
				break;
		}
	}

	/**
	 * get_instance function
	 *
	 * Return singleton instance
	 *
	 * @return object
	 **/
	static function get_instance() {
		if( self::$_instance === NULL ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * view function
	 *
	 * Display requested calendar page.
	 *
	 * @return void
	 **/
	function view()
 	{
		global $ai1ec_view_helper,
		       $ai1ec_settings,
		       $ai1ec_events_helper;

		$this->process_request();

		// Set body class
		add_filter( 'body_class', array( &$this, 'body_class' ) );
		// Queue any styles, scripts
		$this->load_css();


	  $post_ids = array_filter( explode( ',', $this->request['ai1ec_post_ids'] ), 'is_numeric' );
		// Define arguments for specific calendar sub-view (month, agenda, posterboard, etc.)
		$args = array(
			'active_event' => $this->request['ai1ec_active_event'],
		  'post_ids'     => $post_ids,
		);

		// Find out which view of the calendar page was requested
		switch( $this->request['action'] )
		{
			case 'ai1ec_posterboard':
				$args['page_offset'] = $this->request['ai1ec_page_offset'];
				$view = $this->get_posterboard_view( $args );
				break;

			case 'ai1ec_month':
				$args['month_offset'] = $this->request['ai1ec_month_offset'];
				$view = $this->get_month_view( $args );
				break;

			case 'ai1ec_oneday':
				$args['oneday_offset'] = $this->request['ai1ec_oneday_offset'];
				$view = $this->get_oneday_view( $args );
				break;

			case 'ai1ec_week':
				$args['week_offset'] = $this->request['ai1ec_week_offset'];
				$view = $this->get_week_view( $args );
				break;

			case 'ai1ec_agenda':
				$args['page_offset'] = $this->request['ai1ec_page_offset'];
				$view = $this->get_agenda_view( $args );
				break;
		}

		if( $ai1ec_settings->show_create_event_button && current_user_can( 'edit_ai1ec_events' ) )
			$create_event_url = admin_url( 'post-new.php?post_type=' . AI1EC_POST_TYPE );
		else
			$create_event_url = false;

		// Validate preselected category/tag/post IDs
		$cat_ids  = join( ',', array_filter( explode( ',', $this->request['ai1ec_cat_ids'] ), 'is_numeric' ) );
		$tag_ids  = join( ',', array_filter( explode( ',', $this->request['ai1ec_tag_ids'] ), 'is_numeric' ) );
		$post_ids = join( ',', $post_ids );

		$categories = get_terms( 'events_categories', array( 'orderby' => 'name' ) );
		foreach( $categories as &$cat ) {
			$cat->color = $ai1ec_events_helper->get_category_color_square( $cat->term_id );
		}

		// Create an empty array to fill with successfully returned values.
		$available_views = array();

		// Loop through array of views and check TRUE / FALSE on `view_instance_enabled`
		// if FALSE, continue, if TRUE add to the $available_views array.
		foreach( Ai1ec_Settings::$view_names as $key => $val ) {
			$view_enabled = 'view_' . $key . '_enabled';
			if( $ai1ec_settings->$view_enabled ) {
				$available_views[$key] = $val;
			}
		};

		$current_view = substr( $this->request['action'], 6 );

		// Define new arguments for overall calendar view
		$args = array(
			'view_names'              => Ai1ec_Settings::$view_names,
			'available_views'         => $available_views,
			'current_view'            => $current_view,
			'view'                    => $view,
			'create_event_url'        => $create_event_url,
			'categories'              => $categories,
			'tags'                    => get_terms( 'events_tags', array( 'orderby' => 'name' ) ),
			'selected_cat_ids'        => $cat_ids,
			'selected_tag_ids'        => $tag_ids,
			'selected_post_ids'       => $post_ids,
			'show_subscribe_buttons'  => ! $ai1ec_settings->turn_off_subscription_buttons,
			'ai1ec_view_helper'       => $ai1ec_view_helper,
		);

		// Feed month view into generic calendar view
		echo apply_filters( 'ai1ec_view', $ai1ec_view_helper->get_theme_view( 'calendar.php', $args ), $args );
	}

	/**
	 * get_posterboard_view function
	 *
	 * Return the embedded posterboard view of the calendar, optionally filtered by
	 * event categories and tags.
	 *
	 * @param array $args     associative array with any of these elements:
	 *   int page_offset   => specifies which page to display relative to today's page
	 *   int active_event  => specifies which event to make visible when
	 *                        page is loaded
	 *   array categories  => restrict events returned to the given set of
	 *                        event category slugs
	 *   array tags        => restrict events returned to the given set of
	 *                        event tag names
	 *
	 * @return string	        returns string of view output
	 **/
	function get_posterboard_view( $args )
 	{
		global $ai1ec_view_helper,
		       $ai1ec_events_helper,
		       $ai1ec_calendar_helper,
		       $ai1ec_settings;

		extract( $args );

		// Get localized time
		$timestamp = $ai1ec_events_helper->gmt_to_local( time() );

		// Get events, then classify into date array
		$event_results = $ai1ec_calendar_helper->get_events_relative_to(
			$timestamp,
			$ai1ec_settings->posterboard_events_per_page,
			$page_offset,
			array( 'post_ids' => $post_ids )
		);
		$dates = $ai1ec_calendar_helper->get_posterboard_date_array( $event_results['events'] );

		$pagination_links =
			$ai1ec_calendar_helper->get_posterboard_pagination_links(
			 	$page_offset, $event_results['prev'], $event_results['next'] );

		// Incorporate offset into date
		$args = array(
			'title'                  => __( 'Posterboard', AI1EC_PLUGIN_NAME ),
			'dates'                  => $dates,
			'show_location_in_title' => $ai1ec_settings->show_location_in_title,
			'page_offset'            => $page_offset,
			'pagination_links'       => $pagination_links,
			'active_event'           => $active_event,
			'expanded'               => $ai1ec_settings->posterboard_events_expanded,
			'post_ids'               => join( ',', $post_ids )
		);
		return apply_filters( 'ai1ec_get_posterboard_view', $ai1ec_view_helper->get_theme_view( 'posterboard.php', $args ), $args );
	}

	 /**
	 * get_month_view function
	 *
	 * Return the embedded month view of the calendar, optionally filtered by
	 * event categories and tags.
	 *
	 * @param array $args     associative array with any of these elements:
	 *   int month_offset  => specifies which month to display relative to the
	 *                        current month
	 *   int active_event  => specifies which event to make visible when
	 *                        page is loaded
	 *   array categories  => restrict events returned to the given set of
	 *                        event category slugs
	 *   array tags        => restrict events returned to the given set of
	 *                        event tag names
	 *   array post_ids    => restrict events returned to the given set of
	 *                        post IDs
	 *
	 * @return string	        returns string of view output
	 **/
	function get_month_view( $args )
 	{
		global $ai1ec_view_helper,
		       $ai1ec_events_helper,
		       $ai1ec_calendar_helper,
		       $ai1ec_settings;

    $defaults = array(
      'month_offset'  => 0,
      'active_event'  => null,
      'categories'    => array(),
      'tags'          => array(),
      'post_ids'      => array(),
    );
    $args = wp_parse_args( $args, $defaults );

		extract( $args );

		// Get components of localized time
		$bits = $ai1ec_events_helper->gmgetdate( $ai1ec_events_helper->gmt_to_local( time() ) );
		// Use first day of the month as reference timestamp, and apply month offset
		$timestamp = gmmktime( 0, 0, 0, $bits['mon'] + $month_offset, 1, $bits['year'] );

		$days_events = $ai1ec_calendar_helper->get_events_for_month( $timestamp,
			array( 'cat_ids' => $categories, 'cat_ids' => $tags, 'post_ids' => $post_ids ) );
		$cell_array = $ai1ec_calendar_helper->get_month_cell_array( $timestamp, $days_events );
		$pagination_links = $ai1ec_calendar_helper->get_month_pagination_links( $month_offset );

		$view_args = array(
			'title'                  => date_i18n( 'F Y', $timestamp, true ),
			'weekdays'               => $ai1ec_calendar_helper->get_weekdays(),
			'cell_array'             => $cell_array,
			'show_location_in_title' => $ai1ec_settings->show_location_in_title,
			'pagination_links'       => $pagination_links,
			'active_event'           => $active_event,
			'post_ids'               => join( ',', $post_ids ),
		);

		return apply_filters( 'ai1ec_get_month_view', $ai1ec_view_helper->get_theme_view( 'month.php', $view_args ), $view_args );
	}

	/**
	 * get_oneday_view function
	 *
	 * Return the embedded dayh view of the calendar, optionally filtered by
	 * event categories and tags.
	 *
	 * @param array $args     associative array with any of these elements:
	 *   int oneday_offset  => specifies which day to display relative to the
	 *                        current day
	 *   int active_event  => specifies which event to make visible when
	 *                        page is loaded
	 *   array categories  => restrict events returned to the given set of
	 *                        event category slugs
	 *   array tags        => restrict events returned to the given set of
	 *                        event tag names
	 *   array post_ids    => restrict events returned to the given set of
	 *                        post IDs
	 *
	 * @return string	        returns string of view output
	 **/
	function get_oneday_view( $args )
 	{
		global $ai1ec_view_helper,
		       $ai1ec_events_helper,
		       $ai1ec_calendar_helper,
		       $ai1ec_settings;

		$defaults = array(
			'oneday_offset'  => 0,
			'active_event'  => null,
			'categories'    => array(),
			'tags'          => array(),
			'post_ids'      => array(),
		);
		$args = wp_parse_args( $args, $defaults );

		extract( $args );
		// Get components of localized time
		$bits = $ai1ec_events_helper->gmgetdate( $ai1ec_events_helper->gmt_to_local( time() ) );
		// Use actually day of the month as reference timestamp, and apply day offset
		$timestamp = gmmktime( 0, 0, 0, $bits['mon'], $bits['mday'], $bits['year'] );
		$day_shift = 0;
		// Then apply one-day offset
		$day_shift += $args['oneday_offset'];
		$timestamp = gmmktime( 0, 0, 0, $bits['mon'], $bits['mday'] + $day_shift, $bits['year'] );

		$cell_array = $ai1ec_calendar_helper->get_oneday_cell_array( $timestamp, array( 'cat_ids' => $categories, 'cat_ids' => $tags, 'post_ids' => $post_ids ) );
		$pagination_links = $ai1ec_calendar_helper->get_oneday_pagination_links( $oneday_offset );

		$view_args = array(
			'title'                  => date_i18n( 'j F Y', $timestamp, true ),
			'cell_array'             => $cell_array,
			'show_location_in_title' => $ai1ec_settings->show_location_in_title,
			'now_top'                => $bits['hours'] * 60 + $bits['minutes'],
			'pagination_links'       => $pagination_links,
			'active_event'           => $active_event,
			'post_ids'               => join( ',', $post_ids ),
			'time_format'            => get_option( 'time_format', 'g a' ),
			'done_allday_label'      => false,
			'done_grid'              => false
		);
		return apply_filters( 'ai1ec_get_oneday_view', $ai1ec_view_helper->get_theme_view( 'oneday.php', $view_args ), $view_args );
	}

	/**
	 * get_week_view function
	 *
	 * Return the embedded week view of the calendar, optionally filtered by
	 * event categories and tags.
	 *
	 * @param array $args     associative array with any of these elements:
	 *   int week_offset   => specifies which week to display relative to the
	 *                        current week
	 *   int active_event  => specifies which event to make visible when
	 *                        page is loaded
	 *   array categories  => restrict events returned to the given set of
	 *                        event category slugs
	 *   array tags        => restrict events returned to the given set of
	 *                        event tag names
	 *   array post_ids    => restrict events returned to the given set of
	 *                        post IDs
	 *
	 * @return string	        returns string of view output
	 */
	function get_week_view( $args )
 	{
		global $ai1ec_view_helper,
		       $ai1ec_events_helper,
		       $ai1ec_calendar_helper,
		       $ai1ec_settings;

		$defaults = array(
			'week_offset'   => 0,
			'active_event'  => null,
			'categories'    => array(),
			'tags'          => array(),
			'post_ids'      => array(),
		);
		$args = wp_parse_args( $args, $defaults );

		extract( $args );

		// Get components of localized time
		$bits = $ai1ec_events_helper->gmgetdate( $ai1ec_events_helper->gmt_to_local( time() ) );
		// Day shift is initially the first day of the week according to settings
		$day_shift = $ai1ec_events_helper->get_week_start_day_offset( $bits['wday'] );
		// Then apply week offset
		$day_shift += $args['week_offset'] * 7;

		// Now apply to reference timestamp
		$timestamp = gmmktime( 0, 0, 0, $bits['mon'], $bits['mday'] + $day_shift, $bits['year'] );

		$cell_array = $ai1ec_calendar_helper->get_week_cell_array( $timestamp,
			array( 'cat_ids' => $categories, 'cat_ids' => $tags, 'post_ids' => $post_ids ) );
		$pagination_links = $ai1ec_calendar_helper->get_week_pagination_links( $week_offset );

		// Translators: "%s" below represents the week's start date.
		$view_args = array(
			'title'                  => sprintf( __( 'Week of %s', AI1EC_PLUGIN_NAME ), date_i18n( __( 'F j', AI1EC_PLUGIN_NAME ), $timestamp, true ) ),
			'cell_array'             => $cell_array,
			'show_location_in_title' => $ai1ec_settings->show_location_in_title,
			'now_top'                => $bits['hours'] * 60 + $bits['minutes'],
			'pagination_links'       => $pagination_links,
			'active_event'           => $active_event,
			'post_ids'               => join( ',', $post_ids ),
			'time_format'            => get_option( 'time_format', 'g a' ),
			'done_allday_label'      => false,
			'done_grid'              => false
		);
		return apply_filters( 'ai1ec_get_week_view', $ai1ec_view_helper->get_theme_view( 'week.php', $view_args ), $view_args );
	}

	/**
	 * get_agenda_view function
	 *
	 * Return the embedded agenda view of the calendar, optionally filtered by
	 * event categories and tags.
	 *
	 * @param array $args     associative array with any of these elements:
	 *   int page_offset   => specifies which page to display relative to today's page
	 *   int active_event  => specifies which event to make visible when
	 *                        page is loaded
	 *   array categories  => restrict events returned to the given set of
	 *                        event category slugs
	 *   array tags        => restrict events returned to the given set of
	 *                        event tag names
	 *
	 * @return string	        returns string of view output
	 **/
	function get_agenda_view( $args )
 	{
		global $ai1ec_view_helper,
		       $ai1ec_events_helper,
		       $ai1ec_calendar_helper,
		       $ai1ec_settings;

		extract( $args );

		// Get localized time
		$timestamp = $ai1ec_events_helper->gmt_to_local( time() );

		// Get events, then classify into date array
		$event_results = $ai1ec_calendar_helper->get_events_relative_to(
			$timestamp,
			$ai1ec_settings->agenda_events_per_page,
			$page_offset,
			array( 'post_ids' => $post_ids )
		);
		$dates = $ai1ec_calendar_helper->get_agenda_date_array( $event_results['events'] );

		$pagination_links =
			$ai1ec_calendar_helper->get_agenda_pagination_links(
			 	$page_offset, $event_results['prev'], $event_results['next'] );

		// Incorporate offset into date
		$args = array(
			'title'                  => __( 'Agenda', AI1EC_PLUGIN_NAME ),
			'dates'                  => $dates,
			'show_location_in_title' => $ai1ec_settings->show_location_in_title,
			'show_year_in_agenda_dates' => $ai1ec_settings->show_year_in_agenda_dates,
			'page_offset'            => $page_offset,
			'pagination_links'       => $pagination_links,
			'active_event'           => $active_event,
			'expanded'               => $ai1ec_settings->agenda_events_expanded,
			'post_ids'               => join( ',', $post_ids )
		);
		return apply_filters( 'ai1ec_get_agenda_view', $ai1ec_view_helper->get_theme_view( 'agenda.php', $args ), $args );
	}

	/**
	 * ajax_posterboard function
	 *
	 * AJAX request handler for posterboard view.
	 *
	 * @return void
	 **/
	function ajax_posterboard() {
		global $ai1ec_view_helper;

		$this->process_request();

		// View arguments
		$args = array(
			'page_offset'  => $this->request['ai1ec_page_offset'],
			'active_event' => $this->request['ai1ec_active_event'],
			'post_ids'     => array_filter( explode( ',', $this->request['ai1ec_post_ids'] ), 'is_numeric' ),
		);

		// Return this data structure to the client
		$data = array(
			'body_class' => join( ' ', $this->body_class() ),
			'html' => $this->get_posterboard_view( $args ),
		);
		$ai1ec_view_helper->json_response( $data );
	}

	/**
	 * ajax_month function
	 *
	 * AJAX request handler for month view.
	 *
	 * @return void
	 */
	function ajax_month() {
		global $ai1ec_view_helper;

		$this->process_request();

		// View arguments
		$args = array(
			'month_offset' => $this->request['ai1ec_month_offset'],
			'active_event' => $this->request['ai1ec_active_event'],
		  'post_ids'     => array_filter( explode( ',', $this->request['ai1ec_post_ids'] ), 'is_numeric' ),
		);

		// Return this data structure to the client
		$data = array(
			'body_class' => join( ' ', $this->body_class() ),
			'html' => $this->get_month_view( $args ),
		);
		$ai1ec_view_helper->json_response( $data );
	}

	/**
	 * ajax_oneday function
	 *
	 * AJAX request handler for day view.
	 *
	 * @return void
	 */
	function ajax_oneday() {
		global $ai1ec_view_helper;

		$this->process_request();

		// View arguments
		$args = array(
			'oneday_offset' => $this->request['ai1ec_oneday_offset'],
			'active_event' => $this->request['ai1ec_active_event'],
			'post_ids'     => array_filter( explode( ',', $this->request['ai1ec_post_ids'] ), 'is_numeric' ),
		);

		// Return this data structure to the client
		$data = array(
			'body_class' => join( ' ', $this->body_class() ),
			'html' => $this->get_oneday_view( $args ),
		);
		$ai1ec_view_helper->json_response( $data );
	}

	/**
	 * ajax_week function
	 *
	 * AJAX request handler for week view.
	 *
	 * @return void
	 */
	function ajax_week() {
		global $ai1ec_view_helper;

		$this->process_request();

		// View arguments
		$args = array(
			'week_offset' => $this->request['ai1ec_week_offset'],
			'active_event' => $this->request['ai1ec_active_event'],
		  'post_ids'     => array_filter( explode( ',', $this->request['ai1ec_post_ids'] ), 'is_numeric' ),
		);

		// Return this data structure to the client
		$data = array(
			'body_class' => join( ' ', $this->body_class() ),
			'html' => $this->get_week_view( $args ),
		);
		$ai1ec_view_helper->json_response( $data );
	}

	/**
	 * ajax_agenda function
	 *
	 * AJAX request handler for agenda view.
	 *
	 * @return void
	 **/
	function ajax_agenda() {
		global $ai1ec_view_helper;

		$this->process_request();

		// View arguments
		$args = array(
			'page_offset'  => $this->request['ai1ec_page_offset'],
			'active_event' => $this->request['ai1ec_active_event'],
			'post_ids'     => array_filter( explode( ',', $this->request['ai1ec_post_ids'] ), 'is_numeric' ),
		);

		// Return this data structure to the client
		$data = array(
			'body_class' => join( ' ', $this->body_class() ),
			'html' => $this->get_agenda_view( $args ),
		);
		$ai1ec_view_helper->json_response( $data );
	}

	/**
	 * ajax_term_filter function
	 *
	 * AJAX request handler that takes a comma-separated list of event IDs and
	 * comma-separated list of term IDs and returns those event IDs within the
	 * set that have any of the term IDs.
	 *
	 * @return void
	 **/
	function ajax_term_filter() {
		global $ai1ec_view_helper, $ai1ec_events_helper;

		$this->process_request();

		$post_ids = array_unique( explode( ',', $this->request['ai1ec_post_ids'] ) );

		if( $this->request['ai1ec_term_ids'] ) {
			$term_ids = explode( ',', $this->request['ai1ec_term_ids'] );
			$matching_ids = $ai1ec_events_helper->filter_by_terms( $post_ids, $term_ids );
		} else {
			// If no term IDs were provided for filtering, then return all posts
			$matching_ids = $post_ids;
		}

		$unmatching_ids = array_diff( $post_ids, $matching_ids );

		$data = array(
			'matching_ids' => $matching_ids,
			'unmatching_ids' => $unmatching_ids,
	 	);
		$ai1ec_view_helper->json_response( $data );
	}

	/**
	 * body_class function
	 *
	 * Append custom classes to body element.
	 *
	 * @return void
	 **/
	function body_class( $classes = array() ) {
		$classes[] = 'ai1ec-calendar';

		// Reformat action for body class
		$action = $this->request['action'];
		$action = strtr( $action, '_', '-' );
		$action = preg_replace( '/^ai1ec-/', '', $action );

		$classes[] = "ai1ec-action-$action";
		if( isset( $this->request['ai1ec_month_offset'] ) && ! $this->request['ai1ec_month_offset'] &&
				isset( $this->request['ai1ec_page_offset'] ) && ! $this->request['ai1ec_page_offset'] ) {
			$classes[] = 'ai1ec-today';
		}
		return $classes;
	}

	/**
	 * load_css function
	 *
	 * Enqueue any CSS files required by the calendar views, as well as embeds any
	 * CSS rules necessary for calendar container replacement.
	 *
	 * @return void
	 */
	function load_css() {
		global $ai1ec_settings, $ai1ec_view_helper;

		$ai1ec_view_helper->theme_enqueue_style( 'ai1ec-general', 'style.css' );
		$ai1ec_view_helper->theme_enqueue_style( 'ai1ec-calendar', 'calendar.css' );

		if( $ai1ec_settings->calendar_css_selector )
			add_action( 'wp_head', array( &$this, 'selector_css' ) );
	}

	/**
	 * selector_css function
	 *
	 * Inserts dynamic CSS rules into <head> section of page to replace
	 * desired CSS selector with calendar.
	 */
	function selector_css() {
		global $ai1ec_view_helper, $ai1ec_settings;

		$ai1ec_view_helper->display_admin_css(
			'selector.css',
			array( 'selector' => $ai1ec_settings->calendar_css_selector )
		);
	}

	/**
	 * load_js_translations function
	 *
	 * Load js data required by the calendar view
	 *
	 * @return void
	 **/
	function load_js_translations()
 	{
		global $ai1ec_settings,
		       $ai1ec_app_controller;
		$data = array(
			// Point script to AJAX URL - use relative to plugin URL to fix domain mapping issues
			'ajaxurl'       => site_url( 'wp-admin/admin-ajax.php' ),
			// What this view defaults to, in case there is no #hash appended
			'default_hash'  => '#' . http_build_query( $this->request ),
			'export_url'    => AI1EC_EXPORT_URL,
			// Body classes if need to be set manually
			'body_class'    => join( ' ', $this->body_class() ),
		);
		// Replace desired CSS selector with calendar, if selector has been set
		if( $ai1ec_settings->calendar_css_selector )
		{
			$page = get_post( $ai1ec_settings->calendar_post_id );
			$data['selector'] = $ai1ec_settings->calendar_css_selector;
			$data['title']    = $page->post_title;
		}

		$ai1ec_app_controller->localize_script_for_requirejs( 'ai1ec_calendar_requirejs', 'ai1ec_calendar', $data, true );
	}

	/**
	 * function is_category_requested
	 *
	 * Returns the comma-separated list of category IDs that the calendar page
	 * was requested to be prefiltered by.
	 *
	 * @return string
	 */
	function get_requested_categories() {
		return $this->request['ai1ec_cat_ids'];
	}
}
// END class
