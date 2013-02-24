<?php
//timezone
$tz = date_default_timezone_get(); // get current PHP timezone
date_default_timezone_set( get_option('timezone_string')); // set the PHP timezone to match WordPress
//send headers
header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: inline; filename="events.ics"');
		
$description_format = str_replace ( ">", "&gt;", str_replace ( "<", "&lt;", get_option ( 'dbem_ical_description_format' ) ) );
if( !empty($_REQUEST['event_id']) ){
	$EM_Events = array(new EM_Event($_REQUEST['event_id']));
}else{
	$EM_Events = EM_Events::get(apply_filters('em_calendar_template_args',array( 'limit'=>get_option('dbem_ical_limit'), 'owner'=>false, 'orderby'=>'event_start_date', 'scope' => get_option('dbem_ical_scope') )) );
}		
$blog_desc = ent2ncr(convert_chars(strip_tags(get_bloginfo()))) . " - " . __('Calendar','dbem');
			
echo "BEGIN:VCALENDAR
VERSION:2.0
CALSCALE:GREGORIAN
METHOD:PUBLISH
X-WR-TIMEZONE:UTC
X-WR-CALDESC:
BEGIN:VTIMEZONE
TZID:America/New_York
X-LIC-LOCATION:America/New_York
BEGIN:DAYLIGHT
TZOFFSETFROM:-0500
TZOFFSETTO:-0400
TZNAME:EDT
DTSTART:19700308T020000
RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=2SU
END:DAYLIGHT
BEGIN:STANDARD
TZOFFSETFROM:-0400
TZOFFSETTO:-0500
TZNAME:EST
DTSTART:19701101T020000
RRULE:FREQ=YEARLY;BYMONTH=11;BYDAY=1SU
END:STANDARD
END:VTIMEZONE
PRODID:-//wp-events-plugin.com//".EM_VERSION."//EN";

foreach ( $EM_Events as $EM_Event ) {
	/* @var $EM_Event EM_Event */
	date_default_timezone_set('UTC'); // set the PHP timezone to UTC, we already calculated event    
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
	date_default_timezone_set( get_option('timezone_string')); // set the PHP timezone to match WordPress
	
	//formats
	
	$eventname = $EM_Event->output('#_EVENTNAME','ical');
	$eventname = str_replace("\\","\\\\",strip_tags($eventname));
	$eventname = str_replace(';','\;',$eventname);
	$eventname = str_replace(',','\,',$eventname);
	$eventname = str_replace('\n','\\n,',$eventname);
	
	$description = $EM_Event->output('#_EVENTNOTES','ical');
	$description = str_replace(array("\r\n", "\r", "\n"), "", $description);
	
	$location = $EM_Event->output('#_LOCATIONNAME, #_LOCATIONFULLLINE - #_ATT{Location Details}', 'ical');
	$location = str_replace("\\","\\\\",strip_tags($location));
	$location = str_replace(';','\;',$location);
	$location = str_replace(',','\,',$location);
	
	$locations = array();
	foreach($EM_Event->get_categories() as $EM_Category){
		$locations[] = $EM_Category->name;
	}
	$UID = 
				$UID = $EM_Event->output('#_EVENTURL');
				//$UID = $EM_Event->output('#_EVENTURL');
				$UID = str_replace('/','',$UID);
				$UID = str_replace(':','',$UID);
				$UID = rtrim($UID,"/");
				//sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        //mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
        // 16 bits for "time_mid"
       // mt_rand( 0, 0xffff ),
        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
       // mt_rand( 0, 0x0fff ) | 0x4000,
        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        //mt_rand( 0, 0x3fff ) | 0x8000,
        // 48 bits for "node"
        //mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		    //);
echo "
BEGIN:VEVENT
UID:{$UID}
DTSTART{$dateStart}
DTEND{$dateEnd}
DTSTAMP:{$dateModified}
SUMMARY:{$eventname}
DESCRIPTION:{$description}
LOCATION:{$location}
URL:{$EM_Event->output('#_EVENTURL')}
END:VEVENT";
}
echo "
END:VCALENDAR";
date_default_timezone_set($tz); // set the PHP timezone back the way it was