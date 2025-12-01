<?php
define( 'WP_CACHE', true );
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'u226939231_test' );

/** Database username */
define( 'DB_USER', 'u226939231_test' );

/** Database password */
define( 'DB_PASSWORD', 'V@][bmu1y' );

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
define( 'AUTH_KEY',         'qa^u,) )4,A.#-lPlHr$Ve?09ws)}OVdObs0!B{/k|,Cy-0~rn3/}M|5|`#*Y/,#' );
define( 'SECURE_AUTH_KEY',  'UeltD484$8Y9+ qJ}{kfi. /cMzI1`{~i[2`!BSc^no5!toN ]tO4TuC$&5y:gii' );
define( 'LOGGED_IN_KEY',    '<kqh%~f{.`joJxKf+/n& t`Cg`KA!i{W=.Tz/r9:@,uYNzb3<@(b5$!?[eO7xheT' );
define( 'NONCE_KEY',        '=/3T{=T<;5|V-%p}I9lVU>O@`/}@Q79jmxEntx|:/P}e1=)D(~ZV~:eXaP_9q--c' );
define( 'AUTH_SALT',        '.]~O%QOi3thEE^Akkr)Ioc7Q0qFtlj#&>:Un)gEy8-l5fk8&qT2xz!3knJc@(iai' );
define( 'SECURE_AUTH_SALT', ')Y%2IoI1RCZ{n}R=5esxQ=8`2ok3xy?^;rSI,6^@|j2rE)-b=?_DdG-rqpm`u?[e' );
define( 'LOGGED_IN_SALT',   ' FcvbYVX.UrRLbDL$}25La8ovBDv_CY<3C5KP/cd}^8RVgJ[~&ht,!>|:4_,<J+/' );
define( 'NONCE_SALT',       'h`|ErMy.}y$)TVdi^qmi.?`%1lH+7c=G;TBtxHXn5ZU[d_OLurxk_2P6W)4Uk~ql' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
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
