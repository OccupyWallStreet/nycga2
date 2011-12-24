<?php

/* Load JS necessary for group invitation pages */

function invite_anyone_add_js() {
	global $bp;

	if ( $bp->current_action == BP_INVITE_ANYONE_SLUG || ( isset( $bp->action_variables[1] ) && $bp->action_variables[1] == BP_INVITE_ANYONE_SLUG ) ) {

		wp_enqueue_script( 'invite-anyone-autocomplete-js', WP_PLUGIN_URL . '/invite-anyone/group-invites/jquery.autocomplete/jquery.autocomplete-min.js', array( 'jquery' ) );

		wp_register_script( 'invite-anyone-js', WP_PLUGIN_URL . '/invite-anyone/group-invites/group-invites-js.js', array( 'invite-anyone-autocomplete-js' ) );
		wp_enqueue_script( 'invite-anyone-js' );

	}
}
add_action( 'wp_head', 'invite_anyone_add_js', 1 );

function invite_anyone_add_group_invite_css() {
	global $bp;

	if ( $bp->current_action == BP_INVITE_ANYONE_SLUG || ( isset( $bp->action_variables[1] ) && $bp->action_variables[1] == BP_INVITE_ANYONE_SLUG ) ) {
   		$style_url = WP_PLUGIN_URL . '/invite-anyone/group-invites/group-invites-css.css';
		$style_file = WP_PLUGIN_DIR . '/invite-anyone/group-invites/group-invites-css.css';

		if (file_exists($style_file)) {
			wp_register_style('invite-anyone-group-invites-style', $style_url);
			wp_enqueue_style('invite-anyone-group-invites-style');
		}
	}
}
add_action( 'wp_print_styles', 'invite_anyone_add_group_invite_css' );

function invite_anyone_add_old_css() { ?>
	<style type="text/css">

li a#nav-invite-anyone {
	padding: 0.55em 3.1em 0.55em 0px !important;
	margin-right: 10px;
	background: url(<?php echo WP_PLUGIN_URL, '/invite-anyone/invite-anyone/invite_bullet.gif'; ?>) no-repeat 89% 52%;

}
	</style>
<?php
}

class BP_Invite_Anyone extends BP_Group_Extension {

	var $enable_nav_item = true;
	var $enable_create_step = true;
	var $enable_edit_item = false;

	function bp_invite_anyone() {
		global $bp;

		$this->has_caps = true;

		/* Group API Extension Properties */
		$this->name = __( 'Send Invites', 'buddypress' );
		$this->slug = BP_INVITE_ANYONE_SLUG;

		/* Set as early in the order as possible */
		$this->create_step_position = 42;
		$this->nav_item_position = 71;

		/* Generic check access */
		if ( $this->has_caps == false ) {
			$this->enable_create_step = false;
			$this->enable_edit_step = false;
		}

		$this->enable_nav_item = $this->enable_nav_item();
		$this->enable_create_step = $this->enable_nav_item();
	}

	function display() {
		global $bp;

		if ( BP_INVITE_ANYONE_SLUG == $bp->current_action && isset( $bp->action_variables[0] ) && 'send' == $bp->action_variables[0] ) {
			if ( !check_admin_referer( 'groups_send_invites', '_wpnonce_send_invites' ) )
				return false;

			// Send the invites.
			groups_send_invites( $bp->loggedin_user->id, $bp->groups->current_group->id );

			do_action( 'groups_screen_group_invite', $bp->groups->current_group->id );

			// Hack to imitate bp_core_add_message, since bp_core_redirect is giving me such hell
			echo '<div id="message" class="updated"><p>' . __( 'Group invites sent.', 'buddypress' ) . '</p></div>';
		}

		invite_anyone_create_screen_content('invite');
	}

	function create_screen() {
		global $bp;

		/* If we're not at this step, go bye bye */
		if ( !bp_is_group_creation_step( $this->slug ) )
			return false;

		invite_anyone_create_screen_content( 'create' );

		wp_nonce_field( 'groups_create_save_' . $this->slug );
	}

	function create_screen_save( ) {
		global $bp;

		/* Always check the referer */
		check_admin_referer( 'groups_create_save_' . $this->slug );

		/* Set method and save */
		if ( bp_group_has_invites() )
			$this->has_invites = true;
		$this->method = 'create';
		$this->save();
	}

	function save() {
		global $bp;

		/* Set error redirect based on save method */
		if ( $this->method == 'create' )
			$redirect_url = $bp->loggedin_user->domain . $bp->groups->slug . '/create/step/' . $this->slug;
		else
			$redirect_url = bp_get_group_permalink( $bp->groups->current_group ) . '/admin/' . $this->slug;

		groups_send_invites( $bp->loggedin_user->id, $bp->groups->current_group->id );

		if ( $this->has_invites )
			bp_core_add_message( __('Group invites sent.', 'buddypress') );
		else
			bp_core_add_message( __('Group created successfully.', 'buddypress') );
	}

	function enable_nav_item() {
		global $bp;

		if ( invite_anyone_group_invite_access_test() == 'anyone' )
			return true;
		else
			return false;
	}

	function widget_display() {}
}
bp_register_group_extension( 'BP_Invite_Anyone' );


function invite_anyone_catch_group_invites() {
	global $bp;

	if ( BP_INVITE_ANYONE_SLUG == $bp->current_action && isset( $bp->action_variables[0] ) && 'send' == $bp->action_variables[0] ) {
		if ( !check_admin_referer( 'groups_send_invites', '_wpnonce_send_invites' ) )
			return false;

		// Send the invites.
		groups_send_invites( $bp->loggedin_user->id, $bp->groups->current_group->id );

		bp_core_add_message( __('Group invites sent.', 'buddypress') );

		do_action( 'groups_screen_group_invite', $bp->groups->current_group->id );

		bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . BP_INVITE_ANYONE_SLUG );
	}
}
add_action( 'wp', 'invite_anyone_catch_group_invites', 1 );

function invite_anyone_create_screen_content( $event ) {
	if ( !$template = locate_template( 'groups/single/invite-anyone.php', true ) ) {
		include_once( 'templates/invite-anyone.php' );
	}
}

/* Creates the list of members on the Sent Invite screen */
function bp_new_group_invite_member_list() {
	echo bp_get_new_group_invite_member_list();
}
	function bp_get_new_group_invite_member_list( $args = '' ) {
		global $bp;

		$defaults = array(
			'group_id' => false,
			'separator' => 'li'
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		if ( !$group_id )
			$group_id = isset( $bp->groups->new_group_id ) ? $bp->groups->new_group_id : $bp->groups->current_group->id;

		$friends = get_members_invite_list( $bp->loggedin_user->id, $group_id );

		if ( $friends ) {
			$invites = groups_get_invites_for_group( $bp->loggedin_user->id, $group_id );

			for ( $i = 0; $i < count( $friends ); $i++ ) {
				$checked = '';
				if ( $invites ) {
					if ( in_array( $friends[$i]['id'], $invites ) ) {
						$checked = ' checked="checked"';
					}
				}

				$items[] = '<' . $separator . '><input' . $checked . ' type="checkbox" name="friends[]" id="f-' . $friends[$i]['id'] . '" value="' . esc_html( $friends[$i]['id'] ) . '" /> ' . $friends[$i]['full_name'] . '</' . $separator . '>';
			}
		}

		return implode( "\n", (array)$items );
	}

/**
 * Fetch a list of site members eligible to be invited to a group.
 *
 * The list is essentially a list of everyone on the site, minus the logged in user and members
 * of the current group.
 *
 * @package Invite Anyone
 * @since 1.0
 *
 * @param int $group_id The group_id you want to exclude
 * @param str $search_terms If you want to search on username/display name
 * @return array $users An array of located users
 */
function invite_anyone_invite_query( $group_id = false, $search_terms = false ) {
	// Get a list of group members to be excluded from the main query
	$group_members = array();
	$args = array(
		'group_id'	      => $group_id,
		'exclude_admins_mods' => false
	);
	if ( $search_terms )
		$args['search'] = $search_terms;

	if ( bp_group_has_members( $args ) ) {
		while ( bp_group_members() ) {
			bp_group_the_member();
			$group_members[] = bp_get_group_member_id();
		}
	}

	// Don't include the logged-in user, either
	$group_members[] = bp_loggedin_user_id();

	// Now do a user query
	// Pass a null blog id so that the capabilities check is skipped. For BP blog_id doesn't
	// matter anyway
	$user_query = new Invite_Anyone_User_Query( array( 'blog_id' => NULL, 'exclude' => $group_members, 'search' => $search_terms ) );

	return $user_query->results;
}

/**
 * Extends the WP_User_Query class to make it easier for us to search across different fields
 *
 * @package Invite Anyone
 * @since 1.0.4
 */
class Invite_Anyone_User_Query extends WP_User_Query {
	/**
	 * @see WP_User_Query::get_search_sql()
	 */
	function get_search_sql( $string, $cols, $wild = false ) {
		$string = esc_sql( $string );

		// Always search all columns
		$cols = array(
			'user_email',
			'user_login',
			'user_nicename',
			'user_url',
			'display_name'
		);

		// Always do 'both' for trailing_wild
		$wild = 'both';

		$searches = array();
		$leading_wild = ( 'leading' == $wild || 'both' == $wild ) ? '%' : '';
		$trailing_wild = ( 'trailing' == $wild || 'both' == $wild ) ? '%' : '';
		foreach ( $cols as $col ) {
			if ( 'ID' == $col )
				$searches[] = "$col = '$string'";
			else
				$searches[] = "$col LIKE '$leading_wild" . like_escape($string) . "$trailing_wild'";
		}

		return ' AND (' . implode(' OR ', $searches) . ')';
	}
}

function get_members_invite_list( $user_id = false, $group_id ) {
	global $bp, $wpdb;

	if ( $users = invite_anyone_invite_query( $bp->groups->current_group->id ) ) {

		foreach( (array)$users as $member ) {
			$user_id = $member->ID;

			$display_name = bp_core_get_user_displayname( $user_id );

			if ( $display_name != '' ) {
				$friends[] = array(
					'id' => $user_id,
					'full_name' => $display_name
				);
			}
		}

	}

	if ( !$friends )
		return false;

	return $friends;
}

function invite_anyone_ajax_invite_user() {
	global $bp;

	check_ajax_referer( 'groups_invite_uninvite_user' );

	if ( !$_POST['friend_id'] || !$_POST['friend_action'] || !$_POST['group_id'] )
		return false;

	if ( 'invite' == $_POST['friend_action'] ) {

		if ( !groups_invite_user( array( 'user_id' => $_POST['friend_id'], 'group_id' => $_POST['group_id'] ) ) )
			return false;

		$user = new BP_Core_User( $_POST['friend_id'] );

		$group_slug = isset( $bp->groups->root_slug ) ? $bp->groups->root_slug : $bp->groups->slug;

		echo '<li id="uid-' . $user->id . '">';
		echo bp_core_fetch_avatar( array( 'item_id' => $user->id ) );
		echo '<h4>' . bp_core_get_userlink( $user->id ) . '</h4>';
		echo '<span class="activity">' . esc_html( $user->last_active ) . '</span>';
		echo '<div class="action">
				<a class="remove" href="' . wp_nonce_url( $bp->loggedin_user->domain . $group_slug . '/' . $_POST['group_id'] . '/invites/remove/' . $user->id, 'groups_invite_uninvite_user' ) . '" id="uid-' . esc_html( $user->id ) . '">' . __( 'Remove Invite', 'buddypress' ) . '</a>
			  </div>';
		echo '</li>';

	} else if ( 'uninvite' == $_POST['friend_action'] ) {

		if ( !groups_uninvite_user( $_POST['friend_id'], $_POST['group_id'] ) )
			return false;

		return true;

	} else {
		return false;
	}
}
add_action( 'wp_ajax_invite_anyone_groups_invite_user', 'invite_anyone_ajax_invite_user' );

function invite_anyone_ajax_autocomplete_results() {
	global $bp;

	$return = array(
		'query' 	=> $_REQUEST['query'],
		'data' 		=> array(),
		'suggestions' 	=> array()
	);

	$users = invite_anyone_invite_query( $bp->groups->current_group->id, $_REQUEST['query'] );

	if ( $users ) {
		$suggestions = array();
		$data 	     = array();

		foreach ( $users as $user ) {
			$suggestions[] 	= $user->display_name . ' (' . $user->user_login . ')';
			$data[] 	= $user->ID;
		}

		$return['suggestions'] = $suggestions;
		$return['data']	       = $data;
	}

	echo json_encode( $return );
}
add_action( 'wp_ajax_invite_anyone_autocomplete_ajax_handler', 'invite_anyone_ajax_autocomplete_results' );

function invite_anyone_remove_group_creation_invites( $a ) {

	foreach ( $a as $key => $value ) {
		if ( $key == 'group-invites' ) {
			unset( $a[$key] );
		}
	}
	return $a;
}

function invite_anyone_remove_invite_subnav() {
	global $bp;

	if ( invite_anyone_group_invite_access_test() == 'friends' )
		return;

	if ( isset( $bp->groups->group_creation_steps['group-invites'] ) )
		unset( $bp->groups->group_creation_steps['group-invites'] );

	// BP 1.5 / BP 1.2
	$parent_slug = isset( $bp->groups->root_slug ) && isset( $bp->groups->current_group->slug ) ? $bp->groups->current_group->slug : $bp->groups->slug;

	bp_core_remove_subnav_item( $parent_slug, 'send-invites' );
}
add_filter( 'groups_create_group_steps', 'invite_anyone_remove_group_creation_invites', 1 );
add_action( 'bp_setup_nav', 'invite_anyone_remove_invite_subnav', 15 );




/* Utility function to test which members the current user can invite to a group */
function invite_anyone_group_invite_access_test() {
	global $current_user, $bp;

	if ( !is_user_logged_in() || empty( $bp->groups->current_group ) )
		return 'noone';

	if ( !$iaoptions = get_option( 'invite_anyone' ) )
		$iaoptions = array();

	if ( bp_is_group_create() ) {
		if ( $iaoptions['group_invites_can_group_admin'] == 'anyone' || !$iaoptions['group_invites_can_group_admin'] )
			return 'anyone';
		if ( $iaoptions['group_invites_can_group_admin'] == 'friends' )
			return 'friends';
		if ( $iaoptions['group_invites_can_group_admin'] == 'noone' )
			return 'noone';
	}

	if ( !groups_is_user_member( $bp->loggedin_user->id, $bp->groups->current_group->id ) )
		return 'noone';

	if ( is_super_admin() ) {
		if ( $iaoptions['group_invites_can_admin'] == 'anyone' || !$iaoptions['group_invites_can_admin'] )
			return 'anyone';
		if ( $iaoptions['group_invites_can_admin'] == 'friends' )
			return 'friends';
		if ( $iaoptions['group_invites_can_admin'] == 'noone' )
			return 'noone';
	} else if ( bp_group_is_admin() || bp_is_group_create() ) {
		if ( $iaoptions['group_invites_can_group_admin'] == 'anyone' || !$iaoptions['group_invites_can_group_admin'] )
			return 'anyone';
		if ( $iaoptions['group_invites_can_group_admin'] == 'friends' )
			return 'friends';
		if ( $iaoptions['group_invites_can_group_admin'] == 'noone' )
			return 'noone';
	} else if ( bp_group_is_mod() ) {
		if ( $iaoptions['group_invites_can_group_mod'] == 'anyone' || !$iaoptions['group_invites_can_group_mod'] )
			return 'anyone';
		if ( $iaoptions['group_invites_can_group_mod'] == 'friends' )
			return 'friends';
		if ( $iaoptions['group_invites_can_group_mod'] == 'noone' )
			return 'noone';
	} else {
		if ( $iaoptions['group_invites_can_group_member'] == 'anyone' || !$iaoptions['group_invites_can_group_member'] )
			return 'anyone';
		if ( $iaoptions['group_invites_can_group_member'] == 'friends' )
			return 'friends';
		if ( $iaoptions['group_invites_can_group_member'] == 'noone' )
			return 'noone';
	}

	return 'noone';
}

?>