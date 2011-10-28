<?php
/*
Simple:Press
Admin Forums Global Permission Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# function to display the add global permission set form. It is hidden until user clicks the add global permission set link
function sfa_forums_global_perm_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfnewglobalpermission').ajaxForm({
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

    $ahahURL = SFHOMEURL."index.php?sf_ahah=forums-loader&amp;saveform=globalperm";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfnewglobalpermission" name="sfnewglobalpermission">
<?php
		echo(sfc_create_nonce('forum-adminform_globalpermissionnew'));
		sfa_paint_open_tab(__("Forums", "sforum")." - ".__("Add Global Permission Set", "sforum"));
			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Add a User Group Permission Set to All Forums", "sforum"), 'true', 'add-a-user-group-permission-set-to-all-forums', false);
?>
					<table class="form-table">
						<tr>
							<td class="sflabel"><?php sfa_display_usergroup_select(); ?></td>
						</tr><tr>
							<td class="sflabel"><?php sfa_display_permission_select(); ?></td>
						</tr>
					</table>
					<p><?php _e("Caution:  Any current Permission Sets for the selected User Group for ANY Forum may be overwritten.", "sforum") ?></p>
<?php
				sfa_paint_close_fieldset(false);
			sfa_paint_close_panel();
		sfa_paint_close_tab();
?>
		<div class="sfform-submit-bar">
		<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Add Global Permission', 'sforum')); ?>" />
		</div>
	</form>

	<div class="sfform-panel-spacer"></div>
<?php
	return;
}

?>