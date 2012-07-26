<?php
//Main loader for buddypress
/**
 * Events Manager component for BuddyPress
 * @author marcus
 * @since 5.0
 */
class BP_EM_Component extends BP_Component {
	
	function __construct() {
		global $bp;
		parent::start('events',	__('Events', 'dbem'), EM_DIR);
		$this->includes();
		//TODO make BP component optional
		$bp->active_components[$this->id] = '1';
	}

	function includes() {
		// Files to include
		$includes = array(
			'buddypress/bp-em-activity.php',
			'buddypress/bp-em-templatetags.php',
			'buddypress/bp-em-notifications.php',
			'buddypress/screens/profile.php',
			'buddypress/screens/my-events.php',
			'buddypress/screens/my-locations.php',
			'buddypress/screens/attending.php',
			'buddypress/screens/my-bookings.php',
			'buddypress/screens/my-group-events.php'
		);
		if( bp_is_active('groups') ){
			$includes[] = 'buddypress/screens/group-events.php';
			$includes[] = 'buddypress/bp-em-groups.php';
		}
		parent::includes( $includes );
		//TODO add admin pages for extra BP specific settings
	}

	/**
	 * Sets up the global Events Manager BuddyPress Components
	 */
	function setup_globals() {
		global $bp, $wpdb;
		// Define a slug constant that will be used to view this components pages
		if ( !defined( 'BP_EM_SLUG' ) )
			define ( 'BP_EM_SLUG', EM_POST_TYPE_EVENT_SLUG );

		// Set up the $globals array to be passed along to parent::setup_globals()
		$globals = array(
			'slug'                  => BP_EM_SLUG,
			'has_directory'         => false, //already done by EM
			'notification_callback' => 'bp_em_format_notifications',
			'search_string'         => sprintf(__( 'Search %s...', 'dbem' ),__('Events','dbem')),
		);

		// Let BP_Component::setup_globals() do its work.
		parent::setup_globals( $globals );

		//quick link shortcut - may need to revisit this
		$bp->{$this->id}->link = trailingslashit($bp->loggedin_user->domain).BP_EM_SLUG.'/';
	}
	
	function setup_nav() {
		global $blog_id; 
		//check multisite or normal mode for correct permission checking
		if(is_multisite() && $blog_id != BP_ROOT_BLOG){
			//FIXME MS mode doesn't seem to recognize cross subsite caps, using the proper functions, for now we use switch_blog.
			$current_blog = $blog_id;
			switch_to_blog(BP_ROOT_BLOG);
			$can_manage_events = current_user_can_for_blog(BP_ROOT_BLOG, 'edit_events');
			$can_manage_locations = current_user_can_for_blog(BP_ROOT_BLOG, 'edit_locations');
			$can_manage_bookings = current_user_can_for_blog(BP_ROOT_BLOG, 'manage_bookings');
			switch_to_blog($current_blog);
		}else{
			$can_manage_events = current_user_can('edit_events');
			$can_manage_locations = current_user_can('edit_locations');
			$can_manage_bookings = current_user_can('manage_bookings');
		}
		/* Add 'Events' to the main user profile navigation */
		$main_nav = array(
			'name' => __( 'Events', 'dbem' ),
			'slug' => em_bp_get_slug(),
			'position' => 80,
			'screen_function' => 'bp_em_events',
			'default_subnav_slug' => 'profile'
		);

		$em_link = trailingslashit( bp_loggedin_user_domain() . em_bp_get_slug() );
		
		/* Create SubNav Items */
		$sub_nav[] = array(
			'name' => __( 'My Profile', 'dbem' ),
			'slug' => 'profile',
			'parent_slug' => em_bp_get_slug(),
			'parent_url' => $em_link,
			'screen_function' => 'bp_em_events',
			'position' => 10
		);
		
		$sub_nav[] = array(
			'name' => __( 'Events I\'m Attending', 'dbem' ),
			'slug' => 'attending',
			'parent_slug' => em_bp_get_slug(),
			'parent_url' => $em_link,
			'screen_function' => 'bp_em_attending',
			'position' => 20,
			'user_has_access' => bp_is_my_profile() // Only the logged in user can access this on his/her profile
		);
	
		if( $can_manage_events ){
			$sub_nav[] = array(
				'name' => __( 'My Events', 'dbem' ),
				'slug' => 'my-events',
				'parent_slug' => em_bp_get_slug(),
				'parent_url' => $em_link,
				'screen_function' => 'bp_em_my_events',
				'position' => 30,
				'user_has_access' => bp_is_my_profile() // Only the logged in user can access this on his/her profile
			);
		}
		
		if( $can_manage_locations && get_option('dbem_locations_enabled') ){
			$sub_nav[] = array(
				'name' => __( 'My Locations', 'dbem' ),
				'slug' => 'my-locations',
				'parent_slug' => em_bp_get_slug(),
				'parent_url' => $em_link,
				'screen_function' => 'bp_em_my_locations',
				'position' => 40,
				'user_has_access' => bp_is_my_profile() // Only the logged in user can access this on his/her profile
			);
		}
		
		if( $can_manage_bookings && get_option('dbem_rsvp_enabled') ){
			$sub_nav[] = array(
				'name' => __( 'My Event Bookings', 'dbem' ),
				'slug' => 'my-bookings',
				'parent_slug' => em_bp_get_slug(),
				'parent_url' => $em_link,
				'screen_function' => 'bp_em_my_bookings',
				'position' => 50,
				'user_has_access' => bp_is_my_profile() // Only the logged in user can access this on his/her profile
			);
		}
	
		if( bp_is_active('groups') ){
			/* Create Profile Group Sub-Nav */
			$sub_nav[] = array(
				'name' => __( 'Events', 'dbem' ),
				'slug' => 'group-events',
				'parent_slug' => bp_get_groups_slug(),
				'parent_url' =>trailingslashit( bp_loggedin_user_domain() . bp_get_groups_slug() ),
				'screen_function' => 'bp_em_my_group_events',
				'position' => 60,
				'user_has_access' => bp_is_my_profile() // Only the logged in user can access this on his/her profile
			);
		}
		
		parent::setup_nav( $main_nav, $sub_nav );
		add_action( 'bp_init', array(&$this, 'setup_group_nav') );
	}
	
	function setup_group_nav(){
		global $bp;	
		/* Add some group subnav items */
		$user_access = false;
		$group_link = '';
		if( bp_is_active('groups') && !empty($bp->groups->current_group) ){
			$group_link = $bp->root_domain . '/' . bp_get_groups_root_slug() . '/' . $bp->groups->current_group->slug . '/';
			$user_access = $bp->groups->current_group->user_has_access;
			if( !empty($bp->current_component) && $bp->current_component == 'groups' ){
				$count = EM_Events::count(array('group'=>$bp->groups->current_group->id));
				if( empty($count) ) $count = 0;
			}
			bp_core_new_subnav_item( array( 
				'name' => __( 'Events', 'dbem' ) . " <span>$count</span>",
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
}
function bp_em_load_core_component() {
	global $bp;
	$bp->events = new BP_EM_Component();
}
add_action( 'bp_loaded', 'bp_em_load_core_component' );

if( !is_admin() ){
	/*
	 * Links and URL Rewriting
	 */
	function em_bp_rewrite_edit_url($url, $EM_Event){
		global $bp;
		return $bp->events->link.'my-events/?action=edit&event_id='.$EM_Event->event_id;
	}
	if( !get_option('dbem_edit_events_page') ){
		add_filter('em_event_get_edit_url','em_bp_rewrite_edit_url',10,2);
	}
	
	
	function em_bp_rewrite_bookings_url($url, $EM_Event){
		global $bp;
		return $bp->events->link.'my-bookings/?event_id='.$EM_Event->event_id;
	}
	if( !get_option('dbem_edit_bookings_page') ){
		add_filter('em_event_get_bookings_url','em_bp_rewrite_bookings_url',10,2);
	}
	
	function em_bp_rewrite_edit_location_url($url, $EM_Location){
		global $bp;
		return $bp->events->link.'my-locations/?action=edit&location_id='.$EM_Location->location_id;
	}
	if( !get_option('dbem_edit_locations_page') ){
		add_filter('em_location_get_edit_url','em_bp_rewrite_edit_location_url',10,2);
	}
}

/**
 * Delete events when you delete a user.
 */
function bp_em_remove_data( $user_id ) {
	$EM_Events = EM_Events::get(array('scope'=>'all','owner'=>$user_id, 'status'=>false));
	EM_Events::delete($EM_Events);
}
add_action( 'wpmu_delete_user', 'bp_em_remove_data', 1 );
add_action( 'delete_user', 'bp_em_remove_data', 1 );

define('EM_BP_LOADED',true); //so we know
?>