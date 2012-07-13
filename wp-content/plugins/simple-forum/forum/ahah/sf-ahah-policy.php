<?php
/*
Simple:Press
Ahah call for policy popups
$LastChangedDate: 2009-11-08 00:16:45 +0000 (Sun, 08 Nov 2009) $
$Rev: 2934 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

sf_forum_ahah_support();
# ----------------------------------

if(isset($_GET['popup']))
{
	$action = sf_esc_str($_GET['popup']);
} else {
	die();
}

if($action == 'reg')
{
	echo '<fieldset>';
	echo sf_retrieve_policy_document('registration');
	echo '</fieldset>';
}

if($action == 'priv')
{
	echo '<fieldset>';
	echo sf_retrieve_policy_document('privacy');
	echo '</fieldset>';
}

die();

?>