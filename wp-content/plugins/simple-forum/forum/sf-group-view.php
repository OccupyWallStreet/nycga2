<?php
/*
Simple:Press
Group View Display
$LastChangedDate: 2011-01-03 14:05:54 -0700 (Mon, 03 Jan 2011) $
$Rev: 5248 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

function sf_render_group()
{
	global $sfvars, $current_user, $sfglobals, $session_groups, $subforums, $subforumids;

	$out='';

	# Check if group id is passed in query var (url)
	if(isset($_GET['group']))
	{
		$groupid = sf_esc_int($_GET['group']);
		if(!sf_group_exists($groupid))
		{
			$sfvars['error'] = true;
			update_sfnotice('sfmessage', '1@'.sprintf(__('Group %s Not Found', 'sforum'), $groupid));
			$out = sf_render_queued_message();
			$out.= '<div class="sfmessagestrip">'."\n";
			$out.= sprintf(__("There is no such group with ID %s", "sforum"), $groupid)."\n";
			$out.= '</div>'."\n";
			return $out;
		}
	} else {
		$groupid = NULL;
	}

	# Get group records - might be in global after quicklinks
	if(isset($session_groups) && is_null($groupid))
	{
		$groups = $session_groups;
	} else {
		$groups = sf_get_combined_groups_and_forums($groupid);
	}

	# If No Access to anything then return access denied flag
	if($groups[0]['group_id'] == "Access Denied") return 'Access Denied';

	if($groups)
	{
		# grab the complete last post stats
		$posts = array();
		foreach($groups as $group)
		{
			if($group['forums'])
			{
				foreach($group['forums'] as $forum)
				{
					if(!empty($forum['post_id']))
					{
						$posts[]=$forum['post_id'];
					}
				}
			}
		}
		$stats = sf_get_combined_forum_stats($posts, true);

		foreach($groups as $group)
		{
			# Setup group icon
			if(!empty($group['group_icon']))
			{
				$icon = SFCUSTOMURL.$group['group_icon'];
			} else {
				$icon = SFRESOURCES.'group.png';
			}
			$alt = '';

			# Start Display
			# Display group header
			$out.= '<div id="group-'.$group['group_id'].'" class="sfblock">'."\n";
			$out.= '<a id="g'.$group['group_id'].'"></a>'."\n";
			$out.= sf_render_main_header_table('group', $group['group_id'], sf_filter_title_display($group['group_name']), sf_filter_text_display($group['group_desc']), $icon,'','','','','','',$group['forums']);

			# Special Message
			if(!empty($group['group_message']))
			{
				$out.= sf_render_special_group_message(sf_filter_text_display($group['group_message']));
			}

			# Get list of forums in current group
			$out.= '<table class="sfforumtable">'."\n";

			if ($group['forums'])
			{
				$subforums = array();
				$subforumids = array();

				# Display forum column headers
				$out.= sf_render_group_column_header_row();

				foreach ($group['forums'] as $forum)
				{
					if (in_array($forum['forum_id'], $subforumids)) continue;

					$showsubs = false;
					# if forum has subforums build list
					if (($forum['children']) && ($sfglobals['display']['groups']['showsubforums'] || $sfglobals['display']['groups']['combinesubcount']))
					{
						$showsubs = $sfglobals['display']['groups']['showsubforums'];
						$subforums = array();
						$subforumids = array();
						$subforums = sf_build_subforum_list($group['forums'], $forum['forum_id'], 0);

						if($subforums && $sfglobals['display']['groups']['combinesubcount'])
						{
							$stats = sf_massage_forum_counts($forum['forum_id'], $stats, $subforums);
						}
					}

					# Display current forum row if not a subform
					if (!$forum['parent'])
					{
						# if a new forum then there will be no stats
						if (isset($stats[$forum['forum_id']]))
						{
							$forumstats = $stats[$forum['forum_id']];
						} else {
							$forumstats['topic_count'] = 0;
							$forumstats['post_count'] = 0;
						}
						$out.= sf_render_forum_entry_row($forum, $forumstats, $alt);
					}

					# show subs if any to show
					if($showsubs)
					{
						$out.= sf_render_sub_forum_entry_row($subforums, $alt);
					}

					if ($alt == '') $alt = 'sfalt'; else $alt = '';
				}
			} else {
				$out.= '<div class="sfmessagestrip">'.__("There are No Forums defined in this Group", "sforum").'</div>'."\n";
			}
			$out.= '</table>'."\n";
			$out.= '</div>'."\n";
		}
		$out.= sf_process_hook('sf_hook_post_group', array($group['group_id']));
	} else {
		$out.= '<div class="sfmessagestrip">'.__("There are No Groups defined", "sforum").'</div>'."\n";
	}
	return $out;
}

?>