<?php
/**
 * BP Template Pack Functions
 *
 * Sets up the current WP theme for BuddyPress compatibility.
 * Most of these functions are extrapolated from bp-default's functions.php.
 *
 * @package BP_TPack
 * @subpackage Functions
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// Check to make sure the active theme is not bp-default
if ( 'bp-default' == get_option( 'template' ) )
	return;

/**
 * Sets up WordPress theme for BuddyPress support.
 *
 * @since 1.2
 */
function bp_tpack_theme_setup() {
	global $bp;

	// Load the default BuddyPress AJAX functions if it isn't explicitly disabled
	if ( !(int)get_option( 'bp_tpack_disable_js' ) )
		require_once( BP_PLUGIN_DIR . '/bp-themes/bp-default/_inc/ajax.php' );

	if ( !is_admin() ) {
		// Register buttons for the relevant component templates
		// Friends button
		if ( bp_is_active( 'friends' ) )
			add_action( 'bp_member_header_actions',    'bp_add_friend_button' );

		// Activity button
		if ( bp_is_active( 'activity' ) )
			add_action( 'bp_member_header_actions',    'bp_send_public_message_button' );

		// Messages button
		if ( bp_is_active( 'messages' ) )
			add_action( 'bp_member_header_actions',    'bp_send_private_message_button' );

		// Group buttons
		if ( bp_is_active( 'groups' ) ) {
			add_action( 'bp_group_header_actions',     'bp_group_join_button' );
			add_action( 'bp_group_header_actions',     'bp_group_new_topic_button' );
			add_action( 'bp_directory_groups_actions', 'bp_group_join_button' );
		}

		// Blog button
		if ( bp_is_active( 'blogs' ) )
			add_action( 'bp_directory_blogs_actions',  'bp_blogs_visit_blog_button' );
	}
}
add_action( 'after_setup_theme', 'bp_tpack_theme_setup', 11 );

/**
 * Enqueues BuddyPress JS and related AJAX functions
 *
 * @since 1.2
 */
function bp_tpack_enqueue_scripts() {
	// Do not enqueue JS if it's disabled
	if ( get_option( 'bp_tpack_disable_js' ) )
		return;

	// Add words that we need to use in JS to the end of the page so they can be translated and still used.
	$params = array(
		'my_favs'           => __( 'My Favorites', 'buddypress' ),
		'accepted'          => __( 'Accepted', 'buddypress' ),
		'rejected'          => __( 'Rejected', 'buddypress' ),
		'show_all_comments' => __( 'Show all comments for this thread', 'buddypress' ),
		'show_all'          => __( 'Show all', 'buddypress' ),
		'comments'          => __( 'comments', 'buddypress' ),
		'close'             => __( 'Close', 'buddypress' )
	);

	// BP 1.5+
	if ( version_compare( BP_VERSION, '1.3', '>' ) ) {
		// Bump this when changes are made to bust cache
		$version            = '20110818';

		$params['view']     = __( 'View', 'buddypress' );
	}
	// BP 1.2.x
	else {
		$version = '20110729';

		if ( bp_displayed_user_id() )
			$params['mention_explain'] = sprintf( __( "%s is a unique identifier for %s that you can type into any message on this site. %s will be sent a notification and a link to your message any time you use it.", 'buddypress' ), '@' . bp_get_displayed_user_username(), bp_get_user_firstname( bp_get_displayed_user_fullname() ), bp_get_user_firstname( bp_get_displayed_user_fullname() ) );
	}

	// Enqueue the global JS - Ajax will not work without it
	wp_enqueue_script( 'dtheme-ajax-js', BP_PLUGIN_URL . '/bp-themes/bp-default/_inc/global.js', array( 'jquery' ), $version );

	// Localize the JS strings
	wp_localize_script( 'dtheme-ajax-js', 'BP_DTheme', $params );
}
add_action( 'wp_enqueue_scripts', 'bp_tpack_enqueue_scripts' );

/**
 * Enqueues BuddyPress basic styles
 *
 * @since 1.2
 */
function bp_tpack_enqueue_styles() {
	// Do not enqueue CSS if it's disabled
	if ( get_option( 'bp_tpack_disable_css' ) )
		return;

	// BP 1.5+
	if ( version_compare( BP_VERSION, '1.3', '>' ) ) {
		$stylesheet = 'bp.css';

		// Bump this when changes are made to bust cache
		$version    = '20110918';
	}
	// BP 1.2.x older styles
	else {
		$stylesheet = 'bp-backpat.css';
		$version    = '20110729';
	}

	// Add the wireframe BP page styles
	wp_enqueue_style( 'bp', plugins_url( '/bp-template-pack/' ) . $stylesheet, array(), $version );

	// Enqueue RTL styles for BP 1.5+
	if ( version_compare( BP_VERSION, '1.3', '>' ) && is_rtl() )
		wp_enqueue_style( 'bp-rtl',  plugins_url( '/bp-template-pack/' ) . 'bp-rtl.css', array( 'bp' ), $version );
}
add_action( 'wp_enqueue_scripts', 'bp_tpack_enqueue_styles' );

if ( !function_exists( 'bp_tpack_use_wplogin' ) ) :
/**
 * BP Template Pack doesn't use bp-default's built-in sidebar login block,
 * so during no access requests, we need to redirect them to wp-login for
 * authentication.
 *
 * @since 1.2
 */
function bp_tpack_use_wplogin() {
	// returning 2 will automatically use wp-login
	return 2;
}
add_filter( 'bp_no_access_mode', 'bp_tpack_use_wplogin' );
endif;

/**
 * Hooks into the 'bp_get_activity_action_pre_meta' action to add secondary activity avatar support
 *
 * @since 1.2
 */
function bp_tpack_activity_secondary_avatars( $action, $activity ) {
	// sanity check - some older versions of BP do not utilize secondary activity avatars
	if ( function_exists( 'bp_get_activity_secondary_avatar' ) ) :
		switch ( $activity->component ) {
			case 'groups' :
			case 'friends' :
				// Only insert avatar if one exists
				if ( $secondary_avatar = bp_get_activity_secondary_avatar() ) {
					$reverse_content = strrev( $action );
					$position        = strpos( $reverse_content, 'a<' );
					$action          = substr_replace( $action, $secondary_avatar, -$position - 2, 0 );
				}
				break;
		}
	endif;

	return $action;
}
add_filter( 'bp_get_activity_action_pre_meta', 'bp_tpack_activity_secondary_avatars', 10, 2 );


/**  BP 1.2.x *************************************************************/
if ( version_compare( BP_VERSION, '1.3', '<' ) ) :

	/*****
	 * Add support for showing the activity stream as the front page of the site
	 */

	/* Filter the dropdown for selecting the page to show on front to include "Activity Stream" */
	function bp_tpack_wp_pages_filter( $page_html ) {
		if ( 'page_on_front' != substr( $page_html, 14, 13 ) )
			return $page_html;

		$selected = false;
		$page_html = str_replace( '</select>', '', $page_html );

		if ( bp_tpack_page_on_front() == 'activity' )
			$selected = ' selected="selected"';

		$page_html .= '<option class="level-0" value="activity"' . $selected . '>' . __( 'Activity Stream', 'buddypress' ) . '</option></select>';
		return $page_html;
	}
	add_filter( 'wp_dropdown_pages', 'bp_tpack_wp_pages_filter' );

	/* Hijack the saving of page on front setting to save the activity stream setting */
	function bp_tpack_page_on_front_update( $oldvalue, $newvalue ) {
		if ( !is_admin() || !is_super_admin() )
			return false;

		if ( 'activity' == $_POST['page_on_front'] )
			return 'activity';
		else
			return $oldvalue;
	}
	add_action( 'pre_update_option_page_on_front', 'bp_tpack_page_on_front_update', 10, 2 );

	/* Load the activity stream template if settings allow */
	function bp_tpack_page_on_front_template( $template ) {
		global $wp_query;

		if ( empty( $wp_query->post->ID ) )
			return locate_template( array( 'activity/index.php' ), false );
		else
			return $template;
	}
	add_filter( 'page_template', 'bp_tpack_page_on_front_template' );

	/* Return the ID of a page set as the home page. */
	function bp_tpack_page_on_front() {
		if ( 'page' != get_option( 'show_on_front' ) )
			return false;

		return apply_filters( 'bp_tpack_page_on_front', get_option( 'page_on_front' ) );
	}

	/* Force the page ID as a string to stop the get_posts query from kicking up a fuss. */
	function bp_tpack_fix_get_posts_on_activity_front() {
		global $wp_query;

		if ( !empty($wp_query->query_vars['page_id']) && 'activity' == $wp_query->query_vars['page_id'] )
			$wp_query->query_vars['page_id'] = '"activity"';
	}
	add_action( 'pre_get_posts', 'bp_tpack_fix_get_posts_on_activity_front' );

endif;

?>