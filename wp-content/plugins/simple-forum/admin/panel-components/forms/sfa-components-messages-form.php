<?php
/*
Simple:Press
Admin Components Custom Messages Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_components_messages_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfmessagesform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	$sfcomps = sfa_get_messages_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=components-loader&amp;saveform=messages";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfmessagesform" name="sfmessages">
	<?php echo(sfc_create_nonce('forum-adminform_messages')); ?>
<?php

	sfa_paint_options_init();

#== CUSTOM MESSAGES Tab ============================================================

	sfa_paint_open_tab(__("Components", "sforum")." - ".__("Custom Messages", "sforum"));

		sfa_paint_open_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Custom Message Above Editor", "sforum"), true, 'editor-message');
				$submessage=__("Text you enter here will be displayed above the editor (New Topic and/or New Post).", "sforum");
				sfa_paint_wide_textarea(__("Custom Message", "sforum"), "sfpostmsgtext", $sfcomps['sfpostmsgtext'], $submessage);
				sfa_paint_checkbox(__("Display for New Topic", "sforum"), "sfpostmsgtopic", $sfcomps['sfpostmsgtopic']);
				sfa_paint_checkbox(__("Display for New Post", "sforum"), "sfpostmsgpost", $sfcomps['sfpostmsgpost']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_tab_right_cell();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Custom Intro Text in Editor", "sforum"), true, 'editor-intro');
				$submessage=__("Text you enter here will be displayed inside the editor (New Topic only).", "sforum");
				sfa_paint_wide_textarea(__("Custom Intro Message", "sforum"), "sfeditormsg", $sfcomps['sfeditormsg'], $submessage);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_close_panel();

	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Custom Messages Component', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}

?>