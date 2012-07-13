<?php
/*
Simple:Press
Admin Permissions Support Functions
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# function to create a new permission set role
function sfa_save_permissions_new_role()
{
    global $sfactions;

    check_admin_referer('forum-adminform_rolenew', 'forum-adminform_rolenew');

    foreach ($sfactions["action"] as $index => $action)
    {
    	if(isset($_POST['b-'.$index]) ? $thisperm = '1' : $thisperm = '0');
    	$actions[$action] = $thisperm;
    }
	$actions = maybe_serialize($actions);

    $role_name = sf_filter_title_save(trim($_POST['role_name']));
    $role_desc = sf_filter_title_save(trim($_POST['role_desc']));

    # force max size
    $role_name = substr($role_name, 0, 50);
    $role_desc = substr($role_desc, 0, 150);

    # create the permission set
    $success = sfa_create_role_row($role_name, $role_desc, $actions, true);
    if ($success == false)
    {
        $mess = __("New Permission Set Creation Failed!", "sforum");
    } else {
        $mess = __("New Permission Set Created", "sforum");
    }

    return $mess;
}

# function to update a current permission set role
function sfa_save_permissions_edit_role()
{
    global $sfactions, $wpdb;

    check_admin_referer('forum-adminform_roleedit', 'forum-adminform_roleedit');

    $role_id = sf_esc_int($_POST['role_id']);
    $role_name = sf_filter_title_save(trim($_POST['role_name']));
    $role_desc = sf_filter_title_save(trim($_POST['role_desc']));

	# get old permissions to check for pm role changes
	$old_roles = sfa_get_role_row($role_id);
	$old_actions = unserialize($old_roles->role_actions);

    foreach ($sfactions["action"] as $index => $action)
    {
    	if(isset($_POST['b-'.$index]) ? $thisperm = '1' : $thisperm = '0');
    	$actions[$action] = $thisperm;
    }
    $new_actions = $actions;

    # save for later user before serializing
	$actions = maybe_serialize($actions);

    $roledata = array();
    $roledata['role_name'] = $role_name;
    $roledata['role_desc'] = $role_desc;

    # force max size
    $roledata['role_name'] = substr($roledata['role_name'], 0, 50);
    $roledata['role_desc'] = substr($roledata['role_desc'], 0, 150);

    # save the permission set role updated information
	$actions = esc_sql($actions);
	$sql = "UPDATE ".SFROLES." SET ";
	$sql.= 'role_name="'.$roledata['role_name'].'", ';
	$sql.= 'role_desc="'.$roledata['role_desc'].'", ';
	$sql.= 'role_actions="'.$actions.'" ';
	$sql.= "WHERE role_id=".$role_id.";";
    $success = $wpdb->query($sql);
    if ($success == false)
    {
        $mess = __("Permission Set Update Failed!", "sforum");
    } else {
        $mess = __("Permission Set Updated", "sforum");
		if ($old_actions['Can use private messaging'] != $new_actions['Can use private messaging'])
		{
			# get affected members
			$members = $wpdb->get_results("
				SELECT DISTINCT user_id
				FROM ".SFMEMBERSHIPS."
				WHERE usergroup_id IN (
					SELECT usergroup_id
					FROM ".SFPERMISSIONS."
					WHERE permission_role = ".$role_id."
				)
			");
			sfc_rebuild_members_pm($members);
		}
    }

    return $mess;
}

# function to remove a permission set role
function sfa_save_permissions_delete_role()
{
	global $wpdb;

    check_admin_referer('forum-adminform_roledelete', 'forum-adminform_roledelete');

    $role_id = sf_esc_int($_POST['role_id']);

	# get affected members
	$members = $wpdb->get_results("
		SELECT DISTINCT user_id
		FROM ".SFMEMBERSHIPS."
		WHERE usergroup_id IN (
			SELECT usergroup_id
			FROM ".SFPERMISSIONS."
			WHERE permission_role = ".$role_id."
		)
	");

    # remove all permission set that use the role we are deleting
    $permissions = $wpdb->get_results("SELECT * FROM ".SFPERMISSIONS." WHERE permission_role=".$role_id);
    if ($permissions)
    {
        foreach ($permissions as $permission)
        {
            sfa_remove_permission_data($permission->permission_id);
        }
    }

	# update the affected members pm flag
	sfc_rebuild_members_pm($members);

    # remove the permission set role
    $success = $wpdb->query("DELETE FROM ".SFROLES." WHERE role_id=".$role_id);
    if ($success == false)
    {
        $mess = __("Permission Set Deletion Failed!", "sforum");
    } else {
        $mess = __("Permission Set Deleted", "sforum");
    }

    return;
}

?>