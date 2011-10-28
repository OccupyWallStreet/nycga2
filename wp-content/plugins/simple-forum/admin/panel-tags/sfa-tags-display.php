<?php
/*
Simple:Press
Admin Tags Panel Rendering
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_render_tags_panel($formid)
{
?>
	<div class="clearboth"></div>

	<div class="wrap sfatag">
		<?php
			sfa_render_sidemenu();
		?>
		<div id='sfmsgspot'></div>
		<div id="sfmaincontainer">
			<?php sfa_render_tags_container($formid); ?>
		</div>
			<div class="clearboth"></div>
	</div>
<?php
	return;
}

function sfa_render_tags_container($formid)
{
	switch ($formid)
	{
		case 'managetags':
			include_once (SF_PLUGIN_DIR.'/admin/panel-tags/forms/sfa-tags-manage-tags-form.php');
			sfa_tags_manage_tags_form();
			break;
		case 'edittags':
			include_once (SF_PLUGIN_DIR.'/admin/panel-tags/forms/sfa-tags-edit-tags-form.php');
			sfa_tags_edit_tags_form();
			break;
	}
	return;
}

?>