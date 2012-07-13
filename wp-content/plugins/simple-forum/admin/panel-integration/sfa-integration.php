<?php
/*
Simple:Press
Admin Integration
$LastChangedDate: 2010-08-14 04:52:34 -0700 (Sat, 14 Aug 2010) $
$Rev: 4424 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# Check Whether User Can Manage Admins
global $sfglobals, $SFSTATUS;

if (!$sfglobals['member']['admin'] && !$sfglobals['member']['moderator'])
{
	echo (__('Access Denied', "sforum"));
	die();
}

include_once (SF_PLUGIN_DIR.'/admin/panel-integration/sfa-integration-display.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-integration/support/sfa-integration-prepare.php');
include_once (SF_PLUGIN_DIR.'/admin/library/sfa-tab-support.php');

if($SFSTATUS != 'ok')
{
    include_once (SFINSTALL);
    die();
}

global $adminhelpfile;
$adminhelpfile = 'admin-integration';
# --------------------------------------------------------------------

if(isset($_GET['tab']) ? $tab=$_GET['tab'] : $tab='page');
sfa_header();
sfa_render_integration_panel($tab);
sfa_footer();

?>