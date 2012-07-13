<?php
/*
Simple:Press
Admin Config Support Functions
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_get_config_data()
{
	$sfconfig = array();
	$sfconfig = sf_get_option('sfconfig');

	return $sfconfig;
}

function sfa_prepare_config_optimisation()
{
	$sfsupport = array();
	$sfsupport = sf_get_option('sfsupport');

	return $sfsupport;
}

?>