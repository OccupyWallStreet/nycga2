<?php
/*
Simple:Press
Admin Toolbox Update Options Support Functions
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_save_toolbox_data()
{
	check_admin_referer('forum-adminform_toolbox', 'forum-adminform_toolbox');

	$mess = __('Options Updated', "sforum").$mess;

	sfa_update_check_option('sfcheck');
	sf_update_option('sfcbexclusions', sf_filter_title_save(trim($_POST['sfcbexclusions'])));

	# build number update
	if (empty($_POST['sfbuild']) || $_POST['sfbuild'] == 0)
	{
		sf_update_option('sfbuild', SFBUILD);
	} else {
		if($_POST['sfbuild'] != SFBUILD && $_POST['sfforceupgrade'])
		{
			sf_update_option('sfbuild', sf_esc_int($_POST['sfbuild']));
		}
	}

	sf_update_option('sfforceupgrade', $_POST['sfforceupgrade']);

	return $mess;
}

function sfa_save_uninstall_data()
{
	check_admin_referer('forum-adminform_uninstall', 'forum-adminform_uninstall');
	$mess = __('Options Updated', "sforum").$mess;

	# Are we setting the uninstall flag?
	sfa_update_check_option('sfuninstall');
	if ($_POST['sfuninstall'])
	{
		$mess = __("Simple:Press will be removed when de-activated", "sforum");
	}
	return $mess;
}

# function to delete the topics that were selected from the filtered list of topics
function sfa_save_toolbox_prune_topics()
{
	global $wpdb, $current_user;

    check_admin_referer('forum-adminform_prunetopics', 'forum-adminform_prunetopics');

	# current user extensions not loaded for admin functions so give self topic delete rights
	$current_user->sfdelete = 1;

	# loop through all of the filtered topics to see which ones we want to delete
	$tcount = sf_esc_int($_POST['tcount']);
	for ($x=0; $x<$tcount; $x++)
	{
		if (isset($_POST['topic'.$x]))
		{
			# call core function to remove topics/posts/subscriptions etc
			include_once (SF_PLUGIN_DIR.'/linking/sf-links-forum.php');
			sf_delete_topic(sf_esc_int($_POST['topic'.$x]), false);
		}
	}

    $mess = __("Database Pruned!", "sforum");

    return $mess;
}

?>