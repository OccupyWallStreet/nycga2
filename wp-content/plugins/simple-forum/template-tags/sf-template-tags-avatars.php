<?php
/*
Simple:Press
Avatar Template Tag(s)
$LastChangedDate: 2010-05-29 14:53:48 -0700 (Sat, 29 May 2010) $
$Rev: 4094 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

/* 	=====================================================================================

	sf_show_avatar($size=0)

	displays avatar of current user

	parameters:
		$size:			Size to display avatar (applied to width) Leave as 0
						to use size of graphic.

	returns:		<img> class = 'sfavatartag'
 	===================================================================================*/


function sf_show_avatar($size=0)
{
	global $current_user;

	if($current_user->guest) $icon='guest';
	if($current_user->member) $icon='user';
	if($current_user->forumadmin) $icon='admin';

	echo sf_render_avatar($icon, $current_user->ID, sf_filter_email_display($current_user->user_email), sf_filter_email_display($current_user->guestemail), true, $size);
	return;
}

/* 	=====================================================================================

	sf_show_members_avatar($userid, $size=0)

	displays avatar of current user

	parameters:
		$userid:		Requires the userid whose avatar is being requested.
		$size:			Size to display avatar (applied to width) Leave as 0
						to use size of graphic.

	returns:		<img> class = 'sfavatartag'
 	===================================================================================*/

function sf_show_members_avatar($userid, $size=0)
{
	global $wpdb;

    $userid = sf_esc_int($userid);
	if (empty($userid)) return;

	$user = $wpdb->get_row("SELECT user_email FROM ".SFUSERS." WHERE ID = ".$userid);
	if ($user)
	{
		if (sf_is_forum_admin($userid) ? $icon='admin' : $icon='user');
		echo sf_render_avatar($icon, $userid, sf_filter_email_display($user->user_email), '', true, $size);
	}
	return;
}

/* 	=====================================================================================

	sf_show_forum_avatar($email, $size=0)

	displays avatar of current user or guest pulled form the forum

	parameters:
		$email:			Requires the email address whose avatar is being requested.
		$size:			Size to display avatar (applied to width) Leave as 0
						to use size of graphic.

	returns:		<img> class = 'sfavatartag'
 	===================================================================================*/

function sf_show_forum_avatar($email, $size=0)
{
	global $wpdb;

    $email = sf_esc_str($email);
    if (empty($email)) return;

	$userid = $wpdb->get_var("SELECT ID FROM ".SFUSERS." WHERE user_email = '".$email."'");
	if ($userid)
	{
		$icon = 'user';
		if (sf_is_forum_admin($userid)) $icon='admin';
		echo sf_render_avatar($icon, $userid, $email, '', true, $size);
	} else {
		$icon = 'guest';
		echo sf_render_avatar($icon, 0, '', $email, '', true, $size);
	}
	return;
}

/* 	=====================================================================================

	sf_show_rank($userid, $both=true, $echo=true)

	displays forum rank for the user. Like the standard forum display, it will always show
    the users badge if it exists and optionally show the rank

	parameters:
		$userid:		user id of the user to get rank for (use $current_user->ID
                        for current user)
		$both:			get the badge and the rank if true, otherwise just badge,
                        if it exists, or rank
		$echo			echo or return the forum rank

 	===================================================================================*/
function sf_show_rank($userid, $both=true, $echo=true)
{
	global $sfglobals;

    $userid = sf_esc_int($userid);

	$sfglobals['display'] = sf_get_option('sfdisplay');
    sf_build_ranks_cache();
    if (!empty($userid))
    {
    	$status = 'user';
    	if (sf_is_forum_admin($userid)) $status = 'admin';
        $posts = sf_get_member_item($userid, 'posts');
    } else {
       	$status = 'guest';
        $posts = 0;
        $userid = 0;
    }

    # save global option before setting option for rank and/or badge based on template tag arguments
    $saved_option = $sfglobals['display']['posts']['rankdisplay'];
    $sfglobals['display']['posts']['rankdisplay'] = $both;

    # get the rank/badge
    $rank = sf_render_usertype($status, $userid, $posts);

    # restore the global option
    $sfglobals['display']['posts']['rankdisplay'] = $saved_option;
    if ($echo)
    {
        echo $rank;
        return;
    } else {
	   return $rank;
    }
}

?>