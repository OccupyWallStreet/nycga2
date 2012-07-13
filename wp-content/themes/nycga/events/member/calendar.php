<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */
?>
<?php do_action( 'bpe_member_calendar_before_cal' ) ?>
<?php bpe_user_calendar(); ?>
<?php do_action( 'bpe_member_calendar_after_cal' ) ?>
