<?php
/*
Simple:Press
Admin Admins Update Your Options Support Functions
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_save_admins_your_options_data()
{
	global $current_user;

    check_admin_referer('my-admin_options', 'my-admin_options');

	$sfadminsettings = array();
	$sfadminsettings = sf_get_option('sfadminsettings');

	# admin settings group
	$sfadminoptions='';
	if (isset($sfadminsettings['sfqueue']))
	{
		if (isset($_POST['sfadminbar'])) { $sfadminoptions['sfadminbar'] = true; } else { $sfadminoptions['sfadminbar'] = false; }
		if (isset($_POST['sfbarfix'])) { $sfadminoptions['sfbarfix'] = true; } else { $sfadminoptions['sfbarfix'] = false; }
	}
	if (isset($_POST['sfnotify'])) { $sfadminoptions['sfnotify'] = true; } else { $sfadminoptions['sfnotify'] = false; }
	if (isset($_POST['sfshownewadmin'])) { $sfadminoptions['sfshownewadmin'] = true; } else { $sfadminoptions['sfshownewadmin'] = false; }
	$sfadminoptions['sfstatusmsgtext'] = sf_filter_text_save(trim($_POST['sfstatusmsgtext']));

	$sfacolours = array();
	if (isset($_POST['submitbg']) ? $sfacolours['submitbg'] = substr(sf_filter_title_save(trim($_POST['submitbg'])), 1) : $sfacolours['submitbg'] = '27537A');
	if (isset($_POST['submitbgt']) ? $sfacolours['submitbgt'] = substr(sf_filter_title_save(trim($_POST['submitbgt'])), 1) : $sfacolours['submitbgt'] = 'FFFFFF');
	if (isset($_POST['bbarbg']) ? $sfacolours['bbarbg'] = substr(sf_filter_title_save(trim($_POST['bbarbg'])), 1) : $sfacolours['bbarbg'] = '0066CC');
	if (isset($_POST['bbarbgt']) ? $sfacolours['bbarbgt'] = substr(sf_filter_title_save(trim($_POST['bbarbgt'])), 1) : $sfacolours['bbarbgt'] = 'FFFFFF');
	if (isset($_POST['formbg']) ? $sfacolours['formbg'] = substr(sf_filter_title_save(trim($_POST['formbg'])), 1) : $sfacolours['formbg'] = '0066CC');
	if (isset($_POST['formbgt']) ? $sfacolours['formbgt'] = substr(sf_filter_title_save(trim($_POST['formbgt'])), 1) : $sfacolours['formbgt'] = 'FFFFFF');
	if (isset($_POST['panelbg']) ? $sfacolours['panelbg'] = substr(sf_filter_title_save(trim($_POST['panelbg'])), 1) : $sfacolours['panelbg'] = '78A1FF');
	if (isset($_POST['panelbgt']) ? $sfacolours['panelbgt'] = substr(sf_filter_title_save(trim($_POST['panelbgt'])), 1) : $sfacolours['panelbgt'] = '000000');
	if (isset($_POST['panelsubbg']) ? $sfacolours['panelsubbg'] = substr(sf_filter_title_save(trim($_POST['panelsubbg'])), 1) : $sfacolours['panelsubbg'] = 'A7C1FF');
	if (isset($_POST['panelsubbgt']) ? $sfacolours['panelsubbgt'] = substr(sf_filter_title_save(trim($_POST['panelsubbgt'])), 1) : $sfacolours['panelsubbgt'] = '000000');
	if (isset($_POST['formtabhead']) ? $sfacolours['formtabhead'] = substr(sf_filter_title_save(trim($_POST['formtabhead'])), 1) : $sfacolours['formtabhead'] = '464646');
	if (isset($_POST['formtabheadt']) ? $sfacolours['formtabheadt'] = substr(sf_filter_title_save(trim($_POST['formtabheadt'])), 1) : $sfacolours['formtabheadt'] = 'D7D7D7');
	if (isset($_POST['tabhead']) ? $sfacolours['tabhead'] = substr(sf_filter_title_save(trim($_POST['tabhead'])), 1) : $sfacolours['tabhead'] = '0066CC');
	if (isset($_POST['tabheadt']) ? $sfacolours['tabheadt'] = substr(sf_filter_title_save(trim($_POST['tabheadt'])), 1) : $sfacolours['tabheadt'] = 'D7D7D7');
	if (isset($_POST['tabrowmain']) ? $sfacolours['tabrowmain'] = substr(sf_filter_title_save(trim($_POST['tabrowmain'])), 1) : $sfacolours['tabrowmain'] = 'EAF3FA');
	if (isset($_POST['tabrowmaint']) ? $sfacolours['tabrowmaint'] = substr(sf_filter_title_save(trim($_POST['tabrowmaint'])), 1) : $sfacolours['tabrowmaint'] = '000000');
	if (isset($_POST['tabrowsub']) ? $sfacolours['tabrowsub'] = substr(sf_filter_title_save(trim($_POST['tabrowsub'])), 1) : $sfacolours['tabrowsub'] = '78A1FF');
	if (isset($_POST['tabrowsubt']) ? $sfacolours['tabrowsubt'] = substr(sf_filter_title_save(trim($_POST['tabrowsubt'])), 1) : $sfacolours['tabrowsubt'] = '000000');
    $sfadminoptions['colors'] = $sfacolours;

	sf_update_member_item($current_user->ID, 'admin_options', $sfadminoptions);

	$mess = __('Your Admin Options Updated (any color option changes take effect on page reload)', "sforum");

	return $mess;
}

function sfa_save_admins_restore_colour()
{
	global $current_user;

	$data = sf_get_member_list($current_user->ID, 'admin_options');
	$sfadminoptions = $data['admin_options'];

	$sfacolours = array();
	$sfacolours['submitbg'] = '27537A';
	$sfacolours['submitbgt'] = 'FFFFFF';
	$sfacolours['bbarbg'] = '0066CC';
	$sfacolours['bbarbgt'] = 'FFFFFF';
	$sfacolours['formbg'] = '0066CC';
	$sfacolours['formbgt'] = 'FFFFFF';
	$sfacolours['panelbg'] = '78A1FF';
	$sfacolours['panelbgt'] = '000000';
	$sfacolours['panelsubbg'] = 'A7C1FF';
	$sfacolours['panelsubbgt'] = '000000';
	$sfacolours['formtabhead'] = '464646';
	$sfacolours['formtabheadt'] = 'D7D7D7';
	$sfacolours['tabhead'] = '0066CC';
	$sfacolours['tabheadt'] = 'D7D7D7';
	$sfacolours['tabrowmain'] = 'EAF3FA';
	$sfacolours['tabrowmaint'] = '000000';
	$sfacolours['tabrowsub'] = '78A1FF';
	$sfacolours['tabrowsubt'] = '000000';
    $sfadminoptions['colors'] = $sfacolours;

	sf_update_member_item($current_user->ID, 'admin_options', $sfadminoptions);

	return __("Colours will update when Page is Reloaded", "sforum");
}

function sfa_save_admins_global_options_data()
{
	global $wpdb;

    check_admin_referer('global-admin_options', 'global-admin_options');

	# admin settings group
	$sfadminsettings='';
	if (isset($_POST['sfqueue'])) { $sfadminsettings['sfqueue'] = true; } else { $sfadminsettings['sfqueue'] = false; }
	if (isset($_POST['sfmodasadmin'])) { $sfadminsettings['sfmodasadmin'] = true; } else { $sfadminsettings['sfmodasadmin'] = false; }
	if (isset($_POST['sfshowmodposts'])) { $sfadminsettings['sfshowmodposts'] = true; } else { $sfadminsettings['sfshowmodposts'] = false; }
	if (isset($_POST['sftools'])) { $sfadminsettings['sftools'] = true; } else { $sfadminsettings['sftools'] = false; }
	if (isset($_POST['sfbaronly'])) { $sfadminsettings['sfbaronly'] = true; } else { $sfadminsettings['sfbaronly'] = false; }
	if (isset($_POST['sfdashboardposts'])) { $sfadminsettings['sfdashboardposts'] = true; } else { $sfadminsettings['sfdashboardposts'] = false; }
	if (isset($_POST['sfdashboardstats'])) { $sfadminsettings['sfdashboardstats'] = true; } else { $sfadminsettings['sfdashboardstats'] = false; }

	sf_update_option('sfadminsettings', $sfadminsettings);

	# do we need to remove the admins queue?
	if($sfadminsettings['sfqueue'] == false)
	{
		$wpdb->query("TRUNCATE TABLE ".SFWAITING);
	}

	$mess = __('Admin Options Updated', "sforum");

	return $mess;
}

function sfa_save_admins_caps_data()
{
	global $current_user;

    check_admin_referer('forum-adminform_sfupdatecaps', 'forum-adminform_sfupdatecaps');

    $users = array_unique($_POST['uids']);

    if (isset($_POST['manage-opts'])) { $manage_opts = $_POST['manage-opts']; } else { $manage_opts = ''; }
    if (isset($_POST['manage-forums'])) { $manage_forums = $_POST['manage-forums']; } else { $manage_forums = ''; }
    if (isset($_POST['manage-ugs'])) { $manage_ugs = $_POST['manage-ugs']; } else { $manage_ugs = ''; }
    if (isset($_POST['manage-perms'])) { $manage_perms = $_POST['manage-perms']; } else { $manage_perms = ''; }
    if (isset($_POST['manage-comps'])) { $manage_comps = $_POST['manage-comps']; } else { $manage_comps = ''; }
    if (isset($_POST['manage-tags'])) { $manage_tags = $_POST['manage-tags']; } else { $manage_tags = ''; }
    if (isset($_POST['manage-users'])) { $manage_users = $_POST['manage-users']; } else { $manage_users = ''; }
    if (isset($_POST['manage-profiles'])) { $manage_profiles = $_POST['manage-profiles']; } else { $manage_profiles = ''; }
    if (isset($_POST['manage-admins'])) { $manage_admins = $_POST['manage-admins']; } else { $manage_admins = ''; }
    if (isset($_POST['manage-tools'])) { $manage_tools = $_POST['manage-tools']; } else { $manage_tools = ''; }
    if (isset($_POST['manage-config'])) { $manage_config = $_POST['manage-config']; } else { $manage_config = ''; }

    if (isset($_POST['old-opts'])) { $old_opts = $_POST['old-opts']; } else { $old_opts = ''; }
    if (isset($_POST['old-forums'])) { $old_forums = $_POST['old-forums']; } else { $old_forums = ''; }
    if (isset($_POST['old-ugs'])) { $old_ugs = $_POST['old-ugs']; } else { $old_ugs = ''; }
    if (isset($_POST['old-perms'])) { $old_perms = $_POST['old-perms']; } else { $old_perms = ''; }
    if (isset($_POST['old-comps'])) { $old_comps = $_POST['old-comps']; } else { $old_comps = ''; }
    if (isset($_POST['old-tags'])) { $old_tags = $_POST['old-tags']; } else { $old_tags = ''; }
    if (isset($_POST['old-users'])) { $old_users = $_POST['old-users']; } else { $old_users = ''; }
    if (isset($_POST['old-profiles'])) { $old_profiles = $_POST['old-profiles']; } else { $old_profiles = ''; }
    if (isset($_POST['old-admins'])) { $old_admins = $_POST['old-admins']; } else { $old_admins = ''; }
    if (isset($_POST['old-tools'])) { $old_tools = $_POST['old-tools']; } else { $old_tools = ''; }
    if (isset($_POST['old-config'])) { $old_config = $_POST['old-config']; } else { $old_config = ''; }

	$data_changed = false;
    for ($index = 0; $index < count($users); $index++)
	{
		# get user index and sanitize
		$uid = intval($users[$index]);

		if ((isset($manage_opts[$uid]) != (isset($old_opts[$uid]) && $old_opts[$uid])) ||
		    (isset($manage_forums[$uid]) != (isset($old_forums[$uid]) && $old_forums[$uid])) ||
		    (isset($manage_ugs[$uid]) != (isset($old_ugs[$uid]) && $old_ugs[$uid])) ||
		    (isset($manage_perms[$uid]) != (isset($old_perms[$uid]) && $old_perms[$uid])) ||
		    (isset($manage_comps[$uid]) != (isset($old_comps[$uid]) && $old_comps[$uid])) ||
		    (isset($manage_tags[$uid]) != (isset($old_tags[$uid]) && $old_tags[$uid])) ||
		    (isset($manage_users[$uid]) != (isset($old_users[$uid]) && $old_users[$uid])) ||
		    (isset($manage_profiles[$uid]) != (isset($old_profiles[$uid]) && $old_profiles[$uid])) ||
		    (isset($manage_admins[$uid]) != (isset($old_admins[$uid]) && $old_admins[$uid])) ||
		    (isset($manage_tools[$uid]) != (isset($old_tools[$uid]) && $old_tools[$uid])) ||
		    (isset($manage_config[$uid]) != (isset($old_config[$uid]) && $old_config[$uid])))
		{
			# Is user still an admin?
			if (!isset($manage_opts[$uid]) &&
			    !isset($manage_forums[$uid]) &&
			    !isset($manage_ugs[$uid]) &&
			    !isset($manage_perms[$uid]) &&
			    !isset($manage_comps[$uid]) &&
			    !isset($manage_tags[$uid]) &&
			    !isset($manage_users[$uid]) &&
			    !isset($manage_profiles[$uid]) &&
			    !isset($manage_admins[$uid]) &&
			    !isset($manage_tools[$uid]) &&
			    !isset($manage_config[$uid]))
			{
				sf_update_member_item($uid, 'admin', 0);
			}
			$data_changed = true;
			$user = new WP_User($uid);

			if (isset($manage_opts[$uid]))
			{
				$user->add_cap('SPF Manage Options');
			} else {
				$user->remove_cap('SPF Manage Options');
			}

			if (isset($manage_forums[$uid]))
			{
				$user->add_cap('SPF Manage Forums');
			} else {
				$user->remove_cap('SPF Manage Forums');
			}

			if (isset($manage_ugs[$uid]))
			{
				$user->add_cap('SPF Manage User Groups');
			} else {
				$user->remove_cap('SPF Manage User Groups');
			}

			if (isset($manage_perms[$uid]))
			{
				$user->add_cap('SPF Manage Permissions');
			} else {
				$user->remove_cap('SPF Manage Permissions');
			}

			if (isset($manage_comps[$uid]))
			{
				$user->add_cap('SPF Manage Components');
			} else {
				$user->remove_cap('SPF Manage Components');
			}

			if (isset($manage_tags[$uid]))
			{
				$user->add_cap('SPF Manage Tags');
			} else {
				$user->remove_cap('SPF Manage Tags');
			}

			if (isset($manage_users[$uid]))
			{
				$user->add_cap('SPF Manage Users');
			} else {
				$user->remove_cap('SPF Manage Users');
			}

			if (isset($manage_profiles[$uid]))
			{
				$user->add_cap('SPF Manage Profiles');
			} else {
				$user->remove_cap('SPF Manage Profiles');
			}

			if (isset($manage_admins[$uid]))
			{
				$user->add_cap('SPF Manage Admins');
			} else {
				$user->remove_cap('SPF Manage Admins');
			}

			if (isset($manage_tools[$uid]))
			{
				$user->add_cap('SPF Manage Toolbox');
			} else {
				$user->remove_cap('SPF Manage Toolbox');
			}

			if (isset($manage_config[$uid]))
			{
				$user->add_cap('SPF Manage Configuration');
			} else {
				$user->remove_cap('SPF Manage Configuration');
			}
		}
	}

	if ($data_changed)
	{
	    $mess = __("Admin Capabilities Updated!", "sforum");
 	} else {
	    $mess = __("No Data Changed!", "sforum");
  	}

    return $mess;
}

function sfa_save_admins_newadmin_data()
{
	global $wpdb;

    check_admin_referer('forum-adminform_sfaddadmins', 'forum-adminform_sfaddadmins');

    if (isset($_POST['newadmins']))
	{
		$newadmins = array_unique($_POST['newadmins']);
	} else {
	    $mess = __("No Users Selected!", "sforum");
		return $mess;
    }
    if (isset($_POST['add-opts'])) { $opts = $_POST['add-opts']; } else { $opts = ''; }
    if (isset($_POST['add-forums'])) { $forums = $_POST['add-forums']; } else { $forums = ''; }
    if (isset($_POST['add-ugs'])) { $ugs = $_POST['add-ugs']; } else { $ugs = ''; }
    if (isset($_POST['add-perms'])) { $perms = $_POST['add-perms']; } else { $perms = ''; }
    if (isset($_POST['add-comps'])) { $comps = $_POST['add-comps']; } else { $comps = ''; }
    if (isset($_POST['add-tags'])) { $tags = $_POST['add-tags']; } else { $tags = ''; }
    if (isset($_POST['add-users'])) { $users = $_POST['add-users']; } else { $users = ''; }
    if (isset($_POST['add-profiles'])) { $profiles = $_POST['add-profiles']; } else { $profiles = ''; }
    if (isset($_POST['add-admins'])) { $admins = $_POST['add-admins']; } else { $admins = ''; }
    if (isset($_POST['add-tools'])) { $tools = $_POST['add-tools']; } else { $tools = ''; }
    if (isset($_POST['add-config'])) { $config = $_POST['add-config']; } else { $config = ''; }

	$added = false;
    for ($index = 0; $index < count($newadmins); $index++)
	{
		# get user index and sanitize
		$uid = intval($newadmins[$index]);
		$user = new WP_User(sf_esc_int($uid));

		if ($opts == 'on')
		{
			$user->add_cap('SPF Manage Options');
		}

		if ($forums == 'on')
		{
			$user->add_cap('SPF Manage Forums');
		}

		if ($ugs == 'on')
		{
			$user->add_cap('SPF Manage User Groups');
		}

		if ($perms == 'on')
		{
			$user->add_cap('SPF Manage Permissions');
		}

		if ($comps == 'on')
		{
			$user->add_cap('SPF Manage Components');
		}

		if ($tags == 'on')
		{
			$user->add_cap('SPF Manage Tags');
		}

		if ($users == 'on')
		{
			$user->add_cap('SPF Manage Users');
		}

		if ($profiles == 'on')
		{
			$user->add_cap('SPF Manage Profiles');
		}

		if ($admins == 'on')
		{
			$user->add_cap('SPF Manage Admins');
		}

		if ($tools == 'on')
		{
			$user->add_cap('SPF Manage Toolbox');
		}

		if ($config == 'on')
		{
			$user->add_cap('SPF Manage Configuration');
		}

		if ($opts == 'on' || $forums == 'on' || $ugs == 'on' || $perms == 'on' || $comps == 'on' || $tags == 'on' || $users == 'on'|| $profiles == 'on'|| $admins == 'on' || $tools == 'on' || $config == 'on')
		{
			$added = true;

			# flag as admin with PM permission and remove moderator flag
			sf_update_member_item($uid, 'admin', 1);
			sf_update_member_item($uid, 'moderator', 0);
			sf_update_member_item($uid, 'pm', 1);

            # admin default options
        	$sfadminoptions = array();
            $sfadminoptions['sfadminbar'] = false;
            $sfadminoptions['sfbarfix'] = false;
            $sfadminoptions['sfnotify'] = false;
            $sfadminoptions['sfshownewadmin'] = false;
            $sfadminoptions['sfstatusmsgtext'] = '';
        	$sfadminoptions['colors']['submitbg'] = '27537A';
        	$sfadminoptions['colors']['submitbgt'] = 'FFFFFF';
			$sfadminoptions['colors']['bbarbg'] = '0066CC';
			$sfadminoptions['colors']['bbarbgt'] = 'FFFFFF';
			$sfadminoptions['colors']['formbg'] = '0066CC';
			$sfadminoptions['colors']['formbgt'] = 'FFFFFF';
			$sfadminoptions['colors']['panelbg'] = '78A1FF';
			$sfadminoptions['colors']['panelbgt'] = '000000';
			$sfadminoptions['colors']['panelsubbg'] = 'A7C1FF';
			$sfadminoptions['colors']['panelsubbgt'] = '000000';
			$sfadminoptions['colors']['formtabhead'] = '464646';
			$sfadminoptions['colors']['formtabheadt'] = 'D7D7D7';
			$sfadminoptions['colors']['tabhead'] = '0066CC';
			$sfadminoptions['colors']['tabheadt'] = 'D7D7D7';
			$sfadminoptions['colors']['tabrowmain'] = 'EAF3FA';
			$sfadminoptions['colors']['tabrowmaint'] = '000000';
			$sfadminoptions['colors']['tabrowsub'] = '78A1FF';
			$sfadminoptions['colors']['tabrowsubt'] = '000000';
            sf_update_member_item($uid, 'admin_options', $sfadminoptions);

			# remove any usergroup permissions
			$wpdb->query("DELETE FROM ".SFMEMBERSHIPS." WHERE user_id=".$uid);
		}
	}
	if ($added)
	{
	    $mess = __("New Admins Added!", "sforum");
 	} else {
		$mess = __("No Data Changed!", "sforum");
	}

	return $mess;
}

?>