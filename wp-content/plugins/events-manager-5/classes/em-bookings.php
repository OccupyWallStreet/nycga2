<?php
/**
 * Deals with the booking info for an event
 * @author marcus
 *
 */
class EM_Bookings extends EM_Object implements Iterator{
	
	/**
	 * Array of EM_Booking objects for a specific event
	 * @var array
	 */
	var $bookings = array();
	/**
	 * @var EM_Tickets
	 */
	var $tickets;
	/**
	 * @var int
	 */
	var $event_id;
	/**
	 * How many spaces this event has
	 * @var int
	 */
	var $spaces;
	
	/**
	 * Creates an EM_Bookings instance, currently accepts an EM_Event object (gets all bookings for that event) or array of any EM_Booking objects, which can be manipulated in bulk with helper functions.
	 * @param EM_Event $event
	 * @return null
	 */
	function EM_Bookings( $data = false ){
		if( is_object($data) && get_class($data) == "EM_Event" ){ //Creates a blank bookings object if needed
			global $wpdb;
			$this->event_id = $data->event_id;
			$sql = "SELECT * FROM ". EM_BOOKINGS_TABLE ." WHERE event_id ='{$this->event_id}' ORDER BY booking_date";
			$bookings = $wpdb->get_results($sql, ARRAY_A);
			foreach ($bookings as $booking){
				$this->bookings[] = new EM_Booking($booking);
			}
			$this->spaces = $this->get_spaces();
		}elseif( is_array($data) ){
			foreach( $data as $EM_Booking ){
				if( get_class($EM_Booking) == 'EM_Booking'){
					$this->bookings[] = $EM_Booking;
				}
			}
		}
	}
	
	/**
	 * Add a booking into this event (or add spaces if person already booked this), checking that there's enough space for the event
	 * @param EM_Booking $EM_Booking
	 * @return boolean
	 */
	function add( $EM_Booking ){
		global $wpdb,$EM_Mailer;
		if ( $this->get_available_spaces() >= $EM_Booking->get_spaces(true) ) {
			//Save the booking
			$email = false;
			//set status depending on approval settings
			if( empty($EM_Booking->booking_status) ){ //if status is not set, give 1 or 0 depending on approval settings
				$EM_Booking->booking_status = get_option('dbem_bookings_approval') ? 0:1;
			}
			$result = $EM_Booking->save(false);
			if($result){
				//Success
			    do_action('em_bookings_added', $EM_Booking);
				$this->bookings[] = $EM_Booking;
				$email = $EM_Booking->email();
				if( get_option('dbem_bookings_approval') == 1 && $EM_Booking->booking_status == 0){
					$this->feedback_message = get_option('dbem_booking_feedback_pending');
				}else{
					$this->feedback_message = get_option('dbem_booking_feedback');
				}
				if(!$email){
					$EM_Booking->email_not_sent = true;
					$this->feedback_message .= ' '.get_option('dbem_booking_feedback_nomail');
					if( current_user_can('activate_plugins') ){
						if( count($EM_Booking->get_errors()) > 0 ){
							$this->feedback_message .= '<br/><strong>Errors:</strong> (only admins see this message)<br/><ul><li>'. implode('</li><li>', $EM_Booking->get_errors()).'</li></ul>';
						}else{
							$this->feedback_message .= '<br/><strong>No errors returned by mailer</strong> (only admins see this message)';
						}
					}
				}
				return apply_filters('em_bookings_add', true, $EM_Booking);
			}else{
				//Failure
				$this->errors[] = "<strong>".get_option('dbem_booking_feedback_error')."</strong><br />". implode('<br />', $EM_Booking->errors);
			}
		} else {
			 $this->add_error(get_option('dbem_booking_feedback_full'));
		} 
		return apply_filters('em_bookings_add', false, $EM_Booking);
	}
	
	/**
	 * Get POST data and create a booking for each ticket requested. If successful, a booking object is returned, false if not.
	 * @return false|object
	 */
	function add_from_post(){
		$EM_Booking = new EM_booking();
		$result = $EM_Booking->get_post();
		if($result){
			$result = $this->add($EM_Booking);
			if($result){
				$result = $EM_Booking;
			}
			$this->feedback_message = sprintf(__('%s created.','dbem'),__('Booking','dbem'));
		}else{
			$this->errors = array_merge($this->errors, $EM_Booking->errors);
		}
		return apply_filters('em_bookings_add_from_post',$result,$EM_Booking,$this);
	}
	
	/**
	 * Smart event locator, saves a database read if possible. Note that if an event doesn't exist, a blank object will be created to prevent duplicates.
	 */
	function get_event(){
		global $EM_Event;
		if( is_object($EM_Event) && $EM_Event->event_id == $this->event_id ){
			return $EM_Event;
		}else{
			if( is_numeric($this->event_id) && $this->event_id > 0 ){
				return new EM_Event($this->event_id, 'event_id');
			}elseif( count($this->bookings) > 0 ){
				foreach($this->bookings as $EM_Booking){
					/* @var $EM_Booking EM_Booking */
					return new EM_Event($EM_Booking->event_id, 'event_id');
				}
			}
		}
		return new EM_Event($this->event_id);
	}
	
	/**
	 * Retrieve and save the bookings belonging to instance. If called again will return cached version, set $force_reload to true to create a new EM_Tickets object.
	 * @param boolean $force_reload
	 * @return EM_Tickets
	 */
	function get_tickets( $force_reload = false ){
		if( !is_object($this->tickets) || $force_reload ){
			$this->tickets = new EM_Tickets($this->event_id);
		}else{
			$this->tickets->event_id = $this->event_id;
		}
		return apply_filters('em_bookings_get_tickets', $this->tickets, $this);
	}
	
	/**
	 * Returns EM_Tickets object with available tickets
	 * @return EM_Tickets
	 */
	function get_available_tickets(){
		$tickets = array();
		foreach ($this->get_tickets() as $EM_Ticket){
			/* @var $EM_Ticket EM_Ticket */
			if( $EM_Ticket->is_available() ){
				//within time range
				if( $EM_Ticket->get_available_spaces() > 0 ){
					$tickets[] = $EM_Ticket;
				}
			}
		}
		$EM_Tickets = new EM_Tickets($tickets);
		return apply_filters('em_bookings_get_tickets', $EM_Tickets, $this);
	}
	
	function get_user_list(){
		$users = array();
		foreach( $this->get_bookings()->bookings as $EM_Booking ){
			$users[$EM_Booking->person->ID] = $EM_Booking->person;
		}
		return $users;
	}
	
	/**
	 * does this ticket exist?
	 * @return bool 
	 */
	function ticket_exists($ticket_id){
		$EM_Tickets = $this->get_tickets();
		foreach( $EM_Tickets->tickets as $EM_Ticket){
			if($EM_Ticket->ticket_id == $ticket_id){
				return apply_filters('em_bookings_ticket_exists',true, $EM_Ticket, $this);
			}
		}
		return apply_filters('em_bookings_ticket_exists',false, false,$this);
	}
	
	function has_space(){
		return count($this->get_available_tickets()->tickets) > 0;
	}
	
	function has_open_time(){
	    $return = false;
	    $EM_Event = $this->get_event();
	    if(!empty($EM_Event->event_rsvp_date) && $EM_Event->rsvp_end > current_time('timestamp')){
	    	$return = true;
	    }elseif( empty($EM_Event->event_rsvp_date) && $EM_Event->start > current_time('timestamp') ){
	    	$return = true;
	    }
	    return $return;
	}
	
	function is_open(){
		//TODO extend booking options
		$return = $this->has_open_time() && $this->has_space();
		return apply_filters('em_bookings_is_open', $return, $this);
	}
	
	/**
	 * Delete bookings on this id
	 * @return boolean
	 */
	function delete(){
		global $wpdb;
		$booking_ids = array();
		//get the booking ids tied to this event
		foreach( $this->bookings as $EM_Booking ){
			$booking_ids[] = $EM_Booking->booking_id;
		}
		$result_tickets = true;
		$result = true;
		if( count($booking_ids) > 0 ){
			//Delete bookings and ticket bookings
			$result_tickets = $wpdb->query("DELETE FROM ". EM_TICKETS_BOOKINGS_TABLE ." WHERE booking_id IN (".implode(',',$booking_ids).");");
			$result = $wpdb->query("DELETE FROM ".EM_BOOKINGS_TABLE." WHERE event_id IN (".implode(',',$booking_ids).")");
		}
		return ($result !== false && $result_tickets !== false);
	}

	
	/**
	 * Will approve all supplied booking ids, which must be in the form of a numeric array or a single number.
	 * @param array|int $booking_ids
	 * @return boolean
	 */
	function approve( $booking_ids ){
		$this->set_status(1, $booking_ids);
		return false;
	}
	
	/**
	 * Will reject all supplied booking ids, which must be in the form of a numeric array or a single number.
	 * @param array|int $booking_ids
	 * @return boolean
	 */
	function reject( $booking_ids ){
		return $this->set_status(2, $booking_ids);
	}
	
	/**
	 * Will unapprove all supplied booking ids, which must be in the form of a numeric array or a single number.
	 * @param array|int $booking_ids
	 * @return boolean
	 */
	function unapprove( $booking_ids ){
		return $this->set_status(0, $booking_ids);
	}
	
	/**
	 * @param int $status
	 * @param array|int $booking_ids
	 * @return bool
	 */
	function set_status($status, $booking_ids){
		//FIXME status should work with instantiated object
		//FIXME there is a vulnerability where any user can approve/reject bookings if they know the ID
		if( $this->array_is_numeric($booking_ids) ){
			//Get all the bookings
			$results = array();
			$mails = array();
			foreach( $booking_ids as $booking_id ){
				$EM_Booking = new EM_Booking($booking_id);
				if( !$EM_Booking->can_manage() ){
					$this->feedback_message = __('Bookings %s. Mails Sent.', 'dbem');
					return false;
				}
				$results[] = $EM_Booking->set_status($status);
			}
			if( !in_array('false',$results) ){
				$this->feedback_message = __('Bookings %s. Mails Sent.', 'dbem');
				return true;
			}else{
				//TODO Better error handling needed if some bookings fail approval/failure
				$this->feedback_message = __('An error occurred.', 'dbem');
				return false;
			}
		}elseif( is_numeric($booking_ids) || is_object($booking_ids) ){
			$EM_Booking = ( is_object($booking_ids) && get_class($booking_ids) == 'EM_Booking') ? $booking_ids : new EM_Booking($booking_ids);
			$result = $EM_Booking->set_status($status);
			$this->feedback_message = $EM_Booking->feedback_message;
			return $result;
		}
		return false;	
	}
	

	/**
	 * Get the total number of spaces this event has. This will show the lower value of event global spaces limit or total ticket spaces. Setting $force_refresh to true will recheck spaces, even if previously done so.
	 * @param boolean $force_refresh
	 * @return int
	 */
	function get_spaces( $force_refresh=false ){
		if($force_refresh || $this->spaces == 0){
			$this->spaces = $this->get_tickets()->get_spaces();
		}
		//check overall events cap
		if(!empty($this->get_event()->event_spaces) && $this->get_event()->event_spaces < $this->spaces) $this->spaces = $this->get_event()->event_spaces;
		return apply_filters('em_booking_get_spaces',$this->spaces,$this);
	}
	
	/**
	 * Returns number of available spaces for this event. If approval of bookings is on, will include pending bookings depending on em option.
	 * @return int
	 */
	function get_available_spaces(){
		$spaces = $this->get_spaces();
		$available_spaces = $spaces - $this->get_booked_spaces();
		if( get_option('dbem_bookings_approval_reserved') ){ //deduct reserved/pending spaces from available spaces 
			$available_spaces -= $this->get_pending_spaces();
		}
		return apply_filters('em_booking_get_available_spaces', $available_spaces, $this);
	}

	/**
	 * Returns number of booked spaces for this event. If approval of bookings is on, will return number of booked confirmed spaces.
	 * @return int
	 */
	function get_booked_spaces($force_refresh = false){
		$booked_spaces = 0;
		foreach ( $this->bookings as $EM_Booking ){
			if( $EM_Booking->booking_status == 1 ){
				$booked_spaces += $EM_Booking->get_spaces($force_refresh);
			}
		}
		return apply_filters('em_bookings_get_booked_spaces', $booked_spaces, $this);
	}
	
	/**
	 * Gets number of pending spaces awaiting approval. Will return 0 if booking approval is not enabled.
	 * @return int
	 */
	function get_pending_spaces(){
		if( get_option('dbem_bookings_approval') == 0 ){
			return apply_filters('em_bookings_get_pending_spaces', 0, $this);
		}
		$pending = 0;
		foreach ( $this->bookings as $booking ){
			if($booking->booking_status == 0){
				$pending += $booking->get_spaces();
			}
		}
		return apply_filters('em_bookings_get_pending_spaces', $pending, $this);
	}
	
	/**
	 * Gets number of bookings (not spaces). If booking approval is enabled, only the number of approved bookings will be shown.
	 * @return EM_Bookings
	 */
	function get_bookings( $all_bookings = false ){
		$confirmed = array();
		foreach ( $this->bookings as $booking ){
			if( $booking->booking_status == 1 || (get_option('dbem_bookings_approval') == 0 && $booking->booking_status == 0) || $all_bookings ){
				$confirmed[] = $booking;
			}
		}
		$EM_Bookings = new EM_Bookings($confirmed);
		return $EM_Bookings;		
	}
	
	/**
	 * Get pending bookings. If booking approval is disabled, will return no bookings. 
	 * @return EM_Bookings
	 */
	function get_pending_bookings(){
		if( get_option('dbem_bookings_approval') == 0 ){
			return new EM_Bookings();
		}
		$pending = array();
		foreach ( $this->bookings as $booking ){
			if($booking->booking_status == 0){
				$pending[] = $booking;
			}
		}
		$EM_Bookings = new EM_Bookings($pending);
		return $EM_Bookings;	
	}	
	
	/**
	 * Get rejected bookings. If booking approval is disabled, will return no bookings. 
	 * @return array EM_Bookings
	 */
	function get_rejected_bookings(){
		$rejected = array();
		foreach ( $this->bookings as $booking ){
			if($booking->booking_status == 2){
				$rejected[] = $booking;
			}
		}
		$EM_Bookings = new EM_Bookings($rejected);
		return $EM_Bookings;
	}	
	
	/**
	 * Get cancelled bookings. 
	 * @return array EM_Booking
	 */
	function get_cancelled_bookings(){
		$cancelled = array();
		foreach ( $this->bookings as $booking ){
			if($booking->booking_status == 3){
				$cancelled[] = $booking;
			}
		}
		$EM_Bookings = new EM_Bookings($cancelled);
		return $EM_Bookings;
	}
	
	/**
	 * Checks if a person with similar details has booked for this before
	 * @param $person_id
	 * @return EM_Booking
	 */
	function find_previous_booking($EM_Booking){
		//First see if we have a similar person on record that's making this booking
		$EM_Booking->person->load_similar();
		//If person exists on record, see if they've booked this event before, if so return the booking.
		if( is_numeric($EM_Booking->person->ID) && $EM_Booking->person->ID > 0 ){
			$EM_Booking->person_id = $EM_Booking->person->ID;
			foreach ($this->bookings as $booking){
				if( $booking->person_id == $EM_Booking->person->ID ){
					return $booking;
				}
			}
		}
		return false;
	}
	
	/**
	 * Checks to see if user has a booking for this event
	 * @param unknown_type $user_id
	 */
	function has_booking( $user_id = false ){
		if( $user_id === false ){
			$user_id = get_current_user_id();
		}
		if( is_numeric($user_id) && $user_id > 0 ){
			foreach ($this->bookings as $EM_Booking){
				if( $EM_Booking->person->ID == $user_id && !in_array($EM_Booking->booking_status, array(2,3)) ){
					return apply_filters('em_bookings_has_booking', $EM_Booking, $this);
				}
			}	
		}
		return apply_filters('em_bookings_has_booking', false, $this);
	}
	
	/**
	 * Get bookings that match the array of arguments passed.
	 * @return array 
	 * @static
	 */
	function get( $args = array(), $count = false ){
		global $wpdb,$current_user;
		$bookings_table = EM_BOOKINGS_TABLE;
		$events_table = EM_EVENTS_TABLE;
		$locations_table = EM_LOCATIONS_TABLE;
		
		//Quick version, we can accept an array of IDs, which is easy to retrieve
		if( self::array_is_numeric($args) ){ //Array of numbers, assume they are event IDs to retreive
			//We can just get all the events here and return them
			$sql = "
				SELECT * FROM $bookings_table b 
				LEFT JOIN $events_table e ON e.event_id=b.event_id 
				WHERE booking_id".implode(" OR booking_id=", $args);
			$results = $wpdb->get_results(apply_filters('em_bookings_get_sql',$sql),ARRAY_A);
			$bookings = array();
			foreach($results as $result){
				$bookings[] = new EM_Booking($result);
			}
			return $bookings; //We return all the bookings matched as an EM_Booking array. 
		}
		
		//We assume it's either an empty array or array of search arguments to merge with defaults			
		$args = self::get_default_search($args);
		$limit = ( $args['limit'] && is_numeric($args['limit'])) ? "LIMIT {$args['limit']}" : '';
		$offset = ( $limit != "" && is_numeric($args['offset']) ) ? "OFFSET {$args['offset']}" : '';
		
		//Get the default conditions
		$conditions = self::build_sql_conditions($args);
		//Put it all together
		$where = ( count($conditions) > 0 ) ? " WHERE " . implode ( " AND ", $conditions ):'';
		
		//Get ordering instructions
		$EM_Booking = new EM_Booking();
		$accepted_fields = $EM_Booking->get_fields(true);
		$orderby = self::build_sql_orderby($args, $accepted_fields);
		//Now, build orderby sql
		$orderby_sql = ( count($orderby) > 0 ) ? 'ORDER BY '. implode(', ', $orderby) : '';
		//Selector
		$selectors = ( $count ) ?  'COUNT(*)':'*';
		
		//Create the SQL statement and execute
		$sql = "
			SELECT $selectors FROM $bookings_table 
			LEFT JOIN $events_table ON {$events_table}.event_id={$bookings_table}.event_id 
			LEFT JOIN $locations_table ON {$locations_table}.location_id={$events_table}.location_id
			$where
			$orderby_sql
			$limit $offset
		";
		//If we're only counting results, return the number of results
		if( $count ){
			return apply_filters('em_bookings_get_count', $wpdb->get_var($sql), $args);		
		}
		$results = $wpdb->get_results( apply_filters('em_events_get_sql',$sql, $args), ARRAY_A);

		//If we want results directly in an array, why not have a shortcut here?
		if( $args['array'] == true ){
			return $results;
		}
		
		//Make returned results EM_Booking objects
		$results = (is_array($results)) ? $results:array();
		$bookings = array();
		foreach ( $results as $booking ){
			$bookings[] = new EM_Booking($booking);
		}
		$EM_Bookings = new EM_Bookings($bookings);
		return apply_filters('em_bookings_get', $EM_Bookings);
	}
	
	function count( $args = array() ){
		return self::get($args, true);
	}
	

	//List of patients in the patient database, that a user can choose and go on to edit any previous treatment data, or add a new admission.
	function export_csv() {
		global $EM_Event;
		if($EM_Event->event_id != $this->event_id ){
			$event = $this->get_event();
			$event_name = $event->name;
		}else{
			$event_name = $EM_Event->name;
		}
		// The name of the file on the user's pc
		$file_name = sanitize_title($event_name). "-bookings.csv";
		
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: Attachment; filename=$file_name");
		em_locate_template('templates/csv-event-bookings.php', true);
		exit();
	}
	
	/* Overrides EM_Object method to apply a filter to result
	 * @see wp-content/plugins/events-manager/classes/EM_Object#build_sql_conditions()
	 */
	function build_sql_conditions( $args = array() ){
		$conditions = apply_filters( 'em_bookings_build_sql_conditions', parent::build_sql_conditions($args), $args );
		if( is_numeric($args['status']) ){
			$conditions['status'] = 'booking_status='.$args['status'];
		}elseif( is_array($args['status']) && count($args['status']) > 0 ){
			$conditions['status'] = 'booking_status IN ('.implode(',',$args['status']).')';
		}elseif( !is_array($args['status']) && preg_match('/^([0-9],?)+$/', $args['status']) ){
			$conditions['status'] = 'booking_status IN ('.$args['status'].')';
		}
		if( is_numeric($args['person']) && current_user_can('manage_others_bookings') ){
			$conditions['person'] = EM_BOOKINGS_TABLE.'.person_id='.$args['person'];
		}
		if( EM_MS_GLOBAL && !empty($args['blog']) && is_numeric($args['blog']) ){
			if( is_main_site($args['blog']) ){
				$conditions['blog'] = "(".EM_EVENTS_TABLE.".blog_id={$args['blog']} OR ".EM_EVENTS_TABLE.".blog_id IS NULL)";
			}else{
				$conditions['blog'] = "(".EM_EVENTS_TABLE.".blog_id={$args['blog']})";
			}
		}
		return apply_filters('em_bookings_build_sql_conditions', $conditions, $args);
	}
	
	/* Overrides EM_Object method to apply a filter to result
	 * @see wp-content/plugins/events-manager/classes/EM_Object#build_sql_orderby()
	 */
	function build_sql_orderby( $args, $accepted_fields, $default_order = 'ASC' ){
		return apply_filters( 'em_bookings_build_sql_orderby', parent::build_sql_orderby($args, $accepted_fields, get_option('dbem_events_default_order')), $args, $accepted_fields, $default_order );
	}
	
	/* 
	 * Adds custom Events search defaults
	 * @param array $array
	 * @return array
	 * @uses EM_Object#get_default_search()
	 */
	function get_default_search( $array = array() ){
		$defaults = array(
			'status' => false,
			'person' => true, //to add later, search by person's bookings...
			'blog' => get_current_blog_id()
		);	
		if( true || is_admin() ){
			//figure out default owning permissions
			if( !current_user_can('edit_others_events') ){
				$defaults['owner'] = get_current_user_id();
			}else{
				$defaults['owner'] = false;
			}
		}
		if( EM_MS_GLOBAL && !is_admin() ){
			if( empty($array['blog']) && is_main_site() && get_site_option('dbem_ms_global_events') ){
			    $array['blog'] = false;
			}
		}
		return apply_filters('em_bookings_get_default_search', parent::get_default_search($defaults,$array), $array, $defaults);
	}

	//Iterator Implementation
    public function rewind(){
        reset($this->bookings);
    }  
    public function current(){
        $var = current($this->bookings);
        return $var;
    }  
    public function key(){
        $var = key($this->bookings);
        return $var;
    }  
    public function next(){
        $var = next($this->bookings);
        return $var;
    }  
    public function valid(){
        $key = key($this->bookings);
        $var = ($key !== NULL && $key !== FALSE);
        return $var;
    }
}
?>