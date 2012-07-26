<?php
//TODO add a shortcode to link for a specific event, e.g. [event id=x]text[/event]

/**
 * Returns the html of an events calendar with events that match given query attributes. Accepts any event query attribute.
 * @param array $atts
 * @return string
 */
function em_get_calendar_shortcode($atts) { 
	$atts = (array) $atts;
	return EM_Calendar::output($atts);
}
add_shortcode('events_calendar', 'em_get_calendar_shortcode');

/**
 * Generates a map of locations that match given query attributes. Accepts any location query attributes. 
 * @param array $args
 * @return string
 */
function em_get_locations_map_shortcode($args){
	$args['width'] = ( !empty($args['width']) ) ? $args['width']:450;
	$args['height'] = ( !empty($args['height']) ) ? $args['height']:300;
	ob_start();
	em_locate_template('templates/map-global.php',true, array('args'=>$args)); 
	return ob_get_clean();
}
add_shortcode('locations_map', 'em_get_locations_map_shortcode');
add_shortcode('locations-map', 'em_get_locations_map_shortcode'); //Depreciate this... confusing for wordpress 

/**
 * Shows a list of events according to given specifications. Accepts any event query attribute.
 * @param array $atts
 * @return string
 */
function em_get_events_list_shortcode($atts, $format='') {
	$atts = (array) $atts;
	$atts['format'] = ($format != '' || empty($atts['format'])) ? $format : $atts['format']; 
	$atts['format'] = html_entity_decode($atts['format']); //shorcode doesn't accept html
	$atts['page'] = ( !empty($atts['page']) && is_numeric($atts['page']) )? $atts['page'] : 1;
	$atts['page'] = ( !empty($_GET['page']) && is_numeric($_GET['page']) )? $_GET['page'] : $atts['page'];
	return EM_Events::output( $atts );
}
add_shortcode ( 'events_list', 'em_get_events_list_shortcode' );

/**
 * Shows a list of events according to given specifications. Accepts any event query attribute.
 * @param array $atts
 * @return string
 */
function em_get_event_shortcode($atts, $format='') {
	$atts = (array) $atts;
	$atts['format'] = ($format != '' || empty($atts['format'])) ? $format : $atts['format']; 
	$atts['format'] = html_entity_decode($atts['format']); //shorcode doesn't accept html
	if( !empty($atts['event']) && is_numeric($atts['event']) ){
		$EM_Event = new EM_Event($atts['event']);
		return ( !empty($atts['format']) ) ? $EM_Event->output($atts['format']) : $EM_Event->output_single();
	}
}
add_shortcode ( 'event', 'em_get_event_shortcode' );

/**
 * Returns list of locations according to given specifications. Accepts any location query attribute.
 */
function em_get_locations_list_shortcode( $atts, $format='' ) {
	$atts = (array) $atts;
	$atts['format'] = ($format != '' || empty($atts['format'])) ? $format : $atts['format']; 
	$atts['format'] = html_entity_decode($atts['format']); //shorcode doesn't accept html
	$atts['page'] = ( !empty($atts['page']) && is_numeric($atts['page']) )? $atts['page'] : 1;
	$atts['page'] = ( !empty($_GET['page']) && is_numeric($_GET['page']) )? $_GET['page'] : $atts['page'];
	return EM_Locations::output( $atts );
}
add_shortcode('locations_list', 'em_get_locations_list_shortcode');

/**
 * Shows a single location according to given specifications. Accepts any event query attribute.
 * @param array $atts
 * @return string
 */
function em_get_location_shortcode($atts, $format='') {
	$atts = (array) $atts;
	$atts['format'] = ($format != '' || empty($atts['format'])) ? $format : $atts['format']; 
	$atts['format'] = html_entity_decode($atts['format']); //shorcode doesn't accept html
	if( !empty($atts['location']) && is_numeric($atts['location']) ){
		$EM_Location = new EM_Location($atts['location']);
		return ( !empty($atts['format']) ) ? $EM_Location->output($atts['format']) : $EM_Location->output_single();
	}
}
add_shortcode ( 'location', 'em_get_location_shortcode' );

/**
 * DO NOT DOCUMENT! This should be replaced with shortcodes events-link and events_uri
 * @param array $atts
 * @return string
 */
function em_get_events_page_shortcode($atts) {
	$atts = shortcode_atts ( array ('justurl' => 0, 'text' => '' ), $atts );
	if($atts['justurl']){
		return EM_URI;
	}else{
		return em_get_link($atts['text']);
	}
}
add_shortcode ( 'events_page', 'em_get_events_page_shortcode' );

/**
 * Shortcode for a link to events page. Default will show events page title in link text, if you use [events_link]text[/events_link] 'text' will be the link text
 * @param array $atts
 * @param string $text
 * @return string
 */
function em_get_link_shortcode($atts, $text='') {
	return em_get_link($text);
}
add_shortcode ( 'events_link', 'em_get_link_shortcode');

/**
 * Returns the uri of the events page only
 * @return string
 */
function em_get_url_shortcode(){
	return EM_URI;
}
add_shortcode ( 'events_url', 'em_get_url_shortcode');

/**
 * CHANGE DOCUMENTATION! if you just want the url you should use shortcode events_rss_uri
 * @param array $atts
 * @return string
 */
function em_get_rss_link_shortcode($atts) {
	$atts = shortcode_atts ( array ('justurl' => 0, 'text' => 'RSS' ), $atts );
	if($atts['justurl']){
		return EM_RSS_URI;
	}else{
		return em_get_rss_link($atts['text']);
	}
}
add_shortcode ( 'events_rss_link', 'em_get_rss_link_shortcode' );

/**
 * Returns the uri of the events rss page only, takes no attributes.
 * @return string
 */
function em_get_rss_url_shortcode(){
	return EM_RSS_URI;
}
add_shortcode ( 'events_rss_url', 'em_get_rss_url_shortcode');

/**
 * Creates a form to submit events with
 * @param array $atts
 * @return string
 */
function em_get_event_form_shortcode( $args = array() ){
	ob_start();
	em_get_event_form( $args );
	return ob_get_clean();
}
add_shortcode ( 'event_form', 'em_get_event_form_shortcode');