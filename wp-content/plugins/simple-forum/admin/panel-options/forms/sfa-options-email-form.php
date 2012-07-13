<?php
/*
Simple:Press
Admin Options Email Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_options_email_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfemailform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	$sfoptions = sfa_get_email_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=options-loader&amp;saveform=email";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfemailform" name="sfemail">
	<?php echo(sfc_create_nonce('forum-adminform_email')); ?>
<?php

	sfa_paint_options_init();

#== EMAIL Tab ============================================================

	sfa_paint_open_tab(__("Options", "sforum")." - ".__("EMail Settings", "sforum"));

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("New User Email", "sforum"), true, 'new-user-email');

				sfa_paint_checkbox(__("Use the SPF New User Email Version", "sforum"), "sfusespfreg", $sfoptions['sfusespfreg']);

	echo "<tr>\n";
	echo "<td class='sflabel' colspan='2'>\n";
	echo "<small><br /><strong>".__("The following placeholders are available: %USERNAME%, %PASSWORD%, %BLOGNAME%, %SITEURL%, %LOGINURL%", "sforum")."</strong><br /><br /></small>\n";
	echo "</td>\n";
	echo "</tr>\n";

		sfa_paint_input(__("Email Subject Line", "sforum"), "sfnewusersubject", $sfoptions['sfnewusersubject'], false, true);
		sfa_paint_wide_textarea(__("Email Message (no html)", "sforum"), 'sfnewusertext', $sfoptions['sfnewusertext'], $submessage='', 9);

		sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_tab_right_cell();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("EMail Address Settings", "sforum"), true, 'email-address-settings');
				sfa_paint_checkbox(__("Use the following Email Settings", "sforum"), "sfmailuse", $sfoptions['sfmailuse']);
				sfa_paint_input(__("The Senders Name", "sforum"), "sfmailsender", $sfoptions['sfmailsender'], false, false);
				sfa_paint_input(__("The EMail From Name", "sforum"), "sfmailfrom", $sfoptions['sfmailfrom'], false, false);
				sfa_paint_input(__("The EMail Domain Name", "sforum"), "sfmaildomain", $sfoptions['sfmaildomain'], false, false);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Email Options', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}

?>