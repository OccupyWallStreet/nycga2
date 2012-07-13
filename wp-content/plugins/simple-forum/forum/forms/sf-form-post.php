<?php
/*
Simple:Press
Post Form Rendering
$LastChangedDate: 2010-12-11 15:48:23 -0700 (Sat, 11 Dec 2010) $
$Rev: 5048 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sf_render_add_post_form($forumid, $topicid, $topicname, $statusset, $statusflag, $user_started, $subs, $watches)
{
	global $sfvars, $current_user, $sfglobals;

	$out='';
	$msg=sf_validation_messages('post');

	$editor="TM";
	if($sfglobals['editor']['sfeditor'] != RICHTEXT) $editor="QT";

	$out.='<div class="sfformblock">';
	$out.='<div id="sfpostform">'."\n";
	$out.='<br />'."\n";
	$out.='<fieldset>'."\n";

	$out.='<legend>'.__("Reply to Topic", "sforum").':</legend>'."\n";
	$out.='<div class="sfmessagestrip">'.$topicname.'</div>';

	$sfpostmsg=sf_get_option('sfpostmsg');
	if ($sfpostmsg['sfpostmsgpost'])
	{
		$out.='<div id="sfeditormsg">'.sf_filter_text_display($sfpostmsg['sfpostmsgtext']).'</div>';
	}

	$out.= '<form class="sfpostformcontent" action="'.SFHOMEURL.'index.php?sf_ahah=post" method="post" id="addpost" name="addpost" onsubmit="return sfjvalidatePostForm(this, \''.$editor.'\', \''.$msg[0].'\', \''.$msg[1].'\', \''.$msg[2].'\', \''.$msg[3].'\', \''.$msg[4].'\', \''.$msg[5].'\', \''.$msg[6].'\', \''.$msg[7].'\', \''.$msg[8].'\')">'."\n";

	$out.= sfc_create_nonce('forum-userform_addpost');

	$out.='<input type="hidden" name="forumid" value="'.$forumid.'" />'."\n";
	$out.='<input type="hidden" name="forumslug" value="'.$sfvars['forumslug'].'" />'."\n";
	$out.='<input type="hidden" name="topicid" value="'.$topicid.'" />'."\n";
	$out.='<input type="hidden" name="topicslug" value="'.$sfvars['topicslug'].'" />'."\n";

	$out.='<table class="sfpostsavetable">';
	$data = 0;

	if($current_user->guest)
	{
		$data = 1;
		$out.='<tr><td width="50%"><p><b>'.__("Guest Name (Required)", "sforum").':</b></p>'."\n";
		$out.='<input type="text" tabindex="100" class="sfcontrol sfpostcontrol" name="guestname" value="'.esc_attr($current_user->guestname).'" style="width: 75%;" /></td>'."\n";

		$sfguests = sf_get_option('sfguests');
		if ($sfguests['reqemail'])
		{
			$out.='<td><p><b>'.__("Guest Email (Required)", "sforum").':</b></p>'."\n";
			$out.='<input type="text"  tabindex="101" class="sfcontrol sfpostcontrol" name="guestemail" value="'.esc_attr($current_user->guestemail).'" style="width: 75%;" /></td></tr>'."\n";
		}
	}
	if($current_user->sfmoderated)
	{
		$data = 1;
		$out.='<tr><td colspan="2"><p><small>'.__("NOTE: New Posts are subject to administrator approval before being displayed", "sforum").'</small></p></td></tr>'."\n";
	} elseif($current_user->sfmodonce) {
		$data = 1;
		$out.='<tr><td colspan="2"><p><small>'.__("NOTE: First Posts are subject to administrator approval before being displayed", "sforum").'</small></p></td></tr>'."\n";
	}

	if ($data == 0) $out.= '<tr style="display:none"><td></td></tr>';
	$out.='</table>';

	$out.='<div class="sfformcontainer">'."\n";

	$out.= sf_setup_editor(102);

	$subscribed='';
	$watching='';
	if ($current_user->sfsubscriptions) $subscribed = sf_is_subscribed($current_user->ID, $topicid);
	if ($current_user->sfwatch) $watching = sf_is_watching($current_user->ID, $topicid);

	# Save/Smileys/Options
	$out.= '<table class="sfpostsavetable">'."\n";

	# work out what we need to display
	$display = array();
	$display['smileys']=false;
	$display['options']=false;
	$display['status']=false;

	if($sfglobals['smileyoptions']['sfsmallow'] && $sfglobals['smileyoptions']['sfsmtype']==1) $display['smileys']=true;
	if($current_user->sfsubscriptions ||
	   $current_user->sfwatch ||
	   $current_user->sfpinposts ||
	   $current_user->sflock ||
	   $current_user->forumadmin ||
	   $current_user->moderator)
	   $display['options']=true;
	if (($statusset != 0 && $current_user->sftopicstatus) || ($current_user->forumadmin) || ($current_user->moderator)) $display['status']=true;

	# Smileys
	$span=1;
	if($display['options']) $span='2';
	if($display['smileys'])
	{
		$out.= '<tr><td class="sfpostheading" width="100%" colspan="'.$span.'">'.__("Smileys", "sforum").'</td></tr>';
		$out.= '<tr><td  width="100%" colspan="'.$span.'" valign="top">'."\n";
		$out.= sf_render_smileys();
		$out.= '</td></tr>'."\n";
	}

	# Options and save
	$out.='<tr>';
	if($display['options'] || $display['status'])
	{
		$out.= '<td class="sfpostheading" width="50%">'.__("Options", "sforum").'</td>';
	}
	$out.= '<td class="sfpostheading">'.__("Post New Reply", "sforum").'</td>';
	$out.= '</tr><tr>';

	# Options
	if($display['options'] || $display['status'])
	{
		$out.= '<td width="50%" valign="top">'."\n";

		if ($statusset !=0)
		{
			if($current_user->moderator || $current_user->sftopicstatus || $current_user->ID == $user_started)
			{
				if($statusflag == 0 ? $tsmsg=__("Assign Topic Status", "sforum") : $tsmsg=__("Change Topic Status", "sforum"));
				$out.= '&nbsp;&nbsp;<label><small>'.$tsmsg.':  '.sf_topic_status_select($statusset, $statusflag, true).'</small></label>';
				$out.= '<div class="sfclear"></div><br />';
			}
		}

		$out.='<table class="sfcheckoptions" cellspacing="4" cellpadding="4">';

		if ($current_user->sfsubscriptions)
		{
			if (!$subscribed)
			{
				if ($sfglobals['member']['user_options']['autosubpost']) $checked = ' checked="checked"'; else $checked = '';
				$out.='<tr><td width="20"><img src="'.SFRESOURCES.'usersubscribed.png" alt="" title="'.__("Subscribe to this Topic", "sforum").'" /></td><td><input type="checkbox"'.$checked.' name="topicsub" id="sftopicsub" tabindex="106" /><label for="sftopicsub">&nbsp;&nbsp;'.sf_render_icons("Subscribe to this Topic").'&nbsp;&nbsp;&nbsp;</label></td></tr>'."\n";
			} else {
				$out.='<tr><td width="20"><img src="'.SFRESOURCES.'usersubscribed.png" alt="" title="'.__("Unsubscribe from this Topic", "sforum").'" /></td><td><input type="checkbox" name="topicsubend" id="sftopicsubend" tabindex="107" /><label for="sftopicsubend">&nbsp;&nbsp;'.sf_render_icons("Unsubscribe from this Topic").'&nbsp;&nbsp;&nbsp;</label></td></tr>'."\n";
			}
		}

		if ($current_user->sfwatch)
		{
			if (!$watching)
			{
				$out.='<tr><td width="20"><img src="'.SFRESOURCES.'watchicon.png" alt="" title="'.__("Watch this Topic", "sforum").'" /></td><td><input type="checkbox" name="topicwatch" id="sftopicwatch" tabindex="108" /><label for="sftopicwatch">&nbsp;&nbsp;'.sf_render_icons("Watch this Topic").'&nbsp;&nbsp;&nbsp;</label></td></tr>'."\n";
			} else {
				$out.='<tr><td width="20"><img src="'.SFRESOURCES.'watchicon.png" alt="" title="'.__("Stop Watching this Topic", "sforum").'" /></td><td><input type="checkbox" name="topicwatchend" id="sftopicwatchend" tabindex="109" /><label for="sftopicwatchend">&nbsp;&nbsp;'.sf_render_icons("Stop Watching this Topic").'&nbsp;&nbsp;&nbsp;</label></td></tr>'."\n";
			}
		}

		if($current_user->sflock)
		{
			$out.='<tr><td width="20"><img src="'.SFRESOURCES.'locked.png" alt="" title="'.__("Lock this Topic", "sforum").'" /></td><td><input type="checkbox" name="topiclock" id="sftopiclock" tabindex="110" /><label for="sftopiclock">&nbsp;&nbsp;'.sf_render_icons("Lock this Topic").'</label></td></tr>'."\n";
		}

		if($current_user->moderator)
		{
			$out.='<tr><td width="20"><img src="'.SFRESOURCES.'pin.png" alt="" title="'.__("Pin this Post", "sforum").'" /></td><td><input type="checkbox" name="postpin" id="sfpostpin" tabindex="110" /><label for="sfpostpin">&nbsp;&nbsp;'.sf_render_icons("Pin this Post").'&nbsp;&nbsp;&nbsp;</label></td></tr>'."\n";
			$out.='<tr><td width="20"><img src="'.SFRESOURCES.'clock.png" alt="" title="'.__("Edit Post Timestamp", "sforum").'" /></td><td><input type="checkbox" tabindex="111" id="sfeditTimestamp" name="editTimestamp" onchange="sfjtoggleLayer(\'sftimestamp\');"/><label for="sfeditTimestamp">&nbsp;&nbsp;'.sf_render_icons("Edit Timestamp").'</label></td></tr>'."\n";
		}
		$out.= '</table>';

		if($current_user->moderator)
		{

			global $wp_locale;
			$time_adj = time() + (get_option( 'gmt_offset' ) * 3600 );
			$dd = gmdate( 'd', $time_adj );
			$mm = gmdate( 'm', $time_adj );
			$yy = gmdate( 'Y', $time_adj );
			$hh = gmdate( 'H', $time_adj );
			$mn = gmdate( 'i', $time_adj );
			$ss = gmdate( 's', $time_adj );

			$out.='<div id="sftimestamp">'."\n";
			$out.='<select tabindex="112" name="tsMonth" onchange="editTimestamp.checked=true">'."\n";
			for ( $i = 1; $i < 13; $i = $i +1 ){
				$out.= "\t\t\t<option value=\"$i\"";
				if ( $i == $mm )
					$out.= ' selected="selected"';
				if(class_exists('WP_Locale'))
				{
					$out.= '>' . $wp_locale->get_month( $i ) . "</option>\n";
				} else {
					$out.= '>' . $month[$i] . "</option>\n";
				}
			}
			$out.='</select>'."\n";

			$out.='<input class="sfcontrolTS" tabindex="113" type="text" id="tsDay" name="tsDay" value="'.$dd.'" size="2" maxlength="2"/>'."\n";
			$out.='<input class="sfcontrolTS" tabindex="114" type="text" id="tsYear" name="tsYear" value="'.$yy.'" size="4" maxlength="5"/>@'."\n";
			$out.='<input class="sfcontrolTS" tabindex="115" type="text" id="tsHour" name="tsHour" value="'.$hh.'" size="2" maxlength="2"/> :'."\n";
			$out.='<input class="sfcontrolTS" tabindex="116" type="text" id="tsMinute" name="tsMinute" value="'.$mn.'" size="2" maxlength="2"/>'."\n";
			$out.='<input class="sfcontrolTS" tabindex="117" type="hidden" id="tsSecond" name="tsSecond" value="'.$ss.'" size="2" maxlength="2"/>'."\n";
			$out.='</div>'."\n";
		}
		$out.= '</td>'."\n";
	}

	# Save Reply
	$out.= '<td valign="top">'."\n";

	# Start Spam Measures
	if($current_user->sfspam ? $usemath = false : $usemath = true);
	$enabled=' ';
	if($usemath)
	{
		$enabled = ' disabled="disabled" ';
		$out.='<div id="sfhide">'."\n";
		$out.='<p>Guest URL (required)<br />'."\n";
		$out.='<input type="text" class="yscontrol" size="30" name="url" value="" /></p>'."\n";
		$out.='</div>'."\n";

		$spammath = sf_math_spam_build();

		$out.='<p><strong>'.__("Math Required!", "sforum").'</strong><br />'."\n";
		$out.=sprintf(__("What is the sum of: <strong> %s + %s </strong>", "sforum"), '<br />'.$spammath[0], $spammath[1]).'&nbsp;&nbsp;&nbsp;'."\n";
		$out.='<input type="text" tabindex="103" class="sfcontrol" size="4" name="sfvalue1" id="sfvalue1" value="" onkeyup="sfjsetPostButton(this, '.$spammath[0].', '.$spammath[1].', \''.esc_js(__("Post New Reply", "sforum")).'\', \''.esc_js(__("Do Math To Save", "sforum")).'\')" /></p>'."\n";
		$out.='<input type="hidden" name="sfvalue2" id ="sfvalue2" value="'.$spammath[2].'" />'."\n";
	}
	# End Spam Measures

	$buttontext = esc_attr(__("Post New Reply", "sforum"));
	if($usemath) $buttontext = esc_attr(__("Do Math To Save", "sforum"));

	$out.='<input type="submit"'.$enabled.'tabindex="104" class="sfcontrol" id="sfsave" name="newpost" value="'.$buttontext.'" />'."\n";
	$out.='<input type="button" tabindex="105" class="sfcontrol" id="sfcancel" name="cancel" value="'.__("Cancel", "sforum").'" onclick="sfjCancelEditor(\''.$editor.'\');" />'."\n";

	$out.='<div class="highslide-html-content" id="my-content" style="width: 200px">';
	$out.='<div class="inline-edit" id="sfvalid"></div>';
	$out.='<input type="button" class="sfcontrol" id="sfclosevalid" onclick="return hs.close(this)" value="Close" />';
	$out.='</div>';

	if($statusset !=0)
	{
		$out.= '<div class="sfclear"></div><br />';
		$out.= sf_render_topic_statusflag($statusset, $statusflag, 'ts-addpform', 'ts-pform', 'left');
	}

	if($subscribed)
	{
		$out.= '<div class="sfclear"></div>';
		$out.= '<p><small>'.__("You are Subscribed to this Topic", "sforum").'</small></p>';
	} else {
		if($subs && $current_user->forumadmin)
		{
			$out.= '<div class="sfclear"></div>';
			$out.= '<p><small>'.__("This Topic has User Subscriptions", "sforum").'</small></p>';
		}
	}

	if ($watching)
	{
		$out.= '<div class="sfclear"></div>';
		$out.= '<p><small>'.__("You are Watching this Topic", "sforum").'</small></p>';
	} else {
		if($watches && $current_user->forumadmin)
		{
			$out.= '<div class="sfclear"></div>';
			$out.= '<p><small>'.__("This Topic has User Watches", "sforum").'</small></p>';
		}
	}

	$out.= '</td>'."\n";

	$out.= '</tr>'."\n";
	$out.= '</table>'."\n";
	$out.='</div>'."\n";
	$out.='</form>'."\n";
	$out.='</fieldset>'."\n";
	$out.='</div>'."\n";
	$out.= '</div>'."\n";

	return $out;
}

function sf_render_edit_post_form($postid, $postcontent, $forumid, $topicid, $page, $postedit, $linked, $blogpostid=0)
{
	global $sfvars, $current_user, $sfglobals;

	if($linked)
	{
		$sfpostlinking = sf_get_option('sfpostlinking');
		$editchecked = '';
		$links = sf_blog_links_control('read', $blogpostid);
		if($links)
		{
			if($links->syncedit || $sfpostlinking['sfautoupdate']) $editchecked = 'checked="checked"';
		}
	}

	$out = '<a id="postedit"></a></hr >'."\n";
	$out.='<form action="'.sf_build_url($sfvars['forumslug'], $sfvars['topicslug'], $sfvars['page'], $postid).'" method="post" name="editpostform">'."\n";

	$out.= sf_setup_editor(1, str_replace('&', '&amp;', $postcontent));

	$out.='<input type="hidden" name="pid" value="'.$postid.'" />'."\n";
	$out.="<input type='hidden' name='pedit' value='".$postedit."' />"."\n";
	$out.="<input type='hidden' name='blogpid' value='".$blogpostid."' />"."\n";

	$out.= '<table style="width: auto; padding:15px;"><tr>';

	$out.='<td><input type="submit" class="sfcontrol" name="editpost" value="'.__("Save Edited Post", "sforum").'" />'."\n";
	$out.='<input type="submit" class="sfcontrol" name="cancel" value="'.__("Cancel", "sforum").'" /></td>'."\n";
	$out.='<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

	$span = 2;
	if($linked)
	{
		$span = 3;
		$out.= '<td><label for="sfedit">';
		$out.= '&nbsp;&nbsp;'.__("Update blog post with changes", "sforum").'</label>';
		$out.= '&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" '.$editchecked.' name="sfedit" id="sfedit" />';
		$out.= '</td>';
	}

	if ($sfglobals['smileyoptions']['sfsmallow'] && $sfglobals['smileyoptions']['sfsmtype']==1)
	{
		$out.= '</tr><tr><td colspan="'.$span.'"><table>';
		$out.= '<tr><td width="100%" valign="top"><br />';
		$out.= sf_render_smileys();
		$out.= '</td></tr>';
		$out.= '</table></td>';
	}

	$out.= '</tr></table>';
	$out.='</form>'."\n";

	return $out;
}

?>