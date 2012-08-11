<h4 class="ai1ec-section-title"><?php _e( 'Organizer contact info', AI1EC_PLUGIN_NAME ); ?></h4>
<table class="ai1ec-form">
	<tbody>
		<tr>
			<td class="ai1ec-first">
				<label for="ai1ec_contact_name">
					<?php _e( 'Contact name:', AI1EC_PLUGIN_NAME ); ?>
				</label>
			</td>
			<td>
				<input type="text" name="ai1ec_contact_name" id="ai1ec_contact_name" value="<?php echo $contact_name; ?>" />
			</td>
		</tr>
		<tr>
			<td>
				<label for="ai1ec_contact_phone">
					<?php _e( 'Phone:', AI1EC_PLUGIN_NAME ); ?>
				</label>
			</td>
			<td>
				<input type="text" name="ai1ec_contact_phone" id="ai1ec_contact_phone" value="<?php echo $contact_phone; ?>" />
			</td>
		</tr>
		<tr>
			<td>
				<label for="ai1ec_contact_email">
					<?php _e( 'E-mail:', AI1EC_PLUGIN_NAME ); ?>
				</label>
			</td>
			<td>
				<input type="text" name="ai1ec_contact_email" id="ai1ec_contact_email" value="<?php echo $contact_email; ?>" />
			</td>
		</tr>
	</tbody>
</table>
