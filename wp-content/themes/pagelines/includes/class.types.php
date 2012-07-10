<?php
/**
 * 
 *
 *  PageLines Custom Post Type Class
 *
 *
 *  @package PageLines Framework
 *  @subpackage Post Types
 *  @since 4.0
 *
 */
class PageLinesPostType {

	var $id;		// Root id for section.
	var $settings;	// Settings for this section
	
	
	/**
	 * PHP5 constructor
	 *
	 */
	function __construct($id, $settings, $taxonomies = array(), $columns = array(), $column_display_function = '') {
		
		$this->id = $id;
		$this->taxonomies = $taxonomies;
		$this->columns = $columns;
		$this->columns_display_function = $column_display_function;
		
		$defaults = array(
				'label' 			=> 'Posts',
				'singular_label' 	=> 'Post',
				'description' 		=> null,
				'public' 			=> false,  
				'show_ui' 			=> true,  
				'capability_type'	=> 'post',  
				'hierarchical' 		=> false,  
				'rewrite' 			=> false,  
				'supports' 			=> array( 'title', 'editor', 'thumbnail' ), 
				'menu_icon' 		=> PL_ADMIN_IMAGES . '/favicon-pagelines.ico', 
				'taxonomies'		=> array(),
				'menu_position'		=> 20, 
				'featured_image'	=> false, 
				'has_archive'		=> false, 
				'map_meta_cap'		=> false,
				'dragdrop'			=> true, 
				'load_sections'		=> false,
				'query_var'			=> true
			);
		
		$this->settings = wp_parse_args($settings, $defaults); // settings for post type

		$this->register_post_type();
		$this->register_taxonomies();
		$this->register_columns();
		$this->featured_image();
		$this->section_loading();
	
	}

	/**
	 * The register_post_type() function is not to be used before the 'init'.
	 */
	function register_post_type(){
		add_action( 'init', array(&$this,'init_register_post_type') );
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function init_register_post_type(){
		
		$capability = (ploption('hide_controls_cpt')) ? ploption('hide_controls_cpt') : 'moderate_comments';
		
		register_post_type( $this->id , array(  
				'labels' => array(
							'name' 			=> $this->settings['label'],
							'singular_name' => $this->settings['singular_label'],
							'add_new'		=> __('Add New ', 'pagelines') . $this->settings['singular_label'], 
							'add_new_item'	=> __('Add New ', 'pagelines') . $this->settings['singular_label'], 
							'edit'			=> __('Edit ', 'pagelines') . $this->settings['singular_label'],
							'edit_item'		=> __('Edit ', 'pagelines') . $this->settings['singular_label'], 
							'view'			=> __('View ', 'pagelines') . $this->settings['singular_label'],
							'view_item'		=> __('View ', 'pagelines') . $this->settings['singular_label'],
						),
			
	 			'label' 			=> $this->settings['label'],  
				'singular_label' 	=> $this->settings['singular_label'],
				'description' 		=> $this->settings['description'],
				'public' 			=> $this->settings['public'],  
				'show_ui' 			=> $this->settings['show_ui'],  
				'capability_type'	=> $this->settings['capability_type'],  
				'hierarchical' 		=> $this->settings['hierarchical'],  
				'rewrite' 			=> $this->settings['rewrite'],  
				'supports' 			=> $this->settings['supports'], 
				'menu_icon' 		=> $this->settings['menu_icon'], 
				'taxonomies'		=> $this->settings['taxonomies'],
				'menu_position'		=> $this->settings['menu_position'],
				'has_archive'		=> $this->settings['has_archive'],
				'map_meta_cap'		=> $this->settings['map_meta_cap'],
				'query_var'			=> $this->settings['query_var'],
				'capabilities' => array(
			        'publish_posts' 		=> $capability,
			        'edit_posts' 			=> $capability,
			        'edit_others_posts' 	=> $capability,
			        'delete_posts' 			=> $capability,
			        'delete_others_posts' 	=> $capability,
			        'read_private_posts' 	=> $capability,
			        'edit_post' 			=> $capability,
			        'delete_post' 			=> $capability,
			        'read_post' 			=> $capability,
			    ),
				
			));
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function register_taxonomies(){
		
		if( !empty($this->taxonomies) ){
		
			foreach($this->taxonomies as $tax_id => $tax_settings){
			
				$defaults = array(
					'hierarchical' 		=> true, 
					'label' 			=> '', 
					'singular_label' 	=> '', 
					'rewrite' 			=> true
				);
					
				$a = wp_parse_args($tax_settings, $defaults);
			
				register_taxonomy( $tax_id, array($this->id), $a );
			}
			
		}
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function register_columns(){
		
		add_filter("manage_edit-{$this->id}_columns", array(&$this, 'set_columns'));
		
		add_action('manage_posts_custom_column',  array(&$this, 'set_column_values'));
	}
		

	/**
	*
	* @TODO document
	*
	*/
	function set_columns( $columns ){ 
		
		return $this->columns; 
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function set_column_values( $wp_column ){
		
		call_user_func( $this->columns_display_function, $wp_column );
						
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function set_default_posts( $callback, $object = false){
	
		if(!get_posts('post_type='.$this->id)){

			if($object)
				call_user_func( array($object, $callback), $this->id);
			else
				call_user_func($callback, $this->id);
		}
						
	}
	
	
	

	/**
	*
	* @TODO document
	*
	*/
	function section_loading(){
	
		if( ! $this->settings['dragdrop'] )
			add_filter('pl_cpt_dragdrop', array(&$this, 'remove_dragdrop'), 10, 2);
			
		if ( true === $this->settings['load_sections'] || is_array( $this->settings['load_sections'] ) )
			add_filter('pl_template_sections', array(&$this, 'load_sections_for_type'), 10, 3);
		
	}
		

		/**
		*
		* @TODO document
		*
		*/
		function load_sections_for_type( $sections, $template_type, $hook ){
			
			if( $template_type == $this->id || $template_type == get_post_type_plural( $this->id ) )
				return $this->settings['load_sections'];
			else
				return $sections;
			
		}
		

		/**
		*
		* @TODO document
		*
		*/
		function remove_dragdrop( $bool, $post_type ){
			if( $post_type == $this->id )
				return false;
			else
				return $bool;
		}

	
	/**
	 * Is the WP featured image supported
	 */
	function featured_image(){	
		
		if( $this->settings['featured_image'] )
			add_filter('pl_support_featured_image', array(&$this, 'add_featured_image'));

	}
	

		/**
		*
		* @TODO document
		*
		*/
		function add_featured_image( $support_array ){
		
			$support_array[] = $this->id;
			return $support_array;
		
		}

}
/////// END OF PostType CLASS ////////

/**
 * Checks to see if page is a CPT, or a CPT archive (type)
 *
 */
function pl_is_cpt( $type = 'single' ){
	
	if( !get_post_type() )
		return false;
	
	$std_pt = (get_post_type() == 'post' || get_post_type() == 'page' || get_post_type() == 'attachment') ? true : false;
	
	$is_type = ( ($type == 'archive' && is_archive()) || $type == 'single') ? true : false;
	
	return ( $is_type && !$std_pt  ? true : false);

}

/**
*
* @TODO do
*
*/
function get_post_type_plural( $id = null ){
	
	if(isset($id))
		return $id.'_archive';
	else
		return get_post_type().'_archive';	
}
