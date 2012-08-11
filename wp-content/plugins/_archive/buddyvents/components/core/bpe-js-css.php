<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

// No direct access is allowed
if( ! defined( 'ABSPATH' ) ) exit;

class Buddyvents_Styles_Scripts
{
	/**
	 * Development folder or not
	 */
	static $folder;
	
	/**
	 * Initialize the class
	 *
	 * @package	 Core
	 * @since 	 1.0
	 */
	function init()
	{
		if( ! is_admin() )
		{
			self::$folder = ( defined( 'WP_LOCAL_DEV' ) && WP_LOCAL_DEV === true ) ? '.dev' : '';
			
			add_action( 'init', 			  array( __CLASS__, 'register_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' 	   ) );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_styles' 	   ) );
		}
	}

	/**
	 * Load any styles
	 *
	 * @package	 Core
	 * @since 	 1.0
	 */
	function load_styles()
	{
		if( file_exists( STYLESHEETPATH .'/events/events.css' ) )
			wp_enqueue_style( 'bpe-css', get_bloginfo('stylesheet_directory') .'/events/events.css' );
		else
			wp_enqueue_style( 'bpe-css', EVENT_URLPATH .'css/events'. self::$folder .'.css' );

		if( file_exists( STYLESHEETPATH .'/events/fullcalendar.css' ) )
			wp_enqueue_style( 'bpe-fullcalendar-css', get_bloginfo('stylesheet_directory') .'/events/fullcalendar.css' );
		else
			wp_enqueue_style( 'bpe-fullcalendar-css', EVENT_URLPATH . 'css/fullcalendar'. self::$folder .'.css' );

		if( file_exists( STYLESHEETPATH .'/colorbox.css' ) )
			wp_enqueue_style( 'colorbox-css', get_bloginfo('stylesheet_directory') .'/events/colorbox.css' );
		else
			wp_enqueue_style( 'colorbox-css', EVENT_URLPATH .'css/colorbox'. self::$folder .'.css' );

		if( bp_is_current_component( bpe_get_base( 'slug' ) ) )
		{
			if( bp_is_current_action( bpe_get_option( 'create_slug' ) ) || bp_is_action_variable( bpe_get_option( 'edit_slug' ), 1 ) || ! bp_current_action() && bpe_get_option( 'default_tab' ) == 'create' )
			{
				if( file_exists( STYLESHEETPATH .'/events/datepicker.css' ) )
					wp_enqueue_style( 'bpe-datepicker-css', get_bloginfo('stylesheet_directory') .'/events/datepicker.css' );
				else
					wp_enqueue_style( 'bpe-datepicker-css', EVENT_URLPATH .'css/datepicker'. self::$folder .'.css' );
			}

			if( bp_is_action_variable( bpe_get_option( 'invite_slug' ), 1 ) || bp_is_action_variable( bpe_get_option( 'edit_slug' ), 1 ) && bp_is_action_variable( bpe_get_option( 'manage_slug' ), 2 ) )
				wp_enqueue_style( 'bpe-messages-autocomplete', EVENT_URLPATH . 'css/jquery.autocompletefb'. self::$folder .'.css' );
		}
	}
	
	/**
	 * Register any JS scripts
	 *
	 * @package	 Core
	 * @since 	 1.0
	 */
	function register_scripts()
	{
		if( ! wp_script_is( 'jquery-ui-datepicker', 'registered' ) )
			wp_register_script( 'jquery-ui-datepicker', EVENT_URLPATH .'js/deprecated/datepicker.js', array( 'jquery' ), '1.0', true );

		if( ! wp_script_is( 'jquery-ui-slider', 'registered' ) )
			wp_register_script( 'jquery-ui-slider', EVENT_URLPATH .'js/deprecated/slider.js', array( 'jquery' ), '1.0', true );

		wp_register_script( 'bpe-general', EVENT_URLPATH .'js/general'. self::$folder .'.js', array( 'jquery' ), '1.0', true );
		wp_localize_script( 'bpe-general', 'bpeGen', array(
			'current' => __( 'Event {current} of {total}', 'events' ),
			'previous' => __( 'Previous', 'events' ),
			'next' => __( 'Next', 'events' ),
			'close' => __( 'Close', 'events' ),
			'nonce' => wp_create_nonce( 'bpe_click_fullcalendar_event_nonce' )
		) );
		
		wp_register_script( 'bpe-fullcalendar-js', EVENT_URLPATH . 'js/fullcalendar.min.js', array( 'jquery' ), '1.5.1', true );
		
		$fc_args = array(
			'today' 		=> __( 'Today', 'events' ),		'month' 		=> __( 'Month', 'events' ),		'week' 			=> __( 'Week', 'events' ),
			'prev' 			=> '&nbsp;&#9668;&nbsp;',		'next' 			=> '&nbsp;&#9658;&nbsp;',		'day' 			=> __( 'Day', 'events' ),
			'prevYear' 		=> '&nbsp;&lt;&lt;&nbsp;',		'nextYear'		=> '&nbsp;&gt;&gt;&nbsp;',		'colMonth' 		=> 'ddd',
			'colWeek' 		=> 'ddd M/d', 					'colDay' 		=> 'dddd M/d',					'headLeft' 		=> 'prev,next today',
			'headCenter'	=> 'title', 					'eventColor'	=> '#1FB3DD',					'headRight'		=> 'month,agendaWeek,agendaDay',
			'defView' 		=> 'month', 					'allDay' 		=> __( 'all-day', 'events' ),	'minSlot' 		=> 30,
			'maxTime' 		=> 24,							'minTime'		=> 0, 							'axisFormat'	=> 'h(:mm)tt',
			'titleMonth' 	=> 'MMMM yyyy', 				'titleDay'		=> 'dddd, MMM d, yyyy', 		'formatAgenda'	=> 'h:mm{ - h:mm}',
			'devFormat' 	=> 'h(:mm)t', 					'longJan' 		=> __( 'January', 'events' ),	'longFeb' 		=> __( 'February', 'events' ),
			'longMar' 		=> __( 'March', 'events' ), 	'longApr' 		=> __( 'April', 'events' ), 	'longMay' 		=> __( 'May', 'events' ),
			'longJun' 		=> __( 'June', 'events' ), 		'longJul' 		=> __( 'July', 'events' ), 		'longAug' 		=> __( 'August', 'events' ),
			'longSep' 		=> __( 'September', 'events' ),	'longOct' 		=> __( 'October', 'events' ), 	'longNov' 		=> __( 'November', 'events' ),
			'longDec' 		=> __( 'December', 'events' ), 	'shortJan'		=> __( 'Jan', 'events' ),		'shortFeb'		=> __( 'Feb', 'events' ),
			'shortMar'		=> __( 'Mar', 'events' ), 		'shortApr'		=> __( 'Apr', 'events' ), 		'shortMay'		=> __( 'Jun', 'events' ),
			'shortJun'		=> __( 'June', 'events' ), 		'shortJul'		=> __( 'Jul', 'events' ), 		'shortAug'		=> __( 'Aug', 'events' ),
			'shortSep'		=> __( 'Sep', 'events' ),		'shortOct'		=> __( 'Oct', 'events' ), 		'shortNov'		=> __( 'Nov', 'events' ),
			'shortDec'		=> __( 'Dec', 'events' ), 		'longSun' 		=> __( 'Sunday', 'events' ),	'longMon' 		=> __( 'Monday', 'events' ),
			'longTue' 		=> __( 'Tuesday', 'events' ),	'longWed' 		=> __( 'Wednesday', 'events' ),	'longThu' 		=> __( 'Thursday', 'events' ),
			'longFri' 		=> __( 'Friday', 'events' ),	'longSat' 		=> __( 'Saturday', 'events' ), 	'shortSun'		=> __( 'Sun', 'events' ),
			'shortMon'		=> __( 'Mon', 'events' ),		'shortTue'		=> __( 'Tue', 'events' ), 		'shortWed' 		=> __( 'Wed', 'events' ),
			'shortThu'		=> __( 'Thu', 'events' ),		'shortFri'		=> __( 'Fri', 'events' ),		'shortSat' 		=> __( 'Sat', 'events' ),
			'defMonth'		=> ( date( 'n' ) - 1 ),			'defYear' 		=> date( 'Y' ),					'titleWeek'		=> "MMM d[ yyyy]{ '&#8212;'[ MMM] d yyyy}",
			'dayLink' 		=> bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. bpe_get_option( 'day_slug' ) .'/',
			'clickNonce' 	=> wp_create_nonce( 'bpe_click_fullcalendar_event_nonce' ),
			'eventNonce' 	=> wp_create_nonce( 'bpe_get_fullcalendar_events_nonce' ),
			'dayNonce' 		=> wp_create_nonce( 'bpe_fullcalendar_day_click' ),
			'firstDay' 		=> ( ( bpe_get_option( 'week_start' ) == 1 ) ? 1 : 0 )
		);
		
		foreach( $fc_args as $key => $val )
			$filtered_fc_args[$key] = apply_filters( 'bpe_fullcalendar_'. $key, $val );

		wp_localize_script( 'bpe-fullcalendar-js', 'fullCal', apply_filters( 'bpe_fullcalendar_args', $filtered_fc_args ) );

		wp_register_script( 'bpe-infobox-js', EVENT_URLPATH .'js/infobox'. self::$folder .'.js', array( 'bpe-maps-js' ), '1.0', true );
		wp_localize_script( 'bpe-infobox-js', 'iBox', array(
			'url' => EVENT_URLPATH,
		) );
		
		wp_register_script( 'bpe-maps-js', 'http://maps.google.com/maps/api/js?sensor=false'. bpe_get_option( 'map_lang' ), array( 'jquery' ) );
		wp_register_script( 'colorbox', EVENT_URLPATH .'js/jquery.colorbox.min.js', array( 'jquery' ), '1.3.9' );
		wp_register_script( 'markerclusterer', EVENT_URLPATH .'js/markerclusterer.min.js', array( 'bpe-maps-js' ), '1.0', true );
		wp_register_script( 'bpe-jquery-autocomplete', EVENT_URLPATH . 'js/jquery.autocomplete.min.js', array( 'jquery' ) );
		wp_register_script( 'bpe-timepicker', EVENT_URLPATH .'js/timepicker.js', array( 'jquery-ui-datepicker', 'jquery-ui-widget', 'jquery-ui-mouse', 'jquery-ui-slider' ), '0.9.3', true );
		wp_register_script( 'bpe-edit', EVENT_URLPATH .'js/edit'. self::$folder .'.js', array( 'bpe-timepicker', 'bpe-maps-js' ), '1.0', true );
	}

	/**
	 * Enqueue any JS files
	 *
	 * @package	 Core
	 * @since 	 1.0
	 */
	function load_scripts()
	{
		if( bpe_is_groups() && bp_is_current_action( bpe_get_base( 'slug' ) ) )
		{
			if( bp_is_action_variable( bpe_get_option( 'map_slug' ), 0 ) || bp_is_action_variable( bpe_get_option( 'calendar_slug' ), 0 ) )
			{
				wp_enqueue_script( 'bpe-maps-js' );
				wp_enqueue_script( 'markerclusterer' );
				wp_enqueue_script( 'bpe-infobox-js' );
			}

			if( bp_is_action_variable( bpe_get_option( 'calendar_slug' ), 0 ) )
			{
				if( bpe_get_option( 'use_fullcalendar' ) === true ) :
					wp_enqueue_script( 'bpe-fullcalendar-js' );
					wp_enqueue_script( 'colorbox' );
				endif;
			}
		}
		
		if( bp_is_current_component( bpe_get_base( 'slug' ) ) )
		{
			wp_enqueue_script( 'bpe-general' );
			wp_enqueue_script( 'colorbox' );

			if( bp_is_current_action( bpe_get_option( 'map_slug' ) ) || ! bp_current_action() && bpe_get_option( 'default_tab' ) == 'map' || bpe_is_event_search_results() || bpe_is_single_event() )
			{
				wp_enqueue_script( 'bpe-maps-js' );
				wp_enqueue_script( 'bpe-infobox-js' );
				wp_enqueue_script( 'markerclusterer' );
			}
			
			if( bp_is_current_action( bpe_get_option( 'calendar_slug' ) ) || ! bp_current_action() && bpe_get_option( 'default_tab' ) == 'calendar' )
			{
				wp_enqueue_script( 'bpe-maps-js' );
				wp_enqueue_script( 'bpe-infobox-js' );
				wp_enqueue_script( 'markerclusterer' );

				if( bpe_get_option( 'use_fullcalendar' ) === true )
					wp_enqueue_script( 'bpe-fullcalendar-js' );
			}

			if( bp_is_current_action( bpe_get_option( 'create_slug' ) ) || bp_is_action_variable( bpe_get_option( 'edit_slug' ), 1 ) || ! bp_current_action() && bpe_get_option( 'default_tab' ) == 'create' ) :
				wp_enqueue_script( 'bpe-maps-js' );
				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_enqueue_script( 'bpe-timepicker' );
			endif;

			if( bp_is_action_variable( bpe_get_option( 'edit_slug' ), 1 ) && bp_is_action_variable( bpe_get_option( 'general_slug' ), 2 ) ) :
				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_enqueue_script( 'bpe-timepicker' );
				wp_enqueue_script( 'bpe-edit' );
			endif;
				
			if( bp_is_action_variable( bpe_get_option( 'invite_slug' ), 1 ) || bp_is_action_variable( bpe_get_option( 'edit_slug' ), 1 ) && bp_is_action_variable( bpe_get_option( 'manage_slug' ), 2 ) )
				wp_enqueue_script( 'bpe-jquery-autocomplete' );
		}
	}
}

Buddyvents_Styles_Scripts::init();
?>