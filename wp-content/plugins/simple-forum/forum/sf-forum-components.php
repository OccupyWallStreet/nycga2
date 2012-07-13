<?php
/*
Simple:Press
Forum Rendering Routines (Topics)
$LastChangedDate: 2011-04-28 20:08:44 -0700 (Thu, 28 Apr 2011) $
$Rev: 5994 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# = SEARCH HEADING TEXT =======================
if(!function_exists('sf_render_search_heading')):
function sf_render_search_heading($search)
{
	# Display search text if in search mode
	if($search)
	{
		return __("Topics Matching Search", "sforum");
	} else {
		return __("Topics", "sforum");
	}
}
endif;

# = CONSTRUCT PAGED TOPIC NAVIGATION TABLE ====
if(!function_exists('sf_compile_paged_topics')):
function sf_compile_paged_topics($forumslug, $forumid, $currentpage, $search, $currentsearchpage, $topiccount, $watch=false, $list=false)
{
	# making minimal changes to this routine at the moment
	# because it has become so intertwined wuth the non-forum specific stuff

	global $sfvars, $sfglobals;

	$tpaged = $sfglobals['display']['topics']['perpage'];
	if(!$tpaged) $tpaged=20;

	if(!isset($currentpage)) $currentpage = 1;
	if(($search) && (!isset($currentsearchpage))) $currentsearchpage = 1;
	$cpage=$currentpage;
	if($search)
	{
		$cpage=$currentsearchpage;
		$searchvalue=urldecode($sfvars['searchvalue']);
	}
	$baseurl = '<a href="'.sf_build_url($forumslug, '', 0, 0);

	if($forumslug == 'all')
	{
		$topiccount = $sfvars['searchresults'];
		$baseurl = '<a href="'.sf_build_qurl('forum='.$forumslug);
	} else if ($forumslug == 'list') {
    	$baseurl = '<a href="'.sf_build_url('members', '', 0, 0);
	} else {
		if($search)
		{
			$topiccount = $sfvars['searchresults'];
			$baseurl = '<a href="'.sf_build_qurl('forum='.$forumslug);
		}
	}
	$totalpages = ($topiccount / $tpaged);
	if(!is_int($totalpages)) $totalpages = (intval($totalpages)+1);

	if($search) $baseurl.= '&amp;value='.urlencode($searchvalue).'&amp;type='.$sfvars['searchtype'].'&amp;include='.$sfvars['searchinclude'];

	$out= __("Page:", "sforum").' ';

	$paging = $sfglobals['display']['topics']['numpagelinks'];
	$out.= sf_pn_next($cpage, $search, $totalpages, $baseurl, $paging, $list);
	if ($search)
	{
		$out.= '&nbsp;&nbsp;' . $baseurl. '&amp;search='.$cpage . '" class="current">'.$cpage.'</a>'. '&nbsp;&nbsp;'."\n";
	} else if ($watch)
	{
		$out.= '&nbsp;&nbsp;' . $baseurl. '&amp;page='.$cpage . '" class="current">'.$cpage.'</a>'. '&nbsp;&nbsp;'."\n";
	} else {
		if ($cpage > 1)
		{
			$out.= '&nbsp;&nbsp;' . trailingslashit($baseurl. 'page-'.$cpage) . '" class="current">'.$cpage.'</a>'. '&nbsp;&nbsp;'."\n";
		} else {
			$out.= '&nbsp;&nbsp;' . $baseurl. '" class="current">'.$cpage.'</a>'. '&nbsp;&nbsp;'."\n";
		}
	}
	$paging = $sfglobals['display']['topics']['numpagelinks'];
	$out.= sf_pn_previous($cpage, $search, $totalpages, $baseurl, $paging, $list);

	return $out;
}
endif;

# = FORUM TOPICS PAGE NAV STRIP ===============
if(!function_exists('sf_render_topic_pagelinks')):
function sf_render_topic_pagelinks($thispagelinks, $bottom, $showadd, $forumlock, $showlegend=true, $override=false)
{
	global $current_user, $sfglobals, $sfvars;

    $out = '';

	if ((!$bottom && $sfglobals['display']['pagelinks']['ptop']) || ($bottom && $sfglobals['display']['pagelinks']['pbottom']) || ($override==true))
	{
		$out.= '<table class="sffooter"><tr>'."\n";
		$out.= '<td class="sfpagelinks">'.$thispagelinks.'</td>'."\n";

		$out.= sf_render_add_topic($bottom, $showadd, $forumlock);

		if (!$bottom && $showlegend)
		{
			$out.= '</tr><tr><td colspan="2" class="sfadditemcell">'.sf_render_forum_icon_legend().'</td>';
		}
		$out.= '</tr></table>'."\n";
	} else {
		# if bottom with no pagelinks but show add button then...
		if($bottom && $sfglobals['display']['pagelinks']['pbottom']==false)
		{
			$out.= '<table class="sffooter"><tr>'."\n";
			$out.= sf_render_add_topic($bottom, $showadd, $forumlock);

			$out.= '</tr></table>'."\n";
		}
	}

	if ($bottom)
	{
		$out.= sf_process_hook('sf_hook_post_forum_bottom_pagelinks', array($sfvars['forumid']));
	} else {
		$out.= sf_process_hook('sf_hook_post_forum_top_pagelinks', array($sfvars['forumid']));
	}

	return $out;
}
endif;

# = FORUM TOPICS PAGE ADD BUTTON ===============
if(!function_exists('sf_render_add_topic')):
function sf_render_add_topic($bottom, $showadd, $forumlock)
{
	global $current_user, $sfglobals;

	$out = '';
	$out.= '<td class="sfadditemcell">'."\n";
	if($showadd)
	{
		if($current_user->offmember)
		{
			$sflogin = sf_get_option('sflogin');
			$out.= '<a class="sficon" href="'.esc_url($sflogin['sfloginurl'].'&amp;redirect_to='.urlencode($_SERVER['REQUEST_URI'])).'"><img src="'.SFRESOURCES.'login.png" alt="" title="'.esc_attr(__("Login", "sforum")).'" />'.sf_render_icons("Login").'</a>'."\n";
		} else {
			if($current_user->sfaddnew)
			{
				$out.= '<a class="sficon" onclick="sfjOpenEditor(\'sfpostform\','.$sfglobals['editor']['sfeditor'].',\'topic\');"><img src="'.SFRESOURCES.'addtopic.png" alt="" title="'.esc_attr(__("Add a New Topic", "sforum")).'" />'.sf_render_icons("Add a New Topic").'</a>'."\n";
			}
		}
	} else {
		if($forumlock)
		{
			$out.= '<img class="sficon" src="'.SFRESOURCES.'locked.png" alt="" title="'.esc_attr(__("Forum Locked", "sforum")).'"/>'.sf_render_icons("Forum Locked")."\n";
		}
	}
	$out.= '</td>';

	return $out;
}
endif;






# = SPECIAL FORUM MESSAGE =====================
if(!function_exists('sf_render_special_forum_message')):
function sf_render_special_forum_message($message)
{
	$out = '<div class="sfmessagestrip sfforummessage"><p>'.$message.'</p></div>'."\n";
	return $out;
}
endif;

# = COLUMN HEADER ROW =========================
if(!function_exists('sf_render_forum_column_header_row')):
function sf_render_forum_column_header_row($coldisplaytext)
{
	global $current_user, $sfglobals;

	$out = '<tr><th colspan="2">'.$coldisplaytext.'</th>'."\n";
	if($sfglobals['display']['topics']['firstcol']) $out.= '<th>'.__("Started", "sforum").'</th>'."\n";
	if($sfglobals['display']['topics']['lastcol']) $out.= '<th>'.__("Last Post", "sforum").'</th>'."\n";
	if($sfglobals['display']['topics']['postcol']) $out.= '<th>'.__("Posts", "sforum").'</th>'."\n";
	if($sfglobals['display']['topics']['viewcol']) $out.= '<th>'.__("Views", "sforum").'</th>'."\n";
	$out.= '</tr>';

	return $out;
}
endif;

# = TOPIC ENTRY ROW ===========================
if(!function_exists('sf_render_topic_entry_row')):
function sf_render_topic_entry_row($forum, $topic, $stats, $value, $alt)
{
	global $current_user, $sfvars, $sfglobals;

	if($topic['topic_pinned'])
	{
		$out = '<tr id="topic-'.$topic['topic_id'].'" class="sfpinnedtopic">'."\n";
	} else {
		$out = '<tr id="topic-'.$topic['topic_id'].'">'."\n";
	}

	# Different icon depending on who and lastvisit etc.
	$out.= '<td class="sficoncell '.$alt.'">';
	$statkeys = sf_create_stats_keys($stats);

	$out.= sf_render_topic_icon($topic['topic_id'], $stats[$statkeys[1]]['udate'], $stats['thisuser'], $alt);

	# Topic ratings
	if ($forum['post_ratings'] && $sfglobals['display']['topics']['postrating'])
	{
		$out.= sf_render_topic_ratings($topic['topic_id']);
	}

	$out.= '</td>';

	if((isset($_POST['topicedit'])) && ($_POST['topicedit'] == $topic['topic_id']))
	{
		$out.= sf_edit_topic_title($topic['topic_id'], sf_filter_title_display($topic['topic_name']), $forum['forum_id']);
	} else {
		# Main Topic Title cell
		$out.= '<td class="sfforumitem '.$alt.'">';

		# Topic Status Icons
		$temp_out = '';  #only output if any icons to show
		if($sfglobals['display']['topics']['statusicons'])
		{
			$temp_out.= sf_render_bloglink_icon($topic['blog_post_id']);
			$temp_out.= sf_render_user_subscribed_icon($topic['topic_subs']);
			$temp_out.= sf_render_user_watching_icon($topic['topic_watches']);
		}
		$temp_out.= sf_render_topic_pinned_icon($topic['topic_pinned']);
		$temp_out.= sf_render_topic_lock_icon($topic['topic_status'], $value['forumlock']);

		# Topic Tools
		if($sfglobals['admin']['sftools'] && ($current_user->sfforumicons || ($current_user->sfeditowntitle && $topic['user_id'] == $current_user->ID)) && !$sfglobals['lockdown'])
		{
			$temp_out.= sf_render_topic_tools_icon($topic['topic_id'], $forum['forum_id'], $sfvars['page']);
		}
		if (!empty($temp_out))
		{
			$out.= '<table class="sfrowstatus">';
			$out.= '<tr>';
			$out.= $temp_out;
			$out.= '</tr>';
			$out.= '</table>';
		}

		# Topic Title
		$out.= '<div class="sfrowtitletop"><p>';

		# Is this a 'watched' or 'subscribed' view?
		if(isset($value['watches']) || isset($value['subscriptions']))
		{
			if ($value['watches'] || $value['subscriptions'])
			{
				$forumname=sf_filter_title_display($forum['forum_name']);
				$out.=$forumname.'<br />';
			}
		}

		$out.= sf_render_topic_title($forum['forum_slug'], $topic['topic_slug'], sf_filter_title_display($topic['topic_name']), $topic['post_tip'], $topic['topic_pinned'])."\n";
		$out.= '</p></div>';

		# Topic Page Links
		if ($sfglobals['display']['topics']['pagelinks'])
		{
			$out.= sf_render_inline_pagelinks($forum['forum_slug'], $topic['topic_slug'], $topic['post_count']);
		}

		# Topic Tags and Topic Status
		if (($sfglobals['display']['topics']['topictags'] && $forum['use_tags']) || ($sfglobals['display']['topics']['topicstatus'] && $topic['topic_status_flag'] != 0))
		{
			$out.= '<div class="sfstatustags">';
			# Topic Status
			if($sfglobals['display']['topics']['topicstatus'] && $topic['topic_status_flag'] != 0)
			{
				$out.= '<div class="sfclearleft"></div>';
				$out.= sf_render_topic_status_block($forum['topic_status_set'], $topic['topic_status_flag'], 'ts-forum'.$topic['topic_id'], 'ts-fview'.$topic['topic_id'], 'left');
			}

			# Topic Tags
			if ($sfglobals['display']['topics']['topictags'] && $forum['use_tags'])
			{
				$out.= '<div class="sfclearleft"></div>';
				$out.= sf_render_topic_tag_block($forum['use_tags'], $topic);
			}
			$out.= '</div>';
		}

		# Program Hook
		$out.= sf_process_hook('sf_hook_post_topic', array($forum['forum_id'], $topic['topic_id']));

		$out.= '</td>'."\n";


		if($sfglobals['display']['topics']['firstcol'])
		{
			if(isset($statkeys[0]))
			{
				$out.= sf_render_first_last_post_cell($forum['forum_slug'], $topic['topic_slug'], $stats[$statkeys[0]], $alt);
			}
		}

		# Display last post column if option set
		if($sfglobals['display']['topics']['lastcol'])
		{
			$out.= sf_render_first_last_post_cell($forum['forum_slug'], $topic['topic_slug'], $stats[$statkeys[1]], $alt);
		}

		# Display post count and views if options set
		if($sfglobals['display']['topics']['postcol']) $out.= '<td class="sfcounts '.$alt.'">'.$topic['post_count'].'</td>'."\n";
		if($sfglobals['display']['topics']['viewcol']) $out.= '<td class="sfcounts '.$alt.'">'.$topic['topic_opened'].'</td>'."\n";

		# Display admin icons if admin and option set
		$out.= '</tr>'."\n";
	}
	return $out;
}
endif;

# = TOPIC ROW SUPPORT: TOPIC ICON =============
if(!function_exists('sf_render_topic_icon')):
function sf_render_topic_icon($topicid, $lastudate, $thisuser, $alt)
{
	global $current_user;

	$icon = 1;
	if($current_user->guest)
	{
		if(($current_user->lastvisit > 0) && ($current_user->lastvisit < $lastudate)) $icon = 2;
	} else {
		if(sf_is_in_users_newposts($topicid)) $icon = 2;
		if($icon == 1 && $thisuser) $icon = 3;
		if($icon == 2 && $thisuser) $icon = 4;
	}
	switch($icon)
	{
		case 1:
			$topicicon = 'topic.png';
			$title = __("Forum Topic", "sforum");
			break;
		case 2:
			$topicicon = 'topicnew.png';
			$title = __("Forum Topic with new Post", "sforum");
			break;
		case 3:
			$topicicon = 'topicuser.png';
			$title = __("Forum Topic with Post from you", "sforum");
			break;
		case 4:
			$topicicon = 'topicnewuser.png';
			$title = __("Forum Topic you've posted in with new Post", "sforum");
			break;
	}
	return '<img src="'.SFRESOURCES.$topicicon.'" alt="" title="'.$title.'" />'."\n";
}
endif;

# = RENDER THE TOPIC TITLE =============================
if(!function_exists('sf_render_topic_title')):
function sf_render_topic_title($forumslug, $topicslug, $topicname, $post_tip, $pinned=false)
{
	global $sfvars;

    $out = '';

	$topicname=sf_filter_title_display($topicname);

	if (isset($sfvars['searchvalue']))
	{
		$out.= '<a title="'.$post_tip.'" href="'.sf_build_url($forumslug, $topicslug, 1, 0);
		if(strpos(SFURL, '?') === false)
		{
			$out.= '?value';
		} else {
			$out.= '&amp;value';
		}
		$out.= '='.$sfvars['searchvalue'].'&amp;type='.$sfvars['searchtype'].'&amp;include='.$sfvars['searchinclude'].'&amp;search='.$sfvars['searchpage'].'">'.$topicname.'</a>'."\n";
	} else {
    	$sfdisplay = sf_get_option('sfdisplay');
        if ($sfdisplay['forums']['pinned'] && $pinned) $out.= __("Pinned", "sforum").': ';
		$out.= '<a title="'.$post_tip.'" href="'.sf_build_url($forumslug, $topicslug, 1, 0).'">'.$topicname.'</a>'."\n";
	}
	return $out;
}
endif;

# = TOPIC ROW SUPPORT: TOPIC LOCK ICON ========
if(!function_exists('sf_render_topic_lock_icon')):
function sf_render_topic_lock_icon($topicstatus, $forumlock)
{
	if(($topicstatus == 1) || ($forumlock))
	{
		return '<td><img src="'.SFRESOURCES.'small-lock.png" alt="" title="'.esc_attr(__("Topic Locked", "sforum")).'" /></td>';
	}
	return;
}
endif;

# = TOPIC ROW SUPPORT: TOPIC PINNED ICON ========
if(!function_exists('sf_render_topic_pinned_icon')):
function sf_render_topic_pinned_icon($topicpinned)
{
	if($topicpinned == 1)
	{
		return '<td><img src="'.SFRESOURCES.'small-pin.png" alt="" title="'.esc_attr(__("Topic Pinned", "sforum")).'" /></td>';
	}
	return;
}
endif;

# = TOPIC ROW SUPPORT: SUBSCRIBED ICON ========
if(!function_exists('sf_render_user_subscribed_icon')):
function sf_render_user_subscribed_icon($subs)
{
	global $current_user;

	if ($current_user->guest) return;

	$out = '';
	if (!empty($subs))
	{
		$sublist = unserialize($subs);
		foreach ($sublist as $i)
		{
			if ($i == $current_user->ID) $out = '<td><img src="'. SFRESOURCES .'small-subscribe.png" alt="" title="'.esc_attr(__("You are subscribed to this topic", "sforum")).'" /></td>';
		}
		if ($current_user->forumadmin) $out = '<td><img src="'. SFRESOURCES .'small-subscribe.png" alt="" title="'.esc_attr(__("This topic has User Subscriptions", "sforum")).'" /></td>';
	}
	return $out;
}
endif;

# = TOPIC ROW SUPPORT: WATCHING ICON ========
if(!function_exists('sf_render_user_watching_icon')):
function sf_render_user_watching_icon($topicwatches)
{
	global $current_user;

	if ($current_user->guest) return;

	$out = '';
	if (!empty($topicwatches))
	{
		$watchlist = unserialize($topicwatches);
		foreach ($watchlist as $watch)
		{
			if ($watch == $current_user->ID) $out = '<td><img src="'. SFRESOURCES .'small-watch.png" alt="" title="'.esc_attr(__("You are watching this topic", "sforum")).'" /></td>';
		}
		if ($current_user->forumadmin) $out = '<td><img src="'. SFRESOURCES .'small-watch.png" alt="" title="'.esc_attr(__("This topic has User Watches", "sforum")).'" /></td>';
	}
	return $out;
}
endif;

# = TOPIC ROW SUPPORT: BLOGLINK ICON ========
if(!function_exists('sf_render_bloglink_icon')):
function sf_render_bloglink_icon($blogpostid)
{
	if($blogpostid != 0)
	{
		return '<td><a href="'.get_permalink($blogpostid).'"><img src="'.SFRESOURCES.'small-link.png" alt="" title="'.esc_attr(__("This topic is Linked to Blog Post", "sforum")).'" /></a></td>';
	}
	return;
}
endif;

if(!function_exists('sf_render_topic_tools_icon')):
function sf_render_topic_tools_icon($topicid, $forumid, $page)
{
    $site = SFHOMEURL."index.php?sf_ahah=adminlinks&action=topictools&topic=".$topicid."&forum=".$forumid."&page=".$page;
	$out = '<td><a rel="nofollow" href="'.$site.'" onclick="return hs.htmlExpand(this, { objectType: \'ajax\', preserveContent: true, width: 280, height: 470, anchor: \'top\'} )"><img class="sfalignright" src="'.SFRESOURCES.'small-tools.png" alt="" title="'.esc_attr(__("Show Edit Tools", "sforum")).'"/></a></td>';

	return $out;
}
endif;

# = TOPIC STATUS BLOCK =======
if(!function_exists('sf_render_topic_status_block')):
function sf_render_topic_status_block($statusset, $statusflag, $boxid, $updateid, $alignment)
{
	$out='';

	$out.= '<div id="sfstatus'.$boxid.'">';
	$out.= sf_render_topic_statusflag($statusset, $statusflag, $boxid, $updateid, $alignment);
	$out.= '</div>';

	return $out;
}
endif;

# = TOPIC TAG BLOCK =======
if(!function_exists('sf_render_topic_tag_block')):
function sf_render_topic_tag_block($showtags, $topic)
{
	$out='';

	$topicid = $topic['topic_id'];
	$out.= '<div id="sftags'.$topicid.'">';
	$out.= sf_render_topic_tags($showtags, $topic);
	$out.= '</div>';

	return $out;
}
endif;

# = DISPLAY SEARCH ALL COLUMN HEADS ===========
if(!function_exists('sf_render_searchall_column_header_row')):
function sf_render_searchall_column_header_row()
{
	global $sfglobals;

	$out = '<tr><th colspan="2">'.__("Forum/Topic", "sforum").'</th>'."\n";
	if($sfglobals['display']['topics']['firstcol']) $out.= '<th>'.__("Started", "sforum").'</th>'."\n";
	if($sfglobals['display']['topics']['lastcol']) $out.= '<th>'.__("Last Post", "sforum").'</th>'."\n";
	if($sfglobals['display']['topics']['postcol']) $out.= '<th>'.__("Posts", "sforum").'</th>'."\n";
	$out.= '</tr>'."\n";
	return $out;
}
endif;

# = DISPLAY SEARCH ALL ROWS ===================
if(!function_exists('sf_render_searchall_entry_row')):
function sf_render_searchall_entry_row($topic, $stats, $alt)
{
	global $sfvars, $sfglobals;

	# Display the topic entry
	$out = '<tr id="topic-'.$topic['topic_id'].'">'."\n";
	$out.= '<td class="sficoncell '.$alt.'">';
	$statkeys = sf_create_stats_keys($stats);
	$out.= sf_render_topic_icon($topic['topic_id'], $stats[$statkeys[1]]['udate'], $stats['thisuser'], $alt);
	$out.= '</td>';
	$out.= '<td class="'.$alt.'"><p>'.sf_filter_title_display($topic['forum_name'])."\n";
	$out.= '<br /><a title="'.$topic['post_tip'].'" href="'.sf_build_url($topic['forum_slug'], $topic['topic_slug'], 1, 0);

	if(strpos(SFURL, '?') === false)
	{
		$out.= '?value';
	} else {
		$out.= '&amp;value';
	}

	$out.= '='.$sfvars['searchvalue'].'&amp;type='.$sfvars['searchtype'].'&amp;include='.$sfvars['searchinclude'].'&amp;search='.$sfvars['searchpage'].'&amp;ret=all">'.sf_filter_title_display($topic['topic_name']).'</a>'."\n";
	$out.= '</p>';

	if($sfglobals['display']['topics']['pagelinks'])
	{
		$out.= sf_render_inline_pagelinks($topic['forum_slug'], $topic['topic_slug'], $topic['post_count'], 'SA');
	}
	$out.= '</td>'."\n";

	if($sfglobals['display']['topics']['firstcol'])
	{
		if(isset($statkeys[0]))
		{
			$out.= sf_render_first_last_post_cell($topic['forum_slug'], $topic['topic_slug'], $stats[$statkeys[0]], $alt);
		}
	}
	# Display last post column if option set
	if($sfglobals['display']['topics']['lastcol'])
	{
		$out.= sf_render_first_last_post_cell($topic['forum_slug'], $topic['topic_slug'], $stats[$statkeys[1]], $alt);
	}

	# Display post count and views if options set
	if($sfglobals['display']['topics']['postcol']) $out.= '<td class="sfcounts '.$alt.'">'.$topic['post_count'].'</td>'."\n";

	$out.= '</tr>'."\n";

	return $out;
}
endif;

# = POST RATINGS SUMMARY FOR TOPIC ============
if(!function_exists('sf_render_topic_ratings')):
function sf_render_topic_ratings($topic_id)
{
	$out = '';

	$postratings = sf_get_option('sfpostratings');
	if ($postratings['sfpostratings'])
	{
		$sum = 0;
		$count = 0;
		$ratings = sf_get_topic_ratings($topic_id);
		if ($ratings)
		{
			# get overall topic rating
			foreach ($ratings as $rating)
			{
				$sum += $rating->ratings_sum;
				$count += $rating->vote_count;
			}
		}

		# display the topic rating
		if ($postratings['sfratingsstyle'] == 1)  # thumb up/down
		{
			$out.= '<div class="sftopicratingscontainer sfthumbs">';
			$downimg = SFRESOURCES.'ratings/ratedown.png';
			$upimg = SFRESOURCES.'ratings/rateup.png';
			if ($sum < 0)
			{
				$out.= '<img src="'.$downimg.'" alt="" title="'.esc_attr(__("Negative Rating", "sforum")).'" />';
			} else {
				$out.= '<img src="'.$upimg.'" alt="" title="'.esc_attr(__("Positive Rating", "sforum")).'" />';
			}
            if ($sum > 0) $sum = '+'.$sum;
			$out.= $sum;
			$out.= '</div>';
		} else {  # stars
			if ($count)
			{
				$star_rating = round($sum / $count, 1);
				$img = SFRESOURCES.'ratings/ratestaron.png';
			} else {
				$star_rating = 0;
				$img = SFRESOURCES.'ratings/ratestaroff.png';
			}
			$out.= '<div class="sftopicratingscontainer sfstars">';
			$text = __("Post Rating: ", "sforum").$star_rating;
			$out.= '<img src="'.$img.'" alt="" title="'.esc_attr($text).'" />';
			$intrating = floor($star_rating);
			$out.= $star_rating;
			$out.= '</div>';
		}
	}
	return $out;
}
endif;

function sf_render_subforums($parentforum, $children, $groupid)
{
	global $subforums, $subforumids, $sfglobals;

	include_once (SF_PLUGIN_DIR.'/forum/sf-group-components.php');

	$forums = sf_get_subforums($children);
	if(!$forums) return;

	$allforums = sf_get_forums_all(false, true);
	$out= '';

	# grab the complete last post stats
	$posts = array();
	foreach($forums as $forum)
	{
		if(!empty($forum['post_id']))
		{
			$posts[]=$forum['post_id'];
		}
	}
	$stats = sf_get_combined_forum_stats($posts, true);

	# Setup group icon
	$group = sf_get_group_record($groupid, true);
	if(!empty($group['group_icon']))
	{
		$icon = SFCUSTOMURL.$group['group_icon'];
	} else {
		$icon = SFRESOURCES.'group.png';
	}

	$out.= '<div class="sfblock">'."\n";
	$out.= sf_render_main_header_table('subforum', 0, sf_filter_title_display($parentforum), '', $icon,'','','','','','','');
	$out.= '<table class="sfforumtable">'."\n";
	$out.= sf_render_group_column_header_row();

	$subforums = array();
	$subforumids = array();

	$alt = '';
	foreach ($forums as $forum)
	{
		if (in_array($forum['forum_id'], $subforumids)) continue;

		# Display current forum row
		$out.= sf_render_forum_entry_row($forum, $stats[$forum['forum_id']], $alt);

		# if forum has subforums build list
		if ($sfglobals['display']['topics']['showsubforums'] && $forum['children'])
		{
			$subforums = array();
			$subforumids = array();
			$subforums = sf_build_subforum_list($allforums, $forum['forum_id'], 0);
			$out.= sf_render_sub_forum_entry_row($subforums, $alt);
		}

		if ($alt == '') $alt = 'sfalt'; else $alt = '';
	}

	$out.= '</table>'."\n";
	$out.= '</div>'."\n";

	return $out;
}

?>