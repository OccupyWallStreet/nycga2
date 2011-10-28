<?php
/*
Simple:Press
User Permissions Display
$LastChangedDate: 2010-05-23 15:19:43 -0700 (Sun, 23 May 2010) $
$Rev: 4069 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# let user view their permissions
function sf_render_permissions_form()
{
	global $current_user;

	# need access to the roles and they are defined in admin
	include_once(SF_PLUGIN_DIR.'/admin/library/sfa-support.php');

	$out = '';

	$out.= sf_render_queued_message();

	$out.='<br />';
	$out.= "\n\n".'<!-- Start of SPF Container (Profile) -->'."\n\n";
	$out.='<div id="sfstandardform">'."\n";
	$out.='<div class="sfheading">';
	$out.='<table><tr>'."\n";
	$out.='<td class="sficoncell">'.sf_render_avatar('user', $current_user->ID, sf_filter_email_display($current_user->user_email), '').'</td>';
	$out.='<td><p>'.__("Current Permission Settings for", "sforum").':<br />'.$current_user->user_login.' ('.sf_filter_name_display($current_user->display_name).')'.'</p></td>'."\n";
	$out.='<td><img class="sfalignright" src="'. SFRESOURCES .'spacer.png" alt="" /><a class="sficon sfalignright" href="'.SFURL.'"><img src="'.SFRESOURCES.'goforum.png" alt="" title="'.esc_attr(__("Return to forum", "sforum")).'" />'.sf_render_icons("Return to forum").'</a></td>'."\n";
	$out.='</tr></table>';
	$out.='</div><br />';

	$groups = sf_get_combined_groups_and_forums();
	if ($groups)
	{
		foreach ($groups as $group)
		{
			$out.= '<div class="sfheading">';
			$out.= '<table>';
			$out.= '<tr>';
			$out.= '<td><div class="sftitle"><p>'.sf_filter_title_display($group['group_name']).'</p></div><div class="sfdescription">'.sf_filter_text_display($group['group_desc']).'</div></td>';
			$out.= '</tr>';
			$out.= '</table>';
			$out.= '</div>';
			$out.= '<table class="sfforumtable">';
			$alt = '';
			if($group['forums'])
			{
				foreach($group['forums'] as $forum)
				{
					$out.= '<tr>';
					$out.= '<td class="'.$alt.'"><div class="sftitle"><p>'.sf_filter_title_display($forum['forum_name']).'</p></div><div class="sfdescription">'.sf_filter_text_display($forum['forum_desc']).'</div></td>';
					$out.= '<td class="'.$alt.'" align="center" width="200px">';
					$out.= '<input style="width:150px" type="button" class="sfcontrol" value="'.esc_attr(__("View Permissions", "sforum")).'" onclick="sfjtoggleLayer(\'perm'.$forum['forum_id'].'\');" />';

					$out.= '</td>';
					$out.= '</tr>';
					$out.= '<tr>';
					$out.= '<td class="'.$alt.'" colspan="2">';
					$out.= '<div id="perm'.$forum['forum_id'].'" class="inline_edit">';
					$out.= '<table class="sfposttable" border="0" cellspacing="5">';
					$out.= '<tr>';
					$items = count($sfactions['action']);
					$cols = 2;
					$rows  = ($items / $cols);
					$lastrow = $rows;
					$lastcol = $cols;
					$curcol = 0;
					if (!is_int($rows))
					{
						$rows = (intval($rows) + 1);
						$lastrow = $rows - 1;
						$lastcol = ($items % $cols);
					}
					$thisrow = 0;
					foreach ($sfactions["action"] as $index => $action)
					{
						$button = 'b-'.$index;
						if ($thisrow == 0)
						{
							$out.= '<td width="50%" class="sfpostcontent">';
							$out.= '<table class="form-table">';
							$curcol++;
						}
						$out.= '<tr>';
						$out.= '<td>';
						if (sf_user_can($current_user->ID, $action, $forum['forum_id']))
						{
							$out.= '<img src="'.SFRESOURCES.'success.png" alt="" />&nbsp;&nbsp;'.__($action, "sforum");
						} else {
							$out.= '<img src="'.SFRESOURCES.'failure.png" alt="" />&nbsp;&nbsp;'.__($action, "sforum");										}
						$out.= '</td>';
						$out.= '</tr>';
						$thisrow++;
						if (($curcol <= $lastcol && $thisrow == $rows) || ($curcol > $lastcol && $thisrow == $lastrow))
						{
							$out.= '</table>';
							$out.= '</td>';
							$thisrow = 0;
						}
					}
					$out.= '</tr>';
					$out.= '<tr style="height:40px;">';
					$out.= '<td colspan="2">';
					$string = __("Close", "sforum");
					$out.= '<input style="width:50px" type="button" class="sfcontrol" name="cancel" value="'.esc_attr($string).'" onclick="sfjtoggleLayer(\'perm'.$forum['forum_id'].'\');" /><br /><br />';
					$out.= '</td>';
					$out.= '</tr>';
					$out.= '</table>';
					$out.= '</div>';
					$out.= '</td>';
					$out.= '</tr>';

					if ($alt == '') $alt = 'sfalt'; else $alt = '';
				}
			} else {
				$out.= '<div class="sfmessagestrip">'.__("There are No Forums defined in this Group", "sforum").'</div>'."\n";
			}
			$out.= '</table>';
			$out.= '<br />';
		}
	} else {
		$out.= '<br />';
		$out.='<div class="sfmessagestrip">'.__("Sorry, you do not have permissions to any Groups of Forums.", "sforum").'</div>';
		$out.= '<br /><hr />';
	}

	$out.='&nbsp;<input type="button" class="sfcontrol" name="button1" value="'.esc_attr(__("Return to Profile", "sforum")).'" onclick="sfjreDirect(\''.sf_build_profile_formlink($current_user->ID).'\');" />'."\n";
	$out.='&nbsp;<input type="button" class="sfcontrol" name="button2" value="'.esc_attr(__("Return to Forum", "sforum")).'" onclick="sfjreDirect(\''.SFURL.'\');" />'."\n";

	$out.= '</div>';

	return $out;
}

?>