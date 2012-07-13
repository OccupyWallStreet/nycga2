<?php
/*
Simple:Press
Admin Permissions Panel Rendering
$LastChangedDate: 2010-04-18 09:22:20 -0700 (Sun, 18 Apr 2010) $
$Rev: 3920 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_render_permissions_panel($formid)
{
?>
	<div class="clearboth"></div>

	<div class="wrap sfatag">
		<?php
			sfa_render_sidemenu();
		?>
		<div id='sfmsgspot'></div>
		<div id="sfmaincontainer">
			<?php sfa_render_permissions_container($formid); ?>
		</div>
			<div class="clearboth"></div>
	</div>
<?php
	return;
}

function sfa_render_permissions_container($formid)
{
	switch ($formid)
	{
		case 'permissions':
			include_once (SF_PLUGIN_DIR.'/admin/panel-permissions/sfa-permissions-display-main.php');
			sfa_permissions_permission_main();
			break;
		case 'createperm':
			include_once (SF_PLUGIN_DIR.'/admin/panel-permissions/forms/sfa-permissions-add-permission-form.php');
			sfa_permissions_add_permission_form();
			break;
		case 'editperm':
			include_once (SF_PLUGIN_DIR.'/admin/panel-permissions/forms/sfa-permissions-edit-permission-form.php');
			sfa_permissions_edit_permission_form(sf_esc_int($_GET['id']));
			break;
		case 'delperm':
			include_once (SF_PLUGIN_DIR.'/admin/panel-permissions/forms/sfa-permissions-delete-permission-form.php');
			sfa_permissions_delete_permission_form(sf_esc_int($_GET['id']));
			break;
	}
	return;
}

?>