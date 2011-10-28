<?php
/*
Simple:Press
Admin Components Extensions Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_components_pm_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfpmform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloadpm').click();
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	$sfcomps = sfa_get_pm_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=components-loader&amp;saveform=pm";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfpmform" name="sfpm">
	<?php echo(sfc_create_nonce('forum-adminform_pm')); ?>
<?php

	sfa_paint_options_init();

#== PM Tab ============================================================

	sfa_paint_open_tab(__("Components", "sforum")." - ".__("Private Messaging", "sforum"));

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Private Messaging Options", "sforum"), true, 'private-messaging');
				sfa_paint_checkbox(__("Enable the Private Messaging System", "sforum"), "sfprivatemessaging", $sfcomps['sfprivatemessaging']);
				sfa_paint_checkbox(__("Enable Sending of Email for PMs (If Enabled, Users can Elect to Receive or not in their Profile)", "sforum"), "sfpmemail", $sfcomps['sfpmemail']);
				sfa_paint_input(__("Maximum Inbox Size (0=No Limit)", "sforum"), "sfpmmax", $sfcomps['sfpmmax']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Private Messages Auto Removal", "sforum"), true, 'pm-removal');
				sfa_paint_checkbox(__("Enable Auto Removal of User's Private Messages", "sforum"), "sfpmremove", $sfcomps['sfpmremove']);
				sfa_paint_input(__("Maximum Number of DAYS to Keep Private Messages (if auto removal enabled)", "sforum"), "sfpmkeep", $sfcomps['sfpmkeep']);
				if ($sfcomps['sched'])
				{
					$msg = __("Private Messages auto removal cron job is scheduled to run daily.", "sforum");
					echo '<tr><td class="message" colspan="2" style="line-height:2em;">&nbsp;<u>'.$msg.'</u></td></tr>';
				}
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_tab_right_cell();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Private Messaging Addressing Options", "sforum"), true, 'pm-addressing');
				sfa_paint_input(__("Maximum Number of Recipients (0=No Limit)", "sforum"), "sfpmmaxrecipients", $sfcomps['sfpmmaxrecipients']);
				sfa_paint_checkbox(__("Allow Use of the Cc Field", "sforum"), "sfpmcc", $sfcomps['sfpmcc']);
				sfa_paint_checkbox(__("Allow Use of the Bcc Field", "sforum"), "sfpmbcc", $sfcomps['sfpmbcc']);
				sfa_paint_checkbox(__("Only Allow sending of PMs from Send PM link on Posts.  You will not be able to address PMs from the PM Compose Page.", "sforum"), "sfpmlimitedsend", $sfcomps['sfpmlimitedsend']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Private Messaging Component', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}

?>