<?php 
define('USE_FETCH_FOR_REQUESTS',true);
?><?php 
define('WP_HOME','https://playground.wordpress.net/scope:0.2965287070239588');
define('WP_SITEURL','https://playground.wordpress.net/scope:0.2965287070239588');
?><?php 
define('WP_DEBUG_LOG',true);
define('WP_DEBUG_DISPLAY',false);
?><?php define( 'CONCATENATE_SCRIPTS', false );
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
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'database_name_here' );

/** Database username */
define( 'DB_USER', 'username_here' );

/** Database password */
define( 'DB_PASSWORD', 'password_here' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY','&k%d]]SopB,DSK&Gj!Fef6wwpjsba_M7S@kqQ?]7');
define( 'SECURE_AUTH_KEY','_Mq.tFSG69TL1HgZ9XaM4]X&-h=b<e=tw8s%ItT<');
define( 'LOGGED_IN_KEY','(wkp0kUO14k>yP8Jcc#kxC.<3(fK35x=5$z[5a#w');
define( 'NONCE_KEY','sj(h>Ev?Fcuy8cO%d@>3U[e,)^s,PMIeTS.0a50$');
define( 'AUTH_SALT','Ips6pjDzWx(ELS-<(Wl>l)I+><cJDnOf$iNs*E8,');
define( 'SECURE_AUTH_SALT','q!WD*e+kv9DFEK,$YS.oXWGPn_cmWjEENRVpVo>d');
define( 'LOGGED_IN_SALT','#z/ng*c!ZD!5<=wKneSOS2Bhg=>gre?=qxi82>cr');
define( 'NONCE_SALT','LZpxgck&8q7bM,bbTsd_CKdwfdL7.5-X^T.ABznx');

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
define( 'WP_DEBUG',true);

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
