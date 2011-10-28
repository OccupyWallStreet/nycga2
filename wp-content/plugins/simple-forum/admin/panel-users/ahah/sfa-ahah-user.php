<?php
/*
Simple:Press
User Specials
$LastChangedDate: 2010-08-17 11:03:43 -0700 (Tue, 17 Aug 2010) $
$Rev: 4465 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

sfa_admin_ahah_support();
sf_build_memberdata_cache();

global $sfglobals;

# Check Whether User Can Manage User Groups
if (!sfc_current_user_can('SPF Manage Users'))
{
	echo (__('Access Denied', "sforum"));
	die();
}

global $wpdb, $sfglobals;

$action = $_GET['action'];
if ($action == 'display-groups')
{
	echo "<select style='width:200px' multiple size='10' class='sfacontrol' id='grouplist' name='watchessubsgroups[]'>";
	$groups = sf_get_groups_all();
	if ($groups)
	{
		foreach ($groups as $group)
		{
			echo '<option value="'.$group->group_id.'">'.sf_filter_title_display($group->group_name).'</option>';
		}
	}
	echo '</select>';
	echo '<br />';
	echo '<input type="button" class="button button-highlighted" value="'.__("Close", "sforum").'" onclick="sfjtoggleLayer(\'select-group\');">';
}

if ($action == 'display-forums')
{
	echo "<select style='width:200px' multiple size='10' class='sfacontrol' id='forumlist' name='watchessubsforums[]'>";
	$forums = sfa_get_forums_all();
	if ($forums)
	{
		$thisgroup = 0;
		foreach ($forums as $forum)
		{
			if ($thisgroup != $forum->group_id)
			{
				if ($thisgroup != 0)
				{
					echo '</optgroup>'."\n";
				}
				echo '<optgroup label="'.sf_filter_title_display($forum->group_name).'">'."\n";
				$thisgroup = $forum->group_id;
			}
			echo '<option value="'.$forum->forum_id.'">'.sf_filter_title_display($forum->forum_name).'</option>';
		}
	}
	echo '</optgroup></select>';
	echo '<br />';
	echo '<input type="button" class="button button-highlighted" value="'.__("Close", "sforum").'" onclick="sfjtoggleLayer(\'select-forum\');">';
}

if ($action == 'del_inbox' || $action == 'del_sentbox' || $action == 'del_pms')
{
	$uid = sf_esc_int($_GET['id']);
	$name = esc_html($_GET['name']);
	$pm = sf_esc_int($_GET['pm']);
	$inbox = sf_esc_int($_GET['inbox']);
	$unread = sf_esc_int($_GET['unread']);
	$sentbox = sf_esc_int($_GET['sentbox']);
	$eid = sf_esc_int($_GET['eid']);

	if ($action == 'del_inbox')
	{
		$wpdb->query("UPDATE ".SFMESSAGES." SET inbox=0, message_status=1 WHERE to_id=".$uid." AND inbox=1");
		$wpdb->query("DELETE FROM ".SFMESSAGES." WHERE to_id=".$uid." AND (inbox=0 AND sentbox=0)");
		$inbox = 0;
		$unread = 0;
	}

	if ($action == 'del_sentbox')
	{
		$wpdb->query("UPDATE ".SFMESSAGES." SET sentbox=0 WHERE from_id=".$uid." AND sentbox=1");
		$wpdb->query("DELETE FROM ".SFMESSAGES." WHERE from_id=".$uid." AND (inbox=0 AND sentbox=0)");
		$sentbox = 0;
	}

	if ($action == 'del_pms')
	{
		$wpdb->query("DELETE FROM ".SFMESSAGES." WHERE to_id=".$uid." OR from_id=".$uid);
		$inbox = 0;
		$sentbox = 0;
		$unread = 0;
	}

	$total = $inbox + $sentbox;
?>
	<table width="100%" cellspacing="0">
		<tr>
			<td align="center" width="90" style="padding:5px 0px;"><?php echo $uid; ?></td>
			<td style="padding:2px 0px;"><?php echo $name; ?></td>
			<td align="center" width="20" style="padding:5px 0px;"></td>
			<td align="center" width="50" style="padding:5px 0px;"><?php if ($pm) echo __("Yes", "sforum"); else echo __("No", "sforum"); ?></td>
			<td align="center" width="20" style="padding:5px 0px;"></td>
			<td align="center" width="90" style="padding:5px 0px;"><?php echo $total; ?></td>
			<td align="center" width="90" style="padding:5px 0px;"><?php echo $inbox; ?></td>
			<td align="center" width="90" style="padding:5px 0px;"><?php echo $unread; ?></td>
			<td align="center" width="90" style="padding:5px 0px;"><?php echo $sentbox; ?></td>
			<td align="center" width="20" style="padding:5px 0px;"></td>
			<td align="center" width="80" style="padding:5px 0px;">
			<?php if ($pmdata[$userid]['inbox'] > 0 || $pmdata[$userid]['sentbox'] > 0)
			{ ?>
                <?php $site = SFHOMEURL."index.php?sf_ahah=user&action=del_inbox&id=".$uid."&name=".$name."&pm=".$pm."&inbox=".$inbox."&unread=".$unread."&sentbox=".$sentbox."&eid=".$eid; ?>
				<?php $gif = SFADMINIMAGES."working.gif"; ?>
				<img onclick="sfjDelPMs('<?php echo $site; ?>', '<?php echo $gif; ?>', '0', 'pmdata<?php echo $eid; ?>');" src="<?php echo SFADMINIMAGES; ?>inbox_pm.png" title="<?php _e("Delete Inbox PMs", "sforum"); ?>"/>&nbsp;
                <?php $site = SFHOMEURL."index.php?sf_ahah=user&action=del_sentbox&id=".$uid."&name=".$name."&pm=".$pm."&inbox=".$inbox."&unread=".$unread."&sentbox=".$sentbox."&eid=".$eid; ?>
				<img onclick="sfjDelPMs('<?php echo $site; ?>', '<?php echo $gif; ?>', '0', 'pmdata<?php echo $eid; ?>');" src="<?php echo SFADMINIMAGES; ?>sentbox_pm.png" title="<?php _e("Delete Sentbox PMs", "sforum"); ?>"/>&nbsp;
                <?php $site = SFHOMEURL."index.php?sf_ahah=user&action=del_pms&id=".$uid."&name=".$name."&pm=".$pm."&inbox=".$inbox."&unread=".$unread."&sentbox=".$sentbox."&eid=".$eid; ?>
				<img onclick="sfjDelPMs('<?php echo $site; ?>', '<?php echo $gif; ?>', '0', 'pmdata<?php echo $eid; ?>');" src="<?php echo SFADMINIMAGES; ?>all_pm.png" title="<?php _e("Delete All PMs", "sforum"); ?>"/>
			<?php } ?>
			</td>
			<td align="center" width="20" style="padding:5px 0px;"></td>
		</tr>
	</table>
<?php
}

if ($action == 'del_watches' || $action == 'del_subs')
{
	$tid = sf_esc_int($_GET['id']);
	$subs = sf_esc_int($_GET['subs']);
	$watches = sf_esc_int($_GET['watches']);
	$group = esc_html($_GET['group']);
	$forum = sf_esc_str($_GET['forum']);
	$slug = sf_esc_str($_GET['slug']);
	$eid = sf_esc_int($_GET['eid']);

	if ($action == 'del_watches')
	{
		sfa_remove_watches($tid);
	}

	if ($action == 'del_subs')
	{
		sfa_remove_subs($tid);
	}

	if ($subs || $watches)
	{
		echo '<table width="100%" cellspacing="0">';
		echo '<tr>';
		echo '<td width="175" style="padding:4px 0 4px 5px;">'.$group.'</td>';
		echo '<td width="175" style="padding:4px 0 4px 5px;">'.stripslashes($forum).'</td>';
		$url = sf_build_url($forum, $slug, 1, 0);
		$topic = "SELECT topic_name FROM ".SFTOPICS." WHERE topic_id=$tid";
		echo '<td width="175" style="padding:4px 0 4px 5px;"><a href="'.$url.'">'.$topic.'</a></td>';
		echo '<td style="padding:4px 0 4px 5px;">';
		if ($subs) # subs
		{
			$query = "SELECT topic_subs FROM ".SFTOPICS." WHERE topic_id=$tid";
			$record = $wpdb->get_var($query);
			if ($record)
			{
				$first = true;
				$list = unserialize($record);
				for ($x=0; $x<count($list); $x++)
				{
					$user = sf_get_member_row($list[$x]);
					if ($first)
					{
						echo __("Subscriptions", "sforum").":<br />";
						echo sf_filter_name_display($user['display_name']);
						$first = false;
					} else {
						echo ', '.sf_filter_name_display($user['display_name']);
					}
				}
			}
		}
		if ($watches) # watches
		{
			$query = "SELECT topic_watches FROM ".SFTOPICS." WHERE topic_id=$tid";
			$record = $wpdb->get_var($query);
			if ($record)
			{
				$first = true;
				$list = unserialize($record);
				for ($x=0; $x<count($list); $x++)
				{
					$user = sf_get_member_row($list[$x]);
					if ($first)
					{
						echo "Watches:<br />";
						echo sf_filter_name_display($user['display_name']);
						$first = false;
					} else {
						echo ', '.sf_filter_name_display($user['display_name']);
					}
				}
			}
		}
		echo '</td>';
		echo '<td width="30" align="center" style="padding:4px 0 4px 5px;">';
		$gif = SFADMINIMAGES."working.gif";
		if ($subs)
		{
            $site = SFHOMEURL."index.php?sf_ahah=user&action=del_subs&id=".$tid."&watches=0&subs=0&eid=".$eid;
			?>
			<img onclick="sfjDelWatchesSubs('<?php echo $site; ?>', '<?php echo $gif; ?>', '1', 'subswatches<?php echo $index; ?>');" src="<?php echo SFADMINIMAGES; ?>del_sub.png" title="<?php _e("Delete Subscriptions", "sforum"); ?>"/>&nbsp;
			<?php
		}
		echo '</td>';
		echo '<td width="30" align="center" style="padding:4px 0 4px 5px;">';
		if ($watches)
		{
            $site = SFHOMEURL."index.php?sf_ahah=user&action=action=del_watches&id=".$tid."&watches=0&subs=0&eid=".$eid;
			?>
			<img onclick="sfjDelWatchesSubs('<?php echo $site; ?>', '<?php echo $gif; ?>', '1', 'subswatches<?php echo $index; ?>');" src="<?php echo SFADMINIMAGES; ?>del_watch.png" title="<?php _e("Delete Watches", "sforum"); ?>"/>&nbsp;
			<?php
		}
		echo '</td>';
		echo '</tr>';
		echo '</table>';
	}
}

die();

function sfa_remove_watches($tid)
{
	global $wpdb, $sfglobals;

	$query = "SELECT topic_watches FROM ".SFTOPICS." WHERE topic_id=$tid";
	$list = $wpdb->get_var($query);
	$list = unserialize($list);
	for ($x=0; $x<count($list); $x++)
	{
		# remove watch from member
		$topics = sf_get_member_item($list[$x], 'watches');
		if (!empty($topics))
		{
			$newlist = array();
			foreach ($topics as $topic)
			{
				if ($topic != $tid)
				{
					$newlist[] = $topic;
				}
			}
			sf_update_member_item($list[$x], 'watches', $newlist);
		}
	}
	$query = "UPDATE ".SFTOPICS." SET topic_watches='' WHERE topic_id=$tid";
	$wpdb->get_var($query);
}

function sfa_remove_subs($tid)
{
	global $wpdb;

	$query = "SELECT topic_subs FROM ".SFTOPICS." WHERE topic_id=$tid";
	$list = $wpdb->get_var($query);
	$list = unserialize($list);
	for ($x=0; $x<count($list); $x++)
	{
		# remove subscriptions from member
		$topics = sf_get_member_item($list[$x], 'subscribe');
		if (!empty($topics))
		{
			$newlist = array();
			foreach ($topics as $topic)
			{
				if ($topic != $tid)
				{
					$newlist[] = $topic;
				}
			}
			sf_update_member_item($list[$x], 'subscribe', $newlist);
		}
	}
	$query = "UPDATE ".SFTOPICS." SET topic_subs='' WHERE topic_id=$tid";
	$wpdb->get_var($query);
}

?>