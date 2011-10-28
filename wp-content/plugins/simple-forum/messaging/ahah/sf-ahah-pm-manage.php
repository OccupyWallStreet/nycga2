<?php
/*
Simple:Press
Ahah call PM related stuff
$LastChangedDate: 2011-05-27 18:38:05 -0700 (Fri, 27 May 2011) $
$Rev: 6136 $
*/

ob_start();

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

sf_forum_ahah_support();
sf_setup_pm_includes();
sf_setup_forum_hooks();
# --------------------------------------------

ob_end_clean();  # Ensure we don't have output from other plugins.
header('Content-Type: text/html; charset='.get_option('blog_charset'));

sf_initialise_globals();

global $current_user, $wpdb;

if (!$current_user->sfusepm) {
	echo (__('Access Denied', "sforum"));
	die();
}

# display message info --------------------------------------------
if(isset($_GET['pminfo']))
{
	$id = sf_esc_int($_GET['pminfo']);
	$view = $_GET['pmaction'];

	if($view == 'inbox' ? $field='from_id' : $field='to_id');

	$message = $wpdb->get_row(
			"SELECT message_id, sent_date, from_id, to_id, title, message_slug, message_status, inbox, sentbox, is_reply, display_name
			 FROM ".SFMESSAGES."
			 JOIN ".SFMEMBERS." ON ".SFMESSAGES.".".$field." = ".SFMEMBERS.".user_id
			 WHERE message_id=".$id);

    echo '<table class="sfpminfolabels">';
    echo '<tr>';
    echo '<td class="sfpminfolabels">';
	echo '<strong>'.__("Subject", "sforum").': </strong>';
    echo '</td>';
    echo '<td>';
    echo sf_filter_title_display($message->title);
    echo '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td class="sfpminfolabels">';
	if ($message)
	{
		if($view == 'inbox')
		{
			echo '<strong>'.__("From", "sforum").': </strong>';
		} else {
			echo'<strong>'. __("To", "sforum").': </strong>';
		}
	}
    echo '</td>';
    echo '<td>';
    echo sf_filter_name_display($message->display_name);
    echo '</td>';
    echo '</tr>';

	if ($view == 'inbox')
	{
		$recipients = $wpdb->get_results(
			"SELECT message_id, sent_date, from_id, to_id, title, message_status, inbox, sentbox, is_reply, display_name, type
			 FROM ".SFMESSAGES."
			 JOIN ".SFMEMBERS." ON ".SFMESSAGES.".to_id = ".SFMEMBERS.".user_id
			 WHERE message_slug='".$message->message_slug."' AND sent_date='".$message->sent_date."'");

		if ($recipients)
		{
			$to = $cc = $bcc = '';
			foreach ($recipients as $recipient)
			{
				if ($recipient->type == 1)  # to
				{
					$to[] = $recipient;
			 	} else if ($recipient->type == 2) # cc
				{
					$cc[] = $recipient;
			 	} else if ($recipient->type == 3 && $recipient->to_id == $current_user->ID) # bcc to this user
			 	{
					$bcc = $recipient;
				}
			}

			# show 'to' addressees
			if ($to)
			{
				$many = count($to);
				$thisone = 1;
                echo '<tr>';
                echo '<td class="sfpminfolabels">';
				echo '<strong>'.__("To", "sforum").': </strong>';
                echo '</td>';
                echo '<td>';
				foreach ($to as $recipient)
				{
					echo sf_filter_name_display($recipient->display_name);
					if ($thisone < $many) echo(", ");
					$thisone++;
				}
                echo '</td>';
                echo '</tr>';
			}

			# show 'cc' addressees
			if ($cc)
			{
				$many = count($cc);
				$thisone = 1;
                echo '<tr>';
                echo '<td class="sfpminfolabels">';
				echo '<strong>'.__("Cc", "sforum").': </strong>';
                echo '</td>';
                echo '<td>';
				foreach ($cc as $recipient)
				{
					echo sf_filter_name_display($recipient->display_name);
					if ($thisone < $many) echo(", ");
					$thisone++;
				}
                echo '</td>';
                echo '</tr>';
			}

			# show 'bcc' address if to this user
			if ($bcc)
			{
                echo '<tr>';
                echo '<td class="sfpminfolabels">';
				echo '<strong>'.__("Bcc", "sforum").': </strong>';
                echo '</td>';
                echo '<td>';
				echo sf_filter_name_display($bcc->display_name);
                echo '</td>';
                echo '</tr>';
			}
		}
	}

    echo '</table>';

	die();
}

# display message content -----------------------------------------
if(isset($_GET['pmshow']))
{
	$id = sf_esc_int($_GET['pmshow']);
	$content = sf_filter_content_display(sf_get_pm_message($id));
	$content = do_shortcode($content);

	# mark it as read in the database record
	if($_GET['pmaction'] == 'inbox')
	{
		sf_pm_set_read($id);
	}
    echo $content;

	# syntax Highlighting
	$sfsyntax = sf_get_option('sfsyntax');
	if ($sfsyntax['sfsyntaxforum'] == true)
	{ ?>
	<script type="text/javascript">
		Syntax.root = "<?php echo SFJSCRIPT.'syntax/'; ?>";
		jQuery.syntax({layout: 'table', replace: true});
	</script>
	<?php }

	die();
}

# display message buttons -----------------------------------------
if(isset($_GET['pmbuttons']))
{
	$id = sf_esc_int($_GET['pmbuttons']);
	$box = $_GET['pmaction'];

	# GET ALL RECIPIENTS
	$idlist = '';
	$namelist = '';

	$allIds = array();
	$allNames = array();

	$msg = $wpdb->get_row(
			"SELECT sent_date, from_id, title, message_slug, message_status, display_name
			 FROM ".SFMESSAGES."
			 JOIN ".SFMEMBERS." ON ".SFMESSAGES.".from_id = ".SFMEMBERS.".user_id
			 WHERE message_id=".$id);

	# Go and get all recipients (for Reply/Quote All buttons)
	$recipients = $wpdb->get_results(
		"SELECT to_id, display_name, type
		 FROM ".SFMESSAGES."
		 JOIN ".SFMEMBERS." ON ".SFMESSAGES.".to_id = ".SFMEMBERS.".user_id
		 WHERE message_slug='".$msg->message_slug."' AND sent_date='".$msg->sent_date."'");

	if ($recipients)
	{
		foreach ($recipients as $recipient)
		{
			if ($recipient->to_id != $current_user->ID && $recipient->type != 3)
			{
				$allIds[] = $recipient->to_id;
				$allNames[] = sf_filter_name_display($recipient->display_name);
			}
		}
	}
	if ($allIds)
	{
		$idlist = implode(',', $allIds);
		$namelist = implode(',', $allNames);
	}

	if ($box == 'inbox')
    {
    	echo sf_render_pm_mark_unread($id, $msg->from_id);
    	echo sf_render_pm_reply_message($id, $msg->from_id, sf_filter_name_display($msg->display_name), sf_filter_title_display($msg->title), $msg->message_status, 1, $idlist, $namelist, $msg->message_slug);
    	echo sf_render_pm_quote_message($id, $msg->from_id, sf_filter_name_display($msg->display_name), sf_filter_title_display($msg->title), $msg->message_status, 1, $idlist, $namelist, $msg->message_slug);
    }
	echo sf_render_pm_forward_message($id, sf_filter_name_display($msg->display_name), sf_filter_title_display($msg->title), $msg->message_status, 1, $msg->message_slug);

	die();
}

# delete a message thread -----------------------------------------
if(isset($_GET['pmdelthread']))
{
	$box = $_GET['pmaction'];
	$slug = sf_esc_str($_GET['pmdelthread']);

	if($box == 'inbox' ? $field='to_id' : $field='from_id');

	$messages = $wpdb->get_results(
			"SELECT message_id
			 FROM ".SFMESSAGES."
			 WHERE ".$field."=".$current_user->ID." AND message_slug='".$slug."'");

	if($messages)
	{
		foreach($messages as $message)
		{
			sf_pm_delete($message->message_id, $box);
		}
	}
	die();
}

# delete a message ------------------------------------------------
if (isset($_GET['pmdelmsg']))
{
	$id = sf_esc_int($_GET['pmdelmsg']);
	$box = $_GET['pmaction'];

	# make sure it belongs to current user
	$field = ($box == 'inbox') ? 'to_id' : 'from_id';
	$messages = $wpdb->get_results(
			"SELECT message_id
			 FROM ".SFMESSAGES."
			 WHERE ".$field."=".$current_user->ID." AND message_id='".$id."'");
	if ($messages) sf_pm_delete($id, $box);

	die();
}

# delete whole inbox or sentbox -----------------------------------
if(isset($_GET['pmdelall']))
{
	$box = $_GET['pmdelall'];
	$userid = sf_esc_int($_GET['owner']);

	switch($box)
	{
		case 'inbox':
			$pmlist = sf_get_pm_inbox_idlist($userid);
			break;
		case 'sentbox':
			$pmlist = sf_get_pm_sentbox_idlist($userid);
			break;
	}

	if($pmlist)
	{
		foreach($pmlist as $pm)
		{
			sf_pm_delete($pm->message_id, $box);
		}
	}
	die();
}

# Populate members box --------------------------------------------
if(isset($_GET['pop']))
{

	$out = '<select class="sflistcontrol" tabindex="4" name="pmmemlist" id="pmmemlist" size="9" onchange="sfjaddpmUser(\''.pmmemlist.'\');">'."\n";
	$out.= sf_create_pmuser_select(-1, 'members', sf_esc_str($_GET['pop']));
	$out.= '</select>';

	echo $out;

	die();
}

# Add recipients to users buddy list ------------------------------
if(isset($_GET['addbuddy']))
{
	$list = array();
	$list = explode('-', sf_esc_str($_GET['addbuddy']));
	if($list)
	{
		foreach($list as $buddy)
		{
			if($buddy != 0)
			{
				sf_add_buddy($buddy);
			}
		}
		$out = '<select class="sflistcontrol" tabindex="5" name="pmbudlist" id="pmbudlist" size="6" onchange="sfjpmaddbuddy(\''.pmbudlist.'\');">'."\n";
		$out.= sf_create_pmuser_select(-1, 'buddies');
		$out.= '</select>';
		echo $out;
	}
	die();
}

if(isset($_GET['q']))
{
	$query = $_GET['q'];
	if ($_GET['search'] == 1)
	{
		$where = 'AND display_name LIKE "%'.esc_sql(like_escape($query)).'%"';
		$users = $wpdb->get_results("SELECT user_id AS ID, display_name, admin, moderator FROM ".SFMEMBERS." WHERE pm = 1 ".$where." ORDER BY admin DESC, moderator DESC, display_name");
	}

	if ($users)
	{
		foreach($users as $user)
		{
			echo sf_filter_name_display($user->display_name).'|'.$user->ID."\n";
		}
	}

	die();
}

# mark message unread ------------------------------------------------
if (isset($_GET['pmmarkunread']))
{
	$id = sf_esc_int($_GET['msgid']);
	sf_pm_mark_unread($id);
	die();
}

# -----------------------
# support routines
# -----------------------

function sf_get_pm_message($id)
{
	global $wpdb;
	return $wpdb->get_var("SELECT message FROM ".SFMESSAGES." WHERE message_id=".$id);
}

function sf_pm_set_read($id)
{
	global $wpdb;
	$wpdb->query("UPDATE ".SFMESSAGES." SET message_status=1 WHERE message_id=".$id);
	return;
}

function sf_pm_delete($id, $box)
{
	global $wpdb;

	$delete = false;

	# Only delete if both sentbox and inbox have been set to zero - so check first
	$message = $wpdb->get_row("SELECT * FROM ".SFMESSAGES." WHERE message_id=".$id, ARRAY_A );

	switch($box)
	{
		case 'inbox':
			if($message['sentbox'] == 0) $delete = true;
			break;
		case 'sentbox':
			if($message['inbox'] == 0) $delete = true;
			break;
	}

	if($delete)
	{
		$wpdb->query("DELETE FROM ".SFMESSAGES." WHERE message_id=".$id);
	} else {
		$wpdb->query("UPDATE ".SFMESSAGES." SET ".$box."=0 WHERE message_id=".$id);
	}
	return;
}

function sf_pm_mark_unread($id)
{
	global $wpdb;
	$wpdb->query("UPDATE ".SFMESSAGES." SET message_status=0 WHERE message_id=".$id);
    return;
}

?>