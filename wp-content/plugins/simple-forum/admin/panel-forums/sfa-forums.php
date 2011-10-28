<?php
/*
Simple:Press
Admin Forums
$LastChangedDate: 2010-08-14 04:52:34 -0700 (Sat, 14 Aug 2010) $
$Rev: 4424 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# Check Whether User Can Manage Forums
global $sfglobals, $SFSTATUS;
if (!sfc_current_user_can('SPF Manage Forums'))
{
	echo (__('Access Denied', "sforum"));
	die();
}

include_once (SF_PLUGIN_DIR.'/admin/panel-forums/sfa-forums-display.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-forums/support/sfa-forums-prepare.php');
include_once (SF_PLUGIN_DIR.'/admin/library/sfa-tab-support.php');

if($SFSTATUS != 'ok')
{
    include_once (SFINSTALL);
    die();
}

global $adminhelpfile;
$adminhelpfile = 'admin-forums';
# --------------------------------------------------------------------

if(isset($_GET['tab']) ? $tab=$_GET['tab'] : $tab='forums');
sfa_header();
sfa_render_forums_panel($tab);
sfa_footer();

?>