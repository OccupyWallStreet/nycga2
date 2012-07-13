<?php
/*
Simple:Press
Admin Components Login Form
$LastChangedDate: 2010-07-24 21:49:52 -0700 (Sat, 24 Jul 2010) $
$Rev: 4322 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_components_login_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfloginform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	$sfcomps = sfa_get_login_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=components-loader&amp;saveform=login";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfloginform" name="sflogin">
	<?php echo(sfc_create_nonce('forum-adminform_login')); ?>
<?php

	sfa_paint_options_init();

#== LOGIN Tab ============================================================

	sfa_paint_open_tab(__("Components", "sforum")." - ".__("Login and Registration", "sforum"));
		sfa_paint_open_panel();
			if (false == get_option('users_can_register'))
			{
				sfa_paint_open_panel();
					sfa_paint_open_fieldset(__("Member Registrations", "sforum"), true, 'no-login', false);
						echo '<div class="sfoptionerror">';
						echo __("Your site is currently not set to allow users to register. Click on the Help icon for details of how to turn this on", "sforum");
						echo '</div>';
					sfa_paint_close_fieldset(false);
				sfa_paint_close_panel();
			}

			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Member Login", "sforum"), true, 'user-login');
					sfa_paint_checkbox(__("Show Login/Logout Link", "sforum"), "sfshowlogin", $sfcomps['sfshowlogin']);
					sfa_paint_checkbox(__("Use In-Line Login Form", "sforum"), "sfinlogin", $sfcomps['sfinlogin']);
					sfa_paint_checkbox(__("Skin Login Forms", "sforum"), "sfloginskin", $sfcomps['sfloginskin']);
				sfa_paint_close_fieldset();
			sfa_paint_close_panel();

			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("User Registration", "sforum"), true, 'user-registration');
					sfa_paint_checkbox(__("Show Register Link", "sforum"), "sfshowreg", $sfcomps['sfshowreg']);
					sfa_paint_checkbox(__("Use Spam Tool on Registration Form", "sforum"), "sfregmath", $sfcomps['sfregmath']);
				sfa_paint_close_fieldset();
			sfa_paint_close_panel();

			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Login Strip Display", "sforum"), true, 'login-display');
					sfa_paint_checkbox(__("Show Current Members Avatar", "sforum"), "sfshowavatar", $sfcomps['sfshowavatar']);
				sfa_paint_close_fieldset();
			sfa_paint_close_panel();

			sfa_paint_open_panel();
				$submessage='';
				sfa_paint_open_fieldset(__("Login/Registration URLs", "sforum"), true, 'login-registration-urls');
					sfa_paint_wide_textarea(__("Login URL", "sforum"), 'sfloginurl', $sfcomps['sfloginurl'], $submessage, $xrows=1);
					sfa_paint_wide_textarea(__("Login Email URL", "sforum"), 'sfloginemailurl', $sfcomps['sfloginemailurl'], $submessage, $xrows=1);
					sfa_paint_wide_textarea(__("Logout URL", "sforum"), 'sflogouturl', $sfcomps['sflogouturl'], $submessage, $xrows=1);
					sfa_paint_wide_textarea(__("Registration URL", "sforum"), 'sfregisterurl', $sfcomps['sfregisterurl'], $submessage, $xrows=1);
					sfa_paint_wide_textarea(__("Lost Password URL", "sforum"), 'sflostpassurl', $sfcomps['sflostpassurl'], $submessage, $xrows=1);
				sfa_paint_close_fieldset();
			sfa_paint_close_panel();

			sfa_paint_tab_right_cell();

			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("RPX 3rd Party Login", "sforum"), true, 'rpx-login');
					sfa_paint_checkbox(__("Enable RPX Support", "sforum"), "sfrpxenable", $sfcomps['sfrpxenable']);
        			echo '<tr><td colspan="2">';
        			echo __("Please enter your RPX API key. If you haven't yet created one, please create one at", "sforum")." <a href='https://rpxnow.com' target='_blank'>RPX</a>";
        			echo '</td></tr>';
			        sfa_paint_input(__("RPX API key", "sforum"), "sfrpxkey", $sfcomps['sfrpxkey'], false, true);
    				$submessage=__('Force a redirect to a specific page on RPX login.  Leave blank to have SPF/RPX determine redirect location.', 'sforum');
					sfa_paint_wide_textarea(__("URL to Redirect to after RPX Login", "sforum"), 'sfrpxredirect', $sfcomps['sfrpxredirect'], $submessage, $xrows=1);
				sfa_paint_close_fieldset();
			sfa_paint_close_panel();

			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Sneak Peek Statement", "sforum"), true, 'sneak-peek');
					$submessage=__("If you are allowing Guests to view Forum and Topic lists, but not see the actual Posts, this message is displayed to encourage them to sign up.", "sforum");
					sfa_paint_wide_textarea(__("Sneak Peek Statement", "sforum"), 'sfsneakpeek', $sfcomps['sfsneakpeek'], $submessage, $xrows=6);
				sfa_paint_close_fieldset();
			sfa_paint_close_panel();

			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Admin View Statement", "sforum"), true, 'admin-view');
					$submessage=__("If you are inhibiting User Groups from seeing Admin posts, this message is displayed to encourage them to sign up.", "sforum");
					sfa_paint_wide_textarea(__("Admin View Statement", "sforum"), 'sfadminview', $sfcomps['sfadminview'], $submessage, $xrows=6);
				sfa_paint_close_fieldset();
			sfa_paint_close_panel();
		sfa_paint_close_panel();
	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Login and Registration Component', 'sforum')); ?>" />
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