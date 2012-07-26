<?php
/**
 * bp_em_record_activity()
 *
 * If the activity stream component is installed, this function will record activity items for your
 * component.
 *
 * You must pass the function an associated array of arguments:
 *
 *     $args = array(
 *	 	 REQUIRED PARAMS
 *		 'action' => For em: "Andy high-fived John", "Andy posted a new update".
 *       'type' => The type of action being carried out, for em 'new_friendship', 'joined_group'. This should be unique within your component.
 *
 *		 OPTIONAL PARAMS
 *		 'id' => The ID of an existing activity item that you want to update.
 * 		 'content' => The content of your activity, if it has any, for em a photo, update content or blog post excerpt.
 *       'component' => The slug of the component.
 *		 'primary_link' => The link for the title of the item when appearing in RSS feeds (defaults to the activity permalink)
 *       'item_id' => The ID of the main piece of data being recorded, for em a group_id, user_id, forum_post_id - useful for filtering and deleting later on.
 *		 'user_id' => The ID of the user that this activity is being recorded for. Pass false if it's not for a user.
 *		 'recorded_time' => (optional) The time you want to set as when the activity was carried out (defaults to now)
 *		 'hide_sitewide' => Should this activity item appear on the site wide stream?
 *		 'secondary_item_id' => (optional) If the activity is more complex you may need a second ID. For em a group forum post may need the group_id AND the forum_post_id.
 *     )
 *
 * Events usage would be:
 *
 *   bp_em_record_activity( array( 'type' => 'new_highfive', 'action' => 'Andy high-fived John', 'user_id' => $bp->loggedin_user->id, 'item_id' => $bp->displayed_user->id ) );
 *
 */
function bp_em_record_activity( $args = '' ) {
	if ( !function_exists( 'bp_activity_add' ) )
		return false;

	$defaults = array(
		'id' => false,
		'user_id' => '',
		'action' => '',
		'content' => '',
		'primary_link' => '',
		'component' => 'events-manager',
		'type' => false,
		'item_id' => false,
		'secondary_item_id' => false,
		'recorded_time' => gmdate( "Y-m-d H:i:s" ),
		'hide_sitewide' => false
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );

	return bp_activity_add( array( 'id' => $id, 'user_id' => $user_id, 'action' => $action, 'content' => $content, 'primary_link' => $primary_link, 'component' => $component, 'type' => $type, 'item_id' => $item_id, 'secondary_item_id' => $secondary_item_id, 'recorded_time' => $recorded_time, 'hide_sitewide' => $hide_sitewide ) );
}

/**
 * Records new events to the activity stream.
 * @param unknown_type $result
 * @param unknown_type $EM_Event
 * @return unknown
 */
function bp_em_record_activity_event_save( $result, $EM_Event ){
	if( $result && $EM_Event->status == 1 && ($EM_Event->previous_status == 0 || !empty($EM_Event->is_new)) ){
		$user = get_userdata($EM_Event->owner);
		bp_em_record_activity( array(
			'user_id' => $user->ID,
			'action' => sprintf(__('%s added the event %s','dbem'), "<a href='".get_bloginfo('wpurl').'/'.BP_MEMBERS_SLUG.'/'.$user->user_login."/'>".$user->display_name."</a>", $EM_Event->output('#_EVENTLINK') ),
			'primary_link' => $EM_Event->output('#_EVENTURL'),
			'type' => 'new_event',
			'item_id' => $EM_Event->id,
		));
		//group activity
		if( !empty($EM_Event->group_id) ){
			//tis a group event
			$group = new BP_Groups_Group($EM_Event->group_id);
			bp_em_record_activity( array(
				'user_id' => $user->ID,
				'action' => sprintf(__('%s added the event %s of the %s group.','dbem'), "<a href='".get_bloginfo('wpurl').'/'.BP_MEMBERS_SLUG.'/'.$user->user_login."/'>".$user->display_name."</a>", $EM_Event->output('#_EVENTLINK'), '<a href="'.bp_get_group_permalink($group).'">'.bp_get_group_name($group).'</a>' ),
				'component' => 'groups',
				'type' => 'new_event',
				'item_id' => $EM_Event->group_id,
			));
		}
	}
	return $result;
}
add_filter('em_event_save','bp_em_record_activity_event_save', 10, 2);

/**
 * @param boolean $result
 * @param EM_Booking $EM_Booking
 * @return boolean
 */
function bp_em_record_activity_booking_save( $result, $EM_Booking ){
	if( $result ){
		$user = $EM_Booking->person;
		$user_link = "<a href='".get_bloginfo('wpurl').'/'.BP_MEMBERS_SLUG.'/'.$user->user_login."/'>".$user->display_name."</a>";
		$event_link = $EM_Booking->get_event()->output('#_EVENTLINK');
		$status = $EM_Booking->status;
		if( $status == 1 || (!get_option('dbem_bookings_approval') && $status < 2) ){
			$action = sprintf(__('%s is attending %s.','dbem'), $user_link, $event_link );
		}elseif( ($EM_Booking->previous_status == 1 || (!get_option('dbem_bookings_approval') && $EM_Booking->previous_status < 2)) && ($status > 1 || empty($status) || (!get_option('dbem_bookings_approval') && $status != 1)) ){
			$action = sprintf(__('%s will not be attending %s anymore.','dbem'), $user_link, $event_link );
		}
		$EM_Event = $EM_Booking->get_event();
		if( !empty($EM_Event->group_id) ){
			$group = new BP_Groups_Group($EM_Event->group_id);
			$group_link = '<a href="'.bp_get_group_permalink($group).'">'.bp_get_group_name($group).'</a>';
			if( $status == 1 || (!get_option('dbem_bookings_approval') && $status < 2) ){
				$action = sprintf(__('%s is attending %s of the group %s.','dbem'), $user_link, $event_link, $group_link );
			}elseif( ($EM_Booking->previous_status == 1 || (!get_option('dbem_bookings_approval') && $EM_Booking->previous_status < 2)) && ($status > 1 || empty($status) || (!get_option('dbem_bookings_approval') && $status != 1)) ){
				$action = sprintf(__('%s will not be attending %s of group %s anymore.','dbem'), $user_link, $event_link, $group_link );
			}
		}
		if( !empty($action) ){
			bp_em_record_activity( array(
				'user_id' => $EM_Booking->person->ID,
				'action' => $action,
				'primary_link' => $EM_Event->output('#_EVENTURL'),
				'type' => 'new_booking',
				'item_id' => $EM_Event->id,
				'secondary_item_id' => $EM_Booking->id
			));
			//group activity
			if( !empty($EM_Event->group_id) ){
				//tis a group event
				bp_em_record_activity( array(
					'component' => 'groups',
					'item_id' => $EM_Event->group_id,
					'user_id' => $EM_Booking->person->ID,
					'action' => $action,
					'primary_link' => $EM_Event->output('#_EVENTURL'),
					'type' => 'new_booking',
					'secondary_item_id' => $EM_Booking->id
				));
			}
		}
	}
	return $result;
}
add_filter('em_booking_save','bp_em_record_activity_booking_save', 10, 2);
add_filter('em_booking_delete','bp_em_record_activity_booking_save', 10, 2);