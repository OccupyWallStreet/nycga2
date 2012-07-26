<?php
class EM_People extends EM_Object {
	
	/**
	 * Handles the action of someone being deleted on WordPress
	 * @param int $id
	 */
	function delete_user( $id ){
		global $wpdb;
		if( current_user_can('delete_users') ){
			if( $_REQUEST['delete_option'] == 'reassign' && is_numeric($_REQUEST['reassign_user']) ){
				$wpdb->update(EM_EVENTS_TABLE, array('event_owner'=>$_REQUEST['reassign_user']), array('event_owner'=>$id));
			}else{
				//User is being deleted, so we delete their events and cancel their bookings.
				$wpdb->query("DELETE FROM ".EM_EVENTS_TABLE." WHERE event_owner=$id");
			}
		}
		//set bookings to cancelled
		$wpdb->update(EM_BOOKINGS_TABLE, array('booking_status'=>3, 'person_id'=>0, 'booking_comment'=>__('User deleted by administrators','dbem')), array('person_id'=>$id));
	}
	
	/**
	 * Adds phone number to contact info of users, compatible with previous phone field method
	 * @param $array
	 * @return array
	 */
	function user_contactmethods($array){
		$array['dbem_phone'] = __('Phone','dbem') . ' <span class="description">('. __('Events Manager','dbem') .')</span>';
		return $array;
	}	
}
add_action('delete_user', array('EM_People','delete_user'),10,1);
add_filter( 'user_contactmethods' , array('EM_People','user_contactmethods'),10,1);
?>