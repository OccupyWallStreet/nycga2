<?php
/*
Simple:Press
Upgrade Path Routines
$LastChangedDate: 2011-06-05 09:16:54 -0700 (Sun, 05 Jun 2011) $
$Rev: 6253 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

global $wpdb, $current_user;

$InstallID = get_option('sfInstallID'); # use wp option table
wp_set_current_user($InstallID);

# use WP check here since SPF stuff may not be set up
if(!current_user_can('activate_plugins'))
{
    echo (__('Access Denied - Only Users who can Activate Plugins may perform this upgrade', 'sforum'));
    die();
}

sf_setup_sitewide_constants();
sf_setup_global_constants();

require_once (dirname(__FILE__).'/sf-upgrade-support.php');
require_once (dirname(__FILE__).'/../admin/library/sfa-support.php');

if(!isset($_GET['start'])) die();

$checkval = $_GET['start'];
$build = intval($checkval);

# Start of Upgrade Routines =============

	$section = 200;
	if($build < $section)
	{
		# 1.2 =====================================================================================
		add_option('sfsortdesc', false);

		# 1.3 =====================================================================================
		add_option('sfavatars', true);
		add_option('sfshownewadmin', true);
		add_option('sfshownewuser', true);
		add_option('sfshownewcount', 6);
		add_option('sfdates', get_option('date_format'));
		add_option('sftimes', get_option('time_format'));
		add_option('sfzone', 0);

		$create_ddl = "ALTER TABLE ".SFFORUMS. " ADD (forum_desc varchar(150) default NULL)";
		sf_upgrade_database(SFFORUMS, 'forum_desc', $create_ddl);

		# 1.4 =====================================================================================
		add_option('sfshowavatars', true);
		add_option('sfuserabove', false);
		add_option('sfrte', true);
		add_option('sfskin', 'default');
		add_option('sficon', 'default');

		# 1.6 =====================================================================================
		$create_ddl = "ALTER TABLE ".SFFORUMS. " ADD (forum_status int(4) NOT NULL default '0')";
		sf_upgrade_database(SFFORUMS, 'forum_status', $create_ddl);

		$create_ddl = "ALTER TABLE ".SFPOSTS. " ADD (post_pinned smallint(1) NOT NULL default '0')";
		sf_upgrade_database(SFPOSTS, 'post_pinned', $create_ddl);

		$postusers = $wpdb->get_results("SELECT user_id, COUNT(post_id) AS numposts FROM ".SFPOSTS." WHERE user_id IS NOT NULL GROUP BY user_id");
		if($postusers)
		{
			foreach($postusers as $postuser)
			{
				update_user_option($postuser->user_id, 'sfposts', $postuser->numposts);
			}
		}

		add_option('sfstopedit', true);
		add_option('sfmodmembers', false);
		add_option('sfmodusers', '');
		add_option('sftopicsort', false);

		sf_check_data_integrity();

		# 1.7 =====================================================================================
		$create_ddl = "ALTER TABLE ".SFTOPICS. " ADD (topic_subs longtext)";
		sf_upgrade_database(SFTOPICS, 'topic_subs', $create_ddl);

		sf_rebuild_subscriptions();

		add_option('sfavatarsize', 50);

		delete_option('sffilters');
		delete_option('sfrte');

		$create_ddl = "ALTER TABLE ".SFGROUPS. " ADD (group_desc varchar(150) default NULL)";
		sf_upgrade_database(SFGROUPS, 'group_desc', $create_ddl);

		$create_ddl = "ALTER TABLE ".SFGROUPS. " ADD (group_view varchar(20) default 'public')";
		sf_upgrade_database(SFGROUPS, 'group_view', $create_ddl);

		$create_ddl = "ALTER TABLE ".SFFORUMS. " ADD (forum_view varchar(20) default 'public')";
		sf_upgrade_database(SFFORUMS, 'forum_view', $create_ddl);

		# 1.8 =====================================================================================
		$create_ddl = "ALTER TABLE ".SFTOPICS. " ADD (topic_sort varchar(4) default NULL)";
		sf_upgrade_database(SFTOPICS, 'topic_sort', $create_ddl);

		add_option('sfspam', true);
		add_option('sfpermalink', get_permalink(get_option('sfpage')));
		add_option('sfextprofile', true);
		add_option('sfusersig', true);
		add_option('sfhome', SFHOMEURL);

		# 1.9 =====================================================================================
		$create_ddl = "ALTER TABLE ".SFTOPICS. " ADD (topic_opened bigint(20) NOT NULL default '0')";
		sf_upgrade_database(SFTOPICS, 'topic_opened', $create_ddl);

		$icons='Login;1@Register;1@Logout;1@Profile;1@Add a New Topic;1@Forum Locked;1@Reply to Post;1@Topic Locked;1@Quote and Reply;1@Edit Your Post;1@Return to Search Results;1@Subscribe;1@Forum RSS;1@Topic RSS;1';
		update_option('sfshowicon', $icons);

		add_option('sfrss', true);
		add_option('sfrsscount', 15);
		add_option('sfrsswords', 0);
		add_option('sfpagedposts', 20);
		add_option('sfgravatar', false);
		add_option('sfmodonce', false);
		add_option('sftitle', true);
		add_option('sflang', 'en');

		$fcols['topics']=true;
		$fcols['posts']=true;
		add_option('sfforumcols', $fcols);

		$tcols['first']=true;
		$tcols['last']=true;
		$tcols['posts']=true;
		$tcols['views']=true;
		add_option('sftopiccols', $tcols);

		$sql = "
		CREATE TABLE IF NOT EXISTS ".SFTRACK." (
			id bigint(20) NOT NULL auto_increment,
			trackuserid bigint(20) default 0,
			trackname varchar(25) NOT NULL,
			trackdate datetime NOT NULL,
			PRIMARY KEY (id)
		) ENGINE=MyISAM ".sf_charset().";";
		$wpdb->query($sql);

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 220;
	if($build < $section)
	{
		# 2.0 =====================================================================================
		$create_ddl = "ALTER TABLE ".SFWAITING. " ADD (post_id bigint(20) NOT NULL default '0')";
		sf_upgrade_database(SFWAITING, 'post_id', $create_ddl);

		$sql = "ALTER TABLE ".SFTRACK." MODIFY trackname VARCHAR(50) NOT NULL;";
		$wpdb->query($sql);

		sf_clean_topic_subs();

		$icons=sf_opcheck(get_option('sfshowicon'));
		if(strpos($icons, '@New Posts;') === false)
		{
			$icons.= '@All RSS;1@Search;1@New Posts;1';
			update_option('sfshowicon', $icons);
		}

		$sql = "
			CREATE TABLE IF NOT EXISTS ".SFSETTINGS." (
				setting_id bigint(20) NOT NULL auto_increment,
				setting_name varchar(20) NOT NULL,
				setting_value longtext,
				PRIMARY KEY (setting_id)
		) ENGINE=MyISAM ".sf_charset().";";
		$wpdb->query($sql);

		$sql = "
			CREATE TABLE IF NOT EXISTS ".SFNOTICE." (
				id varchar(30) NOT NULL,
				item varchar(15),
				message longtext,
				PRIMARY KEY (id)
		) ENGINE=MyISAM ".sf_charset().";";
		$wpdb->query($sql);

		delete_option('sfsearch');
		delete_option('sfaction');
		delete_option('sfppage');
		delete_option('sftpage');
		delete_option('sfmessage');

		add_option('sfstats', true);
		add_option('sfshownewabove', false);
		add_option('sfshowlogin', true);

		$avatar = sf_relocate_avatars();
		if($avatar != 0)
		{
			add_option('sfinstallav', $avatar);
		}

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 228;
	if($build < $section)
	{
		# 2.1 =====================================================================================
		sf_correct_sflast();

		$wpdb->query("DELETE FROM ".SFSETTINGS." WHERE setting_name <> 'maxonline';");
		$wpdb->query("ALTER TABLE ".SFSETTINGS." MODIFY setting_name VARCHAR(50) NOT NULL;");

		$create_ddl = "ALTER TABLE ".SFSETTINGS." ADD (setting_date datetime NOT NULL);";
		sf_upgrade_database(SFSETTINGS, 'setting_date', $create_ddl);

		$create_ddl = "ALTER TABLE ".SFNOTICE." ADD (ndate datetime NOT NULL);";
		sf_upgrade_database(SFNOTICE, 'ndate', $create_ddl);

		$create_ddl = "ALTER TABLE ".SFWAITING." ADD (user_id bigint(20) default 0);";
		sf_upgrade_database(SFWAITING, 'user_id', $create_ddl);

		$wpdb->query("ALTER TABLE ".SFFORUMS." ADD INDEX groupf_idx (group_id);");
		$wpdb->query("ALTER TABLE ".SFPOSTS." ADD INDEX topicp_idx (topic_id);");
		$wpdb->query("ALTER TABLE ".SFPOSTS." ADD INDEX forump_idx (forum_id);");
		$wpdb->query("ALTER TABLE ".SFTOPICS." ADD INDEX forumt_idx (forum_id);");

		add_option('sfregmath', true);
		add_option('sfsearchbar', true);
		add_option('sfadminspam', true);
		add_option('sfshowhome', true);
		add_option('sflockdown', false);
		add_option('sfshowmodposts', true);

		# Links
		$create_ddl = "ALTER TABLE ".SFTOPICS. " ADD (blog_post_id bigint(20) NOT NULL default '0')";
		sf_upgrade_database(SFTOPICS, 'blog_post_id', $create_ddl);

		add_option('sflinkuse', false);
		add_option('sflinkexcerpt', false);
		add_option('sflinkwords', 100);
		add_option('sflinkblogtext', '%ICON% Join the forum discussion on this post');
		add_option('sflinkforumtext', '%ICON% Read original blog post');
		add_option('sflinkabove', false);

		# Announce Tag
		add_option('sfuseannounce', false);
		add_option('sfannouncecount', 8);
		add_option('sfannouncehead', 'Most Recent Forum Posts');
		add_option('sfannounceauto', false);
		add_option('sfannouncetime', 60);
		add_option('sfannouncetext', '%TOPICNAME% posted by %POSTER% in %FORUMNAME% on %DATETIME%');
		add_option('sfannouncelist', false);

		# Rankings
		$ranks=array('New Member' => 2, 'Member' => 1000);
		add_option('sfrankings', $ranks);

		$icons=sf_opcheck(get_option('sfshowicon'));
		if(strpos($icons, '@Group RSS;') === false)
		{
			$icons.= '@Group RSS;1';
			update_option('sfshowicon', $icons);
		}

		# New since build 225
		$cols=sf_opcheck(get_option('sfforumcols'));
		$cols['last'] = false;
		update_option('sfforumcols', $cols);

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 250;
	if($build < $section)
	{
		# 3.0 =====================================================================================
		# Pre-create last visit dates for all existing users who don't have one
		sf_precreate_sflast();

		$sql = "
			CREATE TABLE IF NOT EXISTS ".SFMESSAGES." (
				message_id bigint(20) NOT NULL auto_increment,
				sent_date datetime NOT NULL,
				from_id bigint(20) default NULL,
				to_id bigint(20) default NULL,
				title text,
				message text,
				message_status smallint(1) NOT NULL default '0',
				inbox smallint(1) NOT NULL default '1',
				sentbox smallint(1) NOT NULL default '1',
				is_reply smallint(1) NOT NULL default '0',
				PRIMARY KEY (message_id)
			) ENGINE=MyISAM ".sf_charset().";";
		$wpdb->query($sql);

		# Slugs
		$create_ddl = "ALTER TABLE ".SFFORUMS. " ADD (forum_slug varchar(85) NOT NULL)";
		sf_upgrade_database(SFFORUMS, 'forum_slug', $create_ddl);

		$create_ddl = "ALTER TABLE ".SFTOPICS. " ADD (topic_slug varchar(110) NOT NULL)";
		sf_upgrade_database(SFTOPICS, 'topic_slug', $create_ddl);

		sf_create_slugs();

		add_option('sfimgenlarge', false);
		add_option('sfthumbsize', 100);
		add_option('sfmodasadmin', false);
		add_option('sfmemberspam', true);
		add_option('sfuppath', '');

		# email option array
		$adminname = sf_opcheck(get_user_meta($current_user->ID, 'first_name', true));
		$sfmail = array();
		$sfmail['sfmailsender'] = get_bloginfo('name');
		$sfmail['sfmailfrom'] = str_replace(' ', '', $adminname);
		$sfmail['sfmaildomain'] = preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));
		add_option('sfmail', $sfmail);

		# new user email array
		$sfmail = array();
		$sfmail['sfnewusersubject'] = 'Welcome to %BLOGNAME%';
		$sfmail['sfnewusertext'] = 'Welcome %USERNAME% to %BLOGNAME% %NEWLINE%Please find below your login details: %NEWLINE%Username: %USERNAME% %NEWLINE%Password: %PASSWORD% %NEWLINE%%LOGINURL%';
		add_option('sfnewusermail', $sfmail);

		$icons=sf_opcheck(get_option('sfshowicon'));
		if(strpos($icons, '@Send PM;') === false)
		{
			$icons.= '@Send PM;1@Return to forum;1@Compose PM;1@Go To Inbox;1@Go To Sentbox;1@Report Post;1';
			update_option('sfshowicon', $icons);
		}

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 255;
	if($build < $section)
	{
		# Change usermeta values that previously used table prefix
		$oldkeys[0] = $wpdb->prefix.'sfavatar';
		$oldkeys[1] = $wpdb->prefix.'sfposts';
		$oldkeys[2] = $wpdb->prefix.'sflast';
		$oldkeys[3] = $wpdb->prefix.'sfsubscribe';
		$oldkeys[4] = $wpdb->prefix.'sfadmin';
		$newkeys[0] = 'sfavatar';
		$newkeys[1] = 'sfposts';
		$newkeys[2] = 'sflast';
		$newkeys[3] = 'sfsubscribe';
		$newkeys[4] = 'sfadmin';

		for($x=0; $x<count($oldkeys); $x++)
		{
			$sql = "UPDATE ".SFUSERMETA." SET meta_key = '".$newkeys[$x]."' WHERE meta_key = '".$oldkeys[$x]."'";
			$wpdb->query($sql);
		}

		# Create User Groups table
		$sql = "
			CREATE TABLE IF NOT EXISTS ".SFUSERGROUPS." (
				usergroup_id mediumint(8) unsigned NOT NULL auto_increment,
				usergroup_name varchar(50) NOT NULL default '',
				usergroup_desc varchar(150) NOT NULL default '',
				usergroup_locked tinyint(4) unsigned NOT NULL default '0',
				usergroup_is_moderator tinyint(4) unsigned NOT NULL default '0',
				PRIMARY KEY  (usergroup_id)
			) ENGINE=MyISAM ".sf_charset().";";
		$wpdb->query($sql);

		# Create the Permissions table
		$sql = "
			CREATE TABLE IF NOT EXISTS ".SFPERMISSIONS." (
				permission_id mediumint(8) unsigned NOT NULL auto_increment,
				forum_id mediumint(8) unsigned NOT NULL default '0',
				usergroup_id mediumint(8) unsigned NOT NULL default '0',
				permission_role mediumint(8) unsigned NOT NULL default '0',
				PRIMARY KEY  (permission_id)
			) ENGINE=MyISAM ".sf_charset().";";
		$wpdb->query($sql);

		# Create the Roles table
		$sql = "
			CREATE TABLE IF NOT EXISTS ".SFROLES." (
				role_id mediumint(8) unsigned NOT NULL auto_increment,
				role_name varchar(50) NOT NULL default '',
				role_desc varchar(150) NOT NULL default '',
				role_actions longtext NOT NULL,
				PRIMARY KEY  (role_id)
			) ENGINE=MyISAM ".sf_charset().";";
		$wpdb->query($sql);

		# setup an array of info for the data upgrades
		$keys = array();

		# Create default role data
		$actions = array();
		$actions['Can view forum'] = 0;
		$actions['Can start new topics'] = 0;
		$actions['Can reply to topics'] = 0;
		$actions['Can create linked topics'] = 0;
		$actions['Can break linked topics'] = 0;
		$actions['Can edit topic titles'] = 0;
		$actions['Can pin topics'] = 0;
		$actions['Can move topics'] = 0;
		$actions['Can move posts'] = 0;
		$actions['Can lock topics'] = 0;
		$actions['Can delete topics'] = 0;
		$actions['Can edit own posts forever'] = 0;
		$actions['Can edit own posts until reply'] = 0;
		$actions['Can edit any posts'] = 0;
		$actions['Can delete any posts'] = 0;
		$actions['Can pin posts'] = 0;
		$actions['Can view users email addresses'] = 0;
		$actions['Can view members profiles'] = 0;
		$actions['Can report posts'] = 0;
		$actions['Can sort most recent posts'] = 0;
		$actions['Can bypass spam control'] = 0;
		$actions['Can bypass post moderation'] = 0;
		$actions['Can bypass post moderation once'] = 0;
		$actions['Can upload images'] = 0;
		$actions['Can upload media'] = 0;
		$actions['Can upload files'] = 0;
		$actions['Can use signatures'] = 0;
		$actions['Can use images in signatures'] = 0;
		$actions['Can upload avatars'] = 0;
		$actions['Can use private messaging'] = 0;
		$actions['Can subscribe'] = 0;
		$actions['Can moderate pending posts'] = 0;
		$role_name = 'No Access';
		$role_desc = 'Permission with no access to any Forum features.';
		sfa_create_role_row($role_name, $role_desc, serialize($actions));

		$actions = array();
		$actions['Can view forum'] = 1;
		$actions['Can start new topics'] = 0;
		$actions['Can reply to topics'] = 0;
		$actions['Can create linked topics'] = 0;
		$actions['Can break linked topics'] = 0;
		$actions['Can edit topic titles'] = 0;
		$actions['Can pin topics'] = 0;
		$actions['Can move topics'] = 0;
		$actions['Can move posts'] = 0;
		$actions['Can lock topics'] = 0;
		$actions['Can delete topics'] = 0;
		$actions['Can edit own posts forever'] = 0;
		$actions['Can edit own posts until reply'] = 0;
		$actions['Can edit any posts'] = 0;
		$actions['Can delete any posts'] = 0;
		$actions['Can pin posts'] = 0;
		$actions['Can view users email addresses'] = 0;
		$actions['Can view members profiles'] = 0;
		$actions['Can report posts'] = 0;
		$actions['Can sort most recent posts'] = 0;
		$actions['Can bypass spam control'] = 0;
		$actions['Can bypass post moderation'] = 0;
		$actions['Can bypass post moderation once'] = 0;
		$actions['Can upload images'] = 0;
		$actions['Can upload media'] = 0;
		$actions['Can upload files'] = 0;
		$actions['Can use signatures'] = 0;
		$actions['Can use images in signatures'] = 0;
		$actions['Can upload avatars'] = 0;
		$actions['Can use private messaging'] = 0;
		$actions['Can subscribe'] = 0;
		$actions['Can moderate pending posts'] = 0;
		$role_name = 'Read Only Access';
		$role_desc = 'Permission with access to only view the Forum.';
		sfa_create_role_row($role_name, $role_desc, serialize($actions));

		$actions = array();
		$actions['Can view forum'] = 1;
		$actions['Can start new topics'] = 1;
		$actions['Can reply to topics'] = 1;
		$actions['Can create linked topics'] = 0;
		$actions['Can break linked topics'] = 0;
		$actions['Can edit topic titles'] = 0;
		$actions['Can pin topics'] = 0;
		$actions['Can move topics'] = 0;
		$actions['Can move posts'] = 0;
		$actions['Can lock topics'] = 0;
		$actions['Can delete topics'] = 0;
		$actions['Can edit own posts forever'] = 0;
		$actions['Can edit own posts until reply'] = 1;
		$actions['Can edit any posts'] = 0;
		$actions['Can delete any posts'] = 0;
		$actions['Can pin posts'] = 0;
		$actions['Can view users email addresses'] = 0;
		$actions['Can view members profiles'] = 1;
		$actions['Can report posts'] = 1;
		$actions['Can sort most recent posts'] = 0;
		$actions['Can bypass spam control'] = 0;
		$actions['Can bypass post moderation'] = 0;
		$actions['Can bypass post moderation once'] = 0;
		$actions['Can upload images'] = 0;
		$actions['Can upload media'] = 0;
		$actions['Can upload files'] = 0;
		$actions['Can use signatures'] = 0;
		$actions['Can use images in signatures'] = 0;
		$actions['Can upload avatars'] = 1;
		$actions['Can use private messaging'] = 0;
		$actions['Can subscribe'] = 1;
		$actions['Can moderate pending posts'] = 0;
		$role_name = 'Limited Access';
		$role_desc = 'Permission with access to reply and start topics but with limited features.';
		sfa_create_role_row($role_name, $role_desc, serialize($actions));

		if(sf_opcheck(get_option('sfallowguests'))) $roleid = $wpdb->insert_id;

		# Create default 'Guest' user group data
		$guests = sfa_create_usergroup_row('Guests', 'Default Usergroup for guests of the forum.', '0', false);

		$keys[0]['usergroup'] = $wpdb->insert_id;
		$keys[0]['permission'] = $roleid;

		$actions = array();
		$actions['Can view forum'] = 1;
		$actions['Can start new topics'] = 1;
		$actions['Can reply to topics'] = 1;
		$actions['Can create linked topics'] = 0;
		$actions['Can break linked topics'] = 0;
		$actions['Can edit topic titles'] = 0;
		$actions['Can pin topics'] = 0;
		$actions['Can move topics'] = 0;
		$actions['Can move posts'] = 0;
		$actions['Can lock topics'] = 0;
		$actions['Can delete topics'] = 0;
		$actions['Can edit own posts forever'] = 0;
		$actions['Can edit own posts until reply'] = 1;
		$actions['Can edit any posts'] = 0;
		$actions['Can delete any posts'] = 0;
		$actions['Can pin posts'] = 0;
		$actions['Can view users email addresses'] = 0;
		$actions['Can view members profiles'] = 1;
		$actions['Can report posts'] = 1;
		$actions['Can sort most recent posts'] = 0;
		$actions['Can bypass spam control'] = 0;
		$actions['Can bypass post moderation'] = 1;
		$actions['Can bypass post moderation once'] = 1;
		$actions['Can upload images'] = 0;
		$actions['Can upload media'] = 0;
		$actions['Can upload files'] = 0;
		$actions['Can use signatures'] = 1;
		$actions['Can use images in signatures'] = 1;
		$actions['Can upload avatars'] = 1;
		$actions['Can use private messaging'] = 1;
		$actions['Can subscribe'] = 1;
		$actions['Can moderate pending posts'] = 0;
		$role_name = 'Standard Access';
		$role_desc = 'Permission with access to reply and start topics with advanced features such as signatures and private messaging.';
		sfa_create_role_row($role_name, $role_desc, serialize($actions));

		$roleid = $wpdb->insert_id;

		# Create default 'Members' user group data
		$members = sfa_create_usergroup_row('Members', 'Default Usergroup for registered users of the forum.', '0', false);

		$keys[1]['usergroup'] = $members;
		$keys[1]['permission'] = $roleid;

		$actions = array();
		$actions['Can view forum'] = 1;
		$actions['Can start new topics'] = 1;
		$actions['Can reply to topics'] = 1;
		$actions['Can create linked topics'] = 0;
		$actions['Can break linked topics'] = 0;
		$actions['Can edit topic titles'] = 0;
		$actions['Can pin topics'] = 0;
		$actions['Can move topics'] = 0;
		$actions['Can move posts'] = 0;
		$actions['Can lock topics'] = 0;
		$actions['Can delete topics'] = 0;
		$actions['Can edit own posts forever'] = 1;
		$actions['Can edit own posts until reply'] = 1;
		$actions['Can edit any posts'] = 0;
		$actions['Can delete any posts'] = 0;
		$actions['Can pin posts'] = 0;
		$actions['Can view users email addresses'] = 0;
		$actions['Can view members profiles'] = 1;
		$actions['Can report posts'] = 1;
		$actions['Can sort most recent posts'] = 0;
		$actions['Can bypass spam control'] = 1;
		$actions['Can bypass post moderation'] = 1;
		$actions['Can bypass post moderation once'] = 1;
		$actions['Can upload images'] = 1;
		$actions['Can upload media'] = 1;
		$actions['Can upload files'] = 1;
		$actions['Can use signatures'] = 1;
		$actions['Can use images in signatures'] = 1;
		$actions['Can upload avatars'] = 1;
		$actions['Can use private messaging'] = 1;
		$actions['Can subscribe'] = 1;
		$actions['Can moderate pending posts'] = 0;
		$role_name = 'Full Access';
		$role_desc = 'Permission with Standard Access features plus image uploading and spam control bypass.';
		sfa_create_role_row($role_name, $role_desc, serialize($actions));

		$actions = array();
		$actions['Can view forum'] = 1;
		$actions['Can start new topics'] = 1;
		$actions['Can reply to topics'] = 1;
		$actions['Can create linked topics'] = 1;
		$actions['Can break linked topics'] = 1;
		$actions['Can edit topic titles'] = 1;
		$actions['Can pin topics'] = 1;
		$actions['Can move topics'] = 1;
		$actions['Can move posts'] = 1;
		$actions['Can lock topics'] = 1;
		$actions['Can delete topics'] = 1;
		$actions['Can edit own posts forever'] = 1;
		$actions['Can edit own posts until reply'] = 1;
		$actions['Can edit any posts'] = 1;
		$actions['Can delete any posts'] = 1;
		$actions['Can pin posts'] = 1;
		$actions['Can view users email addresses'] = 1;
		$actions['Can view members profiles'] = 1;
		$actions['Can report posts'] = 1;
		$actions['Can sort most recent posts'] = 1;
		$actions['Can bypass spam control'] = 1;
		$actions['Can bypass post moderation'] = 1;
		$actions['Can bypass post moderation once'] = 1;
		$actions['Can upload images'] = 1;
		$actions['Can upload media'] = 1;
		$actions['Can upload files'] = 1;
		$actions['Can use signatures'] = 1;
		$actions['Can use images in signatures'] = 1;
		$actions['Can upload avatars'] = 1;
		$actions['Can use private messaging'] = 1;
		$actions['Can subscribe'] = 1;
		$actions['Can moderate pending posts'] = 1;
		$role_name = 'Moderator Access';
		$role_desc = 'Permission with access to all Forum features.';
		sfa_create_role_row($role_name, $role_desc, serialize($actions));

		$roleid = $wpdb->insert_id;

		# Create default 'Moderators' user group data
		$moderators = sfa_create_usergroup_row('Moderators', 'Default Usergroup for moderators of the forum.', '1', false);

		$keys[2]['usergroup'] = $moderators;
		$keys[2]['permission'] = $roleid;

		# ensure all users have a display name set
		sf_check_all_display_names();

		# set up the current userbase into default groups etc
		sf_setup_usergroup_data($members, $moderators, true, $keys);

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 300;
	if($build < $section)
	{
		$wpdb->query("ALTER TABLE ".SFGROUPS." DROP group_view;");
		$wpdb->query("ALTER TABLE ".SFFORUMS." DROP forum_view;");

		add_option('sfdefgroup', $members);
		add_option('sfbadwords', '');
		add_option('sfreplacementwords', '');
		add_option('sfpaging', 4);
		add_option('sfadminbar', true);

		# change sftitle
		$sftitle = array();
		$sftitle['sfinclude'] = sf_opcheck(get_option('sftitle'));
		$sftitle['sfnotitle'] = false;
		$sftitle['sfbanner'] = '';
		update_option('sftitle', $sftitle);

		$pm = array();
		$pm['sfpmemail'] = false;
		$pm['sfpmmax'] = 0;
		add_option('sfpm', $pm);

		$sfquicklinks = array();
		$sfquicklinks['sfqlshow'] = true;
		$sfquicklinks['sfqlcount'] = 15;
		add_option('sfquicklinks', $sfquicklinks);

		$sfcustom = array();
		$sfcustom[0]['custext']='';
		$sfcustom[0]['cuslink']='';
		$sfcustom[0]['cusicon']='';
		$sfcustom[1]['custext']='';
		$sfcustom[1]['cuslink']='';
		$sfcustom[1]['cusicon']='';
		$sfcustom[2]['custext']='';
		$sfcustom[2]['cuslink']='';
		$sfcustom[2]['cusicon']='';
		add_option('sfcustom', $sfcustom);

		# remove unwanted options
		delete_option('sfmodusers');
		delete_option('sfsubscriptions');
		delete_option('sfusersig');
		delete_option('sfstopedit');
		delete_option('sfmoderate');
		delete_option('sfmodonce');
		delete_option('sfavatars');
		delete_option('sfspam');
		delete_option('sflinkuse');
		delete_option('sfmodmembers');
		delete_option('sfadminspam');
		delete_option('sfmemberspam');

		$icons=sf_opcheck(get_option('sfshowicon'));
		if(strpos($icons, '@Moderation Queue') === false)
		{
			$icons.= '@Moderation Queue;1';
			update_option('sfshowicon', $icons);
		}

		# RSS feed urls
		$create_ddl = "ALTER TABLE ".SFFORUMS. " ADD (forum_rss text)";
		sf_upgrade_database(SFFORUMS, 'forum_rss', $create_ddl);

		$create_ddl = "ALTER TABLE ".SFGROUPS. " ADD (group_rss text)";
		sf_upgrade_database(SFGROUPS, 'group_rss', $create_ddl);

		# RSS feed urls
		$create_ddl = "ALTER TABLE ".SFFORUMS. " ADD (forum_icon varchar(25) default NULL)";
		sf_upgrade_database(SFFORUMS, 'forum_icon', $create_ddl);

		$create_ddl = "ALTER TABLE ".SFGROUPS. " ADD (group_icon varchar(25) default NULL)";
		sf_upgrade_database(SFGROUPS, 'group_icon', $create_ddl);

		# custom message for editor
		$sfpostmsg = array();
		$sfpostmsg['sfpostmsgtext'] = '';
		$sfpostmsg['sfpostmsgtopic'] = false;
		$sfpostmsg['sfpostmsgpost'] = false;
		update_option('sfpostmsg', $sfpostmsg);

		add_option('sfeditormsg','');
		add_option('sfautoupdate');
		add_option('sfcheck', true);

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 320;
	if($build < $section)
	{
		# 3.0.3 =====================================================================================
		# extra icons
		$icons=sf_opcheck(get_option('sfshowicon'));
		if(strpos($icons, '@Lock this Topic;') === false)
		{
			$icons.= '@Lock this Topic;1@Pin this Topic;1@Create Linked Post;1@Pin this Post;1@Edit Timestamp;1';
			update_option('sfshowicon', $icons);
		}

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 321;
	if($build < $section)
	{
		# 3.1  =====================================================================================
		# post id added to forum and topic tables
		$create_ddl = "ALTER TABLE ".SFFORUMS. " ADD (post_id bigint(20) default NULL)";
		sf_upgrade_database(SFFORUMS, 'post_id', $create_ddl);

		$create_ddl = "ALTER TABLE ".SFFORUMS. " ADD (topic_count mediumint(8) default '0')";
		sf_upgrade_database(SFFORUMS, 'topic_count', $create_ddl);

		$create_ddl = "ALTER TABLE ".SFTOPICS. " ADD (post_id bigint(20) default NULL)";
		sf_upgrade_database(SFTOPICS, 'post_id', $create_ddl);

		$create_ddl = "ALTER TABLE ".SFTOPICS. " ADD (post_count mediumint(8) default '0')";
		sf_upgrade_database(SFTOPICS, 'post_count', $create_ddl);

		$create_ddl = "ALTER TABLE ".SFPOSTS. " ADD (post_index mediumint(8) default '0')";
		sf_upgrade_database(SFPOSTS, 'post_index', $create_ddl);

		sf_build_lastposts();

		# new style array
		$sfstyle = array();
		$sfstyle['sfskin'] = sf_opcheck(get_option('sfskin'));
		$sfstyle['sficon'] = sf_opcheck(get_option('sficon'));
		$sfstyle['sflang'] = sf_opcheck(get_option('sflang'));
		$sfstyle['sfrtl'] = false;
		add_option('sfstyle', $sfstyle);
		delete_option('sfskin');
		delete_option('sficon');
		delete_option('sflang');

		# new login array
		$sflogin = array();
		$sflogin['sfshowlogin'] = sf_opcheck(get_option('sfshowlogin'));
		$sflogin['sfshowreg'] = sf_opcheck(get_option('sfshowlogin'));
		add_option('sflogin', $sflogin);
		delete_option('sfshowlogin');

		$sfadminsettings=array();
		$sfadminsettings['sfnotify']=sf_opcheck(get_option('sfnotify'));
		$sfadminsettings['sfadminbar']=sf_opcheck(get_option('sfadminbar'));
		$sfadminsettings['sfshownewadmin']=sf_opcheck(get_option('sfshownewadmin'));
		$sfadminsettings['sfmodasadmin']=sf_opcheck(get_option('sfmodasadmin'));
		$sfadminsettings['sfshowmodposts']=sf_opcheck(get_option('sfshowmodposts'));
		$sfadminsettings['sftools']=sf_opcheck(get_option('sfedit'));
		$sfadminsettings['sfqueue']=true;
		add_option('sfadminsettings', $sfadminsettings);
		delete_option('sfnotify');
		delete_option('sfadminbar');
		delete_option('sfshownewadmin');
		delete_option('sfmodasadmin');
		delete_option('sfshowmodposts');
		delete_option('sfedit');

		$sfauto=array();
		$sfauto['sfautoupdate']=sf_opcheck(get_option('sfautoupdate'));
		$sfauto['sfautotime']=300;
		add_option('sfauto', $sfauto);
		delete_option('sfautoupdate');

		$sffilters=array();
		$sffilters['sfnofollow']=false;
		$sffilters['sftarget']=true;
		add_option('sffilters', $sffilters);

		add_option('sfshowbreadcrumbs', true);

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 340;
	if($build < $section)
	{
		# sfmembers table def
		$sql = "
			CREATE TABLE IF NOT EXISTS ".SFMEMBERS." (
				user_id bigint(20) NOT NULL default '0',
				display_name varchar(100) default NULL,
				pm smallint(1) NOT NULL default '0',
				moderator smallint(1) NOT NULL default '0',
				quicktags smallint(1) NOT NULL default '0',
				usergroups longtext default NULL,
				avatar varchar(50) default NULL,
				signature tinytext default NULL,
				sigimage tinytext default NULL,
				posts int(4) NOT NULL default '0',
				lastvisit datetime default NULL,
				subscribe longtext,
				buddies longtext,
				newposts longtext,
				checktime datetime default NULL,
				PRIMARY KEY  (user_id)
			) ENGINE=MyISAM ".sf_charset().";";
		$wpdb->query($sql);

		sf_build_members_table('quicktags', 'upgrade');

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 356;
	if($build < $section)
	{
		# 3.1.3  ===================================================================================
		$sql = "UPDATE ".SFUSERGROUPS." SET usergroup_locked = '0'";
		$wpdb->query($sql);

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 360;
	if($build < $section)
	{
		# 4.0  =====================================================================================
		# add new Can view members profiles permission
		sf_upgrade_add_new_role('Can view members profiles', 0, true);
		$create_ddl = "ALTER TABLE ".SFFORUMS. " ADD (forum_rss_private smallint(1) NOT NULL default '0')";
		sf_upgrade_database(SFFORUMS, 'forum_rss_private', $create_ddl);
		$icons=sf_opcheck(get_option('sfshowicon'));
		if(strpos($icons, '@Subscribe to this Topic;') === false)
		{
			$icons.= '@Subscribe to this Topic;1';
			update_option('sfshowicon', $icons);
		}

		if (sf_opcheck(get_option('sfrss')))
		{
			$wpdb->query("UPDATE ".SFFORUMS." SET forum_rss_private=0");
		} else {
			$wpdb->query("UPDATE ".SFFORUMS." SET forum_rss_private=1");
		}
		delete_option('sfrss');

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 365;
	if($build < $section)
	{
		# take existing forum admin and grant all new spf capabilities
		$adminid = sf_opcheck(get_option('sfadmin'));
		$user = new WP_User($adminid);
		$user->add_cap('SPF Manage Options');
		$user->add_cap('SPF Manage Forums');
		$user->add_cap('SPF Manage User Groups');
		$user->add_cap('SPF Manage Permissions');
		$user->add_cap('SPF Manage Database');
		$user->add_cap('SPF Manage Components');
		$user->add_cap('SPF Manage Admins');
		$user->add_cap('SPF Manage Users');

		$create_ddl = "ALTER TABLE ".SFMEMBERS. " ADD (admin smallint(1) NOT NULL default '0')";
		sf_upgrade_database(SFMEMBERS, 'admin', $create_ddl);
		sf_update_member_item($adminid, 'admin', 1);

		delete_option('sfadmin');

		# sfmeta table def
		$sql = "
			CREATE TABLE IF NOT EXISTS ".SFMETA." (
				meta_id bigint(20) NOT NULL auto_increment,
				meta_type varchar(20) NOT NULL,
				meta_key varchar(100) default NULL,
				meta_value longtext,
				PRIMARY KEY (meta_id)
			) ENGINE=MyISAM ".sf_charset().";";
		$wpdb->query($sql);

		$create_ddl = "ALTER TABLE ".SFFORUMS. " ADD (topic_status_set bigint(20) default '0')";
		sf_upgrade_database(SFFORUMS, 'topic_status_set', $create_ddl);

		$create_ddl = "ALTER TABLE ".SFTOPICS. " ADD (topic_status_flag bigint(20) default '0')";
		sf_upgrade_database(SFTOPICS, 'topic_status_flag', $create_ddl);

		# admin bar fixed
		$sfadminsettings=array();
		$sfadminsettings=sf_opcheck(get_option('sfadminsettings'));
		$sfadminsettings['sfbarfix']=false;
		update_option('sfadminsettings', $sfadminsettings);

		$icons=sf_opcheck(get_option('sfshowicon'));
		if(strpos($icons, '@Close New Post List;') === false)
		{
			$icons.= '@Close New Post List;1';
			update_option('sfshowicon', $icons);
		}

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 380;
	if($build < $section)
	{
		# add new columns for tracking watched topics
		$create_ddl = "ALTER TABLE ".SFMEMBERS. " ADD (watches longtext)";
		sf_upgrade_database(SFMEMBERS, 'watches', $create_ddl);

		# add new Can follow topics permission
		sf_upgrade_add_new_role('Can watch topics', 0, true);

		# add new Can change topic status permission
		sf_upgrade_add_new_role('Can change topic status', 0, false, true);

		$icons=sf_opcheck(get_option('sfshowicon'));
		if(strpos($icons, '@Review Watched Topics;') === false)
		{
			$icons.= '@Review Watched Topics;1@End Topic Watch;1@Watch Topic;1';
		}
		update_option('sfshowicon', $icons);

		# remove usergroup locked column that is no longer used
		$wpdb->query("ALTER TABLE ".SFUSERGROUPS." DROP usergroup_locked;");

		# add new columns for tracking watched topics
		$create_ddl = "ALTER TABLE ".SFTOPICS. " ADD (topic_watches longtext)";
		sf_upgrade_database(SFTOPICS, 'topic_watches', $create_ddl);

		add_option('sfpostpaging', 4);

		$qt = sf_opcheck(get_option('sfquicktags'));
		$sfeditor = array();
		if($qt ? $c = 2 : $c = 1);
		$sfeditor['sfeditor'] = $c;
		$sfeditor['sfusereditor'] = false;
		add_option('sfeditor', $sfeditor);
		delete_option('sfquicktags');

		$sfpostratings = array();
		$sfpostratings['sfpostratings'] = false;
		$sfpostratings['sfratingsstyle'] = 1;
		add_option('sfpostratings', $sfpostratings);

		# add new Can rate post permission
		sf_upgrade_add_new_role('Can rate posts', 0, true);

		# change members 'quicktags' to 'editor'
		$sql = "ALTER TABLE ".SFMEMBERS." CHANGE quicktags editor SMALLINT(1) NOT NULL DEFAULT '1'";
		$wpdb->query($sql);
		$sql = "UPDATE ".SFMEMBERS." SET editor=2 WHERE editor=1";
		$wpdb->query($sql);
		$sql = "UPDATE ".SFMEMBERS." SET editor=1 WHERE editor=0";
		$wpdb->query($sql);

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 400;
	if($build < $section)
	{
		# sfpostratings table def
		$sql = "
			CREATE TABLE IF NOT EXISTS ".SFPOSTRATINGS." (
				rating_id bigint(20) NOT NULL auto_increment,
				post_id bigint(20) NOT NULL,
				vote_count bigint(20) NOT NULL,
				ratings_sum bigint(20) NOT NULL,
				ips longtext,
				members longtext,
				PRIMARY KEY  (rating_id)
			) ENGINE=MyISAM ".sf_charset().";";
		$wpdb->query($sql);

		# add new columns for posts users have voted in
		$create_ddl = "ALTER TABLE ".SFMEMBERS." ADD (posts_rated longtext)";
		sf_upgrade_database(SFMEMBERS, 'posts_rated', $create_ddl);

		# sfdefpermissions table def
	    $sql = "
	        CREATE TABLE IF NOT EXISTS ".SFDEFPERMISSIONS." (
	            permission_id mediumint(8) unsigned NOT NULL auto_increment,
	            group_id mediumint(8) unsigned NOT NULL default '0',
	            usergroup_id mediumint(8) unsigned NOT NULL default '0',
	            permission_role mediumint(8) unsigned NOT NULL default '0',
	            PRIMARY KEY  (permission_id)
	        ) ENGINE=MyISAM ".sf_charset().";";
	    $wpdb->query($sql);

		# fill in the default permissions for existing groups
		sf_group_def_perms();

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 480;
	if($build < $section)
	{
		# create new base smiley folder
		$smiley = sf_relocate_smileys();
		if($smiley != 0)
		{
			add_option('sfinstallsm', $smiley);
		}

		sf_build_base_smileys();

		# smileys control options
		$sfsmileys = array();
		$sfsmileys['sfsmallow'] = true;
		$sfsmileys['sfsmtype'] = 1;
		update_option('sfsmileys', $sfsmileys);

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 620;
	if($build < $section)
	{
		# if default Guests user group exists use it as default for guests - otherwise leave blank
		$guests = $wpdb->get_var("SELECT usergroup_id FROM ".SFUSERGROUPS." WHERE usergroup_name='Guests'");
		add_option('sfguestsgroup', $guests);

		$sfusersnewposts = array();
		$sfusersnewposts['sfshownewuser'] = sf_opcheck(get_option('sfshownewuser'));
		$sfusersnewposts['sfshownewcount'] = sf_opcheck(get_option('sfshownewcount'));
		$sfusersnewposts['sfshownewabove'] = sf_opcheck(get_option('sfshownewabove'));
		$sfusersnewposts['sfsortinforum'] = true;
		add_option('sfusersnewposts', $sfusersnewposts);

		delete_option('sfshownewuser');
		delete_option('sfshownewcount');
		delete_option('sfshownewabove');
		delete_option('sfallowguests');

		sf_build_tinymce_toolbar_arrays();

		# add new columns for options for users that are admins
		$create_ddl = "ALTER TABLE ".SFMEMBERS." ADD (admin_options longtext)";
		sf_upgrade_database(SFMEMBERS, 'admin_options', $create_ddl);

		$sfadminsettings = array();
		$sfadminsettings = sf_opcheck(get_option('sfadminsettings'));

		$sfnewadminsettings = array();
		$sfnewadminsettings['sftools'] = sf_opcheck($sfadminsettings['sftools']);
		$sfnewadminsettings['sfmodasadmin'] = sf_opcheck($sfadminsettings['sfmodasadmin']);
		$sfnewadminsettings['sfshowmodposts'] = sf_opcheck($sfadminsettings['sfshowmodposts']);
		$sfnewadminsettings['sfqueue'] = sf_opcheck($sfadminsettings['sfqueue']);
		update_option('sfadminsettings', $sfnewadminsettings);

		$sfadminoptions = array();
		$sfadminoptions['sfadminbar'] = sf_opcheck($sfadminsettings['sfadminbar']);
		$sfadminoptions['sfbarfix'] = sf_opcheck($sfadminsettings['sfbarfix']);
		if (isset($sfadminsettings['sfqueue']))
		{
			$sfadminoptions['sfnotify'] = sf_opcheck($sfadminsettings['sfnotify']);
			$sfadminoptions['sfshownewadmin'] = sf_opcheck($sfadminsettings['sfshownewadmin']);
		}
		sf_update_member_item($current_user->ID, 'admin_options', $sfadminoptions);

		# Style and Editor array changes
		$sfstyle = array();
		$sfrepstyle = array();
		$sfstyle = sf_opcheck(get_option('sfstyle'));

		$sfrepstyle['sfskin'] = sf_opcheck($sfstyle['sfskin']);
		$sfrepstyle['sficon'] = sf_opcheck($sfstyle['sficon']);
		$sfrepstyle['sfsize'] = '';
		update_option('sfstyle', $sfrepstyle);

		$sfeditor = array();
		$sfeditor = sf_opcheck(get_option('sfeditor'));
		$sfeditor['sfrejectformat'] = false;
		$sfeditor['sftmcontentCSS'] = 'content.css';
		$sfeditor['sftmuiCSS'] = 'ui.css';
		$sfeditor['sftmdialogCSS'] = 'dialog.css';
		$sfeditor['SFhtmlCSS'] = 'htmlEditor.css';
		$sfeditor['SFbbCSS'] = 'bbcodeEditor.css';
		$sfeditor['sflang'] = sf_opcheck($sfstyle['sflang']);
		if(get_bloginfo('text_direction') == 'rtl' ? $sfeditor['sfrtl'] = true : $sfeditor['sfrtl'] = false);

		update_option('sfeditor', $sfeditor);

		# Login array changes
		$sflogin = array();
		$sflogin = sf_opcheck(get_option('sflogin'));
		$sflogin['sfregmath'] = sf_opcheck(get_option('sfregmath'));
		$sflogin['sfinlogin'] = true;
		$sflogin['sfregtext'] = false;
		$sflogin['sfregcheck'] = false;
		$sflogin['sfloginskin'] = true;
		update_option('sflogin', $sflogin);

		delete_option('sfregmath');

		add_option('sflinkcomments', false);
		add_option('sfshoweditdata', true);
		add_option('sfshoweditlast', false);

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 693;
	if($build < $section)
	{
		# add new columns for storing post-post edits
		$create_ddl = "ALTER TABLE ".SFPOSTS." ADD (post_edit mediumtext)";
		sf_upgrade_database(SFPOSTS, 'post_edit', $create_ddl);

		add_option('sfavataruploads', true);
		add_option('sfprivatemessaging', true);
		add_option('sfsingleforum', false);
		add_option('sftaggedforum', '');

		# transfer forum rankins from options table to our sf meta table
		$rankings = sf_opcheck(get_option('sfrankings'));
		foreach ($rankings as $rank=>$posts)
		{
			$rankdata['posts'] = $posts;
			$rankdata['usergroup'] = 'none';
			$rankdata['image'] = 'none';
			sf_add_sfmeta('forum_rank', $rank, serialize($rankdata));
		}

		delete_option('sfrankings');

		# create new table for user group memberships
		$sql = "
			CREATE TABLE IF NOT EXISTS ".SFMEMBERSHIPS." (
				membership_id mediumint(8) unsigned NOT NULL auto_increment,
				user_id mediumint(8) unsigned NOT NULL default '0',
				usergroup_id mediumint(8) unsigned NOT NULL default '0',
				PRIMARY KEY  (membership_id)
			) ENGINE=MyISAM ".sf_charset().";";
		$wpdb->query($sql);

		# Build the Memberships from the usergroup column in the members table
		sf_build_memberships_table();

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 820;
	if($build < $section)
	{
		# remove usergroups column from members table
		$wpdb->query("ALTER TABLE ".SFMEMBERS." DROP usergroups;");

		$sfeditor = array();
		$sfeditor = sf_opcheck(get_option('sfeditor'));
		$sfeditor['sfrelative'] = true;
		update_option('sfeditor', $sfeditor);

		$sfaiosp = array();
		$sfaiosp['sfaiosp_topic'] = true;
		$sfaiosp['sfaiosp_forum'] = true;
		$sfaiosp['sfaiosp_sep'] = '|';
		add_option('sfaiosp', $sfaiosp);

		$sfsigimagesize = array();
		$sfsigimagesize['sfsigwidth'] = 0;
		$sfsigimagesize['sfsigheight'] = 0;
		add_option('sfsigimagesize', $sfsigimagesize);

		add_option('sfmemberlistperms', true);

		# correct id in sfmembers possible problem
		$found = false;
		$found = false;
		foreach ($wpdb->get_col("DESC ".SFMEMBERS, 0) as $column )
		{
			if ($column == 'id')
			{
				$found = true;
			}
    	}
		if($found)
		{
			$wpdb->query("ALTER TABLE ".SFMEMBERS." DROP id;");
			$wpdb->query("ALTER TABLE ".SFMEMBERS." ADD PRIMARY KEY (user_id);");
		}

		# gravatar rating of G by default
		add_option('sfgmaxrating', 1);

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 828;
	if($build < $section)
	{
		# pm message slugs
		$create_ddl = "ALTER TABLE ".SFMESSAGES. " ADD (message_slug text NOT NULL)";
		sf_upgrade_database(SFMESSAGES, 'message_slug', $create_ddl);

		$wpdb->query("ALTER TABLE ".SFMESSAGES." ADD UNIQUE mslug (message_slug);");

		sf_create_message_slugs();

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 873;
	if($build < $section)
	{
		add_option('sfcbexclusions', '');
		add_option('sfshowmemberlist', true);

		# store ip for posts
		$create_ddl = "ALTER TABLE ".SFPOSTS. " ADD (poster_ip varchar(15) NOT NULL)";
		sf_upgrade_database(SFPOSTS, 'poster_ip', $create_ddl);

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 945;
	if($build < $section)
	{
		# members icon text
		$icons=sf_opcheck(get_option('sfshowicon'));
		if(strpos($icons, '@Members;') === false)
		{
			$icons.= '@Members;1';
			update_option('sfshowicon', $icons);
		}

		# remove name/title and description length limits from groups and forums
		$wpdb->query("ALTER TABLE ".SFFORUMS." DROP INDEX fslug");
		$wpdb->query("ALTER TABLE ".SFTOPICS." DROP INDEX tslug");

		$wpdb->query("ALTER TABLE ".SFGROUPS." CHANGE group_name group_name TEXT NOT NULL");
		$wpdb->query("ALTER TABLE ".SFGROUPS." CHANGE group_desc group_desc TEXT NULL DEFAULT NULL");

		$wpdb->query("ALTER TABLE ".SFFORUMS." CHANGE forum_name forum_name TEXT NOT NULL");
		$wpdb->query("ALTER TABLE ".SFFORUMS." CHANGE forum_desc forum_desc TEXT NULL DEFAULT NULL");
		$wpdb->query("ALTER TABLE ".SFFORUMS." CHANGE forum_slug forum_slug TEXT NOT NULL");

		$wpdb->query("ALTER TABLE ".SFTOPICS." CHANGE topic_name topic_name TEXT NOT NULL");
		$wpdb->query("ALTER TABLE ".SFTOPICS." CHANGE topic_slug topic_slug TEXT NOT NULL");

		# admin bar - force removal from bar only
		$sfadminsettings=array();
		$sfadminsettings=sf_opcheck(get_option('sfadminsettings'));
		$sfadminsettings['sfbaronly']=false;
		update_option('sfadminsettings', $sfadminsettings);

		# add ability to turn off email settings
		$sfmail = array();
		$sfmail = sf_opcheck(get_option('sfmail'));
		$sfmail['sfmailuse']=true;
		update_option('sfmail', $sfmail);

		add_option('sfwpavatar', false);

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	# 4.0.1  =====================================================================================

	$section = 1030;
	if($build < $section)
	{
		# remove name/title and description length limits from user groups
		$wpdb->query("ALTER TABLE ".SFUSERGROUPS." CHANGE usergroup_name usergroup_name TEXT NOT NULL");
		$wpdb->query("ALTER TABLE ".SFUSERGROUPS." CHANGE usergroup_desc usergroup_desc TEXT NULL DEFAULT NULL");

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	# 4.0.2  =====================================================================================

	$section = 1360;
	if($build < $section)
	{
		# Add blockquote to tm toolbar
		sf_update_tmtoolbar_blockquote();

		add_option('sfcheckformember', true);

		# housekeeping routine to clean up duplicate memberships and members
		sf_update_membership_cleanup();

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	# 4.0.3  =====================================================================================

	$section = 1373;
	if($build < $section)
	{
		# dashboard settngs
		$sfadminsettings=array();
		$sfadminsettings=sf_opcheck(get_option('sfadminsettings'));
		$sfadminsettings['sfdashboardposts']=true;
		$sfadminsettings['sfdashboardstats']=true;
		update_option('sfadminsettings', $sfadminsettings);

		# Optional SPF New User Email
		$sfmail = sf_opcheck(get_option('sfnewusermail'));
		$sfmail['sfusespfreg'] = true;
		update_option('sfnewusermail', $sfmail);

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	# 4.1  =====================================================================================

	$section = 1382;
	if($build < $section)
	{
		# post linking excerpt options (1=full post, 2=full post excerpt, 3=WP Excerpt)
		$sfpostlinking = array();
		$excerpt = sf_opcheck(get_option('sflinkexcerpt'));
		if ($excerpt ? $excerpt = 2 : $excerpt = 1);
		$sfpostlinking['sflinkexcerpt'] = $excerpt;
		$sfpostlinking['sflinksingle'] = false;

		# combine all the post linking options into single array
		$sfpostlinking['sflinkwords'] = sf_opcheck(get_option('sflinkwords'));
		$sfpostlinking['sflinkblogtext'] = sf_opcheck(get_option('sflinkblogtext'));
		$sfpostlinking['sflinkforumtext'] = sf_opcheck(get_option('sflinkforumtext'));
		$sfpostlinking['sflinkabove'] = sf_opcheck(get_option('sflinkabove'));
		$sfpostlinking['sflinkcomments'] = sf_opcheck(get_option('sflinkcomments'));
		add_option('sfpostlinking', $sfpostlinking);

		# remove old singular options
		delete_option('sflinkexcerpt');
		delete_option('sflinkwords');
		delete_option('sflinkblogtext');
		delete_option('sflinkforumtext');
		delete_option('sflinkabove');
		delete_option('sflinkcomments');

		# add new columns for user options
		$create_ddl = "ALTER TABLE ".SFMEMBERS." ADD (user_options longtext)";
		sf_upgrade_database(SFMEMBERS, 'user_options', $create_ddl);

		# build user options table with editor option
		sf_create_user_options();

		# drop the old editor column
		$wpdb->query("ALTER TABLE ".SFMEMBERS." DROP editor;");

		$icons=sf_opcheck(get_option('sfshowicon'));
		if(strpos($icons, '@Unsubscribe;') === false)
		{
			$icons.= '@Unsubscribe;1';
			update_option('sfshowicon', $icons);
		}

		# Add full text index for topic title search
		$wpdb->query("ALTER TABLE ".SFTOPICS." ADD FULLTEXT topic_name_idx (topic_name)");

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 1470;
	if($build < $section)
	{
		# change name of current edit topic title role
		sf_modify_rolename('Can edit topic titles', 'Can edit any topic titles');

		# add new Can edit own topic titles permission
		sf_upgrade_add_new_role('Can edit own topic titles', 0, false, true);

		# code exclusions
		$sfsupport = array();
		$sfsupport['sfusinglinking'] = true;
		$sfsupport['sfusingtags'] = true;
		$sfsupport['sfusingwidgets'] = true;
		$sfsupport['sfusingavatars'] = true;
		add_option('sfsupport', $sfsupport);

		$icons=sf_opcheck(get_option('sfshowicon'));
		if (strpos($icons, '@Watch this Topic;') === false)
		{
			$icons.= '@Watch this Topic;1@Stop Watching this Topic;1@Unsubscribe from this Topic;1';
			update_option('sfshowicon', $icons);
		}

		# get current members default group
		$members = sf_opcheck(get_option('sfdefgroup'));
		$guests = sf_opcheck(get_option('sfguestsgroup'));

		# remove default usergroups from options
		delete_option('sfguestsgroup');
		delete_option('sfdefgroup');

		# create new default usergroups in sfmeta
		sf_add_sfmeta('default usergroup', 'sfguests', $guests); # default usergroup for guests
		sf_add_sfmeta('default usergroup', 'sfmembers', $members); # default usergroup for members
		sf_create_usergroup_meta($members); # create default usergroups for existing wp roles

		# re-assert s/ug indices and name.title length restrictions in database
		$wpdb->query("ALTER TABLE ".SFFORUMS." CHANGE forum_name forum_name VARCHAR(200) NOT NULL;");
		$wpdb->query("ALTER TABLE ".SFFORUMS." CHANGE forum_slug forum_slug VARCHAR(200) NOT NULL;");
		$wpdb->query("ALTER TABLE ".SFFORUMS." ADD INDEX fslug_idx (forum_slug);");

		$wpdb->query("ALTER TABLE ".SFTOPICS." CHANGE topic_name topic_name VARCHAR(200) NOT NULL;");
		$wpdb->query("ALTER TABLE ".SFTOPICS." CHANGE topic_slug topic_slug VARCHAR(200) NOT NULL;");
		$wpdb->query("ALTER TABLE ".SFTOPICS." ADD INDEX tslug_idx (topic_slug);");

		$wpdb->query("ALTER TABLE ".SFMESSAGES." CHANGE title title VARCHAR(200) NULL DEFAULT NULL;");
		$wpdb->query("ALTER TABLE ".SFMESSAGES." CHANGE message_slug message_slug VARCHAR(200) NOT NULL;");
		$wpdb->query("ALTER TABLE ".SFMESSAGES." ADD INDEX mslug_idx (message_slug);");

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 1720;
	if($build < $section)
	{
		add_option('sfsinglemembership', false);

		$sql = "ALTER TABLE ".SFMEMBERS." MODIFY posts int(4) default NULL";
		$wpdb->query($sql);

		# New controls option array
		$max = get_sfsetting('maxonline');
		if(empty($max)) $max = 0;
		delete_sfsetting('maxonline');

		$sfcontrols = array();
		$sfcontrols['dayflag'] = '0';
		$sfcontrols['hourflag'] = '0';
		$sfcontrols['maxonline'] = $max;
		$sfcontrols['membercount'] = 0;
		$sfcontrols['guestcount'] = 0;
		$sfcontrols['fourofour'] = false;
		add_option('sfcontrols', $sfcontrols);

		# New count and info fields
		$create_ddl = "ALTER TABLE ".SFFORUMS. " ADD (post_count mediumint(8) default '0')";
		sf_upgrade_database(SFFORUMS, 'post_count', $create_ddl);
		sf_build_41_counts();

		# change over to topic subscriptions and watches being stored in serialized arrays
		sf_serialize_subs_watches_ratings();

		# allow post ratings on forum by forum basis
		$wpdb->query("ALTER TABLE ".SFFORUMS." ADD (post_ratings smallint(1) NOT NULL default '0')");

		# enablef or all forums if currently enabled
		$sfpostratings = sf_opcheck(get_option('sfpostratings'));
		if ($sfpostratings['sfpostratings']) $wpdb->query("UPDATE ".SFFORUMS." SET post_ratings=1");

		# add spoiler tinymce plugin
		sf_update_tmtoolbar_spoiler();

		# SPF Manage Toolbox caps
		sf_update_admin_toolbox();

		$icons = sf_opcheck(get_option('sfshowicon'));
		if (strpos($icons, '@Manage;') === false)
		{
			$icons.= '@Manage;1';
			update_option('sfshowicon', $icons);
		}

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 1795;
	if($build < $section)
	{
		# New option to not allow users access to wp admin
		add_option('sfblockadmin', false);

		# sftags table def
        $sql = "
            CREATE TABLE IF NOT EXISTS ".SFTAGS." (
                tag_id bigint(20) NOT NULL auto_increment,
                tag_name varchar(50) default NULL,
                tag_slug varchar(50) default NULL,
                tag_count bigint(20) default '0',
                PRIMARY KEY  (tag_id),
                FULLTEXT KEY tag_name (tag_name)
            ) ENGINE=MyISAM ".sf_charset().";";
        $wpdb->query($sql);

		# sftagmeta table def
        $sql = "
            CREATE TABLE IF NOT EXISTS ".SFTAGMETA." (
                meta_id bigint(20) NOT NULL auto_increment,
                tag_id bigint(20) default '0',
                topic_id bigint(20) default '0',
                forum_id bigint(20) default '0',
                PRIMARY KEY  (meta_id),
                KEY tag_idx (tag_id),
                KEY topic_idx (topic_id),
                KEY forum_idx (forum_id)
            ) ENGINE=MyISAM ".sf_charset().";";
        $wpdb->query($sql);

		# allow enabling tags on forum by forum basis
		$wpdb->query("ALTER TABLE ".SFFORUMS." ADD (use_tags smallint(1) NOT NULL default '0')");

		#max tags allowed and combine topics options
		$sftopics = array();
		$sftopics['sfpagedtopics'] = sf_opcheck(get_option('sfpagedtopics'));
		$sftopics['sftopicsort'] = sf_opcheck(get_option('sftopicsort'));
		$sftopics['sfpaging'] = sf_opcheck(get_option('sfpaging'));
		$sftopics['sftagsabove'] = true;
		$sftopics['sftagsbelow'] = true;
		$sftopics['sfmaxtags'] = 0;
		add_option('sftopics', $sftopics);

		delete_option('sfpagedtopics');
		delete_option('sftopicsort');
		delete_option('sfpaging');

		# New indexing to aid SQL Joins
		$wpdb->query("ALTER TABLE ".SFMEMBERSHIPS." ADD INDEX user_id_idx ( user_id );");
		$wpdb->query("ALTER TABLE ".SFMEMBERSHIPS." ADD INDEX usergroup_id_idx ( usergroup_id );");
		$wpdb->query("ALTER TABLE ".SFDEFPERMISSIONS." ADD INDEX usergroup_id_idx ( usergroup_id );");
		$wpdb->query("ALTER TABLE ".SFMESSAGES." ADD INDEX from_id_idx ( from_id );");
		$wpdb->query("ALTER TABLE ".SFMESSAGES." ADD INDEX to_id_idx ( to_id );");
		$wpdb->query("ALTER TABLE ".SFPOSTS." ADD INDEX user_id_idx ( user_id );");
		$wpdb->query("ALTER TABLE ".SFPERMISSIONS." ADD INDEX usergroup_id_idx ( usergroup_id );");
		$wpdb->query("ALTER TABLE ".SFPOSTRATINGS." ADD INDEX post_id_idx ( post_id );");
		$wpdb->query("ALTER TABLE ".SFWAITING." ADD INDEX forum_id_idx ( forum_id );");

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 1835;
	if($build < $section)
	{
		# New configuration record
		$basepath='';
		if (is_multisite())
		{
			# construct multisite storage directory structure and create if necessary
			$basepath .= 'blogs.dir/' . $wpdb->blogid;
			if (!file_exists(SF_STORE_DIR . '/' . $basepath)) @mkdir(SF_STORE_DIR . '/' . $basepath, 0755);
			$basepath .= '/files';
			if (!file_exists(SF_STORE_DIR . '/' . $basepath)) @mkdir(SF_STORE_DIR . '/' . $basepath, 0755);
			$basepath .= '/';
		}

		$sfconfig = array();
		$sfconfig['styles'] 		= 'plugins/simple-forum/styles';
		$sfconfig['avatars'] 		= $basepath . 'forum-avatars';
		$sfconfig['smileys'] 		= $basepath . 'forum-smileys';
		$sfconfig['ranks'] 			= $basepath . 'forum-badges';

		$uploadpath = sf_opcheck(get_option('sfuppath'));
		if(empty($uploadpath))
		{
			$sfconfig['image-uploads'] 	= $basepath . 'forum-image-uploads';
			$newpath = SF_STORE_DIR . '/' . $sfconfig['image-uploads'];
			@mkdir($newpath, 0777);
		} else {
			$sfconfig['image-uploads'] 	= $uploadpath;
		}
		$sfconfig['media-uploads'] 	= $basepath . 'forum-media-uploads';
		$sfconfig['file-uploads'] 	= $basepath . 'forum-file-uploads';
		$sfconfig['hooks'] 			= $basepath . 'plugins/simple-forum/forum/hooks';
		$sfconfig['pluggable'] 		= 'plugins/simple-forum/forum';
		$sfconfig['help'] 			= 'plugins/simple-forum/help';
		$sfstyle = sf_opcheck(get_option('sfstyle'));
		$curiconset = $sfstyle['sficon'];
		$sfconfig['custom-icons']	= 'plugins/simple-forum/styles/icons/'.$curiconset.'/custom/';
		add_option('sfconfig', $sfconfig);

		$newpath = SF_STORE_DIR . '/' . $sfconfig['media-uploads'];
		@mkdir($newpath, 0777);

		$newpath = SF_STORE_DIR . '/' . $sfconfig['file-uploads'];
		@mkdir($newpath, 0777);

		$newpath = SF_STORE_DIR . '/' . $sfconfig['ranks'];
		@mkdir($newpath, 0777);

		delete_option('sfuppath');

		$sfuploads = array();
		$sfuploads['privatefolder'] = true;
		$sfuploads['thumbsize'] = 80;
		$sfuploads['pagecount'] = 25;
		$sfuploads['imagetypes'] = 'jpg, jpeg, gif, png';
		$sfuploads['imagemaxsize'] = 51200;
		$sfuploads['mediatypes'] = 'swf, dcr, mov, qt, mpg, mp3, mp4, mpeg, avi, wmv, wm, asf, asx, wmx, wvx, rm, ra, ram';
		$sfuploads['mediamaxsize'] = 0;
		$sfuploads['filetypes'] = 'txt, rtf, doc, pdf';
		$sfuploads['filemaxsize'] = 51200;
		$sfuploads['prohibited'] = 'php, php3, php5, js, html, htm, phtml, asp, aspx, ascx, jsp, cfm, cfc, pl, bat, exe, dll, reg, cgi, sh, py';
		add_option('sfuploads', $sfuploads);

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 1963;
	if($build < $section)
	{
		$sfmetatags = array();
		$sfmetatags['sfdescr'] = '';
		$sfmetatags['sfdescruse'] = 1;
		$sfmetatags['sfusekeywords'] = true;
		$sfmetatags['keywords'] = '';
		$sfmetatags['sftagwords'] = true;
		add_option('sfmetatags', $sfmetatags);

		# admin colours
		$sfacolours = array();
		$sfacolours['bbarbg']='0066CC';
		$sfacolours['bbarbgt']='FFFFFF';
		$sfacolours['formbg']='0066CC';
		$sfacolours['formbgt']='FFFFFF';
		$sfacolours['panelbg']='78A1FF';
		$sfacolours['panelbgt']='000000';
		$sfacolours['panelsubbg']='A7C1FF';
		$sfacolours['panelsubbgt']='000000';
		$sfacolours['formtabhead']='464646';
		$sfacolours['formtabheadt']='D7D7D7';
		$sfacolours['tabhead']='0066CC';
		$sfacolours['tabheadt']='D7D7D7';
		$sfacolours['tabrowmain']='EAF3FA';
		$sfacolours['tabrowmaint']='000000';
		$sfacolours['tabrowsub']='78A1FF';
		$sfacolours['tabrowsubt']='000000';
		add_option('sfacolours', $sfacolours);

		$sfdisplay = array();

		$sftitle 		= sf_opcheck(get_option('sftitle'));
		$sfquicklinks	= sf_opcheck(get_option('sfquicklinks'));
		$sfusersnewposts = sf_opcheck(get_option('sfusersnewposts'));
		$sfforumcols 	= sf_opcheck(get_option('sfforumcols'));
		$sftopics		= sf_opcheck(get_option('sftopics'));
		$sftopiccols 	= sf_opcheck(get_option('sftopiccols'));

		$sfdisplay['pagetitle']['include'] 			= sf_opcheck($sftitle['sfinclude']);
		$sfdisplay['pagetitle']['notitle'] 			= sf_opcheck($sftitle['sfnotitle']);
		$sfdisplay['pagetitle']['banner'] 			= sf_opcheck($sftitle['sfbanner']);

		$sfdisplay['breadcrumbs']['showcrumbs']		= sf_opcheck(get_option('sfshowbreadcrumbs'));
		$sfdisplay['breadcrumbs']['showhome']		= sf_opcheck(get_option('sfshowhome'));
		$sfdisplay['breadcrumbs']['homepath']		= sf_opcheck(get_option('sfhome'));
		$sfdisplay['breadcrumbs']['tree']			= false;

		$sfdisplay['search']['searchtop']			= sf_opcheck(get_option('sfsearchbar'));
		$sfdisplay['search']['searchbottom']		= sf_opcheck(get_option('sfsearchbar'));

		$sfdisplay['quicklinks']['qltop']			= sf_opcheck($sfquicklinks['sfqlshow']);
		$sfdisplay['quicklinks']['qlbottom']		= sf_opcheck($sfquicklinks['sfqlshow']);
		$sfdisplay['quicklinks']['qlcount']			= sf_opcheck($sfquicklinks['sfqlcount']);

		$sfdisplay['stats']['showstats']			= sf_opcheck(get_option('sfstats'));
		$sfdisplay['stats']['mostusers']			= sf_opcheck(get_option('sfstats'));
		$sfdisplay['stats']['online']				= sf_opcheck(get_option('sfstats'));
		$sfdisplay['stats']['forumstats']			= sf_opcheck(get_option('sfstats'));
		$sfdisplay['stats']['memberstats']			= sf_opcheck(get_option('sfstats'));
		$sfdisplay['stats']['topsix']				= sf_opcheck(get_option('sfstats'));
		$sfdisplay['stats']['admins']				= sf_opcheck(get_option('sfstats'));

		$sfdisplay['firstlast']['date']				= true;
		$sfdisplay['firstlast']['time']				= true;
		$sfdisplay['firstlast']['user']				= true;

		$sfdisplay['groups']['description']			= true;

		$sfdisplay['forums']['description']			= true;
		$sfdisplay['forums']['newposticon']			= true;
		$sfdisplay['forums']['pagelinks']			= true;
		$sfdisplay['forums']['newpposts']			= sf_opcheck($sfusersnewposts['sfshownewuser']);
		$sfdisplay['forums']['newcount']			= sf_opcheck($sfusersnewposts['sfshownewcount']);
		$sfdisplay['forums']['newabove']			= sf_opcheck($sfusersnewposts['sfshownewabove']);
		$sfdisplay['forums']['sortinforum']			= sf_opcheck($sfusersnewposts['sfsortinforum']);
		$sfdisplay['forums']['singleforum']			= sf_opcheck(get_option('sfsingleforum'));
		$sfdisplay['forums']['topiccol']			= sf_opcheck($sfforumcols['topics']);
		$sfdisplay['forums']['postcol']				= sf_opcheck($sfforumcols['posts']);
		$sfdisplay['forums']['lastcol']				= sf_opcheck($sfforumcols['last']);

		$sfdisplay['topics']['perpage']				= sf_opcheck($sftopics['sfpagedtopics']);
		$sfdisplay['topics']['numpagelinks']		= sf_opcheck($sftopics['sfpaging']);
		$sfdisplay['topics']['sortnewtop']			= sf_opcheck($sftopics['sftopicsort']);
		$sfdisplay['topics']['maxtags']				= sf_opcheck($sftopics['sfmaxtags']);
		$sfdisplay['topics']['firstcol']			= sf_opcheck($sftopiccols['first']);
		$sfdisplay['topics']['lastcol']				= sf_opcheck($sftopiccols['last']);
		$sfdisplay['topics']['postcol']				= sf_opcheck($sftopiccols['posts']);
		$sfdisplay['topics']['viewcol']				= sf_opcheck($sftopiccols['views']);
		$sfdisplay['topics']['pagelinks']			= true;
		$sfdisplay['topics']['statusicons']			= true;
		$sfdisplay['topics']['postrating']			= true;
		$sfdisplay['topics']['topicstatus']			= true;
		$sfdisplay['topics']['topictags']			= true;

		$sfdisplay['posts']['perpage']				= sf_opcheck(get_option('sfpagedposts'));
		$sfdisplay['posts']['numpagelinks']			= sf_opcheck(get_option('sfpostpaging'));
		$sfdisplay['posts']['userabove']			= sf_opcheck(get_option('sfuserabove'));
		$sfdisplay['posts']['sortdesc']				= sf_opcheck(get_option('sfsortdesc'));
		$sfdisplay['posts']['showedits']			= sf_opcheck(get_option('sfshoweditdata'));
		$sfdisplay['posts']['showlastedit']			= sf_opcheck(get_option('sfshoweditlast'));
		$sfdisplay['posts']['tagstop']				= sf_opcheck($sftopics['sftagsabove']);
		$sfdisplay['posts']['tagsbottom']			= sf_opcheck($sftopics['sftagsbelow']);
		$sfdisplay['posts']['topicstatushead']		= true;
		$sfdisplay['posts']['topicstatuschanger']	= true;
		$sfdisplay['posts']['online']				= true;
		$sfdisplay['posts']['time']					= true;
		$sfdisplay['posts']['date']					= true;
		$sfdisplay['posts']['usertype']				= true;
		$sfdisplay['posts']['rankdisplay']			= true;
		$sfdisplay['posts']['location']				= true;
		$sfdisplay['posts']['postcount']			= true;
		$sfdisplay['posts']['permalink']			= true;
		$sfdisplay['posts']['print']				= true;

		add_option('sfdisplay', $sfdisplay);

		delete_option('sftitle');
		delete_option('sfusersnewposts');
		delete_option('sfforumcols');
		delete_option('sftopics');
		delete_option('sftopiccols');
		delete_option('sfshowbreadcrumbs');
		delete_option('sfshowhome');
		delete_option('sfhome');
		delete_option('sfsearchbar');
		delete_option('sfquicklinks');
		delete_option('sfstats');
		delete_option('sfsingleforum');
		delete_option('sfpagedposts');
		delete_option('sfpostpaging');
		delete_option('sfuserabove');
		delete_option('sfsortdesc');
		delete_option('sfshoweditdata');
		delete_option('sfshoweditlast');
		delete_option('sftagsabove');
		delete_option('sftagsbelow');

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 2040;
	if ($build < $section)
	{
		$icons=sf_opcheck(get_option('sfshowicon'));
		if (strpos($icons, '@Print this Post') === false)
		{
			$icons.= '@Print this Post;1';
			update_option('sfshowicon', $icons);
		}

		$icons=sf_opcheck(get_option('sfshowicon'));
		if (strpos($icons, '@Related Topics') === false)
		{
			$icons.= '@Related Topics;1';
			update_option('sfshowicon', $icons);
		}

		# add new Can edit own topic titles permission
		sf_upgrade_add_new_role('Can delete own posts', 0);

		# add new Can edit own topic titles permission
		sf_upgrade_add_new_role('Can view forum and topic lists only', 0);

		# sub-forum support
		$create_ddl = "ALTER TABLE ".SFFORUMS." ADD (parent bigint(20) NOT NULL default '0');";
		sf_upgrade_database(SFFORUMS, 'parent', $create_ddl);
		$create_ddl = "ALTER TABLE ".SFFORUMS." ADD (children varchar(255) default NULL);";
		sf_upgrade_database(SFFORUMS, 'children', $create_ddl);

		# code exclusions
		$sfsupport = sf_opcheck(get_option('sfsupport'));
		$sfsupport_new['sfusinglinking'] = sf_opcheck($sfsupport['sfusinglinking']);
		$sfsupport_new['sfusingwidgets'] = sf_opcheck($sfsupport['sfusingwidgets']);
		$sfsupport_new['sfusinggeneraltags'] = sf_opcheck($sfsupport['sfusingtags']);
		$sfsupport_new['sfusingavatartags'] = sf_opcheck($sfsupport['sfusingavatars']);
		$sfsupport_new['sfusinglinkstags'] = true;
		$sfsupport_new['sfusingtagstags'] = true;
		$sfsupport_new['sfusingpagestags'] = true;
		$sfsupport_new['sfusingliststags'] = true;
		$sfsupport_new['sfusingstatstags'] = true;
		$sfsupport_new['sfusingpmtags'] = true;
		update_option('sfsupport', $sfsupport_new);

		# convert icons to serialized and clean up
		sf_upgrade_icontext();

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 2095;
	if($build < $section)
	{
		# avatar options updating and single option
		sf_upgrade_avatar_options();

		# guest settings
		$sfguests = array();
		$sfguests['reqemail'] = true;
		$sfguests['storecookie'] = true;
		add_option('sfguests', $sfguests);

		#redirect url for block admin
		add_option('sfblockredirect', SFURL); #default to forum

		# new Manage Profiles - give to upgrading admin only
		$user = new WP_User($current_user->ID);
		$user->add_cap('SPF Manage Profiles');

		# need to convert how custom profile data is stored to allow more options
		sf_update_custom_fields();

		# convert users custom profile field data
		sf_convert_custom_profile_fields();

		$sffilters = array();
		$sffilters = sf_opcheck(get_option('sffilters'));
		$sffilters['sfurlchars'] = 40;
		$sffilters['sffilterpre'] = false;
		$sffilters['sfmaxlinks'] = 0;
		update_option('sffilters', $sffilters);

		$sfblock = array();
		$sfblock['blockadmin'] = sf_opcheck(get_option('sfblockadmin'));
		$sfblock['blockredirect'] = sf_opcheck(get_option('sfblockredirect'));
		$sfblock['blockrole'] = 'administrator';

		delete_option('sfblockadmin');
		delete_option('sfblockredirect');

		update_option('sfblockadmin', $sfblock);

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 2200;
	if ($build < $section)
	{
		# profile management
		$sfprofile = array();

		# data control
		$sfprofile['require']['first_name'] = true;
		$sfprofile['require']['last_name'] = true;
		$sfprofile['require']['user_url'] = false;
		$sfprofile['require']['location'] = false;
		$sfprofile['require']['description'] = false;
		$sfprofile['require']['aim'] = false;
		$sfprofile['require']['yahoo'] = false;
		$sfprofile['require']['jabber'] = false;
		$sfprofile['require']['icq'] = false;
		$sfprofile['require']['msn'] = false;
		$sfprofile['require']['skype'] = false;
		$sfprofile['require']['facebook'] = false;
		$sfprofile['require']['myspace'] = false;
		$sfprofile['require']['twitter'] = false;

		$sfprofile['include']['first_name'] = true;
		$sfprofile['include']['last_name'] = true;
		$sfprofile['include']['user_url'] = true;
		$sfprofile['include']['location'] = true;
		$sfprofile['include']['description'] = true;
		$sfprofile['include']['aim'] = true;
		$sfprofile['include']['yahoo'] = true;
		$sfprofile['include']['jabber'] = true;
		$sfprofile['include']['icq'] = true;
		$sfprofile['include']['msn'] = true;
		$sfprofile['include']['skype'] = true;
		$sfprofile['include']['facebook'] = true;
		$sfprofile['include']['myspace'] = true;
		$sfprofile['include']['twitter'] = true;

		$sfprofile['display']['first_name'] = true;
		$sfprofile['display']['last_name'] = true;
		$sfprofile['display']['user_url'] = true;
		$sfprofile['display']['location'] = true;
		$sfprofile['display']['description'] = true;
		$sfprofile['display']['aim'] = true;
		$sfprofile['display']['yahoo'] = true;
		$sfprofile['display']['jabber'] = true;
		$sfprofile['display']['icq'] = true;
		$sfprofile['display']['msn'] = true;
		$sfprofile['display']['skype'] = true;
		$sfprofile['display']['facebook'] = true;
		$sfprofile['display']['myspace'] = true;
		$sfprofile['display']['twitter'] = true;

		# derived data display only
		$sfprofile['system']['forumrank'] = true;
		$sfprofile['system']['specialrank'] = true;
		$sfprofile['system']['badge'] = true;
		$sfprofile['system']['memberships'] = true;

		# display labels
		$sfprofile['label']['first_name'] = 'First Name';
		$sfprofile['label']['last_name'] = 'Last Name';
		$sfprofile['label']['user_url'] = 'Website';
		$sfprofile['label']['location'] = 'Location';
		$sfprofile['label']['description'] = 'Short Biography';
		$sfprofile['label']['aim'] = 'AIM';
		$sfprofile['label']['yahoo'] = 'Yahoo IM';
		$sfprofile['label']['jabber'] = 'Jabber-Google Talk';
		$sfprofile['label']['icq'] = 'ICQ';
		$sfprofile['label']['msn'] = 'MSN';
		$sfprofile['label']['skype'] = 'Skype';
		$sfprofile['label']['facebook'] = 'Facebook';
		$sfprofile['label']['myspace'] = 'MySpace';
		$sfprofile['label']['twitter'] = 'Twitter';

		$sfprofile['label']['forumrank'] = 'Forum Rank';
		$sfprofile['label']['specialrank'] = 'Special Rank';
		$sfprofile['label']['badge'] = 'Badge';
		$sfprofile['label']['memberships'] = 'User Group Memberships';

		# add custom fields already defined
		$custom = sf_get_sfmeta('custom_field');
		if($custom)
		{
			foreach ($custom as $x => $cf)
			{
				$sfprofile['require'][$cf['meta_key']] = true;
				$sfprofile['include'][$cf['meta_key']] = true;
				$sfprofile['display'][$cf['meta_key']] = true;
				$sfprofile['label'][$cf['meta_key']] = $cf['meta_key'];
			}
		}

		# profile options
		$sfprofile['nameformat'] = 1;
		$sfprofile['profilelink'] = 3;
		$sfprofile['weblink'] = 3;
		$sfprofile['displaymode'] = 1;
		$sfprofile['displaypage'] = '';
		$sfprofile['displayquery'] = '';
		$sfprofile['displayinforum'] = 0;
		$sfprofile['formmode'] = 1;
		$sfprofile['formpage'] = '';
		$sfprofile['formquery'] = '';
		$sfprofile['forminforum'] = 0;
		$sfprofile['photosmax'] = 0;
		$sfprofile['photoswidth'] = 0;
		$sfprofile['firstvisit'] = true;
		$sfprofile['forcepw'] = false;

		add_option('sfprofile', $sfprofile);
		delete_option('sfextprofile');

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 2236;
	if ($build < $section)
	{
		# Login url collection
		$sflogin = sf_opcheck(get_option('sflogin'));

		$sflogin['sfloginurl'] = SFSITEURL.'wp-login.php?action=login&amp;view=forum';
		$sflogin['sfloginemailurl'] = SFSITEURL.'wp-login.php?action=login&view=forum';
		$sflogin['sflogouturl'] = SFSITEURL.'wp-login.php?action=logout&amp;redirect_to='.SFURL;
		$sflogin['sfregisterurl'] = SFSITEURL.'wp-login.php?action=register&amp;view=forum';
		$sflogin['sflostpassurl'] = SFSITEURL.'wp-login.php?action=lostpassword&amp;view=forum';

		update_option('sflogin', $sflogin);

		# install log table def
		$sql = "
			CREATE TABLE IF NOT EXISTS ".SFLOG." (
				id int(4) NOT NULL auto_increment,
				user_id bigint(20) NOT NULL,
				install_date date NOT NULL,
				release_type varchar(15),
				version varchar(10) NOT NULL,
				build int(4) NOT NULL,
				PRIMARY KEY (id)
			) ENGINE=MyISAM ".sf_charset().";";
		$wpdb->query($sql);

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 2280;
	if($build < $section)
	{
		# forum message
		$create_ddl = "ALTER TABLE ".SFFORUMS." ADD (forum_message text);";
		sf_upgrade_database(SFFORUMS, 'forum_message', $create_ddl);

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 2337;
	if($build < $section)
	{
		# forum message
		$create_ddl = "ALTER TABLE ".SFGROUPS." ADD (group_message text);";
		sf_upgrade_database(SFGROUPS, 'group_message', $create_ddl);

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 2352;
	if($build < $section)
	{
		# fix avatar error for 4.1 b1 users with fresh install
		$sfavatars = sf_opcheck(get_option('sfavatars'));
		if (empty($sfavatars))
		{
			sf_upgrade_avatar_options();
		}

		# clean up old avatar options
		delete_option('sfshowavatars');
		delete_option('sfavatarsize');
		delete_option('sfgravatar');
		delete_option('sfgmaxrating');
		delete_option('sfwpavatar');
		delete_option('sfavataruploads');

		# remove option for add topic template tag since it can now be passed as argument
		delete_option('sftaggedforum');

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 2425;
	if($build < $section)
	{
		# Make signature field larger
		$sql = "ALTER TABLE ".SFMEMBERS." CHANGE signature signature TEXT  NULL DEFAULT NULL";
		$wpdb->query($sql);

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	# 4.1.1  =====================================================================================

	$section = 2500;
	if($build < $section)
	{
		update_option('sfzone', '0');

		# Fix up smileys if the data has been lost
		$s = sf_get_sfmeta('smileys');
		$sm = unserialize($s[0]['meta_value']);
		if(empty($sm))
		{
			sf_build_base_smileys();
		}

		update_option('sfbuild', $section);
		echo $section;
		die();
	}

	# 4.2  =====================================================================================
	# == Note: sfoptions table introduced (4.2) ==

	$section = 2720;
	if($build < $section)
	{
        ###### move options to their own spf table ######
		sf_move_options();
        ###### move options to their own spf table ######

		# add new can view forum lists only permission
		sf_upgrade_add_new_role('Can view forum lists only', 0);

		# add new can view forum lists only permission
		sf_upgrade_add_new_role('Can view admin posts', 0, false, false, true);

		# add new column for pm type to pm table
		$create_ddl = "ALTER TABLE ".SFMESSAGES. " ADD (type smallint(2) NOT NULL default '1')";
		sf_upgrade_database(SFMESSAGES, 'type', $create_ddl);

		# pm options
		$sfpm = array();
		$sfpm = sf_opcheck(sf_get_option('sfpm'));
		$pm['sfpmcc'] = true;
		$pm['sfpmbcc'] = true;
		$pm['sfpmmaxrecipients'] = 0;
		sf_update_option('sfpm', $pm);

		sf_upgrade_members_pm();

		sf_modify_admin_cap('SPF Manage Database', 'SPF Manage Tags');

		sf_delete_option('sfdemocracy');

		$sfaiosp = sf_opcheck(sf_get_option('sfaiosp'));
		$sfseo = array();
		$sfseo['sfseo_topic'] = sf_opcheck($sfaiosp['sfaiosp_topic']);
		$sfseo['sfseo_forum'] = sf_opcheck($sfaiosp['sfaiosp_forum']);
		$sfseo['sfseo_sep'] = sf_opcheck($sfaiosp['sfaiosp_sep']);
		sf_update_option('sfseo', $sfseo);
		sf_delete_option('sfaiosp');

		$sfcontrols = sf_opcheck(sf_get_option('sfcontrols'));
		$sfcontrols['dayflag'] = '0';
		$sfcontrols['hourflag'] = '0';
		$sfcontrols['showtopcount']	= 6;
		$sfcontrols['shownewcount'] = 6;
		sf_update_option('sfcontrols', $sfcontrols);

		$sfdisplay = sf_opcheck(sf_get_option('sfdisplay'));
		$sfdisplay['stats']['topposters'] = sf_opcheck($sfdisplay['stats']['topsix']);
		$sfdisplay['stats']['newusers']	= true;
		sf_update_option('sfdisplay', $sfdisplay);

		sf_update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 2797;
	if($build < $section)
	{
		# update column
		$wpdb->query("ALTER TABLE ".SFMEMBERS." CHANGE avatar avatar LONGTEXT NULL DEFAULT NULL");

		$sfconfig = sf_opcheck(sf_get_option('sfconfig'));

		$basepath='';
		if (is_multisite())
		{
			# construct multisite storage directory structure and create if necessary
			$basepath .= 'blogs.dir/' . $wpdb->blogid;
			if (!file_exists(SF_STORE_DIR . '/' . $basepath)) @mkdir(SF_STORE_DIR . '/' . $basepath, 0755);
			$basepath .= '/files';
			if (!file_exists(SF_STORE_DIR . '/' . $basepath)) @mkdir(SF_STORE_DIR . '/' . $basepath, 0755);
			$basepath .= '/';
		}

		$sfconfig['avatar-pool'] 	= $basepath . 'forum-avatar-pool';
		sf_update_option('sfconfig', $sfconfig);

		$newpath = SF_STORE_DIR . '/' . $sfconfig['avatar-pool'];
		@mkdir($newpath, 0777);

		$sfavatars = sf_opcheck(sf_get_option('sfavatars'));
        if (empty($sfavatars))
        {
            # handle case where corrupted avatar priorities in db from old upgrade
    		$sfavatars['sfavatarpriority'] = array(0, 2, 3, 1, 4, 5);  # gravatar, upload, spf, wp, pool, remote
        } else {
            # just add the new priorities
            $sfavatars['sfavatarpriority'][] = 4;  # add the avatar pool at end of avatar priorities
            $sfavatars['sfavatarpriority'][] = 5;  # add the remote avatar at end of avatar priorities
        }
		sf_update_option('sfavatars', $sfavatars);

		# change avatar column to be serialized array of avatar files (uploaded, pool and remote)
		$users = $wpdb->get_results("SELECT user_id, avatar FROM ".SFMEMBERS);
		foreach ($users as $user)
		{
            # see if the avatar is already serialized which means the upgrade script was already run once
            if (!is_serialized($user->avatar))
            {
    			$data = array();
    			$data['uploaded'] = $user->avatar;
    			$wpdb->query("UPDATE ".SFMEMBERS." SET avatar='".serialize($data)."' WHERE user_id=".$user->user_id);
            }
		}

		# update column
		$wpdb->query("ALTER TABLE ".SFFORUMS." CHANGE children children TEXT NULL DEFAULT NULL");

		sf_update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 2903;
	if($build < $section)
	{
		# new permission for viewing member lists
		sf_upgrade_add_new_role('Can view members lists', 0, true);
		sf_delete_option('sfshowmemberlist');

		# avatar filesize limit
		$sfavatars=array();
		$sfavatars = sf_opcheck(sf_get_option('sfavatars'));
		$sfavatars['sfavatarfilesize'] = 10240;  # 10k filesize limit
		sf_update_option('sfavatars', $sfavatars);

		# option to show subforums on group view
		$sfdisplay = array();
		$sfdisplay = sf_opcheck(sf_get_option('sfdisplay'));
		$sfdisplay['groups']['showsubforums'] = true;
		$sfdisplay['pagelinks']['ptop'] = true;
		$sfdisplay['pagelinks']['pbottom'] = true;
		sf_update_option('sfdisplay', $sfdisplay);

		# new permission for re-assigning posts
		sf_upgrade_add_new_role('Can reassign posts', 0, false, true);

		# combine member opts and add option for hiding online status
		$sfmembersopt = array();
		$sfmembersopt['sfcheckformember'] = sf_opcheck(sf_get_option('sfcheckformember'));
		$sfmembersopt['sfshowmemberlist'] = sf_opcheck(sf_get_option('sfshowmemberlist'));
		$sfmembersopt['sflimitmemberlist'] = sf_opcheck(sf_get_option('sflimitmemberlist'));
		$sfmembersopt['sfsinglemembership'] = sf_opcheck(sf_get_option('sfsinglemembership'));
		$sfmembersopt['sfhidestatus'] = true;
		sf_add_option('sfmemberopts', $sfmembersopt);

        # remove the old options
		sf_delete_option('sfcheckformember');
		sf_delete_option('sfshowmemberlist');
		sf_delete_option('sflimitmemberlist');
		sf_delete_option('sfsinglemembership');

		# add feedkey column for members and generate for existing users
		$create_ddl = "ALTER TABLE ".SFMEMBERS. " ADD (feedkey varchar(36) default NULL)";
		sf_upgrade_database(SFMEMBERS, 'feedkey', $create_ddl);
        sf_generate_member_feedkeys();

		# change sftrack for added stats
		$create_ddl = "ALTER TABLE ".SFTRACK. " ADD (forum_id bigint(20) default NULL)";
		sf_upgrade_database(SFTRACK, 'forum_id', $create_ddl);
		$create_ddl = "ALTER TABLE ".SFTRACK. " ADD (topic_id bigint(20) default NULL)";
		sf_upgrade_database(SFTRACK, 'topic_id', $create_ddl);

		sf_update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 2945;
	if($build < $section)
	{
		# Change slugs to text columns to allow for encoding
		$wpdb->query("ALTER TABLE ".SFFORUMS." DROP INDEX fslug_idx;");
		$wpdb->query("ALTER TABLE ".SFFORUMS." MODIFY forum_slug TEXT NOT NULL;");
		$wpdb->query("ALTER TABLE ".SFTOPICS." DROP INDEX tslug_idx;");
		$wpdb->query("ALTER TABLE ".SFTOPICS." MODIFY topic_slug TEXT NOT NULL;");
		$wpdb->query("ALTER TABLE ".SFMESSAGES." DROP INDEX mslug_idx;");
		$wpdb->query("ALTER TABLE ".SFMESSAGES." MODIFY message_slug TEXT NOT NULL;");
		$wpdb->query("ALTER TABLE ".SFTAGS." MODIFY tag_slug varchar(200) NOT NULL;");

		# Add new profile option for profile in stats names
		$sfprofile = array();
		$sfprofile = sf_opcheck(sf_get_option('sfprofile'));
		$sfprofile['profileinstats'] = true;
		sf_update_option('sfprofile', $sfprofile);

		# new blog link table
		$sql = "
			CREATE TABLE IF NOT EXISTS ".SFLINKS." (
				id int(4) NOT NULL auto_increment,
				post_id int(20) default '0',
				forum_id int(20) default '0',
				topic_id int(20) default '0',
				syncedit smallint(1) default '0',
				PRIMARY KEY (id)
			) ENGINE=MyISAM ".sf_charset().";";
		$wpdb->query($sql);

		sf_upgrade_bloglinks_table();

		$sflogin = array();
		$sflogin = sf_opcheck(sf_get_option('sflogin'));
		$sflogin['sfshowavatar'] = true;
		sf_update_option('sflogin', $sflogin);

		# rss options
		$sfrss = array();
		$sfrss['sfrsscount'] = sf_opcheck(sf_get_option('sfrsscount'));
		$sfrss['sfrsswords'] = sf_opcheck(sf_get_option('sfrsswords'));
		$sfrss['sfrssfeedkey'] = true;
		sf_add_option('sfrss', $sfrss);

    	sf_delete_option('sfrsscount');
		sf_delete_option('sfrsswords');

		# New blog/topic linking options
		$sfpostlinking = array();
		$sfpostlinking = sf_opcheck(sf_get_option('sfpostlinking'));
		$sfpostlinking['sfuseautolabel']=true;
		$sfpostlinking['sfautoupdate']=true;
		$sfpostlinking['sfautocreate']=false;
		$sfpostlinking['sfautoforum']='';

		$sfpostlinking['sfpostcomment']=false;
		$sfpostlinking['sfkillcomment']=false;
		$sfpostlinking['sfeditcomment']=false;

		sf_update_option('sfpostlinking', $sfpostlinking);

		$sfsupport = array();
		$sfsupport = sf_opcheck(sf_get_option('sfsupport'));
		$sfsupport['sfusinglinkcomments']=true;
		sf_update_option('sfsupport', $sfsupport);

		sf_update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 3004;
	if($build < $section)
	{
		# new display options
		# option to show subforums on group view
		$sfdisplay = array();
		$sfdisplay = sf_opcheck(sf_get_option('sfdisplay'));
		$sfdisplay['unreadcount']['unread']	= true;
		$sfdisplay['unreadcount']['markall'] = true;
		sf_update_option('sfdisplay', $sfdisplay);

		# Comment id for linking
		$create_ddl = "ALTER TABLE ".SFPOSTS. " ADD (comment_id bigint(20) default NULL)";
		sf_upgrade_database(SFPOSTS, 'comment_id', $create_ddl);

		# open mode of tb uploader
		$sfuploads = array();
		$sfuploads = sf_opcheck(sf_get_option('sfuploads'));
		$sfuploads['showmode'] = false;
		sf_update_option('sfuploads', $sfuploads);

		# add show title to group view
		$sfdisplay = sf_opcheck(sf_get_option('sfdisplay'));
		$sfdisplay['forums']['showtitle'] = false;
		sf_update_option('sfdisplay', $sfdisplay);

		# sitemap inclusion
		$create_ddl = "ALTER TABLE ".SFFORUMS." ADD (in_sitemap smallint(1) NOT NULL default '1')";
		sf_upgrade_database(SFFORUMS, 'in_sitemap', $create_ddl);

		# fix up topic titles
        $sql = "UPDATE ".SFTOPICS." SET topic_name = REPLACE (topic_name, '\\\\\'', '\'') WHERE topic_name LIKE '%\\\\\\\\\'%'";
        $wpdb->query($sql);
        $sql = "UPDATE ".SFTOPICS." SET topic_name = REPLACE (topic_name, '\\\\\"', '\"') WHERE topic_name LIKE '%\\\\\\\\\"%'";
        $wpdb->query($sql);

        # fix up post content
        $sql = "UPDATE ".SFPOSTS." SET post_content = REPLACE (post_content, '\\\\\'', '\'') WHERE post_content LIKE '%\\\\\\\\\'%'";
        $wpdb->query($sql);
        $sql = "UPDATE ".SFPOSTS." SET post_content = REPLACE (post_content, '\\\\\"', '\"') WHERE post_content LIKE '%\\\\\\\\\"%'";
        $wpdb->query($sql);

		sf_update_option('sfbuild', $section);
		echo $section;
		die();
 	}

	$section = 3020;
	if($build < $section)
	{
		# policy storage
		$sfconfig = sf_opcheck(sf_get_option('sfconfig'));

		$basepath='';
		if (is_multisite())
		{
			# construct multisite storage directory structure and create if necessary
			$basepath .= 'blogs.dir/' . $wpdb->blogid;
			if (!file_exists(SF_STORE_DIR . '/' . $basepath)) @mkdir(SF_STORE_DIR . '/' . $basepath, 0755);
			$basepath .= '/files';
			if (!file_exists(SF_STORE_DIR . '/' . $basepath)) @mkdir(SF_STORE_DIR . '/' . $basepath, 0755);
			$basepath .= '/';
		}

		$sfconfig['policies'] = $basepath . 'forum-policies';
		sf_update_option('sfconfig', $sfconfig);

		$newpath = SF_STORE_DIR . '/' . $sfconfig['policies'];
		@mkdir($newpath, 0777);

		# new policy option collection
		$sflogin = array();
		$sflogin = sf_opcheck(sf_get_option('sflogin'));

		$sfpolicy = array();
		$sfpolicy['sfregtext'] = sf_opcheck($sflogin['sfregtext']);
		$sfpolicy['sfregcheck'] = sf_opcheck($sflogin['sfregcheck']);
		$sfpolicy['sfregfile'] = '';
		$sfpolicy['sfreglink'] = false;
		$sfpolicy['sfprivfile'] = '';
		$sfpolicy['sfprivlink'] = false;
		sf_add_option('sfpolicy', $sfpolicy);

		sf_update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 3022;
	if($build < $section)
	{
		# remove topic sort column from topic table
		$wpdb->query("ALTER TABLE ".SFTOPICS." DROP topic_sort;");

		# New image array
		$sfimage = array();
		$sfimage['enlarge'] = sf_opcheck(sf_get_option('sfimgenlarge'));
		$sfimage['thumbsize'] = sf_opcheck(sf_get_option('sfthumbsize'));
		$sfimage['style'] = 'left';
		sf_add_option('sfimage', $sfimage);

		sf_delete_option('sfimgenlarge');
		sf_delete_option('sfthumbsize');

		# new admin colours defaults
		$sfacolours = array();
		$sfacolours = sf_opcheck(sf_get_option('sfacolours'));
    	$sfacolours['submitbg'] = '27537A';
    	$sfacolours['submitbgt'] = 'FFFFFF';
		sf_update_option('sfacolours', $sfacolours);

        # update all admin specific colors
        sf_update_admin_colors();

		# Dupe post filters
		$sffilters = array();
		$sffilters = sf_opcheck(sf_get_option('sffilters'));
		$sffilters['sfdupemember'] = 0;
		$sffilters['sfdupeguest'] = 0;
		sf_update_option('sffilters', $sffilters);

        # move admin colors from usermeta to admin options
        sf_move_admin_colors();

		sf_add_option('sfbuildsitemap', 2);  # rebuild sitemap on new topics

		# blog linking comments
		$sfpostlinking = array();
		$sfpostlinking = sf_opcheck(sf_get_option('sfpostlinking'));
		if($sfpostlinking['sflinkcomments'])
		{
			$sfpostlinking['sflinkcomments'] = 2;
		} else {
			$sfpostlinking['sflinkcomments'] = 1;
		}
		sf_update_option('sfpostlinking', $sfpostlinking);

		sf_update_option('sfbuild', $section);
		echo $section;
		die();
	}

    # 4.2 beta 2 =====================================================================================

	$section = 3491;
	if($build < $section)
	{
		# default filter shortcodes to on
		sf_update_option('sffiltershortcodes', true);

		# make room for buddypress profile display and edit modes
		$profile = sf_get_option('sfprofile');
        if ($profile['displaymode'] == 3) $sfprofile['displaymode'] = 4;
        if ($profile['formmode'] == 3) $sfprofile['formmode'] = 4;
		sf_update_option('sfprofile', $profile);

		# correct pm flag set to 0 issue in 4.2 beta 1
        $members = $wpdb->get_results("SELECT * FROM ".SFMEMBERS." WHERE pm=0");
        sfc_rebuild_members_pm($members);

        # poster ip size to handle ip6
		$sql = "ALTER TABLE ".SFPOSTS." MODIFY poster_ip varchar(39) NOT NULL";
		$wpdb->query($sql);

        # default uploads FM tab
		$sfuploads = sf_get_option('sfuploads');
		$sfuploads['deftab'] = 2;  # upload tab
		sf_update_option('sfuploads', $sfuploads);

        # force upgrade needs to be set every time
		sf_delete_option('sfforceupgrade');

		# custom filter storage
		$sfconfig = sf_opcheck(sf_get_option('sfconfig'));
		$sfconfig['filters'] = $basepath . 'plugins/simple-forum/library';
		sf_update_option('sfconfig', $sfconfig);

		# seo for other forum pages
		$sfseo = sf_get_option('sfseo');
		$sfseo['sfseo_page'] = true;
		sf_update_option('sfseo', $sfseo);

		sf_update_option('sfbuild', $section);
		echo $section;
		die();
	}

    # 4.3 =====================================================================================

	$section = 3723;
	if($build < $section)
	{
		# add new Can Use Spoilers permission
		sf_upgrade_add_new_role('Can use spoilers', 1, true);

		# add new Can View Links permission
		sf_upgrade_add_new_role('Can view links', 1, false, false, true);

        # init no links message
    	$sffilters = sf_get_option('sffilters');
		$sffilters['sfnolinksmsg'] = "<b>** you don't have permission to see this link **</b>";
    	sf_update_option('sffilters', $sffilters);

		# remove signature image permission and put sig image in sig
		sf_convert_sig_images();

		# remove signature image permission and put sig image in sig
		sf_convert_block_admin();

        # default for topic title location
		$sfdisplay = sf_opcheck(sf_get_option('sfdisplay'));
		$sfdisplay['forums']['showtitletop'] = false;
		sf_update_option('sfdisplay', $sfdisplay);

		# member view permissions
		$sfmembersopt = sf_get_option('sfmemberopts');
		$sfmembersopt['sfviewperm'] = true;
		sf_add_option('sfmemberopts', $sfmembersopt);

		sf_update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 3778;
	if($build < $section)
	{
		# add user id to new user list
		sf_upgrade_new_user_list();

		sf_update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 3783;
	if($build < $section)
	{
		# blog linking comments - hide dupes
		$sfpostlinking = array();
		$sfpostlinking = sf_opcheck(sf_get_option('sfpostlinking'));
		$sfpostlinking['sfhideduplicate'] = true;
		sf_update_option('sfpostlinking', $sfpostlinking);

		# post tip on topic links
		$sfdisplay = array();
		$sfdisplay = sf_get_option('sfdisplay');
		$sfdisplay['topics']['posttip']	= true;
		sf_update_option('sfdisplay', $sfdisplay);

		# second toolbar row
		sf_add_option('sftbextras', '');

		sf_update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 3856;
	if($build < $section)
	{
		# print topic option
		$sfdisplay = array();
		$sfdisplay = sf_get_option('sfdisplay');
		$sfdisplay['topics']['print'] = true;
		sf_update_option('sfdisplay', $sfdisplay);

		sf_update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 3907;
	if($build < $section)
	{
		# remove the page title include option - not used anymore
		$sfdisplay = array();
		$sfdisplay = sf_get_option('sfdisplay');
        unset($sfdisplay['pagetitle']['include']);
		sf_update_option('sfdisplay', $sfdisplay);

		sf_update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 3916;
	if($build < $section)
	{
		# remove the page title include option - not used anymore
		$sfpostlinking = array();
		$sfpostlinking = sf_get_option('sfpostlinking');
		$sfpostlinking['sflinkurls'] = 1; # each get their own canonical url
    	sf_add_option('sfpostlinking', $sfpostlinking);

		sf_update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 3925;
	if($build < $section)
	{
		# syntax highlighting
		$syntax = array();
		$syntax['sfsyntaxforum'] = false;
		$syntax['sfsyntaxblog']  = false;
		$syntax['sfbrushes'] = 'apache,applescript,asm,bash-script,bash,basic,clang,css,diff,html,javascript,lisp,ooc,php,python,ruby,sql';
		sf_add_option('sfsyntax', $syntax);

		sf_update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 3954;
	if($build < $section)
	{
		$sftwitter = array();
		$sftwitter['sftwitterfollow'] = true;
		sf_add_option('sftwitter', $sftwitter);

		$sfdisplay = array();
		$sfdisplay = sf_get_option('sfdisplay');
    	$sfdisplay['forums']['pinned'] = false;
    	sf_add_option('sfdisplay', $sfdisplay);

		sf_update_option('sfbuild', $section);
		echo $section;
		die();
    }

	$section = 4044;
	if($build < $section)
	{
        $sfmobile = array();
        $sfmobile['browsers'] = '2.0 MMP, 240x320, 400X240, AvantGo, BlackBerry, Blazer, Cellphone, Danger, DoCoMo, Elaine/3.0, EudoraWeb, Googlebot-Mobile, hiptop, IEMobile, KYOCERA/WX310K, LG/U990, MIDP-2., MMEF20, MOT-V, NetFront, Newt, Nintendo Wii, Nitro, Nokia, Opera Mini, Palm, PlayStation Portable, portalmmm, Proxinet, ProxiNet, SHARP-TQ-GX10, SHG-i900, Small, SonyEricsson, Symbian OS, SymbianOS, TS21i-10, UP.Browser, UP.Link, webOS, Windows CE, WinWAP, YahooSeeker/M1A1-R2D2';
        $sfmobile['touch'] = 'iPhone, iPod, Android, BlackBerry9530, LG-TU915 Obigo, LGE VX, webOS, Nokia5800';
		sf_add_option('sfmobile', $sfmobile);

		sf_update_option('sfbuild', $section);
		echo $section;
		die();
    }

	$section = 4072;
	if($build < $section)
	{
		$sfdisplay = array();
		$sfdisplay = sf_get_option('sfdisplay');
    	$sfdisplay['posts']['sffbconnect'] = true;
		$sfdisplay['posts']['sfmyspace'] = true;
		sf_add_option('sfdisplay', $sfdisplay);

		sf_update_option('sfbuild', $section);
		echo $section;
		die();
    }

	$section = 4124;
	if($build < $section)
	{
		# remove old options still around
		sf_delete_option('sfsmilies');
		sf_delete_option('sfshowavatars');
		sf_delete_option('sfextprofile');
		sf_delete_option('sfgravatar');
		sf_delete_option('sfpmemail');
		sf_delete_option('sfpmmax');
		sf_delete_option('sfpostpaging');
		sf_delete_option('sfavataruploads');
		sf_delete_option('sfgmaxrating');
		sf_delete_option('sfwpavatar');

		sf_update_option('sfbuild', $section);
		echo $section;
		die();
    }

    # 4.3.1 =====================================================================================

	$section = 4200;
	if($build < $section)
	{
		# post content width
		$sfpostwrap = array();
		$sfpostwrap['postwrap']=false;
		$sfpostwrap['postwidth']=0;
		sf_add_option('sfpostwrap', $sfpostwrap);

		sf_update_option('sfbuild', $section);
		echo $section;
		die();
	}

    # 4.3.2 =====================================================================================

	$section = 4220;
	if($build < $section)
	{
		# filter wp_list_pages
		sf_add_option('sfwplistpages', true);

		# Script in footer
		sf_add_option('sfscriptfoot', true);

		sf_update_option('sfbuild', $section);
		echo $section;
		die();
	}

    # 4.3.3 =====================================================================================

	$section = 4312;
	if($build < $section)
	{
		# add new Can Upload Signatures permission
		sf_upgrade_add_new_role('Can upload signatures', 1, true);

		# add new profile display option (needs to maybe massage data)
		$sfprofile = array();
		$sfprofile = sf_get_option('sfprofile');
		if($sfprofile['displaymode'] == 4)
		{
			$sfprofile['displaymode'] = 5;
			sf_update_option('sfprofile', $sfprofile);
		}

		# add show all nested child forums
		$sfdisplay = array();
		$sfdisplay = sf_get_option('sfdisplay');
		$sfdisplay['groups']['showallsubs']	= false;
		$sfdisplay['groups']['combinesubcount'] = false;
		sf_update_option('sfdisplay', $sfdisplay);

		sf_update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 4376;
	if($build < $section)
	{
		# add new profile field for linkedin
		$sfprofile = array();
		$sfprofile = sf_get_option('sfprofile');
		$sfprofile['require']['linkedin'] = false;
		$sfprofile['include']['linkedin'] = true;
		$sfprofile['display']['linkedin'] = true;
		$sfprofile['label']['linkedin'] = 'LinkedIn';
		sf_update_option('sfprofile', $sfprofile);

		$sfdisplay = array();
		$sfdisplay = sf_get_option('sfdisplay');
    	$sfdisplay['posts']['sflinkedin'] = true;
		sf_update_option('sfdisplay', $sfdisplay);

		$sfimage = array();
		$sfimage = sf_get_option('sfimage');
		$sfimage['process'] = true;
		sf_update_option('sfimage', $sfimage);

		sf_update_option('sfbuild', $section);
		echo $section;
		die();
	}

    # 4.3.4 =====================================================================================

	$section = 4646;
	if($build < $section)
	{
        $sfmobile = array();
		$sfmobile = sf_get_option('sfmobile');
        $sfmobile['touch'].= ', iPad';
		sf_update_option('sfmobile', $sfmobile);

		sf_update_option('sfbuild', $section);
		echo $section;
		die();
    }

	$section = 4696;
	if($build < $section)
	{
		# clean up the user transient records
		sf_transient_cleanup();

		sf_update_option('sfbuild', $section);
		echo $section;
		die();
	}

    # 4.4.0 =====================================================================================

	$section = 5030;
	if($build < $section)
	{
        # user id size to match wp user id
		$sql = "ALTER TABLE ".SFMEMBERS." MODIFY user_id bigint(20) unsigned NOT NULL default '0'";
		$wpdb->query($sql);
		$sql = "ALTER TABLE ".SFMEMBERSHIPS." MODIFY user_id bigint(20) unsigned NOT NULL default '0'";
		$wpdb->query($sql);
		$sql = "ALTER TABLE ".SFMESSAGES." MODIFY from_id bigint(20) unsigned default NULL";
		$wpdb->query($sql);
		$sql = "ALTER TABLE ".SFMESSAGES." MODIFY to_id bigint(20) unsigned default NULL";
		$wpdb->query($sql);
		$sql = "ALTER TABLE ".SFPOSTS." MODIFY user_id bigint(20) unsigned default NULL";
		$wpdb->query($sql);
		$sql = "ALTER TABLE ".SFTOPICS." MODIFY user_id bigint(20) unsigned default NULL";
		$wpdb->query($sql);
		$sql = "ALTER TABLE ".SFTRACK." MODIFY trackuserid bigint(20) unsigned default '0'";
		$wpdb->query($sql);
		$sql = "ALTER TABLE ".SFWAITING." MODIFY user_id bigint(20) unsigned default '0'";
		$wpdb->query($sql);
		$sql = "ALTER TABLE ".SFLOG." MODIFY user_id bigint(20) unsigned NOT NULL";
		$wpdb->query($sql);

		# add key to trackuserid
		$wpdb->query("ALTER TABLE ".SFTRACK." ADD INDEX user_idx (trackuserid);");

		# blog linking for custom post types
		$sflinkposttype = array();
		$sflinkposttype['post'] = true;
		$sflinkposttype['page'] = true;
		sf_add_option('sflinkposttype', $sflinkposttype);

		sf_update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 5098;
	if($build < $section)
	{
        # increase icons to length 50
		$sql = "ALTER TABLE ".SFGROUPS." MODIFY group_icon varchar(50) default NULL";
		$wpdb->query($sql);
		$sql = "ALTER TABLE ".SFFORUMS." MODIFY forum_icon varchar(50) default NULL";
		$wpdb->query($sql);

		sf_update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 5135;
	if($build < $section)
	{
		# Add support for dispalying subforums in topic view
		$sfdisplay = array();
		$sfdisplay = sf_get_option('sfdisplay');
		$sfdisplay['topics']['showsubforums'] = true;
		sf_update_option('sfdisplay', $sfdisplay);

		sf_update_option('sfbuild', $section);
		echo $section;
		die();
	}

	$section = 5336;
	if($build < $section)
	{
		# make sure profile has default label for twitter
    	$sfprofile = sf_get_option('sfprofile');
        if (!array_key_exists('twitter', $sfprofile['label'])) $sfprofile['label']['twitter'] = 'Twitter';
		sf_update_option('sfprofile', $sfprofile);

		sf_update_option('sfbuild', $section);
		echo $section;
		die();
	}

    # 4.4.5 =====================================================================================

	$section = 6060;
	if($build < $section)
	{
		# repair icon array
		$icons = array();
		$icons = sf_get_option('sfshowicon');
		if(!array_key_exists('Mark All Read', $icons)) {
			$icons['Mark All Read']=0;
		}
		if(!array_key_exists('Print this Topic', $icons)) {
			$icons['Print this Topic']=0;
		}
		sf_update_option('sfshowicon', $icons);
	}


	# Finished Upgrades ===============================================================================
	# EVERYTHING BELOW MUST BE AT THE END

	sf_log_event(SFRELEASE, SFVERSION, SFBUILD);

	echo SFBUILD;

	delete_option('sfInstallID'); # use wp option table

	die();
?>