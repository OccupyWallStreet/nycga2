<?php
global $EM_Location, $post;
$required = '*';
?>
<p><?php _e("If you're using the Google Maps, the more detail you provide, the more accurate Google can be at finding your location. If your address isn't being found, please <a='http://maps.google.com'>try it on maps.google.com</a> by adding all the fields below seperated by commas.",'dbem')?></p>
<div id="em-location-data">
	<table>
		<tr>
			<th><?php _e ( 'Address:', 'dbem' )?>&nbsp;</th>
			<td>
				<input id="location-address" type="text" name="location_address" value="<?php echo htmlspecialchars($EM_Location->location_address, ENT_QUOTES); ; ?>" /> <?php echo $required; ?>
			</td>
		</tr>
		<tr>
			<th><?php _e ( 'City/Town:', 'dbem' )?>&nbsp;</th>
			<td>
				<input id="location-town" type="text" name="location_town" value="<?php echo htmlspecialchars($EM_Location->location_town, ENT_QUOTES); ?>" /> <?php echo $required; ?>
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
				<input id="location-region-wpnonce" type="hidden" value="<?php echo wp_create_nonce('search_regions'); ?>" />
			</td>
		</tr>
		<tr>
			<th><?php _e ( 'Country:', 'dbem' )?>&nbsp;</th>
			<td>
				<select id="location-country" name="location_country">
					<?php foreach(em_get_countries(__('none selected','dbem')) as $country_key => $country_name): ?>
					<option value="<?php echo $country_key; ?>" <?php echo ( $EM_Location->location_country === $country_key || ($EM_Location->location_country == '' && $EM_Location->location_id == '' && get_option('dbem_location_default_country')==$country_key) ) ? 'selected="selected"':''; ?>><?php echo $country_name; ?></option>
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
	<div id="location_coordinates" style='display: none;'>
		<input id='location-latitude' name='location_latitude' type='text' value='<?php echo $EM_Location->location_latitude; ?>' size='15' />
		<input id='location-longitude' name='location_longitude' type='text' value='<?php echo $EM_Location->location_longitude; ?>' size='15' />
	</div>
</div>