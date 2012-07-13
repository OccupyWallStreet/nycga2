<?php
/*
Simple:Press
Common AHAH
$LastChangedDate: 2011-03-31 11:50:03 -0700 (Thu, 31 Mar 2011) $
$Rev: 5781 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

sfa_admin_ahah_support();

global $wpdb;

if (isset($_GET['show_msbox']))
{
	$msbox = $_GET['msbox'];                   	# name of multiselect box - used to determine which query to use
	$uid = sf_esc_int($_GET['uid']);         	# uniue identifier - should be a number
	$name = esc_attr($_GET['name']);			# name of select element on the form
	$from = esc_html($_GET['from']);			# text displayed above where selecting from
	$to = esc_html($_GET['to']);				# text displayed above where selecting to
	$num = sf_esc_int($_GET['num']);			# number of items to show per page in the from box
	echo sfc_populate_msbox_list($msbox, $uid, $name, $from, $to, $num);
	die();
}

if (isset($_GET['page_msbox']))
{
	$msbox = $_GET['msbox'];
	$uid = sf_esc_int($_GET['uid']);
	$name = esc_attr($_GET['name']);
	$from = esc_html($_GET['from']);
	$num = sf_esc_int($_GET['num']);
	$offset = sf_esc_int($_GET['offset']);
	$max = sf_esc_int($_GET['max']);
	$filter = $_GET['filter'];

	if ($_GET['page_msbox'] == 'filter')
	{
		$max = sfc_get_query_max($msbox, $uid, $filter);
	}

	echo sfc_page_msbox_list($msbox, $uid, $name, $from, $num, $offset, $max, $filter);
	die();
}

# handle include of file but not via ahah
if (isset($action))
{
    if ($action == 'addug')
        echo sfc_populate_msbox_list('usergroup_add', $usergroup_id, 'member_id', $from, $to, 100);
    else if ($action == 'delug')
       echo sfc_populate_msbox_list('usergroup_del', $usergroup_id, 'dmember_id', $from, $to, 100);
    else if ($action == 'addru')
       echo sfc_populate_msbox_list('rank_add', $rank_id, 'amember_id', $from, $to, 100);
    else if ($action == 'delru')
       echo sfc_populate_msbox_list('rank_del', $rank_id, 'dmember_id', $from, $to, 100);
    return;
}
die();


function sfc_get_query_max($msbox, $uid, $filter)
{
	global $wpdb;

	$like = '';
	if ($filter != '')
	{
		$like = " AND display_name LIKE '%".esc_sql(like_escape($filter))."%'";
	}

	switch ($msbox)
	{
		case 'usergroup_add':
			$max = $wpdb->get_var("
				SELECT COUNT(".SFMEMBERS.".user_id)
				FROM ".SFMEMBERS."
				INNER JOIN ".SFMEMBERSHIPS." ON (".SFMEMBERS.".user_id = ".SFMEMBERSHIPS.".user_id)
				WHERE usergroup_id <> ".$uid.$like."
				ORDER BY display_name"
			);
			break;

		case 'usergroup_del':
			$max = $wpdb->get_var("
				SELECT COUNT(".SFMEMBERSHIPS.".user_id)
				FROM ".SFMEMBERSHIPS."
				JOIN ".SFMEMBERS." ON ".SFMEMBERS.".user_id = ".SFMEMBERSHIPS.".user_id
				WHERE ".SFMEMBERSHIPS.".usergroup_id=".$uid.$like."
				ORDER BY display_name"
			);
			break;

		case 'rank_add':
			$rank = sf_get_sfmeta('special_rank', false, $uid);
			$data = unserialize($rank[0]['meta_value']);
			$memberlist = $data['users'];
			if ($memberlist)
			{
				$memberlist = "'".implode("','", $memberlist)."'";
				$where = ' WHERE user_id NOT IN ('.$memberlist.')'.$like;
			} else {
				$where = '';
			}
			$max = $wpdb->get_var("
				SELECT COUNT(user_id)
				FROM ".SFMEMBERS.
				$where."
				ORDER BY display_name"
			);
			break;

		case 'rank_del':
			$rank = sf_get_sfmeta('special_rank', false, $uid);
			$data = unserialize($rank[0]['meta_value']);
			$memberlist = $data['users'];
			$max = count($memberlist);
			break;
	}

	return $max;
}

function sfc_populate_msbox_list($msbox, $uid, $name, $from, $to, $num)
{
	global $wpdb;

	$out = '';

	switch ($msbox)
	{
		case 'usergroup_add':
			$records = $wpdb->get_results("
				SELECT ".SFMEMBERS.".user_id, display_name, admin
				FROM ".SFMEMBERS."
				INNER JOIN ".SFMEMBERSHIPS." ON (".SFMEMBERS.".user_id = ".SFMEMBERSHIPS.".user_id)
				WHERE usergroup_id <> ".$uid."
				ORDER BY display_name
				LIMIT 0,".$num
			);

			$max = sfc_get_query_max($msbox, $uid, '');
			break;

		case 'usergroup_del':
			$records = 	$wpdb->get_results("
				SELECT ".SFMEMBERSHIPS.".user_id, display_name
				FROM ".SFMEMBERSHIPS."
				JOIN ".SFMEMBERS." ON ".SFMEMBERS.".user_id = ".SFMEMBERSHIPS.".user_id
				WHERE ".SFMEMBERSHIPS.".usergroup_id=".$uid."
				ORDER BY display_name
				LIMIT 0,".$num
			);
			$max = sfc_get_query_max($msbox, $uid, '');
			break;

		case 'rank_add':
			$rank = sf_get_sfmeta('special_rank', false, $uid);
			$data = unserialize($rank[0]['meta_value']);
			$memberlist = $data['users'];
			if ($memberlist)
			{
				$memberlist = "'".implode("','", $memberlist)."'";
				$where = ' WHERE user_id NOT IN ('.$memberlist.')';
			} else {
				$where = '';
			}
			$records = $wpdb->get_results("
				SELECT user_id, display_name
				FROM ".SFMEMBERS.
				$where."
				ORDER BY display_name
				LIMIT 0,".$num
			);
			$max = sfc_get_query_max($msbox, $uid, '');
			break;

		case 'rank_del':
			$rank = sf_get_sfmeta('special_rank', false, $uid);
			$data = unserialize($rank[0]['meta_value']);
			$memberlist = $data['users'];
			$max = count($memberlist);
            $records = array();
            if ($memberlist)
            {
    			if ($filter != '')
    			{
    				$newlist = array();
    				foreach ($memberlist as $k => $v)
    				{
    				      if (eregi($filter, $v)) {
    				            $newlist[] = array ($k, $v);
    				      } else {}
    				}
    				$memberlist = $newlist;
    			}
    			$records = array_slice($memberlist, 0, $num);
            }
			break;

		default:
			die(); # invalid msbox type
	}

	$out.= '<table>';
	$out.= '<tr>';
	$out.= '<td width="50%" style="border:none !important;vertical-align: top !important;">';
	$out.= '<div id="mslist-'.$name.$uid.'">';
	$out.= sfc_render_msbox_list($msbox, $uid, $name, $from, $num, $records, 0, $max, '');
	$out.= '</div>';
	$out.= '</td>';

	$out.= '<td width="50%" style="border:none !important;vertical-align: top !important;">';
	$out.= '<div align="center"><strong>'.$to.'</strong></div>';
	$out.= '<select class="sfacontrol" multiple="multiple" size="10" id="'.$name.$uid.'" name="'.$name.'[]" >';
	$out.= '<option disabled="disabled" value="-1">'.__("List is Empty", "sforum").'</option>';
	$out.= '</select>';
	$out.= '<div align="center" style="margin-top:42px;">';
	$out.= '<input type="button" id="add'.$uid.'" class="button button-highlighted" value="'.__("Remove From Selected List", "sforum").'" onclick="sfjTransferSelectList(\''.$name.$uid.'\', \'temp-'.$name.$uid.'\', \''.esc_js(__("List is Empty", "sforum")).'\')" />';
	$out.= '</div>';
	$out.= '</td>';
	$out.= '</tr>';
	$out.= '</table>';

	return $out;
}

function sfc_page_msbox_list($msbox, $uid, $name, $from, $num, $offset, $max, $filter)
{
	global $wpdb;

	$out = '';
	$like = '';
	if ($filter != '')
	{
		$like = " AND display_name LIKE '%".esc_sql(like_escape($filter))."%'";
	}

	switch ($msbox)
	{
		case 'usergroup_add':
			$sql = "
				SELECT user_id, display_name, admin
				FROM ".SFMEMBERS."
				WHERE NOT EXISTS (SELECT null FROM ".SFMEMBERSHIPS." WHERE usergroup_id = ".$uid." AND user_id = ".SFMEMBERS.".user_id)".$like."
				ORDER BY display_name
				LIMIT ".$offset.", ".$num
			;
			$records = $wpdb->get_results($sql);
			break;

		case 'usergroup_del':
			$sql = "SELECT ".SFMEMBERSHIPS.".user_id, display_name
				FROM ".SFMEMBERSHIPS."
				JOIN ".SFMEMBERS." ON ".SFMEMBERS.".user_id = ".SFMEMBERSHIPS.".user_id
				WHERE ".SFMEMBERSHIPS.".usergroup_id=".$uid.$like."
				ORDER BY display_name
				LIMIT ".$offset.", ".$num
			;
			$records = $wpdb->get_results($sql);
			break;

		case 'rank_add':
			$rank = sf_get_sfmeta('special_rank', false, $uid);
			$data = unserialize($rank[0]['meta_value']);
			$memberlist = $data['users'];
			if ($memberlist)
			{
				$memberlist = "'".implode("','", $memberlist)."'";
				$where = ' WHERE user_id NOT IN ('.$memberlist.')'.$like;
			} else {
				$where = ''.str_replace(' AND ', ' WHERE ', $like);
			}
			$sql = "
				SELECT user_id, display_name
				FROM ".SFMEMBERS.
				$where."
				ORDER BY display_name
				LIMIT ".$offset.", ".$num
			;
			$records = $wpdb->get_results($sql);
			break;

		case 'rank_del':
			$rank = sf_get_sfmeta('special_rank', false, $uid);
			$data = unserialize($rank[0]['meta_value']);
			$memberlist = $data['users'];
			if ($filter != '')
			{
				$newlist = array();
				foreach ($memberlist as $k => $v)
				{
				      if (eregi($filter, $v)) {
				            $newlist[] = array ($k, $v);
				      } else {}
				}
				$memberlist = $newlist;
			}
			$records = array_slice($memberlist, $offset, $num);
			break;

		default:
			die(); # invalid msbox type
	}

	$out.= sfc_render_msbox_list($msbox, $uid, $name, $from, $num, $records, $offset, $max, $filter);

	return $out;
}

function sfc_render_msbox_list($msbox, $uid, $name, $from, $num, $records, $offset, $max, $filter)
{
	$out = '';
	$empty = true;

	$out.= '<div align="center"><strong>'.$from.'</strong></div>';
	$out.= '<select class="sfacontrol" multiple="multiple" size="10" id="temp-'.$name.$uid.'" name="temp-'.$name.$uid.'[]">';

	if ($records)
	{
		foreach ($records as $record)
		{
			switch ($msbox)
			{
				case 'usergroup_add':
					if (!$record->admin)
					{
						$empty = false;
						$out.= '<option value="'.$record->user_id.'">'.sf_filter_name_display($record->display_name).'</option>'."\n";
					}
					break;

				case 'usergroup_del':
				case 'rank_add':
					$empty = false;
					$out.= '<option value="'.$record->user_id.'">'.sf_filter_name_display($record->display_name).'</option>'."\n";
					break;

				case 'rank_del':
					$empty = false;
					$out.= '<option value="'.$record.'">'.sf_filter_name_display(sf_get_member_item($record, 'display_name')).'</option>'."\n";
					break;

				default;
					break;
			}
		}
	}
	if ($empty)
	{
		$out.= '<option disabled="disabled" value="-1">'.__("List is Empty", "sforum").'</option>';
	}
	$out.= '</select>';

	$out.= '<div align="center">';
	$out.= '<small style="line-height:1.6em;">'.__('Paging Controls', 'sforum').'</small><br />';
	$last = floor($max / $num) * $num;
	if ($last >= $max)
	{
		$last = $last - $num;
	}
	$disabled = '';
	if (($offset + $num) >= $max)
	{
		$disabled = ' disabled="disabled"';
	}
    $site = SFHOMEURL."index.php?sf_ahah=common&amp;page_msbox=next&amp;msbox=".$msbox."&amp;uid=".$uid."&amp;name=".$name."&amp;from=".$from."&amp;num=".$num."&amp;offset=".$last."&amp;max=".$max."&amp;filter=".$filter;
	$out.= '<input type="button"'.$disabled.' id="lastpage'.$uid.'" class="button button-highlighted sfalignright" value="'.__('Last','sforum').'" onclick="sfjUpdateMultiSelectList(\''.$site.'\', \''.$name.$uid.'\');" />';

    $site = SFHOMEURL."index.php?sf_ahah=common&amp;page_msbox=next&amp;msbox=".$msbox."&amp;uid=".$uid."&amp;name=".$name."&amp;from=".$from."&amp;num=".$num."&amp;offset=".($offset + $num)."&amp;max=".$max."&amp;filter=".$filter;
	$out.= '<input type="button"'.$disabled.' id="nextpage'.$uid.'" class="button button-highlighted sfalignright" value="'.__('Next','sforum').'" onclick="sfjUpdateMultiSelectList(\''.$site.'\', \''.$name.$uid.'\');" />';

	$disabled = '';
	if ($offset == 0)
	{
		$disabled = ' disabled="disabled"';
	}
    $site = SFHOMEURL."index.php?sf_ahah=common&amp;page_msbox=next&amp;msbox=".$msbox."&amp;uid=".$uid."&amp;name=".$name."&amp;from=".$from."&amp;num=".$num."&amp;offset=0&amp;max=".$max."&amp;filter=".$filter;
	$out.= '<input type="button"'.$disabled.' id="firstpage'.$uid.'" class="button button-highlighted sfalignleft" value="'.__('First','sforum').'" onclick="sfjUpdateMultiSelectList(\''.$site.'\', \''.$name.$uid.'\');" />';

    $site = SFHOMEURL."index.php?sf_ahah=common&amp;page_msbox=next&amp;msbox=".$msbox."&amp;uid=".$uid."&amp;name=".$name."&amp;from=".$from."&amp;num=".$num."&amp;offset=".($offset - $num)."&amp;max=".$max."&amp;filter=".$filter;
	$out.= '<input type="button"'.$disabled.' id="prevpage'.$uid.'" class="button button-highlighted sfalignleft" value="'.__('Prev','sforum').'" onclick="sfjUpdateMultiSelectList(\''.$site.'\', \''.$name.$uid.'\');" />';

	$out.= '<div style="clear:both;padding: 5px 0pt;">';
	$out.= '<input type="button" id="add'.$uid.'" class="button button-highlighted" value="'.__("Move to Selected List", "sforum").'" onclick="sfjTransferSelectList(\'temp-'.$name.$uid.'\', \''.$name.$uid.'\', \''.esc_js(__("List is Empty", "sforum")).'\')" />';
	$out.= '</div>';

	$out.= '<input type=text id="list-filter'.$name.$uid.'" name="list-filter'.$name.$uid.'" value="'.$filter.'" class="sfacontrol sfalignleft" style="width:50% !important;" />';
    $site = SFHOMEURL."index.php?sf_ahah=common&amp;page_msbox=filter&amp;msbox=".$msbox."&amp;uid=".$uid."&amp;name=".$name."&amp;from=".$from."&amp;num=".$num."&amp;offset=0&amp;max=".$max;
	$out.= '<input type="button" id="filter'.$uid.'" class="button button-highlighted" value="'.__("Filter List", "sforum").'" style="margin-top:1px" onclick="sfjFilterMultiSelectList(\''.$site.'\', \''.$name.$uid.'\');" />';

	$out.= '</div>';

	return $out;
}

?>