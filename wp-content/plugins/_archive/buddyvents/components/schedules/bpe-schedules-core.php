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

class Buddyvents_Schedules_Core
{
	/**
	 * Initialize the schedules component
	 *
	 * @package	 Core
	 * @since 	 2.1.1
	 */
	 public static function init()
	{
		add_action( 'init', array( __CLASS__, 'includes' ),	 1 );
	}

	/**
	 * Include relevant files
	 *
	 * @package	 Core
	 * @since 	 2.1.1
	 */
	public static function includes()
	{
		$files = array(
			'bpe-schedules-extension',
			'models/bpe-schedules',
			'templatetags/bpe-schedules',
			'bpe-schedules-functions',
			'bpe-schedules-db',
			'bpe-schedules-ajax',
			'bpe-schedules-filters',
			'bpe-schedules-oembed',
			'bpe-schedules-conditionals',
			'bpe-schedules-js'
		);

		if( bp_is_active( 'activity' ) )
			$files[] = 'bpe-schedules-activity';

		foreach( $files as $file )
			require( EVENT_ABSPATH .'components/schedules/'. $file .'.php' );
	}
}
Buddyvents_Schedules_Core::init();
?>