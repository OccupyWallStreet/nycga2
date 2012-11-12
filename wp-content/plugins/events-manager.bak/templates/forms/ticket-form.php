<?php 
/* Used in single booking mode, hence the 1 count. This form should have $EM_Ticket available to it. */ 
$col_count = empty($col_count) ? 1:$col_count;
?>

<div class="em-ticket-form">
	<input type="hidden" name="em_tickets[<?php echo $col_count; ?>][ticket_id]" value="<?php echo esc_attr($EM_Ticket->id) ?>" />
	<input type="hidden" name="em_tickets[<?php echo $col_count; ?>][ticket_name]" value="<?php echo esc_attr($EM_Ticket->name) ?>" />
	<div><label><?php _e('Price','dbem') ?></label><input type="text" name="em_tickets[<?php echo $col_count; ?>][ticket_price]" value="<?php echo esc_attr($EM_Ticket->price) ?>" /></div>
	<div>
		<label><?php _e('Spaces','dbem') ?></label><input type="text" name="em_tickets[<?php echo $col_count; ?>][ticket_spaces]" value="<?php echo esc_attr($EM_Ticket->spaces) ?>" />
		<a href="#" title="<?php _e('If blank, there\'s no space limit.','dbem'); ?>">?</a>
	</div>
	<div class="date-limits">
		<?php _e('Available from','dbem') ?> 
		<input type="hidden" name="em_tickets[<?php echo $col_count; ?>][ticket_start]" class="start" value="<?php echo ( !empty($EM_Ticket->start) ) ? date("Y-m-d H:i", $EM_Ticket->start_timestamp):''; ?>" />
		<input type="text" name="ticket_start_pub"  class="start-loc" />
		<?php _e('to','dbem'); ?>
		<input type="hidden" name="em_tickets[<?php echo $col_count; ?>][ticket_end]" class="end" value="<?php echo ( !empty($EM_Ticket->end) ) ? date("Y-m-d H:i", $EM_Ticket->end_timestamp):''; ?>" />
		<input type="text" name="ticket_end_pub"  class="end-loc" />
		<a href="#" title="<?php _e('Add a start or end date (or both) to impose time constraints on ticket availability. Leave either blank for no upper/lower limit.','dbem'); ?>">?</a>
	</div>
	<div class="space-limits">
		<?php _e('Bookings must order between','dbem') ?>
		<input type="text" name="em_tickets[<?php echo $col_count; ?>][ticket_min]" value="<?php echo esc_attr($EM_Ticket->min) ?>" />
		<?php _e('and','dbem') ?>
		<input type="text" name="em_tickets[<?php echo $col_count; ?>][ticket_max]" value="<?php echo esc_attr($EM_Ticket->max) ?>" />
		<?php _e('spaces.','dbem') ?>
		<a href="#" title="<?php _e('Leave either blank for no upper/lower limit.','dbem'); ?>">?</a>
	</div>
	<div class="description">
		<label><?php _e('Description','dbem') ?></label>
		<textarea name="em_tickets[<?php echo $col_count; ?>][ticket_description]"><?php echo esc_html(stripslashes($EM_Ticket->description)) ?></textarea>
	</div>
</div>	