<?php
/*
Simple:Press
Admin Users Members Form
$LastChangedDate: 2011-04-29 18:47:22 -0700 (Fri, 29 Apr 2011) $
$Rev: 6003 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_users_members_form()
{
	sfa_paint_options_init();

	sfa_paint_open_tab(__("Users", "sforum")." - ".__("Member Information", "sforum"));
		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Members Info", "sforum"), 'true', 'users-info', false);
				if (isset($_POST['usersearch'])) $term = sf_filter_title_save(trim($_POST['usersearch'])); else $term = '';
				if (isset($_GET['userspage'])) $page = sf_esc_int($_GET['userspage']); else $page = '';
				$user_search = new SP_User_Search($term, $page);
?>
				<form id="posts-filter" name="searchfilter" action="<?php echo SFADMINUSER.'&amp;form=members'; ?>" method="post">
					<div class="tablenav">
						<?php if ( $user_search->results_are_paged() ) : ?>
							<div class="tablenav-pages">
<?php
								$args = array();
								if( ! empty($user_search->search_term) )
								{
									$args['usersearch'] = urlencode($user_search->search_term);
								}
								$user_search->paging_text = paginate_links( array(
									'total' => ceil($user_search->total_users_for_query / $user_search->users_per_page),
									'current' => $user_search->page,
									'base' => 'admin.php?page=simple-forum/admin/panel-users/sfa-users.php&amp;form=members&amp;%_%',
									'format' => 'userspage=%#%',
									'add_args' => $args) );
								echo $user_search->page_links();
?>
							</div>
						<?php endif; ?>
						<div>
							<label class="hidden" for="post-search-input"><?php esc_attr_e(__('Search Members', 'sforum')); ?>:</label>
							<input type="text" class="sfacontrol" id="post-search-input" name="usersearch" value="<?php echo esc_attr($user_search->search_term); ?>" />
							<input type="button" class="sfform-panel-button" onclick="javascript:document.searchfilter.submit();" id="sfusersearch" name="sfusersearch" value="<?php esc_attr_e(__('Search Members', 'sforum')); ?>" />
			 			</div>
						<br class="clear" />
					</div>
					<br class="clear" />
				</form>
				<?php if ( $user_search->get_results() ) : ?>
					<?php if ( $user_search->is_search() ) : ?>
						<p><a href="<?php echo SFADMINUSER; ?>"><?php _e('&laquo; Back to All Members', 'sforum'); ?></a></p>
					<?php endif; ?>
					<table class="sfsubtable">
						<thead>
							<tr class="thead">
								<th width="10"><?php _e('ID', 'sforum'); ?></th>
								<th><?php _e('Username', 'sforum') ?></th>
								<th align="center"><?php _e('First Post', 'sforum') ?></th>
								<th align="center"><?php _e('Last Post', 'sforum') ?></th>
								<th align="center" class="num"><?php _e('Posts', 'sforum') ?></th>
								<th align="center"><?php _e('Last Visit', 'sforum') ?></th>
								<th><?php _e('Memberships', 'sforum') ?></th>
								<th><?php _e('Rank', 'sforum') ?></th>
								<th align="center" style="width:0"><?php _e('Actions', 'sforum') ?></th>
							</tr>
						</thead>
						<tbody id="users" class="list:user user-list">
<?php
							$style = '';
							foreach ($user_search->get_results() as $userid)
							{
								$data = sfa_get_members_info($userid);
?>
								<tr>
									<td><?php echo($userid); ?></td>
									<td><strong><?php echo sf_filter_name_display($data['display_name']); ?></strong></td>
									<td align="center"><?php echo $data['first']; ?></td>
									<td align="center"><?php echo $data['last']; ?></td>
									<td align="center">
<?php
                                        if ($data['posts'] == -1)
                                        {
                                            echo '<img style="vertical-align:top" src="'.SFADMINIMAGES.'userflag.png" title="'.esc_attr(__("User has not yet visited forum", "sforum")).'" alt="" />';
                                        } else {
                                            echo $data['posts'];
                                        }
?>
                                    </td>
									<td align="center"><?php echo sf_date('d', $data['lastvisit']); ?></td>
									<td><?php echo $data['memberships']; ?></td>
									<td><?php echo $data['rank']; ?></td>
									<td align="center">
										<table>
											<tr>
												<td>
<?php
													$param['forum'] = 'all';
													$param['value'] = $userid;
													$param['type'] = 8;
													$param['search'] = 1;
													$url = add_query_arg($param, SFURL);
													$url = sf_filter_wp_ampersand($url);
													echo '<a href="'.$url.'"><img src="'.SFRESOURCES.'topics-posted-in.png" title="'.esc_attr(__("List Topics User Has Posted In", "sforum")).'" alt="" /></a>';

?>
												</td>
												<td>
<?php
                                                    $site = SFHOMEURL."index.php?sf_ahah=profile&u=".$userid."&amp;show=inline";
													echo '<a href="'.$site.'" onclick="return hs.htmlExpand(this, { objectType: \'ajax\', preserveContent: true, width: 600} )"><img src="'.SFRESOURCES.'user.png" title="'.esc_attr(__("View Member Profile", "sforum")).'" alt="" /></a>';

?>

												</td>
											</tr>
										</table>
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>

					<div class="tablenav">
						<?php if ( $user_search->results_are_paged() ) : ?>
							<div class="tablenav-pages"><?php $user_search->page_links(); ?></div>
						<?php endif; ?>
						<br class="clear" />
					</div>
				<?php endif; ?>
<?php
			sfa_paint_close_fieldset(false);
		sfa_paint_close_panel();
	sfa_paint_close_tab();
}

function sfa_get_members_info($userid)
{
	global $wpdb;

	$data = sf_get_member_row($userid);

	$first = $wpdb->get_row("
			SELECT ".SFPOSTS.".forum_id, forum_name, forum_slug, ".SFPOSTS.".topic_id, topic_name, topic_slug, post_date
			FROM ".SFPOSTS."
			JOIN ".SFTOPICS." ON ".SFTOPICS.".topic_id = ".SFPOSTS.".topic_id
			JOIN ".SFFORUMS." ON ".SFFORUMS.".forum_id = ".SFPOSTS.".forum_id
			WHERE ".SFPOSTS.".user_id=$userid
			ORDER BY post_date ASC
			LIMIT 1");
	if ($first)
	{
		$url = '<a href="'.sf_build_url($first->forum_slug, $first->topic_slug, 1, 0).'">'.sf_filter_title_display($first->topic_name).'</a>';
		$data['first'] = sf_filter_title_display($first->forum_name).'<br />'.$url .'<br />'.sf_date('d', $first->post_date);
	} else {
		$data['first'] = __('No Posts', 'sforum');
	}

	$last = $wpdb->get_row("
			SELECT ".SFPOSTS.".forum_id, forum_name, forum_slug, ".SFPOSTS.".topic_id, topic_name, topic_slug, post_date
			FROM ".SFPOSTS."
			JOIN ".SFTOPICS." ON ".SFTOPICS.".topic_id = ".SFPOSTS.".topic_id
			JOIN ".SFFORUMS." ON ".SFFORUMS.".forum_id = ".SFPOSTS.".forum_id
			WHERE ".SFPOSTS.".user_id=$userid
			ORDER BY post_date DESC
			LIMIT 1");
	if ($last)
	{
		$url = '<a href="'.sf_build_url($last->forum_slug, $last->topic_slug, 1, 0).'">'.sf_filter_title_display($last->topic_name).'</a>';
		$data['last'] = sf_filter_title_display($last->forum_name).'<br />'.$url .'<br />'.sf_date('d', $last->post_date);
	} else {
		$data['last'] = __('No Posts', 'sforum');
	}

	if ($data['admin'])
	{
		$user_memberships = 'Admin';
		$status = 'admin';
		$start = 0;
	} else {
		$status = 'user';
		$start = 1;
	}

	$memberships = $wpdb->get_results("SELECT usergroup_id FROM ".SFMEMBERSHIPS." WHERE user_id=".$userid, ARRAY_A);
	if ($memberships)
	{
		foreach ($memberships as $membership)
		{
			$name = $wpdb->get_var("SELECT usergroup_name FROM ".SFUSERGROUPS." WHERE usergroup_id=".$membership['usergroup_id']);
			if ($start)
			{
				$user_memberships = $name;
				$start = 0;
			} else {
				$user_memberships.= ', '.$name;
			}
		}
	} else if ($start) {
		$user_memberships = 'No Memberships';
	}
	$data['memberships'] = $user_memberships;
	$data['rank'] = sf_render_usertype($status, $userid, $data['posts']);
	return $data;
}

?>