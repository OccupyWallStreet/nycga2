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
			'edit.php?post_type=' . AI1EC_POST_TYPE,
			__( 'Update Core Calendar Files', AI1EC_PLUGIN_NAME ),
			__( 'Update Core Calendar Files', AI1EC_PLUGIN_NAME ),
			'install_themes',
			AI1EC_PLUGIN_NAME . '-update-themes',
			array( &$this, 'update_core_themes' )
		);
		remove_submenu_page(
			'edit.php?post_type=' . AI1EC_POST_TYPE,
			AI1EC_PLUGIN_NAME . '-update-themes'
		);
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

		if ( $active_version < 2 ) {
			// Copy over files updated between AI1EC 1.6 and 1.7 RC1
			$files[] = 'vortex/agenda.php';
			$files[] = 'vortex/agenda-widget.php';
			$files[] = 'vortex/js/bootstrap-dropdown.js';
			$files[] = 'vortex/js/bootstrap-tooltip.js';
			$files[] = 'vortex/js/general.min.js';
			$files[] = 'vortex/css/calendar.css';
			$files[] = 'vortex/css/event.css';
			$files[] = 'vortex/css/style.css';
			$files[] = 'vortex/css/print.css';
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
			// Copy over files updated between AI1EC 1.7 RC3 and AI1EC 1.8 RC3
			$files[] = 'vortex/agenda-widget.php';
			$files[] = 'vortex/calendar.php';
			$files[] = 'vortex/css/calendar.css';
			$files[] = 'vortex/css/event.css';
			$files[] = 'vortex/event-excerpt.php';
			$files[] = 'vortex/event-multi.php';
			$files[] = 'vortex/event-single.php';
			$files[] = 'vortex/style.css';
		}

		if ( $active_version < 6 ) {
			// Copy over files updated between AI1EC 1.8 RC3 and AI1EC 1.8.2
			$files[] = 'vortex/event-excerpt.php';
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
			$msg = __( '<div id="message" class="error"><h3>Errors occurred while we tried to update your core calendar files.</h3><p><strong>Please follow any instructions listed below or your calendar may malfunction:</strong></p></div>', AI1EC_PLUGIN_NAME );
		}
		else {
			$msg = __( '<div id="message" class="updated"><h3>Your core calendar files were updated successfully.</h3></div>', AI1EC_PLUGIN_NAME );
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
