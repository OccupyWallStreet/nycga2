<?php
			//send headers
			header('Content-type: text/calendar; charset=utf-8');
			header('Content-Disposition: inline; filename="events.ics"');
					
			$summary_format = str_replace ( ">", "&gt;", str_replace ( "<", "&lt;", get_option ( 'dbem_ical_description_format' ) ) );
			$EM_Events = new EM_Events( apply_filters('em_calendar_template_args',array( 'limit'=>get_option('dbem_ical_limit'), 'owner'=>false, 'orderby'=>'event_start_date' )) );
			
			$blog_desc = ent2ncr(convert_chars(strip_tags(get_bloginfo()))) . " - " . __('Calendar','dbem');
			
echo "BEGIN:VCALENDAR
VERSION:2.0
METHOD:PUBLISH
CALSCALE:GREGORIAN
PRODID:-//Events Manager//1.0//EN
X-WR-CALNAME:{$blog_desc}";
			/* @var $EM_Event EM_Event */
			foreach ( $EM_Events as $EM_Event ) {
			
				$summary = $EM_Event->output($summary_format,'ical');
				$summary = str_replace("\\","\\\\",ent2ncr(convert_chars(strip_tags($summary))));
				$summary = str_replace('"','DQUOTE',$summary);
				$summary = str_replace(';','\;',$summary);
				$summary = str_replace(',','\,',$summary);
				
				$search = array ('/"/', '/,/', '/\n/', '/\r/', '/:/', '/;/', '/\\//'); // evaluate as php
				$replace = array ('\"', '\\,', '\\n', '', '\:', '\\;', '\\\\');
				$description = $EM_Event->output('#_NOTES','ical');
				$description = preg_replace($search, $replace, $description); 
				$description = wordwrap($description); 
				$description = str_replace("\n","\n ",$description);
/*
				$description = str_replace("\\","\\\\",ent2ncr(convert_chars(strip_tags($description))));
				$description = str_replace('"','DQUOTE',$description);
				$description = str_replace(';','\;',$description);
				$description = str_replace(',','\,',$description);
*/
				
				$dateStart	= date('Ymd\THis\Z',$EM_Event->start - (60*60*get_option('gmt_offset')));
				$dateEnd = date('Ymd\THis\Z',$EM_Event->end - (60*60*get_option('gmt_offset')));
				$dateModified = date('Ymd\THis\Z', $EM_Event->modified);			
				
				$location = $EM_Event->output('#_LOCATION, #_ATT{Location Details}');
/* 				$location = str_replace(',','\,',ent2ncr(convert_chars(strip_tags($location)))); */
				$location = str_replace('"','DQUOTE',$location);
				$location = str_replace(';','\;',$location);
				$location = str_replace(',','\,',$location);
					
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
SUMMARY:{$summary}
DESCRIPTION:{$description}
LOCATION:{$location}
URL:{$EM_Event->output('#_EVENTURL')}
END:VEVENT";
//removed these
/*
CATEGORIES:".implode('\,',str_replace(',','\,',$categories))."
ORGANIZER:MAILTO:{$EM_Event->contact->user_email}
*/
			}
echo "
END:VCALENDAR";