<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// Fetch the environment-specific config
$env_path = dirname(__FILE__) . '/env.php';
if ( !file_exists( $env_path ) ) {
	die( 'You must create an env.php file to continue! Use env-sample.php as a template.' );
} else {
	require( dirname(__FILE__) . '/env.php' );
//	require( dirname(__FILE__) . '/env-old.php' );
}

ini_set('post_max_size', '50M');
ini_set('upload_max_filesize', '50M');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'nW(U6FA:t~N<=+O6|dQ$Ump%X+0[n.*@$?(~U--T?h]p~}*E3)/juE{8msE_YBUu');
define('SECURE_AUTH_KEY',  'H_>te[POEoZx$Fw:b0>`#fQ}}7U<Lwr,K)mo<*EpyOVt6`8lH(#(fSTSh4@|$FCa');
define('LOGGED_IN_KEY',    'q[wqC/-N)M:J=(9pyDu).z*n>|N*Cs1LWh:885+$l0$lWshINkvt+>*=:=)YE$Jl');
define('NONCE_KEY',        'W|9`Gzf4RczLxg=*iq.~X)xh6u:W~xkH?TINE+*d`CX:~@Y)OnMk6g`nxM;c8GXu');
define('AUTH_SALT',        'UxuD1=d#oH* (TSz!64D#|y2N0y8T95;R0ZV*QQ<~HbQm8>oz9S._1&G4@lb8r]?');
define('SECURE_AUTH_SALT', '.q8iwo0=%f+%uSgmo`bX2/nYLg1e|9 rf6Y{?vfOKpdqc4axGXAJyX}6Wi(i,o_^');
define('LOGGED_IN_SALT',   '_.jJU_eLv=I`|;Z#HYXl%K5d.^-+/tF,~it+hs>v1 3Q(]B.hJh:F29tV}-VzL!J');
define('NONCE_SALT',       '>}-44RY Ys]cA555a$tl-{18tQ9hP) ?!{;Ju^5h(#p/qT;|]5h&cC@n }GJ+r]k');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
/* Multisite */
define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', true );
$base = '/';
define( 'DOMAIN_CURRENT_SITE', 'nycga.net' );
define( 'PATH_CURRENT_SITE', '/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );
define( 'BP_ROOT_BLOG',5 );
/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

/* Environment Config */
define('OWS_ENV', 'staging');
