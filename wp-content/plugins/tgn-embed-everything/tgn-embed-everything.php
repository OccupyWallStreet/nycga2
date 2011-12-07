<?php

/*
Plugin Name: TGN Embed everything
Plugin URI: http://www.thewpwiki.com/extensions/tgn-embed-everything
Description: Easily embed YouTube, PDF, Google docs, spreadsheets, PowerPoint, Word, VideoReadr, TIFF and more into WordPress 3! For example: [youtube X8gBdsBjpJw 604 364]

Author: George Vanous <george@thewpwiki.com>
Version: 1.0.31
Author URI: http://www.thewpwiki.com/
*/

/*
TODO
	Support Google Forms
	<iframe src="https://spreadsheets.google.com/a/thegamenet.com/embeddedform?formkey=dEFzd3Vwd1M1bXhzTUFzY3VIbURKTUE6MQ" width="760" height="1213" frameborder="0" marginheight="0" marginwidth="0">Loading...</iframe>

	Create a media browser and web toolbar like http://wordpress.org/extend/plugins/apture/

	Avoid linkifying [show http://tgnbooks.com/the-great-gatsby.pdf]
  Add [map (address)] eg [map 8930 watson court delta bc v4c8a1]
  Read directly into WordPress like http://www.blackspike.com/site/html/display-google-docs-spreadsheets-in-wordpress
  Howto edit a google spreadsheet with others in realtime using wave: http://www.google.com/support/forum/p/wave/thread?tid=6b6e582d4e66a39f&hl=en
  Support Google Forms like http://www3.formassembly.com/blog/using-google-spreadsheet-as-a-reporting-tool-for-formassembly/
  To share a spreadsheet, click Share > Publish as a web page > select the sheet to publish > click Start publishing > click on All cells field > insert the cells range, e.g. A2:A3 > click Republish now > copy the link > Close

Features
	removes &nbsp; from shortcodes (WordPress can add "&nbsp;" instead of a space)
  eg [form&nbsp;dE1pUWxwQUJXY21uZlUzSnpMYzhNZ0E6MQ] // in HTML view
*/

//define('tgn_ee_defaultWidth', '722');
//define('tgn_ee_defaultYouTubeObjectHeight', '436');

define('tgn_ee_defaultWidth', '604');
define('tgn_ee_defaultYouTubeObjectHeight', '370');
define('tgn_ee_defaultYouTubeIframeHeight', '370');

define('tgn_ee_shortHeight', '302');
define('tgn_ee_defaultHeight', '604');
define('tgn_ee_defaultVideoReadrHeight', '560');

define('tgn_ee_fullWidth', '100%');

/*
To allow IE to view PDFs

>>>>
This is a bug in Google Document Viewer. They should provide a compact privacy policy on Google Docs site.

Its a standard only enforced in IE 7 and 8 embebed objects but its not new. It is important for a provider of any cross-site embebed object to use it or  IE would reject all cookies from those sites.

If the Google Docs Viewer team would just add...

header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');

...(which is a Compact Privacy Policy) it would work with all MSIE versions.
<<<<

Source: http://www.google.com/support/forum/p/Google+Docs/thread?tid=22d92671afd5b9b7
*/
header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');

function tgn_ee_head()
{
	echo <<<EOS
<style type="text/css">
.videoreadr .ui-widget { font-size:90% !important }
/*.videoreadr .l_toolbar .logo_link img { width:90px !important } uncomment if the VideoReadr logo is too big */
</style>

<script src="http://ajax.googleapis.com/ajax/libs/swfobject/2.1/swfobject.js" type="text/javascript"></script>

<script type="text/javascript">
if (!('jQuery' in window))
{
	document.write('<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></scr' + 'ipt>');
}
</script>

EOS;
}

add_action('wp_head', 'tgn_ee_head');


function tgn_ee_fix_content($content)
{
	// commented: breaks WordPress [caption] feature
//	$content = tgn_ee_linkify_url_and_email_addresses($content);

	$content = tgn_ee_fix_shortcodes($content); // also fixes linkifying [show http://tgnbooks.com/the-great-gatsby.pdf]

	return $content;
}

add_filter('the_content', 'tgn_ee_fix_content');


// same as "function do_shortcode($content)" in /wp-includes/shortcodes.php
function tgn_ee_fix_shortcodes($content)
{
	global $shortcode_tags;

	if (empty($shortcode_tags) || !is_array($shortcode_tags))
		return $content;

	$pattern = get_shortcode_regex();
	return preg_replace_callback('/' . $pattern . '/s', 'tgn_ee_fix_shortcodes_tag', $content);
}

function tgn_ee_fix_shortcodes_tag( $m )
{
	$text = $m[0];

	// allow [[foo]] syntax for escaping a tag
	if ($m[1] == '[' && $m[6] == ']')
	{
		return $text;
	}

// faster to do it in one preg_replace()
//	$text = preg_replace('/&nbsp;/', ' ', $text);
//	$text = preg_replace('/<[^>]+>/', '', $text);

	$text =
		preg_replace
		(
			array
			(
				'/&nbsp;/',
//				'/<[^>]+>/', // breaks [caption width="90" caption="This is a caption"]<a href="image.jpg"><img src="image.jpg" alt="" width="90" height="90" /></a>[/caption]
			),

			array
			(
				' ',
//				'',
			),

			$text
		);

	return $text;
}


/*
function tgn_ee_linkify_url_and_email_addresses($text)
{
	$text = ' ' . $text;

	// in testing, using arrays here was found to be faster
	$text =
		preg_replace
		(
			array
			(
//				'#(\[.+?\])#s', // does not work: avoid linkifying [show http://tgnbooks.com/the-great-gatsby.pdf]

				'#([\s>])([\w]+?://[\w\#$%&~/.\-;:=,?@\[+]*)#s', // don't support ] in URLs to avoid converting [show http://tgnbooks.com/the-great-gatsby.pdf] to [show <a href="http://tgnbooks.com/the-great-gatsby.pdf]">...</a>
				'#([\s>])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[+]*)#is',
//				'#([\s>])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#s',
//				'#([\s>])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is',
				'#([\s>])([-\w.]+)@([-\w.]+\w)#',
//			'#([\s>])([a-z0-9\-_.]+)@([^,< \n\r]+)#i',
			),

			array
			(
//				'$1',

				'$1<a href="$2">$2</a>',
				'$1<a href="http://$2">$2</a>',
				'$1<a href="mailto:$2@$3">$2@$3</a>'
			),

			$text
		);

		// this one is not in an array because we need it to run last, for cleanup of accidental links within links
		$text = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $text);

		return trim($text);
}
*/


function tgn_ee_ckfinder_shortcode($values)
{
	extract(tgn_ee_parse_shortcode_height_first(0, array(
		'width' => tgn_ee_fullWidth,
		'height' => tgn_ee_defaultHeight,
	), $values));

	return tgn_ee_ckfinder_script($width, $height);
}

add_shortcode('ckfinder', 'tgn_ee_ckfinder_shortcode');


function tgn_ee_form_shortcode($values)
{
	extract(tgn_ee_parse_shortcode_height_first(1, array(
		'key' => '',
		'width' => tgn_ee_fullWidth,
		'height' => tgn_ee_defaultHeight,
	), $values));

	return tgn_ee_google_docs_form_iframe($key, $width, $height);
}

add_shortcode('form', 'tgn_ee_form_shortcode');


function tgn_ee_iframe_shortcode($values)
{
	extract(tgn_ee_parse_shortcode_height_first(1, array(
		'src' => 'http://www.thegamenet.com/',
		'width' => tgn_ee_defaultWidth,
		'height' => tgn_ee_defaultHeight,
	), $values));

	return tgn_ee_iframe($src, $width, $height);
}

add_shortcode('iframe', 'tgn_ee_iframe_shortcode');


function tgn_ee_machform_shortcode($values)
{
	extract(tgn_ee_parse_shortcode_height_first(1, array(
		'key' => '',
		'width' => tgn_ee_fullWidth,
		'height' => tgn_ee_defaultHeight,
	), $values));

	return tgn_ee_machform_iframe($key, $width, $height);
}

add_shortcode('machform', 'tgn_ee_machform_shortcode');


function tgn_ee_pdf_shortcode($values)
{
	extract(tgn_ee_parse_shortcode_height_first(1, array(
		'file' => 'tgnbooks.com/the-great-gatsby.pdf',
		'width' => tgn_ee_fullWidth,
		'height' => tgn_ee_defaultHeight,
	), $values));

	return tgn_ee_google_docs_viewer_iframe($file, $width, $height);
}

add_shortcode('pdf', 'tgn_ee_pdf_shortcode');


function tgn_ee_powerpoint_shortcode($values)
{
	extract(tgn_ee_parse_shortcode_height_first(1, array(
		'file' => 'tgnbooks.com/best-caricatures.ppt',
		'width' => tgn_ee_fullWidth,
		'height' => tgn_ee_defaultHeight,
	), $values));

	return tgn_ee_google_docs_viewer_iframe($file, $width, $height);
}

add_shortcode('powerpoint', 'tgn_ee_powerpoint_shortcode');


function tgn_ee_show_shortcode($values)
{
	extract(tgn_ee_parse_shortcode_height_first(1, array(
		'file' => 'tgnbooks.com/the-great-gatsby.pdf',
		'width' => tgn_ee_fullWidth,
		'height' => tgn_ee_defaultHeight,
	), $values));

	return tgn_ee_google_docs_viewer_iframe($file, $width, $height);
}

add_shortcode('show', 'tgn_ee_show_shortcode');


function tgn_ee_stock($values)
{
	extract(tgn_ee_parse_shortcode_height_first(1, array(
		'symbol' => '3GR1.F', // http://finance.yahoo.com/q?s=3GR1.F
	), $values));

  $quote = quotefr($symbol);

  $lastPrice = $quote[0][2];
  $gain = $quote[0][3];
  $change = $quote[0][4];

  return <<<EOS
<span class="last-price">$lastPrice</span><span class="price-gain-or-loss">$gain</span><span class="price-change">$change</span>
EOS;
}

add_shortcode('stockquote', 'tgn_ee_stock');

function quotefr($symbol)
{
	/*
	Returns 2D an array:
		X-0 symbol
		X-1 exchange
		X-2 last	 : last price
		X-3 gain 	 : + or -
		X-4 change : change value
		X-5 high 	 : day's high
	*/

	$quote_csv = "http://finance.yahoo.com/d/quotes.csv?s=" . $symbol . "&f=l1c1hs&o=t";
	$handle = fopen($quote_csv,"r");
	$rows	= explode("\n",fread($handle, 10000));
	fclose($handle);
	$toreturn	= array();
	for ($y=0;$y<count($rows);$y++) {
		if (strlen(trim($rows[$y])) > 0) {
			$data	= explode(",",$rows[$y]);
			$last		= number_format(trim(str_replace("N/A","0",$data[0])),2,".",",");
			$change		= number_format(trim(str_replace("N/A","0",$data[1])),2,".",",");
			$dayh		= number_format(trim(str_replace("N/A","0",$data[2])),2,".",",");
			$temp		= explode(".",trim(str_replace("\"","",$data[3])));
			$symbol		= $temp[0];
			$exchange	= $temp[1];
			if ($exchange == "") {
				$exchange	= "";
			} else {
				$exchange	= ".$exchange";
			}
			if ($change < 0) {
				$gain	= "";
			} else {
				$gain	= "+";
			}
			array_push($toreturn,array($symbol,$exchange,$last,$gain,$change,$dayh));
		}
	}
	return $toreturn;
}

function tgn_ee_document_shortcode($values)
{
	extract(tgn_ee_parse_shortcode_height_first(1, array(
		'key' => '1t0C9HghMO4ttIKbUYxVC2sn5nXi9hXldVS1okjWzTP4',
		'width' => tgn_ee_fullWidth,
		'height' => tgn_ee_defaultHeight,
		'authkey' => '',
	), $values));

	return tgn_ee_google_document_viewer_iframe($key, $width, $height, $authkey);
}

add_shortcode('document', 'tgn_ee_document_shortcode');


function tgn_ee_spreadsheet_shortcode($values)
{
	extract(tgn_ee_parse_shortcode_height_first(1, array(
		'key' => '0AtFXq8XyejTEdGNEZm1NZUJ3V3QwXzIwZnQ1ckh0MlE',
		'width' => tgn_ee_fullWidth,
		'height' => tgn_ee_defaultHeight,
		'view' => 'minimum',
		'authkey' => '',
		'sheet' => '',
	), $values));

	return tgn_ee_google_spreadsheet_viewer_iframe($key, $width, $height, $view, $authkey, $sheet);
}

add_shortcode('spreadsheet', 'tgn_ee_spreadsheet_shortcode');


function tgn_ee_tiff_shortcode($values)
{
	extract(tgn_ee_parse_shortcode_height_first(1, array(
		'file' => '',
		'width' => tgn_ee_fullWidth,
		'height' => tgn_ee_defaultHeight,
	), $values));

	return tgn_ee_google_docs_viewer_iframe($file, $width, $height);
}

add_shortcode('tiff', 'tgn_ee_tiff_shortcode');

function tgn_ee_videoreadr_shortcode($values)
{
	$values = tgn_ee_parse_shortcode(array(
		'youtubeId' => 'Uz7fOLDr2JM',
		'videoreadrId' => 'nmrdsxtb',
		'width' => tgn_ee_defaultWidth,
		'height' => tgn_ee_defaultYouTubeObjectHeight,

		// all public YouTube API parameters http://code.google.com/apis/youtube/player_parameters.html

		'version' => '3', // undocumented, but shows the latest player chrome with seek above bottom bar
		'rel' => '0',
		'autoplay' => '',
		'loop' => '',
		'enablejsapi' => '1',
		'playerapiid' => '',
		'disablekb' => '',
		'egm' => '',
		'border' => '',
		'color1' => '',
		'color2' => '',
		'start' => '',
		'fs' => '1',
		'hd' => '',
		'showsearch' => '',
		'showinfo' => '0', // hide title (we have <h2> above video showing title, in-video is redundant)
		'iv_load_policy' => '',
		'cc_load_policy' => '',
	), $values);

	return tgn_ee_videoreadr_script($values);
}

add_shortcode('videoreadr', 'tgn_ee_videoreadr_shortcode');


function tgn_ee_word_shortcode($values)
{
	extract(tgn_ee_parse_shortcode_height_first(1, array(
		'file' => '',
		'width' => tgn_ee_fullWidth,
		'height' => tgn_ee_defaultHeight,
	), $values));

	return tgn_ee_google_docs_viewer_iframe($file, $width, $height);
}

add_shortcode('word', 'tgn_ee_word_shortcode');


// Paste into your browser
// http://www.youtube.com/v/-PADF6XD7pw?showinfo=0&autoplay=1
// http://www.youtube.com/embed/-PADF6XD7pw?autoplay=1
function tgn_ee_youtube_shortcode($values)
{
//return('<pre>' . print_r($values, true) . ' - ' . print_r(tgn_ee_parse_shortcode(array('key' => 'X8gBdsBjpJw', 'width' => '604', 'height' => '364'), $values), true) . '</pre>');

	$values = tgn_ee_parse_shortcode(array(
		'key' => 'WDLgEyJ3SlE', // without commentary: '-PADF6XD7pw',
		'width' => tgn_ee_defaultWidth,
		'height' => tgn_ee_defaultYouTubeObjectHeight,

		// all public YouTube API parameters http://code.google.com/apis/youtube/player_parameters.html

		'version' => '3', // undocumented, but shows the latest player chrome with seek above bottom bar
		'rel' => '0',
		'autoplay' => '',
		'loop' => '',
		'enablejsapi' => '',
		'playerapiid' => '',
		'disablekb' => '',
		'egm' => '',
		'border' => '',
		'color1' => '',
		'color2' => '',
		'start' => '',
		'fs' => '1',
		'hd' => '',
		'showsearch' => '',
		'showinfo' => '0', // hide title (we have <h2> above video showing title, in-video is redundant)
		'iv_load_policy' => '',
		'cc_load_policy' => '',
	), $values);

	return tgn_ee_youtube_object($values);
}

add_shortcode('youtube', 'tgn_ee_youtube_shortcode');


/*
Replace plain YouTube URLs with the [youtube ...] shortcode

Plain text gets auto-replaced (don't know how!)

http://www.youtube.com/watch?v=mQ-CQVlb8LU
http://www.youtube.com/watch?v=mQ-CQVlb8LU&feature=player_embedded
http://www.youtube.com/watch?v=mQ-CQVlb8LU&amp;feature=player_embedded

Links work

<a href="http://www.youtube.com/watch?v=mQ-CQVlb8LU">http://www.youtube.com/watch?v=mQ-CQVlb8LU</a>
<a href="http://www.youtube.com/watch?feature=player_embedded&amp;v=mQ-CQVlb8LU">http://www.youtube.com/watch?feature=player_embedded&amp;v=mQ-CQVlb8LU</a>
*/
function tgn_ee_youtube_url($content)
{
	$youtube_regexp = 'http://(?:www\.)?youtube\.com/watch\?(?:[-\w=]+&(?:amp;)?)*?v=([-\w]+)';

//	return preg_replace_callback('@\[[^\]]+\]|<a href="(' . $youtube_regexp . '[^"]*)">\1</a>|' . $youtube_regexp . '(?:&(?:amp;)?[-\w=]*)*@', 'tgn_ee_youtube_url_callback', $content);

	// Must be on its own line
//flog('==');
//flog($content);
//flog('--');
	return preg_replace_callback('@\s*(?:<(?:p|div|br\s*/?)[^>]*>\s*)(?:<a [^>]*?href="(' . $youtube_regexp . '[^"]*)"[^>]*>\1</a>|' . $youtube_regexp . '(?:&(?:amp;)?[-\w=]*)*)(?:\s*<(?:/p|/div|br\s*/?)>)@i', 'tgn_ee_youtube_url_callback', $content);

	// Does not work: still has <p> tags! How does autoembed() in wp-includes/media.php *not* have <p> tags?
//	return preg_replace_callback('@^\s*(?:<a href="(' . $youtube_regexp . '[^"]*)">\1</a>|' . $youtube_regexp . '(?:&(?:amp;)?[-\w=]*)*)\s*$@im', 'tgn_ee_youtube_url_callback', $content);
}

function tgn_ee_youtube_url_callback($matches)
{
	$youtube_id = false;

	$count = count($matches);
//flogr($matches);
	if (4 == $count) $youtube_id = $matches[3];
	else if (3 == $count) $youtube_id = $matches[2];

	return
		$youtube_id
		 ? '[youtube ' . $youtube_id . ']'
		 : $matches[0];
}

add_filter('the_content', 'tgn_ee_youtube_url');
add_filter('the_excerpt', 'tgn_ee_youtube_url');

// prevent oEmbed from converting a plain YouTube URL
tgn_ee_oembed_remove_provider('#http://(www\.)?youtube.com/watch.*#i');

function tgn_ee_oembed_remove_provider($format)
{
	require_once(ABSPATH . WPINC . '/class-oembed.php');
	$oembed = _wp_oembed_get_object();

	if (array_key_exists($format, $oembed->providers))
	{
		unset($oembed->providers[$format]);
		$oembed->providers = array();
	}
}

// Helpful debug code
function flogr($vValue) { flog(print_r($vValue, true)); return $vValue; }

function flog($sValue) // `log` is the natural logarithm
{
	$oFileId = fopen ('c:/temp/php.log', 'ab');
	           fwrite($oFileId, $sValue . "\n");
	           fclose($oFileId);

	return $sValue;
}


//==============================================================================
// CKFinder
// ckfinder.com
// File manager, functional free demo (demo notice, cannot drag-and-drop files), $59 per-domain license, $590 per-company license
//

function tgn_ee_ckfinder_script($width, $height)
{
	$siteUrl = get_option('siteurl');

  return <<<EOS
<div id="ckfinder"></div>

<script type="text/javascript" src="$siteUrl/wp-content/plugins/ckeditor-for-wordpress/ckfinder/ckfinder.js"></script>
<script type="text/javascript">

(function()
{
	var config = {};

	// Always use 100% width and height when nested using this middle page.
	config.width = '$width';
	config.height = '$height';

	var ckfinder = new CKFinder(config);
	ckfinder.replace('ckfinder', config);
})();

</script>
EOS;
}

function tgn_ee_ckfinder_iframe($width, $height)
{
	$siteUrl = get_option('siteurl');

	return <<<EOS
<iframe src="$siteUrl/wp-content/plugins/ckeditor-for-wordpress/ckfinder/ckfinder.html" width="$width" height="$height" marginheight="0" marginwidth="0" frameborder="0"></iframe>
EOS;
}


//==============================================================================
// Google docs viewer
// http://docs.google.com/viewer
// Supports doc, pdf, ppt, tiff (no Flash or PDF browser plugins required)
// Also supports some docx, pptx (only way to know is to test yours and see if it displays)

function tgn_ee_google_docs_viewer_iframe($url, $width, $height)
{
	$url = urlEncode($url);

	return <<<EOS
<iframe src="http://docs.google.com/viewer?url=$url&embedded=true" width="$width" height="$height" marginwidth="0" marginheight="0" frameborder="0"></iframe>
EOS;
}


//==============================================================================
// Google docs form
// http://docs.google.com/
// Create > Form

function tgn_ee_google_docs_form_iframe($key, $width, $height)
{
	return <<<EOS
<iframe src="http://spreadsheets.google.com/embeddedform?formkey=$key" width="$width" height="$height" frameborder="0" marginheight="0" marginwidth="0">Loading...</iframe>
EOS;
}


//==============================================================================
// Google document viewer
// http://docs.google.com/
// Supports doc, pdf, ppt, tiff (no Flash or PDF browser plugins required)
// Also supports some docx, pptx (only way to know is to test yours and see if it displays)
//
function tgn_ee_google_document_viewer_iframe($keyOrUrl, $width, $height, $authkey)
{
	tgn_ee_extractKey_google_spreadsheet($keyOrUrl, $key, $sheetId);

	return <<<EOS
<iframe src="https://docs.google.com/document/pub?id=$key&embedded=true" width="$width" height="$height" marginwidth="0" marginheight="0" frameborder="0"></iframe>
EOS;
}


//==============================================================================
// Google spreadsheet viewer
// http://spreadsheets.google.com/
// Supports doc, pdf, ppt, tiff (no Flash or PDF browser plugins required)
// Also supports some docx, pptx (only way to know is to test yours and see if it displays)
//
function tgn_ee_google_spreadsheet_viewer_iframe($keyOrUrl, $width, $height, $view, $authkey, $sheet)
{
	tgn_ee_extractKey_google_spreadsheet($keyOrUrl, $key, $sheetId);

	if ('' != $sheet)
	{
		$sheetId = (int)$sheet - 1;
	}

	if ('normal' == $view)
	{
		$range = '';
	}
	else if ('edit' == $view)
	{
		return <<<EOS
<iframe src="http://spreadsheets.google.com/ccc?key=$key&authkey=$authkey&gid=$sheetId" width="$width" height="$height" marginwidth="0" marginheight="0" frameborder="0"></iframe>
EOS;
	}
	else
	{
		if ('minimum' == $view)
		{
			$view = 'A1:Z10000';
		}

		$range = '&range=' . $view;
	}

	return <<<EOS
<iframe src="http://spreadsheets.google.com/pub?key=$key&single=true$range&output=html&gid=$sheetId" width="$width" height="$height" marginwidth="0" marginheight="0" frameborder="0"></iframe>
EOS;
}

// Google Chrome pastes links as <a> tags, eg: <a href="http://spreadsheets.google.com/pub?key=0AtFXq8XyejTEdGNEZm1NZUJ3V3QwXzIwZnQ1ckh0MlE&amp;hl=en&amp;output=html">http://spreadsheets.google.com/pub?key=0AtFXq8XyejTEdGNEZm1NZUJ3V3QwXzIwZnQ1ckh0MlE&amp;hl=en&amp;output=html</a>
function tgn_ee_extractKey_google_spreadsheet($keyOrUrl, &$key, &$sheetId)
{
	preg_match('@(?:\bkey=)([-\w]+)|([-\w]+)$@', $keyOrUrl, $matches);
	$key = $matches[ count($matches) - 1 ];

	$sheetId =
		preg_match('@#gid=(\d+)@', $keyOrUrl, $matches)
		 ? $matches[1]
		 : '0';
}

/*
function tgn_ee_extractKey_authKey_google_spreadsheet($keyOrUrl, &$key, &$authkey)
{
	if (false !== strpos($keyOrUrl, '?'))
	{
		preg_match('@(?:\bkey=)([-\w]+)|([-\w]+)$@', $keyOrUrl, $matches);

		return $matches[ count($matches) - 1 ];
	}

	return $keyOrUrl
}
*/


//==============================================================================
// <iframe>
// Embed another website inside your post
//

function tgn_ee_iframe($src, $width, $height)
{
	if (!startsWith($src, 'http'))
	{
		$src = 'http://' . $src;
	}

  return <<<EOS
<iframe src="$src" width="$width" height="$height" marginwidth="0" marginheight="0" frameborder="0"></iframe>
EOS;
}


//==============================================================================
// Machform
// appnitro.com
// PHP HTML form builder
//

function tgn_ee_machform_iframe($key, $width, $height)
{
	$siteUrl = get_option('siteurl'); // no trailing slash

  return <<<EOS
<iframe width="$width" height="$height" marginwidth="0" marginheight="0" frameborder="0" scrolling="no" allowTransparency="true" style="border:none" src="$siteUrl/machform/embed.php?id=$key"></iframe>
EOS;
}

function tgn_ee_machform_php($key, $width, $height)
{
	$siteUrl = get_option('siteurl');

	require 'machform/machform.php';
	$mf_param['form_id'] = (integer)$key;
	$mf_param['base_path'] = $siteUrl . '/';
	display_machform($mf_param);
}


//==============================================================================
// UStream
// ustream.tv

function tgn_ee_ustream_shortcode($values)
{
	$values = tgn_ee_parse_shortcode(array(
		'key' => '7641597',
		'width' => tgn_ee_defaultWidth,
		'height' => tgn_ee_defaultYouTubeIframeHeight,
		'autoplay' => 'false',
	), $values);

	return tgn_ee_ustream_object($values);
}

add_shortcode('ustream', 'tgn_ee_ustream_shortcode');

function tgn_ee_ustream_object($values)
{
  $key = $values['key']; unset($values['key']);
	$width = $values['width']; unset($values['width']);
	$height = $values['height']; unset($values['height']);
	$autoplay = $values['autoplay']; unset($values['autoplay']);

  return <<<EOS
<object data="http://www.ustream.tv/flash/viewer.swf" type="application/x-shockwave-flash" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="$width" height="$height" id="utv949721"><param name="flashvars" value="autoplay=$autoplay&amp;brand=embed&amp;cid=$key&amp;v3=1"/><param name="allowfullscreen" value="true"/><param name="allowscriptaccess" value="always"/><param name="movie" value="http://www.ustream.tv/flash/viewer.swf"/></object>
EOS;
}

function tgn_ee_ustream_chat_shortcode($values)
{
	$values = tgn_ee_parse_shortcode(array(
		'key' => '7641597',
		'width' => tgn_ee_defaultWidth,
		'height' => tgn_ee_defaultYouTubeIframeHeight,
	), $values);

	return tgn_ee_ustream_chat_object($values);
}

add_shortcode('ustreamchat', 'tgn_ee_ustream_chat_shortcode');

function tgn_ee_ustream_chat_object($values)
{
  $key = $values['key']; unset($values['key']);
	$width = $values['width']; unset($values['width']);
	$height = $values['height']; unset($values['height']);

  return <<<EOS
<embed width="$width" height="$height" type="application/x-shockwave-flash" flashvars="brandId=1&amp;channelId=$key&amp;channel=%23the-teamcraft-livestream&amp;server=chat1.ustream.tv&amp;locale=en_US" pluginspage="http://www.adobe.com/go/getflashplayer" src="http://www.ustream.tv/flash/irc.swf" allowfullscreen="true" />
EOS;
}



//==============================================================================
// Blip TV
// blip.tv
//

function tgn_ee_blip_shortcode($values)
{
	$values = tgn_ee_parse_shortcode(array(
		'key' => 'AYH_4WYC',
		'width' => tgn_ee_defaultWidth,
		'height' => tgn_ee_defaultYouTubeIframeHeight,
	), $values);

	return tgn_ee_blip_iframe($values);
}

add_shortcode('blip', 'tgn_ee_blip_shortcode');


function tgn_ee_blip_iframe($values)
{
  $key = tgn_ee_extractKey_blip($values['key']); unset($values['key']);
	$width = $values['width']; unset($values['width']);
	$height = $values['height']; unset($values['height']);

//	$values = tgn_ee_parse_valuesArray($values);

  return <<<EOS
<embed src="http://blip.tv/play/$key" type="application/x-shockwave-flash" width="$width" height="$height" allowscriptaccess="always" allowfullscreen="true"></embed>
EOS;
}

// http://blip.tv/file/4171545/
function tgn_ee_extractKey_blip($keyOrUrl)
{
	return
		preg_match('@([^/]+)$@', $keyOrUrl, $matches)
		 ? $matches[1]
		 : $keyOrUrl;
}


//==============================================================================
// Dailymotion
// dailymotion.com
//

function tgn_ee_dailymotion_shortcode($values)
{
	$values = tgn_ee_parse_shortcode(array(
		'key' => 'x2alc3', //'xg7km9',
		'width' => tgn_ee_defaultWidth,
		'height' => tgn_ee_defaultYouTubeIframeHeight,
		'theme' => 'none',
		'foreground' => '%23F7FFFD',
		'highlight' => '%23FFC300',
		'background' => '%23171D1B',
		'theme' => 'none',
		'start' => '',
		'animatedTitle' => '',
		'iframe' => '0',
		'additionalInfos' => '0',
		'autoPlay' => '0',
		'hideInfos' => '0',
	), $values);

	return tgn_ee_dailymotion_iframe($values);
}

add_shortcode('dailymotion', 'tgn_ee_dailymotion_shortcode');


function tgn_ee_dailymotion_iframe($values)
{
  $key = tgn_ee_extractKey_dailymotion($values['key']); unset($values['key']);
	$width = $values['width']; unset($values['width']);
	$height = $values['height']; unset($values['height']);

	$values = tgn_ee_parse_valuesArray($values);

  return <<<EOS
<object data="http://www.dailymotion.com/swf/video/$key$values" type="application/x-shockwave-flash" width="$width" height="$height"><param name="movie" value="http://www.dailymotion.com/swf/video/$key$values"></param><param name="allowFullScreen" value="true"></param><param name="allowScriptAccess" value="always"></param></object>
EOS;
}

// http://www.dailymotion.com/video/xg7km9_top-10-funniest-christmas-movies_shortfilms
function tgn_ee_extractKey_dailymotion($keyOrUrl)
{
	return
		preg_match('@video/([^_]+)@', $keyOrUrl, $matches)
		 ? $matches[1]
		 : $keyOrUrl;
}


//==============================================================================
// Google Video
// googlevideo.com
//

function tgn_ee_googlevideo_shortcode($values)
{
	$values = tgn_ee_parse_shortcode(array(
		'key' => '5203113135725371887',
		'width' => tgn_ee_defaultWidth,
		'height' => tgn_ee_defaultYouTubeIframeHeight,
	), $values);

	return tgn_ee_googlevideo_iframe($values);
}

add_shortcode('googlevideo', 'tgn_ee_googlevideo_shortcode');


function tgn_ee_googlevideo_iframe($values)
{
  $key = tgn_ee_extractKey_googlevideo($values['key']); unset($values['key']);
	$width = $values['width']; unset($values['width']);
	$height = $values['height']; unset($values['height']);

	$values = tgn_ee_parse_valuesArray($values);

  return <<<EOS
<embed src="http://video.google.com/googleplayer.swf?docid=$key&hl=en&fs=true" style="width:{$width}px;height:{$height}px" allowFullScreen="true" allowScriptAccess="always" type="application/x-shockwave-flash"></embed>
EOS;
}

// http://video.google.com/videoplay?docid=-6904839521202283023#docid=-2858281161455552130
// http://video.google.com/videoplay?docid=-2858281161455552130&hl=en#docid=5203113135725371887
function tgn_ee_extractKey_googlevideo($keyOrUrl)
{
	return
		preg_match('@=([^=]+)$@', $keyOrUrl, $matches)
		 ? $matches[1]
		 : $keyOrUrl;
}


//==============================================================================
// Metacafe
// metacafe.com
//

function tgn_ee_metacafe_shortcode($values)
{
	$values = tgn_ee_parse_shortcode(array(
		'key' => '1203580',
		'width' => tgn_ee_defaultWidth,
		'height' => tgn_ee_defaultYouTubeIframeHeight,
		'showStats' => 'yes',
		'autoPlay' => 'no',
		'videoTitle' => '',
	), $values);

	return tgn_ee_metacafe_iframe($values);
}

add_shortcode('metacafe', 'tgn_ee_metacafe_shortcode');

function tgn_ee_metacafe_iframe($values)
{
  $key = tgn_ee_extractKey_metacafe($values['key']); unset($values['key']);
	$width = $values['width']; unset($values['width']);
	$height = $values['height']; unset($values['height']);

	extract($values);
//	$values = tgn_ee_parse_valuesArray($values);

  return <<<EOS
<embed flashVars="playerVars=showStats=$showStats|autoPlay=$autoPlay|videoTitle=$videoTitle" src="http://www.metacafe.com/fplayer/$key/.swf" width="$width" height="$height" wmode="transparent" allowFullScreen="true" allowScriptAccess="always" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>
EOS;
}

// http://www.metacafe.com/watch/1203580/top_10_funniest_super_bowl_commercials_of_2008/
function tgn_ee_extractKey_metacafe($keyOrUrl)
{
	return
		preg_match('@\d+@', $keyOrUrl, $matches)
		 ? $matches[0]
		 : $keyOrUrl;
}


//==============================================================================
// Veoh
// veoh.com
//

function tgn_ee_veoh_shortcode($values)
{
	$values = tgn_ee_parse_shortcode(array(
		'key' => 'v387685pCn9tzwh',
		'width' => tgn_ee_defaultWidth,
		'height' => tgn_ee_defaultYouTubeIframeHeight,
	), $values);

	return tgn_ee_veoh_iframe($values);
}

add_shortcode('veoh', 'tgn_ee_veoh_shortcode');


function tgn_ee_veoh_iframe($values)
{
  $key = tgn_ee_extractKey_veoh($values['key']); unset($values['key']);
	$width = $values['width']; unset($values['width']);
	$height = $values['height']; unset($values['height']);

//	$values = tgn_ee_parse_valuesArray($values);

  return <<<EOS
<object data="http://www.veoh.com/static/swf/webplayer/WebPlayer.swf?version=AFrontend.5.5.4.1002&permalinkId=$key&player=videodetailsembedded&videoAutoPlay=0&id=anonymous" type="application/x-shockwave-flash" width="$width" height="$height"><param name="movie" value="http://www.veoh.com/static/swf/webplayer/WebPlayer.swf?version=AFrontend.5.5.4.1002&permalinkId=$key&player=videodetailsembedded&videoAutoPlay=0&id=anonymous"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param></object>
EOS;
}

// http://www.veoh.com/browse/videos/category/sports/watch/v387685pCn9tzwh
function tgn_ee_extractKey_veoh($keyOrUrl)
{
	return
		preg_match('@/([^/]+)$@', $keyOrUrl, $matches)
		 ? $matches[1]
		 : $keyOrUrl;
}


//==============================================================================
// Vimeo
// vimeo.com
//

function tgn_ee_vimeo_shortcode($values)
{
	$values = tgn_ee_parse_shortcode(array(
		'key' => '14702395',
		'width' => tgn_ee_defaultWidth,
		'height' => tgn_ee_defaultYouTubeIframeHeight,
		'title' => '0',
		'byline' => '0',
		'portrait' => '0',
	), $values);

	return tgn_ee_vimeo_iframe($values);
}

add_shortcode('vimeo', 'tgn_ee_vimeo_shortcode');


function tgn_ee_vimeo_iframe($values)
{
  $key = tgn_ee_extractKey_vimeo($values['key']); unset($values['key']);
	$width = $values['width']; unset($values['width']);
	$height = $values['height']; unset($values['height']);

	$values = tgn_ee_parse_valuesArray($values);

  return <<<EOS
<iframe src="http://player.vimeo.com/video/$key?$values" width="$width" height="$height" frameborder="0" marginwidth="0" marginheight="0"></iframe>
EOS;
}

// http://vimeo.com/2481023
function tgn_ee_extractKey_vimeo($keyOrUrl)
{
	return
		preg_match('@\d+@', $keyOrUrl, $matches)
		 ? $matches[0]
		 : $keyOrUrl;
}


//==============================================================================
// YouTube
// youtube.com
// Broadcast yourself
//

function tgn_ee_youtube_iframe($values)
{
  $key = tgn_ee_extractKey_youtube($values['key']); unset($values['key']);
	$width = $values['width']; unset($values['width']);
	$height = $values['height']; unset($values['height']);

	$values = tgn_ee_parse_valuesArray($values);

  return <<<EOS
<iframe width="$width" height="$height" src="http://www.youtube.com/embed/$key$values" type="text/html" frameborder="0" marginwidth="0" marginheight="0"></iframe>
EOS;
}

function tgn_ee_youtube_object($values)
{
  $key = tgn_ee_extractKey_youtube($values['key']); unset($values['key']);
	$width = $values['width']; unset($values['width']);
	$height = $values['height']; unset($values['height']);

	$values = tgn_ee_parse_valuesArray($values);

  return <<<EOS
<object data="http://www.youtube.com/v/$key$values" type="application/x-shockwave-flash" width="$width" height="$height">
	<param name="movie" value="http://www.youtube.com/v/$key$values"></param>
	<param name="wmode" value="opaque"></param>
	<param name="allowFullScreen" value="true"></param>
	<param name="allowScriptAccess" value="always"></param>
</object>
EOS;
}

function tgn_ee_extractKey_youtube($keyOrUrl)
{
	preg_match('@(?:\?v=|/v/)([-\w]+)|([-\w]+)$@', $keyOrUrl, $matches);
	return $matches[ count($matches) - 1 ];

//	$values = parse_url( $url, PHP_URL_QUERY );
//	parse_str($values);

//	if (isSet($v))
//	{
//		return $v;
//	}

//	return 'not found';
}

//function tgn_ee_extractKey_youtube_test()
//{
// jzNmX69h_HA
// http://www.youtube.com/watch?v=jzNmX69h_HA
// http://www.youtube.com/watch?v=jzNmX69h_HA&feature=player_embedded
// http://www.youtube.com/v/jzNmX69h_HA?fs=1&amp;hl=en_US
// http://www.youtube.com/tgnDragonAge2#p/u/0/jzNmX69h_HA
// http://www.youtube.com/tgnWorldOfWarcraft#p/c/FD3ACB6A1128406A/3/ZXKobFJWuyc
//
//	$s = 'jzNmX69h_HA';
//	echo tgn_ee_extractKey_youtube($s);
//	echo '<br><br>';
//
//	$s = 'http://www.youtube.com/watch?v=jzNmX69h_HA';
//	echo tgn_ee_extractKey_youtube($s);
//	echo '<br><br>';
//
//	$s = 'http://www.youtube.com/watch?v=jzNmX69h_HA&feature=player_embedded';
//	echo tgn_ee_extractKey_youtube($s);
//	echo '<br><br>';
//
//	$s = 'http://www.youtube.com/v/jzNmX69h_HA?fs=1&amp;hl=en_US';
//	echo tgn_ee_extractKey_youtube($s);
//	echo '<br><br>';
//
//	$s = 'http://www.youtube.com/user/tgnDragonAge2#p/u/0/jzNmX69h_HA';
//	echo tgn_ee_extractKey_youtube($s);
//	echo '<br><br>';
//
//	$s = 'http://www.youtube.com/tgnWorldOfWarcraft#p/c/FD3ACB6A1128406A/3/ZXKobFJWuyc';
//	echo tgn_ee_extractKey_youtube($s);
//	echo '<br><br>';
//}


//==============================================================================
// VideoReadr
// videoreadr.com
// Enhance YouTube videos with bookmarks and transcripts
//
function tgn_ee_videoreadr_script($values)
{
//	$background_urlEncoded = str_replace('#', '%23', $background);

  $youtubeId = tgn_ee_extractKey_youtube($values['youtubeId']); unset($values['youtubeId']);
  $videoreadrId = $values['videoreadrId']; unset($values['videoreadrId']);
	$width = $values['width']; unset($values['width']);
	$height = $values['height']; unset($values['height']);

	$values = tgn_ee_parse_valuesArray($values);

	$videoElementId = 'video_' . rand();

  return <<<EOS
<script src="http://videoreadr.com/javascripts/videoreadr.js?1286482226" type="text/javascript"></script>
<div class="youtube_video_container" id="$videoElementId"></div><div style="margin-top:5px" class="videoreadr" data-embed="true" data-infusionId="$videoreadrId" data-layout="transcript2" data-theme="dark" data-video="external" data-autostart="false"></div>
<script type="text/javascript">
swfobject.embedSWF('http://www.youtube.com/v/$youtubeId$values', '$videoElementId', '$width', '$height', '8', null, null, { allowScriptAccess:'always', wmode:'opaque', allowfullscreen:true }, {});
</script>
EOS;

// old way
//  return <<<EOS
//<script src="http://videoreadr.com/read/$key.js?embed=true&width=$width&height=$height&background=$background_urlEncoded&layout=transcript2" type="text/javascript"></script>
//EOS;
}

function tgn_ee_videoreadr_iframe($key, $width, $height, $background)
{
	$background_urlEncoded = str_replace('#', '%23', $background);

  return <<<EOS
<iframe src="http://videoreadr.com/read/$key?embed=true&width=$width&height=$height&background=$background_urlEncoded&layout=transcript2" width="$width" height="$height" marginwidth="0" marginheight="0" frameborder="0"></iframe>
EOS;
}


//==============================================================================
// Utility
// Helper functions
//

function startsWith($haystack, $needle) { return strpos($haystack, $needle, 0) === 0; }

//
// Swap width and height if only one given
// * [pdf tgnbooks.com/the-great-gatsby.pdf 400] // assumes height:400
// * [pdf tgnbooks.com/the-great-gatsby.pdf 600 400] // assumes width:600 height:400
//
function tgn_ee_parse_shortcode_height_first($widthIndex, $defaults, $values)
{
	if (is_array($values) && isSet($values[$widthIndex]) && !isSet($values[$widthIndex + 1]))
	{
		$newDefaults = array();
		$i = 0;

		foreach ($defaults as $key => $value)
		{
			if ($i == $widthIndex) // found the width
			{
				$widthKey = $key;
				$widthValue = $value;
			}
			else
			{
				$newDefaults[$key] = $value;

				if ($i == $widthIndex + 1) // found the height
				{
					$newDefaults[$widthKey] = $widthValue; // swap width and height positions
				}
			}

			$i++;
		}

		return tgn_ee_parse_shortcode($newDefaults, $values);
	}

	return tgn_ee_parse_shortcode($defaults, $values);
}

//
// [youtube]                            -> key:default,     width:default, height:default
// [youtube=X8gBdsBjpJw]                -> key:X8gBdsBjpJw, width:default, height:default (Equals (=) is optional)
// [youtube X8gBdsBjpJw]                -> key:X8gBdsBjpJw, width:default, height:default
// [youtube X8gBdsBjpJw 200]            -> key:X8gBdsBjpJw, width:200,     height:default
// [youtube X8gBdsBjpJw height=200]     -> key:X8gBdsBjpJw, width:default, height:200
// [youtube X8gBdsBjpJw height=200 300] -> (Unsupported: shorthand values must appear before key:value pairs)
//
function tgn_ee_parse_shortcode($defaults, $values)
{
	// commented: can do this in one loop, not two
//	$out = shortcode_atts($defaults, $values);

	if (is_array($values)) // if ('' === $values) // $values === '' if [shortcode] has no atributes
	{
		$out = array();
		reset($defaults);

		// process shorthand values first in $defaults order
		if (isSet($values[0]))
		{
			$key = key($defaults);
			$out[$key] = ltrim($values[0], '='); // first value may start with '=', eg: [youtube=X8gBdsBjpJw] -> values[0] = '=X8gBdsBjpJw'

			for ($i = 1; false !== next($defaults) && isSet($values[$i]); $i++)
			{
				$key = key($defaults);
				$out[$key] = $values[$i];
			}
		}

		// process key:value pairs next, continuing from shorthand values in $defaults order
		while (list($key, $value) = each($defaults))
		{
			$out[$key] = array_key_exists($key, $values) ? $values[$key] : $value;
		}
	}
	else
	{
		$out = $defaults; // creates a copy of $defaults
	}

	return $out;
}

function tgn_ee_parse_valuesArray($values)
{
	if (0 < count($values))
	{
		$keyValues = array();

		foreach ($values as $key => $value)
		{
			if ('' != $value)
			{
				$keyValues[] = $key . '=' . $value;
			}
		}

		return '?' . implode('&', $keyValues);
	}
	else
	{
		return '';
	}
}

?>
