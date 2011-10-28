<?php
/*
Simple:Press
Admin Moderation and mark as read control
$LastChangedDate: 2010-05-13 19:49:45 -0700 (Thu, 13 May 2010) $
$Rev: 4017 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

sf_forum_ahah_support();
# ----------------------------------

global $wpdb, $current_user;

if (isset($_GET['action'])) $action = sf_esc_int($_GET['action']);
if (isset($_GET['pid'])) $postid = sf_esc_int($_GET['pid']);
if (isset($_GET['tid'])) $topicid = sf_esc_int($_GET['tid']);
if (isset($_GET['fid'])) $forumid = sf_esc_int($_GET['fid']);

if (empty($topicid)) die();

sf_initialise_globals($forumid);

if(!$current_user->sfapprove)
{
	echo (__('Access Denied', "sforum"));
	die();
}

# actions:
#	0 = approve
#	1 = mark as read
#	2 = delete

switch($action)
{
	case 0:
		sf_approve_post(true, 0, $topicid, false);
		_e("All Topic Posts Marked as Approved", "sforum");
		break;

	case 1:
		sf_remove_from_waiting(true, $topicid);
		_e("All Topic Posts Marked as Read", "sforum");
		break;

	case 2:
    if (empty($forumid) || empty($postid)) die();
		sf_delete_post($postid, $topicid, $forumid, false);
		_e("Post Deleted", "sforum");
		break;
}

# we always need to remove from users new posts list
sf_remove_users_newposts($topicid);

die();

?>