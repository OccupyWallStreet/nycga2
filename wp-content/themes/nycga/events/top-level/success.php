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
<h3 class="pagetitle"><?php _e( 'Transaction completed', 'events' ) ?></h3>

<div id="event-dir-search" class="dir-search no-ajax">
    <?php bpe_directory_events_search_form() ?>
</div>

<?php do_action( 'template_notices' ) ?>

<?php do_action( 'bpe_before_success_message_navigation' ) ?>

<?php bpe_load_template( 'events/includes/navigation' ); ?>

<?php do_action( 'bpe_before_success_message_content' ) ?>

<div id="events-dir-list" class="events dir-list">
    
    <p><?php _e( 'Your payment has been made and the transaction has been completed. An email containing the purchase details will be sent to you shortly.', 'events' ) ?></p>
    <p><?php _e( 'You can log into your account at <a href="http://www.paypal.com">PayPal</a> to see the details of this transaction.', 'events' ) ?></p>
    <p><?php _e( 'Thank you!', 'events' ) ?></p>
    
    <hr />
    
    <p><a href="<?php bpe_ticket_event_link() ?>"><?php _e( 'Go to event &rarr;', 'events' ) ?></a></p>

</div><!-- #events-dir-list -->

<?php do_action( 'bpe_after_success_message_content' ) ?>