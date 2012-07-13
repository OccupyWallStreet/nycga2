<?php
/*
Simple:Press Admin
Ahah form loader - Forums
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
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

include_once (SF_PLUGIN_DIR.'/admin/panel-forums/sfa-forums-display.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-forums/support/sfa-forums-prepare.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-forums/support/sfa-forums-save.php');
include_once (SF_PLUGIN_DIR.'/admin/library/sfa-tab-support.php');

global $adminhelpfile;
$adminhelpfile = 'admin-forums';
# --------------------------------------------------------------------

# ----------------------------------
# Check Whether User Can Manage Forums
if (!sfc_current_user_can('SPF Manage Forums'))
{
	echo (__('Access Denied', "sforum"));
	die();
}

if (isset($_GET['loadform']))
{
	sfa_render_forums_container($_GET['loadform']);
	die();
}

if (isset($_GET['saveform']))
{
	if($_GET['saveform'] == 'creategroup')
	{
		echo sfa_save_forums_create_group();
		die();
	}
	if($_GET['saveform'] == 'createforum')
	{
		echo sfa_save_forums_create_forum();
		die();
	}
	if($_GET['saveform'] == 'globalperm')
	{
		echo sfa_save_forums_global_perm();
		die();
	}
	if($_GET['saveform'] == 'removeperms')
	{
		echo sfa_save_forums_remove_perms();
		die();
	}
	if($_GET['saveform'] == 'globalrss')
	{
		echo sfa_save_forums_global_rss();
		die();
	}
	if($_GET['saveform'] == 'globalrssset')
	{
		echo sfa_save_forums_global_rssset();
		die();
	}
	if($_GET['saveform'] == 'grouppermission')
	{
		echo sfa_save_forums_group_perm();
		die();
	}
	if($_GET['saveform'] == 'editgroup')
	{
		echo sfa_save_forums_edit_group();
		die();
	}
	if($_GET['saveform'] == 'deletegroup')
	{
		echo sfa_save_forums_delete_group();
		die();
	}
	if($_GET['saveform'] == 'editforum')
	{
		echo sfa_save_forums_edit_forum();
		die();
	}
	if($_GET['saveform'] == 'deleteforum')
	{
		echo sfa_save_forums_delete_forum();
		die();
	}
	if($_GET['saveform'] == 'addperm')
	{
		echo sfa_save_forums_forum_perm();
		die();
	}
	if($_GET['saveform'] == 'editperm')
	{
		echo sfa_save_forums_edit_perm();
		die();
	}
	if($_GET['saveform'] == 'delperm')
	{
		echo sfa_save_forums_delete_perm();
		die();
	}
}

die();

?>