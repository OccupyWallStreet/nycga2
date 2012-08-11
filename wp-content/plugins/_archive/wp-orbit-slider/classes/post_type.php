<?php 

if ( !class_exists('wordpress_custom_post_type') ) {
	
	class wordpress_custom_post_type{
  
		private $post_type, 
				$singular, 
				$plural, 
				$slug, 
				$args;
	  
		function __construct( $post_type, $options = array() ){
			// Extract optional values
			$singular = $plural = $slug = $args = NULL;
			extract($options, EXTR_IF_EXISTS);
			
			// Set class properties
			$this->post_type = $post_type;
			$this->singular = ( $singular ) ? $singular : ucfirst($this->post_type) ;
			$this->plural = ( $plural ) ? $plural : $this->singular.'s';
			$this->slug = ( $slug ) ? $slug : strtolower($this->plural);
			$this->args = $args;
			
			// Add rewrite rules for versions of WordPress older than 3.1
			global $wp_version;
			if( $wp_version < 3.1 )
				add_filter( 'generate_rewrite_rules', array($this, 'add_rewrite_rules') );
			  
			// Register post type
			add_action( 'init', array($this, 'register_post_type') );
			
			 
			// Change how templates are pulled for this post type
			add_filter( 'template_include', array($this, 'template_include') );
			add_action( 'template_redirect', array($this, 'context_fixer') );
			
		}
		  
		function register_post_type( ){
			// Default array of arguments for post type
			$defaults = array('labels' => array('name' => $this->plural,
												'singular_name' => $this->singular,
												'add_new' => __('Add New ', 'wp-orbit-slider').$this->singular,
												'add_new_item' => __('Add New ', 'wp-orbit-slider').$this->singular,
												'edit_item' => __('Edit ', 'wp-orbit-slider').$this->singular,
												'new_item' => __('New ', 'wp-orbit-slider').$this->singular,
												'view_item' => __('View ', 'wp-orbit-slider').$this->singular,
												'search_items' => __('Search ', 'wp-orbit-slider').$this->plural,
												'not_found' => sprintf( __('No %s found', 'wp-orbit-slider'), $this->plural ),
												'not_found_in_trash' => sprintf( __('No %s found in Trash', 'wp-orbit-slider'), $this->plural ) ),
							  'public' => true,
							  'has_archive' => true,
							  'rewrite' => array('slug' => $this->slug));
			  
			// Merge default arguments with passed arguments
			$args = wp_parse_args( $this->args, $defaults );
			  
			// Allow filtering of post type arguments
			$args = apply_filters( 'wp_'.$this->post_type.'_post_type_args', $args );
					  
			// Register the post type
			register_post_type($this->post_type, $args);
		}
			  
		function template_include( $template ) {
			// If our post type is being called, customize how the templates are pulled
			if ( get_query_var('post_type') == $this->post_type ) {
				// PAGE
				if( is_page() ){
					$page = locate_template( array($this->post_type.'/page.php', 'page-'.$this->post_type.'.php'));
					if ( $page ) return $page;
				}
				// SINGLE
				elseif( is_single() ){
					$single = locate_template( array($this->post_type.'/single.php') );
					if ( $single ) return $single;
				}
				// LOOP
				else{
					return locate_template( array($this->post_type.'/index.php', $this->post_type.'.php', 'index.php'));
				}
			}
			return $template;
		}
		  
		function context_fixer() {
			// Fix the context for our post type when on a posts page
			if ( get_query_var('post_type') == $this->post_type ) {
				global $wp_query;
				$wp_query->is_home = false;
			}
		}
		
		function wp_title( $title ){
			// Change page title for our post type when on a posts page
			if( get_query_var('post_type') == $this->post_type && !is_single() ){
				$title = $this->plural;
			}
			return $title;
		}
 
		  
		function add_rewrite_rules( $wp_rewrite ) {
			// Add rewrite rules that allow our post type URLs to work properly
			$new_rules = array();
			// This rewrite rule is not necessary in WP 3.1 because of the has_archive argument
			$new_rules[$this->slug . '/?$'] = 'index.php?post_type=' . $this->post_type;
			// This rewrite rule is not necessary in WP 3.1 because of the rewrite->feeds argument
			$new_rules[$this->slug . '/(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?post_type=' . $this->post_type . '&feed=' . $wp_rewrite->preg_index(1);
			// This rewrite rule is not necessary in WP 3.1 due to the rewrite->pages argument
			$new_rules[$this->slug . '/page/?([0-9]{1,})/?$'] = 'index.php?post_type=' . $this->post_type . '&paged=' . $wp_rewrite->preg_index(1);
			$wp_rewrite->rules = array_merge($new_rules, $wp_rewrite->rules);
			return $wp_rewrite;
		}

	}
	  
}

// Lets add the 16px icon to the post type
// Styling for the custom post type icon
 
add_action( 'admin_head', 'wpt_portfolio_icons' ); 
function wpt_portfolio_icons() { ?>
    <style type="text/css" media="screen">
        #menu-posts-vp_orbitslides .wp-menu-image {
            background: url(<?php echo plugins_url( 'css/images/icon16.png' , dirname(__FILE__) ); ?>) no-repeat 7px 6px !important;
        }
    #menu-posts-vp_orbitslides:hover .wp-menu-image, #menu-posts-vp_orbitslides.wp-has-current-submenu .wp-menu-image:hover {
            background-position:7px -23px !important;
        }
		
		#icon-edit.icon32-posts-vp_orbitslides {background: url(<?php echo plugins_url( 'css/images/icon32.png' , dirname(__FILE__) ); ?>) no-repeat;}
		
    </style>
<?php }

?>