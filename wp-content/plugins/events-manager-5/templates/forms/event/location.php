<?php
global $EM_Event;
$required = "<i>*</i>";
?>
<?php if( !get_option('dbem_require_location') && !get_option('dbem_use_select_for_locations') ): ?>
<div>
	<p>
		<input type="checkbox" name="no_location" id="no-location" value="1" <?php if( !empty($EM_Event->event_id) && ($EM_Event->location_id === '0' || $EM_Event->location_id === 0) ) echo 'checked="checked"'; ?>>
		<?php _e('This event does not have a physical location.','dbem'); ?>
	</p>
	<script type="text/javascript">
		jQuery(document).ready(function($){
			$('#no-location').change(function(){
				if( $('#no-location').is(':checked') ){
					$('#em-location-data').hide();
				}else{
					$('#em-location-data').show();
				}
			}).trigger('change');
		});
	</script>
</div>
<?php endif; ?>
<div id="em-location-data">
	<div id="location_coordinates" style='display: none;'>
		<input id='location-latitude' name='location_latitude' type='text' value='<?php echo $EM_Event->get_location()->location_latitude; ?>' size='15' />
		<input id='location-longitude' name='location_longitude' type='text' value='<?php echo $EM_Event->get_location()->location_longitude; ?>' size='15' />
	</div>
	<?php if( get_option('dbem_use_select_for_locations') || !$EM_Event->can_manage('edit_locations','edit_others_locations') ) : ?> 
	<table class="em-location-data">
		<tr>
			<th><?php _e('Location:','dbem') ?> </th>
			<td> 
				<select name="location_id" id='location-select-id' size="1">  
					<?php if(!get_option('dbem_require_location',true)): ?><option value="0"><?php _e('No Location'); ?></option><?php endif; ?>
					<?php 
					$locations = EM_Locations::get(array('blog'=>false));
					$selected_location = !empty($EM_Event->location_id) ? $EM_Event->location_id:get_option('dbem_default_location');
					foreach($locations as $EM_Location) {
						$selected = ($selected_location == $EM_Location->location_id) ? "selected='selected' " : '';
				   		?>          
				    	<option value="<?php echo $EM_Location->location_id ?>" title="<?php echo "{$EM_Location->location_latitude},{$EM_Location->location_longitude}" ?>" <?php echo $selected ?>><?php echo $EM_Location->location_name; ?></option>
				    	<?php
					}
					?>
				</select>
			</td>
		</tr>
	</table>
	<?php else : ?>
	<table class="em-location-data">
		<?php 
			global $EM_Location;
			if( $EM_Event->location_id !== 0 ){
				$EM_Location = $EM_Event->get_location();
			}elseif(get_option('dbem_default_location') > 0){
				$EM_Location = new EM_Location(get_option('dbem_default_location'));
			}
		?>
		<tr>
			<th><?php _e ( 'Location Name:', 'dbem' )?></th>
			<td>
				<input id='location-id' name='location_id' type='hidden' value='<?php echo $EM_Location->location_id; ?>' size='15' />
				<input id="location-name" type="text" name="location_name" value="<?php echo htmlspecialchars($EM_Location->location_name, ENT_QUOTES); ?>" /><?php echo $required; ?>													
				<br /><em><?php _e ( 'Create a location or start typing to search a previously created location.', 'dbem' )?></em>
				<p id="em-location-reset" style="display:none;"><em><?php _e('You cannot edit saved locations here.', 'dbem'); ?> <a href="#"><?php _e('Reset this form to create a location.', 'dbem')?></a></em></p>
			</td>
 		</tr>
		<tr>
			<th><?php _e ( 'Address:', 'dbem' )?>&nbsp;</th>
			<td>
				<input id="location-address" type="text" name="location_address" value="<?php echo htmlspecialchars($EM_Location->location_address, ENT_QUOTES); ; ?>" /><?php echo $required; ?>
			</td>
		</tr>
		<tr>
			<th><?php _e ( 'City/Town:', 'dbem' )?>&nbsp;</th>
			<td>
				<input id="location-town" type="text" name="location_town" value="<?php echo htmlspecialchars($EM_Location->location_town, ENT_QUOTES); ?>" /><?php echo $required; ?>
			</td>
		</tr>
		<tr>
			<th><?php _e ( 'State/County:', 'dbem' )?>&nbsp;</th>
			<td>
				<input id="location-state" type="text" name="location_state" value="<?php echo htmlspecialchars($EM_Location->location_state, ENT_QUOTES); ?>" />
			</td>
		</tr>
		<tr>
			<th><?php _e ( 'Postcode:', 'dbem' )?>&nbsp;</th>
			<td>
				<input id="location-postcode" type="text" name="location_postcode" value="<?php echo htmlspecialchars($EM_Location->location_postcode, ENT_QUOTES); ?>" />
			</td>
		</tr>
		<tr>
			<th><?php _e ( 'Region:', 'dbem' )?>&nbsp;</th>
			<td>
				<input id="location-region" type="text" name="location_region" value="<?php echo htmlspecialchars($EM_Location->location_region, ENT_QUOTES); ?>" />
			</td>
		</tr>
		<tr>
			<th><?php _e ( 'Country:', 'dbem' )?>&nbsp;</th>
			<td>
				<select id="location-country" name="location_country">
					<option value="0" <?php echo ( $EM_Location->location_country == '' && $EM_Location->location_id == '' && get_option('dbem_location_default_country') == '' ) ? 'selected="selected"':''; ?>><?php _e('none selected','dbem'); ?></option>
					<?php foreach(em_get_countries() as $country_key => $country_name): ?>
					<option value="<?php echo $country_key; ?>" <?php echo ( $EM_Location->location_country == $country_key || ($EM_Location->location_country == '' && $EM_Location->location_id == '' && get_option('dbem_location_default_country')==$country_key) ) ? 'selected="selected"':''; ?>><?php echo $country_name; ?></option>
					<?php endforeach; ?>
				</select><?php echo $required; ?>
			</td>
		</tr>
	</table>
	<?php endif; ?>
	<?php if ( get_option( 'dbem_gmap_is_active' ) ) : ?>
	<div class="em-location-map-container">
		<div id='em-map-404'  class="em-location-map-404">
			<p><em><?php _e ( 'Location not found', 'dbem' ); ?></em></p>
		</div>
		<div id='em-map' class="em-location-map-content" style='display: none;'></div>
	</div>
	<?php endif; ?>
	<br style="clear:both;" />
</div>