<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// *** "My Blogs" Menu ********
function bp_adminbar_blogs_menu() {
	global $bp;

	if ( !is_user_logged_in() || !bp_is_active( 'blogs' ) )
		return false;

	if ( !is_multisite() )
		return false;

	if ( !$blogs = wp_cache_get( 'bp_blogs_of_user_' . $bp->loggedin_user->id . '_inc_hidden', 'bp' ) ) {
		$blogs = bp_blogs_get_blogs_for_user( $bp->loggedin_user->id, true );
		wp_cache_set( 'bp_blogs_of_user_' . $bp->loggedin_user->id . '_inc_hidden', $blogs, 'bp' );
	}

	$counter = 0;
	if ( is_array( $blogs['blogs'] ) && (int)$blogs['count'] ) {

		echo '<li id="bp-adminbar-blogs-menu"><a href="' . trailingslashit( $bp->loggedin_user->domain . bp_get_blogs_slug() ) . '">';

		_e( 'My Sites', 'buddypress' );

		echo '</a>';
		echo '<ul>';

		foreach ( (array)$blogs['blogs'] as $blog ) {
			$alt      = ( 0 == $counter % 2 ) ? ' class="alt"' : '';
			$site_url = esc_attr( $blog->siteurl );

			echo '<li' . $alt . '>';
			echo '<a href="' . $site_url . '">' . esc_html( $blog->name ) . '</a>';
			echo '<ul>';
			echo '<li class="alt"><a href="' . $site_url . 'wp-admin/">' . __( 'Dashboard', 'buddypress' ) . '</a></li>';
			echo '<li><a href="' . $site_url . 'wp-admin/post-new.php">' . __( 'New Post', 'buddypress' ) . '</a></li>';
			echo '<li class="alt"><a href="' . $site_url . 'wp-admin/edit.php">' . __( 'Manage Posts', 'buddypress' ) . '</a></li>';
			echo '<li><a href="' . $site_url . 'wp-admin/edit-comments.php">' . __( 'Manage Comments', 'buddypress' ) . '</a></li>';
			echo '</ul>';

			do_action( 'bp_adminbar_blog_items', $blog );

			echo '</li>';
			$counter++;
		}

		$alt = ( 0 == $counter % 2 ) ? ' class="alt"' : '';

		if ( bp_blog_signup_enabled() ) {
			echo '<li' . $alt . '>';
			echo '<a href="' . bp_get_root_domain() . '/' . bp_get_blogs_root_slug() . '/create/">' . __( 'Create a Site!', 'buddypress' ) . '</a>';
			echo '</li>';
		}

		echo '</ul>';
		echo '</li>';
	}
}
add_action( 'bp_adminbar_menus', 'bp_adminbar_blogs_menu', 6 );

?>