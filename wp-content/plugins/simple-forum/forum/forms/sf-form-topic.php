<?php
/*
Simple:Press
New Topic Form Rendering
$LastChangedDate: 2010-12-11 15:48:23 -0700 (Sat, 11 Dec 2010) $
$Rev: 5048 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sf_render_add_topic_form($forumid, $forumname, $statusset, $use_tags, $userid)
{
	global $sfvars, $current_user, $sfglobals;

	$out='';
	$msg=sf_validation_messages('topic');

	$editor="TM";
	if($sfglobals['editor']['sfeditor'] != RICHTEXT) $editor="QT";

	$out.= '<div class="sfformblock">';
	$out.='<div id="sfpostform">'."\n";
	$out.='<br />'."\n";
	$out.='<fieldset>'."\n";
	$out.='<legend>'.__("Add New Topic to", "sforum").': <strong>'.$forumname.'</strong></legend>'."\n";

	$sfpostmsg=sf_get_option('sfpostmsg');
	if($sfpostmsg['sfpostmsgtopic'])
	{
		$out.='<div id="sfeditormsg">'.sf_filter_text_display($sfpostmsg['sfpostmsgtext']).'</div>';
	}

	$out.= '<form class="sfpostformcontent" action="'.SFHOMEURL.'index.php?sf_ahah=post" method="post" id="addtopic" name="addtopic" onsubmit="return sfjvalidatePostForm(this, \''.$editor.'\', \''.$msg[0].'\', \''.$msg[1].'\', \''.$msg[2].'\', \''.$msg[3].'\', \''.$msg[4].'\', \''.$msg[5].'\', \''.$msg[6].'\', \''.$msg[7].'\', \''.$msg[8].'\')">'."\n";

	$out.= sfc_create_nonce('forum-userform_addtopic');

	$out.='<input type="hidden" name="forumid" value="'.$forumid.'" />'."\n";
	$out.='<input type="hidden" name="forumslug" value="'.esc_attr($sfvars['forumslug']).'" />'."\n";

	$out.='<table class="sfpostsavetable">';

	if($current_user->guest)
	{
		$out.='<tr><td width="50%"><p><b>'.__("Guest Name (Required)", "sforum").':</b></p>'."\n";
		$out.='<input type="text" tabindex="100" class="sfcontrol sfpostcontrol" name="guestname" value="'.esc_attr($current_user->guestname).'" style="width: 75%;" /></td>'."\n";

		$sfguests = sf_get_option('sfguests');
		if ($sfguests['reqemail'])
		{
			$out.='<td><p><b>'.__("Guest Email (Required)", "sforum").':</b></p>'."\n";
			$out.='<input type="text" tabindex="101" class="sfcontrol sfpostcontrol" name="guestemail" value="'.esc_attr($current_user->guestemail).'" style="width: 75%;" /></td></tr>'."\n";
		}
	}

	if($current_user->sfmoderated)
	{
		$out.='<tr><td colspan="2"><p><small>'.__("NOTE: New Posts are subject to administrator approval before being displayed", "sforum").'</small></p></td></tr>'."\n";
	} elseif($current_user->sfmodonce) {
		$out.='<tr><td colspan="2"><p><small>'.__("NOTE: First Posts are subject to administrator approval before being displayed", "sforum").'</small></p></td></tr>'."\n";
	}

	$out.='<tr><td colspan="2"><p><b>'.__("Topic Name", "sforum").':</b></p>'."\n";
	$out.='<input id="topictitle" type="text" tabindex="102" class="sfcontrol sfpostcontrol" maxlength="180" name="newtopicname" value="" style="width: 75%;" /></td></tr>'."\n";

	$out.='</table>';

	# Grab in-editor message if one
	$ineditor = sf_filter_text_display(sf_get_option('sfeditormsg'));

	$out.='<div class="sfformcontainer">'."\n";

	$out.= sf_setup_editor(103, $ineditor);

	# Open 'save table'
	$out.= '<table class="sfpostsavetable">'."\n";

	# work out what we need to display
	$display = array();
	$display['tags']=false;
	$display['smileys']=false;
	$display['options']=false;

	if($use_tags) $display['tags']=true;
	if($sfglobals['smileyoptions']['sfsmallow'] && $sfglobals['smileyoptions']['sfsmtype']==1) $display['smileys']=true;
	if($current_user->sfsubscriptions ||
	   $current_user->sfwatch ||
	   $current_user->sflock ||
	   $current_user->sfpintopics ||
	   $current_user->forumadmin ||
	   $current_user->moderator ||
	   ($current_user->sflinkuse && current_user_can('publish_posts')))
	   $display['options']=true;

	#  Row 1 - tags and smileys
	$cells1=0;
	if($display['tags'] && $display['smileys'])
	{
		$width="45%";
		$cells1=2;
		if($display['options']) $span=1;
	} elseif($display['tags'] || $display['smileys'])
	{
		$width="95%";
		$cells1=1;
		if($display['options']) $span=2;
	}

	if($cells1 != 0) $out.= '<tr>';
	if($display['tags']) $out.= '<td class="sfpostheading" style="width:'.$width.'" colspan="'.$span.'">'.__("Tags", "sforum").'</td>';
	if($display['smileys']) $out.= '<td class="sfpostheading" style="width:'.$width.'" colspan="'.$span.'">'.__("Smileys", "sforum").'</td>';
	if($cells1 != 0) $out.= '</tr><tr>';

	# Tags
	if($display['tags'])
	{
		$out.='<td valign="top" width="'.$width.'" colspan="'.$span.'">';
		$out.= '<p>'.__("Topic Tags", "sforum").':'."\n";
		if ($sfglobals['display']['topics']['maxtags'] == 0)
		{
			$tagmsg = __("comma separated, no limit", "sforum")."\n";
		} else {
			$tagmsg= __("comma separated,", "sforum").' '.$sfglobals['display']['topics']['maxtags'].' '.__("tag limit - extras ignored", "sforum")."\n";
		}
		$out.= '<input id="tags-input" type="text" tabindex="104" class="sfcontrol sfpostcontrol" maxlength="180" name="topictags" value="" style="width: 75%;" />'."\n";
		$out.= '</p>'."\n";
		$out.= '<small style="font-size: 80%;">'.$tagmsg.'</small>';

		# suggest tags stuff
		$out.= '<div id="suggestedtags">';
		$out.= '<img style="float:right; display:none;" id="sftagsloading" src="'.SFJSCRIPT.'tags/ajax-loader.gif" alt="'.esc_attr(__('Loading...', 'sforum')).'" />';
		$out.= '<p>'.__('Get suggested tags from', 'sforum').':</p>';
		$out.= '<p>';
		$out.= '<a class="local_db" href="#suggestedtags">'.__('Local tags', 'sforum').'</a>';
		$out.= '<a class="yahoo_api" href="#suggestedtags">'.__('Yahoo', 'sforum').'</a>';
		$out.= '<a class="ttn_api" href="#suggestedtags">'.__('Tag The Net', 'sforum').'</a>';
		$out.= '</p>';
		$out.= '<div class="inside">';
		$out.= '<span class="container_clicktags"></span>';
		$out.= '</div>';
		$out.= '</div>';
		$out.= '</td>';
	}

	# Smileys
	if($display['smileys'])
	{
		$out.= '<td valign="top" width="'.$width.'" colspan="'.$span.'">'."\n";
		$out.= sf_render_smileys();
		$out.= '</td>'."\n";
	}
	if($cells1 != 0) $out.= '</tr>';

	# Row 2 - Options and Save
	$span=1;
	$width="50%";

	if($display['options'] == false)
	{
		if($cells1 == 0 || $cells1 == 1)
		{
			$span=1;
			$width="100%";
		} elseif($cells1 == 2)
		{
			$span=2;
			$width="100%";
		}
	}

	$out.= '<tr>';
	if($display['options']) $out.= '<td class="sfpostheading" width="'.$width.'" colspan="'.$span.'">'.__("Options", "sforum").'</td>';
	$out.= '<td class="sfpostheading" width="'.$width.'" colspan="'.$span.'">'.__("Post New Topic", "sforum").'</td>';
	$out.= '</tr><tr>';

	# Options
	if($display['options'])
	{
		$out.= '<td width="'.$width.'" colspan="'.$span.'" valign="top">'."\n";

		$out.='<table class="sfcheckoptions" cellspacing="4" cellpadding="4">';
		if($current_user->sfsubscriptions)
		{
			if ($sfglobals['member']['user_options']['autosubpost']) $checked = ' checked="checked"'; else $checked = '';
			$out.='<tr><td width="20"><img src="'.SFRESOURCES.'usersubscribed.png" alt="" title="'.esc_attr(__("Subscribe to this Topic", "sforum")).'" /></td><td><input type="checkbox"'.$checked.' name="topicsub" id="sftopicsub" tabindex="108" /><label for="sftopicsub">&nbsp;&nbsp;'.sf_render_icons("Subscribe to this Topic").'</label></td></tr>'."\n";
		}
		if($current_user->sfwatch)
		{
			$out.='<tr><td width="20"><img src="'.SFRESOURCES.'watchicon.png" alt="" title="'.esc_attr(__("Watch this Topic", "sforum")).'" /></td><td><input type="checkbox" name="topicwatch" id="sftopicwatch" tabindex="109" /><label for="sftopicwatch">&nbsp;&nbsp;'.sf_render_icons("Watch this Topic").'</label></td></tr>'."\n";
		}
		if($current_user->sflock)
		{
			$out.='<tr><td width="20"><img src="'.SFRESOURCES.'locked.png" alt="" title="'.esc_attr(__("Lock this Topic", "sforum")).'" /></td><td><input type="checkbox" name="topiclock" id="sftopiclock" tabindex="110" /><label for="sftopiclock">&nbsp;&nbsp;'.sf_render_icons("Lock this Topic").'</label></td></tr>'."\n";
		}
		if($current_user->sfpintopics)
		{
			$out.='<tr><td width="20"><img src="'.SFRESOURCES.'pin.png" alt="" title="'.esc_attr(__("Pin this Topic", "sforum")).'" /></td><td><input type="checkbox" name="topicpin" id="sftopicpin" tabindex="111"  /><label for="sftopicpin">&nbsp;&nbsp;'.sf_render_icons("Pin this Topic").'</label></td></tr>'."\n";
		}
		if($current_user->forumadmin)
		{
			$out.='<tr><td width="20"><img src="'.SFRESOURCES.'clock.png" alt="" title="'.esc_attr(__("Edit Topic Timestamp", "sforum")).'" /></td><td><input type="checkbox" tabindex="112" id="sfeditTimestamp" name="editTimestamp" onchange="sfjtoggleLayer(\'sftimestamp\');"/><label for="sfeditTimestamp">&nbsp;&nbsp;'.sf_render_icons("Edit Timestamp").'</label></td></tr>'."\n";
		}
		if(($current_user->sflinkuse) && (current_user_can('publish_posts')))
		{
			$gif= SFADMINIMAGES.'working.gif';
            $site = SFHOMEURL."index.php?sf_ahah=categories&forum=".$forumid;
			$out.='<tr><td width="20"><img src="'.SFRESOURCES.'createlink.png" alt="" title="'.esc_attr(__("Link Topic to Blog", "sforum")).'" /></td><td><input type="checkbox" name="bloglink" id="sfbloglink" tabindex="113" onchange="sfjgetCategories(\''.$gif.'\', \''.$site.'\', this.checked)" /><label for="sfbloglink">&nbsp;&nbsp;'.sf_render_icons("Create Linked Post").'</label></td></tr>'."\n";
		}
		$out.='</table>';

		$out.='<div id="sfcats"></div>';

		if($current_user->forumadmin)
		{
			global $wp_locale, $month;
			$time_adj = time() + (get_option( 'gmt_offset' ) * 3600 );
			$dd = gmdate( 'd', $time_adj );
			$mm = gmdate( 'm', $time_adj );
			$yy = gmdate( 'Y', $time_adj );
			$hh = gmdate( 'H', $time_adj );
			$mn = gmdate( 'i', $time_adj );
			$ss = gmdate( 's', $time_adj );

			$out.='<div id="sftimestamp">'."\n";
			$out.='<select tabindex="114" name="tsMonth" onchange="editTimestamp.checked=true">'."\n";
			for ( $i = 1; $i < 13; $i = $i +1 ){
				$out.= "\t\t\t<option value=\"$i\"";
				if ( $i == $mm ) $out.= ' selected="selected"';
				if(class_exists('WP_Locale'))
				{
					$out.= '>' . $wp_locale->get_month( $i ) . "</option>\n";
				} else {
					$out.= '>' . $month[$i] . "</option>\n";
				}
			}
			$out.='</select>'."\n";

			$out.='<input class="sfcontrolTS" tabindex="115" type="text" id="tsDay" name="tsDay" value="'.$dd.'" size="2" maxlength="2"/>'."\n";
			$out.='<input class="sfcontrolTS" tabindex="116" type="text" id="tsYear" name="tsYear" value="'.$yy.'" size="4" maxlength="5"/>@'."\n";
			$out.='<input class="sfcontrolTS" tabindex="117" type="text" id="tsHour" name="tsHour" value="'.$hh.'" size="2" maxlength="2"/> :'."\n";
			$out.='<input class="sfcontrolTS" tabindex="118" type="text" id="tsMinute" name="tsMinute" value="'.$mn.'" size="2" maxlength="2"/>'."\n";
			$out.='<input class="sfcontrolTS" tabindex="119" type="hidden" id="tsSecond" name="tsSecond" value="'.$ss.'" size="2" maxlength="2"/>'."\n";
			$out.='</div>'."\n";
		}

		$out.= '</td>'."\n";
	}

	# Save Topic
	$out.= '<td width="'.$width.'" colspan="'.$span.'" valign="top">'."\n";

	# Start Spam Measures
	if($current_user->sfspam ? $usemath = false : $usemath = true);
	$enabled=' ';
	if($usemath)
	{
		$enabled = ' disabled="disabled" ';
		$out.='<div id="sfhide">'."\n";
		$out.='<p>'.__("Guest URL (Required)", "sforum").'<br />'."\n";
		$out.='<input type="text" class="yscontrol" size="30" name="url" value="" /></p>'."\n";
		$out.='</div>'."\n";

		$spammath = sf_math_spam_build();

		$out.='<p><strong>'.__("Math Required!", "sforum").'</strong><br />'."\n";
		$out.=sprintf(__("What is the sum of: <strong> %s + %s </strong>", "sforum"), '<br />'.$spammath[0], $spammath[1]).'&nbsp;&nbsp;&nbsp;'."\n";
		$out.='<input type="text" tabindex="105" class="sfcontrol" size="4" name="sfvalue1" value="" onkeyup="sfjsetTopicButton(this, '.$spammath[0].', '.$spammath[1].', \''.esc_js(__("Post New Topic", "sforum")).'\', \''.esc_js(__("Do Math To Save", "sforum")).'\')" /></p>'."\n";
		$out.='<input type="hidden" name="sfvalue2" value="'.$spammath[2].'" />'."\n";
	}
	# End Spam Measures

	$buttontext = esc_attr(__("Post New Topic", "sforum"));
	if($usemath) $buttontext = esc_attr(__("Do Math To Save", "sforum"));

	$out.='<input type="submit"'.$enabled.'tabindex="106" class="sfcontrol" name="newtopic" id="sfsave" value="'.$buttontext.'" />'."\n";
	$out.='<input type="button" tabindex="107" class="sfcontrol" id="sfcancel" name="cancel" value="'.esc_attr(__("Cancel", "sforum")).'" onclick="sfjCancelEditor(\''.$editor.'\');" />'."\n";

	$out.='<div class="highslide-html-content" id="my-content" style="width: 200px">';
	$out.='<div class="inline-edit" id="sfvalid"></div>';
	$out.='<input type="button" class="sfcontrol" id="sfclosevalid" onclick="return hs.close(this)" value="'.esc_attr(__("Close", "sforum")).'" />';
	$out.='</div>';

	if($statusset !=0)
	{
		$out.= '<div class="sfclear"></div><br />';
		$out.= '<input type="hidden" name="statusflag" value="1" />'."\n";
		$out.= sf_render_topic_statusflag($statusset, 1, 'ts-addtform', 'ts-tform', 'left');
	}

	$out.= '</td>'."\n";
	$out.= '</tr></table>';

	# Post Options
	$out.='</div>'."\n";
	$out.='</form>'."\n";
	$out.='</fieldset>'."\n";
	$out.='</div>'."\n";
	$out.= '</div>';

	return $out;
}

function sf_render_edit_topic_title_form($topicid, $topicname, $forumid)
{
	global $sfvars;

	$topicslug=sf_get_topic_slug($topicid);

	$out = '<a id="topicedit"></a>'."\n";
	$out.='<form action="'.sf_build_url($sfvars['forumslug'], '', $sfvars['page'], 0).'" method="post" name="edittopicform">'."\n";
	$out.='<input type="hidden" name="tid" value="'.$topicid.'" />'."\n";
	$out.='<td>';

	$out.= __('Topic Title', 'sforum').':<br />';
	$out.='<textarea class="sftextarea" name="topicname" rows="2">'.esc_attr($topicname).'</textarea>'."\n";

	$out.='<br />'.__('Topic Slug', 'sforum').':<br />';
	$out.='<textarea class="sftextarea" name="topicslug" rows="2">'.$topicslug.'</textarea></td>'."\n";


	$out.='<td><input type="submit" class="sfcontrol" name="edittopic" value="'.esc_attr(__("Save", "sforum")).'" /></td>'."\n";
	$out.='<td><input type="submit" class="sfcontrol" name="cancel" value="'.esc_attr(__("Cancel", "sforum")).'" /></td>'."\n";
	$out.= '</form>'."\n";

	return $out;
}

?>