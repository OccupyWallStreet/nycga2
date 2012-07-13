<?php
/*
Simple:Press
Admin Permissions Delete Permission Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# function to display the delete permission set form.  It is hidden until the delete permission set link is clicked
function sfa_permissions_delete_permission_form($role_id)
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfroledel<?php echo $role_id; ?>').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloadpb').click();
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	global $sfactions;
	$role = sfa_get_role_row($role_id);

	sfa_paint_options_init();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=permissions-loader&amp;saveform=delperm";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfroledel<?php echo $role->role_id; ?>" name="sfroledel<?php echo $role->role_id; ?>">
<?php
		echo(sfc_create_nonce('forum-adminform_roledelete'));
		sfa_paint_open_tab(__("Permissions", "sforum")." - ".__("Manage Permissions", "sforum"));
			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Delete Permission", "sforum"), 'true', 'delete-master-permission-set', false);
?>
					<input type="hidden" name="role_id" value="<?php echo($role->role_id); ?>" />
<?php
					echo '<p>';
					echo __("Warning! You are about to delete a Permission!", "sforum");
					echo '</p>';
					echo '<p>';
					echo __("This will remove the Permission and also remove it from ALL Forums that used this Permission.", "sforum");
					echo '</p>';
					echo '<p>';
					echo __("Please note that this action", "sforum") . '<strong>' . __(" can NOT be reversed.", "sforum") . '</strong>';
					echo '</p>';
					echo '<p>';
					echo __("Click on the \"Delete Permission\" button below to proceed.", "sforum");
					echo '</p>';
				sfa_paint_close_fieldset(false);
			sfa_paint_close_panel();
		sfa_paint_close_tab();
?>
		<div class="sfform-submit-bar">
		<input type="submit" class="sfform-panel-button" id="sfpermedit<?php echo $role->role_id; ?>" name="sfpermdel<?php echo $role->role_id; ?>" value="<?php esc_attr_e(__('Delete Permission', 'sforum')); ?>" />
		<input type="button" class="sfform-panel-button" onclick="javascript:jQuery('#perm-<?php echo $role->role_id; ?>').html('');" id="sfpermdel<?php echo $role->role_id; ?>" name="delpermcancel<?php echo $role->role_id; ?>" value="<?php esc_attr_e(__('Cancel', 'sforum')); ?>" />
		</div>
		</form>

	<div class="sfform-panel-spacer"></div>
<?php
	return;
}

?>