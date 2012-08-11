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
METHOD:PUBLISH
CALSCALE:GREGORIAN
PRODID:-//Events Manager//1.0//EN
X-WR-CALNAME:{$blog_desc}";

/* @var $EM_Event EM_Event */
$offset = 3600 * get_option('gmt_offset');
foreach ( $EM_Events as $EM_Event ) {
	$description = $EM_Event->output($description_format,'ical');
	$description = str_replace("\\","\\\\",ent2ncr(convert_chars(strip_tags($description))));
	//$description = str_replace('"','DQUOTE',$description);
	$description = str_replace(';','\;',$description);
	$description = str_replace(',','\,',$description);
	
	$start_offset = ( date('I', $EM_Event->start) ) ? 0 : 3600;
	$end_offset = ( date('I', $EM_Event->end) ) ? 0 : 3600;
	
	if($EM_Event->event_all_day && $EM_Event->event_start_date == $EM_Event->event_end_date){
		$dateStart	= date('Ymd\T000000',$EM_Event->start); //all day
		$dateEnd	= date('Ymd\T000000',$EM_Event->start + 86400); //add one day
	}else{
		$dateStart	= date('Ymd\THis\Z',$EM_Event->start - $offset + $start_offset);
		$dateEnd = date('Ymd\THis\Z',$EM_Event->end - $offset + $end_offset);
	}
	if( !empty($EM_Event->event_date_modified) ){
		$dateModified = date('Ymd\THis\Z', strtotime($EM_Event->event_date_modified) - $offset + $start_offset);
	}else{
		$dateModified = date('Ymd\THis\Z', strtotime($EM_Event->event_date_created) - $offset + $start_offset);
	}	
	
	$location		= $EM_Event->output('#_LOCATION', 'ical');
	$description = str_replace("\\","\\\\",ent2ncr(convert_chars(strip_tags($description))));
	//$location = str_replace('"','DQUOTE',$location);
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
DTSTART:{$dateStart}
DTEND:{$dateEnd}
DTSTAMP:{$dateModified}
SUMMARY:{$description}
LOCATION:{$location}
URL:{$EM_Event->output('#_EVENTURL')}
END:VEVENT";
}
echo "
END:VCALENDAR";
date_default_timezone_set($tz); // set the PHP timezone back the way it was