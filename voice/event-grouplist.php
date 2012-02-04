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

  $var = @$_GET['char'] ;
  $trimmed = trim($var); //trim whitespace from the stored variable
  $sanitize = escapeshellcmd($trimmed);

  $bychar = "WHERE name LIKE '$sanitize%'";

//connect to your database ** EDIT REQUIRED HERE **
mysql_connect(constant("DB_HOST"),constant("DB_USER"),constant("DB_PASSWORD")); //(host, username, password)

//specify database ** EDIT REQUIRED HERE **
mysql_select_db(constant("DB_NAME")) or die("Unable to select database"); //select which database we're using

//mysql_query("set time_zone = '-5:00'");

if ($trimmed == "9999") {
$querygpname = "select id, name as displayname from wp_bp_groups order by name";
$output .= "General Assembly . . Spokes Council . . "; 
}

if ($trimmed <> "9999") {
$querygpname = "select id, name as displayname from wp_bp_groups $bychar order by name";
$thatbegin = "that begin with the letter $trimmed";
}

if ($trimmed == "s") {
$output .= " . Spokes Council . ";
}

if ($trimmed == "g") {
$output .= " . General Assembly . ";
}  

 $gpnumresults=mysql_query($querygpname);
 $gpnumrows=mysql_num_rows($gpnumresults);
 
echo "Listing $gpnumrows groups $thatbegin in alphabetical order. . ";

 
 $gpresult = mysql_query($querygpname) or die("Couldn't execute query");

 $gacount2 = 1 + $s ;
 
 while ($rowgp= mysql_fetch_array($gpresult)) {
  
  $groupnm = $rowgp["displayname"];
  $groupid = $rowgp["id"];
  $output .= " $gacount2 . . $groupnm . . " ;
  $gacount2++;
  }
  
  $badchars = array(">", "<", "&amp;", "/", "&", "\\","ó", "é", '"', "OWS", "Working","Group", "#");
  $replacechars = array(" and ", " ", " and ", " and ", " and ", " and ", "o", "e", "", "", "", "", "");
  $output2 = str_replace($badchars, $replacechars, $output);
  $gpname = preg_replace('/[^(\x20-\x7F)]*/','', $output2);
 
echo "$gpname ";

?>