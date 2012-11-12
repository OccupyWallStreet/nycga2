<?php
//
//  class-ai1ec-events-controller.php
//  all-in-one-event-calendar
//
//  Created by The Seed Studio on 2011-07-13.
//

/**
 * Ai1ec_Events_Controller class
 *
 * @package Controllers
 * @author time.ly
 **/
class Ai1ec_Events_Controller {
	/**
	 * _instance class variable
	 *
	 * Class instance
	 *
	 * @var null | object
	 **/
	private static $_instance = NULL;

	/**
	 * get_instance function
	 *
	 * Return singleton instance
	 *
	 * @return object
	 **/
	static function get_instance() {
		if( self::$_instance === NULL ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Constructor
	 *
	 **/
	private function __construct() { }

	/**
	 * delete_hook function
	 *
	 * If the deleted post is an event
	 * then all entries that match the post_id are
	 * removed from ai1ec_events and ai1ec_event_instances tables
	 *
	 * @param int $pid Post ID
	 *
	 * @return bool | int
	 **/
	function delete_post( $pid ) {
		global $wpdb, $ai1ec_importer_plugin_helper;

		$sql = "SELECT
							ID
						FROM
							$wpdb->posts
						WHERE
							ID = %d AND
							post_type = '" . AI1EC_POST_TYPE . "'";

		// is this post an event?
		if( $wpdb->get_var( $wpdb->prepare( $sql, $pid ) ) ) {
			// We need to pass an event object to the importer plugins to clean up.
			$ai1ec_event = new Ai1ec_Event( $pid );
			$ai1ec_importer_plugin_helper->handle_post_event( $ai1ec_event, 'delete' );
			$table_name = $wpdb->prefix . 'ai1ec_events';
			$sql = "DELETE FROM
								$table_name
							WHERE
								post_id = %d";
			// delete from ai1ec_events
			$wpdb->query( $wpdb->prepare( $sql, $pid ) );

			$table_name = $wpdb->prefix . 'ai1ec_event_instances';
			$sql = "DELETE FROM
								$table_name
							WHERE
								post_id = %d";
			// delete from ai1ec_event_instances
			return $wpdb->query( $wpdb->prepare( $sql, $pid ) );
		}
		return true;
	}
	/**
	 * init function
	 *
	 * This function is executed when admin_head hook is called.
	 * Adds CSS and JS files.
	 *
	 * @return void
	 **/
	public function init()
	{
		global $ai1ec_view_helper;
		// Initialize dashboard view
		if( is_admin() ) {
			// =======
			// = CSS =
			// =======
			// include autocomplete style
			$ai1ec_view_helper->admin_enqueue_style( 'autocomplete', 'jquery.autocomplete.css' );
			// include colorpicker style
			$ai1ec_view_helper->admin_enqueue_style( 'colorpicker', 'colorpicker.css' );
			// include add new event style
			$ai1ec_view_helper->admin_enqueue_style( 'ai1ec_add_new_event', 'add_new_event.css' );
			// include datepicker style
			$ai1ec_view_helper->admin_enqueue_style( 'ai1ec_datepicker', 'datepicker.css' );
			// include plugins style
			$ai1ec_view_helper->admin_enqueue_style( 'ai1ec_plugins_common', 'plugins/plugins-common.css' );
		}
		// Initialize front-end view
		else
		{
			// =======
			// = CSS =
			// =======
			$ai1ec_view_helper->theme_enqueue_style( 'ai1ec-general', 'style.css' );
			$ai1ec_view_helper->theme_enqueue_style( 'ai1ec-event', 'event.css' );
			// Load the print style only if the parameter print is set to true.
			if( isset( $_GET['print'] ) && $_GET['print'] === 'true' ) {
				$ai1ec_view_helper->theme_enqueue_style( 'ai1ec-print', 'print.css' );
			}
		}

	}

	/**
	 * meta_box_view function
	 *
	 * Add Events Calculator box to the Add New Event page
	 *
	 * @return void
	 **/
	function meta_box_view() {
		global $ai1ec_view_helper,
		       $ai1ec_events_helper,
		       $post,
		       $wpdb,
		       $ai1ec_settings,
		       $ai1ec_importer_plugin_helper;

		// ==================
		// = Default values =
		// ==================
		$all_day_event    = '';
		$start_timestamp  = '';
		$end_timestamp    = '';
		$show_map         = false;
		$google_map       = '';
		$venue            = '';
		$country          = '';
		$address          = '';
		$city             = '';
		$province         = '';
		$postal_code      = '';
		$contact_name     = '';
		$contact_phone    = '';
		$contact_email    = '';
		$cost             = '';
		$rrule            = '';
		$rrule_text       = '';
		$repeating_event  = false;
		$exrule           = '';
		$exrule_text      = '';
		$exclude_event    = false;
		$exdate           = '';
		$show_coordinates = false;
		$longitude        = '';
		$latitude         = '';
		$coordinates      = '';

		try
	 	{
			$event = new Ai1ec_Event( $post->ID );

			// Existing event was found. Initialize form values with values from
			// event object.

			$all_day_event    = $event->allday ? 'checked="checked"' : '';


			$start_timestamp  = $ai1ec_events_helper->gmt_to_local( $event->start );
			$end_timestamp 	  = $ai1ec_events_helper->gmt_to_local( $event->end );

			$multi_day        = $event->multiday;

			$show_map         = $event->show_map;
			$google_map       = $show_map ? 'checked="checked"' : '';

			$show_coordinates = $event->show_coordinates;
			$coordinates      = $show_coordinates ? 'checked="checked"' : '';
			$longitude        = $event->longitude !== NULL ? floatval( $event->longitude ) : '';
			$latitude         = $event->latitude !== NULL ?  floatval( $event->latitude ) : '';
			// There is a known bug in Wordpress (https://core.trac.wordpress.org/ticket/15158) that saves 0 to the DB instead of null.
			// We handle a special case here to avoid having the fields with a value of 0 when the user never inputted any coordinates
			if ( ! $show_coordinates ) {
				$longitude = '';
				$latitude = '';
			}

			$venue            = $event->venue;
			$country          = $event->country;
			$address          = $event->address;
			$city             = $event->city;
			$province         = $event->province;
			$postal_code      = $event->postal_code;
			$contact_name     = $event->contact_name;
			$contact_phone    = $event->contact_phone;
			$contact_email    = $event->contact_email;
			$cost             = $event->cost;
			$rrule            = empty( $event->recurrence_rules ) ? '' : $ai1ec_events_helper->ics_rule_to_local( $event->recurrence_rules );
			$exrule           = empty( $event->exception_rules )  ? '' : $ai1ec_events_helper->ics_rule_to_local( $event->exception_rules );
			$exdate           = empty( $event->exception_dates )  ? '' : $ai1ec_events_helper->exception_dates_to_local( $event->exception_dates );
			$repeating_event  = empty( $rrule )  ? false : true;
			$exclude_event    = empty( $exrule ) ? false : true;
			$facebook_status  = $event->facebook_status;

			if( $repeating_event )
				$rrule_text = $ai1ec_events_helper->rrule_to_text( $rrule );

			if( $exclude_event )
				$exrule_text = $ai1ec_events_helper->rrule_to_text( $exrule );
		}
		catch( Ai1ec_Event_Not_Found $e ) {
			// Event does not exist.
			// Leave form fields undefined (= zero-length strings)
			$event = null;
		}

		// Time zone
		$timezone = get_option( 'gmt_offset' );
		$timezone = sprintf( '(GMT%+d:%02d)', intval( $timezone ), ( abs( $timezone ) * 60 ) % 60 );

		// ===============================
		// = Display event time and date =
		// ===============================
		$args = array(
			'all_day_event'   => $all_day_event,
			'start_timestamp' => $start_timestamp,
			'end_timestamp'   => $end_timestamp,
			'repeating_event' => $repeating_event,
			'rrule'           => $rrule,
			'rrule_text'      => $rrule_text,
			'exclude_event'   => $exclude_event,
			'exrule'          => $exrule,
			'exrule_text'     => $exrule_text,
			'timezone'        => $timezone,
			'exdate'          => $exdate
		);
		$ai1ec_view_helper->display_admin( 'box_time_and_date.php', $args );

		// =================================================
		// = Display event location details and Google map =
		// =================================================
		$args = array(
			'venue'            => $venue,
			'country'          => $country,
			'address'          => $address,
			'city'             => $city,
			'province'         => $province,
			'postal_code'      => $postal_code,
			'google_map'       => $google_map,
			'show_map'         => $show_map,
			'show_coordinates' => $show_coordinates,
			'longitude'        => $longitude,
			'latitude'         => $latitude,
			'coordinates'      => $coordinates,
		);
		$ai1ec_view_helper->display_admin( 'box_event_location.php', $args );

		// ======================
		// = Display event cost =
		// ======================
		$args = array(
			'cost' => $cost
		);
		$ai1ec_view_helper->display_admin( 'box_event_cost.php', $args );

		// =========================================
		// = Display organizer contact information =
		// =========================================
		$args = array(
			'contact_name'    => $contact_name,
			'contact_phone'   => $contact_phone,
			'contact_email'   => $contact_email,
		);
		$ai1ec_view_helper->display_admin( 'box_event_contact.php', $args );

		if( $ai1ec_settings->show_publish_button ) {
			$args = array();
			$post_type = $post->post_type;
			$post_type_object = get_post_type_object( $post_type );
			if( current_user_can( $post_type_object->cap->publish_posts ) )
				$args["button_value"] = is_null( $event ) ? __( 'Publish', AI1EC_PLUGIN_NAME ) : __( 'Update', AI1EC_PLUGIN_NAME );
			else
				$args["button_value"] = __( 'Submit for Review', AI1EC_PLUGIN_NAME );

			$ai1ec_view_helper->display_admin( 'box_publish_button.php', $args );
		}

		/*
			TODO Display Eventbrite ticketing
			$ai1ec_view_helper->display( 'box_eventbrite.php' );
		*/
	}

	/**
	 * save_post function
	 *
	 * Saves meta post data
	 *
	 * @param int $post_id Post ID
	 *
	 * @return void
	 **/
	function save_post( $post_id, $post ) {
		global $wpdb, $ai1ec_events_helper, $ai1ec_importer_plugin_helper;

		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times
		if( isset( $_POST[AI1EC_POST_TYPE] ) && ! wp_verify_nonce( $_POST[AI1EC_POST_TYPE], 'ai1ec' ) ) {
			return;
		} else if( ! isset( $_POST[AI1EC_POST_TYPE] ) ) {
			return;
		}

		if( isset( $post->post_status ) && $post->post_status == 'auto-draft' )
		return;

		// verify if this is not inline-editing
		if( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'inline-save' ) {
			return;
		}

		// verify that the post_type is that of an event
		if( isset( $_POST['post_type'] ) && $_POST['post_type'] != AI1EC_POST_TYPE ) {
			return;
		}

		$all_day          = isset( $_POST['ai1ec_all_day_event'] )    ? 1                                             : 0;
		$start_time       = isset( $_POST['ai1ec_start_time'] )       ? $_POST['ai1ec_start_time']                    : '';
		$end_time         = isset( $_POST['ai1ec_end_time'] )         ? $_POST['ai1ec_end_time']                      : '';
		$venue            = isset( $_POST['ai1ec_venue'] )            ? stripslashes( $_POST['ai1ec_venue'] )         : '';
		$address          = isset( $_POST['ai1ec_address'] )          ? stripslashes( $_POST['ai1ec_address'] )       : '';
		$city             = isset( $_POST['ai1ec_city'] )             ? stripslashes( $_POST['ai1ec_city'] )          : '';
		$province         = isset( $_POST['ai1ec_province'] )         ? stripslashes( $_POST['ai1ec_province'] )      : '';
		$postal_code      = isset( $_POST['ai1ec_postal_code'] )      ? stripslashes( $_POST['ai1ec_postal_code'] )   : '';
		$country          = isset( $_POST['ai1ec_country'] )          ? stripslashes( $_POST['ai1ec_country'] )       : '';
		$google_map       = isset( $_POST['ai1ec_google_map'] )       ? 1                                             : 0;
		$cost             = isset( $_POST['ai1ec_cost'] )             ? stripslashes( $_POST['ai1ec_cost'] )          : '';
		$contact_name     = isset( $_POST['ai1ec_contact_name'] )     ? stripslashes( $_POST['ai1ec_contact_name'] )  : '';
		$contact_phone    = isset( $_POST['ai1ec_contact_phone'] )    ? stripslashes( $_POST['ai1ec_contact_phone'] ) : '';
		$contact_email    = isset( $_POST['ai1ec_contact_email'] )    ? stripslashes( $_POST['ai1ec_contact_email'] ) : '';
		$show_coordinates = isset( $_POST['ai1ec_input_coordinates'] )? 1                                             : 0;
		$longitude        = isset( $_POST['ai1ec_longitude'] )        ? stripslashes( $_POST['ai1ec_longitude'] )     : '';
		$latitude         = isset( $_POST['ai1ec_latitude'] )         ? stripslashes( $_POST['ai1ec_latitude'] )      : '';

		$rrule  = null;
		$exrule = null;
		$exdate = null;

		// if rrule is set, convert it from local to UTC time
		if( isset( $_POST['ai1ec_repeat'] ) && ! empty( $_POST['ai1ec_repeat'] ) )
			$rrule = $ai1ec_events_helper->ics_rule_to_gmt( $_POST['ai1ec_rrule'] );

		// if exrule is set, convert it from local to UTC time
		if( isset( $_POST['ai1ec_exclude'] ) && ! empty( $_POST['ai1ec_exclude'] ) )
			$exrule = $ai1ec_events_helper->ics_rule_to_gmt( $_POST['ai1ec_exrule'] );

		// if exdate is set, convert it from local to UTC time
		if( isset( $_POST['ai1ec_exdate'] ) && ! empty( $_POST['ai1ec_exdate'] ) )
			$exdate = $ai1ec_events_helper->exception_dates_to_gmt( $_POST['ai1ec_exdate'] );

		$is_new = false;
		$event 	= null;
		try {
			$event = new Ai1ec_Event( $post_id ? $post_id : null );
		} catch( Ai1ec_Event_Not_Found $e ) {
			// Post exists, but event data hasn't been saved yet. Create new event
			// object.
			$is_new = true;
			$event = new Ai1ec_Event();
			$event->post_id = $post_id;
		}

		$event->start               = $ai1ec_events_helper->local_to_gmt( $start_time );
		$event->end                 = $ai1ec_events_helper->local_to_gmt( $end_time );
		$event->allday              = $all_day;
		$event->venue               = $venue;
		$event->address             = $address;
		$event->city                = $city;
		$event->province            = $province;
		$event->postal_code         = $postal_code;
		$event->country             = $country;
		$event->show_map            = $google_map;
		$event->cost                = $cost;
		$event->contact_name        = $contact_name;
		$event->contact_phone       = $contact_phone;
		$event->contact_email       = $contact_email;
		$event->recurrence_rules    = $rrule;
		$event->exception_rules     = $exrule;
		$event->exception_dates     = $exdate;
		$event->show_coordinates    = $show_coordinates;
		$event->longitude           = trim( $longitude ) !== '' ? (float) $longitude : NULL;
		$event->latitude            = trim( $latitude ) !== '' ? (float) $latitude : NULL;
		// if we are not saving a draft, give the event to the plugins
		if( $post->post_status !== 'draft' ) {
			$ai1ec_importer_plugin_helper->handle_post_event( $event, 'save' );
		}


		$event->save( ! $is_new );

		$ai1ec_events_helper->delete_event_cache( $post_id );
		$ai1ec_events_helper->cache_event( $event );
		return;
	}

	/**
	 * post_updated_messages function
	 *
	 * Filter success messages returned by WordPress when an event post is
	 * updated/saved.
	 */
	function post_updated_messages( $messages )
	{
		global $post, $post_ID;

		$messages[AI1EC_POST_TYPE] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __( 'Event updated. <a href="%s">View event</a>', AI1EC_PLUGIN_NAME ), esc_url( get_permalink( $post_ID ) ) ),
			2 => __( 'Custom field updated.', AI1EC_PLUGIN_NAME ),
			3 => __( 'Custom field deleted.', AI1EC_PLUGIN_NAME ),
			4 => __( 'Event updated.', AI1EC_PLUGIN_NAME ),
			/* translators: %s: date and time of the revision */
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Event restored to revision from %s', AI1EC_PLUGIN_NAME ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __( 'Event published. <a href="%s">View event</a>', AI1EC_PLUGIN_NAME ), esc_url( get_permalink($post_ID) ) ),
			7 => __( 'Event saved.' ),
			8 => sprintf( __( 'Event submitted. <a target="_blank" href="%s">Preview event</a>', AI1EC_PLUGIN_NAME ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			9 => sprintf( __( 'Event scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview event</a>', AI1EC_PLUGIN_NAME ),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i', AI1EC_PLUGIN_NAME ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
			10 => sprintf( __( 'Event draft updated. <a target="_blank" href="%s">Preview event</a>', AI1EC_PLUGIN_NAME ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		);

		return $messages;
	}

	/**
	 * event_content function
	 *
	 * Filter event post content by inserting relevant details of the event
	 * alongside the regular post content.
	 *
	 * @param string $content Post/Page content
	 *
	 * @return string         Post/Page content
	 **/
	function event_content( $content )
	{
		global $ai1ec_events_helper;
		if( get_post_type() == AI1EC_POST_TYPE ) {
			$event = $ai1ec_events_helper->get_event( get_the_ID() );
			$content = $this->get_view( $event, $content );
		}
		return $content;
	}

	/**
	 * event_excerpt function
	 *
	 * Overrides what wp_trim_excerpt() returned if the post is an event,
	 * and outputs better rich-text (but not too rich) excerpt instead.
	 *
	 * @return void
	 **/
	function event_excerpt( $text )
 	{
		global $ai1ec_view_helper,
		       $ai1ec_events_helper;

		if( get_post_type() != AI1EC_POST_TYPE )
			return $text;

		$event = new Ai1ec_Event( get_the_ID() );

		ob_start();

		$this->excerpt_view( $event );

		// Re-apply any filters to the post content that normally would have been
		// applied if it weren't for our interference (below).
		echo
		 	shortcode_unautop( wpautop(
				$ai1ec_events_helper->trim_excerpt( $event->post->post_content )
			) );

		$page_content = ob_get_contents();
		ob_end_clean();

		return $page_content;
	}

	/**
	 * event_excerpt_noautop function
	 *
	 * Conditionally apply wpautop() filter to content, only if it is not an
	 * event.
	 *
	 * @return void
	 **/
	function event_excerpt_noautop( $content )
	{
		if( get_post_type() != AI1EC_POST_TYPE )
			return wpautop( $content );
		return $content;
	}

	/**
	 * get_view function
	 *
	 * Returns the appropriate output to prepend to an event post, depending on
	 * WP loop context.
	 *
	 * @param Ai1ec_Event $event  The event post being displayed
	 * @param string $content     The post's original content
	 *
	 * @return string             The event data markup to prepend to the post content
	 **/
	function get_view( &$event, &$content )
	{
		global $ai1ec_view_helper;

		ob_start();

		if( is_single() ) {
			$this->single_view( $event );
		} else {
			$this->multi_view( $event );
		}
		echo $content;

		if( is_single() )
			$this->single_event_footer( $event );

		$page_content = ob_get_contents();
		ob_end_clean();

		return $page_content;
	}

	/**
	 * single_view function
	 *
	 * Outputs event-specific details as HTML to be prepended to post content
	 * when displayed as a single page.
	 *
	 * @param Ai1ec_Event $event  The event being displayed
	 *
	 * @return void
	 **/
	function single_view( &$event )
	{
		global $ai1ec_view_helper,
		       $ai1ec_calendar_helper,
		       $ai1ec_settings;

		$subscribe_url = AI1EC_EXPORT_URL . "&ai1ec_post_ids=$event->post_id";
		$subscribe_url = str_replace( 'webcal://', 'http://', $subscribe_url );

		$args = array(
			'event'                   => &$event,
			'recurrence'              => $event->recurrence_html,
			'exclude'                 => $event->exclude_html,
			'categories'              => $event->categories_html,
			'tags'                    => $event->tags_html,
			'location'                => nl2br( $event->location ),
			'map'                     => $this->get_map_view( $event ),
			'contact'                 => $event->contact_html,
			'calendar_url'            => $ai1ec_calendar_helper->get_calendar_url( $event ),
			'subscribe_url'           => $subscribe_url,
			'google_url'              => 'http://www.google.com/calendar/render?cid=' . urlencode( $subscribe_url ),
			'show_subscribe_buttons'  => ! $ai1ec_settings->turn_off_subscription_buttons
		);
		$ai1ec_view_helper->display_theme( 'event-single.php', $args );
	}

	/**
	 * multi_view function
	 *
	 * Outputs event-specific details as HTML to be prepended to post content
	 * when displayed in a loop alongside other posts.
	 *
	 * @param Ai1ec_Event $event  The event being displayed
	 *
	 * @return void
	 **/
	function multi_view( &$event )
	{
		global $ai1ec_view_helper,
		       $ai1ec_calendar_helper;

		$location = str_replace( "\n", ', ', rtrim( $event->location ) );

		$args = array(
			'event' => &$event,
			'recurrence' => $event->recurrence_html,
			'categories' => $event->categories_html,
			'tags' => $event->tags_html,
			'location' => $location,
			'contact' => $event->contact_html,
			'calendar_url' => $ai1ec_calendar_helper->get_calendar_url( $event ),
		);
		$ai1ec_view_helper->display_theme( 'event-multi.php', $args );
	}

	/**
	 * excerpt_view function
	 *
	 * Outputs event-specific details as HTML to be prepended to post content
	 * when displayed in an excerpt format.
	 *
	 * @param Ai1ec_Event $event  The event being displayed
	 *
	 * @return void
	 **/
	function excerpt_view( &$event )
	{
		global $ai1ec_view_helper,
		       $ai1ec_calendar_helper;

		$location = str_replace( "\n", ', ', rtrim( $event->location ) );

		$args = array(
			'event'    => &$event,
			'location' => $location,
		);
		$ai1ec_view_helper->display_theme( 'event-excerpt.php', $args );
	}

	/**
	 * get_map_view function
	 *
	 * Returns HTML markup displaying a Google map of the given event, if the event
	 * has show_map set to true. Returns a zero-length string otherwise.
	 *
	 * @return void
	 **/
	function get_map_view( &$event )
	{
		global $ai1ec_view_helper, $ai1ec_events_helper, $ai1ec_settings;

		if( ! $event->show_map )
			return '';

		$location = $ai1ec_events_helper->get_latlng( $event );
		if ( ! $location ) {
			$location = $event->address;
		}

		$args = array(
			'address'                 => $location,
			'gmap_url_link'           => $ai1ec_events_helper->get_gmap_url( $event, false ),
			'hide_maps_until_clicked' => $ai1ec_settings->hide_maps_until_clicked,
		);
		return $ai1ec_view_helper->get_theme_view( 'event-map.php', $args );
	}

	/**
	 * single_event_footer function
	 *
	 * Outputs any markup that should appear below the post's content on the
	 * single post page for this event.
	 *
	 * @return void
	 **/
	function single_event_footer( &$event )
	{
		global $ai1ec_view_helper;

		$args = array(
			'event' => &$event,
		);
		return $ai1ec_view_helper->display_theme( 'event-single-footer.php', $args );
	}

	/**
	 * events_categories_add_form_fields function
	 *
	 *
	 *
	 * @return void
	 **/
	 function events_categories_add_form_fields() {
		global $ai1ec_view_helper;

		$args = array( 'edit' => false );
		$ai1ec_view_helper->display_admin( 'event_categories-color_picker.php', $args );
	 }

	 /**
 	 * events_categories_edit_form_fields function
 	 *
 	 *
 	 *
 	 * @return void
 	 **/
 	 function events_categories_edit_form_fields( $term ) {
		global $ai1ec_view_helper, $wpdb;

		$table_name = $wpdb->prefix . 'ai1ec_event_category_colors';
		$color      = $wpdb->get_var( $wpdb->prepare( "SELECT term_color FROM {$table_name} WHERE term_id = %d ", $term->term_id ) );

		$style = '';
		$clr   = '';

		if( ! is_null( $color ) && ! empty( $color ) ) {
			$style = 'style="background-color: ' . $color . '"';
			$clr = $color;
		}

		$args = array(
			'style' => $style,
			'color' => $clr,
			'edit'  => true,
		);
		$ai1ec_view_helper->display_admin( 'event_categories-color_picker.php', $args );
	}

	 /**
	  * edited_events_categories function
	  *
	  *
	  *
	  * @return void
	  **/
	function created_events_categories( $term_id ) {
	  global $wpdb;
	  $tag_color_value = '';
	  if( isset( $_POST["tag-color-value"] ) && ! empty( $_POST["tag-color-value"] ) ) {
	    $tag_color_value = $_POST["tag-color-value"];
	  }

	  $table_name = $wpdb->prefix . 'ai1ec_event_category_colors';
	  $wpdb->insert( $table_name, array( 'term_id' => $term_id, 'term_color' => $tag_color_value ), array( '%d', '%s' ) );
	}

	function edited_events_categories( $term_id ) {
	  global $wpdb;
	  $tag_color_value = '';
	  if( isset( $_POST["tag-color-value"] ) && ! empty( $_POST["tag-color-value"] ) ) {
	    $tag_color_value = $_POST["tag-color-value"];
	  }

	  $table_name = $wpdb->prefix . 'ai1ec_event_category_colors';
	  $term = $wpdb->get_var( $wpdb->prepare( "SELECT term_id FROM {$table_name} WHERE term_id = %d", $term_id ) );

	  if( is_null( $term ) ) {
	    // term doesn't exist, create it
	    $wpdb->insert( $table_name, array( 'term_id' => $term_id, 'term_color' => $tag_color_value ), array( '%d', '%s' ) );
	  } else {
	    // term exist, update it
	    $wpdb->update( $table_name, array( 'term_color' => $tag_color_value ), array( 'term_id' => $term_id ), array( '%s' ), array( '%d' ) );
	  }


	}
}
// END class
