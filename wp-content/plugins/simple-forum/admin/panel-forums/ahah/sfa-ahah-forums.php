<?php
/*
Simple:Press
Forum Specials
$LastChangedDate: 2010-04-18 09:22:20 -0700 (Sun, 18 Apr 2010) $
$Rev: 3920 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

sfa_admin_ahah_support();

include_once (SF_PLUGIN_DIR.'/admin/panel-forums/support/sfa-forums-prepare.php');

# Check Whether User Can Manage Components
if (!sfc_current_user_can('SPF Manage Forums'))
{
	echo (__('Access Denied', "sforum"));
	die();
}

global $wpdb;

if(isset($_GET['action'])) $action= $_GET['action'];
if(isset($_GET['type'])) $type= sf_esc_str($_GET['type']);
if(isset($_GET['id'])) $id=sf_esc_int($_GET['id']);
if(isset($_GET['title'])) $title= sf_esc_str($_GET['title']);
if(isset($_GET['slugaction'])) $slugaction=sf_esc_str($_GET['slugaction']);


if($action == 'new')
{
	echo sfa_new_forum_sequence_options($action, $type, $id, 0);
}

if($action == 'edit')
{
	echo sfa_edit_forum_sequence_options($action, $type, $id, 0);
}

if($action == 'slug')
{
	$checkdupes = true;
	if($slugaction == 'edit') $checkdupes=false;
	$newslug = sf_create_slug($title, 'forum', $checkdupes);
	echo $newslug;
}

die();

?>