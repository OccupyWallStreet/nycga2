<?php
/**
 * Widget Builder
 *
 * This file build
 *
 * @author Timothy Wood @codearachnid
 * @copyright Modern Tribe, Inc.
 * @package Tribe_Widget_Builder
 **/

// Block direct requests
if ( !defined( 'ABSPATH' ) )
	die();

if ( !class_exists( 'Tribe_Widget_Builder' ) ) {

	/**
	 * Widget Builder
	 *
	 * @package Tribe_Widget_Builder
	 * @author Timothy Wood
	 */
	class Tribe_Widget_Builder {
		const POST_TYPE = 'tribe_widget_builder';
		const TOKEN = self::POST_TYPE;

		private $base_path;

		/**
		 *
		 * Tribe_Widget_Builder Constructor
		 *
		 */
		public function __construct() {
			$this->load_plugin_text_domain();

			// setup the base path for includes in this plugin
			$this->base_path = rtrim( plugin_dir_path(__FILE__), '/classes');

			add_action( 'init', array( $this, 'register_post_type' ), 20 );
			add_action( 'widgets_init', array( $this, 'register_widgets' ) );

			if ( is_admin() ) {

				// remove publish box
				add_action( 'admin_menu', array( &$this, 'remove_publish_box') );

				// setup meta boxes for custom fields
				add_action( 'add_meta_boxes', array( $this, 'meta_box_setup' ) );
				add_action( 'save_post', array( $this, 'meta_box_save') );

				// change the post status messages when saving, publishing or updating
				add_filter( 'post_updated_messages', array( &$this, 'widet_status_message') );

					// caching hooks
				add_action( 'save_post', array( $this, 'maybe_clear_widget_data_cache_on_save_post' ), 10, 2);
				add_action( 'added_post_meta', array( $this, 'maybe_clear_widget_data_cache_on_update_post_meta' ), 10, 3);
				add_action( 'updated_post_meta', array( $this, 'maybe_clear_widget_data_cache_on_update_post_meta' ), 10, 3);
				add_action( 'deleted_post_meta', array( $this, 'maybe_clear_widget_data_cache_on_update_post_meta' ), 10, 3);

			}
		}


		/**
		 * widget_status_message function.
		 *
		 * @access public
		 * @return $messages
		 */
		function widet_status_message( $messages ) {
			if( self::TOKEN == get_post_type() ) {
				$messages["post"][1] = __( 'Widget content has been updated.', 'widget-builder' );
				$messages["post"][2] = '';
				$messages["post"][3] = $messages["post"][2];
				$messages["post"][4] = $messages["post"][1];
				$messages["post"][6] = __( 'Widget has been created.', 'widget-builder' );
				$messages["post"][8] = __( 'Widget has been created.', 'widget-builder' );
			}
			return $messages;
		}

		/**
		 * register_post_type function.
		 *
		 * @access public
		 * @return void
		 */
		public function register_post_type () {
			$page = 'themes.php';

			$menu = __( 'Widget Builder', 'widget-builder' );
			$singular = __( 'Widget', 'widget-builder' );
			$plural = __( 'Widgets', 'widget-builder' );
			$rewrite = array( 'slug' => '' );
			$supports = array( 'title','editor','thumbnail' );

			if ( $rewrite == '' ) { $rewrite = self::TOKEN; }

			$labels = array(
				'name' => $menu,
				'singular_name' => sprintf( __( '%s', 'widget-builder' ), $singular ),
				'add_new' => sprintf( __( 'Add New %s', 'widget-builder' ), $singular ),
				'add_new_item' => sprintf( __( 'Add New %s', 'widget-builder' ), $singular ),
				'edit_item' => sprintf( __( 'Edit %s', 'widget-builder' ), $singular ),
				'new_item' => sprintf( __( 'New %s', 'widget-builder' ), $singular ),
				'all_items' => $menu,
				'view_item' => sprintf( __( 'View %s', 'widget-builder' ), $singular ),
				'search_items' => sprintf( __( 'Search %a', 'widget-builder' ), $plural ),
				'not_found' =>  sprintf( __( 'No %s Found', 'widget-builder' ), $plural ),
				'not_found_in_trash' => sprintf( __( 'No %s Found In Trash', 'widget-builder' ), $plural ),
				'parent_item_colon' => '',
				'menu_name' => $menu

			);
			$args = array(
				'labels' => $labels,
				'public' => false,
				'publicly_queryable' => false,
				'show_ui' => true,
				'show_in_nav_menus' => false,
				'show_in_admin_bar' => false,
				'show_in_menu' => $page,
				'query_var' => true,
				'rewrite' => $rewrite,
				'capability_type' => 'post',
				'has_archive' => false,
				'hierarchical' => false,
				'menu_position' => null,
				'supports' => $supports,
			);
			register_post_type( self::POST_TYPE, $args );
		} // End register_post_type()

		/**
		 * Register each published widget
		 */
		public function register_widgets() {
			foreach ( $this->get_widget_data() as $widget ) {
				tribe_register_widget('Tribe_Widget_Builder_Display', $widget);
			}
		}

		/**
		 * @return array Arguments to be passed to each widget
		 */
		public function get_widget_data() {
			$widget_params = $this->get_widget_data_cache();

			if ( $widget_params ) {
				return $widget_params;
			}

			$available_custom_widgets = $this->get_widget_posts();

			if( !empty($available_custom_widgets) ) {
				foreach($available_custom_widgets as $widget) {
					$widget_params[] = array(
						'ID' => $widget->ID,
						'title' => $widget->post_title,
						'content' => $widget->post_content,
						'image' => ( has_post_thumbnail( $widget->ID ) ) ? wp_get_attachment_image_src( get_post_thumbnail_id( $widget->ID ), 'single-post-thumbnail' ) : null,
						'link_url' => get_post_meta($widget->ID, '_' . Tribe_Widget_Builder::TOKEN . '_link_url', true),
						'link_text' => get_post_meta($widget->ID, '_' . Tribe_Widget_Builder::TOKEN . '_link_text', true),
						'widget_description' => get_post_meta($widget->ID, '_' . Tribe_Widget_Builder::TOKEN . '_widget_description', true),
						'token' => Tribe_Widget_Builder::TOKEN
					);
				}
			}

			$this->set_widget_data_cache($widget_params);

			return $widget_params;
		}

		public function maybe_clear_widget_data_cache_on_save_post( $post_id, $post ) {
			if ( $post->post_type == self::POST_TYPE ) {
				$this->clear_widget_data_cache();
			}
		}

		public function maybe_clear_widget_data_cache_on_update_post_meta( $meta_id, $post_id, $meta_key ) {
			if ( !in_array( $meta_key, array('_edit_lock', '_edit_last') ) ) {
				if ( get_post_type($post_id) == self::POST_TYPE ) {
					$this->clear_widget_data_cache();
				}
			}
		}

		private function get_widget_data_cache() {
			$data = wp_cache_get(self::TOKEN.'_widget_data', self::TOKEN);
			if ( !$data ) { $data = array(); }
			$data = apply_filters( self::TOKEN.'_widget_data_cache', $data );
			return $data;
		}

		private function set_widget_data_cache( array $data ) {
			wp_cache_set(self::TOKEN.'_widget_data', $data, self::TOKEN);
			do_action( self::TOKEN.'_updated_widget_data_cache' );
		}

		private function clear_widget_data_cache() {
			wp_cache_delete(self::TOKEN.'_widget_data', self::TOKEN);
			do_action( self::TOKEN.'_cleared_widget_data_cache' );
		}

		/**
		 * @return array Widget post objects
		 */
		private function get_widget_posts() {
			// setup CPT query args
			$args = array(
				'numberposts'  => -1,
				'post_type'    => Tribe_Widget_Builder::POST_TYPE,
				'post_status'  => 'publish'
			);

			// filter 'tribe_widget_builder_get_posts_args' to modify the cpt query arguments
			$args = apply_filters( Tribe_Widget_Builder::TOKEN . '_get_posts_args', $args );

			$available_custom_widgets = get_posts($args);

			// filter 'tribe_widget_builder_get_posts' to override the cpt query
			$available_custom_widgets = apply_filters( Tribe_Widget_Builder::TOKEN . '_get_posts', $available_custom_widgets );

			return $available_custom_widgets;
		}

		/**
		 * remove_publish_box function.
		 *
		 * @access public
		 * @return void
		 */
		function remove_publish_box() {
			remove_meta_box( 'submitdiv', self::TOKEN, 'side' );
		}

		/**
		 * meta_box_setup function.
		 *
		 * @access public
		 * @return void
		 */
		public function meta_box_setup() {

			// add custom publish box
			add_meta_box(
				self::TOKEN . '_publish',
				__('Publish', 'widget-builder' ),
				array( &$this, 'meta_box_publish' ),
				self::TOKEN,
				'side',
				'high'
			);

			// add link details
			add_meta_box(
				self::TOKEN . '_link_details',
				__('Widget Link Details', 'widget-builder' ),
				array( &$this, 'meta_box_content' ),
				self::POST_TYPE,
				'side',
				'default'
			);

			// add internal widget details
			add_meta_box(
				self::TOKEN . '_widet_details',
				__('Widget Admin Details', 'widget-builder' ),
				array( &$this, 'meta_box_widget' ),
				self::POST_TYPE,
				'normal',
				'low'
			);

			// cleanup excerpt in case it's showing
			remove_meta_box( 'postexcerpt', self::POST_TYPE, 'normal' );


		}

		/**
		 * meta_box_publish function.
		 *
		 * @access public
		 * @return void
		 */
		public function meta_box_publish() {

			global $action, $post;

			$post_type = self::TOKEN;
			$post_type_object = get_post_type_object($post_type);
			$can_publish = current_user_can($post_type_object->cap->publish_posts);

			// get template hierarchy
			include( $this->get_template_hierarchy( 'metabox_pub' ) );

		}

		/**
		 * meta_box_content function.
		 *
		 * @access public
		 * @return void
		 */
		public function meta_box_content() {

			global $post_id;

			// setup view fields
			$fields = array(
				self::TOKEN . '_link_text' => __( 'Link Text', 'widget-builder' ),
				self::TOKEN . '_link_url' => __( 'Link URL', 'widget-builder' )
			);
			$nonce = wp_create_nonce( plugin_basename(__FILE__) );
			// get template hierarchy
			include( $this->get_template_hierarchy( 'metabox_link' ) );

		}

		/**
		 * meta_box_widget function.
		 *
		 * @access public
		 * @return void
		 */
		public function meta_box_widget() {

			global $post_id;

			// setup view fields
			$field = self::TOKEN . '_widget_description';

			// get template hierarchy
			include( $this->get_template_hierarchy( 'metabox_admin' ) );

		}

		/**
		 * meta_box_save function.
		 *
		 * @access public
		 * @param int $post_id
		 * @return void
		 */
		public function meta_box_save( $post_id ) {

			// Verify Autosave routine. Ignore action on Autosave
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			// Verify save source of save to prevent outside access
			if ( ( get_post_type() != self::POST_TYPE ) || ! wp_verify_nonce( $_POST[self::TOKEN . '_nonce'], plugin_basename(__FILE__) ) ) {
				return;
			}

			// Check user permissions
			if ( 'page' == $_POST['post_type'] ) {
				if ( !current_user_can( 'edit_page', $post_id ) ) {
					return;
				}
			} else {
				if ( !current_user_can( 'edit_post', $post_id ) ) {
					return;
				}
			}

			// Authenticated
			$fields = array( self::TOKEN . '_link_text', self::TOKEN . '_link_url', self::TOKEN . '_widget_description' );

			// Parse fields for add, update, delete
			foreach ( $fields as $f ) {

				${$f} = strip_tags(trim($_POST[$f]));

				if ( get_post_meta( $post_id, '_' . $f ) == '' ) {
					add_post_meta( $post_id, '_' . $f, ${$f}, true );
				} elseif( ${$f} != get_post_meta( $post_id, '_' . $f, true ) ) {
					update_post_meta( $post_id, '_' . $f, ${$f} );
				} elseif ( ${$f} == '' ) {
					delete_post_meta( $post_id, '_' . $f, get_post_meta( $post_id, '_' . $f, true ) );
				}
			}

		}

		/**
		 * Loads theme files in appropriate hierarchy: 1) child theme,
		 * 2) parent template, 3) plugin resources. will look in the tribe_widget_builder/
		 * directory in a theme and the views/ directory in the plugin
		 *
		 * @param string $template template file to search for
		 * @param string $class pass through class filters
		 * @return template path
		 * @author Modern Tribe, Inc. (Matt Wiebe)
		 **/

		function get_template_hierarchy($template, $class = null) {
			// whether or not .php was added
			$template = rtrim($template, '.php');

			if ( $theme_file = locate_template( array(self::TOKEN . '/' . $template) ) ) {
				$file = $theme_file;
			} else if ( $theme_file = locate_template(array(self::TOKEN . '/' . $template . '_' . $class)) ) {
				$file = $theme_file;
			} else {
				$file = $this->base_path . '/views/' . $template;
			}

			// ensure we have the proper extension
			$file = $file . '.php';

			return apply_filters( self::TOKEN . '_' . $template, $file, $class);
		}

		/**
		 * load_plugin_text_domain function.
		 *
		 * @access public
		 * @return void
		 */
		function load_plugin_text_domain() {
			load_plugin_textdomain( 'widget-builder', false, trailingslashit(basename(dirname(__FILE__))) . 'lang/');
		}

		/**
		 * Instance of this class for use as singleton
		 */
		private static $instance;

		/**
		 * Create the instance of the class
		 *
		 * @static
		 * @return void
		 */
		public static function init() {
			self::$instance = self::get_instance();
		}

		/**
		 * Get (and instantiate, if necessary) the instance of the class
		 *
		 * @static
		 * @return Tribe_Widget_Builder
		 */
		public static function get_instance() {
			if ( !is_a( self::$instance, __CLASS__ ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

	}

}