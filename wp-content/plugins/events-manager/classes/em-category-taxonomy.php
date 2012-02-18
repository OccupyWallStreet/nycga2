<?php
class EM_Category_Taxonomy{
	function init(){
		if( !is_admin() ){
			add_filter('archive_template', array('EM_Category_Taxonomy','template'));
			add_filter('category_template', array('EM_Category_Taxonomy','template'));
			add_filter('parse_query', array('EM_Category_Taxonomy','parse_query'));
		}
	}
	/**
	 * Overrides archive pages e.g. locations, events, event categories, event tags based on user settings
	 * @param string $template
	 * @return string
	 */
	function template($template){
		global $wp_query, $EM_Category;
		if( is_archive() ){
			if( !empty($wp_query->queried_object->taxonomy) && $wp_query->queried_object->taxonomy == EM_TAXONOMY_CATEGORY && get_option('dbem_cp_categories_formats', true)){
				$EM_Category = em_get_category($wp_query->queried_object->term_id);
				add_filter('the_content', array('EM_Category_Taxonomy','the_content'));
				$wp_query->posts = array();
				$wp_query->posts[0] = new stdClass();
				$wp_query->posts[0]->post_title = $EM_Category->output(get_option('dbem_category_page_title_format'));
				$post_array = array('ID', 'post_author', 'post_date','post_date_gmt','post_content','post_excerpt','post_status','comment_status','ping_status','post_password','post_name','to_ping','pinged','post_modified','post_modified_gmt','post_content_filtered','post_parent','guid','menu_order','post_type','post_mime_type','comment_count','filter');
				foreach($post_array as $post_array_item){
					$wp_query->posts[0]->$post_array_item = '';
				}
				$wp_query->post = $wp_query->posts[0];
				$wp_query->post_count = 1;
				$wp_query->found_posts = 1;
				$wp_query->max_num_pages = 1;
				//echo "<pre>"; print_r($wp_query); echo "</pre>";
				$template = locate_template(array('page.php','index.php'),false); //category becomes a page
			}
		}
		return $template;
	}
	
	function the_content($content){
		global $wp_query, $EM_Category;
		$EM_Category = new EM_Category($wp_query->queried_object);
		ob_start();
		em_locate_template('templates/category-single.php',true);
		return ob_get_clean();	
	}
	
	function parse_query( ){
		global $wp_query;
		if( !empty($wp_query->tax_query->queries[0]['taxonomy']) &&  $wp_query->tax_query->queries[0]['taxonomy'] == EM_TAXONOMY_CATEGORY) {
			//Scope is future
			$today = strtotime(date('Y-m-d', current_time('timestamp')));
			if( get_option('dbem_events_current_are_past') ){
				$wp_query->query_vars['meta_query'][] = array( 'key' => '_start_ts', 'value' => $today, 'compare' => '>=' );
			}else{
				$wp_query->query_vars['meta_query'][] = array( 'key' => '_end_ts', 'value' => $today, 'compare' => '>=' );
			}
		  	if( get_option('dbem_categories_default_archive_orderby') == 'title'){
		  		$wp_query->query_vars['orderby'] = 'title';
		  	}else{
			  	$wp_query->query_vars['orderby'] = 'meta_value_num';
			  	$wp_query->query_vars['meta_key'] = get_option('dbem_categories_default_archive_orderby','_start_ts');
		  	}
			$wp_query->query_vars['order'] = get_option('dbem_categories_default_archive_order','ASC');
		}
	}
}
EM_Category_Taxonomy::init();