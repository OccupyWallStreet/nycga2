<?php
/*
Simple:Press
Admin Forums Data Prep Support Functions
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_group_sequence_options($type, $current)
{
	global $wpdb;

	$groups = sf_get_groups_all();
	$total = count($groups);

	if($groups)
	{
		$positions = array();
		$key = 0;

		foreach($groups as $group)
		{
			if($type == 'edit' && $current == $group->group_seq)
			{
				$positions[$key]['number'] = $group->group_seq;
				$positions[$key]['label']  = '<i>'.__("Current Position", "sforum").'</i>';
			} elseif($type == 'edit' && $group->group_seq == ($current+1)) {
				# skip
			} else {
				$positions[$key]['number'] = $group->group_seq;
				$positions[$key]['label']  = '<i>'.__("Position Before", "sforum").'</i>:  <b>'.sf_filter_title_display($group->group_name).'</b>';
			}
			$key++;
		}
		if(($type == 'new') || ($type == 'edit' && $current < $total))
		{
			$positions[$key]['number'] = ($group->group_seq+1);
			$positions[$key]['label']  = '<i>'.__("Position After", "sforum").'</i>:  <b>'.sf_filter_title_display($group->group_name).'</b>';
		}

		if($current == 0) $current = ($group->group_seq+1);

		if(count($positions) == 0)
		{
			$positions[0]['number'] = 1;
			$positions[0]['label']  = __("Current Position", "sforum");
			$current=1;
		}


		$out = '<td class="sflabel">'.__("Display Position", "sforum").'</td>';
		$out.= '<td>';

		$out.= '<table class="form-table table-cbox"><tr>';
		$out.= "<td width='100%' class='td-cbox'>\n";

		$key = 1;

		foreach($positions as $seq)
		{
			$check = '';
			if($current == $key) $check = ' checked="checked" ';
			$out.= '<label for="sfradio-'.$key.'" class="sflabel radio">'.$seq['label'].'</label>'."\n";
			$out.= '<input type="radio" name="group_seq" id="sfradio-'.$key.'" value="'.$seq['number'].'" '.$check.' />'."\n";
			$key++;
		}
		$out.= "</td></tr></table>";
		$out.= '</td>';
	}
	return $out;
}

function sfa_new_forum_sequence_options($action, $type, $id, $current)
{
	global $wpdb;

	$positions = array();
	$key = 0;

	if($type == 'forum')
	{
		# grab all forums in the group except subforums
		$forums = sfa_get_group_forums_by_parent($id, 0);
		$current = (count($forums)+1);

		if($forums)
		{
			foreach($forums as $forum)
			{
				$positions[$key]['number'] = $forum->forum_seq;
				$positions[$key]['label']  = '<i>'.__("Position Before", "sforum").'</i>:  <b>'.sf_filter_title_display($forum->forum_name).'</b>';
				$key++;
			}
			$positions[$key]['number'] = ($forum->forum_seq+1);
			$positions[$key]['label']  = '<i>'.__("Position After", "sforum").'</i>:  <b>'.sf_filter_title_display($forum->forum_name).'</b>';
		}
		$current = ($forum->forum_seq+1);
	}


	if($type == 'subforum')
	{
		$forum = $wpdb->get_row("SELECT forum_name, forum_seq, children FROM ".SFFORUMS." WHERE forum_id=".$id);
		# forum has no sub forums...
		if(empty($forum->children))
		{
			$positions[$key]['number'] = ($forum->forum_seq+1);
			$positions[$key]['label']  = '<i>'.__("Position After", "sforum").'</i>:  <b>'.sf_filter_title_display($forum->forum_name).'</b>';
			$current = ($forum->forum_seq+1);
		} else {
			# forum does have sub forums
			$list = array();
			$subs = unserialize($forum->children);

			$positions[$key]['number'] = ($forum->forum_seq+1);
			$positions[$key]['label']  = '<i>'.__("Position After", "sforum").'</i>:  <b>'.sf_filter_title_display($forum->forum_name).'</b>';
			$list[] = ($forum->forum_seq+1);
			$key++;

			if($subs)
			{
				foreach($subs as $sub)
				{
					$subrecord = $wpdb->get_row("SELECT forum_name, forum_seq FROM ".SFFORUMS." WHERE forum_id=".$sub);
					if(!in_array(($subrecord->forum_seq+1), $list))
					{
						$positions[$key]['number'] = ($subrecord->forum_seq+1);
						$positions[$key]['label']  = '<i>'.__("Position After", "sforum").'</i>:  <b>'.sf_filter_title_display($subrecord->forum_name).'</b>';
						$list[] = ($subrecord->forum_seq+1);
						$key++;
					}
				}
			}
			$current=$list[count($list)-1];
		}
	}

	if(count($positions) == 0)
	{
		$positions[0]['number'] = 1;
		$positions[0]['label']  = __("Current Position", "sforum");
		$current = 1;
	}

	$out = '<table class="form-table table-cbox"><tr>';
	$out.= "<td width='100%' class='td-cbox'>\n";

	$key = 100;

	foreach($positions as $seq)
	{
		$check = '';
		if($current == $seq['number']) $check = ' checked="checked" ';
		$out.= '<label for="sfradio-'.$key.'" class="sflabel radio">'.$seq['label'].'</label>'."\n";
		$out.= '<input type="radio" class="radiosequence" name="forum_seq" id="sfradio-'.$key.'" value="'.$seq['number'].'" '.$check.' />'."\n";
		$key++;
	}
	$out.= "</td></tr></table>";

	return $out;
}

function sfa_edit_forum_sequence_options($action, $type, $id, $current)
{
	global $wpdb;

	$positions = array();
	$key = 0;

	if($type == 'forum')
	{
		# grab all forums in the group except subforums
		$forums = sfa_get_group_forums_by_parent($id, 0);
		if($forums)
		{
			foreach($forums as $forum)
			{
				if($current == $forum->forum_seq)
				{
					$positions[$key]['number'] = $forum->forum_seq;
					$positions[$key]['label']  = '<i>'.__("Current Position", "sforum").'</i>:  <b>'.sf_filter_title_display($forum->forum_name).'</b>';
					$key++;
				} else {

					if($forum->forum_seq == 1 && ($current > 1 || $current==0))
					{
						$positions[$key]['number'] = $forum->forum_seq;
						$positions[$key]['label']  = '<i>'.__("Position Before", "sforum").'</i>:  <b>'.sf_filter_title_display($forum->forum_name).'</b>';
						$key++;
					} else {

					$positions[$key]['number'] = ($forum->forum_seq+1);
					$positions[$key]['label']  = '<i>'.__("Position After", "sforum").'</i>:  <b>'.sf_filter_title_display($forum->forum_name).'</b>';
					$key++;
					}
				}
			}
			if($current == 0) $current = ($forum->forum_seq+1);
		}
	}

	if($type == 'subforum')
	{
		$parent = $wpdb->get_var("SELECT parent FROM ".SFFORUMS." WHERE forum_id=".$id);
		$forum = $wpdb->get_row("SELECT forum_name, forum_seq, children FROM ".SFFORUMS." WHERE forum_id=".$parent);
		# forum has no sub forums...
		if(empty($forum->children))
		{
			$positions[$key]['number'] = ($forum->forum_seq+1);
			$positions[$key]['label']  = '<i>'.__("Position After", "sforum").'</i>:  <b>'.sf_filter_title_display($forum->forum_name).'</b>';
			$current = ($forum->forum_seq+1);
		} else {
			# forum does have sub forums
			$list = array();
			$subs = unserialize($forum->children);

			$positions[$key]['number'] = ($forum->forum_seq+1);
			$positions[$key]['label']  = '<i>'.__("Position After", "sforum").'</i>:  <b>'.sf_filter_title_display($forum->forum_name).'</b>';
			$list[] = ($forum->forum_seq+1);
			$key++;

			if($subs)
			{
				foreach($subs as $sub)
				{
					if ($sub != $id)
					{
						$subrecord = $wpdb->get_row("SELECT forum_name, forum_seq FROM ".SFFORUMS." WHERE forum_id=".$sub);
						if(!in_array(($subrecord->forum_seq+1), $list))
						{
							$positions[$key]['number'] = ($subrecord->forum_seq+1);
							$positions[$key]['label']  = '<i>'.__("Position After", "sforum").'</i>:  <b>'.sf_filter_title_display($subrecord->forum_name).'</b>';
							$list[] = ($subrecord->forum_seq+1);
							$key++;
						}
					}
				}
			}
		}
	}

	if(count($positions) == 0)
	{
		$positions[0]['number'] = 1;
		$positions[0]['label']  = __("Current Position", "sforum");
		$current = 1;
	}

	$out = '<table class="form-table table-cbox"><tr>';
	$out.= "<td width='100%' class='td-cbox'>\n";

	$key = 100;

	foreach($positions as $seq)
	{
		$check = '';
		if($current == $seq['number']) $check = ' checked="checked" ';
		$out.= '<label for="sfradio-'.$key.'" class="sflabel radio">'.$seq['label'].'</label>'."\n";
		$out.= '<input type="radio" class="radiosequence" name="forum_seq" id="sfradio-'.$key.'" value="'.$seq['number'].'" '.$check.' />'."\n";
		$key++;
	}
	$out.= "</td></tr></table>";

	return $out;
}

?>