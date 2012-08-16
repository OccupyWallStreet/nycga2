<?php
// Don't load directly
if ( !defined( 'ABSPATH' ) ) {
	die('-1');
}

/**
 * The Recurrence dialog box
 * You can customize this view by putting a replacement file of the same name (delete.php) in the events/community/ directory of your theme.
 *
 * @package TribeCommunityEvents
 * @since  1.0
 * @author Modern Tribe Inc.
 */

?>

<div id="recurring-dialog"  title="Saving Recurring Event" style="display: none;">
	<?php _e( 'Which events do you wish to update?', 'tribe-events-community' ); ?><br/>
</div>
<div id="deletion-dialog"  title="<?php _e( 'Delete Recurring Event', 'tribe-events-community' ); ?>" style="display: none;" data-start="<?php echo (isset($recStart)) ?  $recStart : '' ?>" data-post="<?php echo (isset($recPost)) ?  $recPost : '' ?>">
	<?php _e( 'Select your desired action', 'tribe-events-community' ); ?><br/>
</div>
