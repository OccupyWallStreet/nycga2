<?php
/**
 * Plugin Name: All-in-One Event Calendar by Timely
 * Plugin URI: http://time.ly/
 * Description: A calendar system with month, week, day, agenda, posterboard views, upcoming events widget, color-coded categories, recurrence, and import/export of .ics feeds.
 * Author: Timely
 * Author URI: http://time.ly/
 * Version: 1.8.3-premium
 */
@set_time_limit( 0 );
@ini_set( 'memory_limit',           '256M' );
@ini_set( 'max_input_time',         '-1' );

// ===============
// = Plugin Name =
// ===============
define( 'AI1EC_PLUGIN_NAME',        'all-in-one-event-calendar' );

// ===================
// = Plugin Basename =
// ===================
define( 'AI1EC_PLUGIN_BASENAME',    plugin_basename( __FILE__ ) );

// ==================
// = Plugin Version =
// ==================
define( 'AI1EC_VERSION',            '1.8.3-premium' );

// ====================
// = Database Version =
// ====================
define( 'AI1EC_DB_VERSION',         112 );
// ==========================
// = Bundled themes version =
// ==========================
define( 'AI1EC_THEMES_VERSION',     6 );

// ================
// = Cron Version =
// ================
define( 'AI1EC_CRON_VERSION',       103 );
define( 'AI1EC_N_CRON_VERSION',     101 );
define( 'AI1EC_N_CRON_FREQ',        'daily' );
define( 'AI1EC_U_CRON_VERSION',     105 );
define( 'AI1EC_U_CRON_FREQ',        'hourly' );
define( 'AI1EC_UPDATES_URL',        'http://time.ly/latest-version.json' );

// ===============
// = Plugin Path =
// ===============
define( 'AI1EC_PATH',               dirname( __FILE__ ) );

// ===================
// = CSS Folder name =
// ===================
define( 'AI1EC_CSS_FOLDER',         'css' );

// ==================
// = JS Folder name =
// ==================
define( 'AI1EC_JS_FOLDER',          'js' );

// =====================
// = Image folder name =
// =====================
define( 'AI1EC_IMG_FOLDER',         'img' );

// ============
// = Lib Path =
// ============
define( 'AI1EC_LIB_PATH',           AI1EC_PATH . '/lib' );

// =================
// = Language Path =
// =================
define( 'AI1EC_LANGUAGE_PATH',      AI1EC_PLUGIN_NAME . '/language' );

// ============
// = App Path =
// ============
define( 'AI1EC_APP_PATH',           AI1EC_PATH . '/app' );

// ===================
// = Controller Path =
// ===================
define( 'AI1EC_CONTROLLER_PATH',    AI1EC_APP_PATH . '/controller' );

// ==============
// = Model Path =
// ==============
define( 'AI1EC_MODEL_PATH',         AI1EC_APP_PATH . '/model' );

// =============
// = View Path =
// =============
define( 'AI1EC_VIEW_PATH',          AI1EC_APP_PATH . '/view' );

// ====================
// = Admin Theme Path =
// ====================
define( 'AI1EC_ADMIN_THEME_PATH',   AI1EC_VIEW_PATH . '/admin' );

// ==================
// = Admin theme CSS path =
// ==================
define( 'AI1EC_ADMIN_THEME_CSS_PATH', AI1EC_ADMIN_THEME_PATH . '/' . AI1EC_CSS_FOLDER );

// =======================
// = Admin theme JS path =
// =======================
define( 'AI1EC_ADMIN_THEME_JS_PATH', AI1EC_ADMIN_THEME_PATH . '/' . AI1EC_JS_FOLDER );

// ========================
// = Admin theme IMG path =
// ========================
define( 'AI1EC_ADMIN_THEME_IMG_PATH', AI1EC_ADMIN_THEME_PATH . '/' . AI1EC_IMG_FOLDER );

// ===============
// = Helper Path =
// ===============
define( 'AI1EC_HELPER_PATH',        AI1EC_APP_PATH . '/helper' );

// ==================
// = Exception Path =
// ==================
define( 'AI1EC_EXCEPTION_PATH',     AI1EC_APP_PATH . '/exception' );

// ==============
// = Plugin Url =
// ==============
define( 'AI1EC_URL',                plugins_url( '', __FILE__ ) );

// ==============
// = Images URL =
// ==============
define( 'AI1EC_IMAGE_URL',          AI1EC_URL . '/' . AI1EC_IMG_FOLDER );

// ===========
// = CSS URL =
// ===========
define( 'AI1EC_CSS_URL',            AI1EC_URL . '/' . AI1EC_CSS_FOLDER );

// ==========
// = JS URL =
// ==========
define( 'AI1EC_JS_URL',             AI1EC_URL . '/' . AI1EC_JS_FOLDER );

// ================
// = Admin JS URL =
// ================
define( 'AI1EC_ADMIN_THEME_JS_URL', AI1EC_URL . '/app/view/admin/' . AI1EC_JS_FOLDER );

// =================
// = Admin CSS URL =
// =================
define( 'AI1EC_ADMIN_THEME_CSS_URL', AI1EC_URL . '/app/view/admin/' . AI1EC_CSS_FOLDER );

// =================
// = Admin IMG URL =
// =================
define( 'AI1EC_ADMIN_THEME_IMG_URL', AI1EC_URL . '/app/view/admin/' . AI1EC_IMG_FOLDER );

// =============
// = POST TYPE =
// =============
define( 'AI1EC_POST_TYPE',          'ai1ec_event' );

// =======================================================
// = THEME SELECTION PAGE BASE URL (wrap in admin_url()) =
// =======================================================
define( 'AI1EC_THEME_SELECTION_BASE_URL', 'themes.php?page=' . AI1EC_PLUGIN_NAME . '-themes' );

// ======================================================
// = INSTALL THEMES PAGE BASE URL (wrap in admin_url()) =
// ======================================================
define( 'AI1EC_INSTALL_THEMES_BASE_URL', 'themes.php?page=' . AI1EC_PLUGIN_NAME . '-install-themes' );

// =====================================================
// = UPDATE THEMES PAGE BASE URL (wrap in admin_url()) =
// =====================================================
define( 'AI1EC_UPDATE_THEMES_BASE_URL', 'themes.php?page=' . AI1EC_PLUGIN_NAME . '-update-themes' );

// =====================================================
// = FEED SETTINGS PAGE BASE URL (wrap in admin_url()) =
// =====================================================
define( 'AI1EC_FEED_SETTINGS_BASE_URL', 'edit.php?post_type=' . AI1EC_POST_TYPE . '&page=' . AI1EC_PLUGIN_NAME . '-feeds' );

// ================================================
// = SETTINGS PAGE BASE URL (wrap in admin_url()) =
// ================================================
define( 'AI1EC_SETTINGS_BASE_URL',  'options-general.php?page=' . AI1EC_PLUGIN_NAME . '-settings' );

// ======================
// = Default Theme Name =
// ======================
define( 'AI1EC_DEFAULT_THEME_NAME', 'vortex' );

// =============================
// = Default Theme folder name =
// =============================
define( 'AI1EC_THEMES_FOLDER',      'themes-ai1ec' );

// ========================
// = AI1EC Theme location =
// ========================
define( 'AI1EC_THEMES_ROOT',        WP_CONTENT_DIR . '/' . AI1EC_THEMES_FOLDER );

// ===================
// = AI1EC Theme URL =
// ===================
define( 'AI1EC_THEMES_URL',         WP_CONTENT_URL . '/' . AI1EC_THEMES_FOLDER );

// ======================
// = Default theme path =
// ======================
define( 'AI1EC_DEFAULT_THEME_PATH', AI1EC_THEMES_ROOT . '/' . AI1EC_DEFAULT_THEME_NAME );

// =====================
// = Default theme url =
// =====================
define( 'AI1EC_DEFAULT_THEME_URL',  AI1EC_THEMES_URL . '/' . AI1EC_DEFAULT_THEME_NAME );

// ================
// = RSS FEED URL =
// ================
define( 'AI1EC_RSS_FEED',           'http://time.ly/feed/' );

// ======================================
// = FAKE CATEGORY ID FOR CALENDAR PAGE =
// ======================================
define( 'AI1EC_FAKE_CATEGORY_ID',   -4113473042 ); // Numeric-only 1337-speak of AI1EC_CALENDAR - ID must be numeric

// =============================================
// = DIRECTORY FOR EVENT IMPORTERS PLUGINS     =
// =============================================


// ======================================
// = EVENT IMPORTERS PLUGINS PATH       =
// ======================================
define( 'AI1EC_IMPORT_PLUGIN_PATH',     AI1EC_APP_PATH . DIRECTORY_SEPARATOR . 'plugins' );
// ========================================
// = EVENT IMPORTERS PLUGINS INCLUDE PATH =
// ========================================
define( 'AI1EC_IMPORT_PLUGIN_INC_PATH', AI1EC_IMPORT_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'inc');
// ========================================
// = FACEBOOK PLUGIN INCLUDE PATH         =
// ========================================
define( 'AI1EC_FACEBOOK_PLUGIN_INC_PATH', AI1EC_IMPORT_PLUGIN_INC_PATH . DIRECTORY_SEPARATOR . 'facebook');
// ==============
// = SCRIPT URL =
// ==============
$ai1ec_script_url = get_option( 'home' ) . '/?plugin=' . AI1EC_PLUGIN_NAME;
define( 'AI1EC_SCRIPT_URL',         $ai1ec_script_url );

// ====================================================
// = Convert http:// to webcal:// in AI1EC_SCRIPT_URL =
// =  (webcal:// protocol does not support https://)  =
// ====================================================
$tmp = str_replace( 'http://', 'webcal://', AI1EC_SCRIPT_URL );

// ==============
// = EXPORT URL =
// ==============
define( 'AI1EC_EXPORT_URL',         $tmp . "&controller=ai1ec_exporter_controller&action=export_events&cb=" . rand() );

// ====================
// = SPECIAL SETTINGS =
// ====================

// Set AI1EC_EVENT_PLATFORM to TRUE to turn WordPress into an events-only
// platform. For a multi-site install, setting this to TRUE is equivalent to a
// super-administrator selecting the
//   "Turn this blog into an events-only platform" checkbox
// on the Calendar Settings page of every blog on the network.
// This mode, when enabled on blogs where this plugin is active, hides all
// administrative functions unrelated to events and the calendar (except to
// super-administrators), and sets default WordPress settings appropriate for
// pure event management.
define( 'AI1EC_EVENT_PLATFORM',     FALSE );
// Use alternative cron Version to bypass problems in wp_remot_* calls
if( ! defined( 'ALTERNATE_WP_CRON' ) ) {
	define( 'ALTERNATE_WP_CRON', true );
}
// ====================================
// = Include iCal parsers and helpers =
// ====================================
if( version_compare( PHP_VERSION, '5.3.0' ) >= 0 ) {
	// Parser that requires PHP v5.3.0 or up
	require_once( AI1EC_LIB_PATH . '/iCalcreator-2.12/iCalcreator.class.php' );
} else {
	// Parser that works on PHP versions below 5.3.0
	require_once( AI1EC_LIB_PATH . '/iCalcreator-2.10/iCalcreator.class.php' );
	require_once( AI1EC_LIB_PATH . '/iCalcreator-2.10/iCalUtilityFunctions.class.php' );
}
require_once( AI1EC_LIB_PATH . '/SG_iCal.php' );
require_once( AI1EC_LIB_PATH . '/helpers/SG_iCal_Line.php' );
require_once( AI1EC_LIB_PATH . '/helpers/SG_iCal_Duration.php' );
require_once( AI1EC_LIB_PATH . '/helpers/SG_iCal_Freq.php' );
require_once( AI1EC_LIB_PATH . '/helpers/SG_iCal_Recurrence.php' );
require_once( AI1EC_LIB_PATH . '/helpers/SG_iCal_Parser.php' );
require_once( AI1EC_LIB_PATH . '/helpers/SG_iCal_Query.php' );
require_once( AI1EC_LIB_PATH . '/helpers/SG_iCal_Factory.php' );
// include our global functions
require_once( AI1EC_LIB_PATH . '/global-functions.php' );

// ===============================
// = The autoload function =
// ===============================
function ai1ec_autoload( $class_name )
{
	// Convert class name to filename format.
	$class_name = strtr( strtolower( $class_name ), '_', '-' );
	$paths = array(
		AI1EC_CONTROLLER_PATH,
		AI1EC_MODEL_PATH,
		AI1EC_HELPER_PATH,
		AI1EC_EXCEPTION_PATH,
		AI1EC_LIB_PATH,
		AI1EC_VIEW_PATH,
		AI1EC_ADMIN_THEME_PATH,
		get_option( 'ai1ec_current_theme_path', AI1EC_DEFAULT_THEME_PATH ),
		AI1EC_DEFAULT_THEME_PATH,
		AI1EC_IMPORT_PLUGIN_PATH,
		AI1EC_IMPORT_PLUGIN_INC_PATH,
		AI1EC_FACEBOOK_PLUGIN_INC_PATH
	);

	// remove duplicates from the paths array
	$paths = array_unique( $paths );

	// Search each path for the class.
	foreach( $paths as $path ) {
		if( file_exists( "$path/class-$class_name.php" ) )
			require_once( "$path/class-$class_name.php" );
	}
}
spl_autoload_register( 'ai1ec_autoload' );

// ================================================
// = Disable updates checking for premium version =
// ================================================
function ai1ec_disable_updates( $r, $url ) {
	if ( 0 !== strpos( $url, 'http://api.wordpress.org/plugins/update-check' ) )
		return $r; // Not a plugin update request.

	$plugins = unserialize( $r['body']['plugins'] );
	unset( $plugins->plugins[ plugin_basename( __FILE__ ) ] );
	unset( $plugins->active[ array_search( plugin_basename( __FILE__ ), $plugins->active ) ] );
	$r['body']['plugins'] = serialize( $plugins );

	return $r;
}
add_filter( 'http_request_args', 'ai1ec_disable_updates', 5, 2 );

// ===============================
// = Initialize and setup MODELS =
// ===============================
global $ai1ec_settings;

$ai1ec_settings = Ai1ec_Settings::get_instance();

// ================================
// = Initialize and setup HELPERS =
// ================================
global $ai1ec_view_helper,
       $ai1ec_settings_helper,
       $ai1ec_calendar_helper,
       $ai1ec_app_helper,
       $ai1ec_events_helper,
       $ai1ec_importer_helper,
       $ai1ec_exporter_helper,
       $ai1ec_platform_helper,
       $ai1ec_importer_plugin_helper;

$ai1ec_view_helper            = Ai1ec_View_Helper::get_instance();
$ai1ec_settings_helper        = Ai1ec_Settings_Helper::get_instance();
$ai1ec_calendar_helper        = Ai1ec_Calendar_Helper::get_instance();
$ai1ec_app_helper             = Ai1ec_App_Helper::get_instance();
$ai1ec_events_helper          = Ai1ec_Events_Helper::get_instance();
$ai1ec_importer_helper        = Ai1ec_Importer_Helper::get_instance();
$ai1ec_exporter_helper        = Ai1ec_Exporter_Helper::get_instance();
$ai1ec_platform_helper        = Ai1ec_Platform_Helper::get_instance();
$ai1ec_importer_plugin_helper = Ai1ec_Importer_Plugin_Helper::get_instance();

// ====================================
// = Initialize and setup CONTROLLERS =
// ====================================
global $ai1ec_app_controller,
       $ai1ec_settings_controller,
       $ai1ec_events_controller,
       $ai1ec_calendar_controller,
       $ai1ec_importer_controller,
       $ai1ec_exporter_controller,
       $ai1ec_themes_controller,
       $ai1ec_platform_controller,
  	   $ai1ec_duplicate_controller;

$ai1ec_settings_controller  = Ai1ec_Settings_Controller::get_instance();
$ai1ec_events_controller    = Ai1ec_Events_Controller::get_instance();
$ai1ec_calendar_controller  = Ai1ec_Calendar_Controller::get_instance();
$ai1ec_importer_controller  = Ai1ec_Importer_Controller::get_instance();
$ai1ec_exporter_controller  = Ai1ec_Exporter_Controller::get_instance();
$ai1ec_themes_controller    = Ai1ec_Themes_Controller::get_instance();
$ai1ec_platform_controller  = Ai1ec_Platform_Controller::get_instance();
$ai1ec_duplicate_controller = Ai1ec_Duplicate_Controller::get_instance();


// ==========================================================================
// = All app initialization is done in Ai1ec_App_Controller::__construct(). =
// ==========================================================================
$ai1ec_app_controller      = Ai1ec_App_Controller::get_instance();
