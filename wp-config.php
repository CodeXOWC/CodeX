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

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'testsite_wpadmin');

/** MySQL database username */
define('DB_USER', 'testsite_wpadmin');

/** MySQL database password */
define('DB_PASSWORD', 'testsite_wpadmin');

/** MySQL hostname */
define('DB_HOST', 'localhost');

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
define('AUTH_KEY',         '^GUB++Ywn/84|4L]@TQKNp bR-ZbQ57qc!?JH7,Mw$CB:}{t69W@/INqgHWwnuv$');
define('SECURE_AUTH_KEY',  '3QhZl+f-;U/x#gdvX(1Q%u0C/:@-+6HTbyUlY}DYN5VaEp++v4!n+$-d/Esz+8I~');
define('LOGGED_IN_KEY',    '@09`_|Ipn96?$:9-R*hP/dJ>j09$&@[|)3o~`EaZ4`!bsn{^zV_76T;l!@j`=)A}');
define('NONCE_KEY',        'O^X1F_;}nq2D9JyD_<5q?>tUiRuqSZ?n2Rrlw8PT]-9ghQjiE>9rcU2X+tl%|#%.');
define('AUTH_SALT',        '(Y$gqibe=;Hv6^d&-|#`BTfSa$!eYMCG=u|Y]o#V,Ma[(j->U6MX$_>9Iohv_{;m');
define('SECURE_AUTH_SALT', '+(jxsBH*/|Ld;m+Z)Tf-iq7Q*w]?[.fe.K[hWs(>wD^^k+;Bx`58[t:+d|}Ldz9M');
define('LOGGED_IN_SALT',   '#~O{`+|IY@Rb9G#a{fxTVZIk$#<8u4>O?zrVL6_9O n,f.D ]hm;+RA8@yN+oE0)');
define('NONCE_SALT',       'MN*`divZ{W-l3-#j67=I{h,@S;K{N:K5QMP]1m<@6>3iB!iR9%-OL^7B-{Oz3-4D');

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
if (0===strpos($_SERVER["HTTP_HOST"],'es.') || 0===strpos($_SERVER["REQUEST_URI"],'/es/')) {
	define('WPLANG', 'es_ES');
	$_SERVER["REQUEST_URI"]=str_replace('/es/','',$_SERVER["REQUEST_URI"]);
} else {
	define('WPLANG', '');
}
/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
