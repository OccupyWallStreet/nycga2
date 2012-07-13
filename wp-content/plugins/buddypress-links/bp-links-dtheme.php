<?php
/* 
 * Display functions that are specific to the bp-links-default theme
 */

function bp_links_is_default_theme() {
	return ( BP_LINKS_THEME == BP_LINKS_DEFAULT_THEME );
}

function bp_links_dtheme_enqueue_styles() {
	wp_enqueue_style( 'bp-links-screen', BP_LINKS_THEME_URL . '/style.css' );
}

function bp_links_dtheme_enqueue_scripts( $forms = false ) {
	// load global ajax scripts
	wp_enqueue_script( 'bp-links-ajax', BP_LINKS_THEME_URL_INC . '/global.js', array('jquery') );
	// load color box JS
	wp_enqueue_script( 'bp-links-ajax-colorbox', BP_LINKS_THEME_URL_INC . '/jquery.colorbox-min.js', array('jquery') );
	// load create forms ajax scripts if necessary
	if ( $forms || bp_links_is_link_admin_page() ) {
		wp_enqueue_script( 'bp-links-ajax-forms', BP_LINKS_THEME_URL_INC . '/forms.js', array('jquery') );
	}
}

//
// Template Actions / Filters
//

function bp_links_dtheme_add_css() {
	global $bp;
	
	if ( !bp_links_is_default_theme() )
		return false;
	
	if ( $bp->current_component == $bp->links->slug ) {
		bp_links_dtheme_enqueue_styles();
	} else if ( $bp->current_component == $bp->groups->slug && $bp->current_action == $bp->links->slug ) {
		bp_links_dtheme_enqueue_styles();
	}
}
add_action( 'wp_print_styles', 'bp_links_dtheme_add_css' );

function bp_links_dtheme_add_js() {
	global $bp;

	if ( !bp_links_is_default_theme() )
		return false;

	// leaving this debug code here on purpose
	//var_dump( $bp->current_component, $bp->current_action, $bp->action_variables );

	if ( $bp->current_component == $bp->links->slug ) {
		bp_links_dtheme_enqueue_scripts( ( $bp->current_action == 'create' ) );
	} else if ( $bp->current_component == $bp->groups->slug && $bp->current_action == $bp->links->slug ) {
		bp_links_dtheme_enqueue_scripts( ( $bp->action_variables[0] == 'create' ) );
	}
}
add_action( 'wp_print_scripts', 'bp_links_dtheme_add_js');

function bp_links_dtheme_header_nav_setup() {
	global $bp;

	if ( !bp_links_is_default_theme() )
		return false;

	$selected = ( bp_is_page( BP_LINKS_SLUG ) ) ? ' class="selected"' : '';
	$title = __( 'Links', 'buddypress-links' );

	echo sprintf('<li%s><a href="%s/%s" title="%s">%s</a></li>', $selected, get_option('home'), BP_LINKS_SLUG, $title, $title );
}
add_action( 'bp_nav_items', 'bp_links_dtheme_header_nav_setup');

function bp_links_dtheme_activity_type_tabs_setup() {
	global $bp;

	if ( !bp_links_is_default_theme() )
		return false;

	if ( is_user_logged_in() && bp_links_total_links_for_user( bp_loggedin_user_id() ) ) {
		echo sprintf(
			'<li id="activity-links"><a href="%s" title="%s">%s</a></li>',
			bp_loggedin_user_domain() . BP_ACTIVITY_SLUG . '/' . BP_LINKS_SLUG . '/',
			__( 'The activity of links I created.', 'buddypress-links' ),
			sprintf(
				__( 'My Links (%s)', 'buddypress-links' ),
				bp_links_total_links_for_user( bp_loggedin_user_id() )
			)
		);
	}
}
add_action( 'bp_before_activity_type_tab_mentions', 'bp_links_dtheme_activity_type_tabs_setup' );

function bp_links_dtheme_activity_filter_options_setup() {
	global $bp;

	if ( !bp_links_is_default_theme() )
		return false;

	echo sprintf( '<option value="%s">%s</option>', BP_LINKS_ACTIVITY_ACTION_CREATE, __( 'Show Link Created', 'buddypress-links' ) );
	echo sprintf( '<option value="%s">%s</option>', BP_LINKS_ACTIVITY_ACTION_COMMENT, __( 'Show Link Comments', 'buddypress-links' ) );
	echo sprintf( '<option value="%s">%s</option>', BP_LINKS_ACTIVITY_ACTION_VOTE, __( 'Show Link Votes', 'buddypress-links' ) );
}
add_action( 'bp_activity_filter_options', 'bp_links_dtheme_activity_filter_options_setup' );
add_action( 'bp_link_activity_filter_options', 'bp_links_dtheme_activity_filter_options_setup' );
add_action( 'bp_group_activity_filter_options', 'bp_links_dtheme_activity_filter_options_setup' );

function bp_links_dtheme_screen_notification_settings() {

	if ( !bp_links_is_default_theme() )
		return false;
	
	echo bp_links_notification_settings();
}
add_action( 'bp_notification_settings', 'bp_links_dtheme_screen_notification_settings' );

//
// Template Tags
//

function bp_links_dtheme_search_form() {
	global $bp; ?>
	<form action="" method="get" id="search-links-form">
		<label><input type="text" name="s" id="links_search" value="<?php if ( isset( $_GET['s'] ) ) { echo attribute_escape( $_GET['s'] ); } else { _e( 'Search anything...', 'buddypress' ); } ?>"  onfocus="if (this.value == '<?php _e( 'Search anything...', 'buddypress' ) ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'Search anything...', 'buddypress' ) ?>';}" /></label>
		<input type="submit" id="links_search_submit" name="links_search_submit" value="<?php _e( 'Search', 'buddypress-links' ) ?>" />
	</form> <?php
}

function bp_links_dtheme_creation_tabs() {
	global $bp;

	$href = sprintf( '%s/%s/create/', $bp->root_domain, $bp->links->slug ); ?>

	<li class="current"><a href="<?php echo $href ?>"><?php _e( 'Create', 'buddypress-links' ) ?></a></li>
	<li><a href="<?php echo $href ?>"><?php _e( 'Start Over', 'buddypress-links' ) ?></a></li> <?php
	do_action( 'bp_links_dtheme_creation_tabs' );
}

function bp_links_dtheme_link_order_options_list() { ?>
	<li id="links-order-select" class="last filter">
		<?php _e( 'Order By:', 'buddypress' ) ?>
		<select id="links-all">
			<?php bp_links_link_order_options() ?>
			<?php do_action( 'bp_links_dtheme_link_order_options_list' ) ?>
		</select>
	</li> <?php
}

function bp_links_dtheme_link_category_filter_options_list() { ?>
		<li id="links-category-filter-select" class="last">
			<?php _e( 'Category:', 'buddypress-links' ) ?>
			<select id="links-category-filter">
				<option value="-1"><?php _e( 'All', 'buddypress' ) ?></option>
				<?php bp_links_category_select_options( bp_links_dtheme_selected_category() ) ?>
				<?php do_action( 'bp_links_category_filter_options' ) ?>
			</select>
		</li> <?php
}

function bp_links_dtheme_share_button( $link = false, $group = false ) {
	global $bp, $links_template, $groups_template;

	if ( is_user_logged_in() ) {

		if ( !$link )
			$link = $links_template->link;

		if ( !$group && $groups_template->group->id ) {
			$group = $groups_template->group;
			$anchor_id = $group->id;
			$anchor_id_where = 'group';
			$show_shared_icon = ( $bp->is_item_admin || $bp->is_item_mod );
		} else {
			$anchor_id = $bp->loggedin_user->id;
			$anchor_id_where = 'profile';
			$show_shared_icon = ( bp_get_link_share_has_profile_link( $link ) && bp_get_link_share_profile_link_user_id( $link ) == $bp->loggedin_user->id );
		}

		printf(
			'<div class="generic-button link-share-button" id="linksharebutton-%1$d">
				<span class="ajax-loader" id="link-share-loader-%1$d"></span>
				<a class="link-share" id="linkshare-%5$s-%6$d" href="%2$s">%3$s%4$s</a>
			</div>',
			$link->id, // arg 1
			wp_nonce_url( bp_get_link_permalink( $link ) . '/share-link', 'link_share' ), // arg 2
			( $show_shared_icon ) ? '<span class="link-share-active"></span> ' : null, // arg 3
			__( 'Share', 'buddypress-links' ), // arg 4
			$anchor_id_where,
			$anchor_id

		);
	}
}

//
// AJAX Actions and Filters
//

/**
 * Links Directory Hook
 */
function bp_links_dtheme_template_loader() {
	bp_links_locate_template( array( 'links-loop.php' ), true );
}
add_action( 'wp_ajax_links_filter', 'bp_links_dtheme_template_loader' );

/**
 * Augment profile Links page sub-navigation
 */
function bp_links_dtheme_personal_links_subnav( $html ) {
	$html .= bp_links_dtheme_link_order_options_list();
	$html .= bp_links_dtheme_link_category_filter_options_list();

	return $html;
}
add_filter( 'bp_get_options_nav_links-my-links', 'bp_links_dtheme_personal_links_subnav' );

/**
 * Helper function to return selected category cookie
 */
function bp_links_dtheme_selected_category() {
	if ( isset( $_COOKIE['bp-links-extras'] ) && preg_match('/^category-\d+$/', $_COOKIE['bp-links-extras'] ) ) {
		$parts = split( '-', $_COOKIE['bp-links-extras'] );
		if ( $parts[1] > 0 ) {
			return $parts[1];
		}
	}

	return null;
}

/**
 * Filter all AJAX bp_filter_request() calls for the category filter
 *
 * @param string $query_string
 * @param string $object
 * @return string
 */
function bp_links_dtheme_ajax_querystring_category_filter( $query_string, $object ) {
	global $bp;

	if ( ( $bp->links->slug == $bp->current_component || 'links' == $object ) || ( $bp->groups->slug == $bp->current_component && $bp->current_action == $bp->links->slug ) ) {

		$selected_category = bp_links_dtheme_selected_category();

		if ( !empty( $selected_category ) ) {
			$args = array();
			parse_str( $query_string, $args );
			$args['category'] = $selected_category;
			return http_build_query( $args );
		}

	}

	return $query_string;
}
add_filter( 'bp_dtheme_ajax_querystring', 'bp_links_dtheme_ajax_querystring_category_filter', 1, 2 );

/**
 * Filter all AJAX bp_filter_request() calls to add group and user ids to group home page calls
 *
 * @param string $query_string
 * @param string $object
 * @param string $filter
 * @param string $scope
 * @return string
 */
function bp_links_dtheme_ajax_querystring_directory_filter( $query_string, $object, $filter, $scope ) {
	global $bp;

	// look for links component
	if ( $bp->links->slug == $bp->current_component ) {
		// must be my links action or scope
		if ( 'mylinks' == $scope || 'my-links' == $bp->current_action ) {

			$args = array();
			parse_str( $query_string, $args );

			// inject user id
			$args['user_id'] = ( bp_is_member() ) ? $bp->displayed_user->id : $bp->loggedin_user->id;

			return http_build_query( $args );
		}
	}

	return $query_string;
}
add_filter( 'bp_dtheme_ajax_querystring', 'bp_links_dtheme_ajax_querystring_directory_filter', 1, 4 );

/**
 * Filter all AJAX bp_filter_request() calls to add group and user ids to group home page calls
 *
 * @param string $query_string
 * @return string
 */
function bp_links_dtheme_ajax_querystring_group_filter( $query_string ) {
	global $bp;

	if ( !bp_links_is_groups_enabled() )
		return $query_string;

	// look for groups component and links action
	if ( $bp->groups->slug == $bp->current_component && $bp->current_action == $bp->links->slug ) {

		$args = array();
		parse_str( $query_string, $args );

		// inject group id
		$args['group_id'] = $bp->groups->current_group->id;

		// inject user id if we are on my group links page
		if ( 'my-links' == $bp->action_variables[0] ) {
			$args['user_id'] = $bp->loggedin_user->id;
		}

		return http_build_query( $args );
	}

	return $query_string;
}
add_filter( 'bp_dtheme_ajax_querystring', 'bp_links_dtheme_ajax_querystring_group_filter', 1 );

/**
 * Filter all AJAX bp_activity_request() calls for the 'activity' object with the 'links' scope
 *
 * @param string $query_string
 * @param string $object
 * @param string $filter
 * @param string $scope
 * @param integer $page
 * @param string $search_terms
 * @param string $extras
 * @return string
 */
function bp_links_dtheme_ajax_querystring_activity_filter( $query_string, $object, $filter, $scope, $page, $search_terms, $extras ) {
	global $bp;

	if ( !bp_links_is_activity_enabled() )
		return $query_string;

	$do_filter = false;

	// only filter activity.
	if ( $bp->activity->id == $object ) {

		if ( bp_links_is_groups_enabled() && bp_is_group_home() ) {
			$do_filter = 'group';
		} elseif ( bp_is_member() ) {
			// handle filtering for profile pages
			// this nav does not use AJAX so don't rely on $scope
			if ( $bp->activity->id == $bp->current_component && $bp->links->slug == $bp->current_action ) {
				$do_filter = 'user';
			}
		} else {
			// handle filtering for all non-profile, non-links pages
			if ( empty( $bp->current_component ) || $bp->activity->id == $bp->current_component ) {
				// filter under 'activity' component with 'links' scope
				if ( $bp->links->id == $scope ) {
					$do_filter = 'user';
				}
			} elseif ( $bp->links->id == $bp->current_component ) {
				// filter 'links' component home pages
				if ( $bp->is_single_item ) {
					$do_filter = 'default';
				}
			}
		}
	}

	if ( $do_filter ) {
		
		// parse query string
		$args = array();
		parse_str( $query_string, $args );

		switch ( $do_filter ) {
			case 'group':
				// send groups AND links objects
				$args['object'] = sprintf( '%s,%s', $bp->groups->id, $bp->links->id );
				// get recent link cloud ids for this group
				$recent_ids = bp_links_recent_activity_item_ids_for_group();
				// if there is activity, merge the ids with the current group id
				if ( count( $recent_ids ) ) {
					$primary_ids = $recent_ids;
					$primary_ids[] = $bp->groups->current_group->id;
					$args['primary_id'] = join( ',', $primary_ids );
				}
				break;
			case 'user':
				// override with links object
				$args['object'] = $bp->links->id;
				// user_id must be empty to show OTHER user's actions for this user's links
				$args['user_id'] = false;
				// get recent link cloud ids for this user
				$recent_ids = bp_links_recent_activity_item_ids_for_user();
				// if there is activity, send the ids
				if ( count( $recent_ids ) )
					$args['primary_id'] = join( ',', $recent_ids );
				break;
			case 'default':
				// override with links object
				$args['object'] = $bp->links->id;
				// set primary id to current link id if applicable
				if ( $bp->links->current_link ) {
					$args['primary_id'] = $bp->links->current_link->cloud_id;
				}
				break;
		}

		// return modified query string
		return http_build_query( $args );
	}

	// no filtering
	return $query_string;
}
add_filter( 'bp_dtheme_ajax_querystring', 'bp_links_dtheme_ajax_querystring_activity_filter', 1, 7 );

/**
 * Return "my links" feed URL on activity home page
 *
 * @param string $feed_url
 * @param string $scope
 * @return string
 */
function bp_links_dtheme_activity_feed_url( $feed_url, $scope ) {
	global $bp;

	if ( !bp_links_is_activity_enabled() || empty( $scope ) || $scope != $bp->links->id )
		return $feed_url;

	return $bp->loggedin_user->domain . BP_ACTIVITY_SLUG . '/my-links/feed/';
}
add_filter( 'bp_dtheme_activity_feed_url', 'bp_links_dtheme_activity_feed_url', 11, 2 );

/**
 * Handle creating a custom update to a Link
 *
 * @param string $object
 * @param integer $item_id
 * @param string $content
 * @return integer|false Activity id that was created
 */
function bp_links_dtheme_activity_custom_update( $object, $item_id, $content ) {
	// if object is links, try a custom update
	if ( 'links' == $object ) {
		return bp_links_post_update( array( 'type' => BP_LINKS_ACTIVITY_ACTION_COMMENT, 'link_id' => $item_id, 'content' => $content ) );
	}
}
add_filter( 'bp_activity_custom_update', 'bp_links_dtheme_activity_custom_update', 10, 3 );

/**
 * Handle AJAX action from clicking of link share button
 */
function bp_dtheme_ajax_link_share() {
	global $bp;

	check_ajax_referer( 'link_share' );

	if ( !is_user_logged_in() ) {
		bp_links_ajax_response_string( -1, __( 'You must be logged in to share links.', 'buddypress-links' ) );
	}

	$link_id = ( is_numeric( $_POST['link_id'] ) ) ? ( integer ) $_POST['link_id'] : die();

	$link = new BP_Links_Link( $link_id );

	if ( $link->id ) { ?>

		1[[split]]<div class="link-share-panel">
			<form action="<?php echo bp_get_link_permalink( $link ) . '/share-link' ?>" method="post" id="link-share-form">
				<fieldset id="link-share-where-set">
					<?php if ( bp_links_is_groups_enabled() ): ?>
					<legend><?php _e( 'Share this link in:', 'buddypress-links' ) ?></legend>
					<input type="radio" name="link-share-where" id="link-share-where-profile" value="profile" checked="checked"> <?php _e( 'My Profile', 'buddypress' ) ?>
					<input type="radio" name="link-share-where" id="link-share-where-group" value="group"> <?php _e( 'A Group', 'buddypress-links' ) ?>
					<?php else: ?>
					<legend><?php _e( 'Share this link in:', 'buddypress-links' ) ?> <?php _e( 'My Profile', 'buddypress' ) ?></legend>
					<input type="hidden" name="link-share-where" id="link-share-where-profile" value="profile">
					<?php endif; ?>
				</fieldset>
				<?php if ( bp_links_is_groups_enabled() ): ?>
				<fieldset id="link-share-group-set">
					<legend><?php _e( 'Select a group:', 'buddypress-links' ) ?></legend>
					<select name="link-share-group" id="link-share-group" class="link-share-object-select">
						<option value="-1"><?php _e( 'Please Choose', 'buddypress-links' ) ?> ---&gt;</option>
						<?php bp_link_user_group_options() ?>
					</select>
				</fieldset>
				<?php endif; ?>
				<?php do_action( 'bp_links_share_panel_fieldset' ) ?>
				<input type="hidden" name="link-share-id" id="link-share-id" value="<?php echo bp_get_link_id( $link ) ?>">
				<input type="submit" name="link-share-save" id="link-share-save" value="<?php _e( 'Share Now', 'buddypress-links' ) ?>">
				<input type="submit" name="link-share-cancel" id="link-share-cancel" value="<?php _e( 'Cancel', 'buddypress-links' ) ?>">
				<?php bp_link_share_remove_button( $link, $_POST['object'], $_POST['object_id'] ) ?>
				<?php wp_nonce_field( 'link_share_save', 'link-share-nonce' ) ?>
			</form>
		</div><?php

		die();
	}

	// something went wrong
	bp_links_ajax_response_string( -1, __( 'Loading sharing options has failed.', 'buddypress-links' ) );
}
add_action( 'wp_ajax_link_share', 'bp_dtheme_ajax_link_share' );

/**
 * Handle AJAX action from clicking of link share save button (personal share)
 */
function bp_dtheme_ajax_link_share_save_profile() {
	global $bp;

	check_ajax_referer( 'link_share_save' );

	if ( is_user_logged_in() ) {
		$user_id = $bp->loggedin_user->id;
	} else {
		bp_links_ajax_response_string( -1, __( 'You must be logged in to share links.', 'buddypress-links' ) );
	}
	
	$link_id = ( is_numeric( $_POST['link_id'] ) ) ? (integer) $_POST['link_id'] : die();

	$link = new BP_Links_Link( $link_id );

	if ( $link->id ) {

		if ( $user_id == $link->user_id ) {

			// umm, don't try to share links with yourself
			bp_links_ajax_response_string( -1, __( 'Sharing a link with yourself is not allowed.', 'buddypress-links' ) );

		} elseif ( bp_links_profile_link_exists( $link_id, $user_id ) ) {

			// link already exists
			bp_links_ajax_response_string( -1, __( 'This link has already been shared in your profile.', 'buddypress-links' ) );

		} else {

			// try to create a new share
			$profile_link = new BP_Links_Profile_Link();
			$profile_link->link_id = $link_id;
			$profile_link->user_id = $user_id;

			if ( $profile_link->save() ) {
				bp_links_ajax_response_string( 1, __( 'This link has been shared in your profile.', 'buddypress-links' ) );
			} else {
				bp_links_ajax_response_string( -1, __( 'Sharing this link in your profile has failed.', 'buddypress-links' ) );
			}
		}
	}

	// something went horribly wrong
	bp_links_ajax_response_string( -1, __( 'Sharing this link has failed.', 'buddypress-links' ) );
}
add_action( 'wp_ajax_link_share_save_profile', 'bp_dtheme_ajax_link_share_save_profile' );

/**
 * Handle AJAX action from clicking of link share save button (group share)
 */
function bp_dtheme_ajax_link_share_save_group() {
	global $bp;

	if ( !bp_links_is_groups_enabled() )
		return false;

	check_ajax_referer( 'link_share_save' );

	if ( is_user_logged_in() ) {
		$user_id = $bp->loggedin_user->id;
	} else {
		bp_links_ajax_response_string( -1, __( 'You must be logged in to share links.', 'buddypress-links' ) );
	}

	$link_id = ( is_numeric( $_POST['link_id'] ) ) ? (integer) $_POST['link_id'] : die();
	$group_id = ( is_numeric( $_POST['object_id'] ) && $_POST['object_id'] >= 1 ) ? (integer) $_POST['object_id'] : die();

	$link = new BP_Links_Link( $link_id );
	$group = new BP_Groups_Group( $group_id );

	if ( $link->id && $group->id ) {

		// get group name
		$group_name = bp_get_group_name( $group );

		// does group share already exist?
		if ( bp_links_group_link_exists( $link_id, $group_id ) ) {

			// try to load group link
			$group_link = new BP_Links_Group_Link( $link_id, $group_id );

			// check if link was previously removed from this group
			if ( $group_link->removed() ) {
				switch( true ) {
					// only admins and moderators can re-add links
					case ( groups_is_user_admin( $user_id, $group->id ) ):
					case ( groups_is_user_mod( $user_id, $group->id ) ):
						if ( $group_link->remove_revert() )
							bp_links_ajax_response_string( 1, sprintf( __( 'Sharing this link with the %s group was re-enabled.', 'buddypress-links' ), $group_name ) );
						else
							bp_links_ajax_response_string( -1, sprintf( __( 'Failed to re-enable sharing this link with the %s group.', 'buddypress-links' ), $group_name ) );
					default:
						bp_links_ajax_response_string( -1, sprintf( __( 'This link was previously removed from the %s group by an admin or moderator.', 'buddypress-links' ), $group_name ) );
				}
			}
			
			// link already exists
			bp_links_ajax_response_string( 1, sprintf( __( 'This link has already been shared with the %s group.', 'buddypress-links' ), $group_name ) );
			
		} else if ( groups_is_user_member( $user_id, $group_id ) ) {

			// try to create a new share
			$group_link = new BP_Links_Group_Link();
			$group_link->link_id = $link_id;
			$group_link->group_id = $group_id;
			$group_link->user_id = $user_id;

			if ( $group_link->save() ) {
				bp_links_ajax_response_string( 1, sprintf( __( 'This link has been shared with the %s group.', 'buddypress-links' ), $group_name ) );
			} else {
				bp_links_ajax_response_string( -1, sprintf( __( 'Sharing this link with the %s group has failed.', 'buddypress-links' ), $group_name ) );
			}

		} else {
			// not a group member
			bp_links_ajax_response_string( -1, sprintf( __( 'You are not a member of the %s group.', 'buddypress-links' ), $group_name ) );
		}
	}

	// something went horribly, horribly wrong
	bp_links_ajax_response_string( -1, __( 'Sharing with a group has failed.', 'buddypress-links' ) );
}
add_action( 'wp_ajax_link_share_save_group', 'bp_dtheme_ajax_link_share_save_group' );

/**
 * Handle AJAX action from clicking of remove share from profile button
 *
 * @return string
 */
function bp_dtheme_ajax_link_share_remove_profile() {
	global $bp;

	check_ajax_referer( 'link_share_save' );

	if ( is_user_logged_in() ) {
		$user_id = $bp->loggedin_user->id;
	} else {
		bp_links_ajax_response_string( -1, __( 'You must be logged in to remove links.', 'buddypress-links' ) );
	}

	$link_id = ( is_numeric( $_POST['link_id'] ) ) ? ( integer ) $_POST['link_id'] : die();

	$link = new BP_Links_Link( $link_id );

	if ( $link->id ) {

		// try to load profile link
		$profile_link = new BP_Links_Profile_Link( $link->id, $user_id );

		if ( $user_id == $profile_link->user_id ) {
			if ( $profile_link->delete() ) {
				bp_links_ajax_response_string( 1, __( 'This link has been removed from your profile.', 'buddypress-links' ) );
			}
		}
	}

	// something went wrong
	bp_links_ajax_response_string( -1, __( 'Removing this link from your profile has failed.', 'buddypress-links' ) );
}
add_action( 'wp_ajax_share_link_remove_profile', 'bp_dtheme_ajax_link_share_remove_profile' );

/**
 * Handle AJAX action from clicking of remove share from group button
 * 
 * @return string
 */
function bp_dtheme_ajax_link_share_remove_group() {
	global $bp;

	if ( !bp_links_is_groups_enabled() )
		return false;

	check_ajax_referer( 'link_share_save' );

	if ( is_user_logged_in() ) {
		$user_id = $bp->loggedin_user->id;
	} else {
		bp_links_ajax_response_string( -1, __( 'You must be logged in to remove links.', 'buddypress-links' ) );
	}

	$link_id = ( is_numeric( $_POST['link_id'] ) ) ? ( integer ) $_POST['link_id'] : die();
	$group_id = ( is_numeric( $_POST['object_id'] ) ) ? ( integer ) $_POST['object_id'] : die();

	$link = new BP_Links_Link( $link_id );
	$group = new BP_Groups_Group( $group_id );

	if ( $link->id && $group->id ) {

		// try to load group link
		$group_link = new BP_Links_Group_Link( $link->id, $group->id );

		switch( true ) {
			// person who added link can delete the share
			case ( $user_id == $group_link->user_id ):
				if ( $group_link->delete() )
					bp_links_ajax_response_string( 1, __( 'This link has been removed from this group.', 'buddypress-links' ) );
				break;
			// admins and moderators can remove the share
			case ( groups_is_user_admin( $user_id, $group->id ) ):
			case ( groups_is_user_mod( $user_id, $group->id ) ):
				if ( $group_link->remove() )
					bp_links_ajax_response_string( 1, __( 'This link has been removed from this group.', 'buddypress-links' ) );
				break;
		}
	}

	// something went wrong
	bp_links_ajax_response_string( -1, __( 'Removing this link from this group has failed.', 'buddypress-links' ) );
}
add_action( 'wp_ajax_share_link_remove_group', 'bp_dtheme_ajax_link_share_remove_group' );


?>
