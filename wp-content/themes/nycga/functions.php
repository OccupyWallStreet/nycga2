<?php

function bbg_change_home_tab_name() {
  global $bp;

  if ( bp_is_group() ) {
    $bp->bp_options_nav[bp_get_current_group_slug()]['home']['name'] = 'Activity';
  }
}
add_action( 'groups_setup_nav', 'bbg_change_home_tab_name' );


function my_bp_search_form_type_select() {
	global $bp;

	$options = array();

	if ( bp_is_active( 'groups' ) )
		$options['groups']  = __( 'Groups',  'buddypress' );
		
	$options['events'] = __( 'Events', 'buddypress' );

	if ( bp_is_active( 'xprofile' ) )
		$options['members'] = __( 'Members', 'buddypress' );

	if ( bp_is_active( 'forums' ) && bp_forums_is_installed_correctly() && bp_forums_has_directory() )
		$options['forums']  = __( 'Forums',  'buddypress' );

	$options['posts'] = __( 'Posts', 'buddypress' );

	// Eventually this won't be needed and a page will be built to integrate all search results.
	$selection_box  = '<label for="search-which" class="accessibly-hidden">' . __( 'Search these:', 'buddypress' ) . '</label>';
	$selection_box .= '<select name="search-which" id="search-which" style="width: auto">';

	$options = apply_filters( 'bp_search_form_type_select_options', $options );
	foreach( (array)$options as $option_value => $option_title ) {
		$selection_box .= sprintf( '<option id="%s" value="%s">%s</option>', $option_value . "-dropdown-option", $option_value, $option_title );

	}

	$selection_box .= '</select>';
	return $selection_box;

}
add_filter('bp_search_form_type_select','my_bp_search_form_type_select');


function add_script() {
   if (!is_admin()) {
       // comment out the next two lines to load the local copy of jQuery
       	// wp_deregister_script('jquery');
       	wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js', false, '1.5.2');
		wp_enqueue_script('jquery');
		wp_enqueue_script('toggler', get_bloginfo('url') . '/wp-content/js/hide-form/toggler.js');
		}
	}

add_action('init', 'add_script');

add_action('wp_footer', 'add_search_form_script');

function add_search_form_script() {
	?>
	<script>
	// $(document).ready(function() {
	// 	$('#other').click(function() {
	// 	  $('#target').click();
	// 	});
	// }
	// );
	</script>
	<?php
}




register_sidebar(
	array(
		'name' => 'Sidebar 2',
		'id' => 'sidebar-2',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widgettitle">',
		'after_title' => '</h3>'
	)
);


register_sidebar(
	array(
		'name' => 'Sidebar 3',
		'id' => 'sidebar-3',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widgettitle">',
		'after_title' => '</h3>'
	)
);

register_sidebar(
	array(
		'name' => 'Sidebar 4',
		'id' => 'sidebar-4',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widgettitle">',
		'after_title' => '</h3>'
	)
);


// register_sidebar(
// 	array(
// 		'name' => 'Widgeted Page',
// 		'id' => 'centerwidget-page',
// 		'before_widget' => '<div id="%1$s" class="widget %2$s">',
// 		'after_widget' => '</div>',
// 		'before_title' => '<h3 class="widgettitle">',
// 		'after_title' => '</h3>'
// 	)
// );



	


function change_activity_plus_root_folder() {	
	echo "<script>
	var _bpfbRootUrl = '" . get_stylesheet_directory_uri().  "';
	</script>";
}

add_action('wp_head','change_activity_plus_root_folder');


// add_action('init', 'redirect_to_parent_event_if_on_child');

// function redirect_to_parent_event_if_on_child() {
// 	if(! strpos($_SERVER['REQUEST_URI'], 'my-events/edit'))
// 		return;
// 	$event_id= substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], 'event_id')+9);

// 	global $wpdb;
// 	$recurrence_id = $wpdb->get_results("SELECT recurrence_id, recurrence FROM wp_em_events WHERE event_id='{$event_id}'");

// 	if($recurrence_id[0]->recurrence != "1" ) {
// 		$rewritten_link = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?'));
// 		$rewritten_link = site_url() . $rewritten_link .'?event_id=' . $event_id;
// 	}
		
// }


// uncomment line below to add a memory usage statistic to the footer of the page 
// add_action('wp_footer', 'nycga_check_php_mem_usage'); 
function nycga_check_php_mem_usage()
{
	function convert($size)
	{
		$unit=array('b','kb','mb','gb','tb','pb');
		return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
	}

	echo convert(memory_get_peak_usage(true)); // 123 kb
}

// only add below filters if events plugin is enabled
if (defined('EM_VERSION'))
{

	// add filter to enable recurrence_id arg in count function
	add_filter('em_events_count', 'nycga_allow_recurrence', 2, 10);
	function nycga_allow_recurrence ($count, $args)
	{
		if ( isset($args['recurrence_id']))
		{
			global $wpdb;	
			$count = true; 
			$events_table = EM_EVENTS_TABLE;
			$locations_table = EM_LOCATIONS_TABLE;

			$args = EM_Events::get_default_search($args);

			$conditions = EM_Events::build_sql_conditions($args);
			$conditions['recurrence'] = "`recurrence_id`='" . (int) $args['recurrence_id'] . "'";
			unset($conditions['recurring']);

			$limit = ( $args['limit'] && is_numeric($args['limit'])) ? "LIMIT {$args['limit']}" : '';
			$offset = ( $limit != "" && is_numeric($args['offset']) ) ? "OFFSET {$args['offset']}" : '';

			//Put it all together
			$where = ( count($conditions) > 0 ) ? " WHERE " . implode ( " AND ", $conditions ):'';

			//Get ordering instructions
			$EM_Event = new EM_Event();
			$accepted_fields = $EM_Event->get_fields(true);
			$orderby = EM_Events::build_sql_orderby($args, $accepted_fields, get_option('dbem_events_default_order'));
			//Now, build orderby sql
			$orderby_sql = ( count($orderby) > 0 ) ? 'ORDER BY '. implode(', ', $orderby) : '';

			//Create the SQL statement and execute
			$selectors = ( $count ) ?  'COUNT(*)':'*';
			$sql = "
				SELECT $selectors FROM $events_table
				LEFT JOIN $locations_table ON {$locations_table}.location_id={$events_table}.location_id
				$where
				$orderby_sql
				$limit $offset
			";
			return $wpdb->get_var($sql);
		}
		return $count;
	}

	// needed to fix memory issues -- by default the plugin pulls all events and then does limit/offset in the php.
	function nycga_remove_offset_for_output($args)
	{
		unset($args['offset']);
		return $args;
	}

	add_action('init', 'nycga_remove_events_tabs', 10);
	function nycga_remove_events_tabs()
	{

		remove_action('wp', 'bp_em_setup_nav', 2);

		global $bp; //print_r($bp);

		if( empty($bp->events) ) bp_em_setup_globals();

		$em_link = $bp->loggedin_user->domain . $bp->events->slug . '/';

		$count = EM_Events::count(array('owner' => $bp->displayed_user->id, 'recurrence_id' => '0'));

		/* Add 'Events' to the main user profile navigation */
		bp_core_new_nav_item( array(
			'name' => sprintf(__( 'Events <span>%s</span>', 'dbem' ), $count),
			'slug' => $bp->events->slug,
			'position' => 80,
			'screen_function' => (bp_is_my_profile() && current_user_can('edit_events')) ? 'bp_em_my_events':'bp_em_events',
			'default_subnav_slug' => bp_is_my_profile() ? 'my-events':''
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


		$count = 0;

		/* Create two sub nav items for this component */
		$user_access = false;
		$group_link = '';
		if( !empty($bp->groups->current_group) ){
			$group_link = $bp->root_domain . '/' . $bp->groups->slug . '/' . $bp->groups->current_group->slug . '/';
			$user_access = $bp->groups->current_group->user_has_access;
			if( !empty($bp->current_component) && $bp->current_component == 'groups' ){
				$count = EM_Events::count(array('group'=>$bp->groups->current_group->id, 'recurrence_id' => '0'));
				if( empty($count) ) $count = 0;
			}
			bp_core_new_subnav_item( array( 
				'name' => sprintf(__( 'Events <span>%s</span>', 'dbem' ), $count),
				'slug' => 'events', 
				'parent_url' => $group_link, 
				'parent_slug' => $bp->groups->current_group->slug, 
				'screen_function' => 'bp_em_group_events', 
				'position' => 50, 
				'user_has_access' => $user_access, 
				'item_css_id' => 'events' 
			));
		}

	/*
		global $bp;
		bp_core_remove_subnav_item( $bp->events->slug, 'my-locations' );
		bp_core_remove_subnav_item( $bp->events->slug, 'my-bookings' );
		bp_core_remove_subnav_item( $bp->events->slug, 'attending' );
		bp_core_remove_subnav_item( $bp->events->slug, 'profile' );
	*/
	}

	// add events.js
	add_action('wp_head', 'nycga_events_js');
	function nycga_events_js()
	{
		?><script type="text/javascript" src="<?php echo bloginfo('stylesheet_directory') ?>/events.js"></script><?php
	}

	// allow moderator events to be attached to a group
	add_action('em_event_save_pre','nycga_group_event_save',2,1);
	function nycga_group_event_save($EM_Event){
		if( is_object($EM_Event) && empty($EM_Event->group_id) && !empty($_REQUEST['group_id']) && is_numeric($_REQUEST['group_id']) ){
			//we have been requested an event creation tied to a group, so does this group exist, and does this person have admin rights to it?
			if( groups_is_user_admin(get_current_user_id(), $_REQUEST['group_id']) || groups_is_user_mod(get_current_user_id(), $_REQUEST['group_id'])){
				$EM_Event->group_id = $_REQUEST['group_id'];
			}				
		}	
		return $EM_Event;
	}

	// allow mod to manage group events
	add_action('em_event_can_manage','nycga_em_group_event_can_manage',2,2);
	function nycga_em_group_event_can_manage( $result, $EM_Event){
		if( !$result && !empty($EM_Event->group_id) ){ //only override if already false, incase it's true
			if( (groups_is_user_admin(get_current_user_id(),$EM_Event->group_id) || groups_is_user_mod(get_current_user_id(), $EM_Event->group_id)) && current_user_can('edit_events') ){
				//This user is an admin of the owner's group, so they can edit this event.
				return true;
			}
		}
		return $result;
	}
	
	// require categories
	add_action('em_event_validate', 'nycga_require_category', 2, 10);
	function nycga_require_category($valid, $event)
	{
		if ( empty($_POST['event_categories']) || $_POST['event_categories'][0] == '')
		{
			$event->add_error(__('Category is required'));
			return false;
		}
		return $valid;
	}

	// include general assembly events when showing events from "My Groups"
	add_filter('em_events_build_sql_conditions','nycga_my_events_include_general',10,2);
	function nycga_my_events_include_general( $conditions, $args ){
		if( !empty($args['group']) && $args['group'] == 'my' ){
			$conditions['group'] = "(`group_id` = '0' OR `group_id` = NULL";
			$groups = groups_get_user_groups(get_current_user_id());
			if( count($groups) > 0 ){
				$conditions['group'] .= " OR `group_id` IN (".implode(',',$groups['groups']).")";
			}
			$conditions['group'] .= " )";
		}
		return $conditions;
	}
	
	// render strip of edit/delete buttons
	function nycga_em_edit_strip($event, $url, $echo = true)
	{
		$html = '';
		if ($event->can_manage('edit_events', 'edit_others_events') || $event->can_manage('delete_events', 'delete_others_events'))
		{
			if ($event->can_manage('edit_events', 'edit_others_events') )
			{
				$html .= '<a class="button bp-secondary-action" href="'.$url.'edit/?event_id='.$event->id.'" title="'. __( 'Edit this event', 'dbem' ).'">'.__( 'Edit', 'dbem' ).'</a>';
			}
			if (current_user_can('publish_events') && (current_user_can('admin') || (groups_is_user_admin(get_current_user_id(),$event->group_id) || groups_is_user_mod(get_current_user_id(), $event->group_id))))
			{
				$html .= '<a class="button bp-secondary-action" href="'.$url.'edit/?action=event_duplicate&amp;event_id='.$event->id.'" title="'.__( 'Duplicate this event', 'dbem' ).'">Duplicate</a>';
			}
			if ($event->can_manage('delete_events', 'delete_others_events') )
			{
				$html .= '<span class="trash">&nbsp;<a class="button bp-secondary-action" href="'.$url.'?action=event_delete&amp;event_id='.$event->id.'" class="em-event-delete"  title="'.__( 'Delete this event', 'dbem' ).'" onclick ="if( !confirm(\'Are you sure? This cannot be undone.\') ){ return false; }">'. __('Delete','dbem').'</a></span>';
				if ( $event->is_recurrence()) 
				{
					if ($event->can_manage('edit_events', 'edit_others_events'))
					{
						$html .= '<a class="button bp-secondary-action" href="'.$url.'edit/?event_id='.$event->recurrence_id.'">'. __ ( 'Edit Series', 'dbem' ).'</a>';
					}
					if ($event->can_manage('delete_events','delete_others_events') )
					{
						$recurrence_delete_confirm = __('WARNING! You will delete ALL recurrences of this event.','dbem');
						$html .= '<span class="trash">&nbsp;<a class="button bp-secondary-action" href="'.$url.'?action=event_delete&amp;event_id='.$event->recurrence_id.'&scope=future" class="em-event-rec-delete" title="'. __( 'Delete this series', 'dbem' ).'" onclick ="if( !confirm(\''.$recurrence_delete_confirm.'\') ){ return false; }">'. __('Delete Series','dbem') . '</a></span>';
					}
				}
			}
		}
		if ( ! empty($html))
		{
			$html = '<div class="event-actions">'.$html.'</div>';
			if ($echo)
			{
				echo $html;
			}
			return $html;
		}
		return false;
	}
	
	// make locations list show all instead of only "eventful" locations 
	// (this basically replicates the EM_Locations::get function
	add_filter('em_locations_get', 'nycga_get_all_locations', 10, 2);
	function nycga_get_all_locations($locations, $args)
	{
		if (empty($args['eventful']))
		{
			global $wpdb;
			$events_table = EM_EVENTS_TABLE;
			$locations_table = EM_LOCATIONS_TABLE;
			
			// no need to redo search in these cases
			if( EM_Locations::array_is_numeric($args) || is_numeric($args) ){ //Array of numbers, assume they are event IDs to retreive
				return $locations; //We return all the events matched as an EM_Event array. 
			}elseif( is_array($args) && is_object(current($args)) && get_class((current($args))) == 'EM_Location' ){
				return $locations;
			}	
	
			$limit = ( $args['limit'] && is_numeric($args['limit'])) ? "LIMIT {$args['limit']}" : '';
			$offset = ( $limit != "" && is_numeric($args['offset']) ) ? "OFFSET {$args['offset']}" : '';
			
			//Get the default conditions
			$conditions = EM_Locations::build_sql_conditions($args);
			
			//Put it all together
			$EM_Location = new EM_Location(0); //Empty class for strict message avoidance
			$fields = $locations_table .".". implode(", {$locations_table}.", array_keys($EM_Location->fields));
			$where = ( count($conditions) > 0 ) ? " WHERE " . implode ( " AND ", $conditions ):'';
			
			//Get ordering instructions
			$EM_Event = new EM_Event(); //blank event for below
			$accepted_fields = $EM_Location->get_fields(true);
			$accepted_fields = array_merge($EM_Event->get_fields(true),$accepted_fields);
			$orderby = EM_Locations::build_sql_orderby($args, $accepted_fields, get_option('dbem_events_default_order'));
			//Now, build orderby sql
			$orderby_sql = ( count($orderby) > 0 ) ? 'ORDER BY '. implode(', ', $orderby) : '';
	
			//Create the SQL statement and execute
			$sql = "
				SELECT $fields FROM $locations_table, $events_table
				$where
				GROUP BY {$locations_table}.location_id
				$orderby_sql
				$limit $offset
			";	
	
			$results = $wpdb->get_results($sql, ARRAY_A);
			
			//If we want results directly in an array, why not have a shortcut here?
			if( $args['array'] == true ){
				return apply_filters('em_locations_get_array', $results, $args);
			}
			
			$locations = array();
			foreach ($results as $location){
				$locations[] = new EM_Location($location);
			}
		}
		return $locations;
	}
	
} // if defined('EM_DIR')

// remove 'My Sites' menu except for admins
add_action('init', 'nycga_remove_my_sites_menu', 100);
function nycga_remove_my_sites_menu()
{
	if ( ! current_user_can('admin'))
	{
		remove_action( 'bp_adminbar_menus', 'bp_adminbar_blogs_menu', 6 );
	}
}

// remove dashboard access except for admins
add_action('admin_init', 'nycga_remove_dashboard_access', 100);
function nycga_remove_dashboard_access()
{
	if ( ! current_user_can('admin'))
	{
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
}