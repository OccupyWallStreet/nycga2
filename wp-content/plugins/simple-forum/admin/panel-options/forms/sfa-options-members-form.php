<?php
/*
Simple:Press
Admin Options Members Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_options_members_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfmembersform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloadms').click();
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	global $wp_roles;

	$sfoptions = sfa_get_members_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=options-loader&amp;saveform=members";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfmembersform" name="sfmembers">
	<?php echo(sfc_create_nonce('forum-adminform_members')); ?>
<?php

	sfa_paint_options_init();

#== MEMBERS Tab ============================================================

	sfa_paint_open_tab(__("Options", "sforum")." - ".__("Member Settings", "sforum"));

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Member Profiles", "sforum"), true, 'users-and-registration');
				sfa_paint_checkbox(__("Display Membership Lists", "sforum"), "sfshowmemberlist", $sfoptions['sfshowmemberlist']);
				sfa_paint_checkbox(__("Limit Membership Lists to User Groups the User is Membership in", "sforum"), "sflimitmemberlist", $sfoptions['sflimitmemberlist']);
				sfa_paint_checkbox(__("Disallow Members Not Logged in to Post As Guests", "sforum"), "sfcheckformember", $sfoptions['sfcheckformember']);
				sfa_paint_checkbox(__("Allow Members to Hide their Online Status", "sforum"), "sfhidestatus", $sfoptions['sfhidestatus']);
				sfa_paint_checkbox(__("Allow Members to View their Permissions", "sforum"), "sfviewperm", $sfoptions['sfviewperm']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Time Zone", "sforum"), true, 'time-zone');
			sfa_paint_input(__("Default Time Zone for New Members", "sforum"), "sfzone", $sfoptions['sfzone']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Guest Settings", "sforum"), true, 'guest-settings');
				sfa_paint_checkbox(__("Require Guests to Enter Email Address", "sforum"), "reqemail", $sfoptions['reqemail']);
				sfa_paint_checkbox(__("Store Guest Information in Cookie for Subsequent Visits", "sforum"), "storecookie", $sfoptions['storecookie']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Inactive Members Account Auto Removal", "sforum"), true, 'user-removal');
				sfa_paint_checkbox(__("Enable Auto Removal of Member Accounts", "sforum"), "sfuserremove", $sfoptions['sfuserremove']);
				sfa_paint_checkbox(__("Remove Inactive Members (if auto removal enabled)", "sforum"), "sfuserinactive", $sfoptions['sfuserinactive']);
				sfa_paint_checkbox(__("Remove Members Who Havent Posted  (if auto removal enabled)", "sforum"), "sfusernoposts", $sfoptions['sfusernoposts']);
				sfa_paint_input(__("Number of DAYS back to Remove Inactive Members and/or Members with No Posts (if auto removal enabled)", "sforum"), "sfuserperiod", $sfoptions['sfuserperiod']);
				if ($sfoptions['sched'])
				{
					$msg = __("Users auto removal cron job is scheduled to run daily.", "sforum");
					echo '<tr><td class="message" colspan="2" style="line-height:2em;">&nbsp;<u>'.$msg.'</u></td></tr>';
				}
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_tab_right_cell();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("User Memberships", "sforum"), true, 'user-memberships');
    			echo '<tr><td colspan="2"><br /><div class="sfoptionerror">';
    			echo __("Warning: Use caution when setting the single User Group Membership below. It should primarily be used in conjunction with a membership plugin (such as Wishlist) where strict User Group Membership is required.  Please note that auto User Group Membership (below) by WP Role or by Forum Rank may conflict or overwrite any manual User Group memberships (such as moderator) you may set if you have single User Group Membership set.", "sforum");
    			echo '</div><br />';
    			echo '</td></tr>';
				sfa_paint_checkbox(__("Users are Limited to Single User Group Membership", "sforum"), "sfsinglemembership", $sfoptions['sfsinglemembership']);
				echo('<tr><td colspan="2"><p class="subhead">'.__("Default User Group Membership", "sforum").':</p></td></tr>');
				sfa_paint_select_start(__("Default User Group for Guests", "sforum"), "sfguestsgroup", 'sfguestsgroup');
				echo(sfa_create_usergroup_select($sfoptions['sfguestsgroup']));
				sfa_paint_select_end();

				sfa_paint_select_start(__("Default User Group for New Members", "sforum"), "sfdefgroup", 'sfdefgroup');
				echo(sfa_create_usergroup_select($sfoptions['sfdefgroup']));
				sfa_paint_select_end();

				$roles = array_keys($wp_roles->role_names);
				if ($roles)
				{
					echo('<tr><td colspan="2"><p class="subhead">'.__("User Group Memberships Based on WP Role", "sforum").':</p></td></tr>');
					$sfoptions['role'] = array();
					foreach ($roles as $index => $role)
					{
						$value = sf_get_sfmeta('default usergroup', $role);
						if ($value)
						{
							$group = $value[0]['meta_value'];
						} else {
							$group = $sfoptions['sfdefgroup'];
						}
						echo '<input type="hidden" class="sfhiddeninput" name="sfoldrole['.$index.']" value="'.$group.'" />';
						sfa_paint_select_start(__("Default User Group for", "sforum").' '.$role, "sfrole[".$index."]", 'sfguestsgroup');
						echo(sfa_create_usergroup_select($group));
						sfa_paint_select_end();
					}
				}
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Auto Subscriptions", "sforum"), true, 'auto-subs');
    			echo '<tr><td colspan="2"><br /><div class="sfoptionerror">';
    			echo __("Warning: Auto subscribing members is the same as Opt Out (vs Opt In) and is considered bad practice.  If you enable this option, be sure of what you are doing and consider a disclaimer or notice to your users so they know their options.  This option will affect the default setting for new users.", "sforum");
    			echo '</div><br />';
    			echo '</td></tr>';
				sfa_paint_checkbox(__("Auto Subscribe Members to all Topics They Post In", "sforum"), "sfautosub", $sfoptions['sfautosub']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Member Options', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}

function sfa_create_usergroup_select($sfdefgroup)
{
	global $wpdb;

	$ugid = $wpdb->get_var("SELECT usergroup_id FROM ".SFUSERGROUPS." WHERE usergroup_id=".$sfdefgroup);
	if (empty($ugid))
	{
		$out.='<option selected="selected" value="-1">INVALID</option>'."\n";
	}

	$usergroups = sfa_get_usergroups_all();
	$default='';
	foreach ($usergroups as $usergroup)
	{
		if($usergroup->usergroup_id == $sfdefgroup)
		{
			$default = 'selected="selected" ';
		} else {
			$default - null;
		}
		$out.='<option '.$default.'value="'.$usergroup->usergroup_id.'">'.sf_filter_title_display($usergroup->usergroup_name).'</option>'."\n";
		$default='';
	}
	return $out;
}

?>