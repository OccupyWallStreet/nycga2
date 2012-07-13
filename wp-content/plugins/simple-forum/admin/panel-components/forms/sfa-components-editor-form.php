<?php
/*
Simple:Press
Admin Components Editor Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_components_editor_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfeditorform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	$sfcomps = sfa_get_editor_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=components-loader&amp;saveform=editor";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfeditorform" name="sfeditor">
	<?php echo(sfc_create_nonce('forum-adminform_editor')); ?>
<?php

	sfa_paint_options_init();

#== EDITOR Tab ============================================================

	sfa_paint_open_tab(__("Components", "sforum")." - ".__("Editors", "sforum"));

		sfa_paint_open_panel();

			sfa_paint_open_fieldset(__("Post Editing", "sforum"), true, 'post-editing');
				$values = array(__("Rich Text (TinyMCE)", "sforum"), __("HTML (Quicktags)", "sforum"), __("BBCode (Quicktags)", "sforum"), __("Plain Textarea", "sforum"));
				sfa_paint_radiogroup(__("Select Default Editor", "sforum"), 'editor', $values, $sfcomps['sfeditor'], false, true);
				sfa_paint_checkbox(__("Members can Select Editor", "sforum"), "sfusereditor", $sfcomps['sfusereditor']);
				sfa_paint_checkbox(__("Reject Posts with Embedded Formatting and Force Correct use of Paste Options (Rich Text Editor Only)", "sforum"), "sfrejectformat", $sfcomps['sfrejectformat']);
			sfa_paint_close_fieldset();

			sfa_paint_open_fieldset(__("Editor Language (TinyMCE Only)", "sforum"), true, 'editor-language');
				sfa_paint_select_start(__("Select 2 letter Language Code", "sforum"), "sflang", "sflang");
				echo(sfa_create_language_select($sfcomps['sflang']));
				sfa_paint_select_end();
				sfa_paint_checkbox(__("Use Editor Right-to-Left", "sforum"), "sfrtl", $sfcomps['sfrtl']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_tab_right_cell();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("CSS Styles (All Editors)", "sforum"), true, 'editor-styles');
				sfa_paint_input(__("TinyMCE Content CSS", "sforum"), "sftmcontentCSS", $sfcomps['sftmcontentCSS']);
				sfa_paint_input(__("TinyMCE UI CSS", "sforum"), "sftmuiCSS", $sfcomps['sftmuiCSS']);
				sfa_paint_input(__("TinyMCE Dialog CSS", "sforum"), "sftmdialogCSS", $sfcomps['sftmdialogCSS']);
				sfa_paint_input(__("Quicktags HTML CSS", "sforum"), "SFhtmlCSS", $sfcomps['SFhtmlCSS']);
				sfa_paint_input(__("Quicktags bbCode CSS", "sforum"), "SFbbCSS", $sfcomps['SFbbCSS']);
			sfa_paint_close_fieldset();

			sfa_paint_open_fieldset(__("Use Relative URL's (TinyMCE Only)", "sforum"), true, 'editor-relative');
				sfa_paint_checkbox(__("Save Internal URL's as 'Relative'", "sforum"), "sfrelative", $sfcomps['sfrelative']);
			sfa_paint_close_fieldset();

		sfa_paint_close_panel();

	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Editor Component', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}

function sfa_create_language_select($lang="en")
{
	$path = SF_PLUGIN_DIR . '/editors/tinymce/langs';

	$out='';
	$default='';
	$dlist = opendir($path);

	while (false !== ($file = readdir($dlist)))
	{
		if ($file != "." && $file != "..")
		{
			$langcode=explode(".", $file);
			$langcode=$langcode[0];
			if($langcode == $lang)
			{
				$default = 'selected="selected" ';
			} else {
				$default - null;
			}
			$out.='<option '.$default.'value="'.$langcode.'">'.$langcode.'</option>'."\n";
			$default='';
		}
	}
	closedir($dlist);
	return $out;
}

?>