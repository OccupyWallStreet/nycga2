<?php
/*
Simple:Press
Admin Admins Current Admins Form
$LastChangedDate: 2010-10-15 17:38:17 -0700 (Fri, 15 Oct 2010) $
$Rev: 4762 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_admins_manage_admins_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfupdatecaps').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloadma').click();
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
	jQuery('#sfaddadmins').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloadma').click();
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php


	global $wpdb, $current_user;

	$adminsexist = false;
	$adminrecords = sfa_get_admins_caps_data();

	sfa_paint_options_init();

	if ($adminrecords)
	{
		$adminsexist = true;

        $ahahURL = SFHOMEURL."index.php?sf_ahah=admins-loader&amp;saveform=manageadmin";
		?>
		<form action="<?php echo($ahahURL); ?>" method="post" id="sfupdatecaps" name="sfupdatecaps">
		<?php echo(sfc_create_nonce('forum-adminform_sfupdatecaps')); ?>
		<?php

	sfa_paint_open_tab(__("Admins", "sforum")." - ".__("Manage Admins", "sforum"));
		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Current Admins", "sforum"), 'true', 'manage-admins', false);
				?>
				<table class="sfsubtable" cellpadding="0" cellspacing="0">
					<tr>
						<th align="center" width="70"><?php _e("User ID", "sforum"); ?></th>
						<th scope="col"><?php _e("Admin Name", "sforum") ?></th>
						<th align="center" width="10" scope="col"></th>
						<th align="center" width="600" scope="col"><?php _e("Admin Capabilities", "sforum") ?></th>
					</tr>
					<?php
					foreach ($adminrecords as $admin)
					{
						$user = new WP_User($admin['id']);
						$manage_opts = $user->has_cap('SPF Manage Options') ? 1 : 0;
						$manage_forums = $user->has_cap('SPF Manage Forums') ? 1 : 0;
						$manage_ugs = $user->has_cap('SPF Manage User Groups') ? 1 : 0;
						$manage_perms = $user->has_cap('SPF Manage Permissions') ? 1 : 0;
						$manage_comps = $user->has_cap('SPF Manage Components') ? 1 : 0;
						$manage_tags = $user->has_cap('SPF Manage Tags') ? 1 : 0;
						$manage_users = $user->has_cap('SPF Manage Users') ? 1 : 0;
						$manage_profiles = $user->has_cap('SPF Manage Profiles') ? 1 : 0;
						$manage_admins = $user->has_cap('SPF Manage Admins') ? 1 : 0;
						$manage_tools = $user->has_cap('SPF Manage Toolbox') ? 1 : 0;
						$manage_config = $user->has_cap('SPF Manage Configuration') ? 1 : 0;
						?>
					<tr>
						<td align="center"><?php echo($admin['id']); ?></td>
						<td>
							<strong><?php echo sf_filter_name_display($admin['display_name']); ?></strong>
							<input type="hidden" name="uids[]" value="<?php echo($admin['id']); ?>" />
						</td>
						<td align="center"></td>
						<td align="center">
							<table width="100%" class="sfsubsubtable">
								<tr>
									<td>
										<?php sfa_render_caps_checkbox(__("Manage Options", "sforum"), "manage-opts[".$admin['id']."]", $manage_opts, $admin['id']); ?>
										<input type="hidden" name="old-opts[<?php echo $admin['id'] ?>]" value="<?php echo $manage_opts; ?>" />
									</td>
									<td>
										<?php sfa_render_caps_checkbox(__("Manage Forums", "sforum"), "manage-forums[".$admin['id']."]", $manage_forums, $admin['id']); ?>
										<input type="hidden" name="old-forums[<?php echo $admin['id'] ?>]" value="<?php echo $manage_forums; ?>" />
									</td>
									<td>
										<?php sfa_render_caps_checkbox(__("Manage User Groups", "sforum"), "manage-ugs[".$admin['id']."]", $manage_ugs, $admin['id']); ?>
										<input type="hidden" name="old-ugs[<?php echo $admin['id'] ?>]" value="<?php echo $manage_ugs; ?>" />
									</td>
								</tr>
								<tr>
									<td>
										<?php sfa_render_caps_checkbox(__("Manage Permissions", "sforum"), "manage-perms[".$admin['id']."]", $manage_perms, $admin['id']); ?>
										<input type="hidden" name="old-perms[<?php echo $admin['id'] ?>]" value="<?php echo $manage_perms; ?>" />
									</td>
									<td>
										<?php sfa_render_caps_checkbox(__("Manage Components", "sforum"), "manage-comps[".$admin['id']."]", $manage_comps, $admin['id']); ?>
										<input type="hidden" name="old-comps[<?php echo $admin['id'] ?>]" value="<?php echo $manage_comps; ?>" />
									</td>
									<td>
										<?php sfa_render_caps_checkbox(__("Manage Tags", "sforum"), "manage-tags[".$admin['id']."]", $manage_tags, $admin['id']); ?>
										<input type="hidden" name="old-tags[<?php echo $admin['id'] ?>]" value="<?php echo $manage_tags; ?>" />
									</td>
								</tr>
								<tr>
									<td>
										<?php sfa_render_caps_checkbox(__("Manage Users", "sforum"), "manage-users[".$admin['id']."]", $manage_users, $admin['id']); ?>
										<input type="hidden" name="old-users[<?php echo $admin['id'] ?>]" value="<?php echo $manage_users; ?>" />
									</td>
									<td>
										<?php sfa_render_caps_checkbox(__("Manage Profiles", "sforum"), "manage-profiles[".$admin['id']."]", $manage_profiles, $admin['id']); ?>
										<input type="hidden" name="old-profiles[<?php echo $admin['id'] ?>]" value="<?php echo $manage_profiles; ?>" />
									</td>
									<td>
										<?php if ($admin['id'] == $current_user->id) { ?>
<?php
											echo __("Manage Admins", "sforum");
?>
											<input type="hidden" name="manage-admins[<?php echo $admin['id'] ?>]" value="<?php echo $manage_admins; ?>" />
											<img src="<?php echo SFADMINIMAGES.'locked.png'; ?>" alt="" style="vertical-align:middle;padding-left:10px;" />
<?php
										} else {
											sfa_render_caps_checkbox(__("Manage Admins", "sforum"), "manage-admins[".$admin['id']."]", $manage_admins, $admin['id']);
										}
?>
										<input type="hidden" name="old-admins[<?php echo $admin['id'] ?>]" value="<?php echo $manage_admins; ?>" />
									</td>
								</tr>
								<tr>
									<td>
										<?php sfa_render_caps_checkbox(__("Manage Toolbox", "sforum"), "manage-tools[".$admin['id']."]", $manage_tools, $admin['id']); ?>
										<input type="hidden" name="old-tools[<?php echo $admin['id'] ?>]" value="<?php echo $manage_tools; ?>" />
									</td>
									<td>
										<?php sfa_render_caps_checkbox(__("Manage Configuration", "sforum"), "manage-config[".$admin['id']."]", $manage_config, $admin['id']); ?>
										<input type="hidden" name="old-config[<?php echo $admin['id'] ?>]" value="<?php echo $manage_config; ?>" />
									</td>
								</tr>
							</table>
						</td>
					</tr>
				<?php } ?>
				</table>

<?php
			sfa_paint_close_fieldset(false);
		sfa_paint_close_panel();
	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="savecaps" name="savecaps" value="<?php esc_attr_e(__('Update Admin Capabilities', 'sforum')); ?>" />
	</div>
	</form>

	<div class="sfform-panel-spacer"></div>
<?php
	}

    $ahahURL = SFHOMEURL."index.php?sf_ahah=admins-loader&amp;saveform=addadmin";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfaddadmins" name="sfaddadmins">
	<?php echo(sfc_create_nonce('forum-adminform_sfaddadmins')); ?>

<?php

	sfa_paint_open_tab(__("Manage Admins", "sforum")." - ".__("Add Admins", "sforum"));

	sfa_paint_open_panel();
	sfa_paint_open_fieldset(__("Add New Admins", "sforum"), false, '', false);
?>
	<table align="center" class="forum-table" cellpadding="0" cellspacing="0">
		<tr>
			<th align="center"><?php _e("Select New Admin Users", "sforum"); ?></th>
			<th align="center" width="70" scope="col"></th>
			<th align="center" width="175" scope="col"><?php _e("Select New Admin Capabilities", "sforum") ?></th>
		</tr>
		<tr>
			<td align="center">
				<select multiple="multiple" class="sfacontrol" name="newadmins[]" size="10">
<?php
				$users = $wpdb->get_results("SELECT user_id, display_name FROM ".SFMEMBERS." WHERE admin = 0 ORDER BY display_name", ARRAY_A);
				$out = '';
				for ($x=0; $x<count($users); $x++)
				{
					$out.='<option value="'.esc_attr($users[$x]['user_id']).'">'.sf_filter_name_display($users[$x]['display_name']).'</option>'."\n";
				}
				echo $out;
?>
				</select>
			</td>
			<td></td>
			<td>
				<table class="form-table">
					<tr><td class="sflabel"><?php sfa_render_caps_checkbox(__("Manage Options", "sforum"), "add-opts", 0); ?></td></tr>
					<tr><td class="sflabel"><?php sfa_render_caps_checkbox(__("Manage Forums", "sforum"), "add-forums", 0); ?></td></tr>
					<tr><td class="sflabel"><?php sfa_render_caps_checkbox(__("Manage User Groups", "sforum"), "add-ugs", 0); ?></td></tr>
					<tr><td class="sflabel"><?php sfa_render_caps_checkbox(__("Manage Permissions", "sforum"), "add-perms", 0); ?></td></tr>
					<tr><td class="sflabel"><?php sfa_render_caps_checkbox(__("Manage Components", "sforum"), "add-comps", 0); ?></td></tr>
					<tr><td class="sflabel"><?php sfa_render_caps_checkbox(__("Manage Tags", "sforum"), "add-tags", 0); ?></td></tr>
					<tr><td class="sflabel"><?php sfa_render_caps_checkbox(__("Manage Users", "sforum"), "add-users", 0); ?></td></tr>
					<tr><td class="sflabel"><?php sfa_render_caps_checkbox(__("Manage Profiles", "sforum"), "add-profiles", 0); ?></td></tr>
					<tr><td class="sflabel"><?php sfa_render_caps_checkbox(__("Manage Admins", "sforum"), "add-admins", 0); ?></td></tr>
					<tr><td class="sflabel"><?php sfa_render_caps_checkbox(__("Manage Toolbox", "sforum"), "add-tools", 0); ?></td></tr>
					<tr><td class="sflabel"><?php sfa_render_caps_checkbox(__("Manage Configuration", "sforum"), "add-config", 0); ?></td></tr>
				</table>
			</td>
		</tr>
	</table>

<?php
		sfa_paint_close_fieldset(false);
		sfa_paint_close_panel();
	sfa_paint_open_panel();
	sfa_paint_open_fieldset(__("WP Admins But Not SPF Admins", "sforum"), false, '', false);

?>

	<table align="center" class="sfmaintable" cellpadding="0" cellspacing="0" style="width:auto">
		<tr>
			<th align="center" width="30" scope="col"></th>
			<th align="center"><?php _e("User ID", "sforum"); ?></th>
			<th align="center" scope="col"><?php _e("Admin Name", "sforum") ?></th>
			<th align="center" width="30" scope="col"></th>
		</tr>
<?php
		$wp_admins = new SP_User_Search('', '', 'administrator');
		$is_users = false;
		for ($x=0; $x<count($wp_admins->results); $x++)
		{
			$query = "SELECT display_name FROM ".SFMEMBERS." WHERE admin = 0 AND user_id = ".$wp_admins->results[$x];
			$username = $wpdb->get_var($query);
			if ($username)
			{
				echo '<tr>';
				echo '<td></td>';
				echo '<td align="center">';
				echo $wp_admins->results[$x];
				echo '</td>';
				echo '<td>';
				echo esc_html($username);
				echo '</td>';
				echo '<td></td>';
				echo '</tr>';
				$is_users = true;
			}
		}
		if (!$is_users)
		{
			echo '<tr>';
			echo '<td></td>';
			echo '<td colspan="2">';
			echo __('No WP administrators that are not SPF admins were found', 'sforum');
			echo '</td>';
			echo '<td></td>';
			echo '</tr>';
		}
	?>
	</table>

<?php
		sfa_paint_close_fieldset(false);
		sfa_paint_close_panel();

	sfa_paint_close_tab();
?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="savenew" name="savenew" value="<?php esc_attr_e(__('Add New Admins', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}

function sfa_render_caps_checkbox($label, $name, $value, $user=0)
{
	$pos = strpos($name, '[');
	if ($pos) $thisid = substr($name, 0, $pos).$user; else $thisid = $name.$user;
	echo "<label for='sf-$thisid'>$label</label>";
	echo "<input type='checkbox' name='$name' id='sf-$thisid' ";
	if ($value) echo "checked='checked' ";
	echo '/>';
	return;
}

?>