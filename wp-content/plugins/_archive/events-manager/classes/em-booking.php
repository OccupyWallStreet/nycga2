<?php
class EM_Booking extends EM_Object{
	//DB Fields
	var $booking_id;
	var $event_id;
	var $person_id;
	var $booking_price;
	var $booking_spaces;
	var $booking_comment;
	var $booking_status = false;
	var $booking_meta = array();
	var $fields = array(
		'booking_id' => array('name'=>'id','type'=>'%d'),
		'event_id' => array('name'=>'event_id','type'=>'%d'),
		'person_id' => array('name'=>'person_id','type'=>'%d'),
		'booking_price' => array('name'=>'price','type'=>'%f'),
		'booking_spaces' => array('name'=>'spaces','type'=>'%d'),
		'booking_comment' => array('name'=>'comment','type'=>'%s'),
		'booking_status' => array('name'=>'status','type'=>'%d'),
		'booking_meta' => array('name'=>'meta','type'=>'%s')
	);
	//Other Vars
	var $notes; //loaded from em_meta table in construct
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
	var $previous_status = false;
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
		global $wpdb;
	  	if( $booking_data !== false ){
			//Load booking data
			$booking = array();
			if( is_array($booking_data) ){
				$booking = $booking_data;
			}elseif( is_numeric($booking_data) ){
				//Retreiving from the database				
				$sql = "SELECT * FROM ". EM_BOOKINGS_TABLE ." LEFT JOIN ". EM_META_TABLE ." ON object_id=booking_id WHERE booking_id ='$booking_data'";
				$booking = $wpdb->get_row($sql, ARRAY_A);
			}
			//booking meta
			$booking['booking_meta'] = (!empty($booking['booking_meta'])) ? unserialize($booking['booking_meta']):array();
			//Save into the object
			$this->to_object($booking);
			$this->previous_status = $this->booking_status;
			$this->get_person();
			$this->timestamp = !empty($booking['booking_date']) ? strtotime($booking['booking_date']):false;
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
		$this->compat_keys();
		do_action('em_booking', $this, $booking_data);
	}
	
	function get_notes(){
		global $wpdb;
		if( !is_array($this->notes) && !empty($this->booking_id) ){
		  	$notes = $wpdb->get_results("SELECT * FROM ". EM_META_TABLE ." WHERE meta_key='booking-note' AND object_id ='{$this->booking_id}'", ARRAY_A);
		  	$this->notes = array();
		  	foreach($notes as $note){
		  		$this->notes[] = unserialize($note['meta_value']);
		  	}
		}elseif( empty($this->booking_id) ){
			$this->notes = array();
		}
		return $this->notes;
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
				$this->person_id = (empty($this->person_id)) ? $this->get_person()->ID : $this->person_id;			
				//Step 1. Save the booking
				$data = $this->to_array();
				$data['booking_meta'] = serialize($data['booking_meta']);
				if($this->booking_id != ''){
					$update = true;
					//update price and spaces
					$this->get_spaces(true);
					$this->get_price(true);
					$where = array( 'booking_id' => $this->booking_id );  
					$result = $wpdb->update($table, $data, $where, $this->get_types($data));
					$result = ($result !== false);
					$this->feedback_message = __('Changes saved','dbem');
				}else{
					$update = false;
					$result = $wpdb->insert($table, $data, $this->get_types($data));
				    $this->booking_id = $wpdb->insert_id;  
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
				$this->compat_keys();
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
		$this->tickets_bookings = new EM_Tickets_Bookings($this->booking_id);
		do_action('em_booking_get_post_pre',$this);
		$result = array();
		$this->event_id = $_REQUEST['event_id'];
		if( isset($_REQUEST['em_tickets']) && is_array($_REQUEST['em_tickets']) && ($_REQUEST['em_tickets'] || $override_availability) ){
			foreach( $_REQUEST['em_tickets'] as $ticket_id => $values){
				//make sure ticket exists
				if( !empty($values['spaces']) || $override_availability ){
					$args = array('ticket_id'=>$ticket_id, 'ticket_booking_spaces'=>$values['spaces'], 'booking_id'=>$this->booking_id);
					if($this->get_event()->get_bookings()->ticket_exists($ticket_id)){
							$EM_Ticket_Booking = new EM_Ticket_Booking($args);
							$EM_Ticket_Booking->booking = $this;
							$this->tickets_bookings->add( $EM_Ticket_Booking, $override_availability );
					}else{
						$this->errors[]=__('You are trying to book a non-existent ticket for this event.','dbem');
					}
				}
			}
			$this->booking_comment = (!empty($_REQUEST['booking_comment'])) ? wp_kses_data(stripslashes($_REQUEST['booking_comment'])):'';
			$this->get_spaces(true);
			$this->get_price(true, false, false);
			$this->get_person();
			$this->compat_keys();
		}
		return apply_filters('em_booking_get_post',$this->validate(),$this);
	}
	
	function validate(){
		//step 1, basic info
		$basic = ( 
			(empty($this->event_id) || is_numeric($this->event_id)) && 
			(empty($this->person_id) || is_numeric($this->person_id)) &&
			is_numeric($this->booking_spaces) && $this->booking_spaces > 0
		);
		//give some errors in step 1
		if( $this->booking_spaces == 0 ){
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
		if($this->booking_spaces == 0 || $force_refresh == true ){
			$this->booking_spaces = $this->get_tickets_bookings()->get_spaces($force_refresh);
		}
		return apply_filters('em_booking_get_spaces',$this->booking_spaces,$this);
	}
	
	/**
	 * Gets the total price for this whole booking. Seting $force_reset to true will recheck spaces, even if previously done so.
	 * @param boolean $force_refresh
	 * @param boolean $format
	 * @param boolean $add_tax
	 * @return float
	 */
	function get_price( $force_refresh=false, $format=false, $add_tax='x' ){
		if($force_refresh || $this->booking_price == 0 || $add_tax !== 'x' || get_option('dbem_bookings_tax_auto_add')){
			$this->booking_price = $this->get_tickets_bookings()->get_price($force_refresh, false, $add_tax);
			$this->booking_price = apply_filters('em_booking_get_price', $this->booking_price, $this, $add_tax);
		}
		if($format){
			return em_get_currency_formatted($this->booking_price);
		}
		return $this->booking_price;
	}
	
	/**
	 * Gets the event this booking belongs to and saves a refernece in the event property
	 * @return EM_Event
	 */
	function get_event(){
		global $EM_Event;
		if( is_object($this->event) && get_class($this->event)=='EM_Event' && $this->event->event_id == $this->event_id ){
			return $this->event;
		}elseif( is_object($EM_Event) && ( (is_object($this->event) && $this->event->event_id == $this->event_id) || empty($this->booking_id)) ){
			$this->event = $EM_Event;
		}else{
			$this->event = new EM_Event($this->event_id, 'event_id');
		}
		return apply_filters('em_booking_get_event',$this->event);
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
			//This person is already included, so don't do anything
		}elseif( is_object($EM_Person) && ($EM_Person->ID === $this->person_id || $this->booking_id == '') ){
			$this->person = $EM_Person;
		}elseif( is_numeric($this->person_id) ){
			$this->person = new EM_Person($this->person_id);
		}else{
			$this->person = new EM_Person(0);
		}
		//if this user is the parent user of disabled registrations, replace user details here:
		if( get_option('dbem_bookings_registration_disable') && $this->person->ID == get_option('dbem_bookings_registration_user') ){
			//override any registration data into the person objet
			if( !empty($this->booking_meta['registration']) ){
				foreach($this->booking_meta['registration'] as $key => $value){
					$this->person->$key = $value;
				}
			}
			$this->person->user_email = ( !empty($this->booking_meta['registration']['user_email']) ) ? $this->booking_meta['registration']['user_email']:$this->person->user_email;
			if( !empty($this->booking_meta['registration']['user_name']) ){
				$name_string = explode(' ',$this->booking_meta['registration']['user_name']); 
				$this->booking_meta['registration']['first_name'] = array_shift($name_string);
				$this->booking_meta['registration']['last_name'] = implode(' ', $name_string);
			}
			$this->person->user_firstname = ( !empty($this->booking_meta['registration']['first_name']) ) ? $this->booking_meta['registration']['first_name']:__('Guest User','dbem');
			$this->person->first_name = $this->person->user_firstname;
			$this->person->user_lastname = ( !empty($this->booking_meta['registration']['last_name']) ) ? $this->booking_meta['registration']['last_name']:'';
			$this->person->last_name = $this->person->user_lastname;
			$this->person->phone = ( !empty($this->booking_meta['registration']['dbem_phone']) ) ? $this->booking_meta['registration']['dbem_phone']:__('Not Supplied','dbem');
			//build display name
			$full_name = $this->person->user_firstname  . " " . $this->person->user_lastname ;
			$full_name = trim($full_name);
			$display_name = ( empty($full_name) ) ? __('Guest User','dbem'):$full_name;
			$this->person->display_name = $display_name;
		}
		return apply_filters('em_booking_get_person', $this->person, $this);
	}

	/**
	 * Returns a string representation of the booking's status
	 * @return string
	 */
	function get_status(){
		$status = ($this->booking_status == 0 && !get_option('dbem_bookings_approval') ) ? 1:$this->booking_status;
		return apply_filters('em_booking_get_status', $this->status_array[$status], $this);
	}
	
	/**
	 * I wonder what this does....
	 * @return boolean
	 */
	function delete(){
		global $wpdb;
		$result = false;
		if( $this->can_manage('manage_bookings','manage_others_bookings') ){
			$sql = $wpdb->prepare("DELETE FROM ". EM_BOOKINGS_TABLE . " WHERE booking_id=%d", $this->booking_id);
			$result = $wpdb->query( $sql );
			if( $result !== false ){
				//delete the tickets too
				$this->get_tickets_bookings()->delete();
				$this->previous_status = $this->booking_status;
				$this->booking_status = false;
				$this->feedback_message = sprintf(__('%s deleted', 'dbem'), __('Booking','dbem'));
			}else{
				$this->add_error(sprintf(__('%s could not be deleted', 'dbem'), __('Booking','dbem')));
			}
		}
		return apply_filters('em_booking_delete',( $result !== false ), $this);
	}
	
	function cancel($email = true){
		if( $this->person->ID == get_current_user_id() ){
			$this->manage_override = true; //normally, users can't manage a bookiing, only event owners, so we allow them to mod their booking status in this case only.
		}
		return $this->set_status(3, $email);
	}
	
	/**
	 * Approve a booking.
	 * @return bool
	 */
	function approve($email = true, $ignore_spaces = false){
		return $this->set_status(1, $email, $ignore_spaces);
	}	
	/**
	 * Reject a booking and save
	 * @return bool
	 */
	function reject($email = true){
		return $this->set_status(2, $email);
	}	
	/**
	 * Unpprove a booking.
	 * @return bool
	 */
	function unapprove($email = true){
		return $this->set_status(0, $email);
	}
	
	/**
	 * Change the status of the booking. This will save to the Database too. 
	 * @param int $status
	 * @return boolean
	 */
	function set_status($status, $email = true, $ignore_spaces = false){
		global $wpdb;
		$action_string = strtolower($this->status_array[$status]); 
		//if we're approving we can't approve a booking if spaces are full, so check before it's approved.
		if(!$ignore_spaces && $status == 1){
			if( $this->get_event()->get_bookings()->get_available_spaces() < $this->get_spaces() && !get_option('dbem_bookings_approval_overbooking') ){
				$this->feedback_message = sprintf(__('Not approved, spaces full.','dbem'), $action_string);
				return apply_filters('em_booking_set_status', false, $this);
			}
		}
		$this->previous_status = $this->booking_status;
		$this->booking_status = $status;
		$result = $wpdb->query($wpdb->prepare('UPDATE '.EM_BOOKINGS_TABLE.' SET booking_status=%d WHERE booking_id=%d', array($status, $this->booking_id)));
		if($result !== false){
			$this->feedback_message = sprintf(__('Booking %s.','dbem'), $action_string);
			if( $email ){
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
			$this->get_notes();
			$note = array('author'=>get_current_user_id(),'note'=>$note_text,'timestamp'=>current_time('timestamp'));
			$this->notes[] = $note;
			$this->feedback_message = __('Booking note successfully added.','dbem');
			return $wpdb->insert(EM_META_TABLE, array('object_id'=>$this->booking_id, 'meta_key'=>'booking-note', 'meta_value'=> serialize($note)),array('%d','%s','%s'));
		}
		return false;
	}
	
	function output($format, $target="html") {
	 	preg_match_all("/(#@?_?[A-Za-z0-9]+)({([^}]+)})?/", $format, $placeholders);
		foreach( $this->get_tickets() as $EM_Ticket){ break; } //Get first ticket for single ticket placeholders
		$output_string = $format;
		$replaces = array();
		foreach($placeholders[1] as $key => $result) {
			$replace = '';
			$full_result = $placeholders[0][$key];		
			switch( $result ){
				case '#_BOOKINGID':
					$replace = $this->booking_id;
					break;
				case '#_RESPNAME' : //Depreciated
				case '#_BOOKINGNAME':
					$replace = $this->get_person()->get_name();
					break;
				case '#_RESPEMAIL' : //Depreciated
				case '#_BOOKINGEMAIL':
					$replace = $this->get_person()->user_email;
					break;
				case '#_RESPPHONE' : //Depreciated
				case '#_BOOKINGPHONE':
					$replace = $this->get_person()->phone;
					break;
				case '#_BOOKINGSPACES':
					$replace = $this->get_spaces();
					break;
				case '#_BOOKINGLISTURL':
					$replace = em_get_my_bookings_url();
					break;
				case '#_COMMENT' : //Depreciated
				case '#_BOOKINGCOMMENT':
					$replace = $this->booking_comment;
					break;
				case '#_BOOKINGPRICEWITHTAX':
					$replace = em_get_currency_symbol(true)." ". number_format($this->get_price(false,false,true),2);
					break;
				case '#_BOOKINGPRICEWITHOUTTAX':
					$replace = em_get_currency_symbol(true)." ". number_format($this->get_price(false,false,false),2);
					break;
				case '#_BOOKINGPRICETAX':
					$replace = em_get_currency_symbol(true)." ". number_format($this->get_price(false,false,false)*(get_option('dbem_bookings_tax')/100),2);
					break;
				case '#_BOOKINGPRICE':
					$replace = em_get_currency_symbol(true)." ". number_format($this->get_price(),2);
					break;
				case '#_BOOKINGTICKETNAME':
					$replace = $EM_Ticket->name;
					break;
				case '#_BOOKINGTICKETDESCRIPTION':
					$replace = $EM_Ticket->description;
					break;
				case '#_BOOKINGTICKETPRICEWITHTAX':
					$replace = em_get_currency_symbol(true)." ". number_format($EM_Ticket->get_price(false,true),2);
					break;
				case '#_BOOKINGTICKETPRICEWITHOUTTAX':
					$replace = em_get_currency_symbol(true)." ". number_format($EM_Ticket->get_price(false,false),2);
					break;
				case '#_BOOKINGTICKETTAX':
					$replace = em_get_currency_symbol(true)." ". number_format($EM_Ticket->get_price(false,false)*(get_option('dbem_bookings_tax')/100),2);
					break;
				case '#_BOOKINGTICKETPRICE':
					$replace = em_get_currency_symbol(true)." ". number_format($EM_Ticket->get_price(),2);
					break;
				case '#_BOOKINGTICKETS':
					ob_start();
					em_locate_template('emails/bookingtickets.php', true, array('EM_Booking'=>$this));
					$replace = ob_get_clean();
					break;
				default:
					$replace = $full_result;
					break;
			}
			$replaces[$full_result] = apply_filters('em_booking_output_placeholder', $replace, $this, $full_result, $target);
		}
		//sort out replacements so that during replacements shorter placeholders don't overwrite longer varieties.
		krsort($replaces);
		foreach($replaces as $full_result => $replacement){
			$output_string = str_replace($full_result, $replacement , $output_string );
		}
		//run event output too, since this is never run from within events and will not infinitely loop
		$output_string = $this->get_event()->output($output_string, $target);
		return apply_filters('em_booking_output', $output_string, $this, $format, $target);	
	}
	
	/**
	 * @param EM_Booking $EM_Booking
	 * @param EM_Event $event
	 * @return boolean
	 */
	function email( $email_admin = true, $force_resend = false ){
		global $EM_Mailer;
		$result = true;
		
		//FIXME ticket logic needed
		$EM_Event = $this->get_event(); //We NEED event details here.
		$EM_Event->get_bookings(true); //refresh all bookings
		
		//Make sure event matches booking, and that booking used to be approved.
		if( $this->booking_status !== $this->previous_status || $force_resend ){
			$msg = array( 'user'=> array('subject'=>'', 'body'=>''), 'admin'=> array('subject'=>'', 'body'=>'')); //blank msg template
			
			//admin messages won't change whether pending or already approved			
			switch( $this->booking_status ){
				case 0:
				case 5: //TODO remove offline status from here and move to pro
					$msg['user']['subject'] = get_option('dbem_bookings_email_pending_subject');
					$msg['user']['body'] = get_option('dbem_bookings_email_pending_body');
					//admins should get something (if set to)
					$msg['admin']['subject'] = get_option('dbem_bookings_contact_email_subject');
					$msg['admin']['body'] = get_option('dbem_bookings_contact_email_body');
					break;
				case 1:
					$msg['user']['subject'] = get_option('dbem_bookings_email_confirmed_subject');
					$msg['user']['body'] = get_option('dbem_bookings_email_confirmed_body');
					//admins should get something (if set to)
					$msg['admin']['subject'] = get_option('dbem_bookings_contact_email_subject');
					$msg['admin']['body'] = get_option('dbem_bookings_contact_email_body');
					break;
				case 2:
					$msg['user']['subject'] = get_option('dbem_bookings_email_rejected_subject');
					$msg['user']['body'] = get_option('dbem_bookings_email_rejected_body');
					break;
				case 3:
					$msg['user']['subject'] = get_option('dbem_bookings_email_cancelled_subject');
					$msg['user']['body'] = get_option('dbem_bookings_email_cancelled_body');
					//admins should get something (if set to)
					$msg['admin']['subject'] = get_option('dbem_contactperson_email_cancelled_subject');
					$msg['admin']['body'] = get_option('dbem_contactperson_email_cancelled_body');
					break;
			}
			//messages can be overriden just before being sent
			$msg = apply_filters('em_booking_email_messages', $msg, $this);
			$output_type = get_option('dbem_smtp_html') ? 'html':'email';

			//Send user (booker) emails
			if( !empty($msg['user']['subject']) ){
				$msg['user']['subject'] = $this->output($msg['user']['subject'], $output_type);
				$msg['user']['body'] = $this->output($msg['user']['body'], $output_type);
				if( get_option('dbem_smtp_html') && get_option('dbem_smtp_html_br', 1) ){
					$msg['user']['body'] = nl2br($msg['user']['body']);
				}
				//Send to the person booking
				if( !$this->email_send( $msg['user']['subject'], $msg['user']['body'], $this->get_person()->user_email) ){
					$result = false;
				}
			}
			
			//Send admin/contact emails if this isn't the event owner or an events admin
			if( $email_admin && !empty($msg['admin']['subject']) && (!$this->can_manage() || (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'booking_add') || $this->manage_override) ){ //emails won't be sent if admin is logged in unless they book themselves
				if( get_option('dbem_bookings_contact_email') == 1 || get_option('dbem_bookings_notify_admin') ){
					//Only gets sent if this is a pending booking, unless approvals are disabled.
					$msg['admin']['subject'] = $this->output($msg['admin']['subject'], $output_type);
					$msg['admin']['body'] = $this->output($msg['admin']['body'], $output_type); 
					if( get_option('dbem_smtp_html') && get_option('dbem_smtp_html_br', 1) ){
						$msg['admin']['body'] = nl2br($msg['admin']['body']);
					}
					//email contact
					if( get_option('dbem_bookings_contact_email') == 1 ){
						if( !$this->email_send( $msg['admin']['subject'], $msg['admin']['body'], $EM_Event->get_contact()->user_email) && current_user_can('activate_plugins')){
							$this->errors[] = __('Confirmation email could not be sent to contact person. Registrant should have gotten their email (only admin see this warning).','dbem');
							$result = false;
						}
					}
					//email admin
					if( get_option('dbem_bookings_notify_admin') != '' && preg_match('/^([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3},?)+$/', get_option('dbem_bookings_notify_admin')) ){
						$admin_emails =  get_option('dbem_bookings_notify_admin');
						$admin_emails = explode(',', $admin_emails); //supply emails as array 
						if( !$this->email_send( $msg['admin']['subject'], $msg['admin']['body'], $admin_emails) ){
							$this->errors[] = __('Confirmation email could not be sent to admin. Registrant should have gotten their email (only admin see this warning).','dbem');
							$result = false;
						}
					}
				}
			}
		}
		return $result;
		//TODO need error checking for booking mail send
	}	
	
	/**
	 * Can the user manage this event? 
	 */
	function can_manage(){
		return $this->get_event()->can_manage('manage_bookings','manage_others_bookings') || empty($this->booking_id) || !empty($this->manage_override);
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