<?php 

function startswith($ahaystack, $aneedle){ 
    return strpos($ahaystack, $aneedle) === 14;
}

function cleaner($url) {
  $U = explode(' ',$url);

  $W =array();
  foreach ($U as $k => $u) {
    if (stristr($u,'http') || (count(explode('.',$u)) > 1)) {
      unset($U[$k]);
      return cleaner( implode(' ',$U));
    }
  }
  return implode(' ',$U);
}


date_default_timezone_set('EST');


/* *************************************************************************************************
*
* 											TwitterRSS
*
****************************************************************************************************
* TwitterRSS is a PHP script that will read your RSS feed and display your recent entries. You can
* limit how many entries are shown and customise all the HTML that controls its display. 
*
* 	Author:		Kerison Leigh (Keija) <talkback@kerison.com>
*	SiteURL:	http://code.kerison.com/twitter
*	@Twitter:	http://twitter.com/keija
*
* The sections you can customise are split into three. 
*
* The first section asks you for your feed URL and lets you correct any feed/time inaccuracies, the
* second and third give you more customisation and control over the way the data is displayed.
****************************************************************************************************
*  
* THE FEED URL IS THE ONLY RESOURCE THIS SCRIPT NEEDS IN ORDER TO FUNCTION. EVERYTHING ELSE IS
* OPTIONAL BUT YOU *MUST* FILL THIS IN. Your RSS feed is linked at the bottom of your 'home' page
* and will look something like this: http://twitter.com/statuses/user_timeline/XXXXXX.rss
*
* ...where XXXXXX is your user number (example: mine is 5794542).
*
* Additionally, you can limit the number of twits displayed.
*/

	$FEED_URL = 'http://www.twitter.com/statuses/user_timeline/occupywallstnyc.rss';
	$NUM_ITEMS = '7';

/**
* For some reason, Twitter feeds *sometimes* seem to reflect incorrect times. If yours isn't
* displaying correct times, find out how many hours your feed is 'off' by and edit the following
* values. The script will automatically adjust the displayed times for you.
*
* If your times are displaying correctly, leave these blank.
*
*	$FEED_PLUS_MINUS - Set this to 'P' for '+' and 'M' for '-'
*	$FEED_HOUR_OFFSET - Set this to however many hours your times are + or -
*
* e.g. 'P' & '5' for +5, 'M' & '2' for -2.
*/

	$FEED_PLUS_MINUS	= '';
	$FEED_HOUR_OFFSET	= '';

/**
* Basic Customisation:
*
*	$USE_UFTIME -- 'Y' or 'N'
*	Use 'user-friendly' (relative) times? e.g. an hour ago, 45 minutes ago.
*
*	$USE_24HR_TIME -- 'Y' or 'N'
*	Use 24 hour format (02:00, 14:00) or 12 hour (2:00 AM, 2:00 PM).
*
*	$HIDE_DATE_HEADER -- 'Y' or 'N'
*	You can set this to 'Y' to remove the date headers if you want to.
*
*	$HIDE_TWITTER_LINK -- 'Y' or 'N'
*	You can set this to 'Y' to hide the 'More' link that links to your Twitter profile. If you
*	want to keep the link but would prefer different link text, scroll a bit further down.
*
*	$USE_ALT_LICOLOR -- 'Y' or 'N'
*	With the default formatting, you can choose to have alternating item colours to break up the
*	text. Set this to 'Y' to use alternates and 'N' to stick with one colour.
*
*	$CUST_FMT_TIME -- Example: = '(%TIME%)';
*	Let's you change the format of the timestamp. In the example above, brackets have been added to
*	change the timestamp from XX:XX to (XX:XX). If you use this, you MUST include %TIME% where you
*	want the time to be placed. EXACTLY LIKE THAT. Percent sign, TIME, percent sign.
*/

	$USE_UFTIME			= 'N';
	$USE_24HR_TIME		= 'N';
	
	$HIDE_DATE_HEADER	= 'Y';
	$HIDE_TWITTER_LINK	= 'Y';

	$HIGHLIGHT_FIRST	= 'N';
	$USE_ALT_LICOLOR	= 'N';
	
	// INCLUDE THE %TIME% TAG! [see above if you skipped the notes!]
	$CUST_FMT_TIME		= " . next tweet . ";






/**
* Advanced Customisation:
*
* The script automatically displays the data in list format, however you can customise this to
* whatever you wish to use. i.e. if you want to use <p></p> tags and <br />'s, that's fine!
*
* Brief information is given below but it's recommended that you have a quick glance at the
* customisation section of the website if you get stuck.
*
*	$CUST_FMT_DATA_*
*	These tags bracket ALL the data. The defaults are '<ul id="twit-twit">...</ul>'
*
*	$CUST_FMT_TWIT_DATE*
*	These tags bracket the date. The defaults are '<li class="twit-date">...</li>'
*
*	$CUST_FMT_TWIT_*
*	These tags bracket the actual twitter text and timestamp. The defaults are '<li>...</li>';
*
*	$CUST_FMT_DATE -- Example: = "m.d.Y" or = "M, jS"
*	In the above examples, if the date was February 1st, 2007, this would display:
*
*		m.d.Y	-> 02.01.2007
*		M jS	-> Feb 1st
*
*	The syntax used is PHP's date syntax, which you can find here: http://php.net/date
*
*	$CUST_FMT_MORE_TEXT
*	If you want to change the text of the link back to your Twitter page, edit this.
*
*	$UFT_LINK_TEXT_*
*	You can use these four settings to alter the text on your user-friendly links. For the multiple
*	options (they end in _PLU), you need to make sure you include the relevant value/place-holder,
*	same as before. The tags you need to include are %NUM_HOURS% and/or %NUM_MINS%
*
*	Example Set:
*
*		$UFT_LINK_TEXT_HOUR_SGL	= '(an hour ago)';
*		$UFT_LINK_TEXT_HOUR_PLU	= '(%NUM_HOURS% hours ago)';
*
*		$UFT_LINK_TEXT_MINS_SGL	= '(a minute ago)';
*		$UFT_LINK_TEXT_MINS_PLU	= '(%NUM_MINS% minutes ago)';
*
*	Further information can be found at "http://code.kerison.com/twitter" if required.
*/

	// brackets all the data.
	$CUST_FMT_DATA_PRE	= '';
	$CUST_FMT_DATA_POST	= '';

	// brackets the date.
	$CUST_FMT_TWIT_DATE_PRE = '';
	$CUST_FMT_TWIT_DATE_POST = '';

	// brackets the twitter text and time.
	$CUST_FMT_TWIT_PRE	= '';
	$CUST_FMT_TWIT_POST	= '';

	// custom format date and 'more' link text.
	$CUST_FMT_DATE = '';
	$CUST_FMT_MORE_TEXT = '';

	// uft options
	$UFT_LINK_TEXT_HOUR_SGL	= '';
	$UFT_LINK_TEXT_HOUR_PLU	= '';

	$UFT_LINK_TEXT_MINS_SGL	= '%NUM_MINS% minutes ago';
	$UFT_LINK_TEXT_MINS_PLU	= '';


/***************************************************************************************************
****************************************************************************************************
****************************************************************************************************
***********************************                           **************************************
***********************************  THAT'S IT, YOU'RE DONE!  **************************************
***********************************                           **************************************
****************************************************************************************************
****************************************************************************************************
****************************************************************************************************
****************************************************************************************************
****************************************************************************************************
**************                                                                       ***************
**************                 ALL USER-DEFINABLE OPTIONS ARE ABOVE                  ***************
**************   YOU DO NOT NEED TO MODIFY ANYTHING IN THIS FILE BEYOND THIS POINT   ***************
**************                                                                       ***************
****************************************************************************************************
****************************************************************************************************
****************************************************************************************************
****************************************************************************************************
* BEGIN FUNCTIONS... *******************************************************************************
***************************************************************************************************/

// takes information set by the user and uses it to offset/correct any feed time inaccuracies.
function formatTimestamp($twit_time) {

	global $USE_24HR_TIME, $CUST_FMT_TIME;
//		if ( $killtime == 'Y' ) {
//			$twit_time = "";
//			}
	if ( $USE_24HR_TIME == 'Y' ) {
		$time_str = strftime("%H:%M", strtotime($twit_time));
	} else {
		$time_str = strftime("%I:%M %p", strtotime($twit_time));
	}
	
	if ( $CUST_FMT_TIME != '' ) {
		$custom_time = str_replace("%TIME%", "$time_str", $CUST_FMT_TIME);
		return $custom_time; unset($custom_time);
	} else {
		return $time_str;
	}

	unset($time_str);
}


// corrects any feed/time inaccuracies.
function offsetTimeIfNeeded($date) {

	global $FEED_PLUS_MINUS, $FEED_HOUR_OFFSET, $last_offset_date;

	if ( $date == $last_offset_date ) {} else {	
		if ( ($FEED_PLUS_MINUS != '') && ($FEED_HOUR_OFFSET != '') ) {

			if ( strtoupper(substr($FEED_PLUS_MINUS,0,1)) == 'P' ) {
				$offset = strtotime('+'.$FEED_HOUR_OFFSET.' hours', $date);
			} elseif ( strtoupper(substr($FEED_PLUS_MINUS,0,1)) == 'M' )  {
				$offset = strtotime('-'.$FEED_HOUR_OFFSET.' hours', $date);
			} else {
				$offset = $date;
			}

			$corrected = $offset;

		} else {
			$corrected = $date;
		}


		$hour = date("H", $corrected); $mins = date("i", $corrected);
		$day = date("d", $corrected); $mon = date("m", $corrected); $year = date("Y", $corrected);

		return mktime($hour,$mins,'0',$mon,$day,$year);

		$date = ''; $offset = ''; $corrected = '';
	}

}


// format the 'more' link that links back to twitter.com/your_name.
function formatTwitterMoreLink($twit_url,$style) {

	global $CUST_FMT_MORE_TEXT;

	if ( $CUST_FMT_MORE_TEXT != '' ) {
		$text = $CUST_FMT_MORE_TEXT;
	} else {
		$text = 'More &raquo;';
	}
	
	if ( strtoupper($style) == 'P' ) {
		$url = '';
	} else {
		$url = '';
	}
	
	$url .= "trim($twit_url)";

	if ( strtoupper($style) == 'P' ) {
		$url .= '</p>';
	} else {
		$url .= '</li>';
	}
	
	print $url; $url = '';

}


// if the date hasn't been printed to screen yet, print it. Otherwise, skip printing.
function printTwitWithOptDate($raw_date,$twit_date,$prev_date) {
	
	global $CUST_FMT_TWIT_DATE_PRE, $CUST_FMT_TWIT_DATE_POST, $CUST_FMT_DATE, $first_skipped;
	
	if ( $twit_date == $prev_date ) {} else { $date_str = '';
	
		if ( $first_skipped != true ) {
			$first_skipped = true;
		} else {
			if ( ($CUST_FMT_TWIT_DATE_POST != '') || ($CUST_FMT_TWIT_DATE_POST == 'BLANK') ) {} 
			else {
				$date_str .= ''."\n";
			}
		}

		if ( ($CUST_FMT_TWIT_DATE_PRE != '') || ($CUST_FMT_TWIT_DATE_PRE == 'BLANK') ) {
			if ( $CUST_FMT_TWIT_DATE_PRE != 'BLANK' ) $date_str .= $CUST_FMT_TWIT_DATE_PRE;
		} else {
			$date_str .= '';
		}
		
		if ( $CUST_FMT_DATE != '' ) {
			$date_str .= date($CUST_FMT_DATE, $raw_date);
		} else {
			$date_str .= date("D, M jS", $raw_date);
		}

		if ( ($CUST_FMT_TWIT_DATE_PRE != '') || ($CUST_FMT_TWIT_DATE_PRE == 'BLANK') ) {} else {
			$date_str .= '  '."\n";
		}

		if ( ($CUST_FMT_TWIT_DATE_POST != '') || ($CUST_FMT_TWIT_DATE_POST == 'BLANK') ) {
			if ( $CUST_FMT_TWIT_DATE_POST != 'BLANK' ) $date_str .= $CUST_FMT_TWIT_DATE_POST;
		}
		
		return $date_str;
	}
	
	unset($raw_date); unset($twit_date); unset($prev_date); $date_str = '';
}


// calculate the difference between the Twit timestamp and the current time...
function calcDiffsForUft($twit_date,$curr_date) {

	$diff_info = array();
	
	$diff_info['twitstamp'] = $twit_date; $diff_info['currstamp'] = $curr_date;
	
	if ( (strlen($diff_info['twitstamp']) > 0) && (strlen($diff_info['currstamp']) > 0) ) {
	
		$diff = $diff_info['currstamp'] - $diff_info['twitstamp'];
		
		if ( $days	= intval((floor($diff/86400))))	$diff = $diff % 86400;
		if ( $hours	= intval((floor($diff/3600)))) 	$diff = $diff % 3600;
		if ( $mins	= intval((floor($diff/60))))	$diff = $diff % 60;
			 $secs 	= intval($diff);
		
		$diff_info['days'] = $days; $diff_info['hours'] = $hours;
		$diff_info['mins'] = $mins; $diff_info['secs'] = $secs;
		
		return $diff_info; 
	
	} else {
		return $error = true;
	}
	
	$diff_info = ''; $diff = ''; $days = ''; $hours = ''; $mins = ''; $secs = '';

}


// format the timestamp into a user-friendly (relative) format if the user wishes...
function formatUfTime($twit_date,$curr_date) {

	global $UFT_LINK_TEXT_HOUR_SGL, $UFT_LINK_TEXT_HOUR_PLU;
	global $UFT_LINK_TEXT_MINS_SGL,$UFT_LINK_TEXT_MINS_PLU;

	$data = calcDiffsForUft($twit_date,$curr_date);
	
	if ( $data['days'] == 0 ) {
		if ( $data['hours'] == 0 ) {
			if ( ($data['mins'] == 0) || ($data['mins'] == 1) ) { 
				if ( strlen($UFT_LINK_TEXT_MINS_SGL) > 0 ) {
					$uft .= $UFT_LINK_TEXT_MINS_SGL;
				} else {
					$uft .= '1 minute ago';
				}
			} else { 
				if ( strlen($UFT_LINK_TEXT_MINS_PLU) > 0 ) {
					$uft .= str_replace("%NUM_MINS%", $data['mins'], $UFT_LINK_TEXT_MINS_PLU); 
				} else {
					$uft .= $data['mins'].' minutes ago';
				}
			}
		} else { 
			if ( $data['hours'] == 1 ) { 
				if ( strlen($UFT_LINK_TEXT_HOUR_SGL) > 0 ) {
					$uft .= $UFT_LINK_TEXT_HOUR_SGL;
				} else {
					$uft .= '1 hour ago'; 
				}
			} else { 
				if ( strlen($UFT_LINK_TEXT_HOUR_PLU) > 0 ) {
					$uft .= str_replace("%NUM_HOURS%", $data['hours'], $UFT_LINK_TEXT_HOUR_PLU);
				} else {
					$uft .= $data['hours'].' hours ago';
				}
			}
		}
		
	} else {
		$uft .= formatTimestamp(date("Hi", $twit_date));
	}
	
	print $uft;
	
	$data = ''; $uft = ''; $twit_date = ''; $curr_date = '';
}


// startElement for processing the RSS feed's XML.
function startElement($p, $name, $attribs) {

	global $item, $currElement, $title;

	$currElement = strtoupper($name);

	if ( $currElement == 'ITEM' ) {
		$item = true;
	} 
	
}


// endElement for processing the RSS feed's XML.
function endElement($p, $name) {

	global $CUST_FMT_DATA_PRE, $CUST_FMT_DATA_POST, $CUST_FMT_TWIT_PRE, $CUST_FMT_TWIT_POST;
	global $USE_UFTIME, $USE_ALT_LICOLOR, $HIDE_DATE_HEADER, $HIDE_TWITTER_LINK, $USE_CUST_TSTAMP;
	global $item, $title, $description, $pubDate, $link, $prev_date, $display_name, $is_alt, $i;
	global $HIGHLIGHT_FIRST, $first_defined, $NUM_ITEMS;
	
	$hdr_str = ''; $twit_str = ''; $time_str = ''; 
	
	
$killtime = "N";
if (startswith($description, '@')) {
//$description = ' ';
$killtime = "Y";
	
}
	
//remove http links
$description2 = cleaner($description);

	
	$badchars = array(">", "<", "&amp;", "/", "&", "\\","ó", "é", '"', 'lt;3', 'lt;', 'RT ', '#OWS', '@OccupyWallStNYC', '#', '@');
	  $replacechars = array(" ", " ", " ", " ", " ", " ", "o", "e", " ", " ", " ", ' ',' ', ' ', ' hash tag ', ' at sign ');
	  $output2 = str_replace($badchars, $replacechars, $description2);
	  $output3 = preg_replace('/[^(\x20-\x7F)]*/','', $output2);
	  $description = substr($output3,0,4700);
	  
	
//	$tweet = $description;
//	while (($pos = strpos($tweet, '@')) !== false) {
//	  if ($spacepos = strpos($tweet, ' ', $pos)) {
//	    $tweet=substr_replace($tweet, '', $pos, $spacepos-$pos+1);
//	  } else {
//	    $tweet=substr_replace($tweet, '', $pos);
//	  }
//	}
//	$description=rtrim($tweet);
	
//	$tweet = $description;
//	while (($pos = strpos($tweet, '#')) !== false) {
//	  if ($spacepos = strpos($tweet, ' ', $pos)) {
//	    $tweet=substr_replace($tweet, '', $pos, $spacepos-$pos+1);
//	  } else {
//	    $tweet=substr_replace($tweet, '', $pos);
//	  }
//	}
//	$description=rtrim($tweet);

	
	if ( !$i ) $i = 0;

	// gets the user's name from their feed & stores it. If the user changes their displayed name
	// the script will pick it up automatically. &also used to snip the author name from the text.
	if ( (strtoupper($name) == 'TITLE') && ($item != true) ) {
		$display_name = trim($title).': ';
	}

	// final formatting & printing to screen is done here...
	if ( strtoupper($name) == 'ITEM' ) {
	
		$i++; // keeps track of how many items we've cycled through.
		
		if ( !$NUM_ITEMS ) $NUM_ITEMS = 20;
		
		if ( $i <= $NUM_ITEMS ) { // if we've haven't reached the limit yet, process the data.
		
		// print the date if required & it hasn't already been printed...
		$date = date("Ymd", $pubDate); 
		$hdr_str .= printTwitWithOptDate($pubDate,$date,$prev_date);

		// print the date, unless the user has chosen to hide it...
		if ( $HIDE_DATE_HEADER != 'Y' ) {
			print $hdr_str;
		}

		// if the user has chosen to use their own tags, substitute them...		
		if ( ($CUST_FMT_TWIT_PRE != '') || ($CUST_FMT_TWIT_PRE == 'BLANK') ) {				
			if ( $CUST_FMT_TWIT_PRE != 'BLANK' ) print $CUST_FMT_TWIT_PRE;
		} else {
		
			// if you use the $HIGHLIGHT_FIRST option, you cannot use alternates.
			if ( $HIGHLIGHT_FIRST == 'Y' ) { print " . ";
				
				if ( $first_defined != true ) {
					print '';
					$first_defined = true;
				} else {
					print '';
				}
				
			// alternating colour is only currently configured with the default list format.
			} elseif ( $USE_ALT_LICOLOR == 'Y' ) { if ( !$is_alt ) $is_alt = false; print " ";
		
				if ( $is_alt == true ) { // we're using alternate colours...
					print '';
					$is_alt = false;
				} else {
					print '';
					$is_alt = true;
				}
			
			} else { print "  "; } // or choosing to use just one...
		}
		
		print str_replace($display_name,'',trim($description)).' ';
	//	print trim($link);
		
			if ( $USE_UFTIME == 'Y' ) {
				formatUfTime($pubDate,time());
			} else {
				print formatTimestamp(date("Hi", $pubDate));
			}
			
		print '';
		
		// if the user has chosen to use their own tags, substitute them...
		if ( ($CUST_FMT_TWIT_POST != '') || ($CUST_FMT_TWIT_POST == 'BLANK') ) {
			if ( $CUST_FMT_TWIT_POST != 'BLANK' ) print $CUST_FMT_TWIT_POST;
		} else {
			print ' . ';
		} print " . ";
		
		$prev_date = $date;
		
		$title = ''; $pubDate = ''; $link = ''; $date = ''; $description = ''; $item = false;
		
		} else {} // ends the 'number of items' control if.

	}

}


// handles & processes data from the XML feed.
function characterDataHandler($p, $data) {

	global $item, $currElement, $title, $description, $pubDate, $link, $user_url;
		
	if ( $item ) {
		switch($currElement) {

			case "DESCRIPTION":
			$description .= $data;
			break;

			case "PUBDATE":
			$pubDate .= offsetTimeIfNeeded(strtotime($data));
			break;

			case "LINK":
			$link .= $data;
			break;

		}
	}
	
	
	// get users name from the feed, so it can't be wrong.
	if ( ($currElement == 'TITLE') && ($item != true) ) {
		$temp = ereg_replace('Twitter / ', '', $data);
		$title .= $temp;
	}
	
	
	// get users url from the feed, so it can't be wrong.
	if ( ($currElement == 'LINK') && ($item != true) ) {
		$user_url .= $data;
	}
	
}



/***************************************************************************************************
* FUNCTIONS HAVE BEEN DEFINED. BEGIN PARSING XML DATA HERE...                                      *
***************************************************************************************************/

$p = xml_parser_create(); // let's parse an XML feed!

xml_set_element_handler($p, "startElement", "endElement");
xml_set_character_data_handler($p, "characterDataHandler");

if ( !($h = curl_init($FEED_URL)) ) { // can't get the file with cURL...

	if ( !($h = fopen($FEED_URL, "r")) ) { // ...and can't get it with fopen() either.
		die("<p>ERROR: Unable to read contents of RSS feed, please check the URL.</p>");
	} else {

		// user-specified format overrides the default...
		if ( ($CUST_FMT_DATA_PRE != '') || ($CUST_FMT_DATA_PRE == 'BLANK') ) {
			if ( $CUST_FMT_DATA_PRE != 'BLANK' ) print $CUST_FMT_DATA_PRE;
		} else {
			print ''." . ";
		}

		// cycle through the data...	
		while ( $data = fread($h, 4096)) {
			if ( !xml_parse($p, $data) ) {
				die("Error in feed.");
			}
		}
	
		// user-specified format overrides the default...
		if ( ($CUST_FMT_DATA_POST != '') || ($CUST_FMT_DATA_POST == 'BLANK') ) {
			// print a link to user's twitter page, unless they've set $HIDE_TWITTER_LINK to 'Y'.
			if ( $HIDE_TWITTER_LINK != 'Y' ) formatTwitterMoreLink($user_url,'p');
			if ( $CUST_FMT_DATA_POST != 'BLANK' ) print "\n".$CUST_FMT_DATA_POST;
		} else {
			print ''."\n";
			// print a link to user's twitter page, unless they've set $HIDE_TWITTER_LINK to 'Y'.
			if ( $HIDE_TWITTER_LINK != 'Y' ) formatTwitterMoreLink($user_url,'li');
			print " . ".'';
		}
	}

} else {
	
	// user-specified format overrides the default...
	if ( ($CUST_FMT_DATA_PRE != '') || ($CUST_FMT_DATA_PRE == 'BLANK') ) {
		if ( $CUST_FMT_DATA_PRE != 'BLANK' ) print $CUST_FMT_DATA_PRE;
	} else {
		print ''." . ";
	}

	// set cURL options...
	curl_setopt($h, CURLOPT_HEADER, false);
	curl_setopt($h, CURLOPT_RETURNTRANSFER, true);

	$data = curl_exec($h);

	curl_close($h);
	
	if ( !xml_parse($p, $data) ) {
		die("Error in feed.");
	}
	
	// user-specified format overrides the default...
	if ( ($CUST_FMT_DATA_POST != '') || ($CUST_FMT_DATA_POST == 'BLANK') ) {
		// print a link to user's twitter page, unless they've set $HIDE_TWITTER_LINK to 'Y'.
		if ( $HIDE_TWITTER_LINK != 'Y' ) formatTwitterMoreLink($user_url,'p');
		if ( $CUST_FMT_DATA_POST != 'BLANK' ) print " . ".$CUST_FMT_DATA_POST;
	} else {
		print ' '." ";
		// print a link to user's twitter page, unless they've set $HIDE_TWITTER_LINK to 'Y'.
		if ( $HIDE_TWITTER_LINK != 'Y' ) formatTwitterMoreLink($user_url,'li');
		print " . ".' ';
	}

}

xml_parser_free($p);

/***************************************************************************************************
*                                          ALL DONE!                                               *                                             
***************************************************************************************************/
?>
