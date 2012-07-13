<?php
/*
Simple:Press
Admin Panels - Toolbox
$LastChangedDate: 2010-08-14 04:52:34 -0700 (Sat, 14 Aug 2010) $
$Rev: 4424 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# Check Whether User Can Manage Toolbox
if (!sfc_current_user_can('SPF Manage Toolbox'))
{
	echo (__('Access Denied', "sforum"));
	die();
}

global $SFSTATUS;

include_once (SF_PLUGIN_DIR.'/admin/panel-toolbox/sfa-toolbox-display.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-toolbox/support/sfa-toolbox-prepare.php');
include_once (SF_PLUGIN_DIR.'/admin/library/sfa-tab-support.php');

if($SFSTATUS != 'ok')
{
	include_once (SFINSTALL);
	die();
}

global $adminhelpfile;
$adminhelpfile = 'admin-toolbox';
# --------------------------------------------------------------------

if(isset($_GET['tab']))
{
	$formid=$_GET['tab'];
} else {
	if (isset($_GET['form']))
	{
		$formid = $_GET['form'];
	} else {
		$formid = 'toolbox';
	}
}

sfa_header();
sfa_render_toolbox_panel($formid);
sfa_footer();

?>