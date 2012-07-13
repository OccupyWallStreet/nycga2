<?php
/*
Simple:Press
Admin Forums Delete Permission Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# function to display the delete forum permission set form.  It is hidden until the delete permission set link is clicked
function sfa_forums_delete_permission_form($perm_id)
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfpermissiondel<?php echo $perm_id; ?>').ajaxForm({
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

    $ahahURL = SFHOMEURL."index.php?sf_ahah=forums-loader&amp;saveform=delperm";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfpermissiondel<?php echo $perm->permission_id; ?>" name="sfpermissiondel<?php echo $perm->permission_id; ?>">
<?php
		echo(sfc_create_nonce('forum-adminform_permissiondelete'));
		sfa_paint_open_tab(__("Forums", "sforum")." - ".__("Manage Groups and Forums", "sforum"));
			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Delete Permission Set", "sforum"), 'true', 'delete-permission-set', false);
?>
					<input type="hidden" name="permission_id" value="<?php echo($perm->permission_id); ?>" />
<?php
					echo '<p>';
					echo __("Warning! You are about to delete a Permission Set!", "sforum");
					echo '</p>';
					echo '<p>';
					echo __("This will remove ALL access to this Forum for this User Group.", "sforum");
					echo '</p>';
					echo '<p>';
					echo __("Please note that this action", "sforum") . '<strong>' . __(" can NOT be reversed.", "sforum") . '</strong>';
					echo '</p>';
					echo '<p>';
					echo __("Click on the \"Delete Permission Set\" button below to proceed.", "sforum");
					echo '</p>';

				sfa_paint_close_fieldset(false);
			sfa_paint_close_panel();
		sfa_paint_close_tab();
?>
		<div class="sfform-submit-bar">
		<input type="submit" class="sfform-panel-button" id="delperm<?php echo $perm->permission_id; ?>" name="delperm<?php echo $perm->permission_id; ?>" value="<?php esc_attr_e(__('Delete Permission Set', 'sforum')); ?>" />
		<input type="button" class="sfform-panel-button" onclick="javascript:jQuery('#curperm-<?php echo $perm->permission_id; ?>').html('');" id="sfpermissiondel<?php echo $perm->permission_id; ?>" name="delpermcancel<?php echo $perm->permission_id; ?>" value="<?php esc_attr_e(__('Cancel', 'sforum')); ?>" />
	</div>
</form>

	<div class="sfform-panel-spacer"></div>
<?php
	return;
}

?>