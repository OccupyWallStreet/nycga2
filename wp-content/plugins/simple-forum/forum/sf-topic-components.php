<?php
/*
Simple:Press
Topic Rendering Routines (Posts)
$LastChangedDate: 2011-04-28 20:08:44 -0700 (Thu, 28 Apr 2011) $
$Rev: 5994 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# = CONSTRUCT PAGED POST NAVIGATION TABLE =====
if(!function_exists('sf_compile_paged_posts')):
function sf_compile_paged_posts($forumslug, $topicslug, $topicid, $currentpage, $postcount)
{
	global $sfglobals;

	if(!isset($currentpage)) $currentpage = 1;

	$ppaged=$sfglobals['display']['posts']['perpage'];
	if(!$ppaged) $ppaged=20;

	$totalpages = ($postcount / $ppaged);
	if(!is_int($totalpages)) $totalpages = (intval($totalpages)+1);

	if((isset($_GET['xtp'])) && (sf_esc_int($_GET['xtp']) <> 1))
	{
		$xtp = '&amp;xtp='.sf_esc_int($_GET['xtp']);
	} else {
		$xtp = '';
	}

	$out= __("Page:", "sforum").'  ';
	$baseurl = '<a href="'.sf_build_url($forumslug, $topicslug, 1, 0);

	$out.= sf_pn_next($currentpage, '', $totalpages, $baseurl, $sfglobals['display']['posts']['numpagelinks']);
	if ($currentpage > 1)
	{
		$out.= '&nbsp;&nbsp;' . trailingslashit($baseurl. 'page-'.$currentpage) . '" class="current">'.$currentpage.'</a>'. '&nbsp;&nbsp;'."\n";
	} else {
		$out.= '&nbsp;&nbsp;' . $baseurl. '" class="current">'.$currentpage.'</a>'. '&nbsp;&nbsp;'."\n";	}
	$out.= sf_pn_previous($currentpage, '', $totalpages, $baseurl, $sfglobals['display']['posts']['numpagelinks']);

	return $out;
}
endif;

# = TOPIC POSTS PAGE NAV STRIP ================
if(!function_exists('sf_render_post_pagelinks')):
function sf_render_post_pagelinks($thispagelinks, $bottom, $topiclock, $subs, $tpage, $tpagecount)
{
	global $current_user, $sfglobals, $sfvars;

    $out = '';

	if ((!$bottom && $sfglobals['display']['pagelinks']['ptop']) || ($bottom && $sfglobals['display']['pagelinks']['pbottom']))
	{
		$out.= '<table class="sffooter"><tr>'."\n";
		$out.= '<td class="sfpagelinks">'.$thispagelinks."\n";

		if($bottom)
		{
			if($tpage < $tpagecount)
			{
				$out.= '<img src="'.SFRESOURCES.'separator.png" alt="" />';
				$out.= '<a href="'.sf_build_url($sfvars['forumslug'], $sfvars['topicslug'], $tpage+1, 0).'"><img src="'.SFRESOURCES.'next.png" alt="" title="'.esc_attr(__("Next Page", "sforum")).'" /></a>'."\n";
			}
			$out.= '</td>'."\n";
		}

		if(!$bottom)
		{
			if($tpage > 1)
			{
				$out.= '<img src="'.SFRESOURCES.'separator.png" alt="" />';
				$out.= '&nbsp;&nbsp;<a href="'.sf_build_url($sfvars['forumslug'], $sfvars['topicslug'], $tpage-1, 0).'"><img src="'.SFRESOURCES.'previous.png" alt="" title="'.esc_attr(__("Previous Page", "sforum")).'" /></a>'."\n";
			}
			$out.= '</td>'."\n";

			$out.= '<td class="sfadditemcell" style="padding-top:4px;">';

			if((!$topiclock) || ($current_user->forumadmin))
			{
				if (!$sfglobals['lockdown'])
				{
					if($current_user->offmember)
					{
						$sflogin = sf_get_option('sflogin');
						$out.= '<a class="sficon" href="'.esc_url($sflogin['sfloginurl'].'&amp;redirect_to='.urlencode($_SERVER['REQUEST_URI'])).'"><img class="sfalignright" src="'.SFRESOURCES.'login.png" alt="" title="'.esc_attr(__("Login", "sforum")).'"  />'.sf_render_icons("Login").'</a>'."\n";
					} else {
						if ($current_user->sfreply && $sfvars['displaymode'] == 'posts')
						{
							$out.= '<a class="sficon" onclick="sfjOpenEditor(\'sfpostform\','.$sfglobals['editor']['sfeditor'].',\'post\');"><img src="'.SFRESOURCES.'addpost.png" alt="" title="'.esc_attr(__("Reply to Post", "sforum")).'" />'.sf_render_icons("Reply to Post").'</a>'."\n";						}
						if($current_user->sfaddnew)
						{
							$url = sf_build_url($sfvars['forumslug'], '', 1, 0).sf_add_get().'new=topic';
							$out.= '<a class="sficon" href="'.$url.'"><img src="'.SFRESOURCES.'addtopic.png" alt="" title="'.esc_attr(__("Add a New Topic", "sforum")).'" />'.sf_render_icons("Add a New Topic").'</a>'."\n";
						}
					}
				}
			}
			$out.= '</td>'."\n";
		}

		if($bottom)
		{
			$out.='<td class="sfadditemcell">'."\n";
			$out.= sf_render_add_post($bottom, $topiclock);
			$out.= '</td>';
		}
		$out.= '</tr></table>'."\n";

		if(!$bottom)
		{
			$out.= sf_render_topic_icon_legend($subs, $sfvars['topicid'])."\n";
		}
	} else {
		# if bottom with no pagelinks but show add button then...
		if($bottom && $sfglobals['display']['pagelinks']['pbottom']==false)
		{
			if ($current_user->sfreply && $sfvars['displaymode'] == 'posts')
			{
				$out.= '<table class="sffooter"><tr>'."\n";
				$out.= sf_render_add_post($bottom, $topiclock);
				$out.= '</tr></table>'."\n";
			}
		}
	}

	if ($bottom)
	{
		$out.= sf_process_hook('sf_hook_post_topic_bottom_pagelinks', array($sfvars['forumid'], $sfvars['topicid']));
	} else {
		$out.= sf_process_hook('sf_hook_post_topic_top_pagelinks', array($sfvars['forumid'], $sfvars['topicid']));
	}

	return $out;
}
endif;


# = TOPICS POST PAGE ADD BUTTON ===============
if(!function_exists('sf_render_add_post')):
function sf_render_add_post($bottom, $topiclock)
{
	global $current_user, $sfglobals, $sfvars;

	$out = '';
	# Display Reply to Post if allowed
	if((!$topiclock) || ($current_user->forumadmin))
	{
		if (!$sfglobals['lockdown'])
		{
			if($current_user->offmember)
			{
				$sflogin = sf_get_option('sflogin');
				$out.= '<a class="sficon sfalignright" href="'.esc_url($sflogin['sfloginurl'].'&amp;redirect_to='.urlencode($_SERVER['REQUEST_URI'])).'"><img src="'.SFRESOURCES.'login.png" alt="" title="'.esc_attr(__("Login", "sforum")).'"  />'.sf_render_icons("Login").'</a>'."\n";
			} else {
				if ($current_user->sfreply && $sfvars['displaymode'] == 'posts')
				{
					$out.= '<a class="sficon sfalignright" onclick="sfjOpenEditor(\'sfpostform\','.$sfglobals['editor']['sfeditor'].',\'post\');"><img src="'.SFRESOURCES.'addpost.png" alt="" title="'.esc_attr(__("Reply to Post", "sforum")).'" />'.sf_render_icons("Reply to Post").'</a>'."\n";
				}
			}
		}
	}
	return $out;
}
endif;

# = TOPIC: SUBSCRIBED/WATCHING LEGEND ==========
if(!function_exists('sf_render_topic_icon_legend')):
function sf_render_topic_icon_legend($subs, $topicid)
{
	global $wpdb, $current_user;

	# Subscriptions
	if ($current_user->guest) return;

	$out = '';

	$subtext = '';
	if (!empty($subs))
	{
		$out.='<span class="sficonkey"><small>';

		$sublist = unserialize($subs);
		foreach( $sublist as $i)
		{
			if($i == $current_user->ID) $subtext = '<img src="'. SFRESOURCES .'small-subscribe.png" alt="" />&nbsp;&nbsp;'.esc_attr(__("You are subscribed to this topic", "sforum"));
		}
		if (empty($subtext))
		{
			if ($current_user->forumadmin) $subtext= '&nbsp;&nbsp;<img src="'. SFRESOURCES .'small-subscribe.png" alt="" />&nbsp;&nbsp;'.esc_attr(__("This topic has User Subscriptions", "sforum"));
		}
		$out.= $subtext.'</small></span>';
	}

	# Watching
	$watchtext = '';
	$list = $wpdb->get_var("SELECT topic_watches FROM ".SFTOPICS." WHERE topic_id=".$topicid);
	if (!empty($list))
	{
		$out.='<span class="sficonkey"><small>';
		$watchlist = unserialize($list);
		foreach ($watchlist as $watch)
		{
			if ($watch == $current_user->ID) $watchtext = '<img src="'. SFRESOURCES .'small-watch.png" alt="" />&nbsp;&nbsp;'.esc_attr(__("You are watching this topic", "sforum"));
		}
		if (empty($watchtext))
		{
			if ($current_user->forumadmin) $watchtext = '&nbsp;&nbsp;<img src="'. SFRESOURCES .'small-watch.png" alt="" />&nbsp;&nbsp;'.esc_attr(__("This topic has User Watches", "sforum"));
		}
		$out.= $watchtext.'</small></span>';

	}
	return $out;
}
endif;

# = COLUMN HEADER ROW =========================
if(!function_exists('sf_render_topic_column_header_row')):
function sf_render_topic_column_header_row()
{
	global $sfglobals;

	$out = '<tr>'."\n";
	if ($sfglobals['display']['posts']['userabove'])
	{
		$out .= '<th>'.__("Post", "sforum").'</th>'."\n";
	} else {
		$out .= '<th>'.__("User", "sforum").'</th><th>'.__("Post", "sforum").'</th>'."\n";
	}

	$out.= '</tr>'."\n";

	return $out;
}
endif;

# = RENDER USER DETAILS CELL ABOVE ============
if(!function_exists('sf_render_poster_details_above')):
function sf_render_poster_details_above($post, $posterstatus, $poster, $rank, $postcount, $alt, $cacheid)
{
	global $sfglobals, $sfavatarcache, $sfidentitycache, $current_user;

	$rank_class = apply_filters('sanitize_title', $rank);
	$sfavatars = sf_get_option('sfavatars');
	$size = $sfavatars['sfavatarsize'] + 25;

	# Inner poster details table
	$out = '<table  class="sfinnerusertable" cellpadding="5"><tr align="center">';
	$out.= '<td class="'.$alt.'" width="'.$size.'">';

	# get avatar from cache if created already
	if(array_key_exists($cacheid, $sfavatarcache))
	{
		$out.= $sfavatarcache[$cacheid];
	} else {
		$outcache = sf_render_avatar($posterstatus, $post['user_id'], sf_filter_email_display($post['user_email']), sf_filter_email_display($post['guest_email']));
		$sfavatarcache[$cacheid] = $outcache;
		$out.= $outcache;
	}

	$out.= '</td>'."\n";
	if($sfglobals['display']['posts']['usertype'])
	{
		$out.= '<td class="'.$alt.'" width="1">';
		$out.= '<div class="sfuserrank rank-'.esc_attr($rank_class).' sftype-'.$posterstatus.'">'.$rank.'</div>';
		$out.= '</td>';
	}

	$pid = 'guest';
	if ($post['user_id'])
	{
		$pid = $post['user_id'];
	}
	$out.= '<td class="sfusername sfuser-'.$pid.' rank-'.esc_attr($rank_class).' sftype-'.$posterstatus.' '.$alt.'"><p><strong>'.$poster.'</strong></p>';

	if($sfglobals['display']['posts']['location'])
	{
		$loc = get_user_meta($post['user_id'], 'location', true);
		if (!empty($loc)) $out.= '<p>'.$loc.'</p>';
	}

	if($sfglobals['display']['posts']['postcount'])
	{
		$out.= '<p>'.$postcount.'</p>';
	}

	if($sfglobals['display']['posts']['time'] || $sfglobals['display']['posts']['date'])
	{
		$out.= '<p>';
		if($current_user->member && $current_user->lastvisit > 0 && $current_user->lastvisit < $post['udate'])
		{
			$out.= '<img src="'.SFRESOURCES.'announcenew.png" alt="" />'."\n";
		}
		if($sfglobals['display']['posts']['time'])
		{
			$out.= sf_date('t', $post['post_date']).'  ';
		}
		if($sfglobals['display']['posts']['date'])
		{
			$out.= sf_date('d', $post['post_date']);
		}
		$out.= '</p>'."\n";
	}

	$out.= '</td>';

	# get online identities from cache if created already
    $out.= '<td class="'.$alt.' sfalignright">';
	if (!array_key_exists($cacheid, $sfidentitycache))
	{
        $sfidentitycache[$cacheid] = '';
        $start = false;

        $sftwitter = array();
    	$sftwitter = sf_get_option('sftwitter');
        $twitter = get_user_meta($post['user_id'], 'twitter', true);
    	if ($sftwitter['sftwitterfollow'] && $twitter)
    	{
            if (!$start)
            {
                $sfidentitycache[$cacheid] .= '<p class="sfuseridentity">';
                $start = true;
            }
    		$sfidentitycache[$cacheid] .= '<a href="http://twitter.com/'.$twitter.'"><img src="'.SFRESOURCES.'followme.png" alt="" title="'.__("Follow Me", "sforum").'" /></a>';
    	}

        $sfdisplay = array();
    	$sfdisplay = sf_get_option('sfdisplay');
        $facebook = get_user_meta($post['user_id'], 'facebook', true);
    	if ($sfdisplay['posts']['sffbconnect'] && $facebook)
    	{
            if (!$start)
            {
                $sfidentitycache[$cacheid] .= '<p class="sfuseridentity">';
                $start = true;
            }
    		$sfidentitycache[$cacheid] .= '<a href="http://facebook.com/'.$facebook.'"><img src="'.SFRESOURCES.'fbconnect.png" alt="" title="'.__("Connect With Me", "sforum").'" /></a>';
    	}

        $mysapce = get_user_meta($post['user_id'], 'myspace', true);
    	if ($sfdisplay['posts']['sfmyspace'] && $mysapce)
    	{
            if (!$start)
            {
                $sfidentitycache[$cacheid] .= '<p class="sfuseridentity">';
                $start = true;
            }
    		$sfidentitycache[$cacheid] .= '<a href="http://myspace.com/'.$mysapce.'"><img src="'.SFRESOURCES.'myspace.png" alt="" title="'.__("MySpace Page", "sforum").'" /></a>';
    	}

        $linkedin = get_user_meta($post['user_id'], 'linkedin', true);
    	if ($sfdisplay['posts']['sflinkedin'] && $linkedin)
    	{
            if (!$start)
            {
                $sfidentitycache[$cacheid] .= '<p class="sfuseridentity">';
                $start = true;
            }
    		$sfidentitycache[$cacheid] .= '<a href="http://linkedin.com/in/'.$linkedin.'"><img src="'.SFRESOURCES.'linkedin.png" alt="" title="'.__("LinkedIn Page", "sforum").'" /></a>';
    	}

        if ($start) $sfidentitycache[$cacheid] .= '</p>';
    }
	$out.= $sfidentitycache[$cacheid];
    $out.= '</td>';

    $out.= '</tr></table>';

	# check if we need nofollow or target on the identity icons
	$sffilters = sf_get_option('sffilters');
	if ($sffilters['sfnofollow']) {
		$out = sf_filter_save_nofollow($out);
	}
	if ($sffilters['sftarget']) {
		$out = sf_filter_save_target($out);
	}

	return $out;
}
endif;

# = RENDER USER DETAILS CELL SIDE =============
if(!function_exists('sf_render_poster_details_side')):
function sf_render_poster_details_side($post, $posterstatus, $poster, $rank, $postcount, $alt, $cacheid)
{
	global $sfglobals, $sfavatarcache, $sfidentitycache, $current_user;

	$rank_class = apply_filters('sanitize_title', $rank);

	# Inner poster details table
	$out = '<table class="sfinnerusertable" cellpadding="0" cellspacing="0" border="0"><tr align="center">';

	$out.= '<td class="sfposticonstrip '.$alt.'">'."\n";

	if($sfglobals['display']['posts']['time'] || $sfglobals['display']['posts']['date'])
	{
		$out.= '<p>';
		if($current_user->member && $current_user->lastvisit > 0 && $current_user->lastvisit < $post['udate'])
		{
			$out.= '<img src="'.SFRESOURCES.'announcenew.png" alt="" />'."\n";
		}
		if($sfglobals['display']['posts']['time'])
		{
			$out.= sf_date('t', $post['post_date']).'<br />';
		}
		if($sfglobals['display']['posts']['date'])
		{
			$out.= sf_date('d', $post['post_date']);
		}
		$out.= '</p>'."\n";
	}

	$out.= '</td></tr><tr>';

	$pid = 'guest';
	if ($post['user_id'])
	{
		$pid = $post['user_id'];
	}

	$out.= '<td class="sfusername sfuser-'.$pid.' rank-'.esc_attr($rank_class).' sftype-'.$posterstatus.' '.$alt.'"><p><br /><strong>'.$poster.'</strong></p>';
	$out.= '</td></tr>';
	$out.= '<tr><td class="'.$alt.'"></td></tr>';

	if($sfglobals['display']['posts']['location'])
	{
		$loc = get_user_meta($post['user_id'], 'location', true);
		if (!empty($loc)) $out.= '<tr align="center"><td class="'.$alt.'"><p>'.$loc.'</p></td></tr>';
	}

	$out.= '<tr align="center"><td class="'.$alt.'">';

	# get avatar from cache if created already
	if (!array_key_exists($cacheid, $sfavatarcache))
	{
		$sfavatarcache[$cacheid] = sf_render_avatar($posterstatus, $post['user_id'], sf_filter_email_display($post['user_email']), sf_filter_email_display($post['guest_email']));
	}
	$out.= $sfavatarcache[$cacheid];
    $out.= '</td></tr>'."\n";
	$out.= '<tr><td class="'.$alt.'"></td></tr>';

	if($sfglobals['display']['posts']['usertype'])
	{
		$out.= '<tr align="center"><td class="'.$alt.'">';
		$out.= '<div class="sfuserrank rank-'.esc_attr($rank_class).' sftype-'.$posterstatus.'">'.$rank.'</div>';
		$out.= '</td></tr>';
	}

	if($sfglobals['display']['posts']['postcount'])
	{
		$out.= '<tr align="center"><td class="'.$alt.'"><p>'.$postcount.'</p></td></tr>';
	}

	# get online identities from cache if created already
    $out.= '<tr align="center"><td class="'.$alt.'">';
	if (!array_key_exists($cacheid, $sfidentitycache))
	{
        $sfidentitycache[$cacheid] = '';
        $start = false;

        $sftwitter = array();
    	$sftwitter = sf_get_option('sftwitter');
        $twitter = get_user_meta($post['user_id'], 'twitter', true);
    	if ($sftwitter['sftwitterfollow'] && $twitter)
    	{
            if (!$start)
            {
                $sfidentitycache[$cacheid] .= '<p class="sfuseridentity">';
                $start = true;
            }
    		$sfidentitycache[$cacheid] .= '<a href="http://twitter.com/'.$twitter.'"><img src="'.SFRESOURCES.'followme.png" alt="" title="'.__("Follow Me", "sforum").'" /></a>';
    	}

        $sfdisplay = array();
    	$sfdisplay = sf_get_option('sfdisplay');
        $facebook = get_user_meta($post['user_id'], 'facebook', true);
    	if ($sfdisplay['posts']['sffbconnect'] && $facebook)
    	{
            if (!$start)
            {
                $sfidentitycache[$cacheid] .= '<p class="sfuseridentity">';
                $start = true;
            }
    		$sfidentitycache[$cacheid] .= '<a href="http://facebook.com/'.$facebook.'"><img src="'.SFRESOURCES.'fbconnect.png" alt="" title="'.__("Connect With Me", "sforum").'" /></a>';
    	}

        $mysapce = get_user_meta($post['user_id'], 'myspace', true);
    	if ($sfdisplay['posts']['sfmyspace'] && $mysapce)
    	{
            if (!$start)
            {
                $sfidentitycache[$cacheid] .= '<p class="sfuseridentity">';
                $start = true;
            }
    		$sfidentitycache[$cacheid] .= '<a href="http://myspace.com/'.$mysapce.'"><img src="'.SFRESOURCES.'myspace.png" alt="" title="'.__("MySpace Page", "sforum").'" /></a>';
    	}

        $linkedin = get_user_meta($post['user_id'], 'linkedin', true);
    	if ($sfdisplay['posts']['sflinkedin'] && $linkedin)
    	{
            if (!$start)
            {
                $sfidentitycache[$cacheid] .= '<p class="sfuseridentity">';
                $start = true;
            }
    		$sfidentitycache[$cacheid] .= '<a href="http://linkedin.com/in/'.$linkedin.'"><img src="'.SFRESOURCES.'linkedin.png" alt="" title="'.__("LinkedIn Page", "sforum").'" /></a>';
    	}

        if ($start) $sfidentitycache[$cacheid] .= '</p>';
    }
	$out.= $sfidentitycache[$cacheid];
    $out.= '</td></tr>';

	$out.= '</table>';

	# check if we need nofollow or target on the identity icons
	$sffilters = sf_get_option('sffilters');
	if ($sffilters['sfnofollow']) $out = sf_filter_save_nofollow($out);
	if ($sffilters['sftarget']) $out = sf_filter_save_target($out);

	return $out;
}
endif;

# = GET ONLINE STATUS =========================
if(!function_exists('sf_render_online_status')):
function sf_render_online_status($userid)
{
	global $wpdb, $current_user, $sfglobals;

	if(empty($userid)) return '';
	$status = sf_is_online($userid);
	$opts = sf_get_member_item($userid, 'user_options');
   	$sfmemberopts = sf_get_option('sfmemberopts');

    $hide = !$current_user->forumadmin && ((isset($sfmemberopts['sfhidestatus']) && $sfmemberopts['sfhidestatus']==true) && (isset($opts['hidestatus']) && $opts['hidestatus']==true));
	if ($status && !$hide)
	{
		return '<img class="sficon sfonlinestatus" src="'.SFRESOURCES.'online.png" alt="" title="'.esc_attr(__("Member is On-Line", "sforum")).'" />';
	} else {
		return '<img class="sficon sfonlinestatus" src="'.SFRESOURCES.'offline.png" alt="" title="'.esc_attr(__("Member is Off-Line", "sforum")).'" />';
	}
}
endif;

# = RENDER POST ICON STRIP ====================
if(!function_exists('sf_render_post_icon_strip')):
function sf_render_post_icon_strip($post, $posterstatus, $userid, $username, $currentguest, $currentmember, $displaypost, $topiclock, $lastpost, $alt, $admintools, $postratings)
{
	global $sfvars, $sfglobals, $current_user;

	$out = '';

	$out.= '<td class="sfposticonstrip '.$alt.'">';
	$out.= '<div class="sfposticoncontainer">';

	# Admin Post Tools
	if($sfglobals['admin']['sftools'] && $current_user->sftopicicons && !$sfglobals['lockdown'])
	{
		$out.= sf_render_post_tools_icon($sfvars['forumid'], $post['post_id'], $sfvars['page'], $post['post_index'], $username);
	}

	# profile and web link if setup and alowed
	if($current_user->sfprofiles && $posterstatus != 'guest')
	{
		$sfprofile=sf_get_option('sfprofile');

		# profile icon
		if($sfprofile['profilelink'] == 3)
		{
			$link = '<img class="sficon" src="'.SFRESOURCES.'user.png" alt="" title="'.esc_attr(__("view user profile", "sforum")).'" />';
			$out.= '<div class="sfposticon sfprofileicon">'.sf_attach_user_profilelink($post['user_id'], $link).'</div>'."\n";
		}

		# weblink icon
		if($sfprofile['weblink'] == 3)
		{
			$link = '<img class="sficon" src="'.SFRESOURCES.'weblink.png" alt="" title="'.esc_attr(__("visit users website", "sforum")).'" />';
			$out.= '<div class="sfposticon sfwebicon">'.sf_attach_user_weblink($post['user_id'], $link, false).'</div>'."\n";
		}
	}

	if($sfglobals['display']['posts']['online'])
	{
		$out.= '<div class="sfposticon sfonlineicon">'.sf_render_online_status($post['user_id']).'</div>';
	}

	# Do we show Quote and/or Edit icons?
	if((($currentmember) || ($currentguest)) || ($displaypost))
	{
		# Quote Icon
		if ($displaypost && !$sfglobals['lockdown'])
		{
			if($current_user->sfreply)
			{
				if(!$topiclock) $out.= '<div class="sfposticon sfquoteicon">'.sf_render_post_user_quoteicon($post['post_id'], $username, $post['user_id']).'</div>';
			}

			# Report Post
			if($current_user->sfreport)
			{
				$out.= '<div class="sfposticon sfreporticon">'.sf_render_report_post_icon($post['post_id'], $username, $post['user_id']).'</div>';
			}
		}

		# Edit Icon
		if (($currentmember || $currentguest) && !$sfglobals['lockdown'])
		{
			if((($current_user->sfstopedit) && ($lastpost)) || ($current_user->sfeditall))
			{
				$out.= '<div class="sfposticon sfediticon">'.sf_render_post_user_editicon($sfvars['forumslug'], $sfvars['topicslug'], $sfvars['page'], $post['post_id'], $post['post_index']).'</div>';
			}
		}

		# Send PM Icon
		if(($posterstatus != 'guest') && ($current_user->sfusepm))
		{
			if($userid != $post['user_id'] && $post['pm'])
			{
				$out.= '<div class="sfposticon sfpmicon">'.sf_render_post_user_sendpmicon($userid, $post['user_id'], $username, $post['post_id']).'</div>';
			}
		}
	}

	# Is this post pinned?
	if($post['post_pinned'] == 1)
	{
		$out.= '<div class="sfposticon sfpostpinicon"><img class="sficon" src="'.SFRESOURCES.'pin.png" alt="" title="'.esc_attr(__("post pinned", "sforum")).'" /></div>'."\n";
	}

	if($sfglobals['display']['posts']['permalink'])
	{
		$pptitle = esc_js(__("Post Permalink", "sforum"));
        $purl = sf_build_url($sfvars['forumslug'], $sfvars['topicslug'], $sfvars['page'], $post['post_id'], $post['post_index']);
		$out.='<div class="sfposticon sfpermalinkicon"><a href="'.$purl.'" id="sfshowlink'.$post['post_id'].'" onclick="sfjshowPostLink(\''.$pptitle.'\',\''.addslashes($purl).'\',\''.$post['post_id'].'\');return false;"><img src="'.SFRESOURCES.'link.png" alt="" title="'.esc_attr(__("permalink to this post", "sforum")).'" /></a></div>'."\n";
		$out.='<div class="highslide-html-content" id="link-content'.$post['post_id'].'" style="width: 300px">';
		$out.='<div class="inline-edit" id="sfpostlink'.$post['post_id'].'"></div>';
		$out.='<input type="button" class="sfcontrol" id="sfclosevalid'.$post['post_index'].'" onclick="return hs.close(this)" value="'.esc_attr(__("Close", "sforum")).'" />';
		$out.='</div>';
	}

	if($sfglobals['display']['posts']['print'])
	{
		$out.= '<div class="sfposticon sfprinticon">'.sf_render_post_printicon($post['post_id']).'</div>';
	}

	$out.= '<div class="sftoplink">';
	$out.= '<a href="#forumtop"><img src="'.SFRESOURCES.'top.png" alt="" title="'.esc_attr(__("go to top", "sforum")).'" /></a></div>'."\n";
	$out.= '<div class="sfposticon sfpostNumberOnPage">'.$post['post_index'].'</div>';

	if ($postratings)
	{
		$out.= sf_render_post_ratings($post);
	}

	$out.= '</div>';

	# Close the inner table cell
	$out.= '</td>';
	return $out;
}
endif;

# = RENDER POST CONTENT =======================
if(!function_exists('sf_render_post_content')):
function sf_render_post_content($post, $editmode, $displaypost, $approve_text, $currentguest, $currentmember, $bloglink)
{
	global $sfvars, $sfglobals, $current_user;

	$out = '';
	if($editmode)
	{
		$postcontent = sf_filter_content_edit($post['post_content']);

		$linked=false;
		if($bloglink && $post['post_index']==1) $linked=true;
		$out.= sf_edit_post($post['post_id'], $postcontent, $sfvars['forumid'], $sfvars['topicid'], $sfvars['page'], $post['post_edit'], $linked, $bloglink);
	} else {
		$out.= '<div id="post'.$post['post_id'].'"';

		if($post['post_pinned'] == 1)
		{
			$out.=' class="sfpinned">'."\n";
		} else {
			$out.='>'."\n";
		}

		# display any post edit data
		if(!empty($post['post_edit']) && $sfglobals['display']['posts']['showedits'])
		{
			$postedit = unserialize($post['post_edit']);
			$out.= '<p><small><i>';
			$x = 0;
			$showlast = $sfglobals['display']['posts']['showlastedit'];
			$lastedit = (count($postedit)-1);

			$zone = $current_user->timezone;

			foreach($postedit as $edit)
			{
				if (($showlast && ($x==$lastedit)) || ($showlast == false))
				{
					$ts = $edit['at'];
					if($zone < 0) $ts = ($ts-(abs($zone)*3600));
					if($zone > 0) $ts = ($ts+(abs($zone)*3600));
					$out.= sprintf(__("Post edited %s by %s", "sforum"), date_i18n(SFTIMES, $ts).' - '.date_i18n(SFDATES, $ts), $edit['by']).'<br />';
				}
				$x++;
			}
			$out.= '</i></small></p><hr />';
		}

		if (!sf_is_forum_admin($post['user_id']) || $current_user->sfadminview)
		{
			if($displaypost)
			{
				$out.= $approve_text.sf_filter_content_display($post['post_content']);
			} else {
				$out.= sf_filter_content_display($approve_text)."\n";

				if(($currentguest) || ($currentmember))
				{
					$out.= sf_filter_content_display($post['post_content']);
				}
			}
		} else {
			$adminview = sf_get_sfmeta('adminview', 'message');
			if ($adminview)
			{
				$out.= '<div class="sfmessagestrip">';
				$out.= sf_filter_text_display($adminview[0]['meta_value']);
				$out.= '</div>'."\n";
			}
		}

		if(function_exists('sf_forum_show_blog_link'))
		{
			if($bloglink > 0)
			{
				$out.= '<br /><p>'.sf_forum_show_blog_link($bloglink).'</p>';
			}
		}
		$out.= '</div>';
	}
	return $out;
}
endif;

# = RENDER SIGNATURE STRIP ====================
if(!function_exists('sf_render_signature_strip')):
function sf_render_signature_strip($sig)
{
	# force sig to have no follow in links
	$sig = sf_filter_save_nofollow($sig);

	# force sig images to be limited in size
	$sig = sf_filter_signature_display($sig);

	$out = '<div class="sfsignaturestrip">'."\n";
	$out.= $sig;
	$out.= '</div>'."\n";
	return $out;
}
endif;

# = RENDER TOPIC STATUS UPDATER ===============
if(!function_exists('sf_render_topic_status_updater')):
function sf_render_topic_status_updater($statusset, $statusflag, $userid)
{
	global $current_user;

	$out='';
	if($current_user->moderator || $current_user->sftopicstatus || $current_user->ID == $userid)
	{
		$out.= sf_render_topic_statusflag($statusset, $statusflag, 'ts-topic', 'ts-upinline', 'right');
		if($statusflag != 0)
		{
			$out.= '<div class="sfts-status sfalignright"><label><small>'.__("Change Topic Status", "sforum").':  '.sf_topic_status_select($statusset, $statusflag, true).'</small></label></div>';
		} else {
			$out.= '<div class="sfts-status sfalignright"><label><small>'.__("Assign Topic Status", "sforum").':  '.sf_topic_status_select($statusset, $statusflag, true).'</small></label></div>';
		}
		$out.= '<div class="sfclear"></div>';
	}
	return $out;
}
endif;

# = RENDER QUOTE ICON =========================
if(!function_exists('sf_render_post_user_quoteicon')):
function sf_render_post_user_quoteicon($postid, $username, $userid)
{
	global $current_user, $sfglobals, $sfvars;

	$out = '';

	if (sf_is_forum_admin($userid) && $current_user->sfadminview == false) return $out;

	if($current_user->sfreply && $current_user->offmember == false)
	{
        $quoteUrl = SFHOMEURL."index.php?sf_ahah=quote";
		$intro = $username.' '.__("said:", "sforum").' ';
		$out = '<a rel="nofollow" class="sficon" onclick="sfjquotePost('.$postid.', \''.esc_js($intro).'\', '.$sfglobals['editor']['sfeditor'].', '.$sfvars['forumid'].',\''.$quoteUrl.'\');"><img src="'.SFRESOURCES.'quote.png" alt="" title="'.esc_attr(__("Quote and Reply", "sforum")).'" />&nbsp;'.sf_render_icons("Quote and Reply").'</a>'."\n";
	}
	return $out;
}
endif;

# = RENDER REPORT POST ICON ===================
if(!function_exists('sf_render_report_post_icon')):
function sf_render_report_post_icon($postid, $author, $userid)
{
	global $current_user;

	$out = '';

	if (sf_is_forum_admin($userid) && $current_user->sfadminview == false) return $out;

	$returnurl=SFURL;

	$out.= '<form class="sfhiddenform" action="'.SFURL.'" method="post" name="report'.$postid.'">'."\n";
	$out.= '<input type="hidden" class="sfhiddeninput" name="rpaction" value="report" />'."\n";
	$out.= '<input type="hidden" class="sfhiddeninput" name="rpurl" value="'.esc_attr($returnurl).'" />'."\n";
	$out.= '<input type="hidden" class="sfhiddeninput" name="rpuser" value="'.$current_user->ID.'" />'."\n";
	$out.= '<input type="hidden" class="sfhiddeninput" name="rppost" value="'.$postid.'" />'."\n";
	$out.= '<input type="hidden" class="sfhiddeninput" name="rpposter" value="'.esc_attr($author).'" />'."\n";
	$out.= '<a rel="nofollow" class="sficon" href="javascript:document.report'.$postid.'.submit();"><img src="'.SFRESOURCES.'reportpost.png" alt="" title="'.esc_attr(__("Report Post", "sforum")).'" />&nbsp;'.sf_render_icons("Report Post").'</a>'."\n";
	$out.= '</form>'."\n";

	return $out;
}
endif;

# = RENDER SEND PM ICON =======================
if(!function_exists('sf_render_post_user_sendpmicon')):
function sf_render_post_user_sendpmicon($from_user, $to_user, $recipient, $postid)
{
	global $current_user, $wpdb;

	$returnurl=SFURL;

	update_sfsetting($current_user->ID.'@pmurl', $returnurl);
	$user = $wpdb->get_var("SELECT user_login FROM ".SFUSERS." WHERE ID=".$to_user);
	$url = SFURL.'private-messaging/send/'.urlencode($user).'/';
	$out = '<a class="sficon" href="'.$url.'"><img src="'.SFRESOURCES.'sendpm.png" alt="" title="'.esc_attr(__("Send PM", "sforum")).'" />&nbsp;'.sf_render_icons("Send PM").'</a>'."\n";

	return $out;
}
endif;

# = RENDER EDIT ICON ==========================
if(!function_exists('sf_render_post_user_editicon')):
function sf_render_post_user_editicon($forumslug, $topicslug, $pageid, $postid, $postindex)
{
	$out='';
	$out.= '<form class="sfhiddenform" action="'.sf_build_url($forumslug, $topicslug, $pageid, $postid, $postindex).'" method="post" name="usereditpost'.$postid.'">'."\n";
	$out.= '<input type="hidden" class="sfhiddeninput" name="useredit" value="'.$postid.'" />'."\n";
	$out.= '<a class="sficon" href="javascript:document.usereditpost'.$postid.'.submit();"><img src="'.SFRESOURCES.'useredit.png" alt="" title="'.esc_attr(__("Edit Your Post", "sforum")).'" />&nbsp;'.sf_render_icons("Edit Your Post").'</a>'."\n";
	$out.= '</form>'."\n";
	return $out;
}
endif;

# = RENDER POST RATINGS =======================
if(!function_exists('sf_render_post_ratings')):
function sf_render_post_ratings($post)
{
	global $current_user, $sfvars;

	$out = '';
	$postid = $post['post_id'];
	$postratings = sf_get_option('sfpostratings');
	if ($postratings['sfpostratings'])
	{
		$out.= '<div id="sfpostrating-'.$postid.'">';

		if (!isset($post['rating_id']))
		{
			$ratings = 0;
			$votes = 0;
			$voted = false;
		} else {
			$ratings = $post['ratings_sum'];
			$votes = $post['vote_count'];
			if ($current_user->member)
			{
				$members = unserialize($post['members']);
				if ($members)
				{
					$voted = array_search($current_user->ID, $members);
				} else {
					$voted = -1;
				}
			} else {
				$ips = unserialize($post['ips']);
				if ($ips)
				{
					$voted = array_search(getenv("REMOTE_ADDR"), $ips);
				} else {
					$voted = -1;
				}
			}
		}
		if ($postratings['sfratingsstyle'] == 1)  # thumb up/down
		{
			$out.= '<div class="sfpostratingscontainer sfthumbs">';
            $site = SFHOMEURL."index.php?sf_ahah=postrating&fid=".$sfvars['forumid']."&amp;pid=".$postid."&amp;rate=down";
			$downlink = 'style="cursor: pointer;" onclick="javascript:sfjRatePost(\''.$postid.'\', \''.$site.'\');" ';
            $site = SFHOMEURL."index.php?sf_ahah=postrating&fid=".$sfvars['forumid']."&amp;pid=".$postid."&amp;rate=up";
			$uplink = 'style="cursor: pointer;" onclick="javascript:sfjRatePost(\''.$postid.'\', \''.$site.'\');" ';
			$downimg = SFRESOURCES.'ratings/ratedown.png';
			$upimg = SFRESOURCES.'ratings/rateup.png';
			$uptext = __("Rate Post Up", "sforum");
			$downtext = __("Rate Post Down", "sforum");
			if (is_numeric($voted) || !$current_user->sfrateposts)
			{
				$downlink = '';
				$uplink = '';
				$downimg = SFRESOURCES.'ratings/ratedowngrey.png';
				$upimg = SFRESOURCES.'ratings/rateupgrey.png';
				$uptext = __("Post Rating: ", "sforum").$ratings;
				$downtext = $uptext;
			}

            if ($ratings > 0) $ratings = '+'.$ratings;
			$out.= '<div class="sfposticon sfpostrating">'.$ratings.'</div>';
   			$out.= '<div class="sfposticon sfpostratedown"><img src="'.$downimg.'" alt="" title="'.esc_attr($downtext).'" '.$downlink.'/></div>';
   			$out.= '<div class="sfposticon sfpostrateup"><img src="'.$upimg.'" alt="" title="'.esc_attr($uptext).'" '.$uplink.'/></div>';
		} else {
			$out.= '<div class="sfpostratingscontainer sfstars">';
			$offimg = SFRESOURCES.'ratings/ratestaroff.png';
			$onimg = SFRESOURCES.'ratings/ratestaron.png';
			$overimg = SFRESOURCES.'ratings/ratestarover.png';
			if ($votes)
			{
				$star_rating = round($ratings / $votes, 1);
			} else {
				$star_rating = 0;
			}
			$intrating = floor($star_rating);
			$out.= '<div class="sfposticon sfpostrating">'.$star_rating.'</div>';
			$out.= '<div class="sfposticon sfpoststars">';
		    for ($x = 1; $x <= $intrating; $x++)
			{
				$name = ' id="star-'.$postid.'-'.$x.'"';
				if (is_numeric($voted) || !$current_user->sfrateposts)
				{
					$link = '';
					$text = __("Post Rating: ", "sforum").$star_rating;
				} else {
					if ($x == 1) $text = __("Rate Post 1 Star", "sforum");
					if ($x == 2) $text = __("Rate Post 2 Stars", "sforum");
					if ($x == 3) $text = __("Rate Post 3 Stars", "sforum");
					if ($x == 4) $text = __("Rate Post 4 Stars", "sforum");
					if ($x == 5) $text = __("Rate Post 5 Stars", "sforum");
                    $site = SFHOMEURL."index.php?sf_ahah=postrating&fid=".$sfvars['forumid']."&amp;pid=".$postid."&amp;rate=".$x;
  					$link = 'style="cursor: pointer;" onclick="javascript:sfjRatePost(\''.$postid.'\', \''.$site.'\');" onmouseover="sfjstarhover(\''.$postid.'\', \''.$x.'\', \''.$overimg.'\')" onmouseout="sfjstarunhover(\''.$postid.'\', \''.$intrating.'\', \''.$onimg.'\', \''.$offimg.'\')" ';
				}
				$out.= '<img'.$name.' src="'.$onimg.'" alt="" title="'.esc_attr($text).'" '.$link.'/>';
			}

		    for ($x = ($intrating+1); $x <= 5; $x++)
			{
				$name = ' id="star-'.$postid.'-'.$x.'"';
				if (is_numeric($voted)  || !$current_user->sfrateposts)
				{
					$link = '';
					$text = __("Post Rating: ", "sforum").$star_rating;
				} else {
					if ($x == 1) $text = __("Rate Post 1 Star", "sforum");
					if ($x == 2) $text = __("Rate Post 2 Stars", "sforum");
					if ($x == 3) $text = __("Rate Post 3 Stars", "sforum");
					if ($x == 4) $text = __("Rate Post 4 Stars", "sforum");
					if ($x == 5) $text = __("Rate Post 5 Stars", "sforum");
                    $site = SFHOMEURL."index.php?sf_ahah=postrating&fid=".$sfvars['forumid']."&amp;pid=".$postid."&amp;rate=".$x;
					$link = 'style="cursor: pointer;" onclick="javascript:sfjRatePost(\''.$postid.'\', \''.$site.'\');" onmouseover="sfjstarhover(\''.$postid.'\', \''.$x.'\', \''.$overimg.'\')" onmouseout="sfjstarunhover(\''.$postid.'\', \''.$intrating.'\', \''.$onimg.'\', \''.$offimg.'\')" ';
				}
				$out.= '<img'.$name.' src="'.$offimg.'" alt="" title="'.esc_attr($text).'" '.$link.'/>';
			}
			$out.= '</div>';
		}
		$out.= '</div>';
		$out.= '</div>';
	}
	return $out;
}
endif;

function sf_render_post_printicon($postid)
{
	$out = '<a class="sficon" href="javascript:void(0);" onclick="javscript:jQuery(\'#post'.$postid.'\').jqprint({debug:true}); return false;"><img src="'.SFRESOURCES.'print.png" alt="" title="'.esc_attr(__("Print This Post", "sforum")).'"/>'.sf_render_icons("Print this Post").'</a>';

	return $out;
}

function sf_render_post_tools_icon($forumid, $postid, $page, $postnum, $displayname)
{
    $site = SFHOMEURL."index.php?sf_ahah=adminlinks&action=posttools&post=".$postid."&page=".$page."&postnum=".$postnum."&name=".urlencode($displayname)."&forum=".$forumid;
	$out ='<div class="sfposticon sfalignleft sfposttoolsicon" style="padding-top:2px"><a href="'.esc_attr($site).'" onclick="return hs.htmlExpand(this, { objectType: \'ajax\', preserveContent: true, width: 280, height: 350, anchor: \'top left\'} )"><img src="'.SFRESOURCES.'small-tools.png" alt="" title="'.esc_attr(__("Show Edit Tools", "sforum")).'" /></a></div>';

	return $out;
}

?>