<?php
/*
Simple:Press
Admin Forums Remove All Permissions Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# function to display the remove all permission set form.  It is hidden until the remove all permission set link is clicked
function sfa_forums_remove_perms_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfallpermissionsdel').ajaxForm({
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

	sfa_paint_options_init();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=forums-loader&amp;saveform=removeperms";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfallpermissionsdel" name="sfallpermissionsdel">
<?php
		echo(sfc_create_nonce('forum-adminform_allpermissionsdelete'));
		sfa_paint_open_tab(__("Forums", "sforum")." - ".__("Delete All Permission Sets", "sforum"));
			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Delete All Forum Permission Sets", "sforum"), 'true', 'delete-all-forum-permission-sets', false);
					echo '<p>';
					echo __("Warning! You are about to delete ALL Permission Sets!", "sforum");
					echo '</p>';
					echo '<p>';
					echo __("This will delete ALL Permission Sets for all Groups/Forum.", "sforum");
					echo '</p>';
					echo '<p>';
					echo __("Please note that this action", "sforum") . '<strong>' . __(" can NOT be reversed.", "sforum") . '</strong>';
					echo '</p>';
					echo '<p>';
					echo __("Click on the \"Delete All Permission Sets\" button below to proceed.", "sforum");
					echo '</p>';
				sfa_paint_close_fieldset(false);
			sfa_paint_close_panel();
		sfa_paint_close_tab();
?>
		<div class="sfform-submit-bar">
		<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Delete All Permission Sets', 'sforum')); ?>" />
		</div>
	</form>

	<div class="sfform-panel-spacer"></div>
<?php
	return;
}
?>