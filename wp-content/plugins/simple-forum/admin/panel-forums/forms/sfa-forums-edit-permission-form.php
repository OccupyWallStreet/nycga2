<?php
/*
Simple:Press
Admin Forums Edit Permission Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# function to display the edit forum permission set form.  It is hidden until the edit permission set link is clicked
function sfa_forums_edit_permission_form($perm_id)
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfpermissionnedit<?php echo $perm_id; ?>').ajaxForm({
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

	global $wpdb;

	$perm = $wpdb->get_row("SELECT * FROM ".SFPERMISSIONS." WHERE permission_id=".$perm_id);

	echo '<div class="sfform-panel-spacer"></div>';

	sfa_paint_options_init();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=forums-loader&amp;saveform=editperm";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfpermissionnedit<?php echo $perm->permission_id; ?>" name="sfpermissionedit<?php echo $perm->permission_id; ?>">
<?php
		echo(sfc_create_nonce('forum-adminform_permissionedit'));
		sfa_paint_open_tab(__("Forums", "sforum")." - ".__("Manage Groups and Forums", "sforum"));
			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Edit Permission Set", "sforum"), 'true', 'edit-permission-set', false);
?>
					<input type="hidden" name="permission_id" value="<?php echo($perm->permission_id); ?>" />
					<input type="hidden" name="ugroup_perm" value="<?php echo($perm->permission_role) ?>" />
					<table class="form-table">
						<tr>
							<td class="sflabel"><?php sfa_display_permission_select($perm->permission_role); ?></td>
						</tr>
					</table>
<?php
				sfa_paint_close_fieldset(false);
			sfa_paint_close_panel();
		sfa_paint_close_tab();
?>
		<div class="sfform-submit-bar">
		<input type="submit" class="sfform-panel-button" id="editperm<?php echo $perm->permission_id; ?>" name="editperm<?php echo $perm->permission_id; ?>" value="<?php esc_attr_e(__('Update Permission Set', 'sforum')); ?>" />
		<input type="button" class="sfform-panel-button" onclick="javascript:jQuery('#curperm-<?php echo $perm->permission_id; ?>').html('');" id="sfpermissionnedit<?php echo $perm->permission_id; ?>" name="editpermcancel<?php echo $perm->permission_id; ?>" value="<?php esc_attr_e(__('Cancel', 'sforum')); ?>" />
		</div>
	</form>

	<div class="sfform-panel-spacer"></div>
<?php
	return;
}

?>