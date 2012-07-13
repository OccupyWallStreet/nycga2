<?php
/*
Simple:Press
Admin Options Panel Rendering
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_render_options_panel($formid)
{
?>
	<div class="clearboth"></div>

	<div class="wrap sfatag">
		<?php
			sfa_render_sidemenu();
		?>
		<div id='sfmsgspot'></div>
		<div id="sfmaincontainer">
			<?php sfa_render_options_container($formid); ?>
		</div>
			<div class="clearboth"></div>
	</div>
<?php
	return;
}

function sfa_render_options_container($formid)
{
	switch($formid)
	{
		case 'global':
			include_once (SF_PLUGIN_DIR.'/admin/panel-options/forms/sfa-options-global-form.php');
			sfa_options_global_form();
			break;

		case 'display':
			include_once (SF_PLUGIN_DIR.'/admin/panel-options/forms/sfa-options-display-form.php');
			sfa_options_display_form();
			break;

		case 'forums':
			include_once (SF_PLUGIN_DIR.'/admin/panel-options/forms/sfa-options-forums-form.php');
			sfa_options_forums_form();
			break;

		case 'topics':
			include_once (SF_PLUGIN_DIR.'/admin/panel-options/forms/sfa-options-topics-form.php');
			sfa_options_topics_form();
			break;

		case 'posts':
			include_once (SF_PLUGIN_DIR.'/admin/panel-options/forms/sfa-options-posts-form.php');
			sfa_options_posts_form();
			break;

		case 'content':
			include_once (SF_PLUGIN_DIR.'/admin/panel-options/forms/sfa-options-content-form.php');
			sfa_options_content_form();
			break;

		case 'members':
			include_once (SF_PLUGIN_DIR.'/admin/panel-options/forms/sfa-options-members-form.php');
			sfa_options_members_form();
			break;

		case 'email':
			include_once (SF_PLUGIN_DIR.'/admin/panel-options/forms/sfa-options-email-form.php');
			sfa_options_email_form();
			break;

		case 'style':
			include_once (SF_PLUGIN_DIR.'/admin/panel-options/forms/sfa-options-style-form.php');
			sfa_options_style_form();
			break;
	}
	return;
}

?>