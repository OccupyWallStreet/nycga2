<?php
/**
 * gets a location
 * @param mixed $id
 * @param mixed $search_by
 * @return EM_Location
 */
function em_get_location($id = false, $search_by = 'location_id') {
	if( is_object($id) && get_class($id) == 'EM_Location' ){
		return $id;
	}else{
		return new EM_Location($id,$search_by);
	}
}
/**
 * Object that holds location info and related functions
 * @author marcus
 */
class EM_Location extends EM_Object {
	//DB Fields
	var $location_id = '';
	var $post_id = '';
	var $blog_id = '';
	var $location_slug = '';
	var $location_name = '';
	var $location_address = '';
	var $location_town = '';
	var $location_state = '';
	var $location_postcode = '';
	var $location_region = '';
	var $location_country = '';
	var $location_latitude = 0;
	var $location_longitude = 0;
	var $post_content = '';
	var $location_owner = '';
	var $location_status = 0;
	//Other Vars
	var $fields = array( 
		'location_id' => array('name'=>'id','type'=>'%d'),
		'post_id' => array('name'=>'post_id','type'=>'%d'),
		'blog_id' => array('name'=>'blog_id','type'=>'%d'),
		'location_slug' => array('name'=>'slug','type'=>'%s', 'null'=>true), 
		'location_name' => array('name'=>'name','type'=>'%s', 'null'=>true), 
		'location_address' => array('name'=>'address','type'=>'%s','null'=>true),
		'location_town' => array('name'=>'town','type'=>'%s','null'=>true),
		'location_state' => array('name'=>'state','type'=>'%s','null'=>true),
		'location_postcode' => array('name'=>'postcode','type'=>'%s','null'=>true),
		'location_region' => array('name'=>'region','type'=>'%s','null'=>true),
		'location_country' => array('name'=>'country','type'=>'%s','null'=>true),
		'location_latitude' =>  array('name'=>'latitude','type'=>'%f','null'=>true),
		'location_longitude' => array('name'=>'longitude','type'=>'%f','null'=>true),
		'post_content' => array('name'=>'description','type'=>'%s', 'null'=>true),
		'location_owner' => array('name'=>'owner','type'=>'%d', 'null'=>true),
		'location_status' => array('name'=>'status','type'=>'%d', 'null'=>true)
	);
	var $post_fields = array('post_id','location_slug','location_name','post_content','location_owner');
	var $location_attributes = array();
	var $image_url = '';
	var $required_fields = array();
	var $feedback_message = "";
	var $mime_types = array(1 => 'gif', 2 => 'jpg', 3 => 'png'); 
	var $errors = array();
	/**
	 * previous status of location
	 * @access protected
	 * @var mixed
	 */
	var $previous_status = 0;
	
	/* Post Variables - copied out of post object for easy IDE reference */
	var $ID;
	var $post_author;
	var $post_date;
	var $post_date_gmt;
	var $post_title;
	var $post_excerpt;
	var $post_status;
	var $comment_status;
	var $ping_status;
	var $post_password;
	var $post_name;
	var $to_ping;
	var $pinged;
	var $post_modified;
	var $post_modified_gmt;
	var $post_content_filtered;
	var $post_parent;
	var $guid;
	var $menu_order;
	var $post_type;
	var $post_mime_type;
	var $comment_count;
	var $ancestors;
	var $filter;
	
	
	/**
	 * Gets data from POST (default), supplied array, or from the database if an ID is supplied
	 * @param $location_data
	 * @param $search_by can be set to post_id or a number for a blog id if in ms mode with global tables, default is location_id
	 * @return null
	 */
	function __construct($id = false,  $search_by = 'location_id' ) {
		global $wpdb;
		//Initialize
		$this->required_fields = array("location_address" => __('The location address', 'dbem'), "location_town" => __('The location town', 'dbem'), "location_country" => __('The country', 'dbem'));
		//Get the post_id/location_id
		$is_post = !empty($id->ID) && $id->post_type == EM_POST_TYPE_LOCATION;
		if( $is_post || absint($id) > 0 ){ //only load info if $id is a number
			$location_post = false;
			if($search_by == 'location_id' && !$is_post){
				//search by location_id, get post_id and blog_id (if in ms mode) and load the post
				$results = $wpdb->get_row($wpdb->prepare("SELECT post_id, blog_id FROM ".EM_LOCATIONS_TABLE." WHERE location_id=%d",$id), ARRAY_A);
				if( !empty($results['post_id']) ){
					if( is_multisite() && is_numeric($results['blog_id']) ){
						$location_post = get_blog_post($results['blog_id'], $results['post_id']);
						$search_by = $results['blog_id'];
					}else{
						$location_post = get_post($results['post_id']);
					}
				}
			}else{
				if(!$is_post){
					if( is_numeric($search_by) && is_multisite() ){
						//we've been given a blog_id, so we're searching for a post id
						$location_post = get_blog_post($search_by, $id);
					}else{
						//search for the post id only
						$location_post = get_post($id);	
					}
				}else{
					$location_post = $id;
				}
			}
			$this->load_postdata($location_post, $search_by);
		}
		$this->compat_keys();
		do_action('em_location', $this, $id, $search_by);
	}
	
	function load_postdata($location_post, $search_by = false){
		if( is_object($location_post) ){
			if( $location_post->post_status != 'auto-draft' ){
				if( is_numeric($search_by) && is_multisite() ){
					// if in multisite mode, switch blogs quickly to get the right post meta.
					switch_to_blog($search_by);
					$location_meta = get_post_custom($location_post->ID);
					restore_current_blog();
					$this->blog_id = $search_by;
				}else{
					$location_meta = get_post_custom($location_post->ID);
				}	
				//Get custom fields
				foreach($location_meta as $location_meta_key => $location_meta_val){
					$found = false;
					foreach($this->fields as $field_name => $field_info){
						if( $location_meta_key == '_'.$field_name){
							$this->$field_name = $location_meta_val[0];
							$found = true;
						}
					}
					if(!$found && $location_meta_key[0] != '_'){
						$this->location_attributes[$location_meta_key] = ( count($location_meta_val) > 1 ) ? $location_meta_val:$location_meta_val[0];					
					}
				}	
			}
			//load post data - regardless
			$this->post_id = $location_post->ID;
			$this->location_name = $location_post->post_title;
			$this->location_slug = $location_post->post_name;
			$this->location_owner = $location_post->post_author;
			$this->post_content = $location_post->post_content;
			foreach( $location_post as $key => $value ){ //merge the post data into location object
				$this->$key = $value;
			}
			$this->previous_status = $this->location_status; //so we know about updates
			$this->get_status();
		}
	}
	
	/**
	 * Retrieve event information via POST (used in situations where posts aren't submitted via WP)
	 * @param boolean $validate whether or not to run validation, default is true
	 * @return boolean
	 */
	function get_post($validate = true){
	    global $allowedtags;
		do_action('em_location_get_post_pre', $this);
		$this->location_name = ( !empty($_POST['location_name']) ) ? wp_kses_data( stripslashes($_POST['location_name'])):'';
		$this->post_content = ( !empty($_POST['content']) ) ? wp_kses( stripslashes($_POST['content']), $allowedtags):'';
		$this->get_post_meta(false);
		$result = $validate ? $this->validate():true; //validate both post and meta, otherwise return true
		$this->compat_keys();
		return apply_filters('em_location_get_post', $result, $this);		
	}
	/**
	 * Retrieve event post meta information via POST, which should be always be called when saving the event custom post via WP.
	 * @param boolean $validate whether or not to run validation, default is true
	 * @return mixed
	 */
	function get_post_meta($validate = true){
		//We are getting the values via POST or GET
		do_action('em_location_get_post_meta_pre', $this);
		$this->location_address = ( !empty($_POST['location_address']) ) ? wp_kses(stripslashes($_POST['location_address']), array()):'';
		$this->location_town = ( !empty($_POST['location_town']) ) ? wp_kses(stripslashes($_POST['location_town']), array()):'';
		$this->location_state = ( !empty($_POST['location_state']) ) ? wp_kses(stripslashes($_POST['location_state']), array()):'';
		$this->location_postcode = ( !empty($_POST['location_postcode']) ) ? wp_kses(stripslashes($_POST['location_postcode']), array()):'';
		$this->location_region = ( !empty($_POST['location_region']) ) ? wp_kses(stripslashes($_POST['location_region']), array()):'';
		$this->location_country = ( !empty($_POST['location_country']) ) ? wp_kses(stripslashes($_POST['location_country']), array()):'';
		$this->location_latitude = ( !empty($_POST['location_latitude']) && is_numeric($_POST['location_latitude']) ) ? $_POST['location_latitude']:'';
		$this->location_longitude = ( !empty($_POST['location_longitude']) && is_numeric($_POST['location_longitude']) ) ? $_POST['location_longitude']:'';
		//Set Blog ID
		if( is_multisite() && empty($this->blog_id) ){
			$this->blog_id = get_current_blog_id();
		}
		//Sort out event attributes - note that custom post meta now also gets inserted here automatically (and is overwritten by these attributes)
		if(get_option('dbem_location_attributes_enabled')){
			global $allowedtags;
			if( !is_array($this->location_attributes) ){ $this->location_attributes = array(); }
			$location_available_attributes = em_get_attributes(true); //lattributes only
			if( !empty($_POST['em_attributes']) && is_array($_POST['em_attributes']) ){
				foreach($_POST['em_attributes'] as $att_key => $att_value ){
					if( (in_array($att_key, $location_available_attributes['names']) || array_key_exists($att_key, $this->location_attributes) ) ){
						$att_vals = count($location_available_attributes['values'][$att_key]);
						if( $att_vals == 0 || ($att_vals > 0 && in_array($att_value, $location_available_attributes['values'][$att_key])) ){
							$this->location_attributes[$att_key] = stripslashes($att_value);
						}elseif($att_vals > 0){
							$this->location_attributes[$att_key] = stripslashes(wp_kses($location_available_attributes['values'][$att_key][0], $allowedtags));
						}
					}
				}
			}
		}
		$result = $validate ? $this->validate_meta():true; //post returns null
		$this->compat_keys();
		return apply_filters('em_location_get_post_meta',$result,$this);
	}
	
	function validate(){
		$validate_post = true;
		if( empty($this->location_name) ){
			$validate_post = false;
			$this->add_error( __('Location name').__(" is required.", "dbem") );
		}
		$validate_image = $this->image_validate();
		$validate_meta = $this->validate_meta();
		return apply_filters('em_location_validate', $validate_post && $validate_image && $validate_meta, $this );		
	}
	
	/**
	 * Validates the location. Should be run during any form submission or saving operation.
	 * @return boolean
	 */
	function validate_meta(){
		//check required fields
		foreach ( $this->required_fields as $field => $description) {
			if( $field == 'location_country' && !array_key_exists($this->location_country, em_get_countries()) ){ 
				//country specific checking
				$this->add_error( $this->required_fields['location_country'].__(" is required.", "dbem") );				
			}elseif ( $this->$field == "" ) {
				$this->add_error( $description.__(" is required.", "dbem") );
			}
		}
		return apply_filters('em_location_validate_meta', ( count($this->errors) == 0 ), $this);
	}
	
	function save(){
		global $wpdb, $current_user, $blog_id;
		//TODO shuffle filters into right place
		if( !$this->can_manage('edit_locations', 'edit_others_locations') && !( get_option('dbem_events_anonymous_submissions') && empty($this->location_id)) ){
			return apply_filters('em_location_save', false, $this);
		}
		remove_action('save_post',array('EM_Location_Post_Admin','save_post'),10,1); //disable the default save post action, we'll do it manually this way
		do_action('em_location_save_pre', $this);
		$post_array = array();
		//Deal with updates to a location
		if( !empty($this->post_id) ){
			//get the full array of post data so we don't overwrite anything.
			if( !empty($this->blog_id) && is_multisite() ){
				$post_array = (array) get_blog_post($this->blog_id, $this->post_id);
			}else{
				$post_array = (array) get_post($this->post_id);
			}
		}
		//Overwrite new post info
		$post_array['post_type'] = EM_POST_TYPE_LOCATION;
		$post_array['post_title'] = $this->location_name;
		$post_array['post_content'] = $this->post_content;
		//decide on post status
		if( count($this->errors) == 0 ){
			if( EM_MS_GLOBAL && !is_main_site() && get_site_option('dbem_ms_mainblog_locations') ){
				//if in global ms mode and user is a valid role to publish on their blog, then we will publish the location on the main blog
				restore_current_blog();
				$switch_back = true;
			}
			$post_array['post_status'] = ( current_user_can('publish_locations') ) ? 'publish':'pending';
			if(!empty($switch_back) && get_site_option('dbem_ms_mainblog_locations') ) EM_Object::ms_global_switch(); //switch 'back' to main blog
		}else{
			$post_array['post_status'] = 'draft';
		}
		//Anonymous submission
		if( !is_user_logged_in() && get_option('dbem_events_anonymous_submissions') && empty($this->location_id) ){
			$post_array['post_author'] = get_option('dbem_events_anonymous_user');
			if( !is_numeric($post_array['post_author']) ) $post_array['post_author'] = 0;
		}
		//Save post and continue with meta
		$post_id = wp_insert_post($post_array);
		$post_save = false;
		$meta_save = false;
		if( !is_wp_error($post_id) && !empty($post_id) ){
			$post_save = true;			
			//refresh this event with wp post
			$post_data = get_post($post_id);
			$this->post_id = $post_id;
			$this->location_slug = $post_data->post_name;
			$this->location_owner = $post_data->post_author;
			$this->post_status = $post_data->post_status;
			$this->get_status();
			//now save the meta
			$meta_save = $this->save_meta();
			//save the image
			$this->image_upload();
			$image_save = (count($this->errors) == 0);
		}elseif(is_wp_error($post_id)){
			//location not saved, add an error
			$this->add_error($post_id->get_error_message());
		}
		return apply_filters('em_location_save', $post_save && $meta_save && $image_save, $this);
	}
	
	function save_meta(){
		//echo "<pre>"; print_r($this); echo "</pre>"; die();
		global $wpdb, $current_user;
		if( $this->can_manage('edit_locations','edit_others_locations') || ( get_option('dbem_events_anonymous_submissions') && empty($this->location_id)) ){
			do_action('em_location_save_meta_pre', $this);
			$data = $this->to_array();
			//Update Post Meta
			foreach( array_keys($this->fields) as $key ){
				if( !in_array($key, $this->post_fields) ){
					update_post_meta($this->post_id, '_'.$key, $this->$key);
				}
			}
			//Update Post Attributes
			foreach($this->location_attributes as $location_attribute_key => $location_attribute){
				update_post_meta($this->post_id, $location_attribute_key, $location_attribute);
			}
			$this->get_status();
			$this->location_status = (count($this->errors) == 0) ? $this->location_status:null; //set status at this point, it's either the current status, or if validation fails, null
			//Save to em_locations table
			$location_array = $this->to_array(true);
			if( $this->post_status == 'private' ) $location_array['location_private'] = 1;
			unset($location_array['location_id']);
			if( !empty($this->location_id) ){
				$loc_truly_exists = $wpdb->get_var('SELECT post_id FROM '.EM_LOCATIONS_TABLE." WHERE location_id={$this->location_id}") == $this->post_id;
			}else{
				$loc_truly_exists = false;
			}
			if( empty($this->location_id) || !$loc_truly_exists ){
				$this->previous_status = 0; //for sure this was previously status 0
				if ( !$wpdb->insert(EM_LOCATIONS_TABLE, $location_array) ){
					$this->add_error( sprintf(__('Something went wrong saving your %s to the index table. Please inform a site administrator about this.','dbem'),__('location','dbem')));
				}else{
					//success, so link the event with the post via an event id meta value for easy retrieval
					$this->location_id = $wpdb->insert_id;
					update_post_meta($this->post_id, '_location_id', $this->location_id);
					$this->feedback_message = sprintf(__('Successfully saved %s','dbem'),__('Location','dbem'));
				}	
			}else{
				$this->previous_status = $wpdb->get_var('SELECT location_status FROM '.EM_LOCATIONS_TABLE.' WHERE location_id='.$this->location_id); //get status from db, not post_status
				if ( $wpdb->update(EM_LOCATIONS_TABLE, $location_array, array('location_id'=>$this->location_id)) === false ){
					$this->add_error( sprintf(__('Something went wrong updating your %s to the index table. Please inform a site administrator about this.','dbem'),__('location','dbem')));			
				}else{
					$this->feedback_message = sprintf(__('Successfully saved %s','dbem'),__('Location','dbem'));
				}
			}
		}else{
			$this->add_error( sprintf(__('You do not have permission to create/edit %s.','dbem'), __('locations','dbem')) );
		}
		$this->compat_keys();
		return apply_filters('em_location_save_meta', count($this->errors) == 0, $this);
	}
	
	function delete($force_delete = true){ //atm wp seems to force cp deletions anyway
		global $wpdb;
		$result = false;
		if( $this->can_manage('delete_locations','delete_others_locations') ){
			do_action('em_location_delete_pre', $this);
			$result = wp_delete_post($this->post_id,$force_delete); //the post class will take care of the meta
			if( $force_delete ){
				$result_meta = $this->delete_meta();
			}
		}
		return apply_filters('em_location_delete', $result !== false && $result_meta, $this);
	}
	
	function delete_meta(){
		global $wpdb;
		$result = false;
		if( $this->can_manage('delete_locations','delete_others_locations') ){
			do_action('em_location_delete_meta_pre', $this);
			$result = $wpdb->query ( $wpdb->prepare("DELETE FROM ". EM_LOCATIONS_TABLE ." WHERE location_id=%d", $this->location_id) );
		}
		return apply_filters('em_location_delete_meta', $result !== false, $this);
	}
	
	function is_published(){
		return apply_filters('em_location_is_published', ($this->post_status == 'publish' || $this->post_status == 'private'), $this);
	}
	
	/**
	 * Change the status of the location. This will save to the Database too. 
	 * @param int $status
	 * @param boolean $set_post_status
	 * @return string
	 */
	function set_status($status, $set_post_status = false){
		global $wpdb;
		if($status === null){ 
			$set_status='NULL'; 
			if($set_post_status){
				//if the post is trash, don't untrash it!
				$wpdb->update( $wpdb->posts, array( 'post_status' => 'draft' ), array( 'ID' => $this->post_id ) );
				$this->post_status = 'draft';
			} 
		}else{
			$set_status = $status ? 1:0;
			if($set_post_status){
				if($this->post_status == 'pending'){
					$this->post_name = sanitize_title($this->post_title);
				}
				$this->post_status = $set_status ? 'publish':'pending';
				$wpdb->update( $wpdb->posts, array( 'post_status' => $this->post_status, 'post_name' => $this->post_name ), array( 'ID' => $this->post_id ) );
			}
		}
		$this->previous_status = $wpdb->get_var('SELECT location_status FROM '.EM_LOCATIONS_TABLE.' WHERE location_id='.$this->location_id); //get status from db, not post_status, as posts get saved quickly
		$result = $wpdb->query("UPDATE ".EM_LOCATIONS_TABLE." SET location_status=$set_status, location_slug='{$this->post_name}' WHERE location_id={$this->location_id}");
		$this->get_status();
		return apply_filters('em_location_set_status', $result !== false, $status, $this);
	}	
	
	function get_status($db = false){
		switch( $this->post_status ){
			case 'private':
				$this->location_private = 1;
				$this->location_status = $status = 1;
				break;
			case 'publish':
				$this->location_private = 0;
				$this->location_status = $status = 1;
				break;
			case 'pending':
				$this->location_private = 0;
				$this->location_status = $status = 0;
				break;
			default: //draft or unknown
				$this->location_private = 0;
				$status = $db ? 'NULL':null;
				$this->location_status = null;
				break;
		}
		return $status;
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
		$sql = "SELECT count(event_id) as events_no FROM $events_table WHERE location_id = {$this->location_id}";   
	 	$affected_events = $wpdb->get_row($sql);
		return apply_filters('em_location_has_events', (count($affected_events) > 0), $this);
	}
	
	/**
	 * Can the user manage this location? 
	 */
	function can_manage( $owner_capability = false, $admin_capability = false, $user_to_check = false ){
		if( $this->location_id == '' && !is_user_logged_in() && get_option('dbem_events_anonymous_submissions') ){
			$user_to_check = get_option('dbem_events_anonymous_user');
		}
		return apply_filters('em_location_can_manage', parent::can_manage($owner_capability, $admin_capability, $user_to_check), $this, $owner_capability, $admin_capability, $user_to_check);
	}
	
	function get_permalink(){	
		if( EM_MS_GLOBAL ){
			if( get_site_option('dbem_ms_global_locations_links') && !empty($this->blog_id) && is_main_site() && $this->blog_id != get_current_blog_id() ){
				//linking directly to the blog, we should be on the main blog here
				$link = get_blog_permalink( $this->blog_id, $this->post_id);
			}elseif( !empty($this->blog_id) && is_main_site() && $this->blog_id != get_current_blog_id() ){
				if( get_option('dbem_locations_page') ){
					$link = trailingslashit(get_permalink(get_option('dbem_locations_page')).get_site_option('dbem_ms_locations_slug',EM_LOCATION_SLUG).'/'.$this->location_slug.'-'.$this->location_id);
				}else{
					$link = trailingslashit(home_url()).EM_POST_TYPE_LOCATION_SLUG.'/'.get_site_option('dbem_ms_events_slug',EM_LOCATION_SLUG).'/'.$this->location_slug.'-'.$this->location_id;
				}
			}
		}
		if( empty($link) ){
			$link = get_post_permalink($this->post_id);
		}
		return apply_filters('em_location_get_permalink', $link, $this);	;
	}
	
	function get_edit_url(){
		if( $this->can_manage('edit_locations','edit_others_locations') ){
			if( EM_MS_GLOBAL && get_site_option('dbem_ms_global_locations_links') && !empty($this->blog_id) && is_main_site() && $this->blog_id != get_current_blog_id() ){
				if( get_blog_option($this->blog_id, 'dbem_edit_locations_page') ){
					$link = em_add_get_params(get_permalink(get_blog_option($this->blog_id, 'dbem_edit_locations_page')), array('action'=>'edit','location_id'=>$this->location_id), false);
				}
				if( empty($link))
					$link = get_admin_url($this->blog_id, "post.php?post={$this->post_id}&action=edit");
			}else{
				if( get_option('dbem_edit_locations_page') ){
					$link = em_add_get_params(get_permalink(get_option('dbem_edit_locations_page')), array('action'=>'edit','location_id'=>$this->location_id), false);
				}
				if( empty($link))
					$link = admin_url()."post.php?post={$this->post_id}&action=edit";
			}
			return apply_filters('em_location_get_edit_url', $link, $this);
		}
	}
	
	function output_single($target = 'html'){
		$format = get_option ( 'dbem_single_location_format' );
		return apply_filters('em_location_output_single', $this->output($format, $target), $this, $target);			
	}
	
	function output($format, $target="html") {
		$location_string = $format;
		preg_match_all('/\{([a-zA-Z0-9_]+)\}([^{]+)\{\/[a-zA-Z0-9_]+\}/', $format, $conditionals);
		if( count($conditionals[0]) > 0 ){
			//Check if the language we want exists, if not we take the first language there
			foreach($conditionals[1] as $key => $condition){
				$format = str_replace($conditionals[0][$key], apply_filters('em_location_output_condition', '', $conditionals[0][$key], $condition, $this), $format);
			}
		}
		//This is for the custom attributes
		preg_match_all('/#_LATT\{([^}]+)\}(\{([^}]+)\})?/', $format, $results);
		foreach($results[0] as $resultKey => $result) {
			//Strip string of placeholder and just leave the reference
			$attRef = substr( substr($result, 0, strpos($result, '}')), 7 );
			$attString = '';
			if( is_array($this->location_attributes) && array_key_exists($attRef, $this->location_attributes) && !empty($this->location_attributes[$attRef]) ){
				$attString = $this->location_attributes[$attRef];
			}elseif( !empty($results[3][$resultKey]) ){
				//Check to see if we have a second set of braces;
				$attString = $results[3][$resultKey];
			}
			$attString = apply_filters('em_location_output_placeholder', $attString, $this, $result, $target);
			$location_string = str_replace($result, $attString ,$location_string );
		}
	 	preg_match_all("/(#@?_?[A-Za-z0-9]+)({([a-zA-Z0-9,]+)})?/", $format, $placeholders);
	 	$replaces = array();
		foreach($placeholders[1] as $key => $result) {
			$replace = '';
			$full_result = $placeholders[0][$key];
			switch( $result ){
				case '#_LOCATIONID':
					$replace = $this->location_id;
					break;
				case '#_LOCATIONPOSTID':
					$replace = $this->location_id;
					break;
				case '#_NAME': //Depreciated
				case '#_LOCATION': //Depreciated
				case '#_LOCATIONNAME':
					$replace = $this->location_name;
					break;
				case '#_ADDRESS': //Depreciated
				case '#_LOCATIONADDRESS': 
					$replace = $this->location_address;
					break;
				case '#_TOWN': //Depreciated
				case '#_LOCATIONTOWN':
					$replace = $this->location_town;
					break;
				case '#_LOCATIONSTATE':
					$replace = $this->location_state;
					break;
				case '#_LOCATIONPOSTCODE':
					$replace = $this->location_postcode;
					break;
				case '#_LOCATIONREGION':
					$replace = $this->location_region;
					break;
				case '#_LOCATIONCOUNTRY':
					$replace = $this->get_country();
					break;
				case '#_LOCATIONFULLLINE':
					$replace = $this->location_address;
					$replace .= empty($this->location_town) ? '':', '.$this->location_town;
					$replace .= empty($this->location_state) ? '':', '.$this->location_state;
					$replace .= empty($this->location_postcode) ? '':', '.$this->location_postcode;
					$replace .= empty($this->location_region) ? '':', '.$this->location_region;
					break;
				case '#_LOCATIONFULLBR':
					$replace = $this->location_address;
					$replace .= empty($this->location_town) ? '':'<br />'.$this->location_town;
					$replace .= empty($this->location_state) ? '':'<br />'.$this->location_state;
					$replace .= empty($this->location_postcode) ? '':'<br />'.$this->location_postcode;
					$replace .= empty($this->location_region) ? '':'<br />'.$this->location_region;
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
					$replace = $this->post_content;
					if($result == "#_EXCERPT" || $result == "#_LOCATIONEXCERPT"){
						if( !empty($this->post_excerpt) ){
							$replace = $this->post_excerpt;
						}else{
							$matches = explode('<!--more', $this->post_content);
							$replace = $matches[0];
						}
					}
					break;
				case '#_LOCATIONIMAGEURL':
				case '#_LOCATIONIMAGE':
	        		if($this->get_image_url() != ''){
	        			$image_url = esc_url($this->get_image_url());
	        			if($result == '#_LOCATIONIMAGEURL'){
		        			$replace =  $this->get_image_url();
						}else{
							if( empty($placeholders[3][$key]) ){
								$replace = "<img src='".$image_url."' alt='".esc_attr($this->location_name)."'/>";
							}else{
								$image_size = explode(',', $placeholders[3][$key]);
								if( $this->array_is_numeric($image_size) && count($image_size) > 1 ){
									global $blog_id;
									if ( is_multisite() && $blog_id > 0) {
										$imageParts = explode('/blogs.dir/', $image_url);
										if (isset($imageParts[1])) {
											$image_url = network_site_url('/wp-content/blogs.dir/'. $blog_id. '/' . $imageParts[1]);
										}
									}
									$replace = "<img src='".esc_url(em_get_thumbnail_url($image_url, $image_size[0], $image_size[1]))."' alt='".esc_attr($this->location_name)."'/>";
								}else{
									$replace = "<img src='".$image_url."' alt='".esc_attr($this->location_name)."'/>";
								}
							}
						}
	        		}
					break;
				case '#_LOCATIONURL':
				case '#_LOCATIONLINK':
				case '#_LOCATIONPAGEURL': //Depreciated
					$link = esc_url($this->get_permalink());
					$replace = ($result == '#_LOCATIONURL' || $result == '#_LOCATIONPAGEURL') ? $link : '<a href="'.$link.'" title="'.esc_attr($this->location_name).'">'.esc_html($this->location_name).'</a>';
					break;
				case '#_LOCATIONEDITURL':
				case '#_LOCATIONEDITLINK':
					$link = esc_url($this->get_edit_url());
					$replace = ($result == '#_LOCATIONEDITURL') ? $link : '<a href="'.$link.'" title="'.esc_attr($this->location_name).'">'.esc_html(sprintf(__('Edit %s','dbem'),__('Location','dbem'))).'</a>';
					break;
				case '#_PASTEVENTS': //Depreciated
				case '#_LOCATIONPASTEVENTS':
				case '#_NEXTEVENTS': //Depreciated
				case '#_LOCATIONNEXTEVENTS':
				case '#_ALLEVENTS': //Depreciated
				case '#_LOCATIONALLEVENTS':
					//TODO: add limit to lists of events
					//convert depreciated placeholders for compatability
					$result = ($result == '#_PASTEVENTS') ? '#_LOCATIONPASTEVENTS':$result; 
					$result = ($result == '#_NEXTEVENTS') ? '#_LOCATIONNEXTEVENTS':$result;
					$result = ($result == '#_ALLEVENTS') ? '#_LOCATIONALLEVENTS':$result;
					//forget it ever happened? :/
					if ( $result == '#_LOCATIONPASTEVENTS'){ $scope = 'past'; }
					elseif ( $result == '#_LOCATIONNEXTEVENTS' ){ $scope = 'future'; }
					else{ $scope = 'all'; }
					$events = EM_Events::get( array('location'=>$this->location_id, 'scope'=>$scope) );
					if ( count($events) > 0 ){
						$replace .= get_option('dbem_location_event_list_item_header_format');
						foreach($events as $event){
							$replace .= $event->output(get_option('dbem_location_event_list_item_format'));
						}
						$replace .= get_option('dbem_location_event_list_item_footer_format');
					} else {
						$replace = get_option('dbem_location_no_events_message');
					}
					break;
				case '#_LOCATIONNEXTEVENT':
					$events = EM_Events::get( array('location'=>$this->location_id, 'scope'=>'future', 'limit'=>1, 'orderby'=>'event_start_date,event_start_time') );
					$replace = get_option('dbem_location_no_events_message');
					foreach($events as $EM_Event){
						$replace = $EM_Event->output('#_EVENTLINK');
					}
					break;
				default:
					$replace = $full_result;
					break;
			}
			$replaces[$full_result] = apply_filters('em_location_output_placeholder', $replace, $this, $full_result, $target);
		}
		//sort out replacements so that during replacements shorter placeholders don't overwrite longer varieties.
		krsort($replaces);
		foreach($replaces as $full_result => $replacement){
			$location_string = str_replace($full_result, $replacement , $location_string );
		}
		return apply_filters('em_location_output', $location_string, $this, $format, $target);	
	}
	
	function get_country(){
		$countries = em_get_countries();
		if( !empty($countries[$this->location_country]) ){
			return apply_filters('em_location_get_country', $countries[$this->location_country], $this);
		}
		return apply_filters('em_location_get_country', false, $this);
			
	}
}