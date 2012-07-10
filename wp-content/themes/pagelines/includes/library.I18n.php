<?php
/*
	Localize this theme!
*/
load_theme_textdomain('pagelines', PAGELINES_LANGUAGE_DIR);
$locale = get_locale();
$locale_file = PAGELINES_LANGUAGE_DIR . "/$locale.php";
if ( is_readable( $locale_file ) )
	require_once( $locale_file );

/* Uncomment this to test your localization, make sure to enter the right language code.
add_filter('locale','test_localization');

/**
 *
 * @TODO document
 *
 */
function test_localization( $locale ) {
	return "nl_NL";
}
/**/
