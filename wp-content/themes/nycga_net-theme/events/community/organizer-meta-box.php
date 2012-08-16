<?php

// Don't load directly
if ( !defined( 'ABSPATH' ) ) {
	die('-1');
}

/**
 * The Organizer Meta Box
 * You can customize this view by putting a replacement file of the same name (delete.php) in the events/community/ directory of your theme.
 *
 * @package TribeCommunityEvents
 * @since  1.0
 * @author Modern Tribe Inc.
 */

?>

<tr class="organizer">
	<td><label <?php if (isset($tribe_organizer_id) && $tribe_organizer_id && $_POST && empty($_OrganizerOrganizer)) echo 'class="error"'; ?>><?php _e( 'Organizer Name' , 'tribe-events-community' ); ?>:</label></td>
	<td>
		<input tabindex="<?php $this->tabIndex(); ?>" type='text' name='organizer[Organizer]' size='25'  value='<?php echo isset( $_OrganizerOrganizer ) ? esc_attr( $_OrganizerOrganizer ) : ''; ?>' />
	</td>
</tr>
<tr class="organizer">
	<td><label><?php _e( 'Phone' , 'tribe-events-community' ); ?>:</label></td>
	<td><input tabindex="<?php $this->tabIndex(); ?>" type='text' id='OrganizerPhone' name='organizer[Phone]' size='25' value='<?php echo isset( $_OrganizerPhone ) ? esc_attr( $_OrganizerPhone ) : ''; ?>' /></td>
</tr>
<tr class="organizer">
	<td><label><?php _e( 'Website' , 'tribe-events-community' ); ?>:</label></td>
	<td><input tabindex="<?php $this->tabIndex(); ?>" type='text' id='OrganizerWebsite' name='organizer[Website]' size='25' value='<?php echo isset( $_OrganizerWebsite ) ? esc_attr( $_OrganizerWebsite ) : ''; ?>' /></td>
</tr>
<tr class="organizer">
	<td><label><?php _e( 'Email' , 'tribe-events-community' ); ?>:</label></td>
	<td><input tabindex="<?php $this->tabIndex(); ?>" type='text' id='OrganizerEmail' name='organizer[Email]' size='25' value='<?php echo isset( $_OrganizerEmail ) ? esc_attr( $_OrganizerEmail ) : ''; ?>' /></td>
</tr>