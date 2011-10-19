<?php
/*
 * Template Tags
 * These template tags were used up until EM 2.2 they have been modified to use the new OOP structure
 * of EM, but still provide the same values as before for backward compatability.
 * If you'd like to port over to the new template functions, check out the tag you want and see how we did it (or view the new docs)
 */

/**
 * TOTALLY DEPRECIATED (NOT ALTERNATIVE TAG) - Just use EM_Events::output, see below
 * @param unknown_type $limit
 * @param unknown_type $scope
 * @param unknown_type $order
 * @param unknown_type $format
 * @param unknown_type $echo
 * @param unknown_type $category
 * @return unknown_type
 */
function dbem_get_events_list($limit = "10", $scope = "future", $order = "ASC", $format = '', $echo = 1, $category = '') {
	if (strpos ( $limit, "=" )) {
		// allows the use of arguments without breaking the legacy code
		$defaults = EM_Events::get_default_search();		
		$r = wp_parse_args ( $limit, $defaults );
		extract ( $r, EXTR_OVERWRITE );
	}
	$return = EM_Events::output(array('limit'=>$limit, 'scope'=>$scope, 'order'=>$order, 'format'=>$format, 'category'=>$category));
	if( $echo ) echo $return;
	return $return;
}

/**
 * Use constant EM_URI for url and em_get_link($text) for html link  
 * @param unknown_type $justurl
 * @param unknown_type $echo
 * @param unknown_type $text
 * @return string
 */
function dbem_get_events_page($justurl = 0, $echo = 1, $text = '') {
	if (strpos ( $justurl, "=" )) {
		// allows the use of arguments without breaking the legacy code
		$defaults = array ('justurl' => 0, 'text' => '', 'echo' => 1 );
		$r = wp_parse_args ( $justurl, $defaults );
		extract ( $r, EXTR_OVERWRITE );
	}
	
	$page_link = get_permalink ( get_option ( "dbem_events_page" ) );
	if ($justurl) {
		$result = $page_link;
	} else {
		if ($text == '')
			$text = get_option ( "dbem_events_page_title" );
		$result = "<a href='$page_link' title='$text'>$text</a>";
	}
	if ($echo)
		echo $result;
	else
		return $result;

}

/**
 * TOTALLY DEPRECIATED (NOT ALTERNATIVE TAG) - use EM_Calendar::output($args); for now (this will also change though)
 * @param unknown_type $args
 */
function dbem_get_calendar($args=""){
	$defaults = array(
		'full' => 0,
		'month' => '',
		'echo' => 1,
		'long_events' => 0
	); 
	$args_array = wp_parse_args( $args, $defaults );
	$result = EM_Calendar::output($args_array);	
	if ( $args_array['echo'] )
		echo $result;
	else
		return $result;
}

/**
 * 
 * @param unknown_type $justurl
 * @param unknown_type $echo
 * @param unknown_type $text
 * @return string
 */
function dbem_rss_link($justurl = 0, $echo = 1, $text = "RSS") {
	if (strpos ( $justurl, "=" )) {
		// allows the use of arguments without breaking the legacy code
		$defaults = array ('justurl' => 0, 'echo' => 1, 'text' => 'RSS' );
		
		$r = wp_parse_args ( $justurl, $defaults );
		extract ( $r, EXTR_OVERWRITE );
		$justurl = $r ['justurl'];
		$echo = $r ['echo'];
		$text = $r ['text'];
	}
	if ($text == '')
		$text = "RSS";
	$rss_title = get_option ( 'dbem_events_page_title' );
	$url = get_bloginfo ( 'wpurl' ) . "/?dbem_rss=main";
	$link = "<a href='$url'>$text</a>";
	
	if ($justurl)
		$result = $url;
	else
		$result = $link;
	if ($echo)
		echo $result;
	else
		return $result;
}

/*
 * Currently these are location template tags that refer to the main objects.
 * Please replace calls to these with direct object methods. 
 */

function dbem_get_locations($eventful = false) { 
	$EM_Locations = EM_Locations::get(array('eventful'=>$eventful));
	foreach ($EM_Locations as $key => $EM_Location){
		$EM_Locations[$key] = $EM_Location->to_array();
	}
}

function dbem_get_location($location_id) {
	$EM_Location = new EM_Location($location_id);
	return $EM_Location->to_array();
}

/**
 * Find a location with same name, address and town as supplied array
 * @param $location
 * @return array
 */
function dbem_get_identical_location($location) {
	$EM_Location = new EM_Location($location);
	return $EM_Location->load_similar();
}

function dbem_validate_location($location) {
	$EM_Location = new EM_Location($location);
	if ( $EM_Location->validate() ){
		return "OK";
	}else{
		return '<strong>'.__('Ach, some problems here:', 'dbem').'</strong><br /><br />'."\n".implode('<br />', $EM_Location->errors);
	}
}

function dbem_update_location($location) {
	$EM_Location = new EM_Location($location);
	$EM_Location->update();
}   

function dbem_insert_location($location) { 
	$EM_Location = new EM_Location($location);
	$EM_Location->insert();
	return $EM_Location->to_array();
}         

function dbem_location_has_events($location_id) {
	$EM_Location = new EM_Location($location_id);
	return $EM_Location->has_events();
}           

function dbem_upload_location_picture($location) {
	$EM_Location = new EM_Location($location);
	$EM_Location->image_upload();
}    

function dbem_delete_image_files_for_location_id($location_id) {
	$EM_Location = new EM_Location($location_id);
	$EM_Location->image_delete();
}   

function dbem_replace_locations_placeholders($format, $location, $target="html") {
	$EM_Location = new EM_Location($location);
	return $EM_Location->output($format, $target);
}
?>