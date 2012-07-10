<?php
/**
 * Admin Dashboard Hooks
 */

/**
 * Admin styles action
 */
function bp_links_admin_menu_css() {
	wp_enqueue_style( 'bp-links-admin-style', BP_LINKS_ADMIN_THEME_URL . '/style.css' );
}
add_action( 'admin_print_styles', 'bp_links_admin_menu_css' );

/**
 * Admin menus action
 */
function bp_links_add_admin_menu() {
	global $wpdb, $bp, $menu;

	if ( !is_site_admin() )
		return false;

	add_submenu_page( 'bp-general-settings', __( 'BuddyPress Links', 'buddypress-links'), '<span class="buddypress-links-admin-menu-header">' . __( 'BuddyPress Links', 'buddypress-links' ) . '&nbsp;&nbsp;&nbsp;</span>', 'manage_options', 'buddypress-links-admin', 'bp_links_admin_index' );
	add_submenu_page( 'bp-general-settings', __( 'Manage Links', 'buddypress-links'), '<span class="buddypress-links-admin-menu-item">&middot; ' . __( 'Manage Links', 'buddypress-links' ) . '</span>', 'manage_options', 'buddypress-links-admin-links', 'bp_links_admin_manage_links' );
	add_submenu_page( 'bp-general-settings', __( 'Manage Categories', 'buddypress-links'), '<span class="buddypress-links-admin-menu-item">&middot; ' . __( 'Edit Categories', 'buddypress-links' ) . '</span>', 'manage_options', 'buddypress-links-admin-cats', 'bp_links_admin_manage_categories' );
}
add_action( 'admin_menu', 'bp_links_add_admin_menu', 12 );

/**
 * Admin index action
 */
function bp_links_admin_index () {
	require_once BP_LINKS_ADMIN_THEME_DIR . '/index.php';
}

/**
 * Admin manage all links action
 * 
 * @return boolean
 */
function bp_links_admin_manage_links() {

	if ( isset( $_POST['links_admin_delete']) && isset( $_POST['alllinks'] ) ) {
		if ( !check_admin_referer('bp-links-admin') )
			return false;

		$errors = false;
		foreach ( $_POST['alllinks'] as $link_id ) {
			if ( !bp_links_delete_link( $link_id ) ) {
				$errors = true;
			}
		}

		if ( $errors ) {
			$message = __( 'There were errors when deleting links.', 'buddypress-links' ) . ' ' . __( 'Please try again.', 'buddypress-links' );
			$type = 'error';
		} else {
			$message = __( 'Links deleted successfully', 'buddypress-links' );
			$type = 'updated';
		}
	}

	require_once BP_LINKS_ADMIN_THEME_DIR . '/link-manager.php';

	return true;
}

/**
 * Admin manage categories action
 *
 * @return boolean
 */
function bp_links_admin_manage_categories() {

	if ( !current_user_can('manage_categories') ) {
		wp_die( __( 'You do not have sufficient permissions to edit link categories for this blog.', 'buddypress-links' ) );
	}

	if ( isset( $_GET['category_id'] ) || isset( $_POST['category_id'] )) {
		return bp_links_admin_edit_category();
	} else {
		return bp_links_admin_list_categories();
	}
}

/**
 * Admin list categories action
 *
 * @return boolean
 */
function bp_links_admin_list_categories() {

	if ( isset( $_POST['categories_admin_delete']) && isset( $_POST['allcategories'] ) ) {

		if ( !check_admin_referer('bp-links-categories-admin') ) {
			return false;
		}

		foreach ( $_POST['allcategories'] as $category_id ) {
			$category = new BP_Links_Category( $category_id );

			if ( $category->get_link_count($category_id) == 0 ) {
				if ( $category->delete() ) {
					$message = __( 'Categories deleted successfully', 'buddypress-links' );
					$message_type = 'updated';
				} else {
					$message = sprintf( '%s %s', __( 'There were errors when deleting categories.', 'buddypress-links' ), __( 'Please try again.', 'buddypress-links' ) );
					$message_type = 'error';
				}
			} else {
				$message = __( 'Unable to delete a category because it is assigned to one or more links', 'buddypress-links' );
				$message_type = 'error';
				break;
			}
		}
	}

	require_once( BP_LINKS_ADMIN_THEME_DIR . '/category-list.php');
	
	return true;
}

/**
 * Admin edit category action
 *
 * @return boolean
 */
function bp_links_admin_edit_category() {

	// error handling
	$error = false;

	// input value defaults
	$category_id = null;
	$category_name = null;
	$category_description = null;
	$category_priority = null;

	//
	// Handle create/update
	//
	if ( isset($_POST['category_id']) ) {

		if ( is_numeric( $_POST['category_id'] ) ) {
			// edit the category
			$category_id = (int) $_POST['category_id'];
			$category = new BP_Links_Category( $category_id );
		} else {
			// new category
			$category_id = null;
			$category = new BP_Links_Category();
		}

		// new values
		if ( isset( $_POST['name'] ) ) {
			$category_name = $_POST['name'];

			if ( strlen( $category_name ) >= 3 && strlen( $category_name ) <= 50) {
				if ( empty($category->slug) && $category->check_slug_raw( $category_name ) ) {
					$error = true;
					$message = __( 'Link category slug from this name already exists.', 'buddypress-links' ) . ' ' . __( 'Please try again.', 'buddypress-links' );
					$message_type = 'error';
				} else {
					$category->name = $category_name;
				}
			} else {
				$error = true;
				$message = sprintf( __( 'Link category name must be %1$d to %2$d characters in length.', 'buddypress-links' ), 3, 50 ) . ' ' . __( 'Please try again.', 'buddypress-links' );
				$message_type = 'error';
			}
		} else {
			$error = true;
			$message = __( 'Link category name is required.', 'buddypress-links' ) . ' ' . __( 'Please try again.', 'buddypress-links' );
			$message_type = 'error';
		}

		if ( isset( $_POST['description'] ) ) {
			$category_description = $_POST['description'];

			if ( empty( $category_description ) ) {
				$category->description = null;
			} else {
				if ( strlen( $category_description ) >= 5 && strlen( $category_description ) <= 250 ) {
					$category->description = $category_description;
				} else {
					$error = true;
					$message = sprintf( __( 'Link category description must be %1$d to %2$d characters in length.', 'buddypress-links' ), 5, 250 ) . ' ' . __( 'Please try again.', 'buddypress-links' );
					$message_type = 'error';
				}
			}
		}

		if ( isset( $_POST['priority'] ) ) {
			$category_priority = (int) $_POST['priority'];

			if ( $category_priority >= 1 && $category_priority <= 100 ) {
				$category->priority = $category_priority;
			} else {
				$error = true;
				$message = sprintf( __( 'Link category priority must be a number from %1$d to %2$d.', 'buddypress-links' ), 1, 100 ) . ' ' . __( 'Please try again.', 'buddypress-links' );
				$message_type = 'error';
			}
		} else {
			$error = true;
			$message = __( 'Link category priority is required.', 'buddypress-links' ) . ' ' . __( 'Please try again.', 'buddypress-links' );
			$message_type = 'error';
		}

		// try to save
		if ( false === $error ) {
			if ( $category->save() ) {
				$message = sprintf(
					'%1$s <a href="?page=buddypress-links-admin-cats">%2$s</a> %3$s <a href="?page=buddypress-links-admin-cats&amp;category_id=">%4$s</a>',
					__( 'Link category saved!', 'buddypress-links' ), // arg 1
					__( 'Return to list', 'buddypress-links' ), // arg 2
					__( 'or', 'buddypress-links' ), // arg 3
					__( 'Create new category', 'buddypress-links' ) // arg 4
				);
				$message_type = 'updated';
			} else {
				$message = __( 'There were errors when saving the link category.', 'buddypress-links' ) . ' ' . __( 'Please try again.', 'buddypress-links' );
				$message_type = 'error';
			}
		}

	} else {

		if ( is_numeric($_GET['category_id']) ) {
			// edit the category
			$category_id = (int) $_GET['category_id'];
			$category = new BP_Links_Category( $category_id );
		} else {
			// new category
			$category_id = null;
			$category = new BP_Links_Category();
		}

		// defaults for new category
		$category_name = $category->name;
		$category_description = $category->description;
		$category_priority = $category->priority;
	}


	//
	// Display Page
	//

	if ( $category_id ) {
		$heading_text = __( 'Edit Category', 'buddypress-links' ) . ': ' . $category_name;
		$submit_text = __( 'Update Category', 'buddypress-links' );
		$action = 'update';
		$nonce_action = 'update-link-category_' . $category_id;
	} else {
		$heading_text = __( 'New Category', 'buddypress-links' );
		$submit_text = __( 'Add Category', 'buddypress-links' );
		$action = 'create';
		$nonce_action = 'add-link-category';
	}

	require_once( BP_LINKS_ADMIN_THEME_DIR . '/category-edit.php');

	return true;
}

?>
