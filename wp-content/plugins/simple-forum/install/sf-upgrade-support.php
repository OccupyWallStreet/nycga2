<?php
/*
Simple:Press
Install & Upgrade Support Routines
$LastChangedDate: 2010-10-24 05:29:01 -0700 (Sun, 24 Oct 2010) $
$Rev: 4821 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

include_once(SF_PLUGIN_DIR.'/library/sf-common-functions.php');

# ==========================================
#
# GLOBAL UPDATE/INSTALL ROUTINES
#
# ==========================================


# Called from V4.1 onwards to log uodates.
function sf_log_event($release, $version, $build)
{
	global $wpdb, $current_user;

	$now = current_time('mysql');

	$sql = "
		INSERT INTO ".SFLOG." (user_id, install_date, release_type, version, build)
		VALUES (
		".$current_user->ID.",
		'".$now."',
		'".$release."',
		'".$version."',
		".$build.");";
	$wpdb->query($sql);

	sf_update_option('sfversion', $version);
	sf_update_option('sfbuild', $build);

	return;
}

# Upgrade Database wrapper
function sf_upgrade_database($table_name, $column_name, $create_ddl)
{
	global $wpdb;


	foreach ($wpdb->get_col("DESC $table_name", 0) as $column )
	{
		if ($column == $column_name)
		{
			return true;
		}
    }
	# didn't find it try to create it.
    $q = $wpdb->query($create_ddl);

	# we cannot directly tell that whether this succeeded!
	foreach ($wpdb->get_col("DESC $table_name", 0) as $column )
	{
		if ($column == $column_name)
		{
			return true;
		}
	}
	die(sprintf(__("DATABASE ERROR: Unable to ALTER the %s to create new column %s", "sforum"), $table_name, $column));
}

function sf_charset()
{
	global $wpdb;

	$charset='';

	if ( ! empty($wpdb->charset) )
	{
		$charset = "DEFAULT CHARSET $wpdb->charset";
	} else {
		$charset = "DEFAULT CHARSET utf8";
	}

	return $charset;
}

# ==========================================
#
# VERSION SPECIFIC UPDATE/INSTALL ROUTINES
#
# ==========================================

# Called by 1.6 to clear up previous deletion orphans
function sf_check_data_integrity()
{
	global $wpdb;

	$topiclist = array();
	$postlist = array();

	# to be run against a 1.5 install to clean up orphaned posts
	# Step 1: Loop through topics in case forum is gone and remove
	$topics = $wpdb->get_results("SELECT topic_id, forum_id FROM ".SFTOPICS);
	if($topics)
	{
		foreach($topics as $topic)
		{
			$test=$wpdb->get_col("SELECT forum_id FROM ".SFFORUMS." WHERE forum_id=".$topic->forum_id);
			if(!$test)
			{
				$topiclist[]=$topic->topic_id;
			}
		}
		if($topiclist)
		{
			foreach($topiclist as $topic)
			{
				$wpdb->query("DELETE FROM ".SFTOPICS." WHERE topic_id=".$topic);
			}
		}
	}

	# Step 2: Loop through posts in case topic is gone and remove
	$posts = $wpdb->get_results("SELECT post_id, topic_id FROM ".SFPOSTS);
	if($posts)
	{
		foreach($posts as $post)
		{
			$test=$wpdb->get_col("SELECT topic_id FROM ".SFTOPICS." WHERE topic_id=".$post->topic_id);
			if(!$test)
			{
				$postlist[]=$post->post_id;
			}
		}
		if($postlist)
		{
			foreach($postlist as $post)
			{
				$wpdb->query("DELETE FROM ".SFPOSTS." WHERE post_id=".$post);
			}
		}
	}
	return;
}

# Called by 1.7 to re-route subscriptions from usermeta to topics
function sf_rebuild_subscriptions()
{
	global $wpdb;

	# Build a list of users with subscribe set
	$users = $wpdb->get_col("SELECT user_id FROM ".SFUSERMETA." WHERE meta_key='".$wpdb->prefix."sfsubscribe'");
	if($users)
	{
		# clear out the old sfsubcribe values ready for the new
		$wpdb->query("DELETE FROM ".SFUSERMETA." WHERE meta_key='".$wpdb->prefix."sfsubscribe'");

		foreach($users as $user)
		{
			# now build the list of topics into which each user has posted
			$topics = $wpdb->get_col("SELECT DISTINCT topic_id FROM ".SFPOSTS." WHERE user_id=".$user);
			if($topics)
			{
				foreach($topics as $topic)
				{
					sf_save_subscription($topic, $user, false);
				}
			}
		}
	}
	return;
}

# Called by 2.0 to clean up the topic subs lists where duplicates have crept in
function sf_clean_topic_subs()
{
	global $wpdb;

	# build list of topics with subscriptions
	$topics = $wpdb->get_results("SELECT topic_id, topic_subs FROM ".SFTOPICS." WHERE topic_subs IS NOT NULL;");
	if(!$topics) return;

	foreach($topics as $topic)
	{
		$nvalues = array();
		$cvalues = explode('@', $topic->topic_subs);
		$nvalues[0] = $cvalues[0];
		foreach($cvalues as $cvalue)
		{
			$notfound = true;
			foreach($nvalues as $nvalue)
			{
				if($nvalue == $cvalue) $notfound = false;
			}
			if($notfound) $nvalues[]=$cvalue;
		}
		$nvaluelist = implode('@', $nvalues);
		$wpdb->query("UPDATE ".SFTOPICS." SET topic_subs='".$nvaluelist."' WHERE topic_id=".$topic->topic_id);
	}
	return;
}

function sf_relocate_avatars()
{
	global $wpdb;

	$basepath='/';
	if (is_multisite()) $basepath = '/blogs.dir/' . $wpdb->blogid .'/files/';

	$success = 0;
	$newpath = SF_STORE_DIR . $basepath . 'forum-avatars';
	$oldpath = SF_PLUGIN_DIR . '/styles/avatars';

	# check if new folder does not exist - which it shouldn't!
	if(!is_dir($newpath))
	{
		if(!is_writable(SF_STORE_DIR) || !($dir = @mkdir($newpath, 0777)))
		{
			$success = 1;
			return $success;
		}
		if (!is_writable($newpath))
		{
			$success = 2;
			return $success;
		}
		if(is_dir($newpath))
		{
			$avlist = opendir($oldpath);
			while (false !== ($file = readdir($avlist)))
			{
				if (is_file($oldpath.'/'.$file) && $file != "." && $file != "..")
				{
					if(!file_exists($newpath.'/'.$file))
					{
						if(@copy($oldpath.'/'.$file, $newpath.'/'.$file) == false)
						{
							$success = 3;
							break;
						}
					}
				}
			}
			closedir($avlist);
		}
	}
	return $success;
}

# Called by 2.1 to correct old timestamp in usermeta (sflast)
function sf_correct_sflast()
{
	global $wpdb;

	$sql = "UPDATE ".SFUSERMETA." SET meta_value=now() WHERE meta_key = '".$wpdb->prefix."sflast' AND meta_value < DATE_SUB(CURDATE(), INTERVAL 1 YEAR);";
	$wpdb->query($sql);
	return;
}

# Called by 2.1 Patch 2 to pre-create last visited date for all existing users who don't have one - Corrects the zero problem
function sf_precreate_sflast()
{
	global $wpdb;

	$users = $wpdb->get_results("SELECT ID FROM ".SFUSERS);
	if($users)
	{
		foreach($users as $user)
		{
			$check = $wpdb->get_var("SELECT umeta_id FROM ".SFUSERMETA." WHERE meta_key='".$wpdb->prefix."sflast' AND user_id=".$user->ID);
			if(!$check)
			{
				sf_set_last_visited($user->ID);
			}
		}
	}
	return;
}

# Called by 3.0 to create forum and topic slugs
function sf_create_slugs()
{
	global $wpdb;

	# forums
	$records=$wpdb->get_results("SELECT forum_id, forum_name, forum_slug FROM ".SFFORUMS);
	if($records)
	{
		foreach($records as $record)
		{
			$title = sf_create_slug($record->forum_name, 'forum');
			if(empty($title))
			{
				$title = 'forum-'.$record->forum_id;
			}
			$wpdb->query("UPDATE ".SFFORUMS." SET forum_slug='".$title."' WHERE forum_id=".$record->forum_id);
		}
	}

	# topics
	$records=$wpdb->get_results("SELECT topic_id, topic_name, topic_slug FROM ".SFTOPICS);
	if($records)
	{
		foreach($records as $record)
		{
			$title = sf_create_slug($record->topic_name, 'topic');
			if(empty($title))
			{
				$title = 'topic-'.$record->topic_id;
			}
			$wpdb->query("UPDATE ".SFTOPICS." SET topic_slug='".$title."' WHERE topic_id=".$record->topic_id);
		}
	}
	return;
}

# Called by 3 to ensure all users have a display name set
function sf_check_all_display_names()
{
	global $wpdb;

	$users = $wpdb->get_results("SELECT ID, user_login, display_name FROM ".SFUSERS." WHERE display_name=''");
	if($users)
	{
		foreach($users as $user)
		{
			$wpdb->query("UPDATE ".SFUSERS." SET display_name='".$user->login_name."' WHERE ID=".$user->ID);
		}
	}
	return;
}

# Called by 3.0 to set up all users into default usergroups
# And then set all 3 usergroups to all forums by default
function sf_setup_usergroup_data($membergroup, $moderatorgroup, $upgrade, $keys)
{
	global $wpdb, $current_user;

	# if upgrade check if any moderators
	$modusers = '';
	if ($upgrade) $modusers = get_option('sfmodusers');
	if (!empty($modusers))
	{
		$modusers = explode(';', get_option('sfmodusers'));
	}

	# get the list of users and do the stuff
	$userlist = $wpdb->get_results("SELECT ID FROM ".SFUSERS." ORDER BY display_name ASC;");
	if($userlist)
	{
		foreach($userlist as $user)
		{
			# check it's not the admin
			if($user->ID != $current_user->ID)
			{
				$target = $membergroup;
				# is user a moderator?
				if(!empty($modusers))
				{
					if(in_array($user->ID, $modusers)) $target = $moderatorgroup;
				}
				$memberships = get_user_meta($user->ID, 'sfusergroup', true);
				$memberships[] = $target;
				update_user_meta($user->ID, 'sfusergroup', $memberships);
			}
		}
	}

	# Now to assign all 3 default usergroups to all forums
	if(($keys) && ($upgrade))
	{
		$forums = $wpdb->get_results("SELECT forum_id FROM ".SFFORUMS.";");
		if($forums)
		{
			foreach($forums as $forum)
			{
				for($x=0; $x<count($keys); $x++)
				{
					$group = $keys[$x]['usergroup'];
					$perm  = $keys[$x]['permission'];

					$sql ="INSERT INTO ".SFPERMISSIONS." (forum_id, usergroup_id, permission_role) ";
					$sql.="VALUES (".$forum->forum_id.", ".$group.", ".$perm.");";
					$wpdb->query($sql);
				}
			}
		}
	}
	return;
}

# called by 3.1 (?) to build new last post columns, topic post count and post index
function sf_build_lastposts()
{
	global $wpdb;

	$forums = sf_get_forums_all(true);
	if($forums)
	{
		foreach($forums as $forum)
		{
			sf_build_forum_index($forum->forum_id);
		}
	}

	$topics = sf_get_topics_all(true);
	if($topics)
	{
		foreach($topics as $topic)
		{
			sf_build_post_index($topic->topic_id, $topic->topic_slug);
		}
	}
	return;
}

# called by 3.1 upgrade to build members table
# should also now work correctly for individual network sites
# sends $editor_column as install and upgrade have different column names (don't ask!)
function sf_build_members_table($editor_column, $type, $subphase=0)
{
	global $wpdb, $current_user;

	# get limits for installs
	if ($subphase != 0)
	{
		$limit = " LIMIT 250 OFFSET ".(($subphase - 1) * 250);
	}

	# select all users
	$sql =
		"SELECT ID, display_name, user_login FROM ".SFUSERMETA."
		 RIGHT JOIN ".SFUSERMETA." ON ".SFUSERMETA.".ID = ".SFUSERMETA.".user_id
		 WHERE meta_key = '".$wpdb->prefix."capabilities'
		 ORDER BY ID".$limit;
	$members = $wpdb->get_results($sql);

	if($members)
	{
		# grab the user groups so we can ensure the users settings are coprrect and groups exist
		$ugs = $wpdb->get_col("SELECT usergroup_id FROM ".SFUSERGROUPS);
		foreach($members as $member)
		{
			# Check ID exists and is not zero
			if(is_numeric($member->ID) && $member->ID > 0)
			{
				$usergroups = array();
				$usergroups = get_user_meta($member->ID, 'sfusergroup', true);

				# user group handling - check groups exist
				$newgrouplist=array();
				if($usergroups)
				{
					foreach($usergroups as $group)
					{
						if(in_array($group, $ugs))
						{
							$newgrouplist[] = (string) $group;
						}
					}
				} else {
					$newgrouplist[] = (string) get_option('sfdefgroup');
				}
				$usergroups = serialize($newgrouplist);

				# admins dont get user groups
				# forum admin not set up yet for installs
				if ($type == 'upgrade')
				{
					if (sf_is_forum_admin($member->ID)) $usergroups = '';
				} else {
					if ($current_user->ID == $member->ID) $usergroups = '';
				}

				# remaining data items
				$display_name = sf_filter_name_save($member->display_name);
				if(empty($display_name))
				{
					$display_name = sf_filter_name_save($member->user_login);
				}
				$display_name = sf_filter_name_save($display_name);

				$buddies = array();
				$avatar     = esc_sql(get_user_meta($member->ID, 'sfavatar', true));
				$signature  = esc_sql(get_user_meta($member->ID, 'signature', true));
				$sigimage   = esc_sql(get_user_meta($member->ID, 'sigimage', true));
				$posts      = get_user_meta($member->ID, 'sfposts', true);
				$lastvisit  = get_user_meta($member->ID, 'sflast', true);
				$subscribe  = get_user_meta($member->ID, 'sfsubscribe', true);
				$buddies    = get_user_meta($member->ID, 'sfbuddies', true);
				$pm         = sf_get_user_pm_status($member->ID, $newgrouplist);
				$moderator	= sf_get_user_mod_status($member->ID, $newgrouplist);

				$signature = sf_filter_text_save(trim($signature));
				$sigimage = sf_filter_title_save(trim($sigimage));

				$buddies    = serialize($buddies);
				if(!$posts) $posts = '0';

				$editor_setting = get_user_meta($member->ID, 'sfuse_quicktags', true);
				if(empty($editor_setting))
				{
					if($editor_column == 'quicktags') $editor_setting = 0;
					if($editor_column == 'editor') $editor_setting = 1;
				}

				if ($type == 'upgrade')
				{
					$sql ="INSERT INTO ".SFMEMBERS." (user_id, display_name, pm, moderator, {$editor_column}, usergroups, avatar, signature, sigimage, posts, lastvisit, subscribe, buddies) ";
					$sql.="VALUES ({$member->ID}, '{$display_name}', {$pm}, {$moderator}, {$editor_setting}, '{$usergroups}', '{$avatar}', '{$signature}', '{$sigimage}', {$posts}, '{$lastvisit}', '{$subscribe}', '{$buddies}');";
				} else {
					$sql ="INSERT INTO ".SFMEMBERS." (user_id, display_name, pm, moderator, avatar, signature, sigimage, posts, lastvisit, subscribe, buddies, newposts, checktime, admin, watches, posts_rated, admin_options) ";
					$sql.="VALUES ({$member->ID}, '{$display_name}', {$pm}, {$moderator}, '{$avatar}', '{$signature}', '{$sigimage}', {$posts}, now(), '{$subscribe}', '{$buddies}', '', now(), 0, '', '', '');";

					$memberships = unserialize($usergroups);
					if ($memberships)   # will be empty for admin
					{
						foreach ($memberships as $membership)
						{
							sfc_add_membership($membership, $member->ID);
						}
					}

					$useroptions = array();
					$useroptions['pmemail'] = true;
					$useroptions['editor'] = $editor_setting;
					sf_update_member_item($member->ID, 'user_options', $useroptions);
				}
				$wpdb->query($sql);

				# now remove the old userfmeta entries for the current member
				$optionlist = array("sfavatar", "sfposts", "sfsubscribe", "sflast", "sfnewposts", "sfchecktime", "sfbuddies", "sfusergroup", "signature", "sigimage", "sfuse_quicktags");
				foreach($optionlist as $option)
				{
					$wpdb->query("DELETE FROM ".$wpdb->prefix."usermeta WHERE meta_key='".$option."' AND user_id=".$member->ID.";");
				}
			}
		}
	}
	return;
}

# support function
function sf_get_user_pm_status($user_id, $usergroups)
{
	global $wpdb, $current_user;

	if ($current_user->ID == $user_id) return '1';  # if user id is current user then its admin
	if (empty($usergroups)) return '0';

	foreach ($usergroups as $usergroup)
	{
		$rids = $wpdb->get_results("SELECT permission_role FROM ".SFPERMISSIONS." WHERE usergroup_id='".$usergroup."'");
		foreach ($rids as $rid) {
			$role_actions = $wpdb->get_var("SELECT role_actions FROM ".SFROLES." WHERE role_id='".$rid->permission_role."'");
			$actions = maybe_unserialize($role_actions);
			if ($actions['Can use private messaging'] == 1)
			{
				return '1';
			}
		}
	}
	return '0';
}

# support function
function sf_get_user_mod_status($user_id, $usergroups)
{
	global $wpdb, $current_user;

	if (empty($usergroups)) return '0';

	foreach ($usergroups as $usergroup)
	{
		$mod = $wpdb->get_var("SELECT usergroup_is_moderator FROM ".SFUSERGROUPS." WHERE usergroup_id = ".$usergroup);
		if($mod) return '1';
	}
	return '0';
}

# support function for adding new role.  use first two params for fixed value.  third param will override second
function sf_upgrade_add_new_role($newaction, $perm, $limit_access=false, $mods_only=false, $no_access=false)
{
	global $wpdb;

	$roles = $wpdb->get_results("SELECT * FROM ".SFROLES." ORDER BY role_id");
	if ($roles)
	{
		foreach ($roles as $role)
		{
			if ($no_access)
			{
				$perm = 1;
				if ($role->role_name == 'No Access')
				{
					$perm = 0;
				}
			}
			if ($limit_access)
			{
				$perm = 1;
				if ($role->role_name == 'No Access' || $role->role_name == 'Read Only Access')
				{
					$perm = 0;
				}
			}
			if ($mods_only)
			{
				$perm = 0;
				if ($role->role_name == 'Moderators')
				{
					$perm = 1;
				}
			}
			$actions = unserialize($role->role_actions);
			$actions[$newaction] = $perm;
			$actions = maybe_serialize($actions);
			$sql = "UPDATE ".SFROLES." SET ";
			$sql.= 'role_name="'.$role->role_name.'", ';
			$sql.= 'role_desc="'.$role->role_desc.'", ';
			$sql.= 'role_actions="'.esc_sql($actions).'" ';
			$sql.= "WHERE role_id=".$role->role_id.";";
			$wpdb->query($sql);
		}
	}
}

# support function for modifying an existing role name
function array_change_key_name($orig, $new, &$array)
{
    if ( isset( $array[$orig] ) )
    {
        $array[$new] = $array[$orig];
        unset( $array[$orig] );
    }
    return $array;
}

# support function for modifying an existing role name
function sf_modify_rolename($oldrole, $newrole)
{
	global $wpdb;

	$roles = $wpdb->get_results("SELECT * FROM ".SFROLES." ORDER BY role_id");
	if ($roles)
	{
		foreach ($roles as $role)
		{
			$actions = unserialize($role->role_actions);
			$new_actions = array_change_key_name($oldrole, $newrole, $actions);
			$actions = maybe_serialize($new_actions);
			$sql = "UPDATE ".SFROLES." SET ";
			$sql.= 'role_name="'.$role->role_name.'", ';
			$sql.= 'role_desc="'.$role->role_desc.'", ';
			$sql.= 'role_actions="'.esc_sql($actions).'" ';
			$sql.= "WHERE role_id=".$role->role_id.";";
			$wpdb->query($sql);
		}
	}
}

# function to set up default group permissions
function sf_group_def_perms()
{
	global $wpdb;

	# grab the "default" permissions if they exist
	$noaccess = $wpdb->get_var("SELECT role_id FROM ".SFROLES." WHERE role_name='No Access'");
	if (!$noaccess) $noaccess = -1;
	$readonly = $wpdb->get_var("SELECT role_id FROM ".SFROLES." WHERE role_name='Read Only Access'");
	if (!$readonly) $readonly = -1;
	$standard = $wpdb->get_var("SELECT role_id FROM ".SFROLES." WHERE role_name='Standard Access'");
	if (!$standard) $standard = -1;
	$moderator = $wpdb->get_var("SELECT role_id FROM ".SFROLES." WHERE role_name='Moderator Access'");
	if (!$moderator) $moderator = -1;

	$usergroups = $wpdb->get_results("SELECT * FROM ".SFUSERGROUPS);
	$groups = $wpdb->get_results("SELECT group_id FROM ".SFGROUPS);
	if ($groups && $usergroups)
	{
		foreach ($groups as $group)
		{
			foreach ($usergroups as $usergroup)
			{
				if ($usergroup->usergroup_name == 'Guests')
				{
					$rid = $readonly;
				} else if ($usergroup->usergroup_name == 'Members')
				{
					$rid = $standard;
				} else if ($usergroup->usergroup_name == 'Moderators')
				{
					$rid = $moderator;
				} else {
					$rid = $noaccess;
				}
				$wpdb->query("
					INSERT INTO ".SFDEFPERMISSIONS."
					(group_id, usergroup_id, permission_role)
					VALUES
					($group->group_id, $usergroup->usergroup_id, $rid)");
			}
		}
	}
}

# ====== 4.0.0 ==================================================================================================

# Called by 4.0 to create new forum-smileys folder and content
function sf_relocate_smileys()
{
	global $wpdb;

	$basepath='/';
	if (is_multisite()) $basepath = '/blogs.dir/' . $wpdb->blogid .'/files/';

	$success = 0;
	$newpath = SF_STORE_DIR . $basepath . 'forum-smileys';
	$oldpath = SF_PLUGIN_DIR . '/styles/smileys';

	# check if new folder does not exist - which it shouldn't!
	if(!is_dir($newpath))
	{
		if(!is_writable(SF_STORE_DIR) || !($dir = @mkdir($newpath, 0777)))
		{
			$success = 1;
			return $success;
		}
		if (!is_writable($newpath))
		{
			$success = 2;
			return $success;
		}
		if(is_dir($newpath))
		{
			$avlist = opendir($oldpath);
			while (false !== ($file = readdir($avlist)))
			{
				if (is_file($oldpath.'/'.$file) && $file != "." && $file != "..")
				{
					if(!file_exists($newpath.'/'.$file))
					{
						if(@copy($oldpath.'/'.$file, $newpath.'/'.$file) == false)
						{
							$success = 3;
							break;
						}
					}
				}
			}
			closedir($avlist);
		}
	}
	return $success;
}

# Called by 4.0 to build smiley array
function sf_build_base_smileys()
{
	$smileys = array(
	"Confused" => 	array (	0 => "sf-confused.gif",		1 => ":???:",),
	"Cool" =>		array (	0 => "sf-cool.gif",			1 => ":cool:"),
	"Cry" =>		array (	0 => "sf-cry.gif",			1 => ":cry:",),
	"Embarassed" =>	array (	0 => "sf-embarassed.gif",	1 => ":oops:",),
	"Frown" =>		array (	0 => "sf-frown.gif",		1 => ":frown:",),
	"Kiss" =>		array (	0 => "sf-kiss.gif",			1 => ":kiss:",),
	"Laugh" =>		array (	0 => "sf-laugh.gif",		1 => ":lol:",),
	"Smile" =>		array (	0 => "sf-smile.gif",		1 => ":smile:",),
	"Surprised" =>	array (	0 => "sf-surprised.gif",	1 => ":eek:",),
	"Wink" =>		array (	0 => "sf-wink.gif",			1 => ":wink:",),
	"Yell" =>		array (	0 => "sf-yell.gif",			1 => ":yell:",)
	);

	sf_add_sfmeta('smileys', 'smileys', serialize($smileys));

	return;
}

# Called by 4.0 to add tinymce editor toolbar/plugin arrays
function sf_build_tinymce_toolbar_arrays()
{
	$tbar_buttons=array('bold','italic','underline','|','bullist','numlist','|','blockquote','outdent','indent','|','link','unlink','|','undo','redo','forecolor','charmap','|','image');
	$tbar_buttons_add=array('media','|','spoiler','ddcode','|','emotions','|','pastetext','pasteword','|','selectall','preview','code','|','spellchecker');
	$tbar_plugins=array('inlinepopups','safari','media','preview','emotions','spoiler','ddcode','spellchecker','paste');

	$tinymce_toolbar = array();
	$tinymce_toolbar['tbar_buttons'] = $tbar_buttons;
	$tinymce_toolbar['tbar_buttons_add'] = $tbar_buttons_add;
	$tinymce_toolbar['tbar_plugins'] = $tbar_plugins;

	sf_add_sfmeta('tinymce_toolbar', 'default', serialize($tinymce_toolbar));
	sf_add_sfmeta('tinymce_toolbar', 'user', serialize($tinymce_toolbar));

	return;
}

# Called by 4.0 and install to build memberships table
function sf_build_memberships_table()
{
	global $wpdb;

	$users = $wpdb->get_results("SELECT user_id, usergroups FROM ".SFMEMBERS);
	if ($users)
	{
		foreach ($users as $user)
		{
			$memberships = maybe_unserialize($user->usergroups);
			if ($memberships)
			{
				for ($x=0; $x<count($memberships); $x++)
				{
					$sql ="INSERT INTO ".SFMEMBERSHIPS." (user_id, usergroup_id) ";
					$sql.="VALUES ('".$user->user_id."', '".$memberships[$x]."');";
					$wpdb->query($sql);
				}
			}
		}
	}
}

# Called by 4.0 to create pm message slugs
function sf_create_message_slugs()
{
	global $wpdb;

	# remove all single quotes
	$messages = $wpdb->get_results("SELECT message_id, title FROM ".SFMESSAGES);
	if($messages)
	{
		foreach($messages as $message)
		{
			$title = $message->title;
			$title = str_replace ("'", "", $title);
			$wpdb->query("UPDATE ".SFMESSAGES." SET title = '".$title."' WHERE message_id=".$message->message_id);
		}
	}

	# perform slug creation
	$found = true;
	while($found)
	{
		$message = sf_grab_slugless_messages();
		if($message)
		{
			$slug = sf_create_slug($message->title, 'pm');
			# if not created force change of title
			if($slug)
			{
				$wpdb->query("UPDATE ".SFMESSAGES." SET message_slug = '".$slug."' WHERE title='".$message->title."';");
			} else {
				$wpdb->query("UPDATE ".SFMESSAGES." SET title = 'Untitled' WHERE message_id = ".$message->message_id);
			}
		} else {
			$found = false;
		}
	}

	return;
}

function sf_grab_slugless_messages()
{
	global $wpdb;

	return $wpdb->get_row("SELECT * FROM ".SFMESSAGES." WHERE message_slug='' LIMIT 1;");
}

# ====== 4.0.1 ==================================================================================================

# Called by 4.0.1 to add blockquote to tinymce toolbar
function sf_update_tmtoolbar_blockquote()
{
	$tbrow = array();
	$tbrow[0]='default';
	$tbrow[1]='user';
	foreach($tbrow as $tb)
	{
		$tbmeta = sf_get_sfmeta('tinymce_toolbar', $tb);
		$buttons = unserialize($tbmeta[0]['meta_value']);
		$newbuttons = array();

		$found = false;

		# double check not already there...
		foreach($buttons['tbar_buttons'] as $button)
		{
			if($button == 'blockquote') $found=true;
		}

		if(!$found)
		{
			foreach($buttons['tbar_buttons'] as $button)
			{
				if($button == 'outdent')
				{
					$newbuttons[]='blockquote';
				}
				$newbuttons[]=$button;
			}
			$buttons['tbar_buttons']=$newbuttons;
			sf_update_sfmeta('tinymce_toolbar', $tb, serialize($buttons), $tbmeta[0]['meta_id']);
		}
	}
	return;
}

# ====== 4.0.2 ==================================================================================================

function sf_update_membership_cleanup()
{
	global $wpdb;

	#remove any duplicate memberships
	$memberships = $wpdb->get_results("SELECT * FROM ".SFMEMBERSHIPS);
	if ($memberships)
	{
		$test = array();
		foreach ($memberships as $membership)
		{
			if ($test[$membership->usergroup_id][$membership->user_id] == 1)
			{
				$wpdb->query("DELETE FROM ".SFMEMBERSHIPS." WHERE membership_id=".$membership->membership_id);
			} else {
				$test[$membership->usergroup_id][$membership->user_id] = 1;
			}
		}
	}
}



# ====== 4.1 ==================================================================================================

# Called by 4.1 to create and populate the user options table with the old editor column
function sf_create_user_options()
{
	global $wpdb;

	$users = $wpdb->get_results("SELECT user_id, editor FROM ".SFMEMBERS." ORDER BY user_id");
	if ($users)
	{
		foreach ($users as $user)
		{
			# get user options if they exist
			$useroptions = sf_get_member_item($user->user_id, 'user_options');
			# move editor column to new users options column
			if(empty($user->editor) ? $editor=1 : $editor=$user->editor);
			$useroptions['editor'] = $editor;
			sf_update_member_item($user->user_id, 'user_options', $useroptions);
		}
	}

	return;
}

# Called by 4.1 to populate sfmeta for default usergroups
function sf_create_usergroup_meta($members)
{
	global $wp_roles;

	$roles = array_keys($wp_roles->role_names);
	if ($roles)
	{
		foreach ($roles as $role)
		{
			sf_add_sfmeta('default usergroup', $role, $members); # initally set each role to members usergroup
		}
	}
}

# Called by 4.1 to create new count and info fields
function sf_build_41_counts()
{
	global $wpdb;

	# start with post count in forums
	$forums = $wpdb->get_results("SELECT forum_id FROM ".SFFORUMS);
	if($forums)
	{
		foreach($forums as $forum)
		{
			$posts = $wpdb->get_var("SELECT COUNT(post_id) FROM ".SFPOSTS." WHERE forum_id=".$forum->forum_id);
			$wpdb->query("UPDATE ".SFFORUMS." SET post_count = ".$posts." WHERE  forum_id=".$forum->forum_id);
		}
	}
}

# Called by 4.1 to change topic subscriptions and watches, and post ratings to be stored in serialized arrays
function sf_serialize_subs_watches_ratings()
{
	global $wpdb;

	# do sftopics table watches and subscriptions
	$topics = $wpdb->get_results("SELECT topic_id, topic_subs, topic_watches FROM ".SFTOPICS." WHERE topic_subs is not null OR topic_watches is not null");
	if ($topics)
	{
		foreach ($topics as $topic)
		{
			# make sure they arent already serialized, ie maybe ugprade already run once?
			if ($topic->topic_subs && !is_serialized($topic->topic_subs))
			{
				$subs = explode('@', $topic->topic_subs);
				$subs = serialize($subs);
				$wpdb->query("UPDATE ".SFTOPICS." SET topic_subs = '".$subs."' WHERE  topic_id=".$topic->topic_id);
			}

			if ($topic->topic_watches && !is_serialized($topic->topic_watches))
			{
				$watches = explode('@', $topic->topic_watches);
				$watches = serialize($watches);
				$wpdb->query("UPDATE ".SFTOPICS." SET topic_watches = '".$watches."' WHERE  topic_id=".$topic->topic_id);
			}
		}
	}

	# do members table watches and subscriptions
	$members = $wpdb->get_results("SELECT user_id, subscribe, watches FROM ".SFMEMBERS." WHERE subscribe is not null OR watches is not null");
	if ($members)
	{
		foreach ($members as $member)
		{
			if ($member->subscribe && !is_serialized($member->subscribe))
			{
				$subs = explode('@', $member->subscribe);
				$subs = serialize($subs);
				$wpdb->query("UPDATE ".SFMEMBERS." SET subscribe = '".$subs."' WHERE  user_id=".$member->user_id);
			}

			if ($member->watches && !is_serialized($member->watches))
			{
				$watches = explode('@', $member->watches);
				$watches = serialize($watches);
				$wpdb->query("UPDATE ".SFMEMBERS." SET watches = '".$watches."' WHERE  user_id=".$member->user_id);
			}
		}
	}

	# do members table post ratings
	$members = $wpdb->get_results("SELECT user_id, posts_rated FROM ".SFMEMBERS." WHERE posts_rated is not null");
	if ($members)
	{
		foreach ($members as $member)
		{
			if ($member->posts_rated && !is_serialized($member->posts_rated))
			{
				$ratings = explode('@', $member->posts_rated);
				$ratings = serialize($ratings);
				$wpdb->query("UPDATE ".SFMEMBERS." SET posts_rated = '".$ratings."' WHERE  user_id=".$member->user_id);
			}
		}
	}
}

# Called by 4.1.0 to add spoiler to tinymce toolbar
function sf_update_tmtoolbar_spoiler()
{
	$tbrow = array();
	$tbrow[0]='default';
	$tbrow[1]='user';
	foreach($tbrow as $tb)
	{
		$tbmeta = sf_get_sfmeta('tinymce_toolbar', $tb);
		$buttons = unserialize($tbmeta[0]['meta_value']);
		$newbuttons = array();

		$found = false;

		# double check not already there...
		foreach($buttons['tbar_buttons_add'] as $button)
		{
			if($button == 'spoiler') $notfound=true;
		}

		if(!$found)
		{
			foreach($buttons['tbar_buttons_add'] as $button)
			{
				if($button == 'ddcode')
				{
					$newbuttons[]='spoiler';
				}
				$newbuttons[]=$button;
			}
			$buttons['tbar_buttons_add']=$newbuttons;

			$buttons['tbar_plugins'][]='spoiler';

			sf_update_sfmeta('tinymce_toolbar', $tb, serialize($buttons), $tbmeta[0]['meta_id']);
		}
	}
	return;
}

# Called by 4.1.0 to add SPF Manage Toolbox & Configuration caps to all admins with Options caps
function sf_update_admin_toolbox()
{
	global $wpdb;

	$admins = $wpdb->get_col("SELECT user_id FROM ".SFMEMBERS." WHERE admin = 1");
	$metakey = $wpdb->prefix.'capabilities';
	if($admins)
	{
		foreach($admins as $admin)
		{
			$caps = get_user_meta($admin, $metakey, true);
			if($caps['SPF Manage Options'] == 1)
			{
				$caps['SPF Manage Toolbox'] = 1;
				$caps['SPF Manage Configuration'] = 1;
			} else {
				$caps['SPF Manage Toolbox'] = 0;
				$caps['SPF Manage Configuration'] = 0;
			}
			update_user_meta($admin, $metakey, $caps);
		}
	}
	return;
}

# Called by 4.1.0 to clean up and serialise icon text
function sf_upgrade_icontext()
{
	$icons = get_option('sfshowicon');
	$list = explode('@', $icons);

	$newicons = array();
	foreach($list as $i)
	{
		$temp=explode(';', $i);
		if($temp[0] != 'Moderation Queue' && $temp[0] != 'Close New Post List')
		{
			$newicons[$temp[0]] = $temp[1];
		}
	}
	update_option('sfshowicon', $newicons);
	return;
}

# Called by 4.1.0 to clean up and serialise avatar options
function sf_upgrade_avatar_options()
{
	# if avatar options already serialized, bail since must have already been done
	if (is_serialized(get_option('sfavatars'))) return;

	# convert avatar options
	$sfoptions = array();
	$sfoptions['sfshowavatars'] = get_option('sfshowavatars');
	$sfoptions['sfavataruploads'] = get_option('sfavataruploads');
	$sfoptions['sfgmaxrating'] = get_option('sfgmaxrating');
	$sfoptions['sfavatarsize'] = get_option('sfavatarsize');

	$wpavatar = get_option('sfwpavatar');
	$gravatar = get_option('sfgravatar');
	if ($wpavatar)
	{
		$sfoptions['sfavatarpriority'] = array(1, 0, 2, 3);  # wp, gravatar, upload, spf
	} else if ($gravatar) {
		$sfoptions['sfavatarpriority'] = array(0, 2, 3, 1);  # gravatar, upload, spf, wp
	} else {
		$sfoptions['sfavatarpriority'] = array(2, 3, 0, 1);  # upload, spf, gravatar, wp
	}

	# add the new serialized options
	add_option('sfavatars', $sfoptions);

	return;
}

# Called by 4.1.0 to serialise custom field data
function sf_update_custom_fields()
{
	$cfields = sf_get_sfmeta('custom_field');
	if ($cfields && !is_serialized($cfields))
	{
		foreach ($cfields as $info)
		{
			$field = array();
			$fields['type'] = $info['meta_value'];

			$cfname = sf_create_slug($info['meta_key'], 'custom');
			$cfname = preg_replace('|[^a-z0-9_]|i', '', $cfname);

			sf_update_sfmeta('custom_field', $cfname, serialize($fields), $info['meta_id']);
		}
	}
	return;
}

# Called by 4.1.0 to convert exiting user custom field data
function sf_convert_custom_profile_fields()
{
	global $wpdb;

	# see if any custom fields that need converting
	$cfields = sf_get_sfmeta('custom_field');
	if ($cfields && !is_serialized($cfields))
	{
		foreach ($cfields as $x => $cfield)
		{
			$sql = "SELECT user_id, meta_value FROM ".SFUSERMETA." WHERE meta_key='sfcustomfield".$x."'";
			$usermetas = $wpdb->get_results($sql);
			if ($usermetas)
			{
				foreach ($usermetas as $usermeta)
				{
					# delete old usermeta
					delete_user_meta($usermeta->user_id, 'sfcustomfield'.$x);

					# replace the usermeta
					update_user_meta($usermeta->user_id, $cfield['meta_key'], $usermeta->meta_value);
				}
			}
		}
	}
	return;
}

function sf_upgrade_members_pm()
{
	global $wpdb;

	$users = $wpdb->get_results("SELECT user_id, user_options FROM ".SFMEMBERS);
	foreach ($users as $user)
	{
		$useroptions = unserialize($user->user_options);
		$useroptions['pmemail'] = 1;
		$useroptions = serialize($useroptions);
		$wpdb->query("UPDATE ".SFMEMBERS." SET user_options='".$useroptions."' WHERE user_id=".$user->user_id);
	}
}

function sf_modify_admin_cap($curcap, $newcap)
{
	global $wpdb;

	$admins = $wpdb->get_results("SELECT user_id FROM ".SFMEMBERS." WHERE admin=1");
	foreach ($admins as $admin)
	{
		$user = new WP_User($admin->user_id);
		if ($user->has_cap($curcap))
		{
			$user->remove_cap($curcap);
			$user->add_cap($newcap);
		}
	}
}

function sf_move_options()
{
	global $wpdb;

	$sql = "
		CREATE TABLE IF NOT EXISTS ".SFOPTIONS." (
		option_id bigint(20) unsigned NOT NULL auto_increment,
		option_name varchar(64) NOT NULL default '',
		option_value longtext NOT NULL,
		PRIMARY KEY (option_name),
		KEY option_id (option_id)
		) ENGINE=MyISAM ".sf_charset().";";
	$wpdb->query($sql);

	$optionlist = array(
		'sfversion',
		'sfbuild',
		'sfpage',
		'sfslug',
		'sfsmilies',
		'sfuninstall',
		'sfdates',
		'sftimes',
		'sfshowavatars',
		'sfshowicon',
		'sfavatarsize',
		'sfavatars',
		'sfpermalink',
		'sfextprofile',
		'sfrsscount',
		'sfrsswords',
		'sfgravatar',
		'sfuseannounce',
		'sfannouncecount',
		'sfannouncehead',
		'sfannounceauto',
		'sfannouncetime',
		'sfannouncetext',
		'sfannouncelist',
		'sflockdown',
		'sfimgenlarge',
		'sfthumbsize',
		'sfmail',
		'sfnewusermail',
		'sfbadwords',
		'sfreplacementwords',
		'sfpm',
		'sfcustom',
		'sfeditormsg',
		'sfpostmsg',
		'sfcheck',
		'sfstyle',
		'sflogin',
		'sfadminsettings',
		'sfauto',
		'sfpmemail',
		'sfpmmax',
		'sfpostpaging',
		'sfeditor',
		'sfsmileys',
		'sfpostratings',
		'sfavataruploads',
		'sfprivatemessaging',
		'sfseo',
		'sfsigimagesize',
		'sfmemberlistperms',
		'sfgmaxrating',
		'sfcbexclusions',
		'sfshowmemberlist',
		'sfpostlinking',
		'sfwpavatar',
		'sfcheckformember',
		'sfsupport',
		'sfcontrols',
		'sfmetatags',
		'sfacolours',
		'sfallRSSurl',
		'sfautoupdate',
		'sfblockadmin',
		'sfconfig',
		'sfdisplay',
		'sffilters',
		'sfsinglemembership',
		'sfguests',
		'sfprofile',
		'sfuploads',
		'sfStartUpgrade',
		'sfforceupgrade',
		'sfzone'
	);

	foreach( $optionlist as $option)
	{
		$sfopt = sf_opcheck(get_option($option));
		sf_add_option($option, $sfopt);
		delete_option($option);
	}

	return;
}

function sf_generate_member_feedkeys()
{
    global $wpdb;

    $members = $wpdb->get_results("SELECT user_id FROM ".SFMEMBERS);
    foreach ($members as $member)
    {
        # generate a pseudo-random UUID according to RFC 4122
        $key = sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                    mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
                    mt_rand( 0, 0x0fff ) | 0x4000,
                    mt_rand( 0, 0x3fff ) | 0x8000,
                    mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) );
        $wpdb->query("UPDATE ".SFMEMBERS." SET feedkey = '".$key."' WHERE user_id=".$member->user_id);
    }

    return;
}

# 4.2 - Upgrade postmeta records to new blog linking table
function sf_upgrade_bloglinks_table()
{
	global $wpdb;

	$links = $wpdb->get_results("SELECT post_id, meta_value FROM ".$wpdb->prefix."postmeta WHERE meta_key = 'forumlink'");
	if($links)
	{
		foreach($links as $link)
		{
			$key=explode('@', $link->meta_value);
			if((isset($key[0]) && !empty($key[0])) && (isset($key[1]) && !empty($key[1])))
			{
				$postid=$link->post_id;
				$forumid=$key[0];
				$topicid=$key[1];
				$sql="INSERT INTO ".SFLINKS." (post_id, forum_id, topic_id) VALUES (".$postid.", ".$forumid.", '".$topicid."');";
				$wpdb->query($sql);
			}
		}
		$wpdb->query("DELETE FROM ".$wpdb->prefix."postmeta WHERE meta_key = 'forumlink'");
	}
	return;
}

# 4.2 - new members table building for installs
function sf_install_members_table($subphase)
{
	global $wpdb, $current_user;

	# get limits for installs
	if ($subphase != 0)
	{
		$limit = " LIMIT 250 OFFSET ".(($subphase - 1) * 250);
	}

	# select all users
	$sql = "SELECT ID FROM ".$wpdb->prefix."users".$limit;
	$members = $wpdb->get_results($sql);

	if ($members)
	{
		foreach($members as $member)
		{
			# Check ID exists and is not zero
			if(is_numeric($member->ID) && $member->ID > 0)
			{
                sf_create_member_data($member->ID);

                # for the admin installer, remove any usergroup membership added by create member function
				if ($current_user->ID == $member->ID)
                {
                    $wpdb->query("DELETE FROM ".$wpdb->prefix."sfmemberships WHERE user_id=".$member->ID);
        			sf_update_member_item($member->ID, 'pm', 1);
                }
			}
		}
	}
	return;
}

# 4.2 - add additional admin colors
function sf_update_admin_colors()
{
	global $wpdb;

   	# get admins that have custom colors
	$sql = "SELECT user_id FROM ".SFUSERMETA." WHERE meta_key='sfadmincolours'";
	$admins = $wpdb->get_results($sql);
    if ($admins)
    {
        foreach ($admins as $admin)
        {
            $sfacolors = sf_opcheck(get_user_meta($admin->user_id, 'sfadmincolours', true));
        	$sfacolors['submitbg'] = '27537A';
        	$sfacolors['submitbgt'] = 'FFFFFF';
           	update_user_meta($admin->user_id, 'sfadmincolours', $sfacolors);
        }
    }
}

# 4.2 - move admin color options to admin options
function sf_move_admin_colors()
{
	global $wpdb;

   	# get admins that have custom colors
	$sql = "SELECT user_id FROM ".SFUSERMETA." WHERE meta_key='sfadmincolours'";
	$admins = $wpdb->get_results($sql);
    if ($admins)
    {
        foreach ($admins as $admin)
        {
            # get current admin options
            $curdminoptions = sf_get_member_list($admin->user_id, 'admin_options');
            $newadminoptions = $curdminoptions['admin_options'];

            # add in custom colors
        	$newadminoptions['colors'] = sf_opcheck(get_user_meta($admin->user_id, 'sfadmincolours', true));
            if (!$newadminoptions['colors']) $newadminoptions['colors'] = sf_get_option('sfacolours');

            # save new admin options with colors
        	sf_update_member_item($admin->user_id, 'admin_options', $newadminoptions);

            # delete old usermeta colors
           	delete_user_meta($admin->user_id, 'sfadmincolours');
        }
    }
}

# 4.3 - move sig images into sig field and clean up
function sf_convert_sig_images()
{
	global $wpdb;

    # remove the permission
	$roles = $wpdb->get_results("SELECT * FROM ".SFROLES." ORDER BY role_id");
	if ($roles)
	{
		foreach ($roles as $role)
		{
			$actions = unserialize($role->role_actions);
			unset($actions['Can use images in signatures']);
			$actions = maybe_serialize($actions);
			$sql = "UPDATE ".SFROLES." SET ";
			$sql.= 'role_name="'.$role->role_name.'", ';
			$sql.= 'role_desc="'.$role->role_desc.'", ';
			$sql.= 'role_actions="'.esc_sql($actions).'" ';
			$sql.= "WHERE role_id=".$role->role_id.";";
			$wpdb->query($sql);
		}
	}

    # convert sig images to beginning of sig
    $sigs = $wpdb->get_results("SELECT user_id, signature, sigimage FROM ".SFMEMBERS." WHERE sigimage != '' AND sigimage != 'NULL'");
    if ($sigs)
    {
        foreach ($sigs as $sig)
        {
            $newsig = '<img src="'.$sig->sigimage.'" />'.addslashes($sig->signature);
            $wpdb->query("UPDATE ".SFMEMBERS." SET signature = '".$newsig."' WHERE user_id=".$sig->user_id);
        }
    }

    # now remove sigimage column and clean up any usermeta stuff left over
	$wpdb->query("ALTER TABLE ".SFMEMBERS." DROP sigimage;");
	$wpdb->query("DELETE FROM ".SFUSERMETA." WHERE meta_key = 'signature' OR meta_key = 'sigimage'");
}

# 4.3 - conver block admin levels to accessed roles
function sf_convert_block_admin()
{
	global $wp_roles;

    # init allowed roles
    $sfblock['blockroles'] = array();

    # find the min level for admin access
	$sfblock = sf_get_option('sfblockadmin');
    if ($sfblock && !empty($sfblock['blockrole']))
    {
        $minrole = get_role($sfblock['blockrole']);
        $minlevel = 0;
        foreach(array_keys($minrole->capabilities) as $cap)
        {
            preg_match('/^level_(10|[0-9])$/i', $cap, $matches);
            if ($matches[1] > $minlevel) $minlevel = $matches[1];
        }

        # need to find level of each wp role
        $roles = array_keys($wp_roles->role_names);
        if ($roles)
        {
            foreach ($roles as $role)
            {
                $roleobj = $wp_roles->get_role($role);
                $rolelevel = 0;
                foreach(array_keys($roleobj->capabilities) as $cap)
                {
                    preg_match('/^level_(10|[0-9])$/i', $cap, $matches);
                    if ($matches[1] > $rolelevel) $rolelevel = $matches[1];
                }

                # does the role current have access?
                 if ($rolelevel >= $minlevel)
                 {
                    $sfblock['blockroles'][$role] = 1;
                 } else {
                    $sfblock['blockroles'][$role] = 0;
                 }
            }
        }

        # remove the old blockrole setting
        unset($sfblock['blockrole']);
    }

    # always allow admin
    $sfblock['blockroles']['administrator'] = true;

    # save new blockrole access list
	sf_update_option('sfblockadmin', $sfblock);
}

# 4.3 - add user id to new user list
function sf_upgrade_new_user_list()
{
	global $wpdb;

	$sfcontrols = sf_get_option('sfcontrols');
    if ($sfcontrols['newuserlist'])
    {
        $newlist = array();
        foreach ($sfcontrols['newuserlist'] as $index => $user)
        {
            $newlist[$index]['id'] = sf_get_user_id_from_display_name($user);
            $newlist[$index]['name'] = $user;
        }
        $sfcontrols['newuserlist'] = $newlist;

        # save update new user list
        sf_update_option('sfcontrols', $sfcontrols);
    }
}

# 4.3.4 - clean up old transient records.
function sf_transient_cleanup()
{
	global $wpdb;

	$keytime = time() - 3600;
	$messagetime = time() - 120;
	$sfvarstime = time() - 3600;
	$urltime = time() - 3600;
	$bookmarktime = time() - 3600;
	$reloadtime = time() - 900;
	$sql = "
		SELECT * FROM ".SF_PREFIX."options WHERE
			(option_name LIKE '_transient_timeout_%keys' AND option_value < ".$keytime.") OR
   			(option_name LIKE '_transient_timeout_%message' AND option_value < ".$messagetime.") OR
   			(option_name LIKE '_transient_timeout_%sfvars' AND option_value < ".$sfvarstime.") OR
   			(option_name LIKE '_transient_timeout_%url' AND option_value < ".$urltime.") OR
   			(option_name LIKE '_transient_timeout_%bookmark' AND option_value < ".$bookmarktime.") OR
   			(option_name LIKE '_transient_timeout_%reload' AND option_value < ".$reloadtime.")
 	";
	$records = $wpdb->get_results($sql);
	foreach ($records as $record)
	{
		$transient = explode('_transient_timeout_', $record->option_name);
		$wpdb->query("DELETE FROM ".SF_PREFIX."options WHERE option_name='_transient_timeout_".$transient[1]."'");
		$wpdb->query("DELETE FROM ".SF_PREFIX."options WHERE option_name='_transient_".$transient[1]."'");
	}
}

?>