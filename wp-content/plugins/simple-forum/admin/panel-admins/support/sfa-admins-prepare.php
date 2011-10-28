<?php
/*
Simple:Press
Admin Admins Update Global Options Support Functions
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_get_admins_your_options_data()
{
	global $current_user;

	$sfadminoptions = sf_get_member_list($current_user->ID, 'admin_options');
	if (!isset($sfadminoptions['admin_options']['colors'])) $sfadminoptions['admin_options']['colors'] = sf_get_option('sfacolours');

	return $sfadminoptions;
}

function sfa_get_admins_global_options_data()
{
	global $current_user;

	$sfadminsettings = array();
	$sfoptions = array();

	$sfadminsettings = sf_get_option('sfadminsettings');
	$sfoptions['sfmodasadmin'] = $sfadminsettings['sfmodasadmin'];
	$sfoptions['sfshowmodposts'] = $sfadminsettings['sfshowmodposts'];
	$sfoptions['sftools'] = $sfadminsettings['sftools'];
	$sfoptions['sfqueue'] = $sfadminsettings['sfqueue'];
	$sfoptions['sfbaronly'] = $sfadminsettings['sfbaronly'];
	$sfoptions['sfdashboardposts'] = $sfadminsettings['sfdashboardposts'];
	$sfoptions['sfdashboardstats'] = $sfadminsettings['sfdashboardstats'];

	return $sfoptions;
}

function sfa_get_admins_caps_data()
{
	return sf_get_admins();
}

?>