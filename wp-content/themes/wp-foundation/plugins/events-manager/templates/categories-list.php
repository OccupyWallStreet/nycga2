<?php
/*
 * Default Categories List Template
 * This page displays a list of locations, called during the em_content() if this is an events list page.
 * You can override the default display settings pages by copying this file to yourthemefolder/plugins/events-manager/templates/ and modifying it however you need.
 * You can display locations (or whatever) however you wish, there are a few variables made available to you:
 * 
 * $args - the args passed onto EM_Locations::output()
 * 
 */ 
$args = apply_filters('em_content_categories_args', $args);
$categories = EM_Categories::get( $args );	
$args['limit'] = get_option('dbem_categories_default_limit');
$args['page'] = (!empty($_REQUEST['pno']) && is_numeric($_REQUEST['pno']) )? $_REQUEST['pno'] : 1;			
if( count($categories) > 0 ){
	echo EM_Categories::output( $categories, $args );
}else{
	echo get_option ( 'dbem_no_categories_message' );
}