<?php
/*
Simple:Press
Admin Toolbox Panel Rendering
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_render_toolbox_panel($formid)
{
?>
	<div class="clearboth"></div>

	<div class="wrap sfatag">
		<?php
			sfa_render_sidemenu();
		?>
		<div id='sfmsgspot'></div>
		<div id="sfmaincontainer">
			<?php sfa_render_toolbox_container($formid); ?>
		</div>
			<div class="clearboth"></div>
	</div>
<?php
	return;
}

function sfa_render_toolbox_container($formid)
{
	switch($formid)
	{
		case 'toolbox':
			include_once (SF_PLUGIN_DIR.'/admin/panel-toolbox/forms/sfa-toolbox-toolbox-form.php');
			sfa_toolbox_toolbox_form();
			break;

		case 'log':
			include_once (SF_PLUGIN_DIR.'/admin/panel-toolbox/forms/sfa-toolbox-log-form.php');
			sfa_toolbox_log_form();
			break;

		case 'environment':
			include_once (SF_PLUGIN_DIR.'/admin/panel-toolbox/forms/sfa-toolbox-environment-form.php');
			sfa_toolbox_environment_form();
			break;

		case 'uninstall':
			include_once (SF_PLUGIN_DIR.'/admin/panel-toolbox/forms/sfa-toolbox-uninstall-form.php');
			sfa_toolbox_uninstall_form();
			break;
		case 'database':
			include_once (SF_PLUGIN_DIR.'/admin/panel-toolbox/forms/sfa-toolbox-filter-form.php');
			sfa_toolbox_filter_form();
			break;
		case 'prune':
			$topicdata = sfa_prepare_filter_topics();
			include_once (SF_PLUGIN_DIR.'/admin/panel-toolbox/forms/sfa-toolbox-prune-form.php');
			sfa_toolbox_prune_form($topicdata);
			break;
	}
	return;
}

?>