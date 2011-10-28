<?php
/*
Simple:Press
xhtml to bbCode parser
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

function sf_Html2BBCode($text)
{
	$text = trim($text);

	$text = str_replace("\n\n", "\n", $text);

	$text = str_replace ('<div class="sfcode">', "<code>", $text);
	$text = str_replace ('</div>', "</code>", $text);

	# BBCode [code]
	if (!function_exists('htmltobb_escape')) {
		function htmltobb_escape($s) {
			global $text;
			return '[code]'.htmlspecialchars_decode($s[1]).'[/code]';
		}
	}
	$text = preg_replace_callback('/\<code\>(.*?)\<\/code\>/ms', "htmltobb_escape", $text);

	# Tags to Find
	$htmltags = array(
		'/\<b\>(.*?)\<\/b\>/is',
		'/\<em\>(.*?)\<\/em\>/is',
		'/\<u\>(.*?)\<\/u\>/is',
		'/\<ul\>(.*?)\<\/ul\>/is',
		'/\<li\>(.*?)\<\/li\>/is',
		'/\<img(.*?) src=\"(.*?)\" (.*?)\>/is',
		'/\<blockquote\>(.*?)\<\/blockquote\>/is',
		'/\<strong\>(.*?)\<\/strong\>/is',
		'/\<a href=\"(.*?)\"(.*?)\>(.*?)\<\/a\>/is',
	);

	# Replace with
	$bbtags = array(
		'[b]$1[/b]',
		'[i]$1[/i]',
		'[u]$1[/u]',
		'[list]$1[/list]',
		'[*]$1',
		'[img]$2[/img]',
		'[quote]$1[/quote]',
		'[b]$1[/b]',
		'[url=$1]$3[/url]',
	);

	# Replace $htmltags in $text with $bbtags
	$text = preg_replace ($htmltags, $bbtags, $text);

	return $text;
}

?>