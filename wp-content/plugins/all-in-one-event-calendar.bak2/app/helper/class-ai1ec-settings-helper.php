<?php
//
//  class-ai1ec-settings-helper.php
//  all-in-one-event-calendar
//
//  Created by The Seed Studio on 2011-07-13.
//

/**
 * Ai1ec_Settings_Helper class
 *
 * @package Helpers
 * @author time.ly
 **/
class Ai1ec_Settings_Helper {
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
	 * Default constructor
	 **/
	private function __construct() { }

	/**
	 * wp_pages_dropdown function
	 *
	 * Display drop-down list selector of pages, including an "Auto-Create New Page"
	 * option which causes the plugin to generate a new page on user's behalf.
	 *
	 * @param string $field_name
	 * @param int  $selected_page_id
	 * @param string $auto_page
	 * @param bool $include_disabled
	 *
	 * @return string
	 **/
	function wp_pages_dropdown( $field_name, $selected_page_id = 0, $auto_page = '', $include_disabled = false ) {
		global $wpdb;
		ob_start();
		$query = "SELECT
								*
							FROM
								{$wpdb->posts}
							WHERE
								post_status = %s
								AND
								post_type = %s";

		$query = $wpdb->prepare( $query, 'publish', 'page' );
		$results = $wpdb->get_results( $query );
		$pages = array();
		if( $results ) {
			$pages = $results;
		}
		$selected_title = '';
		?>
		<select class="inputwidth" name="<?php echo $field_name; ?>"
		        id="<?php echo $field_name; ?>"
		        class="wafp-dropdown wafp-pages-dropdown">
			<?php if( ! empty( $auto_page ) ) { ?>
				<option value="__auto_page:<?php echo $auto_page; ?>">
					<?php _e( '- Auto-Create New Page -', AI1EC_PLUGIN_NAME ); ?>
				</option>
			<?php }
			foreach( $pages as $page ) {
				if( $selected_page_id == $page->ID ) {
					$selected = ' selected="selected"';
					$selected_title = $page->post_title;
				} else {
					$selected = '';
				}
				?>
				<option value="<?php echo $page->ID ?>" <?php echo $selected; ?>>
					<?php echo $page->post_title ?>
				</option>
			<?php } ?>
			</select>
		<?php
		if( is_numeric( $selected_page_id ) && $selected_page_id > 0 ) {
			$permalink = get_permalink( $selected_page_id );
			?>
			<div><a href="<?php echo $permalink ?>" target="_blank">
				<?php printf( __( 'View "%s" »', AI1EC_PLUGIN_NAME ), $selected_title ) ?>
			</a></div>
			<?php
		}
		return ob_get_clean();
	}

	/**
	 * get_week_dropdown function
	 *
	 * Creates the dropdown element for selecting start of the week
	 *
	 * @param int $week_start_day Selected start day
	 *
	 * @return String dropdown element
	 **/
	function get_week_dropdown( $week_start_day ) {
		global $wp_locale;
		ob_start();
		?>
		<select class="inputwidth" name="week_start_day" id="week_start_day">
		<?php
		for( $day_index = 0; $day_index <= 6; $day_index++ ) :
			$selected = ( $week_start_day == $day_index ) ? 'selected="selected"' : '';
			echo "\n\t<option value='" . esc_attr($day_index) . "' $selected>" . $wp_locale->get_weekday($day_index) . '</option>';
		endfor;
		?>
		</select>
		<?php
		return ob_get_clean();
	}

	/**
	 * get_view_options function
	 *
	 * @return void
	 **/
	function get_view_options( $view = null ) {
		global $ai1ec_settings;

		ob_start();
		?>
		<div>
			<table>
				<tbody>
					<tr class="ai1ec-admin-view-head">
						<td></td>
						<td>Enabled</td>
						<td>Default</td>
					</tr>
					<?php foreach ( Ai1ec_Settings::$view_names as $key => $name ) {
						$this_view_bool = 'view_' . $key . '_enabled';
						$is_view_enabled = $ai1ec_settings->$this_view_bool;
						?>
						<tr>
							<td>
								<?php _e( $name ) ?>
							</td>
							<td class="ai1ec-control-table-column">
								<input class="checkbox toggle-view" type="checkbox" name="<?php echo $this_view_bool ?>" value="1"
									<?php echo $is_view_enabled ? 'checked="checked"' : ''; ?> />
							</td>
							<td class="ai1ec-control-table-column">
								<input class="toggle-default-view" type="radio" name="default_calendar_view" value="<?php echo $key ?>"
									<?php if ( $ai1ec_settings->default_calendar_view == $key ) : echo 'checked="checked"'; endif; ?>>
							</td>
						</tr>
					<?php } ?>

				</tbody>
			</table>

		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * get_timezone_dropdown function
	 *
	 *
	 *
	 * @return void
	 **/
	function get_timezone_dropdown( $timezone = null ) {
		$timezone_identifiers = DateTimeZone::listIdentifiers();
		ob_start();
		?>
		<select id="timezone" name="timezone">
			<?php foreach( $timezone_identifiers as $value ) : ?>
				<?php if( preg_match( '/^(Africa|America|Antartica|Arctic|Asia|Atlantic|Australia|Europe|Indian|Pacific)\//', $value ) ) : ?>
					<?php $ex = explode( "/", $value );  //obtain continent,city ?>
					<?php if( isset( $continent ) && $continent != $ex[0] ) : ?>
						<?php if( ! empty( $continent ) ) : ?>
							</optgroup>
						<?php endif ?>
						<optgroup label="<?php echo $ex[0] ?>">
					<?php endif ?>

					<?php $city = isset( $ex[2] ) ? $ex[2] : $ex[1]; $continent = $ex[0]; ?>
					<option value="<?php echo $value ?>" <?php echo $value == $timezone ? 'selected' : '' ?>><?php echo $city ?></option>
				<?php endif ?>
			<?php endforeach ?>
			</optgroup>
		</select>
		<?php
		return ob_get_clean();
	}

	/**
	* get_date_format_dropdown function
	*
	* @return string
	**/
	function get_date_format_dropdown( $view = null ) {
		ob_start();
		?>
		<select name="input_date_format">
			<option value="def" <?php echo $view == 'def' ? 'selected' : '' ?>>
				<?php _e( 'Default (d/m/y)', AI1EC_PLUGIN_NAME ) ?>
			</option>
			<option value="us" <?php echo $view == 'us' ? 'selected' : '' ?>>
				<?php _e( 'US (m/d/y)', AI1EC_PLUGIN_NAME ) ?>
			</option>
			<option value="iso" <?php echo $view == 'iso' ? 'selected' : '' ?>>
				<?php _e( 'ISO 8601 (y-m-d)', AI1EC_PLUGIN_NAME ) ?>
			</option>
			<option value="dot" <?php echo $view == 'dot' ? 'selected' : '' ?>>
				<?php _e( 'Dotted (m.d.y)', AI1EC_PLUGIN_NAME ) ?>
			</option>

		</select>
		<?php
		return ob_get_clean();
	}

	/**
	 * get_cron_freq_dropdown function
	 *
	 * @return void
	 **/
	function get_cron_freq_dropdown( $cron_freq = null ) {
		ob_start();
		?>
		<select name="cron_freq">
			<option value="hourly" <?php echo $cron_freq == 'hourly' ? 'selected' : ''; ?>>
				<?php _e( 'Hourly', AI1EC_PLUGIN_NAME ) ?>
			</option>
			<option value="twicedaily" <?php echo $cron_freq == 'twicedaily' ? 'selected' : '' ?>>
				<?php _e( 'Twice Daily', AI1EC_PLUGIN_NAME ) ?>
			</option>
			<option value="daily" <?php echo $cron_freq == 'daily' ? 'selected' : '' ?>>
				<?php _e( 'Daily', AI1EC_PLUGIN_NAME ) ?>
			</option>
		</select>
		<?php
		return ob_get_clean();
	}

	/**
	 * get_feed_rows function
	 *
	 * Creates feed rows to display on settings page
	 *
	 * @return String feed rows
	 **/
	function get_feed_rows() {
		global $wpdb,
					 $ai1ec_view_helper;

		// Select all added feeds
		$table_name = $wpdb->prefix . 'ai1ec_event_feeds';
		$sql = "SELECT * FROM {$table_name}";
		$rows = $wpdb->get_results( $sql );

		ob_start();
		foreach( $rows as $row ) :
			$feed_category = get_term( $row->feed_category, 'events_categories' );
			$table_name = $wpdb->prefix . 'ai1ec_events';
			$sql = "SELECT COUNT(*) FROM {$table_name} WHERE ical_feed_url = '%s'";
			$events = $wpdb->get_var( $wpdb->prepare( $sql, $row->feed_url ) );
			$args = array(
				'feed_url' 			 => $row->feed_url,
				'event_category' => $feed_category->name,
				'tags'					 => stripslashes( $row->feed_tags ),
				'feed_id'				 => $row->feed_id,
				'events'				 => $events
			);
			$ai1ec_view_helper->display_admin( 'feed_row.php', $args );
		endforeach;

		return ob_get_clean();
	}

	/**
	 * get_event_categories_select function
	 *
	 * Creates the dropdown element for selecting feed category
	 *
	 * @param int|null $selected The selected category or null
	 *
	 * @return String dropdown element
	 **/
	function get_event_categories_select( $selected = null) {
		ob_start();
		?>
		<select name="ai1ec_feed_category" id="ai1ec_feed_category">
		<?php
		foreach( get_terms( 'events_categories', array( 'hide_empty' => false ) ) as $term ) :
		?>
			<option value="<?php echo $term->term_id; ?>" <?php echo ( $selected === $term->term_id ) ? 'selected' : '' ?>>
				<?php echo $term->name; ?>
			</option>
		<?php
		endforeach;
		?>
		</select>
		<?php
		return ob_get_clean();
	}

  /**
   * Displays the General Settings meta box.
   *
   * @return void
   */
  function general_settings_meta_box( $object, $box ) {
    global $ai1ec_view_helper,
           $ai1ec_settings;

    $calendar_page                  = $this->wp_pages_dropdown(
      'calendar_page_id',
      $ai1ec_settings->calendar_page_id,
      __( 'Calendar', AI1EC_PLUGIN_NAME )
    );
    $week_start_day                 = $this->get_week_dropdown( get_option( 'start_of_week' ) );
    $posterboard_events_per_page    = $ai1ec_settings->posterboard_events_per_page;
    $agenda_events_per_page         = $ai1ec_settings->agenda_events_per_page;
    $include_events_in_rss          =
      '<input type="checkbox" name="include_events_in_rss"
        id="include_events_in_rss" value="1"'
        . ( $ai1ec_settings->include_events_in_rss ? ' checked="checked"' : '' )
        . '/>';
    $exclude_from_search            = $ai1ec_settings->exclude_from_search ? 'checked=checked' : '';
    $show_publish_button            = $ai1ec_settings->show_publish_button ? 'checked=checked' : '';
    $hide_maps_until_clicked        = $ai1ec_settings->hide_maps_until_clicked ? 'checked=checked' : '';
    $agenda_events_expanded         = $ai1ec_settings->agenda_events_expanded ? 'checked=checked' : '';
    $turn_off_subscription_buttons  = $ai1ec_settings->turn_off_subscription_buttons ? 'checked=checked' : '';
    $show_create_event_button       = $ai1ec_settings->show_create_event_button ? 'checked=checked' : '';
    $inject_categories              = $ai1ec_settings->inject_categories ? 'checked=checked' : '';
    $geo_region_biasing             = $ai1ec_settings->geo_region_biasing ? 'checked=checked' : '';
    $input_date_format              = $this->get_date_format_dropdown( $ai1ec_settings->input_date_format );
    $input_24h_time                 = $ai1ec_settings->input_24h_time ? 'checked=checked' : '';
    $default_calendar_view          = $this->get_view_options( $ai1ec_settings->default_calendar_view );
    $timezone_control               = $this->get_timezone_dropdown( $ai1ec_settings->timezone );
    $disable_autocompletion         = $ai1ec_settings->disable_autocompletion ? 'checked=checked' : '';
    $show_location_in_title         = $ai1ec_settings->show_location_in_title ? 'checked=checked' : '';
    $show_year_in_agenda_dates      = $ai1ec_settings->show_year_in_agenda_dates ? 'checked=checked' : '';

    $args = array(
      'calendar_page'                 => $calendar_page,
      'default_calendar_view'         => $default_calendar_view,
      'week_start_day'                => $week_start_day,
      'posterboard_events_per_page'   => $posterboard_events_per_page,
      'agenda_events_per_page'        => $agenda_events_per_page,
      'exclude_from_search'           => $exclude_from_search,
      'show_publish_button'           => $show_publish_button,
      'hide_maps_until_clicked'       => $hide_maps_until_clicked,
      'agenda_events_expanded'        => $agenda_events_expanded,
      'turn_off_subscription_buttons' => $turn_off_subscription_buttons,
      'show_create_event_button'      => $show_create_event_button,
      'inject_categories'             => $inject_categories,
      'input_date_format'             => $input_date_format,
      'input_24h_time'                => $input_24h_time,
      'show_timezone'                 => ! get_option( 'timezone_string' ),
      'timezone_control'              => $timezone_control,
      'geo_region_biasing'            => $geo_region_biasing,
      'disable_autocompletion'	      => $disable_autocompletion,
      'show_location_in_title'	      => $show_location_in_title,
      'show_year_in_agenda_dates'     => $show_year_in_agenda_dates,
    );
    $ai1ec_view_helper->display_admin( 'box_general_settings.php', $args );
  }

	/**
	 * Displays the Advanced Settings meta box.
	 *
	 * @return void
	 */
	function advanced_settings_meta_box( $object, $box ) {
	  global $ai1ec_view_helper,
					 $ai1ec_settings;

    $event_platform             = $ai1ec_settings->event_platform_active ? 'checked="checked"' : '';
    $event_platform_disabled    = AI1EC_EVENT_PLATFORM ? 'disabled="disabled"' : '';
    $event_platform_strict      = $ai1ec_settings->event_platform_strict ? 'checked="checked"' : '';

		$args = array(
      'calendar_css_selector'   => $ai1ec_settings->calendar_css_selector,
      'event_platform'          => $event_platform,
      'event_platform_disabled' => $event_platform_disabled,
      'event_platform_strict'   => $event_platform_strict,
      'display_event_platform'  => is_super_admin(),
	  );
	  $ai1ec_view_helper->display_admin( 'box_advanced_settings.php', $args );
	}

	/**
   * Renders the contents of the Calendar Feeds meta box.
	 *
	 * @return void
	 */
	function feeds_meta_box( $object, $box )
	{
		global $ai1ec_view_helper;

		$ai1ec_view_helper->display_admin( 'box_feeds.php' );
	}
	/**
	 * Renders the contents of the Support meta box.
	 *
	 * @return void
	 */
	function support_meta_box( $object, $box ) {
		global $ai1ec_view_helper;
		include_once( ABSPATH . WPINC . '/feed.php' );
		// Initialize new feed
		$newsItems = array();
		$feed      = fetch_feed( AI1EC_RSS_FEED );
		$newsItems = is_wp_error( $feed ) ? array() : $feed->get_items();
		$ai1ec_view_helper->display_admin( 'box_support.php', array( 'news' => $newsItems ) );
	}

  /**
   * This is called when the settings page is loaded, so that any additional
   * custom meta boxes can be added by other plugins, themes, etc.
   *
   * @return void
   */
  function add_settings_meta_boxes(){
    global $ai1ec_settings;
    do_action( 'add_meta_boxes', $ai1ec_settings->settings_page );
  }

	/**
	 * This is called when the feeds page is loaded, so that any additional
   * custom meta boxes can be added by other plugins, themes, etc.
	 *
	 * @return void
	 */
	function add_feeds_meta_boxes(){
		global $ai1ec_settings;
		do_action( 'add_meta_boxes', $ai1ec_settings->feeds_page );
	}
}
// END class
