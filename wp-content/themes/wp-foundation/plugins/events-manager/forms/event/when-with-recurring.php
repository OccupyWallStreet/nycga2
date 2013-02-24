<?php
/* Used by the buddypress and front-end editors to display event time-related information */
global $EM_Event;
$days_names = em_get_days_names();
$hours_format = em_get_hour_format();
$admin_recurring = is_admin() && $EM_Event->is_recurring();
?>
<?php if( is_admin() ): ?><input type="hidden" name="_emnonce" value="<?php echo wp_create_nonce('edit_event'); ?>" /><?php endif; ?>
<!-- START recurrence postbox -->
<div id="em-form-recurrence" class="event-form-recurrence event-form-when">
	<p><?php _e('This is a recurring event.', 'dbem'); ?> <input type="checkbox" id="em-recurrence-checkbox" name="recurring" value="1" <?php if($EM_Event->is_recurring()) echo 'checked' ?> /></p>
	<p class="em-date-range">
		<span class="em-recurring-text"><?php _e ( 'Recurrences span from ', 'dbem' ); ?></span>
		<span class="em-event-text"><?php _e ( 'From ', 'dbem' ); ?></span>				
		<input class="em-date-start em-date-input-loc" type="text" />
		<input class="em-date-input" type="hidden" name="event_start_date" value="<?php echo $EM_Event->event_start_date ?>" />
		<span class="em-recurring-text"><?php _e('to','dbem'); ?></span>
		<span class="em-event-text"><?php _e('to','dbem'); ?></span>
		<input class="em-date-end em-date-input-loc" type="text" />
		<input class="em-date-input" type="hidden" name="event_end_date" value="<?php echo $EM_Event->event_end_date ?>" />
	</p>
	<p>
		<span class="em-recurring-text"><?php _e('Events start from','dbem'); ?></span>
		<span class="em-event-text"><?php _e('Event starts at','dbem'); ?></span>
		<input id="start-time" class="em-time-input em-time-start" type="text" size="8" maxlength="8" name="event_start_time" value="<?php echo date( $hours_format, $EM_Event->start ); ?>" />
		<?php _e('to','dbem'); ?>
		<input id="end-time" class="em-time-input em-time-end" type="text" size="8" maxlength="8" name="event_end_time" value="<?php echo date( $hours_format, $EM_Event->end ); ?>" />
		<?php _e('All day','dbem'); ?> <input type="checkbox" class="em-time-allday" name="event_all_day" id="em-time-all-day" value="1" <?php if(!empty($EM_Event->event_all_day)) echo 'checked="checked"'; ?> />
	</p>
	<div class="em-recurring-text">
		<p>
			<?php _e ( 'This event repeats', 'dbem' ); ?> 
			<select id="recurrence-frequency" name="recurrence_freq">
				<?php
					$freq_options = array ("daily" => __ ( 'Daily', 'dbem' ), "weekly" => __ ( 'Weekly', 'dbem' ), "monthly" => __ ( 'Monthly', 'dbem' ), 'yearly' => __('Yearly','dbem') );
					em_option_items ( $freq_options, $EM_Event->recurrence_freq ); 
				?>
			</select>
			<?php _e ( 'every', 'dbem' )?>
			<input id="recurrence-interval" name='recurrence_interval' size='2' value='<?php echo $EM_Event->interval ; ?>' />
			<span class='interval-desc' id="interval-daily-singular"><?php _e ( 'day', 'dbem' )?></span>
			<span class='interval-desc' id="interval-daily-plural"><?php _e ( 'days', 'dbem' ) ?></span>
			<span class='interval-desc' id="interval-weekly-singular"><?php _e ( 'week on', 'dbem'); ?></span>
			<span class='interval-desc' id="interval-weekly-plural"><?php _e ( 'weeks on', 'dbem'); ?></span>
			<span class='interval-desc' id="interval-monthly-singular"><?php _e ( 'month on the', 'dbem' )?></span>
			<span class='interval-desc' id="interval-monthly-plural"><?php _e ( 'months on the', 'dbem' )?></span>
			<span class='interval-desc' id="interval-yearly-singular"><?php _e ( 'year', 'dbem' )?></span> 
			<span class='interval-desc' id="interval-yearly-plural"><?php _e ( 'years', 'dbem' ) ?></span>
		</p>
		<p class="alternate-selector" id="weekly-selector">
			<?php
				$saved_bydays = ($EM_Event->is_recurring()) ? explode ( ",", $EM_Event->recurrence_byday ) : array(); 
				em_checkbox_items ( 'recurrence_bydays[]', $days_names, $saved_bydays ); 
			?>
		</p>
		<p class="alternate-selector" id="monthly-selector" style="display:inline;">
			<select id="monthly-modifier" name="recurrence_byweekno">
				<?php
					$weekno_options = array ("1" => __ ( 'first', 'dbem' ), '2' => __ ( 'second', 'dbem' ), '3' => __ ( 'third', 'dbem' ), '4' => __ ( 'fourth', 'dbem' ), '-1' => __ ( 'last', 'dbem' ) ); 
					em_option_items ( $weekno_options, $EM_Event->recurrence_byweekno  ); 
				?>
			</select>
			<select id="recurrence-weekday" name="recurrence_byday">
				<?php em_option_items ( $days_names, $EM_Event->recurrence_byday  ); ?>
			</select>
			<?php _e('of each month','dbem'); ?>
		</p>
		<p class="em-duration-range">
			<?php _e('Each event lasts','dbem'); ?>
			<input id="end-days" type="text" size="8" maxlength="8" name="recurrence_days" value="<?php echo $EM_Event->recurrence_days; ?>" />
			<?php _e('day(s)','dbem'); ?>
		</p>
		<p class="em-range-description"><em><?php _e( 'For a recurring event, a one day event will be created on each recurring date within this date range.', 'dbem' ); ?></em></p>
	</div>
	<script type="text/javascript">
	//<![CDATA[
	jQuery(document).ready( function($) {
		$('#em-recurrence-checkbox').change(function(){
			if( $('#em-recurrence-checkbox').is(':checked') ){
				$('.em-recurring-text').show();
				$('.em-event-text').hide();
			}else{
				$('.em-recurring-text').hide();
				$('.em-event-text').show();						
			}
		});
		$('#em-recurrence-checkbox').trigger('change');
	});
	//]]>
	</script>
</div>