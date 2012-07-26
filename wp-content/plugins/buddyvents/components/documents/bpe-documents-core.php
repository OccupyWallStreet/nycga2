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

class Buddyvents_Documents_Core
{
	/**
	 * Initialize the documents component
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
			'bpe-documents-extension',
			'models/bpe-documents',
			'templatetags/bpe-documents',
			'bpe-documents-functions',
			'bpe-documents-db',
			'bpe-documents-filters',
			'bpe-documents-oembed',
			'bpe-documents-conditionals',
			'bpe-documents-js'
					);

		if( bp_is_active( 'activity' ) )
			$files[] = 'bpe-documents-activity';


		foreach( $files as $file )
			require( EVENT_ABSPATH .'components/documents/'. $file .'.php' );
	}
}
Buddyvents_Documents_Core::init();
?>