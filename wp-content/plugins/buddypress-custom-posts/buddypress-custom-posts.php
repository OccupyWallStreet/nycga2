<?php
/*
	Plugin Name: BuddyPress Custom Posts
	Plugin URI: http://code.google.com/p/buddypress-custom-posts/
	Description: Provides an API to register custom posts as custom components. See more details, raise issues and contribute at <a href = 'http://goo.gl/pBeuL'>http://code.google.com/p/buddypress-custom-posts/</a>.
	Version: 0.1.2.5
	Author: Kunal Bhalla. 
	Author URI: http://kunal-b.in
	License: GPL2
	Text Domain: bpcp

	Copyright 2010  Kunal Bhalla  (email : bhalla.kunal@gmail.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Extending support for Custom Post Types in BuddyPress
 * @author Kunal Bhalla
 */

//Define constants and globals, if any.

/**
 * Store the plugin directory
 *
 * @global string BPCP_DIR
 *
 * @since 0.1.2.2
 */
define( 'BPCP_DIR', dirname(__FILE__) );

/**
 * Store the plugin themes directory
 *
 * @global string BPCP_THEMES_DIR
 *
 * @since 0.1
 */
define( 'BPCP_THEMES_DIR', dirname(__FILE__) . '/themes' );

/**
 * Store the plugin assets URL
 *
 * @global string BPCP_THEMES_DIR
 *
 * @since 0.1
 */
define( 'BPCP_THEMES_ASSETS',  plugins_url( $path = '/' . basename( dirname( __FILE__ ) ) ) . '/themes/type/assets/' );

/**
 * Load models.
 */
require "model.php";

/**
 * Load views.
 */
require "view.php";

/**
 * And the controller.
 */
require "controller.php";
