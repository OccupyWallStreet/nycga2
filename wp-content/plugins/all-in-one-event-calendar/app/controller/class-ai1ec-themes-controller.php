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
	 * Register Update Calendar Themes page in wp-admin.
	 */
	function register_theme_updater() {
		// Add menu item for theme update page, but without the actual menu item
		// by removing it again right away.
		add_submenu_page(
			'themes.php',
			__( 'Update Core Calendar Files', AI1EC_PLUGIN_NAME ),
			__( 'Update Core Calendar Files', AI1EC_PLUGIN_NAME ),
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
			$files[] = 'vortex/agenda.php';
			$files[] = 'vortex/agenda-widget.php';
			$files[] = 'vortex/js/bootstrap-dropdown.js';
			$files[] = 'vortex/js/bootstrap-tooltip.js';
			$files[] = 'vortex/js/general.min.js';
			$files[] = 'vortex/css/calendar.css';
			$files[] = 'vortex/css/event.css';
			$files[] = 'vortex/css/general.css';
			$files[] = 'vortex/css/print.css';
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
