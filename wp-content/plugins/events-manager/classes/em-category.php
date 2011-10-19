<?php
//TODO expand em_category to be like other classes
class EM_Category extends EM_Object {	
	//DB Fields
	var $id = '';
	var $slug = '';
	var $owner = '';
	var $name = '';
	var $description = '';
	//Other Vars
	var $fields = array(
		'category_id' => array('name'=>'id','type'=>'%d'),
		'category_slug' => array('name'=>'slug','type'=>'%s'), 
		'category_owner' => array('name'=>'owner','type'=>'%d'),
		'category_name' => array('name'=>'name','type'=>'%s'),
		'category_description' => array('name'=>'description','type'=>'%s')
	);
	var $required_fields;
	var $feedback_message = "";
	var $errors = array();
	var $image_url = '';
	
	/**
	 * Gets data from POST (default), supplied array, or from the database if an ID is supplied
	 * @param $category_data
	 * @return null
	 */
	function EM_Category( $category_data = false ) {
		global $wpdb;
		//Initialize
		$this->required_fields = array("name" => __('The category name', 'dbem'));
		$category = array();
		if( !empty($category_data) ){
			//Load category data
			if( is_array($category_data) && isset($category_data['category_name']) ){
				$category = $category_data;
			}elseif( is_numeric($category_data) ){
				//Retreiving from the database		
				$sql = "SELECT * FROM ". EM_CATEGORIES_TABLE ." WHERE category_id ='{$category_data}'";   
			  	$category = $wpdb->get_row($sql, ARRAY_A);
			}else{
				$sql = "SELECT * FROM ". EM_CATEGORIES_TABLE ." WHERE category_slug ='{$category_data}'";   
			  	$category = $wpdb->get_row($sql, ARRAY_A);
			}
			//Save into the object
			$this->to_object($category);
		} 
		$this->get_image_url();
		add_action('em_category_save',array(&$this, 'image_upload'), 1, 2);
		do_action('em_category',$this, $category_data);
	}
	
	function get_post(){
		//We are getting the values via POST or GET
		do_action('em_category_get_post_pre', $this);
		$category = array();
		$category['category_id'] = ( !empty($_POST['category_id']) ) ? $_POST['category_id']:'';
		$category['category_name'] = ( !empty($_POST['category_name']) ) ? stripslashes($_POST['category_name']):'';
		$category['category_slug'] = ( !empty($_POST['category_slug']) ) ? sanitize_title($_POST['category_slug']) : '' ;
		$category['category_description'] = ( !empty($_POST['content']) ) ? stripslashes($_POST['content']) : ''; //WP TinyMCE field
		$category['category_owner'] = ( !empty($_POST['category_owner']) && is_numeric($_POST['category_owner']) ) ? $_POST['category_owner']:get_current_user_id();
		$this->to_object( apply_filters('em_category_get_post', $category, $this) );
		return apply_filters('em_category_get_post',$this->validate(), $this);
	}
	
	function validate(){
		//check required fields
		foreach ( $this->required_fields as $field => $description) {
			if ( $this->$field == "" ) {
				$this->add_error($description.__(" is required.", "dbem"));
			}
		}
		$this->image_validate();
		return apply_filters('em_location_validate', ( count($this->errors) == 0 ), $this);
	}
	
	function save(){
		global $wpdb;
		$result = false;
		if( $this->can_manage('edit_categories') ){
			do_action('em_category_save_pre', $this);
			$table = EM_CATEGORIES_TABLE;
			$this->slug = $this->sanitize_title();
			$data = $this->to_array();
			unset($data['category_id']);
			if($this->id != ''){
				$where = array( 'category_id' => $this->id );  
				$result = $wpdb->update($table, $data, $where, $this->get_types($data));
				if( $result !== false ){
					$this->feedback_message = sprintf(__('%s successfully updated.', 'dbem'), __('Category','dbem'));
				}
			}else{
				$wpdb->insert($table, $data, $this->get_types($data));
			    $result = $this->id = $wpdb->insert_id;   
				if( $result !== false ){
					$this->feedback_message = sprintf(__('%s successfully added.', 'dbem'), __('Category','dbem'));
				}
			}
		}else{
			$this->add_error( sprintf(__('You do not have permission to create/edit %s.','dbem'), __('categories','dbem')) );
		}
		return apply_filters('em_category_save', ($result !== false), $this);
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
		$slug_matches = $wpdb->get_results('SELECT category_id FROM '.EM_CATEGORIES_TABLE." WHERE category_slug='{$slug}'", ARRAY_A);
		if( count($slug_matches) > 0 ){ //we will check that the slug is unique
			if( $slug_matches[0]['category_id'] != $this->id || count($slug_matches) > 1 ){
				//we have a conflict, so try another alternative
				$this->slug = preg_replace('/\-[0-9]+$/', '', $slug).'-'.($iteration+1);
				$this->sanitize_title($iteration+1);
			}
		}
		return apply_filters('em_category_title', $this->slug, $this);
	}
	
	function delete(){
		global $wpdb;
		$result = false;
		if( $this->can_manage('edit_categories') ){
			do_action('em_category_delete_pre', $this);
			$table_name = EM_CATEGORIES_TABLE;
			$sql = "DELETE FROM $table_name WHERE category_id = '{$this->id}';";
			$result = $wpdb->query($sql);
		}
		return apply_filters('em_category_delete', $result, $this);
	}
	
	function has_events(){
		global $wpdb;	
		$events_table = EM_EVENTS_TABLE;
		$sql = "SELECT count(event_id) as events_no FROM $events_table WHERE category_id = {$this->id}";   
	 	$affected_events = $wpdb->get_row($sql);
		return apply_filters('em_category_has_events', (count($affected_events) > 0), $this);
	}
	
	function output_single($target = 'html'){
		$format = get_option ( 'dbem_category_page_format' );
		return apply_filters('em_category_output_single', $this->output($format, $target), $this, $target);	
	}
	
	function output($format, $target="html") {
		preg_match_all('/\{([a-zA-Z0-9_]+)\}([^{]+)\{\/[a-zA-Z0-9_]+\}/', $format, $conditionals);
		if( count($conditionals[0]) > 0 ){
			//Check if the language we want exists, if not we take the first language there
			foreach($conditionals[1] as $key => $condition){
				$format = str_replace($conditionals[0][$key], apply_filters('em_category_output_condition', '', $condition, $conditionals[0][$key], $this), $format);
			}
		}
		$category_string = $format;		 
	 	preg_match_all("/(#@?_?[A-Za-z0-9]+)({([a-zA-Z0-9,]+)})?/", $format, $placeholders);
		foreach($placeholders[1] as $key => $result) {
			$match = true;
			$replace = '';
			$full_result = $placeholders[0][$key];
			switch( $result ){
				case '#_CATEGORYNAME':
					$replace = $this->name;
					break;
				case '#_CATEGORYID':
					$replace = $this->id;
					break;
				case '#_CATEGORYNOTES':
				case '#_CATEGORYDESCRIPTION':
					$replace = $this->description;
					break;
				case '#_CATEGORYIMAGE':
				case '#_CATEGORYIMAGEURL':
					if( $this->image_url != ''){
						if($result == '#_CATEGORYIMAGEURL'){
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
				case '#_CATEGORYLINK':
				case '#_CATEGORYURL':
					$joiner = (stristr(EM_URI, "?")) ? "&amp;" : "?";
					$link = esc_url(EM_URI.$joiner."category_id=".$this->id);
					$replace = ($result == '#_CATEGORYURL') ? $link : '<a href="'.$link.'">'.esc_html($this->name).'</a>';
					break;
				case '#_CATEGORYEVENTSPAST': //depreciated, erroneous documentation, left for compatability
				case '#_CATEGORYEVENTSNEXT': //depreciated, erroneous documentation, left for compatability
				case '#_CATEGORYEVENTSALL': //depreciated, erroneous documentation, left for compatability
				case '#_CATEGORYPASTEVENTS':
				case '#_CATEGORYNEXTEVENTS':
				case '#_CATEGORYALLEVENTS':
					//convert depreciated placeholders for compatability
					$result = ($result == '#_CATEGORYEVENTSPAST') ? '#_CATEGORYPASTEVENTS':$result; 
					$result = ($result == '#_CATEGORYEVENTSNEXT') ? '#_CATEGORYNEXTEVENTS':$result;
					$result = ($result == '#_CATEGORYEVENTSALL') ? '#_CATEGORYALLEVENTS':$result;
					//forget it ever happened? :/
					if ($result == '#_CATEGORYPASTEVENTS'){ $scope = 'past'; }
					elseif ( $result == '#_CATEGORYNEXTEVENTS' ){ $scope = 'future'; }
					else{ $scope = 'all'; }
					$events = EM_Events::get( array('category'=>$this->id, 'scope'=>$scope) );
					if ( count($events) > 0 ){
						foreach($events as $EM_Event){
							$replace .= $EM_Event->output(get_option('dbem_category_event_list_item_format'));
						}
					} else {
						$replace = get_option('dbem_category_no_events_message');
					}
					break;
				default:
					$replace = $full_result;
					break;
			}
			$replace = apply_filters('em_category_output_placeholder', $replace, $this, $full_result, $target); //USE WITH CAUTION! THIS MIGHT GET RENAMED
			$category_string = str_replace($full_result, $replace , $category_string );
		}
		$name_filter = ($target == "html") ? 'dbem_general':'dbem_general_rss'; //TODO remove dbem_ filters
		$category_string = str_replace('#_CATEGORY', apply_filters($name_filter, $this->name) , $category_string ); //Depreciated
		return apply_filters('em_category_output', $category_string, $this, $format, $target);	
	}
	
	function can_manage( $capability_owner = 'edit_categories', $capability_admin = false ){
		global $em_capabilities_array;
		//Figure out if this is multisite and require an extra bit of validation
		$multisite_check = true;
		$can_manage = current_user_can($capability_owner);
		//if multisite and supoer admin, just return true
		if( is_multisite() && is_super_admin() ){ return true; }
		if( is_multisite() && get_site_option('dbem_ms_global_table') && !is_main_site() ){
			//User can't admin this bit, as they're on a sub-blog
			$can_manage = false;
			if(array_key_exists($capability_owner, $em_capabilities_array) ){
				$this->add_error( $em_capabilities_array[$capability_owner]);
			}
		}
		return $can_manage;
	}
}
?>