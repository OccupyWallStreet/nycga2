<?php
/*
Simple:Press
Profile Rendering Routines
$LastChangedDate: 2010-11-14 09:34:16 -0700 (Sun, 14 Nov 2010) $
$Rev: 4944 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

function sf_render_profile()
{
	global $sfvars, $current_user;

    $out = '';

	# Maybe a Show Profile page
	if ($sfvars['pageview'] == 'profileshow' || $sfvars['pageview'] == 'profileedit')
    {
        if (!empty($sfvars['member']))
        {
            $userid = sf_get_user_id_from_user_login(urldecode($sfvars['member']));
        } else {
            $userid = $current_user->ID;
        }

        # do we have a valid member profile requested?
        if (empty($userid) || (!$current_user->sfprofiles && $current_user->ID != $userid && !$current_user->forumadmin))
        {
			update_sfnotice('sfmessage', '1@'.__("Invalid Profile Request", "sforum"));
			$out.= sf_render_queued_message();
			$out.= '<div class="sfmessagestrip">';
			$out.= apply_filters('sf_profile_error_msg', __('Sorry, an invalid profile request was detected! Do you need to be logged in?', 'sforum'));
			$out.= '</div>'."\n";
			return $out;
        }

        if ($sfvars['pageview'] == 'profileshow' || ($current_user->ID != $userid && !$current_user->forumadmin))
        {
            $out.= sf_view_profile($userid);
        } else {
            $out.= sf_profile_form('all', $userid, $sfvars['newuser']);
        }
    }

	return $out;
}

function sf_view_profile($userid)
{
	include_once (SF_PLUGIN_DIR.'/profile/sf-profile-display.php');
	return sf_render_member_profile($userid, 'page');
}

function sf_profile_form($panel, $userid, $newuser)
{
	include_once (SF_PLUGIN_DIR.'/profile/forms/sf-form-profile.php');
	return sf_render_profile_form($panel, $userid, $newuser);
}

function sf_view_permissions()
{
	include_once (SF_PLUGIN_DIR.'/profile/forms/sf-form-permissions.php');
	return sf_render_permissions_form();
}

function sf_view_buddylist()
{
	include_once (SF_PLUGIN_DIR.'/profile/forms/sf-form-buddylist.php');
	return sf_render_buddylist_form();
}

?>