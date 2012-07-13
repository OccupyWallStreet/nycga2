<?php
/*
Simple:Press
Admin Forums Forum Permission Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# function to display the current forum permission set.  It is hidden until the permission set link is clicked.
# additional forms to add, edit or delete these permission set are further hidden belwo the permission set information
function sfa_forums_view_forums_permission_form($forum_id)
{
	global $wpdb;

	$forum = $wpdb->get_row("SELECT * FROM ".SFFORUMS." WHERE forum_id=".$forum_id);

	sfa_paint_options_init();
	sfa_paint_open_tab(__("Forums", "sforum")." - ".__("Manage Groups and Forums", "sforum"));
		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("View Forum Permissions", "sforum"), false, '', false);
				$perms = sfa_get_forum_permissions($forum->forum_id);
				if ($perms)
				{
?>
					<table class="sfmaintable" cellpadding="5" cellspacing="3">
						<tr>
							<td align="center" colspan="3"><strong><?php _e("Current Permission Set For Forum ", "sforum"); echo sf_filter_title_display($forum->forum_name); ?></strong></td>
						</tr>
<?php
					foreach ($perms as $perm)
					{
						$usergroup = sfa_get_usergroups_row($perm->usergroup_id);
						$role = sfa_get_role_row($perm->permission_role);
?>
						<tr>
							<td class="sflabel"><?php echo(sf_filter_title_display($usergroup->usergroup_name)); ?> => <?php echo(sf_filter_title_display($role->role_name)); ?></td>
							<td align="center">
<?php
                                $base = SFHOMEURL."index.php?sf_ahah=forums-loader";
								$target = "curperm-".$perm->permission_id;
								$image = SFADMINIMAGES;
?>
								<input type="button" class="button button-highlighted" value="<?php echo splice(esc_attr(__("Edit Permission Set", "sforum")),0,0); ?>" onclick="sfjLoadForm('editperm', '<?php echo($base); ?>', '<?php echo($target); ?>', '<?php echo($image); ?>', '<?php echo($perm->permission_id); ?>');" />
								<input type="button" class="button button-highlighted" value="<?php echo splice(esc_attr(__("Delete Permission Set", "sforum")),0,0); ?>" onclick="sfjLoadForm('delperm', '<?php echo($base); ?>', '<?php echo($target); ?>', '<?php echo($image); ?>', '<?php echo($perm->permission_id); ?>');" />
							</td>
			   			</tr>
						<tr> <!-- This row will hold hidden forms for the current forum permission set -->
						  	<td class="sfinline-form" colspan="3">
								<div id="curperm-<?php echo $perm->permission_id; ?>">
							</td>
						</tr>
					<?php } ?>
				<?php } else { ?>
					<table class="sfmaintable" cellpadding="5" cellspacing="3">
						<tr>
							<td>
								<?php _e("No Permission Sets for any User Group", "sforum"); ?>
							</td>
						</tr>
				<?php } ?>
			   			<tr>
			   				<td colspan="3" align="center">
<?php
                                $base = SFHOMEURL."index.php?sf_ahah=forums-loader";
								$target = "newperm-".$forum->forum_id;
								$image = SFADMINIMAGES;
?>
								<input type="button" class="button button-highlighted" value="<?php echo splice(esc_attr(__("Add Permission", "sforum")),0,0); ?>" onclick="sfjLoadForm('addperm', '<?php echo($base); ?>', '<?php echo($target); ?>', '<?php echo($image); ?>', '<?php echo($forum->forum_id); ?>', 'sfopen');" />
			   				</td>
						</tr>
						<tr> <!-- This row will hold ahah forms for adding a new forum permission set -->
						  	<td class="sfinline-form" colspan="3">
								<div id="newperm-<?php echo $forum->forum_id; ?>">
								</div>
							</td>
						</tr>
					</table>
<?php
			sfa_paint_close_fieldset(false);
		sfa_paint_close_panel();
	sfa_paint_close_tab();
?>
	<form>
		<div class="sfform-submit-bar">
		<input type="button" class="sfform-panel-button" onclick="javascript:jQuery('#forum-<?php echo $forum->forum_id; ?>').html('');" id="sfgroupdel<?php echo $forum->forum_id; ?>" name="forumcancel<?php echo $forum->forum_id; ?>" value="<?php esc_attr_e(__('Cancel', 'sforum')); ?>" />
		</div>
	</form>
	<div class="sfform-panel-spacer"></div>
<?php
}

?>