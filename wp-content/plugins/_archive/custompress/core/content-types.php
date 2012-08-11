<?php

/**
* CustomPress_Content_Types
*
* @uses CustomPress_Core
* @copyright Incsub 2007-2011 {@link http://incsub.com}
* @author Ivan Shaovchev (Incsub), Arnold Bailey (Incsub)
* @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
*/


if (!class_exists('CustomPress_Content_Types')):

//Allow shortcodes in Widgets
add_filter( 'widget_text', 'do_shortcode' );

class CustomPress_Content_Types extends CustomPress_Core {

	/** @var array Available Post Types */
	var $post_types = array();
	/** @var array Available Network Post Types */
	var $network_post_types = array();

	/** @var array Available Taxonomies */
	var $taxonomies = array();
	/** @var array Available Network Taxonomies */
	var $network_taxonomies = array();

	/** @var array Available Custom fields */
	var $custom_fields = array();
	/** @var array Available Network Custom fields */
	var $network_custom_fields = array();

	/** @var boolean Flag whether to flush the rewrite rules or not */
	var $flush_rewrite_rules = false;
	/** @var boolean Flag whether the users have the ability to declare post types for their own blogs */
	var $enable_subsite_content_types = false;
	/** @var bool  keep_network_content_type for site_options */
	var $network_content = true;

	/**
	* Constructor
	*
	* @return void
	*/
	function CustomPress_Content_Types() { __construct(); }

	function __construct(){
		parent::__construct();
		add_action( 'init', array( &$this, 'handle_post_type_requests' ), 0 );
		add_action( 'init', array( &$this, 'register_post_types' ), 2 );
		add_action( 'init', array( &$this, 'handle_taxonomy_requests' ), 0 );
		add_action( 'init', array( &$this, 'register_taxonomies' ), 1 );
		add_action( 'init', array( &$this, 'handle_custom_field_requests' ), 0 );

		//Add custom terms and fields on media page
		add_filter( 'attachment_fields_to_edit', array( &$this, 'add_custom_for_attachment' ), 111, 2 );
		add_filter( 'attachment_fields_to_save', array( &$this, 'save_custom_for_attachment' ), 111, 2 );

		add_action( 'add_meta_boxes', array( &$this, 'on_add_meta_boxes' ), 2 );
		add_action( 'admin_menu', array( &$this, 'create_custom_fields' ), 2 );
		add_action( 'save_post', array( &$this, 'save_custom_fields' ), 1, 1 );
		add_action( 'user_register', array( &$this, 'set_user_registration_rewrite_rules' ) );

		add_shortcode('ct', array($this,'ct_shortcode'));
		add_shortcode('tax', array($this,'tax_shortcode'));
		add_shortcode('custom_fields_block', array($this,'fields_shortcode'));

		add_filter('the_content', array($this,'run_custom_shortcodes'), 6 ); //Early priority so that other shortcodes can use custom values

		$this->init_vars();
	}

	/**
	* Initiate variables
	*
	* @return void
	*/
	function init_vars() {

		$this->display_network_content = get_site_option('display_network_content_types');

		$this->enable_subsite_content_types = apply_filters( 'enable_subsite_content_types', false );

		if ( is_network_admin() ) {
			$this->network_post_types    = get_site_option( 'ct_custom_post_types' );
			$this->network_taxonomies    = get_site_option( 'ct_custom_taxonomies' );
			$this->network_custom_fields = get_site_option( 'ct_custom_fields' );
		}

		if ( $this->enable_subsite_content_types == 1 ) {
			$this->post_types    = get_option( 'ct_custom_post_types' );
			$this->taxonomies    = get_option( 'ct_custom_taxonomies' );
			$this->custom_fields = get_option( 'ct_custom_fields' );
		} else {
			$this->post_types    = get_site_option( 'ct_custom_post_types' );
			$this->taxonomies    = get_site_option( 'ct_custom_taxonomies' );
			$this->custom_fields = get_site_option( 'ct_custom_fields' );
		}

	}

	/**
	* Intercept $_POST request and processes the custom post type submissions.
	*
	* @return void
	*/
	function handle_post_type_requests() {

		//$params is the $_POST variable with slashes stripped
		$params = array_map('stripslashes_deep',$_POST);

		// If add/update request is made
		if ( isset( $params['submit'] )
		&& isset( $params['_wpnonce'] )
		&& wp_verify_nonce( $params['_wpnonce'], 'submit_post_type' )
		) {
			// Validate input fields
			if ( $this->validate_field( 'post_type', strtolower( $params['post_type'] ) ) ) {
				// Post type labels
				$labels = array(
				'name'                  => $params['labels']['name'],
				'singular_name'         => $params['labels']['singular_name'],
				'add_new'               => $params['labels']['add_new'],
				'add_new_item'          => $params['labels']['add_new_item'],
				'edit_item'             => $params['labels']['edit_item'],
				'new_item'              => $params['labels']['new_item'],
				'view_item'             => $params['labels']['view_item'],
				'search_items'          => $params['labels']['search_items'],
				'not_found'             => $params['labels']['not_found'],
				'not_found_in_trash'    => $params['labels']['not_found_in_trash'],
				'parent_item_colon'     => $params['labels']['parent_item_colon'],
				'custom_fields_block'   => $params['labels']['custom_fields_block']
				);

				//choose regular taxonomies
				$supports_reg_tax = array (
				'category' => ( isset( $params['supports_reg_tax']['category'] ) ) ? '1' : '',
				'post_tag' => ( isset( $params['supports_reg_tax']['post_tag'] ) ) ? '1' : '',
				);

				// Post type args
				$args = array(
				'labels'              => $labels,
				'supports'            => $params['supports'],
				'supports_reg_tax'    => $supports_reg_tax,
				'capability_type'     => ( isset( $params['capability_type'] ) ) ? $params['capability_type'] : 'post',
				'map_meta_cap'        => (bool) $params['map_meta_cap'],
				'description'         => $params['description'],
				'menu_position'       => (int)  $params['menu_position'],
				'public'              => (bool) $params['public'] ,
				'show_ui'             => ( isset( $params['show_ui'] ) ) ? (bool) $params['show_ui'] : null,
				'show_in_nav_menus'   => ( isset( $params['show_in_nav_menus'] ) ) ? (bool) $params['show_in_nav_menus'] : null,
				'publicly_queryable'  => ( isset( $params['publicly_queryable'] ) ) ? (bool) $params['publicly_queryable'] : null,
				'exclude_from_search' => ( isset( $params['exclude_from_search'] ) ) ? (bool) $params['exclude_from_search'] : null,
				'hierarchical'        => (bool) $params['hierarchical'],
				'has_archive'		  => (bool) $params['has_archive'],
				'rewrite'             => (bool) $params['rewrite'],
				'query_var'           => (bool) $params['query_var'],
				'can_export'          => (bool) $params['can_export'],
				'cf_columns'          => $params['cf_columns']
				);

				// Remove empty labels so we can use the defaults
				foreach( $args['labels'] as $key => $value ) {
					if ( empty( $value ) )
					unset( $args['labels'][$key] );
				}

				// Set menu icon
				if ( !empty( $params['menu_icon'] ) )
				$args['menu_icon'] = $params['menu_icon'];

				// remove keys so we can use the defaults
				if ( $params['public'] == 'advanced' ) {
					unset( $args['public'] );
				} else {
					unset( $args['show_ui'] );
					unset( $args['show_in_nav_menus'] );
					unset( $args['publicly_queryable'] );
					unset( $args['exclude_from_search'] );
				}

				// Set slug for post type archive pages
				if ( !empty( $params['has_archive_slug'] ) )
				$args['has_archive'] = $params['has_archive_slug'];

				// Customize taxonomy rewrite
				if ( !empty( $params['rewrite'] ) ) {

					if ( !empty( $params['rewrite_slug'] ) )
					$args['rewrite'] = (array) $args['rewrite'] + array( 'slug' => $params['rewrite_slug'] );

					$args['rewrite'] = (array) $args['rewrite'] + array( 'with_front' => !empty( $params['rewrite_with_front'] ) ? true : false );
					$args['rewrite'] = (array) $args['rewrite'] + array( 'feeds' => !empty( $params['rewrite_feeds'] ) ? true : false );
					$args['rewrite'] = (array) $args['rewrite'] + array( 'pages' => !empty( $params['rewrite_pages'] ) ? true : false );

					// Remove boolean remaining from the type casting
					if ( is_array( $args['rewrite'] ) )
					unset( $args['rewrite'][0] );

					$this->flush_rewrite_rules = true;
				}

				// Set post type
				$post_type = ( $params['post_type'] ) ? strtolower( $params['post_type'] ) : $_GET['ct_edit_post_type'];

				// Set new post types
				$post_types = ( $this->post_types ) ? array_merge( $this->post_types, array( $post_type => $args ) ) : array( $post_type => $args );

				// Check whether we have a new post type and set flag which will later be used in register_post_types()
				if ( !is_array( $this->post_types ) || !array_key_exists( $post_type, $this->post_types ) )
				$this->flush_rewrite_rules = true;

				// Update options with the post type options
				if ( $this->enable_subsite_content_types == 1 && !is_network_admin() ) {
					update_option( 'ct_custom_post_types', $post_types );
				} else {
					update_site_option( 'ct_custom_post_types', $post_types );
					// Set flag for flush rewrite rules network-wide
					if ( $this->flush_rewrite_rules ) {
						update_site_option( 'ct_frr_id', uniqid('') );
					}
				}

				// Redirect to post types page
				wp_redirect( self_admin_url( 'admin.php?page=ct_content_types&ct_content_type=post_type&updated&frr=' . $this->flush_rewrite_rules ) );
			}
		}
		elseif ( isset( $params['submit'] )
		&& isset( $params['_wpnonce'] )
		&& wp_verify_nonce( $params['_wpnonce'], 'delete_post_type' )
		) {
			$post_types = $this->post_types;
			// remove the deleted post type
			unset( $post_types[$params['post_type_name']] );
			// update the available post types
			if ( $this->enable_subsite_content_types == 1 && !is_network_admin() )
			update_option( 'ct_custom_post_types', $post_types );
			else
			update_site_option( 'ct_custom_post_types', $post_types );
			// Redirect to post types page
			wp_redirect( self_admin_url( 'admin.php?page=ct_content_types&ct_content_type=post_type&updated' ));
		}
		elseif ( isset( $params['redirect_add_post_type'] ) ) {
			wp_redirect( self_admin_url( 'admin.php?page=ct_content_types&ct_content_type=post_type&ct_add_post_type=true' ) );
		}
	}

	/**
	* Get available custom post types and register them.
	* The function attach itself to the init hook and uses priority of 2. It loads
	* after the register_taxonomies() function which hooks itself to the init
	* hook with priority of 1 ( that's kinda important ) .
	*
	* @return void
	*/
	function register_post_types() {

		//if ( $this->display_network_content == 1 )
		{
			$post_types = get_site_option('ct_custom_post_types');
			// Register each post type if array of data is returned
			if ( is_array( $post_types ) ) {
				foreach ( $post_types as $post_type => $args )

				register_post_type( $post_type, $args );
			}
		}

		$post_types = $this->post_types;

		// Register each post type if array of data is returned
		if ( is_array( $post_types ) ) {
			foreach ( $post_types as $post_type => $args ) {

				//register post type
				register_post_type( $post_type, $args );
				//assign post type with regular taxanomies
				if ( isset( $args['supports_reg_tax'] ) ) {
					foreach ( $args['supports_reg_tax'] as $key => $value ) {
						if ( taxonomy_exists( $key ) && '1' == $value ) {
							register_taxonomy_for_object_type( $key, $post_type );
						}
					}
				}
			}
		}
		$this->flush_rewrite_rules();
	}

	/**
	* Intercepts $_POST request and processes the custom taxonomy requests
	*
	* @return void
	*/
	function handle_taxonomy_requests() {

		//$params is the $_POST variable with slashes stripped
		$params = array_map('stripslashes_deep',$_POST);

		// If valid add/edit taxonomy request is made
		if (   isset( $params['submit'] )
		&& isset( $params['_wpnonce'] )
		&& wp_verify_nonce( $params['_wpnonce'], 'submit_taxonomy' )
		) {
			// Validate input fields
			$valid_taxonomy = $this->validate_field( 'taxonomy', ( isset( $params['taxonomy'] ) ) ? strtolower( $params['taxonomy'] ) : null );
			$valid_object_type = $this->validate_field( 'object_type', ( isset( $params['object_type'] ) ) ? $params['object_type'] : null );

			if ( $valid_taxonomy && $valid_object_type ) {
				// Construct args
				$labels = array(
				'name'                       => $params['labels']['name'],
				'singular_name'              => $params['labels']['singular_name'],
				'add_new_item'               => $params['labels']['add_new_item'],
				'new_item_name'              => $params['labels']['new_item_name'],
				'edit_item'                  => $params['labels']['edit_item'],
				'update_item'                => $params['labels']['update_item'],
				'search_items'               => $params['labels']['search_items'],
				'popular_items'              => $params['labels']['popular_items'],
				'all_items'                  => $params['labels']['all_items'],
				'parent_item'                => $params['labels']['parent_item'],
				'parent_item_colon'          => $params['labels']['parent_item_colon'],
				'add_or_remove_items'        => $params['labels']['add_or_remove_items'],
				'separate_items_with_commas' => $params['labels']['separate_items_with_commas'],
				'choose_from_most_used'      => $params['labels']['all_items']
				);

				$args = array(
				'labels'              => $labels,
				'public'              => (bool) $params['public'] ,
				'show_ui'             => ( isset( $params['show_ui'] ) ) ? (bool) $params['show_ui'] : null,
				'show_tagcloud'       => ( isset( $params['show_tagcloud'] ) ) ? (bool) $params['show_tagcloud'] : null,
				'show_in_nav_menus'   => ( isset( $params['show_in_nav_menus'] ) ) ? (bool) $params['show_in_nav_menus'] : null,
				'hierarchical'        => (bool) $params['hierarchical'],
				'rewrite'             => (bool) $params['rewrite'],
				'query_var'           => (bool) $params['query_var']
				//  'capabilities'        => array () /** TODO implement advanced capabilities */
				);

				// Remove empty values from labels so we can use the defaults
				foreach( $args['labels'] as $key => $value ) {
					if ( empty( $value ))
					unset( $args['labels'][$key] );
				}

				// If no advanced is set, unset values so we can use the defaults
				if ( $params['public'] == 'advanced' ) {
					unset( $args['public'] );
				} else {
					unset( $args['show_ui'] );
					unset( $args['show_tagcloud'] );
					unset( $args['show_in_nav_menus'] );
				}

				// Customize taxonomy rewrite
				if ( !empty( $params['rewrite'] ) ) {

					if ( !empty( $params['rewrite_slug'] ) )
					$args['rewrite'] = (array) $args['rewrite'] + array( 'slug' => $params['rewrite_slug'] );

					$args['rewrite'] = (array) $args['rewrite'] + array( 'with_front' => !empty( $params['rewrite_with_front'] ) ? true : false );
					$args['rewrite'] = (array) $args['rewrite'] + array( 'hierarchical' => !empty( $params['rewrite_hierarchical'] ) ? true : false );

					// Remove boolean remaining from the type casting
					if ( is_array( $args['rewrite'] ) )
					unset( $args['rewrite'][0] );

					$this->flush_rewrite_rules = true;
				}

				// Set the assiciated object types ( post types )
				$object_type = $params['object_type'];

				// Set the taxonomy which we are adding/updating
				$taxonomy = ( isset( $params['taxonomy'] )) ? strtolower( $params['taxonomy'] ) : $_GET['ct_edit_taxonomy'];

				// Set new taxonomies
				$taxonomies = ( $this->taxonomies )
				? array_merge( $this->taxonomies, array( $taxonomy => array( 'object_type' => $object_type, 'args' => $args ) ) )
				: array( $taxonomy => array( 'object_type' => $object_type, 'args' => $args ) );

				// Check whether we have a new post type and set flush rewrite rules
				if ( !is_array( $this->taxonomies ) || !array_key_exists( $taxonomy, $this->taxonomies ) )
				$this->flush_rewrite_rules = true;

				// Update wp_options with the taxonomies options
				if ( $this->enable_subsite_content_types == 1 && !is_network_admin() ) {
					update_option( 'ct_custom_taxonomies', $taxonomies );
				} else {
					update_site_option( 'ct_custom_taxonomies', $taxonomies );

					// Set flag for flush rewrite rules network-wide
					if ( $this->flush_rewrite_rules == true ) {
						update_site_option( 'ct_frr_id', uniqid('') );
					}
				}

				// Redirect back to the taxonomies page
				wp_redirect( self_admin_url( 'admin.php?page=ct_content_types&ct_content_type=taxonomy&updated&frr' . $this->flush_rewrite_rules ) );
			}
		}
		elseif ( isset( $params['submit'] )
		&& isset( $params['_wpnonce'] )
		&& wp_verify_nonce( $params['_wpnonce'], 'delete_taxonomy' )
		) {
			// Set available taxonomies
			$taxonomies = $this->taxonomies;

			// Remove the deleted taxonomy
			unset( $taxonomies[$params['taxonomy_name']] );

			// Update the available taxonomies
			if ( $this->enable_subsite_content_types == 1 && !is_network_admin() )
			update_option( 'ct_custom_taxonomies', $taxonomies );
			else
			update_site_option( 'ct_custom_taxonomies', $taxonomies );

			// Redirect back to the taxonomies page
			wp_redirect( self_admin_url( 'admin.php?page=ct_content_types&ct_content_type=taxonomy&updated' ) );
		}
		elseif ( isset( $params['redirect_add_taxonomy'] ) ) {
			wp_redirect( self_admin_url( 'admin.php?page=ct_content_types&ct_content_type=taxonomy&ct_add_taxonomy=true' ));
		}
	}

	/**
	* Get available custom taxonomies and register them.
	* The function attaches itself to the init hook and uses priority of 1. It loads
	* before the ct_admin_register_post_types() func which hook itself to the init
	* hook with priority of 2 ( that's kinda important )
	*
	* @uses apply_filters() You can use the 'sort_custom_taxonomies' filter hook to sort your taxonomies
	* @return void
	*/
	function register_taxonomies() {

		//if ( $this->display_network_content == 1 )
		{
			$taxonomies = get_site_option('ct_custom_taxonomies');
			// If custom taxonomies are present, register them
			if ( is_array( $taxonomies ) ) {
				// Register taxonomies
				foreach ( $taxonomies as $taxonomy => $args )
				register_taxonomy( $taxonomy, $args['object_type'], $args['args'] );
			}
		}

		$taxonomies = $this->taxonomies;
		// Plugins can filter this value and sort taxonomies
		$sort = null;
		$sort = apply_filters( 'sort_custom_taxonomies', $sort );
		// If custom taxonomies are present, register them
		if ( is_array( $taxonomies ) ) {
			// Sort taxonomies
			if ( $sort == 'alphabetical' )
			ksort( $taxonomies );
			// Register taxonomies
			foreach ( $taxonomies as $taxonomy => $args )
			register_taxonomy( $taxonomy, $args['object_type'], $args['args'] );
		}
	}

	/**
	* Intercepts $_POST request and processes the custom fields submissions
	*/
	function handle_custom_field_requests() {

		//$params is the $_POST variable with slashes stripped
		$params = array_map('stripslashes_deep',$_POST);

		// If valid add/edit custom field request is made
		if ( isset( $params['submit'] )
		&& isset( $params['_wpnonce'] )
		&& wp_verify_nonce( $params['_wpnonce'], 'submit_custom_field' )
		) {

			// Validate input fields data
			$field_title_valid       = $this->validate_field( 'field_title', ( isset( $params['field_title'] ) ) ? $params['field_title'] : null );
			$field_object_type_valid = $this->validate_field( 'object_type', ( isset( $params['object_type'] ) ) ? $params['object_type'] : null );

			// Check for specific field types and validate differently
			if ( in_array( $params['field_type'], array( 'radio', 'checkbox', 'selectbox', 'multiselectbox' ) ) ) {

				$field_options_valid = $this->validate_field( 'field_options', $params['field_options'] ); //2 because an empty first choice is common
				// Check whether fields pass the validation, if not stop execution and return
				if ( $field_title_valid == false || $field_object_type_valid == false || $field_options_valid == false )
				return;
			} else {
				// Check whether fields pass the validation, if not stop execution and return
				if ( $field_title_valid == false || $field_object_type_valid == false )
				return;
			}

			if ( 2 == $params['field_wp_allow'] && empty( $_GET['ct_edit_custom_field'] ) ) {
				$field_title = str_replace( ' ', '_', $params['field_title'] );
				$field_title = substr( preg_replace ( '/\W/', '', $field_title ), 0, 10);
				$field_id = $field_title . '_' . $params['field_type'] . '_' . substr( uniqid(''), 7, 4) ;
			} else {
				$field_id = ( empty( $_GET['ct_edit_custom_field'] ) ) ? $params['field_type'] . '_' . uniqid('') : $_GET['ct_edit_custom_field'];
			}

			$args = array(
			'field_title'          => $params['field_title'],
			'field_wp_allow'       => ( 2 == $params['field_wp_allow'] ) ? 1 : 0,
			'field_type'           => $params['field_type'],
			'field_sort_order'     => $params['field_sort_order'],
			'field_options'        => $params['field_options'],
			'field_date_format'    => $params['field_date_format'],
			'field_regex'          => trim($params['field_regex']),
			'field_regex_options'  => trim($params['field_regex_options']),
			'field_regex_message'  => trim($params['field_regex_message']),
			'field_message'        => trim($params['field_message']),
			'field_default_option' => ( isset( $params['field_default_option'] ) ) ? $params['field_default_option'] : NULL,
			'field_description'    => $params['field_description'],
			'object_type'          => $params['object_type'],
			'field_required'       => (2 == $params['field_required'] ) ? 1 : 0,
			'field_id'             => $field_id,
			);


			// Unset if there are no options to be stored in the db
			if ( $args['field_type'] == 'text' || $args['field_type'] == 'textarea'){
				unset( $args['field_options'] );
			} else {
				//regex on text only
				unset( $args['field_regex'] );
				unset( $args['field_regex_message'] );
				unset( $args['field_regex_options'] );
			}

			// Set new custom fields
			$custom_fields = ( $this->custom_fields )
			? array_merge( $this->custom_fields, array( $field_id => $args ) )
			: array( $field_id => $args );

			//Set the field_order of the fields to the current default order
			$i = 0;
			foreach($custom_fields as &$custom_field){
				$custom_field['field_order'] = $i++;
			}

			if ( $this->enable_subsite_content_types == 1 && !is_network_admin() )
			update_option( 'ct_custom_fields', $custom_fields );
			else
			update_site_option( 'ct_custom_fields', $custom_fields );

			wp_redirect( self_admin_url( 'admin.php?page=ct_content_types&ct_content_type=custom_field&updated' ) );
		}
		elseif ( ( isset( $params['submit'] ) || isset( $params['delete_cf_values'] ) )
		&& isset( $params['_wpnonce'] )
		&& wp_verify_nonce( $params['_wpnonce'], 'delete_custom_field' )
		) {
			// Set available custom fields
			$custom_fields = $this->custom_fields;

			// Remove all values of custom field
			if ( isset( $params['delete_cf_values'] ) ) {

				if ( isset( $custom_fields[$params['custom_field_id']]['field_wp_allow'] ) && 1 == $custom_fields[$params['custom_field_id']]['field_wp_allow'] )
				$prefix = 'ct_';
				else
				$prefix = '_ct_';

				global $wpdb;
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE meta_key = '%s'", $prefix . $params['custom_field_id'] ) );
			}

			//update custom fields colums for custom post type
			$cf_columns_update = 0;
			foreach ( $custom_fields[$params['custom_field_id']]['object_type'] as $object_type ) {
				if ( is_array( $this->post_types[$object_type]['cf_columns'] ) )
				foreach ( $this->post_types[$object_type]['cf_columns'] as $key => $value )
				if ( $params['custom_field_id'] == $key ) {
					unset( $this->post_types[$object_type]['cf_columns'][$key] );
					$cf_columns_update = 1;
				}
			}
			if ( 1 == $cf_columns_update ) {
				// Update options with the post type options
				if ( $this->enable_subsite_content_types == 1 && !is_network_admin() )
				update_option( 'ct_custom_post_types', $this->post_types );
				else
				update_site_option( 'ct_custom_post_types', $this->post_types );
			}


			// Remove the deleted custom field
			unset( $custom_fields[$params['custom_field_id']] );

			//Set the field_order of the fields to the current default order
			$i = 0;
			foreach($custom_fields as &$custom_field){
				$custom_field['field_order'] = $i++;
			}

			// Update the available custom fields
			if ( $this->enable_subsite_content_types == 1 && !is_network_admin() )
			update_option( 'ct_custom_fields', $custom_fields );
			else
			update_site_option( 'ct_custom_fields', $custom_fields );

			// Redirect back to the taxonomies page
			wp_redirect( self_admin_url( 'admin.php?page=ct_content_types&ct_content_type=custom_field&updated' ) );
		}
		elseif ( isset( $_GET['ct_reorder_custom_field'] )
		&& isset( $_GET['_wpnonce'] )
		&& wp_verify_nonce( $_GET['_wpnonce'], 'reorder_custom_fields' )
		) {

			// Reorder the fields
			$dir = $_GET['direction'];
			$fid = $_GET['ct_reorder_custom_field'];
			$custom_fields = $this->custom_fields;

			//Set the field_order of the fields to the current default order
			$i = 0;
			foreach($custom_fields as &$custom_field){
				$custom_field['field_order'] = $i++;
			}

			$keys = array_keys($custom_fields);
			$key = array_search($fid, $keys);

			if($key !== false) {
				$swapped = false;
				if($dir == 'up' && $key > 0) {
					$ndx = $custom_fields[$keys[$key]]['field_order'];
					$custom_fields[$keys[$key]]['field_order']=$custom_fields[$keys[$key-1]]['field_order'];
					$custom_fields[$keys[$key-1]]['field_order'] = $ndx;
					$swapped = true;
				}
				if($dir == 'down' && array_key_exists($key+1, $keys)) {
					$ndx = $custom_fields[$keys[$key]]['field_order'];
					$custom_fields[$keys[$key]]['field_order']=$custom_fields[$keys[$key+1]]['field_order'];
					$custom_fields[$keys[$key+1]]['field_order'] = $ndx;
					$swapped = true;
				}

				if($swapped){

					if(!function_exists('ct_cmp')){
						function ct_cmp($a, $b){
							if ($a['field_order'] == $b['field_order']) return 0;
							return ($a['field_order'] < $b['field_order']) ? -1 : 1;
						}

						if (uasort($custom_fields, 'ct_cmp')){
							// Update the available custom fields
							if ( $this->enable_subsite_content_types == 1 && !is_network_admin() )
							update_option( 'ct_custom_fields', $custom_fields );
							else
							update_site_option( 'ct_custom_fields', $custom_fields );
						}
					}

				}
			}
			// Redirect back to the taxonomies page
			wp_redirect( self_admin_url( 'admin.php?page=ct_content_types&ct_content_type=custom_field&updated' ) );


		}
		elseif ( isset( $params['redirect_add_custom_field'] ) ) {
			wp_redirect( self_admin_url( 'admin.php?page=ct_content_types&ct_content_type=custom_field&ct_add_custom_field=true' ));
		}
	}

	/**
	* Create the custom fields
	*
	* @return void
	*/
	function create_custom_fields() {

		global $submenu;
		//Add admin submenu in media tab for taxanomy of attachment post type
		$taxonomies = $this->taxonomies;
		//If custom taxonomies are present
		if ( is_array( $taxonomies ) ) {
			// Sort taxonomies
			ksort( $taxonomies );
			foreach ( $taxonomies as $taxonomy => $args ) {
				if ( in_array ( 'attachment', $args['object_type'] ) ) {
					$name = ( isset( $args['args']['labels']['name'] ) ) ? $args['args']['labels']['name'] : $taxonomy;
					$submenu['upload.php'][] = array( $name, 'upload_files', 'edit-tags.php?taxonomy=' . $taxonomy );
				}
			}
		}
	}

	/**
	* Add Custom field edit metaboxes
	*
	* @return void
	*/
	function on_add_meta_boxes() {

		$current_post_type = $this->get_current_post_type();

		$net_custom_fields = get_site_option('ct_custom_fields');

		if ( $this->display_network_content && !empty($net_custom_fields)) {
			//get the network fields
			$net_post_types = get_site_option('ct_custom_post_types');
			$meta_box_label = __('Default CustomPress Fields', $this->text_domain);

			//If we have this post type rename the metabox
			if($current_post_type) {
				if ( ! empty( $net_post_types[$current_post_type]['labels']['custom_fields_block'] ) )
				$meta_box_label = $net_post_types[$current_post_type]['labels']['custom_fields_block'];

			}
			//Do we even need the metabox
			$has_fields = false;
			foreach ( $net_custom_fields as $custom_field ) {
				$has_fields = (is_array($custom_field['object_type']) ) ? in_array($current_post_type, $custom_field['object_type']) : false;
				if ($has_fields){
					add_meta_box( 'ct-network-custom-fields', $meta_box_label, array( &$this, 'display_custom_fields_network' ), $current_post_type, 'normal', 'high' );
					break;
				}
			}
		}

		$custom_fields = $this->custom_fields;

		if ( ! empty($custom_fields)) {
			//get the local fields
			$meta_box_label = __('CustomPress Fields', $this->text_domain);

			//If we have this post type rename the metabox
			if($current_post_type) {
				if ( ! empty( $this->post_types[$current_post_type]['labels']['custom_fields_block'] ) )
				$meta_box_label = $this->post_types[$current_post_type]['labels']['custom_fields_block'];
			}
			//Do we even need the metabox
			$has_fields = false;
			foreach ( $custom_fields as $custom_field ) {
				$has_fields = (is_array($custom_field['object_type']) ) ? in_array($current_post_type, $custom_field['object_type']) : false;
				if ($has_fields){
					add_meta_box( 'ct-custom-fields', $meta_box_label, array( &$this, 'display_custom_fields' ), $current_post_type, 'normal', 'high' );
					break;
				}
			}
		}
	}

	/**
	* Display custom fields template on add custom post pages
	*
	* @return void
	*/
	function display_custom_fields() {
		$this->render_admin('display-custom-fields', array( 'type' => 'local' ) );
	}

	/**
	* Display custom fields template on add custom post pages
	*
	* @return void
	*/
	function display_custom_fields_network() {
		$this->render_admin('display-custom-fields', array( 'type' => 'network' ) );
	}

	/**
	* Save custom fields data
	*
	* @param int $post_id The post id of the post being edited
	*/
	function save_custom_fields( $post_id ) {
		// Prevent autosave from deleting the custom fields
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE
		|| defined('DOING_AJAX') && DOING_AJAX
		|| defined('DOING_CRON') && DOING_CRON
		|| isset($_REQUEST['bulk_edit'])
		)
		return;

		$custom_fields = $this->custom_fields;

		if ( !empty( $custom_fields )) {
			foreach ( $custom_fields as $custom_field ) {
				if ( isset( $custom_field['field_wp_allow'] ) && 1 == $custom_field['field_wp_allow'] )
				$prefix = 'ct_';
				else
				$prefix = '_ct_';

				if ( isset( $_POST[$prefix . $custom_field['field_id']] ))
				update_post_meta( $post_id, $prefix . $custom_field['field_id'], $_POST[$prefix . $custom_field['field_id']] );
				//for non checked checkbox set value -1
				elseif ( isset($_POST["post_type"]) && in_array( $_POST["post_type"], $custom_field["object_type"] ) && 'checkbox' == $custom_field['field_type'] )
				update_post_meta( $post_id, $prefix . $custom_field['field_id'], -1 );
				else
				delete_post_meta( $post_id, $prefix . $custom_field['field_id'] );
			}
		}
	}

	/**
	* Makes sure the admin always has all rights to all custom post types.
	*
	* @return void
	*/
	function add_admin_capabilities(){
		global $wp_roles;

		//if ( $this->display_network_content == 1 )
		{
			$post_types = $this->network_post_types;
			if(is_array($post_types)){
				foreach($post_types as $key => $pt){
					$post_type = get_post_type_object($key);
					foreach($post_type->cap as $capability){
						$wp_roles->add_cap('administrator', $capability);
					}
				}
			}
		}

		$post_types = $this->post_types;
		if(is_array($post_types)){
			foreach($post_types as $key => $pt){
				$post_type = get_post_type_object($key);
				foreach($post_type->cap as $cap){
					$wp_roles->add_cap('administrator', $cap);
				}
			}
		}
	}
	/**
	* Flush rewrite rules based on boolean check
	*
	* @return void
	*/
	function flush_rewrite_rules() {
		// Mechanisum for detecting changes in sub-site content types for flushing rewrite rules
		if ( is_multisite() && !is_main_site() && $this->enable_subsite_content_types == 0 ) {
			$global_frr_id = get_site_option('ct_frr_id');
			$local_frr_id = get_option('ct_frr_id');
			if ( $global_frr_id != $local_frr_id ) {
				$this->flush_rewrite_rules = true;
				update_option('ct_frr_id', $global_frr_id );
				$this->add_admin_capabilities();
			}
		}

		// flush rewrite rules
		if ( $this->flush_rewrite_rules || !empty( $_GET['frr'] ) ) {
			flush_rewrite_rules(false);
			$this->flush_rewrite_rules = false;
			$this->add_admin_capabilities();
		}
	}

	/**
	* Check whether new users are registered, since we need to flush the rewrite
	* rules for them.
	*
	* @return void
	*/
	function set_user_registration_rewrite_rules() {
		$this->flush_rewrite_rules = true;
	}

	/**
	* Validates input fields data
	*
	* @param string $field
	* @param mixed $value
	* @return bool true/false depending on validation outcome
	*/
	function validate_field( $field, $value ) {
		// Validate set of common fields
		if ( $field == 'taxonomy' || $field == 'post_type' ) {
			// Regular expression. Checks for alphabetic characters and underscores only
			if ( preg_match( '/^[a-zA-Z0-9_]{2,}$/', $value ) ) {
				return true;
			} else {
				if ( $field == 'post_type' )
				add_action( 'ct_invalid_field_post_type', create_function( '', 'echo "form-invalid";' ) );
				if ( $field == 'taxonomy' )
				add_action( 'ct_invalid_field_taxonomy', create_function( '', 'echo "form-invalid";' ) );
				return false;
			}
		}

		// Validate set of common fields
		if ( $field == 'object_type' || $field == 'field_title' || $field == 'field_options' ) {

			if (is_array($value)){

				$invalid = true;

				foreach($value as $item){
					$invalid = (!empty($item)) ? $false : $invalid;
				}

			} else {
				$invalid = empty($value);
			}

			if ( $invalid ) {
				if ( $field == 'object_type' )
				add_action( 'ct_invalid_field_object_type', create_function( '', 'echo "form-invalid";' ) );
				if ( $field == 'field_title' )
				add_action( 'ct_invalid_field_title', create_function( '', 'echo "form-invalid";' ) );
				if ( $field == 'field_options' )
				add_action( 'ct_invalid_field_options', create_function( '', 'echo "form-invalid";' ) );
				return false;
			} else {
				return true;
			}
		}
	}


	//Add terms and custome fields on media page
	function add_custom_for_attachment( $form_fields, $post ) {
		if ( $form_fields ) {

			$script = '';

			//add hierarchical terms as checkbox
			foreach ( $form_fields as $taxonomy => $taxonomy_value ) {
				if ( isset( $taxonomy_value['hierarchical'] ) && 1 == $taxonomy_value['hierarchical'] ) {
					//get all terms of taxonomy
					$terms = get_terms( $taxonomy, array( 'get' => 'all' ) );

					$taxonomy_tree  = array();
					$children       = array();
					$term_ids       = array();

					//create hierarchical tree
					foreach( $terms as $term ) {
						$term_ids[$term->term_id] = $term;
						if ( 0 == $term->parent )
						$taxonomy_tree[$term->term_id] = array();
						else
						$children[$term->parent][$term->term_id] = array();

					}

					if ( count( $children ) ) {
						foreach( $children as $base => $child )
						foreach( $children as $child_key => $val )
						if ( array_key_exists( $base, $val ) ) {
							$children[$child_key][$base] = &$children[$base];
							break;
						}

						foreach ( $children as $base => $child )
						if ( isset( $taxonomy_tree[$base] ) )
						$taxonomy_tree[$base] = $child;
					}

					//gen checkbox of tree
					$checkbox = $this->media_gen_hierarchical_field( $post->ID, $taxonomy, $term_ids, $taxonomy_tree );
					if ( $terms ) {
						$form_fields[ 'temp_' . $taxonomy]['input']    = 'checkbox';
						$form_fields[ 'temp_' . $taxonomy]['label']    = ( isset( $taxonomy_value['labels']->name ) && '' != $taxonomy_value['labels']->name ) ? $taxonomy_value['labels']->name : ucwords( $taxonomy );
						$form_fields[ 'temp_' . $taxonomy]['checkbox'] = $checkbox;

						/* Save hierarchical terms:
						only with JavaScript - for change Array value to String
						because terms velue can't be array - error of WP \wp-admin\includes\media.php - line 479 - wp_set_object_terms....
						*/
						// gen JavaScript
						$script .= '
						var ' . $taxonomy . ' = "";

						if ( jQuery("input:checkbox[name=\'my_terms_[' . $post->ID . '][' . $taxonomy . '][]\']").length ) {
						jQuery("input:checkbox[name=\'my_terms_[' . $post->ID . '][' . $taxonomy . '][]\']:checked").each(function(){
						' . $taxonomy . ' = ' . $taxonomy . ' + "," + this.value;
						})

						if ( jQuery("input:hidden[name=\'attachments[' . $post->ID . '][' . $taxonomy . ']\']").length ) {
						jQuery("input:hidden[name=\'attachments[' . $post->ID . '][' . $taxonomy . ']\']").val( ' . $taxonomy . ' );
						}
						}
						';

						$form_fields[$taxonomy]['input'] = 'hidden';
						$form_fields[$taxonomy]['value'] = '';
						$add_script = 1;

					} else {
						$form_fields[$taxonomy]['input'] = 'html';
						$form_fields[$taxonomy]['html']  = __( 'No values', $this->text_domain );
					}
				}
			}

			//add JavaScript to meadia page for save terms
			if ( isset( $add_script ) ) {
				$form_fields['script_for_terms']['input'] = 'html';
				$form_fields['script_for_terms']['label'] = '';
				$form_fields['script_for_terms']['html']  = '
				<script type="text/javascript">
				jQuery( document ).ready( function() {
				jQuery("#media-single-form").submit(function(){
				' . $script . '
				return true;
				});
				});
				</script>
				';
			}


			//Add custom fields to Media page
			if ( is_array( $this->custom_fields ) )
			foreach ( $this->custom_fields as $custom_field ) {

				if ( in_array ( 'attachment', $custom_field['object_type'] ) ) {

					$html = '';

					switch ( $custom_field['field_type'] ) {
						case 'text';
						case 'textarea':
						case 'datepicker':
						$form_fields[$custom_field['field_id']]['label'] = $custom_field['field_title'];
						$form_fields[$custom_field['field_id']]['input'] = $custom_field['field_type'];
						$form_fields[$custom_field['field_id']]['value'] = get_post_meta( $post->ID, $custom_field['field_id'], true );
						$form_fields[$custom_field['field_id']]['helps'] = $custom_field['field_description'];
						break;

						case 'datepicker':
						$form_fields[$custom_field['field_id']]['label'] = $custom_field['field_title'];
						$form_fields[$custom_field['field_id']]['input'] = $custom_field['field_type'];
						$form_fields[$custom_field['field_id']]['value'] = get_post_meta( $post->ID, $custom_field['field_id'], true );
						$form_fields[$custom_field['field_id']]['helps'] = $custom_field['field_description'];

						break;

						case 'radio':

						$values = get_post_meta( $post->ID, $custom_field['field_id'], true );

						if ( empty( $values ) )
						$values = (array) $custom_field['field_default_option'];
						elseif ( ! is_array( $values ) )
						$values = (array) $values;


						foreach ( $custom_field['field_options'] as $key => $value ) {
							$input_name = 'attachments['. $post->ID .'][' . $custom_field['field_id'] . ']';
							$input_id   = 'attachments_'. $post->ID .'_' . $custom_field['field_id'] . '_' . $key;

							if ( in_array( $value, $values ) )
							$html .= '<label>
							<input type="radio" name="'. $input_name .'" id="' . $input_id . '" value="' . $value . '" checked />
							' . $value . '
							</label><br />';
							else
							$html .= '<label>
							<input type="radio" name="'. $input_name .'" id="' . $input_id . '" value="' . $value . '" />
							' . $value . '
							</label><br />';
						}

						$form_fields[$custom_field['field_id']]['label'] = $custom_field['field_title'];
						$form_fields[$custom_field['field_id']]['input'] = 'html';
						$form_fields[$custom_field['field_id']]['html']  = $html;
						$form_fields[$custom_field['field_id']]['helps'] = $custom_field['field_description'];
						break;

						case 'checkbox':

						$values = get_post_meta( $post->ID, $custom_field['field_id'], true );

						if ( empty( $values ) )
						$values = (array) $custom_field['field_default_option'];
						elseif ( ! is_array( $values ) )
						$values = (array) $values;

						foreach ( $custom_field['field_options'] as $key => $value ) {
							$input_name = 'attachments['. $post->ID .'][' . $custom_field['field_id'] . '][' . $key . ']';
							$input_id   = 'attachments_'. $post->ID .'_' . $custom_field['field_id'] . '_' . $key;

							if ( in_array( $value, $values ) )
							$html .= '<label>
							<input type="checkbox" name="'. $input_name .'" id="' . $input_id . '" value="' . $value . '" checked />
							' . $value . '
							</label><br />';
							else
							$html .= '<label>
							<input type="checkbox" name="'. $input_name .'" id="' . $input_id . '" value="' . $value . '" />
							' . $value . '
							</label><br />';
						}

						$form_fields[$custom_field['field_id']]['label'] = $custom_field['field_title'];
						$form_fields[$custom_field['field_id']]['input'] = 'html';
						$form_fields[$custom_field['field_id']]['html']  = $html;
						$form_fields[$custom_field['field_id']]['value'] = get_post_meta( $post->ID, $custom_field['field_id'], true );
						$form_fields[$custom_field['field_id']]['helps'] = $custom_field['field_description'];
						$form_fields[$custom_field['field_id']]['date_format'] = $custom_field['field_date_format'];
						break;

						case 'selectbox';
						case 'multiselectbox':
						if ( 'multiselectbox' == $custom_field['field_type'] )
						$multiple = 'multiple style="height: 130px;"';
						else
						$multiple = '';

						$values = get_post_meta( $post->ID, $custom_field['field_id'], true );

						if ( empty( $values ) )
						$values = (array) $custom_field['field_default_option'];
						elseif ( ! is_array( $values ) )
						$values = (array) $values;


						foreach ( $custom_field['field_options'] as $key => $value ) {
							if ( in_array( $value, $values ) )
							$html .= '<option value="' . $value . '" selected >' . $value . '&nbsp;</option>';
							else
							$html .= '<option value="' . $value . '">' . $value . '&nbsp;</option>';
						}

						$html = '
						<select ' . $multiple . ' name="attachments['. $post->ID .'][' . $custom_field['field_id'] . '][]" id="attachments['. $post->ID .'][' . $custom_field['field_id'] . ']">
						' . $html . '
						</select>
						';

						$form_fields[$custom_field['field_id']]['label'] = $custom_field['field_title'];
						$form_fields[$custom_field['field_id']]['input'] = 'html';
						$form_fields[$custom_field['field_id']]['html']  = $html;
						$form_fields[$custom_field['field_id']]['helps'] = $custom_field['field_description'];
						break;
					}
				}
			}

		}
		return $form_fields;
	}

	//generate html for hierarchical fields on media page
	function media_gen_hierarchical_field( $post_id, $taxonomy, $term_ids, $taxonomy_tree, $checkbox = '', &$num = 0 ) {
		foreach ( $taxonomy_tree as $term_id => $tree ) {
			$type       = 'checkbox';
			$checked    = is_object_in_term( $post_id, $taxonomy, $term_id ) ? ' checked="checked"' : '';
			$checkbox  .= str_repeat( '&ndash;&ndash;', count( get_ancestors( $term_id, $taxonomy ) ) );
			$checkbox  .= ' <input type="' . $type . '" id="my_terms_' . $post_id . '_' . $taxonomy . '_' . $num . '" name="my_terms_[' . $post_id . '][' . $taxonomy . '][]" value="' . $term_ids[$term_id]->name . '"' . $checked . ' /><label for="attachments_' . $post_id . '_' . $taxonomy . '_' . $num . '">' . esc_html( $term_ids[$term_id]->name ) . "</label><br />\n";
			$num++;
			if ( count( $tree ) )
			$checkbox = $this->media_gen_hierarchical_field( $post_id, $taxonomy, $term_ids, $tree, $checkbox, $num );
		}
		return $checkbox;
	}

	//Save custom feilds value from media page
	function save_custom_for_attachment( $post, $attachment ) {

		//Save custom fields for Attachment post type
		if ( is_array( $this->custom_fields ) )
		foreach ( $this->custom_fields as $custom_field ) {
			if ( in_array ( 'attachment', $custom_field['object_type'] ) ) {
				if ( isset( $attachment[$custom_field['field_id']] ) ) {
					// update_post_meta
					update_post_meta( $post['ID'], $custom_field['field_id'], $attachment[$custom_field['field_id']] );
				} elseif ( 'checkbox' == $custom_field['field_type'] ) {
					update_post_meta( $post['ID'], $custom_field['field_id'], '-1' );
				}
			}
		}

		return $post;
	}

	/**
	* Creates shortcodes for fields which may be used for shortened embed codes.
	*
	* @return string
	* @uses appy_filters()
	*/
	function ct_shortcode($atts, $content=null){
		global $post;

		extract( shortcode_atts( array(
		'id' => '',
		'property' => 'value',
		), $atts ) );

		// Take off the prefix for indexing the array;
		$cid = str_replace('_ct_','',$id);
		$cid = str_replace('ct_','',$cid);

		$custom_field = (isset($this->custom_fields[$cid])) ? $this->custom_fields[$cid] : null;

		$property = strtolower($property);
		$result = '';

		switch ($property){
			case 'title': $result = $custom_field['field_title']; break;
			case 'description': $result = $custom_field['field_description']; break;
			case 'value':
			default: {
				switch ($custom_field['field_type']){
					case 'checkbox':
					case 'multiselectbox': {
						if ( get_post_meta( $post->ID, $id, true ) ) {
							foreach ( get_post_meta( $post->ID, $id, true ) as $value ) {
								$result .= (empty($result)) ? $value : ', ' . $value;
							}
						}
						break;
					}
					case 'selectbox':
					case 'radio': {
						if ( get_post_meta( $post->ID, $id, false ) ) {
							foreach ( get_post_meta( $post->ID, $id, false ) as $value ) {
								$result .= (empty($result)) ? $value : ', ' . $value;
							}
						}
						break;
					}
					default: {
						$result = get_post_meta( $post->ID, $id, true ); break;
					}
				}
			}
		}
		$result = apply_filters('ct_shortcode', $result, $atts, $content);
		return $result;
	}

	/**
	* Creates shortcodes for fields which may be used for shortened embed codes.
	*
	* @string
	* @uses appy_filters()
	*/
	function tax_shortcode($atts, $content = null){
		global $post;

		extract( shortcode_atts( array(
		'id' => '',
		'before' => '',
		'separator' => ', ',
		'after' => '',
		), $atts ) );

		$result = get_the_term_list( $post->ID, $id, $before, $separator, $after );

		$result = (is_wp_error($result)) ? __('Invalid Taxonomy name in [tax ] shortcode', $this->text_domain) : $result;

		$result = apply_filters('tax_shortcode', $result, $atts, $content);
		return $result;
	}

	/**
	* Creates shortcodes for fields which may be used for shortened embed codes.
	*
	* @string
	* @uses appy_filters()
	*/
	function fields_shortcode($atts, $content = null){
		global $post;

		extract( shortcode_atts( array(
		'wrap' => 'ul',
		'open' => null,
		'close' => null,
		'open_line' => null,
		'close_line' => null,
		'open_title' => null,
		'close_title' => null,
		'open_value' => null,
		'close_value' => null,
		), $atts ) );

		// Setup the various structures table, ul, div
		$structures = array (
		"none" =>
		array (
		"open" => "",
		"close" => "",
		"open_line" => "",
		"close_line" => "",
		"open_title" => "",
		"close_title" => "",
		"open_value" => "",
		"close_value" => "",
		),
		"table" =>
		array (
		"open" => "<table>\n",
		"close" => "</table>\n",
		"open_line" => "<tr>\n",
		"close_line" => "</tr>\n",
		"open_title" => "<th>\n",
		"close_title" => "</th>\n",
		"open_value" => "<td>\n",
		"close_value" => "</td>\n",
		),
		"ul" =>
		array (
		"open" => "<ul>\n",
		"close" => "</ul>\n",
		"open_line" => "<li>\n",
		"close_line" => "</li>\n",
		"open_title" => "<span>",
		"close_title" => "</span>",
		"open_value" => " ",
		"close_value" => "",
		),
		"div" =>
		array (
		"open" => "<div>",
		"close" => "</div>\n",
		"open_line" => "<p>",
		"close_line" => "</p>\n",
		"open_title" => "<span>",
		"close_title" => "</span>",
		"open_value" => " ",
		"close_value" => "",
		),
		);

		//Initialize with blanks
		$fmt = $structures['none'];

		// If its' predefined
		if(in_array($wrap, array('table','ul','div'))){
			$fmt = $structures[$wrap];
		}

		//Override any defined in $atts
		foreach($fmt as $key => $item){
			$fmt[$key] = ($$key === null) ? $fmt[$key] : $$key;
		}

		$custom_fields = get_option( 'ct_custom_fields' );
		if (empty($custom_fields)) $custom_fields = array();

		$result = $fmt['open'];
		foreach ( $custom_fields as $custom_field ){
			$output = in_array($post->post_type, $custom_field['object_type']);
			if ( $output ){


				$prefix = ( empty( $custom_field['field_wp_allow'] ) ) ? '_ct_' : 'ct_';
				$fid = $prefix . $custom_field['field_id'];

				$result .= $fmt['open_line'];
				$result .= $fmt['open_title'];
				$result .= ( $custom_field['field_title'] );
				$result .= $fmt['close_title'];
				$result .= $fmt['open_value'];

				$result .= do_shortcode('[ct id="' . $fid . '"]');

				$result .= $fmt['close_value'];
				$result .= $fmt['close_line'];

			}
		}
		$result .= $fmt['close'];

		$result = apply_filters('custom_fields_shortcode', $result, $atts, $content);

		// Wrap of for CSS after filtering
		$result = '<div class="ct-custom-field-block">' . "\n{$result}</div>\n";

		return $result;
	}

	/**
	* Process the [ct] and [tax] shortcodes.
	*
	* Since the [ct] and [tax] shortcodes needs to be run earlier than all other shortcodes
	* so the values may be used by other shortcodes, media [embed] is the earliest with an 8 priority so we need an earlier priority.
	* this function removes all existing shortcodes, registers the [ct] and [tax] shortcode,
	* calls {@link do_shortcode()}, and then re-registers the old shortcodes.
	*
	* @uses $shortcode_tags
	* @uses remove_all_shortcodes()
	* @uses add_shortcode()
	* @uses do_shortcode()
	*
	* @param string $content Content to parse
	* @return string Content with shortcode parsed
	*/
	function run_custom_shortcodes($content){
		global $shortcode_tags;

		// Back up current registered shortcodes and clear them all out
		$orig_shortcode_tags = $shortcode_tags;
		remove_all_shortcodes();

		add_shortcode( 'ct', array(&$this, 'ct_shortcode') );

		add_shortcode( 'tax', array(&$this, 'tax_shortcode') );

		// Do the shortcode (only the [ct] and [tax] are registered)
		$content = do_shortcode( $content );

		// Put the original shortcodes back
		$shortcode_tags = $orig_shortcode_tags;

		return $content;
	}

	/**
	* gets the current post type in the WordPress Admin
	*/
	function get_current_post_type() {
		global $post, $typenow, $current_screen;

		//we have a post so we can just get the post type from that
		if ( $post && $post->post_type )
		return $post->post_type;

		//check the global $typenow - set in admin.php
		elseif( $typenow )
		return $typenow;

		//check the global $current_screen object - set in sceen.php
		elseif( $current_screen && $current_screen->post_type )
		return $current_screen->post_type;

		//lastly check the post_type querystring
		elseif( isset( $_REQUEST['post_type'] ) )
		return sanitize_key( $_REQUEST['post_type'] );

		//we do not know the post type!
		return null;
	}

	/**
	* Return JQuery script to validate an array of custom fields
	* @$custom_fields array of custom field definition to generate rules for.
	*
	*/
	function validation_rules($custom_fields = null ){ //

		if(empty($custom_fields)) return '';

		$rules = array();
		$messages = array();

		$validation = array();
		$validation[] = "jQuery('#ct_custom_fields_form').closest('form').validate();"; //find the form we're validating

		foreach($custom_fields as $custom_field) {

			$prefix = ( empty( $custom_field['field_wp_allow'] ) ) ? '_ct_' : 'ct_';

			$fid = $prefix . $custom_field['field_id'];
			if ( in_array( $custom_field['field_type'], array('checkbox', 'multiselectbox') ) )
			$fid = '"' . $fid . '[]"'; //Multichoice version
			else
			$fid = '"' . $fid . '"' ;

			// collect messages
			$msgs = array();

			$message = ( empty( $custom_field['field_message']) ) ? '' : trim($custom_field['field_message']);
			if( ! empty( $message ) ) $msgs[] = "required: '{$message}'";

			$regex_options = ( empty( $custom_field['field_regex_options']) ) ? '' : trim($custom_field['field_regex_options']);

			$regex_message = ( empty( $custom_field['field_regex_message']) ) ? '' : trim($custom_field['field_regex_message']);
			if( ! empty( $regex_message ) ) $msgs[] = "regex: '{$regex_message}'";


			if( ! empty($msgs) )	$validation[] = "jQuery('[name={$fid}]').rules('add', { messages: {" . implode(", ", $msgs ) . " } });";

			//Collect rules
			$rls = array();
			if ($custom_field['field_required'] || ! empty($custom_field['field_regex'])) { //we have validation rules
				if( ! empty($custom_field['field_required']) ) $rls[] = 'required: true';
				if( ! empty($custom_field['field_regex'])) $rls[] = "regex: /{$custom_field['field_regex']}/{$regex_options}";
				//Add more in the future
			}

			if( ! empty($rls) ) $validation[] = "jQuery('[name={$fid}]').rules('add', { " . implode(", ", $rls ) . " } );";
		}

		$validation = implode("\n", $validation);

		return $validation;
	}
}

// Initiate Content Types Module


if(!is_admin()) {
	$CustomPress_Core = new CustomPress_Content_Types();
}

endif;
