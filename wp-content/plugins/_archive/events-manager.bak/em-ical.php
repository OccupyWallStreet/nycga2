<?php
	/**
	 * generates an ical feed on init if url is correct
	 */
	function em_ical( $regenerate = false ){
		$cal_file_request = preg_match('/events.ics$/', $_SERVER['REQUEST_URI']); //are we askig for the ics file directly but doesn't exist?
		if ( $cal_file_request || $regenerate ) {
			ob_start();
			em_locate_template('templates/ical.php', true);
			$calendar = preg_replace("/([^\r])\n/", "$1\r\n", ob_get_clean());//get the contents to output and clean crlf issues
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
	
	function em_update_ical($result){
		update_option('dbem_regenerate_ical',true);
		return $result;
	}
	add_filter('em_event_save','em_update_ical', 1, 1);
?>