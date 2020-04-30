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
define( 'DB_NAME', 'amrakorbojoy_wp448' );

/** MySQL database username */
define( 'DB_USER', 'amrakorbojoy_wp448' );

/** MySQL database password */
define( 'DB_PASSWORD', 'M68[SpR9]o2R]@.2' );

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
define( 'AUTH_KEY',         'jvohg2hhalccjgcedcyybeebt5bmortvij10m8wk7pkawy4fl675fonohq8wc1kb' );
define( 'SECURE_AUTH_KEY',  'nl6aqjnv81xux8ruc2ktbtig2as8chl1qkwx1fsykneyn1wq4codsoxbzjw57pnc' );
define( 'LOGGED_IN_KEY',    'xxxn44mbbfuorxytdn8m8otqhfbu8zi0z6ykfl1cskyy62h9ezcsn7cplth2tgec' );
define( 'NONCE_KEY',        'bzobxtg34gbtkivi3ohkhlvetvbylepnzl0o0a45qmk5oal9zbijai8vclj5hwet' );
define( 'AUTH_SALT',        'imsssexozaglmdigvup1lo0t43awylf01obw2qokbvcb452nutm5trcllv5faice' );
define( 'SECURE_AUTH_SALT', 'bbalgbtaoc0xstmlw2gabwz1wgvodaxrscwhmexg30wtrdelgbrj8sa6d8ayehdi' );
define( 'LOGGED_IN_SALT',   'idkrhnzft0ylrczmowiihpssbwhefabur8gso7u8artgwvvmaazjeqvwtn2gmfat' );
define( 'NONCE_SALT',       'pnnofmvsonwlgfdjxzkme66vc2i6blsl9sw4ny10cavjgimyt2ipqe6xmcnkjj3k' );

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
