<?php
//
//  class-ai1ec-themes-controller.php
//  all-in-one-event-calendar
//
//  Created by The Seed Studio on 2012-04-05.
//

/**
 * Ai1ec_Themes_Controller class
 *
 * @package Controllers
 * @author The Seed Studio
 **/
class Ai1ec_Themes_Controller {
	/**
	 * _instance class variable
	 *
	 * Class instance
	 *
	 * @var null | object
	 **/
	private static $_instance = NULL;

	/**
	 * get_instance function
	 *
	 * Return singleton instance
	 *
	 * @return object
	 **/
	static function get_instance() {
		if( self::$_instance === NULL ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Constructor
	 *
	 * Default constructor
	 **/
	private function __construct() { }

	/**
	 * view function
	 *
	 * @return void
	 **/
	public function view() {
		global $ai1ec_view_helper, $ct;
		// defaults
		$activated = false;
		$deleted   = false;

		// check if action is set
		if( isset( $_GET['action'] ) && ! empty( $_GET['action'] ) ) {
			// action can activate or delete a theme
			switch( $_GET['action'] ) {
				// activate theme
				case 'activate':
					$activated = $this->activate_theme();
					break;
				// delete theme
				case 'delete':
					$deleted = $this->delete_theme();
					break;
			}
		}

		$_list_table = new Ai1ec_Themes_List_Table();
		$_list_table->prepare_items();

		$args = array(
			'activated'     => $activated,
			'deleted'       => $deleted,
			'ct'            => $ct,
			'wp_list_table' => $_list_table
		);

		add_thickbox();
		wp_enqueue_script( 'theme-preview' );

		$ai1ec_view_helper->display_admin( 'themes.php', $args );
	}

	/**
	 * view_install function
	 *
	 * @return void
	 **/
	public function view_install() {
		global $ai1ec_view_helper;

		$_list_table = new Ai1ec_Themes_List_Table();
		$_list_table->prepare_items();
		ob_start();
		$_list_table->display();
		$html = ob_get_clean();
		$args = array(
			'html' => $html
		);

		$ai1ec_view_helper->display_admin( 'themes-install.php', $args );
	}

	/**
	 * activate_theme function
	 *
	 * @return bool
	 **/
	public function activate_theme() {
		check_admin_referer( 'switch-ai1ec_theme_' . $_GET['ai1ec_template'] );
		$this->switch_theme( $_GET['ai1ec_template'], $_GET['ai1ec_stylesheet'] );
		return true;
	}

	/**
	 * switch_theme function
	 *
	 * @return void
	 **/
	public function switch_theme( $template, $stylesheet ) {
		update_option( 'ai1ec_template', $template );
		update_option( 'ai1ec_stylesheet', $stylesheet );
		delete_option( 'ai1ec_current_theme' );
	}

	/**
	 * delete_theme function
	 *
	 * @return bool
	 **/
	public function delete_theme() {
		check_admin_referer( 'delete-ai1ec_theme_' . $_GET['ai1ec_template'] );
		if( ! current_user_can( 'delete_themes' ) )
			wp_die( __( 'Cheatin&#8217; uh?' ) );

		$this->remove_theme( $_GET['ai1ec_template'] );
		return true;
	}

	/**
	 * remove_theme function
	 *
	 * @return void
	 **/
	public function remove_theme( $template ) {
		global $wp_filesystem;

		if ( empty($template) )
			return false;

		ob_start();
		if ( empty( $redirect ) )
			$redirect = wp_nonce_url(
				admin_url( AI1EC_THEME_SELECTION_BASE_URL ) .
				"&amp;action=delete&amp;ai1ec_template=$template", 'delete-ai1ec_theme_' . $template
			);
		if ( false === ($credentials = request_filesystem_credentials($redirect)) ) {
			$data = ob_get_contents();
			ob_end_clean();
			if ( ! empty($data) ){
				include_once( ABSPATH . 'wp-admin/admin-header.php');
				echo $data;
				include( ABSPATH . 'wp-admin/admin-footer.php');
				exit;
			}
			return;
		}

		if ( ! WP_Filesystem($credentials) ) {
			request_filesystem_credentials($redirect, '', true); // Failed to connect, Error and request again
			$data = ob_get_contents();
			ob_end_clean();
			if ( ! empty($data) ) {
				include_once( ABSPATH . 'wp-admin/admin-header.php');
				echo $data;
				include( ABSPATH . 'wp-admin/admin-footer.php');
				exit;
			}
			return;
		}

		if ( ! is_object($wp_filesystem) )
			return new WP_Error('fs_unavailable', __('Could not access filesystem.'));

		if ( is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->get_error_code() )
			return new WP_Error('fs_error', __('Filesystem error.'), $wp_filesystem->errors);

		// Get the base plugin folder
		$themes_dir = $wp_filesystem->wp_content_dir() . AI1EC_THEMES_FOLDER . '/';
		if ( empty($themes_dir) )
			return new WP_Error('fs_no_themes_dir', __('Unable to locate WordPress theme directory.'));

		$themes_dir = trailingslashit( $themes_dir );
		$theme_dir = trailingslashit( $themes_dir . $template );

		$deleted = $wp_filesystem->delete($theme_dir, true);

		if ( ! $deleted )
			return new WP_Error('could_not_remove_theme', sprintf(__('Could not fully remove the theme %s.'), $template) );

		return true;
	}

	/**
	 * preview_theme function
	 *
	 * @return void
	 **/
	public function preview_theme() {
		if( ! ( isset( $_GET['ai1ec_template'] ) && isset( $_GET['preview'] ) ) )
			return;

		if( ! current_user_can( 'switch_themes' ) )
			return;

		// Admin Thickbox requests
		if( isset( $_GET['preview_iframe'] ) )
			show_admin_bar( false );

		$_GET['ai1ec_template'] = preg_replace( '|[^a-z0-9_./-]|i', '', $_GET['ai1ec_template'] );

		if( validate_file( $_GET['ai1ec_template'] ) )
			return;

		add_filter( 'ai1ec_template', array( &$this, '_preview_theme_template_filter' ) );

		if( isset( $_GET['ai1ec_stylesheet'] ) ) {
			$_GET['ai1ec_stylesheet'] = preg_replace( '|[^a-z0-9_./-]|i', '', $_GET['ai1ec_stylesheet'] );
			if( validate_file( $_GET['ai1ec_stylesheet'] ) )
				return;
			add_filter( 'ai1ec_stylesheet', array( &$this, '_preview_theme_stylesheet_filter' ) );
		}

		// Prevent theme mods to current theme being used on theme being previewed
		add_filter( 'pre_option_mods_' . get_current_theme(), '__return_empty_array' );

		ob_start( array( &$this, 'preview_theme_ob_filter' ) );
	}

	/**
	 * preview_theme_ob_filter function
	 *
	 * Callback function for ob_start() to capture all links in the theme.
	 *
	 * @param string $content
	 * @return string
	 */
	function preview_theme_ob_filter( $content ) {
		return preg_replace_callback( "|(<a.*?href=([\"']))(.*?)([\"'].*?>)|",
		                              array( &$this, 'preview_theme_ob_filter_callback' ),
		                              $content );
	}

	/**
	 * preview_theme_ob_filter_callback function
	 *
	 * Manipulates preview theme links in order to control and maintain location.
	 *
	 * Callback function for preg_replace_callback() to accept and filter matches.
	 *
	 * @param array $matches
	 * @return string
	 */
	function preview_theme_ob_filter_callback( $matches ) {
		if( strpos( $matches[4], 'onclick' ) !== false )
			$matches[4] = preg_replace( '#onclick=([\'"]).*?(?<!\\\)\\1#i', '', $matches[4] );

		if( ( false !== strpos( $matches[3], '/wp-admin/' ) ) ||
		    ( false !== strpos( $matches[3], '://' ) && 0 !== strpos( $matches[3], home_url() ) ) ||
		    ( false !== strpos( $matches[3], '/feed/' ) ) ||
		    ( false !== strpos( $matches[3], '/trackback/' ) )
		)
			return $matches[1] . "#$matches[2] onclick=$matches[2]return false;" . $matches[4];

		$query_arg = array(
			'preview'          => 1,
			'ai1ec_template'   => $_GET['ai1ec_template'],
			'ai1ec_stylesheet' => @$_GET['ai1ec_stylesheet']
		);

		if( isset( $_GET['preview_iframe'] ) )
			$query_arg['preview_iframe'] = (int) $_GET['preview_iframe'];

		$link = add_query_arg( $query_arg, $matches[3] );

		if( 0 === strpos( $link, 'preview=1' ) )
			$link = "?$link";

		return $matches[1] . esc_attr( $link ) . $matches[4];
	}

	/**
	 * _preview_theme_template_filter function
	 *
	 * Private function to modify the current template when previewing a theme
	 *
	 * @return string
	 */
	public function _preview_theme_template_filter() {
		return isset( $_GET['ai1ec_template'] ) ? $_GET['ai1ec_template'] : '';
	}

	/**
	 * _preview_theme_stylesheet_filter function
	 *
	 * Private function to modify the current stylesheet when previewing a theme
	 *
	 * @return string
	 */
	public function _preview_theme_stylesheet_filter() {
		return isset( $_GET['ai1ec_stylesheet'] ) ? $_GET['ai1ec_stylesheet'] : '';
	}

	/**
   * Returns the root path of ai1ec-themes.
	 *
	 * @return string
	 **/
	public function template_root_path( $template ) {
		return AI1EC_THEMES_ROOT . '/' . $template;
	}

	/**
	 * Returns the root URL of ai1ec-themes.
	 *
	 * @return string
	 **/
	public function template_root_url( $template ) {
		return AI1EC_THEMES_URL . '/' . $template;
	}

  /**
   * Returns the path to the active calendar theme.
   *
   * @return string
   */
  public function active_template_path() {
    return apply_filters(
      'ai1ec_template_root_path',
      apply_filters(
        'ai1ec_template',
        get_option( 'ai1ec_template', AI1EC_DEFAULT_THEME_NAME )
      )
    );
  }

  /**
   * Returns the URL to the active calendar theme.
   *
   * @return string
   */
  public function active_template_url() {
    return apply_filters(
      'ai1ec_template_root_url',
      apply_filters(
        'ai1ec_template',
        get_option( 'ai1ec_template', AI1EC_DEFAULT_THEME_NAME )
      )
    );
  }

	/**
	 * Returns whether core theme files were able to be copied over to wp-content.
	 * Checks if they are already there, and if they are not, tries to copy them
	 * over. If they can't be copied, returns false.
	 *
	 * @return bool
	 **/
	public function are_themes_available() {
		//  are themes folder and Vortex theme available?
		if( @is_dir( AI1EC_THEMES_ROOT ) === true && @is_dir( AI1EC_DEFAULT_THEME_PATH ) === true ) {
			return true;
		} else {
			// try to create AI1EC_THEMES_ROOT
			if( ! @mkdir( AI1EC_THEMES_ROOT ) )
				return false;

			// copy themes-ai1ec from plugin's root to wp-content's themes root
			$this->copy_directory( AI1EC_PATH . '/' . AI1EC_THEMES_FOLDER, AI1EC_THEMES_ROOT );

			if( @is_dir( AI1EC_THEMES_ROOT ) === false || @is_dir( AI1EC_DEFAULT_THEME_PATH ) === false )
				return false;

			// Update installed core themes version.
			update_option( 'ai1ec_themes_version', AI1EC_THEMES_VERSION );
		}
		return true;
	}

	/**
	 * Returns whether core theme files need to be updated (only if core theme
	 * files exist in the first place, checked using above function).
	 *
	 * @return bool
	 */
	public function are_themes_outdated() {
		if ( ! $this->are_themes_available() ) {
			return FALSE;
		}
		if ( get_option( 'ai1ec_themes_version', 1 ) >= AI1EC_THEMES_VERSION ) {
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * copy_directory function
	 *
	 * @return void
	 **/
	private function copy_directory( $source, $destination ) {
		if( is_dir( $source ) ) {
			@mkdir( $destination );
			$directory = dir( $source );
			while( FALSE !== ( $readdirectory = $directory->read() ) ) {
				if( $readdirectory == '.' || $readdirectory == '..' ) {
					continue;
				}
				$PathDir = $source . '/' . $readdirectory;
				if( is_dir( $PathDir ) ) {
					$this->copy_directory( $PathDir, $destination . '/' . $readdirectory );
					continue;
				}
				copy( $PathDir, $destination . '/' . $readdirectory );
			}

			$directory->close();
		} else {
			copy( $source, $destination );
		}
	}

	/**
	 * Checks if themes are installed.
	 *
	 * @return void
	 */
	function check_themes() {
		global $wp_filesystem;

		if ( empty($template) )
			return false;

		ob_start();
		if ( empty( $redirect ) )
			$redirect = wp_nonce_url(
				admin_url( AI1EC_THEME_SELECTION_BASE_URL ) .
				"&amp;action=delete&amp;ai1ec_template=$template", 'delete-ai1ec_theme_' . $template
			);
		if ( false === ($credentials = request_filesystem_credentials($redirect)) ) {
			$data = ob_get_contents();
			ob_end_clean();
			if ( ! empty($data) ){
				include_once( ABSPATH . 'wp-admin/admin-header.php');
				echo $data;
				include( ABSPATH . 'wp-admin/admin-footer.php');
				exit;
			}
			return;
		}

		if ( ! WP_Filesystem($credentials) ) {
			request_filesystem_credentials($redirect, '', true); // Failed to connect, Error and request again
			$data = ob_get_contents();
			ob_end_clean();
			if ( ! empty($data) ) {
				include_once( ABSPATH . 'wp-admin/admin-header.php');
				echo $data;
				include( ABSPATH . 'wp-admin/admin-footer.php');
				exit;
			}
			return;
		}

		if ( ! is_object($wp_filesystem) )
			return new WP_Error('fs_unavailable', __('Could not access filesystem.'));

		if ( is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->get_error_code() )
			return new WP_Error('fs_error', __('Filesystem error.'), $wp_filesystem->errors);

		// Get the base plugin folder
		$themes_dir = $wp_filesystem->wp_content_dir() . AI1EC_THEMES_FOLDER . '/';
		if ( empty($themes_dir) )
			return new WP_Error('fs_no_themes_dir', __('Unable to locate WordPress theme directory.'));

		$themes_dir = trailingslashit( $themes_dir );
		$theme_dir = trailingslashit( $themes_dir . $template );

		$deleted = $wp_filesystem->delete($theme_dir, true);

		if ( ! $deleted )
			return new WP_Error('could_not_remove_theme', sprintf(__('Could not fully remove the theme %s.'), $template) );

		return true;
	}


	/**
	 * Register Update Calendar Themes page in wp-admin.
	 */
	function register_theme_updater() {
		// Add menu item for theme update page, but without the actual menu item
		// by removing it again right away.
		add_submenu_page(
			'themes.php',
			__( 'Update Calendar Themes', AI1EC_PLUGIN_NAME ),
			__( 'Update Calendar Themes', AI1EC_PLUGIN_NAME ),
			'install_themes',
			AI1EC_PLUGIN_NAME . '-update-themes',
			array( &$this, 'update_core_themes' )
		);
		remove_submenu_page( 'themes.php', AI1EC_PLUGIN_NAME . '-update-themes' );
	}

	/**
	 * Called by the Update Calendar Themes page. Updates core themes with any
	 * files that have changed since the last time a theme update has run.
	 */
	function update_core_themes() {
		global $ai1ec_view_helper;

		$src_dir = trailingslashit( AI1EC_PATH . '/' . AI1EC_THEMES_FOLDER . '/' );
		$dest_dir = trailingslashit( AI1EC_THEMES_ROOT . '/' );

		// Get previous version of core themes.
		$active_version = get_option( 'ai1ec_themes_version', 1 );

		$files = array();
		if ( $active_version < 2 ) {
			// Copy over files updated between AI1EC 1.6 and 1.7 RC1
			$files[] = 'gamma/style.css';
			$files[] = 'plana/style.css';
			$files[] = 'umbra/css/calendar.css';
			$files[] = 'umbra/css/event.css';
			$files[] = 'umbra/css/general.css';
			$files[] = 'umbra/less/build-css.sh';
			$files[] = 'umbra/style.css';
			$files[] = 'vortex/agenda.php';
			$files[] = 'vortex/agenda-widget.php';
			$files[] = 'vortex/css/calendar.css';
			$files[] = 'vortex/css/event.css';
			$files[] = 'vortex/css/general.css';
			$files[] = 'vortex/css/print.css';
			$files[] = 'vortex/js/bootstrap-dropdown.js';
			$files[] = 'vortex/js/bootstrap-tooltip.js';
			$files[] = 'vortex/js/general.min.js';
			$files[] = 'vortex/less/build-css.sh';
			$files[] = 'vortex/less/calendar.less';
			$files[] = 'vortex/less/event.less';
			$files[] = 'vortex/less/general.less';
			$files[] = 'vortex/less/mixins-custom.less';
			$files[] = 'vortex/less/variables.less';
			$files[] = 'vortex/month.php';
			$files[] = 'vortex/oneday.php';
			$files[] = 'vortex/style.css';
			$files[] = 'vortex/week.php';
		}

		if ( $active_version < 3 ) {
			// Copy over files updated between AI1EC 1.7 RC1 and AI1EC 1.7 RC2
			$files[] = 'vortex/js/calendar.min.js';
			$files[] = 'vortex/js/calendar.js';
		}

		if ( $active_version < 4 ) {
			// Copy over files updated between AI1EC 1.7 RC2 and AI1EC 1.7 RC3
			$files[] = 'vortex/js/calendar.min.js';
			$files[] = 'vortex/js/calendar.js';
		}

		// Remove duplicates.
		$files = array_unique( $files );

		$errors = array();
		foreach ( $files as $file ) {
			if ( ! copy( $src_dir . $file, $dest_dir . $file ) ) {
				$errors[] = sprintf(
					__( '<div class="error"><p><strong>There was an error updating one of the files.</strong> Please FTP to your web server and manually copy <pre>%s</pre> to <pre>%s</pre></p></div>', AI1EC_PLUGIN_NAME ),
					$src_dir . $file,
					$dest_dir . $file
				);
			}
		}

		update_option( 'ai1ec_themes_version', AI1EC_THEMES_VERSION );

		if ( $errors ) {
			$msg = __( '<div id="message" class="error"><h3>Errors occurred while we tried to update your core Calendar Themes.</h3><p><strong>Please follow any instructions listed below or your calendar may malfunction:</strong></p></div>', AI1EC_PLUGIN_NAME );
		}
		else {
			$msg = __( '<div id="message" class="updated"><h3>Your core Calendar Themes were updated successfully.</h3></div>', AI1EC_PLUGIN_NAME );
		}

		$args = array(
			'msg' => $msg,
			'errors' => $errors,
		);

		$ai1ec_view_helper->display_admin( 'themes-updated.php', $args );
	}

  /**
   * Called immediately after WP theme's functions.php is loaded. Load our own
   * theme's functions.php at this time, and the default theme's functions.php.
   */
  function setup_theme() {
    $functions_files = array(
      $this->active_template_path() . '/functions.php',
      AI1EC_DEFAULT_THEME_PATH . '/functions.php',
    );

    $functions_files = array_unique( $functions_files );

    foreach( $functions_files as $file ) {
      if ( file_exists( $file ) ) {
        include( $file );
      }
    }
  }
}
// END class
