<?php
/*
Simple:Press
Admin Forums Edit Forum Form
$LastChangedDate: 2010-12-20 08:06:14 -0700 (Mon, 20 Dec 2010) $
$Rev: 5098 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# function to display the edit form information form.  It is hidden until the edit forum link is clicked
function sfa_forums_edit_forum_form($forum_id)
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfforumedit<?php echo $forum_id; ?>').ajaxForm({
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

	$sfpostratings = sf_get_option('sfpostratings');
	global $wpdb, $SFPATHS;

	$forum = $wpdb->get_row("SELECT * FROM ".SFFORUMS." WHERE forum_id=".$forum_id);

	sfa_paint_options_init();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=forums-loader&amp;saveform=editforum";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfforumedit<?php echo $forum->forum_id; ?>" name="sfforumedit<?php echo $forum->forum_id; ?>">
<?php
		echo(sfc_create_nonce('forum-adminform_forumedit'));
		sfa_paint_open_tab(__("Forums", "sforum")." - ".__("Manage Groups and Forums", "sforum"));
			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Edit Forum", "sforum"), 'true', 'edit-forum', false);

				if($forum->parent ? $subforum=true : $subforum=false);
?>
					<input type="hidden" name="forum_id" value="<?php echo($forum->forum_id); ?>" />
					<input type="hidden" name="cgroup_id" value="<?php echo($forum->group_id); ?>" />
					<input type="hidden" name="cforum_name" value="<?php echo(sf_filter_title_display($forum->forum_name)); ?>" />
					<input type="hidden" name="cforum_slug" value="<?php echo(esc_attr($forum->forum_slug)); ?>" />
					<input type="hidden" name="cforum_seq" value="<?php echo($forum->forum_seq); ?>" />
					<input type="hidden" name="cforum_desc" value="<?php echo(sf_filter_text_edit($forum->forum_desc)); ?>" />
					<input type="hidden" name="cforum_status" value="<?php echo($forum->forum_status); ?>" />
					<input type="hidden" name="cforum_tags" value="<?php echo($forum->use_tags); ?>" />
					<input type="hidden" name="cforum_ratings" value="<?php echo($forum->post_ratings); ?>" />
					<input type="hidden" name="cforum_rss_private" value="<?php echo($forum->forum_rss_private); ?>" />
					<input type="hidden" name="cforum_sitemap" value="<?php echo($forum->in_sitemap); ?>" />
					<input type="hidden" name="cforum_icon" value="<?php echo(esc_attr($forum->forum_icon)); ?>" />
					<input type="hidden" name="cforum_topic_status" value="<?php echo($forum->topic_status_set); ?>" />
					<input type="hidden" name="cforum_rss" value="<?php echo($forum->forum_rss); ?>" />
					<input type="hidden" name="cforum_message" value="<?php echo(sf_filter_text_edit($forum->forum_message)); ?>" />

					<table class="form-table">
						<tr>
							<td width="35%" class="sflabel">
								<p><?php _e("Type of Forum", "sforum"); ?>:<br /><br /></p>

<?php							if($subforum ? $checked='' : $checked='checked="checked"'); ?>
								<label for="sfradio1" class="sflabel radio">&nbsp;&nbsp;&nbsp;<?php _e("Standard Forum", "sforum"); ?></label>
								<input type="radio" name="forumtype" id="sfradio1" value="1" <?php echo($checked); ?> onchange="sfjSetForumOptions('forum');" />

<?php							if($subforum ? $checked='checked="checked"' : $checked=''); ?>
								<label for="sfradio2" class="sflabel radio">&nbsp;&nbsp;&nbsp;<?php _e("Sub/Child Forum", "sforum"); ?></label>
								<input type="radio" name="forumtype" id="sfradio2" value="2" <?php echo($checked); ?> onchange="sfjSetForumOptions('subforum');" />

							</td>
<?php
                            $ahahURL = SFHOMEURL."index.php?sf_ahah=forums";
							$target = "fseq";
?>
							<td class="sflabel">

<?php							if($subforum ? $style=' style="display:none"' : $style=' style="display:block"'); ?>
								<div id="groupselect"<?php echo($style); ?>>
									<?php _e("The Group this Forum belongs to", "sforum") ?>:<br /><br />
									<select style="width:190px" class="sfacontrol" name="group_id" onchange="sfjSetForumSequence('edit', 'forum', this, '<?php echo($ahahURL); ?>', '<?php echo($target); ?>');">
										<?php echo(sfa_create_group_select($forum->group_id)); ?>
									</select>
								</div>

<?php							if($subforum ? $style=' style="display:block"' : $style=' style="display:none"'); ?>
								<div id="forumselect"<?php echo($style); ?>>
									<?php _e("Parent Forum this Subforum belongs to", "sforum") ?>:<br /><br />
									<select style="width:190px" class="sfacontrol" name="forum_parent" onchange="sfjSetForumSequence('edit', 'subforum', this, '<?php echo($ahahURL); ?>', '<?php echo($target); ?>');">
										<?php echo(sfa_create_group_forum_select($forum->group_id, $forum->forum_id, $forum->parent)); ?>
									</select>
								</div>

							</td>
						</tr>
					</table>
					<br />

					<?php
					$target='thisforumslug';
					$ahahURL = SFHOMEURL."index.php?sf_ahah=forums";
					?>

					<table class="form-table">
						<tr>
							<td class="sflabel"><?php if($subforum ? _e("Subforum Name", "sforum") : _e("Forum Name", "sforum")) ?>:</td>
							<td><input type="text" class=" sfpostcontrol" size="45" name="forum_name" value="<?php echo(sf_filter_title_display($forum->forum_name)); ?>" /></td>
						</tr><tr>
							<td class="sflabel"><?php _e("Forum Slug", "sforum") ?>:</td>
							<td><input type="text" class=" sfpostcontrol" size="45" id="thisforumslug" name="thisforumslug" value="<?php echo (esc_attr($forum->forum_slug)); ?>" onchange="sfjSetForumSlug(this, '<?php echo($ahahURL); ?>', '<?php echo($target); ?>', 'edit');" /></td>
						</tr><tr>
							<td class="sflabel"><?php _e("Description", "sforum") ?>:&nbsp;&nbsp;</td>
							<td><input type="text" class=" sfpostcontrol" size="85" name="forum_desc" value="<?php echo(sf_filter_text_edit($forum->forum_desc)); ?>" /></td>
						</tr>

						<tr id="fsequence">
							<td class="sflabel"><?php _e("Display Position", "sforum") ?>:</td>
							<td id='fseq'>

<?php						if($subforum)
							{
								echo sfa_edit_forum_sequence_options('edit', 'subforum', $forum->forum_id, $forum->forum_seq);
							} else {
								echo sfa_edit_forum_sequence_options('edit', 'forum', $forum->group_id, $forum->forum_seq);
							}
?>

							</td>
						</tr>
					</table><br />

					<table class="form-table">
						<tr>
							<td class="sflabel"><label for="sfforum_status_<?php echo($forum->forum_id); ?>"><?php _e("Locked", "sforum") ?></label>
							<input type="checkbox" id="sfforum_status_<?php echo($forum->forum_id); ?>" name="forum_status"
							<?php if ($forum->forum_status == TRUE) {?> checked="checked" <?php } ?> /></td>
						</tr><tr>
							<td class="sflabel"><label for="sfforum_tags_<?php echo($forum->forum_id); ?>"><?php _e("Enable Tags on this Forum", "sforum") ?></label>
							<input type="checkbox" id="sfforum_tags_<?php echo($forum->forum_id); ?>" name="forum_tags"
							<?php if ($forum->use_tags == TRUE) {?> checked="checked" <?php } ?> /></td>
						<?php if ($sfpostratings['sfpostratings']) { ?>
						</tr><tr>
							<td class="sflabel"><label for="sfforum_ratings_<?php echo($forum->forum_id); ?>"><?php _e("Enable Post Ratings on this Forum", "sforum") ?></label>
							<input type="checkbox" id="sfforum_ratings_<?php echo($forum->forum_id); ?>" name="forum_ratings"
							<?php if ($forum->post_ratings == TRUE) {?> checked="checked" <?php } ?> /></td>
						<?php } ?>
						</tr><tr>
							<td class="sflabel"><label for="sfforum_private_<?php echo($forum->forum_id); ?>"><?php _e("Disable Forum RSS Feed (Feed will not be generated)", "sforum") ?></label>
							<input type="checkbox" id="sfforum_private_<?php echo($forum->forum_id); ?>" name="forum_private"
								<?php if ($forum->forum_rss_private == TRUE) {?> checked="checked" <?php } ?> /></td>
						</tr><tr>
							<td class="sflabel"><label for="sfforum_sitemap_<?php echo($forum->forum_id); ?>"><?php _e("Include this Forum in sitemap", "sforum") ?><br />(<?php _e("Requires XML Sitemap Generator for WordPress plugin", "sforum") ?>)</label>
							<input type="checkbox" id="sfforum_sitemap_<?php echo($forum->forum_id); ?>" name="forum_sitemap"
								<?php if ($forum->in_sitemap == TRUE) {?> checked="checked" <?php } ?> /></td>
						</tr>
					<table><br />

					<table class="form-table">
						<tr>
							<td class="sflabel"><?php _e('Custom Icon', 'sforum') ?>:<br /><?php _e("Custom Icons can be Uploaded on the Components - Custom Icons Panel (50 char filename limit)", "sforum"); ?></td>
							<td>
								<?php sfa_select_icon_dropdown('forum_icon', __("Select Icon", "sforum"), SF_STORE_DIR.'/'.$SFPATHS['custom-icons'].'/', $forum->forum_icon); ?>
							</td>
						</tr><tr>
							<td class="sflabel"><?php _e('Assign a Topic Status Set to Forum', 'sforum') ?>:</td>
							<td><?php echo sfa_create_topic_status_select($forum->topic_status_set); ?></td>
						</tr><tr>
							<td class="sflabel"><?php _e('Replacement External RSS URL', 'sforum') ?>:<br /><?php _e("Default", "sforum"); ?>: <strong><?php echo sf_build_qurl('forum='.$forum->forum_slug, 'xfeed=forum'); ?></strong></td>
							<td><input class="sfpostcontrol" type="text" name="forum_rss" size="45" value="<?php echo(sf_filter_url_display($forum->forum_rss)); ?>" /></td>
						</tr><tr>
							<td class="sflabel"><?php _e('Special Forum Message to be displayed above topics', 'sforum') ?>:</td>
							<td><textarea class="sfpostcontrol" cols="65" rows="3" name="forum_message"><?php echo(sf_filter_text_edit($forum->forum_message)); ?></textarea></td>
						</tr>
					</table>
<?php
				sfa_paint_close_fieldset(false);
			sfa_paint_close_panel();
		sfa_paint_close_tab();
?>
		<div class="sfform-submit-bar">
		<input type="submit" class="sfform-panel-button" id="sfforumedit<?php echo $forum->forum_id; ?>" name="sfforumedit<?php echo $forum->forum_id; ?>" value="<?php esc_attr_e(__('Update Forum', 'sforum')); ?>" />
		<input type="button" class="sfform-panel-button" onclick="javascript:jQuery('#forum-<?php echo $forum->forum_id; ?>').html('');" id="sfforumedit<?php echo $forum->forum_id; ?>" name="editforumcancel<?php echo $forum->forum_id; ?>" value="<?php esc_attr_e(__('Cancel', 'sforum')); ?>" />
		</div>
	</form>

	<div class="sfform-panel-spacer"></div>
<?php
	return;
}

?>