<?php

/**
 * nycga_group_files_group_admin_nav()
 *
 * This function adds the "Documents" tab within each group administrators' "admin" tab
 */
function nycga_group_files_group_admin_nav() {
	global $bp;

	/* Add the "documents" option to the group moderator "Admin" tab */
	add_action( 'groups_admin_tabs', create_function( '$current, $group_slug', 'if ( "' . esc_attr( $bp->group_files->slug ) . '" == $current ){ $selected="class=\"current\""; }else{$selected="";} echo "<li $selected ><a href=\"' . $bp->root_domain . '/' . $bp->groups->slug . '/{$group_slug}/admin/' . esc_attr( $bp->group_files->slug ) . '\">' . __('Files','nycga-group-files') . '</a></li>";' ), 10, 2 );

	do_action('nycga_group_files_group_admin_nav');
}
add_action( 'bp_setup_nav', 'nycga_group_files_group_admin_nav', 2 );
add_action( 'network_admin_menu', 'nycga_group_files_group_admin_nav', 2 );


/*
 * nycga_group_files_group_admin_setting()
 * 
 * This function catches the URL for the Group Administrator screen
 * it will then check to see if we're looking at group docs admin, 
 * and check for updates and display options accordingly
 */
function nycga_group_files_group_admin_settings() {
	global $bp;

	//only continue of we're viewing Group Docs Settings
	if( function_exists('bp_is_group_admin_screen') && bp_is_group_admin_screen($bp->group_files->slug)) {

		if ( !$bp->is_item_admin )
			return false;

		// If the edit form has been submitted, save the edited details
		nycga_group_files_group_admin_save();

		//add a hook to show the options
		add_action('groups_custom_edit_steps','nycga_group_files_group_admin_edit');

		if ( ('1.1' == substr(BP_VERSION,0,3)) && ('' != locate_template( array( 'groups/single/admin.php' ), false )) ) {
			bp_core_load_template( apply_filters( 'groups_template_group_admin_settings', 'groups/single/admin' ) );
		} elseif ( '' != locate_template( array( 'groups/single/home.php' ), false ) ) {
			bp_core_load_template( apply_filters( 'groups_template_group_home', 'groups/single/home' ) );
		} else {
			bp_core_load_template( apply_filters( 'groups_template_group_admin_settings', 'groups/single/admin' ) );
		}
	}
}
add_action( 'wp', 'nycga_group_files_group_admin_settings', 4 );


/**
 * nycga_group_files_group_mod_menu()
 *
 * This function catches the url for the moderators' "group admin" screen.
 * It will check for changes via the nycga_group_files_group_admin_save() function,
 * and then call the display template.
 */
function nycga_group_files_group_network_admin_menu() {

	//catch the '/groupname/admin/documents' url
	if( bp_is_group_admin_screen('files')) {
		die('and here');

		//check if the user is submitting a form, process if neccessary
		nycga_group_files_group_admin_save();

		//load the display template
		if ( '' != locate_template( array( 'groups/single/home.php' ), false ) ) {
			bp_core_load_template( apply_filters( 'groups_template_group_home', 'groups/single/home' ) );
		} else {
			add_action( 'bp_template_content_header','nycga_group_files_group_admin_header');
			add_action( 'bp_template_content', 'nycga_group_files_group_admin_edit' );
			bp_core_load_template( apply_filters( 'bp_core_template_plugin', '/groups/single/plugins' ) );
		}

	}
}
//add_action('plugins_loaded','nycga_group_files_group_network_admin_menu');


/*
 * nycga_group_files_group_admin_edit()
 *
 * displays the options for the group moderator
 * options may vary depending on site admin options
 */
function nycga_group_files_group_admin_edit() {
	global $bp;

	//useful ur for submits & links
	$action_link = get_bloginfo('url') . '/' . $bp->current_component . '/' . $bp->current_item . '/' . $bp->current_action . '/' . $bp->group_files->slug;

	//only show enable/disable if the site admin allows this to be changed at group-level
	if( !get_option( 'nycga_group_files_enable_all_groups' ) ) {
		//we use 'disabled' rather than 'enabled' because it will not be set by default. (enabled by default)
		//this way we don't need to distinguish between not set or false, which could be problematic.
		$documents_disabled = groups_get_groupmeta( $bp->groups->current_group->id, 'group_files_documents_disabled' ); ?>
		<p><label><?php _e('Enable files for this group:','nycga-group-files'); ?></label>
		<input type="radio" name="group_files_documents_disabled" value="0" <?php if( !$documents_disabled ) echo 'checked="checked"' ?> /><?php _e('Yes','nycga-group-files') ?><br /> 
		<input type="radio" name="group_files_documents_disabled" value="1" <?php if( $documents_disabled ) echo 'checked="checked"' ?> /><?php _e('No','nycga-group-files') ?>
		</p>

<?php	}

	//only show the upload persmissions if the site admin allows this to be changed at group-level
	if( 'mods_decide' == get_option( 'nycga_group_files_upload_permission' ) ) {
		$upload_permission = groups_get_groupmeta( $bp->groups->current_group->id, 'group_files_upload_permission'); ?>
		<p><label><?php _e('Document Upload Permissions:','nycga-group-files'); ?></label>
		<input type="radio" name="group_files_upload_permission" value="members" <?php if( 'members' == $upload_permission ) echo 'checked="checked"'; ?> /><?php _e('All Group Members','nycga-group-files'); ?><br />
		<input type="radio" name="group_files_upload_permission" value="mods_only" <?php if( 'mods_only' == $upload_permission ) echo 'checked="checked"'; ?> /><?php _e('Only Group Moderators','nycga-group-files'); ?>
		</p>
	<?php }

	//only show categories if site admin chooses to
	if( get_option('nycga_group_files_use_categories')) {
		$parent_id = NYCGA_Group_Files_Template::get_parent_category_id();
		$group_categories = get_terms( 'group-files-category', array('parent'=>$parent_id,'hide_empty'=>false ) ); ?>

		<div id="group-files-group-admin-categories">
		<label><?php _e('File Category List:','nycga-group-files'); ?></label>
		<div>
			<ul>
			<?php foreach( $group_categories as $category ) {
				if( isset( $_GET['edit'] ) && ( $_GET['edit'] == $category->term_id ) ) { ?>
					<li id="category-<?php echo $category->term_id; ?>"><input type="text" name="group_files_category_edit" value="<?php echo $category->name; ?>" />
					<input type="hidden" name="group_files_category_edit_id" value="<?php echo $category->term_id; ?>" />
					<input type="submit" value="Update" /></li>
				<?php } else {
				$edit_link = wp_nonce_url($action_link . '?edit=' . $category->term_id,'group-files-category-edit');
				$delete_link = wp_nonce_url($action_link . '?delete=' . $category->term_id,'group-files-category-delete');
					 ?>
				<li id="category-<?php echo $category->term_id; ?>"><?php echo $category->name; ?>
				 &nbsp; <a class="group-files-category-edit" href="<?php echo $edit_link; ?>">Edit</a>
				  | <a class="group-files-category-delete" href="<?php echo $delete_link; ?>">Delete</a></li>
				<?php } ?>
			<?php } ?>
				<li><input type="text" name="nycga_group_files_new_category" class="nycga-group-files-new-category" />
				<input type="submit" value="Add" /></li>
			</ul>
		</div>
		</div><!-- #group-documents-group-admin-categories -->
	<?php } ?>

	<p><input type="submit" value="<?php _e( 'Save Changes', 'buddypress' ) ?> &rarr;" id="save" name="save" /></p>
	<?php wp_nonce_field( 'groups-edit-group-files' ) ?>

<?php do_action('nycga_group_files_group_admin_edit');
}


/*
 * checks if modifications have been sumbitted, and processes the result
 * Outputs a message on success, fails silently.
 */
function nycga_group_files_group_admin_save() {
	global $bp;
	
	do_action('nycga_group_files_group_admin_save');

	$success = false;

	//check if category was updated
	if( $_POST['group_files_category_edit'] &&
		ctype_digit( $_POST['group_files_category_edit_id'] ) &&
		term_exists( (int)$_POST['group_files_category_edit_id'], 'group-files-category') ) {

		check_admin_referer('groups-edit-group-files');
	
		$success = wp_update_term( (int)$_POST['group_files_category_edit_id'], 'group-files-category',
			array('name'=>$_POST['group_files_category_edit']));
	}


	//check if category was deleted
	if( isset($_GET['delete'] ) && 
		ctype_digit( $_GET['delete']) && 
		term_exists( (int)$_GET['delete'],'group-files-category' ) ) {

		check_admin_referer('group-files-category-delete');

		$success = wp_delete_term( (int)$_GET['delete'], 'group-files-category');
	}


	//check if new category was added, if so, append to current list
	if( $_POST['nycga_group_files_new_category'] ) {

		$parent_id = NYCGA_Group_Files_Template::get_parent_category_id();

		if( !term_exists( $_POST['nycga_group_files_new_category'], 'group-files-category',$parent_id ) )
			$success = wp_insert_term( $_POST['nycga_group_files_new_category'],'group-files-category',array('parent'=>$parent_id));
	}

	//Update whether documents are enabled
	if( isset( $_POST['group_files_documents_disabled'] ) && ctype_digit( $_POST['group_files_documents_disabled'] ) ) {
		check_admin_referer( 'groups-edit-group-documents' );
		$success = groups_update_groupmeta( $bp->groups->current_group->id, 'group_files_documents_disabled', $_POST['group_files_documents_disabled'] );
	}

	//Update permissions
	$valid_permissions = array( 'members','mods_only' );
	if( isset( $_POST['group_files_upload_permission'] ) && in_array( $_POST['group_files_upload_permission'], $valid_permissions )) {

		check_admin_referer( 'groups-edit-group-files' );

		$success = $success || groups_update_groupmeta( $bp->groups->current_group->id, 'group_files_upload_permission', $_POST['group_files_upload_permission']);

	}

	//If something was updated, post a success 
	if ( $success !== false ) {
		bp_core_add_message( __( 'Settings saved successfully', 'buddypress' ) );
		bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . '/admin/' . $bp->group_files->slug );
	}
}

