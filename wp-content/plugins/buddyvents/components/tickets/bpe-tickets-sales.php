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

class Buddyvents_Sales_Extension extends Buddyvents_Extension
{
	/**
	 * PHP5 Constructor
	 * 
	 * @package Tickets
	 * @since 	2.0
	 */
	function __construct()
	{
		$this->name = __( 'Sales', 'events' );
		$this->slug = bpe_get_option( 'sales_slug' );

		$this->enable_create_step = false;
		$this->enable_edit_item = true;
		$this->enable_nav_item = false;
	}

	/**
	 * Display the edit screen
	 * 
	 * @package Tickets
	 * @since 	2.0
	 */
	function edit_screen()
	{
		if( ! bpe_is_event_edit_screen( $this->slug ) )
			return false;
		
		bpe_load_template( 'events/single/sales' );
	}
}
bpe_register_event_extension( 'Buddyvents_Sales_Extension' );
?>