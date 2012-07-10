<?php 
class PageLinesUpdateCheck {

    function __construct( $version = null ){
	
		global $current_user;
    	$this->url_theme = apply_filters( 'pagelines_theme_update_url', PL_API . 'v3' );
    	$this->theme  = 'PageLines';
 		$this->version = $version;
		$this->username = get_pagelines_credentials( 'user' );
		$this->password = get_pagelines_credentials( 'pass' );

		$this->site_tran = get_site_transient('update_themes');

		get_currentuserinfo();
		$bad_users = apply_filters( 'pagelines_updates_badusernames', array( 'admin', 'root', 'test', 'testing', '' ) );
		if ( in_array( strtolower( $this->username ),  $bad_users ) ) {
			delete_option( 'pagelines_extend_creds' );
			$this->username = '';
			$this->password = '';
		}
		$this->pagelines_theme_check_version();
    }

	/**
	 * TODO Document!
	 */
	function pagelines_theme_check_version() {

		if ( get_pagelines_option('disable_updates') == true )
			return;
		if ( !VPRO ) {
			$this->pagelines_theme_update_check();
		} else {
			add_action('admin_notices', array(&$this,'pagelines_theme_update_nag') );
			add_filter('site_transient_update_themes', array(&$this,'pagelines_theme_update_push') );
			add_filter('transient_update_themes', array(&$this,'pagelines_theme_update_push') );		
			add_action('load-update.php', array(&$this,'pagelines_theme_clear_update_transient') );
			add_action('load-themes.php', array(&$this,'pagelines_theme_clear_update_transient') );
		}
	}
	
		/**
		 * TODO Document!
		 */
		function pagelines_theme_update_push($value) {

			$pagelines_update = ( array ) maybe_unserialize( $this->pagelines_theme_update_check() );

			if ( $pagelines_update && isset( $pagelines_update['package'] ) && $pagelines_update['package'] !== 'bad' ) {
				$value->response['pagelines'] = $pagelines_update;
								
				$this->site_tran->response['pagelines'] = $pagelines_update;				
				set_site_transient( 'update_themes', $this->site_tran );
				return $this->site_tran;
			}
			return $value;
		}
	
	/**
	 * TODO Document!
	 */
	function bad_creds( $errors ) {
		$errors['api']['title'] = 'API error';
		$errors['api']['text'] = 'Launchpad Username and Password are required for automatic updates.';
		return $errors;
	}

	/**
	 * TODO Document!
	 */
	function pagelines_theme_clear_update_transient() {

		delete_transient( EXTEND_UPDATE );
		remove_action( 'admin_notices', array( &$this,'pagelines_theme_update_nag' ) );
		delete_transient( 'pagelines_sections_cache' );
		remove_theme_mod( 'pending_updates' );
	}


	
	/**
	 * TODO Document!
	 */
	function pagelines_theme_update_nag() {
		
		$pagelines_update = $this->pagelines_theme_update_check();

		if ( !is_super_admin() || !$pagelines_update )
			return false;
			
		if ( $this->username == '' || $this->password == '' || $pagelines_update['package'] == 'bad' ) {
			
			//	add_filter('pagelines_admin_notifications', array(&$this,'bad_creds') );

		}
			
		echo '<div class="updated fade update-nag">';
		
		printf( '%s Framework %s is available.', $this->theme, esc_html( $pagelines_update['new_version'] ) );
		
		printf( 
			' %s', 
			( $pagelines_update['package'] != 'bad' ) 
				? sprintf( 'You should <a href="%s">update now</a>.', admin_url('update-core.php') ) 
				: sprintf( '<a href="%s">Click here</a> to setup your PageLines account.', PLAdminPaths::account() ) 
		);

		echo ( $pagelines_update['extra'] ) ? sprintf('<br />%s', $pagelines_update['extra'] ) : '';
		echo '</div>';
		
	}	
	
	/**
	 * TODO Document!
	 */
	function pagelines_theme_update_check() {
		global $wp_version;

		$pagelines_update = get_transient( EXTEND_UPDATE );

		if ( !$pagelines_update ) {
			$url = $this->url_theme;
			$options = array(
					'body' => array(
						'version'		=> $this->version,
						'wp_version'	=> $wp_version,
						'php_version'	=> phpversion(),
						'uri'			=> home_url(),
						'theme'			=> $this->theme,
						'user'			=> $this->username,
						'password'		=> $this->password,
						'user-agent'	=> "WordPress/$wp_version;"
					)
			);

			$response = pagelines_try_api($url, $options);
			$pagelines_update = wp_remote_retrieve_body($response);

			// If an error occurred, return FALSE, store for 1 hour
			if ( $pagelines_update == 'error' || is_wp_error($pagelines_update) || !is_serialized( $pagelines_update ) || isset( $pagelines_update['package'] ) && $pagelines_update['package'] == 'bad' ) {
				$this->set_transients( array('new_version' => $this->version), 60*60);
				return FALSE;
			}

			// Else, unserialize
			$pagelines_update = maybe_unserialize($pagelines_update);

			// And store in transient
			$this->set_transients( $pagelines_update, 60*60*24 );
			if ( isset( $pagelines_update['licence'] ) )
				update_pagelines_licence( $pagelines_update['licence'] );
				
			$this->pagelines_get_user_updates();
			
		}

		// If we're already using the latest version, return FALSE
		if ( !isset($pagelines_update['new_version']) || version_compare($this->version, $pagelines_update['new_version'], '>=') )
			return FALSE;

		return $pagelines_update;
	}
	
	
	function set_transients( $pagelines_update, $time ) {
		
		set_transient( EXTEND_UPDATE, $pagelines_update, $time ); 	
	}
	
	function pagelines_get_user_updates() {

		$options = array(
			'body'	=> array(
				'updates'	=> json_encode( get_theme_mod( 'available_updates' ) )
			) );

		$url = PL_API . '?get_updates';
		$response = pagelines_try_api($url, $options);

		$pagelines_update = wp_remote_retrieve_body($response);
		set_theme_mod( 'pending_updates', $pagelines_update );
	}
} // end class
