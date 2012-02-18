<?php
global $EM_Event, $post;
$hours_format = em_get_hour_format();
$required = "<i>*</i>";
?>
<div class="event-form-when" id="em-form-when">
	<p>
		<?php _e ( 'From ', 'dbem' ); ?>					
		<input id="em-date-start-loc" type="text" />
		<input id="em-date-start" type="hidden" name="event_start_date" value="<?php echo $EM_Event->event_start_date ?>" />
		<?php _e('to','dbem'); ?>
		<input id="em-date-end-loc" type="text" />
		<input id="em-date-end" type="hidden" name="event_end_date" value="<?php echo $EM_Event->event_end_date ?>" />
	</p>
	<p>
		<span class="em-event-text"><?php _e('Event starts at','dbem'); ?></span>
		<input id="start-time" type="text" size="8" maxlength="8" name="event_start_time" value="<?php echo date( $hours_format, $EM_Event->start ); ?>" />
		<?php _e('to','dbem'); ?>
		<input id="end-time" type="text" size="8" maxlength="8" name="event_end_time" value="<?php echo date( $hours_format, $EM_Event->end ); ?>" />
		<?php _e('All day','dbem'); ?> <input type="checkbox" name="event_all_day" id="em-time-all-day" value="1" <?php if(!empty($EM_Event->event_all_day)) echo 'checked="checked"'; ?> />
	</p>
	<span id='event-date-explanation'>
	<?php _e( 'This event spans every day between the beginning and end date, with start/end times applying to each day.', 'dbem' ); ?>
	</span>
</div>  
<?php if( false && get_option('dbem_recurrence_enabled') && $EM_Event->is_recurrence() ) : //in future, we could enable this and then offer a detach option alongside, which resets the recurrence id and removes the attachment to the recurrence set ?>
<input type="hidden" name="recurrence_id" value="<?php echo $EM_Event->recurrence_id; ?>" />
<?php endif;