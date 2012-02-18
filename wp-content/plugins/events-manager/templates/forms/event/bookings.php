<?php
global $EM_Event, $post;
$localised_date_format = em_get_date_format();
?>
<div id="event-rsvp-box">
	<input id="event-rsvp" name='event_rsvp' value='1' type='checkbox' <?php echo ($EM_Event->event_rsvp) ? 'checked="checked"' : ''; ?> />
	&nbsp;&nbsp;
	<?php _e ( 'Enable registration for this event', 'dbem' )?>
</div>
<div id="event-tickets" style="<?php echo ($EM_Event->event_rsvp) ? '':'display:none;' ?>">
	<?php
	//get tickets here and if there are none, create a blank ticket
	$EM_Tickets = $EM_Event->get_tickets();
	if( count($EM_Tickets->tickets) == 0 ){
		$EM_Tickets->tickets[] = new EM_Ticket();
		$delete_temp_ticket = true;
	}
	if( get_option('dbem_bookings_tickets_single') ){	
		$EM_Ticket = $EM_Tickets->get_first();							
		include( em_locate_template('forms/ticket-form.php') );
	}else{
		?>		
		<p><strong><?php _e('Tickets','dbem'); ?></strong></p>
		<p><em><?php _e('You can have single or multiple tickets, where certain tickets become availalble under certain conditions, e.g. early bookings, group discounts, maximum bookings per ticket, etc.', 'dbem'); ?> <?php _e('Basic HTML is allowed in ticket labels and descriptions.','dbem'); ?></em></p>					
		<table class="form-table">
			<thead>
				<tr valign="top">
					<th class="ticket-status">&nbsp;</th>
					<th><?php _e('Ticket Name','dbem'); ?></th>
					<th><?php _e('Price','dbem'); ?></th>
					<th><?php _e('Min/Max','dbem'); ?></th>
					<th><?php _e('Start/End','dbem'); ?></th>
					<th><?php _e('Avail. Spaces','dbem'); ?></th>
					<th><?php _e('Booked Spaces','dbem'); ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>    
			<tfoot>
				<tr valign="top">
					<td colspan="6">
						<a href="#" id="em-tickets-add" rel="#em-tickets-form"><?php _e('Add new ticket','dbem'); ?></a>
					</td>
				</tr>
			</tfoot>
			<tbody id="em-tickets-body">
				<?php
					global $allowedposttags;
					$count = 1;
					foreach( $EM_Tickets->tickets as $EM_Ticket){
						/* @var $EM_Ticket EM_Ticket */
						?>
						<tr valign="top" id="em-tickets-row-<?php echo $count ?>" class="em-tickets-row">
							<td class="ticket-status"><span class="<?php echo ($EM_Ticket->is_available()) ? 'ticket_on':'ticket_off'; ?>"></span></td>													
							<td class="ticket-name"><span class="ticket_name"><?php echo wp_kses_data($EM_Ticket->ticket_name); ?></span><br /><span class="ticket_description"><?php echo wp_kses($EM_Ticket->ticket_description,$allowedposttags); ?></span></td>
							<td class="ticket-price">
								<span class="ticket_price"><?php echo ($EM_Ticket->ticket_price) ? $EM_Ticket->ticket_price : __('Free','dbem'); ?></span>
							</td>
							<td class="ticket-limit">
								<span class="ticket_min">
									<?php  echo ( !empty($EM_Ticket->ticket_min) ) ? $EM_Ticket->ticket_min:'-'; ?>
								</span> / 
								<span class="ticket_max"><?php echo ( !empty($EM_Ticket->ticket_max) ) ? $EM_Ticket->ticket_max:'-'; ?></span>
							</td>
							<td class="ticket-time">
								<span class="ticket_start"><?php echo ( !empty($EM_Ticket->ticket_start) ) ? date($localised_date_format, $EM_Ticket->start_timestamp):''; ?></span> -
								<span class="ticket_end"><?php echo ( !empty($EM_Ticket->ticket_end) ) ? date($localised_date_format, $EM_Ticket->end_timestamp):''; ?></span>
							</td>
							<td class="ticket-qty">
								<span class="ticket_available_spaces"><?php echo $EM_Ticket->get_available_spaces(); ?></span>/
								<span class="ticket_spaces">
									<?php 
									if( $EM_Ticket->get_spaces() ){
										echo $EM_Ticket->get_spaces();
										echo (!empty($EM_Ticket->ticket_spaces_limit)) ? '':'*';
									}else{
										echo '-';
									} 
									?>
								</span>
							</td>
							<td class="ticket-booked-spaces">
								<span class="ticket_booked_spaces"><?php echo $EM_Ticket->get_booked_spaces(); ?></span>
							</td>
							<?php do_action('em_event_edit_ticket_td', $EM_Ticket); ?>
							<td class="ticket-actions">
								<a href="#" class="ticket-actions-edit"><?php _e('Edit','dbem'); ?></a> 
								<?php if( count($EM_Ticket->get_bookings()->bookings) == 0 ): ?>
								| <a href="<?php bloginfo('wpurl'); ?>/wp-load.php" class="ticket-actions-delete"><?php _e('Delete','dbem'); ?></a>
								<?php else: ?>
								| <a href="<?php echo EM_ADMIN_URL; ?>&amp;page=events-manager-bookings&ticket_id=<?php echo $EM_Ticket->ticket_id ?>"><?php _e('View Bookings','dbem'); ?></a>
								<?php endif; ?>
								<input type="hidden" class="ticket_id" name="em_tickets[<?php echo $count; ?>][ticket_id]" value="<?php echo $EM_Ticket->ticket_id ?>" />
								<input type="hidden" class="ticket_name" name="em_tickets[<?php echo $count; ?>][ticket_name]" value="<?php echo esc_attr(stripslashes($EM_Ticket->ticket_name)) ?>" />
								<input type="hidden" class="ticket_description" name="em_tickets[<?php echo $count; ?>][ticket_description]" value="<?php echo esc_attr(stripslashes($EM_Ticket->ticket_description)) ?>" />
								<input type="hidden" class="ticket_price" name="em_tickets[<?php echo $count; ?>][ticket_price]" value="<?php echo $EM_Ticket->ticket_price ?>" />
								<input type="hidden" class="ticket_spaces" name="em_tickets[<?php echo $count; ?>][ticket_spaces]" value="<?php echo $EM_Ticket->ticket_spaces ?>" />
								<input type="hidden" class="ticket_start" name="em_tickets[<?php echo $count; ?>][ticket_start]" value="<?php echo ( !empty($EM_Ticket->ticket_start) ) ? date("Y-m-d H:i", $EM_Ticket->start_timestamp):''; ?>" />
								<input type="hidden" class="ticket_end" name="em_tickets[<?php echo $count; ?>][ticket_end]" value="<?php echo ( !empty($EM_Ticket->ticket_end) ) ? date("Y-m-d H:i", $EM_Ticket->end_timestamp):''; ?>" />
								<input type="hidden" class="ticket_min" name="em_tickets[<?php echo $count; ?>][ticket_min]" value="<?php echo $EM_Ticket->ticket_min ?>" />
								<input type="hidden" class="ticket_max" name="em_tickets[<?php echo $count; ?>][ticket_max]" value="<?php echo $EM_Ticket->ticket_max ?>" />
								<?php do_action('em_event_edit_ticket_hidden', $EM_Ticket); ?>
							</td>
						</tr>
						<?php
						$count++;
					}
					if( !empty($delete_temp_ticket) ){
						array_pop($EM_Tickets->tickets);
					}
				?>
			</tbody>
		</table>
	<?php } ?>
</div>
<script type="text/javascript">
	jQuery(document).ready( function($) {
		//RSVP Warning
		$('#event-rsvp').click( function(event){
			if( !this.checked ){
				confirmation = confirm(EM.disable_bookings_warning);
				if( confirmation == false ){
					event.preventDefault();
				}else{
					$('#event-tickets').hide();
					$("div#rsvp-data").hide();
				}
			}else{
				$('#event-tickets').fadeIn();
				$("div#rsvp-data").fadeIn();
			}
		});
		  
		if($('input#event-rsvp').attr("checked")) {
			$("div#rsvp-data").fadeIn();
		} else {
			$("div#rsvp-data").hide();
		}
	});		
</script>
<?php
/*
 * REMEMBER TO INCLUDE THE OVERLAY IN THE FOOTER, USING:
 * em_locate_template('forms/tickets-form.php', true); //put here as it can't be in the add event form
 */