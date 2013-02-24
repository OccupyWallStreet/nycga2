<?php

/**
 * Create HTML list of nav menu input items.
 *
 * @package WordPress
 * @since 3.0.0
 * @uses Walker_Nav_Menu
 */
class Walker_Group_Edit extends Walker_Group  {
	/**
	 * @see Walker_Nav_Menu::start_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference.
	 */
	function start_lvl(&$output) {}

	/**
	 * @see Walker_Nav_Menu::end_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference.
	 */
	function end_lvl(&$output) {
	}

	/**
	 * @see Walker::start_el()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param object $args
	 */
	function start_el(&$output, $item, $depth, $args) {
		global $_wp_nav_menu_max_depth, $bp;
		$_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		ob_start();
		$item_id = esc_attr( $item->id );
		$removed_args = array(
			'action',
			'customlink-tab',
			'edit-menu-item',
			'menu-item',
			'page-tab',
			'_wpnonce',
		);

		$original_title = '';

		$classes = array(
			'menu-item menu-item-depth-' . $depth,
			'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
		);

		$title = $item->name;

		if ( isset( $item->status ) && 'private' == $item->status ) {
			$classes[] = 'status-private';
			/* translators: %s: title of private group */
			$title = sprintf( __( '%s (Private)', 'bp-group-organizer' ), $title );
		} elseif ( isset( $item->status ) && 'hidden' == $item->status ) {
			$classes[] = 'status-hidden';
			/* translators: %s: title of hidden group */
			$title = sprintf( __('%s (Hidden)', 'bp-group-organizer' ), $title );
		}

		if(defined( 'BP_GROUP_HIERARCHY_IS_INSTALLED' ) && method_exists('BP_Groups_Hierarchy','get_tree')) {
			$all_groups = BP_Groups_Hierarchy::get_tree();
		}
		
		?>
		<li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode(' ', $classes ); ?>">
			<dl class="menu-item-bar">
				<dt class="menu-item-handle">
					<span class="item-title"><?php echo esc_html( $title ); ?></span>
					<span class="item-controls">
						<span class="item-type"><?php echo esc_html( $item->slug ); ?></span>
						<a class="item-edit" id="edit-<?php echo $item_id; ?>" title="<?php _e('Edit Group', 'bp-group-organizer' ); ?>" href="<?php
							echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );
						?>"><?php _e( 'Edit Group', 'bp-group-organizer' ); ?></a>
					</span>
				</dt>
			</dl>

			<div class="menu-item-settings" id="menu-item-settings-<?php echo $item_id; ?>">
				<p class="description description-thin">
					<label for="group-name-<?php echo $item_id; ?>">
						<?php _e( 'Group Name', 'bp-group-organizer' ); ?><br />
						<input type="text" id="group-name-<?php echo $item_id; ?>" class="widefat edit-menu-item-title" name="group[<?php echo $item_id; ?>][name]" value="<?php echo esc_attr( $item->name ); ?>" />
					</label>
				</p>
				<p class="description description-thin">
					<label for="group-slug-<?php echo $item_id; ?>">
						<?php _e( 'Group Slug', 'bp-group-organizer' ); ?><br />
						<input type="text" id="group-slug-<?php echo $item_id; ?>" class="widefat edit-menu-item-attr-title" name="group[<?php echo $item_id; ?>][slug]" value="<?php echo esc_attr( $item->slug ); ?>" />
					</label>
				</p>
				<p class="field-description description description-wide">
					<label for="group-description-<?php echo $item_id; ?>">
						<?php _e( 'Group Description', 'bp-group-organizer' ); ?><br />
						<textarea id="group-description-<?php echo $item_id; ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="group[<?php echo $item_id; ?>][description]"><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
						<span class="description"><?php _e('Enter a brief description for this group.'); ?></span>
					</label>
				</p>
				<?php if ( bp_is_active( 'forums' ) && ( function_exists( 'bp_forums_is_installed_correctly' ) && bp_forums_is_installed_correctly() ) ) : ?>
				<p class="field-css-classes description description-wide">
					<label for="group-forum-enabled-<?php echo $item_id; ?>">
						<?php _e('Enable discussion forum', 'buddypress'); ?><br />
						<input type="checkbox" id="group-forum-enabled-<?php echo $item_id; ?>" class="widefat edit-menu-item-classes" name="group[<?php echo $item_id; ?>][forum_enabled]" <?php checked($item->enable_forum) ?> />
					</label>
				</p>
				<?php endif; ?>
				<p class="field-link-target description description-thin">
					<label for="group-status-<?php echo $item_id; ?>">
						<?php _e( 'Privacy Options', 'buddypress' ); ?><br />
						<select id="group-status-<?php echo $item_id; ?>" class="widefat edit-menu-item-target" name="group[<?php echo $item_id; ?>][status]">
							<?php foreach($bp->groups->valid_status as $status) : ?>
							<option value="<?php echo $status ?>" <?php selected($item->status,$status) ?>><?php echo ucfirst($status) ?></option>
							<?php endforeach; ?>
						</select>
					</label>
				</p>
				
				<?php if(defined('BP_GROUP_HIERARCHY_IS_INSTALLED') && method_exists('BP_Groups_Hierarchy','get_tree')) : ?>
				<p class="field-xfn description description-thin">
					<label for="group-parent-id-<?php echo $item_id; ?>">
						<?php _e( 'Parent Group', 'bp-group-hierarchy' ); ?><br />
						<select id="group-parent-id-<?php echo $item_id; ?>" class="widefat edit-menu-item-target" name="group[<?php echo $item_id; ?>][parent_id]">
							<option value="0" <?php selected( $item->parent_id, 0) ?>><?php _e('Site Root','bp_group_hierarchy') ?></option>
							<?php foreach($all_groups as $group) : ?>
							<option value="<?php echo $group->id ?>" <?php selected($item->parent_id,$group->id) ?>><?php echo $group->name ?> (<?php echo $group->slug ?>)</option>
							<?php endforeach; ?>
						</select>
					</label>
				</p>
				<?php endif; ?>
				
				<?php do_action( 'bp_group_organizer_display_group_options' , $item ); ?>
				
				<div class="menu-item-actions description-wide submitbox">
					<?php if( $original_title !== false ) : ?>
						<p class="link-to-original">
							<?php printf( __('Original: %s'), '<a href="' . esc_attr( bp_get_group_permalink( $item ) ) . '">' . esc_html( $item->name ) . '</a>' ); ?>
						</p>
					<?php endif; ?>
					<a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php
					echo wp_nonce_url(
						add_query_arg(
							array(
								'action' => 'delete-group',
								'group_id' => $item_id,
							),
							remove_query_arg($removed_args, admin_url( 'admin.php?page=group_organizer' ) )
						),
						'delete-group_' . $item_id
					); ?>"><?php _e('Delete Group', 'bp-group-organizer'); ?></a> <span class="meta-sep"> | </span> <a class="item-cancel submitcancel" id="cancel-<?php echo $item_id; ?>" href="<?php	echo esc_url( add_query_arg( array('edit-menu-item' => $item_id, 'cancel' => time()), remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) ) ) );
						?>#menu-item-settings-<?php echo $item_id; ?>"><?php _e('Cancel'); ?></a>
				</div>

				<input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]" value="<?php echo $item_id; ?>" />
				<input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
				<input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
				<input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->parent_id ); ?>" />
				<input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
				<input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->type ); ?>" />
			</div><!-- .menu-item-settings-->
			<ul class="menu-item-transport"></ul>
		<?php
		$output .= ob_get_clean();
	}
}

/**
 * Register nav menu metaboxes and advanced menu items
 *
 * @since 3.0.0
 **/
function wp_nav_menu_setup() {
//	// Register meta boxes
//	if ( wp_get_nav_menus() )
//		add_meta_box( 'nav-menu-theme-locations', __( 'Theme Locations' ), 'wp_nav_menu_locations_meta_box' , 'nav-menus', 'side', 'default' );
//	add_meta_box( 'add-custom-links', __('Custom Links'), 'wp_nav_menu_item_link_meta_box', 'nav-menus', 'side', 'default' );
//	wp_nav_menu_post_type_meta_boxes();
//	wp_nav_menu_taxonomy_meta_boxes();
//
//	// Register advanced menu items (columns)
//	add_filter( 'manage_nav-menus_columns', 'wp_nav_menu_manage_columns');
//
//	// If first time editing, disable advanced items by default.
//	if( false === get_user_option( 'managenav-menuscolumnshidden' ) ) {
//		$user = wp_get_current_user();
//		update_user_option($user->ID, 'managenav-menuscolumnshidden',
//			array( 0 => 'link-target', 1 => 'css-classes', 2 => 'xfn', 3 => 'description', ),
//			true);
//	}
}

/**
 * Save posted nav menu item data.
 *
 * @since 3.0.0
 *
 * @param int $menu_id The menu ID for which to save this item. $menu_id of 0 makes a draft, orphaned menu item.
 * @param array $menu_data The unsanitized posted menu item data.
 * @return array The database IDs of the items saved
 */
function wp_save_nav_menu_items( $menu_id = 0, $menu_data = array() ) {
	$menu_id = (int) $menu_id;
	$items_saved = array();

	if ( 0 == $menu_id || is_nav_menu( $menu_id ) ) {

		// Loop through all the menu items' POST values
		foreach( (array) $menu_data as $_possible_db_id => $_item_object_data ) {
			if (
				empty( $_item_object_data['menu-item-object-id'] ) && // checkbox is not checked
				(
					! isset( $_item_object_data['menu-item-type'] ) || // and item type either isn't set
					in_array( $_item_object_data['menu-item-url'], array( 'http://', '' ) ) || // or URL is the default
					! ( 'custom' == $_item_object_data['menu-item-type'] && ! isset( $_item_object_data['menu-item-db-id'] ) ) ||  // or it's not a custom menu item (but not the custom home page)
					! empty( $_item_object_data['menu-item-db-id'] ) // or it *is* a custom menu item that already exists
				)
			) {
				continue; // then this potential menu item is not getting added to this menu
			}

			// if this possible menu item doesn't actually have a menu database ID yet
			if (
				empty( $_item_object_data['menu-item-db-id'] ) ||
				( 0 > $_possible_db_id ) ||
				$_possible_db_id != $_item_object_data['menu-item-db-id']
			) {
				$_actual_db_id = 0;
			} else {
				$_actual_db_id = (int) $_item_object_data['menu-item-db-id'];
			}

			$args = array(
				'menu-item-db-id' => ( isset( $_item_object_data['menu-item-db-id'] ) ? $_item_object_data['menu-item-db-id'] : '' ),
				'menu-item-object-id' => ( isset( $_item_object_data['menu-item-object-id'] ) ? $_item_object_data['menu-item-object-id'] : '' ),
				'menu-item-object' => ( isset( $_item_object_data['menu-item-object'] ) ? $_item_object_data['menu-item-object'] : '' ),
				'menu-item-parent-id' => ( isset( $_item_object_data['menu-item-parent-id'] ) ? $_item_object_data['menu-item-parent-id'] : '' ),
				'menu-item-position' => ( isset( $_item_object_data['menu-item-position'] ) ? $_item_object_data['menu-item-position'] : '' ),
				'menu-item-type' => ( isset( $_item_object_data['menu-item-type'] ) ? $_item_object_data['menu-item-type'] : '' ),
				'menu-item-title' => ( isset( $_item_object_data['menu-item-title'] ) ? $_item_object_data['menu-item-title'] : '' ),
				'menu-item-url' => ( isset( $_item_object_data['menu-item-url'] ) ? $_item_object_data['menu-item-url'] : '' ),
				'menu-item-description' => ( isset( $_item_object_data['menu-item-description'] ) ? $_item_object_data['menu-item-description'] : '' ),
				'menu-item-attr-title' => ( isset( $_item_object_data['menu-item-attr-title'] ) ? $_item_object_data['menu-item-attr-title'] : '' ),
				'menu-item-target' => ( isset( $_item_object_data['menu-item-target'] ) ? $_item_object_data['menu-item-target'] : '' ),
				'menu-item-classes' => ( isset( $_item_object_data['menu-item-classes'] ) ? $_item_object_data['menu-item-classes'] : '' ),
				'menu-item-xfn' => ( isset( $_item_object_data['menu-item-xfn'] ) ? $_item_object_data['menu-item-xfn'] : '' ),
			);

			$items_saved[] = wp_update_nav_menu_item( $menu_id, $_actual_db_id, $args );

		}
	}
	return $items_saved;
}

/**
 * Returns the menu formatted to edit.
 * 
 * @return string|WP_Error $output The menu formatted to edit or error object on failure.
 */
function bp_get_groups_to_edit() {

	$groups_list = groups_get_groups(array(
		'per_page'	=> 10000
	));
	
	if(defined( 'BP_GROUP_HIERARCHY_IS_INSTALLED' ) && method_exists('BP_Groups_Hierarchy','get_tree')) {
		$groups_list = array(
			'groups' => BP_Groups_Hierarchy::get_tree()
		);
		$groups_list['total'] = count($groups_list['groups']);
	} else if(floatval(BP_VERSION) > 1.3) {
		$groups_list = BP_Groups_Group::get('alphabetical');
	} else {
		$groups_list = BP_Groups_Group::get_alphabetically();
	}
	

	$result = '<div id="menu-instructions" class="post-body-plain';
	$result .= ( ! empty($menu_items) ) ? ' menu-instructions-inactive">' : '">';
	$result .= '<p>' . __('Add groups using the box to the left, or arrange groups below.', 'bp-group-organizer' ) . '</p>';
	$result .= '</div>';

	if($groups_list['total'] == 0)
		return $result . ' <ul class="menu" id="menu-to-edit"><li>' . __( 'No groups were found.', 'bp-group-organizer' ) . '</li></ul>';


	if($groups_list['total'] != 0) {

		$walker_class_name = apply_filters( 'wp_edit_group_walker', 'Walker_Group_Edit');

		if ( class_exists( $walker_class_name ) )
			$walker = new $walker_class_name;
		else
			return new WP_Error( 'menu_walker_not_exist', sprintf( __('The Walker class named <strong>%s</strong> does not exist.'), $walker_class_name ) );

		$result .= '<ul class="menu" id="menu-to-edit"> ';
		$result .= walk_group_tree( $groups_list['groups'], 0, (object) array('walker' => $walker ) );
		$result .= ' </ul> ';
		return $result;
	}
	
}

?>