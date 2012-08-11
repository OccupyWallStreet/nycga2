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

class Buddyvents_Documents_Scripts
{
	/**
	 * Development folder or not
	 */
	static $folder;
	
	/**
	 * Initialize the class
	 *
	 * @package	 Documents
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
	 * @package	 Documents
	 * @since 	 2.1.1
	 */
	function register_scripts()
	{
		wp_register_script( 'bpe-multifile-js', EVENT_URLPATH .'components/documents/js/jquery.MultiFile.min.js', array( 'jquery' ), '1.47', true );
		wp_register_script( 'bpe-documents-js', EVENT_URLPATH .'components/documents/js/documents'. self::$folder .'.js', array( 'jquery', 'bpe-multifile-js' ), '1.47', true );
		wp_localize_script( 'bpe-documents-js', 'bpeDocs', array(
			'fileName' => __( 'File Name', 'events' ),
			'fileDesc' => __( 'File Description', 'events' ),
		) );
	}

	/**
	 * Enqueue any JS files
	 *
	 * @package	 Documents
	 * @since 	 2.1.1
	 */
	function load_scripts()
	{
		if( bp_is_current_component( bpe_get_base( 'slug' ) ) ) :
			if( bp_is_action_variable( bpe_get_option( 'edit_slug' ), 1 ) && bp_is_action_variable( bpe_get_option( 'documents_slug' ), 2 ) || bp_is_current_action( bpe_get_option( 'create_slug' ) ) && bp_is_action_variable( bpe_get_option( 'documents_slug' ), 1 ) ) :
				wp_enqueue_script( 'bpe-multifile-js' );
				wp_enqueue_script( 'bpe-documents-js' );
			endif;
		endif;
	}
}

Buddyvents_Documents_Scripts::init();
?>