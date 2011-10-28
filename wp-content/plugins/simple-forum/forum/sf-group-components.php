<?php
/*
Simple:Press
Group Rendering Routines (Forums)
$LastChangedDate: 2010-04-25 14:55:12 -0700 (Sun, 25 Apr 2010) $
$Rev: 3963 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# = COLUMN HEADER ROW =========================
if(!function_exists('sf_render_group_column_header_row')):
function sf_render_group_column_header_row()
{
	global $sfglobals;

	$out = '<tr><th colspan="2">'.__("Forums", "sforum").'</th>'."\n";
	if($sfglobals['display']['forums']['lastcol']) $out.= '<th>'.__("Last Post", "sforum").'</th>'."\n";
	if($sfglobals['display']['forums']['topiccol']) $out.= '<th>'.__("Topics", "sforum").'</th>'."\n";
	if($sfglobals['display']['forums']['postcol']) $out.= '<th>'.__("Posts", "sforum").'</th>'."\n";
	$out.= '</tr>'."\n";
	return $out;
}
endif;

# = FORUM ENTRY ROW ===========================
if(!function_exists('sf_render_forum_entry_row')):
function sf_render_forum_entry_row($forum, $stats, $alt)
{
	global $sfglobals;

	if(!empty($forum['forum_icon']))
	{
		$icon = SFCUSTOMURL.$forum['forum_icon'];
	} else {
		$icon = SFRESOURCES.'forum.png';
	}

	# Forum Icon
	$out = '<tr id="forum-'.$forum['forum_id'].'">'."\n";
	$row = '';
	if ($forum['children'] && $sfglobals['display']['groups']['showsubforums']) $row = ' rowspan="2"';
	$out.= '<td class="sficoncell '.$alt.'"'.$row.'><img src="'.esc_url($icon).'" alt="" /></td>'."\n";

	# Main Forum Title cell
	$out.= '<td class="sfforumitem '.$alt.'">';

	# Forum Status Icons
	if($forum['forum_status'] || $sfglobals['display']['forums']['newposticon'])
	{
		$out.= sf_render_forum_status_icons($forum['forum_status'], $forum['forum_id']);
	}

	# Forum Title
	$out.= '<div class="sfrowtitletop">';
	$out.= '<p>'.sf_render_forum_title($forum['forum_slug'], sf_filter_title_display($forum['forum_name'])).'</p>'."\n";
	$out.= '</div>';

	# Forum Page Links
	if($sfglobals['display']['forums']['pagelinks'])
	{
		$out.= sf_render_forum_pagelinks($forum['forum_slug'], $stats['topic_count']);
	}
	$out.= "\n";

	# Forum Description
	if($sfglobals['display']['forums']['description'])
	{
		$out.= '<div class="sfdescription">'.sf_filter_text_display($forum['forum_desc']).'</div>'."\n";
	}

	# Program Hook
	$out.= sf_process_hook('sf_hook_post_forum', array($forum['forum_id']));

	$out.= '</td>'."\n";

	# Display last post, topic and post counts if required
	if($sfglobals['display']['forums']['lastcol'])
	{
		if($stats && $stats['topic_count'] > 0)
		{
			if (isset($sfglobals['display']['forums']['showtitle']) && $sfglobals['display']['forums']['showtitle'])
            {
                $title = $stats['topic_name'];
            } else {
                $title = '';
            }
			$out.= sf_render_first_last_post_cell($forum['forum_slug'], $stats['topic_slug'], $stats, $alt, $title);
		} else {
			$out.='<td align="center" class="'.$alt.'">-</td>'."\n";
		}
	}
	if($sfglobals['display']['forums']['topiccol'])
	{
		$out.= '<td class="sfcounts '.$alt.'">'.$stats['topic_count'].'</td>'."\n";
	}
	if($sfglobals['display']['forums']['postcol'])
	{
		$out.= '<td class="sfcounts '.$alt.'">'.$stats['post_count'].'</td>'."\n";
	}

	$out.= '</tr>'."\n";

	return $out;
}
endif;

# = RENDER THE FORUM TITLE =============================
if(!function_exists('sf_render_forum_title')):
function sf_render_forum_title($forumslug, $forumname)
{
	$forumname=sf_filter_title_display($forumname);

	$out = '<a href="'.sf_build_url($forumslug, '', 1, 0).'">'.$forumname.'</a>'."\n";
	return $out;
}
endif;

# = RENDER THE FORUM STATUS ICONS ======================
if(!function_exists('sf_render_forum_status_icons')):
function sf_render_forum_status_icons($forumstatus, $forumid)
{
	global $current_user, $sfglobals;

	$out = '';

	$out.= '<table class="sfrowstatus">';
	$out.= '<tr>';

	$td = 0;
	# New Post Icon
	if ($sfglobals['display']['forums']['newposticon'] && $current_user->member && is_array($sfglobals['member']['newposts']['forums']))
	{
		if (in_array($forumid, $sfglobals['member']['newposts']['forums']))
		{
			$out.= '<td><img src="'.SFRESOURCES.'small-post.png" alt="" title="'.esc_attr(__("New Posts", "sforum")).'" /></td>';
			$td = 1;
		}
	}

	# Forum Locked Icon
	if($forumstatus == 1)
	{
		$out.= '<td><img src="'.SFRESOURCES.'small-lock.png" alt="" title="'.esc_attr(__("Forum Locked", "sforum")).'" /></td>';
		$td = 1;
	}

	if ($td == 0) $out.= '<td style="display:none"></td>';
	$out.= '</tr>';
	$out.= '</table>';

	return $out;
}
endif;

# = RENDER INLINE PAGE LINKS ===========================
if(!function_exists('sf_render_forum_pagelinks')):
function sf_render_forum_pagelinks($forumslug, $topiccount)
{
	global $sfglobals;

	$topicpage = $sfglobals['display']['topics']['perpage'];
	if($topicpage >= $topiccount) return '';

	$out='';

	$out.= '<table class="sfrowstatus">';
	$out.= '<tr>';

	$totalpages=($topiccount / $topicpage);
	if(!is_int($totalpages)) $totalpages=intval($totalpages)+1;
	if($totalpages > 4)
	{
		$maxcount=4;
	} else {
		$maxcount=$totalpages;
	}

	for($x = 1; $x <= $maxcount; $x++)
	{
		$out.= '<td class="sfrowpages"><a href="'.sf_build_url($forumslug, '', $x, 0).'">'.$x.'</a></td>'."\n";
	}

	if($totalpages > 4)
	{
		$out.= '<td class="nobg"><img src="'.SFRESOURCES.'pagelink.png" alt="" /></td>';
		$out.= '<td class="sfrowpages"><a href="'.sf_build_url($forumslug, '', $totalpages, 0).'">'.$totalpages.' </a></td>'."\n";
	}

	$out.= '</tr>';
	$out.= '</table>';

	return $out;
}
endif;

# = SPECIAL FORUM MESSAGE =====================
if(!function_exists('sf_render_special_group_message')):
function sf_render_special_group_message($message)
{
	$out = '<div class="sfmessagestrip sfgroupmessage"><p>'.$message.'</p></div>'."\n";
	return $out;
}
endif;


?>