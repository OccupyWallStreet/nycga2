<?php 

// Create and instance of our rewrite machine
$more_fields_rewrite = new more_fields_rewrite_object; 

class more_fields_rewrite_object {

	/*
	**	more_fields_rewrite_object()
	**
	*/
	function more_fields_rewrite_object () {

		// Enable querying of Custom Fields using rewrite engine
		add_filter('query_vars', array(&$this, 'query_vars'));
		add_filter('posts_join', array(&$this, 'query_join'));
		add_filter('posts_where', array(&$this, 'query_where'));
		// add_filter('generate_rewrite_rules', array(&$this, 'generate_rewrite_rules'));
	}
		
	/*
	**	query_vars()
	**
	*/
	function query_vars( $qvars ) {
		$qvars[] = 'mf_key';
		$qvars[] = 'mf_value';
		return $qvars;
	}

	/*
	**	query_join()
	**
	*/
	function query_join( $join ) {
		global $wpdb, $wp_query;
		if (!is_callable(array($wp_query, 'get'))) return false; 
		$gettype = (array_key_exists('type', $_GET)) ? esc_attr($_GET['type']) : 0; 
		if ($key = $wp_query->get('mf_key') || $type = $gettype) {
			$join .= " LEFT JOIN $wpdb->postmeta as meta ON $wpdb->posts.ID = meta.post_id";
		}
		return $join;
	}

	/*
	**
	**
	*/
	function query_where( $where ) {
		global $wpdb, $wp_query;

		if (!is_callable(array($wp_query, 'get'))) return false; 

		// Should probably be exclusive instead of inclusive
		if(strpos($_SERVER['SCRIPT_NAME'], '/media-upload.php')) return $where;

		$key = $wp_query->get('mf_key');
		$value = $wp_query->get('mf_value');

		if ( $key )
			$catch = "and $wpdb->posts.post_status='publish'";
			
		if ( $key && $value ) $where .= " AND meta.meta_value='$value'" . $catch;
		else if ($key) $where .= " AND meta.meta_value=!''" . $catch;

		// We want to be able to sort by panel type
		$gettype = (array_key_exists('type', $_GET)) ? esc_attr($_GET['type']) : 0; 
		if  (($type = $gettype) && is_admin()) {
			$where .= " AND meta.meta_key='mf_page_type' AND meta.meta_value='$type'";			
		 }
		 //echo $where;
		//exit(0);
		return $where;
	}	
}

?>