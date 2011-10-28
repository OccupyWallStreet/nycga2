<?php
/*
Simple:Press
Admin Users Support Functions
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_save_kill_spam_reg()
{
	global $wpdb;

    check_admin_referer('forum-adminform_spamkill', 'forum-adminform_spamkill');

	$x=0;
	foreach ($_POST['kill'] as $key => $value)
	{
		$wpdb->query("DELETE FROM ".SFUSERS." WHERE ID=".$key);
		$wpdb->query("DELETE FROM ".SFUSERMETA." WHERE user_id=".$key);
		$wpdb->query("DELETE FROM ".SFMEMBERS." WHERE user_id=".$key);
		$wpdb->query("DELETE FROM ".SFMEMBERSHIPS." WHERE user_id=".$key);
		$x++;
	}

	$mess = __('Spam Registrants Removed', "sforum");

	return $mess;
}

?>