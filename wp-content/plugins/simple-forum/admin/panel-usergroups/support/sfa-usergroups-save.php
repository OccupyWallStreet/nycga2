<?php
/*
Simple:Press
Admin User Groups Support Functions
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# function to create a new user group
function sfa_save_usergroups_new_usergroup()
{
    check_admin_referer('forum-adminform_usergroupnew', 'forum-adminform_usergroupnew');

    # if no usergroup name supplied use a default name
    if (empty($_POST['usergroup_name']))
    {
        $usergroupname = __("New User Group", "sforum");
    } else {
        $usergroupname = sf_filter_title_save(trim($_POST['usergroup_name']));
    }

    $usergroupdesc = sf_filter_title_save(trim($_POST['usergroup_desc']));
    if (isset($_POST['usergroup_is_moderator']))
	{
		$usergroupismod = 1;
	} else {
		$usergroupismod = 0;
	}

    # create the usergroup
    $success = sfa_create_usergroup_row($usergroupname, $usergroupdesc, $usergroupismod, true);

    if ($success == false)
    {
        $mess = __("New User Group Creation Failed!", "sforum");
    } else {
        $mess = __("New User Group Created", "sforum");
    }

    return $mess;
}

# function to update an existing user group
function sfa_save_usergroups_edit_usergroup()
{
	global $wpdb;

    check_admin_referer('forum-adminform_usergroupedit', 'forum-adminform_usergroupedit');

    $usergroupdata = array();
    $usergroup_id = sf_esc_int($_POST['usergroup_id']);
    $usergroupdata['usergroup_name'] = sf_filter_title_save(trim($_POST['usergroup_name']));
    $usergroupdata['usergroup_desc'] = sf_filter_title_save(trim($_POST['usergroup_desc']));
    if (isset($_POST['usergroup_is_moderator'])) { $usergroupdata['usergroup_is_moderator'] = 1; } else { $usergroupdata['usergroup_is_moderator'] = 0; }

    # ensure that something has actually changed
    if ($usergroupdata['usergroup_name'] == $_POST['ugroup_name'] && $usergroupdata['usergroup_desc'] ==
        $_POST['ugroup_desc'] && $usergroupdata['usergroup_is_moderator'] == $_POST['ugroup_ismod'])
    {
        $mess = __("No Data Changed", "sforum");
        return $mess;
    }

    # update the user group info
	$sql = "UPDATE ".SFUSERGROUPS." SET ";
	$sql.= 'usergroup_name="'.$usergroupdata['usergroup_name'].'", ';
	$sql.= 'usergroup_desc="'.$usergroupdata['usergroup_desc'].'", ';
	$sql.= 'usergroup_is_moderator="'.$usergroupdata['usergroup_is_moderator'].'" ';
	$sql.= 'WHERE usergroup_id="'.$usergroup_id.'";';
    $success = $wpdb->query($sql);
    if ($success == false)
    {
        $mess = __("User Group Update Failed!", "sforum");
    } else {
        $mess = __("User Group Record Updated", "sforum");
    }

    return $mess;
}

function sfa_save_usergroups_delete_usergroup()
{
	global $wpdb;

    check_admin_referer('forum-adminform_usergroupdelete', 'forum-adminform_usergroupdelete');

    $usergroup_id = sf_esc_int($_POST['usergroup_id']);

    # dont allow updates to the default user groups
    $usergroup = sfa_get_usergroups_row($usergroup_id);
    if ($usergroup->usergroup_locked)
    {
        $mess = __("Sorry, the default User Groups cannot be deleted.", "sforum");
        return $mess;
    }

    # remove all memberships for this user group
    $wpdb->query("DELETE FROM ".SFMEMBERSHIPS." WHERE usergroup_id=".$usergroup_id);

	# remove any permission sets using this user group
	$permissions = $wpdb->get_results("SELECT permission_id FROM ".SFPERMISSIONS." WHERE usergroup_id=".$usergroup_id);
	if ($permissions)
	{
		foreach ($permissions as $permission)
		{
			sfa_remove_permission_data($permission->permission_id);
		}
	}

	# remove any group default permissions using this user group
	$wpdb->query("DELETE FROM ".SFDEFPERMISSIONS." WHERE usergroup_id=".$usergroup_id);

    # remove the user group
   	$wpdb->query("DELETE FROM ".SFMEMBERSHIPS." WHERE usergroup_id=".$usergroup_id);
    $success = $wpdb->query("DELETE FROM ".SFUSERGROUPS." WHERE usergroup_id=".$usergroup_id);
    if ($success == false)
    {
        $mess = __("User Group Delete Failed!", "sforum");
    } else {
        $mess = __("User Group Deleted", "sforum");

        #get affected members and update pm flag
		$members = $wpdb->get_results("SELECT user_id FROM ".SFMEMBERSHIPS.' WHERE usergroup_id='.$usergroup_id);
		sfc_rebuild_members_pm($members);
    }

    return $mess;
}

function sfa_save_usergroups_add_members()
{
    check_admin_referer('forum-adminform_membernew', 'forum-adminform_membernew');

    # add the users to the user group membership
    $usergroup_id = sf_esc_int($_POST['usergroup_id']);
    if (isset($_POST['member_id']))
    {
    	$user_id_list = array_unique($_POST['member_id']);
	}

    if (!isset($user_id_list)){
	    $mess = __("No Data Changed!", "sforum");
		return $mess;
	}

	$members = array();
	for( $x=0; $x<count($user_id_list); $x++)
	{
		$user_id = sf_esc_int($user_id_list[$x]);
		$check = sf_check_membership($usergroup_id, $user_id);
		if (empty($check))
		{
			$success = sfc_add_membership($usergroup_id, $user_id);
		}
 	   	if ($success == false)
		{
	    	$mess = __("Member Add Failed!", "sforum");
	    	return $mess;
		}

		# build members list to update
		$members[$x]->user_id = $user_id;

		# update mod flag
 	   	sfc_update_member_moderator_flag($user_id);
	}

	sfc_rebuild_members_pm($members);

    $mess = __("Member(s) Added to User Group", "sforum");

    return $mess;
}

function sfa_save_usergroups_delete_members()
{
	global $wpdb;

    check_admin_referer('forum-adminform_memberdel', 'forum-adminform_memberdel');

    $usergroup_id = sf_esc_int($_POST['usergroupid']);
    $new_usergroup_id = $_POST['usergroup_id'];
    if (isset($_POST['dmember_id']))
    {
	    $user_id_list = array_unique($_POST['dmember_id']);
	}

	# make sure not moving to same user group
	if (!isset($user_id_list) || $usergroup_id == $new_usergroup_id)
	{
	    $mess = __("No Data Changed!", "sforum");
		return $mess;
	}

	$members = array();
	for( $x=0; $x<count($user_id_list); $x++)
	{
		$user_id = sf_esc_int($user_id_list[$x]);
		$success = $wpdb->query("DELETE FROM ".SFMEMBERSHIPS." WHERE user_id=".$user_id." AND usergroup_id=".$usergroup_id);

		# build members list to update
		$members[$x]->user_id = $user_id;

	    if ($new_usergroup_id != -1)
	    {
			$check = sf_check_membership($new_usergroup_id, $user_id);
			if (empty($check))
			{
				$success = sfc_add_membership($new_usergroup_id, $user_id);
			}
	    }

		# update mod flag
	    sfc_update_member_moderator_flag($user_id);
	}

	# update pm flag for affected members
	sfc_rebuild_members_pm($members);

    if ($new_usergroup_id != -1)
    {
	    $mess = __("Member(s) Moved", "sforum");
	} else {
	    $mess = __("Member(s) Deleted From User Group", "sforum");
	}

    return $mess;
}

?>