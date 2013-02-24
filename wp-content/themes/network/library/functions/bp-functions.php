<?php
if ( !function_exists( 'bp_is_active' ) )
	return;
	
require( TEMPLATEPATH . '/_inc/ajax.php' );

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


if ( !function_exists( 'bp_dtheme_enqueue_scripts' ) ) :
/**
 * Enqueue theme javascript safely
 *
 * @see http://codex.wordpress.org/Function_Reference/wp_enqueue_script
 * @since 1.5
 */
function bp_dtheme_enqueue_scripts() {
	// Bump this when changes are made to bust cache
	$version = '20110804';

	// Enqueue the global JS - Ajax will not work without it
	wp_enqueue_script( 'dtheme-ajax-js', get_template_directory_uri() . '/_inc/global.js', array( 'jquery' ), $version );

	// Add words that we need to use in JS to the end of the page so they can be translated and still used.
	$params = array(
		'my_favs'           => __( 'My Favorites', 'network' ),
		'accepted'          => __( 'Accepted', 'network' ),
		'rejected'          => __( 'Rejected', 'network' ),
		'show_all_comments' => __( 'Show all comments for this thread', 'network' ),
		'show_all'          => __( 'Show all', 'network' ),
		'comments'          => __( 'comments', 'network' ),
		'close'             => __( 'Close', 'network' ),
		'view'              => __( 'View', 'network' )
	);

	wp_localize_script( 'dtheme-ajax-js', 'BP_DTheme', $params );
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
function bp_dtheme_show_notice() {
	global $pagenow;

	// Bail if bp-default theme was not just activated
	if ( empty( $_GET['activated'] ) || ( 'themes.php' != $pagenow ) || !is_admin() )
		return;

	?>

	<div id="message" class="updated fade">
		<p><?php printf( __( 'Theme activated! This theme contains <a href="%s">custom header image</a> support and <a href="%s">sidebar widgets</a>.', 'network' ), admin_url( 'themes.php?page=custom-header' ), admin_url( 'widgets.php' ) ) ?></p>
	</div>

	<style type="text/css">#message2, #message0 { display: none; }</style>

	<?php
}
add_action( 'admin_notices', 'bp_dtheme_show_notice' );
endif;

if ( !function_exists( 'bp_dtheme_sidebar_login_redirect_to' ) ) :
function bp_dtheme_sidebar_login_redirect_to() {
	$redirect_to = apply_filters( 'bp_no_access_redirect', !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '' );
?>
	<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
<?php
}
add_action( 'bp_sidebar_login_form', 'bp_dtheme_sidebar_login_redirect_to' );
endif;

function bp_network_add_top_section_profile_page() {
	global $bp;
?>
	<div class="profile-left-nav">

		<?php locate_template( array( 'members/single/member-header.php' ), true ) ?>
							
		<?php
					
			$option_post = get_option('dev_network_allow_latest_blog_post');
			
			if ($option_post != "no") { 
			
			$blogpost = get_user_latest_blog_post($bp->displayed_user->id);
			
			if (sizeof($blogpost) > 0) {
			
				$link = $blogpost[0]->guid;
		
		?>	
		
		<div class="profile-latest-post-section">		
			<h5><?php printf( __( "Recent Blog Post", 'buddypress' )); ?> (<?php echo networktheme_core_get_last_activity($blogpost[0]->post_date); ?> ago):</h5>
			<div class="profile-latest-post">
				<h4><a href="<?php echo $link; ?>"><?php echo $blogpost[0]->post_title; ?></a></h4>
				<?php if ($blogpost[0]->post_excerpt != '') { ?>
				<p class="profile-latest-post-excerpt"><?php echo $blogpost[0]->post_excerpt; ?><?php } ?> <a href="<?php echo $link; ?>"><?php printf( __( "Read more.", 'buddypress' )); ?></a>. </p>
			</div>
		</div>
		<?php } else { ?>
			<h5><?php printf( __( "This user hasn't written a blog post yet.", 'buddypress' )); ?></h5>
		<?php } ?>
			<div class="clear"></div>
		
		<?php } ?>
		
		<?php do_action( 'template_notices' ) ?>

		<div class="profile-navigation">
			<ul id="profile-nav">
				<?php bp_get_displayed_user_nav() ?>
				<?php do_action( 'bp_member_options_nav' ) ?>
			</ul>
			<div class="clear"></div>
		</div>
<?			
}
add_action( 'bp_before_member_home_content', 'bp_network_add_top_section_profile_page' );
add_action( 'bp_before_member_plugin_template', 'bp_network_add_top_section_profile_page' );
add_action( 'bp_before_member_settings_template', 'bp_network_add_top_section_profile_page' );

function bp_network_add_bottom_section_profile_page() {
?>
	</div> <!-- profile-left-nav -->
			
		<?php locate_template( array( 'sidebar-profile.php' ), true ) ?>
			
	<div class="clear"></div>
<?
}
add_action( 'bp_after_member_home_content', 'bp_network_add_bottom_section_profile_page' );
add_action( 'bp_after_member_plugin_template', 'bp_network_add_bottom_section_profile_page' );
add_action( 'bp_after_member_settings_template', 'bp_network_add_bottom_section_profile_page' );

function bp_network_enhanced_profile_header() {
?>
<div class="profile-top-section">
	<div class="profile-user-main-info">
		<h1><a href="<?php bp_displayed_user_link() ?>"><?php bp_displayed_user_fullname() ?></a></h1>
		<?php if ( function_exists( 'bp_activity_latest_update' ) ) : ?>
			<p id="latest-update" class="last-active"><?php bp_last_activity( bp_displayed_user_id() ) ?></p>
		<?php endif; ?>
	</div>
	<?php if ( function_exists( 'network_badge_boxes' ) ) : ?><?php network_badge_boxes(); ?><?php endif; ?>
</div>
<?
}
add_action( 'bp_before_member_header', 'bp_network_enhanced_profile_header' );


?>