<?php
/*
Simple:Press
Admin Profiles Panel Rendering
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_render_profiles_panel($formid)
{
?>
	<div class="clearboth"></div>

	<div class="wrap sfatag">
		<?php
			sfa_render_sidemenu();
		?>
		<div id='sfmsgspot'></div>
		<div id="sfmaincontainer">
			<?php sfa_render_profiles_container($formid); ?>
		</div>
			<div class="clearboth"></div>
	</div>
<?php
	return;
}

function sfa_render_profiles_container($formid)
{
	switch($formid)
	{
		case 'options':
			include_once (SF_PLUGIN_DIR.'/admin/panel-profiles/forms/sfa-profiles-options-form.php');
			sfa_profiles_options_form();
			break;
		case 'data':
			include_once (SF_PLUGIN_DIR.'/admin/panel-profiles/forms/sfa-profiles-data-form.php');
			sfa_profiles_data_form();
			break;
		case 'fields':
			include_once (SF_PLUGIN_DIR.'/admin/panel-profiles/forms/sfa-profiles-fields-form.php');
			sfa_profiles_fields_form();
			break;
		case 'avatars':
			include_once (SF_PLUGIN_DIR.'/admin/panel-profiles/forms/sfa-profiles-avatars-form.php');
			sfa_profiles_avatars_form();
			break;
	}
	return;
}

?>