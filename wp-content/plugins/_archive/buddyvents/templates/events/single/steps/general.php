<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

wp_nonce_field( 'bpe_edit_event_'. bpe_get_option( 'general_slug' ) ) ?>

<?php do_action( 'bpe_general_edit_before_name' ) ?>

<label for="name"><?php _e( '* Name', 'events' ) ?></label>
<input type="text" name="name" id="name" value="<?php bpe_event_name( bpe_get_displayed_event() ) ?>" /><br />
<small><?php _e( 'What is your event called?', 'events' ) ?></small>

<hr />

<?php do_action( 'bpe_general_edit_after_name' ) ?>

<label for="description"><?php _e( '* Description', 'events' ) ?></label>
<?php bpe_editor( bpe_get_event_description_raw( bpe_get_displayed_event() ), 'description' ); ?><br />
<small><?php _e( 'Describe your event.', 'events' ) ?></small>

 <hr />
 
<?php do_action( 'bpe_general_edit_after_description' ) ?>

<?php bpe_category_dropdown( bpe_get_event_category_id( bpe_get_displayed_event() ) ) ?>

<hr />

<?php do_action( 'bpe_general_edit_after_category' ) ?>

<label for="url"><?php _e( 'Website', 'events' ) ?></label>
<input type="text" name="url" id="url" value="<?php echo ( bpe_get_event_url_raw( bpe_get_displayed_event() ) ) ? bpe_get_event_url_raw( bpe_get_displayed_event() ) : 'http://'; ?>" /><br />
<small><?php _e( 'Enter an URL for this event.', 'events' ) ?></small>

<hr />

<?php do_action( 'bpe_general_edit_after_url' ) ?>

<label for="location"><?php _e( '* Location', 'events' ) ?></label>
<input type="text" name="location" id="location" value="<?php bpe_event_location( bpe_get_displayed_event() ) ?>" /><br />
<small><?php _e( 'Enter the address where your event will take place.', 'events' ) ?></small>

<label for="venue_name"><?php _e( 'Venue Name', 'events' ) ?></label>
<input type="text" name="venue_name" id="venue_name" value="<?php bpe_event_venue_name( bpe_get_displayed_event() ) ?>" /><br />
<small><?php _e( 'Give your location a name.', 'events' ) ?></small>

<label for="no_coords"><input type="checkbox" id="no_coords" name="no_coords" value="1"<?php if( bpe_get_displayed_event_meta( 'no_coords' ) == 1 ) echo ' checked="checked"' ?> /> <?php _e( 'Check to disable the map (e.g. for online events).', 'events' ) ?></label>

<?php if( bpe_get_displayed_event_meta( 'manual_coords' ) ) : ?>
    <label for="manual_coords"><input type="checkbox" id="manual_coords" name="manual_coords" value="1" checked="checked" /> <?php _e( 'Uncheck to remove your manual coordinates and enable Google coordinates lookup.', 'events' ) ?></label>
<?php endif; ?>

<a class="button" id="coords-change" href="#change-coords"><?php _e( 'Manually change coordinates', 'events' ) ?></a>

<div id="change-coords">
    <div id="loc-map" style="height:500px;margin-top:10px"></div>
</div>

<input type="hidden" id="map_location_lat" name="latitude" value="<?php if( bpe_get_displayed_event_meta( 'manual_coords' ) ): bpe_event_latitude( bpe_get_displayed_event() ); endif; ?>" />
<input type="hidden" id="map_location_lng" name="longitude" value="<?php if( bpe_get_displayed_event_meta( 'manual_coords' ) ): bpe_event_longitude( bpe_get_displayed_event() ); endif; ?>" />

<hr />

<?php do_action( 'bpe_general_edit_after_location' ) ?>

<div class="date-wrapper">
    <label for="start_date"><?php _e( '* Event Start', 'events' ) ?></label>
    <input type="text" class="date-input" name="start_date" id="start_date" value="<?php bpe_event_start_date_raw( bpe_get_displayed_event() ) ?>" />
    <input type="text" class="time-input" name="start_time" id="start_time" value="<?php bpe_event_start_time( bpe_get_displayed_event() ) ?>" />
</div>
    
<div class="date-wrapper">
    <label for="end_date"><?php _e( '* Event End', 'events' ) ?></label>
    <input type="text" class="date-input" name="end_date" id="end_date" value="<?php bpe_event_end_date_raw( bpe_get_displayed_event() ) ?>" />
    <input type="text" class="time-input" name="end_time" id="end_time" value="<?php bpe_event_end_time( bpe_get_displayed_event() ) ?>" />
</div>

<div class="clear"></div>

<?php do_action( 'bpe_general_edit_after_date' ) ?>

<label for="all_day"><input type="checkbox" id="all_day" name="all_day" value="1" <?php if( bpe_get_event_all_day( bpe_get_displayed_event() ) == 1 ): ?>checked="checked"<?php endif; ?>/> <?php _e( 'This event lasts the whole day (if checked, only the start date is required).', 'events' ) ?></label>

<hr />

<?php do_action( 'bpe_general_edit_after_all_day' ) ?>

<label for="public"><input type="checkbox" id="public" name="public" value="1" <?php if( bpe_get_event_public( bpe_get_displayed_event() ) == 1 ): ?>checked="checked"<?php endif; ?>/> <?php _e( 'Check if this is a public event.', 'events' ) ?></label>

<hr />

<?php do_action( 'bpe_general_edit_after_public' ) ?>

<?php if( bpe_get_option( 'enable_attendees' ) === true ) : ?>
<label for="rsvp"><input type="checkbox" id="rsvp" name="rsvp" value="1" <?php if( bpe_get_event_rsvp( bpe_get_displayed_event() ) == 1 ): ?>checked="checked"<?php endif; ?>/> <?php _e( 'Check to enable RSVP.', 'events' ) ?></label>
<hr />

<?php do_action( 'bpe_general_edit_after_rsvp' ) ?>

<?php endif; ?>

<label for="limit_members"><?php printf( __( 'Restrict the number of attendees to %s people.', 'events' ), '<input type="text" id="limit_members" name="limit_members" maxlength="4" value="'. ( ( bpe_get_event_limit_members( bpe_get_displayed_event() ) == 0 ) ? '' : bpe_get_event_limit_members( bpe_get_displayed_event() ) ) .'" />' ) ?> <span class="small">(<?php _e( 'Leave empty to disable.', 'events' ) ?>)</span></label>

<hr />

<?php do_action( 'bpe_general_edit_after_limit_members' ) ?>

<label for="recurrent"><?php _e( 'Choose your recurrence interval', 'events' ) ?></label>
<select id="recurrent" name="recurrent">
    <option value=""></option>
    <?php bpe_recurrent_template_options( bpe_get_event_recurrent( bpe_get_displayed_event() ) ) ?>
</select> <small><?php _e( 'Leave empty to disable recurrence or set to empty to remove recurrence.', 'events' ) ?></small>

<hr />

<?php do_action( 'bpe_add_to_edit_page' ) ?>

<div class="submit">
    <input type="submit" value="<?php _e( 'Save Changes', 'events' ) ?>" id="edit-event" name="edit-event" />
</div>

<label for="notify"><input type="checkbox" id="notify" name="notify" value="1" checked="checked"/> <?php _e( 'Notify existing attendees of the changes', 'events' ) ?></label>

<script type="text/javascript">
var editLat = <?php bpe_check_value( bpe_get_event_latitude( bpe_get_displayed_event() ), 5 ) ?>;
var editLng = <?php bpe_check_value( bpe_get_event_longitude( bpe_get_displayed_event() ), 30 ) ?>;
var weekStart = <?php echo ( bpe_get_option( 'week_start' ) == 1 ) ? 1 : 0; ?>;
var clockType = <?php echo ( bpe_get_option( 'clock_type' ) == 12 ) ? 'true' : 'false'; ?>;
</script>