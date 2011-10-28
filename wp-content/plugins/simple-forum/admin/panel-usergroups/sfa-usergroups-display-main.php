<?php
/*
Simple:Press
Admin User Groups Main Display
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_usergroups_usergroup_main()
{
	$usergroups = sfa_get_usergroups_all(Null);
	if($usergroups)
	{
		foreach ($usergroups as $usergroup)
		{
			# display the current usergroup information in table format
?>
			<table class="sfmaintable" cellpadding="0" cellspacing="0">
				<tr>
					<th align="center" width="7%" scope="col"><?php _e("User Group ID", "sforum") ?></th>
					<th scope="col"><?php _e("User Group Name", "sforum") ?></th>
					<th align="center" width="8%" scope="col"><?php _e("Moderator", "sforum") ?></th>
					<th align="center" width="15%" scope="col"></th>
					<th align="center" width="15%" scope="col"></th>
				</tr>
				<tr>
					<td align="center"><?php echo($usergroup->usergroup_id); ?></td>
					<td><strong><?php echo(sf_filter_title_display($usergroup->usergroup_name)); ?></strong><br /><small><?php echo(sf_filter_title_display($usergroup->usergroup_desc)); ?></small></td>
					<td align="center"><?php if ($usergroup->usergroup_is_moderator == 1) echo _e("Yes", "sforum"); else echo _e("No", "sforum"); ?></td>
					<td align="right">
<?php
                        $base = SFHOMEURL."index.php?sf_ahah=usergroups-loader";
						$target = "usergroup-".$usergroup->usergroup_id;
						$image = SFADMINIMAGES;
?>
						<input type="button" class="button button-highlighted" value="<?php echo splice(esc_attr(__("Edit User Group", "sforum")),0,0); ?>" onclick="sfjLoadForm('editusergroup', '<?php echo($base); ?>', '<?php echo($target); ?>', '<?php echo($image); ?>', '<?php echo($usergroup->usergroup_id); ?>');" />
					</td>
					<td>
						<input type="button" class="button button-highlighted" value="<?php echo splice(esc_attr(__("Delete User Group", "sforum")),0,0); ?>" onclick="sfjLoadForm('delusergroup', '<?php echo($base); ?>', '<?php echo($target); ?>', '<?php echo($image); ?>', '<?php echo($usergroup->usergroup_id); ?>');" />
					</td>
				</tr>
				<tr> <!-- This row will hold ahah forms for the current user group -->
				  	<td class="sfinline-form" colspan="5">
						<div id="usergroup-<?php echo $usergroup->usergroup_id; ?>">
						</div>
					</td>
				</tr>
				<tr class="sfsubtable sfugrouptable">
					<td align="center" valign="top"><small><?php _e("Members<br />in this<br />User Group:", "sforum") ?></small></td>
					<td valign="top" colspan="2">
<?php
                        $site = SFHOMEURL."index.php?sf_ahah=usergroups&amp;ug=".$usergroup->usergroup_id;
						$gif= SFADMINIMAGES."working.gif";
						$text = esc_js(__("Show/Hide Members", "sforum"));
						?>
						<input type="button" id="show<?php echo($usergroup->usergroup_id); ?>" class="button button-highlighted" value="<?php echo($text); ?>" onclick="sfjshowMemberList('<?php echo($site); ?>', '<?php echo($gif); ?>', '<?php echo($usergroup->usergroup_id); ?>');" />
					</td>
					<td colspan="2" align="center" valign="top">
<?php
                        $base = SFHOMEURL."index.php?sf_ahah=usergroups-loader";
						$target = "members-".$usergroup->usergroup_id;
						$image = SFADMINIMAGES;
?>
						<input type="button" id="add<?php echo($usergroup->usergroup_id); ?>" class="button button-highlighted" value="<?php esc_attr_e(__('Add Members', 'sforum')); ?>" onclick="sfjLoadForm('addmembers', '<?php echo($base); ?>', '<?php echo($target); ?>', '<?php echo($image); ?>', '<?php echo($usergroup->usergroup_id); ?>'); " />
						<input type="button" id="remove<?php echo($usergroup->usergroup_id); ?>" class="button button-highlighted" value="<?php esc_attr_e(__('Move/Delete Members', 'sforum')); ?>" onclick="sfjLoadForm('delmembers', '<?php echo($base); ?>', '<?php echo($target); ?>', '<?php echo($image); ?>', '<?php echo($usergroup->usergroup_id); ?>'); " />
					</td>
				</tr>
				<tr> <!-- This row will hold hidden forms for the current user group membership-->
				  	<td class="sfinline-form" colspan="5">
                        <div id="members-<?php echo $usergroup->usergroup_id; ?>"></div>
					</td>
				</tr>
			</table>
<?php 	}
	} else {
		echo('<div class="sfempty">&nbsp;&nbsp;&nbsp;&nbsp;'.__("There are no User Groups defined", "sforum").'</div>');
	}
	return;
}
?>