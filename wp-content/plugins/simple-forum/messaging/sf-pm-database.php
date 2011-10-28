<?php
/*
Simple:Press
Main PM database routines
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# ------------------------------------------------------------------
# sf_get_pm_inbox()
#
# Select all Inbox messages for current user
#	$userid:		Current User
# ------------------------------------------------------------------
function sf_get_pm_inbox($userid)
{
	global $wpdb;

	# Get sorted lst of pm inbox titles first
	$titles = $wpdb->get_results(
			"SELECT DISTINCT title, message_slug
			 FROM ".SFMESSAGES."
			 JOIN ".SFMEMBERS." ON ".SFMESSAGES.".from_id = ".SFMEMBERS.".user_id
			 WHERE to_id = ".$userid." AND inbox=1
			 ORDER BY sent_date DESC");
	if(!$titles) return;

	# Now grab the full records
	$pms = $wpdb->get_results(
			"SELECT SQL_CALC_FOUND_ROWS message_id, ".sf_zone_datetime('sent_date').", from_id, to_id, title, message_status, inbox, sentbox, is_reply,
			 message_slug, ".SFMEMBERS.".display_name, avatar, user_email, admin, moderator
			 FROM ".SFMESSAGES."
			 JOIN ".SFMEMBERS." ON ".SFMESSAGES.".from_id = ".SFMEMBERS.".user_id
			 JOIN ".SFUSERS." ON ".SFMESSAGES.".from_id = ".SFUSERS.".ID
			 WHERE to_id = ".$userid." AND inbox=1
			 ORDER BY message_id ASC");

	return sf_sort_pms($titles, $pms);
}

# ------------------------------------------------------------------
# sf_get_pm_sentbox()
#
# Select all Sentbox messages for current user
#	$userid:		Current User
# ------------------------------------------------------------------
function sf_get_pm_sentbox($userid)
{
	global $wpdb;

	# Get sorted lst of pm inbox titles first
	$titles = $wpdb->get_results(
			"SELECT DISTINCT title, message_slug
			 FROM ".SFMESSAGES."
			 JOIN ".SFMEMBERS." ON ".SFMESSAGES.".from_id = ".SFMEMBERS.".user_id
			 WHERE from_id = ".$userid." AND sentbox=1
			 ORDER BY sent_date DESC");

	if(!$titles) return;

	# Now grab the full records
	$pms = $wpdb->get_results(
			"SELECT SQL_CALC_FOUND_ROWS message_id, ".sf_zone_datetime('sent_date').", from_id, to_id, title, message_status, inbox, sentbox, is_reply,
			message_slug, ".SFMEMBERS.".display_name, avatar, user_email, admin, moderator
			 FROM ".SFMESSAGES."
			 JOIN ".SFMEMBERS." ON ".SFMESSAGES.".to_id = ".SFMEMBERS.".user_id
			 JOIN ".SFUSERS." ON ".SFMESSAGES.".to_id = ".SFUSERS.".ID
			 WHERE from_id = ".$userid." AND sentbox=1
			 ORDER BY message_id ASC");

	return sf_sort_pms($titles, $pms);
}

# ------------------------------------------------------------------
# sf_sort_pms()
#
# Sort query results into required object format
#	$pmlist:		Data Query results
# ------------------------------------------------------------------
function sf_sort_pms($titlelist, $pmlist)
{
	$messages = array();

	$row = 0;
	foreach($titlelist as $this_title)
	{
		$title = $this_title->title;
		$messages[$row]['slug'] = $this_title->message_slug;
		$messages[$row]['title'] = htmlspecialchars_decode($title);

		$index = 0;
		foreach($pmlist as $pm)
		{
			if($pm->message_slug == $this_title->message_slug)
			{
				$messages[$row]['messages'][$index]['message_id'] = $pm->message_id;
				$messages[$row]['messages'][$index]['sent_date'] = $pm->sent_date;
				$messages[$row]['messages'][$index]['from_id'] = $pm->from_id;
				$messages[$row]['messages'][$index]['to_id'] = $pm->to_id;
				$messages[$row]['messages'][$index]['message_status'] = $pm->message_status;
				$messages[$row]['messages'][$index]['inbox'] = $pm->inbox;
				$messages[$row]['messages'][$index]['sentbox'] = $pm->sentbox;
				$messages[$row]['messages'][$index]['is_reply'] = $pm->is_reply;
				$messages[$row]['messages'][$index]['display_name'] = $pm->display_name;
				$messages[$row]['messages'][$index]['user_email'] = $pm->user_email;
				$messages[$row]['messages'][$index]['avatar'] = $pm->avatar;
				$messages[$row]['messages'][$index]['admin'] = $pm->admin;
				$messages[$row]['messages'][$index]['moderator'] = $pm->moderator;
				$index++;
			}
		}
		$row++;
	}

	return $messages;
}

# ------------------------------------------------------------------
# sf_create_pmuser_select()
#
# Populate the buddy option list of PM users
# ------------------------------------------------------------------
function sf_create_pmuser_select()
{
	global $current_user, $wpdb;

	$out = '';

	$users = sf_get_pm_buddies();
	if($users)
	{
		foreach($users as $user)
		{
			if($user->ID != $current_user->ID)
			{
				$donext = true;
				if($donext)
				{
					$out.= '<option value="'.$user->ID.'">'.sf_filter_name_display($user->display_name).'</option>'."\n";
					$default = '';
				}
			}
		}
	} else {
		$out.= '<option />';
	}
	return $out;
}

function sf_get_pm_inbox_idlist($userid)
{
	global $wpdb;
	return $wpdb->get_results("SELECT message_id FROM ".SFMESSAGES." WHERE to_id = ".$userid." AND inbox=1");
}

function sf_get_pm_sentbox_idlist($userid)
{
	global $wpdb;
	return $wpdb->get_results("SELECT message_id FROM ".SFMESSAGES." WHERE from_id = ".$userid." AND sentbox=1");
}

function sf_get_pm_boxcount($userid)
{
	global $wpdb;
	return $wpdb->get_var("SELECT COUNT(message_id) AS cnt FROM ".SFMESSAGES." WHERE (to_id = ".$userid." AND inbox=1) OR (from_id = ".$userid." AND sentbox=1);");
}

function sf_get_pm_buddies()
{
	global $wpdb, $current_user, $sfglobals;

	$buddylist = array();

	$buddies = 	$sfglobals['member']['buddies'];
	if($buddies)
	{
		$x=0;
		foreach($buddies as $buddy)
		{
			$buddylist[$x]->ID = $buddy;
			$buddylist[$x]->display_name = sf_filter_name_display(sf_get_member_item($buddy, 'display_name'));
			$x++;
		}
	}
	return $buddylist;
}

function sf_add_buddy($id)
{
	global $current_user, $sfglobals;

	# Put member into buddy list if not there
	$buddies = $sfglobals['member']['buddies'];
	if ($buddies)
	{
		if (!in_array($id, $buddies))
		{
			$buddies[] = $id;
		}
	} else {
		$buddies[] = $id;
	}

	sf_update_member_item($current_user->ID, 'buddies', $buddies);
	update_sfnotice('sfmessage', '0@'.__("New Buddy Added", "sforum"));
	return;
}

function sf_remove_buddy($id)
{
	global $current_user, $sfglobals;

	$newbuddies = array();

	$buddies = $sfglobals['member']['buddies'];
	if ($buddies)
	{
		foreach ($buddies as $buddy)
		{
			if ($buddy != $id) $newbuddies[] = $buddy;
		}
		sf_update_member_item($current_user->ID, 'buddies', $newbuddies);
		update_sfnotice('sfmessage', '0@'.__("Buddy Removed", "sforum"));
	}
	return;
}

function sf_is_buddy($id)
{
	global $current_user, $sfglobals;

	# is member ($id) in current users buddy list?
	$buddies = $sfglobals['member']['buddies'];
	if ($buddies)
	{
		if (in_array($id, $buddies))
		{
			return true;
		}
	} else {
		return false;
	}
}

?>