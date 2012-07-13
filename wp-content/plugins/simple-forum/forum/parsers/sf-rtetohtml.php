<?php
/*
Simple:Press
rte(tm) to xhtml parser
$LastChangedDate: 2010-04-18 09:22:20 -0700 (Sun, 18 Apr 2010) $
$Rev: 3920 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

function sf_RTE2Html($text)
{
	$text = trim($text);

	# RTE-TM-Code
	if (!function_exists('rtetohtml_escape')) {
		function rtetohtml_escape($s) {
			global $text;
			return '<div class="sfcode">'.str_replace('"', '&quot;', $s[1]).'</div>';
		}
	}
	$text = preg_replace_callback('/\<div class=\"sfcode\"\>(.*?)\<\/div\>/ms', "rtetohtml_escape", $text);

	return $text;
}

?>