<?php
/**
 * Deprecated functions
 *
 * @author		Simon Prosser
 * @copyright	2011 PageLines
 */

/**
 * pagelines_register_section()
 *
 * @since 1.0
 * @deprecated 2.0
 * @deprecated Sections are now autoloaded and registered by the framework.
 */
function pagelines_register_section() {
	_deprecated_function( __FUNCTION__, '2.0', 'the CHILDTHEME/sections/ folder' );
	return;
}

/**
 * cmath()
 *
 * @since 1.0
 * @deprecated 2.0
 * @deprecated A more useful function name
 */
function cmath( $color ) {
	_deprecated_function( __FUNCTION__, '2.0', 'loadmath' );
	return new PageLinesColor( $color );
}

function pl_get_theme_data( $stylesheet = null, $header = 'Version') {
	
	if ( function_exists( 'wp_get_theme' ) ) {
		return wp_get_theme( basename( $stylesheet ) )->get( $header );
	} else {
		$data = get_theme_data( sprintf( '%s/themes/%s/style.css', WP_CONTENT_DIR, basename( $stylesheet ) ) );	
		return $data[ $header ];
	}
}

function pl_get_themes() {
	
	if ( ! class_exists( 'WP_Theme' ) )
		return get_themes();

	$themes = wp_get_themes();

	foreach ( $themes as $key => $theme ) {
		$theme_data[$key] = array(
			'Name'			=> $theme->get('Name'),
			'URI'			=> $theme->display('ThemeURI', true, false),
			'Description'	=> $theme->display('Description', true, false),
			'Author'		=> $theme->display('Author', true, false),
			'Author Name'	=> $theme->display('Author', false),
			'Author URI'	=> $theme->display('AuthorURI', true, false),
			'Version'		=> $theme->get('Version'),
			'Template'		=> $theme->get('Template'),
			'Status'		=> $theme->get('Status'),
			'Tags'			=> $theme->get('Tags'),
			'Title'			=> $theme->get('Name'),
			'Template'		=> ( '' != $theme->display('Template', false, false) ) ? $theme->display('Template', false, false) : $key,
			'Stylesheet'	=> $key,
			'Stylesheet Files'	=> array(
				0 => sprintf( '%s/style.css' , $theme->get_stylesheet_directory() )
			)
		);
	}

	return $theme_data;	
}
