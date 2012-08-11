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
<input type="hidden" id="schedule-counter" value="<?php echo bpe_schedule_counter() ?>" />
<input type="hidden" id="first-cal-day" value="<?php echo ( bpe_get_option( 'week_start' ) == 1 ) ? 1 : 0; ?>" />
<input type="hidden" id="start_date" value="<?php bpe_display_cookie( 'start_date' ) ?>" />
<input type="hidden" id="end_date" value="<?php bpe_display_cookie( 'end_date' ) ?>" />

<div id="schedule-wrapper"><?php bpe_edit_schedules() ?></div>
<a class="button add-schedule" href="#"><?php _e( 'Add schedule', 'events' ) ?></a>
<small><?php _e( 'Add a schedule to your event. These will be automatically sorted by time and date.', 'events' ) ?></small>

<script type="text/javascript">
jQuery(document).ready(function() {
	var minDateCreate = jQuery("#start_date").val();
	minDateCreate = minDateCreate.split('-');

	var maxDateCreate = jQuery("#end_date").val();
	maxDateCreate = maxDateCreate.split('-');

	jQuery(".schedule-date").datepicker({
			<?php if( bpe_get_option( 'week_start' ) == 1 ) : ?>firstDay: 1,<?php endif; ?>
			minDate: new Date( minDateCreate[0], minDateCreate[1], minDateCreate[2] ),
			maxDate: new Date( maxDateCreate[0], maxDateCreate[1], maxDateCreate[2] ),
			changeMonth: false,
			changeYear: false,
			dateFormat: "yy-mm-dd"
	});
});
</script>