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
	$UID = sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),
        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,
        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,
        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
echo "
BEGIN:VEVENT
UID:{$UID}
DTSTART{$dateStart}
DTEND{$dateEnd}
DTSTAMP:{$dateModified}
SUMMARY:{$description}
LOCATION:{$location}
URL:{$EM_Event->output('#_EVENTURL')}
END:VEVENT";
}
echo "
END:VCALENDAR";
date_default_timezone_set($tz); // set the PHP timezone back the way it was