<?php
/*
Simple:Press
Component Specials
$LastChangedDate: 2010-04-10 06:06:45 -0700 (Sat, 10 Apr 2010) $
$Rev: 3884 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

sfa_admin_ahah_support();

# Check Whether User Can Manage Components
if (!sfc_current_user_can('SPF Manage Components'))
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

	# remove the custom field
	sf_delete_sfmeta($id);

	# remove any usermeta for the custom field
	$wpdb->query("DELETE FROM ".SFUSERMETA." WHERE meta_key='sfcustomfield".$cfield."'");
}

if ($action == 'del_rank')
{
	$key = sf_esc_int($_GET['key']);

	# remove the forum rank
	$sql = "DELETE FROM ".SFMETA." WHERE meta_type='forum_rank' AND meta_id='".$key."'";
	$wpdb->query($sql);
}

if ($action == 'del_specialrank')
{
	$key = sf_esc_int($_GET['key']);

	# remove the forum rank
	$sql = "DELETE FROM ".SFMETA." WHERE meta_type='special_rank' AND meta_id='".$key."'";
	$wpdb->query($sql);
}

if ($action == 'show')
{
	$key = sf_esc_int($_GET['key']);

	$rank = sf_get_sfmeta('special_rank', false, $key);
	$data = unserialize($rank[0]['meta_value']);
	echo '<fieldset class="sfsubfieldset">';
    echo '<legend>'.__("Special Rank Members", "sforum").'</legend>';
	if ($data['users'])
	{
		echo '<ul class="memberlist">';
		for ($x=0; $x<count($data['users']); $x++)
		{
			echo '<li>'.sf_filter_name_display(sf_get_member_item($data['users'][$x], 'display_name')).'</li>';
		}
		echo '</ul>';
	} else {
		echo __("No Users with this Special Rank.", "sforum");
	}

	echo '</fieldset>';
}

if ($action == 'add')
{
	$key = sf_esc_int($_GET['key']);

	echo '<select class="sfacontrol" multiple="multiple" size="10" name="amember_id[]">';
	$empty = true;
	$users = $wpdb->get_results("SELECT user_id, display_name FROM ".SFMEMBERS." ORDER BY display_name");
	$rank = sf_get_sfmeta('special_rank', false, $key);
	$data = unserialize($rank[0]['meta_value']);
	$memberlist = $data['users'];
	foreach ($users as $user)
	{
		if (!in_array($user->user_id, $memberlist))
		{
			echo '<option value="'.$user->user_id.'">'.sf_filter_name_display($user->display_name).'</option>'."\n";
			$empty = false;
		}
	}

	if ($empty) echo '<option disabled="disabled" value="-1">'.__("No Members To Add", "sforum").'</option>';
	echo '</select>';
}

if ($action == 'del')
{
	$key = sf_esc_int($_GET['key']);

	echo '<select class="sfacontrol" multiple="multiple" size="10" name="dmember_id[]">';
	$rank = sf_get_sfmeta('special_rank', false, $key);
	$data = unserialize($rank[0]['meta_value']);
	$memberlist = $data['users'];
	if ($memberlist) {
		for ($x=0; $x<count($memberlist); $x++)
		{
			echo '<option value="'.$memberlist[$x].'">'.sf_filter_name_display(sf_get_member_item($memberlist[$x], 'display_name')).'</option>'."\n";
		}
	} else {
		echo '<option disabled="disabled" value="-1">'.__("No Users To Delete", "sforum").'</option>';
	}
	echo '</select>';
}

if ($action == 'delsmiley')
{
	$file = sf_esc_str($_GET['file']);
	$path = SF_STORE_DIR.'/'.$SFPATHS['smileys'].'/'.$file;
	@unlink($path);

	# load smiles from sfmeta
	$smileys = array();
	$meta = sf_get_sfmeta('smileys', 'smileys');
	$smeta = $meta[0]['meta_value'];
	$smileys = unserialize($smeta);

	# now cycle through to remove this entry and resave
	if($smileys)
	{
		$newsmileys = array();
		foreach ($smileys as $name => $info)
		{
			if($info[0] != $file)
			{
				$newsmileys[$name][0]=sf_filter_title_save($info[0]);
				$newsmileys[$name][1]=sf_filter_name_save($info[1]);
			}
		}
		$smeta = serialize($newsmileys);
		sf_update_sfmeta('smileys', 'smileys', $smeta, $meta[0]['meta_id']);
	}

	echo '1';
}

if ($action == 'delbadge')
{
	$file = sf_esc_str($_GET['file']);
	$path = SF_STORE_DIR.'/'.$SFPATHS['ranks'].'/'.$file;
	@unlink($path);
	echo '1';
}

if ($action == 'delicon')
{
	$file = sf_esc_str($_GET['file']);
	$path = SF_STORE_DIR.'/'.$SFPATHS['custom-icons'].'/'.$file;
	@unlink($path);
	echo '1';
}

die();

?>