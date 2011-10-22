<?php
/**
 * Framework Initialization.
 * This file is called at framework load time.
 *
 * @version		$Rev: 199485 $
 * @author		Jordi Canals
 * @copyright   Copyright (C) 2008, 2009, 2010 Jordi Canals
 * @license		GNU General Public License version 2
 * @link		http://alkivia.org
 * @package		Alkivia
 * @subpackage	Framework
 *

	Copyright 2008, 2009, 2010 Jordi Canals <devel@jcanals.cat>

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	version 2 as published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Creates and returns the framework URL.
 *
 * @return string Framework URL
 */
function ak_styles_url ()
{
   $dir = str_replace('\\', '/', WP_CONTENT_DIR);
   $fmw = str_replace('\\', '/', AK_FRAMEWORK);

   return str_replace($dir, WP_CONTENT_URL, $fmw) . '/styles';
}

// ================================================= SET GLOBAL CONSTANTS =====

if ( ! defined('AK_STYLES_URL') ) {
    /** Define the framework URL */
    define ( 'AK_STYLES_URL', ak_styles_url() );
}

if ( ! defined('AK_INI_FILE') ) {
    /** Define the alkivia.ini filename and absoilute location */
    define ( 'AK_INI_FILE', WP_CONTENT_DIR . '/alkivia.ini');
}

if ( ! defined('AK_CLASSES') ) {
    /** Define the classes folder */
    define ( 'AK_CLASSES', AK_FRAMEWORK . '/classes');
}

if ( ! defined('AK_LIB') ) {
    /** Library folder for functions files */
    define ( 'AK_LIB', AK_FRAMEWORK . '/lib');
}

if ( ! defined('AK_VENDOR') ) {
    /** Vendor classes and libs */
    define ('AK_VENDOR', AK_FRAMEWORK . '/vendor');
}

$akf_uploads = wp_upload_dir();
if ( ! defined('AK_UPLOAD_DIR') ) {
    /** Absolute path to upload folder */
    define ( 'AK_UPLOAD_DIR', $akf_uploads['basedir'] . '/alkivia');
}
if ( ! defined('AK_UPLOAD_URL') ) {
    /** URL to upload folder. This could be replaced by a download manager. */
    define ( 'AK_UPLOAD_URL', $akf_uploads['baseurl'] . '/alkivia');
}

// ============================================== SET GLOBAL ACTION HOOKS =====

/**
 * Adds meta name for Alkivia Framework to head.
 *
 * @hook action 'wp_head'
 * @access private
 * @return void
 */
function _ak_framework_meta_tags() {
    echo '<meta name="framework" content="Alkivia Framework ' . get_option('ak_framework_version') . '" />' . PHP_EOL;
}
add_action('wp_head', '_ak_framework_meta_tags');

/**
 * Loads the framework translations.
 * Sets the translation text domain to 'akvf'.
 *
 * @return bool true on success, false on failure
 */
function _ak_framework_translation()
{
    $locale = get_locale();
    $mofile = AK_FRAMEWORK . "/lang/$locale.mo";

    return load_textdomain('akfw', $mofile);
}
add_action('init', '_ak_framework_translation');

// ================================================ INCLUDE ALL LIBRARIES =====

// Create the upload folder if does not exist.
if ( ! is_dir(AK_UPLOAD_DIR) ) {
    wp_mkdir_p(AK_UPLOAD_DIR);
}

// Prepare the settings and objects libraries.
require_once ( AK_CLASSES . '/settings.php');

require_once ( AK_LIB . '/filesystem.php' );
require_once ( AK_LIB . '/formating.php' );
require_once ( AK_LIB . '/modules.php' );
require_once ( AK_LIB . '/objects.php' );
require_once ( AK_LIB . '/system.php' );
require_once ( AK_LIB . '/themes.php' );
require_once ( AK_LIB . '/users.php' );

do_action('ak_framework_loaded');
