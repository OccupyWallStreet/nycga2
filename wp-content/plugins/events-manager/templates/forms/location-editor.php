<?php
	global $EM_Location, $EM_Notices;

	//check that user can access this page
	if( is_object($EM_Location) && !$EM_Location->can_manage('edit_locations','edit_others_locations') ){
		?>
		<div class="wrap"><h2><?php _e('Unauthorized Access','dbem'); ?></h2><p><?php echo sprintf(__('You do not have the rights to manage this %s.','dbem'),__('location','dbem')); ?></p></div>
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
	<form enctype='multipart/form-data' name='editcat' id='location-form' method='post' action='' class='validate'>
		<input type='hidden' name='action' value='location_save' />
		<input type='hidden' name='_wpnonce' value='<?php echo wp_create_nonce('location_save'); ?>' />
		<input type='hidden' name='location_id' value='<?php echo $EM_Location->id ?>'/>
		<input type='hidden' id='location-latitude' name='location_latitude' value='<?php echo $EM_Location->latitude; ?>' size='15' />
		<input type='hidden' id='location-longitude' name='location_longitude' value='<?php echo $EM_Location->longitude; ?>' size='15' />
		
		<h3><?php echo $title ?></h3>   
 		
		<?php global $EM_Notices; echo $EM_Notices; ?>
		<?php do_action('em_front_location_form_header'); ?>
		<h4>
			<?php _e ( 'Location Name', 'dbem' ); ?>
		</h4>
		<div class="inside">
			<input name='location_name' id='location-name' type='text' value='<?php echo htmlspecialchars($EM_Location->name, ENT_QUOTES); ?>' size='40'  />
			<br />
			<?php _e('The name of the location', 'dbem') ?>
		</div>

		<h4>
			<?php _e ( 'Location', 'dbem' ); ?>
		</h4>
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

		<h4>
			<?php _e ( 'Details', 'dbem' ); ?>
		</h4>
		<div class="inside">
			<textarea name="content"><?php echo $EM_Location->description; ?></textarea>
			<?php _e ( 'Details about the location', 'dbem' )?>
		</div>

		<h4>
			<?php _e ( 'Location image', 'dbem' ); ?>
		</h4>
		<div class="inside" style="padding:10px;">
				<?php if ($EM_Location->image_url != '') : ?> 
					<img src='<?php echo $EM_Location->image_url; ?>' alt='<?php echo $EM_Location->name ?>'/>
				<?php else : ?> 
					<?php _e('No image uploaded for this location yet', 'dbem') ?>
				<?php endif; ?>
				<br /><br />
				<label for='location_image'><?php _e('Upload/change picture', 'dbem') ?></label> <input id='location-image' name='location_image' id='location_image' type='file' size='40' />
				<br />
				<label for='location_image_delete'><?php _e('Delete Image?', 'dbem') ?></label> <input id='location-image-delete' name='location_image_delete' id='location_image_delete' type='checkbox' value='1' />
		</div>
		<?php do_action('em_front_location_form_footer'); ?>
		<p class='submit'><input type='submit' class='button-primary' name='submit' value='<?php _e('Update location', 'dbem') ?>' /></p>
	</form>