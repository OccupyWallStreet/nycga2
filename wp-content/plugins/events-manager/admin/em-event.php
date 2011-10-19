<?php
/**
 * Generates Event Admin page, for adding and updating a single (or recurring) event.
 * @param $title
 * @return null
 */
function em_admin_event_page() {
	global $EM_Event, $current_user, $EM_Notices, $localised_date_formats;
	//$EM_Event->get_bookings()->get_tickets()->get_ticket_bookings(); print_r($EM_Event);die();
	
	//check that user can access this page
	if( is_object($EM_Event) && !$EM_Event->can_manage('edit_events','edit_others_events') ){
		?>
		<div class="wrap"><h2><?php _e('Unauthorized Access','dbem'); ?></h2><p><?php echo sprintf(__('You do not have the rights to manage this %s.','dbem'),__('Event','dbem')); ?></p></div>
		<?php
		return false;
	}elseif( !is_object($EM_Event) ){
		$EM_Event = new EM_Event();
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
		if( is_numeric($default_cat) && $default_cat > 0 ){
			$EM_Category = new EM_Category($default_cat);
			$EM_Event->get_categories()->categories[] = $EM_Category;
		}
		if( is_numeric($default_loc) && $default_loc > 0 && ( empty($EM_Event->location->id) && empty($EM_Event->location->name) && empty($EM_Event->location->address) && empty($EM_Event->location->town) ) ){
			$EM_Event->location_id = $default_loc;
			$EM_Event->location = new EM_Location($default_loc);
		}
	}
	
	$use_select_for_locations = get_option('dbem_use_select_for_locations');
	// change prefix according to event/recurrence
	$pref = "event_";	
	
	$locale_code = substr ( get_locale (), 0, 2 );
	$localised_date_format = $localised_date_formats[$locale_code];
	
	//FIXME time useage is very flimsy imho
	$hours_locale_regexp = "H:i";
	// Setting 12 hours format for those countries using it
	if (preg_match ( "/en|sk|zh|us|uk/", $locale_code ))
		$hours_locale_regexp = "h:iA";
	
	$days_names = array (1 => __ ( 'Mon' ), 2 => __ ( 'Tue' ), 3 => __ ( 'Wed' ), 4 => __ ( 'Thu' ), 5 => __ ( 'Fri' ), 6 => __ ( 'Sat' ), 0 => __ ( 'Sun' ) );
	$required = "<i>*</i>";
	?>
	<?php echo $EM_Notices; ?>

	<form id="event-form" method="post" action=""  enctype='multipart/form-data'>
		<div class="wrap">
			<div id="icon-events" class="icon32"><br /></div>
			<h2><?php echo $title; ?></h2>
			<?php if ( count($EM_Event->warnings) > 0 ) : ?>
				<?php foreach($EM_Event->warnings as $warning): ?>
				<p class="warning"><?php echo $warning; ?></p>
				<?php endforeach; ?>
			<?php endif; ?>             
			<div id="poststuff" class="metabox-holder has-right-sidebar">
				<!-- SIDEBAR -->
				<div id="side-info-column" class='inner-sidebar'>
					<div id='side-sortables'>
						<?php do_action('em_admin_event_form_side_header'); ?>       
						<?php if(get_option('dbem_recurrence_enabled')) : ?>
							<!-- START recurrence postbox -->
							<div class="postbox ">
								<div class="handlediv"><br />
								</div>
								<h3 class='hndle'><span>
									<?php _e ( "Recurrence", 'dbem' ); ?>
									</span></h3>
									<div class="inside">
									
									<?php if( $EM_Event->is_recurrence() ) : ?>
										<p>
											<?php echo $EM_Event->get_recurrence_description(); ?>
											<br />
											<a href="<?php bloginfo ( 'wpurl' )?>/wp-admin/admin.php?page=events-manager-event&amp;event_id=<?php echo $EM_Event->recurrence_id; ?>">
											<?php _e ( 'Reschedule', 'dbem' ); ?>
											</a>
											<input type="hidden" name="recurrence_id" value="<?php echo $EM_Event->recurrence_id; ?>" />
										</p>
									<?php //TODO add js warning if rescheduling, since all bookings are deleted ?>
									<?php else : ?>
										<p>
											<input id="event-recurrence" type="checkbox" name="repeated_event" value="1" <?php echo ( $EM_Event->is_recurring() ) ? 'checked="checked"':'' ; ?> />
											<?php _e ( 'Repeated event', 'dbem' ); ?>
										</p>
										<div id="event_recurrence_pattern">
											<p>
												Frequency:
												<select id="recurrence-frequency" name="recurrence_freq">
													<?php
														$freq_options = array ("daily" => __ ( 'Daily', 'dbem' ), "weekly" => __ ( 'Weekly', 'dbem' ), "monthly" => __ ( 'Monthly', 'dbem' ) );
														em_option_items ( $freq_options, $EM_Event->freq ); 
													?>
												</select>
											</p>
											<p>
												<?php _e ( 'Every', 'dbem' )?>
												<input id="recurrence-interval" name='recurrence_interval' size='2' value='<?php echo $EM_Event->interval ; ?>' />
												<span class='interval-desc' id="interval-daily-singular">
												<?php _e ( 'day', 'dbem' )?>
												</span> <span class='interval-desc' id="interval-daily-plural">
												<?php _e ( 'days', 'dbem' ) ?>
												</span> <span class='interval-desc' id="interval-weekly-singular">
												<?php _e ( 'week', 'dbem' )?>
												</span> <span class='interval-desc' id="interval-weekly-plural">
												<?php _e ( 'weeks', 'dbem' )?>
												</span> <span class='interval-desc' id="interval-monthly-singular">
												<?php _e ( 'month', 'dbem' )?>
												</span> <span class='interval-desc' id="interval-monthly-plural">
												<?php _e ( 'months', 'dbem' )?>
												</span> 
											</p>
											<p class="alternate-selector" id="weekly-selector">
												<?php
													$saved_bydays = ($EM_Event->is_recurring()) ? explode ( ",", $EM_Event->byday ) : array(); 
													em_checkbox_items ( 'recurrence_bydays[]', $days_names, $saved_bydays ); 
												?>
											</p>
											<p class="alternate-selector" id="monthly-selector">
												<?php _e ( 'Every', 'dbem' )?>
												<select id="monthly-modifier" name="recurrence_byweekno">
													<?php
														$weekno_options = array ("1" => __ ( 'first', 'dbem' ), '2' => __ ( 'second', 'dbem' ), '3' => __ ( 'third', 'dbem' ), '4' => __ ( 'fourth', 'dbem' ), '-1' => __ ( 'last', 'dbem' ) ); 
														em_option_items ( $weekno_options, $EM_Event->byweekno  ); 
													?>
												</select>
												<select id="recurrence-weekday" name="recurrence_byday">
													<?php em_option_items ( $days_names, $EM_Event->byday  ); ?>
												</select>
												&nbsp;
											</p>
										</div>
										<p id="recurrence-tip">
											<?php _e ( 'Check if your event happens more than once according to a regular pattern', 'dbem' )?>
										</p>
									<?php endif; ?>
								</div>
							</div> 
							<!-- END recurrence postbox -->   
						<?php endif; ?>   
						   
						<?php if ( current_user_can('edit_others_events') ): ?>
						<div class="postbox ">
							<div class="handlediv"><br />
							</div>
							<h3 class='hndle'><span><?php _e ( 'Event Owner/Contact Person', 'dbem' ); ?></span></h3>
							<div class="inside">
								<?php
									$event_owner = (empty($EM_Event->id)) ? $EM_Event->owner:get_current_user_id();
									$user_args = array ('name' => 'event_owner', 'show_option_none' => __ ( "Select...", 'dbem' ), 'selected' => $EM_Event->owner  );
									if( is_super_admin() || is_main_site() ){ $user_args['blog_id'] = false; }
									wp_dropdown_users ( apply_filters('em_event_owner_dropdown_users',$user_args) );
								?>
							</div>
						</div>
						<?php else: ?>
						<input type="hidden" name="event_owner" value="<?php get_current_user_id() ?>" />
						<?php endif; ?> 
						   
						<?php if( empty($EM_Event->id) ): ?>
							<?php 
							$user_groups = array();
							if( function_exists('groups_get_user_groups') ){
								$group_data = groups_get_user_groups(get_current_user_id());
								foreach( $group_data['groups'] as $group_id ){
									if( groups_is_user_admin(get_current_user_id(), $group_id) ){
										$user_groups[] = groups_get_group( array('group_id'=>$group_id)); 
									}
								}
							}
							?>
							<?php if( count($user_groups) > 0 ): ?>
							<!-- START RSVP -->
							<div class="postbox " id='group-data'>
								<div class="handlediv"><br />
								</div>
								<h3 class='hndle'><span><?php _e('Group Ownership','dbem'); ?></span></h3>
								<div class="inside">
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
										<em><?php _e ( 'Select a group you admin to attach this event to it. Note that all other admins of that group can modify the booking, and you will not be able to unattach the event without deleting it.', 'dbem' )?></em>
									</p>
								</div>
							</div>
							<?php endif; ?>
						<?php endif; ?>
						
						<?php if(get_option('dbem_rsvp_enabled')) : ?>
							<!-- START RSVP -->
							<div class="postbox " id='rsvp-data'>
								<div class="handlediv"><br />
								</div>
								<h3 class='hndle'><span><?php _e('Bookings Stats','dbem'); ?></span></h3>
								<div class="inside">
									<div>
										<!-- START RSVP Stats -->
										<?php
												$available_spaces = $EM_Event->get_bookings()->get_available_spaces();
												$booked_spaces = $EM_Event->get_bookings()->get_booked_spaces();
													
												if ( count($EM_Event->get_bookings()->bookings) > 0 ) {
													?>
													<div class='wrap'>
														<p><strong><?php echo __('Available Spaces','dbem').': '.$EM_Event->get_bookings()->get_available_spaces(); ?></strong></p>
														<p><strong><?php echo __('Confirmed Spaces','dbem').': '.$EM_Event->get_bookings()->get_booked_spaces(); ?></strong></p>
														<p><strong><?php echo __('Pending Spaces','dbem').': '.$EM_Event->get_bookings()->get_pending_spaces(); ?></strong></p>
												 	</div>
													 		
											 	    <br class='clear'/>
											 	    
											 	 	<div id='major-publishing-actions'>  
														<div id='publishing-action'> 
															<a id='printable' href='<?php echo get_bloginfo('wpurl') . "/wp-admin/admin.php?page=events-manager-bookings&event_id=".$EM_Event->id ?>'><?php _e('manage bookings','dbem')?></a><br />
															<a target='_blank' href='<?php echo get_bloginfo('wpurl') . "/wp-admin/admin.php?page=events-manager-bookings&action=bookings_report&event_id=".$EM_Event->id ?>'><?php _e('printable view','dbem')?></a>
															<a href='<?php echo get_bloginfo('wpurl') . "/wp-admin/admin.php?page=events-manager-bookings&action=export_csv&event_id=".$EM_Event->id ?>'><?php _e('export csv','dbem')?></a>
															<?php do_action('em_admin_event_booking_options'); ?>
															<br class='clear'/>             
												        </div>
														<br class='clear'/>    
													</div>
													<?php                                                     
												} else {
													?>
													<p><em><?php _e('No responses yet!', 'dbem')?></em></p>
													<?php
												} 
										?>
										<!-- END RSVP Stats -->
									</div>
								</div>
							</div>
							<!-- END RSVP -->
						<?php endif; ?>  
						<?php if(get_option('dbem_categories_enabled')) :?>
							<!-- START Categories -->
							<div class="postbox ">
								<div class="handlediv"><br />
								</div>
								<h3 class='hndle'><span>
									<?php _e ( 'Category', 'dbem' ); ?>
									</span></h3>
								<div class="inside">
									<?php $categories = EM_Categories::get(array('orderby'=>'category_name')); ?>
									<?php if( count($categories) > 0 ): ?>
										<p>
											<?php foreach( $categories as $EM_Category ):?>
											<label><input type="checkbox" name="event_categories[]" value="<?php echo $EM_Category->id; ?>" <?php if($EM_Event->get_categories()->has($EM_Category->id)) echo 'checked="checked"'; ?> /> <?php echo $EM_Category->name ?></label><br />			
											<?php endforeach; ?>
										</p>
									<?php else: ?>
										<p><?php sprintf(__('No categories available, <a href="%s">create one here first</a>','dbem'), get_bloginfo('wpurl').'/wp-admin/admin.php?page=events-manager-categories'); ?></p>
									<?php endif; ?>
								</div>
							</div> 
							<!-- END Categories -->
						<?php endif; ?>
						<?php do_action('em_admin_event_form_side_footer'); ?>
					</div>
				</div>
				<!-- END OF SIDEBAR -->
				<div id="post-body">
					<div id="post-body-content">
						<?php do_action('em_admin_event_form_header'); ?>
						<div id="event_name" class="stuffbox">
							<h3>
								<?php _e ( 'Name', 'dbem' ); ?>
							</h3>
							<div class="inside">
								<input type="text" name="event_name" id="event-name" value="<?php echo htmlspecialchars($EM_Event->name,ENT_QUOTES); ?>" /><?php echo $required; ?>
								<br />
								<?php _e ( 'The event name. Example: Birthday party', 'dbem' )?>
								<?php $slug_link = __('View Slug','dbem'); ?>
								<a href="#" id="event-slug-trigger"><?php echo $slug_link; ?></a>
								<script type="text/javascript">
									jQuery(document).ready(function($){
										$('#event-slug-trigger').click(function(){
											if( $(this).text() == '<?php echo $slug_link; ?>'){
												$('.event-slug').show(); 
												 $(this).text('<?php _e('Hide Slug','dbem'); ?>');
											}else{ 
												$('.event-slug').hide(); 
												 $(this).text('<?php echo $slug_link; ?>'); 
											}
										});
									});
								</script>
								<p class='event-slug' style="display:none">
									<?php _e('Event Slug','dbem'); ?>: <input type="text" name="event_slug" id="event-slug" value="<?php echo $EM_Event->slug; ?>" />
									<br />
									<?php _e ( 'The event slug. If the event slug already exists, a random number will be appended to the end.', 'dbem' )?>
								</p>
							</div>
						</div>
						<div id="event_start_date" class="stuffbox">
							<h3 id='event-date-title'><?php _e ( 'Event date', 'dbem' ); ?></h3>
							<h3 id='recurrence-dates-title'><?php _e ( 'Recurrence dates', 'dbem' ); ?></h3>
							<div class="inside">
								<input id="em-date-start-loc" type="text" />
								<input id="em-date-start" type="hidden" name="event_start_date" value="<?php echo $EM_Event->start_date ?>" />
								<input id="em-date-end-loc" type="text" />
								<input id="em-date-end" type="hidden" name="event_end_date" value="<?php echo $EM_Event->end_date ?>" />
								<br />
								<span id='event-date-explanation'>
								<?php
									_e ( 'The event date.', 'dbem' );
									/* Marcus Begin Edit */
									echo " ";
									_e ( 'When not reoccurring, this event spans between the beginning and end date.', 'dbem' );
									/* Marcus End Edit */
								?>
								</span>
								<span id='recurrence-dates-explanation'>
									<?php _e ( 'The recurrence beginning and end date.', 'dbem' ); ?>
								</span>
							</div>
						</div>
						<div id="event_end_day" class="stuffbox">
							<h3>
								<?php _e ( 'Event time', 'dbem' ); ?>
							</h3>
							<div class="inside">
								<input id="start-time" type="text" size="8" maxlength="8" name="event_start_time" value="<?php echo date( $hours_locale_regexp, strtotime($EM_Event->start_time) ); ?>" />
								-
								<input id="end-time" type="text" size="8" maxlength="8" name="event_end_time" value="<?php echo date( $hours_locale_regexp, strtotime($EM_Event->end_time) ); ?>" />
								<br />
								<?php _e ( 'The time of the event beginning and end', 'dbem' )?>. 
							</div>
						</div>
						<div id="location_coordinates" class="stuffbox" style='display: none;'>
							<h3>
								<?php _e ( 'Coordinates', 'dbem' ); ?>
							</h3>
							<div class="inside">
								<input id='location-latitude' name='location_latitude' type='text' value='<?php echo $EM_Event->get_location()->latitude; ?>' size='15' />
								-
								<input id='location-longitude' name='location_longitude' type='text' value='<?php echo $EM_Event->get_location()->longitude; ?>' size='15' />
							</div>
						</div>
						<div id="location_info" class="stuffbox">
							<h3>
								<?php _e ( 'Location', 'dbem' ); ?>
							</h3>
							<div class="inside">
								<div id="em-location-data">
									<table>
										<?php if($use_select_for_locations) : ?> 
										<tr>
											<th><?php _e('Location:','dbem') ?></th>
											<td> 
												<select name="location_id" id='location-select-id' size="1">  
													<?php 
													$locations = EM_Locations::get();
													foreach($locations as $location) {    
														$selected = "";  
														if ($EM_Event->get_location()->id == $location->id){ 
															$selected = "selected='selected' ";
														}
												   		?>          
												    	<option value="<?php echo $location->id ?>" title="<?php echo "{$location->latitude},{$location->longitude}" ?>" <?php echo $selected ?>><?php echo $location->name; ?></option>
												    	<?php
													}
													?>
												</select>
												<p><?php _e ( 'The name of the location where the event takes place. You can use the name of a venue, a square, etc', 'dbem' )?></p>
											</td>
										</tr>
										<?php else : ?>
										<tr>
											<th><?php _e ( 'Name:', 'dbem' )?></th>
											<td>
												<input id='location-id' name='location_id' type='hidden' value='<?php echo $EM_Event->get_location()->id; ?>' size='15' />
												<input id="location-name" type="text" name="location_name" value="<?php echo htmlspecialchars($EM_Event->location->name, ENT_QUOTES); ?>" /><?php echo $required; ?>													
			                            		<p><em><?php _e ( 'Create a location or start typing to search a previously created location.', 'dbem' )?></em></p>
			                            		<p id="em-location-reset" style="display:none;"><em><?php _e('You cannot edit saved locations here.', 'dbem'); ?> <a href="#"><?php _e('Reset this form to create a location.', 'dbem')?></a></em></p>
			                            	</td>
								 		</tr>
										<tr>
											<th><?php _e ( 'Address:', 'dbem' )?>&nbsp;</th>
											<td>
												<input id="location-address" type="text" name="location_address" value="<?php echo htmlspecialchars($EM_Event->location->address, ENT_QUOTES); ; ?>" /><?php echo $required; ?>
											</td>
										</tr>
										<tr>
											<th><?php _e ( 'City/Town:', 'dbem' )?>&nbsp;</th>
											<td>
												<input id="location-town" type="text" name="location_town" value="<?php echo htmlspecialchars($EM_Event->location->town, ENT_QUOTES); ?>" /><?php echo $required; ?>
												<input id="location-town-wpnonce" type="hidden" value="<?php echo wp_create_nonce('search_town'); ?>" />
											</td>
										</tr>
										<tr>
											<th><?php _e ( 'State/County:', 'dbem' )?>&nbsp;</th>
											<td>
												<input id="location-state" type="text" name="location_state" value="<?php echo htmlspecialchars($EM_Event->location->state, ENT_QUOTES); ?>" />
												<input id="location-state-wpnonce" type="hidden" value="<?php echo wp_create_nonce('search_states'); ?>" />
											</td>
										</tr>
										<tr>
											<th><?php _e ( 'Postcode:', 'dbem' )?>&nbsp;</th>
											<td>
												<input id="location-postcode" type="text" name="location_postcode" value="<?php echo htmlspecialchars($EM_Event->location->postcode, ENT_QUOTES); ?>" />
											</td>
										</tr>
										<tr>
											<th><?php _e ( 'Region:', 'dbem' )?>&nbsp;</th>
											<td>
												<input id="location-region" type="text" name="location_region" value="<?php echo htmlspecialchars($EM_Event->location->region, ENT_QUOTES); ?>" />
												<input id="location-region-wpnonce" type="hidden" value="<?php echo wp_create_nonce('search_regions'); ?>" />
											</td>
										</tr>
										<tr>
											<th><?php _e ( 'Country:', 'dbem' )?>&nbsp;</th>
											<td>
												<select id="location-country" name="location_country">
													<option value="0" <?php echo ( $EM_Event->location->country == '' && $EM_Event->location->id == '' && get_option('dbem_location_default_country') == '' ) ? 'selected="selected"':''; ?>><?php _e('none selected','dbem'); ?></option>
													<?php foreach(em_get_countries() as $country_key => $country_name): ?>
													<option value="<?php echo $country_key; ?>" <?php echo ( $EM_Event->location->country == $country_key || ($EM_Event->location->country == '' && $EM_Event->location->id == '' && get_option('dbem_location_default_country')==$country_key) ) ? 'selected="selected"':''; ?>><?php echo $country_name; ?></option>
													<?php endforeach; ?>
												</select><?php echo $required; ?>
												<!-- <p><em><?php _e('Filling this in first will allow you to quickly find previously filled states and regions for the country.','dbem'); ?></em></p> -->
											</td>
										</tr>
										<?php endif; ?>
									</table>
									
									<?php if ( get_option( 'dbem_gmap_is_active' ) ) : ?>
									<div style="width: 400px; height: 300px; float:left; ">
										<div id='em-map-404' style='width: 400px; height:300px; vertical-align:middle; text-align: center;'>
											<p><em><?php _e ( 'Location not found', 'dbem' ); ?></em></p>
										</div>
										<div id='em-map' style='width: 400px; height: 300px; display: none;'></div>
									</div>
									<?php endif; ?>
									<br style="clear:both;" />
								</div>
							</div>
						</div>
					<div id="event_notes" class="stuffbox">
						<h3>
							<?php _e ( 'Details', 'dbem' ); ?>
						</h3>
						<div class="inside">
							<div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="postarea">
								<?php the_editor($EM_Event->notes ); ?>
							</div>
							<br />
							<?php _e ( 'Details about the event', 'dbem' )?>
						</div>
					</div>
									
					<div id="event-image" class="stuffbox">
						<h3>
							<?php _e ( 'Event image', 'dbem' ); ?>
						</h3>
						<div class="inside" style="padding:10px;">
								<?php if ($EM_Event->get_image_url() != '') : ?> 
									<img src='<?php echo $EM_Event->image_url; ?>' alt='<?php echo $EM_Event->name ?>'/>
								<?php else : ?> 
									<?php _e('No image uploaded for this event yet', 'dbem') ?>
								<?php endif; ?>
								<br /><br />
								<label for='event_image'><?php _e('Upload/change picture', 'dbem') ?></label> <input id='event-image' name='event_image' id='event_image' type='file' size='40' />
								<br />
								<label for='event_image_delete'><?php _e('Delete Image?', 'dbem') ?></label> <input id='event-image-delete' name='event_image_delete' id='event_image_delete' type='checkbox' value='1' />
						</div>
					</div>
					
					<?php if(get_option('dbem_rsvp_enabled')) : ?>
					<div id="event-bookings" class="stuffbox">
						<h3><span><?php _e('Bookings/Registration','dbem'); ?></span></h3>
						<div class="inside">
							<div class="wrap">
								<div id="event-rsvp-box">
									<input id="event-rsvp" name='event_rsvp' value='1' type='checkbox' <?php echo ($EM_Event->rsvp) ? 'checked="checked"' : ''; ?> />
									&nbsp;&nbsp;
									<?php _e ( 'Enable registration for this event', 'dbem' )?>
								</div>
								<div id="event-tickets" style="<?php echo ($EM_Event->rsvp) ? '':'display:none;' ?>">
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
													$count = 1;
													foreach( $EM_Tickets->tickets as $EM_Ticket){
														?>
														<tr valign="top" id="em-tickets-row-<?php echo $count ?>" class="em-tickets-row">
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
																<input type="hidden" class="ticket_id" name="em_tickets[<?php echo $count; ?>][ticket_id]" value="<?php echo $EM_Ticket->id ?>" />
																<input type="hidden" class="ticket_name" name="em_tickets[<?php echo $count; ?>][ticket_name]" value="<?php echo esc_attr(stripslashes($EM_Ticket->name)) ?>" />
																<input type="hidden" class="ticket_description" name="em_tickets[<?php echo $count; ?>][ticket_description]" value="<?php echo esc_attr(stripslashes($EM_Ticket->description)) ?>" />
																<input type="hidden" class="ticket_price" name="em_tickets[<?php echo $count; ?>][ticket_price]" value="<?php echo $EM_Ticket->price ?>" />
																<input type="hidden" class="ticket_spaces" name="em_tickets[<?php echo $count; ?>][ticket_spaces]" value="<?php echo $EM_Ticket->spaces ?>" />
																<input type="hidden" class="ticket_start" name="em_tickets[<?php echo $count; ?>][ticket_start]" value="<?php echo ( !empty($EM_Ticket->start) ) ? date("Y-m-d H:i", $EM_Ticket->start_timestamp):''; ?>" />
																<input type="hidden" class="ticket_end" name="em_tickets[<?php echo $count; ?>][ticket_end]" value="<?php echo ( !empty($EM_Ticket->end) ) ? date("Y-m-d H:i", $EM_Ticket->end_timestamp):''; ?>" />
																<input type="hidden" class="ticket_min" name="em_tickets[<?php echo $count; ?>][ticket_min]" value="<?php echo $EM_Ticket->min ?>" />
																<input type="hidden" class="ticket_max" name="em_tickets[<?php echo $count; ?>][ticket_max]" value="<?php echo $EM_Ticket->max ?>" />
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
							</div>
						</div>
					</div>
					<?php endif; ?>
					
					<?php if(get_option('dbem_attributes_enabled')) : ?>
						<div id="event-attributes" class="stuffbox">
							<h3>
								<?php _e ( 'Attributes', 'dbem' ); ?>
							</h3>
							<div class="inside">
								<?php
								$attributes = em_get_attributes();
								$has_depreciated = false;
								?>
								<div class="wrap">
									<?php if( !empty($attributes['names']) && count( $attributes['names'] ) > 0 ) : ?>
										<table class="form-table">
											<thead>
												<tr valign="top">
													<td><strong>Attribute Name</strong></td>
													<td><strong>Value</strong></td>
												</tr>
											</thead> 
											<tbody id="mtm_body">
												<?php
												$count = 1;
												foreach( $attributes['names'] as $name){
													?>
													<tr valign="top" id="em_attribute_<?php echo $count ?>">
														<td scope="row"><?php echo $name ?></td>
														<td>
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
														</td>
													</tr>
													<?php
													$count++;
												}
												if($count == 1){
													?>
													<tr><td colspan="2"><?php echo sprintf(__("You don't have any custom attributes defined in any of your Events Manager template settings. Please add them the <a href='%s'>settings page</a>",'dbem'),get_bloginfo('wpurl')."/wp-admin/admin.php?page=events-manager-options"); ?></td></tr>
													<?php
												}
												?>
											</tbody>
										</table>
										<?php if( count(array_diff(array_keys($EM_Event->attributes), $attributes['names'])) > 0 ): ?>
										<p><strong><?php _e('Depreciated Attributes', 'dbem')?></strong></p>
										<p><em><?php _e("If you see any attributes under here, that means they're not used in Events Manager anymore. To add them, you need to add the custom attribute again to a formatting option in the settings page. To remove any of these depreciated attributes, give it a blank value and save.") ?></em></p>
										<table class="form-table">
											<thead>
												<tr valign="top">
													<td><strong>Attribute Name</strong></td>
													<td><strong>Value</strong></td>
												</tr>
											</thead> 
											<tbody id="mtm_body">
												<?php
												if( is_array($EM_Event->attributes) and count($EM_Event->attributes) > 0){
													foreach( $EM_Event->attributes as $name => $value){
														if( !in_array($name, $attributes['names']) ){
															?>
															<tr valign="top" id="em_attribute_<?php echo $count ?>">
																<td scope="row"><?php echo $name ?></td>
																<td>
																	<input type="text" name="em_attributes[<?php echo $name ?>]" value="<?php echo htmlspecialchars($value, ENT_QUOTES); ?>" />
																</td>
															</tr>
															<?php
															$count++;
														}
													}
												}
												?>
											</tbody>
										</table>
										<?php endif; ?>
									<?php else : ?>
										<p>
										<?php _e('In order to use attributes, you must define some in your templates, otherwise they\'ll never show. Go to Events > Settings to add attribute placeholders.', 'dbem'); ?>
										</p> 
										<script>
											jQuery(document).ready(function($){ $('#event_attributes').addClass('closed'); });
										</script>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<?php endif; ?>
						<?php do_action('em_admin_event_form_footer'); ?>
					</div>
					<p class="submit">
						<?php 
						if ( $EM_Event->is_recurring() ) {
							$recurrence_delete_confirm = __('WARNING! You are about to re-create all your recurrent events including erasing your old booking data! Are you sure you want to do this?','dbem');
							$js = 'onclick = "if( !confirm(\''. $recurrence_delete_confirm.'\') ){ return false; }"';
						}
						?>					
						<input type="submit" name="events_update" value="<?php _e ( 'Submit Event', 'dbem' ); ?> &raquo;" <?php if(!empty($js)) echo $js; ?> />						
					</p>
					<input type="hidden" name="p" value="<?php echo ( !empty($_REQUEST['pno']) ) ? $_REQUEST['pno']:''; ?>" /><a>
					<input type="hidden" name="scope" value="<?php echo ( !empty($_REQUEST['scope']) ) ? $_REQUEST['scope']:'' ?>" /></a>
					<input type="hidden" name="event_id" value="<?php echo $EM_Event->id; ?>" />
					<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('wpnonce_event_save'); ?>" />
					<input type="hidden" name="action" value="event_save" />
				</div>
			</div>
		</div>
	</form>
	<?php 
		if( !get_option('dbem_bookings_tickets_single') ){
			em_locate_template('forms/tickets-form.php', true); //put here as it can't be in the add event form
		} 
	?>
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
			<?php if( get_option('dbem_rsvp_enabled') ): ?>
			//RSVP Warning
			$('#event-rsvp').click( function(event){
				if( !this.checked ){
					confirmation = confirm('<?php _e('Are you sure you want to disable bookings? If you do this and save, you will lose all previous bookings. If you wish to prevent further bookings, reduce the number of spaces available to the amount of bookings you currently have', 'dbem'); ?>');
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
			<?php endif; ?>
		});		
	</script>
<?php
}
?>