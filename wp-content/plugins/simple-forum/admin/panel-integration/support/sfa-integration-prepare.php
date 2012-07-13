<?php
/*
Simple:Press
Admin integration Update Global Options Support Functions
$LastChangedDate: 2010-07-16 10:56:09 -0700 (Fri, 16 Jul 2010) $
$Rev: 4276 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_get_integration_page_data()
{
	global $wpdb;

	$sfoptions = array();

	$sfoptions['sfslug'] = sf_get_option('sfslug');
	$sfoptions['sfpage'] = sf_get_option('sfpage');
	$sfoptions['sfpermalink'] = sf_get_option('sfpermalink');
	$sfoptions['sfinloop'] = sf_get_option('sfinloop');
	$sfoptions['sfmultiplecontent'] = sf_get_option('sfmultiplecontent');
	$sfoptions['sfscriptfoot'] = sf_get_option('sfscriptfoot');

	if(!empty($sfoptions['sfslug']))
	{
		$pageslug = explode("/", $sfoptions['sfslug']);
		$thisslug = $pageslug[count($pageslug)-1];
		$pageid = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '".$thisslug."'");
		if (!$pageid)
		{
			$sfoptions['sfpage'] = 0;
			$sfoptions['sfslug'] = '';
		}
	} else {
		$sfoptions['sfpage'] = 0;
		$sfoptions['sfslug'] = '';
	}

	return $sfoptions;
}

?>