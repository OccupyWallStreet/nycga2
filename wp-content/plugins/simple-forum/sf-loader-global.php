<?php
/*
Simple:Press
Global routines needed to start up plugin
$LastChangedDate: 2010-12-31 06:36:40 -0700 (Fri, 31 Dec 2010) $
$Rev: 5214 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# ------------------------------------------------------------------
# sfg_set_rewrite_rules()
# Setup the forum rewrite rules
# ------------------------------------------------------------------
function sfg_set_rewrite_rules ($rules)
{
	global $wp_rewrite;

	$slug = sf_get_option('sfslug');
	if ($wp_rewrite->using_index_permalinks())
	{
		$slugmatch ='index.php/'.$slug;
	} else {
		$slugmatch = $slug;
	}

    # private messaging
	$sf_rules[$slugmatch.'/private-messaging/?$'] = 'index.php?pagename='.$slug.'&sf_pm=view&sf_box=inbox';
	$sf_rules[$slugmatch.'/private-messaging/inbox/?$'] = 'index.php?pagename='.$slug.'&sf_pm=view&sf_box=inbox';
	$sf_rules[$slugmatch.'/private-messaging/sentbox/?$'] = 'index.php?pagename='.$slug.'&sf_pm=view&sf_box=sentbox';
	$sf_rules[$slugmatch.'/private-messaging/send/([^/]+)/?$'] = 'index.php?pagename='.$slug.'&sf_pm=send&sf_box=inbox&sf_member=$matches[1]';

    # policy page
	$sf_rules[$slugmatch.'/policy/?$'] = 'index.php?pagename='.$slug.'&sf_policy=show';

    # admin new posts list
	$sf_rules[$slugmatch.'/newposts/?$'] = 'index.php?pagename='.$slug.'&sf_newposts=all';

    # members list?
	$sf_rules[$slugmatch.'/members/?$'] = 'index.php?pagename='.$slug.'&sf_list=members';
	$sf_rules[$slugmatch.'/members/page-([0-9]+)/?$'] = 'index.php?pagename='.$slug.'&sf_list=members&sf_page=$matches[1]';

    # match profile?
	$sf_rules[$slugmatch.'/profile/?$'] = 'index.php?pagename='.$slug.'&sf_profile=edit';
	$sf_rules[$slugmatch.'/profile/permissions/?$'] = 'index.php?pagename='.$slug.'&sf_profile=permissions';
	$sf_rules[$slugmatch.'/profile/buddies/?$'] = 'index.php?pagename='.$slug.'&sf_profile=buddies';
	$sf_rules[$slugmatch.'/profile/([^/]+)/?$'] = 'index.php?pagename='.$slug.'&sf_profile=show&sf_member=$matches[1]';
	$sf_rules[$slugmatch.'/profile/([^/]+)/edit/?$'] = 'index.php?pagename='.$slug.'&sf_profile=edit&sf_member=$matches[1]';

    # match forum and topic with pages
	$sf_rules[$slugmatch.'/([^/]+)/?$'] = 'index.php?pagename='.$slug.'&sf_forum=$matches[1]';
	$sf_rules[$slugmatch.'/([^/]+)/page-([0-9]+)/?$'] = 'index.php?pagename='.$slug.'&sf_forum=$matches[1]&sf_page=$matches[2]';
	$sf_rules[$slugmatch.'/([^/]+)/([^/]+)/?$'] = 'index.php?pagename='.$slug.'&sf_forum=$matches[1]&sf_topic=$matches[2]';
	$sf_rules[$slugmatch.'/([^/]+)/([^/]+)/page-([0-9]+)/?$'] = 'index.php?pagename='.$slug.'&sf_forum=$matches[1]&sf_topic=$matches[2]&sf_page=$matches[3]';

	$rules = array_merge($sf_rules, $rules);

	return $rules;
}

# ------------------------------------------------------------------
# sfg_set_query_vars()
# Setup the forum query variables
# ------------------------------------------------------------------
function sfg_set_query_vars($vars)
{
    # forums and topics
	$vars[] = 'sf_forum';
	$vars[] = 'sf_topic';
	$vars[] = 'sf_page';

    # ahah handler
	$vars[] = 'sf_ahah';

    # private messaging
	$vars[] = 'sf_pm';
	$vars[] = 'sf_box';

    # members list
	$vars[] = 'sf_list';

    # policy page
	$vars[] = 'sf_policy';

    # admin newposts list
	$vars[] = 'sf_newposts';

    # profile
	$vars[] = 'sf_profile';
	$vars[] = 'sf_member';

	return $vars;
}

# ------------------------------------------------------------------
# sfg_front_page_redirect()
#
# gets around the default canonical url behaviour when the
# forum is set to be the front page of the site - normally the
# ful url wold be discarded leaving just the home url.
# ------------------------------------------------------------------
function sfg_front_page_redirect($redirect)
{
	global $wp_query;

	if($wp_query->is_page)
	{
		if(isset($wp_query->queried_object) && 'page' == get_option('show_on_front') && $wp_query->queried_object->ID == get_option('page_on_front'))
		{
			if(sf_get_option('sfpage') == get_option('page_on_front'))
			{
				return false;
			}
		}
	}
	return $redirect;
}

# ------------------------------------------------------------------
# sfg_update_permalink()
#
# Updates the forum permalink. Called from plugin activation and
# upon each display of a forum admin page. If the permalink is
# found to have changed the rewrite rules are also flushed
# ------------------------------------------------------------------
function sfg_update_permalink($autoflush=false)
{
	global $wpdb, $wp_rewrite;

	$slug = sf_get_option('sfslug');

	if($slug)
	{
		$sfperm = sf_get_option('sfpermalink');

		$pageid = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '".$slug."' AND post_status='publish' AND post_type='page'");
		if ($pageid)
		{
			sf_update_option('sfpage', $pageid);
			$perm = get_permalink($pageid);
			if(get_option('page_on_front') == $pageid && get_option('show_on_front') == 'page')
			{
				$perm = rtrim($perm, '/');
				$perm.= '/'.$slug;
			}
			# only update it if base permalink has been changed
			if($sfperm != $perm)
			{
				sf_update_option('sfpermalink', $perm);
				$sfperm = $perm;
				$autoflush = true;
			}
		}
	}
	if($autoflush)
	{
		$wp_rewrite->flush_rules();
	}

	return $sfperm;
}

# ------------------------------------------------------------------
# sfg_permalink_changed()
#
# Triggered by permalink changed action passing in old and new
# ------------------------------------------------------------------
function sfg_permalink_changed($old, $new)
{
	global $wp_rewrite;

	if(empty($new))
	{
		$perm = SFHOMEURL . '?page_id=' . sf_get_option('sfpage');
		sf_update_option('sfpermalink', $perm);
	} else {
		$perm = SFHOMEURL . sf_get_option('sfslug') . '/';
		sf_update_option('sfpermalink', $perm);
		$wp_rewrite->flush_rules();
	}
}

# ------------------------------------------------------------------
# sfg_localisation()
# Setup the forum localisation
# ------------------------------------------------------------------
function sfg_localisation()
{
	# i18n support
	load_plugin_textdomain('sforum', SF_PLUGIN_DIR.'/languages', 'simple-forum/languages');
	return;
}

# ------------------------------------------------------------------
# sfg_feed()
# Redirects RSS feed requests
# ------------------------------------------------------------------
function sfg_feed()
{
	if(isset($_GET['xfeed']))
	{
		include SF_PLUGIN_DIR.'/feeds/sf-feeds.php';
		exit;
	}
}

# ------------------------------------------------------------------
# sfg_404()
# notes....
# ------------------------------------------------------------------
function sfg_404()
{
	if(is_404())
	{
		$sfcontrols = sf_get_option('sfcontrols');
		if(strpos($_SERVER['REQUEST_URI'], sf_get_option('sfslug'), 0) && $sfcontrols['fourofour'] == false)
		{
			$sfcontrols['fourofour'] = true;
			sf_update_option('sfcontrols', $sfcontrols);
			sfg_update_permalink(true);
			wp_redirect($_SERVER['REQUEST_URI']);
		}
	}
}

# ------------------------------------------------------------------
# sfg_setup_browser_title()
#
# Filter call
# Sets up the browser page title
#	$title		page title
# ------------------------------------------------------------------
function sfg_setup_browser_title($title)
{
	global $wp_query;

	$post = $wp_query->get_queried_object();
    if (isset($post->ID) && $post->ID == sf_get_option('sfpage'))
	{
    	include_once (SF_PLUGIN_DIR.'/sf-header-forum.php');
		sf_populate_query_vars();
		sf_setup_page_type();

		$sfseo = sf_get_option('sfseo');
		$title = sf_setup_title($title, ' '.$sfseo['sfseo_sep'].' ');
	}

	return $title;
}

# ******************************************************************
# COMPLETE TABLE AND DATA REMOVAL
# ******************************************************************

# ------------------------------------------------------------------
# sfg_remove_data()
#
# Removes all forum data prior to deactivation
# ------------------------------------------------------------------
function sfg_remove_data()
{
	global $wpdb;

	if(sf_get_option('sfuninstall'))
	{
		# remove any admin capabilities
		$admins = $wpdb->get_results("SELECT user_id FROM ".SFMEMBERS." WHERE admin=1");
		foreach ($admins as $admin)
		{
			$user = new WP_User($admin->user_id);
			$user->remove_cap('SPF Manage Options');
			$user->remove_cap('SPF Manage Forums');
			$user->remove_cap('SPF Manage User Groups');
			$user->remove_cap('SPF Manage Permissions');
			$user->remove_cap('SPF Manage Tags');
			$user->remove_cap('SPF Manage Components');
			$user->remove_cap('SPF Manage Admins');
			$user->remove_cap('SPF Manage Profiles');
			$user->remove_cap('SPF Manage Users');
			$user->remove_cap('SPF Manage Toolbox');
			$user->remove_cap('SPF Manage Configuration');
		}

		# First remove tables
		$wpdb->query("DROP TABLE IF EXISTS ".SFGROUPS);
		$wpdb->query("DROP TABLE IF EXISTS ".SFFORUMS);
		$wpdb->query("DROP TABLE IF EXISTS ".SFTOPICS);
		$wpdb->query("DROP TABLE IF EXISTS ".SFPOSTS);
		$wpdb->query("DROP TABLE IF EXISTS ".SFWAITING);
		$wpdb->query("DROP TABLE IF EXISTS ".SFTRACK);
		$wpdb->query("DROP TABLE IF EXISTS ".SFSETTINGS);
		$wpdb->query("DROP TABLE IF EXISTS ".SFNOTICE);
		$wpdb->query("DROP TABLE IF EXISTS ".SFMESSAGES);
		$wpdb->query("DROP TABLE IF EXISTS ".SFUSERGROUPS);
		$wpdb->query("DROP TABLE IF EXISTS ".SFPERMISSIONS);
		$wpdb->query("DROP TABLE IF EXISTS ".SFROLES);
		$wpdb->query("DROP TABLE IF EXISTS ".SFMEMBERS);
		$wpdb->query("DROP TABLE IF EXISTS ".SFMEMBERSHIPS);
		$wpdb->query("DROP TABLE IF EXISTS ".SFMETA);
		$wpdb->query("DROP TABLE IF EXISTS ".SFPOSTRATINGS);
		$wpdb->query("DROP TABLE IF EXISTS ".SFDEFPERMISSIONS);
		$wpdb->query("DROP TABLE IF EXISTS ".SFTAGS);
		$wpdb->query("DROP TABLE IF EXISTS ".SFTAGMETA);
		$wpdb->query("DROP TABLE IF EXISTS ".SFLOG);
		$wpdb->query("DROP TABLE IF EXISTS ".SFOPTIONS);
		$wpdb->query("DROP TABLE IF EXISTS ".SFLINKS);

		# Remove the Page record and any blog links
		$sfpage = sf_get_option('sfpage');
		if(!empty($sfpage))
		{
			$wpdb->query("DELETE FROM ".$wpdb->prefix."posts WHERE ID=".sf_get_option('sfpage'));
			$wpdb->query("DELETE FROM ".$wpdb->prefix."postmeta WHERE post_id=".sf_get_option('sfpage'));
		}

		# remove widget data
		delete_option('widget_spf');
		delete_option('widget_sforum');

		# remove cron jobs
		wp_clear_scheduled_hook('spf_cron_pm');
		wp_clear_scheduled_hook('spf_cron_user');
		wp_clear_scheduled_hook('spf_cron_sitemap');

		# Now remove user meta data
		$optionlist = array(
			"sfadmin",
			"location",
			"msn",
			"skype",
			"icq",
			"facebook",
			"myspace",
			"twitter",
			"linkedin",
			"sfuse_quicktags",
			"signature",
			"sigimage"
		);

		foreach($optionlist as $option)
		{
			$wpdb->query("DELETE FROM ".SFUSERMETA." WHERE meta_key='".$option."';");
		}
	}
	return;
}

?>