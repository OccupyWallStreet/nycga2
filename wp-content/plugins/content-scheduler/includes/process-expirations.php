<?php
// find posts that need to take some expiration action
			global $wpdb;
			$options = get_option('ContentScheduler_Options');
			// setup timezone
			$this->setup_timezone();
			// select all Posts / Pages that have "enable-expiration" set and have expiration date older than right now
			// 12/8/2010 7:18:08 PM
			// Original has expiration date in results -- differing from process_notifications and causing problems.
			// $querystring = 'SELECT postmetadate.post_id, postmetadate.meta_value AS expiration 
			$querystring = 'SELECT postmetadate.post_id 
				FROM 
				' .$wpdb->postmeta. ' AS postmetadate, 
				' .$wpdb->postmeta. ' AS postmetadoit, 
				' .$wpdb->posts. ' AS posts 
				WHERE postmetadoit.meta_key = "_cs-enable-schedule" 
				AND postmetadoit.meta_value = "Enable" 
				AND postmetadate.meta_key = "_cs-expire-date" 
				AND postmetadate.meta_value <= "' . date("Y-m-d H:i:s") . '" 
				AND postmetadate.post_id = postmetadoit.post_id 
				AND postmetadate.post_id = posts.ID 
				AND posts.post_status = "publish"';
			$result = $wpdb->get_results($querystring);
			// Act upon the results
			if ( ! empty( $result ) )
			{
				// See if we are supposed to NOTIFY upon expiration
				// we do this in its own loop before deleting
				// because do_notifications() needs to access the posts before they are deleted to get info for the notify message
				// Maybe not necessary, though, since deleting just puts them in trash?
				if( $options['notify-on'] == '1' )
				{
					// build array of posts to send to do_notifications
					$posts_to_notify_on = array();
					foreach ( $result as $cur_post )
					{
						$posts_to_notify_on[] = $cur_post->post_id;
					}
					// call the notification function
					$this->do_notifications($posts_to_notify_on, 'expired');
				} // end if for notification on expiration
				// Shortcut: If exp-status = "Delete" then let's just delete and get on with things.
				if( $options['exp-status'] == '2' )
				{
					// Delete all those posts
					foreach ( $result as $cur_post )
					{
						// Move the item to the trash
						wp_delete_post( $cur_post->post_id );
					} // end foreach
				}
				else
				{
					// Proceed with the updating process	      	        
					// step through the results
					foreach ( $result as $cur_post )
					{
						// find out if it is a Page, Post, or what
						$post_type = $wpdb->get_var( 'SELECT post_type FROM ' . $wpdb->posts .' WHERE ID = ' . $cur_post->post_id );
						if ( $post_type == 'post' )
						{
							$this->process_post( $cur_post->post_id );
						}
						elseif ( $post_type == 'page' )
						{
							$this->process_page( $cur_post->post_id );
						}
						else
						{
							// it could be a custom post type
							$this->process_custom( $cur_post->post_id );
						} // end if
					} // end foreach
				} // end if (checking for DELETE)
			} // endif
?>