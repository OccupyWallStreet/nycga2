<?php 

if ( !class_exists('wordpress_custom_taxonomy') ) {
	
	class wordpress_custom_taxonomy{
  
		private $taxonomy, $object_type, $singular, $plural, $slug, $args;
	  
		function __construct( $taxonomy, $object_type, $options = array() ){
		
			// Extract optional values
			$singular = $plural = $slug = $args = NULL;
			extract($options, EXTR_IF_EXISTS);
		
			// Set class properties
			$this->taxonomy = $taxonomy;
			$this->object_type = $object_type;
			$this->singular = ( $singular ) ? $singular : ucfirst($this->taxonomy) ;
			$this->plural = ( $plural ) ? $plural : $this->singular.'s';
			$this->slug = ( $slug ) ? $slug : strtolower($this->plural);
			$this->args = $args;
		  
			// Register taxonomy
			add_action( 'init', array(&$this, 'register_taxonomy') );
		}
	  
		function register_taxonomy(){
			// Create array of arguments for taxonomy
			$defaults = array('labels' => array('name' => $this->plural,
												'singular_name' => $this->singular,
												'search_items' => __('Search ', 'wp-orbit-slider').$this->plural,
												'popular_items' => __('Popular ', 'wp-orbit-slider').$this->plural,
												'all_items' => __('All ', 'wp-orbit-slider').$this->plural,
												'parent_item' => __('Parent ', 'wp-orbit-slider').$this->singular,
												'parent_item_colon' => sprintf( __('Parent %s:', 'wp-orbit-slider'), $this->singular ),
												'edit_item' => __('Edit ', 'wp-orbit-slider').$this->singular,
												'update_item' => __('Update ', 'wp-orbit-slider').$this->singular,
												'add_new_item' => __('Add New ', 'wp-orbit-slider').$this->singular,
												'new_item_name' => sprintf( __('New %s Name', 'wp-orbit-slider'), $this->singular ),
												'separate_items_with_commas' => sprintf( __('Separate %s with commas', 'wp-orbit-slider'), $this->plural),
												'add_or_remove_items' => __('Add or remove ', 'wp-orbit-slider').$this->plural,
												'choose_from_most_used' => __('Choose from the most used ', 'wp-orbit-slider').$this->plural),
							  'rewrite' => array('slug' => $this->slug));
			
			// Merge default arguments with passed arguments
			$args = wp_parse_args( $this->args, $defaults );
			
			// Allow editing of default arguments using the filter: wp_{taxonomy}_args
			$args = apply_filters('wp_'.$this->taxonomy.'_tax_args', $args);
		  
			// Register the taxonomy
			register_taxonomy($this->taxonomy, $this->object_type, $args);
		}
	}

}

?>