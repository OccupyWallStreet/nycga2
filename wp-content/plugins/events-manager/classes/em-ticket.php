<?php
class EM_Ticket extends EM_Object{
	//DB Fields
	var $ticket_id;
	var $event_id;
	var $ticket_name;
	var $ticket_description;
	var $ticket_price;
	var $ticket_start;
	var $ticket_end;
	var $ticket_min;
	var $ticket_max;
	var $ticket_spaces = 10;
	var $ticket_members = false;
	var $ticket_required = false;
	var $fields = array(
		'ticket_id' => array('name'=>'id','type'=>'%d'),
		'event_id' => array('name'=>'event_id','type'=>'%d'),
		'ticket_name' => array('name'=>'name','type'=>'%s'),
		'ticket_description' => array('name'=>'description','type'=>'%s','null'=>1),
		'ticket_price' => array('name'=>'price','type'=>'%f','null'=>1),
		'ticket_start' => array('name'=>'start','type'=>'%s','null'=>1),
		'ticket_end' => array('name'=>'end','type'=>'%s','null'=>1),
		'ticket_min' => array('name'=>'min','type'=>'%s','null'=>1),
		'ticket_max' => array('name'=>'max','type'=>'%s','null'=>1),
		'ticket_spaces' => array('name'=>'spaces','type'=>'%s','null'=>1),
		'ticket_members' => array('name'=>'members','type'=>'%d','null'=>1),
		'ticket_required' => array('name'=>'required','type'=>'%d','null'=>1)
	);
	//Other Vars
	/**
	 * Contains only bookings belonging to this ticket.
	 * @var EM_Booking
	 */
	var $bookings = array();
	var $required_fields = array('ticket_name');
	var $start_timestamp;
	var $end_timestamp;
	/**
	 * is this ticket limited by spaces allotted to this ticket? false if no limit (i.e. the events general limit of seats)
	 * @var unknown_type
	 */
	var $spaces_limit = true;
	
	/**
	 * Creates ticket object and retreives ticket data (default is a blank ticket object). Accepts either array of ticket data (from db) or a ticket id.
	 * @param mixed $ticket_data
	 * @return null
	 */
	function EM_Ticket( $ticket_data = false ){
		$this->ticket_name = __('Standard Ticket','dbem');
		$ticket = array();
		if( $ticket_data !== false ){
			//Load ticket data
			if( is_array($ticket_data) ){
				$ticket = $ticket_data;
			}elseif( is_numeric($ticket_data) ){
				//Retreiving from the database		
				global $wpdb;
				$sql = "SELECT * FROM ". EM_TICKETS_TABLE ." WHERE ticket_id ='$ticket_data'";   
			  	$ticket = $wpdb->get_row($sql, ARRAY_A);
			}
			//Save into the object
			$this->to_object($ticket);
			$this->start_timestamp = (!empty($ticket['ticket_start'])) ? strtotime($ticket['ticket_start']):false;
			$this->end_timestamp = (!empty($ticket['ticket_end'])) ? strtotime($ticket['ticket_end']):false;
		}
		$this->compat_keys();
		do_action('em_ticket',$this, $ticket_data, $ticket);
	}
	
	function get_notes(){
		global $wpdb;
		if( !is_array($this->notes) && !empty($this->ticket_id) ){
		  	$notes = $wpdb->get_results("SELECT * FROM ". EM_META_TABLE ." WHERE meta_key='ticket-note' AND object_id ='{$this->ticket_id}'", ARRAY_A);
		  	foreach($notes as $note){
		  		$this->ticket_id[] = unserialize($note['meta_value']);
		  	}
		}elseif( empty($this->booking_id) ){
			$this->notes = array();
		}
		return $this->notes;
	}
	
	/**
	 * Saves the ticket into the database, whether a new or existing ticket
	 * @return boolean
	 */
	function save(){
		global $wpdb;
		$table = EM_TICKETS_TABLE;
		do_action('em_ticket_save_pre',$this);
		//First the person
		if($this->validate() && $this->can_manage() ){			
			//Now we save the ticket
			$data = $this->to_array(true); //add the true to remove the nulls
			if($this->ticket_id != ''){
				//since currently wpdb calls don't accept null, let's build the sql ourselves.
				$set_array = array();
				foreach( $this->fields as $field_name => $field ){
					if( empty($this->$field['name']) && $field['null'] ){
						$set_array[] = "{$field_name}=NULL";
					}else{
						$set_array[] = "{$field_name}='".$wpdb->escape($this->$field['name'])."'";						
					}
				}
				$sql = "UPDATE $table SET ".implode(', ', $set_array)." WHERE ticket_id={$this->ticket_id}";
				$result = $wpdb->query($sql);
				$this->feedback_message = __('Changes saved','dbem');
			}else{
				//TODO better error handling
				$result = $wpdb->insert($table, $data, $this->get_types($data));
			    $this->ticket_id = $wpdb->insert_id;
				$this->feedback_message = __('Ticket created','dbem'); 
			}
			if( $result === false ){
				$this->feedback_message = __('There was a problem saving the ticket.', 'dbem');
				$this->errors[] = __('There was a problem saving the ticket.', 'dbem');
			}
			$this->compat_keys();
			return apply_filters('em_ticket_save', ( count($this->errors) == 0 ), $this);
		}else{
			$this->feedback_message = __('There was a problem saving the ticket.', 'dbem');
			$this->errors[] = __('There was a problem saving the ticket.', 'dbem');
			return apply_filters('em_ticket_save', false, $this);
		}
		return true;
	}
	
	/**
	 * Get posted data and save it into the object (not db)
	 * @return boolean
	 */
	function get_post($post = array()){
		//We are getting the values via POST or GET
		global $allowedposttags;
		if( empty($post) ){
		    $post = $_REQUEST;
		}
		do_action('em_location_get_post_pre', $this, $post);
		$this->ticket_id = ( !empty($post['ticket_id']) ) ? $post['ticket_id']:'';
		$this->event_id = ( !empty($post['event_id']) ) ? $post['event_id']:'';
		$this->ticket_name = ( !empty($post['ticket_name']) ) ? wp_kses_data(stripslashes($post['ticket_name'])):'';
		$this->ticket_description = ( !empty($post['ticket_description']) ) ? wp_kses(stripslashes($post['ticket_description']), $allowedposttags):'';
		$this->ticket_price = ( !empty($post['ticket_price']) ) ? $post['ticket_price']:'';
		$this->ticket_start = ( !empty($post['ticket_start']) ) ? $post['ticket_start']:'';
		$this->ticket_end = ( !empty($post['ticket_end']) ) ? $post['ticket_end']:'';
		$this->start_timestamp = ( !empty($post['ticket_start']) ) ? strtotime($post['ticket_start']):'';
		$this->end_timestamp = ( !empty($post['ticket_end']) ) ? strtotime($post['ticket_end']):'';
		$this->ticket_min = ( !empty($post['ticket_min']) ) ? $post['ticket_min']:'';
		$this->ticket_max = ( !empty($post['ticket_max']) ) ? $post['ticket_max']:'';
		$this->ticket_spaces = ( !empty($post['ticket_spaces']) ) ? $post['ticket_spaces']:10;
		$this->ticket_members = ( !empty($post['ticket_members']) ) ? 1:0;
		$this->ticket_required = ( !empty($post['ticket_required']) ) ? 1:0;
		$this->compat_keys();
		do_action('em_ticket_get_post', $this);
	}	
	

	/**
	 * Validates the ticket for saving. Should be run during any form submission or saving operation.
	 * @return boolean
	 */
	function validate(){
		$missing_fields = Array ();
		$this->errors = array();
		foreach ( $this->required_fields as $field ) {
			$true_field = $this->fields[$field]['name'];
			if ( $this->$true_field == "") {
				$missing_fields[] = $field;
			}
		}
		if( !empty($this->ticket_price) && !is_numeric($this->ticket_price) ){
			$this->add_error(__('Please enter a valid ticket price e.g. 10.50 (no currency signs)','dbem'));
		}
		if ( count($missing_fields) > 0){
			// TODO Create friendly equivelant names for missing fields notice in validation 
			$this->errors[] = __ ( 'Missing fields: ' ) . implode ( ", ", $missing_fields ) . ". ";
		}
		return apply_filters('em_ticket_validate', count($this->errors) == 0, $this );
	}
	
	function is_available(){
		$timestamp = current_time('timestamp');
		$EM_Event = $this->get_event();
		$available_spaces = $this->get_available_spaces();
		$condition_1 = (empty($this->ticket_start) || $this->start_timestamp <= $timestamp);
		$condition_2 = $this->end_timestamp + 86400 >= $timestamp || empty($this->ticket_end);
		$condition_3 = $EM_Event->start > $timestamp || strtotime($EM_Event->event_rsvp_date. ' '. $EM_Event->event_rsvp_time) > $timestamp;
		$condition_4 = !$this->ticket_members || ($this->ticket_members && is_user_logged_in());
		if( $condition_1 && $condition_2 && $condition_3 && $condition_4 ){
			//Time Constraints met, now quantities
			if( $available_spaces > 0 && ($available_spaces >= $this->ticket_min || empty($this->ticket_min)) ){
				return apply_filters('em_ticket_is_available',true,$this);
			}
		}
		return apply_filters('em_ticket_is_available',false,$this);
	}
	
	/**
	 * Gets the total price for this ticket.
	 * @return float
	 */
	function get_price($format = false, $add_tax = 'x' ){
		$price = $this->ticket_price;
		if( is_numeric(get_option('dbem_bookings_tax')) && get_option('dbem_bookings_tax') > 0 ){
			//tax could be added here
			if( $add_tax === true || ($add_tax !== false && get_option('dbem_bookings_tax_auto_add')) ){
				$price = round($price * (1 + get_option('dbem_bookings_tax')/100),2);
			}
		}
		$price = apply_filters('em_ticket_get_price',$price,$this);
		if($format){
			return em_get_currency_formatted($price);
		}
		return $price;
	}
	
	/**
	 * Get the total number of tickets (spaces) available, bearing in mind event-wide maxiumums and ticket priority settings.
	 * @return int
	 */
	function get_spaces(){
		return apply_filters('em_ticket_get_spaces',$this->ticket_spaces,$this);
	}

	/**
	 * Returns the number of available spaces left in this ticket, bearing in mind event-wide restrictions, previous bookings, approvals and other tickets.
	 * @return int
	 */
	function get_available_spaces(){
		$event_available_spaces = $this->get_event()->get_bookings()->get_available_spaces();
		$ticket_available_spaces = $this->get_spaces() - $this->get_booked_spaces();
		if( get_option('dbem_bookings_approval_reserved')){
		    $ticket_available_spaces = $ticket_available_spaces - $this->get_pending_spaces();
		}
		$return = ($ticket_available_spaces <= $event_available_spaces) ? $ticket_available_spaces:$event_available_spaces;
		return apply_filters('em_ticket_get_available_spaces', $return, $this);
	}
	
	function get_pending_spaces(){
	    $spaces = 0;
		foreach( $this->get_bookings()->get_pending_bookings()->bookings as $EM_Booking ){ //get_bookings() is used twice so we get the confirmed (or all if confirmation disabled) bookings of this ticket's total bookings.
			//foreach booking, get this ticket booking info if found
			foreach($EM_Booking->get_tickets_bookings()->tickets_bookings as $EM_Ticket_Booking){
				if( $EM_Ticket_Booking->ticket_id == $this->ticket_id ){
					$spaces += $EM_Ticket_Booking->get_spaces();
				}
			}
		}
		return apply_filters('em_ticket_get_pending_spaces', $spaces, $this);
	}

	/**
	 * Returns the number of booked spaces in this ticket.
	 * @return int
	 */
	function get_booked_spaces($force_reload=false){
		//get all bookings for this event
		$spaces = 0;
		if( is_object($this->bookings) && $force_reload ){
			//return $this->bookings;
		}
		foreach( $this->get_bookings()->get_bookings()->bookings as $EM_Booking ){ //get_bookings() is used twice so we get the confirmed (or all if confirmation disabled) bookings of this ticket's total bookings.
			//foreach booking, get this ticket booking info if found
			foreach($EM_Booking->get_tickets_bookings()->tickets_bookings as $EM_Ticket_Booking){
				if( $EM_Ticket_Booking->ticket_id == $this->ticket_id ){
					$spaces += $EM_Ticket_Booking->get_spaces();
				}
			}
		}
		return apply_filters('em_ticket_get_available_spaces', $spaces, $this);
	}
	
	/**
	 * Smart event locator, saves a database read if possible.
	 * @return EM_Event 
	 */
	function get_event(){
		return em_get_event($this->event_id);
	}
	
	/**
	 * returns array of EM_Booking objects that have this ticket
	 * @return EM_Bookings
	 */
	function get_bookings(){
		$bookings = array();
		foreach( $this->get_event()->get_bookings()->bookings as $EM_Booking ){
			foreach($EM_Booking->get_tickets_bookings()->tickets_bookings as $EM_Ticket_Booking){
				if( $EM_Ticket_Booking->ticket_id == $this->ticket_id ){
					$bookings[$EM_Booking->booking_id] = $EM_Booking;
				}
			}
		}
		$this->bookings = new EM_Bookings($bookings);
		return $this->bookings;
	}
	
	/**
	 * I wonder what this does....
	 * @return boolean
	 */
	function delete(){
		global $wpdb;
		$result = false;
		if( $this->can_manage() ){
			if( count($this->get_bookings()->bookings) == 0 ){
				$sql = $wpdb->prepare("DELETE FROM ". EM_TICKETS_TABLE . " WHERE ticket_id=%d", $this->ticket_id);
				$result = $wpdb->query( $sql );
			}else{
				$this->feedback_message = __('You cannot delete a ticket that has a booking on it.','dbem');
				$this->add_error($this->feedback_message);
				return false;
			}
		}
		return ( $result !== false );
	}

	/**
	 * Based on ticket minimums, whether required and if the event has more than one ticket this function will return the absolute minimum required spaces for a booking 
	 */
	function get_spaces_minimum(){
	    $ticket_count = count($this->get_event()->get_bookings()->get_tickets()->tickets);
	    //count available tickets to make sure
	    $available_tickets = 0;
	    foreach($this->get_event()->get_bookings()->get_tickets()->tickets as $EM_Ticket){
	    	if($EM_Ticket->is_available()){
	    		$available_tickets++;
	    	}
	    }
	    $min_spaces = 0;
	    if( $ticket_count > 1 ){
	        if( $this->is_required() && $this->is_available() ){
	            $min_spaces = ($this->ticket_min > 0) ? $this->ticket_min:1;
	        }elseif( $this->is_available() && $this->ticket_min > 0 ){
	            $min_spaces = $this->ticket_min;	            
	        }elseif( $this->is_available() && $available_tickets == 1 ){
	            $min_spaces = 1;
	        }
	    }else{
	    	$min_spaces = $this->ticket_min > 0 ? $this->ticket_min : 1;
	    }
	    return $min_spaces;
	}
	
	function is_required(){
	    if( $this->ticket_required || count($this->get_event()->get_tickets()->tickets) == 1 ){
	        return true;
	    }
	    return false;
	}

	/**
	 * Get the html options for quantities to go within a <select> container
	 * @return string
	 */
	function get_spaces_options($zero_value = true, $default_value = 0){
		$available_spaces = $this->get_available_spaces();		
		if( $this->is_available() ) {
		    $min_spaces = $this->get_spaces_minimum();
		    if( $default_value > 0 ){
			    $default_value = $min_spaces > $default_value ? $min_spaces:$default_value;
		    }else{
		        $default_value = $this->is_required() ? $min_spaces:0;
		    }
			ob_start();
			?>
			<select name="em_tickets[<?php echo $this->ticket_id ?>][spaces]" class="em-ticket-select" id="em-ticket-spaces-<?php echo $this->ticket_id ?>">
				<?php 
					$min = ($this->ticket_min > 0) ? $this->ticket_min:1;
					$max = ($this->ticket_max > 0) ? $this->ticket_max:get_option('dbem_bookings_form_max');
				?>
				<?php if($zero_value && !$this->is_required()) : ?><option>0</option><?php endif; ?>
				<?php for( $i=$min; $i<=$available_spaces && $i<=$max; $i++ ): ?>
					<option <?php if($i == $default_value){ echo 'selected="selected"'; $shown_default = true; } ?>><?php echo $i ?></option>
				<?php endfor; ?>
				<?php if(empty($shown_default) && $default_value > 0 ): ?><option selected="selected"><?php echo $default_value; ?></option><?php endif; ?>
			</select>
			<?php 
			return apply_filters('em_ticket_get_spaces_options', ob_get_clean(), $zero_value, $default_value, $this);
		}else{
			return false;
		}
	}
	
	/**
	 * Can the user manage this event? 
	 */
	function can_manage(){
		return $this->get_event()->can_manage('manage_bookings','manage_others_bookings');
	}
	
	/**
	 * Outputs properties with formatting
	 * @param string $property
	 * @return string
	 */
	function output_property($property){
		switch($property){
			case 'start':
				$value = date_i18n( get_option('date_format'), $this->start_timestamp );
				break;
			case 'end':
				$value = date_i18n( get_option('date_format'), $this->end_timestamp );
				break;
				break;
			default:
				$value = $this->$property;
				break;
		}
		return apply_filters('em_ticket_output_property',$value,$this);
	}
}
?>