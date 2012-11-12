<?php
//
//  class-ai1ec-settings.php
//  all-in-one-event-calendar
//
//  Created by The Seed Studio on 2011-07-13.
//

/**
 * Ai1ec_Settings class
 *
 * @package Models
 * @author time.ly
 **/
class Ai1ec_Settings {
	/**
	 * _instance class variable
	 *
	 * Class instance
	 *
	 * @var null | object
	 **/
	private static $_instance = NULL;

	/**
	 * Class variable (constant, really) to associate views with their names.
	 *
	 * @var array
	 */
	public static $view_names = NULL;

	/**
	 * posterboard_events_per_page class variable
	 *
	 * @var int
	 **/
	var $posterboard_events_per_page;

	/**
	 * calendar_page_id class variable
	 *
	 * @var int
	 **/
	var $calendar_page_id;

	/**
	 * default_calendar_view class variable
	 *
	 * @var string
	 **/
	var $default_calendar_view;

	/**
	 * view_month_enabled class variable
	 *
	 * @var string
	 **/
	var $view_month_enabled;

	/**
	 * view_week_enabled class variable
	 *
	 * @var string
	 **/
	var $view_week_enabled;

	/**
	 * view_oneday_enabled class variable
	 *
	 * @var string
	 **/
	var $view_oneday_enabled;

	/**
	 * view_agenda_enabled class variable
	 *
	 * @var string
	 **/
	var $view_agenda_enabled;

	/**
	 * week_start_day class variable
	 *
	 * @var int
	 **/
	var $week_start_day;

	/**
	 * agenda_events_per_page class variable
	 *
	 * @var int
	 **/
	var $agenda_events_per_page;

	/**
	 * calendar_css_selector class variable
	 *
	 * @var string
	 **/
	var $calendar_css_selector;

	/**
	 * include_events_in_rss class variable
	 *
	 * @var bool
	 **/
	var $include_events_in_rss;

	/**
	 * allow_publish_to_facebook class variable
	 *
	 * @var bool
	 **/
	var $allow_publish_to_facebook;

	/**
	 * facebook_credentials class variable
	 *
	 * @var array
	 **/
	var $facebook_credentials;

	/**
	 * user_role_can_create_event class variable
	 *
	 * @var bool
	 **/
	var $user_role_can_create_event;

	/**
	 * cron_freq class variable
	 *
	 * Cron frequency
	 *
	 * @var string
	 **/
	var $cron_freq;

	/**
	 * timezone class variable
	 *
	 * @var string
	 **/
	var $timezone;

	/**
	 * exclude_from_search class variable
	 *
	 * Whether to exclude events from search results
	 * @var bool
	 **/
	var $exclude_from_search;

	/**
	 * show_publish_button class variable
	 *
	 * Display publish button at the bottom of the
	 * submission form
	 *
	 * @var bool
	 **/
	var $show_publish_button;

	/**
	 * hide_maps_until_clicked class variable
	 *
	 * When this setting is on, instead of showing the Google Map,
	 * show a dotted-line box containing the text "Click to view map",
	 * and when clicked, this box is replaced by the Google Map.
	 *
	 * @var bool
	 **/
	var $hide_maps_until_clicked;

	/**
	 * agenda_events_expanded class variable
	 *
	 * When this setting is on, events are expanded
	 * in agenda view
	 *
	 * @var bool
	 **/
	var $agenda_events_expanded;

	/**
	 * show_create_event_button class variable
	 *
	 * Display "Post Your Event" button on the calendar page for those users with
	 * the privilege.
	 *
	 * @var bool
	 **/
	var $show_create_event_button;

	/**
	 * turn_off_subscription_buttons class variable
	 *
	 * Hides "Subscribe"/"Add to Calendar" and same Google buttons in calendar and
	 * single event views
	 *
	 * @var bool
	 **/
	var $turn_off_subscription_buttons;

	/**
	 * inject_categories class variable
	 *
	 * Include Event Categories as part of the output of the wp_list_categories()
	 * template tag.
	 *
	 * @var bool
	 **/
	var $inject_categories;

	/**
	 * input_date_format class variable
	 *
	 * Date format used for date input. For supported formats
	 * @see jquery.calendrical.js
	 *
	 * @var string
	 **/
	var $input_date_format;

	/**
	 * input_24h_time class variable
	 *
	 * Use 24h time in time pickers.
	 *
	 * @var bool
	 **/
	var $input_24h_time;

	/**
	* settings_page class variable
	*
	* Stores a reference to the settings page added using the
	* add_submenu_page function.
	*
	* @var object
	*/
	var $settings_page;

	/**
	 * feeds_page class variable
	 *
	 * Stores a reference to the calendar feeds page added using the
	 * add_submenu_page function.
	 *
	 * @var object
	 */
	var $feeds_page;

	/**
	 * geo_region_biasing class variable
	 *
	 * If set to TRUE the ISO-3166 part of the configured
	 * locale in WordPress is going to be used to bias the
	 * geo autocomplete plugin towards a specific region.
	 *
	 * @var bool
	 **/
	var $geo_region_biasing;

	/**
	 * Whether to display data collection notice on the admin side.
	 *
	 * @var bool
	 */
	var $show_data_notification;

	/**
	 * Whether to display the introductory video notice.
	 *
	 * @var bool
	 */
	var $show_intro_video;

	/**
	 * Whether to collect event data for Timely.
	 *
	 * @var bool
	 */
	var $allow_statistics;

	/**
	 * Turn this blog into an events-only platform (this setting is overridden by
	 * AI1EC_EVENT_PLATFORM; i.e. if that is TRUE, this setting does nothing).
	 *
	 * @var bool
	 */
	var $event_platform;

	/**
	 * Enable "strict" event platform mode for this blog.
	 *
	 * @var bool
	 */
	var $event_platform_strict;

	/**
	 * Holds the configuration options of the various plugins.
	 *
	 * @var array
	 */
	var $plugins_options;

	/**
	 * disable_autocompletion class variable
	 *
	 * @var bool
	 **/
	var $disable_autocompletion;

	/**
	 * Show location name in event title in various calendar views.
	 *
	 * @var bool
	 */
	var $show_location_in_title;

	/**
	 * Show year in agenda date labels.
	 *
	 * @var bool
	 */
	var $show_year_in_agenda_dates;

	/**
	 * __construct function
	 *
	 * Default constructor
	 **/
	private function __construct() {
		$this->set_defaults(); // set default settings
	}

	/**
	 * get_instance function
	 *
	 * Return singleton instance
	 *
	 * @return object
	 **/
	static function get_instance()
 	{
		if( self::$_instance === NULL ) {
			// if W3TC is enabled, we have to empty the cache
			// before requesting it
			if( defined( 'W3TC' ) ) {
				wp_cache_delete( 'alloptions', 'options' );
			}
			// get the settings from the database
			self::$_instance = get_option( 'ai1ec_settings' );

			// if there are no settings in the database
			// save default values for the settings
			if( ! self::$_instance ) {
				self::$_instance = new self();
				delete_option( 'ai1ec_settings' );
				add_option( 'ai1ec_settings', self::$_instance );
			} else {
				self::$_instance->set_defaults(); // set default settings
			}
		}

		return self::$_instance;
	}

	/**
	* Magic get function. Returns correct value of event_platform based on
	* whether it was defined.
	*
	* @param string $name Property name
	*
	* @return mixed Property value
	**/
	public function __get( $name ) {
	global $post, $more, $ai1ec_events_helper;

		switch( $name ) {

		  case 'event_platform_active':
		    return AI1EC_EVENT_PLATFORM || $this->event_platform;
		    break;
		}
	}

	/**
	 * Save only the setting object withouth updating the CRON and other options.
	 * Used in the importer plugins architecture to avoid resetting the cron when saving plugin variables
	 *
	 *
	 * @return void
	 */
	function save_only_settings_object() {
		update_option( 'ai1ec_settings', $this );
	}

	/**
	 * save function
	 *
	 * Save settings to the database.
	 *
	 * @return void
	 **/
	function save() {
		update_option( 'ai1ec_settings', $this );
		update_option( 'start_of_week', $this->week_start_day );
		update_option( 'ai1ec_cron_version', get_option( 'ai1ec_cron_version' ) + 1 );
		update_option( 'timezone_string', $this->timezone );
	}

	/**
	 * set_defaults function
	 *
	 * Set default values for settings.
	 *
	 * @return void
	 **/
	function set_defaults() {
		self::$view_names = array(
			'posterboard' => __( 'Posterboard', AI1EC_PLUGIN_NAME ),
			'month' => __( 'Month', AI1EC_PLUGIN_NAME ),
			'week' => __( 'Week', AI1EC_PLUGIN_NAME ),
			'oneday' => __( 'Day', AI1EC_PLUGIN_NAME ),
			'agenda' => __( 'Agenda', AI1EC_PLUGIN_NAME ),
		);

		$defaults = array(
			'calendar_page_id'              => 0,
			'default_calendar_view'         => 'posterboard',
			'view_names'                    => self::$view_names,
			'view_posterboard_enabled'      => TRUE,
			'view_month_enabled'            => TRUE,
			'view_week_enabled'             => TRUE,
			'view_oneday_enabled'           => TRUE,
			'view_agenda_enabled'           => TRUE,
			'calendar_css_selector'         => '',
			'week_start_day'                => get_option( 'start_of_week' ),
			'posterboard_events_per_page'   => 30,
			'agenda_events_per_page'        => get_option( 'posts_per_page' ),
			'agenda_events_expanded'        => FALSE,
			'include_events_in_rss'         => FALSE,
			'allow_publish_to_facebook'     => FALSE,
			'facebook_credentials'          => NULL,
			'user_role_can_create_event'    => NULL,
			'show_publish_button'           => FALSE,
			'hide_maps_until_clicked'       => FALSE,
			'exclude_from_search'           => FALSE,
			'show_create_event_button'      => FALSE,
			'turn_off_subscription_buttons' => FALSE,
			'inject_categories'             => FALSE,
			'input_date_format'             => 'def',
			'input_24h_time'                => FALSE,
			'cron_freq'                     => 'daily',
			'timezone'                      => get_option( 'timezone_string' ),
			'geo_region_biasing'            => FALSE,
			'show_data_notification'        => TRUE,
			'show_intro_video'              => TRUE,
			'allow_statistics'              => TRUE,
			'event_platform'                => FALSE,
			'event_platform_strict'         => FALSE,
			'plugins_options'               => array(),
			'disable_autocompletion'        => FALSE,
			'show_location_in_title'        => TRUE,
			'show_year_in_agenda_dates'     => FALSE,
		);

		foreach( $defaults as $key => $default ) {
			if( ! isset( $this->$key ) ) {
				$this->$key = $default;
			}
		}

		// Force enable data collection setting.
		$this->allow_statistics = $defaults['allow_statistics'];
	}

	/**
	 * Updates field values with corresponding values found in $params
	 * associative array.
	 *
   * @param string $settings_page Which settings page is being updated.
	 * @param array $params Assoc. array of new settings, e.g. from $_REQUEST.
	 *
	 * @return void
	 */
	function update( $settings_page, $params ) {
    switch ($settings_page) {
      // ==================
      // = Settings page. =
      // ==================
      case 'settings':
        $field_names = array(
          'default_calendar_view',
          'calendar_css_selector',
          'week_start_day',
          'posterboard_events_per_page',
          'agenda_events_per_page',
          'input_date_format',
          'allow_events_posting_facebook',
          'facebook_credentials',
          'user_role_can_create_event',
          'timezone',
        );
        $checkboxes = array(
          'view_posterboard_enabled',
          'view_month_enabled',
          'view_week_enabled',
          'view_oneday_enabled',
          'view_agenda_enabled',
          'agenda_events_expanded',
          'include_events_in_rss',
          'show_publish_button',
          'hide_maps_until_clicked',
          'exclude_from_search',
          'show_create_event_button',
          'turn_off_subscription_buttons',
          'inject_categories',
          'input_24h_time',
          'geo_region_biasing',
          'disable_autocompletion',
          'show_location_in_title',
          'show_year_in_agenda_dates',
        );

        // Only super-admins have the power to change Event Platform mode.
        if( is_super_admin() ) {
          $checkboxes[] = 'event_platform';
          $checkboxes[] = 'event_platform_strict';
        }
        // Save the settings for the plugins.
        global $ai1ec_importer_plugin_helper;
        $ai1ec_importer_plugin_helper->save_plugins_settings( $params );

        // Assign parameters to settings.
        foreach( $field_names as $field_name ) {
          if( isset( $params[$field_name] ) ) {
            $this->$field_name = $params[$field_name];
          }
        }
        foreach( $checkboxes as $checkbox ) {
          $this->$checkbox = isset( $params[$checkbox] ) ? TRUE : FALSE;
        }

        // Validate specific parameters.
        $this->posterboard_events_per_page = intval( $this->posterboard_events_per_page );
        if( $this->posterboard_events_per_page <= 0 ) {
          $this->posterboard_events_per_page = 1;
        }
        $this->agenda_events_per_page = intval( $this->agenda_events_per_page );
        if( $this->agenda_events_per_page <= 0 ) {
          $this->agenda_events_per_page = 1;
        }

        // Update special parameters.
    		$this->update_page( 'calendar_page_id', $params );
        break;

      // ===============
      // = Feeds page. =
      // ===============
      case 'feeds':
        // Assign parameters to settings.
        if( isset( $params['cron_freq'] ) ) {
          $this->cron_freq = $params['cron_freq'];
        }
        break;
    }
	}

  /**
   * Update setting of show_data_notification - whether to display data
   * collection notice on the admin side.
   *
   * @param  boolean $value The new setting for show_data_notification.
   * @return void
   */
	function update_notification( $value = FALSE ) {
		$this->show_data_notification = $value;
		update_option( 'ai1ec_settings', $this );
	}

  /**
   * Update setting of show_intro_video - whether to display the
   * intro video notice on the admin side.
   *
   * @param  boolean $value The new setting for show_intro_video.
   * @return void
   */
	function update_intro_video( $value = FALSE ) {
		$this->show_intro_video = $value;
		update_option( 'ai1ec_settings', $this );
	}

	/**
	 * update_page function
	 *
	 * Update page for the calendar with the one specified by the drop-down box.
	 * If the value is not numeric, user chose to auto-create a new page,
	 * therefore do so.
	 *
	 * @param string $field_name
	 * @param array $params
	 *
	 * @return void
	 **/
	function update_page( $field_name, &$params ) {
		if( ! is_numeric( $params[$field_name] ) &&
		    preg_match( '#^__auto_page:(.*?)$#', $params[$field_name], $matches ) )
	 	{
			$this->$field_name = $params[$field_name] = $this->auto_add_page( $matches[1] );
		} else {
			$this->$field_name = (int) $params[$field_name];
		}
	}

	/**
	 * auto_add_page function
	 *
	 * Auto-create a WordPress page with given name for use by this plugin.
	 *
	 * @param string page_name
	 *
	 * @return int the new page's ID.
	 **/
	function auto_add_page( $page_name ) {
		return wp_insert_post(
			array(
				'post_title' 			=> $page_name,
				'post_type' 			=> 'page',
				'post_status' 		=> 'publish',
				'comment_status' 	=> 'closed'
			)
		);
	}

}
// END class
