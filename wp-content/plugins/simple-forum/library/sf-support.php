<?php
/*
Simple:Press
Support Routines
$LastChangedDate: 2011-04-28 20:08:44 -0700 (Thu, 28 Apr 2011) $
$Rev: 5994 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# ******************************************************************
# USER TRACKING AND USER NEW POST LIST MANAGEMENT
# ******************************************************************

# ------------------------------------------------------------------
# sf_track_online()
#
# Tracks online users. Creates their new-post-list when they first
# appear through to saving their last visit date when they go again
# (either logout or time out - 15 minutes)
# ------------------------------------------------------------------
function sf_track_online()
{
	global $wpdb, $current_user, $sfvars;

	$lastvisit = 0;

	if($current_user->member)
	{
		# it's a member
		$trackuserid = $current_user->ID;
		$trackname = $current_user->user_login;

		$wpdb->query("DELETE FROM ".SFTRACK." WHERE trackname='".$_SERVER['REMOTE_ADDR']."'");
	} elseif(!empty($current_user->guestname)) {
		# it's a returning guest
		$trackuserid=0;
		$trackname = $current_user->guestname.$_SERVER['REMOTE_ADDR'];
	} else {
		# Unklnown guest
		$trackuserid=0;
		$trackname = $_SERVER['REMOTE_ADDR'];
	}

	# Update tracking
	$id=$wpdb->get_var("SELECT id FROM ".SFTRACK." WHERE trackname='".$trackname."'");

	$now = current_time('mysql');

	# Set up forum/topic values for tracking
	switch($sfvars['pageview'])
	{
		case 'forum':
			$forumid = $sfvars['forumid'];
			$topicid = "0";
			break;
		case 'topic':
			$forumid = $sfvars['forumid'];
			$topicid = $sfvars['topicid'];
			break;
		default:
			$forumid = "0";
			$topicid = "0";
			break;
	}

	if($id)
	{
		# they are still here
		$wpdb->query(
			"UPDATE ".SFTRACK."
			 SET trackdate='".$now."',
			 forum_id=".$forumid.",
			 topic_id=".$topicid."
			 WHERE id=".$id
		);

	} else {
		# newly arrived
		$wpdb->query(
			"INSERT INTO ".SFTRACK."
			 (trackuserid, trackname, forum_id, topic_id, trackdate)
			 VALUES (
			 ".$trackuserid.",
			 '".$trackname."',
			 ".$forumid.",
			 ".$topicid.",
			 '".$now."')
		");

		if($current_user->member)
		{
			sf_construct_users_newposts();
		}
	}

	# Check for expired tracking - so may have left the scene
	$expired=$wpdb->get_results("SELECT * FROM ".SFTRACK." WHERE trackdate	< DATE_SUB('".$now."', INTERVAL 20 MINUTE)");
	if($expired)
	{
		# if any Members expired - update user meta
		foreach($expired as $expire)
		{
			if($expire->trackuserid > 0)
			{
				sf_set_last_visited($expire->trackuserid);
				sf_destroy_users_newposts($expire->trackuserid);
			}
		}
		# finally delete them
		$wpdb->query("DELETE FROM ".SFTRACK." WHERE trackdate < DATE_SUB('".$now."', INTERVAL 20 MINUTE)");
	}
	return;
}

# ------------------------------------------------------------------
# sf_construct_users_newposts()
#
# Constructs the new users personalised new/unread posts list when
# they first appear on the system and creates the timestamp for
# their creation
#
#	$nosave		if called from a tempate tag and this is true
#				do NOT save checktime
# ------------------------------------------------------------------
function sf_construct_users_newposts($nosave = false)
{
	global $wpdb, $current_user, $sfglobals;

	$newpostlist=array();
	$newpostlist['topics']=array();
	$newpostlist['forums']=array();

	# NOTE: Use $sfglobals['member']['lastvisit'] as this holds te correct timezone date/time
	$records=$wpdb->get_results("SELECT DISTINCT topic_id, forum_id FROM ".SFPOSTS." WHERE post_status = 0 AND post_date > '".$sfglobals['member']['lastvisit']."' AND user_id != ".$current_user->ID." ORDER BY topic_id DESC LIMIT 50;", ARRAY_A);

	if($records)
	{
		foreach($records as $record)
		{
			$newpostlist['topics'][]=$record['topic_id'];
			$newpostlist['forums'][]=$record['forum_id'];
		}
	}

	$newpostlist = sf_add_waiting_post_list($newpostlist);

	if(count($newpostlist['topics'])==0) $newpostlist['topics'][0]=0;
	if(count($newpostlist['forums'])==0) $newpostlist['forums'][0]=0;

	sf_update_member_item($current_user->ID, 'newposts', $newpostlist);

	if($nosave == false)
	{
		sf_update_member_item($current_user->ID, 'checktime', 0);
	}

	$current_user->newpostlist = true;
	return;
}

# ------------------------------------------------------------------
# sf_set_last_visited()
#
# Set the last visited timestamp after user has disappeared
#	$userid:		Users ID
# ------------------------------------------------------------------
function sf_set_last_visited($userid)
{
	sf_update_member_item($userid, 'lastvisit', 0);
	return;
}

# ------------------------------------------------------------------
# sf_destroy_users_newposts()
#
# Destroy users new-post-list now they have departed
#	$userid:		Users ID
# ------------------------------------------------------------------
function sf_destroy_users_newposts($userid)
{
	$newpostlist['topics'][0]=0;
	$newpostlist['forums'][0]=0;
	sf_update_member_item($userid, 'newposts', $newpostlist);
	return;
}

# ------------------------------------------------------------------
# sf_update_users_newposts()
#
# Updates a users new-post-list on subsequent page loads
#	$newpostlist:		new-post-list
# ------------------------------------------------------------------
function sf_update_users_newposts($newpostlist)
{
	global $wpdb, $current_user, $sfglobals;

	if($newpostlist['topics'][0] == 0)
	{
		unset($newpostlist);
		$newpostlist=array();
		$newpostlist['topics']=array();
		$newpostlist['forums']=array();
	}
	$checktime=$sfglobals['member']['checktime'];

	$newpostlist['topics']=array_reverse($newpostlist['topics']);
	$newpostlist['forums']=array_reverse($newpostlist['forums']);

	$where = '';
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
		}

		# create where clause based on forums that current user can view
		if ($forum_ids != '')
		{
			$where.= "forum_id IN (" . implode(",", $forum_ids) . ") AND ";
		}
	}

	if ($where != '')
	{
		$records = $wpdb->get_results("SELECT DISTINCT topic_id, forum_id FROM ".SFPOSTS." WHERE ".$where."post_status = 0 AND (post_date > '".$checktime."') AND user_id != ".$current_user->ID." ORDER BY topic_id DESC LIMIT 50;", ARRAY_A);
	} else {
		$records = '';
	}

	if (!empty($records))
	{
		foreach($records as $record)
		{
			if(!in_array($record['topic_id'], $newpostlist['topics']))
			{
				$newpostlist['topics'][]=$record['topic_id'];
				$newpostlist['forums'][]=$record['forum_id'];
			}
		}
	}

	$newpostlist = sf_add_waiting_post_list($newpostlist);

	$newpostlist['topics']=array_reverse($newpostlist['topics']);
	$newpostlist['forums']=array_reverse($newpostlist['forums']);

	if(count($newpostlist['topics'])==0) $newpostlist['topics'][0]=0;
	if(count($newpostlist['forums'])==0) $newpostlist['forums'][0]=0;

	sf_update_member_item($current_user->ID, 'newposts', $newpostlist);
	sf_update_member_item($current_user->ID, 'checktime', 0);

	$current_user->newpostlist = true;

	return $newpostlist;
}

# ------------------------------------------------------------------
# sf_remove_users_newposts()
#
# Removes items from users new-post-list upon viewing them
#	$topicid:		the topic to remove from new-post-list
# ------------------------------------------------------------------
function sf_remove_users_newposts($topicid)
{
	global $current_user, $sfglobals;

	if ($current_user->member)
	{
		$newpostlist = $sfglobals['member']['newposts'];

		if (($newpostlist) && ($newpostlist['topics'][0] != 0))
		{
			if ((count($newpostlist['topics']) == 1) && ($newpostlist['topics'][0] == $topicid))
			{
				sf_destroy_users_newposts($current_user->ID);
			} else {
				$remove = -1;
				for ($x=0; $x < count($newpostlist['topics']); $x++)
				{
					if ($newpostlist['topics'][$x] == $topicid)
					{
						$remove = $x;
						break;
					}
				}
				if ($remove != -1)
				{
					array_splice($newpostlist['topics'], $remove, 1);
					array_splice($newpostlist['forums'], $remove, 1);
					sf_update_member_item($current_user->ID, 'newposts', $newpostlist);
				}
			}
		}
	}
	return;
}

# ------------------------------------------------------------------
# sf_is_in_users_newposts()
#
# Determines if topic is in current users new-post-list
#	$topicid:		the topic to look for
# ------------------------------------------------------------------
function sf_is_in_users_newposts($topicid)
{
	global $current_user, $sfglobals;

	if ($current_user->member)
	{
		$newpostlist = $sfglobals['member']['newposts'];
	}

	$found = false;
	if (($newpostlist['topics']) && ($newpostlist['topics'][0] != 0))
	{
		for ($x=0; $x < count($newpostlist['topics']); $x++)
		{
			if ($newpostlist['topics'][$x] == $topicid) $found=true;
		}
	}
	return $found;
}

# ------------------------------------------------------------------
# sf_add_waiting_post_list()
#
# Adds posts in waiting to admins new post list
#	$topics:		Current topics in list
# ------------------------------------------------------------------
function sf_add_waiting_post_list($newpostlist)
{
	global $wpdb, $current_user, $sfglobals;

	# If a moderator but not allowed to remove from queue then exclude queued posts from personal new post list
	if(($current_user->forumadmin == false) && ($current_user->moderator == true) && ($sfglobals['admin']['sfmodasadmin'] == false))
	{
		return $newpostlist;
	}

	if (empty($newpostlist['topics'][0]))
	{
		unset($newpostlist);
		$newpostlist=array();
		$newpostlist['topics']=array();
		$newpostlist['forums']=array();
	}

	# Add waiting if admin or moderator (->moderator covers both)
	if($current_user->moderator)
	{
		$records=$wpdb->get_results("SELECT DISTINCT topic_id, forum_id FROM ".SFWAITING, ARRAY_A);
		if($records)
		{
			foreach($records as $record)
			{
				if(!in_array($record['topic_id'], $newpostlist['topics']))
				{
					$newpostlist['topics'][]=$record['topic_id'];
					$newpostlist['forums'][]=$record['forum_id'];
				}
			}
		}
	}
	return $newpostlist;
}

# ******************************************************************
# ADMINS NEW POST QUEUE MANAGEMENT
# ******************************************************************

# ------------------------------------------------------------------
# sf_approve_post()
#
# Approve a post and take it out of moderation and the queue (if allowed)
# if postid is set then work on just that post and if topicid is set
# as well, then check with waiting for removal of the one post.
# if postid is zero and topicid is set - approve all in topic.
#	$fromBar		Set to true if called from Admins Bar
#	$postid:		the post to approve
#	$topicid		the topic to approve (if set then 'all')
#	$show			true if no return message is required
# ------------------------------------------------------------------
function sf_approve_post($fromBar, $postid=0, $topicid=0, $show=true)
{
	global $wpdb, $sfvars, $current_user, $sfglobals;

	if($postid == 0 && $topicid == 0) return;

	if(!$current_user->sfapprove)
	{
		if($show) update_sfnotice('sfmessage', '1@'.__('Access Denied', "sforum"));
		return;
	}

	if($postid != 0)
	{
		$wpdb->query("UPDATE ".SFPOSTS." SET post_status = 0 WHERE post_id=".$postid);
	}

	if($postid == 0 && $topicid != 0)
	{
		$wpdb->query("UPDATE ".SFPOSTS." SET post_status = 0 WHERE topic_id=".$topicid);
	}

	if($wpdb === false)
	{
		if($show) update_sfnotice('sfmessage', '1@'.__("Post Approval Failed", "sforum"));
	} else {
		if($show) update_sfnotice('sfmessage', '0@'.__("Post Approved", "sforum"));
		if($topicid == 0) $topicid = $sfvars['topicid'];

		if(($sfglobals['admin']['sfbaronly']==true && $fromBar==true) || ($sfglobals['admin']['sfbaronly'] == false))
		{
			sf_remove_from_waiting($fromBar, $topicid, $postid);
		}
	}
	return;
}

# ------------------------------------------------------------------
# sf_remove_from_waiting()
#
# Removes an item from admins queue when it is viewed (or from Bar)
#	$fromBar		Set to true if called from Admins Bar
#	$topicid:		the topic to remove (all posts is postid is 0)
#	$postid:		if specified removed the one post from topic
# ------------------------------------------------------------------
function sf_remove_from_waiting($fromBar, $topicid, $postid=0)
{
	global $wpdb, $current_user, $sfglobals;

	if(empty($topicid) || $topicid==0) return;

	$remove = false;

	if(($current_user->forumadmin) || ($current_user->moderator && $sfglobals['admin']['sfmodasadmin']))
	{
		if(($sfglobals['admin']['sfbaronly']==true && $fromBar==true) || ($sfglobals['admin']['sfbaronly'] == false))
		{
			$remove = true;
		}
	} else {
		# if moderator and mods posts are to be shown get out quick
		if($current_user->forumadmin == false &&  $current_user->moderator && $sfglobals['admin']['sfshowmodposts'])
		{
			return;
		}
	}

	if($remove == true)
	{
		# are we removing the whole topic?
		if($postid == 0)
		{
			# first check there are no posts still to be moderated in this topic...
			$rows = $wpdb->get_col("SELECT post_status FROM ".SFPOSTS." WHERE topic_id=".$topicid." AND post_status=1");
			If($rows)
			{
				return;
			} else {
				$wpdb->query("DELETE FROM ".SFWAITING." WHERE topic_id=".$topicid);
			}
		} else {
			# get the current row to see if the postid matches - and the post count is more than 1)
			$current = $wpdb->get_row("SELECT * FROM ".SFWAITING." WHERE topic_id=".$topicid);
			if($current)
			{
				# if post count is 1 may as well delete the row
				if($current->post_count == 1)
				{
					$wpdb->query("DELETE FROM ".SFWAITING." WHERE topic_id=".$topicid);
				} elseif($current->post_id != $postid)
				{
					$wpdb->query("UPDATE ".SFWAITING." SET post_count = ".($current->post_count-1)." WHERE topic_id=".$topicid);
				} else {
					$newpostid = $wpdb->get_var("SELECT post_id FROM ".SFPOSTS." WHERE topic_id=".$topicid." AND post_id > ".$postid." ORDER BY post_id DESC LIMIT 1");
					if($newpostid)
					{
						$wpdb->query("UPDATE ".SFWAITING." SET post_count = ".($current->post_count-1).", post_id = ".$newpostid." WHERE topic_id=".$topicid);
					} else {
						$wpdb->query("DELETE FROM ".SFWAITING." WHERE topic_id=".$topicid);
					}
				}
			}
		}
	}
	return;
}

# ------------------------------------------------------------------
# sf_remove_waiting_queue()
#
# Removes the admin queue unless a post is awaiting approval
# ------------------------------------------------------------------
function sf_remove_waiting_queue()
{
	global $wpdb;

	$rows = $wpdb->get_col("SELECT topic_id FROM ".SFWAITING);
	if($rows)
	{
		$queued = array();
		foreach($rows as $row)
		{
			$queued[]=$row;
		}
		foreach($queued as $topic)
		{
			sf_remove_from_waiting(true, $topic);
		}
	}
	return;
}

# ------------------------------------------------------------------
# sf_get_waiting_url()
#
# Creates the new post urls and counts in the Admin Bar
#	$postlist:		array from the admin queue of posts
# ------------------------------------------------------------------
function sf_get_waiting_url($postlist, $pageview, $shownew)
{
	global $sfvars, $current_user, $sfglobals;

	# check if topic in url - if yes and it is in postlist - remove it.
	$newposts = array();
	$index = 0;
	$modcount = 0;
	$readcount = 0;
	$fixed = '0';
	if ($sfglobals['member']['admin_options']['sfbarfix']) $fixed = '1';

	if ($postlist)
	{
		if (!empty($sfvars['topicid']))
		{
			$topicid=$sfvars['topicid'];

			foreach ($postlist as $forum)
			{
				if (isset($forum['topics']))
				{
					foreach ($forum['topics'] as $topic)
					{
						if (isset($topic['posts']))
						{
							foreach ($topic['posts'] as $post)
							{
								$readcount++;
								if (!isset($post['topic_id']) || $post['topic_id'] != $topicid)
								{
									$newposts[$index]->post_id=$post['post_id'];
									# increment mod count for this user
									if ($post['post_status'] == 1) $modcount++;
									$index++;
								} else {
									if ($post['post_status'] == 1)
									{
										$modcount++;
										$newposts[$index]->post_id=$post['post_id'];
										$index++;
									}
								}
							}
						}
					}
				}
			}
		} else {
			$newposts = $postlist;
			foreach ($postlist as $forum)
			{
				if (isset($forum['topics']))
				{
					foreach ($forum['topics'] as $topic)
					{
						if (isset($topic['posts']))
						{
							foreach ($topic['posts'] as $post)
							{
								$readcount++;
								# increment mod count for this user
								if ($post['post_status'] == 1) $modcount++;
							}
						}
					}
				}
			}
		}
	}
	if($newposts)
	{
		$readcount = $readcount - $modcount;
	} else {
		$readcount = 0;
		$modcount = 0;
	}
	$unreadclass='sfrednumber';
	$needmodclass='sfbluenumber';
	if($readcount == 0) $unreadclass='sfrednumberzero';
	if($modcount == 0) $needmodclass='sfbluenumberzero';

	$site = SFHOMEURL."index.php?sf_ahah=newposts";
	$numbersurl = SFHOMEURL."index.php?sf_ahah=autoupdate";

	$href = 'javascript:void(0)';
	$jscall = ' onclick="sfjgetNewPostList(\''.$site.'\', \''.$numbersurl.'\', \''.$fixed.'\' )"';
	$spinner = '<img class="inline_edit" id="sfbarspinner" src="'.SFADMINIMAGES.'working.gif" alt="" />';
	$out = '<span><a class="sficon" href="'.$href.'" '.$jscall.'><img class="sfalignleft" src="'. SFRESOURCES .'newpost.png" alt="" title="'.esc_attr(__("New Posts", "sforum")).'" /><span id="sfunread" class="'.$unreadclass.' sfalignleft" title="'.esc_attr(__("New Posts", "sforum")).'">'.$readcount.'</span><span id="sfmod" class="'.$needmodclass.' sfalignleft" title="'.esc_attr(__("Awaiting Approval", "sforum")).'">'.$modcount.'</span>&nbsp;'.sf_render_icons("New Posts").'</a></span>'.$spinner."\n";

	return $out;
}

# ******************************************************************
# URL GENERATION
# ******************************************************************

# ------------------------------------------------------------------
# sf_get_forum_url_newposts()
#
# Builds the admin new post url
#	$forumslug:		forum slug
#	$forumname:		forum name
# ------------------------------------------------------------------
function sf_get_forum_url_newposts($forumslug, $forumname)
{
	$out = '<a href="'.sf_build_url($forumslug, '', 1, 0).'">Forum: '.$forumname.'</a>'."\n";
	return $out;
}

# ------------------------------------------------------------------
# sf_get_topic_url_newpost()
#
# Builds the admin new post url
#	$forumslug:		forum slug
#	$topicid:		id of topic
#	$postid:		if of post
#	$postindex:		index of post if known
# ------------------------------------------------------------------
function sf_get_topic_url_newpost($forumslug, $topicid, $postid, $postindex=0)
{
	$topic=sf_get_topic_record($topicid);
	$out = '<a href="'.sf_build_url($forumslug, $topic->topic_slug, 0, $postid, $postindex).'">'.sf_filter_title_display($topic->topic_name).'</a>'."\n";

	return $out;
}

# RELACEMENT FUNCTION FOR ABOVE EXCEPT THE ABOVE IS USED BY TWO TEMPLATE TAGS THAT NEED RE-ENGINEERING
function sf_get_topic_newpost_url($forumslug, $topicslug, $topicname, $postid, $postindex=0)
{
	$out = '<a href="'.sf_build_url($forumslug, $topicslug, 0, $postid, $postindex).'">'.$topicname.'</a>'."\n";
	return $out;
}

# ------------------------------------------------------------------
# sf_get_topic_url()
#
# Builds a topic url including all icons etc
#	$forumslug:		forum slug for url
#	$topicslug:		topic slug for url
#	etc.
# ------------------------------------------------------------------
function sf_get_topic_url($forumslug, $topicslug, $topicname)
{
	global $sfvars;

	$topicname=sf_filter_title_display($topicname);

	if($sfvars['searchvalue'])
	{
		$out.= '<a href="'.sf_build_url($forumslug, $topicslug, 1, 0);
		if(strpos(SFURL, '?') === false)
		{
			$out.= '?value';
		} else {
			$out.= '&amp;value';
		}
		$out.= '='.$sfvars['searchvalue'].'&amp;type='.$sfvars['searchtype'].'&amp;include='.$sfvars['searchinclude'].'&amp;search='.$sfvars['searchpage'].'">'.sf_filter_title_display($topicname).'</a>'."\n";
	} else {
		$out = '<a href="'.sf_build_url($forumslug, $topicslug, 1, 0).'">'.sf_filter_title_display($topicname).'</a>'."\n";
	}
	return $out;
}

# ------------------------------------------------------------------
# sf_get_post_url()
#
# Builds a post url including all icons etc
#	$forumslug:		forum slug for url
#	$topicslug:		topic slug for url
#	$postid			id of the post
#	$postindex:		position of post within the topic (for paging)
# ------------------------------------------------------------------
function sf_get_post_url($forumslug, $topicslug, $postid, $postindex=0, $title='', $posttip='')
{

	$out = '<a href="'.sf_build_url($forumslug, $topicslug, 0, $postid, $postindex).'"';
	if($title != '' && $posttip != '')
	{
		$out.= ' title="'.$posttip.'"';
	}
	$out.= '>';
	if($title=='')
	{
		if($posttip=='') $posttip = esc_attr(__("Go to Post", "sforum"));
		$out.= '<img src="'. SFRESOURCES .'gopost.png" alt="" title="'.$posttip.'" />';
	} else {
		$out.= $title;
	}
	$out.= '</a>';
	return $out;
}

# ------------------------------------------------------------------
# sf_get_forum_search_url()
#
# Builds a forum search url with the query vars
# ------------------------------------------------------------------
function sf_get_forum_search_url()
{
	global $sfvars;

	if(isset($_GET['ret']))
	{
		$forumid='all';
	} else {
		$forumid=$sfvars['forumid'];
	}
	$out = '<a class="sficon" href="'.sf_build_qurl('forum='.$forumid, 'value='.$sfvars['searchvalue'], 'type='.$sfvars['searchtype'], 'include='.$sfvars['searchinclude'], 'search='.$sfvars['searchpage']).'">'."\n";
	return $out;
}

# ------------------------------------------------------------------
# sf_build_subforum_list()
#
# Constructs array of subforums for later display control
# ------------------------------------------------------------------
function sf_build_subforum_list($forums, $parent)
{
	global $subforums, $subforumids, $sfglobals;


	foreach($forums as $forum)
	{
		if($forum['parent'] == $parent && $forum['forum_id'] != $parent)
		{
			$subforums[]=$forum;
			$subforumids[]=$forum['forum_id'];

			if($sfglobals['display']['groups']['showallsubs'] && $forum['children'])
			{
				$subforums = sf_build_subforum_list($forums, $forum['forum_id']);
			}
		}
	}
	return $subforums;
}

# ------------------------------------------------------------------
# sf_build_subforum_list()
#
# Adds subforum counts into man forum (Group View) if option set
# and swaps in last topic/post data if subforum last post newer
# ------------------------------------------------------------------
function sf_massage_forum_counts($parent, &$stats, $subforums)
{
	if($subforums)
	{
		foreach($subforums as $sub)
		{
			$thissub = $sub['forum_id'];

			# only check if subforum actually has some topics
			if (!empty($stats[$thissub]['topic_count']))
			{
				# default the parent counts?
				if (empty($stats[$parent]['topic_count']))
				{
					$stats[$parent]['topic_count'] = 0;
					$stats[$parent]['post_count'] = 0;
					$stats[$parent]['udate'] = 0;
				}

				# add topc and post counts to parent
				$stats[$parent]['topic_count']+= $stats[$thissub]['topic_count'];
				$stats[$parent]['post_count']+= $stats[$thissub]['post_count'];

				# now check if the last subforum post is newer than parent or if no topics in parent
				if (empty($stats[$parent]['topic_count']) || (($stats[$thissub]['udate'] > $stats[$parent]['udate']) && $stats[$thissub]['post_status'] == 0))
				{
					# copy it over
					$stats[$parent]['proxy'] = $stats[$thissub]['forum_id'];
					$stats[$parent]['proxy_slug'] = $sub['forum_slug'];
					$stats[$parent]['post_id'] = $stats[$thissub]['post_id'];
					$stats[$parent]['topic_id'] = $stats[$thissub]['topic_id'];
					$stats[$parent]['topic_name'] = $stats[$thissub]['topic_name'];
					$stats[$parent]['post_date'] = $stats[$thissub]['post_date'];
					$stats[$parent]['udate'] = $stats[$thissub]['udate'];
					$stats[$parent]['guest_name'] = $stats[$thissub]['guest_name'];
					$stats[$parent]['user_id'] = $stats[$thissub]['user_id'];
					$stats[$parent]['post_status'] = $stats[$thissub]['post_status'];
					$stats[$parent]['display_name'] = $stats[$thissub]['display_name'];
					$stats[$parent]['post_index'] = $stats[$thissub]['post_index'];
					$stats[$parent]['topic_slug'] = $stats[$thissub]['topic_slug'];
					$stats[$parent]['post_tip'] = $stats[$thissub]['post_tip'];
				}
			}
		}
	}
	return $stats;
}

# ******************************************************************
# MISCELLANEOUS ROUTINES
# ******************************************************************

# ------------------------------------------------------------------
# sf_push_topic_page()
#
# called on forum display to note current topic page user is viewing.
#	$forumid:
#	$page:
# ------------------------------------------------------------------
function sf_push_topic_page($forumid, $page)
{
	update_sfsetting($_SERVER['REMOTE_ADDR'], $forumid.'@'.$page);
	return;
}

# ------------------------------------------------------------------
# sf_pop_topic_page()
#
# called on topic display to set breadcrumb to correct page
# if same forum
#	$forumid:
# ------------------------------------------------------------------
function sf_pop_topic_page($forumid)
{
	$page = 1;
	$check = get_sfsetting($_SERVER['REMOTE_ADDR']);
	# if no record then resprt to page 1
	if($check == -1) return $page;
	$check = explode('@', $check);
	# is it the same forum?
	if($check[0] == $forumid)
	{
		$page = $check[1];
	}
	return $page;
}

# ------------------------------------------------------------------
# sf_display_banner()
#
# displays optional banner instead of page title
# ------------------------------------------------------------------
function sf_display_banner()
{
	global $sfglobals;

	if(!empty($sfglobals['display']['pagetitle']['banner']))
	{
		return '<img id="sfbanner" src="'.esc_url($sfglobals['display']['pagetitle']['banner']).'" alt="" />';
	}
	return '';
}

# ------------------------------------------------------------------
# sf_render_icons()
#
# displays an icon text if not turned off in the options
#	$icontext:		text to display if needed
# ------------------------------------------------------------------
function sf_render_icons($icontext)
{
	global $sfglobals;

	if ($sfglobals['icons'][$icontext] == 1)
	{
		return ' '.__($icontext, "sforum").' ';
	} else {
		return '';
	}
}

# ------------------------------------------------------------------
# sf_get_topic_status_flag()
#
# Returns status entry $pos for forums set $statusset
#	$statusset:		stats set name used in forum
#	$pos:			position in list (base 1 so take 1 off)
# ------------------------------------------------------------------
function sf_get_topic_status_flag($statusset, $pos)
{
	global $session_topicstatus;

	if(!isset($session_topicstatus[$statusset]))
	{
		$list=sf_get_sfmeta('topic-status', false, $statusset);
		$session_topicstatus[$statusset] = explode(',', $list[0]['meta_value']);
	}
	return $session_topicstatus[$statusset][$pos-1];
}

# ------------------------------------------------------------------
# sf_topic_status_select()
#
# Returns status entry $pos for forums set $statusset
#	$statusset:		stats set name used in forum
#	$current:		current position in list (base 1 so take 1 off)
#	$inline:		Sets up the auto changng of status
#	$search:		If search operation do not check permissions
# ------------------------------------------------------------------
function sf_topic_status_select($statusset, $current = -1, $inline = false, $search = false)
{
	global $wpdb, $sfvars, $current_user;

	if(!$search)
	{
		if(!$current_user->sftopicstatus) return '';
	}

	$set = sf_get_sfmeta('topic-status', false, $statusset);

	if($set)
	{
		$list = $set[0]['meta_value'];
		$list = explode(',', $list);

		$out='';

		if($inline)
		{
			$site = SFHOMEURL."index.php?sf_ahah=admintools&action=ss&amp;id=".$sfvars['topicid']."&amp;set=".$statusset;
			$out.= '<select class="sfcontrol" name="statvalue" onchange="javascript:sfjsetStatus(this,	\''.$site.'\')" >'."\n";
		} else {
			$out.= '<select class="sfquicklinks sfcontrol" name="statvalue">'."\n";
		}

		$out.= '<option value="0">'.__("Select Status:", "sforum").'</option>'."\n";
		$default='';
		for($x=0; $x<count($list); $x++)
		{
			if(($current-1) == $x)
			{
				$default = 'selected="selected" ';
			} else {
				$default - null;
			}
			$out.='<option '.$default.'value="'.($x+1).'">'.sf_filter_title_display(trim($list[$x])).'</option>'."\n";
			$default='';
		}
	}
	$out.='</select>';
	return $out;
}

# ------------------------------------------------------------------
# sf_create_stats_keys()
#
# Create list of stats keys for first/last post column
# Ensures that 0 and 1 are the same if only one post
# ------------------------------------------------------------------
function sf_create_stats_keys($keys)
{
	$list=array();

	if(is_array($keys))
	{
		$list = array_keys($keys);

		if(count($keys) == 2)
		{
			$list[2]=$list[1];
			$list[1]=$list[0];
		}
	} else {
		$list[1] = $keys;
		$list[2] = $keys;
	}
	return $list;
}

# ------------------------------------------------------------------
# sf_check_unlogged_user()
#
# checks if 'guest' is a user not logged in and returns their name
# ------------------------------------------------------------------
function sf_check_unlogged_user()
{
	$sfmemberopts = sf_get_option('sfmemberopts');
	if(isset($_COOKIE['sforum_'.COOKIEHASH]) && $sfmemberopts['sfcheckformember'])
	{
		# Yes it is - a user not logged in
		$username = $_COOKIE['sforum_'.COOKIEHASH];
		return $username;
	}
	return 0;
}

# ------------------------------------------------------------------
# sf_report_post_send()
#
# Send 'report post' email to forum admin
# ------------------------------------------------------------------
function sf_report_post_send()
{
	global $current_user;

	$eol = "\r\n";
	$msg = '';

	# if either the posturl or the comments are empty then just forget it
	if(empty($_POST['posturl']) || empty($_POST['postreport'])) return;

	# clean up the content for the plain text email
	$post_content = html_entity_decode($_POST['postcontent']);
	$post_content = sf_filter_content_display($post_content);

	if($current_user->guest && $current_user->guestname='')
	{
		$reporter = __('A Guest Visitor', 'sforum');
	} else {
		# if it got ths far but there is no display name then it's bogus - leave
		if(empty($current_user->display_name)) return;
		$reporter = __('Member', 'sforum').' '.sf_filter_name_display($current_user->display_name);
	}

	$msg.= sprintf(__("%s has reported the following post as questionable", "sforum"), $reporter).$eol.$eol;
	$msg.= $_POST['posturl'].$eol;
	$msg.= stripslashes($_POST['postauthor']).$eol;
	$msg.= $post_content.$eol.$eol;
	$msg.= __("Comments", "sforum").$eol;
	$msg.= $_POST['postreport'].$eol;

	$email_sent = sf_send_email(get_option('admin_email'), sprintf(__('[%s] Questionable Post Report', "sforum"), get_option('blogname')), $msg);

	if($email_sent[0])
	{
		$returnmsg = '0@';
	} else {
		$returnmsg = '1@';
	}
	update_sfnotice('sfmessage', $returnmsg.$email_sent[1]);
	return;
}

# ------------------------------------------------------------------
# sf_zone_datetime()
#
# Sets date time for sql queries based on user options
#	$datefield:		sql field being queried
# ------------------------------------------------------------------
function sf_zone_datetime($datefield)
{
	global $current_user;

	$zone = $current_user->timezone;

	if($zone == 0) return $datefield;
	if($zone < 0)
	{
		$out='DATE_SUB('.$datefield.', INTERVAL '.abs($zone).' HOUR) as '.$datefield;
	} else {
		$out='DATE_ADD('.$datefield.', INTERVAL '.abs($zone).' HOUR) as '.$datefield;
	}
	return $out;
}

# ------------------------------------------------------------------
# sf_date()
#
# Formats a date and time for display
#	$type	't'=time  'd'=date
#	$data	The actual date string
# ------------------------------------------------------------------
function sf_date($type, $data)
{
	if($type == 'd')
	{
		return date_i18n(SFDATES, mysql2date('U', $data));
	} else {
		return date_i18n(SFTIMES, mysql2date('U', $data));
	}
}

# ------------------------------------------------------------------
# sf_check_url()
#
# Check url has http (else browser will assume relative link
#	$url:		URL to be checked
# ------------------------------------------------------------------
function sf_check_url($url)
{
	if($url == 'http://' || $url == 'https://')
	{
		$url='';
	}
	return $url;
}

# ******************************************************************
# POST & USER DISPLAY AND FILTERS AND USERS EDITOR CHOICE
# ******************************************************************

# ------------------------------------------------------------------
# sf_push_newuser()
#
# Adds new user stats new user list
#	$name:		new users disp0lay name
# ------------------------------------------------------------------
function sf_push_newuser($id, $name)
{
	$sfcontrols = sf_get_option('sfcontrols');
	$num = $sfcontrols['shownewcount'];
	if ($num == 0 || empty($num)) return;

	$newuserlist = $sfcontrols['newuserlist'];
	if (is_array($newuserlist))
	{
		# is this name already listed?
		foreach ($newuserlist as $user)
		{
			if ($user['name'] == $name) return;
		}

		# is the array full? if so pop one off
		$ccount = count($newuserlist);
		while ($ccount > ($num-1))
		{
			array_pop($newuserlist);
			$ccount--;
		}

		# add new user
		array_unshift($newuserlist, array('id' => esc_sql($id), 'name' => esc_sql($name)));
	} else {
		# first name nto the emoty array
		$newuserlist[0]['id'] = esc_sql($id);
		$newuserlist[0]['name'] = esc_sql($name);
	}
	$sfcontrols['newuserlist'] = $newuserlist;

	sf_update_option('sfcontrols', $sfcontrols);
	return;
}

# ------------------------------------------------------------------
# sf_remove_newuser()
#
# Adds new user stats new user list
#	$id:		new users id
# ------------------------------------------------------------------
function sf_remove_newuser($id)
{
	$sfcontrols = sf_get_option('sfcontrols');
	$newuserlist = $sfcontrols['newuserlist'];
	if (is_array($newuserlist))
	{
		# remove the user if present
		foreach ($newuserlist as $index => $user)
		{
			if ($user['id'] == $id)
			{
				unset($newuserlist[$index]);
			}
		}
		$newuserlist = array_values($newuserlist);
	}
	$sfcontrols['newuserlist'] = $newuserlist;
	sf_update_option('sfcontrols', $sfcontrols);

	return;
}

# ------------------------------------------------------------------
# sf_update_newuser_name()
#
# Updates users name of changed in profile
#	$oldname:		users old name
#	$newname:		users new name
# ------------------------------------------------------------------
function sf_update_newuser_name($oldname, $newname)
{
	$sfcontrols = sf_get_option('sfcontrols');
	$newuserlist = $sfcontrols['newuserlist'];
	if(is_array($newuserlist))
	{
		for($x=0; $x<count($newuserlist)+1; $x++)
		{
			if($newuserlist[$x]['name'] == $oldname)
			{
				$newuserlist[$x]['name'] = $newname;
			}
		}
	}
	$sfcontrols['newuserlist'] = $newuserlist;
	sf_update_option('sfcontrols', $sfcontrols);
	return;
}

# ------------------------------------------------------------------
# sf_build_name_display()
#
# Cleans user name and attaches profile or website link if set
#	$userid:		id of the user
#	$username:		name of the user or guest
#	$stats:			Optional - if stats set to true
# ------------------------------------------------------------------
function sf_build_name_display($userid, $username, $stats=false)
{
	global $current_user;

	if(!$userid) return $username;

	if($current_user->sfprofiles)
	{
		$sfprofile=sf_get_option('sfprofile');

		# is profile linked to user name
		if(($sfprofile['profilelink'] == 1 || ($sfprofile['profileinstats']==1 && $stats==true)))
		{
			# link to profile
			return sf_attach_user_profilelink($userid, $username);
		} elseif($sfprofile['weblink'] == 1)
		{
			# link to website
			return sf_attach_user_weblink($userid, $username);
		}
	}

	# neither permission or profile/web link
	return $username;
}

# ------------------------------------------------------------------
# sf_build_avatar_display()
#
# Attaches profile or website link if set to Avatar
#	$userid:		id of the user
#	$avatar:		Avatar display code
# ------------------------------------------------------------------
function sf_build_avatar_display($userid, $avatar, $link)
{
	global $current_user;

	if(!$userid) return $avatar;

	if($current_user->sfprofiles)
	{
		$sfprofile=sf_get_option('sfprofile');

		# is profile linked to user name
		if($sfprofile['profilelink'] == 2)
		{
			# link to profile
			if ($link){
				return sf_attach_user_profilelink($userid, $avatar);
			} else {
				return $avatar;
			}
		} elseif($sfprofile['weblink'] == 2)
		{
			# link to website
			return sf_attach_user_weblink($userid, $avatar);
		}
	}

	# neither permission or profile/web link
	return $avatar;
}

# ------------------------------------------------------------------
# sf_attach_user_weblink()
#
# Create a link to a users website if they have entered one in their
# profile record.
#	$userid:		id of the user
#	$targetitem:	user name, avatar or web icon - sent as code
#	$returnitem:	return targetitem if nothing found
# ------------------------------------------------------------------
function sf_attach_user_weblink($userid, $targetitem, $returnitem=true)
{
	global $wpdb, $session_weblink;

	if(isset($session_weblink[$userid]))
	{
		$website=$session_weblink[$userid];
	} else {
		$website = $wpdb->get_var("SELECT user_url FROM ".SFUSERS." WHERE ID=".$userid);
		if(empty($website))
		{
			$website='#';
			$session_weblink[$userid]=$website;
		}
	}

	if($website != '#')
	{
		$website = sf_check_url($website);
		$session_weblink[$userid]=$website;

		if($website != '')
		{
			return '<a href="'.$website.'" target="_blank">'.$targetitem.'</a>';
		}
	}

	# No wesbite link exists
	if($returnitem)
	{
		return $targetitem;
	} else {
		return '';
	}
}

# ------------------------------------------------------------------
# sf_attach_user_profilelink()
#
# Create a link to a users profile using the global profile display
# settings
#	$userid:		id of the user
#	$targetitem:	user name, avatar or web icon - sent as code
# ------------------------------------------------------------------
function sf_attach_user_profilelink($userid, $targetitem)
{
	global $wpdb;

	$sfprofile=sf_get_option('sfprofile');
	switch($sfprofile['displaymode'])
	{
		case 1:
			# SF Popup profile
			$site = SFHOMEURL."index.php?sf_ahah=profile&u=".$userid;
			return '<a rel="nofollow" href="'.$site.'" onclick="return hs.htmlExpand(this, { objectType: \'ajax\', preserveContent: true, height:675, width: 750} )">'.$targetitem.'</a>';
			break;

		case 2:
			# SF Profile page
			$user = new WP_User($userid);
			$site = SFURL.'profile/'.urlencode($user->user_login).'/';
			return '<a href="'.$site.'">'.$targetitem.'</a>';
			break;

		case 3:
			# BuddyPress Profile page
			$user = new WP_User($userid);
			$site = SFHOMEURL.'members/'.str_replace(' ', '', $user->user_login).'/profile/';
			return '<a href="'.$site.'">'.$targetitem.'</a>';
			break;

		case 4:
			# WordPress Authors page
			$userkey = $wpdb->get_var("SELECT user_nicename FROM ".SFUSERS." WHERE ID=".$userid);
			if($userkey)
			{
				$site = SFSITEURL.'author/'.$userkey.'/';
				return '<a href="'.$site.'">'.$targetitem.'</a>';
			} else {
				return $targetitem;
			}
			break;

		case 5:
			# Handoff to user specified page
			if($sfprofile['displaypage'])
			{
				$out = '<a href="'.$sfprofile['displaypage'];
				if($sfprofile['displayquery']) $out.= '?'.sf_filter_title_display($sfprofile['displayquery']).'='.$userid;
				$out.= '">'.$targetitem.'</a>';
			} else {
				$out = $targetitem;
			}
			return $out;
			break;

		default:
			return $targetitem;
			break;
	}
}

# ------------------------------------------------------------------
# sf_build_profile_formlink()
#
# Create a link to the profile form preferred
#	$userid:		id of the user
# ------------------------------------------------------------------
function sf_build_profile_formlink($userid)
{
	global $current_user;

	$sfprofile=sf_get_option('sfprofile');
	switch($sfprofile['formmode'])
	{
		case 1:
			# SPF form
			$edit = '';
			if ($userid != $current_user->ID)
			{
				$user = new WP_User($userid);
				$edit = urlencode($user->user_login).'/edit/';
			}
			$site = SFURL.'profile/'.$edit;
			return $site;
			break;

		case 2:
			# WordPress form
			return trailingslashit(admin_url('user-edit.php?user_id='.$userid));
			break;

		case 3:
			# BuddyPress Profile page
			$user = new WP_User($userid);
			$site = SFHOMEURL.'members/'.str_replace(' ', '', $user->user_login).'/profile/edit/';
			return $site;
			break;

		case 4:
			# Handoff to user specified form
			if($sfprofile['formpage'])
			{
				$out = $sfprofile['formpage'];
				if($sfprofile['formquery']) $out.= '?'.sf_filter_title_display($sfprofile['formquery']).'='.$userid;
			} else {
				$out = '';
			}
			return $out;
			break;
	}
}

# ------------------------------------------------------------------
# sf_filter_wp_ampersand()
#
# Replace & with &amp; in urls
#	$url:		url to be filtered
# ------------------------------------------------------------------
function sf_filter_wp_ampersand($url)
{
	return str_replace('&', '&amp;', $url);
}

# ------------------------------------------------------------------
# sf_retrieve_policy_document()
#
# $policy	registration or privacy
# ------------------------------------------------------------------
function sf_retrieve_policy_document($policy)
{
	$sfpolicy = sf_get_option('sfpolicy');
	if($policy == 'privacy' ? $item='sfprivfile' : $item='sfregfile');
	if(!empty($sfpolicy[$item]))
	{
		# text file option
		$sfconfig = sf_get_option('sfconfig');
		$filename = WP_CONTENT_DIR.'/'.$sfconfig['policies'].'/'.$sfpolicy[$item];
		if (file_exists($filename) == false)
		{
			return __("Policy Document Not Found", "sforum");
		} else {
			$handle = fopen($filename, "r");
			$contents = fread($handle, filesize($filename));
			fclose($handle);
			return $contents;
		}
	} else {
		# sfmeta option
		$policytext = sf_get_sfmeta($policy, 'policy');
		return sf_filter_text_display($policytext[0]['meta_value']);
	}
}

# ------------------------------------------------------------------
# sf_wp_list_pages()
#
# Filter Call
# Sorts bug in wp_list_pages by swapping out modified title
#	$ptext: Page titles html string
# ------------------------------------------------------------------
function sf_wp_list_pages($ptext)
{
	global $sfvars, $sfglobals;

	if(!empty($sfvars['seotitle']))
	{
		$seotitle = $sfvars['seotitle'];
		$ptext = str_replace($seotitle, SFPAGETITLE, $ptext);
		$seotitle=html_entity_decode($seotitle, ENT_QUOTES);
		$seotitle=htmlspecialchars($seotitle, ENT_QUOTES);
		$ptext = str_replace($seotitle, SFPAGETITLE, $ptext);
		$seotitle = sf_filter_title_save($seotitle);
		$ptext = str_replace($seotitle, SFPAGETITLE, $ptext);
		$ptext = str_replace(strtoupper($seotitle), SFPAGETITLE, $ptext);
	} else {
		if($sfglobals['display']['pagetitle']['banner'] || $sfglobals['display']['pagetitle']['notitle'])
		{
			$ptext = str_replace('></a>', '>'.SFPAGETITLE.'</a>', $ptext);
		}
	}
	return $ptext;
}

# ------------------------------------------------------------------
# sf_setup_page_title()
#
# Filter Call
# Sets up the page title option
#	$title: Page title
# ------------------------------------------------------------------
function sf_setup_page_title($title)
{
	global $sfglobals, $sfvars;

	if (trim($title) == trim(SFPAGETITLE))
	{
		if (!empty($sfglobals['display']['pagetitle']['banner'])) return '';
		if ($sfglobals['display']['pagetitle']['notitle']) return '';

		$seo = array();
		$seo = sf_get_option('sfseo');
		$title = sf_setup_title($title, ' '.$seo['sfseo_sep'].' ');
		$sfvars['seotitle']=$title;
	}
	return $title;
}

# ------------------------------------------------------------------
# sf_setup_title()
#
# Support Routine
# Sets up the page title option
# ------------------------------------------------------------------
function sf_setup_title($title, $sep)
{
	global $sfvars;

	$sfseo = sf_get_option('sfseo');

	$forumslug = $sfvars['forumslug'];
	if (!empty($forumslug) && $forumslug != 'all' && $sfseo['sfseo_forum'])
	{
		$title = sf_get_forum_name($forumslug).$sep.$title;
	}

	$topicslug = $sfvars['topicslug'];
	if (!empty($topicslug) && $sfseo['sfseo_topic'])
	{
		$title = sf_get_topic_name($topicslug).$sep.$title;
	}

	if ($sfseo['sfseo_page'])
	{
		$pm = urlencode($sfvars['pm']);
		if (!empty($pm))
		{
			$pmview = "inbox";
			$box = urlencode($sfvars['box']);
			if (!empty($box)) $pmview = $box;
			return __("Private Messaging", "sforum").' '.ucfirst($pmview).$sep.$title;
		}

		$profile = urlencode($sfvars['profile']);
		if (!empty($profile) && $profile == 'edit') return __("Edit Member Profile", "sforum").$sep.$title;
		if (!empty($profile) && $profile == 'show') return __("Member Profile", "sforum").$sep.$title;
		if (!empty($profile) && $profile == 'permissions') return __("Member Permissions", "sforum").$sep.$title;
		if (!empty($profile) && $profile == 'buddies') return __("Member Buddies List", "sforum").$sep.$title;

		$newposts = urlencode($sfvars['newposts']);
		if (!empty($newposts) && $newposts == 'all') return __("New/Unread Posts", "sforum").$sep.$title;

		$list = urlencode($sfvars['list']);
		if (!empty($list) && $list == 'members') return __("List of Members", "sforum").$sep.$title;

		$policy = urlencode($sfvars['policy']);
		if (!empty($policy) && $policy == 'show') return __("Site Policy", "sforum").$sep.$title;

		if (isset($_POST['rpaction'])) return __("Report Post", "sforum").$sep.$title;
	}

	return $title;
}

function sf_canonical_url()
{
	global $sfvars;

	if ($sfvars['pageview'] == 'profileshow' || $sfvars['pageview'] == 'profileedit')
	{
		$url = SFURL.'profile/';
	} else if ($sfvars['pageview'] == 'permissions') {
		$url = SFURL.'permissions/';
	} else if ($sfvars['pageview'] == 'buddies') {
		$url = SFURL.'profile/buddies/';
	} else if ($sfvars['pageview'] == 'permissions') {
		$url = SFURL.'profile/permissions/';
	} else if ($sfvars['pageview'] == 'newposts') {
		$url = SFURL.'newposts/';
	} else if ($sfvars['pageview'] == 'list') {
		$page = '';
		if ($sfvars['page'] > 0) $page = '/page-'.$sfvars['page'];
		$url = SFURL.'members'.$page.'/';
	} else if ($sfvars['pageview'] == 'policy') {
		$url = SFURL.'policy/';
	} else if ($sfvars['pageview'] == 'pm' && $sfvars['box'] == 'inbox') {
		$url = SFURL.'private-messaging/inbox/';
	} else if ($sfvars['pageview'] == 'pm' && $sfvars['box'] == 'sentbox') {
		$url = SFURL.'private-messaging/sentbox/';
	} else {
		# check for linked topic
		$sfpostlinking = sf_get_option('sfpostlinking');
		$topic = sf_get_topic_record_from_slug($sfvars['topicslug']);
		if (!empty($topic) && $topic->blog_post_id && $sfpostlinking['sflinkurls'] == 3) # pointing linke topic to blog post?
		{
			$url = get_permalink($topic->blog_post_id);
		} else {
			$url = sf_build_url($sfvars['forumslug'], $sfvars['topicslug'], $sfvars['page'], 0);
		}
	}

	return $url;
}

function sf_get_permalink($link, $id, $sample)
{
	if ($id == sf_get_option('sfpage') && in_the_loop()) $link = sf_canonical_url();
	return $link;
}

function sf_avatar($avatar, $id_or_email, $size)
{
	$uid = 0;
	$icon = 'guest';
	$email = '';
	$guest_email = '';
	if (is_numeric($id_or_email))
	{
		$id = (int) $id_or_email;
		$user = get_userdata($id);
		if ($user)
		{
			$uid = $id;
			$email = sf_filter_email_display($user->user_email);
			if (sf_is_forum_admin($id) ? $icon = 'admin' : $icon = 'user');
		}
	} elseif (is_object($id_or_email)) {
		if (!empty($id_or_email->user_id))
		{
			$id = (int) $id_or_email->user_id;
			$user = get_userdata($id);
			if ($user)
			{
				$uid = $id;
				$email = sf_filter_email_display($user->user_email);
				if (sf_is_forum_admin($id) ? $icon = 'admin' : $icon = 'user');
			}
		} elseif (!empty($id_or_email->comment_author_email)) {
			$guest_email = $id_or_email->comment_author_email;
		}
	} else {
		$guest_email = $id_or_email;
	}

	# was an email address passed? if so, see if its a user
	if ($guest_email != '')
	{
		$user = get_user_by_email($guest_email);
		if ($user)
		{
			$uid = $user->ID;
			$email = sf_filter_email_display($user->user_email);
			if (sf_is_forum_admin($uid) ? $icon = 'admin' : $icon = 'user');
			$guest_email = '';
		}
	}

	# replace the wp avatar image src with our spf img src
	$pattern = '/<img[^>]+src[\\s=\'"]+([^"\'>\\s]+)/is';
	$sfavatar = sf_render_avatar($icon, $uid, $email, $guest_email, true, $size, false, $avatar);
	preg_match($pattern, $sfavatar, $sfmatch);
	preg_match($pattern, $avatar, $wpmatch);
	$avatar = str_replace($wpmatch[1], $sfmatch[1], $avatar);

	return $avatar;
}

# ------------------------------------------------------------------
# sf_title_hook()
#
# called by start of the wp loop action to output hook data before the page title
# ------------------------------------------------------------------
function sf_title_hook()
{
	echo sf_process_hook('sf_hook_pre_title', '');
}

# This is pretty filthy. Doing math in hex is much too weird. It's more likely to work, this way!
# Provided from UTW. Thanks.
function sf_get_color_scaled($scale_color, $min_color, $max_color)
{
	$scale_color = $scale_color / 100;

	$minr = hexdec(substr($min_color, 1, 2));
	$ming = hexdec(substr($min_color, 3, 2));
	$minb = hexdec(substr($min_color, 5, 2));

	$maxr = hexdec(substr($max_color, 1, 2));
	$maxg = hexdec(substr($max_color, 3, 2));
	$maxb = hexdec(substr($max_color, 5, 2));

	$r = dechex(intval((($maxr - $minr) * $scale_color) + $minr));
	$g = dechex(intval((($maxg - $ming) * $scale_color) + $ming));
	$b = dechex(intval((($maxb - $minb) * $scale_color) + $minb));

	if (strlen($r) == 1) $r = '0'.$r;
	if (strlen($g) == 1) $g = '0'.$g;
	if (strlen($b) == 1) $b = '0'.$b;

	return '#'.$r.$g.$b;
}

?>