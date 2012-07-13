<?php

function ftm_admin_links() {
	global $bp, $forum_template;
	
	if ( bp_group_is_admin() || bp_group_is_mod() || is_super_admin() ) { ?>
	
		<script type="text/javascript"> jQuery(document).ready( function() {
			jQuery('#ftm_move_topic').live('click', function() {
				var topicinfo = jQuery(this).attr('alt').split('|');
				jQuery(this).addClass('loading');
				//alert( jQuery(this).attr('alt'));
				
				jQuery.post( ajaxurl, {
					action: 'ftm_move_topic',
					'topic_id': topicinfo[0],
					'group_id': topicinfo[1],
					'user_id': topicinfo[2],
					'action_url': topicinfo[3]
				},
				function(data) {
					jQuery('#ftm_container').html(data).show();
				});
				
				jQuery(this).removeClass('loading');
			});
		});</script>	
		
		| <a  id="ftm_move_topic" href="javascript:void()" alt="<?php 
			echo bp_the_topic_id() . '|' .
			$bp->groups->current_group->id . '|' .
			$bp->loggedin_user->id . '|' .
			$bp->root_domain . '/' . BP_GROUPS_SLUG . '/' . $forum_template->topic->object_slug . '/forum/'
		?>"><?php _e( 'Move Topic', 'topic_mover' ) ?></a>
		
		<div id="ftm_container" style="display:none; margin: 10px 0; "></div>
		
	<?php }
}
add_action( 'bp_group_forum_topic_meta', 'ftm_admin_links' );


function topic_mover() {
	global $bp, $forum_template, $wpdb;
		
/*		
	if ( !bp_group_is_admin() && !bp_group_is_mod() )
		return;
	$topic_id = bp_get_the_topic_id();
	$group_id = $bp->groups->current_group->id;
	$user_id = $bp->loggedin_user->id;
	$action_url = $bp->root_domain . '/' . BP_GROUPS_SLUG . '/' . $forum_template->topic->object_slug . '/forum/';	
*/
		
	$topic_id = $_POST['topic_id'];
	$group_id = $_POST['group_id'];
	$user_id = $_POST['user_id'];
	$action_url = $_POST['action_url'];
	
	// Insert dropdown option box in text.
	$output .= "<form action='{$action_url}' method='post' name='topic_mover_dropdown'>\n";
	$output .= __('Move this topic to the group: ', 'topic_mover') . "\n";
	$output .= '<select name="ftm_new_group_id" style="width:150px;">' . "\n";

	// incase we have the announce group plugin installed... find announce groups where this user is not an admin.
	// later move this to the announce group plugin, and make this an filter hook
	$announce_group_ids = ag_get_all_announce_groups_non_admin( $user_id );
	
	$all_groups = groups_get_groups( 'type=alphabetical&user_id='.$user_id.'&per_page=1000&populate_extras=&' );
	
	foreach ( $all_groups['groups'] as $group ) {
		if ( count($announce_group_ids) )
			if ( in_array( $group->id, $announce_group_ids ) )
				continue;
		$is_selected = ( $group_id == $group->id ) ? "selected" : "";
		$output .= "<option {$is_selected} value='{$group->id}'>".stripcslashes($group->name)."</option>\n";
	}

	$output .= '</select>' . "\n";
	$output .= "<input type='hidden' name='topic_mover' value='1' />\n";
	$output .= "<input type='hidden' name='ftm_old_group_id' value='{$group_id}' />\n";
	$output .= "<input type='hidden' name='ftm_topic_id' value='{$topic_id}' />\n";
	$output .= '<input type="submit" value="Move it!" />';
	$output .= wp_nonce_field( 'topic_mover', '_ftm_nonce', false, false );
	$output .= '</form>';
	
	echo $output;

}
add_action( 'wp_ajax_ftm_move_topic', 'topic_mover' );


// move the topic to the new group
function topic_mover_handler() {
	global $wpdb, $bbdb, $bp;	

	do_action( 'bbpress_init' );
	
	$old_group_id = $_POST['ftm_old_group_id'];
	$new_group_id = $_POST['ftm_new_group_id'];
	$topic_id = $_POST['ftm_topic_id'];
		
	if ( isset($_POST['topic_mover']) && isset($old_group_id) && isset($new_group_id) && isset($topic_id) ) {
	
		if ( !wp_verify_nonce( $_POST[ '_ftm_nonce' ], 'topic_mover' ) ) {
			return; 
		}
		
		//echo '<br><br>';
		//echo '<pre style="background:white">'; print_r( $_POST ); echo '</pre>';
			
		// Load Values from Post, and set up forum id, group objects and urls
		$old_forum_id = groups_get_groupmeta( $old_group_id, 'forum_id' );
		$new_forum_id = groups_get_groupmeta( $new_group_id, 'forum_id' );
		$old_group = groups_get_group( 'group_id=' . $old_group_id );
		$new_group = groups_get_group( 'group_id=' . $new_group_id );
		$old_group_url = bp_get_group_permalink($old_group);
		$new_group_url = bp_get_group_permalink($new_group);
		
		if ( !$new_forum_id ){
			bp_core_add_message( __('Could not move topic, the target group does not have an active forum', 'topic_mover'), 'error' );
			return;
		}
			
		// update the activity
		
		// get the new_forum_topic activity id
		$old_activity_ids[] = bp_activity_get_activity_id( array( 'type' => 'new_forum_topic', 'item_id' => $old_group_id, 'secondary_item_id' => $topic_id ) );
		
		// for forum posts, get the post ids so we can later get the activity ids. the activity table does not have this useful info. boohoo
		$sql = "SELECT post_id FROM {$bbdb->posts} WHERE topic_id='{$topic_id}' AND forum_id={$old_forum_id}"; // using new forum id cause we just updated it
		$topic_post_reply_ids = $wpdb->get_col( $sql );
		array_shift($topic_post_reply_ids); // remove the forum topic, which we'll get another way
		//echo '<pre style="background:white">'; print_r( $topic_post_reply_ids ); echo '</pre>';
		
		// get the new_forum_post activity ids
		foreach ( $topic_post_reply_ids as $topic_post_reply_id ) {
			$old_id = bp_activity_get_activity_id( array( 'type' => 'new_forum_post', 'item_id' => $old_group_id, 'secondary_item_id' => $topic_post_reply_id ) );
			if ( $old_id ) 
				$old_activity_ids[] = $old_id;
		}
		//echo '<pre style="background:white">'; print_r( $old_activity_ids ); echo '</pre>';
		
		// get the activity data based on the ids
		$old_topic_activity = bp_activity_get_specific( array( 'activity_ids' => $old_activity_ids ) );
		//echo '<pre style="background:white">'; print_r( $old_topic_activity ); echo '</pre>';
		
		// loop through each activity item and update it with the new group permalink
/*
		foreach ( $old_topic_activity['activities'] as $activity ) {
			$action = str_replace( $old_group_url, $new_group_url, $activity->action );
			$primary_link = str_replace( $old_group_url, $new_group_url, $activity->primary_link );
			bp_activity_add( $test = array(
				'id'                => $activity->id,
				'action'            => $action,
				'content'           => $activity->content,
				'component'         => $activity->component,
				'type'              => $activity->type,
				'primary_link'      => $primary_link,
				'user_id'           => $activity->user_id,
				'item_id'           => $new_group_id,
				'secondary_item_id' => $activity->secondary_item_id,
				'recorded_time'     => $activity->date_recorded,
				)
			);
		}
*/
		
		
		
		// Move Topic to new Forum & update the meta
		$sql = "UPDATE {$bbdb->topics} SET forum_id={$new_forum_id} WHERE topic_id={$topic_id}";
		$wpdb->query( $wpdb->prepare( $sql ) );
		//echo '<pre style="background:white">'; print_r( $sql ); echo '</pre>';
		
		$sql = "UPDATE {$bbdb->posts} SET forum_id={$new_forum_id} WHERE topic_id={$topic_id}";
		$wpdb->query( $wpdb->prepare( $sql ) );
		//echo '<pre style="background:white">'; print_r( $sql ); echo '</pre>';

		update_forum_meta_data( $new_forum_id );
		update_forum_meta_data( $old_forum_id );		
				
				
		bp_core_add_message( __('The topic has been moved to the group: ', 'topic_mover') . $new_group->name );
	}
}
add_action('init', 'topic_mover_handler');


// helper function to update forum meta values
function update_forum_meta_data( $forum_id ) {
	global $wpdb, $bbdb;
	
	if ( $forum_id ) {
		$num_topics = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$bbdb->topics} WHERE forum_id={$forum_id}" ) );
		$num_posts = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$bbdb->posts} WHERE forum_id={$forum_id}" ) );
		$results = $wpdb->query( $wpdb->prepare( "UPDATE {$bbdb->forums} SET topics={$num_topics}, posts={$num_posts} WHERE forum_id={$forum_id}" ) );
		return true;
	}
}

// return an array of announce group ids. (if the user is an admin of that announce group, don't return the id)
// later this shouuld be moved to the Announce Group plugin
function ag_get_all_announce_groups_non_admin( $user_id ) {
	global $wpdb;
	$result = $wpdb->get_col( $wpdb->prepare( 
		"SELECT {$bp->groups->table_name_members}.group_id 
		FROM {$bp->groups->table_name_groupmeta}, {$bp->groups->table_name_members} 
		WHERE {$bp->groups->table_name_members}.group_id = {$bp->groups->table_name_members}.group_id 
		AND user_id = {$user_id} 
		AND meta_key = 'ag_announce_group' 
		AND (is_admin = '0' AND is_mod = '0') 
		GROUP BY {$bp->groups->table_name_members}.group_id" 
		) );
	return $result;
}
?>