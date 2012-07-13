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
 * Maybe redirect an active event to an archived one and vice versa
 * 
 * By default events are available both under /active/ and /archive/.
 * To stop this from happening we redirect to the correct directory
 * if needed.
 * 
 * @package Core
 * @since 	2.1
 */
function bpe_redirect_active_archived_events()
{
	global $bp;
	
	if( ! bpe_is_single_event() )
		return false;
	
	$redirect = false;
	
	if( bpe_is_closed_event( bpe_get_displayed_event() ) && bp_is_current_action( bpe_get_option( 'active_slug' ) ) ) :
		$slug 	  = bpe_get_option( 'archive_slug' );
		$redirect = true;
	elseif( ! bpe_is_closed_event( bpe_get_displayed_event() ) && bp_is_current_action( bpe_get_option( 'archive_slug' ) ) ) :
		$slug 	  = bpe_get_option( 'active_slug' );
		$redirect = true;
	endif;
	
	if( $redirect === true ) :
		$link  = bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. $slug .'/';
		$link .= trailingslashit( join( '/', (array)$bp->action_variables ) );
		
		$link = apply_filters( 'bpe_redirect_active_archived_events_link', $link, $slug );
		
		bp_core_redirect( $link );
	endif;
}
add_action( 'wp', 'bpe_redirect_active_archived_events', 0 );

/**
 * Count days between 2 dates (incl. first day)
 * expects timestamp input
 * 
 * @package Core
 * @since 	2.0
 */
function bpe_count_days( $a, $b )
{
	$gd_a = getdate( $a );
	$gd_b = getdate( $b );

	$a_new = mktime( 12, 0, 0, $gd_a['mon'], $gd_a['mday'], $gd_a['year'] );
	$b_new = mktime( 12, 0, 0, $gd_b['mon'], $gd_b['mday'], $gd_b['year'] );

	return ( round( abs( $a_new - $b_new ) / 86400 ) );
}

/**
 * Make sure that groups are activated and that we are on a groups page
 * 
 * @package Core
 * @since 	2.1
 */
function bpe_is_groups()
{
	if( ! bp_is_active( 'groups' ) )
		return false;
	
	if( ! bp_is_current_component( bp_get_groups_slug() ) )
		return false;
	
	return true;
}

/**
 * Display the management buttons
 *
 * @package Core
 * @since 	1.3
 */
function bpe_attendee_management_buttons( $user_id, $event, $context )
{
	if( $context == 'not_attending' )
		return false;

	if( ! bpe_is_admin( $event ) )
		return false;

	if( $user_id == bpe_get_event_user_id( $event ) )
		return false;

	if( $context != 'admins' && $context != 'maybe' )
		$button[] = '<a class="confirm" href="'. wp_nonce_url( bpe_get_management_link( 'promote-admin', $user_id ), 'bpe_promote_admin' ) .'">'. __( 'Promote to admin', 'events' ) .'</a>';

	if( $context == 'attendees' )
		$button[] = '<a class="confirm" href="'. wp_nonce_url( bpe_get_management_link( 'promote-organizer', $user_id ), 'bpe_promote_organizer' ) .'">'. __( 'Promote to organizer', 'events' ) .'</a>';

	if( $context == 'admins' )
		$button[] = '<a class="confirm" href="'. wp_nonce_url( bpe_get_management_link( 'demote-organizer', $user_id ), 'bpe_demote_organizer' ) .'">'. __( 'Demote to organizer', 'events' ) .'</a>';

	if( $context == 'admins' || $context == 'organizers' )
		$button[] = '<a class="confirm" href="'. wp_nonce_url( bpe_get_management_link( 'demote-attendee', $user_id ), 'bpe_demote_attendee' ) .'">'. __( 'Demote to attendee', 'events' ) .'</a>';

	$button[] = '<a class="confirm" href="'. wp_nonce_url( bpe_get_management_link( 'remove', $user_id ), 'bpe_remove_attendee' ) .'">'. __( 'Remove', 'events' ) .'</a>';

	$button = implode( ' - ', $button );

	echo $button;
}
add_action( 'bpe_directory_members_actions', 'bpe_attendee_management_buttons', 10, 3 );

/**
 * Get member management links
 *
 * @package Core
 * @since 	1.7
 */
function bpe_get_management_link( $action, $user_id )
{
	global $bp;

	if( is_admin() )
		return admin_url( 'admin.php?page='. EVENT_FOLDER .'&paged='. $_GET['paged'] .'&event='. $_GET['event'] .'&step='. bpe_get_option( 'manage_slug') .'&action='. $action .'&user_id='. $user_id );
	else
		return bpe_get_event_link() . bpe_get_option( 'edit_slug' ) .'/'. bpe_get_option( 'manage_slug' ) .'/'. $action .'/'. $user_id .'/';
}

/**
 * Replace the default avatar with our default logo
 *
 * @package Core
 * @since 	2.1
 */
function bpe_replace_default_avatar( $avatar )
{
	return bpe_get_config( 'default_logo' );
}
add_filter( 'bp_core_default_avatar_event', 'bpe_replace_default_avatar' );

/**
 * Add a message
 *
 * @package Core
 * @since 	1.5
 */
function bpe_add_message( $message, $type = false )
{
	if( is_admin() )
		bpe_admin_add_notice( $message, $type );
	else
		bp_core_add_message( $message, $type );
}

/**
 * Create redirect success url
 *
 * @package Core
 * @since 	1.5
 */
function bpe_create_redirect_success_url( $slug )
{
	if( is_admin() )
		return admin_url( 'admin.php?page='. EVENT_FOLDER );

	else
		return bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. bpe_get_option( 'active_slug' ) .'/'. $slug .'/';
}

/*
* Localize the month names
 *
 * @package Core
 * @since 	1.5
 */
function bpe_localize_month_name( $month, $year )
{
	if( bpe_get_option( 'localize_months' ) )
		$month_name = strftime( '%B', mktime( 0, 0, 0, $month, 1, $year ) );
	else
		$month_name = gmdate( 'F', mktime( 0, 0, 0, $month, 1, $year ) );

	return $month_name;
}

/**
 * Check a value
 *
 * @package Core
 * @since 	1.2
 */
function bpe_check_value( $value, $default = false )
{
	echo ( empty( $value ) ) ? $default : $value;
}

/**
 * Display all categories in a dropdown
 *
 * @package Core
 * @since 	1.1
 */
function bpe_category_dropdown( $value = false, $label = true )
{
	$categories = bpe_get_event_categories();
	
	if( $label ) : ?>
	<label for="category"><?php _e( '* Category', 'events' ) ?></label>
    <?php endif; ?>
	<select id="category" name="category">
		<option value="">----</option>
		<?php foreach( $categories as $key => $val ) { ?>
			<option<?php if( $value == $val->id ) echo ' selected="selected"'; ?> value="<?php echo $val->id ?>"><?php echo $val->name ?></option>
		<?php } ?>
	</select>
	<?php
}

/**
 * Check user access based on groups (public/private/hidden) and events (public/private)
 *
 * @package Core
 * @since 	1.0
 */
function bpe_restrict_event_access( $event )
{
	// don't restrict if there is no event
	if( ! $event )
		return false;
	
	// site admins can do anything
	if( is_super_admin() )
		return false;

	// don't restrict if user is event creator
	if( bp_loggedin_user_id() == bpe_get_event_user_id( $event ) )
		return false;
	
	$status  = bpe_event_get_group_status( $event );
	$invites = bpe_get_invitations( $event );

	// user is logged in
	if( is_user_logged_in() )
	{
		// NOT attached to a group
		if( ! bpe_get_event_group_id( $event ) )
		{
			// skip if private event and current user is not invited
			if( bpe_get_event_public( $event ) != 1 )
				if( ! in_array( bp_loggedin_user_id(), $invites ) )
					return true;
		}
		// attached to a group
		else
		{
			if( $status == 'public' )
			{
				// skip if event is private and user isn't invited
				if( bpe_get_event_public( $event ) != 1 )
					if( ! in_array( bp_loggedin_user_id(), $invites ) )
						return true;
			}
			elseif( $status == 'private' || $status == 'hidden' )
			{
				// skip if user isn't a member of the group
				if( ! groups_is_user_member( bp_loggedin_user_id(), bpe_get_event_group_id( $event ) ) )
					return true;								
				
				// skip if event is private and user isn't invited
				if( bpe_get_event_public( $event ) != 1 )
					if( ! in_array( bp_loggedin_user_id(), $invites ) )
						return true;
			}
		}
	}
	// user is logged out
	else
	{
		// NOT attached to a group
		if( ! bpe_get_event_group_id( $event ) )
		{
			// skip if event is private
			if( bpe_get_event_public( $event ) != 1 )
				return true;
		}
		// attached to a group
		else
		{
			// skip if event is private / group is non-public
			if( $status != 'public' || bpe_get_event_public( $event ) != 1 )
				return true;
		}
	}
	
	return false;
}

/**
 * Display cookie information
 *
 * @package Core
 * @since 	1.0
 */
function bpe_display_cookie( $field, $echo = true, $name = 'buddyvents_submission' )
{
	if( $field == 'location' && isset( $_GET['group'] ) )
	{
		$addr = groups_get_groupmeta( $_GET['group'], 'group_address' );
		
		if( ! is_array( $addr ) )
			return false;

		$address_parts = array();
		
		if( ! empty( $addr['street'] ) )
			$address_parts[] = $addr['street'];
	
		if( ! empty( $addr['city'] ) )
			$address_parts[] = $addr['city'];
	
		if( ! empty( $addr['postcode'] ) )
			$address_parts[] = $addr['postcode'];
	
		if( ! empty( $addr['country'] ) )
			$address_parts[] = $addr['country'];

		echo implode( ', ', $address_parts );
		return false;
	}
	
	$cookie = ( isset( $_COOKIE[$name] ) ) ? maybe_unserialize( stripslashes( $_COOKIE[$name] ) ) : false;
	
	if( $cookie === false )
		return false;
	
	$value = wp_filter_kses( $cookie[$field] );
	
	if( $echo )
		echo stripslashes( $value ); 
	else
		return stripslashes( $value ); 
}

/**
 * Show the view style links
 *
 * @package Core
 * @since 	1.1
 */
function bpe_view_link( $type )
{
	echo bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. bpe_get_option( 'view_slug' ) .'/'. $type .'/';
}

/**
 * Checks for grid style
 *
 * @package Core
 * @since 	1.1
 */
function bpe_grid_style()
{
	if( ! in_array( bpe_get_option( 'grid_slug' ), bpe_get_config( 'view_styles' ) ) )
		return false;
 	
	$view = ( isset( $_COOKIE['bpe_view_style'] ) ) ? $_COOKIE['bpe_view_style'] : bpe_get_option( 'default_view' );
	
	if( $view == bpe_get_option( 'grid_slug' ) )
		return true;
		
	return false;
}

/**
 * Turns encoded umlauts etc back into plain letters
 * 
 * Can be extended via <code>bpe_safe_email_text</code> to make other languages than German safe for sending
 * Attached to <code>wp_mail</code>
 *
 * @package Core
 * @since 	2.0.6
 */
function bpe_safe_email_text( $email_vars )
{
	extract( $email_vars );
	
	$vars = apply_filters( 'bpe_safe_email_text', array(
		'look_for'	   => array( '&auml;', '&ouml;', '&uuml;', '&Auml;', '&Ouml;', '&Uuml;' ),
		'replace_with' => array( 'ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü' )
	) );
	
	// we only modify the message and the subject, nothing else
	$message = str_replace( $vars['look_for'], $vars['replace_with'], $message );
	$subject = str_replace( $vars['look_for'], $vars['replace_with'], $subject );
	
	return compact( 'to', 'subject', 'message', 'headers', 'attachments' );
}
add_filter( 'wp_mail', 'bpe_safe_email_text' );

/**
 * Adds a view style class
 *
 * @package Core
 * @since 	1.1
 */
function bpe_view_class( $type )
{
	$cookie = ( isset( $_COOKIE['bpe_view_style'] ) ) ? $_COOKIE['bpe_view_style'] : false;

	if( $cookie == $type || ! $cookie && $type == bpe_get_option( 'list_slug' ) )
		echo ' active-view';
	else
		echo ' inactive-view';
}

/**
 * Change per_page value based on view
 *
 * @package Core
 * @since 	1.1
 */
function bpe_get_view_per_page()
{
	$cookie = ( isset( $_COOKIE['bpe_view_style'] ) ) ? $_COOKIE['bpe_view_style'] : false;
	return apply_filters( 'bpe_get_view_per_page', ( ( $cookie == bpe_get_option( 'grid_slug' )  ) ? 25 : 10 ), $cookie );
}

/**
 * Get and save the clear chache timestamp
 *
 * @package Core
 * @since 	1.3
 */
function bpe_get_clear_cache_timestamp( $event )
{
	$timestamp = strtotime( bpe_get_event_end_date_raw( $event ) .' '. bpe_get_event_end_time_raw( $event ) ) + 300;
	
	bpe_update_eventmeta( bpe_get_event_id( $event ), 'bpe_scheduled_cache_timestamp', $timestamp );
	
	return $timestamp;
}

/**
 * Remove danish characters
 *
 * @package Core
 * @since 	1.2.3
 */
function bpe_remove_accents( $string ) 
{
	$chars = array( '%c3%b8', '%c3%a6' );
	$repl = array( 'o', 'ae' );
	
	$string = str_replace( $chars, $repl, $string );

	return $string;
}

/**
 * Adjust the page title
 *
 * @package Core
 * @since 	1.0
 */
function bpe_adjust_page_title( $page_title_sep, $page_title, $sep, $seplocation )
{
	global $paged, $page;
	
	// don't change anything on non buddyvents pages
	if( ! bp_is_current_component( bpe_get_base( 'slug' ) ) )
		return $page_title_sep;
		
	if( bpe_is_events_create() )
		$title = __( 'Create Event', 'events' );

	elseif( bpe_is_event_month_archive() )
	{
		$paged = $page = 1;
		$title = sprintf( __( 'Monthly Archive for %s %s', 'events' ), mysql2date( 'F', bp_action_variable( 1 ) .'-'. bp_action_variable( 0 ), true ), bp_action_variable( 1 ) );
	}
	elseif( bpe_is_event_day_archive() )
		$title = sprintf( __( 'Daily Archive for %s', 'events' ), mysql2date( bpe_get_option( 'date_format' ), bp_action_variable( 0 ), true ) );

	elseif( bpe_is_event_category() )
		$title = sprintf( __( 'Event Category: %s', 'events' ), ucwords( stripslashes( bp_action_variable( 0 ) ) ) );

	elseif( bpe_is_event_search_results() )
		$title = sprintf( __( 'Search for %s', 'events' ), bpe_get_search_query() );

	elseif( bpe_is_edit_event() )
		$title = stripslashes( bpe_get_displayed_event( 'name' ) ) .' '. $sep .' '. __( 'Edit Event', 'events' );

	elseif( bpe_is_event_attendees() )
		$title = stripslashes( bpe_get_displayed_event( 'name' ) ) .' '. $sep .' '. __( 'Attendees', 'events' );
		
	elseif( bpe_is_invite_event_page() )
		$title = stripslashes( bpe_get_displayed_event( 'name' ) ) .' '. $sep .' '. __( 'Invite', 'events' );
		
	elseif( bpe_is_event_directions() )
		$title = stripslashes( bpe_get_displayed_event( 'name' ) ) .' '. $sep .' '. __( 'Directions', 'events' );

	elseif( bpe_is_single_event() )
		$title = stripslashes( bpe_get_displayed_event( 'name' ) ) .' '. $sep .' '. __( 'Home', 'events' );

	elseif( bpe_is_event_search() )
		$title = __( 'Search', 'events' );

	elseif( bpe_is_member_active() )
		$title = __( 'Active Events', 'events' );

	elseif( bpe_is_member_sale_success() || bpe_is_sale_success() )
		$title = __( 'Transaction completed', 'events' );

	elseif( bpe_is_member_sale_cancel() || bpe_is_sale_cancel() )
		$title = __( 'Tansaction cancelled', 'events' );

	elseif( bpe_is_member_attending() )
		$title = __( 'Attending Events', 'events' );

	elseif( bpe_is_events_map() )
		$title = __( 'Events Map', 'events' );

	elseif( bpe_is_events_calendar() )
		$title = __( 'Events Calendar', 'events' );

	elseif( bpe_is_events_archive() )
		$title = __( 'Events Archive', 'events' );

	elseif( bpe_is_events_directory_loop() )
		$title = __( 'Active Events', 'events' );
		
	else
		$title = $page_title;
	
	$title = apply_filters( 'bpe_adjust_page_title', $title, $sep );
		
	return esc_attr( $title ) .' '. $sep .' ';
}
add_filter( 'bp_modify_page_title', 'bpe_adjust_page_title', 10, 4 );

/**
 * Get a short bit.ly url
 *
 * @package Core
 * @since 	1.6
 */
function bpe_get_bitly_url( $url )
{
	if( defined( 'WP_LOCAL_DEV' ) && WP_LOCAL_DEV )
		return false;

	if( ! bpe_get_option( 'bitly_login' ) || ! bpe_get_option( 'bitly_key' ) )
		return $url;
	
	$bitly_url = 'http://api.bit.ly/v3/shorten?login='. bpe_get_option( 'bitly_login' ) .'&apiKey='. bpe_get_option( 'bitly_key' ) .'&longUrl='. urlencode( $url ) .'&format=txt';

	$data = wp_remote_get( $bitly_url );
		
	return wp_remote_retrieve_body( $data );
}

/**
 * Check an url
 *
 * @package Core
 * @since 	1.7
 */
function bpe_is_url( $url )
{
	return preg_match( '|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url );
}

/**
 * Add an item to the random menu point
 *
 * @package Core
 * @since 	1.0
 */
function bpe_random_event_adminbar()
{
    echo '<li><a href="'. bp_get_root_domain() . '/' . bpe_get_base( 'root_slug' ) . '/?random-event">'. __( 'Random Event', 'events' ) .'</a></li>';
}
add_action( 'bp_adminbar_random_menu', 'bpe_random_event_adminbar' );

/**
 * Redirect to a random event
 *
 * @package Core
 * @since 	1.0
 */
function bpe_random_event()
{
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && isset( $_GET['random-event'] ) )
	{
		$event = bpe_get_events( array( 'sort' => 'random', 'public' => 1, 'per_page' => 1, 'future' => true, 'past' => false ) );

		bp_core_redirect( bp_get_root_domain() . '/' . bpe_get_base( 'root_slug' ) . '/active/' . $event['events'][0]->slug . '/' );
	}
}
add_action( 'wp', 'bpe_random_event', 0 );

/**
 * Get all countries
 *
 * @package Core
 * @since 	1.0
 */
function bpe_countries()
{
	return array(
		__( 'Afghanistan', 'events' ), __( 'Albania', 'events' ), __( 'Algeria', 'events' ), __('American Samoa', 'events' ), __('Andorra', 'events' ),
		__( 'Angola', 'events' ), __( 'Antigua and Barbuda', 'events' ), __( 'Argentina', 'events' ), __( 'Armenia', 'events' ), __( 'Australia', 'events' ),
		__( 'Austria', 'events' ), __( 'Azerbaijan', 'events' ), __( 'Bahamas', 'events' ), __( 'Bahrain', 'events' ), __( 'Bangladesh', 'events' ),
		__( 'Barbados', 'events' ), __( 'Belarus', 'events' ), __( 'Belgium', 'events' ), __( 'Belize', 'events' ), __( 'Benin', 'events' ), __( 'Bermuda', 'events' ),
		__( 'Bhutan', 'events' ), __( 'Bolivia', 'events' ), __( 'Bosnia and Herzegovina', 'events' ), __( 'Botswana', 'events' ), __( 'Brazil', 'events' ),
		__( 'Brunei', 'events' ), __( 'Bulgaria', 'events' ), __( 'Burkina Faso', 'events' ), __( 'Burundi', 'events' ), __( 'Cambodia', 'events' ),
		__( 'Cameroon', 'events' ), __('Canada', 'events' ), __( 'Cape Verde', 'events' ), __( 'Central African Republic', 'events' ), __( 'Chad', 'events' ),
		__( 'Chile', 'events' ), __( 'China', 'events' ), __( 'Colombia', 'events' ), __( 'Comoros', 'events' ), __( 'Congo', 'events' ), __( 'Costa Rica', 'events' ),
		__( 'C&ocirc;te d\'Ivoire', 'events' ), __( 'Croatia', 'events' ), __( 'Cuba', 'events' ), __( 'Cyprus', 'events' ), __( 'Czech Republic', 'events' ),
		__( 'Denmark', 'events' ), __( 'Djibouti', 'events' ), __( 'Dominica', 'events' ), __( 'Dominican Republic', 'events' ), __( 'East Timor', 'events' ),
		__( 'Ecuador', 'events' ), __( 'Egypt', 'events' ), __( 'El Salvador', 'events' ), __( 'Equatorial Guinea', 'events' ), __( 'Eritrea', 'events' ),
		__( 'Estonia', 'events' ), __( 'Ethiopia', 'events' ), __( 'Fiji', 'events' ), __( 'Finland', 'events' ), __( 'France', 'events' ), __( 'Gabon', 'events' ),
		__( 'Gambia', 'events' ), __( 'Georgia', 'events' ), __( 'Germany', 'events' ), __( 'Ghana', 'events' ), __( 'Greece', 'events' ), __( 'Grenada', 'events' ),
		__( 'Guam', 'events' ), __( 'Guatemala', 'events' ), __( 'Guinea', 'events' ), __( 'Guinea-Bissau', 'events' ), __( 'Guyana', 'events' ), __( 'Haiti', 'events' ),
		__( 'Honduras', 'events' ), __( 'Hong Kong', 'events' ), __( 'Hungary', 'events' ), __( 'Iceland', 'events' ), __( 'India', 'events' ),
		__( 'Indonesia', 'events' ), __( 'Iran', 'events' ), __( 'Iraq', 'events' ), __( 'Ireland', 'events' ), __( 'Israel', 'events' ), __( 'Italy', 'events' ),
		__( 'Jamaica', 'events' ), __( 'Japan', 'events' ), __( 'Jordan', 'events' ), __( 'Kazakhstan', 'events' ), __( 'Kenya', 'events' ), __( 'Kiribati', 'events' ),
		__( 'North Korea', 'events' ), __( 'South Korea', 'events' ), __( 'Kuwait', 'events' ), __( 'Kyrgyzstan', 'events' ), __( 'Laos', 'events' ),
		__( 'Latvia', 'events' ), __( 'Lebanon', 'events' ), __( 'Lesotho', 'events' ), __( 'Liberia', 'events' ), __( 'Libya', 'events' ),
		__( 'Liechtenstein', 'events' ), __( 'Lithuania', 'events' ), __( 'Luxembourg', 'events' ), __( 'Macedonia', 'events' ), __( 'Madagascar', 'events' ),
		__( 'Malawi', 'events' ), __( 'Malaysia', 'events' ), __( 'Maldives', 'events' ), __( 'Mali', 'events' ), __( 'Malta', 'events' ),
		__( 'Marshall Islands', 'events' ), __( 'Mauritania', 'events' ), __( 'Mauritius', 'events' ), __( 'Mexico', 'events' ), __( 'Micronesia', 'events' ),
		__( 'Moldova', 'events' ), __( 'Monaco', 'events' ), __( 'Mongolia', 'events' ), __( 'Montenegro', 'events' ), __( 'Morocco', 'events' ),
		__( 'Mozambique', 'events' ), __( 'Myanmar', 'events' ), __( 'Namibia', 'events' ), __( 'Nauru', 'events' ), __( 'Nepal', 'events' ),
		__( 'Netherlands', 'events' ), __( 'New Zealand', 'events' ), __( 'Nicaragua', 'events' ), __( 'Niger', 'events' ), __( 'Nigeria', 'events' ),
		__( 'Norway', 'events' ), __('Northern Mariana Islands', 'events' ), __('Oman', 'events' ), __( 'Pakistan', 'events' ), __( 'Palau', 'events' ),
		__( 'Palestine', 'events' ), __( 'Panama', 'events' ), __( 'Papua New Guinea', 'events' ), __( 'Paraguay', 'events' ), __( 'Peru', 'events' ),
		__( 'Philippines', 'events' ), __( 'Poland', 'events' ), __( 'Portugal', 'events' ), __( 'Puerto Rico', 'events' ), __( 'Qatar', 'events' ),
		__( 'Romania', 'events' ), __( 'Russia', 'events' ), __( 'Rwanda', 'events' ), __( 'Saint Kitts and Nevis', 'events' ), __( 'Saint Lucia', 'events' ),
		__( 'Saint Vincent and the Grenadines', 'events' ), __( 'Samoa', 'events' ), __( 'San Marino', 'events' ), __( 'Sao Tome and Principe', 'events' ),
		__( 'Saudi Arabia', 'events' ), __( 'Senegal', 'events' ), __( 'Serbia and Montenegro', 'events' ), __( 'Seychelles', 'events' ),
		__( 'Sierra Leone', 'events' ), __( 'Singapore', 'events' ), __( 'Slovakia', 'events' ), __( 'Slovenia', 'events' ), __( 'Solomon Islands', 'events' ),
		__( 'Somalia', 'events' ), __( 'South Africa', 'events' ), __( 'Spain', 'events' ), __( 'Sri Lanka', 'events' ), __( 'Sudan', 'events' ),
		__( 'Suriname', 'events' ), __( 'Swaziland', 'events' ), __( 'Sweden', 'events' ), __( 'Switzerland', 'events' ), __( 'Syria', 'events' ),
		__( 'Taiwan', 'events' ), __( 'Tajikistan', 'events' ), __( 'Tanzania', 'events' ), __( 'Thailand', 'events' ), __( 'Togo', 'events' ),
		__( 'Tonga', 'events' ), __( 'Trinidad and Tobago', 'events' ), __( 'Tunisia', 'events' ), __( 'Turkey', 'events' ), __( 'Turkmenistan', 'events' ),
		__( 'Tuvalu', 'events' ), __( 'Uganda', 'events' ), __( 'Ukraine', 'events' ), __( 'United Arab Emirates', 'events' ), __( 'United Kingdom', 'events' ),
		__('United States', 'events' ), __( 'Uruguay', 'events' ), __( 'Uzbekistan', 'events' ), __( 'Vanuatu', 'events' ), __( 'Vatican City', 'events' ),
		__( 'Venezuela', 'events' ), __( 'Vietnam', 'events' ), __('Virgin Islands, British', 'events' ), __('Virgin Islands, U.S.', 'events' ),
		__( 'Yemen', 'events' ), __( 'Zambia', 'events' ), __( 'Zimbabwe', 'events' )
	);
}

/**
 * Dropdown of all countries
 * Props to Gravity Forms
 *
 * @package Core
 * @since 	1.0
 */
function bpe_country_select( $selected_country = '' )
{
	$countries = array_merge( array(''), bpe_countries() );
	foreach( $countries as $country )
	{
		$selected = ( $country == $selected_country ) ? ' selected="selected"' : '';
		$options .= '<option value="'. esc_attr( $country ) .'"'. $selected .'>'. $country .'</option>';
	}
	
	echo $options;
}

/**
 * Get ticket currencies
 *
 * @package Core
 * @since 	2.0
 */
function bpe_ticket_currencies()
{
	return apply_filters( 'bpe_ticket_currencies', array(
		'EUR' => __( 'Euro', 'events' ),
		'AUD' => __( 'Australian Dollar', 'events' ),
		'CAD' => __( 'Canadian Dollar', 'events' ),
		'CZK' => __( 'Czech Koruna', 'events' ),
		'DKK' => __( 'Danish Krone', 'events' ),
		'HKD' => __( 'Hong Kong Dollar', 'events' ),
		'HUF' => __( 'Hungarian Forint', 'events' ),
		'ILS' => __( 'Israeli New Sheqel', 'events' ),
		'JPY' => __( 'Japanese Yen', 'events' ),
		'MXN' => __( 'Mexican Peso', 'events' ),
		'NOK' => __( 'Norwegian Krone', 'events' ),
		'NZD' => __( 'New Zealand Dollar', 'events' ),
		'PHP' => __( 'Philippine Peso', 'events' ),
		'PLN' => __( 'Polish Zloty', 'events' ),
		'GBP' => __( 'Pound Sterling', 'events' ),
		'SGD' => __( 'Singapore Dollar', 'events' ),
		'SEK' => __( 'Swedish Krona', 'events' ),
		'CHF' => __( 'Swiss Franc', 'events' ),
		'TWD' => __( 'Taiwan New Dollar', 'events' ),
		'THB' => __( 'Thai Baht', 'events' ),
		'TRY' => __( 'Turkish Lira', 'events' ),
		'USD' => __( 'U.S. Dollar', 'events' )
	) );
}

/**
 * Check an array or object for being empty
 * 
 * @package Core
 * @since 	1.0
 * 
 * @param 	object	$object
 * @return 	boolean
 */
function bpe_check_empty_object( $object )
{
	$empty = array();
	
	foreach( (array)$object as $k => $v )
			$empty[] = ( empty( $v ) ) ? '0' : '1';

	if( in_array( '1', $empty ) )
		return true;
	
	return false;
}

/**
 * Get translatable ticket currencies
 *
 * @package Core
 * @since 	2.0
 */
function bpe_get_translatable_currency( $code )
{
	$currencies = bpe_ticket_currencies();
	return $currencies[$code];
}

/**
 * Send an API message
 *
 * @package API
 * @since 	2.0
 * 
 * @param	string	$message
 * @param	string	$status
 * @param	int		$event_id
 */
function bpe_api_message( $message, $status, $event_id = false )
{
	$api = array( 'status' => $status, 'message' => $message );
	
	if( $event_id )
		$api['event_id'] = $event_id;
		
	return $api;
}

/**
 * Show a WYSIWYG editor or normal textarea, depending on WP version (>3.3)
 *
 * @package Core
 * @since 	2.0.7
 */
function bpe_editor( $content, $id, $name = false )
{
	global $wp_version;
	
	if( ! $name )
		$name = $id;
	
	if( ! function_exists( 'wp_editor' ) && version_compare( $wp_version, '3.3-beta4', '>=' ) == false ) :
		echo '<textarea id="'. esc_attr( $id ) .'" name="'. esc_attr( $name ) .'">'. esc_textarea( $content ) .'</textarea>';
	else :
		wp_editor( $content, $id, array(
			'textarea_name' => $name,
			'media_buttons' => current_user_can( 'upload_files' ),
			'textarea_rows' => 15,
			'editor_class'  => 'no-border',
			'tinymce' 		=> array(
				'theme_advanced_buttons1' => apply_filters( 'bpe_editor_theme_advanced_buttons1', 'bold,italic,strikethrough,underline,|,bullist,numlist,blockquote,|,justifyleft,justifycenter,justifyright,justifyfull,|,spellchecker,|,formatselect,forecolor,|,pastetext,pasteword,removeformat,|,charmap,|,outdent,indent,|,undo,redo' ),
				'theme_advanced_buttons2' => apply_filters( 'bpe_editor_theme_advanced_buttons2', '' ),
			),
			'quicktags'		=> array( 
				'buttons' => apply_filters( 'bpe_editor_quicktags_buttons', 'strong,em,block,del,ins,img,ul,ol,li,code,spell,close' )
			)
		) );
	endif;
}
?>