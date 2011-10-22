<?php
// Define a slug constant that will be used to view this components pages
if ( !defined( 'BP_EM_SLUG' ) )
	define ( 'BP_EM_SLUG', 'events' );

//Include component files
require ( dirname( __FILE__ ) . '/bp-em-activity.php' ); /* The notifications file should contain functions to send email notifications on specific user actions */
require ( dirname( __FILE__ ) . '/bp-em-templatetags.php' ); /* The templatetags file should contain classes and functions designed for use in template files */
require ( dirname( __FILE__ ) . '/bp-em-notifications.php' ); /* The notifications file should contain functions to send email notifications on specific user actions */
require ( dirname( __FILE__ ) . '/bp-em-groups.php' ); /* The notifications file should contain functions to send email notifications on specific user actions */
//Screens
	include( dirname( __FILE__ ). '/screens/settings.php');
	include( dirname( __FILE__ ). '/screens/profile.php');
	include( dirname( __FILE__ ). '/screens/my-events.php');
	include( dirname( __FILE__ ). '/screens/my-locations.php');
	include( dirname( __FILE__ ). '/screens/attending.php');
	include( dirname( __FILE__ ). '/screens/my-bookings.php');
	include( dirname( __FILE__ ). '/screens/my-group-events.php');
	include( dirname( __FILE__ ). '/screens/group-events.php');
	

/**
 * bp_em_setup_globals()
 *
 * Sets up global variables for your component.
 */
function bp_em_setup_globals() {
	global $bp, $wpdb;
	$bp->events = new stdClass();
	$bp->events->id = 'events';
	//$bp->events->table_name = $wpdb->base_prefix . 'bp_em';
	$bp->events->format_notification_function = 'bp_em_format_notifications';
	$bp->events->slug = BP_EM_SLUG;
	/* Register this in the active components array */
	$bp->active_components[$bp->events->slug] = $bp->events->id;
	//quick link shortcut
	$bp->events->link = trailingslashit($bp->loggedin_user->domain).'events/';
}
add_action( 'wp', 'bp_em_setup_globals', 2 );
//add_action( 'admin_menu', 'bp_em_setup_globals', 2 );

/**
 * bp_em_setup_nav()
 *
 * Sets up the user profile navigation items for the component. This adds the top level nav
 * item and all the sub level nav items to the navigation array. This is then
 * rendered in the template.
 */
function bp_em_setup_nav() {
	global $bp;
	$count = 0; 
	if( empty($bp->events) ) bp_em_setup_globals();
	/* Add 'Events' to the main user profile navigation */
	bp_core_new_nav_item( array(
		'name' => __( 'Events', 'dbem' ),
		'slug' => $bp->events->slug,
		'position' => 80,
		'screen_function' => (bp_is_my_profile() && current_user_can('edit_events')) ? 'bp_em_my_events':'bp_em_events',
		'default_subnav_slug' => bp_is_my_profile() ? 'my-events':''
	) );

	$em_link = $bp->loggedin_user->domain . $bp->events->slug . '/';

	/* Create two sub nav items for this component */
	bp_core_new_subnav_item( array(
		'name' => __( 'My Profile', 'dbem' ),
		'slug' => 'profile',
		'parent_slug' => $bp->events->slug,
		'parent_url' => $em_link,
		'screen_function' => 'bp_em_events',
		'position' => 10,
		'user_has_access' => bp_is_my_profile() // Only the logged in user can access this on his/her profile
	) );
	
	bp_core_new_subnav_item( array(
		'name' => __( 'Events I\'m Attending', 'dbem' ),
		'slug' => 'attending',
		'parent_slug' => $bp->events->slug,
		'parent_url' => $em_link,
		'screen_function' => 'bp_em_attending',
		'position' => 20,
		'user_has_access' => bp_is_my_profile() // Only the logged in user can access this on his/her profile
	) );

	if( current_user_can('edit_events') ){
		bp_core_new_subnav_item( array(
			'name' => __( 'My Events', 'dbem' ),
			'slug' => 'my-events',
			'parent_slug' => $bp->events->slug,
			'parent_url' => $em_link,
			'screen_function' => 'bp_em_my_events',
			'position' => 30,
			'user_has_access' => bp_is_my_profile() // Only the logged in user can access this on his/her profile
		) );
	}
	
	if( current_user_can('edit_locations') ){
		bp_core_new_subnav_item( array(
			'name' => __( 'My Locations', 'dbem' ),
			'slug' => 'my-locations',
			'parent_slug' => $bp->events->slug,
			'parent_url' => $em_link,
			'screen_function' => 'bp_em_my_locations',
			'position' => 40,
			'user_has_access' => bp_is_my_profile() // Only the logged in user can access this on his/her profile
		) );
	}
	
	if(current_user_can('manage_bookings')){
		bp_core_new_subnav_item( array(
			'name' => __( 'My Event Bookings', 'dbem' ),
			'slug' => 'my-bookings',
			'parent_slug' => $bp->events->slug,
			'parent_url' => $em_link,
			'screen_function' => 'bp_em_my_bookings',
			'position' => 50,
			'user_has_access' => bp_is_my_profile() // Only the logged in user can access this on his/her profile
		) );
	}

	/* Add a nav item for this component under the settings nav item. */
	bp_core_new_subnav_item( array(
		'name' => __( 'Events', 'dbem' ),
		'slug' => 'group-events',
		'parent_slug' => $bp->groups->slug,
		'parent_url' => $bp->loggedin_user->domain . $bp->groups->slug . '/',
		'screen_function' => 'bp_em_my_group_events',
		'position' => 60,
		'user_has_access' => bp_is_my_profile() // Only the logged in user can access this on his/her profile
	) );
	
	/* Add a nav item for this component under the settings nav item. */
	bp_core_new_subnav_item( array(
		'name' => __( 'Events', 'dbem' ),
		'slug' => 'events-settings',
		'parent_slug' => $bp->settings->slug,
		'parent_url' => $bp->loggedin_user->domain . $bp->settings->slug . '/',
		'screen_function' => 'bp_em_screen_settings_menu',
		'position' => 40,
		'user_has_access' => bp_is_my_profile() // Only the logged in user can access this on his/her profile
	) );
	

	/* Create two sub nav items for this component */
	$user_access = false;
	$group_link = '';
	if( !empty($bp->groups->current_group) ){
		$group_link = $bp->root_domain . '/' . $bp->groups->slug . '/' . $bp->groups->current_group->slug . '/';
		$user_access = $bp->groups->current_group->user_has_access;
		if( !empty($bp->current_component) && $bp->current_component == 'groups' ){
			$count = EM_Events::count(array('group'=>$bp->groups->current_group->id));
			if( empty($count) ) $count = 0;
		}
		bp_core_new_subnav_item( array( 
			'name' => sprintf(__( 'Events (%s)', 'dbem' ), $count),
			'slug' => 'events', 
			'parent_url' => $group_link, 
			'parent_slug' => $bp->groups->current_group->slug, 
			'screen_function' => 'bp_em_group_events', 
			'position' => 50, 
			'user_has_access' => $user_access, 
			'item_css_id' => 'forums' 
		));
	}
}

/***
 * In versions of BuddyPress 1.2.2 and newer you will be able to use:
 * add_action( 'bp_setup_nav', 'bp_example_setup_nav' );
 */
add_action( 'wp', 'bp_em_setup_nav', 2 );
add_action( 'admin_menu', 'bp_em_setup_nav', 2 );


function em_bp_rewrite_links($replace, $object, $result){
	global $bp;
	if( is_object($object) && get_class($object)=='EM_Event' ){
		switch( $result ){
			case '#_EDITEVENTURL':
			case '#_EDITEVENTLINK':
				if( $object->can_manage('edit_events','edit_others_events') && !is_admin() ){
					$replace = $bp->events->link.'my-events/edit/?event_id='.$object->id;
					if($result == '#_EDITEVENTLINK'){
						$replace = "<a href='".$replace."'>".__('Edit', 'dbem').' '.__('Event', 'dbem')."</a>";
					}
				}	 
				break;
			case '#_BOOKINGSLINK':	
			case '#_BOOKINGSURL':
				if( $object->can_manage('manage_bookings','manage_others_bookings') && !is_admin() ){
					$replace = $bp->events->link.'my-bookings/?event_id='.$object->id;
					if($result == '#_BOOKINGSLINK'){
						$replace = "<a href='{$replace}' title='{$object->name}'>{$object->name}</a>";
					}
				}
				break;
		}
	}
	return $replace;
}
add_filter('em_event_output_placeholder','em_bp_rewrite_links',10,3);
			
/**
 * bp_em_load_template_filter()
 *
 * You can define a custom load template filter for your component. This will allow
 * you to store and load template files from your plugin directory.
 *
 * This will also allow users to override these templates in their active theme and
 * replace the ones that are stored in the plugin directory.
 *
 * If you're not interested in using template files, then you don't need this function.
 *
 * This will become clearer in the function bp_em_screen_one() when you want to load
 * a template file.
 */
function bp_em_load_template_filter( $found_template, $templates ) {
	global $bp;

	/**
	 * Only filter the template location when we're on the em component pages.
	 */
	if ( $bp->current_component != $bp->events->slug )
		return $found_template;

	foreach ( (array) $templates as $template ) {
		if ( file_exists( STYLESHEETPATH . '/' . $template ) )
			$filtered_templates[] = STYLESHEETPATH . '/' . $template;
		else
			$filtered_templates[] = dirname( __FILE__ ) . '/templates/' . $template;
	}

	$found_template = $filtered_templates[0];

	return apply_filters( 'bp_em_load_template_filter', $found_template );
}
add_filter( 'bp_located_template', 'bp_em_load_template_filter', 10, 2 );

/**
 * Remove a screen notification for a user.
 */
function bp_em_remove_screen_notifications() {
	global $bp;
	bp_core_delete_notifications_for_user_by_type( $bp->loggedin_user->id, $bp->events->slug, 'attending' );
}
add_action( 'bp_em_my_events', 'bp_em_remove_screen_notifications' );
add_action( 'xprofile_screen_display_profile', 'bp_em_remove_screen_notifications' );

/**
 * Delete events when you delete a user.
 */
function bp_em_remove_data( $user_id ) {
	$EM_Events = EM_Events::get(array('scope'=>'all','owner'=>$user_id, 'status'=>false));
	EM_Events::delete($EM_Events);
}
add_action( 'wpmu_delete_user', 'bp_em_remove_data', 1 );
add_action( 'delete_user', 'bp_em_remove_data', 1 );

?>