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
<input type="hidden" id="first-cal-day" value="<?php echo ( bpe_get_option( 'week_start' ) == 1 ) ? 1 : 0; ?>" />
<input type="hidden" id="schedule-counter" value="<?php echo bpe_schedule_amount( bpe_get_event_id( bpe_get_displayed_event() ) ) ?>" />
<input type="hidden" id="start_date" value="<?php bpe_event_start_date_raw( bpe_get_displayed_event() ) ?>" />
<input type="hidden" id="end_date" value="<?php bpe_event_end_date_raw( bpe_get_displayed_event() ) ?>" />

<div id="schedule-wrapper"><?php bpe_edit_schedules() ?></div>
<a class="button add-schedule" href="#"><?php _e( 'Add schedule', 'events' ) ?></a>
<small><?php _e( 'Add a schedule to your event. These will be automatically sorted by time and date.', 'events' ) ?></small>

<div class="submit">
	<input type="submit" value="<?php _e( 'Save Changes', 'events' ) ?>" id="edit-event" name="edit-event" />
</div>

<script type="text/javascript">
jQuery(document).ready( function() {
	var startDate = jQuery("#start_date").val();
	startDate = startDate.split("-");
	var endDate = jQuery("#end_date").val();
	endDate = endDate.split("-");
	jQuery(".schedule-date").datepicker({
		firstDay: <?php echo ( bpe_get_option( 'week_start' ) == 1 ) ? 1 : 0; ?>,
		minDate: new Date(startDate[0], startDate[1], startDate[2]),
		maxDate: new Date(endDate[0], endDate[1], endDate[2]),
		changeMonth: false,
		changeYear: false,
		dateFormat: "yy-mm-dd"
	});
	jQuery('.time-input').timepicker({});
});
</script>