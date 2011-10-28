<?php
/*
Simple:Press
Admin Components SEO Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_components_seo_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfseoform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloadse').click();
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	$sfcomps = sfa_get_seo_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=components-loader&amp;saveform=seo";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfseoform" name="sfseo">
	<?php echo(sfc_create_nonce('forum-adminform_seo')); ?>
<?php

	sfa_paint_options_init();

#== EXTENSIONS Tab ============================================================

	sfa_paint_open_tab(__("Components", "sforum")." - ".__("SEO", "sforum"));

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Page/Browser Title (SEO)", "sforum"), true, 'seo-plugin-integration');
				sfa_paint_checkbox(__("Include Topic Name in Page/Browser Title", "sforum"), "sfseo_topic", $sfcomps['sfseo_topic']);
				sfa_paint_checkbox(__("Include Forum Name in Page/Browser Title", "sforum"), "sfseo_forum", $sfcomps['sfseo_forum']);
				sfa_paint_checkbox(__("Include Non-Forum Page Names in Page/Browser Title", "sforum"), "sfseo_page", $sfcomps['sfseo_page']);
				sfa_paint_input(__("Title Separator", "sforum"), "sfseo_sep", $sfcomps['sfseo_sep']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Meta Tags Data", "sforum"), true, 'meta-tags');
				$submessage=__("Text you enter here will entered as a custom Meta Desciption tag if enabled in Option above.", "sforum");
				sfa_paint_wide_textarea(__("Custom Meta Description", "sforum"), "sfdescr", $sfcomps['sfdescr'], $submessage);
				$submessage=__("Enter keywords separated by commas.", "sforum");
				sfa_paint_wide_textarea(__("Custom Meta Keywords", "sforum"), "sfkeywords", $sfcomps['sfkeywords'], $submessage);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_tab_right_cell();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Site Map Generation", "sforum"), true, 'seo-sitemap');
    			echo '<tr><td colspan="2">';
    			echo '<strong>'.__("Sitemap generation is only valid if you have the Google XML Sitemap (by Arne Brachhold) Wordpress plugin installed", "sforum").'</strong>';
    			echo '</td></tr>';
				$values = array(__("Don't Auto Generate Sitemap", "sforum"), __("Generate Sitemap on Every New Topic", "sforum"), __("Generate Sitemap Once Per Day", "sforum"));
				sfa_paint_radiogroup(__("Select Sitemap Rebuild Option", "sforum"), 'sfbuildsitemap', $values, $sfcomps['sfbuildsitemap'], false, true);
				if ($sfcomps['sched'])
				{
					$msg = __("Sitemap build cron job is scheduled to run daily.", "sforum");
					echo '<tr><td class="message" colspan="2" style="line-height:2em;">&nbsp;<u>'.$msg.'</u></td></tr>';
				}
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Meta Tags Setup", "sforum"), true, 'meta-tags');
				$values = array(__("Do Not Add Meta Description to Any Forum Pages", "sforum"), __("Use Custom Meta Description on All Forum Pages", "sforum"), __("Use Custom Meta Description on Main Forum Page Only and Use Forum Description on Forum and Topic Pages", "sforum"), __("Use Custom Description on Main Forum Page Only, Use Forum Description on Forum Pages and Use Topic Title on Topic Pages", "sforum"));
				sfa_paint_radiogroup(__("Select Meta Description Option", "sforum"), 'sfdescruse', $values, $sfcomps['sfdescruse'], false, true);
				sfa_paint_checkbox(__("Use Custom Keywords (entered in left panel) on Forum Pages", "sforum"), "sfusekeywords", $sfcomps['sfusekeywords']);
				sfa_paint_checkbox(__("Override custom meta keywords with topic tags (if using) on Topic Pages", "sforum"), "sftagwords", $sfcomps['sftagwords']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update SEO Component', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}

?>