<?php
/*
Simple:Press
Edit Tools - Move Topic/Move Post
$LastChangedDate: 2011-04-26 05:52:27 -0700 (Tue, 26 Apr 2011) $
$Rev: 5981 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

sf_forum_ahah_support();
sf_setup_admin_constants();

# ----------------------------------

# get out of here if no action specified
if (empty($_GET['action'])) die();
$action = $_GET['action'];

if ($action == 'mt')
{
	# move topic form
	sf_move_topic_popup();
}

if ($action == 'mp')
{
	# move post form
	sf_move_post_popup();
}

if ($action == 'rp')
{
	# move post form
	sf_reassign_post_popup();
}

if ($action == 'ct')
{
	# change topic status form
	sf_reset_topic_status();
}

if ($action == 'ss')
{
	sf_save_new_status();
}

if ($action == 'edit-tags')
{
	sf_edit_topic_tags();
}

if ($action == 'props')
{
	sf_show_properties();
}

die();

function sf_move_topic_popup()
{
	global $current_user, $wpdb;

	$thistopic = sf_get_topic_record(sf_esc_int($_GET['topicid']));
	$thisforum = sf_get_forum_record(sf_esc_int($_GET['forumid']));
    if (empty($thistopic) || empty($thisforum)) die();

	sf_initialise_globals($thisforum->forum_id);
	$groups = sf_get_combined_groups_and_forums();

	if (!$current_user->sfmovetopics)
	{
		echo (__('Access Denied', "sforum"));
		die();
	}
?>
	<div id="sfpostform">
	<fieldset><legend><?php echo(sprintf(__("Select new Forum for Topic: &nbsp;&nbsp;&nbsp; %s", "sforum"), "<br />".sf_filter_title_display($thistopic->topic_name))); ?></legend>
		<form action="<?php echo(sf_build_url($thisforum->forum_slug, '', 1, 0)); ?>" method="post" name="movetopicform">
			<input type="hidden" name="currenttopicid" value="<?php echo($thistopic->topic_id); ?>" />
			<input type="hidden" name="currentforumid" value="<?php echo($thisforum->forum_id); ?>" />
			<br />
<?php
			if($groups)
			{
				sf_build_newforum_select($groups, $thisforum->forum_id);
			}
?>
			<input type="submit" class="sfcontrol" name="maketopicmove" value="<?php esc_attr_e("Move Topic to Selected Forum", "sforum") ?>" />
			<input type="button" class="sfcontrol" name="cancel" value="<?php esc_attr_e("Cancel", "sforum") ?>" onclick="hs.close(this);" />
		</form>
	</fieldset>
	<div>
<?php
	return;
}

function sf_move_post_popup()
{
	global $current_user, $wpdb;

	$thispost = sf_esc_int($_GET['pid']);
	$thispostindex = sf_esc_int($_GET['pix']);
	$thistopic = sf_get_topic_record(sf_esc_int($_GET['id']));
    if (empty($thispost) || empty($thistopic)) die();

	# Only set blog_post_id if this is the very first post in a blog linked topic
	if($thispostindex == 1)
	{
		$blogpostid = $thistopic->blog_post_id;
	} else {
		$blogpostid = 0;
	}

	sf_initialise_globals($thistopic->forum_id);
	$thisforum = sf_get_forum_record($thistopic->forum_id);
	$groups = sf_get_combined_groups_and_forums();

	if (!$current_user->sfmoveposts)
	{
		echo (__('Access Denied', "sforum"));
		die();
	}
?>
	<div id="sfpostform">
	<fieldset><legend><?php echo(sprintf(__("Move Post (ID: %s) to New Topic/Forum", "sforum"), $thispost)); ?></legend>
		<form action="<?php echo(sf_build_url($thisforum->forum_slug, $thistopic->topic_slug, 1, 0)); ?>" method="post" name="movepostform">
			<input type="hidden" name="postid" value="<?php echo($thispost); ?>" />
			<input type="hidden" name="oldtopicid" value="<?php echo($thistopic->topic_id); ?>" />
			<input type="hidden" name="oldforumid" value="<?php echo($thisforum->forum_id); ?>" />
			<input type="hidden" name="blogpostid" value="<?php echo($blogpostid); ?>" />
			<br />
<?php
			if($groups)
			{
				sf_build_newforum_select($groups, 0);
			}
?>
			<p><?php _e("New Topic Name", "sforum"); ?></p>
			<input type="text" class="sfcontrol sfpostcontrol" size="80" name="newtopicname" value="" /><br /><br />
			<input type="submit" class="sfcontrol" name="makepostmove" value="<?php esc_attr_e("Move Post", "sforum") ?>" />
			<input type="button" class="sfcontrol" name="cancel" value="<?php esc_attr_e("Cancel", "sforum") ?>" onclick="hs.close(this);" />
		</form>
	</fieldset>
	<div>
<?php
	return;
}

function sf_reassign_post_popup()
{
	global $current_user, $wpdb;

	$thispost = sf_esc_int($_GET['pid']);
	$thisuser = sf_esc_int($_GET['uid']);
    $thistopic = sf_esc_int($_GET['id']);
    if (empty($thispost) || empty($thisuser) || empty($thistopic)) die();

	$thistopic = sf_get_topic_record($thistopic);
	sf_initialise_globals($thistopic->forum_id);
	$thisforum = sf_get_forum_record($thistopic->forum_id);

	if (!$current_user->sfreassign)
	{
		echo (__('Access Denied', "sforum"));
		die();
	}
?>
	<div id="sfpostform">
	<fieldset><legend><?php echo(sprintf(__("Reassign Post to New User (currently ID: %d)", "sforum"), $thisuser)); ?></legend>
		<form action="<?php echo(sf_build_url($thisforum->forum_slug, $thistopic->topic_slug, 1, 0)); ?>" method="post" name="reassignpostform">
			<input type="hidden" name="postid" value="<?php echo($thispost); ?>" />
			<input type="hidden" name="olduserid" value="<?php echo($thisuser); ?>" />
			<p><?php _e("New User ID", "sforum"); ?></p>
			<input type="text" class="sfcontrol sfpostcontrol" size="80" name="newuserid" value="" /><br /><br />
			<input type="submit" class="sfcontrol" name="makepostreassign" value="<?php esc_attr_e("Reassign Post", "sforum") ?>" />
			<input type="button" class="sfcontrol" name="cancel" value="<?php esc_attr_e("Cancel", "sforum") ?>" onclick="hs.close(this);" />
		</form>
	</fieldset>
	<div>
<?php
	return;
}

function sf_build_newforum_select($groups, $forumid)
{
	echo '<p>'.__("Select Forum", "sforum").'</p>';
	echo '<select class="sfquicklinks sfcontrol" name="forumid">'."\n";

	foreach($groups as $group)
	{
		$name = sf_filter_title_display($group['group_name']);
		if(strlen($name) > 30) $name = substr($name, 0, 30).'...';

		echo '<optgroup class="sflist" label="&nbsp;&nbsp;'.sf_create_name_extract(sf_filter_title_display($group['group_name'])).'">'."\n";
		if($group['forums'])
		{
			foreach($group['forums'] as $forum)
			{
				if($forum['forum_id'] != $forumid)
				{
					$name = sf_filter_title_display($forum['forum_name']);
					if(strlen($name) > 35) $name = substr($name, 0, 35).'...';
					echo '<option value="'.$forum['forum_id'].'">&nbsp;&nbsp;&nbsp;&nbsp;'.sf_create_name_extract(sf_filter_title_display($forum['forum_name'])).'</option>'."\n";
				}
			}
		}
		echo '</optgroup>';
	}
	echo '</select><br /><br />'."\n";
	return;
}

function sf_reset_topic_status()
{
	global $current_user, $wpdb;

    $thisid = sf_esc_int($_GET['id']);
	$statusset = sf_esc_int($_GET['set']);
	$statusflag = sf_esc_int($_GET['flag']);
	$returnpage = sf_esc_int($_GET['returnpage']);

	$thistopic = sf_get_topic_record($thisid);
	sf_initialise_globals($thistopic->forum_id);
	$thisforum = sf_get_forum_record($thistopic->forum_id);

?>
	<div id="sfpostform">
	<fieldset><legend><?php echo(__("Change Topic Status", "sforum")); ?></legend>
		<form action="<?php echo(sf_build_url($thisforum->forum_slug, '', $returnpage, 0)); ?>" method="post" name="changetopicstatus">
			<input type="hidden" name="id" value="<?php echo($thistopic->topic_id); ?>" />
			<br />
			<?php echo sf_topic_status_select($statusset, $statusflag); ?>

			<input type="submit" class="sfcontrol" name="makestatuschange" value="<?php esc_attr_e("Save Status", "sforum") ?>" />
			<input type="button" class="sfcontrol" name="cancel" value="<?php esc_attr_e("Cancel", "sforum") ?>" onclick="hs.close(this);" />
		</form>
	</fieldset>
	<div>
<?php
	return;
}

function sf_save_new_status()
{
	$topicid= sf_esc_int($_GET['id']);
	$statvalue = sf_esc_int($_GET['newvalue']);
    if (empty($topicid) || empty($statvalue)) die();

	sf_update_topic_status_flag($statvalue, $topicid);

	echo('<small>'.esc_html($_GET['newtext']).'</small>');
	return;
}

function sf_show_properties()
{
	global $wpdb, $current_user;

    $forumid = sf_esc_int($_GET['forum']);
    $topicid = sf_esc_int($_GET['topic']);
    if (empty($forumid) || empty($topicid)) die();

	$thisforum = sf_get_forum_record($forumid);
	$thistopic = sf_get_topic_record($topicid);

	if(isset($_GET['post']))
	{
		$thisgroup = sf_get_group_record(sf_esc_int($thisforum->group_id));
	} else {
        $groupid = sf_esc_int($_GET['group']);
        if (empty($groupid)) die();
		$thisgroup = sf_get_group_record($groupid);
	}

	$posts = $wpdb->get_col("SELECT post_id FROM ".SFPOSTS." WHERE topic_id=".$thistopic->topic_id." ORDER BY post_id");
	If($posts)
	{
		$first = $posts[0];
		$last  = $posts[count($posts)-1];
	}

	# set timezone onto the started date
	$zone = $current_user->timezone;
	$startdate = strtotime($thistopic->topic_date);
	if($zone < 0) $startdate = ($startdate-(abs($zone)*3600));
	if($zone > 0) $startdate = ($startdate+(abs($zone)*3600));

	$topicstart = date(SFTIMES, $startdate).' - '.date(SFDATES, $startdate)
?>
	<table class="sfpopuptable">
		<tr><td class="sflabel" width="35%"><?php _e("Group ID", "sforum"); ?></td><td colspan="2" class="sfdata"><?php echo($thisgroup->group_id); ?></td></tr>
		<tr><td class="sflabel"><?php _e("Group Title", "sforum"); ?></td><td colspan="2" class="sfdata"><?php echo(sf_filter_title_display($thisgroup->group_name)); ?></td></tr>
		<tr><td class="sflabel"><?php _e("Forum ID", "sforum"); ?></td><td class="sfdata"><?php echo($thisforum->forum_id); ?></td><td class="sfdata"><?php echo sf_rebuild_forum_form($thisforum->forum_id, $thistopic->topic_id, $thisforum->forum_slug, $thistopic->topic_slug); ?></td></tr>
		<tr><td class="sflabel"><?php _e("Forum Title", "sforum"); ?></td><td colspan="2" class="sfdata"><?php echo(sf_filter_title_display($thisforum->forum_name)); ?></td></tr>
		<tr><td class="sflabel"><?php _e("Forum Slug", "sforum"); ?></td><td colspan="2" class="sfdata"><?php echo($thisforum->forum_slug); ?></td></tr>
		<tr><td class="sflabel"><?php _e("Topics in Forum", "sforum"); ?></td><td colspan="2" class="sfdata"><?php echo($thisforum->topic_count); ?></td></tr>
		<tr><td class="sflabel"><?php _e("Topic ID", "sforum"); ?></td><td class="sfdata"><?php echo($thistopic->topic_id); ?></td><td class="sfdata"><?php echo sf_rebuild_topic_form($thisforum->forum_id, $thistopic->topic_id, $thisforum->forum_slug, $thistopic->topic_slug); ?></td></tr>
		<tr><td class="sflabel"><?php _e("Topic Title", "sforum"); ?></td><td colspan="2" class="sfdata"><?php echo(sf_filter_title_display($thistopic->topic_name)); ?></td></tr>
		<tr><td class="sflabel"><?php _e("Topic Slug", "sforum"); ?></td><td colspan="2" class="sfdata"><?php echo($thistopic->topic_slug); ?></td></tr>
		<tr><td class="sflabel"><?php _e("Posts in Topic", "sforum"); ?></td><td colspan="2" class="sfdata"><?php echo($thistopic->post_count); ?></td></tr>

		<tr><td class="sflabel"><?php _e("Topic Started", "sforum"); ?></td><td colspan="2" class="sfdata"><?php echo($topicstart); ?></td></tr>

		<tr><td class="sflabel"><?php _e("First Post ID", "sforum"); ?></td><td colspan="2" class="sfdata"><?php echo($first); ?></td></tr>
		<tr><td class="sflabel"><?php _e("Last Post ID", "sforum"); ?></td><td colspan="2" class="sfdata"><?php echo($last); ?></td></tr>
<?php
		if(isset($_GET['post']))
		{
			$postid = sf_esc_int($_GET['post']);
			$ip = $wpdb->get_var("SELECT poster_ip FROM ".SFPOSTS." WHERE post_id=".$postid);
?>
			<tr><td class="sflabel"><?php _e("This Post ID", "sforum"); ?></td><td colspan="2" class="sfdata"><?php echo($postid); ?></td></tr>
			<tr><td class="sflabel"><?php _e("Poster IP", "sforum"); ?></td><td colspan="2" class="sfdata"><?php echo($ip); ?></td></tr>
<?php
		}
?>
	</table>
<?php
	return;
}

function sf_edit_topic_tags()
{
	global $wpdb;

    $topicid = sf_esc_int($_GET['topicid']);
    if (empty($topicid)) die();

	$thistopic = sf_get_topic_record($topicid);
	sf_initialise_globals($thistopic->forum_id);
	$thisforum = sf_get_forum_record($thistopic->forum_id);

	$tags = $wpdb->get_results("SELECT tag_name, ".SFTAGS.".tag_id
								FROM ".SFTAGS."
							 	JOIN ".SFTAGMETA." ON ".SFTAGMETA.".tag_id = ".SFTAGS.".tag_id
								WHERE topic_id=".$thistopic->topic_id);
	$curtags = '';
	if ($tags)
	{
		foreach ($tags as $tag)
		{
			if ($curtags == '')
			{
				$curtags = $tag->tag_name;
			} else {
				$curtags.= ', '.$tag->tag_name;
			}
		}
	}
?>
	<div id="sfpostform">
	<fieldset><legend><?php echo(sprintf(__("Edit Topic Tags: &nbsp;&nbsp;&nbsp; %s", "sforum"), "<br />".sf_filter_title_display($thistopic->topic_name))); ?></legend>
		<form action="<?php echo(sf_build_url($thisforum->forum_slug, '', 1, 0)); ?>" method="post" name="edittags">
			<input type="hidden" name="topicid" value="<?php echo($thistopic->topic_id); ?>" />
			<p><?php _e("Tags:", "sforum"); ?></p>
			<input type="text" size="45" name="topictags" value="<?php echo($curtags); ?>" />
			<br />
			<input type="submit" class="sfcontrol" name="maketagsedit" value="<?php esc_attr_e("Update Tags", "sforum") ?>" />
			<input type="button" class="sfcontrol" name="cancel" value="<?php esc_attr_e("Cancel", "sforum") ?>" onclick="hs.close(this);" />
		</form>
	</fieldset>
	<div>
<?php
}

function sf_rebuild_forum_form($forumid, $topicid, $forumslug, $topicslug)
{
	$out = '<form action="'.sf_build_url($forumslug, $topicslug, 1, 0).'" method="post" name="forumrebuild">'."\n";
	$out.= '<input type="hidden" name="forumid" value="'.$forumid.'" />'."\n";
	$out.= '<input type="hidden" name="topicid" value="'.$topicid.'" />'."\n";
	$out.= '<input type="hidden" name="forumslug" value="'.esc_attr($forumslug).'" />'."\n";
	$out.= '<input type="hidden" name="topicslug" value="'.esc_attr($topicslug).'" />'."\n";
	$out.= '<input type="submit" class="sfxcontrol" name="rebuildforum" value="'.esc_attr(__("Verify", "sforum")).'" />';
	$out.= '</form>'."\n";

	return $out;
}

function sf_rebuild_topic_form($forumid, $topicid, $forumslug, $topicslug)
{
	$out = '<form action="'.sf_build_url($forumslug, $topicslug, 1, 0).'" method="post" name="topicrebuild">'."\n";
	$out.= '<input type="hidden" name="forumid" value="'.$forumid.'" />'."\n";
	$out.= '<input type="hidden" name="topicid" value="'.$topicid.'" />'."\n";
	$out.= '<input type="hidden" name="forumslug" value="'.esc_attr($forumslug).'" />'."\n";
	$out.= '<input type="hidden" name="topicslug" value="'.esc_attr($topicslug).'" />'."\n";
	$out.= '<input type="submit" class="sfxcontrol" name="rebuildtopic" value="'.esc_attr(__("Verify", "sforum")).'" />';
	$out.= '</form>'."\n";

	return $out;
}

?>