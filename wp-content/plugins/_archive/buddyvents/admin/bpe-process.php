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
 * Process any $_POST variables
 * 
 * @package Admin
 * @since 	1.4
 */
function bpe_settings_processor()
{
	global $bpe, $wpdb, $bp;

	// import events settings
	if( isset( $_POST['import_settings'] ) )
	{
		check_admin_referer( 'bpe_settings' );
		
		$import = bpe_import_settings_file();
		
		if( $import['errors'] > 0 ) :
			bpe_admin_add_notice( $import['message'], 'error' );
			bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-settings' ), 'admin.php' ) ) );
		endif;
		
		bpe_admin_add_notice( __( 'Options have been updated successfully from a settings file.', 'events' ) );
		bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-settings' ), 'admin.php' ) ) );
	}
	
	// export events settings
	if( isset( $_GET['export_settings'] ) && $_GET['export_settings'] == 1 )
	{
		header( "Pragma: no-cache" );
		header( "Expires: 0" );
		header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
		header( "Robots: none" );
		header( "Content-Type: application/json;charset=". $wpdb->charset );
		header( "Content-Description: File Transfer" );
		header( "Content-Transfer-Encoding: binary" );
		header( "Content-Disposition: attachment; filename=\"bpe-settings.js\";" );
		
		// add a check option
		$bpe->options->bpe_export_file = true;
		
		// some options should not get exported
		unset( $bpe->options->img );
		unset( $bpe->options->logo );
		unset( $bpe->options->page_id );
		unset( $bpe->options->invoice_logo );
		
		echo bpe_export_to_json( $bpe->options );
		exit;
	}

	// delete the default avatar
	if( isset( $_POST['reset_options'] ) )
	{
		check_admin_referer( 'bpe_settings' );
		
		include_once( EVENT_ABSPATH .'admin/bpe-install.php' );
		bpe_default_options();

		bpe_admin_add_notice( __( 'Options have been reset successfully.', 'events' ) );
		bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-settings' ), 'admin.php' ) ) );
	}
	
	// create a test invoice and output to screen
	if( isset( $_POST['create_test_invoice'] ) )
	{
		// test data
		$sale1 				 = new stdClass();
		$sale1->id 			 = 1;
		$sale1->ticket_id 	 = 1;
		$sale1->seller_id 	 = 1;
		$sale1->buyer_id 	 = 1;
		$sale1->single_price = 25.00;
		$sale1->currency 	 = 'EUR';
		$sale1->quantity 	 = 1;
		$sale1->attendees 	 = ''; 
		$sale1->gateway 	 = 'paypal';
		$sale1->sales_id 	 = 0;
		$sale1->status 		 = 'completed';
		$sale1->sale_date 	 = '2011-06-20 21:58:09';
		$sale1->commission 	 = 5.50;
		$sale1->requested 	 = 1;

		$sale2 				 = new stdClass();
		$sale2->id 			 = 2;
		$sale2->ticket_id 	 = 1;
		$sale2->seller_id 	 = 1;
		$sale2->buyer_id 	 = 1;
		$sale2->single_price = 45.00;
		$sale2->currency 	 = 'EUR';
		$sale2->quantity 	 = 3;
		$sale2->attendees 	 = ''; 
		$sale2->gateway 	 = 'paypal';
		$sale2->sales_id 	 = 0;
		$sale2->status 		 = 'completed';
		$sale2->sale_date 	 = '2011-06-20 21:58:09';
		$sale2->commission 	 = 5.50;
		$sale2->requested 	 = 1;
		
		$invoice 			 = new stdClass();
		$invoice->id 		 = 1;
		$invoice->user_id 	 = 1;
		$invoice->sales 	 = serialize( array( 1, 2, 3, 4 ) );
		$invoice->month 	 = '1/1970';
		$invoice->settled 	 = 0;
		$invoice->datasets 	 = array( 0 => $sale1, 1 => $sale2 );
		
		$client['company'] 	 = 'Test Company';
		$client['street'] 	 = 'Test Street';
		$client['postcode']  = '123456';
		$client['city'] 	 = 'Test City';
		$client['country'] 	 = 'Test Country';

		$event 				 = new stdClass();
		$event->name 		 = 'Test Event';

		bpe_tickets_produce_invoice( $invoice, 'D', $client, $event );
	}
	
	// update all posts
	if( isset( $_POST['get_timezone'] ) )
	{
		check_admin_referer( 'bpe_settings' );
		
		$events = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$bpe->tables->events} WHERE timezone = ''" ) );
		
		foreach( (array)$events as $event )
		{
			$timezone = bpe_get_timezone( bpe_get_event_latitude( $event ), bpe_get_event_longitude( $event ) );
			$wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->events} SET timezone = %s WHERE id = %d", $timezone, bpe_get_event_id( $event ) ) );
		}

		bpe_admin_add_notice( __( 'Timezones have been successfully updated.', 'events' ) );
		bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-settings' ), 'admin.php' ) ) );
	}
	
	// delete the default avatar
	if( isset( $_POST['del_default_avatar'] ) )
	{
		check_admin_referer( 'bpe_settings' );
		
		bpe_delete_default_avatars( bpe_get_option( 'default_avatar' ) );
		
		$bpe->options->default_avatar = '';
		update_blog_option( Buddyvents::$root_blog, 'bpe_options', $bpe->options );

		bpe_admin_add_notice( __( 'The avatar has been successfully deleted.', 'events' ) );
		bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-settings' ), 'admin.php' ) ) );
	}

	// delete the default avatar
	if( isset( $_POST['del_invoice_logo'] ) )
	{
		check_admin_referer( 'bpe_settings' );
		
		bpe_delete_default_avatars( bpe_get_option( 'invoice_logo' ) );
		
		$bpe->options->invoice_logo = '';
		update_blog_option( Buddyvents::$root_blog, 'bpe_options', $bpe->options );

		bpe_admin_add_notice( __( 'The logo has been successfully deleted.', 'events' ) );
		bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-settings' ), 'admin.php' ) ) );
	}

	// update the options
	if( isset( $_POST['update_bpe_options'] ) )
	{	
		check_admin_referer( 'bpe_settings' );
		
		$error = false;

		// proceed if there is no error
		if( ! $error )
		{
			if( $_POST['page_options'] )	
				$options = explode( ',', stripslashes( $_POST['page_options'] ) );
				
			if( $options )
			{
				foreach( $options as $option )
				{
					$option = trim( $option );

					$valopt = ( isset( $_POST[$option] ) ) ? $_POST[$option] : '';

					if( is_array( $valopt ) )
						$value = $valopt;
					else
					{
						$value = trim( $valopt );

						if( in_array( $option, apply_filters( 'bpe_admin_boolean_settings', array(
							'enable_address', 'restrict_creation', 'enable_achievements', 'enable_newsletter',
							'approve_events', 'localize_months', 'enable_api', 'enable_twitter', 'enable_aweber',
							'enable_facebook', 'use_event_images', 'enable_eventbrite', 'enable_forums',
							'enable_webhooks', 'enable_logo', 'enable_attendees', 'enable_tickets', 'enable_groups',
							'enable_sandbox', 'enable_invites',	'enable_cubepoints', 'enable_invoices',
							'use_fullcalendar', 'enable_manual_attendees', 'group_contact_required',
							'disable_warnings', 'enable_facebook_pages', 'enable_cmonitor', 'enable_mailchimp',
							'enable_bp_gallery'
						) ) ) )
							$value = (bool)$value;
							

						elseif( in_array( $option, apply_filters( 'bpe_admin_array_settings', array(
							'enable_ical', 'enable_schedules', 'enable_documents', 'enable_directions'
						) ) ) )
							$value = absint( $value );
					}
					
					if( $option == 'allowed_currencies' )
					{
						if( count( (array)$value ) < 1 )
							$value = array( 'EUR' );
					}
					
					if( $option == 'commission_percent' || $option == 'invoice_tax' )
					{
						$value = str_replace( '%', '', $value );
						$value = (float) str_replace( ',', '.', $value );
						
						if( $value < 0 ) $value = 0;						
						if( $value > 100 ) $value = 100;
					}
					
					if( $option == 'tab_order' )
					{
						$tabs = explode( ',', $value );

						$value = array();
						$counter = 1;
						foreach( (array)$tabs as $tab )
						{
							$value[$tab] = $counter;
							$counter++; 
						}

						$default = key( $value );
						
						if( in_array( $default, array( 'attending', 'create', 'invoices' ) ) )
							$errors[] = __( "The default slug cannot be 'Attending', 'Invoices' or 'Create'", 'events' );
						else
						{						
							$bpe->options->backend_order = $value;
							$bpe->options->default_tab = $default;
						}
					}
					
					if( $option == 'img' && $_FILES['img']['error'] == 0 )
					{
						$avatar = bpe_upload_image();
						
						if( ! $avatar['errors'] )
						{
							// delete the old avatars
							bpe_delete_default_avatars( bpe_get_option( 'default_avatar' ) );
							
							// define the new avatar
							$bpe->options->default_avatar = maybe_unserialize( $avatar['url'] );
						}
						else
							$bpe->options->default_avatar = '';
					}
					
					if( $option == 'logo' && $_FILES['logo']['error'] == 0 )
					{
						$logo = bpe_upload_image( 'logo', false, false, false, false );
						
						if( ! $logo['errors'] )
						{
							// delete the old avatars
							bpe_delete_default_avatars( bpe_get_option( 'invoice_logo' ) );
							
							// define the new logo
							$bpe->options->invoice_logo = $logo['url'];
						}
						else
							$bpe->options->invoice_logo = '';
					}
					
					do_action( 'bpe_admin_sanitize_options', $option, $value );
					
					if( $option == 'slugs' )
					{
						if( count( array_unique( $value ) ) < count( $value ) )
							$errors[] = __( 'You cannot use the same slug more than once.', 'events' );

						else
						{
							foreach( $value as $key => $slug )
							{
								// do this early before the option gets reset
								if( $key == 'slug' )
								{
									// we need to adjust the $bp->pages object
									$directory_pages = bp_core_get_directory_page_ids();
									$page_id = $directory_pages[$bpe->options->slug];
									
									if( isset( $directory_pages[$bpe->options->slug] ) )
										unset( $directory_pages[$bpe->options->slug] );
									
									$directory_pages[$slug] = $page_id;
									
									bp_core_update_directory_page_ids( $directory_pages );
									
									// we also need to adjust the $bp->active_components object
									$components = bp_get_option( 'bp-active-components' );
									if( isset( $components[$bpe->options->slug] ) )
										unset( $components[$bpe->options->slug] );
									
									$components[$slug] = '1';
					
									bp_update_option( 'bp-active-components', $components );									
								}
								
								if( ! empty( $slug ) )
									$bpe->options->{$key} = sanitize_title_with_dashes( $slug );
							}
						}
					}
					else
						$bpe->options->{$option} = $value;
						
					// install the table rows for the achievements plugin
					if( $option == 'enable_achievements' )
					{
						if( $value === true )
						{
							if( ! $installed = $wpdb->get_col( $wpdb->prepare( "SELECT category FROM {$bp->achievements->table_actions} WHERE category = %s", bpe_get_base( 'id' ) ) ) )
							{
								$actions = array();
								$actions[] = array( 'category' => bpe_get_base( 'id' ), 'name' => 'bpe_saved_new_event', 'description' => __( 'The user publishes a new event.', 'events' ) );
								$actions[] = array( 'category' => bpe_get_base( 'id' ), 'name' => 'bpe_delete_event_action', 'description' => __( 'The user deletes an event.', 'events' ) );
								$actions[] = array( 'category' => bpe_get_base( 'id' ), 'name' => 'bpe_edited_event_action', 'description' => __( 'The user edits an event.', 'events' ) );
								$actions[] = array( 'category' => bpe_get_base( 'id' ), 'name' => 'bpe_maybe_attend_event', 'description' => __( 'The user might attend an event.', 'events' ) );
								$actions[] = array( 'category' => bpe_get_base( 'id' ), 'name' => 'bpe_not_attending_event', 'description' => __( 'The user does not attend an event.', 'events' ) );
								$actions[] = array( 'category' => bpe_get_base( 'id' ), 'name' => 'bpe_attend_event', 'description' => __( 'The user signs up for an event.', 'events' ) );
	
								foreach ( $actions as $action )
									$wpdb->insert( "{$bp->achievements->table_actions}", $action );
							}
						}
						else
						{
							if( isset( $bp->achievements->table_actions ) )
								$wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->achievements->table_actions} WHERE category = %s", bpe_get_base( 'id' ) ) );
						}
					}
				}
			
				// save the date format once
				$bpe->options->date_format = get_blog_option( Buddyvents::$root_blog, 'date_format' );
			}

			// disable invoices if tickets are disabled
			if( $bpe->options->enable_tickets === false )
				$bpe->options->enable_invoices = false;
			
			// Save options
			update_blog_option( Buddyvents::$root_blog, 'bpe_options', $bpe->options );
			
			if( isset( $avatar['errors'] ) )
				bpe_admin_add_notice( join( '<br />', (array)$avatar['errors'] ), 'error' );

			elseif( isset( $errors ) )
				bpe_admin_add_notice( join( '<br />', (array)$errors ), 'error' );

			else
				bpe_admin_add_notice( __( 'Update Successfully', 'events' ) );

			bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-settings' ), 'admin.php' ) ) );
		}
	}
}

/**
 * Process any $_POST variables
 * 
 * @package Admin
 * @since 	1.4
 */
function bpe_events_processor()
{
	global $bpe, $wpdb, $bp;

	if( isset( $_GET['event'] ) )
	{
		$event = bpe_get_events( array(
			'restrict' => false,
			'ids' 	   => $_GET['event'],
			'future'   => false
		) );
		
		$bpe->displayed_event = $event['events'][0];
	}
		
	// bulk edit (spam or delete)
	if( isset( $_POST['bulkedit-submit'] ) || isset( $_POST['bulkedit-submit2'] ) )
	{
		check_admin_referer( 'bpe_bulkedit' );
		
		$bulk = false;
		
		if( isset( $_POST['bulkoption'] ) && $_POST['bulkoption'] != '-1' )
			$bulk = $_POST['bulkoption'];
			
		elseif( isset( $_POST['bulkoption2'] ) && $_POST['bulkoption2'] != '-1' )
			$bulk = $_POST['bulkoption2'];
			
		if( ! $bulk )
		{
			bpe_admin_add_notice( __( 'There was an error bulk editing your events.', 'events' ), 'error' );
			bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER ), 'admin.php' ) ) );
		}
			
		$ids = $wpdb->escape( implode( ',', (array)$_POST['be'] ) );
		
		switch( $bulk )
		{
			case 'spam' :
				bpe_change_spam_by_ids( $ids, 1 );
				bpe_admin_add_notice( __( 'Selected events have been set as spam.', 'events' ) );
				break;

			case 'nospam' :
				bpe_change_spam_by_ids( $ids, 0 );
				bpe_admin_add_notice( __( 'Selected events have been set as ham.', 'events' ) );
				break;
				
			case 'del' :
				foreach( (array)$_POST['be'] as $event_id )
				{
					$event = new Buddyvents_Events( $event_id );
					
					// delete event data
					do_action( 'bpe_delete_event_action', $event );
					
					$event->delete();
				}
				
				bpe_admin_add_notice( __( 'Selected events have been deleted.', 'events' ) );
				break;

			default:
				if( function_exists( 'bpe_process_bulk_option_'. $bulk ) )
					call_user_func( 'bpe_process_bulk_option_'. $bulk, $ids );
				break;
		}
		bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER ), 'admin.php' ) ) );
	}
	
	// delete an event
	if( isset( $_GET['page'] ) && isset( $_GET['step'] ) )
	{
		if( $_GET['page'] == EVENT_FOLDER && $_GET['step'] == 'delete'  )
		{
			check_admin_referer( 'bpe_delete_event_now' );
			
			$event = ( isset( $_GET['event'] ) ) ? $_GET['event'] : '';
			$paged = ( isset( $_GET['paged'] ) ) ? $_GET['paged'] : '';
	
			bpe_process_event_deletion( bpe_get_displayed_event(), admin_url( add_query_arg( array( 'page' => EVENT_FOLDER, 'paged' => $paged, 'event' => $event, 'step' => 'delete' ), 'admin.php' ) ) );	
			bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER ), 'admin.php' ) ) );
		}
	}
	
	// process attendee management
	if( isset( $_GET['page'] ) && $_GET['page'] == EVENT_FOLDER && isset( $_GET['step'] ) &&  $_GET['step'] == bpe_get_option( 'manage_slug' ) && isset( $_GET['user_id'] ) && is_numeric( $_GET['user_id'] ) )
	{
		if( $_GET['action'] == 'promote-admin' )
		{
			check_admin_referer( 'bpe_promote_admin' );

			if( ! is_super_admin() )
			{
				if( ! in_array( bp_loggedin_user_id(), (array) bpe_get_displayed_event( 'admin_ids' ) ) )
				{
					bpe_admin_add_notice( __( 'Only an event admin can change a member role.', 'events' ), 'error' );
					bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER, 'paged' => $_GET['paged'], 'event' => $_GET['event'], 'step' => bpe_get_option( 'manage_slug' ) ), 'admin.php' ) ) );
				}
			}

			bpe_set_event_member_role( $_GET['user_id'], bpe_get_displayed_event( 'id' ), 'admin' );

			do_action( 'bpe_promote_user_to_admin', bpe_get_displayed_event() );

			bpe_admin_add_notice( __( 'The user has been promoted to admin', 'events' ) );
			bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER, 'paged' => $_GET['paged'], 'event' => $_GET['event'], 'step' => bpe_get_option( 'manage_slug' ) ), 'admin.php' ) ) );
		}
	
		if( $_GET['action'] == 'promote-organizer' )
		{
			check_admin_referer( 'bpe_promote_organizer' );

			if( ! is_super_admin() )
			{
				if( ! in_array( bp_loggedin_user_id(), (array) bpe_get_displayed_event( 'admin_ids' ) ) )
				{
					bpe_admin_add_notice( __( 'Only an event admin can change a member role.', 'events' ), 'error' );
					bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER, 'paged' => $_GET['paged'], 'event' => $_GET['event'], 'step' => bpe_get_option( 'manage_slug' ) ), 'admin.php' ) ) );
				}
			}
			
			bpe_set_event_member_role( $_GET['user_id'], bpe_get_displayed_event( 'id' ), 'organizer' );

			do_action( 'bpe_promote_user_to_organizer', bpe_get_displayed_event() );

			bpe_admin_add_notice( __( 'The user has been promoted to organizer.', 'events' ) );
			bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER, 'paged' => $_GET['paged'], 'event' => $_GET['event'], 'step' => bpe_get_option( 'manage_slug' ) ), 'admin.php' ) ) );
		}
	
		if( $_GET['action'] == 'demote-organizer' )
		{
			check_admin_referer( 'bpe_demote_organizer' );

			if( ! is_super_admin() )
			{
				if( ! in_array( bp_loggedin_user_id(), (array) bpe_get_displayed_event( 'admin_ids' ) ) )
				{
					bpe_admin_add_notice( __( 'Only an event admin can change a member role.', 'events' ), 'error' );
					bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER, 'paged' => $_GET['paged'], 'event' => $_GET['event'], 'step' => bpe_get_option( 'manage_slug' ) ), 'admin.php' ) ) );
				}
			}

			if( bp_is_active( 'groups' ) && bpe_get_displayed_event( 'group_id' ) )
			{
				if( groups_is_user_admin( bp_action_variable( 4 ), bpe_get_displayed_event( 'group_id' ) ) )
				{
					bpe_admin_add_notice( __( 'The group admin cannot be removed from this event.', 'events' ), 'error' );
					bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER, 'paged' => $_GET['paged'], 'event' => $_GET['event'], 'step' => bpe_get_option( 'manage_slug' ) ), 'admin.php' ) ) );
				}
			}
			
			bpe_set_event_member_role( $_GET['user_id'], bpe_get_displayed_event( 'id' ), 'organizer' );

			do_action( 'bpe_demote_user_to_organizer', bpe_get_displayed_event() );

			bpe_admin_add_notice( __( 'The user has been demoted to organizer.', 'events' ) );
			bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER, 'paged' => $_GET['paged'], 'event' => $_GET['event'], 'step' => bpe_get_option( 'manage_slug' ) ), 'admin.php' ) ) );
		}
	
		if( $_GET['action'] == 'demote-attendee' )
		{
			check_admin_referer( 'bpe_demote_attendee' );

			if( ! is_super_admin() )
			{
				if( ! in_array( bp_loggedin_user_id(), (array) bpe_get_displayed_event( 'admin_ids' ) ) )
				{
					bpe_admin_add_notice( __( 'Only an event admin can change a member role.', 'events' ), 'error' );
					bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER, 'paged' => $_GET['paged'], 'event' => $_GET['event'], 'step' => bpe_get_option( 'manage_slug' ) ), 'admin.php' ) ) );
				}
			}

			if( bp_is_active( 'groups' ) && bpe_get_displayed_event( 'group_id' ) )
			{
				if( groups_is_user_admin( bp_action_variable( 4 ), bpe_get_displayed_event( 'group_id' ) ) )
				{
					bpe_admin_add_notice( __( 'The group admin cannot be removed from this event.', 'events' ), 'error' );
					bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER, 'paged' => $_GET['paged'], 'event' => $_GET['event'], 'step' => bpe_get_option( 'manage_slug' ) ), 'admin.php' ) ) );
				}
			}
			
			bpe_set_event_member_role( $_GET['user_id'], bpe_get_displayed_event( 'id' ), 'attendee' );

			do_action( 'bpe_demote_user_to_attendee', bpe_get_displayed_event() );

			bpe_admin_add_notice( __( 'The user has been demoted to attendee.', 'events' ) );
			bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER, 'paged' => $_GET['paged'], 'event' => $_GET['event'], 'step' => bpe_get_option( 'manage_slug' ) ), 'admin.php' ) ) );
		}
	
		if( $_GET['action'] == 'remove' )
		{
			check_admin_referer( 'bpe_remove_attendee' );

			if( $_GET['user_id'] == bpe_get_displayed_event( 'user_id' ) )
			{
				bpe_admin_add_notice( __( 'You cannot remove yourself from the event.', 'events' ), 'error' );
				bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER, 'paged' => $_GET['paged'], 'event' => $_GET['event'], 'step' => bpe_get_option( 'manage_slug' ) ), 'admin.php' ) ) );
			}
			
			if( ! is_super_admin() )
			{
				if( ! in_array( bp_loggedin_user_id(), (array)bpe_get_displayed_event( 'admin_ids' ) ) )
				{
					bpe_admin_add_notice( __( 'Only an event admin can remove attendees.', 'events' ), 'error' );
					bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER, 'paged' => $_GET['paged'], 'event' => $_GET['event'], 'step' => bpe_get_option( 'manage_slug' ) ), 'admin.php' ) ) );
				}
			}
			
			bpe_remove_user_from_event( $_GET['user_id'], bpe_get_displayed_event( 'id' ) );
			
			do_action( 'bpe_removed_user_from_event', bpe_get_displayed_event() );

			bpe_admin_add_notice( __( 'The user has been removed from the event.', 'events' ) );
			bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER, 'paged' => $_GET['paged'], 'event' => $_GET['event'], 'step' => bpe_get_option( 'manage_slug' ) ), 'admin.php' ) ) );
		}
	}
}

/**
 * Process any invoices
 * 
 * @package Admin
 * @since 	2.0
 */
function bpe_invoice_processor()
{
	global $wpdb;
	
	if( ! bpe_get_option( 'enable_tickets' ) && ! bpe_get_option( 'enable_invoices' ) )
		return false;
	
	// bulk edit (send or delete)
	if( isset( $_POST['ibulkedit-submit'] ) || isset( $_POST['ibulkedit-submit2'] ) )
	{
		check_admin_referer( 'bpe_invoice_table' );
		
		if( isset( $_POST['ibulkoption'] ) && $_POST['ibulkoption'] != '-1' )
			$bulk = $_POST['ibulkoption'];
			
		elseif( isset( $_POST['ibulkoption2'] ) && $_POST['ibulkoption2'] != '-1' )
			$bulk = $_POST['ibulkoption2'];

		if( ! $bulk )
		{
			bpe_admin_add_notice( __( 'There was an error bulk editing your invoices.', 'events' ), 'error' );
			bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-învoices' ), 'admin.php' ) ) );
		}
		
		$ids = $wpdb->escape( implode( ',', (array)$_POST['be'] ) );
			
		switch( $bulk )
		{
			case 'send' :
				$datasets = bpe_get_invoices( array( 'ids' => $ids ) );
				
				$count = 0;
				foreach( (array)$datasets as $invoice )
				{
					$pdf = bpe_tickets_produce_invoice( $invoice, 'F' );
					
					$email_body = sprintf( __( "Hello %s,\n\nplease find attached the invoice for your ticket sales commission for %s\n\nYour %s Team", 'events' ), bp_core_get_user_displayname( $invoice->user_id ), $invoice->month, get_bloginfo( 'name' ) );
					wp_mail( bp_core_get_user_email( $invoice->user_id ), sprintf( __( '[%s] Invoice for %s', 'events' ), get_bloginfo( 'name' ), $invoice->month ), $email_body, '', array( $pdf ) );
					
					// remove the pdf
					if( file_exists( $pdf ) ) @unlink( $pdf );
					
					// upgrade date
					bpe_ticket_update_date( date( 'Y-m-d H:i:s' ), $invoice->id );
					
					$count++;
				}
				
				bpe_admin_add_notice( sprintf( _n( '%d invoice has been sent.', '%d invoices have been sent.', $count, 'events' ), $count ) );
				break;
				
			case 'del' :
				bpe_tickets_delete_invoices( $ids );
				bpe_admin_add_notice( __( 'Invoices have been deleted.', 'events' ) );
				break;

			case 'paid' :
				bpe_invoice_change_settled( 1, $ids );
				bpe_admin_add_notice( __( 'Invoices have been set to paid.', 'events' ) );
				break;

			case 'not-paid' :
				bpe_invoice_change_settled( 0, $ids );
				bpe_admin_add_notice( __( 'Invoices have been set to not paid.', 'events' ) );
				break;
		}

		bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-învoices' ), 'admin.php' ) ) );
	}

	if( isset( $_GET['action'] ) && isset( $_GET['invoice'] ) )
	{
		// send an invoice
		if( $_GET['action'] == 'send' )
		{
			check_admin_referer( 'bpe_sendmail_invoices' );
			
			$data = bpe_get_invoices( array( 'ids' => array( (int)$_GET['invoice'] ) ) );
			$invoice = $data['invoices'][0];
			
			$pdf = bpe_tickets_produce_invoice( $invoice, 'F' );
			
			$email_body = sprintf( __( "Hello %s,\n\nplease find attached the invoice for your ticket sales commission for %s\n\nYour %s Team", 'events' ), bp_core_get_user_displayname( $invoice->user_id ), $invoice->month, get_bloginfo( 'name' ) );
			wp_mail( bp_core_get_user_email( $invoice->user_id ), sprintf( __( '[%s] Invoice for %s', 'events' ), get_bloginfo( 'name' ), $invoice->month ), $email_body, '', array( $pdf ) );
			
			// remove the pdf
			if( file_exists( $pdf ) ) @unlink( $pdf );
			
			// upgrade date
			bpe_ticket_update_date( date( 'Y-m-d H:i:s' ), (int)$_GET['invoice'] );

			bpe_admin_add_notice( __( 'The invoice has been sent.', 'events' ) );
			bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-învoices' ), 'admin.php' ) ) );
		}
		
		// delete an invoice
		if( $_GET['action'] == 'delete' )
		{
			check_admin_referer( 'bpe_trash_invoices' );
			
			$invoice = new Buddyvents_Invoices( (int)$_GET['invoice'] );
			$invoice->delete();

			bpe_admin_add_notice( __( 'The invoice has been deleted.', 'events' ) );
			bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-învoices' ), 'admin.php' ) ) );
		}
		
		// preview an invoice
		if( $_GET['action'] == 'preview' )
		{
			check_admin_referer( 'bpe_pdfpreview_invoices' );
			
			$data = bpe_get_invoices( array( 'ids' => array( (int)$_GET['invoice'] ) ) );
			bpe_tickets_produce_invoice( $data['invoices'][0], 'D' );
		}

		// preview an invoice
		if( $_GET['action'] == 'stat' )
		{
			check_admin_referer( 'bpe_changestat_invoices' );
			$invoice = new Buddyvents_Invoices( (int)$_GET['invoice'] );
			
			$settled = ( $invoice->settled == 0 ) ? 1 : 0;
			bpe_invoice_change_settled( $settled, array( (int)$_GET['invoice'] ) );
		}
	}
}

/**
 * Process any sales invoices
 * 
 * @package Admin
 * @since 	2.0
 */
function bpe_sales_processor()
{
	if( ! bpe_get_option( 'enable_tickets' ) )
		return false;

	if( isset( $_POST['send-invoices'] ) )
	{
		check_admin_referer( 'bpe_sales_table' );

		$month = (int)$_POST['month'];
		$year = (int)$_POST['year'];
		$user_id = (int)$_POST['user_id'];

		if( ! $created = bpe_tickets_create_invoices( $_POST['sale_ids'], zeroise( $month, 2 ) .'/'. $year ) )
		{
			bpe_admin_add_notice( __( 'No invoices were created.', 'events' ), 'error' );
			bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-sales' ), 'admin.php' ) ) );
		}
		
		$for = $month .'/'. $year;
		if( ! empty( $user_id ) )
			$for = bp_core_get_user_displayname( $user_id ) .' ('. $for .')';

		bpe_admin_add_notice( sprintf( _n( 'Invoice for %s has been created. You can review it here and then send it off.', 'Invoices for %s have been created. You can review them here and then send them off.', $user_id, 'events' ), $for ) );
		bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-invoices' ), 'admin.php' ) ) );
	}
}

/**
 * Process any $_GET variables
 * 
 * @package Admin
 * @since 	1.5
 */
function bpe_api_processor()
{
	if( ! bpe_get_option( 'enable_api' ) )
		return false;

	if( isset( $_POST['bulkapi-submit'] ) || isset( $_POST['bulkapi-submit2'] ) )
	{
		check_admin_referer( 'bpe_bulk_api_keys' );
		
		$bulk = false;
		
		if( isset( $_POST['bulkoption'] ) && $_POST['bulkoption'] != '-1' )
			$bulk = $_POST['bulkoption'];
			
		elseif( isset( $_POST['bulkoption2'] ) && $_POST['bulkoption2'] != '-1' )
			$bulk = $_POST['bulkoption2'];
			
		if( ! $bulk )
		{
			bpe_admin_add_notice( __( 'There was an error bulk editing api access rights.', 'events' ), 'error' );
			bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-apikeys' ), 'admin.php' ) ) );
		}
		
		switch( $bulk )
		{
			case 'revoke' :
				bpe_set_api_access( $_POST['be'], 0 );
				bpe_admin_add_notice( __( 'API access has been revoked for the selected keys.', 'events' ) );
				break;

			case 'grant' :
				bpe_set_api_access( $_POST['be'], 1 );
				bpe_admin_add_notice( __( 'API access has been granted for the selected keys.', 'events' ) );
				break;

			case 'del' :
				foreach( (array)$_POST['be'] as $api_id )
				{
					$ev = new Buddyvents_API( (int)$api_id );
					$ev->delete();					
				}
				
				bpe_admin_add_notice( __( 'Selected API keys have been deleted.', 'events' ) );
				break;		}
		
		bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-apikeys' ), 'admin.php' ) ) );
	}

	if( isset( $_GET['action']) && $_GET['action'] == 'revoke' )
	{
		check_admin_referer( 'bpe_revoke_api' );
		
		bpe_set_api_access( $_GET['id'], 0 );
		
		bpe_admin_add_notice( __( 'The API access has been revoked for this key.', 'events' ) );
		bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-apikeys' ), 'admin.php' ) ) );
	}

	if( isset( $_GET['action']) && $_GET['action'] == 'grant' )
	{
		check_admin_referer( 'bpe_grant_api' );

		bpe_set_api_access( $_GET['id'], 1 );
		
		bpe_admin_add_notice( __( 'The API access has been granted for this key.', 'events' ) );
		bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-apikeys' ), 'admin.php' ) ) );
	}
}

/**
 * Process any $_GET/$_POST variables
 * 
 * @package Admin
 * @since 	2.0
 */
function bpe_webhook_processor()
{
	global $wpdb;

	if( ! bpe_get_option( 'enable_webhooks' ) )
		return false;

	if( isset( $_POST['bulkhook-submit'] ) || isset( $_POST['bulkhook-submit2'] ) )
	{
		check_admin_referer( 'bpe_bulk_webhooks' );
		
		$bulk = false;

		if( isset( $_POST['bulkhook'] ) && $_POST['bulkhook'] != '-1' )
			$bulk = $_POST['bulkhook'];
			
		elseif( isset( $_POST['bulkhook2'] ) && $_POST['bulkhook2'] != '-1' )
			$bulk = $_POST['bulkhook2'];

		if( ! $bulk )
		{
			bpe_admin_add_notice( __( 'There was an error bulk editing webhooks.', 'events' ), 'error' );
			bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-webhooks' ), 'admin.php' ) ) );
		}
		
		$ids = $wpdb->escape( implode( ',', (array)$_POST['be'] ) );
		
		switch( $bulk )
		{
			case 'verify' :
				bpe_bulk_verify_webhooks( $ids );
				bpe_admin_add_notice( __( 'Selected webhooks have been verified.', 'events' ) );
				break;

			case 'unverify' :
				bpe_bulk_unverify_webhooks( $ids );
				bpe_admin_add_notice( __( 'Selected webhooks have been unverified.', 'events' ) );
				break;

			case 'delete' :
				bpe_bulk_delete_webhooks( $ids );
				bpe_admin_add_notice( __( 'Selected webhooks have been deleted.', 'events' ) );
				break;
		}
		
		bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-webhooks' ), 'admin.php' ) ) );
	}
}

/**
 * Process any $_GET/$_POST variables
 * 
 * @package Admin
 * @since 	1.4
 */
function bpe_approve_processor()
{
	global $wpdb;
	
	$proceed = false;

	if( bpe_get_option( 'approve_events' ) === true )
		$proceed = true;
	
	if( bpe_get_option( 'enable_api' ) === true )
		$proceed = true;
	
	if( ! $proceed )
		return false;

	if( isset( $_POST['bulkapprove'] ) || isset( $_POST['bulkapprove2'] ) )
	{
		check_admin_referer( 'bpe_approve_event_actions' );

		$bulk = false;
		
		if( isset( $_POST['bulkapprove'] ) && $_POST['bulkapprove'] != '-1' )
			$bulk = $_POST['bulkapprove'];
			
		elseif( isset( $_POST['bulkapprove2'] ) && $_POST['bulkapprove2'] != '-1' )
			$bulk = $_POST['bulkapprove2'];
			
		if( ! $bulk )
		{
			bpe_admin_add_notice( __( 'There was an error bulk approving events.', 'events' ), 'error' );
			bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-approve' ), 'admin.php' ) ) );
		}
		
		$ids = $wpdb->escape( implode( ',', (array)$_POST['be'] ) );
		
		switch( $bulk )
		{
			case 'approve' :
				foreach( (array)$_POST['be'] as $event_id )
				{
					$ev = new Buddyvents_Events( (int)$event_id );
					
					if( function_exists( 'bpe_add_new_event_activity' ) )
						bpe_add_new_event_activity( $ev );
					
					if( function_exists( 'bpe_twitter_process_send' ) )
						bpe_twitter_process_send( $ev );
						
					if( function_exists( 'bpe_facebook_send_update' ) )
						bpe_facebook_send_update( $ev );
			
					if( function_exists( 'bpe_eventbrite_process_send' ) )
						bpe_eventbrite_process_send( $ev );
					
					bpe_approve_event( bpe_get_event_id( $ev ) );
					bpe_process_after_event_publication( $ev );

					if( bpe_get_event_group_approved( $ev ) == 1 )
						bpe_send_approval_status_mail( $ev, 'approved' );
				}
				
				bpe_admin_add_notice( __( 'Selected events have been approved.', 'events' ) );
				break;

			case 'del' :
				foreach( (array)$_POST['be'] as $event_id )
				{
					$ev = new Buddyvents_Events( (int)$event_id );
					
					// delete event data
					do_action( 'bpe_delete_event_action', $event );
					
					bpe_send_approval_status_mail( $ev, 'deleted' );
				}
				
				bpe_delete_by_ids( $ids );

				bpe_admin_add_notice( __( 'Selected events have been deleted.', 'events' ) );
				break;
		}
		
		bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-approve' ), 'admin.php' ) ) );
	}
	
	if( isset( $_GET['approved'] ) && $_GET['approved'] == 'true' )
	{
		check_admin_referer( 'bpe_approve_event' );

		$ev = new Buddyvents_Events( (int)$_GET['eid'] );

		if( function_exists( 'bpe_add_new_event_activity' ) )
			bpe_add_new_event_activity( $ev );
		
		if( function_exists( 'bpe_twitter_process_send' ) )
			bpe_twitter_process_send( $ev );
			
		if( function_exists( 'bpe_facebook_send_update' ) )
			bpe_facebook_send_update( $ev );

		if( function_exists( 'bpe_eventbrite_process_send' ) )
			bpe_eventbrite_process_send( $ev );
		
		bpe_approve_event( $ev->id );
		bpe_process_after_event_publication( $ev );

		if( bpe_get_event_group_approved( $ev ) == 1 )
			bpe_send_approval_status_mail( $ev, 'approved' );
		
		do_action( 'bpe_approved_created_event', $ev );
		
		bpe_admin_add_notice( __( 'The event has been approved.', 'events' ) );
		bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-approve' ), 'admin.php' ) ) );
	}
	
	if( isset( $_GET['approved'] ) && $_GET['approved'] == 'false' )
	{
		check_admin_referer( 'bpe_delete_event' );

		$ev = new Buddyvents_Events( (int)$_GET['eid'] );

		// delete event data
		do_action( 'bpe_delete_event_action', $ev );

		bpe_send_approval_status_mail( $ev, 'deleted' );
		
		$ev->delete();
				
		bpe_admin_add_notice( __( 'The event has been deleted.', 'events' ) );
		bp_core_redirect( admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-approve' ), 'admin.php' ) ) );
	}
}

/**
 * Process a special search request
 * 
 * I wonder when the first person actually finds out...
 * :)
 * 
 * @package Admin
 * @since 	2.0
 */
function bpe_process_search_ee()
{
	global $wpdb;
	
	if( isset( $_GET['page'] ) && $_GET['page'] == EVENT_FOLDER && isset( $_GET['s'] ) && $_GET['s'] == 'del buddyvents' )
	{
		echo '<!DOCTYPE HTML><html><head><title>Buddyvents Command Line</title><style>body{font: 13px Monaco, Consolas, "Andale Mono", "DejaVu Sans Mono", monospace;color: #fff;background: #000;}a{color:#fff;font-weight:bold;text-decoration:none;}a:hover{text-decoration:underline;}</style>';
		echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>';
		echo "<script>var adminUrl = '". admin_url( '/admin.php?page='. EVENT_FOLDER ) ."';var dbPrefix = '". $wpdb->prefix ."';</script>";
		echo '<script src="'. EVENT_URLPATH .'admin/js/search.js"></script></head><body><div id="prompt"><span id="captions"></span><span class="cursor">|</span></div></body></html>';
		exit;
	}
}
add_action( 'admin_init', 'bpe_process_search_ee', 0 );
?>