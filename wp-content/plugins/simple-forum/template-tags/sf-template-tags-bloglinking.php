<?php
/*
Simple:Press
Template Tag(s) - Blog Linking Specific
$LastChangedDate: 2010-05-15 14:31:34 -0700 (Sat, 15 May 2010) $
$Rev: 4025 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}


/* 	=====================================================================================

	sf_blog_linked_tag($postid, $show_img=true)

	Allows display of forum topic link for blog linked post outside of the post content

	parameters:

		$postid			id of the blog post					number				required
		$show_img		display blog linked image			true/fase			true
 	===================================================================================*/

function sf_blog_linked_tag($postid, $show_img=true)
{
	include_once(SF_PLUGIN_DIR.'/linking/library/sf-links-support.php');

    $postid = sf_esc_int($postid);
    if (empty($postid)) return;

	sf_initialise_globals();

    $links = sf_blog_links_control('read', $postid);
    if ($links && sf_can_view_forum($links->forum_id))
    {
		echo sf_transform_bloglink_label($postid, $links, $show_img);
	}
}


/* 	=====================================================================================

	sf_linked_topic_post_count()

	displays the number of topic posts in the currently displayed linked blog post

	parameters: None

	For use with in the wp loop

 	===================================================================================*/

function sf_linked_topic_post_count()
{
	global $wp_query;

	sf_initialise_globals();

	$result = '';
	$postid = $wp_query->post->ID;
	$links = sf_blog_links_control('read', $postid);

    if ($links && sf_can_view_forum($links->forum_id))
	{
		# link found for this post
		$result = sf_get_posts_count_in_linked_topic($links->topic_id, false);
	}
	echo $result;
	return;
}



?>