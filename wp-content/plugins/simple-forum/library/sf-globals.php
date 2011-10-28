<?php
/*
Simple:Press
Global defs
$LastChangedDate: 2010-10-29 14:05:28 -0700 (Fri, 29 Oct 2010) $
$Rev: 4839 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# ------------------------------------------------------
# sf_setup_globals()
#
# some global system level defs used here and there
# NOTE: This array is initialised in sf-includes
# ------------------------------------------------------
function sf_setup_globals()
{
	global $sfglobals, $current_user, $sfvars, $CACHE;

	if($CACHE['globals'] == true) return;

	# Main admin options
	$sfglobals['admin'] = sf_get_option('sfadminsettings');

	$sfglobals['lockdown'] = sf_get_option('sflockdown');
	$sfglobals['custom'] = sf_get_option('sfcustom');

	# Load icon List
	$sfglobals['icons'] = sf_get_option('sfshowicon');
	# Load smiley options
	$sfsmileys = array();
	$sfsmileys = sf_get_option('sfsmileys');
	$sfglobals['smileyoptions']=$sfsmileys;

	# Load smileys if custom being used
	if($sfsmileys['sfsmallow'] && $sfsmileys['sfsmtype']==1)
	{
		$meta = sf_get_sfmeta('smileys', 'smileys');
		$smeta = $meta[0]['meta_value'];
		$collection = unserialize($meta[0]['meta_value']);
		if($collection)
		{
			foreach($collection as $smiley => $info)
			{
				$sfglobals['smileys'][$smiley][0]=sf_filter_title_display($info[0]);
				$sfglobals['smileys'][$smiley][1]=sf_filter_name_display($info[1]);
			}
		}
	}

	# Load editor and tinymce toolbar if in use
	$editor = array();
	$editor = sf_get_option('sfeditor');

	# make sure editor has some defaults
	if (empty($editor['sflang'])) $editor['sflang'] = 'en';
	if (empty($editor['sfeditor'])) $editor['sfeditor'] = RICHTEXT;
	$sfglobals['editor'] = $editor;

	# see if user editor defined
	if ($editor['sfusereditor'] && $current_user->member && !empty($sfglobals['member']['user_options']['editor']))
	{
		$sfglobals['editor']['sfeditor'] = $sfglobals['member']['user_options']['editor'];
	}

	# set up toolbar if tinymce
	if ($sfglobals['editor']['sfeditor'] == RICHTEXT)
	{
		$tbmeta = sf_get_sfmeta('tinymce_toolbar', 'user');
		$sfglobals['toolbar'] = unserialize($tbmeta[0]['meta_value']);
	}

	# Display array
	$sfglobals['display']=sf_get_option('sfdisplay');

	$CACHE['globals'] = true;

	return;
}

# ------------------------------------------------------
# sf_extend_current_user()
#
# extends the WP class $current_user object
# these are the base/global permission settings
#	$pageview:		i.e. 'group', 'forum' etc
#	$forumid:		forumd if relevant
# ------------------------------------------------------
function sf_extend_current_user($forumid='')
{
	global $sfvars, $current_user, $sfglobals, $CACHE;

	if($CACHE['user'] == true) return;

	if($current_user->ID == 0)
	{
		$current_user->ID = '';
	} else {
		$member = $sfglobals['member'];
	}

	# start with some specials
	$current_user->forumadmin = 0;
	$current_user->moderator = 0;
	$current_user->offmember = 0;
	$current_user->member = 0;
	$current_user->guest = 0;
	$current_user->guestname = '';
	$current_user->guestemail = '';
	$current_user->lastvisit = 0;

	if ($current_user->ID == '' || $current_user->ID == 0)
	{
		$current_user->offmember = sf_check_unlogged_user();
		$current_user->guest = 1;
		$current_user->timezone = 0;
		sf_get_guest_cookie();
	} else {
		if ($member['admin'] || (is_multisite() && is_super_admin()))
		{
			$current_user->forumadmin = 1;
			$current_user->moderator = 1;
		} else {
			if(sf_is_forum_moderator($sfvars['forumid']))
			{
				$current_user->moderator = 1;
			}
		}
		$current_user->display_name = $member['display_name'];
		$current_user->member = 1;
		$current_user->lastvisit = strtotime($member['lastvisit']);
		$current_user->timezone = $member['user_options']['timezone'];
		if(empty($current_user->timezone)) $current_user->timezone=0;
	}
	$current_user->display_name = $current_user->display_name;

	# now get all the permissions
	$they = sf_get_global_permissions($forumid);

	# save the permissions to global current_user variable
	$current_user->sfaccess = $they['Can view forum'];
	$current_user->sfforumlistaccess = $they['Can view forum lists only'];
	$current_user->sftopiclistaccess = $they['Can view forum and topic lists only'];
	$current_user->sfadminview = $they['Can view admin posts'];
	$current_user->sfaddnew = $they['Can start new topics'];
	$current_user->sfreply = $they['Can reply to topics'];
	$current_user->sflinkuse = $they['Can create linked topics'];
	$current_user->sfbreaklink = $they['Can break linked topics'];
	$current_user->sfpintopics = $they['Can pin topics'];
	$current_user->sfmovetopics = $they['Can move topics'];
	$current_user->sfmoveposts = $they['Can move posts'];
	$current_user->sfeditalltitle = $they['Can edit any topic titles'];
	$current_user->sfeditowntitle = $they['Can edit own topic titles'];
	$current_user->sflock = $they['Can lock topics'];
	$current_user->sfdelete = $they['Can delete topics'];
	$current_user->sfeditall = $they['Can edit own posts forever'];
	$current_user->sfstopedit = $they['Can edit own posts until reply'];
	$current_user->sfedit = $they['Can edit any posts'];
	$current_user->sfdeleteown = $they['Can delete own posts'];
	$current_user->sfdelete = $they['Can delete any posts'];
	$current_user->sfpinposts = $they['Can pin posts'];
	$current_user->sfreassign = $they['Can reassign posts'];
	$current_user->sfemail = $they['Can view users email addresses'];
	$current_user->sfprofiles = $they['Can view members profiles'];
	$current_user->sfmemberlist = $they['Can view members lists'];
	$current_user->sfreport = $they['Can report posts'];
	$current_user->sfspam = $they['Can bypass spam control'];
	$current_user->sfspoilers = $they['Can use spoilers'];
	$current_user->sfviewlinks = $they['Can view links'];
	if ($they['Can bypass post moderation']) {
		$current_user->sfmoderated = 0;
	} else {
		$current_user->sfmoderated = 1;
	}
	if ($they['Can bypass post moderation once']) {
		$current_user->sfmodonce = 0;
	} else {
		$current_user->sfmodonce = 1;
	}
	$current_user->sfuploadimg = $they['Can upload images'];
	$current_user->sfuploadmedia = $they['Can upload media'];
	$current_user->sfuploadfile = $they['Can upload files'];
	$current_user->sfusersig = $they['Can use signatures'];
	$current_user->sfuploadsig = $they['Can upload signatures'];
	$current_user->sfsubscriptions = $they['Can subscribe'];
	$current_user->sfwatch = $they['Can watch topics'];
	$current_user->sftopicstatus = $they['Can change topic status'];
	$current_user->sfrateposts = $they['Can rate posts'];
	$current_user->sfapprove = $they['Can moderate pending posts'];

	$current_user->sfforumicons = 0;
	if ($current_user->sfbreaklink ||
		$current_user->sfedit ||
		$current_user->sfdelete ||
		$current_user->sfpintopics ||
		$current_user->sfmovetopics ||
		$current_user->sflock ||
		$current_user->sfeditalltitle ||
		$current_user->sftopicstatus)
	{
		$current_user->sfforumicons = 1;
	}

	$current_user->sftopicicons = 0;
	if ($current_user->sfapprove ||
		$current_user->sfemail ||
		$current_user->sfpinposts ||
		$current_user->sfedit ||
		$current_user->sfdelete ||
		$current_user->sfdeleteown ||
		$current_user->sfmoveposts)
	{
		$current_user->sftopicicons = 1;
	}

	if(sf_get_option('sfprivatemessaging'))
	{
		$current_user->sfusepm = $they['Can use private messaging'];
	} else {
		$current_user->sfusepm = 0;
	}

	$sfavatars = sf_get_option('sfavatars');
	if($sfavatars['sfavataruploads'])
	{
		$current_user->sfavatars = $they['Can upload avatars'];
	} else {
		$current_user->sfavatars = 0;
	}

	# regardless of the permissions, the following are overriden for guests
	if($current_user->guest)
	{
		# these go without saying but we can ensure they are off
		$current_user->sfforumicons = 0;
		$current_user->sftopicicons = 0;
	}

	$current_user->newpostlist = 0;

	$CACHE['user'] = $current_user->ID;
	$CACHE['forumid'] = $forumid;

	return;
}

# ------------------------------------------------------
# sf_get_guest_cookie()
#
# load cookie data if a returning guest
# ------------------------------------------------------
function sf_get_guest_cookie()
{
	global $current_user;

	if (isset($_COOKIE['guestname_'.COOKIEHASH])) $current_user->guestname = $_COOKIE['guestname_'.COOKIEHASH];
	if (isset($_COOKIE['guestemail_'.COOKIEHASH])) $current_user->guestemail = $_COOKIE['guestemail_'.COOKIEHASH];
	if (isset($_COOKIE['sflast_'.COOKIEHASH])) $current_user->lastvisit = $_COOKIE['sflast_'.COOKIEHASH];

	$current_user->display_name = $current_user->guestname;

	return;
}

# ------------------------------------------------------
# sf_build_membership_cache()
#
# load usergroup memberships for current user into cache
# ------------------------------------------------------
function sf_build_membership_cache()
{
	global $current_user, $CACHE, $sfglobals, $wpdb;

	if($CACHE['membership'] == true) return;

	if (sf_is_forum_admin($current_user->ID)) return;

	$memberships = array();

	if (($current_user->ID != '') && ($current_user->ID != 0))
	{
		# get the usergroup memberships for the current user
		$memberships = sf_get_user_memberships($current_user->ID);
	}
	if (empty($memberships) || (($current_user->ID == '') || ($current_user->ID == 0)))
	{
		# user is a guest or unassigned member so get the global permissions from the guest usergroup
		$value = sf_get_sfmeta('default usergroup', 'sfguests');
		$memberships[] = $wpdb->get_row("SELECT usergroup_id, usergroup_name, usergroup_desc FROM ".SFUSERGROUPS." WHERE usergroup_id=".$value[0]['meta_value'], ARRAY_A);
	}
	# put in the cache
	$sfglobals['memberships'] = $memberships;

	$CACHE['membership'] = true;
	return;
}

# ------------------------------------------------------
# sf_build_permissions_cache()
#
# load permissions table into cache
# ------------------------------------------------------
function sf_build_permissions_cache()
{
	global $wpdb, $sfglobals, $CACHE;

	if($CACHE['permissions'] == true) return;

	$sfglobals['permissions'] = $wpdb->get_results("SELECT forum_id, usergroup_id, permission_role FROM ".SFPERMISSIONS." ORDER BY permission_id");

	$CACHE['permissions'] = true;

	return;
}

# ------------------------------------------------------
# sf_build_roles_cache()
#
# load roles table into cache
# ------------------------------------------------------
function sf_build_roles_cache()
{
	global $wpdb, $sfglobals, $CACHE;

	if($CACHE['roles'] == true) return;

	$roles = $wpdb->get_results("SELECT role_id, role_actions FROM ".SFROLES." ORDER BY role_id");
	if($roles)
	{
		foreach($roles as $role)
		{
			$sfglobals['roles'][$role->role_id] = unserialize($role->role_actions);
		}
	}

	$CACHE['roles'] = true;

	return;
}

# ------------------------------------------------------
# sf_build_ranks_cache()
#
# load forum ranks into cache
# ------------------------------------------------------
function sf_build_ranks_cache()
{
	global $sfglobals, $CACHE;

	if($CACHE['ranks'] == true) return;

	$sfglobals['ranks'] = array();
	$sfglobals['special_ranks'] = array();

	# get forum rankings information
	$rankdata = sf_get_sfmeta('forum_rank');
	if ($rankdata)
	{
		# put into arrays to make easy to sort
		foreach ($rankdata as $x => $info)
		{
			$sfglobals['ranks']['title'][$x] = $info['meta_key'];
			$data = unserialize($info['meta_value']);
			$sfglobals['ranks']['posts'][$x] = $data['posts'];
			$sfglobals['ranks']['badge'][$x] = '';
			if (isset($data['badge'])) $sfglobals['ranks']['badge'][$x] = $data['badge'];
		}
		# sort rankings highest to lowest
		array_multisort($sfglobals['ranks']['posts'], SORT_ASC, $sfglobals['ranks']['title'], $sfglobals['ranks']['badge']);
	}

	# get the special forum ranks
	$rankdata = sf_get_sfmeta('special_rank');
	if ($rankdata)
	{
		foreach ($rankdata as $x => $rank)
		{
			$data = unserialize($rank['meta_value']);
			$sfglobals['special_ranks'][$rank['meta_key']]['badge'] = $data['badge'];
			if (!empty($data['users']))
			{
				foreach ($data['users'] as $user)
				{
					$sfglobals['special_ranks'][$rank['meta_key']]['users'][$user] = true;
				}
			}
		}
	}

	$CACHE['ranks'] = true;

	return;
}

# ------------------------------------------------------
# sf_build_memberdata_cache()
#
# load permissions table into cache
# ------------------------------------------------------
function sf_build_memberdata_cache()
{
	global $wpdb, $current_user, $sfglobals, $CACHE;

	if($CACHE['member'] == true) return;

	$sfglobals['member'] = '';
	if (($current_user->ID != '') && ($current_user->ID != 0))
	{
		$memberdata = $wpdb->get_row("SELECT * FROM ".SFMEMBERS." WHERE user_id=".$current_user->ID);

		#check for ghost user
		if (empty($memberdata))
		{
			#create the member
			sf_create_member_data($current_user->ID);

			#reload member data
			$memberdata = $wpdb->get_row("SELECT * FROM ".SFMEMBERS." WHERE user_id=".$current_user->ID);
		}

		$sfglobals['member']['display_name'] = $memberdata->display_name;
		$sfglobals['member']['avatar'] = unserialize($memberdata->avatar);
		$sfglobals['member']['signature'] = $memberdata->signature;

		$sfglobals['member']['admin'] = $memberdata->admin;
		$sfglobals['member']['moderator'] = $memberdata->moderator;
		$sfglobals['member']['pm'] = $memberdata->pm;
		$sfglobals['member']['posts'] = $memberdata->posts;

		$sfglobals['member']['subscribe'] = unserialize($memberdata->subscribe);
		$sfglobals['member']['watches'] = unserialize($memberdata->watches);
		$sfglobals['member']['posts_rated'] = unserialize($memberdata->posts_rated);

		$sfglobals['member']['buddies'] = unserialize($memberdata->buddies);
		$sfglobals['member']['newposts'] = unserialize($memberdata->newposts);
		$sfglobals['member']['admin_options'] = unserialize($memberdata->admin_options);
		$sfglobals['member']['user_options'] = unserialize($memberdata->user_options);

		$sfglobals['member']['lastvisit'] = $memberdata->lastvisit;
		$sfglobals['member']['checktime'] = $memberdata->checktime;

		$sfglobals['member']['feedkey'] = $memberdata->feedkey;
	}

	$CACHE['member'] = true;
	return;
}

# ------------------------------------------------------
# sf_initialise_globals()
#
# calls routines necessary to have loaded when using
# any forum code outside of the actual forum page
# ------------------------------------------------------
function sf_initialise_globals($forumid='')
{
	global $current_user, $sfvars, $SFSTATUS;

	if(!isset($SFSTATUS) || empty($SFSTATUS))
	{
		$SFSTATUS = sfg_get_system_status();
	}

	if($SFSTATUS == 'ok')
	{
		sf_build_memberdata_cache();
		sf_build_membership_cache();
		sf_build_permissions_cache();
		sf_build_roles_cache();
		sf_build_ranks_cache();

		sf_extend_current_user($forumid);
		sf_setup_globals();
	}
	return;
}

?>