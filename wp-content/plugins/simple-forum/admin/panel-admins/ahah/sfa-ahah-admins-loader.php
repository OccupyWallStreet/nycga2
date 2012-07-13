<?php
/*
Simple:Press Admin
Ahah form loader - Admins
$LastChangedDate: 2010-04-04 11:21:48 -0700 (Sun, 04 Apr 2010) $
$Rev: 3873 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

sfa_admin_ahah_support();

global $SFSTATUS;
if($SFSTATUS != 'ok')
{
	echo($SFSTATUS);
	die();
}

include_once (SF_PLUGIN_DIR.'/admin/panel-admins/sfa-admins-display.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-admins/support/sfa-admins-prepare.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-admins/support/sfa-admins-save.php');
include_once (SF_PLUGIN_DIR.'/admin/library/sfa-tab-support.php');

global $adminhelpfile, $sfglobals;
$adminhelpfile = 'admin-admins';
# --------------------------------------------------------------------

# ----------------------------------
# Check Whether User Can Manage Options
sf_initialise_globals();
$modchk = ($sfglobals['member']['admin'] || $sfglobals['member']['moderator']) && ((isset($_GET['saveform']) && $_GET['saveform'] == 'youradmin') || (isset($_GET['loadform']) && $_GET['loadform'] == 'youradmin'));
if (!sfc_current_user_can('SPF Manage Admins') && !$modchk)
{
	echo (__('Access Denied', "sforum"));
	die();
}

if(isset($_GET['loadform']))
{
	sfa_render_admins_container($_GET['loadform']);
	die();
}

if(isset($_GET['saveform']))
{
	if($_GET['saveform'] == 'youradmin')
	{
		echo sfa_save_admins_your_options_data();
		die();
	}
	if($_GET['saveform'] == 'globaladmin')
	{
		echo sfa_save_admins_global_options_data();
		die();
	}
	if($_GET['saveform'] == 'manageadmin')
	{
		echo sfa_save_admins_caps_data();
		die();
	}
	if($_GET['saveform'] == 'addadmin')
	{
		echo sfa_save_admins_newadmin_data();
		die();
	}
	if($_GET['saveform'] == 'colourrestore')
	{
		echo sfa_save_admins_restore_colour();
		die();
	}
}

die();

?>