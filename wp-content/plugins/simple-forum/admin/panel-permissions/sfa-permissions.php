<?php
/*
Simple:Press
Admin Permissions
$LastChangedDate: 2010-08-14 04:52:34 -0700 (Sat, 14 Aug 2010) $
$Rev: 4424 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# Check Whether User Can Manage Permissions
if (!sfc_current_user_can('SPF Manage Permissions'))
{
	echo (__('Access Denied', "sforum"));
	die();
}

global $SFSTATUS;

include_once (SF_PLUGIN_DIR.'/admin/panel-permissions/sfa-permissions-display.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-permissions/support/sfa-permissions-prepare.php');
include_once (SF_PLUGIN_DIR.'/admin/library/sfa-tab-support.php');

if($SFSTATUS != 'ok')
{
	include_once (SFINSTALL);
	die();
}

global $adminhelpfile;
$adminhelpfile = 'admin-permissions';
# --------------------------------------------------------------------

if(isset($_GET['tab']) ? $tab=$_GET['tab'] : $tab='permissions');
sfa_header();
sfa_render_permissions_panel($tab);
sfa_footer();

?>