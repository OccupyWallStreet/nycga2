<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// **** "Notifications" Menu *********
function bp_adminbar_notifications_menu() {
	global $bp;

	if ( !is_user_logged_in() )
		return false;

	echo '<li id="bp-adminbar-notifications-menu"><a href="' . $bp->loggedin_user->domain . '">';
	_e( 'Notifications', 'buddypress' );

	if ( $notifications = bp_core_get_notifications_for_user( $bp->loggedin_user->id ) ) { ?>
		<span><?php echo count( $notifications ) ?></span>
	<?php
	}

	echo '</a>';
	echo '<ul>';

	if ( $notifications ) {
		$counter = 0;
		for ( $i = 0, $count = count( $notifications ); $i < $count; ++$i ) {
			$alt = ( 0 == $counter % 2 ) ? ' class="alt"' : ''; ?>

			<li<?php echo $alt ?>><?php echo $notifications[$i] ?></li>

			<?php $counter++;
		}
	} else { ?>

		<li><a href="<?php echo $bp->loggedin_user->domain ?>"><?php _e( 'No new notifications.', 'buddypress' ); ?></a></li>

	<?php
	}

	echo '</ul>';
	echo '</li>';
}
add_action( 'bp_adminbar_menus', 'bp_adminbar_notifications_menu', 8 );

// **** "Blog Authors" Menu (visible when not logged in) ********
function bp_adminbar_authors_menu() {
	global $bp, $wpdb;

	// Only for multisite
	if ( !is_multisite() )
		return false;

	// Hide on root blog
	if ( $wpdb->blogid == bp_get_root_blog_id() || !bp_is_active( 'blogs' ) )
		return false;

	$blog_prefix = $wpdb->get_blog_prefix( $wpdb->blogid );
	$authors     = $wpdb->get_results( "SELECT user_id, user_login, user_nicename, display_name, user_email, meta_value as caps FROM $wpdb->users u, $wpdb->usermeta um WHERE u.ID = um.user_id AND meta_key = '{$blog_prefix}capabilities' ORDER BY um.user_id" );

	if ( !empty( $authors ) ) {
		// This is a blog, render a menu with links to all authors
		echo '<li id="bp-adminbar-authors-menu"><a href="/">';
		_e('Blog Authors', 'buddypress');
		echo '</a>';

		echo '<ul class="author-list">';
		foreach( (array)$authors as $author ) {
			$caps = maybe_unserialize( $author->caps );
			if ( isset( $caps['subscriber'] ) || isset( $caps['contributor'] ) ) continue;

			echo '<li>';
			echo '<a href="' . bp_core_get_user_domain( $author->user_id, $author->user_nicename, $author->user_login ) . '">';
			echo bp_core_fetch_avatar( array( 'item_id' => $author->user_id, 'email' => $author->user_email, 'width' => 15, 'height' => 15 ) ) ;
 			echo ' ' . $author->display_name . '</a>';
			echo '<div class="admin-bar-clear"></div>';
			echo '</li>';
		}
		echo '</ul>';
		echo '</li>';
	}
}
add_action( 'bp_adminbar_menus', 'bp_adminbar_authors_menu', 12 );

/**
 * Adds an admin bar menu to any profile page providing site moderator actions
 * that allow capable users to clean up a users account.
 *
 * @package BuddyPress XProfile
 * @global $bp BuddyPress
 */
function bp_members_adminbar_admin_menu() {
	global $bp;

	// Only show if viewing a user
	if ( !$bp->displayed_user->id )
		return false;

	// Don't show this menu to non site admins or if you're viewing your own profile
	if ( !current_user_can( 'edit_users' ) || bp_is_my_profile() )
		return false; ?>

	<li id="bp-adminbar-adminoptions-menu">

		<a href=""><?php _e( 'Admin Options', 'buddypress' ) ?></a>

		<ul>
			<?php if ( bp_is_active( 'xprofile' ) ) : ?>

				<li><a href="<?php bp_members_component_link( 'profile', 'edit' ); ?>"><?php printf( __( "Edit %s's Profile", 'buddypress' ), esc_attr( $bp->displayed_user->fullname ) ) ?></a></li>

			<?php endif ?>

			<li><a href="<?php bp_members_component_link( 'profile', 'change-avatar' ); ?>"><?php printf( __( "Edit %s's Avatar", 'buddypress' ), esc_attr( $bp->displayed_user->fullname ) ) ?></a></li>

			<?php if ( !bp_core_is_user_spammer( $bp->displayed_user->id ) ) : ?>

				<li><a href="<?php echo wp_nonce_url( $bp->displayed_user->domain . 'admin/mark-spammer/', 'mark-unmark-spammer' ) ?>" class="confirm"><?php printf( __( "Mark as Spammer", 'buddypress' ), esc_attr( $bp->displayed_user->fullname ) ); ?></a></li>

			<?php else : ?>

				<li><a href="<?php echo wp_nonce_url( $bp->displayed_user->domain . 'admin/unmark-spammer/', 'mark-unmark-spammer' ) ?>" class="confirm"><?php _e( "Not a Spammer", 'buddypress' ) ?></a></li>

			<?php endif; ?>

			<li><a href="<?php echo wp_nonce_url( $bp->displayed_user->domain . 'admin/delete-user/', 'delete-user' ) ?>" class="confirm"><?php printf( __( "Delete %s's Account", 'buddypress' ), esc_attr( $bp->displayed_user->fullname ) ); ?></a></li>

			<?php do_action( 'bp_members_adminbar_admin_menu' ) ?>

		</ul>
	</li>

	<?php
}
add_action( 'bp_adminbar_menus', 'bp_members_adminbar_admin_menu', 20 );

?>