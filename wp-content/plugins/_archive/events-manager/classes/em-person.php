<?php
// TODO make person details more secure and integrate with WP user data 
class EM_Person extends WP_User{
	
	function EM_Person( $person_id = false, $username = false ){
		if( is_array($person_id) ){
			if( array_key_exists('person_id',$person_id) ){
				$person_id = $person_id['person_id'];
			}elseif ( array_key_exists('user_id',$person_id) ){
				$person_id = $person_id['user_id'];
			}else{
				$person_id = $person_id['ID'];
			}
		}elseif( is_object($person_id) && get_class($person_id) == 'WP_User'){
			$person_id = $person_id->ID; //create new object if passed a wp_user
		}
		if($username){
			parent::__construct($person_id, $username);
		}elseif( is_numeric($person_id) && $person_id == 0 ){
			$this->ID = 0;
			$this->display_name = 'Non-Registered User';
			$this->user_email = 'n/a';
		}else{
			parent::__construct($person_id);
		}
		$this->phone = get_metadata('user', $this->ID, 'dbem_phone', true); //extra field for EM
		do_action('em_person',$this, $person_id, $username);
	}
	
	function get_bookings($ids_only = false){
		global $wpdb;
		$blog_condition = '';
		if( is_multisite() ){
			if( !is_main_site() ){
				//not the main blog, force single blog search
				$blog_condition = "AND e.blog_id=".get_current_blog_id();
			}elseif(is_main_site() && !get_option('dbem_ms_global_events')){
				$blog_condition = "AND (e.blog_id=".get_current_blog_id().' OR e.blog_id IS NULL)';
			}
		}		
		$EM_Booking = new EM_Booking(); //empty booking for fields
		$results = $wpdb->get_results("SELECT b.".implode(', b.', array_keys($EM_Booking->fields))." FROM ".EM_BOOKINGS_TABLE." b, ".EM_EVENTS_TABLE." e WHERE e.event_id=b.event_id AND person_id={$this->ID} {$blog_condition} ORDER BY ".get_option('dbem_bookings_default_orderby','event_start_date')." ".get_option('dbem_bookings_default_order','ASC'),ARRAY_A);
		$bookings = array();
		if($ids_only){
			foreach($results as $booking_data){
				$bookings[] = $booking_data['booking_id'];
			}
			return apply_filters('em_person_get_bookings', $bookings, $this);
		}else{
			foreach($results as $booking_data){
				$bookings[] = new EM_Booking($booking_data);
			}
			return apply_filters('em_person_get_bookings', new EM_Bookings($bookings), $this);
		}
	}

	/**
	 * @return EM_Events
	 */
	function get_events(){
		global $wpdb;
		$events = array();
		foreach( $this->get_bookings()->get_bookings() as $EM_Booking ){
			$events[$EM_Booking->event_id] = $EM_Booking->get_event();
		}
		return apply_filters('em_person_get_events', $events);
	}
	
	function display_summary(){
		ob_start();
		?>
		<table>
			<tr>
				<td><?php echo get_avatar($this->ID); ?></td>
				<td style="padding-left:10px; vertical-align: top;">
					<strong><?php _e('Name','dbem'); ?></strong> : <a href="<?php echo EM_ADMIN_URL ?>&amp;page=events-manager-bookings&amp;person_id=<?php echo $this->ID; ?>"><?php echo $this->get_name() ?></a><br /><br />
					<strong><?php _e('Email','dbem'); ?></strong> : <?php echo $this->user_email; ?><br /><br />
					<strong><?php _e('Phone','dbem'); ?></strong> : <?php echo $this->phone; ?>
				</td>
			</tr>
		</table>
		<?php
		return apply_filters('em_person_display_summary', ob_get_clean(), $this);
	}
	
	function get_name(){
		$full_name = $this->user_firstname  . " " . $this->user_lastname ;
		$full_name = trim($full_name);
		$name = !empty($full_name) ? $full_name : $this->display_name;
		return apply_filters('em_person_get_name', $name, $this);
	}
}
?>