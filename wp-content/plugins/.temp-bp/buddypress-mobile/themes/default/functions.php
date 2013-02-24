<?php
define ( 'BP_DISABLE_ADMIN_BAR', true );
define( 'BP_DTHEME_DISABLE_CUSTOM_HEADER', true );

remove_action( ‘wp_print_styles’, ‘bp_tpack_enqueue_styles’ );
remove_filter( 'wp_nav_menu', 'remove_ul' );

// Stop the theme from killing WordPress if BuddyPress is not enabled.
if ( !class_exists( 'BP_Core_User' ) )
	return false;

// Load the AJAX functions for the theme
require_once( WP_PLUGIN_DIR . '/buddypress/bp-themes/bp-default/_inc/ajax.php' );

// Load the javascript for the theme
wp_enqueue_script( 'dtheme-ajax-js', get_template_directory_uri() . '/_inc/global.js', array( 'jquery' ) );

// Add words that we need to use in JS to the end of the page so they can be translated and still used.
$params = array(
	'my_favs'           => __( 'My Favorites', 'buddypress' ),
	'accepted'          => __( 'Accepted', 'buddypress' ),
	'rejected'          => __( 'Rejected', 'buddypress' ),
	'show_all_comments' => __( 'Show all comments for this thread', 'buddypress' ),
	'show_all'          => __( 'Show all', 'buddypress' ),
	'comments'          => __( 'comments', 'buddypress' ),
	'close'             => __( 'Close', 'buddypress' ),
	'mention_explain'   => sprintf( __( "%s is a unique identifier for %s that you can type into any message on this site. %s will be sent a notification and a link to your message any time you use it.", 'buddypress' ), '@' . bp_get_displayed_user_username(), bp_get_user_firstname( bp_get_displayed_user_fullname() ), bp_get_user_firstname( bp_get_displayed_user_fullname() ) )
);
wp_localize_script( 'dtheme-ajax-js', 'BP_DTheme', $params );

/**
 * Add the JS needed for blog comment replies
 *
 * @package BuddyPress Theme
 * @since 1.2
 */
function bp_dtheme_add_blog_comments_js() {
	if ( is_singular() ) wp_enqueue_script( 'comment-reply' );
}
add_action( 'template_redirect', 'bp_dtheme_add_blog_comments_js' );


/**
 * HTML for outputting blog comments as defined by the WP comment API
 *
 * @param mixed $comment Comment record from database
 * @param array $args Arguments from wp_list_comments() call
 * @param int $depth Comment nesting level
 * @see wp_list_comments()
 * @package BuddyPress Theme
 * @since 1.2
 */
function bp_dtheme_blog_comments( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment; ?>

	<?php if ( 'pingback' == $comment->comment_type ) return false; ?>

	<li id="comment-<?php comment_ID(); ?>">

		<div class="comment-content">
		
				<div class="comment-avatar-box">
			<div class="avb">
				<a href="<?php echo get_comment_author_url() ?>" rel="nofollow">
					<?php if ( $comment->user_id ) : ?>
						<?php echo bp_core_fetch_avatar( array( 'item_id' => $comment->user_id, 'width' => 50, 'height' => 50, 'email' => $comment->comment_author_email ) ); ?>
					<?php else : ?>
						<?php echo get_avatar( $comment, 50 ) ?>
					<?php endif; ?>
				</a>
			</div>
		</div>

		
		

			<div class="comment-meta">
				<a href="<?php echo get_comment_author_url() ?>" rel="nofollow"><?php echo get_comment_author(); ?></a> <?php _e( 'said:', 'buddypress' ) ?>
				<em><?php _e( 'On', 'buddypress' ) ?> <a href="#comment-<?php comment_ID() ?>" title=""><?php comment_date() ?></a></em>
			</div>

			<?php if ( $comment->comment_approved == '0' ) : ?>
			 	<em class="moderate"><?php _e('Your comment is awaiting moderation.'); ?></em><br />
			<?php endif; ?>

			<?php comment_text() ?>

			<div class="comment-options">
				<?php echo comment_reply_link( array('depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ?>
				<?php edit_comment_link( __( 'Edit' ),'','' ); ?>
			</div>

		</div>
<?php
}

/**
 * Filter the dropdown for selecting the page to show on front to include "Activity Stream"
 *
 * @param string $page_html A list of pages as a dropdown (select list)
 * @see wp_dropdown_pages()
 * @return string
 * @package BuddyPress Theme
 * @since 1.2
 */
function bp_dtheme_wp_pages_filter( $page_html ) {
	if ( !bp_is_active( 'activity' ) )
		return $page_html;

	if ( 'page_on_front' != substr( $page_html, 14, 13 ) )
		return $page_html;

	$selected = false;
	$page_html = str_replace( '</select>', '', $page_html );

	if ( bp_dtheme_page_on_front() == 'activity' )
		$selected = ' selected="selected"';

	$page_html .= '<option class="level-0" value="activity"' . $selected . '>' . __( 'Activity Stream', 'buddypress' ) . '</option></select>';
	return $page_html;
}
add_filter( 'wp_dropdown_pages', 'bp_dtheme_wp_pages_filter' );

/**
 * Hijack the saving of page on front setting to save the activity stream setting
 *
 * @param $string $oldvalue Previous value of get_option( 'page_on_front' )
 * @param $string $oldvalue New value of get_option( 'page_on_front' )
 * @return string
 * @package BuddyPress Theme
 * @since 1.2
 */
function bp_dtheme_page_on_front_update( $oldvalue, $newvalue ) {
	if ( !is_admin() || !is_super_admin() )
		return false;

	if ( 'activity' == $_POST['page_on_front'] )
		return 'activity';
	else
		return $oldvalue;
}
add_action( 'pre_update_option_page_on_front', 'bp_dtheme_page_on_front_update', 10, 2 );

/**
 * Load the activity stream template if settings allow
 *
 * @param string $template Absolute path to the page template 
 * @return string
 * @global WP_Query $wp_query WordPress query object
 * @package BuddyPress Theme
 * @since 1.2
 */
function bp_dtheme_page_on_front_template( $template ) {
	global $wp_query;

	if ( empty( $wp_query->post->ID ) )
		return locate_template( array( 'activity/index.php' ), false );
	else
		return $template;
}
add_filter( 'page_template', 'bp_dtheme_page_on_front_template' );

/**
 * Return the ID of a page set as the home page.
 *
 * @return false|int ID of page set as the home page
 * @package BuddyPress Theme
 * @since 1.2
 */
function bp_dtheme_page_on_front() {
	if ( 'page' != get_option( 'show_on_front' ) )
		return false;

	return apply_filters( 'bp_dtheme_page_on_front', get_option( 'page_on_front' ) );
}

/**
 * Force the page ID as a string to stop the get_posts query from kicking up a fuss.
 *
 * @global WP_Query $wp_query WordPress query object
 * @package BuddyPress Theme
 * @since 1.2
 */
function bp_dtheme_fix_get_posts_on_activity_front() {
	global $wp_query;

	if ( !empty($wp_query->query_vars['page_id']) && 'activity' == $wp_query->query_vars['page_id'] )
		$wp_query->query_vars['page_id'] = '"activity"';
}
add_action( 'pre_get_posts', 'bp_dtheme_fix_get_posts_on_activity_front' );

/**
 * WP 3.0 requires there to be a non-null post in the posts array
 *
 * @param array $posts Posts as retrieved by WP_Query
 * @global WP_Query $wp_query WordPress query object
 * @return array
 * @package BuddyPress Theme
 * @since 1.2.5
 */
function bp_dtheme_fix_the_posts_on_activity_front( $posts ) {
	global $wp_query;

	// NOTE: the double quotes around '"activity"' are thanks to our previous function bp_dtheme_fix_get_posts_on_activity_front()
	if ( empty( $posts ) && !empty( $wp_query->query_vars['page_id'] ) && '"activity"' == $wp_query->query_vars['page_id'] )
		$posts = array( (object) array( 'ID' => 'activity' ) );

	return $posts;
}
add_filter( 'the_posts', 'bp_dtheme_fix_the_posts_on_activity_front' );

/**
 * bp_dtheme_activity_secondary_avatars()
 *
 * Add secondary avatar image to this activity stream's record, if supported
 *
 * @param string $action The text of this activity
 * @param BP_Activity_Activity $activity Activity object
 * @return string
 * @package BuddyPress Theme
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



// Member Buttons
if ( bp_is_active( 'friends' ) )
	add_action( 'bp_member_header_actions',    'bp_add_friend_button' );

if ( bp_is_active( 'activity' ) )
	add_action( 'bp_member_header_actions',    'bp_send_public_message_button' );

if ( bp_is_active( 'messages' ) )
	add_action( 'bp_member_header_actions',    'bp_send_private_message_button' );

// Group Buttons
if ( bp_is_active( 'groups' ) ) {
	add_action( 'bp_group_header_actions',     'bp_group_join_button' );
	add_action( 'bp_group_header_actions',     'bp_group_new_topic_button' );
	add_action( 'bp_directory_groups_actions', 'bp_group_join_button' );
}

// Blog Buttons
if ( bp_is_active( 'blogs' ) )
	add_action( 'bp_directory_blogs_actions',  'bp_blogs_visit_blog_button' );

?>
