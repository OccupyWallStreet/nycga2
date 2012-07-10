<?php
/**
 * Extend Actions
 * 
 * @author PageLines
 *
 * @since 2.0.b3
 */

 class PageLinesExtendActions {
	
	

	/**
	*
	* @TODO document
	*
	*/
	function __construct() {

		$this->ui = new PageLinesExtendUI;

		add_action( 'wp_ajax_pagelines_ajax_extend_it_callback', array(&$this, 'extend_it_callback' ) );	
		add_action( 'admin_init', array(&$this, 'extension_uploader' ) );
		add_action( 'admin_init', array(&$this, 'check_creds' ) );
 	}

	/**
	 * 
	 * Extension AJAX callbacks
	 * 
	 */
	function extend_it_callback( $uploader = false, $checked = null ) {

		// 2. Variable Setup
			$mode =  $_POST['extend_mode'];
			$type =  $_POST['extend_type'];
			$file =  $_POST['extend_file'];
			$path =  $_POST['extend_path'];
			$product = $_POST['extend_product'];
			$dash = isset( $_POST['extend_dash'] ) ? $_POST['extend_dash'] : false;
			
		if ( $uploader = 'pagelines_ajax_extend_it_callback' )
			$uploader = false;

		// 3. Do our thing...

		switch ( $mode ) {

			case 'integration_download':
			
				$this->integration_download( $type, $file, $path, $uploader, $checked );

			break;

			case 'integration_activate':

				integration_activate( $type, $file, $path, $uploader, $checked );
				
			break;

			case 'integration_deactivate':

				integration_deactivate( $type, $file, $path, $uploader, $checked );

			break;			

			case 'plugin_install': 
				
				$this->plugin_install( $type, $file, $path, $uploader, $checked, $dash );
					
			break;

			case 'plugin_upgrade':

				$this->plugin_upgrade( $type, $file, $path, $uploader, $checked, $dash );
				
			break;

			case 'plugin_delete':
				
				$this->plugin_delete( $type, $file, $path, $uploader, $checked );
				
			break;

			case 'plugin_activate':

				$this->plugin_activate( $type, $file, $path, $uploader, $checked );
				
			break;

			case 'plugin_deactivate':

				$this->plugin_deactivate( $type, $file, $path, $uploader, $checked );
				
			break;

			case 'section_install':

				$this->section_install( $type, $file, $path, $uploader, $checked );
				
			break;

			case 'section_upgrade':

				$this->section_upgrade( $type, $file, $path, $uploader, $checked, $dash );
				
			break;

			case 'section_delete':

				$this->section_delete( $type, $file, $path, $uploader, $checked );

			break;

			case 'section_activate':

				$this->section_activate( $type, $file, $path, $uploader, $checked );
				
			break;

			case 'section_deactivate':

				$this->section_deactivate( $type, $file, $path, $uploader, $checked );
				
			break;

			case 'theme_install':

				$this->theme_install( $type, $file, $path, $uploader, $checked );
	
			break;

			case 'theme_upgrade':

				$this->theme_upgrade( $type, $file, $path, $uploader, $checked, $dash );

			break;					

			case 'theme_delete':

				$this->theme_delete( $type, $file, $path, $uploader, $checked );

			break;

			case 'theme_activate':

				$this->theme_activate( $type, $file, $path, $uploader, $checked );
	
			break;

			case 'theme_deactivate':

				$this->theme_deactivate( $type, $file, $path, $uploader, $checked );

			break;
			
			case 'redirect':

				$this->redirect( $type, $file, $path, $uploader, $checked );

			break;

			case 'purchase':

				$this->purchase( $type, $file, $path, $uploader, $checked );

			break;

			case 'login':

				$this->login( $type, $file, $path, $uploader, $checked );
				
			break;
			
			case 'version_fail':
			
				$this->version_fail( $type, $file, $path, $uploader, $checked );

			break;
			
			case 'depends_fail':
			
				$this->depends_fail( $type, $file, $path, $uploader, $checked );

			break;
			
			case 'pro_fail':
			
				$this->pro_fail( $type, $file, $path, $uploader, $checked );

			break;
			
			case 'subscribe':
			
				$this->subscribe( $type, $file, $path, $uploader, $checked );

			break;
			
			case 'unsubscribe':
			
				$this->unsubscribe( $type, $file, $path, $uploader, $checked );

			break;
		}
		die(); // needed at the end of ajax callbacks
	}

	/**
	 * Uploader for sections.
	 * 
	 */
	function extension_uploader() {
		
		if ( !empty($_POST['upload_check'] ) && check_admin_referer( 'pagelines_extend_upload', 'upload_check') ) {

			if ( $_FILES[ $_POST['type']]['size'] == 0 ) {
				$this->page_reload( PL_ADMIN_STORE_SLUG.'&extend_error=blank', null, 0 );
				exit();
			}

			// right we made it this far! It needs to be a section!
			$type = $_POST['type'];
			$filename = $_FILES[ $type ][ 'name' ];
			$payload = $_FILES[ $type ][ 'tmp_name' ];
				
			if ( !preg_match( '/section-([^\.]*)\.zip/i', $filename, $out ) ) {
				$this->page_reload( PL_ADMIN_STORE_SLUG.'&extend_error=filename', null, 0 );
				exit();
			}

			$uploader = true;
			$_POST['extend_mode']	= 'section_install';
			$_POST['extend_file']	= $payload;
			$_POST['extend_path']	= $out[1];
			$_POST['extend_type']	= 'section';
			$_POST['extend_product']= '';
			$this->extend_it_callback( $uploader, null );
			exit;
		}	
	}
	
	/**
	 * See if we have filesystem permissions.
	 * 
	 */	

	/**
	*
	* @TODO document
	*
	*/
	function check_creds( $extend = null, $context = WP_PLUGIN_DIR ) {
		
		if ( get_filesystem_method() == 'direct' ) {
			
			WP_Filesystem();
			return;
		}

		if ( isset( $_GET['creds'] ) && $_POST && WP_Filesystem( $_POST ) )
			$this->extend_it_callback( false, true );
			
		if ( !$extend )
			return;			

		if ( false === ( $creds = @request_filesystem_credentials( admin_url( PL_ADMIN_STORE_URL.'&creds=yes' ), $type = '', $error = false, $context, $extra_fields = array( 'extend_mode', 'extend_type', 'extend_file', 'extend_path', 'extend_product' ) ) ) ) {
			exit; 
		}	
	}
	
	
	// return true if were NOT using direct fs.

	/**
	*
	* @TODO document
	*
	*/
	function get_fs_method(){
		
		global $wp_filesystem;
		
		if ( is_object( $wp_filesystem ) && $wp_filesystem->method != 'direct' )
			return true;
		else
			return false;
	}
	
	
	/**
	 * Generate a download link.
	 * 
	 */
	function make_url( $type, $file, $product = null ) {
		
		return sprintf( '%s%ss/download.php?d=%s.zip%s', PL_API_FETCH, $type, $file, ( isset( $product ) ) ? '&product=' . $product : '' );
		
	}
	
	/**
	 * Get a PayPal link.
	 * 
	 */
	function get_payment_link( $product ) {
		
		return sprintf( 'https://pagelines.com/api/?paypal=%s|%s', $product, admin_url( 'admin.php' ) );
	}
	
	
	/**
	 * Reload the page
	 * Helper function
	 */
 	function page_reload( $location, $product = null, $message = '') {
		
		do_action( 'extend_flush' );
		
		if ( $this->get_fs_method() ) {
			
			$time = 0;
		} else {
			$time = 700;
			echo $message;
		}
		
		$r = rand( 1,100 );
		
		$admin = admin_url( sprintf( 'admin.php?r=%1$s&page=%2$s', $r, $location ) );
		
		$location = ( $product ) ? self::get_payment_link( $product ) : $admin;

		printf( 
			'<script type="text/javascript">setTimeout(function(){ window.location.href = \'%s\';}, %s);</script>', 
			$location, 
			$time 
		);
		
 	}

 	function int_download( $location, $time = 300 ) {
	
		$r = rand( 1,100 );
		$admin = admin_url( sprintf( 'admin.php?r=%1$s&page=%2$s', $r, PL_ADMIN_STORE_SLUG.'#integrations' ) );
		printf( '<script type="text/javascript">setTimeout(function(){ window.location.href = \'%s\';}, %s);</script>', $location, $time );	
		printf( '<script type="text/javascript">setTimeout(function(){ window.location.href = \'%s\';}, %s);</script>', $admin, 700 );
 	}


	/**
	*
	* @TODO document
	*
	*/
	function sandbox( $file, $type ) {

		register_shutdown_function( array(&$this, 'error_handler'), $type );
		@include_once( $file );
	}

	/**
	 * Throw up on error
	 */
	function error_handler( $type ) { 
		
		$a = error_get_last();
		
		$error = '';
		
		// Unable to activate
		if( $a['type'] == 4 || $a['type'] == 1  )
			$error .= sprintf( 'Unable to activate the %s.', $type );
		
		//Error on line
		if( $error && is_pl_debug() )
			$error .= sprintf( '<br />%s in %s on line: %s', $a['message'], basename( $a['file'] ), $a['line'] );
		
		echo $error;
	}
	
	
	/**
	 * Provide Download to integration
	 */
	function integration_download( $type, $file, $path, $uploader, $checked ) {
		
		$url = $this->make_url( $type, $file );
	
		echo __( 'Downloaded', 'pagelines' );
	
		$this->int_download( $url );
		
	}
	/**
	*
	* @TODO document
	*
	*/
	function subscribe( $type, $file, $path, $uploader, $checked ) {
		
		print __( 'Subscribing...', 'pagelines' );

		$data = explode ( '|', $path );

		$options = array(
			'body' => array(
				'email'		=>	get_bloginfo( 'admin_email'),
				'version'	=>	$data[2],
				'username'	=>	$data[0]
			)
		);
		
		$url = 'api.pagelines.com/subscribe/' . $data[1];
		
		$result = pagelines_try_api($url, $options);

		if ( $result['response']['code'] == 200 && $result['body'] == $data[1] )
			{

				delete_transient( sprintf( PL_ADMIN_STORE_SLUG.'_%ss', $type ) );				
				$message = __( 'Done.', 'pagelines' );		
				$text = sprintf( '&extend_text=%s_install', $type );
				$this->page_reload( PL_ADMIN_STORE_SLUG . $text, null, $message );
			}
		
	}	

	/**
	*
	* @TODO document
	*
	*/
	function unsubscribe( $type, $file, $path, $uploader, $checked ) {
		
			print __( 'Unsubscribing...', 'pagelines');

			$data = explode ( '|', $path );
			
			$options = array(
				'body' => array(
					'email'		=>	get_bloginfo( 'admin_email'),
					'version'	=>	$data[2],
					'username'	=>	$data[0]
				)
			);

			$url = 'api.pagelines.com/unsubscribe/' . $data[1];

			$result = pagelines_try_api($url, $options);

			if ( $result['response']['code'] == 200 && $result['body'] == $data[1] )
				{

					delete_transient( sprintf( PL_ADMIN_STORE_SLUG.'_%ss', $type ) );				
					$message = __( 'Done.', 'pagelines' );		
					$text = sprintf( '&extend_text=%s_install#added', $type );
					$this->page_reload( PL_ADMIN_STORE_SLUG . $text, null, $message );
				}
		
	}

	/**
	*
	* @TODO document
	*
	*/
	function version_fail( $type, $file, $path, $uploader, $checked ) {
		
		printf( __( 'You need to have version %s of the framework for this %s', 'pagelines' ), $file, $path );
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function depends_fail( $type, $file, $path, $uploader, $checked ) {
		
		printf( __( 'You need to install %s first.', 'pagelines' ), $file );
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function pro_fail( $type, $file, $path, $uploader, $checked ) {
		
		printf( __( 'This %s is free with a Pro license.', 'pagelines' ), $path );
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function plugin_install( $type, $file, $path, $uploader, $checked, $dash) {
		

		$this->wp_libs();
		
		if ( !$checked )
			$this->check_creds( 'extend', WP_PLUGIN_DIR );

		$skin = new PageLines_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );
		$destination = $this->make_url( $type, $file );						
		@$upgrader->install( $destination );

		$this->sandbox( WP_PLUGIN_DIR . $path, 'plugin' );
		activate_plugin( $path );
		
		$message = __( 'Plugin Installed.', 'pagelines' );		
		$text = '&extend_text=plugin_install#your_plugins';
		
		$url = PL_ADMIN_STORE_SLUG;
		
		if ( $dash ) {
			$url = PL_MAIN_DASH;
		}
		$this->page_reload( $url, null, $message );		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function plugin_delete( $type, $file, $path, $uploader, $checked ) {
		
		$this->wp_libs();
		
		if ( !$checked )
			$this->check_creds( 'extend', WP_PLUGIN_DIR );		
		global $wp_filesystem;
		delete_plugins( array( ltrim( $file, '/' ) ) );
		$message = __( 'Plugin Deleted.', 'pagelines' );		
		$text = '&extend_text=plugin_delete#your_plugins';
		$this->page_reload( PL_ADMIN_STORE_SLUG . $text, null, $message );
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function plugin_upgrade( $type, $file, $path, $uploader, $checked, $dash ) {
		
		$this->wp_libs();
		
		if ( !$checked )
			$this->check_creds( 'extend' );		
		global $wp_filesystem;

		$skin = new PageLines_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );

		$active = is_plugin_active( ltrim( $file, '/' ) );
		deactivate_plugins( array( $file ) );
		
		$wp_filesystem->delete( trailingslashit( WP_PLUGIN_DIR ) . $path, true, false  );
		@$upgrader->install( $this->make_url( $type, $path ) );

		$this->sandbox( WP_PLUGIN_DIR . $file, 'plugin');
		if ( $active )
			activate_plugin( ltrim( $file, '/' ) );
		// Output
		$message = __( 'Plugin Upgraded.', 'pagelines' );
		$text = '&extend_text=plugin_upgrade#your_plugins';

		$url = PL_ADMIN_STORE_SLUG;
		
		if ( $dash ) {
			$url = PL_MAIN_DASH;
			$this->remove_update( $path );
		}
		$this->page_reload( $url, null, $message );
		
	}
	
	function remove_update( $slug ) {

		$updates = json_decode( get_theme_mod( 'pending_updates' ) );
		if( isset( $updates->$slug ) ) {
			unset( $updates->$slug );
			set_theme_mod( 'pending_updates', json_encode( $updates ) );
			remove_theme_mod( 'available_updates' );
		}
	}


	/**
	*
	* @TODO document
	*
	*/
	function plugin_activate( $type, $file, $path, $uploader, $checked ) {
		
		$this->sandbox( WP_PLUGIN_DIR . $file, 'plugin' );
	 	activate_plugin( $file );
	 	$message = __( 'Plugin Activated.', 'pagelines' );
	 	$this->page_reload( PL_ADMIN_STORE_SLUG, null, $message );
	
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function plugin_deactivate( $type, $file, $path, $uploader, $checked ) {
		
		deactivate_plugins( array( $file ) );
		// Output
 		$message = __( 'Plugin Deactivated.', 'pagelines' );
	 	$this->page_reload( PL_ADMIN_STORE_SLUG, null, $message );

	}
	

	/**
	*
	* @TODO document
	*
	*/
	function section_install( $type, $file, $path, $uploader, $checked ) {

		$this->wp_libs();
				
		if ( !$checked )
			$this->check_creds( 'extend', WP_PLUGIN_DIR );		
		global $wp_filesystem;

		$skin = new PageLines_Upgrader_Skin();
		$upgrader = new PageLines_Section_Installer( $skin );
		$time = 0;
		
		$url = ( $uploader ) ? $file : $this->make_url( $type, $path );
		$out = @$upgrader->install( $url );		
		$wp_filesystem->move( trailingslashit( $wp_filesystem->wp_plugins_dir() ) . $path, sprintf( '%s/pagelines-sections/%s', trailingslashit( $wp_filesystem->wp_plugins_dir() ), $path ) );
		
		$this->sections_reset();
		$text = '&extend_text=section_install#your_added_sections';
		if ( $uploader && is_wp_error( $out ) )
			$this->page_reload( sprintf( PL_ADMIN_STORE_SLUG.'&extend_error=%s', $out->get_error_code() ) , null, 0 );
		else
			$this->page_reload( PL_ADMIN_STORE_SLUG . $text, null, __( 'Section Installed.', 'pagelines' ) );
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function section_delete( $type, $file, $path, $uploader, $checked ) {

		$this->wp_libs();
				
		if ( !$checked ) {
			$this->check_creds( 'extend', PL_EXTEND_DIR );		
		}
		global $wp_filesystem;

		$wp_filesystem->delete( sprintf( '%s/pagelines-sections/%s', trailingslashit( $wp_filesystem->wp_plugins_dir() ), $file ), true, false );
		
		$this->sections_reset();
		$message = __( 'Section Deleted.', 'pagelines' );
		$text = '&extend_text=section_delete#your_added_sections';
		$this->page_reload( PL_ADMIN_STORE_SLUG . $text, null, $message );
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function section_upgrade( $type, $file, $path, $uploader, $checked, $dash ) {

		$this->wp_libs();
				
		if ( !$checked )
			$this->check_creds( 'extend', PL_EXTEND_DIR );		
		global $wp_filesystem;

		$skin = new PageLines_Upgrader_Skin();
		$upgrader = new PageLines_Section_Installer($skin);


		$folder = sprintf( '%s/pagelines-sections/%s', trailingslashit( $wp_filesystem->wp_plugins_dir() ), $file );

		$wp_filesystem->delete( $folder, true, false  );
				
		@$upgrader->install( $this->make_url( 'section', $file ) );			
		$wp_filesystem->move( trailingslashit( $wp_filesystem->wp_plugins_dir() ) . $file, sprintf( '%s/pagelines-sections/%s', trailingslashit( $wp_filesystem->wp_plugins_dir() ), $file ) );
		
		$this->sections_reset();
		// Output
		$text = '&extend_text=section_upgrade#your_added_sections';
		$message = __( 'Section Upgraded', 'pagelines' );
		
		$url = PL_ADMIN_STORE_SLUG;
		if ( $dash ) {
			$url = PL_MAIN_DASH;
			$this->remove_update( $file );
		}
		$this->page_reload( $url, null, $message );
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function section_activate( $type, $file, $path, $uploader, $checked ) {

		$this->sandbox( $path, 'section' );
		$available = get_option( 'pagelines_sections_disabled' );
		unset( $available[$path][$file] );
		update_option( 'pagelines_sections_disabled', $available );
		// Output
	 	$message = __( 'Section Activated.', 'pagelines' );
	 	$this->page_reload( PL_ADMIN_STORE_SLUG, null, $message );
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function section_deactivate( $type, $file, $path, $uploader, $checked ) {
		
		$disabled = get_option( 'pagelines_sections_disabled', array( 'child' => array(), 'parent' => array() ) );
		$disabled[$path][$file] = true; 
		update_option( 'pagelines_sections_disabled', $disabled );
		$this->sections_reset();
		// Output
	 	$message = __( 'Section Deactivated.', 'pagelines' );
	 	$this->page_reload( PL_ADMIN_STORE_SLUG, null, $message );
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function theme_install( $type, $file, $path, $uploader, $checked ) {

		$this->wp_libs();
				
		if ( !$checked ) {
			$this->check_creds( 'extend', PL_EXTEND_THEMES_DIR );
		}			
		$skin = new PageLines_Upgrader_Skin();
		$upgrader = new Theme_Upgrader( $skin );
		global $wp_filesystem;
		@$upgrader->install( $this->make_url( $type, $file ) );

		// Output
		$text = '&extend_text=theme_install#your_themes';
		$message = __( 'Theme Installed.', 'pagelines' );
		$this->page_reload( PL_ADMIN_STORE_SLUG . $text, null, $message );
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function theme_delete( $type, $file, $path, $uploader, $checked ) {

		$this->wp_libs();
				
		if ( !$checked ) {
			$this->check_creds( 'extend', PL_EXTEND_THEMES_DIR );		
		}
		global $wp_filesystem;
		$wp_filesystem->delete( trailingslashit( PL_EXTEND_THEMES_DIR ) . $file, true, false );

		$text = '&extend_text=theme_delete#your_themes';
		$message = __( 'Theme Deleted.', 'pagelines' );
		$this->page_reload( PL_ADMIN_STORE_SLUG . $text, null, $message );
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function theme_upgrade( $type, $file, $path, $uploader, $checked, $dash) {

		$this->wp_libs();
			
		if ( !$checked )
			$this->check_creds( 'extend', PL_EXTEND_THEMES_DIR );		
		global $wp_filesystem;

		$active = ( basename( get_stylesheet_directory()  ) === $file ) ? true : false;

		if ( $active )
			switch_theme( basename( get_template_directory() ), basename( get_template_directory() ) );

		$skin = new PageLines_Upgrader_Skin();
		$upgrader = new Theme_Upgrader( $skin );

		$wp_filesystem->delete( trailingslashit( PL_EXTEND_THEMES_DIR ) . $file, true, false );

		@$upgrader->install( $this->make_url( $type, $file ) );

		if ( $active )
			switch_theme( basename( get_template_directory() ), $file );
		// Output
		$text = '&extend_text=theme_upgrade#your_themes';
		$message = __( 'Theme Upgraded.', 'pagelines' );
	
		$url = PL_ADMIN_STORE_SLUG;
		
		if ( $dash ) {
			$url = PL_MAIN_DASH;
			$this->remove_update( $path );
		}
		$this->page_reload( $url, null, $message );
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function theme_activate( $type, $file, $path, $uploader, $checked ) {
		
		switch_theme( basename( get_template_directory() ), $file );
		delete_transient( 'pagelines_sections_cache' );

		$message = __( 'Theme Activated.', 'pagelines' );
		$this->page_reload( PL_ADMIN_STORE_SLUG, null, $message );
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function theme_deactivate( $type, $file, $path, $uploader, $checked ) {
		
		switch_theme( basename( get_template_directory() ), basename( get_template_directory() ) );
		delete_transient( 'pagelines_sections_cache' );
		
		$message = __( 'Theme Deactivated.', 'pagelines' );
		$this->page_reload( PL_ADMIN_STORE_SLUG, null, $message );
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function redirect( $type, $file, $path, $uploader, $checked ) {
		
		echo sprintf( __( 'Sorry only network admins can install %ss.', 'pagelines' ), $type );		
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function purchase( $type, $file, $path, $uploader, $checked ) {
		
		_e( 'Taking you to PayPal.com', 'pagelines' );
		$this->page_reload( PL_ADMIN_STORE_SLUG, $file );
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function login( $type, $file, $path, $uploader, $checked ) {
		
		_e( 'Moving to account setup..', 'pagelines' );
		$this->page_reload( PL_MAIN_DASH . '#Your_Account' );
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function wp_libs() {
		
		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
		include( PL_ADMIN . '/library.extension.php' );
		
	}	
	

	/**
	*
	* @TODO document
	*
	*/
	function sections_reset() {
		
		global $load_sections;
		delete_transient( 'pagelines_sections_cache' );
		$load_sections->pagelines_register_sections( true, false );
	}
}
