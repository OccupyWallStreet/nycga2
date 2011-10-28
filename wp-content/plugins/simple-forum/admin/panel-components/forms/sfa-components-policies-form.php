<?php
/*
Simple:Press
Admin Components Policy Documents Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_components_policies_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfpoliciesform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	$sfcomps = sfa_get_policies_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=components-loader&amp;saveform=policies";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfpoliciesform" name="sfpolicies">
	<?php echo(sfc_create_nonce('forum-adminform_policies')); ?>
<?php

	sfa_paint_options_init();

#== LOGIN Tab ============================================================

	sfa_paint_open_tab(__("Components", "sforum")." - ".__("Policy Documents", "sforum"));
		sfa_paint_open_panel();

			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Registration Policy", "sforum"), true, 'registration-policy');
					sfa_paint_checkbox(__("Display Registration Policy", "sforum"), "sfregtext", $sfcomps['sfregtext']);
					sfa_paint_checkbox(__("Force Policy Acceptance (checkbox)", "sforum"), "sfregcheck", $sfcomps['sfregcheck']);
					sfa_paint_checkbox(__("Display Link in Version Strip", "sforum"), "sfreglink", $sfcomps['sfreglink']);
				sfa_paint_close_fieldset();
			sfa_paint_close_panel();

			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Registration Policy Statement", "sforum"), true, 'registration-policy');

					sfa_paint_input(__("Optional Policy Text File Name", "sforum"), "sfregfile", $sfcomps['sfregfile']);
					$submessage=__("If not using a text file you can use the area below for your statement:", "sforum");
					$submessage.='<br />'.__("Enter the text of the site Registration Policy for display (and optional acceptance) prior to the user registration form being displayed.", "sforum");
					sfa_paint_wide_textarea(__("Policy Statement", "sforum"), 'sfregpolicy', $sfcomps['sfregpolicy'], $submessage, $xrows=10);
				sfa_paint_close_fieldset();
			sfa_paint_close_panel();

			sfa_paint_tab_right_cell();

			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Privacy Policy", "sforum"), true, 'privacy-policy');
					sfa_paint_checkbox(__("Display Link in Version Strip", "sforum"), "sfprivlink", $sfcomps['sfprivlink']);
				sfa_paint_close_fieldset();
			sfa_paint_close_panel();

			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Privacy Policy Statement", "sforum"), true, 'privacy-policy');

					sfa_paint_input(__("Optional Policy Text File Name", "sforum"), "sfprivfile", $sfcomps['sfprivfile']);
					$submessage=__("If not using a text file you can use the area below for your statement:", "sforum");
					$submessage.='<br />'.__("Enter the text of the site Privacy Policy for display.", "sforum");
					sfa_paint_wide_textarea(__("Policy Statement", "sforum"), 'sfprivpolicy', $sfcomps['sfprivpolicy'], $submessage, $xrows=10);
				sfa_paint_close_fieldset();
			sfa_paint_close_panel();

			$sfconfig = sf_get_option('sfconfig');
			echo '<p>&nbsp;&nbsp;&nbsp;&nbsp;'.__("Policy Text Files must be located at", "sforum").':<br />&nbsp;&nbsp;&nbsp;&nbsp;'.WP_CONTENT_DIR.'/'.$sfconfig['policies'].'</p>';



		sfa_paint_close_panel();
	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Policy Documents Component', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}

function sfa_paint_site_policy($textname, $textvalue, $submessage)
{
	global $tab;

	echo "<tr>\n";
	echo "<td class='sflabel'>\n";
	echo "<small><strong>".$submessage."</strong></small>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td class='sflabel'>\n";
	echo __("Policy Statement", "sforum").":\n";
	echo "<div class='sfformcontainer'>\n";
	echo "<textarea rows='11' cols='80' class='sftextarea' tabindex='$tab' name='$textname'>".esc_html($textvalue)."</textarea>\n";
	echo "</div>\n";
	echo "</td>\n";
	echo "</tr>\n";
	$tab++;
	return;
}

?>