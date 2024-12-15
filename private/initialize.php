<?php
//Turn on output buffering
ob_start();
//Turn on sessions
session_start();

//Assign file paths to PHP constants
define("PRIVATE_PATH", dirname(__FILE__));
define("PROJECT_PATH", dirname(PRIVATE_PATH));
const PUBLIC_PATH = PROJECT_PATH . '/public';
const SHARED_PATH = PRIVATE_PATH . '/shared';

//Assign the root URL to a PHP constant
$public_end = strpos($_SERVER['SCRIPT_NAME'], '/public') + 7;
$doc_root = substr($_SERVER['SCRIPT_NAME'], 0, $public_end);
define("WWW_ROOT", $doc_root);

//Load Environment variables
require_once('DotEnv.php');
$dotenv = new DotEnv(PRIVATE_PATH . '/.env');
$dotenv->load();

//Connect to database
require_once('database_functions.php');
$db = new Database();
$db->connect();

//Load settings
require_once('setting_functions.php');
$settings = new Settings();
$settings->load();

require_once('http_functions.php');
require_once('auth_functions.php');
require_once('access_functions.php');
require_once('misc_functions.php');
require_once('validation_functions.php');

if ($_ENV['APPLICATION_ENV'] == "PROD") {
    //Show errors in the browser?
    ini_set('display_errors', 0);
} else {
    //Allow Google auth on laptop at work
    $_ENV['google_oauth_redirect_uri'] = "http://".$_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
}