<?php
/*
Simple:Press
Admin Forums Edit Group Form
$LastChangedDate: 2010-12-20 08:06:14 -0700 (Mon, 20 Dec 2010) $
$Rev: 5098 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# function to display the edit group information form.  It is hidden until the edit group link is clicked
function sfa_forums_edit_group_form($group_id)
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfgroupedit<?php echo $group_id; ?>').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloadfb').click();
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	global $wpdb, $SFPATHS;

	$group = $wpdb->get_row("SELECT * FROM ".SFGROUPS." WHERE group_id=".$group_id);

	sfa_paint_options_init();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=forums-loader&amp;saveform=editgroup";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfgroupedit<?php echo $group->group_id; ?>" name="sfgroupedit<?php echo $group->group_id; ?>">
<?php
		echo(sfc_create_nonce('forum-adminform_groupedit'));
		sfa_paint_open_tab(__("Forums", "sforum")." - ".__("Manage Groups and Forums", "sforum"));
			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Edit Group", "sforum"), 'true', 'edit-forum-group', false);
?>
					<input type="hidden" name="group_id" value="<?php echo($group->group_id); ?>" />
					<input type="hidden" name="cgroup_name" value="<?php echo(sf_filter_title_display($group->group_name)); ?>" />
					<input type="hidden" name="cgroup_desc" value="<?php echo(sf_filter_text_edit($group->group_desc)); ?>" />
					<input type="hidden" name="cgroup_seq" value="<?php echo($group->group_seq); ?>" />
					<input type="hidden" name="cgroup_icon" value="<?php echo(esc_attr($group->group_icon)); ?>" />
					<input type="hidden" name="cgroup_rss" value="<?php echo($group->group_rss); ?>" />
					<input type="hidden" name="cgroup_message" value="<?php echo(sf_filter_text_edit($group->group_message)); ?>" />
					<table class="form-table">
						<tr>
							<td class="sflabel"><?php _e("Group Name", "sforum") ?>:</td>
							<td><input type="text" class=" sfpostcontrol" size="45" name="group_name" value="<?php echo(sf_filter_title_display($group->group_name)); ?>" /></td>
						</tr><tr>
							<td class="sflabel"><?php _e("Description", "sforum") ?>:&nbsp;</td>
							<td><input type="text" class=" sfpostcontrol" size="85" name="group_desc" value="<?php echo(sf_filter_text_edit($group->group_desc)); ?>" /></td>
						</tr><tr>
<?php
							echo sfa_group_sequence_options('edit', $group->group_seq);
?>
						</tr><tr>
							<td class="sflabel"><?php _e('Custom Icon', 'sforum') ?>:<br /><?php _e("Custom Icons can be Uploaded on the Components - Custom Icons Panel (50 char filename limit)", "sforum"); ?></td>
							<td>
								<?php sfa_select_icon_dropdown('group_icon', __("Select Icon", "sforum"), SF_STORE_DIR.'/'.$SFPATHS['custom-icons'].'/', $group->group_icon); ?>
								</td>
						</tr><tr>
							<td class="sflabel"><?php _e('Replacement External RSS URL', 'sforum') ?>:<br /><?php _e("Default", "sforum"); ?>: <strong><?php echo sf_build_qurl('group='.$group->group_id, 'xfeed=group'); ?></strong></td>
							<td><input class="sfpostcontrol" type="text" name="group_rss" size="45" value="<?php echo(sf_filter_url_display($group->group_rss)); ?>" /></td>
						</tr><tr>
							<td class="sflabel"><?php _e('Special Group Message to be displayed above forums', 'sforum') ?>:</td>
							<td><textarea class="sfpostcontrol" cols="65" rows="3" name="group_message"><?php echo(sf_filter_text_edit($group->group_message)); ?></textarea></td>
						</tr>
					</table>
					<br /><br />
					<?php _e("Set Default User Group Permission Sets for this Group", "sforum") ?>
					<br /><br />
					<?php _e("Note - This will not will add or modify any current permissions. It's only a default setting for future forums created in this group.  Existing default User Group settings will be shown in the drop down menus.", "sforum") ?>
						<table class="form-table">
<?php
							$usergroups = sfa_get_usergroups_all();
							foreach ($usergroups as $usergroup)
							{
?>
							<tr>
								<td class="sflabel"><?php echo sf_filter_title_display($usergroup->usergroup_name); ?>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input type="hidden" name="usergroup_id[]" value="<?php echo($usergroup->usergroup_id); ?>" /></td>
								<?php $roles = sfa_get_all_roles(); ?>
								<td class="sflabel"><select style="width:165px" class='sfacontrol' name='role[]'>
<?php
									$defrole = sfa_get_defpermissions_role($group->group_id, $usergroup->usergroup_id);
									$out = '';
									if ($defrole == -1 || $defrole == '')
									{
										$out = '<option value="-1">'.__("Select Permission Set", "sforum").'</option>';
									}
									foreach($roles as $role)
									{
										$selected = '';
										if ($defrole == $role->role_id)
										{
											$selected = 'selected="selected" ';
										}
										$out.='<option '.$selected.'value="'.$role->role_id.'">'.sf_filter_title_display($role->role_name).'</option>'."\n";
									}
									echo $out;
?>
									</select>
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
		<input type="submit" class="sfform-panel-button" id="groupedit<?php echo $group->group_id; ?>" name="groupedit<?php echo $group->group_id; ?>" value="<?php esc_attr_e(__('Update Group', 'sforum')); ?>" />
		<input type="button" class="sfform-panel-button" onclick="javascript:jQuery('#group-<?php echo $group->group_id; ?>').html('');" id="sfgroupedit<?php echo $group->group_id; ?>" name="groupeditcancel<?php echo $group->group_id; ?>" value="<?php esc_attr_e(__('Cancel', 'sforum')); ?>" />
		</div>
	</form>

	<div class="sfform-panel-spacer"></div>
<?php
	return;
}

?>