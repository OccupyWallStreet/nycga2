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
<div id="event-actions">

	<?php do_action( 'bpe_single_nav_before_image' ) ?>
           
    <?php if( bpe_are_logos_enabled() ) : ?>
    <div class="item-avatar">
        <?php bpe_event_image() ?>
    </div>
    <?php endif; ?>
    
    <?php do_action( 'bpe_single_nav_after_image' ) ?>

    <a class="button<?php if( bpe_is_home_event() ) : echo ' selected'; endif; ?>" href="<?php bpe_event_link() ?>"><?php _e( 'Home', 'events' ) ?></a>

     <?php if( bpe_is_member() && bpe_is_rsvp_enabled() ) : ?>
        <a class="button<?php if( bpe_is_event_attendees() ) : echo ' selected'; endif; ?>" href="<?php bpe_event_link() ?><?php echo bpe_get_option( 'attendee_slug' ) ?>/"><?php _e( 'Attendees', 'events' ) ?></a>
        <?php if( ! bpe_is_private_event() && ! bpe_is_closed_event() && bpe_get_option( 'enable_invites' ) == true && ! bpe_is_event_cancelled( bpe_get_displayed_event() ) ) : ?>
            <a class="button<?php if( bpe_is_invite_event() ) : echo ' selected'; endif; ?>" href="<?php bpe_event_link() ?><?php echo bpe_get_option( 'invite_slug' ) ?>/"><?php _e( 'Invite', 'events' ) ?></a>
        <?php endif; ?>
    <?php endif; ?>
    
    <?php if( ! bpe_is_closed_event() && bpe_has_event_location() && bpe_are_directions_enabled( bpe_get_displayed_event() ) && ! bpe_is_event_cancelled( bpe_get_displayed_event() ) ) : ?>
		<a class="button<?php if( bpe_is_event_directions() ) : echo ' selected'; endif; ?>" href="<?php bpe_event_link() ?><?php echo bpe_get_option( 'directions_slug' ) ?>/"><?php _e( 'Directions', 'events' ) ?></a>
    <?php endif; ?>

    <?php do_action( 'bpe_single_nav_inside_menu', bpe_get_displayed_event(), bp_action_variable( 1 ) ) ?>

    <?php if( bpe_is_admin() && ! bpe_is_event_cancelled( bpe_get_displayed_event() ) ) : ?>
        <a class="button<?php if( bpe_is_edit_event() ) : echo ' selected'; endif; ?>" href="<?php bpe_event_link() ?><?php echo bpe_get_option( 'edit_slug' ) ?>/"><?php _e( 'Edit Event', 'events' ) ?></a>
    <?php endif; ?>

    <?php if( bpe_is_ical_enabled( bpe_get_displayed_event() ) && ! bpe_is_event_cancelled( bpe_get_displayed_event() ) ) : ?>
		<a class="button" href="<?php bpe_event_ical_link() ?>"><?php _e( 'iCalendar Download', 'events' ) ?></a>
    <?php endif; ?>
   
    <?php bpe_attendance_button() ?>

	<?php bpe_event_leftover_spots() ?>
	
    <?php do_action( 'bpe_single_nav_after_menu' ) ?>
</div>