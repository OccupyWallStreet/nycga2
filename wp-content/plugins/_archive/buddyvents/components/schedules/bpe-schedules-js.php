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

class Buddyvents_Schedules_Scripts
{
	/**
	 * Development folder or not
	 */
	static $folder;
	
	/**
	 * Initialize the class
	 *
	 * @package	 Schedules
	 * @since 	 2.1.1
	 */
	function init()
	{
		if( ! is_admin() )
		{
			self::$folder = ( defined( 'WP_LOCAL_DEV' ) && WP_LOCAL_DEV === true ) ? '.dev' : '';
			
			add_action( 'init', 			  array( __CLASS__, 'register_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' 	   ) );
		}
	}
	
	/**
	 * Register any JS scripts
	 *
	 * @package	 Schedules
	 * @since 	 2.1.1
	 */
	function register_scripts()
	{
		wp_register_script( 'bpe-schedules-js', EVENT_URLPATH .'components/schedules/js/schedules'. self::$folder .'.js', array( 'jquery' ), '1.0', true );
	}

	/**
	 * Enqueue any JS files
	 *
	 * @package	 Schedules
	 * @since 	 2.1.1
	 */
	function load_scripts()
	{
		if( bp_is_current_component( bpe_get_base( 'slug' ) ) ) :
			if( bp_is_action_variable( bpe_get_option( 'edit_slug' ), 1 ) && bp_is_action_variable( bpe_get_option( 'schedule_slug' ), 2 ) || bp_is_current_action( bpe_get_option( 'create_slug' ) ) && bp_is_action_variable( bpe_get_option( 'schedule_slug' ), 1 ) ) :
				wp_enqueue_script( 'bpe-schedules-js' );
			endif;
		endif;
	}
}

Buddyvents_Schedules_Scripts::init();
?>