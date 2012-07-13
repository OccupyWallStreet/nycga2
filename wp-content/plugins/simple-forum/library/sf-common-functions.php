<?php
/*
Simple:Press
sf-common-functions.php - common/shared routines between back and front ends
$LastChangedDate: 2011-03-10 06:26:08 -0700 (Thu, 10 Mar 2011) $
$Rev: 5655 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# = BASE SLUG CREATION ========================

# ------------------------------------------------------------------
# sf_create_slug()
#
# Create a new slug
#	$itle:		Forum or Topic title
#	$type:		'forum', 'topic' or 'pm'
#	$checkdup	Check for duplicates (optional)
# ------------------------------------------------------------------
function sf_create_slug($title, $type, $checkdup=true)
{
//	$slug = apply_filters('sanitize_title', $title);
	$slug = sanitize_title($title);
	# clean up pm slug as used for html element id=no url
	if($type == 'pm')
	{
		$altslug = rawurldecode($slug);
		$slug = sanitize_html_class($slug, $altslug);
	}

	if($checkdup)
	{
		$slug = sf_check_slug_unique($slug, $type);
	}
	$slug = apply_filters('sf_create_slug', $slug, $type);

	return $slug;
}

# ------------------------------------------------------------------
# sf_check_slug_unique()
#
# Check new slug is unique and not used. Add numeric suffix if
# exists. If slug receved is empty then return empty.
#	$itle:		Forum or Topic title new slug
#	$type:		'forum' or 'topic' or 'pm'
# ------------------------------------------------------------------
function sf_check_slug_unique($title, $type)
{
	global $wpdb;

	if (empty($title)) return '';

	$exists = true;
	$suffix = 1;
	$testtitle = $title;
	while ($exists)
	{
		$check = '';
		if ($type == 'forum') {
			$check = $wpdb->get_var("SELECT forum_slug FROM ".SFFORUMS." WHERE forum_slug='".$testtitle."'");
		} elseif($type == 'topic') {
			$check = $wpdb->get_var("SELECT topic_slug FROM ".SFTOPICS." WHERE topic_slug='".$testtitle."'");
		} elseif($type == 'pm') {
			$check = $wpdb->get_var("SELECT message_slug FROM ".SFMESSAGES." WHERE message_slug='".$testtitle."'");
		}
		if ($check)
		{
			$testtitle = $title.'-'.$suffix;
			$suffix++;
		} else {
			$exists = false;
		}
	}
	return $testtitle;
}

function sfc_current_user_can($cap)
{
	global $current_user, $wpdb;

    # is is multi site admin?
    $multisite_admin = is_multisite() && is_super_admin();

	# if there are no SPF admins defined, revert to allowing all WP admins so forum admin isn't locked out
	$allow_wp_admins = (sf_get_admins() == '' && get_user_meta($current_user->ID, $wpdb->prefix.'user_level', true) == 10) ? true : false;

	if (current_user_can($cap) || $allow_wp_admins || $multisite_admin)
		return true;
	else
		return false;
}

function sfc_create_nonce($action)
{
	return '<input type="hidden" name="'.$action.'" value="'.wp_create_nonce($action).'" />'."\n";
}

# ------------------------------------------------------------------
# sfc_add_membership()
#
# Adds the specified user to the specified user group
#	$usergroup_id:		user group to which to add the user
#	$userid:			user to be added
# ------------------------------------------------------------------
function sfc_add_membership($usergroup_id, $user_id)
{
	global $wpdb;

    # dont allow admins to be added to user groups
    if (sf_is_forum_admin($user_id)) return false;

	$success = false;

	# if only one membership allowed, remove all current memberships
	$sfmemberopts = sf_get_option('sfmemberopts');
	if ($sfmemberopts['sfsinglemembership'])
	{
		$wpdb->query("DELETE FROM ".SFMEMBERSHIPS." WHERE user_id=".$user_id);
	}

	# dont add membership if it already exists
	$check = sf_check_membership($usergroup_id, $user_id);
	if (empty($check))
	{
		$sql ="INSERT INTO ".SFMEMBERSHIPS." (user_id, usergroup_id) ";
		$sql.="VALUES ('".$user_id."', '".$usergroup_id."');";
		$success = $wpdb->query($sql);

	    sfc_update_member_moderator_flag($user_id);
		sfc_update_member_pm($user_id);
	}
	return $success;
}

# ------------------------------------------------------------------
# sfc_update_member_moderator_flag()
#
# checks an updates moderator flag for specified user
#	$userid:		User to lookup
# ------------------------------------------------------------------
function sfc_update_member_moderator_flag($userid)
{
	global $wpdb;

	$ugs = sf_get_user_memberships($userid);
	if ($ugs)
	{
		foreach ($ugs as $ug)
		{
			$mod = $wpdb->get_var("SELECT usergroup_is_moderator FROM ".SFUSERGROUPS." WHERE usergroup_id = ".$ug['usergroup_id']);
			if ($mod)
			{
				sf_update_member_item($userid, 'moderator', 1);
				return;
			}
		}
	}

	# not a moderator if we get here
	sf_update_member_item($userid, 'moderator', 0);

	return;
}

# ------------------------------------------------------------------
# sfc_rebuild_members_pm()
#
# rebuilds the members 'can pm' column in the db.  should be called
# whenever usergroup memberships have been changed for multiple users
# ------------------------------------------------------------------
function sfc_rebuild_members_pm($members)
{
	global $wpdb;

	if ($members)
	{
		foreach ($members as $member)
		{
			$canpm = 0;
			$ugs = array();
			$ugs = sf_get_user_memberships($member->user_id);
			if ($ugs)
			{
				foreach ($ugs as $ug)
				{
					$rids = $wpdb->get_results("SELECT permission_role FROM ".SFPERMISSIONS." WHERE usergroup_id='".$ug['usergroup_id']."'");
					foreach ($rids as $rid)
					{
						$role_actions = $wpdb->get_var("SELECT role_actions FROM ".SFROLES." WHERE role_id='".$rid->permission_role."'");
						$actions = maybe_unserialize($role_actions);
						if ($actions['Can use private messaging'] == 1)
						{
							$canpm = '1';
							break 2; # jump out and update the user
						}
					}
				}
			}

			# update PM for user
			sf_update_member_item($member->user_id, 'pm', $canpm);
		}
	}

	return;
}

# ------------------------------------------------------------------
# sfc_update_member_pm()
#
# Determines if a user can still PM.  Should be called
# whenever usergroup memberships have been changed for single user
# ------------------------------------------------------------------
function sfc_update_member_pm($userid)
{
	global $wpdb;

	if (sf_is_forum_admin($userid))
	{
		return;  # no udpate required for admins
	} else {
		$ugs = array();
		$ugs = sf_get_user_memberships($userid);
		if ($ugs)
		{
			foreach ($ugs as $ug)
			{
				$rids = $wpdb->get_results("SELECT permission_role FROM ".SFPERMISSIONS." WHERE usergroup_id='".$ug['usergroup_id']."'");
				foreach ($rids as $rid)
				{
					$role_actions = $wpdb->get_var("SELECT role_actions FROM ".SFROLES." WHERE role_id='".$rid->permission_role."'");
					$actions = maybe_unserialize($role_actions);
					if ($actions['Can use private messaging'] == 1)
					{
						sf_update_member_item($userid, 'pm', 1);
						return;
					}
				}
			}
		}
	}

	# if we get here, user cannot PM
	sf_update_member_item($userid, 'pm', 0);

	return;
}

function sfc_add_tags($topicid, $tags)
{
	global $wpdb;

	if ($tags)
	{
		foreach ($tags as $tag)
		{
			$tagid = '';

			$tagname = sf_filter_title_save($tag);
			$tagslug = sf_create_slug($tag, 'tag');

			#check if tag already exists
			$tagcheck = $wpdb->get_row("SELECT tag_id, tag_count FROM ".SFTAGS." WHERE tag_slug='".$tagslug."'");
			if ($tagcheck)
			{
				#is it already tied to this topic?
				$topictag = $wpdb->get_var("SELECT topic_id FROM ".SFTAGMETA." WHERE tag_id=".$tagcheck->tag_id." AND topic_id=".$topicid);
				if (empty($topictag))
				{
					# tag exists, but not on this topic so increment the tag count
					$count = $tagcheck->tag_count + 1;
					$wpdb->query("UPDATE ".SFTAGS." SET tag_count=".$count." WHERE tag_id=".$tagcheck->tag_id);

					# use current tag id
					$tagid = $tagcheck->tag_id;
				}
			} else {
				# new tag, so create the
				$wpdb->query("INSERT INTO ".SFTAGS." (tag_name, tag_slug, tag_count) VALUES ('".$tagname."', '".$tagslug."', 1)");

				# get new tag id
				$tagid = $wpdb->insert_id;
			}

			# now save the tag meta info if it didnt exist for this topic
			if ($tagid)
			{
				$forumid = $wpdb->get_var("SELECT forum_id FROM ".SFTOPICS." WHERE topic_id=".$topicid);
				$wpdb->query("INSERT INTO ".SFTAGMETA." (tag_id, topic_id, forum_id) VALUES (".$tagid.", ".$topicid.", ".$forumid.");");
			}
		}
	}

	return;
}

function sfc_get_metadescription()
{
	global $sfvars;

	$description = '';

	# do we need a meta description
	$sfmetatags = sf_get_option('sfmetatags');
	switch ($sfmetatags['sfdescruse'])
	{
		case 1:  # no meta description
			break;

		case 2:  # use custom meta description on all forum pages
			$description = sf_filter_title_display($sfmetatags['sfdescr']);
			break;

		case 3:  # use custom meta description on group view and forum description on forum/topic pages
			if (($sfvars['pageview'] == 'forum' || $sfvars['pageview'] == 'topic') && !isset($_GET['search']))
			{
				$forum = sf_get_forum_record_from_slug($sfvars['forumslug']);
				$description = sf_filter_title_display($forum->forum_desc);
			} else {
				$description = sf_filter_title_display($sfmetatags['sfdescr']);
			}
			break;

		case 4:  # use custom meta description on group view, forum description on forum pages and topic title on topic pages
			if ($sfvars['pageview'] == 'forum' && !isset($_GET['search'])) {
				$forum = sf_get_forum_record_from_slug($sfvars['forumslug']);
				$description = sf_filter_title_display($forum->forum_desc);
			} else if ($sfvars['pageview'] == 'topic' && !isset($_GET['search'])) {
				$topic = sf_get_topic_record_from_slug($sfvars['topicslug']);
				$description =sf_filter_title_display($topic->topic_name);
			} else {  # must be group or other
				$description = sf_filter_title_display($sfmetatags['sfdescr']);
			}
			break;
	}

	return $description;
}

function sfc_get_metakeywords()
{
	global $sfvars, $wpdb;

	$keywords = '';

	$sfmetatags = sf_get_option('sfmetatags');
	if ($sfmetatags['sfusekeywords'])
	{
		if ($sfvars['pageview'] == 'topic' && $sfmetatags['sftagwords'])
		{
			$notags = true;
			$forum = sf_get_forum_record_from_slug($sfvars['forumslug']);
			if ($forum->use_tags) # make sure tags in use on this forum
			{
				$topic = sf_get_topic_record_from_slug($sfvars['topicslug']);
				if ($topic)
				{
					$tags = $wpdb->get_col("SELECT tag_name
											FROM ".SFTAGS."
										 	JOIN ".SFTAGMETA." ON ".SFTAGMETA.".tag_id = ".SFTAGS.".tag_id
											WHERE topic_id=".$topic->topic_id);
				}
				if ($tags)
				{
					$tags = implode(',', $tags);
					$keywords = stripslashes($tags);
				}
			}
		}

		# if no tags, revert to custom keywords
		if ($keywords == '')
		{
			$keywords = stripslashes($sfmetatags['sfkeywords']);
		}
	}

	return $keywords;
}

# ------------------------------------------------------------------
# sfc_get_profile_data(userid)
#
# Grabs all of the profile data for specified user and
# returns in an array
# ------------------------------------------------------------------
function sfc_get_profile_data($userid)
{
	global $wpdb;

	if ($userid == 0 || $userid == '') return;

	$profile=array();

	# from USERS
	$profile = $wpdb->get_row("SELECT ID, user_login, user_email, user_url, user_registered FROM ".SFUSERS." WHERE ID=".$userid, ARRAY_A);

	if(empty($profile)) return;

	# from USERMETA
	$m = $wpdb->get_results("SELECT meta_key, meta_value FROM ".SFUSERMETA." WHERE user_id=".$userid, ARRAY_A);

	if (empty($m)) return;

	foreach ($m as $item)
	{
		$profile[$item['meta_key']]=$item['meta_value'];
	}

	# from SFMEMBERS
	$m = $wpdb->get_row("SELECT * FROM ".SFMEMBERS." WHERE user_id=".$userid, ARRAY_A);
	if (!empty($m))
	{
		$profile = array_merge($profile, $m);
	}

	if (isset($profile['avatar'])) $profile['avatar'] = unserialize($profile['avatar']);
	if (isset($profile['photos'])) $profile['photos'] = unserialize($profile['photos']);
	if (isset($profile['user_options'])) $profile['user_options'] = unserialize($profile['user_options']);

	return $profile;
}

?>