<?php
/*
Simple:Press
Admin User Groups
$LastChangedDate: 2010-08-14 04:52:34 -0700 (Sat, 14 Aug 2010) $
$Rev: 4424 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# Check Whether User Can Manage User Groups
if (!sfc_current_user_can('SPF Manage User Groups'))
{
	echo (__('Access Denied', "sforum"));
	die();
}

global $SFSTATUS;

include_once (SF_PLUGIN_DIR.'/admin/panel-usergroups/sfa-usergroups-display.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-usergroups/support/sfa-usergroups-prepare.php');
include_once (SF_PLUGIN_DIR.'/admin/library/sfa-tab-support.php');

if($SFSTATUS != 'ok')
{
	include_once (SFINSTALL);
	die();
}

global $adminhelpfile;
$adminhelpfile = 'admin-usergroups';
# --------------------------------------------------------------------

if(isset($_GET['tab']) ? $tab=$_GET['tab'] : $tab='usergroups');
sfa_header();
sfa_render_usergroups_panel($tab);
sfa_footer();

?>