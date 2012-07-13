<?php
/*
Simple:Press
Admin Forums Main Display
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_forums_forums_main()
{
	$groups = sf_get_groups_all();
	if ($groups)
	{
		foreach ($groups as $group)
		{
			# display the current group information in table format
?>
			<table class="sfmaintable" cellpadding="0" cellspacing="0">
				<tr> <!-- display group table header information -->
					<th align="center" width="40"><?php _e("Icon", "sforum"); ?></th>
					<th align="center" width="31"><?php _e("ID", "sforum"); ?></th>
					<th scope="col"><?php _e("Group Name", "sforum") ?></th>
					<th align="center" width="20" scope="col"></th>
					<th align="center" width="30%" scope="col"></th>
				</tr>
				<tr> <!-- display group information for each group -->
<?php
					if(empty($group->group_icon))
					{
						$icon = SFRESOURCES.'group.png';
					} else {
						$icon = esc_url(SFCUSTOMURL.$group->group_icon);
						if (!file_exists(SFCUSTOM.$group->group_icon))
						{
							$icon = SFRESOURCES.'group.png';
						}
					}
?>
					<td align="center">
<?php
						echo '<img src="'.$icon.'" alt="" title="'.__("Current Group Icon", "sforum").'" />';
?>
					</td>
					<td align="center"><?php echo($group->group_id); ?></td>
					<td><p><strong><?php echo(sf_filter_title_display($group->group_name)); ?></strong><br /><?php echo(sf_filter_text_display($group->group_desc)); ?></p></td>
					<td></td>
					<td align="center">
<?php
                        $base = SFHOMEURL."index.php?sf_ahah=forums-loader";
						$target = "group-".$group->group_id;
						$image = SFADMINIMAGES;
?>
						<input type="button" class="button button-highlighted" value="<?php echo splice(esc_attr(__("Add Group Permission", "sforum")),1,0); ?>" onclick="sfjLoadForm('groupperm', '<?php echo($base); ?>', '<?php echo($target); ?>', '<?php echo($image); ?>', '<?php echo($group->group_id); ?>');" />
						<input type="button" class="button button-highlighted" value="<?php echo splice(esc_attr(__("Edit Group", "sforum")),0,0); ?>" onclick="sfjLoadForm('editgroup', '<?php echo($base); ?>', '<?php echo($target); ?>', '<?php echo($image); ?>', '<?php echo($group->group_id); ?>');" />
						<input type="button" class="button button-highlighted" value="<?php echo splice(esc_attr(__("Delete Group", "sforum")),0,0); ?>" onclick="sfjLoadForm('deletegroup', '<?php echo($base); ?>', '<?php echo($target); ?>', '<?php echo($image); ?>', '<?php echo($group->group_id); ?>');" />
					</td>
				</tr>
				<tr>  <!-- This row will hold ahah forms for the current group -->
				  	<td class="sfinline-form" colspan="10">
						<div id="group-<?php echo $group->group_id; ?>">
						</div>
					</td>
				</tr>
			</table>
<?php
			$forums = sfa_get_forums_in_group($group->group_id);
			if ($forums)
			{
				# display the current forum information for each forum in table format
?>
				<table  class="sfsubtable" cellpadding="0" cellspacing="0">
					<tr> <!-- display forum table header information -->
						<th align="center" width="40"></th>
						<th align="center" width="31"><?php _e("ID", "sforum"); ?></th>
						<th scope="col"><?php _e("Forum Name", "sforum") ?></th>
						<th align="center" width="20" scope="col"></th>
						<th align="center" width="30%" scope="col"></th>
					</tr>
<?php
					sfa_paint_group_forums($group->group_id, 0, '', 0);
?>
				</table>
				<br /><br />
<?php
			} else {
				echo('<div class="sfempty">&nbsp;&nbsp;&nbsp;&nbsp;'.__("There are No Forums defined in this Group", "sforum").'</div>');
			}
		}
	} else {
		echo('<div class="sfempty">&nbsp;&nbsp;&nbsp;&nbsp;'.__("There are No Groups defined", "sforum").'</div>');
	}
	return;
}



function sfa_paint_group_forums($groupid, $parent, $parentname, $level)
{
	$space = '<img class="sfalignleft" src="'.SFADMINIMAGES.'subforum-level.png" alt="" />';
	$forums = sfa_get_group_forums_by_parent($groupid, $parent);

	if($forums)
	{
		foreach ($forums as $forum)
		{
			$locked = '';
			if ($forum->forum_status) $locked=__("Locked", "sforum");
			$subforum = $forum->parent;

			$haschild = '';
			if($forum->children)
			{
				$childlist = array(unserialize($forum->children));
				if(count($childlist) > 0)
				{
					$haschild=$childlist;
				}
			}
	?>
			<tr> <!-- display forum information for each forum -->
	<?php
			if(empty($forum->forum_icon))
			{
				$icon = SFRESOURCES.'forum.png';
			} else {
				$icon = esc_url(SFCUSTOMURL.$forum->forum_icon);
				if (!file_exists(SFCUSTOM.$forum->forum_icon))
				{
					$icon = SFRESOURCES.'forum.png';
				}
			}
	?>
			<td align="center">
	<?php
			echo '<img src="'.$icon.'" alt="" title="'.esc_attr(__("Current Forum Icon", "sforum")).'" />';
	?>
			</td>
			<td align="center"><?php echo($forum->forum_id); ?></td>

	<?php
			if($subforum) { ?>
				<td>
				<?php echo str_repeat($space, ($level-1)); ?>
				<img class="sfalignleft" src="<?php echo SFADMINIMAGES.'subforum.png'; ?>" alt="" title="<?php esc_attr_e(__("Subforum", "sforum")); ?>" />
				<p><strong><?php echo sf_filter_title_display($forum->forum_name).'</strong> ('.__("Subforum of", "sforum").': '.$parentname.')'; ?>
	<?php	} else { ?>
				<td><p><strong><?php echo(sf_filter_title_display($forum->forum_name)); ?></strong>
	<?php	} ?>
			<br />
			<p><?php echo(sf_filter_text_display($forum->forum_desc)); ?></p>
	<?php
			if($haschild) { ?>
				<img src="<?php echo SFADMINIMAGES.'haschild.png'; ?>" alt="" title="<?php esc_attr_e(__("Parent Forum", "sforum")); ?>" />
	<?php	} ?>

			</td>
			<td align="center"><?php if ($forum->forum_status) echo('<img src="'.SFADMINIMAGES.'locked.png" alt="" />'); ?></td>

			<td align="center">
	<?php
            $base = SFHOMEURL."index.php?sf_ahah=forums-loader";
			$target = "forum-".$forum->forum_id;
			$image = SFADMINIMAGES;
	?>
			<input id="sfreloadpb<?php echo($forum->forum_id); ?>" type="button" class="button button-highlighted" value="<?php echo splice(__("View Forum Permissions", "sforum"),1,0); ?>" onclick="sfjLoadForm('forumperm', '<?php echo($base); ?>', '<?php echo($target); ?>', '<?php echo($image); ?>', '<?php echo($forum->forum_id); ?>');" />
			<input type="button" class="button button-highlighted" value="<?php echo splice(esc_attr(__("Edit Forum", "sforum")),0,0); ?>" onclick="sfjLoadForm('editforum', '<?php echo($base); ?>', '<?php echo($target); ?>', '<?php echo($image); ?>', '<?php echo($forum->forum_id); ?>');" />
			<input type="button" class="button button-highlighted" value="<?php echo splice(esc_attr(__("Delete Forum", "sforum")),0,0); ?>" onclick="sfjLoadForm('deleteforum', '<?php echo($base); ?>', '<?php echo($target); ?>', '<?php echo($image); ?>', '<?php echo($forum->forum_id); ?>');" />

			</td>
			</tr>
			<tr>  <!-- This row will hold ahah forms for the current forum -->
			<td class="sfinline-form" colspan="10">
			<div id="forum-<?php echo $forum->forum_id; ?>">
			</div>
			</td>
			</tr>
	<?php
			if($haschild)
			{
				$newlevel = $level+1;
				sfa_paint_group_forums($groupid, $forum->forum_id, $forum->forum_name, $newlevel);
			}
		}
	}
}

?>