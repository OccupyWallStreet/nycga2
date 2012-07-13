<?php
/*
Simple:Press User Groups Admin
Ahah form loader - User Groups
$LastChangedDate: 2009-12-20 23:34:50 -0700 (Sun, 20 Dec 2009) $
$Rev: 3093 $
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

include_once (SF_PLUGIN_DIR.'/admin/panel-usergroups/sfa-usergroups-display.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-usergroups/support/sfa-usergroups-prepare.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-usergroups/support/sfa-usergroups-save.php');
include_once (SF_PLUGIN_DIR.'/admin/library/sfa-tab-support.php');

global $adminhelpfile;
$adminhelpfile = 'admin-usergroups';
# --------------------------------------------------------------------

# ----------------------------------
# Check Whether User Can Manage User Groups
if (!sfc_current_user_can('SPF Manage User Groups'))
{
	echo (__('Access Denied', "sforum"));
	die();
}

if (isset($_GET['loadform']))
{
	sfa_render_usergroups_container($_GET['loadform']);
	die();
}

if (isset($_GET['saveform']))
{
	if($_GET['saveform'] == 'newusergroup')
	{
		echo sfa_save_usergroups_new_usergroup();
		die();
	}
	if($_GET['saveform'] == 'editusergroup')
	{
		echo sfa_save_usergroups_edit_usergroup();
		die();
	}
	if($_GET['saveform'] == 'delusergroup')
	{
		echo sfa_save_usergroups_delete_usergroup();
		die();
	}
	if($_GET['saveform'] == 'addmembers')
	{
		echo sfa_save_usergroups_add_members();
		die();
	}
	if($_GET['saveform'] == 'delmembers')
	{
		echo sfa_save_usergroups_delete_members();
		die();
	}
}

die();

?>