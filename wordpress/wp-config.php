<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'test' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'h3gBFol.XlFl-7<!.id+cY@C7XVT4oFFO7|x%)5+0{TUmfT4s.v4+uvx:|(GRCh0' );
define( 'SECURE_AUTH_KEY',  '&flLw+jlC!<sMG&|f+xUh)$}vUd+/T.Z(ks)}8Vp3pV]]$uX=lW&|/p&LA]sArv{' );
define( 'LOGGED_IN_KEY',    '(jg7bFttmBlE)wp=%g#U<xv+>/{;yrAGhNh8MOpJMxwf7/y=5MZ_5<+r{#!}u&({' );
define( 'NONCE_KEY',        '.T,Zlf^[*jyW$d1FHj$z*39+X!F<F*!,PfxiWP}V1R~c>EG9OP04>I.,i}Ca/,2B' );
define( 'AUTH_SALT',        'YTg=O-?u7JwmcG-,<=%%E>`:C;(.e6,H<pMs]%0?Gov=~+EfVyF8U))p-jx<0a=t' );
define( 'SECURE_AUTH_SALT', 'Fw6dF-tCpxAXJ{8Rov$:?;s9#ITZKUjW,jxStk6Z}YvX{ERPwJmL|sd(Q(wSfSh&' );
define( 'LOGGED_IN_SALT',   '4NL{q-u-:p4Nhzo_KwXA]w%f6hb$l .$ArcF;bj)o40mE6:k0khX?H9#QM 3@{dc' );
define( 'NONCE_SALT',       'z%$;/ParL1_ljaQ;BUD)ujVh{f;Hrl3zdt$$30^IVFOreOPilHSkK]Haxs9G<M(I' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
