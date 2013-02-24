<?php 
/* 
 * Used in single ticket mode, or if an event has a single ticket and ticket tables aren't forced to be displayed for single tickets, hence the 1 count. 
 * This form should have $EM_Ticket available to it. 
 */ 
$col_count = empty($col_count) ? 1:$col_count;
?>

<div class="em-ticket-form">
	<input type="hidden" name="em_tickets[<?php echo $col_count; ?>][ticket_id]" value="<?php echo esc_attr($EM_Ticket->ticket_id) ?>" />
	<div>
		<label><?php _e('Name','dbem') ?></label>
		<input type="text" name="em_tickets[<?php echo $col_count; ?>][ticket_name]" value="<?php echo esc_attr($EM_Ticket->ticket_name) ?>" />
		<a href="#" title="<?php __('Enter a ticket name.','dbem'); ?>">?</a>
	</div>
	<div><label><?php _e('Price','dbem') ?></label><input type="text" name="em_tickets[<?php echo $col_count; ?>][ticket_price]" value="<?php echo esc_attr($EM_Ticket->ticket_price) ?>" /></div>
	<div>
		<label><?php _e('Spaces','dbem') ?></label><input type="text" name="em_tickets[<?php echo $col_count; ?>][ticket_spaces]" value="<?php echo esc_attr($EM_Ticket->ticket_spaces) ?>" />
		<a href="#" title="<?php __('Enter a maximum number of spaces (required).','dbem'); ?>">?</a>
	</div>
	<div class="date-limits em-date-range">
		<?php _e('Available from','dbem') ?> 
		<input type="text" name="ticket_start_pub"  class="em-date-input-loc em-date-start" />
		<input type="hidden" name="em_tickets[<?php echo $col_count; ?>][ticket_start]" class="em-date-input" value="<?php echo ( !empty($EM_Ticket->ticket_start) ) ? date("Y-m-d", $EM_Ticket->start_timestamp):''; ?>" />
		<?php _e('to','dbem'); ?>
		<input type="text" name="ticket_end_pub" class="em-date-input-loc em-date-end" />
		<input type="hidden" name="em_tickets[<?php echo $col_count; ?>][ticket_end]" class="em-date-input" value="<?php echo ( !empty($EM_Ticket->ticket_end) ) ? date("Y-m-d", $EM_Ticket->end_timestamp):''; ?>" />
		<a href="#" title="<?php _e('Add a start or end date (or both) to impose time constraints on ticket availability. Leave either blank for no upper/lower limit.','dbem'); ?>">?</a>
	</div>
	<div class="space-limits">
		<?php _e('Bookings must order between','dbem') ?>
		<input type="text" name="em_tickets[<?php echo $col_count; ?>][ticket_min]" value="<?php echo esc_attr($EM_Ticket->ticket_min) ?>" />
		<?php _e('and','dbem') ?>
		<input type="text" name="em_tickets[<?php echo $col_count; ?>][ticket_max]" value="<?php echo esc_attr($EM_Ticket->ticket_max) ?>" />
		<?php _e('spaces.','dbem') ?>
		<a href="#" title="<?php _e('Leave either blank for no upper/lower limit.','dbem'); ?>">?</a>
	</div>
	<div class="description">
		<label><?php _e('Description','dbem') ?></label>
		<textarea name="em_tickets[<?php echo $col_count; ?>][ticket_description]"><?php echo esc_html(stripslashes($EM_Ticket->ticket_description)) ?></textarea>
	</div>
	<div class="members">
		<label><?php _e('Members Only?','dbem') ?></label>
		<input type="checkbox" value="1" name="em_tickets[<?php echo $col_count; ?>][ticket_members]" <?php if($EM_Ticket->ticket_members) echo 'checked="checked"'; ?> />
	</div>
	<div class="required">
		<label><?php _e('Required?','dbem') ?></label>
		<input type="checkbox" value="1" name="em_tickets[<?php echo $col_count; ?>][ticket_required]" <?php if($EM_Ticket->ticket_required) echo 'checked="checked"'; ?> />
		<a href="#" onclick="return false;" title="<?php _e('If checked every booking must select one or the minimum number of this ticket.'); ?>">?</a>
	</div>
	<?php do_action('em_ticket_edit_form_fields', $col_count, $EM_Ticket); //do not delete, add your extra fields this way, remember to save them too! ?>
</div>	