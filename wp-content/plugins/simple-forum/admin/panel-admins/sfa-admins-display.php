<?php
/*
Simple:Press
Admin Admins Display Rendering
$LastChangedDate: 2010-04-22 15:20:30 -0700 (Thu, 22 Apr 2010) $
$Rev: 3939 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_render_admins_panel($formid)
{
?>
	<div class="clearboth"></div>

	<div class="wrap sfatag">
		<?php
			sfa_render_sidemenu();
		?>
		<div id='sfmsgspot'></div>
		<div id="sfmaincontainer">
			<?php sfa_render_admins_container($formid); ?>
		</div>
			<div class="clearboth"></div>
	</div>
<?php
	return;
}

function sfa_render_admins_container($formid)
{

	switch($formid)
	{
		case 'youradmin':
			include_once (SF_PLUGIN_DIR.'/admin/panel-admins/forms/sfa-admins-your-options-form.php');
			sfa_admins_your_options_form();
			break;

		case 'globaladmin':
			include_once (SF_PLUGIN_DIR.'/admin/panel-admins/forms/sfa-admins-global-options-form.php');
			sfa_admins_global_options_form();
			break;

		case 'manageadmin':
			require_once (ABSPATH.'wp-admin/includes/admin.php');
			include_once (SF_PLUGIN_DIR.'/admin/panel-admins/forms/sfa-admins-manage-admins-form.php');
			sfa_admins_manage_admins_form();
			break;
	}
	return;
}

?>