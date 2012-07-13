<?php
/*
Simple:Press
Admin Options General Support Functions
$LastChangedDate: 2011-06-05 09:16:54 -0700 (Sun, 05 Jun 2011) $
$Rev: 6253 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_get_global_data()
{
	$sfoptions = array();
	$sfoptions['sflockdown'] = sf_get_option('sflockdown');

	# auto update
	$sfauto = array();
	$sfauto = sf_get_option('sfauto');
	$sfoptions['sfautoupdate'] = $sfauto['sfautoupdate'];
	$sfoptions['sfautotime'] = $sfauto['sfautotime'];

	$sfrss = array();
	$sfrss = sf_get_option('sfrss');
	$sfoptions['sfrsscount'] = $sfrss['sfrsscount'];
	$sfoptions['sfrsswords'] = $sfrss['sfrsswords'];
	$sfoptions['sfrssfeedkey'] = $sfrss['sfrssfeedkey'];

	$sfblock = array();
	$sfblock = sf_get_option('sfblockadmin');
	$sfoptions['blockadmin'] = $sfblock['blockadmin'];
	$sfoptions['blockredirect'] = sf_filter_url_display($sfblock['blockredirect']);
	$sfoptions['blockprofile'] = $sfblock['blockprofile'];
    $sfoptions['blockroles'] = $sfblock['blockroles'];

	return $sfoptions;
}

function sfa_get_display_data()
{
	$sfdisplay = sf_get_option('sfdisplay');
	$sfcontrols = sf_get_option('sfcontrols');

	# Page title
	$sfoptions['sfnotitle'] = $sfdisplay['pagetitle']['notitle'];
	$sfoptions['sfbanner']  = sf_filter_url_display($sfdisplay['pagetitle']['banner']);

	# Breadcrumbs
	$sfoptions['sfshowbreadcrumbs'] = $sfdisplay['breadcrumbs']['showcrumbs'];
	$sfoptions['sfshowhome'] 		= $sfdisplay['breadcrumbs']['showhome'];
	$sfoptions['sfshowgroup'] 		= $sfdisplay['breadcrumbs']['showgroup'];
	$sfoptions['sfhome'] 			= sf_filter_url_display($sfdisplay['breadcrumbs']['homepath']);
	$sfoptions['tree'] 				= $sfdisplay['breadcrumbs']['tree'];

	# Members Unread Post Count
	$sfoptions['sfunread']			= $sfdisplay['unreadcount']['unread'];
	$sfoptions['sfmarkall']			= $sfdisplay['unreadcount']['markall'];

	# Search
	$sfoptions['sfsearchtop'] 		= $sfdisplay['search']['searchtop'];
	$sfoptions['sfsearchbottom'] 	= $sfdisplay['search']['searchbottom'];

	# Quicklinks
	$sfoptions['sfqltop'] 		= $sfdisplay['quicklinks']['qltop'];
	$sfoptions['sfqlbottom'] 	= $sfdisplay['quicklinks']['qlbottom'];
	$sfoptions['sfqlcount'] 	= $sfdisplay['quicklinks']['qlcount'];

	# Pagelinks
	$sfoptions['sfptop'] 		= $sfdisplay['pagelinks']['ptop'];
	$sfoptions['sfpbottom'] 	= $sfdisplay['pagelinks']['pbottom'];

	# Stats
	$sfoptions['sfstats'] 		= $sfdisplay['stats']['showstats'];
	$sfoptions['mostusers'] 	= $sfdisplay['stats']['mostusers'];
	$sfoptions['online'] 		= $sfdisplay['stats']['online'];
	$sfoptions['forumstats'] 	= $sfdisplay['stats']['forumstats'];
	$sfoptions['memberstats'] 	= $sfdisplay['stats']['memberstats'];
	$sfoptions['topposters'] 	= $sfdisplay['stats']['topposters'];
	$sfoptions['admins'] 		= $sfdisplay['stats']['admins'];
	$sfoptions['newusers']		= $sfdisplay['stats']['newusers'];

	$sfoptions['showtopcount']	= $sfcontrols['showtopcount'];
	$sfoptions['shownewcount']	= $sfcontrols['shownewcount'];

	# First/Last posts
	$sfoptions['fldate'] 		= $sfdisplay['firstlast']['date'];
	$sfoptions['fltime'] 		= $sfdisplay['firstlast']['time'];
	$sfoptions['fluser'] 		= $sfdisplay['firstlast']['user'];

	return $sfoptions;
}

function sfa_get_forums_data()
{
	$sfoptions = array();
	$sfdisplay = sf_get_option('sfdisplay');

	$sfoptions['grpdescription']	= $sfdisplay['groups']['description'];
	$sfoptions['showsubforums']		= $sfdisplay['groups']['showsubforums'];
	$sfoptions['showallsubs']		= $sfdisplay['groups']['showallsubs'];
	$sfoptions['combinesubcount']	= $sfdisplay['groups']['combinesubcount'];

	$sfoptions['frmdescription'] 	= $sfdisplay['forums']['description'];
	$sfoptions['newposticon'] 		= $sfdisplay['forums']['newposticon'];
	$sfoptions['pagelinks'] 		= $sfdisplay['forums']['pagelinks'];

	# users new post list
	$sfoptions['sfshownewuser'] 	= $sfdisplay['forums']['newposts'];
	$sfoptions['sfshownewcount'] 	= $sfdisplay['forums']['newcount'];
	$sfoptions['sfshownewabove'] 	= $sfdisplay['forums']['newabove'];
	$sfoptions['sfsortinforum'] 	= $sfdisplay['forums']['sortinforum'];
	$sfoptions['sfpinned'] 		    = $sfdisplay['forums']['pinned'];

	$sfoptions['sfsingleforum'] 	= $sfdisplay['forums']['singleforum'];

	# Load View Column Settings
	$sfoptions['fc_topics'] = $sfdisplay['forums']['topiccol'];
	$sfoptions['fc_posts'] 	= $sfdisplay['forums']['postcol'];
	$sfoptions['fc_last'] 	= $sfdisplay['forums']['lastcol'];
	$sfoptions['showtitle'] = $sfdisplay['forums']['showtitle'];
	$sfoptions['showtitletop'] = $sfdisplay['forums']['showtitletop'];

	return $sfoptions;
}

function sfa_get_topics_data()
{
	$sfoptions = array();
	$sfdisplay = sf_get_option('sfdisplay');

	$sfoptions['pagelinks'] 	= $sfdisplay['topics']['pagelinks'];
	$sfoptions['statusicons'] 	= $sfdisplay['topics']['statusicons'];
	$sfoptions['postrating'] 	= $sfdisplay['topics']['postrating'];
	$sfoptions['topicstatus'] 	= $sfdisplay['topics']['topicstatus'];
	$sfoptions['topictags'] 	= $sfdisplay['topics']['topictags'];
	$sfoptions['showsubforums']	= $sfdisplay['topics']['showsubforums'];

	$sfoptions['posttip'] 		= $sfdisplay['topics']['posttip'];

	$sfoptions['sfpagedtopics'] = $sfdisplay['topics']['perpage'];
	$sfoptions['sfpaging'] 		= $sfdisplay['topics']['numpagelinks'];
	$sfoptions['sftopicsort'] 	= $sfdisplay['topics']['sortnewtop'];
	$sfoptions['sfmaxtags'] 	= $sfdisplay['topics']['maxtags'];

	$sfoptions['tc_first'] 		= $sfdisplay['topics']['firstcol'];
	$sfoptions['tc_last'] 		= $sfdisplay['topics']['lastcol'];
	$sfoptions['tc_posts'] 		= $sfdisplay['topics']['postcol'];
	$sfoptions['tc_views'] 		= $sfdisplay['topics']['viewcol'];
	$sfoptions['print'] 		= $sfdisplay['topics']['print'];

	return $sfoptions;
}

function sfa_get_posts_data()
{
	$sfoptions = array();
	$sfdisplay = sf_get_option('sfdisplay');

	$sfoptions['sfpagedposts'] 		= $sfdisplay['posts']['perpage'];
	$sfoptions['sfpostpaging'] 		= $sfdisplay['posts']['numpagelinks'];
	$sfoptions['sfuserabove'] 		= $sfdisplay['posts']['userabove'];
	$sfoptions['sfsortdesc'] 		= $sfdisplay['posts']['sortdesc'];
	$sfoptions['sfshoweditdata'] 	= $sfdisplay['posts']['showedits'];
	$sfoptions['sfshoweditlast'] 	= $sfdisplay['posts']['showlastedit'];

	$sfoptions['sftagsabove'] 		= $sfdisplay['posts']['tagstop'];
	$sfoptions['sftagsbelow'] 		= $sfdisplay['posts']['tagsbottom'];

	$sfoptions['topicstatushead'] 	= $sfdisplay['posts']['topicstatushead'];
	$sfoptions['topicstatuschanger'] = $sfdisplay['posts']['topicstatuschanger'];

	$sfoptions['online'] 		= $sfdisplay['posts']['online'];
	$sfoptions['time'] 			= $sfdisplay['posts']['time'];
	$sfoptions['date'] 			= $sfdisplay['posts']['date'];
	$sfoptions['usertype'] 		= $sfdisplay['posts']['usertype'];
	$sfoptions['rankdisplay'] 	= $sfdisplay['posts']['rankdisplay'];
	$sfoptions['location'] 		= $sfdisplay['posts']['location'];
	$sfoptions['postcount'] 	= $sfdisplay['posts']['postcount'];
	$sfoptions['permalink'] 	= $sfdisplay['posts']['permalink'];
	$sfoptions['print'] 		= $sfdisplay['posts']['print'];

	$sfoptions['sffbconnect'] 	= $sfdisplay['posts']['sffbconnect'];
	$sfoptions['sfmyspace'] 	= $sfdisplay['posts']['sfmyspace'];
	$sfoptions['sflinkedin'] 	= $sfdisplay['posts']['sflinkedin'];

	# twitter
	$sftwitter = array();
	$sftwitter = sf_get_option('sftwitter');
	$sfoptions['sftwitterfollow'] = $sftwitter['sftwitterfollow'];

	return $sfoptions;
}

function sfa_get_content_data()
{

	$sfoptions = array();

	# image resizing
	$sfimage = array();
	$sfimage = sf_get_option('sfimage');
	$sfoptions['sfimgenlarge'] = $sfimage['enlarge'];
	$sfoptions['sfthumbsize'] = $sfimage['thumbsize'];
	$sfoptions['style'] = $sfimage['style'];
	$sfoptions['process'] = $sfimage['process'];

	# Post Rating
	$sfpostratings = array();
	$sfpostratings = sf_get_option('sfpostratings');
	$sfoptions['sfpostratings'] = $sfpostratings['sfpostratings'];
	$sfoptions['sfratingsstyle'] = $sfpostratings['sfratingsstyle'];

	$sfoptions['sfdates'] = sf_get_option('sfdates');
	$sfoptions['sftimes'] = sf_get_option('sftimes');
	$sfoptions['sfzone'] = sf_get_option('sfzone');

	if (empty($sfoptions['sfdates'])) $sfoptions['sfdates']='j F Y';
	if (empty($sfoptions['sftimes'])) $sfoptions['sftimes']='g:i a';

	# link filters
	$sffilters = array();
	$sffilters = sf_get_option('sffilters');
	$sfoptions['sfnofollow'] = $sffilters['sfnofollow'];
	$sfoptions['sftarget'] = $sffilters['sftarget'];
	$sfoptions['sfurlchars'] = $sffilters['sfurlchars'];
	$sfoptions['sffilterpre'] = $sffilters['sffilterpre'];
	$sfoptions['sfmaxlinks'] = $sffilters['sfmaxlinks'];
	$sfoptions['sfnolinksmsg'] = sf_filter_text_edit($sffilters['sfnolinksmsg']);
	$sfoptions['sfdupemember'] = $sffilters['sfdupemember'];
	$sfoptions['sfdupeguest'] = $sffilters['sfdupeguest'];

	# get options for bad word filtering
	$sfoptions['sfbadwords'] = stripslashes(sf_filter_text_edit(sf_get_option('sfbadwords')));  # extra stripslashes due to regex slashing
	$sfoptions['sfreplacementwords'] = sf_filter_text_edit(sf_get_option('sfreplacementwords'));

	# shortcode filtering
	$sfoptions['sffiltershortcodes'] = sf_get_option('sffiltershortcodes');
	$sfoptions['sfshortcodes'] = sf_filter_text_edit(sf_get_option('sfshortcodes'));

	# syntax highlighting
	$sfsyntax = array();
	$sfsyntax = sf_get_option('sfsyntax');
	$sfoptions['sfsyntaxforum'] = $sfsyntax['sfsyntaxforum'];
	$sfoptions['sfsyntaxblog'] = $sfsyntax['sfsyntaxblog'];
	$sfoptions['sfbrushes'] = $sfsyntax['sfbrushes'];

	return $sfoptions;
}

function sfa_get_members_data()
{
	global $wp_roles;

	$sfoptions = array();

	$sfmemberopts = sf_get_option('sfmemberopts');
	$sfoptions['sfshowmemberlist'] = $sfmemberopts['sfshowmemberlist'];
	$sfoptions['sflimitmemberlist'] = $sfmemberopts['sflimitmemberlist'];
	$sfoptions['sfcheckformember'] = $sfmemberopts['sfcheckformember'];
	$sfoptions['sfsinglemembership'] = $sfmemberopts['sfsinglemembership'];
	$sfoptions['sfhidestatus'] = $sfmemberopts['sfhidestatus'];
	$sfoptions['sfautosub'] = $sfmemberopts['sfautosub'];
	$sfoptions['sfviewperm'] = $sfmemberopts['sfviewperm'];

	# get default usergroups
	$value = sf_get_sfmeta('default usergroup', 'sfmembers');
	$sfoptions['sfdefgroup'] = $value[0]['meta_value'];
	$value = sf_get_sfmeta('default usergroup', 'sfguests');
	$sfoptions['sfguestsgroup'] = $value[0]['meta_value'];

	$sfguests = array();
	$sfguests = sf_get_option('sfguests');
	$sfoptions['reqemail'] = $sfguests['reqemail'];
	$sfoptions['storecookie'] = $sfguests['storecookie'];

	$sfuser = array();
	$sfuser = sf_get_option('sfuserremoval');
	$sfoptions['sfuserremove'] = $sfuser['sfuserremove'];
	$sfoptions['sfuserperiod'] = $sfuser['sfuserperiod'];
	$sfoptions['sfuserinactive'] = $sfuser['sfuserinactive'];
	$sfoptions['sfusernoposts'] = $sfuser['sfusernoposts'];

	$sfoptions['sfzone'] = sf_get_option('sfzone');

	# cron scheduled?
	$sfoptions['sched'] = wp_get_schedule('spf_cron_user');

	return $sfoptions;
}

function sfa_get_email_data()
{
	$sfoptions = array();

	# Load New User Email details
	$sfmail = array();
	$sfmail = sf_get_option('sfnewusermail');
	$sfoptions['sfusespfreg']=$sfmail['sfusespfreg'];
	$sfoptions['sfnewusersubject'] = sf_filter_title_display($sfmail['sfnewusersubject']);
	$sfoptions['sfnewusertext'] = sf_filter_title_display($sfmail['sfnewusertext']);

	# Load Email Filter Options
	$sfmail = array();
	$sfmail = sf_get_option('sfmail');
	$sfoptions['sfmailsender'] = $sfmail['sfmailsender'];
	$sfoptions['sfmailfrom'] = $sfmail['sfmailfrom'];
	$sfoptions['sfmaildomain'] = $sfmail['sfmaildomain'];
	$sfoptions['sfmailuse'] = $sfmail['sfmailuse'];

	return $sfoptions;
}

function sfa_get_style_data()
{
	$sfoptions = array();

	# style settings
	$sfstyle = array();
	$sfstyle = sf_get_option('sfstyle');
	$sfoptions['sfskin'] = $sfstyle['sfskin'];
	$sfoptions['sficon'] = $sfstyle['sficon'];
	$sfoptions['sfsize'] = $sfstyle['sfsize'];
	$sfoptions['sfcsssrc'] = $sfstyle['sfcsssrc'];

	# Load icon List
	$ilist = array();
	$ilist = sf_get_option('sfshowicon');

	# We need to check this is kosher as it has been known to get corrupted.
	# if empty - rebuild it
	if(!empty($ilist))
	{
		$sfoptions['icon-list'] = $ilist;
	} else {
		$sfoptions['icon-list'] = sf_regenerate_iconlist();
	}

	# output clear floats?
	$sfoptions['sffloatclear'] = sf_get_option('sffloatclear');

	# post content wrap and width
	$sfpostwrap = array();
	$sfpostwrap = sf_get_option('sfpostwrap');
	$sfoptions['postwrap']=$sfpostwrap['postwrap'];
	$sfoptions['postwidth']=$sfpostwrap['postwidth'];

	$sfoptions['sfwplistpages'] = sf_get_option('sfwplistpages');

	$sfoptions['sftemplate'] = false;

	return $sfoptions;
}

function sf_regenerate_iconlist()
{
	$icons = array(
	'Login'							=> 1,
	'Register'						=> 1,
	'Logout'						=> 1,
	'Profile'						=> 1,
	'Add a New Topic'				=> 1,
	'Forum Locked'					=> 1,
	'Reply to Post'					=> 1,
	'Topic Locked'					=> 1,
	'Quote and Reply'				=> 1,
	'Edit Your Post'				=> 1,
	'Return to Search Results'		=> 1,
	'Subscribe'						=> 1,
	'Forum RSS'						=> 1,
	'Topic RSS'						=> 1,
	'All RSS'						=> 1,
	'Search'						=> 1,
	'New Posts'						=> 1,
	'Group RSS'						=> 1,
	'Send PM'						=> 1,
	'Return to forum'				=> 1,
	'Compose PM'					=> 1,
	'Go To Inbox'					=> 1,
	'Go To Sentbox'					=> 1,
	'Report Post'					=> 1,
	'Lock this Topic'				=> 1,
	'Pin this Topic'				=> 1,
	'Create Linked Post'			=> 1,
	'Pin this Post'					=> 1,
	'Edit Timestamp'				=> 1,
	'Subscribe to this Topic'		=> 1,
	'Review Watched Topics'			=> 1,
	'End Topic Watch'				=> 1,
	'Watch Topic'					=> 1,
	'Members'						=> 1,
	'Unsubscribe'					=> 1,
	'Watch this Topic'				=> 1,
	'Stop Watching this Topic'		=> 1,
	'Unsubscribe from this Topic'	=> 1,
	'Manage'						=> 1,
	'Print this Post'				=> 1,
	'Print this Topic'				=> 1,
	'Related Topics'				=> 1,
	'Mark All Read'					=> 0
	);
	return $icons;
}

?>