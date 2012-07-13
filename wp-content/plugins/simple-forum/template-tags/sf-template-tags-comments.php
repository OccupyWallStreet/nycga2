<?php
/*
Simple:Press
Template Tag(s) - Linked Topics as Comments
$LastChangedDate: 2010-12-17 09:42:54 -0700 (Fri, 17 Dec 2010) $
$Rev: 5081 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

/* 	=====================================================================================

	sf_comments_number($no_comment="0 Comments", $one_comment="1 Comment", $many_comment="% Comments", $blogcomments)

	Replaces the WP Template Tag: comments_number()
	Supplies Count of topic posts and can optionally include the standard blog comments in the total

	parameters:

		$no_comment		Used for zero comments									text
		$one_comment	Used for one comment									text
		$many_comment	Used for multiple comments								text
		$blogcomments	Include Standard Blog Comments 							(true or false)
		$postid			Option to specify postid, otherwise $wp_query is used   integer
 	===================================================================================*/

function sf_comments_number($no_comment="0 Comments", $one_comment="1 Comment", $many_comment="% Comments", $blogcomments=false, $postid=0)
{
	global $wp_query;

	sf_initialise_globals();

	$result = $no_comment;
	$total = 0;
	if (empty($postid)) $postid = $wp_query->post->ID;
	$links = sf_blog_links_control('read', $postid);

	# If linked get the post count (-1 of course)
    if ($links && sf_can_view_forum($links->forum_id))
	{
		$sfpostlinking = sf_get_option('sfpostlinking');

		# link found for this post
		$total = (sf_get_posts_count_in_linked_topic($links->topic_id, $sfpostlinking['sfhideduplicate'])-1);
	}

	# If to include standard blog comments add that number
	if($blogcomments)
	{
		$total += get_comments_number($postid);
	}

	if($total > 0)
	{
		if($total == 1 ? $result=$one_comment : $result=str_replace('%', number_format_i18n($total), $many_comment));
	}
	echo $result;

	return;
}

/* 	=====================================================================================

	sf_first_topic_post_link($blog_post_id, $link_text

	Creates a link to the first topic post in a blog post/topic linked thread

	parameters:

		$blog_post_id		The ID pof the blog post ($post->ID in Post Loop)
		$link text			What text to display as the link
 	===================================================================================*/

function sf_first_topic_post_link($blogpostid, $linktext)
{
	global $wpdb;

	$topiclink='';

    $blogpostid = sf_esc_int($blogpostid);
	if($blogpostid)
	{
		$links = sf_blog_links_control('read', $blogpostid);
	    if ($links && sf_can_view_forum($links->forum_id))
		{
			$postid = $wpdb->get_var("SELECT post_id FROM ".SFPOSTS." WHERE topic_id=".$links->topic_id." AND post_index=1");
			$topiclink = '<a href="'.sf_build_url(sf_get_forum_slug($links->forum_id), sf_get_topic_slug($links->topic_id), 1, $postid, 1).'">'.$linktext.'</a>';
		}
	}
	echo $topiclink;
	return;
}

/* 	=====================================================================================

	sf_last_topic_post_link($blog_post_id, $link_text

	Creates a link to the last topic post in a blog post/topic linked thread

	parameters:

		$blog_post_id		The ID pof the blog post ($post->ID in Post Loop)
		$link text			What text to display as the link
 	===================================================================================*/

function sf_last_topic_post_link($blogpostid, $linktext)
{
	global $wpdb;

	$topiclink='';

    $blogpostid = sf_esc_int($blogpostid);
	if($blogpostid)
	{
		$links = sf_blog_links_control('read', $blogpostid);
	    if ($links && sf_can_view_forum($links->forum_id))
		{
			$postid = $wpdb->get_var("SELECT post_id FROM ".SFPOSTS." WHERE topic_id=".$links->topic_id." ORDER BY post_index DESC LIMIT 1");
			$topiclink = '<a href="'.sf_build_url(sf_get_forum_slug($links->forum_id), sf_get_topic_slug($links->topic_id), 0, $postid).'">'.$linktext.'</a>';
		}
	}
	echo $topiclink;
	return;
}

?>