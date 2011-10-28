<?php
/*
Simple:Press
bbCode to xhtml parser
$LastChangedDate: 2011-01-23 10:56:55 -0700 (Sun, 23 Jan 2011) $
$Rev: 5346 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

function sf_BBCode2Html($text, $dobr=true)
{

	$text = trim($text);

	# BBCode [code]
	if (!function_exists('bbtohtml_escape')) {
		function bbtohtml_escape($s) {
			global $text;
			$text = strip_tags($text);
			return '<code>'.htmlspecialchars($s[1]).'</code>';
		}
	}
	$text = preg_replace_callback('/\[code\](.*?)\[\/code\]/ms', "bbtohtml_escape", $text);

	# BBCode to find...
	$in = array( 	 '/\[b\](.*?)\[\/b\]/ms',
					 '/\[i\](.*?)\[\/i\]/ms',
					 '/\[u\](.*?)\[\/u\]/ms',
					 '/\[left\](.*?)\[\/left\]/ms',
					 '/\[right\](.*?)\[\/right\]/ms',
					 '/\[center\](.*?)\[\/center\]/ms',
					 '/\[img\](.*?)\[\/img\]/ms',
					 '/\[url\="?(.*?)"?\](.*?)\[\/url\]/is',
					 '/\[url\](.*?)\[\/url\]/is',
					 '/\[quote\](.*?)\[\/quote\]/ms',
					 '/\[list\=(.*?)\](.*?)\[\/list\]/ms',
					 '/\[list\](.*?)\[\/list\]/ms',
					 '/\[B\](.*?)\[\/B\]/ms',
					 '/\[I\](.*?)\[\/I\]/ms',
					 '/\[U\](.*?)\[\/U\]/ms',
					 '/\[LEFT\](.*?)\[\/LEFT\]/ms',
					 '/\[RIGHT\](.*?)\[\/RIGHT\]/ms',
					 '/\[CENTER\](.*?)\[\/CENTER\]/ms',
					 '/\[IMG\](.*?)\[\/IMG\]/ms',
					 '/\[URL\="?(.*?)"?\](.*?)\[\/URL\]/is',
					 '/\[QUOTE\](.*?)\[\/QUOTE\]/ms',
					 '/\[LIST\=(.*?)\](.*?)\[\/LIST\]/ms',
					 '/\[LIST\](.*?)\[\/LIST\]/ms',
					 '/\[\*\]\s?(.*?)\n/ms'
	);
	# And replace them by...
	$out = array(	 '<strong>\1</strong>',
					 '<em>\1</em>',
					 '<u>\1</u>',
					 '<div style="text-align:left">\1</div>',
					 '<div style="text-align:right">\1</div>',
					 '<div style="text-align:center">\1</div>',
					 '<img src="\1" alt="\1" />',
					 '<a href="\1">\2</a>',
					 '\1',
					 '<blockquote>\1</blockquote>',
					 '<ol start="\1">\2</ol>',
					 '<ul>\1</ul>',
					 '<strong>\1</strong>',
					 '<em>\1</em>',
					 '<u>\1</u>',
					 '<div style="text-align:left">\1</div>',
					 '<div style="text-align:right">\1</div>',
					 '<div style="text-align:center">\1</div>',
					 '<img src="\1" alt="\1" />',
					 '<a href="\1">\2</a>',
					 '<blockquote>\1</blockquote>',
					 '<ol start="\1">\2</ol>',
					 '<ul>\1</ul>',
					 '<li>\1</li>'
	);
	$text = preg_replace($in, $out, $text);

	# special case for nested quotes
	$text = str_replace('[quote]', '<blockquote>', $text);
	$text = str_replace('[/quote]', '</blockquote>', $text);

	# paragraphs
	if($dobr)
	{
		$text = str_replace("\r", "", $text);

		# clean some tags to remain strict
		if (!function_exists('bbtohtml_removeBr')) {
			function bbtohtml_removeBr($s) {
				return str_replace("<br />", "", $s[0]);
			}
		}

		$text = preg_replace_callback('/<pre>(.*?)<\/pre>/ms', "bbtohtml_removeBr", $text);
		$text = preg_replace('/<p><pre>(.*?)<\/pre><\/p>/ms', "<pre>\\1</pre>", $text);

		$text = preg_replace_callback('/<ul>(.*?)<\/ul>/ms', "bbtohtml_removeBr", $text);
		$text = preg_replace('/<p><ul>(.*?)<\/ul><\/p>/ms', "<ul>\\1</ul>", $text);
	}

	return $text;
}

?>