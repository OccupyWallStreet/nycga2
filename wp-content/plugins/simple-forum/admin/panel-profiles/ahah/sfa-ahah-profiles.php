<?php
/*
Simple:Press
profiles Specials
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

sfa_admin_ahah_support();

# Check Whether User Can Manage Profiles
if (!sfc_current_user_can('SPF Manage Profiles'))
{
	echo (__('Access Denied', "sforum"));
	die();
}

global $wpdb, $SFPATHS;

$action = $_GET['action'];

if ($action == 'delete-cfield')
{
	$id = sf_esc_int($_GET['id']);
	$cfield = sf_esc_int($_GET['cfield']);
	$fname = sf_esc_str($_GET['fname']);

	# remove the custom field
	sf_delete_sfmeta($id);

	# remove any usermeta for the custom field
	$wpdb->query("DELETE FROM ".SFUSERMETA." WHERE meta_key='".$fname."'");

	# remove from admins required/display/include field lists
	$sfprofile = sf_get_option('sfprofile');
	$nrequire = array();
	$ninclude = array();
	$ndisplay = array();

	foreach($sfprofile['require'] as $key=>$value)
	{
		if($key != $fname)
		{
			$nrequire[$key]=$value;
			$ninclude[$key]=$sfprofile['include'][$key];
			$ndisplay[$key]=$sfprofile['include'][$key];
		}
	}
	$sfprofile['require']=$nrequire;
	$sfprofile['include']=$ninclude;
	$sfprofile['display']=$ndisplay;
	sf_update_option('sfprofile', $sfprofile);

}

if ($action == 'delavatar')
{
	$file = $_GET['file'];
	$path = SF_STORE_DIR.'/'.$SFPATHS['avatar-pool'].'/'.$file;
	@unlink($path);
	echo '1';
}

die();

?>