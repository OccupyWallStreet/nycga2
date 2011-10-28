<?php
/*
Simple:Press
Template Tag(s) - Lists
$LastChangedDate: 2011-03-27 19:18:56 -0700 (Sun, 27 Mar 2011) $
$Rev: 5755 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

/* 	=====================================================================================

	sf_recent_posts_tag($limit, $forum, $user, $postdate, $listtags, $forumids)

	displays the most recent topics to have received a new post

	parameters:

		$limit			How many items to show in the list		number			5
		$forum			Show the Forum Title					true/false		false
		$user			Show the Users Name						true/false		true
		$postdate		Show date of posting					true/false		false
		$listtags		Wrap in <li> tags (li only)				true/false		true
		$forumids		comma delimited list of forum id's		optional		0
		$posttime		Show time of posting (reqs postdate)	true/false		false
		$avatar		    Show poster's avatar                	true/false		false
		$size 		    Size of avatar if showing       	    optional		25

 	===================================================================================*/

function sf_recent_posts_tag($limit=5, $forum=false, $user=true, $postdate=false, $listtags=true, $forumids=0, $posttime=false, $avatar=false, $size=25)
{
	global $wpdb, $current_user, $sfvars;

    $limit = sf_esc_int($limit);
    if (empty($limit)) return;

	sf_initialise_globals($sfvars['forumid']);

	$out = '';
	$forum_ids = '';

	# are we passing forum ID's?
	if ($forumids != 0)
    {
		$flist = explode(",", $forumids);
		foreach($flist as $thisforum)
		{
			if (sf_can_view_forum($thisforum))
			{
				$forum_ids[] = $thisforum;
			}
		}
	} else {
		# limit to viewable forums based on permissions
		if($current_user->forumadmin == false)
		{
			$allforums = sf_get_forum_memberships($current_user->ID);
			if ($allforums)
			{
				foreach ($allforums as $thisforum)
				{
					if (sf_can_view_forum($thisforum->forum_id))
					{
						$forum_ids[] = $thisforum->forum_id;
					}
				}
			} else {
				return '';
			}
		}
	}

	# get out if nothing to see
	if($current_user->forumadmin == false && empty($forum_ids)) return '';

	# create where clause based on forums that current user can view
	if ($forum_ids != '')
	{
		$where = ' AND '.SFPOSTS.".forum_id IN (" . implode(",", $forum_ids) . ") = 1 ";
	} else {
		$where = '';
	}

	$sfposts = $wpdb->get_results("SELECT DISTINCT topic_id
                                   FROM ".SFPOSTS."
                                   WHERE post_status = 0 ".$where."
                                   ORDER BY post_id DESC
                                   LIMIT ".$limit);

	if($sfposts)
	{
		foreach($sfposts as $sfpost)
		{
			$postdetails = sf_get_last_post_in_topic($sfpost->topic_id);
			$thisforum = sf_get_forum_record($postdetails->forum_id);
			$p=false;

			# Start contruction
			if($listtags) $out.="<li class='sftagli'>\n";

			if ($avatar)
    			{
            	if ($postdetails->user_id)
            	{
            		$icon = 'user';
            		if (sf_is_forum_admin($postdetails->user_id)) $icon='admin';
            	} else {
            		$icon = 'guest';
            	}
            	$out.= sf_render_avatar($icon, $postdetails->user_id, sf_filter_email_display($postdetails->user_email), sf_filter_email_display($postdetails->guestemail), false, $size);
            }

			$out.= sf_get_topic_url_newpost($thisforum->forum_slug, $sfpost->topic_id, $postdetails->post_id, $postdetails->post_index);

			if($forum)
			{
				if ($p == false) $out.="<p class='sftagp'>";
				$out.= __("posted in forum", "sforum").' '.sf_filter_title_display($thisforum->forum_name)."&nbsp;"."\n";
				$p=true;
			}

			if($user)
			{
				if($p == false) $out.="<p class='sftagp'>";
				$poster = sf_build_name_display($postdetails->user_id, sf_filter_name_display($postdetails->display_name));
				if(empty($poster)) $poster = sf_filter_name_display($postdetails->guest_name);
				$out.=__("by", "sforum").' '.$poster.' '."\n";
				$p=true;
			}

			if($postdate)
			{
				if($p == false) $out.="<p class='sftagp'>";
				$out.=__("on", "sforum").' '.sf_date('d', $postdetails->post_date)."\n";
				if ($posttime)
				{
					$out.=' '.__("at", "sforum").' '.sf_date('t', $postdetails->post_date)."\n";
				}
				$p=true;
			}

			if($p) $out.="</p>\n";

			if($listtags) $out.="</li>\n";
		}
	} else {
		if($listtags) $out.="<li class='sftagli'>\n";
		$out.='<p>'.__("No Topics to Display", "sforum").'</p>'."\n";
		if($listtags) $out.="</li>\n";
	}
	echo($out);
	return;
}

/* 	=====================================================================================

	sf_recent_posts_alt_tag($limit, $forum, $user, $postdate, $listtags, $forumids)

	displays the most recent topics to have received a new post in an alternate method

	parameters:

		$limit			How many items to show in the list		number			5
		$forum			Show the Forum Title					true/false		false
		$user			Show the Users Name						true/false		true
		$postdate		Show date of posting					true/false		false
		$listtags		Wrap in <li> tags (li only)				true/false		true
		$posttime		Show time of posting (reqs postdate)	true/false		false
		$avatar		    Show poster's avatar                	true/false		false
		$size 		    Size of avatar if showing       	    optional		25

 	===================================================================================*/

function sf_recent_posts_alt_tag($limit=5, $forum=false, $user=true, $postdate=false, $listtags=true, $posttime=false, $avatar=false, $size=25)
{
	global $wpdb, $current_user, $sfvars;

    $limit = sf_esc_int($limit);
    if (empty($limit)) return;

	sf_initialise_globals($sfvars['forumid']);

	$out = '';

	$where = " WHERE post_status = 0";

	# limit to viewable forums based on permissions
	if (!$current_user->forumadmin)
	{
		$allforums = sf_get_forum_memberships($current_user->ID);
		if ($allforums)
		{
			$forum_ids = '';
			foreach ($allforums as $thisforum)
			{
				if (sf_can_view_forum($thisforum->forum_id))
				{
					$forum_ids[] = $thisforum->forum_id;
				}
			}
		} else {
			return '';
		}

		# create where clause based on forums that current user can view
		if ($forum_ids != '')
		{
			$where .= " AND forum_id IN (" . implode(",", $forum_ids) . ") = 1";
		} else {
            return '';
		}
	}

	$sfposts = $wpdb->get_results("SELECT DISTINCT topic_id
                                   FROM ".SFPOSTS.
                                   $where." ORDER BY post_id DESC LIMIT ".$limit);
	if ($sfposts)
	{
		foreach ($sfposts as $sfpost)
		{
			$postdetails = sf_get_last_post_in_topic($sfpost->topic_id);
			$thisforum = sf_get_forum_record($postdetails->forum_id);
			$p = false;

			# Start contruction
			if ($listtags) $out.="<li class='sftagli'>\n";

			if ($avatar)
			{
            	if ($postdetails->user_id)
            	{
            		$icon = 'user';
            		if (sf_is_forum_admin($postdetails->user_id)) $icon='admin';
            	} else {
            		$icon = 'guest';
            	}
            	$out.= sf_render_avatar($icon, $postdetails->user_id, sf_filter_email_display($postdetails->user_email), sf_filter_email_display($postdetails->guestemail), false, $size);
            }

			$out .= '<a href="'.sf_build_url($thisforum->forum_slug, sf_get_topic_slug($sfpost->topic_id), 0, $postdetails->post_id, $postdetails->post_index).'">';

			$out.= sf_get_topic_name(sf_get_topic_slug($sfpost->topic_id));

			if ($forum)
			{
				$out.= ' '.__("posted in", "sforum").' '.sf_filter_title_display($thisforum->forum_name);
				$p = true;
			}

			if ($user)
			{
				$out.= ' '.__("by ", "sforum").' ';
				$poster = sf_build_name_display($postdetails->user_id, sf_filter_name_display($postdetails->display_name));
				if (empty($poster)) $poster = sf_filter_name_display($postdetails->guest_name);
				$out.= $poster;
				$p = true;
			}

			if ($postdate)
			{
				$out.= ' '.__("on", "sforum").' '.sf_date('d', $postdetails->post_date);
				if ($posttime)
				{
					$out.= ' '.__("at", "sforum").' '.sf_date('t', $postdetails->post_date)."\n";
				}
				$p = true;
			}

			$out.= '</a>';
			if ($listtags) $out.= "</li>\n";
		}
	} else {
		if ($listtags) $out.= "<li class='sftagli'>\n";
		$out.= __("No Topics to Display", "sforum")."\n";
		if ($listtags) $out.= "</li>\n";
	}
	echo $out;

	return;
}

/* 	=====================================================================================

	sf_latest_posts($limit)

	displays the most recent topics to have received a new post

	parameters:

		$limit			How many items to show in the list		number			5=default

 	===================================================================================*/

function sf_latest_posts($limit=5)
{
	global $wpdb, $current_user, $sfvars;

    $limit = sf_esc_int($limit);
    if (empty($limit)) return;

	sf_initialise_globals($sfvars['forumid']);

	$out = '';

	$where = " WHERE ".SFPOSTS.".post_status = 0";

	# limit to viewable forums based on permissions
	if (!$current_user->forumadmin)
	{
		$allforums = sf_get_forum_memberships($current_user->ID);
		if ($allforums)
		{
			$forum_ids = '';
			foreach ($allforums as $thisforum)
			{
				if (sf_can_view_forum($thisforum->forum_id))
				{
					$forum_ids[] = $thisforum->forum_id;
				}
			}
		} else {
			return '';
		}

		# create where clause based on forums that current user can view
		if ($forum_ids != '')
		{
			$where .= " AND forum_id IN (" . implode(",", $forum_ids) . ") = 1";
		} else {
            return '';
		}
	}

	$posts = $wpdb->get_results(
			"SELECT post_id, topic_id, forum_id, post_content, post_index, ".sf_zone_datetime('post_date').",
			 ".SFPOSTS.".user_id, guest_name, ".SFMEMBERS.".display_name FROM ".SFPOSTS."
			 LEFT JOIN ".SFMEMBERS." ON ".SFPOSTS.".user_id = ".SFMEMBERS.".user_id
			 ".$where."
			 ORDER BY post_date DESC
			 LIMIT ".$limit);

	$out.='<div class="sf-latest">';

	if ($posts) {
		foreach ($posts as $post)
		{
			$thisforum = sf_get_forum_record($post->forum_id);
			$poster = sf_build_name_display($post->user_id, sf_filter_name_display($post->display_name));
			if (empty($poster)) $poster = sf_filter_name_display($post->guest_name);
			$topic = sf_get_topic_record($post->topic_id);
			$out.='<div class="sf-latest-header">';
			$out.=$poster.__(' posted ', "sforum");
			$out.='<a href="'.sf_build_url($thisforum->forum_slug, $topic->topic_slug, 0, $post->post_id, $post->post_index).'">';
			$out.=sf_filter_title_display($topic->topic_name).'</a>';
			$out.=__(' in ', "sforum");
			$out.='<a href="'.sf_build_url($thisforum->forum_slug, '', 1, 0).'">'.sf_get_forum_name($thisforum->forum_slug).'</a>';
			$out.='<br />'.sf_date('d', $post->post_date);
			$out.='</div>';
			$out.='<div class="sf-latest-content">';
			$text=sf_filter_content_display($post->post_content);
			$text=sf_rss_excerpt($text);
			$out.=$text;
			$out.='</div>';
			$out.='<br />';
		}
	} else {
		$out.='<div class="sf-latest-header">';
		$out.='<p>'.__("No Topics to Display", "sforum").'</p>'."\n";
		$out.='</div>';
	}

	$out.='</div>';

	echo($out);
	return;
}

/* 	=====================================================================================

	sf_new_post_announce()

	displays the latest forum post in  the sidebar - updated every XX seconds

	parameters: None

	The option to use this tag MUST be turned on in the forum options

 	===================================================================================*/

function sf_new_post_announce()
{
	global $sfscript;

	if(sf_get_option('sfuseannounce'))
	{
        $url = SFHOMEURL."index.php?sf_ahah=announce";

		if(sf_get_option('sfannounceauto'))
		{
			$timer = (sf_get_option('sfannouncetime') * 1000);
			$sfscript =
				'<script type="text/javascript">
				sfjNewPostCheck("'.$url.'", "sfannounce", "'.$timer.'");
				</script>';

				add_action( 'wp_footer', 'sfjs_setup_announce' );
		}
		echo '<div id="sfannounce">';
		sf_new_post_announce_display();
		echo '</div>';
	}
	return;
}

function sfjs_setup_announce() {
	global $sfscript;
	echo $sfscript;
}

function sf_new_post_announce_display()
{
	global $wpdb, $current_user, $sfvars;

	sf_initialise_globals($sfvars['forumid']);

	$aslist = sf_get_option('sfannouncelist');
	$out = '';

	$sfposts = sf_get_users_new_post_list(sf_get_option('sfannouncecount'));

	if($sfposts)
	{
		$sfposts = sf_combined_new_posts_list($sfposts);
		if($aslist)
		{
			$out = '<ul><li>'.sf_filter_text_display(sf_get_option('sfannouncehead')).'<ul>';
		} else {
			$out = '<p>'.sf_filter_text_display(sf_get_option('sfannouncehead')).'<br /></p>';
			$out.= '<table id="sfannouncetable" cellpadding="4" cellspacing="0" border="0">';
		}
		foreach($sfposts as $sfpost)
		{
			# GET LAST POSTER DETAILS
			$last = sf_get_last_post_in_topic($sfpost['topic_id']);

			$poster = sf_build_name_display($last->user_id, sf_filter_name_display($last->display_name));
			if(empty($poster)) $poster = sf_filter_name_display($last->guest_name);

			if(!$aslist)
			{
				$out.= '<tr><td class="sfannounceicon" valign="top" align="left">';
				# DISPLAY TOPIC ENTRY
				$topicicon = 'announceold.png';
				if($current_user->member && $current_user->ID != $sfpost['user_id'])
				{
					if(sf_is_in_users_newposts($sfpost['topic_id'])) $topicicon = 'announcenew.png';
				} else {
					if(($current_user->lastvisit > 0) && ($current_user->lastvisit < $last->udate)) $topicicon = 'announcenew.png';
				}
				$out.= '<img src="'. SFRESOURCES . $topicicon. '" alt="" />'."\n";
			}

			if($aslist)
			{
				$out.= '<li>';
			} else {
				$out.='</td><td class="sfannounceentry" valign="top">';
			}
			$out.= '<a href="'.sf_build_url($sfpost['forum_slug'], $sfpost['topic_slug'], 0, $last->post_id, $last->post_index).'">'.sf_format_announce_tag($sfpost['forum_name'], $sfpost['topic_name'], $poster, $last->post_date).'</a>';

			if($aslist)
			{
				$out.= '</li>';
			} else {
				$out.='</td></tr>';
			}
		}
		if($aslist)
		{
			$out.= '</ul></li></ul>';
		} else {
			$out.='</table>';
		}
	}
	echo $out;
	return;
}

function sf_format_announce_tag($forumname, $topicname, $poster, $postdate)
{
	$text=sf_filter_text_display(sf_get_option('sfannouncetext'));

	$text = str_replace('%TOPICNAME%', $topicname, $text);
	$text = str_replace('%FORUMNAME%', $forumname, $text);
	$text = str_replace('%POSTER%', $poster, $text);
	$text = str_replace('%DATETIME%', sf_date('d', $postdate)." - ".sf_date('t', $postdate), $text);
	return $text;
}

/* 	=====================================================================================

	sf_hot_topics($limit, $days, $forum, $listtags, $forumids)

	displays online status of admins and moderators

	parameters:

		$limit			How many items to show in the list		number			5
		$days			Number of days to include				number			30
		$forum			Show the Forum Title					true/false		false
		$listtags		Wrap in <li> tags (li only)				true/false		true
		$forumids		comma delimited list of forum id's		optional		0

 	===================================================================================*/

function sf_hot_topics($limit=10, $days=30, $forum=true, $listtags=true, $forumids=0)
{
	global $wpdb, $current_user, $sfvars;

    $limit = sf_esc_int($limit);
    if (empty($limit)) return;

	sf_initialise_globals($sfvars['forumid']);

	$out = '';

	# are we passing forum ID's?
	$where = '';
	if($forumids != 0)
	{
		$flist = explode(",", $forumids);
		$x = 0;
		$where = ' AND (';
		for($x; $x<count($flist); $x++)
		{
			$where.= ' '.SFTOPICS.'.forum_id = '.$flist[$x];
			if ($x != count($flist)-1) $where.= " OR ";
		}
		$where.= ')';
	}

	# limit to viewable forums based on permissions
	if (!$current_user->forumadmin)
	{
		$allforums = sf_get_forum_memberships($current_user->ID);
		if ($allforums)
		{
			$forum_ids = '';
			foreach ($allforums as $thisforum)
			{
				if (sf_can_view_forum($thisforum->forum_id))
				{
					$forum_ids[] = $thisforum->forum_id;
				}
			}
		} else {
			return '';
		}

		# create where clause based on forums that current user can view
		if ($forum_ids != '')
		{
			$where .= " AND ".SFPOSTS.".forum_id IN (" . implode(",", $forum_ids) . ") =1 ";
		} else {
            return '';
		}
	}

	# get any posts that meeet date criteria
	$posts = $wpdb->get_results("
		SELECT ".SFPOSTS.".topic_id, DATEDIFF(CURDATE(), post_date) AS delta, ".SFPOSTS.".forum_id, forum_name, forum_slug, forum_slug, topic_name, topic_slug
		FROM ".SFPOSTS."
		JOIN ".SFTOPICS." ON ".SFTOPICS.".topic_id = ".SFPOSTS.".topic_id
		JOIN ".SFFORUMS." ON ".SFFORUMS.".forum_id = ".SFPOSTS.".forum_id
		WHERE DATE_SUB(CURDATE(),INTERVAL ".$days." DAY) <= post_date".$where);
	if ($posts)
	{
		# give each topic with posts a score - currently ln(cur date - post date) for each post
		$score = $forum_id = $forum_name = $forum_slug = $topic_slug = $topic_name = array();
		foreach ($posts as $post)
		{
			if ($post->delta != $days)
			{
				$score[$post->topic_id] = $score[$post->topic_id] + log($days - $post->delta);
				$forum_id[$post->topic_id] = $post->forum_id;
				$forum_name[$post->topic_id] = sf_filter_title_display($post->forum_name);
				$forum_slug[$post->topic_id] = $post->forum_slug;
				$topic_slug[$post->topic_id] = $post->topic_slug;
				$topic_name[$post->topic_id] = sf_filter_title_display($post->topic_name);
			}
		}
		# reverse sort the posts and limit to number to display
		arsort($score);
		$topics = array_slice($score, 0, $limit, true);

		# now output the popular topics
		foreach ($topics as $id => $topic)
		{
			$p = false;

			# Start contruction
			if ($listtags) $out.= "<li class='sftagli'>\n";
			$out.= sf_get_topic_url($forum_slug[$id], $topic_slug[$id], $topic_name[$id]);

			if ($forum)
			{
				$out.= "<p class='sftagp'>".__("posted in forum", "sforum").' '.$forum_name[$id]."&nbsp;"."\n";
				$p = true;
			}

			if ($p) $out.= "</p>\n";
			if ($listtags) $out.= "</li>\n";
		}

	} else {
		if ($listtags) $out.="<li class='sftagli'>\n";
		$out.='<p>'.__("No Topics to Display", "sforum").'</p>'."\n";
		if ($listtags) $out.="</li>\n";
	}

	echo $out;
	return;
}

/* 	=====================================================================================

	sf_author_posts($author_id, $showforum=true, $showdate=true)

	displays all the posts for the specified author id - forum visability rules apply

	parameters:

		$author_id			author to show the posts for
		$showforum			show the forum name							true/false
		$showdate			show the date of the latest post			true/false
		$limit				number of posts to return					0 (all)

 	===================================================================================*/

function sf_author_posts($author_id, $showforum=true, $showdate=true, $limit=0)
{
	global $wpdb, $current_user, $sfvars;

    $limit = sf_esc_int($limit);
    $author_id = sf_esc_int($author_id);
    if (empty($author_id)) return;

	sf_initialise_globals($sfvars['forumid']);

	$posts = 0;

	$out = '<div class="sf-authortopics">';

	if ($limit > 0)
	{
		$limit = 'LIMIT '.$limit;
	} else {
		$limit = '';
	}

	# limit to viewable forums based on permissions
	$where = ' WHERE user_id = '.$author_id.' ';
	if (!$current_user->forumadmin)
	{
		$allforums = sf_get_forum_memberships($current_user->ID);
		if ($allforums)
		{
			$forum_ids = '';
			foreach ($allforums as $thisforum)
			{
				if (sf_can_view_forum($thisforum->forum_id))
				{
					$forum_ids[] = $thisforum->forum_id;
				}
			}
		} else {
			return '';
		}

		# create where clause based on forums that current user can view
		if ($forum_ids != '')
		{
			$where .= " AND forum_id IN (" . implode(",", $forum_ids) . ") = 1 ";
		} else {
            return '';
		}
	}

	$sql = "SELECT DISTINCT post_id, forum_id, topic_id, post_date, post_index FROM ".SFPOSTS.$where."ORDER BY post_date DESC $limit";
	$sfposts = $wpdb->get_results($sql);

	if ($sfposts) {
		foreach ($sfposts as $sfpost)
		{
			$forum = $wpdb->get_row("SELECT forum_name, forum_slug FROM ".SFFORUMS." WHERE forum_id = $sfpost->forum_id");
			$posts = 1;
			if ($showforum)
			{
				$out .= '<div class="sf-authorforum">';
				$out .= sf_filter_title_display($forum->forum_name);
				$out .= '</div>';
			}

			$out .= '<div class="sf-authorlink">';
			$out .= sf_get_topic_url_newpost($forum->forum_slug, $sfpost->topic_id, $sfpost->post_id, $sfpost->post_index);
			$out .= '</div>';

			if ($showdate)
			{
				$out .= '<div class="sf-authordate">';
				$out .= sf_date('d', $sfpost->post_date);
				$out .= '</div>';
			}
		}
	}

	if (!$posts) {
		$out .= __('No posts by this author', 'sforum');
	}

	$out .= '</div>';
	echo $out;
	return;
}

/* 	=====================================================================================

	sf_highest_rated_posts($limit, $forum, $user, $postdate, $listtags, $forumids)

	displays the highest rated posts

	parameters:

		$limit			How many items to show in the list		number			10
		$forum			Show the Forum Title					true/false		false
		$user			Show the Users Name						true/false		true
		$postdate		Show date of posting					true/false		false
		$listtags		Wrap in <li> tags (li only)				true/false		true
		$forumids		comma delimited list of forum id's		optional		0

 	===================================================================================*/

function sf_highest_rated_posts($limit=10, $forum=true, $user=true, $postdate=true, $listtags=true, $forumids=0)
{
	global $wpdb, $current_user, $sfvars;

    $limit = sf_esc_int($limit);
    if (empty($limit)) return;

	sf_initialise_globals($sfvars['forumid']);

	$out = '';

	$postratings = sf_get_option('sfpostratings');
	if (!$postratings['sfpostratings'])
	{
		if ($listtags) $out.= "<li class='sftagli'>\n";
		$out.= __("Post Rating is not Enabled!", "sforum")."\n";
		if ($listtags) $out.= "</li>\n";
		return;
	}

	# are we passing forum ID's?
	if ($forumids == 0)
	{
		$where = '';
	} else {
		$flist = explode(",", $forumids);
		$where=' WHERE (';
		$x=0;
		for($x; $x<count($flist); $x++)
		{
			$where.= SFPOSTS.".forum_id = ".$flist[$x];
			if ($x != count($flist)-1)
                $where.= " OR ";
            else
                $where.= ")";
		}
	}

	# limit to viewable forums based on permissions
	if (!$current_user->forumadmin)
	{
		$allforums = sf_get_forum_memberships($current_user->ID);
		if ($allforums)
		{
			$forum_ids = '';
			foreach ($allforums as $thisforum)
			{
				if (sf_can_view_forum($thisforum->forum_id))
				{
					$forum_ids[] = $thisforum->forum_id;
				}
			}
		} else {
			return '';
		}

		# create where clause based on forums that current user can view
		if ($forum_ids != '')
		{
			if ($where == '')
			{
				$where = ' WHERE ';
			} else {
				$where.= ' AND ';
			}
			$where .= SFPOSTS.".forum_id IN (" . implode(",", $forum_ids) . ") = 1";
		} else {
            return '';
		}
	}

	# how to order
	if ($postratings['sfratingsstyle'] == 1)  # thumb up/down
	{
		$order = "ORDER BY ratings_sum DESC";
	} else {
		$order = "ORDER BY (ratings_sum / vote_count) DESC";
	}

	$sfposts = $wpdb->get_results(
			"SELECT ".SFPOSTRATINGS.".post_id, ratings_sum, vote_count, ".SFPOSTS.".topic_id, ".SFPOSTS.".forum_id, ".SFPOSTS.".user_id, post_date, post_index, topic_slug, topic_name, forum_slug, forum_name, display_name, guest_name
			FROM ".SFPOSTRATINGS."
			JOIN ".SFPOSTS." ON ".SFPOSTRATINGS.".post_id = ".SFPOSTS.".post_id
			JOIN ".SFTOPICS." ON ".SFPOSTS.".topic_id = ".SFTOPICS.".topic_id
			JOIN ".SFFORUMS." ON ".SFPOSTS.".forum_id = ".SFFORUMS.".forum_id
			LEFT JOIN ".SFMEMBERS." ON ".SFPOSTS.".user_id = ".SFMEMBERS.".user_id
			".$where."
			".$order."
			LIMIT ".$limit);

	if ($sfposts)
	{
		foreach ($sfposts as $sfpost)
		{
			# Start contruction
			if ($listtags) $out.= "<li class='sftagli'>\n";

			$out .= '<a href="'.sf_build_url($sfpost->forum_slug, $sfpost->topic_slug, 0, $sfpost->post_id, $sfpost->post_index).'">';

			$out.= sf_filter_title_display($sfpost->topic_name);
			if ($forum)
			{
				$out.= ' '.__("posted in", "sforum").' '.sf_filter_title_display($sfpost->forum_name);
				$p = true;
			}

			if ($user)
			{
				$out.= ' '.__("by", "sforum").' ';
				$poster = sf_build_name_display($sfpost->user_id, sf_filter_name_display($sfpost->display_name));
				if (empty($poster)) $poster = sf_filter_name_display($sfpost->guest_name);
				$out.= $poster;
				$p = true;
			}

			if ($postdate)
			{
				$out.= ' '.__("on", "sforum").sf_date('d', $sfpost->post_date);
				$p=true;
			}

			$out.='</a>';
			if ($listtags) $out.= "</li>\n";
		}
	} else {
		if ($listtags) $out.= "<li class='sftagli'>\n";
		$out.= __("No Rated Posts to Display", "sforum")."\n";
		if ($listtags) $out.= "</li>\n";
	}
	echo ($out);
	return;
}

/* 	=====================================================================================

	sf_most_rated_posts($limit, $forum, $user, $postdate, $listtags, $forumids)

	displays the highest rated posts

	parameters:

		$limit			How many items to show in the list		number			10
		$forum			Show the Forum Title					true/false		false
		$user			Show the Users Name						true/false		true
		$postdate		Show date of posting					true/false		false
		$listtags		Wrap in <li> tags (li only)				true/false		true
		$forumids		comma delimited list of forum id's		optional		0

 	===================================================================================*/

function sf_most_rated_posts($limit=10, $forum=true, $user=true, $postdate=true, $listtags=true, $forumids=0)
{
	global $wpdb, $current_user, $sfvars;

    $limit = sf_esc_int($limit);
    if (empty($limit)) return;

	sf_initialise_globals($sfvars['forumid']);

	$out = '';

	$postratings = sf_get_option('sfpostratings');
	if (!$postratings['sfpostratings'])
	{
		if ($listtags) $out.= "<li class='sftagli'>\n";
		$out.= __("Post Rating is not Enabled!", "sforum")."\n";
		if ($listtags) $out.= "</li>\n";
		return;
	}

	# are we passing forum ID's?
	if ($forumids == 0)
	{
		$where = '';
	} else {
		$flist = explode(",", $forumids);
		$where=' WHERE (';
		$x=0;
		for($x; $x<count($flist); $x++)
		{
			$where.= SFPOSTS.".forum_id = ".$flist[$x];
			if ($x != count($flist)-1)
                $where.= " OR ";
            else
                $where.= ")";
		}
	}

	# limit to viewable forums based on permissions
	if (!$current_user->forumadmin)
	{
		$allforums = sf_get_forum_memberships($current_user->ID);
		if ($allforums)
		{
			$forum_ids = '';
			foreach ($allforums as $thisforum)
			{
				if (sf_can_view_forum($thisforum->forum_id))
				{
					$forum_ids[] = $thisforum->forum_id;
				}
			}
		} else {
			return '';
		}

		# create where clause based on forums that current user can view
		if ($forum_ids != '')
		{
			if ($where == '')
			{
				$where = ' WHERE ';
			} else {
				$where.= ' AND ';
			}
			$where .= SFPOSTS.".forum_id IN (" . implode(",", $forum_ids) . ") = 1";
		} else {
            return '';
		}
	}

	$sfposts = $wpdb->get_results(
			"SELECT ".SFPOSTRATINGS.".post_id, ratings_sum, vote_count, ".SFPOSTS.".topic_id, ".SFPOSTS.".forum_id, ".SFPOSTS.".user_id, post_date, post_index, topic_slug, topic_name, forum_slug, forum_name, display_name, guest_name
			FROM ".SFPOSTRATINGS."
			JOIN ".SFPOSTS." ON ".SFPOSTRATINGS.".post_id = ".SFPOSTS.".post_id
			JOIN ".SFTOPICS." ON ".SFPOSTS.".topic_id = ".SFTOPICS.".topic_id
			JOIN ".SFFORUMS." ON ".SFPOSTS.".forum_id = ".SFFORUMS.".forum_id
			LEFT JOIN ".SFMEMBERS." ON ".SFPOSTS.".user_id = ".SFMEMBERS.".user_id
			".$where."
			ORDER BY vote_count DESC
			LIMIT ".$limit);

	if ($sfposts)
	{
		foreach ($sfposts as $sfpost)
		{
			if (sf_can_view_forum($sfpost->forum_id))
			{
				# Start contruction
				if ($listtags) $out.= "<li class='sftagli'>\n";

				$out .= '<a href="'.sf_build_url($sfpost->forum_slug, $sfpost->topic_slug, 0, $sfpost->post_id, $sfpost->post_index).'">';

				$out.= sf_filter_title_display($sfpost->topic_name);
				if ($forum)
				{
					$out.= ' '.__("posted in", "sforum").' '.sf_filter_title_display($sfpost->forum_name);
					$p = true;
				}

				if ($user)
				{
					$out.= ' '.__("by", "sforum").' ';
					$poster = sf_build_name_display($sfpost->user_id, sf_filter_name_display($sfpost->display_name));
					if (empty($poster)) $poster = sf_filter_name_display($sfpost->guest_name);
					$out.= $poster;
					$p = true;
				}

				if ($postdate)
				{
					$out.= ' '.__("on", "sforum").' '.sf_date('d', $sfpost->post_date);
					$p=true;
				}

				$out.='</a>';
				if ($listtags) $out.= "</li>\n";
			}
		}
	} else {
		if ($listtags) $out.= "<li class='sftagli'>\n";
		$out.= __("No Rated Posts to Display", "sforum")."\n";
		if ($listtags) $out.= "</li>\n";
	}
	echo ($out);
	return;
}

/* 	=====================================================================================

	sf_recent_posts_expanded($limit=5, $forumids=0)

	displays lastest posts in expanded, forum styled list

	parameters:

		$limit			How many items to show in the list		number			10
		$forumids		comma delimited list of forum id's		optional		0

 	===================================================================================*/

function sf_recent_posts_expanded($limit=10, $forumids=0)
{
    include_once( SF_PLUGIN_DIR . '/forum/sf-page-components.php' );

    global $wpdb, $sfvars;

    $limit = sf_esc_int($limit);
    if (empty($limit)) return;

    sf_initialise_globals($sfvars['forumid']);

    $out = '';

    # Are we passing forum IDs?
    if ($forumids == 0)
    {
        $where = '';
    }
    else
    {
        $flist = explode(',', $forumids);
        $where = ' WHERE ';
        $x = 0;
        for ($x;$x<count($flist);$x++)
        {
			$where.= SFPOSTS.".forum_id = ".$flist[$x];
			if ($x != count($flist)-1) $where.= " OR ";
        }
    }

    $sfposts = $wpdb->get_results('SELECT DISTINCT forum_id, topic_id FROM '.SFPOSTS.$where.' ORDER BY post_id DESC LIMIT '.$limit);
    if ($sfposts)
    {
        $sfposts = sf_combined_new_posts_list($sfposts);
    }

	sf_setup_forum_constants();
	$out.= '<link rel="stylesheet" type="text/css" href="'.SFSKINCSS.'" />';
    $out.= '<div id="sforum">';
    $out.= '<table class="sfforumtable">';

	if ($sfposts)
	{
	    # grab the complete first/last post stats
	    $newposts = array();
	    foreach ($sfposts as $sfpost)
	    {
	        $newposts[$sfpost['topic_id']] = $sfpost['post_index'];
	    }
	    $stats = sf_get_combined_topic_stats($newposts);

	    $out.= '<tr><th colspan="2">'.__("Forum/Topic", "sforum").'</th><th>'.__("Started", "sforum").'</th><th>'.__("Last Post", "sforum").'</th><th>'.__("Posts", "sforum").'</th>';
	    $out.= '</tr>';
	    foreach ($sfposts as $sfpost)
	    {
	        # Display topic entry
	        $out.= '<tr>';
	        $out.= '<td class="sficoncell '.$alt.'">';
	        $statkeys = array_keys($stats[$sfpost['topic_id']]);
	        $out.= '<img src="'.SFRESOURCES.'topic.png" alt="" />';
	        $out.= '</td>';

	        $out.= '<td><p>'.sf_filter_title_display($sfpost['forum_name']);
	        $out.= '<br /><br /><a href="'.sf_build_url($sfpost['forum_slug'], $sfpost['topic_slug'], 0, $stats[0]['post_id'], $sfpost['post_index']).'">'.sf_filter_title_display($sfpost['topic_name']).'</a></p>';

	        if ($sfglobals['display']['topics']['pagelinks'])
	        {
	            $out.= sf_render_inline_pagelinks($sfpost['forum_slug'], $sfpost['topic_slug'], $stats[0]['post_index']);
	        }
	        $out.= '</td>';

	        # Display first and last post links
	        $topicstats = $stats[$sfpost['topic_id']];
	        if (isset($topicstats[1]))
	        {
	            $out.= sf_render_first_last_post_cell($sfpost['forum_slug'], $sfpost['topic_slug'], $topicstats[1], $alt);
	            $x = $sfpost['post_index'];
	            $out.= sf_render_first_last_post_cell($sfpost['forum_slug'], $sfpost['topic_slug'], $topicstats[$x], $alt);
	        }

	        # Dislay post count
	        $out.= '<td class="sfcounts">'.$sfpost['post_index'].'</td>';
	        $out.= '</tr>';
	    }
	} else {
		$out.= '<tr><td><div class="sfmessagestrip">'.__("There are No Recent Posts", "sforum").'</div></td></tr>';
	}
    $out.= '</table></div>';

    echo $out;
}

/* 	=====================================================================================

	sf_quicklinks_tag($limit=10, $forum=true, $recent=true)

	displays the quicklinks dropdowns for forum list and recent topics list

	parameters:

		$limit			How many items to show in the list		number			10
		$forum			Show the Forum quicklinks list		    boolean         true
		$recent			Show the recent posts quicklinks list   boolean         true

 	===================================================================================*/

function sf_quicklinks_tag($limit=10, $forum=true, $recent=true)
{
	global $sfvars;

    $limit = sf_esc_int($limit);
    if (empty($limit)) return;

	include_once (SF_PLUGIN_DIR.'/forum/sf-page-components.php');

	sf_initialise_globals($sfvars['forumid']);

	$out = '<div id="sfql">';
	# QuickLinks
    if ($forum)
    {
        $out.= sf_render_forum_quicklinks();
    }

    if ($recent)
    {
    	$out.= '<div id="sfqlposts">';
    	$out.= sf_render_newpost_quicklinks($limit);
    	$out.= '</div>';
    }
	$out.= '</div>';

	echo $out;
	return;
}

?>