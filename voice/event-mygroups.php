<?php

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

  $var = @$_GET['userid'] ;
  $trimmed = trim($var); //trim whitespace from the stored variable

// rows to return
$limit=10; 


//connect to your database ** EDIT REQUIRED HERE **
mysql_connect(constant("DB_HOST"),constant("DB_USER"),constant("DB_PASSWORD")); //(host, username, password)

//specify database ** EDIT REQUIRED HERE **
mysql_select_db(constant("DB_NAME")) or die("Unable to select database"); //select which database we're using
//mysql_query("set time_zone = '-5:00'");
  
// Build SQL Query  
$query = "select group_id, user_id from wp_bp_groups_members where user_id = {$str->$trimmed}";

 $numresults=mysql_query($query);
 $numrows=mysql_num_rows($numresults);

 echo "Your a member of . $numrows . groups. .";

// If we have no results, offer a google search as an alternative


// next determine if s has been passed to script, if not use 0
  if (empty($s)) {
  $s=0;
  }

// get results
  $query .= " limit $s,$limit";
  $result = mysql_query($query) or die("Couldn't execute query");
  $timenow = date("h:i A");
// display what the person searched for
//echo "It's currently $timenow.  There are $numrows more scheduled events today.  To hear them all say Todays Events or you can search by saying Group Name, Location name, or Date.";

// begin to show results set

$count = 1 + $s ;

// now you can display the results returned
  while ($row= mysql_fetch_array($result)) {
  $groupid = $row["group_id"];
//sanitize input string from get
  $groupid0 = $str->$groupid;


  
  $idlist .= " or group_id = $groupid0 ";
  
  
  }
   
  
// Build SQL Query  
$query = "SELECT wp_em_events.event_name, DATE_FORMAT(wp_em_events.event_start_time,'%l:%i%p') as StartTime, DATE_FORMAT(wp_em_events.event_end_time,'%l:%i%p') as EndTime, DATE_FORMAT(wp_em_events.event_start_date,'%W %M %D') as StartDate, wp_em_events.location_id, wp_em_locations.location_name as LocationName, wp_em_locations.location_address as LocationAddress, wp_em_events.group_id, wp_bp_groups.name as GroupName, wp_em_categories.category_name as CategoryName " .
"FROM wp_em_events LEFT JOIN wp_em_locations ON wp_em_events.location_id = wp_em_locations.location_id " .
"LEFT JOIN wp_bp_groups ON wp_em_events.group_id = wp_bp_groups.id " .
"LEFT JOIN wp_em_categories ON wp_em_events.event_category_id = wp_em_categories.category_name " .
"WHERE (group_id = 9999 $idlist) and event_start_date = DATE( NOW( ) ) and event_end_time >= CURTIME( ) order by event_start_time, GroupName";


 $numresults=mysql_query($query);
 $numrows=mysql_num_rows($numresults);
  
// get results
  $query .= " limit $s,$limit";
  $result = mysql_query($query) or die("Couldn't execute query");
  $timenow = date("h:i A");
// display what the person searched for
//echo "It's currently $timenow.  There are $numrows more scheduled events today.  To hear them all say Todays Events or you can search by saying Group Name, Location name, or Date.";

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

  

  $output .= " . $count.  $groupname, $eventname.  Time. $startime . to . $endtime  .  Location. $locationname . at . $locationaddress.    " ;
  $count++ ;
  
  
  
  }
  echo "and have $numrows more events today. . ";
  
  $badchars = array(">", "<", "&amp;", "/", "&", "\\","ó", "é", '"');
  $replacechars = array(" and ", " ", " and ", " and ", " and ", " and ", "o", "e", "");
  $output2 = str_replace($badchars, $replacechars, $output);
  $cleantext = preg_replace('/[^(\x20-\x7F)]*/','', $output2);
  echo $cleantext;
  



  
  
  if ($numrows <= 5) {

  
  //tomorrows events
 
  $aquery = "SELECT wp_em_events.event_name, DATE_FORMAT(wp_em_events.event_start_time,'%l:%i%p') as StartTime, DATE_FORMAT(wp_em_events.event_end_time,'%l:%i%p') as EndTime, DATE_FORMAT(wp_em_events.event_start_date,'%W %M %D') as StartDate, wp_em_events.location_id, wp_em_locations.location_name as LocationName, wp_em_locations.location_address as LocationAddress, wp_em_events.group_id, wp_bp_groups.name as GroupName, wp_em_categories.category_name as CategoryName " .
"FROM wp_em_events LEFT JOIN wp_em_locations ON wp_em_events.location_id = wp_em_locations.location_id " .
"LEFT JOIN wp_bp_groups ON wp_em_events.group_id = wp_bp_groups.id " .
"LEFT JOIN wp_em_categories ON wp_em_events.event_category_id = wp_em_categories.category_name " .
"WHERE (group_id = 9999 $idlist) and event_start_date = CURDATE() + 1 order by event_start_time, GroupName";


 $anumresults=mysql_query($aquery);
 $anumrows=mysql_num_rows($anumresults);
  
// get results
//  $aquery .= " limit $s,$limit";
  $aresult = mysql_query($aquery) or die("Couldn't execute query");
//  $atimenow = date("h:i A");
// display what the person searched for
//echo "It's currently $timenow.  There are $numrows more scheduled events today.  To hear them all say Todays Events or you can search by saying Group Name, Location name, or Date.";

// begin to show results set

$acount = 1;

// now you can display the results returned
  while ($arow= mysql_fetch_array($aresult)) {
  $aeventname = $arow["event_name"];
  $astartime = $arow["StartTime"];
  $aendtime = $arow["EndTime"];
  $astartdate = $arow["StartDate"];
  $alocationname = $arow["LocationName"];
  $alocationaddress = $arow["LocationAddress"];
  $agroupname = $arow["GroupName"];
  

  $aoutput .= "$acount.  $agroupname, $aeventname.  Time. $astartime . to . $aendtime  .  Location. $alocationname . at . $alocationaddress.    " ;
  $acount++ ;
 
 }

if($anumrows >= 1) {
  echo "and $anumrows tomorrow. . Here are your events for tomorrow . ";
  
  $abadchars = array(">", "<", "&amp;", "/", "&", "\\","ó", "é", '"');
  $areplacechars = array(" and ", " ", " and ", " and ", " and ", " and ", "o", "e", "");
  $aoutput2 = str_replace($abadchars, $areplacechars, $aoutput);
  $acleantext = preg_replace('/[^(\x20-\x7F)]*/','', $aoutput2);
  echo $acleantext;
  }

  
  // end tomorrows events
  
  }
  
  
  
$currPage = (($s/$limit) + 1);


// calculate number of pages needing links
  $pages=intval($numrows/$limit);

// $pages now contains int of pages needed unless there is a remainder from division

  if ($numrows%$limit) {
  // has remainder so add one page
  $pages++;
  }


$a = $s + ($limit) ;
  if ($a > $numrows) { $a = $numrows ; }
  $b = $s + 1 ;
 
  
?>