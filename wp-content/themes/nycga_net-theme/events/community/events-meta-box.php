<?php

// Don't load directly
if ( !defined( 'ABSPATH' ) ) {
	die('-1' );
}

/**
 * The Events Meta Box
 * You can customize this view by putting a replacement file of the same name (events-meta-box.php) in the events/community/ directory of your theme.
 *
 * @package TribeCommunityEvents
 * @since  1.0
 * @author Modern Tribe Inc.
 */

?>
<style type="text/css">
	<?php if( class_exists( 'Eventbrite_for_TribeEvents' ) ) : ?>
		.eventBritePluginPlug {display:none;}
	<?php endif; ?>
</style>

<?php //if( WP_DEBUG ) echo '<small>[DEBUG] Path to this template: '.__FILE__.'</small>'; ?>

<div id="eventIntro">
<div id="tribe-events-post-error" class="tribe-events-error error"></div>
<?php do_action( 'tribe_events_post_errors', $postId, true ) ?>

</div>
<div id='eventDetails' class="inside eventForm bubble">
   <?php do_action( 'tribe_events_detail_top', $postId, true ) ?>
	<?php wp_nonce_field( TribeEvents::POSTTYPE, 'ecp_nonce' ); ?>
	<table cellspacing="0" cellpadding="0" id="EventInfo">
		<tr>
			<td colspan="2" class="tribe_sectionheader"><h4 class="event-time"><?php _e( 'Event Time &amp; Date', 'tribe-events-community' ); ?></h4></td>
		</tr>
		<tr id="recurrence-changed-row">
			<td colspan='2'><?php _e( 'You have changed the recurrence rules of this event. Saving the event will update all future events.  If you did not mean to change all events, then please refresh the page.', 'tribe-events-community' ) ?></td>
		</tr>
		<tr>
			<td><?php _e( 'All day event?', 'tribe-events-community' ); ?></td>
			<td><input tabindex="<?php $this->tabIndex(); ?>" type='checkbox' id='allDayCheckbox' name='EventAllDay' value='yes' <?php echo $isEventAllDay; ?> /></td>
		</tr>
		<tr>
			<td style="width:125px;"><?php _e( 'Start Date / Time:','tribe-events-community' ); ?></td>
			<td>
				<input autocomplete="off" tabindex="<?php $this->tabIndex(); ?>" type="text" class="datepicker" name="EventStartDate" id="EventStartDate"  value="<?php echo esc_attr( $EventStartDate ) ?>" />
				<span class="helper-text hide-if-js"><?php _e( 'YYYY-MM-DD', 'tribe-events-community' ) ?></span>
				<span class='timeofdayoptions'>
					<?php _e( '@','tribe-events-community' ); ?>
					<select tabindex="<?php $this->tabIndex(); ?>" name='EventStartHour'>
						<?php echo $startHourOptions; ?>
					</select>
					<select tabindex="<?php $this->tabIndex(); ?>" name='EventStartMinute'>
						<?php echo $startMinuteOptions; ?>
					</select>
					<?php if ( !strstr( get_option( 'time_format', TribeDateUtils::TIMEFORMAT ), 'H' ) ) : ?>
						<select tabindex="<?php $this->tabIndex(); ?>" name='EventStartMeridian'>
							<?php echo $startMeridianOptions; ?>
						</select>
					<?php endif; ?>
				</span>
			</td>
		</tr>
		<tr>
			<td><?php _e( 'End Date / Time:','tribe-events-community' ); ?></td>
			<td>
				<input autocomplete="off" type="text" class="datepicker" name="EventEndDate" id="EventEndDate"  value="<?php echo esc_attr( $EventEndDate ); ?>" />
				<span class="helper-text hide-if-js"><?php _e( 'YYYY-MM-DD', 'tribe-events-community' ) ?></span>
				<span class='timeofdayoptions'>
					<?php _e( '@','tribe-events-community' ); ?>
					<select class="spEventsInput" tabindex="<?php $this->tabIndex(); ?>" name='EventEndHour'>
						<?php echo $endHourOptions; ?>
					</select>
					<select tabindex="<?php $this->tabIndex(); ?>" name='EventEndMinute'>
						<?php echo $endMinuteOptions; ?>
					</select>
					<?php if ( !strstr( get_option( 'time_format', TribeDateUtils::TIMEFORMAT ), 'H' ) ) : ?>
						<select tabindex="<?php $this->tabIndex(); ?>" name='EventEndMeridian'>
							<?php echo $endMeridianOptions; ?>
						</select>
					<?php endif; ?>
				</span>
			</td>
		</tr>
		<?php do_action( 'tribe_events_date_display', $postId, true ) ?>
	</table>
	<div class="tribe_sectionheader" style="padding: 6px 6px 0 0; font-size: 11px; margin: 0 10px;"><h4><?php _e( 'Event Location Details', 'tribe-events-community' ); ?></h4></div>
	<div style="float: left;">
		<table id="event_venue" class="eventtable">
         <?php do_action( 'tribe_venue_table_top', $postId ) ?>
			<?php
			$venue_meta_box = $this->pluginPath . 'views/venue-meta-box.php';
			$venue_meta_box = apply_filters( 'tribe_events_venue_meta_box_template', $venue_meta_box );
			include( $venue_meta_box );
			?>
			<tr id="google_map_link_toggle">
				<td><?php _e( 'Show Google Maps Link:','tribe-events-community' ); ?></td>
				<td>
					<input tabindex="<?php $this->tabIndex(); ?>" type="checkbox" id="EventShowMapLink" name="EventShowMapLink" value="1" <?php

					//check $_POST for unsaved events
					if ( isset( $_POST['EventShowMapLink'] ) ){
						checked( $_POST['EventShowMapLink'] );
					} else {
						checked( get_post_meta( $postId, '_EventShowMapLink', true ) );
					}
					?> />
				</td>
			</tr>
			<?php if( tribe_get_option( 'embedGoogleMaps' ) ) : ?>
				<tr id="google_map_toggle">
					<td><?php _e( 'Show Google Map:','tribe-events-community' ); ?></td>
					<td><input tabindex="<?php $this->tabIndex(); ?>" type="checkbox" id="EventShowMap" name="EventShowMap" value="1" <?php checked( tribe_embed_google_map( $postId ) ); ?> /></td>
				</tr>
			<?php endif; ?>
		</table>
	</div>
   <?php do_action( 'tribe_after_location_details', $postId ); ?>
	<table id="event_organizer" class="eventtable">
			<tr>
				<td colspan="2" class="tribe_sectionheader"><h4><?php _e( 'Event Organizer Details', 'tribe-events-community' ); ?></h4></td>
			</tr>
         <?php do_action( 'tribe_organizer_table_top', $postId ); ?>
			<?php
			$organizer_meta_box = $this->pluginPath . 'views/organizer-meta-box.php';
			$organizer_meta_box = apply_filters( 'tribe_events_organizer_meta_box_template', $organizer_meta_box );
			include( $organizer_meta_box );
			?>
	</table>
    <?php do_action( 'tribe_events_details_table_bottom', $postId, true ) ?>
	<?php if ( !class_exists( 'Event_Tickets_PRO' ) ) { ?>
	<table id="event_cost" class="eventtable">
		<tr>
			<td colspan="2" class="tribe_sectionheader"><h4><?php _e( 'Event Cost', 'tribe-events-community' ); ?></h4></td>
		</tr>
		<tr>
			<td><?php _e( 'Cost:','tribe-events-community' ); ?></td>
			<td><input tabindex="<?php $this->tabIndex(); ?>" type='text' id='EventCost' name='EventCost' size='6' value='<?php echo ( isset( $_POST['EventCost'] ) ) ? esc_attr( $_POST['EventCost'] ) : tribe_get_cost( $postId ); ?>' /></td>
		</tr>
		<tr>
			<td></td>
			<td><small><?php _e( 'Leave blank to hide the field. Enter a 0 for events that are free.', 'tribe-events-community' ); ?></small></td>
		</tr>
      <?php do_action( 'tribe_events_cost_table', $postId, true ) ?>
	</table>
	<?php } ?>
	</div>
   <?php do_action( 'tribe_events_above_donate', $postId, true ) ?>
   <?php do_action( 'tribe_events_details_bottom', $postId, true ) ?>
