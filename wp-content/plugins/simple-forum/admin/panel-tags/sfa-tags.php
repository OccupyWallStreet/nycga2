<?php
/*
Simple:Press
Admin Tags
$LastChangedDate: 2010-08-14 04:52:34 -0700 (Sat, 14 Aug 2010) $
$Rev: 4424 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# Check Whether User Can Manage Tags
if (!sfc_current_user_can('SPF Manage Tags'))
{
	echo (__('Access Denied', "sforum"));
	die();
}

global $SFSTATUS;

include_once (SF_PLUGIN_DIR.'/admin/panel-tags/sfa-tags-display.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-tags/support/sfa-tags-prepare.php');
include_once (SF_PLUGIN_DIR.'/admin/library/sfa-tab-support.php');

if($SFSTATUS != 'ok')
{
	include_once (SFINSTALL);
	die();
}

global $adminhelpfile;
$adminhelpfile = 'admin-tags';
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
		$formid = 'managetags';
	}
}
sfa_render_tags_panel($formid);
sfa_footer();

?>