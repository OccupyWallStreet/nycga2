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
if( get_option('dbem_events_page_search') && !defined('DOING_AJAX') ){
	em_locate_template('templates/events-search.php',true);
}

//TODO fine tune ajax searches - we have some pagination issues otherwise, due to search querystrings
if( get_option('dbem_events_page_ajax', (defined('EM_AJAX_SEARCH'))) ) echo "<div class='em-events-search-ajax'>";
$events_count = EM_Events::count( apply_filters('em_content_events_args', $args) );
$args['limit'] = get_option('dbem_events_default_limit');
$args['page'] = (!empty($_REQUEST['pno']) && is_numeric($_REQUEST['pno']) )? $_REQUEST['pno'] : 1;
if( $events_count > 0 ){
	//If there's a search, let's change the pagination a little here
	if(!empty($_REQUEST['action']) && $_REQUEST['action'] == 'search_events'){
		$args['pagination'] = false;
	    if(get_option('dbem_event_list_groupby') ){
	        $args['mode'] = get_option('dbem_event_list_groupby');
	        $args['date_format'] = get_option('dbem_event_list_groupby_format');
	        echo em_events_list_grouped($args);
	    }else{
			echo EM_Events::output( $args );
	    }
		//do some custom pagination (if needed/requested)
		if( !empty($args['limit']) && $events_count > $args['limit'] ){
			//Show the pagination links (unless there's less than $limit events), note that we set em_search instead of search to prevent conflicts
			$search_args = array_merge(EM_Events::get_post_search(), array('pno'=>'%PAGE%','action'=>'search_events','search'=>null, 'em_search'=>$args['search'])); 
			$page_link_template = em_add_get_params($_SERVER['REQUEST_URI'], $search_args, false); //don't html encode, so em_paginate does its thing
			echo apply_filters('em_events_output_pagination', em_paginate( $page_link_template, $events_count, $args['limit'], $args['pno']), $page_link_template, $events_count, $args['limit'], $args['pno']);
		}
	}else{
	    if(get_option('dbem_event_list_groupby') ){
	        $args['mode'] = get_option('dbem_event_list_groupby');
	        $args['date_format'] = get_option('dbem_event_list_groupby_format');
	        echo em_events_list_grouped($args);
	    }else{
			echo EM_Events::output( $args );
	    }
	}
}else{
	echo get_option ( 'dbem_no_events_message' );
}
if( get_option('dbem_events_page_ajax', (defined('EM_AJAX_SEARCH'))) ) echo "</div>";