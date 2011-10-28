<?php
/*
Simple:Press
Forum RSS Feeds
$LastChangedDate: 2010-10-23 11:06:35 -0700 (Sat, 23 Oct 2010) $
$Rev: 4806 $
*/
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

sf_setup_RSS_includes();

global $wpdb, $current_user;

# check installed version is correct
if(sfg_get_system_status() != 'ok')
{
	$out.= '<img style="vertical-align:middle" src="'.SFRESOURCES.'information.png" alt="" />'."\n";
	$out.= '&nbsp;&nbsp;'.__("The forum is temporarily unavailable while being upgraded to a new version", "sforum");
	echo $out;
	return;
}

$rssopt = sf_get_option('sfrss');
$limit = $rssopt['sfrsscount'];
if(!isset($limit)) $limit = 15;

$feed = $_GET['xfeed'];

# are we doing feed keys?
if ($rssopt['sfrssfeedkey'])
{
    # get the user requesting feed
    $feedkey = sf_esc_str($_GET['feedkey']);
    $userid = $wpdb->get_var("SELECT user_id FROM ".SFMEMBERS." WHERE feedkey='".$feedkey."'");

    # get user permissions
    wp_set_current_user($userid);
}

# is it a search feed?
$match = '';
if (isset($_GET['search']))
{
    $search = $_GET['search'];
    $match = "(MATCH(".SFPOSTS.".post_content) AGAINST ('".esc_sql(like_escape($search))."' IN BOOLEAN MODE) OR MATCH(".SFTOPICS.".topic_name) AGAINST ('".esc_sql(like_escape($search))."' IN BOOLEAN MODE)) AND ";
}

# get the requested feed type
switch($feed)
{
	case 'group':
        sf_initialise_globals();

		# Get Data
		if(isset($_GET['group']))
		{
			$groupid = sf_esc_int($_GET['group']);
			if(sf_group_exists($groupid))
			{
				$posts = $wpdb->get_results(
						"SELECT ".SFPOSTS.".post_id, ".SFPOSTS.".topic_id, ".SFPOSTS.".forum_id, post_content, ".sf_zone_datetime('post_date').", ".SFPOSTS.".user_id,
						 guest_name, display_name, group_id
						 FROM (".SFPOSTS." LEFT JOIN ".SFMEMBERS." ON ".SFPOSTS.".user_id = ".SFMEMBERS.".user_id)
						 JOIN ".SFFORUMS." ON ".SFPOSTS.".forum_id = ".SFFORUMS.".forum_id
						 JOIN ".SFTOPICS." ON ".SFTOPICS.".topic_id = ".SFPOSTS.".topic_id
						 WHERE ".$match.SFFORUMS.".group_id=".$groupid." AND ".SFFORUMS.".forum_rss_private = 0 AND ".SFPOSTS.".post_status = 0
						 ORDER BY post_date DESC
						 LIMIT 0, ".$limit.";");

				# Define Channel Elements
				$grouprec = sf_get_group_record($groupid);
				$rssTitle=get_bloginfo('name').' - '.__("Group", "sforum").': '.sf_filter_title_display($grouprec->group_name);
				$rssLink=sf_build_qurl('group='.$groupid);
                if ($rssopt['sfrssfeedkey'] && isset($sfglobals['member']['feedkey']))
				    $atomLink=sf_build_qurl('group='.$groupid, 'xfeed=group', 'feedkey='.$sfglobals['member']['feedkey']);
                else
				    $atomLink=sf_build_qurl('group='.$groupid, 'xfeed=group');

				$rssDescription=get_bloginfo('description');
				$rssGenerator=__('Simple:Press Version ', "sforum").SFVERSION;

				$rssItem=array();

				if($posts)
				{
					foreach($posts as $post)
					{
						$thisforum = sf_get_forum_record($post->forum_id);
						if ($post->topic_id && $thisforum && (sf_can_view_forum($post->forum_id) || !$rssopt['sfrssfeedkey']))
						{
							# Define Item Elements
							$item = new stdClass;

							$poster = sf_filter_name_display($post->display_name);
							if(empty($poster)) $poster = sf_filter_name_display($post->guest_name);
							$topic=sf_get_topic_record($post->topic_id);

							$item->title=$poster.' '.__('on', "sforum").' '.sf_filter_title_display($topic->topic_name);
							$item->link=sf_build_url($thisforum->forum_slug, $topic->topic_slug, 0, $post->post_id);
							$item->pubDate=mysql2date('r', $post->post_date);
							$item->category=sf_filter_title_display($thisforum->forum_name);
							$text=sf_filter_rss_display($post->post_content);
							$item->description=sf_rss_excerpt($text);
							$item->guid=sf_build_url($thisforum->forum_slug, $topic->topic_slug, 0, $post->post_id);

							$rssItem[]=$item;
						}
					}
				}
			}
		}

		break;

	case 'topic':
		# Get Data
		if(isset($_GET['topic']))
		{
			$topicid = sf_get_topic_id(sf_esc_str($_GET['topic']));
			if($topicid)
			{
				$topic=sf_get_topic_record($topicid);
				sf_initialise_globals($topic->forum_id);
				$posts = $wpdb->get_results(
						"SELECT ".SFPOSTS.".post_id, post_content, ".sf_zone_datetime('post_date').", ".SFPOSTS.".user_id, guest_name, display_name, ".SFPOSTS.".forum_id
						 FROM (".SFPOSTS." LEFT JOIN ".SFMEMBERS." ON ".SFPOSTS.".user_id = ".SFMEMBERS.".user_id)
						 JOIN ".SFFORUMS." ON ".SFPOSTS.".forum_id = ".SFFORUMS.".forum_id
						 JOIN ".SFTOPICS." ON ".SFTOPICS.".topic_id = ".SFPOSTS.".topic_id
						 WHERE ".$match.SFPOSTS.".topic_id = ".$topicid." AND ".SFPOSTS.".post_status = 0 AND ".SFFORUMS.".forum_rss_private = 0
						 ORDER BY post_date DESC
						 LIMIT 0, ".$limit);
				$forumslug = sf_esc_str($_GET['forum']);

				# Define Channel Elements
				$rssTitle=get_bloginfo('name').' - '.__("Topic", "sforum").': '.sf_filter_title_display($topic->topic_name);
				$rssLink=sf_build_url($forumslug, $topic->topic_slug, 0, 0);
                if ($rssopt['sfrssfeedkey'] && isset($sfglobals['member']['feedkey']))
    				$atomLink=sf_build_qurl($forumslug, $topic->topic_slug, 'xfeed=topic', 'feedkey='.$sfglobals['member']['feedkey']);
                else
    				$atomLink=sf_build_qurl($forumslug, $topic->topic_slug, 'xfeed=topic');

				$rssDescription=get_bloginfo('description');
				$rssGenerator=__('Simple:Press Version ', "sforum").SFVERSION;

				$rssItem=array();

				if($posts)
				{
					foreach($posts as $post)
					{
						if($post->post_id && (sf_can_view_forum($post->forum_id) || !$rssopt['sfrssfeedkey']))
						{
    						# Define Item Elements
    						$item = new stdClass;

    						$poster = sf_filter_name_display($post->display_name);
    						if(empty($poster)) $poster = sf_filter_name_display($post->guest_name);

    						$item->title=$poster.' '.__('on', "sforum").' '.sf_filter_title_display($topic->topic_name);
    						$item->link=sf_build_url($forumslug, $topic->topic_slug, 0, $post->post_id);
    						$item->pubDate=mysql2date('r', $post->post_date);
    						$item->category=sf_get_forum_name($forumslug);
    						$text=sf_filter_rss_display($post->post_content);
    						$item->description=sf_rss_excerpt($text);
    						$item->guid=sf_build_url($forumslug, $topic->topic_slug, 0, $post->post_id);

    						$rssItem[]=$item;
                        }
					}
				}
			}
		}

		break;

	case 'forum':
		# Get Data
		if(isset($_GET['forum']))
		{
			$forumid = sf_get_forum_id(sf_esc_str($_GET['forum']));
			if($forumid)
			{
				sf_initialise_globals($forumid);
				$forum=sf_get_forum_record($forumid);
				if($forum == '') exit();
				$posts = $wpdb->get_results(
						"SELECT ".SFPOSTS.".post_id, ".SFPOSTS.".topic_id, ".SFPOSTS.".forum_id, post_content, ".sf_zone_datetime('post_date').", ".SFPOSTS.".user_id, guest_name, display_name, ".SFPOSTS.".forum_id
						 FROM (".SFPOSTS." LEFT JOIN ".SFMEMBERS." ON ".SFPOSTS.".user_id = ".SFMEMBERS.".user_id)
						 JOIN ".SFFORUMS." ON ".SFPOSTS.".forum_id = ".SFFORUMS.".forum_id
						 JOIN ".SFTOPICS." ON ".SFTOPICS.".topic_id = ".SFPOSTS.".topic_id
						 WHERE ".$match.SFPOSTS.".forum_id = ".$forumid." AND ".SFPOSTS.".post_status = 0 AND ".SFFORUMS.".forum_rss_private = 0
						 ORDER BY post_date DESC
						 LIMIT 0, ".$limit);

				# Define Channel Elements
				$rssTitle=get_bloginfo('name').' - '.__("Forum", "sforum").': '.sf_filter_title_display($forum->forum_name);
				$rssLink=sf_build_url($forum->forum_slug, '', 0, 0);
                if ($rssopt['sfrssfeedkey'] && isset($sfglobals['member']['feedkey']))
    				$atomLink=sf_build_qurl($forum->forum_slug, 'xfeed=forum', 'feedkey='.$sfglobals['member']['feedkey']);
                else
    				$atomLink=sf_build_qurl($forum->forum_slug, 'xfeed=forum');

				$rssDescription=get_bloginfo('description');
				$rssGenerator=__('Simple:Press Version ', "sforum").SFVERSION;

				$rssItem=array();

				if($posts)
				{
					foreach($posts as $post)
					{
						# Define Item Elements
						if($post->topic_id && (sf_can_view_forum($post->forum_id) || !$rssopt['sfrssfeedkey']))
						{
    						$item = new stdClass;

							$poster = sf_filter_name_display($post->display_name);
							if(empty($poster)) $poster = sf_filter_name_display($post->guest_name);
							$topic=sf_get_topic_record($post->topic_id);

							$item->title=$poster.' '.__('on', "sforum").' '.sf_filter_title_display($topic->topic_name);
							$item->link=sf_build_url($forum->forum_slug, $topic->topic_slug, 0, $post->post_id);
							$item->pubDate=mysql2date('r', $post->post_date);
							$item->category=sf_get_forum_name($forum->forum_slug);
							$text=sf_filter_rss_display($post->post_content);
							$item->description=sf_rss_excerpt($text);
							$item->guid=sf_build_url($forum->forum_slug, $topic->topic_slug, 0, $post->post_id);

							$rssItem[]=$item;
						}
					}
				}
			}
		}

		break;

	case 'all':
        sf_initialise_globals();

		# Get Data
   		$posts = $wpdb->get_results(
 				"SELECT ".SFPOSTS.".post_id, ".SFPOSTS.".topic_id, ".SFPOSTS.".forum_id, post_content, post_date, ".SFPOSTS.".user_id, guest_name, display_name
   				 FROM (".SFPOSTS." LEFT JOIN ".SFMEMBERS." ON ".SFPOSTS.".user_id = ".SFMEMBERS.".user_id)
   				 JOIN ".SFFORUMS." ON ".SFPOSTS.".forum_id = ".SFFORUMS.".forum_id
				 JOIN ".SFTOPICS." ON ".SFTOPICS.".topic_id = ".SFPOSTS.".topic_id
   				 WHERE ".$match.SFPOSTS.".post_status = 0 AND ".SFFORUMS.".forum_rss_private = 0
   				 ORDER BY post_date DESC
   				 LIMIT 0, ".$limit);

		# Define Channel Elements
		$rssTitle=get_bloginfo('name').' - '.__("All Forums", "sforum");
		$rssLink=SFURL;
        if ($rssopt['sfrssfeedkey'] && isset($sfglobals['member']['feedkey']))
    		$atomLink=sf_build_qurl('xfeed=all', 'feedkey='.$sfglobals['member']['feedkey']);
        else
    		$atomLink=sf_build_qurl('xfeed=all');

		$rssDescription=get_bloginfo('description');
		$rssGenerator=__('Simple:Press Version ', "sforum").SFVERSION;

		$rssItem=array();

		if($posts)
		{
			foreach($posts as $post)
			{
				$thisforum = sf_get_forum_record($post->forum_id);
				if($post->topic_id && $thisforum && (sf_can_view_forum($post->forum_id) || !$rssopt['sfrssfeedkey']))
				{
					# Define Item Elements
					$item = new stdClass;

					$poster = sf_filter_name_display($post->display_name);
					if(empty($poster)) $poster = sf_filter_name_display($post->guest_name);
					$topic=sf_get_topic_record($post->topic_id);

					$item->title=$poster.' '.__('on', "sforum").' '.sf_filter_title_display($topic->topic_name);
					$item->link=sf_build_url($thisforum->forum_slug, $topic->topic_slug, 0, $post->post_id);
					$item->pubDate=mysql2date('r', $post->post_date);
					$item->category=sf_get_forum_name($thisforum->forum_slug);
					$text=sf_filter_rss_display($post->post_content);
					$item->description=sf_rss_excerpt($text);
					$item->guid=sf_build_url($thisforum->forum_slug, $topic->topic_slug, 0, $post->post_id);

					$rssItem[]=$item;
				}
			}
		}

		break;
}

# Send headers and XML
header("HTTP/1.1 200 OK");
header('Content-Type: application/xml');
header("Cache-control: max-age=3600");
header("Expires: ".date('r', time()+3600));
header("Pragma: ");
echo'<?xml version="1.0" encoding="'.get_option('blog_charset').'"?>';
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
	<title><?php sf_rss_filter($rssTitle) ?></title>
	<link><?php sf_rss_filter($rssLink) ?></link>
	<description><![CDATA[<?php sf_rss_filter($rssDescription) ?>]]></description>
	<generator><?php sf_rss_filter($rssGenerator) ?></generator>
	<atom:link href="<?php sf_rss_filter($atomLink) ?>" rel="self" type="application/rss+xml" />
<?php
if($rssItem)
{
	foreach($rssItem as $item)
	{
?>
<item>
	<title><?php sf_rss_filter($item->title) ?></title>
	<link><?php sf_rss_filter($item->link) ?></link>
	<category><?php sf_rss_filter($item->category) ?></category>
	<guid isPermaLink="true"><?php sf_rss_filter($item->guid) ?></guid>
	<description><![CDATA[<?php sf_rss_filter($item->description) ?>]]></description>
	<pubDate><?php sf_rss_filter($item->pubDate) ?></pubDate>
</item>
<?php
	}
}
?>
</channel>
</rss>