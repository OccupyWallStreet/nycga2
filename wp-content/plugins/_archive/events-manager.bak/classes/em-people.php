<?php
class EM_People extends EM_Object {
	
	/**
	 * Gets all users, if $return_people false an array associative arrays will be returned. If $return_people is true this will return an array of EM_Person objects
	 * @param $return_people
	 * @return array
	 */
	function get( $return_people = true ) {
		global $wpdb; 
		$sql = "SELECT *  FROM ". EM_PEOPLE_TABLE ;    
		$result = $wpdb->get_results($sql, ARRAY_A);
		if( $return_people ){
			//Return people as EM_Person objects
			$people = array();
			foreach ($result as $person){
				$people[] = new EM_Person($person);
			}
			return $people;
		}
		return $result;
	}
	
	/* Overrides EM_Object method to apply a filter to result
	 * @see wp-content/plugins/events-manager/classes/EM_Object#build_sql_conditions()
	 */
	function build_sql_conditions( $args = array() ){
		global $wpdb;
		//FIXME EM_People doesn't build sql conditions in EM_Object
		$conditions = array();
		
		//owner lookup
		//FIXME permissions need tweaking for people, not owned by event owner, but site.
		/*
		if( is_numeric($args['owner']) ){
			$conditions['owner'] = "person_owner=".get_current_user_id();
		}elseif( preg_match('/^([0-9],?)+$/', $args['owner']) ){
			$conditions['owner'] = "person_owner IN (".explode(',', $args['owner']).")";			
		}	
		*/
		return apply_filters( 'em_people_build_sql_conditions', $conditions, $args );
	}
	
	/* Overrides EM_Object method to apply a filter to result
	 * @see wp-content/plugins/people-manager/classes/EM_Object#build_sql_orderby()
	 */
	function build_sql_orderby( $args, $accepted_fields, $default_order = 'ASC' ){
		return apply_filters( 'em_people_build_sql_orderby', parent::build_sql_orderby($args, $accepted_fields, get_option('dbem_people_default_order')), $args, $accepted_fields, $default_order );
	}
	
	/* 
	 * Adds custom people search defaults
	 * @param array $array
	 * @return array
	 * @uses EM_Object#get_default_search()
	 */
	function get_default_search( $array = array() ){
		$defaults = array(
			'scope'=>false,
			'eventful' => false, //cats that have an event (scope will also play a part here
			'eventless' => false, //cats WITHOUT events, eventful takes precedence
		);
		return apply_filters('em_people_get_default_search', parent::get_default_search($defaults,$array), $array, $defaults);
	}	
	
	/**
	 * Handles the action of someone being deleted on wordpress
	 * @param int $id
	 */
	function user_deleted( $id ){
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
}
add_action('delete_user', array('EM_People','user_deleted'), 1,10);
?>