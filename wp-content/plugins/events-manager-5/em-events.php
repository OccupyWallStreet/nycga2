<?php
/*
 * This file contains the event related hooks in the front end, as well as some event template tags
 */

/**
 * Filters for page content and if an event replaces it with the relevant event data.
 * @param $data
 * @return string
 */
function em_content($page_content) {
	global $post, $wpdb, $wp_query, $EM_Event, $EM_Location, $EM_Category;
	$events_page_id = get_option ( 'dbem_events_page' );
	$locations_page_id = get_option( 'dbem_locations_page' );
	$categories_page_id = get_option( 'dbem_categories_page' );
	$edit_events_page_id = get_option( 'dbem_edit_events_page' );
	$edit_locations_page_id = get_option( 'dbem_edit_locations_page' );
	$edit_bookings_page_id = get_option( 'dbem_edit_bookings_page' );
	$my_bookings_page_id = get_option( 'dbem_my_bookings_page' );
	//general defaults
	$args = array(				
		'owner' => false,
		'pagination' => 1
	);
	if( in_array($post->ID, array($events_page_id, $locations_page_id, $categories_page_id, $edit_bookings_page_id, $edit_events_page_id, $edit_locations_page_id, $my_bookings_page_id)) ){
		$content = apply_filters('em_content_pre', '', $page_content);
		if( empty($content) ){
			ob_start();
			if ( $post->ID == $events_page_id && $events_page_id != 0 ) {
				if ( !empty($_REQUEST['calendar_day']) ) {
					//Events for a specific day
					$args = EM_Events::get_post_search( array_merge($args, $_REQUEST) );
					em_locate_template('templates/calendar-day.php',true, array('args'=>$args));
				}elseif ( $wp_query->get('bookings_page') && empty($my_bookings_page_id)) {
					//Bookings Page
					em_locate_template('templates/my-bookings.php',true);
				}elseif ( is_object($EM_Event)) {
					em_locate_template('templates/event-single.php',true, array('args'=>$args));	
				}else{
					// Multiple events page
					$args['orderby'] = get_option('dbem_events_default_orderby');
					$args['order'] = get_option('dbem_events_default_order');
					if (get_option ( 'dbem_display_calendar_in_events_page' )){
						$args['long_events'] = 1;
						em_locate_template('templates/events-calendar.php',true, array('args'=>$args));
					}else{
						//Intercept search request, if defined
						$args['scope'] = get_option('dbem_events_page_scope');
						if( !empty($_REQUEST['action']) && $_REQUEST['action'] == 'search_events' ){
							$args = EM_Events::get_post_search( array_merge($args, $_REQUEST) );
						}
						em_locate_template('templates/events-list.php', true, array('args'=>$args));
					}
				}
			}elseif( $post->ID == $locations_page_id && $locations_page_id != 0 ){
				$args['orderby'] = get_option('dbem_locations_default_orderby');
				$args['order'] = get_option('dbem_locations_default_order');
				if( EM_MS_GLOBAL && is_object($EM_Location) ){
					em_locate_template('templates/location-single.php',true, array('args'=>$args));
				}else{
					em_locate_template('templates/locations-list.php',true, array('args'=>$args));
				}
			}elseif( $post->ID == $categories_page_id && $categories_page_id != 0 ){
				$args['orderby'] = get_option('dbem_categories_default_orderby');
				$args['order'] = get_option('dbem_categories_default_order');
				em_locate_template('templates/categories-list.php',true, array('args'=>$args));
			}elseif( $post->ID == $edit_events_page_id && $edit_events_page_id != 0 ){
				em_events_admin();
			}elseif( $post->ID == $edit_locations_page_id && $edit_locations_page_id != 0 ){
				em_locations_admin();
			}elseif( $post->ID == $edit_bookings_page_id && $edit_bookings_page_id != 0 ){
				em_bookings_admin();
			}elseif( $post->ID == $my_bookings_page_id && $my_bookings_page_id != 0 ){
				em_my_bookings();
			}
			$content = ob_get_clean();
			//If disable rewrite flag is on, then we need to add a placeholder here
			if( get_option('dbem_disable_title_rewrites') == 1 ){
				$content = str_replace('#_PAGETITLE', em_content_page_title(''),get_option('dbem_title_html')) . $content;
			}
			//Now, we either replace CONTENTS or just replace the whole page
			if( preg_match('/CONTENTS/', $page_content) ){
				$content = str_replace('CONTENTS',$content,$page_content);
			}
			if(get_option('dbem_credits')){
				$content .= '<p style="color:#999; font-size:11px;">Powered by <a href="http://wp-events-plugin.com" style="color:#999;" target="_blank">Events Manager</a></p>';
			}
		}
		return apply_filters('em_content', '<div id="em-wrapper">'.$content.'</div>');
	}
	return $page_content;
}
add_filter('the_content', 'em_content');

/**
 * Filter for titles when on event pages
 * @param $data
 * @return string
 */
function em_content_page_title($original_content) {
	global $EM_Event, $EM_Location, $EM_Category, $wp_query, $post;
	$events_page_id = get_option ( 'dbem_events_page' );
	$locations_page_id = get_option( 'dbem_locations_page' );
	$edit_events_page_id = get_option( 'dbem_edit_events_page' );
	$edit_locations_page_id = get_option( 'dbem_edit_locations_page' );
	$edit_bookings_page_id = get_option( 'dbem_edit_bookings_page' );
	if( !empty($post->ID) && in_array($post->ID, array($events_page_id, $locations_page_id, $edit_events_page_id, $edit_locations_page_id, $edit_bookings_page_id))){
		$content = apply_filters('em_content_page_title_pre', '', $original_content);
		if( empty($content) ){
			if ( $post->ID == $events_page_id ) {
				if ( !empty( $_REQUEST['calendar_day'] ) ) {
					$events = EM_Events::get(array('limit'=>2,'scope'=>$_REQUEST['calendar_day'],'owner'=>false));
					if ( count($events) != 1 || get_option('dbem_display_calendar_day_single') == 1 ) {
						//We only support dates for the calendar day list title, so we do a simple filter for the supplied calendar_day
						$content = get_option ('dbem_list_date_title');
						preg_match_all("/#[A-Za-z0-9]+/", $content, $placeholders);
						foreach($placeholders[0] as $placeholder) {
							// matches all PHP date and time placeholders
							if (preg_match('/^#[dDjlNSwzWFmMntLoYyaABgGhHisueIOPTZcrU]$/', $placeholder)) {
								$content = str_replace($placeholder, mysql2date(ltrim($placeholder, "#"), $_REQUEST['calendar_day']),$content );
							}
						}
					}else{
						$event = array_shift($events);
						$content =  $event->output( get_option('dbem_event_page_title_format') );
					}
				}elseif ( $wp_query->get('bookings_page') ) {
					//Bookings Page
					$content = sprintf(__('My %s','dbem'),__('Bookings','dbem'));
				}elseif ( is_object($EM_Event) && EM_MS_GLOBAL ) {
					// single event page
					if( $EM_Event->status == 1 ){
						$content =  $EM_Event->output ( get_option ( 'dbem_event_page_title_format' ) );
					}else{
						$content = get_option('dbem_events_page_title');
					}
				}else{
					// Multiple events page, leave untouched
					$content =  $original_content;
				}
			}elseif( $post->ID == $locations_page_id ){
				if( EM_MS_GLOBAL && is_object($EM_Location) ){
					$content = $EM_Location->output(get_option( 'dbem_location_page_title_format' ));
				}else{
					$content = $original_content;
				}
			}elseif( $post->ID == $edit_events_page_id ){
				if( !empty($_REQUEST['action']) && $_REQUEST['action'] = 'edit' ){			
					if( is_object($EM_Event) ){
						if($EM_Event->is_recurring()){
							$content = __( "Reschedule Events", 'dbem' )." '{$EM_Event->event_name}'";
						}else{
							$content = __( "Edit Event", 'dbem' ) . " '" . $EM_Event->event_name . "'";
						}
					}else{
						$content = __( 'Add Event', 'dbem' );
					}
				}else{
					$content = $original_content;
				}
			}elseif( $post->ID == $edit_locations_page_id ){
				if( !empty($_REQUEST['action']) && $_REQUEST['action'] = 'edit' ){
					if( empty($EM_Location) || !is_object($EM_Location) ){
						$content = __('Add Location', 'dbem');
					}else{
						$content = __('Edit Location', 'dbem');
					}
				}else{
					$content = $original_content;
				}
			}elseif( $post->ID == $edit_bookings_page_id){ 
				if( is_object($EM_Event) ){
					$content = $EM_Event->name .' - '. $original_content;
				}else{
					$content = $original_content;
				}
			}
			return apply_filters('em_content_page_title', $content);
		}
	}
	return $original_content;
}

function em_content_wp_title($title, $sep = '', $seplocation = ''){
	global $EM_Location, $post;
	$events_page_id = get_option ( 'dbem_events_page' );
	$locations_page_id = get_option( 'dbem_locations_page' );
	$edit_events_page_id = get_option( 'dbem_edit_events_page' );
	$edit_locations_page_id = get_option( 'dbem_edit_locations_page' );
	$edit_bookings_page_id = get_option( 'dbem_edit_bookings_page' );
	if( !empty($post->ID) && $post->ID != $events_page_id && !in_array($post->ID, array($events_page_id, $locations_page_id, $edit_events_page_id, $edit_locations_page_id, $edit_bookings_page_id)) ){ return $title; }
	// Determines position of the separator and direction of the breadcrumb
	$title = em_content_page_title($title);
	$t_sep = '%WP_TITILE_SEP%'; // Temporary separator, for accurate flipping, if necessary
	if ( 'right' == $seplocation ) { // sep on right, so reverse the order
		$title_array = explode( $t_sep, $title );
		$title_array = array_reverse( $title_array );
		$title = implode( " $sep ", $title_array );
	} else {
		$title_array = explode( $t_sep, $title );
		$title = implode( " $sep ", $title_array );
	}
	return $title;
}
add_filter ( 'wp_title', 'em_content_wp_title',100,3 ); //override other plugin SEO due to way EM works.

/**
 * Makes sure we're in "THE Loop", which is determinied by a flag set when the_post() (start) is first called, and when have_posts() (end) returns false.
 * @param string $data
 * @return string
 */
function em_wp_the_title($data){
	//This is set by the loop_start and loop_end actions
	global $post, $wp_query, $EM_Location;
	$events_page_id = get_option ( 'dbem_events_page' );
	$locations_page_id = get_option( 'dbem_locations_page' );
	$edit_events_page_id = get_option( 'dbem_edit_events_page' );
	$edit_locations_page_id = get_option( 'dbem_edit_locations_page' );
	$edit_bookings_page_id = get_option( 'dbem_edit_bookings_page' );
	if( !is_main_query() && !empty($post->ID) && in_array($post->ID, array($events_page_id, $locations_page_id, $edit_events_page_id, $edit_locations_page_id, $edit_bookings_page_id)) ){
		if ( $wp_query->in_the_loop ) {
			return apply_filters('em_wp_the_title', em_content_page_title($data)) ;
		}
	}
	return $data;
}
add_filter ( 'the_title', 'em_wp_the_title',10,1 );


function em_get_page_type(){
	global $EM_Location, $EM_Category, $EM_Event, $wp_query, $post;	
	$events_page_id = get_option ( 'dbem_events_page' );
	$locations_page_id = get_option( 'dbem_locations_page' );
	$categories_page_id = get_option( 'dbem_categories_page' );
	if ( !empty($events_page_id) && $post->ID == $events_page_id ) {
		if ( $wp_query->get('calendar_day') ) {
			return "calendar_day";
		}elseif ( $wp_query->get('bookings_page') ) {
			return "my_bookings";
		}else{
			return is_object($EM_Event) ? "event" : "events";
		}
	}elseif( empty($events_page_id) ){
		if( $wp_query->get('calendar_day') ){
			return "calendar_day";
		}elseif ( $wp_query->get('bookings_page') ) {
			return "my_bookings";
		}
	}
	if( is_single() && $post->post_type == EM_POST_TYPE_EVENT  ){
		return 'event';
	}
	if( (!empty($locations_page_id) && $post->ID == $locations_page_id) || (!is_single() && $wp_query->query_vars['post_type'] == EM_POST_TYPE_LOCATION) ){
		return is_object($EM_Location) ? "location":"locations";
	}elseif( is_single() && $post->post_type == EM_POST_TYPE_LOCATION ){
		return 'location';
	}
	if( (!empty($categories_page_id) && $post->ID == $categories_page_id) ){
		return "categories";		
	}elseif( !empty($wp_query->tax_query->queries[0]['taxonomy']) &&  $wp_query->tax_query->queries[0]['taxonomy'] == EM_TAXONOMY_CATEGORY ){
		return "category";
	}
}
?>