<?php
/*
Simple:Press
Admin Panels
$LastChangedDate: 2010-12-16 18:22:33 -0700 (Thu, 16 Dec 2010) $
$Rev: 5073 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

include_once (SF_PLUGIN_DIR.'/admin/sfa-menu.php');

# ------------------------------------------------------------------
# sfa_message()
#
# Common success/failure post-save messaging
# ------------------------------------------------------------------
function sfa_message($message)
{
	echo '<div class="message"><p><strong>'.$message.'</strong></p></div>';
	return;
}

# ------------------------------------------------------------------
# sfa_header()
#
# Common admin header. Sets up main toolbar and content area.
#	$title:			admin panel title
#	$icon:			admin panel icon
# ------------------------------------------------------------------
function sfa_header()
{
	global $apage, $sfglobals;

	$current_user = wp_get_current_user();

	$out ='<!-- Common wrapper and header -->';
	$out.='<div class="wrap ">';
	$out.='<div id="sfupdate"></div>';
	$out.='<div class="clearboth"></div>';

	# display warning message if no user groups exist
	sfa_check_warnings();

	$out.='<table class="sfamenutable" width="100%"><tr><td width="50%">';
	$out.='<div class="mainicon icon-forums"></div>';
	$out.='<h2>'.__("SPF Administration", "sforum").'</h2>';
	$out.='</td>';

    $site = SFHOMEURL."index.php?sf_ahah=acknowledge";
	$out.= '<td class="sfamenuitem sfabgwiki" width="12%" align="center"><a class="sfabutton" target="_blank" href="http://wiki.simple-press.com"><small>'.sf_split_label(__("Simple:Press Wiki", "sforum"),0).'</small></a></td>';
	$out.= '<td class="sfamenuitem sfabgabout" width="12%" align="center"><a class="sfabutton" href="'.$site.'" onclick="return hs.htmlExpand(this, { objectType: \'ajax\', preserveContent: true, reflow: true, width: 650} )"><small>'.sf_split_label(__("About Simple:Press", "sforum"),0).'</small></a></td>';

	$out.= '<td class="sfamenuitem sfabgpaypal" width="12%" align="center"><a class="sfabutton" href="'.SFHOMESITE.'/donation/"><small>'.sf_split_label(__("Make A Donation", "sforum"),1).'</small></a></td>';
	$out.= '<td class="sfamenuitem sfabggoto" width="12%" align="center"><a class="sfabutton" href="'.esc_url(sf_get_option('sfpermalink')).'"><small>'.sf_split_label(__("Go To Forum", "sforum"),1).'</small></a></td>';

	$out.='</tr></table></div><div class="clearboth"></div>';

	echo $out;

	return;
}

# ------------------------------------------------------------------
# sfa_footer()
#
# Common admin footer. Closes down content area and performs update
# check if option is turned on
# ------------------------------------------------------------------
function sfa_footer()
{
	if (sf_get_option('sfcheck'))
	{
        $site = SFHOMEURL."index.php?sf_ahah=toolbox&item=inlinecheck";
		$target = 'sfupdate';
		$gif = '';
		echo '<script type="text/javascript">'."\n";
		echo 'sfjadminTool("'.$site.'", "'.$target.'","'.$gif.'");';
		echo '</script>'."\n"."\n";
	}
	return;
}

function sfa_check_warnings()
{
	global $wpdb;

	# output warning if no SPF admins are defined
	if (sf_get_admins() == '')
	{
		sfa_message(__('Warning - There are no SPF Admins defined!  All WP admins now have SPF backend access!', 'sforum'));
	}

	# check for missing default members user group
	$value = sf_get_sfmeta('default usergroup', 'sfmembers');
	$defusers = $value[0]['meta_value'];
	$ugid = $wpdb->get_var("SELECT usergroup_id FROM ".SFUSERGROUPS." WHERE usergroup_id=".$defusers);
	if (empty($ugid))
	{
		sfa_message(__('Warning - The default user group for new Members is undefined!  Please visit the SPF options admin page, members tab and set the default user group.', 'sforum'));

	}

	# check for missing default guest user group
	$value = sf_get_sfmeta('default usergroup', 'sfguests');
	$defguest = $value[0]['meta_value'];
	$ugid = $wpdb->get_var("SELECT usergroup_id FROM ".SFUSERGROUPS." WHERE usergroup_id=".$defguest);
	if (empty($ugid))
	{
		sfa_message(__('Warning - The default user group for Guests is undefined!  Please visit the SPF options admin page, members tab and set the default user group.', 'sforum'));

	}

	# check for unreachable forums because of permissions
	$done = 0;
	$usergroups = $wpdb->get_results("SELECT usergroup_id FROM ".SFUSERGROUPS);
	if ($usergroups)
	{
		$has_members = false;
		foreach ($usergroups as $usergroup)
		{
			$members = $wpdb->get_row("SELECT user_id FROM ".SFMEMBERSHIPS." WHERE usergroup_id=".$usergroup->usergroup_id." LIMIT 1");
			if ($members || $usergroup->usergroup_id == $defguest)
			{
				$has_members = true;
				break;
			}
		}

		if (!$has_members)
		{
			sfa_message(__('Warning - There are no User Groups that have Members!  All Forums may only visible to Admin!', 'sforum'));
			$done = 1;
		}
	} else {
		sfa_message(__('Warning - There are no User Groups defined!  All Forums may only visible to Admin!', 'sforum'));
		$done = 1;
	}

	$roles = sfa_get_all_roles();
	if (!$roles)
	{
		sfa_message(__('Warning - There are no Permission Sets defined!  All Forums may only visible to Admin!', 'sforum'));
		$done = 1;
	}

	# dont duplicate forum warnings if there are no user groups, no user groups with members or no permission sets
	if ($done) return;

	$forums = $wpdb->get_results("SELECT forum_id, forum_name FROM ".SFFORUMS);
	if ($forums)
	{
		foreach ($forums as $forum)
		{
			$has_members = false;
			$permissions = sfa_get_forum_permissions($forum->forum_id);
			if ($permissions)
			{
				foreach ($permissions as $permission)
				{
					$members = $wpdb->get_row("SELECT user_id FROM ".SFMEMBERSHIPS." WHERE usergroup_id = $permission->usergroup_id LIMIT 1");
					if ($members || $usergroup->usergroup_id == $ugid)
					{
						$has_members = true;
						break;
					}
				}
			}

			if (!$has_members)
			{
				sfa_message(__('Warning - There are no User Groups with Members that have Permissions to Forum: '.sf_filter_title_display($forum->forum_name).'.  This Forum may be only visible to Admin!', 'sforum'));
			}
		}
	}

	# check if compatible with wp super cache
	if (function_exists('wp_cache_edit_rejected'))
	{
		global $cache_rejected_uri;
		$slug = '/'.sf_get_option('sfslug').'/';
		if (isset($cache_rejected_uri))
		{
			$found = false;
			foreach ($cache_rejected_uri as $value)
			{
				if ($value == $slug)
				{
					$found = true;
					break;
				}
			}

			if (!$found)
			{
				$string = __('WP Super Cache is not properly configured to work with Simple:Press. Please visit your WP Super Cache Settings Page and in the Accepted Filenames & Rejected URIs Section for the pages not to be cached input field, add the following string', 'sforum');
				$string.= ':<br /><br />'.$slug.'<br /><br />';
				$string.= __('Then, please clear your WP Super Cache to remove any cached Simple:Press pages.', 'sforum');
				sfa_message($string);
			}
		}
	}

	return;
}

?>