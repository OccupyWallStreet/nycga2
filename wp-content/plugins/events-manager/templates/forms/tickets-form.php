<?php 
/* 
 * Used in multiple (default) ticket mode. This is the form that appears as an overlay when a user chooses to create or edit a ticket in their event bookings.
 */ 
?>
<div id="em-tickets-form" style="display:none" title="<?php _e('Create a ticket', 'dbem'); ?>">
	<form action="" method="post">
		<fieldset>
			<div><label><?php _e('Name','dbem'); ?></label><input type="text" name="ticket_name" /></div>
			<div><label><?php _e('Description','dbem') ?></label><br /><textarea name="ticket_description"></textarea></div>
			<div><label><?php _e('Price','dbem') ?></label><input type="text" name="ticket_price" /></div>
			<div>
				<label><?php _e('Available ticket spaces','dbem') ?></label><input type="text" name="ticket_spaces" /><br />
			</div>
			<div class="em-date-range">
				<label><?php _e('Start date of ticket availability','dbem') ?></label>
				<input type="text" name="ticket_start_pub" class="em-date-input-loc em-date-start" />
				<input type="hidden" name="ticket_start" class="em-date-input" />
				<br />
				<label><?php _e('End date of ticket availability','dbem') ?></label>
				<input type="text" name="ticket_end_pub" class="em-date-input-loc em-date-end" />
				<input type="hidden" name="ticket_end" class="em-date-input" />
			</div>
			<div><label><?php _e('Minimum tickets required per booking','dbem') ?></label><input type="text" name="ticket_min" /></div>
			<div><label><?php _e('Maximum tickets required per booking','dbem') ?></label><input type="text" name="ticket_max" /></div>
			<div><label><?php _e('Members Only?','dbem') ?></label> <input type="checkbox" name="ticket_members" value="1" /></div>
			<div><label><?php _e('Required?','dbem') ?></label> <input type="checkbox" name="ticket_required" value="1" /> <a href="#" onclick="return false;" title="<?php _e('If checked every booking must select one or the minimum number of this ticket.'); ?>">?</a></div>
			<?php do_action('em_tickets_edit_form_fields'); //do not delete, add your own fields here ?>
			<input type="hidden" name="ticket_id" />
			<input type="hidden" name="event_id" />
			<input type="hidden" name="prev_slot" />
		</fieldset>
	</form>
</div>