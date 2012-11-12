<?php
/**
 * Get an tag in a db friendly way, by checking globals and passed variables to avoid extra class instantiations
 * @param mixed $id
 * @return EM_Tag
 */
function em_get_tag($id = false) {
	global $EM_Tag;
	//check if it's not already global so we don't instantiate again
	if( is_object($EM_Tag) && get_class($EM_Tag) == 'EM_Tag' ){
		if( $EM_Tag->term_id == $id ){
			return $EM_Tag;
		}elseif( is_object($id) && $EM_Tag->term_id == $id->term_id ){
			return $EM_Tag;
		}
	}
	if( is_object($id) && get_class($id) == 'EM_Tag' ){
		return $id;
	}else{
		return new EM_Tag($id);
	}
}
class EM_Tag extends EM_Object {	
	//Taxonomy Fields
	var $id = '';
	var $term_id;
	var $name;
	var $slug;
	var $term_group;
	var $term_taxonomy_id;
	var $taxonomy;
	var $description = '';
	var $parent = 0;
	var $count;
	
	/**
	 * Gets data from POST (default), supplied array, or from the database if an ID is supplied
	 * @param $tag_data
	 * @return null
	 */
	function EM_Tag( $tag_data = false ) {
		global $wpdb;
		//Initialize
		$tag = array();
		if( !empty($tag_data) ){
			//Load tag data
			if( is_object($tag_data) && !empty($tag_data->taxonomy) && $tag_data->taxonomy == EM_TAXONOMY_TAG ){
				$tag = $tag_data;
			}elseif( !is_numeric($tag_data) ){
				$tag = get_term_by('name', $tag_data, EM_TAXONOMY_TAG);
				if( empty($tag) ){
					$tag = get_term_by('slug', $tag_data, EM_TAXONOMY_TAG);					
				}
			}else{		
				$tag = get_term_by('id', $tag_data, EM_TAXONOMY_TAG);
			}
		}
		if( !empty($tag) ){
			foreach($tag as $key => $value){
				$this->$key = $value;
			}
			$this->id = $this->term_id; //backward compatability
		}
		do_action('em_tag',$this, $tag_data);
	}
	
	function get_url(){
		if( empty($this->link) ){
			$this->ms_global_switch();
			$this->link = get_term_link($this->slug, EM_TAXONOMY_TAG);
			$this->ms_global_switch_back();
			if ( is_wp_error($this->link) ) $this->link = '';
		}
		return $this->link;
	}
	
	function output_single($target = 'html'){
		$format = get_option ( 'dbem_tag_page_format' );
		return apply_filters('em_tag_output_single', $this->output($format, $target), $this, $target);	
	}
	
	function output($format, $target="html") {
		preg_match_all('/\{([a-zA-Z0-9_]+)\}([^{]+)\{\/[a-zA-Z0-9_]+\}/', $format, $conditionals);
		if( count($conditionals[0]) > 0 ){
			foreach($conditionals[1] as $key => $condition){
				$format = str_replace($conditionals[0][$key], apply_filters('em_tag_output_condition', '', $condition, $conditionals[0][$key], $this), $format);
			}
		}
		$tag_string = $format;		 
	 	preg_match_all("/(#@?_?[A-Za-z0-9]+)({([a-zA-Z0-9,]+)})?/", $format, $placeholders);
		foreach($placeholders[1] as $key => $result) {
			$match = true;
			$replace = '';
			$full_result = $placeholders[0][$key];
			switch( $result ){
				case '#_TAGNAME':
					$replace = $this->name;
					break;
				case '#_TAGID':
					$replace = $this->term_id;
					break;
				case '#_TAGLINK':
				case '#_TAGURL':
					$link = $this->get_url();
					$replace = ($result == '#_TAGURL') ? $link : '<a href="'.$link.'">'.esc_html($this->name).'</a>';
					break;
				case '#_TAGEVENTSPAST': //depreciated, erroneous documentation, left for compatability
				case '#_TAGEVENTSNEXT': //depreciated, erroneous documentation, left for compatability
				case '#_TAGEVENTSALL': //depreciated, erroneous documentation, left for compatability
				case '#_TAGPASTEVENTS':
				case '#_TAGNEXTEVENTS':
				case '#_TAGALLEVENTS':
					//convert depreciated placeholders for compatability
					$result = ($result == '#_TAGEVENTSPAST') ? '#_TAGPASTEVENTS':$result; 
					$result = ($result == '#_TAGEVENTSNEXT') ? '#_TAGNEXTEVENTS':$result;
					$result = ($result == '#_TAGEVENTSALL') ? '#_TAGALLEVENTS':$result;
					//forget it ever happened? :/
					if ($result == '#_TAGPASTEVENTS'){ $scope = 'past'; }
					elseif ( $result == '#_TAGNEXTEVENTS' ){ $scope = 'future'; }
					else{ $scope = 'all'; }					
					$events = EM_Events::get( array('tag'=>$this->term_id, 'scope'=>$scope) );
					if ( count($events) > 0 ){
						$replace .= get_option('dbem_tag_event_list_item_header_format','<ul>');
						foreach($events as $EM_Event){
							$replace .= $EM_Event->output(get_option('dbem_tag_event_list_item_format'));
						}
						$replace .= get_option('dbem_tag_event_list_item_footer_format');
					} else {
						$replace = get_option('dbem_tag_no_events_message','</ul>');
					}
					break;
				default:
					$replace = $full_result;
					break;
			}
			$replace = apply_filters('em_tag_output_placeholder', $replace, $this, $full_result, $target); //USE WITH CAUTION! THIS MIGHT GET RENAMED
			$tag_string = str_replace($full_result, $replace , $tag_string );
		}
		return apply_filters('em_tag_output', $tag_string, $this, $format, $target);	
	}
}
?>