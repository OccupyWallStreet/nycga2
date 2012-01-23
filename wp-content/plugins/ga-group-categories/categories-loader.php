<?php
class GA_Categories extends BP_Group_Extension {
	var $group_categories = false;
	var $nav_item_position = 16;
	var $visibility = true;	
	var $enable_edit_item = true;
	var $display_hook = 'groups_statustab_group_boxes';
	var $template_file = 'groups/single/plugins';
	var $enable_create_step = false;
	function GA_Categories(){
		global $bp;
		$this->name = 'GA Categories';
		$this->slug = 'categories';
		$this->create_step_position = 21;
                $this->enable_edit_item = current_user_can('manage_options');
		// In Admin
		$this->name = "Category";
	}
	function create_screen() {
	}
	function create_screen_save(){
	}
	
	// Admin area
	function edit_screen() {
		global $bp;
		$type = groups_get_groupmeta($bp->groups->current_group->id, 'category');
		echo '<label>Group Category</label>';
		echo '<select name="group-category" id="group-category">';
		echo '<option value="uncategorized">Uncategorized</option>';
		echo '<option value="operations" '. ($type=='operations' ? 'selected="selected"':'') . '>Operations Group</option>';
		echo '<option value="caucus" '. ($type=='caucus' ? 'selected="selected"':'') . '>Caucus</option>';
		echo '</select>';
		echo '<p><input type="submit" name="save_category" id="save" value="Save"></p>';
		wp_nonce_field('groups_edit_group_categories');

	}
	// save all changes into DB
	function edit_screen_save() {
		global $bp;
		if ( $bp->current_component == $bp->groups->slug && 'categories' == $bp->action_variables[0] ) {
			if ( !$bp->is_item_admin )
				return false;
			// Save general settings
			if ( isset($_POST['save_category'])){
				/* Check the nonce first. */
				if ( !check_admin_referer( 'groups_edit_group_categories' ) )
					return false;
				
				$meta = $_POST['group-category'];
				
				// Save into groupmeta table
				groups_update_groupmeta( $bp->groups->current_group->id, 'category', $meta );
				bp_core_add_message(__('Group category saved:' . $meta['category'],'group-categories'));
				
				bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/' );
			}
		}
	}	
	
}
bp_register_group_extension('GA_Categories');

add_action('bp_groups_directory_group_filter', 'add_category_tabs');
function add_category_tabs(){
?>
					
					<li id="groups-cat_operations"><a class="hoverhand" <?php /*href="<?php echo trailingslashit( bp_get_root_domain() . '/groups/categories/operations/' ); ?>" */ ?>><?php printf( __( 'Operations Groups <span>%s</span>', 'buddypress' ), bp_get_category_group_count('operations') ); ?></a></li>

					<li id="groups-cat_caucus"><a class="hoverhand" <?php /*href="<?php echo trailingslashit( bp_get_root_domain() . '/groups/categories/caucus/' ); ?>" */ ?>><?php printf( __( 'Caucuses <span>%s</span>', 'buddypress' ), bp_get_category_group_count('caucus') ); ?></a></li>

					<li id="groups-cat_uncategorized"><a class="hoverhand" <?php /*href="<?php echo trailingslashit( bp_get_root_domain() . '/groups/categories/uncategorized/' ); ?>" */ ?>><?php printf( __( 'Uncategorized <span>%s</span>', 'buddypress' ), bp_get_category_group_count('uncategorized') ); ?></a></li>

<?php
}
?>
