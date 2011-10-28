<?php
/*
Simple:Press
Forum PM Saves
$LastChangedDate: 2010-12-21 06:25:04 -0700 (Tue, 21 Dec 2010) $
$Rev: 5100 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# --------------------------------------------

global $wpdb, $current_user, $sfglobals;

sf_setup_pm_save_includes();
sf_setup_post_save_includes();
sf_forum_ahah_support();
sf_initialise_globals();
sf_setup_forum_hooks();

if (!$current_user->sfusepm) {
	echo (__('Access Denied', "sforum"));
	die();
}

# clear out the message buffer
delete_sfnotice();

# new pm post creation
sf_save_pm();

$url = SFURL."private-messaging/inbox/";

wp_redirect($url);

die();


# = SAVE NEW PM ===============================
function sf_save_pm()
{
	global $wpdb, $current_user;

	check_admin_referer('forum-userform_addpm', 'forum-userform_addpm');

	if($current_user->ID != $_POST['pmuser'])
	{
		update_sfnotice('sfmessage', '1@'.__('Access Denied', "sforum"));
		return;
	}

	# Check the pre-save hook
	if(function_exists('sf_hook_pre_pm_save'))
	{
		if(sf_process_hook('sf_hook_pre_pm_save', array($_POST['postitem'], $_POST['userid'])) == false)
		{
			update_sfnotice('sfmessage', '1@'.__('This private message has been refused', "sforum"));
			wp_redirect(SFURL."private-messaging/inbox/");
			die();
		}
	}

	# Data Checks
	$title = sf_filter_title_save(trim($_POST['pmtitle']));
	$slugtitle = $title;

	if(empty($title)) $title = __('Untitled', 'sforum');

	$messagecontent = $_POST['postitem'];
	if(empty($messagecontent))
	{
		update_sfnotice('sfmessage', '1@'.__('No message was entered', "sforum"));
		return;
	}

	$messagecontent = sf_filter_content_save($messagecontent, 'new');

    $slug = sf_filter_title_save(trim($_POST['pmslug']));

	$reply = sf_esc_int($_POST['pmreply']);
	if ($reply != 1) $reply = '0';

    if ($reply=='1')
    {
        $oldtitle = sf_filter_title_save(trim($_POST['pmoldtitle']));
        # did the title change? if so, make new slug
        if ($oldtitle != $title)
        {
           $slug = sf_create_slug($slugtitle, 'pm');
        }
    } else {
        $slug = sf_create_slug($slugtitle, 'pm');
    }
    if (empty($slug)) $slug = sf_create_slug($title, 'pm');

	$sentbox = '1';

	$tolist = $_POST['userid'];
	if (!$tolist)
	{
		update_sfnotice('sfmessage', '1@'.__('No message recipients were set', "sforum"));
		return;
	}
	$typelist = $_POST['type'];

    # are we pming all users?
    $all = false;
    if ($tolist[0] == -1)
    {
        $tolist = $wpdb->get_col("SELECT user_id FROM ".SFMEMBERS." WHERE pm = 1 AND user_id != ".$current_user->ID);
        $all = true;
    }

    # process recipient list
	foreach ($tolist as $key => $recipient)
	{
		$recipient = sf_esc_int($recipient);
		$now = "'".current_time('mysql')."'";
		$sql  = "INSERT INTO ".SFMESSAGES;
		$sql .= " (sent_date, from_id, to_id, title, message, sentbox, message_slug, is_reply, type) ";
		$sql .= "VALUES (";
		$sql .= $now.", ";
		$sql .= $current_user->ID.", ";
		$sql .= $recipient.", ";
		$sql .= "'".$title."', ";
		$sql .= "'".$messagecontent."', ";
		$sql .= $sentbox.", ";
		$sql .= "'".$slug."', ";
		$sql .= $reply.", ";
        if ($all)
        {
		  $pmtype = 1;
        } else {
		  $pmtype = sf_esc_int($typelist[$key]);
        }
		$sql .= $pmtype.");";
		if($wpdb->query($sql) === false)
		{
			update_sfnotice('sfmessage', '1@'.__("Unable to Save New Post Message", "sforum"));
			return;
		}
	}

	$emailmsg = '';
	$sfpm = sf_get_option('sfpm');
	if($sfpm['sfpmemail'])
	{
		foreach($tolist as $recipient)
		{
			$uopts = sf_get_member_item($recipient,'user_options');
			if ($uopts['pmemail'])
			{
				$emailmsg = sf_pm_send_email(sf_filter_name_display($current_user->display_name), $recipient, $title);
			}
		}
	}

	# save hook
	sf_process_hook('sf_hook_pm_save', array($current_user->ID, $tolist, $title, $messagecontent, $reply, $pmtype));

	if ($emailmsg != '') $emailmsg = ' - '.$emailmsg;
	update_sfnotice('sfmessage', '0@'.__("Message Posted", "sforum").$emailmsg);
	return;
}

function sf_pm_send_email($sender, $recipient, $title)
{
	global $wpdb, $siteurl;
	global $wp_rewrite;

	$eol = "\r\n";

	# get user email address
	$email = $wpdb->get_var("SELECT user_email FROM ".SFUSERS." WHERE ID=".$recipient);

	$msg = '';

	# recipient message
	$url = SFURL."private-messaging/inbox/";

	$msg.= __('There is a New Private Message for you on the forum at', "sforum").': '.$url.$eol.$eol;
	$msg.= __('From', "sforum").': '.$sender.$eol;
	$msg.= __('Title', "sforum").': '.$title.$eol.$eol;
	$msg.= SFURL.$eol;

	$email_status = sf_send_email($email, get_option('blogname').' '.__('New Private Message', "sforum"), $msg);
	return $email_status[1];
}

?>