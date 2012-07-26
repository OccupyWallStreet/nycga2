<?php
class EM_Categories extends EM_Object implements Iterator{
	
	/**
	 * Array of EM_Category objects for a specific event
	 * @var array
	 */
	var $categories = array();
	/**
	 * @var EM_Event
	 */
	var $event;	
	
	/**
	 * Creates an EM_Categories instance, currently accepts an EM_Event object (gets all Categories for that event) or array of any EM_Category objects, which can be manipulated in bulk with helper functions.
	 * @param EM_Event $event
	 * @return null
	 */
	function EM_Categories( $data = false ){
		if( is_object($data) && get_class($data) == "EM_Event" ){ //Creates a blank categories object if needed
			global $wpdb;
			$this->event = $data;
			$sql = "SELECT meta_value FROM ". EM_META_TABLE ." WHERE meta_key='event-category' AND object_id ='{$this->event->id}'";
			$categories = $wpdb->get_results($sql, ARRAY_A);
			foreach ($categories as $category_data){
				$this->categories[] = new EM_Category($category_data['meta_value']);
			}
		}elseif( is_array($data) && $this->array_is_numeric($data) ){
			foreach($data as $category_id){
				$this->categories[] =  new EM_Category($category_id);
			}
		}elseif( is_array($data) ){
			foreach( $data as $EM_Category ){
				if( get_class($EM_Category) == 'EM_Category'){
					$this->categories[] = $EM_Category;
				}
			}
			$this->get_event();
		}
	}
		
	function get( $args = array() ) {
		global $wpdb;
		$categories_table = EM_CATEGORIES_TABLE;
		$events_table = EM_EVENTS_TABLE;
		
		//Quick version, we can accept an array of IDs, which is easy to retrieve
		if( self::array_is_numeric($args) ){ //Array of numbers, assume they are event IDs to retreive
			//We can just get all the events here and return them
			$sql = "SELECT * FROM $categories_table WHERE category_id=".implode(" OR category_id=", $args);
			$results = $wpdb->get_results(apply_filters('em_categories_get_sql',$sql),ARRAY_A);
			$categories = array();
			foreach($results as $result){
				$categories[$result['category_id']] = new EM_Category($result);
			}
			return apply_filters('em_categories_get', $categories, $args); //We return all the categories matched as an EM_Event array. 
		}
		
		//We assume it's either an empty array or array of search arguments to merge with defaults			
		$args = self::get_default_search($args);
		$limit = ( $args['limit'] && is_numeric($args['limit'])) ? "LIMIT {$args['limit']}" : '';
		$offset = ( $limit != "" && is_numeric($args['offset']) ) ? "OFFSET {$args['offset']}" : '';
		
		//Get the default conditions
		$conditions = self::build_sql_conditions($args);
		//Put it all together
		$where = ( count($conditions) > 0 ) ? " WHERE " . implode ( " AND ", $conditions ):'';
		
		//Get ordering instructions
		$EM_Category = new EM_Category();
		$accepted_fields = $EM_Category->get_fields(true);
		$orderby = self::build_sql_orderby($args, $accepted_fields, get_option('dbem_categories_default_order'));
		//Now, build orderby sql
		$orderby_sql = ( count($orderby) > 0 ) ? 'ORDER BY '. implode(', ', $orderby) : '';
		
		//Create the SQL statement and execute
		$sql = "
			SELECT * FROM $categories_table
			$where
			GROUP BY category_id
			$orderby_sql
			$limit $offset
		";
		$results = $wpdb->get_results( apply_filters('em_categories_get_sql',$sql, $args), ARRAY_A);
		//If we want results directly in an array, why not have a shortcut here?
		if( $args['array'] == true ){
			return apply_filters('em_categories_get_array', $results, $args);
		}
		
		//Make returned results EM_Event objects
		$results = (is_array($results)) ? $results:array();
		$categories = array();
		foreach ( $results as $category_array ){
			$categories[$category_array['category_id']] = new EM_Category($category_array);
		}
		
		return apply_filters('em_categories_get', $categories, $args);
	}
	
	/**
	 * Will delete given an array of category_ids or EM_Event objects
	 * @param unknown_type $id_array
	 */
	function delete( $array ){
		global $wpdb;
		//Detect array type and generate SQL for event IDs
		$category_ids = array();
		if( @get_class(current($array)) == 'EM_Category' ){
			foreach($array as $EM_Category){
				$category_ids[] = $EM_Category->id;
			}
		}else{
			$category_ids = $array;
		}
		if(self::array_is_numeric($category_ids)){
			apply_filters('em_categories_delete', $category_ids);
			$condition = implode(" OR category_id=", $category_ids);
			//Delete all the categories
			$result_categories = $wpdb->query("DELETE FROM ". EM_CATEGORIES_TABLE ." WHERE category_id=$condition;");
			//Now delete the categories
			$result = $wpdb->query ( "DELETE FROM ". EM_CATEGORIES_TABLE ." WHERE category_id=$condition;" );
			do_action('em_categories_delete', $category_ids);
		}
		//TODO add error detection on categories delete fails
		return apply_filters('em_categories_delete', true, $category_ids);
	}

	function output( $args ){
		global $EM_Category;
		$EM_Category_old = $EM_Category; //When looping, we can replace EM_Category global with the current event in the loop
		//Can be either an array for the get search or an array of EM_Category objects
		if( is_object(current($args)) && get_class((current($args))) == 'EM_Category' ){
			$func_args = func_get_args();
			$categories = $func_args[0];
			$args = (!empty($func_args[1])) ? $func_args[1] : array();
			$args = apply_filters('em_categories_output_args', self::get_default_search($args), $categories);
			$limit = ( !empty($args['limit']) && is_numeric($args['limit']) ) ? $args['limit']:false;
			$offset = ( !empty($args['offset']) && is_numeric($args['offset']) ) ? $args['offset']:0;
			$page = ( !empty($args['page']) && is_numeric($args['page']) ) ? $args['page']:1;
		}else{
			$args = apply_filters('em_categories_output_args', self::get_default_search($args) );
			$limit = ( !empty($args['limit']) && is_numeric($args['limit']) ) ? $args['limit']:false;
			$offset = ( !empty($args['offset']) && is_numeric($args['offset']) ) ? $args['offset']:0;
			$page = ( !empty($args['page']) && is_numeric($args['page']) ) ? $args['page']:1;
			$args['limit'] = false;
			$args['offset'] = false;
			$args['page'] = false;
			$categories = self::get( $args );
		}
		//What format shall we output this to, or use default
		$format = ( $args['format'] == '' ) ? get_option( 'dbem_categories_list_item_format' ) : $args['format'] ;
		
		$output = "";
		$categories_count = count($categories);
		$categories = apply_filters('em_categories_output_categories', $categories);
		if ( count($categories) > 0 ) {
			$category_count = 0;
			$categories_shown = 0;
			foreach ( $categories as $EM_Category ) {
				if( ($categories_shown < $limit || empty($limit)) && ($category_count >= $offset || $offset === 0) ){
					$output .= $EM_Category->output($format);
					$categories_shown++;
				}
				$category_count++;
			}
			//Add headers and footers to output
			if( $format == get_option ( 'dbem_categories_list_item_format' ) ){
				$single_event_format_header = get_option ( 'dbem_categories_list_item_format_header' );
				$single_event_format_header = ( $single_event_format_header != '' ) ? $single_event_format_header : "<ul class='em-categories-list'>";
				$single_event_format_footer = get_option ( 'dbem_categories_list_item_format_footer' );
				$single_event_format_footer = ( $single_event_format_footer != '' ) ? $single_event_format_footer : "</ul>";
				$output =  $single_event_format_header .  $output . $single_event_format_footer;
			}
			//Pagination (if needed/requested)
			if( !empty($args['pagination']) && !empty($limit) && $categories_count >= $limit ){
				//Show the pagination links (unless there's less than 10 events, or the custom limit)
				$page_link_template = preg_replace('/(&|\?)page=\d+/i','',$_SERVER['REQUEST_URI']);
				$page_link_template = em_add_get_params($page_link_template, array('page'=>'%PAGE%'));
				$output .= apply_filters('em_events_output_pagination', em_paginate( $page_link_template, $categories_count, $limit, $page), $page_link_template, $categories_count, $limit, $page);
			}
		} else {
			$output = get_option ( 'dbem_no_categories_message' );
		}
		//FIXME check if reference is ok when restoring object, due to changes in php5 v 4
		$EM_Category_old= $EM_Category;
		return apply_filters('em_categories_output', $output, $categories, $args);		
	}
	
	/**
	 * If these categories collection are connected with an existing EM_Event object, then we can add categories to this event.  
	 */
	function save(){
		if( !empty($this->get_event()->id) ){
			global $wpdb;
			$event_id = $this->event->id;
			//remove old cats
			$wpdb->query('DELETE FROM '.EM_META_TABLE." WHERE object_id='$event_id' AND meta_key='event-category'");
			//Now add new ones
			$inserts = array();
			foreach($this->get_ids() as $id){
				$inserts[] = "($event_id,'event-category',$id)";
			}
			if( count($inserts) > 0 ){
				$result = $wpdb->query("INSERT INTO ".EM_META_TABLE." (`object_id`,`meta_key`,`meta_value`) VALUES ".implode(',',$inserts));
				if( $result === false ){
					$this->add_error( sprintf(__('Could not save the %s details due to a database error.', 'dbem'),__('category','dbem') ));
				}
			}
		}
		return apply_filters('em_categories_save', count($this->errors) == 0, $this);
	}
	
	function has( $search ){
		if( is_numeric($search) ){
			foreach($this->categories as $EM_Category){
				if($EM_Category->id == $search) return apply_filters('em_categories_has', true, $search, $this);
			}
		}
		return apply_filters('em_categories_has', false, $search, $this);
	}
	
	function get_ids(){
		$ids = array();
		foreach($this->categories as $EM_Category){
			if( !empty($EM_Category->id) ){
				$ids[] = $EM_Category->id;
			}
		}
		return $ids;	
	}
	
	/**
	 * Gets the event for this object, or a blank event if none exists
	 * @return EM_Event
	 */
	function get_event(){
		if( !( is_object($this->event) && get_class($this->event) == 'EM_Event' ) ){
			$this->event = new EM_Event();
		}
		return apply_filters('em_categories_get_event', $this->event, $this);
	}

	/* Overrides EM_Object method to apply a filter to result. Categories won't accept many arguments as you tend to search with events for much else.
	 * @see wp-content/plugins/categories-manager/classes/EM_Object#build_sql_conditions()
	 */
	function build_sql_conditions( $args = array() ){
		global $wpdb;
		$events_table = EM_EVENTS_TABLE;
		$locations_table = EM_LOCATIONS_TABLE;
		
		$temp_conditions = parent::build_sql_conditions($args);
		$conditions = array();
		if( !empty($temp_conditions['category']) ){
			$conditions['category'] = $temp_conditions['category'];
		}
		return apply_filters( 'em_categories_build_sql_conditions', $conditions, $args );
	}
	
	/* Overrides EM_Object method to apply a filter to result
	 * @see wp-content/plugins/categories-manager/classes/EM_Object#build_sql_orderby()
	 */
	function build_sql_orderby( $args, $accepted_fields, $default_order = 'ASC' ){
		return apply_filters( 'em_categories_build_sql_orderby', parent::build_sql_orderby($args, $accepted_fields, get_option('dbem_categories_default_order')), $args, $accepted_fields, $default_order );
	}
	
	/* 
	 * Adds custom categories search defaults
	 * @param array $array
	 * @return array
	 * @uses EM_Object#get_default_search()
	 */
	function get_default_search( $array = array() ){
		return apply_filters('em_categories_get_default_search', parent::get_default_search(array(),$array), $array, array());
	}	
	
	/**
	 * will return the default search parameter to use according to permission settings
	 * @return string
	 */
	function get_default_search_owner(){
		//by default, we only get categories the owner can manage
		$defaults = array('owner'=>false);
		//by default, we only get categories the owner can manage
		if( !current_user_can('edit_categories') ){
			$defaults['owner'] = get_current_user_id();
			break;
		}else{
			$defaults['owner'] = false;
			break;
		}
		return $defaults['owner'];
	}

	//Iterator Implementation
    public function rewind(){
        reset($this->categories);
    }  
    public function current(){
        $var = current($this->categories);
        return $var;
    }  
    public function key(){
        $var = key($this->categories);
        return $var;
    }  
    public function next(){
        $var = next($this->categories);
        return $var;
    }  
    public function valid(){
        $key = key($this->categories);
        $var = ($key !== NULL && $key !== FALSE);
        return $var;
    }
}