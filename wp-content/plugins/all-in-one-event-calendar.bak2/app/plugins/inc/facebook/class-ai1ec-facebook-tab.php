<?php
/**
 * @author time.ly
 *
 * This class handles the rendering of the HTML for the Facebook Tab.
 */
class Ai1ec_Facebook_Tab {
	const FB_MULTISELECT_NAME = 'ai1ec-facebook-';
	/**
	 * Holds any informational messages that will be printed
	 *
	 * @var array
	 */
	private $_info_messages;
	/**
	 * Holds any error messages that will be printed
	 *
	 * @var array
	 */
	private $_error_messages;
	/**
	 * @param array: $_error_messages
	 */
	public function set_error_messages( array $_error_messages ) {
		$this->_error_messages = $_error_messages;
	}
	/**
	 *
	 * @return string
	 */
	public function render_question_mark_for_facebook() {
		$html = '<a class="btn btn-mini icon-question-sign icon-large" target="_blank" ' .
			'href="http://help.time.ly/customer/portal/articles/616553-how-do-i-use-the-facebook-import-" ' .
			'title="' . __( 'How do I use Facebook import?', AI1EC_PLUGIN_NAME ) .
			'"></a>';
		return $html;
	}
	/**
	 * @param array: $_info_messages
	 */
	public function set_informational_messages( array $_info_messages ) {
		$this->_info_messages = $_info_messages;
	}
	/**
	 * Creates the HTML for the specified multiselect
	 *
	 * @param string $type the type of multiselect to return
	 *
	 * @param $current_id The id of the currently logged on user which must be excluded from subscribers and multiselects
	 *
	 * @return string The HTML of the multiselect
	 */
	public function render_multi_select( $type, $current_id ) {
		$data = $this->get_data_for_multiselect( $type, $current_id );
		// If there is no data in the DB show a standard message.
		if ( count($data) === 0 ) {
			// Add the div so that the ajax refresh of the multiselect works as expecte also when there is no multiselect.
			return '<div class="ai1ec-facebook-multiselect">' . __( 'Nothing to display', AI1EC_PLUGIN_NAME ) . '</div>';
		}
		// Build the name.
		$name = self::FB_MULTISELECT_NAME . $type;

		// Start building the multiselect
		$html = "<select multiple='multiple' id='ai1ec-facebook-$type' class='ai1ec-facebook-multiselect' name='{$name}[]' size='8'>";
		foreach ( $data as $option ) {
			// Build the final text.
			$text = $option['user_name'];
			// the value that will be posted is the user id.
			$value = $option['user_id'];
			$html .= "<option value='$value'>$text</option>";
		}
		// Close the select.
		$html .= "</select>";
		// Return it.
		return $html;
	}
	/**
	 * Renders the modal that is used to input facebook app id and secret
	 *
	 * @return string
	 */
	public function render_modal_for_facebook_app_id_and_secret_and_return_html() {
		$info = __( Ai1ecFacebookConnectorPlugin::FB_INFO_VARIABLE, AI1EC_PLUGIN_NAME );
		$label_app_id = esc_html__( Ai1ecFacebookConnectorPlugin::FB_APP_ID_DESCRIPTION_TEXT, AI1EC_PLUGIN_NAME );
		$label_app_secret = esc_html__( Ai1ecFacebookConnectorPlugin::FB_APP_SECRET_DESCRIPTION_TEXT, AI1EC_PLUGIN_NAME );
		$modal_body = <<<HTML
<div id="form_fields">
	$info
	<div>
		<label for="ai1ec_facebook_app_id_modal" id="label_app_id">
			$label_app_id
		</label>
		<input type="text" name="ai1ec_facebook_app_id_modal" id="ai1ec_facebook_app_id_modal" value="">
	</div>
	<div>
		<label for="ai1ec_facebook_app_secret_modal" id="label_app_secret">
			$label_app_secret
		</label>
		<input type="text" name="ai1ec_facebook_app_secret_modal" id="ai1ec_facebook_app_secret_modal" value="">
	</div>
</div>
HTML;
		$twitter_bootstrap_modal = new Ai1ec_Twitter_Bootstrap_Modal( $modal_body );
		$twitter_bootstrap_modal->set_keep_button_text( esc_html__( "Save", AI1EC_PLUGIN_NAME ) );
		$twitter_bootstrap_modal->set_header_text( esc_html__( "Facebook Configuration", AI1EC_PLUGIN_NAME ) );
		$twitter_bootstrap_modal->set_id( "ai1ec_facebook_connect_modal" );
		return $twitter_bootstrap_modal->render_modal_and_return_html();
	}
	/**
	 * Gets the data to populate the multiselect
	 *
	 * @param string $type
	 *
	 * @param $current_id The id of the currently logged on user which must be excluded from subscribers and multiselects
	 *
	 * @return array the results
	 */
	private function get_data_for_multiselect( $type, $current_id ) {
		global $wpdb;
		$table_name = Ai1ec_Facebook_Factory::get_plugin_table();
		$data = $wpdb->get_results( $wpdb->prepare(
				"
				SELECT
					user_id,
					user_name,
					user_pic
				FROM
					$table_name
				WHERE
					subscribed = 0 AND
					type = %s AND
					user_id != %d
				ORDER BY
					user_name ASC
				",
				$type,
				$current_id
		), ARRAY_A );
		return $data;
	}
	/**
	 * Gets the subscribers data from the db
	 *
	 * @param string $type the type to get
	 *
	 * @param $current_id The id of the currently logged on user which must be excluded from subscribers and multiselects
	 *
	 * @return array the results
	 */
	private function get_data_for_subscribed_items( $type, $current_id ) {
		global $wpdb;
		$table_name = Ai1ec_Facebook_Factory::get_plugin_table();
		$data = $wpdb->get_results( $wpdb->prepare(
				"
				SELECT
					user_id,
					user_name,
					user_pic,
					category,
					tag
				FROM
					$table_name
				WHERE
					subscribed = 1 AND
					type = %s AND
					user_id != %s
				ORDER BY
					user_name ASC
				",
				$type,
				$current_id
		), ARRAY_A );
		return $data;
	}
	/**
	 * Calls the appropriate function to render either the multiselect or the subscriber of the specified types
	 *
	 * @param array $types the Facebook Graph Object types
	 *
	 * @param boolean $multiselect Whether we should render the multiselect or the subscribers
	 *
	 * @param $current_id The id of the currently logged on user which must be excluded from subscribers and multiselects
	 *
	 * @return array
	 */
	private function render_all_elements( array $types, $multiselect = TRUE, $current_id ) {
		$elements = array();
		$function = ($multiselect === TRUE) ? 'render_multi_select' : 'render_subscribed_items';
		foreach( $types as $type ) {
			$elements[$type] = $this->$function( $type, $current_id );
		}
		return $elements;
	}
	/**
	 * Create the text of the message that is visualized after the user refreshes the events
	 *
	 * @param array $response
	 *
	 * @return string
	 */
	public function create_refresh_message( $response ) {
		$type = isset( $response['type'] ) ? sprintf( __( "Syncing events for %s.", AI1EC_PLUGIN_NAME ), $response['type'] ) : '';
		$errors    = $response['errors'] ? __( 'Something went wrong while updating events, but some events might still be updated / inserted correctly', AI1EC_PLUGIN_NAME ) . "<br />" : '';
		$updated   = $response['events_updated'] > 0 ? sprintf( _n( " %d event was updated.", ' %d events were updated.', (int) $response['events_updated'], AI1EC_PLUGIN_NAME ), $response['events_updated'] ) . "<br />" : '';
		$inserted  = $response['events_inserted'] > 0 ? sprintf( _n( " %d event was added.", ' %d events were added.', (int) $response['events_inserted'], AI1EC_PLUGIN_NAME ), $response['events_inserted'] ) . "<br />" : '';
		$deleted   = $response['events_deleted'] > 0 ? sprintf( _n( " %d event was deleted.", ' %d events were deleted.', (int) $response['events_deleted'], AI1EC_PLUGIN_NAME ), $response['events_deleted'] ) . "<br />" : '';
		$to_return = $errors . $updated . $inserted . $deleted;
		return empty( $to_return ) ? $type . __( " No events to add or update.", AI1EC_PLUGIN_NAME ) : $type . $to_return ;
	}
	/**
	 * Render the html for the category and tags div
	 *
	 * @param int $category the category
	 *
	 * @param string $tag the tags
	 *
	 * @return the HTML
	 */
	private function render_category_tag_div( $category, $tag ) {
		// Check if we have something.
		$category_empty = empty( $category );
		$tag_empty = empty( $tag );
		$html = '';
		// If nothing is set, just return (by default category is 0 and tag ''
		if ( $category_empty && $tag_empty ) {
			return $html;
		}
		$html = '<div class="ai1ec-facebook-category-tag-wrapper">';
		if( ! $category_empty ) {
			$feed_category = get_term( $category, 'events_categories' );
			$category_name = $feed_category->name;
			$label_category = __( "Event category: ", AI1EC_PLUGIN_NAME );
			$html .= "<span class='ai1ec-facebook-category' data-category='$category'>$label_category<strong>$category_name</strong></span> &nbsp;&nbsp;";
		}
		if( ! $tag_empty ) {
			$tag_label = __( "Tag with: ", AI1EC_PLUGIN_NAME );
			$html .= "<span class='ai1ec-facebook-tag' data-tag='$tag'>$tag_label<strong>$tag</strong></span>";
		}
		$html .= "</div>";
		return $html;
	}
	/**
	 * Renders the HTML for the subscribed items of the specified type.
	 *
	 * @param string $type
	 *
	 * @param $current_id The id of the currently logged on user which must be excluded from subscribers and multiselects
	 *
	 * @return string the HTML
	 */
	private function render_subscribed_items( $type, $current_id ) {
		$data = $this->get_data_for_subscribed_items( $type, $current_id );
		if ( count($data) === 0 ) {
			return __( 'No subscriptions yet.', AI1EC_PLUGIN_NAME );
		}
		$html = "";
		$chunked_data = array_chunk( $data, 2 );
		foreach ( $chunked_data as $row ) {
			$html .= '<div class="row-fluid">';
			foreach ( $row as $subscriber ) {
				$name         = esc_html__( $subscriber['user_name'] );
				$id           = $subscriber['user_id'];
				$pic          = $subscriber['user_pic'];
				$cat_tag_html = $this->render_category_tag_div( $subscriber["category"], $subscriber["tag"] );
				$refresh_tip  = esc_attr__( 'Refresh these events', AI1EC_PLUGIN_NAME );
				$remove_tip   = esc_attr__( 'Unsubscribe from these events', AI1EC_PLUGIN_NAME );

				$html .= '<div class="ai1ec-facebook-subscriber well span6">';
				if ( ! empty( $pic ) ) {
					$html .= <<<HTML
					<img src="$pic" alt="$name" class="ai1ec-facebook-pic pull-left" />
HTML;
				}
				$html .= <<<HTML
					<div class="pull-right ai1ec-facebook-buttons">
						<a class="ai1ec-facebook-refresh btn btn-mini icon-refresh" data-id="$id" title="$refresh_tip"></a>
						<a class="ai1ec-facebook-remove btn btn-mini icon-remove" data-id="$id" title="$remove_tip"></a>
					</div>
					<img src="images/wpspin_light.gif" class="ajax-loading-user hide pull-right" alt="" />
					<div class="ai1ec-facebook-name">$name</div>
					$cat_tag_html
				</div>
HTML;
			}
			$html .= "</div>";
		}
		return $html;
	}
	/**
	 * Generate the HTML for the alerts that are set.
	 *
	 * @return string the HTML
	 */
	private function generate_html_for_alerts() {
		$html = '';
		if( isset( $this->_error_messages ) ) {
			foreach( $this->_error_messages as $message ) {
				$error = esc_html( __( $message, AI1EC_PLUGIN_NAME ) );
				$html .= "<div id='message' class='alert alert-error'>
							<a class='close' data-dismiss='alert' href='#'>x</a>
							$error
						</div>";
			}
		}
		if( isset( $this->_info_messages ) ) {
			foreach( $this->_info_messages as $message ) {
				$class = ( $message['errors'] === TRUE ) ? '' : 'alert-success';
				$text = $this->create_refresh_message( $message );
				$html .= "<div id='message' class='alert $class'>
				<a class='close' data-dismiss='alert' href='#'>x</a>
				$text
				</div>";
			}
			}
		return $html;
	}
	/**
	 * Renders the user pictur and name rendering the appropriate icons
	 *
	 * @param Ai1ec_Facebook_Current_User $user the User currently logged into Facebook
	 *
	 * @return string the generated HTML
	 */
	private function render_user_pic_and_icons( Ai1ec_Facebook_Current_User $user ) {
		$name                = $user->get_name();
		$id                  = $user->get_id();
		$username            = $user->get_username();
		$alt_img             = esc_attr__( 'Profile image', AI1EC_PLUGIN_NAME );
		$logged_in_text      = esc_html__( 'Hi, you are logged in as:', AI1EC_PLUGIN_NAME );
		$type                = Ai1ec_Facebook_Graph_Object_Collection::FB_USER;
		$cat_tag_html        = $this->render_category_tag_div( $user->get_category(), $user->get_tag() );
		$refresh_label       = esc_attr__( ' Refresh', AI1EC_PLUGIN_NAME );
		$remove_label        = esc_attr__( ' Unsubscribe', AI1EC_PLUGIN_NAME );
		$refresh_tip         = esc_attr__( 'Refresh my events', AI1EC_PLUGIN_NAME );
		$remove_tip          = esc_attr__( 'Unsubscribe from my events', AI1EC_PLUGIN_NAME );

		$html = <<<HTML
<div id="profile-name">$logged_in_text</div>
<div class="row-fluid">
	<div class="ai1ec-facebook-subscriber ai1ec-facebook-items well pull-left" data-type="$type">
		<img id="profile-img" class="ai1ec-facebook-pic pull-left" alt="$alt_img" src="https://graph.facebook.com/$username/picture" />
HTML;

			if( $user->get_subscribed() ) {
				$html .= <<<HTML
		<div class="pull-right ai1ec-facebook-buttons">
			<a class="ai1ec-facebook-refresh btn btn-mini icon-refresh" data-id="$id" title="$refresh_tip">$refresh_label</a>
			<a class="ai1ec-facebook-remove btn btn-mini icon-remove logged" data-id="$id" title="$remove_tip">$remove_label</a>
		</div>
HTML;
			}

			$html .= <<<HTML
		<img src="images/wpspin_light.gif" class="ajax-loading-user hide pull-right" alt="" />
		<div class="ai1ec-facebook-name"><big>$name</big></div>
		$cat_tag_html
	</div>
</div>
HTML;

		return $html;
	}
	/**
	 * Returns the upper part of the tab, with any errors
	 *
	 * @param Ai1ec_Facebook_Current_User $user The user currently logged in to Facebook
	 *
	 * @return string the HTML
	 */
	private function render_user_html( Ai1ec_Facebook_Current_User $user ) {
		// Create the variables and then create the HTML.

		$submit_logout_value           = esc_attr__( 'Log out from Facebook', AI1EC_PLUGIN_NAME );
		$submit_subscribe_your_events  = esc_attr__( 'Subscribe to your events', AI1EC_PLUGIN_NAME );
		$alerts                        = $this->generate_html_for_alerts();
		$submit_your_events            = $user->get_subscribed() ?
		                                 "<input type='submit' id='ai1ec_facebook_subscribe_yours' name='ai1ec_facebook_subscribe_yours' class='button-primary hide' value='$submit_subscribe_your_events'>" :
		                                 "<input type='submit' id='ai1ec_facebook_subscribe_yours' name='ai1ec_facebook_subscribe_yours' class='button-primary' value='$submit_subscribe_your_events'>";
		$user                          = $this->render_user_pic_and_icons( $user );
		$question_mark                 = $this->render_question_mark_for_facebook();
		$html = <<<HTML
<div class="row-fluid">
	<div class="span12">
		<div id="alerts">$alerts</div>
		<div id="ai1ec-facebook">
		$user
		</div>
		<div class="ai1ec_submit_wrapper">
			$submit_your_events
			<input type="submit" id="ai1ec_logout_facebook" name="ai1ec_logout_facebook" class="button-secondary" value="$submit_logout_value">
			$question_mark
		</div>
	</div>
</div>
HTML;
		return $html;
	}
	/**
	 * Generate the HTML for the select category
	 *
	 * @return string the HTML
	 */
	private function create_select_category() {
		$select = '<select name="ai1ec_facebook_feed_category" id="ai1ec_facebook_feed_category">';
		// Set an empty option
		$select .= "<option value=''>" . esc_html__( "Choose a category", AI1EC_PLUGIN_NAME );
		foreach( get_terms( 'events_categories', array( 'hide_empty' => false ) ) as $term ) {
			$select .= "<option value='{$term->term_id}'>{$term->name}</option>";
		}
		$select .= '</select>';
		return $select;
	}
	/**
	 * Renders the HTML for the Multiselect of the specified types
	 *
	 * @param array $types The type of Facebook Graph Object to render
	 *
	 * @param $current_id The id of the currently logged on user which must be excluded from subscribers and multiselects
	 *
	 * @return the HTML
	 */
	private function render_multiselects( array $types, $current_id ) {
		$multiselects = $this->render_all_elements( $types, TRUE, $current_id );
		$category_select = $this->create_select_category();
		$label_category = esc_html__( 'Event category', AI1EC_PLUGIN_NAME );
		$label_tag = esc_html__( 'Tag with', AI1EC_PLUGIN_NAME );
		$label_refresh = esc_html__( 'Refresh list', AI1EC_PLUGIN_NAME );
		$html = '<h1>' . esc_html__( 'Subscribe to more events', AI1EC_PLUGIN_NAME ) . '</h1>';
		$html .= '<p>';
		$html .= esc_html__( "You can subscribe to the shared calendars of friends, pages, and groups you are connected with. Select those calendars you're interested in below.", AI1EC_PLUGIN_NAME );
		$html .= '</p>';
		$html .= '<div class="row-fluid">';
		foreach ( $types as $type ) {
			// Get the correct text for the type.
			$text = Ai1ec_Facebook_Graph_Object_Collection::get_type_printable_text( $type );
			$html .= <<<HTML
<div class="span4 ai1ec-facebook-multiselect-container" data-type="$type">
	<div class="ai1ec-facebook-multiselect-title-wrapper">
		<h2 class="ai1ec-facebook-header pull-left">$text</h2>
		<a class="ai1ec-facebook-refresh-multiselect btn btn-small icon-refresh">
			$label_refresh
		</a>
		<img src="images/wpspin_light.gif" class="ajax-loading pull-right" alt="" />
	</div>
	<div class="clear"></div>
	{$multiselects[$type]}
</div>
HTML;
		}
		$html .= "</div>";
		$submit_subscribe_value = __( 'Subscribe to selected', AI1EC_PLUGIN_NAME );
		$html .= <<<HTML
<div class="row-fluid ai1ec_submit_wrapper">
	<div class="ai1ec-feed-category pull-left">
		<label for="ai1ec_facebook_feed_category">
			$label_category:
		</label>
		$category_select
	</div>
	<div class="ai1ec-feed-tags pull-left">
		<label for="ai1ec_facebook_feed_tags">
			$label_tag:
		</label>
		<input type="text" name="ai1ec_facebook_feed_tags" id="ai1ec_facebook_feed_tags" />
	</div>
</div>
<div class="row-fluid ai1ec_submit_wrapper">
	<div class="span12">
		<input type="submit" id="ai1ec_subscribe_users" name="ai1ec_subscribe_users" class="button-primary" value="$submit_subscribe_value">
	</div>
</div>
HTML;
		return $html;
	}
	/**
	 * Renders the Facebook Graph Object that user has subscribed to.
	 *
	 * @param array $types the Type of Facebook Graph Objects to render
	 *
	 * @param $current_id The id of the currently logged on user which must be excluded from subscribers and multiselects
	 *
	 * @return string the HTML
	 */
	private function render_subscribers( array $types, $current_id ) {
		$subscribers = $this->render_all_elements( $types, FALSE, $current_id );
		$html = '';
		foreach ( $types as $type ) {
			$text = Ai1ec_Facebook_Graph_Object_Collection::get_type_printable_text( $type );
			$html .= <<<HTML
<div class="ai1ec-facebook-items" data-type="$type">
	<h2 class="ai1ec-facebook-header">$text</h2>
	{$subscribers[$type]}
</div>
HTML;
		}
		$keep_events = __( "Keep Events", AI1EC_PLUGIN_NAME );
		$remove_events = __( "Remove Events", AI1EC_PLUGIN_NAME );
		$body = __( "Would you like to remove these events from your calendar, or preserve them?", AI1EC_PLUGIN_NAME );
		$removing = __( "Removing the following subscription: ", AI1EC_PLUGIN_NAME );
		$header_text = $removing . '<span id="ai1ec-facebook-user-modal"></span>';
		// Attach the modal for when you unsubscribe.
		$twitter_bootstrap_modal = new Ai1ec_Twitter_Bootstrap_Modal( $body );
		$twitter_bootstrap_modal->set_id( 'ai1ec-facebook-modal' );
		$twitter_bootstrap_modal->set_delete_button_text( $remove_events );
		$twitter_bootstrap_modal->set_keep_button_text( $keep_events );
		$twitter_bootstrap_modal->set_header_text( $header_text );
		$modal = <<<HTML
<div class="modal hide" id="ai1ec-facebook-modal">
	<div class="modal-header">
		<button class="close" data-dismiss="modal">×</button>
		<h1><small>$removing <span id="ai1ec-facebook-user-modal"></span></small></h1>
	</div>
	<div class="modal-body">
		<p>$body</p>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn remove btn-danger">$remove_events</a>
		<a href="#" class="btn keep btn-primary">$keep_events</a>
	</div>
</div>
HTML;
		$html .= $twitter_bootstrap_modal->render_modal_and_return_html();
		return $html;
	}
	/**
	 * echoes the HTML for the Facebook Tab
	 *
	 * @param Ai1ec_Facebook_Current_User $user the currently logged user
	 *
	 * @param array $types The types to render
	 */
	public function render_tab( Ai1ec_Facebook_Current_User $user, array $types ) {
		// Render the part with the user and the submit buttons
		$html = $this->render_user_html( $user );
		// Render the multiselects
		$html .= $this->render_multiselects( $types, $user->get_id() );
		// Render the subscribers.
		$html .= $this->render_subscribers( $types, $user->get_id() );
		echo $html;
	}
}

?>
