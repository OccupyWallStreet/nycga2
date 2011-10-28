<?php
/*
Simple:Press
Admin Users PM Stats Form
$LastChangedDate: 2010-10-15 17:38:17 -0700 (Fri, 15 Oct 2010) $
$Rev: 4762 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_users_pm_stats_form()
{
	global $wpdb;

	sfa_paint_options_init();

	sfa_paint_open_tab(__("Users", "sforum")." - ".__("Member PM Stats", "sforum"));
		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Member PM Stats", "sforum"), 'true', 'users-pm-stats', false);
				if (isset($_POST['pmsearch'])) $term = sf_filter_title_save(trim($_POST['pmsearch'])); else $term = '';
				if (isset($_GET['userspage'])) $page = sf_esc_int($_GET['userspage']); else $page = '';
				$pm_search = new SP_User_Search($term, $page);
?>
				<form id="pmsearch-filter" name="pmsearchfilter" action="<?php echo SFADMINUSER.'&amp;form=pmstats'; ?>" method="post">
					<div class="tablenav">
						<?php if ( $pm_search->results_are_paged() ) : ?>
							<div class="tablenav-pages">
								<?php
								$args = array();
								if( ! empty($pm_search->search_term) )
								{
									$args['usersearch'] = urlencode($pm_search->search_term);
								}
								$pm_search->paging_text = paginate_links( array(
									'total' => ceil($pm_search->total_users_for_query / $pm_search->users_per_page),
									'current' => $pm_search->page,
									'base' => 'admin.php?page=simple-forum/admin/panel-users/sfa-users.php&amp;form=pmstats&amp;%_%',
									'format' => 'userspage=%#%',
									'add_args' => $args) );
								echo $pm_search->page_links();
?>
							</div>
						<?php endif; ?>
						<div>
							<label class="hidden" for="pm-search-input"><?php _e('Search Members', 'sforum'); ?>:</label>
							<input type="text" class="sfacontrol" id="pm-search-input" name="pmsearch" value="<?php echo esc_attr($pm_search->search_term); ?>" />
							<input type="button" class="sfform-panel-button" onclick="javascript:document.pmsearchfilter.submit();" id="sfpmsearch" name="sfpmsearch" value="<?php esc_attr_e(__('Search Members', 'sforum')); ?>" />
			 			</div>
						<br class="clear" />
					</div>
					<br class="clear" />
				</form>

				<?php if ( $pm_search->get_results() ) : ?>
					<?php if ( $pm_search->is_search() ) : ?>
						<p><a href="<?php echo SFADMINUSER; ?>"><?php _e('&laquo; Back to All Members', 'sforum'); ?></a></p>
					<?php endif; ?>

					<table align="center" class="sfsubtable" cellpadding="0" cellspacing="0">
						<tr>
							<th align="center" width="90" scope="col" style="padding:5px 0px;"><?php _e("User ID", "sforum"); ?></th>
							<th style="padding:2px 0px;"><?php _e("User Name", "sforum"); ?></th>
							<th align="center" width="20" scope="col" style="padding:5px 0px;"></th>
							<th align="center" width="50" scope="col" style="padding:5px 0px;"><?php _e("Can PM", "sforum") ?></th>
							<th align="center" width="20" scope="col" style="padding:5px 0px;"></th>
							<th align="center" width="90" scope="col" style="padding:5px 0px;"><?php _e("Total PMs", "sforum") ?></th>
							<th align="center" width="90" scope="col" style="padding:5px 0px;"><?php _e("Inbox PMs", "sforum") ?></th>
							<th align="center" width="90" scope="col" style="padding:5px 0px;"><?php _e("Unread PMs", "sforum") ?></th>
							<th align="center" width="90" scope="col" style="padding:5px 0px;"><?php _e("Sentbox PMs", "sforum") ?></th>
							<th align="center" width="20" scope="col" style="padding:5px 0px;"></th>
							<th align="center" width="80" scope="col" style="padding:5px 0px;"><?php _e("Manage PMs", "sforum") ?></th>
							<th align="center" width="20" scope="col" style="padding:5px 0px;"></th>
						</tr>
<?php
						if ($pm_search)
						{
							foreach ($pm_search->get_results() as $userid)
							{
								$pmdata = sfa_get_user_pm_data($userid);
								if (!isset($pmdata[$userid]))
								{
									$data = sf_get_member_row($userid);
									$pmdata[$userid]['id'] = $userid;
									$pmdata[$userid]['name'] = sf_filter_name_display($data['display_name']);
									$pmdata[$userid]['pm'] = $data['pm'];
									$pmdata[$userid]['inbox'] = 0;
									$pmdata[$userid]['unread'] = 0;
									$pmdata[$userid]['sentbox'] = 0;
								}
?>
							<tr>
								<td colspan="12" style="border-bottom:0px;padding:0px;">
									<div id="pmdata<?php echo $userid; ?>">
										<table width="100%" cellspacing="0">
											<tr>
												<td align="center" width="90" style="padding:5px 0px;"><?php echo $userid; ?></td>
												<td style="padding:2px 0px;"><?php echo esc_html($pmdata[$userid]['name']); ?></td>
												<td align="center" width="20" style="padding:5px 0px;"></td>
												<td align="center" width="50" style="padding:5px 0px;"><?php if ($pmdata[$userid]['pm']) echo __("Yes", "sforum"); else echo __("No", "sforum"); ?></td>
												<td align="center" width="20" style="padding:5px 0px;"></td>
												<td align="center" width="90" style="padding:5px 0px;"><?php echo ($pmdata[$userid]['inbox'] + $pmdata[$userid]['sentbox']); ?></td>
												<td align="center" width="90" style="padding:5px 0px;"><?php echo $pmdata[$userid]['inbox']; ?></td>
												<td align="center" width="90" style="padding:5px 0px;"><?php echo $pmdata[$userid]['unread']; ?></td>
												<td align="center" width="90" style="padding:5px 0px;"><?php echo $pmdata[$userid]['sentbox']; ?></td>
												<td align="center" width="20" style="padding:5px 0px;"></td>
												<td align="center" width="80" style="padding:5px 0px;">
													<?php if ($pmdata[$userid]['inbox'] > 0 || $pmdata[$userid]['sentbox'] > 0)
													{ ?>
                                                        <?php $site = SFHOMEURL."index.php?sf_ahah=user&action=del_inbox&amp;id=".$pmdata[$userid]['id']."&amp;name=".$pmdata[$userid]['name']."&amp;pm=".$pmdata[$userid]['pm']."&amp;inbox=".$pmdata[$userid]['inbox']."&amp;unread=".$pmdata[$userid]['unread']."&amp;sentbox=".$pmdata[$userid]['sentbox']."&amp;eid=".$userid; ?>
														<?php $gif = SFADMINIMAGES."working.gif"; ?>
														<img onclick="sfjDelPMs('<?php echo $site; ?>', '<?php echo $gif; ?>', '0', 'pmdata<?php echo $userid; ?>');" src="<?php echo SFADMINIMAGES; ?>inbox_pm.png" title="<?php _e("Delete Inbox PMs", "sforum"); ?>" alt="" />&nbsp;
                                                        <?php $site = SFHOMEURL."index.php?sf_ahah=user&action=del_sentbox&amp;id=".$pmdata[$userid]['id']."&amp;name=".$pmdata[$userid]['name']."&amp;pm=".$pmdata[$userid]['pm']."&amp;inbox=".$pmdata[$userid]['inbox']."&amp;unread=".$pmdata[$userid]['unread']."&amp;sentbox=".$pmdata[$userid]['sentbox']."&amp;eid=".$userid; ?>
														<img onclick="sfjDelPMs('<?php echo $site; ?>', '<?php echo $gif; ?>', '0', 'pmdata<?php echo $userid; ?>');" src="<?php echo SFADMINIMAGES; ?>sentbox_pm.png" title="<?php _e("Delete Sentbox PMs", "sforum"); ?>" alt="" />&nbsp;
                                                        <?php $site = SFHOMEURL."index.php?sf_ahah=user&action=del_pms&amp;id=".$pmdata[$userid]['id']."&amp;name=".$pmdata[$userid]['name']."&amp;pm=".$pmdata[$userid]['pm']."&amp;inbox=".$pmdata[$userid]['inbox']."&amp;unread=".$pmdata[$userid]['unread']."&amp;sentbox=".$pmdata[$userid]['sentbox']."&amp;eid=".$userid; ?>
														<img onclick="sfjDelPMs('<?php echo $site; ?>', '<?php echo $gif; ?>', '0', 'pmdata<?php echo $userid; ?>');" src="<?php echo SFADMINIMAGES; ?>all_pm.png" title="<?php _e("Delete All PMs", "sforum"); ?>" alt=""/>
													<?php } ?>
												</td>
												<td align="center" width="20" style="padding:5px 0px;"></td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
							<?php } ?>
							<tr style="height:50px">
								<td colspan="12" style="padding-left:10px;padding-right:10px;"><?php echo __("Note: Removing all PMs for a user may result in invalid data being displayed for other users until the page is refreshed.", "sforum"); ?></td>
							</tr>
						<?php } else { ?>
						<tr style="height:50px">
							<td colspan="12" style="padding-left:10px;padding-right:10px;"><?php echo __("There currently are not any stored PMs.", "sforum"); ?></td>
						</tr>
						<?php }?>
					</table>
					<div class="tablenav">
						<?php if ( $pm_search->results_are_paged() ) : ?>
							<div class="tablenav-pages"><?php $pm_search->page_links(); ?></div>
						<?php endif; ?>
						<br class="clear" />
					</div>
				<?php endif; ?>
<?php
			sfa_paint_close_fieldset(false);
		sfa_paint_close_panel();
	sfa_paint_close_tab();
?>
	<div class="sfform-panel-spacer"></div>
<?php
}

function sfa_get_user_pm_data($userid)
{
	global $wpdb;

	$records = array();
	$users = $wpdb->get_results("
			SELECT user_id, display_name, pm, to_id, from_id, message_status, inbox, sentbox
			FROM ".SFMEMBERS."
			JOIN ".SFMESSAGES." ON (".SFMEMBERS.".user_id = ".SFMESSAGES.".from_id OR ".SFMEMBERS.".user_id = ".SFMESSAGES.".to_id)
			WHERE (inbox=1 OR sentbox=1) AND (to_id=".$userid." OR from_id=".$userid.")
			ORDER BY display_name ASC");

	if ($users)
	{
		foreach ($users as $user)
		{
			if (($user->user_id == $user->from_id && $user->sentbox == 1) ||
			    ($user->user_id == $user->to_id && $user->inbox == 1))
			{
				if (!isset($records[$user->user_id]))
				{
					$records[$user->user_id]['unread'] = 0;
					$records[$user->user_id]['inbox'] = 0;
					$records[$user->user_id]['sentbox'] = 0;
					$first = 0;
				}
				$records[$user->user_id]['id'] = $user->user_id;
				$records[$user->user_id]['name'] = $user->display_name;
				$records[$user->user_id]['pm'] = $user->pm;
				if ($user->to_id == $user->user_id && $user->message_status == 0) $records[$user->user_id]['unread']++;
				if ($user->to_id == $user->user_id && $user->inbox == 1) $records[$user->user_id]['inbox']++;
				if ($user->from_id == $user->user_id && $user->sentbox == 1) $records[$user->user_id]['sentbox']++;
			}
		}
	}

	return $records;
}

?>