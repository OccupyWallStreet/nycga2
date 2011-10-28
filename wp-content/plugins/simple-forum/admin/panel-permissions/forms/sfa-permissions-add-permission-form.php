<?php
/*
Simple:Press
Admin Permissions Add Permission Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_permissions_add_permission_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfrolenew').ajaxForm({
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

	sfa_paint_options_init();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=permissions-loader&amp;saveform=addperm";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfrolenew" name="sfrolenew">
<?php
		echo(sfc_create_nonce('forum-adminform_rolenew'));
		sfa_paint_open_tab(__("Permissions", "sforum")." - ".__("Add New Permission", "sforum"));
			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Add New Permission", "sforum"), 'true', 'create-new-permission-set', false);
?>
					<table class="form-table">
						<tr>
							<td class="sflabel"><?php _e("Permission Set Name", "sforum") ?>:&nbsp;&nbsp;<br />
							<input type="text" class="sfpostcontrol" size="45" name="role_name" value="" /></td>
							<td class="sflabel"><?php _e("Permission Set Description", "sforum") ?>:&nbsp;&nbsp;<br/>
							<input type="text" class="sfpostcontrol" size="85" name="role_desc" value="" /></td>
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
							$ptype = $sfactions["members"][$index];

							if ($sfactions["members"][$index] != 0) {
								$span = '';
							} else {
								$span = ' colspan="2" ';
							}

							if ($thisrow == 0)
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

									<label for="sf<?php echo $button; ?>" class="sflabel">
									<img align="middle" style="border: 0pt none ; margin: -3px 5px 5px 3px;" class="vtip" title="<?php echo $tooltips[$action]; ?>" src="<?php echo(SFADMINIMAGES); ?>information.png" alt="" />
									<?php _e($action, "sforum"); ?></label>
									<input type="checkbox" name="<?php echo $button; ?>" id="sf<?php echo $button; ?>"  />
									<?php if ($span == '')
									{ ?>
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
								$thisrow = 0;
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
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Create New Permission', 'sforum')); ?>" />
	</div>
	</form>

	<div class="sfform-panel-spacer"></div>
<?php
	return;
}

?>