<?php
global $db;
require_once('../private/initialize.php');

if(isset($db)) $db->disconnect();

dump($_GET);