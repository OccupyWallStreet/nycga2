<?php

if( !class_exists('EM_Permalinks') ){
	class EM_Permalinks {
		static $em_queryvars = array(
			'event_id','event_slug', 'em_redirect',
			'location_id','location_slug',
			'person_id',
			'booking_id',
			'category_id', 'category_slug',
			'ticket_id',
			'calendar_day',
			'rss', 'ical', 'bookings_page','event_categories','event_locations'
		);
		
		function init(){
			add_filter('pre_update_option_dbem_events_page', array('EM_Permalinks','option_update'));
			if( get_option('dbem_flush_needed') ){
				add_filter('init', array('EM_Permalinks','flush'));
			}
			add_filter('rewrite_rules_array',array('EM_Permalinks','rewrite_rules_array'));
			add_filter('query_vars',array('EM_Permalinks','query_vars'));
			add_action('parse_query',array('EM_Permalinks','init_objects'), 1);
			add_action('parse_query',array('EM_Permalinks','redirection'), 1);
			if( !defined('EM_EVENT_SLUG') ){ define('EM_EVENT_SLUG','event'); }
			if( !defined('EM_LOCATION_SLUG') ){ define('EM_LOCATION_SLUG','location'); }
			if( !defined('EM_LOCATIONS_SLUG') ){ define('EM_LOCATIONS_SLUG','locations'); }
			if( !defined('EM_CATEGORY_SLUG') ){ define('EM_CATEGORY_SLUG','category'); }
			if( !defined('EM_CATEGORIES_SLUG') ){ define('EM_CATEGORIES_SLUG','categories'); }
		}
		
		function flush(){
			global $wp_rewrite;
			$wp_rewrite->flush_rules();
			delete_option('dbem_flush_needed');
		}
		
		/**
		 * will redirect old links to new link structures.
		 * @return mixed
		 */
		function redirection(){
			global $wpdb, $wp_rewrite, $post, $wp_query;
			if( is_object($wp_query) && $wp_query->get('em_redirect') ){
				//is this a querystring url?
				if( $wp_query->get('event_slug') ){
					$event = $wpdb->get_row('SELECT event_id, post_id FROM '.EM_EVENTS_TABLE." WHERE event_slug='".$wp_query->get('event_slug')."' AND (blog_id=".get_current_blog_id()." OR blog_id IS NULL OR blog_id=0)", ARRAY_A);
					if( !empty($event) ){
						$EM_Event = em_get_event($event['event_id']);
						$url = get_permalink($EM_Event->post_id);
					}
				}elseif( $wp_query->get('location_slug') ){
					$location = $wpdb->get_row('SELECT location_id, post_id FROM '.EM_LOCATIONS_TABLE." WHERE location_slug='".$wp_query->get('location_slug')."' AND (blog_id=".get_current_blog_id()." OR blog_id IS NULL OR blog_id=0)", ARRAY_A);
					if( !empty($location) ){
						$EM_Location = em_get_location($location['location_id']);
						$url = get_permalink($EM_Location->post_id);
					}
				}elseif( $wp_query->get('category_slug') ){
					$url = get_term_link($wp_query->get('category_slug'), EM_TAXONOMY_CATEGORY);
				}
				if(!empty($url)){
					wp_redirect($url,301);
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
				$events_slug = preg_replace('/\/$/', '', str_replace( trailingslashit(home_url()), '', get_permalink($events_page_id)) );
				$events_slug = ( !empty($events_slug) ) ? trailingslashit($events_slug) : $events_slug;		
				$em_rules[$events_slug.'(\d{4}-\d{2}-\d{2})$'] = 'index.php?pagename='.$events_slug.'&calendar_day=$matches[1]'; //event calendar date search
				if( !get_option( 'dbem_my_bookings_page') || !is_object(get_post(get_option( 'dbem_my_bookings_page'))) ){
					$em_rules[$events_slug.'my\-bookings$'] = 'index.php?pagename='.$events_slug.'&bookings_page=1'; //page for users to manage bookings
				}
				$em_rules[$events_slug.'rss$'] = 'index.php?pagename='.$events_slug.'&rss=1'; //rss page
				$em_rules[$events_slug.'feed$'] = 'index.php?pagename='.$events_slug.'&rss=1'; //compatible rss page
				if( EM_POST_TYPE_EVENT_SLUG.'/' == $events_slug ){ //won't apply on homepage
					//make sure we hard-code rewrites for child pages of events
					$child_posts = get_posts(array('post_type'=>'page', 'post_parent'=>$events_page->ID));
					foreach($child_posts as $child_post){
						$em_rules[$events_slug.$child_post->post_name.'/?$'] = 'index.php?page_id='.$child_post->ID; //single event booking form with slug
					}		
				}
				if( EM_MS_GLOBAL && !get_site_option('dbem_ms_global_events_links', true) ){
					//MS Mode has slug also for global links
					$em_rules[$events_slug.get_site_option('dbem_ms_events_slug',EM_EVENT_SLUG).'/(.+)$'] = 'index.php?pagename='.$events_slug.'&em_redirect=1&event_slug=$matches[1]'; //single event from subsite
				}
				//add redirection for backwards compatability
				$em_rules[$events_slug.EM_EVENT_SLUG.'/(.+)$'] = 'index.php?pagename='.$events_slug.'&em_redirect=1&event_slug=$matches[1]'; //single event
				$em_rules[$events_slug.EM_LOCATION_SLUG.'/(.+)$'] = 'index.php?pagename='.$events_slug.'&em_redirect=1&location_slug=$matches[1]'; //single location page
				$em_rules[$events_slug.EM_CATEGORY_SLUG.'/(.+)$'] = 'index.php?pagename='.$events_slug.'&em_redirect=1&category_slug=$matches[1]'; //single category page slug
			}else{
				$events_slug = EM_POST_TYPE_EVENT_SLUG;
				$em_rules[$events_slug.'/(\d{4}-\d{2}-\d{2})$'] = 'index.php?post_type='.EM_POST_TYPE_EVENT.'&calendar_day=$matches[1]'; //event calendar date search
				$em_rules[$events_slug.'/my\-bookings$'] = 'index.php?post_type='.EM_POST_TYPE_EVENT.'&bookings_page=1'; //page for users to manage bookings
				$em_rules[$events_slug.'/rss$'] = 'index.php?post_type='.EM_POST_TYPE_EVENT.'&rss=1'; //rss page
			}
			//If in MS global mode and locations are linked on same site
			if( EM_MS_GLOBAL && !get_site_option('dbem_ms_global_locations_links', true) ){
				$locations_page_id = get_option ( 'dbem_locations_page' );
				$locations_page = get_post($locations_page_id);
				if( is_object($locations_page) ){
					$locations_slug = preg_replace('/\/$/', '', str_replace( trailingslashit(home_url()), '', get_permalink($locations_page_id) ));
					$em_rules[$locations_slug.'/'.get_site_option('dbem_ms_locations_slug',EM_LOCATION_SLUG).'/(.+)$'] = 'index.php?pagename='.$locations_slug.'&location_slug=$matches[1]'; //single event booking form with slug
				}					
			}
			//add ical endpoint
			$em_rules[EM_POST_TYPE_EVENT_SLUG."/([^/]+)/ical/?$"] = 'index.php?event=$matches[1]&ical=1';
			return $em_rules + $rules;
		}
		
		/**
		 * Depreciated, use get_post_permalink() from now on or the output function with a placeholder
		 * Generate a URL. Pass each section of a link as a parameter, e.g. EM_Permalinks::url('event',$event_id); will create an event link.
		 * @param mixed 
		 */
		function url(){
			global $wp_rewrite;
			$args = func_get_args();
			$em_uri = get_permalink(get_option("dbem_events_page")); //PAGE URI OF EM
			if ( $wp_rewrite->using_permalinks() /*&& !defined('EM_DISABLE_PERMALINKS')*/ ) {
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
			global $wp_query, $wp_rewrite;
			//check some homepage conditions
			$events_page_id = get_option ( 'dbem_events_page' );
			if( is_object($wp_query) && $wp_query->is_home && 'page' == get_option('show_on_front') && get_option('page_on_front') == $events_page_id ){
				$wp_query->is_page = true;
				$wp_query->is_home = false;
				$wp_query->query_vars['page_id'] = $events_page_id;
			}
			if ( is_object($wp_query) && is_object($wp_rewrite) && $wp_rewrite->using_permalinks() ) {
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
	}elseif( get_option('dbem_my_bookings_page') ){
		return get_permalink(get_option('dbem_my_bookings_page'));
	}else{
		if( $wp_rewrite->using_permalinks() && !defined('EM_DISABLE_PERMALINKS') ){
			return trailingslashit(EM_URI)."my-bookings/";
		}else{
			return preg_match('/\?/',EM_URI) ? EM_URI.'&bookings_page=1':EM_URI.'?bookings_page=1';
		}
	}
}
