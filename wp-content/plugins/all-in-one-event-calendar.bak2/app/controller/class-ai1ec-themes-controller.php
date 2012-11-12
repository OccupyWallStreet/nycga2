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
 * @author time.ly
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
	 * are_themes_available function
	 *
	 * Checks if core calendar theme folder is present in wp-content.
	 *
	 * @return bool
	 **/
	public function are_themes_available() {
		//  Are calendar themes folder and Vortex theme present under wp-content ?
		if( @is_dir( AI1EC_THEMES_ROOT ) === true && @is_dir( AI1EC_DEFAULT_THEME_PATH ) === true )
			return true;

		return false;
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
	 * Register Install Calendar Themes page in wp-admin.
	 */
	function register_theme_installer() {
		// Add menu item for theme install page, but remove it using remove_submenu_page
		// to generate a "ghost" page
		add_submenu_page(
			'themes.php',
			__( 'Install Calendar Themes', AI1EC_PLUGIN_NAME ),
			__( 'Install Calendar Themes', AI1EC_PLUGIN_NAME ),
			'install_themes',
			AI1EC_PLUGIN_NAME . '-install-themes',
			array( &$this, 'install_themes' )
		);
		remove_submenu_page( 'themes.php', AI1EC_PLUGIN_NAME . '-install-themes' );
	}

	/**
	 * install_themes function
	 *
	 * @return void
	 **/
	function install_themes() {
		?>
		<div class="wrap">
			<?php
			screen_icon();
			?>
			<h2><?php _e( 'Install Calendar Themes', AI1EC_PLUGIN_NAME ) ?></h2>
		<?php
		// WP_Filesystem figures it out by itself, but the filesystem method may be overriden here
		$method = '';
		$url = wp_nonce_url( AI1EC_INSTALL_THEMES_BASE_URL, AI1EC_PLUGIN_NAME . '-theme-installer' );
		if( false === ( $creds = request_filesystem_credentials( $url, $method, false, false ) ) ) {
			// if we get here, then we don't have credentials yet,
			// but have just produced a form for the user to fill in,
			// so stop processing for now
			return false; // stop the normal page form from displaying
		}

		// now we have some credentials, try to get the wp_filesystem running
		if( ! WP_Filesystem( $creds ) ) {
			// our credentials were no good, ask the user for them again
			request_filesystem_credentials( $url, $method, true, false );
			return false;
		}
		global $wp_filesystem;
		$themes_root = $wp_filesystem->wp_content_dir() . AI1EC_THEMES_FOLDER;
		$result = $wp_filesystem->mkdir( $themes_root );
		if( $result === false ) {
			?>
			<p><?php _e( sprintf( 'Unable to create %s folder', AI1EC_THEMES_ROOT ), AI1EC_PLUGIN_NAME ) ?></p>
			<p><?php _e( sprintf( 'Try to create %s folder manually and then restart the process',
			            AI1EC_THEMES_ROOT ), AI1EC_PLUGIN_NAME ) ?></p>
			</div>
			<?php
			return false;
		}
		$plugin_themes_dir = $wp_filesystem->wp_plugins_dir() . AI1EC_PLUGIN_NAME . DIRECTORY_SEPARATOR . AI1EC_THEMES_FOLDER;
		$result = copy_dir( $plugin_themes_dir, $themes_root );
		if( is_wp_error( $result ) ) {
			?>
			<div id="message" class="error">
				<h3>
					<?php _e( 'Errors occurred while we tried to install your core Calendar Themes', AI1EC_PLUGIN_NAME ) ?>.
				</h3>
				<p>
					<strong>
						<?php _e(
							sprintf( 'Please fix the error listed below or your calendar may malfunction: %s', $result->get_error_message() ),
							AI1EC_PLUGIN_NAME
						) ?>
					</strong>
				</p>
			</div>
			<?php
		} else {
			update_option( 'ai1ec_themes_version', AI1EC_THEMES_VERSION );
			?>
			<div id="message" class="updated"><h3><?php _e( 'Calendar themes were installed successfully', AI1EC_PLUGIN_NAME ) ?>.</h3></div>
			<p>
				<a class="button" href="<?php echo AI1EC_SETTINGS_BASE_URL; ?>">
					<?php _e( 'All-in-One Event Calendar Settings »', AI1EC_PLUGIN_NAME ); ?>
				</a>
			</p>
			<?php
		}
		?>
		</div>
		<?php
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

		$files = array();             // files to copy
		$files_to_delete = array();   // files to delete
		$folders = array();           // folders to copy
		$folders_to_delete = array(); // folders to delete
		$folders_to_make = array();   // folders to make

		$errors = array();
		if ( $active_version < 2 ) {
			// Copy over files updated between AI1EC 1.6 and 1.7 RC1
			$files[] = 'gamma/style.css';
			$files[] = 'plana/style.css';
			$files[] = 'umbra/css/calendar.css';
			$files[] = 'umbra/css/event.css';
			$files[] = 'umbra/css/style.css';
			$files[] = 'umbra/style.css';
			$files[] = 'vortex/agenda.php';
			$files[] = 'vortex/agenda-widget.php';
			$files[] = 'vortex/css/calendar.css';
			$files[] = 'vortex/css/event.css';
			$files[] = 'vortex/css/style.css';
			$files[] = 'vortex/css/print.css';
			$files[] = 'vortex/js/bootstrap-dropdown.js';
			$files[] = 'vortex/js/bootstrap-tooltip.js';
			$files[] = 'vortex/js/general.min.js';
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

		if ( $active_version < 5 ) {
			// Copy over files updated between AI1EC 1.7 RC3 and AI1EC 1.8 RC1
			$files[] = 'vortex/posterboard.php';
			$files[] = 'vortex/functions.php';
			$files[] = 'vortex/js/pages/calendar.js';
			$files[] = 'vortex/img/posterboard-view.png';
			$files[] = 'vortex/style.css';// See comments of the issue  AIOEC-255
			$files_to_delete[] = "vortex/js/bootstrap-dropdown.js";
			$files_to_delete[] = "vortex/js/bootstrap-tooltip.js";
			$files_to_delete[] = "vortex/js/build-js.sh";
			$files_to_delete[] = "vortex/js/calendar.js";
			$files_to_delete[] = "vortex/js/calendar.min.js";
			$files_to_delete[] = "vortex/js/event.js";
			$files_to_delete[] = "vortex/js/event.min.js";
			$files_to_delete[] = "vortex/js/general.js";
			$files_to_delete[] = "vortex/js/general.min.js";
			$files_to_delete[] = "vortex/js/jquery.scrollTo-min.js";
			$files_to_delete[] = "vortex/js/jquery.tablescroll.js";
			$files_to_delete[] = "vortex/js/modernizr.custom.78720.js";
			$folders_to_make[] = "vortex/js/pages";
		}

		if ( $active_version < 6 ) {
			// Copy over files updated between AI1EC 1.8 RC1 and AI1EC 1.8 RC3
			$files[] = 'gamma/style.css';
			$files[] = 'plana/style.css';
			$files[] = 'umbra/css/calendar.css';
			$files[] = 'umbra/css/event.css';
			$files[] = 'umbra/img/ajax-loader.gif';
			$files[] = 'umbra/style.css';
			$files[] = 'vortex/agenda-widget.php';
			$files[] = 'vortex/agenda.php';
			$files[] = 'vortex/calendar.php';
			$files[] = 'vortex/css/calendar.css';
			$files[] = 'vortex/css/print.css';
			$files[] = 'vortex/event-excerpt.php';
			$files[] = 'vortex/event-multi.php';
			$files[] = 'vortex/event-single.php';
			$files[] = 'vortex/img/ajax-loader.gif';
			$files[] = 'vortex/month.php';
			$files[] = 'vortex/style.css';
		}

		// Remove duplicates.
		$files             = array_unique( $files );
		$files_to_delete   = array_unique( $files_to_delete );
		$folders           = array_unique( $folders );
		$folders_to_delete = array_unique( $folders_to_delete );
		$folders_to_make   = array_unique( $folders_to_make );

		// array to hold error notifications to the user while updating the themes
		$errors = array();

		// do we have something to update?
		if( count( $files ) > 0 ||
		    count( $files_to_delete ) > 0 ||
		    count( $folders ) > 0 ||
		    count( $folders_to_delete ) > 0 ||
		    count( $folders_to_make ) > 0 ) {

			// WP_Filesystem figures it out by itself, but the filesystem method may be overriden here
			$method = '';
			$url = wp_nonce_url( AI1EC_UPDATE_THEMES_BASE_URL, AI1EC_PLUGIN_NAME . '-theme-updater' );
			if( false === ( $creds = request_filesystem_credentials( $url, $method, false, false ) ) ) {
				// if we get here, then we don't have credentials yet,
				// but have just produced a form for the user to fill in,
				// so stop processing for now
				return false; // stop the normal page form from displaying
			}

			// now we have some credentials, try to get the wp_filesystem running
			if( ! WP_Filesystem( $creds ) ) {
				// our credentials were no good, ask the user for them again
				request_filesystem_credentials( $url, $method, true, false );
				return false;
			}

			global $wp_filesystem;

			// 1. Create new folders
			foreach ( $folders_to_make as $folder_to_make ) {
				// try to create the folder
				if( FALSE === $wp_filesystem->mkdir( $dest_dir . $folder_to_make ) ) {
					// we were not able to create the folder, notify the user
					$errors[] = sprintf(
						__( '<div class="error"><p><strong>There was an error creating one of the theme folders.</strong> Please FTP to your web server and manually create <pre>%s</pre></p></div>', AI1EC_PLUGIN_NAME ),
						$dest_dir . $folder_to_make
					);
				}
			}

			// 2. Copy folders
			foreach ( $folders as $folder ) {
				// try to copy the folder
				$result = copy_dir( $src_dir . $folder, $dest_dir . $folder );
				if( is_wp_error( $result ) ) {
					// we were not able to copy the folder, notify the user
					$errors[] = sprintf(
						__( '<div class="error"><p><strong>There was an error("%s") while copying theme folders.</strong> Please FTP to your web server and manually copy <pre>%s</pre> to <pre>%s</pre></p></div>', AI1EC_PLUGIN_NAME ),
						$result->get_error_message(),
						$src_dir . $folder,
						$dest_dir . $folder
					);
				}
			}

			// 3. Copy files
			// loop over files
			foreach ( $files as $file ) {
				// copy only files that exist
				if( $wp_filesystem->exists( $src_dir . $file ) ) {
					// was file copied successfully?
					if ( ! $wp_filesystem->copy( $src_dir . $file, $dest_dir . $file, true, FS_CHMOD_FILE ) ) {
						// If copy failed, chmod file to 0644 and try again.
						$wp_filesystem->chmod( $dest_dir . $file, 0644);
						if ( ! $wp_filesystem->copy( $src_dir . $file, $dest_dir . $file, true, FS_CHMOD_FILE ) ) {
							// we were not able to copy the file, notify the user
							$errors[] = sprintf(
								__( '<div class="error"><p><strong>There was an error updating one of the files.</strong> Please FTP to your web server and manually copy <pre>%s</pre> to <pre>%s</pre></p></div>', AI1EC_PLUGIN_NAME ),
								$src_dir . $file,
								$dest_dir . $file
							);
						}
					}
				}
			}

			// 4. Remove folders
			foreach ( $folders_to_delete as $folder_to_delete ) {
				// check if folder exist
				if( $wp_filesystem->is_dir( $dest_dir . $folder_to_delete ) ) {
					// folder actions are always recursive
					$recursive = true;
					// try to delete the folder
					if( FALSE === $wp_filesystem->delete( $dest_dir . $folder_to_delete, $recursive ) ) {
						// If delete failed, chmod folder recursively to 0644 and try again.
						$wp_filesystem->chmod( $dest_dir . $folder_to_delete, 0644, $recursive );
						if( FALSE === $wp_filesystem->delete( $dest_dir . $folder_to_delete, $recursive ) ) {
							// we were not able to remove the folder, notify the user
							$errors[] = sprintf(
									__( '<div class="error"><p><strong>There was an error deleting one of the folders.</strong> Please FTP to your web server and manually delete <pre>%s</pre></p></div>', AI1EC_PLUGIN_NAME ),
									$dest_dir . $folder_to_delete
							);
						}
					}
				}
			}

			// 5. Remove files
			foreach ( $files_to_delete as $file ) {
				// check if file exist
				if( $wp_filesystem->exists( $dest_dir . $file ) ) {
					// try to delete the file
					if( FALSE === $wp_filesystem->delete( $dest_dir . $file ) ) {
						// If delete failed, chmod file to 0644 and try again.
						$wp_filesystem->chmod( $dest_dir . $file, 0644 );
						if( FALSE === $wp_filesystem->delete( $dest_dir . $file ) ) {
							// we were not able to remove the file, notify the user
							$errors[] = sprintf(
									__( '<div class="error"><p><strong>There was an error deleting one of the files.</strong> Please FTP to your web server and manually delete <pre>%s</pre></p></div>', AI1EC_PLUGIN_NAME ),
									$dest_dir . $file
							);
						}
					}
				}
			}
		}

		// TODO: Only update the theme version when the update was successful.
		// Otherwise provide a way for the user to review the error log that this
		// update generated and to run the update again, after fixing the reported
		// errors.

		// Update theme version
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
