<?php
			global $wpdb;
			// get options
			$options = get_option('ContentScheduler_Options');
			// get days of notification offset
			$notice_days = $options['notify-before'];
			// setup timezone
			$this->setup_timezone();
			// find posts that need to send notifications
			$notification_timestamp = time() + ($notice_days * 24 * 60 * 60);
			$notification_string = date( "Y-m-d H:i:s", $notification_timestamp );
			// select all Posts / Pages that have:
			// a. "enable-expiration" set
			// b. have expiration date older than right now
			// OR
			// c. have expiration date older than right now PLUS {n} days
			// We need to get the hours / days offset from $options first.
			// Then we need to do some date arithmetic:
			// Take RIGHT NOW and ADD the hours / days ofset.
			// That is the date to use below in date comparison.
			// This version gets only the post_id.
			// By WP defaults, it returns on object, with properties 'post_id' and 'expiration'
			$querystring = 'SELECT postmetadate.post_id 
				FROM 
				' .$wpdb->postmeta. ' AS postmetadate, 
				' .$wpdb->postmeta. ' AS postmetadoit, 
				' .$wpdb->posts. ' AS posts 
				WHERE postmetadoit.meta_key = "_cs-enable-schedule" 
				AND postmetadoit.meta_value = "Enable" 
				AND postmetadate.meta_key = "_cs-expire-date" 
				AND postmetadate.meta_value <= "' . $notification_string . '" 
				AND postmetadate.post_id = postmetadoit.post_id 
				AND postmetadate.post_id = posts.ID 
				AND posts.post_status = "publish"';
			$result = $wpdb->get_results($querystring);
			// Act upon the results
			if ( ! empty( $result ) )
			{
				// Loop through the results
				// Is there a faster way to spit the results into an array?
				// Seems since we don't do any processing in the foreach, 
				// there is probably a faster method to get results to array.
				$post_list = array();
				foreach ( $result as $cur_post )
				{
					// get the ID into the array
					$post_list[] = $cur_post->post_id;
				} // end foreach stepping through posts to notify on
				// call the notification function
				$this->do_notifications($post_list, 'notified');
			} // end if checking for empty result
?>