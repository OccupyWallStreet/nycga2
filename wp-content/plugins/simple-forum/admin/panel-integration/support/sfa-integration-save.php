<?php
/*
Simple:Press
Admin integration Update Support Functions
$LastChangedDate: 2010-07-16 10:56:09 -0700 (Fri, 16 Jul 2010) $
$Rev: 4276 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_save_integration_page_data()
{
	global $wpdb;

    check_admin_referer('forum-adminform_integration', 'forum-adminform_integration');

	$mess = '';
	$slugid = $_POST['slug'];

	if($slugid == '' || $slugid == 0)
	{
		$setslug = '';
		$setpage = 0;
	} else {
		$setpage = $slugid;
		$page = $wpdb->get_row("SELECT ID, post_name, post_parent FROM $wpdb->posts WHERE ID = ".$slugid);
		$setslug = $page->post_name;

		if($page->post_parent)
		{
			$parent = $page->post_parent;
			while($parent)
			{
				$thispage = $wpdb->get_row("SELECT ID, post_name, post_parent FROM ".$wpdb->posts." WHERE ID = ".$parent);
				$setslug = $thispage->post_name.'/'.$setslug;
				$parent = $thispage->post_parent;
			}
		}
	}

	sf_update_option('sfpage', $setpage);
	sf_update_option('sfslug', $setslug);
	sfa_update_check_option('sfinloop');
	sfa_update_check_option('sfmultiplecontent');
	sfa_update_check_option('sfscriptfoot');

	if(!$setpage)
	{
		$mess.= __("Page Slug Missing", "sforum").$endmsg;
		$mess.= __(" - Unable to determine forum permalink without it", "sforum");
	} else {
		$mess.= __('Forum Page and Slug Updated', "sforum");
		sf_update_option('sfpermalink', get_permalink($setpage));
	}

	return $mess;
}

?>