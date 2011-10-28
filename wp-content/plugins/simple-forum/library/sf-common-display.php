<?php
/*
Simple:Press
sf-common-display.php - common/shared rendering/display routines between back and front ends
$LastChangedDate: 2011-04-28 20:08:44 -0700 (Thu, 28 Apr 2011) $
$Rev: 5994 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# = RENDER GROUP/FORUM SELECT LIST ========
if(!function_exists('sf_render_group_forum_select')):
function sf_render_group_forum_select($goURL=false, $valueURL=false, $showSelects=true, $showFirstRow=true)
{
	global $session_groups;

	$indent = '&nbsp;&nbsp;';

	if(isset($session_groups))
	{
		$groups = $session_groups;
	} else {
		$groups = sf_get_combined_groups_and_forums();
	}

	if($groups[0]['group_id'] == "Access Denied") return;

	if($groups)
	{
		if($showSelects)
		{
			$out = '<select class="sfquicklinks sfcontrol" name="sfquicklinks" ';
    		if($goURL)
    		{
    			$out.= 'onchange="javascript:sfjchangeURL(this)"';
    		}
    		$out.= '>'."\n";
		}
		if($showFirstRow)
		{
			$out.= '<option>'.__("Select Forum", "sforum").'</option>'."\n";
		}
		foreach($groups as $group)
		{
			$out.= '<optgroup class="sflist" label="'.$indent.sf_create_name_extract(sf_filter_title_display($group['group_name'])).'">'."\n";
			if($group['forums'])
			{
				$out.= sf_compile_forums($group['forums'], 0, 0, $valueURL);
			}
			$out.= '</optgroup>';
		}
		if($showSelects)
		{
			$out.='</select>'."\n";
		}
	}
	return $out;
}
endif;

function sf_compile_forums($forums, $parent=0, $level=0, $valueURL=false)
{
	$out = '';
	$indent = '&nbsp;&rarr;&nbsp;';

	foreach($forums as $forum)
	{
		if($forum['parent'] == $parent && $forum['forum_id'] != $parent)
		{
			if($valueURL)
			{
				$out.='<option value="'.sf_build_url($forum['forum_slug'], '', 1, 0).'">';
			} else {
				$out.='<option value="'.$forum['forum_id'].'">';
			}
			$out.= str_repeat($indent, $level).sf_create_name_extract(sf_filter_title_display($forum['forum_name'])).'</option>'."\n";
			if($forum['children'])
			{
				$out.= sf_compile_forums($forums, $forum['forum_id'], $level+1, $valueURL);
			}
		}
	}
	return $out;
}

# ------------------------------------------------------------------
# sf_create_name_extract()
#
# truncates a forum or topic name for display in Quicklinks
#	$name:		name of forum or topic
# ------------------------------------------------------------------
function sf_create_name_extract($name)
{
	$name=sf_filter_title_display($name);
	if(strlen($name) > 35) $name = substr($name, 0, 35).'...';
	return $name;
}

# = RENDER POSTERS USER TYPE (or RANKING) =====
if(!function_exists('sf_render_usertype')):
function sf_render_usertype($status, $userid, $userposts)
{
	switch($status)
	{
		case 'admin':
			$default = __("Admin", "sforum").' '."\n";
			break;

		case 'user':
			$ismod = false;
			$moderators = sf_get_moderators();
			if ($moderators)
			{
				foreach ($moderators as $mod)
				{
					if ($userid == $mod['id']) $ismod=true;
				}
			}
			if ($ismod)
			{
				$default = __("Moderator", "sforum").' '."\n";
				$status = 'moderator';
			} else {
				$default = __("Member", "sforum").' '."\n";
			}
			break;

		case 'guest':
			$default = __("Guest", "sforum").' '."\n";
			break;
	}

	$out = sf_render_user_ranking($userid, $userposts, $status, esc_attr($default));

	return $out;
}
endif;

# = GET POSTERS RANKING =======================
if(!function_exists('sf_render_user_ranking')):
function sf_render_user_ranking($userid, $userposts, $status, $default)
{
	global $sfglobals, $SFPATHS;

	$out = '';

	# special ranks have top priority, then default Admin/Moderator/Guest, followed by forum ranks for users

	# check for special rank for user
	# if user has multiple special ranks, we just grab the first one
	if (isset($sfglobals['special_ranks']))
	{
		foreach ($sfglobals['special_ranks'] as $key => $rank)
		{
			if (isset($rank['users'][$userid]))
			{
				if ($rank['badge'] && file_exists(SF_STORE_DIR.'/'.$SFPATHS['ranks'].'/'.$rank['badge']))
				{
					$out.= '<img class="sfbadge" src="'.esc_url(SFRANKS.$rank['badge']).'" alt="'.$key.'" />';
					if($sfglobals['display']['posts']['rankdisplay'])
					{
						$out.= '<br />'.$key;
					}
				} else {
					$out.= $key;
				}
				return $out;
			}
		}
	}

	# check for forum rank
	$out = $default;
	if ($status == 'user')
	{
		$rankdata = $sfglobals['ranks'];
		if ($rankdata)
		{
			# find ranking of current user
			for ($x=0; $x<count($rankdata['posts']); $x++)
			{
				if ($userposts <= $rankdata['posts'][$x])
				{
					if ($rankdata['badge'][$x] && file_exists(SF_STORE_DIR.'/'.$SFPATHS['ranks'].'/'.$rankdata['badge'][$x]))
					{
						$out = '<img class="sfbadge" src="'.esc_url(SFRANKS.$rankdata['badge'][$x]).'" alt="'.$rankdata['title'][$x].'" />';
						if($sfglobals['display']['posts']['rankdisplay'])
						{
							$out.= '<br />'.$rankdata['title'][$x];
						}
					} else {
						$out = $rankdata['title'][$x];
					}
					return $out;
				}
			}
		}
	}

	# no special rank or forum rank, so use admin, moderator, member, guest defaults
	return $out;
}
endif;

# = RENDER TOPIC TAGS =====================
if(!function_exists('sf_render_topic_tags')):
function sf_render_topic_tags($use_tags, $topic, $related=false, $location=0)
{
	$out = '';

	if ($use_tags)
	{
		$out.= '<img class="sfalignleft" src="'.SFRESOURCES.'small-tag.png" alt="" title="'.__("Topic Tags", "sforum").'" />';
		if (isset($topic['tags']) && !empty($topic['tags']))
		{
			# Related Tags
			if ($related)
			{
                $site = SFHOMEURL."index.php?sf_ahah=tags&topicid=".$topic['topic_id'];
				$out.= '<a rel="nofollow" class="sficon" href="'.$site.'" onclick="return hs.htmlExpand(this, { objectType: \'ajax\', preserveContent: true, reflow: true, width: 650} )"><img class="sfalignright" style="margin-top:2px;" src="'. SFRESOURCES .'related.png" alt="" title="'.__("Related Topics", "sforum").'" /></a>';
			}

			foreach ($topic['tags'] as $key => $tag)
			{
                $url = esc_url(sf_build_qurl('forum=all', 'value='.urlencode($tag->tag_name), 'type=1', 'include=4', 'search=1'));
				$out.= '<a href="'.$url.'">';
				$out.= '<input class="sftagitem" type="button" name="tag-'.$topic['topic_id'].'-'.$key.'" value="'.sf_filter_title_display($tag->tag_name).'" onclick="location.href=\''.$url.'\';" />';
				$out.= '</a>';
			}
		} else {
			$out.= '<span class="sfstatusitem sfnotags">'.__("No Tags", "sforum").'</span>';
		}
	}

	return $out;
}
endif;



# = RENDER POSTERS AVATAR =====================
if(!function_exists('sf_render_avatar')):
function sf_render_avatar($icon, $userid, $useremail, $guestemail, $tag=false, $size=0, $link=true, $wpavatar='')
{
	# are we showing avatars?
	$sfavatars = array();

	$sfavatars = sf_get_option('sfavatars');
	if ($sfavatars['sfshowavatars'] == true)
	{
		# setup some variables
		$out = '';
		$avatar = array();

		# grab default size if not passed in
		if ($size == 0) $size = $sfavatars['sfavatarsize'];

		# setup the correct email address
		if (empty($useremail) ? $email = esc_attr($guestemail) : $email = $useremail);

		# if member - load their avatar settings array
		if ($userid) $avatar = sf_get_member_item($userid, 'avatar');

		# loop through prorities until we find an avatar to use
		foreach ($sfavatars['sfavatarpriority'] as $priority)
		{
			switch ($priority)
			{
				case 0:  # Gravatars
					if (function_exists('gravatar_path'))
					{
                        if ($email == '') $email = 'x@x.com';

						$url = gravatar_path(strtolower($email));

                        # see if the users default gravatar is returned and ignore if it is
                        $gopt = get_option('gravatar_options');
                        $bname = explode('/', $gopt['gravatar_default']);
                        $bname = $bname[count($bname) - 1];
    					$gravatar = strpos($url, $bname) ? false : true;
					} else {
						$rating = $sfavatars['sfgmaxrating'];
						switch($rating)
						{
							case 1:
								$grating='G';
								break;
							case 2:
								$grating='PG';
								break;
							case 3:
								$grating='R';
								break;
							case 4:
							default:
								$grating='X';
								break;
						}

						$url = "http://www.gravatar.com/avatar/".md5(strtolower($email))."?d=404&amp;size=".$size."&amp;rating=".$grating;

                    	# Is there an gravatar?
                        $headers = wp_get_http_headers($url);
                    	if (!is_array($headers)) :
                    		$gravatar = false;
                    	elseif (isset($headers["content-disposition"]) ) :
                    		$gravatar = true;
                    	else :
                    		$gravatar = false;
                    	endif;
					}

					if ($gravatar == true) # ignore gravatar blank images
					{
						$checkwidth = false;
						break 2;  # if actual gravatar image found, show it
					}

					break;

				case 1:  # WP avatars
					$out.= '<div class="sfuseravatar">';
                    if ($wpavatar)
                    {
                        $out.= sf_build_avatar_display($userid, $wpavatar, $link);

                    } else {
                        if ($userid) $email = $userid;
                        $out.= sf_build_avatar_display($userid, get_avatar($email, $size), $link);
                    }
					$out.= '</div>';
					return $out;   # display the wp avatars

				case 2:  # Uploaded avatars
					if (!empty($avatar['uploaded']))
					{
    					$avfile = $avatar['uploaded'];
						$url = SFAVATARURL.$avfile;
						if (file_exists(SFAVATARDIR.$avfile))
						{
							$checkwidth = true;
                            $avpath = SFAVATARDIR.$avfile;
							break 2;  # if uploaded avatar exists, show it
						}
					}
					break;

				case 3:  # SPF default avatars
				default:
					$checkwidth=true;
					switch ($icon)
					{
						case 'user':
							$image='userdefault.png';
							break;

						case 'admin':
							$image='admindefault.png';
							break;

						case 'guest':
						default:
							$image = 'guestdefault.png';
							break;
					}
					$url = SFAVATARURL.$image;
                    $avpath = SFAVATARDIR.$image;
					break 2;  # defaults, so show it

				case 4:  # Pool avatars
					$pavfile = $avatar['pool'];
					if (!empty($pavfile))
					{
						$url = SFAVATARPOOLURL.$pavfile;
						if (file_exists(SFAVATARPOOLDIR.$pavfile))
						{
							$checkwidth = true;
                            $avpath = SFAVATARPOOLDIR.$pavfile;
							break 2;  # if pool avatar exists, show it
						}
					}
					break;

				case 5:  # Remote avatars
					$ravfile = $avatar['remote'];
					if (!empty($ravfile))
					{
						$url = $ravfile;
					    if (!function_exists('curl_init') || ($fp = curl_init($url))) # just accept url if curl not available
						{
							$checkwidth = true;
                            $avpath = $url;
							break 2;  # if pool avatar exists, show it
						}
					}
					break;
			}
		}

		if($checkwidth)
		{
			if(!empty($url))
			{
				$avdata = array();

				global $gis_error;
				$gis_error = '';
				set_error_handler('sf_gis_error');
				$avdata = getimagesize($avpath);
				restore_error_handler();
				if($gis_error == '')
				{
					if($avdata[0] < $size)
					{
						$size = $avdata[0];
					}
				}
			}
		}

		if (!$tag)
		{
			$tsize = '';
			if($size != 0)
			{
				$tsize = ' width="'.$size.'" ';
			}
			$out.= '<div class="sfuseravatar">';
			$out.= sf_build_avatar_display($userid, '<img class="sfavatar" src="'.esc_url($url).'"'.$tsize.' alt="" />', $link);
			$out.= '</div>';
		} else {
			# template tag
			$tsize = '';
			if($size != 0)
			{
				$tsize = ' width="'.$size.'" ';
			}
			$out.= sf_build_avatar_display($userid, '<img class="sfavatartag" src="'.esc_url($url).'"'.$tsize.' alt="" />', $link);
		}
	}
	return $out;
}
endif;

?>