<?php
/*
Simple:Press
Blog Linking - Blog side comment support
$LastChangedDate: 2011-06-24 01:23:05 -0700 (Fri, 24 Jun 2011) $
$Rev: 6374 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# ------------------------------------------------------------------
# sf_process_new_comment()
#
# NOT DIRECTLY CALLABLE - 'comment_post' & 'wp_set_comment_status'
# Fires when a new comment is added (Action hook)
#	$cid:		ID of new comment
#	$status:	"spam", "1' or "0" ("1" is approved)
#	-- OR --	"approve", "delete", "spam", "hold"
# ------------------------------------------------------------------
function sf_process_new_comment($cid, $commentstatus)
{
	global $wpdb;

	$commentstatus = trim($commentstatus);

	if($commentstatus == '0' || $commentstatus == 'spam' || $commentstatus == 'hold') return;

	# First - if not approved then back out
	if($commentstatus == '1' || $commentstatus == "approve")
	{

		# Next find the post ID to see if it is a linked post
		$crecord = $wpdb->get_row("SELECT * FROM ".$wpdb->comments." WHERE comment_ID=".$cid);
		if(!$crecord) return;

		# if a pingback or trackback then leave it alone
		if($crecord->comment_type == 'pingback' || $crecord->comment_type == 'trackback') return;

		# Is it a linked post?
		$links = sf_blog_links_control('read', $crecord->comment_post_ID);
		if(!$links) return;

		# This means it is an approved, normal comment with linking
		sf_create_post_from_comment($crecord, $links);

		# Do we delete original comment?
		$sfpostlinking = sf_get_option('sfpostlinking');
		if($sfpostlinking['sfkillcomment'])
		{
			$wpdb->query("DELETE FROM ".$wpdb->comments." WHERE comment_ID=".$cid);
		}
	}

	# And just in case - was it a delete action?
	if($commentstatus == 'delete')
	{
		# Check posts for thie comment ID
		$post = $wpdb->get_row("SELECT * FROM ".SFPOSTS." WHERE comment_ID=".$cid);
		if($post)
		{
			$sfpostlinking = sf_get_option('sfpostlinking');
			if($sfpostlinking['sfeditcomment'])
			{
				sf_initialise_globals($post->forum_id);
				sf_delete_post($post->post_id, $post->topic_id, $post->forum_id, false);
			}
		}
	}
	return;
}

# ------------------------------------------------------------------
# sf_create_post_from_comment()
#
# Create new topic post from comment
#	$crecord:		The comment record
#	$links:			Blog Linking IDs
# ------------------------------------------------------------------
function sf_create_post_from_comment($crecord, $links)
{
	global $wpdb;

	include_once(SF_PLUGIN_DIR.'/library/sf-post-support.php');

	$topicid=sf_esc_int($links->topic_id);
	$forumid=sf_esc_int($links->forum_id);

	$newpost = array();

	$newpost['guestname'] 		= '';
	$newpost['guestemail']		= '';

	$newpost['forumid']			= $forumid;
	$newpost['topicid']			= $topicid;

	$newpost['postcontent']		= $crecord->comment_content;
	$newpost['postcontent'] 	= sf_filter_content_save($newpost['postcontent'], 'new');

	$newpost['postpin']			= 0;
	$newpost['statvalue']		= '';
	$newpost['posttimestamp'] 	= "'" . current_time('mysql') . "'";
	$newpost['userid']			= $crecord->user_id;
	$newpost['poststatus']		= 0;

	$newpost['forumslug'] 		= sf_get_forum_slug($forumid);
	$newpost['topicslug']		= sf_get_topic_slug($topicid);

	$newpost['ip']				= $crecord->comment_author_IP;

	if(empty($crecord->user_id) || $crecord->user_id == 0)
	{
		$newpost['guestname'] 	= sf_filter_name_save($crecord->comment_author);
		$newpost['guestemail']	= sf_filter_email_save($crecord->comment_author_email);
	}

	# Get post count in topic to enable index setting
	$index=$wpdb->get_var("SELECT COUNT(post_id) FROM ".SFPOSTS." WHERE topic_id = ".$newpost['topicid']);
	$index++;

	$sql =  "INSERT INTO ".SFPOSTS;
	$sql .= " (post_content, post_date, topic_id, forum_id, user_id, guest_name, guest_email, post_pinned, post_index, post_status, comment_id, poster_ip) ";
	$sql .= "VALUES (";
	$sql .= "'".$newpost['postcontent']."', ";
	$sql .= $newpost['posttimestamp'].", ";
	$sql .= $newpost['topicid'].", ";
	$sql .= $newpost['forumid'].", ";
	$sql .= $newpost['userid'].", ";
	$sql .= "'".$newpost['guestname']."', ";
	$sql .= "'".$newpost['guestemail']."', ";
	$sql .= $newpost['postpin']. ", ";
	$sql .= $index.", ";
	$sql .= $newpost['poststatus'].", ";
	$sql .= $crecord->comment_ID.", ";
	$sql .= "'".$ip."');";

	$wpdb->query($sql);
	$newpost['postid'] = $wpdb->insert_id;

	if($crecord->user_id)
	{
		$postcount = sf_get_member_item($crecord->user_id, 'posts');
		$postcount++;
		sf_update_member_item($newpost['userid'], 'posts', $postcount);
	}

	# construct new url
	$newpost['url']=sf_build_url($newpost['forumslug'], $newpost['topicslug'], 0, $newpost['postid']);

	# save hook
	sf_process_hook('sf_hook_post_save', array($newpost['url'], $newpost['postcontent']));

	# send out email notifications
	$newpost['emailmsg'] = sf_email_notifications($newpost);

	# Update forum, topic and post index data
	sf_build_forum_index($newpost['forumid']);
	sf_build_post_index($newpost['topicid'], $newpost['topicslug']);

	return;
}

# ------------------------------------------------------------------
# sf_update_comment_post()
#
# NOT DIRECTLY CALLABLE
# Updates topic post when comment is edited
#	$cid	Comment ID
# ------------------------------------------------------------------
function sf_update_comment_post($cid)
{
	global $wpdb;

	# find the post ID to see if it is a linked post
	$crecord = $wpdb->get_row("SELECT * FROM ".$wpdb->comments." WHERE comment_ID=".$cid);
	if(!$crecord) return;
	$links = sf_blog_links_control('read', $crecord->comment_post_ID);
	if(!$links) return;

	# So we need to perform an update
	$sfpostlinking = sf_get_option('sfpostlinking');
	if($sfpostlinking['sfeditcomment'])
	{
		$postcontent = $crecord->comment_content;
		if($postcontent)
		{
			$postid = $wpdb->get_var("SELECT post_id FROM ".SFPOSTS." WHERE comment_id=".$cid);
			if($postid)
			{
				$postcontent = sf_filter_content_save($postcontent, 'edit');
				$sql = "UPDATE ".SFPOSTS." SET post_content='".$postcontent."' WHERE post_id=".$postid;
				$wpdb->query($sql);
			}
		}
	}
	return;
}

# ------------------------------------------------------------------
# sf_topic_as_comments()
#
# NOT DIRECTLY CALLABLE
# Adds the topic posts to the comments stream
#	$comments	Passed in by the comments_array filter
# ------------------------------------------------------------------
function sf_topic_as_comments($comments)
{
	global $wp_query;

	sf_initialise_globals();
	$sfpostlinking = sf_get_option('sfpostlinking');

	if($comments)
	{
		$postid = $comments[0]->comment_post_ID;
	} else {
		$postid = $wp_query->post->ID;
	}
	$links = sf_blog_links_control('read', $postid);
	if(!$links) return $comments;

	# quick permission check
 	if(!sf_can_view_forum($links->forum_id)) return $comments;

	$topicid = $links->topic_id;

	$thread = sf_get_thread_for_comments($topicid, $sfpostlinking['sfhideduplicate']);

	if($thread)
	{
		$index = count($comments);

		foreach($thread as $post)
		{
			$comments[$index]->comment_ID = $links->forum_id.'@'.$links->topic_id.'@'.$post['post_id'];
			$comments[$index]->comment_post_ID = $postid;

			if(!$post['user_id'])
			{
				$comments[$index]->comment_author = sf_filter_name_display($post['guest_name']);
				$comments[$index]->comment_author_email = sf_filter_email_display($post['guest_email']);
				$comments[$index]->comment_author_url = "";
			} else {
				$comments[$index]->comment_author = sf_filter_name_display($post['display_name']);
				$comments[$index]->comment_author_email = sf_filter_email_display($post['user_email']);
				$comments[$index]->comment_author_url = sf_check_url($post['user_url']);
			}
			$comments[$index]->comment_author_IP = $post['poster_ip'];
			$comments[$index]->comment_date = $post['post_date'];
			$comments[$index]->comment_date_gmt = $post['post_date'];

			if($post['post_status'] == 1)
			{
				$comments[$index]->comment_content = '<b><em>'.__("Post Awaiting Approval by Forum Administrator", "sforum").'</em></b>';
			} else {
				$comments[$index]->comment_content = sf_filter_content_display($post['post_content']);
			}

			$comments[$index]->comment_karma = 0;
			$comments[$index]->comment_approved = 1;
			$comments[$index]->comment_agent = "";

			if($sfpostlinking['sflinkcomments'] == 2)
			{
				$comments[$index]->comment_type = "comment";
			} else {
				$comments[$index]->comment_type = "forum";
			}
			$comments[$index]->comment_parent = 0;
			$comments[$index]->user_id = $post['user_id'];
			$comments[$index]->comment_subscribe = "N";

			$index++;
		}
	}
	if($sfpostlinking['sflinkcomments'] == 2)
	{
		usort($comments, 'sp_sort_comments');
	}

	return $comments;
}

function sp_sort_comments($a, $b)
{
    if ($a->comment_date == $b->comment_date) {
        return 0;
    }
    $sort=get_option('comment_order');
    if($sort == 'asc') {
	    return ($a->comment_date < $b->comment_date) ? -1 : 1;
	} else {
	    return ($a->comment_date > $b->comment_date) ? 1 : -1;
	}
}

function sf_get_thread_for_comments($topicid, $hidedupes)
{
	global $wpdb;

	$hide='';
	if($hidedupes) $hide = " AND comment_id IS NULL ";

	$records = $wpdb->get_results(
			"SELECT ".SFPOSTS.".post_id, post_content, post_date, ".SFPOSTS.".user_id, guest_name, guest_email, post_status, poster_ip,
			".SFMEMBERS.".display_name, user_url, user_email
			 FROM ".SFPOSTS."
			 LEFT JOIN ".SFUSERS." ON ".SFPOSTS.".user_id = ".SFUSERS.".ID
			 LEFT JOIN ".SFMEMBERS." ON ".SFPOSTS.".user_id = ".SFMEMBERS.".user_id
			 WHERE topic_id = ".$topicid." AND post_index > 1".$hide."
			 ORDER BY post_id ".strtoupper(get_option('comment_order')).";", ARRAY_A);

	return $records;
}

# ------------------------------------------------------------------
# sf_remove_edit_comment_link()
#
# NOT DIRECTLY CALLABLE
# Sets the forum post to the 'edit' link in cmments when viewed
# bya  site admin.
# ------------------------------------------------------------------
function sf_remove_edit_comment_link($link, $id)
{
	if(strpos($id, '@'))
	{
		$link = '';
		$target = explode('@', $id);

		if((isset($target[0]) && $target[0] != 0) && (isset($target[1]) && $target[1] != 0) && (isset($target[2]) && $target[2] != 0))
		{
			$link = '<span><form action="'.sf_build_url(sf_get_forum_slug($target[0]), sf_get_topic_slug($target[1]), 0, $target[2]).'" method="post" name="admineditpost'.$target[2].'">'."\n";
			$link.= '<input type="hidden" name="adminedit" value="'.$target[2].'" />'."\n";
			$link.= '<a class="comment-edit-link" href="javascript:document.admineditpost'.$target[2].'.submit();">('.__("Edit", "sforum").')</a>'."\n";
			$link.= '</form></span>'."\n";
		}
	}

	return $link;
}

# ------------------------------------------------------------------
# sf_add_comment_type()
#
# NOT DIRECTLY CALLABLE
# Sets up new comment typoe of 'forum; so topic posts as comments
# can show users avatar
# 	$list	Current list of commnent types
# ------------------------------------------------------------------
function sf_add_comment_type($list)
{
	$list[]='forum';
	return $list;
}

?>