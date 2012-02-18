<?php 
/**
 * Check if there's any admin-related actions to take for bookings. All actions are caught here.
 * @return null
 */
function em_admin_actions_bookings() {
  	global $dbem_form_add_message;   
	global $dbem_form_delete_message; 
	global $wpdb, $EM_Booking, $EM_Event, $EM_Notices;
	
	if( is_object($EM_Booking) && !empty($_REQUEST['action']) && $EM_Booking->can_manage('manage_bookings','manage_others_bookings') ) {
		if( $_REQUEST['action'] == 'bookings_add_note' ){
			$EM_Booking->add_note($_REQUEST['booking_note']);
			function em_booking_save_notification(){ global $EM_Booking; ?><div class="updated"><p><strong><?php echo $EM_Booking->feedback_message; ?></strong></p></div><?php }
			add_action ( 'admin_notices', 'em_booking_save_notification' );
		}
	}
	if( is_object($EM_Event) && !empty($_REQUEST['action']) ){
		if( $_REQUEST['action'] == 'bookings_export_csv' && wp_verify_nonce($_REQUEST['_wpnonce'],'bookings_export_csv') ){
			$EM_Event->get_bookings()->export_csv();
			exit();
		}
	}
}
add_action('admin_init','em_admin_actions_bookings',100);

/**
 * Decide what content to show in the bookings section. 
 */
function em_bookings_page(){
	global $action;
	//First any actions take priority
	if( !empty($_REQUEST['action']) && substr($_REQUEST['action'],0,7) != 'booking' ){ //actions not starting with booking_
		do_action('em_bookings_'.$action);
	}elseif( !empty($_REQUEST['booking_id']) ){
		em_bookings_single();
	}elseif( !empty($_REQUEST['person_id']) ){
		em_bookings_person();
	}elseif( !empty($_REQUEST['event_id']) ){
		em_bookings_event();
	}elseif( !empty($_REQUEST['ticket_id']) ){
		em_bookings_ticket();
	}else{
		em_bookings_dashboard();
	}
}

/**
 * Generates the bookings dashboard, showing information on all events 
 */
function em_bookings_dashboard(){
	global $EM_Notices;
	?>
	<div class='wrap'>
		<?php if( is_admin() ): ?>
		<div id='icon-users' class='icon32'>
			<br/>
		</div>
  		<h2>
  			<?php _e('Event Bookings Dashboard', 'dbem'); ?>
  		</h2>
  		<?php endif; ?>
  		<?php echo $EM_Notices; ?>
		<?php if( is_admin() ): ?>
		<div class="icon32" id="icon-bookings"><br></div>
		<?php endif; ?>
		<h2><?php _e('Recent Bookings','dbem'); ?></h2>	
  		<?php
		$EM_Bookings_Table = new EM_Bookings_Table();
		$EM_Bookings_Table->status = get_option('dbem_bookings_approval') ? 'needs-attention':'confirmed';
		$EM_Bookings_Table->output();
  		?>
  		<br class="clear" />
		<?php if( is_admin() ): ?>
		<div class="icon32" id="events"><br></div>
		<?php endif; ?>
		<h2><?php _e('Events With Bookings Enabled','dbem'); ?></h2>		
		<?php em_bookings_events_table(); ?>
		<?php do_action('em_bookings_dashboard'); ?>
	</div>
	<?php		
}

/**
 * Shows all booking data for a single event 
 */
function em_bookings_event(){
	global $EM_Event,$EM_Person,$EM_Notices;
	//check that user can access this page
	if( is_object($EM_Event) && !$EM_Event->can_manage('manage_bookings','manage_others_bookings') ){
		?>
		<div class="wrap"><h2><?php _e('Unauthorized Access','dbem'); ?></h2><p><?php _e('You do not have the rights to manage this event.','dbem'); ?></p></div>
		<?php
		return false;
	}
	$localised_start_date = date_i18n('D d M Y', $EM_Event->start);
	$localised_end_date = date_i18n('D d M Y', $EM_Event->end);
	?>
	<div class='wrap'>
		<div id='icon-users' class='icon32'>
			<br/>
		</div>
  		<h2>
  			<?php echo sprintf(__('Manage %s Bookings', 'dbem'), "'{$EM_Event->event_name}'"); ?>
  			<a href="<?php echo $EM_Event->output('#_EDITEVENTURL'); ?>" class="button add-new-h2"><?php _e('View/Edit Event','dbem') ?></a>
  			<?php do_action('em_admin_event_booking_options_buttons'); ?>
  		</h2>
  		<?php echo $EM_Notices; ?>
  		<div><a href='<?php echo EM_ADMIN_URL ."&amp;page=events-manager-bookings&action=bookings_export_csv&_wpnonce=".wp_create_nonce('bookings_export_csv')."&event_id=".$EM_Event->event_id ?>'><?php _e('export csv','dbem')?></a></div>  
		<div>
			<p><strong><?php _e('Event Name','dbem'); ?></strong> : <?php echo ($EM_Event->event_name); ?></p>
			<p><strong><?php _e('Availability','dbem'); ?></strong> : <?php echo $EM_Event->get_bookings()->get_booked_spaces() . '/'. $EM_Event->get_spaces() ." ". __('Spaces confirmed','dbem'); ?></p>
			<p>
				<strong><?php _e('Date','dbem'); ?></strong> : 
				<?php echo $localised_start_date; ?>
				<?php echo ($localised_end_date != $localised_start_date) ? " - $localised_end_date":'' ?>
				<?php echo substr ( $EM_Event->event_start_time, 0, 5 ) . " - " . substr ( $EM_Event->event_end_time, 0, 5 ); ?>							
			</p>
			<p>
				<strong><?php _e('Location','dbem'); ?></strong> :
				<a class="row-title" href="<?php echo admin_url(); ?>post.php?action=edit&amp;post=<?php echo $EM_Event->get_location()->post_id ?>"><?php echo ($EM_Event->get_location()->location_name); ?></a> 
			</p>
		</div>
		<div class="icon32" id="icon-bookings"><br></div>
		<h2><?php _e('Bookings','dbem'); ?></h2>
		<?php
		$EM_Bookings_Table = new EM_Bookings_Table();
		$EM_Bookings_Table->status = get_option('dbem_bookings_approval') ? 'needs-attention':'confirmed';
		$EM_Bookings_Table->output();
  		?>
		<?php do_action('em_bookings_event_footer', $EM_Event); ?>
	</div>
	<?php
}

/**
 * Shows a ticket view
 */
function em_bookings_ticket(){
	global $EM_Ticket,$EM_Notices;
	$EM_Event = $EM_Ticket->get_event();
	//check that user can access this page
	if( is_object($EM_Ticket) && !$EM_Ticket->can_manage() ){
		?>
		<div class="wrap"><h2><?php _e('Unauthorized Access','dbem'); ?></h2><p><?php _e('You do not have the rights to manage this ticket.','dbem'); ?></p></div>
		<?php
		return false;
	}
	?>
	<div class='wrap'>
		<div id='icon-users' class='icon32'>
			<br/>
		</div>
  		<h2>
  			<?php echo sprintf(__('Ticket for %s', 'dbem'), "'{$EM_Event->name}'"); ?>
  			<a href="<?php echo EM_ADMIN_URL; ?>&amp;page=events-manager-event&event_id=<?php echo $EM_Event->event_id; ?>" class="button add-new-h2"><?php _e('View/Edit Event','dbem') ?></a>
  			<a href="<?php echo EM_ADMIN_URL; ?>&amp;page=events-manager-bookings&event_id=<?php echo $EM_Event->event_id; ?>" class="button add-new-h2"><?php _e('View Event Bookings','dbem') ?></a>
  		</h2> 
  		<?php echo $EM_Notices; ?>
		<div>
			<table>
				<tr><td><?php echo __('Name','dbem'); ?></td><td></td><td><?php echo $EM_Ticket->ticket_name; ?></td></tr>
				<tr><td><?php echo __('Description','dbem'); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td></td><td><?php echo ($EM_Ticket->ticket_description) ? $EM_Ticket->ticket_description : '-'; ?></td></tr>
				<tr><td><?php echo __('Price','dbem'); ?></td><td></td><td><?php echo ($EM_Ticket->ticket_price) ? $EM_Ticket->ticket_price : '-'; ?></td></tr>
				<tr><td><?php echo __('Spaces','dbem'); ?></td><td></td><td><?php echo ($EM_Ticket->ticket_spaces) ? $EM_Ticket->ticket_spaces : '-'; ?></td></tr>
				<tr><td><?php echo __('Min','dbem'); ?></td><td></td><td><?php echo ($EM_Ticket->ticket_min) ? $EM_Ticket->ticket_min : '-'; ?></td></tr>
				<tr><td><?php echo __('Max','dbem'); ?></td><td></td><td><?php echo ($EM_Ticket->ticket_max) ? $EM_Ticket->ticket_max : '-'; ?></td></tr>
				<tr><td><?php echo __('Start','dbem'); ?></td><td></td><td><?php echo ($EM_Ticket->ticket_start) ? $EM_Ticket->ticket_start : '-'; ?></td></tr>
				<tr><td><?php echo __('End','dbem'); ?></td><td></td><td><?php echo ($EM_Ticket->ticket_end) ? $EM_Ticket->ticket_end : '-'; ?></td></tr>
				<?php do_action('em_booking_admin_ticket_row', $EM_Ticket); ?>
			</table>
		</div>
		<div class="icon32" id="icon-bookings"><br></div>
		<h2><?php _e('Bookings','dbem'); ?></h2>
		<?php
		$EM_Bookings_Table = new EM_Bookings_Table();
		$EM_Bookings_Table->status = get_option('dbem_bookings_approval') ? 'needs-attention':'confirmed';
		$EM_Bookings_Table->output();
  		?>
		<?php do_action('em_bookings_ticket_footer', $EM_Ticket); ?>
	</div>
	<?php	
}

/**
 * Shows a single booking for a single person. 
 */
function em_bookings_single(){
	global $EM_Booking, $EM_Notices;
	//check that user can access this page
	if( is_object($EM_Booking) && !$EM_Booking->can_manage() ){
		?>
		<div class="wrap"><h2><?php _e('Unauthorized Access','dbem'); ?></h2><p><?php _e('You do not have the rights to manage this event.','dbem'); ?></p></div>
		<?php
		return false;
	}
	?>
	<div class='wrap'>
		<div class="icon32" id="icon-bookings"><br></div>
  		<h2>
  			<?php _e('Edit Booking', 'dbem'); ?>
  		</h2>
  		<?php echo $EM_Notices; ?>
  		<div id="poststuff" class="metabox-holder">
	  		<div id="post-body">
				<div id="post-body-content">
					<div id="em-booking-details" class="stuffbox">
						<h3>
							<?php _e ( 'Event Details', 'dbem' ); ?>
						</h3>
						<div class="inside">
							<?php
							$EM_Event = $EM_Booking->get_event();
							$localised_start_date = date_i18n('D d M Y', $EM_Event->start);
							$localised_end_date = date_i18n('D d M Y', $EM_Event->end);
							?>
							<table>
								<tr><td><strong><?php _e('Name','dbem'); ?></strong></td><td><a class="row-title" href="<?php echo EM_ADMIN_URL; ?>&amp;page=events-manager-bookings&amp;event_id=<?php echo $EM_Event->event_id ?>"><?php echo ($EM_Event->event_name); ?></a></td></tr>
								<tr>
									<td><strong><?php _e('Date/Time','dbem'); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
									<td>
										<?php echo $localised_start_date; ?>
										<?php echo ($localised_end_date != $localised_start_date) ? " - $localised_end_date":'' ?>
										<?php echo substr ( $EM_Event->start_time, 0, 5 ) . " - " . substr ( $EM_Event->end_time, 0, 5 ); ?>
									</td>
								</tr>
							</table>
						</div>
					</div> 		
					<div id="em-booking-details" class="stuffbox">
						<h3>
							<?php _e ( 'Personal Details', 'dbem' ); ?>
						</h3>
						<div class="inside">
							<?php echo $EM_Booking->get_person()->display_summary(); ?>
						</div>
					</div> 	
					<div id="em-booking-details" class="stuffbox">
						<h3>
							<?php _e ( 'Booking Details', 'dbem' ); ?>
						</h3>
						<div class="inside">
							<?php
							$EM_Event = $EM_Booking->get_event();
							$localised_start_date = date_i18n('D d M Y', $EM_Event->start);
							$localised_end_date = date_i18n('D d M Y', $EM_Event->end);
							$shown_tickets = array();
							?>
							<p><strong><?php _e('Status','dbem'); ?> : </strong><?php echo $EM_Booking->get_status(); ?></p>
							<form action="" method="post">
								<table class="em-tickets-bookings-table" cellspacing="0" cellpadding="0">
									<thead>
									<tr>
										<th><?php _e('Ticket Type','dbem'); ?></th>
										<th><?php _e('Spaces','dbem'); ?></th>			
										<th><?php _e('Price','dbem'); ?></th>
									</tr>
									</thead>
									<tbody>
										<?php foreach($EM_Booking->get_tickets_bookings()->tickets_bookings as $EM_Ticket_Booking): ?>
										<tr>
											<td class="ticket-type"><a class="row-title" href="<?php echo EM_ADMIN_URL; ?>&amp;page=events-manager-bookings&amp;ticket_id=<?php echo $EM_Ticket_Booking->get_ticket()->ticket_id ?>"><?php echo $EM_Ticket_Booking->get_ticket()->ticket_name ?></a></td>
											<td>
												<input name="em_tickets[<?php echo $EM_Ticket_Booking->get_ticket()->ticket_id; ?>][spaces]" class="em-ticket-select" value="<?php echo $EM_Ticket_Booking->get_spaces(); ?>" />
											</td>
											<td><?php echo $EM_Ticket_Booking->get_price(true,true); ?></td>
										</tr>
										<?php $shown_tickets[] = $EM_Ticket_Booking->ticket_id; ?>
										<?php endforeach; ?>
										<?php if( count($shown_tickets) < count($EM_Event->get_bookings()->get_tickets()->tickets)): ?><tr>
											<?php foreach($EM_Event->get_bookings()->get_tickets()->tickets as $EM_Ticket): ?>
												<?php if( !in_array($EM_Ticket->ticket_id, $shown_tickets) ): ?>
												<tr>
													<td class="ticket-type"><a class="row-title" href="<?php echo EM_ADMIN_URL; ?>&amp;page=events-manager-bookings&amp;ticket_id=<?php echo $EM_Ticket->ticket_id ?>"><?php echo $EM_Ticket->ticket_name ?></a></td>
													<td>
														<input name="em_tickets[<?php echo $EM_Ticket->ticket_id; ?>][spaces]" class="em-ticket-select" value="0" />
													</td>
													<td><?php echo em_get_currency_symbol() ?>0.00</td>
												</tr>
												<?php endif; ?>
											<?php endforeach; ?>
										<?php endif; ?>
									</tbody>
									<tfoot>
										<tr>
											<th><?php _e('Totals','dbem'); ?></th>
											<th><?php echo $EM_Booking->get_spaces(); ?></th>
											<th><?php echo $EM_Booking->get_price(true, true); ?></th>
										</tr>
										<?php if( !get_option('dbem_bookings_tax_auto_add') && is_numeric(get_option('dbem_bookings_tax')) && get_option('dbem_bookings_tax') > 0  ): ?>
										<tr>
											<th><?php _e('Tax','dbem'); ?></th>
											<th><?php echo get_option('dbem_bookings_tax') ?>%</th>
											<th><?php echo em_get_currency_symbol().number_format($EM_Booking->get_price() * (get_option('dbem_bookings_tax')/100),2); ?></th>
										</tr>
										<tr>
											<th><?php _e('Total (inc. tax)','dbem'); ?></th>
											<th>&nbsp;</th>
											<th><?php echo em_get_currency_symbol().number_format($EM_Booking->get_price()* (1 + get_option('dbem_bookings_tax')/100),2); ?></th>
										</tr>
										<?php endif; ?>
									</tfoot>
								</table>
								<p>
									<input type="submit" class="em-booking-submit" id="em-booking-submit" value="<?php _e('Modify Booking', 'dbem'); ?>" />
								 	<input type='hidden' name='action' value='booking_save'/>
								 	<input type='hidden' name='booking_id' value='<?php echo $EM_Booking->booking_id; ?>'/>
								 	<input type='hidden' name='event_id' value='<?php echo $EM_Event->event_id; ?>'/>
								 	<input type='hidden' name='_wpnonce' value='<?php echo wp_create_nonce('booking_save_'.$EM_Booking->booking_id); ?>'/>
								 	<em><?php _e('<strong>Note:</strong> ticket availability not taken into account (i.e. you can overbook). Confirmation email is not resent automatically.','dbem'); ?></em>
								</p>
								<table cellspacing="0" cellpadding="0">
									<?php if( !get_option('em_booking_form_custom') ): ?>
									<tr><td><strong><?php _e('Comment','dbem'); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td><td><?php echo $EM_Booking->booking_comment; ?></td></tr>
									<?php foreach( $EM_Booking->get_custom() as $custom_option ){
										?><tr><td><strong><?php echo $custom_option['name'] ?></strong></td><td><?php echo esc_html($custom_option['value']); ?></td></tr><?php
									} ?>
									<?php else: do_action('em_bookings_single_custom',$EM_Booking); ?>
									<?php endif; ?>
								</table>
							</form>
						</div>
					</div>
					<div id="em-booking-notes" class="stuffbox">
						<h3>
							<?php _e ( 'Booking Notes', 'dbem' ); ?>
						</h3>
						<div class="inside">
							<p><?php _e('You can add private notes below for internal reference that only event managers will see.','dbem'); ?></p>
							<?php foreach( $EM_Booking->notes as $note ): 
								$user = new EM_Person($note['author']);
							?>
							<div>
								<?php echo date(get_option('date_format'), $note['timestamp']) .' - '. $user->get_name(); ?> <?php _e('wrote','dbem'); ?>: 
								<p style="background:#efefef; padding:5px;"><?php echo nl2br($note['note']); ?></p> 
							</div>
							<?php endforeach; ?>
							<form method="post" action="" style="padding:5px;">
								<textarea class="widefat" rows="5" name="booking_note"></textarea>
								<input type="hidden" name="action" value="bookings_add_note" />
								<input type="submit" value="Add Note" />
							</form>
						</div>
					</div> 
					<?php do_action('em_bookings_single_metabox_footer', $EM_Booking); ?> 
				</div>
			</div>
		</div>
		<br style="clear:both;" />
		<?php do_action('em_bookings_single_footer', $EM_Booking); ?>
	</div>
	<?php
	
}

/**
 * Shows all bookings made by one person.
 */
function em_bookings_person(){	
	global $EM_Person, $EM_Notices;
	$EM_Person->get_bookings();
	$has_booking = false;
	foreach($EM_Person->get_bookings() as $EM_Booking){
		if($EM_Booking->can_manage('manage_bookings','manage_others_bookings')){
			$has_booking = true;
		}
	}
	if( !$has_booking ){
		?>
		<div class="wrap"><h2><?php _e('Unauthorized Access','dbem'); ?></h2><p><?php _e('You do not have the rights to manage this event.','dbem'); ?></p></div>
		<?php
		return false;
	}
	?>
	<div class='wrap'>
		<div id='icon-users' class='icon32'>
			<br/>
		</div>
  		<h2>
  			<?php _e('Manage Person\'s Booking', 'dbem'); ?>
  			<?php if( current_user_can('edit_users') ) : ?>
  			<a href="user-edit.php?user_id=<?php echo $EM_Person->ID; ?>" class="button add-new-h2"><?php _e('Edit User','dbem') ?></a>
  			<?php endif; ?>
  		</h2>
  		<?php echo $EM_Notices; ?>
		<?php do_action('em_bookings_person_header'); ?>
  		<div id="poststuff" class="metabox-holder has-right-sidebar">
	  		<div id="post-body">
				<div id="post-body-content">
					<div id="event_name" class="stuffbox">
						<h3>
							<?php _e ( 'Personal Details', 'dbem' ); ?>
						</h3>
						<div class="inside">
							<?php echo $EM_Person->display_summary(); ?>
						</div>
					</div> 
				</div>
			</div>
		</div>
		<br style="clear:both;" />
		<?php do_action('em_bookings_person_body_1'); ?>
		<div class="icon32" id="icon-bookings"><br></div>
		<h2><?php _e('Past And Present Bookings','dbem'); ?></h2>
		<?php
		$EM_Bookings_Table = new EM_Bookings_Table();
		$EM_Bookings_Table->status = 'all';
		$EM_Bookings_Table->scope = 'all';
		$EM_Bookings_Table->output();
  		?>
		<?php do_action('em_bookings_person_footer', $EM_Person); ?>
	</div>
	<?php
}

function em_printable_booking_report() {
	global $EM_Event;
	//check that user can access this page
	if( isset($_GET['page']) && $_GET['page']=='events-manager-bookings' && isset($_GET['action']) && $_GET['action'] == 'bookings_report' && is_object($EM_Event)){
		if( is_object($EM_Event) && !$EM_Event->can_manage('edit_events','edit_others_events') ){
			?>
			<div class="wrap"><h2><?php _e('Unauthorized Access','dbem'); ?></h2><p><?php _e('You do not have the rights to manage this event.','dbem'); ?></p></div>
			<?php
			return false;
		}
		em_locate_template('templates/bookings-event-printable.php', true);
		die();
	}
} 
add_action('admin_init', 'em_printable_booking_report');
?>