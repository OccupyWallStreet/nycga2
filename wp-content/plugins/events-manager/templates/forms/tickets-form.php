<div id="em-tickets-form" style="display:none">
	<h4><?php _e('Create a ticket', 'dbem'); ?></h4>
	<form action="" method="post">
		<div><label><?php _e('Name','dbem'); ?></label><input type="text" name="ticket_name" /></div>
		<div><label><?php _e('Description','dbem') ?></label><br /><textarea name="ticket_description"></textarea></div>
		<div><label><?php _e('Price','dbem') ?></label><input type="text" name="ticket_price" /></div>
		<div>
			<label><?php _e('Available ticket spaces','dbem') ?></label><input type="text" name="ticket_spaces" /><br />
		</div><br />
		<div><label><?php _e('Start date of ticket availability','dbem') ?></label><input type="hidden" name="ticket_start" class="start" /><input type="text" name="ticket_start_pub" class="start-loc" /></div>
		<div><label><?php _e('End date of ticket availability','dbem') ?></label><input type="hidden" name="ticket_end" class="end" /><input type="text" name="ticket_end_pub" class="end-loc" /></div>
		<div><label><?php _e('Minimum tickets required per booking','dbem') ?></label><input type="text" name="ticket_min" /></div>
		<div><label><?php _e('Maximum tickets required per booking','dbem') ?></label><input type="text" name="ticket_max" /></div>
		<?php do_action('em_tickets_edit_form_fields'); ?>
		<p class="submit">
			<input type="hidden" name="ticket_id" />
			<input type="hidden" name="event_id" />
			<input type="hidden" name="prev_slot" />
			<input type="submit" value="<?php _e('Add Ticket','dbem'); ?>" />
		</p>
	</form>
</div>