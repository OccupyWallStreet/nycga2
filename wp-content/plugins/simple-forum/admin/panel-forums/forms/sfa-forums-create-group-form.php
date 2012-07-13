<?php
/*
Simple:Press
Admin Forums Create Group Form
$LastChangedDate: 2010-12-20 08:06:14 -0700 (Mon, 20 Dec 2010) $
$Rev: 5098 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# function to display the create new group form. It is hidden until user clicks on the create new group link
function sfa_forums_create_group_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfgroupnew').ajaxForm({
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
	global $wpdb, $SFPATHS;

	sfa_paint_options_init();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=forums-loader&amp;saveform=creategroup";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfgroupnew" name="sfgroupnew">
<?php
		echo(sfc_create_nonce('forum-adminform_groupnew'));
		sfa_paint_open_tab(__("Forums", "sforum")." - ".__("Create New Group", "sforum"));
			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Create New Group", "sforum"), 'true', 'create-new-forum-group', false);
?>
					<table class="form-table">
						<tr>
							<td class="sflabel"><?php _e("Group Name", "sforum") ?>:</td>
							<td><input type="text" class="sfpostcontrol" size="45" name="group_name" value="" /></td>
						</tr><tr>
							<td class="sflabel"><?php _e("Description", "sforum") ?>:&nbsp;</td>
							<td><input type="text" class="sfpostcontrol" size="85" name="group_desc" value="" /></td>
						</tr><tr>
<?php
							echo sfa_group_sequence_options('new', 0);
?>
						</tr><tr>
							<td class="sflabel"><?php _e('Custom Icon', 'sforum') ?>:<br /><?php _e("Custom Icons can be Uploaded on the Components - Custom Icons Panel (50 char filename limit)", "sforum"); ?></td>
							<td>
								<?php sfa_select_icon_dropdown('group_icon', __("Select Icon", "sforum"), SF_STORE_DIR.'/'.$SFPATHS['custom-icons'].'/', ''); ?>
							</td>
						</tr><tr>
							<td class="sflabel"><?php _e('Special Group Message to be displayed above forums', 'sforum') ?>:</td>
							<td><textarea class="sfpostcontrol" cols="65" rows="3" name="group_message"></textarea></td>
						</tr>
					</table>
					<div class="clearboth"></div>
					<br /><br />
					<strong><?php _e("Set Default User Group Permission Sets", "sforum") ?></strong>
					<br /><br />
					<?php _e("Note - This will not change or define any current permissions. It's only a default setting for forums that get created in this Group. You will have the chance to explicitly set each permission when creating a forum in this group.", "sforum") ?>
					<table class="form-table">
						<?php
						$usergroups = sfa_get_usergroups_all();
						foreach ($usergroups as $usergroup)
						{
						?>
						<tr>
							<td width="50%" class="sflabel">
                                <?php echo(sf_filter_title_display($usergroup->usergroup_name)); ?>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="hidden" name="usergroup_id[]" value="<?php echo($usergroup->usergroup_id); ?>" />
                            </td>
							<?php $roles = sfa_get_all_roles(); ?>
							<td width="50%"><select style="width:165px" class='sfacontrol' name='role[]'>
<?php
								$out = '';
								$out = '<option value="-1">'.__("Select Permission Set", "sforum").'</option>';
								foreach($roles as $role)
								{
									$out.='<option value="'.$role->role_id.'">'.sf_filter_title_display($role->role_name).'</option>'."\n";
								}
								echo $out;
?>
								</select>
							</td>
						</tr>
						<?php } ?>
					</table>
<?php
				sfa_paint_close_fieldset(false);
			sfa_paint_close_panel();
		sfa_paint_close_tab();
?>
		<div class="sfform-submit-bar">
		<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Create New Group', 'sforum')); ?>" />
		</div>
		</form>

	<div class="sfform-panel-spacer"></div>
<?php
	return;
}

?>