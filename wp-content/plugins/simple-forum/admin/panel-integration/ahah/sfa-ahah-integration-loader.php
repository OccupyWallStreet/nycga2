<?php
/*
Simple:Press Admin
Ahah form loader - Integration
$LastChangedDate: 2010-04-18 09:22:20 -0700 (Sun, 18 Apr 2010) $
$Rev: 3920 $
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

include_once (SF_PLUGIN_DIR.'/admin/panel-integration/sfa-integration-display.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-integration/support/sfa-integration-prepare.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-integration/support/sfa-integration-save.php');
include_once (SF_PLUGIN_DIR.'/admin/library/sfa-tab-support.php');

global $adminhelpfile, $sfglobals;
$adminhelpfile = 'admin-integration';
# --------------------------------------------------------------------

# ----------------------------------
# Check Whether User Can Manage Options
sf_initialise_globals();
$modchk = $sfglobals['member']['moderator'] && ($_GET['saveform'] == 'youradmin' || $_GET['loadform'] == 'youradmin');
if (!sfc_current_user_can('SPF Manage Options') && !$modchk)
{
	echo (__('Access Denied', "sforum"));
	die();
}

if(isset($_GET['loadform']))
{
	sfa_render_integration_container($_GET['loadform']);
	die();
}

if(isset($_GET['saveform']))
{
	if($_GET['saveform'] == 'page')
	{
		echo sfa_save_integration_page_data();
		die();
	}
}

die();

?>