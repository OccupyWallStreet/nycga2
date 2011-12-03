<?php
/***** The following is optional and requires editing the original p2/js/P2.js file ******/

/******
 * Uncomment in the function getPosts between line 106-151 of P2.js
 * the code between and including "toggleUpdates('unewposts')" to
 * deactivate Go to homepage Notifications
 ******/

/******
 * Around line 235 find and change the following code in order to show Update Posted Notification:
 *
 *	if (isFirstFrontPage && result != "0") {
 *		getPosts(false);
 *	} else if (!isFirstFrontPage && result != "0") {
 *		newNotification(p2txt.update_posted);
 *	}
 *
 * to:
 *
		if (result != "0") {
			newNotification(p2txt.update_posted);
		}
 *
 *****/

/******
 * In p2/inc/ajax.php add the following categories to "$accepted_post_cats" array:
 * 'photo', 'video', 'featured' around line 155
 ******/

/********************************************************
 * START P2 FUNCTIONS
 ********************************************************/

function status_excerpt() {
	global $post;

	$excerpt = get_the_excerpt();
	$maxchar = 140;

	$status .= substr( $excerpt, 0, $maxchar );
	$status .= '... <a href="' . get_permalink() .'" title="' . __('Read On', 'groupblog') . '">&raquo;</a>';

	echo $status;
}

function catch_that_image() {
  global $post, $posts;
  $first_img = '';
  ob_start();
  ob_end_clean();
  $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
  $first_img = $matches [1] [0];

  if(empty($first_img)){ //Defines a default image
    $first_img = WP_CONTENT_URL . '/themes/p2-buddypress/groupblog/_inc/i/noimage.jpg';
  }
  return $first_img;
}

/********************************************************
 * START GROUPBLOG FUNCTIONS
 ********************************************************/

function blog_id() {
	echo get_blog_id();
}
	function get_blog_id() {
		global $blog_id;

		return $blog_id;
	}

/*
 * is_home_redirect()
 *
 */
function is_home_redirect() {
	$checks = get_site_option('bp_groupblog_blog_defaults_options');
	if ( $checks['deep_group_integration'] == 1 )
		return true;
	else
		return false;
}

/**
 * bp_p2_post_update()
 *
 * We duplicate this function form inc/ajax.php
 * In order to update the stream through ajax but trying to post the right
 * activity_id, we need to figure out how to do that before sending the info.
 *
 * AJAX update posting
 */
function bp_p2_post_update() {
	global $bp, $activities_template;

	/* Check the nonce */
	check_admin_referer( 'p2_post_update', '_wpnonce_p2_post_update' );

	if ( !is_user_logged_in() ) {
		echo '-1';
		return false;
	}

	if ( empty( $_POST['content'] ) ) {
		echo '-1<div id="message" class="error"><p>' . __( 'Please enter some content to post.', 'buddypress' ) . '</p></div>';
		return false;
	}

	$groupblog_id = $_POST['item_id'];
	$post_id = apply_filters( 'bp_get_activity_secondary_item_id', $activities_template->activity->secondary_item_id );
	// $post_id = If we would only already know the get_the_ID();

	if ( bp_has_activities ( 'max=1&primary_id=' . $groupblog_id . '&secondary_id=' . $post_id ) ) {
		while ( bp_activities() ) : bp_the_activity();

			/*
if ( groupblog_current_layout() == 'magazine' )
				include( 'groupblog/layouts/magazine-entry.php' );
			elseif ( groupblog_current_layout() == 'media' )
				include( 'groupblog/layouts/media-entry.php' );
			else
*/if ( groupblog_current_layout() == 'microblog' )
				include( 'groupblog/layouts/microblog-entry.php' );
			else
				include( WP_PLUGIN_DIR . '/buddypress/bp-themes/bp-default/activity/entry.php' );

		endwhile;
	}

}
add_action( 'wp_ajax_p2_post_update', 'bp_p2_post_update' );

/**
 * bp_groupblog_options_nav()
 *
 * Manually create the navigation for the group since we can't fetch any other way.
 * You should manually add items if you have third party plugins that add a menu item.
 *
 * The BuddyPress function we duplicate is called bp_get_options_nav()
 */
function bp_groupblog_options_nav() {
  global $bp;
  $checks = get_site_option('bp_groupblog_blog_defaults_options');
  ?>

	  <li id="home-personal-li"<?php if ( $checks['deep_group_integration'] ) : ?> class="current selected"<?php endif; ?>>
			<a id="home" href="<?php bp_group_permalink() ?>"><?php _e( 'Home', 'groupblog' ); ?></a>
		</li>

    <?php if ( groups_is_user_admin( $bp->loggedin_user->id, bp_get_group_id() ) || groups_is_user_mod( $bp->loggedin_user->id, bp_get_group_id() ) ) : ?>
			<li id="admin-personal-li" >
				<a id="admin" href="<?php bp_group_permalink() ?>admin/"><?php _e( 'Admin', 'groupblog' ); ?></a>
			</li>
		<?php endif; ?>

		<?php if ( bp_group_is_visible() ) : ?>

			<?php if ( bp_groupblog_is_blog_enabled ( bp_get_group_id() ) ) : ?>
				<?php if ( !$checks['deep_group_integration'] ) : ?>
					<li id="<?php echo BP_GROUPBLOG_SLUG; ?>-personal-li"<?php //if ( is_page() ) : ?> class="current selected"<?php //endif; ?>>
						<a id="<?php echo BP_GROUPBLOG_SLUG; ?>" href="<?php bp_group_permalink() ?>blog/"><?php _e( 'Blog', 'groupblog' ); ?></a>
					</li>
				<?php endif; ?>
		  <?php endif; ?>

			<?php if ( bp_is_active( 'forums' ) && ( function_exists( 'bp_forums_is_installed_correctly' ) && bp_group_is_forum_enabled() && !(int) bp_get_option( 'bp-disable-forum-directory' ) ) && bp_forums_is_installed_correctly() ) : ?>
				<li id="<?php echo BP_FORUMS_SLUG; ?>-personal-li" >
					<a id="<?php echo BP_FORUMS_SLUG; ?>" href="<?php bp_group_permalink() ?>forum/"><?php _e( 'Forum', 'groupblog' ); ?></a>
				</li>
			<?php endif; ?>

			<li id="<?php echo BP_MEMBERS_SLUG; ?>-personal-li" >
				<a id="<?php echo BP_MEMBERS_SLUG; ?>" href="<?php bp_group_permalink() ?>members/"><?php _e( 'Members', 'groupblog' ); ?> (<?php bp_group_total_members() ?>)</a>
			</li>

			<li id="invite-personal-li" >
				<a id="invite" href="<?php bp_group_permalink() ?>send-invites/"><?php _e( 'Send Invites', 'groupblog' ); ?></a>
			</li>

		<?php elseif ( !bp_group_is_visible() && bp_get_group_status() != 'hidden' ) : ?>

			<li id="request-membership-personal-li" >
				<a id="request-membership" href="<?php bp_group_permalink() ?>request-membership/"><?php _e( 'Request Membership', 'groupblog' ); ?></a>
			</li>

		<?php endif; ?>

	<?php
}

/* Load the javascript for the theme */
wp_enqueue_script( 'p2theme-ajax-js', get_stylesheet_directory_uri() . '/groupblog/_inc/custom.js', array( 'jquery' ) );

/********************************************************
 * START BUDDYPRESS FUNCTIONS
 ********************************************************/

/* Stop the theme from killing WordPress if BuddyPress is not enabled. */
if ( !class_exists( 'BP_Core_User' ) )
	return false;

/* Load the AJAX functions for the theme */
require_once( WP_PLUGIN_DIR . '/buddypress/bp-themes/bp-default/_inc/ajax.php' );

function bp_dtheme_enqueue_styles() {
	// Default CSS
	wp_enqueue_style( 'bp-default-main', WP_PLUGIN_URL . '/buddypress/bp-themes/bp-default/_inc/css/default.css', array() );

	// Default CSS RTL
	if ( is_rtl() )
		wp_enqueue_style( 'bp-default-main-rtl', WP_PLUGIN_URL . '/buddypress/bp-themes/bp-default/_inc/css/default-rtl.css', array( 'bp-default-main' ) );

}
add_action( 'wp_print_styles', 'bp_dtheme_enqueue_styles' );

/* Load the javascript for the theme */
wp_enqueue_script( 'dtheme-ajax-js', WP_PLUGIN_URL . '/buddypress/bp-themes/bp-default/_inc/global.js', array( 'jquery' ) );

/* Add the JS needed for blog comment replies */
function bp_dtheme_add_blog_comments_js() {
	if ( is_singular() ) wp_enqueue_script( 'comment-reply' );
}
add_action( 'template_redirect', 'bp_dtheme_add_blog_comments_js' );

/* HTML for outputting blog comments as defined by the WP comment API */
function bp_dtheme_blog_comments( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment; ?>

	<?php if ( 'pingback' == $comment->comment_type ) return false; ?>

	<li id="comment-<?php comment_ID(); ?>">
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

		<div class="comment-content">

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
	</li>
<?php
}

/* Add words that we need to use in JS to the end of the page so they can be translated and still used. */
function bp_dtheme_js_terms() { ?>
<script type="text/javascript">
	var bp_terms_my_favs = '<?php _e( "My Favorites", "buddypress" ) ?>';
	var bp_terms_accepted = '<?php _e( "Accepted", "buddypress" ) ?>';
	var bp_terms_rejected = '<?php _e( "Rejected", "buddypress" ) ?>';
	var bp_terms_show_all_comments = '<?php _e( "Show all comments for this thread", "buddypress" ) ?>';
	var bp_terms_show_all = '<?php _e( "Show all", "buddypress" ) ?>';
	var bp_terms_comments = '<?php _e( "comments", "buddypress" ) ?>';
	var bp_terms_close = '<?php _e( "Close", "buddypress" ) ?>';
	var bp_terms_mention_explain = '<?php printf( __( "%s is a unique identifier for %s that you can type into any message on this site. %s will be sent a notification and a link to your message any time you use it.", "buddypress" ), '@' . bp_get_displayed_user_username(), bp_get_user_firstname(bp_get_displayed_user_fullname()), bp_get_user_firstname(bp_get_displayed_user_fullname()) ); ?>';
	</script>
<?php
}
add_action( 'wp_footer', 'bp_dtheme_js_terms' );