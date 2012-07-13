<?php
/*
Simple:Press
Ahah linking related stuff
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

sf_forum_ahah_support();
# --------------------------------------------

sf_initialise_globals();

global $current_user;

if (!$current_user->sflinkuse) {
	echo (__('Access Denied', "sforum"));
	die();
}

include_once (SF_PLUGIN_DIR.'/linking/forms/sf-form-blog-link.php');
include_once (SF_PLUGIN_DIR.'/linking/library/sf-links-support.php');

$postid = sf_esc_int($_GET['postid']);

sf_blog_links_control('delete', $postid);

sf_populate_post_form();

die();

?>