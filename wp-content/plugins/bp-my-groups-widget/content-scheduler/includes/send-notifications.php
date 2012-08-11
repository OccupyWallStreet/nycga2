<?php
			$options = get_option('ContentScheduler_Options');
			// $why_notify should be:
			// a. 'expired' -- came from process_expiration
			// b. 'notified' -- came from process_notification
			// We can use this info to customize the message.
			// We're going to change the value of that same variable and use it in the subject line
			switch( $why_notify )
			{
				case "expired":
					/* translators: flag explaining why notification is occuring */
					$why_notify = __('Expiration', 'contentscheduler');
					break;
				case "notified":
					/* translators: flag explaining why notification is occuring */
					$why_notify = __('Pending', 'contentscheduler');
					break;
				default:
					/* translators: flag explaining why notification is occuring */
					$why_notify = __('Mysterious', 'contentscheduler');
			} // end switch
			// Determine who we need to notify:
			// notify_whom
			// a. 'admin'
			// b. 'author'
			// c. 'both'
			$notify_whom = '';
			if( $options['notify-admin'] == '1' )
			{
				if( $options['notify-author'] == '1' )
				{
					$notify_whom = 'both';
				}
				else
				{
					$notify_whom = 'admin';
				}
			}
			elseif( $options['notify-author'] == '1' )
			{
				$notify_whom = 'author';
			} // end if
			// Now, make sure we really have people to notify, otherwise get out of here.
			if( $notify_whom == '' )
			{
				return;
			}
			// get the admin_email, to use repeatedly inside this foreach
			$site_admin_email = "Admin <".get_option('admin_email').">";
			foreach( $posts_to_notify as $cur_post )
			{				
				// cur_post is just a number	
				// get post data
				$post_data = get_post( $cur_post, ARRAY_A );
				// get the author ID
				$auth_id = $post_data['post_author'];
				// get the author email address
				$auth_info = get_userdata( $auth_id );
				$auth_email = "Author <" . $auth_info->user_email . ">";
				// get the post ID
				$post_id = $post_data['ID'];
				// get the post title
				$post_title = $post_data['post_title'];
				// get / create the post viewing URL
				$post_view_url = $post_data['guid'];
				// get / create the post editing url
				// $post_edit_url = "Fake Post Editing URL";
				// get the post expiration date
				$post_expiration_date = ( get_post_meta( $post_data['ID'], '_cs-expire-date', true) );
				// pack it up into our array
				// make a new item array
				$new_item['ID'] = $post_id;
				$new_item['post_title'] = $post_title;
				$new_item['view_url'] = $post_view_url;
				// $new_item['edit_url'] = $post_edit_url;
				$new_item['expiration_date'] = $post_expiration_date;
				if( $notify_whom == 'author' OR $notify_whom == 'both' )
				{
					// add the post to the notification list
					$notify_list[$auth_email][] = $new_item;
				}
				// if we are notifying admin, we can add it to their array
				if( $notify_whom == 'admin' OR $notify_whom == 'both' )
				{
					// See if the site_admin_email matches the author email.
					// If it does, the admin is already being notified, so we should not continue
					if( $site_admin_email != $auth_email )
					{
						// add the post to the notification list for the admin user
						$notify_list[$site_admin_email][] = $new_item;
					}
				} // end if
			} // end foreach
			// Now we need to step through each of $notify_list[ {email_address} ]
			// and compile a message for each unique email_address
			// then send it and repeat
			//
			// step through each element of $notify_list
			$blog_name = htmlspecialchars_decode( get_bloginfo('name'), ENT_QUOTES );
			foreach( $notify_list as $usr_email=>$user )
			{
				// reset $usr_msg
				$usr_msg = sprintf( __("The following notifications come from the website: %s\n", 'contentscheduler'), $blog_name );
				// tell them why they are receiving the notification
				$usr_msg .= __("Reason for notification:\n", 'contentscheduler');
				if( $why_notify == 'Expiration' )
				{
					$usr_msg .= __("These notifications indicate items Content Scheduler has automatically applied expiration changes to.\n", 'contentscheduler');
				}
				else
				{
					$usr_msg .= __("These notifications indicate items expiring soon OR items that have expired but have not had any automatic changes applied.\n", 'contentscheduler');
				} // end if
				$usr_msg .= "====================\n";
				// get this user's email address -- it is the key for the current element, $user
				if( ! empty( $usr_email ) )
				{
					// step through elements in the user's array
					foreach( $user as $post )
					{						
						$usr_msg .= sprintf( __('The Post / Page entitled %1$s, with the post_id of %2$d, has an expiration date of %3$s', 'contentscheduler'), $post['post_title'], $post['ID'], $post['expiration_date'] );
						$usr_msg .= "\n";
						$usr_msg .= sprintf( __('Unless the content is deleted, it can be viewed at %s.', 'contentscheduler'), $post['view_url'] );
						$usr_msg .= "\n=====\n";
					} // end foreach stepping through list of posts for a user
					// send $msg to $user_email
					// Build subject line
					/* translators: Type of notification, then blog name */
					$subject = sprintf( __('%1$s Notification from %2$s', 'contentscheduler'), $why_notify, $blog_name );
					// Send the message
					if( wp_mail( $usr_email, $subject, $usr_msg ) == 1 )
					{
						// SUCCESS
						//$this->log_to_file('debug', "Email sent out successfully.\n");
					}
					else
					{
						// FAILED
						//$this->log_to_file('debug', "Email sending returned FALSE.\n");
					}
				} // end if checking that email address existed
				else
				{
					// 
				}
			} // end foreach stepping through list of users to notify
?>