<?php 

/**
 * Define framework version
 */
define( 'CORE_VERSION', get_theme_mod( 'pagelines_version', pl_get_theme_data( get_template_directory(), 'Version' ) ) );
define( 'CHILD_VERSION', get_theme_mod( 'pagelines_child_version', pl_get_theme_data( get_stylesheet_directory(), 'Version' ) ) );

/**
 * Set Theme Name
 */
$theme = 'PageLines';

define('CORE_LIB', PL_INCLUDES); // Deprecated, but used in bbPress forum < 1.2.3

define('THEMENAME', $theme);
define('CHILDTHEMENAME', get_option('stylesheet'));

define('NICETHEMENAME', pl_get_theme_data( get_template_directory(), 'Name' ) );
define('NICECHILDTHEMENAME',  pl_get_theme_data( get_stylesheet_directory(), 'Name' ) );


define('PARENT_DIR', get_template_directory());
define('CHILD_DIR', get_stylesheet_directory());

define('PARENT_URL', get_template_directory_uri());
define('CHILD_URL', get_stylesheet_directory_uri());
define('CHILD_IMAGES', CHILD_URL . '/images');

/**
 * Define Settings Constants for option DB storage
 */
define( 'PAGELINES_SETTINGS', apply_filters( 'pagelines_settings_field', 'pagelines-settings-two' ));
define( 'PAGELINES_EXTENSION', apply_filters( 'pagelines_settings_extension', 'pagelines-extension' ));
define( 'PAGELINES_ACCOUNT', apply_filters( 'pagelines_settings_account', 'pagelines-account' ));
define( 'PAGELINES_SPECIAL', apply_filters( 'pagelines_settings_special', 'pagelines-special' ));
define( 'PAGELINES_TEMPLATES', apply_filters( 'pagelines_settings_templates', 'pagelines-templates' ));
define( 'PAGELINES_TEMPLATE_MAP', apply_filters( 'pagelines_settings_map', 'pagelines-template-map-two' ));

/**
 * Active Integrations (adds options in core)
 */
define( 'PAGELINES_INTEGRATIONS', 'pagelines-integrations-handling' );


/**
 * Legacy Settings Fields >> ALLOWS FOR REVERT
 */
define( 'PAGELINES_SETTINGS_LEGACY', 'pagelines-settings' );
define( 'PAGELINES_TEMPLATE_MAP_LEGACY', 'pagelines_template_map' );



/**
 * Define PL Admin Paths
 */
define( 'PL_ADMIN', get_template_directory() . '/admin' );
define( 'PL_ADMIN_URI', PARENT_URL . '/admin' );
define( 'PL_ADMIN_CSS', PL_ADMIN_URI . '/css' );
define( 'PL_ADMIN_JS', PL_ADMIN_URI . '/js' );
define( 'PL_ADMIN_IMAGES', PL_ADMIN_URI . '/images' );
define( 'PL_ADMIN_ICONS', PL_ADMIN_IMAGES . '/icons' );

define('PL_MAIN_DASH', 'PageLines-Admin');
define('PL_ADMIN_STORE_SLUG', 'pagelines_extend');
define('PL_SPECIAL_OPTS_SLUG', 'pagelines_special');


define('PL_SETTINGS_SLUG', 'pagelines');
define('PL_SETTINGS_URL', 'admin.php?page='.PL_SETTINGS_SLUG);
define('PL_IMPORT_EXPORT_URL', 'admin.php?page='.PL_MAIN_DASH);
define('PL_DASH_URL', 'admin.php?page='.PL_MAIN_DASH);
define('PL_ACCOUNT_URL', 'admin.php?page='.PL_MAIN_DASH.'&rand='.rand().'#Your_Account'); // rand forces page reload
define('PL_ADMIN_STORE_URL', 'admin.php?page='.PL_ADMIN_STORE_SLUG);
define('PL_TEMPLATE_SETUP_URL', 'admin.php?page=pagelines_templates');
define('PL_SPECIAL_OPTS_URL', 'admin.php?page=pagelines_special');



/**
 * Define theme path constants
 */
define('PL_SECTIONS', get_template_directory() . '/sections');

/**
 * Define web constants
 */
define('SECTION_ROOT', PARENT_URL . '/sections');

/**
 * Define theme web constants
 */
define('PL_CSS', PARENT_URL . '/css');
define('PL_JS', PARENT_URL . '/js');
define('PL_IMAGES', PARENT_URL . '/images');

/**
 * Define Extension Constants
 */

define( 'EXTEND_CHILD_DIR', WP_PLUGIN_DIR . '/pagelines-customize' );
define( 'EXTEND_CHILD_URL', plugins_url( 'pagelines-customize' ) );
define( 'EXTEND_UPDATE', 'pagelines_theme_update' );

define( 'PL_EXTEND_DIR', WP_PLUGIN_DIR . '/pagelines-sections');
define( 'PL_EXTEND_URL', plugins_url( 'pagelines-sections' ) );
define( 'PL_EXTEND_INIT', WP_PLUGIN_DIR . '/pagelines-sections/pagelines-sections.php');
define( 'PL_EXTEND_STYLE', EXTEND_CHILD_URL . '/style.css' );
define( 'PL_EXTEND_STYLE_PATH', EXTEND_CHILD_DIR . '/style.css' );
define( 'PL_EXTEND_FUNCTIONS', EXTEND_CHILD_DIR . '/functions.php' );
define( 'PL_EXTEND_THEMES_DIR', WP_CONTENT_DIR .'/themes/' );
define( 'PL_EXTEND_SECTIONS_PLUGIN', 'pagelines-sections.php' );
define( 'PL_STORE_URL', 'http://www.pagelines.com/store' );
define( 'CORE_LESS', PARENT_DIR . '/less' );

if ( is_multisite() && ! is_super_admin() )
	define( 'EXTEND_NETWORK', true);
else
	define( 'EXTEND_NETWORK', false);


/**
 * Define API Constants
 */
define( 'PL_API', 'www.pagelines.com/api/');
define( 'PL_API_FETCH', 'http://www.pagelines.com/api/' );
define( 'PL_API_CDN', 'http://cdn.pagelines.com/api/' );


/**
 * Define language constants
 */
$lang = ( is_dir( EXTEND_CHILD_DIR . '/language' ) ) ? EXTEND_CHILD_DIR . '/language' : get_template_directory() . '/language';
define( 'PAGELINES_LANGUAGE_DIR', $lang );

/**
 * Functional Singletons - Used to work around hooks/filters
 */
$GLOBALS['pagelines_user_pages'] = array();

/**
 * Pro/Free Version Variables
 */
define( 'VPRO_NAME','PageLines Framework' );
define( 'VPRO_TOUR','http://www.pagelines.com/tour/' );
define( 'VPRO_PRICING','http://www.pagelines.com/pricing/' );
define( 'ADD_PLUS_PRO', 'https://www.pagelines.com/launchpad/add_pro_plus' );
define( 'ADD_PLUS_DEV', 'https://www.pagelines.com/launchpad/add_dev_plus' );
define( 'ADD_PLUS', 'https://www.pagelines.com/launchpad/add_plus' );
define( 'PL_SIGNUP', 'https://www.pagelines.com/launchpad/signup.php?price_group=-1000&hide_paysys=stripe' );
