<?php
/*
Simple:Press
Admin ADmins
$LastChangedDate: 2010-12-30 11:09:51 -0700 (Thu, 30 Dec 2010) $
$Rev: 5199 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# Check Whether User Can Manage Admins
global $sfglobals, $SFSTATUS;

if (!sfc_current_user_can('SPF Manage Admins') && !$sfglobals['member']['admin'] && !$sfglobals['member']['moderator'])
{
	echo (__('Access Denied', "sforum"));
	die();
}

include_once (SF_PLUGIN_DIR.'/admin/panel-admins/sfa-admins-display.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-admins/support/sfa-admins-prepare.php');
include_once (SF_PLUGIN_DIR.'/admin/library/sfa-tab-support.php');

if($SFSTATUS != 'ok')
{
    include_once (SFINSTALL);
    die();
}

global $adminhelpfile;
$adminhelpfile = 'admin-admins';
# --------------------------------------------------------------------

if(isset($_GET['tab']) ? $tab=$_GET['tab'] : $tab='youradmin');
sfa_header();
sfa_render_admins_panel($tab);
sfa_footer();

?>