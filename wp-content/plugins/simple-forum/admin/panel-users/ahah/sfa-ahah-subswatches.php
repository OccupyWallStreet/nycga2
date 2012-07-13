<?php
/*
Simple:Press
AHAH routine displaying member subscriptions and watches
$LastChangedDate: 2010-10-22 03:27:46 -0700 (Fri, 22 Oct 2010) $
$Rev: 4785 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

sfa_admin_ahah_support();

# Check Whether User Can Manage Forums
if (!sfc_current_user_can('SPF Manage Forums'))
{
	echo (__('Access Denied', "sforum"));
	die();
}

$error = "";

if (isset($_GET['action']) && $_GET['action'] == 'swlist')
{

	if (isset($_GET['groups']) && sf_esc_str($_GET['groups']) == 'error')
	{
		$error = "Group";
	}
	if (isset($_GET['forums']) && sf_esc_str($_GET['forums']) == 'error')
	{
		$error = "Forums";
	}
	if ($error)
	{
		echo sprintf(__("You elected to filter by %s but selected no %s items", "sforum"), $error, $error);
		die();
	}

	sfa_render_subswatches();
	die();
} else {
	die();
}

function sfa_render_subswatches()
{
    $subs = sf_esc_str($_GET['showsubs']);
    $watches = sf_esc_str($_GET['showwatches']);
    $filter = sf_esc_str($_GET['filter']);
    if (isset($_GET['page']))
    {
    	$curpage = sf_esc_int($_GET['page']);
   	} else {
   		$curpage = 1;
   	}
    if (isset($_GET['swsearch']))
    {
    	$search = sf_esc_str($_GET['swsearch']);
   	} else {
   		$search = '';
   	}
    if (isset($_GET['groups']))
    {
    	$groups = explode('-', sf_esc_str($_GET['groups']));
    } else {
		$groups[0] = -1;
	}
    if (isset($_GET['forums']))
    {
    	$forums = explode('-', sf_esc_str($_GET['forums']));
    } else {
		$forums[0] = -1;
	}
	$data = sfa_get_watches_subs($subs, $watches, $filter, $groups, $forums, $curpage, $search);
	$records = $data['data'];

	if ($subs || $watches)
	{
		# paging
		$totalpages = ceil($data['count'] / 20);
		echo '<div class="sfform-panel">';
		echo '<div class="sfform-panel-head">';
		echo '<span class="sftitlebar">'. __("Manage Users - Subscriptions and Watches", "sforum"). '</span>';
		echo '</div>';
		echo '<table width="100%" cellspacing="0" cellpadding="0"><tr><td>';
		echo '<fieldset class="sffieldset"><legend><strong>'. __("Subscriptions and Watches", "sforum"). '</strong></legend>';
		echo '<div class="tablenav">';
		echo '<div class="tablenav-pages">';
		echo '<strong>'.__("Page:", "sforum").'</strong>  ';
 		echo sfa_pn_next($curpage, $totalpages, 3);
		echo '<span class="page-numbers current">'.$curpage.'</span>'."\n";
		echo sfa_pn_previous($curpage, $totalpages, 3);
		echo '</div>';

		echo '<div>';
        $site = SFHOMEURL."index.php?sf_ahah=subswatches&action=swlist&amp;page=1";
		$gif = SFADMINIMAGES."working.gif";
		echo '<form action="'.SFADMINUSER.'" method="post" name="sfwatchessubs" id="sfwatchessubs" onsubmit="return sfjshowSubsList(this, \''.$site.'\', \''.$gif.'\');" >';
?>
		<input type="text" class="sfacontrol" id="post-search-input" name="swsearch" value="<?php echo esc_attr($search); ?>" />
		<input type="button" class="sfform-panel-button" onclick="sfjshowSubsList('sfwatchessubs', '<?php echo $site; ?>', '<?php echo $gif; ?>');" value="<?php esc_attr_e(__('Search', 'sforum')); ?>" />
		</form>
		</div>
<?php
		echo '</div>';

		# show data
		echo '<table class="sfsubtable" cellpadding="0" cellspacing="0">';
		if ($records)
		{
			echo '<tr>';
			echo '<th width="15%"><small>'.__("Group", "sforum").'</small></th>';
			echo '<th width="20%"><small>'.__("Forum", "sforum").'</small></th>';
			echo '<th width="20%"><small>'.__("Topic", "sforum").'</small></th>';
			echo '<th><small>'.__("Watches/Subscriptions", "sforum").'</small></th>';
			echo '<th align="right" width="50" colspan="2" style="padding-right:20px"><small>'.__("Manage", "sforum").'</small></th>';
			echo '</tr>';
			foreach ($records as $index => $record)
			{
				echo '<tr>';
				echo '<td colspan="6" style="border-bottom:0px;padding:0;">';
				echo '<div id="subswatches'.$index.'">';
				echo '<table width="100%" cellspacing="0">';
				echo '<tr>';
				echo '<td width="15%">'.sf_filter_title_display($record->group_name).'</td>';
				echo '<td width="20%">'.sf_filter_title_display($record->forum_name).'</td>';
				$url = sf_build_url($record->forum_slug, $record->topic_slug, 1, 0);
				echo '<td width="20%"><a href="'.$url.'">'.sf_filter_title_display($record->topic_name).'</a></td>';
				echo '<td>';
				$have_subs = 0;
				if ($subs) # subs
				{
					if ($record->topic_subs)
					{
						$have_subs = 1;
						$first = true;
						$list = unserialize($record->topic_subs);
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
						if ($record->topic_watches) echo '<br /><br />';
					}
				}
				$have_watches = 0;
				if ($watches) # watches
				{
					if ($record->topic_watches)
					{
						$have_watches = 1;
						$first = true;
						$list = unserialize($record->topic_watches);
						for ($x=0; $x<count($list); $x++)
						{
							$user = sf_get_member_row($list[$x]);
							if ($first)
							{
								echo __("Watches", "sforum").":<br />";
								echo sf_filter_name_display($user['display_name']);
								$first = false;
							} else {
								echo ', '.sf_filter_name_display($user['display_name']);
							}
						}
					}
				}
				echo '</td>';
				echo '<td width="25" align="center">';
				$gif = SFADMINIMAGES."working.gif";
				if ($subs && $record->topic_subs)
				{
                    $site = SFHOMEURL."index.php?sf_ahah=user&action=del_subs&id=".$record->topic_id."&watches=".$have_watches."&subs=0&group=".sf_filter_title_display($record->group_name)."&forum=".sf_filter_title_display($record->forum_name)."&slug=".$record->topic_slug."&eid=".$index;
					if ($have_watches) $fade = 0; else $fade = 1;
					?>
					<img onclick="sfjDelWatchesSubs('<?php echo $site; ?>', '<?php echo $gif; ?>', '<?php echo $fade; ?>', 'subswatches<?php echo $index; ?>');" src="<?php echo SFADMINIMAGES; ?>del_sub.png" title="<?php esc_attr_e(__("Delete Subscriptions", "sforum")); ?>"/>&nbsp;
					<?php
				}
				echo '</td>';
				echo '<td width="25">';
				if ($watches && $record->topic_watches)
				{
					if ($have_subs) $fade = 0; else $fade = 1;
                    $site = SFHOMEURL."index.php?sf_ahah=user&action=del_watches&id=".$record->topic_id."&subs=".$have_subs."&watches=0&group=".sf_filter_title_display($record->group_name)."&forum=".sf_filter_title_display($record->forum_name)."&slug=".$record->topic_slug."&eid=".$index;
					?>
					<img onclick="sfjDelWatchesSubs('<?php echo $site; ?>', '<?php echo $gif; ?>', '<?php echo $fade; ?>', 'subswatches<?php echo $index; ?>');" src="<?php echo SFADMINIMAGES; ?>del_watch.png" title="<?php esc_attr_e(__("Delete Watches", "sforum")); ?>"/>&nbsp;
					<?php
				}
				echo '</td>';
				echo '</tr>';
				echo '</table>';
				echo '</div>';
				echo '</td>';
				echo '</tr>';
			}
		} else {
			echo '<tr>';
			echo '<td>';
			echo __("No Watches or Subscriptions Found!", "sforum");
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';
		echo '</fieldset></td></tr></table>';
		echo '</div>';
	} else {
		echo '<p>'.__("You must select Show Subscriptions and/or Show Watches to get results!", "sforum").'</p>';
	}

	return;
}

function sfa_get_watches_subs($subs, $watches, $filter, $groups, $forums, $curpage, $search)
{
	global $wpdb;

	# setup where we are in the list (paging)
	$startlimit = 0;
	if ($curpage != 1)
	{
		$startlimit = ($curpage - 1) * 20;
	}

	$limit = " LIMIT ".$startlimit.', 20';

	# create the where based on watches and/or subscriptions
	if (($subs==1) && ($watches==0))  # subscriptions only
	{
		$where2 = ' AND topic_subs != ""';
	} else if (($subs==0) && ($watches==1)) # watches only
	{
		$where2 = ' AND topic_watches != ""';
	} else if (($subs==1) && ($watches==1)) # both subscriptions and watches
	{
		$where2 = ' AND (topic_subs != "" OR topic_watches != "")';
	} else # neither selected return empty result
	{
		return '';
	}

	# create the join based on all, group or forum filter
	if ($filter == 'groups' && $groups[0] != -1) # filter by groups
	{
		$where1 = " WHERE ".SFGROUPS.".group_id IN (".implode(",", $groups).")";
	} else if ($filter == 'forums' && $forums[0] != -1) # filter by forums
	{
		$where1 = " WHERE ".SFFORUMS.".forum_id IN (".implode(",", $forums).")";
	} else { # all groups/forums
		$where1 = " WHERE 1";
	}

	# any search terms?
	$like = '';
	if (!empty($search))
	{
		$like = ' AND topic_name LIKE "%'.esc_sql(like_escape($search)).'%"';
	}

	# retrieve watched topic records
	$query = "SELECT SQL_CALC_FOUND_ROWS topic_id, topic_name, topic_slug, group_name, forum_name, forum_slug, topic_subs, topic_watches
			 FROM ".SFTOPICS."
			 JOIN ".SFFORUMS." ON ".SFFORUMS.".forum_id = ".SFTOPICS.".forum_id
			 JOIN ".SFGROUPS." ON ".SFGROUPS.".group_id = ".SFFORUMS.".group_id ".
			 $where1.$where2.$like.' ORDER BY topic_id DESC'.$limit;
	$records['data'] = $wpdb->get_results($query);
	$records['count'] = $wpdb->get_var("SELECT FOUND_ROWS()");

	return $records;
}

function sfa_pn_next($cpage, $totalpages, $pnshow)
{
	$start = ($cpage - $pnshow);
	if ($start < 1) $start = 1;
	$end = ($cpage - 1);
	$out = '';

	if ($start > 1)
	{
		$out.= sfa_pn_url(1);
		$out.= '<span class="page-numbers dota">...</span>'."\n";
	}

	if ($end > 0)
	{
		for ($i = $start; $i <= $end; $i++)
		{
			$out.= sfa_pn_url($i);
		}
	}

	return $out;
}

function sfa_pn_previous($cpage, $totalpages, $pnshow)
{
	$start = ($cpage + 1);
	$end = ($cpage + $pnshow);
	if ($end > $totalpages) $end = $totalpages;
	$out = '';

	if ($start <= $totalpages)
	{
		for ($i = $start; $i <= $end; $i++)
		{
			$out.= sfa_pn_url($i);
		}
		if ($end < $totalpages)
		{
			$out.= '<span class="page-numbers dota">...</span>'."\n";
			$out.= sfa_pn_url($totalpages);
		}
	}

	return $out;
}

function sfa_pn_url($thispage)
{
	$out = '';

    $site = SFHOMEURL."index.php?sf_ahah=subswatches&action=swlist&amp;page=".$thispage;
	$gif = SFADMINIMAGES."working.gif";
	$out.= '<a class="page-numbers" href="#" onclick="sfjshowSubsList(\'sfwatchessubs\', \''.$site.'\', \''.$gif.'\');">'.$thispage.'</a>';

	return $out;
}

?>