<?php
/*
Simple:Press
raw html to xhtml parser (just looking for 'code' blocks
$LastChangedDate: 2010-04-18 09:22:20 -0700 (Sun, 18 Apr 2010) $
$Rev: 3920 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

function sf_Raw2Html($text)
{
	$text = trim($text);

	if (!function_exists('rawtohtml_escape')) {
		function rawtohtml_escape($s) {
			global $text;
			return '<code>'.htmlspecialchars($s[1]).'</code>';
		}
	}
	$text = preg_replace_callback('/\<code\>(.*?)\<\/code\>/ms', "rawtohtml_escape", $text);

	return $text;
}

?>