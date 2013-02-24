<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Output the friends component slug
 *
 * @package BuddyPress
 * @subpackage Friends Template
 * @since 1.5
 *
 * @uses bp_get_friends_slug()
 */
function bp_friends_slug() {
	echo bp_get_friends_slug();
}
	/**
	 * Return the friends component slug
	 *
	 * @package BuddyPress
	 * @subpackage Friends Template
	 * @since 1.5
	 */
	function bp_get_friends_slug() {
		global $bp;
		return apply_filters( 'bp_get_friends_slug', $bp->friends->slug );
	}

/**
 * Output the friends component root slug
 *
 * @package BuddyPress
 * @subpackage Friends Template
 * @since 1.5
 *
 * @uses bp_get_friends_root_slug()
 */
function bp_friends_root_slug() {
	echo bp_get_friends_root_slug();
}
	/**
	 * Return the friends component root slug
	 *
	 * @package BuddyPress
	 * @subpackage Friends Template
	 * @since 1.5
	 */
	function bp_get_friends_root_slug() {
		global $bp;
		return apply_filters( 'bp_get_friends_root_slug', $bp->friends->root_slug );
	}

/**
 * Displays Friends header tabs
 *
 * @package BuddyPress
 * @todo Deprecate?
 */
function bp_friends_header_tabs() {
	global $bp; ?>

	<li<?php if ( !bp_action_variable( 0 ) || bp_is_action_variable( 'recently-active', 0 ) ) : ?> class="current"<?php endif; ?>><a href="<?php echo $bp->displayed_user->domain . bp_get_friends_slug() ?>/my-friends/recently-active"><?php _e( 'Recently Active', 'buddypress' ) ?></a></li>
	<li<?php if ( bp_is_action_variable( 'newest', 0 ) ) : ?> class="current"<?php endif; ?>><a href="<?php echo $bp->displayed_user->domain . bp_get_friends_slug() ?>/my-friends/newest"><?php _e( 'Newest', 'buddypress' ) ?></a></li>
	<li<?php if ( bp_is_action_variable( 'alphabetically', 0 ) ) : ?> class="current"<?php endif; ?>><a href="<?php echo $bp->displayed_user->domain . bp_get_friends_slug() ?>/my-friends/alphabetically"><?php _e( 'Alphabetically', 'buddypress' ) ?></a></li>

<?php
	do_action( 'friends_header_tabs' );
}

/**
 * Filters the title for the Friends component
 *
 * @package BuddyPress
 * @todo Deprecate?
 */
function bp_friends_filter_title() {
	$current_filter = bp_action_variable( 0 );

	switch ( $current_filter ) {
		case 'recently-active': default:
			_e( 'Recently Active', 'buddypress' );
			break;
		case 'newest':
			_e( 'Newest', 'buddypress' );
			break;
		case 'alphabetically':
			_e( 'Alphabetically', 'buddypress' );
			break;
	}
}

function bp_friends_random_friends() {
	global $bp;

	if ( !$friend_ids = wp_cache_get( 'friends_friend_ids_' . $bp->displayed_user->id, 'bp' ) ) {
		$friend_ids = BP_Friends_Friendship::get_random_friends( $bp->displayed_user->id );
		wp_cache_set( 'friends_friend_ids_' . $bp->displayed_user->id, $friend_ids, 'bp' );
	} ?>

	<div class="info-group">
		<h4><?php bp_word_or_name( __( "My Friends", 'buddypress' ), __( "%s's Friends", 'buddypress' ) ) ?>  (<?php echo BP_Friends_Friendship::total_friend_count( $bp->displayed_user->id ) ?>) <span><a href="<?php echo $bp->displayed_user->domain . bp_get_friends_slug() ?>"><?php _e('See All', 'buddypress') ?></a></span></h4>

		<?php if ( $friend_ids ) { ?>

			<ul class="horiz-gallery">

			<?php for ( $i = 0, $count = count( $friend_ids ); $i < $count; ++$i ) { ?>

				<li>
					<a href="<?php echo bp_core_get_user_domain( $friend_ids[$i] ) ?>"><?php echo bp_core_fetch_avatar( array( 'item_id' => $friend_ids[$i], 'type' => 'thumb' ) ) ?></a>
					<h5><?php echo bp_core_get_userlink($friend_ids[$i]) ?></h5>
				</li>

			<?php } ?>

			</ul>

		<?php } else { ?>

			<div id="message" class="info">
				<p><?php bp_word_or_name( __( "You haven't added any friend connections yet.", 'buddypress' ), __( "%s hasn't created any friend connections yet.", 'buddypress' ) ) ?></p>
			</div>

		<?php } ?>
		<div class="clear"></div>
	</div>
<?php
}

/**
 * Pull up a group of random members, and display some profile data about them
 *
 * This function is no longer used by BuddyPress core.
 *
 * @package BuddyPress
 *
 * @param int $total_members The number of members to retrieve
 */
function bp_friends_random_members( $total_members = 5 ) {
	global $bp;

	if ( !$user_ids = wp_cache_get( 'friends_random_users', 'bp' ) ) {
		$user_ids = BP_Core_User::get_users( 'random', $total_members );
		wp_cache_set( 'friends_random_users', $user_ids, 'bp' );
	}

	?>

	<?php if ( $user_ids['users'] ) { ?>

		<ul class="item-list" id="random-members-list">

		<?php for ( $i = 0, $count = count( $user_ids['users'] ); $i < $count; ++$i ) { ?>

			<li>
				<a href="<?php echo bp_core_get_user_domain( $user_ids['users'][$i]->id ) ?>"><?php echo bp_core_fetch_avatar( array( 'item_id' => $user_ids['users'][$i]->id, 'type' => 'thumb' ) ) ?></a>
				<h5><?php echo bp_core_get_userlink( $user_ids['users'][$i]->id ) ?></h5>

				<?php if ( bp_is_active( 'xprofile' ) ) { ?>

					<?php $random_data = xprofile_get_random_profile_data( $user_ids['users'][$i]->id, true ); ?>

					<div class="profile-data">
						<p class="field-name"><?php echo $random_data[0]->name ?></p>

						<?php echo $random_data[0]->value ?>

					</div>

				<?php } ?>

				<div class="action">

					<?php if ( bp_is_active( 'friends' ) ) { ?>

						<?php bp_add_friend_button( $user_ids['users'][$i]->id ) ?>

					<?php } ?>

				</div>
			</li>

		<?php } ?>

		</ul>

	<?php } else { ?>

		<div id="message" class="info">
			<p><?php _e( "There aren't enough site members to show a random sample just yet.", 'buddypress' ) ?></p>
		</div>

	<?php } ?>
<?php
}

function bp_friend_search_form() {
	global $friends_template, $bp;

	$action = $bp->displayed_user->domain . bp_get_friends_slug() . '/my-friends/search/';
	$label = __( 'Filter Friends', 'buddypress' ); ?>

		<form action="<?php echo $action ?>" id="friend-search-form" method="post">

			<label for="friend-search-box" id="friend-search-label"><?php echo $label ?></label>
			<input type="search" name="friend-search-box" id="friend-search-box" value="<?php echo $value ?>"<?php echo $disabled ?> />

			<?php wp_nonce_field( 'friends_search', '_wpnonce_friend_search' ) ?>

			<input type="hidden" name="initiator" id="initiator" value="<?php echo esc_attr( $bp->displayed_user->id ) ?>" />

		</form>

	<?php
}

function bp_member_add_friend_button() {
	global $members_template;

	if ( !isset( $members_template->member->is_friend ) || null === $members_template->member->is_friend )
		$friend_status = 'not_friends';
	else
		$friend_status = ( 0 == $members_template->member->is_friend ) ? 'pending' : 'is_friend';

	echo bp_get_add_friend_button( $members_template->member->id, $friend_status );
}
add_action( 'bp_directory_members_actions', 'bp_member_add_friend_button' );

function bp_member_total_friend_count() {
	global $members_template;

	echo bp_get_member_total_friend_count();
}
	function bp_get_member_total_friend_count() {
		global $members_template;

		if ( 1 == (int) $members_template->member->total_friend_count )
			return apply_filters( 'bp_get_member_total_friend_count', sprintf( __( '%d friend', 'buddypress' ), (int) $members_template->member->total_friend_count ) );
		else
			return apply_filters( 'bp_get_member_total_friend_count', sprintf( __( '%d friends', 'buddypress' ), (int) $members_template->member->total_friend_count ) );
	}

/**
 * bp_potential_friend_id( $user_id )
 *
 * Outputs the ID of the potential friend
 *
 * @uses bp_get_potential_friend_id()
 * @param <type> $user_id
 */
function bp_potential_friend_id( $user_id = 0 ) {
	echo bp_get_potential_friend_id( $user_id );
}
	/**
	 * bp_get_potential_friend_id( $user_id )
	 *
	 * Returns the ID of the potential friend
	 *
	 * @global object $bp
	 * @global object $friends_template
	 * @param int $user_id
	 * @return int ID of potential friend
	 */
	function bp_get_potential_friend_id( $user_id = 0 ) {
		global $bp, $friends_template;

		if ( empty( $user_id ) && isset( $friends_template->friendship->friend ) )
			$user_id = $friends_template->friendship->friend->id;
		else if ( empty( $user_id ) && !isset( $friends_template->friendship->friend ) )
			$user_id = $bp->displayed_user->id;

		return apply_filters( 'bp_get_potential_friend_id', (int)$user_id );
	}

/**
 * bp_is_friend( $user_id )
 *
 * Returns - 'is_friend', 'not_friends', 'pending'
 *
 * @global object $bp
 * @param int $potential_friend_id
 * @return string
 */
function bp_is_friend( $user_id = 0 ) {
	global $bp;

	if ( !is_user_logged_in() )
		return false;

	if ( empty( $user_id ) )
		$user_id = bp_get_potential_friend_id( $user_id );

	if ( $bp->loggedin_user->id == $user_id )
		return false;

	return apply_filters( 'bp_is_friend', friends_check_friendship_status( $bp->loggedin_user->id, $user_id ), $user_id );
}

function bp_add_friend_button( $potential_friend_id = 0, $friend_status = false ) {
	echo bp_get_add_friend_button( $potential_friend_id, $friend_status );
}
	function bp_get_add_friend_button( $potential_friend_id = 0, $friend_status = false ) {
		global $bp, $friends_template;

		if ( empty( $potential_friend_id ) )
			$potential_friend_id = bp_get_potential_friend_id( $potential_friend_id );

		$is_friend = bp_is_friend( $potential_friend_id );

		if ( empty( $is_friend ) )
			return false;

		switch ( $is_friend ) {
			case 'pending' :
				$button = array(
					'id'                => 'pending',
					'component'         => 'friends',
					'must_be_logged_in' => true,
					'block_self'        => true,
					'wrapper_class'     => 'friendship-button pending',
					'wrapper_id'        => 'friendship-button-' . $potential_friend_id,
					'link_href'         => trailingslashit( $bp->loggedin_user->domain . bp_get_friends_slug() . '/requests' ),
					'link_text'         => __( 'Friendship Requested', 'buddypress' ),
					'link_title'        => __( 'Friendship Requested', 'buddypress' ),
					'link_class'        => 'friendship-button pending requested'
				);
				break;

			case 'is_friend' :
				$button = array(
					'id'                => 'is_friend',
					'component'         => 'friends',
					'must_be_logged_in' => true,
					'block_self'        => false,
					'wrapper_class'     => 'friendship-button is_friend',
					'wrapper_id'        => 'friendship-button-' . $potential_friend_id,
					'link_href'         => wp_nonce_url( $bp->loggedin_user->domain . bp_get_friends_slug() . '/remove-friend/' . $potential_friend_id . '/', 'friends_remove_friend' ),
					'link_text'         => __( 'Cancel Friendship', 'buddypress' ),
					'link_title'        => __( 'Cancel Friendship', 'buddypress' ),
					'link_id'           => 'friend-' . $potential_friend_id,
					'link_rel'          => 'remove',
					'link_class'        => 'friendship-button is_friend remove'
				);
				break;

			default:
				$button = array(
					'id'                => 'not_friends',
					'component'         => 'friends',
					'must_be_logged_in' => true,
					'block_self'        => true,
					'wrapper_class'     => 'friendship-button not_friends',
					'wrapper_id'        => 'friendship-button-' . $potential_friend_id,
					'link_href'         => wp_nonce_url( $bp->loggedin_user->domain . bp_get_friends_slug() . '/add-friend/' . $potential_friend_id . '/', 'friends_add_friend' ),
					'link_text'         => __( 'Add Friend', 'buddypress' ),
					'link_title'        => __( 'Add Friend', 'buddypress' ),
					'link_id'           => 'friend-' . $potential_friend_id,
					'link_rel'          => 'add',
					'link_class'        => 'friendship-button not_friends add'
				);
				break;
		}

		// Filter and return the HTML button
		return bp_get_button( apply_filters( 'bp_get_add_friend_button', $button ) );
	}

function bp_get_friend_ids( $user_id = 0 ) {
	global $bp;

	if ( !$user_id )
		$user_id = ( $bp->displayed_user->id ) ? $bp->displayed_user->id : $bp->loggedin_user->id;

	$friend_ids = friends_get_friend_user_ids( $user_id );

	if ( empty( $friend_ids ) )
		return false;

	return implode( ',', friends_get_friend_user_ids( $user_id ) );
}
function bp_get_friendship_requests() {
	global $bp;

	return apply_filters( 'bp_get_friendship_requests', implode( ',', (array) friends_get_friendship_request_user_ids( $bp->loggedin_user->id ) ) );
}

function bp_friend_friendship_id() {
	echo bp_get_friend_friendship_id();
}
	function bp_get_friend_friendship_id() {
		global $members_template, $bp;

		if ( !$friendship_id = wp_cache_get( 'friendship_id_' . $members_template->member->id . '_' . $bp->loggedin_user->id ) ) {
			$friendship_id = friends_get_friendship_id( $members_template->member->id, $bp->loggedin_user->id );
			wp_cache_set( 'friendship_id_' . $members_template->member->id . '_' . $bp->loggedin_user->id, $friendship_id, 'bp' );
		}

		return apply_filters( 'bp_get_friend_friendship_id', $friendship_id );
	}

function bp_friend_accept_request_link() {
	echo bp_get_friend_accept_request_link();
}
	function bp_get_friend_accept_request_link() {
		global $members_template, $bp;

		if ( !$friendship_id = wp_cache_get( 'friendship_id_' . $members_template->member->id . '_' . $bp->loggedin_user->id ) ) {
			$friendship_id = friends_get_friendship_id( $members_template->member->id, $bp->loggedin_user->id );
			wp_cache_set( 'friendship_id_' . $members_template->member->id . '_' . $bp->loggedin_user->id, $friendship_id, 'bp' );
		}

		return apply_filters( 'bp_get_friend_accept_request_link', wp_nonce_url( $bp->loggedin_user->domain . bp_get_friends_slug() . '/requests/accept/' . $friendship_id, 'friends_accept_friendship' ) );
	}

function bp_friend_reject_request_link() {
	echo bp_get_friend_reject_request_link();
}
	function bp_get_friend_reject_request_link() {
		global $members_template, $bp;

		if ( !$friendship_id = wp_cache_get( 'friendship_id_' . $members_template->member->id . '_' . $bp->loggedin_user->id ) ) {
			$friendship_id = friends_get_friendship_id( $members_template->member->id, $bp->loggedin_user->id );
			wp_cache_set( 'friendship_id_' . $members_template->member->id . '_' . $bp->loggedin_user->id, $friendship_id, 'bp' );
		}

		return apply_filters( 'bp_get_friend_reject_request_link', wp_nonce_url( $bp->loggedin_user->domain . bp_get_friends_slug() . '/requests/reject/' . $friendship_id, 'friends_reject_friendship' ) );
	}

function bp_total_friend_count( $user_id = 0 ) {
	echo bp_get_total_friend_count( $user_id );
}
	function bp_get_total_friend_count( $user_id = 0 ) {
		return apply_filters( 'bp_get_total_friend_count', friends_get_total_friend_count( $user_id ) );
	}
	add_filter( 'bp_get_total_friend_count', 'bp_core_number_format' );

function bp_friend_total_requests_count( $user_id = 0 ) {
	echo bp_friend_get_total_requests_count( $user_id );
}
	function bp_friend_get_total_requests_count( $user_id = 0 ) {
		global $bp;

		if ( empty( $user_id ) )
			$user_id = $bp->loggedin_user->id;

		return apply_filters( 'bp_friend_get_total_requests_count', count( BP_Friends_Friendship::get_friend_user_ids( $user_id, true ) ) );
	}

?>