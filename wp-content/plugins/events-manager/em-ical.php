<?php
	/**
	 * generates an ical feed on init if url is correct
	 */
	function em_ical( $regenerate = false ){
		//check if this is a calendar request for all events
		$cal_file_request = preg_match('/events.ics$/', $_SERVER['REQUEST_URI']); //are we askig for the ics file directly but doesn't exist?
		if ( $cal_file_request || $_SERVER['REQUEST_URI'] == '/?ical=1' || $regenerate ) {
			$calendar = em_ical_events();
			//let's create a cache file
			/*
			if( get_option('dbem_regenerate_ical') || !file_exists(ABSPATH . "/events.ics") ){
				$file = fopen( ABSPATH . "/events.ics", 'w');
				if($file){
					fwrite($file, $calendar, strlen($calendar));
					fclose($file);
					update_option('dbem_regenerate_ical',false);
				}
			}
			*/
			echo $calendar;	
			die ();
		}
	}
	add_action ( 'init', 'em_ical' );
	
	function em_ical_event(){
		global $wpdb, $wp_query;
		//add endpoints to events
		if( !empty($wp_query) && $wp_query->get(EM_POST_TYPE_EVENT) && $wp_query->get('ical') ){
			$event_id = $wpdb->get_var('SELECT event_id FROM '.EM_EVENTS_TABLE." WHERE event_slug='".$wp_query->get(EM_POST_TYPE_EVENT)."' AND event_status=1 LIMIT 1");
			if( !empty($event_id) ){
				global $EM_Event;
				$EM_Event = em_get_event($event_id);
				ob_start();
				em_locate_template('templates/ical-event.php', true);
				echo preg_replace("/([^\r])\n/", "$1\r\n", ob_get_clean());
				exit();
			}
		}
	}
	add_action ( 'parse_query', 'em_ical_event' );
	
	
	function em_ical_events(){
		ob_start();
		em_locate_template('templates/ical.php', true);
		return preg_replace("/([^\r])\n/", "$1\r\n", ob_get_clean());//get the contents to output and clean crlf issues
	}
	
	function em_update_ical($result){
		update_option('dbem_regenerate_ical',true);
		return $result;
	}
	add_filter('em_event_save','em_update_ical', 1, 1);
?>