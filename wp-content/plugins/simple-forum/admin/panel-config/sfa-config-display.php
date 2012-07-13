<?php
/*
Simple:Press
Admin Config Panel Rendering
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_render_config_panel($formid)
{
?>
	<div class="clearboth"></div>

	<div class="wrap sfatag">
		<?php
			sfa_render_sidemenu();
		?>
		<div id='sfmsgspot'></div>
		<div id="sfmaincontainer">
			<?php sfa_render_config_container($formid); ?>
		</div>
			<div class="clearboth"></div>
	</div>
<?php
	return;
}

function sfa_render_config_container($formid)
{
	switch($formid)
	{
		case 'config':
			include_once (SF_PLUGIN_DIR.'/admin/panel-config/forms/sfa-config-config-form.php');
			sfa_config_config_form();
			break;
		case 'optimise':
			$options = sfa_prepare_config_optimisation();
			include_once (SF_PLUGIN_DIR.'/admin/panel-config/forms/sfa-config-optimise-form.php');
			sfa_config_optimise_form($options);
			break;
		case 'colour':
			include_once (SF_PLUGIN_DIR.'/admin/panel-config/forms/sfa-config-colour-form.php');
			sfa_config_colour_form();
			break;
		case 'post-upgrade':
            include (SF_PLUGIN_DIR.'/help/install/sf-postupgrade-help.php');
			break;
		case 'post-install':
            include (SF_PLUGIN_DIR.'/help/install/sf-postinstall-help.php');
			break;
	}
	return;
}

?>