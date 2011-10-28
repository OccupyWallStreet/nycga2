<?php
/*
Simple:Press
Includes - loading just what is needed
$LastChangedDate: 2010-07-04 08:10:57 -0700 (Sun, 04 Jul 2010) $
$Rev: 4214 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# ------------------------------------------------------------------
# GLOBAL FORUM - Front and Back end base Includes
# ------------------------------------------------------------------
function sf_setup_global_includes()
{
	global $SFSTATUS, $SFPATHS;

	if (file_exists(SF_STORE_DIR.'/'.$SFPATHS['pluggable'].'/sf-pluggable.php')) {
		include_once (SF_STORE_DIR.'/'.$SFPATHS['pluggable'].'/sf-pluggable.php');
	}

	if (file_exists(SF_STORE_DIR.'/'.$SFPATHS['filters'].'/sf-custom-filters.php')) {
		include_once (SF_STORE_DIR.'/'.$SFPATHS['filters'].'/sf-custom-filters.php');
	}

	if (file_exists(SF_STORE_DIR.'/'.$SFPATHS['hooks'].'/sf-hook-template.php')) {
		include_once (SF_STORE_DIR.'/'.$SFPATHS['hooks'].'/sf-hook-template.php');
	}

	include_once (SF_PLUGIN_DIR.'/library/sf-primitives.php');
	include_once (SF_PLUGIN_DIR.'/library/sf-public.php');
	include_once (SF_PLUGIN_DIR.'/library/sf-ahah-handler.php');
	include_once (SF_PLUGIN_DIR.'/library/sf-filters.php');

	# load cron stuff if needed
	$sfpm = sf_get_option('sfpm');
	$sfuser = sf_get_option('sfuserremoval');
    if (!empty($sfpm['sfpmremove']) || !empty($sfuser['sfuserremove']) || sf_get_option('sfbuildsitemap') == 3)
	{
		include_once (SF_PLUGIN_DIR.'/library/sf-cron.php');
	}

    # load rpx support if needed
    $sfrpx = sf_get_option('sfrpx');
    if ($sfrpx['sfrpxenable'])
    {
		include_once (SF_PLUGIN_DIR.'/library/sf-rpx.php');
    }

    # general includes
	include_once (SF_PLUGIN_DIR.'/library/sf-permissions.php');
	include_once (SF_PLUGIN_DIR.'/library/sf-database.php');
	include_once (SF_PLUGIN_DIR.'/library/sf-support.php');
	include_once (SF_PLUGIN_DIR.'/library/sf-permalinks.php');
	include_once (SF_PLUGIN_DIR.'/library/sf-common-display.php');
	include_once (SF_PLUGIN_DIR.'/credentials/sf-credentials.php');
	$sfmail = sf_get_option('sfnewusermail');
	if($sfmail['sfusespfreg']) {
		include_once('credentials/sf-new-user-email.php');
	}
	include_once (SF_PLUGIN_DIR.'/library/sf-common-functions.php');
	include_once (SF_PLUGIN_DIR.'/library/sf-globals.php');

	# optionally load stuff based on configuration options
	$sfsupport = sf_get_option('sfsupport');
	if ($sfsupport['sfusinglinking'] || $SFSTATUS != 'ok') {
		include_once (SF_PLUGIN_DIR.'/linking/sf-links-blog.php');
		include_once (SF_PLUGIN_DIR.'/template-tags/sf-template-tags-bloglinking.php');
    	include_once (SF_PLUGIN_DIR.'/library/sf-kses.php');
	}
	if ($sfsupport['sfusinglinkcomments'] || $SFSTATUS != 'ok') {
		include_once (SF_PLUGIN_DIR.'/linking/sf-links-comments.php');
		include_once (SF_PLUGIN_DIR.'/template-tags/sf-template-tags-comments.php');
	}
	if ($sfsupport['sfusingwidgets'] || $SFSTATUS != 'ok') {
		include_once (SF_PLUGIN_DIR.'/template-tags/sf-widgets.php');
		include_once (SF_PLUGIN_DIR.'/template-tags/sf-template-tags-lists.php');
	}
	if ($sfsupport['sfusinggeneraltags'] || $SFSTATUS != 'ok') {
		include_once (SF_PLUGIN_DIR.'/template-tags/sf-template-tags-general.php');
	}
	if ($sfsupport['sfusingavatartags'] || $SFSTATUS != 'ok') {
		include_once (SF_PLUGIN_DIR.'/template-tags/sf-template-tags-avatars.php');
	}
	if ($sfsupport['sfusinglinkstags'] || $SFSTATUS != 'ok') {
		include_once (SF_PLUGIN_DIR.'/template-tags/sf-template-tags-links.php');
	}
	if ($sfsupport['sfusingtagstags'] || $SFSTATUS != 'ok') {
		include_once (SF_PLUGIN_DIR.'/template-tags/sf-template-tags-tags.php');
	}
	if ($sfsupport['sfusingpagestags'] || $SFSTATUS != 'ok') {
		include_once (SF_PLUGIN_DIR.'/template-tags/sf-template-tags-pages.php');
	}
	if ($sfsupport['sfusingliststags'] || $SFSTATUS != 'ok') {
		include_once (SF_PLUGIN_DIR.'/template-tags/sf-template-tags-lists.php');
	}
	if ($sfsupport['sfusingstatstags'] || $SFSTATUS != 'ok') {
		include_once (SF_PLUGIN_DIR.'/template-tags/sf-template-tags-stats.php');
		include_once (SF_PLUGIN_DIR.'/linking/sf-links-forum.php');
	}
	if ($sfsupport['sfusingpmtags'] || $SFSTATUS != 'ok') {
		include_once (SF_PLUGIN_DIR.'/template-tags/sf-template-tags-pm.php');
	}
}

# ------------------------------------------------------------------
# ADMIN - Back end base Includes
# ------------------------------------------------------------------
function sf_setup_admin_includes()
{
	include_once (SF_PLUGIN_DIR.'/sf-loader-admin.php');
	include_once (SF_PLUGIN_DIR.'/library/sf-common-functions.php');
	include_once (SF_PLUGIN_DIR.'/library/sf-kses.php');
	include_once (SF_PLUGIN_DIR.'/admin/library/sfa-support.php');
	include_once (SF_PLUGIN_DIR.'/admin/sfa-framework.php');
}

# ------------------------------------------------------------------
# FORUM - Front end base Includes
# ------------------------------------------------------------------
function sf_setup_forum_includes()
{
	global $SFPATHS;

	include_once (SF_PLUGIN_DIR.'/sf-header-forum.php');
	include_once (SF_PLUGIN_DIR.'/sf-loader-forum.php');
	include_once (SF_PLUGIN_DIR.'/library/sf-common-functions.php');
	include_once (SF_PLUGIN_DIR.'/library/sf-kses.php');
	include_once (SF_PLUGIN_DIR.'/library/sf-globals.php');
	include_once (SF_PLUGIN_DIR.'/forum/sf-page.php');
	include_once (SF_PLUGIN_DIR.'/forum/sf-page-components.php');
	include_once (SF_PLUGIN_DIR.'/forum/sf-forms.php');
	if (file_exists(SF_STORE_DIR.'/'.$SFPATHS['hooks'].'/sf-hook-template.php')) {
		include_once (SF_STORE_DIR.'/'.$SFPATHS['hooks'].'/sf-hook-template.php');
	}
	$sfsupport = sf_get_option('sfsupport');
	if ($sfsupport['sfusinglinking']) {
		include_once (SF_PLUGIN_DIR.'/linking/sf-links-forum.php');
	}
}

# ------------------------------------------------------------------
# FORUM - Post Saves
# ------------------------------------------------------------------
function sf_setup_post_save_includes()
{
	global $SFPATHS;

	include_once (SF_PLUGIN_DIR.'/library/sf-post-support.php');
	include_once (SF_PLUGIN_DIR.'/library/sf-common-functions.php');
	if (file_exists(SF_STORE_DIR.'/'.$SFPATHS['hooks'].'/sf-hook-template.php')) {
		include_once (SF_STORE_DIR.'/'.$SFPATHS['hooks'].'/sf-hook-template.php');
	}
}

# ------------------------------------------------------------------
# PMs - Front end PMs extension Includes
# ------------------------------------------------------------------
function sf_setup_pm_includes()
{
	include_once (SF_PLUGIN_DIR.'/messaging/sf-pm-control.php');
	include_once (SF_PLUGIN_DIR.'/messaging/sf-pm-components.php');
	include_once (SF_PLUGIN_DIR.'/messaging/sf-pm-database.php');
}

# ------------------------------------------------------------------
# PMs - PM Post Save
# ------------------------------------------------------------------
function sf_setup_pm_save_includes()
{
	include_once (SF_PLUGIN_DIR.'/library/sf-common-functions.php');
	include_once (SF_PLUGIN_DIR.'/library/sf-globals.php');
	include_once (SF_PLUGIN_DIR.'/library/sf-kses.php');
	include_once (SF_PLUGIN_DIR.'/library/sf-filters.php');
}

# ------------------------------------------------------------------
# FORUM - Front end ahah Includes
# ------------------------------------------------------------------
function sf_setup_ahah_includes()
{
	include_once (SF_PLUGIN_DIR.'/library/sf-globals.php');
	include_once (SF_PLUGIN_DIR.'/library/sf-support.php');
	include_once (SF_PLUGIN_DIR.'/forum/sf-page-components.php');
	include_once (SF_PLUGIN_DIR.'/library/sf-kses.php');
	include_once (SF_PLUGIN_DIR.'/library/sf-filters.php');
	include_once (SF_PLUGIN_DIR.'/library/sf-post-support.php');
}

# ------------------------------------------------------------------
# FORUM - Feed (RSS) Includes
# ------------------------------------------------------------------
function sf_setup_RSS_includes()
{
	include_once (SF_PLUGIN_DIR.'/library/sf-support.php');
	include_once (SF_PLUGIN_DIR.'/library/sf-filters.php');
}

?>