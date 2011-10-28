<?php
/*
Simple:Press
Admin User Groups Delete User Group Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# function to display the delete user group form.  It is hidden until the delete user group link is clicked
function sfa_usergroups_delete_usergroup_form($usergroup_id)
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfusergroupdel<?php echo $usergroup_id; ?>').ajaxForm({
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

    $ahahURL = SFHOMEURL."index.php?sf_ahah=usergroups-loader&amp;saveform=delusergroup";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfusergroupdel<?php echo $usergroup->usergroup_id; ?>" name="sfusergroupdel<?php echo $usergroup->usergroup_id; ?>">
<?php
		echo(sfc_create_nonce('forum-adminform_usergroupdelete'));
		sfa_paint_open_tab(__("User Groups", "sforum")." - ".__("Manage User Groups", "sforum"));
			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Delete User Group", "sforum"), 'true', 'delete-user-group', false);
?>
					<input type="hidden" name="usergroup_id" value="<?php echo($usergroup->usergroup_id); ?>" />
<?php
					echo '<p>';
					echo __("Warning! You are about to delete a User Group!", "sforum");
					echo '</p>';
					echo '<p>';
					echo __("This will remove the User Group and also remove User Memberships contained in this User Group.", "sforum");
					echo '</p>';
					echo '<p>';
					echo __("Please note that this action", "sforum") . '<strong>' . __(" can NOT be reversed.", "sforum") . '</strong>';
					echo '</p>';
					echo '<p>';
					echo __("Click on the \"Delete User Group\" button below to proceed.", "sforum");
					echo '</p>';
				sfa_paint_close_fieldset(false);
			sfa_paint_close_panel();
		sfa_paint_close_tab();
?>
		<div class="sfform-submit-bar">
		<input type="submit" class="sfform-panel-button" id="sfusergroupdel<?php echo $usergroup->usergroup_id; ?>" name="sfusergroupdel<?php echo $usergroup->usergroup_id; ?>" value="<?php esc_attr_e(__('Delete User Group', 'sforum')); ?>" />
		<input type="button" class="sfform-panel-button" onclick="javascript:jQuery('#usergroup-<?php echo $usergroup->usergroup_id; ?>').html('');" id="sfusergroupdel<?php echo $usergroup->usergroup_id; ?>" name="delusergroupcancel<?php echo $usergroup->usergroup_id; ?>" value="<?php esc_attr_e(__('Cancel', 'sforum')); ?>" />
		</div>
		</form>

	<div class="sfform-panel-spacer"></div>
<?php
	return;
}

?>