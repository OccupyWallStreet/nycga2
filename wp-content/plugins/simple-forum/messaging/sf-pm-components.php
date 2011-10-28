<?php
/*
Simple:Press
PM Rendering Routines
$LastChangedDate: 2011-04-26 19:22:16 -0700 (Tue, 26 Apr 2011) $
$Rev: 5983 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# ------------------------------------------------------------------
# sf_render_pm_table()
#
# Main rendering loop of PM table
#	$view:			Set to 'inbox' or 'sentbox'
#	$threads		The PM data array
#	$messagecount	Total number of all messages in this view
#	$cansendpm		True/False - can current user send PMs
# ------------------------------------------------------------------
function sf_render_pm_table($view, $threads, $messagecount, $cansendpm)
{
	$out = '';

	$threadindex = 0;

	if($threads)
	{
		foreach ($threads as $thread)
		{
			$out.= sf_render_pm_thread($threadindex, $view, $thread, $cansendpm);
			$threadindex = $threadindex + 2;
		}
	}

	return $out;
}

# ------------------------------------------------------------------
# sf_render_pm_preview()
#
# Main rendering loop of PM table
#	$view:			Set to 'inbox' or 'sentbox'
#	$threads		The PM data array
#	$messagecount	Total number of all messages in this view
#	$cansendpm		True/False - can current user send PMs
# ------------------------------------------------------------------
function sf_render_pm_preview($view, $threads, $messagecount, $cansendpm)
{
	global $sfscript;
	$out = '';

	$out.= '<div class="sfheading" id="sfpmbuttons"></div>';
	$out.= '<div class="sfpmcontent" id="sfpminfo"></div>';
	$out.= '<div class="sfpmcontent" id="sfpmmsg"></div>';

    if ($threads)
    {
    	$gif= SFADMINIMAGES.'working.gif';
        $site = SFHOMEURL."index.php?sf_ahah=pm-manage";
    	$msgid = $threads[0]['messages'][0]['message_id'];
        $status = $threads[0]['messages'][0]['message_status'];
    	$slug = $threads[0]['slug'];

		# inline script to populate first message - held in global
    	$sfscript = '
            <script type="text/javascript">
            	jQuery(document).ready(function() {
	                sfjgetPMText(\''.$gif.'\', \''.$site.'\', \''.$msgid.'\', \''.$view.'\', \''.$status.'\');
	                var thread = document.getElementById("pm-'.$slug.'");
	               	jQuery("#messagediv-'.$slug.'").toggle("slow");
	                thread.className = "sfpmshow";
	            });
            </script>';

		add_action( 'wp_footer', 'sfjs_display_start_PM' );
    }

	return $out;
}

# # inline script to populate first message - held in global
function sfjs_display_start_PM() {
	global $sfscript;
	echo $sfscript;
}

# ------------------------------------------------------------------
# sf_render_pm_outer_header_row()
#
# Man headings in the 'thread' (outer) table
#	$view:			Set to 'inbox' or 'sentbox'
#	$messagecount	Total number of all messages in this view
# ------------------------------------------------------------------
function sf_render_pm_outer_header_row($view, $messagecount)
{
	global $current_user, $sfglobals;

	$delete='';

	# dont allow deletion if forum locked down
	if ($messagecount > 0 && !$sfglobals['lockdown'])
	{
		$pmitem = sf_localise_boxname($view);
		$msg = sprintf(esc_js(__("Are you sure you want to empty your %s?", "sforum")), $pmitem);
        $site = SFHOMEURL."index.php?sf_ahah=pm-manage&pmdelall=".$view."&amp;owner=".$current_user->ID;
		$delete = '<input type="button" class="sfxcontrol" name="deleteall" id="deleteall" tabindex="0" value="'.__("Delete All", "sforum").'" onclick="javascript: if(confirm(\''.$msg.'\')) {sfjdeleteMassPM(\''.$site.'\');}" />';
	}

	if($view == 'inbox')
	{
		$out = '<tr><th width="125">'.__("From", "sforum").'</th><th align="left">'.__("Title", "sforum").'</th><th align="center" width="95">'.__("Date", "sforum").'</th><th width="40">'.__("Thread", "sforum").'</th><th width="125">'.$delete.'</th></tr>'."\n";
	} else {
		$out = '<tr><th width="125">'.__("To", "sforum").'</th><th align="left">'.__("Title", "sforum").'</th><th align="center" width="95">'.__("Date", "sforum").'</th><th width="40">'.__("Thread", "sforum").'</th><th width="125">'.$delete.'</th></tr>'."\n";
	}
	return $out;
}

# ------------------------------------------------------------------
# sf_render_pm_thread()
#
# Main rendering loop of individual PM thread
#	$threadindex	The table row index
#	$view			Set to 'inbox' or 'sentbox'
#	$thread			The PM thread data array
#	$cansendpm		True/False - can current user send PMs
# ------------------------------------------------------------------
function sf_render_pm_thread($threadindex, $view, $thread, $cansendpm)
{
	global $sfglobals;

	$out = '';
	$alt = '';

	# Is there an unread (inbox view) and get last message date and sender (inbox) or recopient (sentbox)
	$read_status = 1;
	foreach($thread['messages'] as $message)
	{
		if($message['message_status'] == '0') $read_status = 0;
		$sent_date = $message['sent_date'];
		if($view == 'inbox')
		{
			$sender_id = $message['from_id'];
		} else {
			$sender_id = $message['to_id'];
			$read_status = 1;
		}
		$sender_name = sf_filter_name_display($message['display_name']);
	}

	if($read_status == 0)
	{
		$out.= '<tr class="sfpmunread" id="pm-'.$thread['slug'].'">'."\n";
	} else {
		$out.= '<tr class="sfpmread" id="pm-'.$thread['slug'].'">'."\n";
	}

	$out.= '<td width="175" class="pmuserinfo '.$alt.'">';
	$poster = __("user", "sforum");
	if ($message['admin']) $poster = __("admin", "sforum");
	$out.= sf_render_avatar($poster, $sender_id, sf_filter_email_display($message['user_email']), '', false, 25);
	$out.= '<p>';
	$out.= sf_render_pm_sender($sender_id, $sender_name, $read_status, $cansendpm)."\n";
	$out.= '<br /><small>'.$poster.'</small>';
    $out.= '</p>';

	$out.= "</td>\n";

	$out.= '<td class="'.$alt.'">'.sf_render_pm_thread_title($threadindex, sf_filter_title_display($thread['title']), $read_status, $thread['slug']).'</td>'."\n";
	$out.= '<td width="85" class="'.$alt.'" align="center">'.sf_render_sent_date($sent_date).'</td>'."\n";
	$out.= '<td width="55" class="'.$alt.'" align="center" id="pm-'.$thread['slug'].'count'.'">'.count($thread['messages']).'</td>'."\n";
	$out.= '<td width="50" class="'.$alt.'">'.sf_render_pm_delete_thread($threadindex, $view, $thread['slug']).'</td>'."\n";
	$out.= '</tr>'."\n";

	if ($alt == '') $alt = 'sfalt'; else $alt = '';

	$out.= sf_render_pm_messages($view, $thread, $threadindex, $cansendpm);

	return $out;
}

# ------------------------------------------------------------------
# sf_render_pm_messages()
#
# Main rendering loop of individual PM message
#	$view			Set to 'inbox' or 'sentbox'
#	$thread			The PM thread data array
#	$cansendpm		True/False - can current user send PMs
# ------------------------------------------------------------------
function sf_render_pm_messages($view, $thread, $threadindex, $cansendpm)
{
	global $current_user, $wpdb;

	$out = '';

	$out.='<tr class="sfadminrow" id="messagerow-'.$thread['slug'].'">'."\n";
	$out.='<td class="sfadminrow" colspan="5">'."\n";
	$out.='<div id="messagediv-'.$thread['slug'].'" class="inline_edit">'."\n";
	$out.= '<table class="sfpmtable" id="sfmessagetable-'.$thread['slug'].'" cellspacing="0" border="1">'."\n";

	$messageindex = 0;

	foreach($thread['messages'] as $message)
	{
		if($message['message_status'] == '0')
		{
			$out.= '<tr class="sfpmunread" id="message-'.$message['message_id'].'">';
		} else {
			$out.= '<tr class="sfpmread" id="message-'.$message['message_id'].'">';
		}

		$out.= '<td width="18" valign="middle">'.sf_render_pm_status_icon($message['message_status'], $message['is_reply']).'</td>';
		if($view == 'inbox')
		{
			$out.= '<td width="172" valign="middle">'.sf_render_pm_sender($message['from_id'], sf_filter_name_display($message['display_name']), $message['message_status'], $cansendpm).'</td>'."\n";
		} else {
			$out.= '<td width="172" valign="middle">'.sf_render_pm_sender($message['to_id'], sf_filter_name_display($message['display_name']), $message['message_status'], $cansendpm).'</td>'."\n";
		}

		$out.= '<td valign="middle">'.sf_render_pm_message_title(sf_filter_title_display($thread['title']), $message['is_reply'], $message['message_status'], $message['message_id'], $thread['slug'], $view).'</td>'."\n";
		$out.= '<td width="90" valign="middle" align="center">'.sf_render_sent_date($message['sent_date']).'</td>'."\n";
		if($view == 'inbox')
		{
			$out.= '<td width="50" ></td>';
		} else {
			$out.= '<td width="50" ></td>';
		}
		$out.= '<td width="50" valign="middle">'.sf_render_pm_delete_message($messageindex, $threadindex, $view, $message['message_id'], $thread['slug']).'</td>';
		$out.= '</tr>';

		$messageindex++;
	}

	$out.= '</table>';
	$out.= '</div>';
	$out.= '</td>';
	$out.= '</tr>';

	return $out;
}

# ------------------------------------------------------------------
# sf_render_pm_inner_header_row()
#
# Sub-heading of inner message table
#	$view			Set to 'inbox' or 'sentbox'
# ------------------------------------------------------------------
function sf_render_pm_inner_header_row($view)
{
	if($view == 'inbox')
	{
		$out = '<tr><th width="18"></th><th align="left" width="160"></th><th></th><th width="85"></th><th width="50"></th><th width="50"></th></tr>';
	} else {
		$out = '<tr><th width="18"></th><th align="left" width="'.$size.'">'.__("To", "sforum").'</th><th>'.__("Title", "sforum").'</th><th width="100">'.__("Date Sent", "sforum").'</th><th width="50"></th><th width="50"></th></tr>';
	}
	return $out;
}

# ------------------------------------------------------------------
# sf_render_pm_sender()
#
# Display sender data and link to 'compose new'
#	$sender_id:			ID of the user who sent the message
#	$sender_name		Display ame of the user who sent the message
#	$read_status		0 = read - 1 = unread
#	$cansendpm			True/False - can current user send PMs
# ------------------------------------------------------------------
function sf_render_pm_sender($sender_id, $sender_name, $read_status, $cansendpm)
{
    global $sfglobals;

	$out = '';
	$reply = 0;
    $editor = $sfglobals['editor']['sfeditor'];
	$title = '';
	$class=' sfread';
	if($read_status == 0) $class='';
	if($cansendpm)
	{
		$out.= '<a class="sfpmentry'.$class.'" onclick="sfjsendPMTo(\''.$sender_id.'\', \''.addslashes($sender_name).'\', \''.$title.'\', \''.$reply.'\', \'\', \''.$editor.'\');" title="'.__("Send Message To Member", "sforum").'">'.stripslashes($sender_name).'</a>';
	} else {
		$out.= $sender_name;
	}
	return $out;
}

# ------------------------------------------------------------------
# sf_render_pm_thread_title()
#
# Dislay message title with link to expand thread
#	$threadindex	The table row index
#	$title:			Ttle of the message thread
#	$read_status	0 = read - 1 = unread
#	$slug			ID of messages row to open oin click
# ------------------------------------------------------------------
function sf_render_pm_thread_title($threadindex, $title, $read_status, $slug)
{
	$out = '';
	$class=' sfread';
	if($read_status == 0) $class='';
    $out.= '<table class="sf-pmtitle"><tr>';
    $out.= '<td class="sf-pmtitle">';
	$out.= '<a class="sfpmentry'.$class.'" onclick="sfjtoggleThread(this, \'messagediv-'.$slug.'\', \''.$threadindex.'\');" title="'.__("Open/Close Thread", "sforum").'">';
    $out.= '<img id="pmopener'.$threadindex.'" src="'.SFRESOURCES.'pm-open-thread.png" alt="" title="'.__("Open/Close Thread", "sforum").'"/>';
    $out.= '</a>'."\n";
    $out.= '</td>';
    $out.= '<td class="sf-pmtitle">';
	$out.= '<a class="sfpmentry'.$class.'" onclick="sfjtoggleThread(this, \'messagediv-'.$slug.'\', \''.$threadindex.'\');" title="'.__("Open/Close Thread", "sforum").'">';
    $out.= $title;
    $out.= '</a>'."\n";
    $out.= '</td>';
    $out.= '</tr></table>';
	return $out;
}

# ------------------------------------------------------------------
# sf_render_pm_message_title()
#
# Dislay message title with link to expand thread
#	$title:			Ttle of the message thread
#	$read_status	0 = read - 1 = unread
#	$msgid			ID of the message
#	$slug			ID of messages row to open on click
#	$view			Set to 'inbox' or 'sentbox'
# ------------------------------------------------------------------
function sf_render_pm_message_title($title, $is_reply, $read_status, $msgid, $slug, $view)
{
	$out = '';
	$gif= SFADMINIMAGES.'working.gif';
    $site = SFHOMEURL."index.php?sf_ahah=pm-manage";
	$class=' sfread';
	if($read_status == 0) $class='';
	if($is_reply ? $re="Re: " : $re="");

	$out.= '<a class="sfpmentry'.$class.'" onclick="sfjgetPMText(\''.$gif.'\', \''.$site.'\', \''.$msgid.'\', \''.$view.'\', \''.$read_status.'\')" title="'.__("View Message", "sforum").'">'.$re.$title.'</a>';
	return $out;
}

# ------------------------------------------------------------------
# sf_render_sent_date()
#
# Dislay message sent date
#	$sent_date		Date message was sent
# ------------------------------------------------------------------
function sf_render_sent_date($sent_date)
{
	$out = '<small>'.sf_date('d', $sent_date).'<br />'.sf_date('t', $sent_date).'</small>';
	return $out;
}

# ------------------------------------------------------------------
# sf_render_pm_status_icon()
#
# Delete thread button
#	$status		unread=0 or read-1
#	$is_reply	true of a reply message
# ------------------------------------------------------------------
function sf_render_pm_status_icon($status, $is_reply)
{
	if($is_reply) $status='2';

	switch($status)
	{
		case '0':
			$icon = 'pmunread.png';
			$title= __("Unread Message", "sforum");
			break;
		case '1':
			$icon = 'pmread.png';
			$title= __("Read Message", "sforum");
			break;
		case '2':
			$icon = 'pmreplied.png';
			$title= __("Replied To Message", "sforum");
			break;
	}
	$out = '<img src="'.SFRESOURCES.$icon.'" alt="" title="'.$title.'" />';
	return $out;
}

# ------------------------------------------------------------------
# sf_render_pm_reply_message()
#
# Remders the 'Reply button on inbox view
#	$msgid				ID of the message
#	$sender_id:			ID of the user who sent the message
#	$sender_name		Display ame of the user who sent the message
#	$title				Message title
#	$read_status		0 = read - 1 = unread
#	$cansendpm			True/False - can current user send PMs
# ------------------------------------------------------------------
function sf_render_pm_reply_message($msgid, $sender_id, $sender_name, $title, $read_status, $cansendpm, $idlist, $namelist, $slug)
{
	global $sfglobals;

	$editor = $sfglobals['editor']['sfeditor'];

	$out = '';
	$reply = 1;
	if($cansendpm)
	{
		$title = esc_sql($title);
		$out.= '<input type="button" id="pmreply-'.$msgid.'" class="sfxcontrol" name="pmreply-'.$msgid.'" value="'.__("Reply", "sforum").'" onclick="sfjsendPMTo(\''.$sender_id.'\', \''.esc_js($sender_name).'\', \''.esc_js($title).'\', \''.$reply.'\', \''.$slug.'\', \''.$editor.'\');" />';
		if(!empty($idlist))
		{
			$idlist = $idlist.','.$sender_id;
			$namelist = $namelist.','.$sender_name;
			$out.= '<input type="button" id="pmreplyall-'.$msgid.'" class="sfxcontrol" name="pmreplyall-'.$msgid.'" value="'.__("Reply All", "sforum").'" onclick="sfjsendPMTo(\''.$idlist.'\', \''.esc_js($namelist).'\', \''.esc_js($title).'\', \''.$reply.'\', \''.$slug.'\', \''.$editor.'\');" />';
		}
	}

	return $out;
}

# ------------------------------------------------------------------
# sf_render_pm_quote_message()
#
# Remders the 'Reply button on inbox vien
#	$msgid				ID of message being quoted
#	$sender_id:			ID of the user who sent the message
#	$sender_name		Display ame of the user who sent the message
#	$title				Title of the message
#	$read_status		0 = read - 1 = unread
#	$cansendpm			True/False - can current user send PMs
# ------------------------------------------------------------------
function sf_render_pm_quote_message($msgid, $sender_id, $sender_name, $title, $read_status, $cansendpm, $idlist, $namelist, $slug)
{
	global $sfglobals;

	$editor = $sfglobals['editor']['sfeditor'];

	$out = '';
	$reply = 1;
	if($cansendpm)
	{
		$title = esc_sql($title);
		$intro = '&lt;p&gt;'.$sender_name.' '.__("said:", "sforum").'&lt;/p&gt;';
		$out.= '<input type="button" id="pmquote-'.$msgid.'" class="sfxcontrol" name="pmquote-'.$msgid.'" value="'.__("Quote", "sforum").'" onclick="sfjquotePM(\''.$sender_id.'\', \'sfpm'.$msgid.'\', \''.esc_js($intro).'\', '.$editor.', \''.esc_js($sender_name).'\', \''.esc_js($title).'\', \''.$reply.'\', \''.$slug.'\');" />';
		if(!empty($idlist))
		{
			$idlist = $idlist.','.$sender_id;
			$namelist = $namelist.','.$sender_name;
			$out.= '<input type="button" id="pmquoteall-'.$msgid.'" class="sfxcontrol" name="pmquoteall-'.$msgid.'" value="'.__("Quote All", "sforum").'" onclick="sfjquotePM(\''.$idlist.'\', \'sfpm'.$msgid.'\', \''.esc_js($intro).'\', '.$editor.', \''.esc_js($namelist).'\', \''.esc_js($title).'\', \''.$reply.'\', \''.$slug.'\');" />';
		}
	}

	return $out;
}

# ------------------------------------------------------------------
# sf_render_pm_forward_message()
#
# Renders the Forward button on inbox vien
#	$msgid				ID of message being quoted
#	$title				Title of the message
#	$read_status		0 = read - 1 = unread
#	$cansendpm			True/False - can current user send PMs
# ------------------------------------------------------------------
function sf_render_pm_forward_message($msgid, $sender_name, $title, $read_status, $cansendpm, $slug)
{
	global $sfglobals;

	$editor = $sfglobals['editor']['sfeditor'];

	$out = '';
	$reply = 1;
	if($cansendpm)
	{
		$title = esc_sql($title);
		$intro = '&lt;p&gt;'.$sender_name.' '.__("said:", "sforum").'&lt;/p&gt;';
		$out.= '<input type="button" id="pmforward-'.$msgid.'" class="sfxcontrol" name="pmforward-'.$msgid.'" value="'.__("Forward", "sforum").'" onclick="sfjquotePM(\'\', \'sfpm'.$msgid.'\', \''.esc_js($intro).'\', '.$editor.', \'\', \''.esc_js($title).'\', \''.$reply.'\', \''.$slug.'\');" />';
	}

	return $out;
}

# ------------------------------------------------------------------
# sf_render_pm_mark_unread()
#
# Renders the Reply button on inbox vien
#	$msgid				ID of message being quoted
#	$sender_id:			ID of the user who sent the message
# ------------------------------------------------------------------
function sf_render_pm_mark_unread($msgid, $sender_id)
{
	$out = '';
    $out = '<div id="sfpmmsg-'.$msgid.'" style="float:right"></div>';
    $msgurl = SFHOMEURL."index.php?sf_ahah=pm-manage&amp;pmmarkunread=1&amp;msgid=".$msgid;
    $text = esc_js(__('Marked Unread', 'sforum'));
	$out.= '<input type="button" id="pmmark-'.$msgid.'" class="sfxcontrol" name="pmmark-'.$msgid.'" value="'.__("Mark Unread", "sforum").'" onclick="sfjmarkUnread(\''.$msgurl.'\', \''.$msgid.'\', \''.$text.'\')" />';

	return $out;
}

# ------------------------------------------------------------------
# sf_render_pm_delete_thread()
#
# Delete thread button
#	$threadindex	Table row index
#	$view			Set to 'inbox' or 'sentbox'
#	$slug			The outer table row slug
# ------------------------------------------------------------------
function sf_render_pm_delete_thread($threadindex, $view, $slug)
{
	global $sfglobals;

	if (!$sfglobals['lockdown'])
	{
        $threadurl = SFHOMEURL."index.php?sf_ahah=pm-manage&pmdelthread=".$slug."&amp;pmaction=".$view;
		$out = '<input type="button" class="sfxcontrol" name="deletethread" id="pm-'.$slug.'delthread'.'" tabindex="0" value="'.__("Delete Thread", "sforum").'" onclick="sfjdeleteThread(this, \''.$threadurl.'\', \''.$threadindex.'\', \'messagediv-'.$slug.'\');" />';
	}

	return $out;
}

# ------------------------------------------------------------------
# sf_render_pm_delete_message()
#
# Remders the 'Delete button on inbox vien
#	$
#	$
#	$
#	$
# ------------------------------------------------------------------
function sf_render_pm_delete_message($messageindex, $threadindex, $view, $msgid, $slug)
{
	global $sfglobals;

	if (!$sfglobals['lockdown'])
	{
        $messageurl = SFHOMEURL."index.php?sf_ahah=pm-manage&pmdelmsg=".$msgid."&amp;pmaction=".$view;
        $threadurl = SFHOMEURL."index.php?sf_ahah=pm-manage&pmdelthread=".$slug."&amp;pmaction=".$view;
		$out = '<input type="button" class="sfxcontrol" name="deletemessage'.$msgid.'" id="deletemessage'.$msgid.'" tabindex="0" value="'.__("Delete Message", "sforum").'" onclick="sfjdeletePM(this, \''.$messageurl.'\', \''.$threadurl.'\', \''.$messageindex.'\', \''.$threadindex.'\', \'messagediv-'.$slug.'\', \''.$slug.'\');" />';
	}

	return $out;
}

# ------------------------------------------------------------------
# sf_render_pm_inbox_warning()
#
# Remders warning message regarding inbox size
#	$message		The appropriate message to display
# ------------------------------------------------------------------
function sf_render_pm_inbox_warning($message)
{
	$out = '<div class="sfmessagestrip sfpmalert"><p>'.$message.'</p></div>';
	return $out;
}

# ------------------------------------------------------------------
# sf_localise_boxname()
#
# Localises box name 'inbox' and 'sentbox' used as parameters
#	$box		The English name of the box
# ------------------------------------------------------------------
function sf_localise_boxname($box)
{
	$box = ucfirst($box);
	if($box == "Inbox")
	{
		return __("Inbox", "sforum");
	} else {
		return __("Sentbox", "sforum");
	}
}

?>