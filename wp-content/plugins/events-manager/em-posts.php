<?php
//Custom Post Type and Taxonomy Names, overridable in wp-config.php, not adviseable but the point is flexibility if you know what you're doing ;)
if( !defined('EM_POST_TYPE_EVENT') ) define('EM_POST_TYPE_EVENT','event');
if( !defined('EM_POST_TYPE_LOCATION') ) define('EM_POST_TYPE_LOCATION','location');
if( !defined('EM_TAXONOMY_CATEGORY') ) define('EM_TAXONOMY_CATEGORY','event-categories');
if( !defined('EM_TAXONOMY_TAG') ) define('EM_TAXONOMY_TAG','event-tags');
//Slugs
define('EM_POST_TYPE_EVENT_SLUG',get_option('dbem_cp_events_slug', 'events'));
define('EM_POST_TYPE_LOCATION_SLUG',get_option('dbem_cp_locations_slug', 'locations'));
define('EM_TAXONOMY_CATEGORY_SLUG', get_site_option('dbem_taxonomy_category_slug', 'events/categories'));
define('EM_TAXONOMY_TAG_SLUG', get_option('dbem_taxonomy_tag_slug', 'events/tags'));

/*
 * This checks that you have post thumbnails enabled, if not, it enables it. 
 * You can always disable this by adding remove_action('after_setup_theme','wp_events_plugin_after_setup_theme'); in your functions.php theme file.
 */
add_action('after_setup_theme','wp_events_plugin_after_setup_theme',100);
function wp_events_plugin_after_setup_theme(){
	if( !get_option('disable_post_thumbnails') && function_exists('add_theme_support') ){
		global $_wp_theme_features;
		if( !empty($_wp_theme_features['post-thumbnails']) ){
			//either leave as true, or add our cpts to this
			if( is_array($_wp_theme_features['post-thumbnails']) ){
				$post_thumbnails = array_shift($_wp_theme_features['post-thumbnails']);
				//add to featured image post types for specific themes
				$post_thumbnails[] = EM_POST_TYPE_EVENT;
				$post_thumbnails[] = 'event-recurring';
				$post_thumbnails[] = EM_POST_TYPE_LOCATION;
				add_theme_support('post-thumbnails', $post_thumbnails);
			}
		}else{
			add_theme_support('post-thumbnails'); //need to add this for themes that don't have it. 
		}
	}
}
//This bit registers the CPTs
add_action('init','wp_events_plugin_init',1);
function wp_events_plugin_init(){
	define('EM_ADMIN_URL',admin_url().'edit.php?post_type='.EM_POST_TYPE_EVENT); //we assume the admin url is absolute with at least one querystring
	if( get_option('dbem_tags_enabled', true) ){
		register_taxonomy(EM_TAXONOMY_TAG,array(EM_POST_TYPE_EVENT,'event-recurring'),array( 
			'hierarchical' => false, 
			'public' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array('slug' => EM_TAXONOMY_TAG_SLUG,'with_front'=>false),
			//'update_count_callback' => '',
			//'show_tagcloud' => true,
			//'show_in_nav_menus' => true,
			'label' => __('Event Tags'),
			'singular_label' => __('Event Tag'),
			'labels' => array(
				'name'=>__('Event Tags','dbem'),
				'singular_name'=>__('Event Tag','dbem'),
				'search_items'=>__('Search Event Tags','dbem'),
				'popular_items'=>__('Popular Event Tags','dbem'),
				'all_items'=>__('All Event Tags','dbem'),
				'parent_items'=>__('Parent Event Tags','dbem'),
				'parent_item_colon'=>__('Parent Event Tag:','dbem'),
				'edit_item'=>__('Edit Event Tag','dbem'),
				'update_item'=>__('Update Event Tag','dbem'),
				'add_new_item'=>__('Add New Event Tag','dbem'),
				'new_item_name'=>__('New Event Tag Name','dbem'),
				'seperate_items_with_commas'=>__('Seperate event tags with commas','dbem'),
				'add_or_remove_items'=>__('Add or remove events','dbem'),
				'choose_from_the_most_used'=>__('Choose from most used event tags','dbem'),
			),
			'capabilities' => array(
				'manage_terms' => 'edit_event_categories',
				'edit_terms' => 'edit_event_categories',
				'delete_terms' => 'delete_event_categories',
				'assign_terms' => 'edit_events',
			)
		));
	}
	if( get_option('dbem_categories_enabled', true) ){
		$supported_array = (EM_MS_GLOBAL && !is_main_site()) ? array():array(EM_POST_TYPE_EVENT,'event-recurring');
		register_taxonomy(EM_TAXONOMY_CATEGORY,$supported_array,array( 
			'hierarchical' => true, 
			'public' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array('slug' => EM_TAXONOMY_CATEGORY_SLUG, 'hierarchical' => true,'with_front'=>false),
			//'update_count_callback' => '',
			//'show_tagcloud' => true,
			//'show_in_nav_menus' => true,
			'label' => __('Event Categories','dbem'),
			'singular_label' => __('Event Category','dbem'),
			'labels' => array(
				'name'=>__('Event Categories','dbem'),
				'singular_name'=>__('Event Category','dbem'),
				'search_items'=>__('Search Event Categories','dbem'),
				'popular_items'=>__('Popular Event Categories','dbem'),
				'all_items'=>__('All Event Categories','dbem'),
				'parent_items'=>__('Parent Event Categories','dbem'),
				'parent_item_colon'=>__('Parent Event Category:','dbem'),
				'edit_item'=>__('Edit Event Category','dbem'),
				'update_item'=>__('Update Event Category','dbem'),
				'add_new_item'=>__('Add New Event Category','dbem'),
				'new_item_name'=>__('New Event Category Name','dbem'),
				'seperate_items_with_commas'=>__('Seperate event categories with commas','dbem'),
				'add_or_remove_items'=>__('Add or remove events','dbem'),
				'choose_from_the_most_used'=>__('Choose from most used event categories','dbem'),
			),
			'capabilities' => array(
				'manage_terms' => 'edit_event_categories',
				'edit_terms' => 'edit_event_categories',
				'delete_terms' => 'delete_event_categories',
				'assign_terms' => 'edit_events',
			)
		));
	}
	$event_post_type = array(	
		'public' => true,
		'hierarchical' => false,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus'=>true,
		'can_export' => true,
		'exclude_from_search' => false,
		'publicly_queryable' => true,
		'rewrite' => array('slug' => EM_POST_TYPE_EVENT_SLUG,'with_front'=>false),
		'has_archive' => get_option('dbem_cp_events_has_archive', false) == true,
		'supports' => apply_filters('em_cp_event_supports', array('custom-fields','title','editor','excerpt','comments','thumbnail','author')),
		'capability_type' => 'event',
		'capabilities' => array(
			'publish_posts' => 'publish_events',
			'edit_posts' => 'edit_events',
			'edit_others_posts' => 'edit_others_events',
			'delete_posts' => 'delete_events',
			'delete_others_posts' => 'delete_others_events',
			'read_private_posts' => 'read_private_events',
			'edit_post' => 'edit_event',
			'delete_post' => 'delete_event',
			'read_post' => 'read_event',		
		),
		'label' => __('Events','dbem'),
		'description' => __('Display events on your blog.','dbem'),
		'labels' => array (
			'name' => __('Events','dbem'),
			'singular_name' => __('Event','dbem'),
			'menu_name' => __('Events','dbem'),
			'add_new' => __('Add Event','dbem'),
			'add_new_item' => __('Add New Event','dbem'),
			'edit' => __('Edit','dbem'),
			'edit_item' => __('Edit Event','dbem'),
			'new_item' => __('New Event','dbem'),
			'view' => __('View','dbem'),
			'view_item' => __('View Event','dbem'),
			'search_items' => __('Search Events','dbem'),
			'not_found' => __('No Events Found','dbem'),
			'not_found_in_trash' => __('No Events Found in Trash','dbem'),
			'parent' => __('Parent Event','dbem'),
		),
		'menu_icon' => plugins_url('includes/images/calendar-16.png', __FILE__)
	);
	if ( get_option('dbem_recurrence_enabled') ){
		$event_recurring_post_type = array(	
			'public' => apply_filters('em_cp_event_recurring_public', false),
			'show_ui' => true,
			'show_in_admin_bar' => true,
			'show_in_menu' => 'edit.php?post_type='.EM_POST_TYPE_EVENT,
			'show_in_nav_menus'=>false,
			'publicly_queryable' => apply_filters('em_cp_event_recurring_publicly_queryable', false),
			'exclude_from_search' => true,
			'has_archive' => false,
			'can_export' => true,
			'hierarchical' => false,
			'supports' => apply_filters('em_cp_event_supports', array('custom-fields','title','editor','excerpt','comments','thumbnail','author')),
			'capability_type' => 'recurring_events',
			'rewrite' => array('slug' => 'events-recurring','with_front'=>false),
			'capabilities' => array(
				'publish_posts' => 'publish_recurring_events',
				'edit_posts' => 'edit_recurring_events',
				'edit_others_posts' => 'edit_others_recurring_events',
				'delete_posts' => 'delete_recurring_events',
				'delete_others_posts' => 'delete_others_recurring_events',
				'read_private_posts' => 'read_private_recurring_events',
				'edit_post' => 'edit_recurring_event',
				'delete_post' => 'delete_recurring_event',
				'read_post' => 'read_recurring_event',
			),
			'label' => __('Recurring Events','dbem'),
			'description' => __('Recurring Events Template','dbem'),
			'labels' => array (
				'name' => __('Recurring Events','dbem'),
				'singular_name' => __('Recurring Event','dbem'),
				'menu_name' => __('Recurring Events','dbem'),
				'add_new' => __('Add Recurring Event','dbem'),
				'add_new_item' => __('Add New Recurring Event','dbem'),
				'edit' => __('Edit','dbem'),
				'edit_item' => __('Edit Recurring Event','dbem'),
				'new_item' => __('New Recurring Event','dbem'),
				'view' => __('View','dbem'),
				'view_item' => __('Add Recurring Event','dbem'),
				'search_items' => __('Search Recurring Events','dbem'),
				'not_found' => __('No Recurring Events Found','dbem'),
				'not_found_in_trash' => __('No Recurring Events Found in Trash','dbem'),
				'parent' => __('Parent Recurring Event','dbem'),
			)
		);
	}
	if( get_option('dbem_locations_enabled', true) ){
		$location_post_type = array(	
			'public' => true,
			'hierarchical' => false,
			'show_in_admin_bar' => true,
			'show_ui' => !(EM_MS_GLOBAL && !is_main_site() && get_site_option('dbem_ms_mainblog_locations')),
			'show_in_menu' => 'edit.php?post_type='.EM_POST_TYPE_EVENT,
			'show_in_nav_menus'=>true,
			'can_export' => true,
			'exclude_from_search' => get_option('dbem_cp_locations_search_results') == false,
			'publicly_queryable' => true,
			'rewrite' => array('slug' => EM_POST_TYPE_LOCATION_SLUG, 'with_front'=>false),
			'query_var' => true,
			'has_archive' => get_option('dbem_cp_locations_has_archive', false) == true,
			'supports' => apply_filters('em_cp_location_supports', array('title','editor','excerpt','custom-fields','comments','thumbnail','author')),
			'capability_type' => 'location',
			'capabilities' => array(
				'publish_posts' => 'publish_locations',
				'delete_others_posts' => 'delete_others_locations',
				'delete_posts' => 'delete_locations',
				'delete_post' => 'delete_location',
				'edit_others_posts' => 'edit_others_locations',
				'edit_posts' => 'edit_locations',
				'edit_post' => 'edit_location',
				'read_private_posts' => 'read_private_locations',
				'read_post' => 'read_location',
			),
			'label' => __('Locations','dbem'),
			'description' => __('Display locations on your blog.','dbem'),
			'labels' => array (
				'name' => __('Locations','dbem'),
				'singular_name' => __('Location','dbem'),
				'menu_name' => __('Locations','dbem'),
				'add_new' => __('Add Location','dbem'),
				'add_new_item' => __('Add New Location','dbem'),
				'edit' => __('Edit','dbem'),
				'edit_item' => __('Edit Location','dbem'),
				'new_item' => __('New Location','dbem'),
				'view' => __('View','dbem'),
				'view_item' => __('View Location','dbem'),
				'search_items' => __('Search Locations','dbem'),
				'not_found' => __('No Locations Found','dbem'),
				'not_found_in_trash' => __('No Locations Found in Trash','dbem'),
				'parent' => __('Parent Location','dbem'),
			)
		);
	}
	if( strstr(EM_POST_TYPE_EVENT_SLUG, EM_POST_TYPE_LOCATION_SLUG) !== FALSE ){
		//Now register posts, but check slugs in case of conflicts and reorder registrations
		register_post_type(EM_POST_TYPE_EVENT, $event_post_type);
		if ( get_option('dbem_recurrence_enabled') ){
			register_post_type('event-recurring', $event_recurring_post_type);
		}
		if( get_option('dbem_locations_enabled', true) ){
			register_post_type(EM_POST_TYPE_LOCATION, $location_post_type);
		}
	}else{
		if( get_option('dbem_locations_enabled', true) ){
			register_post_type(EM_POST_TYPE_LOCATION, $location_post_type);
		}
		register_post_type(EM_POST_TYPE_EVENT, $event_post_type);
		//Now register posts, but check slugs in case of conflicts and reorder registrations
		if ( get_option('dbem_recurrence_enabled') ){
			register_post_type('event-recurring', $event_recurring_post_type);
		}
	}
}

//Post Customization
function supported_event_custom_fields($supported){
	$remove = array();
	if( !get_option('dbem_cp_events_custom_fields') ) $remove[] = 'custom-fields';
	if( !get_option('dbem_cp_events_comments') ) $remove[] = 'comments';
	return  supported_custom_fields($supported, $remove);
}
add_filter('em_cp_event_supports', 'supported_event_custom_fields',10,1);

function supported_location_custom_fields($supported){
	$remove = array();
	if( !get_option('dbem_cp_locations_custom_fields') ) $remove[] = 'custom-fields';
	if( !get_option('dbem_cp_locations_comments') ) $remove[] = 'comments';
	return supported_custom_fields($supported, $remove);
}
add_filter('em_cp_location_supports', 'supported_location_custom_fields',10,1);

function supported_custom_fields($supported, $remove = array()){
	foreach($supported as $key => $support_field){
		if( in_array($support_field, $remove) ){
			unset($supported[$key]);
		}
	}
	return $supported;
}

function em_map_meta_cap( $caps, $cap, $user_id, $args ) {
	/* Handle event reads */
	if ( 'edit_event' == $cap || 'delete_event' == $cap || 'read_event' == $cap ) {
		$EM_Event = em_get_event($args[0],'post_id');
		$post_type = get_post_type_object( $EM_Event->post_type );
		/* Set an empty array for the caps. */
		$caps = array();
		//Filter according to event caps
		switch( $cap ){
			case 'read_event':
				if ( 'private' != $EM_Event->post_status )
					$caps[] = 'read';
				elseif ( $user_id == $EM_Event->event_owner )
					$caps[] = 'read';
				else
					$caps[] = $post_type->cap->read_private_posts;
				break;
			case 'edit_event':
				if ( $user_id == $EM_Event->event_owner )
					$caps[] = $post_type->cap->edit_posts;
				else
					$caps[] = $post_type->cap->edit_others_posts;
				break;
			case 'delete_event':
				if ( $user_id == $EM_Event->event_owner )
					$caps[] = $post_type->cap->delete_posts;
				else
					$caps[] = $post_type->cap->delete_others_posts;
				break;
		}
	}
	if ( 'edit_recurring_event' == $cap || 'delete_recurring_event' == $cap || 'read_recurring_event' == $cap ) {
		$EM_Event = em_get_event($args[0],'post_id');
		$post_type = get_post_type_object( $EM_Event->post_type );
		/* Set an empty array for the caps. */
		$caps = array();
		//Filter according to recurring_event caps
		switch( $cap ){
			case 'read_recurring_event':
				if ( 'private' != $EM_Event->post_status )
					$caps[] = 'read';
				elseif ( $user_id == $EM_Event->event_owner )
					$caps[] = 'read';
				else
					$caps[] = $post_type->cap->read_private_posts;
				break;
			case 'edit_recurring_event':
				if ( $user_id == $EM_Event->event_owner )
					$caps[] = $post_type->cap->edit_posts;
				else
					$caps[] = $post_type->cap->edit_others_posts;
				break;
			case 'delete_recurring_event':
				if ( $user_id == $EM_Event->event_owner )
					$caps[] = $post_type->cap->delete_posts;
				else
					$caps[] = $post_type->cap->delete_others_posts;
				break;
		}
	}
	if ( 'edit_location' == $cap || 'delete_location' == $cap || 'read_location' == $cap ) {
		$EM_Location = em_get_location($args[0],'post_id');
		$post_type = get_post_type_object( $EM_Location->post_type );
		/* Set an empty array for the caps. */
		$caps = array();
		//Filter according to location caps
		switch( $cap ){
			case 'read_location':
				if ( 'private' != $EM_Location->post_status )
					$caps[] = 'read';
				elseif ( $user_id == $EM_Location->location_owner )
					$caps[] = 'read';
				else
					$caps[] = $post_type->cap->read_private_posts;
				break;
			case 'edit_location':
				if ( $user_id == $EM_Location->location_owner )
					$caps[] = $post_type->cap->edit_posts;
				else
					$caps[] = $post_type->cap->edit_others_posts;
				break;
			case 'delete_location':
				if ( $user_id == $EM_Location->location_owner )
					$caps[] = $post_type->cap->delete_posts;
				else
					$caps[] = $post_type->cap->delete_others_posts;
				break;
		}
	}
	/* Return the capabilities required by the user. */
	return $caps;
}
add_filter( 'map_meta_cap', 'em_map_meta_cap', 10, 4 );