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
	
	add_action('toplevel_page_events-manager', 'nycga_limit_events_list');
	
	add_action('admin_menu', 'nycga_override_em_admin_list', 100);
	function nycga_override_em_admin_list()
	{
		remove_action('toplevel_page_events-manager', 'em_admin_events_page');
	}

	function nycga_limit_events_list()
	{
		//TODO Simplify panel for events, use form flags to detect certain actions (e.g. submitted, etc)
		global $wpdb, $EM_Notices, $EM_Event;
	
		$action = ( !empty($_REQUEST ['action']) ) ? $_REQUEST ['action']:'';
		$order = ( !empty($_REQUEST ['order']) ) ? $_REQUEST ['order']:'ASC';
		$limit = ( !empty($_REQUEST['limit']) ) ? $_REQUEST['limit'] : 20;//Default limit
		$page = ( !empty($_REQUEST['pno']) ) ? $_REQUEST['pno']:1;
		$offset = ( $page > 1 ) ? ($page-1)*$limit : 0;
		$search = ( !empty($_REQUEST['em_search']) ) ? $_REQUEST['em_search']:'';
		$scope_names = em_get_scopes();
		$scope = ( !empty($_REQUEST ['scope']) && array_key_exists($_REQUEST ['scope'], $scope_names) ) ? $_REQUEST ['scope']:'future';
		$selectedEvents = ( !empty($_REQUEST ['events']) ) ? $_REQUEST ['events']:'';
		
		$args = array('scope'=>$scope, 'limit'=>$limit, 'offset' => $offset, 'order'=>$order, 'search'=>$search );
		
		if(	!current_user_can('edit_others_events') ){
			$args['owner'] = get_current_user_id();
		}
		//Figure out what status to search for
		$args['status'] = ( isset($_REQUEST['status']) && is_numeric($_REQUEST['status']) ) ? $_REQUEST['status'] : false;
		
		$events = EM_Events::get( $args );
		unset($args['offset']);
		$args['limit'] = 0;
		$events_count = EM_Events::count ( $args );
		$pending_count = EM_Events::count( array('status'=>0, 'scope'=>$scope) );
		$approved_count = EM_Events::count( array('status'=> 1, 'scope'=>$scope) );
		$total_count = EM_Events::count( array('status'=> false, 'scope'=>$scope) );
		
		$use_events_end = get_option('dbem_use_event_end');
		echo $EM_Notices; 
		?>
		<div class="wrap">
			<div id="icon-events" class="icon32"><br />
			</div>
			<h2>	
				<?php echo $scope_names[$scope]; ?>
	 	 		<a href="admin.php?page=events-manager-event" class="button add-new-h2"><?php _e('Add New','dbem'); ?></a>
	 	 	</h2>
			<?php	
				$link = array ();
				$link ['past'] = "<a href='" . get_bloginfo ( 'wpurl' ) . "/wp-admin/admin.php?page=events-manager&amp;scope=past&amp;order=desc'>" . __ ( 'Past events', 'dbem' ) . "</a>";
				$link ['all'] = " <a href='" . get_bloginfo ( 'wpurl' ) . "/wp-admin/admin.php?page=events-manager&amp;scope=all&amp;order=desc'>" . __ ( 'All events', 'dbem' ) . "</a>";
				$link ['future'] = "  <a href='" . get_bloginfo ( 'wpurl' ) . "/wp-admin/admin.php?page=events-manager&amp;scope=future'>" . __ ( 'Future events', 'dbem' ) . "</a>";
			?> 
			<?php if ( !empty($_REQUEST['error']) ) : ?>
			<div id='message' class='error'>
				<p><?php echo $_REQUEST['error']; ?></p>
			</div>
			<?php endif; ?>
			<?php if ( !empty($_REQUEST['message']) ) : ?>
			<div id='message' class='updated fade'>
				<p><?php echo $_REQUEST['message']; ?></p>
			</div>
			<?php endif; ?>
			<form id="posts-filter" action="" method="get"><input type='hidden' name='page' value='events-manager' />
				<ul class="subsubsub">
					<li><a href='<?php bloginfo ( 'wpurl' )?>/wp-admin/admin.php?page=events-manager&amp;scope=<?php echo $scope; ?>' <?php echo ( !isset($_REQUEST['status']) ) ? 'class="current"':''; ?>><?php _e ( 'Total', 'dbem' ); ?> <span class="count">(<?php echo $total_count; ?>)</span></a></li>
					<?php if( current_user_can('publish_events') ): ?>
					<li>| <a href='<?php bloginfo ( 'wpurl' )?>/wp-admin/admin.php?page=events-manager&amp;scope=<?php echo $scope; ?>&amp;status=1' <?php echo ( isset($_REQUEST['status']) && $_REQUEST['status']=='1' ) ? 'class="current"':''; ?>><?php _e ( 'Approved', 'dbem' ); ?> <span class="count">(<?php echo $approved_count; ?>)</span></a></li>
					<li>| <a href='<?php bloginfo ( 'wpurl' )?>/wp-admin/admin.php?page=events-manager&amp;scope=<?php echo $scope; ?>&amp;status=0' <?php echo ( isset($_REQUEST['status']) && $_REQUEST['status']=='0' ) ? 'class="current"':''; ?>><?php _e ( 'Pending', 'dbem' ); ?> <span class="count">(<?php echo $pending_count; ?>)</span></a></li>
					<?php endif; ?>
				</ul>
				<p class="search-box">
					<label class="screen-reader-text" for="post-search-input"><?php _e('Search Events','dbem'); ?>:</label>
					<input type="text" id="post-search-input" name="em_search" value="<?php echo (!empty($_REQUEST['em_search'])) ? $_REQUEST['em_search']:''; ?>" />
					<input type="submit" value="<?php _e('Search Events','dbem'); ?>" class="button" />
				</p>			
				<div class="tablenav">
				
					<div class="alignleft actions">
						<select name="action">
							<option value="-1" selected="selected"><?php _e ( 'Bulk Actions' ); ?></option>
							<option value="event_delete"><?php _e ( 'Delete selected','dbem' ); ?></option>
						</select> 
						<input type="submit" value="<?php _e ( 'Apply' ); ?>" name="doaction2" id="doaction2" class="button-secondary action" /> 
						<select name="scope">
							<?php
							foreach ( $scope_names as $key => $value ) {
								$selected = "";
								if ($key == $scope)
									$selected = "selected='selected'";
								echo "<option value='$key' $selected>$value</option>  ";
							}
							?>
						</select> 
						<input id="post-query-submit" class="button-secondary" type="submit" value="<?php _e ( 'Filter' )?>" />
					</div>
					<!--
					<div class="view-switch">
						<a href="/wp-admin/edit.php?mode=list"><img class="current" id="view-switch-list" src="http://wordpress.lan/wp-includes/images/blank.gif" width="20" height="20" title="List View" alt="List View" name="view-switch-list" /></a> <a href="/wp-admin/edit.php?mode=excerpt"><img id="view-switch-excerpt" src="http://wordpress.lan/wp-includes/images/blank.gif" width="20" height="20" title="Excerpt View" alt="Excerpt View" name="view-switch-excerpt" /></a>
					</div>
					-->
					<?php
					if ( $events_count >= $limit ) {
						$events_nav = em_admin_paginate( $events_count, $limit, $page);
						echo $events_nav;
					}
					?>
					<br class="clear" />
				</div>
					
				<?php
				if (empty ( $events )) {
					// TODO localize
					_e ( 'no events','dbem' );
				} else {
				?>
						
				<table class="widefat events-table">
					<thead>
						<tr>
							<th class='manage-column column-cb check-column' scope='col'>
								<input class='select-all' type="checkbox" value='1' />
							</th>
							<th><?php _e ( 'Name', 'dbem' ); ?></th>
							<th>&nbsp;</th>
							<th><?php _e ( 'Location', 'dbem' ); ?></th>
							<th colspan="2"><?php _e ( 'Date and time', 'dbem' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php 
						$rowno = 0;
						$event_count = 0;
						foreach ( $events as $event ) {
							/* @var $event EM_Event */
/* 							if( ($rowno < $limit || empty($limit)) && ($event_count >= $offset || $offset === 0) ) { */
								$rowno++;
								$class = ($rowno % 2) ? 'alternate' : '';
								// FIXME set to american
								$localised_start_date = date_i18n('D d M Y', $event->start);
								$localised_end_date = date_i18n('D d M Y', $event->end);
								$style = "";
								$today = date ( "Y-m-d" );
								$location_summary = "<b>" . $event->location->name . "</b><br/>" . $event->location->address . " - " . $event->location->town;
								
								if ($event->start_date < $today && $event->end_date < $today){
									$class .= " past";
								}
								//Check pending approval events
								if ( !$event->status ){
									$class .= " pending";
								}					
								?>
								<tr class="event <?php echo trim($class); ?>" <?php echo $style; ?> id="event_<?php echo $event->id ?>">
									<td>
										<input type='checkbox' class='row-selector' value='<?php echo $event->id; ?>' name='events[]' />
									</td>
									<td>
										<strong>
											<a class="row-title" href="<?php bloginfo ( 'wpurl' )?>/wp-admin/admin.php?page=events-manager-event&amp;event_id=<?php echo $event->id ?>&amp;scope=<?php echo $scope ?>&amp;pno=<?php echo $page ?>"><?php echo ($event->name); ?></a>
										</strong>
										<?php 
										if( get_option('dbem_rsvp_enabled') == 1 && $event->rsvp == 1 ){
											?>
											<br/>
											<a href="<?php bloginfo ( 'wpurl' )?>/wp-admin/admin.php?page=events-manager-bookings&amp;event_id=<?php echo $event->id ?>"><?php echo __("Bookings",'dbem'); ?></a> &ndash;
											<?php _e("Booked",'dbem'); ?>: <?php echo $event->get_bookings()->get_booked_spaces()."/".$event->get_spaces(); ?>
											<?php if( get_option('dbem_bookings_approval') == 1 ): ?>
												| <?php _e("Pending",'dbem') ?>: <?php echo $event->get_bookings()->get_pending_spaces(); ?>
											<?php endif;
										}
										?>
										<div class="row-actions">
											<span class="trash"><a href="<?php bloginfo ( 'wpurl' )?>/wp-admin/admin.php?page=events-manager&amp;action=event_delete&amp;event_id=<?php echo $event->id ?>&amp;scope=<?php echo $scope ?>&amp;pno=<?php echo $page ?>" class="em-event-delete"><?php _e('Delete','dbem'); ?></a></span>
											<?php if( !$event->status && current_user_can('publish_events') ): ?>
											| <a href="<?php bloginfo ( 'wpurl' )?>/wp-admin/admin.php?page=events-manager&amp;action=event_approve&amp;event_id=<?php echo $event->id ?>&amp;scope=<?php echo $scope ?>&amp;pno=<?php echo $page ?>" class="em-event-approve" style="color:green"><?php _e('Approve','dbem'); ?></a>
											<?php endif; ?>
										</div>
									</td>
									<td>
										<a href="<?php bloginfo ( 'wpurl' )?>/wp-admin/admin.php?page=events-manager-event&amp;action=event_duplicate&amp;event_id=<?php echo $event->id; ?>&amp;scope=<?php echo $scope ?>&amp;pno=<?php echo $page ?>" title="<?php _e ( 'Duplicate this event', 'dbem' ); ?>">
											<strong>+</strong>
										</a>
									</td>
									<td>
										<?php echo $location_summary; ?>
									</td>
							
									<td>
										<?php echo $localised_start_date; ?>
										<?php echo ($localised_end_date != $localised_start_date) ? " - $localised_end_date":'' ?>
										<br />
										<?php
											//TODO Should 00:00 - 00:00 be treated as an all day event? 
											echo substr ( $event->start_time, 0, 5 ) . " - " . substr ( $event->end_time, 0, 5 ); 
										?>
									</td>
									<td>
										<?php 
										if ( $event->is_recurrence() ) {
											$recurrence_delete_confirm = __('WARNING! You will delete ALL recurrences of this event, including booking history associated with any event in this recurrence. To keep booking information, go to the relevant single event and save it to detach it from this recurrence series.','dbem');
											?>
											<strong>
											<?php echo $event->get_recurrence_description(); ?> <br />
											<a href="<?php bloginfo ( 'wpurl' )?>/wp-admin/admin.php?page=events-manager-event&amp;event_id=<?php echo $event->recurrence_id ?>&amp;scope=<?php echo $scope ?>&amp;pno=<?php echo $page ?>"><?php _e ( 'Reschedule', 'dbem' ); ?></a> |
											<span class="trash"><a href="<?php bloginfo ( 'wpurl' )?>/wp-admin/admin.php?page=events-manager&amp;action=event_delete&amp;event_id=<?php echo $event->recurrence_id ?>&amp;scope=<?php echo $scope ?>&amp;pno=<?php echo $page ?>" class="em-event-rec-delete" onclick ="if( !confirm('<?php echo $recurrence_delete_confirm; ?>') ){ return false; }"><?php _e('Delete','dbem'); ?></a></span>
											</strong>
											<?php
										}
										?>
									</td>
								</tr>
								<?php
/* 							} */
							$event_count++;
						}
						?>
					</tbody>
				</table>  
				<?php
				} // end of table
				?>
				<div class='tablenav'>
					<div class="alignleft actions">
					<br class='clear' />
					</div>
					<?php if ( $events_count >= $limit ) : ?>
					<div class="tablenav-pages">
						<?php
						echo $events_nav;
						?>
					</div>
					<?php endif; ?>
					<br class='clear' />
				</div>
			</form>
		</div>
		<?php
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