<?php
/*
Simple:Press
Template Tag(s) - General
$LastChangedDate: 2011-03-06 17:03:08 -0700 (Sun, 06 Mar 2011) $
$Rev: 5639 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

/* 	=====================================================================================

	sf_sidedash_tag()

	Allows display of a common SPF dashboard on pages

	parameters:

		show_avatar		display user avatar						true/false								true
		show_pm			display pm template tag					true/false								true
		redirect		controls login/logout redirection		1=home, 2=admin, 3=cur page, 4=forum 	4
		show_admin_link	display link to admin dashboard			true/false								true
		show_login_link	display login form and lost pw link		true/false								true
 	===================================================================================*/

function sf_sidedash_tag($show_avatar=true, $show_pm=true, $redirect=4, $show_admin_link=true, $show_login_link=true)
{
	include_once (SF_PLUGIN_DIR.'/template-tags/sf-template-tags-pm.php');
	include_once (SF_PLUGIN_DIR.'/template-tags/sf-template-tags-avatars.php');

	global $current_user, $sfvars, $sfglobals;

	sf_initialise_globals($sfvars['forumid']);

	$sflogin = sf_get_option('sflogin');

	if ($redirect == 1)
	{
		$redirect_to = SFHOMEURL;
	} else if ($redirect == 2) {
		$redirect_to = admin_url();
	} else if ($redirect == 3) {
		$redirect_to = $_SERVER['REQUEST_URI'];
	} else {
		$redirect_to = SFURL;
	}

	if($current_user->guest)
	{
	    # are we showing login form and lost password
		if ($show_login_link)
		{
			# display login form
			echo '<form action="'.SFSITEURL.'wp-login.php?action=login" method="post">'."\n";
			echo '<div class="sftagusername"><label for="sftaglog">'.__("Username: ", "sforum").'<input type="text" name="log" id="sftaglog" value="" size="15" /></label></div>'."\n";
			echo '<div class="sftagpassword"><label for="sftagpwd">'.__("Password: ", "sforum").'<input type="password" name="pwd" id="sftagpwd" value="" size="15"  /></label></div>'."\n";
			echo '<div class="sftagremember"><input type="checkbox" id="rememberme" name="rememberme" value="forever" /><label for="rememberme">'.__("Remember me", "sforum").'</label></div>';
			echo '<input type="submit" name="submit" id="submit" value="'.esc_attr(__("Login", "sforum")).'" />'."\n";
			echo '<input type="hidden" name="redirect_to" value="'.esc_attr($redirect_to).'" />'."\n";
			echo '</form>'."\n";
			echo '<p class="sftagguest"><a href="'.$sflogin['sflostpassurl'].'">'.__("Lost Password", "sforum").'</a>'."\n";

		    # if registrations allowed, display register link
			if (TRUE == get_option('users_can_register') && !$sfglobals['lockdown'])
			{
    			$sfpolicy = sf_get_option('sfpolicy');
    			if($sfpolicy['sfregtext'])
    			{
    				echo '<br /><a href="'.SFREGPOLICY.'">'.__("Register", "sforum").'</a></p>'."\n";
    			} else {
    				echo '<br /><a href="'.$sflogin['sfregisterurl'].'">'.__("Register", "sforum").'</a></p>'."\n";
    			}
            }
		}
	} else {
		echo '<div class="sftagavatar">'.sf_show_avatar().'</div>';
		echo '<p class="sftag-loggedin">'.__("Logged in as", "sforum").' <strong>'.sf_filter_name_display($current_user->display_name).'</strong></p>'."\n";
		if ($show_pm)
		{
			sf_pm_tag(true, false);
		}
		if ($show_admin_link)
		{
			echo '<p class="sftag-admin"><a href="'.admin_url().'">'.__('Dashboard', "sforum").'</a></p>';
		}
		echo '<p class="sftag-logout"><a href="'.wp_nonce_url(SFSITEURL.'wp-login.php?action=logout&amp;redirect_to='.esc_attr($redirect_to), 'log-out').'">'.__('Logout', "sforum").'</a></p>'."\n";
	}
}


/* 	=====================================================================================

	sf_admin_mod_status($mod=true, $custom=true)

	displays online status of admins and moderators

	parameters:

		$mod			Display moderator status				true/false		true (default)
		$custom			Display custom status text if set		true/false		true (default)

 	===================================================================================*/

function sf_admin_mod_status($mod=true, $custom=true)
{
	global $wpdb, $current_user, $sfvars;

	if(!defined('SFTRACK')) {
		sf_setup_sitewide_constants();
	}
	if(!defined('SFRESOURCES')) {
		sf_setup_global_constants();
	}

	sf_initialise_globals($sfvars['forumid']);

	$out = "\n";

	if ($mod) $where = ' OR moderator = 1';
	$admins = $wpdb->get_results("SELECT user_id, display_name, admin_options FROM ".SFMEMBERS." WHERE admin = 1".$where);
	if ($admins)
	{
		foreach ($admins as $admin)
		{
			$username = sf_build_name_display($admin->user_id, sf_filter_name_display($admin->display_name));
			$out.= '<li class="sfadmin-onlinestatus'."\n";
			$status = $wpdb->get_var("SELECT id FROM ".SFTRACK." WHERE trackuserid=".$admin->user_id);
			$opts = sf_get_member_item($admin->user_id, 'user_options');
           	$sfmemberopts = sf_get_option('sfmemberopts');
			if (($current_user->forumadmin || (!$sfmemberopts['sfhidestatus'] || !$opts['hidestatus'])) && sf_is_online($admin->user_id))
			{
				$out.= ' sfadmin-online"><img class="sfonline-icon" src="'.SFRESOURCES.'online.png" alt="" title="'.esc_attr(__("On-Line", "sforum")).'" />'.$username."\n";
			} else {
				$out.= ' sfadmin-offline"><img class="sfonline-icon" src="'.SFRESOURCES.'offline.png" alt="" title="'.esc_attr(__("Off-Line", "sforum")).'" />'.$username."\n";
				if ($custom)
				{
					$options = unserialize($admin->admin_options);
					if (isset($options['sfstatusmsgtext']))
					{
						$msg = sf_filter_text_display($options['sfstatusmsgtext']);
						if ($msg != '')
						{
							$out.= '<span class="sfcustom-onlinestatus">'.$msg.'</span>'."\n";
						}
					}
				}
			}
			$out.= '</li>'."\n";
		}
	} else {
		$out.= '<li class="sfadmin-onlinestatus">'."\n";
		$out.= __("No Admins or Moderators", "sforum")."\n";
		$out.= '</li>'."\n";
	}

	echo $out;
	return;
}

/* 	=====================================================================================

	sf_new_posts_tag($unreadmsg='', $nonemsg='')

	Displays a message if the current user has unread posts. If a message is not supplied
	a default one is used

	parameters:

		$unreadmsg			message to display when unread			text		optional
		$nonemsg			message to display when no uread		text		optional
 	===================================================================================*/

function sf_new_posts_tag($unreadmsg='', $nonemsg='')
{
	global $current_user, $sfglobals, $sfvars;

	sf_initialise_globals($sfvars['forumid']);

	if ($current_user->member)
	{
		$count = 0;
		$newpostlist = $sfglobals['member']['newposts'];
		if($newpostlist['topics'][0] == 0 || empty($newpostlist))
		{
			sf_construct_users_newposts(true);
		}
		if($newpostlist['topics'][0] != 0) $count = count($newpostlist['topics']);

		if ($count != 0)
		{
			if ($unreadmsg != '')
			{
				echo $unreadmsg;
			} else {
				echo sprintf(__('You have %s Topics with Unread Posts in the ', 'sforum'), $count).' <a href="'.SFURL.'">'.__('Forum', 'sforum').' </a>';
			}
		} else {
			if ($nonemsg = '')
			{
				echo $nonemsg;
			} else {
				echo __('You have no Unread Posts in the ', 'sforum').' <a href="'.SFURL.'">'.__('Forum', 'sforum').' </a>';
			}
		}
	}
	return;
}

/* 	=====================================================================================

	sf_subscribed_topics_tag($display)

	template tag to display number of unread subscribed topics.  This tag includes
	default text that is output with the unread count data.   This text can
	be suppressed by setting $display to false. If suppressed, the new unread count is returned
    to the caller. Nothing is displayed and 0 returned for guests.

	parameters:

		$display		Determines whether to display unread count plus informational text

 	===================================================================================*/

function sf_subscribed_topics_tag($display=true)
{
	global $current_user, $sfglobals, $sfvars;

	sf_initialise_globals($sfvars['forumid']);

	$count = 0;
	if ($current_user->member)
	{
		$list = $sfglobals['member']['subscribe'];
		if (!empty($list))
		{
			$newpostlist = $sfglobals['member']['newposts'];
			if($newpostlist['topics'][0] == 0 || empty($newpostlist))
			{
				sf_construct_users_newposts(true);
			}
			foreach ($list as $topicid)
			{
				if (sf_is_in_users_newposts($topicid)) $count++;
			}
		}
	}

	if ($display)
	{
		$out = '';
		if ($current_user->member)
		{
			$out .= '<p class="sfsubscribed">';
			$out .= __("You have", "sforum").' '.$count.' '.__("unread subscribed topics", "sforum").'.';
			$out .= '</p>';
		}
        echo $out;
	}

	return $count;
}

/* 	=====================================================================================

	sf_watched_topics_tag($display)

	template tag to display number of unread watched topics.  This tag includes
	default text that is output with the unread count data.   This text can
	be suppressed by setting $display to false. If suppressed, the new unread count is returned
    to the caller. Nothing is displayed and 0 returned for guests.

	parameters:

		$display		Determines whether to display unread count plus informational text

 	===================================================================================*/

function sf_watched_topics_tag($display=true)
{
	global $current_user, $sfglobals, $sfvars;

	sf_initialise_globals($sfvars['forumid']);

	$count = 0;
	if ($current_user->member)
	{
		$list = $sfglobals['member']['watches'];
		if (!empty($list))
		{
			$newpostlist = $sfglobals['member']['newposts'];
			if($newpostlist['topics'][0] == 0 || empty($newpostlist))
			{
				sf_construct_users_newposts(true);
			}
			foreach ($list as $topicid)
			{
				if (sf_is_in_users_newposts($topicid)) $count++;
			}
		}
	}

	if ($display)
	{
		$out = '';
		if ($current_user->member)
		{
			$out .= '<p class="sfwatched">';
			$out .= __("You have", "sforum").' '.$count.' '.__("unread watched topics", "sforum").'.';
			$out .= '</p>';
		}
        echo $out;
	}

	return $count;
}


?>