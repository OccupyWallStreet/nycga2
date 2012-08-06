<?php
			$options = get_option('ContentScheduler_Options');
			// Now, make the array we would pass to wp_update_post
			// This is a local variable, so each time process_post is called, it will be new
			$update_post = array('ID' => $postid);
  	        // Get the Post's ID into the update array
  	        // $update_post['ID'] = $postid;
  	        // STATUS AND VISIBILITY
  	        switch( $options['chg-status'] )
  	        {
				case '0':
					// we do not need a post_status key
					break;
				case '1':
					$update_post['post_status'] = 'pending';
					break;
				case '2':
					$update_post['post_status'] = 'draft';
					break;
				case '3':
					$update_post['post_status'] = 'private';
					break;
				// default:
					// if it is anything else, let's make sure the post_status key is just gone from the array
					// NOTE: It would be better if we could just not make the array in the first place
					// unset( $update_post['post_status'] );
  	        } // end switch
  	        // ==========
  	        // Pages don't have Categories
  	        // Pages don't have Tags
  	        // Pages could have Parent Page changes (in a future version)
  	        // Pages culd have Template changes (in a future version)
  	        // Use the update array to actually update the post
  	        if( !wp_update_post( $update_post ) )
  	        {
  	        	// NOTE: We don't really want this to die
  	        	// for debug
  	        	// $this->log_to_file('debug', 'We failed to update a page');
  	        }
  	        else
  	        {
  	        	// update the post_meta so it won't end up here in our update query again
  	        	// We're not changing the expiration date, so we can look back and know when it expired.
  	        	update_post_meta( $postid, '_cs-enable-schedule', 'Disable' );
  	        }
?>