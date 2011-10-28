<?php
/*
Simple:Press
Admin User Groups Delete Member Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_usergroups_delete_members_form($usergroup_id)
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfmemberdel<?php echo $usergroup_id; ?>').ajaxForm({
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

    $ahahURL = SFHOMEURL."index.php?sf_ahah=usergroups-loader&amp;saveform=delmembers";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfmemberdel<?php echo $usergroup_id; ?>" name="sfmemberdel<?php echo $usergroup_id ?>">
<?php
		echo(sfc_create_nonce('forum-adminform_memberdel'));
		sfa_paint_open_tab(__("User Groups", "sforum")." - ".__("Manage User Groups", "sforum"));
			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Delete/Move Members", "sforum"), 'true', 'move-delete-members', false);
?>
					<input type="hidden" name="usergroupid" value="<?php echo ($usergroup_id); ?>" />
					<p><?php _e("Select Members To Delete/Move (use CONTROL for multiple users)", "sforum") ?></p>
					<p><?php _e("To Move Members, Select New User Group", "sforum") ?></p>
					<?php sfa_display_usergroup_select() ?>
<?php
					$from = esc_js(__("Current Members", "sforum"));
					$to = esc_js(__("Selected Members", "sforum"));
                    $action = 'delug';
                	include_once (SF_PLUGIN_DIR.'/library/ahah/sfc-ahah.php');
?>
					<div class="clearboth"></div>
<?php
				sfa_paint_close_fieldset(false);
			sfa_paint_close_panel();
		sfa_paint_close_tab();
?>
		<div class="sfform-submit-bar">
		<input type="submit" class="sfform-panel-button" id="sfmemberdel<?php echo $usergroup_id; ?>" name="sfmemberdel<?php echo $usergroup_id; ?>" onclick="javascript:jQuery('#dmember_id<?php echo $usergroup_id; ?> option').each(function(i) {jQuery(this).attr('selected', 'selected');});" value="<?php esc_attr_e(__('Delete/Move Members', 'sforum')); ?>" />
		<input type="button" class="sfform-panel-button" onclick="javascript:jQuery('#members-<?php echo $usergroup_id; ?>').html('');" id="sfmemberdel<?php echo $usergroup_id; ?>" name="delmemberscancel<?php echo $usergroup_id; ?>" value="<?php esc_attr_e(__('Cancel', 'sforum')); ?>" />
		</div>
	</form>

	<div class="sfform-panel-spacer"></div>
<?php
	return;
}

?>