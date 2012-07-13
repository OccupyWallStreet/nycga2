<?php
/*
Simple:Press
Forum Permalink Functions
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# --------------------------------------------------------------
# sf_build_url()
#
# Main URL building routine
# To use pass forum and topic slugs. Page if not known should
# always be 1 or 0. If a post id is passed (else use zero), the
# routine will go and get the correct page number for the post
# within the topic
#	$forumslug:		forum link
#	$topicslug:		topic link
#	$pageid:		page (if know - if post will calculate)
#	$postid:		post link if relevant (or zero)
#	$postindex:		sequence number of post if relevant
#
# CHANGE IN 4.2.0:
# ==============
# If the POST ID is passed AND a positive PAGE ID then it WILL
# compile a url with that page number. if the page
# number is NOT known when passing a Post ID then page must be
# passed as zero
# --------------------------------------------------------------
function sf_build_url($forumslug, $topicslug, $pageid, $postid, $postindex=0)
{
	if ($postid != 0 && $pageid == 0)
	{
		$pageid = sf_determine_page($forumslug, $topicslug, sf_esc_int($postid), sf_esc_int($postindex));
	}

	$url = SFURL;
	if ($forumslug) $url.= $forumslug;
	if ($topicslug) $url.= '/'.$topicslug;
	if ($pageid > 1) $url.= '/page-'.$pageid;
	if ($postid) {
		$url.= '/#p'.$postid;
	} else {
		$url = trailingslashit($url);
	}
	return esc_url($url);
}

# --------------------------------------------------------------
# sf_build_qurl()
#
# Main Query String URL building routine
# Must have at least one parameter of 'var=value' string
# Up to five can be passed in.
# --------------------------------------------------------------

function sf_build_qurl($param1, $param2='', $param3='', $param4='', $param5='')
{
	$url = rtrim(SFURL, '/');

	# first does it need the ?
	if(strpos($url, '?') === false)
	{
		$url .= '/?';
		$and = '';
	} else {
		$and = "&";
	}

	$url.= $and.$param1;
	$and = "&";
	if(!empty($param2)) $url.= $and.$param2;
	if(!empty($param3)) $url.= $and.$param3;
	if(!empty($param4)) $url.= $and.$param4;
	if(!empty($param5)) $url.= $and.$param5;

	return $url;
}

# ------------------------------------------------------------------
# sf_get_sfqurl()
#
# Build a forum query url ready for parameters
# ------------------------------------------------------------------
function sf_get_sfqurl($url)
{
	# if no ? then add one on the end
	$url = rtrim($url, '/');
	if(strpos($url, '?') === false)
	{
		$url .= '?';
	} else {
		$url .= '&amp;';
	}
	return $url;
}

# --------------------------------------------------------------
# sf_permalink_from_forumid()
#
# Returns permalink for forum from the forum id
# --------------------------------------------------------------

function sf_permalink_from_forumid($forumid)
{
	$url = '';
	if(!empty($forumid))
	{
		$url = sf_build_url(sf_get_forum_slug($forumid), '', 0, 0);
	}
	return $url;
}

# --------------------------------------------------------------
# sf_permalink_from_topicid()
#
# Returns permalink for topic from the topic id
# --------------------------------------------------------------

function sf_permalink_from_topicid($topicid)
{
	$url = '';
	if(!empty($topicid))
	{
		$forumid = sf_get_forum_from_topic($topicid);
		$url = sf_build_url(sf_get_forum_slug($forumid), sf_get_topic_slug($topicid), 0, 0);
	}
	return $url;
}

# --------------------------------------------------------------
# sf_permalink_from_forumid_and_topicid()
#
# Returns permalink for topic from both forum and topic ids
# --------------------------------------------------------------

function sf_permalink_from_forumid_and_topicid($forumid, $topicid)
{
	$url = '';
	if(!empty($topicid) && !empty($forumid))
	{
		$url = sf_build_url(sf_get_forum_slug($forumid), sf_get_topic_slug($topicid), 0, 0);
	}
	return $url;
}

# --------------------------------------------------------------
# sf_permalink_from_postid()
#
# Returns permalink for topic from only the post id
# --------------------------------------------------------------

function sf_permalink_from_postid($postid)
{
	$url = '';
	if(!empty($postid))
	{
		$slugs = sf_get_slugs_from_postid($postid);
		$url = sf_build_url($slugs->forum_slug, $slugs->topic_slug, 0, $postid, $slugs->post_index);
	}
	return $url;
}

# --------------------------------------------------------------
# sf_determine_page()
#
# Determines the correct page with a topic that the post
# will be displayed on based on current settings
#	$topicslug:		to look up topic id if needed
#	$postid:		the post to calculate page for
#	$postindex:		post sequence ig known
# --------------------------------------------------------------

function sf_determine_page($forumslug, $topicslug, $postid, $postindex)
{
	global $wpdb, $sfglobals;

	# establish paging count - can sometimes be out of scope so check
	$ppaged=$sfglobals['display']['posts']['perpage'];
	if(empty($ppaged) || $ppaged == 0)
	{
		$sfdisplay = sf_get_option('sfdisplay');
		$ppaged = $sfdisplay['posts']['perpage'];
		if(!$ppaged) $ppaged=20;
	}

	# establish topic sort order
	$order="ASC"; # default
	if($sfglobals['display']['posts']['sortdesc']) $order="DESC"; # global override

	$topicrecord = sf_get_topic_record_from_slug($topicslug);

	# If we do not have the postindex then we have to go and get it
	if($postindex == 0 || empty($postindex))
	{
		$postindex = $wpdb->get_var("SELECT post_index FROM ".SFPOSTS." WHERE post_id=".$postid);

		# In the remote possibility postindex is 0 or empty then...
		if($postindex == 0 || empty($postindex))
		{
			$forumrecord = sf_get_forum_record_from_slug($forumslug);
			sf_build_forum_index($forumrecord->forum_id);
			sf_build_post_index($topicrecord->topic_id, $topicslug);
			$postindex = $wpdb->get_var("SELECT post_index FROM ".SFPOSTS." WHERE post_id=".$postid);
		}
	}

	# Now we have what we need to do the math
	if($order == 'ASC')
	{
		$page = ($postindex/$ppaged);
		if(!is_int($page))
		{
			$page=intval(($page)+1);
		}
	} else {
		$page = ($topicrecord->post_count - $postindex);
		$page = ($page/$ppaged);
		$page=intval(($page)+1);
	}
	return $page;
}

function sf_add_get()
{
	global $wp_rewrite;

	if($wp_rewrite->using_permalinks())
	{
		return '?';
	} else {
		return '&amp;';
	}
}

?>