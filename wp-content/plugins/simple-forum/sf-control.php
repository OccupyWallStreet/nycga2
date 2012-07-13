<?php
/*
Plugin Name: Simple:Press
Version: 4.4.5
Plugin URI: http://simple-press.com
Description: Fully featured but simple page-based forum
Author: Andy Staines & Steve Klasen
Author URI: http://simple-press.com
WordPress Versions: 3.1 and above
For full acknowledgements click on the copyright/version strip
at the bottom of forum pages
*/

/*  Copyright 2006/2010  Andy Staines & Steve Klasen
	Please read the 'License' supplied with this plugin (goto help/documentation)
	and abide by it's few simple requests. Note that the Highslide JS library is free to use on non-commercial sites.
	Commercial sites should seek a license for a small fee of about $29US.

$LastChangedDate: 2011-06-25 07:24:41 -0700 (Sat, 25 Jun 2011) $
$Rev: 6398 $
*/

# -------------------------------------------------------------------------------------------------------------------

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

#==================================
# REMOVE FOR DISTRIBUTION
include_once (dirname(__FILE__).'/debug/sf-debug.php');
define("SHOWDEBUG", true);
#==================================

global $is_IIS;

# hack for some IIS installations
if ($is_IIS && @ini_get('error_log') == '') @ini_set('error_log', 'syslog');

# try to increase backtrack limit
if ((int) @ini_get('pcre.backtrack_limit') < 10000000000) @ini_set('pcre.backtrack_limit', 10000000000);

# try to increase php memory
if (function_exists('memory_get_usage') && ((int) @ini_get('memory_limit') < abs(intval('64M')))) @ini_set('memory_limit', '64M');

# ----------------------------------------------------------
# Establist version and system control constants
# ----------------------------------------------------------
define('SFPLUGNAME',	'Simple:Press');
define('SFVERSION',		'4.4.5');
define('SFBUILD',		6398);
define('SFRELEASE',		'Release');

# ==================================================================
# Bootstrap
# ==================================================================

# ------------------------------------------------------------------
# Load global components
# Load anything that is required globally
# ------------------------------------------------------------------
global $wpdb, $ISFORUM, $ISFORUMADMIN, $CACHE, $SFSTATUS, $SFPATHS, $CONTENTLOADED, $ACTIVEPANEL, $SFALLOPTIONS;

$ISFORUM = false;
$ISFORUMADMIN = false;
$CONTENTLOADED = false;
$CACHE = array();
$ACTIVEPANEL = array();

# if this is a network upgrade, make sure we switch to the site being updated
# this is so the constants are defined for right blog
if (isset($_GET['sfnetworkid']))
{
    switch_to_blog(esc_sql($_GET['sfnetworkid']));
}

# ------------------------------------------------------------------
# DB Tables constants
# ------------------------------------------------------------------
if(!defined('SF_PREFIX')) {
	define('SF_PREFIX', $wpdb->prefix);
}

sf_initialise_cache();

include_once (dirname(__FILE__).'/sf-config.php');

include_once (dirname(__FILE__).'/library/sf-primitives.php');

$SFALLOPTIONS = sf_load_alloptions();

$SFPATHS = sf_get_option('sfconfig');
include_once (dirname(__FILE__).'/sf-constants.php');
sf_setup_sitewide_constants();

$SFSTATUS = sfg_get_system_status();

include_once (SF_PLUGIN_DIR.'/sf-includes.php');
sf_setup_global_includes();

include_once (SF_PLUGIN_DIR.'/sf-loader-global.php');

include_once (SF_PLUGIN_DIR.'/sf-hooks.php');
sf_setup_sitewide_hooks();

include_once (SF_PLUGIN_DIR.'/sf-loader-ahah.php');
include_once (SF_PLUGIN_DIR.'/credentials/sf-credentials.php');

# ------------------------------------------------------------------
# Plugin Activation
# ensure that permalink and rewrite rules are set on a re-activation
# after upgrade
# ------------------------------------------------------------------
register_activation_hook(__FILE__, 'sfg_update_permalink');
# ------------------------------------------------------------------

# ------------------------------------------------------------------
# sf_boot_forum()
# Called from hook: 'wp_print_scripts'
# Checks if a forum page (front and back) and loads the required
# javascripts
# ------------------------------------------------------------------
function sf_boot_forum()
{
	global $ISFORUM, $ISFORUMADMIN, $ACTIVEPANEL, $wp_query;

	if (is_admin())
	{
		sf_setup_sitewide_late_hooks();
		if ((isset($_GET['page'])) && (stristr($_GET['page'], 'simple-forum')) !== false)
		{
			$ISFORUMADMIN=true;
			sfa_set_activepanels();
			sfa_admin_load_js();
		}
	} else {
		if ((is_page()) && ($wp_query->post->ID == sf_get_option('sfpage')))
		{
			$ISFORUM=true;
			sf_setup_sitewide_late_hooks();
			sf_setup_global_constants();
			sf_setup_forum_constants();
			sf_setup_forum_includes();
			sf_setup_forum_hooks();
    		sf_load_front_js();
		} else {
			# non-forum page...
			sf_load_front_blog_js();
		}
	}
	return;
}

# ------------------------------------------------------------------
# sf_boot_forum_admin()
# Called from hook: 'admin_menu'
# Starts up the back end loading up the admin menus
# ------------------------------------------------------------------
function sf_boot_forum_admin()
{
	sf_setup_global_constants();
	sf_setup_admin_constants();
	sf_setup_admin_includes();
	sf_setup_admin_hooks();

	sfa_admin_menu();
	return;
}

# ------------------------------------------------------------------
# sf_check_header()
#
# Checks if this is the forum page loading and sets up the incudes
# and the page header
# Called by wp_head filter
# ------------------------------------------------------------------
function sf_check_header()
{
	global $ISFORUM, $sfvars, $wp_query, $sfglobals, $aioseop_options;

	if ($ISFORUM)
	{
		sf_setup_header();

		# If a PM request load PM files
		if (!empty($sfvars['pm']))
		{
			sf_setup_pm_includes();
		}
	} else {
		if(sf_get_option('sfannounceauto'))
		{
			echo '<script type="text/javascript" src="'.SFJSCRIPT.'forum/sf-forum.js"></script>' . "\n";
		}

        # is it a blog linked post and need to point canonical url to linke topic?
        $post = $wp_query->get_queried_object();
        if (!empty($post->ID) && $sfglobals['canonicalurl'] == false)
        {
      		$sfpostlinking = sf_get_option('sfpostlinking');
            $topic = sf_blog_links_control('read', $post->ID);
            if (!empty($topic) && $sfpostlinking['sflinkurls'] == 2) # point blog post to linked topic?
            {
                $forum_slug = sf_get_forum_slug($topic->forum_id);
                $topic_slug = sf_get_topic_slug($topic->topic_id);
                $url = sf_build_url($forum_slug, $topic_slug, 0, 0);
        		echo '<link rel="canonical" href="'.$url.'" />'."\n";
                $sfglobals['canonicalurl'] = true;
            }
        }

        # let wp canonical url run if we didnt do it
        if ($sfglobals['canonicalurl'] == false) rel_canonical();
	}
}

# ------------------------------------------------------------------
# sf_initialise_cache()
#
# Create the cache control array to stop wasted and multiple calls
# ro the building of permission caches and extender user object
# ------------------------------------------------------------------
function sf_initialise_cache()
{
	global $CACHE;

	$CACHE['user']=false;
	$CACHE['forumid']='';
	$CACHE['membership']=false;
	$CACHE['permissions']=false;
	$CACHE['roles']=false;
	$CACHE['ranks']=false;
	$CACHE['member']=false;
	$CACHE['globals']=false;

	return;
}

# ------------------------------------------------------------------
# sfa_set_activepanels()
#
# Builds contro list of admin panels for later identification
# ------------------------------------------------------------------
function sfa_set_activepanels()
{
	global $ACTIVEPANEL;

	$ACTIVEPANEL['forums']=0;
	$ACTIVEPANEL['options']=1;
	$ACTIVEPANEL['components']=2;
	$ACTIVEPANEL['usergroups']=3;
	$ACTIVEPANEL['permissions']=4;
	$ACTIVEPANEL['users']=5;
	$ACTIVEPANEL['profiles']=6;
	$ACTIVEPANEL['admins']=7;
	$ACTIVEPANEL['tags']=8;
	$ACTIVEPANEL['toolbox']=9;
	$ACTIVEPANEL['config']=10;
	$ACTIVEPANEL['integration']=11;

	return;
}

# ------------------------------------------------------------------
# sfg_get_system_status()
# Determine if forum can be run or if it requires install/upgrade
# ------------------------------------------------------------------
function sfg_get_system_status()
{
    global $wpdb;

    $current_version = sf_get_option('sfversion');
    $current_build = sf_get_option('sfbuild');

    # First find out if build number has changed but check first against the log
    # in case it is a glitch or has been changed in the toolbox
    if((empty($current_version) || version_compare($current_version, '1.0', '<')) || (($current_build != SFBUILD) || ($current_version != SFVERSION)))
    {
        # If table SFLOG exists then there is a chance the option records got corrupted (?) or the build has been manually rerset
        # so get the values from the log and compare
        $sql = "SHOW TABLES LIKE '".SFLOG."'";
        $log = $wpdb->query($sql);
        if(!empty($log))
        {
            # So the log table exists. We check the last log entry build number
            # aganst the option build number and if option build number is less
            # we update it to the last log entry
            $sql = "SELECT build, version FROM ".SFLOG." ORDER BY id DESC LIMIT 1";
            $log = $wpdb->get_results($sql);
            if($current_build != $log[0]->build)
            {
                # But if the force upgrade flag is set we do NOIT reset build number
                if(sf_get_option('sfforceupgrade') == false)
                {
                    sf_update_option('sfbuild', $log[0]->build);
                    sf_update_option('sfversion', $log[0]->version);
                    $current_build = $log[0]->build;
                    $current_version = $log[0]->version;
                }
            }
        }
    }

    # Has the systen been installed?
    if(empty($current_version) || version_compare($current_version, '1.0', '<')) return 'Install';

    # Base already installed - check Version and Build Number
    if(($current_build < SFBUILD) || ($current_version != SFVERSION)) return 'Upgrade';

    return 'ok';
}
?>