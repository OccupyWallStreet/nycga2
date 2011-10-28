<?php
/*
Simple:Press
Configurator
$LastChangedDate: 2010-08-14 04:52:34 -0700 (Sat, 14 Aug 2010) $
$Rev: 4424 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# Check Whether User Can Manage Toolbox
if (!sfc_current_user_can('SPF Manage Configuration'))
{
	echo (__('Access Denied', "sforum"));
	die();
}

global $SFSTATUS;

include_once (SF_PLUGIN_DIR.'/admin/panel-config/sfa-config-display.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-config/support/sfa-config-prepare.php');
include_once (SF_PLUGIN_DIR.'/admin/library/sfa-tab-support.php');

if($SFSTATUS != 'ok')
{
	include_once (SFINSTALL);
	die();
}

global $adminhelpfile;
$adminhelpfile = 'admin-config';
# --------------------------------------------------------------------

if(isset($_GET['tab']) ? $tab=$_GET['tab'] : $tab='config');
if (isset($_POST['goforumupgrade'])) $tab = 'post-upgrade';
if (isset($_POST['goforuminstall'])) $tab = 'post-install';

sfa_header();
sfa_render_config_panel($tab);
sfa_footer();

?>