<?php
/*
Simple:Press
List Rendering Routines
$LastChangedDate: 2011-01-03 14:29:28 -0700 (Mon, 03 Jan 2011) $
$Rev: 5253 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# ------------------------------------------------------------------
# sf_render_list()
#
# Main rendering routine for list views
# ------------------------------------------------------------------
function sf_render_list()
{
    global $sfvars;

	# Maybe a membership list
	if ($sfvars['list'] == 'members') return sf_list_membership();
}

# ------------------------------------------------------------------
# sf_list_membership()
#
# Display forum membership list for the current user
# ------------------------------------------------------------------
function sf_list_membership()
{
	global $sfvars, $current_user, $sfglobals, $wpdb;

	# to display member lists must be an admin or a member with display membership lists turned on
	$sfmemberopts = sf_get_option('sfmemberopts');
	if ($sfmemberopts['sfshowmemberlist'] && $current_user->sfmemberlist)
	{
		if (isset($_GET['page'])) $sfvars['page'] = sf_esc_int($_GET['page']);
		$out = '';

		# filtering?
		$search = '';
		if (isset($_POST['msearch'])) $search = sf_esc_str($_POST['msearch']);
		if (isset($_GET['search'])) $search = sf_esc_str($_GET['search']);

		# get the member list data
		$data = sf_get_memberlists($sfvars['page'], $search);

		$memberships = $data->records;
		$count = $data->count;

		# Filtering function
		$out.= '<br />';
		$out.= '<div id="sfpostform" style="display:block">';
		$out.= '<table><tr><td>';
		$out.= '<form action="'.SFMEMBERLIST.'" method="post" name="searchmembers">';
		$out.= '<fieldset style="165px;"><legend>'.__("Search Members List by Name", "sforum").'</legend><br />';
		$out.= '<label for="msearch">'.__("Search String", "sforum").':&nbsp;</label>';
		$out.= '<input type="text" class="sfcontrol sfpostcontrol" tabindex="1" name="msearch" id="msearch" size="30" value="'.esc_attr($search).'" />';
		$out .= '<input type="submit" class="sfcontrol" name="membersearch" id="membersearch" value="'.esc_attr(__("Search Members", "sforum")).'" />';
		$out.= '<a href="'.SFMEMBERLIST.'"><input type="button" class="sfcontrol" name="allmembers" id="allmembers" tabindex="2" value="'.esc_attr(__("All Members", "sforum")).'" /></a>';
		$out.= '<br /><br /><strong>'.__('Wildcard Usage', 'sforum').':</strong><br />%&nbsp;&nbsp;&nbsp;&nbsp;'.__('matches any number of characters', 'sforum').'<br />&nbsp;_&nbsp;&nbsp;&nbsp;&nbsp;'.__('matches exactly one character', 'sforum');
		$out.= '</fieldset></form></td></tr></table></div>';

		$icon = SFRESOURCES.'members-list.png';
		if ($memberships)
		{
			# set up paging
            $out.= '<div class="sfblock">';

			# make sure we have some page links (top/bottom/both)
            $pageoverride = false;
            if($sfglobals['display']['pagelinks']['ptop'] == false && $sfglobals['display']['pagelinks']['pbottom'] == false)
            {
            	$pageoverride = true;
            }

			$thispagelinks = sf_compile_paged_topics('list', 0, $sfvars['page'], false, $search, $count, false, true);

			# Display page links
			$out.= sf_render_topic_pagelinks($thispagelinks, false, false, false, false, $pageoverride);

			# var to track user group change
			$oldug = -1;
			foreach ($memberships as $membership)
			{
				if ($membership['usergroup_id'] != $oldug)
				{
					if ($oldug != -1)
					{
						# except for first new ug, close the previous table and sfblock div
						$out.= '</table>';
						$out.= '</div>';
					}

					# save new ug id
					$oldug = $membership['usergroup_id'];

					# Any members in this user group?
					$emptyug = false;
					if (sf_get_membership_count($membership['usergroup_id']) == 0 && $membership['usergroup_name'] != 'Admin') $emptyug = true;

					# localise of admins
					if($membership['usergroup_name'] == 'Admin') $membership['usergroup_name'] = __("Admin", "sforum");
					if($membership['usergroup_desc'] == 'Forum Administrator') $membership['usergroup_desc'] = __("Forum Administrator", "sforum");

					# put out header table
					$out.= '<div id="members-list'.$oldug.'" class="sfblock">';
					$out.= sf_render_main_header_table('list', $membership['usergroup_id'], sf_filter_title_display($membership['usergroup_name']), sf_filter_text_display($membership['usergroup_desc']), $icon);

                    $sfavatars = sf_get_option('sfavatars');
                    $size = $sfavatars['sfavatarsize'];
					$out.= '<table class="sfforumtable">'."\n";
					$out.= '<tr>';
					$out.= '<th width="'.$size.'"></th>';
					$out.= '<th>'.__("Member", "sforum").'</th>';
					$out.= '<th width="50">'.__("Posts", "sforum").'</th>';
					$out.= '<th width="175">'.__("Last Visit", "sforum").'</th>';
					$out.= '<th width="100">'.__("Rank", "sforum").'</th>';
					$out.= '<th width="150">'.__("Info", "sforum").'</th>';
					$out.= '</tr>';
				}

				# If no members in this user group, output such a message
				if ($emptyug)
				{
					$out.= '<tr>';
					$out.= '<td colspan="6">';
					$out.= __('There are no Members in this User Group.', 'sforum');
					$out.= '</td>';
					$out.= '</tr>';
				} else { # user group has members
 					$status = 'user';
					if (sf_is_forum_admin($membership['user_id'])) $status = 'admin';
					$membership['rank'] = sf_render_usertype($status, $membership['user_id'], $membership['posts']);
					$out.= '<tr id="member-'.$membership['user_id'].'">';
					$out.= '<td>';
            		$out.= sf_render_avatar($status, $membership['user_id'], sf_filter_email_display($membership['user_email']), '');
					$out.= '</td>';
					$out.= '<td>'.sf_filter_name_display($membership['display_name']).'</td>';
					$numposts = 0;
					if ($membership['posts'] > 0) $numposts = $membership['posts'];
					$out.= '<td align="center">'.$numposts.'</td>';
					$out.= '<td align="center">'.sf_date('d', $membership['lastvisit']).'<br />'.sf_date('t', $membership['lastvisit']).'</td>';
					$out.= '<td align="center">'.$membership['rank'].'</td>';

					$out.= '<td align="center">';
					$param['forum'] = 'all';
					$param['value'] = $membership['user_id'];
					$param['type'] = 9;
					$param['search'] = 1;
					$url = add_query_arg($param, SFURL);
					$url = sf_filter_wp_ampersand($url);
					$out.= '<a href="'.esc_url($url).'"><img src="'.SFRESOURCES.'topics-started.png" title="'.esc_attr(__("List Topics User Started", "sforum")).'" alt="" /></a>';

					$param['type'] = 8;
					$url = add_query_arg($param, SFURL);
					$url = sf_filter_wp_ampersand($url);
					$out.= '&nbsp;<a href="'.esc_url($url).'"><img src="'.SFRESOURCES.'topics-posted-in.png" title="'.esc_attr(__("List Topics User Has Posted In", "sforum")).'" alt="" /></a>';

					$user = $wpdb->get_var("SELECT user_login FROM ".SFUSERS." WHERE ID=".$membership['user_id']);
					$url = SFURL.'private-messaging/send/'.urlencode($user).'/';
					$out.= '&nbsp;<a href="'.$url.'"><img src="'.SFRESOURCES.'sendpm-small.png" alt="" title="'.esc_attr(__("Send PM to Member", "sforum")).'" />'.sf_render_icons("Send PM").'</a>';

					# SF profile
					$link = '&nbsp;<img src="'.SFRESOURCES.'user.png" alt="" title="'.esc_attr(__("View Member Profile", "sforum")).'" />';
					$out.= sf_attach_user_profilelink($membership['user_id'], $link)."\n";
					$out.= '</td>';
					$out.= '</tr>';
				}
			}
			# close last table
			$out.= '</table>';
			$out.= '</div>';

			# Display page links
			$out.= sf_render_topic_pagelinks($thispagelinks, true, false, false, false, $pageoverride);
			$out.= '</div>';
		} else {
			$out.= '<div class="sfmessagestrip">'.__("There are no members that matched the criteria!", "sforum").'</div>';
		}
	} else {
		update_sfnotice('sfmessage', '1@'.__('Access Denied', "sforum"));
	}

	return $out;
}

?>