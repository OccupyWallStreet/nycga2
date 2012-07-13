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

class Buddyvents_Groups
{
	/**
	 * Initialize the schedules component
	 *
	 * @package	 Groups
	 * @since 	 2.1.1
	 */
	 public static function init()
	{
		add_action( 'init', array( __CLASS__, 'includes' ),	 1 );
	}

	/**
	 * Include relevant files
	 *
	 * @package	 Groups
	 * @since 	 2.1.1
	 */
	public static function includes()
	{
		$files = array(
			'bpe-groups-approve',
			'bpe-groups-extension',
			'bpe-groups-feeds',
			'bpe-groups-functions'
		);

		if( bpe_get_option( 'enable_address' ) === true ) :
			if( ! defined( 'MAPO_ENABLE_ADDRESS' ) )
				$files[] = 'bpe-groups-info';
		endif;

		foreach( $files as $file )
			require( EVENT_ABSPATH .'components/groups/'. $file .'.php' );
	}
}
Buddyvents_Groups::init();
?>