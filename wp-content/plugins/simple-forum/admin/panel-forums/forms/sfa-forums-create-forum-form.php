<?php
/*
Simple:Press
Admin Forums Create Forum Form
$LastChangedDate: 2010-12-20 08:06:14 -0700 (Mon, 20 Dec 2010) $
$Rev: 5098 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# function to display the create new forum forum.  It is hidden until the create new forum link is clicked
function sfa_forums_create_forum_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfforumnew').ajaxForm({
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

	$sfpostratings = sf_get_option('sfpostratings');

	sf_initialise_globals();

	sfa_paint_options_init();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=forums-loader&amp;saveform=createforum";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfforumnew" name="sfforumnew">
<?php
		echo(sfc_create_nonce('forum-adminform_forumnew'));
		sfa_paint_open_tab(__("Forums", "sforum")." - ".__("Create New Forum", "sforum"));
			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Create New Forum", "sforum"), 'true', 'create-new-forum', false);

					# check there are groups before proceeding
					if($wpdb->get_var("SELECT COUNT(group_id) FROM ".SFGROUPS) == 0)
					{
						echo '<br /><div class="sfoptionerror">';
						echo __("There are No Groups defined", "sforum");
						echo '<br />'.__("Create New Group", "sforum");
						echo '</div><br />';
						sfa_paint_close_fieldset(false);
						sfa_paint_close_panel();
						sfa_paint_close_tab();
						return;
					}
?>

					<table class="form-table">
						<tr>
							<td width="35%" class="sflabel">
								<p><?php _e("What Type of Forum are you Creating", "sforum"); ?>:<br /><br /></p>
								<label for="sfradio1" class="sflabel radio">&nbsp;&nbsp;&nbsp;<?php esc_attr_e(__("Standard Forum", "sforum")); ?></label>
								<input type="radio" name="forumtype" id="sfradio1" value="1" checked="checked" onchange="sfjSetForumOptions('forum');" />

<?php							# check there are forums before offering subforum creation!
								if($wpdb->get_var("SELECT COUNT(forum_id) FROM ".SFFORUMS) != 0)
								{
?>
									<label for="sfradio2" class="sflabel radio">&nbsp;&nbsp;&nbsp;<?php esc_attr_e(__("Sub/Child Forum", "sforum")); ?></label>
									<input type="radio" name="forumtype" id="sfradio2" value="2" onchange="sfjSetForumOptions('subforum');" />
<?php							}
?>
							</td>
<?php
                            $ahahURL = SFHOMEURL."index.php?sf_ahah=forums";
							$target = "fseq";
?>
							<td class="sflabel">
								<div id="groupselect" style="display:block;">
									<?php _e("Select Group New Forum will belong to", "sforum") ?>:<br /><br />
									<select style="width:190px" class="sfacontrol" name="group_id" onchange="sfjSetForumSequence('new', 'forum', this, '<?php echo($ahahURL); ?>', '<?php echo($target); ?>');">
										<?php echo(sfa_create_group_select()); ?>
									</select>
								</div>
								<div id="forumselect" style="display:none;">
									<?php _e("Select Forum New Subforum will belong to", "sforum") ?>:<br /><br />
									<select style="width:190px" class="sfacontrol" name="forum_id" onchange="sfjSetForumSequence('new', 'subforum', this, '<?php echo($ahahURL); ?>', '<?php echo($target); ?>');">
										<?php echo(sf_render_group_forum_select(false, false, false, true)); ?>
									</select>
								</div>
							</td>
						</tr>
					</table>
					<br />
					<table class="form-table sfhidden" id="block1">
						<tr>
							<?php
							$target='thisforumslug';
							$ahahURL = SFHOMEURL."index.php?sf_ahah=forums";
							?>

							<td class="sflabel"><?php _e("Forum Name", "sforum") ?>:</td>
							<td><input type="text" class="sfpostcontrol" size="45" name="forum_name" value="" onchange="sfjSetForumSlug(this, '<?php echo($ahahURL); ?>', '<?php echo($target); ?>');" /></td>
						</tr><tr>
							<td class="sflabel"><?php _e("Forum Slug", "sforum") ?>:&nbsp;&nbsp;</td>
							<td><input id="thisforumslug" type="text" class="sfpostcontrol" size="45" name="thisforumslug" value="" disabled="disabled" onchange="sfjSetForumSlug(this, '<?php echo($ahahURL); ?>', '<?php echo($target); ?>', 'new');" /></td>
						</tr><tr>
							<td class="sflabel"><?php _e("Description", "sforum") ?>:&nbsp;&nbsp;</td>
							<td><input type="text" class="sfpostcontrol" size="85" name="forum_desc" value="" /></td>
						</tr>
						<tr id="fsequence">
							<td class="sflabel"><?php _e("Display Position", "sforum") ?>:</td>
							<td id='fseq'></td>
						</tr>
						<tr>
							<td class="sflabel" colspan="2"><label for="sfforum_status"><?php _e("Locked", "sforum") ?></label></td>
							<td><input type="checkbox" id="sfforum_status" name="forum_status" /></td>
						</tr><tr>
							<td class="sflabel" colspan="2"><label for="sfforum_tags"><?php _e("Enable Tags on this Forum", "sforum") ?></label></td>
							<td><input type="checkbox" checked="checked" id="sfforum_tags" name="forum_tags" /></td>
						<?php if ($sfpostratings['sfpostratings']) { ?>
						</tr><tr>
							<td class="sflabel" colspan="2"><label for="sfforum_ratings"><?php _e("Enable Post Ratings on this Forum", "sforum") ?></label></td>
							<td><input type="checkbox" checked="checked" id="sfforum_ratings" name="sfforum_ratings" /></td>
						<?php } ?>
						</tr><tr>
							<td class="sflabel" colspan="2"><label for="sfforum_private"><?php _e("Disable Forum RSS Feed (Feed will not be generated)", "sforum") ?></label></td>
							<td><input type="checkbox" id="sfforum_private" name="forum_private" /></td>
						</tr><tr>
							<td class="sflabel" colspan="2"><label for="sfforum_sitemap"><?php _e("Include this Forum in sitemap", "sforum") ?><br />(<?php _e("Requires XML Sitemap Generator for WordPress plugin", "sforum") ?>)</label></td>
							<td><input type="checkbox" checked="checked" id="sfforum_sitemap" name="forum_sitemap" /></td>
						</tr><tr>
							<td class="sflabel"><?php _e('Custom Icon', 'sforum') ?>:<br /><?php _e("Custom Icons can be Uploaded on the Components - Custom Icons Panel (50 char filename limit)", "sforum"); ?></td>
							<td>
								<?php sfa_select_icon_dropdown('forum_icon', __("Select Icon", "sforum"), SF_STORE_DIR.'/'.$SFPATHS['custom-icons'].'/', ''); ?>
							</td>
						</tr><tr>
							<td class="sflabel"><?php _e('Assign a Topic Status Set to Forum', 'sforum') ?>:</td>
							<td><?php echo sfa_create_topic_status_select(0); ?></td>
						</tr><tr>
							<td class="sflabel"><?php _e('Special Forum Message to be displayed above topics', 'sforum') ?>:</td>
							<td><textarea class="sfpostcontrol" cols="65" rows="3" name="forum_message"></textarea></td>
						</tr>
					</table>
					<br /><br />
<?php
					$usergroups = sfa_get_usergroups_all();
					if ($usergroups) {
?>
						<div id="block2" class="sfhidden">
						<?php _e("Add User Group Permission Sets", "sforum") ?>
						<br /><br />
						<?php _e("You can selectively set the permission sets for the forum below.  If you want to use the default permissions for the selected group, then don't select anything.", "sforum") ?>
						<table class="form-table">
						<?php foreach ($usergroups as $usergroup) { ?>
							<tr>
								<td class="sflabel"><?php echo(sf_filter_title_display($usergroup->usergroup_name)); ?>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input type="hidden" name="usergroup_id[]" value="<?php echo($usergroup->usergroup_id); ?>" /></td>
								<?php $roles = sfa_get_all_roles(); ?>
								<td class="sflabel"><select style="width:165px" class='sfacontrol' name='role[]'>
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
						</table><br />
					<?php } ?>
					</div>
					<div class="clearboth"></div>
<?php
				sfa_paint_close_fieldset(false);
			sfa_paint_close_panel();
		sfa_paint_close_tab();
?>
		<div class="sfform-submit-bar">
		<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Create New Forum', 'sforum')); ?>" />
		</div>
	</form>

	<div class="sfform-panel-spacer"></div>
<?php
	return;
}

?>