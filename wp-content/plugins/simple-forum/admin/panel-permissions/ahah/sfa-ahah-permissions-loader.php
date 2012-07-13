<?php
/*
Simple:Press Permissions Admin
Ahah form loader - Permissions
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

include_once (SF_PLUGIN_DIR.'/admin/panel-permissions/sfa-permissions-display.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-permissions/support/sfa-permissions-prepare.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-permissions/support/sfa-permissions-save.php');
include_once (SF_PLUGIN_DIR.'/admin/library/sfa-tab-support.php');

global $adminhelpfile;
$adminhelpfile = 'admin-permissions';
# --------------------------------------------------------------------

# ----------------------------------
# Check Whether User Can Manage Forums
if (!sfc_current_user_can('SPF Manage Permissions'))
{
	echo (__('Access Denied', "sforum"));
	die();
}

if (isset($_GET['loadform']))
{
	sfa_render_permissions_container($_GET['loadform']);
	die();
}

if (isset($_GET['saveform']))
{
	if($_GET['saveform'] == 'addperm')
	{
		echo sfa_save_permissions_new_role();
		die();
	}
	if($_GET['saveform'] == 'editperm')
	{
		echo sfa_save_permissions_edit_role();
		die();
	}
	if($_GET['saveform'] == 'delperm')
	{
		echo sfa_save_permissions_delete_role();
		die();
	}
}

die();

?>