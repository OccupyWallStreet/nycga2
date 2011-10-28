<?php
/*
Simple:Press
Admin Options Save Options Support Functions
$LastChangedDate: 2011-06-05 09:16:54 -0700 (Sun, 05 Jun 2011) $
$Rev: 6253 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_save_global_data()
{
	global $wpdb, $wp_roles;

	check_admin_referer('forum-adminform_global', 'forum-adminform_global');
	$mess = __('Options Updated', "sforum");

	sfa_update_check_option('sflockdown');

	# auto update
	$sfauto = '';
	if (isset($_POST['sfautoupdate'])) { $sfauto['sfautoupdate'] = true; } else { $sfauto['sfautoupdate'] = false; }
	$sfauto['sfautotime'] = $_POST['sfautotime'];
	if (empty($sfauto['sfautotime']) || $sfauto['sfautotime']==0)
	{
		$sfauto['sfautotime'] = 300;
	}
	sf_update_option('sfauto', $sfauto);

	$sfrss = array();
	if(sf_esc_int($_POST['sfrsscount']) ? $sfrss['sfrsscount'] = sf_esc_int($_POST['sfrsscount']) : $sfrss['sfrsscount'] = 15);
	$sfrss['sfrsswords'] = sf_esc_int($_POST['sfrsswords']);
	if (isset($_POST['sfrssfeedkey'])) { $sfrss['sfrssfeedkey'] = true; } else { $sfrss['sfrssfeedkey'] = false; }
	sf_update_option('sfrss', $sfrss);

	$sfblock = array();
	if (isset($_POST['blockadmin'])) { $sfblock['blockadmin'] = true; } else { $sfblock['blockadmin'] = false; }
	if (isset($_POST['blockprofile'])) { $sfblock['blockprofile'] = true; } else { $sfblock['blockprofile'] = false; }
	$sfblock['blockredirect'] = sf_filter_save_cleanurl($_POST['blockredirect']);
    if ($sfblock['blockadmin'])
    {
        $sfblock['blockroles'] = array();
		$roles = array_keys($wp_roles->role_names);
		if ($roles)
		{
			foreach ($roles as $index => $role)
			{
                if (isset($_POST['role-'.$index])) { $sfblock['blockroles'][$role] = true; } else { $sfblock['blockroles'][$role] = false; }
            }
            # always allow admin
            $sfblock['blockroles']['administrator'] = true;
        }
    }

	sf_update_option('sfblockadmin', $sfblock);

	return $mess;
}

function sfa_save_display_data()
{
	global $wpdb;

	check_admin_referer('forum-adminform_display', 'forum-adminform_display');
	$mess = __('Display Options Updated', "sforum");

	$sfdisplay = sf_get_option('sfdisplay');
	$sfcontrols = sf_get_option('sfcontrols');

	# Page Title
	if (isset($_POST['sfnotitle'])) { $sfdisplay['pagetitle']['notitle'] = true; } else { $sfdisplay['pagetitle']['notitle'] = false; }
	$sfdisplay['pagetitle']['banner']  = sf_filter_save_cleanurl($_POST['sfbanner']);

	# Breaqdcrumbs
	if (isset($_POST['sfshowbreadcrumbs'])) { $sfdisplay['breadcrumbs']['showcrumbs'] = true; } else { $sfdisplay['breadcrumbs']['showcrumbs'] = false; }
	if (isset($_POST['sfshowhome'])) { $sfdisplay['breadcrumbs']['showhome'] = true; } else { $sfdisplay['breadcrumbs']['showhome'] = false; }
	if (isset($_POST['sfshowgroup'])) { $sfdisplay['breadcrumbs']['showgroup'] = true; } else { $sfdisplay['breadcrumbs']['showgroup'] = false; }
	if (isset($_POST['tree'])) { $sfdisplay['breadcrumbs']['tree'] = true; } else { $sfdisplay['breadcrumbs']['tree'] = false; }
	$sfdisplay['breadcrumbs']['homepath'] = sf_filter_title_save(trim($_POST['sfhome']));

	# Members Unread Post Count
	if (isset($_POST['sfunread'])) { $sfdisplay['unreadcount']['unread'] = true; } else { $sfdisplay['unreadcount']['unread'] = false; }
	if (isset($_POST['sfmarkall'])) { $sfdisplay['unreadcount']['markall'] = true; } else { $sfdisplay['unreadcount']['markall'] = false; }

	# Search
	if (isset($_POST['sfsearchtop'])) { $sfdisplay['search']['searchtop'] = true; } else { $sfdisplay['search']['searchtop'] = false; }
	if (isset($_POST['sfsearchbottom'])) { $sfdisplay['search']['searchbottom'] = true; } else { $sfdisplay['search']['searchbottom'] = false; }

	# Quicklinks
	if (isset($_POST['sfqltop'])) { $sfdisplay['quicklinks']['qltop'] = true; } else { $sfdisplay['quicklinks']['qltop'] = false; }
	if (isset($_POST['sfqlbottom'])) { $sfdisplay['quicklinks']['qlbottom'] = true; } else { $sfdisplay['quicklinks']['qlbottom'] = false; }
	if(sf_esc_int($_POST['sfqlcount']) ? $sfdisplay['quicklinks']['qlcount'] = sf_esc_int($_POST['sfqlcount']) : $sfdisplay['quicklinks']['qlcount'] = 10);

	# Pagelinks
	if (isset($_POST['sfptop'])) { $sfdisplay['pagelinks']['ptop'] = true; } else { $sfdisplay['pagelinks']['ptop'] = false; }
	if (isset($_POST['sfpbottom'])) { $sfdisplay['pagelinks']['pbottom'] = true; } else { $sfdisplay['pagelinks']['pbottom'] = false; }

	# Stats
	if (isset($_POST['sfstats'])) { $sfdisplay['stats']['showstats'] = true; } else { $sfdisplay['stats']['showstats'] = false; }
	if (isset($_POST['mostusers'])) { $sfdisplay['stats']['mostusers'] = true; } else { $sfdisplay['stats']['mostusers'] = false; }
	if (isset($_POST['online'])) { $sfdisplay['stats']['online'] = true; } else { $sfdisplay['stats']['online'] = false; }
	if (isset($_POST['forumstats'])) { $sfdisplay['stats']['forumstats'] = true; } else { $sfdisplay['stats']['forumstats'] = false; }
	if (isset($_POST['memberstats'])) { $sfdisplay['stats']['memberstats'] = true; } else { $sfdisplay['stats']['memberstats'] = false; }
	if (isset($_POST['newusers'])) { $sfdisplay['stats']['newusers'] = true; } else { $sfdisplay['stats']['newusers'] = false; }
	if (isset($_POST['admins'])) { $sfdisplay['stats']['admins'] = true; } else { $sfdisplay['stats']['admins'] = false; }
	if (isset($_POST['topposters'])) { $sfdisplay['stats']['topposters'] = true; } else { $sfdisplay['stats']['topposters'] = false; }

	if(isset($_POST['showtopcount']) ? $sfcontrols['showtopcount'] = sf_esc_int($_POST['showtopcount']) : $sfcontrols['showtopcount'] = 6);
	if(isset($_POST['shownewcount']) ? $sfcontrols['shownewcount'] = sf_esc_int($_POST['shownewcount']) : $sfcontrols['shownewcount'] = 6);

	# reset hour and day flag after any changes to ensure stats are refreshed
	$sfcontrols['hourflag'] = '0';
	$sfcontrols['dayflag'] = '0';
	sf_update_option('sfcontrols', $sfcontrols);

	# First/Last posts
	if (isset($_POST['fldate'])) { $sfdisplay['firstlast']['date'] = true; } else { $sfdisplay['firstlast']['date'] = false; }
	if (isset($_POST['fltime'])) { $sfdisplay['firstlast']['time'] = true; } else { $sfdisplay['firstlast']['time'] = false; }
	if (isset($_POST['fluser'])) { $sfdisplay['firstlast']['user'] = true; } else { $sfdisplay['firstlast']['user'] = false; }

	sf_update_option('sfdisplay', $sfdisplay);

	return $mess;
}

function sfa_save_forums_data()
{
	check_admin_referer('forum-adminform_forums', 'forum-adminform_forums');
	$mess = __('Options Updated', "sforum");

	$sfdisplay = sf_get_option('sfdisplay');

	if (isset($_POST['grpdescription'])) { $sfdisplay['groups']['description'] = true; } else { $sfdisplay['groups']['description'] = false; }
	if (isset($_POST['showsubforums'])) { $sfdisplay['groups']['showsubforums'] = true; } else { $sfdisplay['groups']['showsubforums'] = false; }
	if (isset($_POST['showallsubs'])) { $sfdisplay['groups']['showallsubs'] = true; } else { $sfdisplay['groups']['showallsubs'] = false; }
	if (isset($_POST['combinesubcount'])) { $sfdisplay['groups']['combinesubcount'] = true; } else { $sfdisplay['groups']['combinesubcount'] = false; }

	if (isset($_POST['frmdescription'])) { $sfdisplay['forums']['description'] = true; } else { $sfdisplay['forums']['description'] = false; }
	if (isset($_POST['newposticon'])) { $sfdisplay['forums']['newposticon'] = true; } else { $sfdisplay['forums']['newposticon'] = false; }
	if (isset($_POST['pagelinks'])) { $sfdisplay['forums']['pagelinks'] = true; } else { $sfdisplay['forums']['pagelinks'] = false; }

	# users new post list
	if (isset($_POST['sfshownewuser'])) { $sfdisplay['forums']['newposts'] = true; } else { $sfdisplay['forums']['newposts'] = false; }
	if (isset($_POST['sfshownewabove'])) { $sfdisplay['forums']['newabove'] = true; } else { $sfdisplay['forums']['newabove'] = false; }
	if (isset($_POST['sfsortinforum'])) { $sfdisplay['forums']['sortinforum'] = true; } else { $sfdisplay['forums']['sortinforum'] = false; }

	if(sf_esc_int($_POST['sfshownewcount']) ? $sfdisplay['forums']['newcount'] = sf_esc_int($_POST['sfshownewcount']) : $sfdisplay['forums']['newcount'] = 10);

	if (isset($_POST['sfsingleforum'])) { $sfdisplay['forums']['singleforum'] = true; } else { $sfdisplay['forums']['singleforum'] = false; }

	# Load View Column Settings
	if (isset($_POST['fc_topics'])) { $sfdisplay['forums']['topiccol'] = true; } else { $sfdisplay['forums']['topiccol'] = false; }
	if (isset($_POST['fc_posts'])) { $sfdisplay['forums']['postcol'] = true; } else { $sfdisplay['forums']['postcol'] = false; }
	if (isset($_POST['fc_last'])) { $sfdisplay['forums']['lastcol'] = true; } else { $sfdisplay['forums']['lastcol'] = false; }
	if (isset($_POST['showtitle'])) { $sfdisplay['forums']['showtitle'] = true; } else { $sfdisplay['forums']['showtitle'] = false; }
	if (isset($_POST['showtitletop'])) { $sfdisplay['forums']['showtitletop'] = true; } else { $sfdisplay['forums']['showtitletop'] = false; }

	if (isset($_POST['sfpinned'])) { $sfdisplay['forums']['pinned'] = true; } else { $sfdisplay['forums']['pinned'] = false; }

	sf_update_option('sfdisplay', $sfdisplay);

	return $mess;
}

function sfa_save_topics_data()
{
	check_admin_referer('forum-adminform_topics', 'forum-adminform_topics');
	$mess = __('Options Updated', "sforum");

	$sfdisplay = sf_get_option('sfdisplay');

	if (isset($_POST['pagelinks'])) { $sfdisplay['topics']['pagelinks'] = true; } else { $sfdisplay['topics']['pagelinks'] = false; }
	if (isset($_POST['statusicons'])) { $sfdisplay['topics']['statusicons'] = true; } else { $sfdisplay['topics']['statusicons'] = false; }
	if (isset($_POST['postrating'])) { $sfdisplay['topics']['postrating'] = true; } else { $sfdisplay['topics']['postrating'] = false; }
	if (isset($_POST['topicstatus'])) { $sfdisplay['topics']['topicstatus'] = true; } else { $sfdisplay['topics']['topicstatus'] = false; }
	if (isset($_POST['topictags'])) { $sfdisplay['topics']['topictags'] = true; } else { $sfdisplay['topics']['topictags'] = false; }
	if (isset($_POST['showsubforums'])) { $sfdisplay['topics']['showsubforums'] = true; } else { $sfdisplay['topics']['showsubforums'] = false; }

	if (isset($_POST['posttip'])) { $sfdisplay['topics']['posttip'] = true; } else { $sfdisplay['topics']['posttip'] = false; }

	if(sf_esc_int($_POST['sfpagedtopics']) ? $sfdisplay['topics']['perpage'] = sf_esc_int($_POST['sfpagedtopics']) : $sfdisplay['topics']['perpage'] = 20);
	if(sf_esc_int($_POST['sfpaging']) ? $sfdisplay['topics']['numpagelinks'] = sf_esc_int($_POST['sfpaging']) : $sfdisplay['topics']['numpagelinks'] = 4);
	$sfdisplay['topics']['maxtags'] = sf_esc_int($_POST['sfmaxtags']);
	if (isset($_POST['sftopicsort'])) { $sfdisplay['topics']['sortnewtop'] = true; } else { $sfdisplay['topics']['sortnewtop'] = false; }

	if (isset($_POST['tc_first'])) { $sfdisplay['topics']['firstcol'] = true; } else { $sfdisplay['topics']['firstcol'] = false; }
	if (isset($_POST['tc_last'])) { $sfdisplay['topics']['lastcol'] = true; } else { $sfdisplay['topics']['lastcol'] = false; }
	if (isset($_POST['tc_posts'])) { $sfdisplay['topics']['postcol'] = true; } else { $sfdisplay['topics']['postcol'] = false; }
	if (isset($_POST['tc_views'])) { $sfdisplay['topics']['viewcol'] = true; } else { $sfdisplay['topics']['viewcol'] = false; }
	if (isset($_POST['print'])) { $sfdisplay['topics']['print'] = true; } else { $sfdisplay['topics']['print'] = false; }

	sf_update_option('sfdisplay', $sfdisplay);

	return $mess;
}

function sfa_save_posts_data()
{
	check_admin_referer('forum-adminform_posts', 'forum-adminform_posts');
	$mess = __('Options Updated', "sforum");

	$sfdisplay = sf_get_option('sfdisplay');

	if(sf_esc_int($_POST['sfpagedposts']) ? $sfdisplay['posts']['perpage'] = sf_esc_int($_POST['sfpagedposts']) : $sfdisplay['posts']['perpage'] = 20);
	if(sf_esc_int($_POST['sfpostpaging']) ? $sfdisplay['posts']['numpagelinks'] = sf_esc_int($_POST['sfpostpaging']) : $sfdisplay['posts']['numpagelinks'] = 4);

	if (isset($_POST['sfuserabove'])) { $sfdisplay['posts']['userabove'] = true; } else { $sfdisplay['posts']['userabove'] = false; }
	if (isset($_POST['sfsortdesc'])) { $sfdisplay['posts']['sortdesc'] = true; } else { $sfdisplay['posts']['sortdesc'] = false; }
	if (isset($_POST['sfshoweditdata'])) { $sfdisplay['posts']['showedits'] = true; } else { $sfdisplay['posts']['showedits'] = false; }
	if (isset($_POST['sfshoweditlast'])) { $sfdisplay['posts']['showlastedit'] = true; } else { $sfdisplay['posts']['showlastedit'] = false; }
	if (isset($_POST['sftagsabove'])) { $sfdisplay['posts']['tagstop'] = true; } else { $sfdisplay['posts']['tagstop'] = false; }
	if (isset($_POST['sftagsbelow'])) { $sfdisplay['posts']['tagsbottom'] = true; } else { $sfdisplay['posts']['tagsbottom'] = false; }
	if (isset($_POST['topicstatushead'])) { $sfdisplay['posts']['topicstatushead'] = true; } else { $sfdisplay['posts']['topicstatushead'] = false; }
	if (isset($_POST['topicstatuschanger'])) { $sfdisplay['posts']['topicstatuschanger'] = true; } else { $sfdisplay['posts']['topicstatuschanger'] = false; }

	if (isset($_POST['online'])) { $sfdisplay['posts']['online'] = true; } else { $sfdisplay['posts']['online'] = false; }
	if (isset($_POST['time'])) { $sfdisplay['posts']['time'] = true; } else { $sfdisplay['posts']['time'] = false; }
	if (isset($_POST['date'])) { $sfdisplay['posts']['date'] = true; } else { $sfdisplay['posts']['date'] = false; }
	if (isset($_POST['usertype'])) { $sfdisplay['posts']['usertype'] = true; } else { $sfdisplay['posts']['usertype'] = false; }
	if (isset($_POST['rankdisplay'])) { $sfdisplay['posts']['rankdisplay'] = true; } else { $sfdisplay['posts']['rankdisplay'] = false; }
	if (isset($_POST['location'])) { $sfdisplay['posts']['location'] = true; } else { $sfdisplay['posts']['location'] = false; }
	if (isset($_POST['postcount'])) { $sfdisplay['posts']['postcount'] = true; } else { $sfdisplay['posts']['postcount'] = false; }
	if (isset($_POST['permalink'])) { $sfdisplay['posts']['permalink'] = true; } else { $sfdisplay['posts']['permalink'] = false; }
	if (isset($_POST['print'])) { $sfdisplay['posts']['print'] = true; } else { $sfdisplay['posts']['print'] = false; }

	if (isset($_POST['sffbconnect'])) { $sfdisplay['posts']['sffbconnect'] = true; } else { $sfdisplay['posts']['sffbconnect'] = false; }
	if (isset($_POST['sfmyspace'])) { $sfdisplay['posts']['sfmyspace'] = true; } else { $sfdisplay['posts']['sfmyspace'] = false; }
	if (isset($_POST['sflinkedin'])) { $sfdisplay['posts']['sflinkedin'] = true; } else { $sfdisplay['posts']['sflinkedin'] = false; }

	sf_update_option('sfdisplay', $sfdisplay);

	# twitter
	$sftwitter = array();
	if (isset($_POST['sftwitterfollow'])) { $sftwitter['sftwitterfollow'] = true; } else { $sftwitter['sftwitterfollow'] = false; }
	sf_update_option('sftwitter', $sftwitter);


	return $mess;
}

function sfa_save_content_data()
{
	global $wpdb;

	check_admin_referer('forum-adminform_content', 'forum-adminform_content');
	$mess = __('Options Updated', "sforum");

	# Save Image resizing
	$sfimage = array();
	$sfimage = sf_get_option('sfimage');
	if (isset($_POST['sfimgenlarge'])) { $sfimage['enlarge'] = true; } else { $sfimage['enlarge'] = false; }
	if (isset($_POST['process'])) { $sfimage['process'] = true; } else { $sfimage['process'] = false; }

	$thumb = sf_esc_int($_POST['sfthumbsize']);
	if ($thumb < 100)
	{
		$thumb = 100;
		$mess.= "<br />* ".__("Image Thumbsize reset to Minimum 100", "sforum");
	}
	$sfimage['thumbsize'] = $thumb;
	$sfimage['style'] = $_POST['style'];
	sf_update_option('sfimage', $sfimage);

	# Post Ratings
	$sfpostratings = sf_get_option('sfpostratings');
	if (isset($_POST['sfpostratings'])) { $sfpostratings['sfpostratings'] = true; } else { $sfpostratings['sfpostratings'] = false; }

	# before changing ratings style make sure it was confirmed
	if (isset($_POST['confirm-box-ratingsstyle']))
	{
		# reset post ratings data
		$wpdb->query("TRUNCATE ".SFPOSTRATINGS);
		# save new ratings style
		$sfpostratings['sfratingsstyle'] = sf_esc_int($_POST['ratingsstyle']);
	}
	sf_update_option('sfpostratings', $sfpostratings);

	sf_update_option('sfdates', sf_filter_title_save(trim($_POST['sfdates'])));
	sf_update_option('sftimes', sf_filter_title_save(trim($_POST['sftimes'])));

	# link filters
	$sffilters = array();
	if (isset($_POST['sfnofollow'])) { $sffilters['sfnofollow'] = true; } else { $sffilters['sfnofollow'] = false; }
	if (isset($_POST['sftarget'])) { $sffilters['sftarget'] = true; } else { $sffilters['sftarget'] = false; }
	if (isset($_POST['sffilterpre'])) { $sffilters['sffilterpre'] = true; } else { $sffilters['sffilterpre'] = false; }
	if (isset($_POST['sfdupemember'])) { $sffilters['sfdupemember'] = true; } else { $sffilters['sfdupemember'] = false; }
	if (isset($_POST['sfdupeguest'])) { $sffilters['sfdupeguest'] = true; } else { $sffilters['sfdupeguest'] = false; }
	$sffilters['sfurlchars'] = sf_esc_int($_POST['sfurlchars']);
	$sffilters['sfmaxlinks'] = sf_esc_int($_POST['sfmaxlinks']);
	if(empty($sffilters['sfmaxlinks'])) $sffilters['sfmaxlinks']=0;
	$sffilters['sfnolinksmsg'] = sf_filter_text_save(trim($_POST['sfnolinksmsg']));

	sf_update_option('sfbadwords', esc_regex(sf_filter_text_save(trim($_POST['sfbadwords']), false)));
	sf_update_option('sfreplacementwords', sf_filter_text_save(trim($_POST['sfreplacementwords']), false));

	sfa_update_check_option('sffiltershortcodes');
	sf_update_option('sfshortcodes', sf_filter_text_save(trim($_POST['sfshortcodes'])));

	$sfsyntax=array();
	if (isset($_POST['sfsyntaxforum'])) { $sfsyntax['sfsyntaxforum'] = true; } else { $sfsyntax['sfsyntaxforum'] = false; }
	if (isset($_POST['sfsyntaxblog'])) { $sfsyntax['sfsyntaxblog'] = true; } else { $sfsyntax['sfsyntaxblog'] = false; }

	# clean up brushes string
	$list = explode(',', $_POST['sfbrushes']);
	$brushes = array();
	if($list)
	{
		foreach($list as $item)
		{
			$brushes[]=trim($item);
		}
		$list=implode(',', $brushes);
	}
	$sfsyntax['sfbrushes']=$list;

	# if using highlighting then force turn off the pre filter
	if($sfsyntax['sfsyntaxforum'] == true)
	{
		$sffilters['sffilterpre'] = false;
	}

	sf_update_option('sfsyntax', $sfsyntax);
	sf_update_option('sffilters', $sffilters);

	return $mess;
}

function sfa_save_members_data()
{
	global $wp_roles;

	check_admin_referer('forum-adminform_members', 'forum-adminform_members');
	$mess = __('Options Updated', "sforum");

	$sfmemberopts = array();
	if (isset($_POST['sfshowmemberlist'])) { $sfmemberopts['sfshowmemberlist'] = true; } else { $sfmemberopts['sfshowmemberlist'] = false; }
	if (isset($_POST['sfcheckformember'])) { $sfmemberopts['sfcheckformember'] = true; } else { $sfmemberopts['sfcheckformember'] = false; }
	if (isset($_POST['sflimitmemberlist'])) { $sfmemberopts['sflimitmemberlist'] = true; } else { $sfmemberopts['sflimitmemberlist'] = false; }
	if (isset($_POST['sfsinglemembership'])) { $sfmemberopts['sfsinglemembership'] = true; } else { $sfmemberopts['sfsinglemembership'] = false; }
	if (isset($_POST['sfhidestatus'])) { $sfmemberopts['sfhidestatus'] = true; } else { $sfmemberopts['sfhidestatus'] = false; }
	if (isset($_POST['sfautosub'])) { $sfmemberopts['sfautosub'] = true; } else { $sfmemberopts['sfautosub'] = false; }
	if (isset($_POST['sfviewperm'])) { $sfmemberopts['sfviewperm'] = true; } else { $sfmemberopts['sfviewperm'] = false; }
	sf_update_option('sfmemberopts', $sfmemberopts);

	# save default usergroups
	sf_add_sfmeta('default usergroup', 'sfguests', sf_esc_int($_POST['sfguestsgroup'])); # default usergroup for guests
	sf_add_sfmeta('default usergroup', 'sfmembers', sf_esc_int($_POST['sfdefgroup'])); # default usergroup for members

	# check for changes in wp role usergroup assignments
	if (isset($_POST['sfrole']))
	{
		$roles = array_keys($wp_roles->role_names);
		foreach ($_POST['sfrole'] as $index => $role)
		{
			if ($_POST['sfoldrole'][$index] != $role)
			{
				sf_add_sfmeta('default usergroup', $roles[$index], sf_esc_int($role));
			}
		}
	}

	$sfguests = array();
	if (isset($_POST['reqemail'])) { $sfguests['reqemail'] = true; } else { $sfguests['reqemail'] = false; }
	if (isset($_POST['storecookie'])) { $sfguests['storecookie'] = true; } else { $sfguests['storecookie'] = false; }
	sf_update_option('sfguests', $sfguests);

	$sfuser = array();
	if (isset($_POST['sfuserinactive'])) { $sfuser['sfuserinactive'] = true; } else { $sfuser['sfuserinactive'] = false; }
	if (isset($_POST['sfusernoposts'])) { $sfuser['sfusernoposts'] = true; } else { $sfuser['sfusernoposts'] = false; }
	if (isset($_POST['sfuserperiod']) && $_POST['sfuserperiod'] > 0)
	{
		$sfuser['sfuserperiod'] = intval($_POST['sfuserperiod']);
	} else {
		$sfuser['sfuserperiod'] = 365; # if not filled in make it one year
	}

	# auto removal cron job
	wp_clear_scheduled_hook('spf_cron_user');
	if (isset($_POST['sfuserremove']))
	{
		$sfuser['sfuserremove'] = true;
		wp_schedule_event(time(), 'daily', 'spf_cron_user');
	} else {
		$sfuser['sfuserremove'] = false;
	}
	sf_update_option('sfuserremoval', $sfuser);

	sf_update_option('sfzone', sf_esc_int($_POST['sfzone']));

	return $mess;
}

function sfa_save_email_data()
{
	check_admin_referer('forum-adminform_email', 'forum-adminform_email');
	$mess = __('Options Updated', "sforum");

	# Save Email Options
	# Thanks to Andrew Hamilton for these routines (mail-from plugion)
	# Remove any illegal characters and convert to lowercase both the user name and domain name
	$domain_input_errors = array('http://', 'https://', 'ftp://', 'www.');
	$domainname = strtolower(sf_filter_title_save(trim($_POST['sfmaildomain'])));
	$domainname = str_replace ($domain_input_errors, "", $domainname);
	$domainname = preg_replace('/[^0-9a-z\-\.]/i','',$domainname);

	$illegal_chars_username = array('(', ')', '<', '>', ',', ';', ':', '\\', '"', '[', ']', '@', ' ');
	$username = strtolower(sf_filter_name_save(trim($_POST['sfmailfrom'])));
	$username = str_replace ($illegal_chars_username, "", $username);

	$sfmail = '';
	$sfmail['sfmailsender'] = sf_filter_name_save(trim($_POST['sfmailsender']));
	$sfmail['sfmailfrom'] = $username;
	$sfmail['sfmaildomain'] = $domainname;
	if (isset($_POST['sfmailuse'])) { $sfmail['sfmailuse'] = true; } else { $sfmail['sfmailuse'] = false; }
	sf_update_option('sfmail', $sfmail);

	# Save new user mail options
	$sfmail = array();
	if (isset($_POST['sfusespfreg'])) { $sfmail['sfusespfreg'] = true; } else { $sfmail['sfusespfreg'] = false; }
	$sfmail['sfnewusersubject'] = sf_filter_title_save(trim($_POST['sfnewusersubject']));
	$sfmail['sfnewusertext'] = sf_filter_title_save(trim($_POST['sfnewusertext']));
	sf_update_option('sfnewusermail', $sfmail);

	return $mess;
}

function sfa_save_style_data()
{
	global $SFPATHS;

	check_admin_referer('forum-adminform_style', 'forum-adminform_style');
	$mess = __('Options Updated', "sforum");

	# style settings
	$sfstyle = array();
	$sfstyle['sfskin'] = sf_esc_str($_POST['sfskin']);
	$sfstyle['sficon'] = sf_esc_str($_POST['sficon']);
	$sfstyle['sfsize'] = sf_esc_int($_POST['sfsize']);
	if (isset($_POST['sfcsssrc'])) { $sfstyle['sfcsssrc'] = true; } else { $sfstyle['sfcsssrc'] = false; }
	sf_update_option('sfstyle', $sfstyle);

	# Save Icon String - do we try for template?
	if (isset($_POST['sftemplate']))
	{
		# Ascerttain potemtially new icon set path
		$newpath = SF_STORE_DIR . '/'.$SFPATHS['styles'].'/icons/'.sf_esc_str($_POST['sficon']).'/ICON_DEFAULTS.php';
		if(file_exists($newpath))
		{
			include_once($newpath);
			$mess.=' - '. __("Icons Updated", "sforum");
		} else {
			$mess.=' - '. __("Icon Template File Not Found", "sforum");
		}
	} else {
		$icons = sf_get_option('sfshowicon');
		foreach ($icons as $key=>$value)
		{
			$iName = str_replace(' ', '_', $key);
			if (isset($_POST[$iName]))
			{
				$icons[$key]=1;
			} else {
				$icons[$key]=0;
			}
		}
		sf_update_option('sfshowicon', $icons);
	}

	sfa_update_check_option('sffloatclear');

	# post content wrap and width
	$sfpostwrap = array();
	if (isset($_POST['postwrap'])) { $sfpostwrap['postwrap'] = true; } else { $sfpostwrap['postwrap'] = false; }
	$sfpostwrap['postwidth']=sf_esc_int($_POST['postwidth']);
	sf_update_option('sfpostwrap', $sfpostwrap);

	if (isset($_POST['sfwplistpages'])) { $sfwplistpages = true; } else { $sfwplistpages = false; }
	sf_update_option('sfwplistpages', $sfwplistpages);

	return $mess;
}

?>