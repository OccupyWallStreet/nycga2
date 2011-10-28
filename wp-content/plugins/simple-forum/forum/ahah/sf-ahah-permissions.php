<?php
/*
Simple:Press
Ahah call for acknowledgements
$LastChangedDate: 2009-09-29 00:45:56 +0100 (Tue, 29 Sep 2009) $
$Rev: 2714 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

sf_forum_ahah_support();
sf_setup_admin_constants();

include_once(SF_PLUGIN_DIR.'/admin/library/sfa-support.php');
# ----------------------------------

	global $sfglobals;

	$forumid = sf_esc_int($_GET['forum']);
	if (empty($forumid)) die();

	$forumname = esc_html($_GET['forumname']);
	if (!empty($_GET['user']))
	{
		$userid  = sf_esc_int($_GET['user']);
		$displayname = esc_html($_GET['displayname']);
	} else {
		$displayname = __('Guest', 'sforum');
	}

	sf_initialise_globals($forumid);

	$sfmemberopts = sf_get_option('sfmemberopts');
	if (!$sfmemberopts['sfviewperm'])
	{
		echo (__('Access Denied', "sforum"));
		die();
	}

	$out ='';
	$out.= '<table cellpadding="8"><tr>';
	$out.= '<td><img src="'.SFRESOURCES.'user-permissions.png" alt="" title="'.esc_attr(__("Your Permissions", "sforum")).'"/></td>';
	$out.= '<td>'.sprintf(__("Access Permissions for %s to forum %s", "sforum"), '<b>'.$displayname.'</b><br />', '<b>'.$forumname.'</b>').'</td>';
	$out.= '</tr></table><br />';

	$cols = 3;
	$curcol = 0;

	$out.= '<table class="sfpopuptable" border="0" cellspacing="2">';

	$perms = sf_get_global_permissions($forumid);
	foreach ($perms as $action => $p)
	{
		if ($curcol == 0)
		{
			$curcol++;
			$out.= '<tr valign="middle">';
		}
		$out.= '<td valign="middle">';
		if ($p)
		{
			$out.= '<img src="'.SFRESOURCES.'permission-yes.png" />&nbsp;&nbsp;'.__($action, "sforum");
		} else {
			$out.= '<img src="'.SFRESOURCES.'permission-no.png" />&nbsp;&nbsp;'.__($action, "sforum");
		}
		$out.= '</td>';

		$curcol++;
		if($curcol > $cols)
		{
			$out.= '</tr>';
			$curcol = 0;
		}
	}

	$out.= '</table>';
	echo $out;

die();

?>