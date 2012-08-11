<?php
/******************************************************************************
 * Plugin Name:	Buddyvents
 * Plugin URI:	http://test.shabushabu.eu/
 * Description:	Add events to BuddyPress users and groups
 * Author: 		Boris Glumpler
 * Version: 	2.1.4
 * Author URI: 	http://shabushabu.eu/
 * Text Domain: events
 * Domain Path: /languages/
 * Network: 	true
 * 
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright 	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License 
 ******************************************************************************
 * This script is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 ******************************************************************************
 * Buddyvents 2.1+ is the best ever, but if you do find a bug, then please let
 * us know in our forums at @link http://shabushabu.eu/forums/
 * 
 * NOTICE:
 * If you should find a security vulnerability, then please let us know at
 * boris@shabushabu.eu, rather than publicizing it directly. Once we have
 * fixed the vulnerability, then you can publicize away for all we care :)
 ******************************************************************************
 */

// No direct access is allowed
if( ! defined( 'ABSPATH' ) ) exit;

/**
 * The Buddyvents class should not be referenced directly!
 * Instead, use the available API functions in bpe-common.php.
 * These functions should only be used within an action after
 * <code>plugins_loaded</code> with a priority of 6 or more
 */

class Buddyvents
{
	/**
	 * The plugin version
	 */
	const VERSION 	= '2.1.4';

	/**
	 * The db version
	 */
	const DBVERSION = '3.0';
	
	/**
	 * Plugin creator link
	 */
	const HOME_URL 	= 'http://shabushabu.eu/';

	/**
	 * Minimum required PHP version
	 */
	const MIN_PHP 	= '5.2.4';
	
	/**
	 * Minimum required WP version
	 */
	const MIN_WP 	= '3.2.1';

	/**
	 * Maximum tested WP version
	 */
	const MAX_WP 	= '3.3.2';
	
	/**
	 * Minimum required BP version
	 */
	const MIN_BP 	= '1.5.1';

	/**
	 * Maximum tested BP version
	 */
	const MAX_BP 	= '1.6-bleeding';

	/**
	 * Tested Achievements version
	 * 
	 * Actual working version is 2.2, but the plugin
	 * author has not changed the constant. Could break
	 * some sites.
	 */
	const ACHIEVEMENTS_VERSION = '2.0.6';
	
	/**
	 * Tested BBPress version
	 * 
	 * This is a development version and can change
	 * on a daily basis. This can cause things to break!
	 */
	const BBPRESS_VERSION = '2.1-bleeding';
	
	/**
	 * Tested BP Gallery version
	 */
	const BPGALLERY_VERSION = '1.0.9.3';

	/**
	 * Tested BP Cubepoints version
	 */
	const BP_CUBEPOINTS_VERSION	= '1.9.8.3';

	/**
	 * Tested BP Moderation version
	 * 
	 * Actual working version is 0.1.6, but the plugin
	 * author has not changed the variable. Could break
	 * some sites.
	 */
	const BP_MODERATION_VERSION	= '0.1.5';

	/**
	 * Enable/disable error handling. Mainly
	 * used for debugging and development
	 */
	const ENABLE_ERRORS  = false;
		
	/**
	 * All our options
	 */
	public $options;

	/**
	 * Holds our DB tables
	 */
	public $tables;

	/**
	 * Holds the config values
	 */
	public $config;

	/**
	 * Holds the admin page
	 */
	public $admin;

	/**
	 * Holds all component warnings
	 */
	static $component_warnings;

	/**
	 * Holds all WP/BP warnings
	 */
	static $core_warnings;

	/**
	 * Holds all other warnings
	 */
	static $other_warnings;

	/**
	 * Name of the plugin folder
	 */
	static $plugin_name;

	/**
	 * Get the root blog id
	 */
	static $root_blog;

	/**
	 * Needs to be set to true after plugin checks
	 * for the plugin to load everything. If it
	 * stays false, the plugin will not load
	 */
	static $active = false;

	/**
	 * We need a PHP4 constructor, so
	 * we can output a warning
	 * 
	 * @since 	1.0
	 */
	function Buddyvents()
	{
		$this->__construct();
	}
	
	/**
	 * PHP5 constructor
	 * 
	 * @since 	1.0
	 * @access 	public
	 * 
	 * @uses 	plugin_basename()
	 * @uses 	register_activation_hook()
	 * @uses 	register_uninstall_hook()
	 * @uses 	add_action()
	 */
	public function __construct()
	{
		if( self::ENABLE_ERRORS === true )
			$this->_error_reporting();

		// Let's set some static vars
		self::$plugin_name = plugin_basename( __FILE__ );
		self::$root_blog   = self::get_root_blog_id();

		// Load everything in the correct order. We're using plugins_loaded here as 
		// it's still happening pretty early on, so everything is instantiated before
		// bp_include happens, which is also attached to plugins_loaded.
		add_action( 'plugins_loaded', array( &$this, 'constants'  		  ),  0	);
		add_action( 'plugins_loaded', array( &$this, 'check_requirements' ),  1	);
		add_action( 'plugins_loaded', array( &$this, 'options'  		  ),  1	);
		add_action( 'plugins_loaded', array( &$this, 'translate'  		  ),  1	);
		add_action( 'plugins_loaded', array( &$this, 'globals'  		  ),  2	);
		add_action( 'plugins_loaded', array( &$this, 'core_warnings' 	  ),  4	);
		add_action( 'plugins_loaded', array( &$this, 'component_warnings' ),  4	);
		add_action( 'plugins_loaded', array( &$this, 'other_warnings' 	  ),  4	);
		add_action( 'plugins_loaded', array( &$this, 'components'  		  ),  5	);
		add_action( 'plugins_loaded', array( &$this, 'admin'  			  ),  6	);
		add_action( 'bp_include', 	  array( &$this, 'start' 	  		  ), 10	);
		add_action( 'bp_init', 	  	  array( &$this, 'moderation' 		  ), 20 );

		// activate, deactivate and uninstall hooks
		register_deactivation_hook( self::$plugin_name, array( __CLASS__, 'deactivate' ) );
		register_activation_hook( 	self::$plugin_name, array( __CLASS__, 'activate'   ) );
		register_uninstall_hook( 	self::$plugin_name, array( __CLASS__, 'uninstall'  ) );
	}

	/**
	 * Load all BP related files
	 * 
	 * Attached to bp_include. Stops the plugin if certain conditions are not met.
	 * Sets BUDDYVENTS_SLUG and includes the core files
	 * 
	 * @since 	1.0
	 * @access 	public
	 */
	public function start()
	{
		if( self::$active === false )
			return false;

		if( ! defined( 'BUDDYVENTS_SLUG' ) )
			define( 'BUDDYVENTS_SLUG', ( empty( $this->options->slug ) ? 'events' : $this->options->slug ) );

		// core files
		require( EVENT_ABSPATH .'components/core/bpe-core.php');
	}

	/**
	 * Load the moderation file
	 * 
	 * Attached to bp_init. Checks for and then includes
	 * the moderation file
	 * 
	 * @since 	1.0
	 * @access 	public
	 */
	public function moderation()
	{
		if( self::$active === false )
			return false;

		if( class_exists( 'bpModeration' ) )
			require( EVENT_ABSPATH .'components/moderation/bpe-moderation-core.php' );
	}
	
	/**
	 * Load components
	 * 
	 * Loads all required component files
	 * and common functions
	 * 
	 * @since 	1.6
	 * @access 	public
	 */
	public function components()
	{
		if( self::$active === false )
			return false;

		require( EVENT_ABSPATH .'components/core/bpe-common.php' );

		// load it early, so our components can use it
		require( EVENT_ABSPATH .'components/core/bpe-extension.php' );

 		if( $this->options->enable_api === true && bp_is_active( 'settings' ) ) :
			require( EVENT_ABSPATH .'components/api/bpe-api-core.php' );
			
			if( $this->options->enable_webhooks === true ) :
				require( EVENT_ABSPATH .'components/api/bpe-api-push.php' );
				require( EVENT_ABSPATH .'components/api/models/bpe-push.php' );
				require( EVENT_ABSPATH .'components/api/bpe-api-webhooks.php' );
			endif;
		endif;

		if( $this->options->enable_tickets === true && bp_is_active( 'settings' ) )
			require( EVENT_ABSPATH .'components/tickets/bpe-tickets-core.php' );

 		if( $this->options->enable_twitter === true && bp_is_active( 'settings' ) )
			require( EVENT_ABSPATH .'components/twitter/bpe-twitter-core.php' );

 		if( $this->options->enable_facebook === true && bp_is_active( 'settings' ) )
			require( EVENT_ABSPATH .'components/facebook/bpe-facebook-core.php' );

 		if( $this->options->enable_eventbrite === true && bp_is_active( 'settings' ) )
			require( EVENT_ABSPATH .'components/eventbrite/bpe-eventbrite-core.php' );

 		if( $this->options->enable_newsletter === true && bp_is_active( 'settings' ) )
			require( EVENT_ABSPATH .'components/newsletter/bpe-newsletter-core.php' );

 		if( in_array( $this->options->enable_documents, array( 1, 2, 3 ) ) )
			require( EVENT_ABSPATH .'components/documents/bpe-documents-core.php' );

 		if( in_array( $this->options->enable_schedules, array( 1, 2, 3 ) ) )
			require( EVENT_ABSPATH .'components/schedules/bpe-schedules-core.php' );

 		if( $this->options->enable_cubepoints === true )
			require( EVENT_ABSPATH .'components/cubepoints/bpe-cubepoints-core.php' );

 		//if( $this->options->enable_bp_gallery === true && defined( 'BP_GALLERY_IS_INSTALLED' ) )
			//require( EVENT_ABSPATH .'components/bp-gallery/bpe-gallery-core.php' );

		if( $this->options->enable_achievements === true )
			require( EVENT_ABSPATH .'components/achievements/bpe-achievements-core.php' );
		
		if( bp_is_active( 'groups' ) && $this->options->enable_groups === true )
			require( EVENT_ABSPATH .'components/groups/bpe-groups-core.php' );

		if( $this->options->enable_forums === true && function_exists( 'bbp_get_version' ) )
			if( version_compare( bbp_get_version(), self::BBPRESS_VERSION, '>=' ) == true )
				require( EVENT_ABSPATH .'components/forums/bpe-forums-core.php' );
			
		do_action( 'bpe_register_components' );
	}

	/**
	 * Check for required versions
	 * 
	 * Checks for WP, BP, PHP versions and cURL
	 * 
	 * @since 	1.0
	 * @access 	public
	 * @global 	string 	$wp_version 	Current WordPress version
	 * @uses 	add_action()
	 * @return 	boolean
	 */
	public function check_requirements()
	{		
		global $wp_version, $bp;

		$error = false;
		
		if( ! defined( 'BP_VERSION' ) )
		{
			add_action( 'admin_notices', create_function( '', 'printf( Buddyvents::messages( \'bp\' ), admin_url( \'plugin-install.php\' ) );'	) );
			$error = true;
		}
		elseif( ! empty( $bp->maintenance_mode ) )
		{
			add_action( 'admin_notices', create_function( '', 'echo Buddyvents::messages( \'bp_maintenance\' );' ) );
			$error = true;
		}
		elseif( version_compare( BP_VERSION, self::MIN_BP, '>=' ) == false )
		{
			add_action( 'admin_notices', create_function( '', 'printf( Buddyvents::messages( \'min_bp\' ), Buddyvents::MIN_BP, admin_url( \'update-core.php\' ) );'	) );
			$error = true;
		}

		if( version_compare( $wp_version, self::MIN_WP, '>=' ) == false )
		{
			add_action( 'admin_notices', create_function( '', 'printf( Buddyvents::messages( \'min_wp\' ), Buddyvents::MIN_WP, admin_url( \'update-core.php\' ) );' ) );
			$error = true;
		}
		
		if( version_compare( PHP_VERSION, self::MIN_PHP, '>=' ) == false )
		{
			add_action( 'admin_notices', create_function( '', 'printf( Buddyvents::messages( \'min_php\' ), Buddyvents::MIN_PHP );'	) );
			$error = true;
		}
		
		// cURL is only needed if Twitter or Facebook functionality is activated (libraries)
		// Buddyvents itself uses WP_Http wrapper functions
		if( ! function_exists( 'curl_version' ) )
		{
			if( $this->options->enable_twitter === true || $this->options->enable_facebook === true )
			{
				add_action( 'admin_notices', create_function( '', 'echo Buddyvents::messages( "curl" );' ) );
				$error = true;
			}
		}
		
		self::$active = ( ! $error ) ? true : false;
	}

	/**
	 * Output warnings to the admin screen if certain external components
	 * have not been tested with the current version of Buddyvents
	 * 
	 * Can be disabled in the backend
	 * 
	 * @since 	2.1
	 * @access 	public
	 */
	public function component_warnings()
	{
		$warnings = array();
		
		if( $this->options->enable_achievements === true ) :
			if( version_compare( ACHIEVEMENTS_VERSION, self::ACHIEVEMENTS_VERSION, '==' ) == false ) :
				$warnings[] = sprintf( __( 'Achievements %s (tested version: %s)', 'events' ), ACHIEVEMENTS_VERSION, self::ACHIEVEMENTS_VERSION );
			endif;
		endif;
	
 		if( $this->options->enable_cubepoints === true ) :
			if( version_compare( BP_CUBEPOINT_VERSION, self::BP_CUBEPOINTS_VERSION, '==' ) == false ) :
				$warnings[] = sprintf( __( 'BP Cubepoints %s (tested version: %s)', 'events' ), BP_CUBEPOINT_VERSION, self::BP_CUBEPOINTS_VERSION );
			endif;
		endif;
	
		if( class_exists( 'bpModeration' ) ) :
			if( version_compare( bpModAbstractCore::$plugin_ver, self::BP_MODERATION_VERSION, '==' ) == false ) :
				$warnings[] = sprintf( __( 'BP Moderation %s (tested version: %s)', 'events' ), bpModAbstractCore::$plugin_ver, self::BP_MODERATION_VERSION );
			endif;
		endif;
		
		if( $this->options->enable_forums === true && function_exists( 'bbp_get_version' ) ) :
			if( version_compare( bbp_get_version(), self::BBPRESS_VERSION, '==' ) == false ) :
				$warnings[] = sprintf( __( 'BBPress %s (tested version: %s)', 'events' ), bbp_get_version(), self::BBPRESS_VERSION );
			endif;
		endif;
		
 		/*
		if( $this->options->enable_bp_gallery === true && defined( 'BP_GALLERY_VERSION' ) ) :
			if( version_compare( BP_GALLERY_VERSION, self::BPGALLERY_VERSION, '==' ) == false ) :
				$warnings[] = sprintf( __( 'BP Gallery %s (tested version: %s)', 'events' ), BP_GALLERY_VERSION, self::BPGALLERY_VERSION );
			endif;
		endif;
		*/

		if( count( $warnings ) > 0 ) :
			self::$component_warnings = join( ', ', $warnings );
						
			add_action( 'admin_notices', create_function( '', 'printf( Buddyvents::messages( \'warnings\' ), Buddyvents::$component_warnings );'	) );
		endif;
	}
		
	/**
	 * Output warnings to the admin screen if WP and/or BP
	 * have not been tested with the current version of Buddyvents
	 * 
	 * Can be disabled in the backend
	 * 
	 * @since 	2.1
	 * @access 	public
	 */
	public function core_warnings()
	{
		global $wp_version;
		
		if( $this->options->disable_warnings === true )
			return false;
		
		$warnings = array();

		if( version_compare( self::MAX_WP, $wp_version, '>=' ) == false ) :
			$warnings[] = sprintf( __( 'WordPress %s (last tested version was %s)', 'events' ), $wp_version, self::MAX_WP );
		endif;

		if( version_compare( self::MAX_BP, BP_VERSION, '>=' ) == false ) :
			$warnings[] = sprintf( __( 'BuddyPress %s (last tested version was %s)', 'events' ), BP_VERSION, self::MAX_BP );
		endif;
		
		if( count( $warnings ) > 0 ) :
			self::$core_warnings = join( ', ', $warnings );
						
			add_action( 'admin_notices', create_function( '', 'printf( Buddyvents::messages( \'wp_bp_warnings\' ), Buddyvents::$core_warnings );'	) );
		endif;
	}

	/**
	 * Output various unrelated warnings to the admin screen
	 * 
	 * Can be disabled in the backend
	 * 
	 * @since 	2.1
	 * @access 	public
	 */
	public function other_warnings()
	{
		if( self::$active === false )
			return false;

		if( $this->options->disable_warnings === true )
			return false;
		
		$keys 	  = array();
		$warnings = array();

		if( ! bp_is_active( 'settings' ) ) :
			$keys[] = 'no_settings';
		endif;
		
		if( count( $keys ) > 0 ) :
			foreach( $keys as $key )
				$warnings[] = Buddyvents::messages( $key );
			
			self::$other_warnings = join( ' | ', $warnings );
						
			add_action( 'admin_notices', create_function( '', 'echo Buddyvents::$other_warnings;' ) );
		endif;
	}

	/**
	 * Hold all error messages
	 * 
	 * @since 	2.1
	 * @access 	public
	 * 
	 * @param	$key	string	Error/success key
	 * @param	$type	string	Either 'error' or 'updated'
	 * 
	 * @return	string	Error/success message
	 */
	public static function messages( $key = 'undefined', $type = 'error' )
	{
		$messages = array(
			'bp' 			 => __( 'ERROR: Buddyvents needs BuddyPress to be installed. <a href="%s">Download it now</a>!', 'events' ),
			'curl' 			 => __( 'ERROR: Buddyvents needs cURL installed to function properly. Please ask your hosting company for support!', 'events' ),
			'min_bp' 		 => __( 'ERROR: Buddyvents works only under BuddyPress %s or higher. <a href="%s">Upgrade now</a>!', 'events' ),
			'min_wp' 		 => __( 'ERROR: Buddyvents works only under WordPress %s or higher. <a href="%s">Upgrade now</a>!', 'events' ),
			'min_php' 		 => __( 'ERROR: Buddyvents works only under PHP %s or higher. Please ask your hosting company for support!', 'events' ),
			'warnings'		 => __( 'WARNING: Buddyvents has not been tested with the following plugins: %s', 'events' ),
			'undefined'		 => __( 'ERROR: An undefined Buddyvents related error has occured. Please refresh this page.', 'events' ),
			'no_settings'	 => __( 'WARNING: The BuddyPress settings component is deactivated. You will lose Buddyvents functionality.', 'events' ),
			'bp_maintenance' => __( 'ERROR: BuddyPress is undergoing some maintenance at the moment. Buddyvents will be back shortly!', 'events' ),
			'wp_bp_warnings' => __( 'WARNING: Buddyvents has not been tested with the following programmes: %s', 'events' )
		);
		
		return '<div id="message" class="'. $type .'"><p>'. $messages[$key] .'</p></div>';
	}

	/**
	 * Declare our options
	 * 
	 * Set Buddyvents options early, so we can use the translation function before we
	 * have any translatable strings
	 * 
	 * @since 	2.1
	 * @access 	public
	 * @uses 	get_blog_option()
	 */
	public function options()
	{
		if( self::$active === false )
			return false;

		if( $options = get_blog_option( self::get_root_blog_id(), 'bpe_options' ) )
		{
			foreach( $options as $key => $value )
				$this->options->{$key} = $value;
		}
	}

	/**
	 * Declare our globals
	 * 
	 * Sets Buddyvents database tables and config data
	 * 
	 * @since 	1.0
	 * @access 	public
	 * @uses 	apply_filters()
	 * @global 	object 	$wpdb 	WordPress database object 
	 */
	public function globals()
	{
		global $wpdb;
		
		$this->tables->events 			= $wpdb->base_prefix .'bpe_events';
		$this->tables->members 			= $wpdb->base_prefix .'bpe_members';
		$this->tables->tickets 			= $wpdb->base_prefix .'bpe_tickets';
		$this->tables->sales 			= $wpdb->base_prefix .'bpe_sales';
		$this->tables->invoices 		= $wpdb->base_prefix .'bpe_invoices';
		$this->tables->schedules 		= $wpdb->base_prefix .'bpe_schedules';
		$this->tables->documents 		= $wpdb->base_prefix .'bpe_documents';
		$this->tables->events_meta 		= $wpdb->base_prefix .'bpe_event_meta';
		$this->tables->categories 		= $wpdb->base_prefix .'bpe_event_categories';
		$this->tables->coords 			= $wpdb->base_prefix .'mapo_coords';
		$this->tables->notifications	= $wpdb->base_prefix .'bpe_notifications';
		$this->tables->api 				= $wpdb->base_prefix .'bpe_api';
		$this->tables->webhooks 		= $wpdb->base_prefix .'bpe_webhooks';
		
		// remove any deactivated tabs
		if( is_array( $this->options->deactivated_tabs ) )
		{
			foreach( (array)$this->options->deactivated_tabs as $key => $tab )
				unset( $this->options->tab_order[$tab] );
		}
		
		// all registered views
		$this->config->view_styles = apply_filters( 'bpe_view_styles', array(
			$this->options->list_slug,
			$this->options->grid_slug
		) );
		
		// no logos -> no grid view
		if( $this->options->enable_logo === false )
		{
			$grid_key = array_search( $this->options->grid_slug, $this->config->view_styles );
			unset( $this->config->view_styles[$grid_key] );
		}
		
		// the app key of the Buddyvents AWeber application
		// can be filtered by a plugin to use another app
		$this->config->aweber_app_key = apply_filters( 'bpe_aweber_app_key', '5dc81fc1' );
		
		// these components can be set up for use with PayPal micro payments
		$this->config->micro_payments_enabled = apply_filters( 'bpe_registered_micro_payments_components', array(
			'tickets',
			'forums',
			'newsletter',
			'facebook',
			'twitter',
			'eventbrite',
			'schedules',
			'documents'
		) );
		
		$this->config->default_logo = apply_filters( 'bpe_default_logo_url', EVENT_URLPATH .'css/images/default.png' );

		$this->config->recurrence_intervals = apply_filters( 'bpe_recurrence_intervals', array(
			'daily'		=> __( 'Daily',    'events' ),
			'weekly'	=> __( 'Weekly',   'events' ),
			'biweekly'	=> __( 'Biweekly', 'events' ),
			'monthly'	=> __( 'Monthly',  'events' ),
			'month'		=> __( 'Month',    'events' ),
			'yearly'	=> __( 'Yearly',   'events' )
		) );

		$this->config->forbidden_slugs = apply_filters( 'bpe_forbidden_slugs', array( 
			$this->options->create_slug,
			$this->options->edit_slug,
			$this->options->calendar_slug,
			$this->options->timezone_slug,
			$this->options->venue_slug,
			$this->options->map_slug,
			$this->options->category_slug,
			$this->options->archive_slug,
			$this->options->active_slug,
			$this->options->month_slug,
			$this->options->day_slug,
			$this->options->view_slug,
			$this->options->feed_slug,
			$this->options->api_key_slug,
			$this->options->api_slug
		) );
		
		$this->config->distances = apply_filters( 'bpe_prox_distances', array(
			1, 2, 5, 7, 10, 25, 50, 75, 100, 125, 150, 175, 200, 225, 250
		) );
		
		$this->config->creation_steps[$this->options->general_slug] = array(
			'name' 	   => __( 'General', 'events' ),
			'position' => 0
		);

		if( $this->options->enable_logo === true )
			$this->config->creation_steps[$this->options->logo_slug] = array(
				'name' 	   => __( 'Logo', 'events' ),
				'position' => 80
			);
		
		if( $this->options->enable_invites === true &&  $this->options->approve_events === false )
			$this->config->creation_steps[$this->options->invite_slug] = array(
				'name' 	   => __( 'Invite', 'events' ),
				'position' => 90
			);
		
		$this->config->creation_steps = apply_filters( 'bpe_event_create_steps', $this->config->creation_steps );
	}

	/**
	 * Load the languages
	 * 
	 * @since 	1.0
	 * @access	public
	 * @uses 	load_plugin_textdomain()
	 */
	public function translate()
	{
		if( $this->options->localize_months )
			setlocale( LC_TIME, get_locale() .'.UTF8' );

		load_plugin_textdomain( 'events', false, dirname( self::$plugin_name ) . '/languages/' );
	}

	/**
	 * Include all dependent files
	 * 
	 * Sets up everything for Buddyvents admin pages
	 * 
	 * @since 	1.0
	 * @access 	public
	 * @uses 	is_admin()
	 */
	public function admin()
	{
		if( self::$active === false )
			return false;

		if( is_admin() )
		{
			require_once( EVENT_ABSPATH .'admin/bpe-ajax.php'		);
			require_once( EVENT_ABSPATH .'admin/bpe-admin.php'		);
			require_once( EVENT_ABSPATH .'admin/bpe-functions.php'	);
			require_once( EVENT_ABSPATH .'admin/bpe-core.php'		);
			require_once( EVENT_ABSPATH .'admin/bpe-process.php'	);
			require_once( EVENT_ABSPATH .'admin/bpe-widgets.php'	);
			require_once( EVENT_ABSPATH .'admin/bpe-shortcodes.php'	);

			$this->admin = new Buddyvents_Admin();
		}
	}
	
	/**
	 * Declare all constants
	 * 
	 * EVENT_ADMIN_ROLE and EVENT_DEBUG can be overridden in wp-config.php
	 * 
	 * @since 	1.0
	 * @access 	public
	 * @uses 	plugin_basename()
	 * @uses 	trailingslashit()
	 * @uses 	plugins_url()
	 */
	public function constants()
	{
		if( ! defined( 'EVENT_DEBUG' ) )
			define( 'EVENT_DEBUG', false );

		define( 'EVENT_PLUGIN', 	self::$plugin_name );
		define( 'EVENT_VERSION', 	self::VERSION );
		define( 'EVENT_DBVERSION', 	self::DBVERSION );
		define( 'EVENT_FOLDER', 	plugin_basename( dirname( __FILE__ ) ) );
		define( 'EVENT_ABSPATH', 	trailingslashit( str_replace( '\\','/', WP_PLUGIN_DIR .'/'. EVENT_FOLDER ) ) );
		define( 'EVENT_URLPATH', 	trailingslashit( plugins_url( '/'. EVENT_FOLDER ) ) );
	}
	
	/**
	 * Activate the plugin
	 * 
	 * @since 	1.0
	 * @access 	public
	 * @uses 	delete_blog_option()
	 * @uses 	bpe_activate()
	 */
	public static function activate()
	{
		include_once( dirname( __FILE__ ) .'/admin/bpe-install.php' );
		bpe_activate();
		
		$root_blog = self::get_root_blog_id();
		
		// delete the upgrade nag
		delete_blog_option( $root_blog, 'buddyvents_update_exists' );
		
		// add Buddyvents to the active components array
		$options = get_blog_option( self::get_root_blog_id(), 'bpe_options' );

		$components = bp_get_option( 'bp-active-components' );
		$components[$options->slug] = '1';
					
		bp_update_option( 'bp-active-components', $components );
	}

	/**
	 * Deactivate the plugin
	 * 
	 * Remove Buddyvents from the active components array
	 * 
	 * @since 	2.0
	 * @access 	public
	 */
	public static function deactivate()
	{
		$options = get_blog_option( self::get_root_blog_id(), 'bpe_options' );

		$components = bp_get_option( 'bp-active-components' );
		if( isset( $components[$options->slug] ) )
			unset( $components[$options->slug] );
					
		bp_update_option( 'bp-active-components', $components );		
	}

	/**
	 * Delete all options
	 * 
	 * @since 	1.0
	 * @access 	public
	 * @uses 	bpe_uninstall()
	 */
	public static function uninstall()
	{
		include_once( dirname( __FILE__ ) .'/admin/bpe-install.php' );
		bpe_uninstall();
	}

	/**
	 * Duplicate for bp_get_root_blog_id
	 * 
	 * @since 	2.0
	 * @access 	private
	 * @uses 	apply_filters()
	 * @uses 	is_multisite()
	 * @uses 	get_current_site()
	 * @uses 	get_current_blog_id()
	 */
	private static function get_root_blog_id()
	{
		if( ! defined( 'BP_ROOT_BLOG' ) )
		{	
			$is_bp_multiblog = apply_filters( 'bp_is_multiblog_mode', is_multisite() && defined( 'BP_ENABLE_MULTIBLOG' ) && BP_ENABLE_MULTIBLOG );

			if ( is_multisite() && ! $is_bp_multiblog )
			{
				$current_site = get_current_site();
				$root_blog_id = $current_site->blog_id;
			}
			elseif( is_multisite() && $is_bp_multiblog )
				$root_blog_id = get_current_blog_id();
				
			elseif( ! is_multisite() )
				$root_blog_id = 1;
			
			define( 'BP_ROOT_BLOG', $root_blog_id );
	
		}
		else
			$root_blog_id = BP_ROOT_BLOG;

		return apply_filters( 'bp_get_root_blog_id', (int)$root_blog_id );
	}
	
	/**
	 * Turns error reporting on - off by default
	 * 
	 * @since 	2.1
	 * @access	private
	 */
	private function _error_reporting()
	{
		ini_set( 'display_errors', 1 );
		error_reporting( E_ALL );
	}
}

// get the show on the road
global $bpe;
$bpe = new Buddyvents();
?>