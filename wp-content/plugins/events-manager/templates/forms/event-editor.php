<?php
	global $EM_Event, $current_user, $localised_date_formats, $EM_Notices, $bp;
	//check that user can access this page
	if( is_object($EM_Event) && !$EM_Event->can_manage('edit_events','edit_others_events') ){
		?>
		<div class="wrap"><h2><?php _e('Unauthorized Access','dbem'); ?></h2><p><?php echo sprintf(__('You do not have the rights to manage this %s.','dbem'),__('Event','dbem')); ?></p></div>
		<?php
		return false;
	}
	
	if( is_object($EM_Event) && $EM_Event->id > 0 ){
		if($EM_Event->is_recurring()){
			$title = __( "Reschedule", 'dbem' )." '{$EM_Event->name}'";
		}else{
			$title = __ ( "Edit Event", 'dbem' ) . " '" . $EM_Event->name . "'";
		}
	} else {
		$EM_Event = ( is_object($EM_Event) && get_class($EM_Event) == 'EM_Event') ? $EM_Event : new EM_Event();
		$title = __ ( "Insert New Event", 'dbem' );
		//Give a default location & category
		$default_cat = get_option('dbem_default_category');
		$default_loc = get_option('dbem_default_location');
		if( is_numeric($default_cat) && $default_cat > 0 && !empty($EM_Event->get_categories->categories) ){
			$EM_Category = new EM_Category($default_cat);
			$EM_Event->get_categories()->categories[] = $EM_Category;
		}
		if( is_numeric($default_loc) && $default_loc > 0 && ( empty($EM_Event->location->id) && empty($EM_Event->location->name) && empty($EM_Event->location->address) && empty($EM_Event->location->town) ) ){
			$EM_Event->location_id = $default_loc;
			$EM_Event->location = new EM_Location($default_loc);
		}
	}
	
	// change prefix according to event/recurrence
	$pref = "event_";	
	
	$locale_code = substr ( get_locale (), 0, 2 );
	$localised_date_format = $localised_date_formats[$locale_code];
	
	//FIXME time useage is very flimsy imho
	$hours_locale_regexp = "H:i";
	// Setting 12 hours format for those countries using it
	if (preg_match ( "/en|sk|zh|us|uk/", $locale_code ))
		$hours_locale_regexp = "h:iA";
	?>

	<?php echo $EM_Notices; ?>	
	<form id="event-form" method="post" action="">
		<div class="wrap">			
			<?php if ( count($EM_Event->warnings) > 0 ) : ?>
				<?php foreach($EM_Event->warnings as $warning): ?>
				<p class="warning"><?php echo $warning; ?></p>
				<?php endforeach; ?>
			<?php endif; ?>        
			
			<?php do_action('em_front_event_form_header'); ?>
			
			<h4 class="event-form-name"><?php _e ( 'Event Name', 'dbem' ); ?></h4>
			<div class="inside event-form-name">
				<input type="text" name="event_name" id="event-name" value="<?php echo htmlspecialchars($EM_Event->name,ENT_QUOTES); ?>" />
				<br />
				<?php _e ( 'The event name. Example: Birthday party', 'dbem' )?>
				<?php if( empty($EM_Event->group_id) ): ?>
					<?php 
					$user_groups = array();
					if( !empty($bp->groups) ){
						$group_data = groups_get_user_groups(get_current_user_id());
						foreach( $group_data['groups'] as $group_id ){
							if( groups_is_user_admin(get_current_user_id(), $group_id) ){
								$user_groups[] = groups_get_group( array('group_id'=>$group_id)); 
							}
						}
					} 
					?>
					<?php if( count($user_groups) > 0 ): ?>
					<p>
						<select name="group_id">
							<option value="<?php echo $BP_Group->id; ?>">Not a Group Event</option>
						<?php
						foreach($user_groups as $BP_Group){
							?>
							<option value="<?php echo $BP_Group->id; ?>"><?php echo $BP_Group->name; ?></option>
							<?php
						} 
						?>
						</select>
						<br />
						<?php _e ( 'Select a group you admin to attach this event to it. Note that all other admins of that group can modify the booking, and you will not be able to unattach the event without deleting it.', 'dbem' )?>
					</p>
					<?php endif; ?>
				<?php endif; ?>
			</div>
						
			<h4 class="event-form-when"><?php _e ( 'When', 'dbem' ); ?></h4>
			<div class="inside event-form-when">
				<div>
					<?php _e ( 'Starts on ', 'dbem' ); ?>					
					<input id="em-date-start-loc" type="text" />
					<input id="em-date-start" type="hidden" name="event_start_date" value="<?php echo $EM_Event->start_date ?>" />
					<?php _e('from','dbem'); ?>
					<input id="start-time" type="text" size="8" maxlength="8" name="event_start_time" value="<?php echo date( $hours_locale_regexp, strtotime($EM_Event->start_time) ); ?>" />
					<?php _e('to','dbem'); ?>
					<input id="end-time" type="text" size="8" maxlength="8" name="event_end_time" value="<?php echo date( $hours_locale_regexp, strtotime($EM_Event->end_time) ); ?>" />
					<?php _e('and ends on','dbem'); ?>
					<input id="em-date-end-loc" type="text" />
					<input id="em-date-end" type="hidden" name="event_end_date" value="<?php echo $EM_Event->end_date ?>" />
				</div>			
				<div>
					<span id='event-date-explanation'>
					<?php _e( 'This event spans every day between the beginning and end date, with start/end times applying to each day.', 'dbem' ); ?>
					</span>
					<span id='recurrence-dates-explanation'>
						<?php _e( 'For a recurring event, a one day event will be created on each recurring date within this date range.', 'dbem' ); ?>
					</span>
				</div> 
			</div>  
			<?php if( get_option('dbem_recurrence_enabled') && ($EM_Event->is_recurrence() || $EM_Event->is_recurring() || $EM_Event->id == '') ) : //for now we don't need to show recurrences for single saved events, as backend doesn't allow either ?>
				<!-- START recurrence postbox -->
				<div class="inside event-form-recurrence">
					<?php	
					$days_names = array (1 => __ ( 'Monday' ), 2 => __ ( 'Tuesday' ), 3 => __ ( 'Wednesday' ), 4 => __ ( 'Thursday' ), 5 => __ ( 'Friday' ), 6 => __ ( 'Saturday' ), 0 => __ ( 'Sunday' ) );
					
					if ( !$EM_Event->id || $EM_Event->is_recurring() ) : ?>
						<input id="event-recurrence" type="checkbox" name="repeated_event" value="1" <?php echo ( $EM_Event->is_recurring() ) ? 'checked="checked"':'' ; ?> />
						<?php _e ( 'This event repeats', 'dbem' ); ?> 
							<select id="recurrence-frequency" name="recurrence_freq">
								<?php
									$freq_options = array ("daily" => __ ( 'Daily', 'dbem' ), "weekly" => __ ( 'Weekly', 'dbem' ), "monthly" => __ ( 'Monthly', 'dbem' ) );
									em_option_items ( $freq_options, $EM_Event->freq ); 
								?>
							</select>
							<?php _e ( 'every', 'dbem' )?>
							<input id="recurrence-interval" name='recurrence_interval' size='2' value='<?php echo $EM_Event->interval ; ?>' />
							<span class='interval-desc' id="interval-daily-singular">
							<?php _e ( 'day', 'dbem' )?>
							</span> <span class='interval-desc' id="interval-daily-plural">
							<?php _e ( 'days', 'dbem' ) ?>
							</span> <span class='interval-desc' id="interval-weekly-singular">
							<?php _e ( 'week on', 'dbem'); ?>
							</span> <span class='interval-desc' id="interval-weekly-plural">
							<?php _e ( 'weeks on', 'dbem'); ?>
							</span> <span class='interval-desc' id="interval-monthly-singular">
							<?php _e ( 'month on the', 'dbem' )?>
							</span> <span class='interval-desc' id="interval-monthly-plural">
							<?php _e ( 'months on the', 'dbem' )?>
							</span> 
						<p class="alternate-selector" id="weekly-selector">
							<?php
								$saved_bydays = ($EM_Event->is_recurring()) ? explode ( ",", $EM_Event->byday ) : array(); 
								em_checkbox_items ( 'recurrence_bydays[]', $days_names, $saved_bydays ); 
							?>
						</p>
						<p class="alternate-selector" id="monthly-selector" style="display:inline;">
							<select id="monthly-modifier" name="recurrence_byweekno">
								<?php
									$weekno_options = array ("1" => __ ( 'first', 'dbem' ), '2' => __ ( 'second', 'dbem' ), '3' => __ ( 'third', 'dbem' ), '4' => __ ( 'fourth', 'dbem' ), '-1' => __ ( 'last', 'dbem' ) ); 
									em_option_items ( $weekno_options, $EM_Event->byweekno  ); 
								?>
							</select>
							<select id="recurrence-weekday" name="recurrence_byday">
								<?php em_option_items ( $days_names, $EM_Event->byday  ); ?>
							</select>
							<?php _e('of each month','dbem'); ?>
							&nbsp;
						</p>
						
						<p id="recurrence-tip">
							<?php _e ( 'Check if your event happens more than once according to a regular pattern', 'dbem' )?>
						</p>
					<?php elseif( $EM_Event->is_recurrence() ) : ?>
							<p>
								<?php echo $EM_Event->get_recurrence_description(); ?>
								<br />
								<a href="<?php bloginfo ( 'wpurl' )?>/wp-admin/admin.php?page=events-manager-event&amp;event_id=<?php echo $EM_Event->recurrence_id; ?>">
								<?php _e ( 'Reschedule', 'dbem' ); ?>
								</a>
								<input type="hidden" name="recurrence_id" value="<?php echo $EM_Event->recurrence_id; ?>" />
							</p>
					<?php else : ?>
						<p><?php _e ( 'This is\'t a recurrent event', 'dbem' ) ?></p>
					<?php endif; ?>
				</div>
				<!-- END recurrence postbox -->   
			<?php endif; ?>
			
			
			<h4 class="event-form-where"><?php _e ( 'Where', 'dbem' ); ?></h4>
			<div class="inside event-form-where">
				<div id="em-location-data" style="padding-right:20px; vertical-align:top;">
					<?php
						$args = array();
						$args['owner'] = current_user_can('read_others_locations') ? false:get_current_user_id(); 
						$locations = EM_Locations::get($args); 
					?>
					<?php  if( count($locations) > 0): ?>
					<select name="location_id" id='location-select-id' size="1">  
						<?php 
						foreach($locations as $location) {    
							$selected = "";  
							if( is_object($EM_Event->location) )  {
								if ($EM_Event->location->id == $location->id) 
									$selected = "selected='selected' ";
							}
					   		?>          
					    	<option value="<?php echo $location->id ?>" title="<?php echo "{$location->latitude},{$location->longitude}" ?>" <?php echo $selected ?>><?php echo $location->name; ?></option>
					    	<?php
						}
						?>
					</select>
					<?php endif; ?>
					<p><?php _e ( 'Choose from one of your locations', 'dbem' )?> <?php echo sprintf(__('or <a href="%s">add a new location</a>','dbem'),$bp->events->link . 'my-locations/add/'); ?></p>
				
					<?php if ( get_option ( 'dbem_gmap_is_active' ) ) : ?>
					<div id='em-map-404' style='width: 400px; vertical-align:middle; text-align: center;'>
						<p><em><?php _e ( 'Location not found', 'dbem' ); ?></em></p>
					</div>
					<div id='em-map' style='width: 400px; height: 300px; display: none;'></div>
					<?php endif; ?>
				</div>
			</div>
			
			<h4 class="event-form-details"><?php _e ( 'Details', 'dbem' ); ?></h4>
			<div class="inside event-form-details">
				<div>
					<textarea name="content" rows="10" style="width:100%"><?php echo $EM_Event->notes ?></textarea>
					<br />
					<?php _e ( 'Details about the event.', 'dbem' )?><?php _e ( 'HTML Allowed.', 'dbem' )?>
				</div>
				<div>
				<?php if(get_option('dbem_categories_enabled')) :?>
					<?php $categories = EM_Categories::get(array('orderby'=>'category_name')); ?>
					<?php if( count($categories) > 0 ): ?>
						<!-- START Categories -->
						<label for="event_categories[]"><?php _e ( 'Category:', 'dbem' ); ?></label>
						<select name="event_categories[]" multiple size="10">
							<?php
							foreach ( $categories as $EM_Category ){
								$selected = ($EM_Event->get_categories()->has($EM_Category->id)) ? "selected='selected'": ''; 
								?>
								<option value="<?php echo $EM_Category->id ?>" <?php echo $selected ?>>
								<?php echo $EM_Category->name ?>
								</option>
								<?php 
							}
							?>
						</select>						
						<!-- END Categories -->
					<?php endif; ?>
				<?php endif; ?>	
				</div>
			
				<?php if(get_option('dbem_attributes_enabled')) : ?>
					<?php
					$attributes = em_get_attributes();
					$has_depreciated = false;
					?>
					<?php if( count( $attributes['names'] ) > 0 ) : ?>
						<?php foreach( $attributes['names'] as $name) : ?>
						<div>
							<label for="em_attributes[<?php echo $name ?>]"><?php echo $name ?></label>
							<?php if( count($attributes['values'][$name]) > 0 ): ?>
							<select name="em_attributes[<?php echo $name ?>]">
								<?php foreach($attributes['values'][$name] as $attribute_val): ?>
									<?php if( array_key_exists($name, $EM_Event->attributes) && $EM_Event->attributes[$name]==$attribute_val ): ?>
										<option selected="selected"><?php echo $attribute_val; ?></option>
									<?php else: ?>
										<option><?php echo $attribute_val; ?></option>
									<?php endif; ?>
								<?php endforeach; ?>
							</select>
							<?php else: ?>
							<input type="text" name="em_attributes[<?php echo $name ?>]" value="<?php echo array_key_exists($name, $EM_Event->attributes) ? htmlspecialchars($EM_Event->attributes[$name], ENT_QUOTES):''; ?>" />
							<?php endif; ?>
						</div>
						<?php endforeach; ?>
					<?php endif; ?>
				<?php endif; ?>
			</div>
			
			<?php if( get_option('dbem_rsvp_enabled') && $EM_Event->can_manage('manage_bookings','manage_others_bookings') ) : ?>
				<!-- START Bookings -->
				<h4><span><?php _e('Bookings/Registration','dbem'); ?></span></h4>
				<div class="event-bookings event-form-bookings">
					<p>
						<input id="bookings-checkbox" name='event_rsvp' value='1' type='checkbox' <?php echo ($EM_Event->rsvp) ? 'checked="checked"' : ''; ?> />
						<?php _e ( 'Enable registration for this event', 'dbem' )?>
						<br />
						<a id='printable' href='<?php echo $EM_Event->output('#_BOOKINGSURL'); ?>'><?php _e('manage bookings','dbem')?></a> | 
						<a target='_blank' href='<?php echo get_bloginfo('wpurl') . "/wp-admin/admin.php?page=events-manager-bookings&action=bookings_report&event_id=".$EM_Event->id ?>'><?php _e('printable view','dbem')?></a> | 
						<a href='<?php echo get_bloginfo('wpurl') . "/wp-admin/admin.php?page=events-manager-bookings&action=export_csv&event_id=".$EM_Event->id ?>'><?php _e('export csv','dbem')?></a>
					</p>
					<div id='bookings-data' style="<?php echo ($EM_Event->rsvp) ? '':'display:none;' ?>">
						<!-- START Booking Ticket Details -->
						<div id="event-tickets">
							<?php
							//get tickets here and if there are none, create a blank ticket
							$EM_Tickets = $EM_Event->get_bookings()->get_tickets();
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
											$col_count = 1;
											foreach( $EM_Tickets->tickets as $EM_Ticket){
												?>
												<tr valign="top" id="em-tickets-row-<?php echo $col_count ?>" class="em-tickets-row">
													<td class="ticket-status"><span class="<?php echo ($EM_Ticket->is_available()) ? 'ticket_on':'ticket_off'; ?>"></span></td>								
													<td class="ticket-name"><span class="ticket_name"><?php echo wp_kses_data($EM_Ticket->name); ?></span><br /><span class="ticket_description"><?php echo wp_kses($EM_Ticket->description,$allowedposttags); ?></span></td>
													<td class="ticket-price">
														<span class="ticket_price"><?php echo ($EM_Ticket->price) ? $EM_Ticket->price : __('Free','dbem'); ?></span>
													</td>
													<td class="ticket-limit">
														<span class="ticket_min">
															<?php  echo ( !empty($EM_Ticket->min) ) ? $EM_Ticket->min:'-'; ?>
														</span> / 
														<span class="ticket_max"><?php echo ( !empty($EM_Ticket->max) ) ? $EM_Ticket->max:'-'; ?></span>
													</td>
													<td class="ticket-time">
														<span class="ticket_start"><?php echo ( !empty($EM_Ticket->start) ) ? date($localised_date_format, $EM_Ticket->start_timestamp):''; ?></span> -
														<span class="ticket_end"><?php echo ( !empty($EM_Ticket->end) ) ? date($localised_date_format, $EM_Ticket->end_timestamp):''; ?></span>
													</td>
													<td class="ticket-qty">
														<span class="ticket_available_spaces"><?php echo $EM_Ticket->get_available_spaces(); ?></span>/
														<span class="ticket_spaces">
															<?php 
															if( $EM_Ticket->get_spaces() ){
																echo $EM_Ticket->get_spaces();
																echo ($EM_Ticket->spaces_limit) ? '':'*';
															}else{
																echo '-';
															} 
															?>
														</span>
													</td>
													<td class="ticket-booked-spaces">
														<span class="ticket_booked_spaces"><?php echo $EM_Ticket->get_booked_spaces(); ?></span>
													</td>
													<td class="ticket-actions">
														<a href="#" class="ticket-actions-edit"><?php _e('Edit','dbem'); ?></a> 
														<?php if( count($EM_Ticket->get_bookings()->bookings) == 0 ): ?>
														| <a href="<?php bloginfo('wpurl'); ?>/wp-load.php" class="ticket-actions-delete"><?php _e('Delete','dbem'); ?></a>
														<?php else: ?>
														| <a href="<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=events-manager-bookings&ticket_id=<?php echo $EM_Ticket->id ?>"><?php _e('View Bookings','dbem'); ?></a>
														<?php endif; ?>
														<input type="hidden" class="ticket_id" name="em_tickets[<?php echo $col_count; ?>][ticket_id]" value="<?php echo $EM_Ticket->id ?>" />
														<input type="hidden" class="ticket_name" name="em_tickets[<?php echo $count; ?>][ticket_name]" value="<?php echo esc_attr(stripslashes($EM_Ticket->name)) ?>" />
														<input type="hidden" class="ticket_description" name="em_tickets[<?php echo $count; ?>][ticket_description]" value="<?php echo esc_attr(stripslashes($EM_Ticket->description)) ?>" />
														<input type="hidden" class="ticket_price" name="em_tickets[<?php echo $col_count; ?>][ticket_price]" value="<?php echo $EM_Ticket->price ?>" />
														<input type="hidden" class="ticket_spaces" name="em_tickets[<?php echo $col_count; ?>][ticket_spaces]" value="<?php echo $EM_Ticket->spaces ?>" />
														<input type="hidden" class="ticket_start" name="em_tickets[<?php echo $col_count; ?>][ticket_start]" value="<?php echo ( !empty($EM_Ticket->start) ) ? date("Y-m-d H:i", $EM_Ticket->start_timestamp):''; ?>" />
														<input type="hidden" class="ticket_end" name="em_tickets[<?php echo $col_count; ?>][ticket_end]" value="<?php echo ( !empty($EM_Ticket->end) ) ? date("Y-m-d H:i", $EM_Ticket->end_timestamp):''; ?>" />
														<input type="hidden" class="ticket_min" name="em_tickets[<?php echo $col_count; ?>][ticket_min]" value="<?php echo $EM_Ticket->min ?>" />
														<input type="hidden" class="ticket_max" name="em_tickets[<?php echo $col_count; ?>][ticket_max]" value="<?php echo $EM_Ticket->max ?>" />
													</td>
												</tr>
												<?php
												$col_count++;
											}
											if( !empty($delete_temp_ticket) ){
												array_pop($EM_Tickets->tickets);
											}
										?>
									</tbody>
								</table>
							<?php } ?>
						</div>		
						<!-- START Booking Ticket Details -->
					</div>
				</div>
				<!-- END Bookings -->
			<?php endif; ?>
			<?php do_action('em_front_event_form_footer'); ?>
		</div>
		<p class="submit">
			<input type="submit" name="events_update" value="<?php _e ( 'Submit Event', 'dbem' ); ?> &raquo;" />
		</p>
		<input type="hidden" name="event_id" value="<?php echo $EM_Event->id; ?>" />
		<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('wpnonce_event_save'); ?>" />
		<input type="hidden" name="action" value="event_save" />
	</form>
	<?php em_locate_template('forms/tickets-form.php', true); //put here as it can't be in the add event form ?>
	<script type="text/javascript">
		jQuery(document).ready( function($) {
			<?php if( $EM_Event->is_recurring() ): ?>
			//Recurrence Warnings
			$('#event_form').submit( function(event){
				confirmation = confirm('<?php _e('Are you sure you want to reschedule this recurring event? If you do this, you will lose all booking information and the old recurring events will be deleted.', 'dbem'); ?>');
				if( confirmation == false ){
					event.preventDefault();
				}
			});
			<?php endif; ?>
			//RSVP Warning
			$('#bookings-checkbox').click( function(event){
				if( !this.checked ){
					confirmation = confirm('<?php _e('Are you sure you want to disable bookings? If you do this and save, you will lose all previous bookings. If you wish to prevent further bookings, reduce the number of spaces available to the amount of bookings you currently have', 'dbem'); ?>');
					if( confirmation == false ){
						event.preventDefault();
					}else{
						$("div#bookings-data").hide();
					}
				}else{
					$("div#bookings-data").fadeIn();
				}
			});
			if($('input#bookings-checkbox').attr("checked")) {
				$("div#bookings-data").fadeIn();
			} else {
				$("div#bookings-data").hide();
			}
		});		
	</script>