<?php
/**
 * Performs actions on init. This works for both ajax and normal requests, the return results depends if an em_ajax flag is passed via POST or GET.
 */
function em_init_actions() {
	global $wpdb,$EM_Notices,$EM_Event; 
	if( defined('DOING_AJAX') && DOING_AJAX ) $_REQUEST['em_ajax'] = true;
	
	//NOTE - No EM objects are globalized at this point, as we're hitting early init mode.
	//TODO Clean this up.... use a uniformed way of calling EM Ajax actions
	if( !empty($_REQUEST['em_ajax']) || !empty($_REQUEST['em_ajax_action']) ){
		if(isset($_REQUEST['em_ajax_action']) && $_REQUEST['em_ajax_action'] == 'get_location') {
			if(isset($_REQUEST['id'])){
				$EM_Location = new EM_Location($_REQUEST['id']);
				$location_array = $EM_Location->to_array();
				$location_array['location_balloon'] = $EM_Location->output(get_option('dbem_location_baloon_format'));
		     	echo EM_Object::json_encode($location_array);
			}
			die();
		}   
	 	if(isset($_REQUEST['em_ajax_action']) && $_REQUEST['em_ajax_action'] == 'delete_ticket') {
			if(isset($_REQUEST['id'])){
				$EM_Ticket = new EM_Ticket($_REQUEST['id']);
				$result = $EM_Ticket->delete();
				if($result){
					$result = array('result'=>true);
				}else{
					$result = array('result'=>false, 'error'=>$EM_Ticket->feedback_message);
				}
			}else{
				$result = array('result'=>false, 'error'=>__('No ticket id provided','dbem'));	
			}			
		    echo EM_Object::json_encode($result);
			die();
		} 
		if(isset($_REQUEST['query']) && $_REQUEST['query'] == 'GlobalMapData') {
			$EM_Locations = EM_Locations::get( $_REQUEST );
			$json_locations = array();
			foreach($EM_Locations as $location_key => $EM_Location) {
				$json_locations[$location_key] = $EM_Location->to_array();
				$json_locations[$location_key]['location_balloon'] = $EM_Location->output(get_option('dbem_map_text_format'));
			}
			echo EM_Object::json_encode($json_locations);
		 	die();   
	 	}
	
		if(isset($_REQUEST['ajaxCalendar']) && $_REQUEST['ajaxCalendar']) {
			//FIXME if long events enabled originally, this won't show up on ajax call
			echo EM_Calendar::output($_REQUEST);
			die();
		}
	}
	
	//Event Actions
	if( !empty($_REQUEST['action']) && substr($_REQUEST['action'],0,5) == 'event' ){
		//Load the event object, with saved event if requested
		if( !empty($_REQUEST['event_id']) ){
			$EM_Event = new EM_Event($_REQUEST['event_id']);
		}else{
			$EM_Event = new EM_Event();
		}
		if( $_REQUEST['action'] == 'event_save' && (current_user_can('edit_events') || (!is_user_logged_in() && get_option('dbem_events_anonymous_submissions'))) ){
			//Check Nonces
			if( is_admin() ){
				if( !wp_verify_nonce($_REQUEST['_wpnonce'] && 'event_save') ) check_admin_referer('trigger_error');				
			}else{
				if( !wp_verify_nonce($_REQUEST['_wpnonce'] && 'event_save') ) exit('Trying to perform an illegal action.');
			}
			//Grab and validate submitted data
			if ( $EM_Event->get_post() && $EM_Event->save() ) { //EM_Event gets the event if submitted via POST and validates it (safer than to depend on JS)
				if( is_admin() ){
					$EM_Notices->add_confirm($EM_Event->feedback_message);
					$page = !empty($_REQUEST['pno']) ? $_REQUEST['pno']:'';
					$scope = !empty($_REQUEST['scope']) ? $_REQUEST['scope']:'';
					//wp_redirect( get_bloginfo('wpurl').'/wp-admin/admin.php?page=events-manager&pno='.$page.'&scope='.$scope.'&message='.urlencode($EM_Event->feedback_message));
				}else{
					$EM_Notices->add_confirm($EM_Event->feedback_message, true);
					$redirect = !empty($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : wp_get_referer();
					wp_redirect( $redirect );
				}
				$events_result = true;
			}else{
				$EM_Notices->add_error( $EM_Event->get_errors() );
				$events_result = false;				
			}
		}
		if ( $_REQUEST['action'] == 'event_duplicate' ) {
			global $EZSQL_ERROR;
			$EM_Event = $EM_Event->duplicate();
			if( $EM_Event === false ){
				$EM_Notices->add_error($EM_Event->errors, true);
			}else{
				if( $EM_Event->id == $_REQUEST['event_id'] ){
					$EM_Notices->add_confirm($EM_Event->feedback_message ." ". sprintf(__('You are now viewing the duplicated %s.', 'dbem'),__('event','dbem')), true);
				}else{
					$EM_Notices->add_confirm($EM_Event->feedback_message, true);
				}
			}
		}
		if ( $_REQUEST['action'] == 'event_delete' ) { 
			//DELETE action
			$selectedEvents = !empty($_REQUEST['events']) ? $_REQUEST['events']:'';
			if(  EM_Object::array_is_numeric($selectedEvents) ){
				$events_result = EM_Events::delete( $selectedEvents );
			}elseif( is_object($EM_Event) ){
				$events_result = $EM_Event->delete();
			}		
			$plural = (count($selectedEvents) > 1) ? __('Events','dbem'):__('Event','dbem');
			if($events_result){
				$message = ( is_object($EM_Event) ) ? $EM_Event->feedback_message : sprintf(__('%s successfully deleted.','dbem'),$plural);
				$EM_Notices->add_confirm( $message );
			}else{
				$message = ( is_object($EM_Event) ) ? $EM_Event->errors : sprintf(__('%s could not be deleted.','dbem'),$plural);
				$EM_Notices->add_confirm( $message );		
			}
		}elseif( $_REQUEST['action'] == 'event_approve' ){ 
			//Approve Action
			$events_result = $EM_Event->approve();
			if($events_result){
				$EM_Notices->add_confirm( $EM_Event->feedback_message );
			}else{
				$EM_Notices->add_error( $EM_Event->errors );			
			}
		}
		
		//AJAX Exit
		if( isset($events_result) && !empty($_REQUEST['em_ajax']) ){
			if( $events_result ){
				$return = array('result'=>true, 'message'=>$EM_Event->feedback_message);
			}else{		
				$return = array('result'=>false, 'message'=>$EM_Event->feedback_message, 'errors'=>$EM_Event->errors);
			}	
		}
	}
	
	//Location Actions
	if( !empty($_REQUEST['action']) && substr($_REQUEST['action'],0,8) == 'location' ){
		global $EM_Location, $EM_Notices;
		//Load the location object, with saved event if requested
		if( !empty($_REQUEST['location_id']) ){
			$EM_Location = new EM_Location($_REQUEST['location_id']);
		}else{
			$EM_Location = new EM_Location();
		}
		if( $_REQUEST['action'] == 'location_save' && current_user_can('edit_locations') ){
			//Check Nonces
			em_verify_nonce('location_save');
			//Grab and validate submitted data
			if ( $EM_Location->get_post() && $EM_Location->save() ) { //EM_location gets the location if submitted via POST and validates it (safer than to depend on JS)
				$EM_Notices->add_confirm($EM_Location->feedback_message);
				$result = true;
			}else{
				$EM_Notices->add_error( $EM_Location->get_errors() );
				$result = false;				
			}
		}elseif( !empty($_REQUEST['action']) && $_REQUEST['action'] == "location_delete" ){
			//delete location
			//get object or objects			
			if( !empty($_REQUEST['locations']) || !empty($_REQUEST['location_id']) ){
				$args = !empty($_REQUEST['locations']) ? $_REQUEST['locations']:$_REQUEST['location_id'];
				$locations = EM_Locations::get($args);
				foreach($locations as $location) {
					if( !$location->delete() ){
						$EM_Notices->add_error($location->get_errors());
						$errors = true;
					}			
				}
				if( empty($errors) ){
					$result = true;
					$location_term = ( count($locations) > 1 ) ?__('Locations', 'dbem') : __('Location', 'dbem'); 
					$EM_Notices->add_confirm( sprintf(__('%s successfully deleted', 'dbem'), $location_term) );
				}else{
					$result = false;
				}
			}
		}elseif( !empty($_REQUEST['action']) && $_REQUEST['action'] == "locations_search" && (!empty($_REQUEST['term']) || !empty($_REQUEST['q'])) ){
			$location_cond = ( !current_user_can('edit_others_locations') && !current_user_can('read_others_locations') ) ? "AND location_owner=".get_current_user_id() : '';
			$term = (isset($_REQUEST['term'])) ? '%'.$_REQUEST['term'].'%' : '%'.$_REQUEST['q'].'%';
			$sql = $wpdb->prepare("
				SELECT 
					location_id AS `id`,
					Concat( location_name, ', ', location_address, ', ', location_town)  AS `label`,
					location_name AS `value`,
					location_address AS `address`, 
					location_town AS `town`, 
					location_state AS `state`,
					location_postcode AS `postcode`,
					location_country AS `country`
				FROM ".EM_LOCATIONS_TABLE." 
				WHERE ( `location_name` LIKE %s ) $location_cond LIMIT 10
			", $term);
			echo EM_Object::json_encode($wpdb->get_results($sql));
			die();
		}
		if( isset($result) && $result && !empty($_REQUEST['em_ajax']) ){
			$return = array('result'=>true, 'message'=>$EM_Location->feedback_message);
			echo EM_Object::json_encode($return);
			die();
		}elseif( isset($result) && !$result && !empty($_REQUEST['em_ajax']) ){
			$return = array('result'=>false, 'message'=>$EM_Location->feedback_message, 'errors'=>$EM_Notices->get_errors());
			echo EM_Object::json_encode($return);
			die();
		}
	}
	
	//Category Actions
	if( !empty($_REQUEST['action']) && substr($_REQUEST['action'],0,8) == 'category' ){
		global $EM_Category, $EM_Notices;
		//Load the category object, with saved event if requested
		if( !empty($_REQUEST['category_id']) ){
			$EM_Category = new EM_Category($_REQUEST['category_id']);
		}else{
			$EM_Category = new EM_Category();
		}
		if( $_REQUEST['action'] == 'category_save' && current_user_can('edit_categories') ){
			//Check Nonces
			em_verify_nonce('category_save');
			//Grab and validate submitted data
			if ( $EM_Category->get_post() && $EM_Category->save() ) { //EM_Category gets the category if submitted via POST and validates it (safer than to depend on JS)
				$EM_Notices->add_confirm($EM_Category->feedback_message);
				$result = true;
			}else{
				$EM_Notices->add_error( $EM_Category->get_errors() );
				$result = false;				
			}
		}elseif( !empty($_REQUEST['action']) && $_REQUEST['action'] == "category_delete" ){
			//delete category
			//get object or objects			
			if( !empty($_REQUEST['categories']) || !empty($_REQUEST['category_id']) ){
				$args = !empty($_REQUEST['categories']) ? $_REQUEST['categories']:$_REQUEST['category_id'];
				$categories = EM_Categories::get($args);
				foreach($categories as $category) {
					if( !$category->delete() ){
						$EM_Notices->add_error($category->get_errors());
						$errors = true;
					}			
				}
				if( empty($errors) ){
					$result = true;
					$category_term = ( count($categories) > 1 ) ?__('EM_Categories', 'dbem') : __('Category', 'dbem'); 
					$EM_Notices->add_confirm( sprintf(__('%s successfully deleted', 'dbem'), $category_term) );
				}else{
					$result = false;
				}
			}
		}
		if( isset($result) && $result && !empty($_REQUEST['em_ajax']) ){
			$return = array('result'=>true, 'message'=>$EM_Category->feedback_message);
			echo EM_Object::json_encode($return);
			die();
		}elseif( isset($result) && !$result && !empty($_REQUEST['em_ajax']) ){
			$return = array('result'=>false, 'message'=>$EM_Category->feedback_message, 'errors'=>$EM_Notices->get_errors());
			echo EM_Object::json_encode($return);
			die();
		}
	}
	
	//Booking Actions
	if( !empty($_REQUEST['action']) && substr($_REQUEST['action'],0,7) == 'booking' && (is_user_logged_in() || ($_REQUEST['action'] == 'booking_add' && get_option('dbem_bookings_anonymous'))) ){
		global $EM_Event, $EM_Booking, $EM_Person;
		//Load the booking object, with saved booking if requested
		$EM_Booking = ( !empty($_REQUEST['booking_id']) ) ? new EM_Booking($_REQUEST['booking_id']) : new EM_Booking();
		//Load the event object, with saved event if requested
		$EM_Event = $EM_Booking->get_event();
		
		$allowed_actions = array('bookings_approve'=>'approve','bookings_reject'=>'reject','bookings_unapprove'=>'unapprove', 'bookings_delete'=>'delete');
		$result = false;
		if ( $_REQUEST['action'] == 'booking_add') {
			//ADD/EDIT Booking
			em_verify_nonce('booking_add');
			do_action('em_booking_add', $EM_Event, $EM_Booking);
			if( !is_user_logged_in() || get_option('dbem_bookings_double') || !$EM_Event->get_bookings()->has_booking(get_current_user_id()) ){
				if( $EM_Booking->get_post() ){
					//Does this user need to be registered first?
					$registration = true;
					//TODO do some ticket validation before registering the user
					if ( $EM_Event->get_bookings()->get_available_spaces() >= $EM_Booking->get_spaces(true) ) {
						if( !is_user_logged_in() && get_option('dbem_bookings_anonymous') ){
							//find random username - less options for user, less things go wrong
							$username_root = explode('@', $_REQUEST['user_email']);
							$username_rand = $username_root[0].rand(1,1000);
							while( username_exists($username_root[0].rand(1,1000)) ){
								$username_rand = $username_root[0].rand(1,1000);
							}
							$_REQUEST['user_phone'] = (!empty($_REQUEST['user_phone'])) ? $_REQUEST['user_phone']:''; //fix to prevent warnings
							$_REQUEST['user_name'] = (!empty($_REQUEST['user_name'])) ? $_REQUEST['user_name']:''; //fix to prevent warnings
							$user_data = array('user_login' => $username_rand, 'user_email'=> $_REQUEST['user_email'], 'user_name'=> $_REQUEST['user_name'], 'dbem_phone'=> $_REQUEST['dbem_phone']);
							$id = em_register_new_user($user_data);
							if( is_numeric($id) ){
								$EM_Person = new EM_Person($id);
								$EM_Booking->person_id = $id;
								$feedback = get_option('dbem_booking_feedback_new_user');
								$EM_Notices->add_confirm( $feedback );
							}else{
								$registration = false;
								if( is_object($id) && get_class($id) == 'WP_Error'){
									/* @var $id WP_Error */
									if( $id->get_error_code() == 'email_exists' ){
										$EM_Notices->add_error( get_option('dbem_booking_feedback_email_exists') );
									}else{
										$EM_Notices->add_error( $id->get_error_messages() );
									}
								}else{
									$EM_Notices->add_error( get_option('dbem_booking_feedback_reg_error') );
								}
							}
						}elseif( !is_user_logged_in() ){
							$registration = false;
							$EM_Notices->add_error( get_option('dbem_booking_feedback_log_in') );
						}
					}
					if( $registration && $EM_Event->get_bookings()->add($EM_Booking) ){
						$result = true;
						$EM_Notices->add_confirm( $EM_Event->get_bookings()->feedback_message );		
						$feedback = $EM_Event->get_bookings()->feedback_message;	
					}else{
						$result = false;
						ob_start();
						$EM_Booking->feedback_message = ob_get_clean();
						$EM_Notices->add_error( $EM_Event->get_bookings()->get_errors() );			
						$feedback = $EM_Event->get_bookings()->feedback_message;				
					}
				}else{
					$result = false;
					$EM_Notices->add_error( $EM_Booking->get_errors() );
				}
			}else{
				$result = false;
				$feedback = get_option('dbem_booking_feedback_already_booked');
				$EM_Notices->add_error( $feedback );
			}
	  	}elseif ( $_REQUEST['action'] == 'booking_add_one' && is_object($EM_Event) && is_user_logged_in() ) {
			//ADD/EDIT Booking
			em_verify_nonce('booking_add_one');
			if( !$EM_Event->get_bookings()->has_booking(get_current_user_id()) || get_option('dbem_bookings_double')){
				$EM_Booking = new EM_Booking(array('person_id'=>get_current_user_id(), 'event_id'=>$EM_Event->id)); //new booking
				$EM_Ticket = $EM_Event->get_bookings()->get_tickets()->get_first();			
				//get first ticket in this event and book one place there. similar to getting the form values in EM_Booking::get_post_values()
				$EM_Ticket_Booking = new EM_Ticket_Booking(array('ticket_id'=>$EM_Ticket->id, 'ticket_booking_spaces'=>1));
				$EM_Booking->get_tickets_bookings();
				$EM_Booking->tickets_bookings->booking = $EM_Booking;
				$EM_Booking->tickets_bookings->add( $EM_Ticket_Booking );
				//Now save booking
				if( $EM_Event->get_bookings()->add($EM_Booking) ){
					$result = true;
					$EM_Notices->add_confirm( $EM_Event->get_bookings()->feedback_message );		
					$feedback = $EM_Event->get_bookings()->feedback_message;	
				}else{
					$result = false;
					$EM_Notices->add_error( $EM_Event->get_bookings()->get_errors() );			
					$feedback = $EM_Event->get_bookings()->feedback_message;	
				}
			}else{
				$result = false;
				$feedback = get_option('dbem_booking_feedback_already_booked');
				$EM_Notices->add_error( $feedback );
			}
	  	}elseif ( $_REQUEST['action'] == 'booking_cancel') {
	  		//Cancel Booking
			em_verify_nonce('booking_cancel');
	  		if( $EM_Booking->can_manage() || ($EM_Booking->person->ID == get_current_user_id() && get_option('dbem_bookings_user_cancellation')) ){
				if( $EM_Booking->cancel() ){
					$result = true;
					if( !defined('DOING_AJAX') ){
						if( $EM_Booking->person->ID == get_current_user_id() ){
							$EM_Notices->add_confirm(sprintf(__('Booking %s','dbem'), __('Cancelled','dbem')), true );	
						}else{
							$EM_Notices->add_confirm( $EM_Booking->feedback_message, true );
						}
						wp_redirect( $_SERVER['HTTP_REFERER'] );
						exit();
					}
				}else{
					$EM_Notices->add_error( $EM_Booking->get_errors() );
					$feedback = $EM_Booking->feedback_message;
				}
			}else{
				$EM_Notices->add_error( __('You must log in to cancel your booking.', 'dbem') );
			}
	  	}elseif( array_key_exists($_REQUEST['action'], $allowed_actions) && $EM_Event->can_manage('manage_bookings','manage_others_bookings') ){
	  		//Event Admin only actions
			$action = $allowed_actions[$_REQUEST['action']];
			//Just do it here, since we may be deleting bookings of different events.
			if( !empty($_REQUEST['bookings']) && EM_Object::array_is_numeric($_REQUEST['bookings'])){
				$results = array();
				foreach($_REQUEST['bookings'] as $booking_id){
					$EM_Booking = new EM_Booking($booking_id);
					$result = $EM_Booking->$action();
					$results[] = $result;
					if( !in_array(false, $results) && !$result ){
						$feedback = $EM_Booking->feedback_message;
					}
				}
				$result = !in_array(false,$results);
			}elseif( is_object($EM_Booking) ){
				$result = $EM_Booking->$action();
				$feedback = $EM_Booking->feedback_message;
			}
			//FIXME not adhereing to object's feedback or error message, like other bits in this file.
			//TODO multiple deletion won't work in ajax
			if( !empty($_REQUEST['em_ajax']) ){
				if( $result ){
					echo $feedback;
				}else{
					echo '<span style="color:red">'.$feedback.'</span>';
				}	
				die();
			}
		}elseif( $_REQUEST['action'] == 'booking_save' ){
			em_verify_nonce('booking_save');
			do_action('em_booking_save', $EM_Event, $EM_Booking);
			if( $EM_Booking->can_manage('manage_bookings','manage_others_bookings') ){
				if ($EM_Booking->get_post(true) && $EM_Booking->save(false) ){
					$result = true;
					$EM_Notices->add_confirm( $EM_Booking->feedback_message );		
					$feedback = $EM_Booking->feedback_message;	
				}else{
					$result = false;
					$EM_Notices->add_error( $EM_Booking->get_errors() );			
					$feedback = $EM_Booking->feedback_message;	
				}	
			}
		}
		if( $result && defined('DOING_AJAX') ){
			$return = array('result'=>true, 'message'=>$feedback);
			echo EM_Object::json_encode($return);
			die();
		}elseif( !$result && defined('DOING_AJAX') ){
			$return = array('result'=>false, 'message'=>$feedback, 'errors'=>$EM_Notices->get_errors());
			echo EM_Object::json_encode($return);
			die();
		}
	}elseif( !empty($_REQUEST['action']) && $_REQUEST['action'] == 'booking_add' && !is_user_logged_in() && !get_option('dbem_bookings_anonymous')){
		$EM_Notices->add_error(__('You must log in before you make a booking.','dbem'));
		if( !$result && defined('DOING_AJAX') ){
			$return = array('result'=>false, 'message'=>$EM_Booking->feedback_message, 'errors'=>$EM_Notices->get_errors());
			echo EM_Object::json_encode($return);
		}
		die();
	}
	
	//AJAX call for searches
	if( !empty($_REQUEST['action']) && substr($_REQUEST['action'],0,6) == 'search' ){
		if( $_REQUEST['action'] == 'search_states' && wp_verify_nonce($_REQUEST['_wpnonce'], 'search_states') ){
			$results = array();
			if( !empty($_REQUEST['country']) ){
				$results = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT location_state AS value, location_country AS country, CONCAT(location_state, ', ', location_country) AS label FROM " . EM_LOCATIONS_TABLE ." WHERE location_state IS NOT NULL AND location_state != '' AND location_country=%s", $_REQUEST['country']));
			}elseif( !empty($_REQUEST['region']) ){
				$results = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT location_state AS value, location_country AS country, CONCAT(location_state, ', ', location_country) AS label FROM " . EM_LOCATIONS_TABLE ." WHERE location_state IS NOT NULL AND location_state != '' AND location_region=%s", $_REQUEST['region']));
			}
			if( $_REQUEST['return_html'] ) {
				//quick shortcut for quick html form manipulation
				ob_start();
				?>
				<option value=''><?php _e('All States','dbem'); ?></option>
				<?php			
				foreach( $results as $result ){
					echo "<option>{$result->value}</option>";
				}
				$return = ob_get_clean();
				echo apply_filters('em_ajax_search_states', $return);
				exit();
			}else{
				echo EM_Object::json_encode($results);
				exit();
			}
		}
		if( $_REQUEST['action'] == 'search_regions' && wp_verify_nonce($_REQUEST['_wpnonce'], 'search_regions') ){
			if( !empty($_REQUEST['country']) ){
				$results = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT location_region AS value, location_country AS country, CONCAT(location_region, ', ', location_country) AS label FROM " . EM_LOCATIONS_TABLE ." WHERE location_region IS NOT NULL AND location_region != '' AND location_country=%s", $_REQUEST['country']));
			}else{
				$results = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT location_region AS value, location_country AS country, CONCAT(location_region, ', ', location_country) AS label FROM " . EM_LOCATIONS_TABLE ." WHERE location_region IS NOT NULL AND location_region != ''", $_REQUEST['country']));
			}
			if( $_REQUEST['return_html'] ) {
				//quick shortcut for quick html form manipulation
				ob_start();
				?>
				<option value=''><?php _e('All Regions','dbem'); ?></option>
				<?php	
				foreach( $results as $result ){
					echo "<option>{$result->value}</option>";
				}
				$return = ob_get_clean();
				echo apply_filters('em_ajax_search_regions', $return);
				exit();
			}else{
				echo EM_Object::json_encode($results);
				exit();
			}
		}elseif( $_REQUEST['action'] == 'search_events' && wp_verify_nonce($_POST['_wpnonce'], 'search_events') && get_option('dbem_events_page_search') ){
			$args = EM_Events::get_post_search();
			ob_start();
			em_locate_template('templates/events-list.php', true, array('args'=>$args)); //if successful, this template overrides the settings and defaults, including search
			echo apply_filters('em_ajax_search_events', ob_get_clean(), $args);	
			exit();			
		}
	}
		
	//EM Ajax requests require this flag.
	if( is_user_logged_in() ){
		//Admin operations
		//Specific Oject Ajax
		if( !empty($_REQUEST['em_obj']) ){
			switch( $_REQUEST['em_obj'] ){
				case 'em_bookings_events_table':
				case 'em_bookings_pending_table':
				case 'em_bookings_confirmed_table':
					//add some admin files just in case
					include_once('admin/bookings/em-confirmed.php');
					include_once('admin/bookings/em-events.php');
					include_once('admin/bookings/em-pending.php');
					call_user_func($_REQUEST['em_obj']);
					exit();
					break;
			}
		}
	}	
}  
add_action('init','em_init_actions');

?>