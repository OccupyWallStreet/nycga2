<?php
/*
Simple:Press
Profile View Page Rendering
$LastChangedDate: 2011-04-09 08:22:02 -0700 (Sat, 09 Apr 2011) $
$Rev: 5852 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

function sf_render_member_profile($userid, $type)
{
	global $wpdb, $current_user, $sfglobals, $SFPATHS;

	sf_setup_pm_includes();

	# ----------------------------------------------------------------------------------------------
	# Grab all the profile data that we need and set up some useful variables
	$profile = sfc_get_profile_data($userid);

	$sfprofile = sf_get_option('sfprofile');
	$cfields = sf_get_sfmeta('custom_field');

	$ids = array('aim', 'yahoo', 'jabber', 'icq', 'msn', 'skype', 'facebook', 'myspace', 'twitter', 'linkedin');

	# ----------------------------------------------------------------------------------------------
	# Start Main Page of Profile Display
	# header
	$out = '';
    if ($sfprofile['forminforum'] == false)
    {
	   $out.= "\n\n".'<!-- Start of SPF Container (Profile) -->'."\n\n";
	   $out.= '<div id="sforum">'."\n";
    }

	$out.='<div class="sfprofileblock">'."\n";

	$out.= sf_process_hook('sf_hook_pre_profile', array($userid));

	$out.='<table id="sfprofileheadtable"><tr>'."\n";
	$out.='<td width="20px">'.sf_render_avatar('user', $userid, sf_filter_email_display($profile['user_email']), '', false,  0, false).'</td>'."\n";
	$out.='<td><p>'.__("Profile Information for", "sforum").':</p><h3>'.sf_filter_name_display($profile['display_name']).'</h3></td>'."\n";

	# Info cell
	$sfmemberopts = sf_get_option('sfmemberopts');
	if (($current_user->forumadmin || (!$sfmemberopts['sfhidestatus'] || !$profile['user_options']['hidestatus'])) && sf_is_online($userid) ? $status = __("Online", "sforum") : $status = __("Offline", "sforum"));
	$out.='<td width="55%">';
	$out.= '<p>'.sprintf(__("%s has been a member since %s, has made %s posts, last visited %s and is currently %s", "sforum"), sf_filter_name_display($profile['display_name']), sf_date('d', $profile['user_registered']), $profile['posts'], sf_date('d', $profile['lastvisit']), $status).'</p>'."\n";
	$out.='</td>';
	$out.='</tr></table>'."\n";

	$out.= sf_process_hook('sf_hook_post_profile_header', array($userid));

	$out.='</div>'."\n";

	# ----------------------------------------------------------------------------------------------
	# Photos
	if($type=='page')
	{
		if($profile['photos'])
		{
			$out.='<div class="sfprofileblock">'."\n";
			$out.='<div id="sfprofilephotos">';
			foreach($profile['photos'] as $photo)
			{
				if(!empty($photo))
				{
					$out.='<img src="'.$photo.'" alt="" width="'.$sfprofile['photoswidth'].'" />';
				}
			}
			$out.='</div>';
			$out.= sf_process_hook('sf_hook_post_profile_photos', array($userid));
			$out.='</div>';
		}
	}

	# ----------------------------------------------------------------------------------------------
	# Show standard Data
	$out.='<div class="sfprofileblock">';
	$out.='<table class="sfprofilemaintable">'."\n";

	# Login and Display name
	if($userid == $current_user->ID || $current_user->forumadmin)
	{
		$out.='<tr>';
		$out.='<td class="sfstandardlabel">'.__("Login Name", "sforum").'</td>';
		$out.='<td class="sfdata">'.esc_attr($profile['user_login']).'</td>';
		$out.='<td class="sfstandardlabel">'.__("Display Name", "sforum").'</td>';
		$out.='<td class="sfdata">'.sf_filter_name_display($profile['display_name']).'</td>';
		$out.='</tr>';
	}

	# First/Last name
	if ($sfprofile['display']['first_name'] || $sfprofile['display']['last_name']) {
		$span = ($sfprofile['display']['first_name'] && $sfprofile['display']['last_name']) ? 1 : 3;
		$out.= '<tr>';
		if ($sfprofile['display']['first_name'])
		{
			$out.= '<td class="sfstandardlabel">'.sf_filter_title_display($sfprofile['label']['first_name']).'</td>';
			$fname = isset($profile['first_name']) ? $profile['first_name'] : '';
			$out.= '<td class="sfdata" colspan="'.$span.'">'.sf_filter_name_display($fname).'</td>';
		}

		if ($sfprofile['display']['last_name'])
		{
			$out.= '<td class="sfstandardlabel">'.sf_filter_title_display($sfprofile['label']['last_name']).'</td>';
			$lname = isset($profile['last_name']) ? $profile['last_name'] : '';
			$out.= '<td class="sfdata" colspan="'.$span.'">'.sf_filter_name_display($lname).'</td>';
		}
		$out.= '</tr>';
	}

	# Email and Website
	$website=false;
	$email=false;
	if($sfprofile['display']['user_url']) $website=true;
	if($userid == $current_user->ID || $current_user->forumadmin) $email=true;
	if($website || $email)
	{
		$span=3;
		if($website && $email) $span=1;
		$out.='<tr>';
		if($email)
		{
			$out.='<td class="sfstandardlabel">'.__("Email Address", "sforum").'</td>';
			$out.='<td class="sfdata" colspan="'.$span.'">'.sf_filter_email_display($profile['user_email']).'</td>';
		}
		if($website)
		{
			$out.='<td class="sfstandardlabel">'.sf_filter_title_display($sfprofile['label']['user_url']).'</td>';
			$url = isset($profile['user_url']) ? $profile['user_url'] : '';
			$out.='<td class="sfdata" colspan="'.$span.'">'.make_clickable(sf_filter_url_display($url)).'</td>';
		}
		$out.='</tr>';
	}

	# Location and registration date
	if($sfprofile['display']['location'])
	{
		$out.='<tr>';
		$out.='<td class="sfstandardlabel">'.sf_filter_title_display($sfprofile['label']['location']).'</td>';
		$location = isset($profile['location']) ? $profile['location'] : '';
		$out.='<td class="sfdata">'.esc_attr($location).'</td>';
		$out.='<td class="sfstandardlabel">'.__("Member Since", "sforum").'</td>';
		$out.='<td class="sfdata">'.sf_date('d', $profile['user_registered']).'</td>';
		$out.='</tr>';
	}

	# Biography
	if($sfprofile['display']['description'])
	{
		$out.='<tr>';
		$out.='<td class="sfstandardlabel">'.sf_filter_title_display($sfprofile['label']['description']).'</td>';
		$description = isset($profile['description']) ? $profile['description'] : '';
		$out.='<td class="sfdata" colspan="3">'.sf_filter_text_display($description).'</td>';

		$out.='</tr>';
	}

	$out.='</table>';

	$out.= sf_process_hook('sf_hook_post_profile_personal', array($userid));

	# Web Id's
	$show=array();
	foreach($ids as $id)
	{
		if($sfprofile['display'][$id]) $show[]=$id;
	}
	if($show)
	{
		$out.='<table class="sfprofilemaintable">'."\n";
		$pos=0;
		foreach($show as $id)
		{
			if($pos==0)
			{
				$out.='<tr>';
			}
			$out.='<td class="sfidlabel"><img src="'.SFRESOURCES.'profile/'.$id.'.png" alt="" /></td>'."\n";

			# little bodge here as 'yim' was wrongly saved as 'yahoo' in profile options
			if($id == 'yahoo') $id='yim';

            if (!empty($profile[$id]))
            {
                $text = sf_filter_title_display($profile[$id]);
                # check for clickable links in facebook, myspace and twitter
    			if ($id == 'facebook') $text = make_clickable('http://facebook.com/'.$text);
    			if ($id == 'myspace') $text = make_clickable('http://myspace.com/'.$text);
    			if ($id == 'twitter') $text = make_clickable('http://twitter.com/'.$text);
    			if ($id == 'linkedin') $text = make_clickable('http://linkedin.com/in/'.$text);
                $out.='<td class="sfdata">'.$text.'</td>';
            } else {
                $out.='<td class="sfdata"></td>';
            }
			$pos++;
			if($pos==2)
			{
				$out.='</tr>';
				$pos=0;
			}
		}
		if($pos != 2) $out.='</tr>';
		$out.='</table>';

		$out.= sf_process_hook('sf_hook_post_profile_online', array($userid));
	}

	# Custom fields
	if($cfields)
	{
		$out.='<table class="sfprofilemaintable">'."\n";

		foreach($cfields as $key=>$info)
		{
			$field = $info['meta_key'];
			if($sfprofile['display'][$field])
			{
				$out.='<tr>';
				$out.='<td class="sfcustomlabel">'.sf_filter_title_display($sfprofile['label'][$field]).'</td>';
				$out.='<td class="sfdata">';
				# is it a custom field checkbox
				$cbox=false;
				$thiscfield=unserialize($info['meta_value']);
				if($thiscfield['type'] == 'checkbox')
				{
					$cbox=true;
				}
				if($cbox)
				{
					if($profile[$field] ? $data=__("Yes", "sforum") : $data=__("No", "sforum"));
				} else {
					if($thiscfield['type'] == 'textarea')
					{
						$data=wpautop($profile[$field]);
					} else {
						$data=esc_attr($profile[$field]);
					}
				}
				$out.=$data;
				$out.='</td>';
				$out.='</tr>';
			}
		}
		$out.='</table>';

		$out.= sf_process_hook('sf_hook_post_profile_custom', array($userid));
	}

	# System stuff - do we show anything?
	$show=false;
	foreach($sfprofile['system'] as $item)
	{
		if($item) $show=true;
	}
	if($show)
	{
		$out.='<table class="sfprofilemaintable">'."\n";
		$out.='<tr>';
		foreach($sfprofile['system'] as $item=>$show)
		{
			if($show)
			{
				$out.='<td class="sfsystemlabel">'.sf_filter_title_display($sfprofile['label'][$item]).'</td>';
			}
		}
		$badge='';
		$out.='</tr><tr>';
		foreach($sfprofile['system'] as $item=>$show)
		{
			if($show)
			{
				switch($item)
				{
					case 'forumrank':
						$user_rank='';
						if(sf_is_forum_admin($userid))
						{
							$user_rank='<td class="sfdata">'.__("Administrator", "sforum").'</td>';
						} else {
							$rankdata = $sfglobals['ranks'];
							if ($rankdata)
							{
								# find ranking of current user
								for ($x=0; $x<count($rankdata['posts']); $x++)
								{
									if ($profile['posts'] <= $rankdata['posts'][$x])
									{
										if ($rankdata['badge'][$x] && file_exists(SF_STORE_DIR.'/'.$SFPATHS['ranks'].'/'.$rankdata['badge'][$x]))
										{
											# badge exists
											$badge = SF_STORE_URL.'/'.$SFPATHS['ranks'].'/'.$rankdata['badge'][$x];
										}
										$forum_rank = $rankdata['title'][$x];
										$user_rank='<td class="sfdata">'.esc_attr($forum_rank).'</td>';
										break;
									}
								}
							}
						}
						if($user_rank == '')
						{
							$out.='<td class="sfdata">'.__("No Forum Rank", "sforum").'</td>';
						} else {
							$out.=$user_rank;
						}
						break;
					case 'specialrank':
						$user_rank='';
					    if (isset($sfglobals['special_ranks']))
					    {
					        foreach ($sfglobals['special_ranks'] as $key => $rank)
					        {
					            if (isset($rank['users'][$userid]))
					            {
					                # special rank exists for the user
					                if ($rank['badge'] && file_exists(SF_STORE_DIR.'/'.$SFPATHS['ranks'].'/'.$rank['badge']))
					                {
					                   # badge exists
					                   $badge = SF_STORE_URL.'/'.$SFPATHS['ranks'].'/'.$rank['badge'];
					                }
					                $special_rank = $key;
									$user_rank='<td class="sfdata">'.esc_attr($special_rank).'</td>';
									break;
					            }
					        }
					    }
						if($user_rank == '')
						{
							$out.='<td class="sfdata">'.__("No Special Rank", "sforum").'</td>';
						} else {
							$out.=$user_rank;
						}
						break;
					case 'badge':
						if($badge)
						{
							$out.='<td class="sfdata"><img src="'.esc_url($badge).'" alt="" /></td>';
						} else {
							$out.='<td class="sfdata">'.__("No Forum Badge", "sforum").'</td>';
						}
						break;
					case 'memberships':
						if(sf_is_forum_admin($userid))
						{
							$out.='<td class="sfdata">'.sprintf(__("Administrator %s No User Group Membership", "sforum"), '<br />').'</td>';
						} else {
							$memberships = sf_get_user_memberships($userid);
							if($memberships)
							{
								$out.='<td class="sfdata">';
								foreach($memberships as $m)
								{
									$out.=sf_filter_title_display($m['usergroup_name']).'<br />';
								}
								$out.='</td>';
							}
						}
						break;
				}
			}
		}
		$out.='</tr></table>';
	}
	$out.='</div>';

	$out.= sf_process_hook('sf_hook_post_profile', array($userid));

	# ----------------------------------------------------------------------------------------------
	# add the buttons depending on user viewing and display type
	$out.= '<hr /><table border="0"><tr>';


	# Add to Buddy list of allowed
	if ($current_user->sfusepm && sf_is_pm_user($userid) && $current_user->ID != $userid && !$sfglobals['lockdown'])
	{
		if(sf_is_buddy($userid))
		{
			$out.='<td><form action="'.$_SERVER['HTTP_REFERER'].'" method="post" name="delbuddy">'."\n";
			$out.='<input type="hidden" name="oldbuddy" value="'.$userid.'" />'."\n";
			$out.='<input type="submit" class="sfxcontrol" name="delnewbuddy" value="'.sf_split_button_label(esc_attr(__("Remove", "sforum").' '.sf_filter_name_display($profile['display_name']).' '.__("From Buddy List", "sforum")), 1).'" />'."\n";
			$out.='</form></td>'."\n";
		} else {
			$out.='<td><form action="'.$_SERVER['HTTP_REFERER'].'" method="post" name="addbuddy">'."\n";
			$out.='<input type="hidden" name="newbuddy" value="'.$userid.'" />'."\n";
			$out.='<input type="submit" class="sfxcontrol" name="addnewbuddy" value="'.sf_split_button_label(esc_attr(__("Add", "sforum").' '.sf_filter_name_display($profile['display_name']).' '.__("To Buddy List", "sforum")), 1).'" />'."\n";
			$out.='</form></td>'."\n";
		}
	}

	# View buddy list and permissions
	if($userid == $current_user->ID)
	{
		if ($current_user->sfusepm)
		{
    		$out.= '<td><form action="'.SFURL.'profile/buddies/" method="post" name="buddies">';
			$out.= '<input type="submit" class="sfxcontrol" name="manbuddy" value="'.sf_split_button_label(esc_attr(__("Manage PM Buddy List", "sforum")), 1).'" />'."\n";
    		$out.= '</form></td>';
		}

    	$sfmemberopts = sf_get_option('sfmemberopts');
        if ($sfmemberopts['sfviewperm'] || $current_user->forumadmin)
        {
    		$out.= '<td><form action="'.SFURL.'profile/permissions/" method="post" name="permissions">';
    		$out.= '<input type="submit" class="sfxcontrol" name="viewperms" value="'.sf_split_button_label(esc_attr(__("View Forum Permissions", "sforum")), 1).'" />'."\n";
    		$out.= '</form></td>';
        }
	} else {
		if ($current_user->sfusepm)
		{
			$user = $wpdb->get_var("SELECT user_login FROM ".SFUSERS." WHERE ID=".$userid);
    		$out.= '<td><form action="'.SFURL.'private-messaging/send/'.urlencode($user).'/" method="post" name="buddies">';
			$out.= '<input type="submit" class="sfxcontrol" name="sendpm" value="'.sf_split_button_label(esc_attr(__("Send PM", "sforum")), 1).'" />'."\n";
    		$out.= '</form></td>';
		}
	}

	# Edit the profile record
	if($userid == $current_user->ID || $current_user->forumadmin)
	{
		$out.='<td><form action="'.sf_build_profile_formlink($userid).'" method="post" name="profileedit">';
		$out.='<input type="submit" class="sfxcontrol" name="editprofile" value="'.sf_split_button_label(esc_attr(__("Edit Profile Record", "sforum")), 1).'" />'."\n";
		$out.='</form></td>';
	}

	# View topics user has started and posted in
	if($type == 'popup' || $type=='page')
	{
		$out.='<td><form action="'.SFHOMEURL.'index.php?sf_ahah=search" method="post" name="search">'."\n";
		$out.='<span><input type="hidden" class="sfhiddeninput" name="userid" id="userid" value="'.$userid.'" />';
		$out.='<input type="hidden" class="sfhiddeninput" name="searchoption" id="searchoption" value="All Forums" />';
		$out.='<input type="submit" class="sfxcontrol" name="membersearch" id="membersearch" value="'.sf_split_button_label(esc_attr(__("View Topics Member Has Posted To", "sforum")), 2).'" />'."\n";
		$out.='<input type="submit" class="sfxcontrol" name="memberstarted" id="memberstarted" value="'.sf_split_button_label(esc_attr(__("View Topics Member Started", "sforum")), 1).'" /></span>'."\n";
		$out.='</form></td>'."\n";
	}

	# Add close button if inline
	if($type == 'inline' || $type == 'popup')
	{
		$out.= '<td><input type="button" class="sfxcontrol" name="cancel" value="'.sf_split_button_label(esc_attr(__("Close Profile", "sforum")), 0).'" onclick="hs.close(this);" /></td>';
	}

	$out.='</tr></table><br />';

	$out.= sf_process_hook('sf_hook_post_profile_buttons', array($userid));

	# ----------------------------------------------------------------------------------------------
	# End of page
    if ($sfprofile['forminforum'] == false)
    {
	   $out.= '</div>'."\n";
	   $out.= "\n\n".'<!-- End of SPF Container (Profile) -->'."\n\n";
    }

	return $out;
}

?>