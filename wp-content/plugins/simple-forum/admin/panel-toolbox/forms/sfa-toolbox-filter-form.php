<?php
/*
Simple:Press
Admin Toolbox Filter Form
$LastChangedDate: 2010-04-25 01:46:39 -0700 (Sun, 25 Apr 2010) $
$Rev: 3960 $
*/

# function to display the form that allows admins to select filter criteria for topics before pruning
function sfa_toolbox_filter_form()
{
	global $wpdb;


?>
	<form action="<?php echo SFADMINTOOLBOX.'&amp;form=prune'; ?>" method="post" id="sffiltertopics" name="sffiltertopics">
	<?php echo(sfc_create_nonce('forum-adminform_filtertopics')); ?>
<?php

	sfa_paint_options_init();

	sfa_paint_open_tab(__("Toolbox", "sforum")." - ".__("Prune Database", "sforum"));
		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Filter Database Topics", "sforum"), 'true', 'select-topic-filter-date', false);

			# make sure we have some groups/forums/topics in order to be able to prune
			$groups = sfa_get_database();
			if ($groups)
			{
?>
					<table width="100%" cellpadding="2" cellspacing="3" border="0">
						<tr>
							<td valign="top" width="20%">
								<fieldset style="background:#eeeeee;" class="sffieldset"><legend><?php _e("Select Topic Filter Date", "sforum") ?></legend>
									<?php echo(sfa_paint_help('select-topic-filter-date', 'admin-toolbox')); ?>
								    <!-- Display a popup calendar for pruning date entry -->
									<p align="center">
									<input name="date" id="cal" type="text" class="sfpostcontrol" size="15" value="<?php echo date('M d Y'); ?>" />
									<a href="javascript:sfjNewCal('cal','MMMddyyyy')">
										<img src="<?php echo(SFADMINIMAGES); ?>cal.gif" width="16" height="16" border="0" alt="Pick a Filter Date" />
									</a>
									</p>
									<p><?php _e("Select Topic Filter Date Above.", "sforum"); ?></p>
									<p><?php _e("All topics prior to the date selected above will be available for pruning. If no date is specified, todays date will be used.", "sforum") ?></p>
								</fieldset>
							</td>
							<td></td>
						</tr>
						<tr>
							<td colspan="2" valign="top">
								<div class="sfform-panel-spacer"></div>
								<fieldset style="width:95%" class="sffieldset"><legend><?php _e("Select Group(s) / Forum(s) To Prune", "sforum") ?></legend>
									<?php echo(sfa_paint_help('select-group-forum-to-prune', 'admin-database')); ?>
<?php
									$gcount = 0;
									foreach ($groups as $group)
									{
										# display separate fieldset for each group and forum within that group
?>
										<fieldset style="margin-left:15px;width:95%" class="sffieldset"><legend><?php echo sf_filter_title_display($group['group_name']); ?></legend><br />
										<div id="container<?php echo($group['group_id']); ?>">
										<table  class="sfsubtable" cellpadding="0" cellspacing="0">
										  	<tr>
										  		<th width="5%" align="center"><?php _e("Filter", "sforum") ?></th>
										  		<th><?php _e("Forum Name", "sforum") ?></th>
										  		<th width="10%" align="center"><?php _e("Topic Count", "sforum") ?></th>
										  		<th width="20%" align="center"><?php _e("Earliest Topic", "sforum") ?></th>
										  		<th width="20%" align="center"><?php _e("Latest Topic", "sforum") ?></th>
										  	</tr>
<?php
										if ($group['forums'])
										{
											$fcount = 0;
											foreach($group['forums'] as $forum)
											{
												$id = 'group'.$gcount.'forum';
?>
												<tr>
													<td class="sflabel" colspan="2">
														<label for="sf<?php echo($id.$fcount); ?>"><?php echo sf_filter_title_display($forum['forum_name']); ?></label>
														<input type="checkbox" name="<?php echo $id.$fcount; ?>" id="sf<?php echo $id.$fcount; ?>" value="<?php echo $forum['forum_id']; ?>" />
													</td>
													<td align="center">
														<?php echo $forum['topic_count']; ?>
													</td>
													<td align="center">
<?php
														$date = $wpdb->get_var("SELECT topic_date FROM ".SFTOPICS." WHERE forum_id='".$forum['forum_id']."' ORDER BY topic_date ASC LIMIT 1");
														echo sf_date('d', $date);
?>
													</td>
													<td align="center">
<?php
														$date = $wpdb->get_var("SELECT topic_date FROM ".SFTOPICS." WHERE forum_id='".$forum['forum_id']."' ORDER BY topic_date DESC LIMIT 1");
														echo sf_date('d', $date);
?>
													</td>
												</tr>
<?php
												$fcount++;
											}
?>
											</table>
											</div>
<?php
											$checkcontainer = '#container'.$group['group_id'];
											echo '<br />';
?>
											<table>
											<tr>
											<td>

											<input type="button" class="button button-highlighted" value="<?php esc_attr_e(__('Check All', 'sforum')); ?>" onclick="sfjcheckAll(jQuery('<?php echo($checkcontainer); ?>'))" />

											</td>
											<td />
											<td>

											<input type="button" class="button button-highlighted" value="<?php esc_attr_e(__('Uncheck All', 'sforum')); ?>" onclick="sfjuncheckAll(jQuery('<?php echo($checkcontainer); ?>'))" />

											</td>
											</tr>
											</table>
<?php
										}
?>
										<input type="hidden" name="fcount[]" value="<?php echo($fcount); ?>" />
										</fieldset>
<?php
										$gcount++;
									}
?>
									<p><?php echo __('<strong>Warning:</strong>  The filtering process can be cpu intensive.  It is recommended to select a minimal number of forums (based on number of posts) to filter at once, especially if you are on shared hosting.', 'sforum'); ?></p>
								</fieldset>
							</td>
						</tr>
					</table>
<?php
			sfa_paint_close_fieldset(false);
		sfa_paint_close_panel();
	sfa_paint_close_tab();
?>
	<div class="sfform-submit-bar">
	<input type="hidden" name="gcount" value="<?php echo($gcount); ?>" />
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Filter Topics', 'sforum')); ?>" />
	</div>
	</form>
<?php
			} else {
				echo __("There is Nothing to Prune as there are no Topics.", "sforum");
			}
	return;
}

function sfa_get_database()
{
	global $wpdb;

	# retrieve group and forum records
	$records = $wpdb->get_results(
			"SELECT ".SFGROUPS.".group_id, group_name, forum_id, forum_name, topic_count
			 FROM ".SFGROUPS."
			 JOIN ".SFFORUMS." ON ".SFGROUPS.".group_id = ".SFFORUMS.".group_id
			 ORDER BY group_seq, forum_seq;");

	# rebuild into an array
	$groups=array();
	$gindex=-1;
	$findex=0;
	if($records)
	{
		foreach($records as $record)
		{
			$groupid=$record->group_id;
			$forumid=$record->forum_id;

			if($gindex == -1 || $groups[$gindex]['group_id'] != $groupid)
			{
				$gindex++;
				$findex=0;
				$groups[$gindex]['group_id']=$record->group_id;
				$groups[$gindex]['group_name']=$record->group_name;
			}
			if(isset($record->forum_id))
			{
				$groups[$gindex]['forums'][$findex]['forum_id']=$record->forum_id;
				$groups[$gindex]['forums'][$findex]['forum_name']=$record->forum_name;
				$groups[$gindex]['forums'][$findex]['topic_count']=$record->topic_count;
				$findex++;
			}
		}
	} else {
		$records = sf_get_groups_all(false, false);
		if($records)
		{
			foreach($records as $record)
			{
				$groups[$gindex]['group_id']=$record->group_id;
				$groups[$gindex]['group_name']=$record->group_name;
				$groups[$gindex]['group_desc']=$record->group_desc;
				$gindex++;
			}
		}
	}
	return $groups;
}

?>