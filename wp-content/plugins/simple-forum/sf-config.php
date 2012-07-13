<?php
/*
Simple:Press
wp-config.php - location support for WP 2.6
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

if(file_exists(dirname(__FILE__).'/sf-user-switches.php'))
{
	include_once (dirname(__FILE__).'/sf-user-switches.php');
	$user_switches=true;
} else {
	define('SF_BASEPATH', dirname(dirname(dirname(dirname(__FILE__)))));
	$user_switches=false;
}

if(!$user_switches)
{
	define('SF_USE_PRETTY_CBOX', true);
	define('CONCATENATE_SCRIPTS', false);
}

?>