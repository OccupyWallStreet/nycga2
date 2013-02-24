<?php
/**
 * BP-Default theme functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress and BuddyPress to change core functionality.
 *
 * The first function, bp_dtheme_setup(), sets up the theme by registering support
 * for various features in WordPress, such as post thumbnails and navigation menus, and
 * for BuddyPress, action buttons and javascript localisation.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development, http://codex.wordpress.org/Child_Themes
 * and http://codex.buddypress.org/theme-development/building-a-buddypress-child-theme/), you can override
 * certain functions (those wrapped in a function_exists() call) by defining them first in your
 * child theme's functions.php file. The child theme's functions.php file is included before the
 * parent theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package BuddyPress
 * @subpackage BP-Default
 * @since 1.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'bp_dtheme_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress and BuddyPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override bp_dtheme_setup() in a child theme, add your own bp_dtheme_setup to your child theme's
 * functions.php file.
 *
 * @global BuddyPress $bp The one true BuddyPress instance
 * @since BuddyPress (1.5)
 */
function bp_dtheme_setup() {

	// Load the AJAX functions for the theme
	get_template_part('ajax' );

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// This theme comes with all the BuddyPress goodies
	add_theme_support( 'buddypress' );

	if ( !is_admin() ) {
		// Register buttons for the relevant component templates
		// Friends button
		if ( bp_is_active( 'friends' ) )
			add_action( 'bp_member_header_actions',    'bp_add_friend_button',           5 );

		// Activity button
		if ( bp_is_active( 'activity' ) )
			add_action( 'bp_member_header_actions',    'bp_send_public_message_button',  20 );

		// Messages button
		if ( bp_is_active( 'messages' ) )
			add_action( 'bp_member_header_actions',    'bp_send_private_message_button', 20 );

		// Group buttons
		if ( bp_is_active( 'groups' ) ) {
			add_action( 'bp_group_header_actions',     'bp_group_join_button',           5 );
			add_action( 'bp_group_header_actions',     'bp_group_new_topic_button',      20 );
			add_action( 'bp_directory_groups_actions', 'bp_group_join_button' );
		}

		// Blog button
		if ( bp_is_active( 'blogs' ) )
			add_action( 'bp_directory_blogs_actions',  'bp_blogs_visit_blog_button' );
	}
}
add_action( 'after_setup_theme', 'bp_dtheme_setup' );
endif;

if ( !function_exists( 'bp_dtheme_enqueue_scripts' ) ) :
/**
 * Enqueue theme javascript safely
 *
 * @see http://codex.wordpress.org/Function_Reference/wp_enqueue_script
 * @since BuddyPress (1.5)
 */
function bp_dtheme_enqueue_scripts() {

	// Enqueue the global JS - Ajax will not work without it
	wp_enqueue_script( 'dtheme-ajax-js', get_template_directory_uri() . '/_inc/global.js', array( 'jquery' ), bp_get_version() );

	// Add words that we need to use in JS to the end of the page so they can be translated and still used.
	$params = array(
		'my_favs'           => __( 'My Favorites', 'buddypress' ),
		'accepted'          => __( 'Accepted', 'buddypress' ),
		'rejected'          => __( 'Rejected', 'buddypress' ),
		'show_all_comments' => __( 'Show all comments for this thread', 'buddypress' ),
		'show_all'          => __( 'Show all', 'buddypress' ),
		'comments'          => __( 'comments', 'buddypress' ),
		'close'             => __( 'Close', 'buddypress' ),
		'view'              => __( 'View', 'buddypress' ),
		'mark_as_fav'	    => __( 'Favorite', 'buddypress' ),
		'remove_fav'	    => __( 'Remove Favorite', 'buddypress' )
	);
	wp_localize_script( 'dtheme-ajax-js', 'BP_DTheme', $params );

	// Maybe enqueue comment reply JS
	if ( is_singular() && bp_is_blog_page() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );
}
add_action( 'wp_enqueue_scripts', 'bp_dtheme_enqueue_scripts' );
endif;

if ( !function_exists( 'bp_dtheme_page_on_front' ) ) :
/**
 * Return the ID of a page set as the home page.
 *
 * @return false|int ID of page set as the home page
 * @since 1.2
 */
function bp_dtheme_page_on_front() {
	if ( 'page' != get_option( 'show_on_front' ) )
		return false;

	return apply_filters( 'bp_dtheme_page_on_front', get_option( 'page_on_front' ) );
}
endif;

if ( !function_exists( 'bp_dtheme_activity_secondary_avatars' ) ) :
/**
 * Add secondary avatar image to this activity stream's record, if supported.
 *
 * @param string $action The text of this activity
 * @param BP_Activity_Activity $activity Activity object
 * @package BuddyPress Theme
 * @return string
 * @since 1.2.6
 */
function bp_dtheme_activity_secondary_avatars( $action, $activity ) {
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

	return $action;
}
add_filter( 'bp_get_activity_action_pre_meta', 'bp_dtheme_activity_secondary_avatars', 10, 2 );
endif;

if ( !function_exists( 'bp_dtheme_show_notice' ) ) :
/**
 * Show a notice when the theme is activated - workaround by Ozh (http://old.nabble.com/Activation-hook-exist-for-themes--td25211004.html)
 *
 * @since 1.2
 */
function bp_dtheme_show_notice() {
	global $pagenow;

	// Bail if bp-default theme was not just activated
	if ( empty( $_GET['activated'] ) || ( 'themes.php' != $pagenow ) || !is_admin() )
		return;

	?>

	<div id="message" class="updated fade">
		<p><?php printf( __( 'Theme activated! This theme contains <a href="%s">custom header image</a> support and <a href="%s">sidebar widgets</a>.', 'buddypress' ), admin_url( 'themes.php?page=custom-header' ), admin_url( 'widgets.php' ) ); ?></p>
	</div>

	<style type="text/css">#message2, #message0 { display: none; }</style>

	<?php
}
add_action( 'admin_notices', 'bp_dtheme_show_notice' );
endif;

if ( !function_exists( 'bp_dtheme_main_nav' ) ) :
/**
 * wp_nav_menu() callback from the main navigation in header.php
 *
 * Used when the custom menus haven't been configured.
 *
 * @param array Menu arguments from wp_nav_menu()
 * @see wp_nav_menu()
 * @since BuddyPress (1.5)
 */
function bp_dtheme_main_nav( $args ) {
	$pages_args = array(
		'depth'      => 0,
		'echo'       => false,
		'exclude'    => '',
		'title_li'   => ''
	);
	$menu = wp_page_menu( $pages_args );
	$menu = str_replace( array( '<div class="menu"><ul>', '</ul></div>' ), array( '<ul id="nav">', '</ul><!-- #nav -->' ), $menu );
	echo $menu;

	do_action( 'bp_nav_items' );
}
endif;

if ( !function_exists( 'bp_dtheme_page_menu_args' ) ) :
/**
 * Get our wp_nav_menu() fallback, bp_dtheme_main_nav(), to show a home link.
 *
 * @param array $args Default values for wp_page_menu()
 * @see wp_page_menu()
 * @since BuddyPress (1.5)
 */
function bp_dtheme_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'bp_dtheme_page_menu_args' );
endif;

if ( !function_exists( 'bp_dtheme_comment_form' ) ) :
/**
 * Applies BuddyPress customisations to the post comment form.
 *
 * @param array $default_labels The default options for strings, fields etc in the form
 * @see comment_form()
 * @since BuddyPress (1.5)
 */
function bp_dtheme_comment_form( $default_labels ) {

	$commenter = wp_get_current_commenter();
	$req       = get_option( 'require_name_email' );
	$aria_req  = ( $req ? " aria-required='true'" : '' );
	$fields    =  array(
		'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name', 'buddypress' ) . ( $req ? '<span class="required"> *</span>' : '' ) . '</label> ' .
		            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
		'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email', 'buddypress' ) . ( $req ? '<span class="required"> *</span>' : '' ) . '</label> ' .
		            '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>',
		'url'    => '<p class="comment-form-url"><label for="url">' . __( 'Website', 'buddypress' ) . '</label>' .
		            '<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>',
	);

	$new_labels = array(
		'comment_field'  => '<p class="form-textarea"><textarea name="comment" id="comment" cols="60" rows="10" aria-required="true"></textarea></p>',
		'fields'         => apply_filters( 'comment_form_default_fields', $fields ),
		'logged_in_as'   => '',
		'must_log_in'    => '<p class="alert">' . sprintf( __( 'You must be <a href="%1$s">logged in</a> to post a comment.', 'buddypress' ), wp_login_url( get_permalink() ) )	. '</p>',
		'title_reply'    => __( 'Leave a reply', 'buddypress' )
	);

	return apply_filters( 'bp_dtheme_comment_form', array_merge( $default_labels, $new_labels ) );
}
add_filter( 'comment_form_defaults', 'bp_dtheme_comment_form', 10 );
endif;

if ( !function_exists( 'bp_dtheme_before_comment_form' ) ) :
/**
 * Adds the user's avatar before the comment form box.
 *
 * The 'comment_form_top' action is used to insert our HTML within <div id="reply">
 * so that the nested comments comment-reply javascript moves the entirety of the comment reply area.
 *
 * @see comment_form()
 * @since BuddyPress (1.5)
 */
function bp_dtheme_before_comment_form() {
?>
	<div class="comment-avatar-box">
		<div class="avb">
			<?php if ( bp_loggedin_user_id() ) : ?>
				<a href="<?php echo bp_loggedin_user_domain(); ?>">
					<?php echo get_avatar( bp_loggedin_user_id(), 50 ); ?>
				</a>
			<?php else : ?>
				<?php echo get_avatar( 0, 50 ); ?>
			<?php endif; ?>
		</div>
	</div>

	<div class="comment-content standard-form">
<?php
}
add_action( 'comment_form_top', 'bp_dtheme_before_comment_form' );
endif;

if ( !function_exists( 'bp_dtheme_after_comment_form' ) ) :
/**
 * Closes tags opened in bp_dtheme_before_comment_form().
 *
 * @see bp_dtheme_before_comment_form()
 * @see comment_form()
 * @since BuddyPress (1.5)
 */
function bp_dtheme_after_comment_form() {
?>

	</div><!-- .comment-content standard-form -->

<?php
}
add_action( 'comment_form', 'bp_dtheme_after_comment_form' );
endif;

if ( !function_exists( 'bp_dtheme_sidebar_login_redirect_to' ) ) :
/**
 * Adds a hidden "redirect_to" input field to the sidebar login form.
 *
 * @since BuddyPress (1.5)
 */
function bp_dtheme_sidebar_login_redirect_to() {
	$redirect_to = !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '';
	$redirect_to = apply_filters( 'bp_no_access_redirect', $redirect_to ); ?>

	<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />

<?php
}
add_action( 'bp_sidebar_login_form', 'bp_dtheme_sidebar_login_redirect_to' );
endif;

if ( !function_exists( 'bp_dtheme_content_nav' ) ) :
/**
 * Display navigation to next/previous pages when applicable
 *
 * @global WP_Query $wp_query
 * @param string $nav_id DOM ID for this navigation
 * @since BuddyPress (1.5)
 */
function bp_dtheme_content_nav( $nav_id ) {
	global $wp_query;

	if ( !empty( $wp_query->max_num_pages ) && $wp_query->max_num_pages > 1 ) : ?>

		<div id="<?php echo $nav_id; ?>" class="navigation">
			<div class="alignleft"><?php next_posts_link( __( '&larr; Previous Entries', 'buddypress' ) ); ?></div>
			<div class="alignright"><?php previous_posts_link( __( 'Next Entries &rarr;', 'buddypress' ) ); ?></div>
		</div><!-- #<?php echo $nav_id; ?> -->

	<?php endif;
}
endif;

/**
 * Adds the no-js class to the body tag.
 *
 * This function ensures that the <body> element will have the 'no-js' class by default. If you're
 * using JavaScript for some visual functionality in your theme, and you want to provide noscript
 * support, apply those styles to body.no-js.
 *
 * The no-js class is removed by the JavaScript created in bp_dtheme_remove_nojs_body_class().
 *
 * @package BuddyPress
 * @since BuddyPress (1.5).1
 * @see bp_dtheme_remove_nojs_body_class()
 */
function bp_dtheme_add_nojs_body_class( $classes ) {
	$classes[] = 'no-js';
	return array_unique( $classes );
}
add_filter( 'bp_get_the_body_class', 'bp_dtheme_add_nojs_body_class' );

/**
 * Dynamically removes the no-js class from the <body> element.
 *
 * By default, the no-js class is added to the body (see bp_dtheme_add_no_js_body_class()). The
 * JavaScript in this function is loaded into the <body> element immediately after the <body> tag
 * (note that it's hooked to bp_before_header), and uses JavaScript to switch the 'no-js' body class
 * to 'js'. If your theme has styles that should only apply for JavaScript-enabled users, apply them
 * to body.js.
 *
 * This technique is borrowed from WordPress, wp-admin/admin-header.php.
 *
 * @package BuddyPress
 * @since BuddyPress (1.5).1
 * @see bp_dtheme_add_nojs_body_class()
 */
function bp_dtheme_remove_nojs_body_class() {
?><script type="text/javascript">//<![CDATA[
(function(){var c=document.body.className;c=c.replace(/no-js/,'js');document.body.className=c;})();
//]]></script>
<?php
}
add_action( 'bp_before_header', 'bp_dtheme_remove_nojs_body_class' );

?>
