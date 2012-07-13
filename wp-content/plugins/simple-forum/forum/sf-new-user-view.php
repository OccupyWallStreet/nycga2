<?php
/*
Simple:Press
Topic View Display
$LastChangedDate: 2011-01-03 14:29:28 -0700 (Mon, 03 Jan 2011) $
$Rev: 5253 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

function sf_render_new_post_list_user()
{
	global $sfvars, $current_user, $sfglobals;

	$alt = '';
	$out = '';

	if($sfglobals['display']['forums']['newcount'] == 0) $sfglobals['display']['forums']['newcount'] = 6;
	$getposts = $sfglobals['display']['forums']['newcount'];
	if($current_user->member && $sfvars['pageview'] == 'newposts')
	{
		$getposts = count($sfglobals['member']['newposts']['topics']);
	}

	$sfposts = sf_get_users_new_post_list($getposts);

	if($sfposts)
	{
		$sort = $sfglobals['display']['forums']['sortinforum'];

		$sfposts = sf_combined_new_posts_list($sfposts, $sort);

	 	# just return if viewable posts is empty
		if ($sfposts == '') return '';

		# grab the complete first/last post stats
		$newposts = array();
		foreach($sfposts as $sfpost)
		{
			$newposts[$sfpost['topic_id']]=$sfpost['post_index'];
		}
		$stats = sf_get_combined_topic_stats($newposts, $current_user->ID);

		# Display section heading
		if($current_user->member)
		{
			$out.= '<div class="sfmessagestrip">';
			$out.= '<table><tr>';
			$out.= '<td><p class="sfsubhead">'.__("Most Recent Topics With Unread Posts", "sforum").'</p></td>';
			$out.= '<td>'.sf_render_forum_icon_legend().'</td>';
			$out.= '</tr></table>';
			$out.= '</div>'."\n";
		} else {
			$out.= '<div class="sfmessagestrip"><p class="sfsubhead">'.__("Most Recent Posts", "sforum").'</p></div>'."\n";
		}

		$out.= '<div id="users-new" class="sfblock">'."\n";

		$out.= '<table class="sfforumtable">'."\n";
		$out.= '<tr><th colspan="2">'.__("Forum/Topic", "sforum").'</th><th>'.__("Started", "sforum").'</th><th>'.__("Last Post", "sforum").'</th><th>'.__("Posts", "sforum").'</th>'."\n";
		$out.= '</tr>'."\n";

		$fname = '';
		$gname = '';

		foreach($sfposts as $sfpost)
		{
			# Display topic entry - if sorted then display heading if group/forum change
			if($sort && ($gname != $sfpost['group_name'] || $fname != $sfpost['forum_name']))
			{
				$out.= '<tr><td colspan="5">';
				$out.= '<div class="sfmessagestrip"><strong>';
				$out.= $sfpost['group_name'].'<a href="'.sf_build_url($sfpost['forum_slug'], '', 0, 0).'"> <img src="'.SFRESOURCES.'arrowr.png" alt="" /> '.$sfpost['forum_name'].'</a>';
				$out.= '</strong></div>';
				$out.= '</td></tr>';
				$gname = $sfpost['group_name'];
				$fname = $sfpost['forum_name'];
			}

			$out.= '<tr>'."\n";
			$out.= '<td class="sficoncell '.$alt.'">';

			$statkeys = sf_create_stats_keys($stats[$sfpost['topic_id']]);

			$out.= sf_render_topic_icon($sfpost['topic_id'], $stats[$sfpost['topic_id']][$statkeys[1]]['udate'], $stats[$sfpost['topic_id']]['thisuser'], $alt);
			$out.= '</td>';
			$out.= '<td><p>';
			if(!$sort)
			{
				$out.= sf_filter_title_display($sfpost['forum_name']).'<br />'."\n";
			}
			$out.= '<a href="'.sf_build_url($sfpost['forum_slug'], $sfpost['topic_slug'], 0, $stats[$sfpost['topic_id']][$statkeys[1]]['post_id'], $sfpost['post_index']).'">'.sf_filter_title_display($sfpost['topic_name']).'</a></p>'."\n";

			if($sfglobals['display']['topics']['pagelinks'])
			{
				$out.= sf_render_inline_pagelinks($sfpost['forum_slug'], $sfpost['topic_slug'], $sfpost['post_index']);
			}
			$out.= '</td>'."\n";

			# Display first and last post links
			if(isset($statkeys[0]))
			{
				$out.= sf_render_first_last_post_cell($sfpost['forum_slug'], $sfpost['topic_slug'], $stats[$sfpost['topic_id']][$statkeys[0]], $alt);
				$out.= sf_render_first_last_post_cell($sfpost['forum_slug'], $sfpost['topic_slug'], $stats[$sfpost['topic_id']][$statkeys[1]], $alt);
			}

			# Dislay post count
			$out.= '<td class="sfcounts">'.$sfpost['post_index'].'</td>'."\n";
			$out.= '</tr>'."\n";
		}
		$out.= '</table></div>'."\n";

	} else {
		$out.='<div class="sfmessagestrip">'.__("There are No Recent Unread Posts", "sforum").'</div>'."\n";
	}
	return $out;
}

?>