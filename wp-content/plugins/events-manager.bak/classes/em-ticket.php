<?php
class EM_Ticket extends EM_Object{
	//DB Fields
	var $id;
	var $event_id;
	var $name;
	var $description;
	var $price;
	var $start;
	var $end;
	var $min;
	var $max;
	var $spaces = 10;
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
		'ticket_spaces' => array('name'=>'spaces','type'=>'%s','null'=>1)
	);
	//Other Vars
	/**
	 * @var EM_Event
	 */
	var $event;
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
		$this->name = __('Standard Ticket','dbem');
		if( $ticket_data !== false ){
			//Load ticket data
			$ticket = array();
			if( is_array($ticket_data) ){
				$ticket = $ticket_data;
			}elseif( is_numeric($ticket_data) ){
				//Retreiving from the database		
				global $wpdb;
				$sql = "SELECT * FROM ". EM_TICKETS_TABLE ." WHERE ticket_id ='$ticket_data'";   
			  	$ticket = $wpdb->get_row($sql, ARRAY_A);
			  	//Ticket notes
			  	$notes = $wpdb->get_results("SELECT * FROM ". EM_META_TABLE ." WHERE meta_key='ticket-note' AND object_id ='$ticket_data'", ARRAY_A);
			  	foreach($notes as $note){
			  		$this->notes[] = unserialize($note['meta_value']);
			  	}
			}
			//Save into the object
			$this->to_object($ticket);
			$this->start_timestamp = (!empty($ticket['ticket_start'])) ? strtotime($ticket['ticket_start']):false;
			$this->end_timestamp = (!empty($ticket['ticket_end'])) ? strtotime($ticket['ticket_end']):false;
		}
		do_action('em_ticket',$this, $ticket_data);
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
			$this->event_id = $this->get_event()->id; //event wouldn't exist before save, so refresh id
			$data = $this->to_array(true); //add the true to remove the nulls
			if($this->id != ''){
				//since currently wpdb calls don't accept null, let's build the sql ourselves.
				$set_array = array();
				foreach( $this->fields as $field_name => $field ){
					if( empty($this->$field['name']) && $field['null'] ){
						$set_array[] = "{$field_name}=NULL";
					}else{
						$set_array[] = "{$field_name}='".$wpdb->escape($this->$field['name'])."'";						
					}
				}
				$sql = "UPDATE $table SET ".implode(', ', $set_array)." WHERE ticket_id={$this->id}";
				$result = $wpdb->query($sql);
				$this->feedback_message = __('Changes saved','dbem');
			}else{
				//TODO better error handling
				$result = $wpdb->insert($table, $data, $this->get_types($data));
			    $this->id = $wpdb->insert_id;  
				$this->feedback_message = __('Ticket created','dbem'); 
			}
			if( $result === false ){
				$this->feedback_message = __('There was a problem saving the ticket.', 'dbem');
				$this->errors[] = __('There was a problem saving the ticket.', 'dbem');
			}
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
	function get_post(){
		//We are getting the values via POST or GET
		global $allowedposttags;
		do_action('em_location_get_post_pre', $this);
		$location = array();
		$location['ticket_id'] = ( !empty($_POST['ticket_id']) ) ? $_POST['ticket_id']:'';
		$location['event_id'] = ( !empty($_POST['event_id']) ) ? $_POST['event_id']:'';
		$location['ticket_name'] = ( !empty($_POST['ticket_name']) ) ? wp_kses_data(stripslashes($_POST['ticket_name'])):'';
		$location['ticket_description'] = ( !empty($_POST['ticket_description']) ) ? wp_kses(stripslashes($_POST['ticket_description'], $allowedposttags)):'';
		$location['ticket_price'] = ( !empty($_POST['ticket_price']) ) ? $_POST['ticket_price']:'';
		$location['ticket_start'] = ( !empty($_POST['ticket_start']) ) ? $_POST['ticket_start']:'';
		$location['ticket_end'] = ( !empty($_POST['ticket_end']) ) ? $_POST['ticket_end']:'';
		$this->start_timestamp = ( !empty($_POST['ticket_start']) ) ? strtotime($_POST['ticket_start']):'';
		$this->end_timestamp = ( !empty($_POST['ticket_end']) ) ? strtotime($_POST['ticket_end']):'';
		$location['ticket_min'] = ( !empty($_POST['ticket_min']) ) ? $_POST['ticket_min']:'';
		$location['ticket_max'] = ( !empty($_POST['ticket_max']) ) ? $_POST['ticket_max']:'';
		$location['ticket_spaces'] = ( !empty($_POST['ticket_spaces']) ) ? $_POST['ticket_spaces']:'';
		$this->to_object( apply_filters('em_location_get_post', $location, $this) );
	}	
	

	/**
	 * Validates the ticket for saving. Should be run during any form submission or saving operation.
	 * @return boolean
	 */
	function validate(){
		$missing_fields = Array ();
		foreach ( $this->required_fields as $field ) {
			$true_field = $this->fields[$field]['name'];
			if ( $this->$true_field == "") {
				$missing_fields[] = $field;
			}
		}
		if ( count($missing_fields) > 0){
			// TODO Create friendly equivelant names for missing fields notice in validation 
			$this->errors[] = __ ( 'Missing fields: ' ) . implode ( ", ", $missing_fields ) . ". ";
		}
		return apply_filters('em_event_validate', count($this->errors) == 0, $this );
	}
	
	function is_available(){
		$timestamp = current_time('timestamp');
		$available_spaces = $this->get_available_spaces();
		$val = (empty($this->start) || $this->start_timestamp <= $timestamp);
		$val2 = ($this->end_timestamp >= $timestamp || empty($this->end));
		$val3 = $this->get_event()->end > current_time('timestamp');
		
		if( (empty($this->start) || $this->start_timestamp <= $timestamp) && ($this->end_timestamp >= $timestamp || empty($this->end)) && $this->get_event()->end > current_time('timestamp') ){
			//Time Constraints met, now quantities
			if( $available_spaces > 0 && ($available_spaces >= $this->min || empty($this->min)) ){
				return apply_filters('em_ticket_is_available',true,$this);
			}
		}
		return apply_filters('em_ticket_is_available',false,$this);
	}
	
	/**
	 * Gets the total price for this ticket.
	 * @return float
	 */
	function get_price($format = false){
		if($format){
			return apply_filters('em_ticket_get_price', em_get_currency_symbol().number_format($this->price,2),$this);
		}
		return apply_filters('em_ticket_get_price',$this->price,$this);
	}
	
	/**
	 * Get the total number of tickets (spaces) available, bearing in mind event-wide maxiumums and ticket priority settings.
	 * @return int
	 */
	function get_spaces(){
		return apply_filters('em_ticket_get_spaces',$this->spaces,$this);
	}

	/**
	 * Returns the number of available spaces left in this ticket, bearing in mind event-wide restrictions, previous bookings, approvals and other tickets.
	 * @return int
	 */
	function get_available_spaces(){
		$event_available_spaces = $this->get_event()->get_bookings()->get_available_spaces();
		$ticket_available_spaces = $this->get_spaces() - $this->get_booked_spaces();
		$return = ($ticket_available_spaces <= $event_available_spaces) ? $ticket_available_spaces:$event_available_spaces;
		return apply_filters('em_ticket_get_available_spaces', $return, $this);
	}

	/**
	 * Returns the number of available spaces left in this ticket, bearing in mind event-wide restrictions, previous bookings, approvals and other tickets.
	 * @return int
	 */
	function get_booked_spaces($force_reload=false){
		//get all bookings for this event
		$spaces = 0;
		if( is_object($this->bookings) && $force_reload ){
			return $this->bookings;
		}
		foreach( $this->get_bookings()->get_bookings()->bookings as $EM_Booking ){ //get_bookings() is used twice so we get the confirmed (or all if confirmation disabled) bookings of this ticket's total bookings.
			//foreach booking, get this ticket booking info if found
			foreach($EM_Booking->get_tickets_bookings()->tickets_bookings as $EM_Ticket_Booking){
				if( $EM_Ticket_Booking->ticket_id == $this->id ){
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
		global $EM_Event;
		if( is_object($this->event) && get_class($this->event)=='EM_Event' && ($this->event->id == $this->event_id || empty($this->event_id)) ){
			return $this->event;
		}elseif( is_object($EM_Event) && $EM_Event->id == $this->event_id ){
			$this->event = $EM_Event;
		}else{
			$this->event = new EM_Event($this->event_id);
		}
		return $this->event;
	}
	
	/**
	 * returns array of EM_Booking objects that have this ticket
	 * @return EM_Bookings
	 */
	function get_bookings(){
		$bookings = array();
		foreach( $this->get_event()->get_bookings()->bookings as $EM_Booking ){
			foreach($EM_Booking->get_tickets_bookings()->tickets_bookings as $EM_Ticket_Booking){
				if( $EM_Ticket_Booking->ticket_id == $this->id ){
					$bookings[$EM_Booking->id] = $EM_Booking;
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
				$sql = $wpdb->prepare("DELETE FROM ". EM_TICKETS_TABLE . " WHERE ticket_id=%d", $this->id);
				$result = $wpdb->query( $sql );
			}else{
				$this->feedback_message = __('You cannot delete a ticket that has a booking on it.','dbem');
				return false;
			}
		}
		return ( $result !== false );
	}	

	/**
	 * Get the html options for quantities to go within a <select> container
	 * @return string
	 */
	function get_spaces_options($zero_value = true, $default_value = 0){
		$available_spaces = $this->get_available_spaces();
		if( $this->is_available() ) {
			ob_start();
			?>
			<select name="em_tickets[<?php echo $this->id ?>][spaces]" class="em-ticket-select">
				<?php 
					$min = ($this->min > 0) ? $this->min:1;
					$max = ($this->max > 0) ? $this->max:get_option('dbem_bookings_form_max');
				?>
				<?php if($zero_value) : ?><option>0</option><?php endif; ?>
				<?php for( $i=$min; $i<=$available_spaces && $i<=$max; $i++ ): ?>
					<option <?php if($i == $default_value){ echo 'selected="selected"'; $shown_default = true; } ?>><?php echo $i ?></option>
				<?php endfor; ?>
				<?php if(empty($shown_default) && $default_value > 0 ): ?><option selected="selected"><?php echo $default_value; ?></option><?php endif; ?>
			</select>
			<?php 
			return ob_get_clean();
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