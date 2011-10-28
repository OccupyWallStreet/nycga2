<?php
/*
Simple:Press
Admin Permissions Main Display
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_permissions_permission_main()
{
	global $sfactions;

	$roles = sfa_get_all_roles();
	if ($roles)
	{
		# display the permission set roles in table format
?>
		<table class="sfsubtable" cellpadding="0" cellspacing="0">
			<tr>
				<th align="center" width="9%" scope="col"><?php _e("Permission Set ID", "sforum") ?></th>
				<th scope="col"><?php _e("Permission Set Name", "sforum") ?></th>
				<th align="center" width="5%" scope="col"></th>
				<th align="center" width="15%" scope="col"></th>
			</tr>
<?php
			foreach($roles as $role)
			{
?>
			<tr>
				<td align="center"><?php echo($role->role_id); ?></td>
				<td><strong><?php echo(sf_filter_title_display($role->role_name)); ?></strong><br /><small><?php echo(sf_filter_title_display($role->role_desc)); ?></small></td>
				<td align="center">
<?php
                    $base = SFHOMEURL."index.php?sf_ahah=permissions-loader";
					$target = "perm-".$role->role_id;
					$image = SFADMINIMAGES;
?>
					<input type="button" class="button button-highlighted" value="<?php echo splice(esc_attr(__("Edit Permission", "sforum")),0,0); ?>" onclick="sfjLoadForm('editperm', '<?php echo($base); ?>', '<?php echo($target); ?>', '<?php echo($image); ?>', '<?php echo($role->role_id); ?>');" />
				</td>
				<td align="center">
					<input type="button" class="button button-highlighted" value="<?php echo splice(esc_attr(__("Delete Permission", "sforum")),0,0); ?>" onclick="sfjLoadForm('delperm', '<?php echo($base); ?>', '<?php echo($target); ?>', '<?php echo($image); ?>', '<?php echo($role->role_id); ?>');" />
				</td>
			</tr>
			<tr> <!-- This row will hold ahah forms for the current permission set -->
			  	<td class="sfinline-form" colspan="5">
					<div id="perm-<?php echo $role->role_id; ?>">
					</div>
				</td>
			</tr>
<?php	} ?>
		</table>
		<br />
<?php
	} else {
		echo('<div class="sfempty">&nbsp;&nbsp;&nbsp;&nbsp;'.__("There are no Permission Sets defined.", "sforum").'</div>');
	}
	return;
}

?>