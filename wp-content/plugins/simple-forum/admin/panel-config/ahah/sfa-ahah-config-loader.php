<?php
/*
Simple:Press Admin
Ahah form loader - Config
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

include_once (SF_PLUGIN_DIR.'/admin/panel-config/sfa-config-display.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-config/support/sfa-config-prepare.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-config/support/sfa-config-save.php');
include_once (SF_PLUGIN_DIR.'/admin/library/sfa-tab-support.php');

global $sfglobals;

global $adminhelpfile;
$adminhelpfile = 'admin-config';
# --------------------------------------------------------------------

# ----------------------------------
# Check Whether User Can Manage Options
if (!sfc_current_user_can('SPF Manage Configuration'))
{
	echo (__('Access Denied', "sforum"));
	die();
}

if(isset($_GET['loadform']))
{
	sfa_render_config_container($_GET['loadform']);
	die();
}

if(isset($_GET['saveform']))
{
	if($_GET['saveform'] == 'config')
	{
		echo sfa_save_config_data();
		die();
	}

	if($_GET['saveform'] == 'saveoptions')
	{
		echo sfa_save_config_options();
		die();
	}
}

die();

?>