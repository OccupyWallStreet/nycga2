<?php

// Translations can be filed in the /lang/ directory
load_theme_textdomain( 'themejunkie', TEMPLATEPATH . '/lang' );

require_once(TEMPLATEPATH . '/includes/sidebar-init.php');
require_once(TEMPLATEPATH . '/includes/custom-functions.php'); 
require_once(TEMPLATEPATH . '/includes/post-thumbnails.php');

require_once(TEMPLATEPATH . '/includes/theme-posttypes.php');
require_once(TEMPLATEPATH . '/includes/theme-portfoliometa.php');
require_once(TEMPLATEPATH . '/includes/theme-slidermeta.php');

// require_once(TEMPLATEPATH . '/includes/theme-comments.php');

require_once(TEMPLATEPATH . '/includes/theme-options.php');
require_once(TEMPLATEPATH . '/includes/theme-widgets.php');

require_once(TEMPLATEPATH . '/functions/theme_functions.php'); 
require_once(TEMPLATEPATH . '/functions/admin_functions.php');

// Uncomment this to test your localization, make sure to enter the right language code.
// function test_localization( $locale ) {
// 	return "nl_NL";
// }
// add_filter('locale','test_localization');

?>
