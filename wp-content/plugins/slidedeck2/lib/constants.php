<?php
/**
 * Constants used by this plugin
 * 
 * @package SlideDeck
 * @author dtelepathy
 */

/*
Copyright 2012 digital-telepathy  (email : support@digital-telepathy.com)

This file is part of SlideDeck.

SlideDeck is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

SlideDeck is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with SlideDeck.  If not, see <http://www.gnu.org/licenses/>.
*/

// The current version of this plugin
if( !defined( 'SLIDEDECK2_VERSION' ) ) define( 'SLIDEDECK2_VERSION', '2.1.20121010' );

// Environment - change to "development" to load .dev.js JavaScript files (DON'T FORGET TO TURN IT BACK BEFORE USING IN PRODUCTION)
if( !defined( 'SLIDEDECK2_ENVIRONMENT' ) ) define( 'SLIDEDECK2_ENVIRONMENT', 'production' );

// The license of this plugin
if( !defined( 'SLIDEDECK2_LICENSE' ) ) define( 'SLIDEDECK2_LICENSE', 'LITE' );
if( !defined( 'SLIDEDECK_TOTAL_SLIDES_LITE' ) ) define( 'SLIDEDECK_TOTAL_SLIDES_LITE', 5 );

// The directory the plugin resides in
if( !defined( 'SLIDEDECK2_DIRNAME' ) ) define( 'SLIDEDECK2_DIRNAME', dirname( dirname( __FILE__ ) ) );

// The URL path of this plugin
if( !defined( 'SLIDEDECK2_URLPATH' ) ) define( 'SLIDEDECK2_URLPATH', trailingslashit( plugins_url() ) . basename( SLIDEDECK2_DIRNAME ) );

define( 'SLIDEDECK2_POST_TYPE',                      'slidedeck2' );
define( 'SLIDEDECK2_SLIDE_POST_TYPE',                'slidedeck_slide' );
define( 'SLIDEDECK1_POST_TYPE',                      'slidedeck' );
define( 'SLIDEDECK1_SLIDE_POST_TYPE',                'slidedeck_slide' );
define( 'SLIDEDECK2_NEW_TITLE',                      'My SlideDeck' );
define( 'SLIDEDECK2_CUSTOM_LENS_DIR',                WP_PLUGIN_DIR . "/slidedeck-lenses" );
define( 'SLIDEDECK2_IS_AJAX_REQUEST',                ( !empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) );
define( 'SLIDEDECK2_DEFAULT_LENS',                   'tool-kit' );
define( 'SLIDEDECK2_UPDATE_SITE',                    'http://update.slidedeck.com' );

// SlideDeck anonymous user hash
define( 'SLIDEDECK2_USER_HASH', sha1( $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'] ) );
// KISS Metrics API Key
define( 'SLIDEDECK2_KMAPI_KEY', "d1b65dbd653f5c7f63692c5a3a17a7ad5d8d5d4d" );
