<?php
class EM_Tag_Taxonomy{
	function init(){
		if( !is_admin() ){
			add_filter('archive_template', array('EM_Tag_Taxonomy','template'));
			add_filter('category_template', array('EM_Tag_Taxonomy','template'));
			add_filter('parse_query', array('EM_Tag_Taxonomy','parse_query'));
		}
	}
	/**
	 * Overrides archive pages e.g. locations, events, event tags, event tags based on user settings
	 * @param string $template
	 * @return string
	 */
	function template($template){
		global $wp_query;
		if( is_archive() ){
			if( !empty($wp_query->queried_object->taxonomy) && $wp_query->queried_object->taxonomy == EM_TAXONOMY_TAG && get_option('dbem_cp_tags_formats', true)){
				add_filter('the_content', array('EM_Tag_Taxonomy','the_content'));
				$EM_Tag = em_get_tag($wp_query->queried_object->term_id);
				$wp_query->posts = array();
				$wp_query->posts[0] = new stdClass();
				$wp_query->posts[0]->post_title = $EM_Tag->output(get_option('dbem_tag_page_title_format'));
				$wp_query->posts[0]->post_content = '';
				$wp_query->post = $wp_query->posts[0];
				$wp_query->post_count = 1;
				$wp_query->found_posts = 1;
				$wp_query->max_num_pages = 1;
				$template = locate_template(array('page.php','index.php'),false); //category becomes a page
			}
		}
		return $template;
	}
	
	function the_content($content){
		global $wp_query, $EM_Tag;
		$EM_Tag = new EM_Tag($wp_query->queried_object);
		ob_start();
		em_locate_template('templates/tag-single.php',true);
		return ob_get_clean();	
	}
	
	function parse_query( ){
		global $wp_query;
		if( !empty($wp_query->tax_query->queries[0]['taxonomy']) &&  $wp_query->tax_query->queries[0]['taxonomy'] == EM_TAXONOMY_TAG) {
			//Scope is future
			$today = strtotime(date('Y-m-d', current_time('timestamp')));
			if( get_option('dbem_events_current_are_past') ){
				$wp_query->query_vars['meta_query'][] = array( 'key' => '_start_ts', 'value' => $today, 'compare' => '>=' );
			}else{
				$wp_query->query_vars['meta_query'][] = array( 'key' => '_end_ts', 'value' => $today, 'compare' => '>=' );
			}
		  	if( get_option('dbem_tags_default_archive_orderby') == 'title'){
		  		$wp_query->query_vars['orderby'] = 'title';
		  	}else{
			  	$wp_query->query_vars['orderby'] = 'meta_value_num';
			  	$wp_query->query_vars['meta_key'] = get_option('dbem_tags_default_archive_orderby','_start_ts');
		  	}
			$wp_query->query_vars['order'] = get_option('dbem_tags_default_archive_order','ASC');
		}
	}
}
EM_Tag_Taxonomy::init();