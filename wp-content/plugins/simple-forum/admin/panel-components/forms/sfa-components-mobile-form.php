<?php
/*
Simple:Press
Admin Components Mobile Form
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_components_mobile_form()
{
	global $SFPATHS;
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfmobileform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	$sfcomps = sfa_get_mobile_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=components-loader&amp;saveform=mobile";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfmobileform" name="sfmobile">
	<?php echo(sfc_create_nonce('forum-adminform_mobile')); ?>
<?php

	sfa_paint_options_init();

#== CUSTOM ICONS Tab ============================================================

	sfa_paint_open_tab(__("Mobile Browser Support", "sforum")." - ".__("Mobile Support", "sforum"));
		sfa_paint_open_panel();
			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Mobile Browsers Agents", "sforum"), true, 'mobile-browser');
					$submessage=__("For the List of recognized mobile browser agents below, simple-press will force usage of plain text area since tinymce is not supported.", "sforum");
					$submessage.='<br />'.__("Enter the list of mobile browser agents in comma separated list.", "sforum");
					sfa_paint_wide_textarea(__("Mobile Browser Agents", "sforum"), 'sfbrowsers', $sfcomps['browsers'], $submessage, $xrows=10);
				sfa_paint_close_fieldset();
			sfa_paint_close_panel();

			sfa_paint_tab_right_cell();

			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Touch Browser Agents", "sforum"), true, 'mobile-touch');
					$submessage=__("For the List of recognized mobile touch browser agents below, simple-press will force usage of plain text area since tinymce is not supported.", "sforum");
					$submessage.='<br />'.__("Enter the list of mobile touch browser agents in comma separated list.", "sforum");
					sfa_paint_wide_textarea(__("Mobile Touch Agents", "sforum"), 'sftouch', $sfcomps['touch'], $submessage, $xrows=10);
				sfa_paint_close_fieldset();
			sfa_paint_close_panel();
		sfa_paint_close_panel();
	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Mobile Component', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}

?>