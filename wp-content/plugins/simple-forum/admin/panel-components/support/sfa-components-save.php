<?php
/*
Simple:Press
Admin Options Save Options Support Functions
$LastChangedDate: 2011-04-16 10:18:49 -0700 (Sat, 16 Apr 2011) $
$Rev: 5903 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

#= Save Toolbar Settings ===============================
function sfa_save_toolbar_data()
{
	# Toolbar
	if ($_POST['saveform'] == 'tbrestore')
	{
		return sfa_restore_toolbar_defaults();
	}

	check_admin_referer('forum-adminform_toolbar', 'forum-adminform_toolbar');

	if (!empty($_POST['delbuttons']))
	{
		return sfa_remove_toolbar_buttons($_POST['delbuttons']);
	}

	if ((!empty($_POST['stan_buttons']) || !empty($_POST['plug_buttons'])))
	{
		return sfa_reorder_toolbar_buttons($_POST['stan_buttons'], $_POST['plug_buttons']);
	}

	sf_update_option('sftbextras', sf_filter_text_save(trim($_POST['sftbextras'])));

}

#= Save Editor Options ===============================
function sfa_save_editor_data()
{
	check_admin_referer('forum-adminform_editor', 'forum-adminform_editor');

	$sfeditor = '';
	$sfeditor['sfeditor'] = sf_esc_int($_POST['editor']);
	if (isset($_POST['sfusereditor'])) { $sfeditor['sfusereditor'] = true; } else { $sfeditor['sfusereditor']=false; }
	if (isset($_POST['sfrejectformat'])) { $sfeditor['sfrejectformat'] = true; } else { $sfeditor['sfrejectformat']=false; }
	$sfeditor['sftmcontentCSS'] = sf_filter_filename_save($_POST['sftmcontentCSS']);
	$sfeditor['sftmuiCSS'] = sf_filter_filename_save($_POST['sftmuiCSS']);
	$sfeditor['sftmdialogCSS'] = sf_filter_filename_save($_POST['sftmdialogCSS']);
	$sfeditor['SFhtmlCSS'] = sf_filter_filename_save($_POST['SFhtmlCSS']);
	$sfeditor['SFbbCSS'] = sf_filter_filename_save($_POST['SFbbCSS']);
	$sfeditor['sflang'] = sf_esc_str($_POST['sflang']);
	if (isset($_POST['sfrtl'])) { $sfeditor['sfrtl'] = true; } else { $sfeditor['sfrtl'] = false; }
	if (isset($_POST['sfrelative'])) { $sfeditor['sfrelative'] = true; } else { $sfeditor['sfrelative'] = false; }

	sf_update_option('sfeditor', $sfeditor);

	$mess = __('Post Editor Options Updated', "sforum");
	return $mess;
}

#= Save and Upload Smmileys ===============================
function sfa_save_smileys_data()
{
	global $wpdb, $SFPATHS;

	check_admin_referer('forum-adminform_smileys', 'forum-adminform_smileys');

	$mess = '';

	# save Smiley options -------------------
	$sfsmileys = '';
	if (isset($_POST['sfsmallow'])) { $sfsmileys['sfsmallow'] = true; } else { $sfsmileys['sfsmallow'] = false; }
	$sfsmileys['sfsmtype'] = sf_esc_int($_POST['smileytype']);
	sf_update_option('sfsmileys', $sfsmileys);

	# now for the actual smileys
	$sfsmileys = array();
	$path = SF_STORE_DIR . '/'.$SFPATHS['smileys'].'/';

	$smileyname=array();
	$smileyname = $_POST['smname'];

	for ($x=0; $x < count($smileyname); $x++)
	{
		if((!empty($smileyname[$x])) && (!empty($_POST['smcode'][$x])))
		{
			$file = $_POST['smfile'][$x];
			$thisname = sf_create_slug($smileyname[$x], '');
			if(file_exists($path.$file))
			{
				$sfsmileys[$thisname][] = sf_filter_title_save($_POST['smfile'][$x]);
				$sfsmileys[$thisname][] = sf_filter_name_save($_POST['smcode'][$x]);
			}
		}
	}

	# load current saved smileys to get meta id
	$meta = sf_get_sfmeta('smileys', 'smileys');
	# now serialise and save
	$smeta = serialize($sfsmileys);
	sf_update_sfmeta('smileys', 'smileys', $smeta, $meta[0]['meta_id']);

	$mess .= __('Smileys Component Updated', "sforum");
	return $mess;
}

#= Save Login Options ===============================
function sfa_save_login_data()
{
	check_admin_referer('forum-adminform_login', 'forum-adminform_login');

	# login
	$sflogin = sf_get_option('sflogin');
	if (isset($_POST['sfshowlogin'])) { $sflogin['sfshowlogin'] = true; } else { $sflogin['sfshowlogin'] = false; }
	if (isset($_POST['sfshowreg'])) { $sflogin['sfshowreg'] = true; } else { $sflogin['sfshowreg'] = false; }
	if (isset($_POST['sfshowavatar'])) { $sflogin['sfshowavatar'] = true; } else { $sflogin['sfshowavatar'] = false; }
	if (isset($_POST['sfregmath'])) { $sflogin['sfregmath'] = true; } else { $sflogin['sfregmath'] = false; }
	if (isset($_POST['sfinlogin'])) { $sflogin['sfinlogin'] = true; } else { $sflogin['sfinlogin'] = false; }
	if (isset($_POST['sfloginskin'])) { $sflogin['sfloginskin'] = true; } else { $sflogin['sfloginskin'] = false; }

	if (!empty($_POST['sfloginurl'])) {
		$sflogin['sfloginurl'] = sf_filter_save_cleanurl($_POST['sfloginurl']);
	} else $sflogin['sfloginurl'] = SFSITEURL.'wp-login.php?action=login&amp;view=forum';

	if (!empty($_POST['sfloginemailurl'])) {
		$sflogin['sfloginemailurl'] = sf_filter_save_cleanurl($_POST['sfloginemailurl']);
	} else $sflogin['sfloginemailurl'] = SFSITEURL.'wp-login.php?action=login&view=forum';

	if (!empty($_POST['sflogouturl'])) {
		$sflogin['sflogouturl'] = sf_filter_save_cleanurl($_POST['sflogouturl']);
	} else $sflogin['sflogouturl'] = SFSITEURL.'wp-login.php?action=logout&amp;redirect_to='.SFURL;

	if (!empty($_POST['sfregisterurl'])) {
		$sflogin['sfregisterurl'] = sf_filter_save_cleanurl($_POST['sfregisterurl']);
	} else $sflogin['sfregisterurl'] = SFSITEURL.'wp-login.php?action=register&amp;view=forum';

	if (!empty($_POST['sflostpassurl'])) {
		$sflogin['sflostpassurl'] = sf_filter_save_cleanurl($_POST['sflostpassurl']);
	} else $sflogin['sflostpassurl'] = SFSITEURL.'wp-login.php?action=lostpassword&amp;view=forum';

	sf_update_option('sflogin', $sflogin);

    # RPX support
	$sfrpx = sf_get_option('sfrpx');
    $oldrpx = $sfrpx['sfrpxenable'];
	if (isset($_POST['sfrpxenable'])) { $sfrpx['sfrpxenable'] = true; } else { $sfrpx['sfrpxenable'] = false; }
	$sfrpx['sfrpxkey'] = sf_esc_str($_POST['sfrpxkey']);
	$sfrpx['sfrpxredirect'] = sf_filter_save_cleanurl($_POST['sfrpxredirect']);

    # change in RPX support?
    if (!$oldrpx && $sfrpx['sfrpxenable'])
    {
        require_once(SF_PLUGIN_DIR.'/library/sf-rpx.php');

        $post_data = array('apiKey' => $_POST[sfrpxkey], 'format' => 'json');
        $raw = spf_rpx_http_post('https://rpxnow.com/plugin/lookup_rp', $post_data);
        $r = spf_rpx_parse_lookup_rp($raw);
        if ($r)
        {
            $sfrpx['sfrpxrealm'] = $r['realm'];
        } else {
            $mess = __('Error in RPX API data!', 'sforum');
            return $mess;
        }
    }

	sf_update_option('sfrpx', $sfrpx);

	# if set update, otherwise its empty, so remove
	if ($_POST['sfsneakpeek'] != '')
	{
		sf_add_sfmeta('sneakpeek', 'message', sf_filter_text_save(trim($_POST['sfsneakpeek'])));
	} else {
		$msg = sf_get_sfmeta('sneakpeek', 'message');
		if (!empty($msg)) sf_delete_sfmeta($msg[0]['meta_id']);
	}

	# if set update, otherwise its empty, so remove
	if ($_POST['sfadminview'] != '')
	{
		sf_add_sfmeta('adminview', 'message', sf_filter_text_save(trim($_POST['sfadminview'])));
	} else {
		$msg = sf_get_sfmeta('adminview', 'message');
		if (!empty($msg)) sf_delete_sfmeta($msg[0]['meta_id']);
	}

	$mess = __('Login/Registration Component Updated', "sforum");
	return $mess;
}

#= Save Eextensions Options ===============================
function sfa_save_seo_data()
{
	global $wpdb;

	check_admin_referer('forum-adminform_seo', 'forum-adminform_seo');

	$mess= '';

	# browser title
	$sfseo = array();
	if (isset($_POST['sfseo_topic'])) { $sfseo['sfseo_topic'] = true; } else { $sfseo['sfseo_topic'] = false; }
	if (isset($_POST['sfseo_forum'])) { $sfseo['sfseo_forum'] = true; } else { $sfseo['sfseo_forum'] = false; }
	if (isset($_POST['sfseo_page'])) { $sfseo['sfseo_page'] = true; } else { $sfseo['sfseo_page'] = false; }
	$sfseo['sfseo_sep'] = sf_filter_title_save(trim($_POST['sfseo_sep']));
	sf_update_option('sfseo', $sfseo);

	# meta tags
	$sfmetatags= array();
	$sfmetatags['sfdescr'] = sf_filter_title_save(trim($_POST['sfdescr']));
	$sfmetatags['sfdescruse'] = $_POST['sfdescruse'];
	if (isset($_POST['sfusekeywords'])) { $sfmetatags['sfusekeywords'] = true; } else { $sfmetatags['sfusekeywords'] = false; }
	if (isset($_POST['sftagwords'])) { $sfmetatags['sftagwords'] = true; } else { $sfmetatags['sftagwords'] = false; }
	$sfmetatags['sfkeywords'] = sf_filter_title_save(trim($_POST['sfkeywords']));
	sf_update_option('sfmetatags', $sfmetatags);

    $sfbuildsitemap = sf_esc_int($_POST['sfbuildsitemap']);
	sf_update_option('sfbuildsitemap', $sfbuildsitemap);

    # schedule cron if rebuilding daily
	wp_clear_scheduled_hook('spf_cron_sitemap');
    if ($sfbuildsitemap == 3)  # rebuild once per day
    {
		wp_schedule_event(time(), 'daily', 'spf_cron_sitemap');
    }

	# auto removal cron job
	if (isset($_POST['sfuserremove']))
	{
		$sfuser['sfuserremove'] = true;
	} else {
		$sfuser['sfuserremove'] = false;
	}

	$mess .= '<br />'.__('SEO Components Updated', "sforum").$mess;
	return $mess;
}

#= Save PM Options ===============================
function sfa_save_pm_data()
{
	global $wpdb;

	check_admin_referer('forum-adminform_pm', 'forum-adminform_pm');

	sfa_update_check_option('sfprivatemessaging');

	# Save Private Message options
	$sfpm = '';
	if (isset($_POST['sfpmemail'])) { $sfpm['sfpmemail'] = true; } else { $sfpm['sfpmemail'] = false; }
	if (isset($_POST['sfpmcc'])) { $sfpm['sfpmcc'] = true; } else { $sfpm['sfpmcc'] = false; }
	if (isset($_POST['sfpmbcc'])) { $sfpm['sfpmbcc'] = true; } else { $sfpm['sfpmbcc'] = false; }
	if (isset($_POST['sfpmlimitedsend'])) { $sfpm['sfpmlimitedsend'] = true; } else { $sfpm['sfpmlimitedsend'] = false; }

	$sfpm['sfpmmax'] = sf_esc_int($_POST['sfpmmax']);
	$sfpm['sfpmmaxrecipients'] = sf_esc_int($_POST['sfpmmaxrecipients']);

	# auto removal period
	if (isset($_POST['sfpmkeep']) && $_POST['sfpmkeep'] > 0)
	{
		$sfpm['sfpmkeep'] = intval($_POST['sfpmkeep']);
	} else {
		$sfpm['sfpmkeep'] = 365; # if not filled in make it one year
	}

	# auto removal cron job
	wp_clear_scheduled_hook('spf_cron_pm');
	if (isset($_POST['sfpmremove']))
	{
		$sfpm['sfpmremove'] = true;
		wp_schedule_event(time(), 'daily', 'spf_cron_pm');
	} else {
		$sfpm['sfpmremove'] = false;
	}

	sf_update_option('sfpm', $sfpm);

	$mess = __('Private Messaging Components Updated', "sforum");
	return $mess;
}

function sfa_save_links_data()
{
	check_admin_referer('forum-adminform_links', 'forum-adminform_links');
	$mess = __('Options Updated', "sforum");

	$sfpostlinking = array();
	if (isset($_POST['sflinkabove'])) { $sfpostlinking['sflinkabove'] = true; } else { $sfpostlinking['sflinkabove'] = false; }
	if (isset($_POST['sflinksingle'])) { $sfpostlinking['sflinksingle'] = true; } else { $sfpostlinking['sflinksingle'] = false; }
	if (isset($_POST['sfuseautolabel'])) { $sfpostlinking['sfuseautolabel'] = true; } else { $sfpostlinking['sfuseautolabel'] = false; }
	if (isset($_POST['sfautoupdate'])) { $sfpostlinking['sfautoupdate'] = true; } else { $sfpostlinking['sfautoupdate'] = false; }
	if (isset($_POST['sfautocreate'])) { $sfpostlinking['sfautocreate'] = true; } else { $sfpostlinking['sfautocreate'] = false; }
	if (isset($_POST['sfpostcomment'])) { $sfpostlinking['sfpostcomment'] = true; } else { $sfpostlinking['sfpostcomment'] = false; }
	if (isset($_POST['sfkillcomment'])) { $sfpostlinking['sfkillcomment'] = true; } else { $sfpostlinking['sfkillcomment'] = false; }
	if (isset($_POST['sfeditcomment'])) { $sfpostlinking['sfeditcomment'] = true; } else { $sfpostlinking['sfeditcomment'] = false; }
	if (isset($_POST['sfhideduplicate'])) { $sfpostlinking['sfhideduplicate'] = true; } else { $sfpostlinking['sfhideduplicate'] = false; }

	$sfpostlinking['sflinkexcerpt'] = sf_esc_int($_POST['sflinkexcerpt']);
	$sfpostlinking['sflinkcomments'] = sf_esc_int($_POST['sflinkcomments']);
	$sfpostlinking['sflinkwords'] = sf_esc_int($_POST['sflinkwords']);
	$sfpostlinking['sflinkblogtext'] = sf_filter_text_save(trim($_POST['sflinkblogtext']));
	$sfpostlinking['sflinkforumtext'] = sf_filter_text_save(trim($_POST['sflinkforumtext']));
	$sfpostlinking['sfautoforum'] = sf_esc_int($_POST['sfautoforum']);
	$sfpostlinking['sflinkurls'] = sf_esc_int($_POST['sflinkurls']);

	sf_update_option('sfpostlinking', $sfpostlinking);

	$sflinkposttype = array();
	$post_types=get_post_types();

	foreach($post_types as $key=>$value)
	{
		if($key != 'attachment' && $key != 'revision' && $key != 'nav_menu_item')
		{
			$type = 'posttype_'.$key;
			if (isset($_POST[$type])) { $sflinkposttype[$key] = true; } else { $sflinkposttype[$key] = false; }
		}
	}

	sf_update_option('sflinkposttype', $sflinkposttype);

	return $mess;
}

#= Save Upload Options ===============================
function sfa_save_uploads_data()
{
	check_admin_referer('forum-adminform_uploads', 'forum-adminform_uploads');

	$sfuploads = '';

	if (isset($_POST['privatefolder'])) { $sfuploads['privatefolder'] = true; } else { $sfuploads['privatefolder'] = false; }
	if (isset($_POST['showmode'])) { $sfuploads['showmode'] = true; } else { $sfuploads['showmode'] = false; }

	$sfuploads['thumbsize'] = sf_esc_int($_POST['thumbsize']);
	$sfuploads['pagecount'] = sf_esc_int($_POST['pagecount']);
	$sfuploads['imagetypes'] = sf_filter_title_save(trim($_POST['imagetypes']));
	$sfuploads['imagemaxsize'] = sf_esc_int($_POST['imagemaxsize']);
	$sfuploads['mediatypes'] = sf_filter_title_save(trim($_POST['mediatypes']));
	$sfuploads['mediamaxsize'] = sf_esc_int($_POST['mediamaxsize']);
	$sfuploads['filetypes'] = sf_filter_title_save(trim($_POST['filetypes']));
	$sfuploads['filemaxsize'] = sf_esc_int($_POST['filemaxsize']);
	$sfuploads['prohibited'] = sf_filter_title_save(trim($_POST['prohibited']));
	$sfuploads['deftab'] = sf_esc_int(trim($_POST['sfdeftab']));

	sf_update_option('sfuploads', $sfuploads);

	$mess = __('Upload Options Updated', "sforum");
	return $mess;
}


#= Save Topic Status Sets ===============================
function sfa_save_topicstatus_data()
{
	check_admin_referer('forum-adminform_topicstatus', 'forum-adminform_topicstatus');

	# Topic Status Sets ---------------------
	if (isset($_POST['sftopstatname'][0]))
	{
		for ($x=0; $x<count($_POST['sftopstatid']); $x++)
		{
			$type = 'topic-status';
			$key = sf_filter_title_save(trim($_POST['sftopstatname'][$x]));
			$value = sf_filter_title_save(trim($_POST['sftopstatwords'][$x]));

			if(!empty($_POST['sftopstatid'][$x]))
			{
				if(isset($_POST['sftopstatdel'][$x]) && $_POST['sftopstatdel'][$x] == 'on')
				{
					sf_delete_sfmeta(sf_esc_int($_POST['sftopstatid'][$x]));
				} else {
					sf_update_sfmeta($type, $key, $value, sf_esc_int($_POST['sftopstatid'][$x]));
				}
			} else {
				sf_add_sfmeta($type, $key, $value);
			}
		}
	}

	$mess = __('Topic Status Component Updated', "sforum");
	return $mess;
}

#= Save Forum Rankings ===============================
function sfa_save_forumranks_data()
{
	# save forum ranks
	for ($x=0; $x<count($_POST['rankdesc']); $x++)
	{
		if (!empty($_POST['rankdesc'][$x]))
		{
			$rankdata = array();
			$rankdata['posts'] = sf_esc_int($_POST['rankpost'][$x]);
			$rankdata['usergroup'] = $_POST['rankug'][$x];
			$rankdata['badge'] = $_POST['rankbadge'][$x];
			if ($_POST['rankid'][$x] == -1) {
				sf_add_sfmeta('forum_rank', sf_filter_title_save(trim($_POST['rankdesc'][$x])), serialize($rankdata));
			} else {
				sf_update_sfmeta('forum_rank', sf_filter_title_save(trim($_POST['rankdesc'][$x])), serialize($rankdata), sf_esc_int($_POST['rankid'][$x]));
			}
		}
	}

	$mess = __('Forum Ranks Updated', "sforum");
	return $mess;
}

#= Save Special Rankins ===============================
function sfa_add_specialrank()
{
   check_admin_referer('special-rank-new', 'special-rank-new');

	# save special forum ranks
	if (!empty($_POST['specialrank']))
	{
		$rankdata = array();
		$rankdata['badge'] = '';
		sf_add_sfmeta('special_rank', sf_filter_title_save(trim($_POST['specialrank'])), serialize($rankdata));
	}

	$mess = __('Special Rank Added', "sforum");
	return $mess;
}

#= Save Special Rankins ===============================
function sfa_update_specialrank($id)
{
   check_admin_referer('special-rank-update', 'special-rank-update');

	# save special forum ranks
	if (!empty($_POST['specialrankdesc']))
	{
	    $desc = $_POST['specialrankdesc'];
		$badge = $_POST['specialrankbadge'];
		$rank = sf_get_sfmeta('special_rank', false, $id);
		$rankdata = unserialize($rank[0]['meta_value']);
		$rankdata['badge'] = $badge[$id];
		sf_update_sfmeta('special_rank', sf_filter_title_save(trim($desc[$id])), serialize($rankdata), $id);
	}

	$mess = __('Special Rank Updated', "sforum");
	return $mess;
}

function sfa_add_special_rank_member($id)
{
	check_admin_referer('special-rank-add', 'special-rank-add');

    $user_id_list = array_unique($_POST['amember_id']);
	if (empty($user_id_list)) return;

    # set up the users with special rank
	$rank = sf_get_sfmeta('special_rank', false, $id);
	$rankdata = unserialize($rank[0]['meta_value']);
	$ranks['badge'] = $rankdata['badge'];
	$ranks['users'] = $rankdata['users'];

	# add the new users
	for ($x=0; $x<count($user_id_list); $x++)
	{
		$ranks['users'][] = sf_esc_int($user_id_list[$x]);
	}

	sf_update_sfmeta('special_rank', sf_filter_title_save($rank[0]['meta_key']), serialize($ranks), $id);

    $mess = __("User(s) Added to Special Forum Rank", "sforum");
    return $mess;
}

function sfa_del_special_rank_member($id)
{
    check_admin_referer('special-rank-del', 'special-rank-del');

    $user_id_list = array_unique($_POST['dmember_id']);
	if (empty($user_id_list)) return;

    # remove the users from the special rank
	$rank = sf_get_sfmeta('special_rank', false, $id);
	$rankdata = unserialize($rank[0]['meta_value']);
	$ranks['badge'] = $rankdata['badge'];
	$ranks['users'] = $rankdata['users'];

	for( $x=0; $x<count($user_id_list); $x++)
	{
		$newlist = '';
		$user_id = sf_esc_int($user_id_list[$x]);
		foreach ($ranks['users'] as $userid)
		{
			if ($user_id != $userid)
			{
				$newlist[] = $userid;
			}
		}
		$ranks['users'] = $newlist;
	}

	sf_update_sfmeta('special_rank', sf_filter_title_save($rank[0]['meta_key']), serialize($ranks), $id);

    $mess = __("User(s) Deleted From Special Forum Rank", "sforum");
    return $mess;
}

#= Save Custom  Messages ===============================
function sfa_save_messages_data()
{
	check_admin_referer('forum-adminform_messages', 'forum-adminform_messages');

	# custom message for editor
	$sfpostmsg = array();
	$sfpostmsg['sfpostmsgtext'] = sf_filter_text_save(trim($_POST['sfpostmsgtext']));
	$sfpostmsg['sfpostmsgtopic'] = $_POST['sfpostmsgtopic'];
	$sfpostmsg['sfpostmsgpost'] = $_POST['sfpostmsgpost'];
	sf_update_option('sfpostmsg', $sfpostmsg);

	sf_update_option('sfeditormsg', sf_filter_text_save(trim($_POST['sfeditormsg'])));

	$mess = __('Custom Messages Updated', "sforum");
	return $mess;
}

#= Save Custom Icons ===============================
function sfa_save_icons_data()
{
	check_admin_referer('forum-adminform_icons', 'forum-adminform_icons');

	$mess='';
	for ($x=0; $x<3; $x++)
	{
		$custom='cusicon'.$x;
		if (!empty($_POST[$custom]))
		{
			$icon = $_POST[$custom];
			$path = SFCUSTOM.$icon;
			if(!file_exists($path))
			{
				$mess.= "* ".sprintf(__("Custom Icon '%s' does not exist", "sforum"), $icon).'<br />';
			}
		}
	}

	# Save Custom Icons (3)
	$sfcustom = array();
	$sfcustom[0]['custext'] = sf_filter_title_save(trim($_POST['custext0']));
	$sfcustom[0]['cuslink'] = sf_filter_save_cleanurl($_POST['cuslink0']);
	$sfcustom[0]['cusicon'] = sf_filter_title_save(trim($_POST['cusicon0']));
	$sfcustom[1]['custext'] = sf_filter_title_save(trim($_POST['custext1']));
	$sfcustom[1]['cuslink'] = sf_filter_save_cleanurl($_POST['cuslink1']);
	$sfcustom[1]['cusicon'] = sf_filter_title_save(trim($_POST['cusicon1']));
	$sfcustom[2]['custext'] = sf_filter_title_save(trim($_POST['custext2']));
	$sfcustom[2]['cuslink'] = sf_filter_save_cleanurl($_POST['cuslink2']);
	$sfcustom[2]['cusicon'] = sf_filter_title_save(trim($_POST['cusicon2']));
	sf_update_option('sfcustom', $sfcustom);

	$mess = __('Custom Icons Updated', "sforum");
	return $mess;
}

function sfa_save_tags_data()
{
	check_admin_referer('forum-adminform_tags', 'forum-adminform_tags');
	$mess = __('Options Updated', "sforum");

	sfa_update_check_option('sfuseannounce');
	sfa_update_check_option('sfannouncelist');
	sf_update_option('sfannouncecount', sf_esc_int($_POST['sfannouncecount']));
	sf_update_option('sfannouncehead', sf_filter_text_save(trim($_POST['sfannouncehead'])));
	sf_update_option('sfannouncetext', sf_filter_text_save(trim($_POST['sfannouncetext'])));

	sfa_update_check_option('sfannounceauto');
	sf_update_option('sfannouncetime', sf_esc_int($_POST['sfannouncetime']));

	return $mess;
}

function sfa_save_policies_data()
{
	check_admin_referer('forum-adminform_policies', 'forum-adminform_policies');

	# set up some more allowed tags for the textareas.
	# login
	$sfpolicy = sf_get_option('sfpolicy');

	if (isset($_POST['sfregtext'])) { $sfpolicy['sfregtext'] = true; } else { $sfpolicy['sfregtext'] = false; }
	if (isset($_POST['sfregcheck'])) { $sfpolicy['sfregcheck'] = true; } else { $sfpolicy['sfregcheck'] = false; }
	if (isset($_POST['sfreglink'])) { $sfpolicy['sfreglink'] = true; } else { $sfpolicy['sfreglink'] = false; }
	if (isset($_POST['sfprivlink'])) { $sfpolicy['sfprivlink'] = true; } else { $sfpolicy['sfprivlink'] = false; }

	$sfpolicy['sfregfile'] = sf_filter_filename_save(trim($_POST['sfregfile']));
	$sfpolicy['sfprivfile'] = sf_filter_filename_save(trim($_POST['sfprivfile']));

	sf_update_option('sfpolicy', $sfpolicy);

	# Registration text - if set update, otherwise its empty, so remove
	if ($_POST['sfregpolicy'] != '')
	{
		sf_add_sfmeta('registration', 'policy', sf_filter_text_save(trim($_POST['sfregpolicy'])));
	} else {
		$msg = sf_get_sfmeta('registration', 'policy');
		if (!empty($msg)) sf_delete_sfmeta($msg[0]['meta_id']);
	}

	# Prvacy text - if set update, otherwise its empty, so remove
	if ($_POST['sfprivpolicy'] != '')
	{
		sf_add_sfmeta('privacy', 'policy', sf_filter_text_save(trim($_POST['sfprivpolicy'])));
	} else {
		$msg = sf_get_sfmeta('privacy', 'policy');
		if (!empty($msg)) sf_delete_sfmeta($msg[0]['meta_id']);
	}

	$mess = __('Policy Documents Updated', "sforum");
	return $mess;
}

function sfa_save_mobile_data()
{
	check_admin_referer('forum-adminform_mobile', 'forum-adminform_mobile');

	$sfmobile = array();
	$sfmobile['browsers'] = sf_filter_text_save(trim($_POST['sfbrowsers']));
	$sfmobile['touch'] = sf_filter_text_save(trim($_POST['sftouch']));
	sf_update_option('sfmobile', $sfmobile);

	$mess = __('Mobile Support Updated', "sforum");
	return $mess;
}

#= Components Save Support Routines =================

# restore the full TM toolbar as supplied
function sfa_restore_toolbar_defaults()
{
	# Load up current from sfmeta (User)
	$tbmetauser = sf_get_sfmeta('tinymce_toolbar', 'user');
	$tbmetadefault = sf_get_sfmeta('tinymce_toolbar', 'default');

	sf_update_sfmeta('tinymce_toolbar', 'user', $tbmetadefault[0]['meta_value'], $tbmetauser[0]['meta_id']);

	$mess = __('Toolbar Restored', "sforum");
	return $mess;
}

function sfa_remove_toolbar_buttons($tblist)
{
	# Load up current from sfmeta
	$tbmeta = sf_get_sfmeta('tinymce_toolbar', 'user');
	$current = unserialize($tbmeta[0]['meta_value']);

	$tblist = explode("&", $tblist);
	$buttons = array();
	$plugins = array();
	foreach($tblist as $item)
	{
		$thisone = explode("_", $item);
		if($thisone[0]=="button")
		{
			$buttons[] = $thisone[1];
		} else {
			$plugins[] = $thisone[1];
		}
	}

	if($buttons) sort($buttons, SORT_NUMERIC);
	$index = 0;
	$newarray = array();
	foreach($current['tbar_buttons'] as $btn)
	{
		if(!in_array($index, $buttons))
		{
			$newarray[] = $btn;
		}
		$index++;
	}
	$current['tbar_buttons'] = $newarray;

	if($plugins) sort($plugins, SORT_NUMERIC);
	$index = 0;
	$newarray = array();
	foreach($current['tbar_buttons_add'] as $btn)
	{
		if(!in_array($index, $plugins))
		{
			$newarray[] = $btn;
		}
		$index++;
	}
	$current['tbar_buttons_add'] = $newarray;

	sf_update_sfmeta('tinymce_toolbar', 'user', serialize($current), $tbmeta[0]['meta_id']);

	$mess = __('Toolbar Updated', "sforum");
	return $mess;
}

# Save toolbar changes - re-ordering of buttons
function sfa_reorder_toolbar_buttons($stanlist, $pluglist)
{
	# Load up current from sfmeta
	$tbmeta = sf_get_sfmeta('tinymce_toolbar', 'user');
	$current = unserialize($tbmeta[0]['meta_value']);

	if($stanlist)
	{
		$stanlist = explode("&", $stanlist);
		$newarray = array();
		foreach($stanlist as $btn)
		{
			$thisone = explode("=", $btn);
			$newarray[] = $current['tbar_buttons'][$thisone[1]];
		}
		$current['tbar_buttons'] = $newarray;
	}

	if($pluglist)
	{
		$pluglist = explode("&", $pluglist);
		$newarray = array();
		foreach($pluglist as $btn)
		{
			$thisone = explode("=", $btn);
			$newarray[] = $current['tbar_buttons_add'][$thisone[1]];
		}
		$current['tbar_buttons_add'] = $newarray;
	}

	sf_update_sfmeta('tinymce_toolbar', 'user', serialize($current), $tbmeta[0]['meta_id']);

	$mess = __('Toolbar Updated', "sforum").$mess;
	return $mess;
}

?>