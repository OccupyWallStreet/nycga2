<?php
/*
Simple:Press
Admin Components Uploads Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_components_uploads_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfuploadsform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php
	# Go ahead and display the form
	$sfcomps = sfa_get_uploads_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=components-loader&amp;saveform=uploads";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfuploadsform" name="sfuploads">
	<?php echo(sfc_create_nonce('forum-adminform_uploads')); ?>
<?php

	sfa_paint_options_init();

	sfa_paint_open_tab(__("Components", "sforum")." - ".__("Uploads", "sforum"));

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Upload Settings", "sforum"), true, 'upload-settings');
				sfa_paint_checkbox(__("Allocate Each Member a Private Folder", "sforum"), "privatefolder", $sfcomps['privatefolder']);
				sfa_paint_input(__("Maximum Size of Thumbnails", "sforum"), "thumbsize", $sfcomps['thumbsize']);
				sfa_paint_input(__("How Many Thumbnails to a Page", "sforum"), "pagecount", $sfcomps['pagecount']);
				sfa_paint_checkbox(__("Open in List View (default-thumbnails)", "sforum"), "showmode", $sfcomps['showmode']);
				$values = array(__("Browse Tab", "sforum"), __("Upload Tab", "sforum"), __("Edit Tab", "sforum"), __("Folders Tab", "sforum"));
				sfa_paint_radiogroup(__("Default Uploader Start Tab", "sforum"), 'sfdeftab', $values, $sfcomps['deftab'], false, true);
			sfa_paint_close_fieldset();

		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Prohibited Files", "sforum"), true, 'prohibited-files');
				sfa_paint_wide_textarea(__("Prohibited File Types", "sforum"), "prohibited", $sfcomps['prohibited'], __("Separate each file type with a comma", "sforum"), 3);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	echo '<br />&nbsp;&nbsp;&nbsp;&nbsp;'.__("Note: Maximum upload limits are not applied to forum administrators", "sforum");
	sfa_paint_tab_right_cell();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Image Files", "sforum"), true, 'image-files');
				sfa_paint_input(__("Maximum Size (bytes) of Images File (0=Any Size)", "sforum"), "imagemaxsize", $sfcomps['imagemaxsize']);
				sfa_paint_wide_textarea(__("Allowed File Types", "sforum"), "imagetypes", $sfcomps['imagetypes'], __("Separate each file type with a comma", "sforum"), 1);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Media Files", "sforum"), true, 'media-files');
				sfa_paint_input(__("Maximum Size (bytes) of Media File (0=Any Size)", "sforum"), "mediamaxsize", $sfcomps['mediamaxsize']);
				sfa_paint_wide_textarea(__("Allowed File Types", "sforum"), "mediatypes", $sfcomps['mediatypes'], __("Separate each file type with a comma", "sforum"), 1);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();


		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Other Files", "sforum"), true, 'other-files');
				sfa_paint_input(__("Maximum Size (bytes) of Other File (0=Any Size)", "sforum"), "filemaxsize", $sfcomps['filemaxsize']);
				sfa_paint_wide_textarea(__("Allowed File Types", "sforum"), "filetypes", $sfcomps['filetypes'], __("Separate each file type with a comma", "sforum"), 1);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_close_panel();

	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Uploads Component', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}
?>