<?php
/*
Simple:Press
Form Rendering
$LastChangedDate: 2011-03-06 17:03:08 -0700 (Sun, 06 Mar 2011) $
$Rev: 5639 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

function sf_add_topic($forumid, $forumname, $statusset, $use_tags, $userid)
{
	include_once(SF_PLUGIN_DIR.'/forum/forms/sf-form-topic.php');
	return sf_render_add_topic_form($forumid, $forumname, $statusset, $use_tags, $userid);
}

function sf_edit_topic_title($topicid, $topicname, $forumid)
{
	include_once(SF_PLUGIN_DIR.'/forum/forms/sf-form-topic.php');
	return sf_render_edit_topic_title_form($topicid, $topicname, $forumid);
}

function sf_add_post($forumid, $topicid, $topicname, $statusset, $statusflag, $userid, $subs, $watches)
{
	include_once(SF_PLUGIN_DIR.'/forum/forms/sf-form-post.php');
	return sf_render_add_post_form($forumid, $topicid, $topicname, $statusset, $statusflag, $userid, $subs, $watches);
}

function sf_edit_post($postid, $postcontent, $forumid, $topicid, $page, $postedit, $linked, $blogpostid)
{
	include_once(SF_PLUGIN_DIR.'/forum/forms/sf-form-post.php');
	return sf_render_edit_post_form($postid, $postcontent, $forumid, $topicid, $page, $postedit, $linked, $blogpostid);
}

function sf_searchbox($pageview, $statusset=0)
{
	include_once(SF_PLUGIN_DIR.'/forum/forms/sf-form-search.php');
	return sf_render_searchbox_form($pageview, $statusset);
}

function sf_add_pm()
{
	include_once(SF_PLUGIN_DIR.'/messaging/forms/sf-form-pm.php');
	return sf_render_add_pm_form();
}

function sf_report_post_form()
{
	include_once(SF_PLUGIN_DIR.'/forum/forms/sf-form-report.php');
	return sf_render_report_post_form();
}

function sf_inline_login_form()
{
	include_once(SF_PLUGIN_DIR.'/forum/forms/sf-form-login.php');
	return sf_render_inline_login_form();
}

function sf_policy_form()
{
	include_once(SF_PLUGIN_DIR.'/forum/forms/sf-form-policy.php');
	return sf_render_policy_form();
}

function sf_forum_unavailable()
{
	global $current_user;

	$out = '';
	$out.= '<div id="sforum"><div style="border: 1px solid #666666; padding: 10px; font-weight: bold;">'."\n";
	$out.= '<img class="sfalignleft" src="'.SFRESOURCES.'information.png" alt="" />'."\n";
	$out.= '<p>&nbsp;&nbsp;'.__("The forum is temporarily unavailable while being upgraded to a new version", "sforum").'</p>';
	if (sf_is_forum_admin($current_user->ID))
	{
		sf_setup_admin_constants();
		$out.= '&nbsp;&nbsp;<a style="text-decoration: underline;" href="'.SFADMINFORUM.'">'.__("Perform Upgrade", "sforum").'</a>';
	}
	$out.= '</div><br /><br /></div>';
	return $out;
}

function sf_validation_messages($type)
{
	global $current_user, $sfglobals;

	# 0 = base error message
	# 1 = guest name
	# 2 = guest email
	# 3 = topic title
	# 4 = spam math
	# 5 = post
	# 6 = embedded formatting
	# 7 = saving text

	$msg = array(0 => '', 1 => '', 2 => '', 3 => '', 4 => '', 5 => '', 6 => '', 7 => '');
	$msg[0] = esc_js(__("Problem! Please correct and re-save", "sforum"));

	if($current_user->guest)
	{
		$msg[1] = esc_js(__("No Guest Name Entered", "sforum"));
		$msg[2] = esc_js(__("No Guest EMail Entered", "sforum"));
	} else {
		$msg[1] = '';
		$msg[2] = '';
	}

	if($type == 'topic')
	{
		$msg[3] = esc_js(__("No Topic Name Entered", "sforum"));
	} else {
		$msg[3] = '';
	}

	if(!$current_user->sfspam)
	{
		$msg[4] = esc_js(__("Spam Math Unanswered", "sforum"));
	} else {
		$msg[4] = '';
	}

	$msg[5] = esc_js(__("No Post Content Entered", "sforum"));

	if($sfglobals['editor']['sfrejectformat'] && $sfglobals['editor']['sfeditor'] == RICHTEXT)
	{
		if(in_array('pastetext', $sfglobals['toolbar']['tbar_buttons_add']) || in_array('pasteword', $sfglobals['toolbar']['tbar_buttons_add']))
		{
			$msg[6] = esc_js(__("This text contains embedded formatting and was probably pasted in. Please completely remove text and use the approriate paste toolbar button (text or MS Word) to paste into or rewrite as plain text", "sforum"));
		}
	}

	$msg[7] = esc_js(__("Saving Post", "sforum"));

	$msg[8] = esc_js(__("Please Wait", "sforum"));

	return $msg;
}

function sf_setup_editor($tab, $content='')
{
	global $sfglobals;

	$out = '';
	if($sfglobals['editor']['sfeditor'] == RICHTEXT || $sfglobals['editor']['sfeditor'] == PLAIN)
	{
		# rich text/tinymce - or - plain textarea
		$out.='<textarea  tabindex="'.$tab.'" class="sftextarea" name="postitem" id="postitem" cols="60" rows="12">'.$content.'</textarea>'."\n";
		return $out;
	}
	if($sfglobals['editor']['sfeditor'] == HTML)
	{
		# html quicktags
		$image = "html/htmlEditor.gif";
		$alttext = __("HTML Editor", "sforum");
	} else {
		# bbcode quicktags
		$image = "bbcode/bbcodeEditor.gif";
		$alttext = __("bbCode Editor", "sforum");
	}
	$out.='<div class="quicktags">'."\n";
	$out.='<img class="sfalignright" src="'.SFEDSTYLE.$image.'" alt="'.$alttext.'" />';
	$out.='<script type="text/javascript">edToolbar();</script><textarea tabindex="'.$tab.'" class="sftextarea" name="postitem" id="postitem" rows="12" cols="60">'.$content.'</textarea><script type="text/javascript">var edCanvas = document.getElementById("postitem");</script>'."\n";
	$out.='</div>'."\n";
	return $out;
}

function sf_render_smileys()
{
	global $sfglobals;

	$out='';

	if($sfglobals['smileyoptions']['sfsmallow'] && $sfglobals['smileyoptions']['sfsmtype']==1)
	{
		# load smiles from sfmeta
		if($sfglobals['smileys'])
		{
			foreach ($sfglobals['smileys'] as $sname => $sinfo)
			{
				$out.= '<img class="sfsmiley" src="'.esc_url(SFSMILEYS.$sinfo[0]).'" title="'.esc_attr($sname).'" alt="'.esc_attr($sname).'" ';
				$out.= 'onclick="sfjLoadSmiley(\''.esc_js($sinfo[0]).'\', \''.esc_js($sname).'\', \''.SFSMILEYS.'\', \''.esc_js($sinfo[1]).'\', \''.$sfglobals['editor']['sfeditor'].'\');" />'."\n";
			}
		}
	}
	return $out;
}

?>