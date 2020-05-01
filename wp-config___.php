<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'amrakorbojoy' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'boKv}eUpe$0,KI!{0%^MX? Q{ny l%s=9[l7=IBA;Qmo>VS4A8Xzgpy0Ct$@mOS|' );
define( 'SECURE_AUTH_KEY',  '~m%8!h%xz]Jk;1G5Mll(sp!FpVvx-]oFau!n%oP<Z%sWIsF!L ?fjBy+K+KJPBNy' );
define( 'LOGGED_IN_KEY',    'AmR.4;e2l)Xzw^,_=z2zN$YKsf&Sb1k)Time^kDd}L920IV^Cmm4Avbxq~#%KxeV' );
define( 'NONCE_KEY',        'vwV=#4^Ie3~{-bu~}o62 q.b8agxZePuCG;yg*n[ZiPM&1%-VE0(.eD~=oJo:rpH' );
define( 'AUTH_SALT',        'BCixZ?}4`&2U{OdUWc|V}mtdc$o}lf_8=LHM7F0VfToqj,%6)2,T(zF0,8&<9PgJ' );
define( 'SECURE_AUTH_SALT', ' [cK|%o-rfua0!~2QFX2NEd?F:b;neiLqS#^vErmNGbn}4;C^P+U~k0 C56FT1>P' );
define( 'LOGGED_IN_SALT',   '2hMi-O;zM6ph9:j PgIam&0oj>FEiy_6p>UzU:_-s`.#z38BCH47ga5W*0=$4qV0' );
define( 'NONCE_SALT',       'yakt^hxN&qsq-,+gllK6we=XHPB51X8BW%{CJUBLW{T%@@<T/P|?#eXkZybC{&2 ' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpef_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
