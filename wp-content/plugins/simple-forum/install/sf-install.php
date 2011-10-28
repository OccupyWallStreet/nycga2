<?php
/*
Simple:Press
Main Forum Installer (New Instalations)
$LastChangedDate: 2011-06-05 09:16:54 -0700 (Sun, 05 Jun 2011) $
$Rev: 6253 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

global $current_user, $wpdb;

$InstallID = get_option('sfInstallID'); # use wp option table
wp_set_current_user($InstallID);

# use WP check here since SPF stuff wont be set up
if(!current_user_can('activate_plugins'))
{
    echo (__('Access Denied - Only Users who can Activate Plugins may perform this installation', 'sforum'));
    die();
}

sf_setup_sitewide_constants();
sf_setup_global_constants();

require_once (dirname(__FILE__).'/sf-upgrade-support.php');
require_once (dirname(__FILE__).'/../admin/library/sfa-support.php');

if(isset($_GET['phase']))
{
	$phase = sf_esc_int($_GET['phase']);
	if($phase == 0)
	{
		echo '<h5>'.__("Installing", "sforum").' '.__("Simple:Press", "sforum").'...</h5>';
	} else {
		if (isset($_GET['subphase']))
		{
			$subphase = sf_esc_int($_GET['subphase']);
		}
	}
	sf_perform_install($phase, $subphase);
}
die();

function sf_perform_install($phase, $subphase=0)
{
	global $wpdb, $current_user;

	switch($phase)
	{
		case 1:
				# CREATE FORUM TABLES ----------------------------------

				# sfforums table def
				$sql = "
					CREATE TABLE IF NOT EXISTS ".SFFORUMS." (
						forum_id bigint(20) NOT NULL auto_increment,
						forum_name varchar(200) NOT NULL,
						group_id bigint(20) NOT NULL,
						forum_seq int(4) default NULL,
						forum_desc text default NULL,
						forum_status int(4) NOT NULL default '0',
						forum_slug varchar(200) NOT NULL,
						forum_rss text default NULL,
						forum_icon varchar(50) default NULL,
						post_id bigint(20) default NULL,
						topic_count mediumint(8) default '0',
						post_count mediumint(8) default '0',
						forum_rss_private smallint(1) NOT NULL default '0',
						in_sitemap smallint(1) NOT NULL default '1',
						topic_status_set bigint(20) default '0',
						post_ratings smallint(1) NOT NULL default '0',
						use_tags smallint(1) NOT NULL default '1',
						parent bigint(20) NOT NULL default '0',
						children text default NULL,
						forum_message text,
						PRIMARY KEY  (forum_id),
						KEY groupf_idx (group_id),
						KEY fslug_idx (forum_slug)
					) ENGINE=MyISAM ".sf_charset().";";
				$wpdb->query($sql);

				# sfgroups table def
				$sql = "
					CREATE TABLE IF NOT EXISTS ".SFGROUPS." (
						group_id bigint(20) NOT NULL auto_increment,
						group_name text,
						group_seq int(4) default NULL,
						group_desc text,
						group_rss text,
						group_icon varchar(50) default NULL,
						group_message text,
						PRIMARY KEY  (group_id)
					) ENGINE=MyISAM ".sf_charset().";";
				$wpdb->query($sql);

				# sfmembers table def
				$sql = "
					CREATE TABLE IF NOT EXISTS ".SFMEMBERS." (
						user_id bigint(20) unsigned NOT NULL default '0',
						display_name varchar(100) default NULL,
						pm smallint(1) NOT NULL default '0',
						moderator smallint(1) NOT NULL default '0',
						avatar longtext default NULL,
						signature text default NULL,
						posts int(4) default NULL,
						lastvisit datetime default NULL,
						subscribe longtext,
						buddies longtext,
						newposts longtext,
						checktime datetime default NULL,
						admin smallint(1) NOT NULL default '0',
						watches longtext,
						posts_rated longtext,
						feedkey varchar(36) default NULL,
						admin_options longtext default NULL,
						user_options longtext default NULL,
						PRIMARY KEY  (user_id)
					) ENGINE=MyISAM ".sf_charset().";";
				$wpdb->query($sql);

				$sql = "
					CREATE TABLE IF NOT EXISTS ".SFOPTIONS." (
					option_id bigint(20) unsigned NOT NULL auto_increment,
					option_name varchar(64) NOT NULL default '',
					option_value longtext NOT NULL,
					PRIMARY KEY (option_name),
					KEY option_id (option_id)
					) ENGINE=MyISAM ".sf_charset().";";
				$wpdb->query($sql);

				# sfmemberships table def
				$sql = "
					CREATE TABLE IF NOT EXISTS ".SFMEMBERSHIPS." (
						membership_id mediumint(8) unsigned NOT NULL auto_increment,
						user_id bigint(20) unsigned NOT NULL default '0',
						usergroup_id mediumint(8) unsigned NOT NULL default '0',
						PRIMARY KEY  (membership_id)
					) ENGINE=MyISAM ".sf_charset().";";
				$wpdb->query($sql);

				# sfmessages table def
				$sql = "
					CREATE TABLE IF NOT EXISTS ".SFMESSAGES." (
						message_id bigint(20) NOT NULL auto_increment,
						sent_date datetime NOT NULL,
						from_id bigint(20) unsigned default NULL,
						to_id bigint(20) unsigned default NULL,
						title varchar(200) default NULL,
						type smallint(2) NOT NULL default '1',
						message text,
						message_status smallint(1) NOT NULL default '0',
						inbox smallint(1) NOT NULL default '1',
						sentbox smallint(1) NOT NULL default '1',
						is_reply smallint(1) NOT NULL default '0',
						message_slug varchar(200) NOT NULL,
						PRIMARY KEY  (message_id),
						KEY mslug_idx (message_slug)
					) ENGINE=MyISAM ".sf_charset().";";
				$wpdb->query($sql);

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

				# sfnotice table def
				$sql = "
					CREATE TABLE IF NOT EXISTS ".SFNOTICE." (
						id varchar(30) NOT NULL,
						item varchar(15) default NULL,
						message longtext,
						ndate datetime NOT NULL,
						PRIMARY KEY  (id)
					) ENGINE=MyISAM ".sf_charset().";";
				$wpdb->query($sql);

				# sfpermissions table def
				$sql = "
					CREATE TABLE IF NOT EXISTS ".SFPERMISSIONS." (
						permission_id mediumint(8) unsigned NOT NULL auto_increment,
						forum_id mediumint(8) unsigned NOT NULL default '0',
						usergroup_id mediumint(8) unsigned NOT NULL default '0',
						permission_role mediumint(8) unsigned NOT NULL default '0',
						PRIMARY KEY  (permission_id)
					) ENGINE=MyISAM ".sf_charset().";";
				$wpdb->query($sql);

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

				# sfposts table def
				$sql = "
					CREATE TABLE IF NOT EXISTS ".SFPOSTS." (
						post_id bigint(20) NOT NULL auto_increment,
						post_content text,
						post_date datetime NOT NULL,
						topic_id bigint(20) NOT NULL,
						user_id bigint(20) unsigned default NULL,
						forum_id bigint(20) NOT NULL,
						guest_name varchar(20) default NULL,
						guest_email varchar(50) default NULL,
						post_status int(4) NOT NULL default '0',
						post_pinned smallint(1) NOT NULL default '0',
						post_index mediumint(8) default '0',
						post_edit mediumtext,
						poster_ip varchar(39) NOT NULL,
						comment_id bigint(20) default NULL,
						PRIMARY KEY  (post_id),
						KEY topicp_idx (topic_id),
						KEY forump_idx (forum_id),
						FULLTEXT KEY post_content (post_content)
					) ENGINE=MyISAM ".sf_charset().";";
				$wpdb->query($sql);

				# sfroles table def
				$sql = "
					CREATE TABLE IF NOT EXISTS ".SFROLES." (
						role_id mediumint(8) unsigned NOT NULL auto_increment,
						role_name varchar(50) NOT NULL default '',
						role_desc varchar(150) NOT NULL default '',
						role_actions longtext NOT NULL,
						PRIMARY KEY  (role_id)
					) ENGINE=MyISAM ".sf_charset().";";
				$wpdb->query($sql);

				# sfsettings table def
				$sql = "
					CREATE TABLE IF NOT EXISTS ".SFSETTINGS." (
						setting_id bigint(20) NOT NULL auto_increment,
						setting_name varchar(50) NOT NULL,
						setting_value longtext,
						setting_date datetime NOT NULL,
						PRIMARY KEY  (setting_id)
					) ENGINE=MyISAM ".sf_charset().";";
				$wpdb->query($sql);

				# sftopics table def
				$sql = "
					CREATE TABLE IF NOT EXISTS ".SFTOPICS." (
						topic_id bigint(20) NOT NULL auto_increment,
						topic_name varchar(200) NOT NULL,
						topic_date datetime NOT NULL,
						topic_status int(4) NOT NULL default '0',
						forum_id bigint(20) NOT NULL,
						user_id bigint(20) unsigned default NULL,
						topic_pinned smallint(1) NOT NULL default '0',
						topic_subs longtext,
						topic_opened bigint(20) NOT NULL default '0',
						blog_post_id bigint(20) NOT NULL default '0',
						topic_slug varchar(200) NOT NULL,
						post_id bigint(20) default NULL,
						post_count mediumint(8) default '0',
						topic_status_flag bigint(20) default '0',
						topic_watches longtext,
						PRIMARY KEY  (topic_id),
						KEY forumt_idx (forum_id),
						KEY tslug_idx (topic_slug),
						FULLTEXT KEY topic_name_idx (topic_name)
					) ENGINE=MyISAM ".sf_charset().";";
				$wpdb->query($sql);

				# sftrack table def
				$sql = "
					CREATE TABLE IF NOT EXISTS ".SFTRACK." (
						id bigint(20) NOT NULL auto_increment,
						trackuserid bigint(20) unsigned default '0',
						trackname varchar(50) NOT NULL,
						trackdate datetime NOT NULL,
                        forum_id bigint(20) default NULL,
                        topic_id bigint(20) default NULL,
						PRIMARY KEY  (id),
						KEY user_idx (trackuserid)
					) ENGINE=MyISAM ".sf_charset().";";
				$wpdb->query($sql);

				# sfusergroups table def
				$sql = "
					CREATE TABLE IF NOT EXISTS ".SFUSERGROUPS." (
						usergroup_id mediumint(8) unsigned NOT NULL auto_increment,
						usergroup_name text NOT NULL,
						usergroup_desc text default NULL,
						usergroup_is_moderator tinyint(4) unsigned NOT NULL default '0',
						PRIMARY KEY  (usergroup_id)
					) ENGINE=MyISAM ".sf_charset().";";
				$wpdb->query($sql);

				# sfwaiting table def
				$sql = "
					CREATE TABLE IF NOT EXISTS ".SFWAITING." (
						topic_id bigint(20) NOT NULL,
						forum_id bigint(20) NOT NULL,
						post_count int(4) NOT NULL,
						post_id bigint(20) NOT NULL default '0',
						user_id bigint(20) unsigned default '0',
						PRIMARY KEY  (topic_id)
					) ENGINE=MyISAM ".sf_charset().";";
				$wpdb->query($sql);

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

				# install log table def
				$sql = "
					CREATE TABLE IF NOT EXISTS ".SFLOG." (
						id int(4) NOT NULL auto_increment,
						user_id bigint(20) unsigned NOT NULL,
						install_date date NOT NULL,
						release_type varchar(15),
						version varchar(10) NOT NULL,
						build int(4) NOT NULL,
						PRIMARY KEY (id)
                    ) ENGINE=MyISAM ".sf_charset().";";
                $wpdb->query($sql);

				# blog linking table
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

				echo '<h5>'.__("Phase", "sforum").' - '.$phase.' - ';
				echo __("Tables Created", "sforum").'</h5>';
				break;

		case 2:

				# CREATE DEFAULT DATA ----------------------------------

				# Create default role data
				$actions = array();
				$actions['Can view forum'] = 0;
				$actions['Can view forum lists only'] = 0;
				$actions['Can view forum and topic lists only'] = 0;
				$actions['Can view forum and topic lists only'] = 0;
				$actions['Can view admin posts'] = 0;
				$actions['Can reply to topics'] = 0;
				$actions['Can create linked topics'] = 0;
				$actions['Can break linked topics'] = 0;
				$actions['Can edit own topic titles'] = 0;
				$actions['Can edit any topic titles'] = 0;
				$actions['Can pin topics'] = 0;
				$actions['Can move topics'] = 0;
				$actions['Can move posts'] = 0;
				$actions['Can lock topics'] = 0;
				$actions['Can delete topics'] = 0;
				$actions['Can edit own posts forever'] = 0;
				$actions['Can edit own posts until reply'] = 0;
				$actions['Can edit any posts'] = 0;
				$actions['Can delete own posts'] = 0;
				$actions['Can delete any posts'] = 0;
				$actions['Can pin posts'] = 0;
				$actions['Can reassign posts'] = 0;
				$actions['Can view users email addresses'] = 0;
				$actions['Can view members profiles'] = 0;
				$actions['Can view members lists'] = 0;
				$actions['Can report posts'] = 0;
				$actions['Can sort most recent posts'] = 0;
				$actions['Can bypass spam control'] = 0;
				$actions['Can bypass post moderation'] = 0;
				$actions['Can bypass post moderation once'] = 0;
				$actions['Can upload images'] = 0;
				$actions['Can upload media'] = 0;
				$actions['Can upload files'] = 0;
				$actions['Can use signatures'] = 0;
				$actions['Can upload signatures'] = 0;
				$actions['Can upload avatars'] = 0;
				$actions['Can use private messaging'] = 0;
				$actions['Can subscribe'] = 0;
				$actions['Can watch topics'] = 0;
				$actions['Can change topic status'] = 0;
				$actions['Can rate posts'] = 0;
				$actions['Can use spoilers'] = 0;
				$actions['Can view links'] = 0;
				$actions['Can moderate pending posts'] = 0;
				$role_name = 'No Access';
				$role_desc = 'Permission with no access to any Forum features.';
				sfa_create_role_row($role_name, $role_desc, serialize($actions));

				$actions = array();
				$actions['Can view forum'] = 1;
				$actions['Can view forum lists only'] = 0;
				$actions['Can view forum and topic lists only'] = 0;
				$actions['Can view admin posts'] = 1;
				$actions['Can start new topics'] = 0;
				$actions['Can reply to topics'] = 0;
				$actions['Can create linked topics'] = 0;
				$actions['Can break linked topics'] = 0;
				$actions['Can edit own topic titles'] = 0;
				$actions['Can edit any topic titles'] = 0;
				$actions['Can pin topics'] = 0;
				$actions['Can move topics'] = 0;
				$actions['Can move posts'] = 0;
				$actions['Can lock topics'] = 0;
				$actions['Can delete topics'] = 0;
				$actions['Can edit own posts forever'] = 0;
				$actions['Can edit own posts until reply'] = 0;
				$actions['Can edit any posts'] = 0;
				$actions['Can delete own posts'] = 0;
				$actions['Can delete any posts'] = 0;
				$actions['Can pin posts'] = 0;
				$actions['Can reassign posts'] = 0;
				$actions['Can view users email addresses'] = 0;
				$actions['Can view members profiles'] = 0;
				$actions['Can view members lists'] = 0;
				$actions['Can report posts'] = 0;
				$actions['Can sort most recent posts'] = 0;
				$actions['Can bypass spam control'] = 0;
				$actions['Can bypass post moderation'] = 0;
				$actions['Can bypass post moderation once'] = 0;
				$actions['Can upload images'] = 0;
				$actions['Can upload media'] = 0;
				$actions['Can upload files'] = 0;
				$actions['Can use signatures'] = 0;
				$actions['Can upload signatures'] = 0;
				$actions['Can upload avatars'] = 0;
				$actions['Can use private messaging'] = 0;
				$actions['Can subscribe'] = 0;
				$actions['Can watch topics'] = 0;
				$actions['Can change topic status'] = 0;
				$actions['Can rate posts'] = 0;
				$actions['Can use spoilers'] = 1;
				$actions['Can view links'] = 1;
				$actions['Can moderate pending posts'] = 0;
				$role_name = 'Read Only Access';
				$role_desc = 'Permission with access to only view the Forum.';
				sfa_create_role_row($role_name, $role_desc, serialize($actions));

				$actions = array();
				$actions['Can view forum'] = 1;
				$actions['Can view forum lists only'] = 0;
				$actions['Can view forum and topic lists only'] = 0;
				$actions['Can view admin posts'] = 1;
				$actions['Can start new topics'] = 1;
				$actions['Can reply to topics'] = 1;
				$actions['Can create linked topics'] = 0;
				$actions['Can break linked topics'] = 0;
				$actions['Can edit own topic titles'] = 0;
				$actions['Can edit any topic titles'] = 0;
				$actions['Can pin topics'] = 0;
				$actions['Can move topics'] = 0;
				$actions['Can move posts'] = 0;
				$actions['Can lock topics'] = 0;
				$actions['Can delete topics'] = 0;
				$actions['Can edit own posts forever'] = 0;
				$actions['Can edit own posts until reply'] = 1;
				$actions['Can edit any posts'] = 0;
				$actions['Can delete own posts'] = 0;
				$actions['Can delete any posts'] = 0;
				$actions['Can pin posts'] = 0;
				$actions['Can reassign posts'] = 0;
				$actions['Can view users email addresses'] = 0;
				$actions['Can view members profiles'] = 1;
				$actions['Can view members lists'] = 1;
				$actions['Can report posts'] = 1;
				$actions['Can sort most recent posts'] = 0;
				$actions['Can bypass spam control'] = 0;
				$actions['Can bypass post moderation'] = 0;
				$actions['Can bypass post moderation once'] = 0;
				$actions['Can upload images'] = 0;
				$actions['Can upload media'] = 0;
				$actions['Can upload files'] = 0;
				$actions['Can use signatures'] = 0;
				$actions['Can upload signatures'] = 1;
				$actions['Can upload avatars'] = 1;
				$actions['Can use private messaging'] = 0;
				$actions['Can subscribe'] = 1;
				$actions['Can watch topics'] = 1;
				$actions['Can change topic status'] = 0;
				$actions['Can rate posts'] = 1;
				$actions['Can use spoilers'] = 1;
				$actions['Can view links'] = 1;
				$actions['Can moderate pending posts'] = 0;
				$role_name = 'Limited Access';
				$role_desc = 'Permission with access to reply and start topics but with limited features.';
				sfa_create_role_row($role_name, $role_desc, serialize($actions));

				$actions = array();
				$actions['Can view forum'] = 1;
				$actions['Can view forum lists only'] = 0;
				$actions['Can view forum and topic lists only'] = 0;
				$actions['Can view admin posts'] = 1;
				$actions['Can start new topics'] = 1;
				$actions['Can reply to topics'] = 1;
				$actions['Can create linked topics'] = 0;
				$actions['Can break linked topics'] = 0;
				$actions['Can edit own topic titles'] = 0;
				$actions['Can edit any topic titles'] = 0;
				$actions['Can pin topics'] = 0;
				$actions['Can move topics'] = 0;
				$actions['Can move posts'] = 0;
				$actions['Can lock topics'] = 0;
				$actions['Can delete topics'] = 0;
				$actions['Can edit own posts forever'] = 0;
				$actions['Can edit own posts until reply'] = 1;
				$actions['Can edit any posts'] = 0;
				$actions['Can delete own posts'] = 0;
				$actions['Can delete any posts'] = 0;
				$actions['Can pin posts'] = 0;
				$actions['Can reassign posts'] = 0;
				$actions['Can view users email addresses'] = 0;
				$actions['Can view members profiles'] = 1;
				$actions['Can view members lists'] = 1;
				$actions['Can report posts'] = 1;
				$actions['Can sort most recent posts'] = 0;
				$actions['Can bypass spam control'] = 0;
				$actions['Can bypass post moderation'] = 1;
				$actions['Can bypass post moderation once'] = 1;
				$actions['Can upload images'] = 0;
				$actions['Can upload media'] = 0;
				$actions['Can upload files'] = 0;
				$actions['Can use signatures'] = 1;
				$actions['Can upload signatures'] = 1;
				$actions['Can upload avatars'] = 1;
				$actions['Can use private messaging'] = 1;
				$actions['Can subscribe'] = 1;
				$actions['Can watch topics'] = 1;
				$actions['Can change topic status'] = 0;
				$actions['Can rate posts'] = 1;
				$actions['Can use spoilers'] = 1;
				$actions['Can view links'] = 1;
				$actions['Can moderate pending posts'] = 0;
				$role_name = 'Standard Access';
				$role_desc = 'Permission with access to reply and start topics with advanced features such as signatures and private messaging.';
				sfa_create_role_row($role_name, $role_desc, serialize($actions));

				$actions = array();
				$actions['Can view forum'] = 1;
				$actions['Can view forum lists only'] = 0;
				$actions['Can view forum and topic lists only'] = 0;
				$actions['Can view admin posts'] = 1;
				$actions['Can start new topics'] = 1;
				$actions['Can reply to topics'] = 1;
				$actions['Can create linked topics'] = 0;
				$actions['Can break linked topics'] = 0;
				$actions['Can edit own topic titles'] = 1;
				$actions['Can edit any topic titles'] = 0;
				$actions['Can pin topics'] = 0;
				$actions['Can move topics'] = 0;
				$actions['Can move posts'] = 0;
				$actions['Can lock topics'] = 0;
				$actions['Can delete topics'] = 0;
				$actions['Can edit own posts forever'] = 1;
				$actions['Can edit own posts until reply'] = 1;
				$actions['Can edit any posts'] = 0;
				$actions['Can delete own posts'] = 0;
				$actions['Can delete any posts'] = 0;
				$actions['Can pin posts'] = 0;
				$actions['Can reassign posts'] = 0;
				$actions['Can view users email addresses'] = 0;
				$actions['Can view members profiles'] = 1;
				$actions['Can view members lists'] = 1;
				$actions['Can report posts'] = 1;
				$actions['Can sort most recent posts'] = 0;
				$actions['Can bypass spam control'] = 1;
				$actions['Can bypass post moderation'] = 1;
				$actions['Can bypass post moderation once'] = 1;
				$actions['Can upload images'] = 1;
				$actions['Can upload media'] = 1;
				$actions['Can upload files'] = 0;
				$actions['Can use signatures'] = 1;
				$actions['Can upload signatures'] = 1;
				$actions['Can upload avatars'] = 1;
				$actions['Can use private messaging'] = 1;
				$actions['Can subscribe'] = 1;
				$actions['Can watch topics'] = 1;
				$actions['Can change topic status'] = 1;
				$actions['Can rate posts'] = 1;
				$actions['Can use spoilers'] = 1;
				$actions['Can view links'] = 1;
				$actions['Can moderate pending posts'] = 0;
				$role_name = 'Full Access';
				$role_desc = 'Permission with Standard Access features plus image uploading and spam control bypass.';
				sfa_create_role_row($role_name, $role_desc, serialize($actions));

				$actions = array();
				$actions['Can view forum'] = 1;
				$actions['Can view forum lists only'] = 0;
				$actions['Can view forum and topic lists only'] = 0;
				$actions['Can view admin posts'] = 1;
				$actions['Can start new topics'] = 1;
				$actions['Can reply to topics'] = 1;
				$actions['Can create linked topics'] = 1;
				$actions['Can break linked topics'] = 1;
				$actions['Can edit any topic titles'] = 1;
				$actions['Can edit own topic titles'] = 1;
				$actions['Can pin topics'] = 1;
				$actions['Can move topics'] = 1;
				$actions['Can move posts'] = 1;
				$actions['Can lock topics'] = 1;
				$actions['Can delete topics'] = 1;
				$actions['Can edit own posts forever'] = 1;
				$actions['Can edit own posts until reply'] = 1;
				$actions['Can edit any posts'] = 1;
				$actions['Can delete own posts'] = 0;
				$actions['Can delete any posts'] = 1;
				$actions['Can pin posts'] = 1;
				$actions['Can reassign posts'] = 1;
				$actions['Can view users email addresses'] = 1;
				$actions['Can view members profiles'] = 1;
				$actions['Can view members lists'] = 1;
				$actions['Can report posts'] = 1;
				$actions['Can sort most recent posts'] = 1;
				$actions['Can bypass spam control'] = 1;
				$actions['Can bypass post moderation'] = 1;
				$actions['Can bypass post moderation once'] = 1;
				$actions['Can upload images'] = 1;
				$actions['Can upload media'] = 1;
				$actions['Can upload files'] = 0;
				$actions['Can use signatures'] = 1;
				$actions['Can upload signatures'] = 1;
				$actions['Can upload avatars'] = 1;
				$actions['Can use private messaging'] = 1;
				$actions['Can subscribe'] = 1;
				$actions['Can watch topics'] = 1;
				$actions['Can change topic status'] = 1;
				$actions['Can rate posts'] = 1;
				$actions['Can use spoilers'] = 1;
				$actions['Can view links'] = 1;
				$actions['Can moderate pending posts'] = 1;
				$role_name = 'Moderator Access';
				$role_desc = 'Permission with access to all Forum features.';
				sfa_create_role_row($role_name, $role_desc, serialize($actions));

				echo '<h5>'.__("Phase", "sforum").' - '.$phase.' - ';
				echo __("Permission Data Built", "sforum").'</h5>';
				break;

		case 3:
				# Create default 'Guest' user group data
				$guests = sfa_create_usergroup_row('Guests', 'Default Usergroup for guests of the forum.', '0', false);

				# Create default 'Members' user group data
				$members = sfa_create_usergroup_row('Members', 'Default Usergroup for registered users of the forum.', '0', false);

				# Create default 'Moderators' user group data
				$moderators = sfa_create_usergroup_row('Moderators', 'Default Usergroup for moderators of the forum.', '1', false);

				# Create default user groups
				sf_add_sfmeta('default usergroup', 'sfguests', $guests); # default usergroup for guests
				sf_add_sfmeta('default usergroup', 'sfmembers', $members); # default usergroup for members
				sf_create_usergroup_meta($members); # create default usergroups for existing wp roles

				echo '<h5>'.__("Phase", "sforum").' - '.$phase.' - ';
				echo __("User Group Data Built", "sforum").'</h5>';
				break;

		case 4:
				# CREATE NEW PAGE FOR FORUM ----------------------------

				# Create the WP oage for forum
				$wpdb->query(
					"INSERT INTO ".$wpdb->prefix."posts (
					 post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status,
					 comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt,
					 post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count
					 ) VALUES (
					 ".$current_user->ID.", now(), now(), '', 'Forum', '', 'publish', 'closed', 'closed', '', 'forum', '', '', now(), now(), '', 0, '', 0, 'page', '', 0 )");

				# Grab the new page id
				$page_id = $wpdb->insert_id;

				# Update the guid for the new page
				$guid = get_permalink($page_id);
				$wpdb->query("UPDATE {$wpdb->prefix}posts SET guid='".$guid."' WHERE ID=".$page_id);

				sf_add_option('sfpage', $page_id);

				echo '<h5>'.__("Phase", "sforum").' - '.$phase.' - ';
				echo __("Forum Page Created", "sforum").'</h5>';
				break;

		case 5:
				# CREATE OPTION RECORDS --------------------------------

				# Create Base Option Records (V1)
				sf_add_option('sfslug', 'forum');
				sf_add_option('sfuninstall', false);

				# (V1.3)
				sf_add_option('sfdates', get_option('date_format'));
				sf_add_option('sftimes', get_option('time_format'));
				sf_add_option('sfzone', 0);

				# (V1.8)
				sf_add_option('sfpermalink', get_permalink(sf_get_option('sfpage')));

				# (V2.1)
				sf_add_option('sflockdown', false);
				sf_add_option('sfuseannounce', false);
				sf_add_option('sfannouncecount', 8);
				sf_add_option('sfannouncehead', 'Most Recent Forum Posts');
				sf_add_option('sfannounceauto', false);
				sf_add_option('sfannouncetime', 60);
				sf_add_option('sfannouncetext', '%TOPICNAME% posted by %POSTER% in %FORUMNAME% on %DATETIME%');
				sf_add_option('sfannouncelist', false);

				$rankdata['posts'] = 2;
				$rankdata['usergroup'] = 'none';
				$rankdata['image'] = 'none';
				sf_add_sfmeta('forum_rank', 'New Member', serialize($rankdata));
				$rankdata['posts'] = 1000;
				$rankdata['usergroup'] = 'none';
				$rankdata['image'] = 'none';
				sf_add_sfmeta('forum_rank', 'Member', serialize($rankdata));

				# (V3.0)
				$sfimage = array();
				$sfimage['enlarge'] = true;
				$sfimage['process'] = true;
				$sfimage['thumbsize'] = 100;
				$sfimage['style'] = 'left';
				sf_add_option('sfimage', $sfimage);

				sf_add_option('sfbadwords', '');
				sf_add_option('sfreplacementwords', '');
				sf_add_option('sfeditormsg','');
				sf_add_option('sfcheck', true);

				$pm = array();
				$pm['sfpmemail'] = false;
				$pm['sfpmmax'] = 0;
				$pm['sfpmmaxrecipients'] = 0;
				$pm['sfpmcc'] = true;
				$pm['sfpmbcc'] = true;
				sf_add_option('sfpm', $pm);

				$sfmail = array();
				$sfmail['sfmailsender'] = get_bloginfo('name');
				$admin_email = get_bloginfo('admin_email');
				$comp = explode('@', $admin_email);
				$sfmail['sfmailfrom'] = $comp[0];
				$sfmail['sfmaildomain'] = $comp[1];
				$sfmail['sfmailuse'] = true;
				sf_add_option('sfmail', $sfmail);

				$sfmail = array();
				$sfmail['sfusespfreg'] = true;
				$sfmail['sfnewusersubject'] = 'Welcome to %BLOGNAME%';
				$sfmail['sfnewusertext'] = 'Welcome %USERNAME% to %BLOGNAME% %NEWLINE%Please find below your login details: %NEWLINE%Username: %USERNAME% %NEWLINE%Password: %PASSWORD% %NEWLINE%%LOGINURL%';
				sf_add_option('sfnewusermail', $sfmail);

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
				sf_add_option('sfcustom', $sfcustom);

				$sfpostmsg = array();
				$sfpostmsg['sfpostmsgtext'] = '';
				$sfpostmsg['sfpostmsgtopic'] = false;
				$sfpostmsg['sfpostmsgpost'] = false;
				sf_add_option('sfpostmsg', $sfpostmsg);

				# global
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
					'Mark All Read'					=> 1
				);

				sf_add_option('sfshowicon', $icons);

				# (V3.1)
				$sfstyle = array();
				$sfstyle['sfskin'] = 'default';
				$sfstyle['sficon'] = 'default';
				$sfstyle['sfsize'] = '';
				sf_add_option('sfstyle', $sfstyle);

				$sflogin = array();
				$sflogin['sfshowlogin'] = true;
				$sflogin['sfshowreg'] = true;
				$sflogin['sfshowavatar'] = true;
				$sflogin['sfregmath'] = true;
				$sflogin['sfinlogin'] = true;
				$sflogin['sfloginskin'] = true;
				$sflogin['sfloginurl'] = SFSITEURL.'wp-login.php?action=login&amp;view=forum';
				$sflogin['sfloginemailurl'] = SFSITEURL.'wp-login.php?action=login&view=forum';
				$sflogin['sflogouturl'] = SFSITEURL.'wp-login.php?action=logout&amp;redirect_to='.get_permalink(sf_get_option('sfpage'));
				$sflogin['sfregisterurl'] = SFSITEURL.'wp-login.php?action=register&amp;view=forum';
				$sflogin['sflostpassurl'] = SFSITEURL.'wp-login.php?action=lostpassword&amp;view=forum';
				sf_add_option('sflogin', $sflogin);

				$sfpolicy = array();
				$sfpolicy['sfregtext'] = false;
				$sfpolicy['sfregcheck'] = false;
				$sfpolicy['sfregfile'] = '';
				$sfpolicy['sfreglink'] = false;
				$sfpolicy['sfprivfile'] = '';
				$sfpolicy['sfprivlink'] = false;
				sf_add_option('sfpolicy', $sfpolicy);

				$sfadminsettings=array();
				$sfadminsettings['sfmodasadmin']=false;
				$sfadminsettings['sfshowmodposts']=true;
				$sfadminsettings['sftools']=true;
				$sfadminsettings['sfqueue']=true;
				$sfadminsettings['sfbaronly']=false;
				$sfadminsettings['sfdashboardposts']=true;
				$sfadminsettings['sfdashboardstats']=true;
				sf_add_option('sfadminsettings', $sfadminsettings);

				$sfauto=array();
				$sfauto['sfautoupdate']=false;
				$sfauto['sfautotime']=300;
				sf_add_option('sfauto', $sfauto);

				$sffilters=array();
				$sffilters['sfnofollow'] = false;
				$sffilters['sftarget'] = true;
				$sffilters['sfurlchars'] = 40;
				$sffilters['sffilterpre'] = false;
				$sffilters['sfmaxlinks'] = 0;
				$sffilters['sfnolinksmsg'] = "<b>** you don't have permission to see this link **</b>";
                $sffilters['sfdupemember'] = 0;
				$sffilters['sfdupeguest'] = 0;
				sf_add_option('sffilters', $sffilters);

				# (V4.0)
				$sfeditor = array();
				$sfeditor['sfeditor'] = 1;
				$sfeditor['sfusereditor'] = false;
				$sfeditor['sfrejectformat'] = false;
				$sfeditor['sfrelative'] = true;
				$sfeditor['sftmcontentCSS'] = 'content.css';
				$sfeditor['sftmuiCSS'] = 'ui.css';
				$sfeditor['sftmdialogCSS'] = 'dialog.css';
				$sfeditor['SFhtmlCSS'] = 'htmlEditor.css';
				$sfeditor['SFbbCSS'] = 'bbcodeEditor.css';
				$sfeditor['sflang'] = 'en';

				if(get_bloginfo('text_direction') == 'rtl' ? $sfeditor['sfrtl'] = true : $sfeditor['sfrtl'] = false);

				sf_add_option('sfeditor', $sfeditor);

				$sfpostratings = array();
				$sfpostratings['sfpostratings'] = false;
				$sfpostratings['sfratingsstyle'] = 1;
				sf_add_option('sfpostratings', $sfpostratings);

				$sfsmileys = array();
				$sfsmileys['sfsmallow'] = true;
				$sfsmileys['sfsmtype'] = 1;
				sf_add_option('sfsmileys', $sfsmileys);

				sf_add_option('sfprivatemessaging', true);

				$sfseo = array();
				$sfseo['sfseo_topic'] = true;
				$sfseo['sfseo_forum'] = true;
				$sfseo['sfseo_page'] = true;
				$sfseo['sfseo_sep'] = '|';
				sf_add_option('sfseo', $sfseo);

				sf_add_option('sfbuildsitemap', 2);  # rebuild sitemap on new topics

				$sfsigimagesize = array();
				$sfsigimagesize['sfsigwidth'] = 0;
				$sfsigimagesize['sfsigheight'] = 0;
				sf_add_option('sfsigimagesize', $sfsigimagesize);

				sf_add_option('sfcbexclusions', '');

				$sfpostlinking = array();
				$sfpostlinking['sflinkexcerpt'] = 1;
				$sfpostlinking['sflinkwords'] = 100;
				$sfpostlinking['sflinkblogtext'] = '%ICON% Join the forum discussion on this post';
				$sfpostlinking['sflinkforumtext'] = '%ICON% Read original blog post';
				$sfpostlinking['sflinkabove'] = false;
				$sfpostlinking['sflinkcomments'] = 1;
				$sfpostlinking['sfhideduplicate'] = true;
				$sfpostlinking['sfpostcomment'] = false;
				$sfpostlinking['sfkillcomment'] = false;
				$sfpostlinking['sfeditcomment'] = false;
				$sfpostlinking['sflinksingle'] = false;
				$sfpostlinking['sfuseautolabel'] = true;
				$sfpostlinking['sfautoupdate'] = true;
				$sfpostlinking['sfautocreate'] = false;
				$sfpostlinking['sfautoforum'] = '';
				$sfpostlinking['sflinkurls'] = 1; # each get their own canonical url

				sf_add_option('sfpostlinking', $sfpostlinking);
				$sflinkposttype = array();
				$sflinkposttype['post'] = true;
				$sflinkposttype['page'] = true;
				sf_add_option('sflinkposttype', $sflinkposttype);

				sf_build_tinymce_toolbar_arrays();

				# (V4.1.0)
				$sfmembersopt = array();
				$sfmembersopt['sfcheckformember'] = true;
				$sfmembersopt['sfshowmemberlist'] = true;
				$sfmembersopt['sflimitmemberlist'] = false;
				$sfmembersopt['sfsinglemembership'] = false;
				$sfmembersopt['sfhidestatus'] = true;
				$sfmembersopt['sfviewperm'] = true;
				sf_add_option('sfmemberopts', $sfmembersopt);

				# code exclusions
				$sfsupport = array();
				$sfsupport['sfusinglinking'] = true;
				$sfsupport['sfusingwidgets'] = true;
				$sfsupport['sfusinggeneraltags'] = true;
				$sfsupport['sfusingavatartags'] = true;
				$sfsupport['sfusinglinkstags'] = true;
				$sfsupport['sfusingtagstags'] = true;
				$sfsupport['sfusingpagestags'] = true;
				$sfsupport['sfusingliststags'] = true;
				$sfsupport['sfusingstatstags'] = true;
				$sfsupport['sfusingpmtags'] = true;
				$sfsupport['sfusinglinkcomments'] = true;
				sf_add_option('sfsupport', $sfsupport);


				$sfcontrols = array();
				$sfcontrols['dayflag'] = '0';
				$sfcontrols['hourflag'] = '0';
				$sfcontrols['maxonline'] = 0;
				$sfcontrols['showtopcount']	= 6;
				$sfcontrols['membercount'] = 0;
				$sfcontrols['guestcount'] = 0;
				$sfcontrols['shownewcount'] = 6;
				$sfcontrols['fourofour'] = false;
				sf_add_option('sfcontrols', $sfcontrols);

				$sfblock = array();
				$sfblock['blockadmin'] = false;
				$sfblock['blockroles'] = 'administrator';
				$sfblock['blockredirect'] = get_permalink(sf_get_option('sfpage'));
				sf_add_option('sfblockadmin', $sfblock);

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
				$sfconfig['avatar-pool'] 	= $basepath . 'forum-avatar-pool';
				$sfconfig['smileys'] 		= $basepath . 'forum-smileys';
				$sfconfig['ranks'] 			= $basepath . 'forum-badges';
				$sfconfig['image-uploads'] 	= $basepath . 'forum-image-uploads';
				$sfconfig['media-uploads'] 	= $basepath . 'forum-media-uploads';
				$sfconfig['file-uploads'] 	= $basepath . 'forum-file-uploads';
				$sfconfig['policies'] 		= $basepath . 'forum-policies';
				$sfconfig['hooks'] 			= 'plugins/simple-forum/forum/hooks';
				$sfconfig['pluggable'] 		= 'plugins/simple-forum/forum';
				$sfconfig['filters'] 		= 'plugins/simple-forum/library';
				$sfconfig['help'] 			= 'plugins/simple-forum/help';
				$sfconfig['custom-icons']	= 'plugins/simple-forum/styles/icons/'.$sfstyle['sficon'].'/custom/';
				sf_add_option('sfconfig', $sfconfig);

				$newpath = SF_STORE_DIR . '/' . $sfconfig['image-uploads'];
				@mkdir($newpath, 0777);

				$newpath = SF_STORE_DIR . '/' . $sfconfig['media-uploads'];
				@mkdir($newpath, 0777);

				$newpath = SF_STORE_DIR . '/' . $sfconfig['file-uploads'];
				@mkdir($newpath, 0777);

				$newpath = SF_STORE_DIR . '/' . $sfconfig['ranks'];
				@mkdir($newpath, 0777);

				$newpath = SF_STORE_DIR . '/' . $sfconfig['avatar-pool'];
				@mkdir($newpath, 0777);

				$newpath = SF_STORE_DIR . '/' . $sfconfig['policies'];
				@mkdir($newpath, 0777);

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
				$sfuploads['showmode'] = false;
				$sfuploads['deftab'] = 2;  # upload tab
				sf_add_option('sfuploads', $sfuploads);

				$sfmetatags = array();
				$sfmetatags['sfdescr'] = '';
				$sfmetatags['sfdescruse'] = 1;
				$sfmetatags['sfusekeywords'] = true;
				$sfmetatags['keywords'] = '';
				$sfmetatags['sftagwords'] = true;
				sf_add_option('sfmetatags', $sfmetatags);

				# display array
				$sfdisplay = array();

				$sfdisplay['pagetitle']['notitle'] 			= false;
				$sfdisplay['pagetitle']['banner'] 			= '';

				$sfdisplay['breadcrumbs']['showcrumbs']		= true;
				$sfdisplay['breadcrumbs']['showhome']		= true;
				$sfdisplay['breadcrumbs']['homepath']		= SFHOMEURL;
				$sfdisplay['breadcrumbs']['tree']			= false;

				$sfdisplay['unreadcount']['unread']			= true;
				$sfdisplay['unreadcount']['markall']		= true;

				$sfdisplay['search']['searchtop']			= true;
				$sfdisplay['search']['searchbottom']		= true;

				$sfdisplay['quicklinks']['qltop']			= true;
				$sfdisplay['quicklinks']['qlbottom']		= true;
				$sfdisplay['quicklinks']['qlcount']			= 15;

				$sfdisplay['pagelinks']['ptop']				= true;
				$sfdisplay['pagelinks']['pbottom']			= true;

				$sfdisplay['stats']['showstats']			= true;
				$sfdisplay['stats']['mostusers']			= true;
				$sfdisplay['stats']['online']				= true;
				$sfdisplay['stats']['forumstats']			= true;
				$sfdisplay['stats']['memberstats']			= true;
				$sfdisplay['stats']['topposters']			= true;
				$sfdisplay['stats']['admins']				= true;
				$sfdisplay['stats']['newusers']				= true;

				$sfdisplay['firstlast']['date']				= true;
				$sfdisplay['firstlast']['time']				= true;
				$sfdisplay['firstlast']['user']				= true;

				$sfdisplay['groups']['description']			= true;
				$sfdisplay['groups']['showsubforums']		= true;
				$sfdisplay['groups']['showallsubs']			= false;
				$sfdisplay['groups']['combinesubcount']		= false;

				$sfdisplay['forums']['description']			= true;
				$sfdisplay['forums']['newposticon']			= true;
				$sfdisplay['forums']['pagelinks']			= true;
				$sfdisplay['forums']['newpposts']			= true;
				$sfdisplay['forums']['newcount']			= true;
				$sfdisplay['forums']['newabove']			= true;
				$sfdisplay['forums']['sortinforum']			= true;
				$sfdisplay['forums']['singleforum']			= false;
				$sfdisplay['forums']['topiccol']			= true;
				$sfdisplay['forums']['postcol']				= true;
				$sfdisplay['forums']['lastcol']				= true;
				$sfdisplay['forums']['showtitle']			= false;
				$sfdisplay['forums']['showtitletop']		= false;
				$sfdisplay['forums']['pinned']	            = false;

				$sfdisplay['topics']['perpage']				= 12;
				$sfdisplay['topics']['numpagelinks']		= 4;
				$sfdisplay['topics']['sortnewtop']			= true;
				$sfdisplay['topics']['maxtags']				= 0;
				$sfdisplay['topics']['firstcol']			= true;
				$sfdisplay['topics']['lastcol']				= true;
				$sfdisplay['topics']['postcol']				= true;
				$sfdisplay['topics']['viewcol']				= true;
				$sfdisplay['topics']['pagelinks']			= true;
				$sfdisplay['topics']['statusicons']			= true;
				$sfdisplay['topics']['postrating']			= true;
				$sfdisplay['topics']['topicstatus']			= true;
				$sfdisplay['topics']['topictags']			= true;
				$sfdisplay['topics']['posttip']				= true;
				$sfdisplay['topics']['print']				= true;
				$sfdisplay['topics']['showsubforums']		= true;

				$sfdisplay['posts']['perpage']				= 20;
				$sfdisplay['posts']['numpagelinks']			= 4;
				$sfdisplay['posts']['userabove']			= false;
				$sfdisplay['posts']['sortdesc']				= false;
				$sfdisplay['posts']['showedits']			= true;
				$sfdisplay['posts']['showlastedit']			= true;
				$sfdisplay['posts']['tagstop']				= true;
				$sfdisplay['posts']['tagsbottom']			= true;
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

				$sfdisplay['posts']['sffbconnect']			= true;
				$sfdisplay['posts']['sfmyspace']			= true;
				$sfdisplay['posts']['linkedin'] 			= true;
				sf_add_option('sfdisplay', $sfdisplay);

				# guest settings
				$sfguests = array();
				$sfguests['reqemail'] = true;
				$sfguests['storecookie'] = true;
				sf_add_option('sfguests', $sfguests);

				# profile management
				$sfprofile = array();

				# profile data control
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
				$sfprofile['require']['linkedin'] = false;

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
				$sfprofile['include']['linkedin'] = true;

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
				$sfprofile['display']['linkedin'] = true;

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
				$sfprofile['label']['linkedin'] = 'LinkedIn';

				$sfprofile['label']['forumrank'] = 'Forum Rank';
				$sfprofile['label']['specialrank'] = 'Special Rank';
				$sfprofile['label']['badge'] = 'Badge';
				$sfprofile['label']['memberships'] = 'User Group Memberships';

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
				$sfprofile['profileinstats'] = true;
				sf_add_option('sfprofile', $sfprofile);

				# avatar options
				$sfavatars = array();
				$sfavatars['sfshowavatars'] = true;
				$sfavatars['sfavataruploads'] = true;
				$sfavatars['sfavatarpool'] = false;
				$sfavatars['sfavatarremote'] = false;
				$sfavatars['sfgmaxrating'] = 1;
				$sfavatars['sfavatarsize'] = 50;
				$sfavatars['sfavatarfilesize'] = 10240;
				$sfavatars['sfavatarpriority'] = array(0, 2, 3, 1, 4, 5);  # gravatar, upload, spf, wp, pool, remote
				sf_add_option('sfavatars', $sfavatars);

				# RSS stuff
				$sfrss = array();
				$sfrss['sfrsscount'] = 15;
				$sfrss['sfrsswords'] = 0;
				$sfrss['sfrssfeedkey'] = true;
				sf_add_option('sfrss', $sfrss);

				sf_add_option('sffiltershortcodes', true);

				# 4.3
				sf_add_option('sftbextras', '');
				$syntax = array();
				$syntax['sfsyntaxforum'] = false;
				$syntax['sfsyntaxblog']  = false;
				$syntax['sfbrushes'] = 'apache,applescript,asm,bash-script,bash,basic,clang,css,diff,html,javascript,lisp,ooc,php,python,ruby,sql';
				sf_add_option('sfsyntax', $syntax);

				$sftwitter = array();
				$sftwitter['sftwitterfollow'] = true;
				sf_add_option('sftwitter', $sftwitter);

                $sfmobile = array();
                $sfmobile['browsers'] = '2.0 MMP, 240x320, 400X240, AvantGo, BlackBerry, Blazer, Cellphone, Danger, DoCoMo, Elaine/3.0, EudoraWeb, Googlebot-Mobile, hiptop, IEMobile, KYOCERA/WX310K, LG/U990, MIDP-2., MMEF20, MOT-V, NetFront, Newt, Nintendo Wii, Nitro, Nokia, Opera Mini, Palm, PlayStation Portable, portalmmm, Proxinet, ProxiNet, SHARP-TQ-GX10, SHG-i900, Small, SonyEricsson, Symbian OS, SymbianOS, TS21i-10, UP.Browser, UP.Link, webOS, Windows CE, WinWAP, YahooSeeker/M1A1-R2D2';
                $sfmobile['touch'] = 'iPhone, iPod, ipad, Android, BlackBerry9530, LG-TU915 Obigo, LGE VX, webOS, Nokia5800';
				sf_add_option('sfmobile', $sfmobile);

				# 4.3.1
				# post content width
				$sfpostwrap = array();
				$sfpostwrap['postwrap']=false;
				$sfpostwrap['postwidth']=0;
				sf_add_option('sfpostwrap', $sfpostwrap);

				sf_add_option('sfwplistpages', true);

				# 4.3.2
				# Script in footer
				sf_add_option('sfscriptfoot', false);

				echo '<h5>'.__("Phase", "sforum").' - '.$phase.' - ';
				echo __("Default Forum Options Created", "sforum").'</h5>';
				break;

		case 6:
				# CREATE AVATAR FOLDER AND SMILEYS FOLDER IN WP-CONTENT -------------------

				$avatar = sf_relocate_avatars();
				if($avatar != 0)
				{
					$mess = __("INSTALL PROBLEM: Unable to Create Avatar Folder", "sforum").'</h5>';
					sf_add_option('sfinstallav', $avatar);
				} else {
					$mess = '<h5>'.__("Phase", "sforum").' - '.$phase.' - ';
					$mess.= __("Avatar Folder Created", "sforum").'</h5>';
				}

				echo $mess;
				break;

		case 7:
				$smiley = sf_relocate_smileys();
				if($smiley != 0)
				{
					$mess = __("INSTALL PROBLEM: Unable to Create Smiley Folder", "sforum").'</h5>';
					sf_add_option('sfinstallsm', $smiley);
				} else {
					$mess = '<h5>'.__("Phase", "sforum").' - '.$phase.' - ';
					$mess.= __("Smiley Folder Created", "sforum").'</h5>';
				}

				sf_build_base_smileys();

				echo $mess;
				break;

		case 8:
				# CREATE MEMBERS TABLE ---------------------------
				sf_install_members_table($subphase);

                # give them feedkeys
                sf_generate_member_feedkeys();

				echo '<h5>'.__("Phase", "sforum").' - '.$phase.' - ';
				echo __("Members Data Created for Members ", "sforum").(($subphase - 1) * 250 + 1).' - '.($subphase * 250).'</h5>';
				break;

		case 9:
				# grant spf capabilities to installer
				$user = new WP_User($current_user->ID);
				$user->add_cap('SPF Manage Options');
				$user->add_cap('SPF Manage Forums');
				$user->add_cap('SPF Manage User Groups');
				$user->add_cap('SPF Manage Permissions');
				$user->add_cap('SPF Manage Tags');
				$user->add_cap('SPF Manage Components');
				$user->add_cap('SPF Manage Admins');
				$user->add_cap('SPF Manage Users');
				$user->add_cap('SPF Manage Profiles');
				$user->add_cap('SPF Manage Toolbox');
				$user->add_cap('SPF Manage Configuration');
				sf_update_member_item($current_user->ID, 'admin', 1);

				# admin your option defaults
            	$sfadminoptions = array();
                $sfadminoptions['sfadminbar'] = false;
                $sfadminoptions['sfbarfix'] = false;
                $sfadminoptions['sfnotify'] = false;
                $sfadminoptions['sfshownewadmin'] = false;
                $sfadminoptions['sfstatusmsgtext'] = '';

                # admin colors
            	$sfadminoptions['colors']['submitbg'] = '27537A';
            	$sfadminoptions['colors']['submitbgt'] = 'FFFFFF';
				$sfadminoptions['colors']['bbarbg'] = '0066CC';
				$sfadminoptions['colors']['bbarbgt'] = 'FFFFFF';
				$sfadminoptions['colors']['formbg'] = '0066CC';
				$sfadminoptions['colors']['formbgt'] = 'FFFFFF';
				$sfadminoptions['colors']['panelbg'] = '78A1FF';
				$sfadminoptions['colors']['panelbgt'] = '000000';
				$sfadminoptions['colors']['panelsubbg'] = 'A7C1FF';
				$sfadminoptions['colors']['panelsubbgt'] = '000000';
				$sfadminoptions['colors']['formtabhead'] = '464646';
				$sfadminoptions['colors']['formtabheadt'] = 'D7D7D7';
				$sfadminoptions['colors']['tabhead'] = '0066CC';
				$sfadminoptions['colors']['tabheadt'] = 'D7D7D7';
				$sfadminoptions['colors']['tabrowmain'] = 'EAF3FA';
				$sfadminoptions['colors']['tabrowmaint'] = '000000';
				$sfadminoptions['colors']['tabrowsub'] = '78A1FF';
				$sfadminoptions['colors']['tabrowsubt'] = '000000';

                sf_update_member_item($current_user->ID, 'admin_options', $sfadminoptions);

				echo '<h5>'.__("Phase", "sforum").' - '.$phase.' - ';
				echo __("Admin Permission Data Built", "sforum").'</h5>';
				break;

		case 10:
				# UPDATE VERSION/BUILD NUMBERS -------------------------

				sf_log_event(SFRELEASE, SFVERSION, SFBUILD);

				delete_option('sfInstallID'); # use wp option table

				# Lets update permalink and force a rewrite rules flush
				sfg_update_permalink(true);

				echo '<h5>'.__("Phase", "sforum").' - '.$phase.' - ';
				echo __("Version Number Updated", "sforum").'</h5>';
				break;
	}

	return;
}

?>