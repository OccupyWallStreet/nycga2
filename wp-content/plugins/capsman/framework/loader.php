<?php
/**
 * Framework Loader.
 * This file MUST always be included at startup when using the framework.
 *
 * @version		$Rev: 203758 $
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

// TODO: Bybapass framework loading if already loaded.
// TODO: Load Framework at plugins_loaded or init to allow filters on plugins?
//       If loaded on plufins_loaded will not load for themes.

$akf_version = '0.8';

if ( file_exists(WP_CONTENT_DIR . '/alkivia.php') ) {
    /** Loads alkivia.php to override some default constants */
    include_once( WP_CONTENT_DIR . '/alkivia.php');
}

// Check version for installs and updates.
$akf_current = get_option('ak_framework_version');
$akf_path    = dirname(__FILE__);

if ( false === $akf_current ) {
    // Install the framework. Save version and path.
    add_option('ak_framework_version', $akf_version);
    add_option('ak_framework_path', $akf_path);
} elseif ( version_compare($akf_version, $akf_current, '>') ) {
    // Update framework if newer. Save version and path.
    update_option('ak_framework_version', $akf_version);
    update_option('ak_framework_path', $akf_path);
} else {
    // Using installed version.
    $akf_db_path = get_option('ak_framework_path');
    if ( false !== $akf_db_path && is_dir($akf_db_path) ) {
        // Only use current if still present. Could be from an uninstalled plugin.
        $akf_path = $akf_db_path;
    } else {
        // If installed version not present, use current.
        update_option('ak_framework_version', $akf_version);
        update_option('ak_framework_path', $akf_path);
    }
}

if ( ! defined('AK_FRAMEWORK') ) {
    // Define the framework path.
    define ('AK_FRAMEWORK', $akf_path );
}

include_once( AK_FRAMEWORK . '/init.php');
