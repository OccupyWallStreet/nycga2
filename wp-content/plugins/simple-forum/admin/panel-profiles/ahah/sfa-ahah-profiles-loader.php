<?php
/*
Simple:Press Admin
Ahah form loader - Config
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

sfa_admin_ahah_support();

global $SFSTATUS;
if($SFSTATUS != 'ok')
{
	echo($SFSTATUS);
	die();
}

include_once (SF_PLUGIN_DIR.'/admin/panel-profiles/sfa-profiles-display.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-profiles/support/sfa-profiles-prepare.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-profiles/support/sfa-profiles-save.php');
include_once (SF_PLUGIN_DIR.'/admin/library/sfa-tab-support.php');

global $sfglobals;

global $adminhelpfile;
$adminhelpfile = 'admin-profiles';
# --------------------------------------------------------------------

# ----------------------------------
# Check Whether User Can Manage Profiles
if (!sfc_current_user_can('SPF Manage Profiles'))
{
	echo (__('Access Denied', "sforum"));
	die();
}

if(isset($_GET['loadform']))
{
	sfa_render_profiles_container($_GET['loadform']);
	die();
}

if(isset($_GET['saveform']))
{
	if($_GET['saveform'] == 'options')
	{
		echo sfa_save_options_data();
		die();
	}

	if($_GET['saveform'] == 'data')
	{
		echo sfa_save_data_data();
		die();
	}

	if($_GET['saveform'] == 'fields')
	{
		echo sfa_save_fields_data();
		die();
	}
	if($_GET['saveform'] == 'avatars')
	{
		echo sfa_save_avatars_data();
		die();
	}
}

die();

?>