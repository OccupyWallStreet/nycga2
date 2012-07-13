<?php
/*
Simple:Press
Buddy List Display
$LastChangedDate: 2010-08-11 11:38:42 -0700 (Wed, 11 Aug 2010) $
$Rev: 4383 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# let user view their permissions
function sf_render_buddylist_form()
{
	global $current_user, $sfglobals;

	$out = '';

	$out.= sf_render_queued_message();

	$out.='<br />';
	$out.= "\n\n".'<!-- Start of SPF Container (Profile) -->'."\n\n";
	$out.='<div id="sfstandardform">'."\n";
	$out.='<div class="sfheading">';
	$out.='<table><tr>'."\n";
	$out.='<td class="sficoncell">'.sf_render_avatar('user', $current_user->ID, sf_filter_email_display($current_user->user_email), '').'</td>';
	$out.='<td><p>'.__("Current Buddies List for", "sforum").':<br />'.$current_user->user_login.' ('.sf_filter_name_display($current_user->display_name).')'.'</p></td>'."\n";
	$out.='<td><img class="sfalignright" src="'. SFRESOURCES .'spacer.png" alt="" /><a class="sficon sfalignright" href="'.SFURL.'"><img src="'.SFRESOURCES.'goforum.png" alt="" title="'.esc_attr(__("Return to forum", "sforum")).'" />'.sf_render_icons("Return to forum").'</a></td>'."\n";
	$out.='</tr></table>';
	$out.='</div><br />';

	$buddies = $sfglobals['member']['buddies'];
	if ($buddies)
	{
		$out.= '<table class="sfforumtable">';
		$out.= '<tr>';
		$out.= '<th align="left">'.__("Buddy Name", "sforum").'</th>';
		$out.= '<th align="center" width="200">'.__("Manage", "sforum").'</th>';
		$out.= '</tr>';

		foreach ($buddies as $buddy)
		{
			$buddy_name = sf_filter_name_display(sf_get_member_item($buddy, "display_name"));
			$out.= '<tr>';
			$out.= '<td colspan="2" style="border-bottom:0px;padding:0px;">';
			$out.= '<div id="buddy'.$buddy.'">';
			$out.= '<table width="100%" cellspacing="0">';
			$out.= '<tr>';
			$out.= '<td align="left">'.$buddy_name.'</td>';
			$out.= '<td align="center" width="100">';
    	    $url = SFURL.'private-messaging/send/'.urlencode($buddy_name).'/';
			$out.= '<a class="sficon" href="'.$url.'"><img src="'.SFRESOURCES.'sendpm-small.png" alt="" title="'.__("Send PM to Buddy", "sforum").'" />&nbsp;'.sf_render_icons("Send PM").'</a>';
			$out.= '</td>';
			$out.= '<td align="center" width="100">';
			$text = __("Remove from Buddy List", "sforum");
            $site = SFHOMEURL."index.php?sf_ahah=profile&u=".$current_user->ID."&buddy=".$buddy;
			$out.= '<img onclick="sfjremoveItem(\''.$site.'\', \'buddy'.$buddy.'\');" src="'.SFRESOURCES.'pmdelete.png" alt="" title="'.esc_attr($text).'"/>';
			$out.= '</td>';
			$out.= '</tr>';
			$out.= '</table>';
			$out.= '</div>';
			$out.= '</td>';
			$out.= '</tr>';
		}
		$out.= '</table>';
		$out.= '<br />';
	} else {
		$out.= '<br />';
		$out.='<div class="sfmessagestrip">'.__("Sorry, you do not have any members in your Buddy List.", "sforum").'</div>';
		$out.= '<br />';
	}
	$out.= '<hr />';
	$out.= '&nbsp;<input type="button" class="sfcontrol" name="button1" value="'.esc_attr(__("Return to Profile", "sforum")).'" onclick="sfjreDirect(\''.sf_build_profile_formlink($current_user->ID).'\');" />'."\n";
	$out.= '&nbsp;<input type="button" class="sfcontrol" name="button2" value="'.esc_attr(__("Return to Forum", "sforum")).'" onclick="sfjreDirect(\''.SFURL.'\');" />'."\n";
	$out.= '<br />';

	$out.= '</div>';

	return $out;
}

?>