<?php
/*
Simple:Press
Admin Profile Options Form
$LastChangedDate: 2010-08-02 05:43:04 -0700 (Mon, 02 Aug 2010) $
$Rev: 4344 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_profiles_options_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfoptionsform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	$sfoptions = sfa_get_options_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=profiles-loader&amp;saveform=options";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfoptionsform" name="sfoptions">
	<?php echo(sfc_create_nonce('forum-adminform_options')); ?>
<?php

	sfa_paint_options_init();

#== PROFILE OPTIONS Tab ============================================================

	sfa_paint_open_tab(__("Profiles", "sforum")." - ".__("Profile Options", "sforum"));

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Display Name Format", "sforum"), true, 'display-name-format');
				$values = array(__("Let Member Choose Display Name", "sforum"), __("Use Member's Logon Name", "sforum"), __("Use First Name - Last Name", "sforum"));
				sfa_paint_radiogroup(__("Display Name Format", "sforum"), 'nameformat', $values, $sfoptions['nameformat'], false, true);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Profile Link", "sforum"), true, 'profile-link');
				$values = array(__("Member's Display Name", "sforum"), __("Member's Avatar", "sforum"), __("Profile Icon", "sforum"));
				sfa_paint_radiogroup(__("Set Profile Link To", "sforum"), 'profilelink', $values, $sfoptions['profilelink'], false, true);
				sfa_paint_checkbox(__("Always Use Profile Link in Statistics Lists", "sforum"), "profileinstats", $sfoptions['profileinstats']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Website Link", "sforum"), true, 'website-link');
				$values = array(__("Member's Display Name", "sforum"), __("Member's Avatar", "sforum"), __("Website Icon", "sforum"));
				sfa_paint_radiogroup(__("Set Website Link To", "sforum"), 'weblink', $values, $sfoptions['weblink'], false, true);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Personal Photos", "sforum"), true, 'personal-photos');
				sfa_paint_input(__("Maximum Number of Photos Allowed", "sforum"), "photosmax", $sfoptions['photosmax'], false, false);
				sfa_paint_input(__("Maximum Pixel Width of Photo Display", "sforum"), "photoswidth", $sfoptions['photoswidth'], false, false);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Signature Image Size", "sforum"), true, 'sig-images');
				echo('<tr><td colspan="2">&nbsp;<u>'.__("If you are allowing Signature Images (zero = not limited)", "sforum").':</u></td></tr>');
				sfa_paint_input(__("Maximum Signature Width (pixels)", "sforum"), "sfsigwidth", $sfoptions['sfsigwidth']);
				sfa_paint_input(__("Maximum Signature Height (pixels)", "sforum"), "sfsigheight", $sfoptions['sfsigheight']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("First Forum Visit", "sforum"), true, 'first-forum-visit');
				sfa_paint_checkbox(__("Display Profile Form on Login", "sforum"), "firstvisit", $sfoptions['firstvisit']);
            	$show_password_fields = apply_filters('show_password_fields', true);
        		if ($show_password_fields)
                {
				    sfa_paint_checkbox(__("Force Password Change", "sforum"), "forcepw", $sfoptions['forcepw']);
                }
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_tab_right_cell();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Display Profile Mode", "sforum"), true, 'display-profile-mode');
				$values = array(__("Popup Window", "sforum"), __("Forum Profile Page", "sforum"), __("Buddy Press Profile", "sforum"), __("WordPress Author Page", "sforum"), __("Other Page", "sforum"));
				sfa_paint_radiogroup(__("Display Profile Information In", "sforum"), 'displaymode', $values, $sfoptions['displaymode'], false, true);
				sfa_paint_input(__("URL for 'Other' Page", "sforum"), "displaypage", sf_filter_url_display($sfoptions['displaypage']), false, true);
				sfa_paint_input(__("Query String Variable Name", "sforum"), "displayquery", sf_filter_title_display($sfoptions['displayquery']), false, true);
				sfa_paint_checkbox(__("Wrap Profile Display in Forum Header and Footer", "sforum"), "displayinforum", $sfoptions['displayinforum']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Profile Entry Form Mode", "sforum"), true, 'profile-entry-form-mode');
				$values = array(__("Forum Profile Form", "sforum"), __("WordPress Profile Form", "sforum"), __("Buddy Press Profile", "sforum"), __("Other Form", "sforum"));
				sfa_paint_radiogroup(__("Enter Profile Information In", "sforum"), 'formmode', $values, $sfoptions['formmode'], false, true);
				sfa_paint_input(__("URL for 'Other' Page", "sforum"), "formpage", sf_filter_url_display($sfoptions['formpage']), false, true);
				sfa_paint_input(__("Query String Variable Name", "sforum"), "formquery", sf_filter_title_display($sfoptions['formquery']), false, true);
				sfa_paint_checkbox(__("Wrap Profile Form in Forum Header and Footer", "sforum"), "forminforum", $sfoptions['forminforum']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Custom Profile Message", "sforum"), true, 'profile-message');
				$submessage=__("Text you enter here will be displayed to the User on their profile edit page.", "sforum");
				sfa_paint_wide_textarea(__("Custom Profile Message", "sforum"), "sfprofiletext", sf_filter_text_edit($sfoptions['sfprofiletext']), $submessage);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Profile Options', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}

?>