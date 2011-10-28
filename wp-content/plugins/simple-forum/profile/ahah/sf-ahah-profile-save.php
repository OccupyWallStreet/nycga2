<?php
/*
Simple:Press
Ahah call save Profile data
$LastChangedDate: 2011-01-28 05:31:01 -0700 (Fri, 28 Jan 2011) $
$Rev: 5357 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

sf_forum_ahah_support();
sf_initialise_globals();
# --------------------------------------------



$mess = sf_update_profile();

if($mess)
{
	$out='';
	foreach($mess as $message)
	{
		if($message['type'] == 'error')
		{
			$out.= '<p class="sferrorentry">';
		} else {
			$out.= '<p class="sfsuccessentry">';
		}
		$out.= $message['text'].'</p>';
	}
}

echo($out);

die();


function sf_update_profile()
{
	global $wpdb, $sfglobals, $current_user;

	$mess=array();
	$messnum = 0;
	$cfields = sf_get_sfmeta('custom_field');

	check_admin_referer('forum-profile-personal', 'forum-profile-personal');

	# dont update forum if its locked down
    if ($sfglobals['lockdown'])
    {
    	$mess[$messnum]['type'] = 'error';
		$mess[$messnum]['text']=__('This Forum is Currently Locked - Access is Read Only - Profile Not Updated', "sforum");
		return $mess;
    }

	# do we have an actual user to update?
	if(isset($_POST['uid']))
	{
		$userid = sf_esc_int($_POST['uid']);
		$panel  = $_GET['panel'];
	} else {
    	$mess[$messnum]['type'] = 'error';
		$mess[$messnum]['text']=__('Profile Update Aborted - No Valid User', "sforum");
		return $mess;
	}

	# Check the user ID for current user of admin edit
	if($userid != $current_user->ID)
	{
		if(!$current_user->forumadmin)
		{
	    	$mess[$messnum]['type'] = 'error';
			$mess[$messnum]['text']=__('Profile Update Aborted - No Valid User', "sforum");
			return $mess;
		}
	}

	# Get control data
	$sfprofile=sf_get_option('sfprofile');
	$mess='';

	# set up message data
	$sfprofile['panel']['first_name'] = __("Personal Identity", "sforum");
	$sfprofile['panel']['last_name'] = __("Personal Identity", "sforum");
	$sfprofile['panel']['user_url'] = __("Personal Identity", "sforum");
	$sfprofile['panel']['location'] = __("Personal Identity", "sforum");
	$sfprofile['panel']['description'] = __("Personal Identity", "sforum");
	$sfprofile['panel']['aim'] = __("Your Online Identity", "sforum");
	$sfprofile['panel']['yahoo'] = __("Your Online Identity", "sforum");
	$sfprofile['panel']['jabber'] = __("Your Online Identity", "sforum");
	$sfprofile['panel']['icq'] = __("Your Online Identity", "sforum");
	$sfprofile['panel']['msn'] = __("Your Online Identity", "sforum");
	$sfprofile['panel']['skype'] = __("Your Online Identity", "sforum");
	$sfprofile['panel']['facebook'] = __("Your Online Identity", "sforum");
	$sfprofile['panel']['myspace'] = __("Your Online Identity", "sforum");
	$sfprofile['panel']['twitter'] = __("Your Online Identity", "sforum");
	$sfprofile['panel']['linkedin'] = __("Your Online Identity", "sforum");
	if($cfields)
	{
		foreach($cfields as $key=>$info)
		{
			$sfprofile['panel'][$info['meta_key']] = __("Additional Information", "sforum");
		}
	}

	# check all required fields have been filled
	if($panel == 'all')
	{
		foreach($sfprofile['require'] as $field=>$value)
		{
			if($value)
			{
				if(empty($_POST[$field]))
				{
					$required=true;
					# is it a custom field checkbox? If so can't be required as such
					if($cfields)
					{
						foreach($cfields as $key=>$info)
						{
							if($field == $info['meta_key'])
							{
								$thiscfield = unserialize($info['meta_value']);
								if($thiscfield['type'] == 'checkbox') $required=false;
							}
						}
					}
					if($required)
					{
						$mess[$messnum]['type'] = 'error';
						$mess[$messnum]['text'] = sprintf(__("%s is a Required Field. Go to the %s tab, enter missing data and Update Profile again", "sforum"), '<span class="sferrorbold">'.$sfprofile['label'][$field].'</span>', '<span class="sferrorbold">'.$sfprofile['panel'][$field]."</span>");

						$messnum++;
					}
				}
			}
		}
		if($mess) return $mess;
	}

	# Get current display name
	$oldname = sf_get_member_item($userid, 'display_name');

	# Personal Data except that not in usermeta
	if($panel == 'all' || $panel == 'personal')
	{
		update_user_meta($userid, 'first_name', sf_filter_name_save(trim($_POST['first_name'])));
		update_user_meta($userid, 'last_name', sf_filter_name_save(trim($_POST['last_name'])));
		update_user_meta($userid, 'location', sf_filter_title_save(trim($_POST['location'])));
		update_user_meta($userid, 'description', sf_filter_text_save($_POST['description']));

		# User table stuff
		$email = sf_filter_email_save($_POST['user_email']);

		if(!is_email($email))
		{
			$mess[$messnum]['type'] = 'error';
			$mess[$messnum]['text'] = $email.' '.__('is in invalid email address', "sforum");
			$messnum++;
			return $mess;
		}

		if(!empty($_POST['user_url']))
		{
			$url = sf_filter_url_save($_POST['user_url']);
		}

		# Display Name validation
		if($sfprofile['nameformat'] == 3)
		{
			$display_name = sf_filter_name_save($_POST['first_name']).' '.sf_filter_name_save($_POST['last_name']);
		}
		if(empty($display_name) || $sfprofile['nameformat'] == 2)
		{
			$display_name = $wpdb->get_var("SELECT user_login FROM ".SFUSERS." WHERE ID=".$userid);
		}
		if($sfprofile['nameformat'] == 1)
		{
			$display_name = sf_filter_name_save(trim($_POST['display_name']));
		}

		if(empty($display_name)) $display_name = sf_filter_name_save($_POST['user_login']);

		$records=$wpdb->get_results("SELECT user_id FROM ".SFMEMBERS." WHERE display_name='".$display_name."'");
		if($records)
		{
			foreach($records as $record)
			{
				if($record->user_id != $userid)
				{
					$mess[$messnum]['type'] = 'error';
					$mess[$messnum]['text'] = sf_filter_name_display($display_name).' '.__('is already in use - Please choose a different display name', "sforum");
					$messnum++;
					return $mess;
				}
			}
		}

		sf_update_member_item($userid, 'display_name', sf_filter_name_save($display_name));

		# Update new users list with changed display name
		if($oldname != $display_name)
		{
			sf_update_newuser_name($oldname, sf_filter_name_save($display_name));
		}

		$sql = 'UPDATE '.SFUSERS.' SET ';
		$sql.= 'user_url="'.$url.'", ';
		$sql.= 'user_email="'.$email.'" ';
		$sql.= 'WHERE ID='.$userid.';';

		$wpdb->query($sql);
	}

	# Online
	if($panel == 'all' || $panel == 'online')
	{
		update_user_meta($userid, 'aim', sf_filter_title_save(trim($_POST['aim'])));
		update_user_meta($userid, 'yim', sf_filter_title_save(trim($_POST['yim'])));
		update_user_meta($userid, 'jabber', sf_filter_title_save(trim($_POST['jabber'])));
		update_user_meta($userid, 'msn', sf_filter_title_save(trim($_POST['msn'])));
		update_user_meta($userid, 'icq', sf_filter_title_save(trim($_POST['icq'])));
		update_user_meta($userid, 'skype', sf_filter_title_save(trim($_POST['skype'])));
		update_user_meta($userid, 'facebook', sf_filter_title_save(trim($_POST['facebook'])));
		update_user_meta($userid, 'myspace', sf_filter_title_save(trim($_POST['myspace'])));
		update_user_meta($userid, 'twitter', sf_filter_title_save(trim($_POST['twitter'])));
		update_user_meta($userid, 'linkedin', sf_filter_title_save(trim($_POST['linkedin'])));
	}

	# Additional (Custom fields)
	if($panel == 'all' || $panel == 'custom')
	{
		if($cfields)
		{
			foreach ($cfields as $x => $cfield)
			{
				$fielddata = unserialize($cfield['meta_value']);
				$item=$cfield['meta_key'];
				if($fielddata['type'] == 'textarea')
				{
					update_user_meta($userid, $item, sf_filter_text_save($_POST[$item]));
				} else {
					update_user_meta($userid, $item, sf_filter_title_save(trim($_POST[$item])));
				}

			}
		}
	}

	# Options
	if($panel == 'all' || $panel == 'options')
	{
		$options = sf_get_member_item($userid, 'user_options');
		if (isset($_POST['editor'])) $options['editor'] = sf_esc_int($_POST['editor']);
		if (isset($_POST['sf-sync'])) $options['namesync'] = true; else $options['namesync'] = false;
		if (isset($_POST['sf-subpost'])) $options['autosubpost'] = true; else $options['autosubpost'] = false;
		if (isset($_POST['sf-pmemail'])) $options['pmemail'] = true; else $options['pmemail'] = false;
		if (isset($_POST['sf-hidestatus'])) $options['hidestatus'] = true; else $options['hidestatus'] = false;
		if (isset($_POST['sf-timezone'])) $options['timezone'] = sf_esc_int($_POST['sf-timezone']); else $options['timezone'] = 0;
		sf_update_member_item($userid, 'user_options', $options);

		# see if we need to sync WP display name and SPF display name
		if ($options['namesync'] == true)
		{
			$sql = 'UPDATE '.SFUSERS.' SET display_name="'.sf_filter_name_save($display_name).'" WHERE ID='.$userid;
			$wpdb->query($sql);
		}
	}

	# Password
	if($panel == 'all' || $panel == 'password')
	{
		$inc_pw = false;

		if($_GET['forcepw'] == 1 && empty($_POST['newone1']))
		{
			$mess[$messnum]['type'] = 'error';
			$mess[$messnum]['text'] = __('Forum  rules stipulate that you must change your password', "sforum");
			$messnum++;
			return $mess;
		}

		if(!empty($_POST['newone1']))
		{
			$inc_pw = true;
			if((empty($_POST['newone1'])) || (empty($_POST['newone2'])))
			{
				$mess[$messnum]['type'] = 'error';
				$mess[$messnum]['text'] = __('New Password must be Entered Twice', "sforum");
				$messnum++;
				return $mess;
				$inc_pw = false;
			}
			if($_POST['newone1'] != $_POST['newone2'])
			{
				$mess[$messnum]['type'] = 'error';
				$mess[$messnum]['text'] = __('The Two New Passwords entered are Not the Same!', "sforum");
				$messnum++;
				return $mess;
				$inc_pw = false;
			}
			# OK to save new pw
			if($inc_pw == true)
			{
				$newp = wp_hash_password(sf_esc_str($_POST['newone1']));
			}
		}

		if ($inc_pw)
		{
			$sql = 'UPDATE '.SFUSERS.' SET ';
			$sql.= 'user_pass="'.$newp.'" ';
			$sql.= 'WHERE ID='.$userid.';';
			$wpdb->query($sql);

        	# Update the cookies if the password changed.
        	$current_user = wp_get_current_user();
        	if ($current_user->id == $userid) {
    			wp_clear_auth_cookie();
    			wp_set_auth_cookie($userid);
        	}
		}
	}

	# Avatar
	if($panel == 'all' || $panel == 'avatar')
	{
		$avatar = sf_get_member_item($userid, 'avatar');
		if(isset($_POST['uploadfile']))
		{
			if(!empty($_POST['uploadfile']))
			{
				# see if we can rename the file
				$filename = sf_filter_filename_save($_POST['uploadfile']);
				$newfilename = date('U').sf_filter_filename_save($filename);
				$done=rename(SFAVATARDIR.$filename, SFAVATARDIR.$newfilename);
				if($done == true)
				{
					$filename = $newfilename;
				}

				$avatar['uploaded'] = $filename;
				$mess[$messnum]['type'] = 'success';
				$mess[$messnum]['text'] = __('Avatar Saved - Refresh Page to Display', "sforum");
				$messnum++;
			}
		}

		if(isset($_POST['sfpoolavatar']))
		{
			if(!empty($_POST['sfpoolavatar']))
			{
				# get pool avatar name
				$filename = sf_filter_filename_save($_POST['sfpoolavatar']);
				$avatar['pool'] = $filename;
				$mess[$messnum]['type'] = 'success';
				$mess[$messnum]['text'] = __('Pool Avatar Saved - Refresh Page to Display', "sforum");
				$messnum++;
			}
		}

		if(isset($_POST['sfremoteavatar']) && $_POST['sfremoteavatar'] != $avatar['remote'])
		{
			if(!empty($_POST['sfremoteavatar']))
			{
				# get pool avatar name
				$filename = sf_filter_url_save($_POST['sfremoteavatar']);
				$avatar['remote'] = $filename;
				$mess[$messnum]['type'] = 'success';
				$mess[$messnum]['text'] = __('Remote Avatar Saved - Refresh Page to Display', "sforum");
				$messnum++;
			}
		}
		sf_update_member_item($userid, 'avatar', $avatar);
	}

	# Signature
	if($panel == 'all' || $panel == 'signature')
	{
		if(!empty($_POST['signature']))
		{
			# Check if maxmium links has been exceeded
			$sffilters = sf_get_option('sffilters');
			if($sffilters['sfmaxlinks'] > 0 && !$current_user->forumadmin)
			{
				if(substr_count($_POST['signature'], '</a>') > $sffilters['sfmaxlinks'])
				{
					$mess[$messnum]['type'] = 'error';
					$mess[$messnum]['text'] = sprintf(__('Maximum Number of Links Exceeded in Signature - only %s allowed', "sforum"),$sffilters['sfmaxlinks']);
					$messnum++;
					return $mess;
				}
			}
			sf_update_member_item($userid, 'signature', sf_filter_content_save(trim($_POST['signature']), 'edit'));
		}
	}

	# Photos
	if($panel == 'all' || $panel == 'photos')
	{
		$photos=array();
		for($x=0; $x < $sfprofile['photosmax']; $x++)
		{
			$photos[$x] = sf_filter_url_save($_POST['photo'.$x]);
		}
		update_user_meta($userid, 'photos', $photos);
	}

	# save hook
	sf_process_hook('sf_hook_profile_save', array($userid));

	$mess[$messnum]['type'] = 'success';
	$mess[$messnum]['text'] = __('Your Profile has been Updated', "sforum");
	$messnum++;

	return $mess;
}

?>