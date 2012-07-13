<?php
/*
Plugin Name: BP Group Organizer
Plugin URI: http://www.generalthreat.com/projects/buddypress-group-organizer
Description: Easily create, edit, and delete BuddyPress groups - with drag and drop simplicity
Version: 1.0.4
Revision Date: 03/24/2012
Requires at least: WP 3.1, BuddyPress 1.2
Tested up to: WP 3.3.1 , BuddyPress 1.5.4
License: Example: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html
Author: David Dean
Author URI: http://www.generalthreat.com/
*/

include dirname(__FILE__) . '/functions.php';

/** load localization files if present */
if( file_exists( dirname( __FILE__ ) . '/languages/' . dirname(plugin_basename(__FILE__)) . '-' . get_locale() . '.mo' ) ) {
	load_plugin_textdomain( 'bp-group-organizer', false, dirname(plugin_basename(__FILE__)) . '/languages' );
} else if ( file_exists( dirname( __FILE__ ) . '/languages/' . get_locale() . '.mo' ) ) {
	_doing_it_wrong( 'load_textdomain', 'Please rename your translation files to use the ' . dirname(plugin_basename(__FILE__)) . '-' . get_locale() . '.mo' . ' format', '1.0.2' );
	load_textdomain( 'bp-group-organizer', dirname( __FILE__ ) . '/languages/' . get_locale() . '.mo' );
}

function bp_group_organizer_admin() {
	$page = add_submenu_page( 'bp-general-settings', __('Group Organizer', 'bp-group-organizer'), __('Group Organizer', 'bp-group-organizer'), 'manage_options', 'group_organizer', 'bp_group_organizer_admin_page' );
	add_action('admin_print_scripts-' . $page, 'bp_group_organizer_load_scripts');
	add_action('admin_print_styles-' . $page, 'bp_group_organizer_load_styles');
}

function bp_group_organizer_register_admin() {

	add_action( 'network_admin_menu', 'bp_group_organizer_admin' );
	add_action( 'admin_menu', 'bp_group_organizer_admin' );	// fix issue with BP 1.2 and admin URL
	add_action( 'admin_init', 'bp_group_organizer_register_scripts' );

}
add_action( 'bp_include', 'bp_group_organizer_register_admin' );

function bp_group_organizer_register_scripts() {
	wp_register_script( 'group-organizer', plugins_url( 'js/group-organizer.js', __FILE__ ), array('jquery') );
}

function bp_group_organizer_load_scripts() {

	// jQuery
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-draggable' );
	wp_enqueue_script( 'jquery-ui-droppable' );
	wp_enqueue_script( 'jquery-ui-sortable' );
	
	// Group Organizer script
	wp_enqueue_script( 'group-organizer' );
	wp_localize_script( 'group-organizer', 'OrganizerL10n', bp_group_organizer_translate_script() );
	
	// Meta boxes
	wp_enqueue_script( 'common' );
	wp_enqueue_script( 'wp-lists' );
	wp_enqueue_script( 'postbox' );
	
}

function bp_group_organizer_translate_script() {
	return array(
		'noResultsFound'	=> _x('No results found.', 'search results'),
		'warnDeleteGroup'	=> __( "You are about to permanently delete this group. \n 'Cancel' to stop, 'OK' to delete.", 'bp-group-organizer'),
		'groupDeleted'		=> __('Group was deleted successfully.', 'bp-group-organizer'),
		'groupDeleteFailed'	=> __('Group could not be deleted.', 'bp-group-organizer'),
		'saveAlert' 		=> __('The changes you made will be lost if you navigate away from this page.'),
	);
}

function bp_group_organizer_load_styles() {

	// Nav Menu CSS
	// This dropped out in WP 3.3, but is still needed for compat
	if( floatval( get_bloginfo( 'version' ) ) < 3.3 ) {
		wp_admin_css( 'nav-menu' );
	}
	
	
}

function bp_group_organizer_admin_page() {
	
	global $wpdb;

	// Permissions Check
	if ( ! current_user_can('manage_options') )
		wp_die( __( 'Cheatin&#8217; uh?' ) );
	
	// Load all the nav menu interface functions
	require_once( 'includes/group-meta-boxes.php' );
	require_once( 'includes/group-organizer-template.php' );
	require_once( 'includes/group-organizer.php' );
	
	// Container for any messages displayed to the user
	$messages = array();

	// Container that stores the name of the active menu
	$nav_menu_selected_title = '';
	
	// Allowed actions: add, update, delete
	$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'edit';

	switch ( $action ) {
		case 'add-group':
			check_admin_referer( 'add-group', 'group-settings-column-nonce' );
			
			$group['name']			= stripslashes( $_POST['group_name'] );
			$group['description']	= stripslashes( $_POST['group_desc'] );
			$group['slug']			= groups_check_slug($_POST['group_slug']);
			$group['status']		= $_POST['group_status'];
			$group['enable_forum']	= isset($_POST['group_forum']) ? true : false;
			$group['date_created']	= date('Y-m-d H:i:s');
	
			if($group['slug'] != $_POST['group_slug']) {
				$messages[] = '<div id="message" class="warning"><p>' . sprintf(__('The group slug you specified was unavailable or invalid. This group was created with the slug: <code>%s</code>.', 'bp-group-organizer'),$group['slug']) . '</p></div>';
			}
			
			$group_id = groups_create_group( $group );
			if(!$group_id) {
				$wpdb->show_errors();
				$wpdb->print_error();
				$messages[] = '<div id="message" class="error"><p>' . __('Group was not successfully created.', 'bp-group-organizer') . '</p></div>';
			} else {
				$messages[] = '<div id="message" class="updated"><p>' . __('Group was created successfully.', 'bp-group-organizer') . '</p></div>';
			}
			
			groups_update_groupmeta( $group_id, 'total_member_count', 1);
			
			if(defined('BP_GROUP_HIERARCHY_IS_INSTALLED')) {
				$group = new BP_Groups_Hierarchy( $group_id );
				$group->parent_id	= (int)$_POST['group_parent'];
				$group->save();
			}
			
			do_action( 'bp_group_organizer_save_new_group_options', $group_id );
			
			break;
		case 'delete-group':
			$group_id = (int) $_REQUEST['group_id'];
	
			check_admin_referer( 'delete-group_' . $group_id );
			break;
		case 'update':
			check_admin_referer( 'update-groups', 'update-groups-nonce' );
			
			$groups_order = $_POST['group'];
			
			$parent_ids = $_POST['menu-item-parent-id'];
			$db_ids = $_POST['menu-item-db-id'];
			
			
			foreach($groups_order as $id => $group) {
				
				$group_reference = new BP_Groups_Group( $id );
				
				if( defined( 'BP_GROUP_HIERARCHY_IS_INSTALLED' ) && method_exists('BP_Groups_Hierarchy','get_tree') ) {
					// if group hierarchy is installed and available, check for tree changes
	
					$group_hierarchy = new BP_Groups_Hierarchy( $id );
	
					if( $parent_ids[$id] !== null && $group_hierarchy->parent_id != $parent_ids[$id] ) {
						$group_hierarchy->parent_id = $parent_ids[$id];
						$group_hierarchy->save();
					} else if($group_hierarchy->parent_id != $group['parent_id']) {
						$group_hierarchy->parent_id = $group['parent_id'];
						$group_hierarchy->save();
					}
					unset($group_hierarchy);
				}
				
				// check for group attribute changes
				$attrs_changed = array();
				if($group['name'] != $group_reference->name) {
					$group_reference->name = stripslashes( $group['name'] );
					$attrs_changed[] = 'name';
				}
				if($group['slug'] != $group_reference->slug) {
					$slug = groups_check_slug($group['slug']);
					if($slug == $group['slug']) {
						$group_reference->slug = $group['slug'];
						$attrs_changed[] = 'slug';
					}
				}
				if($group['description'] != $group_reference->description) {
					$group_reference->description = stripslashes( $group['description'] );
					$attrs_changed[] = 'description';
				}
				if( $group['status'] != $group_reference->status && groups_is_valid_status($group['status']) ) {
					$group_reference->status = $group['status'];
					$attrs_changed[] = 'status';
				}
				if( !isset($group['forum_enabled']) || $group['forum_enabled'] != $group_reference->enable_forum ) {
					$group_reference->enable_forum = isset($group['forum_enabled']) ? true : false ;
					$attrs_changed[] = 'enable_forum';
				}
				
				if(count($attrs_changed) > 0) {
					$group_reference->save();
				}
				
				// finally, let plugins run any other changes
				do_action( 'bp_group_organizer_save_group_options', $group, $group_reference );
				
			}
			break;
	}
	
	// Ensure the user will be able to scroll horizontally
	// by adding a class for the max menu depth.
	global $_wp_nav_menu_max_depth;
	$_wp_nav_menu_max_depth = 0;
	
	$edit_markup = bp_get_groups_to_edit( );

?>
<div class="wrap">
	<h2><?php esc_html_e('Group Organizer'); ?></h2>
	<?php
	foreach( $messages as $message ) :
		echo $message . "\n";
	endforeach;
	?>
	<div id="nav-menus-frame">
	<div id="menu-settings-column" class="metabox-holder">
		<form id="nav-menu-meta" action="" class="nav-menu-meta" method="post" enctype="multipart/form-data">
			<input type="hidden" name="action" value="add-group" />
			<?php wp_nonce_field( 'add-group', 'group-settings-column-nonce' ); ?>
			<?php do_meta_boxes( 'group-organizer', 'side', null ); ?>
		</form>
	</div><!-- /#menu-settings-column -->
	<div id="menu-management-liquid">
		<div id="menu-management" class="nav-menus-php">
			<div class="menu-edit">
				<form id="update-nav-menu" action="" method="post" enctype="multipart/form-data">
					<div id="nav-menu-header">
						<div id="submitpost" class="submitbox">
							<div class="major-publishing-actions">
								<label class="menu-name-label howto open-label" for="menu-name">
									<span><?php _e('Group Organizer', 'bp-group-organizer'); ?></span>
								</label>
								<div class="publishing-action">
									<?php submit_button( __( 'Save Groups', 'bp-group-organizer' ), 'button-primary menu-save', 'save_menu', false, array( 'id' => 'save_menu_header' ) ); ?>
								</div><!-- END .publishing-action -->
							</div><!-- END .major-publishing-actions -->
						</div><!-- END #submitpost .submitbox -->
						<?php
						wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
						wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
						wp_nonce_field( 'update-groups', 'update-groups-nonce' );
						?>
						<input type="hidden" name="action" value="update" />
					</div><!-- END #nav-menu-header -->
					<div id="post-body">
						<div id="post-body-content">
							<?php
							if ( isset( $edit_markup ) ) {
								if ( ! is_wp_error( $edit_markup ) )
									echo  $edit_markup;
							} else {
								echo '<div class="post-body-plain">';
								echo '<p>' . __('You don\'t yet have any groups.', 'bp-group-organizer') . '</p>';
								echo '</div>';
							}
							?>
						</div><!-- /#post-body-content -->
					</div><!-- /#post-body -->
					<div id="nav-menu-footer">
						<div class="major-publishing-actions">
						<div class="publishing-action">
							<?php submit_button( __( 'Save Groups', 'bp-group-organizer' ), 'button-primary menu-save', 'save_menu', false, array( 'id' => 'save_menu_footer' ) ); ?>
						</div>
						</div>
					</div><!-- /#nav-menu-footer -->
				</form><!-- /#update-nav-menu -->
			</div><!-- /.menu-edit -->
		</div><!-- /#menu-management -->
	</div><!-- /#menu-management-liquid -->
	</div><!-- /#nav-menus-frame -->
</div><!-- /.wrap-->

<?php	
}

function bp_organizer_delete_group() {
	$group_id = (int)$_REQUEST['group_id'];
	
	if(!current_user_can('manage_options')) {
		die(_('Not authorized to delete groups.','bp-group-organizer'));
	}
	check_ajax_referer('delete-group_' . $group_id);

	if(groups_delete_group( $group_id )) {
		die('success');
	}
	die(__('Group delete failed.','bp-group-organizer'));
}

add_action( 'wp_ajax_bp_organizer_delete-group', 'bp_organizer_delete_group' );

?>