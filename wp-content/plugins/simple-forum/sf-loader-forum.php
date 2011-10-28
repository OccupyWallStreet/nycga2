<?php
/*
Simple:Press
forum frnbt end loader
$LastChangedDate: 2011-04-15 19:51:11 -0700 (Fri, 15 Apr 2011) $
$Rev: 5897 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# ------------------------------------------------------------------
# sf_setup_forum()
#
# Central Control of forum rendering
# Called by the_content filter
#	$content:	The page content
# ------------------------------------------------------------------
function sf_setup_forum($content)
{
	global $ISFORUM, $CONTENTLOADED, $sfvars, $current_user, $sfglobals, $wpdb, $SFSTATUS, $wp_query;

	if ($ISFORUM && !post_password_required(get_post(sf_get_option('sfpage'))))
	{
        # Limit forum display to within the wp loop?
    	if (sf_get_option('sfinloop') && !in_the_loop()) return $content;

		# Has forum content already been loaded and are we limiting?
		if (!sf_get_option('sfmultiplecontent') && $CONTENTLOADED) return $content;
		$CONTENTLOADED = true;

		# check installed version is correct (needed even though the same call is in startup!)
		if($SFSTATUS != 'ok')
		{
			return sf_forum_unavailable();
		}

		sf_clean_controls();

		# Is it a private message action?
		if (!empty($sfvars['pm']))
		{
			$pmview = "inbox";
			if (!empty($sfvars['box'])) $pmview = $sfvars['box'];

			$content = sf_display_banner() . sf_process_hook('sf_hook_pre_content', '') . wpautop($content);
			$content .= sf_process_hook('sf_hook_post_content', '');
			$content .= sf_js_check();

			$content.= sf_message_control($pmview);
			return $content;
		}

# ---------------------------------------------------------

# ---------------------------------------------------------

        # are we marking all posts as read?
		if(isset($_GET['sf-mark-read'])) sf_mark_all_read();

		# Approving a post and displaying it
		if(isset($_GET['sf-mod']))
		{
			sf_approve_post(true, 0, $sfvars['topicid']);
		}

		# removing a post from admins queue
		if(isset($_GET['mark']))
		{
			sf_remove_from_waiting(true, $sfvars['topicid'], 0);
		}

		# Edit post from manage
		if (isset($_POST['editpost'])) sf_save_edited_post();

		# Edit topic from manage
		if (isset($_POST['edittopic'])) sf_save_edited_topic();

		if (isset($_POST['icontoggle'])) sf_icon_toggle();

		# Manage topic admin icons
		if (isset($_POST['locktopic'])) sf_lock_topic_toggle(sf_esc_int($_POST['locktopic']));
		if (isset($_POST['pintopic'])) sf_pin_topic_toggle($_POST['pintopic']);
		if (isset($_POST['killtopic'])) sf_delete_topic(sf_esc_int($_POST['killtopic']));
		if (isset($_POST['linkbreak'])) sf_break_blog_link(sf_esc_int($_POST['linkbreak']), sf_esc_int($_POST['blogpost']));
		if (isset($_POST['maketopicmove'])) sf_move_topic();
		if (isset($_POST['makepostmove'])) sf_move_post();
		if (isset($_POST['makepostreassign'])) sf_reassign_post();
		if (isset($_POST['makestatuschange'])) sf_change_topic_status();
		if (isset($_POST['maketagsedit'])) sf_change_topic_tags(sf_esc_int(sf_esc_int($_POST['topicid'])), sf_esc_str($_POST['topictags']));

		# Manage post admin icons
		if (isset($_POST['approvepost'])) sf_approve_post(false, $_POST['approvepost'], $sfvars['topicid']);
		if (isset($_POST['pinpost'])) sf_pin_post_toggle($_POST['pinpost']);
		if (isset($_POST['killpost'])) sf_delete_post(sf_esc_int($_POST['killpost']), sf_esc_int($_POST['killposttopic']), sf_esc_int($_POST['killpostforum']), true, sf_esc_int($_POST['killpostposter']));

		# Maybe a call to rebuild indices
		if(isset($_POST['rebuildforum']) || isset($_POST['rebuildtopic']))
		{
			sf_build_forum_index(sf_esc_int($_POST['forumid']), false);
			sf_build_post_index(sf_esc_int($_POST['topicid']), sf_esc_str($_POST['topicslug']), true);
		}

		# Maybe a subscription call?
		if (isset($_GET['sf-subscribe']))
		{
			sf_save_subscription(sf_esc_int($_GET['topic']), $current_user->ID, true);
		}

		# Maybe an end subscription call?
		if (isset($_GET['sf-endsub']))
		{
			sf_end_sub(sf_esc_int($_GET['topic']), $current_user->ID, true);
		}

		# Maybe an remove all subscriptions call?
		if (isset($_GET['sf-endallsubs']))
		{
			sf_remove_user_subs(sf_esc_int($_GET['userid']));
		}

		# Maybe a watch call?
		if (isset($_GET['sf-watch']))
		{
			sf_save_watch(sf_esc_int($_GET['topic']), $current_user->ID, true);
		}

		# Maybe an end watch call?
		if (isset($_GET['sf-endwatch']))
		{
			sf_end_watch(sf_esc_int($_GET['topic']), $current_user->ID, true);
		}

		# Maybe an remove all watches call?
		if (isset($_GET['sf-endallwatches']))
		{
			sf_remove_user_watches(sf_esc_int($_GET['userid']));
		}

		# Is it a call to remove unread post list?
		if (isset($_POST['doqueue'])) sf_remove_waiting_queue();

		# Add someone to Buddy List?
		if (isset($_POST['newbuddy']))
		{
			sf_setup_pm_includes();
			sf_add_buddy(sf_esc_int($_POST['newbuddy']));
		}

		# Remove someone from Buddy List?
		if (isset($_POST['oldbuddy']))
		{
			sf_setup_pm_includes();
			sf_remove_buddy(sf_esc_int($_POST['oldbuddy']));
		}

		# Is it a call to report a post to admin?
		if (isset($_POST['rpaction'])) return sf_report_post_form();
		# Or mail a report in?
		if (isset($_POST['sendrp'])) sf_report_post_send();

		# Now display forum page
		$content = sf_display_banner() . sf_process_hook('sf_hook_pre_content', '') . wpautop($content);
		$content .= sf_process_hook('sf_hook_post_content', '');
		$content .= sf_js_check();

		$content .= sf_render_page($sfvars['pageview']);
	}
# ---------------------------------------------------------

# ---------------------------------------------------------
	return $content;
}

?>