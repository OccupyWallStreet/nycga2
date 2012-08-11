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
 
class Buddyvents_Admin_Readme extends Buddyvents_Admin_Core
{
	/**
	 * Constructor
	 * 
	 * @package Admin
	 * @since 	1.4
	 */
    public function __construct()
	{
		parent::__construct();
    }

	/**
	 * Content of the readme tab
	 * 
	 * @package Admin
	 * @since 	1.4
	 */
	protected function content()
	{
		include( EVENT_ABSPATH .'readme.html' );
	}
}
?>