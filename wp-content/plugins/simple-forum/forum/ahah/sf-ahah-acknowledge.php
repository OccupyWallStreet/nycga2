<?php
/*
Simple:Press
Ahah call for acknowledgements
$LastChangedDate: 2011-03-19 21:48:35 -0700 (Sat, 19 Mar 2011) $
$Rev: 5701 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

sf_forum_ahah_support();
# ----------------------------------

$sfstyle = array();
$sfstyle = sf_get_option('sfstyle');

$out = '';
$out.= '<div id="sfAbout">';
$out.= '<img src="'.SFADMINIMAGES.'SPF4-banner.png" alt="" title="" /><br />';

$out.= '<p>&copy; 2005-'.date('Y').' by <a href="http://www.yellowswordfish.com">Andy Staines</a> and <a href="http://cruisetalk.org/"><b>Steve Klasen</b></a></p>';
$out.= '<p><a href="http://twitter.com/simpleforum">Follow us On Twitter</a></p>';
$out.= '<hr />';

$out.= '<p>';

$out.= __("TinyMCE text editor by Moxiecode Systems: ", "sforum").'<a href="http://www.moxiecode.com/">Moxiecode</a></p>';
$out.= __("SPF File Manager based on TinyBrowser by Bryn Jones: ", "sforum").'<a href="http://www.lunarvis.com/">Lunarvis</a><br />';
$out.= __("Highslide (Popup Boxes) by Torstein H&oslash;nsi: ", "sforum").'<a href="http://highslide.com/">HighSlide</a> *<br />';
$out.= __("jQPrint by eros: ", "sforum").'<a href="http://plugins.jquery.com/project/jqPrint">jQPrint</a><br />';
$out.= __("jQuery Autocomplete by Dylan Verheul: ", "sforum").'<a href="http://dyve.net/jquery/?autocomplete">jQuery plugins</a><br />';
$out.= __("jQuery Splitter by Dave Methvin: ", "sforum").'<a href="http://methvin.com/splitter/">Dave Methvin Consulting</a><br />';
$out.= __("Math Spam Protection based on code by Michael Woehrer: ", "sforum").'<a href="http://sw-guide.de/">Software Guide</a><br />';
$out.= __("Calendar Date Picker by TengYong Ng: ", "sforum").'<a href="http://www.rainforestnet.com">Rain Forest Net</a><br />';
$out.= __("Admin Color Picker by Simon: ", "sforum").'<a href="http://www.supersite.me/">Supersite Me</a><br />';
$out.= __("Image Uploader by Andrew Valums: ", "sforum").'<a href="http://valums.com/ajax-upload/">Ajax upload</a><br />';
$out.= __("Checkbox and Radio Button transformations: ", "sforum").'<a href="http://www.no-margin-for-errors.com/">Stephane Caron</a><br />';
$out.= __("SPF tags uses some code and ideas from Simple Tags: ", "sforum").'<a href="http://www.herewithme.fr/wordpress-plugins/simple-tags">Amaury Balmer</a><br />';
$out.= __("SPF RPX implementation uses code and ideas from RPX: ", "sforum").'<a href="http://rpxwiki.com/WordpressPlugin">Brian Ellin</a><br />';
$out.= __("Program Code Syntax Highlighting: ", "sforum").'<a href="http://www.oriontransfer.co.nz/software/jquery-syntax/">Samuel Williams</a><br />';
$out.= __("Popup Tooltips by the Vertigo Project: ", "sforum").'<a href="http://www.vertigo-project.com/">Vertigo Project</a><br />';
$out.= __("Blog Linking Post Extract based on code by Bas van Doren: ", "sforum").'<a href="http://sparepencil.com/">Spare Pencil</a><br />';
$out.= __("Default 'Silk' Icon Set by Mark James: ", "sforum").'<a href="http://www.famfamfam.com/lab/icons/silk/">fam fam fam</a>';

$out.= '</p>';

$out.= '<hr />';

$out.= '<p>'.__("My thanks to all the people who have aided, abetted, coded, suggested and helped test this plugin", "sforum").'</p><br />'."\n";
$out.= '<p>* '.__("Please Note: The Highslide popup window routines can freely be used by non-commercial sites If you intend to use Simple:Press on a commercial site, a license must be purchased.", "sforum").'</p><br />'."\n";
$out.= __("This forum is using the ", "sforum").'<strong> '.$sfstyle['sfskin'].'</strong> '.__("skin and the <strong>", "sforum").' '.$sfstyle['sficon'].'</strong> '.__("icons", "sforum").'<br />'."\n";

$out.= '</div>';
echo $out;

die();

?>