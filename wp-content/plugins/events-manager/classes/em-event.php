<?php
/**
 * Event Object. This holds all the info pertaining to an event, including location and recurrence info.
 * An event object can be one of three "types" a recurring event, recurrence of a recurring event, or a single event.
 * The single event might be part of a set of recurring events, but if loaded by specific event id then any operations and saves are 
 * specifically done on this event. However, if you edit the recurring group, any changes made to single events are overwritten.
 * 
 * @author marcus
 */
//TODO Can add more recurring functionality such as "also update all future recurring events" or "edit all events" like google calendar does.
//TODO Integrate recurrences into events table
//FIXME If you create a super long recurrence timespan, there could be thousands of events... need an upper limit here.
class EM_Event extends EM_Object{
	/**
	 * Assoc array where keys are names of database fields and values are array corresponding object property name, regex, data types, etc. 
	 * for use when importing/exporting event data between database and object
	 * @var array
	 */
	var $fields = array(
		'event_id' => array( 'name'=>'id', 'type'=>'%d' ),
		'event_slug' => array( 'name'=>'slug', 'type'=>'%s' ),
		'event_owner' => array( 'name'=>'owner', 'type'=>'%d' ),
		'event_name' => array( 'name'=>'name', 'type'=>'%s' ),
		'event_start_time' => array( 'name'=>'start_time', 'type'=>'%s' ),
		'event_end_time' => array( 'name'=>'end_time', 'type'=>'%s' ),
		'event_start_date' => array( 'name'=>'start_date', 'type'=>'%s' ),
		'event_end_date' => array( 'name'=>'end_date', 'type'=>'%s' ),
		'event_notes' => array( 'name'=>'notes', 'type'=>'%s' ),
		'event_rsvp' => array( 'name'=>'rsvp', 'type'=>'%d' ),
		//'event_spaces' => array( 'name'=>'spaces', 'type'=>'%d' ),
		'location_id' => array( 'name'=>'location_id', 'type'=>'%d' ),
		'recurrence_id' => array( 'name'=>'recurrence_id', 'type'=>'%d' ),
		'event_attributes' => array( 'name'=>'attributes', 'type'=>'%s' ),
		'recurrence' => array( 'name'=>'recurrence', 'type'=>'%d' ),
		'recurrence_interval' => array( 'name'=>'interval', 'type'=>'%d' ), //every x day(s)/week(s)/month(s)
		'recurrence_freq' => array( 'name'=>'freq', 'type'=>'%s' ), //daily,weekly,monthly?
		'recurrence_byday' => array( 'name'=>'byday', 'type'=>'%s' ), //if weekly or monthly, what days of the week?
		'recurrence_byweekno' => array( 'name'=>'byweekno', 'type'=>'%d' ), //if monthly which week (-1 is last)
		'event_status' => array( 'name'=>'status', 'type'=>'%d' ), //if monthly which week (-1 is last)
		'event_date_created' => array( 'name'=>'date_created', 'type'=>'%s' ),
		'event_date_modified' => array( 'name'=>'date_modified', 'type'=>'%s' ),
		'blog_id' => array( 'name'=>'blog_id', 'type'=>'%d' ),
		'group_id' => array( 'name'=>'group_id', 'type'=>'%d' )
	);
	/* Field Names  - see above for matching DB field names and other field meta data */
	var $id;
	var $slug;
	var $owner;
	var $name;
	var $start_time;
	var $end_time;
	var $start_date;
	var $end_date;
	var $notes;
	var $rsvp;
	//var $spaces;
	var $location_id;
	var $recurrence_id;
	var $category_id;
	var $attributes = array();
	var $recurrence;
	var $interval;
	var $freq;
	var $byday;
	var $byweekno;
	var $status;
	var $date_created;
	var $date_modified;
	var $blog_id;
	var $group_id; 
	
	var $image_url = '';
	/**
	 * Timestamp of start date/time
	 * @var int
	 */
	var $start;
	/**
	 * Timestamp of end date/time
	 * @var int
	 */
	var $end;
	/**
	 * Created on timestamp, taken from DB, converted to TS
	 * @var int
	 */
	var $created;
	/**
	 * Created on timestamp, taken from DB, converted to TS
	 * @var int
	 */
	var $modified;
	
	/**
	 * @var EM_Location
	 */
	var $location;
	/**
	 * @var EM_Bookings
	 */
	var $bookings;
	/**
	 * The contact person for this event
	 * @var WP_User
	 */
	var $contact;
	/**
	 * The category object
	 * @var EM_Category
	 */
	var $category;
	/**
	 * If there are any errors, they will be added here.
	 * @var array
	 */
	var $errors = array();	
	/**
	 * If something was successful, a feedback message might be supplied here.
	 * @var string
	 */
	var $feedback_message;
	/**
	 * Any warnings about an event (e.g. bad data, recurring/recurrence, etc.)
	 * @var string
	 */
	var $warnings;
	/**
	 * Array of dbem_event field names required to create an event 
	 * @var array
	 */
	var $required_fields = array('event_name', 'event_start_date');
	var $mime_types = array(1 => 'gif', 2 => 'jpg', 3 => 'png'); 
	
	/**
	 * Initialize an event. You can provide event data in an associative array (using database table field names), an id number, or false (default) to create empty event.
	 * @param mixed $event_data
	 * @return null
	 */
	function EM_Event($event_data = false) {
		global $wpdb, $EM_Recurrences;
		//TODO Change the way we deal with time, maybe revert to timestamps for manipulation, and worry about output in html and db writes?
		if( $event_data !== false ){
			$event = array();
			if( is_array($event_data) ){
				//Accepts a raw array that'll just be imported directly into the object with no DB lookups (same for event and recurrence)
				$event = $event_data;
				if($event['location_name']){
					$this->location = new EM_Location( $event );
				}
			}elseif( !empty($event_data) ) {
				if( is_numeric($event_data) && $event_data > 0 ){
					$cond = "event_id = $event_data";
				}else{
					$cond = "event_slug = '".$wpdb->escape($event_data)."'";
				}
				//Retreiving from the database  
				$events_table = EM_EVENTS_TABLE;
				$locations_table = EM_LOCATIONS_TABLE;
				$categories_table = EM_CATEGORIES_TABLE;
				$sql = "
					SELECT * FROM $events_table
					LEFT JOIN $locations_table ON {$locations_table}.location_id={$events_table}.location_id 
					WHERE $cond
				"; //We get event and location data here to avoid extra queries
				$event = $wpdb->get_row ( $sql, ARRAY_A );
				//Sort Location
				$this->location = new EM_Location ( $event );
			}
			//Sort out attributes
			if( !empty($event['event_attributes']) ){
				if( is_serialized($event['event_attributes']) ){
					$event['event_attributes'] =  @unserialize($event['event_attributes']);
				}
				$event['event_attributes'] = (!is_array($event['event_attributes'])) ?  array() : $event['event_attributes'] ;
			}
			$event['recurrence_byday'] = ( empty($event['recurrence_byday']) || $event['recurrence_byday'] == 7 ) ? 0:$event['recurrence_byday']; //Backward compatibility (since 3.0.3), using 0 makes more sense due to date() function
			$this->to_object($event, true);
			$this->blog_id = (!empty($event['blog_id'])) ? $event['blog_id']:0;
			
			//Start/End times should be available as timestamp
			$this->start = strtotime($this->start_date." ".$this->start_time);
			$this->end = strtotime($this->end_date." ".$this->end_time);
			$this->modified = ( !empty($event['event_date_modified']) ) ? strtotime($event['event_date_modified']):time();
			$this->created = ( !empty($event['event_date_created']) ) ? strtotime($event['event_date_created']):time();			
			
			//Add Owner as Contact Person
			if($this->owner && $this->owner > 0){
				$this->contact = get_userdata($this->owner);
			}
			if( !is_object($this->contact) ){
				$this->owner = get_option('dbem_default_contact_person');
				$this->contact = get_userdata($this->owner);
			}
			if( is_object($this->contact) ){
	      		$this->contact->phone = get_metadata('user', $this->contact->ID, 'dbem_phone', true);
			}
			//Now, if this is a recurrence, get the recurring for caching to the $EM_Recurrences
			if( $this->is_recurrence() && is_array($EM_Recurrences) && !array_key_exists($this->recurrence_id, $EM_Recurrences) ){
				$EM_Recurrences[$this->recurrence_id] = new EM_Event($this->recurrence_id);
			}
		}else{
			$this->location = new EM_Location(); //blank location
		}
		$this->get_location();
		//Do it here so things appear in the po file.
		$this->status_array = array(
			0 => __('Pending','dbem'),
			1 => __('Approved','dbem')
		);
		
		//Set up some warning meta
		if ( $this->is_recurring() ) {
			$this->warnings['recurring'] = __( 'WARNING: This is a recurring event.', 'dbem' )."<br />". __( 'Modifications to this event will cause all recurrences of this event to be deleted and recreated and previous bookings will be deleted! You can edit individual recurrences and disassociate them with this recurring event.', 'dbem' );
		} elseif ( $this->is_recurrence() ) {
			$this->warnings['recurrence'] = __('WARNING: This is a recurrence in a set of recurring events.', 'dbem')."<br />". __('If you update this event data and save, it will become an independent event, and will not be deleted or modified automatically if you reschedule the original recurring event details.', 'dbem' );
		}elseif( !empty($this->group_id) && function_exists('groups_get_group') ){
			$group = groups_get_group(array('group_id'=>$this->group_id));
			$this->warnings['group'] = sprintf(__('WARNING: This is a event belonging to the group "%s". Other group admins can also modify this event.', 'dbem'), $group->name);
		}
		$this->get_image_url();
		add_filter('em_event_save',array(&$this, 'image_upload'), 1, 2);
		do_action('em_event', $this, $event_data);
	}
	
	/**
	 * Retrieve event, location and recurring information via POST
	 * @return boolean
	 */
	function get_post(){
		//Build Event Array
		do_action('em_event_get_post_pre', $this);
		$this->name = ( !empty($_POST['event_name']) ) ? stripslashes($_POST['event_name']) : '' ;
		$this->slug = ( !empty($_POST['event_slug']) ) ? sanitize_title($_POST['event_slug']) : '' ;
		$this->start_date = ( !empty($_POST['event_start_date']) ) ? $_POST['event_start_date'] : '';
		$this->end_date = ( !empty($_POST['event_end_date']) ) ? $_POST['event_end_date'] : $this->start_date; 
		$this->rsvp = ( !empty($_POST['event_rsvp']) ) ? 1:0;
		//$this->spaces = ( !empty($_POST['event_spaces']) && is_numeric($_POST['event_spaces']) ) ? $_POST['event_spaces']:0;
		$this->notes = ( !empty($_POST['content']) ) ? stripslashes($_POST['content']) : ''; //WP TinyMCE field
		//Sort out time
		//TODO make time handling less painful
		$match = array();
		if( !empty($_POST['event_start_time']) && preg_match ( '/^([01]\d|2[0-3]):([0-5]\d)(AM|PM)?$/', $_POST['event_start_time'], $match ) ){
			if( !empty($match[3]) && $match[3] == 'PM' && $match[1] != 12 ){
				$match[1] = 12+$match[1];
			}elseif( !empty($match[3]) && $match[3] == 'AM' && $match[1] == 12 ){
				$match[1] = '00';
			} 
			$this->start_time = $match[1].":".$match[2].":00";
		}else{
			$this->start_time = "00:00:00";
		}
		if( !empty($_POST['event_end_time']) && preg_match ( '/^([01]\d|2[0-3]):([0-5]\d)(AM|PM)?$/', $_POST['event_end_time'], $match ) ){
			if( !empty($match[3]) && $match[3] == 'PM' && $match[1] != 12 ){
				$match[1] = 12+$match[1];
			}elseif( !empty($match[3]) && $match[3] == 'AM' && $match[1] == 12 ){
				$match[1] = '00';
			}  
			$this->end_time = $match[1].":".$match[2].":00";
		}else{
			$this->end_time = $this->start_time;
		}
		//Start/End times should be available as timestamp
		$this->start = strtotime($this->start_date." ".$this->start_time);
		$this->end = strtotime($this->end_date." ".$this->end_time);
		//owner
		if( !empty($_REQUEST['event_owner']) && is_numeric($_REQUEST['event_owner']) ){
			$this->owner = current_user_can('edit_others_events') ? $_REQUEST['event_owner']:get_current_user_id();
		}
		//categories
		if( !empty($_POST['event_categories']) && is_array($_POST['event_categories']) ){
			$this->categories = new EM_Categories($_POST['event_categories']);
		}else{
			$this->categories = new EM_Categories();
		}
		//Attributes
		$event_attributes = array();
		$post = $_POST;
		$event_available_attributes = em_get_attributes();
		if( !empty($_POST['em_attributes']) && is_array($_POST['em_attributes']) ){
			foreach($_POST['em_attributes'] as $att_key => $att_value ){
				if( (in_array($att_key, $event_available_attributes['names']) || array_key_exists($att_key, $this->attributes) ) && trim($att_value) != '' ){
					$att_vals = count($event_available_attributes['values'][$att_key]);
					if( $att_vals == 0 || ($att_vals > 0 && in_array($att_value, $event_available_attributes['values'][$att_key])) ){
						$event_attributes[$att_key] = $att_value;
					}elseif($att_vals > 0){
						$event_attributes[$att_key] = $event_available_attributes['values'][$att_key][0];
					}
				}
			}
		}
	 	$this->attributes = stripslashes_deep($event_attributes);
		//Recurrence data
		$this->recurrence_id = ( !empty($_POST['recurrence_id']) && is_numeric($_POST['recurrence_id']) ) ? $_POST['recurrence_id'] : 0 ;
		if( !empty($_POST['repeated_event']) ){
			$this->recurrence = 1;
			$this->freq = ( !empty($_POST['recurrence_freq']) && in_array($_POST['recurrence_freq'], array('daily','weekly','monthly')) ) ? $_POST['recurrence_freq']:'daily';
			if( !empty($_POST['recurrence_bydays']) && $this->freq == 'weekly' && self::array_is_numeric($_POST['recurrence_bydays']) ){
				$this->byday = implode ( ",", $_POST['recurrence_bydays'] );	
			}elseif( !empty($_POST['recurrence_byday']) && $this->freq == 'monthly' ){
				$this->byday = $_POST['recurrence_byday'];
			}
			$this->interval = ( !empty($_POST['recurrence_interval']) ) ? $_POST['recurrence_interval']:1;
			$this->byweekno = ( !empty($_POST['recurrence_byweekno']) ) ? $_POST['recurrence_byweekno']:'';
		}
		
		//Add location information, or just link to previous location, this is a requirement...
		if( !empty($_POST['location_id']) && is_numeric($_POST['location_id'])) {
			$this->location_id = $_POST['location_id'];
			$this->location = new EM_Location($_POST['location_id']);			
		} else {
			$this->location_id = '';
			$this->location = new EM_Location();
			$this->location->get_post();
			$this->location->description = ''; //otherwise we get the same event details in the location  
		}
		if( !empty($_REQUEST['event_rsvp']) && $_REQUEST['event_rsvp'] && !$this->get_bookings()->get_tickets()->get_post() ){
			$EM_Tickets = $this->get_bookings()->get_tickets();
			array_merge($this->errors, $this->get_bookings()->get_tickets()->errors);
		}
		return apply_filters('em_event_get_post', $this->validate(), $this);
	}
	
	/**
	 * Will save the current instance into the database, along with location information if a new one was created and return true if successful, false if not.
	 * Will automatically detect what type of event it is (recurrent, recurrence or normal) and whether it's a new or existing event. 
	 * @return boolean
	 */
	function save(){
		//FIXME Event doesn't save title when inserting first time
		global $wpdb, $current_user;
		if( !$this->can_manage('edit_events', 'edit_others_events') && ( get_option('dbem_events_anonymous_submissions') && empty($this->id)) ){
			return apply_filters('em_event_save', false, $this);
		}
		do_action('em_event_save_pre', $this);
   		get_currentuserinfo();
		$events_table = EM_EVENTS_TABLE;
		$request = $_REQUEST;
		//First let's save the location if location doesn't already exist, no location no event!
		if ( empty($this->get_location()->id) && !$this->location->save() ){ //shouldn't try to save if location exists
			$this->errors[] = __('There was a problem saving the location so event was not saved.', 'dbem');
	 		return apply_filters('em_event_save', false, $this);
		}
		$this->location_id = $this->location->id;
		//owner person can be anyone the admin wants, but the creator if not.
		if( current_user_can('edit_others_events') ){
			$this->owner = ( $this->owner > 0 ) ? $this->owner:0;
		}elseif( !is_user_logged_in() && get_option('dbem_events_anonymous_submissions') && get_option('dbem_events_anonymous_user') ){
			$this->owner = get_option('dbem_events_anonymous_user'); //user is anonymous, so give the event
		}else{
			//force 
			$this->owner = get_current_user_id();
		}
		//Set status of this event, depending on user type
		if( current_user_can('publish_events') ){
			//top level can edit and publish any events
			$this->status = 1;
		}else{
			//any updates or additions put the event into pending status
			$this->status = 0;
		}
		$this->slug = $this->sanitize_title();
		
		//Now save the event
		if ( !$this->id ) {
			// Insert New Event
			if( is_multisite() ){
				$this->blog_id = get_current_blog_id();
			}
			$this->date_created = current_time('mysql');
			$event = $this->to_array(true);
			$event['event_attributes'] = serialize($this->attributes);
			$event['recurrence_id'] = ( is_numeric($this->recurrence_id) ) ? $this->recurrence_id : 0;
			$event = apply_filters('em_event_save_pre',$event,$this);
			$result = $wpdb->insert ( $events_table, $event, $this->get_types($event) );
			if($result !== false){
				//$event['event_date_created'] = current_time('mysql');
				$this->id = $wpdb->insert_id;
				$this->is_new = true;
				//Add Tickets
				if( !$this->get_bookings()->get_tickets()->save() ){
					$this->errors[] = 	__( 'Something went wrong with creating tickets.', 'dbem' );
					return apply_filters('em_event_save', false, $this);
				}
				//Save Categories
				if( !$this->get_categories()->save() ){
					$this->add_error( $this->get_categories()->get_errors() );
					return apply_filters('em_event_save', false, $this);					
				}
				//Deal with recurrences
				if ( $this->is_recurring() ) {
					//Recurrence master event saved, now Save Events & check errors
				 	if( !$this->save_events() ){
						$this->add_error(__ ( 'Something went wrong with the recurrence update...', 'dbem' ).
											__ ( 'There was a problem saving the recurring events.', 'dbem' ));
						$this->delete();
				 		return apply_filters('em_event_save', false, $this);
				 	}
				 	//All good! Event Saved
					$this->feedback_message = __ ( 'New recurrent event inserted!', 'dbem' );
					return apply_filters('em_event_save', true, $this);
				}
				//Successful individual save
				$this->feedback_message = __ ( 'New event successfully inserted!', 'dbem' );
				return apply_filters('em_event_save', true, $this);
			}else{
				$this->errors[] = 	__ ( 'Could not save the event details due to a database error.', 'dbem' );
			}
		} else {
			// Update Event
			if($this->is_recurrence()){
				//duplicate the original recurrence image
				$dir = (EM_IMAGE_DS == '/') ? 'events/event':'event';
			  	foreach($this->mime_types as $mime_type) { 
					$file_name = $dir."-{$this->recurrence_id}.$mime_type";
					if( file_exists( EM_IMAGE_UPLOAD_DIR . $file_name) ) {
						$replacement = $dir."-{$this->id}.$mime_type";
			  			copy(EM_IMAGE_UPLOAD_DIR . $file_name, EM_IMAGE_UPLOAD_DIR . $replacement);
					}
				}	
			}
			$this->recurrence_id = 0; // If it's saved here, it becomes individual
			$event = $this->to_array();
			$event['event_attributes'] = serialize($event['event_attributes']);
			unset($event['event_date_created']);
			$event['event_date_modified'] = current_time('mysql');
			$event = apply_filters('em_event_save_pre',$event,$this);
			$result = $wpdb->update ( $events_table, $event, array('event_id' => $this->id), $this->get_types($event) );
			if($result !== false){ //Can't just do $result since if you don't make an actual record details change, it'll return 0 for no changes made
				//Add Tickets
				$this->feedback_message = "{$this->name} " . __ ( 'updated', 'dbem' ) . "!";
				if( !$this->get_bookings()->get_tickets()->save() ){
					$this->errors[] = 	__( 'Something went wrong with creating tickets.', 'dbem' );
					return apply_filters('em_event_save', false, $this);
				}
				//Save Categories
				if( !$this->get_categories()->save() ){
					$this->add_error( $this->get_categories()->get_errors() );
					return apply_filters('em_event_save', false, $this);					
				}
				//Deal with recurrences
				if ( $this->is_recurring() ) {
					if( !$this->save_events() ){
						$this->errors[] = 	__ ( 'Something went wrong with the recurrence update...', 'dbem' ).
											__ ( 'There was a problem saving the recurring events.', 'dbem' );
						return apply_filters('em_event_save', false, $this);
					}
					$this->feedback_message = __ ( 'Recurrence updated!', 'dbem' );
					return apply_filters('em_event_save', true, $this);			
				}
			}else{
				$this->errors[] = __('Could not save the event details due to a database error.', 'dbem');
				return apply_filters('em_event_save', false, $this);
			}
			//Successful individual or recurrence save
			$this->feedback_message = "{$this->name} " . __ ( 'updated', 'dbem' ) . "!";
			if($this->rsvp == 0){
				$this->delete_bookings();
			}
			return apply_filters('em_event_save', true, $this);
		}
	}
	
	/**
	 * Takes the title and gives either a unique slug or returns the currently used slug if this record already has it.
	 * @param unknown_type $title
	 */
	function sanitize_title($iteration = 1){
		global $wpdb;
		//Generate the slug. If this is a new event, create the slug automatically, if not, verify it is still unique and if not rewrite
		if( empty($this->slug) ){
			$this->slug = sanitize_title($this->name);
		}
		$slug = $this->slug;
		$slug_matches = $wpdb->get_results('SELECT event_id FROM '.EM_EVENTS_TABLE." WHERE event_slug='{$slug}'", ARRAY_A);
		if( count($slug_matches) > 0 ){ //we will check that the slug is unique
			if( $slug_matches[0]['event_id'] != $this->id || count($slug_matches) > 1 ){
				//we have a conflict, so try another alternative
				$this->slug = preg_replace('/\-[0-9]+$/', '', $slug).'-'.($iteration+1);
				$this->sanitize_title($iteration+1);
			}
		}
		return apply_filters('em_event_sanitize_title', $this->slug, $this);
	}
	
	/**
	 * Delete whole event, including recurrence and recurring data
	 * @param $recurrence_id
	 * @return boolean
	 */
	function delete(){
		global $wpdb;
		do_action('em_event_delete_pre', $this);
		$result = false;
		if( $this->can_manage( 'delete_events','delete_others_events' ) ){
			if( $this->is_recurring() ){
				//Delete the recurrences then this recurrence event
				$this->delete_events();
			}
			$result = $wpdb->query ( $wpdb->prepare("DELETE FROM ". EM_EVENTS_TABLE ." WHERE event_id=%d", $this->id) );
			if($result !== false){
				//delete bookings
				$result_bookings = $this->get_bookings()->delete();
				//delete tickets
				$result_tickets = $this->get_bookings()->get_tickets()->delete();
				//delete categories
				$result = $wpdb->query ( $wpdb->prepare("DELETE FROM ". EM_META_TABLE ." WHERE meta_key='event-category' AND object_id=%d", $this->id) );
				$this->id = false;
			}
		}
		return apply_filters('em_event_delete', $result !== false, $this);
	}
	
	/**
	 * approve a booking.
	 * @return bool
	 */
	function approve(){
		return $this->set_status(1);
	}
	
	/**
	 * Change the status of the event. This will save to the Database too. 
	 * @param unknown_type $status
	 * @return string
	 */
	function set_status($status){
		$action_string = strtolower($this->status_array[$status]); 
		$this->previous_status = $this->status;
		$this->status = $status;
		$result = $this->save();
		if($result){
			$this->feedback_message = sprintf(__('Event %s.','dbem'), $action_string);
			return true;
		}else{
			//errors should be logged by save()
			$this->feedback_message = sprintf(__('Booking could not be %s.','dbem'), $action_string);
			return false;
		}
	}
	
	/**
	 * Duplicates this event and returns the duplicated event. Will return false if there is a problem with duplication.
	 * @return EM_Event
	 */
	function duplicate(){
		global $wpdb, $EZSQL_ERROR;
		//First, duplicate.
		if( $this->can_manage('edit_events','edit_others_events') ){
			$event_table_name = EM_EVENTS_TABLE;
			$eventArray = $this->to_array();
			unset($eventArray['event_id']);
			$EM_Event = new EM_Event( $eventArray );
			if( $EM_Event->save() ){
				$EM_Event->feedback_message = sprintf(__("%s successfully duplicated.", 'dbem'), __('Event','dbem'));
				return apply_filters('em_event_duplicate', $EM_Event, $this);
			}
		}else{
			$this->add_error( sprintf(__('You are not allowed to manage this %s.', 'dbem'), __('event','dbem')) );
		}
		//TODO add error notifications for duplication failures.
		return apply_filters('em_event_duplicate', false, $this);;
	}
	
	
	/**
	 * Validates the event. Should be run during any form submission or saving operation.
	 * @return boolean
	 */
	function validate() {
		$missing_fields = Array ();
		foreach ( $this->required_fields as $field ) {
			$true_field = $this->fields[$field]['name'];
			if ( $this->$true_field == "") {
				$missing_fields[] = $field;
			}
		}
		if ( count($missing_fields) > 0){
			// TODO Create friendly equivelant names for missing fields notice in validation 
			$this->add_error( __( 'Missing fields: ', 'dbem') . implode ( ", ", $missing_fields ) . ". " );
		}
		if ( !empty($_POST['repeated_event']) && $_POST['repeated_event'] == "1" && $this->end_date == "" ){
			$this->add_error( __( 'Since the event is repeated, you must specify an event date.', 'dbem' ));
		}
		if( preg_match('/\d{4}-\d{2}-\d{2}/', $this->start_date) && preg_match('/\d{4}-\d{2}-\d{2}/', $this->end_date) ){
			if( strtotime($this->start_date . $this->start_time) > strtotime($this->end_date . $this->end_time) ){
				$this->add_error(__('Events cannot start after they end.','dbem'));
			}
		}else{
			$this->add_error(__('Dates must have correct formatting. Please use the date picker provided.','dbem'));
		}
		if( $this->get_location()->id == '' && !$this->location->validate() ){
			$this->errors = array_merge($this->errors, $this->location->errors);
		}
		$this->image_validate();
		
		//TODO validate recurrence during event validate
		$count = count($this->errors);
		return apply_filters('em_event_validate', count($this->errors) == 0, $this );
	}
	
	/**
	 * Returns an EM_Categories object of the EM_Event instance.
	 * @return EM_Categories
	 */
	function get_categories() {
		global $EM_Categories;
		if( !empty($this->categories) && is_object($this->categories) && get_class($this->categories)=='EM_Categories' && ( empty($this->categories->event->id) || $this->categories->event->id == $this->id ) ){
			$this->categories = $this->categories;
		}elseif( is_object($EM_Categories) && $EM_Categories->get_event()->id == $this->id ){
			$this->categories = $EM_Categories;
		}else{
			$this->categories = new EM_Categories($this);
		}
		$this->categories->event = $this;
		return apply_filters('em_event_get_categories', $this->categories, $this);
	}
	
	/**
	 * Returns the location object this event belongs to.
	 * @return EM_Location
	 */
	function get_location() {
		global $EM_location;
		if( is_object($this->location) && get_class($this->location)=='EM_Location' && ($this->location_id == $this->location->id || empty($this->id)) ){
			return $this->location;
		}elseif( is_object($EM_location) && $EM_location->id == $this->location_id ){
			$this->location = $EM_location;
		}else{
			$this->location = new EM_location($this->location_id);
		}
		return apply_filters('em_event_get_location', $this->location, $this);
	}	
	
	/**
	 * Shortcut function for $this->get_bookings()->delete(), because using the EM_Bookings requires loading previous bookings, which isn't neceesary. 
	 */
	function delete_bookings(){
		global $wpdb;
		do_action('em_event_delete_bookings_pre', $this);
		$result = false;
		if( $this->can_manage('manage_bookings','manage_others_bookings') ){
			$result = $wpdb->query( $wpdb->prepare("DELETE FROM ".EM_BOOKINGS_TABLE." WHERE event_id=%d", $this->id) );
		}
		return apply_filters('em_event_delete_bookings', $result, $this);
	}
	
	/**
	 * Retrieve and save the bookings belonging to instance. If called again will return cached version, set $force_reload to true to create a new EM_Bookings object.
	 * @param boolean $force_reload
	 * @return EM_Bookings
	 */
	function get_bookings( $force_reload = false ){
		if( get_option('dbem_rsvp_enabled') ){
			if( (!$this->bookings || $force_reload) ){
				$this->bookings = new EM_Bookings($this);
			}
		}else{
			return new EM_Bookings();
		}
		return apply_filters('em_event_get_bookings', $this->bookings, $this);
	}
	
	function is_free(){
		$free = true;
		if( isset($this->free) ) return $this->free;
		foreach($this->get_bookings()->get_tickets() as $EM_Ticket){
			if( $EM_Ticket->price > 0 ){
				$free = false;
			}
		}
		return apply_filters('em_event_is_free',$free,$this);
	}
	
	/**
	 * Gets number of spaces in this event, dependent on ticket spaces or hard limit, whichever is smaller.
	 * @param boolean $force_refresh
	 * @return int 
	 */
	function get_spaces($force_refresh=false){
		return $this->get_bookings()->get_spaces($force_refresh);
	}
	
	/**
	 * Will output a single event format of this event. 
	 * Equivalent of calling EM_Event::output( get_option ( 'dbem_single_event_format' ) )
	 * @param string $target
	 * @return string
	 */
	function output_single($target='html'){
		$format = get_option ( 'dbem_single_event_format' );
		return apply_filters('em_event_output_single', $this->output($format, $target), $this, $target);
	}
	
	/**
	 * Will output a event in the format passed in $format by replacing placeholders within the format.
	 * @param string $format
	 * @param string $target
	 * @return string
	 */	
	function output($format, $target="html") {
	 	//First let's do some conditional placeholder removals
		preg_match_all('/\{([a-zA-Z0-9_]+)\}([^{]+)\{\/[a-zA-Z0-9_]+\}/', $format, $conditionals);
		if( count($conditionals[0]) > 0 ){
			//Check if the language we want exists, if not we take the first language there
			foreach($conditionals[1] as $key => $condition){
				$replacement = $conditionals[0][$key];
				if ($condition == 'has_bookings') {
					//check if there's a booking, if not, remove this section of code.
					if($this->rsvp && get_option('dbem_rsvp_enabled')){
						$replacement = substr($conditionals[0][$key], 14, strlen($conditionals[0][$key])-29); //29 = (15+14)
					}else{
						$replacement = '';
					}
				}
				if ($condition == 'no_bookings') {
					//check if there's a booking, if not, remove this section of code.
					if(!$this->rsvp && get_option('dbem_rsvp_enabled')){
						$replacement = substr($conditionals[0][$key], 13, strlen($conditionals[0][$key])-28); //28 = (13+14)
					}else{
						$replacement = '';
					}
					str_replace($conditionals[0][$key], $replacement, $format);
				}
				$format = str_replace($conditionals[0][$key], apply_filters('em_event_output_condition', $replacement, $condition, $conditionals[0][$key], $this), $format);
			}
		}
	 	$event_string = $format;
		//Now let's check out the placeholders.
	 	preg_match_all("/(#@?_?[A-Za-z0-9]+)({([a-zA-Z0-9,]+)})?/", $format, $placeholders);
		foreach($placeholders[1] as $key => $result) {
			$match = true;
			$replace = '';
			$full_result = $placeholders[0][$key];
			switch( $result ){
				//Event Details
				case '#_EVENTID':
					$replace = $this->id;
					break;
				case '#_NAME':
					$replace = $this->name;
					break;
				case '#_NOTES':
				case '#_EXCERPT':
					//SEE AT BOTTOM OF FILE FOR OLD TARGET FILTERS FROM 2.x
					$replace = $this->notes;
					if($result == "#_EXCERPT"){
						$matches = explode('<!--more', $this->notes);
						$replace = $matches[0];
					}
					break;
				case '#_EVENTIMAGEURL':
				case '#_EVENTIMAGE':
	        		if($this->image_url != ''){
						if($result == '#_EVENTIMAGEURL'){
		        			$replace =  $this->image_url;
						}else{
							if( empty($placeholders[3][$key]) ){
								$replace = "<img src='".$this->image_url."' alt='".esc_attr($this->name)."'/>";
							}else{
								$image_size = explode(',', $placeholders[3][$key]);
								if( $this->array_is_numeric($image_size) && count($image_size) > 1 ){
									$replace = "<img src='".em_get_thumbnail_url($this->image_url, $image_size[0], $image_size[1])."' alt='".esc_attr($this->name)."'/>";
								}else{
									$replace = "<img src='".$this->image_url."' alt='".esc_attr($this->name)."'/>";
								}
							}
						}
	        		}
					break;
				//Times
				case '#_24HSTARTTIME':
				case '#_24HENDTIME':
					$time = ($result == '#_24HSTARTTIME') ? $this->start_time:$this->end_time;
					$replace = substr($time, 0,5);
					break;
				case '#_12HSTARTTIME':
				case '#_12HENDTIME':
					$time = ($result == '#_12HSTARTTIME') ? $this->start_time:$this->end_time;
					$replace = date('g:i A', strtotime($time));
					break;
				//Links
				case '#_EVENTPAGEURL': //Depreciated	
				case '#_LINKEDNAME': //Depreciated
				case '#_EVENTURL': //Just the URL
				case '#_EVENTLINK': //HTML Link
					//If this event is not of this blog, we need a new URL
					$EM_URI = EM_URI;
					if( is_multisite() && get_site_option('dbem_ms_global_events') && get_site_option('dbem_ms_global_events_links') && !empty($this->blog_id) && is_main_site() && $this->blog_id != get_current_blog_id() ){
						$EM_URI = get_blog_permalink($this->blog_id, get_blog_option($this->blog_id, 'dbem_events_page'));
					}
					$joiner = (stristr($EM_URI, "?")) ? "&amp;" : "?";
					$event_link = esc_url($EM_URI.$joiner."event_id=".$this->id);
					if($result == '#_LINKEDNAME' || $result == '#_EVENTLINK'){
						$replace = '<a href="'.$event_link.'" title="'.esc_attr($this->name).'">'.esc_attr($this->name).'</a>';
					}else{
						$replace = $event_link;	
					}
					break;
				case '#_EDITEVENTURL':
				case '#_EDITEVENTLINK':
					if( $this->can_manage('edit_events','edit_others_events') ){
						if( is_multisite() && get_site_option('dbem_ms_global_events') && get_site_option('dbem_ms_global_events_links') && !empty($this->blog_id) && is_main_site() && $this->blog_id != get_current_blog_id() ){
							$replace = get_site_url($this->blog_id, "/wp-admin/admin.php?page=events-manager-event&amp;event_id={$this->id}");
						}else{
							$replace = esc_url(get_bloginfo('wpurl')."/wp-admin/admin.php?page=events-manager-event&amp;event_id={$this->id}");
						}
						if( $result == '#_EDITEVENTLINK'){
							$replace = '<a href="'.$replace.'">'.esc_html(__('Edit', 'dbem').' '.__('Event', 'dbem')).'</a>';
						}
					}	 
					break;
				//Bookings
				case '#_ADDBOOKINGFORM': //Depreciated
				case '#_REMOVEBOOKINGFORM': //Depreciated
				case '#_BOOKINGFORM':
					if( get_option('dbem_rsvp_enabled')){
						ob_start();
						$template = em_locate_template('placeholders/bookingform.php', true, array('EM_Event'=>$this));
						if( !defined('EM_BOOKING_JS_LOADED') ){
							//this kicks off the Javascript required by booking forms. This is fired once for all booking forms on a page load and appears at the bottom of the page
							//your theme must call the wp_footer() function for this to work (as required by many other plugins too) 
							function em_booking_js_footer(){
								?>		
								<script type="text/javascript">
									jQuery(document).ready( function($){	
										<?php
											//we call the segmented JS files and include them here
											include(WP_PLUGIN_DIR.'/events-manager/includes/js/bookingsform.js'); 
											do_action('em_gateway_js'); 
										?>							
									});
								</script>
								<?php
							}
							add_action('wp_footer','em_booking_js_footer');
							define('EM_BOOKING_JS_LOADED',true);
						}
						$replace = ob_get_clean();
					}
					break;
				case '#_BOOKINGBUTTON':
					if( get_option('dbem_rsvp_enabled')){
						ob_start();
						$template = em_locate_template('placeholders/bookingbutton.php', true, array('EM_Event'=>$this));
						$replace = ob_get_clean();
					}
					break;
				case '#_AVAILABLESEATS': //Depreciated
				case '#_AVAILABLESPACES':
					if ($this->rsvp && get_option('dbem_rsvp_enabled')) {
					   $replace = $this->get_bookings()->get_available_spaces();
					} else {
						$replace = "0";
					}
					break;
				case '#_BOOKEDSEATS': //Depreciated
				case '#_BOOKEDSPACES':
					if ($this->rsvp && get_option('dbem_rsvp_enabled')) {
					   $replace = $this->get_bookings()->get_booked_spaces();
					} else {
						$replace = "0";
					}
					break;
				case '#_PENDINGSPACES':
					if ($this->rsvp && get_option('dbem_rsvp_enabled')) {
					   $replace = $this->get_bookings()->get_pending_spaces();
					} else {
						$replace = "0";
					}
					break;
				case '#_SEATS': //Depreciated
				case '#_SPACES':
					$replace = $this->get_spaces();
					break;
				case '#_BOOKINGSURL':
				case '#_BOOKINGSLINK':
					if( $this->can_manage('manage_bookings','manage_others_bookings') ){
						$bookings_link = esc_url(get_bloginfo ( 'wpurl' )."/wp-admin/admin.php?page=events-manager-bookings&amp;event_id=".$this->id);
						if($result == '#_BOOKINGSLINK'){
							$replace = '<a href="'.$bookings_link.'" title="'.esc_attr($bookings_link).'">'.esc_html($this->name).'</a>';
						}else{
							$replace = $bookings_link;	
						}
					}
					break;
				//Contact Person
				case '#_CONTACTNAME':
				case '#_CONTACTPERSON': //Depreciated (your call, I think name is better)
					$replace = $this->contact->display_name;
					break;
				case '#_CONTACTUSERNAME':
					$replace = $this->contact->user_login;
					break;
				case '#_CONTACTEMAIL':
				case '#_CONTACTMAIL': //Depreciated
					$replace = $this->contact->user_email;
					break;
				case '#_CONTACTID':
					$replace = $this->contact->ID;
					break;
				case '#_CONTACTPHONE':
		      		$replace = ( $this->contact->phone != '') ? $this->contact->phone : __('N/A', 'dbem');
					break;
				case '#_CONTACTAVATAR': 
					$replace = get_avatar( $this->contact->ID, $size = '50' ); 
					break;
				case '#_CONTACTPROFILELINK':
				case '#_CONTACTPROFILEURL':
					if( function_exists('bp_core_get_user_domain') ){
						$replace = bp_core_get_user_domain($this->contact->ID);
						if( $result == '#_CONTACTPROFILELINK' ){
							$replace = '<a href="'.esc_url($replace).'">'.__('Profile', 'dbem').'</a>';
						}
					}
					break;
				case '#_CONTACTPROFILELINK':
				case '#_CONTACTPROFILEURL':
					if( function_exists('bp_core_get_user_domain') ){
						$replace = bp_core_get_user_domain($this->contact->ID);
						if( $result == '#_CONTACTPROFILELINK' ){
							$replace = '<a href="'.esc_url($replace).'">'.__('Profile', 'dbem').'</a>';
						}
					}
					break;
				case '#_ATTENDEES':
					ob_start();
					$template = em_locate_template('placeholders/attendees.php', true, array('EM_Event'=>$this));
					$replace = ob_get_clean();
					break;
				case '#_CATEGORIES':
					ob_start();
					$template = em_locate_template('placeholders/categories.php', true, array('EM_Event'=>$this));
					$replace = ob_get_clean();
					break;
				default:
					$replace = $full_result;
					break;
			}
			$replace = apply_filters('em_event_output_placeholder', $replace, $this, $full_result, $target );
			$event_string = str_replace($full_result, $replace , $event_string );
		}
		//Time placeholders
		foreach($placeholders[1] as $result) {
			// matches all PHP START date and time placeholders
			if (preg_match('/^#[dDjlNSwzWFmMntLoYyaABgGhHisueIOPTZcrU]$/', $result)) {
				$replace = date_i18n(ltrim($result, "#"), $this->start);
				$replace = apply_filters('em_event_output_placeholder', $replace, $this, $result, $target);
				$event_string = str_replace($result, $replace, $event_string );
			}
			// matches all PHP END time placeholders for endtime
			if (preg_match('/^#@[dDjlNSwzWFmMntLoYyaABgGhHisueIOPTZcrU]$/', $result)) {
				$replace = date_i18n(ltrim($result, "#@"), $this->end);
				$replace = apply_filters('em_event_output_placeholder', $replace, $this, $result, $target);
				$event_string = str_replace($result, $replace, $event_string ); 
		 	}
		}
		//Time place holder that doesn't show if empty.
		//TODO add filter here too
		preg_match_all('/#@?_\{[A-Za-z0-9 -\/,\.\\\]+\}/', $format, $results);
		foreach($results[0] as $result) {
			if(substr($result, 0, 3 ) == "#@_"){
				$date = 'end_date';
				$offset = 4;
			}else{
				$date = 'start_date';
				$offset = 3;
			}
			if( $date == 'end_date' && $this->end_date == $this->start_date ){
				$replace = __( apply_filters('em_event_output_placeholder', '', $this, $result, $target) );
			}else{
				$replace = __( apply_filters('em_event_output_placeholder', mysql2date(substr($result, $offset, (strlen($result)-($offset+1)) ), $this->$date), $this, $result, $target) );
			}
			$event_string = str_replace($result,$replace,$event_string );
		}
		//This is for the custom attributes
		preg_match_all('/#_ATT\{([^}]+)\}(\{([^}]+)\})?/', $format, $results);
		foreach($results[0] as $resultKey => $result) {
			//Strip string of placeholder and just leave the reference
			$attRef = substr( substr($result, 0, strpos($result, '}')), 6 );
			$attString = '';
			if( is_array($this->attributes) && array_key_exists($attRef, $this->attributes) ){
				$attString = $this->attributes[$attRef];
			}elseif( !empty($results[3][$resultKey]) ){
				//Check to see if we have a second set of braces;
				$attString = $results[3][$resultKey];
			}
			$attString = apply_filters('em_event_output_placeholder', $attString, $this, $result, $target);
			$event_string = str_replace($result, $attString ,$event_string );
		}
		
		//Now do dependent objects
		$event_string = $this->location->output($event_string, $target);
		
		//for backwards compat and easy use, take over the individual category placeholders with the frirst cat in th elist.
		$EM_Categories = $this->get_categories();
		if( count($EM_Categories->categories) > 0 ){
			$EM_Category = $EM_Categories->categories[0];
		}	
		if( empty($EM_Category) ) $EM_Category = new EM_Category();
		$event_string = $EM_Category->output($event_string, $target);
		
		return apply_filters('em_event_output', $event_string, $this, $format, $target);
	}
	
	/**********************************************************
	 * RECURRENCE METHODS
	 ***********************************************************/
	
	/**
	 * Saves events and replaces old ones. Returns true if sucecssful or false if not.
	 * @return boolean
	 */
	function save_events() {
		if( $this->is_recurring() && $this->can_manage('edit_events','edit_others_events') ){
			do_action('em_event_save_events_pre', $this); //actions/filters only run if event is recurring
			global $wpdb;
			$matching_days = $this->get_recurrence_days(); //Get days where events recur
			$this->delete_events(); //Delete old events beforehand
			//Make template event (and we just change dates)
			$event = $this->to_array();
			unset($event['event_id']); //remove id and we have a event template to feed to wpdb insert
			$event['event_date_created'] = current_time('mysql'); //since the recurrences are recreated
			unset($event['event_date_modified']);		
			$event['event_attributes'] = serialize($event['event_attributes']);
			foreach($event as $key => $value ){ //remove recurrence information
				if( substr($key, 0, 10) == 'recurrence' ){
					unset($event[$key]);
				}
			}
			$event['recurrence_id'] = $this->id;
			//Save event template with different dates
			$event_saves = array();
			$event_ids = array();
			if( count($matching_days) > 0 ){
				foreach( $matching_days as $day ) {
					$event['event_start_date'] = date("Y-m-d", $day);
					$event['event_slug'] = $this->slug.'-'.$event['event_start_date'];
					$event['event_end_date'] = $event['event_start_date'];		
					$event_saves[] = $wpdb->insert(EM_EVENTS_TABLE, $event, $this->get_types($event));
					$event_ids[] = $wpdb->insert_id;
					//if( EM_DEBUG ){ echo "Entering recurrence " . date("D d M Y", $day)."<br/>"; }
			 	}
			 	//save bookings
			 	if( $this->rsvp ){
			 		$inserts = array();
			 		foreach($this->get_bookings()->get_tickets() as $EM_Ticket){
			 			/* @var $EM_Ticket EM_Ticket */
			 			//get array, modify event id and insert
			 			$ticket = $EM_Ticket->to_array();
			 			unset($ticket['ticket_id']);
			 			//clean up ticket values
			 			foreach($ticket as $k => $v){
			 				if( empty($v) && $k != 'ticket_name' ){ 
			 					$ticket[$k] = 'NULL';
			 				}else{
			 					$ticket[$k] = "'$v'";
			 				}
			 			}
			 			foreach($event_ids as $event_id){
			 				$ticket['event_id'] = $event_id;
			 				$inserts[] = "(".implode(",",$ticket).")";
			 			}
			 		}
			 		$keys = "(".implode(",",array_keys($ticket)).")";
			 		$values = implode(',',$inserts);
			 		$sql = "INSERT INTO ".EM_TICKETS_TABLE." $keys VALUES $values";
			 		$result = $wpdb->query($sql);
			 	}
			 	//save categories
			 	$category_ids = $this->get_categories()->get_ids();
			 	$inserts = array();
			 	foreach($event_ids as $event_id){
			 		//create the meta inserts for each event
			 		foreach($category_ids as $category_id){
			 			$inserts[] = "($event_id,'event-category', $category_id)";
			 		}
			 	}
			 	if( count($inserts) > 0 ){
				 	$result = $wpdb->query("INSERT INTO ".EM_META_TABLE." (object_id,meta_key,meta_value) VALUES ".implode(',',$inserts));
				 	if($result === false){
				 		$this->add_error('There was a problem adding categories to your recurring events.','dbem');
				 	}
			 	}
			}else{
		 		$this->add_error('You have not defined a date range long enough to create a recurrence.','dbem');
		 		$result = false;
		 	}
		 	return apply_filters('em_event_save_events', !in_array(false, $event_saves) && $result !== false, $this, $event_ids);
		}
		return apply_filters('em_event_save_events', false, $this, $event_ids);
	}
	
	/**
	 * Removes all reoccurring events.
	 * @param $recurrence_id
	 * @return null
	 */
	function delete_events(){
		global $wpdb;
		do_action('em_event_delete_events_pre', $this);
		//So we don't do something we'll regret later, we could just supply the get directly into the delete, but this is safer
		$result = false;
		if( $this->can_manage('delete_events', 'delete_others_events') ){
			$EM_Events = EM_Events::get( array('recurrence_id'=>$this->id, 'scope'=>'all') );
			$event_ids = array();
			foreach($EM_Events as $EM_Event){
				if($EM_Event->recurrence_id == $this->id){
					$event_ids[] = $EM_Event->id; //ONLY ADD if id's match - hard coded
				}
			}
			$result = EM_Events::delete( $event_ids );
		}
		return apply_filters('delete_events', $result, $this, $event_ids);
	}
	
	/**
	 * Returns true if this event is a recurring event, meaning that it's not an individual event, 
	 * but an event that defines many events that recur over a span of time.
	 * For checking if a specific event is part of a greater set of recurring events, use is_recurrence()
	 * @return boolean
	 */
	function is_recurring(){
		return ( $this->recurrence );
	}	
	/**
	 * Will return true if this individual event is part of a set of events that recur
	 * For checking if this is the "master recurring event", see is_recurring() 
	 * @return boolean
	 */
	function is_recurrence(){
		return ( $this->id > 0 && $this->recurrence_id > 0 );
	}
	/**
	 * Returns if this is an individual event and is not recurring or a recurrence
	 * @return boolean
	 */
	function is_individual(){
		return ( !$this->is_recurring() && !$this->is_recurrence() );
	}
	
	/**
	 * Can the user manage this? 
	 */
	function can_manage( $owner_capability = false, $admin_capability = false ){
		if( $owner_capability == 'edit_events' && $this->id == '' && !is_user_logged_in() && get_option('dbem_events_anonymous_submissions') ){
			return apply_filters('em_event_can_manage',true, $this);
		}
		return apply_filters('em_event_can_manage', parent::can_manage($owner_capability, $admin_capability), $this);
	}
	
	/**
	 * Returns the days that match the recurrance array passed (unix timestamps)
	 * @param array $recurrence
	 * @return array
	 */
	function get_recurrence_days(){
		if( $this->is_recurring() ){
			
			$start_date = strtotime($this->start_date);
			$end_date = strtotime($this->end_date);
					
			$weekdays = explode(",", $this->byday); //what days of the week (or if monthly, one value at index 0)
			 
			$matching_days = array(); 
			$aDay = 86400;  // a day in seconds
			$aWeek = $aDay * 7;		 
				
			//TODO can this be optimized?
			switch ( $this->freq ){
				case 'daily':
					//If daily, it's simple. Get start date, add interval timestamps to that and create matching day for each interval until end date.
					$current_date = $start_date;
					while( $current_date <= $end_date ){
						$matching_days[] = $current_date;
						$current_date = $current_date + ($aDay * $this->interval);
					}
					break;
				case 'weekly':
					//sort out week one, get starting days and then days that match time span of event (i.e. remove past events in week 1)
					$start_of_week = get_option('start_of_week'); //Start of week depends on wordpress
					//first, get the start of this week as timestamp
					$event_start_day = date('w', $start_date);
					$offset = 0;
					if( $event_start_day > $start_of_week ){
						$offset = $event_start_day - $start_of_week; //x days backwards
					}elseif( $event_start_day < $start_of_week ){
						$offset = $start_of_week;
					}
					$start_week_date = $start_date - ( ($event_start_day - $start_of_week) * $aDay );
					//then get the timestamps of weekdays during this first week, regardless if within event range
					$start_weekday_dates = array(); //Days in week 1 where there would events, regardless of event date range
					for($i = 0; $i < 7; $i++){
						$weekday_date = $start_week_date+($aDay*$i); //the date of the weekday we're currently checking
						$weekday_day = date('w',$weekday_date); //the day of the week we're checking, taking into account wp start of week setting
						if( in_array( $weekday_day, $weekdays) ){
							$start_weekday_dates[] = $weekday_date; //it's in our starting week day, so add it
						}
					}					
					//for each day of eventful days in week 1, add 7 days * weekly intervals
					foreach ($start_weekday_dates as $weekday_date){
						//Loop weeks by interval until we reach or surpass end date
						while($weekday_date <= $end_date){
							if( $weekday_date >= $start_date && $weekday_date <= $end_date ){
								$matching_days[] = $weekday_date;
							}
							$weekday_date = $weekday_date + ($aWeek *  $this->interval);
						}
					}//done!
					break;  
				case 'monthly':
					//loop months starting this month by intervals
					$current_arr = getdate($start_date);
					$end_arr = getdate($end_date);
					$end_month_date = strtotime( date('Y-m-t', $end_date) ); //End date on last day of month
					$current_date = strtotime( date('Y-m-1', $start_date) ); //Start date on first day of month
					while( $current_date <= $end_month_date ){
						$last_day_of_month = date('t', $current_date);
						//Now find which day we're talking about
						$current_week_day = date('w',$current_date);
						$matching_month_days = array();
						//Loop through days of this years month and save matching days to temp array
						for($day = 1; $day <= $last_day_of_month; $day++){
							if($current_week_day == $this->byday){
								$matching_month_days[] = $day;
							}
							$current_week_day = ($current_week_day < 6) ? $current_week_day+1 : 0;							
						}
						//Now grab from the array the x day of the month
						$matching_day = ($this->byweekno > 0) ? $matching_month_days[$this->byweekno-1] : array_pop($matching_month_days);
						$matching_date = strtotime(date('Y-m',$current_date).'-'.$matching_day);
						if($matching_date >= $start_date && $matching_date <= $end_date){
							$matching_days[] = $matching_date;
						}
						//add the number of days in this month to make start of next month
						$current_arr['mon'] += $this->interval;
						if($current_arr['mon'] > 12){
							//FIXME this won't work if interval is more than 12
							$current_arr['mon'] = $current_arr['mon'] - 12;
							$current_arr['year']++;
						}
						$current_date = strtotime("{$current_arr['year']}-{$current_arr['mon']}-1"); 
					}
					break;
			}	
			sort($matching_days);
			//TODO delete this after testing
			/*Delete*/
			$test_dates = array();
			foreach($matching_days as $matching_day){
				$test_dates[] = date('d/m/Y', $matching_day);
			}	
			/*end delete*/		
			return $matching_days;
		}
	}
	
	function get_recurrence(){
		global $EM_Recurrences;
		if( is_array($EM_Recurrences) && array_key_exists($this->recurrence_id, $EM_Recurrences) && is_object($EM_Recurrences[$this->recurrence_id]) && get_class($EM_Recurrences[$this->recurrence_id]) == 'EM_Event' ){
			$recurrence = $EM_Recurrences[$this->recurrence_id];
		}else{
			//get this recurrence
			$recurrence = new EM_Event($this->recurrence_id);
			$EM_Recurrences[$this->recurrence_id] = $recurrence;
		}
		return $recurrence;
	}
	
	/**
	 * Returns a string representation of this recurrence. Will return false if not a recurrence
	 * @return string
	 */
	function get_recurrence_description() { 
		if( $this->is_individual() ) return false;
		$recurrence = $this->get_recurrence()->to_array();
		$weekdays_name = array(__('Sunday', 'dbem'),__('Monday', 'dbem'),__('Tuesday', 'dbem'),__('Wednesday', 'dbem'),__('Thursday', 'dbem'),__('Friday', 'dbem'),__('Saturday', 'dbem'));
		$monthweek_name = array('1' => __('the first %s of the month', 'dbem'),'2' => __('the second %s of the month', 'dbem'), '3' => __('the third %s of the month', 'dbem'), '4' => __('the fourth %s of the month', 'dbem'), '-1' => __('the last %s of the month', 'dbem'));
		$output = sprintf (__('From %1$s to %2$s', 'dbem'),  $recurrence['event_start_date'], $recurrence['event_end_date']).", ";
		if ($recurrence['recurrence_freq'] == 'daily')  {
			$freq_desc =__('everyday', 'dbem');
			if ($recurrence['recurrence_interval'] > 1 ) {
				$freq_desc = sprintf (__("every %s days", 'dbem'), $recurrence['recurrence_interval']);
			}
		}elseif ($recurrence['recurrence_freq'] == 'weekly')  {
			$weekday_array = explode(",", $recurrence['recurrence_byday']);
			$natural_days = array();
			foreach($weekday_array as $day){
				array_push($natural_days, $weekdays_name[$day]);
			}
			$output .= implode(" and ", $natural_days);
			$freq_desc = ", " . __("every week", 'dbem');
			if ($recurrence['recurrence_interval'] > 1 ) {
				$freq_desc = ", ".sprintf (__("every %s weeks", 'dbem'), $recurrence['recurrence_interval']);
			}
			
		}elseif ($recurrence['recurrence_freq'] == 'monthly')  {
			$weekday_array = explode(",", $recurrence['recurrence_byday']);
			$natural_days = array();
			foreach($weekday_array as $day){
				array_push($natural_days, $weekdays_name[$day]);
			}
			$freq_desc = sprintf (($monthweek_name[$recurrence['recurrence_byweekno']]), implode(" and ", $natural_days));
			if ($recurrence['recurrence_interval'] > 1 ) {
				$freq_desc .= ", ".sprintf (__("every %s months",'dbem'), $recurrence['recurrence_interval']);
			}
		}else{
			$freq_desc = "[ERROR: corrupted database record]";
		}
		$output .= $freq_desc;
		return  $output;
	}
	
	/**********************************************************
	 * UTILITIES
	 ***********************************************************/

	/**
	 * Returns this object in the form of an array, useful for saving directly into the wp_dbem_events table.
	 * @param boolean $for_database
	 * @return array
	 */
	function to_array($for_database = false){
		$event = array();
		//Core Event Data
		foreach ( $this->fields as $key => $val ) {
			//TODO does it matter if it's for db or not... shouldn't it just not include blanks?
			if( !$for_database || $for_database && $this->$val['name'] != '' ){
				$event[$key] = $this->$val['name'];
			}
		}
		return $event;
	}
}

//TODO placeholder targets filtering could be streamlined better
/**
 * This is a temporary filter function which mimicks the old filters in the old 2.x placeholders function
 * @param string $result
 * @param EM_Event $event
 * @param string $placeholder
 * @param string $target
 * @return mixed
 */
function em_event_output_placeholder($result,$event,$placeholder,$target='html'){	
	if( ($placeholder == "#_EXCERPT" || $placeholder == "#_LOCATIONEXCERPT") && $target == 'html' ){
		$result = apply_filters('dbem_notes_excerpt', $result);
	}elseif( $placeholder == '#_CONTACTEMAIL' && $target == 'html' ){
		$result = em_ascii_encode($event->contact->user_email);
	}elseif( $placeholder == "#_NOTES" || $placeholder == "#_EXCERPT" || $placeholder == "#_LOCATIONEXCERPT" ){
		if($target == 'html'){
			$result = apply_filters('dbem_notes', $result);
		}elseif($target == 'map'){
			$result = apply_filters('dbem_notes_map', $result);
		}elseif($target == 'ical'){
			$result = apply_filters('dbem_notes_ical', $result);
		}else{
			$result = apply_filters('dbem_notes_rss', $result);
			$result = apply_filters('the_content_rss', $result);
		}
	}elseif( in_array($placeholder, array("#_NAME",'#_ADDRESS','#_LOCATION','#_TOWN')) ){
		if ($target == "html"){    
			$result = apply_filters('dbem_general', $result); 
	  	}elseif ($target == "ical"){    
			$result = apply_filters('dbem_general_ical', $result); 
	  	}else{
			$result = apply_filters('dbem_general_rss', $result);
	  	}				
	}
	return $result;
}
add_filter('em_event_output_placeholder','em_event_output_placeholder',1,4);
?>