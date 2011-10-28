<?php
/*
Simple:Press
Main database routines
$LastChangedDate: 2011-04-26 05:52:27 -0700 (Tue, 26 Apr 2011) $
$Rev: 5981 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# ******************************************************************
# GROUP/FORUM VIEW AND GENERAL DB FUNCTIONS
# ******************************************************************

# ------------------------------------------------------------------
# sf_get_combined_groups_and_forums($groupid)
#
# Grabs all groups and forums. Note that the group data is repeated.
# Used to populate 'Select Forum Quicklinks' and Front Main page
# of forum (Group/Forum View)
#	$groupid:		Optional id to display just a single group
# ------------------------------------------------------------------
function sf_get_combined_groups_and_forums($groupid = null)
{
	global $wpdb, $session_groups, $current_user;

	If(is_null($groupid) ? $where='' : $where = " WHERE ".SFGROUPS.".group_id=".$groupid." ");

	# retrieve group and forum records
	$records = $wpdb->get_results(
			"SELECT ".SFGROUPS.".group_id, group_name, group_desc, group_rss, group_icon, group_message,
			 forum_id, forum_name, forum_slug, forum_desc, forum_status, forum_icon, forum_rss_private, post_id, topic_count, parent, children
			 FROM ".SFGROUPS."
			 JOIN ".SFFORUMS." ON ".SFGROUPS.".group_id = ".SFFORUMS.".group_id
			 ".$where."
			 ORDER BY group_seq, forum_seq;");

	# rebuild into an array grabbing permissions on the way
	$groups=array();

	if($records)
	{
		# Set initially to Access Denied in case current user can view no forums
		$groups[0]['group_id'] = "Access Denied";
		$gindex=-1;
		$findex=0;

		foreach($records as $record)
		{
			$groupid=$record->group_id;
			$forumid=$record->forum_id;

			if (sf_can_view_forum($forumid) || sf_user_can($current_user->ID, 'Can view forum lists only', $forumid) || sf_user_can($current_user->ID, 'Can view forum and topic lists only', $forumid))
			{
				if($gindex == -1 || $groups[$gindex]['group_id'] != $groupid)
				{
					$gindex++;
					$findex=-1;
					$groups[$gindex]['group_id']=$record->group_id;
					$groups[$gindex]['group_name']=$record->group_name;
					$groups[$gindex]['group_desc']=$record->group_desc;
					$groups[$gindex]['group_rss']=$record->group_rss;
					$groups[$gindex]['group_icon']=$record->group_icon;
					$groups[$gindex]['group_message']=$record->group_message;
				}
				if(isset($record->forum_id))
				{
						$groups[$gindex]['forums'][$findex]['forum_id']=$record->forum_id;
						$groups[$gindex]['forums'][$findex]['forum_name']=$record->forum_name;
						$groups[$gindex]['forums'][$findex]['forum_slug']=$record->forum_slug;
						$groups[$gindex]['forums'][$findex]['forum_desc']=$record->forum_desc;
						$groups[$gindex]['forums'][$findex]['forum_status']=$record->forum_status;
						$groups[$gindex]['forums'][$findex]['forum_icon']=$record->forum_icon;
						$groups[$gindex]['forums'][$findex]['forum_rss_private']=$record->forum_rss_private;
						$groups[$gindex]['forums'][$findex]['post_id']=$record->post_id;
						$groups[$gindex]['forums'][$findex]['topic_count']=$record->topic_count;
						$groups[$gindex]['forums'][$findex]['parent']=$record->parent;
						$groups[$gindex]['forums'][$findex]['children']=$record->children;
						$findex++;
				}
			}
		}
	} else {
		$records = sf_get_groups_all(false, false);
		if($records)
		{
			foreach($records as $record)
			{
				$groups[$gindex]['group_id']=$record->group_id;
				$groups[$gindex]['group_name']=$record->group_name;
				$groups[$gindex]['group_desc']=$record->group_desc;
				$groups[$gindex]['group_rss']=$record->group_rss;
				$groups[$gindex]['group_icon']=$record->group_icon;
				$groups[$gindex]['group_message']=$record->group_message;
				$gindex++;
			}
		}
	}
	$session_groups = $groups;
	return $groups;
}

# ------------------------------------------------------------------
# sf_get_combined_groups_and_forums_bloglink()
#
# Grabs all groups and forums. Soecial cut down version for
# populating the blog link add post drop down
# ------------------------------------------------------------------
function sf_get_combined_groups_and_forums_bloglink()
{
	global $wpdb, $current_user;

	# retrieve group and forum records
	$records = $wpdb->get_results(
			"SELECT ".SFGROUPS.".group_id, group_name,
			 forum_id, forum_name
			 FROM ".SFGROUPS."
			 JOIN ".SFFORUMS." ON ".SFGROUPS.".group_id = ".SFFORUMS.".group_id
			 ".$where."
			 ORDER BY group_seq, forum_seq;");

	# rebuild into an array grabbing permissions on the way
	$groups=array();
	$gindex=-1;
	$findex=0;
	if($records)
	{
		foreach($records as $record)
		{
			$groupid=$record->group_id;
			$forumid=$record->forum_id;

			if (sf_user_can($current_user->ID, 'Can create linked topics', $forumid) && sf_user_can($current_user->ID, 'Can start new topics', $forumid))
			{
				if($gindex == -1 || $groups[$gindex]['group_id'] != $groupid)
				{
					$gindex++;
					$findex=0;
					$groups[$gindex]['group_id']=$record->group_id;
					$groups[$gindex]['group_name']=$record->group_name;
				}
				if(isset($record->forum_id))
				{
					$groups[$gindex]['forums'][$findex]['forum_id']=$record->forum_id;
					$groups[$gindex]['forums'][$findex]['forum_name']=$record->forum_name;
					$findex++;
				}
			}
		}
	}

	return $groups;
}

function sf_get_combined_forum_stats($posts)
{
	global $wpdb, $current_user;

	$clause='';

	if($posts)
	{
		$pcount = count($posts);
		$done = 0;

		foreach ($posts as $post)
		{
			$clause.= "(".SFPOSTS.".post_id=".$post.")";
			$done++;
			if($done < $pcount) $clause.= " OR ";
		}
	} else {
		$record=array();
		$record['topic_count'] = '0';
		$record['post_count'] = '0';
		$record['udate'] = '';

		return $record;
	}

	$records = $wpdb->get_results(
 			"SELECT ".SFPOSTS.".post_id, ".SFPOSTS.".topic_id, topic_name, ".SFPOSTS.".forum_id, ".sf_zone_datetime('post_date').",
 			 UNIX_TIMESTAMP(post_date) as udate, guest_name, ".SFPOSTS.".user_id, post_content, post_status, ".SFMEMBERS.".display_name, post_index,
 			 ".SFFORUMS.".topic_count, ".SFFORUMS.".post_count, topic_slug
			 FROM ".SFPOSTS."
			 LEFT JOIN ".SFFORUMS." ON ".SFPOSTS.".forum_id = ".SFFORUMS.".forum_id
			 LEFT JOIN ".SFTOPICS." ON ".SFPOSTS.".topic_id = ".SFTOPICS.".topic_id
			 LEFT JOIN ".SFMEMBERS." ON ".SFPOSTS.".user_id = ".SFMEMBERS.".user_id
			 WHERE ".$clause.";", ARRAY_A);

	if($records)
	{
		$sfdisplay=array();
		$sfdisplay=sf_get_option('sfdisplay');

		$forums = array();
		foreach($records as $record)
		{
			$forums[$record['forum_id']] = $record;

			# do we need a post tooltip on the topic link?
			if (($sfdisplay['topics']['posttip']==true) && (sf_can_view_forum($record['forum_id'])==true) && (sf_user_can($current_user->ID, 'Can view forum lists only', $record['forum_id'])==false) &&  (sf_user_can($current_user->ID, 'Can view forum and topic lists only', $record['forum_id'])==false))
			{
				$forums[$record['forum_id']]['post_tip']=sf_filter_tooltip_display($record['post_content'], $record['post_status']);
				$forums[$record['forum_id']]['post_content']='';
			}
		}
	}

	return $forums;
}

# ------------------------------------------------------------------
# sf_get_group_record()
#
# Returns a single group row
# 	$groupid:		group_id of group to return
#	$asArray:		return as an array if true
# Note: No permission checking is performed
# ------------------------------------------------------------------
function sf_get_group_record($groupid, $asArray=false)
{
	global $wpdb;

	if(!$groupid) return '';

	$sql=(
			"SELECT *
			 FROM ".SFGROUPS."
			 WHERE group_id=".$groupid.";");
	if($asArray) return $wpdb->get_row($sql, ARRAY_A);
	return $wpdb->get_row($sql);
}

# ------------------------------------------------------------------
# sf_get_groups_all()
#
# Return ALL group records - no permission checking
#	$id_only:		Optionsal = return just ids
#	$asArray:		Optional - return as array
# ------------------------------------------------------------------
function sf_get_groups_all($id_only=false, $asArray=false)
{
	global $wpdb;

	if($id_only ? $FROM='group_id' : $FROM='*');

	$sql=("SELECT ".$FROM." FROM ".SFGROUPS." ORDER BY group_seq");
	if($asArray) return $wpdb->get_results($sql, ARRAY_A);
	return $wpdb->get_results($sql);
}

# ------------------------------------------------------------------
# sf_group_exists()
#
# Check the existence of a group by id
# 	$groupid:		group to check for
# ------------------------------------------------------------------
function sf_group_exists($groupid)
{
	global $wpdb;

	if(empty($groupid)) return false;
	if($wpdb->get_var(
			"SELECT group_name
			 FROM ".SFGROUPS."
			 WHERE group_id=".$groupid))
	{
		return true;
	}
	return false;
}

# ------------------------------------------------------------------
# sf_get_group_rss_url()
#
# Returns the RSS feed URL for a Group (custom or standard)
# 	$groupid:		group to return
# ------------------------------------------------------------------
function sf_get_group_rss_url($groupid)
{
	global $wpdb, $sfglobals;

	if (empty($groupid)) return '';

	$url = $wpdb->get_var("SELECT group_rss FROM ".SFGROUPS." WHERE group_id=".$groupid);
	if (empty($url))
    {
        $rssopt = sf_get_option('sfrss');
        if ($rssopt['sfrssfeedkey'] && isset($sfglobals['member']['feedkey']))
            $url = sf_build_qurl('group='.$groupid, 'xfeed=group', 'feedkey='.$sfglobals['member']['feedkey']);
        else
            $url = sf_build_qurl('group='.$groupid, 'xfeed=group');
    }

	return $url;
}

# ------------------------------------------------------------------
# sf_get_group_name_from_forum()
#
# Returns the Group Name when only the forum id is known
# 	$forumid:		forum to lookup for group name
# ------------------------------------------------------------------
function sf_get_group_name_from_forum($forumid)
{
	global $wpdb;

	if(!$forumid) return '';

	return $wpdb->get_var(
			"SELECT ".SFGROUPS.".group_name
			 FROM ".SFGROUPS."
			 JOIN ".SFFORUMS." ON ".SFFORUMS.".group_id = ".SFGROUPS.".group_id
			 WHERE ".SFFORUMS.".forum_id=".$forumid);
}

# ******************************************************************
# FORUM/TOPIC VIEW AND GENERAL DB FUNCTIONS
# ******************************************************************

# ------------------------------------------------------------------
# sf_get_combined_forums_and_topics($forumid)
#
# Grabs all forums and their topics. Note that the forum data is
# repeated. Used to populate Topic Listing page of topics
# (Forum/Topics View)
#	$forumid:			forum id to display
#	$currentpage:		index to paging
# ------------------------------------------------------------------
function sf_get_combined_forums_and_topics($forumid, $currentpage)
{
	global $wpdb, $sfvars, $sfglobals, $current_user;

	if(!$forumid) return '';

	$sfvars['searchresults'] = 0;

	# rebuild into an array
	$forums=array();

	# Set initially to Access Denied in case current user can view no forums
	$forums[0]['forum_id']="Access Denied";

	# quick permission check
	if(!sf_can_view_forum($forumid) && !sf_user_can($current_user->ID, 'Can view forum and topic lists only', $forumid)) return $forums;

	# some setup vars
	$startlimit = 0;

	# get post tooltip state
	$sfdisplay = array();
	$sfdisplay = sf_get_option('sfdisplay');

	# how many topics per page?
	$tpaged = $sfglobals['display']['topics']['perpage'];
	if(!$tpaged) $tpaged=20;

	# setup where we are in the topic list (paging)
	if($sfvars['searchpage'] == 0)
	{
		if($currentpage != 1)
		{
			$startlimit = ((($currentpage-1) * $tpaged));
		}
	} else {
		if($sfvars['searchpage'] == 1)
		{
			$currentpage = 1;
		} else {
			$startlimit = ((($sfvars['searchpage']-1) * $tpaged));
		}
	}

	$LIMIT = " LIMIT ".$startlimit.', '.$tpaged;

	if($sfvars['searchpage'] == 0)
	{
		$topicsort = $sfglobals['display']['topics']['sortnewtop'];
		if ($topicsort)
		{
			$ORDER = " ORDER BY topic_pinned DESC, ".SFTOPICS.".post_id DESC";
		} else {
			$ORDER = " ORDER BY topic_pinned DESC, ".SFTOPICS.".post_id ASC";
		}
	} else {
		$ORDER = " ORDER BY ".SFTOPICS.".post_id DESC";
	}

	if($sfvars['searchpage'] == 0)
	{
		# standar forum view
		$SELECT = "SELECT ";
		$MATCH = "";
		$ANDWHERE = "";
	} else {

		$searchvalue=urldecode($sfvars['searchvalue']);
		if(empty($searchvalue))
		{
			return '';
		}

		# what sort of search is it?
		if($sfvars['searchtype'] == 6) {

			# topic status search
			$SELECT = "SELECT SQL_CALC_FOUND_ROWS DISTINCT ";
			$MATCH = "";
			$ANDWHERE = " AND topic_status_flag=".$sfvars['searchvalue']." ";

		} elseif($sfvars['searchtype'] == 8) {

			# members 'posted in' sepcified forum search
			$userid = $sfvars['searchvalue'];
			$SELECT = "SELECT SQL_CALC_FOUND_ROWS DISTINCT ";
			$MATCH = "";
			$ANDWHERE = " AND ".SFPOSTS.".user_id=".$userid." ";

		} elseif($sfvars['searchtype'] == 9) {

			# members 'started' in sepcified forum search
			$userid = $sfvars['searchvalue'];
			$SELECT = "SELECT SQL_CALC_FOUND_ROWS DISTINCT ";
			$MATCH = "";
			$ANDWHERE = " AND ".SFPOSTS.".user_id=".$userid." ";

		} elseif($sfvars['searchinclude'] == 4) {

			# it's a tag search
			$searchtag = sf_create_slug($searchvalue, 'tag');
			$SELECT = "SELECT SQL_CALC_FOUND_ROWS DISTINCT ";
			$MATCH = SFTOPICS.".topic_id IN (SELECT topic_id FROM ".SFTAGMETA." JOIN ".SFTAGS." ON ".SFTAGMETA.".tag_id = ".SFTAGS.".tag_id
				WHERE tag_slug = '".$searchtag."' AND forum_id=".$forumid.") AND ";
			$ANDWHERE = "";

		} else {

			# general keyword search
			$SELECT = "SELECT SQL_CALC_FOUND_ROWS DISTINCT ";

			$searchterm = sf_construct_search_term($searchvalue, $sfvars['searchtype']);

			switch($sfvars['searchinclude'])
			{
				case 1:
					$MATCH = "(MATCH(".SFPOSTS.".post_content) AGAINST ('".esc_sql(like_escape($searchterm))."' IN BOOLEAN MODE) OR MATCH(".SFTOPICS.".topic_name) AGAINST ('".esc_sql(like_escape($searchterm))."' IN BOOLEAN MODE)) AND ";
					break;
				case 2:
					$MATCH = "MATCH(".SFPOSTS.".post_content) AGAINST ('".esc_sql(like_escape($searchterm))."' IN BOOLEAN MODE) AND ";
					break;
				case 3:
					$MATCH = "MATCH(".SFTOPICS.".topic_name) AGAINST ('".esc_sql(like_escape($searchterm))."' IN BOOLEAN MODE) AND ";
					break;
			}
			$ANDWHERE = "";
		}
	}

	# retrieve forum and topic records - is a type 1, 2, 3 or 8 then we need a separate query (for now)
	# NO post_content or post_index = 1
	if (!empty($sfvars['searchtype']) && ($sfvars['searchtype'] == 1 || $sfvars['searchtype'] == 2 || $sfvars['searchtype'] == 3 || $sfvars['searchtype'] == 8))
	{
		$records = sf_query_results(
			$SELECT.SFFORUMS.".forum_id, forum_slug, forum_name, forum_status, group_id, topic_count, forum_icon, forum_desc, topic_status_set,
			parent, children, forum_message,
			 ".SFTOPICS.".topic_id, topic_slug, topic_name, ".sf_zone_datetime('topic_date').",
			 topic_status, topic_pinned, topic_opened, topic_subs, topic_watches, topic_status_flag,
			 post_ratings, use_tags, blog_post_id, ".SFTOPICS.".post_id,  ".SFTOPICS.".post_count, ".SFTOPICS.".user_id, post_status
			 FROM ".SFFORUMS."
			 JOIN ".SFTOPICS." ON ".SFFORUMS.".forum_id = ".SFTOPICS.".forum_id
			 JOIN ".SFPOSTS." ON ".SFTOPICS.".topic_id = ".SFPOSTS.".topic_id
			 WHERE ".$MATCH.SFFORUMS.".forum_id=".$forumid.$ANDWHERE.$ORDER.$LIMIT.";");
	} else {
		$records = sf_query_results(
			$SELECT.SFFORUMS.".forum_id, forum_slug, forum_name, forum_status, group_id, topic_count, forum_icon, forum_desc, topic_status_set,
			parent, children, forum_message,
			 ".SFTOPICS.".topic_id, topic_slug, topic_name, ".sf_zone_datetime('topic_date').",
			 topic_status, topic_pinned, topic_opened, topic_subs, topic_watches, topic_status_flag,
			 post_ratings, use_tags, blog_post_id, ".SFTOPICS.".post_id,  ".SFTOPICS.".post_count, ".SFTOPICS.".user_id, post_content, post_status
			 FROM ".SFFORUMS."
			 JOIN ".SFTOPICS." ON ".SFFORUMS.".forum_id = ".SFTOPICS.".forum_id
			 JOIN ".SFPOSTS." ON ".SFTOPICS.".topic_id = ".SFPOSTS.".topic_id
			 WHERE ".$MATCH.SFFORUMS.".forum_id=".$forumid.$ANDWHERE." AND ".SFPOSTS.".post_index=1 ".$ORDER.$LIMIT.";");
	}

	if (!empty($sfvars['searchpage']) && $records)
	{
		$sfvars['searchresults'] = $wpdb->get_var("SELECT FOUND_ROWS()");
	}

	$findex=-1;
	$tindex=0;

	if($records)
	{
		$topiclist = '';
		foreach($records as $record)
		{
			$forumid=$record->forum_id;

			if($findex == -1 || $forums[$findex]['forum_id'] != $forumid)
			{
				$findex++;
				$tindex=0;
				$forums[$findex]['forum_id']=$record->forum_id;
				$forums[$findex]['forum_slug']=$record->forum_slug;
				$forums[$findex]['forum_name']=$record->forum_name;
				$forums[$findex]['forum_desc']=$record->forum_desc;
				$forums[$findex]['forum_status']=$record->forum_status;
				$forums[$findex]['group_id']=$record->group_id;
				$forums[$findex]['topic_count']=$record->topic_count;
				$forums[$findex]['forum_icon']=$record->forum_icon;
				$forums[$findex]['topic_status_set']=$record->topic_status_set;
				$forums[$findex]['post_ratings']=$record->post_ratings;
				$forums[$findex]['use_tags']=$record->use_tags;
				$forums[$findex]['parent']=$record->parent;
				$forums[$findex]['children']=$record->children;
				$forums[$findex]['forum_message']=$record->forum_message;
			}
			$forums[$findex]['topics'][$tindex]['topic_id']=$record->topic_id;
			$forums[$findex]['topics'][$tindex]['topic_slug']=$record->topic_slug;
			$forums[$findex]['topics'][$tindex]['topic_name']=$record->topic_name;
			$forums[$findex]['topics'][$tindex]['topic_date']=$record->topic_date;
			$forums[$findex]['topics'][$tindex]['topic_status']=$record->topic_status;
			$forums[$findex]['topics'][$tindex]['topic_pinned']=$record->topic_pinned;
			$forums[$findex]['topics'][$tindex]['topic_opened']=$record->topic_opened;
			$forums[$findex]['topics'][$tindex]['topic_subs']=$record->topic_subs;
			$forums[$findex]['topics'][$tindex]['topic_watches']=$record->topic_watches;
			$forums[$findex]['topics'][$tindex]['topic_status_flag']=$record->topic_status_flag;
			$forums[$findex]['topics'][$tindex]['post_ratings']=$record->post_ratings;
			$forums[$findex]['topics'][$tindex]['use_tags']=$record->use_tags;
			$forums[$findex]['topics'][$tindex]['blog_post_id']=$record->blog_post_id;
			$forums[$findex]['topics'][$tindex]['post_id']=$record->post_id;
			$forums[$findex]['topics'][$tindex]['post_count']=$record->post_count;
			$forums[$findex]['topics'][$tindex]['user_id']=$record->user_id;

			# do we need a post tooltip on the topic link?
			if ($sfdisplay['topics']['posttip'] && (!empty($sfvars['searchtype']) && $sfvars['searchtype'] != 8) && $current_user->sfaccess)
			{
				$forums[$findex]['topics'][$tindex]['post_tip'] = sf_filter_tooltip_display($record->post_content, $record->post_status);
			} else {
				$forums[$findex]['topics'][$tindex]['post_tip']="";
			}

			# save topic in list for getting tags later
			$topiclist[] = $record->topic_id;

			$tindex++;
		}

		# Now get tags in one query for these topics
		$topiclist = implode(',', $topiclist);
		$sql = "SELECT tag_name, tag_slug, topic_id
		FROM ".SFTAGS."
	 	JOIN ".SFTAGMETA." ON ".SFTAGMETA.".tag_id = ".SFTAGS.".tag_id
		WHERE topic_id IN (".$topiclist.")
		ORDER BY topic_id";
		$tags = $wpdb->get_results($sql);

		# Now sort the tags into the monsterous forum array
		if ($tags)
		{
			foreach ($forums as $findex => $forum)
			{
				foreach ($forum['topics'] as $tindex => $topic)
				{
					if ($topic['use_tags'])
					{
						$topictags = '';
						foreach ($tags as $tag)
						{
							if ($topic['topic_id'] == $tag->topic_id)
							{
								# build tag list for this forum/topic
								$topictags[] = $tag;
							}
						}
						# save the tags into the master array
						$forums[$findex]['topics'][$tindex]['tags'] = $topictags;
					}
				}
			}
		}
	} else {
		$record = sf_get_forum_record($forumid);
		if($record)
		{
			$forums[0]['forum_id']=$record->forum_id;
			$forums[0]['forum_slug']=$record->forum_slug;
			$forums[0]['forum_name']=$record->forum_name;
			$forums[0]['forum_desc']=$record->forum_desc;
			$forums[0]['forum_status']=$record->forum_status;
			$forums[0]['group_id']=$record->group_id;
			$forums[0]['topic_count']=$record->topic_count;
			$forums[0]['forum_icon']=$record->forum_icon;
			$forums[0]['topic_status_set']=$record->topic_status_set;
			$forums[0]['post_ratings']=$record->post_ratings;
			$forums[0]['use_tags']=$record->use_tags;
			$forums[0]['parent']=$record->parent;
			$forums[0]['children']=$record->children;
			$forums[0]['forum_message']=$record->forum_message;
		}
	}
	return $forums;
}

function sf_get_subforums($sublist)
{
    global $wpdb;

    if (!$sublist) return '';

    $subforums = unserialize($sublist);
    $forumlist = array();
    foreach ($subforums as $forumid)
    {
        if (sf_can_view_forum($forumid)) $forumlist[] = $forumid;
    }

    if (empty($forumlist)) return '';

    $where = " WHERE forum_id IN (" . implode(",", $forumlist) . ")";
    $subforums = $wpdb->get_results("SELECT * FROM ".SFFORUMS.$where." ORDER BY forum_seq", ARRAY_A);

    return $subforums;
}

# ------------------------------------------------------------------
# sf_get_combined_topic_stats()
#
# Returns the first and last post data for a topic
#	$postid:		post id from new posts list
#	$user_id		for checking of user has posted in topic		-
# ------------------------------------------------------------------
function sf_get_combined_topic_stats($posts, $userid=0)
{
	global $wpdb, $current_user;

	$clause='';
	if($posts)
	{
		$pcount = count($posts);
		$done = 0;

		foreach ($posts as $topic=>$postindex)
		{
			$clause.= "(topic_id = ".$topic." AND post_index = 1 OR topic_id = ".$topic." AND post_index=".$postindex.")";
			$done++;
			if($done < $pcount) $clause.= " OR ";
		}
	} else {
		return;
	}

	$records = $wpdb->get_results(
			"SELECT post_id, topic_id, forum_id, ".sf_zone_datetime('post_date').", UNIX_TIMESTAMP(post_date) as udate, guest_name, ".SFPOSTS.".user_id, post_index, post_status, post_content, ".SFMEMBERS.".display_name
			 FROM ".SFPOSTS."
			 LEFT JOIN ".SFMEMBERS." ON ".SFPOSTS.".user_id = ".SFMEMBERS.".user_id
			 WHERE ".$clause."
			 ORDER BY post_id ASC;", ARRAY_A);

	# If forum view are we looking to see if user has posted in ant of the topics?
	if($records && $userid)
	{
		$clause='';
		$pcount = count($posts);
		$done = 0;

		foreach ($posts as $topic=>$postindex)
		{
			$clause.= "(topic_id = ".$topic." AND user_id = ".$userid.")";
			$done++;
			if($done < $pcount) $clause.= " OR ";
		}

		$userposted = $wpdb->get_results(
				"SELECT DISTINCT topic_id, user_id
				 FROM ".SFPOSTS."
				 WHERE ".$clause.";");
	} else {
		$userposted = '';
	}
	# Now to format and combine results
	if ($records)
	{
		$sfdisplay=array();
		$sfdisplay=sf_get_option('sfdisplay');

		$topics = array();
		foreach ($records as $record)
		{
			$topics[$record['topic_id']][$record['post_index']] = $record;

			# do we need a post tooltip on the topic link?
			if ($sfdisplay['topics']['posttip'] && $current_user->sfaccess)
			{
				$topics[$record['topic_id']][$record['post_index']]['post_tip']=sf_filter_tooltip_display($record['post_content'], $record['post_status']);
				$topics[$record['topic_id']][$record['post_index']]['post_content']='';
			}
		}

		foreach ($records as $record)
		{
			$topics[$record['topic_id']]['thisuser'] = false;
			if ($userposted)
			{
				foreach ($userposted as $user)
				{
					if ($user->topic_id == $record['topic_id'])
					{
						$topics[$record['topic_id']]['thisuser'] = true;
						break;
					}
				}
			}
		}
		return $topics;
	} else {
		return '';
	}
}

# ******************************************************************
# FORUM/TOPIC VIEW AND GENERAL DB FUNCTIONS
# ******************************************************************

# ------------------------------------------------------------------
# sf_get_watched_topics($currentpagge)
#
# Grabs all watched topics
#	$currentpage:		index to paging
# ------------------------------------------------------------------
function sf_get_watched_topics($currentpage=0)
{
	global $wpdb, $current_user, $sfglobals;

	# quick permission check
	if (!$current_user->sfwatch) return '';

	$limit='';

	# get watched topics
	$list = $sfglobals['member']['watches'];
	if (empty($list)) return '';
	# create where clause of watched topics
	$where = " WHERE ".SFTOPICS.".topic_id IN (" . implode(",", $list) . ")";

	# retrieve watched topic records
	$query = "SELECT ".SFTOPICS.".topic_id, topic_slug, topic_name, ".sf_zone_datetime('topic_date').",
			 topic_status, topic_pinned, topic_opened, topic_watches, topic_status_flag,
			 blog_post_id, ".SFTOPICS.".forum_id, ".SFTOPICS.".post_id, ".SFTOPICS.".post_count,
			 forum_slug, forum_name, topic_status_set, post_ratings, group_id, post_content, post_status
			 FROM ".SFTOPICS."
			 JOIN ".SFFORUMS." ON ".SFFORUMS.".forum_id = ".SFTOPICS.".forum_id
			 JOIN ".SFPOSTS." ON ".SFTOPICS.".post_id = ".SFPOSTS.".post_id ".
			 $where." ORDER BY topic_date DESC ".$limit;

	$records = $wpdb->get_results($query, ARRAY_A);

	if($records)
	{
		$sfdisplay=array();
		$sfdisplay=sf_get_option('sfdisplay');

		# do we need a post tooltip on the topic link?
		if($sfdisplay['topics']['posttip'])
		{
			for($x=0; $x<count($records); $x++)
			{
				if ($current_user->sfaccess)
				{
					$records[$x]['post_tip']=sf_filter_tooltip_display($records[$x]['post_content'], $records[$x]['post_status']);
					$records[$x]['post_content']='';
				}
			}
		}
	}

	$watched['records'] = $records;
	$watched['count'] = count($list);

	return $watched;
}

# ------------------------------------------------------------------
# sf_get_subscribed_topics($currentpagge)
#
# Grabs all subscribed topics
#	$currentpage:		index to paging
# ------------------------------------------------------------------
function sf_get_subscribed_topics($currentpage=0)
{
	global $wpdb, $current_user, $sfglobals;

	$limit = '';

	# quick permission check
	if (!$current_user->sfsubscriptions) return '';

	# get subscribed topics
	$list = $sfglobals['member']['subscribe'];
	if (empty($list)) return '';

	# create where clause of subscribed topics
	$where = " WHERE ".SFTOPICS.".topic_id IN (" . implode(",", $list) . ")";

	# retrieve watched topic records
	$query = "SELECT ".SFTOPICS.".topic_id, topic_slug, topic_name, ".sf_zone_datetime('topic_date').",
			 topic_status, topic_pinned, topic_opened, topic_subs, topic_status_flag,
			 blog_post_id, ".SFTOPICS.".forum_id, ".SFTOPICS.".post_id, ".SFTOPICS.".post_count,
			 forum_slug, forum_name, topic_status_set, post_ratings, group_id, post_content, post_status
			 FROM ".SFTOPICS."
			 JOIN ".SFFORUMS." ON ".SFFORUMS.".forum_id = ".SFTOPICS.".forum_id
			 JOIN ".SFPOSTS." ON ".SFTOPICS.".post_id = ".SFPOSTS.".post_id ".
			 $where." ORDER BY topic_date DESC ".$limit;
	$records = $wpdb->get_results($query, ARRAY_A);

	if($records)
	{
		$sfdisplay=array();
		$sfdisplay=sf_get_option('sfdisplay');

		# do we need a post tooltip on the topic link?
		if ($sfdisplay['topics']['posttip'])
		{
			for($x=0; $x<count($records); $x++)
			{
				if ($current_user->sfaccess)
				{
					$records[$x]['post_tip']=sf_filter_tooltip_display($records[$x]['post_content'], $records[$x]['post_status']);
					$records[$x]['post_content']='';
				}
			}
		}
	}

	$subscribed['records'] = $records;
	$subscribed['count'] = count($list);

	return $subscribed;
}

# ------------------------------------------------------------------
# sf_get_related_topics($tags)
#
# Grabs related topics based on tags
# ------------------------------------------------------------------
function sf_get_related_topics($tags)
{
	global $wpdb;

	if(!$tags) return '';

	# build list of tags for the topic id
	$taglist = '';
	foreach ($tags as $tag)
	{
		if ($taglist == '')
		{
			$taglist = "('".$tag->tag_slug."'";
		} else {
			$taglist.= ",'".$tag->tag_slug."'";
		}
	}
	$taglist.= ")";

	# now grab the results
	$LIMIT = ' LIMIT 10';
	$ORDER = ' ORDER BY topic_id DESC';
	$WHERE = SFTOPICS.".topic_id IN (SELECT topic_id FROM ".SFTAGMETA." JOIN ".SFTAGS." ON ".SFTAGMETA.".tag_id = ".SFTAGS.".tag_id
		WHERE tag_slug IN ".$taglist.")";
	$topics = $wpdb->get_results(
			"SELECT SQL_CALC_FOUND_ROWS DISTINCT
			 ".SFTOPICS.".topic_id, topic_name, topic_slug, ".SFTOPICS.".forum_id, forum_name, forum_slug,
			 ".sf_zone_datetime('topic_date').", topic_status, topic_pinned, topic_opened,
			 topic_watches, topic_status_flag, blog_post_id, ".SFTOPICS.".post_id, ".SFTOPICS.".post_count,
			 topic_status_set, post_ratings, group_id
			 FROM ".SFTOPICS."
			 JOIN ".SFFORUMS." ON ".SFTOPICS.".forum_id = ".SFFORUMS.".forum_id
			 JOIN ".SFPOSTS." ON ".SFTOPICS.".topic_id = ".SFPOSTS.".topic_id
			 WHERE ".$WHERE.$ORDER.$LIMIT.";", ARRAY_A);

 	return $topics;
}

# ------------------------------------------------------------------
# sf_get_memberlists()
#
# Builds viewable member lists for current user
# ------------------------------------------------------------------
function sf_get_memberlists($currentpage, $search)
{
	global $wpdb, $current_user, $sfglobals;

	$data = '';
	$data->records = '';
	$data->count = 0;

	$sfmemberopts = sf_get_option('sfmemberopts');
	if ($current_user->forumadmin || ($sfmemberopts['sfshowmemberlist'] && $current_user->sfmemberlist))
	{
		# are we limiting member lists to memberships?
		$where = ' WHERE posts > -2';
        $sfmemberopts = sf_get_option('sfmemberopts');
		if ($sfmemberopts['sflimitmemberlist'] && !$current_user->forumadmin)
		{
			# get usergroups user has membership in
			$ugs = sf_get_user_memberships($current_user->ID);

			#if no usergroup memberships return empty list
			if ($ugs)
			{
				$ug_ids='';
				foreach ($ugs as $ug)
				{
					$ug_ids[] = $ug['usergroup_id'];
				}

				# if no visible usergroups, dont return any search results
				if (empty($ug_ids)) return $data;
			} else {
				return $data;
			}

			# create where clause based on user memberships
			$where.= " AND (".SFMEMBERSHIPS.".usergroup_id IN (" . implode(",", $ug_ids) . ") OR ".SFMEMBERSHIPS.".usergroup_id IS NULL)";
		}

		if ($search != '')
		{
			$where.= " AND ".SFMEMBERS.'.display_name LIKE "'.$search.'%"';
		}

		# how many members per page?
		$startlimit = 0;
		$tpaged = $sfglobals['display']['topics']['perpage'];
		if(!$tpaged) $tpaged=20;

		if ($currentpage != 1)
		{
			$startlimit = ((($currentpage-1) * $tpaged));
		}
		$limit = " LIMIT ".$startlimit.', '.$tpaged;

		# retrieve members list records
		$query = "SELECT SQL_CALC_FOUND_ROWS DISTINCT IFNULL(".SFMEMBERSHIPS.".usergroup_id, '0') as usergroup_id, ".SFMEMBERS.".user_id,
			".SFMEMBERS.".display_name, user_email, posts, lastvisit, IFNULL(usergroup_name, 'Admin') as usergroup_name,
			IFNULL(usergroup_desc, 'Forum Administrator') as usergroup_desc
			FROM ".SFUSERS."
			LEFT JOIN ".SFMEMBERS." ON ".SFUSERS.".ID = ".SFMEMBERS.".user_id
			LEFT JOIN ".SFMEMBERSHIPS." ON ".SFMEMBERSHIPS.".user_id = ".SFMEMBERS.".user_id
			LEFT JOIN ".SFUSERGROUPS." ON ".SFUSERGROUPS.".usergroup_id = ".SFMEMBERSHIPS.".usergroup_id
			".$where."
			ORDER BY usergroup_id, ".SFMEMBERS.".display_name "
			.$limit;

		$records = $wpdb->get_results($query, ARRAY_A);
		$data->records = $records;

		$data->count = $wpdb->get_var("SELECT FOUND_ROWS()");
	}

	return $data;
}

function sf_get_forum_memberships($user_id)
{
	global $wpdb, $current_user;

	if ($current_user->guest)
	{
		$value = sf_get_sfmeta('default usergroup', 'sfguests');
		$guests = $value[0]['meta_value'];
		$sql = "SELECT forum_id
				FROM ".SFPERMISSIONS."
				WHERE usergroup_id=".$guests;
	} else {
		$sql = "SELECT forum_id
				FROM ".SFPERMISSIONS."
				JOIN ".SFMEMBERSHIPS." ON ".SFPERMISSIONS.".usergroup_id = ".SFMEMBERSHIPS.".usergroup_id
				WHERE user_id=".$user_id;
	}

	return $wpdb->get_results($sql);
}

function sf_get_membership_count($usergroup_id)
{
	global $wpdb;

	if(!$usergroup_id) return '';

	$sql = "SELECT COUNT(*)
			FROM ".SFMEMBERSHIPS."
			WHERE ".SFMEMBERSHIPS.".usergroup_id=".$usergroup_id;
	return $wpdb->get_var($sql);
}

# ------------------------------------------------------------------
# sf_get_last_post_in_topic()
#
# Returns post details of the latest post in the requested topic
#	$topicid:		requested topic
# NOTE: This one remains as used in a template tag
# ------------------------------------------------------------------
function sf_get_last_post_in_topic($topicid)
{
	global $wpdb;

	if(!$topicid) return '';

	return $wpdb->get_row(
			"SELECT post_id, topic_id, forum_id, post_status, post_index, ".sf_zone_datetime('post_date').", UNIX_TIMESTAMP(post_date) as udate, guest_name, guest_email, ".SFPOSTS.".user_id, ".SFMEMBERS.".display_name, user_email
			 FROM ".SFPOSTS."
			 LEFT JOIN ".SFMEMBERS." ON ".SFPOSTS.".user_id = ".SFMEMBERS.".user_id
			 LEFT JOIN ".SFUSERS." ON ".SFPOSTS.".user_id = ".SFUSERS.".ID
			 WHERE topic_id = ".$topicid." AND post_status = 0
			 ORDER BY post_id DESC LIMIT 1");
}

# ------------------------------------------------------------------
# sf_get_postratings()
#
# Returns post ratings
# 	$postid:		post_id of post to return
#	$asArray:		return as an array if true
# Note: No permission checking is performed
# ------------------------------------------------------------------
function sf_get_postratings($postid, $asArray=false)
{
	global $wpdb;

	if(!$postid) return '';

	$sql=(
			"SELECT *
			 FROM ".SFPOSTRATINGS."
			 WHERE post_id=".$postid.";");
	if($asArray) return $wpdb->get_row($sql, ARRAY_A);
	return $wpdb->get_row($sql);
}

# ------------------------------------------------------------------
# sf_update_postratings()
#
# Upates post ratings
# 	$postid:		post_id
# 	$count:			number of votes
# 	$sum:			ratings sum
# 	$ips:			array of ips voted for guests
# 	$members:		members that have voted
# Note: No permission checking is performed
# ------------------------------------------------------------------
function sf_update_postratings($postid, $count, $sum, $ips, $members)
{
	global $wpdb;

	if(!$postid) return '';

	$sql=(
			"UPDATE ".SFPOSTRATINGS."
			 SET vote_count=$count, ratings_sum=$sum, ips='".$ips."', members='".$members."'
			 WHERE post_id=".$postid.";");
	$wpdb->query($sql);
	return;
}

# ------------------------------------------------------------------
# Add post ratings
# 	$postid:		post_id
# 	$count:			number of votes
# 	$sum:			ratings sum
# 	$ips:			array of ips voted for guests
# 	$members:		members that have voted
# Note: No permission checking is performed
# ------------------------------------------------------------------
function sf_add_postratings($postid, $count, $sum, $ips, $members)
{
	global $wpdb;

	if(!$postid) return '';

	$sql=(
		 	"INSERT INTO ".SFPOSTRATINGS." (post_id, vote_count, ratings_sum, ips, members)
			 VALUES ($postid, $count, $sum, '".$ips."', '".$members."');");
	$wpdb->query($sql);
	return;
}

# ------------------------------------------------------------------
# sf_get_topic_ratings()
#
# Returns post ratings
# 	$topicid:		post_id of post to return
# Note: No permission checking is performed
# ------------------------------------------------------------------
function sf_get_topic_ratings($topicid)
{
	global $wpdb;

	if(!$topicid) return '';

	$sql = ("
			SELECT vote_count, ratings_sum
			FROM ".SFPOSTRATINGS."
 			JOIN ".SFPOSTS." ON ".SFPOSTS.".topic_id = ".$topicid."
			WHERE ".SFPOSTRATINGS.".post_id = ".SFPOSTS.".post_id");
	return $wpdb->get_results($sql);
}

# ------------------------------------------------------------------
# sf_get_forum_record()
#
# Returns a single forum row
# 	$forumid:		forum_id of forum to return
# Note: No permission checking is performed
# ------------------------------------------------------------------
function sf_get_forum_record($forumid)
{
	global $wpdb, $session_forums;

	if(!$forumid) return '';

	# check if in the session forums cache
	if($session_forums)
	{
		foreach($session_forums as $forum)
		{
			if($forum->forum_id == $forumid)
			{
				return $forum;
			}
		}
	}

	$sql=(
			"SELECT *
			 FROM ".SFFORUMS."
			 WHERE forum_id=".$forumid.";");
	$thisforum = $wpdb->get_row($sql);
	$session_forums[] = $thisforum;
	return $thisforum;
}

# ------------------------------------------------------------------
# sf_get_forum_record_from_slug()
#
# Returns a single forum row
# 	$forumslug:		forum_slug of forum to return
#	$asArray:		return as an array if true
# Note: No permission checking is performed
# ------------------------------------------------------------------
function sf_get_forum_record_from_slug($forumslug, $asArray=false)
{
	global $wpdb;

	if(!$forumslug) return '';

	$sql=(
			"SELECT *
			 FROM ".SFFORUMS."
			 WHERE forum_slug='".$forumslug."';");
	if($asArray) return $wpdb->get_row($sql, ARRAY_A);
	return $wpdb->get_row($sql);
}

# ------------------------------------------------------------------
# sf_get_group_record_from_slug()
#
# Returns a single group and forum row
# 	$forumslug:		forum_slug of group and forum to return
#	$asArray:		return as an array if true
# Note: No permission checking is performed
# ------------------------------------------------------------------
function sf_get_group_record_from_slug($forumslug, $asArray=false)
{
	global $wpdb;

	if(!$forumslug) return '';

	$sql=(
			"SELECT *
			 FROM ".SFFORUMS."
             JOIN ".SFGROUPS." ON ".SFFORUMS.".group_id = ".SFGROUPS.".group_id
			 WHERE forum_slug='".$forumslug."';");
	if($asArray) return $wpdb->get_row($sql, ARRAY_A);
	return $wpdb->get_row($sql);
}

# ------------------------------------------------------------------
# sf_get_forums_all()
#
# Returns complete recordset of forums
# 	$id_only:		limit recordset to forum_id and slug only
#	$asArray:		return results as an array
# Note: No permission checking is performed
# ------------------------------------------------------------------
function sf_get_forums_all($id_only=false, $asArray=false)
{
	global $wpdb;

	if($id_only ? $FROM='forum_id, forum_slug' : $FROM='*');
	$sql=("SELECT ".$FROM." FROM ".SFFORUMS." ORDER BY forum_seq");
	if($asArray) return $wpdb->get_results($sql, ARRAY_A);
	return $wpdb->get_results($sql);
}

# ------------------------------------------------------------------
# sf_forum_exists()
#
# Check the existence of a forum by id
# 	$forumid:		forum to check for
# ------------------------------------------------------------------
function sf_forum_exists($forumid)
{
	global $wpdb;

	if(empty($forumid)) return false;
	if($wpdb->get_var(
			"SELECT forum_name
			 FROM ".SFFORUMS."
			 WHERE forum_id=".$forumid))
	{
		return true;
	}
	return false;
}

# ------------------------------------------------------------------
# sf_forum_locked()
#
# Returns the lock status of a forum
# 	$forumid:		forum to check for
# ------------------------------------------------------------------
function sf_forum_locked($forumid)
{
	global $wpdb;

	return $wpdb->get_var("SELECT forum_status FROM ".SFFORUMS." WHERE forum_id=".$forumid);
}

# ------------------------------------------------------------------
# sf_get_forum_rss_url()
#
# Returns the RSS URL for a forum (custom or standard)
# 	$forumid:		forum to return
#	$forumslug:		slug for the url
# ------------------------------------------------------------------
function sf_get_forum_rss_url($forumid, $forumslug)
{
	global $wpdb, $sfglobals;

	if (empty($forumid)) return '';

	$url = $wpdb->get_var("SELECT forum_rss FROM ".SFFORUMS." WHERE forum_id=".$forumid);
	if (empty($url))
    {
        $rssopt = sf_get_option('sfrss');
        if ($rssopt['sfrssfeedkey'])
            $url = sf_build_qurl('forum='.$forumslug, 'xfeed=forum', 'feedkey='.$sfglobals['member']['feedkey']);
        else
            $url = sf_build_qurl('forum='.$forumslug, 'xfeed=forum');
    }

	return $url;
}

# ------------------------------------------------------------------
# sf_get_topic_status_set()
#
# Returns the topic status set name for a forum
# 	$forumid:		forum to return
# ------------------------------------------------------------------
function sf_get_topic_status_set($forumid)
{
	global $wpdb;

	if(empty($forumid)) return '';
	return $wpdb->get_var(
			"SELECT topic_status_set FROM ".SFFORUMS." WHERE forum_id=".$forumid);
}

function sf_get_topic_status_from_forum($forumid, $statusflag)
{
	global $wpdb;

	if(!$forumid) return '';

	$flag='';
	$set=sf_get_topic_status_set($forumid);
	if($set != 0)
	{
		$flag=sf_get_topic_status_flag($set, $statusflag);
	}
	return $flag;
}

# ------------------------------------------------------------------
# sf_find_user_in_topic()
#
# Searches a topics posts to see if user has ever posted in it for
# the forums topic list icon
# 	$topicid:		topic to search
#	$userid:		user to look for
# %%FUTURE OPTIMISE%%
# ------------------------------------------------------------------
function sf_find_user_in_topic($topicid, $userid)
{
	global $wpdb;

	if(!$topicid || !$userid) return '';

	return $wpdb->get_col(
			"SELECT user_id
			 FROM ".SFPOSTS."
			 WHERE topic_id=".$topicid."
			 AND user_id=".$userid);
}

# ------------------------------------------------------------------
# sf_get_forum_from_topic()
#
# returng the firum id when only the topic is known
# 	$topicid:		topic to search
# ------------------------------------------------------------------

function sf_get_forum_from_topic($topicid)
{
	global $wpdb;

	if(!$topicid) return '';

	return $wpdb->get_var(
			"SELECT forum_id
			 FROM ".SFTOPICS."
			 WHERE topic_id=".$topicid);
}

# ------------------------------------------------------------------
# sf_get_forum_name()
#
# Returns forum name when only the slug is known
# 	$forumslug:		forum to return
# ------------------------------------------------------------------
function sf_get_forum_name($forumslug)
{
	global $wpdb;

	if(!$forumslug) return '';

	return $wpdb->get_var(
			"SELECT forum_name
			 FROM ".SFFORUMS."
			 WHERE forum_slug='".$forumslug."'");
}

# ------------------------------------------------------------------
# sf_get_forum_name_from_id()
#
# Returns forum name when only the slug is known
# 	$forumid:		forum to return
# ------------------------------------------------------------------
function sf_get_forum_name_from_id($forumid)
{
	global $wpdb;

	if(!$forumid) return '';

	return $wpdb->get_var(
			"SELECT forum_name
			 FROM ".SFFORUMS."
			 WHERE forum_id=".$forumid);
}

# ------------------------------------------------------------------
# sf_get_forum_slug()
#
# Returns forum slug when only the id is known
# 	$forumid:		forum to return
# ------------------------------------------------------------------
function sf_get_forum_slug($forumid)
{
	global $wpdb;

	if(!$forumid) return '';

	return $wpdb->get_var(
			"SELECT forum_slug
			 FROM ".SFFORUMS."
			 WHERE forum_id=".$forumid);
}

# ------------------------------------------------------------------
# sf_get_forum_id()
#
# Returns forum id when only the slug is known
# 	$forumslug:		forum to return
# ------------------------------------------------------------------

function sf_get_forum_id($forumslug)
{
	global $wpdb;

	if(!$forumslug) return '';

	return $wpdb->get_var(
			"SELECT forum_id
			 FROM ".SFFORUMS."
			 WHERE forum_slug='".$forumslug."'");
}

# ------------------------------------------------------------------
# sf_get_topics_forum_id()
#
# Returns forum id when only the topic id is known
# 	$topicid:		forum to return from topic record
# ------------------------------------------------------------------
function sf_get_topics_forum_id($topicid)
{
	global $wpdb;

	if(!$topicid) return '';

	return $wpdb->get_var(
			"SELECT forum_id
			 FROM ".SFTOPICS."
			 WHERE topic_id=".$topicid);
}

# ******************************************************************
# TOPIC/POST VIEW AND GENERAL DB FUNCTIONS
# ******************************************************************

# ------------------------------------------------------------------
# sf_get_combined_topics_and_posts()
#
# Returns a page of posts for specified topic
# 	$topicid:		topic and posts to load
# ------------------------------------------------------------------
function sf_get_combined_topics_and_posts($topicid)
{
	global $wpdb, $sfvars, $sfglobals;

	if(!$topicid) return '';

	# sadly have to grab the topic row first because we need obverride sort order if set
	$topic = $wpdb->get_row(
			"SELECT topic_id, topic_slug, ".SFTOPICS.".forum_id, topic_name, ".SFTOPICS.".post_count, topic_subs, topic_watches, topic_status, post_ratings, use_tags, blog_post_id,
			 forum_slug, forum_status, topic_status_set, topic_status_flag, user_id, use_tags
			 FROM ".SFTOPICS."
			 JOIN ".SFFORUMS." ON ".SFTOPICS.".forum_id = ".SFFORUMS.".forum_id
			 WHERE topic_id = ".$topicid.";", ARRAY_A);

	# quick permission check
 	if(!sf_can_view_forum($topic['forum_id'])) return '';

	# grab the tags if enabled for this forum
	if ($topic['use_tags'])
	{
		$sql = "SELECT ".SFTAGS.".tag_name, tag_slug
		FROM ".SFTAGMETA."
	 	JOIN ".SFTAGS." ON ".SFTAGMETA.".tag_id = ".SFTAGS.".tag_id
		WHERE ".SFTAGMETA.".topic_id = ".$topicid;
		$topic['tags'] = $wpdb->get_results($sql);
	}

	# now for the posts
	$ORDER="ASC"; # default
	if($sfglobals['display']['posts']['sortdesc']) $ORDER="DESC"; # global override

	$ppaged=$sfglobals['display']['posts']['perpage'];
	if(!$ppaged) $ppaged=20;

	if($sfvars['page'] == 1 ? $startlimit = 0 : $startlimit = ((($sfvars['page']-1) * $ppaged)));

	$topic['topic_page'] = $sfvars['page'];

	$tpages = ($topic['post_count'] / $ppaged);
	if(!is_int($tpages))
	{
		$tpages = intval($topic['post_count'] / $ppaged) +1;
	}
	$topic['topic_total_pages'] = $tpages;

	$LIMIT = ' LIMIT '.$startlimit.', '.$ppaged;

	$records = $wpdb->get_results(
			"SELECT ".SFPOSTS.".post_id, post_content, ".sf_zone_datetime('post_date').", UNIX_TIMESTAMP(post_date) as udate, ".SFPOSTS.".user_id, guest_name, guest_email,
			 post_status, post_pinned, post_index, post_edit,
			".SFMEMBERS.".display_name, admin, posts, signature, avatar, pm,
			 user_url, user_email, rating_id, vote_count, ratings_sum, ips, members
			 FROM ".SFPOSTS."
			 LEFT JOIN ".SFUSERS." ON ".SFPOSTS.".user_id = ".SFUSERS.".ID
			 LEFT JOIN ".SFMEMBERS." ON ".SFPOSTS.".user_id = ".SFMEMBERS.".user_id
			 LEFT JOIN ".SFPOSTRATINGS." ON ".SFPOSTRATINGS.".post_id = ".SFPOSTS.".post_id
			 WHERE topic_id = ".$topicid."
			 ORDER BY post_pinned DESC, ".SFPOSTS.".post_id ".$ORDER.$LIMIT, ARRAY_A);

	$topic['posts'] = $records;

	return $topic;
}

# ------------------------------------------------------------------
# sf_get_topic_record()
#
# Returns a single topic row
# 	$topicid:		topic_id of topic to return
# Note: No permission checking is performed
# ------------------------------------------------------------------
function sf_get_topic_record($topicid)
{
	global $wpdb, $session_topics;

	if(!$topicid) return '';

	# see if in the current session topics cache
	if($session_topics)
	{
		foreach($session_topics as $topic)
		{
			if($topic->topic_id == $topicid)
			{
				return $topic;
			}
		}
	}
	$sql=(
			"SELECT *
			 FROM ".SFTOPICS."
			 WHERE topic_id=".$topicid.";");
	$thistopic = $wpdb->get_row($sql);
	$session_topics[] = $thistopic;
	return $thistopic;
}

# ------------------------------------------------------------------
# sf_get_topic_record_from_slug()
#
# Returns a single topic row
# 	$topicslug:		topic_slug of topic to return
# Note: No permission checking is performed
# ------------------------------------------------------------------
function sf_get_topic_record_from_slug($topicslug)
{
	global $wpdb, $session_topics;

	if(!$topicslug) return '';

	# see if in the current session topics cache
	if($session_topics)
	{
		foreach($session_topics as $topic)
		{
			if($topic->topic_slug == $topicslug)
			{
				return $topic;
			}
		}
	}
	$sql=(
			"SELECT *
			 FROM ".SFTOPICS."
			 WHERE topic_slug='".$topicslug."';");
	$thistopic = $wpdb->get_row($sql);
	$session_topics[] = $thistopic;
	return $thistopic;
}

# ------------------------------------------------------------------
# sf_get_topic_record_from_blogpostid()
#
# Returns a single topic row
# 	$blogpostid:		blog post id to see if any linked topics
# Note: No permission checking is performed
# ------------------------------------------------------------------
function sf_get_topic_record_from_blogpostid($postid)
{
	global $wpdb, $session_topics;

	if (!$postid) return '';

	$sql = (
			"SELECT *
			 FROM ".SFTOPICS."
			 WHERE blog_post_id=".$postid);
	$thistopic = $wpdb->get_row($sql);
	return $thistopic;
}

# ------------------------------------------------------------------
# sf_get_topics_all()
#
# Returns complete recordset of topics
# 	$id_only:		limit recordset to topic_id and slug only
#	$asArray:		returb list as an aray
# Note: No permission checking is performed
# ------------------------------------------------------------------
function sf_get_topics_all($id_only=false, $asArray=false)
{
	global $wpdb;

	if($id_only ? $FROM='topic_id, topic_slug' : $FROM='*');

	$sql=("SELECT ".$FROM." FROM ".SFTOPICS);
	if($asArray) return $wpdb->get_results($sql, ARRAY_A);
	return $wpdb->get_results($sql);
}

# ------------------------------------------------------------------
# sf_topic_exists()
#
# Check the existence of a topic by id
# 	$topicid:		forum to check for
# ------------------------------------------------------------------
function sf_topic_exists($topicid)
{
	global $wpdb;

	if(empty($topicid)) return false;
	if($wpdb->get_var(
			"SELECT topic_name
			 FROM ".SFTOPICS."
			 WHERE topic_id=".$topicid))
	{
		return true;
	}
	return false;
}

# ------------------------------------------------------------------
# sf_get_topic_name()
#
# Returns topic name when only the topic slug is known
# 	$topicslug:		Topic to lookup
# ------------------------------------------------------------------
function sf_get_topic_name($topicslug)
{
	global $wpdb;

	if(!$topicslug) return '';

	return $wpdb->get_var(
			"SELECT topic_name
			 FROM ".SFTOPICS."
			 WHERE topic_slug='".$topicslug."'");
}

# ------------------------------------------------------------------
# sf_get_topic_slug()
#
# Returns topic slug when only the topic id is known
# 	$topicid:		Topic to lookup
# ------------------------------------------------------------------
function sf_get_topic_slug($topicid)
{
	global $wpdb;

	if(!$topicid) return '';

	return $wpdb->get_var(
			"SELECT topic_slug
			 FROM ".SFTOPICS."
			 WHERE topic_id=".$topicid);
}

# ------------------------------------------------------------------
# sf_get_topic_id()
#
# Returns topic id when only the topic slug is known
# 	$topicslug:		Topic to lookup
# ------------------------------------------------------------------
function sf_get_topic_id($topicslug)
{
	global $wpdb;

	if(!$topicslug) return '';

	return $wpdb->get_var(
			"SELECT topic_id
			 FROM ".SFTOPICS."
			 WHERE topic_slug='".$topicslug."'");
}

# ------------------------------------------------------------------
# sf_get_topic_name_from_id()
#
# Returns topic name when only the id is known
# 	$topicid:		topic to return
# ------------------------------------------------------------------
function sf_get_topic_name_from_id($topicid)
{
	global $wpdb;

	if(!$topicid) return '';

	return $wpdb->get_var(
			"SELECT topic_name
			 FROM ".SFTOPICS."
			 WHERE topic_id=".$topicid);
}


# ------------------------------------------------------------------
# sf_get_slugs_from_postid()
#
# Returns forum and topic slugs when only the post id is known
# 	$postid:		Post to lookup
# ------------------------------------------------------------------
function sf_get_slugs_from_postid($postid)
{
	global $wpdb;

	if(!$postid) return '';

	return $wpdb->get_row(
			"SELECT forum_slug, topic_slug, post_index
			 FROM ".SFPOSTS."
			 JOIN ".SFFORUMS." ON ".SFPOSTS.".forum_id = ".SFFORUMS.".forum_id
			 JOIN ".SFTOPICS." ON ".SFPOSTS.".topic_id = ".SFTOPICS.".topic_id
			 WHERE ".SFPOSTS.".post_id=".$postid.";");
}

# ------------------------------------------------------------------
# sf_get_posts_count_in_topic()
#
# Returns the post count from topic record
# 	$topicid:		Topic to lookup
# ------------------------------------------------------------------

function sf_get_posts_count_in_topic($topicid)
{
	global $wpdb;
	if(empty($topicid)) return 0;
	return $wpdb->get_var(
			"SELECT post_count
			 FROM ".SFTOPICS."
			 WHERE topic_id=".$topicid);
}

# ------------------------------------------------------------------
# sf_get_posts_count_in_linked_topic()
#
# Returns the post count of approved posts in topic
# 	$topicid:		Topic to lookup
#	$hidedupes		Hide duplicate comments
# ------------------------------------------------------------------
function sf_get_posts_count_in_linked_topic($topicid, $hidedupes)
{
	global $wpdb;
	if(empty($topicid)) return 0;

	$hide='';
	if($hidedupes) $hide = " AND comment_id IS NULL";

	return $wpdb->get_var(
		"SELECT COUNT(post_id)
		 FROM ".SFPOSTS."
		 WHERE topic_id=".$topicid." AND post_status=0".$hide);
}

function sf_update_topic_status_flag($statvalue, $topicid)
{
	global $wpdb;

	if(!$statvalue || !$topicid) return '';

	return $wpdb->query("UPDATE ".SFTOPICS." SET topic_status_flag=".$statvalue." WHERE topic_id=".$topicid);
}

# ******************************************************************
# NEW/UNREAD POST VIEWS DB FUNCTIONS
# ******************************************************************

# ------------------------------------------------------------------
# sf_get_admins_queued_posts()
#
# Returns the admins new post view
# ------------------------------------------------------------------
function sf_get_admins_queued_posts()
{
	global $wpdb, $sfvars;

	$newposts = '';
	$clause = '';

	$records = $wpdb->get_results(
			"SELECT ".SFWAITING.".forum_id, forum_slug, forum_name, topic_status_set, topic_id, ".SFWAITING.".post_count, ".SFWAITING.".post_id
			 FROM ".SFWAITING."
			 LEFT JOIN ".SFFORUMS." ON ".SFWAITING.".forum_id = ".SFFORUMS.".forum_id
			 ORDER BY forum_id;");

	if($records)
	{
		# now grab all of the post record we are going to need in one query
		$pcount = count($records);
		$done = 0;
		foreach ($records as $record)
		{
			$clause.= "(".SFTOPICS.".topic_id = ".$record->topic_id." AND ".SFPOSTS.".post_id >= ".$record->post_id.")";
			$done++;
			if($done < $pcount) $clause.= " OR ";
		}

		$preparedpostrecords = $wpdb->get_results(
				"SELECT ".SFPOSTS.".topic_id, post_content, post_index, ".SFPOSTS.".post_id, post_status, ".SFPOSTS.".user_id, ".SFMEMBERS.".display_name, guest_name, topic_slug, topic_status_flag, topic_name
				 FROM ".SFPOSTS."
				 LEFT JOIN ".SFTOPICS." ON ".SFPOSTS.".topic_id = ".SFTOPICS.".topic_id
				 LEFT JOIN ".SFMEMBERS." ON ".SFPOSTS.".user_id = ".SFMEMBERS.".user_id
				 WHERE ".$clause."
				 ORDER BY post_id;");

		$sfvars['queue'] = count($preparedpostrecords);


		$newposts = array();
		$findex=-1;
		$pindex=0;
		$tindex=0;

		foreach($records as $record)
		{
			# Check this still has posts in it and they were not removed (it happens)
			$postrecords='';
			foreach($preparedpostrecords as $prepared)
			{
				if($prepared->topic_id == $record->topic_id)
				{
					$postrecords[] = $prepared;
				}
			}
			# So - were they removed? if so donlt add them to the array and remove them from sfwaiting

			if($postrecords == '')
			{
				sf_remove_from_waiting(true, $record->topic_id, $record->post_id);
				continue;
			}

			$forumid=$record->forum_id;
			if($findex == -1 || $newposts[$findex]['forum_id'] != $forumid)
			{
				$findex++;
				$tindex=0;
				$pindex=0;
				$newposts[$findex]['forum_id']=$record->forum_id;
				$newposts[$findex]['forum_name']=$record->forum_name;
				$newposts[$findex]['forum_slug']=$record->forum_slug;
				$newposts[$findex]['topic_status_set']=$record->topic_status_set;
			}

			$newposts[$findex]['topics'][$tindex]['topic_id']=$record->topic_id;
			$newposts[$findex]['topics'][$tindex]['post_id']=$record->post_id;

			# isolate the post records for current topic
			$postrecords='';
			foreach($preparedpostrecords as $prepared)
			{
				if($prepared->topic_id == $record->topic_id)
				{
					$postrecords[] = $prepared;
				}
			}

			if($postrecords)
			{
				$newposts[$findex]['topics'][$tindex]['post_count']=count($postrecords);
				$pindex=0;
				foreach($postrecords as $postrecord)
				{
					$newposts[$findex]['topics'][$tindex]['topic_slug']=$postrecord->topic_slug;
					$newposts[$findex]['topics'][$tindex]['topic_status_flag']=$postrecord->topic_status_flag;
					$newposts[$findex]['topics'][$tindex]['topic_name']=$postrecord->topic_name;

					$newposts[$findex]['topics'][$tindex]['posts'][$pindex]['post_id']=$postrecord->post_id;
					$newposts[$findex]['topics'][$tindex]['posts'][$pindex]['post_status']=$postrecord->post_status;
					$newposts[$findex]['topics'][$tindex]['posts'][$pindex]['post_index']=$postrecord->post_index;
					$newposts[$findex]['topics'][$tindex]['posts'][$pindex]['post_content']=$postrecord->post_content;
					$newposts[$findex]['topics'][$tindex]['posts'][$pindex]['user_id']=$postrecord->user_id;
					if(empty($postrecord->user_id))
					{
						$newposts[$findex]['topics'][$tindex]['posts'][$pindex]['display_name']=$postrecord->guest_name;
						$thisuser = 'Guest';
					} else {
						$newposts[$findex]['topics'][$tindex]['posts'][$pindex]['display_name']=$postrecord->display_name;
						$thisuser = 'Member';
					}
					$newposts[$findex]['topics'][$tindex]['posts'][$pindex]['user_type']=$thisuser;
					$pindex++;
				}
			}
			$tindex++;
		}
	}

	# if no new posts then housekeep sfwaiting
	if(!$newposts) $wpdb->query("TRUNCATE ".SFWAITING);

	return $newposts;
}

# ------------------------------------------------------------------
# sf_combined_new_posts_list()
#
# Extend new post list to include all required data
#	$posts:		Posts array (forumid and topicid)
#	$forumsort	Sort list into group/forum sequence
# ------------------------------------------------------------------
function sf_combined_new_posts_list($posts, $forumsort=false)
{
	global $wpdb;

	$clause='';
	$record='';

	if($posts)
	{
		$pcount = count($posts);
		$done = 0;

		foreach ($posts as $post)
		{
			if (sf_can_view_forum($post->forum_id, true))
			{
                if ($clause != '') $clause.= ' OR ';
				$clause.= "(".SFTOPICS.".forum_id=".$post->forum_id." AND ".SFTOPICS.".topic_id=".$post->topic_id.")";
				$done++;
			}
        }

		# just return if nothing visible
		if ($clause == '') return '';

		if($forumsort)
		{
			$groupjoin = " JOIN ".SFGROUPS." ON ".SFFORUMS.".group_id = ".SFGROUPS.".group_id ";
			$orderby = " ORDER BY group_seq, forum_seq";
			$groupname = ', group_name';
		} else {
			$groupjoin = '';
			$orderby = ' ORDER BY post_id DESC';
			$groupname = '';
		}

		$record = $wpdb->get_results(
				"SELECT ".SFFORUMS.".forum_id, forum_name, forum_slug,
				 topic_name, topic_slug, ".SFTOPICS.".post_id, ".SFTOPICS.".topic_id, topic_status_set,
				 post_status, UNIX_TIMESTAMP(".SFPOSTS.".post_date) as udate, post_index, ".SFPOSTS.".user_id ".$groupname."
				 FROM ".SFFORUMS."
				 JOIN ".SFTOPICS." ON ".SFFORUMS.".forum_id = ".SFTOPICS.".forum_id
				 LEFT JOIN ".SFPOSTS." ON ".SFTOPICS.".post_id = ".SFPOSTS.".post_id
				 ".$groupjoin."
				 WHERE ".$clause.$orderby, ARRAY_A);
	}
	return $record;
}

# ------------------------------------------------------------------
# sf_get_users_new_post_list()
#
# Returns recordset of current users new-post-list
# 	$limit:		limit to x number of records
# ------------------------------------------------------------------
function sf_get_users_new_post_list($limit)
{
	global $current_user, $sfglobals;

	if ($current_user->member)
	{
		$newpostlist = $sfglobals['member']['newposts'];
		if($current_user->newpostlist == false)
		{
			$newpostlist = sf_update_users_newposts($newpostlist);
		}

		# we have a live user so construct SQL if anything in newpostslist
		if ($newpostlist['topics'][0] != 0)
		{
			$wanted = $limit;
			$where = ' WHERE';
			if (count($newpostlist['topics']) < $limit) $limit = count($newpostlist['topics']);
			for ($x=0; $x<$limit; $x++)
			{
				$where.= " topic_id=".$newpostlist['topics'][$x];
				if ($x != $limit-1) $where.= " OR";
			}

			$recordset1 = sf_get_users_new_post_list_db($where, '');

			# try and marry the extra count if not enough to satisfy $limit
			if ($limit < $wanted)
			{
				$limit = " LIMIT ".$wanted;
				$where = " WHERE post_status = 0 ";
				$recordset2 = sf_get_users_new_post_list_db($where, $limit);
				if ($recordset2)
				{
					for ($x=0; $x<count($recordset2); $x++)
					{
						if (!in_array($recordset2[$x]->topic_id, $newpostlist['topics']))
						{
							$recordset1[]=$recordset2[$x];
						}
						if (count($recordset1) == $wanted) break;
					}
				}
			}
			return sf_filter_new_post_list($recordset1);
		}
	}
	# but if not a member, empty post list of members query didnlt reach limit...
	if ($current_user->guest || $newpostlist['topics'][0] == 0)
	{
		$limit = " LIMIT ".$limit;
		$where = " WHERE post_status = 0 ";
		$recordset1 = sf_get_users_new_post_list_db($where, $limit);
		return sf_filter_new_post_list($recordset1);
	}
}

# ------------------------------------------------------------------
# sf_get_users_new_post_list_db()
#
# Support: Returns recordset of current users new-post-list
#	$where:		Option where clause on topic id
# 	$limit:		limit to x number of records
# ------------------------------------------------------------------
function sf_get_users_new_post_list_db($where, $limit)
{
	global $wpdb, $current_user;

	$where2 = '';
	if (!$current_user->forumadmin)
	{
		# limit to viewable forums based on permissions
		$forums = sf_get_forum_memberships($current_user->ID);
		if ($forums)
		{
			$forum_ids = '';
			foreach ($forums as $forum)
			{
				if (sf_can_view_forum($forum->forum_id))
				{
					$forum_ids[] = $forum->forum_id;
				}
			}
		} else {
			return '';
		}

		# create where clause based on forums that current user can view
		if ($forum_ids != '')
		{
            if ($where != '')
            {
			    $where2 = " AND ";
            } else {
                $where2 = " WHERE ";
            }
            $where2.= SFPOSTS.".forum_id IN (" . implode(",", $forum_ids) . ") = 1 ";
		} else {
            return '';
		}
	}

	$records = $wpdb->get_results(
			"SELECT DISTINCT forum_id, topic_id
			 FROM ".SFPOSTS
			 .$where.$where2."
			 ORDER BY post_id DESC"
			 .$limit.";");

	return $records;
}

# ------------------------------------------------------------------
# sf_filter_new_post_list()
#
# Support: Returns filtered list that current user has permissions to
#	$recordset:	Full list of forum/topics
# ------------------------------------------------------------------
function sf_filter_new_post_list($recordset)
{
	if(!$recordset) return '';

	$rlist = array();
	$x = 0;

	foreach($recordset as $record)
	{
		$rlist[$x]->forum_id=$record->forum_id;
		$rlist[$x]->topic_id=$record->topic_id;
		$x++;
	}
	return $rlist;
}

# ******************************************************************
# NEW POSTS FROM WAITING - DASHBOARD
# ******************************************************************

# ------------------------------------------------------------------
# sf_get_unread_forums()
#
# Returns list from the waiting table (Admins queue)
# ------------------------------------------------------------------
function sf_get_unread_forums()
{
	global $wpdb;

	return $wpdb->get_results(
			"SELECT topic_id, ".SFWAITING.".forum_id, forum_slug, forum_name, group_id, ".SFWAITING.".post_count, ".SFWAITING.".post_id, topic_status_set
			 FROM ".SFFORUMS."
			 JOIN ".SFWAITING." ON ".SFFORUMS.".forum_id = ".SFWAITING.".forum_id
			 WHERE ".SFWAITING.".post_count > 0
			 ORDER BY forum_id, topic_id");
}

# ------------------------------------------------------------------
# sf_get_awaiting_approval()
#
# Count of posts currently awaiting moderation
# ------------------------------------------------------------------
function sf_get_awaiting_approval()
{
	global $wpdb;
	return $wpdb->get_var(
			"SELECT COUNT(post_id) AS cnt
			 FROM ".SFPOSTS."
			 WHERE post_status=1");
}

# ------------------------------------------------------------------
# sf_topic_in_queue()
#
# returns true if the topic is in the admin queue
# 	$topicid		topic being tested
# ------------------------------------------------------------------
function sf_topic_in_queue($topicid)
{
	global $wpdb;

	if(!$topicid) return '';

	return $wpdb->get_var(
			"SELECT post_count
			 FROM ".SFWAITING."
			 WHERE topic_id=".$topicid);
}

# ******************************************************************
# STATISTICS VIEWS DB FUNCTIONS
# ******************************************************************

# ------------------------------------------------------------------
# sf_get_online_total()
#
# Returns total number of users currently tagged as online
# ------------------------------------------------------------------
function sf_get_online_total()
{
	global $wpdb;
	return $wpdb->get_var("SELECT COUNT(id) FROM ".SFTRACK);
}

# ------------------------------------------------------------------
# sf_get_members_online()
#
# Returns list of members currently tagged as online
# ------------------------------------------------------------------
function sf_get_members_online()
{
	global $wpdb;

	return $wpdb->get_results("
			SELECT trackuserid, display_name, forum_id, topic_id FROM ".SFTRACK."
			JOIN ".SFMEMBERS." ON ".SFTRACK.".trackuserid = ".SFMEMBERS.".user_id
			ORDER BY trackuserid");
}

# ------------------------------------------------------------------
# sf_is_online()
#
# Returns true if member is currently tagged as online
# ------------------------------------------------------------------
function sf_is_online($userid)
{
	global $wpdb, $session_online;

	if(!$userid) return '';

	if(!isset($session_online))
	{
		$session_online = $wpdb->get_col("SELECT trackuserid FROM ".SFTRACK);
	}
	if(in_array($userid, $session_online)) return true;
	return false;
}


# ------------------------------------------------------------------
# sf_get_stats_counts()
#
# Returns stats on group/forum/topic/post count
# ------------------------------------------------------------------
function sf_get_stats_counts()
{
	global $wpdb;

	$cnt->groups = 0;
	$cnt->forums = 0;
	$cnt->topics = 0;
	$cnt->posts = 0;

	$groupid='';

	$forums = $wpdb->get_results("SELECT group_id, forum_id, topic_count, post_count FROM ".SFFORUMS." ORDER BY group_id");
	if($forums)
	{
		foreach($forums as $forum)
		{
			if($forum->group_id != $groupid)
			{
				$groupid=$forum->group_id;
				$cnt->groups++;
			}
			$cnt->forums++;
			$cnt->topics+=$forum->topic_count;
			$cnt->posts+=$forum->post_count;
		}
	}
	return $cnt;
}

# ------------------------------------------------------------------
# sf_get_post_stats()
#
# Returns stats on posts (admins/moderators and members and updates
# the guest count
# ------------------------------------------------------------------
function sf_get_post_stats()
{
	global $wpdb;

	$sfcontrols = sf_get_option('sfcontrols');

	# if the hour has passed or been reset
	if($sfcontrols['hourflag'] != date('G'))
	{
		# get admins and moderators
		$records1 = $wpdb->get_results("
			SELECT user_id, display_name, posts, admin, moderator
			FROM ".SFMEMBERS."
			WHERE admin=1 OR moderator=1
			ORDER BY admin DESC, moderator DESC, posts DESC;");

		# now get members
		if(empty($sfcontrols['showtopcount']) || $sfcontrols['showtopcount']==0) $sfcontrols['showtopcount']=6;
		$records2 = $wpdb->get_results("
			SELECT SQL_CALC_FOUND_ROWS user_id, display_name, posts, admin, moderator
			FROM ".SFMEMBERS."
			WHERE admin=0 AND moderator=0
			ORDER BY posts DESC
			LIMIT 0,".$sfcontrols['showtopcount'].";");

		# save the members count
		$sfcontrols['membercount'] = $wpdb->get_var("SELECT FOUND_ROWS()");

		# Finally get guests
		$guests = $wpdb->get_col("
			SELECT DISTINCT guest_name
			FROM ".SFPOSTS."
			WHERE guest_name !='';");

		# save the guest count
		$sfcontrols['guestcount'] = count($guests);

		# merge and save the records
		$stats = array_merge($records1, $records2);
		$sfcontrols['statslist'] = $stats;

		# reset the hour flag and save the controls
		$sfcontrols['hourflag'] = date('G');
		sf_update_option('sfcontrols', $sfcontrols);
	} else {
		$stats = $sfcontrols['statslist'];
	}
	# return results
	return $stats;
}

# ------------------------------------------------------------------
# sf_guests_browsing()
#
# Calculates how many guests are browsing current forum or topic
# ------------------------------------------------------------------
function sf_guests_browsing()
{
	global $sfvars, $wpdb;

	$where='';

	if($sfvars['pageview']=='forum') $where = "forum_id=".$sfvars['forumid'];
	if($sfvars['pageview']=='topic') $where = "topic_id=".$sfvars['topicid'];
	if($where=='') return;

	return $wpdb->get_var("
			SELECT COUNT(id)
			FROM ".SFTRACK." WHERE trackuserid=0 AND ".$where);
}

# ------------------------------------------------------------------
# sf_update_max_online()
#
# Updates max online setting if exceeded and returns value
# ------------------------------------------------------------------
function sf_update_max_online($current)
{
	$sfcontrols = sf_get_option('sfcontrols');
	$max = $sfcontrols['maxonline'];
	if(empty($max)) $max = 0;

	if($current > $max)
	{
		$sfcontrols['maxonline'] = $current;
		sf_update_option('sfcontrols', $sfcontrols);
		return $current;
	}
	return $max;
}

# ******************************************************************
# FULL TOPIC SEARCH VIEW DB FUNCTIONS
# ******************************************************************

# ------------------------------------------------------------------
# sf_get_combined_full_topic_search()
#
# Grabs all forums and their topics in the search result.
# Also performs the full search for a members postings
# ------------------------------------------------------------------
function sf_get_combined_full_topic_search()
{
	global $wpdb, $sfvars, $current_user, $sfglobals;

	$sfvars['searchresults'] = 0;

	# how many topics per page?
	$tpaged = $sfglobals['display']['topics']['perpage'];
	if(!$tpaged) $tpaged=20;

	if($sfvars['searchpage'] == 1)
	{
		$startlimit = 0;
	} else {
		$startlimit = ((($sfvars['searchpage']-1) * $tpaged));
	}

	$LIMIT = ' LIMIT '.$startlimit.', '.$tpaged;
	$ORDER = ' ORDER BY topic_id DESC';

	# for admins search all forums, for users check permissions
	$where2 = '';
	if (!$current_user->forumadmin)
	{
		# limit to viewable forums based on permissions
		$forums = sf_get_forum_memberships($current_user->ID);
		if ($forums)
		{
			$forum_ids = '';
			foreach ($forums as $forum)
			{
				if (sf_can_view_forum($forum->forum_id))
				{
					$forum_ids[] = $forum->forum_id;
				}
			}
		} else {
			return '';
		}

		# create where clause based on forums that current user can view
		if ($forum_ids != '')
		{
			$where2 = " AND ".SFTOPICS.".forum_id IN (" . implode(",", $forum_ids) . ") ";
		} else {
            return '';
		}
	}

	$searchvalue=urldecode($sfvars['searchvalue']);
	if(empty($searchvalue))
	{
		return '';
	}

	if($sfvars['searchtype'] == 8) {

		# topics user has posted in
		$userid = $sfvars['searchvalue'];

		$records = sf_query_results(
				"SELECT SQL_CALC_FOUND_ROWS DISTINCT
				 ".SFTOPICS.".topic_id, topic_name, topic_slug, ".SFTOPICS.".forum_id, ".sf_zone_datetime('topic_date').", topic_status, topic_status_flag, ".SFTOPICS.".post_id, ".SFTOPICS.".post_count,
				 forum_name, forum_slug
				 FROM ".SFTOPICS."
				 JOIN ".SFFORUMS." ON ".SFTOPICS.".forum_id = ".SFFORUMS.".forum_id
				 JOIN ".SFPOSTS." ON ".SFTOPICS.".topic_id = ".SFPOSTS.".topic_id
				 WHERE ".SFPOSTS.".user_id = ".$userid.$where2.$ORDER.$LIMIT.";", ARRAY_A);

	} else if($sfvars['searchtype'] == 9) {

		# topics started by user
		$userid = $sfvars['searchvalue'];

		$records = sf_query_results(
				"SELECT SQL_CALC_FOUND_ROWS DISTINCT
				 ".SFTOPICS.".topic_id, topic_name, topic_slug, ".SFTOPICS.".forum_id, ".sf_zone_datetime('topic_date').", topic_status, topic_status_flag, ".SFTOPICS.".post_id, ".SFTOPICS.".post_count,
				 forum_name, forum_slug, post_content
				 FROM ".SFTOPICS."
				 JOIN ".SFFORUMS." ON ".SFTOPICS.".forum_id = ".SFFORUMS.".forum_id
				 JOIN ".SFPOSTS." ON ".SFTOPICS.".topic_id = ".SFPOSTS.".topic_id
				 WHERE ".SFPOSTS.".user_id = ".$userid.$where2." AND ".SFPOSTS.".post_index=1 ".$ORDER.$LIMIT.";", ARRAY_A);

	} elseif($sfvars['searchinclude'] == 4) {

		# it's a tag search
		$searchtag = sf_create_slug(urldecode($searchvalue), 'tag');
		$WHERE = SFTOPICS.".topic_id IN (SELECT topic_id FROM ".SFTAGMETA." JOIN ".SFTAGS." ON ".SFTAGMETA.".tag_id = ".SFTAGS.".tag_id
			WHERE tag_slug = '".$searchtag."') ";
			$records = sf_query_results(
				"SELECT SQL_CALC_FOUND_ROWS DISTINCT
				".SFTOPICS.".topic_id, topic_name, topic_slug, ".SFTOPICS.".forum_id, ".sf_zone_datetime('topic_date').", topic_status, topic_status_flag, ".SFTOPICS.".post_id, ".SFTOPICS.".post_count,
				forum_name, forum_slug, post_content
				FROM ".SFTOPICS."
				JOIN ".SFFORUMS." ON ".SFTOPICS.".forum_id = ".SFFORUMS.".forum_id
				JOIN ".SFPOSTS." ON ".SFTOPICS.".topic_id = ".SFPOSTS.".topic_id
				WHERE ".$WHERE." AND ".SFPOSTS.".post_index=1 ".$where2.$ORDER.$LIMIT.";", ARRAY_A);

	} else {

		$searchterm = sf_construct_search_term($searchvalue, $sfvars['searchtype']);

		switch($sfvars['searchinclude'])
		{
			case 1:
				$MATCH = "(MATCH(".SFPOSTS.".post_content) AGAINST ('".esc_sql(like_escape($searchterm))."' IN BOOLEAN MODE) OR MATCH(".SFTOPICS.".topic_name) AGAINST ('".esc_sql(like_escape($searchterm))."' IN BOOLEAN MODE)) ";
				break;
			case 2:
				$MATCH = "MATCH(".SFPOSTS.".post_content) AGAINST ('".esc_sql(like_escape($searchterm))."' IN BOOLEAN MODE) ";
				break;
			case 3:
				$MATCH = "MATCH(".SFTOPICS.".topic_name) AGAINST ('".esc_sql(like_escape($searchterm))."' IN BOOLEAN MODE) ";
				break;
		}

		$records = sf_query_results(
				"SELECT SQL_CALC_FOUND_ROWS DISTINCT
				 ".SFTOPICS.".topic_id, topic_name, topic_slug, ".SFTOPICS.".forum_id, ".sf_zone_datetime('topic_date').", topic_status, topic_status_flag, ".SFTOPICS.".post_id, ".SFTOPICS.".post_count,
				 forum_name, forum_slug, post_status
				 FROM ".SFTOPICS."
				 JOIN ".SFFORUMS." ON ".SFTOPICS.".forum_id = ".SFFORUMS.".forum_id
				 JOIN ".SFPOSTS." ON ".SFTOPICS.".topic_id = ".SFPOSTS.".topic_id
				 WHERE ".$MATCH.$where2.$ORDER.$LIMIT.";", ARRAY_A);
	}

	if($records)
	{
		$sfvars['searchresults'] = $wpdb->get_var("SELECT FOUND_ROWS()");

		$sfdisplay=array();
		$sfdisplay=sf_get_option('sfdisplay');

		# do we need a post tooltip on the topic link? (Not possible on topics user hs posted in type search)
		if($sfdisplay['topics']['posttip'] && $sfvars['searchtype'] != 8 && $current_user->sfaccess)
		{
			for($x=0; $x<count($records); $x++)
			{
				if (sf_can_view_forum($records[$x]['forum_id']))
				{
					$records[$x]['post_tip']=sf_filter_tooltip_display($records[$x]['post_content'], $records[$x]['post_status']);
					$records[$x]['post_content']='';
				}
			}
		}
	}

	return $records;
}

# ******************************************************************
# USER RELATED DB FUNCTIONS
# ******************************************************************

# ------------------------------------------------------------------
# sf_create_member_data()
#
# Filter Call
# On user registration sets up the new 'members' data row
#	$userid:		Passed in to filter
# ------------------------------------------------------------------
function sf_create_member_data($userid)
{
	global $wpdb;

	if(!$userid) return '';

	# Grab the data we need
	$sfprofile = sf_get_option('sfprofile');
	$user = $wpdb->get_row("SELECT user_login, display_name FROM ".SFUSERS." WHERE ID=".$userid);

	# Display Name validation
	$display_name = '';

	# use first and last name (or just one if only one available)
	if($sfprofile['nameformat'] == 3)
	{
		$first = get_user_meta($userid, 'first_name', true);
		$last  = get_user_meta($userid, 'last_name', true);
		if(!empty($first) || !empty($last))
		{
			$display_name = trim($first.' '.$last);
		}
	}

	# use the login name (which is also the default if 1 or 3 fail)
	if(empty($display_name) || $sfprofile['nameformat'] == 2)
	{
		$display_name = $user->user_login;
	}

	# use the WP recorded display name
	if($sfprofile['nameformat'] == 1)
	{
		$display_name = $user->display_name;
	}

	# use default login name if all else failed - this MUST exist
	if(empty($display_name)) $display_name = $user->user_login;
	$display_name = sf_filter_name_save($display_name);

	$admin = 0;
	$moderator = 0;
	$pm = 0;
	$avatar = 'a:1:{s:8:"uploaded";s:0:"";}';
	$signature = '';
	$posts = -1;
	$lastvisit = current_time('mysql');
	$checktime = current_time('mysql');
	$subscribe = '';
	$buddies = '';
	$watches = '';
	$newposts = '';
	$posts_rated = '';
	$admin_options = '';

    $useropts = array();
	$sfmemberopts = sf_get_option('sfmemberopts');
    if ($sfmemberopts['sfautosub'])
    {
        $useropts['autosubpost'] = 1;
    } else {
        $useropts['autosubpost'] = 0;
    }
    $useropts['hidestatus'] = 0;
    $useropts['timezone'] = sf_get_option('sfzone');
    if(empty($useropts['timezone'])) $useropts['timezone']=0;
    $useropts['editor'] = 1;
    $useropts['pmemail'] = 1;
    $useropts['namesync'] = 1;
	$sfmemberopts = sf_get_option('sfmemberopts');
	if ($sfmemberopts['sfautosub']) $useropts['autosubpost'] = 1;

	$user_options = serialize($useropts);

    # generate feedkey
    $feedkey = sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
                mt_rand( 0, 0x0fff ) | 0x4000,
                mt_rand( 0, 0x3fff ) | 0x8000,
                mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) );

	# save initial record
	$sql = "INSERT INTO ".SFMEMBERS."
		(user_id, display_name, admin, moderator, pm, avatar, signature, posts, lastvisit, checktime, subscribe, buddies, watches, newposts, posts_rated, admin_options, user_options, feedkey)
		VALUES
		({$userid}, '{$display_name}', {$admin}, {$moderator}, {$pm}, '{$avatar}', '{$signature}', {$posts}, '{$lastvisit}', '{$checktime}', '{$subscribe}', '{$buddies}', '{$watches}', '{$newposts}', '{$posts_rated}', '{$admin_options}', '{$user_options}', '{$feedkey}')";
	$wpdb->query($sql);

	# insert new user into usergroup based on wp role
	sf_map_role_to_ug($userid);

	return;
}

# ------------------------------------------------------------------
# sf_update_member_data()
#
# Filter Call
# On user wp profile updates, check if any spf stuff needs updating
#	$userid:		Passed in to filter
# ------------------------------------------------------------------
function sf_update_member_data($userid)
{
	global $wpdb;

	if(!$userid) return '';

	# are we syncing display names between WP and SPF?
	$options = sf_get_member_item($userid, 'user_options');
	if ($options['namesync'] == true)
	{
		$display_name = $wpdb->get_var("SELECT display_name FROM ".SFUSERS." WHERE ID=".$userid);
		sf_update_member_item($userid, 'display_name', $display_name);
	}

	return;
}

# ------------------------------------------------------------------
# sf_map_role_to_ug()
#
# helper function
# update usergroup memberships based on wp role
#	$userid:		id of user to check memberships
# ------------------------------------------------------------------
function sf_map_role_to_ug($userid, $role='')
{
    # make sure user has been created first since wp role hook fires before create hook
	if (!$userid || !sf_get_member_item($userid, 'user_id')) return '';

	# get the user's wp role
    if (empty($role))
    {
    	$user = new WP_User($userid);
    	$roles = $user->roles;
    } else {
        $roles = (array) $role;
    }

	# grab the user group for that wp role
	# if the role doesnt have a mapping, use the defaul user group for new members
    foreach ($roles as $role)
    {
    	$value = sf_get_sfmeta('default usergroup', $role);
    	if (!empty($value))
    	{
    		$ug = $value[0]['meta_value'];
    	} else {
    		$value = sf_get_sfmeta('default usergroup', 'sfmembers');
    		$ug = $value[0]['meta_value'];
    	}

    	sfc_add_membership($ug, $userid);
    }

	return;
}

# ------------------------------------------------------------------
# sf_delete_member_data()
#
# Filter Call
# On user deletion remove 'members' data row
#	$userid:		Passed in to filter
# ------------------------------------------------------------------
function sf_delete_member_data($userid)
{
	global $wpdb;

	if(!$userid) return '';

	# 1: get users email address
	$user_email = $wpdb->get_var("SELECT user_email FROM ".SFUSERS." WHERE ID=".$userid);

	# 2: get the users display name from members table
	$display_name = sf_get_member_item($userid, 'display_name');

	# 3: Set user name and email to guest name and meail in all of their posts
	$wpdb->query("UPDATE ".SFPOSTS." SET user_id=NULL, guest_name='".$display_name."', guest_email='".$user_email."' WHERE user_id=".$userid);

	# 4: Remove PM messages
	$wpdb->query("DELETE FROM ".SFMESSAGES." WHERE to_id=".$userid." OR from_id=".$userid);

	# 5: Remove subscriptions
	$subs = sf_get_member_item($userid, 'subscribe');
	if (!empty($subs))
	{
		foreach ($subs as $sub)
		{
			sf_remove_subscription($sub, $userid);
		}
	}

	# 6: Remove watches
	$watches = sf_get_member_item($userid, 'watches');
	if (!empty($watches))
	{
		foreach ($watches as $watch)
		{
			sf_remove_watch($watch, $userid);
		}
	}

	# 7: Remove posts rated
	$ratings = sf_get_member_item($userid, 'posts_rated');
	if (!empty($ratings))
	{
		foreach ($ratings as $postid)
		{
			sf_remove_postrated($postid, $userid);
		}
	}

    # 8: Remove from recent members list if present
    sf_remove_newuser($userid);

	# 8: Remove from Members table
	$wpdb->query("DELETE FROM ".SFMEMBERS." WHERE user_id=".$userid);

	# 9: Remove user group memberships
	$wpdb->query("DELETE FROM ".SFMEMBERSHIPS." WHERE user_id=".$userid);

	return;
}

# ------------------------------------------------------------------
# sf_track_logout()
#
# Filter Call
# Sets up the last visited upon user logout
# ------------------------------------------------------------------
function sf_track_logout()
{
	global $wpdb, $current_user;

	# re-use this for updating lastvisit (time at logout)
	sf_set_last_visited($current_user->ID);
	$wpdb->query("DELETE FROM ".SFTRACK." WHERE trackuserid=".$current_user->ID);
	sf_destroy_users_newposts($current_user->ID);

	return;
}

# ------------------------------------------------------------------
# sf_get_user_id_from_display_name()
#
# returns the id of a user
#	$display_name:		User to lookup
# ------------------------------------------------------------------
function sf_get_user_id_from_display_name($display_name)
{
	global $wpdb;

	if(!$display_name) return '';

	return $wpdb->get_var(
			"SELECT user_id
			 FROM ".SFMEMBERS."
			 WHERE display_name='".$display_name."'");
}

# ------------------------------------------------------------------
# sf_get_user_id_from_user_login()
#
# returns the id of a user
#	$user_login:		User to lookup
# ------------------------------------------------------------------
function sf_get_user_id_from_user_login($login_name)
{
	global $wpdb;

	if(!$login_name) return '';

	return $wpdb->get_var(
			"SELECT ID
			 FROM ".SFUSERS."
			 WHERE user_login='".$login_name."'");
}

# ------------------------------------------------------------------
# sf_is_subscribed()
#
# determine if user already subscribed to topic
#	$userid:		User being looked up
#	$topicid:		Topic subscribed to
# ------------------------------------------------------------------
function sf_is_subscribed($userid, $topicid)
{
	global $wpdb;

	if(!$userid || !$topicid) return '';

	$list = sf_get_member_item($userid, 'subscribe');
	if (empty($list))
	{
		return false;
	}
	$found = false;
	if (in_array($topicid, $list)) $found = true;
	return $found;
}

# ------------------------------------------------------------------
# sf_is_watching()
#
# determine if user already watcing a topic
#	$userid:		User being looked up
#	$topicid:		Topic watching
# ------------------------------------------------------------------
function sf_is_watching($userid, $topicid)
{
	global $wpdb;

	if(!$userid || !$topicid) return '';

	$list = sf_get_member_item($userid, 'watches');
	if (empty($list))
	{
		return false;
	}

	$found = false;
	if (in_array($topicid, $list)) $found = true;
	return $found;
}

# ******************************************************************
# SAVE ITEM FUNCTIONS
# ******************************************************************

# ------------------------------------------------------------------
# sf_save_edited_post()
#
# Saves a forum post following an edit in the UI
# Values in POST variables
# ------------------------------------------------------------------
function sf_save_edited_post()
{
	global $wpdb, $current_user;

	# post content
	$postcontent = $_POST['postitem'];

	sf_process_hook('sf_hook_pre_edit_post_save', array($_POST["pid"], $postcontent, $current_user->ID));

	$postcontent = sf_filter_content_save($postcontent, 'edit');

	# post edit array
	$postedit = array();
	$pedit = stripslashes($_POST['pedit']);
	if(!empty($pedit))
	{
		$postedit = unserialize($pedit);
	}
	$x = count($postedit);
	$edittime = current_time('mysql');
	$postedit[$x]['by'] = sf_filter_name_save($current_user->display_name);
	$postedit[$x]['at'] = strtotime($edittime);
	$postedit = serialize($postedit);

	$sql = "UPDATE ".SFPOSTS." SET post_content='".$postcontent."', post_edit='".$postedit."' WHERE post_id=".sf_esc_int($_POST["pid"]);

	if($wpdb->query($sql) === false)
	{
		update_sfnotice('sfmessage', '1@'.__("Update Failed!", "sforum"));
	} else {
		update_sfnotice('sfmessage', '0@'.__("Updated Post Saved", "sforum"));
	}

	# is it a blog linked topic post edit?
	if(sf_esc_int($_POST['blogpid']) && $_POST['sfedit'])
	{
		$ID = sf_esc_int($_POST['blogpid']);
		$post_content = $postcontent;

		$post = compact('ID', 'post_content');
		wp_update_post($post);
	}

	sf_process_hook('sf_hook_post_edit_post_save', array($_POST["pid"], $postcontent, $current_user->ID));

	return;
}

# ------------------------------------------------------------------
# sf_save_edited_topic()
#
# Saves a topic title following an edit in the UI
# Values in POST variables
# ------------------------------------------------------------------
function sf_save_edited_topic()
{
	global $wpdb, $sfglobals;

	$topicname = sf_filter_title_save($_POST['topicname']);

	if(empty($_POST['topicslug']))
	{
		$topicslug = sf_create_slug(sf_esc_str($_POST['topicname']), 'topic');
		if(empty($topicslug)) $topicslug = 'topic-'.sf_esc_int($_POST['tid']);
	} else {
		$topicslug = sf_esc_str($_POST['topicslug']);
	}

	$sql = 'UPDATE '.SFTOPICS.' SET topic_name="'.$topicname.'", topic_slug="'.$topicslug.'" WHERE topic_id='.sf_esc_int($_POST['tid']);

	if($wpdb->query($sql) === false)
	{
		update_sfnotice('sfmessage', '1@'.__("Update Failed!", "sforum"));
	} else {
		update_sfnotice('sfmessage', '0@'.__("Updated Topic Title Saved", "sforum"));
	}
	return;
}

# ------------------------------------------------------------------
# sf_save_subscription()
#
# Saves a user subscription following an edit in the UI
# 	$topicid:		The topic being subscribed to
#	$userid:		The user (passed because used in upgrade flow)
#	$retmessage:	True/False: Whether to retrun message (for UI)
# ------------------------------------------------------------------
function sf_save_subscription($topicid, $userid, $retmessage)
{
	global $wpdb, $current_user;

	if(!$userid || !$topicid) return '';

	if (($current_user->guest) || ($current_user->sfsubscriptions == false)) return;

	# is user already subscribed to this topic?
	if (sf_is_subscribed($userid, $topicid))
	{
		if($retmessage)
		{
			update_sfnotice('sfmessage', '1@'.__('You are already subscribed to this topic', "sforum"));
			return;
		}
	}

	# OK  -subscribe them to the topic
	$list = $wpdb->get_var("SELECT topic_subs FROM ".SFTOPICS." WHERE topic_id=".$topicid);
	$list = unserialize($list);
	$list[] = $userid;
	$list = serialize($list);
	$wpdb->query("UPDATE ".SFTOPICS." SET topic_subs = '".$list."' WHERE topic_id=".$topicid);

	# plus note the topic against their usermeta record
	$list = sf_get_member_item($userid, 'subscribe');
	$list[] = $topicid;
	sf_update_member_item($userid, 'subscribe', $list);

	if($retmessage)
	{
		update_sfnotice('sfmessage', '0@'.__('Subscription added', "sforum"));
	}

	return;
}

# ------------------------------------------------------------------
# sf_save_watch()
#
# Saves a user watch topic request following an edit in the UI
# 	$topicid:		The topic being watched
#	$userid:		The user (passed because used in upgrade flow)
#	$retmessage:	True/False: Whether to retrun message (for UI)
# ------------------------------------------------------------------
function sf_save_watch($topicid, $userid, $retmessage)
{
	global $wpdb, $current_user;

	if(!$userid || !$topicid) return '';

	if (($current_user->guest) || ($current_user->sfwatch == false)) return;

	# is user already watching this topic?
	if (sf_is_watching($userid, $topicid))
	{
		if ($retmessage)
		{
			update_sfnotice('sfmessage', '1@'.__('You are already watching this topic', "sforum"));
			return;
		}
	}

	# OK - watch the topic (in members table)
	$list = sf_get_member_item($userid, 'watches');
	$list[] = $topicid;
	sf_update_member_item($userid, 'watches', $list);

	# OK  -subscribe them to the topic
	$list = $wpdb->get_var("SELECT topic_watches FROM ".SFTOPICS." WHERE topic_id=".$topicid);
	$list = unserialize($list);
	$list[] = $userid;
	$list = serialize($list);
	$wpdb->query("UPDATE ".SFTOPICS." SET topic_watches = '".$list."' WHERE topic_id=".$topicid);

	if ($retmessage)
	{
		update_sfnotice('sfmessage', '0@'.__('Topic watch added', "sforum"));
	}

	return;
}

# ------------------------------------------------------------------
# sf_end_watch()
#
# Update watches from the UI - current user
# Values in POST variables
# ------------------------------------------------------------------
function sf_end_watch()
{
	global $current_user, $wpdb, $sfglobals;

	$topic = sf_esc_int($_GET['topic']);

	# remove current watch from member
	$list = $sfglobals['member']['watches'];
	if (!empty($list))
	{
		$newlist = '';
		foreach($list as $topicid)
		{
			if ($topic != $topicid)
			{
				$newlist[] = $topicid;
			}
		}
		sf_update_member_item($current_user->ID, 'watches', $newlist);
	}

	#remove the topic watch
	$list = $wpdb->get_var("SELECT topic_watches FROM ".SFTOPICS." WHERE topic_id=".$topic);
	if (!empty($list))
	{
		$newlist = '';
		$list = unserialize($list);
		foreach ($list as $userid)
		{
			if ($current_user->ID != $userid)
			{
				$newlist[] = $userid;
			}
		}
		if ($newlist != '') $newlist = serialize($newlist);
		$list = $wpdb->query("UPDATE ".SFTOPICS." SET topic_watches ='".$newlist."' WHERE topic_id=".$topic);
	}

	update_sfnotice('sfmessage', '0@'.__('Watches Updated', "sforum"));

	return;
}

# ------------------------------------------------------------------
# sf_end_sub()
#
# Update watches from the UI - current user
# Values in POST variables
# ------------------------------------------------------------------
function sf_end_sub()
{
	global $current_user, $wpdb, $sfglobals;

	$topic = sf_esc_int($_GET['topic']);

	# remove current subscription from member
	$list = $sfglobals['member']['subscribe'];
	if (!empty($list))
	{
		$newlist = '';
		foreach($list as $topicid)
		{
			if ($topic != $topicid)
			{
				$newlist[] = $topicid;
			}
		}
		sf_update_member_item($current_user->ID, 'subscribe', $newlist);
	}

	#remove the topic subscription
	$list = $wpdb->get_var("SELECT topic_subs FROM ".SFTOPICS." WHERE topic_id=".$topic);
	if (!empty($list))
	{
		$newlist = '';
		$list = unserialize($list);
		foreach ($list as $userid)
		{
			if ($current_user->ID != $userid)
			{
				$newlist[] = $userid;
			}
		}
		if ($newlist != '') $newlist = serialize($newlist);
		$list = $wpdb->query("UPDATE ".SFTOPICS." SET topic_subs ='".$newlist."' WHERE topic_id=".$topic);
	}

	update_sfnotice('sfmessage', '0@'.__('Subscriptions Updated', "sforum"));

	return;
}

# ------------------------------------------------------------------
# sf_remove_watch($topic, $userid)
#
# removes the topic watch for the specified user
# $topic			topic to be removed
# $userid			user to have watch removed
# ------------------------------------------------------------------
function sf_remove_watch($topic, $userid)
{
	global $wpdb, $sfglobals;

	if(!$userid || !$topic) return '';

	# remove current watch from member
	$list = $sfglobals['member']['watches'];
	if (!empty($list))
	{
		$newlist = '';
		foreach($list as $topicid)
		{
			if ($topic != $topicid)
			{
				$newlist[] = $topicid;
			}
		}
		sf_update_member_item($userid, 'watches', $newlist);
	}

	#remove the topic subscription
	$list = $wpdb->get_var("SELECT topic_watches FROM ".SFTOPICS." WHERE topic_id=".$topic);
	if (!empty($list))
	{
		$newlist = '';
		$list = unserialize($list);
		foreach ($list as $user)
		{
			if ($userid != $user)
			{
				$newlist[] = $user;
			}
		}
		if ($newlist != '') $newlist = serialize($newlist);
		$list = $wpdb->query("UPDATE ".SFTOPICS." SET topic_watches ='".$newlist."' WHERE topic_id=".$topic);
	}

	return;
}

# ------------------------------------------------------------------
# sf_remove_user_watches($userid)
#
# removes all topic watches for the specified user
# $userid			user to have all watches removed
# ------------------------------------------------------------------
function sf_remove_user_watches($userid)
{
    global $current_user;

    # make sure user is doing this
    if ($userid == $current_user->ID)
    {
    	# Remove watches
    	$watches = sf_get_member_item($userid, 'watches');
    	if (!empty($watches))
    	{
    		foreach ($watches as $watch)
    		{
    			sf_remove_watch($watch, $userid);
    		}
    	}
    }
}

# ------------------------------------------------------------------
# sf_remove_user_subs($userid)
#
# removes all topic subs for the specified user
# $userid			user to have subscriptions removed
# ------------------------------------------------------------------
function sf_remove_user_subs($userid)
{
    global $current_user;

    # make sure user is doing this
    if ($userid == $current_user->ID)
    {
    	# Remove subscriptions
    	$subs = sf_get_member_item($userid, 'subscribe');
    	if (!empty($subs))
    	{
    		foreach ($subs as $sub)
    		{
    			sf_remove_subscription($sub, $userid);
    		}
    	}
    }
}

# ------------------------------------------------------------------
# sf_add_postrating_vote()
#
# Saves a user watch topic request following an edit in the UI
# 	$postid:		The post being voted on
# ------------------------------------------------------------------
function sf_add_postrating_vote($postid)
{
	global $current_user, $sfglobals;

	if(!$postid) return '';

	# record the post as voted (in members table)
	$list = $sfglobals['member']['posts_rated'];
	$list[] = $postid;
	sf_update_member_item($current_user->ID, 'posts_rated', $list);

	return;
}

# ------------------------------------------------------------------
# sf_remove_postrated($topic, $userid)
#
# removes the post rated id for the specified user
# $postid			postid to be removed
# $userid			user to have watche removed
# ------------------------------------------------------------------
function sf_remove_postrated($postid, $userid)
{
	global $wpdb;

	if(!$userid || !$postid) return '';

	#remove the member id from post rated
	$list = $wpdb->get_var("SELECT members FROM ".SFPOSTRATINGS." WHERE post_id=".$postid);
	if (!empty($list))
	{
		$newlist = null;
		$list = unserialize($list);
		foreach ($list as $user)
		{
			if ($userid != $user)
			{
				$newlist[] = $user;
			}
		}
		if ($newlist) $newlist = serialize($newlist);
		$list = $wpdb->query("UPDATE ".SFPOSTRATINGS." SET members ='".$newlist."' WHERE post_id=".$postid);
	}

	return;
}

# ------------------------------------------------------------------
# sf_move_topic()
#
# Move topic from one forum to another
# Values in POST variables
# ------------------------------------------------------------------
function sf_move_topic()
{
	global $wpdb, $current_user;

	if(!$current_user->sfmovetopics)
	{
		update_sfnotice('sfmessage', '1@'.__('Access Denied', "sforum"));
		return;
	}

	if(empty($_POST['forumid']))
	{
		update_sfnotice('sfmessage', '1@'.__('Destination Forum not Selected', "sforum"));
		return;
	}

	$currentforumid = sf_esc_int($_POST['currentforumid']);
	$currenttopicid = sf_esc_int($_POST['currenttopicid']);
	$targetforumid  = sf_esc_int($_POST['forumid']);

	# change topic record to new forum id
	$wpdb->query("UPDATE ".SFTOPICS." SET forum_id = ".$targetforumid." WHERE topic_id=".$currenttopicid);

	if($wpdb === false)
	{
		update_sfnotice('sfmessage', '1@'.__("Topic Move Failed", "sforum"));
		return;
	}

	# check in 'waiting' to see if there is an unread post in there and change forum id if there is
	$wpdb->query("UPDATE ".SFWAITING." SET forum_id = ".$targetforumid." WHERE topic_id=".$currenttopicid);

	# change posts record(s) to new forum
	$wpdb->query("UPDATE ".SFPOSTS." SET forum_id = ".$targetforumid." WHERE topic_id=".$currenttopicid);

	# rebuild forum counts for old and new forums
	sf_build_forum_index($currentforumid);
	sf_build_forum_index($targetforumid);

	# relink to new forum if blog linked topic
	sf_relink_topic($currenttopicid, $currenttopicid, $targetforumid);

	# Ok - do not like doing this but....
	# There seems to have been times when a new post is made to the old forum id so we will now double check...
	$checkposts = $wpdb->get_results("SELECT post_id FROM ".SFPOSTS." WHERE forum_id=".$currentforumid." AND topic_id=".$currenttopicid);
	if($checkposts)
	{
		# made after most were moved
		sf_move_topic();
	} else {
		if($wpdb === false)
		{
			update_sfnotice('sfmessage', '1@'.__("Topic Move Failed", "sforum"));
		} else {
			update_sfnotice('sfmessage', '0@'.__("Topic Moved", "sforum"));
		}
	}
	return;
}

# ------------------------------------------------------------------
# sf_move_post()
#
# Move post from one topic and create new topic -same/another forum
# Values in POST variables
# ------------------------------------------------------------------
function sf_move_post()
{
	global $wpdb, $current_user;

	if(!$current_user->sfmoveposts)
	{
		update_sfnotice('sfmessage', '1@'.__('Access Denied', "sforum"));
		return;
	}
	if(empty($_POST['forumid']))
	{
		update_sfnotice('sfmessage', '1@'.__("Post move abandoned - No Forum selected!", "sforum"));
		return;
	}
	if(empty($_POST['newtopicname']))
	{
		update_sfnotice('sfmessage', '1@'.__("Post move abandoned - No Topic Defined!", "sforum"));
		return;
	}
	# extract data from POST
	$postid     = sf_esc_int($_POST['postid']);
	$oldtopicid = sf_esc_int($_POST['oldtopicid']);
	$oldforumid = sf_esc_int($_POST['oldforumid']);
	$newforumid = sf_esc_int($_POST['forumid']);
	$blogpostid = sf_esc_int($_POST['blogpostid']);
	$newtopicname  = sf_filter_title_save(trim(($_POST['newtopicname'])));

	# start with creating the new topic
	$newtopicslug = sf_create_slug($newtopicname, 'topic');

	# now create the topic and post records
	$wpdb->query(
		"INSERT INTO ".SFTOPICS."
		 (topic_name, topic_slug, topic_date, forum_id, post_count, blog_post_id, post_id)
		 VALUES
		 ('".$newtopicname."', '".$newtopicslug."', now(), ".$newforumid.", 1, '".$blogpostid."', ".$postid.");");

	if($wpdb === false)
	{
		update_sfnotice('sfmessage', '1@'.__("Post Move Failed", "sforum"));
		return;
	}
	$newtopicid = $wpdb->insert_id;

	# check the topic slug and if empty use the topic id
	if(empty($newtopicslug))
	{
		$newtopicslug = 'topic-'.$newtopicid;
		$thistopic = $wpdb->query("UPDATE ".SFTOPICS." SET topic_slug='".$slug."' WHERE topic_id=".$newtopicid);
	}

	# now check if old topic had just the one post and if so remove it
	$check = $wpdb->get_var("SELECT post_count FROM ".SFTOPICS." WHERE topic_id=".$oldtopicid);
	if($check == 1)
	{
		$wpdb->query("DELETE FROM ".SFTOPICS." WHERE topic_id=".$oldtopicid);
	}

	# update post record
	$wpdb->query(
		"UPDATE ".SFPOSTS."
		 SET topic_id=".$newtopicid.", forum_id=".$newforumid.", post_index=1, post_status=0
		 WHERE post_id=".$postid);

	# If old topic was in the admin queue then remove it. Assume it's read
	sf_remove_from_waiting(true, $oldtopicid, 0);

	# rebuild forum counts for old and new forums
	sf_build_forum_index($oldforumid);
	sf_build_forum_index($newforumid);
	sf_build_post_index($oldtopicid, sf_get_topic_slug($oldtopicid));

	# relink to new forum if blog linked topic and this was first post
	if($blogpostid)
	{
		sf_relink_topic($oldtopicid, $newtopicid, $newforumid);
	}

	if($wpdb == false)
	{
		update_sfnotice('sfmessage', '1@'.__("Post Move Failed", "sforum"));
	} else {
		update_sfnotice('sfmessage', '0@'.__("Post Moved", "sforum"));
	}
	return;
}

# ------------------------------------------------------------------
# sf_relink_topic()
#
# Relinks topic after a move
# 	$topicid	original topic ID
#	$newtopicid	new id if moved post
#	$newforumid	new forum id
# ------------------------------------------------------------------
function sf_relink_topic($topicid, $newtopicid, $newforumid)
{
	global $wpdb;

	# Check if the target topic is a linked topic
	$link = $wpdb->get_row("SELECT * FROM ".SFLINKS." WHERE topic_id=".$topicid);
	if($link)
	{
		sf_blog_links_control('update', $link->post_id, $newforumid, $newtopicid, $link->syncedit);
	}
	return;
}


function sf_reassign_post()
{
	global $wpdb, $current_user;

	if (!$current_user->sfreassign)
	{
		update_sfnotice('sfmessage', '1@'.__('Access Denied', "sforum"));
		return;
	}

	$postid = sf_esc_int($_POST['postid']);
	$olduserid = sf_esc_int($_POST['olduserid']);
	$newuserid = sf_esc_int($_POST['newuserid']);

	# transfer the post
	$wpdb->query(
		"UPDATE ".SFPOSTS."
		 SET user_id=".$newuserid."
		 WHERE post_id=".$postid);

	# update old user post counts
	$count = sf_get_member_item($olduserid, 'posts') - 1;
	sf_update_member_item($olduserid, 'posts', $count);

	# update new user post counts
	$count = sf_get_member_item($newuserid, 'posts') + 1;
	sf_update_member_item($newuserid, 'posts', $count);
}

function sf_change_topic_status()
{
	global $wpdb, $current_user;

	if(!$current_user->sfedit)
	{
		update_sfnotice('sfmessage', '1@'.__('Access Denied', "sforum"));
		return;
	}
	$topicid= sf_esc_int($_POST['id']);
	$statvalue = sf_esc_int($_POST['statvalue']);

	sf_update_topic_status_flag($statvalue, $topicid);

	update_sfnotice('sfmessage', '0@'.__("Topic Status Changed", "sforum"));
	return;
}

# ------------------------------------------------------------------
# sf_update_opened()
#
# Updates the number of times a topic is viewed
# 	$topicid:		The topic being opened for view
# ------------------------------------------------------------------
function sf_update_opened($topicid)
{
	global $wpdb, $sfvars;

	if(empty($topicid)) return;

	$ref=array();
	$ref=explode('/', $_SERVER['HTTP_REFERER']);
	$ref_topic = $ref[count($ref)-3];
	if(substr($ref_topic,0,5) == 'page-') $ref_topic = $ref[count($ref)-4];
	if($ref_topic == $sfvars['topicslug']) return;

	$current=$wpdb->get_var("SELECT topic_opened FROM ".SFTOPICS." WHERE topic_id=".$topicid);
	$current++;
	$wpdb->query("UPDATE ".SFTOPICS." SET topic_opened = ".$current." WHERE topic_id=".$topicid);
	return;
}

# ******************************************************************
# DELETE ITEM FUNCTIONS
# ******************************************************************

# ------------------------------------------------------------------
# sf_delete_topic()
#
# Delete a topic and all it;s posts
# 	$topicid:		The topic being subscribed to
#	$show:			True/False: Whether to return message (for UI)
# ------------------------------------------------------------------
function sf_delete_topic($topicid, $show=true)
{
	global $wpdb, $current_user;

	if(!$topicid) return '';

	if (!$current_user->sfdelete && !sf_is_forum_admin($current_user->ID) && !$current_user->sfdeleteown)
	{
		update_sfnotice('sfmessage', '1@'.__('Access Denied', "sforum"));
		return;
	}

	# We need to check for subscriptions on this topic
	$subs = $wpdb->get_var("SELECT topic_subs FROM ".SFTOPICS." WHERE  topic_id = ".$topicid);

	# Any subscriptions to remopve from user records?
	if ($subs)
	{
		$userlist = unserialize($subs);
		foreach ($userlist as $user)
		{
			$topiclist = sf_get_member_item($user, 'subscribe');
			if (!empty($topiclist))
			{
				$newlist = '';
				foreach ($topiclist as $topic)
				{
					if ($topic != $topicid) $newlist[] = $topic;
				}
				sf_update_member_item($user, 'subscribe', $newlist);
			}
		}
	}

	# Any watches to remove?
	$watches = $wpdb->get_var("SELECT topic_watches FROM ".SFTOPICS." WHERE  topic_id = ".$topicid);
	if ($watches)
	{
		$userlist = unserialize($watches);
		foreach ($userlist as $user)
		{
			$topiclist = sf_get_member_item($user, 'watches');
			if (!empty($topiclist))
			{
				$newlist = '';
				foreach ($topiclist as $topic)
				{
					if ($topic != $topicid) $newlist[] = $topic;
				}
				sf_update_member_item($user, 'watches', $newlist);
			}
		}
	}

	# remove any post ratings in this topic

	$rated = $wpdb->get_results("SELECT ".SFPOSTRATINGS.".post_id, members FROM ".SFPOSTRATINGS."
			 JOIN ".SFPOSTS." ON ".SFPOSTRATINGS.".post_id = ".SFPOSTS.".post_id
			 WHERE ".SFPOSTS.".topic_id=".$topicid);
	if ($rated)
	{
		foreach ($rated as $post)
		{
			# remove the post rating
			$wpdb->query("DELETE FROM ".SFPOSTRATINGS." WHERE post_id=".$post->post_id);

			# remove the posts rated data from sfmembers table
			$members = unserialize($post->members);
			if ($members)
			{
				# loop through each members that rated a post in this topic and remove the rating
				foreach ($members as $member)
				{
					$ratedposts = $wpdb->get_var("SELECT posts_rated FROM ".SFMEMBERS." WHERE user_id=".$member);
					if ($ratedposts)
					{
						# save any other rated posts only removing posts from this topic
						$new_ratings = '';
						$ratedposts = unserialize($ratedposts);
						foreach ($ratedposts as $ratedpost)
						{
							if ($ratedpost != $post->post_id)
							{
								$new_ratings[] = $ratedpost;
							}
						}
						if ($new_ratings == '')
						{
							$wpdb->query("UPDATE ".SFMEMBERS." SET posts_rated=NULL WHERE user_id=".$member);
						} else {
							$new_ratings = serialize($new_ratings);
							$wpdb->query("UPDATE ".SFMEMBERS." SET posts_rated='".$new_ratings."' WHERE user_id=".$member);
						}
					}
				}
			}
		}
	}

	# check of there is a post link to it?
	$row = $wpdb->get_row("SELECT blog_post_id, forum_id FROM ".SFTOPICS." WHERE topic_id = ".$topicid);
	if($row->blog_post_id != 0)
	{
		# break the link
		include_once(SF_PLUGIN_DIR.'/linking/library/sf-links-support.php');
		sf_blog_links_control('delete', $row->blog_post_id);
	}

	# delete from waiting just in case
	$wpdb->query("DELETE FROM ".SFWAITING." WHERE topic_id=".$topicid);

	# now delete from topic
	$wpdb->query("DELETE FROM ".SFTOPICS." WHERE topic_id=".$topicid);
	if($wpdb === false)
	{
		if($show) update_sfnotice('sfmessage', '1@'.__("Deletion Failed", "sforum"));
		return;
	}

	# topic delete hook
	if(function_exists('sf_hook_topic_delete'))
	{
		# grab the forum id
		$forumid = $wpdb->get_var("SELECT forum_id FROM ".SFTOPICS." WHERE  topic_id = ".$topicid);
		sf_process_hook('sf_hook_topic_delete', array($topicid, $forumid));
	}

	# now delete all the posts on the topic
	$wpdb->query("DELETE FROM ".SFPOSTS." WHERE topic_id=".$topicid);
	if($wpdb == false)
	{
		if($show) update_sfnotice('sfmessage', '1@'.__("Deletion of Posts in Topic Failed", "sforum"));
	} else {
		if($show) update_sfnotice('sfmessage', '0@'.__("Topic Deleted", "sforum"));
	}

	# delete from forums topic count
	sf_build_forum_index($row->forum_id);

	return;
}

# ------------------------------------------------------------------
# sf_delete_post()
#
# Delete a post
#	$postid:		The post to be deleted
# 	$topicid:		The topic post belongs to
#	$forumid:		The forum post belongs to
# ------------------------------------------------------------------
function sf_delete_post($postid, $topicid, $forumid, $show=true, $poster=0)
{
	global $wpdb, $current_user;

	if(!$postid || !$topicid || !$forumid) return '';

	if(!$current_user->sfdelete && !($current_user->sfdeleteown && $current_user->ID == $poster))
	{
		update_sfnotice('sfmessage', '1@'.__('Access Denied', "sforum"));
		return;
	}

	# Check post actually exsists - might be a browsser refresh!
	$target = $wpdb->get_var("SELECT post_id FROM ".SFPOSTS." WHERE post_id=".$postid);
	if(empty($target))
	{
		if($show) update_sfnotice('sfmessage', '0@'.__("Post Already Deleted", "sforum"));
		return;
	}

	# if just one post then remove topic as well
	if(sf_get_posts_count_in_topic($topicid) == 1)
	{
		sf_delete_topic($topicid);
	} else {
		# remove post ratings
		$rated = $wpdb->get_var("SELECT members FROM ".SFPOSTRATINGS." WHERE post_id=".$postid);
		if ($rated)
		{
			$members = unserialize($rated);
			if ($members)
			{
				# loop through each members that rated a post in this topic and remove the rating
				foreach ($members as $member)
				{
					$ratedposts = $wpdb->get_var("SELECT posts_rated FROM ".SFMEMBERS." WHERE user_id=".$member);
					if ($ratedposts)
					{
						# save any other rated posts only removing posts from this topic
						$new_ratings = '';
						$ratedposts = unserialize($ratedposts);
						foreach ($ratedposts as $ratedpost)
						{
							if ($ratedpost != $postid)
							{
								$new_ratings[] = $ratedpost;
							}
						}
						if ($new_ratings == '')
						{
							$wpdb->query("UPDATE ".SFMEMBERS." SET posts_rated=NULL WHERE user_id=".$member);
						} else {
							$new_ratings = serialize($new_ratings);
							$wpdb->query("UPDATE ".SFMEMBERS." SET posts_rated='".$new_ratings."' WHERE user_id=".$member);
						}
					}
				}
			}
		}
		$wpdb->query("DELETE FROM ".SFPOSTRATINGS." WHERE post_id=".$postid);

		$wpdb->query("DELETE FROM ".SFPOSTS." WHERE post_id=".$postid);
		if($wpdb === false)
		{
			if($show) update_sfnotice('sfmessage', '1@'.__("Deletion Failed", "sforum"));
		} else {
			if($show) update_sfnotice('sfmessage', '0@'.__("Post Deleted", "sforum"));
		}
		# re number post index
		sf_build_post_index($topicid, sf_get_topic_slug($topicid));
		sf_build_forum_index($forumid);
	}

	# post delete hook
	sf_process_hook('sf_hook_post_delete', array($postid, $topicid, $forumid));

	# need to look in sfwaiting to see if it's in there...
	sf_remove_from_waiting(true, $topicid, $postid);

	return;
}

# ------------------------------------------------------------------
# sf_remove_subscription()
#
# Removes a user subscription following edit or topic delete.
# 	$topicid:		The topic being unsubscribed from
#	$userid:		The user (passed because used in upgrade flow)
# ------------------------------------------------------------------
function sf_remove_subscription($topic, $userid)
{
	global $wpdb, $sfglobals;

	if(!$userid || !$topic) return '';

	# remove current subscription from member
	$list = sf_get_member_item($userid, 'subscribe');
	if (!empty($list))
	{
		$newlist = '';
		foreach ($list as $topicid)
		{
			if ($topic != $topicid)
			{
				$newlist[] = $topicid;
			}
		}
		sf_update_member_item($userid, 'subscribe', $newlist);
	}

	$list = $wpdb->get_var("SELECT topic_subs FROM ".SFTOPICS." WHERE topic_id=".$topic);
	if (!empty($list))
	{
		$newlist = '';
		$list = unserialize($list);
		foreach ($list as $user)
		{
			if ($userid != $user)
			{
				$newlist[] = $user;
			}
		}
		if ($newlist != '') $newlist = serialize($newlist);
		$wpdb->query("UPDATE ".SFTOPICS." SET topic_subs = '".$newlist."' WHERE topic_id=".$topic);
	}

	return;
}

# ******************************************************************
# EDIT TOOL ICONS
# ******************************************************************

# ------------------------------------------------------------------
# sf_icon_toggle()
#
# Toggle Tool Icon State
# ------------------------------------------------------------------
function sf_icon_toggle()
{
	global $sfglobals;

	$sfadminsettings = array();
	$sfadminsettings = sf_get_option('sfadminsettings');
	$state = $sfadminsettings['sftools'];
	if ($state ? $state = false : $state = true);
	$sfadminsettings['sftools'] = $state;
	sf_update_option('sfadminsettings', $sfadminsettings);
	$sfglobals['admin']['sftools'] = $state;

	return;
}

# ------------------------------------------------------------------
# sf_lock_topic_toggle()
#
# Toggle Topic Lock
#	Topicid:		Topic to lock/unlock
# ------------------------------------------------------------------
function sf_lock_topic_toggle($topicid)
{
	global $wpdb, $current_user;

	if(!$topicid) return '';

	if(!$current_user->sflock)
	{
		update_sfnotice('sfmessage', '1@'.__('Access Denied', "sforum"));
		return;
	}

	if($_POST['locktopicaction'].$topicid == get_sfsetting('sfaction')) return;

	$status = $wpdb->get_var("SELECT topic_status FROM ".SFTOPICS." WHERE topic_id=".sf_esc_int($topicid));
	if($status == 1 ? $status=0 : $status=1);

	$wpdb->query("UPDATE ".SFTOPICS." SET topic_status = ".$status." WHERE topic_id=".sf_esc_int($topicid));
	if($wpdb == false)
	{
		update_sfnotice('sfmessage', '1@'.__("Topic Lock Toggle Failed", "sforum"));
	} else {
		update_sfnotice('sfmessage', '0@'.__("Topic Lock Toggled", "sforum"));
		update_sfsetting('sfaction', sf_esc_str($_POST['locktopicaction'].$topicid));
	}
	return;
}

# ------------------------------------------------------------------
# sf_pin_topic_toggle()
#
# Toggle Topic Pin
#	Topicid:		Topic to pin/unpin
# ------------------------------------------------------------------
function sf_pin_topic_toggle($topicid)
{
	global $wpdb, $current_user;

	if(!$topicid) return '';

	if(!$current_user->sfpintopics)
	{
		update_sfnotice('sfmessage', '1@'.__('Access Denied', "sforum"));
		return;
	}

	if($_POST['pintopicaction'].$topicid == get_sfsetting('sfaction')) return;

	$status = $wpdb->get_var("SELECT topic_pinned FROM ".SFTOPICS." WHERE topic_id=".sf_esc_int($topicid));
	if($status == 1 ? $status=0 : $status=1);

	$wpdb->query("UPDATE ".SFTOPICS." SET topic_pinned = ".$status." WHERE topic_id=".sf_esc_int($topicid));
	if($wpdb == false)
	{
		update_sfnotice('sfmessage', '1@'.__("Topic Pin Toggle Failed", "sforum"));
	} else {
		update_sfnotice('sfmessage', '0@'.__("Topic Pin Toggled", "sforum"));
		update_sfsetting('sfaction', sf_esc_str($_POST['pintopicaction'].$topicid));
	}
	return;
}

# ------------------------------------------------------------------
# sf_pin_post_toggle()
#
# Toggle Post Pin
#	postid:		Post to pin/unpin
# ------------------------------------------------------------------
function sf_pin_post_toggle($postid)
{
	global $wpdb, $current_user;

	if(!$postid) return '';

	if(!$current_user->sfpinposts)
	{
		update_sfnotice('sfmessage', '1@'.__('Access Denied', "sforum"));
		return;
	}

	if($_POST['pinpostaction'].$postid == get_sfsetting('sfaction')) return;

	$status = $wpdb->get_var("SELECT post_pinned FROM ".SFPOSTS." WHERE post_id=".$postid);
	if($status == 1 ? $status = 0 : $status = 1);

	$wpdb->query("UPDATE ".SFPOSTS." SET post_pinned = ".$status." WHERE post_id=".sf_esc_int($postid));
	if($wpdb == false)
	{
		update_sfnotice('sfmessage', '1@'.__("Post Pin Toggle Failed", "sforum"));
	} else {
		update_sfnotice('sfmessage', '0@'.__("Post Pin Toggled", "sforum"));
		update_sfsetting('sfaction', sf_esc_str($_POST['pinpostaction']).$postid);
	}
	return;
}

# ******************************************************************
# DATA INTEGRITY MANAGEMENT
# ******************************************************************

# ------------------------------------------------------------------
# sf_build_post_index()
#
# Rebuilds the post index column (post sequence) and also sets the
# last post id and post count into the parent topic record
#	$topicid:		topic whose posts are being re-indexed
#	$topicslug:		slug to check sort order
# ------------------------------------------------------------------
function sf_build_post_index($topicid, $topicslug, $returnmsg=false)
{
	global $wpdb;

	if(!$topicid || !$topicslug) return '';

	# get topic posts is their display order
	$posts=$wpdb->get_results("SELECT post_id, post_index FROM ".SFPOSTS." WHERE topic_id = ".$topicid." ORDER BY post_pinned DESC, post_id ASC");
	if($posts)
	{
		$index = 1;
		foreach($posts as $post)
		{
			# update the post_index for each post to set display order
			$wpdb->query("UPDATE ".SFPOSTS." SET post_index = ".$index." WHERE post_id = ".$post->post_id);
			$index++;
		}
		$lastpost = $post->post_id;
	} else {
		$lastpost = 'NULL';
	}
	# update the topic with the last post id and the post count
	$wpdb->query("UPDATE ".SFTOPICS." SET post_id=".$lastpost.", post_count=".($index-1)." WHERE topic_id=".$topicid);

	if($returnmsg) update_sfnotice('sfmessage', '0@'.__("Verification Complete", "sforum"));

	return;
}

# ------------------------------------------------------------------
# sf_build_forum_index()
#
# Rebuilds the topic count and last post id in a forum record
#	$forumid:		forum needing updating
# ------------------------------------------------------------------
function sf_build_forum_index($forumid, $returnmsg=false)
{
	global $wpdb;

	if(!$forumid) return '';

	# get the topic and post counts for this forum
	$topiccount = $wpdb->get_var("SELECT COUNT(topic_id) FROM ".SFTOPICS." WHERE forum_id=".$forumid);
	$postcount  = $wpdb->get_var("SELECT COUNT(post_id) FROM ".SFPOSTS." WHERE forum_id=".$forumid);
	# get the last post that appeared in a topic within this forum
	$postid =  $wpdb->get_var("SELECT post_id FROM ".SFPOSTS." WHERE forum_id = ".$forumid." ORDER BY post_id DESC LIMIT 1");

	if(!$topiccount) $topiccount = 0;
	if(!$postcount)  $postcount = 0;
	if(!isset($postid)) $postid = 'NULL';

	# update forum record
	$wpdb->query("UPDATE ".SFFORUMS." SET post_id=".$postid.", post_count=".$postcount.", topic_count=".$topiccount." WHERE forum_id=".$forumid);

	if($returnmsg) update_sfnotice('sfmessage', '0@'.__("Verification Complete", "sforum"));

	return;
}

function sf_get_user_memberships($user_id)
{
	global $wpdb;

	if(!$user_id) return '';

	$sql = "SELECT ".SFMEMBERSHIPS.".usergroup_id, usergroup_name, usergroup_desc
			FROM ".SFMEMBERSHIPS."
			JOIN ".SFUSERGROUPS." ON ".SFUSERGROUPS.".usergroup_id = ".SFMEMBERSHIPS.".usergroup_id
			WHERE user_id=".$user_id;
	return $wpdb->get_results($sql, ARRAY_A);
}

function sf_check_membership($usergroup_id, $user_id)
{
	global $wpdb;

	if(!$usergroup_id || !$user_id) return '';

	$sql = "SELECT usergroup_id
			FROM ".SFMEMBERSHIPS."
			WHERE user_id=".$user_id." AND usergroup_id=".$usergroup_id;
	return $wpdb->get_results($sql, ARRAY_A);
}


function sf_get_pm_inbox_new_count($userid)
{
	global $wpdb, $sfglobals;

	if(!$userid) return '';

	return $wpdb->get_var("SELECT COUNT(*) FROM ".SFMESSAGES." WHERE to_id = ".$userid." AND inbox=1 AND message_status=0");
}

function sf_change_topic_tags($topicid, $newtags)
{
	global $wpdb, $sfglobals;

	if(!$topicid || !$newtags) return '';

	# remove any existing tags for the topic
	$oldtags = $wpdb->get_results("SELECT tag_id FROM ".SFTAGMETA." WHERE topic_id=".$topicid);
	foreach ($oldtags as $oldtag)
	{
		# grab all the tag rows and decrement the tag count
		$tagcount = $wpdb->get_var("SELECT tag_count FROM ".SFTAGS." WHERE tag_id=".$oldtag->tag_id);

		# decrement tag count and delete if it gets to zero or update the new count
		$tagcount--;
		if ($tagcount == 0)
		{
			$wpdb->query("DELETE FROM ".SFTAGS." WHERE tag_id=".$oldtag->tag_id); # count is zero so delete
		} else {
			$wpdb->query("UPDATE ".SFTAGS." SET tag_count=".$tagcount." WHERE tag_id=".$oldtag->tag_id); # update count
		}

		# remove all the tag meta entries for the topic
		$wpdb->query("DELETE FROM ".SFTAGMETA." WHERE topic_id=".$topicid);
	}

	# now add in the updated tags
    $tags = trim($newtags);
    $tags = trim($tags, ',');  # no extra commas allowed
	$tags = explode(',', $tags);
	$tags = array_unique($tags);  # remove any duplicates
	$tags = array_values($tags);  # put back in order
	if ($sfglobals['display']['topics']['maxtags'] > 0 && count($tags) > $sfglobals['display']['topics']['maxtags'])
	{
		$tags = array_slice($tags, 0, $sfglobals['display']['topics']['maxtags']);  # limit to maxt tags opton
	}
	sfc_add_tags($topicid, $tags);
}

function sf_mark_all_read()
{
    global $wpdb, $current_user;

    # just to be safe, make sure a member called
    if ($current_user->member)
    {
        sf_destroy_users_newposts($current_user->ID);
    }
}

# ------------------------------------------------------------------
# sf_get_blog_title_from_id()
#
# Returns the post title of a blog post from the post id
#	$postid:		blog post target id
# ------------------------------------------------------------------
function sf_get_blog_title_from_id($postid)
{
	global $wpdb;

	if(!$postid) return '';

	return $wpdb->get_var(
			"SELECT post_title
			 FROM ".$wpdb->prefix."posts
			 WHERE ID=".$postid);
}


# ------------------------------------------------------------------
# sf_query_results()
#
# Returns the query if good - displays error if sql invalid
# 4.2 Just used on two search queries
#	$sql:		query string
#	$type:		return type - defaults to OBJECT
# ------------------------------------------------------------------
function sf_query_results($sql, $type=OBJECT)
{
	global $wpdb;

	$wpdb->hide_errors();
	$records = $wpdb->get_results($sql, $type);

	if($wpdb->last_error == '')
	{
		return $records;
	} else {
		update_sfnotice('sfmessage', '1@'.__("Invalid Database Query", "sforum"));
		echo sf_render_queued_message();
		return '';
	}
}

?>