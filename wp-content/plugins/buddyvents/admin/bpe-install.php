<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

// No direct access is allowed
if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Setup the default options
 * 
 * @package Admin
 * @since 	1.0
 */
function bpe_activate()
{
	global $wpdb;
	
    // can be overridden in wp-config.php
    if( ! defined( 'EVENT_ADMIN_ROLE' ) ) define( 'EVENT_ADMIN_ROLE', 'administrator' );
        
    // set user roles for backend access
    $role = &get_role( EVENT_ADMIN_ROLE );
        
    if( ! $role->has_cap( 'bpe_manage_events' ) )
        $role->add_cap( 'bpe_manage_events' );

    if( ! $role->has_cap( 'bpe_manage_event_approvals' ) )
        $role->add_cap( 'bpe_manage_event_approvals' );

    if( ! $role->has_cap( 'bpe_manage_event_settings' ) )
        $role->add_cap( 'bpe_manage_event_settings' );

    if( ! $role->has_cap( 'bpe_manage_event_apikeys' ) )
        $role->add_cap( 'bpe_manage_event_apikeys' );

    if( ! $role->has_cap( 'bpe_manage_event_webhooks' ) )
        $role->add_cap( 'bpe_manage_event_webhooks' );

    if( ! $role->has_cap( 'bpe_manage_event_sales' ) )
        $role->add_cap( 'bpe_manage_event_sales' );

    if( ! $role->has_cap( 'bpe_manage_event_invoices' ) )
        $role->add_cap( 'bpe_manage_event_invoices' );

    if( ! $role->has_cap( 'bpe_manage_event_categories' ) )
        $role->add_cap( 'bpe_manage_event_categories' );
	
	// now do the options and tables
	if( ! empty( $wpdb->charset ) )
		$charset_collate =  "DEFAULT CHARACTER SET $wpdb->charset";

    $sql = array();
        
    if( ! $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->base_prefix}bpe_events'" ) ) :
    	$sql[] = "CREATE TABLE {$wpdb->base_prefix}bpe_events (
    				id int(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    				user_id int(20) unsigned NOT NULL,
    				group_id int(20) unsigned NOT NULL,
    				name varchar(255) NOT NULL,
    				slug varchar(255) NOT NULL,
    				description longtext NOT NULL,
    				category int(20) NOT NULL,
    				url varchar(255) NOT NULL,
    				location varchar(255) NOT NULL,
    				venue_name varchar(255) NOT NULL,
    				longitude decimal(17, 14) NOT NULL,
    				latitude decimal(17, 14) NOT NULL,
    				start_date date NOT NULL,
    				start_time time NOT NULL,
    				end_date date NOT NULL,
    				end_time time NOT NULL,
    				date_created datetime NOT NULL,
    				public tinyint(1) NOT NULL DEFAULT '1',
    				limit_members int(20) unsigned NOT NULL,
    				recurrent varchar(20) NOT NULL,
    				is_spam tinyint(1) NOT NULL DEFAULT '0',
    				approved tinyint(1) NOT NULL DEFAULT '1',
    				rsvp tinyint(1) NOT NULL DEFAULT '1',
    				all_day tinyint(1) NOT NULL DEFAULT '0',
    				timezone varchar(255) NOT NULL,
    				group_approved tinyint(1) NOT NULL DEFAULT '1',
    				KEY user_id (user_id),
    				KEY group_id (group_id),
    				KEY category (category),
    				KEY public (public),
    				KEY is_spam (is_spam),
    				KEY limit_members (limit_members)
    			   ) {$charset_collate};";
    endif;

    if( ! $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->base_prefix}bpe_members'" ) ) :
    	$sql[] = "CREATE TABLE {$wpdb->base_prefix}bpe_members (
    				id int(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    				event_id int(20) unsigned NOT NULL,
    				user_id int(20) unsigned NOT NULL,
    				rsvp tinyint(1) NOT NULL DEFAULT '0',
    				rsvp_date datetime NOT NULL,
    				role varchar(100) NOT NULL DEFAULT 'attendee',
    				KEY event_id (event_id),
    				KEY user_id (user_id)
    			   ) {$charset_collate};";
    endif;

    if( ! $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->base_prefix}bpe_schedules'" ) ) :
    	$sql[] = "CREATE TABLE {$wpdb->base_prefix}bpe_schedules (
    				id int(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    				event_id int(20) unsigned NOT NULL,
    				day date NOT NULL,
    				start time NOT NULL,
    				end time NOT NULL,
    				description longtext NOT NULL,
    				KEY event_id (event_id)
    			   ) {$charset_collate};";
    endif;

    if( ! $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->base_prefix}bpe_documents'" ) ) :
    	$sql[] = "CREATE TABLE {$wpdb->base_prefix}bpe_documents (
    				id int(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    				event_id int(20) unsigned NOT NULL,
    				name varchar(255) DEFAULT NULL,
    				description longtext NOT NULL,
    				url varchar(255) DEFAULT NULL,
    				type varchar(5) DEFAULT NULL,
    				KEY event_id (event_id)
    			   ) {$charset_collate};";
    endif;

    if( ! $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->base_prefix}bpe_event_meta'" ) ) :
    	$sql[] = "CREATE TABLE {$wpdb->base_prefix}bpe_event_meta (
    				id int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    				event_id int(20) NOT NULL,
    				meta_key varchar(255) DEFAULT NULL,
    				meta_value longtext DEFAULT NULL,
    				KEY event_id (event_id),
    				KEY meta_key (meta_key)
    				) {$charset_collate};";
    endif;

    if( ! $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->base_prefix}bpe_event_categories'" ) ) :
    	$sql[] = "CREATE TABLE {$wpdb->base_prefix}bpe_event_categories (
    				id int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    				name varchar(255) DEFAULT NULL,
    				slug varchar(255) DEFAULT NULL
    				) {$charset_collate};";
    endif;

    if( ! $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->base_prefix}bpe_tickets'" ) ) :
    	$sql[] = "CREATE TABLE {$wpdb->base_prefix}bpe_tickets (
    				id int(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    				event_id int(20) unsigned NOT NULL,
    				name varchar(255) NOT NULL,
    				description longtext NOT NULL,
    				price decimal(20,2) NOT NULL,
    				currency varchar(3) NOT NULL,
    				quantity int(20) unsigned NOT NULL,
    				start_sales date NOT NULL,
    				end_sales date NOT NULL,
    				min_tickets int(20) unsigned NOT NULL,
    				max_tickets int(20) unsigned NOT NULL,
    				KEY event_id (event_id),
    				KEY min_tickets (min_tickets),
    				KEY max_tickets (max_tickets),
    				KEY quantity (quantity)
    			   ) {$charset_collate};";
    endif;

    if( ! $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->base_prefix}bpe_sales'" ) ) :
    	$sql[] = "CREATE TABLE {$wpdb->base_prefix}bpe_sales (
    				id int(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    				ticket_id int(20) unsigned NOT NULL,
    				seller_id int(20) unsigned NOT NULL,
    				buyer_id int(20) unsigned NOT NULL,
    				single_price decimal(20,2) NOT NULL,
    				currency varchar(3) NOT NULL,
    				quantity int(20) unsigned NOT NULL,
    				attendees longtext NOT NULL,
    				gateway varchar(100) NOT NULL,
    				sales_id varchar(255) NOT NULL,
    				status varchar(255) NOT NULL,
    				sale_date datetime NOT NULL,
    				commission decimal(20,2) NOT NULL,
    				requested tinyint(1) NOT NULL DEFAULT '1',
    				KEY ticket_id (ticket_id),
    				KEY seller_id (seller_id),
    				KEY buyer_id (buyer_id),
    				KEY quantity (quantity),
    				KEY requested (requested),
    				KEY sales_id (sales_id)
    			   ) {$charset_collate};";
    endif;

    if( ! $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->base_prefix}bpe_invoices'" ) ) :
    	$sql[] = "CREATE TABLE {$wpdb->base_prefix}bpe_invoices (
    				id int(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    				user_id int(20) unsigned NOT NULL,
    				sales longtext NOT NULL,
    				month varchar(255) NOT NULL,
    				sent_date datetime NOT NULL,
    				settled tinyint(1) NOT NULL DEFAULT '1',
    				transaction_id varchar(255) NOT NULL,
    				KEY user_id (user_id),
    				KEY settled (settled)
    			   ) {$charset_collate};";
    endif;

    if( ! $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->base_prefix}mapo_coords'" ) ) :
    	$sql[] = "CREATE TABLE {$wpdb->base_prefix}mapo_coords (
    				id int(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    				user_id int(20) unsigned NOT NULL,
    				group_id int(20) unsigned NOT NULL,
    				lat decimal(17, 14) NOT NULL,
    				lng decimal(17, 14) NOT NULL,
    				KEY group_id (group_id),
    				KEY user_id (user_id)
    			   ) {$charset_collate};";
    endif;

    if( ! $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->base_prefix}bpe_notifications'" ) ) :
    	$sql[] = "CREATE TABLE {$wpdb->base_prefix}bpe_notifications (
    				id int(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    				user_id int(20) unsigned NOT NULL,
    				keywords longtext DEFAULT NULL,
    				email tinyint(1) NOT NULL DEFAULT '0',
    				screen tinyint(1) NOT NULL DEFAULT '0',
    				remind tinyint(1) NOT NULL DEFAULT '0',
    				KEY user_id (user_id)
    			   ) {$charset_collate};";
    endif;

    if( ! $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->base_prefix}bpe_api'" ) ) :
    	$sql[] = "CREATE TABLE {$wpdb->base_prefix}bpe_api (
    				id int(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    				user_id int(20) unsigned NOT NULL,
    				api_key varchar(255) DEFAULT NULL,
    				active tinyint(1) NOT NULL DEFAULT '1',
    				date_generated datetime NOT NULL,
    				hits int(20) unsigned NOT NULL,
    				hit_date datetime NOT NULL,
    				hits_over int(20) unsigned NOT NULL DEFAULT '0',
    				KEY user_id (user_id)
    			   ) {$charset_collate};";
    endif;

    if( ! $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->base_prefix}bpe_webhooks'" ) ) :
    	$sql[] = "CREATE TABLE {$wpdb->base_prefix}bpe_webhooks (
    				id int(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    				user_id int(20) unsigned NOT NULL,
    				event varchar(255) DEFAULT NULL,
    				url longtext NOT NULL,
    				verifier varchar(255) DEFAULT NULL,
    				verified tinyint(1) NOT NULL DEFAULT '0',
    				KEY user_id (user_id)
    			   ) {$charset_collate};";
    endif;
    			   
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	dbDelta( $sql );

	// Maybe insert the first category
	$categories = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM {$wpdb->base_prefix}bpe_event_categories" ) );
	
	if( ! $categories )
		$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->base_prefix}bpe_event_categories ( name, slug ) VALUES ( 'Uncategorized', 'uncategorized' )" ) );
	
	if( ! get_blog_option( Buddyvents::$root_blog, 'bpe_options' ) )
	   bpe_default_options();
			
	// let's save the db version number
	update_blog_option( Buddyvents::$root_blog, 'bpe_dbversion', Buddyvents::DBVERSION );
	
	// set the current version
	$options = array();
	$options['headers'] = array(
		'User-Agent' => 'Buddyvents v'. Buddyvents::VERSION,
		'Referer' 	 => get_bloginfo( 'url' )
	);
	
	// gets the current version via HTTP request as user
	// might install an older version initially
	$response = wp_remote_request( Buddyvents::HOME_URL .'versions.php', $options );
	$version  = maybe_unserialize( wp_remote_retrieve_body( $response ) );
	
    if( isset( $version['buddyvents'] ) )
    	update_blog_option( Buddyvents::$root_blog, 'buddyvents_current_version', $version['buddyvents'] );
}

/**
 * Reset the options
 * 
 * @package Admin
 * @since 	1.6
 */
function bpe_default_options()
{
	global $bpe;
	
	$bpe->options 							= new stdClass;
	$bpe->options->week_start 				= 7;
	$bpe->options->clock_type 				= 24;
	$bpe->options->enable_address 			= false;
	$bpe->options->approve_events 			= false;
	$bpe->options->map_location['lat'] 		= 5;
	$bpe->options->map_location['lng'] 		= 30;
	$bpe->options->map_zoom_level 			= 2;
	$bpe->options->map_type 				= 'HYBRID';
	$bpe->options->restrict_creation 		= false;
	$bpe->options->slug 					= 'events';
	$bpe->options->active_slug 				= 'active';
	$bpe->options->archive_slug 			= 'archive';
	$bpe->options->month_slug 				= 'month';
	$bpe->options->day_slug 				= 'day';
	$bpe->options->calendar_slug 			= 'calendar';
	$bpe->options->gallery_slug				= 'gallery';
	$bpe->options->map_slug 				= 'map';
	$bpe->options->attending_slug 			= 'attending';
	$bpe->options->create_slug 				= 'create';
	$bpe->options->invite_slug 				= 'invite';
	$bpe->options->schedule_slug 			= 'schedule';
	$bpe->options->general_slug 			= 'general';
	$bpe->options->step_slug 				= 'step';
	$bpe->options->edit_slug 				= 'edit';
	$bpe->options->category_slug 			= 'category';
	$bpe->options->attendee_slug 			= 'attendees';
	$bpe->options->directions_slug 			= 'directions';
	$bpe->options->documents_slug 			= 'documents';
	$bpe->options->search_slug 				= 'search';
	$bpe->options->results_slug 			= 'results';
	$bpe->options->manage_slug 				= 'manage';
	$bpe->options->list_slug 				= 'list';
	$bpe->options->grid_slug 				= 'grid';
	$bpe->options->view_slug 				= 'view';
	$bpe->options->feed_slug 				= 'feed';
	$bpe->options->venue_slug 				= 'venue';
	$bpe->options->api_slug 				= 'api';
	$bpe->options->api_key_slug 			= 'apikey';
	$bpe->options->timezone_slug 			= 'timezone';
	$bpe->options->default_avatar 			= '';
	$bpe->options->default_view 			= $bpe->options->view_slug;
	$bpe->options->system 					= 'km';
	$bpe->options->field_id 				= '';
	$bpe->options->timestamp 				= '-1 day';
	$bpe->options->enable_achievements 		= false;
	$bpe->options->enable_invites 			= true;
	$bpe->options->enable_directions 		= 1;
	$bpe->options->enable_bp_gallery		= false;
	$bpe->options->enable_api 				= false;
	$bpe->options->enable_ical				= 1;
	$bpe->options->enable_webhooks 			= false;
	$bpe->options->default_tab 				= 'active';
	$bpe->options->date_format 				= get_blog_option( Buddyvents::$root_blog, 'date_format' );
	$bpe->options->deactivated_tabs 		= array();
	$bpe->options->default_tab_attending 	= false;
	$bpe->options->map_lang 				= '';
	$bpe->options->localize_months 			= false;
	$bpe->options->tab_order 				= array(
												'active' 	=> 1,
												'archive' 	=> 2,
												'attending' => 3,
												'calendar' 	=> 4,
												'map' 		=> 5,
												'search' 	=> 6,
												'create' 	=> 7,
												'invoices'	=> 8
											);
	$bpe->options->backend_order 			= $bpe->options->tab_order;
	$bpe->options->restrict_api_hits 		= 10;
	$bpe->options->restrict_api_timespan 	= 3600;
	$bpe->options->enable_twitter 			= false;
	$bpe->options->twitter_consumer_key 	= '';
	$bpe->options->twitter_consumer_secret 	= '';
	$bpe->options->bitly_login 				= '';
	$bpe->options->bitly_key 				= '';
	$bpe->options->enable_facebook 			= false;
	$bpe->options->enable_facebook_pages	= false;
	$bpe->options->facebook_appid 			= '';
	$bpe->options->facebook_secret 			= '';
	$bpe->options->use_event_images 		= false;
	$bpe->options->enable_eventbrite 		= false;
	$bpe->options->eventbrite_appkey 		= '';
	$bpe->options->enable_cubepoints 		= false;
	$bpe->options->cp_create_event 			= 100;
	$bpe->options->cp_delete_event 			= -100;
	$bpe->options->cp_attend_event 			= 40;
	$bpe->options->cp_remove_event 			= -40;
	$bpe->options->cp_maybe_attend_event 	= 20;
	$bpe->options->cp_maybe_remove_event 	= -20;
	$bpe->options->enable_schedules 		= 1;
	$bpe->options->enable_documents 		= 1;
	$bpe->options->enable_attendees 		= true;
	$bpe->options->enable_logo 				= true;
	$bpe->options->logo_slug 				= 'logo';
	$bpe->options->geonames_username 		= '';
	$bpe->options->enable_tickets 			= false;
	$bpe->options->tickets_slug 			= 'tickets';
	$bpe->options->enable_sandbox 			= false;
	$bpe->options->commission_percent 		= 0;
	$bpe->options->allowed_currencies 		= array( 'EUR' );
	$bpe->options->enable_invoices 			= false;
	$bpe->options->invoice_footer1 			= '';
	$bpe->options->invoice_footer2 			= '';
	$bpe->options->invoice_footer3 			= '';
	$bpe->options->invoice_footer4 			= '';
	$bpe->options->invoice_tax 				= '';
	$bpe->options->invoice_logo 			= array();
	$bpe->options->invoice_message 			= '';
	$bpe->options->invoice_settle_date 		= 5;
	$bpe->options->invoice_slug 			= 'invoices';
	$bpe->options->paypal_email 			= '';
	$bpe->options->use_fullcalendar 		= true;
	$bpe->options->registration_key			= '';
	$bpe->options->enable_manual_attendees 	= false;
	$bpe->options->page_id 					= false;
	$bpe->options->approve_slug 			= 'approve';
	$bpe->options->sales_slug 				= 'sales';
	$bpe->options->checkout_slug 			= 'checkout';
	$bpe->options->cancel_slug 				= 'cancel';
	$bpe->options->success_slug 			= 'success';
	$bpe->options->signup_slug 				= 'signup';
	$bpe->options->group_contact_required 	= true;
	$bpe->options->enable_groups		 	= true;
	$bpe->options->enable_forums 			= false;
	$bpe->options->forum_slug 				= 'forum';
	$bpe->options->topic_slug 				= 'topic';
	$bpe->options->reply_slug 				= 'reply';
	$bpe->options->main_event_forum 		= '';
	$bpe->options->forum_tag_slug 			= 'tag';
	$bpe->options->disable_warnings			= false;
	$bpe->options->enable_newsletter		= false;
	$bpe->options->enable_mailchimp			= false;
	$bpe->options->enable_cmonitor			= false;
	$bpe->options->newsletter_slug			= 'newsletter';
	$bpe->options->enable_aweber			= false;
			
	// save to the database
	update_blog_option( Buddyvents::$root_blog, 'bpe_options', $bpe->options );
}

/**
 * Delete all options and database tables
 * 
 * @package Admin
 * @since 	1.0
 */
function bpe_uninstall()
{
	global $wpdb;
	
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->base_prefix}bpe_events" 		  );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->base_prefix}bpe_members" 		  );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->base_prefix}bpe_event_meta" 	  );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->base_prefix}bpe_schedules" 		  );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->base_prefix}bpe_tickets" 		  );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->base_prefix}bpe_sales" 			  );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->base_prefix}bpe_invoices" 		  );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->base_prefix}bpe_documents" 		  );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->base_prefix}bpe_event_categories" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->base_prefix}bpe_notifications" 	  );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->base_prefix}bpe_api" 			  );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->base_prefix}bpe_webhooks" 		  );
	
	delete_blog_option( Buddyvents::$root_blog, 'bpe_options'   );
	delete_blog_option( Buddyvents::$root_blog, 'bpe_dbversion' );
}
?>