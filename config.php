<?php

/** 
 * Config: the configuration file.
 * 
 * @package Avant
 */

/**
 * You know what to do...
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

/** Site Config **/
define('SITE_NAME', 'The Proxy');
define('LANG', 'pt_BR');

/** URL Config **/
define('BASE_URL', 'http://localhost/theproxy/');

/**
 * Database configs
 * To deactivate the use of Database, exclude these 4 lines or set DB_NAME as empty: define('DB_NAME', '');
 */

define('DB_NAME', 'theproxy');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_HOST', 'localhost');

/** Theme config **/
define('THEME', 'theproxy');

/** AES Salt Config **/
define('AES_KEY',  '8NsiN=mPex`60Sskg0dhD}lN{^?Qcr$z');

/** Timezone config **/
date_default_timezone_set('America/Fortaleza');

/** Debug config **/
define('DEBUG', false);

/********************************************************************
 * 
 *  That's all. Stop editing :)
 * 
 ********************************************************************/

/** The directories name **/
define('CORE_DIR', 'core');
define('THEMES_DIR', 'themes');