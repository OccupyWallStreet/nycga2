<?php
/*
Simple:Press
Forum Page Rendering
$LastChangedDate: 2011-04-03 12:38:44 -0700 (Sun, 03 Apr 2011) $
$Rev: 5802 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

function sf_render_page($pageview)
{
	global $sfvars, $current_user, $sfglobals;

	$out = '';

	sf_track_online();

	if($sfvars['searchpage'] && $sfvars['forumslug'] == 'all')
	{
		$pageview = 'searchall';
	}

	# Top of the forum - Display starts here
	$out.= sf_render_queued_message();

	$out.= "\n\n".'<!-- Start of SPF Container (sforum) -->'."\n\n";

    # do we want to clear the decks for the forum?
    if (sf_get_option('sffloatclear')) $out.= '<div class="sfclear"></div>'."\n";

    # begin forum display
	$out.= '<div id="sforum">'."\n";
	$out.= '<a id="forumtop">&nbsp;</a>'."\n";
	$out.= '<div id="sflogininfo"></div>'."\n";

	switch ($pageview)
	{
		case 'group':
			$out.= sf_process_hook('sf_hook_group_header', '');
			break;

		case 'forum':
			$out.= sf_process_hook('sf_hook_forum_header', '');
			break;

		case 'topic':
			$out.= sf_process_hook('sf_hook_topic_header', '');
			break;
	}

	# reduce unread counts if necessary
	if($pageview == 'topic')
	{
		sf_remove_from_waiting(false, $sfvars['topicid']);
		sf_remove_users_newposts($sfvars['topicid']);
		sf_update_opened($sfvars['topicid']);
	}

	$showqueueatbottom = false;
	$showqueueattop = false;
	$showstandardbottom = false;
	$showstandardtop = false;

	if ($current_user->forumadmin || $current_user->moderator)
	{
		$usertype = 'admin';

		# admin queue display
		if ($sfglobals['admin']['sfqueue'])
		{
			$newposts=array();
			$newposts = sf_get_admins_queued_posts();

			# check if the queue is up to 200 posts whi is dangerous - output message
			if(isset($sfvars['queue']) && $sfvars['queue'] > 199)
			{
				$out.= '<div class="sfmessagestrip">';
				$out.= '<p>'.sprintf(__("WARNING: The New Post Queue contains %s Posts which require removal", "sforum"), $sfvars['queue']).'</p>';
				$out.= '</div>';
			}

			if($sfglobals['member']['admin_options']['sfadminbar'])
			{
				# using admin queue and admin bar
				$out.= sf_render_admin_strip('forum', $pageview, $newposts);
			} else if ($sfglobals['member']['admin_options']['sfshownewadmin'] && $pageview == 'group') {
				# using admin queue, but not admin bar, so display at top or bottom
				if($sfglobals['display']['forums']['newabove'])
				{
					$showqueueattop = true;
				} else {
					$showqueueatbottom = true;
				}
			}
		}

		# new posts display for admin only if admn queue not at top or bottom
		if ($sfglobals['member']['admin_options']['sfshownewadmin'] && $pageview == 'group' && $showqueueattop == false && $showqueueatbottom == false)
		{
			# display new posts at top or bottom
			if ($sfglobals['display']['forums']['newabove'])
			{
				$showstandardtop = true;
			} else {
				$showstandardbottom = true;
			}
		}
	} else {
		$usertype = 'user';
		if ($pageview == 'group')
		{
			if($sfglobals['display']['forums']['newposts'])
			{
				if ($sfglobals['display']['forums']['newabove'])
				{
					$showstandardtop = true;
				} else {
					$showstandardbottom = true;
				}
			}
		}
	}

	if ($sfglobals['lockdown']) $out.= sf_render_lockdown();
	$out.= sf_render_login_strip('forum', $pageview, 'inbox');
	$out.= sf_render_login_form();

	# Hive off the display so far in case we need it - i.e., user has NO access to anything
	$header_display = $out;

	$searchcache = '';
	if($sfglobals['display']['search']['searchtop'] || $sfglobals['display']['search']['searchbottom'] || $sfglobals['display']['quicklinks']['qltop'] || $sfglobals['display']['quicklinks']['qlbottom'])
	{
		$topsearch = sf_render_searchbar($pageview);
		$searchcache = $topsearch;

		# Prepare and display top search bar
		if($sfglobals['display']['search']['searchtop'] || $sfglobals['display']['quicklinks']['qltop'])
		{
			if(!$sfglobals['display']['search']['searchtop']) $topsearch = str_replace('<div id="sftopsearch">', '<div class="sfhidebox">', $topsearch);
			if(!$sfglobals['display']['quicklinks']['qltop']) $topsearch = str_replace('<div id="sftopql">', '<div class="sfhidebox">', $topsearch);
			$out.= $topsearch;
		}

		$out.= sf_render_searchform($pageview);

		if($sfglobals['display']['search']['searchtop'] || $sfglobals['display']['quicklinks']['qltop'])
		{
			$out.= sf_process_hook('sf_hook_post_top_search', '');
		}

		# Prepare bottom search bar
		if(strpos($searchcache, '<div id="sfsearchform">'))
		{
			$searchcache = substr($searchcache, 0, strpos($searchcache, '<div id="sfsearchform">'));
		}

		if(!$sfglobals['display']['search']['searchbottom'])
		{
			$searchcache = str_replace('<div id="sftopsearch">', '<div class="sfhidebox">', $searchcache);
		} else {
			$searchcache = str_replace('<div id="sftopsearch">', '<div id="sfbottomsearch">', $searchcache);
			$searchcache = str_replace('<div class="sfhidebox">', '<div id="sfbottomsearch">', $searchcache);
		}

		if(!$sfglobals['display']['quicklinks']['qlbottom'])
		{
			$searchcache = str_replace('<div id="sftopql">', '<div class="sfhidebox">', $searchcache);
		} else {
			$searchcache = str_replace('<div id="sftopql">', '<div id="sfbottomql">', $searchcache);
			$searchcache = str_replace('<div id="sfqlposts">', '<div id="sfqlpostsbottom">', $searchcache);
			$searchcache = str_replace('id="sfquicklinksPost"', 'id="sfquicklinksPostBottom"', $searchcache);
		}
	}

	switch ($pageview)
	{
		case 'group':
			include_once (SF_PLUGIN_DIR.'/forum/sf-group-components.php');
			include_once (SF_PLUGIN_DIR.'/forum/sf-group-view.php');

			$out.= sf_render_breadcrumbs('', '', 0);

			if($showstandardtop)
			{
				include_once (SF_PLUGIN_DIR.'/forum/sf-forum-components.php');
				include_once (SF_PLUGIN_DIR.'/forum/sf-new-user-view.php');
				$out.= sf_render_new_post_list_user();
			}

			if($showqueueattop)
			{
				include_once (SF_PLUGIN_DIR.'/forum/sf-new-admin-view.php');
				$out.= sf_render_new_post_list_admin($newposts, false);
			}

			$group_out = sf_render_group();
			if($group_out == "Access Denied")
			{
				$out = $header_display;
				$out.= '<div class="sfmessagestrip">';
				$out.= apply_filters('sf_group_access_denied', __('Access Denied!', 'sforum'));
				$out.= '</div>'."\n";
				$out.= sf_render_version_strip();
				$out.= '</div>'."\n";
				return $out;
			} else {
				$out.= $group_out;
				$group_out = '';
				$header_display = '';
			}

			if($showstandardbottom)
			{
				include_once (SF_PLUGIN_DIR.'/forum/sf-forum-components.php');
				include_once (SF_PLUGIN_DIR.'/forum/sf-new-user-view.php');
				$out.= sf_render_new_post_list_user();
			}

			if($showqueueatbottom)
			{
				include_once (SF_PLUGIN_DIR.'/forum/sf-new-admin-view.php');
				$out.= sf_render_new_post_list_admin($newposts, false);
			}

			$out.= sf_render_bottom_iconstrip('all', $current_user->ID);
			break;

		case 'forum':
			include_once (SF_PLUGIN_DIR.'/forum/sf-forum-components.php');
			include_once (SF_PLUGIN_DIR.'/forum/sf-forum-view.php');
			$out.= sf_render_breadcrumbs($sfvars['forumslug'], '', $sfvars['page']);

			$forum_out = sf_render_forum();
			if($forum_out == "Access Denied")
			{
				$out = $header_display;
				if ($current_user->sfforumlistaccess || $current_user->sfforumlistaccess)
				{
					$sneakpeek = sf_get_sfmeta('sneakpeek', 'message');
					if ($sneakpeek)
					{
						$out.= '<div class="sfmessagestrip">';
						$out.= sf_filter_text_display($sneakpeek[0]['meta_value']);
						$out.= '</div>'."\n";
					}
				} else {
					$out.= '<div class="sfmessagestrip">';
				    $out.= apply_filters('sf_forum_access_denied', __('Access Denied!', 'sforum'));
					$out.= '</div>'."\n";
				}
				$out.= sf_render_version_strip();
				$out.= '</div>'."\n";
				return $out;
			} else {
				$out.= $forum_out;
				$forum_out = '';
				$header_display = '';
			}

			$out.= sf_render_bottom_iconstrip('forum', $current_user->ID);
			break;

		case 'topic':
			include_once (SF_PLUGIN_DIR.'/forum/sf-topic-components.php');
			include_once (SF_PLUGIN_DIR.'/forum/sf-topic-view.php');
			$out.= sf_render_breadcrumbs($sfvars['forumslug'], $sfvars['topicslug'], $sfvars['page']);

			$topic_out = sf_render_topic();
			if ($topic_out == "Access Denied")
			{
				$out = $header_display;
				if ($current_user->sftopiclistaccess)
				{
					$sneakpeek = sf_get_sfmeta('sneakpeek', 'message');
					if ($sneakpeek)
					{
						$out.= '<div class="sfmessagestrip">';
						$out.= sf_filter_text_display($sneakpeek[0]['meta_value']);
						$out.= '</div>'."\n";
					}
				} else {
					$out.= '<div class="sfmessagestrip">';
    				$out.= apply_filters('sf_topic_access_denied', __('Access Denied!', 'sforum'));
					$out.= '</div>'."\n";
				}
				$out.= sf_render_version_strip();
				$out.= '</div>'."\n";
				return $out;
			} else {
				$out.= $topic_out;
				$top1c_out = '';
				$header_display = '';
			}

			$out.= sf_render_bottom_iconstrip('topic', $current_user->ID);
			break;

		case 'newposts':
			$out.= sf_render_breadcrumbs(0, 0, 0);
            if (($current_user->moderator || $current_user->forumadmin) && $sfglobals['admin']['sfqueue']==true)
            {
    			include_once (SF_PLUGIN_DIR.'/forum/sf-new-admin-view.php');
                $out.=  sf_render_new_post_list_admin($newposts, false);
            } else {
				include_once (SF_PLUGIN_DIR.'/forum/sf-forum-components.php');
				include_once (SF_PLUGIN_DIR.'/forum/sf-new-user-view.php');
                $out.= sf_render_new_post_list_user();
            }
			break;

		case 'buddies':
			include_once (SF_PLUGIN_DIR.'/profile/sf-profile-control.php');
			$out.= sf_render_breadcrumbs(0, 0, 0);
			$out.= sf_view_buddylist();
			break;

		case 'permissions':
			include_once (SF_PLUGIN_DIR.'/profile/sf-profile-control.php');
			$out.= sf_render_breadcrumbs(0, 0, 0);
			$out.= sf_view_permissions();
			break;

		case 'searchall':
			include_once (SF_PLUGIN_DIR.'/forum/sf-forum-components.php');
			include_once (SF_PLUGIN_DIR.'/forum/sf-search-view.php');
			$out.= sf_render_breadcrumbs(0, 0, 0);
			$out.= sf_render_search_all();
			break;

		case 'profileedit':
			include_once (SF_PLUGIN_DIR.'/profile/sf-profile-control.php');
			$sfprofile=sf_get_option('sfprofile');
			if($sfprofile['forminforum'] == false)
			{
				$out='';
				return sf_render_profile();
			} else {
				$out.= sf_render_breadcrumbs(0, 0, 0);
				$out.= sf_render_profile();
			}
			break;

		case 'profileshow':
			include_once (SF_PLUGIN_DIR.'/profile/sf-profile-control.php');
			$sfprofile=sf_get_option('sfprofile');
			if($sfprofile['displayinforum'] == false)
			{
				$out='';
				return sf_render_profile();
			} else {
				$out.= sf_render_breadcrumbs(0, 0, 0);
				$out.= sf_render_profile();
			}
			break;

		case 'list':
			include_once (SF_PLUGIN_DIR.'/forum/sf-forum-components.php');
			include_once (SF_PLUGIN_DIR.'/forum/sf-list-components.php');
			$out.= sf_render_breadcrumbs(0, 0, 0);
			$out.= sf_render_list();
			break;

		case 'policy':
			include_once (SF_PLUGIN_DIR.'/forum/sf-forum-components.php');
			include_once (SF_PLUGIN_DIR.'/forum/sf-list-components.php');
			$out.= sf_render_breadcrumbs(0, 0, 0);
			$out.= sf_policy_form();
			break;
	}

	if($sfglobals['display']['search']['searchbottom'] || $sfglobals['display']['quicklinks']['qlbottom'])
	{
		$out.= $searchcache;
		$out.= sf_process_hook('sf_hook_post_bottom_search', '');
	}

	$out.= sf_render_stats();

	$out.= sf_process_hook('sf_hook_footer_inside', '');

	$out.= sf_render_version_strip();
	$out.= '<a id="forumbottom">&nbsp;</a>';

	$out.= "\n\n".'<!-- End of SPF Container (sforum) -->'."\n\n";
	$out.= '</div>'."\n";

	$out.= sf_process_hook('sf_hook_footer_outside', '');

	return $out;
}

?>