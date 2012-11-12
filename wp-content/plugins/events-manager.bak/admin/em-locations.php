<?php
/**
 * Looks at the request values, saves/updates and then displays the right menu in the admin
 * @return null
 */
function em_admin_locations_page() {  
	//TODO EM_Location is globalized, use it fully here
	global $EM_Location;
	//Take actions
	if( (!empty($_REQUEST['action']) && ( ($_REQUEST['action'] == "edit" && !empty($_REQUEST['location_id'])) || $_REQUEST['action'] == "add")) ) { 
		em_admin_location();
	} elseif( !empty($_REQUEST['action']) && $_REQUEST['action'] == "location_save" ) {
		em_admin_location();
	} else { 
		// no action, just a locations list
		em_admin_locations();
  	}
}  

function em_admin_locations($message='', $fill_fields = false) {
	global $EM_Notices;
	$limit = ( !empty($_REQUEST['limit']) ) ? $_REQUEST['limit'] : 20;//Default limit
	$page = ( !empty($_REQUEST['pno']) ) ? $_REQUEST['pno']:1;
	$offset = ( $page > 1 ) ? ($page-1)*$limit : 0;
	if( !empty($_REQUEST['owner']) && current_user_can('read_others_locations') ){
		$locations = EM_Locations::get(array('owner'=>false));
		$locations_mine_count = EM_Locations::count( array('owner'=>get_current_user_id()) );
		$locations_all_count = count($locations);
	}else{
		$locations = EM_Locations::get( array('owner'=>get_current_user_id(), 'blog'=>false) );
		$locations_mine_count = count($locations);
		$locations_all_count = current_user_can('read_others_locations') ? EM_Locations::count(array('blog'=>false)):0;
	}
	$locations_count = count($locations);
	?>
		<div class='wrap'>
			<div id='icon-edit' class='icon32'>
				<br/>
			</div>
 	 		<h2>
 	 			<?php _e('Locations', 'dbem'); ?>
 	 			<a href="admin.php?page=events-manager-locations&action=add" class="button add-new-h2"><?php _e('Add New', 'dbem') ?></a>
 	 		</h2>  

			<?php echo $EM_Notices; ?>  
			  
		 	 <form id='locations-filter' method='post' action=''>
				<input type='hidden' name='page' value='locations'/>
				<input type='hidden' name='limit' value='<?php echo $limit ?>' />	
				<input type='hidden' name='p' value='<?php echo $page ?>' />	
				<div class="subsubsub">
					<a href='admin.php?page=events-manager-locations' <?php echo (empty($_REQUEST['owner'])) ? 'class="current"':''; ?>><?php echo sprintf( __( 'My %s', 'dbem' ), __('Locations','dbem')); ?> <span class="count">(<?php echo $locations_mine_count; ?>)</span></a>
					<?php if( current_user_can('read_others_locations') ): ?>
					&nbsp;|&nbsp;
					<a href='admin.php?page=events-manager-locations&amp;owner=all' <?php echo (!empty($_REQUEST['owner'])) ? 'class="current"':''; ?>><?php echo sprintf( __( 'All %s', 'dbem' ), __('Locations','dbem')); ?> <span class="count">(<?php echo $locations_all_count; ?>)</span></a>
					<?php endif; ?>
				</div>										
				<?php if ( $locations_count > 0 ) : ?>
				<div class='tablenav'>					
					<div class="alignleft actions">
						<select name="action">
							<option value="" selected="selected"><?php _e ( 'Bulk Actions' ); ?></option>
							<option value="location_delete"><?php _e ( 'Delete selected','dbem' ); ?></option>
						</select> 
						<input type="submit" value="<?php _e ( 'Apply' ); ?>" id="doaction2" class="button-secondary action" /> 
					</div>
						<?php
						if ( $locations_count >= $limit ) {
							$locations_nav = em_admin_paginate( $locations_count, $limit, $page );
							echo $locations_nav;
						}
						?>
				</div>
				<table class='widefat'>
					<thead>
						<tr>
							<th class='manage-column column-cb check-column' scope='col'><input type='checkbox' class='select-all' value='1'/></th>
							<th><?php _e('Name', 'dbem') ?></th>
							<th><?php _e('Address', 'dbem') ?></th>
							<th><?php _e('Town', 'dbem') ?></th>  
							<th><?php _e('State', 'dbem') ?></th>    
							<th><?php _e('Country', 'dbem') ?></th>                
						</tr> 
					</thead>
					<tfoot>
						<tr>
							<th class='manage-column column-cb check-column' scope='col'><input type='checkbox' class='select-all' value='1'/></th>
							<th><?php _e('Name', 'dbem') ?></th>
							<th><?php _e('Address', 'dbem') ?></th>
							<th><?php _e('Town', 'dbem') ?></th>    
							<th><?php _e('State', 'dbem') ?></th>    
							<th><?php _e('Country', 'dbem') ?></th>     
						</tr>             
					</tfoot>
					<tbody>
						<?php $i = 1; ?>
						<?php foreach ($locations as $EM_Location) : ?>	
							<?php if( $i >= $offset && $i <= $offset+$limit ): ?>
								<tr>
									<td><input type='checkbox' class ='row-selector' value='<?php echo $EM_Location->id ?>' name='locations[]'/></td>
									<td><a href='admin.php?page=events-manager-locations&amp;action=edit&amp;location_id=<?php echo $EM_Location->id ?>'><?php echo $EM_Location->name ?></a></td>
									<td><?php echo $EM_Location->address ?></td>
									<td><?php echo $EM_Location->town ?></td>     
									<td><?php echo $EM_Location->state ?></td>  
									<td><?php echo $EM_Location->get_country() ?></td>                      
								</tr>
							<?php endif; ?>
							<?php $i++; ?> 
						<?php endforeach; ?>
					</tbody>
				</table>
				<?php else: ?>
				<p><?php _e('No venues have been inserted yet!', 'dbem') ?></p>
				<?php endif; ?>
			</form>
		</div>
  	<?php 
}

function em_admin_location($message = "") {
	global $EM_Location, $EM_Notices;
	//check that user can access this page
	if( is_object($EM_Location) && !$EM_Location->can_manage('edit_locations','edit_others_locations') ){
		?>
		<div class="wrap"><h2><?php _e('Unauthorized Access','dbem'); ?></h2><p><?php _e('You do not have the rights to manage this location.','dbem'); ?></p></div>
		<?php
		return false;
	}	
	if( empty($EM_Location) || !is_object($EM_Location) ){
		$title = __('Add location', 'dbem');
		$EM_Location = new EM_Location();
	}else{
		$title = __('Edit location', 'dbem');
	}
	$required = "<i>(".__('required','dbem').")</i>";
	echo $EM_Notices;
	?>
	<form enctype='multipart/form-data' name='editcat' id='location-form' method='post' action='admin.php?page=events-manager-locations' class='validate'>
		<input type='hidden' name='action' value='location_save' />
		<input type='hidden' name='_wpnonce' value='<?php echo wp_create_nonce('location_save'); ?>' />
		<input type='hidden' name='location_id' value='<?php echo $EM_Location->id ?>'/>
		<div class='wrap'>
			<div id='icon-edit' class='icon32'>
				<br/>
			</div>
			<h2><?php echo $title ?></h2>   
	 		
			<?php global $EM_Notices; echo $EM_Notices; ?>
			<div id='ajax-response'></div>
			
			<div id="poststuff" class="metabox-holder">
				<div id="post-body">
					<div id="post-body-content">
						<div id="location_name" class="stuffbox">
							<h3>
								<?php _e ( 'Location Name', 'dbem' ); ?>
							</h3>
							<div class="inside">
								<input name='location_name' id='location-name' type='text' value='<?php echo htmlspecialchars($EM_Location->name, ENT_QUOTES); ?>' size='40'  />
								<br />
								<?php _e('The name of the location', 'dbem') ?>
								<?php $slug_link = __('View Slug','dbem'); ?>
								<a href="#" id="location-slug-trigger"><?php echo $slug_link; ?></a>
								<script type="text/javascript">
									jQuery(document).ready(function($){
										$('#location-slug-trigger').click(function(){
											if( $(this).text() == '<?php echo $slug_link; ?>'){
												$('.location-slug').show(); 
												 $(this).text('<?php _e('Hide Slug','dbem'); ?>');
											}else{ 
												$('.location-slug').hide(); 
												 $(this).text('<?php echo $slug_link; ?>'); 
											}
										});
									});
								</script>
								<p class='location-slug' style="display:none">
									<?php echo sprintf(__('%s Slug','dbem'),__('Location','dbem')); ?>: <input type="text" name="location_slug" id="location-slug" value="<?php echo $EM_Location->slug; ?>" />
									<br />
									<?php _e ( 'The event slug. If the event slug already exists, a random number will be appended to the end.', 'dbem' )?>
								</p>
							</div>
						</div>
						<?php if( current_user_can('edit_others_locations') ): ?>
						<div id="location_owner" class="stuffbox">
							<h3>
								<?php _e ( 'Location Owner', 'dbem' ); ?>
							</h3>
							<div class="inside">
								<?php
									$location_owner = (!empty($EM_Location->id)) ? $EM_Location->owner:get_current_user_id();
									$user_args = array ('name' => 'location_owner', 'show_option_none' => __ ( "Select...", 'dbem' ), 'selected' => $location_owner );
									if( is_super_admin() || is_main_site() ){ $user_args['blog_id'] = false; }
									wp_dropdown_users ( $user_args );
								?>
							</div>
						</div>
						<?php endif; ?>
						<div id="location_coordinates" class="stuffbox" style='display: none;'>
							<h3>
								<?php _e ( 'Coordinates', 'dbem' ); ?>
							</h3>
							<div class="inside">
								<input id='location-latitude' name='location_latitude' type='text' value='<?php echo $EM_Location->latitude; ?>' size='15' />
								-
								<input id='location-longitude' name='location_longitude' type='text' value='<?php echo $EM_Location->longitude; ?>' size='15' />
							</div>
						</div>
						<div id="location_info" class="stuffbox">
							<h3>
								<?php _e ( 'Location', 'dbem' ); ?>
							</h3>
							<div class="inside">
								<p><?php _e("If you're using the Google Maps, the more detail you provide, the more accurate Google can be at finding your location. If your address isn't being found, please <a='http://maps.google.com'>try it on maps.google.com</a> by adding all the fields below seperated by commas.",'dbem')?></p>
								<div id="em-location-data">
									<table>
										<tr>
											<th><?php _e ( 'Address:', 'dbem' )?>&nbsp;</th>
											<td>
												<input id="location-address" type="text" name="location_address" value="<?php echo htmlspecialchars($EM_Location->address, ENT_QUOTES); ; ?>" /> <?php echo $required; ?>
											</td>
										</tr>
										<tr>
											<th><?php _e ( 'City/Town:', 'dbem' )?>&nbsp;</th>
											<td>
												<input id="location-town" type="text" name="location_town" value="<?php echo htmlspecialchars($EM_Location->town, ENT_QUOTES); ?>" /> <?php echo $required; ?>
											</td>
										</tr>
										<tr>
											<th><?php _e ( 'State/County:', 'dbem' )?>&nbsp;</th>
											<td>
												<input id="location-state" type="text" name="location_state" value="<?php echo htmlspecialchars($EM_Location->state, ENT_QUOTES); ?>" />
											</td>
										</tr>
										<tr>
											<th><?php _e ( 'Postcode:', 'dbem' )?>&nbsp;</th>
											<td>
												<input id="location-postcode" type="text" name="location_postcode" value="<?php echo htmlspecialchars($EM_Location->postcode, ENT_QUOTES); ?>" />
											</td>
										</tr>
										<tr>
											<th><?php _e ( 'Region:', 'dbem' )?>&nbsp;</th>
											<td>
												<input id="location-region" type="text" name="location_region" value="<?php echo htmlspecialchars($EM_Location->region, ENT_QUOTES); ?>" />
												<input id="location-region-wpnonce" type="hidden" value="<?php echo wp_create_nonce('search_regions'); ?>" />
											</td>
										</tr>
										<tr>
											<th><?php _e ( 'Country:', 'dbem' )?>&nbsp;</th>
											<td>
												<select id="location-country" name="location_country">
													<?php foreach(em_get_countries(__('none selected','dbem')) as $country_key => $country_name): ?>
													<option value="<?php echo $country_key; ?>" <?php echo ( $EM_Location->country === $country_key || ($EM_Location->country == '' && $EM_Location->id == '' && get_option('dbem_location_default_country')==$country_key) ) ? 'selected="selected"':''; ?>><?php echo $country_name; ?></option>
													<?php endforeach; ?>
												</select> <?php echo $required; ?>
											</td>
										</tr>
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
								</div>
							</div>
						</div>
								
						<div id="location_description" class="postbox">
							<h3>
								<?php _e ( 'Details', 'dbem' ); ?>
							</h3>
							<div class="inside">
								<div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="postarea">
									<?php the_editor($EM_Location->description ); ?>
								</div>
								<br />
								<?php _e ( 'Details about the location', 'dbem' )?>
							</div>
						</div>
									
						<div id="location_description" class="stuffbox">
							<h3>
								<?php _e ( 'Location image', 'dbem' ); ?>
							</h3>
							<div class="inside" style="padding:10px;">
									<?php if ($EM_Location->get_image_url() != '') : ?> 
										<img src='<?php echo $EM_Location->image_url; ?>' alt='<?php echo $EM_Location->name ?>'/>
									<?php else : ?> 
										<?php _e('No image uploaded for this location yet', 'dbem') ?>
									<?php endif; ?>
									<br /><br />
									<label for='location_image'><?php _e('Upload/change picture', 'dbem') ?></label> <input id='location-image' name='location_image' id='location_image' type='file' size='40' />
									<br />
									<label for='location_image_delete'><?php _e('Delete Image?', 'dbem') ?></label> <input id='location-image-delete' name='location_image_delete' id='location_image_delete' type='checkbox' value='1' />
							</div>
						</div>
						<?php do_action('em_admin_location_form_footer'); ?>
						
					</div>
				</div>
			</div>
			<p class='submit'><input type='submit' class='button-primary' name='submit' value='<?php _e('Update location', 'dbem') ?>' /></p>
		</div>
	</form>
	<?php
}

?>