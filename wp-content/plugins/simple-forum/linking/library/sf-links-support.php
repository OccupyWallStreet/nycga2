<?php
/*
Simple:Press
Blog Linking - general support routines
$LastChangedDate: 2010-09-11 14:51:26 -0700 (Sat, 11 Sep 2010) $
$Rev: 4613 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# ------------------------------------------------------------------
# sf_blog_links_control()
#
# MOVED to sf-public.php to make it always available as can be
# used for canonical urls.
# ------------------------------------------------------------------

# ------------------------------------------------------------------
# sf_make_excerpt()
#
# Creates an excerpt of x number of words from post content
#	$content	The text of the post
#	$words		Word count required (defaults to 50)
# ------------------------------------------------------------------
function sf_make_excerpt($content, $words)
{
	if((empty($words)) || ($words == 0)) $words = 50;

	if($content != '')
	{
		$length = $words;
		$content = str_replace(']]>', ']]&gt;', $content);

		if($length > count(preg_split('/[\s]+/', strip_tags($content), -1)))
		{
			return $content;
		}

		$text_bits = preg_split('/([\s]+)/', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
		$in_tag = false;
		$n_words = 0;
		$content = '';
		foreach($text_bits as $chunk)
		{
			if(0 < preg_match('/<[^>]*$/s', $chunk))
			{
				$in_tag = true;
			} elseif(0 < preg_match('/>[^<]*$/s', $chunk)) {
				$in_tag = false;
			}

			if(!$in_tag && '' != trim($chunk) && substr($chunk, -1, 1) != '>')
			{
				$n_words++;
			}
			$content .= $chunk;

			if($n_words >= $length && !$in_tag)
			{
				break;
			}
		}
		$content = $content . '&#8230;';
		$content = force_balance_tags($content);
	}
	return $content;
}

# ------------------------------------------------------------------
# sf_sync_blog_tags()
#
# Creates topic tags based on blog post tags if used
#	$postid		WP Post id of the link
#	$data		forum/topic postmeta data
# ------------------------------------------------------------------
function sf_sync_blog_tags($postid, $forumid, $topicid)
{
	# get tags for wp blog post
	$terms = apply_filters( 'get_the_tags', wp_get_object_terms($postid, 'post_tag') );

	# get the forum id and topic id ($data => forumid@topicid)
	$forum = sf_get_forum_record($forumid);

	# only do tags if the forum is setup for tags and the blog post has tags
	if ($forum->use_tags && $terms)
	{
		$tags = array();
		foreach ($terms as $term)
		{
			$tags[] = $term->name;
		}
		# need tags in a list
		$tags = implode(",", $tags);

		# now save the topic tags but in case its an update use change routine
		sf_change_topic_tags($topicid, $tags);
	}
}

# ------------------------------------------------------------------
# sf_transform_bloglink_label()
#
# Formats the label to display on blog posts
#	$postid		WP Post id of the link
#	$links		forum/topic postmeta data
#	$show_img	Support template tag no image option
# ------------------------------------------------------------------
function sf_transform_bloglink_label($postid, $links, $show_img=true)
{
	if(!defined('SFRESOURCES')) {
		sf_setup_global_constants();
	}

	$out = '';

	$sfpostlinking = array();
	$sfpostlinking = sf_get_option('sfpostlinking');
	$text = sf_filter_title_display($sfpostlinking['sflinkblogtext']);
	$icon = '<img src="'.SFRESOURCES.'bloglink.png" alt="" />';
	$postcount = sf_get_posts_count_in_linked_topic($links->topic_id, false);
	$href = '<a href="'.sf_build_url(sf_get_forum_slug($links->forum_id), sf_get_topic_slug($links->topic_id), 1, 0).'">';

	if(!$postcount)
	{
		# break the link
		sf_blog_links_control('delete', $postid);
		return $content;
	}

    if ($show_img)
	{
		if(strpos($text, '%ICON%') !== false)
		{
			$text = str_replace('%ICON%', $icon, $text);
		}
	} else {
		$text = str_replace('%ICON%', '', $text);
	}

	if(strpos($text, '%FORUMNAME%') !== false)
	{
		$text = str_replace('%FORUMNAME%', sf_get_forum_name_from_id($links->forum_id), $text);
	}

	if(strpos($text, '%TOPICNAME%') !== false)
	{
		$text = str_replace('%TOPICNAME%', sf_get_topic_name_from_id($links->topic_id), $text);
	}

	if(strpos($text, '%POSTCOUNT%') !== false)
	{
		$text = str_replace('%POSTCOUNT%', $postcount, $text);
	}

	if(strpos($text, '%LINKSTART%') !== false)
	{
		$text = str_replace('%LINKSTART%', $href, $text);
	} else {
		$text = $href.$text;
	}

	if(strpos($text, '%LINKEND%') !== false)
	{
		$text = str_replace('%LINKEND%', '</a>', $text);
	} else {
		$text = $text.'</a>';
	}

	$out = '<span class="sfforumlink">'.$text.'</span>';

	return $out;
}
?>