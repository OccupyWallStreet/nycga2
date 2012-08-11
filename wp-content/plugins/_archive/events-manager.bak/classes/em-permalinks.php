<?php

if( !class_exists('EM_Permalinks') ){
	class EM_Permalinks {
		static $em_queryvars = array(
			'event_id', 'event_slug',
			'location_id', 'location_slug',
			'person_id',
			'booking_id',
			'category_id', 'category_slug',
			'ticket_id',
			'calendar_day',
			'book',
			'rss','ical', 'scope', 'page', 'bookings_page', 'payment_gateway','event_categories','event_locations'
		);
		static $scopes = 'today|tomorrow|this\-month|next\-month|past|all|future';
		
		function init(){	
			add_filter('pre_update_option_dbem_events_page', array('EM_Permalinks','option_update'));
			add_filter('init', array('EM_Permalinks','flush'));
			add_filter('rewrite_rules_array',array('EM_Permalinks','rewrite_rules_array'));
			add_filter('query_vars',array('EM_Permalinks','query_vars'));
			add_action('template_redirect',array('EM_Permalinks','init_objects'), 1);
			add_action('template_redirect',array('EM_Permalinks','redirection'), 1);
			//Add filters to rewrite the URLs
			add_filter('em_event_output_placeholder',array('EM_Permalinks','rewrite_urls'),1,3);
			add_filter('em_location_output_placeholder',array('EM_Permalinks','rewrite_urls'),1,3);
			add_filter('em_category_output_placeholder',array('EM_Permalinks','rewrite_urls'),1,3);
			if( !defined('EM_EVENT_SLUG') ){ define('EM_EVENT_SLUG','event'); }
			if( !defined('EM_LOCATION_SLUG') ){ define('EM_LOCATION_SLUG','location'); }
			if( !defined('EM_LOCATIONS_SLUG') ){ define('EM_LOCATIONS_SLUG','locations'); }
			if( !defined('EM_CATEGORY_SLUG') ){ define('EM_CATEGORY_SLUG','category'); }
			if( !defined('EM_CATEGORIES_SLUG') ){ define('EM_CATEGORIES_SLUG','categories'); }
		}
		
		function flush(){
			global $wp_rewrite;
			if( get_option('dbem_flush_needed') ){
			   	$wp_rewrite->flush_rules();
				delete_option('dbem_flush_needed');
			}
		}
		
		function rewrite_urls($replace, $object, $result){
			global $wp_query, $wp_rewrite;
			if( $wp_rewrite->using_permalinks() && !defined('EM_DISABLE_PERMALINKS')){
				switch( $result ){
					case '#_EVENTPAGEURL': //Depreciated	
					case '#_LINKEDNAME': //Depreciated
					case '#_EVENTURL': //Just the URL
					case '#_EVENTLINK': //HTML Link
						if( is_object($object) && get_class($object)=='EM_Event' ){
							$EM_URI = EM_URI;
							if( is_multisite() && get_site_option('dbem_ms_global_events') && get_site_option('dbem_ms_global_events_links') && !empty($object->blog_id) && is_main_site() && $object->blog_id != get_current_blog_id() ){
								$EM_URI = get_blog_permalink($object->blog_id, get_blog_option($object->blog_id, 'dbem_events_page'));
							}
							$event_link = trailingslashit(trailingslashit($EM_URI).EM_EVENT_SLUG.'/'.$object->slug);
							if($result == '#_LINKEDNAME' || $result == '#_EVENTLINK'){
								$replace = "<a href='{$event_link}' title='{$object->name}'>{$object->name}</a>";
							}else{
								$replace = $event_link;
							}
						}
						break;
					case '#_LOCATIONURL':
					case '#_LOCATIONLINK':
					case '#_LOCATIONPAGEURL': //Depreciated
						if( is_object($object) && get_class($object)=='EM_Location' ){
							$link = trailingslashit(trailingslashit(EM_URI).EM_LOCATION_SLUG.'/'.$object->slug);
							$replace = ($result == '#_LOCATIONURL' || $result == '#_LOCATIONPAGEURL') ? $link : '<a href="'.$link.'">'.$object->name.'</a>';
						}
						break;
					case '#_CATEGORYLINK':
					case '#_CATEGORYURL':
						if( is_object($object) && get_class($object)=='EM_Category' ){
							$link = trailingslashit(trailingslashit(EM_URI).EM_CATEGORY_SLUG.'/'.$object->slug);
							$replace = ($result == '#_CATEGORYURL') ? $link : '<a href="'.$link.'">'.$object->name.'</a>';
						}
						break;
				}
			}
			return $replace;
		}
		
		/**
		 * will redirect old links to new link structures.
		 * @return mixed
		 */
		function redirection(){
			global $wp_rewrite, $post, $wp_query;
			if( $wp_rewrite->using_permalinks() && !is_admin() && !defined('EM_DISABLE_PERMALINKS') ){
				//is this a querystring url?
				$events_page_id = get_option ( 'dbem_events_page' );
				if ( is_object($post) && $post->ID == $events_page_id && $events_page_id != 0 ) {
					$page = ( !empty($_GET['page']) && is_numeric($_GET['page']) )? $_GET['page'] : '';
					if ( !empty($_GET['calendar_day']) ) {
						//Events for a specific day
						wp_redirect( self::url($_GET['calendar_day'],$page), 301);
						exit();
					} elseif ( !empty($_GET['location_id']) && is_numeric($_GET['location_id']) ) {
						//Just a single location
						$EM_Location = new EM_Location($_GET['location_id']);
						wp_redirect( self::url('location', $EM_Location->slug,$page), 301);
						exit();
					} elseif ( !empty($_GET['event_id']) && is_numeric($_GET['event_id']) ) {
						//single event page
						$EM_Event = new EM_Event($_GET['event_id']);
						wp_redirect( self::url(EM_EVENT_SLUG, $EM_Event->slug), 301);
						exit();
					}			
				}
				if( !empty($_GET['dbem_rss']) ){
					//RSS page
					wp_redirect( self::url('rss'), 301);
					exit();
				}
			}
		}		
		// Adding a new rule
		function rewrite_rules_array($rules){
			//get the slug of the event page
			$events_page_id = get_option ( 'dbem_events_page' );
			$events_page = get_post($events_page_id);
			$em_rules = array();
			if( is_object($events_page) ){
				$events_slug = str_replace(trailingslashit(get_bloginfo('wpurl')),'', get_permalink($events_page->ID));
        		$events_slug = str_replace(trailingslashit(get_bloginfo('url')),'', get_permalink($events_page->ID));				
				$events_slug = preg_replace('/\/$/','',$events_slug);
				$em_rules[$events_slug.'/('.self::$scopes.')$'] = 'index.php?pagename='.$events_slug.'&scope=$matches[1]'; //events with scope
				$em_rules[$events_slug.'/(\d{4}-\d{2}-\d{2})$'] = 'index.php?pagename='.$events_slug.'&calendar_day=$matches[1]'; //event calendar date search
				$em_rules[$events_slug.'/'.EM_EVENT_SLUG.'/(\d*)$'] = 'index.php?pagename='.$events_slug.'&event_id=$matches[1]'; //single event page with id
				$em_rules[$events_slug.'/my\-bookings$'] = 'index.php?pagename='.$events_slug.'&bookings_page=1'; //page for users to manage bookings
				$em_rules[$events_slug.'/my\-bookings/(\d+)$'] = 'index.php?pagename='.$events_slug.'&booking_id=$matches[1]'; //page for users to manage bookings
				$em_rules[$events_slug.'/bookings/(\d+)$'] = 'index.php?pagename='.$events_slug.'&event_id=$matches[1]&book=1'; //single event booking form with id
				$em_rules[$events_slug.'/bookings/(.+)$'] = 'index.php?pagename='.$events_slug.'&event_slug=$matches[1]&book=1'; //single event booking form with slug
				$em_rules[$events_slug.'/'.EM_EVENT_SLUG.'/(.+)$'] = 'index.php?pagename='.$events_slug.'&event_slug=$matches[1]'; //single event page with slug
				$em_rules[$events_slug.'/'.EM_LOCATIONS_SLUG.'$'] = 'index.php?pagename='.$events_slug.'&event_locations=1'; //category list with slug
				$em_rules[$events_slug.'/'.EM_LOCATION_SLUG.'/(\d+)$'] = 'index.php?pagename='.$events_slug.'&location_id=$matches[1]'; //location page with id
				$em_rules[$events_slug.'/'.EM_LOCATION_SLUG.'/(.+)$'] = 'index.php?pagename='.$events_slug.'&location_slug=$matches[1]'; //location page with slug
				$em_rules[$events_slug.'/'.EM_CATEGORIES_SLUG.'$'] = 'index.php?pagename='.$events_slug.'&event_categories=1'; //category list with slug
				$em_rules[$events_slug.'/'.EM_CATEGORY_SLUG.'/(.+)$'] = 'index.php?pagename='.$events_slug.'&category_slug=$matches[1]'; //category page with slug
				$em_rules[$events_slug.'/rss$'] = 'index.php?pagename='.$events_slug.'&rss=1'; //rss page
				$em_rules[$events_slug.'/ical$'] = 'index.php?pagename='.$events_slug.'&ical=1'; //ical page
				$em_rules[$events_slug.'/payments/(.+)$'] = 'index.php?pagename='.$events_slug.'&payment_gateway=$matches[1]'; //single event booking form with slug
				$em_rules[$events_slug.'/(\d+)$'] = 'index.php?pagename='.$events_slug.'&page=$matches[1]'; //event pageno
			}
			return $em_rules + $rules;
		}
		
		/**
		 * Generate a URL. Pass each section of a link as a parameter, e.g. EM_Permalinks::url('event',$event_id); will create an event link.
		 * @param mixed 
		 */
		function url(){
			global $wp_rewrite;
			$args = func_get_args();
			$em_uri = get_permalink(get_option("dbem_events_page")); //PAGE URI OF EM
			if ( $wp_rewrite->using_permalinks() && !defined('EM_DISABLE_PERMALINKS') ) {
				$event_link = trailingslashit(trailingslashit($em_uri). implode('/',$args));
			}
			return $event_link;
		}
		
		/**
		 * checks if the events page has changed, and sets a flag to flush wp_rewrite.
		 * @param mixed $val
		 * @return mixed
		 */
		function option_update( $val ){
			if( get_option('dbem_events_page') != $val ){
				update_option('dbem_flush_needed',1);
			}
		   	return $val;
		}
		
		// Adding the id var so that WP recognizes it
		function query_vars($vars){
			foreach(self::$em_queryvars as $em_queryvar){
				array_push($vars, $em_queryvar);
			}
		    return $vars;
		}
		
		/**
		 * Not the "WP way" but for now this'll do! 
		 */
		function init_objects(){
			//Build permalinks here
			global $wp_query, $wp_rewrite;
			if ( $wp_rewrite->using_permalinks() && !defined('EM_DISABLE_PERMALINKS') ) {
				foreach(self::$em_queryvars as $em_queryvar){
					if( $wp_query->get($em_queryvar) ) {
						$_REQUEST[$em_queryvar] = $wp_query->get($em_queryvar);
					}
				}
		    }
			//dirty rss condition
			if( !empty($_REQUEST['rss']) ){
				$_REQUEST['rss_main'] = 'main';
			}
		}
	}
	EM_Permalinks::init();
}

//Specific links that aren't generated by objects

/**
 * returns the url of the my bookings page, depending on the settings page and if BP is installed.
 * @return string
 */
function em_get_my_bookings_url(){
	global $bp, $wp_rewrite;
	if( is_object($bp) ){
		//get member url
		return $bp->events->link.'attending/';
	}elseif( get_option('dbem_bookings_my_page') ){
		return get_permalink(get_option('dbem_bookings_my_page'));
	}else{
		if( $wp_rewrite->using_permalinks() && !defined('EM_DISABLE_PERMALINKS') ){
			return trailingslashit(EM_URI)."my-bookings/";
		}else{
			return preg_match('/\?/',EM_URI) ? EM_URI.'&bookings_page=1':EM_URI.'?bookings_page=1';
		}
	}
}
