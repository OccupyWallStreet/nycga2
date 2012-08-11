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
<h3 class="pagetitle">
	<?php _e( 'Events Directory', 'events' ) ?><?php if( ! bpe_is_restricted() ) : ?>&nbsp;<a class="button" href="<?php echo bp_get_root_domain() . '/' . bpe_get_base( 'root_slug' ) . '/'. bpe_get_option( 'create_slug' ) ?>"><?php _e( 'Create Event', 'events' ) ?></a><?php endif; ?>
</h3>

<div id="event-dir-search" class="dir-search no-ajax">
    <?php bpe_directory_events_search_form() ?>
</div>

<?php do_action( 'template_notices' ) ?>

<?php do_action( 'bpe_before_directory_events_content' ) ?>

<?php bpe_load_template( 'events/includes/navigation' ); ?>

<?php do_action( 'bpe_before_directory_events_list' ) ?>

<div id="events-dir-list" class="events dir-list">
    
	<?php do_action( 'bpe_before_active_events_loop' ) ?>

    <?php if( bpe_is_edit_event() ) : ?>
        <?php bpe_load_template( 'events/single/edit' ); ?>

    <?php elseif( bpe_is_invite_event() ) : ?>
        <?php bpe_load_template( 'events/single/invite' ); ?>

    <?php elseif( bpe_is_event_signup() ) : ?>
        <?php bpe_load_template( 'events/single/signup' ); ?>

    <?php elseif( bpe_is_event_attendees() ) : ?>
        <?php bpe_load_template( 'events/single/attendees' ); ?>

    <?php elseif( bpe_is_event_directions() ) : ?>
        <?php bpe_load_template( 'events/single/directions' ); ?>

    <?php elseif( bpe_is_single_event() ) : ?>
        <?php bpe_load_template( 'events/single/home' ); ?>

    <?php elseif( bpe_is_events_directory_loop() ) : ?>
        <?php bpe_load_template( 'events/includes/loop' ); ?>

    <?php endif; ?>

	<?php do_action( 'bpe_after_active_events_loop' ) ?>

</div><!-- #events-dir-list -->

<?php do_action( 'bpe_active_events_content' ) ?>