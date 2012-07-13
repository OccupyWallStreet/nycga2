<?php
/*
Simple:Press
Defined Constants
$LastChangedDate: 2011-05-14 22:30:14 -0700 (Sat, 14 May 2011) $
$Rev: 6084 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# ----------------------------------------------------------
# SITEWIDE GLOBAL Constants - all page loads
# ----------------------------------------------------------
function sf_setup_sitewide_constants()
{
	global $SFPATHS, $wpdb;

	# ------------------------------------------------------------------
	# Base site constants
	# ------------------------------------------------------------------
	if (!defined('SFPLUGHOME')) define('SFPLUGHOME',	'<a href="http://simple-press.com" target="_blank">'.SFPLUGNAME.'</a>');
	if (!defined('SFVERCHECK')) define('SFVERCHECK',	'http://simple-press.com/downloads/ForumVersion.chk');
	if (!defined('SFHOMESITE')) define('SFHOMESITE',	'http://simple-press.com');

	if (defined('WP_SITEURL')) {
		if (!defined('SFSITEURL')) define('SFSITEURL', trailingslashit(WP_SITEURL));
	} else {
		if (!defined('SFSITEURL')) define('SFSITEURL',	trailingslashit(site_url()));
	}

	if (!defined('SFHOMEURL')) {
		if (defined('WP_HOME')) {
			$home = trailingslashit(WP_HOME);
		} else {
			$home = trailingslashit(home_url());
		}

		if (is_admin() && force_ssl_admin()) $home = str_replace( 'http://', "https://", $home );
		define('SFHOMEURL', $home);
	}
	if (!defined('SFURL')) define('SFURL',	trailingslashit(sf_get_option('sfpermalink')));
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# check if already defined - depends on WP Version
	# ------------------------------------------------------------------
	if (!defined('SF_PLUGIN_DIR')) {
		if (!defined('WP_CONTENT_DIR')) define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
		if (!defined('WP_CONTENT_URL')) define('WP_CONTENT_URL', content_url());

		if (defined('WP_PLUGIN_DIR')) {
			if (!defined('SF_PLUGIN_DIR')) define('SF_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . basename(dirname(__FILE__)));
		} else {
			if (!defined('SF_PLUGIN_DIR')) define('SF_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins/' . basename(dirname(__FILE__)));
		}

		if (defined('WP_PLUGIN_URL')) {
			if (!defined('SF_PLUGIN_URL')) define('SF_PLUGIN_URL', plugins_url('simple-forum'));
		} else {
			if (!defined('SF_PLUGIN_URL')) define('SF_PLUGIN_URL', WP_CONTENT_URL . '/plugins/' . basename(dirname(__FILE__)));
		}
	}
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# define storage location base
	# ------------------------------------------------------------------
	if (!defined('SF_STORE_DIR'))              define('SF_STORE_DIR',				WP_CONTENT_DIR);
	if (!defined('SF_STORE_URL'))              define('SF_STORE_URL', 				WP_CONTENT_URL);
	if (!defined('SF_STORE_RELATIVE_BASE'))    define('SF_STORE_RELATIVE_BASE',		strrchr (WP_CONTENT_DIR, '/').'/');
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# Location of upladed Avatars and Smileys
	# ------------------------------------------------------------------
	if (!defined('SFAVATARURL'))       define('SFAVATARURL',	   SF_STORE_URL . '/'.$SFPATHS['avatars'].'/');
	if (!defined('SFAVATARDIR'))       define('SFAVATARDIR',	   SF_STORE_DIR . '/'.$SFPATHS['avatars'].'/');
	if (!defined('SFAVATARPOOLURL'))   define('SFAVATARPOOLURL',   SF_STORE_URL . '/'.$SFPATHS['avatar-pool'].'/');
	if (!defined('SFAVATARPOOLDIR'))   define('SFAVATARPOOLDIR',   SF_STORE_DIR . '/'.$SFPATHS['avatar-pool'].'/');
	if (!defined('SFSMILEYS'))         define('SFSMILEYS',		   SF_STORE_URL . '/'.$SFPATHS['smileys'].'/');
	if (!defined('SFRANKS'))           define('SFRANKS',		   SF_STORE_URL . '/'.$SFPATHS['ranks'].'/');
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# Location of scripts
	# ------------------------------------------------------------------
	if (!defined('SFJSCRIPT')) define('SFJSCRIPT',     				SF_PLUGIN_URL . '/resources/jscript/');
	if (!defined('SFWPJSCRIPT')) define('SFWPJSCRIPT',				SFSITEURL.'wp-includes/js/jquery/');
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# General paths and locations
	# ------------------------------------------------------------------
	$SFSTYLE=array();
	$SFSTYLE=sf_get_option('sfstyle');
	$SFICONPATH = $SFSTYLE['sficon'];
	if(empty($SFICONPATH)) $SFICONPATH='default';

	if (!defined('SFRESOURCES')) define('SFRESOURCES',   SF_STORE_URL . '/'.$SFPATHS['styles'].'/icons/'.$SFICONPATH.'/');

	if (!defined('SFGROUPS'))          define('SFGROUPS',      	     SF_PREFIX.'sfgroups');
	if (!defined('SFFORUMS'))          define('SFFORUMS',      	     SF_PREFIX.'sfforums');
	if (!defined('SFTOPICS'))          define('SFTOPICS',    	  	 SF_PREFIX.'sftopics');
	if (!defined('SFPOSTS'))           define('SFPOSTS',     	  	 SF_PREFIX.'sfposts');
	if (!defined('SFMESSAGES'))        define('SFMESSAGES',  	  	 SF_PREFIX.'sfmessages');
	if (!defined('SFWAITING'))         define('SFWAITING',           SF_PREFIX.'sfwaiting');
	if (!defined('SFTRACK'))           define('SFTRACK',     	  	 SF_PREFIX.'sftrack');
	if (!defined('SFSETTINGS'))        define('SFSETTINGS',    	     SF_PREFIX.'sfsettings');
	if (!defined('SFNOTICE'))          define('SFNOTICE',      	     SF_PREFIX.'sfnotice');
	if (!defined('SFUSERGROUPS'))      define('SFUSERGROUPS',  	     SF_PREFIX.'sfusergroups');
	if (!defined('SFPERMISSIONS'))     define('SFPERMISSIONS', 	     SF_PREFIX.'sfpermissions');
	if (!defined('SFDEFPERMISSIONS')) define('SFDEFPERMISSIONS',     SF_PREFIX.'sfdefpermissions');
	if (!defined('SFROLES'))           define('SFROLES',       	     SF_PREFIX.'sfroles');
	if (!defined('SFMEMBERS'))         define('SFMEMBERS',     	     SF_PREFIX.'sfmembers');
	if (!defined('SFMEMBERSHIPS'))     define('SFMEMBERSHIPS',       SF_PREFIX.'sfmemberships');
	if (!defined('SFMETA'))            define('SFMETA', 	    	 SF_PREFIX.'sfmeta');
	if (!defined('SFPOSTRATINGS'))     define('SFPOSTRATINGS', 	     SF_PREFIX.'sfpostratings');
	if (!defined('SFTAGS'))            define('SFTAGS',	 		     SF_PREFIX.'sftags');
	if (!defined('SFTAGMETA'))         define('SFTAGMETA',	 		 SF_PREFIX.'sftagmeta');
	if (!defined('SFLOG'))             define('SFLOG',				 SF_PREFIX.'sflog');
	if (!defined('SFOPTIONS'))         define('SFOPTIONS',		   	 SF_PREFIX.'sfoptions');
	if (!defined('SFLINKS'))           define('SFLINKS',			 SF_PREFIX.'sflinks');

	if (defined('CUSTOM_USER_TABLE')) {
		if (!defined('SFUSERS')) define('SFUSERS',		CUSTOM_USER_TABLE);
	} else {
		if (!defined('SFUSERS')) define('SFUSERS',		$wpdb->users);
	}
	if (defined('CUSTOM_USER_META_TABLE')) {
		if (!defined('SFUSERMETA')) define('SFUSERMETA',	CUSTOM_USER_META_TABLE);
	} else {
		if (!defined('SFUSERMETA')) define('SFUSERMETA',	$wpdb->usermeta);
	}

	if (!defined('SFDATES')) define('SFDATES',       sf_get_option('sfdates'));
	if (!defined('SFTIMES')) define('SFTIMES',       sf_get_option('sftimes'));

	if (!defined('SFREGPOLICY'))   define('SFREGPOLICY',	SFURL.'policy');

	# ------------------------------------------------------------------
	# editor defs
	# ------------------------------------------------------------------
	if (!defined('RICHTEXT')) define('RICHTEXT',  1);
	if (!defined('HTML'))     define('HTML',	  2);
	if (!defined('BBCODE'))   define('BBCODE',	  3);
	if (!defined('PLAIN'))    define('PLAIN',	  4);
	# ------------------------------------------------------------------
}

# ----------------------------------------------------------
# FORUM GLOBAL Constants - forum page loads (front & back end)
# ----------------------------------------------------------
function sf_setup_global_constants()
{
	global $SFPATHS;

	if (!defined('SFADMINURL'))    define('SFADMINURL',    SF_PLUGIN_URL . '/admin/');
	if (!defined('SFCUSTOM'))      define('SFCUSTOM',      SF_STORE_DIR . '/'.$SFPATHS['custom-icons'].'/');
	if (!defined('SFCUSTOMURL'))   define('SFCUSTOMURL',   SF_STORE_URL . '/'.$SFPATHS['custom-icons'].'/');
	if (!defined('SFADMINIMAGES')) define('SFADMINIMAGES', SF_PLUGIN_URL . '/resources/images/');
	if (!defined('SFADMINCSS'))    define('SFADMINCSS',	   SF_PLUGIN_URL . '/resources/css/');

	# ------------------------------------------------------------------
	# Profile and PM CSS location
	# ------------------------------------------------------------------
	$SFSTYLE = array();
	$SFSTYLE = sf_get_option('sfstyle');
	$SFCSSPATH = $SFSTYLE['sfskin'];
	if(empty($SFCSSPATH)) $SFCSSPATH='default';
    $src = '';
    if (isset($SFSTYLE['sfcsssrc']) && $SFSTYLE['sfcsssrc']==true) $src = '-src';
	if (!defined('SFPROFILEOPOPUPCSS'))  define('SFPROFILEOPOPUPCSS',  SF_STORE_DIR . '/'.$SFPATHS['styles'].'/skins/'.$SFCSSPATH.'/sf-profile'.$src.'.css');
	if (!defined('SFPROFILECSS'))        define('SFPROFILECSS',  SF_STORE_URL . '/'.$SFPATHS['styles'].'/skins/'.$SFCSSPATH.'/sf-profile'.$src.'.css');
	if (!defined('SFPMCSS'))             define('SFPMCSS',	   SF_STORE_URL . '/'.$SFPATHS['styles'].'/skins/'.$SFCSSPATH.'/sf-pm'.$src.'.css');
	if (!defined('SFPOSTCSS'))           define('SFPOSTCSS',	   SF_STORE_URL . '/'.$SFPATHS['styles'].'/skins/'.$SFCSSPATH.'/sf-post'.$src.'.css');
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# Relative URL's for autocompleter
	# ------------------------------------------------------------------
	if (!defined('SFAUTOCOMP')) define('SFAUTOCOMP',		SFHOMEURL."index.php?sf_ahah=pm-manage");
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# Relative URL's for Uploadify
	# ------------------------------------------------------------------
	if (!defined('SFUPLOADER')) define('SFUPLOADER',		SFHOMEURL."index.php?sf_ahah=uploader");
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# Charset
	# ------------------------------------------------------------------
	if (!defined('SFCHARSET')) define('SFCHARSET', get_bloginfo('charset'));
	# ------------------------------------------------------------------
}

# ----------------------------------------------------------
# ADMIN Constants - all page loads
# ----------------------------------------------------------
function sf_setup_admin_constants()
{
	global $SFPATHS;
	# ------------------------------------------------------------------
	# Base admin panels
	# ------------------------------------------------------------------
	if (!defined('SFADMINFORUM'))      define('SFADMINFORUM',		admin_url('admin.php?page=simple-forum/admin/panel-forums/sfa-forums.php'));
	if (!defined('SFADMINOPTION'))     define('SFADMINOPTION',		admin_url('admin.php?page=simple-forum/admin/panel-options/sfa-options.php'));
	if (!defined('SFADMINCOMPONENT'))  define('SFADMINCOMPONENT',	admin_url('admin.php?page=simple-forum/admin/panel-components/sfa-components.php'));
	if (!defined('SFADMINUSERGROUP'))  define('SFADMINUSERGROUP',	admin_url('admin.php?page=simple-forum/admin/panel-usergroups/sfa-usergroups.php'));
	if (!defined('SFADMINPERMISSION')) define('SFADMINPERMISSION',	admin_url('admin.php?page=simple-forum/admin/panel-permissions/sfa-permissions.php'));
	if (!defined('SFADMINUSER'))       define('SFADMINUSER',		admin_url('admin.php?page=simple-forum/admin/panel-users/sfa-users.php'));
	if (!defined('SFADMINPROFILE'))    define('SFADMINPROFILE',	    admin_url('admin.php?page=simple-forum/admin/panel-profiles/sfa-profiles.php'));
	if (!defined('SFADMINADMIN'))      define('SFADMINADMIN',		admin_url('admin.php?page=simple-forum/admin/panel-admins/sfa-admins.php'));
	if (!defined('SFADMINTAGS'))       define('SFADMINTAGS',	    admin_url('admin.php?page=simple-forum/admin/panel-tags/sfa-tags.php'));
	if (!defined('SFADMINTOOLBOX'))    define('SFADMINTOOLBOX',	    admin_url('admin.php?page=simple-forum/admin/panel-toolbox/sfa-toolbox.php'));
	if (!defined('SFADMINCONFIG'))     define('SFADMINCONFIG',		admin_url('admin.php?page=simple-forum/admin/panel-config/sfa-config.php'));
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# Install and Upgrade
	# ------------------------------------------------------------------
	if (!defined('SFINSTALL')) define('SFINSTALL',	SF_PLUGIN_DIR . '/sf-loader-install.php');
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# Popup Help
	# ------------------------------------------------------------------
	if (!defined('SFHELP')) define('SFHELP',       SF_STORE_DIR . '/'.$SFPATHS['help'].'/');
	# ------------------------------------------------------------------
}

# ----------------------------------------------------------
# FORUM Constants - all page loads
# ----------------------------------------------------------
function sf_setup_forum_constants()
{
	global $wpdb, $SFPATHS, $SFSTATUS;
	# ------------------------------------------------------------------
	# General paths and locations
	# ------------------------------------------------------------------
	$SFSTYLE = array();
	$SFSTYLE = sf_get_option('sfstyle');
	$SFCSSPATH = $SFSTYLE['sfskin'];
	if (empty($SFCSSPATH)) $SFCSSPATH='default';
    $src = '';
    if (isset($SFSTYLE['sfcsssrc']) && $SFSTYLE['sfcsssrc']==true) $src = '-src';

	if (!defined('SFSKINCSS'))     define('SFSKINCSS',		SF_STORE_URL . '/'.$SFPATHS['styles'].'/skins/'.$SFCSSPATH.'/'.$SFCSSPATH.$src.'.css');
	if (!defined('SFCSSRTL'))	   define('SFCSSRTL',		SF_STORE_URL . '/'.$SFPATHS['styles'].'/skins/sf-forum-rtl.css');
	if (!defined('SFEDITORDIR'))   define('SFEDITORDIR',	SF_PLUGIN_DIR . '/editors/');
	if (!defined('SFEDITORURL'))   define('SFEDITORURL',	SF_PLUGIN_URL . '/editors/');
	if (!defined('SFEDSTYLE'))     define('SFEDSTYLE',		SF_STORE_URL . '/'.$SFPATHS['styles'].'/editors/');
	if (!defined('SFSIZE'))        define('SFSIZE',		    $SFSTYLE['sfsize']);

	if (!defined('SFMEMBERLIST'))  define('SFMEMBERLIST',   SFURL.'members/');
	if (!defined('SFMARKREAD'))    define('SFMARKREAD',   	sf_build_qurl('sf-mark-read'));


	if (!defined('SFFMDIR'))   define('SFFMDIR',	SF_PLUGIN_DIR . '/editors/tinymce/plugins/filemanager/');
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# hack to get around wp_list_pages() bug
	# ------------------------------------------------------------------
	if($SFSTATUS == 'ok')
	{
		$wpdb->hide_errors();
		$t = $wpdb->get_var("SELECT post_title FROM ".$wpdb->prefix."posts WHERE ID=".sf_get_option('sfpage'));
		if (!defined('SFPAGETITLE')) define('SFPAGETITLE', $t);
		$wpdb->show_errors();
	}
	# ------------------------------------------------------------------
}

?>