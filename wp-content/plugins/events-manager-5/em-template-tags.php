<?php
/*
 * Template Tags
 * If you know what you're doing, you're probably better off using the EM Objects directly.
 */

/* 
 * ---------------------------------------------------------------------
 * Displaying Functions - Displays Lists, Page Links/URLs, etc.
 * ---------------------------------------------------------------------
 */

/**
 * Returns a html list of events filtered by the array or query-string of arguments supplied. 
 * @param array|string $args
 * @return string
 */
function em_get_events( $args = array() ){
	if ( is_string($args) && strpos ( $args, "=" )) {
		// allows the use of arguments without breaking the legacy code
		$defaults = EM_Events::get_default_search();		
		$args = wp_parse_args ( $args, $defaults );
	}
	return EM_Events::output( $args );
}
/**
 * Prints out a list of events, takes same arguments as em_get_events.
 * @param array|string $args
 * @uses em_get_events()
 */
function em_events( $args = array() ){ echo em_get_events($args); }

/**
 * Returns a html list of locations filtered by the array or query-string of arguments supplied. 
 * @param array|string $args
 * @return string
 */
function em_get_locations( $args = array() ){
	if (strpos ( $args, "=" )) {
		// allows the use of arguments without breaking the legacy code
		$defaults = EM_Locations::get_default_search();		
		$args = wp_parse_args ( $args, $defaults );
	}
	return EM_Locations::output( $args );
}
/**
 * Prints out a list of locations, takes same arguments as em_get_locations.
 * @param array|string $args
 * @uses em_get_locations()
 */
function em_locations( $args = array() ){ echo em_get_locations($args); }

/**
 * Returns an html calendar of events filtered by the array or query-string of arguments supplied. 
 * @param array|string $args
 * @return string
 */
function em_get_calendar( $args = array() ){
	if ( !is_array($args) && strpos ( $args, "=" )) {
		// allows the use of arguments without breaking the legacy code
		$defaults = EM_Calendar::get_default_search();		
		$args = wp_parse_args ( $args, $defaults );
	}
	return EM_Calendar::output($args);
}
/**
 * Prints out an html calendar, takes same arguments as em_get_calendar.
 * @param array|string $args
 * @uses em_get_calendar()
 */
function em_calendar( $args = array() ){ echo em_get_calendar($args); }



/**
 * Generate a grouped list of events by year, month, week or day.
 * @since 4.213
 * @param array $args
 * @return string
 */
function em_get_events_list_grouped($args){
	//Reset some args to include pagination for if pagination is requested.
	$args['limit'] = (!empty($args['limit']) && is_numeric($args['limit']) )? $args['limit'] : false;
	$args['page'] = (!empty($args['page']) && is_numeric($args['page']) )? $args['page'] : 1;
	$args['page'] = (!empty($_REQUEST['pno']) && is_numeric($_REQUEST['pno']) )? $_REQUEST['pno'] : $args['page'];
	$args['offset'] = ($args['page']-1) * $args['limit'];
	$args['orderby'] = 'event_start_date,event_start_time,event_name'; // must override this to display events in right cronology.
	//Reset some vars for counting events and displaying set arrays of events
	$atts = (array) $args;
	$atts['pagination'] = false;
	$atts['limit'] = false;
	$atts['page'] = false;
	$atts['offset'] = false;
	//decide what form of dates to show
	$EM_Events = EM_Events::get($args);
	$events_count = EM_Events::count($atts);
	ob_start();
	switch ( $args['mode'] ){
		case 'yearly':
			//go through the events and put them into a monthly array
			$format = (!empty($args['date_format'])) ? $args['date_format']:'Y';
			$events_dates = array();
			foreach($EM_Events as $EM_Event){
				$events_dates[date_i18n($format,$EM_Event->start)][] = $EM_Event;
			}
			foreach ($events_dates as $year => $events){
				echo '<h2>'.$year.'</h2>';
				echo EM_Events::output($events, $atts);
			}
			break;
		case 'monthly':
			//go through the events and put them into a monthly array
			$format = (!empty($args['date_format'])) ? $args['date_format']:'M Y';
			$events_dates = array();
			foreach($EM_Events as $EM_Event){
				$events_dates[date_i18n($format, $EM_Event->start)][] = $EM_Event;
			}
			foreach ($events_dates as $month => $events){
				echo '<h2>'.$month.'</h2>';
				echo EM_Events::output($events, $atts);
			}
			break;
		case 'weekly':
			$format = (!empty($args['date_format'])) ? $args['date_format']:get_option('date_format');
			$events_dates = array();
			foreach($EM_Events as $EM_Event){
	   			$start_of_week = get_option('start_of_week');
				$day_of_week = date('w',$EM_Event->start);
				$day_of_week = date('w',$EM_Event->start);
				$offset = $day_of_week - $start_of_week;
				if($offset<0){ $offset += 7; }
				$offset = $offset * 60*60*24; //days in seconds
				$start_day = strtotime($EM_Event->start_date);
				$events_dates[$start_day - $offset][] = $EM_Event;
			}
			foreach ($events_dates as $event_day_ts => $events){
				echo '<h2>'.date_i18n($format,$event_day_ts).' - '.date_i18n($format,$event_day_ts+(60*60*24*6)).'</h2>';
				echo EM_Events::output($events, $atts);
			}
			break;
		default: //daily
			//go through the events and put them into a daily array
			$format = (!empty($args['date_format'])) ? $args['date_format']:get_option('date_format');
			$events_dates = array();
			foreach($EM_Events as $EM_Event){
				$events_dates[strtotime($EM_Event->start_date)][] = $EM_Event;
			}
			foreach ($events_dates as $event_day_ts => $events){
				echo '<h2>'.date_i18n($format,$event_day_ts).'</h2>';
				echo EM_Events::output($events, $atts);
			}
			break;
	}
	if( !empty($args['limit']) && $events_count > $args['limit'] && (!empty($args['pagination']) || !isset($args['pagination'])) ){
		//Show the pagination links (unless there's less than $limit events)
		$page_link_template = add_query_arg(array('pno'=>'%PAGE%'));
		echo em_paginate( $page_link_template, $events_count, $args['limit'], $args['page']);
	}
	return ob_get_clean();
}
/**
 * Print a grouped list of events by year, month, week or day.
 * @since 4.213
 * @param array $args
 * @param string $format
 * @return string
 */
function em_events_list_grouped($args, $format=''){ echo em_get_events_list_grouped($args, $format); }

/**
 * Creates an html link to the events page.
 * @param string $text
 * @return string
 */
function em_get_link( $text = '' ) {
	$text = ($text == '') ? get_option ( "dbem_events_page_title" ) : $text;
	$text = ($text == '') ? __('Events','dbem') : $text; //In case options aren't there....
	return '<a href="'.esc_url(EM_URI).'" title="'.esc_attr($text).'">'.esc_html($text).'</a>';
}
/**
 * Prints the result of em_get_link()
 * @param string $text
 * @uses em_get_link()
 */
function em_link($text = ''){ echo em_get_link($text); }

/**
 * Creates an html link to the RSS feed
 * @param string $text
 * @return string
 */
function em_get_rss_link($text = "RSS") {
	$text = ($text == '') ? 'RSS' : $text;
	return '<a href="'.esc_url(EM_RSS_URI).'">'.esc_html($text).'</a>';
}
/**
 * Prints the result of em_get_rss_link()
 * @param string $text
 * @uses em_get_rss_link()
 */
function em_rss_link($text = "RSS"){ echo em_get_rss_link($text); }

/* 
 * ---------------------------------------------------------------------
 * User Interfaces - Forms, Tables etc.
 * ---------------------------------------------------------------------
 */

//Event Forms
/**
 * Outputs the event submission form for guests and members.
 * @param array $args
 */
function em_event_form($args = array()){
	global $EM_Event;
	if( !is_user_logged_in() && get_option('dbem_events_anonymous_submissions') && em_locate_template('forms/event-editor-guest.php') ){
		em_locate_template('forms/event-editor-guest.php',true, array('args'=>$args));
	}else{
	    if( !empty($_REQUEST['success']) ){
	    	$EM_Event = new EM_Event(); //reset the event
	    }
		if( empty($EM_Event->event_id) ){
			$EM_Event = ( is_object($EM_Event) && get_class($EM_Event) == 'EM_Event') ? $EM_Event : new EM_Event();
			//Give a default location & category
			$default_cat = get_option('dbem_default_category');
			$default_loc = get_option('dbem_default_location');
			if( is_numeric($default_cat) && $default_cat > 0 && !empty($EM_Event->get_categories->categories) ){
				$EM_Category = new EM_Category($default_cat);
				$EM_Event->get_categories()->categories[] = $EM_Category;
			}
			if( is_numeric($default_loc) && $default_loc > 0 && ( empty($EM_Event->get_location()->location_id) && empty($EM_Event->get_location()->location_name) && empty($EM_Event->get_location()->location_address) && empty($EM_Event->get_location()->location_town) ) ){
				$EM_Event->location_id = $default_loc;
				$EM_Event->location = new EM_Location($default_loc);
			}
		}
		em_locate_template('forms/event-editor.php',true, array('args'=>$args));
	}
}
/**
 * Retreives the event submission form for guests and members.
 * @param array $args
 */
function em_get_event_form( $args = array() ){
	ob_start();
	em_event_form($args);
	return ob_get_clean();
}

/**
 * Outputs table of events belonging to user
 * @param array $args
 */
function em_events_admin($args = array()){
	global $EM_Event, $bp;
	if( is_user_logged_in() && current_user_can('edit_events') ){
		if( !empty($_GET['action']) && $_GET['action']=='edit' ){
			if( empty($_REQUEST['redirect_to']) ){
				$_REQUEST['redirect_to'] = em_add_get_params($_SERVER['REQUEST_URI'], array('action'=>null, 'event_id'=>null));
			}
			em_event_form();
		}else{
			$limit = ( !empty($_REQUEST['limit']) ) ? $_REQUEST['limit'] : 20;//Default limit
			$page = ( !empty($_REQUEST['pno']) ) ? $_REQUEST['pno']:1;
			$offset = ( $page > 1 ) ? ($page-1)*$limit : 0;
			$order = ( !empty($_REQUEST ['order']) ) ? $_REQUEST ['order']:'ASC';
			$scope_names = em_get_scopes();
			$scope = ( !empty($_REQUEST ['scope']) && array_key_exists($_REQUEST ['scope'], $scope_names) ) ? $_REQUEST ['scope']:'future';
			if( array_key_exists('status', $_REQUEST) ){
				$status = ($_REQUEST['status']) ? 1:0;
			}else{
				$status = false;
			}
			$search = ( !empty($_REQUEST['em_search']) ) ? $_REQUEST['em_search']:'';
			$args = array( 'scope' => $scope, 'order' => $order, 'search' => $search, 'owner' => get_current_user_id(),'status' => $status);
			$events_count = EM_Events::count( $args ); //count events without limits for pagination
			$args['limit'] = $limit;
			$args['offset'] = $offset;
			$EM_Events = EM_Events::get( $args );
			if($scope != 'future'){
				$future_count = EM_Events::count( array('status'=>1, 'owner' =>get_current_user_id(), 'scope' => 'future'));
			}else{
				$future_count = $events_count;
			}
			$pending_count = EM_Events::count( array('status'=>0, 'owner' =>get_current_user_id(), 'scope' => 'all') );
			em_locate_template('tables/events.php',true, array(
				'args'=>$args, 
				'EM_Events'=>$EM_Events, 
				'events_count'=>$events_count, 
				'future_count'=>$future_count,
				'pending_count'=>$pending_count,
				'page' => $page,
				'limit' => $limit,
				'offset' => $offset,
				'show_add_new' => true
			));
		}
	}elseif( !is_user_logged_in() && get_option('dbem_events_anonymous_submissions') ){
		em_event_form($args);
	}else{
		echo apply_filters('em_event_submission_login', __("You must log in to view and manage your events.",'dbem'));
	}
}
/**
 * Retreives table of events belonging to user
 * @param array $args
 */
function em_get_events_admin( $args = array() ){
	ob_start();
	em_events_admin($args);
	return ob_get_clean();
}

/**
 * Outputs the event search form.
 * @param array $args
 */
function em_event_search_form($args = array()){
	em_locate_template('templates/events-search.php',true, array('args'=>$args));
}
/**
 * Retreives the event search form.
 * @param array $args
 */
function em_get_event_search_form( $args = array() ){
	ob_start();
	em_event_search_form($args);
	return ob_get_clean();
}

//Location Forms
/**
 * Outputs the location submission form for guests and members.
 * @param array $args
 */
function em_location_form($args = array()){
	global $EM_Location;
	$EM_Location = ( is_object($EM_Location) && get_class($EM_Location) == 'EM_Location') ? $EM_Location : new EM_Location();
	em_locate_template('forms/location-editor.php',true);
}
/**
 * Retreives the location submission form for guests and members.
 * @param array $args
 */
function em_get_location_form( $args = array() ){
	ob_start();
	em_location_form($args);
	return ob_get_clean();
}

/**
 * Outputs table of locations belonging to user
 * @param array $args
 */
function em_locations_admin($args = array()){
	global $EM_Location;
	if( is_user_logged_in() && current_user_can('edit_locations') ){
		if( !empty($_GET['action']) && $_GET['action']=='edit' ){
			if( empty($_REQUEST['redirect_to']) ){
				$_REQUEST['redirect_to'] = em_add_get_params($_SERVER['REQUEST_URI'], array('action'=>null, 'location_id'=>null));
			}
			em_location_form();
		}else{
			$url = empty($url) ? $_SERVER['REQUEST_URI']:$url; //url to this page
			$limit = ( !empty($_REQUEST['limit']) ) ? $_REQUEST['limit'] : 20;//Default limit
			$page = ( !empty($_REQUEST['pno']) ) ? $_REQUEST['pno']:1;
			$offset = ( $page > 1 ) ? ($page-1)*$limit : 0;
			$args = array('limit'=>$limit, 'offset'=>$offset, 'status'=>false, 'blog'=>false);
			if( !empty($_REQUEST['view']) && $_REQUEST['view'] == 'others' && current_user_can('read_others_locations') ){
				$locations = EM_Locations::get($args);
				$locations_count = EM_Locations::count(array('status'=>false, 'blog'=>false, 'owner'=>false));
			}else{
				$locations = EM_Locations::get( array_merge($args, array('owner'=>get_current_user_id())) );
				$locations_count = EM_Locations::count(array('status'=>false, 'blog'=>false, 'owner'=>get_current_user_id()));
			}
			$locations_mine_count = EM_Locations::count( array('owner'=>get_current_user_id(), 'blog'=>false, 'status'=>false) );
			$locations_all_count = current_user_can('read_others_locations') ? EM_Locations::count(array('blog'=>false, 'status'=>false)):0;
			em_locate_template('tables/locations.php',true, array(
				'args'=>$args, 
				'locations'=>$locations, 
				'locations_count'=>$locations_count, 
				'locations_mine_count'=>$locations_mine_count,
				'locations_all_count'=>$locations_all_count,
				'url' => $url,
				'page' => $page,
				'limit' => $limit,
				'offset' => $offset,
				'show_add_new' => true
			));
		}
	}else{
		echo __("You must log in to view and manage your locations.",'dbem');
	}
}
/**
 * Retreives table of locations belonging to user
 * @param array $args
 */
function em_get_locations_admin( $args = array() ){
	ob_start();
	em_locations_admin($args);
	return ob_get_clean();
}

//Bookings Pages
function em_bookings_admin(){
	if( is_user_logged_in() && current_user_can('manage_bookings') ){
		em_locate_template('buddypress/my-bookings.php', true);
	}else{
		echo __("You must log in to view and manage your bookings.",'dbem');
	}
}
function em_get_bookings_admin(){
	ob_start();
	em_bookings_admin();
	return ob_get_clean();
}

function em_my_bookings(){
	em_locate_template('templates/my-bookings.php', true);
}
function em_get_my_bookings(){
	ob_start();
	em_my_bookings();
	return ob_get_clean();	
}

/* 
 * ---------------------------------------------------------------------
 * Conditionals - Yes/No functions
 * ---------------------------------------------------------------------
 */

/**
 * Returns true if there are any events that exist in the given scope (default is future events).
 * @param string $scope
 * @return boolean
 */
function em_are_events_available($scope = "future") {
	$scope = ($scope == "") ? "future":$scope;
	$events = EM_Events::get( array('limit'=>1, 'scope'=>$scope) );	
	return ( count($events) > 0 );
}

/**
 * Returns true if the page is the events page. this is now only an events page, before v4.0.83 this would be true for any multiple page (e.g. locations) 
 * @return boolean
 */
function em_is_events_page() {
	global $post;
	return em_get_page_type() == 'events';
}

/**
 * Is this a a single event page?
 * @return boolean
 */
function em_is_event_page(){
	return em_get_page_type() == 'event';
}


/**
 * Is this a a single calendar day page?
 * @return boolean
 */
function em_is_calendar_day_page(){
	return em_get_page_type() == 'calendar_day';
}

/**
 * Is this a a single category page?
 * @return boolean
 */
function em_is_category_page(){
	return em_get_page_type() == 'category';
}
/**
 * Is this a categories list page?
 * @return boolean
 */
function em_is_categories_page(){
	return em_get_page_type() == 'categories';
}

/**
 * Is this a a single location page?
 * @return boolean
 */
function em_is_location_page(){
	return em_get_page_type() == 'location';
}
/**
 * Is this a locations list page?
 * @return boolean
 */
function em_is_locations_page(){
	return em_get_page_type() == 'locations';
}

/**
 * Is this my bookings page?
 * @return boolean
 */
function em_is_my_bookings_page(){
	return em_get_page_type() == 'my_bookings';
}

/**
 * Returns true if this is a single events page and the event is RSVPable
 * @return boolean
 */
function em_is_event_rsvpable() {
	//We assume that we're on a single event (or recurring event) page here, so $EM_Event must be loaded
	global $EM_Event;
	return ( em_is_event_page() && $EM_Event->rsvp );
}
?>