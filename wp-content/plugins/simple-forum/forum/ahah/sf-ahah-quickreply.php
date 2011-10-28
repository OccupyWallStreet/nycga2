<?php
/*
Simple:Press
Admin 'Quick Reply' Save
$LastChangedDate: 2011-04-04 04:17:40 -0700 (Mon, 04 Apr 2011) $
$Rev: 5811 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

sf_forum_ahah_support();
sf_build_memberdata_cache();

# --------------------------------------------

global $wpdb, $current_user, $sfglobals;

sf_initialise_globals($forumid);

# check credentials
if($current_user->moderator == false)
{
	die('Access Denied');
}

$postitem = $_GET['postitem'];  # escaped later
$topicid = sf_esc_int($_GET['tid']);
$forumid = sf_esc_int($_GET['fid']);
if (empty($postitem) || empty($topicid) || empty($forumid)) die();

$newpost = array();
$newpost['forumid']			= $forumid;
$newpost['topicid']			= $topicid;
$newpost['postcontent']		= $postitem;

# decode it from the encoded javascript string
$newpost['postcontent']		= urldecode($newpost['postcontent']);
$newpost['postcontent'] 	= sf_filter_content_save($newpost['postcontent'], 'new');
$newpost['postpin']			= 0;
$newpost['topicsub']		= '';
$newpost['statvalue']		= '';
$newpost['posttimestamp'] 	= "'" . current_time('mysql') . "'";
$newpost['userid']			= $current_user->ID;
$newpost['poststatus']		= 0;

$newpost['forumslug'] 		= sf_get_forum_slug($forumid);
$newpost['topicslug']		= sf_get_topic_slug($topicid);

$newpost['postername'] 		= sf_filter_name_save($current_user->display_name);
$newpost['posteremail'] 	= sf_filter_email_save($current_user->user_email);

$newpost['email_prefix'] 	= 'Re: ';

$ip = $_SERVER['REMOTE_ADDR'];

# Get post count in topic to enable index setting
$index=$wpdb->get_var("SELECT COUNT(post_id) FROM ".SFPOSTS." WHERE topic_id = ".$newpost['topicid']);
$index++;

$sql =  "INSERT INTO ".SFPOSTS;
$sql .= " (post_content, post_date, topic_id, forum_id, user_id, guest_name, guest_email, post_pinned, post_index, post_status, poster_ip) ";
$sql .= "VALUES (";
$sql .= "'".$newpost['postcontent']."', ";
$sql .= $newpost['posttimestamp'].", ";
$sql .= $newpost['topicid'].", ";
$sql .= $newpost['forumid'].", ";
$sql .= $newpost['userid'].", ";
$sql .= "'', ";
$sql .= "'', ";
$sql .= $newpost['postpin']. ", ";
$sql .= $index.", ";
$sql .= $newpost['poststatus'].", ";
$sql .= "'".$ip."');";

$wpdb->query($sql);
$newpost['postid'] = $wpdb->insert_id;

$postcount = $sfglobals['member']['posts'];
$postcount++;
sf_update_member_item($newpost['userid'], 'posts', $postcount);
$sfglobals['member']['posts'] = $postcount;  # update user post count in member cache

# construct new url
$newpost['url']=sf_build_url($newpost['forumslug'], $newpost['topicslug'], 0, $newpost['postid']);

# save hook
sf_process_hook('sf_hook_post_save', array($newpost['url'], $newpost['postcontent']));

# send out email notifications
$newpost['emailmsg'] = sf_email_notifications($newpost);

# Update forum, topic and post index data
sf_build_forum_index($newpost['forumid']);
sf_build_post_index($newpost['topicid'], $newpost['topicslug']);

# Maybe a watch call?

if (isset($_GET['watch']) && $_GET['watch'] == 'true')
{
	sf_save_watch($topicid, $current_user->ID, false);
}

# Maybe a topic status?
if (isset($_GET['status']) && sf_esc_int($_GET['status']))
{
	sf_update_topic_status_flag(sf_esc_int($_GET['status']), $topicid);
}

_e("Quick Reply Saved", "sforum");

die();

?>