<?php 
/* @var $EM_Event EM_Event */
global $allowedposttags;
$EM_Tickets = $EM_Event->get_bookings()->get_tickets(); //already instantiated, so should be a quick retrieval.  
?>
<table class="em-tickets" cellspacing="0" cellpadding="0">
	<tr>
		<th class="em-bookings-ticket-table-type"><?php _e('Ticket Type','dbem') ?></th>
		<?php if( !$EM_Event->is_free() ): ?>
		<th class="em-bookings-ticket-table-price"><?php _e('Price','dbem') ?></th>
		<?php endif; ?>
		<th class="em-bookings-ticket-table-spaces"><?php _e('Spaces','dbem') ?></th>
	</tr>
	<?php foreach( $EM_Tickets->tickets as $EM_Ticket ): /* @var $EM_Ticket EM_Ticket */ ?>
		<?php if( $EM_Ticket->is_available() || get_option('dbem_bookings_tickets_show_unavailable') ): ?>
		<tr class="em-ticket" id="em-ticket-<?php echo $EM_Ticket->ticket_id; ?>">
			<td class="em-bookings-ticket-table-type"><?php echo wp_kses_data($EM_Ticket->ticket_name); ?><?php if(!empty($EM_Ticket->ticket_description)) :?><br><span class="ticket-desc"><?php echo wp_kses($EM_Ticket->ticket_description,$allowedposttags); ?></span><?php endif; ?></td>
			<?php if( !$EM_Event->is_free() ): ?>
			<td class="em-bookings-ticket-table-price"><?php echo $EM_Ticket->get_price(true); ?></td>
			<?php endif; ?>
			<?php do_action('em_booking_form_tickets_col', $EM_Ticket); ?>
			<td class="em-bookings-ticket-table-spaces">
				<?php 
					$default = !empty($_REQUEST['em_tickets'][$EM_Ticket->ticket_id]['spaces']) ? $_REQUEST['em_tickets'][$EM_Ticket->ticket_id]['spaces']:0;
					$spaces_options = $EM_Ticket->get_spaces_options(true,$default);
					if( $spaces_options ){
						echo $spaces_options;
					}else{
						echo "<strong>".__('N/A','dbem')."</strong>";
					}
				?>
			</td>
		</tr>
		<?php do_action('em_booking_form_tickets_loop', $EM_Ticket); ?>
		<?php endif; ?>
	<?php endforeach; ?>
</table>