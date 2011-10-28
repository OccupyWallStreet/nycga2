<?php
/*
Simple:Press
Admin User Groups Add Member Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_usergroups_add_members_form($usergroup_id)
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfmembernew<?php echo $usergroup_id; ?>').ajaxForm({
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

    $ahahURL = SFHOMEURL."index.php?sf_ahah=usergroups-loader&amp;saveform=addmembers";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfmembernew<?php echo $usergroup_id; ?>" name="sfmembernew<?php echo $usergroup_id ?>">
<?php
		echo(sfc_create_nonce('forum-adminform_membernew'));
		sfa_paint_open_tab(__("User Groups", "sforum")." - ".__("Manage User Groups", "sforum"));
			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Add Members", "sforum"), 'true', 'add-members', false);
?>
					<input type="hidden" name="usergroup_id" value="<?php echo ($usergroup_id); ?>" />
					<p align="center"><?php _e("Select Members To Add (use CONTROL for multiple users)", "sforum") ?></p>
<?php
                	$from = esc_js(__("Eligible Members", "sforum"));
                	$to = esc_js(__("Selected Members", "sforum"));
                    $action = 'addug';
                	include_once (SF_PLUGIN_DIR.'/library/ahah/sfc-ahah.php');
?>
					<div class="clearboth"></div>
<?php
				sfa_paint_close_fieldset(false);
			sfa_paint_close_panel();
		sfa_paint_close_tab();
?>
		<div class="sfform-submit-bar">
		<input type="submit" class="sfform-panel-button" id="sfmembernew<?php echo $usergroup_id; ?>" name="sfmembernew<?php echo $usergroup_id; ?>" onclick="javascript:jQuery('#member_id<?php echo $usergroup_id; ?> option').each(function(i) {jQuery(this).attr('selected', 'selected');});" value="<?php esc_attr_e(__('Add Members', 'sforum')); ?>" />
		<input type="button" class="sfform-panel-button" onclick="javascript:jQuery('#members-<?php echo $usergroup_id; ?>').html('');" id="sfmembernew<?php echo $usergroup_id; ?>" name="addmemberscancel<?php echo $usergroup_id; ?>" value="<?php esc_attr_e(__('Cancel', 'sforum')); ?>" />
		</div>
	</form>

	<div class="sfform-panel-spacer"></div>
<?php
	return;
}

?>