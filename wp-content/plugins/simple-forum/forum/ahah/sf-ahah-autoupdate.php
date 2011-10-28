<?php
/*
Simple:Press
Ahah call for Auto Update
$LastChangedDate: 2010-10-25 12:36:46 -0700 (Mon, 25 Oct 2010) $
$Rev: 4825 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

sf_forum_ahah_support();
sf_setup_pm_includes();
# ----------------------------------

global $current_user, $sfglobals, $wpdb;

# get out of here if no target specified
$target = $_GET['target'];
if (empty($target)) die();

# First do check to see if user is logged in

if($target == 'checkuser')
{
	$thisuser = sf_esc_int($_GET['thisuser']);
	if($current_user->ID == 0 || $current_user->ID == '')
	{
		if($thisuser != 0 && $thisuser != '')
		{
			$sflogin=sf_get_option('sflogin');
			$out = '<div id="sfsession">';
			$out.= '<p>'.__("Your Session has Expired - ", "sforum");
			$out.= '<a href="'.$sflogin['sfloginurl'].'">'.__("Log Back In", "sforum").'</a></p>';
			$out.= '</div>';
			echo $out;
		}
	}
	die();
}

sf_initialise_globals();

# Update the new post Quicklinks
if(($target == 'quicklinkstop' && $sfglobals['display']['quicklinks']['qltop']) || ($target == 'quicklinksbottom' && $sfglobals['display']['quicklinks']['qlbottom']))
{
	if($sfglobals['display']['quicklinks']['qlcount'] > 0)
	{
		echo sf_render_newpost_quicklinks($sfglobals['display']['quicklinks']['qlcount']);
	}
	die();
}

# Update the New Post Counts
if($target == 'newposts')
{
	if($current_user->forumadmin || $current_user->moderator)
	{
		$newposts = sf_get_admins_queued_posts();
		echo sf_get_waiting_url($newposts, '', false);
	}
	die();
}

# only do pm autoupdate stuff if user is still logged in and can pm
if ($current_user->sfusepm)
{
	# Update the Inbox Count
	if($target == 'counts')
	{
		$out='';
		$out.= sf_render_sub_count();
		$out.= sf_render_watch_count();
		$out.= sf_render_inbox_count();
		echo $out;
		die();
	}

	# Update Inbox/Sentbox lists
	if($target == 'pmview')
	{
		$box = $_GET['show'];

		# Load up the data we need
		if($box == 'inbox')
		{
			$messagebox = sf_get_pm_inbox($current_user->ID);
		} else {
			$messagebox = sf_get_pm_sentbox($current_user->ID);
		}

		# Grab message count
		$messagecount = $wpdb->get_var("SELECT FOUND_ROWS()");

    	echo '<table class="sfforumtable" id="sfmainpmtable">'."\n";
		echo sf_render_pm_table($box, $messagebox, $messagecount, true);
        echo '</table>';

		die();
	}
}

die();
?>