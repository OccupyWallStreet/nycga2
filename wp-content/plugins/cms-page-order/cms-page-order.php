<?php
/*
Plugin Name: CMS Page Order
Plugin URI: http://wordpress.org/extend/plugins/cms-page-order/
Description: Change the page order with quick and easy drag and drop.
Version: 0.3.2
Author: Bill Erickson
Author URI: http://www.billerickson.net
License: Public Domain
*/
/*

	Hello,
	
	You can use the following filters to customize the page list:
	
	* cmspo_post_types
		What post types the Page Order is available on
		Default: page
		
	* cmspo_max_levels
		The number of levels pages can be nested.
	
	* cmspo_post_statuses
		The post statuses to show.
		Default: all (including custom statuses), except trash, auto-draft and inherit

	* cmspo_page_label
		The label for the subpage
		Default: Page Order
		
*/

define( 'CMSPO_VERSION', '0.3.2' );
define( 'CMSPO_URL', WP_PLUGIN_URL . '/cms-page-order/' );

add_action( 'wp_ajax_save_tree', 'cmspo_ajax_save_tree' );
add_action( 'wp_ajax_remove_label', 'cmspo_ajax_remove_label' );
add_action( 'admin_menu', 'cmspo_admin_menu' );
add_action( 'admin_init', 'cmspo_admin_init' );
add_action( 'admin_head', 'cmspo_admin_head' );


/** Setup Page, Styles and Scripts */
function cmspo_admin_menu() {

	// Only add the page if user can edit pages
	if( !current_user_can( 'edit_pages' ) )
		return;
	
	// Load Translations
	load_plugin_textdomain( 'cms-page-order', false, dirname( plugin_basename( __FILE__ ) ) . '/locale/');
	
	// Add Page Order to each post type that can be edited
	$post_types = apply_filters( 'cmspo_post_types', array( 'page' ) );
	foreach( $post_types as $post_type ) {
	
		// Add subpage
		$page = add_submenu_page( 'edit.php?post_type=' . $post_type, apply_filters( 'cmspo_page_label', __( 'Page Order', 'cms-page-order' ), $post_type ), apply_filters( 'cmspo_page_label', __( 'Page Order', 'cms-page-order' ), $post_type ), 'edit_pages', 'order-' . $post_type, 'cmspo_menu_order_page' );
		
		// Add scripts
		if( $page ) 
			add_action( 'admin_print_styles-' . $page, 'cmspo_print_styles' );
		
		// Add contextual help
		if( $page ) 
			add_action( 'load-' . $page, 'cmspo_help_tab' );
	}

}
function cmspo_print_styles() {
	wp_enqueue_style( 'cmspo_stylesheet' );
}

/**
 * Contextual Help
 * @link http://wpdevel.wordpress.com/2011/12/06/help-and-screen-api-changes-in-3-3/
 *
 */
function cmspo_help_tab() {
    
	$help = '<p>'.__( 'Rearrange the pages by dragging and dropping.', 'cms-page-order' );
	if ( ( $max_levels = apply_filters( 'cmspo_max_levels', 0 ) - 1 ) && $max_levels > 0 )
		$help .= ' '.sprintf( _n( 'You can have one submenu.', 'You can have %d sets of submenus.', $max_levels ), $max_levels );

    $screen = get_current_screen();
    $screen->add_help_tab( array(
        'id'      => 'cms-page-order-help', 
        'title'   => __( 'Ordering Instructions', 'cms-page-order' ),
        'content' => $help,
    ) );
}

function cmspo_admin_init() {
	wp_enqueue_script( 'jquery-ui-sortable', '', array('jquery'), false );
	wp_enqueue_script( 'jquery-ui-effects', '', array('jquery', 'jquery-ui'), false );
	wp_enqueue_script( 'jquery-ui-nestedsortable', CMSPO_URL . 'scripts/jquery.ui.nestedSortable-1.3.4.min.js', array('jquery', 'jquery-ui-sortable') );
	wp_enqueue_script( 'cms-page-order', CMSPO_URL . 'scripts/cms-page-order.js', array('jquery', 'jquery-ui-sortable', 'jquery-ui-nestedsortable'), CMSPO_VERSION );
	wp_register_style( 'cmspo_stylesheet', CMSPO_URL . 'styles/style.css', '', CMSPO_VERSION );
	
	$strings = array(
		'Expand_all'		=> __( 'Expand all', 'cms-page-order' ),
		'Collapse_all'	=> __( 'Collapse all', 'cms-page-order' )
	);
	wp_localize_script( 'cms-page-order', 'cmspo', $strings );
	
	global $nonce;
	$nonce = wp_create_nonce( 'cms-page-order' );
	
}


/** Admin Head */
function cmspo_admin_head() {
	global $nonce;
?>
<!--[if lt IE 9]>
	<link rel="stylesheet" href="<?php echo CMSPO_URL . 'styles/ielt9.css' ?>" type="text/css" media="all" />
<![endif]-->
<!--[if IE 7]>
	<link rel="stylesheet" href="<?php echo CMSPO_URL . 'styles/ie7.css' ?>" type="text/css" media="all" />
<![endif]-->
<script type="text/javascript">
//<![CDATA[
	var cmspo_maxLevels = <?php echo apply_filters( 'cmspo_max_levels', 0 ); ?>;
	var _cmspo_ajax_nonce = '<?php echo $nonce ?>';
//]]>
</script>
<?php
}


/** Save Changes */
function cmspo_ajax_save_tree() {
	if ( !check_ajax_referer( 'cms-page-order', false, false )
			&& ( !isset($_REQUEST['open']) || empty($_REQUEST['order'] ) ) )
		cmspo_do_err();
		
	if ( isset($_REQUEST['open']) ) {
		$user = wp_get_current_user();
		update_user_option( $user->ID, 'cmspo_tree_state', $_REQUEST['open'] );		
	}
	
	if ( !empty($_REQUEST['order']) ) {
		global $wpdb;
		unset( $_REQUEST['order'][0] );

		$prev_depth = 1;
		$order = array();
		$order[1] = 1;
		
		foreach ( $_REQUEST['order'] as $page ) {			
			$post_id = (int) $page['item_id'];
			if ($page['parent_id'] == 'root')
				$parent = 0;
			else
				$parent = (int) $page['parent_id'];

			if ( $page['depth'] > $prev_depth ) {
				$order[$page['depth']] = 1;
				$menu_order = $order[$page['depth']];
			}
			else if ( $page['depth'] < $prev_depth )
				$menu_order = $order[$page['depth']];
			
			$prev_depth = (int) $page['depth'];
			
			$data = array( 'menu_order' => $order[$page['depth']], 'post_parent' => $parent );
			$where = array( 'ID' => $post_id );
			
			$wpdb->update( $wpdb->posts, $data, $where );
			clean_page_cache( $post_id );
			
			$order[$page['depth']]++;
		}
		global $wp_rewrite;
		$wp_rewrite->flush_rules( false );
	}
	die();
}

function cmspo_ajax_remove_label() {
	if ( !check_ajax_referer( 'cms-page-order', false, false ) || (empty($_REQUEST['post']) && !(int)$_REQUEST['post'] && empty($_REQUEST['state'])) )
		cmspo_do_err();
		
	global $wpdb;
	$state = $_REQUEST['state'];
	$post_id = $_REQUEST['post'];
	$not_published = array( 'draft', 'pending', 'private', 'future' );
	
	switch ( $state ) {
		case 'future' :
			$post_date = current_time( 'mysql' );
			$data = array(
					'ID' => $post_id,
					'post_date' => current_time( 'mysql' ),
					'post_date_gmt' => get_gmt_from_date( $post_date ),
					'post_status' => 'publish'
			);
			wp_update_post( $data );
			break;
		case 'password' :
			wp_update_post( array( 'ID' => $post_id, 'post_password' => '' ) );
			break;
		default :
			wp_publish_post( $post_id );
	}
	die();
}


/** The HTML Page */
function cmspo_menu_order_page() {
	$post_type = esc_attr( $_GET['post_type'] );
 ?>
<div class="wrap">
	<div id="icon-edit-pages" class="icon32"></div> 
	<h2><?php echo apply_filters( 'cmspo_page_label', __('Page Order', 'cms-page-order'), $post_type ); ?></h2>
<?php if ( isset($_REQUEST['trashed']) && (int) $_REQUEST['trashed'] ) : ?>
		<div id="message" class="updated"><p>
			<?php
				printf( _n( 'Item moved to the Trash.', '%s items moved to the Trash.', $_REQUEST['trashed'] ), number_format_i18n( $_REQUEST['trashed'] ) );
				$ids = isset($_REQUEST['ids']) ? $_REQUEST['ids'] : 0;
				echo ' <a href="' . esc_url( wp_nonce_url( "edit.php?post_type=$post_type&doaction=undo&action=untrash&ids=$ids", "bulk-posts" ) ) . '">' . __('Undo') . '</a><br />';
				unset($_REQUEST['trashed']);
			?>
		</p></div>
<?php endif; ?>
	<div class="cmspo-content">
		<div class="cmspo-toolbar">
			<?php if ( defined('ICL_LANGUAGE_CODE') ) : ?>
			<div class="cmspo-icl-switcher">
				<?php cmspo_icl_switcher(); ?>
			</div>
			<?php endif; ?>
			<div class="cmspo-actions">
			</div>
		</div>
		<?php if ( $pages = cmspo_list_pages() ) : ?>
		<ol id="cmspo-pages" class="cmspo-sortable">
			<?php echo $pages ?>
		</ol>
		<?php else : ?>
			<p><?php _e( 'No pages found.' ) ?></p>
		<?php endif;?>
	</div>
</div>
<?php
}


/** WPML Language Switcher **/
function cmspo_icl_switcher() {
	global $wpdb;
	global $sitepress;
	
	$display_lang = $sitepress->get_admin_language();
		
	$post_statuses = cmspo_post_statuses();
	$post_statuses = "'".implode( "','", $post_statuses )."'";
	
	$res = $wpdb->get_results("
		SELECT t.language_code AS code, l.name, COUNT(t.language_code) AS count
		FROM {$wpdb->prefix}icl_translations t
		JOIN {$wpdb->prefix}icl_languages_translations l
		ON t.language_code = l.language_code AND l.display_language_code = '{$display_lang}'
		JOIN {$wpdb->posts} p
		ON p.id = element_id
		WHERE t.element_type = 'post_page' AND p.post_status IN ({$post_statuses})
		GROUP BY t.language_code
		");

	$post_type = esc_attr( $_GET['post_type'] );
	foreach ( $res as $r ) {
		if ( $r->code == ICL_LANGUAGE_CODE )
			$langs[] = '<span class="po-sel">'.$r->name.' <span class="po-count">('.$r->count.')</span>';
		else
			$langs[] = '<span><a href="' . network_admin_url( add_query_arg( array( 'post_type' => $post_type, 'page' => 'order-page', 'lang' => $r->code ), 'edit.php' ) ) . '">'.$r->name.'</a> <span class="po-count">('.$r->count.')</span>';
	}
	echo implode( ' | </span>', $langs ) . '</span>';
}


/** Mics. functions */
function cmspo_get_user_option($option) {
	$user = wp_get_current_user();
	return get_user_option( $option, $user->ID );
}

function cmspo_list_pages($args = null, $count = false) {
	// no array in post_status until 3.2
	$post_type = esc_attr( $_GET['post_type'] );
	$defaults = array(
		'post_type'		=> $post_type,
		'posts_per_page'	=> -1,
		'orderby'			=> 'menu_order',
		'order'				=> 'ASC',
		'post_status'	=> implode(',', cmspo_post_statuses())
	);
	$r = wp_parse_args( $args, $defaults );
	
	$pages = new WP_Query( $r );
	wp_reset_query();
	$pages = $pages->posts;
	
	if ( count($pages) == 0 )
		return false;
	else if ( $count )
		return count($pages);

	$walker = new PO_Walker;
	$args = array(
		'depth' => 0,
		'link_before' => '',
		'link_after' => '',
		'walker' => $walker
	);
	$output = walk_page_tree($pages, 0, $args['depth'], $args);
	return $output;
}

function cmspo_has_children($page_id) {
	$args = array(
		'post_parent' => $page_id
	);
	return cmspo_list_pages( $args, true );
}

function cmspo_post_statuses() {
	$p = get_available_post_statuses('page');
	$exclude = array( 'trash', 'auto-draft', 'inherit' );
	foreach ( $exclude as $e )
		unset( $p[array_search($e, $p)] );
	return apply_filters( 'cmspo_post_statuses', $p );
}

function cmspo_do_err() {
	header("HTTP/1.0 400 Bad Request");
	die();
}

/** Special Walker for the Pages */
class PO_Walker extends Walker_Page {
	function start_lvl(&$output, $depth) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ol class=\"cmspo-children\">\n";
	}
	function end_lvl(&$output, $depth) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ol>\n";
	}
	function start_el(&$output, $page, $depth, $args) {
		if ( $depth )
			$indent = str_repeat("\t", $depth);
		else
			$indent = '';

		extract($args, EXTR_SKIP);

		// Post States
		$page_states = array();
			
			if ( $user_id = wp_check_post_lock($page->ID) ) {
				$user = get_userdata( $user_id );
				$page_states['post-lock'] = sprintf( __( '! %s is editing this page. Page order might be overwritten.', 'cms-page-order' ), $user->display_name );
			}
			
			// Password protected?
			if ( !empty($page->post_password) )
				$page_states['password'] = __( 'Password protected', 'cms-page-order' );
			
			// Check post status, but skip published pages.
			if ( $page->post_status !== 'publish' )
				$page_states[$page->post_status] = $page->post_status;
			
			// Text for the labels
			foreach ( $page_states as $state => $state_name ) {
				// Don't list private pages if user is not allowed read them
				if ( $state == 'private' )
					if ( !current_user_can('read_private_pages') )
						return;
				
				if ( in_array($state, array('private', 'draft', 'pending')) )
					$page_states[$state] = __( ucfirst($state) );
				elseif ( $state == 'future' )
					$page_states[$state] = __( 'Scheduled', 'cms-page-order' );
				elseif ( $state !== 'password' && empty($state_name) )
					$page_states[$state] = ucfirst($state);
			}
			
			// Set date_i18n( __( 'M j Y @ H:i' ), strtotime( $page->post_date ) ) as title on scheduled posts
			$state_labels = null;
			foreach ( $page_states as $state => $state_name ) {
				$title = null;
				// Text for the title attribute
				if ( in_array($state, array('password', 'private')) )
					$title = __( 'Make page public', 'cms-page-order' );
				elseif ( in_array($state, array('draft', 'pending', 'future')) )
					$title = __( 'Publish page', 'cms-page-order' );
				
				$post_type = esc_attr( $_GET['post_type'] );				
				if ( in_array( $state, array( 'draft', 'pending', 'future', 'private', 'password' ) ) )
					$action_url = wp_nonce_url( '?post_type=' . $post_type. '&page=order&post='.$page->ID.'&action=remove_label&state='.$state, 'cms-page-order' );
					if ( $state == 'private' && !current_user_can( 'edit_private_pages' ) )
						$action_url = null;
				else
					$action = null;
				
				if ( !empty($action_url) )
					$action = '<a title="'.$title.'" href="'.$action_url.'" class="cmspo-delete">x</a>';

				$state_labels .= '<span class="cmspo-state '.$state.'">'.$state_name.' '.$action.'</span> ';
		}

		if ( $children_count = cmspo_has_children($page->ID) ) {
			$children_count = ' <span class="cmspo-count">('.$children_count.')</span>';
			
			if ( ($state = cmspo_get_user_option( 'cmspo_tree_state' ) ) && in_array( $page->ID, $state ) )
				$output .= $indent . '<li id="page-'.$page->ID.'" class="cmspo-open">';
			else
				$output .= $indent . '<li id="page-'.$page->ID.'" class="cmspo-closed">';
				
		} else {
			$output .= $indent . '<li id="page-'.$page->ID.'">';
			$children_count = ' <span class="cmspo-count"></span>';
		}

		$output .= '<div class="cmspo-page">'
								.$state_labels.apply_filters( 'the_title', $page->post_title, $page->ID )
								.$children_count
								.' <span class="cmspo-page-actions">'
									.'<a class="cmspo-edit" href="'.get_permalink( $page->ID ).'">'.__( 'View' ).'</a>';

		// can has capabilities to edit this page?
		if ( $edit = get_edit_post_link( $page->ID ) )
			$output .= 	' | <a class="cmspo-edit" href="'.$edit.'">'.__( 'Edit' ).'</a>';
		// can has capabilities to delete this page?
		if ( $delete = get_delete_post_link( $page->ID ) )
			$output .= 	' | <a class="cmspo-delete" href="'.$delete.'">'._x( 'Trash', 'verb' ).'</a>';
			
		$output .= 		'</span>'
								.'</div>';

	}
}


/** Remove traces from the database */
function cmspo_deactivate() {
	$users = get_users(array(
		'fields' => 'ID'
	));
	foreach ( $users as $user )
		delete_user_option( $user, 'cmspo_tree_state' );
}
register_deactivation_hook( __FILE__, 'cmspo_deactivate' );
