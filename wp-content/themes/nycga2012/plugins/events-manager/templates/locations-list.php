<?php
/*
 * Default Location List Template
 * This page displays a list of locations, called during the em_content() if this is an events list page.
 * You can override the default display settings pages by copying this file to yourthemefolder/plugins/events-manager/templates/ and modifying it however you need.
 * You can display locations (or whatever) however you wish, there are a few variables made available to you:
 * 
 * $args - the args passed onto EM_Locations::output()
 * 
 */ 
$args['limit'] = get_option('dbem_locations_default_limit');
$args['page'] = (!empty($_REQUEST['pno']) && is_numeric($_REQUEST['pno']) )? $_REQUEST['pno'] : 1;
$args['offset'] = ($args['page']-1)*$args['limit'];
$args['pagination'] = true;
echo EM_Locations::output(apply_filters('em_content_locations_args', $args));
	