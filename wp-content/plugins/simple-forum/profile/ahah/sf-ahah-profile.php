<?php
/*
Simple:Press
Ahah call for View Member Profile
$LastChangedDate: 2010-12-22 10:59:40 -0700 (Wed, 22 Dec 2010) $
$Rev: 5111 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

require_once (dirname(__FILE__)."/../sf-profile-display.php");

sf_forum_ahah_support();
sf_setup_pm_includes();

# --------------------------------------------

global $current_user, $sfvars;
sf_initialise_globals();

$killavatar = $killpool = false;

# are we displaying avatar poll
if (isset($_GET['action']) && $_GET['action'] == 'avatarpool') sf_show_avatar_pool();

if (isset($_GET['u'])) $userid = sf_esc_int($_GET['u']);
if (isset($_GET['id'])) $userid = sf_esc_int($_GET['id']);
if (isset($_GET['ug'])) $ugid = sf_esc_int($_GET['ug']);
if (isset($_GET['buddy']))$buddy = sf_esc_str($_GET['buddy']);
if (isset($_GET['avatarremove'])) $killavatar = true;
if (isset($_GET['poolremove'])) $killpool = true;

if ($killavatar && $current_user->ID == $userid) # make sure user is deleting own avatar
{
	echo sf_remove_uploaded_avatar($userid);
	die();
}
if ($killpool && $current_user->ID == $userid) # make sure user is deleting own avatar
{
	echo sf_remove_pool_avatar($userid);
	die();
}
if (!empty($buddy))
{
	echo sf_delete_buddy($userid, $buddy);
} else {
	if ($current_user->sfprofiles)
	{
		if (isset($_GET['show']) ? $type=sf_esc_str($_GET['show']) : $type='popup');
		echo sf_render_member_profile($userid, $type);

		if ($type != 'page')
	    {
	        echo '<style type="text/css">';
	        include_once(SFPROFILEOPOPUPCSS);
	        echo '</style>';
	    }
	} else {
		echo (__('Access Denied', "sforum"));
	}
}

die();

function sf_delete_buddy($uid, $bid)
{
	$list = sf_get_member_item($uid, 'buddies');
	if (!empty($list))
	{
		$newlist = array();
		foreach($list as $user)
		{
			if ($user != $bid)
			{
				$newlist[] = $user;
			}
		}
		sf_update_member_item($uid, 'buddies', $newlist);
	}
}

function sf_remove_uploaded_avatar($userid)
{
	global $wpdb;

	# clear db record
		$avatar = sf_get_member_item($userid, 'avatar');
		$avatar['uploaded'] = '';
		sf_update_member_item($userid, 'avatar', $avatar);

	return '<strong>'.__("Avatar Removed", "sforum").'</strong>';
}

function sf_remove_pool_avatar($userid)
{
	global $wpdb;

	# get the filename
	if(isset($_GET['file']))
	{
		$file=SFAVATARDIR.sf_esc_str($_GET['file']);
		if(file_exists($file))
		{
			$remove = unlink($file);
		}
		$avatar = sf_get_member_item($userid, 'avatar');
		$avatar['pool'] = '';
		sf_update_member_item($userid, 'avatar', $avatar);
	}

	return '<strong>'.__("Avatar Removed", "sforum").'</strong>';
}

function sf_show_avatar_pool()
{
	global $SFPATHS;

	# Open avatar pool folder and get cntents for matching
	$path = SF_STORE_DIR.'/'.$SFPATHS['avatar-pool'].'/';
	$dlist = @opendir($path);
	if (!$dlist)
	{
		echo '<strong>'.__("The avatar pool folder does not exist", "sforum").'</strong>';
	die();
	}

	# start the table display
	echo '<fieldset><legend>'.__("Avatar Pool", "sforum").'</legend>';
	echo '<table width="100%">';
	echo '<th colspan="5"><p>'.__("Please select your avatar from the following available avatars", "sforum").':</p></th>';
	$col = 0;
	while (false !== ($file = readdir($dlist)))
	{
		if ($file != "." && $file != "..")
		{
			if ($col == 0) echo '<tr>';
			echo '<td align="center" width="20%" >';
			echo '<img class="sfavatarpool" src="'.esc_url(SFAVATARPOOLURL.'/'.$file).'" alt="" onclick="sfjSelAvatar(\''.$file.'\', \''.esc_js(__("Avatar selected! Please Update Profile to save!", "sforum")).'\'); return hs.close(this)" />';
			echo '</td>';
			$col++;
			if ($col == 5)
			{
				echo '</tr>';
				$col = 0;
			}
		}
	}
	echo '</table>';
	echo '</fieldset>';
	closedir($dlist);

	die();
}
?>