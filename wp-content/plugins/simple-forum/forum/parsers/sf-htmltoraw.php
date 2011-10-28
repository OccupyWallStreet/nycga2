<?php
/*
Simple:Press
html to raw parser
$LastChangedDate: 2010-04-18 09:22:20 -0700 (Sun, 18 Apr 2010) $
$Rev: 3920 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

function sf_Html2Raw($text)
{
	$text = trim($text);

	$text = str_replace ("\n\n", "\n", $text);

	$text = str_replace ('<div class="sfcode">', "<code>", $text);
	$text = str_replace ('</div>', "</code>", $text);

	# BBCode [code]
	if (!function_exists('rawescape')) {
		function rawescape($s) {
			global $text;
			return '<code>'.htmlspecialchars_decode($s[1]).'</code>';
		}
	}
	$text = preg_replace_callback('/\<code\>(.*?)\<\/code\>/ms', "rawescape", $text);

	return $text;
}

?>