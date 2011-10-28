<?php
/*
Simple:Press
Admin Forums Data Sae Support Functions
$LastChangedDate: 2011-01-08 08:42:25 -0700 (Sat, 08 Jan 2011) $
$Rev: 5277 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_save_forums_create_group()
{
	global $wpdb;

    check_admin_referer('forum-adminform_groupnew', 'forum-adminform_groupnew');

    $ug_list = array_unique($_POST['usergroup_id']);
    $perm_list = $_POST['role'];

    # fail if any user groups arent assigned a permission
	for ($x=0; $x<count($perm_list); $x++)
	{
		if ($perm_list[$x] == -1)
		{
	        $mess = __("All User Groups Must Be Assigned A Default Permission", "sforum");
    	    return $mess;
		}
	}

    $seq = ($wpdb->get_var("SELECT MAX(group_seq) FROM ".SFGROUPS) + 1);
    $groupdata = array();

    if (empty($_POST['group_name']))
    {
        $groupdata['group_name'] = __("New Forum Group", "sforum");
    } else
    {
        $groupdata['group_name'] = sf_filter_title_save(trim($_POST['group_name']));
    }
    if (empty($_POST['group_seq']))
    {
        $groupdata['group_seq'] = $seq;
    } else
    {
    	if (is_numeric($_POST['group_seq']))
    	{
 	       $groupdata['group_seq'] = sf_esc_int($_POST['group_seq']);
    	} else {
	        $mess = __("New Group Creation Failed - Sequence Must Be An Integer!", "sforum");
    		return $mess;
    	}
    }

	if (!empty($_POST['group_icon']))
	{
		# Check new icon exists
		$groupdata['group_icon'] = sf_filter_title_save(trim($_POST['group_icon']));
		$path = SFCUSTOM.$groupdata['group_icon'];
		if (!file_exists($path))
		{
			$mess = sprintf(__("Custom Icon '%s' does not exist", "sforum"), $groupdata['group_icon']);
			return $mess;
		}
	} else {
		$groupdata['group_icon'] = NULL;
	}

    $groupdata['group_desc'] = sf_filter_text_save(trim($_POST['group_desc']));
    $groupdata['group_message'] = sf_filter_text_save(trim($_POST['group_message']));

    # check if we need to shuffle sequence numbers
    if ($groupdata['group_seq'] < $seq)
    {
        $groups = sf_get_groups_all();
        foreach ($groups as $group)
        {
            if ($group->group_seq >= $groupdata['group_seq'])
            {
                sfa_bump_group_seq($group->group_id, ($group->group_seq + 1));
            }
        }
    }

    # create the group
	$sql ="INSERT INTO ".SFGROUPS." (group_name, group_desc, group_seq, group_icon, group_message) ";
	$sql.="VALUES ('".$groupdata['group_name']."', '".$groupdata['group_desc']."', ".$groupdata['group_seq'].", '".$groupdata['group_icon']."', '".$groupdata['group_message']."');";
    $success = $wpdb->query($sql);
    $group_id = $wpdb->insert_id;

 	# save the default permissions for the group
	for( $x=0; $x<count($ug_list); $x++)
	{
		sfa_add_defpermission_row($group_id, $ug_list[$x], $perm_list[$x]);
	}

    if ($success == false)
    {
        $mess = __("New Group Creation Failed!", "sforum");
    } else {
        $mess = __("New Forum Group Created", "sforum");
    }

    return $mess;
}

function sfa_save_forums_create_forum()
{
	global $wpdb;

    check_admin_referer('forum-adminform_forumnew', 'forum-adminform_forumnew');

    $forumdata = array();

	if($_POST['forumtype'] == 1)
	{
		# Standard forum
	    $forumdata['group_id'] = sf_esc_int($_POST['group_id']);
	} else {
		# Sub forum
		$parentforum = $wpdb->get_row("SELECT * FROM ".SFFORUMS." WHERE forum_id=".sf_esc_int($_POST['forum_id']));
		$forumdata['group_id'] = $parentforum->group_id;
	}

    $seq = $wpdb->get_var("SELECT MAX(forum_seq) FROM ".SFFORUMS." WHERE group_id=".$forumdata['group_id']) + 1;
    if(!isset($_POST['forum_seq']) || sf_esc_int($_POST['forum_seq'] == 0))
    {
    	$forumdata['forum_seq'] = $seq;
    } else {
		$forumdata['forum_seq'] = sf_esc_int($_POST['forum_seq']);
	}

    $forumdata['forum_desc'] = sf_filter_text_save(trim($_POST['forum_desc']));

    $forumdata['forum_status'] = 0;
    if (isset($_POST['forum_status']))
    {
        $forumdata['forum_status'] = 1;
	}

    $forumdata['post_ratings'] = 0;
    if (isset($_POST['sfforum_ratings']))
    {
        $forumdata['post_ratings'] = 1;
	}

    $forumdata['use_tags'] = 0;
    if (isset($_POST['forum_tags']))
    {
        $forumdata['use_tags'] = 1;
	}

    $forumdata['forum_rss_private'] = 0;
    if (isset($_POST['forum_private']))
    {
        $forumdata['forum_rss_private'] = 1;
    }

    $forumdata['forum_sitemap'] = 0;
    if (isset($_POST['forum_sitemap']))
    {
        $forumdata['forum_sitemap'] = 1;
    }

    if (empty($_POST['forum_name']))
    {
        $forumdata['forum_name'] = __("New Forum", "sforum");
    } else {
        $forumdata['forum_name'] = sf_filter_title_save(trim($_POST['forum_name']));
    }

    $forumdata['forum_message'] = sf_filter_text_save(trim($_POST['forum_message']));

	if (!empty($_POST['forum_icon']))
	{
		# Check new icon exists
		$forumdata['forum_icon'] = sf_filter_title_save(trim($_POST['forum_icon']));
		$path = SFCUSTOM.$forumdata['forum_icon'];
		if (!file_exists($path))
		{
			$mess = sprintf(__("Custom Icon '%s' does not exist", "sforum"), $forumdata['forum_icon']);
			return $mess;
		}
	} else {
		$forumdata['forum_icon'] = NULL;
	}

	if ($_POST['forum_topic_status'] == '' || $_POST['forum_topic_status'] == __('None', 'sforum'))
	{
		$forumdata['topic_status_set'] = 0;
	} else {
		$forumdata['topic_status_set'] = sf_esc_int($_POST['forum_topic_status']);
	}
    # check if we need to shuffle sequence numbers
    if ($forumdata['forum_seq'] < $seq)
    {
        $forums = sfa_get_forums_in_group($forumdata['group_id']);
        foreach ($forums as $forum)
        {
            if ($forum->forum_seq >= $forumdata['forum_seq'])
            {
                sfa_bump_forum_seq($forum->forum_id, ($forum->forum_seq + 1));
            }
        }
    }

    # create the forum
	if($_POST['forumtype'] == 2)
	{
		$parentdata = $parentforum->forum_id;
	} else {
		$parentdata = '0';
	}

	# do slug
	if(!isset($_POST['thisforumslug']) || empty($_POST['thisforumslug']))
	{
		$forumslug = sf_create_slug($forumdata['forum_name'], 'forum');
	} else {
		$forumslug = $_POST['thisforumslug'];
	}

	$sql = "INSERT INTO ".SFFORUMS." (forum_name, forum_slug, forum_desc, group_id, forum_status, forum_seq, forum_rss_private, forum_icon, topic_status_set, post_ratings, use_tags, parent, forum_message, in_sitemap) ";
	$sql.= "VALUES ('".$forumdata['forum_name']."', '".$forumslug."', '".$forumdata['forum_desc']."', ".$forumdata['group_id'].", ".$forumdata['forum_status'].", ".$forumdata['forum_seq'].", ".$forumdata['forum_rss_private'].", '".$forumdata['forum_icon']."', ".$forumdata['topic_status_set'].", ".$forumdata['post_ratings'].", ".$forumdata['use_tags'].", ".$parentdata.", '".$forumdata['forum_message']."', ".$forumdata['forum_sitemap'].");";
	$thisforum = $wpdb->query($sql);

	# now check the slug was populated and if not replace with forum id
	if (empty($forumslug))
	{
		$forumid = $wpdb->insert_id;
		$forumslug = 'forum-'.$forumid;
		$thisforum = $wpdb->query("UPDATE ".SFFORUMS." SET forum_slug='".$forumslug."' WHERE forum_id=".$forumid);
	}
    $success = $thisforum;
    $forum_id = $wpdb->insert_id;

	# Id subforum add it as child to the parent
	if($_POST['forumtype'] == 2)
	{
		$children = array();
		$list = $wpdb->get_results("SELECT forum_id FROM ".SFFORUMS." WHERE parent=".$parentforum->forum_id." ORDER BY forum_seq");
		foreach($list as $id)
		{
			$children[]=$id->forum_id;
		}
		$wpdb->query("UPDATE ".SFFORUMS." SET children='".serialize($children)."' WHERE forum_id=".$parentforum->forum_id);
	}

    # add the user group permission sets
    $usergroup_id_list = array_unique($_POST['usergroup_id']);
    $role_list = $_POST['role'];
	$perm_prob = false;
	for ($x=0; $x<count($usergroup_id_list); $x++)
	{
		$usergroup_id = sf_esc_int($usergroup_id_list[$x]);
		$role = $role_list[$x];
   		if ($role == -1)
	    {
			$defrole = sfa_get_defpermissions_role($forumdata['group_id'], $usergroup_id);
			if ($defrole == '')
			{
		    	$perm_prob = true;
		    } else {
				sfa_add_permission_data($forum_id, $usergroup_id, $defrole);
		    }
    	} else {
			sfa_add_permission_data($forum_id, $usergroup_id, $role);
		}
    }

	# get affected members
	$members = $wpdb->get_results("
		SELECT DISTINCT user_id
		FROM ".SFMEMBERSHIPS."
		WHERE usergroup_id IN (
			SELECT usergroup_id
			FROM ".SFPERMISSIONS."
			WHERE forum_id = ".$forum_id."
		)
	");
	sfc_rebuild_members_pm($members);

	# if the forum was created, signal success - doesnt check user group permission set though
    if ($success == false)
    {
        $mess = __("New Forum Creation Failed!", "sforum");
    } else {
		if ($perm_prob)
		{
        	$mess = __("New Forum Created - Permission Sets Not Properly Set For All User Groups!", "sforum");
	    } else {
        	$mess = __("New Forum Created!", "sforum");
		}
    }

	sfa_clean_forum_children();
	sfa_resequence_forums($forumdata['group_id'], 0);

    return $mess;
}

# function to add a permission set globally to all forum
function sfa_save_forums_global_perm()
{
	global $wpdb;

    check_admin_referer('forum-adminform_globalpermissionnew', 'forum-adminform_globalpermissionnew');

	if ($_POST['usergroup_id'] != -1 && $_POST['role'] != -1)
	{
	    $usergroup_id = sf_esc_int($_POST['usergroup_id']);
    	$permission = sf_esc_int($_POST['role']);

		# loop through all the groups
		$groups = sf_get_groups_all();
  	  	if ($groups)
  	  	{
  	  		$mess = '';
        	foreach ($groups as $group)
        	{
            	# use group permission set helper function to actually set the permission set
            	$mess.= sfa_set_group_permission($group->group_id, $usergroup_id, $permission);
            }

			#update pm flag
			$members = $wpdb->get_results("SELECT user_id FROM ".SFMEMBERSHIPS.' WHERE usergroup_id='.$usergroup_id);
			sfc_rebuild_members_pm($members);
        } else {
        	$mess = __("There are no Groups or Forum!  No Permission Set Added!", "sforum");
    	}
	} else {
		$mess = __("Adding User Group Permission Set Failed!", "sforum");
	}

    return $mess;
}

# function to add a permission set to every forum within a group
function sfa_save_forums_group_perm()
{
	global $wpdb;

    check_admin_referer('forum-adminform_grouppermissionnew', 'forum-adminform_grouppermissionnew');

	if(isset($_POST['group_id']) && $_POST['usergroup_id'] != -1 && $_POST['role'] != -1)
	{
	    $group_id = sf_esc_int($_POST['group_id']);
	    $usergroup_id = sf_esc_int($_POST['usergroup_id']);
	    $permission = sf_esc_int($_POST['role']);

		#update pm flag
	    $mess = sfa_set_group_permission($group_id, $usergroup_id, $permission);
		$members = $wpdb->get_results("SELECT user_id FROM ".SFMEMBERSHIPS.' WHERE usergroup_id='.$usergroup_id);
		sfc_rebuild_members_pm($members);
	} else {
		$mess = __("Adding User Group Permission Set Failed!", "sforum");
	}

	return $mess;
}

# helper function to loop through all forum in a group and add a permission set
function sfa_set_group_permission($group_id, $usergroup_id, $permission)
{
	global $wpdb;

    $forums = sfa_get_forums_in_group($group_id);

    if ($forums)
    {
    	$mess = '';
        foreach ($forums as $forum)
        {
            # If user group has a current permission set for this forum, remove the old one before adding the new one
            $current = $wpdb->get_row("SELECT * FROM ".SFPERMISSIONS." WHERE forum_id=".$forum->forum_id." AND usergroup_id=".$usergroup_id);

            if ($current)
            {
                sfa_remove_permission_data($current->permission_id);
            }

            # add the new permission set
            $success = sfa_add_permission_data($forum->forum_id, $usergroup_id, $permission);

            if ($success == false)
            {
                $mess.= sf_filter_title_display($forum->forum_name) . ": ". __("Adding User Group Permission Set Failed!", "sforum") . '<br />';
            } else {
                $mess.= sf_filter_title_display($forum->forum_name) . ": ". __("User Group Permission Set Added to Forum!", "sforum") . '<br />';
            }
        }
    } else {
        $mess = __("Group has no Members!  No Permission Sets Added!", "sforum");
    }

    return $mess;
}

# function to remove all permission set from all forum
function sfa_save_forums_remove_perms()
{
	global $wpdb;

    check_admin_referer('forum-adminform_allpermissionsdelete', 'forum-adminform_allpermissionsdelete');

	# remove all permission set
    $wpdb->query("TRUNCATE TABLE ".SFPERMISSIONS);

	# only admins can PM now
	$wpdb->query("UPDATE ".SFMEMBERS." SET pm = 0 WHERE admin = 0");

    $mess = __("All Permission Sets Removed.", "sforum");

    return $mess;
}

# function to add a new permission set to a forum
function sfa_save_forums_forum_perm()
{
	global $wpdb;

    check_admin_referer('forum-adminform_permissionnew', 'forum-adminform_permissionnew');

	if (isset($_POST['forum_id']) && $_POST['usergroup_id'] != -1 && $_POST['role'] != -1)
	{
		$usergroup_id = sf_esc_int($_POST['usergroup_id']);
		$forum_id = sf_esc_int($_POST['forum_id']);
		$permission = sf_esc_int($_POST['role']);

		# If user group has a current permission set for this forum, remove the old one before adding the new one
		$current = $wpdb->get_row("SELECT * FROM ".SFPERMISSIONS." WHERE forum_id=".$forum_id." AND usergroup_id=".$usergroup_id);

		if ($current)
		{
			sfa_remove_permission_data($current->permission_id);
		}

		# add the new permission set
		$success = sfa_add_permission_data($forum_id, $usergroup_id, $permission);
		if ($success == false)
		{
			$mess = __("Adding User Group Permission Set Failed!", "sforum");
		} else {
			$mess = __("User Group Permission Set Added to Forum", "sforum");
			$members = $wpdb->get_results("SELECT user_id FROM ".SFMEMBERSHIPS." WHERE usergroup_id = ".$usergroup_id." AND forum_id = ".$forum_id);
			sfc_rebuild_members_pm($members);
		}
	} else {
		$mess = __("Adding User Group Permission Set Failed!", "sforum");
	}

    return $mess;
}

function sfa_save_forums_delete_forum()
{
	global $wpdb;

    check_admin_referer('forum-adminform_forumdelete', 'forum-adminform_forumdelete');

    $group_id = sf_esc_int($_POST['group_id']);
    $forum_id = sf_esc_int($_POST['forum_id']);
    $cseq = sf_esc_int($_POST['cforum_seq']);

	# get affected members
	$members = $wpdb->get_results("
		SELECT DISTINCT user_id
		FROM ".SFMEMBERSHIPS."
		WHERE usergroup_id IN (
			SELECT usergroup_id
			FROM ".SFPERMISSIONS."
			WHERE forum_id = ".$forum_id."
		)
	");

	# If subforum or parent remove the relationship first.
	# Read the 'children' from the database because it is serialised
	$children = $wpdb->get_var("SELECT children FROM ".SFFORUMS." WHERE forum_id=".$forum_id);
	if($children)
	{
		$children = unserialize($children);
		foreach($children as $child)
		{
			$wpdb->query("UPDATE ".SFFORUMS." SET parent = null WHERE forum_id=".sf_esc_int($child));
		}
	}
	if($_POST['parent'])
	{
		$parentforum=$wpdb->get_var("SELECT children FROM ".SFFORUMS." WHERE forum_id=".sf_esc_int($_POST['parent']));
		$children = unserialize($parentforum);
		if(count($children) == 1)
		{
			$wpdb->query("UPDATE ".SFFORUMS." SET children = null WHERE forum_id=".sf_esc_int($_POST['parent']));
		} else {
			$newlist=array();
			foreach($children as $child)
			{
				if($child != $forum_id) $newlist[]=$child;
			}
			$wpdb->query("UPDATE ".SFFORUMS." SET children = '".serialize($newlist)."' WHERE forum_id=".sf_esc_int($_POST['parent']));
		}
	}

	# need to delete all topics in the forum using standard routine to clean up behind it
	$topics = $wpdb->get_results("SELECT topic_id FROM ".SFTOPICS." WHERE forum_id=".$forum_id);
	if ($topics)
	{
		foreach ($topics as $topic)
		{
			sf_delete_topic($topic->topic_id, false);
		}
	}

	# now delete the forum itself
	$wpdb->query("DELETE FROM ".SFFORUMS." WHERE forum_id=".$forum_id);

	# remove permissions for this forum
	$perms = sfa_get_forum_permissions($forum_id);
	if ($perms)
	{
		foreach ($perms as $perm)
		{
			sfa_remove_permission_data($perm->permission_id);
		}
	}

	# update pm flag for affected members
	sfc_rebuild_members_pm($members);

    # need to iterate through the groups
    $forums = sfa_get_forums_in_group($group_id);
    foreach ($forums as $forum)
    {
        if ($forum->forum_seq > $cseq)
        {
            sfa_bump_forum_seq($forum->forum_id, ($forum->forum_seq - 1));
        }
    }

	$mess = "Forum Deleted!";

	sfa_clean_forum_children();
	sfa_resequence_forums($group_id, 0);

    return $mess;
}

function sfa_save_forums_delete_group()
{
	global $wpdb;

    check_admin_referer('forum-adminform_groupdelete', 'forum-adminform_groupdelete');

    $group_id = sf_esc_int($_POST['group_id']);
    $cseq = sf_esc_int($_POST['cgroup_seq']);

	# get affected members
	$members = $wpdb->get_results("
		SELECT DISTINCT user_id
		FROM ".SFMEMBERSHIPS."
		WHERE usergroup_id IN (
			SELECT usergroup_id
			FROM ".SFPERMISSIONS."
			WHERE forum_id IN (
				SELECT forum_id
				FROM ".SFFORUMS."
				WHERE group_id = ".$group_id."
			)
		)
	");

	# remove permissions for each forum in group
	$forums = sfa_get_forums_in_group($group_id);
	if ($forums)
	{
		foreach ($forums as $forum)
		{
			# remove permissions for this forum
			$perms = sfa_get_forum_permissions($forum->forum_id);
			if ($perms)
			{
				foreach ($perms as $perm)
				{
					sfa_remove_permission_data($perm->permission_id);
				}
			}
		}
	}

	# rebuild pm column for affected members
	sfc_rebuild_members_pm($members);

	# select all the forums in the group
	$forums = sfa_get_forums_in_group($group_id);

	# remove the topics and posts in each forum
	foreach ($forums as $forum)
	{
		# need to delete all topics in the forum using standard routine to clean up behind it
		$topics = $wpdb->get_results("SELECT topic_id FROM ".SFTOPICS." WHERE forum_id=".$forum->forum_id);
		if ($topics)
		{
			foreach ($topics as $topic)
			{
				sf_delete_topic($topic->topic_id, false);
			}
		}
	}

	#now remove the forums themselves
	$wpdb->query("DELETE FROM ".SFFORUMS." WHERE group_id=".$group_id);
	# and finaly remove the group
	$wpdb->query("DELETE FROM ".SFGROUPS." WHERE group_id=".$group_id);

    # need to iterate through the groups
    $groups = sf_get_groups_all();
    foreach ($groups as $group)
    {
        if ($group->group_seq > $cseq)
        {
            sfa_bump_group_seq($group->group_id, ($group->group_seq - 1));
        }
    }

	# remove the default permissions for the group being deleted
	$wpdb->query("DELETE FROM ".SFDEFPERMISSIONS." WHERE group_id=".$group_id);

    $mess = __("Group Deleted", "sforum");

    return $mess;
}

# function to delete an existing permission set for a forum
function sfa_save_forums_delete_perm()
{
	global $wpdb;

    check_admin_referer('forum-adminform_permissiondelete', 'forum-adminform_permissiondelete');

    $permission_id = sf_esc_int($_POST['permission_id']);

	# get affected members
	$members = $wpdb->get_results("
		SELECT DISTINCT user_id
		FROM ".SFMEMBERSHIPS."
		WHERE usergroup_id = (
			SELECT usergroup_id
			FROM ".SFPERMISSIONS."
			WHERE permission_id = ".$permission_id."
		)
	");

	# remove the permission set from the forum
    $success = sfa_remove_permission_data($permission_id);
    if ($success == false)
    {
        $mess = __("Permission Set Delete Failed!", "sforum");
    } else {
        $mess = __("Permission Set Deleted", "sforum");

        # rebuild pm flag for affected members
		sfc_rebuild_members_pm($members);
    }

    return $mess;
}

function sfa_save_forums_edit_forum()
{
	global $wpdb;

    check_admin_referer('forum-adminform_forumedit', 'forum-adminform_forumedit');

    $forumdata = array();
    $forum_id = sf_esc_int($_POST['forum_id']);
    $forumdata['forum_name'] = sf_filter_title_save(trim($_POST['forum_name']));
	if(!empty($_POST['thisforumslug']))
	{
		$forumdata['forum_slug'] = sf_create_slug($_POST['thisforumslug'], 'forum', false);
	} else {
		$forumdata['forum_slug'] = sf_create_slug($forumdata['forum_name'], 'forum');
	}
    $forumdata['forum_desc'] = sf_filter_text_save(trim($_POST['forum_desc']));

	if(!isset($_POST['forum_seq']) || sf_esc_int($_POST['forum_seq'] == 0))
	{
		$mess = __("Unable to Save until Display Position is set", "sforum");
		return $mess;
	} else {
	    $forumdata['forum_seq'] = sf_esc_int($_POST['forum_seq']);
	}

    $forumdata['group_id'] = sf_esc_int($_POST['group_id']);

    $forumdata['forum_status'] = 0;
    if (isset($_POST['forum_status']))
    {
        $forumdata['forum_status'] = 1;
	}

    $forumdata['use_tags'] = 0;
    if (isset($_POST['forum_tags']))
    {
        $forumdata['use_tags'] = 1;
	}

    $forumdata['post_ratings'] = 0;
    if (isset($_POST['forum_ratings']))
    {
        $forumdata['post_ratings'] = 1;
    }

    $forumdata['forum_rss_private'] = 0;
    if (isset($_POST['forum_private']))
    {
        $forumdata['forum_rss_private'] = 1;
    }

    $forumdata['forum_sitemap'] = 0;
    if (isset($_POST['forum_sitemap']))
    {
        $forumdata['forum_sitemap'] = 1;
    }

	if (!empty($_POST['forum_icon']))
	{
		# Check new icon exists
		$forumdata['forum_icon'] = sf_filter_title_save(trim($_POST['forum_icon']));
		$path = SFCUSTOM.$forumdata['forum_icon'];
		if (!file_exists($path))
		{
			$mess = sprintf(__("Custom Icon '%s' does not exist", "sforum"), $forumdata['forum_icon']);
			return $mess;
		}
	} else {
		$forumdata['forum_icon'] = NULL;
	}

	if (isset($_POST['forum_topic_status']))
	{
		if ($_POST['forum_topic_status'] == '')
		{
			$forumdata['topic_status_set'] = 0;

			# remove from all topics in the forum
			$wpdb->query("UPDATE ".SFTOPICS." SET topic_status_flag=0 WHERE forum_id=".$forum_id);
		} else {
            $forumdata['topic_status_set'] = $_POST['forum_topic_status'];
		}
	} else {
		$forumdata['topic_status_set'] = sf_esc_int($_POST['cforum_topic_status']);
	}

	if (isset($_POST['forum_rss']))
	{
		$forumdata['forum_rss'] = sf_filter_save_cleanurl($_POST['forum_rss']);
	} else {
		$forumdata['forum_rss'] = sf_filter_save_cleanurl($_POST['cforum_rss']);
	}

    $forumdata['forum_message'] = sf_filter_text_save(trim($_POST['forum_message']));

    if (($forumdata['forum_name'] == sf_filter_title_display($_POST['cforum_name'])) &&
		($forumdata['forum_slug'] == $_POST['cforum_slug']) &&
		($forumdata['forum_seq'] == $_POST['cforum_seq']) &&
		($forumdata['group_id'] == $_POST['cgroup_id']) &&
		($forumdata['forum_status'] == $_POST['cforum_status']) &&
		($forumdata['use_tags'] == $_POST['cforum_tags']) &&
		($forumdata['post_ratings'] == $_POST['cforum_ratings']) &&
		($forumdata['forum_rss_private'] == $_POST['cforum_rss_private'])  &&
		($forumdata['forum_desc'] == $_POST['cforum_desc']) &&
		($forumdata['forum_icon'] == $_POST['cforum_icon']) &&
		($forumdata['forum_topic_status'] == $_POST['cforum_topic_status']) &&
		($forumdata['forum_message'] == $_POST['cforum_message']) &&
		($forumdata['forum_sitemap'] == $_POST['cforum_sitemap']) &&
		($forumdata['forum_rss'] == $_POST['cforum_rss']))
    {
        $mess = __("No Data Changed", "sforum");
        return $mess;
    }

    # has the forum changed to a new group
    if ($forumdata['group_id'] != $_POST['cgroup_id'])
    {
        # let's resequence old group list first
        $forums = $wpdb->get_results("SELECT forum_id, forum_seq FROM ".SFFORUMS." WHERE group_id=".sf_esc_int($_POST['cgroup_id'])." AND forum_id <> ".$forum_id." ORDER BY forum_seq;");
        $cnt = count($forums);
        for ($i = 0; $i < $cnt; $i++)
        {
            sfa_bump_forum_seq($forums[$i]->forum_id, ($i + 1));
        }

        # now we can make room in new group
        $seq = $wpdb->get_var("SELECT MAX(forum_seq) FROM ".SFFORUMS." WHERE group_id=". $forumdata['group_id']) + 1;
        if ($forumdata['forum_seq'] < $seq)
        {
            $forums = sfa_get_forums_in_group($forumdata['group_id']);
            foreach ($forums as $forum)
            {
                if ($forum->forum_seq >= $forumdata['forum_seq'])
                {
                    sfa_bump_forum_seq($forum->forum_id, ($forum->forum_seq + 1));
                }
            }
        }
    } else {
        # same group but has the seq changed?
        if ($forumdata['forum_seq'] != $_POST['cforum_seq'])
        {
            $forums = $wpdb->get_results("SELECT forum_id, forum_seq FROM ".SFFORUMS." WHERE group_id=".sf_esc_int($_POST['cgroup_id'])." AND forum_id <> ".$forum_id." ORDER BY forum_seq;");
            $cnt = count($forums);
            for ($i = 0; $i < $cnt; $i++)
            {
                if (($i + 1) < $forumdata['forum_seq'])
                {
                    sfa_bump_forum_seq($forums[$i]->forum_id, ($i + 1));
                } else {
                    sfa_bump_forum_seq($forums[$i]->forum_id, ($i + 2));
                }
            }
        }
    }

    # Finally - we can save the updated forum record!
	if(empty($forumdata['forum_slug']))
	{
		$forumslug = sf_create_slug($forumdata['forum_name'], 'forum');
		if(empty($forumslug)) $forumslug = 'forum-'.$forum_id;
	} else {
		$forumslug = $forumdata['forum_slug'];
	}

	# Let's make sure parent is set
	if($_POST['forumtype'] == 1)
	{
		$parent = 0;
	} else {
		$parent = sf_esc_int($_POST['forum_parent']);
	}

	$sql = "UPDATE ".SFFORUMS." SET ";
	$sql.= 'forum_name="'.$forumdata['forum_name'].'", ';
	$sql.= 'forum_slug="'.$forumslug.'", ';
	$sql.= 'forum_desc="'.$forumdata['forum_desc'].'", ';
	$sql.= 'group_id='.$forumdata['group_id'].', ';
	$sql.= 'forum_status='.$forumdata['forum_status'].', ';
	$sql.= 'use_tags='.$forumdata['use_tags'].', ';
	$sql.= 'post_ratings='.$forumdata['post_ratings'].', ';
	$sql.= 'forum_rss_private='.$forumdata['forum_rss_private'].', ';
	$sql.= 'forum_icon="'.$forumdata['forum_icon'].'", ';
	$sql.= 'topic_status_set='.$forumdata['topic_status_set'].', ';
	$sql.= 'forum_rss="'.$forumdata['forum_rss'].'", ';
	$sql.= 'in_sitemap="'.$forumdata['forum_sitemap'].'", ';
	$sql.= 'parent='.$parent.', ';
	$sql.= 'forum_message="'.$forumdata['forum_message'].'", ';
	$sql.= 'forum_seq='.$forumdata['forum_seq']." ";
	$sql.= "WHERE forum_id=".$forum_id.";";
    $success = $wpdb->query($sql);
    if ($success == false)
    {
        $mess = __("Forum Record Update Failed!", "sforum");
    } else {
        $mess = __("Forum Record Updated", "sforum");
    }

	sfa_clean_forum_children();
	sfa_resequence_forums($forumdata['group_id'], 0);

    return $mess;
}

function sfa_save_forums_edit_group()
{
	global $wpdb;

    check_admin_referer('forum-adminform_groupedit', 'forum-adminform_groupedit');

    $groupdata = array();
    $group_id = sf_esc_int($_POST['group_id']);
    $groupdata['group_name'] = sf_filter_title_save(trim($_POST['group_name']));
    $groupdata['group_seq'] = sf_filter_title_save(trim($_POST['group_seq']));
    $groupdata['group_desc'] = sf_filter_text_save(trim($_POST['group_desc']));
    $groupdata['group_message'] = sf_filter_text_save(trim($_POST['group_message']));

    $ug_list = array_unique($_POST['usergroup_id']);
    $perm_list = $_POST['role'];

	if (!empty($_POST['group_icon']))
	{
		# Check new icon exists
		$groupdata['group_icon'] = sf_filter_title_save(trim($_POST['group_icon']));
		$path = SFCUSTOM.$groupdata['group_icon'];
		if (!file_exists($path))
		{
			$mess = sprintf(__("Custom Icon '%s' does not exist", "sforum"), $groupdata['group_icon']);
			return $mess;
		}
	} else {
		$groupdata['group_icon'] = NULL;
	}

	if (isset($_POST['group_rss']))
	{
		$groupdata['group_rss'] = sf_filter_save_cleanurl($_POST['group_rss']);
	} else {
		$groupdata['group_rss'] = sf_filter_save_cleanurl($_POST['cgroup_rss']);
	}

    # fail if any user groups arent assigned a permission
	for ($x=0; $x<count($perm_list); $x++)
	{
		if ($perm_list[$x] == -1)
		{
	        $mess = __("All User Groups Must Be Assigned A Default Permission", "sforum");
    	    return $mess;
		}
	}

	# save the default permissions for the group
	for ($x=0; $x<count($ug_list); $x++)
	{
		if (sfa_get_defpermissions_role($group_id, $ug_list[$x]))
		{
			$sql = "
				UPDATE ".SFDEFPERMISSIONS."
				SET permission_role=$perm_list[$x]
				WHERE group_id=$group_id AND usergroup_id=$ug_list[$x]";
			$wpdb->query($sql);
		} else {
			sfa_add_defpermission_row($group_id, $ug_list[$x], $perm_list[$x]);
		}
	}

    if ($groupdata['group_name'] == $_POST['cgroup_name'] &&
		$groupdata['group_seq'] == $_POST['cgroup_seq'] &&
		$groupdata['group_desc'] == $_POST['cgroup_desc'] &&
		$groupdata['group_rss'] == $_POST['cgroup_rss'] &&
		$groupdata['group_message'] == $_POST['cgroup_message'] &&
		$groupdata['group_icon'] == $_POST['cgroup_icon'])
    {
        $mess = __("No Data Changed", "sforum");
    } else {
	    # has the sequence changed?
	    if ($groupdata['group_seq'] != $_POST['cgroup_seq'])
	    {
	        # need to iterate through the groups to change sequence number
	        $groups = $wpdb->get_results("SELECT group_id, group_seq FROM ".SFGROUPS." WHERE group_id <> ".$group_id." ORDER BY group_seq;");
	        $cnt = count($groups);
	        for ($i = 0; $i < $cnt; $i++)
	        {
	            if (($i + 1) < $groupdata['group_seq'])
	            {
	                sfa_bump_group_seq($groups[$i]->group_id, ($i + 1));
	            } else {
	                sfa_bump_group_seq($groups[$i]->group_id, ($i + 2));
	            }
	        }
	    }

		$sql = "UPDATE ".SFGROUPS." SET ";
		$sql.= 'group_name="'.$groupdata['group_name'].'", ';
		$sql.= 'group_desc="'.$groupdata['group_desc'].'", ';
		$sql.= 'group_icon="'.$groupdata['group_icon'].'", ';
		$sql.= 'group_rss="'.$groupdata['group_rss'].'", ';
		$sql.= 'group_message="'.$groupdata['group_message'].'", ';
		$sql.= 'group_seq='.$groupdata['group_seq']." ";
		$sql.= "WHERE group_id=".$group_id.";";
	    $success = $wpdb->query($sql);
	    if ($success == false)
	    {
	        $mess = __("Group Record Update Failed!", "sforum");
	    } else {
	        $mess = __("Forum Group Record Updated", "sforum");
	    }
    }

    return $mess;
}

# function to update an existing permission set for a forum
function sfa_save_forums_edit_perm()
{
	global $wpdb;

    check_admin_referer('forum-adminform_permissionedit', 'forum-adminform_permissionedit');

    $permissiondata = array();
    $permission_id = sf_esc_int($_POST['permission_id']);
    $permissiondata['permission_role'] = sf_esc_int($_POST['role']);

    # dont do anything if the permission set wasnt actually updated
    if ($permissiondata['permission_role'] == $_POST['ugroup_perm'])
    {
        $mess = __("No Data Changed", "sforum");
        return;
    }

	# save the updated permission set info
	$sql = "UPDATE ".SFPERMISSIONS." SET ";
	$sql.= 'permission_role="'.$permissiondata['permission_role'].'" ';
	$sql.= "WHERE permission_id=".$permission_id.";";
    $success = $wpdb->query($sql);
    if ($success == false)
    {
        $mess = __("Permission Set Update Failed!", "sforum");
    } else {
        $mess = __("Permission Set Updated", "sforum");

		# get affected members
		$members = $wpdb->get_results("
			SELECT DISTINCT user_id
			FROM ".SFMEMBERSHIPS."
			WHERE usergroup_id = (
				SELECT usergroup_id
				FROM ".SFPERMISSIONS."
				WHERE permission_id = ".$permission_id."
			)
		");
		sfc_rebuild_members_pm($members);
    }

    return $mess;
}

function sfa_bump_group_seq($id, $seq)
{
	global $wpdb;

	$sql = "UPDATE ".SFGROUPS." SET ";
	$sql.= 'group_seq='.$seq." ";
	$sql.= "WHERE group_id=".$id.";";

	$wpdb->query($sql);
	return;
}

function sfa_bump_forum_seq($id, $seq)
{
	global $wpdb;

	$sql = "UPDATE ".SFFORUMS." SET ";
	$sql.= 'forum_seq='.$seq." ";
	$sql.= "WHERE forum_id=".$id.";";

	$wpdb->query($sql);
	return;
}

function sfa_add_permission_data($forum_id, $usergroup_id, $permission)
{
	global $wpdb;

	$forumid = esc_sql($forum_id);
	$usergroupid = esc_sql($usergroup_id);
	$perm = esc_sql($permission);

	$sql ="INSERT INTO ".SFPERMISSIONS." (forum_id, usergroup_id, permission_role) ";
	$sql.="VALUES ('".$forumid."', '".$usergroupid."', '".$perm."');";

	return $wpdb->query($sql);
}

function sfa_add_defpermission_row($group_id, $usergroup_id, $role)
{
	global $wpdb;

	$sql = "
		INSERT INTO ".SFDEFPERMISSIONS."
		(group_id, usergroup_id, permission_role)
		VALUES
		($group_id, $usergroup_id, $role)";

	return $wpdb->query($sql);
}

function sfa_resequence_forums($groupid, $parent)
{
	global $sequence;

	$forums = sfa_get_group_forums_by_parent($groupid, $parent);

	if($forums)
	{
		foreach ($forums as $forum)
		{
			$sequence++;
			sfa_bump_forum_seq($forum->forum_id, $sequence);

			if($forum->children)
			{
				$childlist = array(unserialize($forum->children));
				if(count($childlist) > 0)
				{
					sfa_resequence_forums($groupid, $forum->forum_id);
				}
			}
		}
	}
	return;
}

function sfa_clean_forum_children()
{
	global $wpdb;

	# Remove all chil records from forums
	$wpdb->query("UPDATE ".SFFORUMS." set children=''");

	# Now get ALL forums
	$forums = $wpdb->get_results("SELECT forum_id, parent FROM ".SFFORUMS);
	if($forums)
	{
		foreach($forums as $forum)
		{
			if($forum->parent != 0)
			{
				$childlist = $wpdb->get_row("SELECT group_id, children FROM ".SFFORUMS." WHERE forum_id=".$forum->parent);
				if($childlist->children)
				{
					$children=unserialize($childlist->children);
				} else {
					$children = array();
				}
				$children[]=$forum->forum_id;
				$wpdb->query("UPDATE ".SFFORUMS." set children='".serialize($children)."' WHERE forum_id=".$forum->parent);
				$wpdb->query("UPDATE ".SFFORUMS." set group_id=".$childlist->group_id." WHERE forum_id=".$forum->forum_id);
			}
		}
	}
	return;
}

function sfa_save_forums_global_rss()
{
	global $wpdb;

    check_admin_referer('forum-adminform_globalrss', 'forum-adminform_globalrss');

	# update the globla rss replacement url
	sf_update_option('sfallRSSurl', sf_filter_save_cleanurl($_POST['sfallrssurl']));
    $mess = __("Global RSS Settings Updated!", "sforum");

    return $mess;
}

function sfa_save_forums_global_rssset()
{
	global $wpdb;

    check_admin_referer('forum-adminform_globalrssset', 'forum-adminform_globalrssset');

    $private = sf_esc_int($_POST['sfglobalrssset']);

	$sql = "UPDATE ".SFFORUMS." SET ";
	$sql.= 'forum_rss_private='.$private;
	$success = $wpdb->query($sql);

    $mess = __("Global RSS Settings Updated!", "sforum");

    return $mess;
}

?>