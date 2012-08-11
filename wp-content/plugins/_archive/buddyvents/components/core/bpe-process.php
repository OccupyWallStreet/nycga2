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
 * Setup the directory
 * 
 * @package	Core
 * @since 	1.0
 */
function bpe_directory_setup()
{
	global $bp;
	
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && ! bp_displayed_user_id() )
	{
		$bp->is_directory = true;
		
		/***************************************************************************
		//
		// CHANGE THE VIEW STYLE
		//
		***************************************************************************/
		if( bp_is_current_action( bpe_get_option( 'view_slug' ) ) && in_array( bp_action_variable( 0 ), bpe_get_config( 'view_styles' ) ) )
		{
			@setcookie( 'bpe_view_style', bp_action_variable( 0 ), time() + 31104000, COOKIEPATH );

			bp_core_add_message( sprintf( __( 'The view has been changed to %s-style', 'events' ), bp_action_variable( 0 ) ) );
			
			if( ! bp_action_variable( 1 ) )
			{
				bp_core_redirect( bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/' );
			}
			elseif( bp_is_action_variable( 'group', 1 ) )
			{
				$group = new BP_Groups_Group( bp_action_variable( 2 ) );
				bp_core_redirect( bp_get_group_permalink( $group ) . bpe_get_base( 'slug' ) .'/' );
			}
			elseif( bp_is_action_variable( 'user', 1 ) )
			{
				bp_core_redirect( bp_core_get_user_domain( bp_action_variable( 2 ) ) . bpe_get_base( 'slug' ) .'/' );
			}
		}

		/***************************************************************************
		//
		// REMOVE USER FROM EVENT
		//
		***************************************************************************/
		if( bp_is_action_variable( bpe_get_option( 'edit_slug' ), 1 ) && bp_is_action_variable( bpe_get_option( 'manage_slug' ), 2 ) && bp_is_action_variable( 'remove', 3 ) && bp_action_variable( 4 ) )
		{
			check_admin_referer( 'bpe_remove_attendee' );
			
			if( bp_is_action_variable( bpe_get_displayed_event( 'user_id' ), 4 ) )
			{
				bp_core_add_message( __( 'You cannot remove yourself from the event.', 'events' ), 'error' );
				bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) . bpe_get_option( 'edit_slug' ) .'/'. bpe_get_option( 'manage_slug' ) .'/' );
			}
			
			if( ! is_super_admin() )
			{
				if( ! in_array( bp_loggedin_user_id(), (array)bpe_get_displayed_event( 'admin_ids' ) ) )
				{
					bp_core_add_message( __( 'Only an event admin can remove attendees.', 'events' ), 'error' );
					bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) . bpe_get_option( 'edit_slug' ) .'/'. bpe_get_option( 'manage_slug' ) .'/' );
				}
			}
			
			bpe_remove_user_from_event( bp_action_variable( 4 ), bpe_get_displayed_event( 'id' ) );
			
			do_action( 'bpe_removed_user_from_event', bpe_get_displayed_event(), bp_action_variable( 4 ) );

			bp_core_add_message( __( 'The user has been removed from the event.', 'events' ) );
			bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) . bpe_get_option( 'edit_slug' ) .'/'. bpe_get_option( 'manage_slug' ) .'/' );
		}

		/***************************************************************************
		//
		// PROMOTE TO ADMIN
		//
		***************************************************************************/
		if( bp_is_action_variable( bpe_get_option( 'edit_slug' ), 1 ) && bp_is_action_variable( bpe_get_option( 'manage_slug' ), 2 ) && bp_is_action_variable( 'promote-admin', 3 ) && bp_action_variable( 4 ) )
		{
			check_admin_referer( 'bpe_promote_admin' );

			if( ! is_super_admin() )
			{
				if( ! in_array( bp_loggedin_user_id(), (array)bpe_get_displayed_event( 'admin_ids' ) ) )
				{
					bp_core_add_message( __( 'Only an event admin can change a member role.', 'events' ), 'error' );
					bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) . bpe_get_option( 'edit_slug' ) .'/'. bpe_get_option( 'manage_slug' ) .'/' );
				}
			}
			
			bpe_set_event_member_role( bp_action_variable( 4 ), bpe_get_displayed_event( 'id' ), 'admin' );

			do_action( 'bpe_promote_user_to_admin', bpe_get_displayed_event(), bp_action_variable( 4 ) );

			bp_core_add_message( __( 'The user has been promoted to admin', 'events' ) );
			bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) . bpe_get_option( 'edit_slug' ) .'/'. bpe_get_option( 'manage_slug' ) .'/' );
		}

		/***************************************************************************
		//
		// PROMOTE TO ORGANIZER
		//
		***************************************************************************/
		if( bp_is_action_variable( bpe_get_option( 'edit_slug' ), 1 ) && bp_is_action_variable( bpe_get_option( 'manage_slug' ), 2 ) && bp_is_action_variable( 'promote-organizer', 3 ) && bp_action_variable( 4 ) )
		{
			check_admin_referer( 'bpe_promote_organizer' );

			if( ! is_super_admin() )
			{
				if( ! in_array( bp_loggedin_user_id(), (array)bpe_get_displayed_event( 'admin_ids' ) ) )
				{
					bp_core_add_message( __( 'Only an event admin can change a member role.', 'events' ), 'error' );
					bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) . bpe_get_option( 'edit_slug' ) .'/'. bpe_get_option( 'manage_slug' ) .'/' );
				}
			}
			
			bpe_set_event_member_role( bp_action_variable( 4 ), bpe_get_displayed_event( 'id' ), 'organizer' );

			do_action( 'bpe_promote_user_to_organizer', bpe_get_displayed_event(), bp_action_variable( 4 ) );

			bp_core_add_message( __( 'The user has been promoted to organizer.', 'events' ) );
			bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) . bpe_get_option( 'edit_slug' ) .'/'. bpe_get_option( 'manage_slug' ) .'/' );
		}

		/***************************************************************************
		//
		// DEMOTE TO ORGANIZER
		//
		***************************************************************************/
		if( bp_is_action_variable( bpe_get_option( 'edit_slug' ), 1 ) && bp_is_action_variable( bpe_get_option( 'manage_slug' ), 2 ) && bp_is_action_variable( 'demote-organizer', 3 ) && bp_action_variable( 4 ) )
		{
			check_admin_referer( 'bpe_demote_organizer' );

			if( ! is_super_admin() )
			{
				if( ! in_array( bp_loggedin_user_id(), (array) bpe_get_displayed_event( 'admin_ids' ) ) )
				{
					bp_core_add_message( __( 'Only an event admin can change a member role.', 'events' ), 'error' );
					bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) . bpe_get_option( 'edit_slug' ) .'/'. bpe_get_option( 'manage_slug' ) .'/' );
				}
			}
			
			if( bp_is_active( 'groups' ) && bpe_get_displayed_event( 'group_id' ) )
			{
				if( groups_is_user_admin( bp_action_variable( 4 ), bpe_get_displayed_event( 'group_id' ) ) )
				{
					bp_core_add_message( __( 'The group admin cannot be removed from this event.', 'events' ), 'error' );
					bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) . bpe_get_option( 'edit_slug' ) .'/'. bpe_get_option( 'manage_slug' ) .'/' );
				}
			}
			
			bpe_set_event_member_role( bp_action_variable( 4 ), bpe_get_displayed_event( 'id' ), 'organizer' );

			do_action( 'bpe_demote_user_to_organizer', bpe_get_displayed_event(), bp_action_variable( 4 ) );

			bp_core_add_message( __( 'The user has been demoted to organizer.', 'events' ) );
			bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) . bpe_get_option( 'edit_slug' ) .'/'. bpe_get_option( 'manage_slug' ) .'/' );
		}

		/***************************************************************************
		//
		// DEMOTE TO ATTENDEE
		//
		***************************************************************************/
		if( bp_is_action_variable( bpe_get_option( 'edit_slug' ), 1 ) && bp_is_action_variable( bpe_get_option( 'manage_slug' ), 2 ) && bp_is_action_variable( 'demote-attendee', 3 ) && bp_action_variable( 4 ) )
		{
			check_admin_referer( 'bpe_demote_attendee' );

			if( ! is_super_admin() )
			{
				if( ! in_array( bp_loggedin_user_id(), (array) bpe_get_displayed_event( 'admin_ids' ) ) )
				{

					bp_core_add_message( __( 'Only an event admin can change a member role.', 'events' ), 'error' );
					bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) . bpe_get_option( 'edit_slug' ) .'/'. bpe_get_option( 'manage_slug' ) .'/' );
				}
			}
			
			if( bp_is_active( 'groups' ) && bpe_get_displayed_event( 'group_id' ) )
			{
				if( groups_is_user_admin( bp_action_variable( 4 ), bpe_get_displayed_event( 'group_id' ) ) )
				{
					bp_core_add_message( __( 'The group admin cannot be removed from this event.', 'events' ), 'error' );
					bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) . bpe_get_option( 'edit_slug' ) .'/'. bpe_get_option( 'manage_slug' ) .'/' );
				}
			}
			
			bpe_set_event_member_role( bp_action_variable( 4 ), bpe_get_displayed_event( 'id' ), 'attendee' );

			do_action( 'bpe_demote_user_to_attendee', bpe_get_displayed_event(), bp_action_variable( 4 ) );

			bp_core_add_message( __( 'The user has been demoted to attendee.', 'events' ) );
			bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) . bpe_get_option( 'edit_slug' ) .'/'. bpe_get_option( 'manage_slug' ) .'/' );
		}

		/***************************************************************************
		//
		// INVITE TO AN EVENT
		//
		***************************************************************************/
		if( isset( $_POST['send-invites'] ) )
		{
			check_admin_referer( 'bpe_invite_members_event' );
			
			bpe_process_event_invitations( $_POST, bpe_get_displayed_event() );
			
			bp_core_add_message( __( 'Your invitations have been sent successfully.', 'events' ) );
			bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) . bpe_get_option( 'invite_slug' ) .'/' );
		}
		
		/***************************************************************************
		//
		// ATTENDING AN EVENT
		//
		***************************************************************************/
		if( bp_is_action_variable( 'attending', 1 ) )
		{
			if( ! bp_action_variable( 2 ) )
				return false;
			
			// no-go for manual attendance if we have tickets
			if( bpe_get_displayed_event( 'has_tickets' ) )
				return false;
			
			// if event is private and current user is not invited
			if( bpe_get_event_public( bpe_get_displayed_event() ) == 0 && ! in_array( bp_loggedin_user_id(), bpe_get_invitations( bpe_get_displayed_event() ) ) )
			{
				bp_core_add_message( __( 'This event is private and you have not been invited.', 'events' ), 'error' );
				bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) );
			}

			if( ! bp_is_action_variable( bp_loggedin_user_id(), 2 ) || ! is_user_logged_in() )
			{
				bp_core_add_message( __( 'You need to be logged in to join this event.', 'events' ), 'error' );
				bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) );
			}

			// check if the event has started already 
			if( bpe_get_displayed_event( 'end_date' ) .' '. bpe_get_displayed_event( 'end_time' ) < bp_core_current_time() )
			{
				bp_core_add_message( __( 'This event is over and registration is closed.', 'events' ), 'error' );
				bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) );
			}
			
			// check if user isn't a member already
			if( $existing_id = bpe_is_user_member_already( bpe_get_displayed_event( 'id' ) ) )
			{
				bp_core_add_message( __( 'You are attending this event already.', 'events' ), 'error' );
				bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) );
			}
			
			if( ! $existing_id )
				$existing_id = bpe_was_user_member( bpe_get_displayed_event( 'id' ) );
			
			if( ! $id = bpe_add_member( $existing_id, bpe_get_displayed_event( 'id' ), bp_loggedin_user_id(), 1, bp_core_current_time(), 'attendee' ) )
				bp_core_add_message( __( 'There was a problem when saving your choice. Please try again.', 'events' ), 'error' );

			else
			{
				do_action( 'bpe_attend_event', bpe_get_displayed_event(), bp_loggedin_user_id() ); 

				bp_core_add_message( __( 'You are attending this event.', 'events' ) );
			}
			
			bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) );
		}

		/***************************************************************************
		//
		// NOT ATTENDING AN EVENT
		//
		***************************************************************************/
		if( bp_is_action_variable( 'not-attending', 1 ) )
		{
			if( ! bp_action_variable( 2 ) )
				return false;

			if( ! bp_is_action_variable( bp_loggedin_user_id(), 2 ) || ! is_user_logged_in() )
			{
				bp_core_add_message( __( 'You need to be logged in to join this event.', 'events' ), 'error' );
				bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) );
			}

			// if event is private and current user is not invited
			if( bpe_get_event_public( bpe_get_displayed_event() ) == 0 && ! in_array( bp_loggedin_user_id(), bpe_get_invitations( bpe_get_displayed_event() ) ) )
			{
				bp_core_add_message( __( 'This event is private and you have not been invited.', 'events' ), 'error' );
				bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) );
			}

			// check if the event has started already 
			if( bpe_get_displayed_event( 'end_date' ) .' '. bpe_get_displayed_event( 'end_time' ) < bp_core_current_time() )
			{
				bp_core_add_message( __( 'This event is over and registration is closed.', 'events' ), 'error' );
				bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) );
			}
			
			// admin can't remove himself
			if( bpe_get_displayed_event( 'user_id' ) == bp_loggedin_user_id() )
			{
				bp_core_add_message( __( 'You are the creator of this event.', 'events' ), 'error' );
				bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) );
			}
			
			// user needs to be member
			if( ! $existing_id = bpe_is_user_member_already( bpe_get_displayed_event( 'id' ) ) )
			{
				bp_core_add_message( __( 'You have to be a member to remove yourself from this event.', 'events' ), 'error' );
				bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) );
			}
			
			if( ! $existing_id )
				$existing_id = bpe_was_user_member( bpe_get_displayed_event( 'id' ) );

			if( ! $id = bpe_add_member( $existing_id, bpe_get_displayed_event( 'id' ), bp_loggedin_user_id(), 0, bp_core_current_time(), 'not_attendee' ) )
				bp_core_add_message( __( 'There was a problem when saving your choice. Please try again.', 'events' ), 'error' );

			else
			{
				if( $existing_id )
					do_action( 'bpe_not_attending_event', bpe_get_displayed_event(), bp_loggedin_user_id() ); 

				do_action( 'bpe_not_attending_event_general', bpe_get_displayed_event(), bp_loggedin_user_id() ); 

				bp_core_add_message( __( 'You chose not to attend this event.', 'events' ) );
			}
			
			bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) );
		}

		/***************************************************************************
		//
		// MAYBE ATTEND AN EVENT
		//
		***************************************************************************/
		if( bp_is_action_variable( 'maybe', 1 ) )
		{
			if( ! bp_action_variable( 2 ) )
				return false;

			// no-go for manual attendance if we have tickets
			if( bpe_get_displayed_event( 'has_tickets' ) )
				return false;

			if( ! bp_is_action_variable( bp_loggedin_user_id(), 2 ) || ! is_user_logged_in() )
			{
				bp_core_add_message( __( 'You need to be logged in to join this event.', 'events' ), 'error' );
				bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) );
			}

			// if event is private and current user is not invited
			if( bpe_get_event_public( bpe_get_displayed_event() ) == 0 && ! in_array( bp_loggedin_user_id(), bpe_get_invitations( bpe_get_displayed_event() ) ) )
			{
				bp_core_add_message( __( 'This event is private and you have not been invited.', 'events' ), 'error' );
				bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) );
			}

			// check if the event has started already 
			if( bpe_get_displayed_event( 'end_date' ) .' '. bpe_get_displayed_event( 'end_time' ) < bp_core_current_time() )
			{
				bp_core_add_message( __( 'This event is over and registration is closed.', 'events' ), 'error' );
				bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) );
			}
			
			// admin can't set himself to maybe
			if( bpe_get_displayed_event( 'user_id' ) == bp_loggedin_user_id() )
			{
				bp_core_add_message( __( 'You are the creator of this event.', 'events' ), 'error' );
				bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) );
			}
			
			// user can't be member yet
			if( $existing_id = bpe_is_user_member_already( bpe_get_displayed_event( 'id' ) ) )
			{
				bp_core_add_message( __( 'You are attending this event already.', 'events' ), 'error' );
				bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) );
			}
			
			if( ! $existing_id )
				$existing_id = bpe_was_user_member( bpe_get_displayed_event( 'id' ) );

			if( ! $id = bpe_add_member( $existing_id, bpe_get_displayed_event( 'id' ), bp_loggedin_user_id(), 2, bp_core_current_time(), 'maybe_attendee' ) )
				bp_core_add_message( __( 'There was a problem when saving your choice. Please try again.', 'events' ), 'error' );

			else
			{	
				do_action( 'bpe_maybe_attend_event', bpe_get_displayed_event(), bp_loggedin_user_id() );

				bp_core_add_message( __( 'You might attend this event.', 'events' ) );
			}

			bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) );
		}

		/***************************************************************************
		//
		// DELETE AN EVENT
		//
		***************************************************************************/
		if( bp_is_action_variable( bpe_get_option( 'edit_slug' ), 1 ) && bp_is_action_variable( 'delete', 2 ) )
		{
			check_admin_referer( 'bpe_delete_event_now' );
			
			bpe_process_event_deletion( bpe_get_displayed_event(), bpe_get_event_link( bpe_get_displayed_event( 'slug' ) ) );			
			bp_core_redirect( bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/' );
		}

		/***************************************************************************
		//
		// CANCEL AN EVENT
		//
		***************************************************************************/
		if( bp_is_action_variable( bpe_get_option( 'edit_slug' ), 1 ) && bp_is_action_variable( 'cancel', 2 ) )
		{
			check_admin_referer( 'bpe_cancel_event_now' );
			
			bpe_process_event_cancellation( bpe_get_displayed_event(), bpe_get_event_link( bpe_get_displayed_event( 'slug' ) ) );			
			bp_core_redirect( bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/' );
		}
		
		/***************************************************************************
		//
		// SEND EVENT COMMENT
		//
		***************************************************************************/
		if( isset( $_POST['send_event_comment'] ) )
		{
			if( ! bp_is_active( 'activity' ) )
				return false;
			
			check_admin_referer( 'bpe_event_comment' );
			
			$comment_text = ( isset( $_POST['comment_text'] ) ) ? $_POST['comment_text'] : false;
						
			if( empty( $comment_text ) )
			{
				bp_core_add_message( __( 'Please enter a comment text', 'events' ), 'error' );
				bp_core_redirect( bp_get_root_domain() . $_POST['_wp_http_referer'] );
			}
			
			bpe_add_event_comment( bpe_get_displayed_event() , $comment_text );

			bp_core_add_message( __( 'Your comment has been added.', 'events' ) );
			bp_core_redirect( bp_get_root_domain() . $_POST['_wp_http_referer'] );
		}

		/***************************************************************************
		//
		// REMOVE EVENT RECURRENCE
		//
		***************************************************************************/
		if( bp_is_action_variable( 'remove-recurrence', 1 ) )
		{
			check_admin_referer( 'bpe_remove_event_schedule_now' );
			
			bpe_unschedule_event( bp_action_variable( 4 ), bp_action_variable( 2 ), urldecode( bp_action_variable( 3 ) ) );

			bp_core_add_message( __( 'Recurrence has been removed successfully!', 'events' ) );
			bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) );
		}

		do_action( 'bpe_directory_setup' );
		bp_core_load_template( apply_filters( 'bpe_template_directory', 'events/index' ) );
	}
}
add_action( 'bp_actions', 'bpe_directory_setup' );

/**
 * Process event editing
 * 
 * @package	Core
 * @since 	1.5
 */
function bpe_process_event_edit( $input, $displayed_event, $api = false )
{
	$public 		= ( isset( $input['public'] ) ) ? 1 : 0;
	$rsvp 			= ( isset( $input['rsvp'] ) ) ? 1 : 0;
	$notify 		= ( isset( $input['notify'] ) ) ? true : false;
	$all_day 		= ( isset( $input['all_day'] ) ) ? 1 : 0;
	$group_id 		= ( isset( $input['group_id'] ) && ! empty( $input['group_id'] ) ) ? $input['group_id'] : 0;
	$start_time 	= $input['start_time'] .':00';
	$end_time 		= $input['end_time'] .':00';
	$limit_members 	= ( empty( $input['limit_members'] ) ) ? 0 : $input['limit_members'];
	$url 			= ( $input['url'] == 'http://' ) ? '' : $input['url'];
	
	if( ! empty( $input['recurrent'] ) )
	{
		$ts1 = strtotime( $input['start_date']  );
		$ts2 = strtotime( $input['end_date'] 	);
		
		$length = bpe_count_days( $ts1, $ts2 );
		$days 	= bpe_get_recurrence_days( $input['recurrent'] );
		
		if( $length >= $days )
		{
			if( ! $api )
			{
				bpe_add_message( __( 'The event is too long for the chosen recurrence interval.', 'events' ), 'error' );
				bp_core_redirect( bp_get_root_domain() . $input['_wp_http_referer'] );
			}
			else
				return bpe_api_message( 'Event too long for chosen recurrence interval', 'failed' );
		}
	}
	
	// check for all required fields
	if( empty( $input['name'] ) || empty( $input['description'] ) || empty( $input['location'] ) || empty( $input['start_date'] ) || empty( $input['start_time'] ) && $all_day == 0 || empty( $input['end_date'] ) && $all_day == 0 || empty( $input['end_time'] ) && $all_day == 0 || empty( $input['category'] ) || bpe_get_option( 'restrict_creation' ) === true && empty( $input['group_id'] ) )
	{
		if( ! $api )
		{
			bpe_add_message( __( 'Please fill in all fields marked by *.', 'events' ), 'error' );
			bp_core_redirect( bp_get_root_domain() . $input['_wp_http_referer'] );
		}
		else
			return bpe_api_message( 'Required data missing', 'failed' );
	}
	
	if( ! empty( $input['limit_members'] ) )
	{
		if( ! is_numeric( $input['limit_members'] ) )
		{
			if( ! $api )
			{
				bpe_add_message( __( 'Please enter a number to limit the attendees.', 'events' ), 'error' );
				bp_core_redirect( bp_get_root_domain() . $input['_wp_http_referer'] );
			}
			else
				return bpe_api_message( 'Limit attendees not numeric', 'failed' );
		}
	}

	if( ! empty( $url ) )
	{
		if( ! bpe_is_url( $url ) )
		{
			if( ! $api )
			{
				bpe_add_message( __( 'Please enter a valid URL', 'events' ), 'error' );
				bp_core_redirect( bp_get_root_domain() . $input['_wp_http_referer'] );
			}
			else
				return bpe_api_message( 'Invalid URL', 'failed' );
		}
	}
	
	do_action( 'bpe_pre_save_event_edited', $displayed_event );
	
	$changed = false;
	
	// check for changes
	if( $input['name'] 			!= bpe_get_event_name( $displayed_event ) 			 ||
		$input['description'] 	!= bpe_get_event_description_raw( $displayed_event ) ||
		$public 				!= bpe_get_event_public( $displayed_event ) 		 ||
		$rsvp 					!= bpe_get_event_rsvp( $displayed_event ) 			 ||
		$group_id 				!= bpe_get_event_group_id( $displayed_event ) 		 ||
		$limit_members 			!= bpe_get_event_limit_members( $displayed_event )	 ||
		$input['recurrent'] 	!= bpe_get_event_recurrent( $displayed_event ) 		 ||
		$input['category'] 		!= bpe_get_event_category_id( $displayed_event ) 	 ||
		$url 					!= bpe_get_event_url_raw( $displayed_event ) 		 ||
		$all_day 				!= bpe_get_event_all_day( $displayed_event ) 		 ||
		$input['venue_name'] 	!= bpe_get_event_venue_name( $displayed_event )
	)
		$changed = true;

	$start_time = ( bpe_get_option( 'clock_type' ) == 12 ) ? date( 'H:i:s', strtotime( $input['start_time'] ) ) : $input['start_time'] .':00';
	$end_time 	= ( bpe_get_option( 'clock_type' ) == 12 ) ? date( 'H:i:s', strtotime( $input['end_time'] 	) ) : $input['end_time'] .':00';

	if( $all_day == 1 )
	{
		if( empty( $input['end_date'] ) )
			$input['end_date'] = $input['start_date'];
			
		$start_time = '00:01';
		$end_time	= '23:59';
	}
	
	// check the dates if anything changed
	if( $input['start_date'] 	!= bpe_get_event_start_date_raw( $displayed_event ) ||
		$start_time 			!= bpe_get_event_start_time_raw( $displayed_event ) ||
		$input['end_date'] 		!= bpe_get_event_end_date_raw( $displayed_event ) 	||
		$end_time 				!= bpe_get_event_end_time_raw( $displayed_event )
	)
	{
		if( $input['start_date'] .' '. $start_time >= $input['end_date'] .' '. $end_time )
		{
			if( ! $api )
			{
				bpe_add_message( __( 'The end date needs to be after the start date.', 'events' ), 'error' );
				bp_core_redirect( bp_get_root_domain() . $input['_wp_http_referer'] );
			}
			else
				return bpe_api_message( 'Start date after end date', 'failed' );
		}
		
		$changed = true;
	}

	// get the coordinates
	if( $input['location'] != bpe_get_event_location( $displayed_event ) || isset( $displayed_event->meta->manual_coords ) && $displayed_event->meta->manual_coords && ! isset( $input['manual_coords'] ) )
	{
		$coords = bpe_get_event_coordinates( $input['location'] );
		$longitude = $coords['lng'];
		$latitude = $coords['lat'];
		
		bpe_delete_eventmeta( bpe_get_event_id( $displayed_event ), 'no_coords' );
		
		$changed = true;
	}
	else
	{
		$longitude = $displayed_event->longitude;
		$latitude = $displayed_event->latitude;
	}

	if( ! isset( $input['manual_coords'] ) )
		bpe_delete_eventmeta( bpe_get_event_id( $displayed_event ), 'manual_coords' );
	
	// override coordinates with manual values
	if( ! empty( $input['longitude'] ) && ! empty( $input['latitude'] ) )
	{
		if( bpe_get_event_longitude( $displayed_event ) != $input['longitude'] || bpe_get_event_latitude( $displayed_event ) != $input['latitude'] )
			$changed = true;
		
		$longitude = $input['longitude'];
		$latitude = $input['latitude'];
		
		bpe_update_eventmeta( bpe_get_event_id( $displayed_event ), 'manual_coords', 1 );
	}

	// check for map
	if( isset( $input['no_coords'] ) )
	{
		$longitude = '';
		$latitude = '';
		
		if( ! empty( $displayed_event->longitude ) || ! empty( $displayed_event->latitude ) )
			$changed = true;
		
		bpe_update_eventmeta( bpe_get_event_id( $displayed_event ), 'no_coords', 1  );
		bpe_delete_eventmeta( bpe_get_event_id( $displayed_event ), 'manual_coords' );
	}
	
	// maybe update the timezone
	if( bpe_get_option( 'geonames_username' ) )
	{
		if( $input['location'] != bpe_get_event_location( $displayed_event ) || ! bpe_get_event_timezone_raw( $displayed_event ) ) :
			$timezone = bpe_get_timezone( $latitude, $longitude );
			$changed = true;
		else :
			$timezone = bpe_get_event_timezone_raw( $displayed_event );
		endif;
	}
	else
		$timezone = bpe_get_event_timezone_raw( $displayed_event );
		
	if( isset( $input['no_coords'] ) )
	{
		$timezone = '';
		
		if( ! empty( $displayed_event->timezone ) )
			$changed = true;
	}	
		
	$changed = apply_filters( 'bpe_filter_changed_variable', $changed, $displayed_event, $input );
	
	// soomething has changed
	if( $changed == true )
	{
		// check if the user is a spammer
		$is_spam = bp_core_is_user_spammer( $displayed_event->user_id );
	
		if( ! bpe_save_event( bpe_get_event_id( $displayed_event ), $displayed_event->user_id, $group_id, $input['name'], $displayed_event->slug, $input['description'], $input['category'], $url, $input['location'], $input['venue_name'], $longitude, $latitude, $input['start_date'], $start_time, $input['end_date'], $end_time, $displayed_event->date_created, $public, $limit_members, $input['recurrent'], $is_spam, 1, $rsvp, $all_day, $timezone, $displayed_event->group_approved ) )
		{
			if( ! $api )
			{
				bpe_add_message( __( 'Your event could not be edited. Please try again!', 'events' ), 'error' );
				bp_core_redirect( bp_get_root_domain() . $input['_wp_http_referer'] );
			}
			else
				return bpe_api_message( 'Event not edited', 'failed' );
		}
	}

	// get the new data
	$result = bpe_get_events( array( 'ids' => bpe_get_event_id( $displayed_event ), 'future' => false, 'past' => false ) );
	$new_event = $result['events'][0];
	
	$slug = ( $input['end_date'] .' '. $end_time < bp_core_current_time() ) ? bpe_get_option( 'archive_slug' ) .'/' : bpe_get_option( 'active_slug' ) .'/';
	
	$link = bpe_get_event_link( $displayed_event );
	
	// handle the recurrence
	if( $input['recurrent'] != $displayed_event->recurrent )
	{
		// delete the recurrence if there is one
		if( ! empty( $displayed_event->recurrent ) )
			bpe_unschedule_event( $displayed_event->recurrent, bpe_get_event_id( $displayed_event ), $displayed_event->date_created );

		// then reschedule the event if applicable
		$intervals = bpe_get_config( 'recurrence_intervals' );
		
		if( in_array( $input['recurrent'], array_keys( $intervals ) ) )
			bpe_schedule_event( $input['recurrent'], bpe_get_event_id( $displayed_event ), $displayed_event->date_created );
	}
	
	do_action( 'bpe_edited_event_action', $new_event );
	
	// reschedule the email reminders
	if( $input['start_date'] != $displayed_event->start_date || $start_time != $displayed_event->start_time )
	{
		wp_unschedule_event( $displayed_event->meta->bpe_reminder_timestamp, 'bpe_create_email_event_reminder_action', array( bpe_get_event_id( $displayed_event ) ) );
		wp_schedule_single_event( bpe_get_reminder_timestamp( $new_event ), 'bpe_create_email_event_reminder_action', array( bpe_get_event_id( $displayed_event ) ) );
	}
	
	// schedule clearing the archive cache
	if( $input['end_date'] != $displayed_event->end_date || $end_time != $displayed_event->end_time )
	{
		wp_unschedule_event( $displayed_event->meta->bpe_scheduled_cache_timestamp, 'bpe_clear_events_archive_cache_action', array( $displayed_event->user_id, $displayed_event->group_id ) );
		wp_schedule_single_event( bpe_get_clear_cache_timestamp( $new_event ), 'bpe_clear_events_archive_cache_action', array( $displayed_event->user_id, $new_event->group_id ) );
	}
	
	// Only send messages for active events
	if( $notify )
	{
		if( $input['end_date'] .' '. $end_time > bp_core_current_time() )
		{
			$message = sprintf( __( "Hello,\n\nthe event %s has changed.\n\nPlease follow the link below to view the changes:\n%s", 'events' ), $displayed_event->name, bpe_get_event_link( $displayed_event ) );
	
			$counter = 1;
			
			$send_to = array_merge( (array)$displayed_event->attendee_ids, (array)$displayed_event->maybe_attendee_ids );
			
			foreach( (array)$send_to as $uid )
			{
				// notify existing users about changes
				messages_new_message(  array(
						'sender_id'  => $displayed_event->user_id,
						'recipients' => array( $uid ),
						'subject' 	 => __( 'Changes to an event', 'events' ),
						'content' 	 => $message
				) );
				
				$counter++;
	
				if( $counter % 50 == 0 )
					sleep( 10 );
			}
		}
	}
	
	if( ! $api )
	{
		bpe_add_message( __( 'Your event has been edited!', 'events' ) );
		bp_core_redirect( bp_get_root_domain() . $input['_wp_http_referer'] );
	}
	else
		return bpe_api_message( 'Event edited', 'success' );
}

/**
 * Process event creation
 * 
 * @package	Core
 * @since 	1.5
 */
function bpe_process_event_creation( $input, $api = false, $user = false )
{
	if( ! $user )
		$user = bp_loggedin_user_id();
	
	$location 		= trim( $input['location'], '.,;:' );
	$public 		= ( isset( $input['public'] ) ) ? 1 : 0;
	$rsvp 			= ( isset( $input['rsvp'] ) ) ? 1 : 0;
	$all_day 		= ( isset( $input['all_day'] ) ) ? 1 : 0;
	$group_id 		= ( isset( $input['group_id'] ) && ! empty( $input['group_id'] ) ) ? $input['group_id'] : 0;
	$new_event_id 	= ( ! empty( $input['new_event_id'] ) ) ? $input['new_event_id'] : null;
	$no_coords 		= ( isset( $input['no_coords'] ) ) ? $input['no_coords'] : false;
	$url 			= ( $input['url'] == 'http://' ) ? '' : $input['url'];
	
	if( ! $api )
	{
		$cookie =  array(
			'user_id'		=> $user,
			'name' 			=> $input['name'],
			'description' 	=> $input['description'],
			'location' 		=> $location,
			'venue_name' 	=> $input['venue_name'],
			'start_date' 	=> $input['start_date'],
			'start_time' 	=> $input['start_time'],
			'end_date' 		=> $input['end_date'],
			'end_time' 		=> $input['end_time'],
			'limit_members' => $input['limit_members'],
			'category' 		=> $input['category'],
			'public' 		=> $public,
			'group_id' 		=> $group_id,
			'recurrent' 	=> $input['recurrent'],
			'no_coords' 	=> $no_coords,
			'url' 			=> $url,
			'all_day' 		=> $all_day,
			'rsvp' 			=> $rsvp
		);
		
		// Let's not lose any data, so set a cookie with milk
		@setcookie( 'buddyvents_submission', serialize( $cookie ), time() + 86400, COOKIEPATH );
	}

	if( ! empty( $input['recurrent'] ) )
	{
		$ts1 = strtotime( $input['start_date']  );
		$ts2 = strtotime( $input['end_date'] 	);
		
		$length = bpe_count_days( $ts1, $ts2 );
		$days 	= bpe_get_recurrence_days( $input['recurrent'] );
		
		if( $length >= $days )
		{
			if( ! $api )
			{
				bpe_add_message( __( 'The event is too long for the chosen recurrence interval.', 'events' ), 'error' );
				bp_core_redirect( bp_get_root_domain() . $input['_wp_http_referer'] );
			}
			else
				return bpe_api_message( 'Event too long for chosen recurrence interval', 'failed' );
		}
	}
	
	// check for all required fields
	if( empty( $input['name'] ) || empty( $input['description'] ) || empty( $location ) || empty( $input['start_date'] ) || empty( $input['start_time'] ) && $all_day == 0 || empty( $input['end_date'] ) && $all_day == 0 || empty( $input['end_time'] ) && $all_day == 0 || empty( $input['category'] ) || bpe_get_option( 'restrict_creation' ) === true && empty( $input['group_id'] ) )
	{
		if( ! $api )
		{
			bpe_add_message( __( 'Please fill in all fields marked by *.', 'events' ), 'error' );
			bp_core_redirect( bp_get_root_domain() . $input['_wp_http_referer'] );
		}
		else
			return bpe_api_message( 'Required data missing', 'failed' );
	}
	
	if( ! empty( $input['limit_members'] ) )
	{
		if( ! is_numeric( $input['limit_members'] ) )
		{
			if( ! $api )
			{
				bpe_add_message( __( 'Please enter a number to limit the attendees.', 'events' ), 'error' );
				bp_core_redirect( bp_get_root_domain() . $input['_wp_http_referer'] );
			}
			else
				return bpe_api_message( 'Limit attendees not numeric', 'failed' );
		}
	}

	if( ! empty( $url ) )
	{
		if( ! bpe_is_url( $url ) )
		{
			if( ! $api )
			{
				bpe_add_message( __( 'Please enter a valid URL', 'events' ), 'error' );
				bp_core_redirect( bp_get_root_domain() . $input['_wp_http_referer'] );
			}
			else
				return bpe_api_message( 'Invalid URL', 'failed' );
		}
	}
	
	$start_time = ( bpe_get_option( 'clock_type' ) == 12 ) ? date( 'H:i:s', strtotime( $input['start_time'] ) ) : $input['start_time'] .':00';
	$end_time 	= ( bpe_get_option( 'clock_type' ) == 12 ) ? date( 'H:i:s', strtotime( $input['end_time'] 	) ) : $input['end_time'] .':00';

	if( $all_day == 1 )
	{
		if( empty( $input['end_date'] ) )
			$input['end_date'] = $input['start_date'];
			
		$start_time = '00:01:00';
		$end_time	= '23:59:00';
	}
	
	// check the date
	if( $input['start_date'] .' '. $start_time >= $input['end_date'] .' '. $end_time )
	{
		if( ! $api )
		{
			bpe_add_message( __( 'The end date needs to be after the start date.', 'events' ), 'error' );
			bp_core_redirect( bp_get_root_domain() . $input['_wp_http_referer'] );
		}
		else
			return bpe_api_message( 'Start date after end date', 'failed' );
	}
	
	// get the unique slug, while taking resaving the step into consideration
	if( empty( $input['new_event_id'] ) )
	{
		$slug = sanitize_title_with_dashes( $input['name'] );
		$slug = bpe_remove_accents( $slug );
		$slug = bpe_check_unique_slug( $slug );
	}
	else
		$slug = bpe_get_displayed_event( 'slug' );
	
	// can't have an event with an internal name
	if( in_array( $slug, bpe_get_config( 'forbidden_slugs' ) ) )
	{
		if( ! $api )
		{
			bpe_add_message( __( 'Please rename your event. Your chosen name is reserved.', 'events' ), 'error' );
			bp_core_redirect( bp_get_root_domain() . $input['_wp_http_referer'] );
		}
		else
			return bpe_api_message( 'Reserved event name', 'failed' );
	}
	
	// get the coordinates
	if( $no_coords )
	{
		$longitude = '';
		$latitude = '';
	}
	else
	{
		$coords 	= bpe_get_event_coordinates( $location );
		$longitude 	= $coords['lng'];
		$latitude 	= $coords['lat'];
	}
	
	do_action( 'bpe_pre_save_event' );
	
	// needed for a recurrent event
	$now = bp_core_current_time();
	
	// check if the user is a spammer
	$is_spam = bp_core_is_user_spammer( $user );
	
	// get the timezone
	$timezone = bpe_get_timezone( $latitude, $longitude );

	// check for user_status within a group
	$group_approved = 1;
	if( ! empty( $group_id ) && bp_is_active( 'groups' ) )
	{
		// only unapprove if user is not group admin and not group mod
		if( ! groups_is_user_admin( $user, $group_id ) )
			$group_approved = 0;
	}
	
	// set to approved only for api if option is turned on
	$approved = ( $api ) ? 0 : ( ( bpe_get_option( 'approve_events' ) === true ) ? 0 : 1 );
	
	if( ! $event_id = bpe_save_event( $new_event_id, $user, $group_id, $input['name'], $slug, $input['description'], $input['category'], $url, $location, $input['venue_name'], $longitude, $latitude, $input['start_date'], $start_time, $input['end_date'], $end_time, $now, $public, $input['limit_members'], $input['recurrent'], $is_spam, $approved, $rsvp, $all_day, $timezone, $group_approved ) )
	{
		if( ! $api )
		{
			bpe_add_message( __( 'Your event could not be published. Please try again!', 'events' ), 'error' );
			bp_core_redirect( bp_get_root_domain() . $input['_wp_http_referer'] );
		}
		else
			return bpe_api_message( 'Event not published', 'failed' );
	}
	else
	{
		// new event data
		$event = new Buddyvents_Events( $event_id );

		do_action( 'bpe_event_is_publishable', $event );

		// add eventmeta so events are not shown on approve screen while being created
		bpe_update_eventmeta( bpe_get_event_id( $event ), 'not_published_yet', 1 );

		// set the event status
		bpe_update_eventmeta( bpe_get_event_id( $event ), 'status', 'active' );
		
		// prevent the address lookup
		if( isset( $input['no_coords'] ) )
			bpe_update_eventmeta( bpe_get_event_id( $event ), 'no_coords', 1 );
		
		// add the event creator as the first attendee
		bpe_add_member( null, bpe_get_event_id( $event ), bpe_get_event_user_id( $event ), 1, $now, 'admin' );

		if( ! $api )
			return bpe_get_event_id( $event );
		else
			return bpe_api_message( 'Event created', 'success', bpe_get_event_id( $event ) );
	}
}

/**
 * Process some things only after publication
 * 
 * @package	Core
 * @since 	2.0
 */
function bpe_process_after_event_publication( $event )
{
	// send out notifications (email/screen) for public events
	if( bpe_get_event_public( $event ) == 1 )
	{
		$cat = new Buddyvents_Categories( bpe_get_event_category( $event ) );
		
		$searchable = apply_filters( 'bpe_searchable_text', bpe_get_event_name( $event ) .' '. bpe_get_event_description_raw( $event ) .' '. $cat->name .' '. bpe_get_event_location( $event ), $event, $cat );
		
		$uids = bpe_get_uids_for_keywords( $searchable, bpe_get_event_user_id( $event ) );
		
		bpe_send_email_notifications( $uids['email'],   $event );
		bpe_send_screen_notifications( $uids['screen'], $event );
	}
	
	// schedule the email reminders
	wp_schedule_single_event( bpe_get_reminder_timestamp( $event ), 'bpe_create_email_event_reminder_action', array( bpe_get_event_id( $event ) ) );
	
	// schedule clearing the archive cache
	wp_schedule_single_event( bpe_get_clear_cache_timestamp( $event ), 'bpe_clear_events_archive_cache_action', array( bpe_get_event_user_id( $event ), bpe_get_event_group_id( $event ) ) );
					
	// schedule the recurrence
	$intervals = bpe_get_config( 'recurrence_intervals' );

	if( in_array( bpe_get_event_recurrent( $event ), array_keys( $intervals ) ) )
		bpe_schedule_event( bpe_get_event_recurrent( $event ), bpe_get_event_id( $event ), bp_core_current_time() );
}
add_action( 'bpe_saved_new_event', 'bpe_process_after_event_publication' );

/**
 * Process manual attendees
 * 
 * @package	Core
 * @since 	1.7.10
 */
function bpe_process_manual_attendees( $input, $displayed_event )
{
	if( ! empty( $input['invitations'] ) || ! empty( $input['send_to_usernames'] ) )
	{
		$invitations = explode( ',', $input['invitations'] );
		$invites = explode( ' ', $input['send_to_usernames'] );
		
		$recipients = array_merge( (array)$invites, (array)$invitations );

		// Loop the recipients and convert all usernames to user_ids where needed
		foreach( (array) $recipients as $k => $recipient ) {
			if ( is_numeric( trim( $recipient ) ) )
				$recipient_ids[] = (int)trim( $recipient );
	
			if ( $recipient_id = bp_core_get_userid( trim( $recipient ) ) )
				$recipient_ids[] = (int)$recipient_id;
		}
	
		// Strip the sender from the recipient list if they exist 
		if( is_numeric( $key = array_search( $displayed_event->user_id, (array)$recipient_ids ) ) )
			unset( $recipient_ids[$key] );
	
		// Remove duplicates including the ones already sent
		$recipient_ids = array_unique( (array)$recipient_ids );
	
		// Remove any users that are a member already
		$current_members = bpe_get_attendee_ids( bpe_get_event_id( $displayed_event ) );
		$recipient_ids = array_diff( (array)$recipient_ids, (array)$current_members );

		$counter = 0;

		// send a message each to all invites to avoid large cc lists
		if( ! empty( $recipient_ids ) )
		{
			$subject = __( 'You have been added to an event', 'events' );
			
			$eventlink = bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. bpe_get_option( 'active_slug' ) .'/'. $displayed_event->slug .'/';
	
			foreach( $recipient_ids as $uid )
			{
				// Add them to an event
				bpe_add_member( null, bpe_get_event_id( $displayed_event ), $uid, 1, bp_core_current_time(), 'attendee' );
				
				// Send a message
				$message = sprintf( __( "Hello %s,\n\nyou have been added to %s:\n%s\n\nClick the link below if you will not attend this event:\n%s", 'events' ), bp_core_get_user_displayname( $uid ), $displayed_event->name, $eventlink, $eventlink .'not-attending/'. $uid .'/' );
				
				messages_new_message(  array(
					'sender_id'  => $displayed_event->user_id,
					'recipients' => array( $uid ),
					'subject' 	 => $subject,
					'content' 	 => $message
				) );
					
				$counter++;
	
				// help the server load and rest for 10 seconds every 25 messages
				if( $counter % 50 == 0 )
					sleep( 10 );
			}
		}

		bpe_add_message( sprintf( _n( '%d attendee has been added.', '%d attendees have been added.', (int)$counter, 'events' ), (int)$counter ) );
	}
}

/**
 * Process event invites
 * 
 * @package	Core
 * @since 	1.7
 */
function bpe_process_event_invitations( $input, $displayed_event )
{
	if( ! empty( $input['invitations'] ) || ! empty( $input['send_to_usernames'] ) || isset( $input['notify_group'] ) )
	{
		$group_members = array();
		
		if( isset( $input['notify_group'] ) && bpe_get_event_group_id( $displayed_event ) )
			$group_members = bpe_get_group_members( bpe_get_event_group_id( $displayed_event ) );
		
		$invitations = explode( ',', $input['invitations'] );
		$invites = explode( ' ', $input['send_to_usernames'] );
		
		$recipients = array_merge( (array)$invites, (array)$invitations, (array)$group_members );
	
		// Loop the recipients and convert all usernames to user_ids where needed
		foreach( (array) $recipients as $k => $recipient ) {
			if ( is_numeric( trim( $recipient ) ) )
				$recipient_ids[] = (int)trim( $recipient );
	
			if ( $recipient_id = bp_core_get_userid( trim( $recipient ) ) )
				$recipient_ids[] = (int)$recipient_id;
		}
	
		// Strip the sender from the recipient list if they exist 
		if( is_numeric( $key = array_search( bpe_get_event_user_id( $displayed_event ), (array)$recipient_ids ) ) )
			unset( $recipient_ids[$key] );
	
		// Remove duplicates including the ones already sent
		$recipient_ids = array_unique( (array)$recipient_ids );
		
		$invitations = ( isset( $displayed_event->meta->invitations ) ) ? $displayed_event->meta->invitations : array();
	
		// Remove any users that are invited already
		$recipient_ids = array_diff( (array)$recipient_ids, $invitations );
	
		// Update the invitations array
		bpe_update_eventmeta( bpe_get_event_id( $displayed_event ), 'invitations', array_merge( $invitations, (array)$recipient_ids ) );
	
		// send a message each to all invites to avoid large cc lists
		if( ! empty( $recipient_ids ) )
		{
			$subject = __( 'Event Invitation', 'events' );
			
			$eventlink = bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. bpe_get_option( 'active_slug' ) .'/'. bpe_get_event_slug( $displayed_event ) .'/';
	
			$counter = 1;
			
			foreach( $recipient_ids as $uid )
			{
				$message = sprintf( __( "Hello %s,\n\nyou have been invited to attend %s:\n%s\n\nPlease make a choice:\n\nI will attend:\n%s\n\nI might attend:\n%s\n\nI will not attend:\n%s", 'events' ), bp_core_get_user_displayname( $uid ), bpe_get_event_name( $displayed_event ), $eventlink, $eventlink .'attending/'. $uid .'/', $eventlink .'maybe/'. $uid .'/', $eventlink .'not-attending/'. $uid .'/' );

				messages_new_message(  array(
					'sender_id' 	=> bpe_get_event_user_id( $displayed_event ),
					'recipients' 	=> array( $uid ),
					'subject' 		=> $subject,
					'content' 		=> apply_filters( 'bpe_new_invite_message', $message, $displayed_event, $uid, $eventlink )
				) );
					
				$counter++;
	
				// help the server load and rest for 10 seconds every 25 messages
				if( $counter % 50 == 0 )
					sleep( 10 );
			}
		}
	}
}

/**
 * Process event deletion
 * 
 * @package	Core
 * @since 	1.7
 */
function bpe_process_event_deletion( $displayed_event, $redirect_url )
{
	$event = new Buddyvents_Events( bpe_get_event_id( $displayed_event ) );
	
	// only an admin can delete the event
	if( ! is_super_admin() )
	{
		if( bpe_is_admin( $displayed_event ) )
		{
			bpe_add_message( __( 'You are not allowed to delete this event.', 'events' ), 'error' );
			bp_core_redirect( $redirect_url );
		}
	}
	
	// delete the event
	if( $event->delete() )
	{
		$recipient_ids = bpe_get_attendee_ids( $displayed_event->id );

		if( ! empty( $recipient_ids ) )
		{
			$subject = __( 'Event Deletion', 'events' );
		
			$eventlink = bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. bpe_get_option( 'active_slug' ) .'/'. bpe_get_event_slug( $displayed_event ) .'/';
		
			$counter = 1;
				
			foreach( $recipient_ids as $uid )
			{
				$message = sprintf( __( "Hello %s,\n\nthe event %s that you registered for got deleted.\n\nThis is a system message, so please respond if you have any questions.\n\nEvent Link\n%s", 'events' ), bp_core_get_user_displayname( $uid ), bpe_get_event_name( $displayed_event ), $eventlink );
	
				messages_new_message(  array(
					'sender_id' 	=> bpe_get_event_user_id( $displayed_event ),
					'recipients' 	=> array( $uid ),
					'subject' 		=> $subject,
					'content' 		=> apply_filters( 'bpe_event_deletion_message', $message, $displayed_event, $uid, $eventlink )
				) );
						
				$counter++;
		
				// help the server load and rest for 10 seconds every 25 messages
				if( $counter % 50 == 0 )
					sleep( 10 );
			}
		}

		// delete event data
		do_action( 'bpe_delete_event_action', $event );
		
		bpe_add_message( sprintf( __( 'Your event %s has been deleted.', 'events' ), bpe_get_event_name( $event ) ) );
	}
	else
		bpe_add_message(sprintf(  __( 'Your event %s could not be deleted. Please try again.', 'events' ), bpe_get_event_name( $event ) ), 'error' );
}

/**
 * Process event cancellation
 * 
 * @package	Core
 * @since 	2.1
 */
function bpe_process_event_cancellation( $displayed_event, $redirect_url )
{
	// only an admin can delete the event
	if( ! is_super_admin() )
	{
		if( bpe_is_admin( $displayed_event ) )
		{
			bpe_add_message( __( 'You are not allowed to cancel this event.', 'events' ), 'error' );
			bp_core_redirect( $redirect_url );
		}
	}
	
	bpe_update_eventmeta( $displayed_event->id, 'status', 'cancelled' );

	$recipient_ids = bpe_get_attendee_ids( $displayed_event->id );
			
	if( ! empty( $recipient_ids ) )
	{
		$subject = __( 'Event Cancellation', 'events' );
	
		$eventlink = bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. bpe_get_option( 'active_slug' ) .'/'. bpe_get_event_slug( $displayed_event ) .'/';
	
		$counter = 1;
		
		foreach( $recipient_ids as $uid )
		{
			$message = sprintf( __( "Hello %s,\n\nthe event %s that you registered for got cancelled.\n\nThis is a system message, so please respond if you have any questions.\n\nEvent Link\n%s", 'events' ), bp_core_get_user_displayname( $uid ), bpe_get_event_name( $displayed_event ), $eventlink );

			messages_new_message(  array(
				'sender_id' 	=> bpe_get_event_user_id( $displayed_event ),
				'recipients' 	=> array( $uid ),
				'subject' 		=> $subject,
				'content' 		=> apply_filters( 'bpe_event_cancellation_message', $message, $displayed_event, $uid, $eventlink )
			) );
					
			$counter++;
	
			// help the server load and rest for 10 seconds every 25 messages
			if( $counter % 50 == 0 )
				sleep( 10 );
		}
	}
	
	// maybe close the forum
	if( bpe_are_forums_enabled() ) :
		$forum_id = bpe_get_eventmeta( $displayed_event->id,'forum_id' );
		
		if( ! empty( $forum_id ) ) :
			bbp_close_forum( $forum_id );
		endif;
	endif;

	do_action( 'bpe_cancel_event_action', $displayed_event );
		
	bpe_add_message( sprintf( __( 'Your event %s has been cancelled.', 'events' ), bpe_get_event_name( $event ) ) );
}
?>