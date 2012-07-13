<?php
/*
Simple:Press
Admin Tags Manage Tags Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

function sfa_tags_manage_tags_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfrenametags').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloadmb').click();
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
	jQuery('#sfdeletetags').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloadmb').click();
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
	jQuery('#sfaddtags').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloadmb').click();
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
	jQuery('#sfcleantags').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloadmb').click();
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});

	/* Register initial event */
	registerTagClick();
	registerAjaxNav();
});
</script>
<?php

	include_once (SF_PLUGIN_DIR.'/admin/panel-tags/support/sfa-tags-support.php');

	global $wpdb;

	sfa_paint_options_init();

	sfa_paint_open_tab(__("Tags", "sforum")." - ".__("Manage Tags", "sforum"));
		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Manage Tags", "sforum"), true, 'manage-tags', false);
				# some URL settings
				$baseurl = SFADMINTAGS.'&amp;form=managetags';
				$sort_order = (isset($_GET['tag_sortorder'])) ? sf_esc_str($_GET['tag_sortorder']) : 'desc';
				$search_url = (isset($_GET['search'])) ? '&amp;search=' . sf_esc_str($_GET['search']) : '';
				$page = '';
				if (isset($_GET['page'])) $page = intval($_GET['page']);
				$action_url = $baseurl.$page.'&amp;tag_sortorder='.$sort_order.$search_url;

				# possible ordering types
				$order_array = array(
					'desc' => __('Most popular', 'sforum'),
					'asc' => __('Least used', 'sforum'),
					'natural' => __('Alphabetical', 'sforum'));

				# get search terms
				if (!empty($_GET['search']))
				{
					$search = sf_esc_str($_GET['search']);
				} else {
					$search = '';
				}
?>
				<div class="wrap tag_wrap">
					<table>
						<tr>
							<td colspan="2">
								<form action="<?php echo(SFADMINTAGS); ?>" method="get">
									<label for="search"><?php _e('Search tags', 'sforum'); ?></label><br />
									<input type="hidden" name="page" value="simple-forum/admin/panel-tags/sfa-tags.php" />
									<input type="hidden" name="form" value="managetags" />
									<input type="hidden" name="tag_sortorder" value="<?php echo $sort_order; ?>" />
									<input class="sfpostcontrol" style="width:180px;" type="text" name="search" id="search" value="<?php echo esc_attr($search); ?>" />
									<input class="button button-highlighted" type="submit" value="<?php esc_attr_e(__('Go', 'sforum')); ?>" />
								</form>

							</td>
						</tr>
						<tr>
							<td class="list_tags">
								<fieldset class="options" id="taglist">
									<div class="sort_order">
										<h3><?php _e('Sort Order:', 'sforum'); ?></h3>
	<?php
										$output = array();
										foreach ($order_array as $sort => $title)
										{
											$output[] = ($sort == $sort_order) ? '<span style="color: red;">'.$title.'</span>' : '<a href="'.$baseurl.'&amp;tag_sortorder='.$sort.$search_url.'">'.$title.'</a>';
										}
										echo implode('<br />', $output);
	?>
									</div>

									<div id="tagslist">
										<ul>
<?php
											$tags = sfa_database_get_tags($sort_order, $search, 0);
											foreach ($tags['tags'] as $tag)
											{
												echo '<li><span>'.$tag->tag_name.'</span>&nbsp;('.$tag->tag_count.')</li>'."\n";
											}
?>
										</ul>

										<?php if (empty($_GET['search']) && $tags['count'] > SFMANAGETAGSNUM) : ?>
											<div class="navigation">
												<a href="<?php echo SFHOMEURL."index.php?sf_ahah=admin-tags&pagination=1&amp;order=".$sort_order; ?>"><?php _e('Previous tags', 'sforum'); ?></a> | <?php _e('Next tags', 'sforum'); ?>
											</div>
										<?php endif; ?>
									</div>
								</fieldset>
							</td>

							<td class="forms_manage">
								<h3 style="padding-top:10px;"><?php _e('Rename Tag', 'sforum'); ?>:</h3>
<?php
                                $ahahURL = SFHOMEURL."index.php?sf_ahah=tags-loader&amp;saveform=renametags";
?>
								<form action="<?php echo($ahahURL); ?>" method="post" id="sfrenametags" name="sfrenametags">
									<?php echo sfc_create_nonce('forum-adminform_sfrenametags'); ?>
									<input type="hidden" name="tag_sortorder" value="<?php echo $sort_order; ?>" />
									<table class="form-table">
										<tr>
											<td colspan="3">
												<?php _e('Enter the Tag to rename and its new value.  You can use this feature to merge tags too. Click "Rename" and all Topics which use this Tag will be updated.', 'sforum'); ?>
												<br />
												<?php _e('You can specify multiple Tags to rename by separating them with commas.', 'sforum'); ?>
												<p>&nbsp;</p>
											</td>
										</tr>
										<tr>
											<th style="vertical-align:middle" scope="row"><label for="renametag_old"><?php _e('Tag(s) to Rename:', 'sforum'); ?></label></th>
											<td width="10"></td>
											<td><input class="sfpostcontrol" style="width:240px;" type="text" id="renametag_old" name="renametag_old" value="" /></td>
										</tr>
										<tr>
											<th style="vertical-align:middle" scope="row"><label for="renametag_new"><?php _e('New Tag Name(s):', 'sforum'); ?></label></th>
											<td width="10"></td>
											<td>
												<input class="sfpostcontrol" style="width:240px;" type="text" id="renametag_new" name="renametag_new" value="" />
												<input class="button button-highlighted" type="submit" name="rename" value="<?php esc_attr_e(__('Rename', 'sforum')); ?>" />
											</td>
										</tr>
									</table>
								</form>

								<div class="sfform-panel-spacer"></div>
								<h3><?php _e('Delete Tag', 'sforum'); ?>:</h3>
<?php
                                $ahahURL = SFHOMEURL."index.php?sf_ahah=tags-loader&amp;saveform=deletetags";
?>
								<form action="<?php echo($ahahURL); ?>" method="post" id="sfdeletetags" name="sfdeletetags">
									<?php echo sfc_create_nonce('forum-adminform_sfdeletetags'); ?>
									<input type="hidden" name="tag_sortorder" value="<?php echo $sort_order; ?>" />
									<table class="form-table">
										<tr>
											<td colspan="3">
												<?php _e('Enter the name of the Tag to delete.  This Tag will be removed from all Topics.', 'sforum'); ?>
												<br />
												<?php _e('You can specify multiple Tags to delete by separating them with commas', 'sforum'); ?>.
												<p>&nbsp;</p>
											</td>
										</tr>
										<tr>
											<th style="vertical-align:middle" scope="row"><label for="deletetag_name"><?php _e('Tag(s) to Delete:', 'sforum'); ?></label></th>
											<td width="10"></td>
											<td>
												<input class="sfpostcontrol" style="width:240px;" type="text" id="deletetag_name" name="deletetag_name" value="" />
												<input class="button button-highlighted" type="submit" name="delete" value="<?php esc_attr_e(__('Delete', 'sforum')); ?>" />
											</td>
										</tr>
									</table>
								</form>

								<div class="sfform-panel-spacer"></div>
								<h3><?php _e('Add Tag', 'sforum'); ?>:</h3>
<?php
                                $ahahURL = SFHOMEURL."index.php?sf_ahah=tags-loader&amp;saveform=addtags";
?>
								<form action="<?php echo($ahahURL); ?>" method="post" id="sfaddtags" name="sfaddtags">
									<?php echo sfc_create_nonce('forum-adminform_sfaddtags'); ?>
									<input type="hidden" name="tag_sortorder" value="<?php echo $sort_order; ?>" />
									<table class="form-table">
										<tr>
											<td colspan="3">
												<?php _e('This feature lets you add one or more new Tags to all Topics which match any of the Tags given.', 'sforum'); ?>
												<br />
												<?php _e('You can specify multiple Tags to add by separating them with commas.  If you want the Tag(s) to be added to all Topics, then don\'t specify any Tags to match.', 'sforum'); ?>
												<br />
												<?php _e('The Tags being added will be subject to the maximum Tags limit you have specified in the Forum options.', 'sforum'); ?>
												<p>&nbsp;</p>
											</td>
										</tr>
										<tr>
											<th style="vertical-align:middle" scope="row"><label for="addtag_match"><?php _e('Tag(s) to Match:', 'sforum'); ?></label></th>
											<td width="10"></td>
											<td><input class="sfpostcontrol" style="width:240px;" type="text" id="addtag_match" name="addtag_match" value="" /></td>
										</tr>
										<tr>
											<th style="vertical-align:middle" scope="row"><label for="addtag_new"><?php _e('Tag(s) to Add:', 'sforum'); ?></label></th>
											<td width="10"></td>
											<td>
												<input class="sfpostcontrol" style="width:240px;" type="text" id="addtag_new" name="addtag_new" value="" />
												<input class="button button-highlighted" type="submit" name="Add" value="<?php _e('Add', 'sforum'); ?>" />
											</td>
										</tr>
									</table>
								</form>

								<div class="sfform-panel-spacer"></div>
								<h3><?php _e('Clean Up Tags', 'sforum'); ?>:</h3>
<?php
                                $ahahURL = SFHOMEURL."index.php?sf_ahah=tags-loader&amp;saveform=cleanup";
?>
								<form action="<?php echo($ahahURL); ?>" method="post" id="sfcleantags" name="sfcleantags">
									<?php echo sfc_create_nonce('forum-adminform_sfcleanup'); ?>
									<input type="hidden" name="tag_sortorder" value="<?php echo $sort_order; ?>" />
									<table class="form-table">
										<tr>
											<td colspan="3">
												<?php _e('This feature lets you clean up your Tags database.  This will be useful should some Tags become orphaned from Topics', 'sforum'); ?>
												<p>&nbsp;</p>
											</td>
										</tr>
										<tr>
											<td colspan="3"><input class="button button-highlighted" type="submit" name="Clean" value="<?php esc_attr_e(__('Clean Up', 'sforum')); ?>" /></td>
										</tr>
									</table>
								</form>
							</td>
						</tr>
					</table>
				</div>

				<div class="sfform-panel-spacer"></div>
<?php
			sfa_paint_close_fieldset(false);
		sfa_paint_close_panel();
	sfa_paint_close_tab();

	return;
}

?>