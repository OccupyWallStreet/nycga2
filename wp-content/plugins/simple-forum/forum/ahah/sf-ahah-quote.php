<?php
/*
Simple:Press
Quote handing for posts
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

$postid = sf_esc_int($_GET['post']);
$forumid = sf_esc_int($_GET['forumid']);
if (empty($forumid) || empty($postid)) die();

sf_initialise_globals($forumid);

if(!$current_user->sfreply)
{
	echo (__('Access Denied', "sforum"));
	die();
}

$content = $wpdb->get_var("SELECT post_content FROM ".SFPOSTS." WHERE post_id=".$postid);
$content = sf_filter_content_edit($content, 'edit');
echo $content;

die();

?>