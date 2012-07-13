<?php
/*
Simple:Press Users Admin
Ahah form loader - Users
$LastChangedDate: 2009-11-08 14:27:41 -0700 (Sun, 08 Nov 2009) $
$Rev: 2939 $
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

include_once (SF_PLUGIN_DIR.'/admin/panel-users/sfa-users-display.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-users/support/sfa-users-prepare.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-users/support/sfa-users-save.php');
include_once (SF_PLUGIN_DIR.'/admin/library/sfa-tab-support.php');

global $adminhelpfile;
$adminhelpfile = 'admin-users';
# --------------------------------------------------------------------

# ----------------------------------
# Check Whether User Can Manage Users
if (!sfc_current_user_can('SPF Manage Users'))
{
	echo (__('Access Denied', "sforum"));
	die();
}

if (isset($_GET['loadform']))
{
	sfa_render_users_container($_GET['loadform']);
	die();
}

if (isset($_GET['saveform']))
{
	if($_GET['saveform'] == 'newusergroup')
	{
		echo sfa_save_users_new_usergroup();
		die();
	}
	if($_GET['saveform'] == 'killspamreg')
	{
		echo sfa_save_kill_spam_reg();
		die();
	}
}

die();

?>