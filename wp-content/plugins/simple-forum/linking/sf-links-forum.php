<?php
/*
Simple:Press
Blog Linking - Forum side support routines
$LastChangedDate: 2011-01-03 14:29:28 -0700 (Mon, 03 Jan 2011) $
$Rev: 5253 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# ------------------------------------------------------------------
# sf_forum_show_blog_link()
#
# Displays the link in the forum topic
#	$postid		ID of the post being deleted
# ------------------------------------------------------------------
function sf_forum_show_blog_link($postid)
{
	$sfpostlinking = array();
	$sfpostlinking = sf_get_option('sfpostlinking');
	$text = $sfpostlinking['sflinkforumtext'];
	$icon = '<img src="'.SFRESOURCES.'bloglink.png" alt=""/>';
	$href = '<a href="'.get_permalink($postid).'">';

	if(strpos($text, '%ICON%') !== false)
	{
		$text = str_replace('%ICON%', $icon, $text);
	}

	if(strpos($text, '%BLOGTITLE%') !== false)
	{
		$text = str_replace('%BLOGTITLE%', sf_get_blog_title_from_id($postid), $text);
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

	$out = '<span class="sfbloglink">'.$text.'</span>';

	return $out;
}

# ------------------------------------------------------------------
# sf_break_blog_link()
#
# Breaks the link - removes nothing
#	$topicid	SPF Topic id of the link
#	$postid		WP Post id of the link
# ------------------------------------------------------------------
function sf_break_blog_link($topicid, $postid)
{
	global $wpdb, $sfglobals;

	# dont update forum if its locked down
    if ($sfglobals['lockdown'])
    {
		update_sfnotice('sfmessage', __('This Forum is Currently Locked - Access is Read Only - Not Updated', "sforum"));
		return;
    }

	# remove from postmeta
	sf_blog_links_control('delete', $postid);

	# and set blog_oost_id to zero in topic record
	$wpdb->query("UPDATE ".SFTOPICS." SET blog_post_id = 0 WHERE topic_id = ".$topicid.";");
	return;
}

?>