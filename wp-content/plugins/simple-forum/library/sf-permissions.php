<?php
/*
Simple:Press
Permissions Model forum rendering helper functions
$LastChangedDate: 2011-03-25 10:24:04 -0700 (Fri, 25 Mar 2011) $
$Rev: 5719 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# function to return the permissions for the current user
function sf_get_global_permissions($forum_id = '')
{
	global $current_user, $sfglobals;

	$permissions = array();

	# if user is admin, just return true for all
	if ($current_user->forumadmin) {
		$permissions['Can view forum'] = 1;
		$permissions['Can view forum lists only'] = 1;
		$permissions['Can view forum and topic lists only'] = 1;
		$permissions['Can view admin posts'] = 1;
		$permissions['Can start new topics'] = 1;
		$permissions['Can reply to topics'] = 1;
		$permissions['Can create linked topics'] = 1;
		$permissions['Can break linked topics'] = 1;
		$permissions['Can edit any topic titles'] = 1;
		$permissions['Can edit own topic titles'] = 1;
		$permissions['Can pin topics'] = 1;
		$permissions['Can move topics'] = 1;
		$permissions['Can move posts'] = 1;
		$permissions['Can lock topics'] = 1;
		$permissions['Can delete topics'] = 1;
		$permissions['Can edit own posts forever'] = 1;
		$permissions['Can edit own posts until reply'] = 1;
		$permissions['Can edit any posts'] = 1;
		$permissions['Can delete own posts'] = 1;
		$permissions['Can delete any posts'] = 1;
		$permissions['Can pin posts'] = 1;
		$permissions['Can reassign posts'] = 1;
		$permissions['Can view users email addresses'] = 1;
		$permissions['Can view members profiles'] = 1;
		$permissions['Can view members lists'] = 1;
		$permissions['Can report posts'] = 1;
		$permissions['Can bypass spam control'] = 1;
		$permissions['Can bypass post moderation'] = 1;
		$permissions['Can bypass post moderation once'] = 1;
		$permissions['Can upload images'] = 1;
		$permissions['Can upload media'] = 1;
		$permissions['Can upload files'] = 1;
		$permissions['Can use signatures'] = 1;
		$permissions['Can upload signatures'] = 1;
		$permissions['Can upload avatars'] = 1;
		$permissions['Can use private messaging'] = 1;
		$permissions['Can subscribe'] = 1;
		$permissions['Can watch topics'] = 1;
		$permissions['Can change topic status'] = 1;
		$permissions['Can rate posts'] = 1;
		$permissions['Can use spoilers'] = 1;
		$permissions['Can view links'] = 1;
		$permissions['Can moderate pending posts'] = 1;
	} else {
		# if there aren't any usger group memberhsips or any permissions return no permission
		if (empty($sfglobals['memberships']) || empty($sfglobals['permissions'])) return '';

		#initialize permissions array
		$permissions['Can view forum'] = 0;
		$permissions['Can view forum lists only'] = 0;
		$permissions['Can view forum and topic lists only'] = 0;
		$permissions['Can view admin posts'] = 0;
		$permissions['Can start new topics'] = 0;
		$permissions['Can reply to topics'] = 0;
		$permissions['Can create linked topics'] = 0;
		$permissions['Can break linked topics'] = 0;
		$permissions['Can edit own topic titles'] = 0;
		$permissions['Can edit any topic titles'] = 0;
		$permissions['Can pin topics'] = 0;
		$permissions['Can move topics'] = 0;
		$permissions['Can move posts'] = 0;
		$permissions['Can lock topics'] = 0;
		$permissions['Can delete topics'] = 0;
		$permissions['Can edit own posts forever'] = 0;
		$permissions['Can edit own posts until reply'] = 0;
		$permissions['Can edit any posts'] = 0;
		$permissions['Can delete own posts'] = 0;
		$permissions['Can delete any posts'] = 0;
		$permissions['Can pin posts'] = 0;
		$permissions['Can reassign posts'] = 0;
		$permissions['Can view users email addresses'] = 0;
		$permissions['Can view members profiles'] = 0;
		$permissions['Can view members lists'] = 0;
		$permissions['Can report posts'] = 0;
		$permissions['Can bypass spam control'] = 0;
		$permissions['Can bypass post moderation'] = 0;
		$permissions['Can bypass post moderation once'] = 0;
		$permissions['Can upload images'] = 0;
		$permissions['Can upload media'] = 0;
		$permissions['Can upload files'] = 0;
		$permissions['Can use signatures'] = 0;
		$permissions['Can upload signatures'] = 0;
		$permissions['Can upload avatars'] = 0;
		$permissions['Can use private messaging'] = 0;
		$permissions['Can subscribe'] = 0;
		$permissions['Can watch topics'] = 0;
		$permissions['Can change topic status'] = 0;
		$permissions['Can rate posts'] = 0;
		$permissions['Can use spoilers'] = 0;
		$permissions['Can view links'] = 0;
		$permissions['Can moderate pending posts'] = 0;

		foreach ($sfglobals['memberships'] as $ugid)
		{
			foreach ($sfglobals['permissions'] as $perm)
			{
				if ($perm->usergroup_id == $ugid['usergroup_id'])
				{
					# return the truly global permissions that have no dependency on forum/topic
					$permissions['Can upload avatars'] |= $sfglobals['roles'][$perm->permission_role]['Can upload avatars'];
					$permissions['Can upload signatures'] |= $sfglobals['roles'][$perm->permission_role]['Can upload signatures'];
					$permissions['Can use private messaging'] |= $sfglobals['roles'][$perm->permission_role]['Can use private messaging'];

					if ($forum_id == '')
					{
						# return the pseudo global permissions if no forum/topic is specified
						# these are primarily so they will display on the profile page if allowed in any forum
						$permissions['Can subscribe'] |= $sfglobals['roles'][$perm->permission_role]['Can subscribe'];
						$permissions['Can use signatures'] |= $sfglobals['roles'][$perm->permission_role]['Can use signatures'];
						# this is for check on profile page for direct call check
						$permissions['Can view members profiles'] |= $sfglobals['roles'][$perm->permission_role]['Can view members profiles'];
						$permissions['Can view members lists'] |= $sfglobals['roles'][$perm->permission_role]['Can view members lists'];
						# if no forum, determine watch permission for group page
						$permissions['Can watch topics'] |= $sfglobals['roles'][$perm->permission_role]['Can watch topics'];
						$permissions['Can create linked topics'] |= $sfglobals['roles'][$perm->permission_role]['Can create linked topics'];
						$permissions['Can view forum lists only'] |= $sfglobals['roles'][$perm->permission_role]['Can view forum lists only'];
						$permissions['Can view forum and topic lists only'] |= $sfglobals['roles'][$perm->permission_role]['Can view forum and topic lists only'];
						$permissions['Can view admin posts'] |= $sfglobals['roles'][$perm->permission_role]['Can view admin posts'];
    					$permissions['Can view links'] |= $sfglobals['roles'][$perm->permission_role]['Can view links'];
						# need these for pms
						$permissions['Can upload images'] |= $sfglobals['roles'][$perm->permission_role]['Can upload images'];
						$permissions['Can upload media'] |= $sfglobals['roles'][$perm->permission_role]['Can upload media'];
						$permissions['Can upload files'] |= $sfglobals['roles'][$perm->permission_role]['Can upload files'];
					}

					# return forum/topic specific if forum id is provided
					if ($perm->forum_id == $forum_id)
					{
						# return the pseudo global permissions that need to be overwritten if forum/topic is specified
						$permissions['Can subscribe'] |= $sfglobals['roles'][$perm->permission_role]['Can subscribe'];
						$permissions['Can use signatures'] |= $sfglobals['roles'][$perm->permission_role]['Can use signatures'];
						$permissions['Can view forum lists only'] |= $sfglobals['roles'][$perm->permission_role]['Can view forum lists only'];
						$permissions['Can view forum and topic lists only'] |= $sfglobals['roles'][$perm->permission_role]['Can view forum and topic lists only'];
						$permissions['Can view admin posts'] |= $sfglobals['roles'][$perm->permission_role]['Can view admin posts'];

						# return the permissions that are forum/topic specific
						$permissions['Can view forum'] |= $sfglobals['roles'][$perm->permission_role]['Can view forum'];
						$permissions['Can start new topics'] |= $sfglobals['roles'][$perm->permission_role]['Can start new topics'];
						$permissions['Can reply to topics'] |= $sfglobals['roles'][$perm->permission_role]['Can reply to topics'];
						$permissions['Can create linked topics'] |= $sfglobals['roles'][$perm->permission_role]['Can create linked topics'];
						$permissions['Can break linked topics'] |= $sfglobals['roles'][$perm->permission_role]['Can break linked topics'];
						$permissions['Can edit own topic titles'] |= $sfglobals['roles'][$perm->permission_role]['Can edit own topic titles'];
						$permissions['Can edit any topic titles'] |= $sfglobals['roles'][$perm->permission_role]['Can edit any topic titles'];
						$permissions['Can pin topics'] |= $sfglobals['roles'][$perm->permission_role]['Can pin topics'];
						$permissions['Can move topics'] |= $sfglobals['roles'][$perm->permission_role]['Can move topics'];
						$permissions['Can move posts'] |= $sfglobals['roles'][$perm->permission_role]['Can move posts'];
						$permissions['Can lock topics'] |= $sfglobals['roles'][$perm->permission_role]['Can lock topics'];
						$permissions['Can delete topics'] |= $sfglobals['roles'][$perm->permission_role]['Can delete topics'];
						$permissions['Can edit own posts forever'] |= $sfglobals['roles'][$perm->permission_role]['Can edit own posts forever'];
						$permissions['Can edit own posts until reply'] |= $sfglobals['roles'][$perm->permission_role]['Can edit own posts until reply'];
						$permissions['Can edit any posts'] |= $sfglobals['roles'][$perm->permission_role]['Can edit any posts'];
						$permissions['Can delete own posts'] |= $sfglobals['roles'][$perm->permission_role]['Can delete own posts'];
						$permissions['Can delete any posts'] |= $sfglobals['roles'][$perm->permission_role]['Can delete any posts'];
						$permissions['Can pin posts'] |= $sfglobals['roles'][$perm->permission_role]['Can pin posts'];
						$permissions['Can reassign posts'] |= $sfglobals['roles'][$perm->permission_role]['Can reassign posts'];
						$permissions['Can view users email addresses'] |= $sfglobals['roles'][$perm->permission_role]['Can view users email addresses'];
						$permissions['Can view members profiles'] |= $sfglobals['roles'][$perm->permission_role]['Can view members profiles'];
						$permissions['Can view members lists'] |= $sfglobals['roles'][$perm->permission_role]['Can view members lists'];
						$permissions['Can report posts'] |= $sfglobals['roles'][$perm->permission_role]['Can report posts'];
						$permissions['Can bypass spam control'] |= $sfglobals['roles'][$perm->permission_role]['Can bypass spam control'];
						$permissions['Can bypass post moderation'] |= $sfglobals['roles'][$perm->permission_role]['Can bypass post moderation'];
						$permissions['Can bypass post moderation once'] |= $sfglobals['roles'][$perm->permission_role]['Can bypass post moderation once'];
						$permissions['Can upload images'] |= $sfglobals['roles'][$perm->permission_role]['Can upload images'];
						$permissions['Can upload media'] |= $sfglobals['roles'][$perm->permission_role]['Can upload media'];
						$permissions['Can upload files'] |= $sfglobals['roles'][$perm->permission_role]['Can upload files'];
						$permissions['Can watch topics'] |= $sfglobals['roles'][$perm->permission_role]['Can watch topics'];
						$permissions['Can change topic status'] |= $sfglobals['roles'][$perm->permission_role]['Can change topic status'];
						$permissions['Can rate posts'] |= $sfglobals['roles'][$perm->permission_role]['Can rate posts'];
						$permissions['Can use spoilers'] |= $sfglobals['roles'][$perm->permission_role]['Can use spoilers'];
						$permissions['Can view links'] |= $sfglobals['roles'][$perm->permission_role]['Can view links'];
						$permissions['Can moderate pending posts'] |= $sfglobals['roles'][$perm->permission_role]['Can moderate pending posts'];
					}
				}
			}
		}
	}

	if ($current_user->guest) {
		$permissions['Can upload images'] = 0;
		$permissions['Can upload files'] = 0;
		$permissions['Can upload files'] = 0;
		$permissions['Can use signatures'] = 0;
		$permissions['Can upload signatures'] = 0;
		$permissions['Can upload avatars'] = 0;
		$permissions['Can use private messaging'] = 0;
		$permissions['Can subscribe'] = 0;
		$permissions['Can watch topics'] = 0;
		$permissions['Can change topic status'] = 0;
		$permissions['Can rate posts'] = 0;
		$permissions['Can create linked topics'] = 0;
		$permissions['Can moderate pending posts'] = 0;
	}

	return $permissions;
}

# function to return yes or no (0 or 1) for input permissions array for a specified forum for the current user
function sf_get_permissions($perms, $forum_id)
{
	global $current_user, $sfglobals;

	$permissions = array();

	# if user is admin, just return true for all
	if ($current_user->forumadmin)
	{
		for  ($x=0; $x<count($perms); $x++)
		{
			$permissions[$perms[$x]] = 1;
		}
	} else {
		foreach($sfglobals['memberships'] as $ugid)
		{
			if (empty($sfglobals['permissions'])) return '';
			foreach($sfglobals['permissions'] as $perm)
			{
				if($perm->forum_id == $forum_id && $perm->usergroup_id == $ugid['usergroup_id'])
				{
					for  ($x=0; $x<count($perms); $x++)
					{
						if (isset($permissions[$perms[$x]]))
						{
							$permissions[$perms[$x]] |= $sfglobals['roles'][$perm->permission_role][$perms[$x]];
						} else {
							$permissions[$perms[$x]] = $sfglobals['roles'][$perm->permission_role][$perms[$x]];
						}
					}
				}
			}
		}
	}

	return $permissions;
}

# ----------------------------------------------
# sf_can_view_forum()
# Checks current user against the cached
# groups, roles and permissions to determine
# if current user cab view the forum passed in
# ----------------------------------------------

function sf_can_view_forum($forumid, $newposts=false)
{
	global $current_user, $sfglobals, $sfvars;

	if ($current_user->forumadmin) return true;

	# dont expose password protect forum info
	$post = get_post(sf_get_option('sfpage'));
	if (post_password_required($post)) return false;

	$result = false;
    if ($sfglobals['memberships'])
    {
    	foreach($sfglobals['memberships'] as $ugid)
    	{
    		if (empty($sfglobals['permissions'])) return '';
    		foreach($sfglobals['permissions'] as $perm)
    		{
    			if($perm->forum_id == $forumid && $perm->usergroup_id == $ugid['usergroup_id'])
    			{
    				$result |= $sfglobals['roles'][$perm->permission_role]['Can view forum'];
    			}
    		}
    	}
    }
	return $result;
}

# function to return a list of moderators which is defined as users in a usergroup with the is_moderator flag set
function sf_get_moderators()
{
	global $wpdb;

	$moderators = array();

	# get all the moderator groups - return empty list if no moderator groups
	$mods = $wpdb->get_results("SELECT * FROM ".SFMEMBERS." WHERE moderator=1");
	if (empty($mods)) return '';

	# cycle through all moderators
	$count = 0;
	foreach ($mods as $mod)
	{
		$moderators[$count]['id'] = $mod->user_id;	 # add user to moderator list
		$moderators[$count]['display_name'] = $mod->display_name;  # get display name for moderator
		$count++;
	}

	return $moderators;
}

# function to return a list of admins which is defined as users with the member table admin flag set
function sf_get_admins()
{
	global $wpdb;

	$administrators = array();

	# get all the administrators
	$admins = $wpdb->get_results("SELECT * FROM ".SFMEMBERS." WHERE admin=1");
	if (empty($admins)) return '';

	# cycle through all admins
	$count = 0;
	foreach ($admins as $admin)
	{
		$administrators[$count]['id'] = $admin->user_id;	 # add user to admin list
		$administrators[$count]['display_name'] = $admin->display_name;  # get display name for admin
		$count++;
	}

	return $administrators;
}

# function to check if a specific user can PM which is defined as being a member in any usergroup with 'Can use private messaging' permission set
function sf_is_pm_user($user_id)
{
	if (sf_is_forum_admin($user_id)) return true;

	return sf_get_member_item($user_id, 'pm');
}

function sf_is_forum_moderator($forum_id)
{
    global $wpdb, $current_user, $sfglobals;

	if ($forum_id == '')
	{
		return $wpdb->get_results("SELECT * FROM ".SFMEMBERS." WHERE moderator=1 AND user_id=".$current_user->ID);
	}

    # get all the moderator groups for the specified forum - return false if no moderator groups
    $modgroups = $wpdb->get_results("SELECT * FROM ".SFUSERGROUPS." WHERE usergroup_is_moderator='1'");
    if (empty($modgroups)) return 0;

    # if user is in multiple usergroups, cycle through all of them
	foreach ($sfglobals['memberships'] as $ugid)
    {
        # check each usergroup that user is a member of to see if the usergroup is a moderator group
        foreach ($modgroups as $modgroup)
        {
            # check if modgroup has permission for forum
            # if the user is in a moderator group and the group has forum permission return true
			if (empty($sfglobals['permissions'])) return false;
			foreach ($sfglobals['permissions'] as $perm)
			{
	            if ($ugid['usergroup_id'] == $modgroup->usergroup_id && $perm->forum_id == $forum_id && $perm->usergroup_id == $ugid['usergroup_id'])
				{
					return true;
				}
    		}
        }
    }

    # no matches, return false
    return false;
}

function sf_is_forum_admin($userid)
{
    global $wpdb, $current_user;

	$is_admin = 0;
	if ($userid)
	{
		if (is_multisite() && is_super_admin($userid))
		{
			$is_admin = 1;
		} else {
			$is_admin = sf_get_member_item($userid, 'admin');
		}
	}

	return $is_admin;
}

function sf_user_can($user_id, $permission, $object_id=0)
{
	global $current_user, $wpdb, $sfglobals;

	$memberships = array();

	# just return true for admin
	if (sf_is_forum_admin($user_id)) return true;

    if ($current_user->ID == $user_id)
	{
		$memberships = $sfglobals['memberships'];
	} else {
		# get the usergroup memberships for the current user
		$memberships = sf_get_user_memberships($user_id);
    	if (empty($memberships)) {
			$value = sf_get_sfmeta('default usergroup', 'sfguests');
			$memberships[0]['usergroup_id'] = $value[0]['meta_value'];
		}
	}
	if ($object_id == 0) {
	  	$object = '';
	} else {
		$object = ' AND forum_id='.$object_id;
	}

	# if user is in multiple usergroups, determine the "best" role (ie if any are true, then permission is granted)
	for ($x=0; $x<count($memberships); $x++) {
		$ugid = $memberships[$x];
		# grab the forum and usergroup specific roles and actions
		$rids = $wpdb->get_results("SELECT permission_role FROM ".SFPERMISSIONS." WHERE usergroup_id='".$ugid['usergroup_id']."'".$object);
		foreach ($rids as $rid) {
			$role_actions = $wpdb->get_var("SELECT role_actions FROM ".SFROLES." WHERE role_id='".$rid->permission_role."'");
			$actions = maybe_unserialize($role_actions);

			if ($actions) { # make sure there is a role in this forum before doing anything
				# grab the permission
				# "OR" in the permission with any other potential permission from other usergroups
				if ($actions[$permission] == 1) return true;
			}
		}
	}

	return false;
}

# can the current user remove a topic from the admin queue
function sf_user_can_remove_queue($topicid)
{
	global $current_user, $sfglobals;

	if($current_user->forumadmin) return true;
	if(!$current_user->moderator) return false;

	if(sf_topic_in_queue($topicid))
	{
		if($current_user->sfdelete	&& $sfglobals['admin']['sfmodasadmin']) return true;
	} else {
		if($current_user->sfdelete) return true;
	}
	return false;
}

# returns true if current user can only 'see' one forum
# NOTE: also returns forum id if there is only one forum (and user can see it)
function sf_single_forum_user()
{
	global $current_user, $sfglobals;

	$count = 0;
	$which_forum = false;
	if ($sfglobals['permissions'])
	{
		$checked = array();
		foreach ($sfglobals['permissions'] as $perm)
		{
			if (empty($checked[$perm->forum_id]))
			{
				if (sf_can_view_forum($perm->forum_id))
				{
					$which_forum = $perm->forum_id;
					$count++;
					if ($count > 1) break;
					$checked[$perm->forum_id] = 1;
				}
			}
		}
	}
	if($count == 1)
	{
		return $which_forum;
	} else {
		return false;
	}
}

?>