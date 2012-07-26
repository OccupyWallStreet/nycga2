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

class Buddyvents_API_Core
{
	/**
	 * Setit all up
	 * 
	 * @package API
	 * @since 	1.5
	 */
	public static function init()
	{
		add_action( 'init', 		array( __CLASS__, 'load_files' ),  1 );
		add_action( 'wp', 			array( __CLASS__, 'setup' 	   ),  0 );
		add_action( 'bp_setup_nav', array( __CLASS__, 'setup_nav'  ), 10 );
	}
	
	/**
	 * Load all necessary files
	 * 
	 * @package API
	 * @since 	1.5
	 */
	public static function load_files()
	{
		$files = array(
			'models/bpe-api',
			'bpe-api-filters',
			'bpe-api-db',
			'bpe-api-functions'
		);
		
		foreach( $files as $file )
			require( EVENT_ABSPATH .'components/api/'. $file .'.php' );
	}

	/**
	 * Setup the api
	 * 
	 * @package API
	 * @since 	1.5
	 */
	public static function setup()
	{
		global $wp_query;
	
		if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'api_slug' ) ) )
		{
			$wp_query->is_404 = false;
			status_header( 200 );
	
			require_once( EVENT_ABSPATH . 'components/api/bpe-api-class.php');
			
			$username = ( empty( $_POST['username'] ) ) ? false : $_POST['username'];
			$api_key = ( empty( $_POST['apikey'] ) ) ? false : $_POST['apikey'];
			$data = ( empty( $_POST['data'] ) ) ? false : $_POST['data'];
			
			$bpeAPI = new Buddyvents_API_Response( $username, $api_key, $data );
			exit();
		}
	}

	/**
	 * Add the webhooks settings page
	 * 
	 * @package API
	 * @since 	1.7
	 */
	public static function setup_nav()
	{
		bp_core_new_subnav_item( array(
			'name' 				=> __( 'Events API', 'events' ),
			'slug' 				=> 'events-api',
			'parent_url' 		=> bp_loggedin_user_domain() . bp_get_settings_slug() . '/',
			'parent_slug' 		=> bp_get_settings_slug(),
			'screen_function' 	=> 'bpe_events_settings_api_keys',
			'position' 			=> 35,
			'item_css_id' 		=> 'settings-api',
			'user_has_access' 	=> bp_is_my_profile()
			)
		);
	}
}
Buddyvents_API_Core::init();
?>