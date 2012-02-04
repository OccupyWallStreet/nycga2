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

  $var = @$_GET['phonenumber'] ;
  $trimmed = trim($var); //trim whitespace from the stored variable
  $trimmedS = escapeshellcmd($trimmed);
  $returnpage = @$_GET['returnpage'] ;
  $returnpagetrim = trim($returnpage);
  
print '<ANGELXML>';
print '<MESSAGE>';
print '<PLAY>';
print '<PROMPT type="text">';
print '</PROMPT>';
print '</PLAY>';
print '<GOTO destination="/'.$returnpagetrim.'" />';
print '</MESSAGE>';

  
// rows to return
$limit=1; 


//connect to your database ** EDIT REQUIRED HERE **
mysql_connect(constant("DB_HOST"),constant("DB_USER"),constant("DB_PASSWORD")); //(host, username, password)

//specify database ** EDIT REQUIRED HERE **
mysql_select_db(constant("DB_NAME")) or die("Unable to select database"); //select which database we're using
//mysql_query("set time_zone = '-5:00'");
  
// Build SQL Query  
//$query = "select wp_bp_xprofile_data.field_id, wp_bp_xprofile_data.value, wp_bp_xprofile_data.user_id, wp_users.display_name from wp_bp_xprofile_data LEFT JOIN wp_users ON wp_bp_xprofile_data.user_id = wp_users.id where field_id = 5 and value = $phonenumber";
//$query = "select wp_bp_xprofile_data.field_id, wp_bp_xprofile_data.value, wp_bp_xprofile_data.user_id, wp_users.display_name from wp_bp_xprofile_data LEFT JOIN wp_users ON wp_bp_xprofile_data.user_id = wp_users.id where field_id = 5 and value like '$trimmed'";
//$query = "select wp_bp_xprofile_data.field_id, wp_bp_xprofile_data.value, wp_bp_xprofile_data.user_id, wp_users.display_name, \n"
//$query .="replace(replace(replace(replace(replace(replace(replace(wp_bp_xprofile_data.value,\'-\',\'\'),\' \',\'\'),\'.\',\'\'),\')\',\'\'),\'(\',\'\'),\'c\',\'\'),\'h\',\'\') as newphone from wp_bp_xprofile_data LEFT JOIN wp_users ON wp_bp_xprofile_data.user_id = wp_users.id where field_id = 5 and replace(replace(replace(replace(replace(replace(replace(wp_bp_xprofile_data.value,\'-\',\'\'),\' \',\'\'),\'.\',\'\'),\')\',\'\'),\'(\',\'\'),\'c\',\'\'),\'h\',\'\') like \'%3476847285%\'"
$query = "select wp_bp_xprofile_data.field_id, wp_bp_xprofile_data.value, wp_bp_xprofile_data.user_id, wp_users.display_name, " .
"replace(replace(replace(replace(replace(replace(replace(wp_bp_xprofile_data.value,'-',''),' ',''),'.',''),')',''),'(',''),'c',''),'h','') as newphone from wp_bp_xprofile_data LEFT JOIN wp_users ON wp_bp_xprofile_data.user_id = wp_users.id where field_id = 5 and replace(replace(replace(replace(replace(replace(replace(wp_bp_xprofile_data.value,'-',''),' ',''),'.',''),')',''),'(',''),'c',''),'h','') like '$trimmedS%'";




 $numresults=mysql_query($query);
 $numrows=mysql_num_rows($numresults);

// If we have no results, offer a google search as an alternative


// next determine if s has been passed to script, if not use 0
  if (empty($s)) {
  $s=0;
  }

// get results
  $query .= " limit $s,$limit";
  $result = mysql_query($query) or die("Couldn't execute query");
  //$timenow = date("h:i A");
// display what the person searched for
//echo "It's currently $timenow.  There are $numrows more scheduled events today.  To hear them all say Todays Events or you can search by saying Group Name, Location name, or Date.";

// begin to show results set

$count = 1 + $s ;

// now you can display the results returned
  while ($row= mysql_fetch_array($result)) {
  $displayname = $row["display_name"];
  $userid = $row["user_id"];
  //$endtime = $row["EndTime"];
  //$startdate = $row["StartDate"];
  //$locationname = $row["LocationName"];
  //$locationaddress = $row["LocationAddress"];
  //$groupname = $row["GroupName"];
  

//  $output .= "$count.  $eventname.   $startdate . Time.. $startime . to . $endtime .  Location. $locationname . at $locationaddress.    " ;
  $count++ ;

  
print '<VARIABLES>';
print '<VAR name="CallerName" value="'.$displayname.'"/>';
print '<VAR name="UserID" value="'.$userid.'"/>';
print '</VARIABLES>';
  
  }
  
  //$badchars = array(">", "<", "&amp;", "/", "&", "\\","ó", "é", '"');
  //$replacechars = array(" and ", " ", " and ", " and ", " and ", " and ", "o", "e", "");
  //$output2 = str_replace($badchars, $replacechars, $output);
  //$cleantext = preg_replace('/[^(\x20-\x7F)]*/','', $output2);
  
  
  //echo $cleantext;
  

print '</ANGELXML>';
  
  
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