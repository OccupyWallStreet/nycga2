<?php
class EM_Calendar extends EM_Object {
	
	function init(){
		//nothing to init anymore
	}
	
	function get( $args ){
	
	 	global $wpdb; 
	 	
		$calendar_array = array();
		$calendar_array['cells'] = array();
	 	
		$original_args = $args;
		$args = self::get_default_search($args);
		$full = $args['full']; //For ZDE, don't delete pls
		$month = $args['month']; 
		$year = $args['year'];
		$long_events = $args['long_events'];
		
		$week_starts_on_sunday = get_option('dbem_week_starts_sunday');
	   	$start_of_week = get_option('start_of_week');
		
		if( !(is_numeric($month) && $month <= 12 && $month > 0) )   {
			$month = date('m'); 
		}
		if( !( is_numeric($year) ) ){
			$year = date('Y');
		}  
		  
		// Get the first day of the month 
		$month_start = mktime(0,0,0,$month, 1, $year);
		$calendar_array['month_start'] = $month_start;
		
		// Get friendly month name 		
		$month_name = date('M',$month_start);
		// Figure out which day of the week 
		// the month starts on. 
		$month_start_day = date('D', $month_start);
	  
	  	switch($month_start_day){ 
			case "Sun": $offset = 0; break; 
			case "Mon": $offset = 1; break; 
			case "Tue": $offset = 2; break; 
			case "Wed": $offset = 3; break; 
			case "Thu": $offset = 4; break; 
			case "Fri": $offset = 5; break; 
			case "Sat": $offset = 6; break;
		}       
		//We need to go back to the WP defined day when the week started, in case the event day is near the end
		$offset -= $start_of_week;
		if($offset<0)
			$offset += 7;
		
		// determine how many days are in the last month.
		$month_last = $month-1;
		$month_next = $month+1;
		$calendar_array['month_next'] = $month_next;
		$year_last = $year; 
		$year_next = $year;
		$calendar_array['year_next'] = $year_next;
		
		if($month == 1) { 
		   $month_last = 12;
		   $year_last = $year -1;
		}elseif($month == 12){
			$month_next = 1;
			$year_next = $year + 1; 
		}
		$calendar_array['month_last'] = $month_last;
		$calendar_array['year_last'] = $year_last;
		
		$num_days_last = self::days_in_month($month_last, $year_last);
		 
		// determine how many days are in the current month. 
		$num_days_current = self::days_in_month($month, $year);
		// Build an array for the current days 
		// in the month 
		for($i = 1; $i <= $num_days_current; $i++){ 
		   $num_days_array[] = mktime(0,0,0,$month, $i, $year); 
		}
		// Build an array for the number of days 
		// in last month 
		for($i = 1; $i <= $num_days_last; $i++){ 
		    $num_days_last_array[] = mktime(0,0,0,$month_last, $i, $year_last); 
		}
		// If the $offset from the starting day of the 
		// week happens to be Sunday, $offset would be 0, 
		// so don't need an offset correction. 
	
		if($offset > 0){ 
		    $offset_correction = array_slice($num_days_last_array, -$offset, $offset); 
		    $new_count = array_merge($offset_correction, $num_days_array); 
		    $offset_count = count($offset_correction); 
		} else { // The else statement is to prevent building the $offset array. 
		    $offset_count = 0; 
		    $new_count = $num_days_array;
		}
		// count how many days we have with the two 
		// previous arrays merged together 
		$current_num = count($new_count); 
	
		// Since we will have 5 HTML table rows (TR) 
		// with 7 table data entries (TD) 
		// we need to fill in 35 TDs 
		// so, we will have to figure out 
		// how many days to appened to the end 
		// of the final array to make it 35 days. 	
		if($current_num > 35){ 
		   $num_weeks = 6; 
		   $outset = (42 - $current_num); 
		} elseif($current_num < 35){ 
		   $num_weeks = 5; 
		   $outset = (35 - $current_num); 
		} 
		if($current_num == 35){ 
		   $num_weeks = 5; 
		   $outset = 0; 
		} 
		// Outset Correction 
		for($i = 1; $i <= $outset; $i++){ 
		   $new_count[] = mktime(0,0,0,$month_next, $i, $year_next);  
		}
		// Now let's "chunk" the $all_days array 
		// into weeks. Each week has 7 days 
		// so we will array_chunk it into 7 days. 
		$weeks = array_chunk($new_count, 7);    
		  
		//Get an array of arguments that don't include default valued args
		$link_args = self::get_link_args($args);

		$previous_url = "?ajaxCalendar=1&amp;mo={$month_last}&amp;yr={$year_last}&amp;{$link_args}";
		$next_url = "?ajaxCalendar=1&amp;mo={$month_next}&amp;yr={$year_next}&amp;{$link_args}";
		
	 	$weekdays = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	 	if(!empty($args['full'])) {
 		    if( get_option('dbem_full_calendar_abbreviated_weekdays') ) $weekdays = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
 			$day_initials_length =  get_option('dbem_full_calendar_initials_length');
 		} else {
 		    if ( get_option('dbem_small_calendar_abbreviated_weekdays') ) $weekdays = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
	 		$day_initials_length = get_option('dbem_small_calendar_initials_length');
 		}
 		
		for( $n = 0; $n < $start_of_week; $n++ ) {   
			$last_day = array_shift($weekdays);     
			$weekdays[]= $last_day;
		}
	   
		$days_initials_array = array();
		foreach($weekdays as $weekday) {
			$days_initials_array[] = self::translate_and_trim($weekday, $day_initials_length);
		} 
		
		$calendar_array['links'] = array( 'previous_url'=>$previous_url, 'next_url'=>$next_url);
		$calendar_array['row_headers'] = $days_initials_array;
		
		// Now we break each key of the array  
		// into a week and create a new table row for each 
		// week with the days of that week in the table data 
	  
		$i = 0;
		$current_date = date('Y-m-d', current_time('timestamp'));
		$week_count = 0;
		foreach ( $weeks as $week ) {
			foreach ( $week as $d ) {
				$date = date('Y-m-d', $d);
				$calendar_array['cells'][$date] = array('date'=>$d); //set it up so we have the exact array of dates to be filled
				if ($i < $offset_count) { //if it is PREVIOUS month
					$calendar_array['cells'][$date]['type'] = 'pre';
				}
				if (($i >= $offset_count) && ($i < ($num_weeks * 7) - $outset)) { // if it is THIS month
					if ( $current_date == $date ){	
						$calendar_array['cells'][$date]['type'] = 'today';
					}
				} elseif (($outset > 0)) { //if it is NEXT month
					if (($i >= ($num_weeks * 7) - $outset)) {
						$calendar_array['cells'][$date]['type'] = 'post';
					}
				}
				$i ++;
			}
			$week_count++;
		} 
		
			// query the database for events in this time span
		if ($month == 1) {
			$month_pre=12;
			$month_post=2;
			$year_pre=$year-1;
			$year_post=$year;
		} elseif($month == 12) {
			$month_pre=11;
			$month_post=1;
			$year_pre=$year;
			$year_post=$year+1;
		} else {
			$month_pre=$month-1;
			$month_post=$month+1;
			$year_pre=$year;
			$year_post=$year;
		}
		$args['year'] = array($year_pre, $year_post);
		$args['month'] = array($month_pre, $month_post);
		$events = EM_Events::get($args);
	
		$event_format = get_option('dbem_full_calendar_event_format'); 
		$event_title_format = get_option('dbem_small_calendar_event_title_format');
		$event_title_separator_format = get_option('dbem_small_calendar_event_title_separator');
		
		$eventful_days= array();
		if($events){
			//Go through the events and slot them into the right d-m index
			foreach($events as $event) {
				$event = apply_filters('em_calendar_output_loop_start', $event);
				if( $long_events ){
					//If $long_events is set then show a date as eventful if there is an multi-day event which runs during that day
					$event_start_date = strtotime($event->start_date);
					$event_end_date = mktime(0,0,0,$month_post,date('t', $event_start_date),$year_post );
					if( $event_end_date == '' ) $event_end_date = $event_start_date;
					while( $event_start_date <= $event->end ){
						//Ensure date is within event dates, if so add to eventful days array
						$event_eventful_date = date('Y-m-d', $event_start_date);
						if( array_key_exists($event_eventful_date, $eventful_days) && is_array($eventful_days[$event_eventful_date]) ){
							$eventful_days[$event_eventful_date][] = $event; 
						} else {
							$eventful_days[$event_eventful_date] = array($event);  
						}
						$event_start_date += (86400); //add a day		
					}
				}else{
					//Only show events on the day that they start
					if( isset($eventful_days[$event->event_start_date]) && is_array($eventful_days[$event->event_start_date]) ){
						$eventful_days[$event->event_start_date][] = $event; 
					} else {
						$eventful_days[$event->event_start_date] = array($event);  
					}
				}
			}
		}
		//generate a link argument string containing event search only
		$day_link_args = self::get_link_args( array_intersect_key($original_args, EM_Events::get_post_search($args, true) ));
		foreach($eventful_days as $day_key => $events) {
			if( array_key_exists($day_key, $calendar_array['cells']) ){
				//Get link title for this date
				$events_titles = array();
				foreach($events as $event) {
					if( !get_option('dbem_display_calendar_events_limit') || count($events_titles) < get_option('dbem_display_calendar_events_limit') ){
						$events_titles[] = $event->output($event_title_format);
					}else{
						$events_titles[] = get_option('dbem_display_calendar_events_limit_msg');
						break;
					}
				}   
				$calendar_array['cells'][$day_key]['link_title'] = implode( $event_title_separator_format, $events_titles);
							
				//Get the link to this calendar day
				global $wp_rewrite;
				if( count($events) > 1 || !get_option('dbem_calendar_direct_links') ){
					if( get_option("dbem_events_page") > 0 ){
						$event_page_link = trailingslashit(get_permalink(get_option("dbem_events_page"))); //PAGE URI OF EM
					}else{
						if( $wp_rewrite->using_permalinks() ){
							$event_page_link = trailingslashit(home_url()).EM_POST_TYPE_EVENT_SLUG.'/'; //don't use EM_URI here, since ajax calls this before EM_URI is defined.
						}else{
							$event_page_link = trailingslashit(home_url()).'?post_type='.EM_POST_TYPE_EVENT; //don't use EM_URI here, since ajax calls this before EM_URI is defined.
						}
					}
					if( $wp_rewrite->using_permalinks() && !defined('EM_DISABLE_PERMALINKS') ){
						$calendar_array['cells'][$day_key]['link'] = $event_page_link.$day_key."/";
						if( !empty($day_link_args) ){
							$calendar_array['cells'][$day_key]['link'] .= '?'.$day_link_args;
						}
					}else{
						$joiner = (stristr($event_page_link, "?")) ? "&amp;" : "?";				
						$calendar_array['cells'][$day_key]['link'] = $event_page_link.$joiner."calendar_day=".$day_key;
						if( !empty($day_link_args) ){
							$calendar_array['cells'][$day_key]['link'] .= 'amp;'.$day_link_args;
						}
					}
				}else{
					foreach($events as $EM_Event){
						$calendar_array['cells'][$day_key]['link'] = $EM_Event->get_permalink();
					}
				}
				//Add events to array
				$calendar_array['cells'][$day_key]['events'] = $events;
			}
		}
		return apply_filters('em_calendar_get',$calendar_array, $args);
	}
	
	function output($args = array(), $wrapper = true) {
		//Let month and year REQUEST override for non-JS users
		if( !empty($_REQUEST['mo']) || !empty($args['mo']) ){
			$args['month'] = ($_REQUEST['mo']) ? $_REQUEST['mo']:$args['mo'];	
		}
		if( !empty($_REQUEST['yr']) || !empty($args['yr']) ){
			$args['year'] = (!empty($_REQUEST['yr'])) ? $_REQUEST['yr']:$args['yr'];
		}
		$calendar_array  = self::get($args);
		$template = (!empty($args['full'])) ? 'templates/calendar-full.php':'templates/calendar-small.php';
		ob_start();
		em_locate_template($template, true, array('calendar'=>$calendar_array,'args'=>$args));
		if($wrapper){
			$calendar = '<div id="em-calendar-'.rand(100,200).'" class="em-calendar-wrapper">'.ob_get_clean().'</div>';
		}else{
			$calendar = ob_get_clean();
		}
		return apply_filters('em_calendar_output', $calendar, $args);
	}


	function days_in_month($month, $year) {
		return date('t', mktime(0,0,0,$month,1,$year));
	}
	 
	function translate_and_trim($string, $length = 1) {
	    if( $length > 0 ){
			if(function_exists('mb_substr')){ //fix for diacritic calendar names
			    return mb_substr(__($string,'dbem'), 0, $length, 'UTF-8');
			}else{ 
	    		return substr(__($string,'dbem'), 0, $length); 
	    	}
	    }
	    return __($string,'dbem');
	}  
	
	/**
	 * Helper function to create a link querystring from array which contains arguments with only values that aren't defuaults. 
	 */
	function get_link_args($args = array(), $html_entities=true){
		unset($args['month']); unset($args['year']);
		$default_args = self::get_default_search(array());
		foreach($default_args as $arg_key => $arg_value){
			if( !isset($args[$arg_key]) || $args[$arg_key] == $arg_value ){
				unset($args[$arg_key]);				
			}
		}
		$qs_array = array();
		foreach($args as $key => $value){
			if(is_array($value)){
				$value = implode(',',$value);
			}
			$qs_array[] = "$key=".urlencode($value);
		}
		return ($html_entities) ? implode('&amp;', $qs_array) : implode('&', $qs_array);
	}
		
	
	function get_default_search($array=array()){
		//These defaults aren't for db queries, but flags for what to display in calendar output
		$defaults = array( 
			'full' => 0, //Will display a full calendar with event names
			'long_events' => 0, //Events that last longer than a day
			'scope' => 'future',
			'status' => 1, //approved events only
			'town' => false,
			'state' => false,
			'country' => false,
			'region' => false,
			'blog' => get_current_blog_id(),
			'orderby' => get_option('dbem_display_calendar_orderby'),
			'order' => get_option('dbem_display_calendar_order')
		);
		if(is_multisite()){
			global $bp;
			if( !is_main_site() && !array_key_exists('blog',$array) ){
				//not the main blog, force single blog search
				$array['blog'] = get_current_blog_id();
			}elseif( empty($array['blog']) && get_site_option('dbem_ms_global_events') ) {
				$array['blog'] = false;
			}
		}
		$atts = parent::get_default_search($defaults, $array);
		$atts['full'] = ($atts['full']==true) ? 1:0;
		$atts['long_events'] = ($atts['long_events']==true) ? 1:0;
		return apply_filters('em_calendar_get_default_search', $atts, $array, $defaults);
	}
} 
add_action('init', array('EM_Calendar', 'init'));