<?php
/*
Simple:Press
Admin Components Display Rendering
$LastChangedDate: 2010-05-21 08:26:36 -0700 (Fri, 21 May 2010) $
$Rev: 4044 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_render_components_panel($formid)
{
?>
	<div class="clearboth"></div>

	<div class="wrap sfatag">
		<?php
			sfa_render_sidemenu();
		?>
		<div id='sfmsgspot'></div>
		<div id="sfmaincontainer">
			<?php sfa_render_components_container($formid); ?>
		</div>
			<div class="clearboth"></div>
	</div>
<?php
	return;
}

function sfa_render_components_container($formid)
{
	switch($formid)
	{
		case 'toolbar':
			include_once (SF_PLUGIN_DIR.'/admin/panel-components/forms/sfa-components-toolbar-form.php');
			sfa_components_toolbar_form();
			break;

		case 'editor':
			include_once (SF_PLUGIN_DIR.'/admin/panel-components/forms/sfa-components-editor-form.php');
			sfa_components_editor_form();
			break;

		case 'smileys':
			include_once (SF_PLUGIN_DIR.'/admin/panel-components/forms/sfa-components-smileys-form.php');
			sfa_components_smileys_form();
			break;

		case 'login':
			include_once (SF_PLUGIN_DIR.'/admin/panel-components/forms/sfa-components-login-form.php');
			sfa_components_login_form();
			break;

		case 'seo':
			include_once (SF_PLUGIN_DIR.'/admin/panel-components/forms/sfa-components-seo-form.php');
			sfa_components_seo_form();
			break;

		case 'pm':
			include_once (SF_PLUGIN_DIR.'/admin/panel-components/forms/sfa-components-pm-form.php');
			sfa_components_pm_form();
			break;

		case 'links':
			include_once (SF_PLUGIN_DIR.'/admin/panel-components/forms/sfa-components-links-form.php');
			sfa_components_links_form();
			break;

		case 'uploads':
			include_once (SF_PLUGIN_DIR.'/admin/panel-components/forms/sfa-components-uploads-form.php');
			sfa_components_uploads_form();
			break;

		case 'topicstatus':
			include_once (SF_PLUGIN_DIR.'/admin/panel-components/forms/sfa-components-topicstatus-form.php');
			sfa_components_topicstatus_form();
			break;

		case 'forumranks':
			include_once (SF_PLUGIN_DIR.'/admin/panel-components/forms/sfa-components-forumranks-form.php');
			sfa_components_forumranks_form();
			break;

		case 'addmembers':
			include_once (SF_PLUGIN_DIR.'/admin/panel-components/forms/sfa-components-special-ranks-add-form.php');
			sfa_components_sr_add_members_form($_GET['id']);
			break;

		case 'delmembers':
			include_once (SF_PLUGIN_DIR.'/admin/panel-components/forms/sfa-components-special-ranks-del-form.php');
			sfa_components_sr_del_members_form($_GET['id']);
			break;

		case 'messages':
			include_once (SF_PLUGIN_DIR.'/admin/panel-components/forms/sfa-components-messages-form.php');
			sfa_components_messages_form();
			break;

		case 'icons':
			include_once (SF_PLUGIN_DIR.'/admin/panel-components/forms/sfa-components-icons-form.php');
			sfa_components_icons_form();
			break;

		case 'tags':
			include_once (SF_PLUGIN_DIR.'/admin/panel-components/forms/sfa-components-tags-form.php');
			sfa_components_tags_form();
			break;

		case 'policies':
			include_once (SF_PLUGIN_DIR.'/admin/panel-components/forms/sfa-components-policies-form.php');
			sfa_components_policies_form();
			break;

		case 'mobile':
			include_once (SF_PLUGIN_DIR.'/admin/panel-components/forms/sfa-components-mobile-form.php');
			sfa_components_mobile_form();
			break;
	}
	return;
}

?>