<?php
global $db;
require_once('../private/initialize.php');

$db->disconnect();
//TODO Pretty this, maybe logging?
dump($_GET);