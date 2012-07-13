<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

wp_nonce_field( 'bpe_add_event_'. bpe_get_option( 'general_slug' ) ) ?>

<?php do_action( 'bpe_general_create_before_name' ) ?>

<label for="name"><?php _e( '* Name', 'events' ) ?></label>
<input type="text" name="name" id="name" value="<?php bpe_display_cookie( 'name' ) ?>" /><br />
<small><?php _e( 'What is your event called?', 'events' ) ?></small>

<hr />

<?php do_action( 'bpe_general_create_after_name' ) ?>

<label for="description"><?php _e( '* Description', 'events' ) ?></label>
<?php bpe_editor( bpe_display_cookie( 'description', false ), 'description' ); ?><br />
<small><?php _e( 'Describe your event.', 'events' ) ?></small>

<hr />

<?php do_action( 'bpe_general_create_after_description' ) ?>

<?php bpe_category_dropdown( bpe_display_cookie( 'category', false ) ) ?>

<hr />

<?php do_action( 'bpe_general_create_after_category' ) ?>

<label for="url"><?php _e( 'Website', 'events' ) ?></label>
<input type="text" name="url" id="url" value="<?php echo ( bpe_display_cookie( 'url', false ) ) ? bpe_display_cookie( 'url', false ) : 'http://'; ?>" /><br />
<small><?php _e( 'Enter an URL for this event.', 'events' ) ?></small>

<hr />

<?php do_action( 'bpe_general_create_after_url' ) ?>

<label for="location"><?php _e( '* Location', 'events' ) ?></label>
<input type="text" name="location" id="location" value="<?php bpe_display_cookie( 'location' ) ?>" /><br />
<small><?php _e( 'Enter the address where your event will take place.', 'events' ) ?></small>

<label for="venue_name"><?php _e( 'Venue Name', 'events' ) ?></label>
<input type="text" name="venue_name" id="venue_name" value="<?php bpe_display_cookie( 'venue_name' ) ?>" /><br />
<small><?php _e( 'Give your location a name.', 'events' ) ?></small>

<label for="no_coords"><input type="checkbox" id="no_coords" name="no_coords" value="1"<?php if( bpe_display_cookie( 'no_coords', false ) == 1 ) echo ' checked="checked"' ?> /> <?php _e( 'Check to disable the map (e.g. for online events).', 'events' ) ?></label>

<hr />

<?php do_action( 'bpe_general_create_after_location' ) ?>

<div class="date-wrapper">
	<label for="start_date"><?php _e( '* Event Start', 'events' ) ?></label>
	<input type="text" class="date-input" name="start_date" id="start_date" value="<?php bpe_display_cookie( 'start_date' ) ?>" />
	<input type="text" class="time-input" name="start_time" id="start_time" value="<?php bpe_display_cookie( 'start_time' ) ?>" />
</div>
	
<div class="date-wrapper">
	<label for="end_date"><?php _e( '* Event End', 'events' ) ?></label>
	<input type="text" class="date-input" name="end_date" id="end_date" value="<?php bpe_display_cookie( 'end_date' ) ?>" />
	<input type="text" class="time-input" name="end_time" id="end_time" value="<?php bpe_display_cookie( 'end_time' ) ?>" />
</div>

<div class="clear"></div>

<?php do_action( 'bpe_general_create_after_date' ) ?>

<label for="all_day"><input type="checkbox" id="all_day" name="all_day" value="1" <?php if( bpe_display_cookie( 'all_day', false ) == 1 ): ?>checked="checked"<?php endif; ?>/> <?php _e( 'This event lasts the whole day (if checked, only the start date is required).', 'events' ) ?></label>

<hr />

<?php do_action( 'bpe_general_create_after_all_day' ) ?>

<label for="public"><input type="checkbox" id="public" name="public" value="1"<?php if( bpe_display_cookie( 'public', false ) == 1 || ! isset( $_COOKIE['buddyvents_submission'] ) ) echo ' checked="checked"' ?> /> <?php _e( 'Check if this is a public event.', 'events' ) ?></label>

<hr />

<?php do_action( 'bpe_general_create_after_public' ) ?>

<?php if( bpe_get_option( 'enable_attendees' ) === true ) : ?>
<label for="rsvp"><input type="checkbox" id="rsvp" name="rsvp" value="1"<?php if( bpe_display_cookie( 'rsvp', false ) == 1 || ! isset( $_COOKIE['buddyvents_submission'] ) ) echo ' checked="checked"' ?> /> <?php _e( 'Check to enable RSVP.', 'events' ) ?></label>
<hr />

<?php do_action( 'bpe_general_create_after_rsvp' ) ?>

<?php endif; ?>

<label for="limit_members"><?php printf( __( 'Restrict the number of attendees to %s people.', 'events' ), '<input type="text" id="limit_members" name="limit_members" maxlength="4" value="'. bpe_display_cookie( 'limit_members', false ) .'" />' ) ?> <span class="small">(<?php _e( 'Leave empty to disable.', 'events' ) ?>)</span></label> 

<hr />

<?php do_action( 'bpe_general_create_after_limit_members' ) ?>

<label for="recurrent"><?php _e( 'Choose your recurrence interval', 'events' ) ?></label>
<select id="recurrent" name="recurrent">
	<option value=""></option>
	<?php bpe_recurrent_template_options( bpe_display_cookie( 'recurrent', false ) ) ?>
</select> <small><?php _e( 'Leave empty to disable recurrence.', 'events' ) ?></small>

<hr />

<?php do_action( 'bpe_add_to_create_page' ) ?>

<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery(function() {
		var dates = jQuery("#start_date,#end_date").datepicker({
			<?php if( bpe_get_option( 'week_start' ) == 1 ) : ?>firstDay: 1,<?php endif; ?>
			minDate: '+1d',
			changeMonth: false,
			changeYear: false,
			dateFormat: "yy-mm-dd",
			onSelect: function( selectedDate ) {
				var option = this.id == "start_date" ? "minDate" : "maxDate", instance = jQuery(this).data("datepicker");
				date = jQuery.datepicker.parseDate( instance.settings.dateFormat || jQuery.datepicker._defaults.dateFormat, selectedDate, instance.settings );
				dates.not(this).datepicker( "option", option, date );
			}
		});
		jQuery('#start_time,#end_time').timepicker({<?php if( bpe_get_option( 'clock_type' ) == 12 ) : ?>ampm: true<?php endif; ?>});
	});
});
</script>