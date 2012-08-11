<?php
/*
Plugin Name: WP Orbit Slider
Plugin URI: http://virtualpudding.com
Description: Orbit is a lightweight responsive jQuery slider created by the very excellent team <a href="http://www.zurb.com/playground/orbit-jquery-image-slider" target="_blank">Zurb</a>. This plugin uses both Custom Post Types &amp; Taxonomies. The Custom Post Type is what creates each slide. They can then be grouped into various taxonomies to display different sliders on various posts or pages.
Version: 1.0
Author: Virtual Pudding
Author URI: http://virtualpudding.com
License: GPL3


Copyright (c) 2011 Virtual Pudding, http://virtualpudding.com/

***WORDPRESS PLUGIN LICENCE***
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 3, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA


***ORBIT SLIDER LICENCE***
Released under the MIT license
More info at http://www.opensource.org/licenses/mit-license.php
		
*/

// Check for PHP 5+ and return error before errors
if( phpversion() < 5 )
	die( printf(__('Sorry, this plugin requires PHP version %s or later. You are currently running version %s.', 'wp-orbit-slider'), 5, phpversion()) );

// Define Constants
if( !defined('orbitslider_VER') ) define( 'orbitslider_VER', '1.0' );

// Includes
include( dirname(__FILE__).'/classes/post_type.php' );
include( dirname(__FILE__).'/classes/register_tax.php' );

// Start Main Class
if( !class_exists('orbit_slider') ){
	
	class orbit_slider{
	  
		// Define Post Type & Taxonomy
		private $post_type = 'vp_orbitslides',
				$taxonomy = 'slider-categories',
				$options,
				$options_page;
	  
		function __construct() {
			// Option Defaults
			$this->options = array(	'version' => orbitslider_VER,
								   	'animation' => 'fade',	// fade, horizontal-slide, vertical-slide, horizontal-push
									'animationSpeed' => '800',	// how fast animtions are
									'pauseOnHover' => 'true', // if you hover pauses the slider				
									'bullets' => 'true', // true or false to activate the bullet navigation
									'bulletThumbs' => 'false', // thumbnails for the bullets
									'centerBullets' => 'true', // center bullet nav with js, turn this off if you want to position the bullet nav manually
									'advanceSpeed' => '4000', // if timer is enabled, time between transitions				
									'directionalNav' => 'true',	// manual advancing directional navs
									'captions' => 'true', // do you want captions?
									'captionAnimation' => 'fade', // fade, slideOpen, none	
									'captionAnimationSpeed' => '800', // if so how quickly should they animate in	
									'timer' => 'false', // true or false to have the timer	
									'sliderTheme' => 'default', // default, custom, more to come...
									'readyLoad' => 'ready', // use doc ready or window load
									'imgSize' => 'orbit-slide',
									'loadJs' => 'header' // header, footer			
								     );		  
			// Register Post Type
			new wordpress_custom_post_type($this->post_type, 
										   array('singular' => __('Slide', 'wp-orbit-slider'), 
												 'plural' => __('Slides', 'wp-orbit-slider'),
											     'slug' => 'slide',
											     'args' => array('supports' => array('title', 'editor', 'thumbnail'))));

			// Register Taxonomy
			new wordpress_custom_taxonomy($this->taxonomy, 
										  $this->post_type, 
										  array('singular' => __('Slide Category', 'wp-orbit-slider'), 
											    'plural' => __('Slide Categories', 'wp-orbit-slider'), 
											    'slug' => 'slides',
												'args' => array('hierarchical' => TRUE)) );

			// Perform some maintenance activities on activation
			register_activation_hook( __FILE__, array($this, 'activate') );
			
			// Check if plugin has been updated, if so run activation function
			if( $this->get_options()->version != orbitslider_VER )
				$this->activate();
		  
			// Initiate key components
			add_action( 'after_setup_theme', array($this, 'after_setup_theme') );
			add_action( 'init', array($this, 'init') );
			add_action( 'admin_init', array($this, 'admin_init') );
			add_action( 'admin_menu', array($this, 'admin_menu') );
			add_action( 'admin_print_styles', array($this, 'load_css_for_options_page') );
		  
			// Load this plugin last to ensure other plugins don't overwrite theme support
			add_action( 'activated_plugin', array($this, 'load_last') );
			
			// Add menu items to the WordPress admin bar
			add_action( 'wp_before_admin_bar_render', array($this, 'wp_admin_bar') );

		}
		
		function wp_admin_bar() {
			global $wp_admin_bar;
			$wp_admin_bar->add_menu( array( 'parent' => 'appearance', 
											'id' => 'orbitslider_settings', 
											'title' => __('Orbit Slider Options', 'wp-orbit-slider'), 
											'href' => admin_url( 'edit.php?post_type='.$this->post_type.'&page=options' )) );
		}
		
		function load_css_for_options_page(){
			if( @$_GET['post_type'] == $this->post_type && @$_GET['page'] == 'options' )
				wp_enqueue_style('options_page_css', plugins_url('/css/admin.css', __FILE__) );
		}
				
		function get_include_contents( $filename ) {
			if( is_file($filename) ) {
				ob_start();
				include $filename;
				return ob_get_clean();
			}
			return false;
		}
	  
		function activate() {
			// Make sure user is using WordPress 3.0+
			$this->requires_wordpress_version();
			// Ensure all options are up-to-date when upgrading
			$this->option_management();
			// One time flush of rewrite rules
			flush_rewrite_rules();
		}
		
		function requires_wordpress_version( $ver = 3 ){
			global $wp_version;
			if( $wp_version < $ver )
				die( printf(__('Sorry, this plugin requires WordPress version %d or later. You are currently running version %s.', 'wp-orbit-slider'), $ver, $wp_version) );
		}
	  
		function after_setup_theme(){ 
			// Adds support for featured images and register some default image sizes
			add_theme_support( 'post-thumbnails' ); 
			add_image_size( 'orbit-slide', 540, 450, true ); 
			add_image_size( 'orbit-slide-small', 100, 83, true ); 
		}
	  
		function init(){
			
			$options = $this->get_options();
			
			// Add support for translations
			load_plugin_textdomain( 'wp-orbit-slider', FALSE, dirname(plugin_basename(__FILE__)).'/lang/' );
			
			// Load our js and css files
			add_action( 'wp_print_styles', array($this, 'enqueue_styles') );
						
			// Load scripts
			// Main JS
			add_action( 'wp_print_scripts', array($this, 'enqueue_scripts') );			
			// JS options
			$loadJs_footer = ( $options->loadJs );
			if ( $loadJs_footer == 'footer' ) {
			add_action( 'wp_footer', array($this, 'enqueue_script_options') , 20 );
			} else {
			add_action( 'wp_head', array($this, 'enqueue_script_options') , 20 );	
			}
			
			// Create [orbit-slider] shortcode
			add_shortcode( 'orbit-slider', array($this, 'show_slider') );
			
			// Enable use of the shortcode in text widgets
			add_filter( 'widget_text', 'do_shortcode' );
			
			// Add our custom columns
			add_filter( 'manage_edit-vp_orbitslides_columns', array($this, 'add_slider_columns') );			
			add_action( 'manage_posts_custom_column',  array($this, 'show_slider_columns') );
			
			// Create category filter dropdown for slides screen
			add_action( 'restrict_manage_posts', array($this, 'manage_posts_by_category') );
		}
		
		function enqueue_styles(){
			// Loads our styles, only on the front end of the site
			if( !is_admin() ){
				$options = $this->get_options();
				$cssout = ( $options->sliderTheme );
				wp_enqueue_style( 'orbitslider_main', plugins_url('/css/' . $cssout . '.css', __FILE__) );
			}
		}
		
		function enqueue_scripts(){
			// Loads our scripts, only on the front end of the site
			if( !is_admin() ){
				// Get plugin options
				$options = $this->get_options();
				// Load javascript
				$loadJs_footer = ( $options->loadJs == 'footer' ) ? TRUE : FALSE;				
				wp_enqueue_script( 'orbitslider_main', plugins_url('/js/jquery.orbit-1.3.0.min.js', __FILE__), array('jquery'), FALSE, $loadJs_footer );
			}
		}
		
		// Now for the slider JS options
		function enqueue_script_options () {
			$options = $this->get_options();
			$start = "\n<!-- Begin Orbit Slider -->\n";
			$start .= "<script type=\"text/javascript\">\n";
			$start .= "/* <![CDATA[ */\n";
			$end = "/* ]]> */\n";
			$end .= "</script>\n";
			$end .= "<!-- End Orbit Slider -->\n\n";
			//Doc Ready or Window load
			if ( $options->readyLoad == 'ready' ) { $rlout = '(document).ready'; } else { $rlout = '(window).load'; }
	
			// Hello options
			$out  = 'animation: "' . $options->animation .'",'."\n";
			$out .= 'animationSpeed: ' . $options->animationSpeed .','."\n";
			$out .= 'timer: ' . $options->timer .','."\n";
			$out .= 'advanceSpeed: ' . $options->advanceSpeed .','."\n";
			$out .= 'pauseOnHover: ' . $options->pauseOnHover .','."\n";
			$out .= 'directionalNav: ' . $options->directionalNav .','."\n";
			$out .= 'captions: ' . $options->captions .','."\n";
			$out .= 'captionAnimation: "' . $options->captionAnimation . '",'."\n";
			$out .= 'captionAnimationSpeed: ' . $options->captionAnimationSpeed . ','."\n";
			$out .= 'bullets: ' . $options->bullets .','."\n";
			$out .= 'bulletThumbs: ' . $options->bulletThumbs .','."\n";
			$out .= 'centerBullets: ' . $options->centerBullets .','."\n";
			$out .= 'fluid: true';

			// Output Everything
			$output = "jQuery" . $rlout . "(function() {\n";
			$output .= "jQuery('#orbit-inside').orbit({\n";
			$output .= "$out\n";
			$output .= "});\n";
			$output .= "});\n";
			echo $start . $output . $end;
	}


		function admin_init(){ 
			// Register plugin options
			register_setting( 'slider-settings-group', 'orbit_slider_options', array($this, 'update_options') ); 
			// Add meta boxes to our post type
			add_meta_box( 'orbit_slider_meta_box', __('Orbit Slider Options', 'wp-orbit-slider'), array($this, 'meta_box_content'), $this->post_type );
			// Save meta data when saving our post type
			add_action( 'save_post', array($this, 'save_meta_data') );
		}
	  
		function admin_menu(){
			// Create options page
			$this->options_page = add_submenu_page( 'edit.php?post_type='.$this->post_type, 
												    __('Orbit Slider Options', 'wp-orbit-slider'), 
												    __('Slider Options', 'wp-orbit-slider'), 
												    'manage_options', 
												    'options', 
												    array($this, 'options_page') );
		}
	  
		function load_last(){
			// Get array of active plugins
			if( !$active_plugins = get_option('active_plugins') ) return;
			// Set this plugin as variable
			$my_plugin = 'wp-orbit-slider/'.basename(__FILE__);
			// See if my plugin is in the array
			$key = array_search( $my_plugin, $active_plugins );
			// If my plugin was found
			if( $key !== FALSE ){
				// Remove it from the array
				unset( $active_plugins[$key] );
				// Reset keys in the array
				$active_plugins = array_values( $active_plugins );
				// Add my plugin to the end
				array_push( $active_plugins, $my_plugin );
				// Resave the array of active plugins
				update_option( 'active_plugins', $active_plugins );
			}
		}
	  
		function options_page(){ 
			// Load options page
			include( dirname(__FILE__).'/includes/options_page.php' ); 
		}
	  
		function meta_box_content() { 
		    // MetaBox
			include( dirname(__FILE__).'/includes/meta_box.php' ); 
		}
			
		function save_meta_data( $post_id ) {
			// If this is an auto save, our form has not been submitted, so we don't want to do anything
			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
			// Is the post type correct?
			if ( isset($_POST['post_type']) && $this->post_type != $_POST['post_type'] ) return $post_id;
			// Verify this came from our screen and with proper authorization, because save_post can be triggered at other times
			if ( !isset($_POST['orbit_slider_noncename']) || !wp_verify_nonce( $_POST['orbit_slider_noncename'], 'os_update_slidemeta' )) { return $post_id; }
			// Can user edit this post?
			if ( !current_user_can('edit_post', $post_id) ) return $post_id;
			// Setup array for the data we are saving	
			$data = array('_sliderCaption', '_sliderTarget', '_sliderUrl');
			// Use this filter to add postmeta to save for the default post type
			$data = apply_filters('orbitslider_add_meta_to_save', $data);
			// Save meta data
			foreach($data as $meta){
				// Get current meta
				$current = get_post_meta($post_id, $meta, TRUE);
				// Get new meta
				$new = $_POST[$meta];
				// If the new meta is empty, delete the current meta
				if( empty($new) ) delete_post_meta($post_id, $meta);
				// Otherwise, update the current meta
				else update_post_meta($post_id, $meta, $new);
			}
		}
	  
		function show_slider( $atts ){
			global $post;
			// Get plugin options
			$options = $this->get_options();
			// Get combined and filtered attribute list
			$options = shortcode_atts(array('post_type' => $this->post_type, 
											'category' => NULL,
											'numberposts' => -1), $atts);
			// Validate options
			foreach( $options as $option => $value )
				$options[$option] = $this->validate_options( $option, $value );
			
			// Extract shortcode attributes
			extract( $options );
			
			// Create an array with default values that we can use to build our query
			$query = array('numberposts' => $numberposts, 'post_type' => $post_type);
			
			// Set the category based on taxonomy.
			if( $category ){
				if( $query['post_type'] == $this->post_type ) $query[$this->taxonomy] = $category;
			}
			
			// Use the orbitslider_query filter to customize the results returned.
			$query = apply_filters('orbitslider_query', $query);

			// Run query and get posts
			if( !has_filter('orbitslider_custom_query_results') )
				$slider_posts = get_posts( $query );
		
			// If there are results, build slider.  Otherwise, don't show anything.
			if( $slider_posts ) { 
			
              // Begin Output
			  ob_start(); ?>
				  <div class="custom-slider<?php if ($category == true) { echo '-' . $category; } ?>"> 
					  <div id="orbit-inside">
	 
						  <?php foreach($slider_posts as $post): setup_postdata($post);
						  $options = $this->get_options();
						  // Get the title
						  $title = get_the_title(); 
						  // Get the excerpt
						  $excerpt = get_the_excerpt();
						  // Fetch image for slider
						  $imgsize = $options->imgSize;
						  $img = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), ''.$imgsize.'' ); $urlimg = $img['0'];
						  // Fetch image ID (then applied to content divs for styling individual content areas)
						  $imgid = get_post_thumbnail_id();
						  // Fetch thumbnail for slider
						  $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'orbit-slide-small' ); $urlthumb = $thumb['0'];							
						  // Slide Caption
						  $caption = get_post_meta($post->ID, '_sliderCaption', TRUE);
						  // Target exists?
						  $target = get_post_meta($post->ID, '_sliderTarget', TRUE);
						  // If so, target URL
						  $link = get_post_meta($post->ID, '_sliderUrl', TRUE); 
						  // If excerpt used, lets init captions
						  if( $caption == TRUE ) { 
						  $datacaption = 'data-caption="#slide-' . get_the_ID() . '"';
						  } else { 
						  $datacaption = '';
						  }
						  // Output the thumbnail
						  $datathumb = 'data-thumb="' . $urlthumb . '"';
						  
						  // If slide has target
						  if( $link == TRUE ) { 
						  echo '<a target="' . $target . '" href="' . $link . '"' . $datacaption . $datathumb . '><img class="orbit-slide" src="' . $urlimg . '" /></a>';
						  // If not and has html content
						  } elseif( !empty($post->post_content) ) {
						  echo '<div class="content" style=""' . $datathumb . '>';
						  echo '<div class="slide-content slide-content-' . $imgid . '">' . get_the_content() . '</div>';
						  echo '<img class="orbit-slide" src="' . $urlimg . '" />';
						  echo '</div>';
						  // Otherwise, lets just use images
						  } else {
						  echo '<img class="orbit-slide" src="' . $urlimg . '"' . $datacaption . $datathumb . ' />';
						  }
						  endforeach;  ?>
				  
			  
					  </div><!-- #orbit-inside -->
						  <?php // For slides with captions 
						  foreach($slider_posts as $post): setup_postdata($post);  
						  $caption = get_post_meta($post->ID, '_sliderCaption', TRUE);
						  echo '<span class="orbit-caption" id="slide-' . get_the_ID() . '">' . $caption . '</span>';
						  endforeach; ?>                              
				  </div><!-- .custom-slider -->
					
			  <?php wp_reset_query();	
			  
			  // End Output		  
			  return ob_get_clean();
		
			}
		}
				
		function add_slider_columns( $columns ){
			// Lets make a little more space and remove author column
			unset($columns['author']);
			// Create a new array so we can put columns in the order we want
			$new_columns = array();
			// Transfer columns to new array and append ours after the desired elements
			foreach($columns as $key => $value){
				$new_columns[$key] = $value;
				if($key == 'title')
					$new_columns[$this->taxonomy] = __('Slider Categories', 'wp-orbit-slider');
					// Lets add some thumbs for our slides
					$new_columns['thumbnail'] = __('Thumbnail', 'wp-orbit-slider');
					
			}
			// Return the new column configuration
			return $new_columns;
		}

		function show_slider_columns( $name ) {
			global $post;
			// Display our categories on the slider listing page
			switch ( $name ) {
				case $this->taxonomy:
					$terms = get_the_terms( $post->ID, $this->taxonomy );
					if( $terms ){
						$links = array();
						foreach( $terms as $term ){
							$links[] = '<a href="edit.php?post_type='.$this->post_type.'&'.$this->taxonomy.'='.$term->slug.'">'.$term->name.'</a>';
						}
						echo implode(', ', $links);
					}
					else
						_e('No Slider Categories', 'wp-orbit-slider');
					break;
					case 'thumbnail': 
						echo get_the_post_thumbnail($post->ID, 'orbit-slide-small');
					break;
			}
		}
		
		function manage_posts_by_category(){
			global $typenow;
			// If we are on our custom post type screen, add our custom taxonomy as a filter
			if( $typenow == $this->post_type ){
				$taxonomy = get_terms($this->taxonomy); 
				if( $taxonomy ): //print_r($taxonomy); ?>
					<select name="<?php echo $this->taxonomy; ?>" id="<?php echo $this->taxonomy; ?>" class="postform">
						<option value="">All Slide Categories</option><?php
						foreach( $taxonomy as $terms ): ?>
							<option value="<?php echo $terms->slug; ?>"<?php if( isset($_GET[$this->taxonomy]) && $terms->slug == $_GET[$this->taxonomy] ) echo ' selected="selected"'; ?>><?php echo $terms->name; ?></option><?php
						endforeach; ?>
					</select><?php
				endif;
			}
		}
		
		function get_options(){ 
			// Get options from database
			$options = get_option('orbit_slider_options'); 
			// If nothing, return false
			if( !$options ) return FALSE;
			// Otherwise, return the options as an object (my personal preference)
			return (object) $options;
		}
		
		function update_options( $options = array() ){
			// Get plugin default options as an array
			$defaults = (array) $this->options;
			// Get new options as an array
			$options = (array) $options;
			// Merge the arrays allowing the new options to override defaults
			$options = wp_parse_args( $options, $defaults );
			// Validate options
			foreach( $options as $option => $value ){
				$options[$option] = $this->validate_options( $option, $value );
				if( $value === FALSE ) unset($options[$option]);
			}
			// Return new options array
			return $options;
		}
		
		function validate_options( $option_name, $option_value ){
			switch( $option_name ){
				case 'version':
					return orbitslider_VER;
				case 'pauseOnHover':
					if( in_array($option_value, array('true', 'false')) ) 
						return $option_value;
					break;
				case 'bullets':
					if( in_array($option_value, array('true', 'false')) )
						return $option_value;
					break;
				case 'bulletThumbs':
					if( in_array($option_value, array('true', 'false')) )
						return $option_value;
					break;	
				case 'centerBullets':
					if( in_array($option_value, array('true', 'false')) )
						return $option_value;
					break;					
				case 'advanceSpeed':
					$option_value = (int) $option_value;
					if( is_int($option_value) && $option_value >= 100 && $option_value <= 8000 ) 
						return $option_value;
					break;
				case 'directionalNav':
					if( in_array($option_value, array('true', 'false')) )
						return $option_value;
					break;
				case 'captionAnimation':
					if( in_array($option_value, array('fade', 'slideOpen', 'none')) )
						return $option_value;
					break;
				case 'captionAnimationSpeed':
					$option_value = (int) $option_value;
					if( is_int($option_value) && $option_value >= 100 && $option_value <= 8000 ) 
						return $option_value;
					break;	
				case 'animationSpeed':
					$option_value = (int) $option_value;
					if( is_int($option_value) && $option_value >= 100 && $option_value <= 8000 ) 
						return $option_value;
					break;					
				case 'timer':
					if( in_array($option_value, array('true', 'false')) )
						return $option_value;
					break;
				case 'captions':
					if( in_array($option_value, array('true', 'false')) )
						return $option_value;
					break;
				case 'loadJs':
					if( in_array($option_value, array('head', 'footer')) )
						return $option_value;
					break;
				case 'imgSize':
					if( in_array($option_value, array('orbit-slide', 'orbit-custom')) )
						return $option_value;
					break;					
				case 'readyLoad':
					if( in_array($option_value, array('ready', 'load')) )
						return $option_value;
					break;					
				case 'sliderTheme':
					if( in_array($option_value, array('default', 'custom')) )
						return $option_value;
					break;	
				case 'post_type':
					if( $option_value != $this->post_type && post_type_exists($option_value) ) 
						return $option_value;
					return $this->post_type;
					break;
				case 'category':
					if( term_exists($option_value) )
						return $option_value;
					return FALSE;
				case 'id':
					return $option_value;
					break;
				case 'numberposts':
					$option_value = (int) $option_value;
					if( is_int($option_value) )
						return $option_value;
					return -1;
					break;
				case 'animation':
					if( in_array($option_value, array('fade', 'horizontal-slide', 'vertical-slide', 'horizontal-push')) )
						return $option_value;
					break;
				default:
					return FALSE;
			}
			return $this->options[$option_name];
		}
		
		function save_options( $options ){
			// Takes an array or object and saves the options to the database after validating
			update_option('orbit_slider_options', $this->update_options($options));
		}
		
		function option_management(){
			// Get existing options array, if available
			$options = (array) $this->get_options();
			// Properly saves options and updates plugin version
			$this->save_options( $options );
		}

	}
  
}

if( class_exists('orbit_slider') ){
	new orbit_slider();
}

?>