<?php
/*
Simple:Press
Forum General Page Rendering Routines
$LastChangedDate: 2011-06-05 09:16:54 -0700 (Sun, 05 Jun 2011) $
$Rev: 6253 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# = SUCCESS/FAILURE MESSAGING==================
if(!function_exists('sf_render_queued_message')):
function sf_render_queued_message()
{
	$out = '';
	$message = get_sfnotice('sfmessage');
	if(!empty($message))
	{
		$out = sf_message($message);
		delete_sfnotice();
	}
	return $out;
}
endif;

# = FORUM LOCKDOWN STRIP ======================
if(!function_exists('sf_render_lockdown')):
function sf_render_lockdown()
{
	$out = '<div class="sfmessagestrip">';
	$out.= '<img src="'.SFRESOURCES.'locked.png" alt="" />'."\n";
	$out.= __("This Forum is Currently Locked - Access is Read Only", "sforum").'</div>'."\n";
	return $out;
}
endif;

# = ADMIN STRIP ===============================
if(!function_exists('sf_render_admin_strip')):
function sf_render_admin_strip($source, $pageview, $newposts)
{
	global $current_user, $sfvars, $sfglobals;

	$out = '';
	if($current_user->forumadmin || $current_user->moderator)
	{
		$fixed = $sfglobals['member']['admin_options']['sfbarfix'];

		if($fixed)
		{
			$out.= '<div id="sfadminstripfixed">';
		} else {
			$out.= '<div id="sfadminstrip">';
		}
		if($current_user->forumadmin)
		{
			$out.= sf_render_admin_quicklinks($source, $pageview);
		}

		if(($source == 'forum' || $source == 'inbox') && $sfglobals['admin']['sfqueue'])
		{
			$out.= '<div id="sfpostnumbers">';
			$out.= sf_get_waiting_url($newposts, $pageview, $sfglobals['member']['admin_options']['sfshownewadmin']);
			$out.= '</div>';
		}

		# need to close this div differently depending on fixed bar or not - this is for fixed = false
		if(!$fixed)
		{
			$out.= '</div>';
		}
	}

	if($fixed)
	{
		$out.= '<div id="sfadminpostlistfixed"></div>';
		# This closes div if bar is fixed
		$out.= '</div>';
	} else {
		$out.= '<div id="sfadminpostlist"></div>';
	}

	return $out;
}
endif;

# = LOGIN STRIP ===============================
if(!function_exists('sf_render_login_strip')):
function sf_render_login_strip($source, $pageview, $button)
{
	global $sfvars, $current_user, $sfglobals;

	$sflogin = array();
	$sflogin = sf_get_option('sflogin');
	$out = '';
	$textbelow = '';

	$out.= '<div class="sfloginstrip">'."\n";
	$out.= '<div class="inline_edit" id="sfthisuser">'.$current_user->ID.'</div>';

	$out.= '<table cellpadding="1" cellspacing="0"><tr>'."\n";

	if($sflogin['sfshowavatar'])
	{
		$out.= '<td rowspan="2" width="45">';
		if ($current_user->member) $type = 'user';
        if ($current_user->forumadmin) $type='admin';
		if ($current_user->guest) $type = 'guest';
        $useremail = '';
        if (isset($current_user->user_email)) $useremail = sf_filter_email_display($current_user->user_email);
        $guestemail = '';
        if (isset($current_user->guestemail)) $guestemail = sf_filter_email_display($current_user->guestemail);
		$out.= sf_render_avatar($type, $current_user->ID, $useremail, $guestemail, false, '40', true);
		$out.= '</td>';
	}

	$out.= '<td class="sfusercell">'."\n";

	# User Name
	if($current_user->guest)
	{
		# need to check posting permissions in all forums to decide on guest posting message for group pageview
		if ($pageview == 'group')
		{
			if ($sfglobals['permissions'])
			{
				$checked = array();  # set up array to only check each forum id once to save time
				foreach ($sfglobals['permissions'] as $perm)
				{
					if (!isset($checked[$perm->forum_id]))
					{
						$permissions = sf_get_permissions(array('Can start new topics', 'Can reply to topics'), $perm->forum_id);
						$current_user->sfaddnew |= $permissions['Can start new topics'];
						$current_user->sfreply |= $permissions['Can reply to topics'];
						$checked[$perm->forum_id] = 1;   # mark this forum id as checked
					}
				}
			}
		}

		# Not logged in - might be a guest - so, do we allow guest posters?
		if(!$current_user->sfaddnew && !$current_user->sfreply)
		{
			if($pageview == 'pm')
			{
				$out.= '<strong>'.__("You must be logged in to use Private Messaging", "sforum").'</strong>'."\n";
			} else {
				$out.= '<strong>'.__("You must be logged in to post", "sforum").'</strong>'."\n";
			}
		} else {
			# So - Guests are allowed but could this be a registered user not yet logged in?
			if($current_user->offmember)
			{
				$out.= sprintf(__('Welcome back <strong>%s</strong>', "sforum"), $current_user->offmember);
				$textbelow = __("Please login if you intend posting", "sforum");
			} else {
				# So a genuine Guest - have they been here before?
				$out.= __("Current User: <strong>Guest</strong>", "sforum")."\n";
				if(!empty($current_user->guestname))
				{
					$out.= ': <strong>'.$current_user->guestname.'</strong>'."\n";
					if((!empty($current_user->lastvisit)) && ($current_user->lastvisit > 0))
					{
						$textbelow = __("Last Post", "sforum").': '.date_i18n(SFDATES, $current_user->lastvisit)."\n";
					}
				} else {
					if($sflogin['sfshowreg'])
					{
						$textbelow = '<strong>'.__("Please consider registering", "sforum").'</strong>'."\n";
					}
				}
			}
		}
	} else {
		$out.= sprintf(__("Logged in as <strong> %s </strong>", "sforum"), sf_filter_name_display($current_user->display_name))."\n";
		if(!empty($current_user->lastvisit)) $textbelow = __("Last Visit", "sforum").': '.date_i18n(SFDATES, $current_user->lastvisit)."\n";
	}
	$sfmemberopts = sf_get_option('sfmemberopts');
	if (($pageview == 'forum' || $pageview == 'topic') && $sfmemberopts['sfviewperm'])
	{
		$out.= sf_render_user_permissions();
	}
	$out.= '</td>'."\n";

	# Login/Register icons
	$out.= '<td class="sflogincell">'."\n";

	$out.= sf_render_custom_icons();

	if($current_user->guest)
	{
		if($sflogin['sfshowlogin'])
		{
			if($sflogin['sfinlogin'])
			{
				$out.= '<a class="sficon" onclick="sfjtoggleLayer(\'sfloginform\');"><img src="'.SFRESOURCES.'login.png" alt="" title="'.esc_attr(__("Login", "sforum")).'" />'.sf_render_icons("Login").'</a>'."\n";
			} else {
				$out.= '<a class="sficon" href="'.$sflogin['sfloginurl'].'");"><img src="'.SFRESOURCES.'login.png" alt="" title="'.esc_attr(__("Login", "sforum")).'" />'.sf_render_icons("Login").'</a>'."\n";
			}
			if (TRUE == get_option('users_can_register') && !$sfglobals['lockdown'])
			{
				if($sflogin['sfshowreg'])
				{
					$sfpolicy = sf_get_option('sfpolicy');
					if($sfpolicy['sfregtext'] ? $regurl=SFREGPOLICY : $regurl=$sflogin['sfregisterurl']);

					$out.= '<a class="sficon" href="'.$regurl.'"><img src="'.SFRESOURCES.'register.png" alt="" title="'.esc_attr(__("Register", "sforum")).'" />'.sf_render_icons("Register").'</a>'."\n";
				}
			}
            $sfmemberopts = sf_get_option('sfmemberopts');
	  		if ($sfmemberopts['sfshowmemberlist'] && !$sfmemberopts['sflimitmemberlist'] && $current_user->sfmemberlist)
	  		{
				$out.= '<a class="sficon" href="'.SFMEMBERLIST.'"><img src="'.SFRESOURCES.'members-display.png" alt="" title="'.esc_attr(__("Membership List", "sforum")).'" />'.sf_render_icons("Members").'</a>'."\n";
			}
		}
	} else {
		if($sflogin['sfshowlogin'])
		{
			$out.= '<a class="sficon" href="'.wp_nonce_url($sflogin['sflogouturl'], 'log-out').'"><img src="'.SFRESOURCES.'logout.png" alt="" title="'.esc_attr(__("Logout", "sforum")).'" />'.sf_render_icons("Logout").'</a>'."\n";
  		}
        $sfmemberopts = sf_get_option('sfmemberopts');
  		if ($sfmemberopts['sfshowmemberlist'] && ($current_user->sfmemberlist))
  		{
			$out.= '<a class="sficon" href="'.SFMEMBERLIST.'"><img src="'.SFRESOURCES.'members-display.png" alt="" title="'.esc_attr(__("Membership List", "sforum")).'" />'.sf_render_icons("Members").'</a>'."\n";
		}
		$out.= '<a class="sficon" href="'.sf_build_profile_formlink($current_user->ID).'"><img src="'.SFRESOURCES.'profile.png" alt="" title="'.esc_attr(__("Profile", "sforum")).'" />'.sf_render_icons("Profile").'</a>'."\n";
	}
	$out.= '</td></tr><tr>';
	$out.= '<td class="sfusercell">'.$textbelow.'</td>';
	$out.= '<td class="sflogincell">'."\n";

	$out.='<div id="sfinboxcount">';
	$out.= sf_render_sub_count();
	$out.= sf_render_watch_count();
	$out.= sf_render_inbox_count();
	$out.= '</div>';

	$out.= '</td>'."\n";
	$out.= '</tr></table>'."\n";
	$out.= '</div>'."\n";

	$out.= sf_process_hook('sf_hook_post_loginstrip', '');

	return $out;
}
endif;

# = LOGIN FORM ==+=============================
if(!function_exists('sf_render_login_form')):
function sf_render_login_form()
{
	global $current_user;

	$sflogin=array();
	$sflogin=sf_get_option('sflogin');

	$out = '';
	if($current_user->guest && $sflogin['sfshowlogin'] && $sflogin['sfinlogin'])
	{
		$out.= sf_inline_login_form();
	}
	return $out;
}
endif;

# = Subscription Count ==============================
if(!function_exists('sf_render_sub_count')):
function sf_render_sub_count()
{
	global $current_user, $sfglobals;

	$out='';

	# Subscription Count and Button
	if ($current_user->sfsubscriptions)
	{
		$scount = 0;
		$unreadclass='sfrednumberzero';

		$list = $sfglobals['member']['subscribe'];
		if (!empty($list))
		{
			foreach ($list as $topicid)
			{
				if (sf_is_in_users_newposts($topicid))
				{
					$scount++;
					$unreadclass='sfrednumber';
				}
			}
		}
        $site = SFHOMEURL."index.php?sf_ahah=watch-subs&action=subs";
  		$out.= '<span><a rel="nofollow" class="sficon" href="'.$site.'" onclick="return hs.htmlExpand(this, { objectType: \'ajax\', preserveContent: true, reflow: true, width: 700} )"><img class="sfalignright" src="'. SFRESOURCES .'subscribed.png" alt="" title="'.__("Review Subscribed Topics", "sforum").'" /><span id="sfunreadsub" class="'.$unreadclass.' sfalignright" title="'.esc_attr(__("New Posts in Subscribed Topics", "sforum")).'">'.$scount.'</span></a></span>';
	}
	return $out;
}
endif;

# = Watch Count ==============================
if(!function_exists('sf_render_watch_count')):
function sf_render_watch_count()
{
	global $current_user, $sfglobals;

	$out='';

	# Watched Count and Button
	if ($current_user->sfwatch)
	{
		$wcount = 0;
		$unreadclass='sfrednumberzero';

		$list = $sfglobals['member']['watches'];
		if (!empty($list))
		{
			foreach ($list as $topicid)
			{
				if (sf_is_in_users_newposts($topicid))
				{
					$wcount++;
					$unreadclass='sfrednumber';
				}
			}
		}
		$spacerimg = '<img class="sfalignright" src="'. SFRESOURCES .'spacer.png" alt="" />';

        $site = SFHOMEURL."index.php?sf_ahah=watch-subs&action=watch";
		$out.= '<span><a rel="nofollow" class="sficon" href="'.$site.'" onclick="return hs.htmlExpand(this, { objectType: \'ajax\', preserveContent: true, reflow: true, width: 700} )">'.$spacerimg.'<img class="sfalignright" src="'. SFRESOURCES .'watching.png" alt="" title="'.esc_attr(__("Review Watched Topics", "sforum")).'" /><span id="sfunreadwatch" class="'.$unreadclass.' sfalignright" title="'.esc_attr(__("New Posts in Watched Topics", "sforum")).'">'.$wcount.'</span></a></span>';
	}
	return $out;
}
endif;

# = Inbox Count ==============================
if(!function_exists('sf_render_inbox_count')):
function sf_render_inbox_count()
{
	global $current_user, $sfglobals;

	$out = '';

	if ($current_user->sfusepm)
	{
		#Inbox Count and Button
		$new = sf_get_pm_inbox_new_count($current_user->ID);
		if(!$new) $new=0;

		# Do we show inbox icon?
		$unreadclass='sfrednumber';
		if($new == 0) $unreadclass='sfrednumberzero';
		$spacerimg = '<img class="sfalignright" src="'. SFRESOURCES .'spacer.png" alt="" />';

		$url = SFURL."private-messaging/inbox/";
		$out.= '<span><a class="sficon" href="'.$url.'">'.$spacerimg.'<img class="sfalignright" src="'. SFRESOURCES .'goinbox.png" alt="" title="'.esc_attr(__("Go To Inbox", "sforum")).'" /><span id="sfunreadpm" class="'.$unreadclass.' sfalignright" title="'.esc_attr(__("New PMs", "sforum")).'">'.$new.'</span></a></span>';
	}

	return $out;
}
endif;

# = CUSTOM ICONS ==============================
if(!function_exists('sf_render_custom_icons')):
function sf_render_custom_icons()
{
	global $sfglobals;

	$out = '';
	for($x=0; $x<3; $x++)
	{
		if(!empty($sfglobals['custom'][$x]['cuslink']))
		{
			$out.= '<a class="sficon" href="'.$sfglobals["custom"][$x]["cuslink"].'"><img src="'.esc_url(SFCUSTOMURL.$sfglobals["custom"][$x]["cusicon"]).'" alt="" />'.sf_filter_title_display($sfglobals["custom"][$x]["custext"]).'</a>'."\n";
		}
	}

	return $out;
}
endif;

# = SEARCH BAR ================================
if(!function_exists('sf_render_searchbar')):
function sf_render_searchbar($pageview)
{
	global $sfvars, $sfglobals;

	$out ='<div class="sfmessagestrip">'."\n";
	$out.='<table><tr>'."\n";

	if($sfglobals['display']['search']['searchtop'] || $sfglobals['display']['search']['searchbottom'])
	{
		$out.= '<td width="105">';
		$out.= '<div id="sftopsearch">';
		if($pageview != 'inbox')
		{
			$out.= '<a class="sficon" onclick="sfjtoggleLayer(\'sfsearchform\');"><img class="sficon" src="'.SFRESOURCES.'search.png" alt="" title="'.esc_attr(__("Search", "sforum")).'" />'.sf_render_icons("Search").'</a>'."\n";
			$out.= '</div>';
			$out.= '</td>'."\n";

			if($pageview == 'topic')
			{
				# If search mode - display link to return to search results
				if($sfvars['searchpage'])
				{
					$out.= '<td>'.sf_get_forum_search_url().'<img class="sficon" src="'.SFRESOURCES.'results.png" alt="" title="'.esc_attr(__("Return to Search Results", "sforum")).'" />'.sf_render_icons("Return to Search Results").'</a>'."\n";
					$out.= '</td>'."\n";
				}
			}
		} else {
			$out.= '<a class="sficon" href="#forumbottom"><img src="'.SFRESOURCES.'bottom.png" alt="" title="'.esc_attr(__("go to bottom", "sforum")).'" /></a>'."\n";
			$out.= '</div>';
			$out.= '</td>'."\n";
		}
	}

	if($sfglobals['display']['quicklinks']['qltop'] || $sfglobals['display']['quicklinks']['qlbottom'])
	{
		$out.= '<td>'."\n";
		$out.= '<div id="sftopql">';
		# QuickLinks
		$out.= sf_render_forum_quicklinks();
		if ($sfglobals['display']['quicklinks']['qlcount'] > 0)
		{
			$out.= '<div id="sfqlposts">';
			$out.= sf_render_newpost_quicklinks($sfglobals['display']['quicklinks']['qlcount']);
			$out.= '</div>';
		}
		$out.= '</div>';
		$out.= '</td>'."\n";
	}

	$out.='</tr></table></div>'."\n";

	return $out;
}
endif;

# = SEARCH BAR ================================
if(!function_exists('sf_render_searchform')):
function sf_render_searchform($pageview)
{
	global $sfvars, $sfglobals;

	$out = '';

	if($sfglobals['display']['search']['searchtop'] || $sfglobals['display']['search']['searchbottom'])
	{
		# get status set if forum
		$statusset = '';
		if($pageview == 'forum')
		{
			$statusset = sf_get_topic_status_set($sfvars['forumid']);
		}
		# Dislay search bar
		$out.= sf_searchbox($pageview, $statusset);
	}

	return $out;
}
endif;

# = BREADCRUMB STRIP ==========================
if(!function_exists('sf_render_breadcrumbs')):
function sf_render_breadcrumbs($forumslug, $topicslug, $page)
{
	global $sfvars, $post, $sfglobals, $current_user;

	$out = '';

	if ($sfglobals['display']['breadcrumbs']['showcrumbs'] || ($current_user->member && $sfglobals['display']['unreadcount']['unread']))
	{
		$out.= '<div class="sfmessagestrip sfbreadcrumbs">'."\n";
		$out.= '<table><tr>'."\n";
	}

	if ($sfglobals['display']['breadcrumbs']['showcrumbs'])
	{
		$out.= '<td valign="middle"><p>';
		$xtree = 0;
		$tree='';
		$treespace = '';
		if ($sfglobals['display']['breadcrumbs']['tree'])
		{
			$tree = '<br />';
			$treespace = '<span class="treespace"></span>';
			$xtree = 0;
		}

		$arr = '<img src="'.SFRESOURCES.'arrowr.png" alt="" />'."\n";

		# Home link
		if ($sfglobals['display']['breadcrumbs']['showhome'] &&
			!(get_option('page_on_front') == sf_get_option('sfpage') && get_option('show_on_front') == 'page'))
		{
			$out.= '<a class="sficon sfpath" href="'.$sfglobals['display']['breadcrumbs']['homepath'].'">'.$arr.__("Home", "sforum").'</a>'."\n";
			$xtree++;
		}

		# Group View link (front forum page)
		$out.= $tree.str_repeat($treespace, $xtree).'<a class="sficon sfpath" href="'.trailingslashit(SFURL).'">'.$arr.$post->post_title.'</a>'."\n";
		$xtree++;

		if ($sfglobals['display']['breadcrumbs']['showgroup'])
        {
       	    if (isset($_GET['group']))
            {
        		$groupid = sf_esc_int($_GET['group']);
        		if (sf_group_exists($groupid))
        		{
                    $group = sf_get_group_record($groupid);
                }
            } else if (isset($sfvars['forumslug'])) {
                $group = sf_get_group_record_from_slug($sfvars['forumslug']);
            }

            if ($group)
            {
                $out.= $tree.str_repeat($treespace, $xtree).'<a class="sficon sfpath" href="'.sf_build_qurl('group='.$group->group_id).'">'.$arr.sf_filter_title_display($group->group_name).'</a>'."\n";
                $xtree++;
            }
        }

		# Parent Forum Links if current forum is a sub-forum
		if(isset($sfvars['parentforumid']))
		{
			$forumnames = array_reverse($sfvars['parentforumname']);

			$forumslugs = array_reverse($sfvars['parentforumslug']);
			for($x=0; $x<count($forumnames); $x++)
			{
				$out.= $tree.str_repeat($treespace, $xtree).'<a class="sficon sfpath" href="'.sf_build_url($forumslugs[$x], '', 0, 0).'">'.$arr.sf_filter_title_display($forumnames[$x]).'</a>'."\n";
				$xtree++;
			}
		}

		# Forum link (Paren or Child forum)
		if(!empty($sfvars['forumslug']) && $sfvars['forumslug'] != 'all')
		{
			# if showing a topic then check the return page of forum in sfsettings...
			$returnpage = 1;
			if(!empty($sfvars['topicslug'])) $returnpage = sf_pop_topic_page($sfvars['forumid']);
			$forumname = $sfvars['forumname'];
			$out.= $tree.str_repeat($treespace, $xtree).'<a class="sficon sfpath" href="'.sf_build_url($sfvars['forumslug'], '', $returnpage, 0).'">'.$arr.sf_filter_title_display($forumname).'</a>'."\n";
			$xtree++;
		}

		# Topic lnk
		if(!empty($sfvars['topicslug']))
		{
			$topicname = $sfvars['topicname'];
			$out.= $tree.str_repeat($treespace, $xtree).'<a class="sficon sfpath" href="'.sf_build_url($sfvars['forumslug'], $sfvars['topicslug'], 1, 0).'">'.$arr.sf_filter_title_display($topicname).'</a>'."\n";
		}

		$out.= '</p></td>';
	}

	# Mark all as read
	if($current_user->member && $sfglobals['display']['unreadcount']['unread'] == true)
	{
		if($sfglobals['member']['newposts']['topics'][0] == 0)
		{
			$unreads = 0;
		} else {
			$unreads = count($sfglobals['member']['newposts']['topics']);
		}
		if($unreads==1 ? $label=__("Topic with", "sforum") : $label=__("Topics with", "sforum"));
		$out.= '<td valign="top" nowrap="nowrap"><span class="sfalignright sfunreadcount"><b>'.$unreads.'</b> '.$label.'&nbsp;<br />'.__("unread posts", "sforum").'</span></td>';
		if($unreads > 0 && $sfglobals['display']['unreadcount']['markall'])
		{
			$out.= '<td width="25" valign="top"><a href="'.SFURL.'newposts/"><img class="sfalignright" src="'.SFRESOURCES.'topics-started.png" alt="" title="'.esc_attr(__("Most Recent Topics With Unread Posts", "sforum")).'" /></a></td>';
			$out.= '<td width="25" valign="top"><a href="'.SFMARKREAD.'"><img class="sfalignright" src="'.SFRESOURCES.'mark-read.png" alt="" title="'.esc_attr(__("Mark All As Read", "sforum")).'" />'.sf_render_icons('Mark All Read').'</a></td>';
		}
	}

	if ($sfglobals['display']['breadcrumbs']['showcrumbs'] || ($current_user->member && $sfglobals['display']['unreadcount']['unread']))
	{
    	# Go To Bottom link
    	$out.= '<td valign="top" width="40">';
    	$out.= '<span class="sfalignright"><a class="sficon" href="#forumbottom"><img src="'.SFRESOURCES.'bottom.png" alt="" title="'.esc_attr(__("go to bottom", "sforum")).'" /></a></span>'."\n";
    	$out.= '</td>';
	}

	if ($sfglobals['display']['breadcrumbs']['showcrumbs'] || ($current_user->member && $sfglobals['display']['unreadcount']['unread']))
	{
		$out.= '</tr></table>'."\n";
		$out.= '</div>'."\n";
	}

	$out.= sf_process_hook('sf_hook_post_breadcrumbs', '');

	return $out;
}
endif;

# = PAGED TOPIC NAVIGATION SUPPORT ============
if(!function_exists('sf_pn_next')):
function sf_pn_next($cpage, $search, $totalpages, $baseurl, $pnshow, $list=false)
{
	if($pnshow == 0) $pnshow=4;
	$start = ($cpage - $pnshow);
	if($start < 1) $start = 1;
	$end = ($cpage - 1);
	$out='';

	if($start > 1)
	{
		$out.= sf_pn_url($cpage, 1, $search, $baseurl, 'None', $list);
		$out.= sf_pn_url($cpage, $cpage-1, $search, $baseurl, 'Previous', $list);
	}

	if($end > 0)
	{
		for($i = $start; $i <= $end; $i++)
		{
			$out.= sf_pn_url($cpage, $i, $search, $baseurl, 'None', $list);
		}
	} else {
		$end = 0;
	}
	return $out;
}
endif;

# = PAGED TOPIC NAVIGATION SUPPORT ============
if(!function_exists('sf_pn_previous')):
function sf_pn_previous($cpage, $search, $totalpages, $baseurl, $pnshow, $list=false)
{
	if($pnshow == 0) $pnshow=4;
	$start = ($cpage + 1);
	$end = ($cpage + $pnshow);
	if($end > $totalpages) $end = $totalpages;
	$out='';

	if($start <= $totalpages)
	{
		for($i = $start; $i <= $end; $i++)
		{
			$out.= sf_pn_url($cpage, $i, $search, $baseurl, 'None', $list);
		}
		if($end < $totalpages)
		{
			$out.= sf_pn_url($cpage, $cpage+1, $search, $baseurl, 'Next', $list);
			$out.= sf_pn_url($cpage, $totalpages, $search, $baseurl, 'None', $list);
		}
	} else {
		$start = 0;
	}
	return $out;
}
endif;

# = PAGED TOPIC NAVIGATION SUPPORT ============
if(!function_exists('sf_pn_url')):
function sf_pn_url($cpage, $thispage, $search, $baseurl, $arrow='None', $list=false)
{
	$out='';

    if ($search)
	{
		$out.= $baseurl . '&amp;search='.$thispage;
	} else {
		if ($thispage > 1)
		{
			$out.= trailingslashit($baseurl . 'page-'.$thispage);
		} else {
			$out.= $baseurl;
		}
	}

	Switch ($arrow)
	{
		case 'None':
			$out.= '">'.$thispage.'</a>';
			break;
		case 'Previous':
			$out.= '" class="sfpointer"><img src="'.SFRESOURCES.'arrowl.png" alt="" /></a>'."\n";
			break;
		case 'Next':
			$out.= '" class="sfpointer"><img src="'.SFRESOURCES.'arrowr.png" alt="" /></a>'."\n";
			break;
	}
	return $out;
}
endif;

# = VIEW HEADER TABLE =========================
if(!function_exists('sf_render_main_header_table')):
function sf_render_main_header_table($view, $itemid, $title, $desc, $icon, $forumlock=false, $showadd=false, $topiclock=false, $blogpostid=0, $pmview='inbox', $cansendpm=true, $forums=0, $statusset=0, $statusflag=0, $messagecount=0)
{
	global $sfvars, $current_user, $sfglobals;

	$out = '<div class="sfheading">';
	$out.= '<table><tr>'."\n";
	$out.= '<td class="sficoncell"><img class="" src="'.esc_url($icon).'" alt="" /></td>'."\n";
	switch($view)
	{
		case 'group':
			$title = sf_filter_title_display($title);
			$out.= '<td><div class="sftitle"><p>'.$title.'</p></div>';
			if ($sfglobals['display']['groups']['description'])
			{
				$out.= '<div class="sfdescription">'.$desc.'</div>';
			}
			$out.= '</td>'."\n";

			# dont display group rss icon if all forum rss feeds are disabled
			$rss_display = 0;
			if ($forums)
			{
				foreach ($forums as $forum)
				{
					if ($forum['forum_rss_private'] == 0)
					{
						$rss_display = 1;
						break;
					}
				}
			}

			if ($rss_display)
			{
				$rssurl= sf_get_group_rss_url($itemid);
				$out.= '<td class="sfadditemcell"><a class="sficon" rel="nofollow" href="'.$rssurl.'"><img src="'.SFRESOURCES.'feedgroup.png" alt="" title="'.esc_attr(__("Group RSS", "sforum")).'" />'.sf_render_icons('Group RSS').'&nbsp;</a></td>'."\n";
			}
			break;

		case 'forum':
		case 'subs':
		case 'watches':
			$title = sf_filter_title_display($title);
			$out.= '<td><div class="sftitle"><p>'.$title.'</p></div>'."\n";
			if ($sfvars['searchpage'])
			{
				$out.= ' - '.__("Search Results", "sforum").'<br />'.sf_deconstruct_search_for_display($sfvars['searchvalue'], $sfvars['searchtype'])."\n";
				if($sfvars['searchresults'] == 1 ? $mat = __("Match Found", "sforum") : $mat = __("Matches Found", "sforum"));
				$out.= '('.$sfvars['searchresults'].' '.$mat.')';
			}
			if ($sfglobals['display']['forums']['description'])
			{
				$out.= '<div class="sfdescription">'.$desc.'</div>';
			}
			$out.= '</td>'."\n";

			$out.= '<td class="sfadditemcell">'."\n";
			if ($showadd)
			{
				if($sfglobals['display']['pagelinks']['ptop']==false && $current_user->sfaddnew) {
					if ($current_user->offmember)
					{
						$sflogin = sf_get_option('sflogin');
						$out.= '<a class="sficon" href="'.esc_url($sflogin['sfloginurl'].'&amp;redirect_to='.urlencode($_SERVER['REQUEST_URI'])).'"><img src="'.SFRESOURCES.'login.png" alt="" title="'.esc_attr(__("Login", "sforum")).'" />'.sf_render_icons("Login").'</a>'."\n";
					} elseif($sfglobals['display']['pagelinks']['ptop']==false && $current_user->sfaddnew) {
						# show add topic here if pagelinks strip is hidden
						$out.= '<a class="sficon" onclick="sfjOpenEditor(\'sfpostform\','.$sfglobals['editor']['sfeditor'].',\'topic\');"><img src="'.SFRESOURCES.'addtopic.png" alt="" title="'.esc_attr(__("Add a New Topic", "sforum")).'" />'.sf_render_icons("Add a New Topic").'</a>'."\n";
					}
				}
			} else {
				if ($forumlock)
				{
					$out.= '<img class="sficon" src="'.SFRESOURCES.'locked.png" alt="" title="'.esc_attr(__("Forum Locked", "sforum")).'" />'.sf_render_icons("Forum Locked")."\n";
				}
			}

            if ($view == 'subs')
            {
       			$out.= '<form action="'.SFURL.'" method="get" name="sf-endallsubs">';
    			$out.= '<input type="hidden" class="sfhiddeninput" name="userid" id="userid" value="'.$current_user->ID.'" />';
    			$out.= '<input type="submit" class="sfcontrol" name="endallsubs" value="'.sf_split_button_label(esc_attr(__("Remove All Subscriptions", "sforum")), 3).'" />'."\n";
       			$out.= '</form>';
            }
            if ($view == 'watches')
            {
       			$out.= '<form action="'.SFURL.'" method="get" name="sf-endallwatches">';
    			$out.= '<input type="hidden" class="sfhiddeninput" name="userid" id="userid" value="'.$current_user->ID.'" />';
    			$out.= '<input type="submit" class="sfcontrol" name="endallwatches" value="'.sf_split_button_label(esc_attr(__("Remove All Watches", "sforum")), 3).'" />'."\n";
       			$out.= '</form>';
            }
			$out.= '</td>'."\n";
			break;

		case 'subforum':
			$title = sf_filter_title_display($title);
			$out.= '<td><div class="sftitle"><p>'.$title;
			$out.= '<br />'.__("Sub-Forums", "sforum");
			$out.= '</p></div></td>'."\n";
			break;

		case 'topic':
			$title = sf_filter_title_display($title);

			$out.= '<td><div class="sftitle"><p>'.$title;

			if (function_exists('sf_forum_show_blog_link'))
			{
				if($blogpostid != 0)
				{
					$out.= '<br />'.sf_forum_show_blog_link($blogpostid);
				}
			}
			$out.= '</p></div>'."\n";
			if ($sfglobals['display']['posts']['topicstatushead'])
			{
				$out.= sf_render_topic_statusflag($statusset, $statusflag, 'ts-header', 'ts-upheader', 'left');
			}
			$out.= '</td>';
			$out.= '<td class="sfadditemcell">'."\n";

			# Display reply to post link if alowed - or locked icon if topic locked
			if ((!$topiclock) || ($current_user->forumadmin))
			{
				if ($showadd)
				{
					if($current_user->offmember)
					{
						$sflogin = sf_get_option('sflogin');
						$out.= '<a class="sficon" href="'.esc_url($sflogin['sfloginurl'].'&amp;redirect_to='.urlencode($_SERVER['REQUEST_URI'])).'"><img src="'.SFRESOURCES.'login.png" alt="" title="'.esc_attr(__("Login", "sforum")).'" />'.sf_render_icons("Login").'</a>'."\n";
					} elseif($sfglobals['display']['pagelinks']['ptop']==false && $current_user->sfaddnew) {
						# show add topic here if pagelinks strip is hidden

						if($current_user->sfaddnew)
						{
							$url = sf_build_url($sfvars['forumslug'], '', 1, 0).sf_add_get().'new=topic';
							$out.= '<a class="sficon" href="'.$url.'"><img src="'.SFRESOURCES.'addtopic.png" alt="" title="'.esc_attr(__("Add a New Topic", "sforum")).'" />'.sf_render_icons("Add a New Topic").'</a>'."\n";
						}
						if ($current_user->sfreply && $sfvars['displaymode'] == 'posts')
						{
							$out.= '<a class="sficon" onclick="sfjOpenEditor(\'sfpostform\','.$sfglobals['editor']['sfeditor'].',\'post\');"><img src="'.SFRESOURCES.'addpost.png" alt="" title="'.esc_attr(__("Reply to Post", "sforum")).'" />'.sf_render_icons("Reply to Post").'</a>'."\n";
						}

					}
				}
			} else {
				$out.= '<img class="sficon" src="'.SFRESOURCES.'locked.png" alt="" title="'.esc_attr(__("Topic Locked", "sforum")).'" />'.sf_render_icons("Topic Locked")."\n";
			}

        	if ($sfglobals['display']['topics']['print'])
        	{
        		$out.= '<div class="sfalignright">'.sf_render_topic_printicon($sfvars['topicid']).'</div>';
        	}

			$out.= '</td>'."\n";
			break;

		case 'searchall':
			$out.= '<td><p>'.__("Search All Forums", "sforum").'<br />'.sf_deconstruct_search_for_display($sfvars['searchvalue'], $sfvars['searchtype'])."\n";
			if ($sfvars['searchresults'] == 1 ? $mat = __("Match Found", "sforum") : $mat = __("Matches Found", "sforum"));
			$out.= '('.$sfvars['searchresults'].' '.$mat.')</p></td>'."\n";
			break;

		case 'list':
			$title = sf_filter_title_display($title);
			$out.= '<td><div class="sftitle"><p>'.$title.'</p></div><div class="sfdescription">'.$desc.'</div></td>'."\n";
			$out.= '<td><img class="sfalignright" src="'. SFRESOURCES .'spacer.png" alt="" /><a class="sficon sfalignright" href="'.SFURL.'"><img src="'.SFRESOURCES.'goforum.png" alt="" title="'.esc_attr(__("Return to forum", "sforum")).'" />'.sf_render_icons("Return to forum").'</a></td>'."\n";
			break;
	}
	$out.= '</tr></table></div>'."\n";
	return $out;
}
endif;

# = LAST VISIT/POST ICON LEGEND ===============
if(!function_exists('sf_render_forum_icon_legend')):
function sf_render_forum_icon_legend()
{
	global $current_user;

	$out = '<span class="sficonkey"><small>';
	if ($current_user->lastvisit > 0)
	{
		if ($current_user->member)
		{
			$mess = __("since your last visit", "sforum");
		} else {
			$mess = __("since you last posted", "sforum");
		}
		$out.= '<img src="'. SFRESOURCES .'topickey.png" alt="" />&nbsp;&nbsp;'.__("New Posts", "sforum").'&nbsp;'.$mess.'&nbsp;&nbsp;'."\n";
	}
	if ($current_user->member)
	{
		$out.= '<img src="'. SFRESOURCES .'topickeyuser.png" alt="" />&nbsp;&nbsp;'.__("Topics you have posted in", "sforum")."\n";
	}
	$out.= '&nbsp;</small></span>';
	return $out;
}
endif;

# = RENDER PAGE LINKS (IN ENTRIES) ============
if(!function_exists('sf_render_inline_pagelinks')):
function sf_render_inline_pagelinks($forumslug, $topicslug, $postcount, $searchparam='')
{
	global $sfvars, $sfglobals;

	$postpage=$sfglobals['display']['posts']['perpage'];
	if($postpage >= $postcount) return '';

	$out='';

	$out.= '<table class="sfrowstatus">';
	$out.= '<tr>';

	$totalpages=($postcount / $postpage);
	if(!is_int($totalpages)) $totalpages=intval($totalpages)+1;

	$sfpnshow = $sfglobals['display']['posts']['numpagelinks'];
	if($sfpnshow == 0) $sfpnshow = 5;

	if($totalpages > $sfpnshow)
	{
		$maxcount=$sfpnshow;
	} else {
		$maxcount=$totalpages;
	}

	if($sfvars['searchpage'] != 0)
	{
		if($searchparam == 'SA')
		{
			$ret = '&amp;ret=all';
		} else {
			$ret = '';
		}
	}

	if((isset($sfvars['page'])) && $sfvars['page'] <> 1)
	{
		$xtp = '&amp;xtp='.$sfvars['page'];
	} else {
		$xtp = '';
	}

	for($x = 1; $x <= $maxcount; $x++)
	{
		$out.= '<td class="sfrowpages"><a href="'.sf_build_url($forumslug, $topicslug, $x, 0);
		if (isset($sfvars['searchvalue']) && $sfvars['searchvalue'] != '')
		{
			if(strpos(SFURL, '?') === false)
			{
				$out.= '?value';
			} else {
				$out.= '&amp;value';
			}
			$out.= '='.$sfvars['searchvalue'].'&amp;type='.$sfvars['searchtype'].'&amp;include='.$sfvars['searchinclude'].'&amp;search='.$sfvars['searchpage'].$ret;
		}
		$out.= '">'.$x.'</a></td>'."\n";
		$sep = '| ';
	}

	if($totalpages > $sfpnshow)
	{
		$out.= '<td class="nobg"><img src="'.SFRESOURCES.'pagelink.png" alt="" /></td>';
		$out.= '<td><a href="'.sf_build_url($forumslug, $topicslug, $totalpages, 0);
		if($sfvars['searchvalue'] != '')
		{
			if(strpos(SFURL, '?') === false)
			{
				$out.= '?value';
			} else {
				$out.= '&amp;value';
			}
			$out.= '='.$sfvars['searchvalue'].'&amp;type='.$sfvars['searchtype'].'&amp;include='.$sfvars['searchinclude'].'&amp;search='.$sfvars['searchpage'].$ret;
		}
		$out.= '">'.$totalpages.'</a></td>'."\n";
	}

	$out.= '</tr>';
	$out.= '</table>';

	return $out;
}
endif;

if(!function_exists('sf_render_topic_statusflag')):
function sf_render_topic_statusflag($statusset, $statusflag, $boxid, $updateid, $alignment)
{
	global $sfglobals;

	if ($statusset == 0) return;

	$out = '';
	if($statusflag != 0)
	{
		# topic status is active
		$topicstatusflag = sf_get_topic_status_flag($statusset, $statusflag);
	} else {
		$topicstatusflag = __('No Status Selected', 'sforum');
	}

	if($alignment == 'left')
	{
		$out.= '<span class="sfalignleft" id="'.$boxid.'">';
		$out.= '<img class="sfalignleft" src="'.SFRESOURCES.'small-status.png" alt="" />';
		$out.= '<span id="'.$updateid.'" class="sfstatusitem sfalignleft">'.$topicstatusflag.'</span>';
	} else {
		$out.= '<span class="sfalignright" id="'.$boxid.'">';
		$out.= '<img class="sfalignleft" src="'.SFRESOURCES.'small-status.png" alt="" />';
		$out.= '<span id="'.$updateid.'" class="sfstatusitem sfalignright">'.$topicstatusflag.'</span>';
	}
	$out.= '</span>';
	return $out;
}
endif;

# = FIRST/LAST POST CELLS =====================
if(!function_exists('sf_render_first_last_post_cell')):
function sf_render_first_last_post_cell($forumslug, $topicslug, $item, $alt, $title='')
{
	global $sfglobals;

	$postuser='';
	$postdate='';
	$posttime='';

	# if there is a 'proxy' element then use that as forum id (will be last post in a subforum)
	# Will ONLY be available from Group View forum listing
	if(array_key_exists('proxy', $item))
	{
		$forumslug = $item['proxy_slug'];
	}

	if($sfglobals['display']['firstlast']['user'])
	{
		$poster = sf_build_name_display($item['user_id'], sf_filter_name_display($item['display_name']));
		if(empty($poster)) $poster = sf_filter_name_display($item['guest_name']);
        $postuser = ' '.__("by", "sforum").'<br />'.$poster;
	}
	if($sfglobals['display']['firstlast']['date'])
	{
		$postdate = sf_date('d', $item['post_date']);
	}
	if($sfglobals['display']['firstlast']['time'])
	{
		$posttime = sf_date('t',$item['post_date']);
	}

	if (empty($item['post_tip'])) $item['post_tip'] = '';
	if ($title == '')
	{
		$out = '<td class="sfuserdetails '.$alt.'">';
    	$out.= '<p>'.$postdate."<br />".$posttime;
        $out.= $postuser.'<br />'.sf_get_post_url($forumslug, $topicslug, $item['post_id'], $item['post_index'], $title, $item['post_tip']).'</p></td>'."\n";
	} else {
		$out = '<td class="sfuserdetailswide '.$alt.'">';
		if (isset($sfglobals['display']['forums']['showtitletop']) && $sfglobals['display']['forums']['showtitletop'])
        {
        	$out.= '<p>'.sf_get_post_url($forumslug, $topicslug, $item['post_id'], $item['post_index'], $title, $item['post_tip']).'<br />'.$postdate."<br />".$posttime;
        	$out.= $postuser.'</p></td>'."\n";
        } else {
            $out.= '<p>'.$postdate."<br />".$posttime;
            $out.= $postuser.'<br />'.sf_get_post_url($forumslug, $topicslug, $item['post_id'], $item['post_index'], $title, $item['post_tip']).'</p></td>'."\n";
        }
	}
	return $out;
}
endif;

function sf_render_sub_forum_entry_row($subforums, $alt)
{
	global $sfglobals, $current_user;

	$out = '';
	$icon = SFRESOURCES.'subforum.png';

	$out.= '<tr>';
	$out.= '<td colspan="5" class="sfsubforum '.$alt.'">';
	$subs = count($subforums);
	if ($subs == 1 ? $label=__("Subforum", "sforum") : $label=__("Subforums", "sforum"));

	$out.= '<div class="sfrowtitle">';
	$out.= '<p><img src="'.$icon.'" alt="" />&nbsp;'.$label.':&nbsp;'."\n";

	$first = true;

	foreach ($subforums as $subforum)
	{
		if(!$first) $out.= ' | ';
		$first=false;
		$out.= sf_render_forum_title($subforum['forum_slug'], sf_filter_title_display($subforum['forum_name'])).' ('.$subforum['topic_count'].')'."\n";

		# New Post Icon
		if ($sfglobals['display']['forums']['newposticon'] && $current_user->member && is_array($sfglobals['member']['newposts']['forums']))
		{
			if (in_array($subforum['forum_id'], $sfglobals['member']['newposts']['forums']))
			{
				$out.= '<img src="'.SFRESOURCES.'small-post.png" alt="" title="'.esc_attr(__("New Posts", "sforum")).'" />';
			}
		}
	}

	$out.= '</p>';
	$out.= '</div>';
	$out.= '</td></tr>';

	return $out;
}

# = BOTTOM ICON (RRS) STRIP ===================
if(!function_exists('sf_render_bottom_iconstrip')):
function sf_render_bottom_iconstrip($view, $user='', $cansendpm='')
{
	global $sfvars, $current_user, $sfglobals;

    $rssopt = sf_get_option('sfrss');

	$out = '<div class="sfmessagestrip">'."\n";
	$out.= '<table cellpadding="0" cellspacing="0"><tr><td width="45%">'."\n";

	# RSS
	$rss_display = 1;
	if ($view != 'inbox' && $view !='sentbox')
	{
		switch($view)
		{
			case 'all':
				# dont display icon for private rss feeds
				$forums = sf_get_forums_all();
				if ($forums)
				{
					$rss_display = 0;
					foreach ($forums as $forum)
					{
						if ($forum->forum_rss_private == 0)
						{
							$rss_display = 1;
							break;
						}
					}
				}

				$url = sf_get_option('sfallRSSurl');
				if(empty($url))
				{
                    if ($rssopt['sfrssfeedkey'] && isset($sfglobals['member']['feedkey']))
	       				$url = sf_build_qurl('xfeed=all', 'feedkey='.$sfglobals['member']['feedkey']);
                    else
	       				$url = sf_build_qurl('xfeed=all');

				}
				$icon='feedall.png';
				$text= __('All RSS', 'sforum');
				break;

			case 'forum':
				if($sfvars['error']==false)
				{
					# dont display icon for private rss feeds
					$forum = sf_get_forum_record($sfvars['forumid']);
					if ($forum->forum_rss_private) $rss_display = 0;

					$url = sf_get_forum_rss_url($sfvars['forumid'], $sfvars['forumslug']);
					$icon='feedforum.png';
					$text=__('Forum RSS', 'sforum');
				}
				break;

			case 'topic':
				if($sfvars['error']==false)
				{
					# dont display icon for private rss feeds
					$forum = sf_get_forum_record($sfvars['forumid']);
					if ($forum->forum_rss_private) $rss_display = 0;

                    if ($rssopt['sfrssfeedkey'])
    					$url = sf_build_qurl('forum='.$sfvars['forumslug'], 'topic='.$sfvars['topicslug'], 'xfeed=topic', 'feedkey='.$sfglobals['member']['feedkey']);
                    else
    					$url = sf_build_qurl('forum='.$sfvars['forumslug'], 'topic='.$sfvars['topicslug'], 'xfeed=topic');

					$icon='feedtopic.png';
					$text=__('Topic RSS', 'sforum');
				}
				break;
		}

		if($sfvars['error']==false && $rss_display)
		{
			$out.= '<a class="sficon sfalignleft" rel="nofollow" href="'.$url.'"><img src="'.SFRESOURCES.$icon.'" alt="" title="'.esc_attr($text).'" />'.sf_render_icons($text).'</a>'."\n";
			$out.= '<img class="sficon sfalignleft" src="'.SFRESOURCES.'spacer.png" alt="" title="" />';
		}
	}

	# Subscribe
	if($sfvars['error']==false && $view != 'inbox')
	{
		if ($current_user->member && $current_user->sfsubscriptions && $view=='topic' && !$sfglobals['lockdown'])
		{
			if (sf_is_subscribed($current_user->ID, $sfvars['topicid']))
			{
				$out.= '<a class="sficon sfalignleft" href="'.sf_build_qurl('forum='.$sfvars['forumid'], 'topic='.$sfvars['topicid'],'sf-endsub=user').'"><img src="'.SFRESOURCES.'unsubscribe.png" alt="" title="'.esc_attr(__("End Topic Subscription", "sforum")).'" />'.sf_render_icons("Unsubscribe").'</a>'."\n";
			} else {
				$out.= '<a class="sficon sfalignleft" href="'.sf_build_qurl('forum='.$sfvars['forumid'], 'topic='.$sfvars['topicid'],'sf-subscribe=user').'"><img src="'.SFRESOURCES.'subscribe.png" alt="" title="'.esc_attr(__("Subscribe", "sforum")).'" />'.sf_render_icons("Subscribe").'</a>'."\n";
			}
			$out.= '<img class="sficon sfalignleft" src="'.SFRESOURCES.'spacer.png" alt="" title="" />';
		}
	}

	# Watch
	if($sfvars['error']==false && $view != 'inbox')
	{
		if ($current_user->member && $current_user->sfwatch && $view=='topic' && !$sfglobals['lockdown'])
		{
			if (sf_is_watching($current_user->ID, $sfvars['topicid']))
			{
				$out.= '<a class="sficon sfalignleft" href="'.sf_build_qurl('forum='.$sfvars['forumid'], 'topic='.$sfvars['topicid'],'sf-endwatch=user').'"><img src="'.SFRESOURCES.'watchoff.png" alt="" title="'.esc_attr(__("End Topic Watch", "sforum")).'" />'.sf_render_icons("End Topic Watch").'</a>'."\n";
			} else {
				$out.= '<a class="sficon sfalignleft" href="'.sf_build_qurl('forum='.$sfvars['forumid'], 'topic='.$sfvars['topicid'],'sf-watch=user').'"><img src="'.SFRESOURCES.'watchon.png" alt="" title="'.esc_attr(__("Watch Topic", "sforum")).'" />'.sf_render_icons("Watch Topic").'</a>'."\n";
			}
		}
	}

	$out.='</td><td align="center">';

	# Go to top button
	$out.= '<a href="#forumtop"><img class="sficon" src="'.SFRESOURCES.'top.png" alt="" title="'.esc_attr(__("go to top", "sforum")).'" /></a><br />'."\n";

	# Dummy cell here - might be of some use in the future!
	$out.='</td><td class="sflogincell" width="45%">';

	# PM buttons on bottom strip
	if ($view == 'inbox' || $view == 'sentbox')
	{
		$sfpm = array();
		$sfpm = sf_get_option('sfpm');
		if ($cansendpm && (!$sfpm['sfpmlimitedsend'] || $current_user->forumadmin))
		{
			$out.= '<a class="sficon sfalignright" onclick="sfjtoggleLayer(\'sfpostform\');"><img src="'.SFRESOURCES.'compose.png" alt="" title="'.esc_attr(__("Compose PM", "sforum")).'" />'.sf_render_icons("Compose PM").'</a>';
		}
		if ($view == 'sentbox')
		{
			$url = SFURL."private-messaging/inbox/";
			$out.= '<a class="sficon sfalignright" href="'.$url.'"><img src="'.SFRESOURCES.'goinbox.png" alt="" title="'.esc_attr(__("Go To Inbox", "sforum")).'" />&nbsp;'.sf_render_icons("Go To Inbox").'</a>'."\n";
		} else {
			$url = SFURL."private-messaging/sentbox/";
			$out.= '<a class="sficon sfalignright" href="'.$url.'"><img src="'.SFRESOURCES.'gosentbox.png" alt="" title="'.esc_attr(__("Go To Sentbox", "sforum")).'" />&nbsp;'.sf_render_icons("Go To Sentbox").'</a>'."\n";
		}
		$out.= '<img class="sfalignright" src="'. SFRESOURCES .'spacer.png" alt="" /><a class="sficon sfalignright" href="'.SFURL.'"><img src="'.SFRESOURCES.'goforum.png" alt="" title="'.esc_attr(__("Return to forum", "sforum")).'" />'.sf_render_icons("Return to forum").'</a>'."\n";
	}
	$out.='</td></tr></table>';
	$out.='</div>'."\n";

	$out.= sf_process_hook('sf_hook_post_rss_strip', '');

	return $out;
}
endif;

# = STATISTICS STRIP ==========================
if(!function_exists('sf_render_stats')):
function sf_render_stats()
{
	global $sfglobals;

    $out = '';

	if($sfglobals['display']['stats']['showstats'])
	{
		$tz=get_option('timezone_string');
		if(empty($tz)) $tz = 'UTC '.get_option('gmt_offset');
		if($tz ? $w=2 : $w=4);
		$out.= '<br /><table id="sfstatstrip">'."\n";
		$out.= '<tr class="sfstatsheader">';
		$out.= '<th colspan="'.$w.'"><p>'.sprintf(__("About the %s Forum", "sforum"), get_bloginfo('name')).'</p></th>';
		if($tz)
		{
			$out.= '<th colspan="'.$w.'"><p class="sfalignright"><strong>'.__("Forum Timezone: ", "sforum").'</strong>'.$tz.'</p></th>';
		}
		$out.= '</tr>'."\n";

		$out.= '<tr class="sfstatsdata">'."\n";
		$out.= sf_render_online_stats();
		$out.= sf_render_forum_stats();
		$stats = sf_get_post_stats();
		$out.= sf_render_member_stats($stats).'</tr>';
		$out.= sf_render_newusers();
		$out.= sf_render_ownership($stats);
		$out.= '</table><br />'."\n";

		$out.= sf_process_hook('sf_hook_post_stats', '');
	}
	return $out;
}
endif;

# = STATISTICS STRIP SUPPORT ROUTINE===========
if(!function_exists('sf_render_online_stats')):
function sf_render_online_stats($dashboard=false)
{
	global $wpdb, $sfglobals, $sfvars, $current_user;

	$out='';
	$browse='';

	$online = sf_get_online_total();
	$members = sf_get_members_online();
	$max = sf_update_max_online($online);

	if($sfglobals['display']['stats']['mostusers'] || $sfglobals['display']['stats']['online'] || $dashboard)
	{
		$out.= '<td class="sfstatsonline">'."\n";

		$guests = 0;
		$label = ' '.__("Guests", "sforum");

		# Most users online
		if($sfglobals['display']['stats']['mostusers'] || $dashboard)
		{
			$out.= '<p><span><span class="sfstatsmaxtitle"><strong>'.__("Most Users Ever Online", "sforum").': </strong></span>'."\n";
			$out.= '<span class="sfstatsmaxdata">'.$max.'</span></span></p>'."\n";
		}

		# Current members online
		if($sfglobals['display']['stats']['online'] || $dashboard)
		{
			if(($members || $online && ($online > count($members))))
			{
				$out.= '<p><span><span class="sfstatsonlinecurrent"><strong>'.__("Currently Online", "sforum").': </strong></span>'."\n";
			}
			if ($members)
			{
				$firstonline = true;
				$firstbrowsing = true;

				$out.= '<span class="sfstatsonlineuser">';
				foreach ($members as $user)
				{
					$opts = sf_get_member_item($user->trackuserid, 'user_options');
                	$sfmemberopts = sf_get_option('sfmemberopts');
					if ($current_user->forumadmin || !$sfmemberopts['sfhidestatus'] || !$opts['hidestatus'])
					{
						if(!$firstonline) $out.= ', ';
						$out.= sf_build_name_display($user->trackuserid, sf_filter_name_display($user->display_name), true);
						$firstonline = false;

						# Set up the members browsing curent item list while here
						if(($sfvars['pageview']=='forum' && $user->forum_id==$sfvars['forumid']) || ($sfvars['pageview']=='topic' && $user->topic_id==$sfvars['topicid']))
						{
							if(!$firstbrowsing) $browse.= ', ';
							$browse.= sf_build_name_display($user->trackuserid, sf_filter_name_display($user->display_name), true);
							$firstbrowsing = false;
						}
					}
				}
				$out.= '</span>'."\n";
			}

			# And guest count on line
			if($online && ($online > count($members)))
			{
				$guests = ($online - count($members));
				if ($guests == 1)
				{
					$glabel = ' '.__("Guest", "sforum");
				} else {
					$glabel = $label;
				}
				$out.= '<br /><span class="sfstatsonlineguest">'.$guests.$glabel.'</span>'."\n";
			}

			if(($members || $online && ($online > count($members))))
			{
				$out.= '</span></p>'."\n";
			}

			# Members and guests browsing?
			$guestbrowsing = sf_guests_browsing();
			if($browse || $guestbrowsing)
			{
				$type = ucfirst($sfvars['pageview']);
				$type = __($type, "sforum");
				$out.= '<p><span><span class="sfstatsonlinecurrent"><strong>'.sprintf(__("Currently Browsing this %s", "sforum"), $type).': </strong></span>'."\n";
			}
			if($browse)
			{
				$out.= '<span class="sfstatsonlineuser">'.$browse.'</span>';
			}

			if($guestbrowsing != 0)
			{
				if ($guestbrowsing == 1)
				{
					$glabel = ' '.__("Guest", "sforum");
				} else {
					$glabel = $label;
				}
				$out.='<br /><span class="sfstatsonlineguest">'.$guestbrowsing.$glabel.'</span>'."\n";
			}
			if($browse || $guestbrowsing)
			{
				$out.= '</span></p>'."\n";
			}
		}
		$out.= '</td>'."\n";
	}
	return $out;
}
endif;

# = STATISTICS STRIP SUPPORT ROUTINE===========
if(!function_exists('sf_render_forum_stats')):
function sf_render_forum_stats($dashboard=false)
{
	global $sfglobals;

	$out='';
	if($sfglobals['display']['stats']['forumstats'] || $dashboard)
	{
		$cnt = sf_get_stats_counts();
		$out = '<td class="sfstatscounts">'."\n";
		$out.= '<p class="sfstatstitle"><strong>'.__("Forum Stats: ", "sforum").'</strong></p>'."\n";
		$out.= '<p><span class="sfstatsgroups">'.__("Groups: ", "sforum").$cnt->groups.'</span><br />'."\n";
		$out.= '<span class="sfstatsforums">'.__("Forums: ", "sforum").$cnt->forums.'</span><br />'."\n";
		$out.= '<span class="sfstatstopics">'.__("Topics: ", "sforum").$cnt->topics.'</span><br />'."\n";
		$out.= '<span class="sfstatsposts">'.__("Posts: ", "sforum").$cnt->posts.'</span></p>'."\n";

		$out.= '</td>'."\n";
	}
	return $out;
}
endif;

# = STATISTICS STRIP SUPPORT ROUTINE===========
if(!function_exists('sf_render_member_stats')):
function sf_render_member_stats($stats, $dashboard=false)
{
	global $sfglobals;

	$out='';

	if($sfglobals['display']['stats']['memberstats'] || $dashboard)
	{
		$sfcontrols = sf_get_option('sfcontrols');

		$out = '<td class="sfstatsmembers">'."\n";
		$out.= '<p class="sfstatsmembership"><strong>'.__("Membership:", "sforum").'</strong></p>'."\n";

		$out.= '<p>';

		$membercount = $sfcontrols['membercount'];
		if(empty($membercount)) $membercount = 0;
		if($membercount != 0)
		{
			if ($membercount == 1)
			{
				$out.= '<span class="sfstatsmembercount">'.__("There is 1 Member", "sforum").'</span><br />'."\n";
			} else {
				$out.= '<span class="sfstatsmembercount">'.sprintf(__("There are %s Members", "sforum"), $membercount).'</span><br />'."\n";
			}
		}

		$guestcount = $sfcontrols['guestcount'];
		if(empty($guestcount)) $guestcount = 0;
		if($guestcount != 0)
		{
			if ($guestcount == 1)
			{
				$out.='<span class="sfstatsguestcount">'.__("There has been 1 Guest", "sforum").'</span><br />'."\n";
			} else {
				$out.='<span class="sfstatsguestcount">'.sprintf(__("There have been %s Guests", "sforum"), $guestcount).'</span><br />'."\n";
			}
		}

		$out.= '</p>';
		$out.= '<p>';

		$admins = 0;
		$mods = 0;
		if($stats)
		{
			foreach($stats as $stat)
			{
				if($stat->admin) $admins++;
				if($stat->moderator) $mods++;
			}
		}

		if($admins != 0)
		{
			if ($admins == 1)
			{
				$out.='<span class="sfstatsadmincount">'.__("There is 1 Admin", "sforum").'</span><br />'."\n";
			} else {
				$out.='<span class="sfstatsadmincount">'.sprintf(__("There are %s Admins", "sforum"), $admins).'</span><br />'."\n";
			}
		}

		if($mods != 0)
		{
			if ($mods == 1)
			{
				$out.='<span class="sfstatsmodcount">'.__("There is 1 Moderator", "sforum").'</span>'."\n";
			} else {
				$out.='<span class="sfstatsmodcount">'.sprintf(__("There are %s Moderators", "sforum"), $mods).'</span>'."\n";
			}
		}

		$out.= '</p></td>';
	}

	if($sfglobals['display']['stats']['topposters'] || $dashboard)
	{
		if ($stats)
		{
			$out.= '<td class="sfstatstop">'."\n";
			$out.= '<p><strong>'.__("Top Posters:", "sforum").'</strong></p>'."\n";
			$done = 0;

			$out.= '<p>';
			foreach ($stats as $stat)
			{
				if($stat->admin == false && $stat->moderator == false && $stat->posts > 0)
				{
					$out.= '<span class="sfstatstopname">'.sf_build_name_display($stat->user_id, sf_filter_name_display($stat->display_name), true).' - '.$stat->posts.'</span><br />';
					$done++;
				}
			}
			$out.= '</p></td>'."\n";
		}
	}

	return $out;
}
endif;

# = STATISTICS STRIP NEW USERS================
if(!function_exists('sf_render_newusers')):
function sf_render_newusers()
{
	global $sfglobals;

	$out = '';

	if($sfglobals['display']['stats']['newusers'])
	{
		$sfcontrols=sf_get_option('sfcontrols');
		$newuserlist = $sfcontrols['newuserlist'];
		if($newuserlist)
		{
			$out.= '<tr class="sfstatsnew"><td colspan="4">';
			$done = false;
			$out.= '<p class="sfstatsadmins">';
			foreach($newuserlist as $user)
			{
				if($done == false)
				{
					$out.= '<strong>'.__("Recent New Members: ", "sforum").'</strong>';
				} else {
					$out.= ', ';
				}
				$out.= sf_build_name_display($user['id'], sf_filter_name_display($user['name']), true);
				$done = true;
			}
			$out.='</p></td></tr>';
		}
	}
	return $out;
}
endif;

# = STATISTICS STRIP ADMIN/MODS================
if(!function_exists('sf_render_ownership')):
function sf_render_ownership($stats, $dashboard=false)
{
	global $sfglobals;

	$out = '';

	if($sfglobals['display']['stats']['admins'] || $dashboard)
	{
		if($stats)
		{
			$out.= '<tr class="sfstatsfooter"><td colspan="4">';

			$done = false;
			$out.= '<p class="sfstatsadmins">';
			foreach($stats as $stat)
			{
				if($stat->admin == true)
				{
					if($done == false)
					{
						$out.= '<strong>'.__("Administrators: ", "sforum").'</strong>';
					} else {
						$out.= ', ';
					}
					$label = __("Posts", "sforum");
       				if ($stat->posts < 0) $stat->posts = 0;
					if ($stat->posts == 1) $label = __("Post", "sforum");
					$out.= sf_build_name_display($stat->user_id, sf_filter_name_display($stat->display_name), true).' ('.$stat->posts.' '.$label.')';
					$done = true;
				}
			}
			$out.='</p>';
			$done = false;
			$out.= '<p class="sfstatsmods">';
			foreach($stats as $stat)
			{
				if($stat->moderator == true)
				{
					if($done == false)
					{
						$out.= '<strong>'.__("Moderators: ", "sforum").'</strong>';
					} else {
						$out.= ', ';
					}
					$label = __("Posts", "sforum");
       				if ($stat->posts < 0) $stat->posts = 0;
					if ($stat->posts == 1) $label = __("Post", "sforum");
					$out.= sf_build_name_display($stat->user_id, sf_filter_name_display($stat->display_name), true).' ('.$stat->posts.' '.$label.')';
					$done = true;
				}
			}
			$out.='</p></td></tr>';
		}
	}
	return $out;
}
endif;

# = FORUM QUICKLINKS ==========================
if(!function_exists('sf_render_forum_quicklinks')):
function sf_render_forum_quicklinks()
{
	$out = sf_render_group_forum_select(true, true, true, true);

	return $out;
}
endif;

# = LATEST POST QUICKLINKS ====================
if(!function_exists('sf_render_newpost_quicklinks')):
function sf_render_newpost_quicklinks($show)
{
	global $current_user;

	$space = '&nbsp;&nbsp;';

	$sfposts = sf_get_users_new_post_list($show);

	if($sfposts)
	{
		$sfposts = sf_combined_new_posts_list($sfposts, true);
	 	# just return if viewable posts is empty
		if ($sfposts == '') return '';

		$out = '<select class="sfquicklinks sfcontrol" name="sfquicklinksPost" id="sfquicklinksPost" size="1" onchange="javascript:sfjchangeURL(this)">'."\n";
		$out.= '<option>'.__("New/Recently Updated Topics", "sforum").'</option>'."\n";
		$thisforum = 0;
		$group = false;

		if($sfposts)
		{
			foreach($sfposts as $sfpost)
			{
				if($sfpost['forum_id'] != $thisforum)
				{
					if($group)
					{
						$out.= '</optgroup>';
					}
					$name = sf_filter_title_display($sfpost['forum_name']);
					if(strlen($name) > 35) $name = substr($name, 0, 35).'...';
					$out.= '<optgroup class="sflist" label="'.$space.sf_create_name_extract(sf_filter_title_display($sfpost['forum_name'])).'">'."\n";
					$thisforum = $sfpost['forum_id'];
					$group = true;
				}
				$topicicon = '&nbsp;&nbsp;';
				$class = '';

				if($current_user->member && $current_user->ID != $sfpost['user_id'])
				{
					if($current_user->forumadmin || $current_user->moderator)
					{
						if($sfpost['post_status'] == 1)
						{
							$topicicon = '&bull;';
							$class = 'class="sfmod"';
						} elseif(sf_is_in_users_newposts($sfpost['topic_id']))
						{
							$topicicon = '&bull;';
							$class = 'class="sfnew"';
						}
					} else {
						if($current_user->member)
						{
							if(sf_is_in_users_newposts($sfpost['topic_id']))
							{
								$topicicon = '&bull;';
								$class = 'class="sfnew"';
							}
						} else {
							if(($current_user->lastvisit > 0) && ($current_user->lastvisit < $sfpost['udate']))
							{
								$topicicon = '&bull;';
								$class = 'class="sfnew"';
							}
						}
					}
				}

				$name = sf_filter_title_display($sfpost['topic_name']);
				if(strlen($name) > 35) $name = substr($name, 0, 35).'...';

				$out.='<option '.$class.' value="'.sf_build_url($sfpost['forum_slug'], $sfpost['topic_slug'], 0, $sfpost['post_id'], $sfpost['post_index']).'">'.$topicicon.sf_create_name_extract(sf_filter_title_display($sfpost['topic_name'])).'</option>'."\n";
			}
		}
		$out.= '</optgroup>';
		$out.='</select>'."\n";
	}
	return $out;
}
endif;

# = ADMINS QUICKLINKS =========================
if(!function_exists('sf_render_admin_quicklinks')):
function sf_render_admin_quicklinks($source, $pageview)
{
    $site = SFHOMEURL."index.php?sf_ahah=adminlinks&action=manage";
	$out = '<a rel="nofollow" class="sficon sfalignright sfquickadmin" href="'.$site.'" onclick="return hs.htmlExpand(this, { objectType: \'ajax\', preserveContent: true, width: 250, height: 450, anchor: \'top\'} )"><img src="'.SFRESOURCES.'manage.png" alt="" />'.sf_render_icons("Manage").'</a>';

	return $out;
}
endif;


function sf_render_user_permissions()
{
	global $sfvars, $current_user;

    $site = SFHOMEURL."index.php?sf_ahah=permissions&forum=".$sfvars['forumid']."&forumname=".$sfvars['forumname']."&user=".$current_user->ID."&displayname=".$current_user->display_name;
	$out = '<a rel="nofollow" href="'.$site.'" onclick="return hs.htmlExpand(this, { objectType: \'ajax\', preserveContent: true, width: 650, height: 570, anchor: \'top\'} )"><img src="'.SFRESOURCES.'user-permissions.png" alt="" title="'.esc_attr(__("Your Permissions", "sforum")).'"/></a>';
	return $out;
}

# = VERSION/ACKNOWLEDGEMENT STRIP =============
function sf_render_version_strip()
{

/*--------------------------------------------------------------------------------------------
	This plugin is provided free for anyone to use. We have spent an enormous amount of my time
	and, indeed, money, creating, maintaining, and continuing development of this software.
	The least that you, the user, could do to recognise that, is to leave this copyright strip
	in place which at the very least enables a link back to our site. Thank you.
--------------------------------------------------------------------------------------------*/

    $out = '';

	$sfpolicy = sf_get_option('sfpolicy');
	$width=' width="100%"';
	if($sfpolicy['sfreglink'] && $sfpolicy['sfprivlink'])
	{
		$width=' width="33%"';
	} else {
		if($sfpolicy['sfreglink'] || $sfpolicy['sfprivlink'])
		{
			$width=' width="50%"';
		}
	}

	$out.= '<div id="sfversion">';
	$out.= '<table width="100%"><tr>';

	# Registration Policy
	if($sfpolicy['sfreglink'])
	{
		$site = SFHOMEURL."index.php?sf_ahah=regpolicy&popup=reg";
		$out.= '<td'.$width.'>';
		$out.= '<a rel="nofollow" href="'.$site.'" onclick="return hs.htmlExpand(this, { objectType: \'ajax\', preserveContent: true, reflow: true, width: 500} )">'.esc_attr(__("Registration Policy", "sforum")).'&nbsp;&nbsp;<img class="sficon" src="'.SFRESOURCES.'documents.png" alt="" title="'.esc_attr(__("Registration Policy", "sforum")).'" /></a>';
		$out.= '</td>';
	}

	# Version
    $site = SFHOMEURL."index.php?sf_ahah=acknowledge";
	$out.= '<td'.$width.'>&copy; '.SFPLUGHOME;
	$out.= '&nbsp;&nbsp;<a  rel="nofollow" href="'.$site.'" onclick="return hs.htmlExpand(this, { objectType: \'ajax\', preserveContent: true, reflow: true, width: 650} )"><img class="sficon" src="'.SFRESOURCES.'information.png" alt="" title="'.esc_attr(__("Acknowledgements", "sforum")).'" /></a>';
	$out.= '</td>';

	# Privacy Policy
	if($sfpolicy['sfprivlink'])
	{
		$site = SFHOMEURL."index.php?sf_ahah=privpolicy&popup=priv";
		$out.= '<td'.$width.'>';
		$out.= '<a rel="nofollow" href="'.$site.'" onclick="return hs.htmlExpand(this, { objectType: \'ajax\', preserveContent: true, reflow: true, width: 500} )">'.esc_attr(__("Privacy Policy", "sforum")).'&nbsp;&nbsp;<img class="sficon" src="'.SFRESOURCES.'documents.png" alt="" title="'.esc_attr(__("Privacy Policy", "sforum")).'" /></a>';
		$out.= '</td>';
	}

	$out.= '</tr></table>';
	$out.= '</div><br />'."\n";

	return $out;
}

function sf_render_topic_printicon($topicid)
{
   	$out = '<a class="sficon" href="javascript:void(0);" onclick="javscript:jQuery(\'#topic-'.$topicid.'\').jqprint({debug:true}); return false;"><img src="'.SFRESOURCES.'print.png" alt="" title="'.esc_attr(__("Print This Topic", "sforum")).'"/>'.sf_render_icons("Print this Topic").'</a>';

	return $out;
}

?>