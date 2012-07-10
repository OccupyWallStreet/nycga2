<?php
/**
 * Upgrader skin and other functions.
 *
 * 
 * @author PageLines
 *
 * @since 2.0.b10
 */
class PageLines_Upgrader_Skin extends WP_Upgrader_Skin {


	/**
	*
	* @TODO document
	*
	*/
	function __construct( $args = array() ) {
		parent::__construct($args);
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function header() { }
	

	/**
	*
	* @TODO document
	*
	*/
	function footer(){ }
	

	/**
	*
	* @TODO document
	*
	*/
	function feedback($string) {
	
		$string = str_replace( 'downloading_package', '', $string );
		$string = str_replace( 'unpack_package', '', $string );
		$string = str_replace( 'installing_package', '', $string );
		$string = str_replace( 'process_failed', '', $string );	
		$string = str_replace( 'process_success', '', $string );
		$string = str_replace( 'parent_theme_search', '', $string );
		$string = str_replace( 'parent_theme_currently_installed', '', $string );
		
		// if anything left, must be a fatal error!
		
		if ( $string )	{			
			if ( strstr( $string, 'Download failed' ) ) {
				_e( "Could not connect to download.<br/><a href='#'>Reload Page</a>", 'pagelines' );
				exit();
			}
			if ( strstr( $string, 'Destination folder already exists' ) ) {
				$string = str_replace( 'Destination folder already exists.', '', $string );
				printf( __('Destination folder already exists %s', 'pagelines' ), $string );
				exit;				
			}
			if ( strstr( $string, 'Could not' ) ) {
				printf( __('Permissions Error<br /> %s', 'pagelines' ), $string );
				exit;	
			}
				// fatal error?
				wp_die( sprintf( '<h1>Fatal error!</h1><strong>%s</strong>', $string ) );
		}
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function after() {}


	/**
	*
	* @TODO document
	*
	*/
	function before() {}
}

class PageLines_Section_Installer extends Plugin_Upgrader {
	

	/**
	*
	* @TODO document
	*
	*/
	function __construct( $args = array() ) {
		parent::__construct($args);
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function install($package) {

		$this->init();
		$this->install_strings();

		$this->run(array(
					'package' => $package,
					'destination' => WP_PLUGIN_DIR,
					'clear_destination' => false, //Do not overwrite files.
					'clear_working' => true,
					'hook_extra' => array()
					));

		if ( ! $this->result || is_wp_error($this->result) )
			return $this->result;

		// Force refresh of plugin update information
		delete_site_transient('update_plugins');

		return true;
	}
}
