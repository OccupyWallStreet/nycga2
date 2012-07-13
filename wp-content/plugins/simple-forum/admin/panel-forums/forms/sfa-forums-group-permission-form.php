<?php
/*
Simple:Press
Admin Forums Add Group Permission Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# function to display the add group permission set form.  It is hidden until the add group permission set link is clicked
function sfa_forums_add_group_permission_form($group_id)
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfgrouppermnew<?php echo $group_id; ?>').ajaxForm({
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

	$group = $wpdb->get_row("SELECT * FROM ".SFGROUPS." WHERE group_id=".$group_id);

	sfa_paint_options_init();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=forums-loader&amp;saveform=grouppermission";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfgrouppermnew<?php echo $group->group_id; ?>" name="sfgrouppermnew<?php echo $group->group_id; ?>">
<?php
		echo(sfc_create_nonce('forum-adminform_grouppermissionnew'));
		sfa_paint_open_tab(__("Forums", "sforum")." - ".__("Manage Groups and Forums", "sforum"));
			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Add a User Group Permission Set to an Entire Group", "sforum"), 'true', 'add-a-user-group-permission-set-to-an-entire-group', false);
?>
					<?php sprintf(__("Set a User Group Permission Set for ALL Forum in a Group: %s", "sforum"), sf_filter_title_display($group->group_name)); ?>
					<table class="form-table">
						<tr>
							<td class="sflabel"><?php sfa_display_usergroup_select(); ?></td>
						</tr><tr>
							<td class="sflabel"><?php sfa_display_permission_select(); ?></td>
						</tr>
					</table>

					<input type="hidden" name="group_id" value="<?php echo($group->group_id); ?>" />
					<p><?php _e("Caution:  Any current Permission Set for the selected User Group for ANY Forum in this Group will be overwritten.", "sforum") ?></p>
<?php
				sfa_paint_close_fieldset(false);
			sfa_paint_close_panel();
		sfa_paint_close_tab();
?>
		<div class="sfform-submit-bar">
		<input type="submit" class="sfform-panel-button" id="groupperm<?php echo $group->group_id; ?>" name="groupperm<?php echo $group->group_id; ?>" value="<?php esc_attr_e(__('Add Group Permission', 'sforum')); ?>" />
		<input type="button" class="sfform-panel-button" onclick="javascript:jQuery('#group-<?php echo $group->group_id; ?>').html('');" id="grouppermcancel<?php echo $group->group_id; ?>" name="grouppermcancel<?php echo $group->group_id; ?>" value="<?php esc_attr_e(__('Cancel', 'sforum')); ?>" />
		</div>
	</form>

	<div class="sfform-panel-spacer"></div>
<?php
	return;
}

?>