<?php
/*
Simple:Press
Forum Topic/Post Saves
$LastChangedDate: 2011-03-07 08:26:20 -0700 (Mon, 07 Mar 2011) $
$Rev: 5645 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# --------------------------------------------

# set up required globals
global $wpdb, $current_user, $sfglobals;

sf_forum_ahah_support();
sf_setup_forum_hooks();
sf_setup_post_save_includes();

# Initialise the array -------------------------------------------------------------
$newpost = array();
$newpost['forumid'] = 0;
$newpost['forumslug'] = '';

# Validation checks on forum data ---------------------------------------------------
# if the forum is not set then this may be a back door approach
if((!isset($_POST['forumid'])) || (!isset($_POST['forumslug'])))
{
	echo (__('Forum not set - Unable to create post', 'sforum'));
	die();
} else {
	$newpost['forumid'] = sf_esc_int($_POST['forumid']);
	$newpost['forumslug'] = sf_esc_str($_POST['forumslug']);
}

# If forum or system locked then refuse post
sf_initialise_globals($newpost['forumid']);

if($current_user->moderator == false)
{
	if($sfglobals['lockdown'] || sf_forum_locked($newpost['forumid']))
	{
		echo (__("This forum is currently locked - access is read only", 'sforum'));
		die();
	}
}

# clear out the message buffer
delete_sfnotice();

# Check the pre-save hook
if(function_exists('sf_hook_pre_post_save'))
{
	if(sf_process_hook('sf_hook_pre_post_save', array($_POST['postitem'])) == false)
	{
		update_sfnotice('sfmessage', '1@'.__('This post has been refused', "sforum"));
		wp_redirect(SFURL);
		die();
	}
}

# set up the main vars -------------------------------------------------------------
$newpost['topicid'] = 0;
$newpost['topicslug'] = '';
$newpost['postid'] = 0;
$newpost['submsg'] = '';
$newpost['email_prefix'] = '';
$action='';
if(isset($_POST['newtopic'])) $action = 'topic';
if(isset($_POST['newpost']))  $action = 'post';

# if this is an existing topic check id and slug is set
if($action == 'post')
{
	if((!isset($_POST['topicid'])) || (!isset($_POST['topicslug'])))
	{
		echo (__('Topic not set - Unable to create post', 'sforum'));
		die();
	} else {
		$newpost['topicid'] = sf_esc_int($_POST['topicid']);
		$newpost['topicslug'] = sf_esc_str($_POST['topicslug']);
		$newpost['email_prefix'] = 'Re: ';
	}
}

# check that current user is actually allowed to do this ---------------------------
if(($action == 'topic' && !$current_user->sfaddnew) || ($action == 'post' && !$current_user->sfreply))
{
	echo (__('Access Denied', "sforum"));
	die();
}

# set up initial url to return to if save fails ------------------------------------
$returnURL = sf_build_url($newpost['forumslug'], $newpost['topicslug'], 0, $newpost['postid']);

# setup and prepare post data ------------------------------------------------------
if($action == 'topic')
{
	# topic specific items
	if(empty($_POST['newtopicname']))
	{
		update_sfnotice('sfmessage', '1@'.__('No Topic Name has been entered! Post can not be saved', "sforum"));
		wp_redirect($returnURL);
		die();
	} else {
		$newpost['topicname'] = sf_filter_title_save(trim($_POST['newtopicname']));
	}
	$newpost['topicslug'] = sf_create_slug($newpost['topicname'], 'topic');
	$newpost['topicpin']=0;
	$newpost['topicsub']='NULL';
	$newpost['statusflag']='0';
	$newpost['bloglink']='0';
	$newpost['post_category']='NULL';
	if(isset($_POST['topiclock'])) $newpost['topiclock']=1;
	if(isset($_POST['topicpin'])) $newpost['topicpin']=1;
	if(isset($_POST['statusflag'])) $newpost['statusflag']=sf_esc_int($_POST['statusflag']);
	if($_POST['bloglink'] == 'on') $newpost['bloglink']=true;
	if(isset($_POST['post_category'])) $newpost['post_category'] = $_POST['post_category']; # array so santize later!!

	# get the tags for the new topic
	if (empty($_POST['topictags']))
	{
		$newpost['tags'] = '';
	} else {
		# check for duplicates and limit to max tag option
	    $newpost['tags'] = sf_filter_title_save(trim($_POST['topictags']));
	    $newpost['tags'] = trim($newpost['tags'], ',');  # no extra commas allowed
		$newpost['taglist'] = $newpost['tags']; # save comma separated list for later use
		$newpost['tags'] = explode(',', $newpost['tags']);
		$newpost['tags'] = array_unique($newpost['tags']);  # remove any duplicates
		$newpost['tags'] = array_values($newpost['tags']);  # put back in order
		if ($sfglobals['display']['topics']['maxtags'] > 0 && count($newpost['tags']) > $sfglobals['display']['topics']['maxtags'])
		{
			$newpost['tags'] = array_slice($newpost['tags'], 0, $sfglobals['display']['topics']['maxtags']);  # limit to maxt tags opton
		}
	}
}

# post specific (needed by new topic and new post)
$newpost['postpin']=0;
$newpost['topiclock']=0;
$newpost['topicsub']='';
$newpost['topicwatch']='';
$newpost['topicsubend']='';
$newpost['topicwatchend']='';
$newpost['statvalue']='';
$newpost['posttimestamp'] = "'" . current_time('mysql') . "'";
if(isset($_POST['postpin'])) $newpost['postpin']=1;
if(isset($_POST['topiclock'])) $newpost['topiclock']=1;
if(isset($_POST['topicsub'])) $newpost['topicsub']=sf_esc_str($_POST['topicsub']);
if(isset($_POST['topicwatch'])) $newpost['topicwatch']=sf_esc_str($_POST['topicwatch']);
if(isset($_POST['topicsubend'])) $newpost['topicsubend']=sf_esc_str($_POST['topicsubend']);
if(isset($_POST['topicwatchend'])) $newpost['topicwatchend']=sf_esc_str($_POST['topicwatchend']);
if(isset($_POST['statvalue'])) $newpost['statvalue']=sf_esc_int($_POST['statvalue']);
if(!empty($_POST['editTimestamp']))
{
	$yy = sf_esc_int($_POST['tsYear']);
	$mm = sf_esc_int($_POST['tsMonth']);
	$dd = sf_esc_int($_POST['tsDay']);
	$hh = sf_esc_int($_POST['tsHour']);
	$mn = sf_esc_int($_POST['tsMinute']);
	$ss = sf_esc_int($_POST['tsSecond']);
	$dd = ($dd > 31 ) ? 31 : $dd;
	$hh = ($hh > 23 ) ? $hh -24 : $hh;
	$mn = ($mn > 59 ) ? $mn -60 : $mn;
	$ss = ($ss > 59 ) ? $ss -60 : $ss;
	$posttimestamp = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $yy, $mm, $dd, $hh, $mn, $ss );
	$newpost['posttimestamp'] = '"'.( $posttimestamp ).'"';
}

$newpost['poststatus'] = 0;
if(empty($_POST['postitem']))
{
	update_sfnotice('sfmessage', '1@'.__('No Topic Post has been entered! Post can not be saved', "sforum"));
	wp_redirect($returnURL);
	die();
} else {
	$newpost['postcontent'] = $_POST['postitem'];
	$newpost['postcontent'] = sf_filter_content_save($newpost['postcontent'], 'new');
}

# Check if maxmium links has been exceeded
$sffilters = sf_get_option('sffilters');
if($sffilters['sfmaxlinks'] > 0 && !$current_user->forumadmin)
{
	if(substr_count($newpost['postcontent'], '</a>') > $sffilters['sfmaxlinks'])
	{
		update_sfnotice('sfmessage', '1@'.__('Maximum Number of Links Exceeded', "sforum"));
		wp_redirect(SFURL);
		die();
	}
}

$newpost['guestname']='';
$newpost['guestemail']='';
if ($current_user->guest)
{
	$sfguests = sf_get_option('sfguests');
	$newpost['guestname'] = sf_filter_name_save($_POST['guestname']);
	$newpost['guestemail'] = sf_filter_email_save($_POST['guestemail']);
	if (empty($newpost['guestname']) || ((empty($newpost['guestemail']) || !is_email($newpost['guestemail'])) && $sfguests['reqemail']))
	{
		update_sfnotice('sfmessage', '1@'.__('Guest name and valid Email address required', "sforum"));
		wp_redirect($returnURL);
		die();
	}
	# force maximum lengths
	$newpost['guestname'] = substr($newpost['guestname'], 0, 20);
	$newpost['guestemail'] = substr($newpost['guestemail'], 0, 50);
	$newpost['postername'] = sf_filter_name_save($newpost['guestname']);
	$newpost['posteremail'] = sf_filter_email_save($newpost['guestemail']);
	$newpost['userid']='';
} else {
	$newpost['postername'] = sf_filter_name_save($current_user->display_name);
	$newpost['posteremail'] = sf_filter_email_save($current_user->user_email);
	$newpost['userid'] = $current_user->ID;
}

# grab poster IP address and store in db
$newpost['ip'] = $_SERVER['REMOTE_ADDR'];

# Check for duplicate post of option is set
if(($current_user->member && $sffilters['sfdupemember'] == true) || ($current_user->guest && $sffilters['sfdupeguest'] == true))
{
	# Bur not admin or moderator
	if($current_user->forumadmin == false && $current_user->moderator == false)
	{
		$dupecheck = $wpdb->get_row("SELECT post_id, forum_id, topic_id FROM ".SFPOSTS." WHERE
									post_content = '".$newpost['postcontent']."' AND
									poster_ip = '".$newpost['ip']."' AND
									forum_id = ".$newpost['forumid']." AND
									topic_id = ".$newpost['topicid']
									, ARRAY_A);
		if($dupecheck)
		{
			update_sfnotice('sfmessage', '1@'.__('Duplicate Post Refused', "sforum"));
			$returnURL = sf_build_url(sf_get_forum_slug($dupecheck['forum_id']), sf_get_topic_slug($dupecheck['topic_id']), 0, $dupecheck['post_id'], 0);
			wp_redirect($returnURL);
			die();
		}
	}
}

# Branch to correct routine --------------------------------------------------------
if($action == 'topic')
{
	$newpost = sf_create_topic($newpost, $action);
}
if($action == 'post')
{
	$newpost = sf_create_post($newpost, $action);
}

# reset the url now we should have kosher values and re-direct
$returnURL = sf_build_url($newpost['forumslug'], $newpost['topicslug'], 0, $newpost['postid']);

wp_redirect($returnURL);
die();


# ==================================================================================
# CREATION FUNCTIONS
# ==================================================================================

# Create new Topic and First Post --------------------------------------------------
function sf_create_topic($newpost, $action)
{
	global $wpdb, $current_user;

	# security checks
	check_admin_referer('forum-userform_addtopic', 'forum-userform_addtopic');
	$spamcheck = sf_check_spammath();
	if($spamcheck[0]==true)
	{
		update_sfnotice('sfmessage', $spamcheck[1]);
		return;
	}

	# save the new topic record to db
	$newpost = sf_write_topic($newpost);
	if(!$newpost['db'])
	{
		update_sfnotice('sfmessage', __("Unable to Save New Topic Record", "sforum"));
		return;
	} else {
		# lets grab the new topic id
		$newpost['topicid'] = $wpdb->insert_id;
	}

	# check the topic slug and if empty use the topic id
	if(empty($newpost['topicslug']))
	{
		$newpost['topicslug'] = 'topic-'.$newpost['topicid'];
		$wpdb->query("UPDATE ".SFTOPICS." SET topic_slug='".$newpost['topicslug']."' WHERE topic_id=".$newpost['topicid']);
	}

	# save the tags
	if ($newpost['tags'] != '')
	{
		sfc_add_tags($newpost['topicid'], $newpost['tags']);
	}

	# Now save the new post record to db
	$newpost = sf_write_post($newpost);
	if(!$newpost['db'])
	{
		update_sfnotice('sfmessage', __("Unable to Save New Post Message", "sforum"));
		return;
	} else {
		# lets grab the new post id
		$newpost['postid'] = $wpdb->insert_id;
	}

	$wpdb->flush();

	# Post-Save New Post Processing
	$newpost = sf_post_save_processing($newpost, $action);

	# do we need to create a blog link?
	if($newpost['bloglink'])
	{
		include_once(SF_PLUGIN_DIR.'/linking/sf-links-forum.php');

		$catlist = array();
		if($newpost['post_category'])
		{
			foreach ($newpost['post_category'] as $key=>$value)
			{
				$catlist[] = sf_esc_int($value);
			}
		} else {
			$catlist[] = get_option('default_category');
		}

		# set up post stuff
		$post_content = $newpost['postcontent'];
		$post_title   = $newpost['topicname'];
		$post_status  = 'publish';
		$tags_input = $newpost['taglist'];
		$post = compact('post_content', 'post_title', 'post_status', 'tags_input');
		$blog_post_id = wp_insert_post($post);

		# save categories
		wp_set_post_categories($blog_post_id, $catlist);

		# save postmeta
		$metadata = $newpost['forumid'].'@'.$newpost['topicid'];
		sf_blog_links_control('save', $blog_post_id, $newpost['forumid'], $newpost['topicid']);

		# go back and insert blog_post_id in topic record
		$wpdb->query("UPDATE ".SFTOPICS." SET blog_post_id = ".$blog_post_id." WHERE topic_id = ".$newpost['topicid'].";");
	}

	# udpate sitemap if set for every new topic
    if (sf_get_option('sfbuildsitemap') == 2)
    {
        do_action("sm_rebuild");
    }

	if($newpost['poststatus'])
	{
		$newpost['submsg'] .= ' - '.__("Placed in Moderation", "sforum").' ';
	}
	update_sfnotice('sfmessage', '0@'.__("New Topic Saved", "sforum").$newpost['submsg'].' '.$newpost['emailmsg']);
	return $newpost;
}

# Create new Post in existing Topic ------------------------------------------------
function sf_create_post($newpost, $action)
{
	global $wpdb, $current_user;

	check_admin_referer('forum-userform_addpost', 'forum-userform_addpost');
	$spamcheck = sf_check_spammath();
	if($spamcheck[0]==true)
	{
		update_sfnotice('sfmessage', $spamcheck[1]);
		return;
	}
	# Write the post
	$newpost = sf_write_post($newpost);
	if(!$newpost['db'])
	{
		update_sfnotice('sfmessage', __("Unable to Save New Post Message", "sforum"));
		return;
	}

	$wpdb->flush();

	# Post-Save New Post Processing
	$newpost = sf_post_save_processing($newpost, $action);

	# Is there a topic status flag to save?
	if(!empty($newpost['statvalue']))
	{
		sf_update_topic_status_flag($newpost['statvalue'], $newpost['topicid']);
	}

	if($newpost['poststatus'])
	{
		$newpost['submsg'] .= ' - '.__("Placed in Moderation", "sforum").' ';
	}

	update_sfnotice('sfmessage', '0@'.__("New Post Saved", "sforum").$newpost['submsg'].' '.$newpost['emailmsg']);
	return $newpost;
}


# ==================================================================================
# DATABASE WRITE FUNCTIONS
# ==================================================================================

# Save new Topic to database -------------------------------------------------------
function sf_write_topic($newpost)
{
	global $wpdb;

	$now = "'" . current_time('mysql') . "'";

	$sql =  "INSERT INTO ".SFTOPICS;
	$sql .= " (topic_name, topic_slug, topic_date, forum_id, topic_status, topic_pinned, topic_status_flag, user_id) ";
	$sql .= "VALUES (";
	$sql .= "'".$newpost['topicname']."', ";
	$sql .= "'".$newpost['topicslug']."', ";
	$sql .= $now.", ";
	$sql .= $newpost['forumid'].", ";
	$sql .= $newpost['topiclock'].", ";
	$sql .= $newpost['topicpin'].", ";
	$sql .= $newpost['statusflag'].", ";
	if('' == $newpost['userid'])
	{
		$sql .= "NULL);";
	} else {
		$sql .= $newpost['userid'].");";
	}

	if($wpdb->query($sql) === false)
	{
		$newpost['db'] = false;
	} else {
		$newpost['db'] = true;
	}
	return $newpost;
}

# Save new Post to database --------------------------------------------------------
function sf_write_post($newpost)
{
	global $wpdb, $current_user;

	# If a Guest posting...
	if((($current_user->sfmoderated) || ($current_user->sfmodonce)) && ($current_user->guest))
	{
		$newpost['poststatus'] = 1;
		# unless mod once is on and they have posted before...
		if(($current_user->sfmodonce == true) && ($current_user->sfmoderated == false))
		{
			$prior=$wpdb->get_row("SELECT post_id FROM ".SFPOSTS." WHERE guest_name='".$newpost['guestname']."' AND guest_email='".sf_filter_email_display($newpost['guestemail'])."' AND post_status=0 LIMIT 1");
			if($prior) $newpost['poststatus']=0;
		}
	}

	# If a Member posting...
	if((($current_user->sfmoderated) || ($current_user->sfmodonce)) && ($current_user->member))
	{
		$newpost['poststatus'] = 1;
		# unless mod once is on and they have posted before...
		if(($current_user->sfmodonce == true) && ($current_user->sfmoderated == false))
		{
			$prior=$wpdb->get_row("SELECT post_id FROM ".SFPOSTS." WHERE user_id=".$newpost['userid']." AND post_status=0 LIMIT 1");
			if($prior) $newpost['poststatus']=0;
		}
	}

	# Double check forum id is correct - it has been known for a topic to have just been moved!
	$newpost['forumid'] = sf_get_topics_forum_id($newpost['topicid']);

	# Get post count in topic to enable index setting
	$index=$wpdb->get_var("SELECT COUNT(post_id) FROM ".SFPOSTS." WHERE topic_id = ".$newpost['topicid']);
	$index++;

	# if topic lock set in post reply update topic
	if($newpost['topiclock'])
	{
		$wpdb->query("UPDATE ".SFTOPICS." SET topic_status=1 WHERE topic_id=".$newpost['topicid']);
	}

	$sql =  "INSERT INTO ".SFPOSTS;
	$sql .= " (post_content, post_date, topic_id, forum_id, user_id, guest_name, guest_email, post_pinned, post_index, post_status, poster_ip) ";
	$sql .= "VALUES (";
	$sql .= "'".$newpost['postcontent']."', ";
	$sql .= $newpost['posttimestamp'].", ";
	$sql .= $newpost['topicid'].", ";
	$sql .= $newpost['forumid'].", ";
	if('' == $newpost['userid'])
	{
		$sql .= "NULL, ";
	} else {
		$sql .= $newpost['userid'].", ";
	}
	$sql .= "'".$newpost['guestname']."', ";
	$sql .= "'".$newpost['guestemail']."', ";
	$sql .= $newpost['postpin']. ", ";
	$sql .= $index.", ";
	$sql .= $newpost['poststatus'].", ";
	$sql .= "'".$newpost['ip']."');";

	if($wpdb->query($sql) === false)
	{
		$newpost['db'] = false;
	} else {
		$newpost['db'] = true;
		$newpost['postid'] = $wpdb->insert_id;

		if ($current_user->guest)
		{
    		$sfguests = sf_get_option('sfguests');
    		if ($sfguests['storecookie']) sf_write_guest_cookie($newpost['guestname'], $newpost['guestemail']);
		} else {
			$postcount = sf_get_member_item($newpost['userid'], 'posts');
			$postcount++;
			sf_update_member_item($newpost['userid'], 'posts', $postcount);

			# see if postcount qualifies member for new user group membership
			# get rankings information
			if (!$current_user->forumadmin)  # ignore for admins as they dont belong to user groups
			{
				$rankdata = sf_get_sfmeta('forum_rank');
				if ($rankdata)
				{
					# put into arrays to make easy to sort
					foreach ($rankdata as $x => $info)
					{
						$ranks['title'][$x] = $info['meta_key'];
						$data = unserialize($info['meta_value']);
						$ranks['posts'][$x] = $data['posts'];
						$ranks['usergroup'][$x] = $data['usergroup'];
					}
					# sort rankings highest to lowest
					array_multisort($ranks['posts'], SORT_DESC, $ranks['title'], $ranks['usergroup']);

					# check for new ranking
					for ($x=0; $x<count($rankdata); $x++)
					{
						if ($postcount > $ranks['posts'][$x])
						{
							# if a user group is tied to forum rank add member to the user group
							if ($ranks['usergroup'][$x] != 'none')
							{
								sfc_add_membership($ranks['usergroup'][$x], $newpost['userid']);
								break;  # only update highest rank
							}
						}
					}
				}
			}
		}
	}
	return $newpost;
}

# ==================================================================================
# POST-SAVE NEW POST ROUTINES
# ==================================================================================

# Post-Save New Post processing ----------------------------------------------------
function sf_post_save_processing($newpost, $action)
{
	global $current_user;

	# construct new url
	$newpost['url']=sf_build_url($newpost['forumslug'], $newpost['topicslug'], 0, $newpost['postid']);

	$newpost['submsg'] = '';

	# subscribing?
	if ($current_user->sfsubscriptions && !empty($newpost['topicsub']))
	{
		sf_save_subscription($newpost['topicid'], $newpost['userid'], true);
		$newpost['submsg'] .= ' '.__('and Subscribed', 'sforum');
	}

	# unsubscribing?
	if ($current_user->sfsubscriptions && !empty($newpost['topicsubend']))
	{
		sf_remove_subscription($newpost['topicid'], $newpost['userid']);
		$newpost['submsg'] .= ' '.__('and Unsubscribing', 'sforum');
	}

	# watching?
	if ($current_user->sfwatch && !empty($newpost['topicwatch']))
	{
		sf_save_watch($newpost['topicid'], $newpost['userid'], true);
		$newpost['submsg'] .= ' '.__('and Watching', 'sforum');
	}

	# stop watching?
	if ($current_user->sfwatch && !empty($newpost['topicwatchend']))
	{
		sf_remove_watch($newpost['topicid'], $newpost['userid']);
		$newpost['submsg'] .= ' '.__('and Ending Watch', 'sforum');
	}

	# save hook
	sf_process_hook('sf_hook_post_save', array($newpost, $action));

	# add to admins new post queue
	sf_add_to_waiting($newpost['topicid'], $newpost['forumid'], $newpost['postid'], $newpost['userid']);

	# send out email notifications
	$newpost['emailmsg']='';
	$newpost['emailmsg'] = sf_email_notifications($newpost);

	# Update forum, topic and post index data
	sf_build_forum_index($newpost['forumid']);
	sf_build_post_index($newpost['topicid'], $newpost['topicslug']);

	return $newpost;
}

?>