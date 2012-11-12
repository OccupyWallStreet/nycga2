<?php

function em_install() {
	global $wp_rewrite;
   	$wp_rewrite->flush_rules();
	$old_version = get_option('dbem_version');
	//Won't upgrade 2 anymore, let 3 do that and we worry about 3.
   	if( $old_version != '' && $old_version < 3.096 ){
		function em_update_required_notification(){ 
			global $EM_Booking; 
			?><div class="error"><p><strong>Events Manager upgrade not complete, please upgrade to the version 3.0.96 or higher first from <a href="http://wordpress.org/extend/plugins/events-manager/download/">here</a> first before upgrading to this version. <a href="http://wp-events-plugin.com/news/upgrade-issues-screencast-walkthrough/">We made a screencast</a> to help you out.</strong></p></div><?php
		}
		add_action ( 'admin_notices', 'em_update_required_notification' );
		return; 
   	}
	if( EM_VERSION > $old_version || $old_version == '' ){
	 	// Creates the events table if necessary
		em_create_events_table(); 
		em_create_events_meta_table();
		em_create_locations_table();
	  	em_create_bookings_table();
		em_create_categories_table();
		em_create_tickets_table();
		em_create_tickets_bookings_table();
		em_set_capabilities();
		em_add_options();
		
		//New install, or Migrate?
		if( $old_version < 4 && !empty($old_version) ){
			em_migrate_v3();
		}elseif( empty($old_version) ){
			update_option('dbem_hello_to_user',1);
		}
		//Upate Version	
	  	update_option('dbem_version', EM_VERSION); 
	  	
		// wp-content must be chmodded 777. Maybe just wp-content.
		if(!file_exists(EM_IMAGE_UPLOAD_DIR)){
			mkdir(EM_IMAGE_UPLOAD_DIR, 0777); //do we need to 777 it? it'll be owner apache anyway, like normal uploads
			if(EM_IMAGE_DS == '/'){
				mkdir(EM_IMAGE_UPLOAD_DIR."events/", 0777); //do we need to 777 it? it'll be owner apache anyway, like normal uploads
				mkdir(EM_IMAGE_UPLOAD_DIR."locations/", 0777); //do we need to 777 it? it'll be owner apache anyway, like normal uploads
				mkdir(EM_IMAGE_UPLOAD_DIR."categories/", 0777); //do we need to 777 it? it'll be owner apache anyway, like normal uploads
			}
		}
		
		em_create_events_page(); 
	}
}

/**
 * Magic function that takes a table name and cleans all non-unique keys not present in the $clean_keys array. if no array is supplied, all but the primary key is removed.
 * @param string $table_name
 * @param array $clean_keys
 */
function em_sort_out_table_nu_keys($table_name, $clean_keys = array()){
	global $wpdb;
	//sort out the keys
	$new_keys = $clean_keys;
	$table_key_changes = array();
	$table_keys = $wpdb->get_results("SHOW KEYS FROM $table_name WHERE Key_name != 'PRIMARY'", ARRAY_A);
	foreach($table_keys as $table_key_row){
		if( !in_array($table_key_row['Key_name'], $clean_keys) ){
			$table_key_changes[] = "ALTER TABLE $table_name DROP INDEX ".$table_key_row['Key_name'];
		}elseif( in_array($table_key_row['Key_name'], $clean_keys) ){
			foreach($clean_keys as $key => $clean_key){
				if($table_key_row['Key_name'] == $clean_key){
					unset($new_keys[$key]);
				}
			}
		}
	}
	//delete duplicates
	foreach($table_key_changes as $sql){
		$wpdb->query($sql);
	}
	//add new keys
	foreach($new_keys as $key){
		$wpdb->query("ALTER TABLE $table_name ADD INDEX ($key)");
	}
}

function em_create_events_table() {
	global  $wpdb, $user_level, $user_ID;
	get_currentuserinfo();
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php'); 
	
	$table_name = EM_EVENTS_TABLE; 
	$sql = "CREATE TABLE ".$table_name." (
		event_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		event_slug VARCHAR( 200 ) NOT NULL,
		event_owner bigint(20) unsigned DEFAULT NULL,
		event_status int(1) NULL DEFAULT NULL, 
		event_name tinytext NOT NULL,
		event_start_time time NOT NULL,
		event_end_time time NOT NULL,
		event_start_date date NOT NULL,
		event_end_date date NULL, 
		event_notes text NULL DEFAULT NULL,
		event_rsvp bool NOT NULL DEFAULT 0,
		event_spaces int(5),
		location_id bigint(20) unsigned NOT NULL,
		recurrence_id bigint(20) unsigned NULL,
  		event_category_id bigint(20) unsigned NULL DEFAULT NULL,
  		event_attributes text NULL,
  		event_date_created datetime NULL,
  		event_date_modified datetime NULL,
		recurrence bool NOT NULL DEFAULT 0,
		recurrence_interval int(4) NULL DEFAULT NULL,
		recurrence_freq tinytext NULL DEFAULT NULL,
		recurrence_byday tinytext NULL DEFAULT NULL,
		recurrence_byweekno int(4) NULL DEFAULT NULL,	
		blog_id bigint(20) unsigned NULL DEFAULT NULL,
		group_id bigint(20) unsigned NULL DEFAULT NULL,
		PRIMARY KEY  (event_id)
		) DEFAULT CHARSET=utf8 ;";
	
	$old_table_name = EM_OLD_EVENTS_TABLE; 

	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name && $wpdb->get_var("SHOW TABLES LIKE '$old_table_name'") != $old_table_name) {
		dbDelta($sql);		
		//Add default events
		$in_one_week = date('Y-m-d', time() + 60*60*24*7);
		$in_four_weeks = date('Y-m-d', time() + 60*60*24*7*4); 
		$in_one_year = date('Y-m-d', time() + 60*60*24*365);
		
		$wpdb->query("INSERT INTO ".$table_name." (event_name, event_start_date, event_start_time, event_end_time, location_id, event_slug, event_owner, event_status) VALUES ('Orality in James Joyce Conference', '$in_one_week', '16:00:00', '18:00:00', 1, 'oralty-in-james-joyce-conference','".get_current_user_id()."',1)");
		$wpdb->query("INSERT INTO ".$table_name." (event_name, event_start_date, event_start_time, event_end_time, location_id, event_slug, event_owner, event_status)	VALUES ('Traditional music session', '$in_four_weeks', '20:00:00', '22:00:00', 2, 'traditional-music-session','".get_current_user_id()."',1)");
		$wpdb->query("INSERT INTO ".$table_name." (event_name, event_start_date, event_start_time, event_end_time, location_id, event_slug, event_owner, event_status) VALUES ('6 Nations, Italy VS Ireland', '$in_one_year','22:00:00', '24:00:00', 3, '6-nations-italy-vs-ireland','".get_current_user_id()."',1)");
	}else{
		if( get_option('dbem_version') < 4 && get_option('dbem_version') != '' ){
			$wpdb->query("ALTER TABLE $table_name CHANGE event_seats event_spaces int(5)");
			$wpdb->query("ALTER TABLE $table_name CHANGE event_author event_owner BIGINT( 20 ) UNSIGNED NULL DEFAULT NULL");
		}
		dbDelta($sql);
	}
	em_sort_out_table_nu_keys($table_name, array('event_status','blog_id','event_slug','group_id'));
}

function em_create_events_meta_table(){
	global  $wpdb, $user_level;
	$table_name = EM_META_TABLE;

	// Creating the events table
	$sql = "CREATE TABLE ".$table_name." (
		meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		object_id bigint(20) unsigned NOT NULL,
		meta_key varchar(255) DEFAULT NULL,
		meta_value longtext,
		meta_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY  (meta_id)
		) DEFAULT CHARSET=utf8 ";
		
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	$old_table_name = EM_OLD_LOCATIONS_TABLE;     
	dbDelta($sql);	
	em_sort_out_table_nu_keys($table_name, array('object_id','meta_key'));
}

function em_create_locations_table() {
	
	global  $wpdb, $user_level;
	$table_name = EM_LOCATIONS_TABLE;

	// Creating the events table
	$sql = "CREATE TABLE ".$table_name." (
		location_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		location_slug VARCHAR( 200 ) NOT NULL,
		location_name tinytext NOT NULL,
		location_owner bigint(20) unsigned DEFAULT 0 NOT NULL,
		location_address tinytext NOT NULL,
		location_town tinytext NOT NULL,
		location_state VARCHAR( 200 ) NULL,
		location_postcode VARCHAR( 10 ) NULL,
		location_region VARCHAR( 200 ) NULL,
		location_country CHAR( 2 ) NOT NULL,
		location_latitude float DEFAULT NULL,
		location_longitude float DEFAULT NULL,
		location_description text DEFAULT NULL,
		PRIMARY KEY  (location_id)
		) DEFAULT CHARSET=utf8 ;";
		
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	$old_table_name = EM_OLD_LOCATIONS_TABLE; //for 3.0 
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name && $wpdb->get_var("SHOW TABLES LIKE '$old_table_name'") != $old_table_name) {
		dbDelta($sql);		
		//Add default values
		$wpdb->query("INSERT INTO ".$table_name." (location_name, location_address, location_town, location_state, location_country, location_latitude, location_longitude, location_slug, location_owner) VALUES ('Arts Millenium Building', 'Newcastle Road','Galway','Galway','IE', 53.275, -9.06532, 'arts-millenium-building','".get_current_user_id()."')");
		$wpdb->query("INSERT INTO ".$table_name." (location_name, location_address, location_town, location_state, location_country, location_latitude, location_longitude, location_slug, location_owner) VALUES ('The Crane Bar', '2, Sea Road','Galway','Galway','IE', 53.2692, -9.06151, 'the-crane-bar','".get_current_user_id()."')");
		$wpdb->query("INSERT INTO ".$table_name." (location_name, location_address, location_town, location_state, location_country, location_latitude, location_longitude, location_slug, location_owner) VALUES ('Taaffes Bar', '19 Shop Street','Galway','Galway','IE', 53.2725, -9.05321, 'taffes-bar','".get_current_user_id()."')");
	}else{
		if( get_option('dbem_version') < 4 && get_option('dbem_version') != '' ){
			$wpdb->query('ALTER TABLE wp_em_locations CHANGE location_province location_state TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL');
		}
		dbDelta($sql);
	}
	em_sort_out_table_nu_keys($table_name, array('location_state','location_region','location_country','location_slug'));
}

function em_create_bookings_table() {
	
	global  $wpdb, $user_level;
	$table_name = EM_BOOKINGS_TABLE;
		
	$sql = "CREATE TABLE ".$table_name." (
		booking_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		event_id bigint(20) unsigned NOT NULL,
		person_id bigint(20) unsigned NOT NULL,
		booking_spaces int(5) NOT NULL,
		booking_comment text DEFAULT NULL,
		booking_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		booking_status bool NOT NULL DEFAULT 1,
 		booking_price decimal(10,2) unsigned NOT NULL DEFAULT 0,
		booking_meta LONGTEXT NULL,
		PRIMARY KEY  (booking_id)
		) DEFAULT CHARSET=utf8 ;";
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	if( get_option('dbem_version') != '' && get_option('dbem_version') < 4){
		$wpdb->query("ALTER TABLE $table_name CHANGE  `booking_seats`  `booking_spaces` INT( 5 ) NULL DEFAULT NULL");
	}
	dbDelta($sql);
	em_sort_out_table_nu_keys($table_name, array('event_id'));
} 

//Add the categories table
function em_create_categories_table() {
	
	global  $wpdb, $user_level;
	$table_name = EM_CATEGORIES_TABLE;

	// Creating the events table
	$sql = "CREATE TABLE ".$table_name." (
		category_id bigint(20) unsigned NOT NULL auto_increment,
		category_slug VARCHAR( 200 ) NOT NULL,
		category_owner bigint(20) unsigned DEFAULT 0 NOT NULL,
		category_name tinytext NOT NULL,
		category_description text DEFAULT NULL,
		PRIMARY KEY  (category_id)
		) DEFAULT CHARSET=utf8 ;";
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	$old_table_name = EM_OLD_CATEGORIES_TABLE;     
	
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name && $wpdb->get_var("SHOW TABLES LIKE '$old_table_name'") != $old_table_name) {
		dbDelta($sql);
		$wpdb->insert( $table_name, array('category_name'=>__('Uncategorized', 'dbem'), 'category_slug'=>'uncategorized'), array('%s') );
	}else{
		dbDelta($sql);
	}
	em_sort_out_table_nu_keys($table_name, array('category_slug'));
}


//Add the categories table
function em_create_tickets_table() {
	
	global  $wpdb, $user_level;
	$table_name = EM_TICKETS_TABLE;

	// Creating the events table
	$sql = "CREATE TABLE {$table_name} (
		ticket_id BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT,
		event_id BIGINT( 20 ) UNSIGNED NOT NULL ,
		ticket_name TINYTEXT NOT NULL ,
		ticket_description TEXT NULL ,
		ticket_price DECIMAL( 10 , 2 ) NULL ,
		ticket_start DATETIME NULL ,
		ticket_end DATETIME NULL ,
		ticket_min INT( 10 ) NULL ,
		ticket_max INT( 10 ) NULL ,
		ticket_spaces INT NULL ,
		PRIMARY KEY  (ticket_id)
		) DEFAULT CHARSET=utf8 ;";
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	em_sort_out_table_nu_keys($table_name, array('event_id'));
}

//Add the categories table
function em_create_tickets_bookings_table() {	
	global  $wpdb, $user_level;
	$table_name = EM_TICKETS_BOOKINGS_TABLE;

	// Creating the events table
	$sql = "CREATE TABLE {$table_name} (
		  ticket_booking_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  booking_id bigint(20) unsigned NOT NULL,
		  ticket_id bigint(20) unsigned NOT NULL,
		  ticket_booking_spaces int(6) NOT NULL,
		  ticket_booking_price decimal(10,2) NOT NULL,
		  PRIMARY KEY  (ticket_booking_id)
		) DEFAULT CHARSET=utf8 ;";
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	em_sort_out_table_nu_keys($table_name, array('booking_id','ticket_id'));
}

function em_add_options() {
	$contact_person_email_body_localizable = __("#_BOOKINGNAME (#_BOOKINGEMAIL) will attend #_NAME on #F #j, #Y. He wants to reserve #_BOOKINGSPACES spaces.<br/> Now there are #_BOOKEDSPACES spaces reserved, #_AVAILABLESPACES are still available.<br/>Yours faithfully,<br/>Events Manager - http://wp-events-plugin.com",'dbem').__('<br/><br/>-------------------------------<br/>Powered by Events Manager - http://wp-events-plugin.com','dbem');
	$contact_person_email_cancelled_body_localizable = __("#_BOOKINGNAME (#_BOOKINGEMAIL) cancelled his booking at #_NAME on #F #j, #Y. He wanted to reserve #_BOOKINGSPACES spaces.<br/> Now there are #_BOOKEDSPACES spaces reserved, #_AVAILABLESPACES are still available.<br/>Yours faithfully,<br/>Events Manager - http://wp-events-plugin.com",'dbem').__('<br/><br/>-------------------------------<br/>Powered by Events Manager - http://wp-events-plugin.com','dbem');
	$respondent_email_body_localizable = __("Dear #_BOOKINGNAME, <br/>you have successfully reserved #_BOOKINGSPACES space/spaces for #_NAME.<br/>Yours faithfully,<br/>#_CONTACTNAME",'dbem').__('<br/><br/>-------------------------------<br/>Powered by Events Manager - http://wp-events-plugin.com','dbem');
	$respondent_email_pending_body_localizable = __("Dear #_BOOKINGNAME, <br/>You have requested #_BOOKINGSPACES space/spaces for #_NAME.<br/>Your booking is currently pending approval by our administrators. Once approved you will receive an automatic confirmation.<br/>Yours faithfully,<br/>#_CONTACTNAME",'dbem').__('<br/><br/>-------------------------------<br/>Powered by Events Manager - http://wp-events-plugin.com','dbem');
	$respondent_email_rejected_body_localizable = __("Dear #_BOOKINGNAME, <br/>Your requested booking for #_BOOKINGSPACES spaces at #_NAME on #F #j, #Y has been rejected.<br/>Yours faithfully,<br/>#_CONTACTNAME",'dbem').__('<br/><br/>-------------------------------<br/>Powered by Events Manager - http://wp-events-plugin.com','dbem');
	$respondent_email_cancelled_body_localizable = __("Dear #_BOOKINGNAME, <br/>Your requested booking for #_BOOKINGSPACES spaces at #_NAME on #F #j, #Y has been cancelled.<br/>Yours faithfully,<br/>#_CONTACTNAME",'dbem').__('<br/><br/>-------------------------------<br/>Powered by Events Manager - http://wp-events-plugin.com','dbem');
	
	$dbem_options = array(
		//defaults
		'dbem_default_category'=>0,
		'dbem_default_location'=>0,
		//Event List Options
		'dbem_events_default_orderby' => 'start_date,start_time,name',
		'dbem_events_default_order' => 'ASC',
		'dbem_events_default_limit' => 10,
		'dbem_list_events_page' => 1,
		//Event Anonymous submissions
		'dbem_events_anonymous_submissions' => 0,
		'dbem_events_anonymous_user' => 0,
		'dbem_events_anonymous_result_success' => 'You have successfully submitted your event, which will be published pending approval.',
		//Event Formatting
		'dbem_events_page_title' => __('Events','dbem'),
		'dbem_events_page_scope' => 'future',
		'dbem_events_page_search' => 1,
		'dbem_event_list_item_format_header' => '<table cellpadding="0" cellspacing="0" id="current-events" >
    <thead>
        <tr>
			<th id="event-time" width="150">Date/Time</th>
			<th id="event-description" width="*">Event</th>
   	</thead>
    <tbody>',
		'dbem_event_list_item_format' => '<tr>
			<td>
                #_{d/m/Y} #@_{- d/m/Y}<br/>
                #H:#i -#@H:#@i
            </td>
            <td>
                #_EVENTLINK<br/>
                <i>#_LOCATIONNAME, #_LOCATIONTOWN #_LOCATIONSTATE</i>
            </td>
        </tr>',
		'dbem_event_list_item_format_footer' => '</tbody></table>',
		'dbem_display_calendar_in_events_page' => 0,
		'dbem_single_event_format' => '<div style="float:right; margin:0px 0px 15px 15px;">#_MAP</div>
<p>	
	<strong>Date/Time</strong><br/>
	Date(s) - #j #M #Y #@_{ \u\n\t\i\l j M Y}<br />
	<i>#_12HSTARTTIME - #_12HENDTIME</i>
</p>
<p>	
	<strong>Location</strong><br/>
	#_LOCATIONLINK
</p>
<p>	
	<strong>Category(ies)</strong>
	#_CATEGORIES
</p>
<br style="clear:both" />
#_NOTES
{has_bookings}
<h3>Bookings</h3>
#_BOOKINGFORM
{/has_bookings}',
		'dbem_event_page_title_format' => '#_NAME',
		'dbem_no_events_message' => sprintf(__( 'No %s', 'dbem' ),__('Events','dbem')),
		//Location Formatting
		'dbem_locations_default_orderby' => 'name',
		'dbem_locations_default_order' => 'ASC',
		'dbem_locations_page_title' => __('Event','dbem')." ".__('Locations','dbem'),
		'dbem_no_locations_message' => sprintf(__( 'No %s', 'dbem' ),__('Locations','dbem')),
		'dbem_location_default_country' => 'US',
		'dbem_location_list_item_format' => '#_LOCATIONLINK<ul><li>#_ADDRESS, #_LOCATIONTOWN, #_LOCATIONSTATE</li></ul>',
		'dbem_location_page_title_format' => '#_LOCATIONNAME',
		'dbem_single_location_format' => '<div style="float:right; margin:0px 0px 15px 15px;">#_MAP</div>
<p>	
	<strong>Address</strong><br/>
	#_LOCATIONADDRESS<br/>
	#_LOCATIONTOWN<br/>
	#_LOCATIONSTATE<br/>
	#_LOCATIONREGION<br/>
	#_LOCATIONPOSTCODE<br/>
	#_LOCATIONCOUNTRY
</p>
<br style="clear:both" />
#_DESCRIPTION

<h3>Upcoming Events</h3>
<p>#_NEXTEVENTS</p>',
		'dbem_location_no_events_message' => __('<li>No events in this location</li>', 'dbem'),
		'dbem_location_event_list_item_format' => "<li>#_EVENTLINK - #j #M #Y - #H:#i</li>",
		//Category Formatting
		'dbem_category_page_title_format' => '#_CATEGORYNAME',
		'dbem_category_page_format' => '<p>#_CATEGORYNAME</p>#_CATEGORYNOTES<h3>Upcoming Events</h3>#_CATEGORYNEXTEVENTS',
		'dbem_categories_page_title' => __('Event','dbem')." ".__('Categories','dbem'),
		'dbem_categories_list_item_format' => '<li>#_CATEGORYLINK</li>',
		'dbem_no_categories_message' =>  sprintf(__( 'No %s', 'dbem' ),__('Categories','dbem')),
		'dbem_categories_default_orderby' => 'name',
		'dbem_categories_default_order' =>  'ASC',
		'dbem_category_no_events_message' => __('<li>No events in this category</li>', 'dbem'),
		'dbem_category_event_list_item_format' => "<li>#_EVENTLINK - #j #M #Y - #H:#i</li>",
		//RSS Stuff
		'dbem_rss_limit' => 10,
		'dbem_rss_scope' => 'future',
		'dbem_rss_main_title' => get_bloginfo('title')." - ".__('Events', 'dbem'),
		'dbem_rss_main_description' => get_bloginfo('description')." - ".__('Events', 'dbem'),
		'dbem_rss_description_format' => "#j #M #y - #H:#i <br/>#_LOCATION <br/>#_LOCATIONADDRESS <br/>#_LOCATIONTOWN",
		'dbem_rss_title_format' => "#_NAME",
		//iCal Stuff
		'dbem_ical_limit' => 0,
		'dbem_ical_description_format' => "#_NAME - #_LOCATIONNAME - #j #M #y #H:#i",
		//Google Maps
		'dbem_gmap_is_active'=> 1,
		'dbem_location_baloon_format' =>  "<strong>#_LOCATIONNAME</strong><br/>#_LOCATIONADDRESS - #_LOCATIONTOWN<br/><a href='#_LOCATIONPAGEURL'>Details</a>",
		'dbem_map_text_format' => '<strong>#_LOCATION</strong><p>#_LOCATIONADDRESS</p><p>#_LOCATIONTOWN</p>',
		//Email Config
		'dbem_email_disable_registration' => 0,
		'dbem_rsvp_mail_port' => 465,
		'dbem_smtp_host' => 'localhost',
		'dbem_mail_sender_name' => '',
		'dbem_rsvp_mail_send_method' => 'mail',
		'dbem_rsvp_mail_SMTPAuth' => 1,
		//Image Manipulation
		'dbem_image_max_width' => 700,
		'dbem_image_max_height' => 700,
		'dbem_image_max_size' => 204800,
		//Calendar Options
		'dbem_list_date_title' => __('Events', 'dbem').' - #j #M #y',
		'dbem_full_calendar_event_format' => '<li>#_EVENTLINK</li>',
		'dbem_full_calendar_long_events' => '0',
		'dbem_small_calendar_event_title_format' => "#_NAME",
		'dbem_small_calendar_event_title_separator' => ", ", 
		//General Settings
		'dbem_use_select_for_locations' => 0,
		'dbem_attributes_enabled' => 1,
		'dbem_recurrence_enabled'=> 1,
		'dbem_rsvp_enabled'=> 1,
		'dbem_categories_enabled'=> 1,
		'dbem_placeholders_custom' => '',
		//Title rewriting compatability
		'dbem_disable_title_rewrites'=> false,
		'dbem_title_html' => '<h2>#_PAGETITLE</h2>',
		//Bookings
		'dbem_bookings_form_max' => 20,
		'dbem_bookings_anonymous' => 1, 
		'dbem_bookings_approval' => 1, //approval is on by default
		'dbem_bookings_approval_reserved' => 0, //overbooking before approval?
		'dbem_bookings_login_form' => 1, //show login form on booking area
		'dbem_bookings_approval_overbooking' => 0, //overbooking possible when approving?
		'dbem_bookings_double'=>0,//double bookings or more, users can't double book by default
		'dbem_bookings_user_cancellation' => 1, //can users cancel their booking?
			//messages
			'dbem_booking_feedback_pending' =>__('Booking successful, pending confirmation (you will also receive an email once confirmed).', 'dbem'),
			'dbem_booking_feedback' => __('Booking successful.', 'dbem'),
			'dbem_booking_feedback_full' => __('Booking cannot be made, not enough spaces available!', 'dbem'),
			'dbem_booking_feedback_log_in' => __('You must log in or register to make a booking.','dbem'),
			'dbem_booking_feedback_nomail' => __('However, there were some problems whilst sending confirmation emails to you and/or the event contact person. You may want to contact them directly and letting them know of this error.', 'dbem'),
			'dbem_booking_feedback_error' => __('Booking could not be created','dbem').':',
			'dbem_booking_feedback_email_exists' => __('This email already exists in our system, please log in to register to proceed with your booking.','dbem'),
			'dbem_booking_feedback_new_user' => __('A new user account has been created for you. Please check your email for access details.','dbem'),
			'dbem_booking_feedback_reg_error' => __('There was a problem creating a user account, please contact a website administrator.','dbem'),
			'dbem_booking_feedback_already_booked' => __('You already have booked a seat at this event.','dbem'),
			'dbem_booking_feedback_min_space' => __('You must request at least one space to book an event.','dbem'),
			//Emails
			'dbem_default_contact_person' => 1, //admin
			'dbem_bookings_notify_admin' => 0,
			'dbem_bookings_contact_email' => 1,
			'dbem_bookings_contact_email_subject' => __("New booking",'dbem'),
			'dbem_bookings_contact_email_body' => str_replace("<br/>", "\n\r", $contact_person_email_body_localizable),
			'dbem_contactperson_email_cancelled_subject' => __("Booking Cancelled",'dbem'),
			'dbem_contactperson_email_cancelled_body' => str_replace("<br/>", "\n\r", $contact_person_email_cancelled_body_localizable),
			'dbem_bookings_email_pending_subject' => __("Booking Pending",'dbem'),
			'dbem_bookings_email_pending_body' => str_replace("<br/>", "\n\r", $respondent_email_pending_body_localizable),
			'dbem_bookings_email_rejected_subject' => __("Booking Rejected",'dbem'),
			'dbem_bookings_email_rejected_body' => str_replace("<br/>", "\n\r", $respondent_email_rejected_body_localizable),
			'dbem_bookings_email_confirmed_subject' => __('Booking Confirmed','dbem'),
			'dbem_bookings_email_confirmed_body' => str_replace("<br/>", "\n\r", $respondent_email_body_localizable),
			'dbem_bookings_email_cancelled_subject' => __('Booking Cancelled','dbem'),
			'dbem_bookings_email_cancelled_body' => str_replace("<br/>", "\n\r", $respondent_email_cancelled_body_localizable),
			//Bookings Form - beta - not working at all yet
			'dbem_bookings_page' => '<p>Date/Time - #j #M #Y #_12HSTARTTIME #@_{ \u\n\t\i\l j M Y}<br />Where - #_LOCATIONLINK</p>#_EXCERPT #_BOOKINGFORM<p>'.__('Powered by','dbem').'<a href="http://wp-events-plugin.com">events manager</a></p>',
			'dbem_bookings_page_title' => __('Bookings - #_NAME','dbem'),
			//Ticket Specific Options
			'dbem_bookings_tickets_orderby' => 'ticket_price DESC, ticket_name ASC',
			'dbem_bookings_tickets_priority' => 0,
			'dbem_bookings_tickets_show_unavailable' => 0,
			'dbem_bookings_tickets_show_loggedout' => 1,
			'dbem_bookings_tickets_single' => 0,
			'dbem_bookings_tickets_single_form' => 0, 
			//My Bookings Page
			'dbem_bookings_my_title_format' => __('My Bookings','dbem'),
		//Flags
		'dbem_hello_to_user' => 1,
		//BP Settings
		'dbem_bp_events_list_format_header' => '<ul class="em-events-list">',
		'dbem_bp_events_list_format' => '<li>#_EVENTLINK - #j #M #Y #_12HSTARTTIME #@_{ \u\n\t\i\l j M Y}<ul><li>#_LOCATIONLINK - #_LOCATIONADDRESS, #_LOCATIONTOWN</li></ul></li>',
		'dbem_bp_events_list_format_footer' => '</ul>',
		'dbem_bp_events_list_none_format' => '<p class="em-events-list">'.__('No Events','dbem').'</p>'
	);
	
	foreach($dbem_options as $key => $value){
		add_option($key, $value);
	}
	if( !get_option('dbem_version') ){ add_option('dbem_credits',1); }
	if( get_option('dbem_version') < 4.16 ){
		update_option('dbem_ical_limit',0); //fix, would rather do this than change the option name.
		update_option('dbem_category_no_events_message',get_option('dbem_location_no_events_message'));
		update_option('dbem_category_event_list_item_format',get_option('dbem_location_event_list_item_format'));
	}
	if( get_option('dbem_version') < 4.18 ){
		if( get_option('dbem_category_page_format') == '<p>#_CATEGORYNAME</p>#_CATEGORYNOTES<div><h3>Upcoming Events</h3>#_CATEGORYNEXTEVENTS' ){
			update_option('dbem_category_page_format',$dbem_options['dbem_category_page_format']);
		}
	}
}    

function em_set_mass_caps( $roles, $caps ){
	global $wp_roles;
	foreach( $roles as $user_role ){
		foreach($caps as $cap){
			$wp_roles->add_cap($user_role, $cap);
		}
	}
}

function em_set_capabilities(){
	//Get default roles
	global $wp_roles;
	if( get_option('dbem_version') == '' || get_option('dbem_version') < 4 ){
		//Assign caps in groups, as we go down, permissions are "looser"
		$caps = array('publish_events', 'edit_others_events', 'delete_others_events', 'edit_others_locations', 'delete_others_locations', 'manage_others_bookings', 'edit_categories');
		em_set_mass_caps( array('administrator','editor'), $caps );
		
		//Add all the open caps
		$users = array('administrator','editor');
		$caps = array('edit_events', 'edit_locations', 'delete_events', 'manage_bookings', 'delete_locations', 'edit_recurrences', 'read_others_locations');
		em_set_mass_caps( array('administrator','editor'), $caps );
		if( get_option('dbem_version') == '' ){ //so pre v4 doesn't get security loopholes
			em_set_mass_caps(array('contributor','author','subscriber'), $caps);
		}
	}
}

function em_create_events_page(){
	global $wpdb,$current_user;	
	if( get_option('dbem_events_page') == '' && get_option('dbem_dismiss_events_page') != 1 && !is_object( get_page( get_option('dbem_events_page') )) ){
		$post_data = array(
			'post_status' => 'publish', 
			'post_type' => 'page',
			'ping_status' => get_option('default_ping_status'),
			'post_content' => 'CONTENTS', 
			'post_excerpt' => 'CONTENTS',
			'post_title' => __('Events','dbem')
		);
		$post_id = wp_insert_post($post_data, false);
	   	if( $post_id > 0 ){
	   		update_option('dbem_events_page', $post_id); 			
	   	}
	}
}   

// migrate old dbem tables to new em ones
function em_migrate_v3(){
	global $wpdb, $current_user;
	get_currentuserinfo();
	$errors = array();
	
	//approve all old events
	$wpdb->query('UPDATE '.EM_EVENTS_TABLE.' SET event_status=1');
	
	//give all old events a default ticket
	$wpdb->query('TRUNCATE TABLE '.EM_TICKETS_TABLE);
	$wpdb->query("INSERT INTO ".EM_TICKETS_TABLE." (`event_id`, `ticket_name`, `ticket_spaces`) SELECT event_id, 'Standard' as ticket_name, event_spaces FROM ".EM_EVENTS_TABLE." WHERE recurrence!=1 and event_rsvp=1");
	
	//create permalinks for each location, category, event
	$array = array('event' => EM_EVENTS_TABLE, 'location' => EM_LOCATIONS_TABLE, 'category' => EM_CATEGORIES_TABLE);
	foreach( $array as $prefix => $table ){
		$used_slugs = array();
		$results = $wpdb->get_results("SELECT {$prefix}_id AS id, {$prefix}_slug AS slug, {$prefix}_name AS name FROM $table", ARRAY_A);
		foreach($results as $row){
			$slug = sanitize_title($row['name']);
			$count = 2;
			while( in_array($slug, $used_slugs) ){
				$slug = preg_replace('/\-[0-9]+$/', '', $slug).'-'.$count;
				$count++;
			}
			$wpdb->query("UPDATE $table SET {$prefix}_slug='$slug' WHERE {$prefix}_id={$row['id']}");
			$used_slugs[] = $slug;
		}
	}
	//categories
	$wpdb->query('DELETE FROM '.EM_META_TABLE." WHERE meta_key='event-category'");
	$wpdb->query('INSERT INTO '.EM_META_TABLE." (`meta_key`,`meta_value`,`object_id`) SELECT 'event-category' as meta_key, event_category_id, event_id FROM ".EM_EVENTS_TABLE ." WHERE recurrence!=1 AND event_category_id IS NOT NULL");
	
	update_option('em_notice_migrate_v3',1);
	//change some old values so it doesn't surprise admins with new features
	update_option('dbem_events_page_search', 0);
	
	$time_limit = get_option('dbem_events_page_time_limit');
	if ( is_numeric($time_limit) && $time_limit > 0 ){
		$scopes = em_get_scopes();
		if( array_key_exists($time_limit.'-months',$scopes) ){
			update_option('dbem_events_page_scope',$time_limit.'-months');
		}elseif( array_key_exists('month',$scopes) ){
			update_option('dbem_events_page_scope','month');
		}else{
			update_option('dbem_events_page_scope','future');
		}
	}
}

/**
 * Migrates bookings from 3.x or less. Not really recommended due to the amount of spam the other form allowed, but maybe useful for some. 
 */
function em_migrate_bookings(){
	global $wpdb;
	if( $wpdb->get_var("SHOW TABLES LIKE '".EM_PEOPLE_TABLE."'") == EM_PEOPLE_TABLE && current_user_can('activate_plugins') && wp_verify_nonce($_REQUEST['_wpnonce'], 'bookings_migrate') ){
		$new_people = array();
		$ticket_bookings = array();
		$errors = array();
		foreach( $wpdb->get_results("SELECT ticket_id, b.booking_id, booking_spaces, b.person_id, person_name, person_email, person_phone FROM ".EM_BOOKINGS_TABLE." b LEFT JOIN ".EM_PEOPLE_TABLE." p ON p.person_id=b.person_id LEFT JOIN ".EM_EVENTS_TABLE." e ON e.event_id=b.event_id LEFT JOIN ".EM_TICKETS_TABLE." tt ON tt.event_id=e.event_id", ARRAY_A) as $booking_array ){
			if( $booking_array['person_email'] != ''){
				//first we create the user if we hadn't before
				$user = get_user_by_email($booking_array['person_email']);
				if( is_object($user) ){
					//User exists, whether from current insert or not, so ammend array
					$new_people[$booking_array['person_id']] = $user->ID;
				}else{
		 			$username_root = explode('@', $booking_array['person_email']);
					$username_rand = $username_root[0].rand(1,1000);
					while( username_exists($username_rand) ){
						$username_rand = $username_root[0].rand(1,1000);
					}
					$user_array = array(
						'user_email' => $booking_array['person_email'],
						'user_login' => $username_rand,
						'first_name' => $booking_array['person_name'],
						'display_name'=> $booking_array['person_name'],
					);
					$new_people[$booking_array['person_id']] = wp_insert_user($user_array);
					update_user_meta($new_people[$booking_array['person_id']], 'dbem_phone', $booking_array['person_phone']);
				}	
				//save the booking
				if( !is_object($new_people[$booking_array['person_id']]) ){
					$ticket_bookings[] = "({$booking_array['booking_id']} , {$booking_array['ticket_id']}, {$booking_array['booking_spaces']}, '0')";
				}
			}else{
				if( !in_array('You have some bookings without a corresponding person in the database. These bookings will be deleted when you delete the people table.', $errors) ){
					$errors[] = 'You have some bookings without a corresponding person in the database. These bookings will be deleted when you delete the people table.';
				} 
			} 
		} 
		//modify the booking to have data about the new people, we do this here to avoid duplicate names
		foreach($new_people as $old_id => $new_id){
			if( !is_object($new_id) ){
				$wpdb->query('UPDATE '.EM_BOOKINGS_TABLE." SET person_id='".$new_id."', booking_status=1 WHERE person_id='".$old_id."'");
			}elseif( get_class($new_id) == 'WP_Error' ){
				/* @var $new_id WP_Error */
				$errors[] = "Person ID - $old_id : ".$new_id->get_error_message();
			}
		}
		//Finally insert all the tickets bookings
		$wpdb->query('TRUNCATE TABLE '.EM_TICKETS_BOOKINGS_TABLE); //empty tickets bookings table first
		$sql = "INSERT INTO ". EM_TICKETS_BOOKINGS_TABLE.' ( `booking_id` ,`ticket_id` ,`ticket_booking_spaces` ,`ticket_booking_price`) VALUES' . implode(',',$ticket_bookings);
		$ticket_inserts = $wpdb->query($sql);
		if($ticket_inserts === false){
			/* @var $wpdb WPDB */
			$errors[] = 'DB Error when adding ticket bookings : '.$wpdb->last_error;
		}
		if( count($errors) > 0 ){
			echo "<div class='error'><p>There were some errors during the upgrade. It might be that you can ignore these errors, depending on what kind of data was on your database.</p>";
			foreach($errors as $error){
				echo "<p>$error</p>";
			}
			echo "</div>";
		}
		echo "<div class='updated'><p>Bookings have been migrated. Please verify this in the <a href='admin.php?page=events-manager-bookings'>bookings</a> and wordpress <a href='users.php'>users</a> sections (or for older bookings, access the booking info via the admin events list). If this didn't work you can try migrating again or delete the old persons database table which is not in use anymore.</p></div>";
	}
}

function em_migrate_bookings_delete(){
	global $wpdb;
	if( wp_verify_nonce($_REQUEST['_wpnonce'], 'bookings_migrate_delete') ){
		//REMOVE ALL NULL BOOKINGS
		$wpdb->query('DELETE FROM '.EM_BOOKINGS_TABLE." WHERE booking_id IN (SELECT b.booking_id FROM ".EM_BOOKINGS_TABLE." b LEFT JOIN ".EM_PEOPLE_TABLE." p ON p.person_id=b.person_id LEFT JOIN ".EM_EVENTS_TABLE." e ON e.event_id=b.event_id LEFT JOIN ".EM_TICKETS_TABLE." tt ON tt.event_id=e.event_id) WHERE person_email IS NULL");
		$wpdb->query('DROP TABLE '.$wpdb->prefix.'em_people');
		echo "<div class='updated'><p>Old People table deleted, enjoy Events Manager 4!</p></div>";
	}
}
?>