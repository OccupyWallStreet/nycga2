<?php
/*
Simple:Press
Admin Profiles
$LastChangedDate: 2010-08-14 04:52:34 -0700 (Sat, 14 Aug 2010) $
$Rev: 4424 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# Check Whether User Can Manage Profiles
if (!sfc_current_user_can('SPF Manage Profiles'))
{
	echo (__('Access Denied', "sforum"));
	die();
}

global $SFSTATUS;

include_once (SF_PLUGIN_DIR.'/admin/panel-profiles/sfa-profiles-display.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-profiles/support/sfa-profiles-prepare.php');
include_once (SF_PLUGIN_DIR.'/admin/library/sfa-tab-support.php');

if($SFSTATUS != 'ok')
{
	include_once (SFINSTALL);
	die();
}

global $adminhelpfile;
$adminhelpfile = 'admin-profiles';
# --------------------------------------------------------------------

if(isset($_GET['tab']) ? $tab=$_GET['tab'] : $tab='options');
sfa_header();
sfa_render_profiles_panel($tab);
sfa_footer();

?>