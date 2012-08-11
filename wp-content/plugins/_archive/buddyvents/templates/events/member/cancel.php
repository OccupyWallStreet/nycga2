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
<h3><?php _e( 'Transaction cancelled', 'events' ) ?></h3>
<p><?php printf( __( 'Your invoice payment has been canceled. If this was a mistake, please go back to your <a href="%s">invoice page</a>!', 'events' ), bpe_get_invoice_link() ) ?></p>
<p><?php _e( 'Thank you!', 'events' ) ?></p>