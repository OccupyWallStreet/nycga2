<?php

/**
 * Helper function for parsing dates.
 * 
 * Possible inputs:
 *
 * NOW()
 * FUTURE(1000)  (1000 seconds)
 * PAST(1000)
 * TODAY()    (time at 00:00 today)
 * FUTURE_DAY(1)
 * PAST_DAY(1)
 * THIS_MONTH()   (time at 00:00 on first day of this month)
 * FUTURE_MONTH(1)
 * PAST_MONTH(1)
 * THIS_YEAR()   (time at 00:00 on first day of this year)
 * FUTURE_YEAR(1)
 * PAST_YEAR(1)
 * DATE(dd,mm,yyyy)    eg. DATE(03,04,2012)
 * 
 * @param int timestamp $date_format
 */
function wpv_filter_parse_date($date_format) {
	$occurences = preg_match_all('/(\\w+)\(([\\d,-]*)\)/', $date_format, $matches);
	
	$resulting_date = false;
	
	if($occurences > 0) {
		for($i = 0; $i < $occurences; $i++) { 
			$date_func = $matches[1][$i];
			$date_value = $matches[2][$i];
			
			switch(strtoupper($date_func)) {
					case "NOW": $resulting_date = time(); break;
					case "TODAY": $resulting_date = mktime(0, 0, 0, date('m'), date('d'), date('Y')); break;
					case "FUTURE_DAY": $resulting_date = mktime(0, 0, 0, date('m'), date('d') + $date_value, date('Y')); break;
					case "PAST_DAY": $resulting_date = mktime(0, 0, 0, date('m'), date('d') - $date_value, date('Y')); break;
					case "THIS_MONTH": $resulting_date = mktime(0, 0, 0, date('m'), 1, date('Y')); break;
					case "FUTURE_MONTH": $resulting_date = mktime(0, 0, 0, date('m') + $date_value, 1, date('Y')); break;
					case "PAST_MONTH": $resulting_date = mktime(0, 0, 0, date('m') - $date_value, 1, date('Y')); break;
					case "THIS_YEAR": $resulting_date = mktime(0, 0, 0, 1, 1, date('Y')); break;
					case "FUTURE_YEAR": $resulting_date = mktime(0, 0, 0, 1, 1, date('Y') + $date_value); break;
					case "PAST_YEAR": $resulting_date = mktime(0, 0, 0, 1, 1, date('Y') - $date_value); break;
					case "SECONDS_FROM_NOW": $resulting_date = time() + 1000 * $date_value; break;
					case "MONTHS_FROM_NOW": $resulting_date = mktime(0, 0, 0, date('m') + $date_value, date('d'), date('Y')); break;
					case "YEARS_FROM_NOW": $resulting_date = mktime(0, 0, 0, date('m'), date('d'), date('Y') + $date_value); break;
					case "DATE": $date_parts = explode(',', $date_value); $resulting_date = mktime(0, 0, 0, $date_parts[1], $date_parts[0], $date_parts[2]); break;  
				}
				
				$date_format = str_replace($matches[0][$i], $resulting_date, $date_format);
		}
	} 
	
	return $date_format;
}