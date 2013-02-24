<?php
/*
 * Default Calendar day
 * This page displays a list of events or single event for a specific calendar day, called during the em_content() if this is an calendar day page.
 * You can override the default display settings pages by copying this file to yourthemefolder/plugins/events-manager/templates/ and modifying it however you need.
 * You can display events however you wish, there are a few variables made available to you:
 * 
 * $args - the args passed onto EM_Events::output()
 * 
 */ 
$args['scope'] = $_REQUEST['calendar_day'];
$page = ( !empty($_REQUEST['pno']) && is_numeric($_REQUEST['pno']) )? $_REQUEST['pno'] : 1;
$events_count = EM_Events::count( apply_filters('em_content_calendar_day_args', $args) ); //Get events first, so we know how many there are in advance
if ( $events_count > 1 || $page > 1 || get_option('dbem_display_calendar_day_single') == 1 ) {
	$args['limit'] = get_option('dbem_events_default_limit');
	$args['offset'] = $args['limit'] * ($page-1);
	echo EM_Events::output(apply_filters('em_content_calendar_day_output_args', $args) );
} elseif( $events_count == 1 ) {
    $args['format'] = get_option('dbem_single_event_format');
	echo EM_Events::output(apply_filters('em_content_calendar_day_output_args', $args));
} else {
	echo get_option('dbem_no_events_message');
}