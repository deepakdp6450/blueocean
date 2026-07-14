<?php
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
define( 'DB_NAME', 'blueocean' );

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
define( 'AUTH_KEY',         '.o2pJ&%|T1?I${PWW%(@e3Qv( /hUQ$f:gcp>ae0^VdpQ,$KoCR&:L47it|}k$8T' );
define( 'SECURE_AUTH_KEY',  'oQ@/-#%AC;e7q(+|v5Z~m=Y,rE[}]}JwiRYP6T>l$#N<9i<eE`$hQ6<pt^._Wi[k' );
define( 'LOGGED_IN_KEY',    '9BIlMvjcbMv;c1cb&iVLEyG`,Wm=x%eqwOLU%*q@IUY<@(z0, @_DZjJYAb/oLgV' );
define( 'NONCE_KEY',        '`,~dTSZ%6E!uX/L*Na:5jCm.J/cXaTSfYV9s<4/sMX4zy8oF hpu[&,!9W)Bxe,w' );
define( 'AUTH_SALT',        '3u`To_1znLCHsj=k,#V-,zNp.T%H~(O@;2>?03MDat-;$OQk8(c,|B#D;ISGi&s4' );
define( 'SECURE_AUTH_SALT', 'aP+L/o6#na<m|5L>w4zBsaXIQ#&Yw6PCLEyVQKZDkGe=C6DEW/S]E&z_b2~]94{&' );
define( 'LOGGED_IN_SALT',   'TMc;En_~LDB&&Yewp1YJHuIOkqpunRZa[M}Q{KH,(F=xTJ63#~8r@Ncin~qgPlz5' );
define( 'NONCE_SALT',       'cHS8EtY};+$RP/s=q(1?({30r/0+=4X@|gqL^TarBm*BbG7tu9k*vFaet]Z;iQDI' );

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
