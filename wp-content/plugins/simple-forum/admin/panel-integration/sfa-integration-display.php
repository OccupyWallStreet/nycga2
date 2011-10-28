<?php
/*
Simple:Press
Admin integration Display Rendering
$LastChangedDate: 2010-04-18 09:22:20 -0700 (Sun, 18 Apr 2010) $
$Rev: 3920 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_render_integration_panel($formid)
{
?>
	<div class="clearboth"></div>

	<div class="wrap sfatag">
		<?php
			sfa_render_sidemenu();
		?>
		<div id='sfmsgspot'></div>
		<div id="sfmaincontainer">
			<?php sfa_render_integration_container($formid); ?>
		</div>
			<div class="clearboth"></div>
	</div>
<?php
	return;
}

function sfa_render_integration_container($formid)
{

	switch($formid)
	{
		case 'page':
			include_once (SF_PLUGIN_DIR.'/admin/panel-integration/forms/sfa-integration-page-form.php');
			sfa_integration_page_form();
			break;
	}
	return;
}

?>