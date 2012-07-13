<?php
/*
Simple:Press
Admin User Groups Add User Group Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# function to display the create user group form.  It is hidden until the create user group link is clicked
function sfa_usergroups_create_usergroup_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfusergroupnew').ajaxForm({
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

	sfa_paint_options_init();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=usergroups-loader&amp;saveform=newusergroup";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfusergroupnew" name="sfusergroupnew">
<?php
		echo(sfc_create_nonce('forum-adminform_usergroupnew'));
		sfa_paint_open_tab(__("User Groups", "sforum")." - ".__("Create New User Group", "sforum"));
			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Create New User Group", "sforum"), 'true', 'create-new-user-group', false);
?>
					<table class="form-table">
						<tr>
							<td class="sflabel"><?php esc_attr_e(__("User Group Name", "sforum")) ?>:</td>
							<td><input type="text" class="sfpostcontrol" size="45" name="usergroup_name" value="" /></td>
						</tr><tr>
							<td class="sflabel"><?php esc_attr_e(__("User Group Description", "sforum")) ?>:&nbsp;&nbsp;</td>
							<td><input type="text" class="sfpostcontrol" size="85" name="usergroup_desc" value="" /></td>
						</tr><tr>
							<td class="sflabel" colspan="2"><label for="sfusergroup_is_moderator" class="sflabel"><?php _e("Is Moderator", "sforum") ?>&nbsp;&nbsp;</label>
							<input type="checkbox" name="usergroup_is_moderator" id="sfusergroup_is_moderator" value="1" />
							<?php _e("(Indicates that members of this User Group are considered moderators)", "sforum") ?></td>
						</tr>
					</table>
<?php
				sfa_paint_close_fieldset(false);
			sfa_paint_close_panel();
		sfa_paint_close_tab();
?>
		<div class="sfform-submit-bar">
		<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Create New User Group', 'sforum')); ?>" />
		</div>
		</form>

	<div class="sfform-panel-spacer"></div>
<?php
	return;
}

?>