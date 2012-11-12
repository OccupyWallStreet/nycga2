<?php
/*
 * Default Events List Template
 * This page displays a list of events, called during the em_content() if this is an events list page.
 * You can override the default display settings pages by copying this file to yourthemefolder/plugins/events-manager/templates/ and modifying it however you need.
 * You can display events however you wish, there are a few variables made available to you:
 * 
 * $args - the args passed onto EM_Events::output()
 * 
 */ 

$events = EM_Events::get( apply_filters('em_content_events_args', $args) );
$args['limit'] = get_option('dbem_events_default_limit'); //since we are passing this info to an output function or template, we should get all the events first
$args['page'] = (!empty($_REQUEST['page']) && is_numeric($_REQUEST['page']) )? $_REQUEST['page'] : 1;
$events_count = count($events);

if( get_option('dbem_events_page_search') ){
	em_locate_template('templates/events-search.php',true);
}	
if( $events_count > 0 ){
	//If there's a search, let's change the pagination a little here
	if(!empty($_REQUEST['_wpnonce']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'search_events')){
		$args['pagination'] = false;
		echo EM_Events::output( $events, $args );
		//do some custom pagination (if needed/requested)
		if( !empty($args['limit']) && $events_count > $args['limit'] ){
			//Show the pagination links (unless there's less than $limit events)
			$search_args = EM_Events::get_post_search() + array('page'=>'%PAGE%','_wpnonce'=>$_REQUEST['_wpnonce']);
			$page_link_template = preg_replace('/(&|\?)page=\d+/i','',$_SERVER['REQUEST_URI']);
			$page_link_template = em_add_get_params($page_link_template, $search_args);
			echo apply_filters('em_events_output_pagination', em_paginate( $page_link_template, $events_count, $args['limit'], $args['page']), $page_link_template, $events_count, $args['limit'], $args['page']);
		}
	}else{
		echo EM_Events::output( $events, $args );
	}
}else{
	echo get_option ( 'dbem_no_events_message' );
}