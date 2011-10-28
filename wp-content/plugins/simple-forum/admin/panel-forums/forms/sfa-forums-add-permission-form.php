<?php
/*
Simple:Press
Admin Forums Add Permission Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# function to display the add new forum permission set form.  It is hidden until the add permission set link is clicked
function sfa_forums_add_permission_form($forum_id)
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfpermissionnew<?php echo $forum_id; ?>').ajaxForm({
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

	$forum = $wpdb->get_row("SELECT * FROM ".SFFORUMS." WHERE forum_id=".$forum_id);

	echo '<div class="sfform-panel-spacer"></div>';

	sfa_paint_options_init();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=forums-loader&amp;saveform=addperm";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfpermissionnew<?php echo $forum->forum_id; ?>" name="sfpermissionnew<?php echo $forum->forum_id; ?>">
<?php
		echo(sfc_create_nonce('forum-adminform_permissionnew'));
		sfa_paint_open_tab(__("Forums", "sforum")." - ".__("Manage Groups and Forums", "sforum"));
			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Add Permission Set", "sforum"), 'true', 'add-user-group-permission-set', false);
?>
					<table class="form-table">
						<tr>
							<td class="sflabel"><?php sfa_display_usergroup_select(true, $forum->forum_id); ?></td>
						</tr><tr>
							<td class="sflabel"><?php sfa_display_permission_select(); ?></td>
						</tr>
					</table>
					<input type="hidden" name="forum_id" value="<?php echo($forum->forum_id); ?>" />
<?php
				sfa_paint_close_fieldset(false);
			sfa_paint_close_panel();
		sfa_paint_close_tab();
?>
		<div class="sfform-submit-bar">
		<input type="submit" class="sfform-panel-button" id="permnew<?php echo $forum->forum_id; ?>" name="permnew<?php echo $forum->forum_id; ?>" value="<?php esc_attr_e(__('Add Permission Set', 'sforum')); ?>" />
		<input type="button" class="sfform-panel-button" onclick="javascript:jQuery('#newperm-<?php echo $forum->forum_id; ?>').html('');" id="sfpermissionnew<?php echo $forum->forum_id; ?>" name="addpermcancel<?php echo $forum->forum_id; ?>" value="<?php esc_attr_e(__('Cancel', 'sforum')); ?>" />
		</div>
	</form>

	<div class="sfform-panel-spacer"></div>
<?php
	return;
}

?>