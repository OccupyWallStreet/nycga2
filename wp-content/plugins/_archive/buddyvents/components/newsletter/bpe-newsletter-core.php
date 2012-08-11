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

class Buddyvents_Newsletter
{
	public static $services = array();
	
	/**
	 * Start component
	 * 
	 * Start the newsletter component
	 * 
	 * @since 	2.1
	 * @access 	public
	 */
	public static function init()
	{
		add_action( 'init', 			  array( __CLASS__, 'load_services' ),  1 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts'  ), 10 );
	}
	
	/**
	 * Load scripts
	 * 
	 * @since 	2.1
	 * @access 	public
	 */
	public static function load_scripts()
	{
		if( bp_is_current_component( bpe_get_base( 'slug' ) ) ) :
			if( bp_is_current_action( bpe_get_option( 'create_slug' ) ) && bp_is_action_variable( bpe_get_option( 'newsletter_slug' ), 1 ) ||
				bp_is_action_variable( bpe_get_option( 'edit_slug' ), 1 ) && bp_is_action_variable( bpe_get_option( 'newsletter_slug' ), 2 )
			)
			wp_enqueue_script( 'bpe-newsletter', EVENT_URLPATH .'/components/newsletter/js/general.js', array( 'jquery' ), '1.0', true );
		endif;
		if( bpe_is_single_event() )
			wp_enqueue_script( 'bpe-newsletter-subscribe', EVENT_URLPATH .'/components/newsletter/js/subscribe.js', array( 'jquery' ), '1.0', true );
	}
	
	/**
	 * Load services
	 * 
	 * Loads all required services files
	 * 
	 * @since 	2.1
	 * @access 	public
	 */
	public static function load_services()
	{
		if( bpe_get_option( 'enable_mailchimp' ) === true ) :
			self::$services[] = 'mailchimp';
		endif;
		
		if( bpe_get_option( 'enable_cmonitor' ) === true ) :
			self::$services[] = 'cmonitor';
		endif;

		if( bpe_get_option( 'enable_aweber' ) === true ) :
			self::$services[] = 'aweber';
		endif;
		
		// 3rd party plugins can add services here
		self::$services = array_unique( (array) apply_filters( 'bpe_newsletter_load_services', self::$services ) );
		
		if( count( self::$services ) > 0 ) :
			require( EVENT_ABSPATH .'components/newsletter/bpe-newsletter-extension.php' );
			require( EVENT_ABSPATH .'components/newsletter/bpe-newsletter-class.php' );

			foreach( self::$services as $service ) :
				require( EVENT_ABSPATH .'components/newsletter/bpe-newsletter-'. $service .'.php' );
			endforeach;
		endif;
	}
}
Buddyvents_Newsletter::init();
?>