<?php
//
//  class-ai1ec-view-helper.php
//  all-in-one-event-calendar
//
//  Created by The Seed Studio on 2011-07-13.
//

/**
 * Ai1ec_View_Helper class
 *
 * @package Helpers
 * @author time.ly
 **/
class Ai1ec_View_Helper {
	/**
	 * _instance class variable
	 *
	 * Class instance
	 *
	 * @var null | object
	 **/
	private static $_instance = NULL;

	/**
	 * Constructor
	 *
	 * Default constructor
	 **/
	private function __construct() { }

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
	 * Enqueue a script from the admin resources directory (app/view/admin/js).
	 *
   * @param string $name Unique identifer for the script
   * @param string $file Filename of the script
   * @param array $deps Dependencies of the script
   * @param bool $in_footer Whether to add the script to the footer of the page
   *
	 * @return void
	 */
	function admin_enqueue_script( $name, $file, $deps = array(), $in_footer = FALSE ) {
		if( ! $file || empty( $file ) ) {
			throw new Ai1ec_File_Not_Provided( "You need to specify a script file." );
		}

		$_file = AI1EC_ADMIN_THEME_JS_PATH . '/' . $file;

		if( ! file_exists( $_file ) ) {
			throw new Ai1ec_File_Not_Found( "The specified file " . $_file . " doesn't exist." );
		} else {
			$file = AI1EC_ADMIN_THEME_JS_URL . '/' . $file;
			wp_enqueue_script( $name, $file, $deps, AI1EC_VERSION, $in_footer );
		}
	}
	/**
	 * Looks in the currently selected theme folder if there is the js file otherwise it falls back to the default template
	 *
	 * @param string $file the file to look for
	 *
	 * @throws Ai1ec_File_Not_Found if the file is not found
	 *
	 * @return string $file the path to the js file
	 */
	public function get_path_of_js_file_to_load( $file ) {
		global $ai1ec_themes_controller;
		// template path
		$active_template_path = $ai1ec_themes_controller->active_template_path();
		// template url
		$active_template_url = $ai1ec_themes_controller->active_template_url();

		// look for the file in the active theme
		$themes_root = array(
				(object) array(
						'path' => $active_template_path . '/' . AI1EC_JS_FOLDER,
						'url'  => $active_template_url . '/' . AI1EC_JS_FOLDER
				),
				(object) array(
						'path' => AI1EC_DEFAULT_THEME_PATH . '/' . AI1EC_JS_FOLDER,
						'url'  => AI1EC_DEFAULT_THEME_URL . '/' . AI1EC_JS_FOLDER
				),
		);

		$file_found = false;

		// look for the file in each theme
		foreach( $themes_root as $theme_root ) {
			// $_file is a local var to hold the value of
			// the file we are looking for
			$_file = $theme_root->path . '/' . $file;
			if( file_exists( $_file ) ) {
				// file is found
				$file_found = true;
				// assign the found file
				$file       = $theme_root->url . '/' . $file;
				// exit the loop;
				break;
			}
		}
		if( $file_found === false ) {
			throw new Ai1ec_File_Not_Found( "The specified file '" . $file . "' doesn't exist." );
		} else {
			return $file;
		}
	}

	/**
   * Enqueue a script from the theme resources directory.
   *
   * @param string $name Unique identifer for the script
   * @param string $file Filename of the script
   * @param array $deps Dependencies of the script
   * @param bool $in_footer Whether to add the script to the footer of the page
   *
	 * @return void
	 */
	function theme_enqueue_script( $name, $file, $deps = array(), $in_footer = FALSE  ) {


		if( ! $file || empty( $file ) ) {
			throw new Ai1ec_File_Not_Provided( "You need to specify a script file." );
		}

		try {
			$file_path = $this->get_path_of_js_file_to_load( $file );
			// Append core themes version to version string to make sure recently
			// updated files are used.
			wp_enqueue_script(
					$name,
					$file_path,
					$deps,
					AI1EC_VERSION . '-' . get_option( 'ai1ec_themes_version', 1 ),
					$in_footer
			);
		} catch ( Ai1ec_File_Not_Found  $e ) {
			throw $e;
		}
	}

	/**
	 * admin_enqueue_style function
	 *
	 * @return void
	 **/
	function admin_enqueue_style( $name, $file, $deps = array() ) {
		if( ! $file || empty( $file ) ) {
			throw new Ai1ec_File_Not_Provided( "You need to specify a style file." );
		}

		$_file = AI1EC_ADMIN_THEME_CSS_PATH . '/' . $file;

		if( ! file_exists( $_file ) ) {
			throw new Ai1ec_File_Not_Found( "The specified file " . $file . " doesn't exist." );
		} else {
			$file = AI1EC_ADMIN_THEME_CSS_URL . '/' . $file;
			wp_enqueue_style( $name, $file, $deps, AI1EC_VERSION );
		}
	}

	/**
	 * theme_enqueue_style function
	 *
	 * @return void
	 **/
	function theme_enqueue_style( $name, $file, $deps = array() ) {
    global $ai1ec_themes_controller;

		if( ! $file || empty( $file ) ) {
			throw new Ai1ec_File_Not_Provided( "You need to specify a style file." );
		}

		// template path
		$active_template_path = $ai1ec_themes_controller->active_template_path();
		// template url
		$active_template_url = $ai1ec_themes_controller->active_template_url();

		// look for the file in the active theme
		$themes_root = array(
			(object) array(
				'path' => $active_template_path . '/' . AI1EC_CSS_FOLDER,
				'url'  => $active_template_url . '/' . AI1EC_CSS_FOLDER
			),
			(object) array(
				'path' => $active_template_path,
				'url'  => $active_template_url
			),
			(object) array(
				'path' => AI1EC_DEFAULT_THEME_PATH . '/' . AI1EC_CSS_FOLDER,
				'url'  => AI1EC_DEFAULT_THEME_URL . '/' . AI1EC_CSS_FOLDER
			),
			(object) array(
				'path' => AI1EC_DEFAULT_THEME_PATH,
				'url'  => AI1EC_DEFAULT_THEME_URL
			),
		);

		$file_found = false;

		// look for the file in each theme
		foreach( $themes_root as $theme_root ) {
			// $_file is a local var to hold the value of
			// the file we are looking for
			$_file = $theme_root->path . '/' . $file;
			if( file_exists( $_file ) ) {
				// file is found
				$file_found = true;
				// assign the found file
				$file       = $theme_root->url . '/' . $file;
				// exit the loop;
				break;
			}
		}

		if( $file_found === false ) {
			throw new Ai1ec_File_Not_Found( "The specified file '" . $file . "' doesn't exist." );
		}
		else {
			// Append core themes version to version string to make sure recently
			// updated files are used.
			wp_enqueue_style(
				$name,
				$file,
				$deps,
				AI1EC_VERSION . '-' . get_option( 'ai1ec_themes_version', 1 )
			);
		}
	}

	/**
	 * display_admin function
	 *
	 * Display the view specified by file $file and passed arguments $args.
	 *
	 * @param string $file
	 * @param array $args
	 *
	 * @return void
	 **/
	function display_admin( $file = false, $args = array() ) {
		if( ! $file || empty( $file ) ) {
			throw new Ai1ec_File_Not_Provided( "You need to specify a view file." );
		}

		$file = AI1EC_ADMIN_THEME_PATH . '/' . $file;

		if( ! file_exists( $file ) ) {
			throw new Ai1ec_File_Not_Found( "The specified view file doesn't exist." );
		} else {
			extract( $args );
			require( $file );
		}
	}

	/**
	 * display_theme function
	 *
	 * Display the view specified by file $file and passed arguments $args.
	 *
	 * @param string $file
	 * @param array $args
	 *
	 * @return void
	 **/
	function display_theme( $file = false, $args = array() ) {
    global $ai1ec_themes_controller;

		if( ! $file || empty( $file ) ) {
			throw new Ai1ec_File_Not_Provided( "You need to specify a view file." );
		}

		// look for the file in the selected theme
		$themes_root = array(
			$ai1ec_themes_controller->active_template_path(),
			AI1EC_DEFAULT_THEME_PATH
		);

		// remove duplicates
		$themes_root = array_unique( $themes_root );

		$file_found = false;

		// look for the file in each theme
		foreach( $themes_root as $theme_root ) {
			// $_file is a local var to hold the value of
			// the file we are looking for
			$_file = $theme_root . '/' . $file;
			if( file_exists( $_file ) ) {
				// file is found
				$file_found = true;
				// assign the found file
				$file       = $_file;
				// exit the loop;
				break;
			}
		}

		if( $file_found === false ) {
			throw new Ai1ec_File_Not_Found( "The specified view file '" . $file . "' doesn't exist." );
		} else {
			extract( $args );
			require( $file );
		}
	}

	/**
	 * display_admin_css function
	 *
	 * Renders the given stylesheet inline. If stylesheet has already been
	 * displayed once before with the same set of $args, does not display
	 * it again.
	 *
	 * @param string $file
	 * @param array $args
	 *
	 * @return void
	 **/
	function display_admin_css( $file = false, $args = array() ) {
		static $displayed = array();

		if( ! $file || empty( $file ) ) {
			throw new Ai1ec_File_Not_Provided( 'You need to specify a css file.' );
		}

		$file = AI1EC_ADMIN_THEME_CSS_PATH . '/' . $file;

		if( isset( $displayed[$file] ) && $displayed[$file] === $args )	// Skip if already displayed
			return;

		if( ! file_exists( $file ) ) {
			throw new Ai1ec_File_Not_Found( "The specified css file doesn't exist." );
		} else {
			$displayed[$file] = $args;	// Flag that we've displayed this file with these args

			extract( $args );
			echo '<style type="text/css">';
			require( $file );
			echo '</style>';
		}
	}

	/**
	 * display_theme_css function
	 *
	 * Renders the given stylesheet inline. If stylesheet has already been
	 * displayed once before with the same set of $args, does not display
	 * it again.
	 *
	 * @param string $file
	 * @param array $args
	 *
	 * @return void
	 **/
	function display_theme_css( $file = false, $args = array() ) {
    global $ai1ec_themes_controller;
		static $displayed = array();

		if( ! $file || empty( $file ) ) {
			throw new Ai1ec_File_Not_Provided( 'You need to specify a CSS file.' );
		}

		// look for the file in the selected theme
		$themes_root = array(
			$ai1ec_themes_controller->active_template_path() . '/' . AI1EC_THEME_CSS_FOLDER,
			AI1EC_DEFAULT_THEME_PATH . '/' . AI1EC_THEME_CSS_FOLDER
		);

		// remove duplicates
		$themes_root = array_unique( $themes_root );

		$file_found = false;

		// look for the file in each theme
		foreach( $themes_root as $theme_root ) {
			// $_file is a local var to hold the value of
			// the file we are looking for
			$_file = $theme_root . '/' . $file;
			if( file_exists( $_file ) ) {
				// file is found
				$file_found = true;
				// assign the found file
				$file       = $_file;
				// exit the loop;
				break;
			}
		}

		if( isset( $displayed[$file] ) && $displayed[$file] === $args )	// Skip if already displayed
			return;

		if( ! file_exists( $file ) ) {
			throw new Ai1ec_File_Not_Found( "The specified CSS file doesn't exist." );
		} else {
			$displayed[$file] = $args;	// Flag that we've displayed this file with these args

			extract( $args );
			echo '<style type="text/css">';
			require( $file );
			echo '</style>';
		}
	}

	/**
	 * display_admin_js function
	 *
	 * Renders the given script inline. If script has already been displayed
	 * once before with the same set of $args, does not display it again.
	 *
	 * @param string $file
	 * @param array $args
	 *
	 * @return void
	 **/
	function display_admin_js( $file = false, $args = array() ) {
		static $displayed = array();

		if( ! $file || empty( $file ) ) {
			throw new Ai1ec_File_Not_Provided( "You need to specify a js file." );
		}

		$file = AI1EC_ADMIN_THEME_JS_PATH . '/' . $file;

		if( $displayed[$file] === $args)	// Skip if already displayed
			return;

		if( ! file_exists( $file ) ) {
			throw new Ai1ec_File_Not_Found( "The specified js file doesn't exist." );
		} else {
			$displayed[$file] = $args;	// Flag that we've displayed this file with these args

			extract( $args );
			echo '<script type="text/javascript" charset="utf-8">';
			echo '/* <![CDATA[ */';
			require( $file );
			echo '/* ]]> */';
			echo '</script>';
		}
	}
	/**
	 * display_theme_js function
	 *
	 * Renders the given script inline. If script has already been displayed
	 * once before with the same set of $args, does not display it again.
	 *
	 * @param string $file
	 * @param array $args
	 *
	 * @return void
	 **/
	function display_theme_js( $file = false, $args = array() ) {
    global $ai1ec_themes_controller;
		static $displayed = array();

		if( ! $file || empty( $file ) ) {
			throw new Ai1ec_File_Not_Provided( "You need to specify a JS file." );
		}

    // look for the file in the selected theme
    $themes_root = array(
      $ai1ec_themes_controller->active_template_path() . '/' . AI1EC_THEME_JS_FOLDER,
      AI1EC_DEFAULT_THEME_PATH . '/' . AI1EC_THEME_JS_FOLDER
    );

    // remove duplicates
    $themes_root = array_unique( $themes_root );

    $file_found = false;

    // look for the file in each theme
    foreach( $themes_root as $theme_root ) {
      // $_file is a local var to hold the value of
      // the file we are looking for
      $_file = $theme_root . '/' . $file;
      if( file_exists( $_file ) ) {
        // file is found
        $file_found = true;
        // assign the found file
        $file       = $_file;
        // exit the loop;
        break;
      }
    }

		if( $displayed[$file] === $args)	// Skip if already displayed
			return;

		if( ! file_exists( $file ) ) {
			throw new Ai1ec_File_Not_Found( "The specified JS file doesn't exist." );
		} else {
			$displayed[$file] = $args;	// Flag that we've displayed this file with these args

			extract( $args );
			echo '<script type="text/javascript" charset="utf-8">';
			echo '/* <![CDATA[ */';
			require( $file );
			echo '/* ]]> */';
			echo '</script>';
		}
	}

	/**
	 * get_admin_view function
	 *
	 * Return the output of a view as a string rather than output to response.
	 *
	 * @param string $file
	 * @param array $args
	 *
	 * @return void
	 **/
	function get_admin_view( $file = false, $args = array() ) {
		ob_start();
		$this->display_admin( $file, $args );
		return ob_get_clean();
	}

	/**
	 * get_theme_view function
	 *
	 * Return the output of a view in the theme as a string rather than output to response.
	 *
	 * @param string $file
	 * @param array $args
	 *
	 * @return void
	 **/
	function get_theme_view( $file = false, $args = array() ) {
		ob_start();
		$this->display_theme( $file, $args );
		return ob_get_clean();
	}

	/**
	 * get_admin_img_url function
	 *
	 * @return string
	 **/
	public function get_admin_img_url( $file ) {
		if( ! $file || empty( $file ) ) {
			throw new Ai1ec_File_Not_Provided( "You need to specify an image file." );
		}

		$_file = AI1EC_ADMIN_THEME_IMG_PATH . '/' . $file;

		if( ! file_exists( $_file ) ) {
			throw new Ai1ec_File_Not_Found( "The specified file " . $_file . " doesn't exist." );
		} else {
			$file = AI1EC_ADMIN_THEME_IMG_URL . '/' . $file;
			return $file;
		}
	}

	/**
	 * get_theme_img_url function
	 *
	 * @return string
	 **/
	public function get_theme_img_url( $file ) {
    global $ai1ec_themes_controller;

		if( ! $file || empty( $file ) ) {
			throw new Ai1ec_File_Not_Provided( "You need to specify a style file." );
		}

    // template path
    $active_template_path = $ai1ec_themes_controller->active_template_path();
    // template url
    $active_template_url = $ai1ec_themes_controller->active_template_url();

    // look for the file in the active theme
    $themes_root = array(
      (object) array(
        'path' => $active_template_path . '/' . AI1EC_IMG_FOLDER,
        'url'  => $active_template_url . '/' . AI1EC_IMG_FOLDER
      ),
      (object) array(
        'path' => AI1EC_DEFAULT_THEME_PATH . '/' . AI1EC_IMG_FOLDER,
        'url'  => AI1EC_DEFAULT_THEME_URL . '/' . AI1EC_IMG_FOLDER
      ),
    );

		$file_found = false;

		// look for the file in each theme
		foreach( $themes_root as $theme_root ) {
			// $_file is a local var to hold the value of
			// the file we are looking for
			$_file = $theme_root->path . '/' . $file;
			if( file_exists( $_file ) ) {
				// file is found
				$file_found = true;
				// assign the found file
				$file       = $theme_root->url . '/' . $file;
				// exit the loop;
				break;
			}
		}

		if( $file_found === false ) {
			throw new Ai1ec_File_Not_Found( "The specified file '" . $file . "' doesn't exist." );
		} else {
			return $file;
		}
	}

	/**
	 * json_response function
	 *
	 * Utility for properly outputting JSON data as an AJAX response.
	 *
	 * @param array $data
	 *
	 * @return void
	 **/
	function json_response( $data ) {
		header( 'Cache-Control: no-cache, must-revalidate' );
		header( 'Pragma: no-cache' );
		header( 'Content-type: application/json' );

		// Output JSON-encoded result and quit
		echo json_encode( $data );
		exit;
	}

}
// END class
