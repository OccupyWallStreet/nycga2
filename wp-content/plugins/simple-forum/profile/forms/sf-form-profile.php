<?php
/*
Simple:Press
Profile Form Rendering
$LastChangedDate: 2011-04-27 15:17:54 -0700 (Wed, 27 Apr 2011) $
$Rev: 5990 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

function sf_render_profile_form($panel, $userid, $newuser)
{
	global $wpdb, $current_user, $SFPATHS, $sfglobals;

	$sfavatars = sf_get_option('sfavatars');

	# inline js - setup form and uploader ajax
	add_action( 'wp_footer', 'sfjs_profile_ajax' );

	$out = '<br />';
	$out.= sf_render_queued_message();

	# ----------------------------------------------------------------------------------------------
	# Grab all the profile data that we need and set up some useful variables
	$profile = sfc_get_profile_data($userid);

	$require = '<img src="'.SFRESOURCES.'profile/profile-required.png" alt="" title="'.esc_attr(__("Required Data", "sforum")).'" /> ';
	$display  = '<img src="'.SFRESOURCES.'profile/profile-display.png" alt="" title="'.esc_attr(__("Displayed on Profile to other Members", "sforum")).'" /> ';
	$locked   = '<img src="'.SFRESOURCES.'profile/profile-locked.png" alt="" title="'.esc_attr(__("Locked Data", "sforum")).'" /> ';

	$sfprofile = sf_get_option('sfprofile');

	if($sfprofile['nameformat'] == 2) $profile['display_name'] = $profile['user_login'];
	if($sfprofile['nameformat'] == 3) $profile['display_name'] = sf_filter_name_display($profile['first_name'].' '.$profile['last_name']);

	$show_password_fields = apply_filters('show_password_fields', true);
	$cfields = sf_get_sfmeta('custom_field');
	$sfavatars = sf_get_option('sfavatars');

	if($panel == 'all')
	{
		$class=' class="inline_edit"';
	} else {
		$class = '';
	}

	# ----------------------------------------------------------------------------------------------
	# Start Main Page of Profile Display
    if ($sfprofile['forminforum'] == false)
    {
	   $out.= "\n\n".'<!-- Start of SPF Container (Profile) -->'."\n\n";
	   $out.= '<div id="sforum">'."\n";
    }

	# pre-profile form hook
	$out.= sf_process_hook('sf_hook_pre_profile_form', array($userid));

	# ----------------------------------------------------------------------------------------------
	# Header
	if($panel == 'all')
	{
		$out.='<div class="sfheading">'."\n";
		$out.='<table><tr>'."\n";
		$out.='<td class="sficoncell">'.sf_render_avatar('user', $userid, sf_filter_email_display($profile['user_email']), '', false, 0, false).'</td>'."\n";
		$out.='<td><p>'.__("Profile Information for", "sforum").':<br />'.$profile['user_login'].' ('.sf_filter_name_display($profile['display_name']).')'.'</p></td>'."\n";
		$out.='<td><img class="sfalignright" src="'. SFRESOURCES .'spacer.png" alt="" /><a class="sficon sfalignright" href="'.SFURL.'"><img src="'.SFRESOURCES.'goforum.png" alt="" title="'.esc_attr(__("Return to forum", "sforum")).'" />'.sf_render_icons("Return to forum").'</a></td>'."\n";
		$out.='</tr></table>'."\n";
		$out.='</div>'."\n";

		# Info strip
    	$sfmemberopts = sf_get_option('sfmemberopts');
		if (($current_user->forumadmin || (!$sfmemberopts['sfhidestatus'] || !$profile['user_options']['hidestatus'])) && sf_is_online($userid) ? $status = __("Online", "sforum") : $status = __("Offline", "sforum"));
		$out.= '<div class="sfmessagestrip"><p>'."\n";
		$out.= sprintf(__("%s has been a member since %s, has made %s posts, last visited %s and is currently %s", "sforum"), sf_filter_name_display($profile['display_name']), sf_date('d', $profile['user_registered']), $profile['posts'], sf_date('d', $profile['lastvisit']), $status)."\n";
		$out.= '</p></div>'."\n";

		# special forum message strip
		if(!empty($sfprofile['sfprofiletext']))
		{
			$out.= '<div class="sfmessagestrip"><p>'."\n";
			$out.= sf_filter_text_display($sfprofile['sfprofiletext']);
			$out.= '</p></div>'."\n";
		}

		# ----------------------------------------------------------------------------------------------
		# Icon legend
		$out.= '<p class="sfprofile-legend"><br />'."\n";
		$out.= $require.' '.__("Required Data", "sforum").' '."\n";
		$out.= $display.' '.__("Displayed on Profile to other Members", "sforum").' '."\n";
		$out.= $locked.' '.__("Data Can Not be Changed", "sforum")."\n";
		$out.= '</p><br />'."\n";

		# ----------------------------------------------------------------------------------------------
		# Start Form Container
		$out.= '<div id="sfprofileform">'."\n";

		# ----------------------------------------------------------------------------------------------
		# Toolbar Buttons
		$out.='<table class="sfprofilebar"><tr>'."\n";
		# Personal-------
		$out.='<td id="sftabpersonal" class="sftbar sftbaron" onclick="sfjSwitchProfile(\'personal\')">'.__("Personal Identity", "sforum").'</td>'."\n";
		# Online --------
		if($sfprofile['include']['aim'] || $sfprofile['include']['yahoo'] || $sfprofile['include']['jabber'] || $sfprofile['include']['icq'] || $sfprofile['include']['msn'] || $sfprofile['include']['skype'] || $sfprofile['include']['myspace'] || $sfprofile['include']['facebook'] || $sfprofile['include']['twitter'] || $sfprofile['include']['linkedin'])
		{
			$out.='<td id="sftabonline" class="sftbar" onclick="sfjSwitchProfile(\'online\')">'.__("Your Online Identity", "sforum").'</td>'."\n";
		}
		# Additional ----
		if($cfields)
		{
			$out.='<td id="sftabadditional" class="sftbar" onclick="sfjSwitchProfile(\'additional\')">'.__("Additional Information", "sforum").'</td>'."\n";
		}
		# Options -------
		$out.='<td id="sftaboptions" class="sftbar" onclick="sfjSwitchProfile(\'options\')">'.__("Personal Options", "sforum").'</td>'."\n";
		# Password ------
		if($show_password_fields)
		{
			$out.='<td id="sftabpassword" class="sftbar" onclick="sfjSwitchProfile(\'password\')">'.__("Change Password", "sforum").'</td>'."\n";
		}
		# Avatar -------
		if($sfavatars['sfshowavatars'] && (($sfavatars['sfavataruploads'] && sf_user_can($userid, 'Can upload avatars')) || $sfavatars['sfavatarpool'] || $sfavatars['sfavatarremote']))
		{
			$out.='<td id="sftabavatar" class="sftbar" onclick="sfjSwitchProfile(\'avatar\')">'.__("Select Your Avatar", "sforum").'</td>'."\n";
		}
		# Signature ----
		if(sf_user_can($userid, 'Can use signatures'))
		{
			$out.='<td id="sftabsignature" class="sftbar" onclick="sfjSwitchProfile(\'signature\')">'.__("Setup Your Signature", "sforum").'</td>'."\n";
		}
		# Photos -------
		if($sfprofile['photosmax'])
		{
			$out.='<td id="sftabphotos" class="sftbar" onclick="sfjSwitchProfile(\'photos\')">'.__("Setup Your Photos", "sforum").'</td>'."\n";
		}
		$out.= '</tr></table>'."\n";

		# ----------------------------------------------------------------------------------------------
		# New user display message
		if ($newuser)
		{
            $out.= '<p><b>'.__("Welcome to the forum.", "sforum");
    		if ($show_password_fields)
            {
    			if($sfprofile['forcepw'] == false)
    			{
    				$out.= '&nbsp;'.__("As a new member who has not yet posted, it is recommended that you change your password.", "sforum");
    			} else {
    				$out.= '&nbsp;'.__("As a new member who has not yet posted, it is required by the forum rules that you change your password.", "sforum");
                }
            }
            $out.= '&nbsp;'.__("Please consider entering some personal information about yourself.", "sforum").'</b></p><br />'."\n";
		}
	}

	# ----------------------------------------------------------------------------------------------
	# Setup Form variables
	if($newuser && $sfprofile['forcepw'] ? $forcepw='&forcepw=1' : $forcepw='&forcepw=0');
    $ahahURL = SFHOMEURL."index.php?sf_ahah=profile-save&id=".$userid.$forcepw."&panel=".$panel;
	$target = "sfprofilemsg";

	# ----------------------------------------------------------------------------------------------
	# start of main personal form
	$out.='<form action="'.$ahahURL.'" method="post" name="profilepersonal" id="profilepersonal" enctype="multipart/form-data">'."\n";
	$out.= sfc_create_nonce('forum-profile-personal')."\n";
	$out.='<input type="hidden" size="0" name="uid" id="uid" value="'.$userid.'" />'."\n";

	# ----------------------------------------------------------------------------------------------
	# Personal Identity Set
	if($panel == 'all' || $panel == 'personal')
	{
		$out.='<div id="sfprofilepersonal">'."\n";
		$out.='<fieldset><legend>'.__("Personal Identity", "sforum").'</legend>'."\n";
		$out.='<table class="sfprofiletable">'."\n";

		# user login
		$out.='<tr>'."\n";
		$out.='<td class="sfprofilelabel">'.__("Login ID", "sforum").':</td>'."\n";
		$out.='<td class="sfprofiledata"><input type="text" name="user_login" disabled="disabled" value="'.esc_attr($profile['user_login']).'" /></td>'."\n";
		$out.='<td>'.$locked.'</td>'."\n";
		$out.='</tr>'."\n";

		# display name
		$value=sf_filter_name_display($profile['display_name']);
		$out.='<tr>'."\n";
		$out.='<td class="sfprofilelabel">'.__("Display Name", "sforum").':</td>'."\n";
		if($sfprofile['nameformat'] != 1 ? $disabled=' disabled="disabled"' : $disabled="");
		$out.='<td class="sfprofiledata"><input type="text"'.$disabled.' name="display_name" value="'.$value.'" /></td>'."\n";
		$out.='<td>'.$require.$display."\n";
		if($sfprofile['nameformat'] != 1) $out.= $locked;
		$out.='</td>'."\n";
		$out.='</tr>'."\n";

		# user email
		$value=sf_filter_email_display($profile['user_email']);
		$out.='<tr>'."\n";
		$out.='<td class="sfprofilelabel">'.__("Email Address", "sforum").':</td>'."\n";
		$out.='<td class="sfprofiledata"><input type="text" name="user_email" value="'.$value.'" /></td>'."\n";
		$out.='<td>'.$require.'</td>'."\n";
		$out.='</tr>'."\n";

		# first name
		if($sfprofile['include']['first_name'])
		{
            if (isset($profile['first_name']))
            {
    			$value = sf_filter_name_display($profile['first_name']);
            } else {
                $value = '';
            }
			$label=sf_filter_title_display($sfprofile['label']['first_name']);
			$out.='<tr>'."\n";
			$out.='<td class="sfprofilelabel">'.$label.':</td>'."\n";
			$out.='<td class="sfprofiledata"><input type="text" name="first_name" value="'.$value.'" /></td>'."\n";
			$out.='<td>'."\n";
			if($sfprofile['require']['first_name']) $out.= $require."\n";
			if($sfprofile['display']['first_name']) $out.= $display."\n";
			$out.='</td>'."\n";
			$out.='</tr>'."\n";
		}

		# last name
		if($sfprofile['include']['last_name'])
		{
            if (isset($profile['last_name']))
            {
    			$value = sf_filter_name_display($profile['last_name']);
            } else {
                $value = '';
            }
   			$label=sf_filter_title_display($sfprofile['label']['last_name']);
			$out.='<tr>'."\n";
			$out.='<td class="sfprofilelabel">'.$label.':</td>'."\n";
			$out.='<td class="sfprofiledata"><input type="text" name="last_name" value="'.$value.'" /></td>'."\n";
			$out.='<td>'."\n";
			if($sfprofile['require']['last_name']) $out.= $require."\n";
			if($sfprofile['display']['last_name']) $out.= $display."\n";
			$out.='</td>'."\n";
			$out.='</tr>'."\n";
		}

		# user url
		if($sfprofile['include']['user_url'])
		{
            if (isset($profile['user_url']))
            {
    			$value=sf_filter_url_display($profile['user_url']);
            } else {
                $value = '';
            }
			$label=sf_filter_title_display($sfprofile['label']['user_url']);
			$out.='<tr>'."\n";
			$out.='<td class="sfprofilelabel">'.$label.':</td>'."\n";
			$out.='<td class="sfprofiledata"><input type="text" name="user_url" value="'.$value.'" /></td>'."\n";
			$out.='<td>'."\n";
			if($sfprofile['require']['user_url']) $out.= $require."\n";
			if($sfprofile['display']['user_url']) $out.= $display."\n";
			$out.='</td>'."\n";
			$out.='</tr>'."\n";
		}

		# location
		if($sfprofile['include']['location'])
		{
            if (isset($profile['location']))
            {
                $value=esc_attr($profile['location']);
            } else {
                $value = '';
            }
			$label=sf_filter_title_display($sfprofile['label']['location']);
			$out.='<tr>'."\n";
			$out.='<td class="sfprofilelabel">'.$label.':</td>'."\n";
			$out.='<td class="sfprofiledata"><input type="text" name="location" value="'.$value.'" /></td>'."\n";
			$out.='<td>'."\n";
			if($sfprofile['require']['location']) $out.= $require."\n";
			if($sfprofile['display']['location']) $out.= $display."\n";
			$out.='</td>'."\n";
			$out.='</tr>'."\n";
		}

		# description (biography)
		if($sfprofile['include']['description'])
		{
            if (isset($profile['description']))
            {
                $value = sf_filter_text_edit($profile['description']);
            } else {
                $value = '';
            }
			$label=sf_filter_title_display($sfprofile['label']['description']);
			$out.='<tr>'."\n";
			$out.='<td class="sfprofilelabel">'.$label.':</td>'."\n";
			$out.='<td class="sfprofiledata"><textarea name="description" cols="1" rows="4">'.$value.'</textarea></td>'."\n";
			$out.='<td>'."\n";
			if($sfprofile['require']['description']) $out.= $require."\n";
			if($sfprofile['display']['description']) $out.= $display."\n";
			$out.='</td>'."\n";
			$out.='</tr>'."\n";
		}

		$out.='</table>'."\n";
		$out.='</fieldset>'."\n";
		$out.='</div>'."\n";

		# End of 'personal' profile form
	}

	# ----------------------------------------------------------------------------------------------
	# Start Online ID form
	if($panel == 'all' || $panel == 'online')
	{
		$out.='<div id="sfprofileonline"'.$class.'>'."\n";

		# ----------------------------------------------------------------------------------------------
		# Online Identity Set
		$out.='<fieldset><legend>'.__("Your Online Identity", "sforum").'</legend>'."\n";
		$out.='<table class="sfprofiletable">'."\n";

		# aim
		if($sfprofile['include']['aim'])
		{
			$value='';
			if (isset($profile['aim'])) $value=esc_attr($profile['aim']);
			$label=sf_filter_url_display($sfprofile['label']['aim']);
			$out.='<tr>'."\n";
			$out.='<td><img src="'.SFRESOURCES.'profile/aim.png" alt="" /></td>'."\n";
			$out.='<td class="sfprofilelabel">'.$label.':</td>'."\n";
			$out.='<td class="sfprofiledata"><input type="text" name="aim" value="'.$value.'" /></td>'."\n";
			$out.='<td>'."\n";
			if($sfprofile['require']['aim']) $out.= $require."\n";
			if($sfprofile['display']['aim']) $out.= $display."\n";
			$out.='</td>'."\n";
			$out.='</tr>'."\n";
		}

		# yahoo
		if($sfprofile['include']['yahoo'])
		{
			$value='';
			if (isset($profile['yim'])) $value=esc_attr($profile['yim']);
			$label=sf_filter_url_display($sfprofile['label']['yahoo']);
			$out.='<tr>'."\n";
			$out.='<td><img src="'.SFRESOURCES.'profile/yahoo.png" alt="" /></td>'."\n";
			$out.='<td class="sfprofilelabel">'.$label.':</td>'."\n";
			$out.='<td class="sfprofiledata"><input type="text" name="yim" value="'.$value.'" /></td>'."\n";
			$out.='<td>'."\n";
			if($sfprofile['require']['yahoo']) $out.= $require."\n";
			if($sfprofile['display']['yahoo']) $out.= $display."\n";
			$out.='</td>'."\n";
			$out.='</tr>'."\n";
		}

		# jabber/google
		if($sfprofile['include']['jabber'])
		{
			$value='';
			if (isset($profile['jabber'])) $value=esc_attr($profile['jabber']);
			$label=sf_filter_url_display($sfprofile['label']['jabber']);
			$out.='<tr>'."\n";
			$out.='<td><img src="'.SFRESOURCES.'profile/jabber.png" alt="" /></td>'."\n";
			$out.='<td class="sfprofilelabel">'.$label.':</td>'."\n";
			$out.='<td class="sfprofiledata"><input type="text" name="jabber" value="'.$value.'" /></td>'."\n";
			$out.='<td>'."\n";
			if($sfprofile['require']['jabber']) $out.= $require."\n";
			if($sfprofile['display']['jabber']) $out.= $display."\n";
			$out.='</td>'."\n";
			$out.='</tr>'."\n";
		}

		# icq
		if($sfprofile['include']['icq'])
		{
			$value='';
			if (isset($profile['icq'])) $value=esc_attr($profile['icq']);
			$label=sf_filter_url_display($sfprofile['label']['icq']);
			$out.='<tr>'."\n";
			$out.='<td><img src="'.SFRESOURCES.'profile/icq.png" alt="" /></td>'."\n";
			$out.='<td class="sfprofilelabel">'.$label.':</td>'."\n";
			$out.='<td class="sfprofiledata"><input type="text" name="icq" value="'.$value.'" /></td>'."\n";
			$out.='<td>'."\n";
			if($sfprofile['require']['icq']) $out.= $require."\n";
			if($sfprofile['display']['icq']) $out.= $display."\n";
			$out.='</td>'."\n";
			$out.='</tr>'."\n";
		}

		# msn
		if($sfprofile['include']['msn'])
		{
			$value='';
			if (isset($profile['msn'])) $value=esc_attr($profile['msn']);
			$label=sf_filter_url_display($sfprofile['label']['msn']);
			$out.='<tr>'."\n";
			$out.='<td><img src="'.SFRESOURCES.'profile/msn.png" alt="" /></td>'."\n";
			$out.='<td class="sfprofilelabel">'.$label.':</td>'."\n";
			$out.='<td class="sfprofiledata"><input type="text" name="msn" value="'.$value.'" /></td>'."\n";
			$out.='<td>'."\n";
			if($sfprofile['require']['msn']) $out.= $require."\n";
			if($sfprofile['display']['msn']) $out.= $display."\n";
			$out.='</td>'."\n";
			$out.='</tr>'."\n";
		}

		# skype
		if($sfprofile['include']['skype'])
		{
			$value='';
			if (isset($profile['skype'])) $value=esc_attr($profile['skype']);
			$label=sf_filter_url_display($sfprofile['label']['skype']);
			$out.='<tr>'."\n";
			$out.='<td><img src="'.SFRESOURCES.'profile/skype.png" alt="" /></td>'."\n";
			$out.='<td class="sfprofilelabel">'.$label.':</td>'."\n";
			$out.='<td class="sfprofiledata"><input type="text" name="skype" value="'.$value.'" /></td>'."\n";
			$out.='<td>'."\n";
			if($sfprofile['require']['skype']) $out.= $require."\n";
			if($sfprofile['display']['skype']) $out.= $display."\n";
			$out.='</td>'."\n";
			$out.='</tr>'."\n";
		}

		# facebook
		if($sfprofile['include']['facebook'])
		{
			$value='';
			if (isset($profile['facebook'])) $value=esc_attr($profile['facebook']);
			$label=sf_filter_url_display($sfprofile['label']['facebook']);
			$out.='<tr>'."\n";
			$out.='<td><img src="'.SFRESOURCES.'profile/facebook.png" alt="" /></td>'."\n";
			$out.='<td class="sfprofilelabel">'.$label.':</td>'."\n";
			$out.='<td class="sfprofiledata"><input type="text" name="facebook" value="'.$value.'" /></td>'."\n";
			$out.='<td>'."\n";
			if($sfprofile['require']['facebook']) $out.= $require."\n";
			if($sfprofile['display']['facebook']) $out.= $display."\n";
			$out.='</td>'."\n";
			$out.='</tr>'."\n";
		}

		# myspace
		if($sfprofile['include']['myspace'])
		{
			$value='';
			if (isset($profile['myspace'])) $value=esc_attr($profile['myspace']);
			$label=sf_filter_url_display($sfprofile['label']['myspace']);
			$out.='<tr>'."\n";
			$out.='<td><img src="'.SFRESOURCES.'profile/myspace.png" alt="" /></td>'."\n";
			$out.='<td class="sfprofilelabel">'.$label.':</td>'."\n";
			$out.='<td class="sfprofiledata"><input type="text" name="myspace" value="'.$value.'" /></td>'."\n";
			$out.='<td>'."\n";
			if($sfprofile['require']['myspace']) $out.= $require."\n";
			if($sfprofile['display']['myspace']) $out.= $display."\n";
			$out.='</td>'."\n";
			$out.='</tr>'."\n";
		}

		# twitter
		if($sfprofile['include']['twitter'])
		{
			$value='';
			if (isset($profile['twitter'])) $value=esc_attr($profile['twitter']);
			$label=sf_filter_url_display($sfprofile['label']['twitter']);
			$out.='<tr>'."\n";
			$out.='<td><img src="'.SFRESOURCES.'profile/twitter.png" alt="" /></td>'."\n";
			$out.='<td class="sfprofilelabel">'.$label.':</td>'."\n";
			$out.='<td class="sfprofiledata"><input type="text" name="twitter" value="'.$value.'" /></td>'."\n";
			$out.='<td>'."\n";
			if($sfprofile['require']['twitter']) $out.= $require."\n";
			if($sfprofile['display']['twitter']) $out.= $display."\n";
			$out.='</td>'."\n";
			$out.='</tr>'."\n";
		}

		# linkedin
		if($sfprofile['include']['linkedin'])
		{
			$value='';
			if (isset($profile['linkedin'])) $value=esc_attr($profile['linkedin']);
			$label=sf_filter_url_display($sfprofile['label']['linkedin']);
			$out.='<tr>'."\n";
			$out.='<td><img src="'.SFRESOURCES.'profile/linkedin.png" alt="" /></td>'."\n";
			$out.='<td class="sfprofilelabel">'.$label.':</td>'."\n";
			$out.='<td class="sfprofiledata"><input type="text" name="linkedin" value="'.$value.'" /></td>'."\n";
			$out.='<td>'."\n";
			if($sfprofile['require']['linkedin']) $out.= $require."\n";
			if($sfprofile['display']['linkedin']) $out.= $display."\n";
			$out.='</td>'."\n";
			$out.='</tr>'."\n";
		}

		$out.='</table>'."\n";
		$out.='</fieldset>'."\n";
		$out.='</div>'."\n";

		# End of 'online' profile form
	}
	# ----------------------------------------------------------------------------------------------
	# Start Additional Info form
	if($panel == 'all' || $panel == 'custom')
	{
		$out.='<div id="sfprofileadditional"'.$class.'>'."\n";

		# ----------------------------------------------------------------------------------------------
		# Additional Set
		$out.='<fieldset><legend>'.__("Additional Information", "sforum").'</legend>'."\n";

		if($cfields)
		{
			$out.='<table class="sfprofiletable">'."\n";
			foreach ($cfields as $x => $cfield)
			{
				$item=$cfield['meta_key'];
				if($sfprofile['include'][$item])
				{
					$fielddata = unserialize($cfield['meta_value']);
					if($fielddata['type'] == 'textarea')
					{
						$value=esc_html($profile[$item]);
					} else {
						$value=esc_attr($profile[$item]);
					}
					$label=sf_filter_title_display($sfprofile['label'][$item]);

					$out.='<tr>'."\n";
					$out.='<td class="sfprofilelabel">'.$label.':</td>'."\n";
					$out.='<td class="sfprofiledata">'."\n";

					switch($fielddata['type'])
					{

						case 'checkbox':
							$out.='<label for="'.esc_attr($item).'">&nbsp;</label>'."\n";
							$out.='<input type="checkbox" name="'.esc_attr($item).'" id="'.esc_attr($item).'" '."\n";
							if($value == true)
							{
								$out.='checked="checked" '."\n";
							}
							$out.='/></td>'."\n";
							break;

						case 'input':
							$out.='<input type="text" name="'.esc_attr($item).'" value="'.esc_attr($value).'" /></td>'."\n";
							break;

						case 'textarea':
							$out.='<textarea name="'.esc_attr($item).'" cols="1" rows="4">'.esc_attr($value).'</textarea></td>'."\n";
							break;

						case 'select':
							$out.='<select class="sfcontrol" name="'.esc_attr($item).'" id="'.esc_attr($item).'" >'."\n";
							if (!$value) $out.='<option value="" selected="selected"></option>'."\n";
							$list=explode(',', $fielddata['selectvalues']);
							if($list)
							{
								foreach ($list as $option)
								{
									$selected='';
									if($value == trim($option)) $selected=" selected='selected'";
									$out.='<option value="'.trim(esc_attr($option)).'"'.$selected.'><b>'.trim($option).'</b></option>'."\n";
								}
							}
							$out.='</select></td>'."\n";
							break;

						case 'radio':
							$list=explode(',', $fielddata['selectvalues']);
							if($list)
							{
								foreach($list as $option)
								{
									$check='';
									if($value == trim($option)) $check = ' checked="checked"';
									$out.='<input type="radio" name="'.esc_attr($item).'" id="'.esc_attr($item).'" value="'.trim(esc_attr($option)).'"'.$check.' />'."\n"."\n";
									$out.='<label for="'.esc_attr($item).'" class="sflabel radio"><b>'.trim($option).'</b></label>'."\n"."\n";
									$out.= '<br />'."\n";
								}
							}
							break;

					}
					$out.='<td>'."\n";
					if($sfprofile['require'][$item]) $out.= $require."\n";
					if($sfprofile['display'][$item]) $out.= $display."\n";
					$out.='</td>'."\n";
					$out.='</tr>'."\n";
				}
			}
			$out.='</table>'."\n";
		}
		$out.='</fieldset>'."\n";
		$out.='</div>'."\n";

		# End of 'additional' (custom) profile form
	}
	# ----------------------------------------------------------------------------------------------
	# Start Options form
	if($panel == 'all' || $panel == 'options')
	{
		$out.='<div id="sfprofileoptions"'.$class.'>'."\n";

		# ----------------------------------------------------------------------------------------------
		# Options Set
		$out.='<fieldset><legend>'.__("Personal Options", "sforum").'</legend>'."\n";
		$out.='<table class="sfprofiletable">'."\n";

		# Sync display name
		if($sfprofile['nameformat'] == 1)
		{
			$out.='<tr>'."\n";
			$checked = '';
			if ($profile['user_options']['namesync'])
			{
				$checked = 'checked="checked" ';
			}
			$out.='<td class="sfprofilelabel">'.__("Keep Forum and WP Display Name In Sync", "sforum").':</td>'."\n";
			$out.='<td class="sfprofiledata"><label for="sf-sync">&nbsp;&nbsp;&nbsp;</label><input type="checkbox" '.$checked.'name="sf-sync" id="sf-sync" /></td>'."\n";
			$out.='</tr>'."\n";
		}

		# Auto Subscribe
		if(sf_user_can($userid, 'Can subscribe'))
		{
			$out.='<tr>'."\n";
			$checked = '';
			if ($profile['user_options']['autosubpost'])
			{
				$checked = 'checked="checked" ';
			}
			$out.='<td class="sfprofilelabel">'.__("Auto subscribe to topics I post in", "sforum").':</td>'."\n";
			$out.='<td class="sfprofiledata"><label for="sf-subpost">&nbsp;&nbsp;&nbsp;</label><input type="checkbox" '.$checked.'name="sf-subpost" id="sf-subpost" /></td>'."\n";
			$out.='</tr>'."\n";
		}

		# Receive emails on receipt of PM (if pm enabled and pm email enabled)
		$sfpm = sf_get_option('sfprivatemessaging');
		$sfpmopts = sf_get_option('sfpm');
		if ($sfpm && $sfpmopts['sfpmemail'])
		{
			$out.='<tr>'."\n";
			$checked = '';
			if ($profile['user_options']['pmemail'])
			{
				$checked = 'checked="checked" ';
			}
			$out.='<td class="sfprofilelabel">'.__("Receive an Email when someone sends you a Private Message", "sforum").':</td>'."\n";
			$out.='<td class="sfprofiledata"><label for="sf-pmemail">&nbsp;&nbsp;&nbsp;</label><input type="checkbox" '.$checked.'name="sf-pmemail" id="sf-pmemail" /></td>'."\n";
			$out.='</tr>'."\n";
		}

    	# Hide online status
    	$sfmemberopts = sf_get_option('sfmemberopts');
        if ($sfmemberopts['sfhidestatus'])
        {
    		$out.='<tr>'."\n";
    		$checked = '';
    		if ($profile['user_options']['hidestatus'])
    		{
    			$checked = 'checked="checked" ';
    		}
    		$out.='<td class="sfprofilelabel">'.__("Hide your online status", "sforum").':<br />('.__("except from admins", "sforum").')</td>'."\n";
    		$out.='<td class="sfprofiledata"><label for="sf-hidestatus">&nbsp;&nbsp;&nbsp;</label><input type="checkbox" '.$checked.'name="sf-hidestatus" id="sf-hidestatus" /></td>'."\n";
    		$out.='</tr>'."\n";
        }

		# Editor
		$sfeditor = sf_get_option('sfeditor');
		if($sfeditor['sfusereditor'])
		{
			$out.='<tr>'."\n";
			$out.='<td class="sfprofilelabel">'.__("Select Preferred Editor", "sforum").':</td>'."\n";
			$out.='<td class="sfprofiledata">'."\n";
			if($profile['user_options']['editor'] == 1 ? $checked='checked="checked"' : $checked='');
			$out.= '<input type="radio" id="sfradio-1" name="editor" value="1" '.$checked.'  />'."\n";
			$out.= '<label class="sfradio" for="sfradio-1">&nbsp;&nbsp;<b>'.__("Rich Text (TinyMCE)", "sforum").'</b></label><br />'."\n";

			if($profile['user_options']['editor'] == 2 ? $checked='checked="checked"' : $checked='');
			$out.= '<input type="radio" id="sfradio-2" name="editor" value="2" '.$checked.'  />'."\n";
			$out.= '<label class="sfradio" for="sfradio-2">&nbsp;&nbsp;<b>'.__("HTML (Quicktags)", "sforum").'</b></label><br />'."\n";

			if($profile['user_options']['editor'] == 3 ? $checked='checked="checked"' : $checked='');
			$out.= '<input type="radio" id="sfradio-3" name="editor" value="3" '.$checked.'  />'."\n";
			$out.= '<label class="sfradio" for="sfradio-3">&nbsp;&nbsp;<b>'.__("BBCode (Quicktags)", "sforum").'</b></label><br />'."\n";

			if($profile['user_options']['editor'] == 4 ? $checked='checked="checked"' : $checked='');
			$out.= '<input type="radio" id="sfradio-4" name="editor" value="4" '.$checked.'  />'."\n";
			$out.= '<label class="sfradio" for="sfradio-4">&nbsp;&nbsp;<b>'.__("Plain Textarea", "sforum").'</b></label><br />'."\n";

			$out.= '</td>'."\n";
			$out.='</tr>'."\n";
		}

		#timezone
		$value = $profile['user_options']['timezone'];
		if(!isset($value) || empty($value)) $value=0;

		$out.= '<tr>'."\n";
		$out.= '<td class="sfprofilelabel" rowspan="2">'.__("Set your Timezone as +/- hours from Server", "sforum").':</td>'."\n";

		$tz = get_option('timezone_string');
		if(empty($tz)) $tz = 'UTC '.get_option('gmt_offset');
		$out.= '<td>'.__("Server Timezone set to", "sforum").': <b>'.$tz.'</b>';
		$out.= '<p><small>'.sprintf(__("For zones in front of server use positive number (i.e., 4) %s For zones behind server use a minus sign (i.e., -4)", "sforum"), '<br />').'</small></p>'."\n";
		$out.= '</td>';
		$out.= '</tr><tr>';

		$out.='<td class="sfprofiledata"><input type="text" name="sf-timezone" value="'.$value.'" /></td>'."\n";

		$out.='</tr>'."\n";

		$out.= '<tr><td colspan="2"><p>';
		$out.= '<a href="http://en.wikipedia.org/wiki/Time_zone">'.__('Help and explanation of timezones', 'sforum').'</a>';
		$out.= '</p></td></tr>';

		$out.='</table>'."\n";
		$out.='</fieldset>'."\n";
		$out.='</div>'."\n";

		# End of 'options' profile form
	}
	# ----------------------------------------------------------------------------------------------
	# Start Change password
	if($panel == 'all' || $panel == 'password')
	{
		$out.='<div id="sfprofilepassword"'.$class.'>'."\n";

		# ----------------------------------------------------------------------------------------------
		# Password Set
		$out.='<fieldset><legend>'.__("Change Password", "sforum").'</legend>'."\n";
		$out.='<table class="sfprofiletable">'."\n";

		$out.='<tr>'."\n";
		$out.='<td class="sfprofilelabel">'.__("New Password", "sforum").':</td>'."\n";
		$out.='<td class="sfprofiledata"><input type="password" name="newone1" id="newone1" value="" autocomplete="off" /></td>'."\n";
		$out.='</tr><tr>'."\n";
		$out.='<td class="sfprofilelabel">'.__("Repeat New Password", "sforum").':</td>'."\n";
		$out.='<td class="sfprofiledata"><input type="password" name="newone2" id="newone2" value="" autocomplete="off" /></td>'."\n";
		$out.='</tr>'."\n";

		$out.='</table>'."\n";
		$out.='</fieldset>'."\n";
		$out.='</div>'."\n";

		# End of 'change password' profile form
	}
	# ----------------------------------------------------------------------------------------------
	# Start Avatar
	if($panel == 'all' || $panel == 'avatar')
	{
		$out.='<div id="sfprofileavatar"'.$class.'>'."\n";

		# ----------------------------------------------------------------------------------------------
		# Avatar Set
		$out.='<fieldset><legend>'.__("Select Your Avatar", "sforum").'</legend>'."\n";

		$out.='<fieldset><legend>'.__("Current Avatar in Use", "sforum").'</legend>'."\n";
		$out.='<table rules="cols" cellspacing="10" cellpadding="10"><tr valign="top">';
		$list = array(
			0 => __("From Gravatar.com", "sforum"),
			1 => __("WordPress Avatar Setting", "sforum"),
			2 => __("Uploaded Avatar", "sforum"),
			3 => __("Forum Default Avatars", "sforum"),
			4 => __("Forum Avatar Pool", "sforum"),
			5 => __("Remote Avatar", "sforum")
		);
		$out.='<td><p>'.__("This forum searches and selects a member avatar in the following sequence until one is found", "sforum").':</p>'."\n";
		$out.='<ol>'."\n";
		foreach ($sfavatars['sfavatarpriority'] as $key => $priority)
		{
			$out.='<li>'.$list[$priority].'</li>'."\n";
            if ($priority == 3) break; # done with priorities if we reach uploaded avatars since others are inactive then
		}
		$out.='</ol>'."\n";
		$out.= '</td>';

		# Avatar Used by forum
		$out.='<td align="center"><p style="text-align:center">'.__("Current Avatar Used by Forum", "sforum").':<br /><br /></p>'.sf_render_avatar('user', $userid, sf_filter_email_display($profile['user_email']), '', false, 0, false).'</td>'."\n";
		$out.='</tr></table>';
		$out.='</fieldset>'."\n";
		$out.='<br />';
		if ($sfavatars['sfavataruploads'] && sf_user_can($userid, 'Can upload avatars'))
		{
			$maxsize = $sfavatars['sfavatarsize'];
			$out.='<fieldset><legend>'.__("Upload An Avatar", "sforum").'</legend>'."\n";
			$out.='<table class="sfprofiletable">'."\n";
			$out.='<tr>';
			$out.='<td class="sfprofilelabel">';
			$out.=__("Select Avatar Image", "sforum").':</td>'."\n";
			$out.='<td id="sfavatarupload" class="sfprofiledata">';
			if (is_writable(SF_STORE_DIR."/".$SFPATHS['avatars']."/"))
			{
				$out.= '<div id="sf-upload-button" class="sfcontrol sf-upload-button">'.__('Browse', 'sforum').'</div>';
				$out.= '<div id="sf-upload-status"></div>';
				$out.= '<input type="hidden" name="uploadfile" id="uploadfile" value="" />'."\n";
			} else {
				$out.= '<div id="sf-upload-status">';
				$out.= '<p class="sf-upload-status-fail">'.__("Sorry, uploads disabled! Storage location does not exist or is not writable. Please contact a forum Admin.", "sforum").'</p>';
				$out.= '</div>';
			}
			$out.='</td>'."\n";

			if($profile['avatar']['uploaded'])
			{
                $ahahURL = SFHOMEURL."index.php?sf_ahah=profile&id=".$userid."&avatarremove=1&file=".$profile['avatar']['uploaded'];
				$target = "sfuploadedavatar";
				$spinner = SFADMINIMAGES.'working.gif';
				$out.='<td align="center"><p style="text-align:center">'.__("Current Uploaded Avatar", "sforum").':<br /><br /></p>';
				$out.='<div id="sfuploadedavatar" style="text-align:center"><img src="'.esc_url(SFAVATARURL.$profile['avatar']['uploaded']).'" alt="" /></div><br />';
				$out.='<input type="button" class="sfxcontrol" id="delavatar" value="'.esc_attr(__("Remove", "sforum")).'" onclick="sfjRemoveAvatar(\''.$ahahURL.'\', \''.$target.'\', \''.$spinner.'\');" /></td>';
			}
			$out.='</tr><tr>'."\n";
			$out.='<td colspan="3" align="center"><br />';
			$out.=__("Files accepted: GIF, PNG, JPG and JPEG", "sforum").'<br />';
			$out.=__("Maximum width displayed", "sforum").': <b>'.$maxsize.'</b> '.__("pixels", "sforum").'<br />';
			$out.= __("Maximum filesize", "sforum").': <b>'.$sfavatars['sfavatarfilesize'].'</b> '.__("bytes", "sforum");
			$out.='</td>'."\n";
			$out.='</tr>'."\n";
			$out.='</table>'."\n";
			$out.='</fieldset>'."\n";
			$out.='<br />';
		}

		if ($sfavatars['sfavatarpool'])
		{
			$out.='<fieldset><legend>'.__("Select Avatar from Pool", "sforum").'</legend>'."\n";
			$out.='<table class="sfprofiletable"><tr>';
			$out.='<td class="sfprofilelabel">';
			$out.=__("Select Pool Avatar", "sforum").':';
			$out.='</td>'."\n";
			$out.='<td id="sfavatarpool" class="sfprofiledata">';
            $site = SFHOMEURL."index.php?sf_ahah=profile&action=avatarpool";
			$out.= '<div id="sf-pool-button" class="sfcontrol sf-upload-button">';
			$out.= '<a class="sfcontrol" style="border:none !important;font-size:100% !important;padding:1px 10px;" href="'.$site.'" onclick="return hs.htmlExpand(this, { objectType: \'ajax\', preserveContent: true, width: 600, height: 400} )">'.__('Browse', 'sforum').'</a>';
			$out.='</div>';
			$out.= '<div id="sf-pool-status"></div>';
			$out.= '<input type="hidden" name="sfpoolavatar" id="sfpoolavatar" value="" />'."\n";
			$out.='</td>'."\n";
			if($profile['avatar']['pool'])
			{
                $ahahURL = SFHOMEURL."index.php?sf_ahah=profile&id=".$userid."&poolremove=1&file=".$profile['avatar']['pool'];
				$target = "sfpoolavatar";
				$spinner = SFADMINIMAGES.'working.gif';
				$out.='<td align="center"><p style="text-align:center">'.__("Current Pool Avatar", "sforum").':<br /><br /></p>';
				$out.='<div id="sfpoolavatar" style="text-align:center"><img src="'.esc_url(SFAVATARPOOLURL.$profile['avatar']['pool']).'" alt="" /></div><br />';
				$out.='<input type="button" class="sfxcontrol" id="delpool" value="'.esc_attr(__("Remove", "sforum")).'" onclick="sfjRemovePool(\''.$ahahURL.'\', \''.$target.'\', \''.$spinner.'\');" /></td>';
			}
			$out.='</tr></table>'."\n";
			$out.='</fieldset>'."\n";
			$out.='<br />';
		}

		if ($sfavatars['sfavatarremote'])
		{
			$out.='<fieldset><legend>'.__("Select Remote Avatar", "sforum").'</legend>'."\n";
			$out.='<table class="sfprofiletable"><tr>';
			$out.='<td class="sfprofilelabel">';
			$out.=__("Select Remote Avatar", "sforum").':';
			$out.='</td>'."\n";
			$out.='<td class="sfprofiledata">';
			if($profile['avatar']['remote'])
			{
				$out.='<p style="text-align:center">'.__("Current Remote Avatar", "sforum").':<br /><br /></p>';
				$out.='<div style="text-align:center"><img src="'.$profile['avatar']['remote'].'" alt="" /></div>';
			}
			$out.= '<p><br /><input type="text" name="sfremoteavatar" id="sfremoteavatar" value="'.$profile['avatar']['remote'].'" /></p>';
			$out.='</td>'."\n";
			$out.='</tr></table>'."\n";
			$out.='</fieldset>'."\n";
			$out.='<br />';
		}

		$out.='<div class="inline_edit" id="sfpostupload"><p class="sfsuccessentry">'.__("New Avatar will be Saved upon Updating the Profile", "sforum").'</p></div>';
		$out.='</fieldset>'."\n";
		$out.='</div>'."\n";

		# End of 'avatar' profile form
	}
	# ----------------------------------------------------------------------------------------------
	# Start Signature
	if($panel == 'all' || $panel == 'signature')
	{
		include_once (SF_PLUGIN_DIR.'/forum/sf-topic-components.php');
		$out.='<div id="sfprofilesignature"'.$class.'>'."\n";

		# ----------------------------------------------------------------------------------------------
		# Signature Set
		$out.='<fieldset><legend>'.__("Setup Your Signature", "sforum").'</legend>'."\n";
		$out.='<table>'."\n";
		$out.='<tr>'."\n";
		$value='';
		$value=sf_filter_content_edit($profile['signature']);

		$out.= '<td>'.sf_setup_sig_editor($value).'</td>'."\n";

		$out.='</tr>'."\n";
		$sfsigimagesize = sf_get_option('sfsigimagesize');
		$sigwidth = __('width - none specified', 'sforum').', ';
		$sigheight = __('height - none specified', 'sforum');
		if ($sfsigimagesize['sfsigwidth'] > 0) $sigwidth = __('width - ', 'sforum').$sfsigimagesize['sfsigwidth'].', ';
		if ($sfsigimagesize['sfsigheight'] > 0) $sigheight = __('height - ', 'sforum').$sfsigimagesize['sfsigheight'];
		$out.='<tr>'."\n";
		$out.='<td colspan="2">'.__("Signature Image Size Limits (pixels): ", "sforum").' '.$sigwidth.$sigheight.'</td>'."\n";
		$out.='</tr>'."\n";
		$out.='<tr>'."\n";
		$out.='<td colspan="2">';
        $out.= sf_render_signature_strip(sf_filter_content_display($profile['signature']));
        $out.='</td>'."\n";
		$out.='</tr>'."\n";
		$out.='</table>'."\n";
		$out.='</fieldset>'."\n";
		$out.='</div>'."\n";

		# End of 'signature' profile form
	}
	# ----------------------------------------------------------------------------------------------
	# Start Photos
	if($panel == 'all' || $panel == 'photos')
	{
		$out.='<div id="sfprofilephotos"'.$class.'>'."\n";

		# ----------------------------------------------------------------------------------------------
		# Photos Set
		$out.='<fieldset><legend>'.__("Setup Your Photos", "sforum").'</legend>'."\n";
		$out.='<p>'.$sfprofile['photosmax'].' '.__("personal photos or images can be displayed in your profile", "sforum").':</p>'."\n";
		$out.='<p>'.__("Please keep images small. They will be displayed at a maximum width of", "sforum").' '.$sfprofile['photoswidth'].' '.__("pixels", "sforum").':</p>'."\n";

		if ($sfprofile['photosmax'] > 0)
		{
			$out.='<table class="sfprofiletable">'."\n";
			for($x=0; $x < $sfprofile['photosmax']; $x++)
			{
				$out.='<tr>'."\n";
				$out.='<td class="sfprofilelabel">'.__("Url to Photo", "sforum").' '.($x+1).':</td>'."\n";
				$out.='<td class="sfprofiledata"><input type="text" name="photo'.$x.'" value="'.$profile['photos'][$x].'" /></td>'."\n";
				$out.='<td>'."\n";
				$out.= $display."\n";
				$out.='</td>'."\n";
				$out.='</tr>'."\n";
			}
			$out.='</table>'."\n";
		}
		$out.='</fieldset>'."\n";
		$out.='</div>'."\n";

		# End of 'photos' profile form
	}
	# ----------------------------------------------------------------------------------------------

	# Update/problem message area
	if(!isset($profile['user_email']) || empty($profile['user_email']))
	{
		$out.='<div id="sfprofilemsg">';
		$out.= '<p class="sferrorentry">';
		$out.= '<span class="sferrorbold">'.__("Required Data", "sforum").':</span><b> '.__("Email Address", "sforum").'</b>';
		$out.='</p></div>'."\n";
		# inline email alert
		add_action( 'wp_footer', 'sfjs_profile_email_alert' );
	} else {
		$out.='<div id="sfprofilemsg"></div>'."\n";
	}

	$out.='<input style="float:left" type="submit" class="sfcontrol" name="subprofile" id="subprofile" value="'.__("Update Profile", "sforum").'" />'."\n";
    $rssopt = sf_get_option('sfrss');
    if ($rssopt['sfrssfeedkey'])
    {
        $out.='<span style="float:right;padding-top:8px">'.__("Your Feedkey is", "sforum").' '.$sfglobals['member']['feedkey'].'</span>';
    }
	$out.='</form>'."\n";

	# post-profile form hook
	$out.= sf_process_hook('sf_hook_post_profile_form', array($userid));

	$out.= '<div style="clear:both; margin: 8px 0;"></div>';
	if($panel == 'all')
	{
		if($userid == $current_user->ID)
		{
			$out.= '<br /><hr /><table border="0" width="auto"><tr><td>';
			$out.= '<table><tr>';
			if ($current_user->sfusepm)
			{
    			$out.= '<td><form action="'.SFURL.'profile/buddies/" method="post" name="buddies">';
				$out.= '<input type="submit" class="sfcontrol" name="manbuddy" value="'.sf_split_button_label(esc_attr(__("Manage PM Buddy List", "sforum")), 1).'" />'."\n";
    			$out.= '</form></td>';
			}

        	$sfmemberopts = sf_get_option('sfmemberopts');
            if ($sfmemberopts['sfviewperm'] || $current_user->forumadmin)
            {
       			$out.= '<td><form action="'.SFURL.'profile/permissions/" method="post" name="permissions">';
    			$out.= '<input type="submit" class="sfcontrol" name="viewperms" value="'.sf_split_button_label(esc_attr(__("View Forum Permissions", "sforum")), 1).'" />'."\n";
       			$out.= '</form></td>';
            }

			$out.= '</tr></table>';
			$out.= '</td>';

			$out.= '<td width="30%"></td>';

			$out.= '<td>';
			$out.= '<form action="'.SFHOMEURL.'index.php?sf_ahah=search" method="post" name="search">'."\n";
			$out.= '<input type="hidden" class="sfhiddeninput" name="userid" id="userid" value="'.$current_user->ID.'" />';
			$out.= '<input type="hidden" class="sfhiddeninput" name="searchoption" id="searchoption" value="All Forums" />';
			$out.= '<table><tr>';
			$out.= '<td><input type="submit" class="sfcontrol" name="membersearch" id="membersearch" value="'.sf_split_button_label(esc_attr(__("View Topics Member Has Posted To", "sforum")), 2).'" /></td>'."\n";
			$out.= '<td><input type="submit" class="sfcontrol" name="memberstarted" id="memberstarted" value="'.sf_split_button_label(esc_attr(__("View Topics Member Started", "sforum")), 1).'" /></td>'."\n";
			$out.= '</tr></table>';
			$out.= '</form></td>'."\n";
			$out.= '</tr></table>';
    	} else {
    		if ($current_user->sfusepm)
    		{
                $out.= '<br /><table><tr>';
        		$out.= '<td><form action="'.SFURL.'private-messaging/send/'.urlencode($profile['display_name']).'/" method="post" name="buddies">';
    			$out.= '<input type="submit" class="sfcontrol" name="sendpm" value="'.sf_split_button_label(esc_attr(__("Send PM", "sforum")), 1).'" />'."\n";
        		$out.= '</form></td>';
    			$out.= '</tr></table>';
    		}
    	}

		# ----------------------------------------------------------------------------------------------
		# Close sfprofileform outer form container
		$out.= '</div>'."\n";
	}

	# ----------------------------------------------------------------------------------------------
	# post-profile form buttons hook
	$out.= sf_process_hook('sf_hook_post_profile_form_buttons', array($userid));
	# ----------------------------------------------------------------------------------------------

 	# Close sforum block
    if ($sfprofile['forminforum'] == false)
    {
	   $out.= '</div>'."\n";
	   $out.= "\n\n".'<!-- End of SPF Container (Profile) -->'."\n\n";
    }

	return $out;
}


function sf_setup_sig_editor($content='')
{
	global $sfglobals;

	$out = '';
	if($sfglobals['editor']['sfeditor'] == RICHTEXT || $sfglobals['editor']['sfeditor'] == PLAIN)
	{
		# rich text/tinymce - or - plain textarea
		$out.='<textarea class="sftextarea" name="signature" id="signature" cols="60" rows="12">'.$content.'</textarea>'."\n";
		return $out;
	}
	if($sfglobals['editor']['sfeditor'] == HTML)
	{
		# html quicktags
		$image = "html/htmlEditor.gif";
		$alttext = __("HTML Editor", "sforum");
	} else {
		# bbcode quicktags
		$image = "bbcode/bbcodeEditor.gif";
		$alttext = __("bbCode Editor", "sforum");
	}
	$out.='<div class="quicktags">'."\n";
	$out.='<img class="sfalignright" src="'.SFEDSTYLE.$image.'" alt="'.$alttext.'" />';
	$out.='<script type="text/javascript">edToolbar();</script><textarea class="sftextarea" name="signature" id="signature" rows="12" cols="60">'.$content.'</textarea><script type="text/javascript">var edCanvas = document.getElementById("signature");</script>'."\n";
	$out.='</div>'."\n";
	return $out;
}

# inline js - setup form and uploader ajax
function sfjs_profile_ajax() {
	global $SFPATHS, $sfglobals;

	$sfavatars = sf_get_option('sfavatars');
?>
<script type= "text/javascript">/*<![CDATA[*/
jQuery(document).ready(function(){

	jQuery('#profilepersonal').ajaxForm({
		target: '#sfprofilemsg',
<?php if($sfglobals['editor']['sfeditor'] == RICHTEXT) { ?>
		beforeSubmit: function(a) {
			for (var i=0; i<a.length; i++)
				if (a[i].name == 'signature')
					a[i].value = tinyMCE.get('signature').getContent();
	   },
<?php } ?>
		success: function() {
			jQuery('#sfprofilemsg').show();
			jQuery('#sfprofilemsg').fadeOut(12000);
		}
	});

	var button = jQuery('#sf-upload-button'), interval;
	new AjaxUpload(button,{
		action: '<?php echo SFUPLOADER; ?>',
		name: 'uploadfile',
	    data: {
		    saveloc : '<?php echo addslashes(SF_STORE_DIR."/".$SFPATHS['avatars']."/"); ?>',
		    size : '<?php echo $sfavatars['sfavatarfilesize']; ?>'
	    },
		onSubmit : function(file, ext){
			/* check for valid extension */
			if(sfjTestExt(ext) == false){
				jQuery('#sf-upload-status').html('<p class="sf-upload-status-fail"><?php echo esc_js(__("Only JPG, JPEG, PNG, or GIF files are allowed!", "sforum")); ?></p>');
				return false;
			}
			/* change button text, when user selects file */
			utext = '<?php echo esc_js(__("Uploading", "sforum")); ?>';
			button.text(utext);
			/* If you want to allow uploading only 1 file at time, you can disable upload button */
			this.disable();
			/* Uploding -> Uploading. -> Uploading... */
			interval = window.setInterval(function(){
				var text = button.text();
				if (text.length < 13){
					button.text(text + '.');
				} else {
					button.text(utext);
				}
			}, 200);
		},
		onComplete: function(file, response){
			jQuery('#sf-upload-status').html('');
			button.text('<?php echo esc_js(__("Browse", "sforum")); ?>');
			window.clearInterval(interval);
			/* re-enable upload button */
			this.enable();
			/* add file to the list */
			if (response==="success"){
				jQuery('<div></div>').appendTo('#sfavatarupload').html('<input type="hidden" name="uploadfile" id="uploadfile" value="' + file + '" />');
				jQuery('#sf-upload-status').html('<p class="sf-upload-status-success"><?php echo esc_js(__("Avatar uploaded! Please Update Profile to save!", "sforum")); ?></p>');
			} else if (response==="extension"){
				jQuery('#sf-upload-status').html('<p class="sf-upload-status-fail"><?php echo esc_js(__("Sorry, only JPG, JPEG, PNG, or GIF files are allowed!", "sforum")); ?></p>');
			} else if (response==="invalid"){
				jQuery('#sf-upload-status').html('<p class="sf-upload-status-fail"><?php echo esc_js(__("Sorry, the file has an invalid format!", "sforum")); ?></p>');
			} else if (response==="match"){
				jQuery('#sf-upload-status').html('<p class="sf-upload-status-fail"><?php echo esc_js(__("Sorry, the file mime type does not match file extension!", "sforum")); ?></p>');
			} else if (response==="exists"){
				jQuery('#sf-upload-status').html('<p class="sf-upload-status-fail"><?php echo esc_js(__("Sorry, the file already exists!", "sforum")); ?></p>');
			} else if (response==="size"){
				jQuery('#sf-upload-status').html('<p class="sf-upload-status-fail"><?php echo esc_js(__("Sorry, the file is too large!", "sforum")); ?></p>');
			} else {
				jQuery('#sf-upload-status').html('<p class="sf-upload-status-fail"><?php echo esc_js(__("Error uploading file!", "sforum")); ?></p>');
			}
		}
	});
});/*]]>*/</script><?php
}

# inline js - email alert
function sfjs_profile_email_alert() {
?>
<script type="text/javascript">
	jQuery("#sfprofilemsg").show();
</script>
<?php
}

?>