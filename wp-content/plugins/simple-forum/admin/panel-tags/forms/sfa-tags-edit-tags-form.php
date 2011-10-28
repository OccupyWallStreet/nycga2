<?php
/*
Simple:Press
Admin Tags Edit Tags Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

function sfa_tags_edit_tags_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfedittags').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	include_once (SF_PLUGIN_DIR.'/admin/panel-tags/support/sfa-tags-support.php');

	global $wpdb, $wp_locale;

	sfa_paint_options_init();

	sfa_paint_open_tab(__("Tags", "sforum")." - ".__("Mass Edit Tags", "sforum"));
		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Mass Edit Tags", "sforum"), true, 'mass-edit-tags', false);
				if (!isset($_GET['paged']))
				{
					$_GET['paged'] = 1;
				}

				$topics_per_page = 20;
				if (isset($_GET['topics_per_page'])) $topics_per_page = sf_esc_int($_GET['topics_per_page']);

				# get the topics and tags to display
				$month = '';
				if (isset($_GET['m'])) $month = sf_esc_str($_GET['m']);
				$forum = '';
				if (isset($_GET['forum'])) $forum = sf_esc_str($_GET['forum']);
				$search = '';
				if (isset($_GET['s'])) $search = $_GET['s'];
				$topics = sfa_database_get_topics(sf_esc_int($_GET['paged']), $topics_per_page, $month, $forum, $search);
?>
				<form id="posts-filter" action="<?php echo SFADMINTAGS.'?form=edittags'; ?>" method="get">
					<input type="hidden" name="page" value="simple-forum/admin/panel-tags/sfa-tags.php" />
					<input type="hidden" name="form" value="edittags" />
					<ul class="subsubsub" style="padding-right:40px">
<?php
						$num_topics = $wpdb->get_var("SELECT count(topic_id) FROM ".SFTOPICS);
						echo '</li><a href="'.SFADMINTAGS.'&amp;form=edittags">'.__("All Topics", "sforum").' ('.$num_topics.')</a></li>';
?>
					</ul>

					<p id="post-search">
						<input class="sfpostcontrol" style="width:275px;" type="text" id="post-search-input" name="s" value="<?php echo $search; ?>" />
						<input type="submit" value="<?php _e('Search Topic Titles', 'sforum' ); ?>" class="button" />
					</p>

					<div class="tablenav">
<?php
						$page_links = paginate_links(array(
							'total' => ceil($topics['count'] / $topics_per_page ),
							'current' => sf_esc_int($_GET['paged']),
							'base' => 'admin.php?page=simple-forum/admin/panel-tags/sfa-tags.php&amp;form=edittags&amp;%_%',
							'format' => 'paged=%#%',
							'add_args' => ''));

						if ( $page_links )
							echo "<div class='tablenav-pages'>$page_links</div>";
						?>

						<div class="sfalignleft">
							<?php
							$arc_query = "SELECT DISTINCT YEAR(topic_date) AS yyear, MONTH(topic_date) AS mmonth FROM ".SFTOPICS." ORDER BY topic_date DESC";
							$arc_result = $wpdb->get_results($arc_query);

							$month_count = count($arc_result);
							if ( $month_count && !( 1 == $month_count && 0 == $arc_result[0]->mmonth ) ) { ?>
								<select name='m' style='font-weight:normal'>
								<option<?php if (empty($_GET['m'])) echo ' selected'; ?> value='0'><?php _e('Show all dates', 'sforum'); ?></option>
								<?php
								foreach ($arc_result as $arc_row) {
									if ( $arc_row->yyear == 0 )
										continue;
									$arc_row->mmonth = zeroise( $arc_row->mmonth, 2 );

									if ( $arc_row->yyear . $arc_row->mmonth == sf_esc_str($_GET['m']))
										$default = ' selected="selected"';
									else
										$default = '';

									echo "<option$default value='$arc_row->yyear$arc_row->mmonth'>";
									echo $wp_locale->get_month($arc_row->mmonth) . " $arc_row->yyear";
									echo "</option>\n";
								}
?>
								</select>
<?php
							}

							$forums = sfa_get_forums_all();
							$selected = '';
							if (empty($_GET['forum'])) $selected=' selected';
							echo '<select name="forum" class="sfcontrol" style="font-weight:normal">';
							echo '<option'.$selected.' value="0">'.__("View all forums", "sforum").'</option>'."\n";
							foreach ($forums as $forum)
							{
								$selected = '';
								if ($_GET['forum'] == $forum->forum_id) $selected=' selected';
								echo '<option'.$selected.' value="'.$forum->forum_id.'">'.sf_filter_title_display($forum->forum_name).'</option>';
							}
							echo '</select>';
?>
							<select name="topics_per_page" id="topics_per_page" style='font-weight:normal'>
								<option <?php if ( !isset($_GET['topics_per_page']) ) echo 'selected="selected"'; ?> value=""><?php _e('Quantity', 'sforum').'&hellip;'; ?></option>
								<option <?php if ( $topics_per_page == 10 ) echo 'selected="selected"'; ?> value="10">10</option>
								<option <?php if ( $topics_per_page == 15 ) echo 'selected="selected"'; ?> value="15">15</option>
								<option <?php if ( $topics_per_page == 20 ) echo 'selected="selected"'; ?> value="20">20</option>
								<option <?php if ( $topics_per_page == 30 ) echo 'selected="selected"'; ?> value="30">30</option>
								<option <?php if ( $topics_per_page == 40 ) echo 'selected="selected"'; ?> value="40">40</option>
								<option <?php if ( $topics_per_page == 50 ) echo 'selected="selected"'; ?> value="50">50</option>
								<option <?php if ( $topics_per_page == 100 ) echo 'selected="selected"'; ?> value="100">100</option>
								<option <?php if ( $topics_per_page == 200 ) echo 'selected="selected"'; ?> value="200">200</option>
							</select>

							<input type="submit" id="filter-submit" value="<?php esc_attr_e(__('Filter', 'sforum')); ?>" class="button-secondary" />
						</div>

						<br style="clear:both;" />
					</div>
				</form>

				<br style="clear:both;" />
<?php
                $ahahURL = SFHOMEURL."index.php?sf_ahah=tags-loader&amp;saveform=edittags";
?>
				<?php if ($topics['count'] > 0) : ?>
					<form action="<?php echo($ahahURL); ?>" method="post" id="sfedittags" name="sfedittags">
						<?php echo sfc_create_nonce('forum-adminform_sfedittags'); ?>
						<table class="form-table">
							<tr>
								<th class="manage-column"><?php _e('Topic Title', 'sforum'); ?></th>
								<th style="text-align:center" class="manage-column"><?php _e('Tags', 'sforum'); ?></th>
							</tr>
<?php
							$x = -1;
							$class = 'alternate';
							foreach ($topics['topic'] as $topic)
							{
								$x++;
								$class = ( $class == 'alternate' ) ? '' : 'alternate';
?>
								<tr valign="top" class="<?php echo $class; ?>">
									<td scope="row">
										<?php echo sf_get_topic_url($topic['forum_slug'], $topic['topic_slug'], sf_filter_title_display($topic['topic_name'])); echo '<br/>('.sf_filter_title_display($topic['forum_name']).')'; ?>
									</td>
									<td>
<?php
										$ttags = '';
										if (isset($topic['tags']['list'])) $ttags = $topic['tags']['list'];
?>
										<input class="tags_input sfpostcontrol" type="text" size="100" name="tags[<?php echo $x; ?>]" value="<?php echo sf_filter_title_display($ttags); ?>" />
										<input type="hidden" name="topic_id[<?php echo $x; ?>]" value="<?php echo $topic['topic_id']; ?>" />
										<input type="hidden" name="tag_id[<?php echo $x; ?>]" value="<?php echo $topic['tags']['ids']; ?>" />
									</td>
								</tr>
							<?php } ?>
						</table>
						<div class="sfform-panel-spacer"></div>
						<div class="sfform-submit-bar">
							<input type="submit" class="sfform-panel-button" id="sfedittags" name="sfedittags" value="<?php esc_attr_e(__('Mass Update Tags', 'sforum')); ?>" />
						</div>
					</form>
				<?php else: ?>
					<p><?php _e('No Topics Match the Search Criteria!', 'sforum'); ?>
				<?php endif; ?>

<?php
			sfa_paint_close_fieldset(false);
		sfa_paint_close_panel();
	sfa_paint_close_tab();

	return;
}

?>