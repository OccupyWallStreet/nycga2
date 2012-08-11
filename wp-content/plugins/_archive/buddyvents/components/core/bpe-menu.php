<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

// No direct access is allowed
if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Sort and display the main navigation
 *
 * @package Core
 * @since 	1.4
 */
function bpe_main_navigation()
{
	$menu = array(
        'active' 	=> '<li id="events-active"'. bpe_highlight_current_tab( bp_current_action(), bpe_get_option( 'active_slug' ), 'selected' ) .'><a href="'. bpe_get_events_link() . bpe_check_default_slug( bpe_get_option( 'active_slug' ), false ) .'">'. sprintf( __( 'Active <span>%d</span>', 'events' ), bpe_get_event_count( 'active', 0, 'none' ) ) .'</a></li>',
        'archive' 	=> '<li id="events-archive"'. bpe_highlight_current_tab( bp_current_action(), bpe_get_option( 'archive_slug' ), 'selected' ) .'><a href="'. bpe_get_events_link() . bpe_check_default_slug( bpe_get_option( 'archive_slug' ), false ) .'">'. sprintf( __( 'Archive <span>%d</span>', 'events' ), bpe_get_event_count( 'archive', 0, 'none' ) ) .'</a></li>',
        'calendar' 	=> '<li id="events-calendar"'. bpe_highlight_current_tab( bp_current_action(), bpe_get_option( 'calendar_slug' ), 'selected' ) .'><a href="'. bpe_get_events_link() . bpe_check_default_slug( bpe_get_option( 'calendar_slug' ), false ) .'">'. __( 'Calendar', 'events' ) .'</a></li>',
        'map' 		=> '<li id="events-map"'. bpe_highlight_current_tab( bp_current_action(), bpe_get_option( 'map_slug' ), 'selected' ) .'><a href="'. bpe_get_events_link(). bpe_check_default_slug( bpe_get_option( 'map_slug' ), false ) .'">'. __( 'Map', 'events' ) .'</a></li>',
        'search' 	=> '<li id="events-search"'. bpe_highlight_current_tab( bp_current_action(), bpe_get_option( 'search_slug' ), 'selected' ) .'><a href="'. bpe_get_events_link(). bpe_check_default_slug( bpe_get_option( 'search_slug' ), false ) .'">'. __( 'Search', 'events' ) .'</a></li>'
	);
	
	$new_order = bpe_sort_menu( $menu );
	
	$new_menu = '';
	foreach( $new_order as $value )
		$new_menu .= $value;
	
	echo $new_menu;
}

/**
 * Highlight the current tab
 *
 * @package Core
 * @since 	1.4
 */
function bpe_highlight_current_tab( $val, $tab, $class )
{
	if( bpe_get_option( 'default_tab' ) == $tab )
	{
		if( empty( $val ) )
			return ' class="'. $class .'"';
	}
	
	if( $val == $tab )
		return ' class="'. $class .'"';
}

/**
 * Check default slug
 *
 * @package Core
 * @since 	1.4
 */
function bpe_check_default_slug( $slug, $sep = true )
{
	$div = ( $sep === true ) ? '/' : '';
	
	if( bpe_get_option( 'default_tab' ) != $slug )
		return $div . $slug .'/';
		
	return false;
}

/**
 * Sort the nav menus
 *
 * @package Core
 * @since 	1.4
 */
function bpe_sort_menu( $menu )
{
	foreach( (array) bpe_get_option( 'tab_order' ) as $tab => $order )
	{
		foreach( (array)$menu as $t => $o )
		{
			if( $t == $tab )
				$new_menu[$order] = $o;
		}
	}
	
	ksort( $new_menu );
	
	return $new_menu;
}

/**
 * Add 'New Event' menu to the WP Admin Bar
 *
 * @package Core
 * @since 	2.1
 */
function bpe_add_new_event_menu( $wp_admin_bar )
{
	if( current_user_can( 'bpe_manage_events' ) ) :
		$wp_admin_bar->add_menu( array(
			'parent'    => 'new-content',
			'id'        => 'new-event',
			'title'     => __( 'Event', 'events' ),
			'href'      => admin_url( '/admin.php?page='. EVENT_FOLDER .'&action=create' )
		) );
	endif;
}
add_action( 'admin_bar_menu', 'bpe_add_new_event_menu', 99 );

/**
 * Add events menu (plus all sub menues) to an enabled WP admin bar
 *
 * @package Core
 * @since 	2.0.6
 */
function bpe_add_wp_admin_bar_menus()
{
	global $wp_admin_bar, $bp;
	
	if( ! bp_use_wp_admin_bar() || ! is_user_logged_in() )
		return false;

	$events_link = bp_loggedin_user_domain() . bpe_get_base( 'slug' ). '/';
	
	$default = ( bpe_get_option( 'default_tab_attending' ) === false ) ? bpe_get_option( 'default_tab' ) : bpe_get_option( 'attending_slug' );
	$deactivated_tabs = bpe_get_option( 'deactivated_tabs' );

	$wp_admin_bar->add_menu( array(
		'id'	 => 'events-main',
		'parent' => $bp->my_account_menu_id,
		'title'  => __( 'Events', 'events' ),
		'href'   => $events_link . $default
	) );
	
	$submenu_items = array();
	
	if( ! isset( $deactivated_tabs['active'] ) ) :
		$submenu_items[bpe_get_option( 'tab_order', 'active' )] =	array(
			'name' 		=> __( 'Active', 'events' ),
			'href' 		=> $events_link . bpe_get_option( 'active_slug' ),
			'id' 		=> 'active-events'
		);
	endif;
			
	if( ! isset( $deactivated_tabs['archive'] ) ) :
		$submenu_items[bpe_get_option( 'tab_order', 'archive' )] =	array(
			'name' 		=> __( 'Archive', 'events' ),
			'href' 		=> $events_link . bpe_get_option( 'archive_slug' ),
			'id' 		=> 'archive-events'
		);
	endif;
			
	if( ! isset( $deactivated_tabs['attending'] ) ) :
		$submenu_items[bpe_get_option( 'tab_order', 'attending' )] =	array(
			'name' 		=> __( 'Attending', 'events' ),
			'href' 		=> $events_link . bpe_get_option( 'attending_slug' ),
			'id' 		=> 'attending-events'
		);
	endif;
			
	$email = bp_get_user_meta( $user_id, 'bpe_paypal_email', true );
	if( ! isset( $deactivated_tabs['invoices'] ) && bpe_get_option( 'enable_tickets' ) === true && bpe_get_option( 'enable_invoices' ) === true && $email ) :
		$submenu_items[bpe_get_option( 'tab_order', 'invoices' )] =	array(
			'name' 		=> __( 'Invoices', 'events' ),
			'href' 		=> $events_link . bpe_get_option( 'invoice_slug' ),
			'id' 		=> 'invoices-events'
		);
	endif;
	
	if( ! isset( $deactivated_tabs['calendar'] ) ) :
		$submenu_items[bpe_get_option( 'tab_order', 'calendar' )] =	array(
			'name' 		=> __( 'Calendar', 'events' ),
			'href' 		=> $events_link . bpe_get_option( 'calendar_slug' ),
			'id' 		=> 'calendar-events'
		);
	endif;
	
	if( ! isset( $deactivated_tabs['map'] ) ) :
		$submenu_items[bpe_get_option( 'tab_order', 'map' )] =	array(
			'name' 		=> __( 'Map', 'events' ),
			'href' 		=> $events_link . bpe_get_option( 'map_slug' ),
			'id' 		=> 'map-events'
		);
	endif;
	
	if( ! bpe_is_restricted() ) :
		if( ! isset( $deactivated_tabs['create'] ) ) :
			$submenu_items[99999] =	array(
				'name' 		=> __( 'Create', 'events' ),
				'href' 		=> bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. bpe_get_option( 'create_slug' ) .'/',
				'id' 		=> 'create-events'
			);
		endif;
	endif;
	
	ksort( $submenu_items );
	
	foreach( $submenu_items as $item ) :
		$wp_admin_bar->add_menu( array(
			'id'	 => $item['id'],
			'parent' => 'events-main',
			'title'  => $item['name'],
			'href'   => $item['href']
		) );
	endforeach;
	
	if( bp_is_active( 'settings' ) ) :
		$wp_admin_bar->add_menu( array(
			'id'	 => 'events-settings',
			'parent' => 'my-account-settings',
			'title'  => __( 'Events', 'events' ),
			'href'   => bp_loggedin_user_domain() . bp_get_settings_slug() . '/'. bpe_get_base( 'slug' )
		) );
	endif;
}
add_action( 'bp_setup_admin_bar', 'bpe_add_wp_admin_bar_menus' );

/**
 * Add a single event nav item to the WP admin bar
 *
 * @package Core
 * @since 	2.0.6
 */
function bpe_add_event_admin_bar_menu()
{
	global $wp_admin_bar, $bp;
	
	// Only show if viewing an event
	if ( ! bpe_get_displayed_event() || bpe_is_events_create() || is_admin() )
		return false;

	if ( ! bpe_is_admin( bpe_get_displayed_event() ) )
		return false;

	if( bpe_is_event_cancelled( bpe_get_displayed_event() ) )
		return false;

	$avatar = bp_core_fetch_avatar( array(
		'object'     => 'event',
		'type'       => 'thumb',
		'avatar_dir' => 'event-avatars',
		'item_id'    => bpe_get_event_id( bpe_get_displayed_event() ),
		'width'      => 16,
		'height'     => 16,
		'no_grav'	 => true
	) );

	$bp->event_admin_menu_id = ( ! empty( $avatar ) ) ? 'event-admin-with-avatar' : 'event-admin';
	
	if( is_admin() ) :
		$event = ( isset( $_GET['event'] ) ) ? $_GET['event'] : '';
		$paged = ( isset( $_GET['paged'] ) ) ? $_GET['paged'] : '';

		$url = admin_url( 'admin.php?page='. EVENT_FOLDER .'&paged='. $paged .'&event='. $event .'&step=' );
	else :
		$url = bpe_get_event_link( bpe_get_displayed_event() ) . bpe_get_option( 'edit_slug' ) .'/';
	endif;
	
	$wp_admin_bar->add_menu( array(
		'id'    => $bp->event_admin_menu_id,
		'title' => $avatar . bpe_get_displayed_event( 'name' ),
		'href'  => bpe_get_event_link( bpe_get_displayed_event() )
	) );
	
	$wp_admin_bar->add_menu( array(
		'id'	 => 'events-edit-general',
		'parent' => $bp->event_admin_menu_id,
		'title'  => __( 'Edit General', 'events' ),
		'href'   => $url . bpe_get_option( 'general_slug' )
	) );

	if( in_array( bpe_get_option( 'enable_schedules' ), array( 1, 2, 3 ) ) )
		$wp_admin_bar->add_menu( array(
			'id'	 => 'events-edit-schedules',
			'parent' => $bp->event_admin_menu_id,
			'title'  => __( 'Edit Schedules', 'events' ),
			'href'   => $urll . bpe_get_option( 'schedule_slug' )
		) );

	if( in_array( bpe_get_option( 'enable_documents' ), array( 1, 2, 3 ) ) )
		$wp_admin_bar->add_menu( array(
			'id'	 => 'events-edit-documents',
			'parent' => $bp->event_admin_menu_id,
			'title'  => __( 'Edit Documents', 'events' ),
			'href'   => $url . bpe_get_option( 'documents_slug' )
		) );

	if( bpe_get_option( 'enable_tickets' ) === true )
		$wp_admin_bar->add_menu( array(
			'id'	 => 'events-edit-tickets',
			'parent' => $bp->event_admin_menu_id,
			'title'  => __( 'Edit Tickets', 'events' ),
			'href'   => $url . bpe_get_option( 'ticket_slug' )
		) );
		
	if( bpe_get_option( 'enable_attendees' ) === true )
		$wp_admin_bar->add_menu( array(
			'id'	 => 'events-edit-attendees',
			'parent' => $bp->event_admin_menu_id,
			'title'  => __( 'Edit Attendees', 'events' ),
			'href'   => $url . bpe_get_option( 'manage_slug' )
		) );

	if( bpe_get_option( 'enable_logo' ) === true )
		$wp_admin_bar->add_menu( array(
			'id'	 => 'events-edit-logo',
			'parent' => $bp->event_admin_menu_id,
			'title'  => __( 'Upload Logo', 'events' ),
			'href'   => $url . bpe_get_option( 'logo_slug' )
		) );

	$forum_id = bpe_get_eventmeta( bpe_get_displayed_event( 'id' ), 'forum_id' );

	if( bpe_get_option( 'enable_forums' ) === true && ! empty( $forum_id ) )
		$wp_admin_bar->add_menu( array(
			'id'	 => 'events-edit-forum',
			'parent' => $bp->event_admin_menu_id,
			'title'  => __( 'Edit Forum', 'events' ),
			'href'   => $url . bpe_get_option( 'forum_slug' )
		) );
		
	$wp_admin_bar->add_menu( array(
		'id'	 => 'events-cancel-event',
		'parent' => $bp->event_admin_menu_id,
		'title'  => __( 'Cancel Event', 'events' ),
		'href'   => wp_nonce_url( $url .'cancel', 'bpe_cancel_event_now' ),
		'meta'   => array( 'onclick' => 'confirm(" ' . __( 'Are you sure you want to cancel this event? This cannot be undone.', 'events' ) . '");' )
	) );

	$wp_admin_bar->add_menu( array(
		'id'	 => 'events-delete-event',
		'parent' => $bp->event_admin_menu_id,
		'title'  => __( 'Delete Event', 'events' ),
		'href'   => wp_nonce_url( $url .'delete', 'bpe_delete_event_now' ),
		'meta'   => array( 'onclick' => 'confirm(" ' . __( 'Are you sure you want to delete this event? This cannot be undone.', 'events' ) . '");' )
	) );
}
add_action( 'admin_bar_menu', 'bpe_add_event_admin_bar_menu', 40 );
?>