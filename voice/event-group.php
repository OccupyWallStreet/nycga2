<?php

$timenow = date("h:i A");

include("/var/www/nycga.net/web/env.php");

//sanitize get input function
class MysqlStringEscaper
{
    function __get($value)
    {
        return mysql_real_escape_string($value);
    }
}
$str = new MysqlStringEscaper;

  // Get the search variable from URL

  $var = @$_GET['groupid'] ;
  $trimmed = trim($var); //trim whitespace from the stored variable

// rows to return

//connect to your database ** EDIT REQUIRED HERE **
mysql_connect(constant("DB_HOST"),constant("DB_USER"),constant("DB_PASSWORD")); //(host, username, password)

//specify database ** EDIT REQUIRED HERE **
mysql_select_db(constant("DB_NAME")) or die("Unable to select database"); //select which database we're using

//mysql_query("set time_zone = '-5:00'");


 if ($trimmed == "9999") {
  
   $gaquery2 = "SELECT wp_em_events.event_name, DATE_FORMAT(wp_em_events.event_start_time,'%l:%i%p') as StartTime, DATE_FORMAT(wp_em_events.event_end_time,'%l:%i%p') as EndTime, DATE_FORMAT(wp_em_events.event_start_date,'%W %M %D') as StartDate, wp_em_events.location_id, wp_em_locations.location_name as LocationName, wp_em_locations.location_address as LocationAddress, wp_em_events.group_id, wp_bp_groups.name as GroupName, wp_em_categories.category_name as CategoryName " .
"FROM wp_em_events LEFT JOIN wp_em_locations ON wp_em_events.location_id = wp_em_locations.location_id " .
"LEFT JOIN wp_bp_groups ON wp_em_events.group_id = wp_bp_groups.id " .
"LEFT JOIN wp_em_categories ON wp_em_events.event_category_id = wp_em_categories.category_name " .
"WHERE  event_end_time >= CURTIME( ) and (event_start_date >= CURDATE() and event_start_date <= CURDATE( ) + 20 ) and (event_name like '%Spokes%' or event_name like '%General Assembly%') order by event_start_date, event_start_time, GroupName";
 
 $ganumresults2=mysql_query($gaquery2);
 $ganumrows2=mysql_num_rows($ganumresults2);

 
  $garesult2 = mysql_query($gaquery2) or die("cccCouldn't execute query");



// now you can display the results returned
  while ($row= mysql_fetch_array($garesult2)) {
  $eventname = $row["event_name"];
  $startime = $row["StartTime"];
  $endtime = $row["EndTime"];
  $startdate = $row["StartDate"];
  $locationname = $row["LocationName"];
  $locationaddress = $row["LocationAddress"];
  $groupname = $row["GroupName"];
  

  echo " . $startdate . $eventname . starts at  . Time.. $startime . to . $endtime . at $locationaddress . " ;

  }

}

$querygpname = "select id, name as displayname from wp_bp_groups where id = '{$str->$trimmed}' LIMIT 1";


 $gpnumresults=mysql_query($querygpname);
 $gpnumrows=mysql_num_rows($gpnumresults);

 
 $gpresult = mysql_query($querygpname) or die("Couldn't execute query");

 while ($rowgp= mysql_fetch_array($gpresult)) {
  
  $groupnm = $rowgp["displayname"];
  $output .= " $groupnm " ;
  
  }
  
  $badchars = array(">", "<", "&amp;", "/", "&", "\\","ó", "é", '"');
  $replacechars = array(" and ", " ", " and ", " and ", " and ", " and ", "o", "e", "");
  $output2 = str_replace($badchars, $replacechars, $output);
  $gpname = preg_replace('/[^(\x20-\x7F)]*/','', $output2);
 
echo "$gpname has ";

// Build SQL Query  
$query = "SELECT wp_em_events.event_name, DATE_FORMAT(wp_em_events.event_start_time,'%l:%i%p') as StartTime, DATE_FORMAT(wp_em_events.event_end_time,'%l:%i%p') as EndTime, DATE_FORMAT(wp_em_events.event_start_date,'%W %M %D') as StartDate, wp_em_events.location_id, wp_em_locations.location_name as LocationName, wp_em_locations.location_address as LocationAddress, wp_em_events.group_id, wp_bp_groups.name as GroupName, wp_em_categories.category_name as CategoryName " .
"FROM wp_em_events LEFT JOIN wp_em_locations ON wp_em_events.location_id = wp_em_locations.location_id " .
"LEFT JOIN wp_bp_groups ON wp_em_events.group_id = wp_bp_groups.id " .
"LEFT JOIN wp_em_categories ON wp_em_events.event_category_id = wp_em_categories.category_name " .
"WHERE group_id = {$str->$trimmed} and (event_start_date >= CURDATE() and event_start_date <= CURDATE( ) + 20 ) and event_end_time >= CURTIME( )  order by event_start_date, event_start_time, GroupName";

 $numresults=mysql_query($query);
 $numrows=mysql_num_rows($numresults);

// If we have no results, offer a google search as an alternative

echo " . $numrows . events scheduled. ";

// next determine if s has been passed to script, if not use 0
  if (empty($s)) {
  $s=0;
  }

// get results
 
  $result = mysql_query($query) or die("Couldn't execute query");

// display what the person searched for

// begin to show results set

$count = 1 + $s ;

// now you can display the results returned
  while ($row= mysql_fetch_array($result)) {
  $eventname = $row["event_name"];
  $startime = $row["StartTime"];
  $endtime = $row["EndTime"];
  $startdate = $row["StartDate"];
  $locationname = $row["LocationName"];
  $locationaddress = $row["LocationAddress"];
  $groupname = $row["GroupName"];
  

  $aoutput .= "$count.  $eventname.   $startdate . Time.. $startime . to . $endtime .  Location. $locationname . at $locationaddress.    " ;
  $count++ ;
  
  }
  
  $badchars = array(">", "<", "&amp;", "/", "&", "\\","ó", "é", '"');
  $replacechars = array(" and ", " ", " and ", " and ", " and ", " and ", "o", "e", "");
  $output2 = str_replace($badchars, $replacechars, $aoutput);
  $output3 = preg_replace('/[^(\x20-\x7F)]*/','', $output2);
  $cleantext = substr($output3,0,4700);
  
  
  echo $cleantext;
  
  echo "These are events scheduled for the next 30 days.  You can say . date . to search for events on a specific date or say . group . to search for a different group";

  
?>