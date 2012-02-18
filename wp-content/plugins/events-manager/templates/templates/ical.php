<?php
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
			foreach ( $EM_Events as $EM_Event ) {
			
				$description = $EM_Event->output($description_format,'ical');
				$description = str_replace("\\","\\\\",ent2ncr(convert_chars(strip_tags($description))));
				//$description = str_replace('"','DQUOTE',$description);
				$description = str_replace(';','\;',$description);
				$description = str_replace(',','\,',$description);
				
				if($EM_Event->event_all_day && $EM_Event->event_start_date == $EM_Event->event_end_date){
					$dateStart	= date('Ymd\T000000',$EM_Event->start - (60*60*get_option('gmt_offset')));
					$dateEnd	= date('Ymd\T000000',$EM_Event->start + 60*60*24 - (60*60*get_option('gmt_offset')));
				}else{
					$dateStart	= date('Ymd\THis\Z',$EM_Event->start - (60*60*get_option('gmt_offset')));
					$dateEnd = date('Ymd\THis\Z',$EM_Event->end - (60*60*get_option('gmt_offset')));
				}
				$dateModified = date('Ymd\THis\Z', $EM_Event->modified);			
				
				$location		= $EM_Event->output('#_LOCATION');
				$location		= str_replace(',','\,',ent2ncr(convert_chars(strip_tags($location))));
				//$location = str_replace('"','DQUOTE',$location);
				$location = str_replace(';','\;',$location);
				$location = str_replace(',','\,',$location);
				
				$locations = array();
				foreach($EM_Event->get_categories() as $EM_Category){
					$locations[] = $EM_Category->name;
				}
	
//FIXME we need a modified date for events
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
//removed these
/*
CATEGORIES:".implode('\,',str_replace(',','\,',$categories))."
ORGANIZER:MAILTO:{$EM_Event->get_contact()->user_email}
*/
			}
echo "
END:VCALENDAR";