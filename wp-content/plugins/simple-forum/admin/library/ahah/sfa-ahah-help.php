<?php
/*
Simple:Press
Admin Help
$LastChangedDate: 2010-04-18 09:22:20 -0700 (Sun, 18 Apr 2010) $
$Rev: 3920 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

sfa_admin_ahah_support();

if (!isset($_GET['file']))
{
	die();
}

$file = sf_esc_str($_GET['file']);
$tag = sf_esc_str($_GET['item']);
$tag = '['.$tag.']';
$folder = "panels/";

# Formatting and Display of Help Panel
$helptext = wpautop(sf_retrieve_help($file, $tag, $folder), false);

echo '<div class="sfhelptext">';
echo '<div class="sfhelptag"><p>'.sf_convert_tag($tag).'</p></div>';
echo '<fieldset>';
echo $helptext;
echo '</fieldset>';
echo '<div class="sfhelptextlogo">';
echo '<img src="'.SFADMINIMAGES.'SPF-badge-125.png" alt="" title="" />';
echo '</div></div>';

die();

function sf_retrieve_help($file, $tag, $folder)
{
	$path = SFHELP.'admin/'.$folder;
	$note = '';
	$lang = WPLANG;
	if (empty($lang))
	{
		$lang = 'en';
	}
	$helpfile = $path.$file.'.'.$lang;

	if (file_exists($helpfile) == false)
	{
		$helpfile = $path.$file.'.en';
		if (file_exists($helpfile) == false)
		{
			return __("No Help File can be located", "sforum");
		} else {
			$note = __("Sorry - A Help File can not be found in your language", "sforum");
		}
	}

	$fh = fopen($helpfile, 'r');

	do {
		$theData = fgets($fh);
		if (feof($fh))
		{
			break;
		}
	} while ((substr($theData, 0, strlen($tag)) != $tag));

	$theData = '';
	$theEnd = false;
	do {
		if (feof($fh))
		{
			break;
		}
		$theLine = fgets($fh);
		if (substr($theLine, 0, 5) == '[end]')
		{
			$theEnd = true;
		} else {
			$theData.= $theLine;
		}
	} while ($theEnd == false);

	fclose($fh);

	return $note.'<br /><br />'.$theData;
}

function sf_convert_tag($tag)
{
	$tag = str_replace ('[', '', $tag);
	$tag = str_replace (']', '', $tag);
	$tag = str_replace ('-', ' ', $tag);
	$tag = str_replace ('_', ' ', $tag);
	return ucwords($tag);
}

?>