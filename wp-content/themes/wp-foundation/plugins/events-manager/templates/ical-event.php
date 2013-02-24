<?php
/* @var $EM_Event EM_Event */
global $EM_Event;
//timezone
$tz = date_default_timezone_get(); // get current PHP timezone
//correct event start/end timestamps in correct timezone
date_default_timezone_set( get_option('timezone_string')); // set the PHP timezone to match WordPress
$EM_Event->start = strtotime($EM_Event->event_start_date." ".$EM_Event->event_start_time);
$EM_Event->end = strtotime($EM_Event->event_end_date." ".$EM_Event->event_end_time);
date_default_timezone_set( 'UTC'); // set the PHP timezone to UTC, we already calculated event dates

//send headers
header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: inline; filename="'.$EM_Event->event_slug.'.ics"');
		
$description_format = str_replace ( ">", "&gt;", str_replace ( "<", "&lt;", get_option ( 'dbem_ical_description_format' ) ) );
$blog_desc = ent2ncr(convert_chars(strip_tags(get_bloginfo()))) . " - " . __('Calendar','dbem');
			
echo "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//wp-events-plugin.com//".EM_VERSION."//EN";

	/* @var $EM_Event EM_Event */
	if($EM_Event->event_all_day){
		$dateStart	= ';VALUE=DATE:'.date('Ymd',$EM_Event->start); //all day
		$dateEnd	= ';VALUE=DATE:'.date('Ymd',$EM_Event->end + 86400); //add one day
	}else{
		$dateStart	= ':'.date('Ymd\THis\Z',$EM_Event->start);
		$dateEnd = ':'.date('Ymd\THis\Z',$EM_Event->end);
	}
	if( !empty($EM_Event->event_date_modified) && $EM_Event->event_date_modified != '0000-00-00 00:00:00' ){
		$dateModified = date('Ymd\THis\Z', strtotime($EM_Event->event_date_modified));
	}else{
	    $dateModified = date('Ymd\THis\Z', strtotime($EM_Event->post_modified));
	}

	//correct event start/end timestamps in correct timezone
	date_default_timezone_set( get_option('timezone_string')); // set the PHP timezone to match WordPress
	
	//Formats
	$description = $EM_Event->output($description_format,'ical');
	$description = str_replace("\\","\\\\",strip_tags($description));
	$description = str_replace(';','\;',$description);
	$description = str_replace(',','\,',$description);
	
	$location = $EM_Event->output('#_LOCATION', 'ical');
	$location = str_replace("\\","\\\\",strip_tags($location));
	$location = str_replace(';','\;',$location);
	$location = str_replace(',','\,',$location);
	
	$locations = array();
	foreach($EM_Event->get_categories() as $EM_Category){
		$locations[] = $EM_Category->name;
	}
	
echo "
BEGIN:VEVENT
DTSTART{$dateStart}
DTEND{$dateEnd}
DTSTAMP:{$dateModified}
SUMMARY:{$description}
LOCATION:{$location}
URL:{$EM_Event->output('#_EVENTURL')}
END:VEVENT";
echo "
END:VCALENDAR";
date_default_timezone_set($tz); // set the PHP timezone back the way it was