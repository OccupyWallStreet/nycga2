<?php
global $EM_Event;
$user_groups = array();
$group_data = groups_get_user_groups(get_current_user_id());
foreach( $group_data['groups'] as $group_id ){
	if( groups_is_user_admin(get_current_user_id(), $group_id) ){
		$user_groups[] = groups_get_group( array('group_id'=>$group_id)); 
	}
}
if( count($user_groups) > 0 ){ 
	?>
	<select name="group_id">
		<option value="<?php echo $BP_Group->id; ?>"><?php _e('Not a Group Event', 'dbem'); ?></option>
	<?php
	foreach($user_groups as $BP_Group){
		?>
		<option value="<?php echo $BP_Group->id; ?>" <?php echo ($BP_Group->id == $EM_Event->group_id) ? 'selected="selected"':''; ?>><?php echo $BP_Group->name; ?></option>
		<?php
	} 
	?>
	</select>
	<br />
	<em><?php _e ( 'Select a group you admin to attach this event to it. Note that all other admins of that group can modify the booking, and you will not be able to unattach the event without deleting it.', 'dbem' )?></em>
	<?php
}else{
	?><em><?php _e('No groups defined yet.','dbem'); ?></em><?php
}