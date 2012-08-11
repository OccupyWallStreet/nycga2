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

class BPE_Tickets_JS
{
	/**
	 * PHP5 Constructor
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @uses 	is_admin()
	 * @uses	add_action()
	 */
	function __construct()
	{
		if( ! is_admin() )
			add_action( 'wp_print_scripts', array( &$this, 'load_scripts' ) );
	}
	
	/**
	 * Load all ticket relevant scripts
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @uses	bp_is_current_component()
	 * @uses	bpe_get_base()
	 * @uses	wp_enqueue_script()
	 * @uses	bp_is_current_action()
	 * @uses	bpe_get_option()
	 * @uses	bp_is_action_variable()
	 */
	function load_scripts()
	{
		if( bp_is_current_component( bpe_get_base( 'slug' ) ) )
		{
			wp_enqueue_script( 'bpe-tickets-general', EVENT_URLPATH .'components/tickets/js/general.js', array( 'jquery' ), '1.0', true );
			
			if( bp_is_current_action( bpe_get_option( 'create_slug' ) ) && bp_is_action_variable( bpe_get_option( 'tickets_slug' ), 1 ) || bp_is_action_variable( bpe_get_option( 'edit_slug' ), 1 ) && bp_is_action_variable( bpe_get_option( 'tickets_slug' ), 2 ) )
			{
				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_enqueue_script( 'bpe-tickets-create', EVENT_URLPATH .'components/tickets/js/create.js', array( 'jquery' ), '1.0', true );
			}
		}
	}
}
$bpe_tickets_js = new BPE_Tickets_JS();
?>