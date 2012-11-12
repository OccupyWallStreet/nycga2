<?php

/**
 * @author The Seed Studio
 *
 * This class is responsible of handling the custom "Export to Facebook" bulk action
 */

class Ai1ec_Facebook_Custom_Bulk_Action {
	// The name/id of the select in the All events page
	const FB_SELECT_NAME                    = 'ai1ec-facebook-filter';
	/**
	 * @var Ai1ecFacebookConnectorPlugin
	 */
	private $facebook_plugin;
	public function __construct( Ai1ecFacebookConnectorPlugin $facebook_plugin ) {
		$this->facebook_plugin = $facebook_plugin;
	}
	/**
	 * echoes the SELECT that allows the user to filter events according to their facebook status
	 *
	 */
	public function facebook_filter_restrict_manage_posts() {
		global $typenow;
		if( $typenow === AI1EC_POST_TYPE ) {
			$select_id       = self::FB_SELECT_NAME;
			$all_evts_lbl    = __( "Show all events", AI1EC_PLUGIN_NAME );
			$imported_lbl    = __( "Show only events imported from Facebook", AI1EC_PLUGIN_NAME );
			$exported_lbl    = __( "Show only events exported to Facebook", AI1EC_PLUGIN_NAME );
			$exportable_lbl  = __( "Show only events that can be exported to Facebook", AI1EC_PLUGIN_NAME );
			$exp_val         = Ai1ecFacebookConnectorPlugin::FB_EXPORTED_EVENT;
			$imp_val         = Ai1ecFacebookConnectorPlugin::FB_IMPORTED_EVENT;
			$exportable_val  = "exportable";
			$options = array(
					''              => $all_evts_lbl,
					$exp_val        => $exported_lbl,
					$imp_val        => $imported_lbl,
					$exportable_val => $exportable_lbl,
			);
			$html = "<select name='$select_id' id='$select_id'>";
			$selected = isset( $_GET[self::FB_SELECT_NAME] ) ? $_GET[self::FB_SELECT_NAME] : '';
			foreach ( $options as $val => $label ) {
				$sel = $selected === $val ? 'selected' : '';
				$html .= "<option value='$val' $sel>$label</option>";
			}
			$html .= "</select>";
			echo $html;
		}
	}
	/**
	 * Implements the filtering for events that are exportable to facebook
	 *
	 * @param string $where the where clause currently in use by wordpress
	 *
	 * @return string the updated where clause
	 */
	public function facebook_filter_posts_where( $where ) {
		$type = '';
		$end = '';
		// If we have something to query
		if( isset( $_GET[self::FB_SELECT_NAME] ) && ! empty( $_GET[self::FB_SELECT_NAME] ) ) {
			// Let's see what was requested
			switch ( $_GET[self::FB_SELECT_NAME] ) {
				case Ai1ecFacebookConnectorPlugin::FB_EXPORTED_EVENT :
					$type = Ai1ecFacebookConnectorPlugin::FB_EXPORTED_EVENT;
					break;
				case Ai1ecFacebookConnectorPlugin::FB_IMPORTED_EVENT :
					$type = Ai1ecFacebookConnectorPlugin::FB_IMPORTED_EVENT;
					break;
				default:
					$type = '';
					// Select only events that end in the future.
					$end = ' and end > NOW()';
					break;
			}
			$table_name = Ai1ec_Facebook_Factory::get_events_table();
			// update the query
			$where .= " AND ID in ( SELECT post_id from $table_name WHERE facebook_status = '$type' $end ) ";
		}
		return $where;
	}
	/**
	 * Execute the custom bulk action "Export to Facebook".
	 *
	 */
	public function facebook_custom_bulk_action() {
		// if our custom action is selected and something has been posted
		if( ( ( isset( $_GET['action'] ) && $_GET['action'] === 'export-facebook' ) || ( isset( $_GET['action2'] ) && $_GET['action2'] === 'export-facebook' ) ) && isset( $_GET['post'] ) ) {
			// Check if we have a valid access token
			if( $this->facebook_plugin->check_if_we_have_a_valid_access_token() ) {
				// Iterate on the posts
				foreach( $_GET['post'] as $post_id ) {
					$ai1ec_event = new Ai1ec_Event( $post_id );
					// MAke an extra check that the post can be exported
					if( $ai1ec_event->facebook_status === '' ) {
						// Get a facebook event instance
						$facebook_event = Ai1ec_Facebook_Factory::get_facebook_event_instance();
						// Prepare the event
						$facebook_event->populate_event_from_ai1ec_event( $ai1ec_event );
						$facebook = $this->facebook_plugin->facebook_instance_factory();
						// Save the event to facebook
						$result = $facebook_event->save_to_facebook( $facebook );
						// If everything went as expected, update the calendar event
						if( is_array( $result )  && ! empty( $result ) ) {
							$ai1ec_event->facebook_eid = $result['id'];
							$ai1ec_event->facebook_status = Ai1ecFacebookConnectorPlugin::FB_EXPORTED_EVENT;
							$ai1ec_event->save( TRUE );
						}
					}
					// Wait one second to avoid overloading Facebook
					sleep( 1 );
				}
			} else {
				$message = array(
						"label"        => __( 'All-in-One Event Calendar Facebook Export Error', AI1EC_PLUGIN_NAME ),
						"message"      => __( "We couldn't export data to Facebook as we don't have a valid access token. Try to log out of Facebook from the <strong>Events</strong> &gt; <strong>Calendar Feeds</strong> screen, then log in again.", AI1EC_PLUGIN_NAME ),
						"message_type" => "error",
				);
				update_option( Ai1ecFacebookConnectorPlugin::FB_OPTION_CRON_NOTICE, $message );
			}
		}
	}
}

?>
