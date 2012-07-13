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
<h3 class="pagetitle"><?php _e( 'Transaction cancelled', 'events' ) ?></h3>

<div id="event-dir-search" class="dir-search no-ajax">
    <?php bpe_directory_events_search_form() ?>
</div>

<?php do_action( 'template_notices' ) ?>

<?php do_action( 'bpe_before_cancel_message_navigation' ) ?>

<?php bpe_load_template( 'events/includes/navigation' ); ?>

<?php do_action( 'bpe_before_cancel_message_content' ) ?>

<div id="events-dir-list" class="events dir-list">
    
    <p><?php _e( 'Your payment has been canceled. If this was a mistake, please check out your tickets again!', 'events' ) ?></p>
    <p><?php _e( 'Thank you!', 'events' ) ?></p>

</div><!-- #events-dir-list -->

<?php do_action( 'bpe_after_cancel_message_content' ) ?>