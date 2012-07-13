<?php
/*
Simple:Press
Admin Forums Display Rendering
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_render_forums_panel($formid)
{
?>
	<div class="clearboth"></div>

	<div class="wrap sfatag">
		<?php
			sfa_render_sidemenu();
		?>
		<div id='sfmsgspot'></div>
		<div id="sfmaincontainer">
			<?php sfa_render_forums_container($formid); ?>
		</div>
			<div class="clearboth"></div>
	</div>
<?php
	return;
}

function sfa_render_forums_container($formid)
{
	switch ($formid)
	{
		case 'forums':
			include_once (SF_PLUGIN_DIR.'/admin/panel-forums/sfa-forums-display-main.php');
			sfa_forums_forums_main();
			break;
		case 'creategroup':
			include_once (SF_PLUGIN_DIR.'/admin/panel-forums/forms/sfa-forums-create-group-form.php');
			sfa_forums_create_group_form();
			break;
		case 'createforum':
			include_once (SF_PLUGIN_DIR.'/admin/panel-forums/forms/sfa-forums-create-forum-form.php');
			sfa_forums_create_forum_form();
			break;
		case 'globalperm':
			include_once (SF_PLUGIN_DIR.'/admin/panel-forums/forms/sfa-forums-global-perm-form.php');
			sfa_forums_global_perm_form();
			break;
		case 'removeperms':
			include_once (SF_PLUGIN_DIR.'/admin/panel-forums/forms/sfa-forums-remove-perms-form.php');
			sfa_forums_remove_perms_form();
			break;
		case 'globalrss':
			include_once (SF_PLUGIN_DIR.'/admin/panel-forums/forms/sfa-forums-global-rss-form.php');
			sfa_forums_global_rss_form();
			break;
		case 'globalrssset':
			include_once (SF_PLUGIN_DIR.'/admin/panel-forums/forms/sfa-forums-global-rssset-form.php');
			sfa_forums_global_rssset_form(sf_esc_int($_GET['id']));
			break;
		case 'groupperm':
			include_once (SF_PLUGIN_DIR.'/admin/panel-forums/forms/sfa-forums-group-permission-form.php');
			sfa_forums_add_group_permission_form(sf_esc_int($_GET['id']));
			break;
		case 'editgroup':
			include_once (SF_PLUGIN_DIR.'/admin/panel-forums/forms/sfa-forums-edit-group-form.php');
			sfa_forums_edit_group_form(sf_esc_int($_GET['id']));
			break;
		case 'deletegroup':
			include_once (SF_PLUGIN_DIR.'/admin/panel-forums/forms/sfa-forums-delete-group-form.php');
			sfa_forums_delete_group_form(sf_esc_int($_GET['id']));
			break;
		case 'forumperm':
			include_once (SF_PLUGIN_DIR.'/admin/panel-forums/forms/sfa-forums-forum-permissions-form.php');
			sfa_forums_view_forums_permission_form(sf_esc_int($_GET['id']));
			break;
		case 'editforum':
			include_once (SF_PLUGIN_DIR.'/admin/panel-forums/forms/sfa-forums-edit-forum-form.php');
			sfa_forums_edit_forum_form(sf_esc_int($_GET['id']));
			break;
		case 'deleteforum':
			include_once (SF_PLUGIN_DIR.'/admin/panel-forums/forms/sfa-forums-delete-forum-form.php');
			sfa_forums_delete_forum_form(sf_esc_int($_GET['id']));
			break;
		case 'addperm':
			include_once (SF_PLUGIN_DIR.'/admin/panel-forums/forms/sfa-forums-add-permission-form.php');
			sfa_forums_add_permission_form(sf_esc_int($_GET['id']));
			break;
		case 'editperm':
			include_once (SF_PLUGIN_DIR.'/admin/panel-forums/forms/sfa-forums-edit-permission-form.php');
			sfa_forums_edit_permission_form(sf_esc_int($_GET['id']));
			break;
		case 'delperm':
			include_once (SF_PLUGIN_DIR.'/admin/panel-forums/forms/sfa-forums-delete-permission-form.php');
			sfa_forums_delete_permission_form(sf_esc_int($_GET['id']));
			break;
	}
	return;
}

?>