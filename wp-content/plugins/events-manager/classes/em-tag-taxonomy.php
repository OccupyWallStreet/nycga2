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
		global $wp_query, $EM_Tag, $em_tag_id;
		if( is_tax(EM_TAXONOMY_TAG) && get_option('dbem_cp_tags_formats', true)){
			add_filter('the_content', array('EM_Tag_Taxonomy','the_content'));
			$EM_Tag = em_get_tag($wp_query->queried_object->term_id);
			$wp_query->em_tag_id = $em_tag_id = $EM_Tag->term_id; //we assign $em_category_id just in case other themes/plugins do something out of the ordinary to WP_Query
			$wp_query->posts = array();
			$wp_query->posts[0] = new stdClass();
			$wp_query->posts[0]->post_title = $wp_query->queried_object->post_title = $EM_Tag->output(get_option('dbem_tag_page_title_format'));
			$post_array = array('ID', 'post_author', 'post_date','post_date_gmt','post_content','post_excerpt','post_status','comment_status','ping_status','post_password','post_name','to_ping','pinged','post_modified','post_modified_gmt','post_content_filtered','post_parent','guid','menu_order','post_type','post_mime_type','comment_count','filter');
			foreach($post_array as $post_array_item){
				$wp_query->posts[0]->$post_array_item = '';
			}
			$wp_query->post = $wp_query->posts[0];
			$wp_query->post_count = 1;
			$wp_query->found_posts = 1;
			$wp_query->max_num_pages = 1;
			//tweak flags for determining page type
			$wp_query->is_tax = 0;
			$wp_query->is_page = 1;
			$wp_query->is_single = 0;
			$wp_query->is_singular = 1;
			$wp_query->is_archive = 0;
			$template = locate_template(array('page.php','index.php'),false); //category becomes a page
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
	
	function parse_query(){
	    global $wp_query;
		if( is_tax(EM_TAXONOMY_TAG) ){
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