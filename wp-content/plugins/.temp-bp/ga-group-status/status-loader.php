<?php
class GA_Status extends BP_Group_Extension {
	var $nav_item_position = 17;
	var $enable_nav_item = false;
	function GA_Status(){
		global $bp;
		$this->name = 'GA Status';
		$this->slug = 'status';
                $this->enable_edit_item = current_user_can('manage_options');
		// In Admin
		$this->name = "Active Status";
	}
	function create_screen() {
	}
	function create_screen_save(){
		global $bp;
 
        check_admin_referer( 'groups_create_save_' . $this->slug );
 
        /* Save any details submitted here */
        groups_update_groupmeta( $bp->groups->new_group_id, 'activity_status', 'active' );
	}
	
	// Admin area
	function edit_screen() {
		global $bp;
		$status = groups_get_groupmeta($bp->groups->current_group->id, 'active_status');
		echo '<label>Group Status</label>';
		echo '<select name="group-status" id="group-status">';
		echo '<option value="active">Active	</option>';
		echo '<option value="inactive" '. ($status=='inactive' ? 'selected="selected"':'') . '>Inactive</option>';
		echo '</select>';
		echo '<p><input type="submit" name="save_status" id="save" value="Save"></p>';
		wp_nonce_field('groups_edit_group_status');

	}
	
	// save all changes into DB
	function edit_screen_save() {
		global $bp;
		if ( $bp->current_component == $bp->groups->slug && 'status' == $bp->action_variables[0] ) {
			if ( !$bp->is_item_admin )
				return false;
			
			// Save Status
			if ( isset($_POST['save_status'])){
				/* Check the nonce first. */
				if ( !check_admin_referer( 'groups_edit_group_status' ) )
					return false;
				
				$meta = $_POST['group-status'];
				
				// Save into groupmeta table
				groups_update_groupmeta( $bp->groups->current_group->id, 'active_status', $meta );
				bp_core_add_message(__('Group Status Saved: ' . groups_get_groupmeta($bp->groups->current_group->id, 'active_status')));
				
				bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/' );
			}
		}
	}	
	
}
bp_register_group_extension('GA_Status');
?>
