<?php

/**
 * bp_group_documents_screen_notification_settings()
 *
 * Adds notification settings for the component, so that a user can turn off email
 * notifications set on specific component actions.  These will be added to the 
 * bottom of the existing "Group" settings
 */
function bp_group_documents_screen_notification_settings() { 
	global $current_user; ?>
	
		<tr>
			<td></td>
			<td><?php _e( 'A member uploads a document to a group you belong to', 'bp-group-documents' ) ?></td>
			<td class="yes"><input type="radio" name="notifications[notification_group_documents_upload_member]" value="yes" <?php if ( !get_user_meta( $current_user->id,'notification_group_documents_upload_member') || 'yes' == get_user_meta( $current_user->id,'notification_group_documents_upload_member') ) { ?>checked="checked" <?php } ?>/></td>
			<td class="no"><input type="radio" name="notifications[notification_group_documents_upload_member]" value="no" <?php if ( get_user_meta( $current_user->id,'notification_group_documents_upload_member') == 'no' ) { ?>checked="checked" <?php } ?>/></td>
		</tr>
		<tr>
			<td></td>
			<td><?php _e( 'A member uploads a document to a group for which you are an moderator/admin', 'bp-group-documents' ) ?></td>
			<td class="yes"><input type="radio" name="notifications[notification_group_documents_upload_mod]" value="yes" <?php if ( !get_user_meta( $current_user->id,'notification_group_documents_upload_mod') || 'yes' == get_user_meta( $current_user->id,'notification_group_documents_upload_mod') ) { ?>checked="checked" <?php } ?>/></td>
			<td class="no"><input type="radio" name="notifications[notification_group_documents_upload_mod]" value="no" <?php if ( 'no' == get_user_meta( $current_user->id,'notification_group_documents_upload_mod') ) { ?>checked="checked" <?php } ?>/></td>
		</tr>
		
		<?php do_action( 'bp_group_documents_notification_settings' ); ?>
<?php	
}
add_action( 'groups_screen_notification_settings', 'bp_group_documents_screen_notification_settings' );


/**
 * bp_group_documents_email_notificiation()
 *
 * This function will send email notifications to users on successful document upload.
 * For each group memeber, it will check to see the users notification settings first, 
 * if the user has the notifications turned on, they will be sent a formatted email notification. 
 */
function bp_group_documents_email_notification( $document ) {
	global $bp;

	$user_name = bp_core_get_userlink($bp->loggedin_user->id,true);
	$user_profile_link = bp_core_get_userlink($bp->loggedin_user->id,false,true);
	$group_name = $bp->groups->current_group->name;
	$group_link = bp_get_group_permalink( $bp->groups->current_group );
	$document_name = $document->name; 
	$document_link = $document->get_url();


	$subject = '[' . get_blog_option( 1, 'blogname' ) . '] ' . sprintf( __( 'A document was uploaded to %s', 'bp-group-documents' ), $bp->groups->current_group->name );

	//these will be all the emails getting the update
	//'user_id' => 'user_email
	$emails = array();

	//first get the admin & moderator emails
	if( count( $bp->groups->current_group->admins ) ) {
		foreach( $bp->groups->current_group->admins as $user ) {
			if( 'no' == get_user_meta( $user->user_id, 'notification_group_documents_upload_mod' ) ) continue;
			$emails[$user->user_id] = $user->user_email;
		}
	}
	if( count( $bp->groups->current_group->mods ) ) {
		foreach( $bp->groups->current_group->mods as $user ) {
			if( 'no' == get_user_meta( $user->user_id, 'notification_group_documents_upload_mod' ) ) continue;
			if( !in_array( $user->user_email, $emails ) ) {
				$emails[$user->user_id] = $user->user_email;
			}
		}
	}

	//now get all member emails, checking to make sure not to send any emails twice
	$user_ids = BP_Groups_Member::get_group_member_ids( $bp->groups->current_group->id );
	foreach ( (array)$user_ids as $user_id ) {
		if ( 'no' == get_user_meta( $user_id, 'notification_group_documents_upload_member' ) ) continue;

		$ud = bp_core_get_core_userdata( $user_id );
		if( !in_array( $ud->user_email, $emails ) ) {
			$emails[$user_id] = $ud->user_email;
		}
	}

	foreach( $emails as $current_id => $current_email ) {
		$message = sprintf( __(
'%s uploaded a new file: %s to the group: %s.

To see %s\'s profile: %s

To see the group %s\'s homepage: %s

To download the new document directly: %s

------------------------
', 'bp-group-documents'), $user_name, $document_name, $group_name, $user_name, $user_profile_link, $group_name, $group_link, $document_link );


		$settings_link = bp_core_get_user_domain( $current_id ) . $bp->settings->slug . '/notifications/';
		$message .= sprintf( __( 'To disable these notifications please log in and go to: %s', 'bp-group-documents' ), $settings_link );

		// Set up and send the message
		$to = $current_email;

		wp_mail( $to, $subject, $message );
		unset( $to, $message);
	} //end foreach
}
add_action('bp_group_documents_add_success','bp_group_documents_email_notification',10);

