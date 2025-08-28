<?php
/**
 * WordPress Configuration for Zeabur Deployment
 * This file reads configuration from environment variables
 */

// Database Configuration from Environment Variables
define('DB_NAME', getenv('DB_NAME') ?: 'atomdpc3_superdouble_wp');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_CHARSET', getenv('DB_CHARSET') ?: 'utf8mb4');
define('DB_COLLATE', getenv('DB_COLLATE') ?: 'utf8mb4_unicode_ci');

// WordPress Configuration
$table_prefix = getenv('WP_TABLE_PREFIX') ?: 'wp_';

// Security Keys from Environment Variables
define('AUTH_KEY',         getenv('AUTH_KEY') ?: 'ZD~l:8W?B}uIfWZD5/;`L[+FL(/$e0+v+44X%E6geIHR:3[.#R0z`zfstwE8*;?N');
define('SECURE_AUTH_KEY',  getenv('SECURE_AUTH_KEY') ?: 'XYkX%(7XJ{I70GeL&}:4#29#<ZcC0BcgT1q5Srl.2J_5>s/1Sd3SmVJ3f40BXO/Z');
define('LOGGED_IN_KEY',    getenv('LOGGED_IN_KEY') ?: '6~ChO&O(oZwCB#-]%eGl)^P@f*~bxEPiA3v(R3Eswz*2:Sk]05oN,}~PQf#%NbZJ');
define('NONCE_KEY',        getenv('NONCE_KEY') ?: 'RCqx$fNOLsuVkiktr#c1ni-Qg/fS?@:q@t;VR]g?2:^tgT.A>J+/MCU{*:Bo:M@C');
define('AUTH_SALT',        getenv('AUTH_SALT') ?: 'W3SwjfO^1~6@On}v--kiWD{f@?#wZ+jkg|+TmdyX<Rc:7}UF27ij^e#Ol` tUBi9');
define('SECURE_AUTH_SALT', getenv('SECURE_AUTH_SALT') ?: 'M[/y ]dm=h9@7K}Ngga8t-A<R`309M^=Ln,e8x8+&%kE*>: q~Gc^{[g:Zk&h5R^');
define('LOGGED_IN_SALT',   getenv('LOGGED_IN_SALT') ?: '[z-Yvne!(FOTCq8N1<k[4Dx|EN(PY$1KQ)d,u*ja]#y=1:q#dl+Q6.%#ZdCRBZ#q');
define('NONCE_SALT',       getenv('NONCE_SALT') ?: 'lo0[G@{;^lcdA-/F$NX*er[TMUjaMJ}1pHzZpo{#0]XET97:-F<J%PtQZT.hk{46');

// URL Configuration for Zeabur
if (getenv('WP_HOME')) {
    define('WP_HOME', getenv('WP_HOME'));
    define('WP_SITEURL', getenv('WP_SITEURL') ?: getenv('WP_HOME'));
}

// HTTPS Detection for Zeabur
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

// WordPress Debug Mode
define('WP_DEBUG', getenv('WP_DEBUG') === 'true');
define('WP_DEBUG_LOG', getenv('WP_DEBUG') === 'true');
define('WP_DEBUG_DISPLAY', false);

// Cache Configuration
define('WP_CACHE', getenv('WP_CACHE') === 'true');

// Memory Limits
define('WP_MEMORY_LIMIT', '256M');
define('WP_MAX_MEMORY_LIMIT', '512M');

// Auto Updates
define('AUTOMATIC_UPDATER_DISABLED', false);
define('WP_AUTO_UPDATE_CORE', 'minor');

// File Permissions
define('FS_CHMOD_DIR', (0755 & ~ umask()));
define('FS_CHMOD_FILE', (0644 & ~ umask()));

// Absolute path to the WordPress directory
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}

// Sets up WordPress vars and included files
require_once(ABSPATH . 'wp-settings.php');