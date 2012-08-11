<?php
class EM_Location_Post {
	function init(){
		//Front Side Modifiers
		if( !is_admin() ){
			//override single page with formats? 
			add_filter('the_content', array('EM_Location_Post','the_content'));
			//display as page template?
			if( get_option('dbem_cp_locations_template_page') ){
				add_filter('single_template',array('EM_Location_Post','single_template'));
			}
		}
		add_action('parse_query', array('EM_Location_Post','parse_query'));
	}	
	
	/**
	 * Overrides the default post format of a location and can display a location as a page, which uses the page.php template.
	 * @param string $template
	 * @return string
	 */
	function single_template($template){
		global $post;
		if( !locate_template('single-'.EM_POST_TYPE_LOCATION.'.php') && $post->post_type == EM_POST_TYPE_LOCATION ){
			$template = locate_template(array('page.php','index.php'),false);
		}
		return $template;
	}
	
	function the_content( $content ){
		global $post, $EM_Location;
		if( $post->post_type == EM_POST_TYPE_LOCATION ){
			if( is_archive() || is_search() ){
				if( get_option('dbem_cp_locations_archive_formats') ){
					$EM_Location = em_get_location($post);
					$content = $EM_Location->output(get_option('dbem_location_list_item_format'));
				}
			}else{
				if( get_option('dbem_cp_locations_formats') && !post_password_required() ){
					$EM_Location = em_get_location($post);
					ob_start();
					em_locate_template('templates/location-single.php',true);
					$content = ob_get_clean();
				}
			}
		}
		return $content;
	}
	
	function parse_query( ){
		global $wp_query;
		if( !empty($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] == EM_POST_TYPE_LOCATION ){
			if( is_admin() ){
				$wp_query->query_vars['orderby'] = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby']:'title';
				$wp_query->query_vars['order'] = (!empty($_REQUEST['order'])) ? $_REQUEST['order']:'ASC';
			}else{
				if( empty($wp_query->query_vars['location']) ) {
				  	if( get_option('dbem_locations_default_archive_orderby') == 'title'){
				  		$wp_query->query_vars['orderby'] = 'title';
				  	}else{
					  	$wp_query->query_vars['orderby'] = 'meta_value_num';
					  	$wp_query->query_vars['meta_key'] = get_option('dbem_locations_default_archive_orderby','_location_country');	  		
				  	}
					$wp_query->query_vars['order'] = get_option('dbem_locations_default_archive_orderby','ASC');
				}
			}
		}
	}
}
EM_Location_Post::init();