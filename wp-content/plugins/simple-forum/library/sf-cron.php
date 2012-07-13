<?php
/*
Simple:Press
Cron - global code
$LastChangedDate: 2011-01-13 07:22:37 -0700 (Thu, 13 Jan 2011) $
$Rev: 5302 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

function spf_remove_pms()
{
	global $wpdb;

	# make sure auto removal is enabled
	$sfpm = sf_get_option('sfpm');
	if ($sfpm['sfpmremove'])
	{
		$wpdb->query("DELETE FROM ".SFMESSAGES." WHERE sent_date < DATE_SUB(NOW(), INTERVAL ".$sfpm['sfpmkeep']." DAY)");
	} else {
		wp_clear_scheduled_hook('spf_cron_pm');
	}
}

function spf_remove_users()
{
	require_once(ABSPATH.'wp-admin/includes/user.php');

	global $wpdb;

	# make sure auto removal is enabled
	$sfuser = sf_get_option('sfuserremoval');
	if ($sfuser['sfuserremove'])
	{
		# see if removing users with no posts
		if ($sfuser['sfusernoposts'])
		{
			$users = $wpdb->get_results("SELECT ".SFUSERS.".ID FROM ".SFUSERS."
										JOIN ".SFMEMBERS." on ".SFUSERS.".ID = ".SFMEMBERS.".user_id
										LEFT JOIN ".$wpdb->posts." ON ".SFUSERS.".ID = ".$wpdb->posts.".post_author
										WHERE user_registered < DATE_SUB(NOW(), INTERVAL ".$sfuser['sfuserperiod']." DAY)
										AND post_author IS NULL
										AND posts < 1");

			if ($users)
			{
				foreach ($users as $user)
				{
					wp_delete_user($user->ID);
				}
			}
		}

		# see if removing inactive users
		if ($sfuser['sfuserinactive'])
		{
			$users = $wpdb->get_results("SELECT user_id FROM ".SFMEMBERS."
										WHERE lastvisit < DATE_SUB(NOW(), INTERVAL ".$sfuser['sfuserperiod']." DAY)");
			if ($users)
			{
				foreach ($users as $user)
				{
					wp_delete_user($user->user_id);
				}
			}
		}
		#$wpdb->query("DELETE FROM ".SFMESSAGES." WHERE sent_date < DATE_SUB(NOW(), INTERVAL ".$sfpm['sfpmkeep']." DAY)");
	} else {
		wp_clear_scheduled_hook('spf_cron_user');
	}
}

function spf_generate_sitemap()
{
    if (sf_get_option('sfbuildsitemap') == 3)
    {
        do_action("sm_rebuild");
    } else {
    	wp_clear_scheduled_hook('spf_cron_sitemap');
    }
}

?>