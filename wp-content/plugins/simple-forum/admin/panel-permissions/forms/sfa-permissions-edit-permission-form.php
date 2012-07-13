<?php
/*
Simple:Press
Admin Permissions Edit Permission Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# function to display the edit permission set form.  It is hidden until the edit permission set link is clicked
function sfa_permissions_edit_permission_form($role_id)
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfroleedit<?php echo $role_id; ?>').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloadpb').click();
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
	jQuery(function(jQuery){vtip("<?php echo(SFADMINIMAGES.'vtip_arrow.png'); ?>");})
});
</script>
<?php

	# Get correct tooltips file
	$lang = WPLANG;
	if (empty($lang)) $lang = 'en';
	$ttpath = SFHELP.'admin/tooltips/admin-permissions-tips-'.$lang.'.php';
	if (file_exists($ttpath) == false) $ttpath = SFHELP.'admin/tooltips/admin-permissions-tips-en.php';
	if(file_exists($ttpath))
	{
		include_once($ttpath);
	}

	global $sfactions;

	$role = sfa_get_role_row($role_id);

	sfa_paint_options_init();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=permissions-loader&amp;saveform=editperm";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfroleedit<?php echo $role->role_id; ?>" name="sfroleedit<?php echo $role->role_id; ?>">
<?php
		echo(sfc_create_nonce('forum-adminform_roleedit'));
		sfa_paint_open_tab(__("Permissions", "sforum")." - ".__("Manage Permissions", "sforum"));
			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Edit Permission", "sforum"), 'true', 'edit-master-permission-set', false);
?>
					<input type="hidden" name="role_id" value="<?php echo $role->role_id; ?>" />
					<table class="form-table">
						<tr>
							<td class="sflabel"><?php _e("Permission Set Name", "sforum") ?>:&nbsp;&nbsp;<br />
							<input type="text" class="sfpostcontrol" size="45" name="role_name" value="<?php echo sf_filter_title_display($role->role_name); ?>" /></td>
							<td class="sflabel"><?php _e("Permission Set Description", "sforum") ?>:&nbsp;&nbsp;<br/>
							<input type="text" class="sfpostcontrol" size="85" name="role_desc" value="<?php echo sf_filter_title_display($role->role_desc); ?>" /></td>
						</tr>
					</table>

					<br /><p><strong><?php _e("Permission Set Actions", "sforum") ?>:</strong></p>
					<?php
					echo '<p><img src="'.SFADMINIMAGES.'guestperm.png" alt="" width="16" height="16" align="top" />';
					echo '<small>&nbsp;'.__("Note: Action settings displaying this icon will be ignored for Guest Users", "sforum").'</small>';
					echo '&nbsp;&nbsp;&nbsp;<img src="'.SFADMINIMAGES.'globalperm.png" alt="" width="16" height="16" align="top" />';
					echo '<small>&nbsp;'.__("Note: Action settings displaying this icon require enabling to use", "sforum").'</small></p>';
					?>

					<table class="outershell" width="100%" border="0" cellspacing="3">
					<tr>
<?php
						$actions = maybe_unserialize($role->role_actions);
						$items = count($sfactions['action']);
						$cols = 3;
						$rows  = ($items / $cols);
						$lastrow = $rows;
						$lastcol = $cols;
						$curcol = 0;
						if (!is_int($rows))
						{
							$rows = (intval($rows) + 1);
							$lastrow = $rows - 1;
							$lastcol = ($items % $cols);
						}
						$thisrow = 0;

						foreach ($sfactions["action"] as $index => $action)
						{
							$button = 'b-'.$index;
							$checked="";
							if (isset($actions[$action]) && $actions[$action] == 1)
							{
								$checked= ' checked="checked"';
							}
							$ptype = $sfactions["members"][$index];
							if($sfactions["members"][$index] != 0) {
								$span = '';
							} else {
								$span = ' colspan="2" ';
							}

							if($thisrow == 0)
							{
								$curcol++;
?>
								<td width="33%" style="vertical-align:top">
								<table class="form-table">
<?php
							}
?>
								<tr>
									<td class="sflabel"<?php echo($span); ?>>

									<label for="sfR<?php echo $role->role_id.$button; ?>" class="sflabel">
									<img align="middle" style="border: 0pt none ; margin: -3px 5px 5px 3px;" class="vtip" title="<?php echo $tooltips[$action]; ?>" src="<?php echo(SFADMINIMAGES); ?>information.png" alt="" />
									<?php _e($action, "sforum"); ?></label>
									<input type="checkbox" name="<?php echo $button; ?>" id="sfR<?php echo $role->role_id.$button; ?>"<?php echo($checked); ?>  />
									<?php if ($span == '') { ?>
									<td align="center">
									<?php } ?>
<?php
									if ($span == '') {
										if($ptype == 2)
										{
											echo '<img src="'.SFADMINIMAGES.'globalperm.png" alt="" width="16" height="16" title="'.esc_attr(__("Requires Enabling", "sforum")).'" />';
										}
										echo '<img src="'.SFADMINIMAGES.'guestperm.png" alt="" width="16" height="16" title="'.esc_attr(__("Ignored for Guests", "sforum")).'" />';
										echo '</td>';
									}
									?>
								</tr>
<?php
							$thisrow++;
							if (($curcol <= $lastcol && $thisrow == $rows) || ($curcol > $lastcol && $thisrow == $lastrow))
							{
?>
								</table>
								</td>
<?php
								$thisrow=0;
							}
						} ?>
						</tr>
					</table>
<?php
				sfa_paint_close_fieldset(false);
			sfa_paint_close_panel();
		sfa_paint_close_tab();
?>
		<div class="sfform-submit-bar">
		<input type="submit" class="sfform-panel-button" id="sfpermedit<?php echo $role->role_id; ?>" name="sfpermedit<?php echo $role->role_id; ?>" value="<?php esc_attr_e(__('Update Permission', 'sforum')); ?>" />
		<input type="button" class="sfform-panel-button" onclick="javascript:jQuery('#perm-<?php echo $role->role_id; ?>').html('');" id="sfpermedit<?php echo $role->role_id; ?>" name="editpermcancel<?php echo $role->role_id; ?>" value="<?php esc_attr_e(__('Cancel', 'sforum')); ?>" />
		</div>
		</form>

	<div class="sfform-panel-spacer"></div>
<?php
	return;
}

?>