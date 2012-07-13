<?php
/*
Simple:Press Admin
Ahah form loader - Option
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

include_once (SF_PLUGIN_DIR.'/admin/panel-options/sfa-options-display.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-options/support/sfa-options-prepare.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-options/support/sfa-options-save.php');
include_once (SF_PLUGIN_DIR.'/admin/library/sfa-tab-support.php');

global $sfglobals;

global $adminhelpfile;
$adminhelpfile = 'admin-options';
# --------------------------------------------------------------------

# ----------------------------------
# Check Whether User Can Manage Options
if (!sfc_current_user_can('SPF Manage Options'))
{
	echo (__('Access Denied', "sforum"));
	die();
}

if(isset($_GET['loadform']))
{
	sfa_render_options_container($_GET['loadform']);
	die();
}

if(isset($_GET['saveform']))
{
	switch($_GET['saveform'])
	{
		case 'global':
		echo sfa_save_global_data();
		break;

		case 'display':
		echo sfa_save_display_data();
		break;

		case 'forums':
		echo sfa_save_forums_data();
		break;

		case 'topics':
		echo sfa_save_topics_data();
		break;

		case 'posts':
		echo sfa_save_posts_data();
		break;

		case 'content':
		echo sfa_save_content_data();
		break;

		case 'members':
		echo sfa_save_members_data();
		break;

		case 'email':
		echo sfa_save_email_data();
		break;

		case 'style':
		echo sfa_save_style_data();
		break;
	}
	die();
}

die();

?>