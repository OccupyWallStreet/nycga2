<?php
/*
Simple:Press
Search Form Rendering
$LastChangedDate: 2010-11-17 14:32:06 -0700 (Wed, 17 Nov 2010) $
$Rev: 4958 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sf_render_searchbox_form($pageview, $statusset=0)
{
	global $sfvars, $current_user, $wpdb;

	$out = '';

	$out.='<div id="sfsearchform">'."\n";

	$out.='<form action="'.SFHOMEURL.'index.php?sf_ahah=search" method="post" name="sfsearch">'."\n";

	$out.='<fieldset>'."\n";
	$out.='<legend>'.__("Search Forums", "sforum").':</legend>'."\n";

	$out.='<div class="sfsearchblock">'."\n";
	$out.='<input type="text" class="sfcontrol" size="20" name="searchvalue" value="" />'."\n";
	$out.='<br /><br />'."\n";
	$out.= '<img src="'.SFRESOURCES.'searchicon.png" alt="" />&nbsp;'."\n";
	$out.='<input type="submit" class="sfcontrol" name="search" value="'.esc_attr(__("Search Forum", "sforum")).'" />'."\n";
	$out.='</div>'."\n";

	# all or current forum?
	$out.='<div class="sfsearchblock">'."\n";
	$out.= '<div class="sfradioblock sfalignleft">'."\n";
	$ccheck='checked="checked"';
	$acheck='';
	if(($pageview == 'forum') || ($pageview == 'topic'))
	{
		$out.= '<input type="hidden" name="forumslug" value="'.esc_attr($sfvars['forumslug']).'" />'."\n";
		$out.= '<label class="sfradio" for="sfradio1">&nbsp;&nbsp;'.__("Current Forum", "sforum").'</label><input type="radio" id="sfradio1" name="searchoption" value="Current" '.$ccheck.' /><br />'."\n";
	} else {
		$acheck='checked="checked"';
	}
	$out.= '<label class="sfradio" for="sfradio2">&nbsp;&nbsp;'.__("All Forums", "sforum").'</label><input type="radio" id="sfradio2" name="searchoption" value="All Forums" '.$acheck.' />'."\n";
	$out.= '</div>'."\n";

	# search type?
	$out.= '<div class="sfradioblock sfalignleft">'."\n";
	$out.= '<label class="sfradio" for="sfradio3">&nbsp;&nbsp;'.__("Match Any Word", "sforum").'</label><input type="radio" id="sfradio3" name="searchtype" value="1" checked="checked" /><br />'."\n";
	$out.= '<label class="sfradio" for="sfradio4">&nbsp;&nbsp;'.__("Match All Words", "sforum").'</label><input type="radio" id="sfradio4" name="searchtype" value="2" /><br />'."\n";
	$out.= '<label class="sfradio" for="sfradio5">&nbsp;&nbsp;'.__("Match Phrase", "sforum").'</label><input type="radio" id="sfradio5" name="searchtype" value="3" />'."\n";
	$out.= '</div>'."\n";

	# topic title?
	$out.= '<div class="sfradioblock sfalignleft">'."\n";
	$out.= '<label class="sfradio" for="sfradio6">&nbsp;&nbsp;'.__("Posts And Topic Titles", "sforum").'</label><input type="radio" id="sfradio6" name="encompass" value="1" checked="checked" /><br />'."\n";
	$out.= '<label class="sfradio" for="sfradio7">&nbsp;&nbsp;'.__("Posts Only", "sforum").'</label><input type="radio" id="sfradio7" name="encompass" value="2" /><br />'."\n";
	$out.= '<label class="sfradio" for="sfradio8">&nbsp;&nbsp;'.__("Topic Titles Only", "sforum").'</label><input type="radio" id="sfradio8" name="encompass" value="3" />'."\n";
	$out.= '<label class="sfradio" for="sfradio9">&nbsp;&nbsp;'.__("Tags Only (limited to single tag)", "sforum").'</label><input type="radio" id="sfradio9" name="encompass" value="4" />'."\n";
	$out.= '</div>'."\n";

	$out.='</div><br />'."\n";

	$min = $wpdb->get_row("SHOW VARIABLES LIKE 'ft_min_word_len'");
	$max = $wpdb->get_row("SHOW VARIABLES LIKE 'ft_max_word_len'");

	$out.= '<p style="clear:both">'.sprintf(__("Minimum search word length is %s characters - Maximum search word length is %s characters", "sforum"), '<b>'.$min->Value.'</b>', '<b>'.$max->Value.'</b>').'<br />'."\n";
	$out.= '<strong>'.__('Wildcard Usage', 'sforum').':</strong><br />*&nbsp; '.__('matches any number of characters', 'sforum').'&nbsp;&nbsp;&nbsp;&nbsp;%&nbsp; '.__('matches exactly one character', 'sforum').'</p>';

 	$out.='</fieldset>'."\n";

	$temp_out = '';
	if($pageview == 'forum' && $statusset != 0)
	{
		$temp_out.= '<td><fieldset>'."\n";
		$temp_out.= '<legend>'.__("Topic Status Search (Current Forum)", "sforum").':</legend>'."\n";

		$temp_out.= '<div class="sfsearchblock">'."\n";
		$temp_out.= '<img src="'.SFRESOURCES.'searchicon.png" alt="" />&nbsp;'."\n";

		$temp_out.= sf_topic_status_select($statusset, 0, false, true);
		$temp_out.='<input type="submit" class="sfcontrol" name="statussearch" value="'.esc_attr(__("List Topics With Selected Status", "sforum")).'" />'."\n";

		$temp_out.= '</div>'."\n";
		$temp_out.= '</fieldset></td>'."\n";
	}

	if($current_user->member)
	{
		$temp_out.='<td><fieldset>'."\n";
		$temp_out.='<legend>'.__("Member Search (Current or All Forums)", "sforum").':</legend>'."\n";

		$temp_out.='<div class="sfsearchblock">'."\n";

		$temp_out.= '<img src="'.SFRESOURCES.'searchicon.png" alt="" />&nbsp;'."\n";
		$temp_out.= '<input type="hidden" name="userid" value="'.$current_user->ID.'" />'."\n";

		$temp_out.='<input type="submit" class="sfcontrol" name="membersearch" value="'.esc_attr(__("List Topics You Have Posted To", "sforum")).'" />'."\n";
		$temp_out.='<input type="submit" class="sfcontrol" name="memberstarted" value="'.esc_attr(__("View Topics You Started", "sforum")).'" />'."\n";
		$temp_out.= '</div>'."\n";
		$temp_out.='</fieldset></td>'."\n";
	}

	if (!empty($temp_out))
	{
	 	$out.= '<table><tr>';
	 	$out.= $temp_out;
		$out.= '</tr></table>';
	}
	$out.='</form>'."\n";
	$out.='</div>'."\n";

	return $out;
}

?>