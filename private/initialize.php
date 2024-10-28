<?php
//Turn on output buffering
ob_start();
//Turn on sessions
session_start();

// Assign file paths to PHP constants
define("PRIVATE_PATH", dirname(__FILE__));
define("PROJECT_PATH", dirname(PRIVATE_PATH));
const PUBLIC_PATH = PROJECT_PATH . '/public';
const SHARED_PATH = PRIVATE_PATH . '/shared';

// Assign the root URL to a PHP constant
$public_end = strpos($_SERVER['SCRIPT_NAME'], '/public') + 7;
$doc_root = substr($_SERVER['SCRIPT_NAME'], 0, $public_end);
define("WWW_ROOT", $doc_root);

require_once('http_functions.php');
require_once('auth_functions.php');

//Load Environment variables
require_once('DotEnv.php');
$dotenv = new DotEnv(PRIVATE_PATH . '/.env');
$dotenv->load();

//Show errors in the browser?
if ($_ENV['APPLICATION_ENV'] == "PROD") {
    ini_set('display_errors', 0);
}

//Connect to database
require_once('database.php');
$db = new Database();
$db->connect();

//Validation functions (requires $db)
#require_once('validation_functions.php');