<?php
/*
Simple:Press
Blog Linking - Blog side support routines
$LastChangedDate: 2011-03-11 08:12:39 -0700 (Fri, 11 Mar 2011) $
$Rev: 5665 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

include_once(SF_PLUGIN_DIR.'/linking/forms/sf-form-blog-link.php');
include_once(SF_PLUGIN_DIR.'/linking/library/sf-links-support.php');

# ------------------------------------------------------------------
# sf_save_blog_link()
#
# Filter call
# Called on  a Post Save to create the blog/forum Link
#	$postid		id of the blog post to link to
# ------------------------------------------------------------------
function sf_save_blog_link($postid)
{
	global $current_user;

	# can the user do this?
	if(isset($_POST['sflink']) && $_POST['sflink'])
	{
		$blogpost = sf_get_postrecord($postid);
		if($blogpost)
		{
			# if revision or autosave go get parent post id
			if($blogpost->post_type == 'revision' && $blogpost->post_parent > 0)
			{
				$postid = $blogpost->post_parent;
			}

			# Prepare data
			$forumid = sf_esc_int($_POST['sfforum']);
			if(empty($forumid) || $forumid == 0)
			{
				return;
			}

			if($_POST['sfedit']==false ? $editmode='0' : $editmode='1');

			# Check if we already have a topic
			$topicid = '0';
			$checktopic = sf_get_linkedtopic($postid);
			if(!empty($checktopic) && $checktopic > 0)
			{
				$topicid = $checktopic;
			}

			# go get link record if already saved before
			$links = sf_blog_links_control('read', $postid);
			if($links)
			{
				# if all links fields have value no need to linger
				if (($links->forum_id == $forumid) && ($links->topic_id == $topicid) && ($links->syncedit == $editmode))
				{
					# all data present - so finish up
					return;
				}
			}

			# Save links record - will update of already exists
			sf_blog_links_control('save', $postid, $forumid, $topicid, $editmode);
		}
	}
	return;
}

# ------------------------------------------------------------------
# sf_publish_blog_link()
#
# Filter call
# Called on  a Post Publish to create the blog/forum Link
#	$post		The Actual Post Object
# ------------------------------------------------------------------
function sf_publish_blog_link($post)
{
	global $wpdb, $current_user;

	# Check post status for published as it calls this hook regardless of state
	if($post->post_status != 'publish') return;

	# go get link record if already saved before
	$links = sf_blog_links_control('read', $post->ID);

	if(!$links) return;

	# Prepare data
	$forumid = 0;
	if(isset($_POST['sfforum']))
	{
		$forumid = sf_esc_int($_POST['sfforum']);
	} else {
		if($links) $forumid = $links->forum_id;
	}

	if(empty($forumid) || $forumid == 0)
	{
		return;
	}

	$editmode = '0';
	if((isset($_POST['sfedit']) && $_POST['sfedit'] == true) || $links->syncedit == '1')
	{
		$editmode = '1';
	}

	# Check if we already have a topic
	$topicid = '0';
	$checktopic = sf_get_linkedtopic($post->ID);
	if(!empty($checktopic) && $checktopic > 0)
	{
		$topicid = $checktopic;
	}

	if($links)
	{
		# if all links fields have value no need to linger
		if (($links->forum_id == $forumid) && ($links->topic_id == $topicid && $topicid > 0) && ($links->syncedit == $editmode))
		{
			# all data present - so finish up
			return;
		}
	}

	# so - go ahead and create the topic and link
	$sfpostlinking = array();
	$sfpostlinking = sf_get_option('sfpostlinking');

	$post_title = sf_filter_title_save($post->post_title);
	$slug = sf_create_slug($post_title, 'topic');
	$now = current_time('mysql');

	# now create the topic and post records - it should already be escaped fully.
	$sql = "INSERT INTO ".SFTOPICS." (topic_name, topic_slug, topic_date, forum_id, user_id, post_count, blog_post_id, post_id) VALUES ('".$post_title."', '".$slug."', '".$now."', ".$forumid.", ".$post->post_author.", 1, ".$post->ID.", ".$post->ID.");";
	$wpdb->query($sql);

	$topicid = $wpdb->insert_id;

	# check the topic slug and if empty use the topic id
	if(empty($slug))
	{
		$slug = 'topic-'.$topicid;
		$thistopic = $wpdb->query("
			UPDATE ".SFTOPICS."
			SET topic_slug='".$slug."', topic_name='".$slug."'
			WHERE topic_id=".$topicid);
	}

	$postcontent = sf_prepare_linked_topic_content($post->post_content, $post->post_excerpt, $sfpostlinking);

	$sql = "INSERT INTO ".SFPOSTS." (post_content, post_date, topic_id, user_id, forum_id) VALUES ('".$postcontent."', '".$now."', ".$topicid.", ".$post->post_author.", ".$forumid.");";
	$wpdb->query($sql);

	# and then update links table with forum AND topic
	sf_blog_links_control('save', $post->ID, $forumid, $topicid, $editmode);

	# Update authors forum post count
	$postcount = (sf_get_member_item($post->post_author, 'posts')+1);
	sf_update_member_item($post->post_author, 'posts', $postcount);

	# sync blog and forum tags
	sf_sync_blog_tags($post->ID, $forumid, $topicid);

	# Update forum, topic and post index data
	sf_build_forum_index(sf_esc_int($forumid));
	sf_build_post_index($topicid, $slug);

	return;
}

# ------------------------------------------------------------------
# sf_prepare_linked_topic_content()
#
# prepares blog post content for the topic post
# 	$content		full content o the blog post
#	$excerpt		excerpt of the blog post
#	$sfpostlinking	link options
# ------------------------------------------------------------------
function sf_prepare_linked_topic_content($content, $excerpt, $sfpostlinking)
{
	$content = sf_filter_content_save($content, 'new');

	switch($sfpostlinking['sflinkexcerpt'])
	{
		case 3:
		$postcontent = sf_filter_content_save($excerpt, 'new');
		break;

		case 2:
		$postcontent = sf_make_excerpt($content, $sfpostlinking['sflinkwords']);
		break;

		default:
		$postcontent = $content;
		break;
	}

	return apply_filters('sph_add_custom_post_content', $postcontent);
}

# ------------------------------------------------------------------
# sf_update_blog_link()
#
# Filter call
# Called on a Post Edit to update the blog/forum Link
#	$postid		id of the blog post to link to
# ------------------------------------------------------------------
function sf_update_blog_link($postid)
{
	global $wpdb, $current_user;

	# This could be an update to post content OR a new link on existing post so check
	# If new link on exisiting post this gets run before the 'save_post' hook so we need to force a save post first
	sf_save_blog_link($postid);

	$links = sf_blog_links_control('read', $postid);

	if($links && $links->topic_id == 0)
	{
		# then a new link on existing blog post so get post object
		$postrecord = sf_get_postrecord($postid);
		if($postrecord)
		{
			sf_publish_blog_link($postrecord);
			return;
		}
	}

	# probably an edit to content then
	if((isset($_POST['sfedit']) && $_POST['sfedit'] == true) || $links->syncedit == true)
	{
		$post = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."posts WHERE ID=".$postid);
		if($post)
		{
			# first - get the options
			$sfpostlinking = array();
			$sfpostlinking = sf_get_option('sfpostlinking');

			$postcontent = sf_prepare_linked_topic_content($post->post_content, $post->post_excerpt, $sfpostlinking);

			$sql = "UPDATE ".SFPOSTS." SET post_content='".$postcontent."' WHERE topic_id=".$links->topic_id." AND post_index=1";
			$wpdb->query($sql);
		}
	}
	return;
}

# ------------------------------------------------------------------
# sf_show_blog_link()
#
# Filter call
# Adds the user-defined link text to a blog post
#	$content	The content of the target post
# ------------------------------------------------------------------
if(!function_exists('sf_show_blog_link')):
function sf_show_blog_link($content)
{
	global $wp_query;

	$postid = $wp_query->post->ID;
	$links = sf_blog_links_control('read', $postid);
	if(!$links) return $content;

	#show only on single pages?
	$sfpostlinking = sf_get_option('sfpostlinking');
	if ($sfpostlinking['sflinksingle'] && !is_single()) return $content;

	$out = sf_transform_bloglink_label($postid, $links, true);

	if($sfpostlinking['sflinkabove'])
	{
		return $out.$content;
	} else {
		return $content.$out;
	}
}
endif;

# ------------------------------------------------------------------
# sf_delete_blog_link()
#
# Action call
# Removes forum link if blog post is deleted
#	$postid		ID of the post being deleted
# ------------------------------------------------------------------
function sf_delete_blog_link($postid)
{
	global $current_user;

	if(!$current_user->sfbreaklink) return;

	include_once(SF_PLUGIN_DIR.'/linking/sf-links-forum.php');

	$links = sf_blog_links_control('read', $postid);
	if($links)
	{
		# Check - this might be a Revision record
		if($links->forum_id != 0)
		{
			sf_break_blog_link($links->topic_id, $postid);
		}
	}
	return;
}

# ------------------------------------------------------------------
# sf_add_admin_link_column()
#
# Filter call
# Adds link column to edit posts/pages
# ------------------------------------------------------------------
function sf_add_admin_link_column($defaults)
{
    $defaults['spflinked'] = __('Forum Linked', "sforum");
    return $defaults;
}

# ------------------------------------------------------------------
# sf_add_admin_link_column()
#
# Action call
# Displays link column info in edit posts/pages
# ------------------------------------------------------------------
function sf_show_admin_link_column($column, $postid)
{
	if($column == 'spflinked')
	{
		$links = sf_blog_links_control('read', $postid);
		if($links)
		{
			echo __("Linked to forum", "sforum").':<br />'.sf_get_forum_name_from_id($links->forum_id);
		}
	}
}


function sf_get_postrecord($postid)
{
	global $wpdb;
	return $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."posts WHERE ID=".$postid);
}

function sf_get_linkedtopic($blogpostid)
{
	global $wpdb;
	return $wpdb->get_var("SELECT topic_id FROM ".SFTOPICS." WHERE blog_post_id=".$blogpostid);
}

?>