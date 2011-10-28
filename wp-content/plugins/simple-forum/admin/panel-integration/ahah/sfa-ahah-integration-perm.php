<?php
/*
Simple:Press Admin
Ahah call for permalink update/integration
$LastChangedDate: 2010-04-18 09:22:20 -0700 (Sun, 18 Apr 2010) $
$Rev: 3920 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

sfa_admin_ahah_support();

global $sfglobals;

# ----------------------------------
# Check Whether User Can Manage Toolbox
if (!sfc_current_user_can('SPF Manage Options'))
{
	echo (__('Access Denied', "sforum"));
	die();
}

if (isset($_GET['item']))
{
	$item = $_GET['item'];
	if($item == 'upperm') sfa_update_permalink_tool();
}

die();

function sfa_update_permalink_tool()
{
	echo '<strong>&nbsp;'.sfg_update_permalink(true).'</strong>';
	die();
}

die();

?>