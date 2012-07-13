<?php
/*
Simple:Press
Admin User Groups Panel Rendering
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_render_usergroups_panel($formid)
{
?>
	<div class="clearboth"></div>

	<div class="wrap sfatag">
		<?php
			sfa_render_sidemenu();
		?>
		<div id='sfmsgspot'></div>
		<div id="sfmaincontainer">
			<?php sfa_render_usergroups_container($formid); ?>
		</div>
			<div class="clearboth"></div>
	</div>
<?php
	return;
}

function sfa_render_usergroups_container($formid)
{
	switch ($formid)
	{
		case 'usergroups':
			include_once (SF_PLUGIN_DIR.'/admin/panel-usergroups/sfa-usergroups-display-main.php');
			sfa_usergroups_usergroup_main();
			break;
		case 'createusergroup':
			include_once (SF_PLUGIN_DIR.'/admin/panel-usergroups/forms/sfa-usergroups-create-usergroup-form.php');
			sfa_usergroups_create_usergroup_form();
			break;
		case 'editusergroup':
			include_once (SF_PLUGIN_DIR.'/admin/panel-usergroups/forms/sfa-usergroups-edit-usergroup-form.php');
			sfa_usergroups_edit_usergroup_form(sf_esc_int($_GET['id']));
			break;
		case 'delusergroup':
			include_once (SF_PLUGIN_DIR.'/admin/panel-usergroups/forms/sfa-usergroups-delete-usergroup-form.php');
			sfa_usergroups_delete_usergroup_form(sf_esc_int($_GET['id']));
			break;
		case 'addmembers':
			include_once (SF_PLUGIN_DIR.'/admin/panel-usergroups/forms/sfa-usergroups-add-members-form.php');
			sfa_usergroups_add_members_form(sf_esc_int($_GET['id']));
			break;
		case 'delmembers':
			include_once (SF_PLUGIN_DIR.'/admin/panel-usergroups/forms/sfa-usergroups-delete-members-form.php');
			sfa_usergroups_delete_members_form(sf_esc_int($_GET['id']));
			break;
	}
	return;
}

?>