<?php
/*
Simple:Press
Admin User Groups Edit User Group Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# function to display the edit user group form.  It is hidden until the edit user group link is clicked
function sfa_usergroups_edit_usergroup_form($usergroup_id)
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfusergroupedit<?php echo $usergroup_id; ?>').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloadub').click();
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	$usergroup = sfa_get_usergroups_row($usergroup_id);

	sfa_paint_options_init();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=usergroups-loader&amp;saveform=editusergroup";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfusergroupedit<?php echo $usergroup->usergroup_id; ?>" name="sfusergroupedit<?php echo $usergroup->usergroup_id; ?>">
<?php
		echo(sfc_create_nonce('forum-adminform_usergroupedit'));
		sfa_paint_open_tab(__("User Groups", "sforum")." - ".__("Manage User Groups", "sforum"));
			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Edit User Group", "sforum"), 'true', 'edit-user-group', false);
?>
					<input type="hidden" name="usergroup_id" value="<?php echo($usergroup->usergroup_id); ?>" />
					<input type="hidden" name="ugroup_name" value="<?php echo(sf_filter_title_display($usergroup->usergroup_name)); ?>" />
					<input type="hidden" name="ugroup_desc" value="<?php echo(sf_filter_title_display($usergroup->usergroup_desc)); ?>" />
					<input type="hidden" name="ugroup_ismod" value="<?php echo($usergroup->usergroup_is_moderator); ?>" />
					<table class="form-table">
						<tr>
							<td class="sflabel"><?php _e("User Group Name", "sforum") ?>:</td>
							<td><input type="text" class="sfpostcontrol" size="45" name="usergroup_name" value="<?php echo sf_filter_title_display($usergroup->usergroup_name); ?>" /></td>
						</tr><tr>
							<td class="sflabel"><?php _e("User Group Description", "sforum") ?>:&nbsp;</td>
							<td><input type="text" class="sfpostcontrol" size="85" name="usergroup_desc" value="<?php echo sf_filter_title_display($usergroup->usergroup_desc); ?>" /></td>
						</tr><tr>
					<td class="sflabel" colspan="2"><label for="sfusergroup_is_moderator_<?php echo($usergroup->usergroup_id); ?>"><?php _e("Is Moderator", "sforum") ?>&nbsp;&nbsp;</label>
				<input type="checkbox" name="usergroup_is_moderator" id="sfusergroup_is_moderator_<?php echo($usergroup->usergroup_id); ?>" value="1" <?php if ($usergroup->usergroup_is_moderator == 1) echo 'checked="checked"'; ?>/>
							<?php _e("(Indicates that members of this User Group are considered Moderators)", "sforum") ?></td>
						</tr>
					</table>
<?php
				sfa_paint_close_fieldset(false);
			sfa_paint_close_panel();
		sfa_paint_close_tab();
?>
		<div class="sfform-submit-bar">
		<input type="submit" class="sfform-panel-button" id="sfusergroupedit<?php echo $role->role_id; ?>" name="sfusergroupedit<?php echo $role->role_id; ?>" value="<?php esc_attr_e(__('Update User Group', 'sforum')); ?>" />
		<input type="button" class="sfform-panel-button" onclick="javascript:jQuery('#usergroup-<?php echo $usergroup->usergroup_id; ?>').html('');" id="sfusergroupedit<?php echo $usergroup->usergroup_id; ?>" name="editusergroupcancel<?php echo $usergroup->usergroup_id; ?>" value="<?php esc_attr_e(__('Cancel', 'sforum')); ?>" />
		</div>
	</form>

	<div class="sfform-panel-spacer"></div>
<?php
	return;
}

?>