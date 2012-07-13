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
 * Display the upgrade page
 * 
 * @package Admin
 * @since 	1.1
 */
function bpe_upgrade()
{
	set_time_limit( 0 );
	
	global $bpe, $wpdb, $bp;
	
	$version = get_blog_option( Buddyvents::$root_blog, 'bpe_dbversion' );
	
	if( ! $version ) $version = '1.0';
	
	$updated = false;

	// needs to bo overridden in wp-config.php
	if( ! defined( 'EVENT_ADMIN_ROLE' ) ) define( 'EVENT_ADMIN_ROLE', 'administrator' );

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	if( ! empty( $wpdb->charset ) )
		$charset_collate =  "DEFAULT CHARACTER SET $wpdb->charset";

	echo '<div class="wrap"><h2>'. __( 'Upgrade', 'events' ) .'</h2><div id="bpe-content">';
	
	// Upgrade to database version 1.1
	if( version_compare( $version, '1.1', '<' ) )
	{
		printf( __( 'Upgrading database structure for DB %s...', 'events' ), 'v1.1' );
		$wpdb->show_errors();
		
		// Create the categories table
		$sql11[] = "CREATE TABLE {$bpe->tables->categories} (
					id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
					name varchar(255) DEFAULT NULL,
					slug varchar(255) DEFAULT NULL
					) {$charset_collate};";
	
		dbDelta( (array)$sql11 );

		// Insert the first category
		$wpdb->query( $wpdb->prepare( "INSERT INTO {$bpe->tables->categories} ( name, slug ) VALUES ( 'Uncategorized', 'uncategorized' )" ) );

		// update the options
		$bpe->options->enable_address = bpe_get_upgrade_option( 'enable_address', false );
		
		// save to the database
		update_blog_option( Buddyvents::$root_blog, 'bpe_options', $bpe->options );
		
		// Add the categories column to the events table 
		bpe_maybe_add_column( $bpe->tables->events, 'category', "bigint(20) NOT NULL AFTER description" );
		
		// Add the key
		$wpdb->query( "ALTER TABLE {$bpe->tables->events} ADD KEY category (category)" );
		
		// Add all events to the default category
		$wpdb->query( "UPDATE {$bpe->tables->events} SET category = 1" );
		
		// Update all image urls
		$images = $wpdb->get_results( $wpdb->prepare( "SELECT id, image FROM {$bpe->tables->events} WHERE image != ''" ) );
		if( $images )
		{
			foreach( $images as $k => $v )
			{
				$image = maybe_unserialize( $v->image );
						
				$mid = str_replace( bp_get_root_domain(), '', $image['mid'] );
				$mini = str_replace( bp_get_root_domain(), '', $image['mini'] );

				$img = array( 'mid' => $mid, 'mini' => $mini );
				$img = serialize( $img );
				
				$wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->events} SET image = %s WHERE id = %d", $img, $v->id ) );
			}
		}
		
		// Update the db version
		update_blog_option( Buddyvents::$root_blog, 'bpe_dbversion', EVENT_DBVERSION );
		
		echo __( 'Done! Please refresh this page.', 'events' ) . "<br />\n";
		$wpdb->hide_errors();
		
		$updated = true;
	} // END UPGRADE TO 1.1
	
	// Upgrade to database version 1.2
	if( version_compare( $version, '1.2', '<' ) )
	{
		printf( __( 'Upgrading database structure for DB %s...', 'events' ), 'v1.2' );
		$wpdb->show_errors();

		$sql12[] = "CREATE TABLE IF NOT EXISTS {$bpe->tables->coords} (
					id int(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
					user_id int(20) unsigned NOT NULL,
					group_id int(20) unsigned NOT NULL,
					lat decimal(17, 14) NOT NULL,
					lng decimal(17, 14) NOT NULL,
					KEY group_id (group_id),
					KEY user_id (user_id)
				   ) {$charset_collate};";
	
		dbDelta( (array)$sql12 );
		
		// Update the db version
		update_blog_option( Buddyvents::$root_blog, 'bpe_dbversion', EVENT_DBVERSION );
		
		echo __( 'Done! Please refresh this page.', 'events' ) . "<br />\n";
		$wpdb->hide_errors();

		$updated = true;
	} // END UPGRADE TO 1.2

	// Upgrade to database version 1.3
	if( version_compare( $version, '1.3', '<' ) )
	{
		printf( __( 'Upgrading database structure for DB %s...', 'events' ), 'v1.3' );
		$wpdb->show_errors();
		
		// Add the is_spam column to the events table 
		bpe_maybe_add_column( $bpe->tables->events, 'is_spam', "tinyint(1) NOT NULL DEFAULT '0' AFTER recurrent" );
		
		// Alter the tables
		$wpdb->query( "ALTER TABLE {$bpe->tables->events} ADD KEY is_spam (is_spam)" );
		$wpdb->query( "ALTER TABLE {$bpe->tables->events} CHANGE longitude longitude decimal(17, 14) NOT NULL");
		$wpdb->query( "ALTER TABLE {$bpe->tables->events} CHANGE latitude latitude decimal(17, 14) NOT NULL");
		$wpdb->query( "ALTER TABLE {$bpe->tables->events} CHANGE id id int(20) unsigned NOT NULL AUTO_INCREMENT" );
		$wpdb->query( "ALTER TABLE {$bpe->tables->events} CHANGE user_id user_id int(20) unsigned NOT NULL" );
		$wpdb->query( "ALTER TABLE {$bpe->tables->events} CHANGE group_id group_id int(20) unsigned NOT NULL" );
		$wpdb->query( "ALTER TABLE {$bpe->tables->events} CHANGE category category int(20) NOT NULL" );
		$wpdb->query( "ALTER TABLE {$bpe->tables->events} CHANGE limit_members limit_members int(20) unsigned NOT NULL" );
		$wpdb->query( "ALTER TABLE {$bpe->tables->members} CHANGE id id int(20) unsigned NOT NULL AUTO_INCREMENT" );
		$wpdb->query( "ALTER TABLE {$bpe->tables->members} CHANGE event_id event_id int(20) unsigned NOT NULL" );
		$wpdb->query( "ALTER TABLE {$bpe->tables->members} CHANGE user_id user_id int(20) unsigned NOT NULL" );
		$wpdb->query( "ALTER TABLE {$bpe->tables->events_meta} CHANGE id id int(20) NOT NULL AUTO_INCREMENT" );
		$wpdb->query( "ALTER TABLE {$bpe->tables->events_meta} CHANGE event_id event_id int(20) NOT NULL" );
		$wpdb->query( "ALTER TABLE {$bpe->tables->categories} CHANGE id id int(20) NOT NULL AUTO_INCREMENT" );
		$wpdb->query( "ALTER TABLE {$bpe->tables->coords} CHANGE id id int(20) unsigned NOT NULL AUTO_INCREMENT" );
		$wpdb->query( "ALTER TABLE {$bpe->tables->coords} CHANGE user_id user_id int(20) unsigned NOT NULL" );
		$wpdb->query( "ALTER TABLE {$bpe->tables->coords} CHANGE group_id group_id int(20) unsigned NOT NULL" );
		$wpdb->query( "ALTER TABLE {$bpe->tables->coords} CHANGE lat lat decimal(17, 14) NOT NULL" );
		$wpdb->query( "ALTER TABLE {$bpe->tables->coords} CHANGE lng lng decimal(17, 14) NOT NULL" );

		// Update the db version
		update_blog_option( Buddyvents::$root_blog, 'bpe_dbversion', EVENT_DBVERSION );
		
		echo __( 'Done! Please refresh this page.', 'events' ) . "<br />\n";
		$wpdb->hide_errors();
		
		$updated = true;
	}
	
	// Upgrade to database version 1.4
	if( version_compare( $version, '1.4', '<' ) )
	{
		printf( __( 'Upgrading database structure for DB %s...', 'events' ), 'v1.4' );
		$wpdb->show_errors();

		$sql14[] = "CREATE TABLE {$bpe->tables->notifications} (
					id int(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
					user_id int(20) unsigned NOT NULL,
					keywords longtext DEFAULT NULL,
					email tinyint(1) NOT NULL DEFAULT '0',
					screen tinyint(1) NOT NULL DEFAULT '0',
					KEY user_id (user_id)
				   ) {$charset_collate};";
	
		dbDelta( (array)$sql14 );
		
		// Update the db version
		update_blog_option( Buddyvents::$root_blog, 'bpe_dbversion', EVENT_DBVERSION );
		
		echo __( 'Done! Please refresh this page.', 'events' ) . "<br />\n";
		$wpdb->hide_errors();
		
		$updated = true;
	}

	// Upgrade to database version 1.5
	if( version_compare( $version, '1.5', '<' ) )
	{
		printf( __( 'Upgrading database structure for DB %s...', 'events' ), 'v1.5' );
		$wpdb->show_errors();
		
		// Add the remind column to the notifications table 
		bpe_maybe_add_column( $bpe->tables->notifications, 'remind', "tinyint(1) NOT NULL DEFAULT '0' AFTER screen" );
		
		// new option
		$bpe->options->timestamp 		   = bpe_get_upgrade_option( 'timestamp', '-1 day' );
		$bpe->options->enable_achievements = bpe_get_upgrade_option( 'enable_achievements', false );
		
		// save to the database
		update_blog_option( Buddyvents::$root_blog, 'bpe_options', $bpe->options );

		// Update the db version
		update_blog_option( Buddyvents::$root_blog, 'bpe_dbversion', EVENT_DBVERSION );
		
		echo __( 'Done! Please refresh this page.', 'events' ) . "<br />\n";
		$wpdb->hide_errors();
		
		$updated = true;
	}

	// Upgrade to database version 1.6
	if( version_compare( $version, '1.6', '<' ) )
	{
		printf( __( 'Upgrading database structure for DB %s...', 'events' ), 'v1.6' );
		$wpdb->show_errors();

		// new option
		$bpe->options->tab_order 			= bpe_get_upgrade_option( 'tab_order', array(
			'active' 	=> 1,
			'archive' 	=> 2,
			'attending' => 3,
			'calendar' 	=> 4,
			'map' 		=> 5,
			'invoices' 	=> 6
		) );
		$bpe->options->backend_order 		= $bpe->options->tab_order;
		$bpe->options->default_tab 			= bpe_get_upgrade_option( 'default_tab', 'active' );
		$bpe->options->active_slug 			= bpe_get_upgrade_option( 'active_slug', 'active' );
		$bpe->options->archive_slug 		= bpe_get_upgrade_option( 'archive_slug', 'archive' );
		$bpe->options->calendar_slug 		= bpe_get_upgrade_option( 'calendar_slug', 'calendar' );
		$bpe->options->map_slug 			= bpe_get_upgrade_option( 'map_slug', 'map' );
		$bpe->options->attending_slug 		= bpe_get_upgrade_option( 'attending_slug', 'attending' );
		$bpe->options->month_slug 			= bpe_get_upgrade_option( 'month_slug', 'month' );
		$bpe->options->day_slug 			= bpe_get_upgrade_option( 'day_slug', 'day' );
		$bpe->options->create_slug 			= bpe_get_upgrade_option( 'create_slug', 'create' );
		$bpe->options->invite_slug 			= bpe_get_upgrade_option( 'invite_slug', 'invite' );
		$bpe->options->edit_slug 			= bpe_get_upgrade_option( 'edit_slug', 'edit' );
		$bpe->options->category_slug 		= bpe_get_upgrade_option( 'category_slug', 'category' );
		$bpe->options->attendee_slug 		= bpe_get_upgrade_option( 'attendee_slug', 'attendee' );
		$bpe->options->directions_slug 		= bpe_get_upgrade_option( 'directions_slug', 'directions' );
		$bpe->options->date_format 			= bpe_get_upgrade_option( 'date_format', get_blog_option( Buddyvents::$root_blog, 'date_format' ) );
		$bpe->options->deactivated_tabs 	= bpe_get_upgrade_option( 'date_format', array() );
		$bpe->options->approve_events 		= bpe_get_upgrade_option( 'approve_events', false );
		$bpe->options->enable_directions 	= bpe_get_upgrade_option( 'enable_directions', true );
		
		// save to the database
		update_blog_option( Buddyvents::$root_blog, 'bpe_options', $bpe->options );

		// Add the remind column to the notifications table 
		bpe_maybe_add_column( $bpe->tables->events, 'approved', "tinyint(1) NOT NULL DEFAULT '1' AFTER is_spam" );

		// Update the db version
		update_blog_option( Buddyvents::$root_blog, 'bpe_dbversion', EVENT_DBVERSION );
		
		echo __( 'Done! Please refresh this page.', 'events' ) . "<br />\n";
		$wpdb->hide_errors();
		
		$updated = true;
	}
	
		// Upgrade to database version 1.7
	if( version_compare( $version, '1.7', '<' ) )
	{
		printf( __( 'Upgrading database structure for DB %s...', 'events' ), 'v1.7' );
		$wpdb->show_errors();
		
		$sql17[] = "CREATE TABLE {$bpe->tables->schedules} (
					id int(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
					event_id int(20) unsigned NOT NULL,
					day date NOT NULL,
					start time NOT NULL,
					end time NOT NULL,
					description longtext NOT NULL,
					KEY event_id (event_id)
				   ) {$charset_collate};";

		$sql17[] = "CREATE TABLE {$bpe->tables->api} (
					id int(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
					user_id int(20) unsigned NOT NULL,
					api_key varchar(255) DEFAULT NULL,
					active tinyint(1) NOT NULL DEFAULT '1',
					date_generated datetime NOT NULL,
					KEY user_id (user_id)
				   ) {$charset_collate};";

		dbDelta( (array)$sql17 );

		$bpe->options->schedule_slug 			= bpe_get_upgrade_option( 'schedule_slug', 'schedule' );
		$bpe->options->map_lang 				= bpe_get_upgrade_option( 'map_lang', '' );
		$bpe->options->ical_tzid 				= bpe_get_upgrade_option( 'ical_tzid', 'Europe/Berlin' );
		$bpe->options->view_slug 				= bpe_get_upgrade_option( 'view_slug', 'view' );
		$bpe->options->feed_slug 				= bpe_get_upgrade_option( 'feed_slug', 'feed' );
		$bpe->options->list_slug 				= bpe_get_upgrade_option( 'list_slug', 'list' );
		$bpe->options->grid_slug 				= bpe_get_upgrade_option( 'grid_slug', 'grid' );
		$bpe->options->search_slug 				= bpe_get_upgrade_option( 'search_slug', 'search' );
		$bpe->options->localize_months 			= bpe_get_upgrade_option( 'localize_months', false );
		$bpe->options->default_tab_attending 	= bpe_get_upgrade_option( 'default_tab_attending', ( ( $bpe->options->default_tab == 'attending' ) ? true : false ) );
		$bpe->options->default_view 			= bpe_get_upgrade_option( 'default_view', $bpe->options->view_slug );
		$bpe->options->enable_api 				= bpe_get_upgrade_option( 'enable_api', false );
				
		// save to the database
		update_blog_option( Buddyvents::$root_blog, 'bpe_options', $bpe->options );

		// Update the db version
		update_blog_option( Buddyvents::$root_blog, 'bpe_dbversion', EVENT_DBVERSION );
		
		echo __( 'Done! Please refresh this page.', 'events' ) . "<br />\n";
		$wpdb->hide_errors();
		
		$updated = true;
	}

	// upgrading to db version 1.8
	if( version_compare( $version, '1.8', '<' ) )
	{
		printf( __( 'Upgrading database structure for DB %s...', 'events' ), 'v1.8' );
		$wpdb->show_errors();

		// Add the remind column to the notifications table 
		bpe_maybe_add_column( $bpe->tables->api, 'hits', "int(20) unsigned NOT NULL AFTER date_generated" );
		bpe_maybe_add_column( $bpe->tables->api, 'hit_date', "datetime NOT NULL AFTER hits" );

		// type in the last version, so to be safe, let's redefine the slug again
		$bpe->options->schedule_slug = bpe_get_upgrade_option( 'schedule_slug', 'schedule' );
		
		// save to the database
		update_blog_option( Buddyvents::$root_blog, 'bpe_options', $bpe->options );
		
		// Update the db version
		update_blog_option( Buddyvents::$root_blog, 'bpe_dbversion', EVENT_DBVERSION );
		
		echo __( 'Done! Please refresh this page.', 'events' ) . "<br />\n";
		$wpdb->hide_errors();
		
		$updated = true;
	}

	// upgrading to db version 1.9
	if( version_compare( $version, '1.9', '<' ) )
	{
		printf( __( 'Upgrading database structure for DB %s...', 'events' ), 'v1.9' );
		$wpdb->show_errors();

		// type in the last version, so to be safe, let's redefine the slug again
		$bpe->options->results_slug = bpe_get_upgrade_option( 'results_slug', 'results' );
		
		// save to the database
		update_blog_option( Buddyvents::$root_blog, 'bpe_options', $bpe->options );
		
		// Update the db version
		update_blog_option( Buddyvents::$root_blog, 'bpe_dbversion', EVENT_DBVERSION );
		
		echo __( 'Done! Please refresh this page.', 'events' ) . "<br />\n";
		$wpdb->hide_errors();
		
		$updated = true;
	}

	// upgrading to db version 2.0
	if( version_compare( $version, '2.0', '<' ) )
	{
		printf( __( 'Upgrading database structure for DB %s...', 'events' ), 'v2.0' );
		$wpdb->show_errors();

		// Add the remind column to the notifications table 
		bpe_maybe_add_column( $bpe->tables->events, 'rsvp', "tinyint(1) NOT NULL DEFAULT '1' AFTER approved" );

		$bpe->options->enable_api_check 		= bpe_get_upgrade_option( 'enable_api_check', false );
		$bpe->options->restrict_api_hits 		= bpe_get_upgrade_option( 'restrict_api_hits', 10 );
		$bpe->options->restrict_api_timespan 	= bpe_get_upgrade_option( 'restrict_api_timespan', 3600 );
		$bpe->options->api_slug 				= bpe_get_upgrade_option( 'api_slug', 'api' );
		$bpe->options->api_key_slug 			= bpe_get_upgrade_option( 'api_key_slug', 'apikey' );
		$bpe->options->tab_order['search'] 		= bpe_get_upgrade_option( 'tab_order', 7, 'search' );
		$bpe->options->backend_order 			= $bpe->options->tab_order;
		$bpe->options->enable_twitter 			= bpe_get_upgrade_option( 'enable_twitter', false );
		$bpe->options->twitter_consumer_key 	= bpe_get_upgrade_option( 'twitter_consumer_key', '' );
		$bpe->options->twitter_consumer_secret 	= bpe_get_upgrade_option( 'twitter_consumer_secret', '' );
		$bpe->options->bitly_login 				= bpe_get_upgrade_option( 'bitly_login', '' );
		$bpe->options->bitly_key 				= bpe_get_upgrade_option( 'bitly_key', '' );
		$bpe->options->enable_facebook 			= bpe_get_upgrade_option( 'enable_facebook', false );
		$bpe->options->facebook_appid 			= bpe_get_upgrade_option( 'facebook_appid', '' );
		$bpe->options->facebook_secret 			= bpe_get_upgrade_option( 'facebook_secret', '' );
		$bpe->options->use_event_images 		= bpe_get_upgrade_option( 'use_event_images', false );
		$bpe->options->enable_eventbrite 		= bpe_get_upgrade_option( 'enable_eventbrite', false );
		$bpe->options->eventbrite_appkey 		= bpe_get_upgrade_option( 'eventbrite_appkey', '' );

		// save to the database
		update_blog_option( Buddyvents::$root_blog, 'bpe_options', $bpe->options );
		
		// Update the db version
		update_blog_option( Buddyvents::$root_blog, 'bpe_dbversion', EVENT_DBVERSION );
		
		echo __( 'Done! Please refresh this page.', 'events' ) . "<br />\n";
		$wpdb->hide_errors();
		
		$updated = true;
	}

	// upgrading to db version 2.1
	if( version_compare( $version, '2.1', '<' ) )
	{
		printf( __( 'Upgrading database structure for DB %s...', 'events' ), 'v2.1' );
		$wpdb->show_errors();
		
		$sql21[] = "CREATE TABLE {$bpe->tables->webhooks} (
					id int(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
					user_id int(20) unsigned NOT NULL,
					event varchar(255) DEFAULT NULL,
					url longtext NOT NULL,
					verifier varchar(255) DEFAULT NULL,
					verified tinyint(1) NOT NULL DEFAULT '0',
					KEY user_id (user_id)
				   ) {$charset_collate};";

		$sql21[] = "CREATE TABLE {$bpe->tables->documents} (
					id int(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
					event_id int(20) unsigned NOT NULL,
					name varchar(255) DEFAULT NULL,
					description longtext NOT NULL,
					url varchar(255) DEFAULT NULL,
					type varchar(5) DEFAULT NULL,
					KEY event_id (event_id)
				   ) {$charset_collate};";

		dbDelta( (array)$sql21 );
		
		bpe_maybe_add_column( $bpe->tables->events, 'url', "varchar(255) NOT NULL AFTER category" );
		bpe_maybe_add_column( $bpe->tables->events, 'all_day', "tinyint(1) NOT NULL DEFAULT '0' AFTER rsvp" );
		bpe_maybe_add_column( $bpe->tables->events, 'timezone', "varchar(255) NOT NULL AFTER all_day" );
		bpe_maybe_add_column( $bpe->tables->members, 'role', "varchar(100) NOT NULL DEFAULT 'attendee' AFTER rsvp_date" );
		bpe_change_event_admin_roles();

		bpe_maybe_delete_column( $bpe->tables->api, 'domain' );
		
		bpe_transform_event_avatars();
		bpe_maybe_delete_column( $bpe->tables->events, 'image' );
		
		bpe_generate_venue_array();

		// MS installs have different table for site options, so delete the option from main blog
		if( is_multisite() )
		{
			$bpe->options = '';
			$options = update_blog_option( Buddyvents::$root_blog, 'bpe_options' );
			
			foreach( (array)$options as $key => $value )
				$bpe->options->{$key} = $value;
				
			delete_blog_option( Buddyvents::$root_blog, 'bpe_options' );
		}
		
		$bpe->options->general_slug 			= bpe_get_upgrade_option( 'general_slug', 'general' );
		$bpe->options->step_slug 				= bpe_get_upgrade_option( 'step_slug', 'step' );
		$bpe->options->enable_webhooks 			= bpe_get_upgrade_option( 'enable_webhooks', false );
		$bpe->options->enable_cubepoints 		= bpe_get_upgrade_option( 'enable_cubepoints', false );
		$bpe->options->cp_create_event 			= bpe_get_upgrade_option( 'cp_create_event', 100 );
		$bpe->options->cp_delete_event 			= bpe_get_upgrade_option( 'cp_delete_event', -100 );
		$bpe->options->cp_attend_event 			= bpe_get_upgrade_option( 'cp_attend_event', 40 );
		$bpe->options->cp_remove_event 			= bpe_get_upgrade_option( 'cp_remove_event', -40 );
		$bpe->options->cp_maybe_attend_event 	= bpe_get_upgrade_option( 'cp_maybe_attend_event', 20 );
		$bpe->options->cp_maybe_remove_event 	= bpe_get_upgrade_option( 'cp_maybe_remove_event', -20 );
		$bpe->options->documents_slug 			= bpe_get_upgrade_option( 'documents_slug', 'documents' );
		$bpe->options->logo_slug 				= bpe_get_upgrade_option( 'logo_slug', 'logo' );	
		$bpe->options->enable_schedules 		= bpe_get_upgrade_option( 'enable_schedules', true );
		$bpe->options->enable_documents 		= bpe_get_upgrade_option( 'enable_documents', true );
		$bpe->options->enable_attendees 		= bpe_get_upgrade_option( 'enable_attendees', true );
		$bpe->options->enable_directions 		= bpe_get_upgrade_option( 'enable_directions', ( ( $bpe->options->enable_directions === false ) ? 2 : 1 ) );
		$bpe->options->manage_slug 				= bpe_get_upgrade_option( 'manage_slug', 'manage' );
		$bpe->options->geonames_username 		= bpe_get_upgrade_option( 'geonames_username', '' );
		$bpe->options->timezone_slug 			= bpe_get_upgrade_option( 'timezone_slug', 'timezone' );
		$bpe->options->venue_slug 				= bpe_get_upgrade_option( 'venue_slug', 'venue' );
		
		unset( $bpe->options->enable_api_domain_check );
		unset( $bpe->options->enable_api_check );
		unset( $bpe->options->ical_tzid );
		
		// save to the database
		update_blog_option( Buddyvents::$root_blog, 'bpe_options', $bpe->options );
		
		// Update the db version
		update_blog_option( Buddyvents::$root_blog, 'bpe_dbversion', EVENT_DBVERSION );
		
		echo __( 'Done! Please refresh this page.', 'events' ) . "<br />\n";
		$wpdb->hide_errors();
		
		$updated = true;
	}

	// upgrading to db version 2.2
	if( version_compare( $version, '2.2', '<' ) )
	{
		printf( __( 'Upgrading database structure for DB %s...', 'events' ), 'v2.2' );
		$wpdb->show_errors();

		$bpe->options->date_format = bpe_get_upgrade_option( 'date_format', get_blog_option( Buddyvents::$root_blog, 'date_format' ) );

		// save to the database
		update_blog_option( Buddyvents::$root_blog, 'bpe_options', $bpe->options );
		
		// Update the db version
		update_blog_option( Buddyvents::$root_blog, 'bpe_dbversion', EVENT_DBVERSION );
		
		echo __( 'Done! Please refresh this page.', 'events' ) . "<br />\n";
		$wpdb->hide_errors();
		
		$updated = true;
	}

	// upgrading to db version 2.3
	if( version_compare( $version, '2.3', '<' ) )
	{
		printf( __( 'Upgrading database structure for DB %s...', 'events' ), 'v2.3' );
		$wpdb->show_errors();

		$bpe->options->enable_logo 	  = bpe_get_upgrade_option( 'enable_logo', true );
		$bpe->options->enable_invites = bpe_get_upgrade_option( 'enable_invites', true );

		// save to the database
		update_blog_option( Buddyvents::$root_blog, 'bpe_options', $bpe->options );
		
		// Update the db version
		update_blog_option( Buddyvents::$root_blog, 'bpe_dbversion', EVENT_DBVERSION );
		
		echo __( 'Done! Please refresh this page.', 'events' ) . "<br />\n";
		$wpdb->hide_errors();
		
		$updated = true;
	}

	// upgrading to db version 2.4
	if( version_compare( $version, '2.4', '<' ) )
	{
		printf( __( 'Upgrading database structure for DB %s...', 'events' ), 'v2.4' );
		$wpdb->show_errors();

		bpe_maybe_add_column( $bpe->tables->events, 'venue_name', "varchar(255) NOT NULL AFTER location" );
		
		// Update the db version
		update_blog_option( Buddyvents::$root_blog, 'bpe_dbversion', EVENT_DBVERSION );
		
		echo __( 'Done! Please refresh this page.', 'events' ) . "<br />\n";
		$wpdb->hide_errors();
		
		$updated = true;
	}
	
	// upgrading to db version 2.4.5
	if( version_compare( $version, '2.4.5', '<' ) )
	{
		printf( __( 'Upgrading database structure for DB %s...', 'events' ), 'v2.4.5' );
		$wpdb->show_errors();

		delete_blog_option( Buddyvents::$root_blog, 'bpe_timezones' );
		delete_blog_option( Buddyvents::$root_blog, 'bpe_venues' );
		
		// Update the db version
		update_blog_option( Buddyvents::$root_blog, 'bpe_dbversion', EVENT_DBVERSION );
		
		echo __( 'Done! Please refresh this page.', 'events' ) . "<br />\n";
		$wpdb->hide_errors();
		
		$updated = true;
	}

	// upgrading to db version 2.5
	if( version_compare( $version, '2.5', '<' ) )
	{
		printf( __( 'Upgrading database structure for DB %s...', 'events' ), 'v2.5' );
		$wpdb->show_errors();
		
		// move everything to blog option
		$options = get_site_option( 'bpe_options' );
		update_blog_option( Buddyvents::$root_blog, 'bpe_options', $options );
		delete_site_option( 'bpe_options' );

		$role = get_role( EVENT_ADMIN_ROLE );
		
		$role->add_cap( 'bpe_manage_events' );
		$role->add_cap( 'bpe_manage_event_approvals' );
		$role->add_cap( 'bpe_manage_event_settings' );
		$role->add_cap( 'bpe_manage_event_apikeys' );
		$role->add_cap( 'bpe_manage_event_webhooks' );
		$role->add_cap( 'bpe_manage_event_sales' );
		$role->add_cap( 'bpe_manage_event_invoices' );
		$role->add_cap( 'bpe_manage_event_categories' );
		
		$sql25[] = "CREATE TABLE {$bpe->tables->tickets} (
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

		$sql25[] = "CREATE TABLE {$bpe->tables->sales} (
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

		$sql25[] = "CREATE TABLE {$bpe->tables->invoices} (
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

		dbDelta( (array)$sql25 );
		
		// Update the db version
		update_blog_option( Buddyvents::$root_blog, 'bpe_dbversion', EVENT_DBVERSION );
		
		// set the current version
		update_blog_option( Buddyvents::$root_blog, 'buddyvents_current_version', '2.0' );

		$bpe->options->enable_tickets 			= bpe_get_upgrade_option( 'enable_tickets', false );
		$bpe->options->tickets_slug 			= bpe_get_upgrade_option( 'tickets_slug', 'tickets' );
		$bpe->options->enable_sandbox 			= bpe_get_upgrade_option( 'enable_sandbox', false );
		$bpe->options->commission_percent 		= bpe_get_upgrade_option( 'commission_percent', 0 );
		$bpe->options->allowed_currencies 		= bpe_get_upgrade_option( 'allowed_currencies', array( 'EUR' ) );
		$bpe->options->enable_invoices 			= bpe_get_upgrade_option( 'enable_invoices', false );
		$bpe->options->invoice_footer1 			= bpe_get_upgrade_option( 'invoice_footer1', '' );
		$bpe->options->invoice_footer2 			= bpe_get_upgrade_option( 'invoice_footer2', '' );
		$bpe->options->invoice_footer3 			= bpe_get_upgrade_option( 'invoice_footer3', '' );
		$bpe->options->invoice_footer4 			= bpe_get_upgrade_option( 'invoice_footer4', '' );
		$bpe->options->invoice_tax 				= bpe_get_upgrade_option( 'invoice_tax', '' );
		$bpe->options->invoice_logo 			= bpe_get_upgrade_option( 'invoice_logo', array() );
		$bpe->options->invoice_message 			= bpe_get_upgrade_option( 'invoice_message', '' );
		$bpe->options->invoice_settle_date 		= bpe_get_upgrade_option( 'invoice_settle_date', 5 );
		$bpe->options->invoice_slug 			= bpe_get_upgrade_option( 'invoice_slug', 'invoices' );
		$bpe->options->paypal_email 			= bpe_get_upgrade_option( 'paypal_email', '' );
		$bpe->options->enable_manual_attendees 	= bpe_get_upgrade_option( 'enable_manual_attendees', false );
		$bpe->options->tab_order['invoices'] 	= bpe_get_upgrade_option( 'tab_order', 8, 'invoices' );
		$bpe->options->backend_order 			= $bpe->options->tab_order;
		$bpe->options->page_id 					= bpe_get_upgrade_option( 'page_id', false );
		$bpe->options->approve_slug 			= bpe_get_upgrade_option( 'approve_slug', 'approve' );
		$bpe->options->sales_slug 				= bpe_get_upgrade_option( 'sales_slug', 'sales' );
		$bpe->options->checkout_slug 			= bpe_get_upgrade_option( 'checkout_slug', 'checkout' );
		$bpe->options->cancel_slug 				= bpe_get_upgrade_option( 'cancel_slug', 'cancel' );
		$bpe->options->success_slug 			= bpe_get_upgrade_option( 'success_slug', 'success' );
		$bpe->options->signup_slug 				= bpe_get_upgrade_option( 'signup_slug', 'signup' );
		$bpe->options->group_contact_required 	= bpe_get_upgrade_option( 'group_contact_required', true );
		$bpe->options->enable_ical			 	= bpe_get_upgrade_option( 'enable_ical', 1 );
				
		if( $bpe->options->use_fullcalendar !== true || ! isset( $bpe->options->use_fullcalendar ) )
			$bpe->options->use_fullcalendar 	= false;
		
				
		// included in core now
		unset( $bpe->options->enable_oembed );
		
		// change directions, schedules and documents options
		$bpe->options->enable_directions = bpe_get_upgrade_option( 'enable_directions', ( ( $bpe->options->enable_directions === 3 	  ) ? 4 : $bpe->options->enable_directions ) );
		$bpe->options->enable_schedules  = bpe_get_upgrade_option( 'enable_schedules', ( ( $bpe->options->enable_schedules === false ) ? 4 : 1 ) );
		$bpe->options->enable_documents  = bpe_get_upgrade_option( 'enable_documents', ( ( $bpe->options->enable_documents === false ) ? 4 : 1 ) );
		
		// chmod 777 the PDF folder
		$path = EVENT_ABSPATH .'components/tickets/pdf-cache/';
		if( is_dir( $path ) )
			chmod( $path, 0777 );

		bpe_maybe_add_column( $bpe->tables->events, 'group_approved', "tinyint(1) NOT NULL DEFAULT '1' AFTER timezone" );
		bpe_maybe_add_column( $bpe->tables->api, 'hits_over', "int(20) unsigned NOT NULL DEFAULT '0' AFTER hit_date" );
		
		// add Buddyvents to the active components array
		$components = bp_get_option( 'bp-active-components' );
		$components[$bpe->options->slug] = '1';
				
		bp_update_option( 'bp-active-components', $components );
				
		// save to the database
		update_blog_option( Buddyvents::$root_blog, 'bpe_options', $bpe->options );
		
		echo __( 'Done! Please refresh this page.', 'events' ) . "<br />\n";
		$wpdb->hide_errors();
		
		$updated = true;
	}

	// upgrading to db version 2.6
	if( version_compare( $version, '2.6', '<' ) )
	{
		printf( __( 'Upgrading database structure for DB %s...', 'events' ), 'v2.6' );
		$wpdb->show_errors();

		$bpe->options->enable_forums 			= bpe_get_upgrade_option( 'enable_forums', false );
		$bpe->options->forum_slug 	 			= bpe_get_upgrade_option( 'forum_slug', 'forum' );
		$bpe->options->topic_slug 	 			= bpe_get_upgrade_option( 'topic_slug', 'topic' );
		$bpe->options->reply_slug 	 			= bpe_get_upgrade_option( 'reply_slug', 'reply' );
		$bpe->options->main_event_forum 		= bpe_get_upgrade_option( 'main_event_forum', '' );
		$bpe->options->forum_tag_slug 			= bpe_get_upgrade_option( 'forum_tag_slug', 'tag' );
		$bpe->options->disable_warnings			= bpe_get_upgrade_option( 'disable_warnings', false );
		$bpe->options->enable_newsletter		= bpe_get_upgrade_option( 'enable_newsletter', false );
		$bpe->options->enable_mailchimp			= bpe_get_upgrade_option( 'enable_mailchimp', false );
		$bpe->options->enable_cmonitor			= bpe_get_upgrade_option( 'enable_cmonitor', false );
		$bpe->options->enable_facebook_pages	= bpe_get_upgrade_option( 'enable_facebook_pages', false );
		$bpe->options->newsletter_slug			= bpe_get_upgrade_option( 'newsletter_slug', 'newsletter' );
			
		// set the status for all current events
		$ids = (array) $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$bpe->tables->events}" ) );
		
		if( count( $ids ) > 0 ) :
			$insert = array();
			foreach( (array)$ids as $id )
				$insert[] = $wpdb->prepare( "(%d, 'status', 'active')",	$id );
		
			$insert = implode( ',',$insert );
	
			$wpdb->query( $wpdb->prepare( "INSERT INTO {$bpe->tables->events_meta} ( event_id, meta_key, meta_value ) VALUES {$insert};" ) );
		endif;
		
		// save to the database
		update_blog_option( Buddyvents::$root_blog, 'bpe_options', $bpe->options );
		
		// Update the db version
		update_blog_option( Buddyvents::$root_blog, 'bpe_dbversion', EVENT_DBVERSION );
		
		echo __( 'Done! Please refresh this page.', 'events' ) . "<br />\n";
		$wpdb->hide_errors();
		
		$updated = true;
	}

	// upgrading to db version 2.7
	if( version_compare( $version, '2.7', '<' ) )
	{
		printf( __( 'Upgrading database structure for DB %s...', 'events' ), 'v2.7' );
		$wpdb->show_errors();
		
		// Let's do some cleaning up from an earlier bug with recurrent events
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->events} WHERE user_id = 0 AND group_id = 0" ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->members} WHERE user_id = 0" ) );
		
		// need to make sure that the default value for group_approved is in fact 1
		$wpdb->query( $wpdb->prepare( "ALTER TABLE {$bpe->tables->events} ALTER COLUMN group_approved SET DEFAULT '1'" ) );
		
		// transform all group_approved = 0 into group_approved = 1
		if( defined( 'BPE_RESET_GROUP_APPROVED' ) && BPE_RESET_GROUP_APPROVED === true )
			$wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->events} SET group_approved = 1 WHERE group_approved = 0" ) );

		$bpe->options->enable_aweber = bpe_get_upgrade_option( 'enable_aweber', false );
		$bpe->options->enable_groups = bpe_get_upgrade_option( 'enable_groups', true  );
				
		// save to the database
		update_blog_option( Buddyvents::$root_blog, 'bpe_options', $bpe->options );
		
		// Update the db version
		update_blog_option( Buddyvents::$root_blog, 'bpe_dbversion', EVENT_DBVERSION );
		
		echo __( 'Done! Please refresh this page.', 'events' ) . "<br />\n";
		$wpdb->hide_errors();
		
		$updated = true;
	}

    // upgrading to db version 2.8
    if( version_compare( $version, '2.8', '<' ) )
    {
        printf( __( 'Upgrading database structure for DB %s...', 'events' ), 'v2.8' );
        $wpdb->show_errors();
            
        // this will run the installation routine and should
        // upgrade anyone with problems from db version 2.7
        if( ! function_exists( 'bpe_activate' ) )
            require_once( EVENT_ABSPATH .'admin/bpe-install.php' );
            
        bpe_activate();

		// Update the db version
		update_blog_option( Buddyvents::$root_blog, 'bpe_dbversion', EVENT_DBVERSION );
            
        echo __( 'Done! Please refresh this page.', 'events' ) . "<br />\n";
        $wpdb->hide_errors();
            
        $updated = true;
    }

    // upgrading to db version 2.9
    if( version_compare( $version, '2.9', '<' ) )
    {
        printf( __( 'Upgrading database structure for DB %s...', 'events' ), 'v2.9' );
        $wpdb->show_errors();
            
		// add status metadata for all events
		$events = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$bpe->tables->events}" ) );		
		foreach( $events as $event_id ) 
		{
			if( ! bpe_get_eventmeta( $event_id, 'status' ) )
				bpe_update_eventmeta( $event_id, 'status', 'active' );
		}

		// Update the db version
		update_blog_option( Buddyvents::$root_blog, 'bpe_dbversion', EVENT_DBVERSION );
		
        echo __( 'Done! Please refresh this page.', 'events' ) . "<br />\n";
        $wpdb->hide_errors();
            
        $updated = true;
    }
	
    // upgrading to db version 3.0
    if( version_compare( $version, '3.0', '<' ) )
    {
        printf( __( 'Upgrading database structure for DB %s...', 'events' ), 'v3.0' );
        $wpdb->show_errors();
            
		$bpe->options->enable_bp_gallery = bpe_get_upgrade_option( 'enable_bp_gallery', false );
		$bpe->options->gallery_slug		 = bpe_get_upgrade_option( 'gallery_slug', 'gallery' );
				
		// save to the database
		update_blog_option( Buddyvents::$root_blog, 'bpe_options', $bpe->options );

		// Update the db version
		update_blog_option( Buddyvents::$root_blog, 'bpe_dbversion', EVENT_DBVERSION );
		
        echo __( 'Done! Please refresh this page.', 'events' ) . "<br />\n";
        $wpdb->hide_errors();
            
        $updated = true;
    }

	if( ! $updated )
    	echo __( 'Upgrade failed!', 'events' );
		
	echo '</div></div>';
    return;
}

/**
 * Check for a db column
 * THX to NextGEN Gallery
 * 
 * @package Admin
 * @since 	1.1
 */
function bpe_maybe_add_column( $table_name, $column_name, $create_ddl )
{
	global $wpdb;
	
	foreach( $wpdb->get_col( $wpdb->prepare( "SHOW COLUMNS FROM {$table_name}" ) ) as $column )
	{
		if( $column == $column_name )
			return true;
	}
	
	$wpdb->query( $wpdb->prepare( "ALTER TABLE {$table_name} ADD {$column_name} {$create_ddl}" ) );
	
	foreach( $wpdb->get_col( $wpdb->prepare( "SHOW COLUMNS FROM {$table_name}" ) ) as $column )
	{
		if( $column == $column_name )
			return true;
	}
	
	echo "\n" . sprintf( __( 'Could not add column %s in table %s<br />', 'events' ), $column_name, $table_name ) ."\n";
	return false;
}

/**
 * Delete a db column
 * 
 * @package Admin
 * @since 	1.7
 */
function bpe_maybe_delete_column( $table_name, $column_name )
{
	global $wpdb;
	
	foreach( $wpdb->get_col( $wpdb->prepare( "SHOW COLUMNS FROM {$table_name}" ) ) as $column )
	{
		if( $column == $column_name )
		{
			$wpdb->query( $wpdb->prepare( "ALTER TABLE {$table_name} DROP {$column_name}" ) );
			return true;
		}
	}
	
	echo "\n" . sprintf( __( 'Could not delete column %s in table %s<br />', 'events' ), $column_name, $table_name ) ."\n";
	return false;
}

/**
 * Make old avatars into new ones, then delete old files
 * 
 * @package Admin
 * @since 	1.7
 */
function bpe_transform_event_avatars()
{
	global $wpdb, $bpe;
	
	$images = $wpdb->get_results( $wpdb->prepare( "SELECT id as event_id, image as src FROM {$bpe->tables->events} WHERE image != ''" ) );
	
	foreach( (array)$images as $key => $image )
	{
		$image_src = maybe_unserialize( $image->src );
	
		// move images to /event-avatars/{event_id}/{image-name}
		if( file_exists( ABSPATH . $image_src['mid'] ) )
		{
			$img_name = pathinfo( $image_src['mid'], PATHINFO_BASENAME );
			$path = BP_AVATAR_UPLOAD_PATH . '/event-avatars/' . $image->event_id;

			// maybe create the dir
			if( ! file_exists( $path ) )
				@wp_mkdir_p( $path );

			rename( ABSPATH . $image_src['mid'], $path .'/'. $img_name );
		
			// process the new image
			bp_core_avatar_handle_crop(array(
				'object' => 'event',
				'avatar_dir' => 'event-avatars',
				'item_id' => $image->event_id,
				'original_file' => '/event-avatars/'. $image->event_id .'/'. $img_name
			));
			
			// delete the leftover image
			@unlink( ABSPATH . $image_src['mini'] );
		}
	}
}

/**
 * Change the role of the event admin back to admin
 * 
 * @package Admin
 * @since 	1.7
 */
function bpe_change_event_admin_roles()
{
	global $wpdb, $bpe;
	
	$admins = $wpdb->get_results( $wpdb->prepare( "SELECT id as event_id, user_id FROM {$bpe->tables->events}" ) );

	foreach( (array)$admins as $key => $admin )
		bpe_set_event_member_role( $admin->user_id, $admin->event_id, 'admin' );
}

/**
 * Build the first venue array
 * 
 * @package Admin
 * @since 	1.7
 */
function bpe_generate_venue_array()
{
	global $wpdb, $bpe;
	
	$locations = $wpdb->get_col( $wpdb->prepare( "SELECT location FROM {$bpe->tables->events} WHERE location != '' AND venue_name = ''" ) );
	$venue_names = $wpdb->get_col( $wpdb->prepare( "SELECT venue_name FROM {$bpe->tables->events} WHERE venue_name != ''" ) );
	
	$venues = array_values( array_merge( $locations, $venue_names ) );
	
	foreach( (array)$venues as $venue )
	{
		$slug = sanitize_title_with_dashes( $venue );
		$bpe->config->venues[$slug] = $venue;
	}
		
	$bpe->config->venues = array_unique( $bpe->config->venues );
	update_blog_option( Buddyvents::$root_blog, 'bpe_venues', $bpe->config->venues );
}

/**
 * Only update an option if needed
 * 
 * @package Admin
 * @since 	2.1.1
 */
function bpe_get_upgrade_option( $option, $value, $sub = false )
{
	global $bpe;
	
	$original_value = ( $sub ) ? $bpe->options->{$option}[$sub] : $bpe->options->{$option};

	if( ! isset( $original_value ) ) :
		return $value;
	endif;
	
	if( is_array( $value ) ) :
		if( maybe_serialize( $value ) != maybe_serialize( $original_value ) ) :
			return $original_value;
		endif;
	endif;
	
	return ( $sub ) ? $bpe->options->{$option}[$sub] : $bpe->options->{$option};
}
?>