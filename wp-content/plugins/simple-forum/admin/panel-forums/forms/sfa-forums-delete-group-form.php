<?php
/*
Simple:Press
Admin Forums Delete Group Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# function to display the delete group form.  It is hidden until the delete group link is clicked
function sfa_forums_delete_group_form($group_id)
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfgroupdel<?php echo $group_id; ?>').ajaxForm({
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

    $ahahURL = SFHOMEURL."index.php?sf_ahah=forums-loader&amp;saveform=deletegroup";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfgroupdel<?php echo $group->group_id; ?>" name="sfgroupdel<?php echo $group->group_id; ?>">
<?php
		echo(sfc_create_nonce('forum-adminform_groupdelete'));
		sfa_paint_open_tab(__("Forums", "sforum")." - ".__("Manage Groups and Forums", "sforum"));
			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Delete Group", "sforum"), 'true', 'delete-forum-group', false);
?>
					<input type="hidden" name="group_id" value="<?php echo($group->group_id); ?>" />
					<input type="hidden" name="cgroup_seq" value="<?php echo($group->group_seq); ?>" />
<?php
					echo '<p>';
					echo __("Warning! You are about to delete a Group!", "sforum");
					echo '</p>';
					echo '<p>';
					echo __("This will remove ALL Forums, Topics and Posts contained in this Group.", "sforum");
					echo '</p>';
					echo '<p>';
					echo __("Please note that this action", "sforum") . '<strong>' . __(" can NOT be reversed.", "sforum") . '</strong>';
					echo '</p>';
					echo '<p>';
					echo __("Click on the \"Delete Group\" button below to proceed.", "sforum");
					echo '</p>';

				sfa_paint_close_fieldset(false);
			sfa_paint_close_panel();
		sfa_paint_close_tab();
?>
		<div class="sfform-submit-bar">
		<input type="submit" class="sfform-panel-button" id="groupdel<?php echo $group->group_id; ?>" name="groupdel<?php echo $group->group_id; ?>" value="<?php esc_attr_e(__('Delete Group', 'sforum')); ?>" />
		<input type="button" class="sfform-panel-button" onclick="javascript:jQuery('#group-<?php echo $group->group_id; ?>').html('');" id="sfgroupdel<?php echo $group->group_id; ?>" name="groupdelcancel<?php echo $group->group_id; ?>" value="<?php esc_attr_e(__('Cancel', 'sforum')); ?>" />
		</div>
	</form>

	<div class="sfform-panel-spacer"></div>
<?php
	return;
}

?>