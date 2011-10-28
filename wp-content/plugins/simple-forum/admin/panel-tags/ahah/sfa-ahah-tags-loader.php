<?php
/*
Simple:Press Tags Admin
Ahah form loader - Tags
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

include_once (SF_PLUGIN_DIR.'/admin/panel-tags/sfa-tags-display.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-tags/support/sfa-tags-prepare.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-tags/support/sfa-tags-save.php');
include_once (SF_PLUGIN_DIR.'/admin/library/sfa-tab-support.php');

global $adminhelpfile;
$adminhelpfile = 'admin-tags';
# --------------------------------------------------------------------

# ----------------------------------
# Check Whether User Can Manage Tags
if (!sfc_current_user_can('SPF Manage Tags'))
{
	echo (__('Access Denied', "sforum"));
	die();
}

if (isset($_GET['loadform']))
{
	sfa_render_tags_container($_GET['loadform']);
	die();
}

if (isset($_GET['saveform']))
{
	if($_GET['saveform'] == 'edittags')
	{
		echo sfa_save_tags_edit_tags();
		die();
	}
	if($_GET['saveform'] == 'renametags')
	{
		echo sfa_save_tags_rename_tags();
		die();
	}
	if($_GET['saveform'] == 'deletetags')
	{
		echo sfa_save_tags_delete_tags();
		die();
	}
	if($_GET['saveform'] == 'addtags')
	{
		echo sfa_save_tags_add_tags();
		die();
	}
	if($_GET['saveform'] == 'cleanup')
	{
		echo sfa_save_tags_cleanup_tags();
		die();
	}
}

die();

?>