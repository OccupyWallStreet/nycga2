<?php

// Don't load directly
if ( !defined( 'ABSPATH' ) ) {
	die('-1');
}

/**
 * The Venue Meta Box
 * You can customize this view by putting a replacement file of the same name (delete.php) in the events/community/ directory of your theme.
 *
 * @package TribeCommunityEvents
 * @since  1.0
 * @author Modern Tribe Inc.
 */

if (empty($post->post_type) || $post->post_type != TribeEvents::VENUE_POST_TYPE) :
?>
   <tr class="venue">
      <td><?php _e( 'Venue Name:', 'tribe-events-community' ); ?></td>
      <td>
         <input tabindex="<?php $this->tabIndex(); ?>" type='text' name='venue[Venue]' size='25'  value='<?php if ( isset( $_VenueVenue ) ) echo esc_attr( $_VenueVenue ); ?>' />
      </td>
   </tr>
<?php endif; ?>

<tr class="venue">
	<td><?php _e( 'Address:', 'tribe-events-community' ); ?></td>
	<td><input tabindex="<?php $this->tabIndex(); ?>" type='text' name='venue[Address]' size='25' value='<?php if ( isset( $_VenueAddress ) ) echo esc_attr( $_VenueAddress ); ?>' /></td>
</tr>
<tr class="venue">
	<td><?php _e( 'City:', 'tribe-events-community' ); ?></td>
	<td><input tabindex="<?php $this->tabIndex(); ?>" type='text' name='venue[City]' size='25' value='<?php if ( isset( $_VenueCity ) )  echo esc_attr( $_VenueCity ); ?>' /></td>
</tr>
<tr class="venue">
	<td><?php _e( 'Country:', 'tribe-events-community' ); ?></td>
	<td>
		<select class="chosen" tabindex="<?php $this->tabIndex(); ?>" name='venue[Country]' id="EventCountry">
			<?php
			$countries = TribeEventsViewHelpers::constructCountries( $postId );
			// $defaultCountry = tribe_get_option('defaultCountry');
			if ( isset( $_VenueCountry ) && $_VenueCountry ) {
				$current = $_VenueCountry;
			} elseif ( isset( $defaultCountry[1] ) && tribe_get_option( 'defaultValueReplace' ) ) {
				$current = $defaultCountry[1];
			} else {
				$current = null;
			}

			foreach ( $countries as $abbr => $fullname ) {
				echo '<option value="' . esc_attr( $fullname ) . '" ';

				if($abbr == '')
					echo "disabled='disabled' ";

				selected( $current == $fullname );
				echo '>' . esc_html( $fullname ) . '</option>';
			}
			?>
		</select>
	</td>
</tr>
<tr class="venue">
	<?php if ( !isset( $_VenueStateProvince ) || $_VenueStateProvince == '' ) $_VenueStateProvince = -1; ?>
	<td><?php _e( 'State or Province:', 'tribe-events-community' ); ?></td>
	<td><input tabindex="<?php $this->tabIndex(); ?>" id="StateProvinceText" name="venue[Province]" type='text' name='' size='25' value='<?php echo ( isset( $_VenueStateProvince ) && $_VenueStateProvince != '' && $_VenueStateProvince != -1 ) ? esc_attr( $_VenueProvince ) : ''; ?>' />
	<select class="chosen" tabindex="<?php $this->tabIndex(); ?>" id="StateProvinceSelect" name="venue[State]">
		<option value=""><?php _e( 'Select a State:', 'tribe-events-community' ); ?></option>
		<?php
			foreach ( TribeEventsViewHelpers::loadStates() as $abbr => $fullname ) {
				echo '<option value="' . $abbr .'" ';
				if ( $_VenueStateProvince != -1 ) {
					selected( ( ( $_VenueStateProvince != -1 ? $_VenueStateProvince : $_VenueState ) == $abbr ) );
				}
				echo '>' . esc_html( $fullname ) . '</option>' . "\n";
			}
		?>
	</select>

	</td>
</tr>
<tr class="venue">
	<td><?php _e( 'Postal Code:', 'tribe-events-community' ); ?></td>
	<td><input tabindex="<?php $this->tabIndex(); ?>" type='text' id='EventZip' name='venue[Zip]' size='6' value='<?php if ( isset( $_VenueZip ) ) echo esc_attr( $_VenueZip ); ?>' /></td>
</tr>
<tr class="venue">
	<td><?php _e( 'Phone:', 'tribe-events-community' ); ?></td>
	<td><input tabindex="<?php $this->tabIndex(); ?>" type='text' id='EventPhone' name='venue[Phone]' size='14' value='<?php if ( isset( $_VenuePhone ) ) echo esc_attr( $_VenuePhone ); ?>' /></td>
</tr>