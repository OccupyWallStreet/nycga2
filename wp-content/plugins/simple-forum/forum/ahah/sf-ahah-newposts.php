<?php
/*
Simple:Press
Ahah - Admins NewPosts Dropdown
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

sf_forum_ahah_support();

require_once (dirname(__FILE__)."/../sf-new-admin-view.php");

# --------------------------------------------

global $current_user, $sfglobals;

sf_initialise_globals();

if ($current_user->forumadmin || $current_user->moderator)
{
	# must be loading up the new post list
	$newposts = sf_get_admins_queued_posts();
	$out = sf_render_new_post_list_admin($newposts, true);
	echo $out;
} else {
	echo (__('Access Denied', "sforum"));
}

die();

?>