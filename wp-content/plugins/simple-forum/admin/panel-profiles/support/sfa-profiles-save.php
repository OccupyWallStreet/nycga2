<?php
/*
Simple:Press
Admin Profile Update Support Functions
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

#= Save Options Data ===============================
function sfa_save_options_data()
{
	check_admin_referer('forum-adminform_options', 'forum-adminform_options');
	$mess = __("Profile Options Updated", "sforum");

	$sfprofile=sf_get_option('sfprofile');

	$sfprofile['nameformat'] = sf_esc_int($_POST['nameformat']);
	$sfprofile['displaymode'] = sf_esc_int($_POST['displaymode']);
	$sfprofile['displaypage'] = sf_filter_save_cleanurl($_POST['displaypage']);
	$sfprofile['displayquery'] = sf_filter_title_save(trim($_POST['displayquery']));
	$sfprofile['formmode'] = sf_esc_int($_POST['formmode']);
	$sfprofile['formpage'] = sf_filter_save_cleanurl($_POST['formpage']);
	$sfprofile['formquery'] = sf_filter_title_save(trim($_POST['formquery']));
	$sfprofile['photosmax'] = sf_esc_int($_POST['photosmax']);
	$sfprofile['photoswidth'] = sf_esc_int($_POST['photoswidth']);

	if($sfprofile['photosmax'] && $sfprofile['photoswidth']==0)
	{
		$sfprofile['photoswidth']=300;
	}

	$sfsigimagesize = array();
	$sfsigimagesize['sfsigwidth'] = sf_esc_int($_POST['sfsigwidth']);
	$sfsigimagesize['sfsigheight'] = sf_esc_int($_POST['sfsigheight']);
	sf_update_option('sfsigimagesize', $sfsigimagesize);

	if (isset($_POST['firstvisit'])) { $sfprofile['firstvisit'] = true; } else { $sfprofile['firstvisit'] = false; }
	if (isset($_POST['forcepw'])) { $sfprofile['forcepw'] = true; } else { $sfprofile['forcepw'] = false; }
	if (isset($_POST['displayinforum'])) { $sfprofile['displayinforum'] = true; } else { $sfprofile['displayinforum'] = false; }
	if (isset($_POST['forminforum'])) { $sfprofile['forminforum'] = true; } else { $sfprofile['forminforum'] = false; }
	if (isset($_POST['profileinstats'])) { $sfprofile['profileinstats'] = true; } else { $sfprofile['profileinstats'] = false; }

	$sfprofile['profilelink'] = sf_esc_int($_POST['profilelink']);
	$sfprofile['weblink'] = sf_esc_int($_POST['weblink']);

	if($_POST['profilelink'] != 3 && ($_POST['profilelink'] == $_POST['weblink']))
	{
		$mess.= '<br />'.__("Profile and Website Links can not be the same", "sforum");
		$sfprofile['weblink']=3;
	}

	$sfprofile['sfprofiletext'] = sf_filter_text_save(trim($_POST['sfprofiletext']));

	sf_update_option('sfprofile', $sfprofile);

	return $mess;
}

#= Save Manage Data ===============================
function sfa_save_data_data()
{
	check_admin_referer('forum-adminform_data', 'forum-adminform_data');
	$mess = __("Profile Data Items Updated", "sforum");

	$sfprofile = sf_get_option('sfprofile');

	foreach($sfprofile['require'] as $field=>$value)
	{
		if(isset($_POST['r-'.$field]) ? $sfprofile['require'][$field]=true : $sfprofile['require'][$field]=false);
	}

	foreach($sfprofile['include'] as $field=>$value)
	{
		if(isset($_POST['i-'.$field]) ? $sfprofile['include'][$field]=true : $sfprofile['include'][$field]=false);
		# if required then this MUST be set true
		if($sfprofile['require'][$field]) $sfprofile['include'][$field]=true;
	}

	foreach($sfprofile['display'] as $field=>$value)
	{
		if(isset($_POST['d-'.$field]) ? $sfprofile['display'][$field]=true : $sfprofile['display'][$field]=false);
	}

	foreach($sfprofile['system'] as $field=>$value)
	{
		if(isset($_POST[$field]) ? $sfprofile['system'][$field]=true : $sfprofile['system'][$field]=false);
	}

	foreach($sfprofile['label'] as $field=>$value)
	{
		if(!empty($_POST['l-'.$field]) ? $label=$_POST['l-'.$field] : $label=$field);
		$sfprofile['label'][$field] = sf_filter_title_save($label);
	}

	sf_update_option('sfprofile', $sfprofile);
	return $mess;
}

#= Save Custom Fields ===============================
function sfa_save_fields_data()
{
	check_admin_referer('forum-adminform_fields', 'forum-adminform_fields');

	$mess = '';

	$fields = array();
	$sfprofile = sf_get_option('sfprofile');

	for($x=0; $x<count(array_unique($_POST['cfieldname'])); $x++)
	{
		if (!empty($_POST['cfieldname'][$x]))
		{
			$fields = array();
			$fields['type'] = sf_esc_str($_POST['cfieldtype'][$x]);
			$fields['selectvalues'] = sf_filter_title_save(trim($_POST['cfieldvalues'][$x]));

			# ensure type selected
			if ($fields['type'] != 'none')
			{
				if (($fields['type'] == 'select' || $fields['type'] == 'radio') && empty($fields['selectvalues']))
				{
					$mess .= __('One Select/Radio Custom Field Missing Values - Not Updated!', 'sforum').'<br />';
					continue;
				}
				$fname = sf_create_slug(sf_filter_title_save(trim($_POST['cfieldname'][$x])),'');
				$fname = preg_replace('|[^a-z0-9_]|i', '', $fname);

				sf_add_sfmeta('custom_field', $fname, serialize($fields));

				# add to admins list
				$sfprofile['require'][$fname] = true;
				$sfprofile['include'][$fname] = true;
				$sfprofile['display'][$fname] = true;
				$sfprofile['label'][$fname] = sf_filter_title_save($fname);
			} else {
				$mess .= __('One Custom Field Missing Type - Not Updated!', 'sforum').'<br />';
			}
		}
	}
	sf_update_option('sfprofile', $sfprofile);

	$mess .= __('Custom Fields Updated!', 'sforum');
	return $mess;
}

function sfa_save_avatars_data()
{
	check_admin_referer('forum-adminform_avatars', 'forum-adminform_avatars');

	$mess = '';

	$sfavatars = array();
	if (isset($_POST['sfshowavatars'])) { $sfavatars['sfshowavatars'] = true; } else { $sfavatars['sfshowavatars'] = false; }
	if (isset($_POST['sfavataruploads'])) { $sfavatars['sfavataruploads'] = true; } else { $sfavatars['sfavataruploads'] = false; }
	if (isset($_POST['sfavatarpool'])) { $sfavatars['sfavatarpool'] = true; } else { $sfavatars['sfavatarpool'] = false; }
	if (isset($_POST['sfavatarremote'])) { $sfavatars['sfavatarremote'] = true; } else { $sfavatars['sfavatarremote'] = false; }
	if (isset($_POST['sfavatarreplace'])) { $sfavatars['sfavatarreplace'] = true; } else { $sfavatars['sfavatarreplace'] = false; }
	$sfavatars['sfavatarsize'] = sf_esc_int($_POST['sfavatarsize']);
	$sfavatars['sfavatarfilesize'] = sf_esc_int($_POST['sfavatarfilesize']);
	if(empty($sfavatars['sfavatarsize']) || $sfavatars['sfavatarsize'] == 0) $sfavatars['sfavatarsize'] = 50;
	if(empty($sfavatars['sfavatarfilesize']) || $sfavatars['sfavatarfilesize'] == 0) $sfavatars['sfavatarfilesize'] = 10240;
	$sfavatars['sfgmaxrating'] = sf_esc_int($_POST['sfgmaxrating']);
	$current = array();
	$current = sf_get_option('sfavatars');
	if ($_POST['sfavataropts'])
	{
		$list = explode("&", $_POST['sfavataropts']);
		$newarray = array();
		foreach($list as $item)
		{
			$thisone = explode("=", $item);
			$newarray[] = $thisone[1];
		}
		$sfavatars['sfavatarpriority'] = $newarray;
	} else {
		$sfavatars['sfavatarpriority'] = $current['sfavatarpriority'];
	}
	sf_update_option('sfavatars', $sfavatars);

	$mess .= __('Avatars Updated!', 'sforum');
	return $mess;
}

?>