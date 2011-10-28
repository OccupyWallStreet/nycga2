<?php
/*
Simple:Press
Admin Users
$LastChangedDate: 2010-08-14 04:52:34 -0700 (Sat, 14 Aug 2010) $
$Rev: 4424 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# Check Whether User Can Manage Users
if (!sfc_current_user_can('SPF Manage Users'))
{
	echo (__('Access Denied', "sforum"));
	die();
}

global $SFSTATUS;

include_once (SF_PLUGIN_DIR.'/admin/panel-users/sfa-users-display.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-users/support/sfa-users-prepare.php');
include_once (SF_PLUGIN_DIR.'/admin/library/sfa-tab-support.php');

if($SFSTATUS != 'ok')
{
	include_once (SFINSTALL);
	die();
}

global $adminhelpfile;
$adminhelpfile = 'admin-users';
# --------------------------------------------------------------------

sfa_header();
if(isset($_GET['tab']))
{
	$formid=$_GET['tab'];
} else {
	if (isset($_GET['form']))
	{
		$formid = $_GET['form'];
	} else {
		$formid = 'members';
	}
}
sfa_render_users_panel($formid);
sfa_footer();

?>