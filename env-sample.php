<?php
/**
 * WordPress
 */
define( 'DB_NAME', 'nycga' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', 'root' );
define( 'DB_HOST', 'localhost' );

/**
 * bbPress (BP version)
 *
 * These should be the same as your WP info
 */
define( 'BBDB_NAME', 'nycga' );
define( 'BBDB_USER', 'root' );
define( 'BBDB_PASSWORD', 'root' );
define( 'BBDB_HOST', 'localhost' );

/**
 * Other environment specific constants
 */

// Text for "ENVIRONMENT" Tab ("LOCAL INSTALL", "DEV ENVIRONMENT", "STAGING", etc)
define( 'ENV_TAB', 'DEV ENVIRONMENT' );

// Only change this on production sites!
define( 'IS_LOCAL_ENV', true );

// Many installed plugins will flood you with errors, so use this only when necessary for debugging
define( 'WP_DEBUG', false ); 

// Unless you walk through the cache setup on your local installation, you'll want to keep this shut off
define( 'WP_CACHE', false );

define( 'WP_MEMORY_LIMIT', '128M' );

?>
