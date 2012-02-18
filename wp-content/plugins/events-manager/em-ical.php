<?php
	/**
	 * generates an ical feed on init if url is correct
	 */
	function em_ical( $regenerate = false ){
		//add endpoints to events
		add_filter('template_redirect', 'em_ical_event');
		//check if this is a calendar request for all events
		$cal_file_request = preg_match('/events.ics$/', $_SERVER['REQUEST_URI']); //are we askig for the ics file directly but doesn't exist?
		if ( $cal_file_request || $regenerate ) {
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
		global $post;
		if( is_single() && !empty($post) && $post->post_type == EM_POST_TYPE_EVENT && get_query_var('ical') ){
			global $EM_Event;
			$EM_Event = em_get_event($post->ID, 'post_id');
			ob_start();
			em_locate_template('templates/ical-event.php', true);
			echo preg_replace("/([^\r])\n/", "$1\r\n", ob_get_clean());
			exit();
		}
	}
	
	
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