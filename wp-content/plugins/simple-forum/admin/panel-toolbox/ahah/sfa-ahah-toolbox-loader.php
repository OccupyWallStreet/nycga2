<?php
/*
Simple:Press Admin
Ahah form loader - Toolbox
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

include_once (SF_PLUGIN_DIR.'/admin/panel-toolbox/sfa-toolbox-display.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-toolbox/support/sfa-toolbox-prepare.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-toolbox/support/sfa-toolbox-save.php');
include_once (SF_PLUGIN_DIR.'/admin/library/sfa-tab-support.php');

global $sfglobals;

global $adminhelpfile;
$adminhelpfile = 'admin-toolbox';
# --------------------------------------------------------------------

# ----------------------------------
# Check Whether User Can Manage Options
if (!sfc_current_user_can('SPF Manage Toolbox'))
{
	echo (__('Access Denied', "sforum"));
	die();
}

if(isset($_GET['loadform']))
{
	sfa_render_toolbox_container($_GET['loadform']);
	die();
}

if(isset($_GET['saveform']))
{
	if($_GET['saveform'] == 'toolbox')
	{
		echo sfa_save_toolbox_data();
		die();
	}
	if($_GET['saveform'] == 'uninstall')
	{
		echo sfa_save_uninstall_data();
		die();
	}
	if($_GET['saveform'] == 'updatedb')
	{
		echo sfa_save_toolbox_prune_topics();
		die();
	}
}

die();

?>