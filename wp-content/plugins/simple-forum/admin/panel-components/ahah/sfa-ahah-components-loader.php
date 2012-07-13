<?php
/*
Simple:Press Admin
Ahah form loader - Components
$LastChangedDate: 2010-05-21 08:26:36 -0700 (Fri, 21 May 2010) $
$Rev: 4044 $
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

include_once (SF_PLUGIN_DIR.'/admin/panel-components/sfa-components-display.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-components/support/sfa-components-prepare.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-components/support/sfa-components-save.php');
include_once (SF_PLUGIN_DIR.'/admin/library/sfa-tab-support.php');

global $adminhelpfile;
$adminhelpfile = 'admin-components';
# --------------------------------------------------------------------

# ----------------------------------
# Check Whether User Can Manage Options
if (!sfc_current_user_can('SPF Manage Components'))
{
	echo (__('Access Denied', "sforum"));
	die();
}

if (isset($_GET['loadform']))
{
	sfa_render_components_container($_GET['loadform']);
	die();
}

if (isset($_GET['saveform']))
{
	switch($_GET['saveform'])
	{
		case 'toolbar':
			echo sfa_save_toolbar_data();
			break;

		case 'tbrestore':
			echo sfa_restore_toolbar_defaults();
			break;

		case 'editor':
			echo sfa_save_editor_data();
			break;

		case 'smileys':
			echo sfa_save_smileys_data();
			break;

		case 'login':
			echo sfa_save_login_data();
			break;

		case 'seo':
			echo sfa_save_seo_data();
			break;

		case 'pm':
			echo sfa_save_pm_data();
			break;

		case 'links':
			echo sfa_save_links_data();
			break;

		case 'uploads':
			echo sfa_save_uploads_data();
			break;

		case 'topicstatus':
			echo sfa_save_topicstatus_data();
			break;

		case 'forumranks':
			echo sfa_save_forumranks_data();
			break;

		case 'specialranks':
			switch ($_GET['action'])
			{
				case 'newrank':
					echo sfa_add_specialrank();
					break;
				case 'updaterank':
					echo sfa_update_specialrank(sf_esc_int($_GET['id']));
					break;
				case 'addmember':
					echo sfa_add_special_rank_member(sf_esc_int($_GET['id']));
					break;
				case 'delmember':
					echo sfa_del_special_rank_member(sf_esc_int($_GET['id']));
					break;
			}
			break;

		case 'messages':
			echo sfa_save_messages_data();
			break;

		case 'icons':
			echo sfa_save_icons_data();
			break;

		case 'tags':
			echo sfa_save_tags_data();
			break;

		case 'policies':
			echo sfa_save_policies_data();
			break;

		case 'mobile':
			echo sfa_save_mobile_data();
			break;
	}
	die();
}

die();

?>