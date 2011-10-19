<?php
/**
 * Object that holds location info and related functions
 * @author marcus
 */
class EM_Location extends EM_Object {
	//DB Fields
	var $id = '';
	var $slug = '';
	var $name = '';
	var $address = '';
	var $town = '';
	var $state = '';
	var $postcode = '';
	var $region = '';
	var $country = '';
	var $latitude = '';
	var $longitude = '';
	var $description = '';
	var $owner = '';
	//Other Vars
	var $fields = array( 
		'location_id' => array('name'=>'id','type'=>'%d'), 
		'location_slug' => array('name'=>'slug','type'=>'%s'), 
		'location_name' => array('name'=>'name','type'=>'%s'), 
		'location_address' => array('name'=>'address','type'=>'%s'),
		'location_town' => array('name'=>'town','type'=>'%s'),
		'location_state' => array('name'=>'state','type'=>'%s'),
		'location_postcode' => array('name'=>'postcode','type'=>'%s'),
		'location_region' => array('name'=>'region','type'=>'%s'),
		'location_country' => array('name'=>'country','type'=>'%s'),
		'location_latitude' =>  array('name'=>'latitude','type'=>'%f'),
		'location_longitude' => array('name'=>'longitude','type'=>'%f'),
		'location_description' => array('name'=>'description','type'=>'%s'),
		'location_owner' => array('name'=>'owner','type'=>'%d')
	);
	var $image_url = '';
	var $required_fields = array();
	var $feedback_message = "";
	var $mime_types = array(1 => 'gif', 2 => 'jpg', 3 => 'png'); 
	var $errors = array();
	
	/**
	 * Gets data from POST (default), supplied array, or from the database if an ID is supplied
	 * @param $location_data
	 * @return null
	 */
	function EM_Location( $location_data = 0 ) {
		//Initialize
		$this->required_fields = array("name" => __('The location name', 'dbem'), "address" => __('The location address', 'dbem'), "town" => __('The location town', 'dbem'), "country" => __('The country', 'dbem'),);
		if( !empty($location_data) ){
			//Load location data
			if( is_array($location_data) && isset($location_data['location_name']) ){
				$location = $location_data;
			}elseif( !empty($location_data) ){
				//Retreiving from the database		
				global $wpdb;
				if( is_numeric($location_data) ){
					$sql = "SELECT * FROM ". EM_LOCATIONS_TABLE ." WHERE location_id ='{$location_data}'";   
				  	$location = $wpdb->get_row($sql, ARRAY_A);
				}else{
					$sql = "SELECT * FROM ". EM_LOCATIONS_TABLE ." WHERE location_slug ='{$location_data}'";   
				  	$location = $wpdb->get_row($sql, ARRAY_A);
				}
			}
			//If gmap is turned off, values may not be returned and set, so we set it here
			if(empty($location['location_latitude'])) {
				$location['location_latitude']  = 0;
				$location['location_longitude'] = 0;
			}
			//Save into the object
			$this->to_object($location, true);
			$this->get_image_url();
			add_filter('em_location_save',array(&$this,'image_upload'),1,2);
		} 
		do_action('em_location', $this, $location_data);
	}
	
	function get_post(){
		//We are getting the values via POST or GET
		do_action('em_location_get_post_pre', $this);
		$location = array();
		$location['location_id'] = ( !empty($_POST['location_id']) ) ? $_POST['location_id']:'';
		$location['location_name'] = ( !empty($_POST['location_name']) ) ? stripslashes($_POST['location_name']):'';
		$location['location_slug'] = ( !empty($_POST['location_slug']) ) ? sanitize_title($_POST['location_slug']) : '' ;
		if( current_user_can('edit_others_events') ){
			$location['location_owner'] = ( !empty($_POST['location_owner']) && is_numeric($_POST['location_owner']) ) ? $_POST['location_owner']:'';
		}
		$location['location_address'] = ( !empty($_POST['location_address']) ) ? stripslashes($_POST['location_address']):'';
		$location['location_town'] = ( !empty($_POST['location_town']) ) ? stripslashes($_POST['location_town']):'';
		$location['location_state'] = ( !empty($_POST['location_state']) ) ? stripslashes($_POST['location_state']):'';
		$location['location_postcode'] = ( !empty($_POST['location_postcode']) ) ? stripslashes($_POST['location_postcode']):'';
		$location['location_region'] = ( !empty($_POST['location_region']) ) ? stripslashes($_POST['location_region']):'';
		$location['location_country'] = ( !empty($_POST['location_country']) ) ? stripslashes($_POST['location_country']):'';
		$location['location_latitude'] = ( !empty($_POST['location_latitude']) ) ? $_POST['location_latitude']:'';
		$location['location_longitude'] = ( !empty($_POST['location_longitude']) ) ? $_POST['location_longitude']:'';
		$location['location_description'] = ( !empty($_POST['content']) ) ? stripslashes($_POST['content']):'';
		$this->to_object( apply_filters('em_location_get_post', $location, $this) );
		return apply_filters('em_location_get_post',$this->validate(),$this);
	}

	/**
	 * Validates the location. Should be run during any form submission or saving operation.
	 * @return boolean
	 */
	function validate(){
		//check required fields
		foreach ( $this->required_fields as $field => $description) {
			if( $field == 'country' && !array_key_exists($this->country, em_get_countries()) ){ 
				//country specific checking
				$this->add_error( $this->required_fields['country'].__(" is required.", "dbem") );				
			}elseif ( $this->$field == "" ) {
				$this->add_error( $description.__(" is required.", "dbem") );
			}
		}
		$this->image_validate();
		return apply_filters('em_location_validate', ( count($this->errors) == 0 ), $this);
	}
	
	function save(){
		global $wpdb, $current_user;
		$table = EM_LOCATIONS_TABLE;
		if( $this->validate() ){
			if( $this->can_manage('edit_locations','edit_others_locations') ){
				//owner person can be anyone the admin wants, but the creator if not.
				if( current_user_can('edit_others_events') ){
					$this->owner = ( $this->owner > 0 ) ? $this->owner:get_current_user_id();
				}else{
					//force user id - user is either editing a location or making a new one, as can_manage checks ownership too. 
					$this->owner = get_current_user_id();
				}
				get_currentuserinfo();
				do_action('em_location_save_pre', $this);
				$this->slug = $this->sanitize_title();
				$data = $this->to_array();
				unset($data['location_id']);
				unset($data['location_image_url']);
				if($this->id != ''){
					$where = array( 'location_id' => $this->id );
					$result = $wpdb->update($table, $data, $where, $this->get_types($data));
					if( $result !== false ){
						$this->feedback_message = sprintf(__('%s successfully updated.', 'dbem'), __('Location','dbem'));
					}else{
						$this->add_error( sprintf(__('Could not save the %s details due to a database error.', 'dbem'),__('location','dbem') ));
					}			
				}else{
					$result = $wpdb->insert($table, $data, $this->get_types($data));
				    $this->id = $wpdb->insert_id;   
					if( $result !== false ){
						$this->feedback_message = sprintf(__('%s successfully added.', 'dbem'), __('Location','dbem'));
					}else{
						$this->add_error( sprintf(__('Could not save the %s details due to a database error.', 'dbem'),__('location','dbem') ));
					}	
				}
				return apply_filters('em_location_save', ( $this->id > 0 && count($this->errors) == 0 ), $this);
			}else{
				$this->add_error( sprintf(__('You do not have permission to create/edit %s.','dbem'), __('locations','dbem')) );
			}
		}
		return apply_filters('em_location_save', false, $this, false);
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
		$slug_matches = $wpdb->get_results('SELECT location_id FROM '.EM_LOCATIONS_TABLE." WHERE location_slug='{$slug}'", ARRAY_A);
		if( count($slug_matches) > 0 ){ //we will check that the slug is unique
			if( $slug_matches[0]['location_id'] != $this->id || count($slug_matches) > 1 ){
				//we have a conflict, so try another alternative
				$this->slug = preg_replace('/\-[0-9]+$/', '', $slug).'-'.($iteration+1);
				$this->sanitize_title($iteration+1);
			}
		}
		return apply_filters('em_location_sanitize_title', $this->slug, $this);
	}
	
	function delete(){
		global $wpdb;	
		if( current_user_can('delete_locations') ){
			do_action('em_location_delete_pre', $this);
			$table_name = EM_LOCATIONS_TABLE;
			$sql = "DELETE FROM $table_name WHERE location_id = '{$this->id}';";
			$result = $wpdb->query($sql);
			$result = $this->image_delete() && $result;
			if( $result ){
				$this->feedback_message = sprintf(__('%s successfully deleted.', 'dbem'), __('Location','dbem')) ;
			}else{
				$this->add_error( sprintf(__('%s could not be deleted.', 'dbem'), __('Location','dbem')) );
			}
		}else{
			$this->add_error( sprintf(__('You do not have permission to delete %s.','dbem'), __('locations','dbem')) );
			$result = false;
		}
		return apply_filters('em_location_delete', $result, $this);
	}

	function load_similar($criteria){
		global $wpdb;
		if( !empty($criteria['location_name']) && !empty($criteria['location_name']) && !empty($criteria['location_name']) ){
			$locations_table = EM_LOCATIONS_TABLE; 
			$prepared_sql = $wpdb->prepare("SELECT * FROM $locations_table WHERE location_name = %s AND location_address = %s AND location_town = %s AND location_state = %s AND location_postcode = %s AND location_country = %s", stripcslashes($criteria['location_name']), stripcslashes($criteria['location_address']), stripcslashes($criteria['location_town']), stripcslashes($criteria['location_state']), stripcslashes($criteria['location_postcode']), stripcslashes($criteria['location_country']) );
			//$wpdb->show_errors(true);
			$location = $wpdb->get_row($prepared_sql, ARRAY_A);
			if( is_array($location) ){
				$this->to_object($location);
			}
			return apply_filters('em_location_load_similar', $location, $this);
		}
		return apply_filters('em_location_load_similar', false, $this);
	}
	
	function has_events(){
		global $wpdb;	
		$events_table = EM_EVENTS_TABLE;
		$sql = "SELECT count(event_id) as events_no FROM $events_table WHERE location_id = {$this->id}";   
	 	$affected_events = $wpdb->get_row($sql);
		return apply_filters('em_location_has_events', (count($affected_events) > 0), $this);
	}
	
	/**
	 * Can the user manage this location? 
	 */
	function can_manage( $owner_capability = false, $admin_capability = false ){
		if( $owner_capability == 'edit_locations' && $this->id == '' && !is_user_logged_in() && get_option('dbem_events_anonymous_submissions') ){
			return apply_filters('em_event_can_manage',true);
		}
		return apply_filters('em_location_can_manage', parent::can_manage($owner_capability, $admin_capability), $this);
	}
	
	function output_single($target = 'html'){
		$format = get_option ( 'dbem_single_location_format' );
		return apply_filters('em_location_output_single', $this->output($format, $target), $this, $target);			
	}
	
	function output($format, $target="html") { 
		preg_match_all('/\{([a-zA-Z0-9_]+)\}([^{]+)\{\/[a-zA-Z0-9_]+\}/', $format, $conditionals);
		if( count($conditionals[0]) > 0 ){
			//Check if the language we want exists, if not we take the first language there
			foreach($conditionals[1] as $key => $condition){
				$format = str_replace($conditionals[0][$key], apply_filters('em_location_output_condition', '', $conditionals[0][$key], $condition, $this), $format);
			}
		}
		$location_string = $format;
	 	preg_match_all("/(#@?_?[A-Za-z0-9]+)({([a-zA-Z0-9,]+)})?/", $format, $placeholders);
		foreach($placeholders[1] as $key => $result) {
			$match = true;
			$replace = '';
			$full_result = $placeholders[0][$key];
			switch( $result ){
				case '#_LOCATIONID':
					$replace = $this->id;
					break;
				case '#_NAME': //Depreciated
				case '#_LOCATIONNAME':
					$replace = $this->name;
					break;
				case '#_ADDRESS': //Depreciated
				case '#_LOCATIONADDRESS': 
					$replace = $this->address;
					break;
				case '#_TOWN': //Depreciated
				case '#_LOCATIONTOWN':
					$replace = $this->town;
					break;
				case '#_LOCATIONSTATE':
					$replace = $this->state;
					break;
				case '#_LOCATIONPOSTCODE':
					$replace = $this->postcode;
					break;
				case '#_LOCATIONREGION':
					$replace = $this->region;
					break;
				case '#_LOCATIONCOUNTRY':
					$replace = $this->get_country();
					break;
				case '#_LOCATIONFULLLINE':
					$replace = $this->address.', ';
					$replace = empty($this->town) ? '':', '.$this->town;
					$replace = empty($this->state) ? '':', '.$this->state;
					$replace = empty($this->postcode) ? '':', '.$this->postcode;
					$replace = empty($this->region) ? '':', '.$this->region;
					break;
				case '#_LOCATIONFULLBR':
					$replace = $this->address.'<br /> ';
					$replace = empty($this->town) ? '':'<br /> '.$this->town;
					$replace = empty($this->state) ? '':'<br /> '.$this->state;
					$replace = empty($this->postcode) ? '':'<br /> '.$this->postcode;
					$replace = empty($this->region) ? '':'<br /> '.$this->region;
					break;
				case '#_MAP': //Depreciated
				case '#_LOCATIONMAP':
					ob_start();
					$template = em_locate_template('placeholders/locationmap.php', true, array('EM_Location'=>$this));
					$replace = ob_get_clean();			
					break;
				case '#_DESCRIPTION':  //Depreciated
				case '#_EXCERPT': //Depreciated
				case '#_LOCATIONNOTES':
				case '#_LOCATIONEXCERPT':	
					$replace = $this->description;
					if($result == "#_EXCERPT" || $result == "#_LOCATIONEXCERPT"){
						$matches = explode('<!--more', $this->description);
						$replace = $matches[0];
					}
					break;
				case '#_LOCATIONIMAGEURL':
				case '#_LOCATIONIMAGE':
	        		if($this->image_url != ''){
	        			if($result == '#_LOCATIONIMAGEURL'){
		        			$replace =  $this->image_url;
						}else{
							if( empty($placeholders[3][$key]) ){
								$replace = "<img src='".esc_url($this->image_url)."' alt='".esc_attr($this->name)."'/>";
							}else{
								$image_size = explode(',', $placeholders[3][$key]);
								if( $this->array_is_numeric($image_size) && count($image_size) > 1 ){
									$replace = "<img src='".em_get_thumbnail_url($this->image_url, $image_size[0], $image_size[1])."' alt='".esc_attr($this->name)."'/>";
								}else{
									$replace = "<img src='".esc_url($this->image_url)."' alt='".esc_attr($this->name)."'/>";
								}
							}
						}
	        		}
					break;
				case '#_LOCATIONURL':
				case '#_LOCATIONLINK':
				case '#_LOCATIONPAGEURL': //Depreciated
					$joiner = (stristr(EM_URI, "?")) ? "&amp;" : "?";
					$link = esc_url(EM_URI.$joiner."location_id=".$this->id);
					$replace = ($result == '#_LOCATIONURL' || $result == '#_LOCATIONPAGEURL') ? $link : '<a href="'.$link.'" title="'.esc_attr($this->name).'">'.esc_html($this->name).'</a>';
					break;
				case '#_PASTEVENTS': //Depreciated
				case '#_LOCATIONPASTEVENTS':
				case '#_NEXTEVENTS': //Depreciated
				case '#_LOCATIONNEXTEVENTS':
				case '#_ALLEVENTS': //Depreciated
				case '#_LOCATIONALLEVENTS':
					//convert depreciated placeholders for compatability
					$result = ($result == '#_PASTEVENTS') ? '#_LOCATIONPASTEVENTS':$result; 
					$result = ($result == '#_NEXTEVENTS') ? '#_LOCATIONNEXTEVENTS':$result;
					$result = ($result == '#_ALLEVENTS') ? '#_LOCATIONALLEVENTS':$result;
					//forget it ever happened? :/
					if ( $result == '#_LOCATIONPASTEVENTS'){ $scope = 'past'; }
					elseif ( $result == '#_LOCATIONNEXTEVENTS' ){ $scope = 'future'; }
					else{ $scope = 'all'; }
					$events = EM_Events::get( array('location'=>$this->id, 'scope'=>$scope) );
					if ( count($events) > 0 ){
						foreach($events as $event){
							$replace .= $event->output(get_option('dbem_location_event_list_item_format'));
						}
					} else {
						$replace = get_option('dbem_location_no_events_message');
					}
					break;
				default:
					$match = false;
					break;
			}
			if($match){ //if true, we've got a placeholder that needs replacing
				$replace = apply_filters('em_location_output_placeholder', $replace, $this, $full_result, $target); //USE WITH CAUTION! THIS MIGHT GET RENAMED
				$location_string = str_replace($full_result, $replace , $location_string );
			}else{
				$custom_replace = apply_filters('em_location_output_placeholder', $replace, $this, $full_result, $target); //USE WITH CAUTION! THIS MIGHT GET RENAMED
				if($custom_replace != $replace){
					$location_string = str_replace($full_result, $custom_replace , $location_string );
				}
			}
		}
		$name_filter = ($target == "html") ? 'dbem_general':'dbem_general_rss'; //TODO remove dbem_ filters
		$location_string = str_replace('#_LOCATION', apply_filters($name_filter, $this->name) , $location_string ); //Depreciated
		return apply_filters('em_location_output', $location_string, $this, $format, $target);	
	}
	
	function get_country(){
		$countries = em_get_countries();
		if( !empty($countries[$this->country]) ){
			return apply_filters('em_location_get_country', $countries[$this->country], $this);
		}
		return apply_filters('em_location_get_country', false, $this);
			
	}
}