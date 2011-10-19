<?php
class EM_Booking extends EM_Object{
	//DB Fields
	var $id;
	var $event_id;
	var $person_id;
	var $price;
	var $spaces;
	var $comment;
	var $status = 0;
	var $notes = array();
	var $meta = array();
	var $fields = array(
		'booking_id' => array('name'=>'id','type'=>'%d'),
		'event_id' => array('name'=>'event_id','type'=>'%d'),
		'person_id' => array('name'=>'person_id','type'=>'%d'),
		'booking_price' => array('name'=>'price','type'=>'%d'),
		'booking_spaces' => array('name'=>'spaces','type'=>'%d'),
		'booking_comment' => array('name'=>'comment','type'=>'%s'),
		'booking_status' => array('name'=>'status','type'=>'%d'),
		'booking_meta' => array('name'=>'meta','type'=>'%s')
	);
	//Other Vars
	var $timestamp;
	var $person;
	var $required_fields = array('booking_id', 'event_id', 'person_id', 'booking_spaces');
	var $feedback_message = "";
	var $errors = array();
	/**
	 * Contains an array of custom fields for a booking. This is loaded from em_meta, where the booking_custom name contains arrays of data.
	 * @var array
	 */
	var $custom = array();
	/**
	 * If saved in this instance, you can see what previous approval status was.
	 * @var int
	 */
	var $previous_status;
	/**
	 * The booking approval status number corresponds to a state in this array.
	 * @var unknown_type
	 */
	var $status_array = array();
	/**
	 * @var EM_Tickets
	 */
	var $tickets;
	/**
	 * @var EM_Event
	 */
	var $event;
	/**
	 * @var EM_Tickets_Bookings
	 */
	var $tickets_bookings;
	/**
	 * If set to true, this booking can be managed by any logged in user.
	 * @var EM_Tickets_Bookings
	 */
	var $manage_override;
	
	/**
	 * Creates booking object and retreives booking data (default is a blank booking object). Accepts either array of booking data (from db) or a booking id.
	 * @param mixed $booking_data
	 * @return null
	 */
	function EM_Booking( $booking_data = false ){
		//Get the person for this booking
	  	if( $booking_data !== false ){
			//Load booking data
			$booking = array();
			if( is_array($booking_data) ){
				$booking = $booking_data;
			}elseif( is_numeric($booking_data) ){
				//Retreiving from the database		
				global $wpdb;			
				$sql = "SELECT * FROM ". EM_BOOKINGS_TABLE ." LEFT JOIN ". EM_META_TABLE ." ON object_id=booking_id WHERE booking_id ='$booking_data'";
				$booking = $wpdb->get_row($sql, ARRAY_A);
				//Custom Fields
				$custom = $wpdb->get_row("SELECT meta_key, meta_value FROM ". EM_BOOKINGS_TABLE ." LEFT JOIN ". EM_META_TABLE ." ON object_id=booking_id WHERE booking_id ='$booking_data' AND meta_key='booking_custom'");
			  	//Booking notes
			  	$notes = $wpdb->get_results("SELECT * FROM ". EM_META_TABLE ." WHERE meta_key='booking-note' AND object_id ='$booking_data'", ARRAY_A);
			  	foreach($notes as $note){
			  		$this->notes[] = unserialize($note['meta_value']);
			  	}
			}
			//booking meta
			$booking['booking_meta'] = (!empty($booking['booking_meta'])) ? unserialize($booking['booking_meta']):array();
			//Save into the object
			$this->to_object($booking);
			$this->get_person();
			$this->timestamp = strtotime($booking['booking_date']);
			//Add custom booking data
			if( !empty($custom['meta_key']) && $custom['meta_key'] == 'booking_custom' && is_serialized($custom['meta_value']) ){
				$this->custom = unserialize($custom['meta_value']);
			}
		}
		//Do it here so things appear in the po file.
		$this->status_array = array(
			0 => __('Pending','dbem'),
			1 => __('Approved','dbem'),
			2 => __('Rejected','dbem'),
			3 => __('Cancelled','dbem'),
			4 => __('Awaiting Online Payment','dbem'),
			5 => __('Awaiting Payment','dbem')
		);
		do_action('em_booking', $this, $booking_data);
	}
	
	/**
	 * Saves the booking into the database, whether a new or existing booking
	 * @param $mail whether or not to email the user and contact people
	 * @return boolean
	 */
	function save($mail = true){
		global $wpdb;
		$table = EM_BOOKINGS_TABLE;
		do_action('em_booking_save_pre',$this);
		if( $this->validate() ){
			if( $this->can_manage() ){
				$this->person_id = $this->get_person()->ID;			
				//Step 1. Save the booking
				$data = $this->to_array();
				$data['booking_meta'] = serialize($data['booking_meta']);
				if($this->id != ''){
					$update = true;
					//update price and spaces
					$this->get_spaces(true);
					$this->get_price(true);
					$where = array( 'booking_id' => $this->id );  
					$result = $wpdb->update($table, $data, $where, $this->get_types($data));
					$result = ($result !== false);
					$this->feedback_message = __('Changes saved','dbem');
				}else{
					$update = false;
					$result = $wpdb->insert($table, $data, $this->get_types($data));
				    $this->id = $wpdb->insert_id;  
					$this->feedback_message = __('Your booking has been recorded','dbem'); 
				}
				//Step 2. Insert ticket bookings for this booking id if no errors so far
				if( $result === false ){
					$this->feedback_message = __('There was a problem saving the booking.', 'dbem');
					$this->errors[] = __('There was a problem saving the booking.', 'dbem');
				}else{
					$tickets_bookings_result = $this->get_tickets_bookings()->save();
					if( !$tickets_bookings_result ){
						if( !$update ){
							//delete the booking and tickets, instead of a transaction
							$this->delete();
						}
						$this->errors[] = __('There was a problem saving the booking.', 'dbem');
						$this->add_error( $this->get_tickets_bookings()->get_errors() );
					}
				}
				//Step 3. email if necessary
				if ( count($this->errors) == 0  && $mail ) {
					$this->email();
				}
				return apply_filters('em_booking_save', ( count($this->errors) == 0 ), $this);
			}else{
				$this->feedback_message = __('There was a problem saving the booking.', 'dbem');
				if( !$this->can_manage() ){
					$this->feedback_message = sprintf(__('You cannot manage this %s.', 'dbem'),__('Booking','dbem'));
				}
			}
		}else{
			$this->feedback_message = __('There was a problem saving the booking.', 'dbem');
			if( !$this->can_manage() ){
				$this->feedback_message = sprintf(__('You cannot manage this %s.', 'dbem'),__('Booking','dbem'));
			}
		}
		return apply_filters('em_booking_save', false, $this);
	}
	
	/**
	 * Load an record into this object by passing an associative array of table criterie to search for. 
	 * Returns boolean depending on whether a record is found or not. 
	 * @param $search
	 * @return boolean
	 */
	function get($search) {
		global $wpdb;
		$conds = array(); 
		foreach($search as $key => $value) {
			if( array_key_exists($key, $this->fields) ){
				$value = $wpdb->escape($value);
				$conds[] = "`$key`='$value'";
			} 
		}
		$sql = "SELECT * FROM ". $wpdb->EM_BOOKINGS_TABLE ." WHERE " . implode(' AND ', $conds) ;
		$result = $wpdb->get_row($sql, ARRAY_A);
		if($result){
			$this->to_object($result);
			$this->person = new EM_Person($this->person_id);
			return true;	
		}else{
			return false;
		}
	}
	
	/**
	 * Get posted data and save it into the object (not db)
	 * @return boolean
	 */
	function get_post( $override_availability = false ){
		$this->tickets_bookings = new EM_Tickets_Bookings($this->id);
		do_action('em_booking_get_post_pre',$this);
		$result = array();
		$this->event_id = $this->get_event()->id;
		if( isset($_REQUEST['em_tickets']) && is_array($_REQUEST['em_tickets']) && ($_REQUEST['em_tickets'] || $override_availability) ){
			foreach( $_REQUEST['em_tickets'] as $ticket_id => $values){
				//make sure ticket exists
				if( !empty($values['spaces']) || $override_availability ){
					$args = array('ticket_id'=>$ticket_id, 'ticket_booking_spaces'=>$values['spaces'], 'booking_id'=>$this->id);
					if($this->get_event()->get_bookings()->ticket_exists($ticket_id)){
							$EM_Ticket_Booking = new EM_Ticket_Booking($args);
							$EM_Ticket_Booking->booking = $this;
							$this->tickets_bookings->add( $EM_Ticket_Booking, $override_availability );
					}else{
						$this->errors[]=__('You are trying to book a non-existent ticket for this event.','dbem');
					}
				}
			}
			$this->comment = (!empty($_REQUEST['booking_comment'])) ? wp_kses_data(stripslashes($_REQUEST['booking_comment'])):'';
			$this->get_spaces(true);
			$this->get_price(true);
			$this->get_person();
		}
		return apply_filters('em_booking_get_post',$this->validate(),$this);
	}
	
	function validate(){
		//step 1, basic info
		$basic = ( 
			(empty($this->event_id) || is_numeric($this->event_id)) && 
			(empty($this->person_id) || is_numeric($this->person_id)) &&
			is_numeric($this->spaces) && $this->spaces > 0
		);
		//give some errors in step 1
		if( $this->spaces == 0 ){
			$this->add_error(get_option('dbem_booking_feedback_min_space'));
		}
		//step 2, tickets bookings info
		if( count($this->get_tickets_bookings()) > 0 ){
			$ticket_validation = array();
			foreach($this->get_tickets_bookings()->tickets_bookings as $EM_Ticket_Booking){
				if ( !$EM_Ticket_Booking->validate() ){
					$ticket_validation[] = false;
					$result = $basic && !in_array(false,$ticket_validation);
				}
				$this->errors = array_merge($this->errors, $EM_Ticket_Booking->get_errors());
			}
			$result = $basic && !in_array(false,$ticket_validation);
		}else{
			$result = false;
		}
		
		return apply_filters('em_booking_validate',$result,$this);
	}
	
	/**
	 * Get the total number of spaces booked in THIS booking. Seting $force_refresh to true will recheck spaces, even if previously done so.
	 * @param unknown_type $force_refresh
	 * @return mixed
	 */
	function get_spaces( $force_refresh=false ){
		if($this->spaces == 0 || $force_refresh == true ){
			$this->spaces = $this->get_tickets_bookings()->get_spaces($force_refresh);
		}
		return apply_filters('em_booking_get_spaces',$this->spaces,$this);
	}
	
	/**
	 * Gets the total price for this whole booking. Seting $force_reset to true will recheck spaces, even if previously done so.
	 * @param boolean $force_refresh
	 * @return float
	 */
	function get_price( $force_refresh=false, $format=false ){
		if($force_refresh || $this->price == 0){
			$this->price = $this->get_tickets_bookings()->get_price($force_refresh);
		}
		if($format){
			return apply_filters('em_booking_get_price', em_get_currency_symbol().number_format($this->price,2),$this);
		}
		return apply_filters('em_booking_get_price',$this->price,$this);
	}
	
	/**
	 * Gets the event this booking belongs to and saves a refernece in the event property
	 * @return EM_Event
	 */
	function get_event(){
		global $EM_Event;
		if( is_object($this->event) && get_class($this->event)=='EM_Event' && $this->event->id == $this->event_id ){
			return $this->event;
		}elseif( is_object($EM_Event) && ( (is_object($this->event) &&$this->event->id == $this->event_id) || empty($this->id)) ){
			$this->event = $EM_Event;
		}else{
			$this->event = new EM_Event($this->event_id);
		}
		return apply_filters('em_booking_get_event',$this->event);
	}
	
	/**
	 * Outdated, use booking meta array. Get custom fields for this booking.
	 * @return array
	 */
	function get_custom(){
		global $wpdb;
		if( count($this->custom) == 0 ){
			$sql = "SELECT * FROM ". EM_META_TABLE ." WHERE object_id ='{$this->id}' AND (meta_key='booking_custom' OR meta_key IS NULL)";
			$booking = $wpdb->get_row($sql, ARRAY_A);
			//Add custom booking data
			if( !empty($booking['meta_key']) && $booking['meta_key'] == 'booking_custom' && is_serialized($booking['meta_value']) ){
				$this->custom = unserialize($booking['meta_value']);
			}			
		}
		return $this->custom;
	}
	
	/**
	 * Gets the ticket object this booking belongs to, saves a reference in ticket property
	 * @return EM_Tickets
	 */
	function get_tickets(){
		if( is_object($this->tickets) && get_class($this->tickets)=='EM_Tickets' ){
			return apply_filters('em_booking_get_tickets', $this->tickets, $this);
		}else{
			$this->tickets = new EM_Tickets($this);
		}
		return apply_filters('em_booking_get_tickets', $this->tickets, $this);
	}
	
	/**
	 * Gets the ticket object this booking belongs to, saves a reference in ticket property
	 * @return EM_Tickets_Bookings
	 */
	function get_tickets_bookings(){
		global $wpdb;
		if( !is_object($this->tickets_bookings) || get_class($this->tickets_bookings)!='EM_Tickets_Bookings'){
			$this->tickets_bookings = new EM_Tickets_Bookings($this);
		}
		return apply_filters('em_booking_get_tickets_bookings', $this->tickets_bookings, $this);
	}
	
	function get_person(){
		global $EM_Person;
		if( is_object($this->person) && get_class($this->person)=='EM_Person' && ($this->person->ID == $this->person_id || empty($this->person_id) ) ){
			return apply_filters('em_booking_get_person', $this->person, $this);
		}elseif( is_object($EM_Person) && ($EM_Person->ID === $this->person_id || $this->id == '') ){
			$this->person = $EM_Person;
		}elseif( is_numeric($this->person_id) ){
			$this->person = new EM_Person($this->person_id);
		}else{
			$this->person = new EM_Person(0);
		}
		return apply_filters('em_booking_get_person', $this->person, $this);
	}

	/**
	 * Returns a string representation of the booking's status
	 * @return string
	 */
	function get_status(){
		$status = ($this->status == 0 && !get_option('dbem_bookings_approval') ) ? 1:$this->status;
		return $this->status_array[$status];
	}
	
	/**
	 * I wonder what this does....
	 * @return boolean
	 */
	function delete(){
		global $wpdb;
		//FIXME ticket logic needed
		$sql = $wpdb->prepare("DELETE FROM ". EM_BOOKINGS_TABLE . " WHERE booking_id=%d", $this->id);
		$result = $wpdb->query( $sql );
		if( $result !== false ){
			//delete the tickets too
			$this->get_tickets_bookings()->delete();
			$this->previous_status = $this->status;
			$this->status = false;
			$this->feedback_message = sprintf(__('%s deleted', 'dbem'), __('Booking','dbem'));
		}else{
			$this->add_error(sprintf(__('%s could not be deleted', 'dbem'), __('Booking','dbem')));
		}
		return apply_filters('em_booking_delete',( $result !== false ), $this);
	}
	
	function cancel(){
		if( $this->person->ID == get_current_user_id() ){
			$this->manage_override = true; //normally, users can't manage a bookiing, only event owners, so we allow them to mod their booking status in this case only.
		}
		return $this->set_status(3);
	}
	
	/**
	 * Approve a booking.
	 * @return bool
	 */
	function approve(){
		return $this->set_status(1);
	}	
	/**
	 * Reject a booking and save
	 * @return bool
	 */
	function reject(){
		return $this->set_status(2);
	}	
	/**
	 * Unpprove a booking.
	 * @return bool
	 */
	function unapprove(){
		return $this->set_status(0);
	}
	
	/**
	 * Change the status of the booking. This will save to the Database too. 
	 * @param int $status
	 * @return boolean
	 */
	function set_status($status){
		$action_string = strtolower($this->status_array[$status]); 
		//if we're approving we can't approve a booking if spaces are full, so check before it's approved.
		if($status == 1){
			if( $this->get_event()->get_bookings()->get_available_spaces() < $this->get_spaces() && !get_option('dbem_bookings_approval_overbooking') ){
				$this->feedback_message = sprintf(__('Not approved, spaces full.','dbem'), $action_string);
				return apply_filters('em_booking_set_status', false, $this);
			}
		}
		$this->previous_status = $this->status;
		$this->status = $status;
		$result = $this->save(false);
		if($result){
			$this->event = new EM_Event($this->event_id); //force a refresh of event object
			$this->feedback_message = sprintf(__('Booking %s.','dbem'), $action_string);
			if( !($this->status == 0 && $this->previous_status > 0) || $this->previous_status == 4 ){
				if( $this->email() ){
					$this->feedback_message .= " ".__('Mail Sent.','dbem');
				}elseif( $this->previous_status == 0 ){
					//extra errors may be logged by email() in EM_Object
					$this->feedback_message .= ' <span style="color:red">'.__('ERROR : Mail Not Sent.','dbem').'</span>';
					$this->add_error(__('ERROR : Mail Not Sent.','dbem'));
					$result =  false;
				}
			}
		}else{
			//errors should be logged by save()
			$this->feedback_message = sprintf(__('Booking could not be %s.','dbem'), $action_string);
			$this->add_error(sprintf(__('Booking could not be %s.','dbem'), $action_string));
		}
		return apply_filters('em_booking_set_status', $result, $this);
	}
	
	/**
	 * Add a booking note to this booking. returns wpdb result or false if use can't manage this event.
	 * @param string $note
	 * @return mixed
	 */
	function add_note( $note_text ){
		global $wpdb;
		if( $this->can_manage() ){
			$note = array('author'=>get_current_user_id(),'note'=>$note_text,'timestamp'=>current_time('timestamp'));
			$this->notes[] = $note;
			$this->feedback_message = __('Booking note successfully added.','dbem');
			return $wpdb->insert(EM_META_TABLE, array('object_id'=>$this->id, 'meta_key'=>'booking-note', 'meta_value'=> serialize($note)),array('%d','%s','%s'));
		}
		return false;
	}
	
	/**
	 * @param EM_Booking $EM_Booking
	 * @param EM_Event $event
	 * @return boolean
	 */
	function email(){
		global $EM_Mailer;
		//FIXME ticket logic needed
		$EM_Event = $this->get_event(); //We NEED event details here.
		//Make sure event matches booking, and that booking used to be approved.
		if( !($this->status == 0 && $this->previous_status > 0) || $this->previous_status == 4 ){
			$contact_id = ( $EM_Event->owner != "") ? $EM_Event->owner : get_option('dbem_default_contact_person');
	
			$contact_subject = get_option('dbem_bookings_contact_email_subject');
			$contact_body = get_option('dbem_bookings_contact_email_body');
			
			if( (get_option('dbem_bookings_approval') == 0 && $this->status < 2) || $this->status == 1 ){
				$booker_subject = get_option('dbem_bookings_email_confirmed_subject');
				$booker_body = get_option('dbem_bookings_email_confirmed_body');
			}elseif( $this->status == 0 || $this->status == 5 || ( $this->status == 0 && ($this->previous_status == 4 || $this->previous_status == 5) )  ){
				$booker_subject = get_option('dbem_bookings_email_pending_subject');
				$booker_body = get_option('dbem_bookings_email_pending_body');
			}elseif( $this->status == 2 ){
				$booker_subject = get_option('dbem_bookings_email_rejected_subject');
				$booker_body = get_option('dbem_bookings_email_rejected_body');
			}elseif( $this->status == 3 ){
				$booker_subject = get_option('dbem_bookings_email_cancelled_subject');
				$booker_body = get_option('dbem_bookings_email_cancelled_body');
				$contact_subject = get_option('dbem_contactperson_email_cancelled_subject');
				$contact_body = get_option('dbem_contactperson_email_cancelled_body');
			}else{
				return true;
			}
			
			// email specific placeholders
			foreach( $this->get_tickets() as $EM_Ticket){ break; }
			ob_start();
			em_locate_template('emails/bookingtickets.php', true, array('EM_Booking'=>$this));
			$tickets = ob_get_clean();
			$placeholders = apply_filters('em_booking_email_placeholders', array(
				'#_RESPNAME' =>  '#_BOOKINGNAME',//Depreciated
				'#_RESPEMAIL' => '#_BOOKINGEMAIL',//Depreciated
				'#_RESPPHONE' => '#_BOOKINGPHONE',//Depreciated
				'#_COMMENT' => '#_BOOKINGCOMMENT',//Depreciated
				'#_RESERVEDSPACES' => '#_BOOKEDSPACES',//Depreciated
				'#_BOOKINGID' =>  $this->id,
				'#_BOOKINGNAME' =>  $this->person->get_name(),
				'#_BOOKINGEMAIL' => $this->person->user_email,
				'#_BOOKINGPHONE' => $this->person->phone,
				'#_BOOKINGSPACES' => $this->get_spaces(),
				'#_BOOKINGLISTURL' => em_get_my_bookings_url(),
				'#_BOOKINGCOMMENT' => $this->comment,
				'#_BOOKINGTICKETNAME' => $EM_Ticket->name,
				'#_BOOKINGTICKETDESCRIPTION' => $EM_Ticket->description,
				'#_BOOKINGTICKETPRICE' => em_get_currency_symbol(true)." ". number_format($EM_Ticket->get_price(),2),
				'#_BOOKINGTICKETS' => $tickets
			),$this);	 
			foreach($placeholders as $key => $value) {
				$contact_subject = str_replace($key, $value, $contact_subject);
				$contact_body = str_replace($key, $value, $contact_body); 
				$booker_subject = str_replace($key, $value, $booker_subject); 
				$booker_body = str_replace($key, $value, $booker_body);
			}
			$booker_subject = $EM_Event->output($booker_subject, 'email'); 
			$booker_body = $EM_Event->output($booker_body, 'email');
						
			//Send to the person booking
			if( !$this->email_send( $booker_subject,$booker_body, $this->person->user_email) ){
				return false;
			}
			
			//Send admin/contact emails
			if( (get_option('dbem_bookings_approval') == 0 || in_array($this->status, array(0,3,4,5)) || (in_array($this->previous_status, array(4)) && $this->status == 1)) && (get_option('dbem_bookings_contact_email') == 1 || get_option('dbem_bookings_notify_admin') != '') ){
				//Only gets sent if this is a pending booking, unless approvals are disabled.
				$contact_subject = $EM_Event->output($contact_subject, 'email');
				$contact_body = $EM_Event->output($contact_body, 'email');
				
				if( get_option('dbem_bookings_contact_email') == 1 ){
					if( !$this->email_send( $contact_subject, $contact_body, $EM_Event->contact->user_email) && current_user_can('activate_plugins')){
						$this->errors[] = __('Confirmation email could not be sent to contact person. Registrant should have gotten their email (only admin see this warning).','dbem');
						return false;
					}
				}
		
				if( get_option('dbem_bookings_notify_admin') != '' && preg_match('/^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3}$/', get_option('dbem_bookings_notify_admin')) ){
					if( !$this->email_send( $contact_subject, $contact_body, get_option('dbem_bookings_notify_admin')) ){
						$this->errors[] = __('Confirmation email could not be sent to admin. Registrant should have gotten their email (only admin see this warning).','dbem');
						return false;
					}
				}
			}
			return true;
		}
		return false;
		//TODO need error checking for booking mail send
	}	
	
	/**
	 * Can the user manage this event? 
	 */
	function can_manage(){
		return $this->get_event()->can_manage('manage_bookings','manage_others_bookings') || empty($this->id) || !empty($this->manage_override);
	}
	
	/**
	 * Returns this object in the form of an array
	 * @return array
	 */
	function to_array($person = false){
		$booking = array();
		//Core Data
		$booking = parent::to_array();
		//Person Data
		if($person && is_object($this->person)){
			$person = $this->person->to_array();
			$booking = array_merge($booking, $person);
		}
		return $booking;
	}
}
?>