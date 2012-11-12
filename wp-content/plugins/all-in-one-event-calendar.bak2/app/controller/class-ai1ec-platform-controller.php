<?php
//
// class-ai1ec-platform-controller.php
// all-in-one-event-calendar
//
// Created by The Seed Studio on 2012-04-10.


/**
 * Ai1ec_Platform_Controller class
 * This class provides functions for turning WordPress into an events-only
 * platform.
 *
 * @package Controllers
 * @author time.ly
 **/
class Ai1ec_Platform_Controller {
	/**
	 * Class instance.
	 *
	 * @var null | object
	 */
	private static $_instance = NULL;

	/**
	 * Return singleton instance.
	 *
	 * @return object
	 */
	static function get_instance() {
		if( self::$_instance === NULL ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Constructor.
	 *
	 * Default constructor - platform interface initialization.
	 */
	private function __construct() {
		global $ai1ec_platform_helper,
		       $ai1ec_settings;

		// Modify role permissions.
		add_action( 'init',                     array( &$ai1ec_platform_helper, 'modify_roles' ) );

		// Only further modify admin UI if event platform is requested.
		if( $ai1ec_settings->event_platform_active ) {
			// Check Ai1ec & WordPress settings.
			add_action( 'init',                   array( &$ai1ec_platform_helper, 'check_settings' ) );
			// Modify meta boxes on admin dashboard for Calendar Administrators.
			add_action( 'admin_init',             array( &$ai1ec_platform_helper, 'modify_dashboard' ) );
			// Scripts/styles for dashboard admin screen.
			add_action( 'admin_enqueue_scripts',  array( &$ai1ec_platform_helper, 'admin_enqueue_scripts' ) );
			// Add option to general settings page.
			add_action( 'ai1ec_general_settings_before', array( &$ai1ec_platform_helper, 'ai1ec_general_settings_before' ) );
			// Save general settings page.
			add_action( 'ai1ec_save_settings',    array( &$ai1ec_platform_helper, 'ai1ec_save_settings' ), 10, 2 );
		}
	}
}
