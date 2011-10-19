<?php 
/* 
 * By modifying this in your theme folder within plugins/events-manager/templates/event-form.php, you can change the way the search form will look.
 * To ensure compatability, it is recommended you maintain class, id and form name attributes, unless you now what you're doing. 
 * You also must keep the _wpnonce hidden field in this form too.
 */
	global $EM_Event, $current_user, $localised_date_formats, $EM_Notices, $bp;
	//Success notice
	if( !empty($_REQUEST['successful']) ){
		echo get_option('dbem_events_anonymous_result_success');
		return false;
	}
	//check that user can access this page
	if( is_object($EM_Event) && !$EM_Event->can_manage('edit_events','edit_others_events') && !(!is_user_logged_in() && get_option('dbem_events_anonymous_submissions') && $EM_Event->owner == get_option('dbem_events_anonymous_user')) ){
		?>
		<div class="wrap"><h2><?php _e('Unauthorized Access','dbem'); ?></h2><p><?php echo sprintf(__('You do not have the rights to manage this %s.','dbem'),__('Event','dbem')); ?></p></div>
		<?php
		return false;
	}
	
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
					<?php if( get_option('dbem_use_select_for_locations') ): ?>
						<?php
							$args = array();
							$args['owner'] = current_user_can('read_others_locations') ? false:get_current_user_id();
							//if this is an anonymous form, then submit
							if( !is_user_logged_in() && get_option('dbem_events_anonymous_submissions') ){ 
								if( get_option('dbem_events_anonymous_locations_user') ){
									$args['owner'] = get_option('dbem_events_anonymous_locations_user');
								}elseif( get_option('dbem_events_anonymous_user') ){
									$args['owner'] = get_option('dbem_events_anonymous_user');
								}
							}  
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
						<?php if( is_user_logged_in() ): ?>
						<p><?php _e ( 'Choose from one of your locations', 'dbem' )?> <?php echo sprintf(__('or <a href="%s">add a new location</a>','dbem'),$bp->events->link . 'my-locations/add/'); ?></p>
						<?php endif; ?>
				
					<?php else: ?>
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
								<th><?php _e ( 'Name:' )?></th>
								<td>
									<input id='location-id' name='location_id' type='hidden' value='<?php echo $EM_Event->get_location()->id; ?>' size='15' />
									<input id="location-name" type="text" name="location_name" value="<?php echo htmlspecialchars($EM_Event->location->name, ENT_QUOTES); ?>" /><?php echo $required; ?>													
                            		<p><em><?php _e ( 'Create a location or start typing to search a previously created location.', 'dbem' )?></em></p>
                            		<p id="em-location-reset" style="display:none;"><em><?php _e('You cannot edit saved locations here.', 'dbem'); ?> <a href="#"><?php _e('Reset this form to create a location.', 'dbem')?></a></em></p>
                            	</td>
					 		</tr>
							<tr>
								<th><?php _e ( 'Address:' )?>&nbsp;</th>
								<td>
									<input id="location-address" type="text" name="location_address" value="<?php echo htmlspecialchars($EM_Event->location->address, ENT_QUOTES); ; ?>" /><?php echo $required; ?>
								</td>
							</tr>
							<tr>
								<th><?php _e ( 'City/Town:' )?>&nbsp;</th>
								<td>
									<input id="location-town" type="text" name="location_town" value="<?php echo htmlspecialchars($EM_Event->location->town, ENT_QUOTES); ?>" /><?php echo $required; ?>
									<input id="location-town-wpnonce" type="hidden" value="<?php echo wp_create_nonce('search_town'); ?>" />
								</td>
							</tr>
							<tr>
								<th><?php _e ( 'State/County:' )?>&nbsp;</th>
								<td>
									<input id="location-state" type="text" name="location_state" value="<?php echo htmlspecialchars($EM_Event->location->state, ENT_QUOTES); ?>" />
									<input id="location-state-wpnonce" type="hidden" value="<?php echo wp_create_nonce('search_states'); ?>" />
								</td>
							</tr>
							<tr>
								<th><?php _e ( 'Postcode:' )?>&nbsp;</th>
								<td>
									<input id="location-postcode" type="text" name="location_postcode" value="<?php echo htmlspecialchars($EM_Event->location->postcode, ENT_QUOTES); ?>" />
								</td>
							</tr>
							<tr>
								<th><?php _e ( 'Region:' )?>&nbsp;</th>
								<td>
									<input id="location-region" type="text" name="location_region" value="<?php echo htmlspecialchars($EM_Event->location->region, ENT_QUOTES); ?>" />
									<input id="location-region-wpnonce" type="hidden" value="<?php echo wp_create_nonce('search_regions'); ?>" />
								</td>
							</tr>
							<tr>
								<th><?php _e ( 'Country:' )?>&nbsp;</th>
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
						<?php if ( get_option ( 'dbem_gmap_is_active' ) ) : ?>
						<div style="width: 400px; height: 300px; float:left;">
							<div id='em-map-404' style='width: 400px; height:300px; vertical-align:middle; text-align: center;'>
								<p><em><?php _e ( 'Location not found', 'dbem' ); ?></em></p>
							</div>
							<div id='em-map' style='width: 400px; height: 300px; display: none;'></div>
						</div>
						<?php endif; ?>
						<br style="clear:both; " />
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
								<option><?php echo __('No Value','dbem'); ?></option>
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
			
			<?php do_action('em_front_event_form_footer'); ?>
		</div>
		<p class="submit">
			<input type="submit" name="events_update" value="<?php _e ( 'Submit Event', 'dbem' ); ?> &raquo;" />
		</p>
		<input type="hidden" name="event_id" value="<?php echo $EM_Event->id; ?>" />
		<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('wpnonce_event_save'); ?>" />
		<input type="hidden" name="action" value="event_save" />
		<input type="hidden" name="redirect_to" value="<?php echo em_add_get_params($_SERVER['REQUEST_URI'], array('successful'=>'1')); ?>" />
	</form>