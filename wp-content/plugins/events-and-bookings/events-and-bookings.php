<?php
/*
 Plugin Name: Events +
 Plugin URI: http://premium.wpmudev.org/project/events-and-booking
 Description: Events gives you a flexible WordPress-based system for organizing parties, dinners, fundraisers - you name it.
 Author: S H Mohanjith (Incsub)
 WDP ID: 249
 Version: 1.4
 Author URI: http://premium.wpmudev.org
*/

/**
 * Eab_EventsHub object
 * 
 * Allow your readers to register for events you organize
 * 
 * @since 1.0.0
 * @author S H Mohanjith <moha@mohanjith.net>
 */
class Eab_EventsHub {
    
    /**
     * Current version.
	 * @TODO Update version number for new releases
     * @var	string
     */
    const CURRENT_VERSION = '1.3';
    
    /**
     * Translation domain
	 * @var string
     */
	const TEXT_DOMAIN = 'eab';
	
	const BOOKING_TABLE = 'bookings';
	const BOOKING_META_TABLE = 'booking_meta';
    
    /**
     * Options instance.
	 * @var object
     */
    private $_data;
    
    /**
     * Get the table name with prefixes
     * 
     * @global	object	$wpdb
     * @param	string	$table	Table name
     * @return	string			Table name complete with prefixes
     */
    function tablename($table) {
		global $wpdb;
    	// We use per-blog tables for network events
		return $wpdb->prefix.'eab_'.$table;
    }
	
	private function _blog_has_tables () {
		global $wpdb;
		$table = Eab_EventsHub::tablename(Eab_EventsHub::BOOKING_TABLE); // Check only one
		return ($wpdb->get_var("show tables like '{$table}'") == $table);
	}
    
    /**
     * Initializing object
     * 
     * Plugin register actions, filters and hooks. 
     */
    function __construct () {
		global $wpdb, $wp_version;
		
		// Activation deactivation hooks
		register_activation_hook(__FILE__, array(&$this, 'install'));
		register_deactivation_hook(__FILE__, array(&$this, 'uninstall'));
		
		// Actions
		add_action('init', array(&$this, 'init'), 0);
		add_action('init', array(&$this, 'process_rsvps'), 99); // Bind this a bit later, so BP can load up
		add_action('admin_init', array(&$this, 'admin_init'), 0);
		if (version_compare($wp_version, "3.3") >= 0) {
		    add_action('admin_init', array(&$this, 'tutorial') );
		}
		add_action('admin_menu', array(&$this, 'admin_menu'));
		add_action('admin_notices', array($this, 'check_permalink_format'));
	
		add_action('option_rewrite_rules', array(&$this, 'check_rewrite_rules'));
		
		add_action('wp_print_styles', array(&$this, 'wp_print_styles'));
		add_action('wp_enqueue_scripts', array(&$this, 'wp_enqueue_scripts'));
		
		add_action('manage_incsub_event_posts_custom_column', array(&$this, 'manage_posts_custom_column') );
		add_filter('manage_incsub_event_posts_columns', array(&$this, 'manage_posts_columns') );
		
		add_action('add_meta_boxes_incsub_event', array(&$this, 'meta_boxes') );
		add_action('wp_insert_post', array(&$this, 'save_event_meta'), 10, 2 );
		add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts') );
		add_action('admin_print_styles', array(&$this, 'admin_print_styles') );
		add_action('widgets_init', array(&$this, 'widgets_init'));
		
		add_action('wp_ajax_nopriv_eab_paypal_ipn', array(&$this, 'process_paypal_ipn'));
		add_action('wp_ajax_eab_paypal_ipn', array(&$this, 'process_paypal_ipn'));
		add_action('wp_ajax_nopriv_eab_list_rsvps', array(&$this, 'process_list_rsvps'));
		add_action('wp_ajax_eab_list_rsvps', array(&$this, 'process_list_rsvps'));
		add_filter('single_template', array( &$this, 'handle_single_template' ) );
		add_filter('archive_template', array( &$this, 'handle_archive_template' ) );
		
		add_action('wp', array($this, 'load_events_from_query'));
		
		add_filter('rewrite_rules_array', array(&$this, 'add_rewrite_rules'));
		add_filter('post_type_link', array(&$this, 'post_type_link'), 10, 3);
		
		add_filter('query_vars', array(&$this, 'query_vars') );
		add_filter('cron_schedules', array(&$this, 'cron_schedules') );
		
		add_filter('views_edit-incsub_event', array(&$this, 'views_list') );
		add_filter('agm_google_maps-post_meta-address', array(&$this, 'agm_google_maps_post_meta_address'));
		add_filter('agm_google_maps-options', array(&$this, 'agm_google_maps_options'));
		
		add_filter('user_has_cap', array(&$this, 'user_has_cap'), 10, 3);
		
		add_filter('login_message', array(&$this, 'login_message'), 10);
		
		$this->_data = Eab_Options::get_instance();
		
		// Thrashing recurrent post trashes its instances, likewise for deleting.
		add_action('wp_trash_post', array($this, 'process_recurrent_trashing'));
		add_action('untrash_post', array($this, 'process_recurrent_untrashing'));
		add_action('before_delete_post', array($this, 'process_recurrent_deletion'));
		
		// API login after the options have been initialized
		if ($this->_data->get_option('accept_api_logins')) {
			add_action('wp_ajax_nopriv_eab_facebook_login', array($this, 'handle_facebook_login'));
			add_action('wp_ajax_nopriv_eab_get_form', array($this, 'handle_get_form'));
			
			add_action('wp_ajax_nopriv_eab_get_twitter_auth_url', array($this, 'handle_get_twitter_auth_url'));
			add_action('wp_ajax_nopriv_eab_twitter_login', array($this, 'handle_twitter_login'));
			
			add_action('wp_ajax_nopriv_eab_get_google_auth_url', array($this, 'handle_get_google_auth_url'));
			add_action('wp_ajax_nopriv_eab_google_login', array($this, 'handle_google_login'));

			add_action('wp_ajax_nopriv_eab_wordpress_login', array($this, 'handle_wordpress_login'));
			add_action('wp_ajax_nopriv_eab_wordpress_register', array($this, 'handle_wordpress_register'));
			
			add_action('wp_ajax_eab_get_form', array($this, 'handle_get_form'));
			
			// API avatars
			add_filter('get_avatar', array($this, 'get_social_api_avatar'), 10, 3);
			
			// Google
			if ( !session_id() )
				session_start();
			if (!class_exists('LightOpenID')) 
				include_once  WP_PLUGIN_DIR . '/events-and-bookings/lib/lightopenid/openid.php';
			$this->openid = new LightOpenID;
			
			$this->openid->identity = 'https://www.google.com/accounts/o8/id';
			$this->openid->required = array('namePerson/first', 'namePerson/last', 'namePerson/friendly', 'contact/email');
			if (!empty($_REQUEST['openid_ns'])) {
			$cache = $this->openid->getAttributes();
				if (isset($cache['namePerson/first']) || isset($cache['namePerson/last']) || isset($cache['contact/email'])) {
					$_SESSION['wdcp_google_user_cache'] = $cache;
				}
			}
			$this->_google_user_cache = $_SESSION['wdcp_google_user_cache'];
			
		}
		// End API login & form section	
		add_action('wp_ajax_eab_restart_tutorial', array($this, 'handle_tutorial_restart'));
		add_action('wp_ajax_eab_cancel_attendance', array($this, 'handle_attendance_cancel'));		
		add_action('wp_ajax_eab_delete_attendance', array($this, 'handle_attendance_delete'));		
    }

	function process_recurrent_trashing ($post_id) {
		$event = new Eab_EventModel(get_post($post_id));
		if (!$event->is_recurring()) return false;
		$event->trash_recurring_instances();
	}

	function process_recurrent_untrashing ($post_id) {
		$event = new Eab_EventModel(get_post($post_id));
		if (!$event->is_recurring()) return false;
		$event->untrash_recurring_instances();
	}

	function process_recurrent_deletion ($post_id) {
		$event = new Eab_EventModel(get_post($post_id));
		if (!$event->is_recurring()) return false;
		$event->delete_recurring_instances();
	}
    
    /**
     * Initialize the plugin
     * 
     * @see		http://codex.wordpress.org/Plugin_API/Action_Reference
     * @see		http://adambrown.info/p/wp_hooks/hook/init
     */
    function init() {
		global $wpdb, $wp_rewrite, $current_user, $blog_id;
		
		if (preg_match('/mu\-plugin/', PLUGINDIR) > 0) {
		    load_muplugin_textdomain(self::TEXT_DOMAIN, dirname(plugin_basename(__FILE__)).'/languages');
		} else {
		    load_plugin_textdomain(self::TEXT_DOMAIN, false, dirname(plugin_basename(__FILE__)).'/languages');
		}
		
		$labels = array(
		    'name' => __('Events', self::TEXT_DOMAIN),
		    'singular_name' => __('Event', self::TEXT_DOMAIN),
		    'add_new' => __('Add Event', self::TEXT_DOMAIN),
		    'add_new_item' => __('Add New Event', self::TEXT_DOMAIN),
		    'edit_item' => __('Edit Event', self::TEXT_DOMAIN),
		    'new_item' => __('New Event', self::TEXT_DOMAIN),
		    'view_item' => __('View Event', self::TEXT_DOMAIN),
		    'search_items' => __('Search Event', self::TEXT_DOMAIN),
		    'not_found' =>  __('No event found', self::TEXT_DOMAIN),
		    'not_found_in_trash' => __('No event found in Trash', self::TEXT_DOMAIN),
		    'menu_name' => __('Events', self::TEXT_DOMAIN)
		);
		
		$supports = array( 'title', 'editor', 'author', 'venue', 'thumbnail', 'comments');
		$supports = apply_filters('eab-event-post_type-supports', $supports);
		
		$event_type_args = array(
			'labels' => $labels,
			'public' => true,
			'show_ui' => true,
			'publicly_queryable' => true,
			'capability_type' => 'event',
			'hierarchical' => false,
			'map_meta_cap' => true,
			'query_var' => true,
			'supports' => $supports,
			'rewrite' => array( 'slug' => $this->_data->get_option('slug'), 'with_front' => false ),
			'has_archive' => true,
			'menu_icon' => plugins_url('events-and-bookings/img/small-greyscale.png'),		
		);
		register_post_type(
			Eab_EventModel::POST_TYPE,
			apply_filters('eab-post_type-register', $event_type_args)
		);
		register_taxonomy(
			'eab_events_category',		
			Eab_EventModel::POST_TYPE,
			array(
				'labels' => array(
					'name' => __('Event Categories', self::TEXT_DOMAIN),
					'singular_name' => __('Event Category', self::TEXT_DOMAIN),
					'singular_name' => __('Event Category', self::TEXT_DOMAIN),
				),
				'hierarchical' => true,
				'public' => true,
				'rewrite' => array(
					'slug' => $this->_data->get_option('slug'),
					'with_front' => true,
				),
			)
		);
		
		$pts_args = array('show_in_admin_all_list' => false);
		if (is_admin()) $pts_args['protected'] = true; 
		else $pts_args['public'] = true; 
		register_post_status(Eab_EventModel::RECURRENCE_STATUS, $pts_args);
		
		$event_structure = '/'.$this->_data->get_option('slug').'/%event_year%/%event_monthnum%/%incsub_event%';
		
		$wp_rewrite->add_rewrite_tag("%incsub_event%", '(.+?)', "incsub_event=");
		$wp_rewrite->add_rewrite_tag("%event_year%", '([0-9]{4})', "event_year=");
		$wp_rewrite->add_rewrite_tag("%event_monthnum%", '([0-9]{2})', "event_monthnum=");
		$wp_rewrite->add_permastruct('incsub_event', $event_structure, false);
		
		wp_register_script('eab_jquery_ui', plugins_url('events-and-bookings/js/jquery-ui.custom.min.js'), array('jquery'), self::CURRENT_VERSION);
		wp_register_script('eab_admin_js', plugins_url('events-and-bookings/js/eab-admin.js'), array('jquery'), self::CURRENT_VERSION);
		wp_register_script('eab_event_js', plugins_url('events-and-bookings/js/eab-event.js'), array('jquery'), self::CURRENT_VERSION);
		wp_register_script('eab_api_js', plugins_url('events-and-bookings/js/eab-api.js'), array('jquery'), self::CURRENT_VERSION);
		
		wp_register_style('eab_jquery_ui', plugins_url('events-and-bookings/css/smoothness/jquery-ui-1.8.16.custom.css'), null, self::CURRENT_VERSION);
		wp_register_style('eab_admin', plugins_url('events-and-bookings/css/admin.css'), null, self::CURRENT_VERSION);
		
		wp_register_style('eab_front', plugins_url('events-and-bookings/css/front.css'), null, self::CURRENT_VERSION);
		
		if (defined('AGM_PLUGIN_URL')) {
		    add_action('admin_print_scripts-post.php', array($this, 'js_editor_button'));
		    add_action('admin_print_scripts-post-new.php', array($this, 'js_editor_button'));
		    //add_action('admin_print_scripts-widgets.php', array($this, 'js_widget_editor'));
		}
	
		$event_localized = array(
		    'view_all_bookings' => __('View all RSVPs', self::TEXT_DOMAIN),
		    'back_to_gettting_started' => __('Back to getting started', self::TEXT_DOMAIN),
		);
		
		wp_localize_script('eab_admin_js', 'eab_event_localized', $event_localized);
		
		
		if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'incsub_event-update-options')) {
			$options = array();
		    $options['slug'] 						= trim(trim($_POST['event_default']['slug'], '/'));
			$options['accept_payments'] 			= $_POST['event_default']['accept_payments'];
			$options['accept_api_logins'] 			= $_POST['event_default']['accept_api_logins'];
			$options['display_attendees'] 			= $_POST['event_default']['display_attendees'];
			$options['currency'] 					= $_POST['event_default']['currency'];
			$options['paypal_email'] 				= $_POST['event_default']['paypal_email'];
			$options['paypal_sandbox'] 				= @$_POST['event_default']['paypal_sandbox'];
			
			$options['override_appearance_defaults']	= $_POST['event_default']['override_appearance_defaults'];
			$options['archive_template'] 			= $_POST['event_default']['archive_template'];
			$options['single_template'] 			= $_POST['event_default']['single_template'];
			
			$options['facebook-app_id'] 			= $_POST['event_default']['facebook-app_id'];
			$options['facebook-no_init'] 			= $_POST['event_default']['facebook-no_init'];
			
			$options['twitter-app_id'] 				= $_POST['event_default']['twitter-app_id'];
			$options['twitter-app_secret'] 			= $_POST['event_default']['twitter-app_secret'];
			
		    //update_option('incsub_event_default', $this->_options['default']);
			$options = apply_filters('eab-settings-before_save', $options);
			$this->_data->set_options($options);
		    wp_redirect('edit.php?post_type=incsub_event&page=eab_settings&incsub_event_settings_saved=1');
		    exit();
		}
		
		if (isset($_REQUEST['eab_step'])) {
		    setcookie('eab_step', $_REQUEST['eab_step'], time()+(3600*24));
		} else if (isset($_COOKIE['eab_step'])) {
		    $_REQUEST['eab_step'] = $_COOKIE['eab_step'];
		}

		if (isset($_REQUEST['eab_export'])) {
			if (!class_exists('Eab_ExporterFactory')) require_once EAB_PLUGIN_DIR . 'lib/class_eab_exporter.php';
			Eab_ExporterFactory::serve($_REQUEST);
		}
    }

	function process_rsvps () {
		global $wpdb, $current_user;
		if (isset($_POST['event_id']) && isset($_POST['user_id'])) {
		    $booking_actions = array('yes' => 'yes', 'maybe' => 'maybe', 'no' => 'no');
		    
		    $event_id = intval($_POST['event_id']);
		    $booking_action = $booking_actions[$_POST['action_yes']];
		    
		    do_action( 'incsub_event_booking', $event_id, $current_user->ID, $booking_action );
		    if (isset($_POST['action_yes'])) {
				$wpdb->query(
				    $wpdb->prepare("INSERT INTO ".self::tablename(self::BOOKING_TABLE)." VALUES(null, %d, %d, NOW(), 'yes') ON DUPLICATE KEY UPDATE `status` = 'yes';", $event_id, $current_user->ID)
				);
				// --todo: Add to BP activity stream
				do_action( 'incsub_event_booking_yes', $event_id, $current_user->ID );
				$this->recount_bookings($event_id);
				wp_redirect('?eab_success_msg='.urlencode(__("Excellent! We've got you marked as coming and we'll see you there!", self::TEXT_DOMAIN)));
				exit();
		    }
		    if (isset($_POST['action_maybe'])) {
				$wpdb->query(
				    $wpdb->prepare("INSERT INTO ".self::tablename(self::BOOKING_TABLE)." VALUES(null, %d, %d, NOW(), 'maybe') ON DUPLICATE KEY UPDATE `status` = 'maybe';", $event_id, $current_user->ID)
				);
				// --todo: Add to BP activity stream
				do_action( 'incsub_event_booking_maybe', $event_id, $current_user->ID );
				$this->recount_bookings($event_id);
				wp_redirect('?eab_success_msg='.urlencode(__("Thanks for letting us know. Hopefully you'll be able to make it!", self::TEXT_DOMAIN)));
				exit();
		    }
		    if (isset($_POST['action_no'])) {
				$wpdb->query(
				    $wpdb->prepare("INSERT INTO ".self::tablename(self::BOOKING_TABLE)." VALUES(null, %d, %d, NOW(), 'no') ON DUPLICATE KEY UPDATE `status` = 'no';", $event_id, $current_user->ID)
				);
				// --todo: Remove from BP activity stream
				do_action( 'incsub_event_booking_no', $event_id, $current_user->ID );
				$this->recount_bookings($event_id);
				wp_redirect('?eab_success_msg='.urlencode(__("That's too bad you won't be able to make it", self::TEXT_DOMAIN)));
				exit();
		    }
		}	
	}
    
    function admin_init() {
    	// Check for tables first
    	if (!$this->_blog_has_tables()) $this->install();
		
		if (get_option('eab_activation_redirect', false)) {
		    delete_option('eab_activation_redirect');
		    if (!(is_multisite() && is_super_admin()) || !is_network_admin()) {
				wp_redirect('edit.php?post_type=incsub_event&page=eab_welcome');
		    }
		}
    }
	
    function js_editor_button() {
		wp_enqueue_script('thickbox');
        wp_enqueue_script('eab_editor',  plugins_url('events-and-bookings/js/editor.js'), array('jquery'));
        wp_localize_script('eab_editor', 'eab_l10nEditor', array(
            'loading' => __('Loading maps... please wait', 'agm_google_maps'),
            'use_this_map' => __('Insert this map', 'agm_google_maps'),
            'preview_or_edit' => __('Preview/Edit', 'agm_google_maps'),
            'delete_map' => __('Delete', 'agm_google_maps'),
            'add_map' => __('Add Map', 'agm_google_maps'),
            'existing_map' => __('Existing map', 'agm_google_maps'),
            'no_existing_maps' => __('No existing maps', 'agm_google_maps'),
            'new_map' => __('Create new map', 'agm_google_maps'),
            'advanced' => __('Advanced mode', 'agm_google_maps'),
            'advanced_mode_activate_help' => __('Activate Advanced mode to select individual maps to merge into one new map or to batch delete maps', 'agm_google_maps'),
	    	'advanced_mode_help' => __('To create a new map from several maps select the maps you want to use and click Merge locations', 'agm_google_maps'),
            'advanced_off' => __('Exit advanced mode', 'agm_google_maps'),
	    	'merge_locations' => __('Merge locations', 'agm_google_maps'),
	    	'batch_delete' => __('Batch delete', 'agm_google_maps'),
            'new_map_intro' => __('Create a new map which can be inserted into this post or page. Once you are done you can manage all maps below', 'agm_google_maps'),
        ));
    }

	function check_permalink_format () {
		if (get_option('permalink_structure')) return false;
		echo '<div class="error"><p>' . 
			sprintf(
				__('You must must update your permalink structure to something other than default to use Events. <a href="%s">You can do so here.</a>', self::TEXT_DOMAIN),
				admin_url('options-permalink.php')
			) .
		'</p></div>';
	}
    
    function login_message($message) {	
		if (isset($_REQUEST['eab']) && $_REQUEST['eab'] == 'y') {
		    $message = '<p class="message">'.__("Excellent, few more steps! We need you to login or register to get you marked as coming!", self::TEXT_DOMAIN).'</p>';
		}
		
		if (isset($_REQUEST['eab']) && $_REQUEST['eab'] == 'm') {
		    $message = '<p class="message">'.__("Please login or register to help us let you know any changes about the event and record your response!", self::TEXT_DOMAIN).'</p>';
		}
		
		if (isset($_REQUEST['eab']) && $_REQUEST['eab'] == 'n') {
		    $message = '<p class="message">'.__("That's too bad you won't be able to make it, if you login or register we will be able to record your response", self::TEXT_DOMAIN).'</p>';
		}
		
		return $message;
    }
    
    function recount_bookings($event_id) {
		global $wpdb;
		
		// Yes
		$yes_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM ".self::tablename(self::BOOKING_TABLE)." WHERE `status` = 'yes' AND event_id = %d;", $event_id));
	    	update_post_meta($event_id, 'incsub_event_yes_count', $yes_count);
		
		// Maybe
		$maybe_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM ".self::tablename(self::BOOKING_TABLE)." WHERE `status` = 'maybe' AND event_id = %d;", $event_id));
	    	update_post_meta($event_id, 'incsub_event_maybe_count', $maybe_count);
		update_post_meta($event_id, 'incsub_event_attending_count', $maybe_count+$yes_count);
		
		// No
		$no_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM ".self::tablename(self::BOOKING_TABLE)." WHERE `status` = 'no' AND event_id = %d;", $event_id));
		update_post_meta($event_id, 'incsub_event_no_count', $no_count);
    }
    
    function process_paypal_ipn() {
		$req = 'cmd=_notify-validate';
	
		$request = $_REQUEST;
		
		$post_values = "";
		$cart = array();
		foreach ($request as $key => $value) {
		    $value = urlencode(stripslashes($value));
		    $req .= "&$key=$value";
		    $post_values .= " $key : $value\n";
		}
		
		$header = "";
		// post back to PayPal system to validate
		$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
		
		//$ip = $ip;
		
		$pay_to_email = $request['receiver_email'];
		$pay_from_email = $request['payer_email'];
		$transaction_id = $request['txn_id'];
		
		$status = $request['payment_status'];
		$amount = $request['mc_gross'];
		$ticket_count = $request['quantity']; // Ticket count is the number of paid for tickets
		$currency = $request['mc_currency'];
		$test_ipn = $request['test_ipn'];
		$event_id = $request['item_number'];
		$booking_id = (int)$request['booking_id'];
		$blog_id = (int)$request['blog_id'];
		
		$eab_options = get_option('incsub_event_default');
	
		if ((int)@$eab_options['paypal_sandbox'] == 1) {
		    $fp = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);
		} else {
		    $fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);
		}
		
		if (is_multisite()) switch_to_blog($blog_id);
		$booking_obj = Eab_EventModel::get_booking($booking_id);
		
		if (!$booking_obj || !$booking_obj->id) {
		    header('HTTP/1.0 404 Not Found');
		    header('Content-type: text/plain; charset=UTF-8');
		    print 'Booking not found';
		    exit(0);
	    }
		
		if ($booking_obj->event_id != $event_id) {
		    header('HTTP/1.0 404 Not Found');
		    header('Content-type: text/plain; charset=UTF-8');
		    print 'Fake event id. REF: PP0';
		    exit(0);
		}
		
		if (@$eab_options['currency'] != $currency) {
		    header('HTTP/1.0 400 Bad Request');
		    header('Content-type: text/plain; charset=UTF-8');
		    print 'We were not expecting you. REF: PP1';
		    exit(0);
		}

		//if ($amount != get_post_meta($event_id, 'incsub_event_fee', true)) {	    
		//if ($amount != $ticket_count * get_post_meta($event_id, 'incsub_event_fee', true)) {	    
		if ($amount != $ticket_count * apply_filters('eab-payment-event_price-for_user', get_post_meta($event_id, 'incsub_event_fee', true), $event_id, $booking_obj->user_id)) {	    
		    header('HTTP/1.0 400 Bad Request');
		    header('Content-type: text/plain; charset=UTF-8');
		    print 'We were not expecting you. REF: PP2';
	    	    exit(0);
		}

		if (!$ticket_count) {	    
		    header('HTTP/1.0 400 Bad Request');
		    header('Content-type: text/plain; charset=UTF-8');
		    print 'Cheapskate. REF: PP2';
	    	    exit(0);
		}
		
		if ($pay_to_email != @$eab_options['paypal_email']) {
		    header('HTTP/1.0 400 Bad Request');
		    header('Content-type: text/plain; charset=UTF-8');
		    print 'We were not expecting you. REF: PP3';
		    exit(0);
		}
		
		if (!$fp) {
		    header('HTTP/1.0 400 Bad Request');
		    header('Content-type: text/plain; charset=UTF-8');
		    print 'We were not expecting you. REF: PP4';
		    exit(0);
		} else {
		    fputs ($fp, $header . $req);
		    while (!feof($fp)) $res = fgets ($fp, 1024);
			
			if (strcmp ($res, "VERIFIED") == 0) {
				if ($test_ipn == 1) {
				    if ((int)@$eab_options['paypal_sandbox'] == 1) {
						// Sandbox, it's allowed so do stuff
				    	Eab_EventModel::update_booking_meta($booking_obj->id, 'booking_transaction_key', $transaction_id);
				    	Eab_EventModel::update_booking_meta($booking_obj->id, 'booking_ticket_count', $ticket_count);
				    } else {
				    	// Sandbox, not allowed, bail out
				    	header('HTTP/1.0 400 Bad Request');
					    header('Content-type: text/plain; charset=UTF-8');
					    print 'We were not expecting you. REF: PP1';
					    exit(0);
				    }
				} else {
				    // Paid
				    Eab_EventModel::update_booking_meta($booking_obj->id, 'booking_transaction_key', $transaction_id);
					Eab_EventModel::update_booking_meta($booking_obj->id, 'booking_ticket_count', $ticket_count);
				}
				header('HTTP/1.0 200 OK');
				header('Content-type: text/plain; charset=UTF-8');
				print 'Success';
			    exit(0);
			} else if (strcmp ($res, "INVALID") == 0) {
			    $message = "Invalid PayPal IPN $transaction_id";
			}
		    fclose ($fp);
	    }
		if (is_multisite()) restore_current_blog();
		header('HTTP/1.0 200 OK');
		header('Content-type: text/plain; charset=UTF-8');
		print 'Thank you very much for letting us know. REF: '.$message;
		exit(0);
    }
    
    function agm_google_maps_post_meta_address($location) {
		global $post;
		
		if (!$location && $post->post_type == 'incsub_event') {
		    $meta = get_post_custom($post->ID);
		    
		    $venue = '';
		    if (isset($meta["incsub_event_venue"]) && isset($meta["incsub_event_venue"][0])) {
				$venue = stripslashes($meta["incsub_event_venue"][0]);
				if (preg_match_all('/map id="([0-9]+)"/', $venue, $matches) > 0) {
				    if (isset($matches[1]) && isset($matches[1][0])) {
						$model = new AgmMapModel();
						$map = $model->get_map($matches[1][0]);
						$venue = $map['markers'][0]['title'];
						if ($meta["agm_map_created"][0] != $map['id']) {
						    update_post_meta($post->ID, 'agm_map_created', $map['id']);
						    return false;
						}
				    }
				}
		    }
		    
		    return $venue;
		}
		return $location;
    }
    
    function agm_google_maps_options($opts) {
		$opts['use_custom_fields'] = 1;
		$opts['custom_fields_options']['associate_map'] = 1;
		//$opts['custom_fields_options']['autoshow_map'] = 1;
		return $opts;
    }
    
    function handle_archive_template( $path ) {
		global $wp_query, $post;
		
		if ( 'incsub_event' != $post->post_type )
		    return $path;
		
		$type = reset( explode( '_', current_filter() ) );
		
		$file = basename( $path );
		
		$style = file_exists(get_stylesheet_directory() . '/events.css')
			? get_stylesheet_directory_uri() . '/events.css'
			: file_exists(get_template_directory() . '/events.css')
				? get_template_directory_uri() . '/events.css'
				: false
		;
		$eab_type = $is_theme_tpl = false;
		if ($this->_data->get_option('override_appearance_defaults')) {
			$eab_type = $this->_data->get_option('archive_template');
			$eab_type = $eab_type ? $eab_type : '';
			$is_theme_tpl = preg_match('/\.php$/', $eab_type);
		}
		if (!$style && !$is_theme_tpl && @$this->_data->get_option('override_appearance_defaults')) {
			$style_path = file_exists(EAB_PLUGIN_DIR . "default-templates/{$eab_type}/events.css");
			$style = $style_path ? plugins_url(basename(dirname(__FILE__)) . "/default-templates/{$eab_type}/events.css") : $style;
		}
		if ($style) add_action('wp_head', create_function('', "wp_enqueue_style('eab-events', '$style');"));
		
		if ( empty( $path ) || "$type.php" == $file ) {
			if ($eab_type && !$is_theme_tpl) {
				$path = EAB_PLUGIN_DIR . "default-templates/{$eab_type}/{$type}-incsub_event.php";
				if (file_exists($path)) return $path;
				else {
					// A more specific template was not found, so load the default one
				    add_filter('the_content', array(&$this, 'archive_content'));
				    if (file_exists(get_stylesheet_directory().'/archive.php')) {
						$path = get_stylesheet_directory().'/archive.php';
				    } else if (file_exists(get_template_directory().'/archive.php')) {
						$path = get_template_directory().'/archive.php';
				    } else $path = '';
				}
			} else if ($eab_type && $is_theme_tpl) {
				// Selected file is a theme file
			    add_filter('the_content', array(&$this, 'archive_content'));
				if (file_exists(get_stylesheet_directory() . '/' . $eab_type)) {
					$path = get_stylesheet_directory() . '/' . $eab_type;
			    } else if (file_exists(get_template_directory() . '/' . $eab_type)) {
					$path = get_template_directory() . '/' . $eab_type;
			    }
			} else {
			    // A more specific template was not found, so load the default one
			    add_filter('the_content', array(&$this, 'archive_content'));
			    if (file_exists(get_stylesheet_directory().'/archive.php')) {
					$path = get_stylesheet_directory().'/archive.php';
			    } else if (file_exists(get_template_directory().'/archive.php')) {
					$path = get_template_directory().'/archive.php';
			    }
			}
		}
		return $path;
    }
    
    function archive_content($content) {
		global $post;
		if ('incsub_event' != $post->post_type) return $content;
		return Eab_Template::get_archive_content($post);
    }
    
    function handle_single_template( $path ) {
		global $wp_query, $post;
		
		if ( 'incsub_event' != $post->post_type )
		    return $path;
		
		$type = reset( explode( '_', current_filter() ) );
		
		$file = basename( $path );
		
		$style = file_exists(get_stylesheet_directory() . '/events.css')
			? get_stylesheet_directory_uri() . '/events.css'
			: file_exists(get_template_directory() . '/events.css')
				? get_template_directory_uri() . '/events.css'
				: false
		;
		$eab_type = $is_theme_tpl = false;
		if ($this->_data->get_option('override_appearance_defaults')) {
			$eab_type = $this->_data->get_option('single_template');
			$eab_type = $eab_type ? $eab_type : '';
			$is_theme_tpl = preg_match('/\.php$/', $eab_type);
		}
		if (!$style && !$is_theme_tpl && @$this->_data->get_option('override_appearance_defaults')) {
			$style_path = file_exists(EAB_PLUGIN_DIR . "default-templates/{$eab_type}/events.css");
			$style = $style_path ? plugins_url(basename(dirname(__FILE__)) . "/default-templates/{$eab_type}/events.css") : $style;
		}
		if ($style) add_action('wp_head', create_function('', "wp_enqueue_style('eab-events', '$style');"));
		
		if ( empty( $path ) || "$type.php" == $file ) {
			if ($eab_type && !$is_theme_tpl) {
				$path = EAB_PLUGIN_DIR . "default-templates/{$eab_type}/{$type}-incsub_event.php";
				if (file_exists($path)) return $path;
				else {
					// A more specific template was not found, so load the default one
				    add_filter('agm_google_maps-options', 'eab_autoshow_map_off', 99); // Shut down maps autoshowing
				    add_filter('the_content', array(&$this, 'single_content'));
				    if (file_exists(get_stylesheet_directory().'/single.php')) {
						$path = get_stylesheet_directory().'/single.php';
				    } else if (file_exists(get_template_directory().'/single.php')) {
						$path = get_template_directory().'/single.php';
				    } else $path = '';
				}
			} else if ($eab_type && $is_theme_tpl) {
				// Selected file is a theme file
			    add_filter('the_content', array(&$this, 'single_content'));
				if (file_exists(get_stylesheet_directory() . '/' . $eab_type)) {
					$path = get_stylesheet_directory() . '/' . $eab_type;
			    } else if (file_exists(get_template_directory() . '/' . $eab_type)) {
					$path = get_template_directory() . '/' . $eab_type;
			    }
			} else {
			    // A more specific template was not found, so load the default one
			    add_filter('agm_google_maps-options', 'eab_autoshow_map_off', 99); // Shut down maps autoshowing
			    add_filter('the_content', array(&$this, 'single_content'));
			    if (file_exists(get_stylesheet_directory().'/single.php')) {
					$path = get_stylesheet_directory().'/single.php';
			    } else if (file_exists(get_template_directory().'/single.php')) {
					$path = get_template_directory().'/single.php';
			    }
		    }
		}
		return $path;
    }
    
    function single_content($content) {
		global $post;
		if ('incsub_event' != $post->post_type) return $content;
		return Eab_Template::get_single_content($post, $content);
    }
    
    function process_list_rsvps() {
		global $post;
		
		$post = get_post($_REQUEST['pid']);
		echo Eab_Template::get_rsvps($post);
		
		exit(0);
    }
    
    function meta_boxes() {
		global $post, $current_user;
		
		add_meta_box('incsub-event', __('Event Details', self::TEXT_DOMAIN), array(&$this, 'event_meta_box'), 'incsub_event', 'side', 'high');
		add_meta_box('incsub-event-bookings', __("Event RSVPs", self::TEXT_DOMAIN), array(&$this, 'bookings_meta_box'), 'incsub_event', 'normal', 'high');
		if (isset($_REQUEST['eab_step'])) {
		    add_meta_box('incsub-event-wizard', __('Are you following the step by step guide?', self::TEXT_DOMAIN), array(&$this, 'wizard_meta_box'), 'incsub_event', 'normal', 'low');
		}
		do_action('eab-event_meta-meta_box_registration');
    }
    
    function wizard_meta_box() {
		return '';
    }
 	
	private function _check_admin_page_id () {
    	$_page_ids = array (
			'incsub_event_page_eab_welcome',
			'edit-incsub_event',
			'incsub_event',
			'incsub_event_page_eab_settings',
		);
    	$screen = get_current_screen();
		if (!in_array($screen->id, $_page_ids)) return false;
    	return true;
    }
		
    function admin_enqueue_scripts() {
    	if (!$this->_check_admin_page_id()) return false;
		wp_enqueue_script('eab_jquery_ui');
		wp_enqueue_script('eab_admin_js');
    }
    
    function admin_print_styles() {
		wp_enqueue_style('eab_jquery_ui');
		wp_enqueue_style('eab_admin');
    }
    
    function wp_print_styles() {
		global $wp_query;
		
		if (isset($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] == 'incsub_event') {
		    wp_enqueue_style('eab_front');
		}
    }
    
    function wp_enqueue_scripts() {
		global $wp_query;
		
		echo '<script type="text/javascript">var _eab_data=' . json_encode(apply_filters('eab-javascript-public_data', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'root_url' => plugins_url('events-and-bookings/img/'),
			'fb_scope' => 'email',
		))) . ';</script>';
		/*
		printf(
			'<script type="text/javascript">var _eab_data=;</script>',
			//{"ajax_url": "%s", "root_url": "%s"};</script>',
			admin_url('admin-ajax.php'), plugins_url('events-and-bookings/img/')
		);
		*/
		if (isset($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] == 'incsub_event') {
		    wp_enqueue_script('eab_event_js');
			if (!$this->_data->get_option('accept_api_logins')) return false;
			$domain = get_bloginfo('name');
			$domain = $domain ? $domain : __('WordPress', self::TEXT_DOMAIN);
			
		    wp_enqueue_script('eab_api_js');
			wp_localize_script('eab_api_js', 'l10nEabApi', array(
				'facebook' => __('Login with Facebook', self::TEXT_DOMAIN),
				'twitter' => __('Login with Twitter', self::TEXT_DOMAIN),
				'google' => __('Login with Google', self::TEXT_DOMAIN),
				'wordpress' => sprintf(__('Login with %s', self::TEXT_DOMAIN), $domain),
				'cancel' => __('Cancel', self::TEXT_DOMAIN),
				'please_wait' => __('Please, wait...', self::TEXT_DOMAIN),
				
				'wp_register' => __('Register', self::TEXT_DOMAIN), 
				'wp_registration_msg' => __('Create a username in order to register for this event - or just click cancel to register using your Facebook or Twitter ID', self::TEXT_DOMAIN), 
				'wp_login' => __('Log in', self::TEXT_DOMAIN), 
				'wp_login_msg' => __('Login with your existing username in order to register for this event - or just click cancel to register using your Facebook or Twitter ID', self::TEXT_DOMAIN), 
				'wp_username' => __('Username', self::TEXT_DOMAIN), 
				'wp_password' => __('Password', self::TEXT_DOMAIN), 
				'wp_email' => __('Email', self::TEXT_DOMAIN), 
				'wp_toggle_on' => __('Already a member? Log in here', self::TEXT_DOMAIN), 
				'wp_toggle_off' => __('Click here to register', self::TEXT_DOMAIN), 
				'wp_submit' => __('Submit', self::TEXT_DOMAIN), 
				'wp_cancel' => __('Cancel', self::TEXT_DOMAIN), 
			));
			if (!$this->_data->get_option('facebook-no_init')) {
				add_action('wp_footer', create_function('', "echo '" .
				sprintf(
					'<div id="fb-root"></div><script type="text/javascript">
					window.fbAsyncInit = function() {
						FB.init({
						  appId: "%s",
						  status: true,
						  cookie: true,
						  xfbml: true
						});
					};
					// Load the FB SDK Asynchronously
					(function(d){
						var js, id = "facebook-jssdk"; if (d.getElementById(id)) {return;}
						js = d.createElement("script"); js.id = id; js.async = true;
						js.src = "//connect.facebook.net/en_US/all.js";
						d.getElementsByTagName("head")[0].appendChild(js);
					}(document));
					</script>',
					$this->_data->get_option('facebook-app_id')
				) .
				"';"));
			}
			do_action('eab-javascript-enqueue_scripts');
		}
	
    }
    
    function event_meta_box () {
		global $post;
		
		$content = '';
		$content = apply_filters('eab-meta_box-event_meta_box-before', $content);
		$content .= $this->meta_box_part_where();
		$content .= '<div class="clear"></div>';
		$content .= $this->meta_box_part_when();
		$content .= '<div class="clear"></div>';
		$content .= $this->meta_box_part_status();
		$content .= '<div class="clear"></div>';
		if ($this->_data->get_option('accept_payments')) {
		    $content .= $this->meta_box_part_payments();
		    $content .= '<div class="clear"></div>';
		}
		$content = apply_filters('eab-event_meta-event_meta_box-after', $content);
		
		echo $content;
    }
    
    function meta_box_part_where () {
		global $post;
		$event = new Eab_EventModel($post);
		
		$content  = '';
		$content .= '<div class="eab_meta_box">';
		$content .= '<input type="hidden" name="incsub_event_where_meta" value="1" />';
		$content .= '<div class="misc-eab-section" >';
		$content .= '<div class="eab_meta_column_box top"><label for="incsub_event_venue" id="incsub_event_venue_label">'.__('Event location', self::TEXT_DOMAIN).'</label> <span id="eab_insert_map"></span></div>';
		$content .= '<textarea class="widefat" type="text" name="incsub_event_venue" id="incsub_event_venue" size="20" >' . $event->get_venue() . '</textarea>';
		$content .= '</div>';
		$content .= '</div>';

		return $content;
    }
    
    function meta_box_part_when () {
		global $post;
		$event = new Eab_EventModel($post);
		
		$content = '';
		$content .= '<div class="eab_meta_box">';
		$content .= '<div class="eab_meta_column_box" id="incsub_event_times_label">'.__('Event times and dates', self::TEXT_DOMAIN).'</div>';
		
		$content .= '<input type="hidden" name="incsub_event_when_meta" value="1" />';
		
		$start_dates = $event->get_start_dates();
		
		$content .= $this->_meta_box_part_recurring_add($event);
		if (!$event->is_recurring()) {
			$content .= '<div id="eab-add-more-rows">';
			if ($start_dates) {
			    foreach ($start_dates as $key => $date) {
					$start = $event->get_start_timestamp($key);
					$no_start = $event->has_no_start_time($key) ? 'checked="checked"' : '';
					$end = $event->get_end_timestamp($key);
					$no_end = $event->has_no_end_time($key) ? 'checked="checked"' : '';
			
					$content .= '<div class="eab-section-block">';
					$content .= '<div class="eab-section-heading">'.sprintf(__('Part %d', self::TEXT_DOMAIN), $key+1).'</div>';
					$content .= '<div class="misc-eab-section eab-start-section"><label for="incsub_event_start_'.$key.'">';
					$content .= __('Start', self::TEXT_DOMAIN).':</label>&nbsp;';
					$content .= '<input type="text" name="incsub_event_start['.$key.']" id="incsub_event_start_'.$key.'" class="incsub_event_picker incsub_event incsub_event_date incsub_event_start" value="'.date('Y-m-d', $start).'" size="10" /> ';
					$content .= '<input type="text" name="incsub_event_start_time['.$key.']" id="incsub_event_start_time_'.$key.'" class="incsub_event incsub_event_time incsub_event_start_time" value="'.date('H:i', $start).'" size="3" />';
					$content .= ' <input type="checkbox" name="incsub_event_no_start_time['.$key.']" id="incsub_event_no_start_time_'.$key.'" class="incsub_event incsub_event_time incsub_event_no_start_time" value="1" ' . $no_start . ' />';
					$content .= ' <label for="incsub_event_no_start_time_'.$key.'">' . __('No start time', self::TEXT_DOMAIN) . '</label>';
					$content .= '</div>';
					
					$content .= '<div class="misc-eab-section"><label for="incsub_event_end_'.$key.'">';
					$content .= __('End', self::TEXT_DOMAIN).':</label>&nbsp;&nbsp;';
					$content .= '<input type="text" name="incsub_event_end['.$key.']" id="incsub_event_end_'.$key.'" class="incsub_event_picker incsub_event incsub_event_date incsub_event_end" value="'.date('Y-m-d', $end).'" size="10" /> ';
					$content .= '<input type="text" name="incsub_event_end_time['.$key.']" id="incsub_event_end_time_'.$key.'" class="incsub_event incsub_event_time incsub_event_end_time" value="'.date('H:i', $end).'" size="3" />';
					$content .= ' <input type="checkbox" name="incsub_event_no_end_time['.$key.']" id="incsub_event_no_end_time_'.$key.'" class="incsub_event incsub_event_time incsub_event_no_end_time" value="1" ' . $no_end . ' />';
					$content .= ' <label for="incsub_event_no_end_time_'.$key.'">' . __('No end time', self::TEXT_DOMAIN) . '</label>';
					$content .= '</div>';
					$content .= '</div>';
			    }
			} else {
			    $i=0;
			    $content .= '<div class="eab-section-block">';
			    $content .= '<div class="eab-section-heading">'.sprintf(__('Part %d', self::TEXT_DOMAIN), $i+1).'</div>';
			    $content .= '<div class="misc-eab-section eab-start-section"><label for="incsub_event_start_'.$i.'">';
			    $content .= __('Start', self::TEXT_DOMAIN).':</label>&nbsp;';
			    $content .= '<input type="text" name="incsub_event_start['.$i.']" id="incsub_event_start_'.$i.'" class="incsub_event_picker incsub_event incsub_event_date incsub_event_start" value="" size="10" /> ';
			    $content .= '<input type="text" name="incsub_event_start_time['.$i.']" id="incsub_event_start_time_'.$i.'" class="incsub_event incsub_event_time incsub_event_start_time" value="" size="3" />';
				$content .= ' <input type="checkbox" name="incsub_event_no_start_time['.$i.']" id="incsub_event_no_start_time_'.$i.'" class="incsub_event incsub_event_time incsub_event_no_start_time" value="1" />';
				$content .= ' <label for="incsub_event_no_start_time_'.$i.'">' . __('No start time', self::TEXT_DOMAIN) . '</label>';
			    $content .= '</div>';
			    
			    $content .= '<div class="misc-eab-section"><label for="incsub_event_end_'.$i.'">';
			    $content .= __('End', self::TEXT_DOMAIN).':</label> &nbsp;&nbsp;';
			    $content .= '<input type="text" name="incsub_event_end['.$i.']" id="incsub_event_end_'.$i.'" class="incsub_event_picker incsub_event incsub_event_date incsub_event_end" value="" size="10" /> ';
			    $content .= '<input type="text" name="incsub_event_end_time['.$i.']" id="incsub_event_end_time_'.$i.'" class="incsub_event incsub_event_time incsub_event_end_time" value="" size="3" />';
				$content .= ' <input type="checkbox" name="incsub_event_no_end_time['.$i.']" id="incsub_event_no_end_time_'.$i.'" class="incsub_event incsub_event_time incsub_event_no_end_time" value="1" />';
				$content .= ' <label for="incsub_event_no_end_time_'.$i.'">' . __('No end time', self::TEXT_DOMAIN) . '</label>';
			    $content .= '</div>';
			    $content .= '</div>';
			}
			$content .= '</div>';
			
			$content .= '<div id="eab-add-more"><input type="button" name="eab-add-more-button" id="eab-add-more-button" class="eab_add_more" value="'.__('Click here to add another date to event', self::TEXT_DOMAIN).'"/></div>';
			
			$content .= '<div id="eab-add-more-bank">';
			$content .= '<div class="eab-section-block">';
			$content .= '<div class="eab-section-heading">'.__('Part bank', self::TEXT_DOMAIN).'</div>';
			$content .= '<div class="misc-eab-section eab-start-section"><label for="incsub_event_start_bank" >';
			$content .= __('Start', self::TEXT_DOMAIN).':</label>&nbsp;';
			$content .= '<input type="text" name="incsub_event_start_b[bank]" id="incsub_event_start_bank" class="incsub_event_picker_b incsub_event incsub_event_date incsub_event_start_b" value="" size="10" /> ';
			$content .= '<input type="text" name="incsub_event_start_time_b[bank]" id="incsub_event_start_time_bank" class="incsub_event incsub_event_time incsub_event_start_time_b" value="" size="3" />';
			$content .= ' <input type="checkbox" name="incsub_event_no_start_time[bank]" id="incsub_event_no_start_time_bank" class="incsub_event incsub_event_time incsub_event_no_start_time" value="1" />';
			$content .= ' <label for="incsub_event_no_start_time_bank">' . __('No start time', self::TEXT_DOMAIN) . '</label>';
			$content .= '</div>';
			
			$content .= '<div class="misc-eab-section eab-end-section"><label for="incsub_event_end_bank">';
			$content .= __('End', self::TEXT_DOMAIN).':</label>&nbsp;&nbsp;';
			$content .= '<input type="text" name="incsub_event_end_b[bank]" id="incsub_event_end_bank" class="incsub_event_picker_b incsub_event incsub_event_date incsub_event_end_b" value="" size="10" /> ';
			$content .= '<input type="text" name="incsub_event_end_time_b[bank]" id="incsub_event_end_time_bank" class="incsub_event incsub_event_time incsub_event_end_time_b" value="" size="3" />';
			$content .= ' <input type="checkbox" name="incsub_event_no_end_time[bank]" id="incsub_event_no_end_time_bank" class="incsub_event incsub_event_time incsub_event_no_end_time" value="1" />';
			$content .= ' <label for="incsub_event_no_end_time_bank">' . __('No end time', self::TEXT_DOMAIN) . '</label>';
			$content .= '</div></div>';
			$content .= '</div>';
		} else {
			$content .= $this->_meta_box_part_recurring_edit($event);
		}
		
		$content .= '</div>';

		return $content;
    }
	
	private function _meta_box_part_recurring_edit ($event) {
		$events = Eab_CollectionFactory::get_all_recurring_children_events($event);
		$dt_format = get_option('date_format') . ' ' . get_option('time_format');
		
		$selection = '<h4><a href="#edit-instances" id="eab_event-edit_recurring_instances">' . __('Edit instances', self::TEXT_DOMAIN) . '</a></h4>';
		$selection .= "<ul id='eab_event-recurring_instances' style='display:none'>";
		foreach ($events as $instance) {
			$url = admin_url('post.php?post=' . $instance->get_id() . '&action=edit');
			$start = date($dt_format, $instance->get_start_timestamp());
			$selection .= "<li><a href='{$url}'>{$start}</a></li>";
		}
		$selection .= '</ul>';
		
		return $selection;
	}
	
	private function _meta_box_part_recurring_add ($event) {
		if ($event->is_recurring_child()) return false;
		
		$supported_intervals = $event->get_supported_recurrence_intervals();
		
		if (!$event->is_recurring()) {
			$content = '<div id="eab-start_recurrence">' .
				'<input type="button" id="eab-eab-start_recurrence-button" class="button" value="' .
					__('This is recurring event', self::TEXT_DOMAIN) .
					'" data-eab-alter_label="' . __('This is a regular event', self::TEXT_DOMAIN) . '" ' .
				' />' .
			'</div>';
		}
		
		$style = $event->is_recurring() ? '' : 'style="display:none"';
		$content .= '<div id="eab_event-recurring_event" ' . $style . '>';
		
		$parts = wp_parse_args(
			$event->get_recurrence_parts(),
			array(
				'month' => '', 'weekday' => '', 'day' => '', 'time' => '', 'duration' => '',
			)
		);
		
		$starts_ts = $event->get_recurrence_starts();
		$starts_ts = $starts_ts ? $starts_ts : time();
		$starts = date('Y-m-d', $starts_ts);
		
		$ends_ts = $event->get_recurrence_ends();
		$ends_ts = $ends_ts ? $ends_ts : strtotime("+1 month");
		$ends = date('Y-m-d', $ends_ts);
		
		// Start on...
		$content .= '<label for="eab_event-repeat_start">' . __('Start on', self::TEXT_DOMAIN);
		$content .= ' <input type="text" name="eab_repeat[repeat_start]" id="eab_event-repeat_start" value="' . $starts . '" />';
		$content .= '</label>';
		
		// Repeat every...
		$content .= '<br />';
		$content .= '<label for="eab_event-repeat_every">' . __('Repeat every', self::TEXT_DOMAIN);
		$content .= ' <select name="eab_repeat[repeat_every]" id="eab_event-repeat_every">';
		$content .= '<option value="">' . __('Select one', self::TEXT_DOMAIN) . '</option>';
		foreach ($supported_intervals as $key => $label) {
			$selected = $event->is_recurring($key) ? 'selected="selected"' : '';
			$content .= "<option value='{$key}' {$selected}>{$label}</option>";
		}
		$content .= '</select>';
		$content .= '</label>';
		
		// ... Year
		if (in_array(Eab_EventModel::RECURRANCE_YEARLY, array_keys($supported_intervals))) {
			$style = $event->is_recurring(Eab_EventModel::RECURRANCE_YEARLY) ? '' : 'style="display:none"';
			$content .= '<div class="eab_event_recurrence_mode" id="eab_event-repeat_interval-' . Eab_EventModel::RECURRANCE_YEARLY . '" ' . $style . '>';
			$content .= '<select name="eab_repeat[month]">';
			for ($i=1; $i<=12; $i++) {
				$month = date('F', strtotime("2012-{$i}-01"));
				$selected = ($month == $parts['month']) ? 'selected="selected"' : ''; 
				$content .= "<option value='{$i}' {$selected}>{$month}</option>";
			}
			$content .= '</select> ';
			$content .= __('On', self::TEXT_DOMAIN) . ' <input type="text" size="2" name="eab_repeat[day]" id="" value="' . $parts["day"] . '" /> '; // Date
			$content .= __('At', self::TEXT_DOMAIN) . ' <input type="text" size="5" name="eab_repeat[time]" id="" value="' . $parts["time"] . '" /> <small>HH:mm</small>'; // Time
			$content .= '</div>';
		}

		// ... Month
		if (in_array(Eab_EventModel::RECURRANCE_MONTHLY, array_keys($supported_intervals))) {
			$style = $event->is_recurring(Eab_EventModel::RECURRANCE_MONTHLY) ? '' : 'style="display:none"';
			$content .= '<div class="eab_event_recurrence_mode" id="eab_event-repeat_interval-' . Eab_EventModel::RECURRANCE_MONTHLY . '" ' . $style . '>';
			$content .= __('On', self::TEXT_DOMAIN) . ' <input type="text" size="2" name="eab_repeat[day]" id="" value="' . $parts["day"] . '" /> '; // Date
			$content .= __('At', self::TEXT_DOMAIN) . ' <input type="text" size="5" name="eab_repeat[time]" id="" value="' . $parts["time"] . '" /> <small>HH:mm</small>'; // Time
			$content .= '</div>';
		}

		// ... Week
		if (in_array(Eab_EventModel::RECURRANCE_WEEKLY, array_keys($supported_intervals))) {
			$style = $event->is_recurring(Eab_EventModel::RECURRANCE_WEEKLY) ? '' : 'style="display:none"';
			$content .= '<div class="eab_event_recurrence_mode" id="eab_event-repeat_interval-' . Eab_EventModel::RECURRANCE_WEEKLY . '" ' . $style . '>';
			$tmp = strtotime("this Sunday");
			for ($i=0; $i<=6; $i++) {
				$checked = (is_array($parts['weekday']) && in_array($i, $parts['weekday'])) ? 'checked="checked"' : '';
				$content .= "<input type='checkbox' name='eab_repeat[weekday][]' id='' value='{$i}' {$checked} /> ";
				$content .= "<label for=''>" . date("D", $tmp) . '</label><br />';
				$tmp += 86400;
			}
			$content .= __('At', self::TEXT_DOMAIN) . ' <input type="text" size="5" name="eab_repeat[time]" id="" value="' . $parts["time"] . '" /> <small>HH:mm</small>'; // Time
			$content .= '</div>';
		}

		// ... Day
		if (in_array(Eab_EventModel::RECURRANCE_DAILY, array_keys($supported_intervals))) {
			$style = $event->is_recurring(Eab_EventModel::RECURRANCE_DAILY) ? '' : 'style="display:none"';
			$content .= '<div class="eab_event_recurrence_mode" id="eab_event-repeat_interval-' . Eab_EventModel::RECURRANCE_DAILY . '" ' . $style . '>';
			$content .= __('At', self::TEXT_DOMAIN) . ' <input type="text" size="5" name="eab_repeat[time]" id="" value="' . $parts["time"] . '" /> <small>HH:mm</small>'; // Time
			$content .= '</div>';
		}
		
		// ... Until
		$content .= '<br />';
		$content .= '<label for="eab_event-repeat_end">' . __('Until', self::TEXT_DOMAIN);
		$content .= ' <input type="text" name="eab_repeat[repeat_end]" id="eab_event-repeat_end" value="' . $ends . '" />';
		$content .= '</label>';

		// ... Duration
		$content .= '<br />';
		$content .= '<label for="eab_event-repeat_event_duration">' . __('Event duration', self::TEXT_DOMAIN);
		$content .= ' <input type="text" name="eab_repeat[duration]" size="2" id="eab_event-repeat_event_duration" value="' . $parts["duration"] . '" /> ' . __('hours', self::TEXT_DOMAIN);
		$content .= '</label>';
		
		$content .= '</div>';
		
		return $content;
	}
    
    function meta_box_part_status () {
		global $post;
		$event = new Eab_EventModel($post);
		
		$status = $event->get_status(); 
		$status = $status ? $status : Eab_EventModel::STATUS_OPEN;
		
		$content  = '';
		$content .= '<div class="eab_meta_box">';
		$content .= '<div class="eab_meta_column_box">'.__('Event status', self::TEXT_DOMAIN).'</div>';
		$content .= '<input type="hidden" name="incsub_event_status_meta" value="1" />';
		$content .= '<div class="misc-eab-section"><label for="incsub_event_status" id="incsub_event_status_label">';
		$content .= __('What is the event status? ', self::TEXT_DOMAIN).':</label>&nbsp;';
		$content .= '<select name="incsub_event_status" id="incsub_event_status">';
		$content .= '	<option value="open" '.(($event->is_open())?'selected="selected"':'').' >'.__('Open', self::TEXT_DOMAIN).'</option>';
		$content .= '	<option value="closed" '.(($event->is_closed())?'selected="selected"':'').' >'.__('Closed', self::TEXT_DOMAIN).'</option>';
		$content .= '	<option value="expired" '.(($event->is_expired())?'selected="selected"':'').' >'.__('Expired', self::TEXT_DOMAIN).'</option>';
		$content .= '	<option value="archived" '.(($event->is_archived())?'selected="selected"':'').' >'.__('Archived', self::TEXT_DOMAIN).'</option>';
		$content .= '</select>';
		$content .= '</div>';
		$content .= '<div class="clear"></div>';
		$content .= '</div>';

		return $content;
    }
    
    function meta_box_part_payments () {
		global $post;
		$event = new Eab_EventModel($post);
		
		$content  = '';
		$content .= '<div class="eab_meta_box">';
		$content .= '<input type="hidden" name="incsub_event_payments_meta" value="1" />';
		$content .= '<div class="misc-eab-section">';
		$content .= '<div class="eab_meta_column_box">'.__('Event type', self::TEXT_DOMAIN).'</div>';
		$content .= '<label for="incsub_event_paid" id="incsub_event_paid_label">'.__('Is this a paid event? ', self::TEXT_DOMAIN).':</label>&nbsp;';
		$content .= '<select name="incsub_event_paid" id="incsub_event_paid" class="incsub_event_paid" >';
		$content .= '<option value="1" ' . ($event->is_premium() ? 'selected="selected"' : '') . '>'.__('Yes', self::TEXT_DOMAIN).'</option>';
		$content .= '<option value="0" ' . ($event->is_premium() ? '' : 'selected="selected"') . '>'.__('No', self::TEXT_DOMAIN).'</option>';
		$content .= '</select>';
		$content .= '<div class="clear"></div>';
		$content .= '<label class="incsub_event-fee_row" id="incsub_event-fee_row_label">'.__('Fee', self::TEXT_DOMAIN).':&nbsp;';
		$content .= $this->_data->get_option('currency') .
			'&nbsp;<input type="text" name="incsub_event_fee" id="incsub_event_fee" class="incsub_event_fee" value="' .
			$event->get_price() .
		'" size="6" /> ';
		$content .= '</label>';
		$content .= '</div>';
		$content .= '</div>';
	
		return $content;
    }
    
    function bookings_meta_box () {
		global $post;
		echo '<a href="' . admin_url('?eab_export=attendees&event_id='. $post->ID) . '" class="eab-export_attendees">' .
			__('Export', self::TEXT_DOMAIN) .
		'</a>';
		echo $this->meta_box_part_bookings($post);
	}	
	
	function meta_box_part_bookings ($post) {
		$event = new Eab_EventModel($post);
		
		$content  = '';
		$content .= '<div id="eab-bookings-response">';
		$content .= '<input type="hidden" name="incsub_event_bookings_meta" value="1" />';
		$content .= '<div class="bookings-list-left">';
		
		if ($event->has_bookings(false)) {
	    	$content .= '<div id="event-booking-yes">';
            $content .= Eab_Template::get_admin_bookings(Eab_EventModel::BOOKING_YES, $event);
            $content .= '</div>';
                
            $content .= '<div id="event-booking-maybe">';
            $content .= Eab_Template::get_admin_bookings(Eab_EventModel::BOOKING_MAYBE, $event);
            $content .= '</div>';
	    
	    	$content .= '<div id="event-booking-no">';
            $content .= Eab_Template::get_admin_bookings(Eab_EventModel::BOOKING_NO, $event);
            $content .= '</div>';
        }  else {
            $content .= __('No bookings', self::TEXT_DOMAIN);
        }
		$content .= '</div>';
		$content .= '<div class="clear"></div>';
		$content .= '</div>';

		return $content;
    }
    
    function save_event_meta($post_id, $post = null) {
		global $wpdb;
		
		//skip quick edit
		if ( defined('DOING_AJAX') )
		    return;
	    
	    // Setting up event venue
		if ( $post->post_type == "incsub_event" && isset( $_POST['incsub_event_where_meta'] ) ) {
		    $meta = get_post_custom($post_id);
		    
		    update_post_meta($post_id, 'incsub_event_venue', strip_tags($_POST['incsub_event_venue']));
		    
		    //for any other plugin to hook into
		    do_action( 'incsub_event_save_where_meta', $post_id, $meta );
		}
		
		// Setting up event status
		if ( $post->post_type == "incsub_event" && isset( $_POST['incsub_event_status_meta'] ) ) {
		    $meta = get_post_custom($post_id);
		    
		    update_post_meta($post_id, 'incsub_event_status', strip_tags($_POST['incsub_event_status']));
		    
		    //for any other plugin to hook into
		    do_action( 'incsub_event_save_status_meta', $post_id, $meta );
		}
		
		// Setting up event payments
		if ( $post->post_type == "incsub_event" && isset( $_POST['incsub_event_payments_meta'] ) ) {
		    $meta = get_post_custom($post_id);
		    
			$is_paid = (int)$_POST['incsub_event_paid'];
			$fee = $is_paid ? strip_tags($_POST['incsub_event_fee']) : '';
		    update_post_meta($post_id, 'incsub_event_paid', ($is_paid ? '1' : ''));
		    update_post_meta($post_id, 'incsub_event_fee', $fee);
		    
		    //for any other plugin to hook into
		    do_action( 'incsub_event_save_payments_meta', $post_id, $meta );
		}
		
		// Setting up recurring event
		if ('incsub_event' == $post->post_type && isset($_POST['eab_repeat'])) {
			$repeat = $_POST['eab_repeat'];
			$start = $repeat['repeat_start'] ? strtotime($repeat['repeat_start']) : time();
			$end =  $repeat['repeat_end'] ? strtotime($repeat['repeat_end']) : time();
			if ($end <= $start) {
				// BAH! Wrong order
			}
			$interval = $repeat['repeat_every'];
			$time_parts = array(
				'month' => @$repeat['month'],
				'day' => @$repeat['day'],
				'weekday' => @$repeat['weekday'],
				'time' => @$repeat['time'],
				'duration' => @$repeat['duration'],
			);
			$event = new Eab_EventModel($post);
			$event->spawn_recurring_instances($start, $end, $interval, $time_parts); //@TODO: Improve
		}
		
		if ( $post->post_type == "incsub_event" && isset( $_POST['incsub_event_when_meta'] ) ) {
		    $meta = get_post_custom($post_id);

			delete_post_meta($post_id, 'incsub_event_start');
			delete_post_meta($post_id, 'incsub_event_no_start');
			delete_post_meta($post_id, 'incsub_event_end');
			delete_post_meta($post_id, 'incsub_event_no_end');
		   	if (isset($_POST['incsub_event_start']) && count($_POST['incsub_event_start']) > 0) foreach ($_POST['incsub_event_start'] as $i => $event_start) {
		   		if (!empty($_POST['incsub_event_start'][$i])) {
					$start_time = @$_POST['incsub_event_no_start_time'][$i] ? '00:01' : @$_POST['incsub_event_start_time'][$i];
				    add_post_meta($post_id, 'incsub_event_start', date('Y-m-d H:i:s', strtotime("{$_POST['incsub_event_start'][$i]} {$start_time}")));
				    if (@$_POST['incsub_event_no_start_time'][$i]) add_post_meta($post_id, 'incsub_event_no_start', 1);
				} 
				if (!empty($_POST['incsub_event_end'][$i])) {
		   			$end_time = @$_POST['incsub_event_no_end_time'][$i] ? '23:59' : @$_POST['incsub_event_end_time'][$i];
				    add_post_meta($post_id, 'incsub_event_end', date('Y-m-d H:i:s', strtotime("{$_POST['incsub_event_end'][$i]} {$end_time}")));
				    if (@$_POST['incsub_event_no_end_time'][$i]) add_post_meta($post_id, 'incsub_event_no_end', 1);
				} 
			}
		    //for any other plugin to hook into
		    do_action( 'incsub_event_save_when_meta', $post_id, $meta );
		}

		if ('incsub_event' == $post->post_type) do_action('eab-event_meta-save_meta', $post_id);
		if ('incsub_event' == $post->post_type) do_action('eab-event_meta-after_save_meta', $post_id);
    }
    
    function post_type_link($permalink, $post_id, $leavename) {
		global $event_variation;
		
		$post = get_post($post_id);
		
		$rewritecode = array(
		    '%incsub_event%',
		    '%event_year%',
		    '%event_monthnum%'
		);
		
		if ($post->post_type == 'incsub_event' && '' != $permalink) {
		    
		    $ptype = get_post_type_object($post->post_type);
		    
		    $start = time();
		    $end = time();
		    
		    $meta = get_post_custom($post->ID);
		    if (isset($meta["incsub_event_start"])) {// && isset($meta["incsub_event_start"][$event_variation[$post->ID]])) {
				//$start = strtotime($meta["incsub_event_start"][$event_variation[$post->ID]]);
				$start = strtotime($meta["incsub_event_start"][0]);
		    }

		    $year = date('Y', $start);
		    $month = date('m', $start);
		    
		    $rewritereplace = array(
		    	($post->post_name == "")?(isset($post->id)?$post->id:0):$post->post_name,
				$year,
				$month,
		    );
		    $permalink = str_replace($rewritecode, $rewritereplace, $permalink);
		} else {
		    // if they're not using the fancy permalink option
		}
		
		return $permalink;
    }
    
    function add_rewrite_rules($rules){
		$new_rules = array();
		$slug = $this->_data->get_option('slug');
		unset($rules[$slug . '/([0-9]{4})/([0-9]{1,2})/?$']);
		unset($rules[$slug .'/([0-9]{4})/?$']);
		$new_rules[$slug .'/([0-9]{4})/?$'] = 'index.php?event_year=$matches[1]&post_type=incsub_event';
		$new_rules[$slug .'/([0-9]{4})/([0-9]{1,2})/?$'] = 'index.php?event_year=$matches[1]&event_monthnum=$matches[2]&post_type=incsub_event';
		$new_rules[$slug .'/([0-9]{4})/([0-9]{2})/(.+?)/?$'] = 'index.php?event_year=$matches[1]&event_monthnum=$matches[2]&incsub_event=$matches[3]';
		
		return array_merge($new_rules, $rules);
    }
    
    function check_rewrite_rules($value) {
		//prevent an infinite loop
		if ( ! post_type_exists( 'incsub_event' ) )
		    return $value;
		
		if (!is_array($value))
		    $value = array();
		
		$slug = $this->_data->get_option('slug');
		$array_key = $slug . '/([0-9]{4})/?$';
		if ( !array_key_exists($array_key, $value) ) {
		    $this->flush_rewrite();
		}
		$array_key = $slug . '/([0-9]{4})/([0-9]{1,2})/?$';
		if ( !array_key_exists($array_key, $value) ) {
		    $this->flush_rewrite();
		}
		$array_key = $slug . '/([0-9]{4})/([0-9]{1,2})/(.+?)/?$';
		if ( !array_key_exists($array_key, $value) ) {
		    $this->flush_rewrite();
		}
		return $value;
    }
    
    function query_vars($vars) {
		array_push($vars, 'event_year');
		array_push($vars, 'event_monthnum');
		return $vars;
    }
    
    function manage_posts_columns($old_columns)	{
		global $post_status;

		$columns['cb'] = $old_columns['cb'];
		//$columns['title'] = $old_columns['title'];
		$columns['event'] = $old_columns['title'];
		$columns['start'] = __('When', self::TEXT_DOMAIN);
		$columns['venue'] = __('Where', self::TEXT_DOMAIN);
		$columns['author'] = $old_columns['author'];
		$columns['date'] = $old_columns['date'];
		$columns['attendees'] = __('RSVPs', self::TEXT_DOMAIN);
		
		return $columns;
    }
    
    function manage_posts_custom_column($column) {
		global $post;
	
		switch ($column) {
			case "attendees":
				global $wpdb;
				$event = ($post instanceof Eab_EventModel) ? $post : new Eab_EventModel($post);
				$yes = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM ".Eab_EventsHub::tablename(Eab_EventsHub::BOOKING_TABLE)." WHERE event_id = %d AND status = %s;", $event->get_id(), Eab_EventModel::BOOKING_YES));
				$no = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM ".Eab_EventsHub::tablename(Eab_EventsHub::BOOKING_TABLE)." WHERE event_id = %d AND status = %s;", $event->get_id(), Eab_EventModel::BOOKING_NO));
				$maybe = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM ".Eab_EventsHub::tablename(Eab_EventsHub::BOOKING_TABLE)." WHERE event_id = %d AND status = %s;", $event->get_id(), Eab_EventModel::BOOKING_MAYBE));
				printf('<b>' . __('Attending / Undecided', self::TEXT_DOMAIN) . ':</b> %d / %d<br />', $yes, $maybe);
				printf('<b>' . __('Not Attending', self::TEXT_DOMAIN) . ':</b> %d', $no);
				echo '&nbsp;';
				echo '<a href="' . admin_url('?eab_export=attendees&event_id='. $event->get_id()) . '" class="eab-export_attendees">' .
					__('Export', self::TEXT_DOMAIN) .
				'</a>';
				break;
			case "start":
				$event = new Eab_EventModel($post);
				if (!$event->is_recurring()) {
					echo date(get_option('date_format', 'Y-m-d'), $event->get_start_timestamp());
				} else {
					$repeats = $event->get_supported_recurrence_intervals();
					$title = @$repeats[$event->get_recurrence()];
					$start = date(get_option('date_format', 'Y-m-d'), $event->get_recurrence_starts());
					$end = date(get_option('date_format', 'Y-m-d'), $event->get_recurrence_starts());
					printf(__("From %s, repeats every %s until %s", self::TEXT_DOMAIN), $start, $title, $end);
				}
				break;
			case "venue":
				$event = new Eab_EventModel($post);
				echo $event->get_venue_location();
				break;
			case "event":
				$event = new Eab_EventModel($post);
				$post_type_object = get_post_type_object($post->post_type);
				$edit_link = get_edit_post_link($event->get_id());
				
				$statuses = array();
				if ('draft' == $post->post_status) $statuses[] = __('Draft');
				if ('private' == $post->post_status) $statuses[] = __('Private');
				if ('pending' == $post->post_status) $statuses[] = __('Pending');
				$status = $statuses ? ' - <span class="post-state">' . join(', ', $statuses) . '</span>' : '';
				
				$title = (current_user_can($post_type_object->cap->edit_post, $event->get_id()) && 'trash' != $post->post_status)
					? '<strong>' . '<a class="row-title" href="' . $edit_link .'" title="' . esc_attr(sprintf(__('Edit &#8220;%s&#8221;' ), $event->get_title())) . '">' . $event->get_title() . '</a>&nbsp;' . $status . '</strong>'
					: '<strong>' . $event->get_title() . '&nbsp;' . $status . '</strong>'
				;
				
				if (current_user_can($post_type_object->cap->edit_post, $event->get_id()) && 'trash' != $post->post_status) {
					$actions['edit'] = '<a title="' . esc_attr(__('Edit Event', self::TEXT_DOMAIN)) . '" href="' . $edit_link . '">' . __('Edit') . '</a>';
					$actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="' . esc_attr(__( 'Edit this Event inline', self::TEXT_DOMAIN)) . '">' . __('Quick&nbsp;Edit') . '</a>';
				}
				
				if (current_user_can($post_type_object->cap->delete_post, $event->get_id())) {
					if ('trash' == $post->post_status) {
						$actions['untrash'] = "<a title='" . esc_attr(__('Restore this Event from the Trash', self::TEXT_DOMAIN)) . "' href='" . wp_nonce_url(admin_url(sprintf($post_type_object->_edit_link . '&amp;action=untrash', $event->get_id())), 'untrash-' . $post->post_type . '_' . $event->get_id()) . "'>" . __( 'Restore' ) . "</a>";
					} else if (EMPTY_TRASH_DAYS) {
						$actions['trash'] = '<a class="submitdelete" title="' . esc_attr(__('Move this Event to the Trash', self::TEXT_DOMAIN)) . '" href="' . get_delete_post_link($event->get_id()) . '">' . __('Trash') . '</a>';
					}
					if ('trash' == $post->post_status || !EMPTY_TRASH_DAYS) {
						$actions['delete'] = "<a class='submitdelete' title='" . esc_attr(__('Delete this Event permanently'), self::TEXT_DOMAIN) . "' href='" . get_delete_post_link($event->get_id(), '', true ) . "'>" . __( 'Delete Permanently' ) . "</a>";
					}
				}
				
				if ('trash' != $post->post_status) {
					$event_id = $event->get_id();
					if ($event->is_recurring()) {
						$children = Eab_CollectionFactory::get_all_recurring_children_events($event);
						if (!$children || !($children[0]) instanceof Eab_EventModel) $event_id = false;
						else $event_id = $children[0]->get_id(); 
					}
					if ($event_id) {
						$actions['view'] = '<a href="' . get_permalink($event_id) . '" title="' . esc_attr(sprintf(__( 'View &#8220;%s&#8221;'), $event->get_title())) . '" rel="permalink">' . __('View') . '</a>';
					}
				}
				echo $title . WP_List_Table::row_actions($actions);
				get_inline_data($post);
				break;
		}
    }
    
    /**
     * Activation hook
     * 
     * Create tables if they don't exist and add plugin options
     * 
     * @see     http://codex.wordpress.org/Function_Reference/register_activation_hook
     * 
     * @global	object	$wpdb
     */
    function install () {
        global $wpdb;
        
		/**
		 * WordPress database upgrade/creation functions
		 */
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	    	
		// Get the correct character collate
		if ( ! empty($wpdb->charset) )
	            $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		if ( ! empty($wpdb->collate) )
		    $charset_collate .= " COLLATE $wpdb->collate";
		
		$sql_main = "CREATE TABLE IF NOT EXISTS ".self::tablename(self::BOOKING_TABLE)." (
				`id` BIGINT NOT NULL AUTO_INCREMENT,
	                        `event_id` BIGINT NOT NULL ,
	                        `user_id` BIGINT NOT NULL ,
				`timestamp` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' ,
				`status` ENUM( 'paid', 'yes', 'maybe', 'no' ) NOT NULL DEFAULT 'no' ,
		    		PRIMARY KEY (`id`),
				UNIQUE KEY `event_id_2` (`event_id`,`user_id`),
				KEY `event_id` (`event_id`),
				KEY `user_id` (`user_id`),
				KEY `timestamp` (`timestamp`),
				KEY `status` (`status`)
			    ) ENGINE = InnoDB {$charset_collate};";
		dbDelta($sql_main);
	        
	        $sql_main = "CREATE TABLE ".self::tablename(self::BOOKING_META_TABLE)." (
				`id` BIGINT NOT NULL AUTO_INCREMENT,
				`booking_id` BIGINT NOT NULL ,
	                        `meta_key` VARCHAR(255) NOT NULL ,
	                        `meta_value` TEXT NOT NULL DEFAULT '',
		    		PRIMARY KEY (`id`),
				KEY `booking_id` (`booking_id`),
				KEY `meta_key` (`meta_key`)
			    ) ENGINE = InnoDB {$charset_collate};";
		dbDelta($sql_main);

        
    	if (!get_option('event_default', false)) add_option('event_default', array());
		if (!get_option('eab_activation_redirect', true)) add_option('eab_activation_redirect', true);
    }
    
    function user_has_cap($allcaps, $caps = null, $args = null) {
		global $current_user, $blog_id, $post;

		$capable = false;

		if (preg_match('/(_event|_events)/i', join($caps, ',')) > 0) {
		    if (in_array('administrator', $current_user->roles)) {
				foreach ($caps as $cap) {
				    $allcaps[$cap] = 1;
				}
				return $allcaps;
		    }
		    foreach ($caps as $cap) {
				$capable = false;
				switch ($cap) {
				    case 'read_events':
						$capable = true;
						break;
				    default:
						if (isset($args[1]) && isset($args[2])) {
						    if (current_user_can(preg_replace('/_event/i', '_post', $cap), $args[1], $args[2])) {
								$capable = true;
						    }
						} else if (isset($args[1])) {
						    if (current_user_can(preg_replace('/_event/i', '_post', $cap), $args[1])) {
								$capable = true;
						    }
						} else if (current_user_can(preg_replace('/_event/i', '_post', $cap))) {
						    $capable = true;
						}
						break;
				}
				$capable = apply_filters('eab-capabilities-user_can', $cap, $current_user, $capable, $args);
				
				if ($capable) {
				    $allcaps[$cap] = 1;
				}
		    }
		}
		return $allcaps;
    }
    
    function flush_rewrite() {
	global $wp_rewrite;
	
	$wp_rewrite->flush_rules();
    }
    
    /**
     * Deactivation hook
     * 
     * @see		http://codex.wordpress.org/Function_Reference/register_deactivation_hook
     *  
     * @global	object	$wpdb
     */
    function uninstall() {
    	global $wpdb;
	// Nothing to do
    }
    
    /**
     * Add the admin menus
     * 
     * @see		http://codex.wordpress.org/Adding_Administration_Menus
     */
    function admin_menu() {
		global $submenu;
	        global $menu;
		
		if (get_option('eab_setup', false) == false) {
		    add_submenu_page('edit.php?post_type=incsub_event', __("Get Started", self::TEXT_DOMAIN), __("Get started", self::TEXT_DOMAIN), 'manage_options', 'eab_welcome', array(&$this,'welcome_render'));
		    
		    if (isset($submenu['edit.php?post_type=incsub_event']) && is_array($submenu['edit.php?post_type=incsub_event'])) foreach ($submenu['edit.php?post_type=incsub_event'] as $k=>$item) {
				if ($item[2] == 'eab_welcome') {
				    $submenu['edit.php?post_type=incsub_event'][1] = $item;
				    unset($submenu['edit.php?post_type=incsub_event'][$k]);
				}
		    }
		}
		add_submenu_page('edit.php?post_type=incsub_event', __("Event Settings", self::TEXT_DOMAIN), __("Settings", self::TEXT_DOMAIN), 'manage_options', 'eab_settings', array(&$this,'settings_render'));
		if (isset($submenu['edit.php?post_type=incsub_event']) && is_array($submenu['edit.php?post_type=incsub_event'])) ksort($submenu['edit.php?post_type=incsub_event']);
    }
	    
    function cron_schedules($schedules) {
		$schedules['thirtyminutes'] = array( 'interval' => 1800, 'display' => __('Once every half an hour', self::TEXT_DOMAIN) );
		
		return $schedules;
    }
    
    function welcome_render() {
	?>
	<div class="wrap">
	    <div id="icon-events-general" class="icon32"><br/></div>
	    <h2><?php _e('Getting started', self::TEXT_DOMAIN); ?></h2>
	    
	    <p>
	    	<?php _e('Events gives you a flexible WordPress-based system for organizing parties, dinners, fundraisers - you name it.', self::TEXT_DOMAIN) ?>
	    </p>
	    
	    <div class="eab-metaboxcol metabox-holder eab-metaboxcol-one eab-metaboxcol-center">
		<div id="eab-actionlist" class="eab-metabox postbox">
		    <h3 class="eab-hndle"><?php _e('Getting Started', self::TEXT_DOMAIN); ?></h3>
		    <div class="eab-inside">
				<div class="eab-note"><?php _e('You\'re almost ready! Follow these steps and start creating events on your WordPress site.', self::TEXT_DOMAIN); ?></div>
			<ol>
			    <li>
				<?php _e('Before creating an event, you\'ll need to configure some basic settings, like your root slug and payment options.', self::TEXT_DOMAIN); ?>
				<a href="edit.php?post_type=incsub_event&page=eab_settings&eab_step=1" class="eab-goto-step button" id="eab-goto-step-0" ><?php _e('Configure Your Settings', self::TEXT_DOMAIN); ?></a>
			    </li>
			    <li>
				<?php _e('Now you can create your first event.', self::TEXT_DOMAIN); ?>
				<a href="post-new.php?post_type=incsub_event&eab_step=2" class="eab-goto-step button"><?php _e('Add an Event', self::TEXT_DOMAIN); ?></a>
			    </li>	
			    <li>
				<?php _e('You can view and edit your existing events whenever you like.', self::TEXT_DOMAIN); ?>
				<a href="edit.php?post_type=incsub_event&eab_step=3" class="eab-goto-step button"><?php _e('Edit Events', self::TEXT_DOMAIN); ?></a>
			    </li>	
			    <li>
				<?php _e('The archive displays a list of upcoming events on your site.', self::TEXT_DOMAIN); ?>
				<a href="<?php echo site_url($this->_data->get_option('slug')); ?>" class="eab-goto-step button"><?php _e('Events Archive', self::TEXT_DOMAIN); ?></a>
			    </li>	
			</ol>
		    </div>
		</div>
	    </div>
	
		<?php if (!defined('WPMUDEV_REMOVE_BRANDING') || !constant('WPMUDEV_REMOVE_BRANDING')) { ?>
	    <div class="eab-metaboxcol metabox-holder eab-metaboxcol-one eab-metaboxcol-center">
			<div id="eab-helpbox" class="eab-metabox postbox">
			    <h3 class="eab-hndle"><?php _e('Need help?', self::TEXT_DOMAIN); ?></h3>
			    <div class="eab-inside">
					<ol>
					    <li><a href="http://premium.wpmudev.org/project/events-and-booking"><?php _e('Check out the Events plugin page on WPMU DEV', self::TEXT_DOMAIN); ?></a></li>
					    <li><a href="http://premium.wpmudev.org/forums/tags/events-and-bookings"><?php _e('Post a question about this plugin on our support forums', self::TEXT_DOMAIN); ?></a></li>
					    <li><a href="http://premium.wpmudev.org/project/events-and-booking/installation/"><?php _e('Watch a video of the Events plugin in action', self::TEXT_DOMAIN); ?></a></li>
					</ol>
			    </div>
			</div>
	    </div>
	    <?php } ?>
	    
	    <div class="clear"></div>
	    
	    <div class="eab-dashboard-footer">
	
	    </div>
	</div>
	<?php
    }
    
    function views_list($views) {
	global $wp_query;
	
	$avail_post_stati = wp_edit_posts_query();
	$num_posts = wp_count_posts( 'incsub_event', 'readable' );
	
	$argvs = array('post_type' => 'incsub_event');
	// $argvs = array();
	foreach ( get_post_stati($argvs, 'objects') as $status ) {
	    $class = '';
	    $status_name = $status->name;
	    if ( !in_array( $status_name, $avail_post_stati ) )
	        continue;
	    
	    if ( empty( $num_posts->$status_name ) )
	        continue;
	    
	    if ( isset($_GET['post_status']) && $status_name == $_GET['post_status'] )
	        $class = ' class="current"';
	    
	    $views[$status_name] = "<li><a href='edit.php?post_type=incsub_event&amp;post_status=$status_name'$class>" . sprintf( _n( $status->label_count[0], $status->label_count[1], $num_posts->$status_name ), number_format_i18n( $num_posts->$status_name ) ) . '</a>';
	}
	
	return $views;
    }
    
    function settings_render() {
		if(!current_user_can('manage_options')) {
	  		echo "<p>" . __('Nice Try...', self::TEXT_DOMAIN) . "</p>";  //If accessed properly, this message doesn't appear.
	  		return;
	  	}
		if (isset($_GET['incsub_event_settings_saved']) && $_GET['incsub_event_settings_saved'] == 1) {
		    echo '<div class="updated fade"><p>'.__('Settings saved.', self::TEXT_DOMAIN).'</p></div>';
	    }
		if (!class_exists('WpmuDev_HelpTooltips')) require_once dirname(__FILE__) . '/lib/class_wd_help_tooltips.php';
		$tips = new WpmuDev_HelpTooltips();
		$tips->set_icon_url(plugins_url('events-and-bookings/img/information.png'));
		
		$archive_tpl = file_exists(get_stylesheet_directory().'/archive-incsub_event.php') 
			? get_stylesheet_directory() . '/archive-incsub_event.php'
		    : get_template_directory() . '/archive-incsub_event.php'
		;
		$archive_tpl_present = apply_filters(
			'eab-settings-appearance-archive_template_copied',
			file_exists($archive_tpl)
		); 
		
		$single_tpl = file_exists(get_stylesheet_directory().'/single-incsub_event.php') 
			? get_stylesheet_directory() . '/single-incsub_event.php'
		    : get_template_directory() . '/single-incsub_event.php'
		;
		$single_tpl_present = apply_filters(
			'eab-settings-appearance-single_template_copied',
			file_exists($single_tpl)
		); 
		
		$theme_tpls_present = apply_filters(
			'eab-settings-appearance-templates_copied', 
			($archive_tpl_present && $single_tpl_present)
		);
		$raw_tpl_sets = glob(EAB_PLUGIN_DIR . 'default-templates/*');
		$templates = array();
		foreach ($raw_tpl_sets as $item) {
			if (!is_dir($item)) continue;
			$key = basename($item);
			$label = ucwords(preg_replace('/[^a-z0-9]+/i', ' ', $key));
			$templates[$key] = sprintf(__("Plugin: %s", self::TEXT_DOMAIN), $label);
		}
		foreach (get_page_templates() as $name => $tpl) {
			$templates[$tpl] = sprintf(__("Theme: %s", self::TEXT_DOMAIN), $name);
			
		}
	?>
	<div class="wrap">
	    <div id="icon-events-general" class="icon32"><br/></div>
	    <h2><?php _e('Events Settings', self::TEXT_DOMAIN); ?></h2>
	    <div class="eab-note">
		<p><?php _e('This is where you manage your general settings for the plugin and how events are displayed on your site.', self::TEXT_DOMAIN); ?>.</p>
	    </div>
	    <form method="post" action="edit.php?post_type=incsub_event&page=eab_settings">
		<?php wp_nonce_field('incsub_event-update-options'); ?>
		<div class="eab-metaboxcol metabox-holder eab-metaboxcol-one eab-metaboxcol-center">
		    <?php do_action('eab-settings-before_plugin_settings'); ?>
		    <div id="eab-settings-general" class="eab-metabox postbox">
				<h3 class="eab-hndle"><?php _e('Plugin settings :', self::TEXT_DOMAIN); ?></h3>
				<div class="eab-inside">
					<div class="eab-settings-settings_item">
					    <label for="incsub_event-slug" id="incsub_event_label-slug"><?php _e('Set your root slug here:', self::TEXT_DOMAIN); ?></label>
						/<input type="text" size="20" id="incsub_event-slug" name="event_default[slug]" value="<?php print $this->_data->get_option('slug'); ?>" />
						<span><?php echo $tips->add_tip(__('This is the URL where your events archive can be found. By default, the format is yoursite.com/events, but you can change this to whatever you want.', self::TEXT_DOMAIN)); ?></span>
				    </div>
					    
					<div class="eab-settings-settings_item">
					    <label for="incsub_event-accept_payments" id="incsub_event_label-accept_payments"><?php _e('Will you be accepting payment for any of your events?', self::TEXT_DOMAIN); ?></label>
						<input type="checkbox" size="20" id="incsub_event-accept_payments" name="event_default[accept_payments]" value="1" <?php print ($this->_data->get_option('accept_payments') == 1)?'checked="checked"':''; ?> />
						<span><?php echo $tips->add_tip(__('Leave this box unchecked if you don\'t intend to collect payment at any time.', self::TEXT_DOMAIN)); ?></span>
				    </div>
	
					<div class="eab-settings-settings_item">
					    <label for="incsub_event-accept_api_logins" id="incsub_event_label-accept_api_logins"><?php _e('Allow Facebook and Twitter Login?', self::TEXT_DOMAIN); ?></label>
						<input type="checkbox" size="20" id="incsub_event-accept_api_logins" name="event_default[accept_api_logins]" value="1" <?php print ($this->_data->get_option('accept_api_logins') == 1)?'checked="checked"':''; ?> />
						<span><?php echo $tips->add_tip(__('Check this box to allow guests to RSVP to an event with their Facebook or Twitter account. (If this feature is not enabled, guests will need a WordPress account to RSVP).', self::TEXT_DOMAIN)); ?></span>
				    </div>
					    
					<div class="eab-settings-settings_item">
					    <label for="incsub_event-display_attendees" id="incsub_event_label-display_attendees"><?php _e('Display public RSVPs?', self::TEXT_DOMAIN); ?></label>
						<input type="checkbox" size="20" id="incsub_event-display_attendees" name="event_default[display_attendees]" value="1" <?php print ($this->_data->get_option('display_attendees') == 1)?'checked="checked"':''; ?> />
						<span><?php echo $tips->add_tip(__('Check this box to display a "who\'s attending" list in the event details.', self::TEXT_DOMAIN)); ?></span>
				    </div>
				</div>
		    </div>
		    <?php if (!$theme_tpls_present) { ?>
		    <div id="eab-settings-general" class="eab-metabox postbox">
				<h3 class="eab-hndle"><?php _e('Appearance settings :', self::TEXT_DOMAIN); ?></h3>
				<div class="eab-inside">
					<div class="eab-settings-settings_item">
					    <label for="incsub_event-override_appearance_defaults" id="incsub_event_label-override_appearance_defaults"><?php _e('Override default appearance?', self::TEXT_DOMAIN); ?></label>
						<input type="checkbox" size="20" id="incsub_event-override_appearance_defaults" name="event_default[override_appearance_defaults]" value="1" <?php print ($this->_data->get_option('override_appearance_defaults') == 1)?'checked="checked"':''; ?> />
						<span><?php echo $tips->add_tip(__('Check this box if you want to customize the appearance of your events with overriding templates.', self::TEXT_DOMAIN)); ?></span>
					</div>
						
					<div class="eab-settings-settings_item">
						<?php if (!$archive_tpl_present) { ?>
					    <label for="incsub_event-archive_template" id="incsub_event_label-archive_template"><?php _e('Archive template', self::TEXT_DOMAIN); ?></label>
						<select id="incsub_event-archive_template" name="event_default[archive_template]">
						<?php foreach ($templates as $tkey => $tlabel) { ?>
							<?php $selected = ($this->_data->get_option('archive_template') == $tkey) ? 'selected="selected"' : ''; ?>
							<option value="<?php esc_attr_e($tkey);?>" <?php echo $selected;?>><?php echo $tlabel;?></option>
						<?php } ?>		
						</select>
						<span>
							<small><em>* templates may not work in all themes</em></small>
							<?php echo $tips->add_tip(__('Choose how the events archive is displayed on your site.', self::TEXT_DOMAIN)); ?>
						</span>
					    <?php } ?>
					</div>
					   
					<div class="eab-settings-settings_item">
						<?php if (!$single_tpl_present) { ?>
					    <label for="incsub_event-single_template" id="incsub_event_label-single_template"><?php _e('Single Event template', self::TEXT_DOMAIN); ?></label>
						<select id="incsub_event-single_template" name="event_default[single_template]">
						<?php foreach ($templates as $tkey => $tlabel) { ?>
							<?php $selected = ($this->_data->get_option('single_template') == $tkey) ? 'selected="selected"' : ''; ?>
							<option value="<?php esc_attr_e($tkey);?>" <?php echo $selected;?>><?php echo $tlabel;?></option>
						<?php } ?>		
						</select>
						<span>
							<small><em>* templates may not work in all themes</em></small>
							<?php echo $tips->add_tip(__('Choose how single event listings are displayed on your site.', self::TEXT_DOMAIN)); ?>
						</span>
					    <?php } ?>
					</div>
					
				</div>
		    </div>
		    <?php do_action('eab-settings-after_appearance_settings'); ?>
		    <?php } ?>
		    <!-- Payment settings -->
		    <div id="eab-settings-paypal" class="eab-metabox postbox">
				<h3 class="eab-hndle"><?php _e('Payment settings :', self::TEXT_DOMAIN); ?></h3>
				<div class="eab-inside">
					<div class="eab-settings-settings_item">
					    <label for="incsub_event-currency" id="incsub_event_label-currency"><?php _e('Currency', self::TEXT_DOMAIN); ?></label>
						<input type="text" size="4" id="incsub_event-currency" name="event_default[currency]" value="<?php print $this->_data->get_option('currency'); ?>" />
						<span><?php echo $tips->add_tip(sprintf(__('Nominate the currency in which you will be accepting payment for your events. For more information see <a href="%s" target="_blank">Accepted PayPal Currency Codes</a>.', self::TEXT_DOMAIN), 'https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_nvp_currency_codes')); ?></span>
					</div>
					    
					<div class="eab-settings-settings_item">
					    <label for="incsub_event-paypal_email" id="incsub_event_label-paypal_email"><?php _e('PayPal E-Mail address', self::TEXT_DOMAIN); ?></label>
						<input type="text" size="20" id="incsub_event-paypal_email" name="event_default[paypal_email]" value="<?php print $this->_data->get_option('paypal_email'); ?>" />
						<span><?php echo $tips->add_tip(__('Add the primary email address of the PayPal account you will use to collect payment for your events.', self::TEXT_DOMAIN)); ?></span>
					</div>
					    
					<div class="eab-settings-settings_item">
					    <label for="incsub_event-paypal_sandbox" id="incsub_event_label-paypal_sandbox"><?php _e('PayPal Sandbox mode?', self::TEXT_DOMAIN); ?></label>
						<input type="checkbox" size="20" id="incsub_event-paypal_sandbox" name="event_default[paypal_sandbox]" value="1" <?php print ($this->_data->get_option('paypal_sandbox') == 1)?'checked="checked"':''; ?> />
						<span><?php echo $tips->add_tip(__('Use PayPal Sandbox mode for testing your payments', self::TEXT_DOMAIN)); ?></span>
					</div>
				</div>
		    </div>
		    <?php do_action('eab-settings-after_payment_settings'); ?>
		    <!-- API settings -->
		    <div id="eab-settings-apis" class="eab-metabox postbox">
				<h3 class="eab-hndle"><?php _e('API settings :', self::TEXT_DOMAIN); ?></h3>
				<div class="eab-inside">
					<div class="eab-settings-settings_item">
					    <label for="incsub_event-facebook-app_id" id="incsub_event_label-facebook-app_id"><?php _e('Facebook App ID', self::TEXT_DOMAIN); ?></label>
						<input type="text" id="incsub_event-facebook-app_id" name="event_default[facebook-app_id]" value="<?php print $this->_data->get_option('facebook-app_id'); ?>" />
						<span><?php echo $tips->add_tip(sprintf(__('Enter your App ID number here. If you don\'t have a Facebook App yet, you will need to create one <a href="%s">here</a>', self::TEXT_DOMAIN), 'https://developers.facebook.com/apps')); ?></span>
					</div>
		
					<div class="eab-settings-settings_item">
					    <label for="incsub_event-facebook-no_init" id="incsub_event_label-facebook-no_init"><?php _e('My pages already load scripts from Facebook', self::TEXT_DOMAIN); ?></label>
					    <input type="hidden" name="event_default[facebook-no_init]" value="" />
						<input type="checkbox" id="incsub_event-facebook-no_init" name="event_default[facebook-no_init]" <?php print ($this->_data->get_option('facebook-no_init') ? "checked='checked'" : ''); ?> value="1" />
						<span><?php echo $tips->add_tip(__('Check this box if you\'re already using Facebook scripts on your WordPress site. (If you\'re not sure what this means, leave the box unchecked).', self::TEXT_DOMAIN)); ?></span>
					</div>
		
					<div class="eab-settings-settings_item">
					    <label for="incsub_event-twitter-app_id" id="incsub_event_label-twitter-app_id"><?php _e('Twitter Consumer Key', self::TEXT_DOMAIN); ?></label>
						<input type="text" id="incsub_event-twitter-app_id" name="event_default[twitter-app_id]" value="<?php print $this->_data->get_option('twitter-app_id'); ?>" />
						<span><?php echo $tips->add_tip(sprintf(__('Enter your Twitter App ID number here. If you don\'t have a Twitter App yet, you will need to create one <a href="%s">here</a>', self::TEXT_DOMAIN), 'https://dev.twitter.com/apps/new')); ?></span>
					</div>
					    
					<div class="eab-settings-settings_item">
					    <label for="incsub_event-twitter-app_secret" id="incsub_event_label-twitter-app_secret"><?php _e('Twitter Consumer Secret', self::TEXT_DOMAIN); ?></label>
						<input type="text" id="incsub_event-twitter-app_secret" name="event_default[twitter-app_secret]" value="<?php print $this->_data->get_option('twitter-app_secret'); ?>" />
						<span><?php echo $tips->add_tip(__('Enter your Twitter App secret here.', self::TEXT_DOMAIN)); ?></span>
					</div>
				</div>
		    </div>
		    <?php do_action('eab-settings-after_api_settings'); ?>
		    <!-- Addon settings -->
		    <div id="eab-settings-addons" class="eab-metabox postbox">
				<h3 class="eab-hndle"><?php _e('Events comes with a range of extras just activate them below :', self::TEXT_DOMAIN); ?></h3>
				<!--<div class="eab-inside">-->
		    		<?php Eab_AddonHandler::create_addon_settings(); ?>
		    		<br />
		    	<!--</div>-->
		    </div>
		    <?php do_action('eab-settings-after_plugin_settings'); ?>
		</div>
		
		<p class="submit">
		    <input type="submit" class="button-primary" name="submit_settings" value="<?php _e('Save Changes', self::TEXT_DOMAIN) ?>" />
		    <?php if (isset($_REQUEST['eab_step']) && $_REQUEST['eab_step'] == 1) { ?>
		    <a href="edit.php?post_type=incsub_event&page=eab_welcome&eab_step=-1" class="button"><?php _e('Go back to Getting started guide', self::TEXT_DOMAIN) ?></a>
		    <?php } ?>
		</p>
	    </form>
	</div>
	<?php
    }
    
    function widgets_init() {
		require_once dirname(__FILE__) . '/lib/widgets/Widget.class.php';
		require_once dirname(__FILE__) . '/lib/widgets/Attendees_Widget.class.php';
		require_once dirname(__FILE__) . '/lib/widgets/Popular_Widget.class.php';
		require_once dirname(__FILE__) . '/lib/widgets/Upcoming_Widget.class.php';
		require_once dirname(__FILE__) . '/lib/widgets/UpcomingCalendar_Widget.class.php';
		
		register_widget('Eab_Attendees_Widget');
		register_widget('Eab_Popular_Widget');
		register_widget('Eab_Upcoming_Widget');
		register_widget('Eab_CalendarUpcoming_Widget');
    }
    
    function tutorial() {
		//load the file
		require_once( dirname(__FILE__) . '/lib/pointers-tutorial/pointer-tutorials.php' );
		
		//create our tutorial, with default redirect prefs
		$tutorial = new Pointer_Tutorial('eab_tutorial', true, false);
		
		//add our textdomain that matches the current plugin
		$tutorial->set_textdomain = self::TEXT_DOMAIN;
		
		//add the capability a user must have to view the tutorial
		$tutorial->set_capability = 'manage_options';
		
		$tutorial->add_icon( plugins_url( 'events-and-bookings/img/large-greyscale.png' , __FILE__ ) );
		
		$tutorial->add_step(admin_url('edit.php?post_type=incsub_event&page=eab_settings'), 'incsub_event_page_eab_settings', '#incsub_event-slug', __('Event Slug', self::TEXT_DOMAIN), array(
		    'content'  => '<p>' . esc_js( __('Change the root slug for events', self::TEXT_DOMAIN) ) . '</p>',
		    'position' => array( 'edge' => 'left', 'align' => 'center' ),
		));
		
		$tutorial->add_step(admin_url('edit.php?post_type=incsub_event&page=eab_settings'), 'incsub_event_page_eab_settings', '#incsub_event-accept_payments', __('Accept Payments?', self::TEXT_DOMAIN), array(
		    'content'  => '<p>' . esc_js( __('Check this to accept payments for your events', self::TEXT_DOMAIN) ) . '</p>',
		    'position' => array( 'edge' => 'left', 'align' => 'center' ),
		));
		
		$tutorial->add_step(admin_url('edit.php?post_type=incsub_event&page=eab_settings'), 'incsub_event_page_eab_settings', '#incsub_event-display_attendees', __('Display RSVP\'s?', self::TEXT_DOMAIN), array(
		    'content'  => '<p>' . esc_js( __('Check this to display RSVP\'s in the event details', self::TEXT_DOMAIN) ) . '</p>',
		    'position' => array( 'edge' => 'left', 'align' => 'center' ),
		));
		
		$tutorial->add_step(admin_url('edit.php?post_type=incsub_event&page=eab_settings'), 'incsub_event_page_eab_settings', '#incsub_event-currency', __('Currency', self::TEXT_DOMAIN), array(
		    'content'  => '<p>' . esc_js(__('Which currency will you be accepting payment in? See ', self::TEXT_DOMAIN)) . '<a href="https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_nvp_currency_codes" target="_blank">Accepted PayPal Currency Codes</a></p>',
		    'position' => array( 'edge' => 'left', 'align' => 'center' ),
		));
		
		$tutorial->add_step(admin_url('edit.php?post_type=incsub_event&page=eab_settings'), 'incsub_event_page_eab_settings', '#incsub_event-paypal_email', __('PayPal E-Mail', self::TEXT_DOMAIN), array(
		    'content'  => '<p>' . esc_js(__('PayPal e-mail address payments should be made to', self::TEXT_DOMAIN)) . '</p>',
		    'position' => array( 'edge' => 'left', 'align' => 'center' ),
		));
		
		$tutorial->add_step(admin_url('post-new.php?post_type=incsub_event'), 'post-new.php', '#title', __('Event title', self::TEXT_DOMAIN), array(
		    'content'  => '<p>' . __("What's happening?", self::TEXT_DOMAIN) . '</p>',
		    'position' => array( 'edge' => 'top', 'align' => 'center' ), 'post_type' => 'incsub_event',
		));
		
		if (defined('AGM_PLUGIN_URL')) {
		    $tutorial->add_step(admin_url('post-new.php?post_type=incsub_event'), 'post-new.php', '#incsub_event_venue_label', __('Event location', self::TEXT_DOMAIN), array(
			'content'  => '<p>' . __("Where? Enter the address or insert a map by clicking the globe icon", self::TEXT_DOMAIN) . '</p>',
			'position' => array( 'edge' => 'right', 'align' => 'left' ), 'post_type' => 'incsub_event',
		    ));
		} else {
		    $tutorial->add_step(admin_url('post-new.php?post_type=incsub_event'), 'post-new.php', '#incsub_event_venue_label', __('Event location', self::TEXT_DOMAIN), array(
			'content'  => '<p>' . __("Where? Enter the address", self::TEXT_DOMAIN) . '</p>',
			'position' => array( 'edge' => 'right', 'align' => 'left' ), 'post_type' => 'incsub_event',
		    ));
		}
		
		$tutorial->add_step(admin_url('post-new.php?post_type=incsub_event'), 'post-new.php', '#incsub_event_times_label', __('Event time and dates', self::TEXT_DOMAIN), array(
		    'content'  => '<p>' . __("When? YYYY-mm-dd HH:mm", self::TEXT_DOMAIN) . '</p>',
		    'position' => array( 'edge' => 'right', 'align' => 'left' ), 'post_type' => 'incsub_event',
		));
		
		$tutorial->add_step(admin_url('post-new.php?post_type=incsub_event'), 'post-new.php', '#incsub_event_status_label', __('Event status', self::TEXT_DOMAIN), array(
		    'content'  => '<p>' . __("Is this event still open to RSVP?", self::TEXT_DOMAIN) . '</p>',
		    'position' => array( 'edge' => 'right', 'align' => 'left' ), 'post_type' => 'incsub_event',
		));
		
		$tutorial->add_step(admin_url('post-new.php?post_type=incsub_event'), 'post-new.php', '#incsub_event_paid_label', __('Event type', self::TEXT_DOMAIN), array(
		    'content'  => '<p>' . __("Is this a paid event? Select 'Yes' and enter how much do you plan to charge in the text box that will appear", self::TEXT_DOMAIN) . '</p>',
		    'position' => array( 'edge' => 'right', 'align' => 'left' ), 'post_type' => 'incsub_event',
		));
		
		$tutorial->add_step(admin_url('post-new.php?post_type=incsub_event'), 'post-new.php', '#wp-content-editor-container', __('Event Details', self::TEXT_DOMAIN), array(
		    'content'  => '<p>' . __("More about the event", self::TEXT_DOMAIN) . '</p>',
		    'position' => array( 'edge' => 'bottom', 'align' => 'center' ), 'post_type' => 'incsub_event',
		));
		
		$tutorial->add_step(admin_url('post-new.php?post_type=incsub_event'), 'post-new.php', '#incsub-event-bookings', __("Event RSVPs", self::TEXT_DOMAIN), array(
		    'content'  => '<p>' . __("See who is attending, who may be attend and who is not after you publish the event", self::TEXT_DOMAIN) . '</p>',
		    'position' => array( 'edge' => 'bottom', 'align' => 'center' ), 'post_type' => 'incsub_event',
		));
		
		$tutorial->add_step(admin_url('post-new.php?post_type=incsub_event'), 'post-new.php', '#publish', __('Publish', self::TEXT_DOMAIN), array(
		    'content'  => '<p>' . __("Now it's time to publish the event", self::TEXT_DOMAIN) . '</p>',
		    'position' => array( 'edge' => 'right', 'align' => 'center' ), 'post_type' => 'incsub_event',
		));
		
		//start the tutorial
		$tutorial->initialize();
		
		// $tutorial->restart(6);
		return $tutorial;
    }
	
	/**
	 * Handles tutorial restart requests.
	 */
	function handle_tutorial_restart () {
		$tutorial = $this->tutorial();
		$step = (int)$_POST['step'];
		$tutorial->restart($step);
		die;
	}

	/**
	 * Handles Facebook user login and creation
	 */
	function handle_facebook_login () {
		header("Content-type: application/json");
		$resp = array(
			"status" => 0,
		);
		$fb_uid = @$_POST['user_id'];
		$token = @$_POST['token'];
		if (!$token) die(json_encode($resp));
		
		$request = new WP_Http;
		$result = $request->request(
			'https://graph.facebook.com/me?oauth_token=' . $token, 
			array('sslverify' => false) // SSL certificate issue workaround
		);
		if (200 != $result['response']['code']) die(json_encode($resp)); // Couldn't fetch info
		
		$data = json_decode($result['body']);
		if (!$data->email) die(json_encode($resp)); // No email, can't go further
		
		$email = is_email($data->email);
		if (!$email) die(json_encode($resp)); // Wrong email
		
		$wordp_user = get_user_by('email', $email);
		
		if (!$wordp_user) { // Not an existing user, let's create a new one
			$password = wp_generate_password(12, false);
			$username = @$data->name
				? preg_replace('/[^_0-9a-z]/i', '_', strtolower($data->name))
				: preg_replace('/[^_0-9a-z]/i', '_', strtolower($data->first_name)) . '_' . preg_replace('/[^_0-9a-z]/i', '_', strtolower($data->last_name))
			;
	
			$wordp_user = wp_create_user($username, $password, $email);
			if (is_wp_error($wordp_user)) die(json_encode($resp)); // Failure creating user
			else {
				update_user_meta($wordp_user, 'first_name', @$data->first_name);
				update_user_meta($wordp_user, 'last_name', @$data->last_name);
			}
		} else {
			$wordp_user = $wordp_user->ID;
		}
		
		update_user_meta($wordp_user, '_eab_fb', array(
			'id' => $fb_uid,
			'token' => $token,
		));
		do_action('eab-user_logged_in-facebook', $wordp_user, $fb_uid, $token);
		
		$user = get_userdata($wordp_user);

		wp_set_current_user($user->ID, $user->user_login);
		wp_set_auth_cookie($user->ID); // Logged in with Facebook, yay
		do_action('wp_login', $user->user_login);
		
		die(json_encode(array(
			"status" => 1,
		)));
	}

	/**
	 * Spawn a TwitterOAuth object.
	 */
	private function _get_twitter_object ($token=false, $secret=false) {
		if (!class_exists('TwitterOAuth')) include_once 'lib/twitteroauth/twitteroauth.php';
		$twitter = new TwitterOAuth(
			$this->_data->get_option('twitter-app_id'), 
			$this->_data->get_option('twitter-app_secret'),
			$token, $secret
		);
		return $twitter;
	}
	
	/**
	 * Get OAuth request URL and token.
	 */
	function handle_get_twitter_auth_url () {
		header("Content-type: application/json");
		$twitter = $this->_get_twitter_object();
		$request_token = $twitter->getRequestToken(@$_POST['url']);
		echo json_encode(array(
			'url' => $twitter->getAuthorizeURL($request_token['oauth_token']),
			'secret' => $request_token['oauth_token_secret'],
		));
		die;
	}
	
	/**
	 * Login or create a new user using whatever data we get from Twitter.
	 */
	function handle_twitter_login () {
		header("Content-type: application/json");
		$resp = array(
			"status" => 0,
		);
		$secret = @$_POST['secret'];
		$data_str = @$_POST['data'];
		$data_str = ('?' == substr($data_str, 0, 1)) ? substr($data_str, 1) : $data_str;
		$data = array();
		parse_str($data_str, $data);
		if (!$data) die(json_encode($resp));
		
		$twitter = $this->_get_twitter_object($data['oauth_token'], $secret);
		$access = $twitter->getAccessToken($data['oauth_verifier']);
		
		$twitter = $this->_get_twitter_object($access['oauth_token'], $access['oauth_token_secret']);
		$tw_user = $twitter->get('account/verify_credentials');

		// Have user, now register him/her
		$domain = preg_replace('/www\./', '', parse_url(site_url(), PHP_URL_HOST));
		$username = preg_replace('/[^_0-9a-z]/i', '_', strtolower($tw_user->name));
		$email = $username . '@twitter.' . $domain; //STUB email
		$wordp_user = get_user_by('email', $email);
		
		if (!$wordp_user) { // Not an existing user, let's create a new one
			$password = wp_generate_password(12, false);
			$count = 0;
			while (username_exists($username)) {
				$username .= rand(0,9);
				if (++$count > 10) break;
			}
	
			$wordp_user = wp_create_user($username, $password, $email);
			if (is_wp_error($wordp_user)) die(json_encode($resp)); // Failure creating user
			else {
				list($first_name, $last_name) = explode(' ', @$tw_user->name, 2);
				update_user_meta($wordp_user, 'first_name', $first_name);
				update_user_meta($wordp_user, 'last_name', $last_name);
			}
		} else {
			$wordp_user = $wordp_user->ID;
		}
		
		update_user_meta($wordp_user, '_eab_tw', array(
			'id' => $tw_user->id,
			'avatar' => $tw_user->profile_image_url,
			'token' => $access,
		));
		do_action('eab-user_logged_in-twitter', $wordp_user, $tw_user->id, $tw_user->profile_image_url, $access);
		
		$user = get_userdata($wordp_user);
		wp_set_current_user($user->ID, $user->user_login);
		wp_set_auth_cookie($user->ID); // Logged in with Twitter, yay
		do_action('wp_login', $user->user_login);
		
		die(json_encode(array(
			"status" => 1,
		)));
	}
	
	/**
	 * Save a message to the log file
	 */	
	function log( $message='' ) {
		// Don't give warning if folder is not writable
		@file_put_contents( WP_PLUGIN_DIR . "/events-and-bookings/log.txt", $message . chr(10). chr(13), FILE_APPEND ); 
	}
	
	
	/**
	 * Get OAuth request URL and token.
	 */
	function handle_get_google_auth_url () {
		header("Content-type: application/json");
		
		$this->openid->returnUrl = $_POST['url'];
		
		echo json_encode(array(
			'url' => $this->openid->authUrl()
		));
		exit();
	}
	
	/**
	 * Login or create a new user using whatever data we get from Google.
	 */
	function handle_google_login () {
		header("Content-type: application/json");
		$resp = array(
			"status" => 0,
		);
		
		$cache = $this->openid->getAttributes();
		
		if (isset($cache['namePerson/first']) || isset($cache['namePerson/last']) || isset($cache['namePerson/friendly']) || isset($cache['contact/email'])) {
			$this->_google_user_cache = $cache;
		}

		// Have user, now register him/her
		if ( !$username = $this->_google_user_cache['namePerson/friendly'] )
			$username = $this->_google_user_cache['namePerson/first'];
		$email = $this->_google_user_cache['contact/email'];
		$wordp_user = get_user_by('email', $email);
		
		if (!$wordp_user) { // Not an existing user, let's create a new one
			$password = wp_generate_password(12, false);
			$count = 0;
			while (username_exists($username)) {
				$username .= rand(0,9);
				if (++$count > 10) break;
			}
	
			$wordp_user = wp_create_user($username, $password, $email);
			if (is_wp_error($wordp_user)) 
				die(json_encode($resp)); // Failure creating user
			else {
				update_user_meta($wordp_user, 'first_name', $this->_google_user_cache['namePerson/first']);
				update_user_meta($wordp_user, 'last_name', $this->_google_user_cache['namePerson/last']);
			}
		} 
		else {
			$wordp_user = $wordp_user->ID;
		}
		
		
		$user = get_userdata($wordp_user);
		wp_set_current_user($user->ID, $user->user_login);
		wp_set_auth_cookie($user->ID); // Logged in with Google, yay
		do_action('wp_login', $user->user_login);
		
		die(json_encode(array(
			"status" => 1,
		)));
	}

	function handle_wordpress_login () {
		header("Content-type: application/json");
		$resp = array(
			"status" => 0,
		);
		$data = stripslashes_deep(@$_POST['data']);
		$login = @$data['username'];
		$pass = @$data['password'];
		if (!user_pass_ok($login, $pass)) die(json_encode($resp));
		
		$user = get_user_by('login', $login);
		if (is_wp_error($user)) die(json_encode($resp));
		
		wp_set_current_user($user->ID, $user->user_login);
		wp_set_auth_cookie($user->ID); // Logged in with WordPress, yay
		do_action('wp_login', $user->user_login);
		
		die(json_encode(array(
			"status" => 1,
		)));
	}

	function handle_wordpress_register () {
		header("Content-type: application/json");
		$resp = array(
			"status" => 0,
		);
		$data = stripslashes_deep(@$_POST['data']);
		$login = @$data['username'];
		$email = @$data['email'];
		
		// Check the username
		if ( empty($login) ) {
			//$errors[] = __('Please enter a username.');
			die(json_encode($resp));
		}
		if ( !validate_username( $login ) ) {
			//$errors[] = __('This username is invalid.  Please enter a valid username.');
			die(json_encode($resp));
		}
		if ( username_exists( $login ) ) {
			//$errors[] = __('This username is already registered, please choose another.');
			die(json_encode($resp));
		}

		// Check the e-mail address
		if (empty($email)) {
			//$errors[] = __('Please type your e-mail address.');
			die(json_encode($resp));
		} else if ( !is_email( $email ) ) {
			//$errors[] = __('The email address appears invalid.');
			//$email = '';
			die(json_encode($resp));
		}
		if ( email_exists( $email ) ) {
			//$errors[] = __('This email is already registered, please choose another.');
			die(json_encode($resp));
		}

		$password = wp_generate_password(12, false);
		
		$wordp_user = wp_create_user($login, $password, $email);
		if (is_wp_error($wordp_user)) die(json_encode($resp));
		
		$user = get_userdata($wordp_user);
		
		//notify
		wp_new_user_notification($user->ID, $password);
		
		wp_set_current_user($user->ID, $user->user_login);
		wp_set_auth_cookie($user->ID); // Logged in with WordPress, yay
		do_action('wp_login', $user->user_login);
		
		die(json_encode(array(
			"status" => 1,
		)));
	}

	/**
	 * Responds with RSVP form
	 */
	function handle_get_form () {
		$post_id = (int)@$_POST['post_id'];
		if (!$post_id) die;
		
		$post = get_post($post_id);
		echo Eab_Template::get_rsvp_form($post);
		die;
	}
	
	function handle_attendance_cancel () {
		$user_id = (int)$_POST['user_id'];
		$post_id = (int)$_POST['post_id'];
		
		$post = get_post($post_id);
		$event = new Eab_EventModel($post);
		$event->cancel_attendance($user_id);
		echo $this->meta_box_part_bookings($post);
		die;
	}
	
	function handle_attendance_delete () {
		$user_id = (int)$_POST['user_id'];
		$post_id = (int)$_POST['post_id'];
		
		$post = get_post($post_id);
		$event = new Eab_EventModel($post);
		$event->delete_attendance($user_id);
		echo $this->meta_box_part_bookings($post);
		die;
	}
	
	/**
	 * Proper query rewriting.
	 * HAVE to calculate in the year as well.
	 */
	function load_events_from_query () {
		if (is_admin()) return false;
		global $wp_query;
		if (
			'incsub_event' == $wp_query->query_vars['post_type']
			/*
			&&
			(
				(isset($wp_query->query_vars['event_monthnum']) && $wp_query->query_vars['event_monthnum']) 
				|| 
				(isset($wp_query->query_vars['event_year']) && $wp_query->query_vars['event_year'])
			)
			*/
		) {
			$original_year = (int)@$wp_query->query_vars['event_year'];
			$year = $original_year ? $original_year : date('Y');
			$original_month = (int)@$wp_query->query_vars['event_monthnum'];
			$month = $original_month ? $original_month : date('m');

			do_action('eab-query_rewrite-before_query_replacement', $original_year, $original_month);
			$wp_query = Eab_CollectionFactory::get_upcoming(strtotime("{$year}-{$month}-01 00:00"), $wp_query->query);
			do_action('eab-query_rewrite-after_query_replacement');
		}
	}
	
	function get_social_api_avatar ($avatar, $id_or_email, $size = '96') {
		$wp_uid = false;
		if (is_object($id_or_email)) {
			if (isset($id_or_email->comment_author_email)) $id_or_email = $id_or_email->comment_author_email;
			else return $avatar;
		}

		if (is_numeric($id_or_email)) {
			$wp_uid = (int)$id_or_email;
		} else if (is_email($id_or_email)) {
			$user = get_user_by('email', $id_or_email);
			if ($user) $wp_uid = $user->ID;
		} else return $avatar;
		if (!$wp_uid) return $avatar;
		
		$fb = get_user_meta($wp_uid, '_eab_fb', true);
		if ($fb && isset($fb['id'])) {
			return "<img class='avatar avatar-{$size} photo eab-avatar eab-avatar-facebook' width='{$size}' height='{$size}' src='https://graph.facebook.com/" . $fb['id'] . "/picture' />";
		}
		$tw = get_user_meta($wp_uid, '_eab_tw', true);
		if ($tw && isset($tw['avatar'])) {
			return "<img class='avatar avatar-{$size} photo eab-avatar eab-avatar-twitter' width='{$size}' height='{$size}' src='" . $tw['avatar'] . "' />";
		}
		
		return $avatar;
	}
}

function eab_autoshow_map_off ($opts) {
	@$opts['custom_fields_options']['autoshow_map'] = false;
	return $opts;
}
    
include_once 'template-tags.php';

define('EAB_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . basename( dirname( __FILE__ ) ) . '/');

require_once EAB_PLUGIN_DIR . 'lib/class_eab_options.php';
require_once EAB_PLUGIN_DIR . 'lib/class_eab_collection.php';
require_once EAB_PLUGIN_DIR . 'lib/class_eab_event_model.php';
require_once EAB_PLUGIN_DIR . 'lib/class_eab_template.php';

// Lets get things started
$__booking = new Eab_EventsHub(); // @TODO: Refactor

require_once EAB_PLUGIN_DIR . 'lib/class_eab_network.php';
Eab_Network::serve();
require_once EAB_PLUGIN_DIR . 'lib/class_eab_shortcodes.php';
Eab_Shortcodes::serve();
require_once EAB_PLUGIN_DIR . 'lib/class_eab_scheduler.php';
Eab_Scheduler::serve();
require_once EAB_PLUGIN_DIR . 'lib/class_eab_addon_handler.php';
Eab_AddonHandler::serve();

if (is_admin()) {
	require_once dirname(__FILE__) . '/lib/contextual_help/class_eab_admin_help.php';
	Eab_AdminHelp::serve();
}

if ( !function_exists( 'wdp_un_check' ) ) {
	add_action( 'admin_notices', 'wdp_un_check', 5 );
	add_action( 'network_admin_notices', 'wdp_un_check', 5 );

	function wdp_un_check() {
		if ( !class_exists( 'WPMUDEV_Update_Notifications' ) && current_user_can( 'edit_users' ) )
			echo '<div class="error fade"><p>' . __('Please install the latest version of <a href="http://premium.wpmudev.org/project/update-notifications/" title="Download Now &raquo;">our free Update Notifications plugin</a> which helps you stay up-to-date with the most stable, secure versions of WPMU DEV themes and plugins. <a href="http://premium.wpmudev.org/wpmu-dev/update-notifications-plugin-information/">More information &raquo;</a>', 'wpmudev') . '</a></p></div>';
	}
}
