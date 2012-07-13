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
		global $_wp_nav_menu_max_depth, $bp, $groups_template;
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

		$classes = array(
			'menu-item menu-item-depth-' . $depth,
			'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
		);

		/** Set global $groups_template to current group so we can use BP groups template functions */
		$groups_template->group = $item;

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
					<span class="item-title"><?php bp_group_avatar_micro() ?> <?php echo esc_html( stripslashes( $title ) ); ?></span>
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
						<input type="text" id="group-name-<?php echo $item_id; ?>" class="widefat edit-menu-item-title" name="group[<?php echo $item_id; ?>][name]" value="<?php echo esc_attr( stripslashes( $item->name ) ); ?>" />
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
						<textarea id="group-description-<?php echo $item_id; ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="group[<?php echo $item_id; ?>][description]"><?php echo esc_textarea( stripslashes( $item->description ) ); // textarea_escaped ?></textarea>
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
							<option value="<?php echo $group->id ?>" <?php selected($item->parent_id,$group->id) ?>><?php echo esc_html( stripslashes( $group->name ) ) ?> (<?php echo $group->slug ?>)</option>
							<?php endforeach; ?>
						</select>
					</label>
				</p>
				<?php endif; ?>
				
				<?php do_action( 'bp_group_organizer_display_group_options' , $item ); ?>
				
				<div class="menu-item-actions description-wide submitbox">
					<p class="link-to-original">
						<?php printf( __( 'Link: %s', 'bp-group-organizer' ), '<a href="' . bp_get_group_permalink() . '">' . esc_html( stripslashes( $item->name ) ) . '</a>' ); ?>
					</p>
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