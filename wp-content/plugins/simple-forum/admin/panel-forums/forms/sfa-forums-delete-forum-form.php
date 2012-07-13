<?php
/*
Simple:Press
Admin Forums Delete Forum Form
$LastChangedDate: 2011-01-08 08:43:34 -0700 (Sat, 08 Jan 2011) $
$Rev: 5278 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# function to display the delete forum form.  It is hidden until the delete forum link is clicked
function sfa_forums_delete_forum_form($forum_id)
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfforumdelete<?php echo $forum_id; ?>').ajaxForm({
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

	sfa_paint_options_init();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=forums-loader&amp;saveform=deleteforum";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfforumdelete<?php echo $forum->forum_id; ?>" name="sfforumdelete<?php echo $forum->forum_id; ?>">
<?php
		echo(sfc_create_nonce('forum-adminform_forumdelete'));
		sfa_paint_open_tab(__("Forums", "sforum")." - ".__("Manage Groups and Forums", "sforum"));
			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Delete Forum", "sforum"), 'true', 'delete-forum', false);
?>
					<input type="hidden" name="group_id" value="<?php echo($forum->group_id); ?>" />
					<input type="hidden" name="forum_id" value="<?php echo($forum->forum_id); ?>" />
					<input type="hidden" name="cforum_seq" value="<?php echo($forum->forum_seq); ?>" />
					<input type="hidden" name="parent" value="<?php echo($forum->parent); ?>" />
					<input type="hidden" name="children" value="<?php echo(addslashes($forum->children)); ?>" />
<?php
					echo '<p>';
					echo __("Warning! You are about to delete a Forum!", "sforum");
					echo '</p>';
					echo '<p>';
					echo __("This will remove ALL Topics and Posts contained in this Forum.", "sforum");
					echo '</p>';
					echo '<p>';
					_e("Any Sub-Forums will be promoted.", "sforum");
					echo '</p>';
					echo '<p>';
					echo __("Please note that this action", "sforum") . '<strong>' . __(" can NOT be reversed.", "sforum") . '</strong>';
					echo '</p>';
					echo '<p>';
					echo __("Click on the \"Delete Forum\" button below to proceed.", "sforum");
					echo '</p>';
					echo '<p><strong>';
					_e("IMPORTANT: Be patient. For busy forums this action can take some time!", "spa");
					echo '</strong></p>';
				sfa_paint_close_fieldset(false);
			sfa_paint_close_panel();
		sfa_paint_close_tab();
?>
		<div class="sfform-submit-bar">
		<input type="submit" class="sfform-panel-button" id="sfforumdelete<?php echo $forum->forum_id; ?>" name="sfforumdelete<?php echo $forum->forum_id; ?>" value="<?php esc_attr_e(__('Delete Forum', 'sforum')); ?>" />
		<input type="button" class="sfform-panel-button" onclick="javascript:jQuery('#forum-<?php echo $forum->forum_id; ?>').html('');" id="sfforumdelete<?php echo $forum->forum_id; ?>" name="delforumcancel<?php echo $forum->forum_id; ?>" value="<?php esc_attr_e(__('Cancel', 'sforum')); ?>" />
		</div>
	</form>

	<div class="sfform-panel-spacer"></div>
<?php
	return;
}

?>